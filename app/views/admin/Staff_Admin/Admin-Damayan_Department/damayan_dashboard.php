<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
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
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--danger); }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--danger); line-height: 1; }
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
        <div>
          <div class="top-bar-title">Damayan Management</div>
          <div class="top-bar-subtitle">Manage burial services and funeral assistance</div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Damayan Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Burial Requests</div>
            <div class="insight-value" id="stat-burial">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed Services</div>
            <div class="insight-value success" id="stat-completed">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Assistance</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>Burial Service Requests</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Deceased Name</th>
                    <th>Requester</th>
                    <th>Burial Date</th>
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
    const records = <?= json_encode($records ?? []) ?>;
    
    function renderTable() {
      const tbody = document.getElementById('burial-tbody');
      if(records.length === 0) return;
      
      tbody.innerHTML = records.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.deceased_name}</td>
          <td>${r.requester_name}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:var(--danger);">Manage</button>
          </td>
        </tr>
      `).join('');
    }
    
    // Initial stats
    document.getElementById('stat-burial').textContent = records.length;
    document.getElementById('stat-pending').textContent = records.filter(r => r.status === 'Pending').length;
    document.getElementById('stat-completed').textContent = records.filter(r => r.status === 'Completed').length;
    
    renderTable();
  </script>
</body>
</html>
