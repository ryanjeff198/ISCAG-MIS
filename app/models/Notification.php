<?php

require_once BASE_PATH . '/config/database.php';

class Notification
{
    protected string $table = 'notifications';
    protected PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->initTable();
    }

    private function initTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            notification_id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'system',
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id) ON DELETE CASCADE
        )";
        $this->db->exec($sql);
    }

    public function create(int $tenantId, string $title, string $message, string $type = 'system'): bool
    {
        $sql = "INSERT INTO {$this->table} (tenant_id, title, message, type) VALUES (:tenant_id, :title, :message, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'tenant_id' => $tenantId,
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function getUserNotifications(int $tenantId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE tenant_id = :tenant_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount(int $tenantId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE tenant_id = :tenant_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return (int) $stmt->fetchColumn();
    }

    public function markAsRead(int $id, int $tenantId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE notification_id = :id AND tenant_id = :tenant_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId
        ]);
    }

    public function markAllAsRead(int $tenantId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE tenant_id = :tenant_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['tenant_id' => $tenantId]);
    }
}
