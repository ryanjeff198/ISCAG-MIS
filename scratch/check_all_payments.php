<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/TimeSim.php';
require_once BASE_PATH . '/app/models/Lease.php';
require_once BASE_PATH . '/app/models/Payment.php';

$userId = 63;
$leaseModel = new Lease();
$paymentModel = new Payment();
$lease = $leaseModel->getLeaseByTenantId($userId);
$payments = $paymentModel->getPaymentsByLease($lease['lease_id']);

echo "Total payments: " . count($payments) . "\n";
foreach ($payments as $p) {
    echo "- ID: {$p['payment_id']}, Type: {$p['payment_type']}, Status: {$p['payment_status']}, Amount: {$p['amount']}, Date: {$p['payment_date']}\n";
}
