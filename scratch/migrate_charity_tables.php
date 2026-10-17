<?php
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$pdo = getDbConnection();

$queries = [
    // 1. Charity Donations Records Table
    "CREATE TABLE IF NOT EXISTS `charity_donations_records` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `donation_ref` VARCHAR(50) NOT NULL UNIQUE,
        `donor_name` VARCHAR(255) NOT NULL,
        `donor_address` TEXT NULL,
        `donation_type` ENUM('monetary', 'in-kind') NOT NULL,
        `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        `item_description` TEXT NULL,
        `quantity` INT NOT NULL DEFAULT 0,
        `allocated_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        `allocated_quantity` INT NOT NULL DEFAULT 0,
        `date_received` DATE NOT NULL,
        `payment_method` VARCHAR(100) NULL,
        `payment_reference` VARCHAR(100) NULL,
        `received_by` VARCHAR(255) NOT NULL,
        `remarks` TEXT NULL,
        `ack_issued` TINYINT(1) NOT NULL DEFAULT 0,
        `ack_issued_at` DATETIME NULL,
        `ack_issued_by` VARCHAR(255) NULL,
        `status` ENUM('Available', 'Partially Allocated', 'Fully Allocated', 'Cancelled') NOT NULL DEFAULT 'Available',
        `created_by` INT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_donor (`donor_name`),
        INDEX idx_type (`donation_type`),
        INDEX idx_status (`status`),
        INDEX idx_date (`date_received`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // 2. Charity Allocations Table
    "CREATE TABLE IF NOT EXISTS `charity_allocations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `allocation_ref` VARCHAR(50) NOT NULL UNIQUE,
        `donation_id` INT NOT NULL,
        `donation_ref` VARCHAR(50) NOT NULL,
        `receiving_department` ENUM('Damayan', 'Dawah') NOT NULL,
        `allocation_type` ENUM('monetary', 'in-kind') NOT NULL,
        `allocated_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        `allocated_quantity` INT NOT NULL DEFAULT 0,
        `distributed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        `distributed_quantity` INT NOT NULL DEFAULT 0,
        `purpose` TEXT NOT NULL,
        `allocation_date` DATE NOT NULL,
        `allocated_by` VARCHAR(255) NOT NULL,
        `remarks` TEXT NULL,
        `status` ENUM('Pending Distribution', 'Partially Distributed', 'Distributed', 'Cancelled') NOT NULL DEFAULT 'Pending Distribution',
        `created_by` INT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_alloc_dept (`receiving_department`),
        INDEX idx_alloc_status (`status`),
        INDEX idx_alloc_date (`allocation_date`),
        CONSTRAINT `fk_alloc_donation` FOREIGN KEY (`donation_id`) REFERENCES `charity_donations_records`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // 3. Charity Distributions Table
    "CREATE TABLE IF NOT EXISTS `charity_distributions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `distribution_ref` VARCHAR(50) NOT NULL UNIQUE,
        `allocation_id` INT NOT NULL,
        `allocation_ref` VARCHAR(50) NOT NULL,
        `donation_id` INT NOT NULL,
        `donation_ref` VARCHAR(50) NOT NULL,
        `receiving_department` ENUM('Damayan', 'Dawah') NOT NULL,
        `distribution_type` ENUM('monetary', 'in-kind') NOT NULL,
        `distributed_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        `distributed_quantity` INT NOT NULL DEFAULT 0,
        `items_summary` TEXT NULL,
        `purpose` TEXT NOT NULL,
        `distribution_date` DATE NOT NULL,
        `distributed_by` VARCHAR(255) NOT NULL,
        `received_by` VARCHAR(255) NOT NULL,
        `supporting_document` VARCHAR(255) NULL,
        `status` ENUM('Distributed', 'Cancelled') NOT NULL DEFAULT 'Distributed',
        `remarks` TEXT NULL,
        `created_by` INT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_dist_dept (`receiving_department`),
        INDEX idx_dist_date (`distribution_date`),
        INDEX idx_dist_alloc (`allocation_id`),
        CONSTRAINT `fk_dist_allocation` FOREIGN KEY (`allocation_id`) REFERENCES `charity_allocations`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    // 4. Charity Activity Logs Table
    "CREATE TABLE IF NOT EXISTS `charity_activity_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `action_type` VARCHAR(50) NOT NULL,
        `reference_no` VARCHAR(50) NOT NULL,
        `details` TEXT NOT NULL,
        `performed_by` VARCHAR(255) NOT NULL,
        `user_id` INT NULL,
        `ip_address` VARCHAR(45) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_act_ref (`reference_no`),
        INDEX idx_act_type (`action_type`),
        INDEX idx_act_created (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($queries as $i => $q) {
    try {
        $pdo->exec($q);
        echo "Table query " . ($i + 1) . " executed successfully.\n";
    } catch (PDOException $e) {
        echo "Error on query " . ($i + 1) . ": " . $e->getMessage() . "\n";
    }
}
