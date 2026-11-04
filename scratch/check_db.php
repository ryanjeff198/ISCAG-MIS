<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();
$stmt = $db->query('DESCRIBE tenant_accounts');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("%-25s %-20s %-10s %-10s %-10s\n", $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'] ?? 'NULL');
}
