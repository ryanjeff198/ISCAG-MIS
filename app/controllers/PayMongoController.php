<?php

class PayMongoController
{
    /**
     * Display PayMongo Sandbox Hosted Checkout Page
     */
    public function sandbox(): void
    {
        $sessionId = $_GET['session_id'] ?? '';
        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';

        $sessionData = PayMongoHelper::getCheckoutSession($sessionId);
        if (!$sessionData['success'] || empty($sessionData['data']['attributes'])) {
            http_response_code(404);
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
            echo "<h2>PayMongo Checkout Session Not Found or Expired</h2>";
            echo "<p>The requested checkout session could not be retrieved.</p>";
            echo "<a href='" . url('/') . "' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#16a34a; color:#fff; text-decoration:none; border-radius:6px;'>Return Home</a>";
            echo "</div>";
            exit;
        }

        $attrs = $sessionData['data']['attributes'];
        $amount = (float)($attrs['amount'] ?? 0);
        $lineItems = $attrs['line_items'] ?? [];
        $refNo = $attrs['reference_number'] ?? ('ISCAG-' . date('Ymd'));
        $description = $attrs['description'] ?? 'ISCAG Online Payment';
        $billing = $attrs['billing'] ?? [];
        $successUrl = $attrs['success_url'] ?? url('/');
        $cancelUrl = $attrs['cancel_url'] ?? url('/');

        $this->renderSandboxView([
            'sessionId'   => $sessionId,
            'amount'      => $amount,
            'lineItems'   => $lineItems,
            'refNo'       => $refNo,
            'description' => $description,
            'billing'     => $billing,
            'successUrl'  => $successUrl,
            'cancelUrl'   => $cancelUrl
        ]);
    }

    /**
     * Process Sandbox Payment and redirect back to application success URL
     */
    public function processSandboxPayment(): void
    {
        $sessionId = $_POST['session_id'] ?? '';
        $channel = $_POST['payment_channel'] ?? 'QR Ph';

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
        $sessionData = PayMongoHelper::getCheckoutSession($sessionId);

        if (!$sessionData['success'] || empty($sessionData['data']['attributes'])) {
            header('Location: ' . url('/'));
            exit;
        }

        $attrs = $sessionData['data']['attributes'];
        $successUrl = $attrs['success_url'] ?? url('/');

        // Mark simulated session as paid
        PayMongoHelper::markSimulatedSessionPaid($sessionId, $channel);

        // Replace template placeholder {CHECKOUT_SESSION_ID} with the real session ID
        $redirectUrl = str_replace('{CHECKOUT_SESSION_ID}', urlencode($sessionId), $successUrl);
        if (strpos($redirectUrl, 'session_id=') === false) {
            $separator = (strpos($redirectUrl, '?') !== false) ? '&' : '?';
            $redirectUrl .= $separator . 'session_id=' . urlencode($sessionId);
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Render Sandbox Hosted Checkout Interface
     */
    private function renderSandboxView(array $data): void
    {
        extract($data);
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayMongo Checkout — Sandbox Simulator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pm-green: #00d66f;
            --pm-dark: #0a2540;
            --pm-slate: #425466;
            --pm-light-bg: #f7fafc;
            --pm-border: #e6ebf1;
            --pm-blue: #0070ba;
            --pm-card-bg: #ffffff;
            --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--font);
            background: #f4f7fb;
            color: var(--pm-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sandbox-ribbon {
            background: linear-gradient(90deg, #1e293b, #0f172a);
            color: #f8fafc;
            padding: 8px 16px;
            font-size: 0.78rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sandbox-ribbon-badge {
            background: #f59e0b;
            color: #000;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .checkout-layout {
            max-width: 1060px;
            margin: 30px auto;
            width: 94%;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            flex: 1;
        }
        @media (max-width: 860px) {
            .checkout-layout { grid-template-columns: 1fr; margin: 15px auto; }
        }
        .main-card, .sidebar-card {
            background: var(--pm-card-bg);
            border-radius: 16px;
            border: 1px solid var(--pm-border);
            box-shadow: 0 4px 20px rgba(10, 37, 64, 0.05);
            overflow: hidden;
        }
        .main-card { padding: 32px; }
        .sidebar-card { padding: 28px; background: #ffffff; height: fit-content; }
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--pm-border);
        }
        .paymongo-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--pm-dark);
            letter-spacing: -0.5px;
        }
        .paymongo-logo-icon {
            width: 32px;
            height: 32px;
            background: #00d66f;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0a2540;
            font-weight: 900;
            font-size: 1.1rem;
        }
        .merchant-sub {
            font-size: 0.75rem;
            color: var(--pm-slate);
            font-weight: 500;
        }
        .channel-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .channel-tab {
            flex: 1;
            padding: 12px 14px;
            border: 1.5px solid var(--pm-border);
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pm-slate);
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .channel-tab:hover { border-color: #cbd5e1; color: var(--pm-dark); }
        .channel-tab.active {
            border-color: #00d66f;
            background: #f0fdf4;
            color: #065f46;
            box-shadow: 0 0 0 1px #00d66f;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.25s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

        /* QR Ph Box */
        .qrph-box {
            text-align: center;
            padding: 24px 20px;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px dashed #cbd5e1;
            margin-bottom: 24px;
        }
        .qr-frame {
            background: #fff;
            padding: 16px;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            display: inline-block;
            margin: 12px 0 16px;
            position: relative;
        }
        .qr-badge-inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #00d66f;
            color: #0a2540;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            border: 2px solid #fff;
        }
        .btn-pay-now {
            width: 100%;
            padding: 15px 20px;
            background: #00d66f;
            color: #0a2540;
            font-size: 0.98rem;
            font-weight: 800;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 214, 111, 0.35);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-pay-now:hover {
            background: #00c364;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 214, 111, 0.45);
        }
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s;
        }
        .btn-cancel:hover { color: #dc2626; }
        .order-summary-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--pm-dark);
            margin-bottom: 16px;
        }
        .amount-hero {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--pm-border);
        }
        .amount-hero-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--pm-slate);
            letter-spacing: 0.5px;
        }
        .amount-hero-val {
            font-size: 2rem;
            font-weight: 800;
            color: var(--pm-dark);
            margin-top: 4px;
        }
        .line-item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.86rem;
            color: var(--pm-slate);
        }
        .line-item-row strong { color: var(--pm-dark); }
        .ref-pill {
            display: inline-block;
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: monospace;
            margin-top: 8px;
        }
    </style>
