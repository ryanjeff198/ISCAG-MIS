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
        .type-badge.deposit { background: rgba(59,130,246,0.1); color: #2563eb; }
        .type-badge.contribution { background: rgba(139,92,246,0.1); color: #7c3aed; }
        
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
        .modal-overlay { position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; padding: 20px; opacity: 0; transition: opacity 0.2s; overflow-y: auto; }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: white; border-radius: 16px; width: 100%; max-width: 480px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 40px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; margin: auto; }
        .modal-overlay.show .modal-content { transform: translateY(0); }
        .modal-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .modal-header h3 { margin: 0; font-family: 'Lora', serif; font-size: 1.2rem; color: var(--primary-dark); }
        .modal-close { background: none; border: none; font-size: 1.4rem; color: var(--text-muted); cursor: pointer; padding: 2px 6px; line-height: 1; border-radius: 4px; transition: color 0.2s; }
        .modal-close:hover { color: var(--text-main); }
        .modal-body { padding: 24px; overflow-y: auto; flex: 1 1 auto; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8f9fa; }
        .modal-body::-webkit-scrollbar { width: 6px; }
        .modal-body::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 4px; }
        .modal-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .modal-body::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        .modal-item-list { background:#f8f9fa; border:1px solid var(--border); border-radius:8px; padding:12px 14px; margin-bottom:20px; font-size:0.88rem; color:var(--text-main); max-height: 180px; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9; }
        .modal-item-list::-webkit-scrollbar { width: 6px; }
        .modal-item-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .modal-item-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .modal-item-list::-webkit-scrollbar-thumb:hover { background: var(--primary); }
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

    // Move-Out Settlement simulation
    $moveoutTotal = 0;
    if ($moveout && $moveout['status'] === 'Processing') {
        $moDamagePaid = false;
        $moUtilityPaid = false;
        foreach ($payments as $p) {
            if ($p['payment_status'] === 'Paid') {
                if ($p['payment_type'] === 'Move-Out Damage') $moDamagePaid = true;
                if ($p['payment_type'] === 'Move-Out Utility') $moUtilityPaid = true;
            }
        }
        
        // Damage is ONLY added to the grand total if it EXCEEDS the ₱1,000 security deposit
        if (!$moDamagePaid) {
            $excessDamage = max(0, (float)($moveout['damage_costs'] ?? 0) - 1000);
            $moveoutTotal += $excessDamage;
        }
        
        // Utilities are always considered separate charges to be paid
        if (!$moUtilityPaid) {
            $moveoutTotal += (float)($moveout['utility_deductions'] ?? 0);
        }
    }
    $grandTotal += $moveoutTotal;

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

    // Count paid advance credits
    $paidAdvanceCount = 0;
    $paidAdvanceTotal = 0;
    foreach ($payments as $p) {
        if ($p['payment_status'] === 'Paid' && in_array(strtolower($p['payment_type']), ['rent-advance', 'advance', 'water-advance', 'contribution-advance', 'parking-advance'])) {
            $paidAdvanceTotal += (float)$p['amount'];
            if (in_array(strtolower($p['payment_type']), ['rent-advance', 'advance'])) {
                $paidAdvanceCount++;
            }
        }
    }
?>
                    <!-- Hero Banner -->
                    <div class="payment-banner">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
                            <div>
                                <h2 class="payment-banner-title"><?= $allInitialPaid ? 'Your Billing Dashboard' : 'Welcome to ISCAG!' ?></h2>
                                <p class="payment-banner-subtitle">
                                    <?php if (!$allInitialPaid): ?>
                                        To activate your lease for <strong><?= htmlspecialchars($lease['roomtype'] ?? $lease['unit_type'] ?? 'Apartment') ?></strong>, please complete your initial payments below.
                                    <?php else: ?>
                                        All your charges, payments, and balances are consolidated here. Stay on top of your dues easily.
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if ($paidAdvanceCount > 1): ?>
                            <div style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); border-radius:12px; padding:12px 18px; text-align:right;">
                                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; opacity:0.9;">Advance Deposit Credits</div>
                                <div style="font-size:1.3rem; font-weight:800; font-family:'Lora',serif; margin-top:2px;">₱<?= number_format($paidAdvanceTotal, 2) ?></div>
                                <div style="font-size:0.75rem; color:#bbf7d0; margin-top:2px;">Covering upcoming months automatically</div>
                            </div>
                            <?php endif; ?>
                        </div>
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
                                                    $bClass = 'rent';
                                                    $disp = 'Charge';
                                                    if ($pay['payment_type'] === 'Deposit') { $bClass = 'deposit'; $disp = 'Security Deposit'; }
                                                    elseif ($pay['payment_type'] === 'Advance') { $bClass = 'rent'; $disp = 'Advance Rent'; }
                                                    elseif ($pay['payment_type'] === 'Water-Advance') { $bClass = 'water'; $disp = 'Water Bill'; }
                                                    elseif ($pay['payment_type'] === 'Contribution-Advance') { $bClass = 'contribution'; $disp = 'Contribution'; }
                                                    elseif ($pay['payment_type'] === 'Parking-Advance') { $bClass = 'parking'; $disp = 'Parking Fee'; }
                                                    else { $disp = htmlspecialchars($pay['payment_type']); }
                                                ?>
                                                <span class="type-badge <?= $bClass ?>"><?= $disp ?></span>
                                            </div>
                                            <div class="breakdown-item-desc">
                                                <?php 
                                                    if($pay['payment_type'] === 'Deposit') echo "Refundable at end of lease"; 
                                                    elseif($pay['payment_type'] === 'Advance') echo "Equivalent to 1 month rent"; 
                                                    elseif($pay['payment_type'] === 'Water-Advance') echo "1st Month Water Consumption"; 
                                                    elseif($pay['payment_type'] === 'Contribution-Advance') echo "1st Month Security & Garbage"; 
                                                    elseif($pay['payment_type'] === 'Parking-Advance') echo "1st Month Parking"; 
                                                ?>
                                                <?php if($isPaid && $pay['reference_number']) echo "<br/><span style='color:var(--text-muted);font-size:0.72rem;font-weight:600;'>Receipt: {$pay['reference_number']}</span>"; ?>
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
                    
                    <!-- ═══════════ SECTION MOVE-OUT: SETTLEMENT ═══════════ -->
