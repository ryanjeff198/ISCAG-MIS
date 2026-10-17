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

$db = getDbConnection();
$now = TimeSim::now();

$stmt = $db->prepare("SELECT COUNT(*) as cnt FROM tenant_family_members WHERE tenant_id = ?");
$stmt->execute([$userId]);
$memberCount = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] ?? 0);
$occupants = $memberCount + 1;

$stmt = $db->prepare("SELECT parking_id, vehiclename, plateno, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
$stmt->execute([$userId]);
$parkingApps = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

$paidRecurringKeys = [];
$advancePaymentCount = 0;
$advanceWaterCount = 0;
$advanceParkingCount = 0;
$advanceContributionCount = 0;
foreach ($payments as $p) {
    if ($p['payment_status'] === 'Paid') {
        $typeLower = strtolower($p['payment_type']);
        if (strpos($typeLower, '-') !== false) {
            $paidRecurringKeys[] = $typeLower;
        }
        if ($typeLower === 'rent-advance' || $typeLower === 'advance') $advancePaymentCount++;
        if ($typeLower === 'water-advance') $advanceWaterCount++;
        if ($typeLower === 'parking-advance') $advanceParkingCount++;
        if ($typeLower === 'contribution-advance') $advanceContributionCount++;
    }
}

echo "Advance counts:\n";
echo "Rent: $advancePaymentCount, Water: $advanceWaterCount, Contrib: $advanceContributionCount, Parking: $advanceParkingCount\n";

$leaseStart = new \DateTime($lease['start_date']);
$leaseEnd = $lease['end_date'] ? new \DateTime($lease['end_date']) : null;
$maxAdv = max($advancePaymentCount, $advanceWaterCount, $advanceContributionCount, $advanceParkingCount);

$effectiveAdv = max(0, $maxAdv - 1);
$limitDate = ($leaseEnd && $leaseEnd < $now) ? clone $leaseEnd : clone $now;
if ($effectiveAdv > 0) {
    $limitDate->modify("+{$effectiveAdv} month");
}

$currentDate = clone $leaseStart;
$monthCount = 0;
$recurringCharges = [];

while ($currentDate <= $limitDate) {
    $monthName = $currentDate->format('F Y');
    $monthKey = $currentDate->format('Y-m');

    $rentId = 'rent-' . $monthKey;
    $isPaid = in_array(strtolower($rentId), $paidRecurringKeys);
    if (!$isPaid && $advancePaymentCount > 0) {
        $isPaid = true;
        $advancePaymentCount--;
    }

    $waterId = 'water-' . $monthKey;
    $isWaterPaid = in_array(strtolower($waterId), $paidRecurringKeys);
    if (!$isWaterPaid && $advanceWaterCount > 0) {
        $isWaterPaid = true;
        $advanceWaterCount--;
    }

    $contribId = 'contribution-' . $monthKey;
    $isContribPaid = in_array(strtolower($contribId), $paidRecurringKeys);
    if (!$isContribPaid && $advanceContributionCount > 0) {
        $isContribPaid = true;
        $advanceContributionCount--;
    }

    if ($monthCount > 0) {
        $recurringCharges[] = [
            'id'          => $rentId,
            'date'        => $currentDate->format('Y-m-d'),
            'type'        => 'Monthly Rent',
            'description' => "Monthly Rent — $monthName",
            'amount'      => (float)$lease['monthly_rent'],
            'status'      => $isPaid ? 'Paid' : 'Unpaid'
        ];

        $recurringCharges[] = [
            'id'          => $waterId,
            'date'        => $currentDate->format('Y-m-d'),
            'type'        => 'Water Bill',
            'description' => "Water Consumption ($occupants occupants) — $monthName",
            'amount'      => (float)($occupants * 100),
            'status'      => $isWaterPaid ? 'Paid' : 'Unpaid'
        ];

        $recurringCharges[] = [
            'id'          => $contribId,
            'date'        => $currentDate->format('Y-m-d'),
            'type'        => 'Contribution Fee',
            'description' => "Contribution (Security/Garbage) — $monthName",
            'amount'      => 150.00,
            'status'      => $isContribPaid ? 'Paid' : 'Unpaid'
        ];
    }

    $currentDate->modify('+1 month');
    $monthCount++;
    if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
}

echo "\nCharges generated (" . count($recurringCharges) . "):\n";
foreach ($recurringCharges as $c) {
    echo "- [{$c['status']}] {$c['description']} : PHP {$c['amount']} (Date: {$c['date']})\n";
}
