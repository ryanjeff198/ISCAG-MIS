<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Auth Helper
 * Handles session-based authentication and RBAC checks.
 */
class Auth
{
    /**
     * Check if user is logged in.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get logged-in user's role (Dynamic).
     */
    public static function role(): ?string
    {
        if (!isset($_SESSION['user_id'])) return null;
        
        // Live Role Check: Fetch from DB to allow instant updates from Admin
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT role FROM tenant_accounts WHERE tenant_id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $role = $stmt->fetchColumn();
        
        if ($role) {
            $_SESSION['role'] = $role; // Sync session with latest DB value
            return $role;
        }
        
        return $_SESSION['role'] ?? null;
    }

    /**
     * Check if user has a specific role.
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!self::check()) return false;
        
        $userRole = self::role();
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }

    /**
     * Protect route: Hide with 404 if not authenticated.
     */
    public static function protect(): void
    {
        if (!self::check()) {
            // Temporarily disabled for debugging
            // require_once BASE_PATH . '/app/controllers/ErrorController.php';
            // ErrorController::show404();
        }
    }

    /**
     * Protect route by role: Hide with 404 if role doesn't match.
     */
    public static function protectRole(string|array $roles): void
    {
        self::protect();
        if (!self::hasRole($roles)) {
            // Temporarily disabled for debugging
            // require_once BASE_PATH . '/app/controllers/ErrorController.php';
            // ErrorController::show404();
        }
    }
}
