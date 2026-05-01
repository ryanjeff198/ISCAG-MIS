<?php

require_once BASE_PATH . '/config/database.php';

class AdminNotification
{
    protected string $table = 'admin_notifications';
    protected PDO $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->initTable();
    }

    private function initTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'system',
            actor_name VARCHAR(255) DEFAULT 'System',
            actor_id INT DEFAULT NULL,
            source_url VARCHAR(500) DEFAULT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    /**
     * Create a new admin notification.
     */
    public function create(string $title, string $message, string $type = 'system', string $actorName = 'System', ?int $actorId = null, ?string $sourceUrl = null): bool
    {
        $sql = "INSERT INTO {$this->table} (title, message, type, actor_name, actor_id, source_url) VALUES (:title, :message, :type, :actor_name, :actor_id, :source_url)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'actor_name' => $actorName,
            'actor_id'   => $actorId,
            'source_url' => $sourceUrl
        ]);
    }

    /**
     * Fetch all admin notifications (newest first).
     */
    public function getAll(int $limit = 100): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get unread count.
     */
    public function getUnreadCount(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE is_read = 0";
        return $this->db->exec($sql) !== false;
    }
}
