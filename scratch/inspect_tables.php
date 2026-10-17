<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "TABLES:\n" . implode("\n", $tables) . "\n\n";

foreach (['burial_requests', 'funeral_cases', 'deceased_records', 'users', 'notifications', 'admin_notifications'] as $tbl) {
    if (in_array($tbl, $tables)) {
        echo "=== $tbl ===\n";
        $cols = $db->query("DESCRIBE $tbl")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo "  {$col['Field']} - {$col['Type']} - Null:{$col['Null']} - Key:{$col['Key']} - Default:{$col['Default']}\n";
        }
    } else {
        echo "=== $tbl NOT FOUND ===\n";
    }
}
