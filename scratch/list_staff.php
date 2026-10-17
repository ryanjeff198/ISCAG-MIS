<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$staff = $db->query("SELECT tenant_id, first_name, last_name, email, role FROM tenant_accounts WHERE role IN ('Admin', 'Staff_Damayan', 'Staff_Tenant', 'Staff_Male', 'Staff_Female')")->fetchAll(PDO::FETCH_ASSOC);
print_r($staff);
