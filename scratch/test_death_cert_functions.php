<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

echo "=== DAMAYAN DEATH CERTIFICATE FUNCTIONS TEST ===\n\n";

$burialModel = new BurialRequest();
$db = (new \ReflectionClass($burialModel))->getProperty('db');
$db->setAccessible(true);
$pdo = $db->getValue($burialModel);

// Count deceased records before test
$decCountBefore = (int)$pdo->query("SELECT COUNT(*) FROM deceased_records")->fetchColumn();
echo "Initial deceased_records count: $decCountBefore\n";

// ── TEST 1: Register Death Certificate (New Deceased) ──
echo "\n--- TEST 1: Register Death Certificate ---\n";
$testTenantId = 60; // rjfelizardo25@gmail.com
$testSuffix = time();
$uniqueRegNo = "LCRO-MNL-$testSuffix";

$newDeceasedData = [
    'tenant_id' => $testTenantId,
    'cert_action_type' => 'register',
    'first_name' => 'Tariq_' . $testSuffix,
    'middle_name' => 'Ahmad',
    'last_name' => 'Al-Mansoor',
    'gender' => 'Male',
    'date_of_death' => '2026-10-10',
    'place_of_death' => 'Philippine General Hospital, Manila',
    'informant_name' => 'Fatima Al-Mansoor',
    'informant_contact' => '09171234567',
    'informant_email' => 'fatima.mansoor@example.com',
    'informant_address' => '123 Islamic Center St, Quiapo, Manila',
    'relationship' => 'Daughter',
    'death_cert_requested' => 1,
    'cemetery_plot_requested' => 0,
    'request_type' => 'death_certificate',
    'supporting_document' => 'uploads/test_med_cert.pdf'
];

