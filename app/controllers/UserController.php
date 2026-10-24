<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';
require_once BASE_PATH . '/app/helpers/AuditLogger.php';

class UserController extends Controller
{
    public function dashboard(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;
        
        $userModel = new User();
        $account = $userModel->findById($userId);
        
        // Synchronize session role with database role (Real-time approval updates)
        if ($account && isset($account['role']) && $account['role'] !== ($_SESSION['role'] ?? '')) {
            $_SESSION['role'] = $account['role'];
        }

        $info = $userModel->getAdditionalInfo($userId);
        
        $appModel = new ApartmentApp();
        $application = $appModel->getApplication($userId);

        // Fetch active service requests from Database
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $counselingModel = new CounselingRequest();
        $counselingRequests = $userId ? $counselingModel->getByUser((int)$userId) : [];

        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        $marriageModel = new MarriageRequest();
        $marriageRequests = $userId ? $marriageModel->getByUser((int)$userId) : [];

        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        $conversionModel = new ConversionRequest();
        $conversionRequests = $userId ? $conversionModel->getByUser((int)$userId) : [];

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $burialRequests = $userId ? $burialModel->getByTenantId((int)$userId) : [];

        // Run automated billing reminder check
        try {
            require_once BASE_PATH . '/app/helpers/BillingReminder.php';
            BillingReminder::checkAndNotify($userId);
        } catch (\Exception $e) { /* silently continue */ }
        
        $this->view('dashboard', [
            'account' => $account,
            'info' => $info,
            'application' => $application,
            'counselingRequests' => $counselingRequests,
            'marriageRequests' => $marriageRequests,
            'conversionRequests' => $conversionRequests,
            'burialRequests' => $burialRequests
        ]);
    }
    public function profile(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;
        
        $userModel = new User();
        $account = $userModel->findById($userId);
        $info = $userModel->getAdditionalInfo($userId);
        
        $this->view('user/tenant_account', [
            'account' => $account,
            'info' => $info
        ]);
    }

    public function notifications()
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        $userModel = new User();
        $account = $userModel->findById($userId);
        $info = $userModel->getAdditionalInfo($userId);

        // STRENGTHENED IDENTITY SYNC:
        // Force the session to reflect the database state for THIS user ID.
        // This prevents the "Setsuna/Admin" ghost bug where the sidebar is out of sync.
        if ($account) {
            $_SESSION['role'] = $account['role'];
            $_SESSION['name'] = $account['first_name'] . ' ' . $account['last_name'];
            $_SESSION['email'] = $account['email'];
        }

        require_once BASE_PATH . '/app/models/Notification.php';
        $notifModel = new Notification();
        $notifications = $notifModel->getUserNotifications($userId);

        $viewId = $_GET['view'] ?? null;
        if ($viewId) {
            $notifModel->markAsRead((int)$viewId, (int)$userId);
        }

