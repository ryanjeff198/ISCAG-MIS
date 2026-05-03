<?php
require 'config/database.php';
$db = getDbConnection();
// Show all leases for Ryan showing all columns
$r = $db->query("SELECT * FROM leases WHERE tenant_id = 63")->fetchAll(PDO::FETCH_ASSOC);
foreach ($r as $row) {
    echo "lease_id={$row['lease_id']} status=[{$row['lease_status']}] app_id={$row['application_id']}\n";
}
// Show all pending payments  
$p = $db->query("SELECT payment_id, lease_id, payment_type, payment_status FROM payments WHERE tenant_id = 63 AND payment_status = 'Pending'")->fetchAll(PDO::FETCH_ASSOC);
echo "Pending payments: " . count($p) . "\n";
foreach ($p as $row) {
    echo "  pay_id={$row['payment_id']} lease={$row['lease_id']} type={$row['payment_type']}\n";
}
