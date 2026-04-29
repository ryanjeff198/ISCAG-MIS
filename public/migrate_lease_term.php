<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE apartmentsapp ADD COLUMN lease_term INT NOT NULL DEFAULT 12 AFTER roomtype;");
    echo "Added lease_term to apartmentsapp.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) echo $e->getMessage();
}
