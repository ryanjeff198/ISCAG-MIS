<?php
require_once __DIR__ . '/config/database.php';
try {
    $db = getDbConnection();
    // Check if column exists
    $check = $db->query("SHOW COLUMNS FROM tenant_accounts LIKE 'time_offset'");
    if ($check->rowCount() === 0) {
        $db->exec("ALTER TABLE tenant_accounts ADD COLUMN time_offset VARCHAR(50) DEFAULT NULL");
        echo "Migration successful: Column time_offset added to tenant_accounts.\n";
    } else {
        echo "Migration skipped: Column time_offset already exists.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
