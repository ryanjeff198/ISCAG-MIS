<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$data = $db->query('SELECT unit_id, room_number, building, status, tenant_id FROM apartment_units WHERE status != "Available"')->fetchAll(PDO::FETCH_ASSOC);
file_put_contents('c:/xampp/htdocs/Iscag/test_units.json', json_encode($data, JSON_PRETTY_PRINT));
