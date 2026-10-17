<?php
$cookieFile = __DIR__ . '/cookie_rpendatun_iscag_org.txt';

// Test available plots API
$ch = curl_init('http://localhost/ISCAG-MIS/admin/damayan/cemetery-plots/available');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);
echo "[API Test 1] GET /admin/damayan/cemetery-plots/available:\n";
echo " - Success: " . ($data['success'] ? 'YES' : 'NO') . " | Available plots count: " . count($data['plots'] ?? []) . "\n";

// Test get case details API
$ch = curl_init('http://localhost/ISCAG-MIS/admin/damayan/burial/details?id=18');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$res = curl_exec($ch);
curl_close($ch);
$caseData = json_decode($res, true);
echo "[API Test 2] GET /admin/damayan/burial/details?id=18:\n";
echo " - Success: " . ($caseData['success'] ? 'YES' : 'NO') . "\n";
if ($caseData['success']) {
    $b = $caseData['burial'];
    echo " - Ref ID: {$b['ref_id']} | Deceased: {$b['deceased_name']}\n";
    echo " - Death Cert Status: {$b['death_cert_status']} | Registry No: {$b['death_cert_registry_no']}\n";
    echo " - Cemetery Plot: {$b['assigned_plot_number']} ({$b['assigned_section']}) | Status: {$b['cemetery_plot_status']}\n";
    echo " - Available plots in response: " . count($caseData['available_plots']) . "\n";
    echo " - Audit logs in response: " . count($caseData['audit_logs']) . "\n";
}
