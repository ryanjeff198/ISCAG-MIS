<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$pdo = getDbConnection();
$cols = $pdo->query('DESCRIBE charitydonations')->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
