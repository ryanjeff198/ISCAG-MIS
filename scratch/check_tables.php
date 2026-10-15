<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDbConnection();
    $rows = $db->query("SELECT * FROM deceased_records LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    echo "deceased_records count: " . count($rows) . "\n";
    print_r($rows);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
