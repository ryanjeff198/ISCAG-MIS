<?php
/**
 * Migration: Fix column type + Add FK Constraints + Auto-Vacancy Trigger
 */
define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();
$results = [];

try {
    // Step 1: Clean orphaned references
    $db->exec("
        UPDATE apartment_units SET tenant_id = NULL, application_id = NULL, status = 'Available'
        WHERE tenant_id IS NOT NULL 
        AND tenant_id NOT IN (SELECT tenant_id FROM tenant_accounts)
    ");
    $results[] = "✓ Cleaned orphaned tenant references";

    // Step 2: Fix column type — VARCHAR(50) → INT NULL to match tenant_accounts.tenant_id
    $db->exec("ALTER TABLE apartment_units MODIFY COLUMN tenant_id INT NULL DEFAULT NULL");
    $results[] = "✓ Fixed tenant_id column type: VARCHAR(50) → INT NULL";

    // Step 3: Drop existing FKs (safe re-run)
    $fks = $db->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.TABLE_CONSTRAINTS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'apartment_units' 
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($fks as $fk) {
        if ($fk !== 'apartment_units_ibfk_1') { // Keep the type_id FK
            $db->exec("ALTER TABLE apartment_units DROP FOREIGN KEY `$fk`");
            $results[] = "✓ Dropped old FK: $fk";
        }
    }

    // Step 4: Add FK tenant_id → tenant_accounts ON DELETE SET NULL
    $db->exec("
        ALTER TABLE apartment_units
        ADD CONSTRAINT fk_unit_tenant
        FOREIGN KEY (tenant_id) REFERENCES tenant_accounts(tenant_id)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");
    $results[] = "✓ FK added: apartment_units.tenant_id → tenant_accounts (ON DELETE SET NULL)";

    // Step 5: Add FK application_id → apartmentsapp ON DELETE SET NULL
    $db->exec("
        ALTER TABLE apartment_units
        ADD CONSTRAINT fk_unit_application
        FOREIGN KEY (application_id) REFERENCES apartmentsapp(application_id)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");
    $results[] = "✓ FK added: apartment_units.application_id → apartmentsapp (ON DELETE SET NULL)";

    // Step 6: Create auto-vacancy trigger
    $db->exec("DROP TRIGGER IF EXISTS trg_auto_vacate_room");
    $db->exec("
        CREATE TRIGGER trg_auto_vacate_room
        BEFORE UPDATE ON apartment_units
        FOR EACH ROW
        BEGIN
            IF NEW.tenant_id IS NULL AND OLD.tenant_id IS NOT NULL THEN
                SET NEW.status = 'Available';
                SET NEW.application_id = NULL;
            END IF;
        END
    ");
    $results[] = "✓ Trigger created: trg_auto_vacate_room";

    echo "\n=== Migration Complete ===\n\n";
    foreach ($results as $r) echo "  $r\n";
    echo "\nGhost tenants will now be auto-cleaned when accounts are deleted.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n\nCompleted:\n";
    foreach ($results as $r) echo "  $r\n";
}
