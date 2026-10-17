<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

$db = getDbConnection();

echo "Running Damayan Death Certificate and Cemetery Plot Migration...\n";

// 1. Create cemetery_plots table
$db->exec("CREATE TABLE IF NOT EXISTS cemetery_plots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cemetery_name VARCHAR(255) NOT NULL DEFAULT 'ISCAG Designated Muslim Cemetery',
    section VARCHAR(100) NOT NULL DEFAULT 'Section A',
    plot_number VARCHAR(100) NOT NULL,
    status ENUM('Available', 'Reserved', 'Occupied', 'Unavailable') DEFAULT 'Available',
    assigned_deceased_name VARCHAR(255) NULL,
    assigned_burial_request_id INT NULL,
    assigned_tenant_id INT NULL,
    assigned_date DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_plot (cemetery_name, section, plot_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
echo "cemetery_plots table checked/created.\n";

// 2. Add Death Certificate & Cemetery Plot columns to burial_requests
$cols = $db->query("DESCRIBE burial_requests")->fetchAll(PDO::FETCH_COLUMN);

$columnsToAdd = [
    'death_cert_requested' => "TINYINT(1) DEFAULT 1",
    'death_cert_status' => "ENUM('Request Received','Under Review','Additional Information Required','Awaiting Authorized Certificate Preparation','Certificate Prepared and Certified','Submitted for Civil Registration','Registered','Certified Copy Requested or Received','Rejected') DEFAULT 'Request Received'",
    'death_cert_prep_date' => "DATE NULL",
    'death_cert_certified_date' => "DATE NULL",
    'death_cert_submitted_lcro_date' => "DATE NULL",
    'death_cert_registered_date' => "DATE NULL",
    'death_cert_registry_no' => "VARCHAR(100) NULL",
    'death_cert_lcro_ref' => "VARCHAR(100) NULL",
    'death_cert_certified_copy_ref' => "VARCHAR(100) NULL",
    'death_cert_responsible_personnel' => "VARCHAR(200) NULL",
    'death_cert_remarks' => "TEXT NULL",
    'death_cert_rejection_reason' => "TEXT NULL",
    'cemetery_plot_requested' => "TINYINT(1) DEFAULT 1",
    'cemetery_plot_status' => "ENUM('Pending Review','Under Review','Available for Assignment','Approved','Plot Assigned','Awaiting Plot Availability','Unavailable','Rejected') DEFAULT 'Pending Review'",
    'cemetery_plot_id' => "INT NULL",
    'assigned_cemetery_name' => "VARCHAR(255) NULL",
    'assigned_plot_number' => "VARCHAR(100) NULL",
    'assigned_section' => "VARCHAR(100) NULL",
    'plot_assigned_date' => "DATETIME NULL",
    'cemetery_plot_remarks' => "TEXT NULL",
    'cemetery_plot_rejection_reason' => "TEXT NULL",
    'time_of_death' => "TIME NULL",
    'request_type' => "ENUM('both','death_certificate','cemetery_plot') DEFAULT 'both'"
];

foreach ($columnsToAdd as $col => $definition) {
    if (!in_array($col, $cols)) {
        try {
            $db->exec("ALTER TABLE burial_requests ADD COLUMN $col $definition");
            echo "Added column: $col\n";
        } catch (\PDOException $e) {
            echo "Error adding $col: " . $e->getMessage() . "\n";
        }
    }
}

// 3. Seed cemetery plots if empty
$plotCount = (int)$db->query("SELECT COUNT(*) FROM cemetery_plots")->fetchColumn();
if ($plotCount === 0) {
    echo "Seeding initial cemetery plots...\n";
    $insertPlot = $db->prepare("INSERT IGNORE INTO cemetery_plots (cemetery_name, section, plot_number, status, notes) VALUES (:cname, :sec, :plot, :st, :notes)");
    
    // ISCAG Cemetery - Section A
    for ($i = 1; $i <= 12; $i++) {
        $pNum = sprintf("A-%02d", $i);
        $st = ($i === 1 || $i === 2) ? 'Occupied' : 'Available';
        $dec = ($i === 1) ? 'Sulaiman Tariq Macarambon' : null;
        $insertPlot->execute([
            'cname' => 'ISCAG Designated Muslim Cemetery',
            'sec' => 'Section A',
            'plot' => $pNum,
            'st' => $st,
            'notes' => 'Sunnah orientation (facing Qiblah)'
        ]);
    }
    // ISCAG Cemetery - Section B
    for ($i = 1; $i <= 12; $i++) {
        $pNum = sprintf("B-%02d", $i);
        $insertPlot->execute([
            'cname' => 'ISCAG Designated Muslim Cemetery',
            'sec' => 'Section B',
            'plot' => $pNum,
            'st' => 'Available',
            'notes' => 'Sunnah orientation'
        ]);
    }
    // Norzagaray Islamic Cemetery - Block 1
    for ($i = 1; $i <= 8; $i++) {
        $pNum = sprintf("N1-%02d", $i);
        $insertPlot->execute([
            'cname' => 'Norzagaray Muslim Cemetery, Bulacan',
            'sec' => 'Block 1',
            'plot' => $pNum,
            'st' => 'Available',
            'notes' => 'Caretaker Brother Hassan'
        ]);
    }
    // Taguig Muslim Cemetery - Area 1
    for ($i = 1; $i <= 6; $i++) {
        $pNum = sprintf("T1-%02d", $i);
        $insertPlot->execute([
            'cname' => 'Taguig Muslim Cemetery',
            'sec' => 'Area 1',
            'plot' => $pNum,
            'st' => 'Available',
            'notes' => 'Taguig Community Muslim Section'
        ]);
    }
    echo "Seeded cemetery plots successfully.\n";
} else {
    echo "cemetery_plots table already has $plotCount records.\n";
}

// 4. Update existing requests to have death_cert_status and cemetery_plot_status matching their current status
$db->exec("UPDATE burial_requests SET death_cert_status = 'Request Received' WHERE death_cert_status IS NULL OR death_cert_status = ''");
$db->exec("UPDATE burial_requests SET cemetery_plot_status = 'Pending Review' WHERE cemetery_plot_status IS NULL OR cemetery_plot_status = ''");

echo "Migration completed successfully.\n";
