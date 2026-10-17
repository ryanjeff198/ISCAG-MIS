<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/BillingReminder.php';

echo "Sending mock billing reminder for Tenant ID 63 (Ryan Felizardo)...\n";
$res = BillingReminder::sendMockReminder(63);
print_r($res);
