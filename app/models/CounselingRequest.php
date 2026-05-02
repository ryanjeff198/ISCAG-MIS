<?php

/**
 * CounselingRequest Model
 * Handles all database operations related to counseling requests.
 */
class CounselingRequest
{
    protected string $table = 'counseling_requests';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        
        // Auto-create table if missing
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            gender ENUM('male', 'female') NOT NULL,
            reason VARCHAR(255) NOT NULL,
            preferred_date DATE,
            preferred_time VARCHAR(20),
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id)
        )");
    }

    public function create(array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getAnalytics(string $gender): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM {$this->table}
            WHERE gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

        // Get breakdown by concern (reason)
        $stmt = $this->db->prepare("
            SELECT reason, COUNT(*) as count 
            FROM {$this->table} 
            WHERE gender = :gender 
            GROUP BY reason 
            ORDER BY count DESC
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['concerns'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $stats;
    }

    public function getByGender(string $gender): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, t.first_name, t.last_name, t.email,
                   TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE()) as age
            FROM {$this->table} r
            JOIN tenant_accounts t ON r.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON t.tenant_id = p.tenant_id
            WHERE r.gender = :gender
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE tenant_id = :id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => strtolower($status), 'id' => $id]);
    }
}
