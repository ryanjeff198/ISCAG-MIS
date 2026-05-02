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
        <div class="dash-kpi-grid">
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
            <div class="kpi-sub">Applications filed</div>
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
              <?= $analytics['student_active'] ?? 0 ?> active · <?= $analytics['student_completed'] ?? 0 ?> graduated
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
        <div class="quick-links">
          <a href="<?= url('/admin/dawah/counseling') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(23,107,69,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div>
              <div class="ql-title">Counseling</div>
              <div class="ql-desc">Manage sessions</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/schedule') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(199,154,43,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--accent);"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
            </div>
            <div>
              <div class="ql-title">Schedule</div>
              <div class="ql-desc">Calendar & events</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/education') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(31,111,90,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--info);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div>
              <div class="ql-title">Education</div>
              <div class="ql-desc">Student records</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/marriage') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(139,46,46,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--danger);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
            <div>
              <div class="ql-title">Marriage</div>
              <div class="ql-desc">Applications & records</div>
            </div>
          </a>
        </div>

        <!-- Service Request Table with Pill Tabs -->
        <div class="section-card">
          <div class="tab-group">
            <button class="tab-pill active" onclick="switchTab('all', this)">All Requests</button>
            <button class="tab-pill" onclick="switchTab('counseling', this)">Counseling</button>
            <button class="tab-pill" onclick="switchTab('marriage', this)">Marriage</button>
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

      </div>
    </div>
  </div>
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
          return `
            <tr>
              <td class="td-id">#${r.id}</td>
              <td style="font-weight:600;">${r.name}</td>
              <td>${r.service_label}</td>
              <td>${r.date}</td>
              <td><span class="badge-status ${sc}">${r.status}</span></td>
              <td>
                <div class="actions-cell">
                    <button class="btn-action btn-view" onclick="alert('Processing request...')">
                        <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        View
                    </button>
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
  </script>
</body>
</html>
