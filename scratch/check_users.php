<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once 'c:/xampp/htdocs/ISCAG-MIS/config/database.php';
$db = getDbConnection();
$hash = password_hash('password123', PASSWORD_DEFAULT);
$db->prepare("UPDATE tenant_accounts SET password = :p1, confirmpass = :p2, is_verified = 1 WHERE email = 'rjfelizardo25@gmail.com'")->execute(['p1' => $hash, 'p2' => $hash]);
echo "Updated password for rjfelizardo25@gmail.com\n";
require_once 'c:/xampp/htdocs/ISCAG-MIS/app/models/User.php';
$u = new User();
$auth1 = $u->authenticate('rjfelizardo25@gmail.com', 'password123');
echo "User auth: " . ($auth1 ? 'SUCCESS (tenant_id=' . $auth1['tenant_id'] . ')' : 'FAILED') . "\n";
