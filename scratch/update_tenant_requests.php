<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$db = getDbConnection();
$count = $db->exec("UPDATE burial_requests SET tenant_id = 60 WHERE tenant_id = 1");
echo "Updated $count requests to tenant_id 60\n";
