<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$damayanTables = array_filter($tables, function($t) {
    return preg_match('/(burial|cemetery|plot|funeral|deceased|death|certificate)/i', $t);
});
echo "Relevant tables:\n";
print_r($damayanTables);

foreach ($damayanTables as $t) {
    echo "\n--- Table: $t ---\n";
    $cols = $db->query("DESCRIBE $t")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
}
