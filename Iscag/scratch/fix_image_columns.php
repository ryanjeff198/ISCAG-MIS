<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

try {
    $db->exec('ALTER TABLE tenant_requirements MODIFY COLUMN picture LONGBLOB');
    echo "✓ picture -> LONGBLOB\n";
    
    $db->exec('ALTER TABLE tenant_requirements MODIFY COLUMN nbi LONGBLOB');
    echo "✓ nbi -> LONGBLOB\n";
    
    $db->exec('ALTER TABLE tenant_requirements MODIFY COLUMN proofofincome LONGBLOB');
    echo "✓ proofofincome -> LONGBLOB\n";
    
    echo "\nDone! All columns now support large image data.\n";
    echo "NOTE: The existing truncated data for tenant_id=4 is corrupted.\n";
    echo "The tenant will need to re-upload their picture, NBI, and proof of income.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
