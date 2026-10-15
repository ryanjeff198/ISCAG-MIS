<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/BurialRequest.php';

$userId = $_SESSION['user_id'] ?? 0;
$userModel = new User();
$dbUser = $userModel->findById($userId) ?: [];
$extraInfo = $userModel->getAdditionalInfo($userId) ?: [];
$dbUser = array_merge($dbUser, $extraInfo);

$userName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
if (empty($userName)) {
    $userName = $_SESSION['name'] ?? 'Applicant';
}
$userPhone = $dbUser['phone_number'] ?? $dbUser['contact_number'] ?? ($dbUser['phone'] ?? ($dbUser['contactnum'] ?? ''));
$userEmail = $dbUser['email'] ?? ($_SESSION['email'] ?? '');
$userAddress = $dbUser['address'] ?? '';

$burialModel = new BurialRequest();

// Fetch request by ID if provided in query string, otherwise load user's most recent request
$reqId = $_GET['id'] ?? ($_GET['case'] ?? '');
$activeBurial = null;

if (!empty($reqId)) {
    $foundBurial = $burialModel->findByRefId($reqId);
    if ($foundBurial && ($foundBurial['tenant_id'] == $userId || Auth::hasRole(['Admin', 'Staff_Damayan']))) {
        $activeBurial = $foundBurial;
    }
} else {
    $userBurials = $burialModel->getByTenantId((int)$userId);
    if (!empty($userBurials)) {
        $activeBurial = $burialModel->findByRefId($userBurials[0]['ref_id']);
    }
}

// Existing deceased records to allow auto-fill and prevent duplicate data entry
$existingDeceased = $burialModel->getDeceasedRecords((int)$userId);

// Status text and style mappings
$hasApplication = !empty($activeBurial);
$appId = $hasApplication ? ($activeBurial['ref_id'] ?? 'BR-PENDING') : 'NONE';
$status = $hasApplication ? ($activeBurial['status'] ?? 'Under Review') : 'No Active Request';
$dateSubmitted = $hasApplication && !empty($activeBurial['submitted_at']) ? date('M d, Y', strtotime($activeBurial['submitted_at'])) : '—';
$deceasedName = $hasApplication ? ($activeBurial['deceased_name'] ?? '—') : 'No Deceased Listed';
$dateOfDeath = $hasApplication && !empty($activeBurial['date_of_death']) ? date('M d, Y', strtotime($activeBurial['date_of_death'])) : '—';
$placeOfDeath = $hasApplication && !empty($activeBurial['place_of_death']) ? $activeBurial['place_of_death'] : 'ISCAG Area';
$requesterName = $hasApplication && !empty($activeBurial['informant_name']) ? $activeBurial['informant_name'] : $userName;
$relationship = $hasApplication && !empty($activeBurial['relationship']) ? $activeBurial['relationship'] : 'Family Member';

$certActionType = $activeBurial['cert_action_type'] ?? 'register';
$isCopyRequest = ($certActionType === 'copy');
$existingCertNo = $activeBurial['existing_cert_no'] ?? '';
$copyReason = $activeBurial['copy_reason'] ?? '';
$numCopies = (int)($activeBurial['num_copies'] ?? 1);
$isRecordVerified = !empty($activeBurial['existing_record_verified']);
$isCopyIssued = !empty($activeBurial['copy_issued_at']);
$copyIssuedDate = $isCopyIssued ? date('M d, Y', strtotime($activeBurial['copy_issued_at'])) : '';
$isCopyReceived = !empty($activeBurial['copy_received_confirmed']);
$copyReceivedDate = (!empty($activeBurial['copy_received_at'])) ? date('M d, Y', strtotime($activeBurial['copy_received_at'])) : '';

$stLower = strtolower($status);
$isInfoRequired = ($stLower === 'additional information required');
$isCompleted = ($stLower === 'burial completed' || $stLower === 'completed' || ($isCopyRequest && $isCopyReceived));
$isRejected = ($stLower === 'rejected');

