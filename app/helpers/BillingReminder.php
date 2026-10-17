<?php

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/Notification.php';
require_once BASE_PATH . '/app/models/Lease.php';
require_once BASE_PATH . '/app/helpers/Mailer.php';
require_once BASE_PATH . '/app/helpers/TimeSim.php';

class BillingReminder {

    /**
     * Get accurate billing details and line items for a tenant, matching ApartmentController & tenant_payment.php
     */
    public static function getTenantBillingData(int $tenantId, ?\DateTime $targetDate = null): ?array {
        $db = getDbConnection();
        $leaseModel = new Lease();
        $lease = $leaseModel->getLeaseByTenantId($tenantId);
        if (!$lease || strtolower($lease['lease_status'] ?? '') !== 'active') {
            return null;
        }

        // 1. Tenant info
        $stmt = $db->prepare("SELECT tenant_id, first_name, last_name, email FROM tenant_accounts WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        $tenant = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$tenant) return null;

        $tenantName = trim(($tenant['first_name'] ?? '') . ' ' . ($tenant['last_name'] ?? ''));
        $tenantEmail = trim($tenant['email'] ?? '');

        // 2. Unit info
        $stmt = $db->prepare("SELECT unit_id, building, room_number FROM apartment_units WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        $unit = $stmt->fetch(\PDO::FETCH_ASSOC);
        $unitDisplay = $unit ? trim(($unit['building'] ?? 'Building') . ' - Room ' . ($unit['room_number'] ?? '')) : 'ISCAG Apartment Unit';

        // 3. Occupants count for water bill
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$tenantId]);
        $memberCount = (int)$stmt->fetchColumn();
        $occupants = $memberCount + 1; // tenant + registered members

        // 4. Approved Parking (ONLY approved vehicles from tenant_parking)
        $stmt = $db->prepare("SELECT parking_id, vehiclename, plateno, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$tenantId]);
        $parkingApps = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // 5. Billing target date
        $now = TimeSim::now('now', $tenantId);
        if (!$targetDate) {
            $startDate = new \DateTime($lease['start_date']);
            $dueDay = (int)$startDate->format('j');
            
            $targetDate = clone $now;
            $currentDay = (int)$now->format('j');
            if ($currentDay > ($dueDay - 5)) {
                $targetDate->modify('first day of next month');
            }
            $lastDay = (int)$targetDate->format('t');
            $targetDate->setDate((int)$targetDate->format('Y'), (int)$targetDate->format('m'), min($dueDay, $lastDay));
        }

        $monthKey = $targetDate->format('Y-m');
        $monthName = $targetDate->format('F Y');
        $dueDateFormatted = $targetDate->format('F j, Y');

        // 6. Build exact item breakdown matching tenant_payment.php
        $items = [];
        $totalDue = 0.0;

        // Rent
        $rentAmount = (float)$lease['monthly_rent'];
        $items[] = [
            'name'   => "Monthly Rent ({$monthName})",
            'amount' => $rentAmount,
            'type'   => 'rent'
        ];
        $totalDue += $rentAmount;

        // Water
        $waterAmount = (float)($occupants * 100);
        $items[] = [
            'name'   => "Water Consumption ({$occupants} occupants)",
            'amount' => $waterAmount,
            'type'   => 'water'
        ];
        $totalDue += $waterAmount;

        // Contribution
        $contribAmount = 150.00;
        $items[] = [
            'name'   => "Contribution (Security/Garbage)",
            'amount' => $contribAmount,
            'type'   => 'contribution'
        ];
        $totalDue += $contribAmount;

        // Parking (ONLY if approved vehicle registered)
        foreach ($parkingApps as $pa) {
            $vName = $pa['vehiclename'] ?: 'Vehicle';
            $pPlate = $pa['plateno'] ? " ({$pa['plateno']})" : '';
            $items[] = [
                'name'   => "Parking Fee — {$vName}{$pPlate}",
                'amount' => 1000.00,
                'type'   => 'parking'
            ];
            $totalDue += 1000.00;
        }

        // Days until due
        $diff = $now->diff($targetDate);
        $daysUntilDue = (int)$diff->format('%r%a');

        $billingRef = 'ISCAG-BILL-' . $targetDate->format('Ym') . '-T' . str_pad((string)$tenantId, 3, '0', STR_PAD_LEFT);

        return [
            'tenant_id'        => $tenantId,
            'tenant_name'      => $tenantName,
            'tenant_email'     => $tenantEmail,
            'unit'             => $unitDisplay,
            'due_date'         => $dueDateFormatted,
            'month_name'       => $monthName,
            'month_key'        => $monthKey,
            'reference_number' => $billingRef,
            'occupants'        => $occupants,
            'has_parking'      => !empty($parkingApps),
            'items'            => $items,
            'total_due'        => $totalDue,
            'days_until_due'   => $daysUntilDue,
            'is_overdue'       => ($daysUntilDue < 0),
        ];
    }

    /**
     * Checks if tenant has upcoming dues and sends reminder via in-app notification AND PHPMailer.
     */
    public static function checkAndNotify(int $tenantId): void {
        $data = self::getTenantBillingData($tenantId);
        if (!$data) return;

        // Check 5-day window before due
        $daysUntilDue = $data['days_until_due'];
        if ($daysUntilDue < 0 || $daysUntilDue > 5) return;

        $monthKey = $data['month_key'];
        $monthTitle = $data['month_name'];
        $notifTitle = "Upcoming Payment Reminder - $monthTitle";

        // Check if already notified
        $notifModel = new Notification();
        if ($notifModel->hasNotificationWithTitle($tenantId, $notifTitle)) return;

        // Check paid status
        $db = getDbConnection();
        $rentKey = "rent-$monthKey";
        $waterKey = "water-$monthKey";
        $contribKey = "contribution-$monthKey";

        $stmt = $db->prepare("SELECT payment_type FROM payments WHERE tenant_id = ? AND payment_status = 'Paid' AND (payment_type IN (?,?,?) OR payment_type LIKE '%advance%')");
        $stmt->execute([$tenantId, $rentKey, $waterKey, $contribKey]);
        $paidItems = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];

        $hasRent = in_array($rentKey, $paidItems);
        $hasWater = in_array($waterKey, $paidItems);
        $hasContrib = in_array($contribKey, $paidItems);

        if (!$hasRent || !$hasWater || !$hasContrib) {
            // In-app Notification
            $notifModel->create(
                $tenantId,
                $notifTitle,
                "Heads up! Your monthly bill of ₱" . number_format($data['total_due'], 2) . " for $monthTitle is due on {$data['due_date']}. Please settle your dues to keep your account in good standing.",
                'payment'
            );

            // Send Email via PHPMailer
            if (!empty($data['tenant_email'])) {
                Mailer::sendBillingReminder([
                    'to_email'         => $data['tenant_email'],
                    'to_name'          => $data['tenant_name'],
                    'reference_number' => $data['reference_number'],
                    'due_date'         => $data['due_date'],
                    'days_until_due'   => $daysUntilDue,
                    'month_name'       => $data['month_name'],
                    'items'            => $data['items'],
                    'total_due'        => $data['total_due'],
                    'unit'             => $data['unit'],
                    'is_overdue'       => $data['is_overdue'],
                ]);
            }
        }
    }

