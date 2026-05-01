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
      --damayan-accent: #dc2626;
      --damayan-dark: #991b1b;
      --damayan-light: #fef2f2;
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
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Welcome, <?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Damayan Manager' ?></div>
            <div class="top-bar-subtitle">Managing burial services, charity programs, and community assistance</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current" style="color: var(--damayan-accent);">Damayan Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Burial Requests</div>
            <div class="insight-value" id="stat-burial">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Charity Programs</div>
            <div class="insight-value" id="stat-charity">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed Services</div>
            <div class="insight-value" id="stat-completed" style="color: #16a34a;">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Assistance</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('all')">All Requests</button>
          <button class="tab-btn" onclick="switchTab('burial')">Burial</button>
          <button class="tab-btn" onclick="switchTab('charity')">Charity</button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Service Request Overview
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Requester</th>
                    <th>Service Type</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="request-tbody">
                  <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No records found.</td></tr>
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
    const records = <?= json_encode($records ?? []) ?>;
    
    function renderTable(filter = 'all') {
      const tbody = document.getElementById('request-tbody');
      let filtered = records;
      if(filter !== 'all') filtered = records.filter(r => r.type === filter);
      
      if(filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No records found for this category.</td></tr>';
        return;
      }
      
      tbody.innerHTML = filtered.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.name}</td>
          <td>${r.service_label}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:#dc2626;">Manage</button>
          </td>
        </tr>
      `).join('');
    }
    
    function switchTab(type) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.target.classList.add('active');
      renderTable(type);
    }
    
    // Stats
    const analytics = <?= json_encode($analytics ?? ['burial' => 0, 'charity' => 0, 'completed' => 0, 'pending' => 0]) ?>;
    document.getElementById('stat-burial').textContent = analytics.burial || 0;
    document.getElementById('stat-charity').textContent = analytics.charity || 0;
    document.getElementById('stat-completed').textContent = analytics.completed || 0;
    document.getElementById('stat-pending').textContent = analytics.pending || 0;
    
    renderTable();
  </script>
</body>
</html>
