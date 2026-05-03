<?php
require 'config/database.php';
$db = getDbConnection();
echo "COLUMNS FOR apartment_units:\n";
print_r($db->query('DESCRIBE apartment_units')->fetchAll(PDO::FETCH_ASSOC));
echo "\nDATA FOR UNIT 2211:\n";
print_r($db->query("SELECT * FROM apartment_units WHERE room_number = '211' OR room_number = '2211'")->fetchAll(PDO::FETCH_ASSOC));