$regRes = $burialModel->create($newDeceasedData);
echo "Create result: " . ($regRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";
if (!$regRes['success']) {
    die("Error: " . ($regRes['error'] ?? 'Unknown'));
}

$regRef = $regRes['ref_id'];
$regId = $regRes['id'];
echo "Generated Reference Number: $regRef\n";
assert(strpos($regRef, 'DC-REG-') === 0, "Ref should start with DC-REG-");

// Check that deceased_records was created
$decCountAfterReg = (int)$pdo->query("SELECT COUNT(*) FROM deceased_records")->fetchColumn();
echo "Deceased records count after registration: $decCountAfterReg (Expected: " . ($decCountBefore + 1) . ")\n";
assert($decCountAfterReg === $decCountBefore + 1, "A new deceased record should have been created!");

// Retrieve the created burial request
$regCase = $burialModel->findById($regId);
echo "Linked deceased ID: " . ($regCase['deceased_id'] ?? 'NULL') . "\n";
assert(!empty($regCase['deceased_id']), "Deceased ID should be linked!");
$tariqDecId = (int)$regCase['deceased_id'];

// Staff Admin updates Death Certificate status to Registered with LCRO registry number
$updateRes = $burialModel->updateDeathCertStatus($regId, [
    'death_cert_status' => 'Registered',
    'death_cert_registered_date' => '2026-10-12',
    'death_cert_registry_no' => $uniqueRegNo,
    'death_cert_responsible_personnel' => 'Officer A. Santos (LCRO Manila)',
    'death_cert_remarks' => 'Official registration completed and certified copy on file.'
], 'Admin Staff');
echo "Staff update to Registered: " . ($updateRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";

// ── TEST 2: Request Another Death Certificate (Copy of Existing) ──
echo "\n--- TEST 2: Request Another Death Certificate (Copy Request) ---\n";
$copyData = [
    'tenant_id' => $testTenantId,
    'cert_action_type' => 'copy',
    'existing_cert_no' => $uniqueRegNo,
    'copy_reason' => 'Required for SSS funeral benefit claim and family estate documentation',
    'num_copies' => 2,
    'first_name' => 'Tariq_' . $testSuffix,
    'last_name' => 'Al-Mansoor',
    'date_of_death' => '2026-10-10',
    'informant_name' => 'Fatima Al-Mansoor',
    'informant_contact' => '09171234567',
    'informant_email' => 'fatima.mansoor@example.com',
    'relationship' => 'Daughter',
    'death_cert_requested' => 1,
    'cemetery_plot_requested' => 0,
    'request_type' => 'death_certificate',
    'supporting_document' => 'uploads/test_existing_copy.pdf'
];

$copyRes = $burialModel->create($copyData);
echo "Copy Request create result: " . ($copyRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";
if (!$copyRes['success']) {
    die("Error: " . ($copyRes['error'] ?? 'Unknown'));
}

$copyRef = $copyRes['ref_id'];
$copyId = $copyRes['id'];
echo "Generated Copy Reference Number: $copyRef\n";
assert(strpos($copyRef, 'DC-CPY-') === 0, "Ref should start with DC-CPY-");

// CRUCIAL CHECK: Ensure NO new deceased record was created!
$decCountAfterCopy = (int)$pdo->query("SELECT COUNT(*) FROM deceased_records")->fetchColumn();
echo "Deceased records count after copy request: $decCountAfterCopy (Expected: $decCountAfterReg)\n";
assert($decCountAfterCopy === $decCountAfterReg, "CRITICAL: No new deceased record should be created when requesting another copy!");

// Check that copy was linked to Tariq's deceased record
$copyCase = $burialModel->findById($copyId);
echo "Linked deceased ID in copy case: " . ($copyCase['deceased_id'] ?? 'NULL') . "\n";
echo "Existing record verified: " . ($copyCase['existing_record_verified'] ?? '0') . "\n";
echo "Copy reason: " . ($copyCase['copy_reason'] ?? 'NULL') . "\n";
echo "Number of copies: " . ($copyCase['num_copies'] ?? 'NULL') . "\n";
assert($copyCase['deceased_id'] == $tariqDecId, "Copy request should be automatically linked to Tariq's deceased record!");
assert($copyCase['existing_record_verified'] == 1, "Existing record should be marked as verified!");

// ── TEST 3: Staff Admin Issues Copy ──
echo "\n--- TEST 3: Staff Admin Issues Certificate Copy ---\n";
// Before issuing: check that copy is NOT marked as issued or received
assert(empty($copyCase['copy_issued_at']), "Copy must NOT be marked as issued yet!");
assert(empty($copyCase['copy_received_confirmed']), "Copy must NOT be marked as received yet!");

$issueRes = $burialModel->issueCertificateCopy(
    $copyId,
    'PSA-CPY-2026-7788',
    'Staff Coordinator Pendatun',
    '2 certified true copies prepared and ready for claimant release.'
);
echo "Issue copy result: " . ($issueRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";
assert($issueRes['success'], "Issuing copy should succeed");

$copyCaseAfterIssue = $burialModel->findById($copyId);
echo "Copy issued at: " . $copyCaseAfterIssue['copy_issued_at'] . "\n";
echo "Copy certified ref: " . $copyCaseAfterIssue['death_cert_certified_copy_ref'] . "\n";
assert(!empty($copyCaseAfterIssue['copy_issued_at']), "Copy should now have issue timestamp");
assert(empty($copyCaseAfterIssue['copy_received_confirmed']), "Copy must NOT be marked as received until confirmed by user!");

// ── TEST 4: User Confirms Receipt ──
echo "\n--- TEST 4: User Confirms Copy Receipt ---\n";
$confirmRes = $burialModel->confirmCopyReceipt($copyId, $testTenantId, 'Fatima Al-Mansoor');
echo "Confirm receipt result: " . ($confirmRes['success'] ? 'SUCCESS' : 'FAILED') . "\n";
assert($confirmRes['success'], "User confirming copy receipt should succeed");

$copyCaseFinal = $burialModel->findById($copyId);
echo "Copy received confirmed: " . $copyCaseFinal['copy_received_confirmed'] . "\n";
echo "Copy received at: " . $copyCaseFinal['copy_received_at'] . "\n";
echo "Final status: " . $copyCaseFinal['status'] . "\n";
assert($copyCaseFinal['copy_received_confirmed'] == 1, "Copy should now be marked as confirmed received");
assert($copyCaseFinal['status'] === 'Completed', "Case should now be marked as Completed");

// ── TEST 5: Search Deceased Records API ──
echo "\n--- TEST 5: Search Deceased Records API ---\n";
$searchMatches = $burialModel->searchDeceasedRecords('Al-Mansoor');
echo "Search matches for 'Al-Mansoor': " . count($searchMatches) . " found\n";
assert(count($searchMatches) >= 1, "Should find at least 1 match for Al-Mansoor");
echo "Found deceased: " . $searchMatches[0]['deceased_name'] . " (ID: " . $searchMatches[0]['id'] . ")\n";

echo "\n ALL TESTS PASSED SUCCESSFULLY! \n";
