<?php
require 'config/database.php';
$db = getDbConnection();
$db->exec("ALTER TABLE leases MODIFY COLUMN lease_status ENUM('Pending','Accepted','Rejected','Active','Expired','Archived') DEFAULT 'Pending'");
echo "Column altered.\n";
// Now fix Ryan
$db->exec("UPDATE leases SET lease_status = 'Archived' WHERE lease_id = 9");
echo "Ryan fixed: " . $db->query("SELECT lease_status FROM leases WHERE lease_id = 9")->fetchColumn() . "\n";
