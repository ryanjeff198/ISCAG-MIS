<?php
define('BASE_PATH', 'C:/Users/Dela_/Desktop/Xampp/htdocs/ISCAG-MIS');
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDbConnection();
    echo "Connected to database.\n";
    
    $tables = ['burial_requests', 'charity_donations'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists.\n";
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "Row count in '$table': $count\n";
        } else {
            echo "Table '$table' DOES NOT exist.\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
