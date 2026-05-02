<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Marriage Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'marriage';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Marriage Records</div>
          <div class="top-bar-subtitle">Male Da'wah Department — Marriage applications and certification tracking</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="alert('Export functionality coming soon')">📥 Export CSV</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Marriage Applications</span>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Brothers' Marriage Applications
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant Name</th>
                    <th>Fiancée Name</th>
                    <th>Date Filed</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($applications)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($applications as $app): 
                      $sc = ($app['status'] === 'approved') ? 'badge-approved' : (($app['status'] === 'rejected') ? 'badge-rejected' : 'badge-pending');
                    ?>
                      <tr>
                        <td class="td-id">#<?= $app['id'] ?></td>
                        <td style="font-weight:600;"><?= $app['applicant_name'] ?></td>
                        <td><?= $app['fiancee_name'] ?></td>
                        <td><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                        <td><span class="badge-status <?= $sc ?>"><?= ucfirst($app['status']) ?></span></td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-action btn-view" onclick="alert('Viewing application details...')">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                              Details
                            </button>
                            <button class="btn-action btn-approve" onclick="alert('Processing approval...')">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Approve
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
  </script>
</body>
</html>
