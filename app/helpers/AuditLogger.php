<?php

class AuditLogger {
    private static ?bool $hasRoleColumn = null;

    /**
     * Log an action to the audit trail
     *
     * @param string $module  e.g. 'APARTMENT', 'AUTH', 'PARKING', 'DAWAH', 'DAMAYAN', 'GOVERNANCE'
     * @param string $action  e.g. 'LOGIN', 'SUBMIT_APP', 'REJECT_MAINTENANCE'
     * @param string $details Detailed description of the action
     */
    public static function log(string $module, string $action, string $details): void {
        try {
            // Ensure we have access to the database connection
            if (!function_exists('getDbConnection')) {
                if (defined('BASE_PATH')) {
                    require_once BASE_PATH . '/config/database.php';
                } elseif (file_exists(__DIR__ . '/../../config/database.php')) {
                    require_once __DIR__ . '/../../config/database.php';
                }
            }
            
            $db = getDbConnection();
            
            // Fetch identity from session
            $userId   = $_SESSION['user_id'] ?? 0;
            $userName = $_SESSION['name'] ?? 'System';
            $userRole = $_SESSION['role'] ?? 'System';

            // Check if admin_role column exists in audit_logs table
            if (self::$hasRoleColumn === null) {
                try {
                    $check = $db->query("SHOW COLUMNS FROM audit_logs LIKE 'admin_role'")->fetch();
                    if (!$check) {
                        // Attempt to add column dynamically if permissions allow
                        @$db->exec("ALTER TABLE audit_logs ADD COLUMN admin_role VARCHAR(50) DEFAULT NULL AFTER admin_name");
                        $recheck = $db->query("SHOW COLUMNS FROM audit_logs LIKE 'admin_role'")->fetch();
                        self::$hasRoleColumn = (bool)$recheck;
                    } else {
                        self::$hasRoleColumn = true;
                    }
                } catch (\Throwable $t) {
                    self::$hasRoleColumn = false;
                }
            }

            if (self::$hasRoleColumn) {
                $stmt = $db->prepare("INSERT INTO audit_logs (admin_id, admin_name, admin_role, module, action, details) VALUES (:aid, :aname, :arole, :mod, :act, :det)");
                $stmt->execute([
                    'aid'   => $userId,
                    'aname' => $userName,
                    'arole' => $userRole,
                    'mod'   => strtoupper($module),
                    'act'   => strtoupper($action),
                    'det'   => $details
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO audit_logs (admin_id, admin_name, module, action, details) VALUES (:aid, :aname, :mod, :act, :det)");
                $stmt->execute([
                    'aid'   => $userId,
                    'aname' => $userName,
                    'mod'   => strtoupper($module),
                    'act'   => strtoupper($action),
                    'det'   => $details
                ]);
            }
        } catch (\Throwable $e) {
            // Silently fail to not block main operation, but log the error
            error_log("Audit Logger Failure [{$module}/{$action}]: " . $e->getMessage());
        }
    }
}
