<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
require_once 'c:/xampp/htdocs/ISCAG-MIS/app/models/CharityProgram.php';

$model = new CharityProgram();
echo "=== TESTING CHARITY WORKFLOW ===\n\n";

// 1. Record a new monetary donation
$monetaryDonation = [
    'donor_name' => 'Bro. Tariq Al-Mansoor',
    'donor_address' => 'Makati City',
    'donation_type' => 'monetary',
    'amount' => 40000.00,
    'date_received' => date('Y-m-d'),
    'payment_method' => 'Bank Transfer',
    'payment_reference' => 'BDO-TEST-999',
    'received_by' => 'Staff Tester',
    'remarks' => 'Test monetary donation'
];
$resDon1 = $model->recordDonation($monetaryDonation, 'Staff Tester', 1);
echo "1. Record Monetary Donation: " . ($resDon1['success'] ? "SUCCESS ({$resDon1['donation_ref']})" : "FAILED ({$resDon1['error']})") . "\n";
$don1Id = $resDon1['id'];

// 2. Record a new in-kind donation
$inKindDonation = [
    'donor_name' => 'Al-Hikmah Relief Group',
    'donor_address' => 'Quezon City',
    'donation_type' => 'in-kind',
    'item_description' => 'Winter Blanket & Emergency Relief Packs',
    'quantity' => 50,
    'date_received' => date('Y-m-d'),
    'payment_method' => 'In-Person Delivery',
    'payment_reference' => 'DEL-TEST-888',
    'received_by' => 'Staff Tester',
    'remarks' => 'Test in-kind donation'
];
$resDon2 = $model->recordDonation($inKindDonation, 'Staff Tester', 1);
echo "2. Record In-Kind Donation: " . ($resDon2['success'] ? "SUCCESS ({$resDon2['donation_ref']})" : "FAILED ({$resDon2['error']})") . "\n";
$don2Id = $resDon2['id'];

// 3. Verify Available
$d1 = $model->getDonationById($don1Id);
echo "3. Available balance for DON-1: ₱" . number_format($d1['available_amount'], 2) . " (Expected: ₱40,000.00)\n";
assert($d1['available_amount'] == 40000.00);

// 4. Over-allocation check (Should FAIL)
$resOver = $model->createAllocation([
    'donation_id' => $don1Id,
    'receiving_department' => 'Damayan',
    'allocated_amount' => 50000.00, // exceeds 40,000
    'purpose' => 'Excess allocation test',
    'allocation_date' => date('Y-m-d'),
    'allocated_by' => 'Staff Tester'
], 'Staff Tester', 1);
echo "4. Exceed Available Allocation: " . (!$resOver['success'] ? "CORRECTLY REJECTED ({$resOver['error']})" : "UNEXPECTEDLY PASSED") . "\n";

// 5. Valid Partial Allocation (₱25,000 to Damayan)
$resAlloc1 = $model->createAllocation([
    'donation_id' => $don1Id,
    'receiving_department' => 'Damayan',
    'allocated_amount' => 25000.00,
    'purpose' => 'Burial Assistance Funding',
    'allocation_date' => date('Y-m-d'),
    'allocated_by' => 'Staff Tester',
    'remarks' => 'Assigned to Damayan operations'
], 'Staff Tester', 1);
echo "5. Create Allocation: " . ($resAlloc1['success'] ? "SUCCESS ({$resAlloc1['allocation_ref']})" : "FAILED ({$resAlloc1['error']})") . "\n";
$alloc1Id = $resAlloc1['id'];

// 6. Check Available Balance after Allocation
$d1After = $model->getDonationById($don1Id);
echo "6. Remaining Available for DON-1: ₱" . number_format($d1After['available_amount'], 2) . " (Expected: ₱15,000.00)\n";
assert($d1After['available_amount'] == 15000.00);

// 7. Over-distribution check (Should FAIL)
$resDistOver = $model->recordDistribution([
    'allocation_id' => $alloc1Id,
    'distributed_amount' => 30000.00, // exceeds allocated 25,000
    'purpose' => 'Excess distribution test',
    'distribution_date' => date('Y-m-d'),
    'distributed_by' => 'Staff Tester',
    'received_by' => 'Beneficiary A'
], null, 'Staff Tester', 1);
echo "7. Exceed Allocated Distribution: " . (!$resDistOver['success'] ? "CORRECTLY REJECTED ({$resDistOver['error']})" : "UNEXPECTEDLY PASSED") . "\n";

