<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();

echo "TENANT APPLICATIONS:\n";
$q = $db->query("SELECT tenant_id, status FROM apartment_record_confirmations LIMIT 10");
while($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $r['tenant_id'] . " Status: " . $r['status'] . "\n";
    // Check their requirements
    $q2 = $db->prepare("SELECT 
        picture_mime, valididfront_mime, birthcert_mime, nbi_mime, proofofincome_mime
        FROM tenant_requirements WHERE tenant_id = ?");
    $q2->execute([$r['tenant_id']]);
    $req = $q2->fetch(PDO::FETCH_ASSOC);
    if ($req) {
        foreach ($req as $k => $v) {
            if ($v) echo "  - $k: $v\n";
        }
    } else {
        echo "  - No requirements found in tenant_requirements table.\n";
    }
}
