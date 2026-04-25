<?php
require 'config/database.php';
$db = getDbConnection();

$stmt = $db->query('DESCRIBE tenant_addinfo');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "---IMAGES---\n";
$stmt2 = $db->query('DESCRIBE tenant_addinfo_images');
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
