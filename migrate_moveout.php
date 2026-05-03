<?php
require_once 'config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE move_out_requests ADD COLUMN move_out_date DATE AFTER unit_id");
    echo "Successfully added move_out_date column.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
