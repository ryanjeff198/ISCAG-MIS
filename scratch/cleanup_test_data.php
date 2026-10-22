<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once BASE_PATH . '/config/database.php';
$pdo = getDbConnection();
$pdo->exec("DELETE FROM burial_requests WHERE ref_id LIKE 'TEST-%' OR ref_id LIKE 'DC-CPY-20261022-929A'");
echo "Cleaned up test rows successfully.\n";
