<?php
require_once __DIR__ . '/../config/database.php';
$db = getDbConnection();
$existing = $db->query("SELECT tenant_id FROM tenant_accounts WHERE role = 'Staff_Damayan'")->fetch();
if (!$existing) {
    $db->prepare("INSERT INTO tenant_accounts (first_name, last_name, email, password, role, is_verified, contactnum) VALUES ('Rashid', 'Pendatun', 'rpendatun@iscag.org', :pass, 'Staff_Damayan', 1, '0917-555-8899')")->execute([
        'pass' => password_hash('password123', PASSWORD_BCRYPT)
    ]);
    echo "Created Staff_Damayan account (rpendatun@iscag.org / password123).\n";
} else {
    echo "Staff_Damayan account already exists.\n";
}
