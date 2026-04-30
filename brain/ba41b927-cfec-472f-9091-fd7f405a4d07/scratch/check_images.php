<?php
define('BASE_PATH', 'c:/Users/Dela_/Desktop/Xampp/htdocs/ISCAG-MIS');
require 'config/database.php';
$db = getDbConnection();
$stmt = $db->query('SELECT i.type_id, t.label, i.image_id, i.is_thumbnail FROM apartment_type_images i JOIN apartment_types t ON i.type_id = t.type_id');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
