<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once BASE_PATH . '/app/models/AdminNotification.php';
$model = new AdminNotification();
$rows = $model->getAllForRole('Staff_Female', 20);
echo "Staff_Female total notifs: " . count($rows) . PHP_EOL;
foreach ($rows as $r) {
    echo "ID: {$r['id']} | Type: {$r['type']} | is_read: {$r['is_read']} | Title: {$r['title']}" . PHP_EOL;
}
echo "Unread count for Staff_Female: " . $model->getUnreadCountForRole('Staff_Female') . PHP_EOL;

// Check all admin_notifications table
require_once BASE_PATH . '/app/models/Database.php';
$db = (new Database())->connect();
$stmt = $db->query("SELECT id, type, title, is_read, created_at FROM admin_notifications ORDER BY id DESC LIMIT 15");
echo "\n--- Last 15 rows in admin_notifications ---" . PHP_EOL;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Type: {$row['type']} | is_read: {$row['is_read']} | Title: {$row['title']}" . PHP_EOL;
}
