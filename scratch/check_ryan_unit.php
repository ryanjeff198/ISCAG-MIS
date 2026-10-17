<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$u = $db->query("SELECT * FROM apartment_units WHERE tenant_id = 63")->fetch(PDO::FETCH_ASSOC);
print_r($u);
