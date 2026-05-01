<?php
require_once 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$res = $db->query("DESCRIBE apartment_types")->fetchAll(PDO::FETCH_ASSOC);
foreach($res as $r) echo $r['Field'] . "\n";
