<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();

$stmt = $db->query("
    SELECT l.*, a.building, a.room_number, a.unit_type as app_unit_type
    FROM leases l
    LEFT JOIN apartment_applications a ON l.application_id = a.id
    WHERE l.tenant_id = 63
");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
