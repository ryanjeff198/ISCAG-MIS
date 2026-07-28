<?php

/**
 * MarriageRequest Model
 * Handles all database operations related to marriage applications.
 */
class MarriageRequest
{
    protected string $table = 'marriage_requests';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        
        // Auto-create table if missing
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            groom_name VARCHAR(255) NOT NULL,
            bride_name VARCHAR(255) NOT NULL,
            marriage_date DATE NOT NULL,
            marriage_time TIME NOT NULL,
            marriage_venue VARCHAR(255) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id)
        )");
    }

    public function getAnalytics(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
            FROM {$this->table}
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'pending' => 0, 'approved' => 0];
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

    public function create(array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, t.first_name, t.last_name, t.email,
                   TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE()) as age
            FROM {$this->table} r
            JOIN tenant_accounts t ON r.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON t.tenant_id = p.tenant_id
            ORDER BY r.marriage_date ASC, r.marriage_time ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => strtolower($status), 'id' => $id]);
    }
}
