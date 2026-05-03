<?php
require_once 'config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE move_out_requests MODIFY COLUMN status VARCHAR(20) DEFAULT 'Pending'");
    $db->exec("UPDATE move_out_requests SET status = 'Processing' WHERE request_id = 1");
    echo "Fixed table default and updated request ID 1 to Processing.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
