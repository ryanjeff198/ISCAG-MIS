<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
try {
    $res = $db->query("SELECT tenant_id, 
        LENGTH(picture) as pic_len, picture_mime,
        LENGTH(valididfront) as id_len, valididfront_mime
        FROM tenant_requirements WHERE tenant_id = 4");
    $row = $res->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "User ID 4:\n";
        echo "Picture Length: " . ($row['pic_len'] ?? 'NULL') . " bytes\n";
        echo "Picture MIME: " . ($row['picture_mime'] ?? 'NULL') . "\n";
        echo "ID Front Length: " . ($row['id_len'] ?? 'NULL') . " bytes\n";
        echo "ID Front MIME: " . ($row['valididfront_mime'] ?? 'NULL') . "\n";
    } else {
        echo "No record found for User ID 4 in tenant_requirements.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
