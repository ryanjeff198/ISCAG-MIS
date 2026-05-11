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
            registered_by INT NULL,
            relationship VARCHAR(50) DEFAULT 'self',
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            middle_initial VARCHAR(10) NULL,
            muslim_name VARCHAR(100) NULL,
            birthdate DATE NULL,
            islamic_status ENUM('revert', 'born') NULL,
            shahadah_date DATE NULL,
            previous_education TEXT NULL,
            home_address TEXT NULL,
            facebook_account VARCHAR(255) NULL,
            contact_number VARCHAR(20) NULL,
            emergency_contact_name VARCHAR(100) NULL,
            emergency_contact_relationship VARCHAR(50) NULL,
            emergency_contact_no VARCHAR(20) NULL,
            guardian_name VARCHAR(100) NULL,
            guardian_mobile VARCHAR(20) NULL,
            program_name VARCHAR(255) NOT NULL,
            payment_method VARCHAR(50) NULL,
            status ENUM('pending', 'active', 'completed', 'dropped') DEFAULT 'pending',
            gender ENUM('male', 'female') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id) ON DELETE SET NULL,
            FOREIGN KEY (registered_by) REFERENCES tenant_accounts(tenant_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);

        // Migration logic for existing tables
        $columnsToAdd = [
            'registered_by' => "INT NULL AFTER tenant_id",
            'relationship' => "VARCHAR(50) DEFAULT 'self' AFTER registered_by",
            'middle_initial' => "VARCHAR(10) NULL AFTER last_name",
            'muslim_name' => "VARCHAR(100) NULL AFTER middle_initial",
            'islamic_status' => "ENUM('revert', 'born') NULL AFTER birthdate",
            'shahadah_date' => "DATE NULL AFTER islamic_status",
            'previous_education' => "TEXT NULL AFTER shahadah_date",
            'home_address' => "TEXT NULL AFTER previous_education",
            'facebook_account' => "VARCHAR(255) NULL AFTER home_address",
            'contact_number' => "VARCHAR(20) NULL AFTER facebook_account",
            'emergency_contact_name' => "VARCHAR(100) NULL AFTER contact_number",
            'emergency_contact_relationship' => "VARCHAR(50) NULL AFTER emergency_contact_name",
            'emergency_contact_no' => "VARCHAR(20) NULL AFTER emergency_contact_relationship",
            'guardian_name' => "VARCHAR(100) NULL AFTER emergency_contact_no",
            'guardian_mobile' => "VARCHAR(20) NULL AFTER guardian_name",
            'payment_method' => "VARCHAR(50) NULL AFTER program_name"
        ];

        foreach ($columnsToAdd as $col => $definition) {
            try {
                $this->db->exec("ALTER TABLE islamic_education_enrollments ADD COLUMN $col $definition");
            } catch (Exception $e) { /* column likely exists */ }
        }
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

    public function getByRegistrant($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM islamic_education_enrollments 
            WHERE registered_by = :uid1 OR tenant_id = :uid2
            ORDER BY created_at DESC
        ");
        $stmt->execute(['uid1' => $userId, 'uid2' => $userId]);
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
        $sql = "INSERT INTO islamic_education_enrollments (
                    tenant_id, registered_by, relationship, first_name, last_name, 
                    middle_initial, muslim_name, birthdate, islamic_status, 
                    shahadah_date, previous_education, home_address, facebook_account, 
                    contact_number, emergency_contact_name, emergency_contact_relationship, 
                    emergency_contact_no, guardian_name, guardian_mobile, 
                    program_name, payment_method, gender, status
                ) VALUES (
                    :tenant_id, :registered_by, :relationship, :first_name, :last_name, 
                    :middle_initial, :muslim_name, :birthdate, :islamic_status, 
                    :shahadah_date, :previous_education, :home_address, :facebook_account, 
                    :contact_number, :emergency_contact_name, :emergency_contact_relationship, 
                    :emergency_contact_no, :guardian_name, :guardian_mobile, 
                    :program_name, :payment_method, :gender, :status
                )";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tenant_id' => $data['tenant_id'] ?? null,
            'registered_by' => $data['registered_by'] ?? null,
            'relationship' => $data['relationship'] ?? 'self',
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_initial' => $data['middle_initial'] ?? null,
            'muslim_name' => $data['muslim_name'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'islamic_status' => $data['islamic_status'] ?? null,
            'shahadah_date' => $data['shahadah_date'] ?? null,
            'previous_education' => $data['previous_education'] ?? null,
            'home_address' => $data['home_address'] ?? null,
            'facebook_account' => $data['facebook_account'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
            'emergency_contact_no' => $data['emergency_contact_no'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'guardian_mobile' => $data['guardian_mobile'] ?? null,
            'program_name' => $data['program_name'],
            'payment_method' => $data['payment_method'] ?? null,
            'gender' => $data['gender'],
            'status' => $data['status'] ?? 'pending'
        ]);
    }
}
