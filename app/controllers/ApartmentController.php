<?php
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/config/database.php';

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
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            $vc = count($body['vehicles']);
            $adminNotif->create(
                'Parking Application Received',
                $tenantName . ' has submitted a parking application for ' . $vc . ' vehicle(s).',
                'request',
                $tenantName,
                $userId,
                '/admin/mis_admin/parking_approval'
            );

            // Notify the tenant
            require_once BASE_PATH . '/app/models/Notification.php';
            $tenantNotif = new Notification();
            $tenantNotif->create(
                $userId,
                'Parking Request Submitted',
                'Your parking application for ' . $vc . ' vehicle(s) has been successfully submitted and is under review.',
                'system'
            );

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
        
        if ($ok) {
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A user';
            $adminNotif->create(
                'New Application Received',
                $tenantName . ' has submitted a new apartment application.',
                'request',
                $tenantName,
                $userId,
                '/admin/apartment/confirmation'
            );

            // Notify the tenant
            require_once BASE_PATH . '/app/models/Notification.php';
            $tenantNotif = new Notification();
            $tenantNotif->create(
                $userId,
                'Application Under Review',
                'Your apartment application has been submitted successfully and is currently undergoing administrative review.',
                'system'
            );
        }
        
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
        $term = (int) ($body['term'] ?? 12);

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
            $ok = $leaseModel->acceptLease($lease['lease_id'], $userId, $term);
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
        
        // A) Initial Payments (Deposit & Advance)
        $payments = [];
        if ($lease && in_array($lease['lease_status'], ['Accepted', 'Active'])) {
            $payments = $paymentModel->getPaymentsByLease($lease['lease_id']);
            
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

        // B) Recurring Charges (Monthly Rent, Water, Parking)
        $recurringCharges = [];
        if ($lease && $lease['lease_status'] === 'Active') {
            $db = getDbConnection();
            $simulationMonths = 0; // REAL TIME
            $now = clone (new \DateTime());
            
            // Family members for water bill
            $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM tenant_family_members WHERE tenant_id = ?");
            $stmt->execute([$userId]);
            $memberCount = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] ?? 0);
            $occupants = $memberCount + 1; // +1 for the tenant

            // Parking
            $stmt = $db->prepare("SELECT parking_id, vehiclename, plateno, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
            $stmt->execute([$userId]);
            $parkingApps = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            // Extract paid recurring keys so we can mark them as PAID
            $paidRecurringKeys = [];
            $advancePaymentCount = 0;
            $advanceWaterCount = 0;
            $advanceParkingCount = 0;
            $advanceContributionCount = 0;
            foreach ($payments as $p) {
                if ($p['payment_status'] === 'Paid' && strpos($p['payment_type'], '-') !== false) {
                    $paidRecurringKeys[] = strtolower($p['payment_type']);
                    if (strtolower($p['payment_type']) === 'rent-advance') {
                        $advancePaymentCount++;
                        $simulationMonths++;
                    }
                    if (strtolower($p['payment_type']) === 'water-advance') $advanceWaterCount++;
                    if (strtolower($p['payment_type']) === 'parking-advance') $advanceParkingCount++;
                    if (strtolower($p['payment_type']) === 'contribution-advance') $advanceContributionCount++;
                }
            }

            if ($simulationMonths > 0) {
                $now->modify("+$simulationMonths month");
            }

            // Generate monthly charges from lease start
            $leaseStart = new \DateTime($lease['start_date']);
            $leaseEnd = $lease['end_date'] ? new \DateTime($lease['end_date']) : null;
            $limitDate = ($leaseEnd && $leaseEnd < $now) ? $leaseEnd : $now;

            $currentDate = clone $leaseStart;
            $monthCount = 0;

            while ($currentDate <= $limitDate) {
                $monthName = $currentDate->format('F Y');
                $monthKey = $currentDate->format('Y-m');

                // Skip Month 0 (covered by Advance Rent)
                if ($monthCount > 0) {
                    // Monthly Rent
                    $rentId = 'rent-' . $monthKey;
                    $isPaid = in_array(strtolower($rentId), $paidRecurringKeys);
                    
                    // Consume floating advance payments to mark future unpaid rents as paid
                    if (!$isPaid && $advancePaymentCount > 0) {
                        $isPaid = true;
                        $advancePaymentCount--;
                    }

                    $recurringCharges[] = [
                        'id'          => $rentId,
                        'date'        => $currentDate->format('Y-m-d'),
                        'type'        => 'Monthly Rent',
                        'description' => "Monthly Rent — $monthName",
                        'amount'      => (float)$lease['monthly_rent'],
                        'status'      => $isPaid ? 'Paid' : 'Unpaid'
                    ];

                    // Water Bill
                    $waterId = 'water-' . $monthKey;
                    $isWaterPaid = in_array(strtolower($waterId), $paidRecurringKeys);
                    if (!$isWaterPaid && $advanceWaterCount > 0) {
                        $isWaterPaid = true;
                        $advanceWaterCount--;
                    }
                    
                    $recurringCharges[] = [
                        'id'          => $waterId,
                        'date'        => $currentDate->format('Y-m-d'),
                        'type'        => 'Water Bill',
                        'description' => "Water Consumption ($occupants occupants) — $monthName",
                        'amount'      => (float)($occupants * 100),
                        'status'      => $isWaterPaid ? 'Paid' : 'Unpaid'
                    ];
                    // Contribution
                    $contribId = 'contribution-' . $monthKey;
                    $isContribPaid = in_array(strtolower($contribId), $paidRecurringKeys);
                    if (!$isContribPaid && $advanceContributionCount > 0) {
                        $isContribPaid = true;
                        $advanceContributionCount--;
                    }
                    
                    $recurringCharges[] = [
                        'id'          => $contribId,
                        'date'        => $currentDate->format('Y-m-d'),
                        'type'        => 'Contribution Fee',
                        'description' => "Contribution (Security/Garbage) — $monthName",
                        'amount'      => 150.00,
                        'status'      => $isContribPaid ? 'Paid' : 'Unpaid'
                    ];
                }

                $currentDate->modify('+1 month');
                $monthCount++;
                if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
            }

            // Recurring Parking Fees
            foreach ($parkingApps as $pa) {
                $parkStart = new \DateTime($pa['datestarted'] ?: $pa['date']);
                $parkStart->modify('first day of this month'); // Align to month start 
                
                $pDate = clone $parkStart;
                while ($pDate <= $limitDate) {
                    $pMonthKey = $pDate->format('Y-m');
                    $pMonthName = $pDate->format('F Y');
                    $parkId = 'parking-' . $pa['parking_id'] . '-' . $pMonthKey;
                    
                    $isParkPaid = in_array(strtolower($parkId), $paidRecurringKeys);
                    if (!$isParkPaid && $advanceParkingCount > 0) {
                        $isParkPaid = true;
                        $advanceParkingCount--;
                    }

                    $recurringCharges[] = [
                        'id'          => $parkId,
                        'date'        => $pDate->format('Y-m-d'),
                        'type'        => 'Parking Fee',
                        'description' => 'Parking Fee — ' . ($pa['vehiclename'] ?: 'Vehicle') . ' (' . ($pa['plateno'] ?: 'N/A') . ") — $pMonthName",
                        'amount'      => 1000.00,
                        'status'      => $isParkPaid ? 'Paid' : 'Unpaid'
                    ];
                    
                    $pDate->modify('+1 month');
                    if ($pDate > $limitDate && $pDate->format('mY') === $limitDate->format('mY')) break;
                }
            }

            // Sort by date
            usort($recurringCharges, function($a, $b) {
                return strtotime($a['date']) <=> strtotime($b['date']);
            });
        }

        $this->view('user/Apartment/tenant_payment', [
            'lease'            => $lease,
            'payments'         => $payments,
            'recurringCharges' => $recurringCharges,
            'occupants'        => $occupants ?? 1,
            'parkingApps'      => $parkingApps ?? []
        ]);
    }

    /**
     * Submit single or bulk payment (Initial or Recurring).
     */
    public function submitPayment() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $paymentIds = $body['payment_id'] ?? []; // Now accepts array or single string/int
        if (!is_array($paymentIds)) {
            $paymentIds = [$paymentIds]; // Normalize to array
        }
        
        $refNo = $body['reference'] ?? '';
        $userId = $_SESSION['user_id'];

        if (empty($paymentIds) || empty($paymentIds[0])) {
            echo json_encode(['success' => false, 'message' => 'No payment items selected']);
            return;
        }

        require_once BASE_PATH . '/app/models/Payment.php';
        require_once BASE_PATH . '/app/models/Lease.php';
        
        $paymentModel = new Payment();
        $lease = (new Lease())->getLeaseByTenantId($userId);
        
        if (!$lease) {
            echo json_encode(['success' => false, 'message' => 'Active lease not found.']);
            return;
        }

        $db = getDbConnection();
        $allOk = true;

        foreach ($paymentIds as $pid) {
            // CASE 1: Recurring Charge (String ID like 'rent-2026-05')
            if (is_string($pid) && strpos($pid, '-') !== false) {
                $parts = explode('-', $pid);
                $type = ucfirst($parts[0]); // Rent, Water, Parking
                $amount = 0;
                
                // Determine amount based on type
                if ($type === 'Rent') {
                    $amount = (float)$lease['monthly_rent'];
                } elseif ($type === 'Water') {
                    $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
                    $stmt->execute([$userId]);
                    $occupants = (int)$stmt->fetchColumn() + 1;
                    $amount = (float)($occupants * 100);
                } elseif ($type === 'Parking') {
                    $amount = 1000.00;
                } elseif ($type === 'Contribution') {
                    $amount = 150.00;
                }
                
                $exactType = ucfirst(strtolower($pid)); 

                // Create record
                $stmt = $db->prepare("INSERT INTO payments (lease_id, tenant_id, amount, payment_type, reference_number, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, 'Paid', NOW())");
                $ok = $stmt->execute([$lease['lease_id'], $userId, $amount, $exactType, $refNo]);
                if (!$ok) $allOk = false;
            } 
            // CASE 2: Initial Payment (Numeric ID)
            else {
                $ok = $paymentModel->markAsPaid((int) $pid, $refNo);
                if (!$ok) $allOk = false;
            }
        }

        if ($allOk) {
            require_once BASE_PATH . '/app/models/Notification.php';
            $notifModel = new Notification();
            
            $items = array_map(function($pid) {
                if (is_string($pid) && strpos($pid, '-') !== false) {
                    $parts = explode('-', $pid);
                    return implode(" ", array_map('ucfirst', $parts));
                }
                return "Initial Payment";
            }, $paymentIds);
            
            // Limit summary string if bulk paying many things
            $itemsText = count($items) > 3 ? $items[0] . ' + ' . (count($items)-1) . ' other items' : implode(', ', $items);
            
            $notifModel->create(
                $userId,
                'Payment Received',
                'Your payment for ' . $itemsText . ' has been recorded (Ref: ' . htmlspecialchars($refNo, ENT_QUOTES) . '). Thank you for settling your dues!',
                'payment'
            );

            // ── Admin Notification ──
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            $adminNotif->create(
                'Payment Received',
                $tenantName . ' submitted a payment for: ' . $itemsText . ' (Ref: ' . htmlspecialchars($refNo, ENT_QUOTES) . ').',
                'payment',
                $tenantName,
                $userId,
                '/admin/mis_admin/billing'
            );

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Some payments could not be processed.']);
        }
    }

    /**
     * Request Contract Renewal (Tenant Action)
     */
    public function requestRenewal() {
        Auth::protectRole(['Tenant']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $leaseId = $body['lease_id'] ?? 0;
        $term = (int) ($body['term'] ?? 12);
        $userId = $_SESSION['user_id'];

        if (!$leaseId) {
            echo json_encode(['success' => false, 'message' => 'Invalid lease ID']);
            return;
        }

        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        $renewalModel = new LeaseRenewal();

        $ok = $renewalModel->requestRenewal((int) $leaseId, $userId, $term);

        if ($ok) {
            // Notify admin about the renewal request
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            $adminNotif->create(
                'Contract Renewal Requested',
                $tenantName . ' has requested a lease renewal for ' . $term . ' months.',
                'request',
                $tenantName,
                $userId,
                '/admin/apartment/renewals'
            );

            // Notify the tenant
            require_once BASE_PATH . '/app/models/Notification.php';
            $tenantNotif = new Notification();
            $tenantNotif->create(
                $userId,
                'Renewal Request Submitted',
                'Your request to renew your contract for ' . $term . ' months has been submitted to the admin for review.',
                'system'
            );
        }

        echo json_encode(['success' => $ok]);
    }

    /**
     * Official Statement of Account (Tenant View)
     */
    public function soa() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];
        $db = getDbConnection();

        require_once BASE_PATH . '/app/models/Lease.php';
        $leaseModel = new Lease();
        $lease = $leaseModel->getLeaseByTenantId($userId);

        if (!$lease) {
            $this->view('user/Apartment/tenant_soa', [
                'lease' => null, 
                'transactions' => [],
                'filterMonth' => 'all',
                'availableMonths' => []
            ]);
            return;
        }

        // 1. Fetch needed data for billing engine
        // Family members (for water)
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$userId]);
        $memberCount = (int)$stmt->fetchColumn();
        $occupants = $memberCount + 1;

        // Parking
        $stmt = $db->prepare("SELECT * FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$userId]);
        $parkingApps = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Payments
        $stmt = $db->prepare("SELECT p.* FROM payments p JOIN leases l ON p.lease_id = l.lease_id WHERE l.tenant_id = ?");
        $stmt->execute([$userId]);
        $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Manual Billing Records
        $stmt = $db->prepare("SELECT * FROM billing WHERE tenant_id = ?");
        $stmt->execute([$userId]);
        $billingRecords = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // Count Advance Payments for Simulation
        $advancePaymentCount = 0;
        foreach ($payments as $p) {
            if ($p['payment_status'] === 'Paid' && strtolower($p['payment_type']) === 'rent-advance') {
                $advancePaymentCount++;
            }
        }

        // 2. Billing Engine (Reuse same logic as Admin SOA)
        $transactions = [];
        $simulationMonths = $advancePaymentCount; // Extend SOA time horizon based on advance payments
        $now = clone (new \DateTime());
        if ($simulationMonths > 0) $now->modify("+$simulationMonths month"); 
        $leaseStart = new \DateTime($lease['start_date']);
        $leaseEnd = $lease['end_date'] ? (new \DateTime($lease['end_date'])) : null;
        $limitDate = ($leaseEnd && $leaseEnd < $now) ? $leaseEnd : $now;

        // A) Recurring Timeline
        $currentDate = clone $leaseStart;
        $monthCount = 0;
        while ($currentDate <= $limitDate) {
            $monthName = $currentDate->format('F Y');
            $monthKey = $currentDate->format('my');

            if ($monthCount > 0) {
                // Rent
                $transactions[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'type' => 'Monthly Rent',
                    'description' => "Monthly Rent Usage — $monthName",
                    'ref' => 'LSE-R' . str_pad($lease['lease_id'], 3, '0', STR_PAD_LEFT) . '-' . $monthKey,
                    'charge' => (float)$lease['monthly_rent'],
                    'payment' => 0
                ];
                // Water
                $transactions[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'type' => 'Water',
                    'description' => "Water Consumption ($occupants occupants) — $monthName",
                    'ref' => 'LSE-W' . str_pad($lease['lease_id'], 3, '0', STR_PAD_LEFT) . '-' . $monthKey,
                    'charge' => (float)($occupants * 100),
                    'payment' => 0
                ];
                // Contribution
                $transactions[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'type' => 'Contribution',
                    'description' => "Monthly Contribution (Security/Garbage) — $monthName",
                    'ref' => 'LSE-C' . str_pad($lease['lease_id'], 3, '0', STR_PAD_LEFT) . '-' . $monthKey,
                    'charge' => 150.00,
                    'payment' => 0
                ];
            }
            $currentDate->modify('+1 month');
            $monthCount++;
            if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
        }

        // B) Process Payments (Initial Charges & Global Payments)
        foreach ($payments as $p) {
            $isInitial = in_array($p['payment_type'], ['Deposit', 'Advance']);

            // 1. Only add a CHARGE row if it's an initial payment
            if ($isInitial) {
                $transactions[] = [
                    'date' => date('Y-m-d', strtotime($p['created_at'])),
                    'type' => $p['payment_type'],
                    'description' => $p['payment_type'] === 'Deposit' ? 'Security Deposit (Initial)' : 'Advance Rent (Month 1)',
                    'ref' => 'PMT-' . str_pad($p['payment_id'], 4, '0', STR_PAD_LEFT),
                    'charge' => (float)$p['amount'],
                    'payment' => 0
                ];
            }

            // 2. Add a PAYMENT row for ANY paid transaction (Initial or Recurring)
            if ($p['payment_status'] === 'Paid') {
                $displayName = $isInitial ? $p['payment_type'] : str_replace('-', ' ', $p['payment_type']);
                $transactions[] = [
                    'date' => $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : date('Y-m-d', strtotime($p['created_at'])),
                    'type' => 'Payment',
                    'description' => "Payment Received — " . ucwords($displayName),
                    'ref' => $p['reference_number'] ?: 'REF-PAY',
                    'charge' => 0,
                    'payment' => (float)$p['amount']
                ];
            }
        }

        // C) Recurring Parking
        foreach ($parkingApps as $pa) {
            $parkStart = new \DateTime($pa['datestarted'] ?: $pa['date']);
            $parkStart->modify('first day of this month');
            $pDate = clone $parkStart;

            while ($pDate <= $limitDate) {
                $pMonthName = $pDate->format('F Y');
                $transactions[] = [
                    'date' => $pDate->format('Y-m-d'),
                    'type' => 'Parking Fee',
                    'description' => 'Parking Fee — ' . ($pa['vehiclename'] ?: 'Vehicle') . ' — ' . $pMonthName,
                    'charge' => 1000.00,
                    'payment' => 0,
                    'ref' => 'PKG-' . str_pad($pa['parking_id'], 4, '0', STR_PAD_LEFT) . '-' . $pDate->format('my')
                ];

                $pDate->modify('+1 month');
                if ($pDate > $limitDate && $pDate->format('mY') === $limitDate->format('mY')) break;
            }
        }

        // D) Manual Invoices
        foreach ($billingRecords as $b) {
            $transactions[] = [
                'date' => $b['due_date'],
                'type' => 'Invoice',
                'description' => 'Monthly Rent Invoice',
                'charge' => (float)$b['amount'],
                'payment' => 0,
                'ref' => 'INV-' . str_pad($b['billing_id'], 4, '0', STR_PAD_LEFT)
            ];
            if ($b['status'] === 'Paid') {
                $transactions[] = [
                    'date' => $b['due_date'],
                    'type' => 'Payment',
                    'description' => 'Payment Received — Rent',
                    'charge' => 0,
                    'payment' => (float)$b['amount'],
                    'ref' => 'PAY-INV-' . $b['billing_id']
                ];
            }
        }

        // Sort by date
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        // Filter by month logically to maintain accurate Running Balance
        $filterMonth = $_GET['month'] ?? 'all';
        $balanceForwarded = 0;
        $filteredTransactions = [];
        $availableMonths = [];

        foreach ($transactions as $t) {
            $tMonth = substr($t['date'], 0, 7); // '2026-06'
            if (!in_array($tMonth, $availableMonths)) {
                $availableMonths[] = $tMonth;
            }

            if ($filterMonth !== 'all') {
                if ($tMonth < $filterMonth) {
                    $balanceForwarded += ($t['charge'] - $t['payment']);
                } elseif ($tMonth === $filterMonth) {
                    $filteredTransactions[] = $t;
                }
            }
        }

        if ($filterMonth === 'all') {
            $filteredTransactions = $transactions;
        }

        // Sort available months descending for the dropdown
        rsort($availableMonths);

        $this->view('user/Apartment/tenant_soa', [
            'lease' => $lease,
            'transactions' => $filteredTransactions,
            'balanceForwarded' => $balanceForwarded,
            'filterMonth' => $filterMonth,
            'availableMonths' => $availableMonths,
            'occupants' => $occupants
        ]);
    }
}
