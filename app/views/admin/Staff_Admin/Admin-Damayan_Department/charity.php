<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Charity Dashboard</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary: #176b45;
      --primary-dark: #0f5c3a;
      --accent: #c79a2b;
      --accent-light: #e0b84a;
      --border: #d9e3de;
      --content-bg: #f4f6f5;
    }

    /* ── Apartment Admin Insight Card Styling ── */
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 18px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: #ffffff;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: relative;
      overflow: hidden;
    }
    .insight-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.07);
      border-color: var(--accent);
    }
    .insight-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .insight-card.total::after { background: var(--primary); }
    .insight-card.available::after { background: var(--success); }
    .insight-card.allocated::after { background: var(--accent); }
    .insight-card.distributed::after { background: #1f6f5a; }
    .insight-card.pending::after { background: #8b2e2e; }

    .insight-label {
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .insight-value {
      font-size: 1.75rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.1;
    }
    .insight-sub {
      font-size: 0.8rem;
      color: var(--text-muted);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Quick Action Cards */
    .quick-nav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }
    .quick-nav-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 18px 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      text-decoration: none;
      color: var(--text-main);
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .quick-nav-card:hover {
      border-color: var(--accent);
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.06);
    }
    .quick-nav-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .quick-nav-title {
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--primary-dark);
      margin-bottom: 2px;
    }
    .quick-nav-desc {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    /* Status Badges */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .badge-available { background: rgba(47, 138, 96, 0.12); color: #176b45; }
    .badge-allocated { background: rgba(199, 154, 43, 0.12); color: #a37817; }
    .badge-distributed { background: rgba(31, 111, 90, 0.12); color: #1f6f5a; }
    .badge-pending { background: rgba(139, 46, 46, 0.12); color: #8b2e2e; }
    .badge-cancelled { background: rgba(111, 127, 120, 0.12); color: #6f7f78; }

    .dept-tag {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
    }
    .dept-damayan { background: #e8f5ed; color: #0f5c3a; }
    .dept-dawah { background: #fdf5e6; color: #a37817; }

    .btn-action-icon {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: white;
      color: var(--text-main);
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .btn-action-icon:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(199, 154, 43, 0.05);
    }

    /* Modal Backdrop */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
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
      max-width: 640px;
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
      padding: 20px 24px;
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
    .modal-body {
      padding: 24px;
    }
    .modal-footer {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      background: #fafbfb;
    }
  </style>
  <link rel="stylesheet" href="<?= asset('css/charity.css') ?>?v=<?= time() ?>" />
</head>
<body class="charity-module">
  <div class="app-wrapper">
    <!-- Damayan Sidebar -->
    <?php 
      $active_page = 'charity';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar matching Apartment Admin -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Charity Programs & Aid Distribution</div>
          <div class="top-bar-subtitle">Monitor available donations, allocate resources to Damayan & Da'wah, and track distributions</div>
        </div>
        <div class="top-bar-actions" style="display:flex; gap:10px;">
          <a href="<?= url('/admin/damayan/charity/donations') ?>" class="btn-topbar" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:var(--primary); color:white; border-radius:8px; padding:8px 16px; font-weight:700; font-size:0.85rem;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Record Donation
          </a>
          <a href="<?= url('/admin/damayan/charity/reports') ?>" class="btn-topbar" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:white; color:var(--text-main); border:1px solid var(--border); border-radius:8px; padding:8px 16px; font-weight:700; font-size:0.85rem;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Reports Summary
          </a>
        </div>
      </div>

      <div class="page-body">
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a>
          <span class="sep">›</span>
          <span class="current">Charity Dashboard</span>
        </div>

        <?php
          $__received    = (float) ($stats['received']['monetary'] ?? 0);
          $__distributed = (float) ($stats['distributed']['monetary'] ?? 0);
          $__utilPct     = $__received > 0 ? min(100, round(($__distributed / $__received) * 100)) : 0;
        ?>
        <!-- Hero Banner -->
        <section class="ch-hero">
          <div class="ch-hero-text">
            <span class="ch-hero-eyebrow">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Damayan Charity Programs
            </span>
            <h1>Charity Dashboard</h1>
            <p>Track every peso and item from the moment it is received, through department allocation, all the way to the hands of beneficiaries.</p>
          </div>
          <div class="ch-hero-stat">
            <div class="ch-hero-stat-label">Funds Distributed</div>
            <div class="ch-hero-stat-value"><?= $__utilPct ?>%</div>
            <div class="ch-hero-progress"><span style="width:<?= $__utilPct ?>%;"></span></div>
            <div class="ch-hero-stat-sub">₱<?= number_format($__distributed, 2) ?> of ₱<?= number_format($__received, 2) ?> received</div>
          </div>
        </section>

        <!-- 5 Summary Cards (Accurate DB totals) -->
        <div class="admin-insights">
          <!-- 1. Total Received -->
          <div class="insight-card total">
            <div class="insight-head">
              <div class="insight-label">Total Donations Received</div>
              <span class="insight-icon"><svg viewBox="0 0 24 24"><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 12 7.4l3.38 4.6L17 10.83 14.92 8H20v6z"/></svg></span>
            </div>
            <div class="insight-value">₱<?= number_format($stats['received']['monetary'], 2) ?></div>
            <div class="insight-sub">
              <span><?= number_format($stats['received']['in_kind']) ?> physical item(s)</span>
              <span>•</span>
              <span><?= $stats['received']['count'] ?> record(s)</span>
            </div>
          </div>

          <!-- 2. Total Available -->
          <div class="insight-card available">
            <div class="insight-head">
              <div class="insight-label">Total Available Donations</div>
              <span class="insight-icon"><svg viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg></span>
            </div>
            <div class="insight-value" style="color:var(--success);">₱<?= number_format($stats['available']['monetary'], 2) ?></div>
            <div class="insight-sub">
              <span><?= number_format($stats['available']['in_kind']) ?> available item(s)</span>
              <span>•</span>
              <span>Ready for Allocation</span>
            </div>
          </div>

          <!-- 3. Total Allocated -->
          <div class="insight-card allocated">
            <div class="insight-head">
              <div class="insight-label">Total Donations Allocated</div>
              <span class="insight-icon"><svg viewBox="0 0 24 24"><path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z"/></svg></span>
            </div>
            <div class="insight-value" style="color:var(--accent);">₱<?= number_format($stats['allocated']['monetary'], 2) ?></div>
            <div class="insight-sub">
              <span>Damayan: ₱<?= number_format($stats['allocated']['damayan_monetary']) ?></span>
              <span>•</span>
              <span>Da'wah: ₱<?= number_format($stats['allocated']['dawah_monetary']) ?></span>
            </div>
          </div>

          <!-- 4. Total Distributed -->
          <div class="insight-card distributed">
            <div class="insight-head">
              <div class="insight-label">Total Donations Distributed</div>
              <span class="insight-icon"><svg viewBox="0 0 24 24"><path d="M16.48 10.41c-.39.39-1.04.39-1.43 0l-4.47-4.46-7.05 7.04-.66-.63c-1.17-1.17-1.17-3.07 0-4.24l4.24-4.24c1.17-1.17 3.07-1.17 4.24 0L16.48 9c.39.39.39 1.02 0 1.41zm.7-2.12c.78.78.78 2.05 0 2.83-1.27 1.27-2.61.22-2.83 0l-3.76-3.76-5.57 5.57a.996.996 0 0 0 0 1.41c.39.39 1.02.39 1.42 0l4.62-4.62.71.71-4.62 4.62a.996.996 0 0 0 0 1.41c.39.39 1.02.39 1.42 0l4.62-4.62.71.71-4.62 4.62a.996.996 0 1 0 1.41 1.41l4.62-4.62.71.71-4.62 4.62a.996.996 0 1 0 1.41 1.41l8.32-8.34c1.17-1.17 1.17-3.07 0-4.24l-4.24-4.24c-1.15-1.15-3.01-1.17-4.18-.06l4.47 4.47z"/></svg></span>
            </div>
            <div class="insight-value" style="color:#1f6f5a;">₱<?= number_format($stats['distributed']['monetary'], 2) ?></div>
            <div class="insight-sub">
              <span><?= number_format($stats['distributed']['in_kind']) ?> distributed item(s)</span>
              <span>•</span>
              <span><?= $stats['distributed']['count'] ?> distribution(s)</span>
            </div>
          </div>

          <!-- 5. Pending Distributions -->
          <div class="insight-card pending">
            <div class="insight-head">
              <div class="insight-label">Pending Distributions</div>
              <span class="insight-icon"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></span>
            </div>
            <div class="insight-value" style="color:#8b2e2e;">₱<?= number_format($stats['pending']['monetary'], 2) ?></div>
            <div class="insight-sub">
              <span><?= number_format($stats['pending']['in_kind']) ?> pending item(s)</span>
              <span>•</span>
              <span><?= $stats['pending']['count'] ?> batch(es) awaiting</span>
            </div>
          </div>
        </div>

        <!-- Quick Access to the 4 sections -->
        <div class="quick-nav-grid">
          <a href="<?= url('/admin/damayan/charity/donations') ?>" class="quick-nav-card">
            <div class="quick-nav-icon" style="background:#e8f5ed; color:#0f5c3a;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
            </div>
            <div>
              <div class="quick-nav-title">Donation Records</div>
              <div class="quick-nav-desc">Record monetary & in-kind donations and issue acknowledgments</div>
              <div class="quick-nav-go">Open ›</div>
            </div>
          </a>

          <a href="<?= url('/admin/damayan/charity/available') ?>" class="quick-nav-card">
            <div class="quick-nav-icon" style="background:#fdf5e6; color:#a37817;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <div>
              <div class="quick-nav-title">Available Donations</div>
              <div class="quick-nav-desc">Inspect unallocated funds & remaining physical goods</div>
              <div class="quick-nav-go">Open ›</div>
            </div>
          </a>

          <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="quick-nav-card">
            <div class="quick-nav-icon" style="background:#eaf2fc; color:#1e5f8b;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/></svg>
            </div>
            <div>
              <div class="quick-nav-title">Donation Allocation</div>
              <div class="quick-nav-desc">Allocate available balances to Damayan & Da'wah departments</div>
              <div class="quick-nav-go">Open ›</div>
            </div>
          </a>

          <a href="<?= url('/admin/damayan/charity/distributions') ?>" class="quick-nav-card">
            <div class="quick-nav-icon" style="background:#eef6f3; color:#176b45;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
            </div>
            <div>
              <div class="quick-nav-title">Distribution Records</div>
              <div class="quick-nav-desc">Log actual handouts, beneficiaries, and supporting receipts</div>
              <div class="quick-nav-go">Open ›</div>
            </div>
          </a>
        </div>

        <!-- 2 Column Layout: Recent Donations & Recent Distributions -->
        <div class="ch-split">
          
          <!-- Recent Donations Card -->
          <div class="section-card">
            <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
              <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                Recent Donations
              </h6>
              <a href="<?= url('/admin/damayan/charity/donations') ?>" style="font-size:0.78rem; font-weight:700; color:var(--primary); text-decoration:none;">View All ›</a>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Donor / Sponsor</th>
                      <th>Type</th>
                      <th>Amount / Items</th>
                      <th>Received</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recentDonations)): ?>
                      <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">No donations recorded yet.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($recentDonations as $d): ?>
                        <tr>
                          <td>
                            <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($d['donation_ref']) ?></strong>
                          </td>
                          <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($d['donor_name']) ?></div>
                            <?php if (!empty($d['payment_method'])): ?>
                              <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($d['payment_method']) ?></div>
                            <?php endif; ?>
                          </td>
                          <td>
                            <span class="badge-status" style="background:#f0f4f2; color:var(--text-main);">
                              <?= ucfirst($d['donation_type']) ?>
                            </span>
                          </td>
                          <td>
                            <?php if ($d['donation_type'] === 'monetary'): ?>
                              <span style="font-weight:700; color:var(--primary);">₱<?= number_format($d['amount'], 2) ?></span>
                            <?php else: ?>
                              <span style="font-weight:700; color:var(--accent);"><?= htmlspecialchars($d['quantity']) ?>x</span>
                              <span style="font-size:0.8rem; color:var(--text-muted);"><?= htmlspecialchars($d['item_description']) ?></span>
                            <?php endif; ?>
                          </td>
                          <td style="font-size:0.82rem; color:var(--text-muted);">
                            <?= date('M d, Y', strtotime($d['date_received'])) ?>
                          </td>
                          <td>
                            <?php 
                              $stClass = 'badge-available';
                              if ($d['status'] === 'Partially Allocated') $stClass = 'badge-allocated';
                              elseif ($d['status'] === 'Fully Allocated') $stClass = 'badge-distributed';
                              elseif ($d['status'] === 'Cancelled') $stClass = 'badge-cancelled';
                            ?>
                            <span class="badge-status <?= $stClass ?>"><?= htmlspecialchars($d['status']) ?></span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Recent Distributions Card -->
          <div class="section-card">
            <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
              <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                Recent Aid Distributions
              </h6>
              <a href="<?= url('/admin/damayan/charity/distributions') ?>" style="font-size:0.78rem; font-weight:700; color:var(--accent); text-decoration:none;">View All ›</a>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Dist Ref</th>
                      <th>Dept</th>
                      <th>Distributed Aid</th>
                      <th>Recipient</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($recentDistributions)): ?>
                      <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">No aid distributed yet.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($recentDistributions as $dist): ?>
                        <tr>
                          <td>
                            <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($dist['distribution_ref']) ?></strong>
                          </td>
                          <td>
                            <span class="dept-tag <?= $dist['receiving_department'] === 'Damayan' ? 'dept-damayan' : 'dept-dawah' ?>">
                              <?= htmlspecialchars($dist['receiving_department']) ?>
                            </span>
                          </td>
                          <td>
                            <div style="font-weight:700; font-size:0.88rem; color:var(--primary-dark);">
                              <?= htmlspecialchars($dist['items_summary']) ?>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($dist['purpose']) ?></div>
                          </td>
                          <td style="font-weight:600; font-size:0.85rem;">
                            <?= htmlspecialchars($dist['received_by']) ?>
                          </td>
                          <td style="font-size:0.82rem; color:var(--text-muted);">
                            <?= date('M d, Y', strtotime($dist['distribution_date'])) ?>
                          </td>
                          <td>
                            <span class="badge-status badge-distributed">Completed</span>
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

        <!-- Recent Audit Activities -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--text-muted);"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
              Recent Activity & Transaction Log
            </h6>
            <span style="font-size:0.75rem; color:var(--text-muted);">Logged in Real-Time</span>
          </div>
          <div class="section-card-body" style="padding:16px 24px;">
            <?php if (empty($activityLogs)): ?>
              <div class="ch-empty">No recorded activities yet.</div>
            <?php else: ?>
              <div class="ch-activity">
                <?php foreach ($activityLogs as $log): ?>
                  <div class="ch-activity-item">
                    <span class="ch-activity-dot">
                      <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </span>
                    <div class="ch-activity-main">
                      <div class="ch-activity-title">
                        <span class="ch-activity-ref"><?= htmlspecialchars($log['reference_no']) ?></span>
                        <span class="ch-activity-details"><?= htmlspecialchars($log['details']) ?></span>
                      </div>
                      <div class="ch-activity-meta">
                        Performed by <strong><?= htmlspecialchars($log['performed_by']) ?></strong>
                      </div>
                    </div>
                    <div class="ch-activity-time">
                      <?= date('M d, Y · h:i A', strtotime($log['created_at'])) ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
