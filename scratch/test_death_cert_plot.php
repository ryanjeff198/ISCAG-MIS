<?php
/**
 * Automated Verification Script for Death Certificate and Cemetery Plot Management
 */
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

echo "=== STARTING DAMAYAN DEATH CERTIFICATE & CEMETERY PLOT VERIFICATION ===\n\n";

$model = new BurialRequest();

// 1. Verify Plot Inventory Exists
$plots = $model->getAllPlots();
echo "[TEST 1] Checking Cemetery Plot Inventory: Found " . count($plots) . " plots.\n";
if (empty($plots)) {
    die("FAILED: No cemetery plots found in database.\n");
}
echo "✓ Inventory loaded successfully.\n\n";

// 2. Test User Submitting a Request for Both Services
echo "[TEST 2] Testing User Submission of Death Certificate + Cemetery Plot Request...\n";
$testDod = date('Y-m-d', strtotime('-2 days'));
$userSubmitData = [
    'tenant_id' => 1,
    'request_type' => 'both',
    'death_cert_requested' => 1,
    'cemetery_plot_requested' => 1,
    'deceased_first_name' => 'Tariq',
    'deceased_middle_name' => 'Kareem',
    'deceased_last_name' => 'Al-Husseini',
    'deceased_name' => 'Tariq Kareem Al-Husseini',
    'deceased_sex' => 'Male',
    'date_of_birth' => '1958-04-12',
    'date_of_death' => $testDod,
    'place_of_death' => 'St. Jude Hospital, Dasmariñas',
    'residence' => 'Blk 12 Lot 5, Dasmariñas, Cavite',
    'religion' => 'Islam',
    'civil_status' => 'Married',
    'nationality' => 'Filipino',
    'relationship' => 'Son',
    'informant_name' => 'Ryan Felizardo',
    'informant_contact' => '0917-123-4567',
    'informant_email' => 'rjfelizardo25@gmail.com',
    'informant_address' => 'Blk 12 Lot 5, Dasmariñas, Cavite',
    'cemetery_preference' => 'ISCAG Designated Muslim Cemetery',
    'preferred_section' => 'Section A',
    'additional_notes' => 'Urgent civil registration assistance needed.',
    'requested_services' => ['Death Certificate', 'Cemetery Plot']
];

$res = $model->create($userSubmitData);
if (!$res || empty($res['success'])) {
    die("FAILED: create() failed: " . json_encode($res) . "\n");
}
$caseId = $res['id'];
$refId = $res['ref_id'];
echo "✓ Case created successfully: ID #$caseId, Reference: $refId\n\n";

// 3. Verify Initial Statuses
$case = $model->findById($caseId);
echo "[TEST 3] Verifying Initial Case State:\n";
echo " - Request Type: " . $case['request_type'] . "\n";
echo " - Death Cert Status: " . $case['death_cert_status'] . " (Requested: " . $case['death_cert_requested'] . ")\n";
echo " - Cemetery Plot Status: " . $case['cemetery_plot_status'] . " (Requested: " . $case['cemetery_plot_requested'] . ")\n";
assert($case['death_cert_status'] === 'Request Received', 'Death cert status should be Request Received');
assert($case['cemetery_plot_status'] === 'Pending Review', 'Plot status should be Pending Review');
echo "✓ Initial statuses verified.\n\n";

// 4. Test Staff Progressing Death Certificate Through Stages
echo "[TEST 4] Testing Death Certificate Processing Stages...\n";
$stages = [
    'Under Review' => ['death_cert_remarks' => 'Reviewing hospital death certificate summary.'],
    'Awaiting Authorized Certificate Preparation' => [
        'death_cert_prep_date' => date('Y-m-d'),
        'death_cert_responsible_personnel' => 'Dr. A. Usman / Lying-in Staff'
    ],
    'Certificate Prepared and Certified' => [
        'death_cert_certified_date' => date('Y-m-d'),
        'death_cert_remarks' => 'Signed by attending physician.'
    ],
    'Submitted for Civil Registration' => [
        'death_cert_submitted_lcro_date' => date('Y-m-d'),
        'death_cert_lcro_ref' => 'LCRO-TRANSMIT-2026-091'
    ],
    'Registered' => [
        'death_cert_registered_date' => date('Y-m-d'),
        'death_cert_registry_no' => 'LCRO-2026-44810',
        'death_cert_certified_copy_ref' => 'PSA-REQ-88129'
    ]
];

foreach ($stages as $stage => $extra) {
    $dcUpdate = $model->updateDeathCertStatus($caseId, $stage, $extra, 'Rashid Pendatun (Staff)');
    if (!$dcUpdate['success']) {
        die("FAILED: updating death cert stage $stage: " . json_encode($dcUpdate) . "\n");
    }
    echo " -> Stage updated to: $stage\n";
}

$updatedCase = $model->findById($caseId);
assert($updatedCase['death_cert_status'] === 'Registered', 'Death cert should now be Registered');
assert($updatedCase['death_cert_registry_no'] === 'LCRO-2026-44810', 'Registry number should match');
echo "✓ Death Certificate successfully tracked to 'Registered' with LCRO Registry No: {$updatedCase['death_cert_registry_no']}.\n\n";

