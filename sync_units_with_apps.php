<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();

// 1. Get valid assigned units from apartmentsapp
$validUnits = $db->query("SELECT unit_id FROM apartmentsapp WHERE status IN ('Assigned', 'Accepted', 'Active') AND unit_id IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

if (empty($validUnits)) {
    echo "WARNING: No valid units found in apartmentsapp. Resetting ALL to Available.\n";
    $stmt = $db->query("UPDATE apartment_units SET status = 'Available', tenant_id = NULL, application_id = NULL");
} else {
    $placeholders = rtrim(str_repeat('?,', count($validUnits)), ',');
    $sql = "UPDATE apartment_units SET status = 'Available', tenant_id = NULL, application_id = NULL WHERE unit_id NOT IN ($placeholders) AND status = 'Occupied'";
    $stmt = $db->prepare($sql);
    $stmt->execute($validUnits);
}

echo "Cleaned up " . $stmt->rowCount() . " phantom-occupied units from apartment_units based on apartmentsapp truth.";
