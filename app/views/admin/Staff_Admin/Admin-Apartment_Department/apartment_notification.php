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
  <title>ISCAG MIS — Apartment Staff Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
</head>

<body>
  <div class="app-wrapper">
    <?php 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" id="page-title">Apartment Notifications</div>
          <div class="top-bar-subtitle">System alerts, tenant applications, contract renewals, and payment receipts</div>
        </div>
        <div style="display:flex; gap:10px;">
          <a href="<?= url('/admin/apartment') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar" style="margin-bottom: 16px;">
          <a href="<?= url('/admin/apartment') ?>">Apartment</a><span class="sep">›</span><span class="current">Notifications</span>
        </div>

        <?php 
          $context = 'apartment';
          include BASE_PATH . '/app/views/components/notification_module.php'; 
        ?>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Apartment Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'staff@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Apartment Manager';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");
  </script>
</body>
</html>