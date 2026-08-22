<?php

/**
 * ConversionRequest Model
 * Handles all database operations related to Conversion to Islam / Shahadah applications.
 */
class ConversionRequest
{
    protected string $table = 'conversion_requests';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        
        // Auto-create table if missing
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            fname VARCHAR(100) NOT NULL,
            mname VARCHAR(100),
            lname VARCHAR(100) NOT NULL,
            adopted_name VARCHAR(255) NOT NULL,
            sex VARCHAR(20) DEFAULT 'Male',
            civil_status VARCHAR(50),
            citizenship VARCHAR(50) DEFAULT 'Filipino',
            dob DATE,
            age INT,
            occupation VARCHAR(100),
            former_religion VARCHAR(100),
            pob VARCHAR(255),
            residence VARCHAR(255),
            father_name VARCHAR(255),
            father_religion VARCHAR(100),
            mother_name VARCHAR(255),
            mother_religion VARCHAR(100),
            conversion_date DATE NOT NULL,
            witness1_name VARCHAR(255),
            witness1_address VARCHAR(255),
            witness2_name VARCHAR(255),
            witness2_address VARCHAR(255),
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

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, t.first_name, t.last_name, t.email 
            FROM {$this->table} c
            LEFT JOIN tenant_accounts t ON c.tenant_id = t.tenant_id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }
}
