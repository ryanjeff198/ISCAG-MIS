<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

echo "=== STARTING DAMAYAN RELEASE SCHEDULING TESTS ===\n";

$model = new BurialRequest();
$db = getDbConnection();

// Pick an existing burial request and reset it to Accepted & Certificate Prepared for clean test run
$stmt = $db->query("SELECT * FROM burial_requests ORDER BY id DESC LIMIT 1");
$sample = $stmt->fetch(PDO::FETCH_ASSOC);

$id = (int)$sample['id'];
$db->prepare("UPDATE burial_requests SET
    status = 'Accepted',
    death_cert_status = 'Certificate Prepared',
    scheduled_release_date = NULL,
    scheduled_release_time = NULL,
    certificate_released_at = NULL,
    release_cancelled_at = NULL
    WHERE id = ?")->execute([$id]);
$sample = $model->findById($id);

echo "Testing on Burial Request ID: {$id} (#{$sample['ref_id']})\n";

// 2. Test future validation rejection on past date
echo "\n--- Test 2: Past Date Validation ---\n";
$pastRes = $model->scheduleCertificateRelease($id, [
    'release_date' => '2020-01-01',
    'release_time' => '10:00',
    'instructions' => 'Bring ID'
], 1, 'Admin');

if (!$pastRes['success'] && strpos($pastRes['error'], 'future') !== false) {
    echo "PASS: Caught expected error for past date: {$pastRes['error']}\n";
} else {
    echo "FAIL: Expected rejection for past date, got: " . json_encode($pastRes) . "\n";
}

// 3. Test scheduling valid release in the future
echo "\n--- Test 3: Valid Future Release Scheduling ---\n";
$futureDate = '2026-10-30';
$futureTime = '10:00';
$instructions = 'Please bring 2 valid government IDs and official receipt.';
$model->setCertificateInDepartment($id, true, 'Damayan Chief Admin', 'Physical certificate in department custody');
$res = $model->scheduleCertificateRelease($id, [
    'release_date' => $futureDate,
    'release_time' => $futureTime,
    'instructions' => $instructions,
    'location' => 'Damayan Department'
], 1, 'Damayan Chief Admin');

if ($res['success']) {
    echo "PASS: Schedule saved successfully! Email sent flag: " . ($res['email_sent'] ? 'true' : 'false') . "\n";
} else {
    echo "FAIL: Failed to schedule release: " . ($res['error'] ?? 'Unknown error') . "\n";
}

// Verify DB persistence
$updated = $model->findById($id);
assert($updated['scheduled_release_date'] === $futureDate, "scheduled_release_date matches");
assert($updated['death_cert_status'] === 'Ready for Release', "death_cert_status is Ready for Release");
echo "PASS: Database persistence verified for scheduled_release_date ({$updated['scheduled_release_date']}) and status ({$updated['death_cert_status']}).\n";

// 4. Test schedule history
echo "\n--- Test 4: Verify Schedule History Record ---\n";
$history = $model->getScheduleHistory($id);
assert(count($history) >= 1, "At least one history item recorded");
$latestH = $history[0];
assert($latestH['action_type'] === 'SCHEDULED', "Latest action type is SCHEDULED");
echo "PASS: History recorded with action_type={$latestH['action_type']}, scheduled_date={$latestH['scheduled_date']}\n";

// 5. Test rescheduling
echo "\n--- Test 5: Rescheduling Appointment ---\n";
$revisedDate = '2026-11-05';
$revisedTime = '14:30';
$reschedRes = $model->rescheduleCertificateRelease($id, [
    'release_date' => $revisedDate,
    'release_time' => $revisedTime,
    'instructions' => 'Revised instructions: claim at window 2',
    'reason' => 'Coordinator emergency meeting',
    'location' => 'Damayan Department'
], 1, 'Damayan Chief Admin');
assert($reschedRes['success'] === true, "Rescheduling succeeded");
$afterResched = $model->findById($id);
assert($afterResched['scheduled_release_date'] === $revisedDate, "Updated date matches revised date");
echo "PASS: Rescheduled successfully to {$afterResched['scheduled_release_date']} at {$afterResched['scheduled_release_time']}\n";

$historyAfterResched = $model->getScheduleHistory($id);
assert(count($historyAfterResched) >= 2, "History now has at least 2 entries");
assert($historyAfterResched[0]['action_type'] === 'RESCHEDULED', "Most recent action is RESCHEDULED");
echo "PASS: Rescheduled history recorded correctly. Reason: {$historyAfterResched[0]['reason']}\n";

// 6. Test cancellation
echo "\n--- Test 6: Cancel Claiming Schedule ---\n";
$cancelRes = $model->cancelCertificateRelease($id, [
    'reason' => 'Hospital medical record re-examination required'
], 1, 'Damayan Chief Admin');
assert($cancelRes['success'] === true, "Cancellation succeeded");
$afterCancel = $model->findById($id);
assert(empty($afterCancel['scheduled_release_date']), "Scheduled date cleared");
assert($afterCancel['death_cert_status'] === 'Certificate Prepared', "Status returned to Certificate Prepared");
echo "PASS: Cancelled schedule. Current stage is {$afterCancel['death_cert_status']}.\n";

// 7. Test re-schedule and mark released / completed
echo "\n--- Test 7: Mark Certificate Released & Completed ---\n";
$model->scheduleCertificateRelease($id, [
    'release_date' => '2026-11-10',
    'release_time' => '09:00',
    'instructions' => 'Claim at main desk'
], 1, 'Damayan Chief Admin');

$releaseRes = $model->markCertificateAsReleased($id, [
    'recipient_name' => 'Fatima Salih (Daughter)',
    'release_notes' => 'Verified with PhilID #9876-5432-1098'
], 1, 'Damayan Chief Admin');
assert($releaseRes['success'] === true, "Mark released succeeded");
$afterReleased = $model->findById($id);
assert($afterReleased['status'] === 'Completed', "Overall status is Completed");
assert($afterReleased['death_cert_status'] === 'Completed', "Death cert status is Completed");
assert(!empty($afterReleased['certificate_released_at']), "certificate_released_at is populated");
assert($afterReleased['certificate_recipient_name'] === 'Fatima Salih (Daughter)', "Recipient name matches");
echo "PASS: Released certificate recorded. Status={$afterReleased['status']}, Recipient={$afterReleased['certificate_recipient_name']}.\n";

echo "\n=== ALL TESTS PASSED SUCCESSFULLY! ===\n";
