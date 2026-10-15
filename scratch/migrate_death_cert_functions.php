<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDbConnection();
    echo "Connected to database.\n";

    $existingCols = $db->query("SHOW COLUMNS FROM burial_requests")->fetchAll(PDO::FETCH_COLUMN);

    $needed = [
        'cert_action_type' => "VARCHAR(50) DEFAULT 'register'",
        'existing_cert_no' => "VARCHAR(100) NULL",
        'copy_reason' => "TEXT NULL",
        'num_copies' => "INT DEFAULT 1",
        'existing_record_verified' => "TINYINT(1) DEFAULT 0",
        'copy_issued_at' => "DATETIME NULL",
        'copy_received_at' => "DATETIME NULL",
        'copy_received_confirmed' => "TINYINT(1) DEFAULT 0"
    ];

    foreach ($needed as $col => $def) {
        if (!in_array($col, $existingCols)) {
            $db->exec("ALTER TABLE burial_requests ADD COLUMN `{$col}` {$def}");
            echo "Added column {$col} to burial_requests.\n";
        } else {
            echo "Column {$col} already exists.\n";
        }
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
