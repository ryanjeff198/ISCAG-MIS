<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Da'wah Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .insight-card:hover { border-color: var(--accent); }
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
        <div class="top-bar-left">
          <div class="top-bar-title">Welcome, <?= htmlspecialchars(explode(' ',trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')))[0]) ?: 'Da\'wah Manager' ?></div>
          <div class="top-bar-subtitle">Female Da'wah Department — Managing religious services and community programs</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar" onclick="location.href='<?= url('/admin/dawah/female/analytics') ?>'">📊 Dept Analytics</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Female Da'wah Dashboard</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/female/education') ?>'">
            <div class="insight-label">Total Students</div>
            <div class="insight-value" style="color:var(--primary);" id="stat-total">0</div>
          </div>
          <div class="insight-card" onclick="window.location.href='<?= url('/admin/dawah/female/counseling') ?>'">
            <div class="insight-label">Counseling Sessions</div>
            <div class="insight-value" style="color:var(--accent);" id="stat-classes">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed Programs</div>
            <div class="insight-value" style="color:var(--success);" id="stat-completed">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value" style="color:var(--danger);" id="stat-pending">0</div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
              Student Records & Recent Activity
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
                <tbody id="student-tbody">
                  <?php if (empty($students)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No recent records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($students as $s): 
                      $sc = 'badge-pending';
                      if($s['status'] === 'active') $sc = 'badge-active';
                      if($s['status'] === 'completed') $sc = 'badge-complete';
                    ?>
                      <tr>
                        <td class="td-id">#<?= $s['id'] ?></td>
                        <td style="font-weight:600;"><?= trim($s['first_name'] . ' ' . $s['last_name']) ?></td>
                        <td><?= $s['program_name'] ?></td>
                        <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                        <td><span class="badge-status <?= $sc ?>"><?= ucfirst($s['status']) ?></span></td>
                        <td>
                          <div class="actions-cell">
                             <button class="btn-action btn-view" onclick="alert('Viewing record...')">
                               <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                               Details
                             </button>
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
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
    
    const stats = <?= json_encode($stats ?? []) ?>;
    document.getElementById('stat-total').textContent = stats.total || 0;
    document.getElementById('stat-classes').textContent = stats.active_classes || 0;
    document.getElementById('stat-completed').textContent = stats.completed || 0;
    document.getElementById('stat-pending').textContent = stats.pending || 0;
  </script>
</body>
</html>
