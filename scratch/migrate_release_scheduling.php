<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$pdo = getDbConnection();

echo "Running migration for Damayan Death Certificate Release Scheduling...\n";

// 1. Modify death_cert_status in burial_requests to VARCHAR(100) so any status can be saved
try {
    $pdo->exec("ALTER TABLE burial_requests MODIFY COLUMN death_cert_status VARCHAR(100) DEFAULT 'Request Received'");
    echo "Modified death_cert_status to VARCHAR(100) successfully.\n";
} catch (\Throwable $e) {
    echo "Notice on death_cert_status: " . $e->getMessage() . "\n";
}

// 2. Add scheduling and release columns to burial_requests
$cols = [
    "scheduled_release_date DATE NULL",
    "scheduled_release_time TIME NULL",
    "release_location VARCHAR(255) DEFAULT 'Damayan Department'",
    "release_instructions TEXT NULL",
    "release_scheduled_by_id INT NULL",
    "release_scheduled_by_name VARCHAR(200) NULL",
    "release_scheduled_at DATETIME NULL",
    "certificate_released_at DATETIME NULL",
    "certificate_released_by VARCHAR(200) NULL",
    "certificate_recipient_name VARCHAR(200) NULL",
    "release_notes TEXT NULL",
    "release_cancelled_at DATETIME NULL",
    "release_cancelled_by VARCHAR(200) NULL",
    "release_cancellation_reason TEXT NULL",
    "reschedule_reason TEXT NULL"
];

foreach ($cols as $col) {
    try {
        $pdo->exec("ALTER TABLE burial_requests ADD COLUMN $col");
        echo "Added column: $col\n";
    } catch (\Throwable $e) {
        // already exists or error
    }
}

// 3. Create burial_schedule_history table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS burial_schedule_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        burial_request_id INT NOT NULL,
        ref_id VARCHAR(50) NULL,
        action_type VARCHAR(50) NOT NULL, -- 'SCHEDULED', 'RESCHEDULED', 'CANCELLED', 'RELEASED'
        previous_date DATE NULL,
        previous_time TIME NULL,
        new_date DATE NULL,
        new_time TIME NULL,
        location VARCHAR(255) DEFAULT 'Damayan Department',
        instructions TEXT NULL,
        change_reason TEXT NULL,
        admin_id INT NULL,
        admin_name VARCHAR(200) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_bsh_b_id (burial_request_id),
        INDEX idx_bsh_ref (ref_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Created table burial_schedule_history successfully.\n";
} catch (\Throwable $e) {
    echo "Error on burial_schedule_history: " . $e->getMessage() . "\n";
}

echo "Migration finished.\n";
