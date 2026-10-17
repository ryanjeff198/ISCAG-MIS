<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();

$users = $db->query("SELECT tenant_id, first_name, last_name, email FROM tenant_accounts WHERE first_name LIKE '%Ryan%' OR email LIKE '%felizardo%'")->fetchAll(PDO::FETCH_ASSOC);
echo "USERS:\n";
print_r($users);

if (!empty($users)) {
    $uid = $users[0]['tenant_id'];
    $leases = $db->query("SELECT * FROM leases WHERE tenant_id = $uid")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nLEASES:\n";
    print_r($leases);

    $members = $db->query("SELECT * FROM tenant_family_members WHERE tenant_id = $uid")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nMEMBERS (" . count($members) . "):\n";
    print_r($members);

    $parking = $db->query("SELECT * FROM tenant_parking WHERE tenant_id = $uid")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nPARKING (" . count($parking) . "):\n";
    print_r($parking);

    $payments = $db->query("SELECT * FROM payments WHERE tenant_id = $uid ORDER BY payment_id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nPAYMENTS:\n";
    print_r($payments);
}
