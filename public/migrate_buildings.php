<?php
/**
 * Migration: Add Building Column & Seed 125 Rooms
 * Run via browser: /Iscag/public/migrate_buildings.php
 * 
 * Building 1: 12 rooms
 * Building 2: 18 rooms
 * Building 3: 32 rooms
 * Building 4: 31 rooms
 * Building 5: 32 rooms
 * Total: 125 rooms
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

echo "<h2>Building Migration — Add Building Column & Seed 125 Rooms</h2><pre>";

// ═══ 1. Add building column if not exists ═══
try {
    $cols = $db->query("SHOW COLUMNS FROM apartment_units LIKE 'building'")->fetchAll();
    if (empty($cols)) {
        $db->exec("ALTER TABLE apartment_units ADD COLUMN building VARCHAR(30) DEFAULT NULL AFTER room_number");
        echo "✅ Added 'building' column to apartment_units\n";
    } else {
        echo "⚠️ 'building' column already exists, skipping ALTER.\n";
    }
} catch (PDOException $e) {
    echo "❌ Error adding column: " . $e->getMessage() . "\n";
}

// ═══ 2. Verify apartment types ═══
$types = $db->query("SELECT type_id, type_key, label FROM apartment_types WHERE is_active = 1 ORDER BY type_id")->fetchAll(PDO::FETCH_ASSOC);
echo "\n--- Existing Apartment Types ---\n";
$typeIds = [];
foreach ($types as $t) {
    echo "  ID={$t['type_id']}  key={$t['type_key']}  label={$t['label']}\n";
    $typeIds[] = $t['type_id'];
}

if (count($typeIds) < 1) {
    echo "❌ No apartment types found! Please run migrate_apartments.php first.\n";
    echo "</pre>";
    exit;
}

echo "\nFound " . count($typeIds) . " type(s) to distribute across rooms.\n";

// ═══ 3. Clear old sample units (optional but clean) ═══
$existingCount = $db->query("SELECT COUNT(*) FROM apartment_units")->fetchColumn();
if ($existingCount > 0) {
    echo "\n⚠️ Found $existingCount existing units. Clearing them for fresh seed...\n";
    $db->exec("DELETE FROM apartment_units");
    $db->exec("ALTER TABLE apartment_units AUTO_INCREMENT = 1");
    echo "✅ Cleared old units.\n";
}

// ═══ 4. Seed 125 rooms across 5 buildings ═══
$buildings = [
    'Building 1' => 12,
    'Building 2' => 18,
    'Building 3' => 32,
    'Building 4' => 31,
    'Building 5' => 32,
];

$statuses = ['Available', 'Available', 'Available', 'Occupied', 'Reserved', 'Maintenance'];

$insertUnit = $db->prepare(
    "INSERT INTO apartment_units (type_id, room_number, building, status, description) 
     VALUES (:type_id, :room_number, :building, :status, :description)"
);

$totalInserted = 0;

foreach ($buildings as $buildingName => $roomCount) {
    $buildingNum = (int) str_replace('Building ', '', $buildingName);
    echo "\n--- $buildingName ($roomCount rooms) ---\n";

    for ($i = 1; $i <= $roomCount; $i++) {
        // Random type from all available types
        $randomTypeId = $typeIds[array_rand($typeIds)];
        
        // Room number format: B{building}-{sequence padded}
        $roomNumber = "B{$buildingNum}-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        
        // Random status (weighted toward Available)
        $status = $statuses[array_rand($statuses)];
        
        // Type label for description
        $typeLabel = '';
        foreach ($types as $t) {
            if ($t['type_id'] == $randomTypeId) {
                $typeLabel = $t['label'];
                break;
            }
        }
        
        $description = "$buildingName, Floor " . ceil($i / 8) . " — $typeLabel";

        $insertUnit->execute([
            'type_id'     => $randomTypeId,
            'room_number' => $roomNumber,
            'building'    => $buildingName,
            'status'      => $status,
            'description' => $description,
        ]);

        $totalInserted++;
        echo "  ✅ $roomNumber → $typeLabel ($status)\n";
    }
}

echo "\n═══════════════════════════════════\n";
echo "✅ Total rooms inserted: $totalInserted\n";

// ═══ 5. Summary verification ═══
echo "\n--- Verification ---\n";
$summary = $db->query("
    SELECT u.building, COUNT(*) as total, 
           SUM(CASE WHEN u.status = 'Available' THEN 1 ELSE 0 END) as available,
           SUM(CASE WHEN u.status = 'Occupied' THEN 1 ELSE 0 END) as occupied,
           SUM(CASE WHEN u.status = 'Reserved' THEN 1 ELSE 0 END) as reserved,
           SUM(CASE WHEN u.status = 'Maintenance' THEN 1 ELSE 0 END) as maintenance
    FROM apartment_units u
    GROUP BY u.building
    ORDER BY u.building
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($summary as $row) {
    echo "  {$row['building']}: {$row['total']} total | {$row['available']} avail | {$row['occupied']} occ | {$row['reserved']} res | {$row['maintenance']} maint\n";
}

$grandTotal = $db->query("SELECT COUNT(*) FROM apartment_units")->fetchColumn();
echo "\n  Grand Total: $grandTotal rooms\n";

echo "\n</pre><p style='color:green;font-weight:bold;'>✅ Building migration complete!</p>";
