<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Islamic Education</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
        --male-accent: #14532d;
        --male-dark: #064e3b;
        --male-light: #f0fdf4;
    }
    .top-bar-title { color: var(--male-dark); }
    .breadcrumb-bar .current { color: var(--male-accent); }
    .btn-action { color: var(--male-accent); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'education';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--male-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--male-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Male Islamic Education Records</div>
            <div class="top-bar-subtitle">Manage student enrollments and program progress</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Education Records</span>
        </div>

        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
          <button class="btn-action" style="background: var(--male-accent); color: white; padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(20, 83, 45, 0.25);">
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Student
          </button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--male-dark); margin: 0;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Student Enrollment List (Male)
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
                          <button class="btn-action" style="color: var(--male-accent);">Manage</button>
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
