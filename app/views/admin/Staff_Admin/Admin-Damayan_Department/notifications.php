<?php
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
$active_page = 'notifications';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
</head>
<body>
  <div class="app-wrapper">
    <?php 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Damayan Notifications</div>
          <div class="top-bar-subtitle">System alerts, burial requests, certificate tracking, and charity programs</div>
        </div>
        <div style="display:flex; gap:10px;">
          <a href="<?= url('/admin/damayan') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar" style="margin-bottom: 16px;">
          <a href="<?= url('/admin/damayan') ?>">Damayan</a><span class="sep">›</span><span class="current">Notifications</span>
        </div>

        <?php 
          $context = 'damayan';
          include BASE_PATH . '/app/views/components/notification_module.php'; 
        ?>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('staff');
  </script>
</body>
</html>
