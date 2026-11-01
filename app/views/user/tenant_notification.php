<?php
// ISCAG MIS — User Notifications
$notifs = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Notifications</title>
    <!-- Fonts & CSS -->
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>">
</head>
<body>

    <div class="app-wrapper">
        <!-- ═══ SIDEBAR ═══ -->
        <?php $active_page = 'notifications'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="main-content">
            
            <!-- TOP BAR -->
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Notifications</div>
                    <div class="top-bar-subtitle">Stay updated with your account activity, service requests, and announcements</div>
                </div>
            </div>

            <!-- PAGE BODY -->
            <div class="page-body" style="padding-top: 20px;">
                <?php 
                    $context = 'user';
                    include BASE_PATH . '/app/views/components/notification_module.php'; 
                ?>
            </div>
        </main>
    </div>

    <!-- ═══ SCRIPTS ═══ -->
    <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof standardizePage === 'function') {
                standardizePage('tenant');
            }
        });
    </script>
</body>
</html>