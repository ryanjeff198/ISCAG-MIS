<?php
require 'config/database.php';
$db = getDbConnection();
$db->prepare("UPDATE tenant_accounts SET role = 'Guest' WHERE tenant_id = 63")->execute();
$db->prepare("UPDATE leases SET lease_status = 'Archived' WHERE tenant_id = 63")->execute();
echo "Ryan downgraded!";
