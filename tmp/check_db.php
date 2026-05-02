<?php
$pdo = new PDO('mysql:host=localhost;dbname=iscag', 'root', '');

// Check if tenant_family_members table exists
$r = $pdo->query("SHOW TABLES LIKE 'tenant_family_members'");
$exists = $r->fetchAll();
echo "tenant_family_members table exists: " . (count($exists) > 0 ? "YES" : "NO") . "\n";

// Check tenant_addinfo columns
echo "\ntenant_addinfo columns:\n";
$cols = $pdo->query("SHOW COLUMNS FROM tenant_addinfo LIKE 'family_data'");
$fd = $cols->fetchAll(PDO::FETCH_ASSOC);
echo "family_data column exists: " . (count($fd) > 0 ? "YES" : "NO") . "\n";

// Check sample family_data content
$stmt = $pdo->query("SELECT tenant_id, family_data FROM tenant_addinfo WHERE family_data IS NOT NULL AND family_data != '' AND family_data != '[]' LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nSample family_data entries (" . count($rows) . " found):\n";
foreach ($rows as $row) {
    $decoded = json_decode($row['family_data'], true);
    $count = is_array($decoded) ? count($decoded) : 0;
    echo "  tenant_id={$row['tenant_id']}: {$count} members\n";
}

// If tenant_family_members exists, check its content
if (count($exists) > 0) {
    $stmt = $pdo->query("SELECT tenant_id, COUNT(*) as cnt FROM tenant_family_members GROUP BY tenant_id");
    $tfm = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\ntenant_family_members content (" . count($tfm) . " tenants):\n";
    foreach ($tfm as $row) {
        echo "  tenant_id={$row['tenant_id']}: {$row['cnt']} members\n";
    }
}
