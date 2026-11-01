<?php
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
$active_page = 'notifications';
$dawah_type = 'male';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Da'wah Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
</head>

<body>
  <div class="app-wrapper">
    <?php 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>

    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" id="page-title">Male Da'wah Notifications</div>
          <div class="top-bar-subtitle">System alerts, counseling appointments, marriage services, and conversion requests</div>
        </div>
        <div style="display:flex; gap:10px;">
          <a href="<?= url('/admin/dawah/male') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar" style="margin-bottom: 16px;">
          <a href="<?= url('/admin/dawah/male') ?>">Male Da'wah</a><span class="sep">›</span><span class="current">Notifications</span>
        </div>

        <?php 
          $context = 'dawah';
          include BASE_PATH . '/app/views/components/notification_module.php'; 
        ?>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? '';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Staff_Male';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");
  </script>
</body>
</html>