// 5. Test Cemetery Plot Availability and Assignment
echo "[TEST 5] Testing Cemetery Plot Assignment...\n";
$availPlots = $model->getAvailablePlots();
if (empty($availPlots)) {
    die("FAILED: No available plots for testing.\n");
}
$selectedPlot = $availPlots[0];
$plotId = (int)$selectedPlot['id'];
echo " - Selecting available plot: ID #$plotId ({$selectedPlot['cemetery_name']} - Sec {$selectedPlot['section']} Plot #{$selectedPlot['plot_number']})\n";

$plotAssignData = [
    'cemetery_plot_id' => $plotId,
    'cemetery_name' => $selectedPlot['cemetery_name'],
    'section' => $selectedPlot['section'],
    'plot_number' => $selectedPlot['plot_number'],
    'plot_assigned_date' => date('Y-m-d'),
    'cemetery_plot_remarks' => 'North-facing headrest Qiblah orientation verified.'
];

$plotRes = $model->updateCemeteryPlot($caseId, 'Plot Assigned', $plotAssignData, 'Rashid Pendatun (Staff)');
if (!$plotRes['success']) {
    die("FAILED: Plot assignment: " . json_encode($plotRes) . "\n");
}
echo "✓ Cemetery Plot assigned successfully: Plot #{$selectedPlot['plot_number']}.\n\n";

// 6. Test Duplicate Plot Assignment Prevention
echo "[TEST 6] Testing Duplicate Plot Assignment Prevention...\n";
// Create a second case
$secondCaseRes = $model->create([
    'tenant_id' => 1,
    'request_type' => 'cemetery_plot',
    'deceased_first_name' => 'Faris',
    'deceased_middle_name' => '',
    'deceased_last_name' => 'Mansoor',
    'deceased_name' => 'Faris Mansoor',
    'deceased_sex' => 'Male',
    'date_of_death' => date('Y-m-d'),
    'place_of_death' => 'Cavite',
    'informant_name' => 'Relative',
    'informant_contact' => '0912-345-6789'
]);
$secondCaseId = $secondCaseRes['id'];

// Attempt to assign the exact same occupied plot to second case
$duplicateAttempt = $model->updateCemeteryPlot($secondCaseId, 'Plot Assigned', $plotAssignData, 'Rashid Pendatun (Staff)');
echo " - Duplicate assignment result: " . json_encode($duplicateAttempt) . "\n";
assert(strpos($duplicateAttempt['error'], 'already occupied') !== false || strpos($duplicateAttempt['error'], 'Duplicate assignments') !== false, 'Error message should warn about plot already assigned/occupied');
echo "✓ Duplicate assignment correctly BLOCKED by system.\n\n";

// 7. Test Deceased Records Reuse (Requirement 4)
echo "[TEST 7] Testing Deceased Records Reuse to Avoid Duplicate Entry...\n";
$deceasedRecords = $model->getDeceasedRecords(1);
echo " - Found " . count($deceasedRecords) . " previously registered deceased records for user #1.\n";
assert(!empty($deceasedRecords), 'User should have existing deceased records');
$firstDeceased = $deceasedRecords[0];

// Submit a new request linking directly to existing deceased_id
$thirdCaseRes = $model->create([
    'tenant_id' => 1,
    'deceased_id' => (int)$firstDeceased['id'],
    'request_type' => 'death_certificate',
    'deceased_name' => trim($firstDeceased['first_name'] . ' ' . $firstDeceased['last_name']),
    'deceased_first_name' => $firstDeceased['first_name'],
    'deceased_middle_name' => $firstDeceased['middle_name'],
    'deceased_last_name' => $firstDeceased['last_name'],
    'deceased_sex' => $firstDeceased['sex'],
    'date_of_death' => $firstDeceased['date_of_death'],
    'place_of_death' => $firstDeceased['place_of_death'],
    'informant_name' => 'Ryan Felizardo',
    'informant_contact' => '0917-123-4567'
]);
assert($thirdCaseRes['success'] === true, 'Linking to existing deceased should succeed');
$thirdCase = $model->findById($thirdCaseRes['id']);
assert((int)$thirdCase['deceased_id'] === (int)$firstDeceased['id'], 'Deceased ID should be linked');
echo "✓ Third case successfully connected to deceased record ID #{$firstDeceased['id']} without duplicate entries.\n\n";

// 8. Verify Audit Trail Logs
echo "[TEST 8] Verifying Audit Trail Logs...\n";
$logs = $model->getAuditLogs($caseId);
echo " - Found " . count($logs) . " audit trail logs for case #$caseId.\n";
foreach ($logs as $l) {
    echo "   * [{$l['created_at']}] {$l['title']} by {$l['actor_name']}: {$l['details']}\n";
}
assert(!empty($logs), 'Audit logs must not be empty');
echo "✓ Complete audit trail verified.\n\n";

echo "=== ALL VERIFICATION TESTS PASSED SUCCESSFULLY! ===\n";
