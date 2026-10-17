<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
require_once 'c:/xampp/htdocs/ISCAG-MIS/app/models/CharityProgram.php';

$model = new CharityProgram();
$model->seedInitialDataIfEmpty('Admin Staff');

$stats = $model->getDashboardStats();
print_r($stats);

$avail = $model->getAvailableDonations();
echo "\nAvailable donations count: " . count($avail) . "\n";
foreach ($avail as $a) {
    echo "Ref: {$a['donation_ref']} | Type: {$a['donation_type']} | Avail Amt: ₱{$a['available_amount']} | Avail Qty: {$a['available_quantity']}\n";
}
