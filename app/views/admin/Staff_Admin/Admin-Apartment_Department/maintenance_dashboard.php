<?php
$maintConfig = [
    'sidebar' => '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php',
    'dashboardUrl' => '/admin/apartment',
    'routeBase' => '/admin/apartment/maintenance',
    'reportUrl' => null,
    'standardizeRole' => 'staff',
    'subtitle' => 'Facility repairs, tenant service requests, and maintenance reports',
];
include BASE_PATH . '/app/views/components/maintenance_module.php';
