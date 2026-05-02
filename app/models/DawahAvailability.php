<?php

/**
 * DawahAvailability Model
 * Manages blocked/unavailable dates for department services.
 */
class DawahAvailability
{
    protected string $table = 'dawah_availability';
    protected PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            blocked_date DATE NOT NULL,
            reason VARCHAR(255),
            department ENUM('male', 'female') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_date_dept (blocked_date, department)
        )");

        // Migrate: fix old tables that had UNIQUE on blocked_date alone
        try {
            $this->db->exec("ALTER TABLE {$this->table} DROP INDEX blocked_date");
        } catch (\Exception $e) { /* index doesn't exist, ok */ }
        try {
            $this->db->exec("ALTER TABLE {$this->table} ADD UNIQUE KEY unique_date_dept (blocked_date, department)");
        } catch (\Exception $e) { /* already exists, ok */ }
    }

    public function blockDate(string $date, string $dept, string $reason = ''): bool
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (blocked_date, department, reason) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE reason = VALUES(reason)");
        return $stmt->execute([$date, $dept, $reason]);
    }

    public function unblockDate(string $date, string $dept): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE blocked_date = ? AND department = ?");
        return $stmt->execute([$date, $dept]);
    }

    public function getBlockedDates(string $dept): array
    {
        $stmt = $this->db->prepare("SELECT blocked_date, reason FROM {$this->table} WHERE department = ?");
        $stmt->execute([$dept]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