</head>
<body>

    <div class="sandbox-ribbon">
        <div style="display:flex; align-items:center; gap:10px;">
            <span class="sandbox-ribbon-badge">Sandbox Simulation</span>
            <span>PayMongo Hosted Payment Gateway (Offline / Development Simulator)</span>
        </div>
        <div style="color:#94a3b8; font-size:0.75rem;">
            Test Session: <span style="font-family:monospace; color:#cbd5e1;"><?= htmlspecialchars(substr($sessionId, 0, 15)) ?>...</span>
        </div>
    </div>

    <div class="checkout-layout">
        <!-- Main Payment Options -->
        <div class="main-card">
            <div class="brand-header">
                <div>
                    <div class="paymongo-logo">
                        <div class="paymongo-logo-icon">P</div>
                        <span>PayMongo</span>
                    </div>
                    <div class="merchant-sub">Secured checkout for <strong>ISCAG Philippines</strong></div>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:0.75rem; color:#64748b;">Instant Verification</span>
                    <div style="font-size:0.8rem; font-weight:700; color:#16a34a;">● Live Sandbox</div>
                </div>
            </div>

            <div style="margin-bottom:14px; font-size:0.88rem; font-weight:700; color:var(--pm-dark);">
                Select Payment Channel:
            </div>

            <div class="channel-tabs">
                <button type="button" class="channel-tab active" onclick="switchChannel('qrph')">
                    📱 QR Ph (Scan to Pay)
                </button>
                <button type="button" class="channel-tab" onclick="switchChannel('gcash')">
                    💙 GCash
                </button>
                <button type="button" class="channel-tab" onclick="switchChannel('maya')">
                    💚 Maya
                </button>
                <button type="button" class="channel-tab" onclick="switchChannel('card')">
                    💳 Card
                </button>
            </div>

            <!-- Tab 1: QR Ph -->
            <div id="tab-qrph" class="tab-content active">
                <div class="qrph-box">
                    <p style="font-size:0.85rem; font-weight:700; color:#1e293b;">Scan BSP Standard QR Ph Code</p>
                    <p style="font-size:0.75rem; color:#64748b; margin-top:2px;">Works with GCash, Maya, ShopeePay, BPI, BDO, UnionBank & any QR Ph banking app</p>
                    
                    <div class="qr-frame">
                        <!-- High Quality SVG QR Code Simulation -->
                        <svg width="170" height="170" viewBox="0 0 170 170" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="170" height="170" fill="white"/>
                            <!-- Position Detection Patterns (Top-Left) -->
                            <rect x="10" y="10" width="40" height="40" stroke="#0a2540" stroke-width="6"/>
                            <rect x="22" y="22" width="16" height="16" fill="#0a2540"/>
                            <!-- Position Detection Patterns (Top-Right) -->
                            <rect x="120" y="10" width="40" height="40" stroke="#0a2540" stroke-width="6"/>
                            <rect x="132" y="22" width="16" height="16" fill="#0a2540"/>
                            <!-- Position Detection Patterns (Bottom-Left) -->
                            <rect x="10" y="120" width="40" height="40" stroke="#0a2540" stroke-width="6"/>
                            <rect x="22" y="132" width="16" height="16" fill="#0a2540"/>
                            <!-- QR Data matrix dots simulation -->
                            <rect x="60" y="15" width="8" height="8" fill="#0a2540"/>
                            <rect x="75" y="15" width="8" height="8" fill="#0a2540"/>
                            <rect x="90" y="15" width="8" height="8" fill="#0a2540"/>
                            <rect x="105" y="15" width="8" height="8" fill="#0a2540"/>
                            <rect x="60" y="30" width="8" height="8" fill="#0a2540"/>
                            <rect x="85" y="30" width="8" height="8" fill="#0a2540"/>
                            <rect x="100" y="30" width="8" height="8" fill="#0a2540"/>
                            <rect x="15" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="30" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="45" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="65" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="85" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="105" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="125" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="145" y="60" width="8" height="8" fill="#0a2540"/>
                            <rect x="15" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="35" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="60" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="80" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="100" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="135" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="150" y="80" width="8" height="8" fill="#0a2540"/>
                            <rect x="15" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="40" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="60" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="85" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="110" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="130" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="150" y="100" width="8" height="8" fill="#0a2540"/>
                            <rect x="60" y="125" width="8" height="8" fill="#0a2540"/>
                            <rect x="80" y="125" width="8" height="8" fill="#0a2540"/>
                            <rect x="100" y="125" width="8" height="8" fill="#0a2540"/>
                            <rect x="125" y="125" width="8" height="8" fill="#0a2540"/>
                            <rect x="145" y="125" width="8" height="8" fill="#0a2540"/>
                            <rect x="65" y="145" width="8" height="8" fill="#0a2540"/>
                            <rect x="85" y="145" width="8" height="8" fill="#0a2540"/>
                            <rect x="115" y="145" width="8" height="8" fill="#0a2540"/>
                            <rect x="140" y="145" width="8" height="8" fill="#0a2540"/>
                        </svg>
                        <div class="qr-badge-inner">QR Ph</div>
                    </div>

                    <div style="font-size:0.75rem; color:#64748b;">
                        Amount to authorize: <strong style="color:#0a2540;">₱<?= number_format($amount, 2) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Tab 2: GCash -->
            <div id="tab-gcash" class="tab-content">
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <span style="font-size:1.4rem;">📱</span>
                        <strong>GCash E-Wallet Authorization</strong>
                    </div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#1e40af; margin-bottom:6px;">Mobile Number</label>
                    <input type="text" value="<?= htmlspecialchars($billing['phone'] ?? '0917-000-0000') ?>" readonly style="width:100%; padding:10px 14px; border:1px solid #93c5fd; border-radius:8px; font-weight:600; background:#fff; font-size:0.9rem;" />
                    <p style="font-size:0.75rem; color:#3b82f6; margin-top:8px;">Sandbox test mode: Click below to simulate MPIN verification and debit approval.</p>
                </div>
            </div>

            <!-- Tab 3: Maya -->
            <div id="tab-maya" class="tab-content">
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <span style="font-size:1.4rem;">💳</span>
                        <strong>Maya Wallet Authorization</strong>
                    </div>
                    <label style="display:block; font-size:0.78rem; font-weight:700; color:#166534; margin-bottom:6px;">Registered Mobile / Email</label>
                    <input type="text" value="<?= htmlspecialchars($billing['email'] ?? 'tenant@iscag.local') ?>" readonly style="width:100%; padding:10px 14px; border:1px solid #86efac; border-radius:8px; font-weight:600; background:#fff; font-size:0.9rem;" />
                    <p style="font-size:0.75rem; color:#15803d; margin-top:8px;">Sandbox test mode: Instant debit authorization simulated for Maya.</p>
                </div>
            </div>

            <!-- Tab 4: Card -->
            <div id="tab-card" class="tab-content">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                        <span style="font-size:1.4rem;">💳</span>
                        <strong>Credit / Debit Card (Visa, Mastercard, JCB)</strong>
                    </div>
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Card Number</label>
                        <input type="text" value="4111 •••• •••• 1111 (PayMongo Test Card)" readonly style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-weight:600; background:#fff; font-size:0.9rem;" />
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Expiry</label>
                            <input type="text" value="12/28" readonly style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-weight:600; background:#fff; font-size:0.85rem;" />
                        </div>
                        <div>
                            <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">CVC</label>
                            <input type="text" value="123" readonly style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-weight:600; background:#fff; font-size:0.85rem;" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Form -->
            <form method="POST" action="<?= url('/paymongo/sandbox/process') ?>">
                <input type="hidden" name="session_id" value="<?= htmlspecialchars($sessionId) ?>" />
                <input type="hidden" name="payment_channel" id="input-payment-channel" value="QR Ph" />
                
                <button type="submit" class="btn-pay-now">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    Authorize Payment of ₱<?= number_format($amount, 2) ?>
                </button>
            </form>

            <a href="<?= htmlspecialchars($cancelUrl) ?>" class="btn-cancel">
                ← Cancel and Return to Merchant
            </a>
        </div>

        <!-- Sidebar / Order Summary -->
        <div class="sidebar-card">
            <div class="order-summary-title">Summary of Payment</div>

            <div class="amount-hero">
                <div class="amount-hero-label">Total Amount Due</div>
                <div class="amount-hero-val">₱<?= number_format($amount, 2) ?></div>
                <div class="ref-pill">Ref: <?= htmlspecialchars($refNo) ?></div>
            </div>

            <div style="margin-bottom:18px;">
                <div style="font-size:0.78rem; font-weight:700; color:var(--pm-slate); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Item Details</div>
                <?php if (!empty($lineItems)): ?>
                    <?php foreach ($lineItems as $it): ?>
                        <div class="line-item-row">
                            <span><?= htmlspecialchars($it['name'] ?? 'Fee') ?> <?= !empty($it['quantity']) && $it['quantity'] > 1 ? '× ' . $it['quantity'] : '' ?></span>
                            <strong>₱<?= number_format(((float)($it['amount'] ?? 0)) / 100, 2) ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="line-item-row">
                        <span><?= htmlspecialchars($description) ?></span>
                        <strong>₱<?= number_format($amount, 2) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <div style="padding-top:14px; border-top:1px solid var(--pm-border); font-size:0.78rem; color:#64748b;">
                <div style="margin-bottom:4px;"><strong>Payer:</strong> <?= htmlspecialchars($billing['name'] ?? 'Guest / Tenant') ?></div>
                <div style="margin-bottom:4px;"><strong>Email:</strong> <?= htmlspecialchars($billing['email'] ?? 'iscag.tenant@local') ?></div>
                <div style="margin-top:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; font-size:0.72rem; line-height:1.4;">
                    🛡️ <strong>Sandbox Assurance:</strong> Submitting will simulate PayMongo webhook execution, mark the payment as <strong>Paid</strong> in the database, and issue an official ISCAG receipt.
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchChannel(channel) {
            document.querySelectorAll('.channel-tab').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            event.currentTarget.classList.add('active');
            const target = document.getElementById('tab-' + channel);
            if (target) target.classList.add('active');

            const channelMap = {
                'qrph': 'QR Ph',
                'gcash': 'GCash',
                'maya': 'Maya',
                'card': 'Credit / Debit Card'
            };
            document.getElementById('input-payment-channel').value = channelMap[channel] || 'QR Ph';
        }
    </script>
</body>
</html>
        <?php
    }
}
