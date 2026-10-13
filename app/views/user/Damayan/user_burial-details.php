<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';
require_once BASE_PATH . '/app/models/FuneralCase.php';

$userId = $_SESSION['user_id'] ?? 0;
$userModel = new User();
$dbUser = $userModel->findById($userId) ?: [];
$extraInfo = $userModel->getAdditionalInfo($userId) ?: [];
$dbUser = array_merge($dbUser, $extraInfo);

$userName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
if (empty($userName)) {
    $userName = $_SESSION['name'] ?? 'Applicant';
}
$userPhone = $dbUser['phone_number'] ?? $dbUser['contact_number'] ?? ($dbUser['phone'] ?? '');
$userEmail = $dbUser['email'] ?? ($_SESSION['email'] ?? '');
$userAddress = $dbUser['address'] ?? '';

$burialModel = new BurialRequest();
$fcModel = new FuneralCase();

// Fetch request by ID if provided in query string, otherwise load user's most recent request
$reqId = $_GET['id'] ?? ($_GET['case'] ?? '');
$activeBurial = null;

if (!empty($reqId)) {
    $foundBurial = $burialModel->findByRefId($reqId);
    if ($foundBurial && ($foundBurial['tenant_id'] == $userId || Auth::hasRole(['Admin', 'Staff_Damayan']))) {
        $activeBurial = $foundBurial;
    } else {
        $foundCase = $fcModel->findByCaseNumber($reqId);
        if ($foundCase && ($foundCase['tenant_id'] == $userId || Auth::hasRole(['Admin', 'Staff_Damayan']))) {
            $activeBurial = [
                'ref_id' => $foundCase['case_number'],
                'deceased_name' => trim($foundCase['deceased_first_name'] . ' ' . ($foundCase['deceased_middle_name'] ? $foundCase['deceased_middle_name'] . ' ' : '') . $foundCase['deceased_last_name']),
                'date_of_death' => $foundCase['deceased_dod'],
                'place_of_death' => $foundCase['deceased_place_of_death'],
                'residence' => $foundCase['deceased_address'],
                'religion' => $foundCase['deceased_religion'] ?? 'Islam',
                'relationship' => $foundCase['informant_relationship'] ?? 'Relative',
                'status' => $foundCase['status'] ?? 'Under Review',
                'submitted_at' => $foundCase['submitted_at'] ?? date('Y-m-d H:i:s')
            ];
        }
    }
} else {
    $userBurials = $burialModel->getByTenantId($userId);
    if (!empty($userBurials)) {
        $activeBurial = $userBurials[0];
    } else {
        $userCases = $fcModel->getByTenantId($userId);
        if (!empty($userCases)) {
            $fc = $userCases[0];
            $activeBurial = [
                'ref_id' => $fc['case_number'],
                'deceased_name' => trim($fc['deceased_first_name'] . ' ' . ($fc['deceased_middle_name'] ? $fc['deceased_middle_name'] . ' ' : '') . $fc['deceased_last_name']),
                'date_of_death' => $fc['deceased_dod'],
                'place_of_death' => $fc['deceased_place_of_death'],
                'residence' => $fc['deceased_address'],
                'religion' => $fc['deceased_religion'] ?? 'Islam',
                'relationship' => $fc['informant_relationship'] ?? 'Relative',
                'status' => $fc['status'] ?? 'Under Review',
                'submitted_at' => $fc['submitted_at'] ?? date('Y-m-d H:i:s')
            ];
        }
    }
}

