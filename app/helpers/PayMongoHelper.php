<?php

class PayMongoHelper
{
    private static function getConfig(): array
    {
        $configFile = BASE_PATH . '/config/paymongo.php';
        if (file_exists($configFile)) {
            return require $configFile;
        }
        return require BASE_PATH . '/config/paymongo.example.php';
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
     * @return array Response from PayMongo API
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
        $secretKey = $config['secret_key'];

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
                'name'        => $description ?: 'Apartment Payment',
                'quantity'    => 1,
                'description' => $description ?: 'Apartment Payment'
            ];
        }

        $billing = [
            'name'  => !empty($billingInfo['name']) ? $billingInfo['name'] : ($_SESSION['name'] ?? 'Valued Tenant'),
            'email' => !empty($billingInfo['email']) ? $billingInfo['email'] : ($_SESSION['email'] ?? 'tenant@iscag.local')
        ];
        if (!empty($billingInfo['phone'])) {
            $billing['phone'] = $billingInfo['phone'];
        }

        $attributes = [
            'billing'              => $billing,
            'send_email_receipt'   => true,
            'show_description'     => true,
            'show_line_items'      => true,
            'description'          => substr($description, 0, 255),
            'line_items'           => $formattedLineItems,
            'payment_method_types' => $config['payment_method_types'],
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'metadata'             => empty($metadata) ? new \stdClass() : (object)$metadata
        ];

        if (!empty($referenceNumber)) {
            $attributes['reference_number'] = substr($referenceNumber, 0, 255);
        }

        $payload = [
            'data' => [
                'attributes' => $attributes
            ]
        ];

        return self::request('POST', '/checkout_sessions', $payload);
    }

    /**
     * Retrieve a Checkout Session by ID
     */
    public static function getCheckoutSession(string $sessionId): array
    {
        return self::request('GET', '/checkout_sessions/' . urlencode($sessionId));
    }

    /**
     * Make an authenticated HTTP request to PayMongo
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
}
