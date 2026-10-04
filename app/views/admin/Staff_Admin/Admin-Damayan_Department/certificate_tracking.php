<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Certificate Tracking &amp; Release Scheduling</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
      --warning-gold: #b45309;
      --warning-gold-bg: #fef3c7;
      --info-blue: #1d4ed8;
      --info-blue-bg: #dbeafe;
      --purple-accent: #6d28d9;
      --purple-bg: #ede9fe;
    }
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    .insight-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .insight-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--damayan-accent);
    }
    .insight-card.st-requested::before { background: #3b82f6; }
    .insight-card.st-review::before { background: #f59e0b; }
    .insight-card.st-processing::before { background: #6366f1; }
    .insight-card.st-ready::before { background: #8b5cf6; }
    .insight-card.st-scheduled::before { background: #d97706; }
    .insight-card.st-released::before { background: #10b981; }

    .insight-label { 
      font-size: 0.72rem; 
      font-weight: 700; 
      color: var(--text-muted); 
      text-transform: uppercase; 
      letter-spacing: 0.05em; 
    }
    .insight-value { 
      font-size: 1.7rem; 
      font-weight: 800; 
      color: var(--damayan-dark); 
      line-height: 1.1; 
    }

    /* Filter & Search Bar */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 6px 14px;
      gap: 8px;
      min-width: 280px;
    }
    .search-box input {
      border: none;
      outline: none;
      font-size: 0.85rem;
      width: 100%;
      font-family: inherit;
    }
    .search-box svg {
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
    }

    .filter-pills {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 4px;
    }
    .filter-pill {
      font-size: 0.78rem;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
      text-decoration: none;
      color: var(--text-muted);
      background: white;
      border: 1px solid var(--border);
      transition: all 0.2s;
      white-space: nowrap;
    }
    .filter-pill:hover, .filter-pill.active {
      background: var(--damayan-light);
      color: var(--damayan-accent);
      border-color: var(--damayan-accent);
    }

    /* Status Badges */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .badge-status::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
    }
    .badge-st-requested { background: #eff6ff; color: #1d4ed8; }
    .badge-st-requested::before { background: #3b82f6; }
    .badge-st-under-review { background: #fffbeb; color: #b45309; }
    .badge-st-under-review::before { background: #f59e0b; }
    .badge-st-processing { background: #eef2ff; color: #4338ca; }
    .badge-st-processing::before { background: #6366f1; }
    .badge-st-ready { background: #f5f3ff; color: #6d28d9; }
    .badge-st-ready::before { background: #8b5cf6; }
    .badge-st-scheduled { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
    .badge-st-scheduled::before { background: #ea580c; }
    .badge-st-released { background: #ecfdf5; color: #047857; }
    .badge-st-released::before { background: #10b981; }
    .badge-st-cancelled, .badge-st-rejected { background: #fef2f2; color: #b91c1c; }
    .badge-st-cancelled::before, .badge-st-rejected::before { background: #ef4444; }

    /* Dropdown action button */
    .action-dropdown {
      position: relative;
      display: inline-block;
    }
    .action-menu {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background: white;
      border: 1px solid var(--border);
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      z-index: 100;
      min-width: 180px;
      padding: 6px 0;
    }
    .action-menu.show {
      display: block;
    }
    .action-menu-item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-main);
      text-decoration: none;
      cursor: pointer;
      background: none;
      border: none;
      width: 100%;
      text-align: left;
      transition: background 0.15s;
    }
    .action-menu-item:hover {
      background: #f8fafc;
      color: var(--damayan-accent);
    }
    .action-menu-item svg {
      width: 15px;
      height: 15px;
      fill: currentColor;
      flex-shrink: 0;
    }
    .action-menu-item.danger {
      color: #dc2626;
    }
    .action-menu-item.danger:hover {
      background: #fef2f2;
    }
    .action-divider {
      height: 1px;
      background: var(--border);
      margin: 4px 0;
    }

    /* Tracking Panel Modal Styles */
    .panel-section {
      background: #fafbfc;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 16px;
    }
    .panel-section-title {
      font-size: 0.76rem;
      font-weight: 800;
      color: var(--damayan-dark);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .panel-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
    .panel-item-label {
      font-size: 0.72rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 2px;
    }
    .panel-item-value {
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-main);
      word-break: break-word;
    }

    /* Mini Activity Timeline in Modal */
    .panel-timeline {
      position: relative;
      padding-left: 24px;
      margin-top: 12px;
    }
    .panel-timeline::before {
      content: '';
      position: absolute;
      left: 7px;
      top: 6px;
      bottom: 6px;
      width: 2px;
      background: var(--border);
    }
    .ptl-item {
      position: relative;
      padding-bottom: 14px;
    }
    .ptl-item:last-child {
      padding-bottom: 0;
    }
    .ptl-dot {
      position: absolute;
      left: -24px;
      top: 3px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      border: 2px solid var(--damayan-accent);
      background: white;
    }
    .ptl-dot.active {
      background: var(--damayan-accent);
      box-shadow: 0 0 0 3px var(--damayan-light);
    }
    .ptl-title {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--text-main);
    }
    .ptl-desc {
      font-size: 0.76rem;
      color: var(--text-muted);
      margin-top: 2px;
      line-height: 1.4;
    }
    .ptl-time {
      font-size: 0.7rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .form-group {
      margin-bottom: 16px;
    }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--text-main);
    }
    .form-group .form-hint {
      font-size: 0.72rem;
      color: var(--text-muted);
      margin-top: 4px;
    }
    .required-star {
      color: #dc2626;
      margin-left: 2px;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'certificate_tracking';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Certificate Tracking &amp; Release Scheduling</div>
            <div class="top-bar-subtitle">End-to-end management of Death Certificates from verification to release</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/damayan') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-accent);">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
          </a>
        </div>
      </div>

      <!-- Page Body -->
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span>
          <span class="current">Certificate Tracking</span>
        </div>

        <!-- Metric Insights Cards -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Requests</div>
            <div class="insight-value"><?= (int)($analytics['total'] ?? 0) ?></div>
          </div>
          <div class="insight-card st-processing">
            <div class="insight-label">In Processing</div>
            <div class="insight-value" style="color:var(--info-blue);"><?= (int)($analytics['processing'] ?? 0) ?></div>
          </div>
          <div class="insight-card st-ready">
            <div class="insight-label">Ready for Release</div>
            <div class="insight-value" style="color:var(--purple-accent);"><?= (int)($analytics['ready'] ?? 0) ?></div>
          </div>
          <div class="insight-card st-scheduled">
            <div class="insight-label">Scheduled for Release</div>
            <div class="insight-value" style="color:#d97706;"><?= (int)($analytics['scheduled'] ?? 0) ?></div>
          </div>
          <div class="insight-card st-released">
            <div class="insight-label">Released</div>
            <div class="insight-value" style="color:#10b981;"><?= (int)($analytics['released'] ?? 0) ?></div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="filter-bar">
          <div class="filter-pills">
            <a href="<?= url('/admin/damayan/certificate-tracking') ?>" class="filter-pill <?= empty($currentFilter) ? 'active' : '' ?>">All Requests</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Requested') ?>" class="filter-pill <?= ($currentFilter === 'Requested') ? 'active' : '' ?>">Requested</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Under Review') ?>" class="filter-pill <?= ($currentFilter === 'Under Review') ? 'active' : '' ?>">Under Review</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Processing') ?>" class="filter-pill <?= ($currentFilter === 'Processing') ? 'active' : '' ?>">Processing</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Ready for Release') ?>" class="filter-pill <?= ($currentFilter === 'Ready for Release') ? 'active' : '' ?>">Ready</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Scheduled for Release') ?>" class="filter-pill <?= ($currentFilter === 'Scheduled for Release') ? 'active' : '' ?>">Scheduled</a>
            <a href="<?= url('/admin/damayan/certificate-tracking?status=Released') ?>" class="filter-pill <?= ($currentFilter === 'Released') ? 'active' : '' ?>">Released</a>
          </div>

          <form method="GET" action="<?= url('/admin/damayan/certificate-tracking') ?>" style="display:flex;gap:8px;">
            <?php if (!empty($currentFilter)): ?>
              <input type="hidden" name="status" value="<?= htmlspecialchars($currentFilter) ?>">
            <?php endif; ?>
            <div class="search-box">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" name="search" placeholder="Search ID, deceased, or requester..." value="<?= htmlspecialchars($search ?? '') ?>" />
            </div>
            <button type="submit" class="btn-topbar primary" style="padding:6px 14px;">Search</button>
            <?php if (!empty($search) || !empty($currentFilter)): ?>
              <a href="<?= url('/admin/damayan/certificate-tracking') ?>" class="btn-topbar" style="padding:6px 14px;text-decoration:none;">Clear</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Certificate Table Card -->
        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              Death Certificate Requests &amp; Release Schedules
            </h6>
            <div style="font-size:0.78rem;font-weight:600;color:var(--text-muted);">
              Showing <?= count($certificates) ?> record(s)
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Requester Name</th>
                    <th>Certificate Type</th>
                    <th>Request Date</th>
                    <th>Current Status</th>
                    <th>Processing Status</th>
                    <th>Scheduled Release</th>
                    <th>Last Updated</th>
                    <th style="text-align:center;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($certificates)): ?>
                    <tr>
                      <td colspan="9" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <div style="font-weight:600;font-size:0.95rem;margin-bottom:4px;">No certificate requests found</div>
                        <div style="font-size:0.8rem;">Requests submitted through the death report system will appear here.</div>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($certificates as $c): 
                      $cStatus = $c['certificate_status'] ?? 'Requested';
                      $cBadgeCls = match($cStatus) {
                        'Requested' => 'badge-st-requested',
                        'Under Review' => 'badge-st-under-review',
                        'Processing' => 'badge-st-processing',
                        'Ready for Release' => 'badge-st-ready',
                        'Scheduled for Release' => 'badge-st-scheduled',
                        'Released' => 'badge-st-released',
                        'Cancelled', 'Rejected' => 'badge-st-cancelled',
                        default => 'badge-st-requested'
                      };

                      $reqName = trim(($c['informant_name'] ?? '') ?: (($c['t_first'] ?? '') . ' ' . ($c['t_last'] ?? ''))) ?: 'Requester';
                      $deceased = trim(($c['deceased_first_name'] ?? '') . ' ' . ($c['deceased_last_name'] ?? ''));

                      // Scheduled release display
                      $scheduledDisplay = '—';
                      if (!empty($c['scheduled_release_date'])) {
                        $sDateStr = date('M d, Y', strtotime($c['scheduled_release_date']));
                        $sTimeStr = !empty($c['scheduled_release_time']) ? date('g:i A', strtotime($c['scheduled_release_time'])) : '';
                        $scheduledDisplay = "<strong>{$sDateStr}</strong>" . ($sTimeStr ? "<br><span style='font-size:0.72rem;color:var(--text-muted);'>{$sTimeStr}</span>" : '');
                      } elseif ($cStatus === 'Released' && !empty($c['released_at'])) {
                        $scheduledDisplay = "<span style='color:var(--success);font-weight:700;'>Released " . date('M d, Y', strtotime($c['released_at'])) . "</span>";
                      }
                    ?>
                      <tr>
                        <td class="td-id" style="font-weight:700;color:var(--damayan-dark);">
                          <a href="javascript:void(0)" onclick="openTrackingDetails(<?= (int)$c['id'] ?>)" style="color:var(--damayan-accent);text-decoration:none;">
                            <?= htmlspecialchars($c['case_number']) ?>
                          </a>
                          <?php if ($deceased): ?>
                            <div style="font-size:0.72rem;color:var(--text-muted);font-weight:400;">Deceased: <?= htmlspecialchars($deceased) ?></div>
                          <?php endif; ?>
                        </td>
                        <td style="font-weight:600;">
                          <?= htmlspecialchars($reqName) ?>
                          <?php if (!empty($c['informant_contact'])): ?>
                            <div style="font-size:0.72rem;color:var(--text-muted);font-weight:400;"><?= htmlspecialchars($c['informant_contact']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($c['certificate_type'] ?? 'Death Certificate') ?></td>
                        <td><?= !empty($c['submitted_at']) ? date('M d, Y', strtotime($c['submitted_at'])) : '—' ?></td>
                        <td>
                          <span class="badge-status <?= $cBadgeCls ?>"><?= htmlspecialchars($cStatus) ?></span>
                        </td>
                        <td style="font-size:0.78rem;">
                          <?php if ($cStatus === 'Processing'): ?>
                            <span style="color:var(--info-blue);font-weight:600;">Started: <?= !empty($c['processing_started_at']) ? date('M d, g:i A', strtotime($c['processing_started_at'])) : 'In Progress' ?></span>
                          <?php elseif ($cStatus === 'Ready for Release' || $cStatus === 'Scheduled for Release' || $cStatus === 'Released'): ?>
                            <span style="color:var(--purple-accent);font-weight:600;">Completed: <?= !empty($c['ready_at']) ? date('M d, Y', strtotime($c['ready_at'])) : 'Ready' ?></span>
                          <?php elseif ($cStatus === 'Under Review'): ?>
                            <span style="color:var(--warning-gold);font-weight:600;">Under Staff Review</span>
                          <?php else: ?>
                            <span style="color:var(--text-muted);">Awaiting Processing</span>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.8rem;"><?= $scheduledDisplay ?></td>
                        <td style="font-size:0.75rem;color:var(--text-muted);">
                          <?= !empty($c['updated_at']) ? date('M d, Y g:i A', strtotime($c['updated_at'])) : '—' ?>
                        </td>
                        <td style="text-align:center;">
                          <div class="action-dropdown" id="dropdown-<?= (int)$c['id'] ?>">
                            <button class="btn-topbar primary" style="padding:4px 10px;font-size:0.75rem;" onclick="openTrackingDetails(<?= (int)$c['id'] ?>)">
                              Manage
                            </button>
                            <button class="btn-topbar" style="padding:4px 8px;font-size:0.75rem;margin-left:2px;" onclick="toggleActionMenu(event, <?= (int)$c['id'] ?>)">
                              ▾
                            </button>
                            <div class="action-menu" id="menu-<?= (int)$c['id'] ?>">
                              <button class="action-menu-item" onclick="openTrackingDetails(<?= (int)$c['id'] ?>)">
                                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                View Details &amp; Activity
                              </button>
                              
                              <?php if ($cStatus === 'Requested' || $cStatus === 'Under Review'): ?>
                                <button class="action-menu-item" onclick="quickStatus(<?= (int)$c['id'] ?>, 'Processing')">
                                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                  Start Processing
                                </button>
                              <?php endif; ?>

                              <?php if ($cStatus === 'Processing'): ?>
                                <button class="action-menu-item" onclick="quickStatus(<?= (int)$c['id'] ?>, 'Ready for Release')">
                                  <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                  Mark as Ready
                                </button>
                              <?php endif; ?>

                              <?php if ($cStatus === 'Ready for Release'): ?>
                                <button class="action-menu-item" onclick="openScheduleModal(<?= (int)$c['id'] ?>, false)">
                                  <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                  Schedule Release
                                </button>
                              <?php endif; ?>

                              <?php if ($cStatus === 'Scheduled for Release'): ?>
                                <button class="action-menu-item" onclick="openScheduleModal(<?= (int)$c['id'] ?>, true)">
                                  <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                  Reschedule Release
                                </button>
                                <button class="action-menu-item" onclick="openReleaseModal(<?= (int)$c['id'] ?>, '<?= htmlspecialchars(addslashes($reqName)) ?>')">
                                  <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                  Mark as Released
                                </button>
                              <?php endif; ?>

                              <div class="action-divider"></div>
                              <?php if ($cStatus !== 'Released' && $cStatus !== 'Cancelled'): ?>
                                <button class="action-menu-item danger" onclick="cancelCertificate(<?= (int)$c['id'] ?>)">
                                  <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                  Cancel Request
                                </button>
                              <?php endif; ?>
                            </div>
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

  <!-- ══════════════════════════════════════════════════════════════════
       MODAL 1: DETAILED TRACKING & AUDIT PANEL
  ══════════════════════════════════════════════════════════════════ -->
  <div id="detailsModal" class="mis-modal" style="display:none;">
    <div class="mis-modal-content" style="max-width:760px;max-height:90vh;overflow-y:auto;">
      <div class="mis-modal-header">
        <div>
          <h6 id="modal-case-title" style="margin:0;font-size:1.05rem;color:var(--damayan-dark);">Certificate Details &amp; Tracking</h6>
          <div id="modal-case-subtitle" style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">Request ID: —</div>
        </div>
        <button class="close-btn" onclick="closeDetailsModal()">&times;</button>
      </div>

      <div class="mis-modal-body" style="padding:20px 24px;">
        <!-- Status summary banner -->
        <div id="modal-status-banner" style="padding:14px 18px;border-radius:12px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;background:var(--damayan-light);border:1px solid #c7e6d4;">
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--damayan-accent);color:white;display:flex;align-items:center;justify-content:center;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div>
              <div style="font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Current Stage</div>
              <div id="modal-banner-status" style="font-size:1rem;font-weight:800;color:var(--damayan-dark);">Requested</div>
            </div>
          </div>
          <div id="modal-primary-action-wrap">
            <!-- Dynamic button injected here based on stage -->
          </div>
        </div>

        <!-- 1. Request Information -->
        <div class="panel-section">
          <div class="panel-section-title">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:var(--damayan-accent);"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            Request Information
          </div>
          <div class="panel-grid">
            <div>
              <div class="panel-item-label">Request ID</div>
              <div class="panel-item-value" id="p-req-id">—</div>
            </div>
            <div>
              <div class="panel-item-label">Certificate Type</div>
              <div class="panel-item-value" id="p-cert-type">Death Certificate</div>
            </div>
            <div>
              <div class="panel-item-label">Requester's Name</div>
              <div class="panel-item-value" id="p-req-name">—</div>
            </div>
            <div>
              <div class="panel-item-label">Date Requested</div>
              <div class="panel-item-value" id="p-req-date">—</div>
            </div>
            <div>
              <div class="panel-item-label">Contact Information</div>
              <div class="panel-item-value" id="p-req-contact">—</div>
            </div>
            <div>
              <div class="panel-item-label">Deceased Individual</div>
              <div class="panel-item-value" id="p-req-deceased">—</div>
            </div>
          </div>
        </div>

        <!-- 2. Certificate Information -->
        <div class="panel-section">
          <div class="panel-section-title">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:var(--damayan-accent);"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
            Certificate Information
          </div>
          <div class="panel-grid">
            <div>
              <div class="panel-item-label">Certificate Status</div>
              <div class="panel-item-value" id="p-cert-status">—</div>
            </div>
            <div>
              <div class="panel-item-label">Current Processing Stage</div>
              <div class="panel-item-value" id="p-cert-stage">—</div>
            </div>
            <div>
              <div class="panel-item-label">Date Processing Started</div>
              <div class="panel-item-value" id="p-cert-started">—</div>
            </div>
            <div>
              <div class="panel-item-label">Date Completed / Ready</div>
              <div class="panel-item-value" id="p-cert-completed">—</div>
            </div>
            <div style="grid-column: 1 / -1;">
              <div class="panel-item-label">Certificate Availability</div>
              <div class="panel-item-value" id="p-cert-availability">Document records in preparation</div>
            </div>
          </div>
        </div>

        <!-- 3. Release Information -->
        <div class="panel-section">
          <div class="panel-section-title">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:var(--damayan-accent);"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
            Release Information
          </div>
          <div class="panel-grid">
            <div>
              <div class="panel-item-label">Release Method</div>
              <div class="panel-item-value" id="p-rel-method">Office Pick-up</div>
            </div>
            <div>
              <div class="panel-item-label">Release Location</div>
              <div class="panel-item-value" id="p-rel-location">Masjid Office</div>
            </div>
            <div>
              <div class="panel-item-label">Scheduled Release Date</div>
              <div class="panel-item-value" id="p-rel-date">—</div>
            </div>
            <div>
              <div class="panel-item-label">Scheduled Release Time</div>
              <div class="panel-item-value" id="p-rel-time">—</div>
            </div>
            <div>
              <div class="panel-item-label">Assigned Staff / Admin</div>
              <div class="panel-item-value" id="p-rel-staff">—</div>
            </div>
            <div>
              <div class="panel-item-label">Released At / By</div>
              <div class="panel-item-value" id="p-rel-actual">—</div>
            </div>
            <div style="grid-column: 1 / -1;" id="p-rel-notes-wrap">
              <div class="panel-item-label">Release Notes</div>
              <div class="panel-item-value" id="p-rel-notes" style="font-style:italic;color:var(--text-muted);">None</div>
            </div>
          </div>
        </div>

        <!-- 4. Activity History / Audit Trail -->
        <div class="panel-section">
          <div class="panel-section-title">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:var(--damayan-accent);"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
            Activity History &amp; Audit Trail
          </div>
          <div class="panel-timeline" id="modal-activity-timeline">
            <!-- Dynamic audit trail logs injected here -->
          </div>
        </div>
      </div>

      <div class="mis-modal-footer" style="padding:16px 24px;justify-content:space-between;">
        <button class="btn-topbar" onclick="closeDetailsModal()">Close</button>
        <div id="modal-footer-actions" style="display:flex;gap:8px;"></div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════════
       MODAL 2: RELEASE SCHEDULING & RESCHEDULING
  ══════════════════════════════════════════════════════════════════ -->
  <div id="scheduleModal" class="mis-modal" style="display:none;">
    <div class="mis-modal-content" style="max-width:520px;">
      <div class="mis-modal-header">
        <h6 id="sched-modal-title">Schedule Certificate Release</h6>
        <button class="close-btn" onclick="closeScheduleModal()">&times;</button>
      </div>
      <div class="mis-modal-body" style="padding:20px 24px;">
        <input type="hidden" id="sched-case-id" />
        <input type="hidden" id="sched-is-reschedule" value="0" />

        <div id="resched-alert-box" style="display:none;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px;margin-bottom:16px;font-size:0.8rem;color:#92400e;">
          <strong>Rescheduling Notice:</strong> Previous schedule will be safely archived in the activity history.
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div class="form-group">
            <label>Release Date <span class="required-star">*</span></label>
            <input type="date" id="sched-date" class="mis-input" min="<?= date('Y-m-d') ?>" required />
            <div class="form-hint">Cannot be in the past</div>
          </div>
          <div class="form-group">
            <label>Release Time <span class="required-star">*</span></label>
            <input type="time" id="sched-time" class="mis-input" required />
            <div class="form-hint">Working hours (e.g. 09:00 - 17:00)</div>
          </div>
        </div>

        <div class="form-group">
          <label>Release Location <span class="required-star">*</span></label>
          <select id="sched-location" class="mis-input">
            <option value="Masjid Office">Masjid Office / Main Reception</option>
            <option value="Damayan Department Office">Damayan Department Office (Room 102)</option>
            <option value="ISCAG Administration Center">ISCAG Administration Center</option>
            <option value="Other">Custom Location...</option>
          </select>
          <input type="text" id="sched-location-custom" class="mis-input" placeholder="Enter custom release location" style="display:none;margin-top:8px;" />
        </div>

        <div class="form-group">
          <label>Assigned Staff Member</label>
          <select id="sched-staff" class="mis-input">
            <option value="">Select Responsible Staff...</option>
            <?php foreach ($staffList as $staff): ?>
              <option value="<?= htmlspecialchars($staff['tenant_id']) ?>" data-name="<?= htmlspecialchars(trim(($staff['first_name'] ?? '') . ' ' . ($staff['last_name'] ?? ''))) ?>">
                <?= htmlspecialchars(trim(($staff['first_name'] ?? '') . ' ' . ($staff['last_name'] ?? ''))) ?> (<?= htmlspecialchars($staff['role']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group" id="resched-reason-group" style="display:none;">
          <label>Reason for Rescheduling <span class="required-star">*</span></label>
          <input type="text" id="sched-reason" class="mis-input" placeholder="e.g. Requester requested later pickup time" />
          <div class="form-hint">Required for the official audit log</div>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label>Release Notes (Optional)</label>
          <textarea id="sched-notes" class="mis-input" rows="2" placeholder="e.g. Please bring original valid government-issued ID upon pickup"></textarea>
        </div>
      </div>
      <div class="mis-modal-footer" style="padding:16px 24px;justify-content:flex-end;gap:8px;">
        <button class="btn-topbar" onclick="closeScheduleModal()">Cancel</button>
        <button class="btn-topbar primary" id="sched-submit-btn" onclick="submitSchedule()">Schedule Release</button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════════════
       MODAL 3: RELEASE CONFIRMATION MODAL
  ══════════════════════════════════════════════════════════════════ -->
  <div id="releaseConfirmModal" class="mis-modal" style="display:none;">
    <div class="mis-modal-content" style="max-width:500px;">
      <div class="mis-modal-header">
        <h6>Confirm Certificate Release</h6>
        <button class="close-btn" onclick="closeReleaseModal()">&times;</button>
      </div>
      <div class="mis-modal-body" style="padding:20px 24px;">
        <input type="hidden" id="rel-confirm-case-id" />
        
        <div style="display:flex;align-items:center;gap:12px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:14px;margin-bottom:18px;">
          <div style="width:36px;height:36px;border-radius:50%;background:#10b981;color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
          </div>
          <div style="font-size:0.85rem;color:#065f46;line-height:1.4;">
            Are you sure you want to mark this certificate as <strong>Released</strong>? This completes the certificate request workflow.
          </div>
        </div>

        <div class="form-group">
          <label>Recipient Full Name <span class="required-star">*</span></label>
          <input type="text" id="rel-recipient" class="mis-input" required />
          <div class="form-hint">Person who actually received the physical certificate</div>
        </div>

        <div class="form-group">
          <label>Release Method</label>
          <select id="rel-method" class="mis-input">
            <option value="Office Pick-up">Office Pick-up (In-person)</option>
            <option value="Authorized Representative">Authorized Representative (with SPA/ID)</option>
            <option value="Official Courier / Delivery">Official Courier / Delivery</option>
          </select>
        </div>

        <div class="form-group">
          <label>Released By (Staff/Admin)</label>
          <input type="text" id="rel-released-by" class="mis-input" value="<?= htmlspecialchars($_SESSION['name'] ?? 'Admin Staff') ?>" />
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label>Release Notes / Remarks</label>
          <textarea id="rel-notes" class="mis-input" rows="2" placeholder="e.g. ID verified and signed acknowledgment form received"></textarea>
        </div>
      </div>
      <div class="mis-modal-footer" style="padding:16px 24px;justify-content:flex-end;gap:8px;">
        <button class="btn-topbar" onclick="closeReleaseModal()">Cancel</button>
        <button class="btn-topbar primary" onclick="submitReleaseConfirmation()">Confirm &amp; Mark Released</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    let currentActiveCase = null;

    // Toggle menu
    function toggleActionMenu(e, id) {
      e.stopPropagation();
      document.querySelectorAll('.action-menu').forEach(m => {
        if(m.id !== 'menu-' + id) m.classList.remove('show');
      });
      const menu = document.getElementById('menu-' + id);
      if(menu) menu.classList.toggle('show');
    }
    document.addEventListener('click', () => {
      document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('show'));
    });

    // Custom location toggle
    document.getElementById('sched-location').addEventListener('change', function() {
      const customInput = document.getElementById('sched-location-custom');
      if(this.value === 'Other') {
        customInput.style.display = 'block';
        customInput.focus();
      } else {
        customInput.style.display = 'none';
      }
    });

    /* ══════════════════════════════════════════════════════════════
       DETAILS PANEL MODAL
    ══════════════════════════════════════════════════════════════ */
    async function openTrackingDetails(caseId) {
      try {
        const res = await fetch('<?= url('/admin/damayan/certificate-tracking/detail') ?>?id=' + caseId);
        const data = await res.json();
        if(!data.success) {
          showAlert('Error', data.error || 'Failed to load details.', 'error');
          return;
        }

        const c = data.case;
        currentActiveCase = c;

        document.getElementById('modal-case-title').textContent = 'Certificate Tracking — ' + c.case_number;
        document.getElementById('modal-case-subtitle').textContent = 'Deceased: ' + (c.deceased_first_name + ' ' + (c.deceased_last_name || ''));
        document.getElementById('modal-banner-status').textContent = c.certificate_status || 'Requested';

        // 1. Request Info
        document.getElementById('p-req-id').textContent = c.case_number;
        document.getElementById('p-cert-type').textContent = c.certificate_type || 'Death Certificate';
        document.getElementById('p-req-name').textContent = c.informant_name || (data.requester ? data.requester.first_name + ' ' + data.requester.last_name : 'Requester');
        document.getElementById('p-req-date').textContent = c.submitted_at ? formatDate(c.submitted_at) : '—';
        document.getElementById('p-req-contact').textContent = (c.informant_contact || '') + (c.informant_email ? ' / ' + c.informant_email : '');
        document.getElementById('p-req-deceased').textContent = (c.deceased_first_name || '') + ' ' + (c.deceased_last_name || '');

        // 2. Certificate Info
        document.getElementById('p-cert-status').textContent = c.certificate_status || 'Requested';
        document.getElementById('p-cert-stage').textContent = c.certificate_status || 'Requested';
        document.getElementById('p-cert-started').textContent = c.processing_started_at ? formatDateTime(c.processing_started_at) : 'Not started yet';
        document.getElementById('p-cert-completed').textContent = c.ready_at ? formatDateTime(c.ready_at) : (c.certificate_status === 'Released' ? 'Completed' : 'Pending completion');
        document.getElementById('p-cert-availability').textContent = (c.certificate_status === 'Ready for Release' || c.certificate_status === 'Scheduled for Release' || c.certificate_status === 'Released') ? 'Printed and Verified (Ready)' : 'In processing pipeline';

        // 3. Release Info
        document.getElementById('p-rel-method').textContent = c.release_method || 'Office Pick-up';
        document.getElementById('p-rel-location').textContent = c.release_location || 'Masjid Office';
        document.getElementById('p-rel-date').textContent = c.scheduled_release_date ? formatDate(c.scheduled_release_date) : 'Not scheduled yet';
        document.getElementById('p-rel-time').textContent = c.scheduled_release_time ? formatTime(c.scheduled_release_time) : 'Not scheduled yet';
        document.getElementById('p-rel-staff').textContent = c.assigned_staff_name || 'Unassigned';
        document.getElementById('p-rel-actual').textContent = c.released_at ? (formatDateTime(c.released_at) + (c.released_by ? ' by ' + c.released_by : '')) : (c.certificate_status === 'Released' ? 'Released' : 'Waiting for release');
        document.getElementById('p-rel-notes').textContent = c.release_notes || 'None';

        // 4. Activity Logs
        renderModalTimeline(data.logs || []);

        // 5. Action Buttons in Modal
        renderModalActions(c);

        document.getElementById('detailsModal').style.display = 'flex';
      } catch (err) {
        console.error(err);
        showAlert('Error', 'Unable to fetch certificate details.', 'error');
      }
    }

    function closeDetailsModal() {
      document.getElementById('detailsModal').style.display = 'none';
    }

    function renderModalTimeline(logs) {
      const tl = document.getElementById('modal-activity-timeline');
      if(!logs || logs.length === 0) {
        tl.innerHTML = '<div style="font-size:0.8rem;color:var(--text-muted);padding:8px 0;">No activity logged yet.</div>';
        return;
      }
      tl.innerHTML = logs.map((l, idx) => `
        <div class="ptl-item">
          <div class="ptl-dot ${idx === 0 ? 'active' : ''}"></div>
          <div class="ptl-title">${escapeHtml(l.action ? l.action.replace('CERTIFICATE_', '').replace(/_/g, ' ') : 'Activity')}</div>
          <div class="ptl-desc">${escapeHtml(l.description || '')}</div>
          <div class="ptl-time">${formatDateTime(l.created_at)}</div>
        </div>
      `).join('');
    }

    function renderModalActions(c) {
      const wrap = document.getElementById('modal-primary-action-wrap');
      const footerWrap = document.getElementById('modal-footer-actions');
      const st = c.certificate_status || 'Requested';

      let primaryBtn = '';
      let secondaryBtns = '';

      if (st === 'Requested' || st === 'Under Review') {
        primaryBtn = `<button class="btn-topbar primary" onclick="quickStatus(${c.id}, 'Processing')">Start Processing →</button>`;
      } else if (st === 'Processing') {
        primaryBtn = `<button class="btn-topbar primary" onclick="quickStatus(${c.id}, 'Ready for Release')">Mark as Ready ✓</button>`;
      } else if (st === 'Ready for Release') {
        primaryBtn = `<button class="btn-topbar primary" onclick="openScheduleModal(${c.id}, false)">Schedule Release 📅</button>`;
      } else if (st === 'Scheduled for Release') {
        primaryBtn = `<button class="btn-topbar primary" onclick="openReleaseModal(${c.id}, '${escapeHtml(c.informant_name || 'Requester')}')">Mark as Released 🎁</button>`;
        secondaryBtns = `<button class="btn-topbar" onclick="openScheduleModal(${c.id}, true)">Reschedule</button>`;
      }

      wrap.innerHTML = primaryBtn;
      footerWrap.innerHTML = secondaryBtns;
    }

    /* ══════════════════════════════════════════════════════════════
       STATUS UPDATE API
    ══════════════════════════════════════════════════════════════ */
    async function quickStatus(caseId, newStatus, reason = '') {
      try {
        const res = await fetch('<?= url('/admin/damayan/certificate-tracking/status') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ case_id: caseId, status: newStatus, reason: reason })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Success', 'Status successfully updated to: ' + newStatus, 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Error', data.error || 'Failed to update status.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'An unexpected error occurred.', 'error');
      }
    }

    function cancelCertificate(caseId) {
      showConfirm(
        'Cancel Certificate Request',
        'Are you sure you want to cancel this certificate request? This will mark the record as Cancelled.',
        'Cancel Request',
        () => {
          const reason = prompt('Please enter the cancellation reason:') || 'Cancelled by administrator';
          quickStatus(caseId, 'Cancelled', reason);
        }
      );
    }

    /* ══════════════════════════════════════════════════════════════
       RELEASE SCHEDULING / RESCHEDULING MODAL
    ══════════════════════════════════════════════════════════════ */
    function openScheduleModal(caseId, isReschedule) {
      document.getElementById('sched-case-id').value = caseId;
      document.getElementById('sched-is-reschedule').value = isReschedule ? '1' : '0';

      const title = document.getElementById('sched-modal-title');
      const submitBtn = document.getElementById('sched-submit-btn');
      const reschedAlert = document.getElementById('resched-alert-box');
      const reasonGroup = document.getElementById('resched-reason-group');

      if(isReschedule) {
        title.textContent = 'Reschedule Certificate Release';
        submitBtn.textContent = 'Save New Schedule';
        reschedAlert.style.display = 'block';
        reasonGroup.style.display = 'block';
      } else {
        title.textContent = 'Schedule Certificate Release';
        submitBtn.textContent = 'Schedule Release';
        reschedAlert.style.display = 'none';
        reasonGroup.style.display = 'none';
      }

      // Default date to tomorrow if not set
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      document.getElementById('sched-date').value = tomorrow.toISOString().split('T')[0];
      document.getElementById('sched-time').value = '14:00';

      document.getElementById('scheduleModal').style.display = 'flex';
    }

    function closeScheduleModal() {
      document.getElementById('scheduleModal').style.display = 'none';
    }

    async function submitSchedule() {
      const caseId = document.getElementById('sched-case-id').value;
      const isReschedule = document.getElementById('sched-is-reschedule').value === '1';

      const date = document.getElementById('sched-date').value;
      const time = document.getElementById('sched-time').value;
      let location = document.getElementById('sched-location').value;
      if(location === 'Other') {
        location = document.getElementById('sched-location-custom').value.trim() || 'Masjid Office';
      }

      const staffSelect = document.getElementById('sched-staff');
      const staffId = staffSelect.value;
      const staffName = staffSelect.selectedIndex > 0 ? staffSelect.options[staffSelect.selectedIndex].getAttribute('data-name') : '';
      const notes = document.getElementById('sched-notes').value.trim();
      const reason = document.getElementById('sched-reason').value.trim();

      // Front-end validations
      if(!date || !time) {
        showAlert('Validation Error', 'Please select both a valid release date and time.', 'warning');
        return;
      }

      const selectedDt = new Date(date + 'T' + time);
      const now = new Date();
      if(selectedDt < now) {
        showAlert('Validation Error', 'Release date and time cannot be in the past.', 'warning');
        return;
      }

      if(isReschedule && !reason) {
        showAlert('Validation Error', 'Please provide a reason for rescheduling.', 'warning');
        return;
      }

      const endpoint = isReschedule 
        ? '<?= url('/admin/damayan/certificate-tracking/reschedule') ?>'
        : '<?= url('/admin/damayan/certificate-tracking/schedule') ?>';

      try {
        const res = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            case_id: caseId,
            release_date: date,
            release_time: time,
            release_location: location,
            assigned_staff_id: staffId,
            assigned_staff_name: staffName,
            release_notes: notes,
            reschedule_reason: reason
          })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Success', data.message || 'Release schedule updated successfully.', 'success');
          closeScheduleModal();
          setTimeout(() => location.reload(), 1400);
        } else {
          showAlert('Scheduling Failed', data.error || 'Unable to save release schedule.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'An unexpected error occurred while saving the schedule.', 'error');
      }
    }

    /* ══════════════════════════════════════════════════════════════
       RELEASE CONFIRMATION MODAL
    ══════════════════════════════════════════════════════════════ */
    function openReleaseModal(caseId, recipientName) {
      document.getElementById('rel-confirm-case-id').value = caseId;
      document.getElementById('rel-recipient').value = recipientName || '';
      document.getElementById('releaseConfirmModal').style.display = 'flex';
    }

    function closeReleaseModal() {
      document.getElementById('releaseConfirmModal').style.display = 'none';
    }

    async function submitReleaseConfirmation() {
      const caseId = document.getElementById('rel-confirm-case-id').value;
      const recipient = document.getElementById('rel-recipient').value.trim();
      const method = document.getElementById('rel-method').value;
      const releasedBy = document.getElementById('rel-released-by').value.trim();
      const notes = document.getElementById('rel-notes').value.trim();

      if(!recipient) {
        showAlert('Validation Error', 'Recipient name is required.', 'warning');
        return;
      }

      try {
        const res = await fetch('<?= url('/admin/damayan/certificate-tracking/release') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            case_id: caseId,
            recipient_name: recipient,
            release_method: method,
            released_by: releasedBy,
            release_notes: notes
          })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Certificate Released', 'Certificate has been marked as Released successfully.', 'success');
          closeReleaseModal();
          setTimeout(() => location.reload(), 1400);
        } else {
          showAlert('Error', data.error || 'Failed to confirm release.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'An unexpected error occurred while confirming release.', 'error');
      }
    }

    // Helper formatters
    function formatDate(dateStr) {
      if(!dateStr) return '—';
      const d = new Date(dateStr);
      return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
    function formatTime(timeStr) {
      if(!timeStr) return '';
      const [h, m] = timeStr.split(':');
      const hour = parseInt(h, 10);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const fHour = hour % 12 || 12;
      return `${fHour}:${m} ${ampm}`;
    }
    function formatDateTime(dtStr) {
      if(!dtStr) return '—';
      const d = new Date(dtStr);
      return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + 
             d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    function escapeHtml(str) {
      if(!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
  </script>
</body>
</html>
