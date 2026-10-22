<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Operational Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --damayan-primary: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
      --damayan-accent: #c79a2b;
      --damayan-info: #0284c7;
      --damayan-danger: #dc2626;
      --damayan-warning: #f59e0b;
      --damayan-purple: #7c3aed;
      --damayan-card-bg: #ffffff;
      --damayan-border: #e2e8f0;
      --damayan-text: #0f172a;
      --damayan-muted: #64748b;
    }

    .analytics-filter-bar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      background: var(--damayan-card-bg);
      padding: 16px 20px;
      border-radius: 12px;
      border: 1px solid var(--damayan-border);
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 24px;
    }

    .filter-tabs {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 2px;
    }

    .tab-btn {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--damayan-muted);
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .tab-btn:hover {
      background: #e2e8f0;
      color: var(--damayan-text);
    }

    .tab-btn.active {
      background: var(--damayan-primary);
      color: #ffffff;
      border-color: var(--damayan-primary);
      box-shadow: 0 2px 6px rgba(23, 107, 69, 0.25);
    }

    .filter-controls {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .filter-select {
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      padding: 7px 12px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--damayan-text);
      outline: none;
      cursor: pointer;
    }

    .filter-select:focus {
      border-color: var(--damayan-primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.15);
    }

    /* Connected Metric Cards */
    .metric-card-link {
      text-decoration: none;
      color: inherit;
      display: block;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card-link:hover {
      transform: translateY(-3px);
    }

    .connected-card {
      background: var(--damayan-card-bg);
      border: 1px solid var(--damayan-border);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 100%;
    }

    .connected-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--damayan-primary);
    }

    .connected-card.accent::before { background: var(--damayan-accent); }
    .connected-card.info::before { background: var(--damayan-info); }
    .connected-card.purple::before { background: var(--damayan-purple); }
    .connected-card.warning::before { background: var(--damayan-warning); }

    .card-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }

    .card-title {
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--damayan-muted);
    }

    .card-icon-wrap {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-icon-wrap svg {
      width: 20px;
      height: 20px;
    }

    .card-value {
      font-size: 1.85rem;
      font-weight: 800;
      color: var(--damayan-text);
      line-height: 1.2;
      margin-bottom: 6px;
    }

    .card-subtext {
      font-size: 0.78rem;
      color: var(--damayan-muted);
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .card-link-hint {
      margin-top: 14px;
      padding-top: 10px;
      border-top: 1px dashed var(--damayan-border);
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--damayan-primary);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-link-hint svg {
      width: 14px;
      height: 14px;
      transition: transform 0.2s ease;
    }

    .metric-card-link:hover .card-link-hint svg {
      transform: translateX(4px);
    }

    /* Analytics Section Grid */
    .analytics-grid-2 {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 24px;
    }

    .analytics-grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 24px;
    }

    .analytics-grid-5 {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
      .analytics-grid-2, .analytics-grid-3 {
        grid-template-columns: 1fr;
      }
    }

    .chart-box {
      background: var(--damayan-card-bg);
      border: 1px solid var(--damayan-border);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      display: flex;
      flex-direction: column;
    }

    .chart-box-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
    }

    .chart-box-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--damayan-text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .chart-box-title svg {
      width: 18px;
      height: 18px;
      color: var(--damayan-primary);
    }

    .chart-box-subtitle {
      font-size: 0.75rem;
      color: var(--damayan-muted);
      margin-top: 2px;
    }

    .chart-canvas-container {
      position: relative;
      height: 280px;
      width: 100%;
    }

    /* Demographic & Distribution Bars */
    .demographic-item {
      margin-bottom: 14px;
    }

    .demographic-header {
      display: flex;
      justify-content: space-between;
      font-size: 0.8rem;
      font-weight: 600;
      margin-bottom: 4px;
      color: var(--damayan-text);
    }

    .progress-bar-bg {
      width: 100%;
      height: 8px;
      background: #f1f5f9;
      border-radius: 4px;
      overflow: hidden;
    }

    .progress-bar-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.6s ease;
    }

    /* Connected Operations Table */
    .connected-table-wrap {
      background: var(--damayan-card-bg);
      border: 1px solid var(--damayan-border);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      margin-bottom: 24px;
    }

    .table-header-panel {
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f8fafc;
      border-bottom: 1px solid var(--damayan-border);
    }

    .connected-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
    }

    .connected-table th {
      background: #ffffff;
      padding: 12px 20px;
      text-align: left;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--damayan-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 1px solid var(--damayan-border);
    }

    .connected-table td {
      padding: 14px 20px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      color: var(--damayan-text);
    }

    .connected-table tr:hover td {
      background: #f8fafc;
    }

    .badge-service {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .badge-burial { background: #dcfce7; color: #166534; }
    .badge-cert { background: #e0f2fe; color: #075985; }
    .badge-dawah { background: #f3e8ff; color: #6b21a8; }
    .badge-charity { background: #fef3c7; color: #92400e; }

    .table-action-btn {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 5px 10px;
      border-radius: 6px;
      background: #f1f5f9;
      color: var(--damayan-primary);
      text-decoration: none;
      font-size: 0.75rem;
      font-weight: 700;
      transition: all 0.2s ease;
    }

    .table-action-btn:hover {
      background: var(--damayan-primary);
      color: #ffffff;
    }

    /* Print styling */
    @media print {
      .sidebar, .top-bar-actions, .analytics-filter-bar, .table-action-btn, .breadcrumb-bar {
        display: none !important;
      }
      .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
      .chart-box, .connected-card, .connected-table-wrap { break-inside: avoid; border: 1px solid #ccc !important; box-shadow: none !important; }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'analytics';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Damayan Operational Analytics & Intelligence</div>
          <div class="top-bar-subtitle">Monitoring Islamic social welfare, funeral registry, cemetery capacity, and community relief</div>
        </div>
        <div class="top-bar-actions">
           <button class="btn-topbar" onclick="exportAnalyticsCSV()" title="Download CSV summary">
             <svg viewBox="0 0 24 24" fill="currentColor" style="width:15px;height:15px;margin-right:4px;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> Export CSV
           </button>
           <button class="btn-topbar" onclick="window.print()" title="Print full report">
             <svg viewBox="0 0 24 24" fill="currentColor" style="width:15px;height:15px;margin-right:4px;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-4 11H9v-5h6v5zm4-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Summary
           </button>
           <a href="<?= url('/admin/damayan') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-primary);">
             <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
           </a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Damayan Social Welfare</a>
          <span class="sep">›</span>
          <span class="current">Operational Analytics</span>
        </div>

        <!-- Filter & Navigation Bar -->
        <div class="analytics-filter-bar">
          <div class="filter-tabs" id="domainTabs">
            <button class="tab-btn active" data-tab="all">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
              All Overview
            </button>
            <button class="tab-btn" data-tab="civil-registry">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
              Civil Registry & Janazah
            </button>
            <button class="tab-btn" data-tab="cemetery">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
              Cemetery & Plots
            </button>
            <button class="tab-btn" data-tab="charity">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Charity & Baitul-Mal
            </button>
            <button class="tab-btn" data-tab="demographics">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Deceased Demographics
            </button>
          </div>
          <div class="filter-controls">
            <label for="yearFilter" style="font-size:0.8rem;font-weight:700;color:var(--damayan-muted);">Analysis Year:</label>
            <select id="yearFilter" class="filter-select" onchange="window.location.href='<?= url('/admin/damayan/analytics') ?>?year='+this.value">
              <?php foreach ($availableYears as $yr): ?>
                <option value="<?= $yr ?>" <?= ($selectedYear == $yr) ? 'selected' : '' ?>><?= $yr ?> Annual</option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- 5 Connected KPI Cards -->
        <div class="analytics-grid-5">
          <!-- Card 1: Burial & Funeral Requests -->
          <a href="<?= url('/admin/damayan/burial-requests') ?>" class="metric-card-link" title="Open Funeral & Burial Requests module">
            <div class="connected-card">
              <div class="card-top">
                <span class="card-title">Burial Requests</span>
                <div class="card-icon-wrap" style="color:var(--damayan-primary);background:#dcfce7;">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                </div>
              </div>
              <div class="card-value"><?= number_format($burial['total'] ?? 0) ?></div>
              <div class="card-subtext">
                <span style="color:#166534;font-weight:700;"><?= $burial['accepted'] ?? 0 ?> Accepted</span>
                <span>•</span>
                <span style="color:#d97706;font-weight:700;"><?= $burial['pending'] ?? 0 ?> Pending</span>
              </div>
              <div class="card-link-hint">
                <span>View Burial Requests</span>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </div>
            </div>
          </a>

          <!-- Card 2: Civil Death Certificates -->
          <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="metric-card-link" title="Open Encoded Death Certificates Registry">
            <div class="connected-card info">
              <div class="card-top">
                <span class="card-title">Encoded Registry</span>
                <div class="card-icon-wrap" style="color:var(--damayan-info);background:#e0f2fe;">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                </div>
              </div>
              <div class="card-value"><?= number_format($burial['death_cert_registered'] ?? 0) ?></div>
              <div class="card-subtext">
                <span style="color:#0369a1;font-weight:700;"><?= $burial['encoded_total'] ?? 0 ?> Encoded Docs</span>
                <span>•</span>
                <span>Civil Registered</span>
              </div>
              <div class="card-link-hint" style="color:var(--damayan-info);">
                <span>Manage Civil Registry</span>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </div>
            </div>
          </a>

          <!-- Card 3: Male Da'wah Transmittals -->
          <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="metric-card-link" title="Open Male Da'wah Transmittal Registry">
            <div class="connected-card purple">
              <div class="card-top">
                <span class="card-title">Da'wah Transmittals</span>
                <div class="card-icon-wrap" style="color:var(--damayan-purple);background:#f3e8ff;">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </div>
              </div>
              <div class="card-value"><?= number_format($burial['dawah_transmitted_total'] ?? 0) ?></div>
              <div class="card-subtext">
                <span style="color:#7e22ce;font-weight:700;"><?= $burial['dawah_reviewed'] ?? 0 ?> Reviewed</span>
                <span>•</span>
                <span style="color:#166534;font-weight:700;"><?= $burial['dawah_completed'] ?? 0 ?> Completed</span>
              </div>
              <div class="card-link-hint" style="color:var(--damayan-purple);">
                <span>View Da'wah Records</span>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </div>
            </div>
          </a>

          <!-- Card 4: Cemetery Capacity -->
          <a href="<?= url('/admin/damayan/burial') ?>" class="metric-card-link" title="Open Cemetery Plots Manager">
            <div class="connected-card warning">
              <div class="card-top">
                <span class="card-title">Cemetery Plots</span>
                <div class="card-icon-wrap" style="color:var(--damayan-warning);background:#fef3c7;">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
              </div>
              <div class="card-value"><?= $burial['available_plots'] ?? 0 ?> <span style="font-size:0.95rem;color:var(--damayan-muted);font-weight:500;">/ <?= $burial['total_plots'] ?? 0 ?> Avail</span></div>
              <div class="card-subtext">
                <span style="color:#166534;font-weight:700;"><?= $burial['occupancy_rate'] ?? 0 ?>% Occupied</span>
                <span>•</span>
                <span><?= $burial['occupied_plots'] ?? 0 ?> In Use</span>
              </div>
              <div class="card-link-hint" style="color:var(--damayan-warning);">
                <span>Cemetery Inventory</span>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </div>
            </div>
          </a>

          <!-- Card 5: Baitul-Mal Charity Inflow -->
          <a href="<?= url('/admin/damayan/charity/donations') ?>" class="metric-card-link" title="Open Charity Donations module">
            <div class="connected-card accent">
              <div class="card-top">
                <span class="card-title">Baitul-Mal Charity</span>
                <div class="card-icon-wrap" style="color:var(--damayan-accent);background:#fefce8;">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
              </div>
              <div class="card-value">₱<?= number_format($charity['total_amount'] ?? 0) ?></div>
              <div class="card-subtext">
                <span style="color:#166534;font-weight:700;">₱<?= number_format($charity['distributed_amount'] ?? 0) ?> Disbursed</span>
                <span>•</span>
                <span><?= $charity['total_count'] ?? 0 ?> Donors</span>
              </div>
              <div class="card-link-hint" style="color:var(--damayan-accent);">
                <span>Manage Donations</span>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
              </div>
            </div>
          </a>
        </div>

        <!-- Section: Monthly Demand & Financial Flow (Row 1) -->
        <div class="analytics-grid-2 domain-section" data-section="all,civil-registry,charity">
          <!-- Chart 1: Monthly Deaths Recorded -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                  Monthly Death Registration & Burial Inflow (<?= $selectedYear ?>)
                </div>
                <div class="chart-box-subtitle">Monthly frequency of confirmed deceased records and burial service applications</div>
              </div>
              <a href="<?= url('/admin/damayan/burial-requests') ?>" class="table-action-btn">View Records ›</a>
            </div>
            <div class="chart-canvas-container">
              <canvas id="burialMonthlyChart"></canvas>
            </div>
          </div>

          <!-- Chart 2: Monthly Charity Inflow -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/></svg>
                  Monthly Baitul-Mal Donation Inflow (<?= $selectedYear ?>)
                </div>
                <div class="chart-box-subtitle">Total monetary collections received per month in Philippine Pesos (₱)</div>
              </div>
              <a href="<?= url('/admin/damayan/charity/donations') ?>" class="table-action-btn">Donation Log ›</a>
            </div>
            <div class="chart-canvas-container">
              <canvas id="donationInflowChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Section: Cemetery Inventory & Service Breakdown (Row 2) -->
        <div class="analytics-grid-3 domain-section" data-section="all,cemetery,civil-registry">
          <!-- Chart 3: Cemetery Plot Occupancy -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  Cemetery Capacity & Plots
                </div>
                <div class="chart-box-subtitle"><?= $burial['total_plots'] ?? 0 ?> Total Assigned Plots</div>
              </div>
              <a href="<?= url('/admin/damayan/burial') ?>" class="table-action-btn">Plots ›</a>
            </div>
            <div class="chart-canvas-container" style="height:220px;">
              <canvas id="cemeteryPlotChart"></canvas>
            </div>
            <div style="margin-top:14px;font-size:0.75rem;display:flex;justify-content:space-around;color:var(--damayan-muted);">
              <div><b style="color:#166534;font-size:0.95rem;"><?= $burial['available_plots'] ?? 0 ?></b> Available</div>
              <div><b style="color:#dc2626;font-size:0.95rem;"><?= $burial['occupied_plots'] ?? 0 ?></b> Occupied</div>
              <div><b style="color:#d97706;font-size:0.95rem;"><?= $burial['reserved_plots'] ?? 0 ?></b> Reserved</div>
            </div>
          </div>

          <!-- Chart 4: Service Request Workflow Status -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Burial Requests Pipeline
                </div>
                <div class="chart-box-subtitle">Processing status of funeral & burial requests</div>
              </div>
              <a href="<?= url('/admin/damayan/burial-requests') ?>" class="table-action-btn">Review ›</a>
            </div>
            <div class="chart-canvas-container" style="height:220px;">
              <canvas id="requestStatusChart"></canvas>
            </div>
            <div style="margin-top:14px;font-size:0.75rem;display:flex;justify-content:space-around;color:var(--damayan-muted);">
              <div><b style="color:#166534;font-size:0.95rem;"><?= $burial['accepted'] ?? 0 ?></b> Approved</div>
              <div><b style="color:#d97706;font-size:0.95rem;"><?= $burial['pending'] ?? 0 ?></b> Pending</div>
              <div><b style="color:#dc2626;font-size:0.95rem;"><?= $burial['rejected'] ?? 0 ?></b> Rejected</div>
            </div>
          </div>

          <!-- Chart 5: Da'wah Transmittal Progress -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                  Da'wah Male Transmittals
                </div>
                <div class="chart-box-subtitle">Official coordination with Male Da'wah division</div>
              </div>
              <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="table-action-btn">Transmittals ›</a>
            </div>
            <div class="chart-canvas-container" style="height:220px;">
              <canvas id="dawahTransmittalChart"></canvas>
            </div>
            <div style="margin-top:14px;font-size:0.75rem;display:flex;justify-content:space-around;color:var(--damayan-muted);">
              <div><b style="color:#0284c7;font-size:0.95rem;"><?= $burial['dawah_transmitted_total'] ?? 0 ?></b> Transmitted</div>
              <div><b style="color:#7c3aed;font-size:0.95rem;"><?= $burial['dawah_reviewed'] ?? 0 ?></b> Reviewed</div>
              <div><b style="color:#166534;font-size:0.95rem;"><?= $burial['dawah_completed'] ?? 0 ?></b> Completed</div>
            </div>
          </div>
        </div>

        <!-- Section: Charity Distribution & Financial Breakdown (Row 3) -->
        <div class="analytics-grid-2 domain-section" data-section="all,charity">
          <!-- Chart 6: Charity Target Programs Breakdown -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11V3H8v6H2v12h20V11h-6zm-6-6h4v14h-4V5zm-6 8h4v6H4v-6zm16 6h-4v-6h4v6z"/></svg>
                  Donation Allocation by Target Program
                </div>
                <div class="chart-box-subtitle">Distribution of community tabarru' across dedicated welfare initiatives</div>
              </div>
              <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="table-action-btn">Allocations ›</a>
            </div>
            <div class="chart-canvas-container">
              <canvas id="charityProgramsChart"></canvas>
            </div>
          </div>

          <!-- Chart 7: Payment Methods Breakdown -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                  Donation Collection Channels & Methods
                </div>
                <div class="chart-box-subtitle">Payment methods utilized by contributors (Bank, GCash, Cash, Check)</div>
              </div>
              <a href="<?= url('/admin/damayan/charity/donations') ?>" class="table-action-btn">Verify Pay ›</a>
            </div>
            <div class="chart-canvas-container">
              <canvas id="paymentMethodsChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Section: Demographics & Public Health Intelligence (Row 4) -->
        <div class="analytics-grid-2 domain-section" data-section="all,demographics,civil-registry">
          <!-- Chart 8: Age Group Breakdown -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/></svg>
                  Deceased Age Demographics
                </div>
                <div class="chart-box-subtitle">Mortality distribution classified by life stage and age categories</div>
              </div>
              <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="table-action-btn">Encoded Docs ›</a>
            </div>
            <div class="chart-canvas-container">
              <canvas id="ageDistributionChart"></canvas>
            </div>
          </div>

          <!-- Demographics & Place of Death Visualizer -->
          <div class="chart-box">
            <div class="chart-box-header">
              <div>
                <div class="chart-box-title">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                  Gender & Place of Occurrence
                </div>
                <div class="chart-box-subtitle">Epidemiological indicators for community health assessment</div>
              </div>
            </div>

            <!-- Gender split visual -->
            <div style="margin-top:10px;margin-bottom:20px;">
              <div style="font-size:0.8rem;font-weight:700;color:var(--damayan-muted);text-transform:uppercase;margin-bottom:10px;">Gender Distribution</div>
              <div class="demographic-item">
                <div class="demographic-header">
                  <span>Male Deceased: <b><?= $burial['demographics']['male'] ?? 0 ?></b></span>
                  <span><?= $burial['demographics']['male_pct'] ?? 50 ?>%</span>
                </div>
                <div class="progress-bar-bg">
                  <div class="progress-bar-fill" style="width:<?= $burial['demographics']['male_pct'] ?? 50 ?>%;background:#0284c7;"></div>
                </div>
              </div>
              <div class="demographic-item">
                <div class="demographic-header">
                  <span>Female Deceased: <b><?= $burial['demographics']['female'] ?? 0 ?></b></span>
                  <span><?= $burial['demographics']['female_pct'] ?? 50 ?>%</span>
                </div>
                <div class="progress-bar-bg">
                  <div class="progress-bar-fill" style="width:<?= $burial['demographics']['female_pct'] ?? 50 ?>%;background:#ec4899;"></div>
                </div>
              </div>
            </div>

            <!-- Place of Death visual -->
            <div style="margin-top:10px;">
              <div style="font-size:0.8rem;font-weight:700;color:var(--damayan-muted);text-transform:uppercase;margin-bottom:10px;">Place of Death Occurrence</div>
              <?php
                $totPlace = max(1, ($burial['demographics']['place_hospital'] ?? 0) + ($burial['demographics']['place_residence'] ?? 0));
                $hospPct = round((($burial['demographics']['place_hospital'] ?? 0) / $totPlace) * 100);
                $resPct = round((($burial['demographics']['place_residence'] ?? 0) / $totPlace) * 100);
              ?>
              <div class="demographic-item">
                <div class="demographic-header">
                  <span>Hospital / Health Facility: <b><?= $burial['demographics']['place_hospital'] ?? 0 ?></b></span>
                  <span><?= $hospPct ?>%</span>
                </div>
                <div class="progress-bar-bg">
                  <div class="progress-bar-fill" style="width:<?= $hospPct ?>%;background:#10b981;"></div>
                </div>
              </div>
              <div class="demographic-item">
                <div class="demographic-header">
                  <span>Home / Residential Address: <b><?= $burial['demographics']['place_residence'] ?? 0 ?></b></span>
                  <span><?= $resPct ?>%</span>
                </div>
                <div class="progress-bar-bg">
                  <div class="progress-bar-fill" style="width:<?= $resPct ?>%;background:#f59e0b;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Connected Operations Activity Log -->
        <div class="connected-table-wrap domain-section" data-section="all,civil-registry,charity,cemetery">
          <div class="table-header-panel">
            <div>
              <div style="font-size:0.95rem;font-weight:700;color:var(--damayan-text);">Connected Operational Records & Live Stream</div>
              <div style="font-size:0.75rem;color:var(--damayan-muted);">Real-time operations directly linked to their corresponding administrative modules</div>
            </div>
            <span class="badge-status badge-approved" style="font-size:0.75rem;">100% Connected</span>
          </div>
          <table class="connected-table">
            <thead>
              <tr>
                <th>Service Domain</th>
                <th>Reference No.</th>
                <th>Subject / Particulars</th>
                <th>Status</th>
                <th>Activity Date</th>
                <th style="text-align:right;">Direct Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recentRecords)): ?>
                <?php foreach ($recentRecords as $rec): ?>
                  <tr>
                    <td>
                      <?php if ($rec['source'] === 'burial_request'): ?>
                        <span class="badge-service badge-burial">
                          <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                          Burial Request
                        </span>
                      <?php elseif ($rec['source'] === 'death_cert'): ?>
                        <span class="badge-service badge-cert">
                          <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                          Civil Registry
                        </span>
                      <?php elseif ($rec['source'] === 'dawah_transmittal'): ?>
                        <span class="badge-service badge-dawah">
                          <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                          Da'wah Transmittal
                        </span>
                      <?php else: ?>
                        <span class="badge-service badge-charity">
                          <svg viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                          Baitul-Mal Charity
                        </span>
                      <?php endif; ?>
                    </td>
                    <td><code style="font-weight:700;color:var(--damayan-text);"><?= htmlspecialchars($rec['reference_no'] ?? 'N/A') ?></code></td>
                    <td><b><?= htmlspecialchars($rec['subject'] ?? 'N/A') ?></b></td>
                    <td>
                      <span class="badge-status badge-active" style="font-size:0.75rem;">
                        <?= htmlspecialchars($rec['record_status'] ?? 'Active') ?>
                      </span>
                    </td>
                    <td><?= !empty($rec['activity_date']) ? date('M d, Y h:i A', strtotime($rec['activity_date'])) : '—' ?></td>
                    <td style="text-align:right;">
                      <a href="<?= url($rec['target_url'] ?? '/admin/damayan') ?>" class="table-action-btn">
                        Open File ›
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" style="text-align:center;padding:30px;color:var(--damayan-muted);">No operational activities recorded for this period.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    // Tab Filter Functionality
    document.querySelectorAll('#domainTabs .tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('#domainTabs .tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const targetTab = this.getAttribute('data-tab');
        const sections = document.querySelectorAll('.domain-section');

        sections.forEach(sec => {
          const allowed = sec.getAttribute('data-section').split(',');
          if (targetTab === 'all' || allowed.includes(targetTab)) {
            sec.style.display = '';
          } else {
            sec.style.display = 'none';
          }
        });
      });
    });

    // CSV Exporter
    function exportAnalyticsCSV() {
      const year = '<?= $selectedYear ?>';
      const rows = [
        ["ISCAG MIS - Damayan Department Operational Analytics Report (" + year + ")"],
        ["Generated At", new Date().toLocaleString()],
        [],
        ["Metric", "Value"],
        ["Total Deaths & Burial Cases", "<?= $burial['total'] ?? 0 ?>"],
        ["Burial Requests Accepted", "<?= $burial['accepted'] ?? 0 ?>"],
        ["Burial Requests Pending", "<?= $burial['pending'] ?? 0 ?>"],
        ["Civil Death Certificates Encoded", "<?= $burial['encoded_total'] ?? 0 ?>"],
        ["Total Registered Certificates", "<?= $burial['death_cert_registered'] ?? 0 ?>"],
        ["Da'wah Transmittals Total", "<?= $burial['dawah_transmitted_total'] ?? 0 ?>"],
        ["Da'wah Transmittals Reviewed", "<?= $burial['dawah_reviewed'] ?? 0 ?>"],
        ["Da'wah Transmittals Completed", "<?= $burial['dawah_completed'] ?? 0 ?>"],
        ["Total Cemetery Plots", "<?= $burial['total_plots'] ?? 0 ?>"],
        ["Available Cemetery Plots", "<?= $burial['available_plots'] ?? 0 ?>"],
        ["Occupied Cemetery Plots", "<?= $burial['occupied_plots'] ?? 0 ?>"],
        ["Cemetery Occupancy Rate (%)", "<?= $burial['occupancy_rate'] ?? 0 ?>%"],
        ["Baitul-Mal Charity Total Collections (PHP)", "<?= $charity['total_amount'] ?? 0 ?>"],
        ["Charity Total Allocated (PHP)", "<?= $charity['allocated_amount'] ?? 0 ?>"],
        ["Charity Total Disbursed (PHP)", "<?= $charity['distributed_amount'] ?? 0 ?>"],
        ["Charity Available Funds (PHP)", "<?= $charity['available_amount'] ?? 0 ?>"],
        ["Unique Donors Count", "<?= $charity['unique_donors'] ?? 0 ?>"],
        [],
        ["Month", "Deaths Recorded", "Charity Collections (PHP)"],
        <?php 
          $mLabels = $burial['monthly_labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          $mBurial = $burial['monthly_data'] ?? array_fill(0,12,0);
          $mCharity = $charity['monthly_data'] ?? array_fill(0,12,0);
          for ($i = 0; $i < 12; $i++): 
        ?>
        ["<?= $mLabels[$i] ?>", "<?= $mBurial[$i] ?>", "<?= $mCharity[$i] ?>"],
        <?php endfor; ?>
      ];

      let csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => '"' + (''+cell).replace(/"/g, '""') + '"').join(",")).join("\n");
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "damayan_analytics_" + year + ".csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Chart.js Visualizations
    document.addEventListener("DOMContentLoaded", function() {
      const palette = {
        primary: '#176b45',
        primaryAlpha: 'rgba(23, 107, 69, 0.85)',
        gold: '#c79a2b',
        goldAlpha: 'rgba(199, 154, 43, 0.85)',
        info: '#0284c7',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#dc2626',
        purple: '#7c3aed',
        pink: '#ec4899',
        slate: '#64748b'
      };

      const monthlyLabels = <?= json_encode($burial['monthly_labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) ?>;

      // 1. Monthly Burial Inflow
      new Chart(document.getElementById('burialMonthlyChart'), {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Deaths & Funerals',
            data: <?= json_encode($burial['monthly_data'] ?? []) ?>,
            backgroundColor: palette.primaryAlpha,
            borderColor: palette.primary,
            borderWidth: 1.5,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => ` ${ctx.raw} deaths / burial records`
              }
            }
          },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
          }
        }
      });

      // 2. Monthly Charity Inflow
      new Chart(document.getElementById('donationInflowChart'), {
        type: 'line',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Donation Volume (₱)',
            data: <?= json_encode($charity['monthly_data'] ?? []) ?>,
            borderColor: palette.gold,
            backgroundColor: 'rgba(199, 154, 43, 0.12)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: palette.gold,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => ` ₱${Number(ctx.raw).toLocaleString()}`
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: v => '₱' + Number(v).toLocaleString()
              }
            },
            x: { grid: { display: false } }
          }
        }
      });

      // 3. Cemetery Plots Capacity Doughnut
      new Chart(document.getElementById('cemeteryPlotChart'), {
        type: 'doughnut',
        data: {
          labels: ['Available', 'Occupied', 'Reserved'],
          datasets: [{
            data: [
              <?= (int)($burial['available_plots'] ?? 0) ?>,
              <?= (int)($burial['occupied_plots'] ?? 0) ?>,
              <?= (int)($burial['reserved_plots'] ?? 0) ?>
            ],
            backgroundColor: [palette.success, palette.danger, palette.warning],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // 4. Request Status Pipeline
      new Chart(document.getElementById('requestStatusChart'), {
        type: 'doughnut',
        data: {
          labels: ['Accepted', 'Pending', 'Rejected'],
          datasets: [{
            data: [
              <?= (int)($burial['accepted'] ?? 0) ?>,
              <?= (int)($burial['pending'] ?? 0) ?>,
              <?= (int)($burial['rejected'] ?? 0) ?>
            ],
            backgroundColor: [palette.primary, palette.warning, palette.danger],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // 5. Da'wah Transmittals
      new Chart(document.getElementById('dawahTransmittalChart'), {
        type: 'doughnut',
        data: {
          labels: ['Transmitted', 'Reviewed', 'Completed'],
          datasets: [{
            data: [
              <?= (int)($burial['dawah_transmitted_total'] ?? 0) ?>,
              <?= (int)($burial['dawah_reviewed'] ?? 0) ?>,
              <?= (int)($burial['dawah_completed'] ?? 0) ?>
            ],
            backgroundColor: [palette.info, palette.purple, palette.success],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: { position: 'bottom' }
          }
        }
      });

      // 6. Charity Programs Breakdown (Horizontal Bar)
      <?php
        $progLabels = [];
        $progData = [];
        foreach (($charity['programs'] ?? []) as $p) {
          $progLabels[] = $p['prog'];
          $progData[] = (float)$p['amount'];
        }
        if (empty($progLabels)) {
          $progLabels = ['General Welfare', 'Orphan Care', 'Food Relief', 'Medical Aid', 'Education'];
          $progData = [0, 0, 0, 0, 0];
        }
      ?>
      new Chart(document.getElementById('charityProgramsChart'), {
        type: 'bar',
        data: {
          labels: <?= json_encode($progLabels) ?>,
          datasets: [{
            label: 'Total Collected (₱)',
            data: <?= json_encode($progData) ?>,
            backgroundColor: [
              '#176b45', '#0284c7', '#c79a2b', '#7c3aed', '#10b981', '#f59e0b'
            ],
            borderRadius: 6
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => ` ₱${Number(ctx.raw).toLocaleString()}`
              }
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              ticks: { callback: v => '₱' + Number(v).toLocaleString() }
            }
          }
        }
      });

      // 7. Payment Methods Breakdown
      <?php
        $payLabels = [];
        $payData = [];
        foreach (($charity['payment_methods'] ?? []) as $pm) {
          $payLabels[] = $pm['method'];
          $payData[] = (int)$pm['count'];
        }
        if (empty($payLabels)) {
          $payLabels = ['GCash', 'Cash', 'Bank Transfer', 'Check'];
          $payData = [0, 0, 0, 0];
        }
      ?>
      new Chart(document.getElementById('paymentMethodsChart'), {
        type: 'polarArea',
        data: {
          labels: <?= json_encode($payLabels) ?>,
          datasets: [{
            data: <?= json_encode($payData) ?>,
            backgroundColor: [
              'rgba(2, 132, 199, 0.75)',
              'rgba(23, 107, 69, 0.75)',
              'rgba(199, 154, 43, 0.75)',
              'rgba(124, 58, 237, 0.75)'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right' }
          }
        }
      });

      // 8. Age Distribution Chart
      new Chart(document.getElementById('ageDistributionChart'), {
        type: 'bar',
        data: {
          labels: ['Infants (<1 yr)', 'Youth (1-17 yrs)', 'Adults (18-59 yrs)', 'Seniors (60+ yrs)'],
          datasets: [{
            label: 'Recorded Cases',
            data: [
              <?= (int)($burial['demographics']['age_infant'] ?? 0) ?>,
              <?= (int)($burial['demographics']['age_youth'] ?? 0) ?>,
              <?= (int)($burial['demographics']['age_adult'] ?? 0) ?>,
              <?= (int)($burial['demographics']['age_senior'] ?? 0) ?>
            ],
            backgroundColor: [
              palette.info,
              palette.warning,
              palette.primary,
              palette.purple
            ],
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
          }
        }
      });

    });
  </script>
</body>
</html>
