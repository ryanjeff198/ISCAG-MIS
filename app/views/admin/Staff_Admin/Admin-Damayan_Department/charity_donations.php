<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Donation Records & Verification</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary: #176b45;
      --primary-dark: #0f5c3a;
      --accent: #c79a2b;
      --border: #d9e3de;
      --content-bg: #f4f6f5;
    }

    /* Insights Row */
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 18px 20px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 4px;
      position: relative;
      overflow: hidden;
    }
    .insight-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .insight-card.monetary::after { background: var(--primary); }
    .insight-card.inkind::after { background: var(--accent); }
    .insight-card.available::after { background: var(--success); }
    .insight-card.pending-verif::after { background: #d97706; }
    .insight-card.ack::after { background: #1e5f8b; }

    .insight-label {
      font-size: 0.7rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .insight-value {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.1;
    }

    /* Filter & Search Bar */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      background: white;
      padding: 16px 20px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .filter-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .search-input {
      padding: 8px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      width: 220px;
      outline: none;
      transition: all 0.2s;
    }
    .search-input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }
    .filter-select {
      padding: 8px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-main);
      background: white;
      cursor: pointer;
      outline: none;
    }
    .filter-select:focus {
      border-color: var(--primary);
    }

    /* Badges */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .badge-available { background: rgba(47, 138, 96, 0.12); color: #176b45; }
    .badge-allocated { background: rgba(199, 154, 43, 0.12); color: #a37817; }
    .badge-distributed { background: rgba(31, 111, 90, 0.12); color: #1f6f5a; }
    .badge-cancelled { background: rgba(111, 127, 120, 0.12); color: #6f7f78; }
    
    .badge-pending-verif { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-pending-coord { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
    .badge-pending-rcpt { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Category Badges */
    .badge-cat {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .cat-digital { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .cat-cash { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .cat-food { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

    .badge-ack {
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 700;
    }
    .badge-ack-yes { background: #e8f5ed; color: #0f5c3a; }
    .badge-ack-no { background: #fdf0ed; color: #8b2e2e; }

    /* Modals */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      backdrop-filter: blur(4px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .modal-box {
      background: white;
      border-radius: 20px;
      width: 100%;
      max-width: 680px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0,0,0,0.18);
      animation: modalPop 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes modalPop {
      from { opacity: 0; transform: scale(0.92) translateY(15px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fafbfb;
    }
    .modal-title {
      font-family: 'Lora', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }
    .modal-body { padding: 24px; }
    .modal-footer {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      background: #fafbfb;
    }

    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--text-main);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 6px;
    }
    .form-control {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.88rem;
      color: var(--text-main);
      background: white;
      outline: none;
      transition: all 0.2s;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .btn-action-sm {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 6px;
      border: 1px solid var(--border);
      background: white;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-main);
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-action-sm:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: rgba(23, 107, 69, 0.04);
    }

    .btn-verify-action {
      background: #176b45;
      color: white !important;
      border-color: #176b45;
      font-weight: 700;
    }
    .btn-verify-action:hover {
      background: #0f5c3a;
      border-color: #0f5c3a;
    }

    .proof-preview-box {
      border: 1px dashed var(--border);
      border-radius: 12px;
      padding: 12px;
      text-align: center;
      background: #f8fafc;
      margin-top: 8px;
    }
    .proof-preview-img {
      max-height: 180px;
      max-width: 100%;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      cursor: pointer;
      transition: transform 0.2s;
    }
    .proof-preview-img:hover {
      transform: scale(1.02);
    }
  </style>
  <link rel="stylesheet" href="<?= asset('css/charity.css') ?>?v=<?= time() ?>" />
</head>
<body class="charity-module">
  <div class="app-wrapper">
    <!-- Damayan Sidebar -->
    <?php 
      $active_page = 'charity_donations';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Donation Records & Verification</div>
          <div class="top-bar-subtitle">Review food coordination requests, record cash receipts, and verify digital transactions</div>
        </div>
        <div class="top-bar-actions">
          <button onclick="openRecordModal()" class="btn-topbar" style="background:var(--primary); color:white; border:none; border-radius:8px; padding:9px 18px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Record Direct Donation
          </button>
        </div>
      </div>

      <div class="page-body">
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= url('/admin/damayan/charity') ?>">Charity Programs</a>
          <span class="sep">›</span>
          <span class="current">Donation Records & Verification</span>
        </div>

        <!-- Summary Insights -->
        <div class="admin-insights">
          <div class="insight-card monetary">
            <div class="insight-label">Confirmed Monetary</div>
            <div class="insight-value">₱<?= number_format($stats['received']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['received']['monetary_count'] ?> confirmed monetary donations</div>
          </div>
          <div class="insight-card inkind">
            <div class="insight-label">Confirmed In-Kind / Food</div>
            <div class="insight-value" style="color:var(--accent);"><?= number_format($stats['received']['in_kind']) ?> Units</div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['received']['in_kind_count'] ?> physical packages/items</div>
          </div>
          <div class="insight-card available">
            <div class="insight-label">Available for Allocation</div>
            <div class="insight-value" style="color:var(--success);">₱<?= number_format($stats['available']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= number_format($stats['available']['in_kind']) ?> in-kind items ready</div>
          </div>
          <div class="insight-card pending-verif">
            <div class="insight-label">Awaiting Staff Review</div>
            <div class="insight-value" style="color:#d97706;"><?= (int)($stats['pending_verifications']['total'] ?? 0) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);">
              📱 <?= (int)($stats['pending_verifications']['digital'] ?? 0) ?> Dig &nbsp;|&nbsp;
              💵 <?= (int)($stats['pending_verifications']['cash'] ?? 0) ?> Cash &nbsp;|&nbsp;
              🍲 <?= (int)($stats['pending_verifications']['food'] ?? 0) ?> Food
            </div>
          </div>
          <div class="insight-card ack">
            <div class="insight-label">Matching Registry Records</div>
            <div class="insight-value" style="color:#1e5f8b;"><?= count($donations) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Filtered registry rows</div>
          </div>
        </div>

        <!-- Search & Filter Bar -->
        <form method="GET" action="<?= url('/admin/damayan/charity/donations') ?>" class="filter-bar">
          <div class="filter-controls">
            <input type="text" name="search" class="search-input" placeholder="Search ref, donor, item, or txn..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
            
            <select name="category" class="filter-select" onchange="this.form.submit()">
              <option value="">All Categories (Digital / Cash / Food)</option>
              <option value="digital" <?= ($filters['category'] ?? '') === 'digital' ? 'selected' : '' ?>>📱 Digital Donations</option>
              <option value="cash" <?= ($filters['category'] ?? '') === 'cash' ? 'selected' : '' ?>>💵 Cash Donations</option>
              <option value="food" <?= ($filters['category'] ?? '') === 'food' ? 'selected' : '' ?>>🍲 Food Donations</option>
            </select>

            <select name="type" class="filter-select" onchange="this.form.submit()">
              <option value="">All Resource Types</option>
              <option value="monetary" <?= ($filters['donation_type'] ?? '') === 'monetary' ? 'selected' : '' ?>>Monetary Only</option>
              <option value="in-kind" <?= ($filters['donation_type'] ?? '') === 'in-kind' ? 'selected' : '' ?>>In-Kind / Goods Only</option>
            </select>

            <select name="status" class="filter-select" onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="Pending Verification" <?= ($filters['status'] ?? '') === 'Pending Verification' ? 'selected' : '' ?>>⏳ Pending Verification (Digital)</option>
              <option value="Pending Coordination" <?= ($filters['status'] ?? '') === 'Pending Coordination' ? 'selected' : '' ?>>🤝 Pending Coordination (Food)</option>
              <option value="Pending Receipt" <?= ($filters['status'] ?? '') === 'Pending Receipt' ? 'selected' : '' ?>>🏢 Pending Receipt (Cash)</option>
              <option value="Available" <?= ($filters['status'] ?? '') === 'Available' ? 'selected' : '' ?>>✓ Available (Confirmed)</option>
              <option value="Partially Allocated" <?= ($filters['status'] ?? '') === 'Partially Allocated' ? 'selected' : '' ?>>Partially Allocated</option>
              <option value="Fully Allocated" <?= ($filters['status'] ?? '') === 'Fully Allocated' ? 'selected' : '' ?>>Fully Allocated</option>
              <option value="Rejected" <?= ($filters['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>✕ Rejected / Action Needed</option>
              <option value="Cancelled" <?= ($filters['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">Date:</span>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <span style="color:var(--text-muted);">-</span>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <button type="submit" class="btn-action-sm" style="background:var(--primary); color:white; border-color:var(--primary); padding:8px 14px;">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['donation_type']) || !empty($filters['category']) || !empty($filters['status']) || !empty($filters['date_from'])): ?>
              <a href="<?= url('/admin/damayan/charity/donations') ?>" class="btn-action-sm">Reset</a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Donations Data Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
              Donation Records Registry & Verification (<?= count($donations) ?>)
            </h6>
            <div style="font-size:0.75rem; color:var(--text-muted);">Traceable audit trail from user submission to allocation</div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Category</th>
                    <th>Donor / Sponsor</th>
                    <th>Target / Description</th>
                    <th>Submitted / Amount</th>
                    <th>Available Balance</th>
                    <th>Date / Schedule</th>
                    <th>Payment / Channel</th>
                    <th>Acknowledgment</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($donations)): ?>
                    <tr>
                      <td colspan="11" style="text-align:center; padding:40px; color:var(--text-muted);">
                        No donations found matching the current search criteria.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($donations as $d): ?>
                      <?php 
                        $cat = $d['donation_category'] ?? ($d['donation_type'] === 'in-kind' ? 'food' : 'digital');
                        $catClass = 'cat-digital';
                        $catLabel = '📱 Digital';
                        if ($cat === 'cash') {
                          $catClass = 'cat-cash';
                          $catLabel = '💵 Cash';
                        } elseif ($cat === 'food') {
                          $catClass = 'cat-food';
                          $catLabel = '🍲 Food';
                        }

                        $isPending = in_array($d['status'], ['Pending Verification', 'Pending Coordination', 'Pending Receipt']);
                      ?>
                      <tr>
                        <td>
                          <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($d['donation_ref']) ?></strong>
                        </td>
                        <td>
                          <span class="badge-cat <?= $catClass ?>">
                            <?= $catLabel ?>
                          </span>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($d['donor_name']) ?></div>
                          <?php if (!empty($d['donor_address'])): ?>
                            <div style="font-size:0.73rem; color:var(--text-muted);"><?= htmlspecialchars($d['donor_address']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($cat === 'food' || $d['donation_type'] === 'in-kind'): ?>
                            <div style="font-weight:700; color:#b45309;">
                              <?= htmlspecialchars($d['item_description'] ?: 'Food Items') ?>
                            </div>
                            <div style="font-size:0.73rem; color:var(--text-muted);">
                              Quantity: <?= (int)$d['quantity'] ?> <?= htmlspecialchars($d['unit'] ?? 'packs') ?>
                            </div>
                          <?php else: ?>
                            <div style="font-size:0.8rem; font-weight:700; color:var(--primary);">
                              🎯 <?= htmlspecialchars($d['target_program'] ?: 'General Welfare & Relief') ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($d['donation_type'] === 'monetary'): ?>
                            <strong style="color:var(--primary); font-size:0.95rem;">₱<?= number_format($d['amount'], 2) ?></strong>
                          <?php else: ?>
                            <strong style="color:var(--accent); font-size:0.95rem;"><?= htmlspecialchars($d['quantity']) ?> <?= htmlspecialchars($d['unit'] ?? 'Units') ?></strong>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($d['donation_type'] === 'monetary'): ?>
                            <span style="font-weight:700; color:<?= $d['available_amount'] > 0 ? 'var(--success)' : 'var(--text-muted)' ?>;">
                              ₱<?= number_format($d['available_amount'], 2) ?>
                            </span>
                          <?php else: ?>
                            <span style="font-weight:700; color:<?= $d['available_quantity'] > 0 ? 'var(--success)' : 'var(--text-muted)' ?>;">
                              <?= $d['available_quantity'] ?> Available
                            </span>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                          <?php if ($cat === 'food' && !empty($d['preferred_dropoff_date'])): ?>
                            <div style="font-weight:600; color:#b45309;">
                              📅 Pref: <?= date('M d, Y', strtotime($d['preferred_dropoff_date'])) ?>
                            </div>
                            <div style="font-size:0.7rem; color:var(--text-muted);">Rec: <?= date('M d, Y', strtotime($d['date_received'])) ?></div>
                          <?php else: ?>
                            <?= date('M d, Y', strtotime($d['date_received'])) ?>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.82rem;">
                          <?php if ($cat === 'food'): ?>
                            <span style="color:#b45309; font-weight:600;">Drop-off ISCAG</span>
                          <?php else: ?>
                            <div><?= htmlspecialchars($d['payment_method'] ?: 'Direct') ?></div>
                          <?php endif; ?>

                          <?php if (!empty($d['payment_reference'])): ?>
                            <div style="font-size:0.72rem; color:var(--text-muted); font-family:monospace;">
                              Ref: <?= htmlspecialchars($d['payment_reference']) ?>
                            </div>
                          <?php endif; ?>

                          <?php if (!empty($d['receipt_path'])): ?>
                            <a href="<?= asset($d['receipt_path']) ?>" target="_blank" class="btn-action-sm" style="font-size:0.7rem; padding:1px 6px; margin-top:2px; color:#0369a1; border-color:#bae6fd; background:#f0f9ff;" title="Open Proof Receipt in New Tab">
                              📷 Proof
                            </a>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($d['ack_issued']): ?>
                            <span class="badge-ack badge-ack-yes">Issued</span>
                          <?php else: ?>
                            <span class="badge-ack badge-ack-no">Not Issued</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php 
                            $st = $d['status'];
                            $stClass = 'badge-available';
                            if ($st === 'Partially Allocated') $stClass = 'badge-allocated';
                            elseif ($st === 'Fully Allocated') $stClass = 'badge-distributed';
                            elseif ($st === 'Cancelled') $stClass = 'badge-cancelled';
                            elseif ($st === 'Pending Verification') $stClass = 'badge-pending-verif';
                            elseif ($st === 'Pending Coordination') $stClass = 'badge-pending-coord';
                            elseif ($st === 'Pending Receipt') $stClass = 'badge-pending-rcpt';
                            elseif ($st === 'Rejected') $stClass = 'badge-rejected';
                          ?>
                          <span class="badge-status <?= $stClass ?>"><?= htmlspecialchars($st) ?></span>
                        </td>
                        <td style="text-align:right;">
                          <div style="display:inline-flex; gap:6px;">
                            <?php if ($isPending): ?>
                              <button onclick="openVerifyModal(<?= $d['id'] ?>)" class="btn-action-sm btn-verify-action" title="Review & Verify Donation">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Review
                              </button>
                            <?php else: ?>
                              <button onclick="openVerifyModal(<?= $d['id'] ?>)" class="btn-action-sm" title="Update Verification Status">
                                Status
                              </button>
                            <?php endif; ?>

                            <button onclick="viewDonation(<?= $d['id'] ?>)" class="btn-action-sm" title="View Full Details">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                              View
                            </button>
                            <button onclick="editDonation(<?= $d['id'] ?>)" class="btn-action-sm" title="Edit Permitted Info">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                              Edit
                            </button>
                            <a href="<?= url('/admin/damayan/charity/donations/print-ack?id=' . $d['id']) ?>" target="_blank" class="btn-action-sm" title="Print Acknowledgment Receipt">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                              Ack
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL 1: RECORD / EDIT DONATION FORM (DIRECT ADMIN ENTRY) -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="donation-form-modal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title" id="form-modal-title">Record Direct Donation</h5>
        <button onclick="closeRecordModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <form id="donation-form" onsubmit="submitDonationForm(event)">
        <input type="hidden" name="action" id="form-action" value="create" />
        <input type="hidden" name="id" id="form-id" value="" />

        <div class="modal-body">
          <div id="form-error" style="display:none; padding:12px 16px; background:#fdf0ed; color:#8b2e2e; border-radius:8px; margin-bottom:16px; font-weight:600; font-size:0.85rem;"></div>

          <!-- Donor Name -->
          <div class="form-group">
            <label>Donor or Sponsor Full Name <span style="color:#8b2e2e;">*</span></label>
            <input type="text" name="donor_name" id="form-donor-name" required class="form-control" placeholder="e.g. Haji Omar Al-Farouq / Al-Ihsan Foundation" />
          </div>

          <!-- Donor Address -->
          <div class="form-group">
            <label>Donor Address / City</label>
            <input type="text" name="donor_address" id="form-donor-address" class="form-control" placeholder="e.g. Dasmariñas, Cavite / Quiapo, Manila" />
          </div>

          <!-- Category & Type Selection -->
          <div class="form-row">
            <div class="form-group">
              <label>Donation Category <span style="color:#8b2e2e;">*</span></label>
              <select name="donation_category" id="form-donation-category" class="form-control" onchange="handleCategoryChange(this.value)">
                <option value="digital">Digital Donation (Online E-Wallet / Bank)</option>
                <option value="cash">Cash Donation (Physical Cash Receipt)</option>
                <option value="food">Food Donation (Food Items & Supplies)</option>
              </select>
            </div>
            <div class="form-group">
              <label>Resource Classification</label>
              <select name="donation_type" id="form-donation-type" class="form-control" readonly>
                <option value="monetary">Monetary Donation</option>
                <option value="in-kind">In-Kind (Physical Goods)</option>
              </select>
            </div>
          </div>

          <!-- Monetary Fields -->
          <div id="monetary-fields">
            <div class="form-row">
              <div class="form-group">
                <label>Donation Amount (₱) <span style="color:#8b2e2e;">*</span></label>
                <input type="number" step="0.01" min="1" name="amount" id="form-amount" class="form-control" placeholder="0.00" />
              </div>
              <div class="form-group">
                <label>Target Charity Program</label>
                <select name="target_program" id="form-target-program" class="form-control">
                  <option value="General Welfare & Relief">General Welfare & Relief</option>
                  <option value="Food Assistance & Nutrition">Food Assistance & Nutrition</option>
                  <option value="Burial Assistance (Janazah Support)">Burial Assistance (Janazah Support)</option>
                  <option value="Educational Support">Educational Support</option>
                  <option value="Orphan & Widow Care">Orphan & Widow Care</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Food / In-Kind Fields -->
          <div id="inkind-fields" style="display:none;">
            <div class="form-row">
              <div class="form-group">
                <label>Food Item / Supply Description <span style="color:#8b2e2e;">*</span></label>
                <input type="text" name="item_description" id="form-item-desc" class="form-control" placeholder="e.g. Sacks of Rice (25kg), Halal Canned Goods" />
              </div>
              <div class="form-group">
                <label>Quantity & Unit <span style="color:#8b2e2e;">*</span></label>
                <div style="display:flex; gap:8px;">
                  <input type="number" min="1" name="quantity" id="form-quantity" class="form-control" placeholder="Qty" style="flex:1;" />
                  <input type="text" name="unit" id="form-unit" class="form-control" placeholder="packs, sacks, boxes" style="flex:1;" value="packs" />
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Preferred Drop-off Date</label>
              <input type="date" name="preferred_dropoff_date" id="form-dropoff-date" class="form-control" />
            </div>
          </div>

          <div class="form-row">
            <!-- Date Received -->
            <div class="form-group">
              <label>Date Received / Logged <span style="color:#8b2e2e;">*</span></label>
              <input type="date" name="date_received" id="form-date-received" required value="<?= date('Y-m-d') ?>" class="form-control" />
            </div>
            <!-- Received By -->
            <div class="form-group">
              <label>Received / Managed By <span style="color:#8b2e2e;">*</span></label>
              <input type="text" name="received_by" id="form-received-by" required value="<?= htmlspecialchars($_SESSION['name'] ?? 'Damayan Staff') ?>" class="form-control" />
            </div>
          </div>

          <div class="form-row">
            <!-- Payment Method -->
            <div class="form-group">
              <label>Payment Method / Delivery Channel</label>
              <select name="payment_method" id="form-payment-method" class="form-control">
                <option value="GCash">GCash</option>
                <option value="Maya">Maya</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash (ISCAG Office)">Cash (ISCAG Office)</option>
                <option value="In-Person Delivery">In-Person Drop-off</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <!-- Payment Reference -->
            <div class="form-group">
              <label>Transaction Reference # (If applicable)</label>
              <input type="text" name="payment_reference" id="form-payment-ref" class="form-control" placeholder="e.g. TRX-99214 or GCash Ref #" />
            </div>
          </div>

          <!-- Initial Status -->
          <div class="form-group">
            <label>Initial Status</label>
            <select name="status" id="form-status" class="form-control">
              <option value="Available">Available (Confirmed into Inventory/Funds)</option>
              <option value="Pending Verification">Pending Verification</option>
              <option value="Pending Coordination">Pending Coordination</option>
              <option value="Pending Receipt">Pending Receipt</option>
            </select>
          </div>

          <!-- Remarks -->
          <div class="form-group" style="margin-bottom:0;">
            <label>Remarks / Donor Notes</label>
            <textarea name="remarks" id="form-remarks" rows="2" class="form-control" placeholder="Any specific wishes, designated intent, or special instructions..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" onclick="closeRecordModal()" class="btn-action-sm" style="padding:9px 18px;">Cancel</button>
          <button type="submit" id="btn-save-donation" class="btn-action-sm" style="background:var(--primary); color:white; border-color:var(--primary); padding:9px 24px; font-weight:700;">
            Confirm & Save Donation
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL 2: VERIFICATION & COORDINATION REVIEW MODAL -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="donation-verify-modal" class="modal-backdrop">
    <div class="modal-box" style="max-width:700px;">
      <div class="modal-header">
        <h5 class="modal-title" id="verify-modal-title">Review & Verify Donation</h5>
        <button onclick="closeVerifyModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <form id="verify-form" onsubmit="submitVerifyForm(event)">
        <input type="hidden" name="id" id="verify-id" value="" />

        <div class="modal-body" id="verify-body">
          <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading submission details...</div>
        </div>

        <div class="modal-footer">
          <button type="button" onclick="closeVerifyModal()" class="btn-action-sm" style="padding:9px 18px;">Cancel</button>
          <button type="submit" id="btn-submit-verify" class="btn-action-sm" style="background:var(--primary); color:white; border-color:var(--primary); padding:9px 24px; font-weight:700;">
            Save Verification Decision
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL 3: DONATION DETAILS & ALLOCATION/DISTRIBUTION HISTORY -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="donation-view-modal" class="modal-backdrop">
    <div class="modal-box" style="max-width:780px;">
      <div class="modal-header">
        <h5 class="modal-title" id="view-title">Donation Details</h5>
        <button onclick="closeViewModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <div class="modal-body" id="view-body">
        <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading details...</div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeViewModal()" class="btn-action-sm" style="padding:8px 18px;">Close</button>
      </div>
    </div>
  </div>

  <script>
    function handleCategoryChange(cat) {
      const typeSelect = document.getElementById('form-donation-type');
      if (cat === 'food') {
        typeSelect.value = 'in-kind';
        document.getElementById('monetary-fields').style.display = 'none';
        document.getElementById('inkind-fields').style.display = 'block';
        document.getElementById('form-amount').removeAttribute('required');
        document.getElementById('form-item-desc').setAttribute('required', 'required');
        document.getElementById('form-quantity').setAttribute('required', 'required');
        document.getElementById('form-payment-method').value = 'In-Person Delivery';
      } else {
        typeSelect.value = 'monetary';
        document.getElementById('monetary-fields').style.display = 'block';
        document.getElementById('inkind-fields').style.display = 'none';
        document.getElementById('form-amount').setAttribute('required', 'required');
        document.getElementById('form-item-desc').removeAttribute('required');
        document.getElementById('form-quantity').removeAttribute('required');
        if (cat === 'cash') {
          document.getElementById('form-payment-method').value = 'Cash (ISCAG Office)';
        } else {
          document.getElementById('form-payment-method').value = 'GCash';
        }
      }
    }

    function openRecordModal() {
      document.getElementById('form-modal-title').textContent = 'Record Direct Donation';
      document.getElementById('form-action').value = 'create';
      document.getElementById('form-id').value = '';
      document.getElementById('donation-form').reset();
      document.getElementById('form-date-received').value = '<?= date('Y-m-d') ?>';
      document.getElementById('form-received-by').value = '<?= htmlspecialchars($_SESSION['name'] ?? 'Damayan Staff') ?>';
      document.getElementById('form-donation-category').value = 'digital';
      handleCategoryChange('digital');
      document.getElementById('form-donation-category').disabled = false;
      document.getElementById('form-error').style.display = 'none';
      document.getElementById('donation-form-modal').style.display = 'flex';
    }

    function closeRecordModal() {
      document.getElementById('donation-form-modal').style.display = 'none';
    }

    function editDonation(id) {
      fetch('<?= url('/admin/damayan/charity/donations/detail') ?>?id=' + id)
        .then(r => r.json())
        .then(res => {
          if (!res.success) {
            alert(res.error || 'Failed to load donation details');
            return;
          }
          const d = res.donation;
          document.getElementById('form-modal-title').textContent = 'Edit Donation (' + d.donation_ref + ')';
          document.getElementById('form-action').value = 'edit';
          document.getElementById('form-id').value = d.id;
          document.getElementById('form-donor-name').value = d.donor_name || '';
          document.getElementById('form-donor-address').value = d.donor_address || '';
          
          const cat = d.donation_category || (d.donation_type === 'in-kind' ? 'food' : 'digital');
          document.getElementById('form-donation-category').value = cat;
          document.getElementById('form-donation-category').disabled = true; // Lock category on edit
          handleCategoryChange(cat);

          if (cat === 'food' || d.donation_type === 'in-kind') {
            document.getElementById('form-item-desc').value = d.item_description || '';
            document.getElementById('form-quantity').value = d.quantity;
            document.getElementById('form-unit').value = d.unit || 'packs';
            document.getElementById('form-dropoff-date').value = d.preferred_dropoff_date || '';
          } else {
            document.getElementById('form-amount').value = d.amount;
            document.getElementById('form-target-program').value = d.target_program || 'General Welfare & Relief';
          }

          document.getElementById('form-date-received').value = d.date_received;
          document.getElementById('form-received-by').value = d.received_by || '';
          document.getElementById('form-payment-method').value = d.payment_method || 'Cash';
          document.getElementById('form-payment-ref').value = d.payment_reference || '';
          document.getElementById('form-status').value = d.status;
          document.getElementById('form-remarks').value = d.remarks || '';

          document.getElementById('form-error').style.display = 'none';
          document.getElementById('donation-form-modal').style.display = 'flex';
        })
        .catch(err => {
          alert('Network error loading donation record.');
        });
    }

    function submitDonationForm(e) {
      e.preventDefault();
      const form = document.getElementById('donation-form');
      const formData = new FormData(form);
      const btn = document.getElementById('btn-save-donation');
      const errBox = document.getElementById('form-error');

      btn.disabled = true;
      btn.textContent = 'Saving...';
      errBox.style.display = 'none';

      fetch('<?= url('/admin/damayan/charity/donations/save') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Save Donation';
        if (res.success) {
          closeRecordModal();
          window.location.reload();
        } else {
          errBox.textContent = res.error || 'Failed to save donation.';
          errBox.style.display = 'block';
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Save Donation';
        errBox.textContent = 'Server communication error. Please try again.';
        errBox.style.display = 'block';
      });
    }

    // ─── Verification Modal Handler ─────────────────────────────
    function openVerifyModal(id) {
      document.getElementById('verify-id').value = id;
      document.getElementById('donation-verify-modal').style.display = 'flex';
      const body = document.getElementById('verify-body');
      body.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Loading donation submission details...</div>';

      fetch('<?= url('/admin/damayan/charity/donations/detail') ?>?id=' + id)
        .then(r => r.json())
        .then(res => {
          if (!res.success) {
            body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">' + (res.error || 'Unable to load donation.') + '</div>';
            return;
          }
          const d = res.donation;
          const cat = d.donation_category || (d.donation_type === 'in-kind' ? 'food' : 'digital');
          document.getElementById('verify-modal-title').textContent = 'Review & Verify Donation — ' + d.donation_ref;

          let catBadge = '';
          let specificDetails = '';
          let defaultStatusOptions = '';

          if (cat === 'food') {
            catBadge = '<span class="badge-cat cat-food">🍲 Food Donation</span>';
            specificDetails = `
              <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:14px; margin-bottom:16px;">
                <div style="font-size:0.75rem; font-weight:700; color:#b45309; text-transform:uppercase; margin-bottom:4px;">Food Item & Coordination Details</div>
                <div style="font-size:1.05rem; font-weight:800; color:#92400e; margin-bottom:6px;">
                  ${d.quantity} ${d.unit || 'packs'} — ${d.item_description || 'Food Supplies'}
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.83rem; margin-top:8px;">
                  <div>
                    <span style="color:#92400e; font-weight:600;">Preferred Drop-off Date:</span>
                    <div><strong>${d.preferred_dropoff_date ? d.preferred_dropoff_date : 'Not specified'}</strong></div>
                  </div>
                  <div>
                    <span style="color:#92400e; font-weight:600;">ISCAG Drop-off Location:</span>
                    <div>${d.dropoff_location || 'ISCAG Islamic Complex, Dasmariñas City, Cavite'}</div>
                  </div>
                </div>
              </div>
            `;
            defaultStatusOptions = `
              <option value="Available" ${d.status === 'Available' ? 'selected' : ''}>✓ Confirm Drop-off (Mark Available in Inventory)</option>
              <option value="Pending Coordination" ${d.status === 'Pending Coordination' ? 'selected' : ''}>🤝 Pending Coordination (Staff Contacting Donor)</option>
              <option value="Rejected" ${d.status === 'Rejected' ? 'selected' : ''}>✕ Decline / Unable to Accept</option>
              <option value="Cancelled" ${d.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
            `;
          } else if (cat === 'cash') {
            catBadge = '<span class="badge-cat cat-cash">💵 Cash Donation</span>';
            specificDetails = `
              <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:14px; margin-bottom:16px;">
                <div style="font-size:0.75rem; font-weight:700; color:#047857; text-transform:uppercase; margin-bottom:4px;">Physical Cash Details</div>
                <div style="font-size:1.3rem; font-weight:800; color:#065f46; margin-bottom:6px;">
                  ₱${parseFloat(d.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}
                </div>
                <div style="font-size:0.83rem; color:#047857;">
                  <strong>Target Program:</strong> ${d.target_program || 'General Welfare & Relief'}
                </div>
                <div style="font-size:0.8rem; color:#047857; margin-top:4px;">
                  <em>Verify physical cash handover at the ISCAG office before marking as Confirmed / Available.</em>
                </div>
              </div>
            `;
            defaultStatusOptions = `
              <option value="Available" ${d.status === 'Available' ? 'selected' : ''}>✓ Cash Received & Confirmed (Add to Available Funds)</option>
              <option value="Pending Receipt" ${d.status === 'Pending Receipt' ? 'selected' : ''}>🏢 Pending Receipt (Awaiting Physical Cash at Office)</option>
              <option value="Rejected" ${d.status === 'Rejected' ? 'selected' : ''}>✕ Did Not Remit / Cancel Record</option>
              <option value="Cancelled" ${d.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
            `;
          } else {
            catBadge = '<span class="badge-cat cat-digital">📱 Digital Donation</span>';
            let proofHtml = '';
            if (d.receipt_path) {
              const proofUrl = '<?= asset('') ?>' + d.receipt_path.replace(/^\/+/, '');
              proofHtml = `
                <div class="proof-preview-box">
                  <div style="font-size:0.75rem; font-weight:700; color:#0369a1; text-transform:uppercase; margin-bottom:6px;">Submitted Proof of Payment</div>
                  <a href="${proofUrl}" target="_blank" title="Click to view full image in new tab">
                    <img src="${proofUrl}" alt="Proof Receipt" class="proof-preview-img" onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'40\'><text y=\'25\' font-size=\'12\'>View Receipt</text></svg>';" />
                  </a>
                  <div style="font-size:0.73rem; color:var(--text-muted); margin-top:4px;">Click image to view or download full receipt</div>
                </div>
              `;
            } else {
              proofHtml = `
                <div style="font-size:0.8rem; color:var(--text-muted); padding:10px; background:#f8fafc; border-radius:8px; border:1px dashed var(--border); margin-top:8px;">
                  No proof screenshot attached. Verified by transaction reference number.
                </div>
              `;
            }

            specificDetails = `
              <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:14px; margin-bottom:16px;">
                <div style="font-size:0.75rem; font-weight:700; color:#0369a1; text-transform:uppercase; margin-bottom:4px;">Digital Payment Details</div>
                <div style="font-size:1.3rem; font-weight:800; color:#075985; margin-bottom:4px;">
                  ₱${parseFloat(d.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.83rem; margin-top:6px;">
                  <div>
                    <span style="color:#0369a1; font-weight:600;">Payment Method:</span>
                    <div><strong>${d.payment_method || 'Digital Wallet'}</strong></div>
                  </div>
                  <div>
                    <span style="color:#0369a1; font-weight:600;">Transaction Ref #:</span>
                    <div><strong style="font-family:monospace; color:#0f5c3a;">${d.payment_reference || 'None provided'}</strong></div>
                  </div>
                </div>
                <div style="font-size:0.82rem; color:#0369a1; margin-top:6px;">
                  <strong>Target Program:</strong> ${d.target_program || 'General Welfare & Relief'}
                </div>
                ${proofHtml}
              </div>
            `;
            defaultStatusOptions = `
              <option value="Available" ${d.status === 'Available' ? 'selected' : ''}>✓ Payment Verified & Confirmed (Add to Available Funds)</option>
              <option value="Pending Verification" ${d.status === 'Pending Verification' ? 'selected' : ''}>⏳ Pending Verification (Under Review / Needs Proof)</option>
              <option value="Rejected" ${d.status === 'Rejected' ? 'selected' : ''}>✕ Reject Transaction (Invalid Ref / Non-receipt)</option>
              <option value="Cancelled" ${d.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
            `;
          }

          body.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; background:#fafbfb; padding:12px 16px; border-radius:10px; border:1px solid var(--border);">
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Donor Details</div>
                <div style="font-size:1.05rem; font-weight:700; color:var(--primary-dark);">${d.donor_name}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">${d.donor_address || 'No address provided'}</div>
              </div>
              <div style="text-align:right;">
                <div>${catBadge}</div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Date: ${d.date_received}</div>
              </div>
            </div>

            ${specificDetails}

            ${d.remarks ? `
              <div style="margin-bottom:16px; background:#f9fafb; padding:10px 14px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem;">
                <span style="font-weight:700; color:var(--text-muted); text-transform:uppercase; font-size:0.7rem;">Donor Remarks:</span>
                <div style="color:var(--text-main); margin-top:2px;">${d.remarks}</div>
              </div>
            ` : ''}

            <!-- Verification Action Form Fields -->
            <div class="form-group">
              <label>Verification Decision / New Status <span style="color:#8b2e2e;">*</span></label>
              <select name="status" id="verify-status" class="form-control" style="font-weight:700;">
                ${defaultStatusOptions}
              </select>
              <div style="font-size:0.73rem; color:var(--text-muted); margin-top:4px;">
                Note: Setting status to "Available" will include this donation in the confirmed donation totals and make it available for allocation.
              </div>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label>Admin Notes / Coordination Message (Sent to Donor)</label>
              <textarea name="admin_notes" id="verify-notes" rows="2" class="form-control" placeholder="Provide drop-off coordination notes, confirmation message, or request for additional information...">${d.admin_notes || ''}</textarea>
            </div>
          `;
        })
        .catch(err => {
          body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">Network error loading donation record.</div>';
        });
    }

    function closeVerifyModal() {
      document.getElementById('donation-verify-modal').style.display = 'none';
    }

    function submitVerifyForm(e) {
      e.preventDefault();
      const form = document.getElementById('verify-form');
      const formData = new FormData(form);
      const btn = document.getElementById('btn-submit-verify');

      btn.disabled = true;
      btn.textContent = 'Saving Decision...';

      fetch('<?= url('/admin/damayan/charity/donations/verify') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        btn.disabled = false;
        btn.textContent = 'Save Verification Decision';
        if (res.success) {
          closeVerifyModal();
          window.location.reload();
        } else {
          alert(res.error || 'Failed to update verification status.');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Save Verification Decision';
        alert('Server communication error. Please try again.');
      });
    }

    // ─── View Donation Modal ─────────────────────────────────────
    function viewDonation(id) {
      document.getElementById('donation-view-modal').style.display = 'flex';
      const body = document.getElementById('view-body');
      body.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Loading donation details and audit history...</div>';

      fetch('<?= url('/admin/damayan/charity/donations/detail') ?>?id=' + id)
        .then(r => r.json())
        .then(res => {
          if (!res.success) {
            body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">' + (res.error || 'Unable to load details.') + '</div>';
            return;
          }
          const d = res.donation;
          const cat = d.donation_category || (d.donation_type === 'in-kind' ? 'food' : 'digital');
          document.getElementById('view-title').textContent = 'Donation Record — ' + d.donation_ref;

          let valDisplay = d.donation_type === 'monetary' 
            ? '₱' + parseFloat(d.amount).toLocaleString('en-US', {minimumFractionDigits: 2})
            : d.quantity + ' ' + (d.unit || 'Units') + ' (' + (d.item_description || '') + ')';

          let availDisplay = d.donation_type === 'monetary'
            ? '₱' + parseFloat(d.available_amount).toLocaleString('en-US', {minimumFractionDigits: 2})
            : d.available_quantity + ' ' + (d.unit || 'Units');

          let proofSection = '';
          if (d.receipt_path) {
            const proofUrl = '<?= asset('') ?>' + d.receipt_path.replace(/^\/+/, '');
            proofSection = `
              <div style="margin-top:12px; padding:10px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px;">
                <span style="font-size:0.75rem; font-weight:700; color:#0369a1;">Verified Proof Attachment: </span>
                <a href="${proofUrl}" target="_blank" style="font-size:0.78rem; font-weight:700; color:#0369a1; text-decoration:underline; margin-left:6px;">View Full Image Receipt ↗</a>
              </div>
            `;
          }

          let allocRows = '';
          if (d.allocations && d.allocations.length > 0) {
            d.allocations.forEach(a => {
              const aVal = d.donation_type === 'monetary' ? '₱' + parseFloat(a.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : a.allocated_quantity + ' ' + (d.unit || 'Units');
              allocRows += `<tr>
                <td><strong style="color:var(--primary); font-family:monospace;">${a.allocation_ref}</strong></td>
                <td><span class="dept-tag ${a.receiving_department === 'Damayan' ? 'dept-damayan' : 'dept-dawah'}">${a.receiving_department}</span></td>
                <td><strong>${aVal}</strong></td>
                <td>${a.purpose}</td>
                <td>${a.allocation_date}</td>
                <td><span class="badge-status badge-allocated">${a.status}</span></td>
              </tr>`;
            });
          } else {
            allocRows = '<tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:16px;">No allocations made from this donation yet.</td></tr>';
          }

          let distRows = '';
          if (d.distributions && d.distributions.length > 0) {
            d.distributions.forEach(dist => {
              distRows += `<tr>
                <td><strong style="color:var(--primary); font-family:monospace;">${dist.distribution_ref}</strong></td>
                <td>${dist.items_summary}</td>
                <td><strong>${dist.received_by}</strong></td>
                <td>${dist.distribution_date}</td>
                <td>${dist.distributed_by}</td>
              </tr>`;
            });
          } else {
            distRows = '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:16px;">No distributions recorded yet.</td></tr>';
          }

          body.innerHTML = `
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:16px; background:#f9fafb; padding:18px; border-radius:12px; border:1px solid var(--border);">
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Donor / Category</div>
                <div style="font-size:1.05rem; font-weight:700; color:var(--primary-dark);">${d.donor_name}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">${d.donor_address || 'No address provided'}</div>
                <div style="margin-top:6px;"><span class="badge-cat ${cat === 'food' ? 'cat-food' : (cat === 'cash' ? 'cat-cash' : 'cat-digital')}">${cat.toUpperCase()} DONATION</span></div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Donated</div>
                <div style="font-size:1.1rem; font-weight:800; color:var(--primary);">${valDisplay}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">${d.payment_method || 'N/A'} ${d.payment_reference ? '(' + d.payment_reference + ')' : ''}</div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Remaining Balance</div>
                <div style="font-size:1.1rem; font-weight:800; color:var(--success);">${availDisplay}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Status: <strong>${d.status}</strong></div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Acknowledgment</div>
                <div style="margin-top:4px;">
                  <span class="badge-ack ${d.ack_issued == 1 ? 'badge-ack-yes' : 'badge-ack-no'}">
                    ${d.ack_issued == 1 ? 'Issued on ' + (d.ack_issued_at ? d.ack_issued_at.substring(0,10) : '') : 'Not Issued'}
                  </span>
                </div>
                <div style="margin-top:6px;">
                  <button onclick="toggleAck(${d.id})" class="btn-action-sm" style="font-size:0.7rem;">
                    ${d.ack_issued == 1 ? 'Revoke Ack' : 'Mark as Issued'}
                  </button>
                  <a href="<?= url('/admin/damayan/charity/donations/print-ack?id=') ?>${d.id}" target="_blank" class="btn-action-sm" style="font-size:0.7rem;">
                    Print Ack
                  </a>
                </div>
              </div>
            </div>

            ${proofSection}

            ${d.admin_notes ? `
              <div style="margin-bottom:16px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; font-size:0.83rem;">
                <span style="font-weight:700; color:#1e40af; text-transform:uppercase; font-size:0.7rem;">Staff Verification / Coordination Notes:</span>
                <div style="color:#1e3a8a; margin-top:2px;">${d.admin_notes}</div>
              </div>
            ` : ''}

            <div style="margin-bottom:20px; margin-top:16px;">
              <h6 style="font-size:0.88rem; font-weight:700; margin-bottom:8px; color:var(--primary-dark);">Allocation History (Departments)</h6>
              <div class="table-wrapper" style="border:1px solid var(--border); border-radius:10px;">
                <table class="data-table" style="font-size:0.82rem;">
                  <thead>
                    <tr>
                      <th>Alloc Ref</th>
                      <th>Dept</th>
                      <th>Allocated</th>
                      <th>Purpose</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>${allocRows}</tbody>
                </table>
              </div>
            </div>

            <div>
              <h6 style="font-size:0.88rem; font-weight:700; margin-bottom:8px; color:var(--primary-dark);">Distribution History (End Beneficiaries)</h6>
              <div class="table-wrapper" style="border:1px solid var(--border); border-radius:10px;">
                <table class="data-table" style="font-size:0.82rem;">
                  <thead>
                    <tr>
                      <th>Dist Ref</th>
                      <th>Resources Handed Out</th>
                      <th>Beneficiary / Recipient</th>
                      <th>Date</th>
                      <th>Staff</th>
                    </tr>
                  </thead>
                  <tbody>${distRows}</tbody>
                </table>
              </div>
            </div>
          `;
        })
        .catch(err => {
          body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">Network error loading details.</div>';
        });
    }

    function closeViewModal() {
      document.getElementById('donation-view-modal').style.display = 'none';
    }

    function toggleAck(id) {
      if (!confirm('Update acknowledgment issuance status for this donation?')) return;
      const fd = new FormData();
      fd.append('id', id);

      fetch('<?= url('/admin/damayan/charity/donations/ack') ?>', {
        method: 'POST',
        body: fd
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          viewDonation(id);
        } else {
          alert(res.error || 'Failed to update acknowledgment.');
        }
      });
    }
  </script>
</body>
</html>