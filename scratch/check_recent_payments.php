<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = getDbConnection();
$rows = $db->query("SELECT payment_id, lease_id, tenant_id, amount, payment_type, reference_number, payment_status, payment_date FROM payments ORDER BY payment_id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
