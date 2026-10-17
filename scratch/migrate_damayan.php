<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

echo "Running Damayan database migration...\n";

// 1. Add missing columns to burial_requests if they don't exist
$existingCols = $db->query("DESCRIBE burial_requests")->fetchAll(PDO::FETCH_COLUMN);

$columnsToAdd = [
    'deceased_first_name' => "VARCHAR(100) NULL AFTER deceased_name",
    'deceased_middle_name' => "VARCHAR(100) NULL AFTER deceased_first_name",
    'deceased_last_name' => "VARCHAR(100) NULL AFTER deceased_middle_name",
    'deceased_sex' => "ENUM('Male', 'Female') DEFAULT 'Male' AFTER deceased_last_name",
    'civil_status' => "VARCHAR(50) NULL AFTER residence",
    'nationality' => "VARCHAR(100) DEFAULT 'Filipino' AFTER civil_status",
    'informant_name' => "VARCHAR(200) NULL AFTER relationship",
    'informant_contact' => "VARCHAR(50) NULL AFTER informant_name",
    'informant_email' => "VARCHAR(200) NULL AFTER informant_contact",
    'informant_address' => "TEXT NULL AFTER informant_email",
    'requested_services' => "TEXT NULL AFTER informant_address",
    'cemetery_preference' => "VARCHAR(255) NULL AFTER requested_services",
    'target_timing' => "VARCHAR(255) NULL AFTER cemetery_preference",
    'additional_notes' => "TEXT NULL AFTER target_timing",
    'supporting_document' => "VARCHAR(255) NULL AFTER additional_notes",
    'assigned_staff_id' => "INT NULL AFTER supporting_document",
    'assigned_staff_name' => "VARCHAR(200) NULL AFTER assigned_staff_id",
    'info_request_notes' => "TEXT NULL AFTER assigned_staff_name",
    'info_requested_at' => "DATETIME NULL AFTER info_request_notes",
    'additional_info_submitted' => "TEXT NULL AFTER info_requested_at",
    'additional_doc_path' => "VARCHAR(255) NULL AFTER additional_info_submitted",
    'info_submitted_at' => "DATETIME NULL AFTER additional_doc_path",
    'rejection_reason' => "TEXT NULL AFTER info_submitted_at",
    'actual_burial_date' => "DATETIME NULL AFTER rejection_reason",
    'final_cemetery' => "VARCHAR(255) NULL AFTER actual_burial_date",
    'final_remarks' => "TEXT NULL AFTER final_cemetery",
    'completed_at' => "DATETIME NULL AFTER final_remarks",
    'completed_by' => "VARCHAR(200) NULL AFTER completed_at",
    'updated_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER submitted_at"
];

