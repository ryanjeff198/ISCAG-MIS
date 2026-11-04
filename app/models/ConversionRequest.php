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
            preferred_time VARCHAR(50),
            venue VARCHAR(255),
            officiant VARCHAR(255),
            notes TEXT,
            witness1_name VARCHAR(255),
            witness1_address VARCHAR(255),
            witness2_name VARCHAR(255),
            witness2_address VARCHAR(255),
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id)
        )");

        // Ensure newly introduced columns exist on already-created tables
        try {
            $cols = [
                'preferred_time' => 'VARCHAR(50) DEFAULT NULL',
                'venue'          => 'VARCHAR(255) DEFAULT NULL',
                'officiant'      => 'VARCHAR(255) DEFAULT NULL',
                'notes'          => 'TEXT DEFAULT NULL'
            ];
            foreach ($cols as $col => $def) {
                $check = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '{$col}'")->fetch();
                if (!$check) {
                    @$this->db->exec("ALTER TABLE {$this->table} ADD COLUMN {$col} {$def}");
                }
            }
        } catch (\Throwable $t) {}
    }

    public function create(array $data): bool
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function createAndGetId(array $data): ?int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($data)) {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }

    public function findDuplicate(string $fname, string $lname, ?string $dob, int $tenantId): ?array
    {
        $query = "
            SELECT id, status FROM {$this->table}
            WHERE ((LOWER(TRIM(fname)) = LOWER(:fname) AND LOWER(TRIM(lname)) = LOWER(:lname)" . ($dob ? " AND dob = :dob" : "") . ")
               OR (tenant_id = :tenant_id AND LOWER(TRIM(fname)) = LOWER(:fname) AND LOWER(TRIM(lname)) = LOWER(:lname)))
              AND LOWER(status) NOT IN ('rejected', 'cancelled')
            LIMIT 1
        ";
        $stmt = $this->db->prepare($query);
        $params = [
            'fname' => trim($fname),
            'lname' => trim($lname),
            'tenant_id' => $tenantId
        ];
        if ($dob) {
            $params['dob'] = $dob;
        }
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findPendingByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE tenant_id = :id AND LOWER(status) IN ('pending', 'under_review')
            LIMIT 1
        ");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
        $stmt = $this->db->prepare("
            SELECT c.*, t.first_name, t.last_name, t.email 
            FROM {$this->table} c
            LEFT JOIN tenant_accounts t ON c.tenant_id = t.tenant_id
            WHERE c.id = :id
        ");
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

    public function updateSchedule(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['conversion_date'])) {
            $fields[] = "conversion_date = :conversion_date";
            $params['conversion_date'] = $data['conversion_date'];
        }
        if (isset($data['preferred_time'])) {
            $fields[] = "preferred_time = :preferred_time";
            $params['preferred_time'] = $data['preferred_time'];
        }
        if (isset($data['venue'])) {
            $fields[] = "venue = :venue";
            $params['venue'] = $data['venue'];
        }
        if (isset($data['officiant'])) {
            $fields[] = "officiant = :officiant";
            $params['officiant'] = $data['officiant'];
        }
        if (isset($data['notes'])) {
            $fields[] = "notes = :notes";
            $params['notes'] = $data['notes'];
        }
        if (!empty($data['status'])) {
            $fields[] = "status = :status";
            $params['status'] = $data['status'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
