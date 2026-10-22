<?php
$modal = file_get_contents(__DIR__ . '/../app/views/admin/Staff_Admin/Admin-Damayan_Department/burial_modal.php');
if (strpos($modal, 'sched-history-table-body') === false) {
    throw new Exception('Modal table body missing');
}
if (strpos($modal, 'sched-history-list') !== false) {
    throw new Exception('Legacy sched-history-list still present');
}

$details = file_get_contents(__DIR__ . '/../app/views/user/Damayan/user_burial-details.php');
if (strpos($details, 'appointment-history-container') === false) {
    throw new Exception('Details appointment table container missing');
}
if (strpos($details, '<table style="width:100%; border-collapse:collapse; font-size:0.8rem;') === false) {
    throw new Exception('Details table missing');
}

$dash = file_get_contents(__DIR__ . '/../app/views/user/Damayan/user_burial-dashboard.php');
if (strpos($dash, 'Claiming Appointment History') === false) {
    throw new Exception('Dashboard appointment header missing');
}
if (strpos($dash, '<table style="width:100%; border-collapse:collapse; font-size:0.78rem;') === false) {
    throw new Exception('Dashboard table missing');
}

// Verify array access on mock row without release_location does not emit warnings
error_reporting(E_ALL);
$sh = [
    'action_type' => 'SCHEDULED',
    'scheduled_date' => '2026-10-30',
    'scheduled_time' => '10:00:00',
    'location' => 'Damayan Department',
    'reason' => 'Test reason',
    'admin_name' => 'Admin',
    'created_at' => '2026-10-22 18:00:00'
];
$loc = ($sh['release_location'] ?? $sh['location'] ?? '') ?: 'Damayan Department';
if ($loc !== 'Damayan Department') {
    throw new Exception('Location mismatch');
}

echo "ALL APPOINTMENT HISTORY UI ASSERTIONS PASSED!\n";
