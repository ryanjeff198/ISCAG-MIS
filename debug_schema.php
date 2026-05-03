<?php
require_once 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("DESCRIBE move_out_requests");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
