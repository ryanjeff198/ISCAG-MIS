<?php
require 'config/database.php';
$db = getDbConnection();
$q = $db->query('DESCRIBE apartment_units');
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo $r['Field'] . "\n";
}
