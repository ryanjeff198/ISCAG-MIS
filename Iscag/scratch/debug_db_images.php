<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
try {
    $res = $db->query("SELECT tenant_id, 
        (picture IS NOT NULL AND LENGTH(picture) > 0) as has_picture,
        (valididfront IS NOT NULL AND LENGTH(valididfront) > 0) as has_id_front
        FROM tenant_requirements");
    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
    echo "Records found: " . count($rows) . "\n";
    foreach($rows as $r) {
        echo "User {$r['tenant_id']}: Picture=" . ($r['has_picture'] ? 'YES' : 'NO') . ", ID Front=" . ($r['has_id_front'] ? 'YES' : 'NO') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
