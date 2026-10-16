<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 24px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      gap: 8px;
      cursor: pointer;
    }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--damayan-accent); }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }

    .tab-nav { display: flex; gap: 12px; border-bottom: 2px solid var(--border); margin-bottom: 24px; }
    .tab-btn { padding: 12px 24px; font-size: 0.9rem; font-weight: 700; color: var(--text-muted); background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.25s ease; }
    .tab-btn.active { color: var(--damayan-accent) !important; border-bottom-color: var(--damayan-accent) !important; }
    .top-bar-title { color: var(--damayan-dark); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'dashboard';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Welcome, <?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Damayan Manager' ?></div>
            <div class="top-bar-subtitle">Managing burial services, charity programs, and community assistance</div>
          </div>
        </div>
        <div class="top-bar-actions" style="display:flex;align-items:center;gap:8px;">
          <button type="button" class="btn-topbar primary" onclick="openEncodeModal()" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Encode Death Certificate
          </button>
          <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="btn-topbar" style="text-decoration:none;font-weight:600;">
            Encoded Archive
          </a>
          <a href="<?= url('/admin/damayan') ?>" class="btn-topbar active" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-accent);">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
          </a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current" style="color: var(--damayan-accent);">Damayan Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card" onclick="location.href='<?= url('/admin/damayan/burial-requests') ?>'">
            <div class="insight-label">Total Cases</div>
            <div class="insight-value" id="stat-total">0</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">All registered applications</div>
          </div>
          <div class="insight-card" onclick="location.href='<?= url('/admin/damayan/burial-requests?filter=death_cert') ?>'">
            <div class="insight-label">Death Certificates</div>
            <div class="insight-value" id="stat-dc-registered" style="color:var(--damayan-accent);">0</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;"><span id="stat-dc-pending">0</span> awaiting / in review</div>
          </div>
          <div class="insight-card" onclick="location.href='<?= url('/admin/damayan/burial') ?>'">
            <div class="insight-label">Available Plots</div>
            <div class="insight-value" id="stat-plots-avail" style="color:#0284c7;">0</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;"><span id="stat-plots-occupied">0</span> plots assigned</div>
          </div>
          <div class="insight-card" onclick="location.href='<?= url('/admin/damayan/burial-requests?filter=pending') ?>'">
            <div class="insight-label">Pending Action</div>
            <div class="insight-value warning" id="stat-pending">0</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Requires staff coordination</div>
          </div>
        </div>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab(this, 'all')">All Cases</button>
          <button class="tab-btn" onclick="switchTab(this, 'death_certificate')">Death Certificates</button>
          <button class="tab-btn" onclick="switchTab(this, 'cemetery_plot')">Cemetery Plots</button>
          <button class="tab-btn" onclick="switchTab(this, 'pending')">Pending Review</button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark); display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              Death Certificate &amp; Cemetery Plot Request Overview
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Deceased Person</th>
                    <th>Applicant / Rep</th>
                    <th>Service Type</th>
                    <th>Death Cert Stage</th>
                    <th>Cemetery Plot</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="request-tbody">
                  <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No records found.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/burial_modal.php'; ?>
  <?php include __DIR__ . '/encode_modal.php'; ?>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
    const records = <?= json_encode($records ?? []) ?>;
    const analytics = <?= json_encode($analytics ?? []) ?>;
    
    function renderTable(filter = 'all') {
      const tbody = document.getElementById('request-tbody');
      let filtered = records;
      
      if(filter === 'death_certificate') {
        filtered = records.filter(r => r.death_cert_requested == 1);
      } else if(filter === 'cemetery_plot') {
        filtered = records.filter(r => r.cemetery_plot_requested == 1);
      } else if(filter === 'pending') {
        filtered = records.filter(r => {
          const s = (r.status || '').toLowerCase();
          const dc = (r.death_cert_status || '').toLowerCase();
          const cp = (r.cemetery_plot_status || '').toLowerCase();
          return s.includes('pending') || s.includes('review') || dc.includes('review') || cp.includes('review') || s.includes('additional');
        });
      }
      
      if(filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No records found for this category.</td></tr>';
        return;
      }
      
      tbody.innerHTML = filtered.map(r => `
        <tr>
          <td class="td-id">
            <span style="font-weight:700; color:var(--damayan-accent);">#${r.id}</span>
            ${r.is_encoded_linked ? `
              <div style="font-size:0.65rem; font-weight:700; color:#065f46; background:#bbf7d0; padding:1px 6px; border-radius:4px; display:inline-block; margin-top:2px;" title="Linked with Encoded Death Certificate #${escapeHtml(r.encoded_reg_no || '')}">
                ✓ Encoded Linked
              </div>
            ` : ''}
          </td>
          <td>
            <div style="font-weight:700; color:var(--text-main);">${escapeHtml(r.deceased)}</div>
            <span class="badge-status" style="font-size:0.68rem; background:#f0fdf4; color:#166534; padding:2px 6px;">${r.deceased_sex || 'Male'}</span>
          </td>
          <td style="font-weight:600;">${escapeHtml(r.name)}</td>
          <td>
            <span class="badge-status badge-info" style="font-size:0.72rem;">${r.service_label}</span>
          </td>
          <td>
            ${r.death_cert_requested ? `
              <span class="badge-status ${getBadgeClass(r.death_cert_status)}">${r.death_cert_status}</span>
              ${r.death_cert_registry_no ? `<div style="font-size:0.7rem; font-weight:700; color:#15803d; margin-top:2px;">Reg: ${escapeHtml(r.death_cert_registry_no)}</div>` : ''}
            ` : '<span style="color:var(--text-muted); font-size:0.8rem; font-style:italic;">Not Requested</span>'}
          </td>
          <td>
            ${r.cemetery_plot_requested ? `
              <span class="badge-status ${getBadgeClass(r.cemetery_plot_status)}">${r.cemetery_plot_status}</span>
              ${r.assigned_plot_number ? `<div style="font-size:0.7rem; font-weight:700; color:var(--damayan-accent); margin-top:2px;">Plot #${escapeHtml(r.assigned_plot_number)}</div>` : ''}
            ` : '<span style="color:var(--text-muted); font-size:0.8rem; font-style:italic;">Not Requested</span>'}
          </td>
          <td style="color:var(--text-muted); font-size:0.82rem;">${r.date}</td>
          <td>
            <button class="btn-action" style="color:var(--damayan-accent);font-weight:700; border:1px solid var(--damayan-accent); border-radius:8px; padding:5px 12px; background:var(--damayan-light);" onclick="openCaseModal('${r.id}')">Manage Case</button>
          </td>
        </tr>
      `).join('');
    }
    
    function switchTab(btn, type) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderTable(type);
    }
    
    function manageBurial(id) {
      openCaseModal(id);
    }

    // Populate Stats
    document.getElementById('stat-total').textContent = analytics.total || analytics.burial || 0;
    document.getElementById('stat-dc-registered').textContent = analytics.death_cert_registered || 0;
    document.getElementById('stat-dc-pending').textContent = analytics.death_cert_pending || 0;
    document.getElementById('stat-plots-avail').textContent = analytics.available_plots || 0;
    document.getElementById('stat-plots-occupied').textContent = analytics.occupied_plots || 0;
    document.getElementById('stat-pending').textContent = analytics.pending || 0;
    
    renderTable();
  </script>
</body>
</html>
