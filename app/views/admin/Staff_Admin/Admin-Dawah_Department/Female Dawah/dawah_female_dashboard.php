<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Da'wah Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: #d4af37; }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: #713f12; line-height: 1; }

    /* Female Department Branding - Premium Gold */
    :root {
        --female-accent: #d4af37;
        --female-light: #fefce8;
    }
    .top-bar-title { color: #713f12; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'dashboard';
      $dawah_type = 'female'; 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--female-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--female-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Welcome, <?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Female Da\'wah Manager' ?></div>
            <div class="top-bar-subtitle">Managing Islamic education and community programs</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current" style="color: var(--female-accent);">Female Da'wah — Islamic Studies Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Enrollees</div>
            <div class="insight-value" id="stat-total">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Active Classes</div>
            <div class="insight-value" id="stat-classes">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed Programs</div>
            <div class="insight-value" id="stat-completed">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Enrollment</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: #713f12;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#d4af37;margin-right:8px;"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
              Islamic Studies — Student Records
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Date Enrolled</th>
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
    
    function renderTable() {
      const tbody = document.getElementById('request-tbody');
      if(requests.length === 0) return;
      
      tbody.innerHTML = requests.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.name}</td>
          <td>${r.service_label}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:#d4af37;">View Details</button>
          </td>
        </tr>
      `).join('');
    }
    
    const analytics = <?= json_encode($analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0]) ?>;
    document.getElementById('stat-total').textContent = analytics.total || 0;
    document.getElementById('stat-pending').textContent = analytics.pending || 0;
    document.getElementById('stat-completed').textContent = analytics.approved || 0;
    
    renderTable();
  </script>
</body>
</html>
