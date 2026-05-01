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
        
        $dayOfMonth = (int)$now->format('j');
        
        // Window: 1st to 5th of the month
        if ($dayOfMonth < 1 || $dayOfMonth > 5) {
            return;
        }

        $monthKey = $now->format('Y-m');
        $monthTitle = $now->format('F Y');
        $notifTitle = "Upcoming Payment Reminder - $monthTitle";

        // 1. Check if already notified for this month
        $notifModel = new Notification();
        if ($notifModel->hasNotificationWithTitle($tenantId, $notifTitle)) {
            return;
        }

        // 2. Check if tenant has an active lease and unpaid items for this month
        $leaseModel = new Lease();
        $lease = $leaseModel->getLeaseByTenantId($tenantId);
        
        if (!$lease || strtolower($lease['lease_status'] ?? '') !== 'active') {
            return;
        }

        // 3. Simple check: Are there payments for this month?
        // Fetch payments for this tenant for the current month
        $stmt = $db->prepare("SELECT payment_type FROM payments WHERE tenant_id = :tid AND payment_status = 'Paid'");
        $stmt->execute(['tid' => $tenantId]);
        $paidTypes = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        // Identify if basic montly items are missing
        $rentKey = "rent-$monthKey";
        $waterKey = "water-$monthKey";
        $contribKey = "contribution-$monthKey";

        $needsReminding = false;
        if (!in_array($rentKey, $paidTypes) || !in_array($waterKey, $paidTypes) || !in_array($contribKey, $paidTypes)) {
            $needsReminding = true;
        }

        if ($needsReminding) {
            $notifModel->create(
                $tenantId,
                $notifTitle,
                "Heads up! Your monthly bill for $monthTitle is due on the 5th. Please settle your Rent, Water, and Contributions to avoid any late flags.",
                'payment'
            );
        }
    }
}
