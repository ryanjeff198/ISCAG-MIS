<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/helpers/Auth.php';

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        
        if ($_SESSION['role'] === 'Staff_Tenant') {
            header('Location: ' . url('/admin/apartment'));
            exit;
        }

        $this->view('admin/mis_admin/admin_dashboard', ['active_page' => 'admin_dashboard']);
    }

    public function apartment(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/ApartmentType.php';
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        
        $userModel = new User();
        $typeModel = new ApartmentType();
        $appModel = new ApartmentApp();
        
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $units = $typeModel->getAllUnits();
        $applications = $appModel->getAllApplications();
        
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_dashboard', [
            'dbUser' => $dbUser,
            'units' => $units,
            'applications' => $applications
        ]);
    }

    public function apartmentInfo(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartments_info', ['dbUser' => $dbUser]);
    }

    public function apartmentProfile(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        $extra = $userModel->getAdditionalInfo($_SESSION['user_id']);
        
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_profile', [
            'dbUser' => array_merge($user ?: [], $extra ?: [])
        ]);
    }

    public function apartmentNotifications(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_notification', ['dbUser' => $dbUser]);
    }

    public function apartmentSoa(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/statement_of_account', ['dbUser' => $dbUser]);
    }

    public function tenantInfo(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/ApartmentApp.php';

        $userModel = new User();
        $appModel = new ApartmentApp();

        $dbUser = $userModel->findById($_SESSION['user_id']);

        // Get all tenant accounts
        $db = getDbConnection();
        $stmt = $db->query("SELECT tenant_id, first_name, last_name, email, contactnum, role FROM tenant_accounts ORDER BY tenant_id DESC");
        $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Get all applications (with addinfo data)
        $applications = $appModel->getAllApplications();

        $this->view('admin/Staff_Admin/Admin-Apartment_Department/tenant_info', [
            'dbUser' => $dbUser,
            'tenants' => $tenants,
            'applications' => $applications
        ]);
    }

    public function payment(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        require_once BASE_PATH . '/app/models/ApartmentType.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $appModel = new ApartmentApp();
        $typeModel = new ApartmentType();

        $applications = $appModel->getAllApplications();
        $approvedTenants = array_values(array_filter($applications, function($app) {
            $status = strtolower($app['status'] ?? '');
            return $status === 'approved' || $status === 'assigned' || $status === 'queued';
        }));

        $db = getDbConnection();
        $stmt = $db->query("SELECT tenant_id, first_name, last_name, email, role FROM tenant_accounts ORDER BY first_name ASC");
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $apartmentTypes = $typeModel->getAllTypes();

        $this->view('admin/Staff_Admin/Admin-Apartment_Department/payment', [
            'dbUser' => $dbUser,
            'approvedTenants' => $approvedTenants,
            'allUsers' => $allUsers,
            'apartmentTypes' => $apartmentTypes
        ]);
    }

    // MIS Admin Modules
    public function staffApartmentConfirmation(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        require_once BASE_PATH . '/app/models/User.php';
        $model = new ApartmentApp();
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $reports = $model->getAllApplications();
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_confirmation', [
            'active_page' => 'apartment_confirmation',
            'reports' => $reports,
            'dbUser' => $dbUser
        ]);
    }

    public function staffApproveApartmentApp(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id !== null && $id !== '') {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            require_once BASE_PATH . '/app/models/User.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            
            $appModel = new ApartmentApp();
            $notifModel = new Notification();
            
            // Use the Room Assignment & Waitlist Engine
            $result = $appModel->assignOrQueue((int) $id);
            
            $tenantId = $appModel->getTenantIdByApplicationId($id);
            
            if ($result['result'] === 'assigned') {
                $notifModel->create(
                    $tenantId,
                    'Room Assigned!',
                    'Congratulations! You have been assigned to Room ' . $result['room_number'] 
                    . ' in ' . $result['building'] . '. Your account has been upgraded to Tenant.',
                    'approval'
                );
            } elseif ($result['result'] === 'queued') {
                $appModel->updateApplicationStatus($id, 'Queued');
                $notifModel->create(
                    $tenantId,
                    'Application Accepted — Waitlisted',
                    'Your application has been verified and accepted, but all rooms of your requested type are currently full. '
                    . 'You are #' . $result['queue_position'] . ' in the waiting list. '
                    . 'You will be notified when a room becomes available.',
                    'info'
                );
            }
        }
        header('Location: ' . url('/admin/apartment/confirmation'));
    }

    public function staffRejectApartmentApp(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id !== null && $id !== '') {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateApplicationStatus($id, 'Rejected', $reason);
        }
        header('Location: ' . url('/admin/apartment/confirmation'));
    }

    public function apartmentRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/apartment_records', ['active_page' => 'apartment_records']);
    }

    public function apartmentConfirmation(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllApplications();
        $this->view('admin/mis_admin/admin_apartment_confirmation', [
            'active_page' => 'apartment_confirmation',
            'reports' => $reports
        ]);
    }

    public function parkingApproval(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllParkingApplications();
        $this->view('admin/mis_admin/admin_parking_approval', [
            'active_page' => 'parking_approval',
            'reports' => $reports
        ]);
    }

    public function approveParking(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Approved');
        }
        header('Location: ' . url('/admin/mis_admin/parking_approval'));
    }

    public function rejectParking(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Rejected', $reason);
        }
        header('Location: ' . url('/admin/mis_admin/parking_approval'));
    }

    // ── Staff Parking Approval ──
    public function staffParkingApproval(): void {
        Auth::protectRole(['Staff_Tenant', 'Admin']);
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        $reports = $model->getAllParkingApplications();
        $this->view('admin/Staff_Admin/Admin-Apartment_Department/parking_info', [
            'active_page' => 'parking_approval',
            'reports' => $reports
        ]);
    }

    public function staffApproveParking(): void {
        Auth::protectRole(['Staff_Tenant', 'Admin']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Approved');
        }
        header('Location: ' . url('/admin/apartment/parking'));
    }

    public function staffRejectParking(): void {
        Auth::protectRole(['Staff_Tenant', 'Admin']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Rejected', $reason);
        }
        header('Location: ' . url('/admin/apartment/parking'));
    }

    public function billing(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/billing_and_payment', ['active_page' => 'billing']);
    }

    public function soa(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/statement_of_account', ['active_page' => 'soa']);
    }

    public function reports(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $this->view('admin/mis_admin/admin_reports', ['active_page' => 'reports']);
    }

    public function daawahRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        $this->view('admin/mis_admin/daawah_records', ['active_page' => 'daawah_records']);
    }

    public function damayanRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        $this->view('admin/mis_admin/damayan_records', ['active_page' => 'damayan_records']);
    }

    public function notificationBroadcast(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/notification_broadcast', ['active_page' => 'notifications']);
    }

    public function userRecords(): void {
        Auth::protectRole(['Admin']);
        
        $db = getDbConnection();
        $sql = "SELECT a.tenant_id, a.first_name, a.last_name, a.email, a.role, a.contactnum, a.is_verified, a.sex as account_sex,
                       b.sex as addinfo_sex, b.birthdate, b.date_applied 
                FROM tenant_accounts a 
                LEFT JOIN tenant_addinfo b ON a.tenant_id = b.tenant_id 
                ORDER BY a.tenant_id DESC";
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/records', [
            'active_page' => 'records',
            'users' => $users
        ]);
    }

    public function toggleUserStatus(): void {
        Auth::protectRole(['Admin']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $id = $body['id'] ?? null;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            return;
        }

        $db = getDbConnection();
        $stmt = $db->prepare("SELECT is_verified FROM tenant_accounts WHERE tenant_id = :id");
        $stmt->execute(['id' => $id]);
        $verified = $stmt->fetchColumn();

        if ($verified === false) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        $newVerified = $verified == 1 ? 0 : 1;
        $upd = $db->prepare("UPDATE tenant_accounts SET is_verified = :v WHERE tenant_id = :id");
        if ($upd->execute(['v' => $newVerified, 'id' => $id])) {
            echo json_encode(['success' => true, 'newStatus' => $newVerified ? 'active' : 'inactive']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    }


    public function auditLogs(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/audit_logs', ['active_page' => 'audit_logs']);
    }

    public function notificationInbox(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        $this->view('admin/mis_admin/notification_inbox', ['active_page' => 'notification']);
    }

    public function serveTenantImage(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $userId = $_GET['uid'] ?? null;
        $type   = $_GET['type'] ?? '';

        if (!$userId) {
            http_response_code(400);
            echo 'Missing user ID';
            return;
        }

        $allowed = ['picture','valididfront','valididback','birthcert','nbi','proofofincome'];
        if (!in_array($type, $allowed)) {
            http_response_code(400);
            echo 'Invalid type';
            return;
        }

        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $model = new ApartmentApp();
        
        // Look up the tenant's addinfo record, then fetch the image
        $info = $model->getInfo($userId);
        if (empty($info)) {
            http_response_code(404);
            echo 'No application info found';
            return;
        }

        $infoId = $info['tenant_info'];
        $result = $model->getAddInfoImage($infoId, $type);

        if (!$result) {
            http_response_code(404);
            echo 'Image not found';
            return;
        }

        // Check filesystem first
        if (!empty($result['file_path'])) {
            $fullPath = BASE_PATH . "/public/" . $result['file_path'];
            if (file_exists($fullPath)) {
                header('Content-Type: ' . $result['mime']);
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                return;
            }
        }

        // Fallback to BLOB
        if (!empty($result['data'])) {
            header("Content-Type: " . $result['mime']);
            header("Content-Length: " . strlen($result['data']));
            echo $result['data'];
            return;
        }

        http_response_code(404);
        echo 'Image not found';
    }
}
