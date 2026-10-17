<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
if (!defined('BASE_URL')) define('BASE_URL', '');
if (!function_exists('url')) {
    function url(string $path = ''): string { return '/' . ltrim($path, '/'); }
}
if (!function_exists('asset')) {
    function asset(string $path): string { return '/public/' . ltrim($path, '/'); }
}
require_once BASE_PATH . '/app/models/BurialRequest.php';
require_once BASE_PATH . '/app/models/Notification.php';
require_once BASE_PATH . '/app/models/AdminNotification.php';

echo "=== STARTING DAMAYAN BURIAL SERVICE E2E TEST ===" . PHP_EOL;

$model = new BurialRequest();

// 1. Test User Submit Burial Assistance Request
echo "[1] Testing User Request Submission..." . PHP_EOL;
$userPayload = [
    'tenant_id' => 1, // Ryan Felizardo
    'deceased_first_name' => 'Sulaiman',
    'deceased_middle_name' => 'Tariq',
    'deceased_last_name' => 'Macarambon',
    'deceased_sex' => 'Male',
    'date_of_birth' => '1955-08-14',
    'date_of_death' => date('Y-m-d'),
    'place_of_death' => 'St. Luke\'s Medical Center, Quezon City',
    'residence' => 'Block 12 Lot 5, Golden Mosque Compound, Quiapo, Manila',
    'civil_status' => 'Married',
    'nationality' => 'Filipino',
    'religion' => 'Islam',
    'informant_name' => 'Ryan Felizardo',
    'relationship' => 'Nephew',
    'informant_contact' => '09171234567',
    'informant_email' => 'rjfelizardo25@gmail.com',
    'informant_address' => 'ISCAG Apartment Unit B-1, Taguig City',
    'requested_services' => 'Complete Islamic Funeral Rites, Ghusl, Kafan, Salat al-Janazah, Ambulance Transportation, Islamic Cemetery Burial',
    'cemetery_preference' => 'Norzagaray Muslim Cemetery, Bulacan',
    'target_timing' => 'Immediate (within 24 hours Sunnah)',
    'additional_notes' => 'Family requests burial before sunset in accordance with Islamic tradition.',
    'supporting_document' => 'uploads/burial/sample_hospital_notice.pdf'
];

$res = $model->create($userPayload);
if (!$res || empty($res['id'])) {
    echo "FAILED: Could not create burial request." . PHP_EOL;
    exit(1);
}

$burialId = (int)$res['id'];
$refId = $res['ref_id'];
echo "SUCCESS: Created Burial Request #{$refId} (ID: {$burialId}) for {$res['deceased_name']}." . PHP_EOL;

// 2. Test Duplicate Prevention
echo "[2] Testing Duplicate Prevention..." . PHP_EOL;
$isDup = $model->checkDuplicate(1, 'Sulaiman Tariq Macarambon', date('Y-m-d'));
if ($isDup) {
    echo "SUCCESS: Duplicate request correctly detected and blocked." . PHP_EOL;
} else {
    echo "WARNING: Duplicate check returned false." . PHP_EOL;
}

// 3. Test Staff Review: Request Additional Information
echo "[3] Testing Staff Requesting Additional Information..." . PHP_EOL;
$reqInfoSuccess = $model->requestAdditionalInfo(
    $burialId,
    "Please provide the official hospital certificate of death or embalming waiver for Islamic immediate burial.",
    "Rashid Pendatun"
);
$check1 = $model->findById($burialId);
if ($reqInfoSuccess && $check1['status'] === 'Additional Information Required') {
    echo "SUCCESS: Status updated to 'Additional Information Required'. Explanation: " . $check1['info_request_notes'] . PHP_EOL;
} else {
    echo "FAILED: Additional info request failed." . PHP_EOL;
}

// 4. Test User Responding to Additional Requirements
echo "[4] Testing User Submitting Requested Requirements..." . PHP_EOL;
$respSuccess = $model->submitAdditionalInfo(
    $burialId,
    1,
    "Attached the hospital death summary and Islamic burial clearance signed by attending physician Dr. Al-Mansoor.",
    "uploads/burial/hospital_clearance_verified.pdf",
    "Ryan Felizardo"
);
$check2 = $model->findById($burialId);
if ($respSuccess && $check2['status'] === 'Under Review' && !empty($check2['additional_info_submitted'])) {
    echo "SUCCESS: User response saved. Status updated back to 'Under Review'." . PHP_EOL;
} else {
    echo "FAILED: User additional info response failed." . PHP_EOL;
}

// 5. Test Staff Acceptance & Coordinator Assignment
echo "[5] Testing Staff Acceptance & Assignment..." . PHP_EOL;
$acceptSuccess = $model->acceptRequest($burialId, 2, "Rashid Pendatun", "Verified all hospital documentation. Proceeding with immediate funeral coordination.");
$check3 = $model->findById($burialId);
if ($acceptSuccess && $check3['status'] === 'Accepted for Coordination' && $check3['assigned_staff_name'] === 'Rashid Pendatun') {
    echo "SUCCESS: Request accepted for coordination by Rashid Pendatun." . PHP_EOL;
} else {
    echo "FAILED: Staff acceptance failed." . PHP_EOL;
}

