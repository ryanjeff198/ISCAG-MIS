<?php
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/helpers/Auth.php';

class ApartmentController extends Controller {

    public function apply() {
        Auth::protectRole(['Guest']);
        $this->view('user/Apartment/tenant_add_information_form');
    }

    public function status() {
        Auth::protectRole(['Guest']);
        $userId = $_SESSION['user_id'];
        $model = new ApartmentApp();
        $application = $model->getApplication($userId);
        $uploadedDocs = $model->getUploadedDocTypes($userId);
        $tenantInfo = $model->getInfo($userId);
        
        $this->view('user/Apartment/tenant_status', [
            'application' => $application,
            'uploadedDocs' => $uploadedDocs,
            'tenantInfo' => $tenantInfo
        ]);
    }

    public function info() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];
        $model = new ApartmentApp();
        $application = $model->getApplication($userId);
        $tenantInfo = $model->getInfo($userId);
        $uploadedDocs = $model->getUploadedDocTypes($userId);
        
        $this->view('user/Apartment/apartment_information', [
            'application' => $application,
            'tenantInfo' => $tenantInfo,
            'uploadedDocs' => $uploadedDocs
        ]);
    }

    public function save() {
        Auth::protectRole(['Guest']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $body   = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            echo json_encode(['success' => false, 'message' => 'No data received']);
            return;
        }

        $model = new ApartmentApp();
        $ok = true;

        if (!empty($body['addinfo']) && is_array($body['addinfo'])) {
            if (!$model->saveInfo($userId, $body['addinfo'])) {
                $ok = false;
            }
        }

        if (!empty($body['roomtype'])) {
            if (!$model->saveApplication($userId, $body['roomtype'])) {
                $ok = false;
            }
        }

        echo json_encode(['success' => $ok]);
    }

    public function handleUpload() {
        Auth::protectRole(['Guest']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $type   = $_POST['type'] ?? 'picture';

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'File too large (max 5 MB)']);
            return;
        }

        $allowedMime = ['image/jpeg','image/png','image/gif','image/webp','application/pdf'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowedMime)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            return;
        }

        // Logic for saving to filesystem
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = "doc_{$userId}_{$type}_" . time() . "." . $ext;
        $relPath = "uploads/tenants/" . $fileName;
        $fullPath = BASE_PATH . "/public/" . $relPath;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save file to disk']);
            return;
        }

        $model = new ApartmentApp();
        $info = $model->getInfo($userId);
        
        if (empty($info)) {
            $infoId = $model->saveInfo($userId, []);
            $infoId = $infoId ?: $model->getInfo($userId)['tenant_info']; 
        } else {
             $infoId = $info['tenant_info'];
        }

        // Save path to DB, and set binaryData to NULL to save space
        if ($model->saveInfoImage($infoId, $type, null, $mime, $relPath)) {
            echo json_encode(['success' => true, 'type' => $type, 'path' => $relPath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database record']);
        }
    }

    public function serveImage() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];
        $type   = $_GET['type'] ?? '';

        $allowed = ['picture','valididfront','valididback','birthcert','nbi','proofofincome'];
        if (!in_array($type, $allowed)) {
            http_response_code(400);
            echo 'Invalid type';
            return;
        }

        $model  = new ApartmentApp();
        $info = $model->getInfo($userId);
        if (empty($info)) {
            http_response_code(404);
            echo 'Image not found';
            return;
        }
        $infoId = $info['tenant_info'];
        $result = $model->getAddInfoImage($infoId, $type);

        if (!$result) {
            http_response_code(404);
            echo 'Image not found';
            return;
        }

        // Logic: Check filesystem first
        if (!empty($result['file_path'])) {
            $fullPath = BASE_PATH . "/public/" . $result['file_path'];
            if (file_exists($fullPath)) {
                header('Content-Type: ' . $result['mime']);
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                return;
            }
        }

        // Fallback to BLOB if file not on disk
        if (!empty($result['data'])) {
            header('Content-Type: ' . $result['mime']);
            header('Content-Length: ' . strlen($result['data']));
            echo $result['data'];
            return;
        }

        http_response_code(404);
        echo 'Image not found';
    }

    public function parking() {
        Auth::protectRole(['Tenant']);
        $model = new ApartmentApp();
        $hasPending = $model->hasPendingParkingApplication($_SESSION['user_id']);
        $this->view('user/Apartment/tenant_parking', [
            'hasPendingParking' => $hasPending
        ]);
    }

    public function submitParking() {
        Auth::protectRole(['Tenant']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body || empty($body['vehicles'])) {
            echo json_encode(['success' => false, 'message' => 'No vehicles provided']);
            return;
        }

        $model = new ApartmentApp();
        $allSuccess = true;

        foreach ($body['vehicles'] as $vehicle) {
            // merge base fields with specific vehicle fields
            $payload = [
                'date' => $body['date'] ?? date('Y-m-d'),
                'dateStarted' => $body['dateStarted'] ?? '',
                'vehicleName' => $vehicle['vehicleName'],
                'vehicleOwner' => $vehicle['vehicleOwner'],
                'vehicleType' => $vehicle['vehicleType'],
                'plateNo' => $vehicle['plateNo']
            ];

            if (!$model->saveParkingApplication($userId, $payload)) {
                $allSuccess = false;
            }
        }

        if ($allSuccess) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Some applications failed to save']);
        }
    }

    public function finalizeSubmission() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $ok = $model->updateStatusByTenant($userId, 'Pending');
        
        echo json_encode(['success' => $ok]);
    }
}
