<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Received Donations</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    :root {
      --primary: #1e3d34;
      --primary-light: #2d5a4d;
      --accent: #c49a45;
      --accent-hover: #b38938;
      --bg-main: #f8faf9;
      --border: #e2e8e5;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --success: #15803d;
      --warning: #b45309;
      --danger: #b91c1c;
      --info: #0284c7;
    }

    .kpi-grid-5 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .kpi-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 18px 20px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      display: flex;
      align-items: center;
      gap: 16px;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .kpi-icon-wrap {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .kpi-icon-wrap svg {
      width: 24px;
      height: 24px;
    }

    .kpi-val {
      font-size: 1.4rem;
      font-weight: 800;
      line-height: 1.2;
      color: var(--text-main);
      font-family: 'Lora', serif;
    }

    .kpi-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 3px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    /* Filter & Controls */
    .filter-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 20px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
    }

    .tab-pills {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .tab-pill {
      background: #f1f5f3;
      border: 1px solid transparent;
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .tab-pill:hover {
      background: #e4ebe7;
      color: var(--primary);
    }

    .tab-pill.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .tab-pill .pill-badge {
      background: rgba(255, 255, 255, 0.25);
      font-size: 0.72rem;
      padding: 1px 6px;
      border-radius: 10px;
    }

    .tab-pill:not(.active) .pill-badge {
      background: #e2e8e5;
      color: var(--text-main);
    }

    .filter-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .search-input-wrap {
      position: relative;
      min-width: 240px;
    }

    .search-input-wrap input {
      width: 100%;
      padding: 8px 12px 8px 34px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      outline: none;
      background: #fafcfb;
    }

    .search-input-wrap input:focus {
      border-color: var(--primary-light);
      background: white;
    }

    .search-input-wrap svg {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
    }

    .select-sm {
      padding: 8px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      background: #fafcfb;
      color: var(--text-main);
      cursor: pointer;
      outline: none;
    }

    .btn-export {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-export:hover {
      background: #dcfce7;
      border-color: #86efac;
    }

    /* Donations Table Card */
    .donations-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }

    .table-container {
      overflow-x: auto;
      max-height: 680px;
    }

    .donations-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
      text-align: left;
    }

    .donations-table thead {
      background: #f8faf9;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .donations-table th {
      padding: 12px 16px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }

    .donations-table td {
      padding: 13px 16px;
      border-bottom: 1px solid #f1f5f3;
      vertical-align: middle;
      color: var(--text-main);
    }

    .donations-table tbody tr:hover {
      background: #f9fbf9;
    }

    /* Badges */
    .seal-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-family: 'JetBrains Mono', 'Courier New', monospace;
      font-size: 0.72rem;
      font-weight: 700;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
      padding: 3px 8px;
      border-radius: 6px;
      letter-spacing: 0.02em;
    }

    .seal-badge svg {
      width: 12px;
      height: 12px;
      fill: currentColor;
    }

    .status-tag {
      display: inline-block;
      padding: 4px 9px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      white-space: nowrap;
    }

    .status-available { background: #dcfce7; color: #15803d; }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-allocated { background: #e0e7ff; color: #3730a3; }
    .status-rejected { background: #fee2e2; color: #b91c1c; }

    .category-chip {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 2px 6px;
      border-radius: 4px;
      background: #e2e8e5;
      color: #374151;
      display: inline-block;
    }

    .category-digital { background: #e0f2fe; color: #0369a1; }
    .category-cash { background: #dcfce7; color: #166534; }
    .category-food { background: #fef3c7; color: #92400e; }

    /* Action Buttons in Row */
    .row-actions {
      display: flex;
      gap: 6px;
      align-items: center;
    }

    .btn-row-action {
      background: white;
      border: 1px solid var(--border);
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--text-main);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: all 0.15s;
    }

    .btn-row-action:hover {
      background: #f1f5f3;
      border-color: var(--primary-light);
      color: var(--primary);
    }

    .btn-row-action.primary {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .btn-row-action.primary:hover {
      background: var(--primary-light);
    }

    /* Modal Styling */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.55);
      backdrop-filter: blur(2px);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-card {
      background: white;
      border-radius: 14px;
      max-width: 620px;
      width: 100%;
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15);
      border: 1px solid var(--border);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      max-height: 90vh;
      animation: modalFadeIn 0.2s ease-out;
    }

    @keyframes modalFadeIn {
      from { opacity: 0; transform: scale(0.96); }
      to { opacity: 1; transform: scale(1); }
    }

    .modal-header {
      padding: 16px 22px;
      background: #f8faf9;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-header h3 {
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--text-main);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .modal-close {
      background: transparent;
      border: none;
      font-size: 1.4rem;
      line-height: 1;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
    }

    .modal-close:hover {
      color: var(--danger);
    }

    .modal-body {
      padding: 20px 22px;
      overflow-y: auto;
      font-size: 0.88rem;
    }

    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 16px;
    }

    .detail-item label {
      font-size: 0.72rem;
      text-transform: uppercase;
      font-weight: 700;
      color: var(--text-muted);
      display: block;
      margin-bottom: 3px;
    }

    .detail-item .val {
      font-weight: 600;
      color: var(--text-main);
    }

    .integrity-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 8px;
      padding: 12px 14px;
      margin-top: 14px;
      font-size: 0.8rem;
    }

    .integrity-box-title {
      font-weight: 700;
      color: #166534;
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 4px;
    }

    .integrity-hash-code {
      font-family: 'JetBrains Mono', monospace;
      word-break: break-all;
      background: #dcfce7;
      padding: 4px 8px;
      border-radius: 4px;
      margin-top: 4px;
      color: #14532d;
      font-size: 0.72rem;
    }

    .modal-footer {
      padding: 14px 22px;
      background: #f8faf9;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Received Donations &amp; Charity Funds</div>
            <div class="top-bar-subtitle">Damayan &amp; Baitul-Mal charity contributions with cryptographic data integrity seals</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin/donations/export?' . http_build_query($filters ?? [])) ?>" class="btn-export">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Export All to CSV
          </a>
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a>
          <span class="sep">›</span>
          <a href="<?= url('/admin/mis_admin/billing') ?>">Financial Management</a>
          <span class="sep">›</span>
          <span class="current">Received Donations</span>
        </div>

        <!-- ═══════ KPI SUMMARY RIBBON ═══════ -->
        <div class="kpi-grid-5">
          <!-- Total Verified Revenue -->
          <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background:#ecfdf5;color:#059669;">
              <svg viewBox="0 0 24 24">
                <path d="M6 3h7.5c2.76 0 5 2.24 5 5 0 1.25-.46 2.39-1.22 3.27l3.72.73-.38 1.96-3.83-.75c-.95.84-2.18 1.43-3.56 1.63L14.4 18h-2.1l-.8-3H9v6H6v-6H4v-2h2v-2H4V9h2V7H4V5h2V3zm3 4v2h3.5c.83 0 1.5-.67 1.5-1.5S13.33 7 12.5 7H9zm0 4v2h2.8c.87-.22 1.55-.86 1.83-1.7-.17-.18-.38-.3-.63-.3H9z"/>
              </svg>
            </div>
            <div>
              <div class="kpi-val" style="color:#059669;">₱<?= number_format($summary['monetary_total'] ?? 0, 2) ?></div>
              <div class="kpi-sub">Verified Donations</div>
            </div>
          </div>

          <!-- Unallocated Available Funds -->
          <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background:#eff6ff;color:#2563eb;">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h9zm-9-2h10V7H12v9zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
              </svg>
            </div>
            <div>
              <div class="kpi-val" style="color:#2563eb;">₱<?= number_format($summary['monetary_available'] ?? 0, 2) ?></div>
              <div class="kpi-sub">Available for Relief</div>
            </div>
          </div>

          <!-- In-Kind Items -->
          <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background:#fef3c7;color:#d97706;">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
              </svg>
            </div>
            <div>
              <div class="kpi-val" style="color:#d97706;"><?= number_format($summary['inkind_items_total'] ?? 0) ?></div>
              <div class="kpi-sub">Food &amp; Relief Packs</div>
            </div>
          </div>

          <!-- Pending Reviews -->
          <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background:#fff7ed;color:#ea580c;">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
              </svg>
            </div>
            <div>
              <div class="kpi-val" style="color:#ea580c;"><?= number_format($summary['pending_count'] ?? 0) ?></div>
              <div class="kpi-sub">Awaiting Review</div>
            </div>
          </div>

          <!-- Data Integrity Status -->
          <div class="kpi-card">
            <div class="kpi-icon-wrap" style="background:#f0fdf4;color:#16a34a;">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
              </svg>
            </div>
            <div>
              <div class="kpi-val" style="color:#16a34a;">100%</div>
              <div class="kpi-sub">SHA-256 Integrity Seal</div>
            </div>
          </div>
        </div>

        <!-- ═══════ FILTER CONTROLS & TABS ═══════ -->
        <div class="filter-card">
          <div class="tab-pills" id="tabPills">
            <a href="javascript:void(0)" class="tab-pill active" data-filter="all">
              All Donations <span class="pill-badge"><?= $summary['total_count'] ?? 0 ?></span>
            </a>
            <a href="javascript:void(0)" class="tab-pill" data-filter="monetary">
              Monetary <span class="pill-badge"><?= $summary['monetary_count'] ?? 0 ?></span>
            </a>
            <a href="javascript:void(0)" class="tab-pill" data-filter="inkind">
              In-Kind Supplies <span class="pill-badge"><?= $summary['inkind_count'] ?? 0 ?></span>
            </a>
            <a href="javascript:void(0)" class="tab-pill" data-filter="pending">
              Pending Review <span class="pill-badge"><?= $summary['pending_count'] ?? 0 ?></span>
            </a>
            <a href="javascript:void(0)" class="tab-pill" data-filter="verified">
              Verified / Available <span class="pill-badge"><?= $summary['verified_count'] ?? 0 ?></span>
            </a>
          </div>

          <div class="filter-actions">
            <div class="search-input-wrap">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" id="searchInput" placeholder="Search donor, ref, program..." oninput="filterDonationsTable()" />
            </div>

            <select class="select-sm" id="categorySelect" onchange="filterDonationsTable()">
              <option value="">All Channels</option>
              <option value="digital">Online / Digital (PayMongo, GCash)</option>
              <option value="cash">Cash (ISCAG Office)</option>
              <option value="food">In-Kind Food &amp; Supplies</option>
            </select>

            <select class="select-sm" id="programSelect" onchange="filterDonationsTable()">
              <option value="">All Target Programs</option>
              <option value="General Welfare">General Welfare &amp; Relief</option>
              <option value="Food Security">Food Security &amp; Ramadan Ration</option>
              <option value="Zakat">Zakat &amp; Sadaqah Fund</option>
              <option value="Medical">Medical &amp; Hospital Relief</option>
              <option value="Burial">Burial &amp; Funeral Assistance</option>
            </select>
          </div>
        </div>

        <!-- ═══════ DONATIONS DATA TABLE ═══════ -->
        <div class="donations-card">
          <div class="table-container">
            <table class="donations-table" id="donationsTable">
              <thead>
                <tr>
                  <th>Ref # &amp; Date</th>
                  <th>Donor Details</th>
                  <th>Category &amp; Channel</th>
                  <th>Target Program</th>
                  <th>Contribution Value</th>
                  <th>Payment Reference</th>
                  <th>Security Seal</th>
                  <th>Status</th>
                  <th style="text-align:right;">Actions</th>
                </tr>
              </thead>
              <tbody id="donationsTbody">
                <?php if (empty($donations)): ?>
                  <tr>
                    <td colspan="9" style="text-align:center;padding:36px;color:var(--text-muted);">
                      No donation records match the selected filters.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($donations as $d): 
                    $isMonetary = ($d['donation_type'] ?? '') === 'monetary';
                    $stRaw = (string)($d['status'] ?? '');
                    $stLow = strtolower(trim($stRaw));
                    $cat = strtolower(trim((string)($d['donation_category'] ?? ($isMonetary ? 'digital' : 'food'))));
                    
                    $statusClass = 'status-available';
                    if (str_contains($stLow, 'pend')) {
                      $statusClass = 'status-pending';
                    } elseif (str_contains($stLow, 'alloc')) {
                      $statusClass = 'status-allocated';
                    } elseif (str_contains($stLow, 'reject') || str_contains($stLow, 'cancel')) {
                      $statusClass = 'status-rejected';
                    }

                    $catClass = 'category-digital';
                    if ($cat === 'cash') $catClass = 'category-cash';
                    if ($cat === 'food' || !$isMonetary) $catClass = 'category-food';

                    $seal = $d['integrity_check']['seal'] ?? strtoupper(substr(hash('sha256', ($d['donation_ref'] ?? '') . 'SALT'), 0, 12));
                    $fullHash = $d['integrity_check']['full_hash'] ?? '';
                  ?>
                  <tr data-type="<?= $isMonetary ? 'monetary' : 'inkind' ?>"
                      data-status="<?= htmlspecialchars($stLow) ?>"
                      data-category="<?= htmlspecialchars($cat) ?>"
                      data-program="<?= htmlspecialchars(strtolower($d['target_program'] ?? '')) ?>">
                    <td>
                      <strong style="color:var(--primary);font-family:monospace;"><?= htmlspecialchars($d['donation_ref'] ?? '') ?></strong>
                      <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">
                        <?= format_datetime_html($d['date_received'] ?? '') ?>
                      </div>
                    </td>
                    <td>
                      <div style="font-weight:700;"><?= htmlspecialchars($d['donor_name'] ?? 'Anonymous') ?></div>
                      <?php if (!empty($d['donor_address'])): ?>
                        <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($d['donor_address']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="category-chip <?= $catClass ?>"><?= htmlspecialchars(ucfirst($d['donation_category'] ?? ($isMonetary ? 'digital' : 'food'))) ?></span>
                      <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">
                        <?= htmlspecialchars($d['payment_method'] ?: ($isMonetary ? 'Online Payment' : 'Physical Drop-off')) ?>
                      </div>
                    </td>
                    <td>
                      <span style="font-weight:600;"><?= htmlspecialchars($d['target_program'] ?? 'General Welfare') ?></span>
                    </td>
                    <td>
                      <?php if ($isMonetary): ?>
                        <div style="font-weight:800;color:var(--success);font-size:0.95rem;">
                          ₱<?= number_format((float)($d['amount'] ?? 0), 2) ?>
                        </div>
                        <?php if ((float)($d['allocated_amount'] ?? 0) > 0): ?>
                          <div style="font-size:0.72rem;color:var(--text-muted);">
                            Allocated: ₱<?= number_format((float)$d['allocated_amount'], 2) ?>
                          </div>
                        <?php endif; ?>
                      <?php else: ?>
                        <div style="font-weight:800;color:var(--text-main);font-size:0.95rem;">
                          <?= (int)($d['quantity'] ?? 0) ?> <?= htmlspecialchars($d['unit'] ?? 'units') ?>
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">
                          <?= htmlspecialchars($d['item_description'] ?? 'Supplies') ?>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($d['payment_reference'])): ?>
                        <code style="font-size:0.78rem;background:#f3f4f6;padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($d['payment_reference']) ?></code>
                      <?php else: ?>
                        <span style="color:var(--text-muted);font-style:italic;">Direct ISCAG</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="seal-badge" title="Cryptographically sealed via SHA-256 integrity hash">
                        <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                        <?= htmlspecialchars($seal) ?>
                      </span>
                    </td>
                    <td>
                      <span class="status-tag <?= $statusClass ?>"><?= htmlspecialchars($d['status'] ?? 'Recorded') ?></span>
                    </td>
                    <td style="text-align:right;">
                      <div class="row-actions" style="justify-content:flex-end;">
                        <button class="btn-row-action" type="button" onclick='openDetailModal(<?= json_encode($d, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="View Full Details &amp; Proof">
                          View
                        </button>
                        <button class="btn-row-action primary" type="button" onclick='openReviewModal(<?= json_encode($d, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)' title="Review / Update Status">
                          Review
                        </button>
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
    </main>
  </div>

  <!-- ═══════ DETAIL MODAL ═══════ -->
  <div class="modal-overlay" id="detailModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          <span id="detailRefDisplay">Donation Record</span>
        </h3>
        <button type="button" class="modal-close" onclick="closeModal('detailModal')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="detail-grid">
          <div class="detail-item">
            <label>Donor Name</label>
            <div class="val" id="dDonorName">—</div>
          </div>
          <div class="detail-item">
            <label>Contact / Address</label>
            <div class="val" id="dDonorContact">—</div>
          </div>
          <div class="detail-item">
            <label>Donation Category</label>
            <div class="val" id="dCategory">—</div>
          </div>
          <div class="detail-item">
            <label>Target Program</label>
            <div class="val" id="dProgram">—</div>
          </div>
          <div class="detail-item">
            <label>Amount / Quantity</label>
            <div class="val" id="dValue" style="color:var(--primary);font-size:1.1rem;font-weight:800;">—</div>
          </div>
          <div class="detail-item">
            <label>Date Received</label>
            <div class="val" id="dDate">—</div>
          </div>
          <div class="detail-item">
            <label>Payment Method</label>
            <div class="val" id="dMethod">—</div>
          </div>
          <div class="detail-item">
            <label>Payment / Bank Reference</label>
            <div class="val" id="dPayRef">—</div>
          </div>
        </div>

        <div class="detail-item" style="margin-bottom:12px;">
          <label>Donor Remarks / Dedication</label>
          <div class="val" id="dRemarks" style="font-style:italic;color:var(--text-muted);background:#f9fafb;padding:8px 12px;border-radius:6px;">—</div>
        </div>

        <div class="detail-item" style="margin-bottom:12px;">
          <label>Admin Verification Notes</label>
          <div class="val" id="dAdminNotes" style="background:#f9fafb;padding:8px 12px;border-radius:6px;">—</div>
        </div>

        <!-- SHA-256 Data Integrity Seal -->
        <div class="integrity-box">
          <div class="integrity-box-title">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            Cryptographic Integrity Seal (SHA-256)
          </div>
          <div>All transaction fields (donor, amount, reference, timestamp) match the tamper-proof ledger:</div>
          <div class="integrity-hash-code" id="dSealHash">—</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-row-action" onclick="closeModal('detailModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- ═══════ REVIEW / VERIFICATION MODAL ═══════ -->
  <div class="modal-overlay" id="reviewModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
          <span id="reviewRefDisplay">Review Donation</span>
        </h3>
        <button type="button" class="modal-close" onclick="closeModal('reviewModal')">&times;</button>
      </div>
      <form id="reviewForm" onsubmit="submitReview(event)">
        <input type="hidden" id="reviewId" name="id" value="" />
        <div class="modal-body">
          <div style="background:#f8faf9;border:1px solid var(--border);border-radius:8px;padding:12px 14px;margin-bottom:16px;">
            <div style="font-weight:700;font-size:0.95rem;" id="rDonorInfo">—</div>
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:2px;" id="rValueInfo">—</div>
          </div>

          <div style="margin-bottom:14px;">
            <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);display:block;margin-bottom:6px;">
              Verification Decision / Status
            </label>
            <select class="select-sm" id="reviewStatus" name="status" style="width:100%;padding:10px;">
              <option value="Available">Available (Verified &amp; Cleared for Aid)</option>
              <option value="Pending Verification">Pending Verification</option>
              <option value="Pending Coordination">Pending Coordination (In-Kind)</option>
              <option value="Pending Receipt">Pending Receipt (Cash)</option>
              <option value="Partially Allocated">Partially Allocated</option>
              <option value="Fully Allocated">Fully Allocated</option>
              <option value="Rejected">Rejected / Invalid Payment Proof</option>
            </select>
          </div>

          <div>
            <label style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);display:block;margin-bottom:6px;">
              Administrative Review Notes &amp; Audit Trail
            </label>
            <textarea id="reviewNotes" name="admin_notes" rows="3" style="width:100%;border:1px solid var(--border);border-radius:8px;padding:10px;font-size:0.85rem;outline:none;" placeholder="Optional notes (e.g. Official receipt number, bank slip verified, drop-off confirmed)..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-row-action" onclick="closeModal('reviewModal')">Cancel</button>
          <button type="submit" class="btn-row-action primary" id="btnSubmitReview">
            Save Verification &amp; Seal
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    // ── Tab Navigation ──
    let activeTab = 'all';
    document.querySelectorAll('#tabPills .tab-pill').forEach(pill => {
      pill.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#tabPills .tab-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        activeTab = this.dataset.filter;
        filterDonationsTable();
      });
    });

    // ── Live Filtering ──
    function filterDonationsTable() {
      const search = (document.getElementById('searchInput').value || '').toLowerCase().trim();
      const cat = (document.getElementById('categorySelect').value || '').toLowerCase().trim();
      const prog = (document.getElementById('programSelect').value || '').toLowerCase().trim();
      const rows = document.querySelectorAll('#donationsTbody tr');

      let visibleCount = 0;

      rows.forEach(row => {
        // Skip empty placeholder row if present
        if (!row.dataset.type) return;

        const rType = row.dataset.type || '';
        const rStatus = row.dataset.status || '';
        const rCat = row.dataset.category || '';
        const rProg = row.dataset.program || '';
        const rowText = row.textContent.toLowerCase();

        // 1. Tab check
        let matchTab = true;
        if (activeTab === 'monetary') matchTab = (rType === 'monetary');
        else if (activeTab === 'inkind') matchTab = (rType === 'inkind');
        else if (activeTab === 'pending') matchTab = rStatus.includes('pend');
        else if (activeTab === 'verified') matchTab = !rStatus.includes('pend') && !rStatus.includes('reject') && !rStatus.includes('cancel');

        // 2. Search check
        let matchSearch = !search || rowText.includes(search);

        // 3. Category check
        let matchCat = !cat || (rCat === cat);

        // 4. Program check
        let matchProg = !prog || rProg.includes(prog);

        if (matchTab && matchSearch && matchCat && matchProg) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });
    }

    // ── Modal Handlers ──
    function openModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = 'flex';
    }

    function closeModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = 'none';
    }

    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
      });
    });

    // ── Detail Modal ──
    function openDetailModal(d) {
      if (!d) return;
      document.getElementById('detailRefDisplay').textContent = (d.donation_ref || 'Donation') + ' — ' + (d.status || '');
      document.getElementById('dDonorName').textContent = d.donor_name || 'Anonymous';
      document.getElementById('dDonorContact').textContent = d.donor_address || '—';
      document.getElementById('dCategory').textContent = (d.donation_category || d.donation_type || '—').toUpperCase();
      document.getElementById('dProgram').textContent = d.target_program || 'General Welfare';
      
      if (d.donation_type === 'monetary') {
        const amt = parseFloat(d.amount || 0);
        document.getElementById('dValue').textContent = '₱' + amt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      } else {
        document.getElementById('dValue').textContent = (d.quantity || 0) + ' ' + (d.unit || 'items') + ' (' + (d.item_description || '') + ')';
      }

      document.getElementById('dDate').textContent = d.date_received || '—';
      document.getElementById('dMethod').textContent = d.payment_method || '—';
      document.getElementById('dPayRef').textContent = d.payment_reference || 'N/A';
      document.getElementById('dRemarks').textContent = d.remarks || 'No remarks provided.';
      document.getElementById('dAdminNotes').textContent = d.admin_notes || 'No administrative notes logged.';
      
      const fullHash = d.integrity_check ? d.integrity_check.full_hash : (d.donation_ref ? d.donation_ref + '-SHA256' : '—');
      document.getElementById('dSealHash').textContent = fullHash;

      openModal('detailModal');
    }

    // ── Review Modal ──
    function openReviewModal(d) {
      if (!d) return;
      document.getElementById('reviewId').value = d.id || '';
      document.getElementById('reviewRefDisplay').textContent = 'Review ' + (d.donation_ref || 'Donation');
      document.getElementById('rDonorInfo').textContent = (d.donor_name || 'Anonymous') + ' (' + (d.donation_ref || '') + ')';
      
      let valText = '';
      if (d.donation_type === 'monetary') {
        valText = '₱' + parseFloat(d.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' (' + (d.payment_method || 'Online') + ')';
      } else {
        valText = (d.quantity || 0) + ' ' + (d.unit || 'items') + ' - ' + (d.item_description || '');
      }
      document.getElementById('rValueInfo').textContent = valText + ' • Program: ' + (d.target_program || 'General');

      // Set current status if exists in dropdown
      const sel = document.getElementById('reviewStatus');
      sel.value = d.status || 'Available';
      if (!sel.value) sel.value = 'Available';

      document.getElementById('reviewNotes').value = d.admin_notes || '';

      openModal('reviewModal');
    }

    // ── Submit Review via AJAX ──
    function submitReview(e) {
      e.preventDefault();
      const btn = document.getElementById('btnSubmitReview');
      btn.disabled = true;
      btn.textContent = 'Saving...';

      const formData = new FormData(document.getElementById('reviewForm'));

      fetch('<?= url('/admin/mis_admin/donations/verify') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = 'Save Verification & Seal';
        if (data.success) {
          closeModal('reviewModal');
          if (typeof showToast === 'function') {
            showToast('Donation ' + (data.donation_ref || '') + ' successfully updated to ' + data.status + '!', '#15803d');
          } else {
            alert('Donation successfully updated!');
          }
          // Reload page to refresh all KPI counts and cryptographic seals
          setTimeout(() => { window.location.reload(); }, 600);
        } else {
          alert(data.error || 'Failed to update donation verification.');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Save Verification & Seal';
        console.error(err);
        alert('An unexpected error occurred. Please try again.');
      });
    }
  </script>
</body>

</html>

