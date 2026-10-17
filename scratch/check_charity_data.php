<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$pdo = getDbConnection();
echo "charitydonations rows: " . $pdo->query('SELECT COUNT(*) FROM charitydonations')->fetchColumn() . "\n";
$rows = $pdo->query('SELECT * FROM charitydonations LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
