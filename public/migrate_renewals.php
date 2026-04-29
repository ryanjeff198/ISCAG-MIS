<?php
/**
 * Migration: Create the `lease_renewals` table
 * Run once via browser: /Iscag/public/migrate_renewals.php
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

$sql = "
CREATE TABLE IF NOT EXISTS lease_renewals (
    renewal_id      INT AUTO_INCREMENT PRIMARY KEY,
    lease_id        INT NOT NULL,
    tenant_id       INT NOT NULL,
    status          ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_lease    (lease_id),
    INDEX idx_tenant   (tenant_id),
    INDEX idx_status   (status),

    CONSTRAINT fk_ren_lease FOREIGN KEY (lease_id)
        REFERENCES leases(lease_id) ON DELETE CASCADE,
    CONSTRAINT fk_ren_tenant FOREIGN KEY (tenant_id)
        REFERENCES tenant_accounts(tenant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "<h2 style='color:green;'>✅ `lease_renewals` table created successfully.</h2>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Migration failed:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
