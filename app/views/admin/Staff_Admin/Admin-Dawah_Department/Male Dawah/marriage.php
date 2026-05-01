<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Marriage Services Management</title>
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
      $active_page = 'marriage';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--male-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--male-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Marriage Services</div>
            <div class="top-bar-subtitle">Manage marriage applications, nikah scheduling, and documentation</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Marriage Records</span>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--male-dark); margin: 0;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);margin-right:8px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Marriage Service Applications
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicants (Groom & Bride)</th>
                    <th>Preferred Date</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($records)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No marriage records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($records as $r): 
                      $sc = ($r['status'] === 'approved') ? 'success' : (($r['status'] === 'rejected') ? 'danger' : 'pending');
                    ?>
                      <tr>
                        <td class="td-id">#<?= $r['id'] ?></td>
                        <td style="font-weight:600;"><?= trim($r['groom_name'] . ' & ' . $r['bride_name']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['preferred_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        <td><span class="badge-status <?= $sc ?>"><?= ucfirst($r['status']) ?></span></td>
                        <td>
                          <button class="btn-action" style="color: var(--male-accent);">View File</button>
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
