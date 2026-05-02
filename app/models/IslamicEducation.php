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

        // Migration: Add birthdate column if it doesn't exist
        try {
            $this->db->exec("ALTER TABLE islamic_education_enrollments ADD COLUMN birthdate DATE NULL AFTER last_name");
        } catch (Exception $e) { /* ignore if column exists */ }
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
}
