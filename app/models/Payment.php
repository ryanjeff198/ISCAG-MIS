<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Payment Model
 * Handles CRUD operations for the `payments` table.
 * Specifically handles the Deposit and Advance rent payments linked to a Lease.
 */
class Payment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    /**
     * Generate initial pending payments (Deposit and Advance) for a lease.
     * This is intended to be called right after a lease is marked 'ACCEPTED'.
     *
     * @param int $leaseId
     * @param int $tenantId
     * @param float $depositAmount
     * @param float $advanceAmount
     */
    public function generateInitialPayments(int $leaseId, int $tenantId, float $depositAmount, float $advanceAmount): bool
    {
        // Check if payments already exist for this lease to prevent duplicates
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE lease_id = :lid");
        $stmt->execute(['lid' => $leaseId]);
        if ($stmt->fetchColumn() > 0) {
            return true; // Already generated
        }

        $sql = "INSERT INTO payments (lease_id, tenant_id, payment_type, amount, payment_status)
                VALUES (:lease_id, :tenant_id, :payment_type, :amount, 'Pending')";
        
        $stmtInsert = $this->db->prepare($sql);

        // Generate Deposit Check
        $ok1 = $stmtInsert->execute([
            'lease_id'     => $leaseId,
            'tenant_id'    => $tenantId,
            'payment_type' => 'Deposit',
            'amount'       => $depositAmount
        ]);

        // Generate Advance Check
        $ok2 = $stmtInsert->execute([
            'lease_id'     => $leaseId,
            'tenant_id'    => $tenantId,
            'payment_type' => 'Advance',
            'amount'       => $advanceAmount
        ]);

        // Calculate Water Amount based on occupants
        $stmtOcc = $this->db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = :tid");
        $stmtOcc->execute(['tid' => $tenantId]);
        $occupants = (int)$stmtOcc->fetchColumn() + 1; // +1 for the tenant
        $waterAmount = $occupants * 100.00;

        // Generate Water-Advance
        $ok3 = $stmtInsert->execute([
            'lease_id'     => $leaseId,
            'tenant_id'    => $tenantId,
            'payment_type' => 'Water-Advance',
            'amount'       => $waterAmount
        ]);

        // Generate Contribution-Advance
        $ok4 = $stmtInsert->execute([
            'lease_id'     => $leaseId,
            'tenant_id'    => $tenantId,
            'payment_type' => 'Contribution-Advance',
            'amount'       => 150.00
        ]);

        return $ok1 && $ok2 && $ok3 && $ok4;
    }

    /**
     * Get all payments linked to a lease.
     *
     * @param int $leaseId
     * @return array
     */
    public function getPaymentsByLease(int $leaseId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE lease_id = :lid ORDER BY payment_id ASC");
        $stmt->execute(['lid' => $leaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Mark a payment as PAID.
     *
     * @param int $paymentId
     * @param string $referenceNumber
     * @return bool
     */
    public function markAsPaid(int $paymentId, string $referenceNumber = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET payment_status = 'Paid', 
                payment_date = NOW(), 
                reference_number = :ref 
            WHERE payment_id = :pid AND payment_status = 'Pending'
        ");
        
        $success = $stmt->execute([
            'ref' => $referenceNumber ?: 'PAY-' . strtoupper(uniqid()),
            'pid' => $paymentId
        ]);

        if ($success) {
            $this->checkAndActivateLease($paymentId);
        }

        return $success;
    }

    /**
     * Internal logic: Activation Logic
     * IF deposit AND advance are fully paid THEN lease_status = ACTIVE
     */
    private function checkAndActivateLease(int $paymentId): void
    {
        // Get the lease ID for this payment
        $stmt = $this->db->prepare("SELECT lease_id FROM payments WHERE payment_id = :pid");
        $stmt->execute(['pid' => $paymentId]);
        $leaseId = $stmt->fetchColumn();

        if (!$leaseId) return;

        // Check if there are any pending payments left for this lease
        $stmtPending = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE lease_id = :lid AND payment_status != 'Paid'");
        $stmtPending->execute(['lid' => $leaseId]);
        
        if ($stmtPending->fetchColumn() == 0) {
            // All required payments are PAID!
            // Update lease to ACTIVE
            $stmtActivate = $this->db->prepare("UPDATE leases SET lease_status = 'Active' WHERE lease_id = :lid AND lease_status = 'Accepted'");
            $stmtActivate->execute(['lid' => $leaseId]);

            // Now that Lease is Active, ASSIGN ROOM
            $stmtApp = $this->db->prepare("SELECT application_id, tenant_id FROM leases WHERE lease_id = :lid");
            $stmtApp->execute(['lid' => $leaseId]);
            $leaseData = $stmtApp->fetch(PDO::FETCH_ASSOC);

            if ($leaseData) {
                require_once BASE_PATH . '/app/models/ApartmentApp.php';
                require_once BASE_PATH . '/app/models/Notification.php';
                
                $appModel = new ApartmentApp();
                $notifModel = new Notification();
                
                $result = $appModel->assignRoom((int) $leaseData['application_id']);
                
                if (isset($result['result']) && $result['result'] === 'assigned') {
                    $notifModel->create(
                        $leaseData['tenant_id'],
                        'Room Assigned!',
                        'Your payment is confirmed and your lease is now Active! You have been assigned to Room ' . $result['room_number'] 
                        . ' in ' . $result['building'] . '. Welcome to your new home!',
                        'payment'
                    );
                } else {
                    $notifModel->create(
                        $leaseData['tenant_id'],
                        'Lease Active — Room Pending',
                        'Your payment is confirmed and lease is Active! However, a room could not be assigned automatically at this moment. '
                        . 'Administration will contact you shortly.',
                        'warning'
                    );
                }
            }
        }
    }
}
