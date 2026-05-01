<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Da'wah Manager</title>
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
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: #14532d; }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: #14532d; line-height: 1; }
    
    .tab-nav { display: flex; gap: 12px; border-bottom: 2px solid var(--border); margin-bottom: 24px; }
    .tab-btn { padding: 12px 24px; font-size: 0.9rem; font-weight: 700; color: var(--text-muted); background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.25s ease; }
    .tab-btn.active { color: #14532d !important; border-bottom-color: #14532d !important; }
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
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #14532d;">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Welcome, <?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Male Da\'wah Manager' ?></div>
            <div class="top-bar-subtitle">Managing religious services and educational programs</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Male Da'wah Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/counseling') ?>'">
            <div class="insight-label">Male Counseling</div>
            <div class="insight-value" id="stat-counseling">0</div>
          </div>
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/marriage') ?>'">
            <div class="insight-label">Marriage Files</div>
            <div class="insight-value" id="stat-marriage">0</div>
          </div>
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/education') ?>'">
            <div class="insight-label">Shahada Records</div>
            <div class="insight-value" id="stat-conversion">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Action Required</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('all')">All Requests</button>
          <button class="tab-btn" onclick="switchTab('counseling')">Counseling</button>
          <button class="tab-btn" onclick="switchTab('marriage')">Marriage</button>
          <button class="tab-btn" onclick="switchTab('shahada')">Shahada</button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6>Service Request Overview (Male)</h6>
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
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
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
            <button class="btn-action" style="color:#14532d;">Process Request</button>
          </td>
        </tr>
      `).join('');
    }
    
    function switchTab(type) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.target.classList.add('active');
      renderTable(type);
    }
    
    const analytics = <?= json_encode($analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0]) ?>;
    document.getElementById('stat-counseling').textContent = analytics.total || 0;
    document.getElementById('stat-pending').textContent = analytics.pending || 0;
    document.getElementById('stat-approved').textContent = analytics.approved || 0;
    
    renderTable();
  </script>
</body>
</html>
