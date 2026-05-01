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
            program_name VARCHAR(255) NOT NULL,
            status ENUM('pending', 'active', 'completed', 'dropped') DEFAULT 'pending',
            gender ENUM('male', 'female') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $this->db->exec($sql);
    }

    public function getAllByGender($gender) {
        $stmt = $this->db->prepare("SELECT * FROM islamic_education_enrollments WHERE gender = :gender ORDER BY created_at DESC");
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
