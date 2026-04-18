<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

$columns = [
    'picture',
    'valididfront',
    'valididback',
    'birthcert',
    'nbi',
    'proofofincome'
];

echo "Starting Database Migration for tenant_requirements...\n\n";

try {
    foreach ($columns as $col) {
        echo "Migrating $col -> LONGBLOB... ";
        $db->exec("ALTER TABLE tenant_requirements MODIFY COLUMN $col LONGBLOB");
        echo "✓ Success\n";

        // Also ensure _mime columns exist and are varchar
        $mimeCol = $col . '_mime';
        echo "Ensuring $mimeCol exists... ";
        // Check if column exists first
        $q = $db->query("SHOW COLUMNS FROM tenant_requirements LIKE '$mimeCol'");
        if ($q->rowCount() == 0) {
            $db->exec("ALTER TABLE tenant_requirements ADD COLUMN $mimeCol VARCHAR(100) AFTER $col");
            echo "✓ Added\n";
        } else {
            echo "✓ Already exists\n";
        }
    }
    
    echo "\n--------------------------------------------------\n";
    echo "Done! All columns now safely support document uploads.\n";
    echo "IMPORTANT: Users with corrupted images must re-upload them.\n";
    echo "--------------------------------------------------\n";

} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
