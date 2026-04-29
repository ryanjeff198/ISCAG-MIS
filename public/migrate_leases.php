<?php
/**
 * Migration: Create the `leases` table
 * Run once via browser: /Iscag/public/migrate_leases.php
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

$sql = "
CREATE TABLE IF NOT EXISTS leases (
    lease_id        INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id       INT NOT NULL,
    application_id  INT NOT NULL,
    unit_type       VARCHAR(100) DEFAULT NULL COMMENT 'Preferred room type from application',
    monthly_rent    DECIMAL(10,2) DEFAULT 0.00,
    deposit_amount  DECIMAL(10,2) DEFAULT 0.00,
    advance_amount  DECIMAL(10,2) DEFAULT 0.00,
    start_date      DATE DEFAULT NULL,
    end_date        DATE DEFAULT NULL,
    lease_status    ENUM('Pending','Accepted','Rejected','Active','Expired') DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_tenant   (tenant_id),
    INDEX idx_app      (application_id),
    INDEX idx_status   (lease_status),

    CONSTRAINT fk_lease_tenant FOREIGN KEY (tenant_id)
        REFERENCES tenant_accounts(tenant_id) ON DELETE CASCADE,
    CONSTRAINT fk_lease_app FOREIGN KEY (application_id)
        REFERENCES apartmentsapp(application_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "<h2 style='color:green;'>✅ `leases` table created successfully.</h2>";
    
    // Show table structure
    $cols = $db->query("DESCRIBE leases")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    foreach ($cols as $col) {
        echo $col['Field'] . " — " . $col['Type'] . " — " . ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . " — " . ($col['Key'] ?: '-') . "\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Migration failed:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
