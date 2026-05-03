<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$stmt = $db->query("UPDATE apartment_units SET status = 'Available', application_id = NULL WHERE status = 'Occupied' AND tenant_id IS NULL");
echo "Cleaned " . $stmt->rowCount() . " ghost units.";
