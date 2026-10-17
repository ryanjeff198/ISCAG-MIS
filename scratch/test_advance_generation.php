<?php
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/app/helpers/TimeSim.php';

$db = getDbConnection();
$userId = 63;
$now = TimeSim::now();

$stmt = $db->prepare("SELECT * FROM leases WHERE tenant_id = ? AND lease_status = 'Active' LIMIT 1");
$stmt->execute([$userId]);
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM payments WHERE tenant_id = ? AND lease_id = ?");
$stmt->execute([$userId, $lease['lease_id']]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

echo "Counts before loop: RentAdv=$advancePaymentCount, WaterAdv=$advanceWaterCount, ContribAdv=$advanceContributionCount\n";

$leaseStart = new \DateTime($lease['start_date']);
$leaseEnd = $lease['end_date'] ? new \DateTime($lease['end_date']) : null;
$maxAdv = max($advancePaymentCount, $advanceWaterCount, $advanceContributionCount, $advanceParkingCount);

$limitDate = ($leaseEnd && $leaseEnd < $now) ? clone $leaseEnd : clone $now;
if ($maxAdv > 0) {
    $limitDate->modify("+{$maxAdv} month");
}

echo "LimitDate: " . $limitDate->format('Y-m-d') . "\n";

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
            'id' => $rentId,
            'type' => 'Monthly Rent',
            'desc' => $monthName,
            'status' => $isPaid ? 'Paid' : 'Unpaid'
        ];
    }
    $currentDate->modify('+1 month');
    $monthCount++;
    if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
}

echo "Generated " . count($recurringCharges) . " rent months:\n";
foreach ($recurringCharges as $rc) {
    echo "{$rc['desc']}: {$rc['status']}\n";
}
