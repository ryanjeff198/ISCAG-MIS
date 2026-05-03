<?php
require 'config/database.php';
$db = getDbConnection();
$stmt = $db->prepare("UPDATE apartment_units SET status = 'Available', tenant_id = NULL, application_id = NULL WHERE unit_id = 11 OR room_number = '211' OR room_number = '2211'");
$stmt->execute();
echo "Unit released.";
