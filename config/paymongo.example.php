<?php
/**
 * PayMongo API Configuration Template
 * 
 * Supports both Sandbox (Test) and Production (Live) environments.
 * 
 * Instructions:
 * - Test Mode: uses 'sk_test_...' and 'pk_test_...'. Real money is NOT charged.
 * - Live Mode: uses 'sk_live_...' and 'pk_live_...'. Charges real funds.
 *   (Requires approved/activated PayMongo merchant account).
 */

// Environment Mode: 'test' (sandbox) or 'live' (production)
$mode = 'test';

// Test Sandbox Credentials (replace with your PayMongo test keys)
$testKeys = [
    'secret_key' => 'YOUR_PAYMONGO_TEST_SECRET_KEY', // Must start with sk_test_
    'public_key' => 'YOUR_PAYMONGO_TEST_PUBLIC_KEY', // Must start with pk_test_
];

// Production Live Credentials (paste your live keys here once approved)
$liveKeys = [
    'secret_key' => 'YOUR_PAYMONGO_LIVE_SECRET_KEY', // Must start with sk_live_
    'public_key' => 'YOUR_PAYMONGO_LIVE_PUBLIC_KEY', // Must start with pk_live_
];

// Determine active keys:
$useLive = ($mode === 'live' && !empty($liveKeys['secret_key']) && strpos($liveKeys['secret_key'], 'sk_live_') === 0);
$active = $useLive ? $liveKeys : $testKeys;

return [
    'mode'                 => $useLive ? 'live' : 'test',
    'secret_key'           => $active['secret_key'],
    'public_key'           => $active['public_key'],
    'api_base'             => 'https://api.paymongo.com/v1',
    'currency'             => 'PHP',
    'payment_method_types' => [
        'qrph',
        'gcash',
        'paymaya',
        'card',
        'dob', // Direct Online Banking
        'grab_pay'
    ],
    'test' => $testKeys,
    'live' => $liveKeys,
];