// Status text and style mappings
$hasApplication = !empty($activeBurial);
$appId = $hasApplication ? ($activeBurial['ref_id'] ?? 'BR-PENDING') : 'NONE';
$status = $hasApplication ? ($activeBurial['status'] ?? 'Under Review') : 'No Active Request';
$dateSubmitted = $hasApplication && !empty($activeBurial['submitted_at']) ? date('M d, Y', strtotime($activeBurial['submitted_at'])) : '—';
$deceasedName = $hasApplication ? ($activeBurial['deceased_name'] ?? '—') : 'No Deceased Listed';
$dateOfDeath = $hasApplication && !empty($activeBurial['date_of_death']) ? date('M d, Y', strtotime($activeBurial['date_of_death'])) : '—';
$placeOfDeath = $hasApplication && !empty($activeBurial['place_of_death']) ? $activeBurial['place_of_death'] : 'ISCAG Area';
$requesterName = $userName;
$relationship = $hasApplication && !empty($activeBurial['relationship']) ? $activeBurial['relationship'] : 'Family Member';
$services = ["Bathing (Ghusl)", "Shrouding (Kafn)", "Janazah Prayer", "Cemetery Plot & Coordination", "Burial Transport"];

$autoOpenForm = isset($startWithFormOpen) && $startWithFormOpen === true;
if (isset($_GET['action']) && in_array($_GET['action'], ['request', 'form', 'fill'])) {
    $autoOpenForm = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Burial Service Details & Request — ISCAG Damayan</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1b5e20;
            --primary-dark: #0f3d14;
            --primary-light: #e8f5e9;
            --primary-accent: #2e7d32;
            --gold: #c9a84c;
            --gold-light: #fef9e7;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success-bg: #ecfdf5;
            --success-text: #065f46;
            --warning-bg: #fffbeb;
            --warning-text: #b45309;
            --danger: #dc2626;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 10px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 40px rgba(27,94,32,0.08);
            --radius-lg: 20px;
            --radius-md: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        .details-container {
            max-width: 1040px;
            margin: 0 auto;
            padding: 32px 24px 60px;
        }

        /* ── Breadcrumb Bar ── */
        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .breadcrumb-nav a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .breadcrumb-nav a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        .breadcrumb-nav .sep { color: #d1d5db; }
        .breadcrumb-nav .current { color: var(--text-main); font-weight: 600; }

        /* ── Top Hero Card ── */
        .app-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            border: 1px solid var(--border);
            margin-bottom: 32px;
            transition: box-shadow 0.3s;
        }
        .app-card:hover {
            box-shadow: var(--shadow-lg);
        }

        /* ── Header ── */
        .app-header {
            background: linear-gradient(135deg, #f0f7f4 0%, #ffffff 70%, #f7faf8 100%);
            padding: 36px 40px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
        }
        .app-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--gold) 100%);
        }
        .app-header-left h1 {
            font-family: 'Lora', serif;
            font-size: 1.85rem;
            color: var(--primary-dark);
            margin: 4px 0 6px;
            line-height: 1.25;
        }
        .app-id-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'Inter', monospace;
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.82rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: rgba(0,0,0,0.03);
            padding: 3px 10px;
            border-radius: 6px;
        }
        .app-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* ── Status Badges ── */
        .status-badge {
            padding: 8px 18px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid transparent;
        }
        .status-badge.approved {
            background: var(--success-bg);
            color: var(--success-text);
            border-color: #a7f3d0;
        }
        .status-badge.pending, .status-badge.under-review {
            background: var(--warning-bg);
            color: var(--warning-text);
            border-color: #fde68a;
        }
        .status-badge.none {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        /* ── Primary Action Button (Request Form) ── */
        .btn-request-form {
            background: linear-gradient(135deg, var(--primary) 0%, #2e7d32 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(27,94,32,0.25);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }
        .btn-request-form:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(27,94,32,0.35);
            color: white;
        }
        .btn-request-form:active {
            transform: translateY(0);
        }
        .btn-request-form svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.2;
            transition: transform 0.2s;
        }
        .btn-request-form:hover svg {
            transform: scale(1.1);
        }

        /* ── Body Content ── */
        .app-body { padding: 36px 40px; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            margin-bottom: 36px;
        }
        .info-group {
            background: #fbfcfd;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 20px;
        }
        .info-group h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin: 0 0 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .info-group h4 svg {
            width: 16px;
            height: 16px;
            color: var(--primary);
        }
        .info-value {
            font-size: 1.18rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .info-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* ── Services Checklist ── */
        .services-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 26px;
            border: 1px solid var(--border);
            margin-bottom: 32px;
        }
        .services-box h4 {
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            color: var(--primary-dark);
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .services-grid-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
        }
        .service-item-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: white;
            border: 1px solid #eef2f6;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
            transition: border-color 0.2s;
        }
        .service-item-card:hover {
            border-color: #a7f3d0;
            background: #fafffc;
        }
        .service-item-card svg {
            width: 20px;
            height: 20px;
            fill: var(--primary);
            flex-shrink: 0;
        }

        /* ── Guidelines Alert Box ── */
        .guidelines-notice {
            background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ec 100%);
            border: 1.5px solid #bbf7d0;
            border-radius: var(--radius-md);
            padding: 20px 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 8px;
        }
        .guidelines-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .guidelines-text h5 {
            margin: 0 0 4px;
            font-size: 0.95rem;
            color: var(--primary-dark);
            font-weight: 800;
        }
        .guidelines-text p {
            margin: 0;
            font-size: 0.85rem;
            color: #166534;
            line-height: 1.55;
        }

        /* ── Footer / Actions ── */
        .app-footer {
            background: #fafbfc;
            padding: 24px 40px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .btn-outline {
            padding: 10px 20px;
            background: white;
            border: 1.5px solid var(--border);
            color: var(--text-main);
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        /* ══════════════════════════════════════════════════════════════════════ */
        /* 📝 FILL UP FORM SECTION (Appears when clicking Request Form button)     */
        /* ══════════════════════════════════════════════════════════════════════ */
        #burial-form-container {
            display: <?= $autoOpenForm ? 'block' : 'none' ?>;
            animation: formSlideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 36px;
        }

        @keyframes formSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 15px 45px rgba(0,0,0,0.08);
            border: 1.5px solid #d1fae5;
            overflow: hidden;
            position: relative;
        }
        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1b5e20, #2e7d32, #c9a84c);
        }

        .form-card-header {
            background: linear-gradient(135deg, #f7faf8 0%, #ffffff 100%);
            padding: 32px 40px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .form-card-title-area h2 {
            font-family: 'Lora', serif;
            font-size: 1.55rem;
            color: var(--primary-dark);
            margin: 0 0 6px;
        }
        .form-card-title-area p {
            margin: 0;
            font-size: 0.88rem;
            color: var(--text-muted);
        }
        .btn-close-form {
            background: none;
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: 8px;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-close-form:hover {
            background: #f3f4f6;
            color: var(--danger);
            border-color: #fca5a5;
        }

        .form-card-body {
            padding: 40px;
        }

        .form-section-title {
            font-family: 'Lora', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-section-title svg {
            width: 20px;
            height: 20px;
            fill: var(--primary);
        }

        /* ── Form Inputs ── */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }
        .form-grid.cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        .form-grid.cols-1 {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .form-label .req {
            color: var(--danger);
            margin-left: 2px;
        }
        .form-control, .form-select {
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.92rem;
            color: var(--text-main);
            background: #fff;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            width: 100%;
            box-sizing: border-box;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(27,94,32,0.12);
        }
        .form-control[readonly] {
            background: #f8fafc;
            color: #64748b;
        }

        /* ── Service Options Grid ── */
        .services-select-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .service-checkbox-card {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            background: #fbfcfd;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            user-select: none;
        }
        .service-checkbox-card:hover {
            border-color: #86efac;
            background: #f0fdf4;
        }
        .service-checkbox-card input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .service-checkbox-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* ── Terms & Submit Bar ── */
        .form-terms-box {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .form-terms-box input[type="checkbox"] {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
        }
        .form-terms-label {
            font-size: 0.85rem;
            color: #166534;
            line-height: 1.5;
            font-weight: 500;
        }

        .form-actions-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 14px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, #2e7d32 100%);
            color: white;
            padding: 13px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(27,94,32,0.25);
            transition: all 0.25s;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(27,94,32,0.35);
        }
        .btn-submit:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ── Toast Notification ── */
        .toast-notify {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            background: #064e3b;
            color: white;
            padding: 16px 24px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: none;
            animation: slideUpToast 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 420px;
            border-left: 5px solid #34d399;
        }
        @keyframes slideUpToast {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Print Media ── */
        @media print {
            .app-wrapper { display: block; }
            .sidebar, .top-bar, .app-header-actions, .app-footer, .breadcrumb-nav, #burial-form-container {
                display: none !important;
            }
            .details-container { margin: 0; padding: 0; max-width: 100%; }
            .app-card { box-shadow: none; border: 1px solid #111; }
        }

        @media (max-width: 768px) {
            .app-header { flex-direction: column; align-items: flex-start; }
            .info-grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .form-grid.cols-2 { grid-template-columns: 1fr; }
            .app-body, .form-card-body { padding: 24px; }
            .app-header, .form-card-header { padding: 24px; }
            .app-footer { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-request-form { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <!-- Sidebar -->
    <?php $active_page = 'burial_service'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <div class="top-bar-title">Burial Service Details</div>
                <div class="top-bar-subtitle">Damayan Department — Islamic Funeral Care</div>
            </div>
            <div class="top-bar-actions">
                <a href="<?= url('/user/services/burial-dashboard') ?>" class="btn-topbar">Burial Dashboard</a>
                <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Main Dashboard</a>
            </div>
        </div>

        <div class="details-container">
            <!-- Breadcrumbs -->
            <div class="breadcrumb-nav">
                <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                <span class="sep">/</span>
                <a href="<?= url('/user/services/burial-dashboard') ?>">Burial Dashboard</a>
                <span class="sep">/</span>
                <span class="current">Burial Details &amp; Request Form</span>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- CARD 1: BURIAL SERVICE DETAILS                                 -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div class="app-card">
                <!-- Header -->
                <header class="app-header">
                    <div class="app-header-left">
                        <div class="app-id-tag">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            REFERENCE: #<?= htmlspecialchars($appId) ?>
                        </div>
                        <h1>Burial Service Overview &amp; Details</h1>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                            Submitted: <strong><?= htmlspecialchars($dateSubmitted) ?></strong> • Applicant: <strong><?= htmlspecialchars($requesterName) ?></strong>
                        </div>
                    </div>
                    <div class="app-header-actions">
                        <?php
                            $statusClass = 'none';
                            $stLower = strtolower($status);
                            if (strpos($stLower, 'approved') !== false || strpos($stLower, 'verified') !== false || strpos($stLower, 'completed') !== false) {
                                $statusClass = 'approved';
                            } elseif (strpos($stLower, 'review') !== false || strpos($stLower, 'pending') !== false || strpos($stLower, 'submitted') !== false) {
                                $statusClass = 'pending';
                            }
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                            <?= htmlspecialchars($status) ?>
                        </span>

                        <!-- 🔘 CLICK REQUEST FORM BUTTON (Reveals the fill-up form) -->
                        <button type="button" class="btn-request-form" id="btn-request-form-top" onclick="toggleRequestForm()">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <span id="btn-request-text-top"><?= $autoOpenForm ? 'Close Request Form' : 'Request Form' ?></span>
                        </button>
                    </div>
                </header>

                <!-- Body -->
                <div class="app-body">
                    <!-- Informative Grid -->
                    <div class="info-grid">
                        <div class="info-group">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                                Deceased Person Information
                            </h4>
                            <div class="info-value"><?= htmlspecialchars($deceasedName) ?></div>
                            <div class="info-sub">
                                Date of Death: <strong><?= htmlspecialchars($dateOfDeath) ?></strong><br>
                                Location: <?= htmlspecialchars($placeOfDeath) ?>
                            </div>
                        </div>

                        <div class="info-group">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Applicant / Informant Information
                            </h4>
                            <div class="info-value"><?= htmlspecialchars($requesterName) ?></div>
                            <div class="info-sub">
                                Relationship: <strong><?= htmlspecialchars($relationship) ?></strong><br>
                                Contact: <?= htmlspecialchars($userPhone ?: 'On File') ?> • <?= htmlspecialchars($userEmail) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Services Included in Islamic Burial -->
                    <div class="services-box">
                        <h4>
                            <span>Islamic Funeral Services Provided by ISCAG Damayan</span>
                            <span style="font-size:0.75rem; color:var(--primary); font-family:'Inter'; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">Compliant with Islamic Sunnah</span>
                        </h4>
                        <div class="services-grid-list">
                            <?php foreach($services as $srv): ?>
                            <div class="service-item-card">
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                <span><?= htmlspecialchars($srv) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Notice / Coordination Guidelines -->
                    <div class="guidelines-notice">
                        <div class="guidelines-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </div>
                        <div class="guidelines-text">
                            <h5>Immediate Damayan Coordination</h5>
                            <p>To request a burial or report a death immediately, click the <strong>Request Form</strong> button above or below. After submitting, please coordinate with the Damayan Coordinator at the ISCAG office or call our 24/7 emergency hotline for immediate Ghusl and Janazah arrangements.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer / Action Row -->
                <footer class="app-footer">
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Need a new service request? Click <strong>Request Form</strong> to fill up the form.
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <button type="button" class="btn-outline" onclick="window.print()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Print Details
                        </button>
                        <a href="<?= url('/user/services/burial-dashboard') ?>" class="btn-outline">
                            View Request History
                        </a>
                        <!-- 🔘 CLICK REQUEST FORM BUTTON (Reveals the fill-up form) -->
                        <button type="button" class="btn-request-form" id="btn-request-form-bottom" onclick="toggleRequestForm()">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <span id="btn-request-text-bottom"><?= $autoOpenForm ? 'Close Request Form' : 'Request Form' ?></span>
                        </button>
                    </div>
                </footer>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- CARD 2: THE FILL UP FORM (APPEARS ON CLICKING "REQUEST FORM")   -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="burial-form-container">
                <div class="form-card" id="burial-form-card">
                    <!-- Form Header -->
                    <div class="form-card-header">
                        <div class="form-card-title-area">
                            <h2>Burial Service Request Form</h2>
                            <p>Please fill out the deceased and informant details below. Our Damayan coordinators will immediately review and prepare the arrangements.</p>
                        </div>
                        <button type="button" class="btn-close-form" onclick="toggleRequestForm(false)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Close Form
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form id="burial-request-form" class="form-card-body" onsubmit="submitBurialRequest(event)">
                        <!-- Section 1: Deceased Personal Information -->
                        <div class="form-section-title">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            1. Deceased's Information
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">First Name <span class="req">*</span></label>
                                <input type="text" id="deceased-first-name" class="form-control" placeholder="e.g. Abdullah" required />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Middle Name</label>
                                <input type="text" id="deceased-middle-name" class="form-control" placeholder="e.g. Masoud" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name <span class="req">*</span></label>
                                <input type="text" id="deceased-last-name" class="form-control" placeholder="e.g. Al-Mansoor" required />
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Sex <span class="req">*</span></label>
                                <select id="deceased-sex" class="form-select" required>
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" id="deceased-dob" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date of Death <span class="req">*</span></label>
                                <input type="date" id="deceased-dod" class="form-control" value="<?= date('Y-m-d') ?>" required />
                            </div>
                        </div>

                        <div class="form-grid cols-2">
                            <div class="form-group">
                                <label class="form-label">Place of Death <span class="req">*</span></label>
                                <input type="text" id="deceased-pod" class="form-control" placeholder="Hospital name, residence, or complete address" required />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Usual Residence Address <span class="req">*</span></label>
                                <input type="text" id="deceased-residence" class="form-control" placeholder="Barangay, City / Municipality, Province" required />
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Religion</label>
                                <input type="text" id="deceased-religion" class="form-control" value="Islam" readonly />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Civil Status</label>
                                <select id="deceased-civil-status" class="form-select">
                                    <option value="Married">Married</option>
                                    <option value="Single">Single</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nationality</label>
                                <input type="text" id="deceased-citizenship" class="form-control" value="Filipino" />
                            </div>
                        </div>

                        <!-- Section 2: Islamic Rites & Arrangements -->
                        <div class="form-section-title" style="margin-top: 16px;">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            2. Islamic Rites &amp; Services Requested
                        </div>

                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 14px;">Select all Islamic Damayan assistance needed for the deceased:</p>
                        
                        <div class="services-select-grid">
                            <label class="service-checkbox-card">
                                <input type="checkbox" name="services_req" value="Ghusl" checked />
                                <span class="service-checkbox-label">Bathing / Ghusl (الغسل)</span>
                            </label>
                            <label class="service-checkbox-card">
                                <input type="checkbox" name="services_req" value="Kafn" checked />
                                <span class="service-checkbox-label">Shrouding / Kafn (الكفن)</span>
                            </label>
                            <label class="service-checkbox-card">
                                <input type="checkbox" name="services_req" value="Janazah" checked />
                                <span class="service-checkbox-label">Janazah Prayer (صلاة الجنازة)</span>
                            </label>
                            <label class="service-checkbox-card">
                                <input type="checkbox" name="services_req" value="Plot" checked />
                                <span class="service-checkbox-label">Islamic Cemetery Plot</span>
                            </label>
                            <label class="service-checkbox-card">
                                <input type="checkbox" name="services_req" value="Transport" checked />
                                <span class="service-checkbox-label">Hearse / Transport Support</span>
                            </label>
                        </div>

                        <div class="form-grid cols-2">
                            <div class="form-group">
                                <label class="form-label">Preferred Cemetery / Burial Site</label>
                                <input type="text" id="deceased-cemetery" class="form-control" placeholder="e.g. Dasmariñas Islamic Cemetery / ISCAG Ground" value="ISCAG Designated Muslim Cemetery" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Target Burial Time</label>
                                <select id="burial-timing" class="form-select">
                                    <option value="Immediate (As soon as possible - Sunnah)">Immediate (As soon as possible — Sunnah)</option>
                                    <option value="After Dhuhr Prayer">After Dhuhr Prayer</option>
                                    <option value="After Asr Prayer">After Asr Prayer</option>
                                    <option value="Morning (Following Day)">Morning (Following Day)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Section 3: Applicant / Informant Details -->
                        <div class="form-section-title" style="margin-top: 16px;">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            3. Applicant &amp; Informant Information
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Applicant Name <span class="req">*</span></label>
                                <input type="text" id="informant-name" class="form-control" value="<?= htmlspecialchars($userName) ?>" required />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Relationship to Deceased <span class="req">*</span></label>
                                <select id="informant-rel" class="form-select" required>
                                    <option value="Son">Son</option>
                                    <option value="Daughter">Daughter</option>
                                    <option value="Spouse">Spouse (Husband / Wife)</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Relative" selected>Relative</option>
                                    <option value="Legal Guardian / Representative">Legal Guardian / Representative</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contact Phone Number <span class="req">*</span></label>
                                <input type="tel" id="informant-phone" class="form-control" value="<?= htmlspecialchars($userPhone) ?>" placeholder="09XX-XXX-XXXX" required />
                            </div>
                        </div>

                        <div class="form-grid cols-2">
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" id="informant-email" class="form-control" value="<?= htmlspecialchars($userEmail) ?>" placeholder="name@example.com" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Applicant Residential Address</label>
                                <input type="text" id="informant-address" class="form-control" value="<?= htmlspecialchars($userAddress) ?>" placeholder="Current residence address" />
                            </div>
                        </div>

                        <!-- Terms & Certification -->
                        <div class="form-terms-box">
                            <input type="checkbox" id="burial-certify" required />
                            <label for="burial-certify" class="form-terms-label">
                                I hereby certify that the information provided is correct and truthful to the best of my knowledge. I request the Islamic Studies, Call and Guidance (ISCAG) Damayan Department to administer the Islamic funeral and burial arrangements according to Quran and Sunnah.
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions-row">
                            <button type="button" class="btn-outline" onclick="toggleRequestForm(false)">Cancel</button>
                            <button type="submit" class="btn-submit" id="btn-submit-burial">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Submit Burial Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Dashboard Link -->
            <div style="text-align: center; margin-top: 36px;">
                <a href="<?= url('/user/dashboard') ?>" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    ← Back to My Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div class="toast-notify" id="burial-toast"></div>

<script>
    // ── Toggle Request Form Visibility ──
    function toggleRequestForm(forceState = null) {
        const container = document.getElementById('burial-form-container');
        const btnTopText = document.getElementById('btn-request-text-top');
        const btnBottomText = document.getElementById('btn-request-text-bottom');
        
        let isOpen = container.style.display === 'block';
        if (forceState !== null) {
            isOpen = !forceState;
        }

        if (isOpen) {
            container.style.display = 'none';
            if (btnTopText) btnTopText.textContent = 'Request Form';
            if (btnBottomText) btnBottomText.textContent = 'Request Form';
        } else {
            container.style.display = 'block';
            if (btnTopText) btnTopText.textContent = 'Close Request Form';
            if (btnBottomText) btnBottomText.textContent = 'Close Request Form';
            
            // Smoothly scroll down to the form card
            setTimeout(() => {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        }
    }

    // ── Submit Burial Request via AJAX ──
    async function submitBurialRequest(event) {
        event.preventDefault();
        
        const certifyCheck = document.getElementById('burial-certify');
        if (!certifyCheck || !certifyCheck.checked) {
            alert('Please check the certification box to confirm the request.');
            return;
        }

        const btnSubmit = document.getElementById('btn-submit-burial');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" style="width:18px;height:18px;fill:none;stroke:white;stroke-width:2.5;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="white"></path></svg> Submitting Request...';

        const payload = {
            firstName: document.getElementById('deceased-first-name').value.trim(),
            middleName: document.getElementById('deceased-middle-name').value.trim(),
            lastName: document.getElementById('deceased-last-name').value.trim(),
            sex: document.getElementById('deceased-sex').value,
            dob: document.getElementById('deceased-dob').value || null,
            dod: document.getElementById('deceased-dod').value,
            pod: document.getElementById('deceased-pod').value.trim(),
            residence: document.getElementById('deceased-residence').value.trim(),
            religion: document.getElementById('deceased-religion').value || 'Islam',
            civilStatus: document.getElementById('deceased-civil-status').value,
            citizenship: document.getElementById('deceased-citizenship').value || 'Filipino',
            relationship: document.getElementById('informant-rel').value,
            informantName: document.getElementById('informant-name').value.trim(),
            informantContact: document.getElementById('informant-phone').value.trim(),
            informantAddress: document.getElementById('informant-address').value.trim()
        };

        try {
            const response = await fetch('<?= url("/user/services/burial/submit") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                const toast = document.getElementById('burial-toast');
                const ref = data.ref_id || 'Submitted';
                toast.innerHTML = '<strong>Success!</strong> Burial Service request filed successfully.<br>Reference ID: <code>' + ref + '</code><br><span style="font-size:0.8rem;opacity:0.9;">Updating record details...</span>';
                toast.style.display = 'block';

                setTimeout(() => {
                    // Reload with the new reference ID so details update automatically
                    window.location.href = '<?= url("/user/services/burial-details") ?>?id=' + encodeURIComponent(ref);
                }, 1600);
            } else {
                throw new Error(data.message || 'Failed to submit burial request.');
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert('Failed to submit burial request. Please check your inputs and try again.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }
</script>

</body>
</html>
