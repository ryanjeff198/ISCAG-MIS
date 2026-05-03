<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Payment Center</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    <style>
        .payment-container { max-width: 860px; margin: 0 auto; }
        
        /* Hero Banner */
        .payment-banner { background: linear-gradient(135deg, var(--primary-dark) 0%, #155736 100%); color: white; border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(15, 92, 58, 0.15); animation: slideUp 0.4s ease; }
        .payment-banner::after { content: ''; position: absolute; right: -30px; bottom: -30px; width: 160px; height: 160px; border-radius: 50%; background: linear-gradient(135deg, rgba(201, 168, 76, 0.1), rgba(201, 168, 76, 0.02)); border: 1px solid rgba(201, 168, 76, 0.2); }
        .payment-banner-title { font-family: 'Lora', serif; font-size: 1.6rem; font-weight: 700; margin: 0 0 8px; position: relative; z-index: 1; }
        .payment-banner-subtitle { font-size: 0.9rem; color: rgba(255, 255, 255, 0.8); margin: 0; line-height: 1.5; position: relative; z-index: 1; max-width: 85%; }
        
        /* Payment Card */
        .payment-card { background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 14px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; animation: slideUp 0.4s ease 0.1s backwards; }
        .payment-header { padding: 20px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, #fbfdfc, white); display: flex; align-items: center; justify-content: space-between; }
        .payment-header-left { display: flex; align-items: center; gap: 12px; }
        .payment-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .payment-icon svg { width: 22px; height: 22px; fill: currentColor; }
        .payment-icon.green { background: linear-gradient(135deg, rgba(201, 168, 76, 0.15), rgba(201, 168, 76, 0.05)); color: var(--accent); }
        .payment-icon.blue { background: rgba(59,130,246,0.1); color: #2563eb; }
        .payment-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); margin: 0; }
        
        .payment-body { padding: 24px 32px; }
        
        /* Breakdown Table */
        .breakdown-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .breakdown-table th { text-align: left; padding: 12px 16px; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        .breakdown-table th.text-right { text-align: right; }
        .breakdown-table td { padding: 16px; border-bottom: 1px dashed var(--border); vertical-align: top; }
        .breakdown-table tr:last-child td { border-bottom: none; }
        .breakdown-item-name { font-weight: 700; color: var(--text-main); font-size: 0.92rem; margin-bottom: 4px; }
        .breakdown-item-desc { font-size: 0.78rem; color: var(--text-muted); }
        .breakdown-item-price { font-family: 'Lora', serif; font-weight: 700; font-size: 1.05rem; color: var(--primary-dark); text-align: right; }
        
        /* Badges */
        .type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 6px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
        .type-badge.rent { background: rgba(23,107,69,0.1); color: #176b45; }
        .type-badge.water { background: rgba(6,182,212,0.1); color: #0891b2; }
        .type-badge.parking { background: rgba(245,158,11,0.1); color: #b45309; }
        
        .payment-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        .payment-status-badge.pending { background: #fffbeb; color: #ca8a04; border: 1px solid #fef08a; }
        .payment-status-badge.paid { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .payment-status-badge.failed { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .payment-status-badge svg { width: 14px; height: 14px; fill: currentColor; }

        .btn-pay { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 3px 10px rgba(23, 107, 69, 0.2); font-family: inherit; }
        .btn-pay:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(23, 107, 69, 0.3); }
        .btn-pay svg { width: 14px; height: 14px; fill: currentColor; }
        .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

        /* Total Row */
        .total-row { background: #f8fcfb; border-radius: 12px; padding: 20px 28px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(23, 107, 69, 0.1); margin-top: 16px; }
        .total-label { font-size: 0.95rem; font-weight: 700; color: var(--text-main); }
        .total-amount { font-family: 'Lora', serif; font-size: 1.6rem; font-weight: 800; color: var(--primary-dark); }
        .total-amount.zero { color: #166534; } /* Different shade of dark green */
        
        /* Success Notice */
        .success-notice { padding: 14px 20px; border-radius: 12px; background: rgba(47, 138, 96, 0.08); border: 1px solid rgba(47, 138, 96, 0.2); color: #166534; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 10px; margin-top: 16px; }
        .success-notice svg { width: 20px; height: 20px; fill: #166534; flex-shrink: 0; }

        /* Empty recurring */
        .empty-recurring { text-align: center; padding: 40px 24px; color: var(--text-muted); }
        .empty-recurring svg { width: 48px; height: 48px; fill: var(--border); margin-bottom: 12px; }
        .empty-recurring h4 { font-family: 'Lora', serif; color: var(--primary-dark); margin: 0 0 6px; }
        .empty-recurring p { font-size: 0.85rem; margin: 0; }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; padding: 20px; opacity: 0; transition: opacity 0.2s; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: white; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { margin: 0; font-family: 'Lora', serif; font-size: 1.2rem; color: var(--primary-dark); }
        .modal-close { background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer; }
        .modal-body { padding: 24px; }
        .payment-options { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
        .payment-option { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 14px; border: 1.5px solid var(--border); border-radius: 12px; cursor: pointer; transition: all 0.2s; }
        .payment-option:hover { border-color: var(--primary); background: #fbfdfc; }
        .payment-option.selected { border-color: var(--primary); background: rgba(23, 107, 69, 0.05); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23, 107, 69, 0.1); }
        .payment-option svg { width: 28px; height: 28px; fill: var(--primary); }
        .payment-option span { font-size: 0.82rem; font-weight: 600; color: var(--text-main); }
        
        .mock-input { width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 0.9rem; margin-bottom: 20px; color: var(--text-main); box-sizing: border-box; }
        .mock-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1); }

        .btn-submit { width: 100%; padding: 13px; background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white; border: none; border-radius: 10px; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 16px rgba(23, 107, 69, 0.25); font-family: inherit; }
        .btn-submit:hover { box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35); transform: translateY(-2px); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Toast */
        .toast-msg { position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%) translateY(100px); background: #1f2e2a; color: white; padding: 14px 28px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 999999; box-shadow: 0 8px 32px rgba(0,0,0,0.3); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .toast-msg.show { transform: translateX(-50%) translateY(0); }

        /* Section Divider */
        .section-divider { display: flex; align-items: center; gap: 12px; margin: 32px 0 20px; }
        .section-divider .divider-line { flex: 1; height: 1px; background: var(--border); }
        .section-divider .divider-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); white-space: nowrap; }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php $active_page = 'apartment_payment'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Payment Center</div>
                    <div class="top-bar-subtitle">View and settle all your apartment charges in one place</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/apartment/lease') ?>" class="btn-topbar">← Back to Lease</a>
                </div>
            </div>

            <div class="page-body">
                <div class="payment-container">
                    
<?php if (!$lease || !in_array($lease['lease_status'], ['Accepted', 'Active'])): ?>
                    <div class="payment-card" style="text-align:center; padding: 60px 24px;">
                        <svg viewBox="0 0 24 24" style="width:64px;height:64px;fill:var(--border);margin-bottom:16px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <h3 style="font-family:'Lora',serif; color:var(--text-main); margin-bottom:8px;">Payment Center Not Available</h3>
                        <p style="color:var(--text-muted); font-size:0.9rem;">You must have an <strong>Accepted</strong> or <strong>Active</strong> lease to view your charges.</p>
                        <a href="<?= url('/user/apartment/lease') ?>" class="btn-pay" style="margin-top:16px;">View Lease Contract</a>
                    </div>
<?php else:
    // Calculate totals
    $initialDue = 0; $initialPaid = 0; $allInitialPaid = true;
    foreach ($payments as $p) {
        if ($p['payment_status'] === 'Paid') {
            $initialPaid += (float)$p['amount'];
        } else {
            $initialDue += (float)$p['amount'];
            $allInitialPaid = false;
        }
    }
    $recurringTotal = 0;
    $recurringCharges = $recurringCharges ?? [];
    foreach ($recurringCharges as $rc) {
        if ($rc['status'] !== 'Paid') {
            $recurringTotal += (float)$rc['amount'];
        }
    }
    $grandTotal = $initialDue + $recurringTotal;

    // Calculate dynamic 1-Month Advance Package
    $advPayload = ['Rent-Advance'];
    $advCost = (float)($lease['monthly_rent'] ?? 0);
    $advItems = [ ['name' => 'Monthly Rent (Advance)', 'amount' => $advCost] ];

    if (isset($occupants) && $occupants > 0) {
        $advPayload[] = 'Water-Advance';
        $wAmt = ($occupants * 100);
        $advCost += $wAmt;
        $advItems[] = ['name' => 'Water Bill (Advance)', 'amount' => $wAmt];
    }

    $advPayload[] = 'Contribution-Advance';
    $advCost += 150.00;
    $advItems[] = ['name' => 'Contribution (Security/Garbage)', 'amount' => 150.00];

    if (!empty($parkingApps)) {
        foreach ($parkingApps as $pa) {
            $advPayload[] = 'Parking-Advance';
            $advCost += 1000.00;
            $advItems[] = ['name' => 'Parking Fee (Advance)', 'amount' => 1000.00];
        }
    }
?>
                    <!-- Hero Banner -->
                    <div class="payment-banner">
                        <h2 class="payment-banner-title"><?= $allInitialPaid ? 'Your Billing Dashboard' : 'Welcome to ISCAG!' ?></h2>
                        <p class="payment-banner-subtitle">
                            <?php if (!$allInitialPaid): ?>
                                To activate your lease for <strong><?= htmlspecialchars($lease['roomtype'] ?? $lease['unit_type'] ?? 'Apartment') ?></strong>, please complete your initial payments below.
                            <?php else: ?>
                                All your charges, payments, and balances are consolidated here. Stay on top of your dues easily.
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- ═══════════ SECTION A: INITIAL PAYMENTS ═══════════ -->
<?php if (!$allInitialPaid || empty($recurringCharges)): ?>
                    <div class="payment-card">
                        <div class="payment-header">
                            <div class="payment-header-left">
                                <div class="payment-icon green"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg></div>
                                <h3 class="payment-title">Initial Payments</h3>
                            </div>
                        </div>

                        <div class="payment-body">
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Custom sort order for Initial Payments
                                    $sortOrder = ['Advance' => 1, 'Deposit' => 2, 'Water-Advance' => 3, 'Contribution-Advance' => 4, 'Parking-Advance' => 5];
                                    usort($payments, function($a, $b) use ($sortOrder) {
                                        $orderA = $sortOrder[$a['payment_type']] ?? 99;
                                        $orderB = $sortOrder[$b['payment_type']] ?? 99;
                                        return $orderA <=> $orderB;
                                    });

                                    $unpaidIds = [];
                                    $unpaidItems = [];
                                    foreach ($payments as $pay): 
                                        if (!in_array($pay['payment_type'], ['Deposit', 'Advance', 'Water-Advance', 'Contribution-Advance', 'Parking-Advance'])) continue;
                                        $isPaid = $pay['payment_status'] === 'Paid';
                                        $isFailed = $pay['payment_status'] === 'Failed';
                                        
                                        if (!$isPaid) {
                                            $unpaidIds[] = $pay['payment_id'];
                                            
                                            $dispName = $pay['payment_type'];
                                            if ($dispName === 'Deposit') $dispName = 'Security Deposit';
                                            if ($dispName === 'Advance') $dispName = 'Advance Rent';
                                            
                                            $unpaidItems[] = [
                                                'name' => $dispName,
                                                'amount' => $pay['amount']
                                            ];
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="breakdown-item-name">
                                                <?php
                                                    if ($pay['payment_type'] === 'Deposit') echo 'Security Deposit';
                                                    elseif ($pay['payment_type'] === 'Advance') echo 'Advance Rent';
                                                    elseif ($pay['payment_type'] === 'Water-Advance') echo 'Water Bill';
                                                    elseif ($pay['payment_type'] === 'Contribution-Advance') echo 'Contribution';
                                                    elseif ($pay['payment_type'] === 'Parking-Advance') echo 'Parking Fee';
                                                    else echo htmlspecialchars($pay['payment_type']);
                                                ?>
                                            </div>
                                            <div class="breakdown-item-desc">
                                                <?php 
                                                    if($pay['payment_type'] === 'Deposit') echo "Refundable at end of lease"; 
                                                    elseif($pay['payment_type'] === 'Advance') echo "Equivalent to 1 month rent"; 
                                                    elseif($pay['payment_type'] === 'Water-Advance') echo "1st Month Water Consumption"; 
                                                    elseif($pay['payment_type'] === 'Contribution-Advance') echo "1st Month Security & Garbage"; 
                                                    elseif($pay['payment_type'] === 'Parking-Advance') echo "1st Month Parking"; 
                                                ?>
                                                <?php if($isPaid && $pay['reference_number']) echo "<br/><span style='color:#166534;font-size:0.72rem;font-weight:600;'>Ref: {$pay['reference_number']}</span>"; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isPaid): ?>
                                                <div class="payment-status-badge paid"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> PAID</div>
                                            <?php elseif ($isFailed): ?>
                                                <div class="payment-status-badge failed"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> FAILED</div>
                                            <?php else: ?>
                                                <div class="payment-status-badge pending"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg> PENDING</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price">₱<?= number_format($pay['amount'], 2) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <?php if ($allInitialPaid): ?>
                            <div class="success-notice">
                                <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                Initial payments fully settled. Your lease is now active.
                            </div>
                            <?php else: ?>
                            <div class="total-row">
                                <div class="total-label">Initial Balance Due</div>
                                <div class="total-amount">₱<?= number_format($initialDue, 2) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
<?php endif; ?>
                    <!-- ═══════════ SECTION B: RECURRING MONTHLY CHARGES ═══════════ -->
<?php if ($allInitialPaid): ?>
                    <div class="section-divider">
                        <span class="divider-line"></span>
                        <span class="divider-label">Monthly Charges & Utilities</span>
                        <span class="divider-line"></span>
                    </div>

    <?php if (empty($recurringCharges)): ?>
                    <div class="payment-card">
                        <div class="empty-recurring">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <h4>You're All Caught Up!</h4>
                            <p>No outstanding monthly charges yet. Your next billing cycle will appear here automatically.</p>
                            <div style="margin-top:24px; display:flex; justify-content:center; gap:12px;">
                                <button class="btn-pay" style="padding: 10px 20px; font-size:0.9rem; background:white; color:var(--primary); border:2px solid var(--border); box-shadow:none;" onclick='openAdvancePaymentModal(<?= json_encode($advPayload) ?>, <?= json_encode($advItems) ?>, <?= $advCost ?>)'>
                                    Pay 1 Month Advance Package
                                </button>
                                <a href="<?= url('/user/apartment/soa') ?>" class="btn-submit" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; padding: 10px 20px; margin:0; width:auto; border-radius:8px; font-size:0.9rem;">
                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                                    Download Receipt
                                </a>
                            </div>
                        </div>
                    </div>
    <?php else: ?>
                    <div class="section-divider">
                        <span class="divider-line"></span>
                        <span class="divider-label">Monthly Charges & Utilities</span>
                        <span class="divider-line"></span>
                    </div>

                    <div class="payment-card" style="animation-delay:0.15s;">
                        <div class="payment-header">
                            <div class="payment-header-left">
                                <div class="payment-icon blue"><svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg></div>
                                <h3 class="payment-title">Recurring Charges</h3>
                            </div>
                            <span style="font-size:0.78rem; color:var(--text-muted); font-weight:600;"><?= count($recurringCharges) ?> item(s)</span>
                        </div>

                        <div class="payment-body">
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recurringCharges as $rc): 
                                        $badgeClass = 'rent';
                                        if (stripos($rc['type'], 'Water') !== false) $badgeClass = 'water';
                                        elseif (stripos($rc['type'], 'Parking') !== false) $badgeClass = 'parking';
                                        elseif (stripos($rc['type'], 'Contribution') !== false) $badgeClass = 'contribution';
                                        
                                        if ($rc['status'] !== 'Paid') {
                                            $unpaidIds[] = $rc['id'];
                                            $unpaidItems[] = [
                                                'name' => $rc['type'] . ' (' . date('M Y', strtotime($rc['date'])) . ')',
                                                'amount' => $rc['amount']
                                            ];
                                        }
                                    ?>
                                    <tr>
                                        <td style="font-size:0.85rem; color:var(--text-main); font-weight:600;">
                                            <?= date('M d, Y', strtotime($rc['date'])) ?>
                                        </td>
                                        <td>
                                            <div class="breakdown-item-name"><span class="type-badge <?= $badgeClass ?>"><?= $rc['type'] ?></span></div>
                                            <div class="breakdown-item-desc"><?= htmlspecialchars($rc['description']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($rc['status'] === 'Paid'): ?>
                                                <div class="payment-status-badge paid"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> PAID</div>
                                            <?php else: ?>
                                                <div class="payment-status-badge pending"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg> UNPAID</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price">₱<?= number_format($rc['amount'], 2) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="total-row">
                                <div class="total-label">Total Outstanding</div>
                                <div class="total-amount <?= $recurringTotal <= 0 ? 'zero' : '' ?>">₱<?= number_format($recurringTotal, 2) ?></div>
                            </div>
                        </div>
                    </div>
    <?php endif; ?>
<?php endif; ?>

                    <!-- Grand Total (if both sections have balances) -->
<?php if (!$allInitialPaid || $recurringTotal > 0): ?>
                    <div class="total-row" style="border: 2px solid var(--primary-dark); background: rgba(15,92,58,0.04); border-radius: 14px; padding: 24px 32px; margin-top: 8px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div class="total-label" style="font-size: 1.05rem;">Grand Total Due</div>
                            <div class="total-amount" style="font-size: 2rem;">₱<?= number_format($grandTotal, 2) ?></div>
                        </div>
                        <div style="text-align:right;">
                            <button class="btn-pay" style="padding: 12px 24px; font-size:1rem;" onclick="openBulkPaymentModal()">
                                Checkout & Pay All (₱<?= number_format($grandTotal, 2) ?>)
                            </button>
                        </div>
                    </div>
<?php else: ?>
    <?php if (!empty($recurringCharges)): ?>
                    <div style="text-align:center; padding:30px; margin-top:20px; display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                        <a href="<?= url('/user/apartment/soa') ?>" class="btn-topbar" style="background:white; color:var(--primary); border:2px solid var(--border); display:inline-flex; align-items:center; gap:8px;">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                            Download Official Receipt (SOA)
                        </a>
                        <button class="btn-pay" style="padding: 12px 24px; font-size:0.95rem; display:inline-flex; align-items:center; gap:8px;" onclick='openAdvancePaymentModal(<?= json_encode($advPayload) ?>, <?= json_encode($advItems) ?>, <?= $advCost ?>)'>
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                            Pay 1 Month Advance Package
                        </button>
                    </div>
    <?php endif; ?>
<?php endif; ?>

<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Submit Payment</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin: 0 0 4px; color: var(--text-muted); font-size: 0.85rem;">Payment For:</p>
                <h2 style="margin: 0 0 10px; color: var(--primary-dark); font-family: 'Lora', serif;" id="modalPayType">Deposit</h2>
                
                <div id="modalItemList" style="background:#f8f9fa; border:1px solid var(--border); border-radius:8px; padding:12px; margin-bottom:20px; font-size:0.88rem; color:var(--text-main); display:none;">
                    <!-- JS injects the list here -->
                </div>
                
                <p style="margin: 0 0 10px; font-weight: 600; font-size: 0.88rem;">Select Payment Method</p>
                <div class="payment-options">
                    <div class="payment-option selected" id="opt-gcash" onclick="selectMethod('gcash')">
                        <svg viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                        <span>GCash</span>
                    </div>
                    <div class="payment-option" id="opt-bank" onclick="selectMethod('bank')">
                        <svg viewBox="0 0 24 24"><path d="M12 3L1 9h4v12h14V9h4L12 3zm-1 16H8v-7h3v7zm6 0h-3v-7h3v7z"/></svg>
                        <span>Bank Transfer</span>
                    </div>
                </div>

                <p style="margin: 0 0 6px; font-weight: 600; font-size: 0.88rem;">Reference Number / Receipt ID</p>
                <input type="text" id="refNumber" class="mock-input" placeholder="e.g. 100234567890" autocomplete="off">

                <button class="btn-submit" id="btnConfirmPay" onclick="submitPayment()">Confirm Payment of ₱<span id="modalAmount">0</span></button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-msg" id="toastMsg">Payment Successful!</div>

    <script>
        const unpaidIds = <?= json_encode($unpaidIds ?? []) ?>;
        const unpaidItems = <?= json_encode($unpaidItems ?? []) ?>;
        const grandTotal = <?= $grandTotal ?? 0 ?>;
        
        let selectedMethod = 'gcash';
        let isAdvanceMode = false;

        function showToast(msg, isErr = false) {
            const t = document.getElementById('toastMsg');
            t.textContent = msg;
            t.style.background = isErr ? '#991b1b' : '#166534';
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        function openBulkPaymentModal() {
            if (unpaidIds.length === 0) {
                showToast("You have no outstanding balances.", false);
                return;
            }
            
            isAdvanceMode = false;
            document.getElementById('modalPayType').textContent = 'All Outstanding Balances';
            document.getElementById('modalAmount').textContent = parseFloat(grandTotal).toFixed(2);
            document.getElementById('refNumber').value = 'PAY-BLK-' + Math.floor(Math.random() * 10000000);
            
            // Build itemized list UI
            const listDiv = document.getElementById('modalItemList');
            listDiv.style.display = 'block';
            let html = '<ul style="margin:0; padding-left:18px;">';
            unpaidItems.forEach(item => {
                html += `<li style="margin-bottom:6px;"><span style="font-weight:600;">${item.name}</span> <span style="float:right; font-family:monospace;">₱${parseFloat(item.amount).toFixed(2)}</span></li>`;
            });
            html += '</ul>';
            listDiv.innerHTML = html;

            document.getElementById('paymentModal').classList.add('show');
        }

        let advancePayloadData = ['Rent-Advance'];

        function openAdvancePaymentModal(payloadArray, itemsArray, totalCost) {
            isAdvanceMode = true;
            advancePayloadData = payloadArray;
            document.getElementById('modalPayType').textContent = 'Advance Payment (1 Month Package)';
            document.getElementById('modalAmount').textContent = parseFloat(totalCost).toFixed(2);
            document.getElementById('refNumber').value = 'PAY-ADV-' + Math.floor(Math.random() * 10000000);
            
            // Build itemized list UI using the passed items array
            const listDiv = document.getElementById('modalItemList');
            listDiv.style.display = 'block';
            let html = '<ul style="margin:0; padding-left:18px;">';
            itemsArray.forEach(item => {
                html += `<li style="margin-bottom:6px;"><span style="font-weight:600;">${item.name}</span> <span style="float:right; font-family:monospace;">₱${parseFloat(item.amount).toFixed(2)}</span></li>`;
            });
            html += '</ul>';
            listDiv.innerHTML = html;

            document.getElementById('paymentModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('show');
        }

        function selectMethod(method) {
            selectedMethod = method;
            document.getElementById('opt-gcash').classList.remove('selected');
            document.getElementById('opt-bank').classList.remove('selected');
            document.getElementById('opt-' + method).classList.add('selected');
        }

        function submitPayment() {
            const refNo = document.getElementById('refNumber').value.trim();
            if (!refNo) {
                showToast('Please enter a reference number.', true);
                return;
            }

            const btn = document.getElementById('btnConfirmPay');
            btn.disabled = true;
            btn.innerHTML = 'Processing...';

            let paymentPayload = isAdvanceMode ? advancePayloadData : unpaidIds;

            fetch('<?= url("/user/apartment/payment/submit") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: paymentPayload, reference: refNo })
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('Payment confirmed successfully!');
                    closeModal();
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast(res.message || 'Payment failed.', true);
                    btn.disabled = false;
                    btn.innerHTML = 'Confirm Payment of ₱<span id="modalAmount">' + document.getElementById('modalAmount').textContent + '</span>';
                }
            })
            .catch(err => {
                showToast('Network error.', true);
                btn.disabled = false;
                btn.innerHTML = 'Confirm Payment';
            });
        }
    </script>
</body>
</html>
