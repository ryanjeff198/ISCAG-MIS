<?php
require_once 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();

$sqls = [
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS inclusions TEXT",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS rules TEXT",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS security_deposit VARCHAR(100) DEFAULT '1 Month'",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS advance_rent VARCHAR(100) DEFAULT '1 Month'",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS other_fees VARCHAR(255)",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS min_lease VARCHAR(100) DEFAULT '3 Months'",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS notice_period VARCHAR(100) DEFAULT '25th day'",
    "ALTER TABLE apartment_types ADD COLUMN IF NOT EXISTS queue_label VARCHAR(50)"
];

foreach ($sqls as $sql) {
    try {
        $db->exec($sql);
        echo "✅ Success: $sql\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
