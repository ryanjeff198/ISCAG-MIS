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
        
        $this->view('dashboard', [
            'account' => $account,
            'info' => $info,
            'application' => $application
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

    public function notifications(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        $userModel = new User();
        $account = $userModel->findById($userId);
        $info = $userModel->getAdditionalInfo($userId);

        require_once BASE_PATH . '/app/models/Notification.php';
        $notifModel = new Notification();
        $notifications = $notifModel->getUserNotifications($userId);

        $this->view('user/tenant_notification', [
            'account' => $account,
            'info' => $info,
            'notifications' => $notifications
        ]);
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

            $binaryData = file_get_contents($file['tmp_name']);
            if ($binaryData !== false) {
                $data['profile_picture'] = $binaryData;
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
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            return;
        }

        $binaryData = file_get_contents($file['tmp_name']);
        if ($binaryData === false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to read file.']);
            return;
        }

        $db = getDbConnection();
        $stmt = $db->prepare("UPDATE tenant_accounts SET profile_picture = :pic, profile_picture_mime = :mime WHERE tenant_id = :id");
        $stmt->bindValue(':pic', $binaryData, PDO::PARAM_LOB);
        $stmt->bindValue(':mime', $mime);
        $stmt->bindValue(':id', $userId);
        $success = $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Avatar uploaded.' : 'Failed',
        ]);
    }

    public function serveAvatar(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $userId = $_SESSION['user_id'] ?? null;

        $db = getDbConnection();
        $stmt = $db->prepare("SELECT profile_picture, profile_picture_mime FROM tenant_accounts WHERE tenant_id = :id LIMIT 1");
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['profile_picture'])) {
            header('Content-Type: ' . ($row['profile_picture_mime'] ?: 'image/jpeg'));
            header('Content-Length: ' . strlen($row['profile_picture']));
            header('Cache-Control: private, max-age=3600');
            echo $row['profile_picture'];
        } else {
            http_response_code(404);
            echo 'Image not found';
        }
    }

    public function burialForm(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Damayan/user_burial-form');
    }

    public function maleCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Da\'awah/Male/user_form-male-counseling');
    }

    public function femaleCounseling(): void
    {
        Auth::protectRole(['Guest', 'Tenant']);
        $this->view('user/Da\'awah/Female/user_form-female-counseling');
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
        Auth::protectRole(['Applicant', 'Tenant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $notifId = $input['id'] ?? 0;
            $userId = $_SESSION['user_id'] ?? 0;
            
            if ($notifId && $userId) {
                require_once BASE_PATH . '/app/models/Notification.php';
                $notifModel = new Notification();
                $notifModel->markAsRead($notifId, $userId);
                
                // If the session role is Guest but DB says Tenant, update session now
                require_once BASE_PATH . '/app/models/User.php';
                $userModel = new User();
                $user = $userModel->findById($userId);
                if ($user && $user['role'] === 'Tenant') {
                    $_SESSION['role'] = 'Tenant';
                }
                
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
}
