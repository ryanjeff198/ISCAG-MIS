<?php
require_once 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("SELECT * FROM move_out_requests");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
