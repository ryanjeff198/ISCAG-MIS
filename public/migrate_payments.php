<?php
/**
 * Migration: Create the `payments` table
 * Run once via browser: /Iscag/public/migrate_payments.php
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

$sql = "
CREATE TABLE IF NOT EXISTS payments (
    payment_id      INT AUTO_INCREMENT PRIMARY KEY,
    lease_id        INT NOT NULL,
    tenant_id       INT NOT NULL,
    payment_type    ENUM('Deposit', 'Advance') NOT NULL,
    amount          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status  ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
    payment_date    DATETIME DEFAULT NULL,
    reference_number VARCHAR(100) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_lease    (lease_id),
    INDEX idx_tenant   (tenant_id),
    INDEX idx_status   (payment_status),

    CONSTRAINT fk_paym_lease FOREIGN KEY (lease_id)
        REFERENCES leases(lease_id) ON DELETE CASCADE,
    CONSTRAINT fk_paym_tenant FOREIGN KEY (tenant_id)
        REFERENCES tenant_accounts(tenant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "<h2 style='color:green;'>✅ `payments` table created successfully.</h2>";
    
    // Show table structure
    $cols = $db->query("DESCRIBE payments")->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    foreach ($cols as $col) {
        echo $col['Field'] . " — " . $col['Type'] . " — " . ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . " — " . ($col['Key'] ?: '-') . "\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>❌ Migration failed:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
