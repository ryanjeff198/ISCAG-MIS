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
                null
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
        $moveout = $moveOutModel->getSettlementForTenant($userId, ['Processing', 'Completed']);
        
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
            $now = TimeSim::now();
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
            $maxAdv = max($advancePaymentCount, $advanceWaterCount, $advanceContributionCount, $advanceParkingCount);
            
            // Advance packages paid for Month 1 (Initial) are consumed in Month 0,
            // so each additional advance package covers 1 subsequent calendar month.
            $effectiveAdv = max(0, $maxAdv - 1);
            $limitDate = ($leaseEnd && $leaseEnd < $now) ? clone $leaseEnd : clone $now;
            if ($effectiveAdv > 0) {
                $limitDate->modify("+{$effectiveAdv} month");
            }

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
                $parkStart->modify('+1 month'); // Start recurring billing 1 month after application
                
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

        // Validate advance payments don't exceed lease end date
        $isAdvance = false;
        foreach ($paymentIds as $pid) {
            if (is_string($pid) && in_array(strtolower($pid), ['rent-advance', 'water-advance', 'contribution-advance', 'parking-advance'])) {
                $isAdvance = true;
                break;
            }
        }
        if ($isAdvance && !empty($lease['end_date']) && !empty($lease['start_date'])) {
            $leaseEnd = new \DateTime($lease['end_date']);
            $existingPayments = $paymentModel->getPaymentsByLease($lease['lease_id']);
            
            $latestCovered = new \DateTime($lease['start_date']);
            $advCount = 0;
            foreach ($existingPayments as $ep) {
                if ($ep['payment_status'] !== 'Paid') continue;
                $tLow = strtolower($ep['payment_type']);
                if (preg_match('/^(?:rent|water|contribution|parking)-(\d{4}-\d{2})$/', $tLow, $matches)) {
                    $chargeDate = new \DateTime($matches[1] . '-01');
                    if ($chargeDate > $latestCovered) {
                        $latestCovered = $chargeDate;
                    }
                }
                if (in_array($tLow, ['rent-advance', 'advance'])) {
                    $advCount++;
                }
            }

            $extraAdvances = max(0, $advCount - 1);
            $finalCoveredDate = clone $latestCovered;
            if ($extraAdvances > 0) {
                $finalCoveredDate->modify("+{$extraAdvances} month");
            }
            $nextAdvMonth = clone $finalCoveredDate;
            $nextAdvMonth->modify("+1 month");

            if ($nextAdvMonth->format('Y-m') > $leaseEnd->format('Y-m')) {
                echo json_encode(['success' => false, 'message' => 'Advance payment exceeds your lease end date (' . $leaseEnd->format('M d, Y') . '). Please renew your contract first.']);
                return;
            }
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
                    require_once BASE_PATH . '/app/models/MoveOut.php';
                    $moveOutModel = new MoveOut();
                    $mo = $moveOutModel->getSettlementForTenant($userId, ['Processing']);
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
     * Create PayMongo Checkout Session for GCash / Maya / Card
     */
    public function createPayMongoCheckout() {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        $body = json_decode(file_get_contents('php://input'), true);
        $paymentIds = $body['payment_id'] ?? [];
        if (!is_array($paymentIds)) {
            $paymentIds = [$paymentIds];
        }

        $items = $body['items'] ?? []; // [{name: '...', amount: 5000}]
        $totalAmount = (float)($body['amount'] ?? 0);
        $userId = $_SESSION['user_id'];

        if (empty($paymentIds) || $totalAmount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid payment request or zero balance.']);
            return;
        }

        // Validate advance payments don't exceed lease end date
        $isAdvance = false;
        foreach ($paymentIds as $pid) {
            if (is_string($pid) && in_array(strtolower($pid), ['rent-advance', 'water-advance', 'contribution-advance', 'parking-advance'])) {
                $isAdvance = true;
                break;
            }
        }
        if ($isAdvance) {
            require_once BASE_PATH . '/app/models/Lease.php';
            require_once BASE_PATH . '/app/models/Payment.php';
            $lease = (new Lease())->getLeaseByTenantId($userId);
            if ($lease && !empty($lease['end_date']) && !empty($lease['start_date'])) {
                $leaseEnd = new \DateTime($lease['end_date']);
                $existingPayments = (new Payment())->getPaymentsByLease($lease['lease_id']);

                $latestCovered = new \DateTime($lease['start_date']);
                $advCount = 0;
                foreach ($existingPayments as $ep) {
                    if ($ep['payment_status'] !== 'Paid') continue;
                    $tLow = strtolower($ep['payment_type']);
                    if (preg_match('/^(?:rent|water|contribution|parking)-(\d{4}-\d{2})$/', $tLow, $matches)) {
                        $chargeDate = new \DateTime($matches[1] . '-01');
                        if ($chargeDate > $latestCovered) {
                            $latestCovered = $chargeDate;
                        }
                    }
                    if (in_array($tLow, ['rent-advance', 'advance'])) {
                        $advCount++;
                    }
                }

                $extraAdvances = max(0, $advCount - 1);
                $finalCoveredDate = clone $latestCovered;
                if ($extraAdvances > 0) {
                    $finalCoveredDate->modify("+{$extraAdvances} month");
                }
                $nextAdvMonth = clone $finalCoveredDate;
                $nextAdvMonth->modify("+1 month");

                if ($nextAdvMonth->format('Y-m') > $leaseEnd->format('Y-m')) {
                    echo json_encode(['success' => false, 'message' => 'Advance payment exceeds your lease end date (' . $leaseEnd->format('M d, Y') . '). Please renew your contract first.']);
                    return;
                }
            }
        }

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';

        // Prepare line items
        $lineItems = [];
        if (!empty($items)) {
            foreach ($items as $it) {
                $amt = (float)($it['amount'] ?? 0);
                if ($amt <= 0) continue;
                $lineItems[] = [
                    'name'     => $it['name'] ?? 'Fee',
                    'amount'   => $amt,
                    'quantity' => 1
                ];
            }
        }

        // Retrieve tenant account info for accurate PayMongo receipt delivery
        $db = getDbConnection();
        $uStmt = $db->prepare("SELECT email, first_name, last_name FROM tenant_accounts WHERE tenant_id = ?");
        $uStmt->execute([$userId]);
        $tAcct = $uStmt->fetch(\PDO::FETCH_ASSOC);

        $tenantEmail = !empty($tAcct['email']) ? trim($tAcct['email']) : ($_SESSION['email'] ?? '');
        $tenantName  = trim(($tAcct['first_name'] ?? '') . ' ' . ($tAcct['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Valued Tenant');

        // Generate official ISCAG payment reference number
        $refNo = 'ISCAG-PAY-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $itemCount = count($paymentIds);
        $description = "ISCAG Apartment Payment [Ref: {$refNo}] (" . $itemCount . ' item' . ($itemCount > 1 ? 's' : '') . ')';

        // Set session state for verification on return
        $_SESSION['paymongo_pending_payment'] = [
            'tenant_id'        => $userId,
            'payment_ids'      => $paymentIds,
            'amount'           => $totalAmount,
            'reference_number' => $refNo,
            'timestamp'        => time()
        ];

        // Construct absolute return URLs for external gateway
        $successUrl = full_url('/user/apartment/payment/paymongo-success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl  = full_url('/user/apartment/payment');

        $response = PayMongoHelper::createCheckoutSession(
            $totalAmount,
            $description,
            $lineItems,
            $successUrl,
            $cancelUrl,
            [
                'tenant_id'        => (string)$userId,
                'payment_ids'      => json_encode($paymentIds),
                'reference_number' => $refNo
            ],
            $refNo,
            [
                'name'  => $tenantName,
                'email' => $tenantEmail
            ]
        );

        if ($response['success'] && !empty($response['data']['attributes']['checkout_url'])) {
            echo json_encode([
                'success'      => true,
                'checkout_url' => $response['data']['attributes']['checkout_url']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $response['message'] ?? 'Could not create checkout session with PayMongo.'
            ]);
        }
    }

    /**
     * Handle return from PayMongo checkout
     */
    public function payMongoSuccess() {
        Auth::protectRole(['Guest', 'Tenant']);
        $sessionId = $_GET['session_id'] ?? '';
        $userId = $_SESSION['user_id'];

        // === DEBUG LOG ===
        $debugLog = BASE_PATH . '/scratch/paymongo_debug.log';
        $logEntry = "\n\n====== PAYMONGO SUCCESS @ " . date('Y-m-d H:i:s') . " ======\n";
        $logEntry .= "session_id (GET): $sessionId\n";
        $logEntry .= "user_id: $userId\n";
        $logEntry .= "PHP session_id: " . session_id() . "\n";

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';

        $pending = $_SESSION['paymongo_pending_payment'] ?? null;
        $logEntry .= "pending session data: " . json_encode($pending) . "\n";
        
        $paymentIds = [];
        $refNo = $pending['reference_number'] ?? ('ISCAG-PAY-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)));

        if (!empty($sessionId) && $sessionId !== '{CHECKOUT_SESSION_ID}') {
            $sessionData = PayMongoHelper::getCheckoutSession($sessionId);
            $logEntry .= "PayMongo API success: " . ($sessionData['success'] ? 'YES' : 'NO') . "\n";
            
            if ($sessionData['success']) {
                $attrs = $sessionData['data']['attributes'] ?? [];
                $logEntry .= "Checkout status: " . ($attrs['status'] ?? 'UNKNOWN') . "\n";
                $logEntry .= "Payments array: " . json_encode($attrs['payments'] ?? []) . "\n";
                $logEntry .= "Metadata: " . json_encode($attrs['metadata'] ?? []) . "\n";
                
                // Prioritize synchronized reference number from PayMongo checkout / metadata / session
                if (!empty($attrs['reference_number'])) {
                    $refNo = $attrs['reference_number'];
                } elseif (!empty($attrs['metadata']['reference_number'])) {
                    $refNo = $attrs['metadata']['reference_number'];
                } elseif (!empty($pending['reference_number'])) {
                    $refNo = $pending['reference_number'];
                } elseif (!empty($attrs['payments'][0]['id'])) {
                    $refNo = $attrs['payments'][0]['id'];
                }

                // If session state was lost during cross-origin redirect, restore payment IDs from metadata
                if (empty($pending) || (int)$pending['tenant_id'] !== (int)$userId) {
                    $logEntry .= "FALLBACK: Session lost, trying metadata recovery\n";
                    $meta = $attrs['metadata'] ?? [];
                    if (!empty($meta['tenant_id']) && (int)$meta['tenant_id'] === (int)$userId && !empty($meta['payment_ids'])) {
                        $decodedIds = json_decode($meta['payment_ids'], true);
                        if (is_array($decodedIds)) {
                            $paymentIds = $decodedIds;
                            $logEntry .= "RECOVERED payment_ids from metadata: " . json_encode($paymentIds) . "\n";
                        }
                    } else {
                        $logEntry .= "METADATA RECOVERY FAILED: tenant_id match=" . ((int)($meta['tenant_id'] ?? 0) === (int)$userId ? 'yes' : 'no') . ", payment_ids present=" . (!empty($meta['payment_ids']) ? 'yes' : 'no') . "\n";
                    }
                } else {
                    $logEntry .= "Session data INTACT, using pending session\n";
                }
            } else {
                $logEntry .= "PayMongo API ERROR: " . json_encode($sessionData) . "\n";
            }
        } else {
            $logEntry .= "WARNING: No session_id in GET params!\n";
        }

        if (empty($paymentIds) && !empty($pending['payment_ids'])) {
            $paymentIds = $pending['payment_ids'];
            $logEntry .= "Using session pending payment_ids: " . json_encode($paymentIds) . "\n";
        }

        unset($_SESSION['paymongo_pending_payment']);

        $logEntry .= "FINAL paymentIds: " . json_encode($paymentIds) . "\n";
        $logEntry .= "FINAL refNo: $refNo\n";

        if (empty($paymentIds)) {
            $logEntry .= "ABORT: paymentIds is empty, redirecting without processing!\n";
            file_put_contents($debugLog, $logEntry, FILE_APPEND);
            header('Location: ' . url('/user/apartment/payment'));
            exit;
        }

        require_once BASE_PATH . '/app/models/Payment.php';
        require_once BASE_PATH . '/app/models/Lease.php';
        
        $paymentModel = new Payment();
        $lease = (new Lease())->getLeaseByTenantId($userId);
        
        if ($lease) {
            $db = getDbConnection();
            $allOk = true;

            $paidItemsList = [];
            $totalPaidAmount = 0.0;

            foreach ($paymentIds as $pid) {
                if (is_string($pid) && strpos($pid, '-') !== false) {
                    $parts = explode('-', $pid);
                    $type = ucfirst($parts[0]); 
                    $amount = 0;
                    $exactType = ucfirst(strtolower($pid));
                    $displayName = $exactType;

                    if (strtolower($type) === 'settlement') {
                        require_once BASE_PATH . '/app/models/MoveOut.php';
                        $moveOutModel = new MoveOut();
                        $mo = $moveOutModel->getSettlementForTenant($userId, ['Processing']);
                        if ($mo) {
                            $field = ($parts[1] === 'damage') ? 'damage_costs' : 'utility_deductions';
                            $amount = (float)$mo[$field];
                            $exactType = 'Move-Out ' . ucfirst($parts[1]);
                            $displayName = $exactType;
                        }
                    } elseif ($type === 'Rent') {
                        $amount = (float)$lease['monthly_rent'];
                        $displayName = 'Monthly Rent (' . (isset($parts[1]) ? $parts[1] : 'Recurring') . ')';
                    } elseif ($type === 'Water') {
                        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
                        $stmt->execute([$userId]);
                        $occupants = (int)$stmt->fetchColumn() + 1;
                        $amount = (float)($occupants * 100);
                        $displayName = "Water Bill ({$occupants} occupants)";
                    } elseif ($type === 'Parking') {
                        $amount = 1000.00;
                        $displayName = 'Parking Fee';
                    } elseif ($type === 'Contribution') {
                        $amount = 150.00;
                        $displayName = 'Contribution (Security/Garbage)';
                    }
                    
                    $stmt = $db->prepare("INSERT INTO payments (lease_id, tenant_id, amount, payment_type, reference_number, payment_status, payment_date) VALUES (?, ?, ?, ?, ?, 'Paid', NOW())");
                    $stmt->execute([$lease['lease_id'], $userId, $amount, $exactType, $refNo]);
                    
                    $paidItemsList[] = ['name' => $displayName, 'amount' => $amount];
                    $totalPaidAmount += $amount;
                } else {
                    $pRow = $paymentModel->getPaymentById((int)$pid);
                    if ($pRow) {
                        $paidItemsList[] = [
                            'name' => ucfirst($pRow['payment_type'] ?? 'Initial Fee'),
                            'amount' => (float)($pRow['amount'] ?? 0)
                        ];
                        $totalPaidAmount += (float)($pRow['amount'] ?? 0);
                    }
                    $paymentModel->markAsPaid((int) $pid, $refNo);
                }
            }

            AuditLogger::log('BILLING', 'PAYMONGO_SUCCESS', "PayMongo payment verified for " . count($paymentIds) . " items. Ref: $refNo");
            
            require_once BASE_PATH . '/app/models/Notification.php';
            (new Notification())->create(
                $userId,
                'Online Payment Successful',
                'Your payment via PayMongo has been confirmed (Ref: ' . htmlspecialchars($refNo, ENT_QUOTES) . '). Thank you!',
                'payment'
            );

            require_once BASE_PATH . '/app/models/AdminNotification.php';
            $tenantName = $_SESSION['name'] ?? 'A tenant';
            (new AdminNotification())->create(
                'Online Payment Received',
                $tenantName . ' paid via PayMongo (Ref: ' . htmlspecialchars($refNo, ENT_QUOTES) . ').',
                'payment',
                $tenantName,
                $userId,
                '/admin/mis_admin/billing'
            );


        }

        header('Location: ' . url('/user/apartment/payment?paid=1&ref=' . urlencode($refNo)));
        exit;
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
                null
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
        $moveOutDate = TimeSim::date('Y-m-d', strtotime('+30 days'));

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
     * Fully synchronized with tenant_payment.php via unified SoaHelper.
     */
    public function soa() {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = (int)$_SESSION['user_id'];

        require_once BASE_PATH . '/app/helpers/SoaHelper.php';
        $soaData = SoaHelper::buildTransactions($userId);

        $lease               = $soaData['lease'];
        $occupants           = $soaData['occupants'];
        $allTransactions     = $soaData['transactions'];
        $availableMonths     = $soaData['availableMonths'];
        $advanceCreditsTotal = $soaData['advanceCreditsTotal'] ?? 0;
        $advanceCreditsCount = $soaData['advanceCreditsCount'] ?? 0;

        $balanceForwarded = 0;
        $filteredTransactions = [];

        if ($lease) {
            // Default to 'all' so tenant sees all billing history and records in full sync with tenant_payment.php
            $filterMonth = $_GET['month'] ?? 'all';

            foreach ($allTransactions as $t) {
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
        } else {
            $filterMonth = 'all';
        }

        $this->view('user/Apartment/tenant_soa', [
            'lease'               => $lease,
            'transactions'        => $filteredTransactions,
            'balanceForwarded'    => $balanceForwarded,
            'filterMonth'         => $filterMonth,
            'availableMonths'     => $availableMonths,
            'occupants'           => $occupants,
            'advanceCreditsTotal' => $advanceCreditsTotal,
            'advanceCreditsCount' => $advanceCreditsCount
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
