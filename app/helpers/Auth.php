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
     * Protect route: Hide with redirect if not authenticated.
     */
    public static function protect(): void
    {
        if (!self::check()) {
            header('Location: ' . url('/login'));
            exit;
        }
    }

    /**
     * Protect route by role: Hide with redirect if role doesn't match.
     */
    public static function protectRole(string|array $roles): void
    {
        self::protect();
        if (!self::hasRole($roles)) {
            // Redirect unauthorized roles back to their own dashboard
            $role = self::role();
            if ($role === 'Admin') {
                header('Location: ' . url('/admin/dashboard'));
            } elseif ($role === 'Staff_Tenant') {
                header('Location: ' . url('/admin/apartment'));
            } elseif ($role === 'Staff_Male') {
                header('Location: ' . url('/admin/dawah/male'));
            } elseif ($role === 'Staff_Female') {
                header('Location: ' . url('/admin/dawah/female'));
            } elseif ($role === 'Staff_Damayan') {
                header('Location: ' . url('/admin/damayan'));
            } else {
                header('Location: ' . url('/user/dashboard'));
            }
            exit;
        }
    }
}
