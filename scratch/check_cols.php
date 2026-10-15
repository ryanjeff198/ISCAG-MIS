<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$stmt = $db->query("SHOW COLUMNS FROM burial_requests");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Columns in burial_requests:\n";
foreach (['cert_action_type', 'existing_cert_no', 'copy_reason', 'num_copies', 'existing_record_verified', 'copy_issued_at', 'copy_received_at', 'copy_received_confirmed'] as $col) {
    echo "$col: " . (in_array($col, $cols) ? "EXISTS" : "MISSING") . "\n";
}
