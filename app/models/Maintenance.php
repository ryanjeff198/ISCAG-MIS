<?php
require_once BASE_PATH . '/config/database.php';

class Maintenance {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS `tenant_maintenance` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `tenant_id` int(11) NOT NULL,
            `category` varchar(50) NOT NULL,
            `description` text NOT NULL,
            `attachment` varchar(255) DEFAULT NULL,
            `status` enum('Pending','In Progress','Completed','Rejected') DEFAULT 'Pending',
            `admin_remarks` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `tenant_id` (`tenant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        try {
            $this->db->exec($sql);
        } catch (Throwable $e) {
            // Silently fail if table already exists or driver error
        }
    }

    public function create($tenantId, $data) {
        $status = $data['status'] ?? 'Pending';
        $remarks = $data['admin_remarks'] ?? null;
        $sql = "INSERT INTO tenant_maintenance (tenant_id, category, description, attachment, status, admin_remarks) VALUES (:tid, :cat, :desc, :att, :status, :remarks)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            'tid'     => $tenantId,
            'cat'     => $data['category'],
            'desc'    => $data['description'],
            'att'     => $data['attachment'] ?? null,
            'status'  => $status,
            'remarks' => $remarks
        ]);
        return $ok ? (int)$this->db->lastInsertId() : false;
    }

    public function getAllPending() {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum,
                       au.building, au.room_number
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                LEFT JOIN (
                    SELECT tenant_id, MAX(building) AS building, MAX(room_number) AS room_number
                    FROM apartment_units
                    GROUP BY tenant_id
                ) au ON m.tenant_id = au.tenant_id
                WHERE m.status = 'Pending'
                ORDER BY m.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum,
                       au.building, au.room_number
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                LEFT JOIN (
                    SELECT tenant_id, MAX(building) AS building, MAX(room_number) AS room_number
                    FROM apartment_units
                    GROUP BY tenant_id
                ) au ON m.tenant_id = au.tenant_id
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
        $sql = "SELECT m.*, u.first_name, u.last_name, u.email, u.contactnum,
                       au.building, au.room_number
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                LEFT JOIN (
                    SELECT tenant_id, MAX(building) AS building, MAX(room_number) AS room_number
                    FROM apartment_units
                    GROUP BY tenant_id
                ) au ON m.tenant_id = au.tenant_id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTenantsWithUnits(): array {
        $sql = "SELECT u.tenant_id, u.first_name, u.last_name, u.role, u.email,
                       au.building, au.room_number
                FROM tenant_accounts u
                LEFT JOIN (
                    SELECT tenant_id, MAX(building) AS building, MAX(room_number) AS room_number
                    FROM apartment_units
                    GROUP BY tenant_id
                ) au ON u.tenant_id = au.tenant_id
                WHERE u.role IN ('Tenant', 'Guest', 'Admin', 'Staff_Tenant')
                ORDER BY (au.room_number IS NULL) ASC, u.role = 'Tenant' DESC, u.first_name ASC";
        try {
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            return [];
        }
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
