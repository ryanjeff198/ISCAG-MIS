<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Records</title>
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
    }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'burial';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Burial Service Management</div>
            <div class="top-bar-subtitle">Processing burial requests, scheduling, and community assistance</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Burial Records</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Requests</div>
            <div class="insight-value" id="stat-total">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Approval</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Approved</div>
            <div class="insight-value success" id="stat-approved">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed</div>
            <div class="insight-value info" id="stat-completed">0</div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Burial Request Registry
            </h6>
            <div class="header-actions">
               <button class="btn-topbar primary" onclick="showAlert('System Notice', 'Adding manual records is currently limited to MIS Admin.', 'info')">+ New Record</button>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Deceased Name</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="burial-tbody">
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
    standardizePage('staff');
    
    // For now use mock data as in MIS Admin
    const records = <?= json_encode($records ?? []) ?>;

    function renderTable() {
      const tbody = document.getElementById('burial-tbody');
      // Show all records for the registry
      if(records.length === 0) return;
      
      tbody.innerHTML = records.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.name}</td>
          <td>${r.deceased}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:var(--text-muted);" onclick="showAlert('Record Info', 'This is a historical record for #${r.id}. View-only mode.', 'info')">Manage</button>
          </td>
        </tr>
      `).join('');

      // Update stats
      document.getElementById('stat-total').textContent = records.length;
      document.getElementById('stat-pending').textContent = records.filter(x => x.status.toLowerCase() === 'pending').length;
      document.getElementById('stat-approved').textContent = records.filter(x => x.status.toLowerCase() === 'approved' || x.status.toLowerCase() === 'verified').length;
      document.getElementById('stat-completed').textContent = records.filter(x => x.status.toLowerCase() === 'completed').length;
    }
    
    renderTable();
  </script>
</body>
</html>
