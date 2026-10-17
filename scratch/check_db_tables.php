<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$pdo = getDbConnection();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
