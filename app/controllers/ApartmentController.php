<?php
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/helpers/Auth.php';

class ApartmentController extends Controller {

    public function apply() {
        Auth::protectRole(['Applicant', 'Tenant']);
        $this->view('user/Apartment/tenant_add_information_form');
    }

    public function status() {
        Auth::protectRole(['Applicant', 'Tenant']);
        $this->view('user/Apartment/tenant_status');
    }

    public function info() {
        Auth::protectRole(['Applicant', 'Tenant']);
        $this->view('user/Apartment/apartment_information');
    }

    public function save() {
        Auth::protectRole(['Applicant', 'Tenant']);
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
        Auth::protectRole(['Applicant', 'Tenant']);
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

        $binaryData = file_get_contents($file['tmp_name']);
        if ($binaryData === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to read file']);
            return;
        }

        $model = new ApartmentApp();
        $info = $model->getInfo($userId);
        
        if (empty($info)) {
            // Need a dummy record or they should have completed Step 1
            $infoId = $model->saveInfo($userId, []); // Creates an empty addinfo record
            $infoId = $infoId ?: $model->getInfo($userId)['tenant_info']; 
        } else {
             $infoId = $info['tenant_info'];
        }

        if ($model->saveInfoImage($infoId, $type, $binaryData, $mime)) {
            echo json_encode(['success' => true, 'type' => $type]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to store in database']);
        }
    }

    public function serveImage() {
        Auth::protectRole(['Applicant', 'Tenant']);
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
            // Fallback to legacy tenant_requirements if needed? No, let's just 404
            http_response_code(404);
            echo 'Image not found';
            return;
        }

        header('Content-Type: ' . $result['mime']);
        header('Content-Length: ' . strlen($result['data']));
        header('Cache-Control: private, max-age=3600');
        echo $result['data'];
    }

    public function parking() {
        Auth::protectRole(['Tenant']);
        $this->view('user/Apartment/tenant_parking');
    }
}