<?php if ($moveout && in_array($moveout['status'], ['Processing', 'Completed'])): ?>
                    <div class="section-divider">
                        <span class="divider-line"></span>
                        <span class="divider-label">Move-Out Clearance & Settlement</span>
                        <span class="divider-line"></span>
                    </div>

                    <div class="payment-card" style="border: 1px solid #fecaca; background: #fffcfc;">
                        <div class="payment-header" style="background: linear-gradient(to right, #fff5f5, #ffffff);">
                            <div class="payment-header-left">
                                <div class="payment-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg></div>
                                <h3 class="payment-title">Final Clearance Items</h3>
                            </div>
                            <span class="badge-status <?= $moveout['status'] === 'Processing' ? 'badge-info' : 'badge-approved' ?>" style="font-size:0.7rem;"><?= $moveout['status'] ?></span>
                        </div>

                        <div class="payment-body">
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Settlement Item</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="breakdown-item-name">Damage / Repair Costs</div>
                                            <div class="breakdown-item-desc"><?= htmlspecialchars($moveout['inspection_notes'] ?: 'Final inspection findings') ?></div>
                                            <div style="font-size:0.7rem; color:var(--text-muted); margin-top:4px;">
                                                Status: <?= $moveout['damage_costs'] <= 1000 ? 'Covered by Security Deposit' : 'Excess to be paid' ?>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price" style="color:#dc2626;">+₱<?= number_format($moveout['damage_costs'], 2) ?></div>
                                        </td>
                                    </tr>
                                    <tr style="background: <?= ($recurringTotal <= 0) ? 'rgba(34, 197, 94, 0.05)' : 'rgba(23, 107, 69, 0.02)' ?>;">
                                        <td>
                                            <div class="breakdown-item-name">Unpaid Utility / Bills</div>
                                            <div class="breakdown-item-desc">Final meter reading and outstanding bills</div>
                                            <div style="font-size:0.7rem; color:<?= ($recurringTotal <= 0) ? 'var(--success)' : 'var(--primary)' ?>; font-weight:600; margin-top:4px;">
                                                <?= ($recurringTotal <= 0) ? 'Status: Fully Paid' : 'Must be settled via checkout' ?>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price" style="color:<?= ($recurringTotal <= 0) ? 'var(--success)' : '#dc2626' ?>;">
                                                <?= ($recurringTotal <= 0) ? '₱0.00' : '+₱' . number_format($moveout['utility_deductions'], 2) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="background: #f8fafc; border-top: 2px solid #e2e8f0;">
                                        <td>
                                            <div class="breakdown-item-name" style="color:var(--success);">Security Deposit Account</div>
                                            <div class="breakdown-item-desc">Your original refundable deposit</div>
                                        </td>
                                        <td class="text-right">
                                            <div class="breakdown-item-price" style="color:var(--success);">-₱1,000.00</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="margin-top:20px; padding:20px; background:white; border: 2px dashed #e2e8f0; border-radius:12px; text-align:center;">
                                <label class="stat-label" style="font-size:0.75rem; color:#64748b;">NET REFUNDABLE TO YOU</label>
                                <div style="font-size:2rem; font-weight:900; color:var(--primary);">₱<?= number_format($moveout['final_refund'], 2) ?></div>
                                <?php if($moveout['status'] === 'Processing'): ?>
                                    <p style="font-size:0.8rem; color:#64748b; margin-top:8px;">Please settle the final charges above to complete your clearance.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php 
                        // Logic to check if settlement items are already paid
                        $damagePaid = false;
                        $utilityPaid = false;
                        foreach ($payments as $p) {
                            if ($p['payment_status'] === 'Paid') {
                                if ($p['payment_type'] === 'Move-Out Damage') $damagePaid = true;
                                if ($p['payment_type'] === 'Move-Out Utility') $utilityPaid = true;
                            }
                        }

                        // Add move-out items to JS tracking if processing and NOT yet paid
                        if($moveout['status'] === 'Processing') {
                               // Damage only adds to payment IDs if it EXCEEDS ₱1,000
                               if($moveout['damage_costs'] > 1000 && !$damagePaid) {
                                   $unpaidIds[] = 'settlement-damage';
                                   $unpaidItems[] = ['name' => 'Move-Out: Excess Damage Costs', 'amount' => (float)($moveout['damage_costs'] - 1000)];
                               }
                               // Utilities always add to payment IDs if there is a balance
                               if($moveout['utility_deductions'] > 0 && !$utilityPaid) {
                                   $unpaidIds[] = 'settlement-utility';
                                   $unpaidItems[] = ['name' => 'Move-Out: Unpaid Utilities', 'amount' => (float)$moveout['utility_deductions']];
                               }
                        }
                    ?>
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
                                <?php if (!$moveout || ($moveout['status'] !== 'Processing' && $moveout['status'] !== 'Completed')): ?>
                                <button class="btn-pay" style="padding: 10px 20px; font-size:0.9rem; background:white; color:var(--primary); border:2px solid var(--border); box-shadow:none;" onclick="openAdvancePaymentModal()">
                                    Pay 1 Month Advance Package
                                </button>
                                <?php endif; ?>
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
                        <?php if (!$moveout || ($moveout['status'] !== 'Processing' && $moveout['status'] !== 'Completed')): ?>
                        <button class="btn-pay" style="padding: 12px 24px; font-size:0.95rem; display:inline-flex; align-items:center; gap:8px;" onclick="openAdvancePaymentModal()">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                            Pay 1 Month Advance Package
                        </button>
                        <?php endif; ?>
                    </div>
    <?php endif; ?>
