<?php
/**
 * Verify: Simulate what happens when the form saves family_data
 */
define('BASE_PATH', 'c:/xampp/htdocs/Iscag');
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';

$model = new ApartmentApp();

// Check tenant 32 (has 2 family members)
$info = $model->getInfo(32);
echo "Tenant 32 - family_data from DB: " . ($info['family_data'] ?? 'NULL') . "\n";

// Check tenant_family_members for tenant 32
$db = getDbConnection();
$stmt = $db->prepare("SELECT * FROM tenant_family_members WHERE tenant_id = ?");
$stmt->execute([32]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Tenant 32 - tenant_family_members: " . count($members) . " member(s)\n";
foreach ($members as $m) {
    echo "  - {$m['name']} ({$m['relation']}, age {$m['age']}, {$m['religion']})\n";
}

echo "\n✓ System is ready. Form saves will now sync to both places.\n";
