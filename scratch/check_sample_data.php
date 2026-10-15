<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

echo "burial_requests count: " . $db->query("SELECT COUNT(*) FROM burial_requests")->fetchColumn() . "\n";
print_r($db->query("SELECT * FROM burial_requests LIMIT 5")->fetchAll(PDO::FETCH_ASSOC));

echo "funeral_cases count: " . $db->query("SELECT COUNT(*) FROM funeral_cases")->fetchColumn() . "\n";
print_r($db->query("SELECT id, case_number, status, deceased_first_name, deceased_last_name, deceased_sex FROM funeral_cases LIMIT 5")->fetchAll(PDO::FETCH_ASSOC));