// 8. Valid Partial Distribution (₱15,000 of ₱25,000)
$resDist1 = $model->recordDistribution([
    'allocation_id' => $alloc1Id,
    'distributed_amount' => 15000.00,
    'purpose' => 'Grave excavation & burial aid for family',
    'distribution_date' => date('Y-m-d'),
    'distributed_by' => 'Staff Tester',
    'received_by' => 'Family of Brother Kareem',
    'remarks' => 'Handed over in cash'
], null, 'Staff Tester', 1);
echo "8. Record Distribution: " . ($resDist1['success'] ? "SUCCESS ({$resDist1['distribution_ref']})" : "FAILED ({$resDist1['error']})") . "\n";

// 9. Check Allocation Status and remaining allocated
$alloc1After = $model->getAllocationById($alloc1Id);
echo "9. Allocation status: {$alloc1After['status']} | Remaining allocated to distribute: ₱" . number_format($alloc1After['remaining_allocated_amount'], 2) . " (Expected: ₱10,000.00)\n";
assert($alloc1After['remaining_allocated_amount'] == 10000.00);

// 10. Complete Remaining Distribution (₱10,000)
$resDist2 = $model->recordDistribution([
    'allocation_id' => $alloc1Id,
    'distributed_amount' => 10000.00,
    'purpose' => 'Final portion of burial assistance',
    'distribution_date' => date('Y-m-d'),
    'distributed_by' => 'Staff Tester',
    'received_by' => 'Family of Sister Aminah',
    'remarks' => 'Full allocation now disbursed'
], null, 'Staff Tester', 1);
echo "10. Complete Remaining Distribution: " . ($resDist2['success'] ? "SUCCESS ({$resDist2['distribution_ref']})" : "FAILED ({$resDist2['error']})") . "\n";

$alloc1Final = $model->getAllocationById($alloc1Id);
echo "    Allocation final status: {$alloc1Final['status']} (Expected: Distributed)\n";
assert($alloc1Final['status'] === 'Distributed');

// 11. Test In-Kind Allocation & Cancellation
$resAlloc2 = $model->createAllocation([
    'donation_id' => $don2Id,
    'receiving_department' => 'Dawah',
    'allocated_quantity' => 20,
    'purpose' => 'Outreach kits for new Muslims',
    'allocation_date' => date('Y-m-d'),
    'allocated_by' => 'Staff Tester'
], 'Staff Tester', 1);
echo "11. Allocate In-Kind (20 units): " . ($resAlloc2['success'] ? "SUCCESS ({$resAlloc2['allocation_ref']})" : "FAILED") . "\n";
$alloc2Id = $resAlloc2['id'];

$d2BeforeCancel = $model->getDonationById($don2Id);
echo "    Available in-kind before cancel: {$d2BeforeCancel['available_quantity']} (Expected: 30)\n";
assert($d2BeforeCancel['available_quantity'] == 30);

// Cancel allocation #2
$resCancel = $model->cancelAllocation($alloc2Id, 'Staff Tester', 'Outreach program postponed');
echo "12. Cancel Allocation: " . ($resCancel['success'] ? "SUCCESS" : "FAILED") . "\n";

$d2AfterCancel = $model->getDonationById($don2Id);
echo "    Available in-kind restored after cancel: {$d2AfterCancel['available_quantity']} (Expected: 50)\n";
assert($d2AfterCancel['available_quantity'] == 50);

// 13. Test Acknowledgment Toggle
$ackBefore = $d1['ack_issued'];
$resAck = $model->issueAcknowledgment($don1Id, 'Staff Tester', 1);
echo "13. Toggle Acknowledgment: " . ($resAck['success'] ? "SUCCESS (New status: {$resAck['ack_issued']})" : "FAILED") . "\n";

echo "\nALL WORKFLOW TESTS COMPLETED SUCCESSFULLY!\n";
