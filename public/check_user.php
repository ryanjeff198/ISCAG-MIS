<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';

$userModel = new User();
$user = $userModel->findByEmail('dacer2314@gmail.com');

if ($user) {
    echo "User Found in tenant_accounts:\n";
    echo "ID: " . $user['tenant_id'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "First Name: [" . $user['first_name'] . "]\n";
    echo "Last Name: [" . $user['last_name'] . "]\n";
    echo "Role: " . ($user['role'] ?? 'N/A') . "\n";

    $stmt = getDbConnection()->prepare("SELECT * FROM tenant_user_profiles WHERE tenant_id = :id LIMIT 1");
    $stmt->execute(['id' => $user['tenant_id']]);
    $profile = $stmt->fetch();
    
    if ($profile) {
        echo "\nProfile Found in tenant_user_profiles:\n";
        echo "Muslim Name: [" . $profile['muslim_name'] . "]\n";
        echo "Occupation: [" . $profile['occupation'] . "]\n";
    } else {
        echo "\nNo record found in tenant_user_profiles for this user.\n";
    }

    $stmt = getDbConnection()->prepare("SELECT * FROM tenant_addinfo WHERE tenant_id = :id LIMIT 1");
    $stmt->execute(['id' => $user['tenant_id']]);
    $addinfo = $stmt->fetch();
    
    if ($addinfo) {
        echo "\nRecord Found in tenant_addinfo:\n";
        echo "Family Name: [" . $addinfo['familyname'] . "]\n";
        echo "Given Name: [" . $addinfo['givenname'] . "]\n";
        echo "Muslim Name: [" . $addinfo['muslimname'] . "]\n";
        echo "Occupation: [" . $addinfo['occupation'] . "]\n";
    } else {
        echo "\nNo record found in tenant_addinfo for this user.\n";
    }
} else {
    echo "User dacer2314@gmail.com NOT found in database.\n";
}
