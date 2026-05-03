<?php
require_once BASE_PATH . '/config/database.php';

class MoveOut {
    private PDO $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function createRequest(int $tenantId, int $unitId, ?string $moveOutDate = null): bool {
        // Check if there is already a pending request
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM move_out_requests WHERE tenant_id = ? AND status = 'Pending'");
        $stmt->execute([$tenantId]);
        if ($stmt->fetchColumn() > 0) return false;

        $stmt = $this->db->prepare("INSERT INTO move_out_requests (tenant_id, unit_id, move_out_date) VALUES (?, ?, ?)");
        return $stmt->execute([$tenantId, $unitId, $moveOutDate]);
    }

    public function getAllRequests(): array {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.email, un.room_number 
                FROM move_out_requests r
                JOIN tenant_accounts u ON r.tenant_id = u.tenant_id
                JOIN apartment_units un ON r.unit_id = un.unit_id
                ORDER BY r.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function updateStatus(int $requestId, string $status): bool {
        $stmt = $this->db->prepare("UPDATE move_out_requests SET status = ? WHERE request_id = ?");
        return $stmt->execute([$status, $requestId]);
    }

    public function finalizeSettlement(int $requestId, array $data): bool {
        $sql = "UPDATE move_out_requests 
                SET inspection_notes = :notes, 
                    damage_costs = :damage, 
                    utility_deductions = :utility, 
                    final_refund = :refund,
                    status = 'Completed',
                    updated_at = CURRENT_TIMESTAMP
                WHERE request_id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'      => $requestId,
            'notes'   => $data['notes'] ?? '',
            'damage'  => $data['damage'] ?? 0,
            'utility' => $data['utility'] ?? 0,
            'refund'  => $data['refund'] ?? 0
        ]);
    }

    public function getRequestById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT r.*, u.first_name, u.last_name, u.email, un.room_number 
                                    FROM move_out_requests r
                                    JOIN tenant_accounts u ON r.tenant_id = u.tenant_id
                                    JOIN apartment_units un ON r.unit_id = un.unit_id
                                    WHERE r.request_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
