<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$users = $db->query("SELECT tenant_id, first_name, last_name, email, role, is_verified FROM tenant_accounts LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
print_r($users);
