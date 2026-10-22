<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$pdo = getDbConnection();

echo "=== burial_requests columns ===\n";
$stmt = $pdo->query("SHOW COLUMNS FROM burial_requests");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (in_array($row['Field'], ['status', 'death_cert_status', 'scheduled_release_date', 'scheduled_release_time', 'release_location', 'release_instructions'])) {
        echo "{$row['Field']} => {$row['Type']}\n";
    }
}

echo "=== sample burial_requests records ===\n";
$stmt = $pdo->query("SELECT id, ref_id, tenant_id, status, death_cert_status FROM burial_requests LIMIT 5");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
