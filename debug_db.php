<?php
require_once 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("DESCRIBE move_out_requests");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
