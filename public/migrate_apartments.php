<?php
/**
 * Migration: Create Apartment Management Tables
 * Run via browser: /ISCAG-MIS/public/migrate_apartments.php
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

$sqls = [
    // 1. Apartment types (Studio, 1BR, 2BR, etc.)
    "CREATE TABLE IF NOT EXISTS apartment_types (
        type_id INT AUTO_INCREMENT PRIMARY KEY,
        type_key VARCHAR(20) UNIQUE NOT NULL,
        label VARCHAR(100) NOT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0,
        capacity VARCHAR(50),
        description TEXT,
        floor_area VARCHAR(20),
        bedrooms VARCHAR(50),
        bathroom VARCHAR(50),
        kitchen VARCHAR(50),
        parking VARCHAR(50),
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // 2. Images for each apartment type (gallery)
    "CREATE TABLE IF NOT EXISTS apartment_type_images (
        image_id INT AUTO_INCREMENT PRIMARY KEY,
        type_id INT NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        caption VARCHAR(100),
        is_thumbnail TINYINT(1) DEFAULT 0,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (type_id) REFERENCES apartment_types(type_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    // 3. Individual apartment units
    "CREATE TABLE IF NOT EXISTS apartment_units (
        unit_id INT AUTO_INCREMENT PRIMARY KEY,
        type_id INT NOT NULL,
        room_number VARCHAR(20) NOT NULL,
        status ENUM('Available','Occupied','Reserved','Maintenance','Inactive') DEFAULT 'Available',
        application_id INT NULL,
        tenant_id VARCHAR(50) NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (type_id) REFERENCES apartment_types(type_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

echo "<h2>Apartment Management — Database Migration</h2><pre>";

foreach ($sqls as $i => $sql) {
    try {
        $db->exec($sql);
        echo "✅ Table " . ($i + 1) . " created/verified successfully.\n";
    } catch (PDOException $e) {
        echo "❌ Table " . ($i + 1) . " error: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Migration Complete ---\n";

// ═══ SEED DATA ═══
echo "\n<h3>Seeding Apartment Types...</h3>\n";

$existingCount = $db->query("SELECT COUNT(*) FROM apartment_types")->fetchColumn();
if ($existingCount > 0) {
    echo "⚠️ Apartment types already seeded ($existingCount records). Skipping.\n";
} else {
    $types = [
        [
            'type_key' => 'studio',
            'label' => 'Studio Unit',
            'price' => 3500.00,
            'capacity' => '1-2 persons',
            'description' => 'A compact and efficient living space perfect for individuals or couples. The studio unit features an open-plan layout combining sleeping, living, and dining areas in one well-designed space, with a separate bathroom and a functional kitchenette.',
            'floor_area' => '22 sqm',
            'bedrooms' => 'Open-plan',
            'bathroom' => '1 (with shower)',
            'kitchen' => 'Kitchenette',
            'parking' => 'Shared lot',
            'sort_order' => 1,
            'images' => [
                ['file' => 'assets/Studio Type/Studio type front.jpg', 'caption' => 'Front View', 'thumb' => 1],
                ['file' => 'assets/Studio Type/Studio type 1.jpg', 'caption' => 'Living & Sleeping Area', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 2.jpg', 'caption' => 'Interior View 2', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 3.jpg', 'caption' => 'Interior View 3', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 4.jpg', 'caption' => 'Interior View 4', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 5.jpg', 'caption' => 'Interior View 5', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 6.jpg', 'caption' => 'Interior View 6', 'thumb' => 0],
                ['file' => 'assets/Studio Type/Studio type 7.jpg', 'caption' => 'Kitchen Area', 'thumb' => 0],
            ]
        ],
        [
            'type_key' => '1br',
            'label' => 'One-Bedroom Unit',
            'price' => 5000.00,
            'capacity' => '2-3 persons',
            'description' => 'A comfortable one-bedroom apartment ideal for small families or couples who prefer a separate sleeping area. Features a distinct living room, a private bedroom, a full bathroom, and a dining-kitchen area with ample counter space.',
            'floor_area' => '35 sqm',
            'bedrooms' => '1 (separate)',
            'bathroom' => '1 (with shower)',
            'kitchen' => 'Full kitchen',
            'parking' => 'Shared lot',
            'sort_order' => 2,
            'images' => [
                ['file' => 'assets/1BR Type/1BR front.jpg', 'caption' => 'Front View', 'thumb' => 1],
                ['file' => 'assets/1BR Type/1BR 1.jpg', 'caption' => 'Bedroom', 'thumb' => 0],
                ['file' => 'assets/1BR Type/1BR 2.jpg', 'caption' => 'Living & Dining Area', 'thumb' => 0],
                ['file' => 'assets/1BR Type/1BR 3.jpg', 'caption' => 'Interior View 3', 'thumb' => 0],
                ['file' => 'assets/1BR Type/1BR 4.jpg', 'caption' => 'Interior View 4', 'thumb' => 0],
            ]
        ],
        [
            'type_key' => '2br',
            'label' => 'Two-Bedroom Unit',
            'price' => 7500.00,
            'capacity' => '3-5 persons',
            'description' => 'A spacious two-bedroom apartment designed for growing families. Includes a master bedroom, a second bedroom, a full living and dining area, a complete kitchen, and a bathroom. Ideal for families seeking comfort and privacy within the community housing complex.',
            'floor_area' => '50 sqm',
            'bedrooms' => '2 (separate)',
            'bathroom' => '1 (with shower & tub)',
            'kitchen' => 'Full kitchen',
            'parking' => 'Dedicated slot',
            'sort_order' => 3,
            'images' => [
                ['file' => 'assets/2BR Type/2BR front.png', 'caption' => 'Front View', 'thumb' => 1],
                ['file' => 'assets/2BR Type/2BR 1.png', 'caption' => 'Living & Dining Area', 'thumb' => 0],
                ['file' => 'assets/2BR Type/2BR 3.png', 'caption' => 'Master Bedroom', 'thumb' => 0],
                ['file' => 'assets/2BR Type/2BR 4.png', 'caption' => 'Interior View', 'thumb' => 0],
            ]
        ]
    ];

    $insertType = $db->prepare("INSERT INTO apartment_types (type_key, label, price, capacity, description, floor_area, bedrooms, bathroom, kitchen, parking, sort_order) VALUES (:type_key, :label, :price, :capacity, :description, :floor_area, :bedrooms, :bathroom, :kitchen, :parking, :sort_order)");
    
    $insertImage = $db->prepare("INSERT INTO apartment_type_images (type_id, file_path, caption, is_thumbnail, sort_order) VALUES (:type_id, :file_path, :caption, :is_thumbnail, :sort_order)");

    foreach ($types as $type) {
        $images = $type['images'];
        unset($type['images']);
        
        $insertType->execute($type);
        $typeId = $db->lastInsertId();
        echo "✅ Created type: {$type['label']} (ID: $typeId)\n";

        foreach ($images as $i => $img) {
            $insertImage->execute([
                'type_id' => $typeId,
                'file_path' => $img['file'],
                'caption' => $img['caption'],
                'is_thumbnail' => $img['thumb'],
                'sort_order' => $i
            ]);
        }
        echo "   → Added " . count($images) . " images\n";
    }

    // Seed some sample units
    echo "\n<h3>Seeding Sample Units...</h3>\n";
    $insertUnit = $db->prepare("INSERT INTO apartment_units (type_id, room_number, status, description) VALUES (:type_id, :room_number, :status, :description)");
    
    $sampleUnits = [
        ['type_id' => 1, 'room_number' => '101-A', 'status' => 'Available', 'description' => 'Ground floor studio unit, near entrance'],
        ['type_id' => 1, 'room_number' => '102-A', 'status' => 'Occupied', 'description' => 'Ground floor studio unit'],
        ['type_id' => 1, 'room_number' => '201-A', 'status' => 'Available', 'description' => 'Second floor studio unit'],
        ['type_id' => 2, 'room_number' => '103-B', 'status' => 'Available', 'description' => 'Ground floor 1-bedroom unit'],
        ['type_id' => 2, 'room_number' => '203-B', 'status' => 'Reserved', 'description' => 'Second floor 1-bedroom unit'],
        ['type_id' => 3, 'room_number' => '104-C', 'status' => 'Occupied', 'description' => 'Ground floor 2-bedroom family unit'],
        ['type_id' => 3, 'room_number' => '204-C', 'status' => 'Available', 'description' => 'Second floor 2-bedroom family unit'],
    ];

    foreach ($sampleUnits as $unit) {
        $insertUnit->execute($unit);
        echo "✅ Unit: {$unit['room_number']} ({$unit['status']})\n";
    }
}

echo "\n</pre><p style='color:green;font-weight:bold;'>✅ All done! You can delete this file now.</p>";
