<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
// Ensure strictly occupied rooms are kept, everything else becomes available
$stmt = $db->query("UPDATE apartment_units SET status = 'Available', tenant_id = NULL, application_id = NULL WHERE tenant_id IS NULL OR tenant_id = 0 OR tenant_id = ''");
echo "Reset " . $stmt->rowCount() . " ghost/orphaned units back to Available.";
