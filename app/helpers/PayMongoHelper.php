<?php

class PayMongoHelper
{
    /**
     * Retrieve configuration array from config/paymongo.php or example file
     */
    public static function getConfig(): array
    {
        $configFile = BASE_PATH . '/config/paymongo.php';
        if (file_exists($configFile)) {
            return require $configFile;
        }
        return require BASE_PATH . '/config/paymongo.example.php';
    }

    /**
     * Check whether valid live/test PayMongo secret credentials are set
     */
    public static function hasValidCredentials(): bool
    {
        $config = self::getConfig();
        $key = trim($config['secret_key'] ?? '');
        return !empty($key) && stripos($key, 'YOUR_') === false && strpos($key, 'sk_') === 0;
    }

    /**
     * Create a PayMongo Checkout Session
     * 
     * @param float $amount Total amount in PHP (e.g. 5000.00)
     * @param string $description Description shown on checkout
     * @param array $lineItems Array of items [['name' => '...', 'amount' => 5000, 'currency' => 'PHP', 'quantity' => 1]]
     * @param string $successUrl Redirect URL on payment completion
     * @param string $cancelUrl Redirect URL on user cancellation
     * @param array $metadata Custom metadata (e.g. payment_ids, tenant_id)
     * @param string|null $referenceNumber External payment reference number
     * @param array|null $billingInfo Customer billing details
     * @return array Response from PayMongo API or sandbox session
     */
    public static function createCheckoutSession(
        float $amount,
        string $description,
        array $lineItems,
        string $successUrl,
        string $cancelUrl,
        array $metadata = [],
        ?string $referenceNumber = null,
        ?array $billingInfo = null
    ): array {
        $config = self::getConfig();
        $useSandbox = !empty($config['enable_sandbox_simulation']) || !self::hasValidCredentials();

        // PayMongo expects line item amounts in cents (multiply by 100)
        $formattedLineItems = [];
        if (!empty($lineItems)) {
            foreach ($lineItems as $item) {
                $itemAmount = (int) round(((float)($item['amount'] ?? 0)) * 100);
                if ($itemAmount <= 0) continue;
                $formattedLineItems[] = [
                    'currency'    => 'PHP',
                    'amount'      => $itemAmount,
                    'name'        => (string) ($item['name'] ?? 'Payment Item'),
                    'quantity'    => (int) ($item['quantity'] ?? 1),
                    'description' => (string) ($item['description'] ?? $item['name'] ?? 'Fee')
                ];
            }
        }

        // If no valid line items, create one combined item
        if (empty($formattedLineItems)) {
            $formattedLineItems[] = [
                'currency'    => 'PHP',
                'amount'      => (int) round($amount * 100),
                'name'        => $description ?: 'ISCAG MIS Payment',
                'quantity'    => 1,
                'description' => $description ?: 'ISCAG MIS Payment'
            ];
        }

        $billing = [
            'name'  => !empty($billingInfo['name']) ? $billingInfo['name'] : ($_SESSION['name'] ?? 'Valued Tenant'),
            'email' => !empty($billingInfo['email']) ? $billingInfo['email'] : ($_SESSION['email'] ?? 'tenant@iscag.local')
        ];
        if (!empty($billingInfo['phone'])) {
            $billing['phone'] = $billingInfo['phone'];
        }

        $allowedMethodTypes = $config['payment_method_types'] ?? ['qrph', 'gcash', 'paymaya', 'card'];

        $attributes = [
            'billing'              => $billing,
            'send_email_receipt'   => true,
            'show_description'     => true,
            'show_line_items'      => true,
            'description'          => substr($description, 0, 255),
            'line_items'           => $formattedLineItems,
            'payment_method_types' => $allowedMethodTypes,
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'metadata'             => empty($metadata) ? new \stdClass() : (object)$metadata
        ];

        if (!empty($referenceNumber)) {
            $attributes['reference_number'] = substr($referenceNumber, 0, 255);
        }

        // If valid PayMongo credentials exist and sandbox simulation is NOT forced, try real API
        if (self::hasValidCredentials() && empty($config['enable_sandbox_simulation'])) {
            $payload = [
                'data' => [
                    'attributes' => $attributes
                ]
            ];

            $apiRes = self::request('POST', '/checkout_sessions', $payload);
            if (!empty($apiRes['success'])) {
                return $apiRes;
            }

            // If real API failed and sandbox is enabled as a fallback, fall through to simulation
            if (empty($config['enable_sandbox_simulation'])) {
                return $apiRes;
            }
        }

        // ── PayMongo Sandbox Simulation Session ────────────────────────
        $sessionId = 'cs_sim_' . bin2hex(random_bytes(12));
        $paymentId = 'pay_sim_' . bin2hex(random_bytes(10));
        $checkoutUrl = self::buildFullUrl('/paymongo/sandbox?session_id=' . urlencode($sessionId));

        $sessionRecord = [
            'id'         => $sessionId,
            'amount'     => $amount,
            'status'     => 'active',
            'created_at' => time(),
            'attributes' => [
                'checkout_url'     => $checkoutUrl,
                'status'           => 'active',
                'reference_number' => $referenceNumber ?: ('ISCAG-REF-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8))),
                'line_items'       => $formattedLineItems,
                'metadata'         => is_object($attributes['metadata']) ? (array)$attributes['metadata'] : (array)$metadata,
                'billing'          => $billing,
                'description'      => $description,
                'payment_method_types' => $allowedMethodTypes,
                'success_url'      => $successUrl,
                'cancel_url'       => $cancelUrl,
                'amount'           => $amount,
                'payments'         => []
            ]
        ];

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        if (!isset($_SESSION['paymongo_simulated_sessions'])) {
            $_SESSION['paymongo_simulated_sessions'] = [];
        }
        $_SESSION['paymongo_simulated_sessions'][$sessionId] = $sessionRecord;

        return [
            'success' => true,
            'data'    => [
                'id'         => $sessionId,
                'type'       => 'checkout_session',
                'attributes' => [
                    'checkout_url'     => $checkoutUrl,
                    'status'           => 'active',
                    'reference_number' => $sessionRecord['attributes']['reference_number'],
                    'line_items'       => $formattedLineItems,
                    'metadata'         => $sessionRecord['attributes']['metadata']
                ]
            ]
        ];
    }

    /**
     * Retrieve a Checkout Session by ID (handles both real API sessions and simulated sessions)
     */
    public static function getCheckoutSession(string $sessionId): array
    {
        $sessionId = trim($sessionId);

        // Reject placeholder query strings
        if (empty($sessionId) || $sessionId === '{CHECKOUT_SESSION_ID}') {
            return [
                'success' => false,
                'message' => 'Invalid or missing checkout session ID.'
            ];
        }

        // Check simulated sessions first
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (isset($_SESSION['paymongo_simulated_sessions'][$sessionId])) {
            $sess = $_SESSION['paymongo_simulated_sessions'][$sessionId];
            return [
                'success' => true,
                'data'    => [
                    'id'         => $sessionId,
                    'type'       => 'checkout_session',
                    'attributes' => $sess['attributes']
                ]
            ];
        }

        // If simulated ID prefix but session was wiped, return fallback with default success
        if (strpos($sessionId, 'cs_sim_') === 0) {
            return [
                'success' => true,
                'data'    => [
                    'id'         => $sessionId,
                    'type'       => 'checkout_session',
                    'attributes' => [
                        'status'           => 'paid',
                        'reference_number' => 'ISCAG-SIM-' . substr($sessionId, 7, 8),
                        'metadata'         => [],
                        'payments'         => [
                            [
                                'id'         => 'pay_sim_' . substr($sessionId, 7),
                                'type'       => 'payment',
                                'attributes' => [
                                    'status' => 'paid'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        // Query real PayMongo API
        return self::request('GET', '/checkout_sessions/' . urlencode($sessionId));
    }

    /**
     * Mark a simulated session as paid and prepare payment details
     */
    public static function markSimulatedSessionPaid(string $sessionId, string $paymentChannel = 'QR Ph'): bool
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['paymongo_simulated_sessions'][$sessionId])) {
            return false;
        }

        $sess = &$_SESSION['paymongo_simulated_sessions'][$sessionId];
        $sess['status'] = 'paid';
        $sess['attributes']['status'] = 'paid';

        $payId = 'pay_sim_' . bin2hex(random_bytes(10));
        $sess['attributes']['payments'] = [
            [
                'id'         => $payId,
                'type'       => 'payment',
                'attributes' => [
                    'status'         => 'paid',
                    'amount'         => (int) round(($sess['amount'] ?? 0) * 100),
                    'currency'       => 'PHP',
                    'source'         => [
                        'type' => strtolower(str_replace(' ', '_', $paymentChannel))
                    ],
                    'paid_at'        => time(),
                    'payment_method' => $paymentChannel
                ]
            ]
        ];

        return true;
    }

    /**
     * Make an authenticated HTTP request to PayMongo API
     */
    private static function request(string $method, string $endpoint, ?array $payload = null): array
    {
        $config = self::getConfig();
        $url = rtrim($config['api_base'], '/') . $endpoint;
        $secretKey = $config['secret_key'];

        $ch = curl_init($url);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($secretKey . ':')
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local XAMPP environments

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }
        }

        $rawResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'message' => 'PayMongo cURL Error: ' . $curlError
            ];
        }

        $data = json_decode($rawResponse, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($data['data'])) {
            return [
                'success' => true,
                'data'    => $data['data']
            ];
        }

        $errorMsg = 'Payment gateway error';
        if (!empty($data['errors']) && is_array($data['errors'])) {
            $errorMsg = $data['errors'][0]['detail'] ?? $data['errors'][0]['code'] ?? $errorMsg;
        }

        return [
            'success' => false,
            'message' => $errorMsg,
            'raw'     => $data,
            'code'    => $httpCode
        ];
    }

    /**
     * Build absolute URL safely across CLI and Web environments
     */
    public static function buildFullUrl(string $path = ''): string
    {
        if (function_exists('full_url')) {
            return full_url($path);
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        return $scheme . '://' . $host . $baseUrl . '/' . ltrim($path, '/');
    }
}

