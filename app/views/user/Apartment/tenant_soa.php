<?php $active_page = 'apartment_soa'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Official Statement of Account</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    /* Fix sidebar icon size issue on hover */
    .sidebar .nav-item svg, 
    .sidebar .nav-dropdown-trigger svg {
      width: 20px !important;
      height: 20px !important;
    }
    .nav-dropdown a svg {
      width: 14px !important;
      height: 14px !important;
    }
    
    /* Exact style clone from Admin SOA */
    .soa-container { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 40px; max-width: 900px; margin: 20px auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
    .soa-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--primary-dark); padding-bottom: 20px; margin-bottom: 30px; }
    .soa-brand { display: flex; align-items: center; gap: 15px; }
    .soa-brand img { width: 80px; height: 80px; border-radius: 8px; }
    .soa-brand-text h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; font-family: inherit; }
    .soa-brand-text p { margin: 5px 0 0; font-size: 0.85rem; color: var(--text-muted); }
    .soa-title { text-align: right; }
    .soa-title h1 { margin: 0; color: var(--text-main); font-size: 2rem; text-transform: uppercase; letter-spacing: 2px; }
    .soa-title p { margin: 5px 0 0; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; }
    .soa-details { display: flex; justify-content: space-between; margin-bottom: 30px; background: #f8f9fa; padding: 20px; border-radius: 8px; }
    .soa-details-left p, .soa-details-right p { margin: 5px 0; font-size: 0.95rem; }
    .soa-details strong { color: var(--primary-dark); }
    .soa-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .soa-table th { background: var(--primary-dark); color: white; padding: 12px; text-align: left; font-size: 0.85rem; }
    .soa-table td { padding: 10px 12px; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
    .soa-table tr:hover { background: #f8faf9; }
    .type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .type-badge.rent { background: rgba(23,107,69,0.1); color: #176b45; }
    .type-badge.deposit { background: rgba(59,130,246,0.1); color: #2563eb; }
    .type-badge.advance { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .type-badge.parking { background: rgba(245,158,11,0.1); color: #b45309; }
    .type-badge.water { background: rgba(6,182,212,0.1); color: #0891b2; }
    .type-badge.payment { background: rgba(34,197,94,0.1); color: #16a34a; }
    .type-badge.contribution { background: rgba(156,163,175,0.15); color: #4b5563; }
    .soa-summary { width: 100%; max-width: 400px; margin-left: auto; }
    .soa-summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
    .soa-summary-row.total { font-size: 1.2rem; font-weight: 700; color: var(--primary-dark); border-bottom: none; border-top: 2px solid var(--primary-dark); margin-top: 10px; padding-top: 15px; }
    .soa-section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--primary); padding: 12px; background: rgba(23,107,69,0.04); border-left: 3px solid var(--primary); }
    .breakdown-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px; padding: 0 12px; }
    .breakdown-card { background: #f8faf9; border-radius: 10px; padding: 14px 16px; border: 1px solid var(--border); }
    .breakdown-card .bk-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--text-muted); margin-bottom: 6px; }
    .breakdown-card .bk-value { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
    .btn-topbar.primary { background: var(--primary); color: white; display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; border: none; cursor: pointer; transition: 0.2s; }
    .btn-topbar.primary:hover { filter: brightness(1.1); transform: translateY(-1px); }

    .soa-stamp {
      position: absolute;
      top: 30%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-15deg);
      font-size: 5rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      pointer-events: none;
      z-index: 10;
      border: 8px solid;
      padding: 10px 40px;
      border-radius: 20px;
    }
    .soa-stamp.paid {
      color: rgba(22, 163, 74, 0.12); /* Green */
      border-color: rgba(22, 163, 74, 0.12);
    }
    .soa-stamp.unpaid {
      color: rgba(220, 38, 38, 0.08); /* Red */
      border-color: rgba(220, 38, 38, 0.08);
    }

    @media print {
      body * { visibility: hidden; }
      .soa-container, .soa-container * { visibility: visible; }
      .soa-container { position: absolute; left: 0; top: 0; width: 100%; max-width: 100%; box-shadow: none; border: none; margin: 0; padding: 20px; }
      .top-bar, .sidebar { display: none !important; }
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <!-- ═══ USER SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/user/sidebar.php'; ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Statement of Account</div>
            <div class="top-bar-subtitle">Your official billing history and current balance</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <button class="btn-topbar primary" onclick="window.print()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Print Statement
          </button>
        </div>
      </div>

      <div class="page-body">
        
        <?php if (!$lease): ?>
          <div style="text-align:center; padding:60px 20px; background:white; border-radius:12px; border:1px solid var(--border);">
            <p>No active lease found. Your statement will appear here once your application is final.</p>
          </div>
        <?php else: 
          // Metrics same as Admin logic
          $metrics = ['Rent' => 0, 'Deposit' => 0, 'Parking' => 0, 'Water' => 0, 'Payments' => 0];
          foreach($transactions as $t) {
            if($t['payment'] > 0) $metrics['Payments'] += $t['payment'];
            else {
              $type = strtolower($t['type']);
              if(strpos($type, 'rent') !== false) $metrics['Rent'] += $t['charge'];
              elseif(strpos($type, 'deposit') !== false) $metrics['Deposit'] += $t['charge'];
              elseif(strpos($type, 'parking') !== false) $metrics['Parking'] += $t['charge'];
              elseif(strpos($type, 'water') !== false) $metrics['Water'] += $t['charge'];
            }
          }
        ?>
        <!-- Filter Form (Hidden from print) -->
        <div class="filter-bar" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
          <form method="GET" action="" style="display:flex; gap:10px; align-items:center;">
            <label for="monthFilter" style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">Filter by Month:</label>
            <select name="month" id="monthFilter" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border); font-family: inherit;">
              <option value="all" <?= $filterMonth === 'all' ? 'selected' : '' ?>>All Time</option>
              <?php foreach ($availableMonths as $am): 
                $amLabel = date('F Y', strtotime($am . '-01'));
              ?>
                <option value="<?= $am ?>" <?= $filterMonth === $am ? 'selected' : '' ?>><?= $amLabel ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>

        <!-- Statement Document (The Exact Admin View) -->
        <div class="soa-container" style="position: relative;">
          <div class="soa-header">
            <div class="soa-brand">
              <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo">
              <div class="soa-brand-text">
                <h2>ISCAG Management Information System</h2>
                <p>Darul Iman Apartment Complex</p>
                <p>Dasmariñas, Cavite, Philippines</p>
              </div>
            </div>
            <div class="soa-title">
              <h1>Statement of Account</h1>
              <p>Generated on: <?= date('F d, Y') ?></p>
            </div>
          </div>

          <div class="soa-details">
            <div class="soa-details-left">
              <p>Bill To:</p>
              <p><strong style="font-size:1.2rem;"><?= htmlspecialchars($_SESSION['user_full_name'] ?? 'Tenant') ?></strong></p>
              <p>Unit: <strong><?= (!empty($lease['building']) && !empty($lease['room_number'])) ? htmlspecialchars($lease['building'] . '-' . $lease['room_number']) : 'Unassigned' ?></strong></p>
              <p>Email: <?= htmlspecialchars($lease['email'] ?? '') ?></p>
            </div>
            <div class="soa-details-right" style="text-align: right;">
              <p>Statement Period:</p>
              <p><strong><?= $filterMonth === 'all' ? 'All Time' : date('F Y', strtotime($filterMonth . '-01')) ?></strong></p>
              <p style="margin-top:8px;">Occupants: <strong><?= $occupants ?> person(s)</strong></p>
              <p>Room Type: <strong><?= htmlspecialchars($lease['roomtype'] ?? 'Apartment') ?></strong></p>
            </div>
          </div>

          <!-- Exact Breakdown Cards -->
          <div class="breakdown-grid">
            <div class="breakdown-card">
              <div class="bk-label">Monthly Rent</div>
              <div class="bk-value">₱<?= number_format($metrics['Rent'], 2) ?></div>
            </div>
            <div class="breakdown-card">
              <div class="bk-label">Security Deposit</div>
              <div class="bk-value">₱<?= number_format($metrics['Deposit'], 2) ?></div>
            </div>
            <div class="breakdown-card">
              <div class="bk-label">Parking Fee</div>
              <div class="bk-value">₱<?= number_format($metrics['Parking'], 2) ?></div>
            </div>
            <div class="breakdown-card">
              <div class="bk-label">Water Bill</div>
              <div class="bk-value">₱<?= number_format($metrics['Water'], 2) ?></div>
            </div>
          </div>

          <table class="soa-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Reference</th>
                <th style="text-align:right;">Charge</th>
                <th style="text-align:right;">Payment</th>
                <th style="text-align:right;">Balance</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Initialize running balance with the forwarded balance from previous months
              $runningBalance = $balanceForwarded ?? 0; 
              $totalCharges = 0; 
              $totalPayments = 0;
              $lastCat = '';

              if (($filterMonth ?? 'all') !== 'all' && $runningBalance > 0): ?>
                <tr>
                  <td colspan="6"><strong>Balance Forwarded from Previous Months</strong></td>
                  <td style="text-align:right; font-weight:700; color:<?= $runningBalance > 0 ? 'var(--danger)' : 'var(--success)' ?>">₱<?= number_format($runningBalance, 2) ?></td>
                </tr>
              <?php endif;

              foreach ($transactions as $t): 
                $cat = getCategoryLabel($t['type']);
                if ($cat !== $lastCat): ?>
                  <tr><td colspan="7" class="soa-section-title"><?= $cat ?></td></tr>
                  <?php $lastCat = $cat; 
                endif;

                $runningBalance += ($t['charge'] - $t['payment']);
                $totalCharges += $t['charge'];
                $totalPayments += $t['payment'];
              ?>
              <tr class="<?= $t['payment'] > 0 ? 'row-payment' : 'row-charge' ?>">
                <td><?= date('Y-m-d', strtotime($t['date'])) ?></td>
                <td><span class="type-badge <?= getBadgeClass($t['type']) ?>"><?= $t['type'] ?></span></td>
                <td><?= htmlspecialchars($t['description']) ?></td>
                <td style="font-family:monospace; font-size:0.8rem;"><?= htmlspecialchars($t['ref']) ?></td>
                <td style="text-align:right;"><?= $t['charge'] > 0 ? '₱'.number_format($t['charge'], 2) : '-' ?></td>
                <td style="text-align:right; color:var(--success);"><?= $t['payment'] > 0 ? '₱'.number_format($t['payment'], 2) : '-' ?></td>
                <td style="text-align:right; font-weight:700; color:<?= $runningBalance > 0 ? 'var(--danger)' : 'var(--success)' ?>">₱<?= number_format($runningBalance, 2) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>


          <div class="soa-summary">
            <div class="soa-summary-row">
              <span class="label-muted">Total Charges:</span>
              <span>₱<?= number_format($totalCharges ?? 0, 2) ?></span>
            </div>
            <div class="soa-summary-row">
              <span class="label-muted">Total Payments:</span>
              <span style="color:var(--success);">₱<?= number_format($totalPayments ?? 0, 2) ?></span>
            </div>
            <div class="soa-summary-row total">
              <span>Outstanding Balance:</span>
              <span style="color:<?= ($runningBalance ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>">₱<?= number_format($runningBalance ?? 0, 2) ?></span>
            </div>
          </div>
          
          <div style="margin-top: 50px; text-align: center; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border); padding-top: 20px;">
            <p>If you have any questions regarding this statement, please contact the Apartment Admin.</p>
            <p>This is a system-generated document and acts as an official statement of account.</p>
          </div>

          <?php if (isset($runningBalance) && $runningBalance <= 0 && !empty($transactions)): ?>
            <div class="soa-stamp paid">FULLY SETTLED</div>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>

<?php
function getCategoryLabel($type) {
  $t = strtolower($type);
  if (strpos($t, 'rent') !== false && strpos($t, 'payment') === false) return '🏠 Apartment Rent';
  if (strpos($t, 'deposit') !== false || strpos($t, 'advance') !== false) return '💰 Initial Payments';
  if (strpos($t, 'parking') !== false) return '🚗 Parking';
  if (strpos($t, 'water') !== false) return '💧 Water Consumption';
  if (strpos($t, 'contribution') !== false) return '🤝 Contribution';
  if (strpos($t, 'payment') !== false) return '✅ Payment Records';
  return '📋 Other';
}
function getBadgeClass($type) {
  $t = strtolower($type);
  if (strpos($t, 'rent') !== false) return 'rent';
  if (strpos($t, 'deposit') !== false) return 'deposit';
  if (strpos($t, 'advance') !== false) return 'advance';
  if (strpos($t, 'parking') !== false) return 'parking';
  if (strpos($t, 'water') !== false) return 'water';
  if (strpos($t, 'contribution') !== false) return 'contribution';
  if (strpos($t, 'payment') !== false) return 'payment';
  return '';
}
?>
