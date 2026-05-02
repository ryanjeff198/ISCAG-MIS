<?php

class AuditLogger {
    /**
     * Log an action to the audit trail
     *
     * @param string $module  e.g. 'APARTMENT', 'AUTH', 'PARKING'
     * @param string $action  e.g. 'LOGIN', 'SUBMIT_APP', 'REJECT_MAINTENANCE'
     * @param string $details Detailed description of the action
     */
    public static function log(string $module, string $action, string $details): void {
        try {
            // Ensure we have access to the database connection
            if (!function_exists('getDbConnection')) {
                require_once BASE_PATH . '/config/database.php';
            }
            
            $db = getDbConnection();
            $stmt = $db->prepare("INSERT INTO audit_logs (admin_id, admin_name, admin_role, module, action, details) VALUES (:aid, :aname, :arole, :mod, :act, :det)");
            
            // Fetch identity from session
            $userId   = $_SESSION['user_id'] ?? 0;
            $userName = $_SESSION['name'] ?? 'System';
            $userRole = $_SESSION['role'] ?? 'System';

            $stmt->execute([
                'aid'   => $userId,
                'aname' => $userName,
                'arole' => $userRole,
                'mod'   => strtoupper($module),
                'act'   => strtoupper($action),
                'det'   => $details
            ]);
        } catch (\Exception $e) {
            // Silently fail to not block main operation, but log the error
            error_log("Audit Logger Failure [{$module}/{$action}]: " . $e->getMessage());
        }
    }
}
