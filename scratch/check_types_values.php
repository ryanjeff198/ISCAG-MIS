<?php
require_once 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$rows = $db->query("SELECT type_id, label, price, security_deposit, advance_rent FROM apartment_types")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
