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

        // Load Real-time Data
        $db = getDbConnection();

        // ── KPI Data ──
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();
        $pendingApprovals = (int) $db->query("SELECT COUNT(*) FROM apartmentsapp WHERE status = 'Pending'")->fetchColumn();
        $auditFlags = (int) $db->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
        $totalApplications = (int) $db->query("SELECT COUNT(*) FROM apartmentsapp")->fetchColumn();
        $totalParking = (int) $db->query("SELECT COUNT(*) FROM tenant_parking")->fetchColumn();

        // ── Billing ──
        $db->exec("CREATE TABLE IF NOT EXISTS billing (
            billing_id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT,
            amount DECIMAL(10,2),
            status ENUM('Paid', 'Pending', 'Overdue') DEFAULT 'Pending',
            due_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $billingStats = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        if (empty($billingStats)) {
            $db->exec("INSERT INTO billing (tenant_id, amount, status, due_date) VALUES 
                (1, 3500.00, 'Paid', '2026-04-01'), (2, 7500.00, 'Pending', '2026-05-01'),
                (3, 5000.00, 'Overdue', '2026-03-15'), (1, 3500.00, 'Pending', '2026-05-01'),
                (2, 4000.00, 'Paid', '2026-03-01'), (3, 6000.00, 'Paid', '2026-02-01')
            ");
            $billingStats = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }
        $totalRevenue = 0; $pendingBilling = 0; $overdueBilling = 0;
        foreach ($billingStats as $stat) {
            if ($stat['status'] === 'Paid') $totalRevenue = (float) $stat['total'];
            if ($stat['status'] === 'Pending') $pendingBilling = (int) $stat['count'];
            if ($stat['status'] === 'Overdue') $overdueBilling = (int) $stat['count'];
        }

        // ── Chart: System Activity (last 7 days) ──
        $stmtActivity = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM notifications 
            GROUP BY DATE(created_at) 
            ORDER BY date DESC 
            LIMIT 7
        ");
        $activityData = array_reverse($stmtActivity->fetchAll(PDO::FETCH_ASSOC) ?: []);

        // ── Chart: Application Status Distribution ──
        $distData = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Unit Occupancy ──
        $occupancyData = $db->query("SELECT status, COUNT(*) as count FROM apartment_units GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Gender Distribution ──
        $genderData = $db->query("SELECT COALESCE(sex,'Unknown') as gender, COUNT(*) as count FROM tenant_addinfo GROUP BY sex")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Recent Activity ──
        $stmtLogs = $db->query("
            SELECT n.*, t.first_name, t.last_name, t.email 
            FROM notifications n
            LEFT JOIN tenant_accounts t ON n.tenant_id = t.tenant_id
            ORDER BY n.created_at DESC 
            LIMIT 8
        ");
        $recentLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/admin_dashboard', [
            'active_page' => 'admin_dashboard',
            'totalUsers' => $totalUsers,
            'pendingApprovals' => $pendingApprovals,
            'auditFlags' => $auditFlags,
            'totalApplications' => $totalApplications,
            'totalParking' => $totalParking,
            'billingStats' => $billingStats,
            'totalRevenue' => $totalRevenue,
            'pendingBilling' => $pendingBilling,
            'overdueBilling' => $overdueBilling,
            'activityData' => $activityData,
            'distData' => $distData,
            'occupancyData' => $occupancyData,
            'genderData' => $genderData,
            'recentLogs' => $recentLogs
        ]);
    }

    public function analytics(): void
    {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        $db = getDbConnection();

        // ── KPI Totals ──
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();
        $totalApps = (int) $db->query("SELECT COUNT(*) FROM apartmentsapp")->fetchColumn();
        $totalParking = (int) $db->query("SELECT COUNT(*) FROM tenant_parking")->fetchColumn();
        $totalNotifs = (int) $db->query("SELECT COUNT(*) FROM notifications")->fetchColumn();

        // ── User Growth (simulated monthly distribution) ──
        $userGrowth = [];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        for ($i = 0; $i < min(date('n'), 12); $i++) {
            $userGrowth[] = ['month' => $months[$i], 'count' => floor(($totalUsers / max(date('n'),1)) * (0.5 + ($i * 0.15)))];
        }

        // ── Module Distribution ──
        $moduleDist = [
            ['module' => 'Apartment', 'count' => $totalApps],
            ['module' => 'Parking', 'count' => $totalParking],
            ['module' => 'Notifications', 'count' => $totalNotifs],
            ['module' => 'Users', 'count' => $totalUsers]
        ];

        // ── Application Status ──
        $appStatusDist = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Parking Status ──
        $parkingStatusDist = $db->query("SELECT status, COUNT(*) as count FROM tenant_parking GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Gender Demographics ──
        $genderDist = $db->query("SELECT COALESCE(sex,'Unknown') as gender, COUNT(*) as count FROM tenant_addinfo GROUP BY sex")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Account Verification ──
        $statusDist = $db->query("SELECT IF(is_verified=1,'Verified','Unverified') as status, COUNT(*) as count FROM tenant_accounts GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Apartment Occupancy ──
        $occupancyDist = $db->query("SELECT status, COUNT(*) as count FROM apartment_units GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Billing ──
        $db->exec("CREATE TABLE IF NOT EXISTS billing (billing_id INT AUTO_INCREMENT PRIMARY KEY, tenant_id INT, amount DECIMAL(10,2), status ENUM('Paid','Pending','Overdue') DEFAULT 'Pending', due_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        $billingDist = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Activity Timeline ──
        $activityTimeline = $db->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM notifications GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 14")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $activityTimeline = array_reverse($activityTimeline);

        $this->view('admin/mis_admin/admin_analytics', [
            'active_page' => 'analytics',
            'totalUsers' => $totalUsers,
            'totalApps' => $totalApps,
            'totalParking' => $totalParking,
            'totalNotifs' => $totalNotifs,
            'userGrowth' => $userGrowth,
            'moduleDist' => $moduleDist,
            'appStatusDist' => $appStatusDist,
            'parkingStatusDist' => $parkingStatusDist,
            'genderDist' => $genderDist,
            'statusDist' => $statusDist,
            'occupancyDist' => $occupancyDist,
            'billingDist' => $billingDist,
            'activityTimeline' => $activityTimeline
        ]);
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
        $this->view('admin/mis_admin/records', ['active_page' => 'records']);
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

        header("Content-Type: " . $result['mime']);
        header("Content-Length: " . strlen($result['data']));
        header("Cache-Control: private, max-age=3600");
        echo $result['data'];
    }
}
