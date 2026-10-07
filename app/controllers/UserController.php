<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/ApartmentApp.php';

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
            'conversionRequests' => $conversionRequests
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
        $_GET['tab'] = $_GET['tab'] ?? 'request';
        $this->funeralCase();
    }

    public function burialDashboard(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Damayan/user_burial-dashboard');
    }

    public function maleCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/DawahAvailability.php';
        
        $model = new CounselingRequest();
        $availModel = new DawahAvailability();
        
        $history = $model->getByUser($_SESSION['user_id']);
        $analytics = $model->getAnalytics('male');
        $blockedDates = $availModel->getBlockedDates('male');
        
        $this->view('user/Da\'wah/Male/user_form-male-counseling', [
            'history' => $history,
            'analytics' => $analytics,
            'blockedDates' => $blockedDates
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
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        
        // Application status is now handled within the view via $hasPending/$hasApproved
        // No redirect needed to allow viewing status on this page.

        $history = [];
        $analytics = ['total' => 0, 'pending' => 0, 'approved' => 0];

        $this->view('user/Da\'wah/Female/user_form-female-education', [
            'dbUser' => $dbUser,
            'history' => $history,
            'analytics' => $analytics
        ]);
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

        $success = $model->create([
            'tenant_id' => $_SESSION['user_id'],
            'groom_name' => $groom,
            'bride_name' => $bride,
            'marriage_date' => $date,
            'marriage_time' => $time,
            'marriage_venue' => $venue,
            'status' => 'pending'
        ]);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Marriage reservation request submitted successfully.' : 'Failed to submit reservation.'
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
            echo json_encode(['success' => false, 'message' => 'Invalid method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $userId = $_SESSION['user_id'];
        $gender = strtolower($input['gender'] ?? 'male');
        $reason = trim($input['reason'] ?? '');
        $date = $input['preferred_date'] ?? null;
        $time = $input['preferred_time'] ?? null;

        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Reason for counseling is required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $model = new CounselingRequest();
        
        $success = $model->create([
            'tenant_id' => $userId,
            'gender' => $gender ?: 'male',
            'reason' => $reason,
            'preferred_date' => $date,
            'preferred_time' => $time,
            'status' => 'pending'
        ]);

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Counseling request submitted successfully.' : 'Failed to save request.'
        ]);
    }
    public function submitBurial(): void {
        Auth::protect();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        
        $refId = 'BR-' . date('Ymd') . '-' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);
        
        $data = [
            'ref_id' => $refId,
            'tenant_id' => $_SESSION['user_id'],
            'deceased_name' => ($input['firstName'] ?? '') . ' ' . ($input['lastName'] ?? ''),
            'date_of_birth' => $input['dob'] ?? null,
            'date_of_death' => $input['dod'] ?? date('Y-m-d'),
            'place_of_death' => $input['pod'] ?? 'N/A',
            'residence' => $input['residence'] ?? 'N/A',
            'religion' => $input['religion'] ?? 'Islam',
            'relationship' => $input['relationship'] ?? 'Relative'
        ];

        $success = $model->create($data);
        $caseNumber = null;

        // Also create death certificate tracking record so it connects to the Certificate Tracking System
        if ($success) {
            try {
                require_once BASE_PATH . '/app/models/FuneralCase.php';
                $fcModel = new FuneralCase();
                $caseId = $fcModel->create([
                    'tenant_id' => $_SESSION['user_id'],
                    'deceased_first_name' => $input['firstName'] ?? '',
                    'deceased_middle_name' => $input['middleName'] ?? '',
                    'deceased_last_name' => $input['lastName'] ?? '',
                    'deceased_sex' => $input['sex'] ?? null,
                    'deceased_dob' => !empty($input['dob']) ? $input['dob'] : null,
                    'deceased_dod' => !empty($input['dod']) ? $input['dod'] : date('Y-m-d'),
                    'deceased_place_of_death' => $input['pod'] ?? 'N/A',
                    'deceased_address' => $input['residence'] ?? 'N/A',
                    'deceased_civil_status' => $input['civilStatus'] ?? '',
                    'deceased_nationality' => $input['citizenship'] ?? 'Filipino',
                    'deceased_religion' => $input['religion'] ?? 'Islam',
                    'deceased_occupation' => $input['occupation'] ?? '',
                    'informant_name' => !empty($input['informantName']) ? $input['informantName'] : ($_SESSION['name'] ?? 'Requester'),
                    'informant_relationship' => $input['relationship'] ?? 'Relative',
                    'informant_contact' => $input['informantContact'] ?? '',
                    'informant_address' => $input['informantAddress'] ?? ($input['residence'] ?? '')
                ]);
                if ($caseId) {
                    $createdCase = $fcModel->find($caseId);
                    $caseNumber = $createdCase['case_number'] ?? null;
                }
            } catch (\Throwable $te) {
                error_log("submitBurial auto-case error: " . $te->getMessage());
            }
        }

        echo json_encode(['success' => $success, 'ref_id' => $refId, 'case_number' => $caseNumber]);
        exit;
    }

    public function submitDonation(): void {
        Auth::protect();
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/CharityDonation.php';
        $model = new CharityDonation();
        
        $data = [
            'tenant_id' => $_SESSION['user_id'],
            'donor_name' => $_SESSION['name'] ?? 'Self',
            'amount' => $input['amount'] ?? 0,
            'program_id' => $input['program_id'] ?? 1
        ];

        $success = $model->create($data);
        echo json_encode(['success' => $success]);
        exit;
    }

    /* ======================================================================
     *  FUNERAL CASE MANAGEMENT
     * ==================================================================== */

    /** Show the death report form */
    public function funeralReport(): void
    {
        Auth::protect();
        require_once BASE_PATH . '/app/models/User.php';
        $u = new User();
        $dbUser = $u->findById($_SESSION['user_id']) ?: [];
        $extra  = $u->getAdditionalInfo($_SESSION['user_id']) ?: [];
        $dbUser = array_merge($dbUser, $extra);
        $this->view('user/Damayan/user_funeral-report', ['dbUser' => $dbUser]);
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

    /** Show user's funeral case dashboard */
    public function funeralCase(): void
    {
        Auth::protect();
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        require_once BASE_PATH . '/app/models/User.php';

        $model = new FuneralCase();
        $cases = $model->getByTenantId($_SESSION['user_id']);

        // Get case details if ?case= is provided
        $caseDetail = null;
        $documents  = [];
        $psaRequest = null;
        $logs       = [];
        $caseNum    = $_GET['case'] ?? '';

        if ($caseNum) {
            $caseDetail = $model->findByCaseNumber($caseNum);
            if ($caseDetail && $caseDetail['tenant_id'] == $_SESSION['user_id']) {
                $documents  = $model->getDocuments($caseDetail['id']);
                $psaRequest = $model->getPsaRequest($caseDetail['id']);
                $logs       = $model->getLogs($caseDetail['id']);
            } else {
                $caseDetail = null;
            }
        } elseif (!empty($cases)) {
            $caseDetail = $cases[0];
            $documents  = $model->getDocuments($caseDetail['id']);
            $psaRequest = $model->getPsaRequest($caseDetail['id']);
            $logs       = $model->getLogs($caseDetail['id']);
        }

        $u = new User();
        $dbUser = $u->findById($_SESSION['user_id']) ?: [];

        $this->view('user/Damayan/user_funeral-case', [
            'cases'      => $cases,
            'caseDetail' => $caseDetail,
            'documents'  => $documents,
            'psaRequest' => $psaRequest,
            'logs'       => $logs,
            'dbUser'     => $dbUser
        ]);
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
