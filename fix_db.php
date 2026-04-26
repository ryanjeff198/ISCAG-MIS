<?php
define('BASE_PATH', __DIR__);
require 'config/database.php';
$db = getDbConnection();
try {
    // 1. Delete the bad row with tenant_id = 0
    $db->exec('DELETE FROM apartmentsapp WHERE tenant_id = 0 OR tenant_id IS NULL;');
    
    // 2. We need to assign unique IDs to any remaining rows with application_id = 0
    // Get max ID
    $stmt = $db->query('SELECT MAX(application_id) FROM apartmentsapp');
    $maxId = (int)$stmt->fetchColumn();
    
    // Find rows with application_id = 0
    $stmt = $db->query('SELECT * FROM apartmentsapp WHERE application_id = 0');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        $maxId++;
        $updateStmt = $db->prepare('UPDATE apartmentsapp SET application_id = :newId WHERE tenant_id = :tid AND application_id = 0');
        $updateStmt->execute(['newId' => $maxId, 'tid' => $row['tenant_id']]);
    }
    
    // 3. Apply PRIMARY KEY
    $db->exec('ALTER TABLE apartmentsapp ADD PRIMARY KEY (application_id);');
    
    // 4. Apply AUTO_INCREMENT
    $db->exec('ALTER TABLE apartmentsapp MODIFY application_id INT(11) NOT NULL AUTO_INCREMENT;');
    
    // 5. Fix the user who was left behind! tenant_id = 42
    $db->exec('UPDATE tenant_accounts SET role = \'Tenant\' WHERE tenant_id = 42;');
    
    echo 'Database schema successfully patched!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}