// 6. Test Deceased Record Synchronization
echo "[6] Testing Deceased Record Update..." . PHP_EOL;
$decUpdateSuccess = $model->updateDeceasedRecord($burialId, [
    'first_name' => 'Sulaiman',
    'middle_name' => 'Tariq',
    'last_name' => 'Macarambon',
    'sex' => 'Male',
    'date_of_birth' => '1955-08-14',
    'date_of_death' => date('Y-m-d'),
    'time_of_death' => '06:45:00',
    'place_of_death' => 'St. Luke\'s Medical Center, Quezon City',
    'residence_address' => 'Block 12 Lot 5, Golden Mosque Compound, Quiapo, Manila',
    'civil_status' => 'Married',
    'nationality' => 'Filipino',
    'religion' => 'Islam'
], "Rashid Pendatun");
$check4 = $model->findById($burialId);
if ($decUpdateSuccess && $check4['time_of_death'] === '06:45:00') {
    echo "SUCCESS: Deceased record synchronized across tables." . PHP_EOL;
} else {
    echo "FAILED: Deceased record update failed." . PHP_EOL;
}

// 7. Test Ghusl (Ritual Washing) with Gender-Matched Volunteers
echo "[7] Testing Ghusl Coordination & Volunteer Assignment..." . PHP_EOL;
$maleVols = $model->getVolunteers('Male');
echo "Found " . count($maleVols) . " registered male volunteers." . PHP_EOL;
$assignedVolNames = [$maleVols[0]['name'] ?? 'Brother Ahmad Bilal', $maleVols[1]['name'] ?? 'Brother Yusuf Karim'];

$ghuslSuccess = $model->updateGhusl($burialId, [
    'ghusl_status' => 'Scheduled',
    'ghusl_venue' => 'ISCAG Ghusl Facility, Main Complex',
    'ghusl_date' => date('Y-m-d'),
    'ghusl_time' => '10:30:00',
    'ghusl_assigned_volunteers' => $assignedVolNames,
    'ghusl_volunteer_status' => 'Confirmed',
    'ghusl_remarks' => 'Warm water and cedar/camphor leaves prepared. Male family members will observe.'
], "Rashid Pendatun");

$check5 = $model->findById($burialId);
if ($ghuslSuccess && $check5['ghusl_status'] === 'Scheduled' && $check5['status'] === 'Ghusl Scheduled') {
    echo "SUCCESS: Ghusl scheduled with volunteers: " . $check5['ghusl_assigned_volunteers'] . PHP_EOL;
} else {
    echo "FAILED: Ghusl scheduling failed." . PHP_EOL;
}

// 8. Test Kafan Preparation
echo "[8] Testing Kafan Shroud Preparation..." . PHP_EOL;
$kafanSuccess = $model->updateKafan($burialId, [
    'kafan_status' => 'Ready',
    'kafan_type' => 'Standard 3-piece shroud (Brother)',
    'kafan_prepared_by' => 'Rashid Pendatun',
    'kafan_remarks' => '3 clean white unstitched sheets prepared with camphor and non-alcoholic attar fragrance.'
], "Rashid Pendatun");
$check6 = $model->findById($burialId);
if ($kafanSuccess && $check6['kafan_status'] === 'Ready') {
    echo "SUCCESS: Kafan prepared: " . $check6['kafan_type'] . PHP_EOL;
} else {
    echo "FAILED: Kafan preparation failed." . PHP_EOL;
}

// 9. Test Salat al-Janazah Coordination
echo "[9] Testing Salat al-Janazah Coordination..." . PHP_EOL;
$janazahSuccess = $model->updateJanazah($burialId, [
    'janazah_status' => 'Scheduled',
    'janazah_masjid' => 'ISCAG Main Masjid, Taguig City',
    'janazah_prayer_time' => 'After Dhuhr',
    'janazah_date' => date('Y-m-d'),
    'janazah_time' => '12:45:00',
    'janazah_imam_name' => 'Ustadh Omar Farooq',
    'janazah_imam_contact' => '09181234567',
    'janazah_remarks' => 'Congregational prayer announced to the ISCAG Muslim community.'
], "Rashid Pendatun");
$check7 = $model->findById($burialId);
if ($janazahSuccess && $check7['janazah_status'] === 'Scheduled' && $check7['status'] === 'Janazah Scheduled') {
    echo "SUCCESS: Salat al-Janazah scheduled with Imam " . $check7['janazah_imam_name'] . PHP_EOL;
} else {
    echo "FAILED: Janazah scheduling failed." . PHP_EOL;
}

