<?php
/**
 * Migration: family_data JSON → tenant_family_members table
 * Safe: Does NOT delete family_data column. Writes to new table only.
 */
$pdo = new PDO('mysql:host=localhost;dbname=iscag', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// 1. Ensure the table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS tenant_family_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        name VARCHAR(255) NOT NULL DEFAULT '',
        relation VARCHAR(100) DEFAULT '',
        age INT DEFAULT NULL,
        religion VARCHAR(100) DEFAULT 'Islam',
        INDEX idx_tenant (tenant_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
echo "✓ Table tenant_family_members ensured.\n";

// 2. Clear any stale data in the new table (fresh migration)
$pdo->exec("TRUNCATE TABLE tenant_family_members");
echo "✓ Cleared old migration data.\n";

// 3. Read all family_data from tenant_addinfo
$rows = $pdo->query("
    SELECT tenant_id, family_data 
    FROM tenant_addinfo 
    WHERE family_data IS NOT NULL 
      AND family_data != '' 
      AND family_data != '[]'
")->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($rows) . " tenant(s) with family data to migrate.\n\n";

$insertStmt = $pdo->prepare("
    INSERT INTO tenant_family_members (tenant_id, name, relation, age, religion)
    VALUES (:tid, :name, :relation, :age, :religion)
");

$totalMigrated = 0;
foreach ($rows as $row) {
    $tid = $row['tenant_id'];
    $members = json_decode($row['family_data'], true);
    
    if (!is_array($members) || empty($members)) {
        echo "  tenant_id={$tid}: Skipped (invalid JSON or empty)\n";
        continue;
    }
    
    $count = 0;
    foreach ($members as $m) {
        $name = trim($m['name'] ?? '');
        if ($name === '') continue; // Skip empty rows
        
        $insertStmt->execute([
            'tid'      => $tid,
            'name'     => $name,
            'relation' => $m['relation'] ?? '',
            'age'      => !empty($m['age']) ? (int)$m['age'] : null,
            'religion' => $m['religion'] ?? 'Islam'
        ]);
        $count++;
    }
    
    echo "  tenant_id={$tid}: Migrated {$count} member(s)\n";
    $totalMigrated += $count;
}

echo "\n══════════════════════════════\n";
echo "Migration complete: {$totalMigrated} total family member(s) migrated.\n";

// 4. Verify
$verify = $pdo->query("SELECT tenant_id, COUNT(*) as cnt FROM tenant_family_members GROUP BY tenant_id")->fetchAll(PDO::FETCH_ASSOC);
echo "\nVerification (tenant_family_members):\n";
foreach ($verify as $v) {
    echo "  tenant_id={$v['tenant_id']}: {$v['cnt']} member(s)\n";
}
echo "\n✓ Done. family_data column in tenant_addinfo is UNTOUCHED (safe).\n";
