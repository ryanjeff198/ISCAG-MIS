<?php
require_once BASE_PATH . '/config/database.php';

class Maintenance {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function create($tenantId, $data) {
        $sql = "INSERT INTO tenant_maintenance (tenant_id, category, description, attachment) VALUES (:tid, :cat, :desc, :att)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tid'  => $tenantId,
            'cat'  => $data['category'],
            'desc' => $data['description'],
            'att'  => $data['attachment'] ?? null
        ]);
    }

    public function getAllPending() {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum 
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                WHERE m.status = 'Pending'
                ORDER BY m.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum 
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                ORDER BY m.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $remarks = null) {
        $sql = "UPDATE tenant_maintenance SET status = :status, admin_remarks = :remarks WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status'  => $status,
            'remarks' => $remarks,
            'id'      => $id
        ]);
    }

    public function getByTenant($tenantId) {
        $sql = "SELECT * FROM tenant_maintenance WHERE tenant_id = :tid ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tid' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum 
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update the apartment unit status for a tenant.
     * Used to sync maintenance approval/resolution with room status.
     *
     * @param int    $tenantId  The tenant whose unit to update
     * @param string $status    The new unit status (e.g. 'Maintenance', 'Occupied')
     * @return bool
     */
    public function setUnitStatus(int $tenantId, string $status): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE apartment_units SET status = :status WHERE tenant_id = :tid"
        );
        return $stmt->execute(['status' => $status, 'tid' => $tenantId]);
    }
}
