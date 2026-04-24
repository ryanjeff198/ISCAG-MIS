<?php
require_once 'config/database.php';
$db = getDbConnection();
$stmt = $db->query("DESCRIBE tenant_user_profiles");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo implode("\n", $columns);
