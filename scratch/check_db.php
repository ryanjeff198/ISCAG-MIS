<?php
require_once __DIR__ . '/../app/core/Database.php';
$db = getDbConnection();
$stmt = $db->query("DESCRIBE billing");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);
