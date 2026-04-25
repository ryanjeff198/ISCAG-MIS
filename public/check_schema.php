<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$stmt = $db->query("DESCRIBE tenant_accounts");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt = $db->query("SELECT * FROM tenant_accounts LIMIT 1");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
