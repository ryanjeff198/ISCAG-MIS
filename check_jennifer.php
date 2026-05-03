<?php
require 'config/database.php';
$db = getDbConnection();

// Check Jennifer's Account
$user = $db->query("SELECT tenant_id, first_name, last_name, role FROM tenant_accounts WHERE first_name = 'Jennifer'")->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "User Jennifer not found.\n";
    exit;
}

$id = $user['tenant_id'];
echo "=== USER INFO ===\n";
print_r($user);

// Check latest Application
echo "\n=== LATEST APPLICATION ===\n";
$app = $db->query("SELECT application_id, status, unit_id, assigned_at FROM apartmentsapp WHERE tenant_id = $id ORDER BY application_id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
print_r($app);

// Check Leases
echo "\n=== LEASES ===\n";
$leases = $db->query("SELECT lease_id, application_id, lease_status FROM leases WHERE tenant_id = $id ORDER BY lease_id DESC")->fetchAll(PDO::FETCH_ASSOC);
print_r($leases);

if ($leases) {
    $leaseId = $leases[0]['lease_id'];
    echo "\n=== PAYMENTS FOR LATEST LEASE ($leaseId) ===\n";
    $payments = $db->query("SELECT payment_id, payment_type, payment_status, amount FROM payments WHERE lease_id = $leaseId")->fetchAll(PDO::FETCH_ASSOC);
    print_r($payments);
}
