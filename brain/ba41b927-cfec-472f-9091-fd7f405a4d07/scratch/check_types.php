<?php
define('BASE_PATH', 'c:/Users/Dela_/Desktop/Xampp/htdocs/ISCAG-MIS');
require 'config/database.php';
$db = getDbConnection();
$stmt = $db->query('SELECT type_id, type_key, label, price FROM apartment_types WHERE is_active = 1');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
