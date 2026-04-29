<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE lease_renewals ADD COLUMN requested_term_months INT NOT NULL DEFAULT 12 AFTER tenant_id;");
    echo "Added requested_term_months to lease_renewals.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) echo $e->getMessage();
}
