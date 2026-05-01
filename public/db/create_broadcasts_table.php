<?php
define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/app/helpers/Security.php';
require_once BASE_PATH . '/config/database.php';

try {
    $db = getDbConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS broadcasts (
        broadcast_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        target_group VARCHAR(50) NOT NULL,
        sender_id INT NOT NULL,
        type VARCHAR(50) DEFAULT 'system',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->exec($sql);
    echo "Broadcasts table created successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
