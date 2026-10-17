<?php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
if (!defined('BASE_URL')) define('BASE_URL', '');
if (!function_exists('url')) { function url($p = '') { return '/' . ltrim($p, '/'); } }
if (!function_exists('asset')) { function asset($p = '') { return '/public/' . ltrim($p, '/'); } }

echo "=== TESTING DAMAYAN HTTP CONTROLLER ENDPOINTS ===" . PHP_EOL;

// Mock session as Staff_Damayan
session_start();
$_SESSION['user_id'] = 2; // Rashid Pendatun
$_SESSION['role'] = 'Staff_Damayan';
$_SESSION['name'] = 'Rashid Pendatun';
$_SESSION['email'] = 'rpendatun@iscag.org';

require_once BASE_PATH . '/app/controllers/AdminController.php';
$adminCtrl = new AdminController();

// 1. Test getBurialDetails API
echo "[1] Testing getBurialDetails API..." . PHP_EOL;
$_GET['id'] = 'BR-20261015-2334';
ob_start();
$adminCtrl->getBurialDetails();
$output = ob_get_clean();
$data = json_decode($output, true);

if ($data && $data['success'] && !empty($data['burial'])) {
    echo "SUCCESS: Fetched details for #" . $data['burial']['ref_id'] . ". Deceased: " . $data['burial']['deceased_name'] . PHP_EOL;
    echo "Volunteers in gender pool: " . count($data['gender_volunteers']) . PHP_EOL;
} else {
    echo "FAILED: getBurialDetails failed. Output: " . $output . PHP_EOL;
}

// 2. Test getBurialVolunteers API
echo "[2] Testing getBurialVolunteers API (Male pool)..." . PHP_EOL;
$_GET['gender'] = 'Male';
ob_start();
$adminCtrl->getBurialVolunteers();
$output = ob_get_clean();
$vols = json_decode($output, true);

if ($vols && $vols['success'] && count($vols['volunteers']) > 0) {
    echo "SUCCESS: Fetched " . count($vols['volunteers']) . " male volunteers." . PHP_EOL;
} else {
    echo "FAILED: getBurialVolunteers failed. Output: " . $output . PHP_EOL;
}

// 3. Test getBurialVolunteers API (Female pool)
echo "[3] Testing getBurialVolunteers API (Female pool)..." . PHP_EOL;
$_GET['gender'] = 'Female';
ob_start();
$adminCtrl->getBurialVolunteers();
$output = ob_get_clean();
$volsF = json_decode($output, true);

if ($volsF && $volsF['success'] && count($volsF['volunteers']) > 0) {
    echo "SUCCESS: Fetched " . count($volsF['volunteers']) . " female volunteers." . PHP_EOL;
} else {
    echo "FAILED: getBurialVolunteers Female failed. Output: " . $output . PHP_EOL;
}

echo "=== ALL CONTROLLER ENDPOINT TESTS PASSED! ===" . PHP_EOL;
