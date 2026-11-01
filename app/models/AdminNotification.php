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
     * Get allowed notification types based on user role.
     */
    public static function getAllowedTypesForRole(?string $role): array
    {
        switch ($role) {
            case 'Staff_Tenant':
                return ['apartment', 'apartment_application', 'parking_application', 'contract_renewal', 'moveout_request', 'maintenance', 'payment', 'request'];
            case 'Staff_Damayan':
                return ['damayan', 'burial', 'charity', 'funeral', 'cemetery', 'death_certificate', 'payment'];
            case 'Staff_Male':
                return ['counseling', 'marriage', 'conversion', 'dawah', 'male_counseling', 'death_certificate'];
            case 'Staff_Female':
                return ['counseling', 'education', 'female_education', 'female_counseling', 'dawah', 'female_payment', 'female_education_payment'];
            case 'Admin':
            default:
                return [];
        }
    }

    /**
     * Fetch admin notifications filtered by role (newest first).
     */
    public function getAllForRole(?string $role = null, int $limit = 100): array
    {
        if (!$role || $role === 'Admin') {
            return $this->getAll($limit);
        }

        $types = self::getAllowedTypesForRole($role);
        if (empty($types)) {
            return $this->getAll($limit);
        }

        $placeholders = implode(',', array_fill(0, count($types), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE (type IN ($placeholders) OR type = 'system' OR type IS NULL) ORDER BY created_at DESC LIMIT " . (int)$limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($types);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get overall unread count.
     */
    public function getUnreadCount(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    /**
     * Get unread count for a specific role.
     */
    public function getUnreadCountForRole(?string $role = null): int
    {
        if (!$role || $role === 'Admin') {
            return $this->getUnreadCount();
        }

        $types = self::getAllowedTypesForRole($role);
        if (empty($types)) {
            return $this->getUnreadCount();
        }

        $placeholders = implode(',', array_fill(0, count($types), '?'));
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_read = 0 AND (type IN ($placeholders) OR type = 'system' OR type IS NULL)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($types);
        return (int) $stmt->fetchColumn();
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

    /**
     * Mark all notifications as read for a specific role.
     */
    public function markAllAsReadForRole(?string $role = null): bool
    {
        if (!$role || $role === 'Admin') {
            return $this->markAllAsRead();
        }

        $types = self::getAllowedTypesForRole($role);
        if (empty($types)) {
            return $this->markAllAsRead();
        }

        $placeholders = implode(',', array_fill(0, count($types), '?'));
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE is_read = 0 AND (type IN ($placeholders) OR type = 'system' OR type IS NULL)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($types);
    }
}
