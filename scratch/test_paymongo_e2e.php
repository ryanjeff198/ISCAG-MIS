<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
require_once BASE_PATH . '/app/models/CharityProgram.php';
require_once BASE_PATH . '/app/models/IslamicEducation.php';
require_once BASE_PATH . '/app/models/Payment.php';

session_start();
$db = getDbConnection();

echo "=== TESTING END-TO-END PAYMONGO WORKFLOWS ===\n\n";

// -------------------------------------------------------------
// Test 1: Charity Donation PayMongo Checkout & Success Flow
// -------------------------------------------------------------
echo "1. Charity Donation PayMongo Flow:\n";
$charityRef = 'ISCAG-DON-' . date('Ymd') . '-TEST01';
$charityAmount = 750.00;
$prog = 'General Welfare & Relief';

$sessRes = PayMongoHelper::createCheckoutSession(
    $charityAmount,
    "ISCAG Damayan Charity Donation [Ref: $charityRef] — $prog",
    [['name' => "$prog — Charity Donation", 'amount' => $charityAmount, 'quantity' => 1]],
    "http://localhost/user/services/charity/paymongo-success?session_id={CHECKOUT_SESSION_ID}",
    "http://localhost/user/services/charity",
    ['tenant_id' => '1', 'target_program' => $prog, 'amount' => (string)$charityAmount, 'reference_number' => $charityRef],
    $charityRef,
    ['name' => 'Bro. Abdullah', 'email' => 'abdullah@test.local']
);

assert($sessRes['success'] === true, "Charity checkout session failed");
$charitySessionId = $sessRes['data']['id'];
echo "   - Checkout session created: $charitySessionId\n";

// Simulate customer authorization
PayMongoHelper::markSimulatedSessionPaid($charitySessionId, 'GCash');

// Simulate success callback verification
$verified = PayMongoHelper::getCheckoutSession($charitySessionId);
assert($verified['success'] === true && $verified['data']['attributes']['status'] === 'paid');

// Simulate recording to DB
$charityModel = new CharityProgram();
$recRes = $charityModel->recordDonation([
    'tenant_id'        => 1,
    'donor_name'       => 'Bro. Abdullah',
    'donor_address'    => 'abdullah@test.local',
    'donation_category'=> 'digital',
    'donation_type'    => 'monetary',
    'amount'           => $charityAmount,
    'target_program'   => $prog,
    'date_received'    => date('Y-m-d'),
    'payment_method'   => 'PayMongo (Online)',
    'payment_reference'=> $charityRef,
    'received_by'      => 'PayMongo Gateway (Verified)',
    'status'           => 'Available',
    'remarks'          => 'Paid via PayMongo Secure Gateway (auto-verified)'
], 'Bro. Abdullah', 1);

assert($recRes['success'] === true, "Donation record failed");
echo "   - Recorded donation successfully: " . $recRes['donation_ref'] . "\n";


// -------------------------------------------------------------
// Test 2: Female Education PayMongo Checkout & Success Flow
// -------------------------------------------------------------
echo "\n2. Female Islamic Education PayMongo Flow:\n";
$eduRef = 'ISCAG-EDU-' . date('Ymd') . '-TEST02';
$eduAmount = 120.00;

$eduSessRes = PayMongoHelper::createCheckoutSession(
    $eduAmount,
    "ISCAG Da'wah Female Education [Ref: $eduRef] — Batch 08",
    [['name' => "Da'wah Female Islamic Education — Level 1", 'amount' => $eduAmount, 'quantity' => 1]],
    "http://localhost/user/services/education/female/paymongo-success?session_id={CHECKOUT_SESSION_ID}",
    "http://localhost/user/services/education/female",
    ['tenant_id' => '1', 'module' => 'female_education', 'reference_number' => $eduRef, 'amount' => (string)$eduAmount],
    $eduRef,
    ['name' => 'Sister Maryam', 'email' => 'maryam@test.local']
);

assert($eduSessRes['success'] === true, "Female education checkout session failed");
$eduSessionId = $eduSessRes['data']['id'];
echo "   - Checkout session created: $eduSessionId\n";

PayMongoHelper::markSimulatedSessionPaid($eduSessionId, 'QR Ph');
$eduVerified = PayMongoHelper::getCheckoutSession($eduSessionId);
assert($eduVerified['success'] === true && $eduVerified['data']['attributes']['status'] === 'paid');

$tRow = $db->query("SELECT tenant_id FROM tenant_accounts LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$validTenantId = $tRow ? (int)$tRow['tenant_id'] : null;

$eduModel = new IslamicEducation();
$regId = $eduModel->createRegistration([
    'tenant_id' => $validTenantId,
    'first_name' => 'Sister',
    'last_name' => 'Maryam',
    'contact_number' => '09123456789',
    'address' => 'Dasmariñas City',
    'batch_no' => 'Batch 08',
    'level' => 'Level 1 — Basic Arabic & Tajweed',
    'payment_method' => 'Online Payment',
    'fee_amount' => $eduAmount,
    'payment_channel' => 'PayMongo Gateway (QR Ph / E-Wallets / Card)',
    'payment_reference' => $eduRef,
    'payment_status' => 'Paid',
    'gender' => 'female',
    'status' => 'pending'
]);

assert($regId > 0, "Female education registration creation failed");
$eduModel->ensureSessionsSeeded($regId);
echo "   - Registered student and seeded 12 curriculum sessions: ID #$regId\n";


// -------------------------------------------------------------
// Test 3: Cleanup Test DB Records
// -------------------------------------------------------------
$db->exec("DELETE FROM charity_donations_records WHERE payment_reference = '$charityRef'");
$db->exec("DELETE FROM islamic_education_sessions WHERE enrollment_id = $regId");
$db->exec("DELETE FROM islamic_education_enrollments WHERE id = $regId");

echo "\n>>> ALL END-TO-END WORKFLOW TESTS COMPLETED AND CLEANED UP! <<<\n";
