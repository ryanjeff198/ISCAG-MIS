<?php
define('BASE_PATH', __DIR__);
$source = file_get_contents(BASE_PATH . '/app/views/admin/mis_admin/admin_parking_approval.php');
$source = str_replace('components/mis_admin_sidebar.php', 'admin/Staff_Admin/Admin-Apartment_Department/sidebar.php', $source);
$source = str_replace('/admin/mis_admin/parking/approve', '/admin/apartment/parking/approve', $source);
$source = str_replace('/admin/mis_admin/parking/reject', '/admin/apartment/parking/reject', $source);
file_put_contents(BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/parking_info.php', $source);
echo 'Done';