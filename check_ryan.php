<?php
require 'config/database.php';
$db = getDbConnection();
$res = $db->query("SELECT tenant_id, first_name, last_name, role FROM tenant_accounts WHERE first_name = 'Ryan'")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
