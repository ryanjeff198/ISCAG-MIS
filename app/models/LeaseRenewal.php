<?php
require_once BASE_PATH . '/config/database.php';

/**
 * LeaseRenewal Model
 * Handles CRUD operations for the `lease_renewals` table.
 */
class LeaseRenewal
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    /**
     * Request a contract renewal.
     */
    public function requestRenewal(int $leaseId, int $tenantId): bool
    {
        // Check if there is already a pending renewal for this lease
        $check = $this->db->prepare("SELECT COUNT(*) FROM lease_renewals WHERE lease_id = :lid AND status = 'Pending'");
        $check->execute(['lid' => $leaseId]);
        if ($check->fetchColumn() > 0) {
            return false; // Already requested
        }

        $stmt = $this->db->prepare("INSERT INTO lease_renewals (lease_id, tenant_id, status) VALUES (:lid, :tid, 'Pending')");
        return $stmt->execute(['lid' => $leaseId, 'tid' => $tenantId]);
    }

    /**
     * Get pending renewal status for a specific lease.
     */
    public function getPendingRenewal(int $leaseId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM lease_renewals WHERE lease_id = :lid AND status = 'Pending' LIMIT 1");
        $stmt->execute(['lid' => $leaseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get all renewals (for admin view).
     */
    public function getAllRenewals(): array
    {
        $sql = "
            SELECT r.*, l.end_date, l.unit_type, u.first_name, u.last_name, u.email
            FROM lease_renewals r
            JOIN leases l ON r.lease_id = l.lease_id
            JOIN tenant_accounts u ON r.tenant_id = u.tenant_id
            ORDER BY r.created_at DESC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Approve a renewal request.
     */
    public function approveRenewal(int $renewalId): bool
    {
        $this->db->beginTransaction();
        try {
            // Update renewal status
            $stmt = $this->db->prepare("UPDATE lease_renewals SET status = 'Approved' WHERE renewal_id = :rid AND status = 'Pending'");
            $stmt->execute(['rid' => $renewalId]);

            if ($stmt->rowCount() > 0) {
                // Extension logic: Add 1 year to the current lease end_date
                $leaseStmt = $this->db->prepare("
                    UPDATE leases 
                    SET end_date = DATE_ADD(end_date, INTERVAL 1 YEAR) 
                    WHERE lease_id = (SELECT lease_id FROM lease_renewals WHERE renewal_id = :rid)
                ");
                $leaseStmt->execute(['rid' => $renewalId]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Reject a renewal request.
     */
    public function rejectRenewal(int $renewalId): bool
    {
        $stmt = $this->db->prepare("UPDATE lease_renewals SET status = 'Rejected' WHERE renewal_id = :rid AND status = 'Pending'");
        return $stmt->execute(['rid' => $renewalId]);
    }
}
