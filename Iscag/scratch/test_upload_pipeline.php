<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/controllers/ApartmentController.php';

// Mock Session
session_start();
$_SESSION['user_id'] = 4; // User from previous failure
$_SESSION['role'] = 'Tenant';

// Mock Post
$_POST['type'] = 'picture';

// Mock Files
$testImage = BASE_PATH . '/scratch/test_photo.jpg';
// Valid JPEG SOI + some random data
$jpegData = "\xFF\xD8\xFF\xE0\x00\x10\x4A\x46\x49\x46\x00\x01\x01\x01\x00\x48\x00\x48\x00\x00\xFF\xDB\x00\x43" . bin2hex(random_bytes(100));
file_put_contents($testImage, $jpegData);

$_FILES['file'] = [
    'name' => 'test_photo.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => $testImage,
    'error' => 0,
    'size' => filesize($testImage)
];

echo "--- MOCK UPLOAD TEST ---\n";

class TestApartmentController extends ApartmentController {
    public function testHandleUpload() {
        $this->handleUpload();
    }
}

$ctrl = new TestApartmentController();

// Use output buffering to capture the JSON response
ob_start();
$ctrl->testHandleUpload();
$response = ob_get_clean();

echo "Response from Controller: $response\n";

$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "SUCCESS: Controller reported success.\n";
    
    // Verify in DB
    require_once BASE_PATH . '/config/database.php';
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT picture FROM tenant_requirements WHERE tenant_id = 4");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && strlen($row['picture']) > 0) {
        echo "VERIFIED: Data is in DB for User 4!\n";
    } else {
        echo "ERROR: Table still empty for User 4!\n";
    }
} else {
    echo "FAILED: " . ($data['message'] ?? 'No message') . "\n";
}

// Cleanup
unlink($testImage);
echo "--- END TEST ---\n";
