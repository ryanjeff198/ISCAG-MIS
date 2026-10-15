<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/app/helpers/Security.php';
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDbConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS move_out_requests (
        request_id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        unit_id INT NOT NULL,
        move_out_date DATE DEFAULT NULL,
        inspection_notes TEXT DEFAULT NULL,
        damage_costs DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        utility_deductions DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        final_refund DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status VARCHAR(20) NOT NULL DEFAULT 'Pending',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_moveout_tenant (tenant_id),
        INDEX idx_moveout_unit (unit_id),
        INDEX idx_moveout_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    echo "move_out_requests table created or verified successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
