<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';

echo "=== TESTING PAYMONGO HELPER & SANDBOX FLOW ===\n\n";

// 1. Test createCheckoutSession
$amount = 120.00;
$refNo = 'ISCAG-TEST-' . date('Ymd') . '-999';
$res = PayMongoHelper::createCheckoutSession(
    $amount,
    'Test Da\'wah Fee',
    [['name' => 'Registration Fee', 'amount' => $amount, 'quantity' => 1]],
    'http://localhost/success?session_id={CHECKOUT_SESSION_ID}',
    'http://localhost/cancel',
    ['tenant_id' => '1', 'module' => 'test'],
    $refNo,
    ['name' => 'Test Student', 'email' => 'student@test.local']
);

echo "1. Checkout Session Creation:\n";
echo "   Success: " . ($res['success'] ? 'YES' : 'NO') . "\n";
echo "   Session ID: " . ($res['data']['id'] ?? 'NONE') . "\n";
echo "   Checkout URL: " . ($res['data']['attributes']['checkout_url'] ?? 'NONE') . "\n";

assert($res['success'] === true, "Session creation failed!");
$sessionId = $res['data']['id'];

// 2. Test getCheckoutSession before payment
$getBefore = PayMongoHelper::getCheckoutSession($sessionId);
echo "\n2. Get Checkout Session (Initial):\n";
echo "   Status: " . ($getBefore['data']['attributes']['status'] ?? 'NONE') . "\n";
echo "   Ref: " . ($getBefore['data']['attributes']['reference_number'] ?? 'NONE') . "\n";
assert($getBefore['data']['attributes']['status'] === 'active');

// 3. Test markSimulatedSessionPaid
$markPaid = PayMongoHelper::markSimulatedSessionPaid($sessionId, 'QR Ph');
echo "\n3. Mark Session as Paid in Sandbox:\n";
echo "   Mark Result: " . ($markPaid ? 'SUCCESS' : 'FAILED') . "\n";
assert($markPaid === true);

// 4. Test getCheckoutSession after payment
$getAfter = PayMongoHelper::getCheckoutSession($sessionId);
echo "\n4. Get Checkout Session (After Payment):\n";
echo "   Status: " . ($getAfter['data']['attributes']['status'] ?? 'NONE') . "\n";
echo "   Payments count: " . count($getAfter['data']['attributes']['payments'] ?? []) . "\n";
assert($getAfter['data']['attributes']['status'] === 'paid');

// 5. Test invalid session ID handling
$invalid = PayMongoHelper::getCheckoutSession('{CHECKOUT_SESSION_ID}');
echo "\n5. Invalid Template Session ID:\n";
echo "   Success: " . ($invalid['success'] ? 'YES' : 'NO (Correctly handled)') . "\n";
assert($invalid['success'] === false);

echo "\n>>> ALL PAYMONGO HELPER & SANDBOX TESTS PASSED SUCCESSFULLY! <<<\n";
