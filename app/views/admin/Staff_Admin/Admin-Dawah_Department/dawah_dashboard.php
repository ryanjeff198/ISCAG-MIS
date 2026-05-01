<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Manager</title>
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
      cursor: pointer;
    }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--primary); }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }
    
    .tab-nav { display: flex; gap: 12px; border-bottom: 2px solid var(--border); margin-bottom: 24px; }
    .tab-btn { padding: 12px 24px; font-size: 0.9rem; font-weight: 700; color: var(--text-muted); background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.25s ease; }
    .tab-btn.active { color: var(--primary) !important; border-bottom-color: var(--primary) !important; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'dashboard';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Da'wah Management</div>
          <div class="top-bar-subtitle">Manage religious services, counseling, and education</div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Da'wah Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Counseling Requests</div>
            <div class="insight-value" id="stat-counseling">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Marriage Applications</div>
            <div class="insight-value" id="stat-marriage">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Conversion Requests</div>
            <div class="insight-value" id="stat-conversion">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Total</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('all')">Recent Requests</button>
          <button class="tab-btn" onclick="switchTab('counseling')">Counseling</button>
          <button class="tab-btn" onclick="switchTab('marriage')">Marriage</button>
          <button class="tab-btn" onclick="switchTab('conversion')">Conversion</button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>Service Request Overview</h6>
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
    // Simulated data for now, would come from database in controller
    const requests = <?= json_encode($requests ?? []) ?>;
    
    function renderTable(filter = 'all') {
      const tbody = document.getElementById('request-tbody');
      let filtered = requests;
      if(filter !== 'all') filtered = requests.filter(r => r.type === filter);
      
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
            <button class="btn-action" style="color:var(--primary);">View Details</button>
          </td>
        </tr>
      `).join('');
    }
    
    function switchTab(type) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.target.classList.add('active');
      renderTable(type);
    }
    
    // Initial stats
    document.getElementById('stat-counseling').textContent = requests.filter(r => r.type === 'counseling').length;
    document.getElementById('stat-marriage').textContent = requests.filter(r => r.type === 'marriage').length;
    document.getElementById('stat-conversion').textContent = requests.filter(r => r.type === 'conversion').length;
    document.getElementById('stat-pending').textContent = requests.filter(r => r.status === 'Pending').length;
    
    renderTable();
  </script>
</body>
</html>
