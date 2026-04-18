<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

try {
    $stmt = $db->prepare("UPDATE tenant_requirements SET picture = NULL, picture_mime = NULL, nbi = NULL, nbi_mime = NULL, proofofincome = NULL, proofofincome_mime = NULL WHERE tenant_id = 4");
    $stmt->execute();
    echo "Cleared corrupted image data for tenant_id=4\n";
    echo "The tenant will need to re-upload their picture, NBI, and proof of income.\n";
    
    // Verify the fix
    echo "\n=== Verify column types ===\n";
    $res = $db->query("DESCRIBE tenant_requirements");
    $cols = $res->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
