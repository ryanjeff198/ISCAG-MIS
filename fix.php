<?php
require_once __DIR__ . '/config/database.php';

$db = getDbConnection();

// Retroactively fix the application status for Tenants who have already been moved out and are now Guests.
$stmt = $db->prepare("UPDATE apartmentsapp SET status = 'Archived' WHERE tenant_id IN (SELECT tenant_id FROM tenant_accounts WHERE role = 'Guest') AND status IN ('Assigned', 'Occupied')");
$stmt->execute();

echo "Retroactive fix applied! " . $stmt->rowCount() . " legacy application(s) archived.\n";
