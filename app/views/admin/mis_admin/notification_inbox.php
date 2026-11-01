<?php 
$active_page = 'notification'; 
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Admin Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title" id="page-title">Admin Notifications</div>
            <div class="top-bar-subtitle">Real-time system alerts from tenant payments, applications, and renewals</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Unread Alerts</div>
            <div class="insight-value danger" id="stat-unread-val"><?= $unreadCount ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Notifications</div>
            <div class="insight-value info"><?= count($notifications) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">System Health</div>
            <div class="insight-value success">EXCELLENT</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Last Activity</div>
            <div class="insight-value info"><?= !empty($notifications) ? date('M j, g:ia', strtotime($notifications[0]['created_at'])) : 'N/A' ?></div>
          </div>
        </div>

        <?php 
          $context = 'admin';
          include BASE_PATH . '/app/views/components/notification_module.php'; 
        ?>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('admin');
    if (typeof loadUserNav === 'function') loadUserNav();
  </script>
</body>
</html>
