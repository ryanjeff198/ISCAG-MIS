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
            require_once BASE_PATH . '/app/models/Lease.php';
            require_once BASE_PATH . '/app/models/ApartmentType.php';
            
            $appModel = new ApartmentApp();
            $notifModel = new Notification();
            $leaseModel = new Lease();
            
            // 1. Remove Room Assignment from Approval Step
            // $result = $appModel->assignOrQueue((int) $id);
            // Instead, just update application status to 'Approved'
            $appModel->updateApplicationStatus((int) $id, 'Approved');
            
            $tenantId = $appModel->getTenantIdByApplicationId($id);

            // ── Auto-Generate Lease Contract ──
            // Fetch application to get roomtype
            $db = getDbConnection();
            $appStmt = $db->prepare("SELECT * FROM apartmentsapp WHERE application_id = :id");
            $appStmt->execute(['id' => $id]);
            $appData = $appStmt->fetch(PDO::FETCH_ASSOC);

            // Get rent from apartment_types if possible
            $monthlyRent = 0;
            if ($appData) {
                $typeModel = new ApartmentType();
                $types = $typeModel->getAllTypes();
                foreach ($types as $t) {
                    if (stripos($t['label'], $appData['roomtype']) !== false || $t['type_key'] === $appData['roomtype']) {
                        $monthlyRent = (float) ($t['price'] ?? 0);
                        break;
                    }
                }
            }

            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+12 months'));

            $leaseModel->createLease([
                'tenant_id'      => $tenantId,
                'application_id' => (int) $id,
                'unit_type'      => $appData['roomtype'] ?? null,
                'monthly_rent'   => $monthlyRent,
                'deposit_amount' => 1000, // Fixed 1000 deposit
                'advance_amount' => $monthlyRent, // 1 month advance
                'start_date'     => $startDate,
                'end_date'       => $endDate,
            ]);
            
            // Notify tenant that application is approved and lease is ready
            $notifModel->create(
                $tenantId,
                'Application Approved!',
                'Congratulations! Your apartment application has been approved. '
                . 'Please review and accept your Lease Contract to proceed to Initial Payments. '
                . 'A room will be assigned once payments are settled.',
                'approval'
            );
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
        $db = getDbConnection();

        // 1. Fetch Stats
        $billingStats = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        $totalRevenue = 0; $pendingBilling = 0; $overdueBilling = 0; $paidCount = 0;
        foreach ($billingStats as $stat) {
            if ($stat['status'] === 'Paid') {
                $totalRevenue = (float) $stat['total'];
                $paidCount = (int) $stat['count'];
            }
            if ($stat['status'] === 'Pending') $pendingBilling = (int) $stat['count'];
            if ($stat['status'] === 'Overdue') $overdueBilling = (int) $stat['count'];
        }

        // 2. Fetch Detailed Records
        $sql = "
            SELECT 
                b.*, 
                u.first_name, 
                u.last_name, 
                au.room_number, 
                au.building 
            FROM billing b 
            JOIN tenant_accounts u ON b.tenant_id = u.tenant_id 
            LEFT JOIN apartmentsapp a ON u.tenant_id = a.tenant_id AND (a.status = 'Assigned' OR a.status = 'Accepted')
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            ORDER BY b.created_at DESC
        ";
        $invoices = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/billing_and_payment', [
            'active_page' => 'billing',
            'invoices' => $invoices,
            'stats' => [
                'totalRevenue' => $totalRevenue,
                'pendingCount' => $pendingBilling,
                'overdueCount' => $overdueBilling,
                'paidCount' => $paidCount
            ]
        ]);
    }

    public function soa(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $db = getDbConnection();

        // 1. Fetch Tenants for selection
        $tenants = $db->query("
            SELECT 
                u.tenant_id, 
                u.first_name, 
                u.last_name, 
                u.contactnum, 
                au.room_number, 
                au.building 
            FROM tenant_accounts u 
            LEFT JOIN apartmentsapp a ON u.tenant_id = a.tenant_id AND (a.status = 'Assigned' OR a.status = 'Accepted')
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            WHERE u.role IN ('Tenant', 'Guest')
            ORDER BY u.last_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 2. Fetch all billing transactions
        $transactions = $db->query("SELECT * FROM billing ORDER BY due_date ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/statement_of_account', [
            'active_page' => 'soa',
            'tenants' => $tenants,
            'transactions' => $transactions
        ]);
    }

    public function reports(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $db = getDbConnection();

        // 1. Apartment Department Stats
        $aptStats = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalApts = 0;
        foreach($aptStats as $s) $totalApts += $s['count'];

        // 2. Billing / Finance Stats
        $billingStats = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalRevenue = 0;
        foreach($billingStats as $s) if($s['status'] === 'Paid') $totalRevenue = (float)$s['total'];

        // 3. User / Tenant Stats
        $userStats = $db->query("SELECT role, COUNT(*) as count FROM tenant_accounts GROUP BY role")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();

        // 4. Detailed Logs for the new design
        $burialLogs = $db->query("SELECT * FROM billing ORDER BY billing_id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $aptLeaseLogs = $db->query("
            SELECT a.*, u.first_name, u.last_name, au.room_number, au.building 
            FROM apartmentsapp a
            JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            ORDER BY a.application_id DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 5. Activity Logs (latest 50)
        $logs = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/admin_reports', [
            'active_page' => 'reports',
            'aptStats' => $aptStats,
            'totalApts' => $totalApts,
            'billingStats' => $billingStats,
            'totalRevenue' => $totalRevenue,
            'userStats' => $userStats,
            'totalUsers' => $totalUsers,
            'recentLogs' => $logs,
            'burialLogs' => $burialLogs,
            'aptLeaseLogs' => $aptLeaseLogs
        ]);
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
        // die('DEBUG: notificationInbox called');
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

    // ═══ CONTRACT RENEWALS ══════════════════════════════════

    public function renewals() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        $renewalModel = new LeaseRenewal();
        
        $renewals = $renewalModel->getAllRenewals();
        
        $this->view('admin/Apartment/renewals', ['renewals' => $renewals]);
    }

    public function approveRenewal() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/LeaseRenewal.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $renewalModel = new LeaseRenewal();
            $notifModel = new Notification();
            
            // Need the tenant ID and term to send correct notification
            $db = getDbConnection();
            $stmt = $db->prepare("SELECT tenant_id, requested_term_months FROM lease_renewals WHERE renewal_id = :id");
            $stmt->execute(['id' => $id]);
            $renData = $stmt->fetch(PDO::FETCH_ASSOC);
            $tenantId = $renData['tenant_id'] ?? null;
            $term = $renData['requested_term_months'] ?? 12;

            if ($renewalModel->approveRenewal((int)$id) && $tenantId) {
                $notifModel->create(
                    $tenantId,
                    'Contract Renewal Approved',
                    'Your lease contract has been successfully renewed and extended for another ' . $term . ' months.',
                    'approval'
                );
            }
        }
        header('Location: ' . url('/admin/apartment/renewals'));
    }

    public function rejectRenewal() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/LeaseRenewal.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $renewalModel = new LeaseRenewal();
            $notifModel = new Notification();

            $db = getDbConnection();
            $stmt = $db->prepare("SELECT tenant_id FROM lease_renewals WHERE renewal_id = :id");
            $stmt->execute(['id' => $id]);
            $tenantId = $stmt->fetchColumn();

            if ($renewalModel->rejectRenewal((int)$id) && $tenantId) {
                $notifModel->create(
                    $tenantId,
                    'Contract Renewal Rejected',
                    'Your recent lease renewal request was not approved. Please contact administration for details.',
                    'warning'
                );
            }
        }
        header('Location: ' . url('/admin/apartment/renewals'));
    }
}
