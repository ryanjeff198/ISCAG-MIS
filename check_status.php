<?php
require_once 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("SELECT request_id, status FROM move_out_requests");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['request_id']} | Status: {$row['status']}\n";
}
