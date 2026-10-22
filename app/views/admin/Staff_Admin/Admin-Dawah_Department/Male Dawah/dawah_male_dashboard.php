<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Da'wah Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .dash-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
    .dash-kpi {
      background: white; border-radius: 14px; padding: 22px 24px; border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: all 0.3s ease; cursor: pointer; position: relative; overflow: hidden;
    }
    .dash-kpi::before {
      content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; border-radius: 14px 0 0 14px;
    }
    .dash-kpi:nth-child(1)::before { background: var(--primary); }
    .dash-kpi:nth-child(2)::before { background: var(--accent); }
    .dash-kpi:nth-child(3)::before { background: var(--success); }
    .dash-kpi:nth-child(4)::before { background: var(--danger); }
    .dash-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-color: var(--accent); }
    .kpi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .kpi-icon {
      width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    }
    .kpi-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    .kpi-value { font-size: 2rem; font-weight: 800; line-height: 1; font-family: 'Lora', serif; }
    .kpi-sub { font-size: 0.72rem; font-weight: 700; margin-top: 6px; display: flex; align-items: center; gap: 4px; color: var(--text-muted); }

    .quick-links { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    .quick-link {
      display: flex; align-items: center; gap: 14px; padding: 16px 20px; background: white;
      border-radius: 12px; border: 1px solid var(--border); text-decoration: none; color: var(--text-main);
      transition: all 0.25s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .quick-link:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
    .ql-icon {
      width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ql-title { font-weight: 700; font-size: 0.9rem; }
    .ql-desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

    .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .activity-item {
      display: flex; align-items: center; gap: 14px; padding: 14px 0;
      border-bottom: 1px solid rgba(0,0,0,0.04); transition: background 0.2s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: rgba(0,0,0,0.01); border-radius: 8px; padding-left: 8px; padding-right: 8px; }
    .act-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .act-name { font-weight: 700; font-size: 0.88rem; }
    .act-meta { font-size: 0.75rem; color: var(--text-muted); }

    .tab-group { display:flex; gap:6px; margin-bottom:0; padding:16px 20px 0; }
    .tab-pill {
      padding:8px 18px; border-radius:8px; border:none; background:transparent; font-weight:700;
      font-size:0.78rem; cursor:pointer; color:var(--text-muted); transition:all 0.2s;
    }
    .tab-pill.active { background:var(--primary); color:white; }
    .tab-pill:hover:not(.active) { background:rgba(0,0,0,0.04); color:var(--text-main); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'dashboard';
      $dawah_type = 'male'; 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Welcome, <?= htmlspecialchars(explode(' ',trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')))[0]) ?: 'Da\'wah Manager' ?></div>
          <div class="top-bar-subtitle">Male Da'wah Department — <?= date('l, F j, Y') ?></div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="location.href='<?= url('/admin/dawah/analytics') ?>'">
             <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/><path d="M9 17V10M12 17V7M15 17V13"/></svg>
             Analytics
           </button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Male Da'wah Dashboard</span>
        </div>

        <!-- KPI Cards -->
        <!-- KPI Cards -->
        <div class="dash-kpi-grid" style="grid-template-columns: repeat(6, 1fr);">
          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/counseling') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Counseling</span>
              <div class="kpi-icon" style="background:rgba(23,107,69,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--primary);"><?= $analytics['counseling_total'] ?? 0 ?></div>
            <div class="kpi-sub">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:var(--success);"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              <?= $analytics['counseling_approved'] ?? 0 ?> approved
            </div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/marriage') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Marriage</span>
              <div class="kpi-icon" style="background:rgba(199,154,43,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--accent);"><?= $analytics['marriage_total'] ?? 0 ?></div>
            <div class="kpi-sub"><?= $analytics['marriage_approved'] ?? 0 ?> approved</div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/conversion') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Converts</span>
              <div class="kpi-icon" style="background:rgba(30,144,255,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#1E90FF;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:#1E90FF;"><?= $analytics['conversion_total'] ?? 0 ?></div>
            <div class="kpi-sub"><?= $analytics['conversion_approved'] ?? 0 ?> certified</div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/education') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Students</span>
              <div class="kpi-icon" style="background:rgba(47,138,96,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--success);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--success);"><?= $analytics['student_count'] ?? 0 ?></div>
            <div class="kpi-sub">
              <?= $analytics['student_active'] ?? 0 ?> active
            </div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/male/death-certificates') ?>'" style="cursor:pointer;">
            <div class="kpi-header">
              <span class="kpi-label">Death Certs</span>
              <div class="kpi-icon" style="background:rgba(20,83,45,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#14532d;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:#14532d;"><?= $analytics['death_cert_total'] ?? 0 ?></div>
            <div class="kpi-sub" style="color:<?= ($analytics['death_cert_new'] ?? 0) > 0 ? '#b91c1c' : 'var(--text-muted)' ?>; font-weight:700;">
              <?= $analytics['death_cert_new'] ?? 0 ?> new received
            </div>
          </div>

          <div class="dash-kpi">
            <div class="kpi-header">
              <span class="kpi-label">Pending</span>
              <div class="kpi-icon" style="background:rgba(139,46,46,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--danger);"><?= $analytics['pending'] ?? 0 ?></div>
            <div class="kpi-sub">Awaiting action</div>
          </div>
        </div>

        <!-- Quick Access Links -->
        <div class="quick-links" style="grid-template-columns: repeat(6, 1fr);">
          <a href="<?= url('/admin/dawah/counseling') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(23,107,69,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div>
              <div class="ql-title">Counseling</div>
              <div class="ql-desc">Manage sessions</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/marriage') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(139,46,46,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--danger);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
            <div>
              <div class="ql-title">Marriage</div>
              <div class="ql-desc">Applications</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/conversion') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(30,144,255,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:#1E90FF;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
            </div>
            <div>
              <div class="ql-title">Conversion</div>
              <div class="ql-desc">Certificates</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/schedule') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(199,154,43,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--accent);"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
            </div>
            <div>
              <div class="ql-title">Schedule</div>
              <div class="ql-desc">Calendar</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/education') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(31,111,90,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--info);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div>
              <div class="ql-title">Education</div>
              <div class="ql-desc">Students</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="quick-link" style="border-color:#bbf7d0;">
            <div class="ql-icon" style="background:#e8f5ed; color:#14532d;">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <div>
              <div class="ql-title" style="color:#14532d;">Death Certs</div>
              <div class="ql-desc">From Damayan</div>
            </div>
          </a>
        </div>

        <!-- Service Request Table with Pill Tabs -->
        <div class="section-card">
          <div class="tab-group">
            <button class="tab-pill active" onclick="switchTab('all', this)">All Requests</button>
            <button class="tab-pill" onclick="switchTab('counseling', this)">Counseling</button>
            <button class="tab-pill" onclick="switchTab('marriage', this)">Marriage</button>
            <button class="tab-pill" onclick="switchTab('conversion', this)">Conversion</button>
          </div>
          <div class="section-card-header" style="border-top:none; padding-top:8px;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
              Service Request Overview
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Service Type</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="request-tbody">
                  <!-- Rendered by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Section: Death Certificates Received from Damayan Table -->
        <div class="section-card" style="margin-top:28px;">
          <div class="section-card-header" style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div style="width:36px; height:36px; border-radius:10px; background:#e8f5ed; color:#14532d; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              </div>
              <div>
                <h6 style="color:#14532d; margin:0; font-size:1.02rem; font-weight:800;">
                  Death Certificates Received from Damayan Department
                </h6>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                  Official transmittal files &amp; deceased vital records forwarded for Salatul Janazah and Islamic burial coordination
                </div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="btn-topbar primary" style="font-size:0.78rem; padding:6px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <span>View Full Registry (<?= (int)($dawahDeathCertsCount ?? 0) ?>)</span>
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
              </a>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Registration #</th>
                    <th>Deceased Information</th>
                    <th>Date &amp; Time of Death</th>
                    <th>Cert Date / Received</th>
                    <th>Informant / Representative</th>
                    <th>Status</th>
                    <th style="text-align:center; min-width:220px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($dawahDeathCerts)): ?>
                    <tr>
                      <td colspan="7" style="text-align:center; padding:40px 20px; color:var(--text-muted);">
                        <div style="font-weight:700; color:var(--text-main); font-size:0.95rem; margin-bottom:4px;">No Death Certificates Received Yet</div>
                        <div style="font-size:0.8rem;">Transmittals sent from the Damayan Department will automatically appear here.</div>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($dawahDeathCerts as $dc): ?>
                      <tr>
                        <td class="td-id">
                          <span style="font-weight:800; color:#14532d;">#<?= htmlspecialchars($dc['registration_number']) ?></span>
                          <div style="font-size:0.68rem; color:var(--text-muted); margin-top:2px;">Ref #<?= (int)$dc['id'] ?></div>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main); font-size:0.9rem;"><?= htmlspecialchars($dc['deceased_name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                            <span style="font-weight:700;"><?= htmlspecialchars($dc['sex'] ?? 'Male') ?></span>
                            <?= !empty($dc['age']) ? ' &bull; ' . htmlspecialchars($dc['age']) . ' yrs old' : '' ?>
                            <?= !empty($dc['civil_status']) ? ' &bull; ' . htmlspecialchars($dc['civil_status']) : '' ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-weight:700; color:#b91c1c; font-size:0.85rem;">
                            <?= date('M d, Y', strtotime($dc['date_of_death'])) ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            <?= !empty($dc['time_of_death']) ? date('g:i A', strtotime($dc['time_of_death'])) : 'Time unrecorded' ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-size:0.83rem; font-weight:700; color:var(--text-main);">
                            <?= !empty($dc['death_cert_date']) ? date('M d, Y', strtotime($dc['death_cert_date'])) : '—' ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            Received: <?= date('M d, Y', strtotime($dc['sent_at'])) ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-weight:600; color:var(--text-main); font-size:0.83rem;">
                            <?= htmlspecialchars($dc['informant_name'] ?? 'Family Representative') ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            <?= !empty($dc['informant_relationship']) ? htmlspecialchars($dc['informant_relationship']) : 'Representative' ?>
                          </div>
                        </td>
                        <td>
                          <?php 
                            $st = $dc['status'] ?? 'Received';
                            $badgeClass = ($st === 'Completed') ? 'badge-completed' : (($st === 'Reviewed') ? 'badge-reviewed' : 'badge-received');
                          ?>
                          <span class="badge-status <?= $badgeClass ?>" style="font-size:0.72rem; padding:3px 8px;">
                            <?= htmlspecialchars($st) ?>
                          </span>
                        </td>
                        <td style="text-align:center;">
                          <div style="display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">
                            <?php if ($st === 'Received'): ?>
                              <button type="button" class="btn-action btn-view" style="background:#ecfdf5; color:#065f46; border-color:#a7f3d0; font-weight:700; padding:5px 9px;" onclick="quickAcknowledge(<?= (int)$dc['id'] ?>, 'Reviewed')" title="Mark as Received & Reviewed">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Receive
                              </button>
                            <?php endif; ?>
                            <button type="button" class="btn-action btn-view" style="padding:5px 9px;" onclick="openDawahCertModal(<?= (int)$dc['id'] ?>)" title="View Certificate Details">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg> View
                            </button>
                            <a href="<?= url('/admin/dawah/death-certificates/file?id=' . $dc['id']) ?>" target="_blank" class="btn-action btn-view" style="background:#14532d; color:#fff; border-color:#14532d; font-weight:700; padding:5px 9px; text-decoration:none;" title="Print Official Philippine Death Certificate (Form 103)">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Cert
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

  <?php include __DIR__ . '/dawah_cert_detail_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
    
    const requests = <?= json_encode($requests ?? []) ?>;
    
    function renderTable(filter = 'all') {
      const tbody = document.getElementById('request-tbody');
      let filtered = requests;
      if(filter !== 'all') filtered = filtered.filter(r => r.type === filter);
      
      if(filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:50px;color:var(--text-muted);">
          <div style="font-size:2rem;margin-bottom:8px;">📋</div>
          <div style="font-weight:600;">No records found</div>
          <div style="font-size:0.8rem;margin-top:4px;">Requests will appear here once submitted</div>
        </td></tr>`;
        return;
      }
      
      tbody.innerHTML = filtered.map(r => {
          const sc = r.status.toLowerCase().includes('pend') ? 'badge-pending' : (r.status.toLowerCase().includes('approv') ? 'badge-approved' : 'badge-rejected');
          let viewUrl = '<?= url('/admin/dawah/counseling') ?>';
          if(r.type === 'marriage') viewUrl = '<?= url('/admin/dawah/marriage') ?>';
          if(r.type === 'conversion') viewUrl = '<?= url('/admin/dawah/conversion') ?>';

          return `
            <tr>
              <td class="td-id">#${r.id}</td>
              <td style="font-weight:600;">${r.name}</td>
              <td>${r.service_label}</td>
              <td>${r.date}</td>
              <td><span class="badge-status ${sc}">${r.status}</span></td>
              <td>
                <div class="actions-cell">
                    <a href="${viewUrl}" class="btn-action btn-view" style="text-decoration:none;">
                        <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        Manage
                    </a>
                </div>
              </td>
            </tr>
          `;
      }).join('');
    }
    
    function switchTab(type, btn) {
      document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderTable(type);
    }
    
    renderTable();

    // Death Certificates Modal & Quick Actions for Male Da'wah
    async function openDawahCertModal(id) {
      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/detail') ?>?id=' + encodeURIComponent(id));
        const data = await res.json();
        if (!data.success || !data.record) {
          alert(data.error || 'Failed to load certificate record.');
          return;
        }

        const r = data.record;
        document.getElementById('ack-id').value = r.id;
        document.getElementById('m-reg-no').textContent = '#' + r.registration_number;
        document.getElementById('m-deceased-name').textContent = r.deceased_name;
        
        let sub = `${r.sex || 'Male'}`;
        if (r.age) sub += ` • ${r.age} yrs old`;
        if (r.civil_status) sub += ` • ${r.civil_status}`;
        document.getElementById('m-vital-sub').textContent = sub;

        const badge = document.getElementById('m-status-badge');
        badge.textContent = r.status;
        badge.className = 'badge-status ' + (r.status === 'Completed' ? 'badge-completed' : (r.status === 'Reviewed' ? 'badge-reviewed' : 'badge-received'));

        document.getElementById('m-reg').textContent = '#' + r.registration_number;
        document.getElementById('m-cert-date').textContent = r.death_cert_date || '—';
        document.getElementById('m-sent-at').textContent = r.sent_at || '—';
        document.getElementById('m-civil').textContent = r.civil_status || '—';
        document.getElementById('m-religion').textContent = r.religion || 'Islam';
        document.getElementById('m-citizenship').textContent = r.citizenship || 'Filipino';

        document.getElementById('m-dod').textContent = r.date_of_death;
        document.getElementById('m-tod').textContent = r.time_of_death || '—';
        document.getElementById('m-dob').textContent = r.date_of_birth || '—';
        document.getElementById('m-age').textContent = r.age || '—';
        document.getElementById('m-occupation').textContent = r.occupation || '—';

        document.getElementById('m-pod').textContent = r.place_of_death || 'City of Dasmariñas, Cavite';
        document.getElementById('m-residence').textContent = r.residence || 'City of Dasmariñas, Cavite';
        
        let inf = r.informant_name || 'Family Representative';
        if (r.informant_relationship) inf += ` (${r.informant_relationship})`;
        if (r.informant_contact) inf += ` • ${r.informant_contact}`;
        document.getElementById('m-informant').textContent = inf;

        document.getElementById('m-sent-by').textContent = r.sent_by_name || 'Damayan Staff';
        document.getElementById('m-purpose').textContent = r.purpose || 'Janazah & Religious Records';
        document.getElementById('m-notes').textContent = r.transmission_notes || 'None provided.';

        document.getElementById('ack-status').value = r.status || 'Received';
        document.getElementById('ack-notes').value = '';

        document.getElementById('btn-open-file-doc').href = '<?= url('/admin/dawah/death-certificates/file?id=') ?>' + r.id;

        document.getElementById('dawahCertModal').style.display = 'flex';
      } catch (err) {
        console.error(err);
        alert('Failed to load certificate record.');
      }
    }

    function closeDawahCertModal() {
      document.getElementById('dawahCertModal').style.display = 'none';
    }

    async function quickAcknowledge(id, status) {
      if (!confirm('Acknowledge and mark this Death Certificate as received & reviewed?')) return;
      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/acknowledge') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status, notes: 'Quick acknowledged by Male Da\'wah Admin.' })
        });
        const data = await res.json();
        if (data.success) {
          location.reload();
        } else {
          alert(data.error || 'Failed to acknowledge.');
        }
      } catch (err) {
        console.error(err);
        alert('Failed to acknowledge certificate.');
      }
    }

    async function submitDawahStatus() {
      const id = document.getElementById('ack-id').value;
      const status = document.getElementById('ack-status').value;
      const notes = document.getElementById('ack-notes').value.trim();
      const btn = document.getElementById('ack-btn');

      if (!id) return;
      btn.disabled = true;
      btn.textContent = 'Saving...';

      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/acknowledge') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status, notes })
        });
        const data = await res.json();
        if (data.success) {
          alert('Status updated successfully.');
          location.reload();
        } else {
          alert(data.error || 'Failed to update status.');
          btn.disabled = false;
          btn.textContent = 'Save Da\'wah Status Update';
        }
      } catch (e) {
        console.error(e);
        alert('Error saving status update.');
        btn.disabled = false;
        btn.textContent = 'Save Da\'wah Status Update';
      }
    }

    document.getElementById('dawahCertModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeDawahCertModal();
    });
  </script>
</body>
</html>
