<?php
$base = 'c:/xampp/htdocs/Iscag';
require_once $base . '/config/database.php';
$db = getDbConnection();
$stmt = $db->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($tables, JSON_PRETTY_PRINT);
?>