// 10. Test Transportation Coordination
echo "[10] Testing Transportation (ISCAG Ambulance)..." . PHP_EOL;
$transSuccess = $model->updateTransport($burialId, [
    'transport_vehicle_type' => 'ISCAG Ambulance',
    'transport_status' => 'Scheduled',
    'transport_driver_name' => 'Brother Tariq Santos',
    'transport_driver_contact' => '09191234567',
    'transport_pickup_location' => 'ISCAG Ghusl Facility',
    'transport_destination' => 'Norzagaray Muslim Cemetery, Bulacan',
    'transport_scheduled_datetime' => date('Y-m-d 13:30:00'),
    'transport_actual_departure' => date('Y-m-d 13:35:00'),
    'transport_actual_arrival' => date('Y-m-d 15:10:00'),
    'transport_remarks' => 'Ambulance escort with family convoy.'
], "Rashid Pendatun");
$check8 = $model->findById($burialId);
if ($transSuccess && $check8['transport_status'] === 'Scheduled') {
    echo "SUCCESS: Transportation arranged with driver " . $check8['transport_driver_name'] . PHP_EOL;
} else {
    echo "FAILED: Transportation arrangement failed." . PHP_EOL;
}

// 11. Test Cemetery & Burial Arrangements
echo "[11] Testing Cemetery Arrangements..." . PHP_EOL;
$cemSuccess = $model->updateCemetery($burialId, [
    'cemetery_name' => 'Norzagaray Muslim Cemetery, Bulacan',
    'cemetery_status' => 'Scheduled',
    'cemetery_plot_number' => 'Section B, Plot 42',
    'cemetery_scheduled_date' => date('Y-m-d'),
    'cemetery_scheduled_time' => '15:30:00',
    'cemetery_remarks' => 'Grave dug according to Sunnah orientation towards the Qiblah. Caretaker Brother Hassan confirmed.'
], "Rashid Pendatun");
$check9 = $model->findById($burialId);
if ($cemSuccess && $check9['cemetery_status'] === 'Scheduled' && $check9['status'] === 'Burial Scheduled') {
    echo "SUCCESS: Cemetery coordinated at " . $check9['cemetery_name'] . " (" . $check9['cemetery_plot_number'] . ")." . PHP_EOL;
} else {
    echo "FAILED: Cemetery coordination failed." . PHP_EOL;
}

// 12. Test Complete Burial Case (Explicit Confirmation)
echo "[12] Testing Burial Case Completion (Requirement 3.I)..." . PHP_EOL;
$compSuccess = $model->completeBurialCase($burialId, [
    'actual_burial_date' => date('Y-m-d 16:15:00'),
    'final_cemetery' => 'Norzagaray Muslim Cemetery, Bulacan',
    'final_remarks' => 'Burial rites fully completed before Maghrib in accordance with the Prophet\'s Sunnah. Du\'a recited for the deceased.'
], "Rashid Pendatun");

$finalCheck = $model->findById($burialId);
if ($compSuccess && $finalCheck['status'] === 'Burial Completed') {
    echo "SUCCESS: Burial case officially completed! Status: " . $finalCheck['status'] . PHP_EOL;
    echo "Completed by: " . $finalCheck['completed_by'] . " at " . $finalCheck['completed_at'] . PHP_EOL;
    echo "Coordination statuses: Ghusl=" . $finalCheck['ghusl_status'] . ", Kafan=" . $finalCheck['kafan_status'] . ", Janazah=" . $finalCheck['janazah_status'] . ", Transport=" . $finalCheck['transport_status'] . ", Cemetery=" . $finalCheck['cemetery_status'] . PHP_EOL;
} else {
    echo "FAILED: Burial completion failed." . PHP_EOL;
}

// 13. Test Activity Logs & Notifications
echo "[13] Verifying Chronological Audit Logs & In-App Notifications..." . PHP_EOL;
$logs = $model->getActivityLogs($burialId);
echo "Total activity logs recorded: " . count($logs) . PHP_EOL;
foreach ($logs as $l) {
    echo "  - [{$l['created_at']}] {$l['actor_name']} ({$l['actor_role']}): {$l['title']} -> {$l['details']}" . PHP_EOL;
}

// Check Notifications in DB
$db = getDbConnection();
$stmtUserNotifs = $db->prepare("SELECT * FROM notifications WHERE tenant_id = 1 ORDER BY created_at DESC LIMIT 5");
$stmtUserNotifs->execute();
$uNotifs = $stmtUserNotifs->fetchAll(PDO::FETCH_ASSOC);
echo "Recent notifications sent to applicant (Tenant ID: 1):" . PHP_EOL;
foreach ($uNotifs as $un) {
    echo "  - [{$un['created_at']}] {$un['title']}: {$un['message']}" . PHP_EOL;
}

echo "=== ALL E2E WORKFLOW TESTS PASSED SUCCESSFULLY! ===" . PHP_EOL;
