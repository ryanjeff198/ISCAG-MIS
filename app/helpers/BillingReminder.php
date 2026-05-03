<?php

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Notification.php';
require_once BASE_PATH . '/app/models/Lease.php';

class BillingReminder {
    /**
     * Checks if the tenant has upcoming dues and sends a reminder notification if within the window.
     * Logic: Reminder triggers between the 1st and 5th of the month if items are unpaid.
     */
    public static function checkAndNotify(int $tenantId): void {
        $db = getDbConnection();
        $now = new \DateTime();
        
        // 1. Get Lease Info
        $leaseModel = new Lease();
        $lease = $leaseModel->getLeaseByTenantId($tenantId);
        if (!$lease || strtolower($lease['lease_status'] ?? '') !== 'active') return;

        $startDate = new \DateTime($lease['start_date']);
        $dueDay = (int)$startDate->format('j');
        
        // 2. Identify the target billing month we are checking
        // If today's day is 5+ days before the due day, we are checking the current month.
        // If we are within 5 days of the due day (or past it), we are checking the NEXT recurring cycle.
        $targetDate = clone $now;
        $currentDay = (int)$now->format('j');

        // Logic: If due day is 3rd, and today is 29th, target is NEXT month's 3rd.
        if ($currentDay > ($dueDay - 5)) {
            $targetDate->modify('first day of next month');
        }
        
        // Set to the specific due day of the target month
        // Handle months with fewer days (e.g. 31st to 28th)
        $lastDayOfTarget = (int)$targetDate->format('t');
        $safeDueDay = min($dueDay, $lastDayOfTarget);
        $targetDate->setDate((int)$targetDate->format('Y'), (int)$targetDate->format('m'), $safeDueDay);

        // 3. Check if we are in the 5-day window before targetDate
        $diff = $now->diff($targetDate);
        $daysUntilDue = (int)$diff->format('%r%a');

        // Only remind if 0 to 5 days before due
        if ($daysUntilDue < 0 || $daysUntilDue > 5) return;

        $monthKey = $targetDate->format('Y-m');
        $monthTitle = $targetDate->format('F Y');
        $notifTitle = "Upcoming Payment Reminder - $monthTitle";

        // 4. Check if already notified
        $notifModel = new Notification();
        if ($notifModel->hasNotificationWithTitle($tenantId, $notifTitle)) return;

        // 5. Check if already paid (including advances)
        // Check payments for this specific month key
        $rentKey = "rent-$monthKey";
        $waterKey = "water-$monthKey";
        $contribKey = "contribution-$monthKey";

        $stmt = $db->prepare("SELECT payment_type FROM payments WHERE tenant_id = :tid AND payment_status = 'Paid' AND (payment_type IN (?,?,?) OR payment_type = 'Advance')");
        $stmt->execute([$rentKey, $waterKey, $contribKey, $tenantId]);
        $paidItems = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        // Note: For Rent, we also need to check if they have a generic 'Advance' that hasn't been used, 
        // but for simplicity in the reminder, we check if the specific month key is marked (which our logic does).
        $hasRent = in_array($rentKey, $paidItems);
        $hasWater = in_array($waterKey, $paidItems);
        $hasContrib = in_array($contribKey, $paidItems);

        if (!$hasRent || !$hasWater || !$hasContrib) {
            $notifModel->create(
                $tenantId,
                $notifTitle,
                "Heads up! Your monthly bill for $monthTitle is due on " . $targetDate->format('M jS') . ". Please settle your Rent, Water, and Contributions to keep your account in good standing.",
                'payment'
            );
        }
    }
}
