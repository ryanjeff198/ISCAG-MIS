<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$aptTables = array_filter($tables, function($t) {
    return preg_match('/(apart|tenant|lease|room|building)/i', $t);
});
print_r($aptTables);
