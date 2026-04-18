define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
$db = getDbConnection();

$testUserId = 999;
$testType = 'picture';
$testData = "FAKE_IMAGE_DATA_" . bin2hex(random_bytes(10));
$testMime = "image/testing";

echo "--- DEEP DATABASE TEST ---\n";

try {
    // 1. Ensure table exists
    $db->exec("CREATE TABLE IF NOT EXISTS tenant_requirements (
        requirement_id INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT NOT NULL,
        valididfront LONGBLOB,
        valididfront_mime VARCHAR(100),
        picture LONGBLOB,
        picture_mime VARCHAR(100)
    )");
    echo "1. Table checked/created.\n";

    // 2. Mock Insert/Update
    require_once __DIR__ . '/../app/models/ApartmentApp.php';
    $model = new ApartmentApp();
    echo "2. Attempting updateRequirement for User 999...\n";
    $ok = $model->updateRequirement($testUserId, $testType, $testData, $testMime);
    
    if ($ok) {
        echo "3. SUCCESS: Model reported updateRequirement OK.\n";
        
        // 4. Verify in DB
        $stmt = $db->prepare("SELECT picture, picture_mime FROM tenant_requirements WHERE tenant_id = 999");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo "4. SUCCESS: Record found for User 999.\n";
            echo " - Length: " . strlen($row['picture']) . " bytes\n";
            echo " - MIME: " . $row['picture_mime'] . "\n";
            if ($row['picture'] === $testData) {
                echo "5. VERIFIED: Data matches perfectly.\n";
            } else {
                echo "5. ERROR: Data mismatch! (Expected " . strlen($testData) . ", Got " . strlen($row['picture']) . ")\n";
            }
        } else {
            echo "4. ERROR: Record NOT found in DB after 'successful' update.\n";
        }
    } else {
        echo "3. FAILED: Model reported failure.\n";
    }

    // Cleanup
    $db->exec("DELETE FROM tenant_requirements WHERE tenant_id = 999");
    echo "--- END TEST ---\n";

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
