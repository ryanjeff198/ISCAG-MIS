<?php

require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/helpers/AuditLogger.php';

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        
        if ($_SESSION['role'] === 'Staff_Tenant') {
            header('Location: ' . url('/admin/apartment'));
            exit;
        }

        if ($_SESSION['role'] === 'Staff_Male' || $_SESSION['role'] === 'Staff_Female') {
            header('Location: ' . url('/admin/dawah'));
            exit;
        }

        if ($_SESSION['role'] === 'Staff_Damayan') {
            header('Location: ' . url('/admin/damayan'));
            exit;
        }

        // Load Real-time Data
        $db = getDbConnection();

        // ── KPI Data ──
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();
        $pendingApprovals = (int) $db->query("SELECT COUNT(*) FROM apartmentsapp WHERE status = 'Pending'")->fetchColumn();
        $auditFlags = (int) $db->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0")->fetchColumn();
        $totalApplications = (int) $db->query("SELECT COUNT(*) FROM apartmentsapp")->fetchColumn();
        $totalParking = (int) $db->query("SELECT COUNT(*) FROM tenant_parking")->fetchColumn();

        // ── Billing KPI Data (Real-Time Synchronized) ──
        $billingKPIs = $this->getBillingAggregateMetrics();
        $totalRevenue = $billingKPIs['totalRevenue'];
        $pendingBilling = $billingKPIs['pendingCount'];
        $overdueBilling = $billingKPIs['overdueCount'];
        $paidThisMonth = $billingKPIs['paidThisMonthCount'];
        $revenueGrowth = $billingKPIs['growth'];

        // ── Chart: System Activity (last 7 days) ──
        $stmtActivity = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM admin_notifications 
            GROUP BY DATE(created_at) 
            ORDER BY date DESC 
            LIMIT 7
        ");
        $actualActivity = $stmtActivity->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $activityData = array_reverse($actualActivity);

        // ── Chart: Application Status Distribution ──
        $distData = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Unit Occupancy ──
        $occupancyData = $db->query("SELECT status, COUNT(*) as count FROM apartment_units GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Gender Distribution ──
        $genderData = $db->query("SELECT COALESCE(sex,'Unknown') as gender, COUNT(*) as count FROM tenant_addinfo GROUP BY sex")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Maintenance Data ──
        $pendingMaintenance = (int) $db->query("SELECT COUNT(*) FROM tenant_maintenance WHERE status = 'Pending'")->fetchColumn();
        $recentMaintenance = $db->query("
            SELECT m.*, u.first_name, u.last_name 
            FROM tenant_maintenance m
            JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
            ORDER BY m.created_at DESC 
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Recent Activity ──
        $stmtLogs = $db->query("
            SELECT * FROM admin_notifications 
            ORDER BY created_at DESC 
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
            'totalRevenue' => $totalRevenue,
            'revenueGrowth' => $revenueGrowth,
            'pendingBilling' => $pendingBilling,
            'overdueBilling' => $overdueBilling,
            'paidThisMonth' => $paidThisMonth,
            'pendingMaintenance' => $pendingMaintenance,
            'recentMaintenance' => $recentMaintenance,
            'billingStats' => [
                ['status' => 'Paid', 'count' => $paidThisMonth, 'total' => $totalRevenue],
                ['status' => 'Pending', 'count' => $pendingBilling, 'total' => 0],
                ['status' => 'Overdue', 'count' => $overdueBilling, 'total' => 0]
            ],
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
        $totalNotifs = (int) $db->query("SELECT COUNT(*) FROM admin_notifications")->fetchColumn();

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

        // ── Billing KPI Data (Real-Time Synchronized) ──
        $billingKPIs = $this->getBillingAggregateMetrics();
        $totalRevenue = $billingKPIs['totalRevenue'];
        $pendingBilling = $billingKPIs['pendingCount'];
        $overdueBilling = $billingKPIs['overdueCount'];
        $paidThisMonth = $billingKPIs['paidThisMonthCount'];
        $billingDist = [
            ['status' => 'Paid', 'count' => $paidThisMonth, 'total' => $totalRevenue],
            ['status' => 'Pending', 'count' => $pendingBilling, 'total' => 0],
            ['status' => 'Overdue', 'count' => $overdueBilling, 'total' => 0]
        ];

        // ── Activity Timeline ──
        $activityTimeline = $db->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM admin_notifications GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 14")->fetchAll(PDO::FETCH_ASSOC) ?: [];
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

    public function apartmentAnalytics(): void
    {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $db = getDbConnection();
        
        // 1. Basic User Info
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        // 2. Application Status Distribution (Real-time)
        $appStatsQuery = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $appStats = ['Applied'=>0, 'Accepted'=>0, 'Rejected'=>0, 'Pending'=>0, 'Verified'=>0, 'Assigned'=>0];
        foreach ($appStatsQuery as $row) {
            $st = ucfirst($row['status']);
            if (isset($appStats[$st])) $appStats[$st] = (int)$row['count'];
        }

        // 3. Unit Occupancy (Real-time)
        $occStatsQuery = $db->query("SELECT status, COUNT(*) as count FROM apartment_units GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $occStats = ['Available'=>0, 'Occupied'=>0, 'Reserved'=>0];
        foreach ($occStatsQuery as $row) {
            $st = ucfirst(strtolower($row['status']));
            if (isset($occStats[$st])) $occStats[$st] = (int)$row['count'];
        }

        // 4. Monthly Application Trends
        $appTrend = $db->query("
            SELECT DATE_FORMAT(date, '%b %Y') as month, COUNT(*) as count 
            FROM apartmentsapp 
            WHERE date IS NOT NULL 
            GROUP BY month 
            ORDER BY MIN(date) ASC 
            LIMIT 12
        ")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 5. Billing & Revenue (Real-time from billing table)
        $revenueTrend = $db->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(amount) as total 
            FROM billing 
            WHERE status = 'Paid' 
            GROUP BY month 
            ORDER BY MIN(created_at) ASC 
            LIMIT 12
        ")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 6. Billing Status Summary
        $billingSummary = $db->query("SELECT status, SUM(amount) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 7. Unit Type Preferences (From applications)
        $typePrefs = $db->query("SELECT roomtype, COUNT(*) as count FROM apartmentsapp GROUP BY roomtype")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // 8. Building-wise Occupancy
        $buildingStats = $db->query("
            SELECT building, 
                   SUM(CASE WHEN status = 'Occupied' THEN 1 ELSE 0 END) as occupied,
                   SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available
            FROM apartment_units 
            WHERE building IS NOT NULL
            GROUP BY building
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/Staff_Admin/Admin-Apartment_Department/apartment_analytics', [
            'active_page' => 'apartment_analytics',
            'dbUser' => $dbUser,
            'appStats' => $appStats,
            'occStats' => $occStats,
            'appTrend' => $appTrend,
            'revenueTrend' => $revenueTrend,
            'billingSummary' => $billingSummary,
            'typePrefs' => $typePrefs,
            'buildingStats' => $buildingStats
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

    public function dawahNotifications(): void
    {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        // Determine department type based on role
        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/notifications'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/notifications';

        $this->view($viewPath, [
            'active_page' => 'notifications',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser
        ]);
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
            $this->logAudit('APARTMENT', 'APPROVE_APP', "Approved application ID: $id for Tenant ID: $tenantId");
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/confirmation' : '/admin/mis_admin/apartment_confirmation';
        header('Location: ' . url($redirect));
    }

    public function staffRejectApartmentApp(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id !== null && $id !== '') {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateApplicationStatus($id, 'Rejected', $reason);
            $tenantId = $model->getTenantIdByApplicationId($id);

            // Notify tenant about rejection
            require_once BASE_PATH . '/app/models/Notification.php';
            $notifModel = new Notification();
            $notifModel->create(
                $tenantId,
                'Application Status Update',
                'Your apartment application has been reviewed. Unfortunately, it was rejected. Reason: ' . htmlspecialchars($reason),
                'alert'
            );
            $this->logAudit('APARTMENT', 'REJECT_APP', "Rejected application ID: $id. Reason: $reason");
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/confirmation' : '/admin/mis_admin/apartment_confirmation';
        header('Location: ' . url($redirect));
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
            $this->logAudit('PARKING', 'APPROVE_PARKING', "Approved parking ID: $id");
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/parking' : '/admin/mis_admin/parking_approval';
        header('Location: ' . url($redirect));
    }

    public function rejectParking(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_GET['reason'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/ApartmentApp.php';
            $model = new ApartmentApp();
            $model->updateParkingStatus($id, 'Rejected', $reason);
            $this->logAudit('PARKING', 'REJECT_PARKING', "Rejected parking ID: $id. Reason: $reason");
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/parking' : '/admin/mis_admin/parking_approval';
        header('Location: ' . url($redirect));
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
            $this->logAudit('PARKING', 'APPROVE_PARKING', "Staff approved parking ID: $id");
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
            $this->logAudit('PARKING', 'REJECT_PARKING', "Staff rejected parking ID: $id. Reason: $reason");
        }
        header('Location: ' . url('/admin/apartment/parking'));
    }

    public function billing(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $db = getDbConnection();
        $now = clone (new \DateTime());

        // 1. Fetch Tenants with active/accepted leases
        $leases = $db->query("
            SELECT l.*, 
                   u.first_name, u.last_name, u.email,
                   a.roomtype, au.room_number, au.building
            FROM leases l
            JOIN tenant_accounts u ON l.tenant_id = u.tenant_id
            LEFT JOIN apartmentsapp a ON u.tenant_id = a.tenant_id AND (a.status = 'Assigned' OR a.status = 'Accepted')
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            WHERE l.lease_status IN ('Active', 'Accepted', 'Pending')
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 2. Fetch dependencies
        $payments = $db->query("SELECT payment_type, payment_status, tenant_id FROM payments WHERE payment_status = 'Paid'")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $parkingApps = $db->query("SELECT parking_id, tenant_id, datestarted, date FROM tenant_parking WHERE status = 'Approved'")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $memberCounts = $db->query("SELECT tenant_id, COUNT(*) as cnt FROM tenant_family_members GROUP BY tenant_id")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $memberMap = []; 
        foreach($memberCounts as $mc) $memberMap[$mc['tenant_id']] = (int)$mc['cnt'];

        // Group payments by tenant
        $tenantPayments = [];
        foreach ($payments as $p) {
            $tid = $p['tenant_id'];
            if (!isset($tenantPayments[$tid])) $tenantPayments[$tid] = [];
            $tenantPayments[$tid][] = strtolower($p['payment_type']);
        }

        // Group parking by tenant

        
        // Group parking by tenant
        $tenantParking = [];
        foreach ($parkingApps as $pa) {
            $tid = $pa['tenant_id'];
            if (!isset($tenantParking[$tid])) $tenantParking[$tid] = [];
            $tenantParking[$tid][] = $pa;
        }

        $invoices = [];
        $totalRevenue = 0; $pendingBilling = 0; $overdueBilling = 0; $paidCount = 0;

        // 3. Dynamic Timeline Engine (Run for all tenants)
        foreach ($leases as $lease) {
            $tid = $lease['tenant_id'];
            $paidKeys = $tenantPayments[$tid] ?? [];
            $myParkings = $tenantParking[$tid] ?? [];
            $occupants = ($memberMap[$tid] ?? 0) + 1; // +1 for the tenant
            
            // Advance Counts
            $advRent = 0; $advWater = 0; $advPark = 0; $advContrib = 0;
            foreach ($paidKeys as $pk) {
                if ($pk === 'rent-advance') $advRent++;
                if ($pk === 'water-advance') $advWater++;
                if ($pk === 'parking-advance') $advPark++;
                if ($pk === 'contribution-advance') $advContrib++;
            }

            $leaseStart = new \DateTime($lease['start_date']);
            $leaseEnd = $lease['end_date'] ? new \DateTime($lease['end_date']) : null;
            
            // Extend timeline if they have advance rent
            $simNow = clone $now;
            if ($advRent > 0) $simNow->modify("+$advRent month");
            $limitDate = ($leaseEnd && $leaseEnd < $simNow) ? $leaseEnd : $simNow;
            
            $currentDate = clone $leaseStart;
            $monthCount = 0;

            while ($currentDate <= $limitDate) {
                $monthKey = $currentDate->format('Y-m');

                // Month 0: Move-In Invoice (Deposit + Advance)
                if ($monthCount === 0) {
                    $invoiceAmount = (float)($lease['deposit_amount'] ?? 0) + (float)($lease['advance_amount'] ?? 0);
                    
                    if ($invoiceAmount > 0) {
                        // Check if paid in payments table
                        $depPaid = in_array('deposit', $paidKeys);
                        // Some systems might call it 'advance' or 'advance_rent'
                        $advPaid = in_array('advance', $paidKeys) || in_array('advance_rent', $paidKeys);
                        
                        // We assume it's fully paid if both elements exist, or if the tenant is active, they probably paid it. 
                        // But let's check precisely based on payments table:
                        $itemsPaidCount = ($depPaid ? 1 : 0) + ($advPaid ? 1 : 0);
                        $totItems = 2;

                        $status = 'Pending';
                        if ($itemsPaidCount >= $totItems || strtolower($lease['lease_status']) === 'active') {
                            // If they are Active, they are officially moved in and paid!
                            $status = 'Paid';
                            $totalRevenue += $invoiceAmount;
                            $paidCount++;
                        } else {
                            $dueDate = clone $leaseStart;
                            if ($now > $dueDate) {
                                $status = 'Overdue';
                                $overdueBilling++;
                            } else {
                                $status = 'Pending';
                                $pendingBilling++;
                            }
                        }

                        $invoiceIdNum = crc32($tid . 'movein') & 0x7FFFFFFF;
                        $invoices[] = [
                            'billing_id' => 'INIT-' . substr(str_pad($invoiceIdNum, 4, '0', STR_PAD_LEFT), 0, 4),
                            'tenant_id' => $tid,
                            'first_name' => $lease['first_name'],
                            'last_name' => $lease['last_name'],
                            'room_number' => $lease['room_number'],
                            'building' => $lease['building'],
                            'amount' => $invoiceAmount,
                            'due_date' => $leaseStart->format('Y-m-d'),
                            'status' => $status
                        ];
                    }
                }
                
                // Months 1+: Recurring Monthly Invoices (Rent + Water + Parking)
                if ($monthCount > 0) {
                    $monthSuffix = $currentDate->format('F Y');
                    $monthKey = $currentDate->format('Y-m');

                    $rentId = 'rent-' . $monthKey;
                    $waterId = 'water-' . $monthKey;
                    $contribId = 'contribution-' . $monthKey;
                    
                    $rentPaid = in_array($rentId, $paidKeys);
                    if (!$rentPaid && $advRent > 0) { $rentPaid = true; $advRent--; }
                    
                    $waterPaid = in_array($waterId, $paidKeys);
                    if (!$waterPaid && $advWater > 0) { $waterPaid = true; $advWater--; }

                    $contribPaid = in_array($contribId, $paidKeys);
                    if (!$contribPaid && $advContrib > 0) { $contribPaid = true; $advContrib--; }
                    
                    // Monthly Charge: Rent + Water + Contribution
                    $invoiceAmount = (float)$lease['monthly_rent'] + ($occupants * 100) + 150.00;
                    $itemsPaidCount = ($rentPaid ? 1 : 0) + ($waterPaid ? 1 : 0) + ($contribPaid ? 1 : 0);
                    $totItems = 3;

                    // Add Parking if applicable
                    foreach ($myParkings as $pa) {
                        $parkStart = new \DateTime($pa['datestarted'] ?: $pa['date']);
                        $parkStart->modify('first day of this month');
                        if ($currentDate >= $parkStart) {
                            $parkId = 'parking-' . $pa['parking_id'] . '-' . $monthKey;
                            $parkingPaid = in_array(strtolower($parkId), $paidKeys);
                            if (!$parkingPaid && $advPark > 0) { $parkingPaid = true; $advPark--; }
                            
                            $invoiceAmount += 1000.00;
                            $totItems++;
                            if ($parkingPaid) $itemsPaidCount++;
                        }
                    }

                    // Determine single invoice status
                    $status = 'Pending';
                    if ($itemsPaidCount === $totItems) {
                        $status = 'Paid';
                        $totalRevenue += $invoiceAmount;
                        $paidCount++;
                    } else {
                        // Not fully paid: Check if it's past the 5th
                        $dueDate = clone $currentDate; 
                        $dueDate->modify('first day of this month')->modify('+4 days'); // 5th of the month
                        
                        if ($now > $dueDate) {
                            $status = 'Overdue';
                            $overdueBilling++;
                        } else {
                            $status = 'Pending';
                            $pendingBilling++;
                        }
                    }

                    // Generate a consistent pseudo-ID for this invoice
                    $invoiceIdNum = crc32($tid . $monthKey) & 0x7FFFFFFF;

                    $invoices[] = [
                        'billing_id' => substr($invoiceIdNum, 0, 6) . $monthCount, // Make a nice 6-8 digit ID
                        'tenant_id' => $tid,
                        'first_name' => $lease['first_name'],
                        'last_name' => $lease['last_name'],
                        'room_number' => $lease['room_number'],
                        'building' => $lease['building'],
                        'amount' => $invoiceAmount,
                        'due_date' => $currentDate->format('Y-m-05'),
                        'status' => $status
                    ];
                } // End if > 0

                $currentDate->modify('+1 month');
                $monthCount++;
                if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
            }
        }
        
        // Sort newest invoices first
        usort($invoices, function($a, $b) {
            return strcmp($b['due_date'], $a['due_date']);
        });

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

        // 1. Fetch Tenants with room info
        $tenants = $db->query("
            SELECT 
                u.tenant_id, 
                u.first_name, 
                u.last_name, 
                u.contactnum, 
                u.email,
                au.room_number, 
                au.building,
                a.roomtype,
                a.application_id,
                a.status AS app_status
            FROM tenant_accounts u 
            LEFT JOIN apartmentsapp a ON u.tenant_id = a.tenant_id AND a.status = 'Assigned'
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            WHERE u.role IN ('Tenant', 'Guest')
            ORDER BY u.last_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 2. Fetch lease data per tenant
        $leases = $db->query("
            SELECT l.*, 
                   u.first_name, u.last_name
            FROM leases l
            JOIN tenant_accounts u ON l.tenant_id = u.tenant_id
            WHERE l.lease_status IN ('Active', 'Accepted', 'Expired')
            ORDER BY l.lease_id ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 3. Fetch all lease payments (Deposit & Advance)
        $payments = $db->query("
            SELECT p.*, l.tenant_id
            FROM payments p
            JOIN leases l ON p.lease_id = l.lease_id
            ORDER BY p.created_at ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 4. Fetch parking applications (Approved = ₱1,000 charge)
        $parkingApps = $db->query("
            SELECT parking_id, tenant_id, date, vehiclename, plateno, status, datestarted
            FROM tenant_parking
            WHERE status = 'Approved'
            ORDER BY date ASC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 5. Fetch family member counts per tenant (for water bill: ₱100/member)
        $memberCounts = $db->query("
            SELECT tenant_id, COUNT(*) as member_count
            FROM tenant_family_members
            GROUP BY tenant_id
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $memberMap = [];
        foreach ($memberCounts as $mc) {
            $memberMap[$mc['tenant_id']] = (int)$mc['member_count'];
        }

        // 6. Fetch existing billing records
        $billingRecords = $db->query("SELECT * FROM billing ORDER BY due_date ASC")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // ── Build unified transactions array ──
        $transactions = [];
        $now = new DateTime('now');
        // --- CATEGORIZE ADVANCES & CONSUMABLE PAYMENTS ---
        $advanceQueues = []; // [tenant_id][type] => [ payment1, payment2, ... ]
        $consumedPaymentIds = [];

        foreach ($payments as $p) {
            if ($p['payment_status'] === 'Paid') {
                $tid = $p['tenant_id'];
                $lowType = strtolower($p['payment_type']);
                // Normalize: bare 'advance' is really 'rent-advance'
                if ($lowType === 'advance') $lowType = 'rent-advance';
                if (strpos($lowType, 'advance') !== false || $lowType === 'deposit') {
                    if (!isset($advanceQueues[$tid])) $advanceQueues[$tid] = [];
                    if (!isset($advanceQueues[$tid][$lowType])) $advanceQueues[$tid][$lowType] = [];
                    $advanceQueues[$tid][$lowType][] = $p;
                }
            }
        }

        // A) Simulated Timeline: Monthly Rent, Water, Contribution, Parking
        foreach ($leases as $l) {
            $tid = $l['tenant_id'];
            $leaseStart = new DateTime($l['start_date']);
            $currentDate = clone $leaseStart;
            
            // Simulation logic
            $myNow = clone $now;
            $totalAdvances = 0;
            if (isset($advanceQueues[$tid]['rent-advance'])) $totalAdvances = count($advanceQueues[$tid]['rent-advance']);
            if ($totalAdvances > 0) $myNow->modify("+$totalAdvances month");
            
            $leaseEnd = $l['end_date'] ? new DateTime($l['end_date']) : null;
            $limitDate = ($leaseEnd && $leaseEnd < $myNow) ? $leaseEnd : $myNow;

            $monthCount = 0;
            while ($currentDate <= $limitDate) {
                $monthName = $currentDate->format('F Y');
                $simDate = $currentDate->format('Y-m-d');

                // Helper to apply advance if available
                $applyAdvance = function($type, $desc, $amt) use (&$advanceQueues, $tid, &$transactions, $simDate, &$consumedPaymentIds) {
                    if (!empty($advanceQueues[$tid][$type])) {
                        $p = array_shift($advanceQueues[$tid][$type]);
                        $consumedPaymentIds[] = $p['payment_id'];
                        // Generate Charge record for the advance element (if it's a deposit)
                        // Actually, Advance Rent charges already exist below. 
                        // We just generate the PAYMENT side here matching the cycle.
                        $transactions[] = [
                            'tenant_id'  => $tid,
                            'date'       => $simDate,
                            'type'       => ucfirst(str_replace('-',' ',$type)) . ' (Advance)',
                            'description'=> "Payment Applied from Advance — $desc",
                            'ref'        => 'ADV-' . str_pad($p['payment_id'], 4, '0', STR_PAD_LEFT),
                            'charge'     => 0,
                            'payment'    => (float)$amt,
                            'status'     => 'Paid'
                        ];
                        return true;
                    }
                    return false;
                };

                // 1. Initial Deposit/Advance (Month 0 only)
                if ($monthCount === 0) {
                   if ($l['deposit_amount'] > 0) {
                       $transactions[] = [
                           'tenant_id' => $tid, 'date' => $simDate, 'type' => 'Deposit',
                           'description' => 'Security Deposit', 'ref' => 'LSE-DEP-'.$l['lease_id'],
                           'charge' => (float)$l['deposit_amount'], 'payment' => 0, 'status' => 'Pending'
                       ];
                       $applyAdvance('deposit', 'Security Deposit', (float)$l['deposit_amount']);
                   }
                   if ($l['advance_amount'] > 0) {
                       $transactions[] = [
                           'tenant_id' => $tid, 'date' => $simDate, 'type' => 'Advance Rent',
                           'description' => 'Advance Rent (Month 1)', 'ref' => 'LSE-ADV-'.$l['lease_id'],
                           'charge' => (float)$l['advance_amount'], 'payment' => 0, 'status' => 'Pending'
                       ];
                       // Consume the very first advance-rent for month 1 charge (even though it's month 0 entry)
                       $applyAdvance('rent-advance', "Rent for $monthName", (float)$l['advance_amount']);
                   }
                }

                // 2. Regular Monthly Rent (Month 1+)
                if ($monthCount > 0) {
                    $rentAmt = (float)$l['monthly_rent'];
                    $transactions[] = [
                        'tenant_id'  => $tid, 'date' => $simDate, 'type' => 'Monthly Rent',
                        'description'=> "Monthly Rent — $monthName", 'ref' => 'LSE-R' . $l['lease_id'] . '-' . $currentDate->format('my'),
                        'charge'     => $rentAmt, 'payment' => 0, 'status' => 'Unpaid'
                    ];
                    $applyAdvance('rent-advance', "Rent for $monthName", $rentAmt);
                }

                // 3. Water Bill (All months including Month 0)
                $occupants = ($memberMap[$tid] ?? 0) + 1;
                $waterAmt = (float)($occupants * 100);
                $transactions[] = [
                    'tenant_id' => $tid, 'date' => $simDate, 'type' => 'Water',
                    'description' => "Water ($occupants occupants) — $monthName", 'ref' => 'LSE-W' . $l['lease_id'] . '-' . $currentDate->format('my'),
                    'charge' => $waterAmt, 'payment' => 0, 'status' => 'Unpaid'
                ];
                $applyAdvance('water-advance', "Water for $monthName", $waterAmt);

                // 4. Contribution (All months including Month 0)
                $transactions[] = [
                    'tenant_id' => $tid, 'date' => $simDate, 'type' => 'Contribution',
                    'description' => "Security & Garbage — $monthName", 'ref' => 'LSE-C' . $l['lease_id'] . '-' . $currentDate->format('my'),
                    'charge' => 150.00, 'payment' => 0, 'status' => 'Unpaid'
                ];
                $applyAdvance('contribution-advance', "Contribution for $monthName", 150.00);

                $currentDate->modify('+1 month');
                $monthCount++;
                if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
            }
            
            // 5. Independent loop for Parking Fees (Computing precise dates per tenant)
            foreach ($parkingApps as $pa) {
                if ($pa['tenant_id'] == $tid) {
                    $parkStart = new \DateTime($pa['datestarted'] ?: $pa['date']);
                    $parkStart->modify('+1 month'); // Align to 1 month after application
                    
                    $pDate = clone $parkStart;
                    while ($pDate <= $limitDate) {
                        $pSimDate = $pDate->format('Y-m-d');
                        $pMonthName = $pDate->format('F Y');
                        
                        $transactions[] = [
                            'tenant_id' => $tid, 'date' => $pSimDate, 'type' => 'Parking Fee',
                            'description' => 'Parking Fee — ' . ($pa['vehiclename'] ?: 'Vehicle') . ' — ' . $pMonthName,
                            'ref' => 'PKG-' . $pa['parking_id'] . '-' . $pDate->format('my'),
                            'charge' => 1000.00, 'payment' => 0, 'status' => 'Unpaid'
                        ];
                        
                        if (!empty($advanceQueues['parking-advance'])) {
                            $pArr = array_shift($advanceQueues['parking-advance']);
                            $consumedPaymentIds[] = $pArr['payment_id'];
                            $transactions[] = [
                                'tenant_id' => $tid, 'date' => $pSimDate, 'type' => 'Payment',
                                'description' => 'Payment Applied from Advance — Parking for ' . $pMonthName,
                                'ref' => $pArr['reference_number'] ?: 'ADV-' . str_pad($pArr['payment_id'], 4, '0', STR_PAD_LEFT),
                                'charge' => 0, 'payment' => 1000.00, 'status' => 'Paid'
                            ];
                        }
                        
                        $pDate->modify('+1 month');
                        if ($pDate > $limitDate && $pDate->format('mY') === $limitDate->format('mY')) break;
                    }
                }
            }
        }

        // B) Non-Consumed Payments (Direct payments made after move-in)
        foreach ($payments as $p) {
            if (in_array($p['payment_id'], $consumedPaymentIds)) continue;

            $isPaid = $p['payment_status'] === 'Paid';
            $chargeDate = date('Y-m-d', strtotime($p['created_at']));
            $payDate = $p['payment_date'] ? date('Y-m-d', strtotime($p['payment_date'])) : $chargeDate;

            $transactions[] = [
                'tenant_id'  => $p['tenant_id'],
                'date'       => $chargeDate,
                'type'       => $p['payment_type'],
                'description'=> $p['payment_type'],
                'ref'        => 'PMT-' . str_pad($p['payment_id'], 4, '0', STR_PAD_LEFT),
                'charge'     => 0, // Should be 0 for payment records (Charge is generated by simulation or manual invoice)
                'payment'    => 0,
                'status'     => $p['payment_status']
            ];
            if ($isPaid) {
                $transactions[] = [
                    'tenant_id'  => $p['tenant_id'],
                    'date'       => $payDate,
                    'type'       => $p['payment_type'] . ' (Payment)',
                    'description'=> 'Payment Received — ' . $p['payment_type'],
                    'ref'        => $p['reference_number'] ?: 'PAY-' . str_pad($p['payment_id'], 4, '0', STR_PAD_LEFT),
                    'charge'     => 0,
                    'payment'    => (float)$p['amount'],
                    'status'     => 'Paid'
                ];
            }
        }

        // D) Existing billing records (Manual Invoices)
        foreach ($billingRecords as $b) {
            $transactions[] = [
                'tenant_id'  => $b['tenant_id'],
                'date'       => $b['due_date'],
                'type'       => 'Rent Invoice',
                'description'=> 'Monthly Rent Invoice',
                'ref'        => 'INV-' . str_pad($b['billing_id'], 4, '0', STR_PAD_LEFT),
                'charge'     => (float)$b['amount'],
                'payment'    => 0,
                'status'     => $b['status']
            ];
            if ($b['status'] === 'Paid') {
                $transactions[] = [
                    'tenant_id'  => $b['tenant_id'],
                    'date'       => $b['due_date'],
                    'type'       => 'Rent Payment',
                    'description'=> 'Payment Received — Rent',
                    'ref'        => 'PAY-INV-' . str_pad($b['billing_id'], 4, '0', STR_PAD_LEFT),
                    'charge'     => 0,
                    'payment'    => (float)$b['amount'],
                    'status'     => 'Paid'
                ];
            }
        }

        // Global Sort
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        $this->view('admin/mis_admin/statement_of_account', [
            'active_page'  => 'soa',
            'tenants'      => $tenants,
            'transactions' => $transactions,
            'memberMap'    => $memberMap
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

    public function dawahRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        $this->view('admin/mis_admin/dawah_records', ['active_page' => 'dawah_records']);
    }

    public function dawahAdminDashboard(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        $role = $_SESSION['role'] ?? '';
        if ($role === 'Staff_Female') {
            header('Location: ' . url('/admin/dawah/female'));
            exit;
        } else {
            header('Location: ' . url('/admin/dawah/male'));
            exit;
        }
    }

    public function dawahMaleDashboard(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        
        // Sync session with live DB data
        if ($dbUser) {
            $_SESSION['name'] = trim($dbUser['first_name'] . ' ' . $dbUser['last_name']);
            $_SESSION['email'] = $dbUser['email'];
        }
        
        $counselingModel = new CounselingRequest();
        $rawRequests = $counselingModel->getByGender('male');
        $counselingAnalytics = $counselingModel->getAnalytics('male');

        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        $marriageModel = new MarriageRequest();
        $eduModel = new IslamicEducation();
        $marriageAll = $marriageModel->getAll();
        $eduAnalytics = $eduModel->getAnalytics('male');

        // Transform raw DB rows into the shape the dashboard JS expects
        $requests = array_map(function($r) {
            $statusMap = [
                'pending'  => ['label' => 'Pending',  'class' => 'pending'],
                'approved' => ['label' => 'Approved', 'class' => 'success'],
                'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
            ];
            $s = $statusMap[$r['status']] ?? ['label' => ucfirst($r['status']), 'class' => 'pending'];
            return [
                'id'            => $r['id'],
                'name'          => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
                'type'          => 'counseling',
                'service_label' => 'Counseling — ' . ($r['reason'] ?? 'General'),
                'date'          => date('M d, Y', strtotime($r['created_at'])),
                'status'        => $s['label'],
                'status_class'  => $s['class'],
            ];
        }, $rawRequests);

        $analytics = [
            'counseling_total' => $counselingAnalytics['total'] ?? 0,
            'counseling_pending' => $counselingAnalytics['pending'] ?? 0,
            'counseling_approved' => $counselingAnalytics['approved'] ?? 0,
            'marriage_total' => count($marriageAll),
            'student_count' => $eduAnalytics['total'] ?? 0,
            'student_active' => $eduAnalytics['active'] ?? 0,
            'student_completed' => $eduAnalytics['completed'] ?? 0,
            'pending' => ($counselingAnalytics['pending'] ?? 0) + ($eduAnalytics['pending'] ?? 0),
        ];

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/dawah_male_dashboard', [
            'active_page' => 'dashboard',
            'dbUser' => $dbUser,
            'requests' => $requests,
            'analytics' => $analytics
        ]);
    }

    public function dawahFemaleDashboard(): void {
        Auth::protectRole(['Admin', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        
        // Sync session with live DB data
        if ($dbUser) {
            $_SESSION['name'] = trim($dbUser['first_name'] . ' ' . $dbUser['last_name']);
            $_SESSION['email'] = $dbUser['email'];
        }
        
        $eduModel = new IslamicEducation();
        $rawStudents = $eduModel->getAllByGender('female');
        $eduAnalytics = $eduModel->getAnalytics('female');

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $counselingModel = new CounselingRequest();
        $counselingAnalytics = $counselingModel->getAnalytics('female');
        $counselingRequests = $counselingModel->getByGender('female');

        $students = $rawStudents;

        $analytics = [
            'total_students' => $eduAnalytics['total'] ?? 0,
            'active_students' => $eduAnalytics['active'] ?? 0,
            'completed' => $eduAnalytics['completed'] ?? 0,
            'pending' => ($eduAnalytics['pending'] ?? 0) + ($counselingAnalytics['pending'] ?? 0),
            'counseling_total' => $counselingAnalytics['total'] ?? 0,
            'counseling_approved' => $counselingAnalytics['approved'] ?? 0,
        ];

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/dawah_female_dashboard', [
            'active_page' => 'dashboard',
            'dbUser' => $dbUser,
            'students' => $students,
            'counseling' => $counselingRequests,
            'analytics' => $analytics
        ]);
    }

    public function dawahEducation(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        // Determine department type based on role
        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $eduModel = new IslamicEducation();
        $records = $eduModel->getAllByGender($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/female_education'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/male_education';

        $this->view($viewPath, [
            'active_page' => 'education',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    public function dawahCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $records = $counselingModel->getByGender($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/counseling'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/counseling';

        $this->view($viewPath, [
            'active_page' => 'counseling',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    public function dawahMarriage(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        // Marriage records placeholder — in production would use a MarriageModel
        $records = []; 

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/marriage'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/marriage';

        $this->view($viewPath, [
            'active_page' => 'marriage',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    public function dawahSchedule(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $marriageModel = new MarriageRequest();

        // 1. Fetch Counseling
        $counselingRaw = $counselingModel->getByGender($dawah_type);
        $counselingScheds = array_map(function($r) {
            return [
                'type' => 'Counseling',
                'name' => $r['first_name'] . ' ' . $r['last_name'],
                'age'  => $r['age'] ?? null,
                'date' => $r['preferred_date'],
                'time' => $r['preferred_time'],
                'desc' => 'Religious Guidance — ' . ($r['reason'] ?? 'General'),
                'status' => $r['status']
            ];
        }, $counselingRaw);

        // 2. Fetch Marriage
        $marriageRaw = $marriageModel->getAll();
        $marriageScheds = array_map(function($r) {
            return [
                'type' => 'Marriage Ceremony',
                'name' => $r['groom_name'] . ' & ' . $r['bride_name'],
                'age'  => $r['age'] ?? null,
                'date' => $r['marriage_date'],
                'time' => $r['marriage_time'],
                'desc' => 'Venue: ' . $r['marriage_venue'],
                'status' => $r['status']
            ];
        }, $marriageRaw);

        // 3. Fetch Manual Assignments (Classes/Seminars)
        require_once BASE_PATH . '/app/models/DawahSchedule.php';
        $manualModel = new DawahSchedule();
        $manualRaw = $manualModel->getByDepartment($dawah_type);
        $manualScheds = array_map(function($r) {
            return [
                'type' => $r['event_type'],
                'name' => $r['title'],
                'date' => $r['event_date'],
                'time' => $r['event_time'],
                'desc' => $r['description'],
                'status' => 'approved' // Manual assignments are pre-approved
            ];
        }, $manualRaw);

        // 4. Consolidate & Sort by Date/Time
        $schedules = array_merge($counselingScheds, $marriageScheds, $manualScheds);
        usort($schedules, function($a, $b) {
            $dateA = strtotime($a['date'] . ' ' . $a['time']);
            $dateB = strtotime($b['date'] . ' ' . $b['time']);
            return $dateA <=> $dateB;
        });

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/schedule'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/schedule';

        require_once BASE_PATH . '/app/models/DawahAvailability.php';
        $availModel = new DawahAvailability();
        $blockedDates = $availModel->getBlockedDates($dawah_type);

        $this->view($viewPath, [
            'active_page' => 'schedule',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'schedules' => $schedules,
            'blockedDates' => $blockedDates
        ]);
    }

    public function dawahMaleProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/profile', [
            'active_page' => 'profile',
            'dbUser' => $dbUser
        ]);
    }

    public function dawahFemaleProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/profile', [
            'active_page' => 'profile',
            'dbUser' => $dbUser
        ]);
    }

    public function assignDawahSchedule(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $role = $_SESSION['role'] ?? '';
                $dept = ($role === 'Staff_Female') ? 'female' : 'male';
                
                $data = [
                    'title' => $input['title'] ?? '',
                    'description' => $input['description'] ?? '',
                    'event_date' => $input['date'] ?? '',
                    'event_time' => $input['time'] ?? '',
                    'event_type' => $input['type'] ?? 'Class',
                    'department' => $dept
                ];

                require_once BASE_PATH . '/app/models/DawahSchedule.php';
                $model = new DawahSchedule();
                
                $success = $model->create($data);
                echo json_encode(['success' => $success]);
            } catch (Exception $e) {
                error_log("Assign Schedule Error: " . $e->getMessage());
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function toggleDawahAvailability(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $date = $input['date'] ?? '';
                $status = $input['status'] ?? '';
                $reason = $input['reason'] ?? 'Administrator Blockout';
                
                $role = $_SESSION['role'] ?? '';
                $dept = ($role === 'Staff_Female') ? 'female' : 'male';

                require_once BASE_PATH . '/app/models/DawahAvailability.php';
                $model = new DawahAvailability();
                
                $success = ($status === 'block') 
                    ? $model->blockDate($date, $dept, $reason)
                    : $model->unblockDate($date, $dept);

                echo json_encode(['success' => $success]);
            } catch (Exception $e) {
                error_log("Toggle Availability Error: " . $e->getMessage());
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function approveCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            
            require_once BASE_PATH . '/app/models/CounselingRequest.php';
            $model = new CounselingRequest();
            $success = $model->updateStatus($id, 'approved');

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    public function rejectCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            
            require_once BASE_PATH . '/app/models/CounselingRequest.php';
            $model = new CounselingRequest();
            $success = $model->updateStatus($id, 'rejected');

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    public function dawahAnalytics(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $eduModel = new IslamicEducation();
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        $marriageModel = new MarriageRequest();

        // 1. Counseling Stats
        $counselingAnalytics = $counselingModel->getAnalytics($dawah_type);
        $counselingRequests = $counselingModel->getByGender($dawah_type);
        
        // 2. Education Stats
        $educationAnalytics = $eduModel->getAnalytics($dawah_type);
        
        // 3. Marriage Stats
        $marriageStats = $marriageModel->getAnalytics();

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/analytics'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/analytics';

        $this->view($viewPath, [
            'active_page' => 'analytics',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'counseling' => $counselingAnalytics,
            'education' => $educationAnalytics,
            'marriage' => $marriageStats,
            'requests' => $counselingRequests
        ]);
    }

    public function damayanAdminDashboard(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        
        $burialModel = new BurialRequest();
        $burialAnalytics = $burialModel->getAnalytics();
        $allBurials = $burialModel->getAll();

        // Sync session with live DB data
        if ($dbUser) {
            $_SESSION['name'] = trim($dbUser['first_name'] . ' ' . $dbUser['last_name']);
            $_SESSION['email'] = $dbUser['email'];
        }

        $analytics = [
            'burial' => $burialAnalytics['total'],
            'charity' => 0, // Charity module still placeholder
            'completed' => $burialAnalytics['completed'],
            'pending' => $burialAnalytics['pending']
        ];

        // Format burial records for the dashboard table
        $records = [];
        foreach(array_slice($allBurials, 0, 10) as $b) {
            $records[] = [
                'id' => $b['ref_id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'type' => 'burial',
                'service_label' => 'Burial Service',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'status' => ucfirst($b['status']),
                'status_class' => $this->getBurialStatusClass($b['status'])
            ];
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/damayan_dashboard', [
            'active_page' => 'dashboard',
            'dbUser' => $dbUser,
            'records' => $records,
            'analytics' => $analytics
        ]);
    }

    public function damayanRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        $this->view('admin/mis_admin/damayan_records', ['active_page' => 'damayan_records']);
    }

    public function damayanProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('admin/Staff_Admin/Admin-Damayan_Department/profile', [
            'active_page' => 'profile',
            'dbUser' => $dbUser
        ]);
    }

    public function damayanBurial(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/BurialRequest.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $burialModel = new BurialRequest();
        $rawBurials = $burialModel->getAll();

        $records = [];
        foreach($rawBurials as $b) {
            $records[] = [
                'id' => $b['ref_id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'status' => ucfirst($b['status']),
                'status_class' => $this->getBurialStatusClass($b['status'])
            ];
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/burial', [
            'active_page' => 'burial',
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    public function damayanBurialRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/BurialRequest.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $burialModel = new BurialRequest();
        $rawBurials = $burialModel->getAll();

        $records = [];
        foreach($rawBurials as $b) {
            $records[] = [
                'id' => $b['ref_id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'status' => ucfirst($b['status']),
                'status_class' => $this->getBurialStatusClass($b['status'])
            ];
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/burial_requests', [
            'active_page' => 'burial_requests',
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    private function getBurialStatusClass(string $status): string {
        $status = strtolower($status);
        if ($status === 'pending') return 'badge-pending';
        if ($status === 'arrived') return 'badge-info';
        if ($status === 'completed') return 'badge-active';
        return 'badge-approved'; // For 'Approved' or 'Verified'
    }

    public function damayanCharity(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityDonation.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $donationModel = new CharityDonation();
        $stats = $donationModel->getStats();
        $recentRequests = $donationModel->getRecentRequests();
        
        // Fetch all donations to pass to the view for modal rendering
        // In a real app, you might fetch these via AJAX when the card is clicked, 
        // but for now we'll pass them all if the dataset is small.
        $allDonations = [];
        $programs = [1, 2, 3]; // Our known program IDs
        foreach($programs as $pid) {
            $allDonations[$pid] = $donationModel->getByProgram($pid);
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity', [
            'active_page' => 'charity',
            'dbUser' => $dbUser,
            'stats' => $stats,
            'requests' => $recentRequests,
            'donations' => $allDonations
        ]);
    }

    public function damayanNotifications(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $this->view('admin/Staff_Admin/Admin-Damayan_Department/notifications', [
            'active_page' => 'notifications',
            'dbUser' => $dbUser
        ]);
    }

    public function updateBurialStatus(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? null;
            $status = $input['status'] ?? null;
            
            require_once BASE_PATH . '/app/models/BurialRequest.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            
            $model = new BurialRequest();
            $notifModel = new Notification();
            
            $burial = $model->findByRefId($id);
            $success = $model->updateStatus($id, $status);

            if ($success && $burial && isset($burial['tenant_id'])) {
                $title = "Burial Service Update";
                $msg = "";
                
                if ($status === 'arrived') {
                    $msg = "Peace be upon you. We would like to inform you that the deceased (" . ($burial['deceased_name'] ?? 'your loved one') . ") has been respectfully received at the Damayan facility. We are now proceeding with the necessary burial preparations.";
                } else {
                    $msg = "Your burial service request (#{$id}) for " . ($burial['deceased_name'] ?? 'your loved one') . " has been " . strtoupper($status) . ".";
                }
                
                $notifModel->create((int)$burial['tenant_id'], $title, $msg, 'burial');
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }

    public function damayanAnalytics(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        require_once BASE_PATH . '/app/models/CharityDonation.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        
        $burialModel = new BurialRequest();
        $charityModel = new CharityDonation();
        
        $burialStats = $burialModel->getAnalytics();
        $charityStats = $charityModel->getAnalytics();
        $allBurials = $burialModel->getAll();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/analytics', [
            'active_page' => 'analytics',
            'dbUser' => $dbUser,
            'burial' => $burialStats,
            'charity' => $charityStats,
            'requests' => $allBurials
        ]);
    }

    public function renewalRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        $renewalModel = new LeaseRenewal();
        
        $db = getDbConnection();
        
        // 1. Fetch Stats
        $activeLeases = $db->query("SELECT COUNT(*) FROM leases WHERE lease_status IN ('Accepted', 'Active')")->fetchColumn();
        $pendingRenewals = $db->query("SELECT COUNT(*) FROM lease_renewals WHERE status = 'Pending'")->fetchColumn();
        
        // 2. Fetch Detailed Records
        $renewals = $renewalModel->getAllRenewals();
        
        $this->view('admin/mis_admin/renewal_records', [
            'active_page' => 'renewal_records',
            'renewals' => $renewals,
            'stats' => [
                'activeLeases' => (int)$activeLeases,
                'pendingRenewals' => (int)$pendingRenewals
            ]
        ]);
    }

    public function notificationBroadcast(): void {
        Auth::protectRole(['Admin']);
        $this->view('admin/mis_admin/notification_broadcast', ['active_page' => 'notifications']);
    }

    public function getBroadcastUsers(): void {
        Auth::protectRole(['Admin']);
        header('Content-Type: application/json');
        $db = getDbConnection();
        // Fetch all verified users for targeting
        $users = $db->query("SELECT tenant_id, first_name, last_name, role FROM tenant_accounts WHERE is_verified = 1 ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }

    public function processBroadcast(): void {
        Auth::protectRole(['Admin']);
        header('Content-Type: application/json');
        
        $body = json_decode(file_get_contents('php://input'), true);
        $audience = $body['audience'] ?? '';
        $specificUser = $body['specificUser'] ?? null;
        $title = $body['title'] ?? '';
        $message = $body['message'] ?? '';
        $type = $body['type'] ?? 'system';

        if (empty($audience) || empty($title) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }

        $db = getDbConnection();
        $targetIds = [];

        if ($audience === 'SPECIFIC' && $specificUser) {
            $targetIds[] = $specificUser;
        } elseif ($audience === 'ALL') {
            $targetIds = $db->query("SELECT tenant_id FROM tenant_accounts WHERE is_verified = 1")->fetchAll(PDO::FETCH_COLUMN);
        } elseif ($audience === 'APARTMENT') {
            $targetIds = $db->query("SELECT tenant_id FROM leases WHERE lease_status IN ('Active', 'Accepted')")->fetchAll(PDO::FETCH_COLUMN);
        }

        // De-duplicate
        $targetIds = array_unique($targetIds);

        if (empty($targetIds)) {
            echo json_encode(['success' => false, 'message' => 'No target users found for this audience']);
            return;
        }

        require_once BASE_PATH . '/app/models/Notification.php';
        $notifModel = new Notification();

        $successCount = 0;
        foreach ($targetIds as $tid) {
            if ($notifModel->create($tid, $title, $message, $type)) {
                $successCount++;
            }
        }

        // Log the broadcast
        $stmt = $db->prepare("INSERT INTO broadcasts (title, message, target_group, type, sender_id) VALUES (:t, :m, :tg, :ty, :sid)");
        $stmt->execute([
            't' => $title,
            'm' => $message,
            'tg' => $audience === 'SPECIFIC' ? "User ID: $specificUser" : $audience,
            'ty' => $type,
            'sid' => $_SESSION['user_id']
        ]);

        $this->logAudit('BROADCAST', 'SEND_NOTIFICATION', "Sent '$title' to $successCount users ($audience)");

        echo json_encode(['success' => true, 'count' => $successCount]);
    }

    public function getBroadcastHistory(): void {
        Auth::protectRole(['Admin']);
        header('Content-Type: application/json');
        $db = getDbConnection();
        $history = $db->query("SELECT * FROM broadcasts ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($history);
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
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $verified = $userData['is_verified'] ?? false;

        if ($verified === false) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }

        $newVerified = $verified == 1 ? 0 : 1;
        $upd = $db->prepare("UPDATE tenant_accounts SET is_verified = :v WHERE tenant_id = :id");
        if ($upd->execute(['v' => $newVerified, 'id' => $id])) {
            $statusText = $newVerified == 1 ? 'Activated' : 'Deactivated';
            $this->logAudit('GOVERNANCE', 'TOGGLE_USER', "$statusText user account ID: $id");
            echo json_encode(['success' => true, 'newStatus' => $newVerified == 1 ? 'active' : 'inactive']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    }


    public function auditLogs(): void {
        Auth::protectRole(['Admin']);
        $db = getDbConnection();
        // Join with tenant_accounts to get the current role, fallback to stored role for system/deleted users
        $query = "
            SELECT al.*, 
                   COALESCE(ta.role, al.admin_role, 'System') as display_role
            FROM audit_logs al
            LEFT JOIN tenant_accounts ta ON al.admin_id = ta.tenant_id
            ORDER BY al.timestamp DESC 
            LIMIT 1000
        ";
        $logs = $db->query($query)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $this->view('admin/mis_admin/audit_logs', [
            'active_page' => 'audit_logs',
            'logs' => $logs
        ]);
    }

    public function notificationInbox(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/AdminNotification.php';
        $model = new AdminNotification();
        $notifications = $model->getAll(100);
        $unreadCount = $model->getUnreadCount();

        $this->view('admin/mis_admin/notification_inbox', [
            'active_page'   => 'notification',
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount
        ]);
    }

    /**
     * AJAX: Mark a single admin notification as read.
     */
    public function markAdminNotifRead(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);
        $id = (int)($body['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false]); return; }

        require_once BASE_PATH . '/app/models/AdminNotification.php';
        $model = new AdminNotification();
        $ok = $model->markAsRead($id);
        echo json_encode(['success' => $ok, 'unread' => $model->getUnreadCount()]);
    }

    /**
     * AJAX: Mark ALL admin notifications as read.
     */
    public function markAllAdminNotifsRead(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant']);
        header('Content-Type: application/json');
        require_once BASE_PATH . '/app/models/AdminNotification.php';
        $model = new AdminNotification();
        $ok = $model->markAllAsRead();
        echo json_encode(['success' => $ok, 'unread' => 0]);
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
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/LeaseRenewal.php';
        require_once BASE_PATH . '/app/models/Lease.php';
        
        $userModel = new User();
        $renewalModel = new LeaseRenewal();
        $leaseModel = new Lease();
        
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $renewals = $renewalModel->getAllRenewals();
        $allLeases = $leaseModel->getAllLeases();
        
        $this->view('admin/Apartment/renewals', [
            'dbUser' => $dbUser,
            'renewals' => $renewals,
            'allLeases' => $allLeases
        ]);
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
                $this->logAudit('RENEWAL', 'APPROVE_RENEWAL', "Approved renewal ID: $id for Tenant ID: $tenantId");
            }
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/renewals' : '/admin/mis_admin/renewal_records';
        header('Location: ' . url($redirect));
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
                $this->logAudit('RENEWAL', 'REJECT_RENEWAL', "Rejected renewal ID: $id for Tenant ID: $tenantId");
            }
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/renewals' : '/admin/mis_admin/renewal_records';
        header('Location: ' . url($redirect));
    }
    /**
     * Private helper to run the financial simulation engine and return aggregated metrics
     */
    private function getBillingAggregateMetrics(): array {
        $db = getDbConnection();
        $now = new \DateTime('now');
        $thisMonthKey = $now->format('Y-m');
        $lastMonthKey = (clone $now)->modify('-1 month')->format('Y-m');

        // 1. Fetch Data (Same as billing controller)
        $leases = $db->query("
            SELECT l.* FROM leases l WHERE l.lease_status IN ('Active', 'Accepted', 'Pending')
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $payments = $db->query("SELECT payment_type, tenant_id FROM payments WHERE payment_status = 'Paid'")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $parkingApps = $db->query("SELECT parking_id, tenant_id, datestarted, date FROM tenant_parking WHERE status = 'Approved'")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $memberCounts = $db->query("SELECT tenant_id, COUNT(*) as cnt FROM tenant_family_members GROUP BY tenant_id")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $memberMap = []; foreach($memberCounts as $mc) $memberMap[$mc['tenant_id']] = (int)$mc['cnt'];
        $tenantPayments = [];
        foreach ($payments as $p) {
            $tid = $p['tenant_id'];
            if (!isset($tenantPayments[$tid])) $tenantPayments[$tid] = [];
            $tenantPayments[$tid][] = strtolower($p['payment_type']);
        }
        $tenantParking = [];
        foreach ($parkingApps as $pa) {
            $tid = $pa['tenant_id'];
            if (!isset($tenantParking[$tid])) $tenantParking[$tid] = [];
            $tenantParking[$tid][] = $pa;
        }

        $totalRevenue = 0; $thisMonthRev = 0; $lastMonthRev = 0;
        $pendingCount = 0; $overdueCount = 0; $paidThisMonthCount = 0;

        // 2. Run Simulation
        foreach ($leases as $lease) {
            $tid = $lease['tenant_id'];
            $paidKeys = $tenantPayments[$tid] ?? [];
            $myParkings = $tenantParking[$tid] ?? [];
            $occupants = ($memberMap[$tid] ?? 0) + 1;
            
            // Advance Counts
            $advRent = 0; $advWater = 0; $advPark = 0; $advContrib = 0;
            foreach ($paidKeys as $pk) {
                if ($pk === 'rent-advance') $advRent++;
                if ($pk === 'water-advance') $advWater++;
                if ($pk === 'parking-advance') $advPark++;
                if ($pk === 'contribution-advance') $advContrib++;
            }

            $leaseStart = new \DateTime($lease['start_date']);
            $leaseEnd = $lease['end_date'] ? (new \DateTime($lease['end_date'])) : null;
            
            // Timeline extension for advances
            $simLimit = clone $now;
            if ($advRent > 0) $simLimit->modify("+$advRent month");
            $limitDate = ($leaseEnd && $leaseEnd < $simLimit) ? $leaseEnd : $simLimit;

            $currentDate = clone $leaseStart;
            $monthCount = 0;

            while ($currentDate <= $limitDate) {
                $monthKey = $currentDate->format('Y-m');
                $isThisMonth = ($monthKey === $thisMonthKey);
                $isLastMonth = ($monthKey === $lastMonthKey);

                // Month 0 logic
                if ($monthCount === 0) {
                    $invoiceAmount = (float)($lease['deposit_amount'] ?? 0) + (float)($lease['advance_amount'] ?? 0);
                    if ($invoiceAmount > 0) {
                        $isPaid = in_array('deposit', $paidKeys) || strtolower($lease['lease_status']) === 'active';
                        if ($isPaid) {
                            $totalRevenue += $invoiceAmount;
                            if ($isThisMonth) $thisMonthRev += $invoiceAmount;
                            if ($isLastMonth) $lastMonthRev += $invoiceAmount;
                        } else {
                            if ($now > $leaseStart) $overdueCount++; else $pendingCount++;
                        }
                    }
                }
                
                // Months 1+ logic
                if ($monthCount > 0) {
                    $rentPaid = in_array('rent-' . $monthKey, $paidKeys);
                    if (!$rentPaid && $advRent > 0) { $rentPaid = true; $advRent--; }
                    
                    $waterPaid = in_array('water-' . $monthKey, $paidKeys);
                    if (!$waterPaid && $advWater > 0) { $waterPaid = true; $advWater--; }

                    $contribPaid = in_array('contribution-' . $monthKey, $paidKeys);
                    if (!$contribPaid && $advContrib > 0) { $contribPaid = true; $advContrib--; }
                    
                    // Monthly Charge: Rent + Water + Contribution
                    $invoiceAmount = (float)$lease['monthly_rent'] + ($occupants * 100) + 150.00;
                    $itemsPaidCount = ($rentPaid ? 1 : 0) + ($waterPaid ? 1 : 0) + ($contribPaid ? 1 : 0);
                    $totItems = 3;

                    // Add parking
                    foreach ($myParkings as $pa) {
                        $ps = new \DateTime($pa['datestarted'] ?: $pa['date']); $ps->modify('first day of this month');
                        if ($currentDate >= $ps) {
                            $parkId = 'parking-' . $pa['parking_id'] . '-' . $monthKey;
                            $parkingPaid = in_array(strtolower($parkId), $paidKeys);
                            if (!$parkingPaid && $advPark > 0) { $parkingPaid = true; $advPark--; }
                            
                            $invoiceAmount += 1000.00;
                            $totItems++;
                            if ($parkingPaid) $itemsPaidCount++;
                        }
                    }

                    if ($itemsPaidCount === $totItems) {
                        $totalRevenue += $invoiceAmount;
                        if ($isThisMonth) { $thisMonthRev += $invoiceAmount; $paidThisMonthCount++; }
                        if ($isLastMonth) { $lastMonthRev += $invoiceAmount; }
                    } else {
                        // Check if past 5th for overdue
                        $dueDate = clone $currentDate; 
                        $dueDate->modify('first day of this month')->modify('+4 days');
                        if ($now > $dueDate) $overdueCount++; else $pendingCount++;
                    }
                }

                $currentDate->modify('+1 month');
                $monthCount++;
                if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
            }
        }

        // 3. Calculate Growth
        $growth = 0;
        if ($lastMonthRev > 0) {
            $growth = (($thisMonthRev - $lastMonthRev) / $lastMonthRev) * 100;
        } elseif ($thisMonthRev > 0) {
            $growth = 100; 
        }

        return [
            'totalRevenue' => $totalRevenue,
            'thisMonthRev' => $thisMonthRev,
            'lastMonthRev' => $lastMonthRev,
            'growth' => round($growth, 1),
            'pendingCount' => $pendingCount,
            'overdueCount' => $overdueCount,
            'paidThisMonthCount' => $paidThisMonthCount
        ];
    }

    /**
     * Helper to log administrative actions for audit
     */
    private function logAudit(string $module, string $action, string $details): void {
        AuditLogger::log($module, $action, $details);
    }

    public function maintenanceRequests() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        require_once BASE_PATH . '/app/models/Maintenance.php';
        $model = new Maintenance();
        $requests = $model->getAll();
        
        $view = 'admin/mis_admin/maintenance_requests';
        if ($_SESSION['role'] === 'Staff_Tenant') {
            $view = 'admin/Staff_Admin/Admin-Apartment_Department/maintenance_dashboard';
        }
        
        $this->view($view, [
            'active_page' => 'maintenance',
            'requests' => $requests
        ]);
    }

    public function approveMaintenance() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/Maintenance.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new Maintenance();
            $notif = new Notification();
            
            $req = $model->getById($id);
            if ($model->updateStatus($id, 'In Progress', 'Your maintenance request has been seen and is now in progress.')) {
                // Sync: Set the tenant's room unit to "Maintenance" status
                $model->setUnitStatus((int)$req['tenant_id'], 'Maintenance');
                $this->logAudit('APARTMENT', 'UNIT_MAINTENANCE', "Room for Tenant ID {$req['tenant_id']} set to MAINTENANCE status due to maintenance request ID: $id");
                
                $notif->create($req['tenant_id'], 'Maintenance Update', 'Your maintenance request for ' . $req['category'] . ' is now In Progress.', 'approval');
                $this->logAudit('MAINTENANCE', 'APPROVE_MAINTENANCE', "Approved maintenance ID: $id");
            }
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/maintenance' : '/admin/mis_admin/maintenance';
        header('Location: ' . url($redirect));
    }
    public function rejectMaintenance() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        $reason = $_POST['reason'] ?? 'Not approved at this time.';
        if ($id) {
            require_once BASE_PATH . '/app/models/Maintenance.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new Maintenance();
            $notif = new Notification();
            
            $req = $model->getById($id);
            if ($model->updateStatus($id, 'Rejected', 'Your maintenance request was rejected. Reason: ' . $reason)) {
                $notif->create($req['tenant_id'], 'Maintenance Update', 'Your maintenance request for ' . $req['category'] . ' was rejected.', 'danger');
                $this->logAudit('MAINTENANCE', 'REJECT_MAINTENANCE', "Rejected maintenance ID: $id. Reason: $reason");
            }
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/maintenance' : '/admin/mis_admin/maintenance';
        header('Location: ' . url($redirect));
    }

    public function resolveMaintenance() {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once BASE_PATH . '/app/models/Maintenance.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new Maintenance();
            $notif = new Notification();
            
            $req = $model->getById($id);
            if ($model->updateStatus($id, 'Completed', 'Your maintenance request has been resolved/completed.')) {
                // Sync: Restore the tenant's room unit back to "Occupied"
                $model->setUnitStatus((int)$req['tenant_id'], 'Occupied');
                $this->logAudit('APARTMENT', 'UNIT_RESTORED', "Room for Tenant ID {$req['tenant_id']} restored to OCCUPIED status after maintenance ID: $id resolved");
                
                $notif->create($req['tenant_id'], 'Maintenance Resolved', 'Your maintenance request for ' . $req['category'] . ' has been marked as Completed.', 'success');
                $this->logAudit('MAINTENANCE', 'RESOLVE_MAINTENANCE', "Resolved maintenance ID: $id");
            }
        }
        $redirect = ($_SESSION['role'] === 'Staff_Tenant') ? '/admin/apartment/maintenance' : '/admin/mis_admin/maintenance';
        header('Location: ' . url($redirect));
    }

    public function damayanFinance(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityFinance.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $financeModel = new CharityFinance();
        $summary = $financeModel->getSummary();
        $liquidations = $financeModel->getAllLiquidations();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/finance', [
            'active_page' => 'finance',
            'dbUser' => $dbUser,
            'summary' => $summary,
            'liquidations' => $liquidations
        ]);
    }

    public function submitLiquidation(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            require_once BASE_PATH . '/app/models/CharityFinance.php';
            $model = new CharityFinance();
            $success = $model->createLiquidation($input);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }
    }
    /**
     * View Move-Out Requests (Staff/Admin)
     */
    public function moveoutRequests() {
        Auth::protectRole(['Staff_Tenant', 'Admin']);
        
        require_once BASE_PATH . '/app/models/MoveOut.php';
        require_once BASE_PATH . '/app/models/User.php';
        
        $model = new MoveOut();
        $userModel = new User();
        
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $requests = $model->getAllRequests();

        $this->view('admin/Staff_Admin/Admin-Apartment_Department/moveout_requests', [
            'requests' => $requests,
            'dbUser' => $dbUser
        ]);
    }

    public function processMoveOutAction() {
        Auth::protectRole(['Staff_Tenant', 'Admin']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $requestId = $input['request_id'] ?? null;
            $action = $input['action'] ?? ''; // 'start' or 'finalize'
            
            if (!$requestId) {
                echo json_encode(['success' => false, 'message' => 'Request ID missing']);
                exit;
            }

            require_once BASE_PATH . '/app/models/MoveOut.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            require_once BASE_PATH . '/app/models/Maintenance.php'; // Reusing setUnitStatus
            
            $model = new MoveOut();
            $notif = new Notification();
            $maint = new Maintenance();
            
            $req = $model->getRequestById($requestId);
            if (!$req) {
                echo json_encode(['success' => false, 'message' => 'Request not found']);
                exit;
            }

            if ($action === 'start') {
                if ($model->updateStatus($requestId, 'Processing')) {
                    $this->logAudit('APARTMENT', 'PROCESS_MOVEOUT_START', "Staff started processing Move-Out ID: $requestId for Tenant ID: {$req['tenant_id']}");
                    $notif->create($req['tenant_id'], 'Move-Out Update', 'Your move-out request is now being processed. An inspection will be scheduled soon.', 'info');
                    echo json_encode(['success' => true]);
                }
            } elseif ($action === 'finalize') {
                // Finalize settlement and vacate unit
                if ($model->finalizeSettlement($requestId, $input)) {
                    // Update Unit Status to 'Vacant'
                    $db = getDbConnection();
                    $stmt = $db->prepare("UPDATE apartment_units SET status = 'Available', tenant_id = NULL, application_id = NULL WHERE unit_id = ?");
                    $stmt->execute([$req['unit_id']]);

                    // Logical Archiving: End the Active Lease
                    $stmtLease = $db->prepare("UPDATE leases SET lease_status = 'Archived' WHERE tenant_id = ? AND lease_status IN ('Active', 'Wait_Advance')");
                    $stmtLease->execute([$req['tenant_id']]);

                    // Archive the old Application so it won't block future applications
                    $stmtApp = $db->prepare("UPDATE apartmentsapp SET status = 'Archived' WHERE tenant_id = ? AND status IN ('Assigned', 'Occupied', 'VERIFIED')");
                    $stmtApp->execute([$req['tenant_id']]);

                    // Role Downgrade: Turn Tenant back into Guest
                    $stmtRole = $db->prepare("UPDATE tenant_accounts SET role = 'Guest' WHERE tenant_id = ?");
                    $stmtRole->execute([$req['tenant_id']]);

                    $this->logAudit('APARTMENT', 'PROCESS_MOVEOUT_COMPLETE', "Completed move-out for Tenant ID: {$req['tenant_id']}. Unit ID: {$req['unit_id']} released. Tenant archived to Guest.");
                    
                    $msg = "Your move-out clearance is complete. Final refund: ₱" . number_format($input['refund'] ?? 0, 2) . ". Your account has been archived. Thank you for staying with us.";
                    $notif->create($req['tenant_id'], 'Move-Out Completed', $msg, 'success');
                    
                    echo json_encode(['success' => true]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            exit;
        }
    }

    public function getTenantBalance(): void {
        Auth::protectRole(['Admin', 'Staff_Tenant']);
        header('Content-Type: application/json');
        
        $tid = (int)($_GET['tid'] ?? 0);
        if (!$tid) {
            error_log("getTenantBalance: Missing TID");
            echo json_encode(['success' => false, 'balance' => 0, 'error' => 'Missing ID']);
            return;
        }

        try {
            $db = getDbConnection();
            $now = new \DateTime('now');
            error_log("getTenantBalance: Calculating for TID $tid as of " . $now->format('Y-m-d'));

        // Fetch Lease
        $stmt = $db->prepare("SELECT * FROM leases WHERE tenant_id = ? AND lease_status IN ('Active', 'Accepted') LIMIT 1");
        $stmt->execute([$tid]);
        $lease = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lease) {
            echo json_encode(['success' => true, 'balance' => 0]);
            return;
        }

        // Fetch Payments
        $stmt = $db->prepare("SELECT payment_type FROM payments WHERE tenant_id = ? AND payment_status = 'Paid'");
        $stmt->execute([$tid]);
        $paidKeys = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        $paidKeys = array_map('strtolower', $paidKeys);

        // Fetch Parking
        $stmt = $db->prepare("SELECT parking_id, datestarted, date FROM tenant_parking WHERE tenant_id = ? AND status = 'Approved'");
        $stmt->execute([$tid]);
        $myParkings = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Fetch Occupants
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenant_family_members WHERE tenant_id = ?");
        $stmt->execute([$tid]);
        $occupants = (int)$stmt->fetchColumn() + 1;

        $unpaidBalance = 0;
        
        // Advance Counts
        $advRent = 0; $advWater = 0; $advPark = 0; $advContrib = 0;
        foreach ($paidKeys as $pk) {
            if ($pk === 'rent-advance' || $pk === 'advance') $advRent++;
            if ($pk === 'water-advance') $advWater++;
            if ($pk === 'parking-advance') $advPark++;
            if ($pk === 'contribution-advance') $advContrib++;
        }

        $leaseStart = new \DateTime($lease['start_date']);
        $limitDate = clone $now;
        
        $currentDate = clone $leaseStart;
        $monthCount = 0;

        while ($currentDate <= $limitDate) {
            $monthKey = $currentDate->format('Y-m');
            
            // 1. Check for Recurring Rent
            $rentPaid = in_array('rent-' . $monthKey, $paidKeys);
            if (!$rentPaid && $advRent > 0) { $rentPaid = true; $advRent--; }
            
            // 2. Check for Utilities
            $waterPaid = in_array('water-' . $monthKey, $paidKeys);
            if (!$waterPaid && $advWater > 0) { $waterPaid = true; $advWater--; }
            
            $contribPaid = in_array('contribution-' . $monthKey, $paidKeys);
            if (!$contribPaid && $advContrib > 0) { $contribPaid = true; $advContrib--; }

            if ($monthCount === 0) {
                // Initial payments package logic
                $isInitialPaid = in_array('deposit', $paidKeys) || strtolower($lease['lease_status']) === 'active';
                if (!$isInitialPaid) {
                    $unpaidBalance += (float)($lease['deposit_amount'] ?? 0) + (float)($lease['advance_amount'] ?? 0);
                    // Also consider the utilities for the first month if they aren't part of the package
                    // Usually utilities aren't part of the initial package, so we add them if unpaid
                    if (!$waterPaid) $unpaidBalance += ($occupants * 100);
                    if (!$contribPaid) $unpaidBalance += 150.00;
                }
            } else {
                // Regular months
                if (!$rentPaid) $unpaidBalance += (float)$lease['monthly_rent'];
                if (!$waterPaid) $unpaidBalance += ($occupants * 100);
                if (!$contribPaid) $unpaidBalance += 150.00;

                // Parking
                foreach ($myParkings as $pa) {
                    $ps = new \DateTime($pa['datestarted'] ?: $pa['date']); $ps->modify('first day of this month');
                    if ($currentDate >= $ps) {
                        $parkId = 'parking-' . $pa['parking_id'] . '-' . $monthKey;
                        $parkingPaid = in_array(strtolower($parkId), $paidKeys);
                        if (!$parkingPaid && $advPark > 0) { $parkingPaid = true; $advPark--; }
                        if (!$parkingPaid) $unpaidBalance += 1000.00;
                    }
                }
            }
            $currentDate->modify('+1 month');
            $monthCount++;
            if ($currentDate > $limitDate && $currentDate->format('mY') === $limitDate->format('mY')) break;
        }

            error_log("getTenantBalance: TID $tid Final Balance: $unpaidBalance");
            echo json_encode(['success' => true, 'balance' => $unpaidBalance]);
        } catch (Exception $e) {
            error_log("getTenantBalance Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