<?php endif; ?>

<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal-overlay" id="paymentModal" onclick="if(event.target===this) closeModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Submit Payment</h3>
                <button class="modal-close" onclick="closeModal()" title="Close">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin: 0 0 4px; color: var(--text-muted); font-size: 0.85rem;">Payment For:</p>
                <h2 style="margin: 0 0 10px; color: var(--primary-dark); font-family: 'Lora', serif;" id="modalPayType">Deposit</h2>
                
                <div id="modalItemList" class="modal-item-list" style="display:none;">
                    <!-- JS injects the list here -->
                </div>
                
                <p style="margin: 0 0 10px; font-weight: 600; font-size: 0.88rem;">Select Payment Method</p>
                <div class="payment-options" style="grid-template-columns: 1fr 1fr;">
                    <div class="payment-option selected" id="opt-paymongo" onclick="selectMethod('paymongo')">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                        <span>PayMongo (GCash / Maya / Card)</span>
                    </div>
                    <div class="payment-option" id="opt-bank" onclick="selectMethod('bank')">
                        <svg viewBox="0 0 24 24"><path d="M12 3L1 9h4v12h14V9h4L12 3zm-1 16H8v-7h3v7zm6 0h-3v-7h3v7z"/></svg>
                        <span>Manual / Bank Reference</span>
                    </div>
                </div>

                <div id="manualRefSection" style="display:none;">
                    <p style="margin: 0 0 6px; font-weight: 600; font-size: 0.88rem;">Reference Number / Receipt ID</p>
                    <input type="text" id="refNumber" class="mock-input" placeholder="e.g. 100234567890" autocomplete="off">
                </div>

                <div id="paymongoNote" style="background: rgba(23, 107, 69, 0.06); border: 1px solid rgba(23, 107, 69, 0.2); border-radius: 8px; padding: 12px; margin-bottom: 20px; font-size: 0.82rem; color: var(--primary-dark); display: flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z"/></svg>
                    <span>You will be redirected to <strong>PayMongo</strong> to complete payment via GCash, Maya, or Card (Test Mode).</span>
                </div>

                <button class="btn-submit" id="btnConfirmPay" onclick="submitPayment()">Pay ₱<span id="modalAmount">0</span> via PayMongo</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-msg" id="toastMsg">Payment Successful!</div>

    <script>
        const unpaidIds = <?= json_encode($unpaidIds ?? []) ?>;
        const unpaidItems = <?= json_encode($unpaidItems ?? []) ?>;
        const grandTotal = <?= $grandTotal ?? 0 ?>;
        const advPayloadDataDefault = <?= json_encode($advPayload ?? ['Rent-Advance']) ?>;
        const advItemsDefault = <?= json_encode($advItems ?? []) ?>;
        const advCostDefault = <?= (float)($advCost ?? 0) ?>;
        
        let selectedMethod = 'paymongo';
        let isAdvanceMode = false;
        let currentModalItems = [];
        let currentModalAmount = 0;
        let advancePayloadData = advPayloadDataDefault;

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
            currentModalItems = unpaidItems;
            currentModalAmount = parseFloat(grandTotal);

            document.getElementById('modalPayType').textContent = 'All Outstanding Balances';
            document.getElementById('modalAmount').textContent = currentModalAmount.toFixed(2);
            document.getElementById('refNumber').value = 'PAY-BLK-' + Math.floor(Math.random() * 10000000);
            
            // Build itemized list UI
            const listDiv = document.getElementById('modalItemList');
            listDiv.style.display = 'block';
            let html = '<ul style="margin:0; padding-left:18px; padding-right:8px;">';
            unpaidItems.forEach(item => {
                html += `<li style="margin-bottom:6px;"><span style="font-weight:600;">${item.name}</span> <span style="float:right; font-family:monospace;">₱${parseFloat(item.amount).toFixed(2)}</span></li>`;
            });
            html += '</ul>';
            listDiv.innerHTML = html;

            selectMethod(selectedMethod || 'paymongo');
            document.getElementById('paymentModal').classList.add('show');
        }

        function openAdvancePaymentModal(payloadArray, itemsArray, totalCost) {
            isAdvanceMode = true;
            advancePayloadData = (payloadArray && payloadArray.length) ? payloadArray : advPayloadDataDefault;
            currentModalItems = (itemsArray && itemsArray.length) ? itemsArray : advItemsDefault;
            currentModalAmount = parseFloat(totalCost !== undefined ? totalCost : advCostDefault);

            document.getElementById('modalPayType').textContent = 'Advance Payment (1 Month Package)';
            document.getElementById('modalAmount').textContent = currentModalAmount.toFixed(2);
            document.getElementById('refNumber').value = 'PAY-ADV-' + Math.floor(Math.random() * 10000000);
            
            // Build itemized list UI using the passed items array
            const listDiv = document.getElementById('modalItemList');
            listDiv.style.display = 'block';
            let html = '<ul style="margin:0; padding-left:18px; padding-right:8px;">';
            currentModalItems.forEach(item => {
                html += `<li style="margin-bottom:6px;"><span style="font-weight:600;">${item.name}</span> <span style="float:right; font-family:monospace;">₱${parseFloat(item.amount).toFixed(2)}</span></li>`;
            });
            html += '</ul>';
            listDiv.innerHTML = html;

            selectMethod(selectedMethod || 'paymongo');
            document.getElementById('paymentModal').classList.add('show');
        }

        // Auto show toast if redirected back from successful PayMongo checkout
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('paid') === '1') {
                const ref = params.get('ref') || '';
                showToast('PayMongo payment confirmed! Ref: ' + ref);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        function closeModal() {
            document.getElementById('paymentModal').classList.remove('show');
        }

        function selectMethod(method) {
            selectedMethod = method;
            document.getElementById('opt-paymongo').classList.remove('selected');
            document.getElementById('opt-bank').classList.remove('selected');
            document.getElementById('opt-' + method).classList.add('selected');

            const manualSection = document.getElementById('manualRefSection');
            const paymongoNote  = document.getElementById('paymongoNote');
            const btn = document.getElementById('btnConfirmPay');
            const amt = document.getElementById('modalAmount').textContent;

            if (method === 'paymongo') {
                manualSection.style.display = 'none';
                paymongoNote.style.display  = 'flex';
                btn.innerHTML = 'Pay ₱' + amt + ' via PayMongo';
            } else {
                manualSection.style.display = 'block';
                paymongoNote.style.display  = 'none';
                btn.innerHTML = 'Confirm Manual Payment of ₱' + amt;
            }
        }

        function submitPayment() {
            const btn = document.getElementById('btnConfirmPay');
            let paymentPayload = isAdvanceMode ? advancePayloadData : unpaidIds;
            let amount = currentModalAmount;
            let items = currentModalItems;

            if (selectedMethod === 'paymongo') {
                btn.disabled = true;
                btn.innerHTML = 'Connecting to PayMongo...';

                fetch('<?= url("/user/apartment/payment/paymongo-checkout") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payment_id: paymentPayload,
                        amount: amount,
                        items: items
                    })
                })
                .then(async r => {
                    const text = await r.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(text || ('HTTP ' + r.status));
                    }
                })
                .then(res => {
                    if (res.success && res.checkout_url) {
                        btn.innerHTML = 'Redirecting...';
                        window.location.href = res.checkout_url;
                    } else {
                        showToast(res.message || 'Could not initiate PayMongo checkout.', true);
                        btn.disabled = false;
                        btn.innerHTML = 'Pay ₱' + document.getElementById('modalAmount').textContent + ' via PayMongo';
                    }
                })
                .catch(err => {
                    console.error('PayMongo Checkout Error:', err);
                    showToast(err.message || 'Error connecting to payment gateway.', true);
                    btn.disabled = false;
                    btn.innerHTML = 'Pay ₱' + document.getElementById('modalAmount').textContent + ' via PayMongo';
                });
                return;
            }

            // Manual / Bank Transfer Reference Flow
            const refNo = document.getElementById('refNumber').value.trim();
            if (!refNo) {
                showToast('Please enter your reference number or deposit slip ID.', true);
                return;
            }

            btn.disabled = true;
            btn.innerHTML = 'Processing...';

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
                    btn.innerHTML = 'Confirm Manual Payment of ₱' + document.getElementById('modalAmount').textContent;
                }
            })
            .catch(err => {
                showToast('Network error.', true);
                btn.disabled = false;
                btn.innerHTML = 'Confirm Manual Payment';
            });
        }
    </script>
</body>
</html>