        $this->view('user/tenant_notification', [
            'notifications' => $notifications,
            'account' => $account,
            'info' => $info,
            'viewId' => $viewId,
            'active_page' => 'notifications'
        ]);
    }

    public function markAllRead()
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        require_once BASE_PATH . '/app/models/Notification.php';
        $notifModel = new Notification();
        $success = $notifModel->markAllAsRead((int)$userId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }

    public function updateProfile(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        // Collect fields from modal.
        // As per requirements: Sex and Name are NOT changeable.
        $data = [
            'email'      => $_POST['email'] ?? null,
            'phone'      => $_POST['phone'] ?? null,
            'address'    => $_POST['address'] ?? null,
            'dob'        => $_POST['dob'] ?? null,
            'civil'      => $_POST['civil'] ?? null,
            'occupation' => $_POST['occupation'] ?? null,
            'arabicName' => $_POST['arabicName'] ?? null,
            'revertYear' => $_POST['revertYear'] ?? null,
        ];

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $maxSize = 5 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Profile picture too large (max 5 MB)']);
                return;
            }

            $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, $allowedMime)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid profile picture file type']);
                return;
            }

            // Save to filesystem
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = "profile_{$userId}_" . time() . "." . $ext;
            $relPath = "uploads/profiles/" . $fileName;
            $fullPath = BASE_PATH . "/public/" . $relPath;

            if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                $data['profile_picture_path'] = $relPath;
                $data['profile_picture_mime'] = $mime;
            }
        }

        $userModel = new User();
        $success = $userModel->updateProfile($userId, $data);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Profile updated successfully.' : 'Failed to update profile data.'
        ]);
    }

    public function uploadAvatar(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
            return;
        }

        $file = $_FILES['profile_picture'];
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Profile picture too large (max 2 MB)']);
            return;
        }

        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowedMime)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            return;
        }

        // Save to filesystem
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = "avatar_{$userId}_" . time() . "." . $ext;
        $relPath = "uploads/profiles/" . $fileName;
        $fullPath = BASE_PATH . "/public/" . $relPath;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to save to disk.']);
            return;
        }

        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE tenant_accounts SET profile_picture_path = :path, profile_picture = NULL, profile_picture_mime = :mime WHERE tenant_id = :id");
        $stmt->bindValue(':path', $relPath);
        $stmt->bindValue(':mime', $mime);
        $stmt->bindValue(':id', $userId);
        $success = $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Avatar uploaded.' : 'Failed',
            'path' => $relPath
        ]);
    }

    public function serveAvatar(): void
    {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant', 'Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        $db = getDbConnection();
        $stmt = $db->prepare("SELECT profile_picture, profile_picture_mime, profile_picture_path FROM tenant_accounts WHERE tenant_id = :id LIMIT 1");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        // Check filesystem first
        if (!empty($row['profile_picture_path'])) {
            $fullPath = BASE_PATH . "/public/" . $row['profile_picture_path'];
            if (file_exists($fullPath)) {
                header('Content-Type: ' . ($row['profile_picture_mime'] ?: 'image/jpeg'));
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                return;
            }
        }

        // Fallback to BLOB
        if (!empty($row['profile_picture'])) {
            header('Content-Type: ' . ($row['profile_picture_mime'] ?: 'image/jpeg'));
            header('Content-Length: ' . strlen($row['profile_picture']));
            echo $row['profile_picture'];
            return;
        }

        http_response_code(404);
        echo 'Avatar not found';
    }

    public function burialForm(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Damayan/user_burial-form');
    }

    public function burialDetails(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Damayan/user_burial-details');
    }

    public function burialDashboard(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Damayan/user_burial-dashboard');
    }

    public function maleCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant', 'Admin', 'Staff_Male']);
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/DawahAvailability.php';
        require_once BASE_PATH . '/app/models/User.php';
        
        $model = new CounselingRequest();
        $availModel = new DawahAvailability();
        $userModel = new User();
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $history = $model->getByUser($userId);
        $activeRequest = $model->getActiveRequestForUser($userId);
        $analytics = $model->getAnalytics('male');
        $blockedDates = $availModel->getBlockedDates('male');
        $counselors = $model->getActiveCounselors('male');
        $dbUser = $userModel->findById($userId);
        
        $this->view('user/Da\'wah/Male/user_form-male-counseling', [
            'history' => $history,
            'activeRequest' => $activeRequest,
            'analytics' => $analytics,
            'blockedDates' => $blockedDates,
            'counselors' => $counselors,
            'dbUser' => $dbUser
        ]);
    }

    public function femaleCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/DawahAvailability.php';

        $model = new CounselingRequest();
        $availModel = new DawahAvailability();

        $history = $model->getByUser($_SESSION['user_id']);
        $analytics = $model->getAnalytics('female');
        $blockedDates = $availModel->getBlockedDates('female');

        $this->view('user/Da\'wah/Female/user_form-female-counseling', [
            'history' => $history,
            'analytics' => $analytics,
            'blockedDates' => $blockedDates
        ]);
    }

    public function femaleCounselingDashboard(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Da\'wah/Female/user_female-counseling');
    }

    public function femaleEducation(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        $userModel = new User();
        $eduModel = new IslamicEducation();
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $dbUser = $userModel->findById($userId) ?: [];
        $extraInfo = $userModel->getAdditionalInfo($userId) ?: [];
        $dbUser = array_merge($dbUser, $extraInfo);

        $history = $eduModel->getByUser($userId);
        $activeRequest = $eduModel->getActiveForUser($userId);

        $hasPending = false;
        $hasApproved = false;
        if ($activeRequest) {
            $st = strtolower($activeRequest['status'] ?? '');
            if (in_array($st, ['pending', 'under_review'])) $hasPending = true;
            if (in_array($st, ['active', 'accepted', 'approved', 'enrolled', 'completed'])) $hasApproved = true;
        }

        $analytics = [
            'total' => count($history),
            'pending' => count(array_filter($history, fn($h) => in_array(strtolower($h['status'] ?? ''), ['pending', 'under_review']))),
            'approved' => count(array_filter($history, fn($h) => in_array(strtolower($h['status'] ?? ''), ['active', 'accepted', 'approved', 'enrolled', 'completed'])))
        ];

        $this->view('user/Da\'wah/Female/user_form-female-education', [
            'dbUser' => $dbUser,
            'history' => $history,
            'activeRequest' => $activeRequest,
            'hasPending' => $hasPending,
            'hasApproved' => $hasApproved,
            'analytics' => $analytics
        ]);
    }

    public function submitFemaleEducationRegistration(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $fname = trim($input['first_name'] ?? '');
        $lname = trim($input['last_name'] ?? '');
        $contact = trim($input['contact_number'] ?? '');
        $dob = !empty($input['birthdate']) ? trim($input['birthdate']) : null;

        if (empty($fname) || empty($lname)) {
            echo json_encode(['success' => false, 'message' => 'First name and Last name are required.']);
            return;
        }

        if (empty($contact)) {
            echo json_encode(['success' => false, 'message' => 'Contact number is required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $eduModel = new IslamicEducation();

        $chosenLevel = trim($input['level'] ?? 'Level 1 — Basic Arabic & Tajweed');
        $chosenProg = !empty($input['program_name']) ? trim($input['program_name']) : $chosenLevel;

        $payload = [
            'tenant_id' => $userId,
            'first_name' => $fname,
            'last_name' => $lname,
            'middle_initial' => trim($input['middle_initial'] ?? ''),
            'muslim_name' => trim($input['muslim_name'] ?? ''),
            'muslim_type' => trim($input['muslim_type'] ?? 'Revert'),
            'birthdate' => $dob,
            'date_of_shahadah' => !empty($input['date_of_shahadah']) ? trim($input['date_of_shahadah']) : null,
            'address' => trim($input['address'] ?? ''),
            'contact_number' => $contact,
            'facebook_account' => trim($input['facebook_account'] ?? ''),
            'studied_islamic_before' => trim($input['studied_islamic_before'] ?? 'No'),
            'previous_school' => trim($input['previous_school'] ?? ''),
            'emergency_name' => trim($input['emergency_name'] ?? ''),
            'emergency_relationship' => trim($input['emergency_relationship'] ?? ''),
            'emergency_address' => trim($input['emergency_address'] ?? ''),
            'emergency_contact' => trim($input['emergency_contact'] ?? ''),
            'is_minor' => !empty($input['is_minor']) ? 1 : 0,
            'parent_name' => trim($input['parent_name'] ?? ''),
            'parent_address' => trim($input['parent_address'] ?? ''),
            'parent_contact' => trim($input['parent_contact'] ?? ''),
            'student_signature' => trim($input['student_signature'] ?? ''),
            'guardian_signature' => trim($input['guardian_signature'] ?? ''),
            'batch_no' => trim($input['batch_no'] ?? 'Batch 08'),
            'level' => $chosenLevel,
            'payment_method' => (stripos(trim($input['payment_method'] ?? 'Cash'), 'online') !== false || stripos(trim($input['payment_method'] ?? ''), 'gcash') !== false) ? 'Online Payment' : 'Cash',
            'fee_amount' => 120.00,
            'payment_channel' => trim($input['payment_channel'] ?? ''),
            'payment_reference' => trim($input['payment_reference'] ?? ''),
            'approved_by' => 'Sister Khadijah Dimaano',
            'prepared_by' => 'IDFS Staff',
            'program_name' => $chosenProg,
            'gender' => 'female',
            'status' => 'pending'
        ];

        $newId = $eduModel->createRegistration($payload);

        if ($newId) {
            $refCode = '#EDU-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT);
            try {
                $notif = new Notification();
                $notif->create(
                    $userId,
                    'Female Education Registration Submitted',
                    "Your registration for Da'wah Female Section ($refCode) has been submitted successfully and is pending review.",
                    'info'
                );
            } catch (Exception $e) {}

            echo json_encode([
                'success' => true,
                'id' => $newId,
                'ref' => $refCode,
                'message' => 'Registration submitted successfully!'
            ]);
            return;
        }

        echo json_encode(['success' => false, 'message' => 'Failed to save registration. Please try again.']);
    }

    /**
     * Create PayMongo Checkout Session for Da'wah Female Education Registration (QR Ph / GCash / Maya / Card)
     */
    public function femaleEducationPayMongoCheckout(): void
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Please log in to continue with payment.']);
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';

        $userModel = new User();
        $dbUser = $userModel->findById($userId) ?: [];
        $eduModel = new IslamicEducation();

        $amount = 120.00;
        $batchNo = trim($body['batch_no'] ?? 'Batch 08');
        $level = trim($body['level'] ?? 'Level 1 — Basic Arabic & Tajweed');

        $existingRegId = !empty($body['existing_registration_id']) ? (int)$body['existing_registration_id'] : null;

        if ($existingRegId) {
            $existing = $eduModel->getById($existingRegId);
            if (!$existing || (int)$existing['tenant_id'] !== $userId) {
                echo json_encode(['success' => false, 'message' => 'Registration record not found or unauthorized.']);
                return;
            }
            $studentName = trim(($existing['first_name'] ?? '') . ' ' . ($existing['last_name'] ?? ''));
            $studentEmail = !empty($existing['email']) ? $existing['email'] : ($dbUser['email'] ?? ($_SESSION['email'] ?? 'student@iscag.local'));
            $contact = $existing['contact_number'] ?? ($dbUser['contact_number'] ?? ($dbUser['phone'] ?? ''));
            $formData = $existing;
        } else {
            $fname = trim($body['first_name'] ?? '');
            $lname = trim($body['last_name'] ?? '');
            $contact = trim($body['contact_number'] ?? '');
            $dob = !empty($body['birthdate']) ? trim($body['birthdate']) : null;
            $address = trim($body['address'] ?? '');

            if (empty($fname) || empty($lname)) {
                echo json_encode(['success' => false, 'message' => 'First name and Last name are required before proceeding to payment.']);
                return;
            }
            if (empty($contact)) {
                echo json_encode(['success' => false, 'message' => 'Contact number is required.']);
                return;
            }
            if (empty($dob)) {
                echo json_encode(['success' => false, 'message' => 'Date of birth is required.']);
                return;
            }
            if (empty($address)) {
                echo json_encode(['success' => false, 'message' => 'Residential address is required.']);
                return;
            }

            $studentName = trim("$fname $lname");
            $studentEmail = !empty($dbUser['email']) ? $dbUser['email'] : ($_SESSION['email'] ?? 'student@iscag.local');

            $formData = [
                'tenant_id' => $userId,
                'first_name' => $fname,
                'last_name' => $lname,
                'middle_initial' => trim($body['middle_initial'] ?? ''),
                'muslim_name' => trim($body['muslim_name'] ?? ''),
                'muslim_type' => trim($body['muslim_type'] ?? 'Revert'),
                'birthdate' => $dob,
                'date_of_shahadah' => !empty($body['date_of_shahadah']) ? trim($body['date_of_shahadah']) : null,
                'address' => $address,
                'contact_number' => $contact,
                'facebook_account' => trim($body['facebook_account'] ?? ''),
                'studied_islamic_before' => trim($body['studied_islamic_before'] ?? 'No'),
                'previous_school' => trim($body['previous_school'] ?? ''),
                'emergency_name' => trim($body['emergency_name'] ?? ''),
                'emergency_relationship' => trim($body['emergency_relationship'] ?? ''),
                'emergency_address' => trim($body['emergency_address'] ?? ''),
                'emergency_contact' => trim($body['emergency_contact'] ?? ''),
                'is_minor' => !empty($body['is_minor']) ? 1 : 0,
                'parent_name' => trim($body['parent_name'] ?? ''),
                'parent_address' => trim($body['parent_address'] ?? ''),
                'parent_contact' => trim($body['parent_contact'] ?? ''),
                'student_signature' => trim($body['student_signature'] ?? $studentName),
                'guardian_signature' => trim($body['guardian_signature'] ?? ''),
                'batch_no' => $batchNo,
                'level' => $level,
                'payment_method' => 'Online Payment',
                'fee_amount' => $amount,
                'payment_channel' => 'PayMongo Gateway (QR Ph / E-Wallets / Card)',
                'approved_by' => 'Sister Khadijah Dimaano',
                'prepared_by' => 'IDFS Staff',
                'program_name' => trim($body['program_name'] ?? "Islamic Education - $batchNo"),
                'gender' => 'female',
                'status' => 'pending'
            ];
        }

        // Generate official ISCAG payment reference number
        $refNo = 'ISCAG-EDU-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $lineItems = [
            [
                'name'        => "Da'wah Female Islamic Education — {$level}",
                'amount'      => $amount,
                'quantity'    => 1,
                'description' => "Registration Fee ({$batchNo}) [Ref: {$refNo}]"
            ]
        ];

        $description = "ISCAG Da'wah Female Education [Ref: {$refNo}] — {$batchNo}";

        // Save pending payment info to session for verification upon return
        $_SESSION['paymongo_pending_female_education'] = [
            'tenant_id'               => $userId,
            'existing_registration_id'=> $existingRegId,
            'amount'                  => $amount,
            'reference_number'        => $refNo,
            'form_data'               => $formData,
            'student_name'            => $studentName,
            'student_email'           => $studentEmail,
            'student_contact'         => $contact,
            'timestamp'               => time()
        ];

        $successUrl = full_url('/user/services/education/female/paymongo-success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl  = full_url('/user/services/education/female');

        $response = PayMongoHelper::createCheckoutSession(
            $amount,
            $description,
            $lineItems,
            $successUrl,
            $cancelUrl,
            [
                'tenant_id'        => (string)$userId,
                'module'           => 'female_education',
                'batch_no'         => $batchNo,
                'level'            => $level,
                'reference_number' => $refNo,
                'amount'           => (string)$amount,
                'existing_id'      => (string)($existingRegId ?? 0)
            ],
            $refNo,
            [
                'name'  => $studentName,
                'email' => $studentEmail,
                'phone' => $contact
            ]
        );

        if ($response['success'] && !empty($response['data']['attributes']['checkout_url'])) {
            echo json_encode([
                'success'      => true,
                'checkout_url' => $response['data']['attributes']['checkout_url'],
                'ref'          => $refNo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $response['message'] ?? 'Could not create PayMongo checkout session. Please try again.'
            ]);
        }
    }

    /**
     * Handle return from PayMongo checkout for Female Islamic Education
     */
    public function femaleEducationPayMongoSuccess(): void
    {
        Auth::protect();
        $sessionId = $_GET['session_id'] ?? '';
        $userId = (int)$_SESSION['user_id'];

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
        require_once BASE_PATH . '/app/helpers/AuditLogger.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        require_once BASE_PATH . '/app/models/AdminNotification.php';
        require_once BASE_PATH . '/app/models/User.php';

        $eduModel = new IslamicEducation();
        $pending = $_SESSION['paymongo_pending_female_education'] ?? null;

        $refNo = $pending['reference_number'] ?? ('ISCAG-EDU-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)));
        $studentName = $pending['student_name'] ?? ($_SESSION['name'] ?? 'Student');
        $amount = (float)($pending['amount'] ?? 120.00);

        // Verify with PayMongo API only if a real session ID was provided
        if (!empty($sessionId) && $sessionId !== '{CHECKOUT_SESSION_ID}') {
            try {
                $sessionData = PayMongoHelper::getCheckoutSession($sessionId);
                if (!empty($sessionData['success'])) {
                    $attrs = $sessionData['data']['attributes'] ?? [];
                    $meta  = $attrs['metadata'] ?? [];

                    if (!empty($attrs['reference_number'])) {
                        $refNo = $attrs['reference_number'];
                    } elseif (!empty($meta['reference_number'])) {
                        $refNo = $meta['reference_number'];
                    }
                }
            } catch (\Throwable $e) {}
        }

        // Prevent duplicate creation if page is refreshed
        $db = getDbConnection();
        $checkStmt = $db->prepare("SELECT id FROM islamic_education_enrollments WHERE payment_reference = :ref LIMIT 1");
        $checkStmt->execute(['ref' => $refNo]);
        $already = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($already) {
            unset($_SESSION['paymongo_pending_female_education']);
            header('Location: ' . url('/user/services/education/female?paid=1&ref=' . urlencode($refNo) . '&id=' . $already['id']));
            exit;
        }

        $regId = 0;
        $existingId = $pending['existing_registration_id'] ?? 0;

        if ($existingId > 0) {
            $regId = (int)$existingId;
            $eduModel->updatePaymentInfo($regId, [
                'payment_method'    => 'Online Payment',
                'fee_amount'        => $amount,
                'payment_channel'   => 'PayMongo Gateway (QR Ph / E-Wallets / Card)',
                'payment_reference' => $refNo,
                'payment_status'    => 'Paid',
                'status'            => 'pending'
            ]);
        } else {
            $formData = $pending['form_data'] ?? [];
            if (empty($formData)) {
                $userModel = new User();
                $dbUser = $userModel->findById($userId) ?: [];
                $formData = [
                    'tenant_id' => $userId,
                    'first_name' => $dbUser['first_name'] ?? 'Student',
                    'last_name' => $dbUser['last_name'] ?? '',
                    'contact_number' => $dbUser['phone'] ?? ($dbUser['contact_number'] ?? ''),
                    'address' => $dbUser['address'] ?? ($dbUser['current_address'] ?? 'ISCAG Community'),
                    'batch_no' => 'Batch 08',
                    'level' => 'Level 1 — Basic Arabic & Tajweed',
                    'gender' => 'female',
                    'status' => 'pending'
                ];
            }

            $formData['payment_method'] = 'Online Payment';
            $formData['fee_amount'] = $amount;
            $formData['payment_channel'] = 'PayMongo Gateway (QR Ph / E-Wallets / Card)';
            $formData['payment_reference'] = $refNo;
            $formData['payment_status'] = 'Paid';
            $formData['status'] = 'pending';

            $regId = $eduModel->createRegistration($formData);
        }

        $refCode = '#EDU-' . str_pad((string)$regId, 4, '0', STR_PAD_LEFT);

        // Audit Log
        try {
            if (class_exists('AuditLogger')) {
                AuditLogger::log('DAWAH', 'FEMALE_EDU_PAYMONGO_PAYMENT',
                    "Female Education registration fee of ₱" . number_format($amount, 2) . " paid via PayMongo ($refCode). Ref: $refNo");
            }
        } catch (\Throwable $e) {}

        // User Notification
        try {
            $notif = new Notification();
            $notif->create(
                $userId,
                'Registration Fee Confirmed (PayMongo)',
                "Your registration fee of ₱" . number_format($amount, 2) . " for Da'wah Female Education ($refCode) has been settled via PayMongo (Ref: $refNo). Your registration is now under review by Sister Khadijah Dimaano.",
                'payment'
            );
        } catch (\Throwable $e) {}

        // Admin Notification for Sister Khadijah / Staff
        try {
            $adminNotif = new AdminNotification();
            $adminNotif->create(
                'Female Education Registration & Payment Received',
                "{$studentName} registered for Da'wah Female Section ($refCode) and paid ₱" . number_format($amount, 2) . " via PayMongo (Ref: {$refNo}).",
                'payment',
                $studentName,
                $userId,
                null
            );
        } catch (\Throwable $e) {}

        // Clear session pending
        unset($_SESSION['paymongo_pending_female_education']);

        header('Location: ' . url('/user/services/education/female?paid=1&ref=' . urlencode($refNo) . '&id=' . $regId));
        exit;
    }

    public function femaleSchool(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']) ?: [];
        $extraInfo = $userModel->getAdditionalInfo($_SESSION['user_id']) ?: [];
        $dbUser = array_merge($dbUser, $extraInfo);
        $this->view('user/Da\'wah/Female/user_female-school', ['dbUser' => $dbUser, 'active_page' => 'female_school']);
    }

    public function femaleSubjects(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        // Check if enrolled - security gate
        if (empty($_SESSION['female_education_enrolled'])) {
            $this->redirect('/user/services/education/female');
            return;
        }

        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']) ?: [];
        $extraInfo = $userModel->getAdditionalInfo($_SESSION['user_id']) ?: [];
        $dbUser = array_merge($dbUser, $extraInfo);
        
        $this->view('user/Da\'wah/Female/user_female-subjects', ['dbUser' => $dbUser, 'active_page' => 'female_subjects']);
    }

    public function counselingResources(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;
        
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $counselingModel = new CounselingRequest();
        $history = $userId ? $counselingModel->getByUser((int)$userId) : [];

        $this->view('user/Da\'wah/Male/counseling_resources', [
            'history' => $history
        ]);
    }

    public function marriageForm(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        require_once BASE_PATH . '/app/models/DawahAvailability.php';

        $model = new MarriageRequest();
        $availModel = new DawahAvailability();

        $history = $model->getByUser($_SESSION['user_id']);
        $analytics = $model->getAnalytics();
        $blockedDates = $availModel->getBlockedDates('male');

        $this->view('user/Da\'wah/Male/user_form-marriage', [
            'history' => $history,
            'analytics' => $analytics,
            'blockedDates' => $blockedDates
        ]);
    }

    public function submitMarriage(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        $model = new MarriageRequest();

        $groom = trim($input['groom_name'] ?? '');
        $bride = trim($input['bride_name'] ?? '');
        $date  = $input['marriage_date'] ?? null;
        $time  = $input['marriage_time'] ?? null;
        $venue = trim($input['marriage_venue'] ?? 'ISCAG Mosque');

        if (empty($groom) || empty($bride) || empty($date) || empty($time)) {
            echo json_encode(['success' => false, 'message' => 'Please complete all required booking fields.']);
            return;
        }

        $newId = $model->createAndGetId([
            'tenant_id' => $_SESSION['user_id'],
            'groom_name' => $groom,
            'bride_name' => $bride,
            'marriage_date' => $date,
            'marriage_time' => $time,
            'marriage_venue' => $venue,
            'status' => 'pending'
        ]);

        if ($newId) {
            $refNo = '#MR-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT);
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$_SESSION['user_id'],
                    'Marriage Reservation Submitted',
                    "Your marriage ceremony reservation ($refNo) for $groom & $bride has been submitted successfully and is pending review by the Da'wah Department.",
                    'info'
                );
            } catch (Exception $e) {}
        }

        echo json_encode([
            'success' => (bool)$newId,
            'id' => $newId,
            'ref' => $newId ? ('#MR-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT)) : '',
            'message' => $newId ? 'Marriage reservation request submitted successfully.' : 'Failed to submit reservation.'
        ]);
    }

    public function conversionForm(): void
    {
        Auth::protect();
        $userId = $_SESSION['user_id'] ?? null;
        $userModel = new User();
        $dbUser = $userId ? $userModel->findById($userId) : [];
        
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        $conversionModel = new ConversionRequest();
        $history = $userId ? $conversionModel->getByUser((int)$userId) : [];

        $this->view('user/Da\'wah/Male/user_form-conversion', [
            'dbUser' => $dbUser,
            'history' => $history
        ]);
    }

    public function submitConversion(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Authentication required.']);
            return;
        }

        $fname = trim($input['fname'] ?? '');
        $lname = trim($input['lname'] ?? '');
        $adoptedName = trim($input['adopted_name'] ?? '');
        $convDate = $input['conversion_date'] ?? date('Y-m-d');

        if (empty($fname) || empty($lname) || empty($adoptedName)) {
            echo json_encode(['success' => false, 'message' => 'First name, last name, and adopted Muslim name are required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        $model = new ConversionRequest();

        $data = [
            'tenant_id' => $userId,
            'fname' => $fname,
            'mname' => trim($input['mname'] ?? ''),
            'lname' => $lname,
            'adopted_name' => $adoptedName,
            'sex' => $input['sex'] ?? 'Male',
            'civil_status' => $input['civil_status'] ?? '',
            'citizenship' => $input['citizenship'] ?? 'Filipino',
            'dob' => !empty($input['dob']) ? $input['dob'] : null,
            'age' => !empty($input['age']) ? (int)$input['age'] : null,
            'occupation' => trim(($input['occupation'] ?? '') === 'Others' ? ($input['occupation_other'] ?? 'Others') : ($input['occupation'] ?? '')),
            'former_religion' => trim(($input['former_religion'] ?? '') === 'Others' ? ($input['former_religion_other'] ?? 'Others') : ($input['former_religion'] ?? '')),
            'pob' => trim($input['pob'] ?? ''),
            'residence' => trim($input['residence'] ?? ''),
            'father_name' => trim($input['father_name'] ?? ''),
            'father_religion' => trim($input['father_religion'] ?? ''),
            'mother_name' => trim($input['mother_name'] ?? ''),
            'mother_religion' => trim($input['mother_religion'] ?? ''),
            'conversion_date' => $convDate,
            'witness1_name' => trim($input['witness1_name'] ?? ''),
            'witness1_address' => trim($input['witness1_address'] ?? ''),
            'witness2_name' => trim($input['witness2_name'] ?? ''),
            'witness2_address' => trim($input['witness2_address'] ?? ''),
            'status' => 'pending'
        ];

        $success = $model->create($data);

        if ($success) {
            try {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notif = new Notification();
                $notif->create(
                    (int)$userId,
                    'Conversion Registration Submitted',
                    "Your Islamic Conversion (Shahadah) registration for {$adoptedName} has been submitted successfully and is pending review.",
                    'info'
                );
            } catch (Exception $e) {}
            AuditLogger::log('DAWAH', 'CONVERSION_SUBMIT', "User submitted conversion registration for adopted name: {$adoptedName}");
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Conversion registration submitted successfully.' : 'Failed to submit registration.'
        ]);
        exit;
    }

    public function charity(): void
    {
        Auth::protect();
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('user/Damayan/user_charity', ['dbUser' => $dbUser]);
    }

    public function checkStatus(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? 0;
        
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        
        $userModel = new User();
        $notifModel = new Notification();
        
        $user = $userModel->findById($userId);
        $notifications = $notifModel->getUserNotifications($userId);
        
        // Sync session role with DB immediately
        $dbRole = $user['role'] ?? 'Guest';
        if ($dbRole !== ($_SESSION['role'] ?? '')) {
            $_SESSION['role'] = $dbRole;
        }
        
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        echo json_encode([
            'role' => $dbRole,
            'notifications' => $notifications
        ]);
        exit;
    }

    public function markNotificationRead(): void
    {
        Auth::protectRole(['Guest', 'Tenant', 'Applicant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $notifId = $input['id'] ?? 0;
            $type = $input['type'] ?? null;
            $userId = $_SESSION['user_id'] ?? 0;
            
            if ($userId) {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notifModel = new Notification();
                if ($notifId) {
                    $notifModel->markAsRead((int)$notifId, (int)$userId);
                }
                if ($type === 'approval') {
                    $notifModel->markTypeAsRead('approval', (int)$userId);
                }
                
                // If the session role is Guest but DB says Tenant, update session now
                require_once BASE_PATH . '/app/models/User.php';
                $userModel = new User();
                $user = $userModel->findById($userId);
                if ($user && isset($user['role']) && $user['role'] === 'Tenant') {
                    $_SESSION['role'] = 'Tenant';
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }

    public function markStatusSeen(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId) {
                $db = getDbConnection();
                $stmt = $db->prepare("UPDATE apartmentsapp SET status_seen = 1 WHERE tenant_id = :id");
                $stmt->execute(['id' => $userId]);
                echo json_encode(['success' => true]);
                return;
            }
        }
        echo json_encode(['success' => false]);
    }

    public function submitCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $gender = strtolower(trim($input['gender'] ?? 'male'));
        $fullName = trim($input['full_name'] ?? '');
        $age = !empty($input['age']) ? (int)$input['age'] : null;
        $civilStatus = trim($input['civil_status'] ?? '');
        $address = trim($input['address'] ?? '');
        $contactNumber = trim($input['contact_number'] ?? '');
        $reason = trim($input['reason'] ?? '');
        $description = trim($input['description'] ?? '');
        $sessionType = trim($input['session_type'] ?? 'In-Person');
        $date = !empty($input['preferred_date']) ? trim($input['preferred_date']) : null;
        $time = !empty($input['preferred_time']) ? trim($input['preferred_time']) : null;

        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Please select the primary concern for counseling.']);
            return;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        
        $newId = $model->createAndGetId([
            'tenant_id' => $userId,
            'full_name' => $fullName,
            'age' => $age,
            'civil_status' => $civilStatus,
            'address' => $address,
            'contact_number' => $contactNumber,
            'gender' => $gender ?: 'male',
            'reason' => $reason,
            'description' => $description,
            'session_type' => $sessionType,
            'preferred_date' => $date,
            'preferred_time' => $time,
            'status' => 'pending'
        ]);

        if ($newId) {
            $refNo = '#MC-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT);
            
            // Send user in-app notification
            try {
                $notif = new Notification();
                $notif->create(
                    $userId,
                    'Your Counseling Request Has Been Submitted',
                    "Thank you for reaching out to the Counseling and Guidance service. Your request ($refNo) is now pending review by our administrator. You will be notified once reviewed.",
                    'info'
                );
            } catch (Exception $e) {
                error_log("Counseling notification error: " . $e->getMessage());
            }

            echo json_encode([
                'success' => true,
                'id' => $newId,
                'ref' => $refNo,
                'redirect' => url('/user/services/counseling/status?id=' . $newId),
                'message' => 'Counseling request submitted successfully.'
            ]);
            return;
        }

        echo json_encode([
            'success' => false,
            'message' => 'Failed to save your counseling request. Please try again.'
        ]);
    }

    public function counselingStatus(): void
    {
        Auth::protectRole(['Guest', 'Tenant', 'Admin', 'Staff_Male', 'Staff_Female']);
        $id = (int)($_GET['id'] ?? 0);
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['role'] ?? '';

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/User.php';
        $model = new CounselingRequest();
        $userModel = new User();

        if ($id > 0) {
            $request = $model->getById($id);
        } else {
            $request = $model->getActiveRequestForUser($userId);
        }

        // RBAC: applicant only or authorized staff
        if ($request && $request['tenant_id'] != $userId && !in_array($role, ['Admin', 'Staff_Male', 'Staff_Female'])) {
            $this->redirect('/user/services/counseling/male');
            return;
        }

        if (!$request) {
            $this->redirect('/user/services/counseling/male');
            return;
        }

        $dbUser = $userModel->findById($userId);

        $this->view('user/Da\'wah/Male/counseling_status', [
            'request' => $request,
            'dbUser' => $dbUser,
            'active_page' => 'counseling_male'
        ]);
    }

    public function acceptCounselingReschedule(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $userId = (int)($_SESSION['user_id'] ?? 0);

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        $req = $model->getById($id);

        if (!$req || (int)$req['tenant_id'] !== $userId) {
            echo json_encode(['success' => false, 'message' => 'Record not found or unauthorized.']);
            return;
        }

        $success = $model->updateStatus($id, 'confirmed');

        if ($success) {
            $notif = new Notification();
            $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
            $notif->create(
                $userId,
                'Appointment Schedule Confirmed',
                "Alhamdulillah. You have confirmed the appointment schedule for reference $refNo. You can now view your confirmed appointment details in your Counseling Portal.",
                'success'
            );
        }

        echo json_encode(['success' => $success]);
    }
    public function submitBurial(): void {
        Auth::protect();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        // Support both FormData (multipart/form-data) and JSON
        $input = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'No request data provided.']);
            return;
        }

        $certActionType = strtolower(trim($input['cert_action_type'] ?? 'register'));
        if (!in_array($certActionType, ['register', 'copy'])) {
            $certActionType = 'register';
        }

        $fName = trim($input['firstName'] ?? ($input['deceased_first_name'] ?? ''));
        $mName = trim($input['middleName'] ?? ($input['deceased_middle_name'] ?? ''));
        $lName = trim($input['lastName'] ?? ($input['deceased_last_name'] ?? ''));
        $sex   = trim($input['sex'] ?? ($input['deceased_sex'] ?? 'Male'));
        $dod   = trim($input['dod'] ?? ($input['date_of_death'] ?? ''));
        $pod   = trim($input['pod'] ?? ($input['place_of_death'] ?? ''));
        $residence = trim($input['residence'] ?? ($input['deceased_address'] ?? ''));
        $informantName = trim($input['informantName'] ?? ($input['informant_name'] ?? ''));
        $informantContact = trim($input['informantContact'] ?? ($input['informant_contact'] ?? ''));
        $relationship = trim($input['relationship'] ?? 'Relative');

        // Validation based on cert action type
        if ($certActionType === 'copy') {
            $decName = trim($input['deceased_name'] ?? ($input['deceasedName'] ?? "$fName $lName"));
            if (empty($decName) && empty($input['existing_cert_no'])) {
                echo json_encode(['success' => false, 'message' => 'Deceased name as appearing on the certificate is required.']);
                return;
            }
            if (empty($informantName) || empty($informantContact)) {
                echo json_encode(['success' => false, 'message' => 'Requester name and contact number are required.']);
                return;
            }
            if (empty($input['copy_reason'])) {
                echo json_encode(['success' => false, 'message' => 'Please provide the reason for requesting another death certificate copy.']);
                return;
            }
        } else {
            if (empty($fName) || empty($lName)) {
                echo json_encode(['success' => false, 'message' => 'Deceased first and last name are required.']);
                return;
            }
            if (empty($dod)) {
                echo json_encode(['success' => false, 'message' => 'Date of death is required.']);
                return;
            }
            if (empty($pod)) {
                echo json_encode(['success' => false, 'message' => 'Place of death is required.']);
                return;
            }
            if (empty($residence)) {
                echo json_encode(['success' => false, 'message' => 'Deceased usual residence address is required.']);
                return;
            }
            if (empty($informantName) || empty($informantContact)) {
                echo json_encode(['success' => false, 'message' => 'Applicant/Informant name and contact number are required.']);
                return;
            }
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $fullName = trim("$fName $mName $lName");
        if (empty($fullName)) {
            $fullName = trim($input['deceased_name'] ?? '');
        }

        // Gate: Processing is only available for death certificates recorded within the Damayan Department
        $existingCertNo = trim($input['existing_cert_no'] ?? ($input['registry_number'] ?? ''));
        $encodedCertId = !empty($input['encoded_cert_id']) ? (int)$input['encoded_cert_id'] : null;
        $deceasedId = !empty($input['deceased_id']) ? (int)$input['deceased_id'] : null;

        $certFoundInDept = false;
        $linkedEncodedRecord = null;

        if ($encodedCertId) {
            $enc = $model->getEncodedDeathCertificateById($encodedCertId);
            if ($enc) {
                $certFoundInDept = true;
                $linkedEncodedRecord = $enc;
            }
        }

        if (!$certFoundInDept && !empty($existingCertNo)) {
            $enc = $model->findEncodedByRegistrationNo($existingCertNo);
            if ($enc) {
                $certFoundInDept = true;
                $linkedEncodedRecord = $enc;
                $encodedCertId = (int)$enc['id'];
            } else {
                $dup = $model->isDuplicateDeathCertRegNo($existingCertNo);
                if ($dup) {
                    $certFoundInDept = true;
                }
            }
        }

        if (!$certFoundInDept && $deceasedId) {
            try {
                $db = getDbConnection();
                $checkDec = $db->prepare("SELECT id, full_name FROM deceased_records WHERE id = :did LIMIT 1");
                $checkDec->execute(['did' => $deceasedId]);
                if ($checkDec->fetch()) {
                    $certFoundInDept = true;
                }
            } catch (\Throwable $e) {}
        }

        if (!$certFoundInDept && !empty($fullName)) {
            $potentialMatches = $model->checkPotentialDuplicates(['deceased_name' => $fullName, 'date_of_death' => $dod]);
            if (!empty($potentialMatches)) {
                $certFoundInDept = true;
            }
        }

        if (!$certFoundInDept) {
            echo json_encode([
                'success' => false,
                'message' => 'Processing is only available for death certificates recorded within the ISCAG Damayan Department. Please enter a valid registration number or select a recorded deceased record from the department archive.'
            ]);
            return;
        }

        if ($linkedEncodedRecord && empty($fullName)) {
            $fullName = $linkedEncodedRecord['deceased_name'];
        }

        // Duplicate check (for new registrations only)
        if ($certActionType === 'register' && $model->checkDuplicate((int)$_SESSION['user_id'], $fullName, $dod)) {
            echo json_encode([
                'success' => false, 
                'message' => 'An active death certificate registration request for this deceased person has already been submitted within the last 24 hours.'
            ]);
            return;
        }

        // Handle file upload if present
        $uploadedDocPath = null;
        if (isset($_FILES['supporting_document']) && $_FILES['supporting_document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['supporting_document'];
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExtensions)) {
                $uploadDir = BASE_PATH . '/public/uploads/burial/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $filename = 'burial_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                    $uploadedDocPath = 'uploads/burial/' . $filename;
                }
            }
        }

        // Services requested & Request Type
        $requestType = ($certActionType === 'copy') ? 'death_certificate' : ($input['request_type'] ?? 'both');
        $deathCertReq = 1;
        $cemeteryPlotReq = ($certActionType === 'copy') ? 0 : (isset($input['cemetery_plot_requested']) ? (int)$input['cemetery_plot_requested'] : ($requestType === 'death_certificate' ? 0 : 1));
        $deceasedId = !empty($input['deceased_id']) ? (int)$input['deceased_id'] : null;
        $cemeteryPref = $input['cemetery_preference'] ?? ($input['cemetery'] ?? 'ISCAG Designated Muslim Cemetery');
        $preferredSection = $input['preferred_section'] ?? null;

        $services = ($certActionType === 'copy') ? ["Death Certificate Copy"] : ($input['services'] ?? ($input['services_req'] ?? ["Death Certificate", "Cemetery Plot"]));
        if (is_string($services)) {
            $decoded = json_decode($services, true);
            if ($decoded) $services = $decoded;
            else $services = array_map('trim', explode(',', $services));
        }

        $data = [
            'tenant_id' => $_SESSION['user_id'],
            'deceased_id' => $deceasedId,
            'request_type' => $requestType,
            'death_cert_requested' => $deathCertReq,
            'cemetery_plot_requested' => $cemeteryPlotReq,
            'deceased_name' => $fullName,
            'deceased_first_name' => $fName,
            'deceased_middle_name' => $mName,
            'deceased_last_name' => $lName,
            'deceased_sex' => in_array(ucfirst($sex), ['Male', 'Female']) ? ucfirst($sex) : 'Male',
            'date_of_birth' => !empty($input['dob']) ? $input['dob'] : null,
            'date_of_death' => !empty($dod) ? $dod : date('Y-m-d'),
            'place_of_death' => $pod ?: 'On File',
            'residence' => $residence ?: 'On File',
            'religion' => $input['religion'] ?? 'Islam',
            'civil_status' => $input['civilStatus'] ?? 'Married',
            'nationality' => $input['citizenship'] ?? 'Filipino',
            'relationship' => $relationship,
            'informant_name' => $informantName,
            'informant_contact' => $informantContact,
            'informant_email' => $input['informantEmail'] ?? ($_SESSION['email'] ?? ''),
            'informant_address' => $input['informantAddress'] ?? $residence,
            'requested_services' => $services,
            'cemetery_preference' => $cemeteryPref,
            'preferred_section' => $preferredSection,
            'target_timing' => $input['targetTiming'] ?? 'Standard Processing',
            'additional_notes' => $input['additionalNotes'] ?? null,
            'supporting_document' => $uploadedDocPath,
            'cert_action_type' => $certActionType,
            'existing_cert_no' => trim($input['existing_cert_no'] ?? ''),
            'encoded_cert_id' => !empty($input['encoded_cert_id']) ? (int)$input['encoded_cert_id'] : null,
            'copy_reason' => trim($input['copy_reason'] ?? ''),
            'num_copies' => max(1, (int)($input['num_copies'] ?? 1))
        ];

        $result = $model->create($data);

        if (!$result || empty($result['success'])) {
            echo json_encode(['success' => false, 'message' => 'Failed to save request to database.']);
            return;
        }

        $refId = $result['ref_id'];

        // Cross-module compatibility with FuneralCase only for new registrations
        if ($certActionType === 'register') {
            try {
                require_once BASE_PATH . '/app/models/FuneralCase.php';
                $fcModel = new FuneralCase();
                $fcModel->create([
                    'tenant_id' => $_SESSION['user_id'],
                    'deceased_first_name' => $fName,
                    'deceased_middle_name' => $mName,
                    'deceased_last_name' => $lName,
                    'deceased_sex' => $sex,
                    'deceased_dob' => !empty($input['dob']) ? $input['dob'] : null,
                    'deceased_dod' => $dod,
                    'deceased_place_of_death' => $pod,
                    'deceased_address' => $residence,
                    'deceased_civil_status' => $input['civilStatus'] ?? 'Married',
                    'deceased_nationality' => $input['citizenship'] ?? 'Filipino',
                    'deceased_religion' => $input['religion'] ?? 'Islam',
                    'informant_name' => $informantName,
                    'informant_relationship' => $relationship,
                    'informant_contact' => $informantContact,
                    'informant_address' => $input['informantAddress'] ?? $residence
                ]);
            } catch (\Throwable $te) {}
        }

        $successMsg = ($certActionType === 'copy') 
            ? 'Request for another death certificate copy submitted successfully.' 
            : 'Death certificate registration submitted successfully.';

        echo json_encode([
            'success' => true,
            'ref_id' => $refId,
            'message' => $successMsg
        ]);
        return;
    }

    public function confirmBurialCopyReceipt(): void {
        Auth::protect();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
        $refId = trim($input['ref_id'] ?? '');

        if (empty($refId)) {
            echo json_encode(['success' => false, 'message' => 'Reference ID is required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $burial = $model->findByRefId($refId);

        if (!$burial) {
            echo json_encode(['success' => false, 'message' => 'Request record not found.']);
            return;
        }

        if ((int)$burial['tenant_id'] !== (int)$_SESSION['user_id'] && !Auth::hasRole(['Admin', 'Staff_Damayan'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        $userName = $_SESSION['name'] ?? 'Applicant';
        $result = $model->confirmCopyReceipt((int)$burial['id'], (int)$_SESSION['user_id'], $userName);

        echo json_encode($result);
        return;
    }

    public function respondBurialInfo(): void {
        Auth::protect();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
        $refId = trim($input['ref_id'] ?? '');
        $infoText = trim($input['additional_info'] ?? '');

        if (empty($refId) || empty($infoText)) {
            echo json_encode(['success' => false, 'message' => 'Reference ID and information details are required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $burial = $model->findByRefId($refId);

        if (!$burial) {
            echo json_encode(['success' => false, 'message' => 'Burial request not found.']);
            return;
        }

        if ((int)$burial['tenant_id'] !== (int)$_SESSION['user_id'] && !Auth::hasRole(['Admin', 'Staff_Damayan'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        // Handle uploaded document
        $docPath = null;
        if (isset($_FILES['additional_doc']) && $_FILES['additional_doc']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['additional_doc'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                $uploadDir = BASE_PATH . '/public/uploads/burial/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $filename = 'addinfo_' . time() . '_' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $docPath = 'uploads/burial/' . $filename;
                }
            }
        }

        $userName = $_SESSION['name'] ?? 'Applicant';
        $success = $model->submitAdditionalInfo((int)$burial['id'], (int)$burial['tenant_id'], $infoText, $docPath, $userName);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Additional information submitted successfully.' : 'Failed to submit additional information.'
        ]);
        return;
    }

    public function submitDonation(): void {
        Auth::protect();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        require_once BASE_PATH . '/app/models/User.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $tenantName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Tenant Donor');

        // Check if multipart form or JSON
        $input = !empty($_POST) ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);

        $method = trim($input['donation_method'] ?? '');
        $category = trim($input['donation_category'] ?? ($method === 'direct' ? 'cash' : 'digital'));
        if ($method === 'direct') {
            $category = 'cash';
        }
        if (!in_array($category, ['food', 'cash', 'digital'])) {
            $category = 'digital';
        }

        $customDonorName = trim($input['donor_name'] ?? '');
        $finalDonorName = !empty($customDonorName) ? $customDonorName : $tenantName;

        $customContact = trim($input['donor_contact'] ?? ($input['donor_address'] ?? ''));
        $finalDonorContact = !empty($customContact) ? $customContact : ($dbUser['email'] ?? ($dbUser['contactnum'] ?? ''));

        $receiptPath = null;
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
            if (in_array($ext, $allowed)) {
                $uploadDir = BASE_PATH . '/public/uploads/charity/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = 'receipt_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['receipt']['tmp_name'], $uploadDir . $filename)) {
                    $receiptPath = 'uploads/charity/' . $filename;
                }
            }
        }

        $charity = new CharityProgram();
        $data = [
            'tenant_id' => (int)$_SESSION['user_id'],
            'donor_name' => $finalDonorName,
            'donor_address' => $finalDonorContact,
            'donation_category' => $category,
            'remarks' => trim($input['remarks'] ?? '')
        ];

        if ($category === 'food') {
            $itemDesc = trim($input['item_description'] ?? '');
            $quantity = (int)($input['quantity'] ?? 0);
            $unit = trim($input['unit'] ?? 'packs');
            $preferredDate = trim($input['preferred_dropoff_date'] ?? '');
            $dropoffLoc = trim($input['dropoff_location'] ?? CharityProgram::OFFICIAL_ISCAG_ADDRESS);

            if (empty($itemDesc)) {
                echo json_encode(['success' => false, 'error' => 'Please provide a description of the food items or supplies.']);
                exit;
            }
            if ($quantity <= 0) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid quantity greater than 0.']);
                exit;
            }
            if (empty($preferredDate)) {
                echo json_encode(['success' => false, 'error' => 'Please select your preferred drop-off date.']);
                exit;
            }

            $data['donation_type'] = 'in-kind';
            $data['item_description'] = $itemDesc;
            $data['quantity'] = $quantity;
            $data['unit'] = $unit;
            $data['preferred_dropoff_date'] = $preferredDate;
            $data['dropoff_location'] = $dropoffLoc;
            $data['target_program'] = 'Food Security & Ramadan Ration';
            $data['status'] = 'Pending Coordination';
            $data['date_received'] = date('Y-m-d');
            $data['payment_method'] = 'Direct In-Kind Drop-off';

        } elseif ($category === 'cash') {
            $amount = (float)($input['amount'] ?? 0);
            $targetProgram = trim($input['target_program'] ?? 'General Welfare & Relief');
            $subDate = trim($input['submission_date'] ?? date('Y-m-d'));

            if ($amount <= 0) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid cash donation amount greater than ₱0.00.']);
                exit;
            }

            $data['donation_type'] = 'monetary';
            $data['amount'] = $amount;
            $data['target_program'] = $targetProgram;
            $data['date_received'] = $subDate;
            $data['payment_method'] = 'Cash (ISCAG Office)';
            $data['status'] = 'Pending Receipt';

        } else { // digital
            $amount = (float)($input['amount'] ?? 0);
            $targetProgram = trim($input['target_program'] ?? 'General Welfare & Relief');
            $paymentMethod = trim($input['payment_method'] ?? 'GCash');
            $paymentRef = trim($input['payment_reference'] ?? '');

            if ($amount <= 0) {
                echo json_encode(['success' => false, 'error' => 'Please enter a valid donation amount greater than ₱0.00.']);
                exit;
            }

            $targetBank = trim($input['target_bank_name'] ?? '');
            $targetAccNum = trim($input['target_account_number'] ?? '');
            $targetAccName = trim($input['target_account_name'] ?? '');
            $targetBranch = trim($input['target_bank_branch'] ?? '');

            $acctNotes = [];
            if (!empty($targetBank)) $acctNotes[] = "Bank: {$targetBank}";
            if (!empty($targetAccNum)) $acctNotes[] = "Acct #: {$targetAccNum}";
            if (!empty($targetAccName)) $acctNotes[] = "Acct Name: {$targetAccName}";
            if (!empty($targetBranch)) $acctNotes[] = "Branch: {$targetBranch}";

            if (!empty($acctNotes)) {
                $noteStr = implode(', ', $acctNotes);
                $data['remarks'] = !empty($data['remarks']) ? $data['remarks'] . " [Real Payment Account: {$noteStr}]" : "[Real Payment Account: {$noteStr}]";
            }

            $data['donation_type'] = 'monetary';
            $data['amount'] = $amount;
            $data['target_program'] = $targetProgram;
            $data['date_received'] = date('Y-m-d');
            $data['payment_method'] = $paymentMethod;
            $data['payment_reference'] = $paymentRef;
            $data['receipt_path'] = $receiptPath;
            $data['status'] = 'Pending Verification';
        }

        $res = $charity->recordDonation($data, $tenantName, (int)$_SESSION['user_id']);
        echo json_encode($res);
        exit;
    }

    /**
     * Create PayMongo Checkout Session for Charity Donation (GCash / Maya / Card)
     */
    public function charityPayMongoCheckout(): void {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Please log in to make a donation.']);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $amount = (float)($body['amount'] ?? 0);
        $targetProgram = trim($body['target_program'] ?? 'General Welfare & Relief');
        $userId = $_SESSION['user_id'];

        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid donation amount.']);
            return;
        }

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
        require_once BASE_PATH . '/app/models/User.php';

        $userModel = new User();
        $dbUser = $userModel->findById($userId);

        $donorEmail = !empty($dbUser['email']) ? trim($dbUser['email']) : ($_SESSION['email'] ?? 'donor@iscag.local');
        $donorName  = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'ISCAG Donor');

        // Generate a unique donation reference number
        $refNo = 'ISCAG-DON-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $lineItems = [
            [
                'name'        => $targetProgram . ' — Charity Donation',
                'amount'      => $amount,
                'quantity'    => 1,
                'description' => 'ISCAG Damayan Charity Donation'
            ]
        ];

        $description = "ISCAG Damayan Charity Donation [Ref: {$refNo}] — {$targetProgram}";

        // Store pending payment info in session for verification on return
        $_SESSION['paymongo_pending_charity'] = [
            'tenant_id'        => $userId,
            'amount'           => $amount,
            'target_program'   => $targetProgram,
            'donor_name'       => $donorName,
            'donor_contact'    => $donorEmail,
            'reference_number' => $refNo,
            'timestamp'        => time()
        ];

        $successUrl = full_url('/user/services/charity/paymongo-success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl  = full_url('/user/services/charity');

        $response = PayMongoHelper::createCheckoutSession(
            $amount,
            $description,
            $lineItems,
            $successUrl,
            $cancelUrl,
            [
                'tenant_id'        => (string)$userId,
                'target_program'   => $targetProgram,
                'amount'           => (string)$amount,
                'reference_number' => $refNo,
                'type'             => 'charity_donation'
            ],
            $refNo,
            [
                'name'  => $donorName,
                'email' => $donorEmail
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
     * Handle return from PayMongo checkout for Charity Donation
     */
    public function charityPayMongoSuccess(): void {
        Auth::protect();
        $sessionId = $_GET['session_id'] ?? '';
        $userId = $_SESSION['user_id'];

        require_once BASE_PATH . '/app/helpers/PayMongoHelper.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';
        require_once BASE_PATH . '/app/models/User.php';

        $pending = $_SESSION['paymongo_pending_charity'] ?? null;

        $donorName    = $pending['donor_name'] ?? ($_SESSION['name'] ?? 'ISCAG Donor');
        $donorContact = $pending['donor_contact'] ?? '';
        $amount       = (float)($pending['amount'] ?? 0);
        $targetProgram = $pending['target_program'] ?? 'General Welfare & Relief';
        $refNo        = $pending['reference_number'] ?? ('ISCAG-DON-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)));

        // Verify with PayMongo API if session_id is available
        if (!empty($sessionId)) {
            $sessionData = PayMongoHelper::getCheckoutSession($sessionId);

            if ($sessionData['success']) {
                $attrs = $sessionData['data']['attributes'] ?? [];
                $meta  = $attrs['metadata'] ?? [];

                // Recover reference from PayMongo metadata if session was lost
                if (!empty($attrs['reference_number'])) {
                    $refNo = $attrs['reference_number'];
                } elseif (!empty($meta['reference_number'])) {
                    $refNo = $meta['reference_number'];
                }

                // Recover donation details from metadata if session was lost
                if (empty($pending) || (int)($pending['tenant_id'] ?? 0) !== (int)$userId) {
                    if (!empty($meta['tenant_id']) && (int)$meta['tenant_id'] === (int)$userId) {
                        $amount        = (float)($meta['amount'] ?? $amount);
                        $targetProgram = $meta['target_program'] ?? $targetProgram;
                    }
                }

                // Get payment ID from PayMongo response for the reference if needed
                if (!empty($attrs['payments'][0]['id']) && empty($pending['reference_number'])) {
                    $refNo = $attrs['payments'][0]['id'];
                }
            }
        }

        unset($_SESSION['paymongo_pending_charity']);

        // Check if this payment/reference was already recorded to prevent duplicates on refresh
        $db = getDbConnection();
        $checkStmt = $db->prepare("SELECT donation_ref FROM charity_donations_records WHERE payment_reference = :ref LIMIT 1");
        $checkStmt->execute(['ref' => $refNo]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            header('Location: ' . url('/user/services/charity?paid=1&ref=' . urlencode($existing['donation_ref'])));
            exit;
        }

        // Record the donation if we have valid data
        $confirmedRef = $refNo;
        if ($amount > 0) {
            $userModel = new User();
            $dbUser = $userModel->findById($userId);
            $tenantName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: $donorName;

            $charity = new CharityProgram();
            $data = [
                'tenant_id'        => (int)$userId,
                'donor_name'       => $tenantName,
                'donor_address'    => $donorContact,
                'donation_category'=> 'digital',
                'donation_type'    => 'monetary',
                'amount'           => $amount,
                'target_program'   => $targetProgram,
                'date_received'    => date('Y-m-d'),
                'payment_method'   => 'PayMongo (Online)',
                'payment_reference'=> $refNo,
                'received_by'      => 'PayMongo Gateway (Verified)',
                'status'           => 'Available',
                'remarks'          => 'Paid via PayMongo Secure Gateway (auto-verified)'
            ];

            $res = $charity->recordDonation($data, $tenantName, (int)$userId);
            if (!empty($res['donation_ref'])) {
                $confirmedRef = $res['donation_ref'];
            }

            require_once BASE_PATH . '/app/helpers/AuditLogger.php';
            AuditLogger::log('CHARITY', 'PAYMONGO_DONATION', "PayMongo charity donation verified. Ref: {$refNo} ({$confirmedRef}), Amount: ₱" . number_format($amount, 2) . ", Program: {$targetProgram}");

            require_once BASE_PATH . '/app/models/Notification.php';
            (new Notification())->create(
                $userId,
                'Charity Donation Successful',
                'Your donation of ₱' . number_format($amount, 2) . ' to ' . htmlspecialchars($targetProgram) . ' via PayMongo has been confirmed (Ref: ' . htmlspecialchars($confirmedRef) . '). JazakAllahu Khairan!',
                'payment'
            );

            require_once BASE_PATH . '/app/models/AdminNotification.php';
            (new AdminNotification())->create(
                'Charity Donation Received (PayMongo)',
                $tenantName . ' donated ₱' . number_format($amount, 2) . ' to ' . $targetProgram . ' via PayMongo (Ref: ' . $confirmedRef . ').',
                'payment',
                $tenantName,
                $userId,
                null
            );
        }

        header('Location: ' . url('/user/services/charity?paid=1&ref=' . urlencode($confirmedRef)));
        exit;
    }

    /* ======================================================================
     *  FUNERAL CASE MANAGEMENT
     * ==================================================================== */

    /** Show the death report / burial form */
    public function funeralReport(): void
    {
        Auth::protect();
        header('Location: ' . url('/user/services/burial-details?action=request'));
        exit;
    }

    /** Submit the death report (POST, JSON) */
    public function submitFuneralReport(): void
    {
        Auth::protect();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $model = new FuneralCase();

        $input['tenant_id'] = $_SESSION['user_id'];
        $caseId = $model->create($input);

        if ($caseId) {
            $case = $model->findById($caseId);
            AuditLogger::log('FUNERAL', 'SUBMIT_DEATH_REPORT',
                "Death report submitted — Case #{$case['case_number']} for {$input['deceased_first_name']} {$input['deceased_last_name']}");
            echo json_encode(['success' => true, 'case_id' => $caseId, 'case_number' => $case['case_number']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create funeral case']);
        }
        exit;
    }

    /** Show user's burial dashboard (legacy funeral case route) */
    public function funeralCase(): void
    {
        Auth::protect();
        header('Location: ' . url('/user/services/burial-dashboard'));
        exit;
    }

    /** Upload a document for a funeral case (POST, multipart) */
    public function uploadFuneralDoc(): void
    {
        Auth::protect();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit;
        }

        $docId  = $_POST['doc_id'] ?? 0;
        $caseId = $_POST['case_id'] ?? 0;

        if (!$docId || !$caseId || empty($_FILES['document'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $model = new FuneralCase();
        $case  = $model->findById((int) $caseId);

        if (!$case || $case['tenant_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $file = $_FILES['document'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf','jpg','jpeg','png','gif','doc','docx'];
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            exit;
        }

        $uploadDir = BASE_PATH . '/public/uploads/funeral/' . $case['case_number'];
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $safeName = 'doc_' . $docId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . '/' . $safeName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $relPath = 'uploads/funeral/' . $case['case_number'] . '/' . $safeName;
            $model->updateDocument((int) $docId, [
                'file_path' => $relPath,
                'file_name' => $file['name']
            ]);
            $model->addLog((int) $caseId, $_SESSION['user_id'], null, 'DOC_UPLOADED', 'DOCUMENTS',
                "Document uploaded: {$file['name']}", 'Uploaded');

            echo json_encode(['success' => true, 'file_name' => $file['name']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Upload failed']);
        }
        exit;
    }

    /** Submit PSA certificate request (POST, JSON) */
    public function submitPsaRequest(): void
    {
        Auth::protect();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $model = new FuneralCase();

        $case = $model->findById((int)($input['case_id'] ?? 0));
        if (!$case || $case['tenant_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($case['status'] !== 'Registered' && $case['status'] !== 'Completed') {
            echo json_encode(['success' => false, 'error' => 'Death must be registered before requesting PSA certificate']);
            exit;
        }

        $psaId = $model->createPsaRequest($case['id'], $input);
        echo json_encode(['success' => (bool)$psaId, 'psa_id' => $psaId]);
        exit;
    }

    /** Serve a funeral document file */
    public function serveFuneralDoc(): void
    {
        Auth::protect();
        $path = $_GET['path'] ?? '';
        if (!$path) { http_response_code(404); exit; }

        $fullPath = BASE_PATH . '/public/' . $path;
        if (!file_exists($fullPath)) { http_response_code(404); exit; }

        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
        readfile($fullPath);
        exit;
    }
}
