<?php
$maintConfig = [
    'sidebar' => '/app/views/components/mis_admin_sidebar.php',
    'dashboardUrl' => '/admin/dashboard',
    'routeBase' => '/admin/mis_admin/maintenance',
    'reportUrl' => '/admin/mis_admin/reports?dept=Maintenance',
    'standardizeRole' => 'admin',
    'subtitle' => 'Manage facility repairs, tenant service requests, and maintenance reports',
];
include BASE_PATH . '/app/views/components/maintenance_module.php';
