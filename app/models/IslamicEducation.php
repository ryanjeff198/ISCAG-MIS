<?php

class IslamicEducation {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS islamic_education_enrollments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            birthdate DATE NULL,
            program_name VARCHAR(255) NOT NULL,
            status ENUM('pending', 'active', 'completed', 'dropped') DEFAULT 'pending',
            gender ENUM('male', 'female') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);

        // Migrations for Female Da'wah Registration fields from the official physical form
        $migrations = [
            "ADD COLUMN birthdate DATE NULL AFTER last_name",
            "ADD COLUMN middle_initial VARCHAR(10) NULL AFTER first_name",
            "ADD COLUMN muslim_name VARCHAR(150) NULL AFTER last_name",
            "ADD COLUMN muslim_type VARCHAR(50) DEFAULT 'Revert' AFTER muslim_name",
            "ADD COLUMN date_of_shahadah DATE NULL AFTER birthdate",
            "ADD COLUMN address TEXT NULL AFTER date_of_shahadah",
            "ADD COLUMN contact_number VARCHAR(50) NULL AFTER address",
            "ADD COLUMN facebook_account VARCHAR(150) NULL AFTER contact_number",
            "ADD COLUMN studied_islamic_before VARCHAR(10) DEFAULT 'No' AFTER facebook_account",
            "ADD COLUMN previous_school VARCHAR(255) NULL AFTER studied_islamic_before",
            "ADD COLUMN emergency_name VARCHAR(150) NULL AFTER previous_school",
            "ADD COLUMN emergency_relationship VARCHAR(100) NULL AFTER emergency_name",
            "ADD COLUMN emergency_address TEXT NULL AFTER emergency_relationship",
            "ADD COLUMN emergency_contact VARCHAR(50) NULL AFTER emergency_address",
            "ADD COLUMN is_minor TINYINT(1) DEFAULT 0 AFTER emergency_contact",
            "ADD COLUMN parent_name VARCHAR(150) NULL AFTER is_minor",
            "ADD COLUMN parent_address TEXT NULL AFTER parent_name",
            "ADD COLUMN parent_contact VARCHAR(50) NULL AFTER parent_address",
            "ADD COLUMN student_signature VARCHAR(150) NULL AFTER parent_contact",
            "ADD COLUMN guardian_signature VARCHAR(150) NULL AFTER student_signature",
            "ADD COLUMN batch_no VARCHAR(50) DEFAULT 'Batch 08' AFTER guardian_signature",
            "ADD COLUMN level VARCHAR(100) DEFAULT 'Basic' AFTER batch_no",
            "ADD COLUMN payment_method VARCHAR(50) DEFAULT 'Cash' AFTER level",
            "ADD COLUMN fee_amount DECIMAL(10,2) DEFAULT 120.00 AFTER payment_method",
            "ADD COLUMN payment_channel VARCHAR(100) NULL AFTER fee_amount",
            "ADD COLUMN payment_reference VARCHAR(100) NULL AFTER payment_channel",
            "ADD COLUMN approved_by VARCHAR(150) DEFAULT 'Sister Khadijah Dimaano' AFTER payment_reference",
            "ADD COLUMN prepared_by VARCHAR(150) DEFAULT 'IDFS Staff' AFTER approved_by",
            "ADD COLUMN payment_status VARCHAR(50) DEFAULT 'Pending' AFTER payment_reference"
        ];

        foreach ($migrations as $m) {
            try {
                $this->db->exec("ALTER TABLE islamic_education_enrollments " . $m);
            } catch (Exception $e) { /* Column already exists */ }
        }
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_enrollments 
            WHERE tenant_id = :tenant_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['tenant_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveForUser($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_enrollments 
            WHERE tenant_id = :tenant_id AND status IN ('pending', 'active')
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute(['tenant_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createRegistration($data) {
        $fields = [
            'tenant_id', 'first_name', 'last_name', 'middle_initial', 'muslim_name',
            'muslim_type', 'birthdate', 'date_of_shahadah', 'address', 'contact_number',
            'facebook_account', 'studied_islamic_before', 'previous_school',
            'emergency_name', 'emergency_relationship', 'emergency_address', 'emergency_contact',
            'is_minor', 'parent_name', 'parent_address', 'parent_contact',
            'student_signature', 'guardian_signature', 'batch_no', 'level',
            'payment_method', 'fee_amount', 'payment_channel', 'payment_reference', 'payment_status',
            'approved_by', 'prepared_by', 'program_name', 'gender', 'status'
        ];
        
        $insertCols = [];
        $insertVals = [];
        $params = [];

        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $insertCols[] = $f;
                $insertVals[] = ":$f";
                $params[$f] = $data[$f];
            }
        }

        $sql = "INSERT INTO islamic_education_enrollments (" . implode(', ', $insertCols) . ") 
                VALUES (" . implode(', ', $insertVals) . ")";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute($params);
        return $res ? (int)$this->db->lastInsertId() : 0;
    }

    public function getAllByGender($gender) {
        $stmt = $this->db->prepare("
            SELECT e.*, 
                   TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as age
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender 
            ORDER BY e.created_at DESC
        ");
        $stmt->execute(['gender' => $gender]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnalytics($gender) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
            FROM islamic_education_enrollments 
            WHERE gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'pending' => 0, 'completed' => 0, 'active' => 0];

        // Age Group Breakdown (Using COALESCE for fallback)
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 0 AND 12 THEN 1 ELSE 0 END) as children,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 13 AND 19 THEN 1 ELSE 0 END) as youth,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 20 AND 39 THEN 1 ELSE 0 END) as adults,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 40 AND 59 THEN 1 ELSE 0 END) as middle_aged,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) >= 60 THEN 1 ELSE 0 END) as seniors
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['age_groups'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Granular Age Breakdown for Active Students
        $stmt = $this->db->prepare("
            SELECT TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as specific_age,
                   COUNT(*) as count
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender AND e.status = 'active'
            GROUP BY specific_age
            HAVING specific_age IS NOT NULL
            ORDER BY specific_age ASC
        ");
        $stmt->execute(['gender' => $gender]);
        $stats['active_ages'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Monthly Enrollment by Age Group (Percentage based)
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(e.created_at, '%Y-%m') as month,
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 0 AND 12 THEN 'Children'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 13 AND 19 THEN 'Youth'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 20 AND 39 THEN 'Adults'
                    WHEN TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) BETWEEN 40 AND 59 THEN 'Middle-Aged'
                    ELSE 'Seniors'
                END as age_group,
                COUNT(*) as count
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.gender = :gender
            GROUP BY month, age_group
            ORDER BY month ASC
        ");
        $stmt->execute(['gender' => $gender]);
        $monthlyRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = [];
        foreach ($monthlyRaw as $row) {
            $m = $row['month'];
            if (!isset($monthlyData[$m])) $monthlyData[$m] = ['total' => 0, 'groups' => []];
            $monthlyData[$m]['groups'][$row['age_group']] = (int)$row['count'];
            $monthlyData[$m]['total'] += (int)$row['count'];
        }

        // Convert counts to percentages
        foreach ($monthlyData as $m => &$data) {
            foreach ($data['groups'] as $group => $count) {
                $data['groups'][$group] = round(($count / $data['total']) * 100, 1);
            }
        }
        $stats['monthly_demographics'] = $monthlyData;

        return $stats;
    }

    public function enroll($data) {
        $sql = "INSERT INTO islamic_education_enrollments (tenant_id, first_name, last_name, program_name, gender, status) 
                VALUES (:tenant_id, :first_name, :last_name, :program_name, :gender, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tenant_id' => $data['tenant_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'program_name' => $data['program_name'],
            'gender' => $data['gender'],
            'status' => $data['status'] ?? 'pending'
        ]);
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE islamic_education_enrollments SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT e.*, 
                   TIMESTAMPDIFF(YEAR, COALESCE(e.birthdate, p.birthdate), CURDATE()) as age,
                   t.email
            FROM islamic_education_enrollments e
            LEFT JOIN tenant_accounts t ON e.tenant_id = t.tenant_id
            LEFT JOIN tenant_user_profiles p ON e.tenant_id = p.tenant_id
            WHERE e.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePaymentInfo(int $id, array $data): bool {
        $allowed = ['payment_method', 'fee_amount', 'payment_channel', 'payment_reference', 'payment_status', 'status'];
        $sets = [];
        $params = ['id' => $id];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $sets[] = "$f = :$f";
                $params[$f] = $data[$f];
            }
        }
        if (empty($sets)) return false;
        $sql = "UPDATE islamic_education_enrollments SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
