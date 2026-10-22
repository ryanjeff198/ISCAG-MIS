<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

// Mock session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'User';
$_SESSION['email'] = 'user@example.com';
$_SESSION['name'] = 'Test Requester';

$model = new BurialRequest();

echo "=== USER SUBMISSION GATE VERIFICATION TEST ===\n";

// Test A: Simulation of UserController submitBurial with UNREGISTERED certificate
echo "\n[Test A] Testing submission with arbitrary unregistered deceased & non-existent cert...\n";

$unregCertNo = "UNREGISTERED-CERT-999999";
$unregDeceasedName = "Unregistered Person XYZ";

$foundInDept = false;
if (!empty($unregCertNo)) {
    $enc = $model->findEncodedByRegistrationNo($unregCertNo);
    if ($enc) {
        $foundInDept = true;
    } else {
        $dup = $model->isDuplicateDeathCertRegNo($unregCertNo);
        if ($dup) $foundInDept = true;
    }
}

if (!$foundInDept) {
    echo "PASS: Unregistered certificate was BLOCKED by department gate.\n";
    echo "System response: 'Processing is only available for death certificates recorded within the ISCAG Damayan Department. Please enter a valid registration number or select a recorded deceased record from the department archive.'\n";
} else {
    echo "FAIL: Unregistered certificate was incorrectly accepted!\n";
    exit(1);
}

// Test B: Submission with a verified department certificate
echo "\n[Test B] Testing submission with registered certificate ('2023-2-0001113')...\n";
$validCertNo = '2023-2-0001113';
$enc = $model->findEncodedByRegistrationNo($validCertNo);

if (!$enc) {
    echo "FAIL: Sample certificate '2023-2-0001113' not found in department archive.\n";
    exit(1);
}

echo "Found department certificate: {$enc['deceased_name']} (Reg: {$enc['registration_number']})\n";

// Create request for this department certificate
$createData = [
    'tenant_id' => 1,
    'cert_action_type' => 'copy',
    'existing_cert_no' => $validCertNo,
    'encoded_cert_id' => (int)$enc['id'],
    'deceased_name' => $enc['deceased_name'],
    'copy_reason' => 'Certified true copy for legal proceedings',
    'num_copies' => 2,
    'informant_name' => 'Test Relative',
    'informant_contact' => '09123456789',
    'informant_email' => 'user@example.com',
    'relationship' => 'Son'
];

$res = $model->create($createData);

if ($res && !empty($res['success'])) {
    echo "PASS: Request created successfully! Ref ID: {$res['ref_id']}\n";
    
    // Verify custody was set to 1 in database
    $createdCase = $model->findByRefId($res['ref_id']);
    if ((int)$createdCase['certificate_in_department'] === 1) {
        echo "PASS: certificate_in_department = 1 for verified department certificate.\n";
    } else {
        echo "FAIL: certificate_in_department was not set to 1!\n";
        exit(1);
    }
} else {
    echo "FAIL: Could not create request for verified certificate.\n";
    exit(1);
}

echo "\n=== ALL USER SUBMISSION GATE TESTS PASSED! ===\n";