foreach ($columnsToAdd as $col => $def) {
    if (!in_array($col, $existingCols)) {
        try {
            $db->exec("ALTER TABLE burial_requests ADD COLUMN $col $def");
            echo "Added column $col to burial_requests.\n";
        } catch (\PDOException $e) {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column $col already exists in burial_requests.\n";
    }
}

// 2. Create deceased_records table
$db->exec("CREATE TABLE IF NOT EXISTS deceased_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    burial_request_id INT NULL,
    ref_id VARCHAR(50) NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    sex ENUM('Male','Female') NOT NULL DEFAULT 'Male',
    date_of_birth DATE NULL,
    date_of_death DATE NOT NULL,
    time_of_death TIME NULL,
    place_of_death VARCHAR(255) NULL,
    residence_address TEXT NULL,
    civil_status VARCHAR(50) NULL,
    nationality VARCHAR(100) DEFAULT 'Filipino',
    religion VARCHAR(100) DEFAULT 'Islam',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (burial_request_id),
    INDEX (ref_id)
)");
echo "Table deceased_records ensured.\n";

// 3. Create burial_coordinations table
$db->exec("CREATE TABLE IF NOT EXISTS burial_coordinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    burial_request_id INT NOT NULL UNIQUE,
    -- Ghusl
    ghusl_status ENUM('Pending', 'Scheduled', 'Volunteers Confirmed', 'In Progress', 'Completed') DEFAULT 'Pending',
    ghusl_date DATE NULL,
    ghusl_time TIME NULL,
    ghusl_venue VARCHAR(255) DEFAULT 'ISCAG Ghusl Facility',
    ghusl_volunteer_gender ENUM('Male', 'Female') DEFAULT 'Male',
    ghusl_assigned_volunteers TEXT NULL,
    ghusl_volunteer_status VARCHAR(50) DEFAULT 'Pending',
    ghusl_completed_at DATETIME NULL,
    ghusl_remarks TEXT NULL,
    
    -- Kafan
    kafan_status ENUM('Pending', 'In Preparation', 'Ready', 'Completed') DEFAULT 'Pending',
    kafan_type VARCHAR(100) DEFAULT 'Standard 3-piece shroud',
    kafan_prepared_by VARCHAR(200) NULL,
    kafan_completed_at DATETIME NULL,
    kafan_remarks TEXT NULL,

    -- Janazah
    janazah_status ENUM('Pending', 'Scheduled', 'Completed') DEFAULT 'Pending',
    janazah_date DATE NULL,
    janazah_time TIME NULL,
    janazah_prayer_time VARCHAR(100) DEFAULT 'After Dhuhr',
    janazah_imam_name VARCHAR(200) NULL,
    janazah_imam_contact VARCHAR(50) NULL,
    janazah_masjid VARCHAR(255) DEFAULT 'ISCAG Main Masjid',
    janazah_remarks TEXT NULL,
    janazah_completed_at DATETIME NULL,

    -- Transportation
    transport_status ENUM('Pending', 'Scheduled', 'En Route', 'Arrived', 'Completed') DEFAULT 'Pending',
    transport_vehicle_type VARCHAR(100) DEFAULT 'ISCAG Ambulance',
    transport_driver_name VARCHAR(200) NULL,
    transport_driver_contact VARCHAR(50) NULL,
    transport_pickup_location VARCHAR(255) NULL,
    transport_destination VARCHAR(255) NULL,
    transport_scheduled_datetime DATETIME NULL,
    transport_actual_departure DATETIME NULL,
    transport_actual_arrival DATETIME NULL,
    transport_remarks TEXT NULL,
    transport_completed_at DATETIME NULL,

    -- Cemetery
    cemetery_status ENUM('Pending', 'Plot Reserved', 'Scheduled', 'Completed') DEFAULT 'Pending',
    cemetery_name VARCHAR(255) DEFAULT 'ISCAG Designated Muslim Cemetery',
    cemetery_plot_number VARCHAR(100) NULL,
    cemetery_scheduled_date DATE NULL,
    cemetery_scheduled_time TIME NULL,
    actual_burial_date DATETIME NULL,
    cemetery_remarks TEXT NULL,
    burial_completed_at DATETIME NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (burial_request_id) REFERENCES burial_requests(id) ON DELETE CASCADE
)");
echo "Table burial_coordinations ensured.\n";

// 4. Create burial_volunteers table
$db->exec("CREATE TABLE IF NOT EXISTS burial_volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(200) NULL,
    role VARCHAR(100) DEFAULT 'Ghusl Volunteer',
    availability_status ENUM('Available', 'Busy', 'Unavailable') DEFAULT 'Available',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "Table burial_volunteers ensured.\n";

// Seed volunteers if table is empty
$volCount = (int)$db->query("SELECT COUNT(*) FROM burial_volunteers")->fetchColumn();
if ($volCount === 0) {
    $volunteers = [
        ['name' => 'Tariq Al-Hassan', 'gender' => 'Male', 'phone' => '0917-111-2233', 'email' => 'tariq.h@gmail.com', 'role' => 'Lead Ghusl Washer (Brother)', 'availability_status' => 'Available'],
        ['name' => 'Rashid Omar', 'gender' => 'Male', 'phone' => '0918-222-3344', 'email' => 'rashid.o@gmail.com', 'role' => 'Ghusl Assistant (Brother)', 'availability_status' => 'Available'],
        ['name' => 'Bilal Ibrahim', 'gender' => 'Male', 'phone' => '0919-333-4455', 'email' => 'bilal.i@gmail.com', 'role' => 'Ghusl Assistant (Brother)', 'availability_status' => 'Available'],
        ['name' => 'Farooq Ahmad', 'gender' => 'Male', 'phone' => '0920-444-5566', 'email' => 'farooq.a@gmail.com', 'role' => 'Ghusl Assistant (Brother)', 'availability_status' => 'Available'],
        ['name' => 'Maryam Binti Yusuf', 'gender' => 'Female', 'phone' => '0921-555-6677', 'email' => 'maryam.y@gmail.com', 'role' => 'Lead Ghusl Washer (Sister)', 'availability_status' => 'Available'],
        ['name' => 'Aminah Al-Zahra', 'gender' => 'Female', 'phone' => '0922-666-7788', 'email' => 'aminah.z@gmail.com', 'role' => 'Ghusl Assistant (Sister)', 'availability_status' => 'Available'],
        ['name' => 'Fatima Noor', 'gender' => 'Female', 'phone' => '0923-777-8899', 'email' => 'fatima.n@gmail.com', 'role' => 'Ghusl Assistant (Sister)', 'availability_status' => 'Available'],
        ['name' => 'Zaynab Abdullah', 'gender' => 'Female', 'phone' => '0924-888-9900', 'email' => 'zaynab.a@gmail.com', 'role' => 'Ghusl Assistant (Sister)', 'availability_status' => 'Available']
    ];
    $stmt = $db->prepare("INSERT INTO burial_volunteers (name, gender, phone, email, role, availability_status) VALUES (:name, :gender, :phone, :email, :role, :availability_status)");
    foreach ($volunteers as $v) {
        $stmt->execute($v);
    }
    echo "Seeded " . count($volunteers) . " initial burial volunteers.\n";
}

// 5. Create burial_activity_logs table
$db->exec("CREATE TABLE IF NOT EXISTS burial_activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    burial_request_id INT NOT NULL,
    ref_id VARCHAR(50) NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    details TEXT NULL,
    actor_name VARCHAR(200) DEFAULT 'Staff Admin',
    actor_role VARCHAR(100) DEFAULT 'Staff_Damayan',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (burial_request_id),
    INDEX (ref_id)
)");
echo "Table burial_activity_logs ensured.\n";

echo "Damayan migration completed successfully!\n";
