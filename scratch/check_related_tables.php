<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "All tables containing funeral, burial, damayan, volunteer:\n";
foreach ($tables as $t) {
    if (preg_match('/(burial|funeral|damayan|volunteer|ghusl|janazah|kafan|transport|cemetery|deceased)/i', $t)) {
        echo "- $t\n";
    }
}
