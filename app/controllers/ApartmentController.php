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

        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'File too large (max 2 MB)']);
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

    public function removeImage() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $type = $_GET['type'] ?? '';

        if (empty($type)) {
            echo json_encode(['success' => false, 'message' => 'Type missing']);
            return;
        }

        $model = new ApartmentApp();
        $info  = $model->getInfo($userId);
        if (!$info) {
            echo json_encode(['success' => false, 'message' => 'No info record']);
            return;
        }

        $ok = $model->deleteInfoImage($info['tenant_info'], $type);
        echo json_encode(['success' => $ok]);
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

    // ═══ LEASE CONTRACT MODULE ═══════════════════════════

    /**
     * Lease Preview Page — shows lease details for the tenant.
     */
    public function lease() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];

        require_once BASE_PATH . '/app/models/Lease.php';
        require_once BASE_PATH . '/app/models/ApartmentApp.php';

        $leaseModel = new Lease();
        $appModel   = new ApartmentApp();

        $lease       = $leaseModel->getLeaseByTenantId($userId);
        $application = $appModel->getApplication($userId);
        $tenantInfo  = $appModel->getInfo($userId);

        // Fetch apartment type details for inclusions / rules
        $typeData = null;
        if ($lease && !empty($lease['unit_type'])) {
            require_once BASE_PATH . '/app/models/ApartmentType.php';
            $typeModel = new ApartmentType();
            $types = $typeModel->getAllTypes();
            foreach ($types as $t) {
                if (stripos($t['label'], $lease['unit_type']) !== false || $t['type_key'] === $lease['unit_type']) {
                    $typeData = $t;
                    break;
                }
            }
        }

        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        $renewalModel = new LeaseRenewal();
        $pendingRenewal = null;

        if ($lease && $lease['lease_status'] === 'Active') {
            $pendingRenewal = $renewalModel->getPendingRenewal((int) $lease['lease_id']);
        }

        $this->view('user/Apartment/tenant_lease', [
            'lease'          => $lease,
            'application'    => $application,
            'tenantInfo'     => $tenantInfo,
            'typeData'       => $typeData,
            'pendingRenewal' => $pendingRenewal
        ]);
    }

    /**
     * Accept or Reject Lease — tenant POST action.
     */
    public function acceptLease() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];

        $body = json_decode(file_get_contents('php://input'), true);
        $action = $body['action'] ?? '';

        if (!in_array($action, ['accept', 'reject'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            return;
        }

        require_once BASE_PATH . '/app/models/Lease.php';
        $leaseModel = new Lease();
        $lease = $leaseModel->getLeaseByTenantId($userId);

        if (!$lease) {
            echo json_encode(['success' => false, 'message' => 'No lease found']);
            return;
        }

        if ($lease['lease_status'] !== 'Pending') {
            echo json_encode(['success' => false, 'message' => 'Lease is not in Pending status']);
            return;
        }

        if ($action === 'accept') {
            $ok = $leaseModel->acceptLease($lease['lease_id'], $userId);
            if ($ok) {
                // Generate initial payments upon successful acceptance
                require_once BASE_PATH . '/app/models/Payment.php';
                $paymentModel = new Payment();
                $paymentModel->generateInitialPayments(
                    (int) $lease['lease_id'],
                    $userId,
                    (float) $lease['deposit_amount'],
                    (float) $lease['advance_amount']
                );
            }
        } else {
            $ok = $leaseModel->rejectLease($lease['lease_id'], $userId);
        }

        echo json_encode([
            'success' => $ok,
            'status'  => $action === 'accept' ? 'Accepted' : 'Rejected'
        ]);
    }

    // ═══ PAYMENT MODULE ══════════════════════════════════

    /**
     * Payment Breakdown Page.
     */
    public function payment() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];

        require_once BASE_PATH . '/app/models/Lease.php';
        require_once BASE_PATH . '/app/models/Payment.php';

        $leaseModel = new Lease();
        $paymentModel = new Payment();

        $lease = $leaseModel->getLeaseByTenantId($userId);
        
        // Validation Rule: DO NOT allow payment if lease_status != "ACCEPTED" or "ACTIVE"
        // Active means they are paid already, but they can view it.
        // We will pass the payments list to the view.
        $payments = [];
        if ($lease && in_array($lease['lease_status'], ['Accepted', 'Active'])) {
            $payments = $paymentModel->getPaymentsByLease($lease['lease_id']);
            
            // Just in case they weren't generated during acceptance (backward compatibility)
            if (empty($payments) && $lease['lease_status'] === 'Accepted') {
                $paymentModel->generateInitialPayments(
                    (int) $lease['lease_id'],
                    $userId,
                    (float) $lease['deposit_amount'],
                    (float) $lease['advance_amount']
                );
                $payments = $paymentModel->getPaymentsByLease($lease['lease_id']);
            }
        }

        $this->view('user/Apartment/tenant_payment', [
            'lease'    => $lease,
            'payments' => $payments
        ]);
    }

    /**
     * Submit a payment (simulate processing).
     */
    public function submitPayment() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $paymentId = $body['payment_id'] ?? 0;
        $refNo = $body['reference'] ?? '';

        if (!$paymentId) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment ID']);
            return;
        }

        require_once BASE_PATH . '/app/models/Payment.php';
        $paymentModel = new Payment();

        $ok = $paymentModel->markAsPaid((int) $paymentId, $refNo);

        echo json_encode(['success' => $ok]);
    }

    /**
     * Request Contract Renewal (Tenant Action)
     */
    public function requestRenewal() {
        Auth::protectRole(['Tenant']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $leaseId = $body['lease_id'] ?? 0;
        $userId = $_SESSION['user_id'];

        if (!$leaseId) {
            echo json_encode(['success' => false, 'message' => 'Invalid lease ID']);
            return;
        }

        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        $renewalModel = new LeaseRenewal();

        $ok = $renewalModel->requestRenewal((int) $leaseId, $userId);

        echo json_encode(['success' => $ok]);
    }
}
