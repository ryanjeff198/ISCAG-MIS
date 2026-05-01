<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — <?= $dawah_type == 'female' ? 'Female' : 'Male' ?> Islamic Education</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
        --active-teal: #0d9488;
        --active-blue: #0284c7;
        --current-accent: <?= $dawah_type == 'female' ? 'var(--active-teal)' : 'var(--active-blue)' ?>;
        --current-dark: <?= $dawah_type == 'female' ? '#134e4a' : '#0c4a6e' ?>;
        --current-light: <?= $dawah_type == 'female' ? '#f0fdfa' : '#f0f9ff' ?>;
    }
    .top-bar-title { color: var(--current-dark); }
    .breadcrumb-bar .current { color: var(--current-accent); }
    .btn-action { color: var(--current-accent); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'education';
      // $dawah_type is already passed from controller
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--current-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--current-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Islamic Education Records (<?= ucfirst($dawah_type) ?>)</div>
            <div class="top-bar-subtitle">Manage student enrollments and program progress</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url($dawah_type == 'female' ? '/admin/dawah/female' : '/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Education Records</span>
        </div>

        <div class="section-card">
          <div class="section-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h6 style="color: var(--current-dark); margin: 0;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--current-accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Student Enrollment List
            </h6>
            <button class="btn-action" style="background: var(--current-accent); color: white; padding: 8px 16px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">
              Add New Student
            </button>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Enrollment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($records)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No student records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($records as $r): 
                      $statusClasses = [
                        'pending'   => 'pending',
                        'active'    => 'warning',
                        'completed' => 'success',
                        'dropped'   => 'danger'
                      ];
                      $sc = $statusClasses[$r['status']] ?? 'pending';
                    ?>
                      <tr>
                        <td class="td-id">#<?= $r['id'] ?></td>
                        <td style="font-weight:600;"><?= trim($r['first_name'] . ' ' . $r['last_name']) ?></td>
                        <td><?= $r['program_name'] ?></td>
                        <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        <td><span class="badge-status <?= $sc ?>"><?= ucfirst($r['status']) ?></span></td>
                        <td>
                          <button class="btn-action">Manage</button>
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
  </script>
</body>
</html>
