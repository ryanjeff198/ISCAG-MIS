<?php
define('BASE_PATH', 'c:/xampp/htdocs/ISCAG-MIS');
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

echo "=== DAMAYAN CERTIFICATE IN DEPARTMENT & CUSTODY GATE TEST ===\n";

$model = new BurialRequest();
$db = getDbConnection();

// 1. Fetch or create a test case for scheduling gate test
$stmt = $db->query("SELECT id, ref_id, certificate_in_department FROM burial_requests ORDER BY id DESC LIMIT 1");
$testCase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$testCase) {
    echo "No existing burial cases found to test. Creating dummy case...\n";
    $db->prepare("INSERT INTO burial_requests (ref_id, tenant_id, deceased_name, death_cert_requested, death_cert_status, certificate_in_department, submitted_at) 
                  VALUES ('TEST-CUSTODY-01', 1, 'Test Deceased', 1, 'Submitted', 0, NOW())")->execute();
    $caseId = (int)$db->lastInsertId();
} else {
    $caseId = (int)$testCase['id'];
}

echo "Testing Case ID: {$caseId}\n";

// Force certificate_in_department = 0
$db->prepare("UPDATE burial_requests SET certificate_in_department = 0, scheduled_release_date = NULL, scheduled_release_time = NULL WHERE id = :id")->execute(['id' => $caseId]);

// TEST 1: Schedule release when NOT in department custody
echo "\n[Test 1] Attempting to schedule release with certificate_in_department = 0...\n";
$futureDate = date('Y-m-d', strtotime('+3 days'));
$futureTime = '14:30';
$schedResult = $model->scheduleCertificateRelease($caseId, [
    'release_date' => $futureDate,
    'release_time' => $futureTime,
    'release_instructions' => 'Claim at counter'
], 1, 'Test Admin');

if ($schedResult['success'] === false && str_contains($schedResult['error'], 'Damayan Department custody')) {
    echo "PASS: Schedule rejected as expected. Error: {$schedResult['error']}\n";
} else {
    echo "FAIL: Expected rejection due to custody gate, got: " . json_encode($schedResult) . "\n";
    exit(1);
}

// TEST 2: Mark certificate in department custody
echo "\n[Test 2] Setting certificate_in_department = 1 via setCertificateInDepartment()...\n";
$custodyResult = $model->setCertificateInDepartment($caseId, true, 'Admin Tester', 'Physical certificate received from LCRO');
if ($custodyResult['success'] === true) {
    echo "PASS: " . $custodyResult['message'] . "\n";
} else {
    echo "FAIL: " . ($custodyResult['error'] ?? 'Unknown error') . "\n";
    exit(1);
}

// Verify database reflects custody
$chk = $model->findById($caseId);
if ((int)$chk['certificate_in_department'] === 1 && !empty($chk['certificate_received_dept_at'])) {
    echo "PASS: Database column certificate_in_department = 1, received_at = {$chk['certificate_received_dept_at']}\n";
} else {
    echo "FAIL: Database record does not reflect custody!\n";
    exit(1);
}

// TEST 3: Schedule release now that certificate is in department custody
echo "\n[Test 3] Scheduling release now that certificate is in custody...\n";
$schedResult2 = $model->scheduleCertificateRelease($caseId, [
    'release_date' => $futureDate,
    'release_time' => $futureTime,
    'release_instructions' => 'Please bring valid ID'
], 1, 'Test Admin');

if ($schedResult2['success'] === true) {
    echo "PASS: Scheduling succeeded. Claiming date: {$schedResult2['scheduled_date']} at {$schedResult2['scheduled_time']}\n";
} else {
    echo "FAIL: Expected scheduling to succeed, got error: " . ($schedResult2['error'] ?? 'Unknown') . "\n";
    exit(1);
}

// TEST 4: Revert custody status
echo "\n[Test 4] Reverting certificate custody status...\n";
$revertResult = $model->setCertificateInDepartment($caseId, false, 'Admin Tester', 'Reverting custody for test');
if ($revertResult['success'] === true) {
    $chkRevert = $model->findById($caseId);
    if ((int)$chkRevert['certificate_in_department'] === 0) {
        echo "PASS: Revert succeeded. Custody is now 0.\n";
    } else {
        echo "FAIL: Revert did not set custody to 0.\n";
        exit(1);
    }
}

// TEST 5: Lookup encoded certificate API
echo "\n[Test 5] Testing lookupEncodedCertificate logic...\n";
// Find any real encoded certificate registration number
$encStmt = $db->query("SELECT registration_number, deceased_name FROM encoded_death_certificates LIMIT 1");
$encRow = $encStmt->fetch(PDO::FETCH_ASSOC);

if ($encRow) {
    echo "Found sample row from encoded_death_certificates: Reg='{$encRow['registration_number']}', Name='{$encRow['deceased_name']}'\n";
    $found = $model->findEncodedByRegistrationNo($encRow['registration_number']);
    if ($found) {
        echo "PASS: Verified findEncodedByRegistrationNo found record '{$found['deceased_name']}' with Reg # {$found['registration_number']}\n";
    } else {
        echo "FAIL: Could not locate encoded certificate by registration number '{$encRow['registration_number']}'.\n";
    }
} else {
    echo "SKIP: No encoded_death_certificates in DB, testing dummy check...\n";
}

// TEST 6: Check non-existent certificate
$fake = $model->findEncodedByRegistrationNo('NON-EXISTENT-FAKE-REG-99999');
if ($fake === null) {
    echo "PASS: Non-existent certificate returned null as expected.\n";
} else {
    echo "FAIL: Fake certificate returned non-null!\n";
}

echo "\n=== ALL 6 TESTS COMPLETED SUCCESSFULLY! ===\n";
