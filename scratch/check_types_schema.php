<?php
require_once 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$cols = $db->query("DESCRIBE `apartment_types`")->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
