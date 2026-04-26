<?php
require_once __DIR__ . '/../app/core/Database.php';
try {
    $db = (new Database())->connect();
    $db->exec("ALTER TABLE tenant_parking ADD COLUMN parking_no VARCHAR(50) AFTER tenant_id");
    echo "Column 'parking_no' added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
