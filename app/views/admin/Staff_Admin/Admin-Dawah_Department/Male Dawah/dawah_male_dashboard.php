<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Da'wah Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .insight-card:hover { border-color: var(--accent); }
    .tab-btn.active { color: var(--primary) !important; border-bottom-color: var(--primary) !important; }
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
          <div class="top-bar-subtitle">Male Da'wah Department — Managing religious services and educational programs</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar" onclick="location.href='<?= url('/admin/dawah/male/analytics') ?>'">📊 Dept Analytics</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Male Da'wah Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/male/counseling') ?>'">
            <div class="insight-label">Counseling Requests</div>
            <div class="insight-value" style="color:var(--primary);" id="stat-counseling">0</div>
          </div>
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/male/marriage') ?>'">
            <div class="insight-label">Marriage Applications</div>
            <div class="insight-value" style="color:var(--accent);" id="stat-marriage">0</div>
          </div>
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/male/education') ?>'">
            <div class="insight-label">Student Records</div>
            <div class="insight-value" style="color:var(--info);" id="stat-conversion">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value" style="color:var(--danger);" id="stat-pending">0</div>
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
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
              Service Request Overview (Male)
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
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No records found for this category.</td></tr>';
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
                        View Details
                    </button>
                </div>
              </td>
            </tr>
          `;
      }).join('');
    }
    
    function switchTab(type) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.target.classList.add('active');
      renderTable(type);
    }
    
    const analytics = <?= json_encode($analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0]) ?>;
    document.getElementById('stat-counseling').textContent = analytics.counseling_total || 0;
    document.getElementById('stat-marriage').textContent = analytics.marriage_total || 0;
    document.getElementById('stat-pending').textContent = analytics.pending || 0;
    document.getElementById('stat-conversion').textContent = analytics.student_count || 0;
    
    renderTable();
  </script>
</body>
</html>
