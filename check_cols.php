<?php
require 'config/database.php';
$db = getDbConnection();
$res = $db->query("SELECT * FROM apartment_units LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo "KEYS: " . implode(", ", array_keys($res)) . "\n";
