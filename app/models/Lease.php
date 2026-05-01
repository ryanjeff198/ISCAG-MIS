<?php
require_once BASE_PATH . '/config/database.php';

/**
 * Lease Model
 * Handles CRUD operations for the `leases` table.
 * Lease is independent of payment/room assignment logic.
 */
class Lease
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    // ─── CREATE ────────────────────────────────────────────

    /**
     * Auto-generate a lease when an application is approved.
     *
     * @param array $data [tenant_id, application_id, unit_type, monthly_rent, deposit_amount, advance_amount, start_date, end_date]
     * @return int|false  The new lease_id on success, false on failure
     */
    public function createLease(array $data)
    {
        // Prevent duplicates for the same application
        $existing = $this->getLeaseByApplicationId($data['application_id']);
        if ($existing) {
            return $existing['lease_id'];
        }

        $sql = "INSERT INTO leases 
                (tenant_id, application_id, unit_type, monthly_rent, deposit_amount, advance_amount, start_date, end_date, lease_status)
                VALUES 
                (:tenant_id, :application_id, :unit_type, :monthly_rent, :deposit_amount, :advance_amount, :start_date, :end_date, 'Pending')";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'tenant_id'       => $data['tenant_id'],
            'application_id'  => $data['application_id'],
            'unit_type'       => $data['unit_type']       ?? null,
            'monthly_rent'    => $data['monthly_rent']    ?? 0,
            'deposit_amount'  => $data['deposit_amount']  ?? 0,
            'advance_amount'  => $data['advance_amount']  ?? 0,
            'start_date'      => $data['start_date']      ?? null,
            'end_date'        => $data['end_date']        ?? null,
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    // ─── READ ──────────────────────────────────────────────

    /**
     * Get the latest lease for a tenant.
     */
    public function getLeaseByTenantId(int $tenantId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, a.roomtype, a.status AS app_status,
                   u.first_name, u.last_name, u.email, u.contactnum,
                   au.building, au.room_number
            FROM leases l
            JOIN apartmentsapp a ON l.application_id = a.application_id
            JOIN tenant_accounts u ON l.tenant_id = u.tenant_id
            LEFT JOIN apartment_units au ON l.tenant_id = au.tenant_id
            WHERE l.tenant_id = :tid
            ORDER BY l.lease_id DESC
            LIMIT 1
        ");
        $stmt->execute(['tid' => $tenantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get lease by application_id.
     */
    public function getLeaseByApplicationId(int $applicationId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM leases WHERE application_id = :aid LIMIT 1");
        $stmt->execute(['aid' => $applicationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get lease by lease_id.
     */
    public function getLeaseById(int $leaseId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT l.*, a.roomtype, a.status AS app_status,
                   u.first_name, u.last_name, u.email, u.contactnum
            FROM leases l
            JOIN apartmentsapp a ON l.application_id = a.application_id
            JOIN tenant_accounts u ON l.tenant_id = u.tenant_id
            WHERE l.lease_id = :lid
            LIMIT 1
        ");
        $stmt->execute(['lid' => $leaseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * List all leases (admin view - future use).
     */
    public function getAllLeases(): array
    {
        $sql = "
            SELECT l.*, a.roomtype,
                   u.first_name, u.last_name, u.email
            FROM leases l
            JOIN apartmentsapp a ON l.application_id = a.application_id
            JOIN tenant_accounts u ON l.tenant_id = u.tenant_id
            ORDER BY l.lease_id DESC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ─── UPDATE ────────────────────────────────────────────

    /**
     * Update lease status.
     * Validated statuses: Pending, Accepted, Rejected, Active, Expired
     */
    public function updateLeaseStatus(int $leaseId, string $status): bool
    {
        $valid = ['Pending', 'Accepted', 'Rejected', 'Active', 'Expired'];
        if (!in_array($status, $valid)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE leases SET lease_status = :status WHERE lease_id = :id");
        return $stmt->execute(['status' => $status, 'id' => $leaseId]);
    }

    /**
     * Accept lease — tenant action.
     * Changes status from Pending → Accepted.
     */
    public function acceptLease(int $leaseId, int $tenantId, int $term = 12): bool
    {
        $stmt = $this->db->prepare("
            UPDATE leases 
            SET lease_status = 'Accepted',
                end_date = DATE_ADD(start_date, INTERVAL :term MONTH)
            WHERE lease_id = :lid AND tenant_id = :tid AND lease_status = 'Pending'
        ");
        return $stmt->execute(['lid' => $leaseId, 'tid' => $tenantId, 'term' => $term]);
    }

    /**
     * Reject lease — tenant action.
     * Changes status from Pending → Rejected.
     */
    public function rejectLease(int $leaseId, int $tenantId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE leases 
            SET lease_status = 'Rejected' 
            WHERE lease_id = :lid AND tenant_id = :tid AND lease_status = 'Pending'
        ");
        return $stmt->execute(['lid' => $leaseId, 'tid' => $tenantId]);
    }
}
