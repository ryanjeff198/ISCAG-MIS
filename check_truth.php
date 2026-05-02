<?php
require 'c:/xampp/htdocs/Iscag/config/database.php';
$db = getDbConnection();
$apps = $db->query("SELECT application_id, tenant_id, unit_id, status FROM apartmentsapp WHERE status IN ('Assigned', 'Accepted', 'Active')")->fetchAll(PDO::FETCH_ASSOC);
file_put_contents('c:/xampp/htdocs/Iscag/apps_truth.json', json_encode($apps, JSON_PRETTY_PRINT));
echo "Found " . count($apps) . " active applications.";
