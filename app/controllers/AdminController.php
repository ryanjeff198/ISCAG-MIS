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

        // ── 1. Department Breakdown & Multi-Department Metrics ──
        $deptMetrics = [
            'apartments' => ['name' => 'Apartments Department', 'total' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0, 'occupancy' => 0, 'units' => 0],
            'dawah'      => ['name' => 'Da\'wah Department', 'total' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0, 'counseling' => 0, 'marriage' => 0, 'conversion' => 0],
            'education'  => ['name' => 'Islamic Education', 'total' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0, 'students' => 0, 'avg_progress' => 0, 'sessions' => 0],
            'damayan'    => ['name' => 'Damayan Department', 'total' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0, 'burial' => 0, 'funeral' => 0, 'donations_amount' => 0],
        ];

        // Apartments
        try {
            $apps = $db->query("SELECT status, COUNT(*) as c FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($apps as $st => $c) {
                $deptMetrics['apartments']['total'] += (int)$c;
                if (stripos($st, 'pend') !== false) $deptMetrics['apartments']['pending'] += (int)$c;
                elseif (stripos($st, 'approv') !== false || stripos($st, 'assign') !== false) $deptMetrics['apartments']['approved'] += (int)$c;
            }
        } catch (Throwable $e) {}

        try {
            $deptMetrics['apartments']['units'] = (int)$db->query("SELECT COUNT(*) FROM apartment_units")->fetchColumn();
            $deptMetrics['apartments']['occupancy'] = (int)$db->query("SELECT COUNT(*) FROM apartment_units WHERE status = 'Occupied'")->fetchColumn();
        } catch (Throwable $e) {}

        // Maintenance Data
        $pendingMaintenance = 0;
        $recentMaintenance = [];
        try {
            $pendingMaintenance = (int) $db->query("SELECT COUNT(*) FROM tenant_maintenance WHERE status = 'Pending'")->fetchColumn();
            $recentMaintenance = $db->query("
                SELECT m.*, u.first_name, u.last_name 
                FROM tenant_maintenance m
                JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                ORDER BY m.created_at DESC 
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            $deptMetrics['apartments']['total'] += (int)$db->query("SELECT COUNT(*) FROM tenant_maintenance")->fetchColumn();
            $deptMetrics['apartments']['pending'] += $pendingMaintenance;
        } catch (Throwable $e) {
            $pendingMaintenance = 0;
            $recentMaintenance = [];
        }

        // Da'wah
        try {
            $couns = $db->query("SELECT status, COUNT(*) as c FROM counseling_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($couns as $st => $c) {
                $deptMetrics['dawah']['total'] += (int)$c;
                $deptMetrics['dawah']['counseling'] += (int)$c;
                if (stripos($st, 'pend') !== false) $deptMetrics['dawah']['pending'] += (int)$c;
                elseif (stripos($st, 'complet') !== false) $deptMetrics['dawah']['completed'] += (int)$c;
                else $deptMetrics['dawah']['approved'] += (int)$c;
            }
            $marr = $db->query("SELECT status, COUNT(*) as c FROM marriage_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($marr as $st => $c) {
                $deptMetrics['dawah']['total'] += (int)$c;
                $deptMetrics['dawah']['marriage'] += (int)$c;
                if (stripos($st, 'pend') !== false) $deptMetrics['dawah']['pending'] += (int)$c;
                elseif (stripos($st, 'complet') !== false) $deptMetrics['dawah']['completed'] += (int)$c;
                else $deptMetrics['dawah']['approved'] += (int)$c;
            }
            $conv = $db->query("SELECT status, COUNT(*) as c FROM conversion_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($conv as $st => $c) {
                $deptMetrics['dawah']['total'] += (int)$c;
                $deptMetrics['dawah']['conversion'] += (int)$c;
                if (stripos($st, 'pend') !== false) $deptMetrics['dawah']['pending'] += (int)$c;
                elseif (stripos($st, 'complet') !== false) $deptMetrics['dawah']['completed'] += (int)$c;
                else $deptMetrics['dawah']['approved'] += (int)$c;
            }
        } catch (Throwable $e) {}

        // Islamic Education
        try {
            $edu = $db->query("SELECT status, COUNT(*) as c, AVG(COALESCE(progress_percent,0)) as avg_p FROM islamic_education_enrollments GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $totalP = 0; $totalStudents = 0;
            foreach ($edu as $row) {
                $c = (int)$row['c'];
                $deptMetrics['education']['total'] += $c;
                $deptMetrics['education']['students'] += $c;
                $totalP += ($row['avg_p'] * $c);
                $totalStudents += $c;
                if (stripos($row['status'], 'pend') !== false) $deptMetrics['education']['pending'] += $c;
                elseif (stripos($row['status'], 'complet') !== false) $deptMetrics['education']['completed'] += $c;
                else $deptMetrics['education']['approved'] += $c;
            }
            if ($totalStudents > 0) $deptMetrics['education']['avg_progress'] = round($totalP / $totalStudents, 1);
            $deptMetrics['education']['sessions'] = (int)$db->query("SELECT COUNT(*) FROM islamic_education_sessions WHERE is_attended = 1 OR is_completed = 1")->fetchColumn();
        } catch (Throwable $e) {}

        // Damayan
        try {
            $bur = $db->query("SELECT status, COUNT(*) as c FROM burial_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($bur as $st => $c) {
                $deptMetrics['damayan']['total'] += (int)$c;
                $deptMetrics['damayan']['burial'] += (int)$c;
                if (stripos($st, 'pend') !== false) $deptMetrics['damayan']['pending'] += (int)$c;
                elseif (stripos($st, 'complet') !== false) $deptMetrics['damayan']['completed'] += (int)$c;
                else $deptMetrics['damayan']['approved'] += (int)$c;
            }
            $fc = $db->query("SELECT status, COUNT(*) as c FROM funeral_cases GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
            foreach ($fc as $st => $c) {
                $deptMetrics['damayan']['total'] += (int)$c;
                $deptMetrics['damayan']['funeral'] += (int)$c;
                if (stripos($st, 'pend') !== false || stripos($st, 'submit') !== false) $deptMetrics['damayan']['pending'] += (int)$c;
                elseif (stripos($st, 'complet') !== false || stripos($st, 'releas') !== false) $deptMetrics['damayan']['completed'] += (int)$c;
                else $deptMetrics['damayan']['approved'] += (int)$c;
            }
            $deptMetrics['damayan']['donations_amount'] = (float)$db->query("SELECT COALESCE(SUM(amount), 0) FROM charity_donations_records WHERE status != 'rejected'")->fetchColumn();
        } catch (Throwable $e) {}

        // ── 2. Grand Totals Across All 4 Departments ──
        $grandTotalRequests = $deptMetrics['apartments']['total'] + $deptMetrics['dawah']['total'] + $deptMetrics['education']['total'] + $deptMetrics['damayan']['total'];
        $grandPendingRequests = $deptMetrics['apartments']['pending'] + $deptMetrics['dawah']['pending'] + $deptMetrics['education']['pending'] + $deptMetrics['damayan']['pending'];
        $grandApprovedRequests = $deptMetrics['apartments']['approved'] + $deptMetrics['dawah']['approved'] + $deptMetrics['education']['approved'] + $deptMetrics['damayan']['approved'];
        $grandCompletedRequests = $deptMetrics['apartments']['completed'] + $deptMetrics['dawah']['completed'] + $deptMetrics['education']['completed'] + $deptMetrics['damayan']['completed'];

        // Users & Notifications
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();
        $activeUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts WHERE is_verified = 1")->fetchColumn();
        $staffUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts WHERE role != 'Tenant'")->fetchColumn();
        $auditFlags = (int) $db->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0")->fetchColumn();
        $totalApplications = $deptMetrics['apartments']['total'];
        $pendingApprovals = $deptMetrics['apartments']['pending'];
        $totalParking = (int) $db->query("SELECT COUNT(*) FROM tenant_parking")->fetchColumn();

        // ── Billing KPI Data (Real-Time Synchronized) ──
        $billingKPIs = $this->getBillingAggregateMetrics();
        $totalRevenue = $billingKPIs['totalRevenue'];
        $pendingBilling = $billingKPIs['pendingCount'];
        $overdueBilling = $billingKPIs['overdueCount'];
        $paidThisMonth = $billingKPIs['paidThisMonthCount'];
        $revenueGrowth = $billingKPIs['growth'];

        // ── 3. Unified Multi-Department Recent Requests ──
        $unifiedRequests = [];
        try {
            $aptRows = $db->query("
                SELECT a.application_id as ref_id, 'Apartments' as department, 'Apartment Application' as request_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as applicant,
                       u.email, a.date as submitted_at, a.status,
                       '/admin/mis_admin/apartment_confirmation' as action_url
                FROM apartmentsapp a
                LEFT JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
                ORDER BY a.application_id DESC LIMIT 4
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $aptRows);
        } catch (Throwable $e) {}

        try {
            $counsRows = $db->query("
                SELECT c.id as ref_id, 'Da\'wah' as department, 'Counseling Consultation' as request_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as applicant,
                       u.email, c.created_at as submitted_at, c.status,
                       '/admin/dawah/counseling' as action_url
                FROM counseling_requests c
                LEFT JOIN tenant_accounts u ON c.tenant_id = u.tenant_id
                ORDER BY c.id DESC LIMIT 3
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $counsRows);
        } catch (Throwable $e) {}

        try {
            $marrRows = $db->query("
                SELECT m.id as ref_id, 'Da\'wah' as department, 'Marriage Reservation' as request_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as applicant,
                       u.email, m.created_at as submitted_at, m.status,
                       '/admin/dawah/marriage' as action_url
                FROM marriage_requests m
                LEFT JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                ORDER BY m.id DESC LIMIT 2
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $marrRows);
        } catch (Throwable $e) {}

        try {
            $convRows = $db->query("
                SELECT cv.id as ref_id, 'Da\'wah' as department, 'Shahadah Conversion' as request_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as applicant,
                       u.email, cv.created_at as submitted_at, cv.status,
                       '/admin/mis_admin/dawah_records' as action_url
                FROM conversion_requests cv
                LEFT JOIN tenant_accounts u ON cv.tenant_id = u.tenant_id
                ORDER BY cv.id DESC LIMIT 2
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $convRows);
        } catch (Throwable $e) {}

        try {
            $eduRows = $db->query("
                SELECT e.id as ref_id, 'Islamic Education' as department, CONCAT('Enrollment: ', COALESCE(e.level,'Course')) as request_type,
                       CONCAT(COALESCE(e.first_name,''), ' ', COALESCE(e.last_name,'')) as applicant,
                       e.contact_number as email, e.created_at as submitted_at, e.status,
                       '/admin/mis_admin/dawah_records' as action_url
                FROM islamic_education_enrollments e
                ORDER BY e.id DESC LIMIT 4
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $eduRows);
        } catch (Throwable $e) {}

        try {
            $burRows = $db->query("
                SELECT b.id as ref_id, 'Damayan' as department, 'Burial Service' as request_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as applicant,
                       u.email, b.submitted_at, b.status,
                       '/admin/mis_admin/damayan_records' as action_url
                FROM burial_requests b
                LEFT JOIN tenant_accounts u ON b.tenant_id = u.tenant_id
                ORDER BY b.id DESC LIMIT 3
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $burRows);
        } catch (Throwable $e) {}

        try {
            $fcRows = $db->query("
                SELECT f.id as ref_id, 'Damayan' as department, 'Funeral Case' as request_type,
                       f.informant_name as applicant,
                       f.informant_email as email, f.reported_at as submitted_at, f.status,
                       '/admin/mis_admin/damayan_records' as action_url
                FROM funeral_cases f
                ORDER BY f.id DESC LIMIT 3
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $unifiedRequests = array_merge($unifiedRequests, $fcRows);
        } catch (Throwable $e) {}

        usort($unifiedRequests, function($a, $b) {
            return strtotime($b['submitted_at'] ?? '1970-01-01') - strtotime($a['submitted_at'] ?? '1970-01-01');
        });
        $recentRequests = array_slice($unifiedRequests, 0, 8);

        // ── 4. Urgent / Pending Actions List ──
        $pendingActions = [];
        if ($deptMetrics['apartments']['pending'] > 0) {
            $pendingActions[] = [
                'type' => 'Apartments',
                'title' => 'Pending Apartment Applications',
                'count' => $deptMetrics['apartments']['pending'],
                'url' => url('/admin/mis_admin/apartment_confirmation'),
                'level' => 'warning'
            ];
        }
        if ($pendingMaintenance > 0) {
            $pendingActions[] = [
                'type' => 'Apartments',
                'title' => 'Pending Maintenance Requests',
                'count' => $pendingMaintenance,
                'url' => url('/admin/mis_admin/maintenance'),
                'level' => 'warning'
            ];
        }
        if ($deptMetrics['damayan']['pending'] > 0) {
            $pendingActions[] = [
                'type' => 'Damayan',
                'title' => 'Pending Burial / Funeral Intakes',
                'count' => $deptMetrics['damayan']['pending'],
                'url' => url('/admin/mis_admin/damayan_records'),
                'level' => 'danger'
            ];
        }
        if ($deptMetrics['education']['pending'] > 0) {
            $pendingActions[] = [
                'type' => 'Islamic Education',
                'title' => 'Pending Student Enrollments',
                'count' => $deptMetrics['education']['pending'],
                'url' => url('/admin/mis_admin/dawah_records'),
                'level' => 'info'
            ];
        }
        if ($deptMetrics['dawah']['pending'] > 0) {
            $pendingActions[] = [
                'type' => 'Da\'wah',
                'title' => 'Pending Counseling / Marriage Requests',
                'count' => $deptMetrics['dawah']['pending'],
                'url' => url('/admin/mis_admin/dawah_records'),
                'level' => 'info'
            ];
        }
        if ($overdueBilling > 0) {
            $pendingActions[] = [
                'type' => 'Finance',
                'title' => 'Overdue Invoices Requiring Follow-up',
                'count' => $overdueBilling,
                'url' => url('/admin/mis_admin/billing'),
                'level' => 'danger'
            ];
        }

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

        // ── Recent Activity / Audit Trail Snippet ──
        $recentLogs = [];
        try {
            $stmtLogs = $db->query("
                SELECT al.audit_id as id, al.admin_name as actor_name, al.module, al.action as title,
                       al.details, al.timestamp as created_at, 'audit' as type
                FROM audit_logs al
                ORDER BY al.timestamp DESC 
                LIMIT 8
            ");
            $recentLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {}

        if (empty($recentLogs)) {
            $stmtLogs = $db->query("
                SELECT * FROM admin_notifications 
                ORDER BY created_at DESC 
                LIMIT 8
            ");
            $recentLogs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        $this->view('admin/mis_admin/admin_dashboard', [
            'active_page' => 'admin_dashboard',
            'grandTotalRequests' => $grandTotalRequests,
            'grandPendingRequests' => $grandPendingRequests,
            'grandApprovedRequests' => $grandApprovedRequests,
            'grandCompletedRequests' => $grandCompletedRequests,
            'deptMetrics' => $deptMetrics,
            'recentRequests' => $recentRequests,
            'pendingActions' => $pendingActions,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'staffUsers' => $staffUsers,
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

        // ── SOA Logic for Selected Tenant (Powered by SoaHelper) ──
        $selectedTenantId = $_GET['tenant_id'] ?? null;
        $lease = null;
        $filteredTransactions = [];
        $balanceForwarded = 0;
        $filterMonth = 'all';
        $availableMonths = [];
        $occupants = 1;
        $selectedTenantInfo = null;
        $advanceCreditsTotal = 0;
        $advanceCreditsCount = 0;

        if ($selectedTenantId) {
            $selectedTenantId = (int)$selectedTenantId;

            // Get tenant name for display
            $stmt = $db->prepare("SELECT first_name, last_name FROM tenant_accounts WHERE tenant_id = ?");
            $stmt->execute([$selectedTenantId]);
            $selectedTenantInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            // Use the unified SoaHelper for perfect sync with tenant_soa.php
            require_once BASE_PATH . '/app/helpers/SoaHelper.php';
            $soaData = SoaHelper::buildTransactions($selectedTenantId);

            $lease               = $soaData['lease'];
            $occupants           = $soaData['occupants'];
            $allTransactions     = $soaData['transactions'];
            $availableMonths     = $soaData['availableMonths'];
            $advanceCreditsTotal = $soaData['advanceCreditsTotal'];
            $advanceCreditsCount = $soaData['advanceCreditsCount'];

            if ($lease) {
                // Default to 'all' so admin sees the full reconciled account
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
            }
        }

        $this->view('admin/Staff_Admin/Admin-Apartment_Department/payment', [
            'dbUser' => $dbUser,
            'approvedTenants' => $approvedTenants,
            'allUsers' => $allUsers,
            'apartmentTypes' => $apartmentTypes,
            // Pass SOA data (same structure as tenant_soa)
            'selectedTenantId' => $selectedTenantId,
            'selectedTenantInfo' => $selectedTenantInfo,
            'lease' => $lease,
            'transactions' => $filteredTransactions,
            'balanceForwarded' => $balanceForwarded,
            'filterMonth' => $filterMonth,
            'availableMonths' => $availableMonths,
            'occupants' => $occupants,
            'advanceCreditsTotal' => $advanceCreditsTotal,
            'advanceCreditsCount' => $advanceCreditsCount
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
            
            // Run the Room Assignment & Waitlist Engine
            $result = $appModel->assignOrQueue((int) $id);
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
            $isAssigned = (isset($result['result']) && $result['result'] === 'assigned');

            $leaseModel->createLease([
                'tenant_id'      => $tenantId,
                'application_id' => (int) $id,
                'unit_type'      => $appData['roomtype'] ?? null,
                'monthly_rent'   => $monthlyRent,
                'deposit_amount' => 1000, // Fixed 1000 deposit
                'advance_amount' => $monthlyRent, // 1 month advance
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'lease_status'   => $isAssigned ? 'Active' : 'Pending',
            ]);
            
            // Notify tenant
            if ($isAssigned) {
                $notifModel->create(
                    $tenantId,
                    'Room Assigned!',
                    'Congratulations! You have been assigned to Room ' . ($result['room_number'] ?? '') 
                    . ' in ' . ($result['building'] ?? '') . '. Your account has been upgraded to Tenant.',
                    'approval'
                );
            } elseif (isset($result['result']) && $result['result'] === 'queued') {
                $notifModel->create(
                    $tenantId,
                    'Application Accepted — Waitlisted',
                    'Your application has been verified and accepted, but all rooms of your requested type are currently full. '
                    . 'You are #' . ($result['queue_position'] ?? '1') . ' in the waiting list. '
                    . 'You will be notified when a room becomes available.',
                    'info'
                );
            } else {
                $notifModel->create(
                    $tenantId,
                    'Application Approved!',
                    'Congratulations! Your apartment application has been approved. Welcome to ISCAG Apartments!',
                    'approval'
                );
            }
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
        $nowGlobal = TimeSim::now(); // Admin's current perspective (Real Time)
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
            
            $tenantNow = TimeSim::now('now', $tid); 
            $myNow = clone $tenantNow;
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

        // ── 1. Department Summary Metrics ──
        // Apartment Department Stats
        $aptStats = $db->query("SELECT status, COUNT(*) as count FROM apartmentsapp GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalApts = 0;
        foreach($aptStats as $s) $totalApts += $s['count'];

        // Billing / Finance Stats
        $billingStats = $db->query("SELECT status, COUNT(*) as count, COALESCE(SUM(amount),0) as total FROM billing GROUP BY status")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalRevenue = 0;
        foreach($billingStats as $s) if($s['status'] === 'Paid') $totalRevenue = (float)$s['total'];

        // User / Tenant Stats
        $userStats = $db->query("SELECT role, COUNT(*) as count FROM tenant_accounts GROUP BY role")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $totalUsers = (int) $db->query("SELECT COUNT(*) FROM tenant_accounts")->fetchColumn();

        // Islamic Education Stats
        $eduEnrollments = $db->query("
            SELECT e.*, 
                   COALESCE(e.progress_percent, 0) as progress,
                   COALESCE(e.sessions_attended, 0) as attended,
                   COALESCE(e.total_sessions, 0) as total_s
            FROM islamic_education_enrollments e
            ORDER BY e.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
        
        $totalEduStudents = count($eduEnrollments);
        $avgEduProgress = 0;
        if ($totalEduStudents > 0) {
            $sumProgress = array_sum(array_column($eduEnrollments, 'progress'));
            $avgEduProgress = round($sumProgress / $totalEduStudents, 1);
        }
        $totalEduSessions = (int)$db->query("SELECT COUNT(*) FROM islamic_education_sessions WHERE is_attended = 1 OR is_completed = 1")->fetchColumn();

        // Damayan & Funeral Stats
        $burialRequests = $db->query("
            SELECT b.*, CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as tenant_name, u.email as tenant_email
            FROM burial_requests b
            LEFT JOIN tenant_accounts u ON b.tenant_id = u.tenant_id
            ORDER BY b.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $funeralCases = $db->query("
            SELECT f.* 
            FROM funeral_cases f 
            ORDER BY f.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $charityDonations = (float)$db->query("SELECT COALESCE(SUM(amount), 0) FROM charity_donations_records WHERE status != 'rejected'")->fetchColumn();

        // Da'wah Stats
        $counselingCount = (int)$db->query("SELECT COUNT(*) FROM counseling_requests")->fetchColumn();
        $marriageCount = (int)$db->query("SELECT COUNT(*) FROM marriage_requests")->fetchColumn();
        $conversionCount = (int)$db->query("SELECT COUNT(*) FROM conversion_requests")->fetchColumn();

        // ── 2. Unified Master Report Items ──
        $masterReportItems = [];

        // Apartments
        try {
            $aptRows = $db->query("
                SELECT a.application_id as id, 'Apartments' as department, 'Apartment Application' as service_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as subject_name,
                       u.email as contact, a.date as report_date, a.status,
                       CONCAT(COALESCE(au.building,''), '-', COALESCE(au.room_number,'Unit')) as detail_info
                FROM apartmentsapp a
                LEFT JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
                LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
                ORDER BY a.application_id DESC LIMIT 50
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $aptRows);
        } catch (Throwable $e) {}

        // Islamic Education
        try {
            $eduRows = $db->query("
                SELECT e.id, 'Islamic Education' as department, CONCAT('Enrollment: ', COALESCE(e.level,'Course')) as service_type,
                       CONCAT(COALESCE(e.first_name,''), ' ', COALESCE(e.last_name,'')) as subject_name,
                       e.contact_number as contact, DATE(e.created_at) as report_date, e.status,
                       CONCAT(COALESCE(e.progress_percent,0), '% Progress (', COALESCE(e.sessions_attended,0), '/', COALESCE(e.total_sessions,0), ' sessions)') as detail_info
                FROM islamic_education_enrollments e
                ORDER BY e.id DESC LIMIT 50
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $eduRows);
        } catch (Throwable $e) {}

        // Da'wah
        try {
            $counsRows = $db->query("
                SELECT c.id, 'Da\'wah' as department, CONCAT('Counseling (', COALESCE(c.reason, c.session_type, 'General'), ')') as service_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as subject_name,
                       u.email as contact, DATE(c.created_at) as report_date, c.status,
                       COALESCE(c.scheduled_at, 'Appointment') as detail_info
                FROM counseling_requests c
                LEFT JOIN tenant_accounts u ON c.tenant_id = u.tenant_id
                ORDER BY c.id DESC LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $counsRows);

            $marrRows = $db->query("
                SELECT m.id, 'Da\'wah' as department, 'Marriage Solemnization' as service_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as subject_name,
                       u.email as contact, DATE(m.created_at) as report_date, m.status,
                       CONCAT('Date: ', COALESCE(m.marriage_date,'TBD')) as detail_info
                FROM marriage_requests m
                LEFT JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
                ORDER BY m.id DESC LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $marrRows);

            $convRows = $db->query("
                SELECT cv.id, 'Da\'wah' as department, 'Shahadah Conversion' as service_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as subject_name,
                       u.email as contact, DATE(cv.created_at) as report_date, cv.status,
                       'Certificate Process' as detail_info
                FROM conversion_requests cv
                LEFT JOIN tenant_accounts u ON cv.tenant_id = u.tenant_id
                ORDER BY cv.id DESC LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $convRows);
        } catch (Throwable $e) {}

        // Damayan
        try {
            $burRows = $db->query("
                SELECT b.id, 'Damayan' as department, 'Burial Service' as service_type,
                       COALESCE(b.deceased_name, CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,''))) as subject_name,
                       b.informant_contact as contact, DATE(b.submitted_at) as report_date, b.status,
                       CONCAT('Plot: ', COALESCE(b.assigned_plot_number,'Pending'), ' (', COALESCE(b.cemetery_preference,'Local'), ')') as detail_info
                FROM burial_requests b
                LEFT JOIN tenant_accounts u ON b.tenant_id = u.tenant_id
                ORDER BY b.id DESC LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $burRows);

            $fcRows = $db->query("
                SELECT f.id, 'Damayan' as department, 'Funeral Case Management' as service_type,
                       CONCAT(COALESCE(f.deceased_first_name,''), ' ', COALESCE(f.deceased_last_name,'')) as subject_name,
                       f.informant_contact as contact, DATE(f.reported_at) as report_date, f.status,
                       CONCAT('Cert: ', COALESCE(f.certificate_status,'Pending')) as detail_info
                FROM funeral_cases f
                ORDER BY f.id DESC LIMIT 20
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $fcRows);
        } catch (Throwable $e) {}

        // Billing
        try {
            $billRows = $db->query("
                SELECT b.billing_id as id, 'Finance' as department, 'Billing Invoice' as service_type,
                       CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as subject_name,
                       u.email as contact, DATE(b.created_at) as report_date, b.status,
                       CONCAT('₱', FORMAT(b.amount, 2)) as detail_info
                FROM billing b
                LEFT JOIN tenant_accounts u ON b.tenant_id = u.tenant_id
                ORDER BY b.billing_id DESC LIMIT 30
            ")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            $masterReportItems = array_merge($masterReportItems, $billRows);
        } catch (Throwable $e) {}

        usort($masterReportItems, function($a, $b) {
            return strtotime($b['report_date'] ?? '1970-01-01') - strtotime($a['report_date'] ?? '1970-01-01');
        });

        // 3. Apartment Lease Logs
        $aptLeaseLogs = $db->query("
            SELECT a.*, u.first_name, u.last_name, au.room_number, au.building 
            FROM apartmentsapp a
            JOIN tenant_accounts u ON a.tenant_id = u.tenant_id
            LEFT JOIN apartment_units au ON a.unit_id = au.unit_id
            ORDER BY a.application_id DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // 4. System Notifications / Audit Logs
        $logs = $db->query("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $this->view('admin/mis_admin/admin_reports', [
            'active_page' => 'reports',
            'aptStats' => $aptStats,
            'totalApts' => $totalApts,
            'billingStats' => $billingStats,
            'totalRevenue' => $totalRevenue,
            'userStats' => $userStats,
            'totalUsers' => $totalUsers,
            'eduEnrollments' => $eduEnrollments,
            'totalEduStudents' => $totalEduStudents,
            'avgEduProgress' => $avgEduProgress,
            'totalEduSessions' => $totalEduSessions,
            'burialRequests' => $burialRequests,
            'funeralCases' => $funeralCases,
            'charityDonations' => $charityDonations,
            'counselingCount' => $counselingCount,
            'marriageCount' => $marriageCount,
            'conversionCount' => $conversionCount,
            'masterReportItems' => $masterReportItems,
            'recentLogs' => $logs,
            'burialLogs' => $burialRequests,
            'aptLeaseLogs' => $aptLeaseLogs
        ]);
    }

    public function dawahRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        $db = getDbConnection();

        $counseling = $db->query("
            SELECT c.id, 'counseling' as type, CONCAT('Counseling (', COALESCE(c.reason, c.session_type, 'General'), ')') as service_type,
                   CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as name,
                   u.email, c.created_at as date, c.status
            FROM counseling_requests c
            LEFT JOIN tenant_accounts u ON c.tenant_id = u.tenant_id
            ORDER BY c.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $marriages = $db->query("
            SELECT m.id, 'marriage_service' as type, 'Marriage Solemnization' as service_type,
                   CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as name,
                   u.email, m.created_at as date, m.status
            FROM marriage_requests m
            LEFT JOIN tenant_accounts u ON m.tenant_id = u.tenant_id
            ORDER BY m.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $conversions = $db->query("
            SELECT cv.id, 'conversion' as type, 'Shahadah Conversion' as service_type,
                   CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as name,
                   u.email, cv.created_at as date, cv.status
            FROM conversion_requests cv
            LEFT JOIN tenant_accounts u ON cv.tenant_id = u.tenant_id
            ORDER BY cv.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $education = $db->query("
            SELECT e.id, 'islamic_education' as type, CONCAT('Enrollment: ', COALESCE(e.level,'Course')) as service_type,
                   CONCAT(COALESCE(e.first_name,''), ' ', COALESCE(e.last_name,'')) as name,
                   e.contact_number as email, e.created_at as date, e.status
            FROM islamic_education_enrollments e
            ORDER BY e.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $dawahRequests = array_merge($counseling, $marriages, $conversions, $education);
        usort($dawahRequests, function($a, $b) {
            return strtotime($b['date'] ?? '1970-01-01') - strtotime($a['date'] ?? '1970-01-01');
        });

        $this->view('admin/mis_admin/dawah_records', [
            'active_page' => 'dawah_records',
            'dawahRequests' => $dawahRequests,
            'counselingCount' => count($counseling),
            'educationCount' => count($education),
            'marriageCount' => count($marriages),
            'conversionCount' => count($conversions)
        ]);
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
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        $marriageModel = new MarriageRequest();
        $eduModel = new IslamicEducation();
        $conversionModel = new ConversionRequest();
        $marriageAll = $marriageModel->getAll();
        $conversionAll = $conversionModel->getAll();
        $eduAnalytics = $eduModel->getAnalytics('male');

        // 1. Transform raw Counseling rows
        $requests = array_map(function($r) {
            $statusMap = [
                'pending'  => ['label' => 'Pending',  'class' => 'pending'],
                'approved' => ['label' => 'Approved', 'class' => 'success'],
                'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
            ];
            $s = $statusMap[$r['status']] ?? ['label' => ucfirst($r['status']), 'class' => 'pending'];
            return [
                'id'            => $r['id'],
                'name'          => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Applicant',
                'type'          => 'counseling',
                'service_label' => 'Counseling — ' . ($r['reason'] ?? 'General'),
                'date'          => date('M d, Y', strtotime($r['created_at'])),
                'status'        => $s['label'],
                'status_class'  => $s['class'],
                'raw'           => $r
            ];
        }, $rawRequests);

        // 2. Transform Marriage rows
        foreach ($marriageAll as $m) {
            $statusMap = [
                'pending'  => ['label' => 'Pending',  'class' => 'pending'],
                'approved' => ['label' => 'Approved', 'class' => 'success'],
                'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
            ];
            $s = $statusMap[$m['status']] ?? ['label' => ucfirst($m['status']), 'class' => 'pending'];
            $requests[] = [
                'id'            => $m['id'],
                'name'          => ($m['groom_name'] ?? 'Groom') . ' & ' . ($m['bride_name'] ?? 'Bride'),
                'type'          => 'marriage',
                'service_label' => 'Marriage Ceremony',
                'date'          => date('M d, Y', strtotime($m['created_at'] ?? $m['marriage_date'])),
                'status'        => $s['label'],
                'status_class'  => $s['class'],
                'raw'           => $m
            ];
        }

        // 3. Transform Conversion rows
        $convPending = 0;
        $convApproved = 0;
        foreach ($conversionAll as $c) {
            if (($c['status'] ?? '') === 'pending') $convPending++;
            if (($c['status'] ?? '') === 'approved') $convApproved++;
            $statusMap = [
                'pending'  => ['label' => 'Pending',  'class' => 'pending'],
                'approved' => ['label' => 'Approved', 'class' => 'success'],
                'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
            ];
            $s = $statusMap[$c['status']] ?? ['label' => ucfirst($c['status']), 'class' => 'pending'];
            $adopted = !empty($c['adopted_name']) ? " ({$c['adopted_name']})" : '';
            $requests[] = [
                'id'            => $c['id'],
                'name'          => trim(($c['fname'] ?? '') . ' ' . ($c['lname'] ?? '')) . $adopted,
                'type'          => 'conversion',
                'service_label' => 'Conversion / Shahadah',
                'date'          => date('M d, Y', strtotime($c['created_at'] ?? $c['conversion_date'])),
                'status'        => $s['label'],
                'status_class'  => $s['class'],
                'raw'           => $c
            ];
        }

        // Sort unified requests by date descending
        usort($requests, function($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        $marriagePending = count(array_filter($marriageAll, fn($m) => ($m['status'] ?? '') === 'pending'));
        $marriageApproved = count(array_filter($marriageAll, fn($m) => ($m['status'] ?? '') === 'approved'));

        $analytics = [
            'counseling_total'    => $counselingAnalytics['total'] ?? 0,
            'counseling_pending'  => $counselingAnalytics['pending'] ?? 0,
            'counseling_approved' => $counselingAnalytics['approved'] ?? 0,
            'marriage_total'      => count($marriageAll),
            'marriage_pending'    => $marriagePending,
            'marriage_approved'   => $marriageApproved,
            'conversion_total'    => count($conversionAll),
            'conversion_pending'  => $convPending,
            'conversion_approved' => $convApproved,
            'student_count'       => $eduAnalytics['total'] ?? 0,
            'student_active'      => $eduAnalytics['active'] ?? 0,
            'student_completed'   => $eduAnalytics['completed'] ?? 0,
            'pending'             => ($counselingAnalytics['pending'] ?? 0) + ($eduAnalytics['pending'] ?? 0) + $marriagePending + $convPending,
        ];

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $dawahDeathCerts = $burialModel->getDawahDeathCertificates('', 'all', 10, 0);
        $dawahDeathCertsCount = $burialModel->countDawahDeathCertificates('', 'all');
        $dawahDeathCertsNew = $burialModel->countDawahDeathCertificates('', 'Received');
        $analytics['death_cert_total'] = $dawahDeathCertsCount;
        $analytics['death_cert_new'] = $dawahDeathCertsNew;

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/dawah_male_dashboard', [
            'active_page'          => 'dashboard',
            'dbUser'               => $dbUser,
            'requests'             => $requests,
            'analytics'            => $analytics,
            'dawahDeathCerts'      => $dawahDeathCerts,
            'dawahDeathCertsCount' => $dawahDeathCertsCount,
            'dawahDeathCertsNew'   => $dawahDeathCertsNew
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
        $coursesSummary = $eduModel->getCoursesSummary('female');

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $counselingModel = new CounselingRequest();
        $counselingAnalytics = $counselingModel->getAnalytics('female');
        $counselingRequests = $counselingModel->getByGender('female');

        $students = $rawStudents;
        foreach ($students as &$st) {
            if (empty($st['total_sessions']) || !isset($st['progress_percent'])) {
                $p = $eduModel->recalculateStudentProgress((int)$st['id']);
                $st['total_sessions'] = $p['total_sessions'];
                $st['sessions_attended'] = $p['sessions_attended'];
                $st['sessions_completed'] = $p['sessions_completed'];
                $st['progress_percent'] = $p['progress_percent'];
                $st['learning_status'] = $p['learning_status'];
            }
        }
        unset($st);

        $activeWithProgress = array_filter($students, fn($s) => in_array(strtolower($s['status'] ?? ''), ['active', 'accepted', 'approved', 'enrolled', 'completed']));
        $avgProgress = !empty($activeWithProgress)
            ? round(array_sum(array_column($activeWithProgress, 'progress_percent')) / count($activeWithProgress))
            : ($eduAnalytics['avg_progress'] ?? 0);

        $analytics = [
            'total_students' => $eduAnalytics['total'] ?? 0,
            'active_students' => $eduAnalytics['active'] ?? 0,
            'completed' => $eduAnalytics['completed'] ?? 0,
            'pending' => ($eduAnalytics['pending'] ?? 0) + ($counselingAnalytics['pending'] ?? 0),
            'counseling_total' => $counselingAnalytics['total'] ?? 0,
            'counseling_approved' => $counselingAnalytics['approved'] ?? 0,
            'avg_progress' => $avgProgress,
        ];

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/dawah_female_dashboard', [
            'active_page' => 'dashboard',
            'dbUser' => $dbUser,
            'students' => $students,
            'coursesSummary' => $coursesSummary,
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

        // Determine department type based on role or explicit query parameter
        $role = $_SESSION['role'] ?? '';
        $typeParam = strtolower(trim($_GET['type'] ?? ''));
        $dawah_type = ($typeParam === 'female' || $role === 'Staff_Female') ? 'female' : 'male';
        
        $eduModel = new IslamicEducation();
        $records = $eduModel->getAllByGender($dawah_type);
        foreach ($records as &$rec) {
            if (empty($rec['total_sessions']) || !isset($rec['progress_percent'])) {
                $p = $eduModel->recalculateStudentProgress((int)$rec['id']);
                $rec['total_sessions'] = $p['total_sessions'];
                $rec['sessions_attended'] = $p['sessions_attended'];
                $rec['sessions_completed'] = $p['sessions_completed'];
                $rec['progress_percent'] = $p['progress_percent'];
                $rec['learning_status'] = $p['learning_status'];
            }
        }
        unset($rec);
        $coursesSummary = $eduModel->getCoursesSummary($dawah_type);
        $analytics = $eduModel->getAnalytics($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/female_education'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/male_education';

        $this->view($viewPath, [
            'active_page' => 'education',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records,
            'coursesSummary' => $coursesSummary,
            'analytics' => $analytics
        ]);
    }

    public function dawahEducationCourses(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $typeParam = strtolower(trim($_GET['type'] ?? ''));
        $dawah_type = ($typeParam === 'female' || $role === 'Staff_Female') ? 'female' : 'male';
        
        $eduModel = new IslamicEducation();
        $records = $eduModel->getAllByGender($dawah_type);
        foreach ($records as &$rec) {
            if (empty($rec['total_sessions']) || !isset($rec['progress_percent'])) {
                $p = $eduModel->recalculateStudentProgress((int)$rec['id']);
                $rec['total_sessions'] = $p['total_sessions'];
                $rec['sessions_attended'] = $p['sessions_attended'];
                $rec['sessions_completed'] = $p['sessions_completed'];
                $rec['progress_percent'] = $p['progress_percent'];
                $rec['learning_status'] = $p['learning_status'];
            }
        }
        unset($rec);
        $coursesSummary = $eduModel->getCoursesSummary($dawah_type);
        $analytics = $eduModel->getAnalytics($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/enrolled_courses'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/enrolled_courses';

        $this->view($viewPath, [
            'active_page' => 'enrolled_courses',
            'view_mode' => 'courses_table',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records,
            'coursesSummary' => $coursesSummary,
            'analytics' => $analytics
        ]);
    }

    public function educationRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $typeParam = strtolower(trim($_GET['type'] ?? ''));
        $dawah_type = ($typeParam === 'female' || $role === 'Staff_Female') ? 'female' : 'male';
        
        $eduModel = new IslamicEducation();
        $records = $eduModel->getAllByGender($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/education_requests'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/education_requests';

        $this->view($viewPath, [
            'active_page' => 'education_requests',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records
        ]);
    }

    public function approveEducationEnrollment(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/IslamicEducation.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new IslamicEducation();
            $success = $model->updateStatus($id, 'Accepted');

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#ER-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Islamic Education Enrollment Accepted',
                        "Your enrollment application ($refNo) for {$req['program_name']} has been accepted. Welcome to the class!",
                        'success'
                    );
                    $this->logAudit('DAWAH', 'ACCEPT_EDUCATION_ENROLLMENT', "Accepted enrollment request $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}");
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Accepted',
                'message' => $success ? 'Enrollment request has been accepted.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function rejectEducationEnrollment(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            $reason = trim($input['reason'] ?? '');
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/IslamicEducation.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new IslamicEducation();
            $success = $model->updateStatus($id, 'Rejected', $reason ?: null);

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#ER-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $notifMsg = "Your enrollment application ($refNo) for {$req['program_name']} could not be accommodated at this time.";
                    if (!empty($reason)) {
                        $notifMsg .= " Reason: {$reason}";
                    }
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Islamic Education Enrollment Update',
                        $notifMsg,
                        'warning'
                    );
                    $auditDetails = "Rejected enrollment request $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}";
                    if (!empty($reason)) {
                        $auditDetails .= " — Reason: {$reason}";
                    }
                    $this->logAudit('DAWAH', 'REJECT_EDUCATION_ENROLLMENT', $auditDetails);
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Rejected',
                'reason' => $reason,
                'message' => $success ? 'Enrollment request has been rejected.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function markEducationPaid(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid enrollment request ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        require_once BASE_PATH . '/app/models/Notification.php';

        $eduModel = new IslamicEducation();
        $record = $eduModel->getById($id);

        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'Enrollment record not found.']);
            exit;
        }

        $refNo = !empty($record['payment_reference']) 
            ? $record['payment_reference'] 
            : ('ISCAG-RCPT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)));

        $success = $eduModel->updatePaymentInfo($id, [
            'payment_status' => 'Paid',
            'payment_reference' => $refNo
        ]);

        if ($success) {
            if (!empty($record['tenant_id'])) {
                try {
                    $notif = new Notification();
                    $progTitle = $record['level'] ?: ($record['program_name'] ?: 'Islamic Education');
                    $notif->create(
                        (int)$record['tenant_id'],
                        'Registration Fee Payment Confirmed',
                        "Your registration fee (₱120.00) for {$progTitle} has been officially verified and marked as Paid by the Da'wah Department.",
                        'success'
                    );
                } catch (Exception $e) {}
            }

            $this->logAudit('DAWAH', 'MARK_EDUCATION_PAID', "Marked enrollment #EDU-$id as Paid (Ref: $refNo)");

            echo json_encode([
                'success' => true,
                'message' => 'Registration fee has been successfully marked as Paid.',
                'payment_status' => 'Paid',
                'payment_reference' => $refNo
            ]);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Failed to update payment status.']);
        exit;
    }

    public function getEducationProgress(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid student enrollment ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        $eduModel = new IslamicEducation();
        $progress = $eduModel->getStudentProgress($id);

        if (!$progress) {
            echo json_encode(['success' => false, 'message' => 'Enrollment record not found.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $progress
        ]);
        exit;
    }

    public function updateEducationProgress(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['enrollment_id'] ?? ($input['id'] ?? 0));
        $sessionNum = (int)($input['session_number'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid enrollment ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        $eduModel = new IslamicEducation();

        // Support bulk update if sessions array is passed
        if (isset($input['sessions']) && is_array($input['sessions'])) {
            $updatedData = $eduModel->bulkUpdateProgress($id, $input['sessions']);
            $this->logAudit('DAWAH', 'UPDATE_EDUCATION_PROGRESS', "Bulk updated session progress for Student ID #$id");
            echo json_encode([
                'success' => true,
                'message' => 'Student progress updated successfully.',
                'data' => $updatedData
            ]);
            exit;
        }

        if ($sessionNum < 1 || $sessionNum > 12) {
            echo json_encode(['success' => false, 'message' => 'Invalid session number (must be 1-12).']);
            exit;
        }

        $isAttended = !empty($input['is_attended']);
        $isCompleted = !empty($input['is_completed']);
        $notes = isset($input['notes']) ? (string)$input['notes'] : null;

        $res = $eduModel->updateSessionProgress($id, $sessionNum, $isAttended, $isCompleted, $notes);
        $fullProgress = $eduModel->getStudentProgress($id);

        $this->logAudit('DAWAH', 'UPDATE_EDUCATION_PROGRESS', "Updated Session #$sessionNum (Attended: " . ($isAttended ? 'Yes' : 'No') . ", Completed: " . ($isCompleted ? 'Yes' : 'No') . ") for Student ID #$id");

        echo json_encode([
            'success' => true,
            'message' => 'Progress updated successfully.',
            'summary' => $fullProgress['summary'],
            'session' => $res['session'],
            'data' => $fullProgress
        ]);
        exit;
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
        $counselors = $counselingModel->getActiveCounselors($dawah_type);
        $analytics = $counselingModel->getAnalytics($dawah_type);

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/counseling'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/counseling';

        $this->view($viewPath, [
            'active_page' => 'counseling',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records,
            'counselors' => $counselors,
            'analytics' => $analytics
        ]);
    }

    public function counselingRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $records = $counselingModel->getByGender($dawah_type);
        $counselors = $counselingModel->getActiveCounselors($dawah_type);

        $viewPath = ($dawah_type === 'female' && file_exists(BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/counseling_requests.php'))
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/counseling_requests'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/counseling_requests';

        $this->view($viewPath, [
            'active_page' => 'counseling_requests',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'records' => $records,
            'counselors' => $counselors
        ]);
    }

    public function acceptCounselingRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        $success = $model->updateStatus($id, 'Accepted');

        if ($success) {
            $req = $model->getById($id);
            if ($req && !empty($req['tenant_id'])) {
                $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                $notif = new Notification();
                $notif->create(
                    (int)$req['tenant_id'],
                    'Counseling Request Accepted',
                    "As-salamu alaykum. Your counseling request ($refNo) has been accepted by the Da'wah Department. Our administration is preparing your session details.",
                    'success'
                );
                $this->logAudit('DAWAH', 'ACCEPT_COUNSELING_REQUEST', "Accepted counseling request ID: $id");
            }
        }

        echo json_encode([
            'success' => $success,
            'status' => 'Accepted',
            'message' => $success ? 'Counseling request has been accepted.' : 'Failed to update request status.'
        ]);
        exit;
    }

    public function rejectCounselingRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        $success = $model->updateStatus($id, 'Rejected');

        if ($success) {
            $req = $model->getById($id);
            if ($req && !empty($req['tenant_id'])) {
                $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                $notif = new Notification();
                $notif->create(
                    (int)$req['tenant_id'],
                    'Counseling Request Update',
                    "Your counseling consultation request ($refNo) could not be accommodated at this time.",
                    'warning'
                );
                $this->logAudit('DAWAH', 'REJECT_COUNSELING_REQUEST', "Rejected counseling request ID: $id");
            }
        }

        echo json_encode([
            'success' => $success,
            'status' => 'Rejected',
            'message' => $success ? 'Counseling request has been rejected.' : 'Failed to update request status.'
        ]);
        exit;
    }

    public function deleteCounselingRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $model = new CounselingRequest();
        $req = $model->getById($id);
        $success = $model->delete($id);

        if ($success) {
            $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
            $this->logAudit('DAWAH', 'DELETE_COUNSELING_REQUEST', "Deleted counseling consultation request $refNo (ID: $id)");
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Counseling request has been deleted successfully.' : 'Failed to delete counseling request.'
        ]);
        exit;
    }

    public function dawahMarriage(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $marriageModel = new MarriageRequest();
        $records = $marriageModel->getAll(); 

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/marriage'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/marriage';

        $this->view($viewPath, [
            'active_page' => 'marriage',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'applications' => $records,
            'records' => $records
        ]);
    }

    public function marriageRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $marriageModel = new MarriageRequest();
        $records = $marriageModel->getAll(); 

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/marriage_requests'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/marriage_requests';

        $this->view($viewPath, [
            'active_page' => 'marriage_requests',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'applications' => $records,
            'records' => $records
        ]);
    }

    public function approveMarriage(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/MarriageRequest.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new MarriageRequest();
            $success = $model->updateStatus($id, 'Accepted');

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#MR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $dateStr = !empty($req['marriage_date']) ? date('M d, Y', strtotime($req['marriage_date'])) : 'Confirmed Date';
                    $timeStr = !empty($req['marriage_time']) ? ' at ' . $req['marriage_time'] : '';
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Marriage Reservation Accepted',
                        "As-salamu alaykum. Your marriage ceremony reservation ($refNo) for {$req['groom_name']} & {$req['bride_name']} has been accepted for {$dateStr}{$timeStr} at {$req['marriage_venue']}.",
                        'success'
                    );
                    $this->logAudit('DAWAH', 'ACCEPT_MARRIAGE', "Accepted marriage reservation $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}");
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Accepted',
                'message' => $success ? 'Marriage reservation request has been accepted.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function rejectMarriage(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/MarriageRequest.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new MarriageRequest();
            $success = $model->updateStatus($id, 'Rejected');

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#MR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Marriage Reservation Update',
                        "Your marriage solemnization reservation ($refNo) for {$req['groom_name']} & {$req['bride_name']} could not be accommodated at this time.",
                        'warning'
                    );
                    $this->logAudit('DAWAH', 'REJECT_MARRIAGE', "Rejected marriage request $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}");
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Rejected',
                'message' => $success ? 'Marriage reservation request has been rejected.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function dawahConversion(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $conversionModel = new ConversionRequest();
        $records = $conversionModel->getAll();

        $viewPath = 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/conversion';

        $this->view($viewPath, [
            'active_page' => 'conversion',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'applications' => $records,
            'records' => $records
        ]);
    }

    public function conversionRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $conversionModel = new ConversionRequest();
        $records = $conversionModel->getAll();

        $viewPath = ($dawah_type === 'female')
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/conversion_requests'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/conversion_requests';

        $this->view($viewPath, [
            'active_page' => 'conversion_requests',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'applications' => $records,
            'records' => $records
        ]);
    }

    public function approveConversion(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/ConversionRequest.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new ConversionRequest();
            $success = $model->updateStatus($id, 'Accepted');

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $adopted = $req['adopted_name'] ?: ($req['fname'] . ' ' . $req['lname']);
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Conversion Request Accepted',
                        "Alhamdulillah! Your Conversion to Islam (Shahadah) request ($refNo) for {$adopted} has been accepted. You can view your Certificate from your account.",
                        'success'
                    );
                    $this->logAudit('DAWAH', 'ACCEPT_CONVERSION', "Accepted conversion request $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}");
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Accepted',
                'message' => $success ? 'Conversion request has been accepted.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function rejectConversion(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = (int)($input['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
                exit;
            }

            require_once BASE_PATH . '/app/models/ConversionRequest.php';
            require_once BASE_PATH . '/app/models/Notification.php';
            $model = new ConversionRequest();
            $success = $model->updateStatus($id, 'Rejected');

            if ($success) {
                $req = $model->getById($id);
                if ($req && !empty($req['tenant_id'])) {
                    $refNo = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                    $notif = new Notification();
                    $notif->create(
                        (int)$req['tenant_id'],
                        'Conversion Request Status Update',
                        "Your Conversion application ($refNo) could not be accommodated or requires additional verification.",
                        'warning'
                    );
                    $this->logAudit('DAWAH', 'REJECT_CONVERSION', "Rejected conversion request $refNo (ID: $id) for Tenant ID: {$req['tenant_id']}");
                }
            }

            echo json_encode([
                'success' => $success,
                'status' => 'Rejected',
                'message' => $success ? 'Conversion request has been rejected.' : 'Failed to update request status.'
            ]);
            exit;
        }
    }

    public function dawahSchedule(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        require_once BASE_PATH . '/app/models/DawahSchedule.php';
        require_once BASE_PATH . '/app/models/DawahAvailability.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $marriageModel = new MarriageRequest();
        $manualModel = new DawahSchedule();
        $availModel = new DawahAvailability();

        // 1. Fetch Counseling
        $counselingRaw = $counselingModel->getByGender($dawah_type);
        $counselingScheds = [];
        $counselingPending = [];

        foreach ($counselingRaw as $r) {
            $st = strtolower($r['status'] ?? 'pending');
            $isConfirmed = in_array($st, ['approved', 'confirmed', 'accepted']);
            $isPending = in_array($st, ['pending', 'under_review']);
            $clientName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: ($r['full_name'] ?? 'Client');
            
            $item = [
                'id' => $r['id'],
                'type' => 'Counseling',
                'category' => 'counseling',
                'name' => $clientName,
                'contact' => $r['contact_number'] ?? $r['email'] ?? '',
                'age' => $r['age'] ?? null,
                'date' => $isConfirmed ? ($r['appointment_date'] ?: $r['preferred_date']) : $r['preferred_date'],
                'time' => $isConfirmed ? ($r['start_time'] ?: $r['preferred_time']) : $r['preferred_time'],
                'preferred_date' => $r['preferred_date'],
                'preferred_time' => $r['preferred_time'],
                'desc' => $r['reason'] ?? 'Religious Guidance',
                'counselor' => $r['counselor_full_name'] ?? $r['counselor_name'] ?? 'Assigned Counselor',
                'location' => $r['appointment_location'] ?? 'ISCAG Center, Room 204',
                'status' => $st
            ];

            if ($isConfirmed) {
                $counselingScheds[] = $item;
            } elseif ($isPending) {
                $counselingPending[] = $item;
            }
        }

        // 2. Fetch Marriage
        $marriageRaw = $marriageModel->getAll();
        $marriageScheds = [];
        $marriagePending = [];

        foreach ($marriageRaw as $r) {
            $st = strtolower($r['status'] ?? 'pending');
            $isApproved = in_array($st, ['approved', 'accepted', 'confirmed']);
            $isPending = in_array($st, ['pending', 'under_review']);
            $names = trim(($r['groom_name'] ?? '') . ' & ' . ($r['bride_name'] ?? '')) ?: 'Wedding Couple';

            $item = [
                'id' => $r['id'],
                'type' => 'Marriage Ceremony',
                'category' => 'marriage',
                'name' => $names,
                'contact' => $r['groom_contact'] ?? '',
                'age' => null,
                'date' => $r['marriage_date'] ?: ($r['preferred_date'] ?? ''),
                'time' => $r['marriage_time'] ?: ($r['preferred_time'] ?? '10:00 AM'),
                'preferred_date' => $r['preferred_date'] ?? $r['marriage_date'] ?? '',
                'preferred_time' => $r['preferred_time'] ?? $r['marriage_time'] ?? '',
                'desc' => 'Marriage Solemnization — Venue: ' . ($r['marriage_venue'] ?? 'ISCAG Center'),
                'counselor' => $r['officiant_name'] ?? 'Islamic Officiant',
                'location' => $r['marriage_venue'] ?? 'ISCAG Center',
                'status' => $st
            ];

            if ($isApproved) {
                $marriageScheds[] = $item;
            } elseif ($isPending) {
                $marriagePending[] = $item;
            }
        }

        // 3. Fetch Islamic Studies / Conversion (Shahadah)
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        $conversionModel = new ConversionRequest();
        $conversionRaw = $conversionModel->getAll();
        $conversionScheds = [];
        $conversionPending = [];

        foreach ($conversionRaw as $r) {
            $st = strtolower($r['status'] ?? 'pending');
            $isApproved = in_array($st, ['approved', 'accepted', 'confirmed']);
            $isPending = in_array($st, ['pending', 'under_review']);
            $cName = trim(($r['fname'] ?? '') . ' ' . ($r['lname'] ?? ''));
            if (!empty($r['adopted_name'])) {
                $cName .= ' (' . $r['adopted_name'] . ')';
            }

            $item = [
                'id' => $r['id'],
                'type' => 'Islamic Studies / Conversion',
                'category' => 'conversion',
                'name' => $cName ?: 'Applicant',
                'contact' => $r['residence'] ?? '',
                'age' => $r['age'] ?? null,
                'date' => !empty($r['conversion_date']) ? $r['conversion_date'] : date('Y-m-d', strtotime($r['created_at'])),
                'time' => $r['preferred_time'] ?? '10:00 AM',
                'preferred_date' => !empty($r['conversion_date']) ? $r['conversion_date'] : date('Y-m-d', strtotime($r['created_at'])),
                'preferred_time' => $r['preferred_time'] ?? '10:00 AM',
                'desc' => 'Islamic Studies & Shahadah — Adopted: ' . ($r['adopted_name'] ?? '—'),
                'counselor' => $r['officiant'] ?? 'Da\'wah Ustaz / Officiant',
                'location' => $r['venue'] ?? 'ISCAG Main Mosque',
                'status' => $st
            ];

            if ($isApproved) {
                $conversionScheds[] = $item;
            } elseif ($isPending) {
                $conversionPending[] = $item;
            }
        }

        // 4. Fetch Male Islamic Education Enrollments
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        $eduModel = new IslamicEducation();
        $eduRaw = $eduModel->getAllByGender($dawah_type);
        $eduScheds = [];
        $eduPending = [];

        foreach ($eduRaw as $e) {
            $st = strtolower($e['status'] ?? 'pending');
            $isApproved = in_array($st, ['active', 'accepted', 'approved', 'completed']);
            $isPending = ($st === 'pending');
            $eName = trim(($e['first_name'] ?? '') . ' ' . ($e['last_name'] ?? ''));
            $item = [
                'id' => $e['id'],
                'type' => 'Islamic Education Class',
                'category' => 'education',
                'name' => $eName ?: 'Student',
                'contact' => $e['contact_number'] ?? '',
                'age' => null,
                'date' => !empty($e['date_of_shahadah']) ? $e['date_of_shahadah'] : date('Y-m-d', strtotime($e['created_at'])),
                'time' => '09:00 AM',
                'preferred_date' => date('Y-m-d', strtotime($e['created_at'])),
                'preferred_time' => '09:00 AM',
                'desc' => 'Islamic Studies: ' . ($e['program_name'] ?? 'General Program') . ' (' . ($e['level'] ?? 'Basic') . ')',
                'counselor' => $e['prepared_by'] ?? 'Ustaz / Instructor',
                'location' => 'ISCAG Madrasah Hall',
                'status' => $st
            ];
            if ($isApproved) {
                $eduScheds[] = $item;
            } elseif ($isPending) {
                $eduPending[] = $item;
            }
        }

        // 5. Fetch Manual Assignments (Classes/Seminars)
        $manualRaw = $manualModel->getByDepartment($dawah_type);
        $manualScheds = array_map(function($r) {
            return [
                'id' => $r['id'],
                'type' => $r['event_type'] ?? 'Class',
                'category' => 'event',
                'name' => $r['title'],
                'contact' => '',
                'age' => null,
                'date' => $r['event_date'],
                'time' => $r['event_time'],
                'preferred_date' => $r['event_date'],
                'preferred_time' => $r['event_time'],
                'desc' => $r['description'] ?: 'Department Educational Activity',
                'counselor' => 'Da\'wah Department Staff',
                'location' => 'ISCAG Center',
                'status' => 'approved'
            ];
        }, $manualRaw);

        // Available / Confirmed schedules (sorted by date/time)
        $availableSchedules = array_merge($counselingScheds, $marriageScheds, $conversionScheds, $eduScheds, $manualScheds);
        usort($availableSchedules, function($a, $b) {
            return strtotime(($a['date'] ?? '') . ' ' . ($a['time'] ?? '')) <=> strtotime(($b['date'] ?? '') . ' ' . ($b['time'] ?? ''));
        });

        // Pending schedules (sorted by requested date)
        $pendingSchedules = array_merge($counselingPending, $marriagePending, $conversionPending, $eduPending);
        usort($pendingSchedules, function($a, $b) {
            return strtotime(($a['date'] ?? '') . ' ' . ($a['time'] ?? '')) <=> strtotime(($b['date'] ?? '') . ' ' . ($b['time'] ?? ''));
        });

        // Blocked / Not Available schedules
        $blockedDates = $availModel->getBlockedDates($dawah_type);
        $blockedList = $availModel->getBlockedList($dawah_type);

        // Combined for calendar indicator
        $schedules = $availableSchedules;

        $viewPath = ($dawah_type === 'female') 
            ? 'admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/schedule'
            : 'admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/schedule';

        $this->view($viewPath, [
            'active_page' => 'schedule',
            'dawah_type' => $dawah_type,
            'dbUser' => $dbUser,
            'schedules' => $schedules,
            'availableSchedules' => $availableSchedules,
            'pendingSchedules' => $pendingSchedules,
            'blockedDates' => $blockedDates,
            'blockedList' => $blockedList
        ]);
    }

    public function dawahMaleProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']) ?: [];
        $addInfo = $userModel->getAdditionalInfo($_SESSION['user_id']) ?: [];
        $dbUser = array_merge($dbUser, $addInfo);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/profile', [
            'active_page' => 'profile',
            'dbUser' => $dbUser
        ]);
    }

    public function dawahFemaleProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']) ?: [];
        $addInfo = $userModel->getAdditionalInfo($_SESSION['user_id']) ?: [];
        $dbUser = array_merge($dbUser, $addInfo);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Female Dawah/profile', [
            'active_page' => 'profile',
            'dbUser' => $dbUser
        ]);
    }

    public function updateStaffProfile(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();

        $name = trim($_POST['name'] ?? '');
        $parts = explode(' ', $name, 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $arabicName = trim($_POST['arabic'] ?? '');
        $occupation = trim($_POST['occupation'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $db = getDbConnection();
        if (!empty($firstName)) {
            $stmt = $db->prepare("UPDATE tenant_accounts SET first_name = :fn, last_name = :ln WHERE tenant_id = :id");
            $stmt->execute(['fn' => $firstName, 'ln' => $lastName, 'id' => $userId]);
            $_SESSION['name'] = trim("$firstName $lastName");
        }
        if (!empty($email)) {
            $_SESSION['email'] = $email;
        }

        $data = [
            'email' => $email,
            'phone' => $phone,
            'arabicName' => $arabicName,
            'occupation' => $occupation,
            'address' => $address,
            'dob' => null,
            'civil' => null,
            'revertYear' => null
        ];

        // Handle profile photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = mime_content_type($file['tmp_name']);
            if (in_array($mime, $allowedMime)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = "staff_{$userId}_" . time() . "." . $ext;
                $relPath = "uploads/profiles/" . $fileName;
                $uploadDir = BASE_PATH . "/public/uploads/profiles";
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $fullPath = BASE_PATH . "/public/" . $relPath;
                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                    $data['profile_picture_path'] = $relPath;
                    $data['profile_picture_mime'] = $mime;
                }
            }
        }

        $success = $userModel->updateProfile($userId, $data);

        $this->logAudit('PROFILE', 'UPDATE_STAFF_PROFILE', "Staff ID $userId updated their profile details.");

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Staff profile updated successfully!' : 'Failed to update profile.',
            'profile' => [
                'name' => trim("$firstName $lastName"),
                'email' => $email,
                'phone' => $phone,
                'arabic' => $arabicName,
                'occupation' => $occupation,
                'address' => $address,
                'avatar' => isset($data['profile_picture_path']) ? asset($data['profile_picture_path']) : null
            ]
        ]);
        exit;
    }

    public function assignDawahSchedule(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $role = $_SESSION['role'] ?? '';
                $dept = !empty($input['department']) ? strtolower($input['department']) : (($role === 'Staff_Female') ? 'female' : 'male');
                
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
                $dept = !empty($input['department']) ? strtolower($input['department']) : (($role === 'Staff_Female') ? 'female' : 'male');

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

    public function acceptAndScheduleCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $counselorId = (int)($input['counselor_id'] ?? 0);
        $aptDate = trim($input['appointment_date'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $sessionType = trim($input['final_session_type'] ?? 'In-Person');
        $location = trim($input['appointment_location'] ?? 'ISCAG Center, 2nd Floor Da\'wah Counseling Office');
        $meetingLink = trim($input['meeting_link'] ?? '');
        $meetingInstructions = trim($input['meeting_instructions'] ?? '');
        $notes = trim($input['counselor_notes'] ?? '');

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
            exit;
        }

        if (!$counselorId) {
            echo json_encode(['success' => false, 'message' => 'Please select an authorized counselor to assign to this session.']);
            exit;
        }

        if (empty($aptDate) || empty($startTime)) {
            echo json_encode(['success' => false, 'message' => 'Please set the finalized appointment date and start time.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();

        // Prevent scheduling conflicts: check if counselor already has an appointment at this time
        $conflict = $model->checkConflict($counselorId, $aptDate, $startTime, $endTime, $id);
        if ($conflict) {
            $cName = $conflict['counselor_name'] ?? 'The selected counselor';
            $cDate = date('M d, Y', strtotime($conflict['appointment_date']));
            $cStart = $conflict['start_time'];
            $cEnd = $conflict['end_time'] ? " - {$conflict['end_time']}" : '';
            echo json_encode([
                'success' => false,
                'conflict' => true,
                'message' => "Scheduling Conflict: {$cName} already has a confirmed session ({$conflict['reason']}) on {$cDate} at {$cStart}{$cEnd}. Please select an alternate time or counselor."
            ]);
            exit;
        }

        $success = $model->acceptAndSchedule($id, [
            'counselor_id' => $counselorId,
            'appointment_date' => $aptDate,
            'start_time' => $startTime,
            'end_time' => $endTime ?: date('h:i A', strtotime("$aptDate $startTime +1 hour")),
            'final_session_type' => $sessionType,
            'appointment_location' => $location,
            'meeting_link' => $meetingLink,
            'meeting_instructions' => $meetingInstructions,
            'counselor_notes' => $notes
        ]);

        if ($success) {
            $req = $model->getById($id);
            if ($req && !empty($req['tenant_id'])) {
                $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                $dateFormatted = date('M d, Y', strtotime($aptDate));
                $cName = $req['counselor_full_name'] ?? 'Your Assigned Counselor';
                
                $notif = new Notification();
                $notif->create(
                    (int)$req['tenant_id'],
                    'Counseling Appointment Confirmed',
                    "As-salamu alaykum. Your counseling appointment ($refNo) has been confirmed for {$dateFormatted} at {$startTime}. {$cName} has been assigned. Please visit your Counseling Portal to view details.",
                    'success'
                );
                $this->logAudit('DAWAH', 'ACCEPT_SCHEDULE_COUNSELING', "Confirmed counseling appointment ID: $id on $aptDate at $startTime with Counselor ID: $counselorId");
            }
        }

        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Appointment confirmed and counselor assigned successfully.' : 'Failed to finalize schedule.'
        ]);
        exit;
    }

    public function checkCounselorConflict(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $counselorId = (int)($input['counselor_id'] ?? 0);
        $date = trim($input['appointment_date'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $excludeId = (int)($input['exclude_id'] ?? 0);

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $model = new CounselingRequest();
        $conflict = $model->checkConflict($counselorId, $date, $startTime, $endTime, $excludeId ?: null);

        echo json_encode([
            'conflict' => (bool)$conflict,
            'details' => $conflict
        ]);
        exit;
    }

    public function reviewCounselingRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $model = new CounselingRequest();
        $success = $model->setUnderReview($id);

        echo json_encode(['success' => $success]);
        exit;
    }

    public function rejectCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $reason = trim($input['reason'] ?? ($input['rejection_reason'] ?? ''));

        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'A rejection reason is strictly required.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        $success = $model->rejectRequest($id, $reason);

        if ($success && $id) {
            $req = $model->getById($id);
            if ($req && !empty($req['tenant_id'])) {
                $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                $notif = new Notification();
                $notif->create(
                    (int)$req['tenant_id'],
                    'Counseling Request Update',
                    "Your counseling consultation request ($refNo) could not be scheduled at this time. Reason: {$reason}.",
                    'warning'
                );
                $this->logAudit('DAWAH', 'REJECT_COUNSELING', "Rejected counseling request ID: $id. Reason: $reason");
            }
        }

        echo json_encode(['success' => $success]);
        exit;
    }

    public function completeCounseling(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $notes = trim($input['notes'] ?? ($input['completed_notes'] ?? ''));

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/Notification.php';
        $model = new CounselingRequest();
        $req = $model->getById($id);

        if (!$req) {
            echo json_encode(['success' => false, 'message' => 'Request not found.']);
            exit;
        }

        // Check if appointment date has passed or is today; if in future, check for justification
        $aptDate = $req['appointment_date'] ?? $req['preferred_date'];
        $today = date('Y-m-d');
        if ($aptDate && $aptDate > $today && empty($notes)) {
            echo json_encode([
                'success' => false,
                'message' => 'This appointment is scheduled for a future date (' . date('M d, Y', strtotime($aptDate)) . '). Please provide an administrative justification in the notes to mark it completed ahead of time.'
            ]);
            exit;
        }

        $success = $model->markCompleted($id, $notes ?: 'Session successfully concluded.');

        if ($success) {
            if (!empty($req['tenant_id'])) {
                $refNo = '#MC-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                $notif = new Notification();
                $notif->create(
                    (int)$req['tenant_id'],
                    'Counseling Consultation Completed',
                    "Alhamdulillah. Your counseling consultation ($refNo) has been marked as Completed. May Allah grant you ease, peace of heart, and guidance.",
                    'success'
                );
                $this->logAudit('DAWAH', 'COMPLETE_COUNSELING', "Marked counseling session ID: $id as Completed");
            }
        }

        echo json_encode(['success' => $success]);
        exit;
    }

    public function getCounselorsList(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        header('Content-Type: application/json');

        $role = $_SESSION['role'] ?? '';
        $gender = ($role === 'Staff_Female') ? 'female' : 'male';

        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        $model = new CounselingRequest();
        $counselors = $model->getActiveCounselors($gender);

        echo json_encode(['success' => true, 'counselors' => $counselors]);
        exit;
    }

    public function approveCounseling(): void {
        // Kept for backward compatibility; redirects to acceptAndScheduleCounseling logic
        $this->acceptAndScheduleCounseling();
    }

    public function dawahAnalytics(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CounselingRequest.php';
        require_once BASE_PATH . '/app/models/IslamicEducation.php';
        require_once BASE_PATH . '/app/models/MarriageRequest.php';
        require_once BASE_PATH . '/app/models/ConversionRequest.php';
        
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $role = $_SESSION['role'] ?? '';
        $dawah_type = ($role === 'Staff_Female') ? 'female' : 'male';
        
        $counselingModel = new CounselingRequest();
        $eduModel = new IslamicEducation();
        $marriageModel = new MarriageRequest();
        $conversionModel = new ConversionRequest();

        // 1. Counseling Stats
        $counselingAnalytics = $counselingModel->getAnalytics($dawah_type);
        $counselingRequests = $counselingModel->getByGender($dawah_type);
        
        // 2. Education Stats
        $educationAnalytics = $eduModel->getAnalytics($dawah_type);
        
        // 3. Marriage Stats
        $marriageStats = $marriageModel->getAnalytics();

        // 4. Conversion Stats
        $conversionAll = $conversionModel->getAll();
        $convTotal = count($conversionAll);
        $convApproved = count(array_filter($conversionAll, fn($c) => ($c['status'] ?? '') === 'approved'));
        $convPending = count(array_filter($conversionAll, fn($c) => ($c['status'] ?? '') === 'pending'));

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
            'conversion' => [
                'total' => $convTotal,
                'approved' => $convApproved,
                'pending' => $convPending
            ],
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
            'total' => $burialAnalytics['total'],
            'burial' => $burialAnalytics['total'],
            'charity' => 0,
            'completed' => $burialAnalytics['death_cert_registered'] + $burialAnalytics['plot_assigned'],
            'pending' => $burialAnalytics['pending'],
            'death_cert_pending' => $burialAnalytics['death_cert_pending'],
            'death_cert_registered' => $burialAnalytics['death_cert_registered'],
            'plot_pending' => $burialAnalytics['plot_pending'],
            'plot_assigned' => $burialAnalytics['plot_assigned'],
            'available_plots' => $burialAnalytics['available_plots'],
            'occupied_plots' => $burialAnalytics['occupied_plots'],
            'total_plots' => $burialAnalytics['total_plots']
        ];

        // Format burial records for the dashboard table
        $records = [];
        foreach(array_slice($allBurials, 0, 20) as $b) {
            $records[] = [
                'id' => $b['ref_id'],
                'burial_id' => $b['id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'deceased_sex' => $b['deceased_sex'] ?? 'Male',
                'type' => 'burial',
                'request_type' => $b['request_type'] ?? 'both',
                'death_cert_requested' => (int)($b['death_cert_requested'] ?? 1),
                'death_cert_status' => $b['death_cert_status'] ?? 'Request Received',
                'death_cert_registry_no' => $b['death_cert_registry_no'] ?? '',
                'cemetery_plot_requested' => (int)($b['cemetery_plot_requested'] ?? 1),
                'cemetery_plot_status' => $b['cemetery_plot_status'] ?? 'Pending Review',
                'assigned_plot_number' => $b['assigned_plot_number'] ?? '',
                'assigned_section' => $b['assigned_section'] ?? '',
                'service_label' => ($b['request_type'] === 'both') ? 'Death Cert & Cemetery Plot' : (($b['request_type'] === 'death_certificate') ? 'Death Certificate' : 'Cemetery Plot'),
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'status' => $b['status'],
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
        $db = getDbConnection();

        $burials = $db->query("
            SELECT b.id, 'Burial Service' as service_type,
                   COALESCE(b.deceased_name, CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,''))) as name,
                   b.informant_contact as contact, b.submitted_at as date, b.status,
                   COALESCE(b.cemetery_preference, 'Local') as cemetery_pref,
                   COALESCE(b.assigned_plot_number, 'Pending') as plot_number
            FROM burial_requests b
            LEFT JOIN tenant_accounts u ON b.tenant_id = u.tenant_id
            ORDER BY b.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $funeralCases = $db->query("
            SELECT f.id, 'Funeral Case' as service_type,
                   CONCAT(COALESCE(f.deceased_first_name,''), ' ', COALESCE(f.deceased_last_name,'')) as name,
                   f.informant_contact as contact, f.reported_at as date, f.status,
                   COALESCE(f.cemetery, 'Local') as cemetery_pref,
                   COALESCE(f.grave_reference, 'Pending') as plot_number
            FROM funeral_cases f
            ORDER BY f.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $damayanRequests = array_merge($burials, $funeralCases);
        usort($damayanRequests, function($a, $b) {
            return strtotime($b['date'] ?? '1970-01-01') - strtotime($a['date'] ?? '1970-01-01');
        });

        $this->view('admin/mis_admin/damayan_records', [
            'active_page' => 'damayan_records',
            'damayanRequests' => $damayanRequests,
            'burialsCount' => count($burials),
            'funeralCasesCount' => count($funeralCases)
        ]);
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
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $rawBurials = $burialModel->getAll($filter, $search, false);
        $rawArchived = $burialModel->getAll('all', $search, true);
        $analytics = $burialModel->getAnalytics();
        $staffList = $burialModel->getAllStaff();
        $volunteers = $burialModel->getVolunteers();

        $records = [];
        $plots = $burialModel->getAllPlots();
        $availablePlots = $burialModel->getAvailablePlots();

        foreach($rawBurials as $b) {
            $rawStatus = trim($b['status'] ?? 'Pending');
            $rawLower = strtolower($rawStatus);
            $dcLower = strtolower(trim($b['death_cert_status'] ?? ''));

            if ($rawLower === 'accepted' || str_starts_with($rawLower, 'accept') || $rawLower === 'completed' || in_array($dcLower, ['awaiting authorized certificate preparation', 'registered', 'certified copy requested or received'])) {
                $displayStatus = 'Accepted';
            } elseif ($rawLower === 'rejected' || $dcLower === 'rejected') {
                $displayStatus = 'Rejected';
            } else {
                $displayStatus = 'Pending';
            }

            $certDateRaw = !empty($b['death_cert_registered_date']) ? $b['death_cert_registered_date'] : (!empty($b['copy_issued_at']) ? $b['copy_issued_at'] : (!empty($b['encoded_at']) ? $b['encoded_at'] : null));

            $records[] = [
                'id' => $b['ref_id'],
                'burial_id' => $b['id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'deceased_sex' => $b['deceased_sex'] ?? 'Male',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'submitted_at' => $b['submitted_at'],
                'cert_date' => $certDateRaw ? date('Y-m-d', strtotime($certDateRaw)) : null,
                'status' => $displayStatus,
                'status_class' => $this->getBurialStatusClass($displayStatus),
                'request_type' => $b['request_type'] ?? 'both',
                'cert_action_type' => $b['cert_action_type'] ?? 'register',
                'existing_cert_no' => $b['existing_cert_no'] ?? '',
                'copy_reason' => $b['copy_reason'] ?? '',
                'num_copies' => (int)($b['num_copies'] ?? 1),
                'existing_record_verified' => (int)($b['existing_record_verified'] ?? 0),
                'copy_issued_at' => $b['copy_issued_at'] ?? null,
                'copy_received_confirmed' => (int)($b['copy_received_confirmed'] ?? 0),
                'death_cert_requested' => (int)($b['death_cert_requested'] ?? 1),
                'death_cert_status' => $b['death_cert_status'] ?? 'Request Received',
                'death_cert_registry_no' => $b['death_cert_registry_no'] ?? '',
                'encoded_cert_id' => !empty($b['encoded_cert_id']) ? (int)$b['encoded_cert_id'] : null,
                'encoded_reg_no' => $b['encoded_reg_no'] ?? null,
                'encoded_deceased_name' => $b['encoded_deceased_name'] ?? null,
                'encoded_representative_name' => $b['encoded_representative_name'] ?? null,
                'encoded_by_name' => $b['encoded_by_name'] ?? null,
                'encoded_at' => $b['encoded_at'] ?? null,
                'is_encoded_linked' => !empty($b['encoded_cert_id']) || !empty($b['encoded_reg_no']),
                'cemetery_plot_requested' => (int)($b['cemetery_plot_requested'] ?? 1),
                'cemetery_plot_status' => $b['cemetery_plot_status'] ?? 'Pending Review',
                'assigned_cemetery_name' => $b['assigned_cemetery_name'] ?? '',
                'assigned_plot_number' => $b['assigned_plot_number'] ?? '',
                'assigned_section' => $b['assigned_section'] ?? '',
                'cemetery_preference' => $b['cemetery_preference'] ?? '',
                'ghusl_status' => $b['ghusl_status'] ?? 'Pending',
                'janazah_status' => $b['janazah_status'] ?? 'Pending',
                'transport_status' => $b['transport_status'] ?? 'Pending',
                'cemetery_status' => $b['cemetery_status'] ?? 'Pending'
            ];
        }

        $archivedRecords = [];
        foreach($rawArchived as $b) {
            $rawStatus = trim($b['status'] ?? 'Pending');
            $rawLower = strtolower($rawStatus);
            $dcLower = strtolower(trim($b['death_cert_status'] ?? ''));

            if ($rawLower === 'accepted' || str_starts_with($rawLower, 'accept') || $rawLower === 'completed' || in_array($dcLower, ['awaiting authorized certificate preparation', 'registered', 'certified copy requested or received'])) {
                $displayStatus = 'Accepted';
            } elseif ($rawLower === 'rejected' || $dcLower === 'rejected') {
                $displayStatus = 'Rejected';
            } else {
                $displayStatus = 'Pending';
            }

            $certDateRaw = !empty($b['death_cert_registered_date']) ? $b['death_cert_registered_date'] : (!empty($b['copy_issued_at']) ? $b['copy_issued_at'] : (!empty($b['encoded_at']) ? $b['encoded_at'] : null));

            $archivedRecords[] = [
                'id' => $b['ref_id'],
                'burial_id' => $b['id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'deceased_sex' => $b['deceased_sex'] ?? 'Male',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'submitted_at' => $b['submitted_at'],
                'cert_date' => $certDateRaw ? date('Y-m-d', strtotime($certDateRaw)) : null,
                'archived_at' => $b['archived_at'] ?? null,
                'archived_by' => $b['archived_by'] ?? null,
                'status' => $displayStatus,
                'status_class' => $this->getBurialStatusClass($displayStatus),
                'cert_action_type' => $b['cert_action_type'] ?? 'register',
            ];
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/burial', [
            'active_page' => 'burial',
            'dbUser' => $dbUser,
            'records' => $records,
            'archivedRecords' => $archivedRecords,
            'analytics' => $analytics,
            'staffList' => $staffList,
            'volunteers' => $volunteers,
            'plots' => $plots,
            'availablePlots' => $availablePlots,
            'currentFilter' => $filter,
            'searchQuery' => $search
        ]);
    }

    public function damayanBurialRequests(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/BurialRequest.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $burialModel = new BurialRequest();
        $filter = $_GET['filter'] ?? 'pending';
        $search = $_GET['search'] ?? '';
        $rawBurials = $burialModel->getAll($filter, $search);
        $analytics = $burialModel->getAnalytics();
        $staffList = $burialModel->getAllStaff();
        $volunteers = $burialModel->getVolunteers();
        $plots = $burialModel->getAllPlots();

        $records = [];
        foreach($rawBurials as $b) {
            $rawStatus = trim($b['status'] ?? 'Pending');
            $rawLower = strtolower($rawStatus);
            $dcLower = strtolower(trim($b['death_cert_status'] ?? ''));

            if ($rawLower === 'accepted' || str_starts_with($rawLower, 'accept') || $rawLower === 'completed' || in_array($dcLower, ['awaiting authorized certificate preparation', 'registered', 'certified copy requested or received'])) {
                $displayStatus = 'Accepted';
            } elseif ($rawLower === 'rejected' || $dcLower === 'rejected') {
                $displayStatus = 'Rejected';
            } else {
                $displayStatus = 'Pending';
            }

            $records[] = [
                'id' => $b['ref_id'],
                'burial_id' => $b['id'],
                'name' => trim(($b['first_name'] ?? 'Guest') . ' ' . ($b['last_name'] ?? '')),
                'deceased' => $b['deceased_name'] ?? 'N/A',
                'deceased_sex' => $b['deceased_sex'] ?? 'Male',
                'date' => date('Y-m-d', strtotime($b['submitted_at'])),
                'status' => $displayStatus,
                'status_class' => $this->getBurialStatusClass($displayStatus),
                'request_type' => $b['request_type'] ?? 'both',
                'cert_action_type' => $b['cert_action_type'] ?? 'register',
                'existing_cert_no' => $b['existing_cert_no'] ?? '',
                'copy_reason' => $b['copy_reason'] ?? '',
                'num_copies' => (int)($b['num_copies'] ?? 1),
                'existing_record_verified' => (int)($b['existing_record_verified'] ?? 0),
                'copy_issued_at' => $b['copy_issued_at'] ?? null,
                'copy_received_confirmed' => (int)($b['copy_received_confirmed'] ?? 0),
                'death_cert_status' => $b['death_cert_status'] ?? 'Request Received',
                'death_cert_registry_no' => $b['death_cert_registry_no'] ?? '',
                'scheduled_release_date' => $b['scheduled_release_date'] ?? null,
                'scheduled_release_time' => $b['scheduled_release_time'] ?? null,
                'release_location' => $b['release_location'] ?? 'Damayan Department',
                'release_instructions' => $b['release_instructions'] ?? '',
                'release_scheduled_by_name' => $b['release_scheduled_by_name'] ?? null,
                'certificate_released_at' => $b['certificate_released_at'] ?? null,
                'encoded_cert_id' => !empty($b['encoded_cert_id']) ? (int)$b['encoded_cert_id'] : null,
                'encoded_reg_no' => $b['encoded_reg_no'] ?? null,
                'encoded_deceased_name' => $b['encoded_deceased_name'] ?? null,
                'encoded_representative_name' => $b['encoded_representative_name'] ?? null,
                'encoded_by_name' => $b['encoded_by_name'] ?? null,
                'encoded_at' => $b['encoded_at'] ?? null,
                'is_encoded_linked' => !empty($b['encoded_cert_id']) || !empty($b['encoded_reg_no']),
                'cemetery_plot_requested' => (int)($b['cemetery_plot_requested'] ?? 1),
                'cemetery_plot_status' => $b['cemetery_plot_status'] ?? 'Pending Review',
                'assigned_cemetery_name' => $b['assigned_cemetery_name'] ?? '',
                'assigned_plot_number' => $b['assigned_plot_number'] ?? '',
                'assigned_section' => $b['assigned_section'] ?? '',
                'cemetery_preference' => $b['cemetery_preference'] ?? '',
                'ghusl_status' => $b['ghusl_status'] ?? 'Pending',
                'janazah_status' => $b['janazah_status'] ?? 'Pending',
                'transport_status' => $b['transport_status'] ?? 'Pending',
                'cemetery_status' => $b['cemetery_status'] ?? 'Pending',
                'verification_result' => $b['verification_result'] ?? 'Pending',
                'verification_remarks' => $b['verification_remarks'] ?? '',
                'verified_by' => $b['verified_by'] ?? '',
                'verified_at' => $b['verified_at'] ?? '',
                'duplicate_review_decision' => $b['duplicate_review_decision'] ?? '',
                'rejection_reason_category' => $b['rejection_reason_category'] ?? '',
                'rejection_specific_reason' => $b['rejection_specific_reason'] ?? '',
                'uploaded_document' => $b['uploaded_document'] ?? ''
            ];
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/burial_requests', [
            'active_page' => 'burial_requests',
            'dbUser' => $dbUser,
            'records' => $records,
            'analytics' => $analytics,
            'staffList' => $staffList,
            'volunteers' => $volunteers,
            'plots' => $plots,
            'currentFilter' => $filter,
            'searchQuery' => $search
        ]);
    }

    private function getBurialStatusClass(string $status): string {
        $st = strtolower(trim($status));
        if ($st === 'accepted') return 'badge-approved';
        if ($st === 'rejected') return 'badge-danger';
        if ($st === 'completed' || $st === 'burial completed') return 'badge-active';
        return 'badge-pending';
    }

    // ══════════════════════════════════════════════════════════════════════
    // CHARITY PROGRAMS MODULE (DAMAYAN / DAWAH COLLABORATIVE SYSTEM)
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Section 1: Charity Dashboard
     */
    public function damayanCharity(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);
        $staffName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Damayan Staff');

        $charity = new CharityProgram();
        $charity->seedInitialDataIfEmpty($staffName);

        $stats = $charity->getDashboardStats();
        $recentDonations = array_slice($charity->getAllDonations(), 0, 6);
        $recentDistributions = array_slice($charity->getDistributions(), 0, 6);
        $activityLogs = $charity->getActivityLogs(8);

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity', [
            'active_page' => 'charity',
            'dbUser' => $dbUser,
            'stats' => $stats,
            'recentDonations' => $recentDonations,
            'recentDistributions' => $recentDistributions,
            'activityLogs' => $activityLogs
        ]);
    }

    /**
     * Section 2: Donation Records
     */
    public function damayanCharityDonations(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $charity = new CharityProgram();
        $filters = [
            'search' => $_GET['search'] ?? '',
            'donation_type' => $_GET['type'] ?? '',
            'category' => $_GET['category'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $donations = $charity->getAllDonations($filters);
        $stats = $charity->getDashboardStats();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_donations', [
            'active_page' => 'charity_donations',
            'dbUser' => $dbUser,
            'donations' => $donations,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }

    /**
     * Save / Edit Donation Record (AJAX)
     */
    public function saveCharityDonation(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $action = $_POST['action'] ?? 'create';

        if ($action === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $res = $charity->updateDonation($id, $_POST, $staffName, $userId);
        } else {
            $res = $charity->recordDonation($_POST, $staffName, $userId);
        }

        echo json_encode($res);
        exit;
    }

    /**
     * Get Donation Detail (AJAX)
     */
    public function getCharityDonationDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        $donation = $charity->getDonationById($id);

        if (!$donation) {
            echo json_encode(['success' => false, 'error' => 'Donation record not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'donation' => $donation]);
        exit;
    }

    /**
     * Issue / Toggle Acknowledgment Receipt (AJAX)
     */
    public function toggleCharityAcknowledgment(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_POST['id'] ?? 0);
        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $res = $charity->issueAcknowledgment($id, $staffName, $userId);
        echo json_encode($res);
        exit;
    }

    /**
     * Verify / Update Donation Status (Digital, Cash, Food) (AJAX)
     */
    public function verifyCharityDonation(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_POST['id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Available');
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $res = $charity->verifyDonation($id, $status, $adminNotes, $staffName, $userId);
        echo json_encode($res);
        exit;
    }

    /**
     * Printable Acknowledgment Receipt View
     */
    public function printCharityAcknowledgment(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $id = (int)($_GET['id'] ?? 0);
        $charity = new CharityProgram();
        $donation = $charity->getDonationById($id);

        if (!$donation) {
            header('Location: ' . url('/admin/damayan/charity/donations'));
            exit;
        }

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_ack_print', [
            'dbUser' => $dbUser,
            'donation' => $donation
        ]);
    }

    /**
     * Section 3: Available Donations
     */
    public function damayanCharityAvailable(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $charity = new CharityProgram();
        $filters = [
            'search' => $_GET['search'] ?? '',
            'donation_type' => $_GET['type'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $availableDonations = $charity->getAvailableDonations($filters);
        $stats = $charity->getDashboardStats();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_available', [
            'active_page' => 'charity_available',
            'dbUser' => $dbUser,
            'availableDonations' => $availableDonations,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }

    /**
     * Section 4: Donation Allocation
     */
    public function damayanCharityAllocations(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $charity = new CharityProgram();
        $filters = [
            'search' => $_GET['search'] ?? '',
            'department' => $_GET['department'] ?? '',
            'status' => $_GET['status'] ?? '',
            'donation_ref' => $_GET['donation_ref'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $allocations = $charity->getAllocations($filters);
        $availableDonations = $charity->getAvailableDonations();
        $stats = $charity->getDashboardStats();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_allocations', [
            'active_page' => 'charity_allocations',
            'dbUser' => $dbUser,
            'allocations' => $allocations,
            'availableDonations' => $availableDonations,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }

    /**
     * Save Allocation (AJAX)
     */
    public function saveCharityAllocation(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $res = $charity->createAllocation($_POST, $staffName, $userId);
        echo json_encode($res);
        exit;
    }

    /**
     * Cancel Allocation (AJAX)
     */
    public function cancelCharityAllocation(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_POST['id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $res = $charity->cancelAllocation($id, $staffName, $reason, $userId);
        echo json_encode($res);
        exit;
    }

    /**
     * Get Allocation Detail (AJAX)
     */
    public function getCharityAllocationDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        $alloc = $charity->getAllocationById($id);

        if (!$alloc) {
            echo json_encode(['success' => false, 'error' => 'Allocation record not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'allocation' => $alloc]);
        exit;
    }

    /**
     * Section 5: Distribution Records
     */
    public function damayanCharityDistributions(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $charity = new CharityProgram();
        $filters = [
            'search' => $_GET['search'] ?? '',
            'department' => $_GET['department'] ?? '',
            'status' => $_GET['status'] ?? '',
            'donation_ref' => $_GET['donation_ref'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $distributions = $charity->getDistributions($filters);
        $awaitingAllocations = $charity->getUndistributedAllocations();
        $stats = $charity->getDashboardStats();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_distributions', [
            'active_page' => 'charity_distributions',
            'dbUser' => $dbUser,
            'distributions' => $distributions,
            'awaitingAllocations' => $awaitingAllocations,
            'filters' => $filters,
            'stats' => $stats
        ]);
    }

    /**
     * Save Distribution Record (AJAX with file upload)
     */
    public function saveCharityDistribution(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $docPath = null;
        if (!empty($_FILES['supporting_document']['name']) && $_FILES['supporting_document']['error'] === UPLOAD_ERR_OK) {
            $allowedExts = ['png', 'jpg', 'jpeg', 'webp', 'pdf'];
            $fileInfo = pathinfo($_FILES['supporting_document']['name']);
            $ext = strtolower($fileInfo['extension'] ?? '');

            if (!in_array($ext, $allowedExts)) {
                echo json_encode(['success' => false, 'error' => 'Supporting document must be an image (PNG, JPG, WEBP) or PDF file.']);
                exit;
            }

            if ($_FILES['supporting_document']['size'] > 10 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'Supporting document file size exceeds the 10MB limit.']);
                exit;
            }

            $uploadDir = BASE_PATH . '/public/uploads/charity/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = 'dist_proof_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['supporting_document']['tmp_name'], $destPath)) {
                $docPath = 'uploads/charity/' . $fileName;
            }
        }

        $res = $charity->recordDistribution($_POST, $docPath, $staffName, $userId);
        echo json_encode($res);
        exit;
    }

    /**
     * Get Distribution Detail (AJAX)
     */
    public function getCharityDistributionDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/CharityProgram.php';
        $charity = new CharityProgram();

        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        $dist = $charity->getDistributionById($id);

        if (!$dist) {
            echo json_encode(['success' => false, 'error' => 'Distribution record not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'distribution' => $dist]);
        exit;
    }

    /**
     * Reports View (Printable / Exportable Summary)
     */
    public function damayanCharityReports(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/CharityProgram.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $charity = new CharityProgram();
        $filters = [
            'date_from' => $_GET['date_from'] ?? date('Y-01-01'),
            'date_to' => $_GET['date_to'] ?? date('Y-m-d')
        ];

        $donations = $charity->getAllDonations($filters);
        $allocations = $charity->getAllocations($filters);
        $distributions = $charity->getDistributions($filters);
        $stats = $charity->getDashboardStats();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/charity_reports', [
            'active_page' => 'charity_reports',
            'dbUser' => $dbUser,
            'filters' => $filters,
            'donations' => $donations,
            'allocations' => $allocations,
            'distributions' => $distributions,
            'stats' => $stats
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

    public function getBurialDetails(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing ID']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $burial = is_numeric($id) ? $model->findById((int)$id) : $model->findByRefId($id);

        if (!$burial) {
            echo json_encode(['success' => false, 'error' => 'Burial case not found']);
            return;
        }

        $gender = $burial['deceased_sex'] ?? 'Male';
        $volunteers = $model->getVolunteers($gender);
        $allVolunteers = $model->getVolunteers();
        $staffList = $model->getAllStaff();
        $availablePlots = $model->getAvailablePlots();
        $allPlots = $model->getAllPlots();
        $auditLogs = $model->getAuditLogs((int)$burial['id']);
        $documents = $model->getDocuments((int)$burial['id']);
        $duplicates = $model->checkPotentialDuplicates($burial);
        $scheduleHistory = $model->getScheduleHistory((int)$burial['id']);

        echo json_encode([
            'success' => true,
            'burial' => $burial,
            'duplicates' => $duplicates,
            'gender_volunteers' => $volunteers,
            'all_volunteers' => $allVolunteers,
            'staff_list' => $staffList,
            'available_plots' => $availablePlots,
            'all_plots' => $allPlots,
            'audit_logs' => $auditLogs,
            'documents' => $documents,
            'schedule_history' => $scheduleHistory
        ]);
        return;
    }

    public function recordDeathCertVerification(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $result = trim($input['result'] ?? '');
        $remarks = trim($input['remarks'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->recordVerification($id, $result, $remarks, $staffName);
        echo json_encode($res);
        return;
    }

    public function reviewDuplicateDecision(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $decision = trim($input['decision'] ?? '');
        $notes = trim($input['notes'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->reviewDuplicate($id, $decision, $notes, $staffName);
        echo json_encode($res);
        return;
    }

    public function acceptBurialRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $staffId = !empty($input['staff_id']) ? (int)$input['staff_id'] : (int)$_SESSION['user_id'];
        $staffName = trim($input['staff_name'] ?? ($_SESSION['name'] ?? 'Damayan Coordinator'));
        $notes = trim($input['notes'] ?? '');

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->acceptRequest($id, $staffId, $staffName, $notes, true);

        echo json_encode($res);
        return;
    }

    public function requestBurialInfo(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $explanation = trim($input['explanation'] ?? ($input['notes'] ?? ''));
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($explanation)) {
            echo json_encode(['success' => false, 'error' => 'Please provide the details or documents required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->requestAdditionalInfo($id, $explanation, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function rejectBurialRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $reasonCategory = trim($input['reason_category'] ?? ($input['reason'] ?? ''));
        $specificReason = trim($input['specific_reason'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        if (empty($reasonCategory)) {
            echo json_encode(['success' => false, 'error' => 'A rejection reason is required.']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->rejectRequest($id, $reasonCategory, $specificReason, $staffName);

        echo json_encode($res);
        return;
    }

    public function deleteBurialRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';
        $ok = $model->archiveRequest($id, $staffName);

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Burial record moved to archive.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to archive burial record.']);
        }
        return;
    }

    public function restoreBurialRequest(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $ok = $model->restoreRequest($id, $staffName);

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Burial record restored to active records.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to restore burial record.']);
        }
        return;
    }

    public function markBurialCertificateReceived(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = $input['id'] ?? ($input['ref_id'] ?? 0);
        $date = !empty($input['received_date']) ? trim($input['received_date']) : date('Y-m-d');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing Request ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->markCertificateReceived($id, $date, $staffName);
        echo json_encode($res);
        return;
    }

    public function assignBurialStaff(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffId = (int)($input['staff_id'] ?? 0);
        $staffName = trim($input['staff_name'] ?? '');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->assignStaff($id, $staffId, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function updateBurialDeceased(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        $dob = trim($input['date_of_birth'] ?? ($input['dob'] ?? ''));
        $dod = trim($input['date_of_death'] ?? ($input['dod'] ?? ''));
        if (!empty($dob) && !empty($dod)) {
            if (strtotime($dob) >= strtotime($dod)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid date of birth. The date of birth must be earlier than the date of death.',
                    'message' => 'Invalid date of birth. The date of birth must be earlier than the date of death.'
                ]);
                return;
            }
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateDeceasedRecord($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function verifyExistingDeathRecord(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $recordId = !empty($input['encoded_cert_id']) ? (int)$input['encoded_cert_id'] : (!empty($input['deceased_id']) ? (int)$input['deceased_id'] : null);
        $source = trim($input['source'] ?? (!empty($input['encoded_cert_id']) ? 'encoded' : 'deceased_records'));
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $result = $model->verifyExistingRecord($id, $recordId, $staffName, $source);

        echo json_encode($result);
        return;
    }

    /**
     * Lookup encoded certificate by registration number
     */
    public function lookupEncodedCertificate(): void {
        header('Content-Type: application/json');
        $reg = trim($_GET['reg'] ?? ($_POST['reg'] ?? ''));
        if (empty($reg)) {
            echo json_encode(['success' => false, 'found' => false]);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $record = $model->findEncodedByRegistrationNo($reg);

        if ($record) {
            echo json_encode([
                'success' => true,
                'found' => true,
                'record' => [
                    'id' => $record['id'],
                    'registration_number' => $record['registration_number'],
                    'deceased_name' => $record['deceased_name'],
                    'representative_name' => $record['representative_name'] ?? '',
                    'source' => 'Damayan Encoded Archive',
                    'encoded_by_name' => $record['encoded_by_name'] ?? 'Damayan Staff',
                    'encoded_at' => $record['encoded_at'] ?? null
                ]
            ]);
            return;
        }

        // Also check official registered records in burial_requests and death_records
        $dup = $model->isDuplicateDeathCertRegNo($reg);
        if ($dup) {
            echo json_encode([
                'success' => true,
                'found' => true,
                'record' => [
                    'id' => null,
                    'registration_number' => $dup['registration_number'],
                    'deceased_name' => $dup['deceased_name'],
                    'representative_name' => $dup['representative_name'] ?? '',
                    'source' => $dup['source'] ?? 'Registered Department Records',
                    'encoded_by_name' => $dup['encoded_by'] ?? 'ISCAG Damayan System',
                    'encoded_at' => $dup['date'] ?? null
                ]
            ]);
            return;
        }

        echo json_encode(['success' => true, 'found' => false]);
        return;
    }

    public function issueCertificateCopy(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $copyRef = trim($input['copy_ref'] ?? '');
        $remarks = trim($input['remarks'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id) || empty($copyRef)) {
            echo json_encode(['success' => false, 'error' => 'Case ID and Certificate Copy Reference are required']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $result = $model->issueCertificateCopy($id, $copyRef, $staffName, $remarks);

        echo json_encode($result);
        return;
    }

    public function searchDeceasedRecords(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $records = $model->searchDeceasedRecords($q);

        echo json_encode(['success' => true, 'records' => $records]);
        return;
    }

    public function updateBurialGhusl(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateGhusl($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function updateBurialKafan(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateKafan($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function updateBurialJanazah(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateJanazah($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function updateBurialTransport(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateTransport($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function updateBurialCemetery(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateCemetery($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function completeBurialCase(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->completeBurialCase($id, $input, $staffName);

        echo json_encode(['success' => $success]);
        return;
    }

    public function getBurialVolunteers(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $gender = $_GET['gender'] ?? null;
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $volunteers = $model->getVolunteers($gender);
        echo json_encode(['success' => true, 'volunteers' => $volunteers]);
        return;
    }

    public function updateVolunteerStatus(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $status = trim($input['status'] ?? 'Available');
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $success = $model->updateVolunteerStatus($id, $status);
        echo json_encode(['success' => $success]);
        return;
    }

    public function updateDeathCertStatus(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $status = trim($input['death_cert_status'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id) || empty($status)) {
            echo json_encode(['success' => false, 'error' => 'Missing ID or status']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->updateDeathCertStatus($id, $status, $input, $staffName);

        echo json_encode($res);
        return;
    }

    /**
     * Schedule Death Certificate Release (Claiming Appointment)
     */
    public function scheduleBurialRelease(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['case_id'] ?? 0));
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $adminName = trim($_SESSION['name'] ?? 'Damayan Admin');

        $result = $model->scheduleCertificateRelease($id, $input, $adminId, $adminName);
        echo json_encode($result);
        return;
    }

    /**
     * Reschedule Death Certificate Release
     */
    public function rescheduleBurialRelease(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['case_id'] ?? 0));
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $adminName = trim($_SESSION['name'] ?? 'Damayan Admin');

        $result = $model->rescheduleCertificateRelease($id, $input, $adminId, $adminName);
        echo json_encode($result);
        return;
    }

    /**
     * Cancel Death Certificate Claiming Appointment
     */
    public function cancelBurialRelease(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['case_id'] ?? 0));
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $adminName = trim($_SESSION['name'] ?? 'Damayan Admin');

        $result = $model->cancelCertificateRelease($id, $input, $adminId, $adminName);
        echo json_encode($result);
        return;
    }

    /**
     * Mark Death Certificate as Physically Released / Completed
     */
    public function markBurialReleased(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['case_id'] ?? 0));
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $adminId = (int)($_SESSION['user_id'] ?? 0);
        $adminName = trim($_SESSION['name'] ?? 'Damayan Admin');

        $result = $model->markCertificateAsReleased($id, $input, $adminId, $adminName);
        echo json_encode($result);
        return;
    }

    /**
     * Set death certificate department custody status
     */
    public function setCertificateCustody(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['burial_id'] ?? 0));
        $inDept = !empty($input['in_department']) && ($input['in_department'] === true || $input['in_department'] === '1' || $input['in_department'] === 1);
        $notes = trim($input['notes'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Admin';

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid certificate request ID.']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $result = $model->setCertificateInDepartment($id, $inDept, $staffName, $notes);

        echo json_encode($result);
        return;
    }

    /**
     * Update Burial / Death Certificate Stage
     */
    public function updateBurialStage(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? ($input['case_id'] ?? 0));
        $stage = trim($input['stage'] ?? ($input['status'] ?? ''));

        if (empty($id) || empty($stage)) {
            echo json_encode(['success' => false, 'error' => 'Missing case ID or stage']); return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');
        $result = $model->updateDeathCertStatus($id, $stage, $input, $staffName);
        echo json_encode($result);
        return;
    }

    public function updateCemeteryPlot(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $status = trim($input['cemetery_plot_status'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($id) || empty($status)) {
            echo json_encode(['success' => false, 'error' => 'Missing ID or status']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->updateCemeteryPlot($id, $status, $input, $staffName);

        echo json_encode($res);
        return;
    }

    public function getAvailableCemeteryPlots(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $plots = $model->getAvailablePlots();
        echo json_encode(['success' => true, 'plots' => $plots]);
        return;
    }

    public function addCemeteryPlot(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->addPlot($input, $staffName);

        echo json_encode($res);
        return;
    }

    public function updateCemeteryPlotInventory(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']); return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $plotId = (int)($input['plot_id'] ?? 0);
        $status = trim($input['status'] ?? '');
        $notes = trim($input['notes'] ?? '');
        $staffName = $_SESSION['name'] ?? 'Damayan Staff';

        if (empty($plotId) || empty($status)) {
            echo json_encode(['success' => false, 'error' => 'Missing plot ID or status']);
            return;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $res = $model->updatePlotInventory($plotId, $status, $notes, $staffName);

        echo json_encode($res);
        return;
    }

    public function updateBurialStatus(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $id = $input['id'] ?? null;
            $status = $input['status'] ?? null;
            $notes = $input['notes'] ?? '';
            $staffName = $_SESSION['name'] ?? 'Damayan Staff';
            
            require_once BASE_PATH . '/app/models/BurialRequest.php';
            $model = new BurialRequest();
            $success = $model->updateStatus($id, $status, $notes, $staffName);

            echo json_encode(['success' => $success]);
            return;
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
        
        $selectedYear = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        $burialStats = $burialModel->getAnalytics($selectedYear);
        $charityStats = $charityModel->getAnalytics($selectedYear);
        
        // Dynamic year list (from current year down to at least 2024)
        $currentYear = (int)date('Y');
        $availableYears = array_unique([$currentYear, $currentYear - 1, $currentYear - 2, 2026, 2025, 2024]);
        rsort($availableYears);

        // Fetch recent combined Damayan operations for the connected activity table
        $recentRecords = [];
        try {
            $db = getDbConnection();
            
            $r1 = $db->query("SELECT id, ref_id as reference_no, deceased_name as subject, status as record_status, submitted_at as activity_date FROM burial_requests ORDER BY submitted_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($r1 as $row) {
                $recentRecords[] = [
                    'source' => 'burial_request',
                    'id' => $row['id'],
                    'reference_no' => $row['reference_no'],
                    'subject' => $row['subject'],
                    'record_status' => $row['record_status'],
                    'activity_date' => $row['activity_date'],
                    'target_url' => '/admin/damayan/burial-requests'
                ];
            }

            $r2 = $db->query("SELECT id, registration_number as reference_no, deceased_name as subject, created_at as activity_date FROM encoded_death_certificates ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($r2 as $row) {
                $recentRecords[] = [
                    'source' => 'death_cert',
                    'id' => $row['id'],
                    'reference_no' => $row['reference_no'],
                    'subject' => $row['subject'],
                    'record_status' => 'Registered',
                    'activity_date' => $row['activity_date'],
                    'target_url' => '/admin/damayan/encoded-certificates'
                ];
            }

            $r3 = $db->query("SELECT id, registration_number as reference_no, deceased_name as subject, status as record_status, sent_at as activity_date FROM dawah_death_certificates ORDER BY sent_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($r3 as $row) {
                $recentRecords[] = [
                    'source' => 'dawah_transmittal',
                    'id' => $row['id'],
                    'reference_no' => $row['reference_no'],
                    'subject' => $row['subject'],
                    'record_status' => $row['record_status'],
                    'activity_date' => $row['activity_date'],
                    'target_url' => '/admin/dawah/male/death-certificates'
                ];
            }

            $r4 = $db->query("SELECT id, donation_ref as reference_no, donor_name, amount, status as record_status, date_received as activity_date FROM charity_donations_records ORDER BY date_received DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($r4 as $row) {
                $donor = !empty($row['donor_name']) ? $row['donor_name'] : 'Anonymous';
                $recentRecords[] = [
                    'source' => 'charity_donation',
                    'id' => $row['id'],
                    'reference_no' => $row['reference_no'],
                    'subject' => $donor . ' (₱' . number_format((float)$row['amount']) . ')',
                    'record_status' => !empty($row['record_status']) ? $row['record_status'] : 'Received',
                    'activity_date' => $row['activity_date'],
                    'target_url' => '/admin/damayan/charity/donations'
                ];
            }

            usort($recentRecords, function($a, $b) {
                $ta = !empty($a['activity_date']) ? strtotime($a['activity_date']) : 0;
                $tb = !empty($b['activity_date']) ? strtotime($b['activity_date']) : 0;
                return $tb <=> $ta;
            });

            $recentRecords = array_slice($recentRecords, 0, 10);
        } catch (\Throwable $e) {
            error_log("damayanAnalytics recentRecords error: " . $e->getMessage());
        }

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/analytics', [
            'active_page' => 'analytics',
            'dbUser' => $dbUser,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'burial' => $burialStats,
            'charity' => $charityStats,
            'recentRecords' => $recentRecords
        ]);
    }

    /* ======================================================================
     *  FUNERAL CASE MANAGEMENT (ADMIN)
     * ==================================================================== */

    /** Admin funeral cases list */
    public function damayanFuneralCases(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/FuneralCase.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $fcModel = new FuneralCase();
        $statusFilter = $_GET['status'] ?? '';
        $allCases = $fcModel->getAll($statusFilter);
        $analytics = $fcModel->getAnalytics();

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/funeral_cases', [
            'active_page' => 'funeral_cases',
            'dbUser' => $dbUser,
            'cases' => $allCases,
            'analytics' => $analytics,
            'currentFilter' => $statusFilter
        ]);
    }

    /** Admin funeral case detail */
    public function damayanFuneralCaseDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        require_once BASE_PATH . '/app/models/User.php';
        require_once BASE_PATH . '/app/models/FuneralCase.php';

        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $fcModel = new FuneralCase();
        $caseId = (int)($_GET['id'] ?? 0);
        $caseDetail = $fcModel->findById($caseId);

        if (!$caseDetail) {
            header('Location: ' . url('/admin/damayan/funeral-cases'));
            exit;
        }

        $documents  = $fcModel->getDocuments($caseId);
        $psaRequest = $fcModel->getPsaRequest($caseId);
        $logs       = $fcModel->getLogs($caseId);

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/funeral_case_detail', [
            'active_page' => 'funeral_cases',
            'dbUser' => $dbUser,
            'caseDetail' => $caseDetail,
            'documents' => $documents,
            'psaRequest' => $psaRequest,
            'logs' => $logs
        ]);
    }

    /** Review / verify a funeral case (POST, JSON) */
    public function reviewFuneralCase(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $caseId = (int)($input['case_id'] ?? 0);
        $action = $input['action'] ?? '';
        $reason = $input['reason'] ?? '';

        $case = $fcModel->findById($caseId);
        if (!$case) {
            echo json_encode(['success' => false, 'error' => 'Case not found']); exit;
        }

        $statusMap = [
            'verify'    => 'Information Verified',
            'review'    => 'Under Review',
            'incomplete'=> 'Requirements Incomplete',
            'ready'     => 'Ready for Registration',
            'submit_lcro' => 'Submitted to LCRO',
            'registered'=> 'Registered',
            'correction'=> 'Returned for Correction',
            'reject'    => 'Rejected',
            'complete'  => 'Completed'
        ];

        if (!isset($statusMap[$action])) {
            echo json_encode(['success' => false, 'error' => 'Invalid action']); exit;
        }

        $newStatus = $statusMap[$action];
        $ok = $fcModel->updateStatus($caseId, $newStatus, $reason ?: null);

        if ($ok) {
            // Update registration info if provided
            if (!empty($input['registration_reference']) || !empty($input['registration_place'])) {
                $fcModel->updateRegistration($caseId, [
                    'registration_reference' => $input['registration_reference'] ?? '',
                    'registration_place' => $input['registration_place'] ?? '',
                    'assigned_admin_id' => $_SESSION['user_id'] ?? '',
                    'assigned_admin_name' => $_SESSION['name'] ?? ''
                ]);
            }
            AuditLogger::log('FUNERAL', 'CASE_' . strtoupper($action),
                "Case #{$case['case_number']} — Status changed to: {$newStatus}" . ($reason ? " — {$reason}" : ''));
        }

        echo json_encode(['success' => $ok, 'new_status' => $newStatus]);
        exit;
    }

    /** Update funeral case general fields (POST, JSON) */
    public function updateFuneralCase(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $caseId = (int)($input['case_id'] ?? 0);
        $ok = $fcModel->updateRegistration($caseId, $input);

        echo json_encode(['success' => $ok]);
        exit;
    }

    /** Update PSA request status (POST, JSON) */
    public function updateFuneralPsa(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $psaId = (int)($input['psa_id'] ?? 0);
        if (!$psaId) {
            echo json_encode(['success' => false, 'error' => 'Missing PSA ID']); exit;
        }

        $ok = $fcModel->updatePsaRequest($psaId, $input);

        if ($ok && isset($input['case_id'])) {
            $fcModel->addLog((int)$input['case_id'], null, $_SESSION['user_id'] ?? '',
                'PSA_STATUS_UPDATE', 'PSA',
                "PSA request updated — Status: " . ($input['request_status'] ?? 'N/A'),
                $input['request_status'] ?? '');

            // If PSA is received, mark case as completed
            if (($input['request_status'] ?? '') === 'Received by Requester') {
                $fcModel->updateStatus((int)$input['case_id'], 'Completed');
            }

            AuditLogger::log('FUNERAL', 'PSA_UPDATE',
                "PSA request updated for case — Status: " . ($input['request_status'] ?? 'N/A'));
        }

        echo json_encode(['success' => $ok]);
        exit;
    }

    /** Review a document (approve/reject) (POST, JSON) */
    public function reviewFuneralDoc(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]); exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $docId  = (int)($input['doc_id'] ?? 0);
        $status = $input['status'] ?? '';
        $reason = $input['reason'] ?? '';

        $updateData = ['upload_status' => $status];
        if ($status === 'Verified') {
            $updateData['verified_by'] = $_SESSION['name'] ?? 'Admin';
        }
        if ($status === 'Rejected' || $status === 'Resubmission Required') {
            $updateData['rejection_reason'] = $reason;
        }
        if (isset($input['remarks'])) {
            $updateData['remarks'] = $input['remarks'];
        }

        $ok = $fcModel->updateDocument($docId, $updateData);

        if ($ok && isset($input['case_id'])) {
            $fcModel->addLog((int)$input['case_id'], null, $_SESSION['user_id'] ?? '',
                'DOC_REVIEW', 'DOCUMENTS',
                "Document #{$docId} marked as {$status}" . ($reason ? " — {$reason}" : ''),
                $status);
        }

        echo json_encode(['success' => $ok]);
        exit;
    }

    /** Serve a funeral document file (admin) */
    public function serveFuneralDoc(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
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

    /* ====================================================================
     *  CERTIFICATE TRACKING & RELEASE SCHEDULING (DAMAYAN ADMIN)
     * ==================================================================== */

    /** Certificate Tracking List View */
    public function damayanCertificateTracking(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Location: ' . url('/admin/damayan/burial-requests'));
        exit;
    }

    /** Certificate Details (JSON for tracking panel) */
    public function damayanCertificateDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $id = (int)($_GET['id'] ?? 0);
        $case = $fcModel->findById($id);

        if (!$case) {
            echo json_encode(['success' => false, 'error' => 'Certificate request not found']);
            exit;
        }

        $logs = $fcModel->getLogs($id);
        $documents = $fcModel->getDocuments($id);
        $staffList = $fcModel->getStaffList();

        // Also fetch requester user info if tenant_id exists
        $requester = null;
        if (!empty($case['tenant_id'])) {
            require_once BASE_PATH . '/app/models/User.php';
            $userModel = new User();
            $requester = $userModel->findById((int)$case['tenant_id']);
        }

        echo json_encode([
            'success'   => true,
            'case'      => $case,
            'logs'      => $logs,
            'documents' => $documents,
            'staffList' => $staffList,
            'requester' => $requester
        ]);
        exit;
    }

    /** Update certificate stage: Under Review, Processing, Ready for Release, Cancelled */
    public function updateCertificateStatus(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $caseId = (int)($input['case_id'] ?? 0);
        $status = trim($input['status'] ?? '');
        $reason = trim($input['reason'] ?? '');

        if (!$caseId || !$status) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $adminId = $_SESSION['user_id'] ?? null;
        $adminName = $_SESSION['name'] ?? 'Admin Staff';

        $ok = $fcModel->updateCertificateStatus($caseId, $status, $reason ?: null, $adminId, $adminName);

        echo json_encode(['success' => $ok]);
        exit;
    }

    /** Schedule Certificate Release */
    public function scheduleCertificateRelease(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $caseId = (int)($input['case_id'] ?? 0);

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $adminId = $_SESSION['user_id'] ?? null;
        $adminName = $_SESSION['name'] ?? 'Admin Staff';

        $result = $fcModel->scheduleRelease($caseId, $input, $adminId, $adminName);
        echo json_encode($result);
        exit;
    }

    /** Reschedule Certificate Release */
    public function rescheduleCertificateRelease(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $caseId = (int)($input['case_id'] ?? 0);

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $adminId = $_SESSION['user_id'] ?? null;
        $adminName = $_SESSION['name'] ?? 'Admin Staff';

        $result = $fcModel->rescheduleRelease($caseId, $input, $adminId, $adminName);
        echo json_encode($result);
        exit;
    }

    /** Mark Certificate as Released */
    public function markCertificateReleased(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $caseId = (int)($input['case_id'] ?? 0);

        require_once BASE_PATH . '/app/models/FuneralCase.php';
        $fcModel = new FuneralCase();

        $adminId = $_SESSION['user_id'] ?? null;
        $adminName = $_SESSION['name'] ?? 'Admin Staff';

        $result = $fcModel->markAsReleased($caseId, $input, $adminId, $adminName);
        echo json_encode($result);
        exit;
    }

    /**
     * View / List Encoded Death Certificates
     */
    public function damayanEncodedCertificates(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        $search = trim($_GET['search'] ?? '');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $records = $model->getEncodedDeathCertificates($search);
        $totalCount = $model->countEncodedDeathCertificates($search);

        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'records' => $records, 'total' => $totalCount]);
            exit;
        }

        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Damayan_Department/encoded_certificates', [
            'active_page' => 'encoded_certificates',
            'dbUser' => $dbUser,
            'records' => $records,
            'totalCount' => $totalCount,
            'search' => $search
        ]);
    }

    /**
     * Save an encoded death certificate
     * Only 3 fields required: registration_number, deceased_name, representative_name
     */
    public function saveEncodedCertificate(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $regNo = trim($input['registration_number'] ?? ($input['registry_number'] ?? ''));
        $firstName = trim($input['first_name'] ?? '');
        $middleName = trim($input['middle_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $deceasedName = trim($input['deceased_name'] ?? '');
        if (empty($deceasedName) && (!empty($firstName) || !empty($lastName))) {
            $deceasedName = trim($firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName);
        }

        if (empty($regNo)) {
            echo json_encode(['success' => false, 'error' => 'Registry Number is required.']);
            exit;
        }
        if (empty($deceasedName)) {
            echo json_encode(['success' => false, 'error' => 'Full Name of the Deceased (First and Last Name) is required.']);
            exit;
        }

        $dodDay = !empty($input['date_of_death_day']) ? (int)$input['date_of_death_day'] : null;
        $dodMonth = !empty($input['date_of_death_month']) ? (int)$input['date_of_death_month'] : null;
        $dodYear = !empty($input['date_of_death_year']) ? (int)$input['date_of_death_year'] : null;
        $dod = ($dodYear && $dodMonth && $dodDay) ? sprintf('%04d-%02d-%02d', $dodYear, $dodMonth, $dodDay) : (!empty($input['date_of_death']) ? date('Y-m-d', strtotime($input['date_of_death'])) : null);

        $dobDay = !empty($input['date_of_birth_day']) ? (int)$input['date_of_birth_day'] : null;
        $dobMonth = !empty($input['date_of_birth_month']) ? (int)$input['date_of_birth_month'] : null;
        $dobYear = !empty($input['date_of_birth_year']) ? (int)$input['date_of_birth_year'] : null;
        $dob = ($dobYear && $dobMonth && $dobDay) ? sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay) : (!empty($input['date_of_birth']) ? date('Y-m-d', strtotime($input['date_of_birth'])) : null);

        if (!empty($dob) && !empty($dod)) {
            if (strtotime($dob) >= strtotime($dod)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid date of birth. The date of birth must be earlier than the date of death.',
                    'message' => 'Invalid date of birth. The date of birth must be earlier than the date of death.'
                ]);
                exit;
            }
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $staffId = (int)($_SESSION['user_id'] ?? 0);
        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');

        $input['registration_number'] = $regNo;
        $input['deceased_name'] = $deceasedName;
        $input['first_name'] = $firstName;
        $input['middle_name'] = $middleName;
        $input['last_name'] = $lastName;

        $result = $model->encodeDeathCertificate($input, $staffId, $staffName);

        echo json_encode($result);
        exit;
    }

    /**
     * Check if a registration number already exists in database
     */
    public function checkDuplicateRegistrationNo(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        $regNo = trim($_GET['reg'] ?? ($_POST['reg'] ?? ''));
        if (empty($regNo)) {
            echo json_encode(['exists' => false]);
            exit;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $dup = $model->isDuplicateDeathCertRegNo($regNo);

        if ($dup) {
            echo json_encode([
                'exists' => true,
                'message' => "Registration #{$regNo} already exists in {$dup['source']} (Deceased: {$dup['deceased_name']}).",
                'duplicate' => $dup
            ]);
        } else {
            echo json_encode(['exists' => false]);
        }
        exit;
    }

    /**
     * Get single encoded death certificate details
     */
    public function getEncodedCertificateDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();
        $record = $model->getEncodedDeathCertificateById($id);

        if (!$record) {
            echo json_encode(['success' => false, 'error' => 'Encoded record not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'record' => $record]);
        exit;
    }

    /**
     * Send Death Certificate and file to Male Da'wah
     */
    public function sendDeathCertToDawah(): void {
        Auth::protectRole(['Admin', 'Staff_Damayan']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        $input = $_POST;
        if (empty($input)) {
            $rawJson = json_decode(file_get_contents('php://input'), true);
            if (is_array($rawJson)) {
                $input = $rawJson;
            }
        }

        $regNo = trim($input['registration_number'] ?? '');
        $deceasedName = trim($input['deceased_name'] ?? '');

        if (empty($regNo) || empty($deceasedName)) {
            echo json_encode(['success' => false, 'error' => 'Registration number and deceased full name are required.']);
            exit;
        }

        // Handle file upload if provided
        $filePath = null;
        $fileName = null;
        if (!empty($_FILES['cert_file']) && $_FILES['cert_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = BASE_PATH . '/public/uploads/death_certificates';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $origName = basename($_FILES['cert_file']['name']);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $safeExts = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'doc'];
            if (!in_array($ext, $safeExts)) {
                echo json_encode(['success' => false, 'error' => 'Invalid file format. Supported formats: PDF, JPG, PNG, DOCX.']);
                exit;
            }
            $cleanReg = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $regNo);
            $newFileName = 'DC_File_' . $cleanReg . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . '/' . $newFileName;
            if (move_uploaded_file($_FILES['cert_file']['tmp_name'], $destPath)) {
                $filePath = 'uploads/death_certificates/' . $newFileName;
                $fileName = $origName;
            }
        }

        if (empty($fileName)) {
            $cleanReg = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $regNo);
            $fileName = "Death_Certificate_Transmittal_{$cleanReg}.html";
        }

        $staffId = (int)($_SESSION['user_id'] ?? 0);
        $staffName = trim($_SESSION['name'] ?? 'Damayan Staff');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $model = new BurialRequest();

        $input['file_path'] = $filePath;
        $input['file_name'] = $fileName;

        $res = $model->sendDeathCertificateToDawah($input, $staffId, $staffName);
        echo json_encode($res);
        exit;
    }

    /**
     * View Received Death Certificates for Male Da'wah
     */
    public function dawahMaleDeathCertificates(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'all');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();

        $records = $burialModel->getDawahDeathCertificates($search, $status);
        $totalCount = $burialModel->countDawahDeathCertificates($search);
        $newCount = $burialModel->countDawahDeathCertificates($search, 'Received');
        $reviewedCount = $burialModel->countDawahDeathCertificates($search, 'Reviewed');
        $completedCount = $burialModel->countDawahDeathCertificates($search, 'Completed');

        // All-time totals for the metric insight cards
        $allTotal = $burialModel->countDawahDeathCertificates();
        $allNew = $burialModel->countDawahDeathCertificates('', 'Received');
        $allReviewed = $burialModel->countDawahDeathCertificates('', 'Reviewed');
        $allCompleted = $burialModel->countDawahDeathCertificates('', 'Completed');

        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/received_death_certificates', [
            'active_page'    => 'death_certificates',
            'dawah_type'     => 'male',
            'dbUser'         => $dbUser,
            'records'        => $records,
            'totalCount'     => $totalCount,
            'newCount'       => $newCount,
            'reviewedCount'  => $reviewedCount,
            'completedCount' => $completedCount,
            'allTotal'       => $allTotal,
            'allNew'         => $allNew,
            'allReviewed'    => $allReviewed,
            'allCompleted'   => $allCompleted,
            'search'         => $search,
            'status'         => $status
        ]);
    }

    /**
     * Get detail of a received death certificate for Male Da'wah
     */
    public function getDawahDeathCertificateDetail(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Damayan']);
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid ID.']);
            exit;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $record = $burialModel->getDawahDeathCertificateById($id);

        if (!$record) {
            echo json_encode(['success' => false, 'error' => 'Record not found.']);
            exit;
        }

        echo json_encode(['success' => true, 'record' => $record]);
        exit;
    }

    /**
     * Acknowledge / Update status of received death certificate
     */
    public function acknowledgeDawahDeathCertificate(): void {
        Auth::protectRole(['Admin', 'Staff_Male']);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $id = (int)($input['id'] ?? 0);
        $status = trim($input['status'] ?? 'Reviewed');
        $notes = !empty($input['notes']) ? trim($input['notes']) : null;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid ID.']);
            exit;
        }

        $staffId = (int)($_SESSION['user_id'] ?? 0);
        $staffName = trim($_SESSION['name'] ?? 'Male Da\'wah Staff');

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $res = $burialModel->acknowledgeDawahDeathCertificate($id, $status, $staffId, $staffName, $notes);

        echo json_encode($res);
        exit;
    }

    /**
     * Official Letter-sized Death Certificate File View (Printable / Readable file)
     */
    public function viewDawahDeathCertificateFile(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Damayan']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo "Invalid certificate ID.";
            exit;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $record = $burialModel->getDawahDeathCertificateById($id);

        if (!$record) {
            echo "Certificate transmittal record not found.";
            exit;
        }

        // If an uploaded PDF or image file exists, provide view or fallback
        if (!empty($_GET['raw']) && !empty($record['file_path'])) {
            $absPath = BASE_PATH . '/public/' . $record['file_path'];
            if (file_exists($absPath)) {
                $mime = mime_content_type($absPath) ?: 'application/octet-stream';
                header('Content-Type: ' . $mime);
                header('Content-Disposition: inline; filename="' . basename($record['file_name']) . '"');
                readfile($absPath);
                exit;
            }
        }

        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        $dbUser = $userModel->findById($_SESSION['user_id']);

        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/death_certificate_file', [
            'record' => $record,
            'dbUser' => $dbUser
        ]);
    }

    /**
     * Download the certificate file
     */
    public function downloadDawahDeathCertificateFile(): void {
        Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Damayan']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . url('/admin/dawah/male/death-certificates'));
            exit;
        }

        require_once BASE_PATH . '/app/models/BurialRequest.php';
        $burialModel = new BurialRequest();
        $record = $burialModel->getDawahDeathCertificateById($id);

        if (!$record) {
            header('Location: ' . url('/admin/dawah/male/death-certificates'));
            exit;
        }

        if (!empty($record['file_path'])) {
            $absPath = BASE_PATH . '/public/' . $record['file_path'];
            if (file_exists($absPath)) {
                $mime = mime_content_type($absPath) ?: 'application/octet-stream';
                header('Content-Type: ' . $mime);
                header('Content-Disposition: attachment; filename="' . basename($record['file_name']) . '"');
                header('Content-Length: ' . filesize($absPath));
                readfile($absPath);
                exit;
            }
        }

        // Render printable Form 103 view and trigger automatic PDF generation and download
        $this->view('admin/Staff_Admin/Admin-Dawah_Department/Male Dawah/death_certificate_file', [
            'record'            => $record,
            'dbUser'            => null,
            'download_mode'     => true,
            'auto_download_pdf' => true
        ]);
        exit;
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

    public function updateUserRole(): void {
        Auth::protectRole(['Admin']);
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $id = $body['id'] ?? null;
        $newRole = trim($body['role'] ?? '');

        if (!$id || !$newRole) {
            echo json_encode(['success' => false, 'message' => 'User ID and Role are required.']);
            return;
        }

        // Prevent self-demotion
        if ((string)$id === (string)($_SESSION['user_id'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Security restriction: You cannot modify your own administrator role.']);
            return;
        }

        $validRoles = [
            'Admin' => 'MIS Administrator',
            'Staff_Tenant' => 'Apartments Department Staff',
            'Staff_Damayan' => 'Damayan Department Staff',
            'Staff_Male' => 'Da\'wah Male Division Staff',
            'Staff_Female' => 'Islamic Education & Da\'wah Staff',
            'Guest' => 'Regular User / Guest',
            'Tenant' => 'Apartment Tenant'
        ];

        if (!array_key_exists($newRole, $validRoles)) {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
            return;
        }

        $db = getDbConnection();
        $stmt = $db->prepare("SELECT tenant_id, first_name, last_name, email, role FROM tenant_accounts WHERE tenant_id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User account not found.']);
            return;
        }

        $oldRole = $user['role'] ?? 'User';
        if ($oldRole === $newRole) {
            echo json_encode(['success' => true, 'message' => 'User already holds this role.', 'newRole' => $newRole]);
            return;
        }

        $upd = $db->prepare("UPDATE tenant_accounts SET role = :r WHERE tenant_id = :id");
        if ($upd->execute(['r' => $newRole, 'id' => $id])) {
            $adminName = $_SESSION['name'] ?? 'Admin';
            $userName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['email'];
            $this->logAudit('USER_MANAGEMENT', 'ROLE_CHANGE', "Admin ($adminName) changed role for $userName (ID: $id) from '$oldRole' to '$newRole'");
            echo json_encode([
                'success' => true,
                'message' => "Role updated to {$validRoles[$newRole]} successfully.",
                'newRole' => $newRole,
                'roleTitle' => $validRoles[$newRole]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user role in database.']);
        }
    }

    public function getUserAuditTrail(): void {
        Auth::protectRole(['Admin']);
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        $userId = $_GET['id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User ID required.']);
            return;
        }

        $db = getDbConnection();
        $stmt = $db->prepare("
            SELECT al.*, COALESCE(ta.role, 'System') as display_role
            FROM audit_logs al
            LEFT JOIN tenant_accounts ta ON al.admin_id = ta.tenant_id
            WHERE al.details LIKE :idPattern OR al.admin_id = :userId
            ORDER BY al.timestamp DESC
            LIMIT 50
        ");
        $stmt->execute([
            'idPattern' => "%ID: $userId%",
            'userId' => $userId
        ]);
        $trails = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        echo json_encode(['success' => true, 'trails' => $trails]);
    }

    public function auditLogs(): void {
        Auth::protectRole(['Admin']);
        $db = getDbConnection();
        $query = "
            SELECT al.*, 
                   COALESCE(ta.role, 'System') as display_role
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
        $now = TimeSim::now('now', $tid ?? null);
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
            $now = TimeSim::now('now', $tid);
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

