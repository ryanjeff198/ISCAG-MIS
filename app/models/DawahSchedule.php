<?php

/**
 * DawahSchedule Model
 * Manages manually assigned events such as Classes, Seminars, and Special Sessions.
 */
class DawahSchedule
{
    protected string $table = 'dawah_manual_schedules';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date DATE NOT NULL,
            event_time TIME NOT NULL,
            department ENUM('male', 'female') NOT NULL,
            event_type VARCHAR(50) DEFAULT 'Class',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (title, description, event_date, event_time, department, event_type) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['event_date'],
            $data['event_time'],
            $data['department'],
            $data['event_type'] ?? 'Class'
        ]);
    }

    public function getByDepartment(string $dept): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE department = ? ORDER BY event_date ASC, event_time ASC");
        $stmt->execute([$dept]);
        return $stmt->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
