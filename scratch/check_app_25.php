<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$app = $db->query("SELECT a.*, u.unit_number, u.building_id, u.floor FROM apartmentsapp a LEFT JOIN apartment_units u ON a.unit_id = u.unit_id WHERE a.application_id = 25")->fetch(PDO::FETCH_ASSOC);
print_r($app);
