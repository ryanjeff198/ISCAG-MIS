<?php
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/helpers/AuditLogger.php';
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
        
        // Fetch billing-related info
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$userId]);
        $familyCount = (int)$stmt->fetchColumn();
        
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$userId]);
        $hasParking = ($stmt->fetchColumn() > 0);

        $this->view('user/Apartment/apartment_information', [
            'application' => $application,
            'tenantInfo' => $tenantInfo,
            'uploadedDocs' => $uploadedDocs,
            'familyCount' => $familyCount,
            'hasParking' => $hasParking
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

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

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
            $vc = count($body['vehicles']);
            AuditLogger::log('PARKING', 'SUBMIT_PARKING', "Submitted parking application for $vc vehicle(s)");
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
            AuditLogger::log('APARTMENT', 'SUBMIT_APP', "Finalized and submitted apartment application");
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
        require_once BASE_PATH . '/app/models/MoveOut.php';

        $leaseModel = new Lease();
        $paymentModel = new Payment();
        $moveOutModel = new MoveOut();

        $lease = $leaseModel->getLeaseByTenantId($userId);
        
        // Fetch Move-Out Request to check for settlement
        // (Assuming we might need a method to get specific request for a tenant)
        // Let's find any processing move-out request for this tenant
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT * FROM move_out_requests WHERE tenant_id = ? AND status IN ('Processing', 'Completed') ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $moveout = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        
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
            $now = clone (new \DateTime('now'));
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
                if ($p['payment_status'] === 'Paid') {
                    $typeLower = strtolower($p['payment_type']);
                    if (strpos($typeLower, '-') !== false) {
                        $paidRecurringKeys[] = $typeLower;
                    }
                    if ($typeLower === 'rent-advance' || $typeLower === 'advance') $advancePaymentCount++;
                    if ($typeLower === 'water-advance') $advanceWaterCount++;
                    if ($typeLower === 'parking-advance') $advanceParkingCount++;
                    if ($typeLower === 'contribution-advance') $advanceContributionCount++;
                }
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

                // 1. Rent Logic
                $rentId = 'rent-' . $monthKey;
                $isPaid = in_array(strtolower($rentId), $paidRecurringKeys);
                if (!$isPaid && $advancePaymentCount > 0) {
                    $isPaid = true;
                    $advancePaymentCount--;
                }

                // 2. Water Logic
                $waterId = 'water-' . $monthKey;
                $isWaterPaid = in_array(strtolower($waterId), $paidRecurringKeys);
                if (!$isWaterPaid && $advanceWaterCount > 0) {
                    $isWaterPaid = true;
                    $advanceWaterCount--;
                }

                // 3. Contribution Logic
                $contribId = 'contribution-' . $monthKey;
                $isContribPaid = in_array(strtolower($contribId), $paidRecurringKeys);
                if (!$isContribPaid && $advanceContributionCount > 0) {
                    $isContribPaid = true;
                    $advanceContributionCount--;
                }

                // UI Display: Only show in recurring charges list if it's Month 1+
                if ($monthCount > 0) {
                    $recurringCharges[] = [
                        'id'          => $rentId,
                        'date'        => $currentDate->format('Y-m-d'),
                        'type'        => 'Monthly Rent',
                        'description' => "Monthly Rent — $monthName",
                        'amount'      => (float)$lease['monthly_rent'],
                        'status'      => $isPaid ? 'Paid' : 'Unpaid'
                    ];

                    $recurringCharges[] = [
                        'id'          => $waterId,
                        'date'        => $currentDate->format('Y-m-d'),
                        'type'        => 'Water Bill',
                        'description' => "Water Consumption ($occupants occupants) — $monthName",
                        'amount'      => (float)($occupants * 100),
                        'status'      => $isWaterPaid ? 'Paid' : 'Unpaid'
                    ];

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
            'parkingApps'      => $parkingApps ?? [],
            'moveout'          => $moveout
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
            // CASE 1: Recurring or Settlement Charge (String ID like 'rent-2026-05' or 'settlement-damage')
            if (is_string($pid) && strpos($pid, '-') !== false) {
                $parts = explode('-', $pid);
                $type = ucfirst($parts[0]); 
                $amount = 0;
                $exactType = ucfirst(strtolower($pid));

                // Move-Out Settlement handling
                if (strtolower($type) === 'settlement') {
                    $stmt = $db->prepare("SELECT * FROM move_out_requests WHERE tenant_id = ? AND status = 'Processing' LIMIT 1");
                    $stmt->execute([$userId]);
                    $mo = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($mo) {
                        $field = ($parts[1] === 'damage') ? 'damage_costs' : 'utility_deductions';
                        $amount = (float)$mo[$field];
                        $exactType = 'Move-Out ' . ucfirst($parts[1]);
                    }
                }
                // Regular Recurring charges
                elseif ($type === 'Rent') {
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
            AuditLogger::log('BILLING', 'SUBMIT_PAYMENT', "Submitted payment for " . count($paymentIds) . " item(s). Ref: $refNo");
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
            AuditLogger::log('RENEWAL', 'SUBMIT_RENEWAL', "Requested lease renewal for $term months (Lease ID: $leaseId)");
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
     * Submit Move-Out Request (Tenant Action)
     */
    public function submitMoveout() {
        Auth::protectRole(['Tenant']);
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'];
        $body = json_decode(file_get_contents('php://input'), true);
        $unitId = $body['unit_id'] ?? 0;

        if (!$unitId) {
            echo json_encode(['success' => false, 'message' => 'Unit context missing']);
            return;
        }

        require_once BASE_PATH . '/app/models/MoveOut.php';
        $model = new MoveOut();

        // Calculate target move-out date (30 days notice)
        $moveOutDate = date('Y-m-d', strtotime('+30 days'));

        if ($model->createRequest($userId, (int)$unitId, $moveOutDate)) {
            // Log it
            AuditLogger::log('APARTMENT', 'REQUEST_MOVEOUT', "Tenant requested to vacate unit ID: $unitId");

            // Notify Admin
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            $adminNotif->create(
                'Move-Out Request Received',
                $tenantName . ' has submitted a formal notice to vacate their unit.',
                'request',
                $tenantName,
                $userId,
                '/admin/mis_admin/moveout_requests'
            );

            // Notify Tenant
            require_once BASE_PATH . '/app/models/Notification.php';
            $tenantNotif = new Notification();
            $tenantNotif->create(
                $userId,
                'Move-Out Request Sent',
                'Your notice to vacate has been received. Our team will contact you for inspection and final payment clearance.',
                'system'
            );

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'A move-out request is already pending for your account.']);
        }
    }

    /**
     * Official Statement of Account (Tenant View)
     */
    public function soa() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'];
        $db = getDbConnection();

        require_once BASE_PATH . '/app/models/Lease.php';
        require_once BASE_PATH . '/app/models/Payment.php';
        $leaseModel = new Lease();
        $paymentModel = new Payment();
        $lease = $leaseModel->getLeaseByTenantId($userId);

        if (!$lease) {
            $this->view('user/Apartment/tenant_soa', [
                'lease' => null, 
                'transactions' => [],
                'balanceForwarded' => 0,
                'filterMonth' => 'all',
                'availableMonths' => [],
                'occupants' => 1
            ]);
            return;
        }

        // --- FETCH DATA FOR SIMULATION ---
        $payments = $paymentModel->getPaymentsByLease($lease['lease_id']);

        // Occupants (Tenant + Family)
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$userId]);
        $resCount = $stmt->fetch(PDO::FETCH_ASSOC);
        $occupants = (int)($resCount['cnt'] ?? 0) + 1;

        // Parking
        $stmt = $db->prepare("SELECT parking_id, vehiclename, plateno, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$userId]);
        $parkingApps = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Manual Billing
        $stmt = $db->prepare("SELECT * FROM billing WHERE tenant_id = ? ORDER BY due_date ASC");
        $stmt->execute([$userId]);
        $billingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 1. Categorize Advances & Consumable Payments
        $advanceQueues = [];
        $consumedPaymentIds = [];

        foreach ($payments as $p) {
            if ($p['payment_status'] === 'Paid') {
                $lowType = strtolower($p['payment_type']);
                // Normalize: bare 'advance' is really 'rent-advance'
                if ($lowType === 'advance') $lowType = 'rent-advance';
                if (strpos($lowType, 'advance') !== false || $lowType === 'deposit') {
                    if (!isset($advanceQueues[$lowType])) $advanceQueues[$lowType] = [];
                    $advanceQueues[$lowType][] = $p;
                }
            }
        }

        $transactions = [];
        $totalAdvances = isset($advanceQueues['rent-advance']) ? count($advanceQueues['rent-advance']) : 0;

        $now = clone (new \DateTime('now'));
        
        $leaseStart = new \DateTime($lease['start_date']);
        $leaseEnd = $lease['end_date'] ? (new \DateTime($lease['end_date'])) : null;
        $limitDate = ($leaseEnd && $leaseEnd < $now) ? $leaseEnd : $now;

        // 2. Simulated Timeline (Monthly Cycles)
        $currentDate = clone $leaseStart;
        $monthCount = 0;
        while ($currentDate <= $limitDate) {
            $monthName = $currentDate->format('F Y');
            $simDate = $currentDate->format('Y-m-d');
            $tid = $userId;

            // Helper to apply advance if available
            $applyAdvance = function($type, $desc, $amt) use (&$advanceQueues, $tid, &$transactions, $simDate, &$consumedPaymentIds) {
                if (!empty($advanceQueues[$type])) {
                    $p = array_shift($advanceQueues[$type]);
                    $consumedPaymentIds[] = $p['payment_id'];
                    $transactions[] = [
                        'date'       => $simDate,
                        'type'       => 'Payment',
                        'description'=> "Payment Applied from Advance — $desc",
                        'ref'        => $p['reference_number'] ?: 'ADV-' . str_pad($p['payment_id'], 4, '0', STR_PAD_LEFT),
                        'charge'     => 0,
                        'payment'    => (float)$amt
                    ];
                    return true;
                }
                return false;
            };

            // Initial Payments (Month 0)
            if ($monthCount === 0) {
                if ($lease['deposit_amount'] > 0) {
                    $transactions[] = [
                        'date' => $simDate, 'type' => 'Deposit', 'description' => 'Security Deposit',
                        'ref' => 'LSE-DEP-'.$lease['lease_id'], 'charge' => (float)$lease['deposit_amount'], 'payment' => 0
                    ];
                    $applyAdvance('deposit', 'Security Deposit', (float)$lease['deposit_amount']);
                }
                if ($lease['advance_amount'] > 0) {
                    $transactions[] = [
                        'date' => $simDate, 'type' => 'Advance Rent', 'description' => 'Advance Rent (Month 1)',
                        'ref' => 'LSE-ADV-'.$lease['lease_id'], 'charge' => (float)$lease['advance_amount'], 'payment' => 0
                    ];
                    $applyAdvance('rent-advance', "Rent for $monthName", (float)$lease['advance_amount']);
                }
            }

            // Recurring Cycles (Month 1+)
            if ($monthCount > 0) {
                // Rent
                $rentAmt = (float)$lease['monthly_rent'];
                $transactions[] = [
                    'date' => $simDate, 'type' => 'Monthly Rent', 'description' => "Monthly Rent — $monthName",
                    'charge' => $rentAmt, 'payment' => 0, 'ref' => 'LSE-R' . $lease['lease_id'] . '-' . $currentDate->format('my')
                ];
                $applyAdvance('rent-advance', "Rent for $monthName", $rentAmt);
            }

            // Water (All months including Month 0)
            $waterAmt = (float)($occupants * 100);
            $transactions[] = [
                'date' => $simDate, 'type' => 'Water', 'description' => "Water Consumption ($occupants occupants) — $monthName",
                'charge' => $waterAmt, 'payment' => 0, 'ref' => 'LSE-W' . $lease['lease_id'] . '-' . $currentDate->format('my')
            ];
            $applyAdvance('water-advance', "Water for $monthName", $waterAmt);

            // Contribution (All months including Month 0)
            $transactions[] = [
                'date' => $simDate, 'type' => 'Contribution', 'description' => "Monthly Contribution (Security & Garbage) — $monthName",
                'charge' => 150.00, 'payment' => 0, 'ref' => 'LSE-C' . $lease['lease_id'] . '-' . $currentDate->format('my')
            ];
            $applyAdvance('contribution-advance', "Contribution for $monthName", 150.00);

            // Parking
            foreach ($parkingApps as $pa) {
                $parkStartStr = $pa['datestarted'] ?: $pa['date'];
                if ($simDate >= date('Y-m-d', strtotime($parkStartStr))) {
                    $transactions[] = [
                        'date' => $simDate, 'type' => 'Parking Fee', 'description' => 'Parking Fee — ' . ($pa['vehiclename'] ?: 'Vehicle'),
                        'charge' => 1000.00, 'payment' => 0, 'ref' => 'PKG-' . $pa['parking_id'] . '-' . $currentDate->format('my')
                    ];
                    $applyAdvance('parking-advance', "Parking for $monthName", 1000.00);
                }
            }

            $currentDate->modify('+1 month');
            $monthCount++;
            if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
        }

        // 3. Process Non-Consumed Payments (Direct payments made after move-in)
        foreach ($payments as $p) {
            if (in_array($p['payment_id'], $consumedPaymentIds)) continue;

            $isPaid = $p['payment_status'] === 'Paid';
            $chargeDate = date('Y-m-d', strtotime($p['created_at']));
            $payDate = $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $chargeDate;

            // Optional Charge row for the payment if it is a manual payment entry?
            // Usually, these are already in 'transactions' via cycle billing or billingRecords.
            // But if it's a floating payment without a matched charge, we show it.
            if ($p['payment_status'] === 'Paid') {
                $displayName = str_replace('-', ' ', $p['payment_type']);
                $transactions[] = [
                    'date' => $payDate,
                    'type' => 'Payment',
                    'description' => "Payment Received — " . ucwords($displayName),
                    'ref' => $p['reference_number'] ?: 'REF-PAY-'.$p['payment_id'],
                    'charge' => 0,
                    'payment' => (float)$p['amount']
                ];
            }
        }

        // D) Manual Billing Records
        foreach ($billingRecords as $b) {
            $transactions[] = [
                'date' => $b['due_date'], 'type' => 'Invoice', 'description' => 'Monthly Rent Invoice',
                'charge' => (float)$b['amount'], 'payment' => 0, 'ref' => 'INV-' . str_pad($b['billing_id'], 4, '0', STR_PAD_LEFT)
            ];
            if ($b['status'] === 'Paid') {
                $transactions[] = [
                    'date' => $b['due_date'], 'type' => 'Payment', 'description' => 'Payment Received — Rent',
                    'charge' => 0, 'payment' => (float)$b['amount'], 'ref' => 'PAY-INV-' . $b['billing_id']
                ];
            }
        }

        // Sort transactions chronologically
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        // 1. Gather all available months first
        $availableMonths = [];
        foreach ($transactions as $t) {
            $tMonth = substr($t['date'], 0, 7);
            if (!in_array($tMonth, $availableMonths)) $availableMonths[] = $tMonth;
        }
        sort($availableMonths);

        // 2. Determine selected filter month (default to first month instead of 'all')
        $firstMonth = !empty($availableMonths) ? $availableMonths[0] : 'all';
        $filterMonth = $_GET['month'] ?? $firstMonth;

        // 3. Apply the filter and calculate balance forwarded
        $balanceForwarded = 0;
        $filteredTransactions = [];
        
        foreach ($transactions as $t) {
            $tMonth = substr($t['date'], 0, 7);
            
            if ($filterMonth !== 'all') {
                if ($tMonth < $filterMonth) {
                    $balanceForwarded += ($t['charge'] - $t['payment']);
                } elseif ($tMonth === $filterMonth) {
                    $filteredTransactions[] = $t;
                }
            } else {
                $filteredTransactions[] = $t;
            }
        }

        $this->view('user/Apartment/tenant_soa', [
            'lease' => $lease,
            'transactions' => $filteredTransactions,
            'balanceForwarded' => $balanceForwarded,
            'filterMonth' => $filterMonth,
            'availableMonths' => $availableMonths,
            'occupants' => $occupants
        ]);
    }

    public function submitMaintenance() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        
        $category = $_POST['category'] ?? '';
        $description = $_POST['description'] ?? '';
        $attachment = null;

        if (empty($category) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Category and description are required']);
            return;
        }

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = "maintenance_{$userId}_" . time() . "_" . uniqid() . "." . $ext;
            $relPath = "uploads/maintenance/" . $fileName;
            $fullPath = BASE_PATH . "/public/" . $relPath;

            if (!is_dir(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                $attachment = $relPath;
            }
        }

        require_once BASE_PATH . '/app/models/Maintenance.php';
        $model = new Maintenance();
        
        $data = [
            'category' => $category,
            'description' => $description,
            'attachment' => $attachment
        ];

        if ($model->create($userId, $data)) {
            AuditLogger::log('MAINTENANCE', 'SUBMIT_MAINTENANCE', "Submitted $category maintenance request");
            // Notify Admin
            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $adminNotif = new AdminNotification();
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            $adminNotif->create(
                'Maintenance Request',
                "$tenantName submitted a $category maintenance request.",
                'request',
                $tenantName,
                $userId,
                '/admin/mis_admin/maintenance'
            );

            // Notify Tenant
            require_once BASE_PATH . '/app/models/Notification.php';
            $notif = new Notification();
            $notif->create($userId, 'Maintenance Request Received', "Your request for $category maintenance has been received and is waiting for review.", 'info');

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit request']);
        }
    }
}