    /**
     * Send a mock/test billing reminder email immediately
     */
    public static function sendMockReminder(int $tenantId, ?string $overrideEmail = null): array {
        $data = self::getTenantBillingData($tenantId);
        if (!$data) {
            return ['success' => false, 'message' => 'No active lease or tenant found for ID ' . $tenantId];
        }

        $emailToSend = !empty($overrideEmail) ? $overrideEmail : $data['tenant_email'];
        if (empty($emailToSend)) {
            return ['success' => false, 'message' => 'No valid email address found for tenant'];
        }

        $mailResult = Mailer::sendBillingReminder([
            'to_email'         => $emailToSend,
            'to_name'          => $data['tenant_name'],
            'reference_number' => $data['reference_number'],
            'due_date'         => $data['due_date'],
            'days_until_due'   => $data['days_until_due'] >= 0 ? $data['days_until_due'] : 3,
            'month_name'       => $data['month_name'],
            'items'            => $data['items'],
            'total_due'        => $data['total_due'],
            'unit'             => $data['unit'],
            'is_overdue'       => false,
        ]);

        return [
            'success'   => $mailResult,
            'sent_to'   => $emailToSend,
            'tenant'    => $data['tenant_name'],
            'unit'      => $data['unit'],
            'items'     => $data['items'],
            'total_due' => $data['total_due']
        ];
    }
}
