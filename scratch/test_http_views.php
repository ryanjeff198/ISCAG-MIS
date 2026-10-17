<?php
function testSessionLogin($email, $password, $targetUrls, $roleName) {
    echo "=== TESTING $roleName SESSION ($email) ===\n";
    $cookieFile = __DIR__ . '/cookie_' . preg_replace('/[^a-z0-9]/i', '_', $email) . '.txt';
    if (file_exists($cookieFile)) unlink($cookieFile);

    // 1. GET login page to retrieve CSRF token and set session cookie
    $ch = curl_init('http://localhost/ISCAG-MIS/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $page = curl_exec($ch);
    curl_close($ch);

    $csrf = '';
    if (preg_match('/name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']/i', $page, $m)) {
        $csrf = $m[1];
    }
    echo " - Retrieved CSRF Token: " . ($csrf ? substr($csrf, 0, 10) . '...' : 'NONE') . "\n";

    // 2. Submit login POST with CSRF token
    $ch = curl_init('http://localhost/ISCAG-MIS/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'csrf_token' => $csrf,
        'email' => $email,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $loginHtml = curl_exec($ch);
    $loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo " - Login response HTTP $loginCode\n";

    // 3. Fetch authenticated pages
    foreach ($targetUrls as $label => $path) {
        $ch = curl_init('http://localhost/ISCAG-MIS' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $hasTitle = preg_match('/<title>(.*?)<\/title>/i', $html, $m) ? $m[1] : 'No title';
        $hasDeathCert = stripos($html, 'Death Certificate') !== false ? 'YES' : 'NO';
        $hasCemetery = stripos($html, 'Cemetery') !== false ? 'YES' : 'NO';
        $hasGhusl = stripos($html, 'Ghusl') !== false ? 'FOUND (unexpected)' : 'CLEAN (none)';

        echo " - [$label] $path -> HTTP $code | Title: $hasTitle (Length: " . strlen($html) . ")\n";
        echo "   * Snippet: " . trim(substr(preg_replace('/\s+/', ' ', strip_tags($html)), 0, 120)) . "\n";
        echo "   * Death Cert present: $hasDeathCert | Cemetery present: $hasCemetery | Deprecated Rites: $hasGhusl\n";
    }
    echo "\n";
}

testSessionLogin('rjfelizardo25@gmail.com', 'password123', [
    'User Burial Dashboard' => '/user/services/burial-dashboard',
    'User Burial Details/Form' => '/user/services/burial-details',
    'User Main Dashboard' => '/user/dashboard'
], 'USER');

testSessionLogin('rpendatun@iscag.org', 'password123', [
    'Admin Damayan Dashboard' => '/admin/damayan',
    'Admin Burial Requests' => '/admin/damayan/burial-requests',
    'Admin Plot Management' => '/admin/damayan/burial'
], 'STAFF ADMIN');