$requestType = $activeBurial['request_type'] ?? ($isCopyRequest ? 'death_certificate' : 'both');
$deathCertReq = !empty($activeBurial['death_cert_requested']) || $isCopyRequest;
$cemeteryPlotReq = !$isCopyRequest && !empty($activeBurial['cemetery_plot_requested']);
$dcStatus = $activeBurial['death_cert_status'] ?? 'Request Received';
$cpStatus = $activeBurial['cemetery_plot_status'] ?? 'Pending Review';

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
    <title>Death Certificate &amp; Cemetery Plot Management — ISCAG Damayan</title>
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
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .app-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .status-badge.pending {
            background: var(--warning-bg);
            color: var(--warning-text);
            border: 1px solid #fde68a;
        }
        .status-badge.coordination {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .status-badge.approved {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid #a7f3d0;
        }
        .status-badge.rejected {
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }
        .status-badge.none {
            background: #f3f4f6;
            color: var(--text-muted);
            border: 1px solid #e5e7eb;
        }

        .btn-request-form {
            background: var(--primary);
            color: white;
            padding: 10px 22px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(27,94,32,0.25);
            transition: all 0.2s;
        }
        .btn-request-form:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(27,94,32,0.35);
        }
        .btn-request-form svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
        }

        /* ── Body ── */
        .app-body {
            padding: 40px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            margin-bottom: 32px;
        }
        .info-group h4 {
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            color: var(--primary-dark);
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-group h4 svg {
            width: 18px;
            height: 18px;
            color: var(--primary);
        }
        .info-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .info-sub {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── Dual Tracking Cards ── */
        .dual-tracking-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }
        .track-box {
            background: #fafbfc;
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            transition: border-color 0.2s;
        }
        .track-box.active-box {
            border-color: #a7f3d0;
            background: #f0fdf4;
        }
        .track-box-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }
        .track-box-title {
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .track-field-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.86rem;
            margin-bottom: 8px;
        }
        .track-field-label {
            color: var(--text-muted);
        }
        .track-field-val {
            font-weight: 700;
            color: var(--text-main);
            text-align: right;
        }

        /* ── Notice Banner ── */
        .guidelines-notice {
            background: #f8fafc;
            border-left: 4px solid var(--primary);
            border-radius: 0 12px 12px 0;
            padding: 18px 22px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
        .guidelines-icon {
            color: var(--primary);
            margin-top: 2px;
            flex-shrink: 0;
        }
        .guidelines-text h5 {
            margin: 0 0 4px;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        .guidelines-text p {
            margin: 0;
            font-size: 0.85rem;
            color: #4b5563;
            line-height: 1.5;
        }

        /* ── Footer ── */
        .app-footer {
            background: #f8fafc;
            padding: 20px 40px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .btn-outline {
            background: white;
            border: 1.5px solid var(--border);
            color: var(--text-main);
            padding: 9px 20px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        /* ── Form Card ── */
        #burial-form-container {
            display: <?= $autoOpenForm ? 'block' : 'none' ?>;
            margin-top: 36px;
            animation: fadeInCard 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes fadeInCard {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            border: 1.5px solid #a7f3d0;
            overflow: hidden;
            position: relative;
        }
        .form-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, #2e7d32 100%);
        }
        .form-card-header {
            padding: 32px 40px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        /* ── Radio / Type Cards ── */
        .type-selector-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }
        .type-option-card {
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #fff;
        }
        .type-option-card:hover {
            border-color: #a7f3d0;
            background: #f8fafc;
        }
        .type-option-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .type-option-title {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .type-option-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .form-terms-box {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 28px;
        }
        .form-terms-box input[type="checkbox"] {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        .form-terms-label {
            font-size: 0.85rem;
            color: #4b5563;
            line-height: 1.5;
            cursor: pointer;
        }

        .form-actions-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 16px;
            padding-top: 10px;
        }
        .btn-submit {
            background: var(--primary);
            color: white;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(27,94,32,0.3);
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(27,94,32,0.4);
        }

        .toast-notify {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1f2937;
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 999999;
            display: none;
            animation: slideInToast 0.3s ease;
        }
        @keyframes slideInToast {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 900px) {
            .app-header { padding: 24px; }
            .app-body { padding: 24px; }
            .info-grid, .dual-tracking-grid, .form-grid, .form-grid.cols-2, .type-selector-grid { grid-template-columns: 1fr; }
            .details-container { padding: 16px 16px 40px; }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <?php $active_page = (!empty($autoOpenForm) ? 'burial_service' : 'burial'); include BASE_PATH . '/app/views/user/sidebar.php'; ?>

    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <div class="top-bar-title">Damayan Service Details</div>
                <div class="top-bar-subtitle">Death Certificate Processing &amp; Cemetery Plot Management</div>
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
                <a href="<?= url('/user/services/burial-dashboard') ?>">Burial Tracking</a>
                <span class="sep">/</span>
                <span class="current">Death Certificate &amp; Plot Coordination</span>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- CARD 1: ACTIVE REQUEST STATUS & TRACKING                        -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div class="app-card">
                <!-- Header -->
                <header class="app-header">
                    <div class="app-header-left">
                        <div class="app-id-tag">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            CASE REFERENCE: #<?= htmlspecialchars($appId) ?>
                            <?php if ($isCopyRequest): ?>
                                <span style="background:rgba(27,94,32,0.12); color:var(--primary); padding:2px 8px; border-radius:6px; margin-left:6px; font-size:0.72rem; font-weight:800;">ADDITIONAL COPY</span>
                            <?php else: ?>
                                <span style="background:rgba(27,94,32,0.12); color:var(--primary); padding:2px 8px; border-radius:6px; margin-left:6px; font-size:0.72rem; font-weight:800;">NEW REGISTRATION</span>
                            <?php endif; ?>
                        </div>
                        <h1><?= htmlspecialchars($deceasedName) ?></h1>
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                            Submitted: <strong><?= htmlspecialchars($dateSubmitted) ?></strong> • Applicant: <strong><?= htmlspecialchars($requesterName) ?></strong>
                            • Function: <strong style="color:var(--primary);"><?= $isCopyRequest ? 'Request Another Death Certificate' : 'Register Death Certificate' ?></strong>
                        </div>
                    </div>
                    <div class="app-header-actions">
                        <span class="status-badge <?= $isCompleted ? 'approved' : ($isRejected ? 'rejected' : 'coordination') ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                            <?= htmlspecialchars($status) ?>
                        </span>

                        <!-- CLICK REQUEST FORM BUTTON (Reveals the fill-up form) -->
                        <button type="button" class="btn-request-form" id="btn-request-form-top" onclick="toggleRequestForm()">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <span id="btn-request-text-top"><?= $autoOpenForm ? 'Close Request Form' : 'Submit New Request' ?></span>
                        </button>
                    </div>
                </header>

                <!-- Body -->
                <div class="app-body">

                    <!-- Alert: Action Required (Additional Information Requested) -->
                    <?php if ($isInfoRequired): ?>
                    <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 14px; padding: 22px 24px; margin-bottom: 28px;">
                      <div style="display: flex; align-items: flex-start; gap: 14px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        </div>
                        <div style="flex: 1;">
                          <h4 style="margin: 0 0 6px; font-size: 1.05rem; color: #92400e; font-family: 'Lora', serif;">Action Required: Additional Information or Documents Needed</h4>
                          <p style="margin: 0 0 14px; font-size: 0.9rem; color: #78350f; line-height: 1.5;">
                            <strong>Message from Damayan Coordinator (<?= htmlspecialchars($activeBurial['assigned_staff_name'] ?? 'Staff') ?>):</strong><br>
                            <?= nl2br(htmlspecialchars($activeBurial['info_request_notes'] ?? 'Please provide the requested details to proceed.')) ?>
                          </p>
                          
                          <!-- Form to respond to additional requirements -->
                          <form id="respond-info-form" onsubmit="submitRequirementsResponse(event)" style="background: white; border: 1.5px solid #fed7aa; border-radius: 12px; padding: 18px 20px;">
                            <input type="hidden" id="resp-ref-id" value="<?= htmlspecialchars($appId) ?>">
                            <div class="form-group" style="margin-bottom: 12px;">
                              <label class="form-label" style="color: #92400e;">Your Explanation / Requested Details <span class="req">*</span></label>
                              <textarea id="resp-info-text" class="form-control" rows="3" placeholder="Provide the requested details or explanation..." required></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom: 16px;">
                              <label class="form-label" style="color: #92400e;">Upload Supporting Document (Optional)</label>
                              <input type="file" id="resp-doc-file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                              <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Allowed: PDF, JPG, PNG (Max 10MB)</span>
                            </div>
                            <div style="display:flex; justify-content:flex-end;">
                              <button type="submit" class="btn-submit" id="btn-submit-resp" style="background:#b45309; padding:10px 24px; font-size:0.88rem;">
                                Submit Information
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($activeBurial['additional_info_submitted'])): ?>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; margin-bottom: 24px; font-size: 0.85rem; color: #166534;">
                      <strong>Latest Update Submitted on <?= !empty($activeBurial['info_submitted_at']) ? date('M d, Y g:i A', strtotime($activeBurial['info_submitted_at'])) : '—' ?>:</strong><br>
                      <?= htmlspecialchars($activeBurial['additional_info_submitted']) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Deceased & Representative Information Summary -->
                    <div class="info-grid">
                        <div class="info-group">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                                Deceased Person Information
                            </h4>
                            <div class="info-value"><?= htmlspecialchars($deceasedName) ?></div>
                            <div class="info-sub">
                                Gender: <strong><?= htmlspecialchars($activeBurial['deceased_sex'] ?? 'Male') ?></strong><br>
                                Date of Death: <strong><?= htmlspecialchars($dateOfDeath) ?></strong><br>
                                Place of Death: <?= htmlspecialchars($placeOfDeath) ?><br>
                                Residence: <?= htmlspecialchars($activeBurial['residence'] ?? 'N/A') ?><br>
                                Civil Status: <?= htmlspecialchars($activeBurial['civil_status'] ?? 'Married') ?> • Nationality: <?= htmlspecialchars($activeBurial['nationality'] ?? 'Filipino') ?>
                            </div>
                        </div>

                        <div class="info-group">
                            <h4>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Applicant / Representative Details
                            </h4>
                            <div class="info-value"><?= htmlspecialchars($requesterName) ?></div>
                            <div class="info-sub">
                                Relationship: <strong><?= htmlspecialchars($relationship) ?></strong><br>
                                Contact: <?= htmlspecialchars($activeBurial['informant_contact'] ?? ($userPhone ?: 'On File')) ?><br>
                                Email: <?= htmlspecialchars($activeBurial['informant_email'] ?? $userEmail) ?><br>
                                Address: <?= htmlspecialchars($activeBurial['informant_address'] ?? ($userAddress ?: 'On File')) ?>
                            </div>
                            <?php if (!empty($activeBurial['supporting_document'])): ?>
                            <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border);">
                                <a href="<?= asset($activeBurial['supporting_document']) ?>" target="_blank" class="btn-outline" style="padding: 6px 12px; font-size: 0.78rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    View Attached Document
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- DUAL PARALLEL TRACKING CARDS (Connected to Same Case Record) -->
                    <div class="dual-tracking-grid">
                        
                        <?php if ($isCopyRequest): ?>
                        <!-- Track A: Death Certificate Copy Request Tracking -->
                        <div class="track-box active-box">
                            <div class="track-box-header">
                                <div class="track-box-title">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                    Death Certificate Copy Request
                                </div>
                                <span class="status-badge <?= $isCopyReceived ? 'approved' : ($isCopyIssued ? 'coordination' : 'coordination') ?>" style="padding:4px 10px; font-size:0.75rem;">
                                    <?= htmlspecialchars($dcStatus) ?>
                                </span>
                            </div>

                            <div class="track-field-row">
                                <span class="track-field-label">Current Stage:</span>
                                <span class="track-field-val" style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($dcStatus) ?></span>
                            </div>
                            <div class="track-field-row">
                                <span class="track-field-label">Existing Record Verification:</span>
                                <span class="track-field-val">
                                    <?php if ($isRecordVerified): ?>
                                        <span style="color:#047857; font-weight:700;">Verified &amp; Linked to Existing Record</span>
                                    <?php else: ?>
                                        <span style="color:#b45309; font-weight:700;">Pending Staff Record Verification</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (!empty($existingCertNo)): ?>
                                <div class="track-field-row">
                                    <span class="track-field-label">Existing Certificate / Registry #:</span>
                                    <span class="track-field-val"><?= htmlspecialchars($existingCertNo) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="track-field-row">
                                <span class="track-field-label">Reason for Request:</span>
                                <span class="track-field-val"><?= htmlspecialchars($copyReason ?: 'Personal / Official Records') ?></span>
                            </div>
                            <div class="track-field-row">
                                <span class="track-field-label">Copies Requested:</span>
                                <span class="track-field-val"><?= htmlspecialchars($numCopies) ?> Copy<?= $numCopies > 1 ? 'ies' : '' ?></span>
                            </div>
                            <div class="track-field-row">
                                <span class="track-field-label">Copy Issuance:</span>
                                <span class="track-field-val">
                                    <?php if ($isCopyIssued): ?>
                                        <strong style="color:#047857;">Issued on <?= $copyIssuedDate ?></strong>
                                        <?php if (!empty($activeBurial['death_cert_certified_copy_ref'])): ?>
                                            (Ref: <?= htmlspecialchars($activeBurial['death_cert_certified_copy_ref']) ?>)
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-style:italic;">Processing / Awaiting Staff Issuance</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="track-field-row">
                                <span class="track-field-label">Receipt Confirmation:</span>
                                <span class="track-field-val">
                                    <?php if ($isCopyReceived): ?>
                                        <strong style="color:#047857;">Confirmed Received on <?= $copyReceivedDate ?></strong>
                                    <?php elseif ($isCopyIssued): ?>
                                        <span style="color:#b45309; font-weight:700;">Issued — Awaiting Your Receipt Confirmation</span>
                                    <?php else: ?>
                                        <span style="color:var(--text-muted); font-style:italic;">Not marked received until confirmed</span>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <?php if ($isCopyIssued && !$isCopyReceived): ?>
                                <div style="margin-top:14px; padding-top:12px; border-top:1px dashed var(--border); text-align:center;">
                                    <button type="button" class="btn-submit" onclick="confirmCopyReceipt('<?= htmlspecialchars($appId) ?>')" style="padding:10px 20px; font-size:0.85rem; background:#059669; width:100%; justify-content:center;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        I Confirm Receipt of Death Certificate Copy
                                    </button>
                                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Click once you have physically or officially collected your requested copy.</div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($activeBurial['death_cert_remarks'])): ?>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:8px; padding-top:8px; border-top:1px dashed var(--border);">
                                    <strong>Staff Remarks:</strong> <?= htmlspecialchars($activeBurial['death_cert_remarks']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Track B: Verification & Record Safeguards -->
                        <div class="track-box active-box">
                            <div class="track-box-header">
                                <div class="track-box-title">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                                    Record Verification Safeguards
                                </div>
                                <span class="status-badge approved" style="padding:4px 10px; font-size:0.75rem;">
                                    Security Policy
                                </span>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:12px; font-size:0.84rem; line-height:1.5; color:#374151;">
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">1. Existing Record Verification</strong>
                                    Staff verifies the original death registration record exists in the system before an additional copy is released.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">2. Non-Duplicate Record Integrity</strong>
                                    Requests for copies are connected to the original deceased record without creating duplicate entries.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">3. Mandatory Receipt Confirmation</strong>
                                    In accordance with civil guidelines, copies are not closed until user confirmation is logged in the audit trail.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">4. Authorized Office Coordination</strong>
                                    Official death certificates are certified and recorded exclusively by authorized civil registrars.
                                </div>
                            </div>
                        </div>

                        <?php else: ?>
                        <!-- Track A: Death Certificate Processing Tracking -->
                        <div class="track-box <?= $deathCertReq ? 'active-box' : '' ?>">
                            <div class="track-box-header">
                                <div class="track-box-title">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                    Death Certificate Registration
                                </div>
                                <span class="status-badge <?= in_array(strtolower($dcStatus), ['registered', 'certified copy requested or received']) ? 'approved' : 'coordination' ?>" style="padding:4px 10px; font-size:0.75rem;">
                                    <?= htmlspecialchars($dcStatus) ?>
                                </span>
                            </div>

                            <div class="track-field-row">
                                <span class="track-field-label">Current Stage:</span>
                                <span class="track-field-val" style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($dcStatus) ?></span>
                            </div>
                            <div class="track-field-row">
                                <span class="track-field-label">LCRO Registry Number:</span>
                                <span class="track-field-val"><?= htmlspecialchars(($activeBurial['death_cert_registry_no'] ?? '') ?: 'Pending Registration') ?></span>
                            </div>
                            <?php if (!empty($activeBurial['death_cert_registered_date'])): ?>
                                <div class="track-field-row">
                                    <span class="track-field-label">Registration Date:</span>
                                    <span class="track-field-val"><?= date('M d, Y', strtotime($activeBurial['death_cert_registered_date'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($activeBurial['death_cert_lcro_ref'])): ?>
                                <div class="track-field-row">
                                    <span class="track-field-label">LCRO Transmittal Ref:</span>
                                    <span class="track-field-val"><?= htmlspecialchars($activeBurial['death_cert_lcro_ref']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($activeBurial['death_cert_certified_copy_ref'])): ?>
                                <div class="track-field-row">
                                    <span class="track-field-label">PSA / Certified Copy:</span>
                                    <span class="track-field-val"><?= htmlspecialchars($activeBurial['death_cert_certified_copy_ref']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="track-field-row">
                                <span class="track-field-label">Responsible Personnel:</span>
                                <span class="track-field-val"><?= htmlspecialchars(($activeBurial['death_cert_responsible_personnel'] ?? '') ?: 'Damayan Coordinator') ?></span>
                            </div>
                            <?php if (!empty($activeBurial['death_cert_remarks'])): ?>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:8px; padding-top:8px; border-top:1px dashed var(--border);">
                                    <strong>Staff Remarks:</strong> <?= htmlspecialchars($activeBurial['death_cert_remarks']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Track B: Civil Registry Coordination Safeguards -->
                        <div class="track-box active-box">
                            <div class="track-box-header">
                                <div class="track-box-title">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                    Civil Registration Coordination
                                </div>
                                <span class="status-badge approved" style="padding:4px 10px; font-size:0.75rem;">
                                    LCRO Process
                                </span>
                            </div>

                            <div style="display:flex; flex-direction:column; gap:12px; font-size:0.84rem; line-height:1.5; color:#374151;">
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">1. Initial Verification</strong>
                                    Damayan staff reviews deceased personal details and supporting documents before civil filing.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">2. Hospital &amp; Medical Certification</strong>
                                    Coordination with authorized certifying physicians or health officers for death certification.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">3. Civil Registration (LCRO)</strong>
                                    Transmittal to the Local Civil Registrar for official registration and entry in the civil register.
                                </div>
                                <div>
                                    <strong style="color:var(--primary-dark); display:block;">4. Tracking &amp; Certified Copies</strong>
                                    Monitor progress until registration is completed and certified true copies are available.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Notice / Coordination Guidelines -->
                    <div class="guidelines-notice">
                        <div class="guidelines-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </div>
                        <div class="guidelines-text">
                            <h5>Damayan Coordination Guidelines</h5>
                            <p>To submit a new Death Certificate or Cemetery Plot request, click <strong>Submit New Request</strong> below. Our Damayan Staff coordinates civil registration with official government registries and manages Islamic cemetery plots.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer / Action Row -->
                <footer class="app-footer">
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Need a new request? Click <strong>Submit New Request</strong> to fill up the form.
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <button type="button" class="btn-outline" onclick="window.print()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Print Details
                        </button>
                        <a href="<?= url('/user/services/burial-dashboard') ?>" class="btn-outline">
                            View Dashboard
                        </a>
                        <button type="button" class="btn-request-form" id="btn-request-form-bottom" onclick="toggleRequestForm()">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="12" y1="18" x2="12" y2="12"></line>
                                <line x1="9" y1="15" x2="15" y2="15"></line>
                            </svg>
                            <span id="btn-request-text-bottom"><?= $autoOpenForm ? 'Close Request Form' : 'Submit New Request' ?></span>
                        </button>
                    </div>
                </footer>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- CARD 2: THE REQUEST FORM (APPEARS ON CLICKING "SUBMIT REQUEST") -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="burial-form-container">
                <div class="form-card" id="burial-form-card">
                    <!-- Form Header -->
                    <div class="form-card-header">
                        <div class="form-card-title-area">
                            <h2>Death Certificate Request &amp; Registration Form</h2>
                            <p>Register a new death record with the Local Civil Registrar, or request an additional copy of an existing registered death certificate.</p>
                        </div>
                        <button type="button" class="btn-close-form" onclick="toggleRequestForm(false)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Close Form
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form id="burial-request-form" class="form-card-body" onsubmit="submitBurialRequest(event)">
                        
                        <!-- Functionality Selector: 1. Register Death Certificate OR 2. Request Another Death Certificate -->
                        <div class="form-section-title">
                            <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                            1. Select Death Certificate Function
                        </div>

                        <div class="type-selector-grid" style="grid-template-columns: repeat(2, 1fr);">
                            <div class="type-option-card selected" id="opt-type-register" onclick="selectCertFunction('register')">
                                <div class="type-option-title">
                                    <input type="radio" name="cert_function_radio" value="register" checked style="accent-color:var(--primary);" />
                                    Register Death Certificate
                                </div>
                                <div class="type-option-desc">
                                    For reporting a newly deceased person whose death record has not yet been registered through this system.
                                </div>
                            </div>
                            <div class="type-option-card" id="opt-type-copy" onclick="selectCertFunction('copy')">
                                <div class="type-option-title">
                                    <input type="radio" name="cert_function_radio" value="copy" style="accent-color:var(--primary);" />
                                    Request Another Death Certificate
                                </div>
                                <div class="type-option-desc">
                                    Exclusively for users who already have an existing death certificate and want to request another copy.
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="selected-cert-action" name="cert_action_type" value="register" />

                        <!-- Copy-Specific Section (Displayed when 'Request Another Death Certificate' is selected) -->
                        <div id="copy-specific-fields" style="display: none; background: #f0fdf4; border: 1.5px solid #a7f3d0; border-radius: 14px; padding: 22px 24px; margin-bottom: 24px;">
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                Existing Certificate Details &amp; Purpose of Request
                            </div>
                            <p style="margin: 0 0 16px; font-size: 0.83rem; color: #4b5563;">
                                Provide details of your existing certificate. Staff will verify that the official record is available before processing an additional copy. No duplicate death registration record will be created.
                            </p>
                            
                            <div class="form-grid cols-2" style="margin-bottom: 14px;">
                                <div class="form-group">
                                    <label class="form-label">Existing Certificate / Registration Number (If Available)</label>
                                    <input type="text" id="existing-cert-no" class="form-control" placeholder="e.g. 2026-00412 or LCRO Registry #" />
                                    <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Allows staff to locate and link the existing record immediately.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Number of Copies Requested <span class="req">*</span></label>
                                    <input type="number" id="num-copies" class="form-control" value="1" min="1" max="10" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Reason for Requesting Another Copy <span class="req">*</span></label>
                                <textarea id="copy-reason" class="form-control" rows="2" placeholder="e.g. Required for estate settlement, insurance claim, pension benefits, legal proceedings, personal records archive..."></textarea>
                                <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Please specify the exact reason for the additional copy.</span>
                            </div>
                        </div>

                        <!-- Existing Deceased Auto-Fill Selector (Avoid duplicate data entry - Req 4) -->
                        <?php if (!empty($existingDeceased)): ?>
                        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:24px;">
                            <label class="form-label" style="color:var(--primary-dark); margin-bottom:6px; display:block;">
                                Select Registered Deceased Person (Link to Existing Record):
                            </label>
                            <select id="select-existing-deceased" class="form-select" onchange="autoFillExistingDeceased(this)">
                                <option value="">-- Or enter deceased person details below --</option>
                                <?php foreach($existingDeceased as $ed): ?>
                                    <option value="<?= $ed['id'] ?>"
                                        data-fname="<?= htmlspecialchars($ed['first_name'] ?? '') ?>"
                                        data-mname="<?= htmlspecialchars($ed['middle_name'] ?? '') ?>"
                                        data-lname="<?= htmlspecialchars($ed['last_name'] ?? '') ?>"
                                        data-sex="<?= htmlspecialchars($ed['sex'] ?? 'Male') ?>"
                                        data-dob="<?= htmlspecialchars($ed['date_of_birth'] ?? '') ?>"
                                        data-dod="<?= htmlspecialchars($ed['date_of_death'] ?? '') ?>"
                                        data-pod="<?= htmlspecialchars($ed['place_of_death'] ?? '') ?>"
                                        data-residence="<?= htmlspecialchars($ed['residence'] ?? '') ?>"
                                        data-civil="<?= htmlspecialchars($ed['civil_status'] ?? 'Married') ?>"
                                        data-nationality="<?= htmlspecialchars($ed['nationality'] ?? 'Filipino') ?>"
                                        data-religion="<?= htmlspecialchars($ed['religion'] ?? 'Islam') ?>">
                                        <?= htmlspecialchars(trim(($ed['first_name'] ?? '') . ' ' . ($ed['last_name'] ?? ''))) ?> (DOD: <?= htmlspecialchars($ed['date_of_death'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="selected-deceased-id" value="" />
                        </div>
                        <?php endif; ?>

                        <!-- Section 2: Deceased Personal Information -->
                        <div class="form-section-title" id="form-deceased-section-title">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
                            2. Deceased Person Information
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
                                <label class="form-label">Sex / Gender <span class="req">*</span></label>
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
                                <input type="text" id="deceased-religion" class="form-control" value="Islam" />
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

                        <!-- Supporting Document Upload -->
                        <div class="form-grid cols-1">
                            <div class="form-group">
                                <label class="form-label">Upload Supporting Document or Existing Certificate Copy (Optional)</label>
                                <input type="file" id="supporting-document-file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" />
                                <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Upload Hospital Certificate of Death, existing Death Certificate copy, Medical Certificate, or Valid Representative ID (PDF, JPG, PNG - Max 10MB)</span>
                            </div>
                        </div>

                        <!-- Cemetery Preference Section Hidden per Restriction 4 -->
                        <div id="cemetery-preference-section" style="display:none;">
                            <input type="hidden" id="deceased-cemetery" value="ISCAG Designated Muslim Cemetery" />
                            <input type="hidden" id="deceased-pref-section" value="" />
                        </div>

                        <!-- Section 3: Applicant / Informant Details -->
                        <div class="form-section-title" style="margin-top: 16px;">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            3. Requester / Authorized Representative Information
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Requester Name <span class="req">*</span></label>
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
                                    <option value="Authorized Representative">Authorized Representative</option>
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
                                <label class="form-label">Residential Address</label>
                                <input type="text" id="informant-address" class="form-control" value="<?= htmlspecialchars($userAddress) ?>" placeholder="Current residence address" />
                            </div>
                        </div>

                        <div class="form-grid cols-1">
                            <div class="form-group">
                                <label class="form-label">Additional Instructions / Notes</label>
                                <textarea id="additional-notes" class="form-control" rows="2" placeholder="Any special notes, family coordination details, or registry liaison instructions..."></textarea>
                            </div>
                        </div>

                        <!-- Terms & Certification -->
                        <div class="form-terms-box">
                            <input type="checkbox" id="burial-certify" required />
                            <label for="burial-certify" class="form-terms-label">
                                I hereby certify that the information provided is true and correct. I authorize the ISCAG Damayan Department to coordinate death certificate processing and verification with authorized offices.
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions-row">
                            <button type="button" class="btn-outline" onclick="toggleRequestForm(false)">Cancel</button>
                            <button type="submit" class="btn-submit" id="btn-submit-burial">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span id="btn-submit-burial-text">Submit Death Certificate Registration</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Dashboard Link -->
            <div style="text-align: center; margin-top: 36px;">
                <a href="<?= url('/user/services/burial-dashboard') ?>" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    ← Back to Death Certificate Tracking Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div class="toast-notify" id="burial-toast"></div>

<script>
    // ── Function Selector Switching (Register vs Copy) ──
    function selectCertFunction(func) {
        document.getElementById('selected-cert-action').value = func;
        const isCopy = (func === 'copy');
        
        const regCard = document.getElementById('opt-type-register');
        const copyCard = document.getElementById('opt-type-copy');
        
        if (isCopy) {
            if (copyCard) {
                copyCard.classList.add('selected');
                const r = copyCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (regCard) regCard.classList.remove('selected');
            document.getElementById('copy-specific-fields').style.display = 'block';
            document.getElementById('form-deceased-section-title').innerHTML = '<svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:6px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg> 2. Deceased Person Information (Existing Certificate Record)';
            document.getElementById('btn-submit-burial-text').textContent = 'Submit Copy Request';
        } else {
            if (regCard) {
                regCard.classList.add('selected');
                const r = regCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (copyCard) copyCard.classList.remove('selected');
            document.getElementById('copy-specific-fields').style.display = 'none';
            document.getElementById('form-deceased-section-title').innerHTML = '<svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:6px;"><circle cx="12" cy="7" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg> 2. Deceased Person Information (New Registration)';
            document.getElementById('btn-submit-burial-text').textContent = 'Submit Death Certificate Registration';
        }
    }

    // ── Auto-fill Existing Deceased ──
    function autoFillExistingDeceased(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if(!opt || !opt.value) {
            document.getElementById('selected-deceased-id').value = '';
            return;
        }

        document.getElementById('selected-deceased-id').value = opt.value;
        document.getElementById('deceased-first-name').value = opt.getAttribute('data-fname') || '';
        document.getElementById('deceased-middle-name').value = opt.getAttribute('data-mname') || '';
        document.getElementById('deceased-last-name').value = opt.getAttribute('data-lname') || '';
        document.getElementById('deceased-sex').value = opt.getAttribute('data-sex') || 'Male';
        document.getElementById('deceased-dob').value = (opt.getAttribute('data-dob') || '').substring(0, 10);
        document.getElementById('deceased-dod').value = (opt.getAttribute('data-dod') || '').substring(0, 10);
        document.getElementById('deceased-pod').value = opt.getAttribute('data-pod') || '';
        document.getElementById('deceased-residence').value = opt.getAttribute('data-residence') || '';
        document.getElementById('deceased-civil-status').value = opt.getAttribute('data-civil') || 'Married';
        document.getElementById('deceased-citizenship').value = opt.getAttribute('data-nationality') || 'Filipino';
        document.getElementById('deceased-religion').value = opt.getAttribute('data-religion') || 'Islam';
    }

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
            if (btnTopText) btnTopText.textContent = 'Submit New Request';
            if (btnBottomText) btnBottomText.textContent = 'Submit New Request';
        } else {
            container.style.display = 'block';
            if (btnTopText) btnTopText.textContent = 'Close Request Form';
            if (btnBottomText) btnBottomText.textContent = 'Close Request Form';
            
            setTimeout(() => {
                container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        }
    }

    // ── Confirm Receipt of Certificate Copy ──
    async function confirmCopyReceipt(refId) {
        if (!confirm('Have you physically or officially received your requested Death Certificate copy?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('ref_id', refId);

            const res = await fetch('<?= url("/user/services/burial/confirm-copy") ?>', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                const toast = document.getElementById('burial-toast');
                toast.innerHTML = '<strong>Receipt Confirmed!</strong> Your receipt of the certificate copy has been recorded.<br><span style="font-size:0.8rem;opacity:0.9;">Updating request status...</span>';
                toast.style.display = 'block';
                setTimeout(() => {
                    window.location.reload();
                }, 1300);
            } else {
                alert(data.error || data.message || 'Failed to confirm receipt.');
            }
        } catch(err) {
            console.error(err);
            alert('An error occurred while confirming receipt.');
        }
    }

    // ── Submit Request via AJAX ──
    async function submitBurialRequest(event) {
        event.preventDefault();
        
        const certifyCheck = document.getElementById('burial-certify');
        if (!certifyCheck || !certifyCheck.checked) {
            alert('Please check the certification box to confirm the request.');
            return;
        }

        const btnSubmit = document.getElementById('btn-submit-burial');
        const originalText = btnSubmit.innerHTML;

        const certAction = document.getElementById('selected-cert-action').value;

        // Validation for Copy Request
        if (certAction === 'copy') {
            const reason = document.getElementById('copy-reason')?.value.trim();
            if (!reason) {
                alert('Please specify the reason for requesting another copy of the death certificate.');
                document.getElementById('copy-reason').focus();
                return;
            }
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Submitting Request...';

        // Package as FormData to support file upload
        const formData = new FormData();
        formData.append('cert_action_type', certAction);
        formData.append('request_type', 'death_certificate');
        formData.append('death_cert_requested', '1');
        formData.append('cemetery_plot_requested', '0');
        
        if (certAction === 'copy') {
            formData.append('copy_reason', document.getElementById('copy-reason')?.value.trim() || '');
            formData.append('existing_cert_no', document.getElementById('existing-cert-no')?.value.trim() || '');
            formData.append('num_copies', document.getElementById('num-copies')?.value || '1');
        }

        const deceasedId = document.getElementById('selected-deceased-id')?.value;
        if(deceasedId) {
            formData.append('deceased_id', deceasedId);
        }

        formData.append('firstName', document.getElementById('deceased-first-name').value.trim());
        formData.append('middleName', document.getElementById('deceased-middle-name').value.trim());
        formData.append('lastName', document.getElementById('deceased-last-name').value.trim());
        formData.append('sex', document.getElementById('deceased-sex').value);
        formData.append('dob', document.getElementById('deceased-dob').value || '');
        formData.append('dod', document.getElementById('deceased-dod').value);
        formData.append('pod', document.getElementById('deceased-pod').value.trim());
        formData.append('residence', document.getElementById('deceased-residence').value.trim());
        formData.append('religion', document.getElementById('deceased-religion').value || 'Islam');
        formData.append('civilStatus', document.getElementById('deceased-civil-status').value);
        formData.append('citizenship', document.getElementById('deceased-citizenship').value || 'Filipino');
        
        formData.append('relationship', document.getElementById('informant-rel').value);
        formData.append('informantName', document.getElementById('informant-name').value.trim());
        formData.append('informantContact', document.getElementById('informant-phone').value.trim());
        formData.append('informantEmail', document.getElementById('informant-email').value.trim());
        formData.append('informantAddress', document.getElementById('informant-address').value.trim());
        formData.append('additionalNotes', document.getElementById('additional-notes').value.trim());

        const fileInput = document.getElementById('supporting-document-file');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('supporting_document', fileInput.files[0]);
        }

        try {
            const response = await fetch('<?= url("/user/services/burial/submit") ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                const toast = document.getElementById('burial-toast');
                const ref = data.ref_id || 'Submitted';
                const successTitle = (certAction === 'copy') ? 'Copy Request Submitted!' : 'Registration Submitted!';
                toast.innerHTML = '<strong>' + successTitle + '</strong><br>Reference ID: <code>#' + ref + '</code><br><span style="font-size:0.8rem;opacity:0.9;">Opening tracking details...</span>';
                toast.style.display = 'block';

                setTimeout(() => {
                    window.location.href = '<?= url("/user/services/burial-details") ?>?id=' + encodeURIComponent(ref);
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to submit request.');
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert(error.message || 'Failed to submit request. Please check your inputs and try again.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // ── Submit Additional Requirements Response via AJAX ──
    async function submitRequirementsResponse(event) {
        event.preventDefault();
        const refId = document.getElementById('resp-ref-id').value;
        const infoText = document.getElementById('resp-info-text').value.trim();
        const btn = document.getElementById('btn-submit-resp');
        const originalText = btn.innerHTML;

        if (!infoText) {
            alert('Please provide the requested details or explanation.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Submitting...';

        const formData = new FormData();
        formData.append('ref_id', refId);
        formData.append('additional_info', infoText);

        const fileInput = document.getElementById('resp-doc-file');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('additional_doc', fileInput.files[0]);
        }

        try {
            const res = await fetch('<?= url("/user/services/burial/respond-info") ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                const toast = document.getElementById('burial-toast');
                toast.innerHTML = '<strong>Success!</strong> Additional information submitted to Damayan staff.<br><span style="font-size:0.8rem;opacity:0.9;">Updating record...</span>';
                toast.style.display = 'block';
                setTimeout(() => {
                    window.location.reload();
                }, 1400);
            } else {
                throw new Error(data.message || 'Failed to submit information.');
            }
        } catch (err) {
            console.error(err);
            alert(err.message || 'Error submitting response.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
</script>

</body>
</html>
