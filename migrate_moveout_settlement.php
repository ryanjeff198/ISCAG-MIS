<?php
require_once 'config/database.php';
$db = getDbConnection();
try {
    $db->exec("ALTER TABLE move_out_requests 
        ADD COLUMN inspection_notes TEXT AFTER move_out_date,
        ADD COLUMN damage_costs DECIMAL(10,2) DEFAULT 0.00 AFTER inspection_notes,
        ADD COLUMN utility_deductions DECIMAL(10,2) DEFAULT 0.00 AFTER damage_costs,
        ADD COLUMN final_refund DECIMAL(10,2) DEFAULT 0.00 AFTER utility_deductions
    ");
    echo "Successfully updated move_out_requests table with settlement columns.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
