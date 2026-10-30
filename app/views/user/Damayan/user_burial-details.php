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
$status = $hasApplication ? ($activeBurial['status'] ?? 'Pending') : 'No Active Request';
if ($hasApplication && !in_array($status, ['Accepted', 'Rejected', 'Completed'])) {
    $status = 'Pending';
}
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

// 6-stage Death Certificate Pipeline
// Submitted → Under Review → Approved → Certificate Prepared → Ready for Release → Completed
$dcStageRanks = [
    'submitted' => 1,
    'request received' => 1,
    'under review' => 2,
    'additional information required' => 2,
    'pending' => 2,
    'approved' => 3,
    'accepted' => 3,
    'awaiting authorized certificate preparation' => 3,
    'certificate prepared' => 4,
    'certificate prepared and certified' => 4,
    'ready for release' => 5,
    'submitted for civil registration' => 5,
    'completed' => 6,
    'registered' => 6,
    'certified copy requested or received' => 6,
    'released' => 6
];
$dcRank = $dcStageRanks[strtolower($dcStatus)] ?? 1;
$dcPercentages = [1 => 0, 2 => 20, 3 => 40, 4 => 60, 5 => 80, 6 => 100];
$dcProgressPercent = $dcPercentages[$dcRank] ?? 0;

$scheduleHistory = ($hasApplication && !empty($activeBurial['id'])) ? $burialModel->getScheduleHistory((int)$activeBurial['id']) : [];
$isAuthorizedForRelease = $hasApplication && in_array(strtolower($dcStatus), ['ready for release', 'completed', 'registered', 'released']);

$autoOpenForm = isset($startWithFormOpen) && $startWithFormOpen === true;
if (isset($_GET['action']) && in_array($_GET['action'], ['request', 'form', 'fill', 'copy'])) {
    $autoOpenForm = true;
}

if (!function_exists('getBurialBadgeMeta')) {
    function getBurialBadgeMeta(string $rawStatus, bool $isCompleted = false, bool $isRejected = false): array {
        $st = strtolower(trim($rawStatus));
        if ($isCompleted || in_array($st, ['completed', 'burial completed', 'registered', 'plot assigned', 'copy received', 'copy received & confirmed'])) {
            return ['class' => 'approved', 'label' => 'Completed'];
        }
        if ($isRejected || $st === 'rejected') {
            return ['class' => 'rejected', 'label' => 'Rejected'];
        }
        if ($st === 'accepted' || strpos($st, 'accepted') !== false) {
            return ['class' => 'approved', 'label' => 'Accepted'];
        }
        if ($st === 'additional information required' || $st === 'action required') {
            return ['class' => 'warning', 'label' => 'Action Required'];
        }
        if ($st === 'no active request') {
            return ['class' => 'none', 'label' => 'No Active Request'];
        }
        return ['class' => 'pending', 'label' => 'Pending'];
    }
}
$headerBadge = getBurialBadgeMeta($status, $isCompleted, $isRejected);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Death Certificate Management — ISCAG Damayan</title>
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
        /* ── Modern Status Badges ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.2;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            white-space: nowrap;
        }

        /* Status Dot Indicator with gentle pulse */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            display: inline-block;
            flex-shrink: 0;
            position: relative;
        }
        .status-dot::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background-color: currentColor;
            opacity: 0.35;
            animation: statusPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes statusPulse {
            0%, 100% { transform: scale(1); opacity: 0.35; }
            50% { transform: scale(1.6); opacity: 0; }
        }

        /* 1. Request Received - Crisp, High-Contrast Soft Blue */
        .status-badge.received {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #93c5fd;
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.08);
        }

        /* 2. Pending / Under Review - Warm Amber */
        .status-badge.pending {
            background: #fffbeb;
            color: #b45309;
            border: 1.5px solid #fde68a;
            box-shadow: 0 2px 8px rgba(180, 83, 9, 0.08);
        }

        /* 3. In Coordination / In Progress - Fresh Teal */
        .status-badge.coordination {
            background: #f0fdfa;
            color: #0f766e;
            border: 1.5px solid #99f6e4;
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.08);
        }

        /* 4. Action Required / Additional Information - Warning Orange */
        .status-badge.warning, .status-badge.action-required {
            background: #fff7ed;
            color: #c2410c;
            border: 1.5px solid #fed7aa;
            box-shadow: 0 2px 8px rgba(194, 65, 12, 0.08);
        }

        /* 5. Approved / Completed / Registered - Vibrant Forest Green */
        .status-badge.approved, .status-badge.completed {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #a7f3d0;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.08);
        }

        /* 6. Rejected - Red */
        .status-badge.rejected {
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fecaca;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.08);
        }

        /* 7. None / Neutral */
        .status-badge.none, .status-badge.neutral {
            background: #f3f4f6;
            color: #6b7280;
            border: 1.5px solid #e5e7eb;
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

        /* ── Certificate Tracking Card ── */
        .dual-tracking-grid {
            display: block;
            margin-bottom: 24px;
        }
        .track-box {
            background: #fafbfc;
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 24px 28px;
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
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .type-selector-grid {
                grid-template-columns: 1fr;
            }
        }
        .type-option-card {
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 18px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
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
            line-height: 1.45;
        }
        .dept-reg-card {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .dept-reg-card label {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.86rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }
        .dept-reg-card .form-control {
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 0.92rem;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .dept-reg-card .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(27,94,32,0.12);
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

        /* ── Print Media Styles ── */
        @media print {
            .top-bar, .sidebar, .breadcrumb-nav, .app-header-actions, .app-footer, 
            .guidelines-notice, #burial-form-container, .toast-notify, button, .btn-outline,
            .btn-request-form {
                display: none !important;
            }
            body, .app-wrapper, .main-content, .details-container {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .app-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                border-radius: 8px !important;
            }
            .app-header {
                padding: 20px 24px !important;
                background: white !important;
                border-bottom: 2px solid #059669 !important;
            }
            .app-body {
                padding: 20px 24px !important;
            }
            .track-box {
                border: 1px solid #cbd5e1 !important;
                background: white !important;
                padding: 18px !important;
            }
            .appointment-history-container {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                margin-top: 14px !important;
            }
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
                <div class="top-bar-subtitle">Death Certificate Civil Registration &amp; Official Issuance</div>
            </div>
            <div class="top-bar-actions">
                <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Main Dashboard</a>
            </div>
        </div>

        <div class="details-container">
            <!-- Breadcrumbs -->
            <div class="breadcrumb-nav">
                <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                <span class="sep">/</span>
                <a href="<?= url('/user/services/burial-dashboard') ?>">Damayan Dashboard</a>
                <span class="sep">/</span>
                <span class="current">Death Certificate Processing &amp; Release</span>
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
                            • Service: <strong style="color:var(--primary);">
                                <?php if ($isCopyRequest): ?>
                                    Request Another Death Certificate (Copy)
                                <?php else: ?>
                                    Death Certificate Registration
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>
                    <div class="app-header-actions">
                        <span class="status-badge <?= $headerBadge['class'] ?>">
                            <span class="status-dot"></span>
                            <?= htmlspecialchars($headerBadge['label']) ?>
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

                    <!-- PROMINENT BANNER: CERTIFICATE READY FOR RELEASE WITH CONFIRMED SCHEDULE -->
                    <?php if ($hasApplication && !empty($activeBurial['scheduled_release_date']) && empty($activeBurial['certificate_released_at']) && strtolower($dcStatus) === 'ready for release'): ?>
                    <div style="background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border: 2px solid #10b981; border-radius: 16px; padding: 24px 28px; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.12); margin-bottom: 28px;">
                      <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:20px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:flex-start; gap:16px;">
                          <div style="width:50px; height:50px; border-radius:12px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                          </div>
                          <div>
                            <div style="display:inline-flex; align-items:center; gap:6px; background:#d1fae5; color:#065f46; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:6px; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">
                              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg> Confirmed Claiming Appointment
                            </div>
                            <h2 style="font-family:'Lora',serif; font-size:1.4rem; font-weight:800; color:#064e3b; margin:0 0 6px;">
                              Your Death Certificate Is Ready for Release
                            </h2>
                            <p style="font-size:0.95rem; color:#047857; margin:0; line-height:1.5;">
                              Please go to the <strong>Damayan Department</strong> to claim your death certificate on the scheduled date and time below.
                            </p>
                          </div>
                        </div>
                        <span class="status-badge approved" style="font-size:0.8rem; padding:6px 14px;">
                          Ready for Release
                        </span>
                      </div>

                      <!-- Actual Saved Schedule from Database -->
                      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-top:20px; background:#ffffff; border:1.5px solid #a7f3d0; border-radius:12px; padding:16px 20px;">
                        <div>
                          <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled Date</span>
                          <strong style="font-size:1.15rem; color:#0f172a;"><?= date('F d, Y', strtotime($activeBurial['scheduled_release_date'])) ?></strong>
                        </div>
                        <div>
                          <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled Time</span>
                          <strong style="font-size:1.15rem; color:#0f172a;"><?= !empty($activeBurial['scheduled_release_time']) ? date('g:i A', strtotime($activeBurial['scheduled_release_time'])) : '10:00 AM' ?></strong>
                        </div>
                        <div>
                          <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Location</span>
                          <strong style="font-size:1.05rem; color:#065f46;"><?= htmlspecialchars($activeBurial['release_location'] ?: 'Damayan Department') ?></strong>
                        </div>
                        <div>
                          <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled By</span>
                          <span style="font-size:0.95rem; color:#374151; font-weight:700;"><?= htmlspecialchars($activeBurial['release_scheduled_by_name'] ?: 'Damayan Admin') ?></span>
                        </div>
                      </div>

                      <?php if (!empty($activeBurial['release_instructions'])): ?>
                      <div style="margin-top:14px; background:#f0fdf4; border-left:4px solid #059669; padding:12px 16px; border-radius:0 8px 8px 0; font-size:0.86rem; color:#065f46;">
                        <strong style="display:block; margin-bottom:3px; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.04em;">Instructions from Damayan Department:</strong>
                        <div style="color:#1f2937; line-height:1.5;"><?= nl2br(htmlspecialchars($activeBurial['release_instructions'])) ?></div>
                      </div>
                      <?php endif; ?>
                    </div>
                    <?php elseif ($hasApplication && !empty($activeBurial['certificate_released_at'])): ?>
                    <!-- BANNER: CERTIFICATE CLAIMED & COMPLETED -->
                    <div style="background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border: 2px solid #059669; border-radius: 16px; padding: 20px 24px; margin-bottom: 28px;">
                      <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:44px; height:44px; border-radius:10px; background:#059669; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                        </div>
                        <div>
                          <div style="font-size:0.72rem; font-weight:800; color:#065f46; text-transform:uppercase; letter-spacing:0.05em;">Certificate Claimed &bull; Request Completed</div>
                          <h4 style="margin:2px 0; font-size:1.15rem; color:#064e3b; font-family:'Lora',serif;">Death Certificate Released to Recipient</h4>
                          <div style="font-size:0.86rem; color:#047857;">
                            Claimed by <strong><?= htmlspecialchars($activeBurial['certificate_recipient_name'] ?: 'Claimant') ?></strong> on <strong><?= date('F d, Y \a\t g:i A', strtotime($activeBurial['certificate_released_at'])) ?></strong> (Handled by: <?= htmlspecialchars($activeBurial['certificate_released_by'] ?: 'Damayan Staff') ?>).
                            <?php if (!empty($activeBurial['release_notes'])): ?>
                              <span style="display:block; margin-top:2px; font-size:0.8rem; color:#475569;"><em>Note: <?= htmlspecialchars($activeBurial['release_notes']) ?></em></span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php elseif ($hasApplication && !empty($activeBurial['release_cancelled_at']) && empty($activeBurial['scheduled_release_date'])): ?>
                    <!-- BANNER: CLAIMING SCHEDULE CANCELLED NOTICE -->
                    <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 14px; padding: 18px 22px; margin-bottom: 28px;">
                      <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:8px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                        </div>
                        <div>
                          <div style="font-size:0.75rem; font-weight:800; color:#92400e; text-transform:uppercase; letter-spacing:0.04em;">Claiming Appointment Update</div>
                          <div style="font-size:0.95rem; font-weight:700; color:#78350f; margin-top:1px;">
                            Claiming Schedule Cancelled by Damayan Department
                          </div>
                          <div style="font-size:0.84rem; color:#92400e; margin-top:2px;">
                            Reason: <strong><?= htmlspecialchars($activeBurial['release_cancellation_reason'] ?: 'Administrative review') ?></strong>. The request is currently at <strong>Certificate Prepared</strong> stage.
                          </div>
                        </div>
                      </div>
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
                                <?php $copyBadge = getBurialBadgeMeta($dcStatus, $isCopyReceived, false); ?>
                                <span class="status-badge <?= $copyBadge['class'] ?>" style="padding:4px 12px; font-size:0.75rem;">
                                    <span class="status-dot" style="width:6px; height:6px;"></span>
                                    <?= htmlspecialchars($copyBadge['label']) ?>
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

                        <?php else: ?>
                        <!-- Track A: Death Certificate Processing Tracking -->
                        <div class="track-box <?= $deathCertReq ? 'active-box' : '' ?>">
                            <div class="track-box-header">
                                <div class="track-box-title">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                    Death Certificate Processing Pipeline
                                </div>
                                <?php $dcBadge = getBurialBadgeMeta($dcStatus, in_array(strtolower($dcStatus), ['completed', 'registered', 'certified copy requested or received']), false); ?>
                                <span class="status-badge <?= $dcBadge['class'] ?>" style="padding:4px 12px; font-size:0.75rem;">
                                    <span class="status-dot" style="width:6px; height:6px;"></span>
                                    <?= htmlspecialchars($dcBadge['label']) ?>
                                </span>
                            </div>

                            <!-- 6-Stage Tracking Pipeline Progression (Req 3) -->
                            <div style="background:#fff; border:1px solid var(--border); border-radius:10px; padding:12px; margin-bottom:14px;">
                              <div style="font-size:0.72rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:10px;">
                                Processing Pipeline:
                              </div>
                              <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:6px; text-align:center;">
                                <?php
                                  $dcSteps = [
                                    1 => '1. Submitted',
                                    2 => '2. Under Review',
                                    3 => '3. Approved',
                                    4 => '4. Prepared',
                                    5 => '5. Ready for Release',
                                    6 => '6. Completed'
                                  ];
                                  foreach($dcSteps as $sNum => $sLabel):
                                    $isPassed = ($dcRank > $sNum || ($dcRank === 6 && $sNum === 6));
                                    $isCurr = ($dcRank === $sNum);
                                    $bg = $isPassed ? '#d1fae5' : ($isCurr ? '#059669' : '#f1f5f9');
                                    $col = $isPassed ? '#065f46' : ($isCurr ? '#ffffff' : '#64748b');
                                    $border = $isPassed ? '#a7f3d0' : ($isCurr ? '#047857' : '#e2e8f0');
                                ?>
                                  <div style="background:<?= $bg ?>; color:<?= $col ?>; border:1px solid <?= $border ?>; border-radius:6px; padding:5px 4px; font-size:0.7rem; font-weight:700;">
                                    <?= $sLabel ?>
                                    <?php if ($sNum === 5 && !empty($activeBurial['scheduled_release_date'])): ?>
                                      <div style="font-size:0.62rem; margin-top:2px; font-weight:800; color:<?= $isCurr ? '#fff' : '#047857' ?>;">
                                        <?= date('M d', strtotime($activeBurial['scheduled_release_date'])) ?>
                                      </div>
                                    <?php endif; ?>
                                  </div>
                                <?php endforeach; ?>
                              </div>
                            </div>

                            <!-- Status & Schedule Data -->
                            <div class="track-field-row">
                                <span class="track-field-label">Current Status:</span>
                                <span class="track-field-val" style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($dcStatus) ?></span>
                            </div>

                            <?php if (!empty($activeBurial['scheduled_release_date']) && empty($activeBurial['certificate_released_at'])): ?>
                                <div style="background:#ecfdf5; border:1.5px solid #a7f3d0; border-radius:10px; padding:12px 14px; margin:10px 0 12px;">
                                    <div style="font-size:0.75rem; font-weight:800; color:#065f46; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:5px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                        Claiming Appointment
                                    </div>
                                    <div class="track-field-row" style="margin-bottom:4px;">
                                        <span class="track-field-label" style="color:#047857;">Scheduled Date &amp; Time:</span>
                                        <span class="track-field-val" style="color:#064e3b; font-weight:800;">
                                            <?= date('F d, Y', strtotime($activeBurial['scheduled_release_date'])) ?> at <?= !empty($activeBurial['scheduled_release_time']) ? date('g:i A', strtotime($activeBurial['scheduled_release_time'])) : '10:00 AM' ?>
                                        </span>
                                    </div>
                                    <div class="track-field-row" style="margin-bottom:4px;">
                                        <span class="track-field-label" style="color:#047857;">Claiming Location:</span>
                                        <span class="track-field-val" style="color:#064e3b;"><?= htmlspecialchars($activeBurial['release_location'] ?: 'Damayan Department') ?></span>
                                    </div>
                                    <div class="track-field-row" style="margin-bottom:0;">
                                        <span class="track-field-label" style="color:#047857;">Scheduled By:</span>
                                        <span class="track-field-val" style="color:#064e3b; font-weight:600;"><?= htmlspecialchars($activeBurial['release_scheduled_by_name'] ?: 'Damayan Admin') ?></span>
                                    </div>
                                    <?php if (!empty($activeBurial['release_instructions'])): ?>
                                        <div style="margin-top:6px; padding-top:6px; border-top:1px dashed #bbf7d0; font-size:0.8rem; color:#065f46;">
                                            <strong>Instructions:</strong> <?= htmlspecialchars($activeBurial['release_instructions']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif (!empty($activeBurial['certificate_released_at'])): ?>
                                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:8px; padding:10px 12px; margin:10px 0; font-size:0.82rem; color:#065f46;">
                                    <strong>Physically Released:</strong> Claimed by <strong><?= htmlspecialchars($activeBurial['certificate_recipient_name'] ?: 'Recipient') ?></strong> on <?= date('M d, Y \a\t g:i A', strtotime($activeBurial['certificate_released_at'])) ?> (Staff: <?= htmlspecialchars($activeBurial['certificate_released_by'] ?: 'Admin') ?>).
                                </div>
                            <?php endif; ?>

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


                            <!-- Print / Claiming Slip Actions Gated by Authorization (Req 5) -->
                            <div style="margin-top:14px; padding-top:10px; border-top:1px solid var(--border);">
                                <?php if ($isAuthorizedForRelease): ?>
                                    <button type="button" class="btn-outline" style="width:100%; justify-content:center; background:#ecfdf5; border-color:#a7f3d0; color:#065f46; font-weight:700;" onclick="printClaimingSlip()">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                        Print Claiming Slip / Certificate Details
                                    </button>
                                <?php else: ?>
                                    <div style="font-size:0.74rem; color:var(--text-muted); text-align:center; padding:6px; background:#f9fafb; border-radius:6px; border:1px solid #e5e7eb;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:3px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        Official claiming documents unlocked when authorized for release.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Appointment History Table at the Bottom of Tracking -->
                    <?php if (!empty($scheduleHistory)): ?>
                    <div class="appointment-history-container" style="margin-top:22px; background:#fff; border:1px solid var(--border); border-radius:12px; padding:18px 20px; box-shadow:var(--shadow-sm);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:8px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:28px; height:28px; border-radius:6px; background:#ecfdf5; color:var(--primary); display:flex; align-items:center; justify-content:center;">
                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                </div>
                                <h4 style="margin:0; font-size:0.92rem; font-weight:800; color:var(--text-dark);">Claiming Appointment History</h4>
                            </div>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); background:var(--bg); border:1px solid var(--border); padding:3px 10px; border-radius:100px;">
                                <?= count($scheduleHistory) ?> <?= count($scheduleHistory) === 1 ? 'Record' : 'Records' ?>
                            </span>
                        </div>
                        <div style="overflow-x:auto; border:1px solid #e2e8f0; border-radius:8px;">
                            <table style="width:100%; border-collapse:collapse; font-size:0.8rem; text-align:left;">
                                <thead>
                                    <tr style="background:#f8fafc; border-bottom:1.5px solid #e2e8f0; color:#475569; font-size:0.72rem; text-transform:uppercase; letter-spacing:0.03em;">
                                        <th style="padding:10px 12px; font-weight:700;">Action</th>
                                        <th style="padding:10px 12px; font-weight:700;">Scheduled Appointment</th>
                                        <th style="padding:10px 12px; font-weight:700;">Location</th>
                                        <th style="padding:10px 12px; font-weight:700;">Reason / Instructions</th>
                                        <th style="padding:10px 12px; font-weight:700;">Processed By</th>
                                        <th style="padding:10px 12px; font-weight:700;">Date Recorded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($scheduleHistory as $sh): 
                                        $badgeBg = ($sh['action_type'] === 'SCHEDULED' ? '#ecfdf5' : ($sh['action_type'] === 'RESCHEDULED' ? '#fffbeb' : ($sh['action_type'] === 'CANCELLED' ? '#fef2f2' : '#f0fdf4')));
                                        $badgeCol = ($sh['action_type'] === 'SCHEDULED' ? '#065f46' : ($sh['action_type'] === 'RESCHEDULED' ? '#92400e' : ($sh['action_type'] === 'CANCELLED' ? '#991b1b' : '#047857')));
                                    ?>
                                    <tr style="border-bottom:1px solid #f1f5f9;">
                                        <td style="padding:10px 12px; vertical-align:middle;">
                                            <span style="font-weight:700; padding:3px 8px; border-radius:4px; font-size:0.7rem; background:<?= $badgeBg ?>; color:<?= $badgeCol ?>; display:inline-block;">
                                                <?= htmlspecialchars($sh['action_type']) ?>
                                            </span>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:var(--text-dark);">
                                            <?php if (!empty($sh['scheduled_date'])): ?>
                                                <strong><?= date('M d, Y', strtotime($sh['scheduled_date'])) ?></strong>
                                                <?php if (!empty($sh['scheduled_time'])): ?>
                                                    at <?= date('g:i A', strtotime($sh['scheduled_time'])) ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color:var(--text-muted);">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:var(--text-muted);">
                                            <?= htmlspecialchars(($sh['release_location'] ?? $sh['location'] ?? '') ?: 'Damayan Department') ?>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:var(--text-dark); max-width:220px;">
                                            <?= !empty($sh['reason']) ? htmlspecialchars($sh['reason']) : (!empty($sh['change_reason']) ? htmlspecialchars($sh['change_reason']) : '<span style="color:var(--text-muted); font-style:italic;">None</span>') ?>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:var(--text-dark); font-weight:600;">
                                            <?= htmlspecialchars(($sh['action_by_name'] ?? $sh['admin_name'] ?? '') ?: 'Damayan Staff') ?>
                                        </td>
                                        <td style="padding:10px 12px; vertical-align:middle; color:var(--text-muted); font-size:0.75rem; white-space:nowrap;">
                                            <?= !empty($sh['created_at']) ? date('M d, Y g:i A', strtotime($sh['created_at'])) : '—' ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Notice / Coordination Guidelines -->
                    <div class="guidelines-notice">
                        <div class="guidelines-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </div>
                        <div class="guidelines-text">
                            <h5>Damayan Coordination Guidelines</h5>
                            <p>To submit a new Death Certificate request, click <strong>Submit New Request</strong> below. Our Damayan Staff coordinates civil registration with official government registries and schedules claiming releases.</p>
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
                            <h2>Death Certificate Processing Form</h2>
                            <p>Exclusively for processing and scheduling release of death certificates recorded within the ISCAG Damayan Department archive.</p>
                        </div>
                        <button type="button" class="btn-close-form" onclick="toggleRequestForm(false)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Close Form
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form id="burial-request-form" class="form-card-body" onsubmit="submitBurialRequest(event)">

                        <!-- Department Archive Requirement Notice -->
                        <div style="background: #f0fdf4; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 16px 20px; margin-bottom: 22px;">
                            <div style="font-weight: 800; font-size: 0.92rem; color: var(--primary-dark); margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                                ISCAG Damayan Department Certificate Processing Requirement
                            </div>
                            <p style="margin: 0; font-size: 0.82rem; color: #374151; line-height: 1.5;">
                                In accordance with Damayan Department guidelines, processing is available <strong>only for death certificates that exist within the department records</strong>. Please identify the certificate using the registry number or select a recorded deceased person from the department archive below.
                            </p>
                        </div>
                        
                        <!-- Functionality Selector: 1. Register Death OR 2. Request Another Death Certificate -->
                        <div class="form-section-title">
                            <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                            1. Select Processing Purpose
                        </div>

                        <div class="type-selector-grid">
                            <div class="type-option-card selected" id="opt-type-register" onclick="selectCertFunction('register')">
                                <div class="type-option-title">
                                    <input type="radio" name="cert_function_radio" value="register" checked style="accent-color:var(--primary);" />
                                    Initial Certificate Processing &amp; Issuance
                                </div>
                                <div class="type-option-desc">
                                    For processing and scheduled release of an official death certificate on file with the Damayan Department.
                                </div>
                            </div>
                            <div class="type-option-card" id="opt-type-copy" onclick="selectCertFunction('copy')">
                                <div class="type-option-title">
                                    <input type="radio" name="cert_function_radio" value="copy" style="accent-color:var(--primary);" />
                                    Additional Certificate Copy Request
                                </div>
                                <div class="type-option-desc">
                                    Exclusively for users requesting additional certified copies of an existing registered death certificate.
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="selected-cert-action" name="cert_action_type" value="register" />

                        <!-- Department Certificate Registry Number (Positioned directly below the cards) -->
                        <div class="dept-reg-card">
                            <label class="form-label" for="reg-cert-no">
                                DEPARTMENT CERTIFICATE REGISTRY NUMBER <span class="req" style="color:var(--danger,#dc2626); font-weight:bold;">*</span>:
                            </label>
                            <input type="text" id="reg-cert-no" class="form-control" placeholder="e.g. 2026-00412, #2023-2-0001113, or LCRO Registry #" onblur="checkRegCertNumber(this.value)" oninput="debouncedCheckRegCert(this.value)" />
                            <input type="hidden" id="linked-reg-cert-id" value="" />
                            <span id="reg-cert-feedback" style="font-size:0.78rem; color:var(--text-muted); margin-top:6px; display:block; line-height:1.4;">Enter the certificate registration number to verify it exists within the ISCAG Damayan Department records.</span>
                        </div>

                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <!-- A. COPY-SPECIFIC CONTAINER (Existing Certificate Details)    -->
                        <!-- Displayed ONLY when 'Request Another Death Certificate' is on -->
                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <div id="copy-specific-container" style="display: none; margin-bottom: 28px;">
                            <div class="form-section-title">
                                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                2. Existing Certificate Details &amp; Purpose of Request
                            </div>

                            <div style="background: #f0fdf4; border: 1.5px solid #a7f3d0; border-radius: 14px; padding: 20px 24px; margin-bottom: 22px;">
                                <div style="font-weight: 800; font-size: 0.95rem; color: var(--primary-dark); margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                                    Existing Record Verification Policy
                                </div>
                                <p style="margin: 0; font-size: 0.83rem; color: #4b5563; line-height: 1.5;">
                                    Provide the details of your existing registered certificate. Staff will locate and verify the record in civil archives before an additional copy is issued. No duplicate death registration record will be created.
                                </p>
                            </div>

                            <?php if (!empty($existingDeceased)): ?>
                            <div style="background:#f9fafb; border:1px solid var(--border); border-radius:12px; padding:16px 20px; margin-bottom:20px;">
                                <label class="form-label" style="color:var(--primary-dark); margin-bottom:6px; display:block;">
                                    Select Registered Deceased Person (Optional — if already in ISCAG records):
                                </label>
                                <select id="copy-select-deceased" class="form-select" onchange="autoFillCopyDeceased(this)">
                                    <option value="">-- Or enter certificate details manually below --</option>
                                    <?php foreach($existingDeceased as $ed): 
                                        $decFullName = trim(($ed['first_name'] ?? '') . ' ' . ($ed['last_name'] ?? ''));
                                        if (!empty($ed['full_name'])) $decFullName = $ed['full_name'];
                                    ?>
                                        <option value="<?= $ed['id'] ?>"
                                            data-fullname="<?= htmlspecialchars($decFullName) ?>"
                                            data-dod="<?= htmlspecialchars($ed['date_of_death'] ?? '') ?>"
                                            data-ref="<?= htmlspecialchars($ed['ref_id'] ?? '') ?>">
                                            <?= htmlspecialchars($decFullName) ?> (DOD: <?= htmlspecialchars($ed['date_of_death'] ?? 'N/A') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="form-grid cols-2" style="margin-bottom: 14px;">
                                <div class="form-group">
                                    <label class="form-label">Deceased Person Full Name (as appearing on Certificate) <span class="req">*</span></label>
                                    <input type="text" id="copy-deceased-name" class="form-control" placeholder="e.g. Abdullah Masoud Al-Mansoor" />
                                    <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Name as recorded on the original death certificate.</span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Existing Certificate / Registry Number (If Available)</label>
                                    <input type="text" id="existing-cert-no" class="form-control" placeholder="e.g. 2026-00412, #2023-2-0001113, or LCRO Registry #" onblur="checkExistingCertNumber(this.value)" oninput="debouncedCheckCert(this.value)" />
                                    <input type="hidden" id="linked-encoded-cert-id" value="" />
                                    <span id="existing-cert-feedback" style="font-size:0.75rem; color:var(--text-muted); margin-top:2px; display:block;">Allows staff to locate and link the existing record immediately.</span>
                                </div>
                            </div>

                            <div class="form-grid cols-2" style="margin-bottom: 14px;">
                                <div class="form-group">
                                    <label class="form-label">Number of Copies Requested <span class="req">*</span></label>
                                    <input type="number" id="num-copies" class="form-control" value="1" min="1" max="10" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Death on Certificate (Optional)</label>
                                    <input type="date" id="copy-deceased-dod" class="form-control" />
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 14px;">
                                <label class="form-label">Reason / Purpose for Requesting Another Copy <span class="req">*</span></label>
                                <textarea id="copy-reason" class="form-control" rows="2" placeholder="e.g. Required for estate settlement, insurance claim, pension benefits, legal proceedings, personal records archive..."></textarea>
                                <span style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Please specify the exact purpose or requesting authority for this additional copy.</span>
                            </div>

                        </div>

                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <!-- B. REGISTRATION DECEASED FIELDS CONTAINER                   -->
                        <!-- Displayed ONLY when 'Initial Certificate Processing' is on  -->
                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <div id="registration-deceased-fields" style="display: block;">

                            <!-- Existing Deceased Auto-Fill Selector (Avoid duplicate data entry - Req 4) -->
                            <?php if (!empty($existingDeceased)): ?>
                            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px 20px; margin-bottom:24px;">
                                <label class="form-label" style="color:var(--primary-dark); margin-bottom:6px; display:block;">
                                    Or Select Registered Deceased Person (Recorded in Department):
                                </label>
                                <select id="select-existing-deceased" class="form-select" onchange="autoFillExistingDeceased(this)">
                                    <option value="">-- Or enter certificate registry number above --</option>
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
                                    <input type="date" id="deceased-dob" class="form-control" onchange="validateDamayanDates(false)" oninput="validateDamayanDates(false)" />
                                    <span id="deceased-dob-error" style="color:#dc2626; font-size:0.75rem; display:none; margin-top:3px;"></span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Date of Death <span class="req">*</span></label>
                                    <input type="date" id="deceased-dod" class="form-control" value="<?= date('Y-m-d') ?>" required onchange="validateDamayanDates(false)" oninput="validateDamayanDates(false)" />
                                </div>
                            </div>

                            <div class="form-grid cols-2">
                                <div class="form-group">
                                    <label class="form-label">Place of Death <span class="req">*</span></label>
                                    <input type="text" id="deceased-pod" class="form-control" placeholder="Residence address, facility, or complete location" required />
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
                                        <option value="" selected>Select Status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nationality</label>
                                    <input type="text" id="deceased-citizenship" class="form-control" value="Filipino" />
                                </div>
                            </div>


                        </div>

                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <!-- C. REQUESTER / AUTHORIZED REPRESENTATIVE INFORMATION        -->
                        <!-- Always displayed for both request types                     -->
                        <!-- ═══════════════════════════════════════════════════════════ -->
                        <div class="form-section-title" id="requester-section-title" style="margin-top: 16px;">
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
                                I hereby certify that the information provided is true and correct. I authorize the ISCAG Damayan Department to coordinate death certificate processing and verification with authorized civil registries.
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
                    ← Back to Death Certificate Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div class="toast-notify" id="burial-toast"></div>

<script src="<?= asset('js/phone-validator.js') ?>"></script>
<script>
    // ── Function Selector Switching (Register vs Copy) ──
    function selectCertFunction(func) {
        document.getElementById('selected-cert-action').value = func;
        const isCopy = (func === 'copy');
        
        const regCard = document.getElementById('opt-type-register');
        const copyCard = document.getElementById('opt-type-copy');
        const copyContainer = document.getElementById('copy-specific-container');
        const regContainer = document.getElementById('registration-deceased-fields');
        const requesterTitle = document.getElementById('requester-section-title');
        const btnSubmitText = document.getElementById('btn-submit-burial-text');

        // List of registration inputs that have required attributes
        const regRequiredIds = [
            'deceased-first-name',
            'deceased-last-name',
            'deceased-dod',
            'deceased-pod',
            'deceased-residence'
        ];
        
        if (isCopy) {
            if (copyCard) {
                copyCard.classList.add('selected');
                const r = copyCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (regCard) regCard.classList.remove('selected');

            // Show ONLY Existing Certificate Details & Purpose of Request + Requester
            if (copyContainer) copyContainer.style.display = 'block';
            if (regContainer) regContainer.style.display = 'none';

            // Remove required attribute from hidden registration inputs so HTML5 validation passes
            regRequiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.removeAttribute('required');
            });

            // Add required attribute to copy fields
            document.getElementById('copy-deceased-name')?.setAttribute('required', 'required');
            document.getElementById('copy-reason')?.setAttribute('required', 'required');

            if (requesterTitle) {
                requesterTitle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> 2. Requester / Authorized Representative Information';
            }
            if (btnSubmitText) btnSubmitText.textContent = 'Submit Copy Request';
        } else {
            if (regCard) {
                regCard.classList.add('selected');
                const r = regCard.querySelector('input[type="radio"]');
                if (r) r.checked = true;
            }
            if (copyCard) copyCard.classList.remove('selected');

            // Show Registration Deceased inputs + Requester
            if (copyContainer) copyContainer.style.display = 'none';
            if (regContainer) regContainer.style.display = 'block';

            // Restore required attribute to registration inputs
            regRequiredIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });

            // Remove required attribute from hidden copy fields
            document.getElementById('copy-deceased-name')?.removeAttribute('required');
            document.getElementById('copy-reason')?.removeAttribute('required');

            if (requesterTitle) {
                requesterTitle.innerHTML = '<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> 3. Requester / Authorized Representative Information';
            }
            if (btnSubmitText) btnSubmitText.textContent = 'Submit Death Certificate Registration';
        }
    }

    // ── Auto-fill Existing Deceased (For Registration Form) ──
    function autoFillExistingDeceased(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if(!opt || !opt.value) {
            document.getElementById('selected-deceased-id').value = '';
            return;
        }

        document.getElementById('selected-deceased-id').value = opt.value;
        isCertVerifiedInDept = true;
        document.getElementById('deceased-first-name').value = opt.getAttribute('data-fname') || '';
        document.getElementById('deceased-middle-name').value = opt.getAttribute('data-mname') || '';
        document.getElementById('deceased-last-name').value = opt.getAttribute('data-lname') || '';
        document.getElementById('deceased-sex').value = opt.getAttribute('data-sex') || 'Male';
        document.getElementById('deceased-dob').value = (opt.getAttribute('data-dob') || '').substring(0, 10);
        document.getElementById('deceased-dod').value = (opt.getAttribute('data-dod') || '').substring(0, 10);
        document.getElementById('deceased-pod').value = opt.getAttribute('data-pod') || '';
        document.getElementById('deceased-residence').value = opt.getAttribute('data-residence') || '';
        document.getElementById('deceased-civil-status').value = opt.getAttribute('data-civil') || '';
        document.getElementById('deceased-citizenship').value = opt.getAttribute('data-nationality') || 'Filipino';
        document.getElementById('deceased-religion').value = opt.getAttribute('data-religion') || 'Islam';
    }

    // ── Auto-fill Existing Deceased (For Copy Request Form) ──
    function autoFillCopyDeceased(selectElem) {
        const opt = selectElem.options[selectElem.selectedIndex];
        if(!opt || !opt.value) {
            document.getElementById('selected-deceased-id').value = '';
            return;
        }

        document.getElementById('selected-deceased-id').value = opt.value;
        const fullName = opt.getAttribute('data-fullname') || opt.text.split('(')[0].trim();
        if (fullName && document.getElementById('copy-deceased-name')) {
            document.getElementById('copy-deceased-name').value = fullName;
        }
        const dod = (opt.getAttribute('data-dod') || '').substring(0, 10);
        if (dod && document.getElementById('copy-deceased-dod')) {
            document.getElementById('copy-deceased-dod').value = dod;
        }
        const refNo = opt.getAttribute('data-ref') || '';
        if (refNo && document.getElementById('existing-cert-no') && !document.getElementById('existing-cert-no').value) {
            document.getElementById('existing-cert-no').value = refNo;
        }
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

    let isCertVerifiedInDept = false;
    let certLookupTimeout = null;

    function debouncedCheckCert(val) {
        clearTimeout(certLookupTimeout);
        certLookupTimeout = setTimeout(() => {
            checkExistingCertNumber(val);
        }, 300);
    }

    let regCertLookupTimeout = null;
    function debouncedCheckRegCert(val) {
        clearTimeout(regCertLookupTimeout);
        regCertLookupTimeout = setTimeout(() => {
            checkRegCertNumber(val);
        }, 300);
    }

    async function checkRegCertNumber(val) {
        val = (val || '').trim();
        const fb = document.getElementById('reg-cert-feedback');
        const hiddenRegId = document.getElementById('linked-reg-cert-id');
        if (!val) {
            if (fb) fb.innerHTML = 'Enter the certificate registration number to verify it exists within the ISCAG Damayan Department records.';
            if (hiddenRegId) hiddenRegId.value = '';
            isCertVerifiedInDept = false;
            return;
        }
        try {
            const res = await fetch('<?= url('/user/damayan/lookup-cert') ?>?reg=' + encodeURIComponent(val));
            const data = await res.json();
            if (data && data.found && data.record) {
                isCertVerifiedInDept = true;
                if (hiddenRegId) hiddenRegId.value = data.record.id || '';
                const hiddenEncId = document.getElementById('linked-encoded-cert-id');
                if (hiddenEncId) hiddenEncId.value = data.record.id || '';
                const existingCertInput = document.getElementById('existing-cert-no');
                if (existingCertInput && (!existingCertInput.value || existingCertInput.value !== val)) {
                    existingCertInput.value = val;
                }
                const copyNameInput = document.getElementById('copy-deceased-name');
                if (copyNameInput && !copyNameInput.value.trim() && data.record.deceased_name) {
                    copyNameInput.value = data.record.deceased_name;
                }
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#ecfdf5; border:1px solid #10b981; border-radius:8px; padding:10px 14px; margin-top:6px;">
                            <div style="color:#059669; font-weight:800; display:flex; align-items:center; gap:6px; font-size:0.85rem;">
                                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:#059669;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                ✓ Verified in Damayan Department Archive
                            </div>
                            <div style="font-size:0.8rem; color:#064e3b; margin-top:4px; line-height:1.4;">
                                <strong>Deceased:</strong> ${escapeHtml(data.record.deceased_name)}<br>
                                <strong>Registry Number:</strong> <code>${escapeHtml(data.record.registration_number)}</code><br>
                                <strong>Archive Source:</strong> ${escapeHtml(data.record.source || 'Encoded Archive')}
                            </div>
                        </div>
                    `;
                }
                // Prepopulate deceased names if empty
                const decName = data.record.deceased_name || '';
                const parts = decName.split(' ');
                const fNameInput = document.getElementById('deceased-first-name');
                const lNameInput = document.getElementById('deceased-last-name');
                const mNameInput = document.getElementById('deceased-middle-name');
                if (fNameInput && !fNameInput.value.trim() && parts.length > 0) {
                    fNameInput.value = parts[0];
                }
                if (lNameInput && !lNameInput.value.trim() && parts.length > 1) {
                    lNameInput.value = parts[parts.length - 1];
                }
                if (mNameInput && !mNameInput.value.trim() && parts.length > 2) {
                    mNameInput.value = parts.slice(1, -1).join(' ');
                }
            } else {
                isCertVerifiedInDept = false;
                if (hiddenRegId) hiddenRegId.value = '';
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; margin-top:6px; color:#92400e; font-size:0.8rem; line-height:1.4;">
                            <strong>⚠ Record Not Found in Department Archive:</strong> Only death certificates registered within the Damayan Department can be processed. Please check the registry number or select a recorded deceased record below.
                        </div>
                    `;
                }
            }
        } catch(e) {}
    }

    function autoFillExistingCopyDeceased(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return;
        isCertVerifiedInDept = true;
        const fullname = opt.getAttribute('data-fullname') || '';
        const dod = opt.getAttribute('data-dod') || '';
        const ref = opt.getAttribute('data-ref') || '';

        const nameInput = document.getElementById('copy-deceased-name');
        if (nameInput && fullname) nameInput.value = fullname;

        const dodInput = document.getElementById('copy-deceased-dod');
        if (dodInput && dod) dodInput.value = dod;

        const certInput = document.getElementById('existing-cert-no');
        if (certInput && ref) {
            certInput.value = ref;
            checkExistingCertNumber(ref);
        }
    }

    async function checkExistingCertNumber(val) {
        val = (val || '').trim();
        const fb = document.getElementById('existing-cert-feedback');
        const hiddenEncId = document.getElementById('linked-encoded-cert-id');
        if (!val) {
            if (fb) fb.innerHTML = 'Allows staff to locate and link the existing record immediately.';
            if (hiddenEncId) hiddenEncId.value = '';
            isCertVerifiedInDept = false;
            return;
        }
        try {
            const res = await fetch('<?= url('/user/damayan/lookup-cert') ?>?reg=' + encodeURIComponent(val));
            const data = await res.json();
            if (data && data.found && data.record) {
                isCertVerifiedInDept = true;
                if (hiddenEncId) hiddenEncId.value = data.record.id;
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#ecfdf5; border:1px solid #10b981; border-radius:8px; padding:10px 14px; margin-top:6px;">
                            <div style="color:#059669; font-weight:800; display:flex; align-items:center; gap:6px; font-size:0.85rem;">
                                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:#059669;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                ✓ Verified in Damayan Department Archive
                            </div>
                            <div style="font-size:0.8rem; color:#064e3b; margin-top:4px; line-height:1.4;">
                                <strong>Deceased:</strong> ${escapeHtml(data.record.deceased_name)}<br>
                                <strong>Representative:</strong> ${escapeHtml(data.record.representative_name || 'N/A')}<br>
                                <strong>Registered #:</strong> ${escapeHtml(data.record.registration_number)}
                            </div>
                        </div>
                    `;
                }
                const decInput = document.getElementById('copy-deceased-name');
                if (decInput && !decInput.value.trim()) {
                    decInput.value = data.record.deceased_name;
                }
            } else {
                isCertVerifiedInDept = false;
                if (hiddenEncId) hiddenEncId.value = '';
                if (fb) {
                    fb.innerHTML = `
                        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; margin-top:6px; color:#92400e; font-size:0.8rem; line-height:1.4;">
                            <strong>⚠ Record Not Found in Department Archive:</strong> Only death certificates registered within the Damayan Department can be processed. Please check the registry number or select a recorded deceased record.
                        </div>
                    `;
                }
            }
        } catch(e) {}
    }

    // ── Birthday and Date of Death Validation ──
    function validateDamayanDates(showAlert = false) {
        const dobInput = document.getElementById('deceased-dob');
        const dodInput = document.getElementById('deceased-dod');
        const errEl = document.getElementById('deceased-dob-error');
        if (!dobInput || !dodInput) return true;
        const dobVal = dobInput.value;
        const dodVal = dodInput.value;
        if (dobVal && dodVal && dobVal >= dodVal) {
            const msg = 'Invalid date of birth. The date of birth must be earlier than the date of death.';
            if (errEl) {
                errEl.textContent = msg;
                errEl.style.display = 'block';
            }
            dobInput.style.borderColor = '#dc2626';
            if (showAlert) {
                alert(msg);
                dobInput.focus();
            }
            return false;
        } else {
            if (errEl) {
                errEl.textContent = '';
                errEl.style.display = 'none';
            }
            dobInput.style.borderColor = '';
            return true;
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

        const certAction = document.getElementById('selected-cert-action').value;

        // Date of birth and date of death validation for registration
        if (certAction === 'register') {
            const dobVal = document.getElementById('deceased-dob')?.value;
            const dodVal = document.getElementById('deceased-dod')?.value;
            if (dobVal && dodVal && dobVal >= dodVal) {
                alert('Invalid date of birth. The date of birth must be earlier than the date of death.');
                const errEl = document.getElementById('deceased-dob-error');
                if (errEl) {
                    errEl.textContent = 'Invalid date of birth. The date of birth must be earlier than the date of death.';
                    errEl.style.display = 'block';
                }
                document.getElementById('deceased-dob')?.focus();
                return;
            }
        }

        const selectedDecId = document.getElementById('selected-deceased-id')?.value;
        const copySelectDecId = document.getElementById('copy-select-deceased')?.value;
        const linkedEncId = document.getElementById('linked-encoded-cert-id')?.value;
        const linkedRegId = document.getElementById('linked-reg-cert-id')?.value;
        const regCertNo = document.getElementById('reg-cert-no')?.value.trim() || '';
        const existingCertNo = document.getElementById('existing-cert-no')?.value.trim() || '';

        // Verification Gate: Certificate must be confirmed in department archive
        const isVerified = isCertVerifiedInDept || selectedDecId || copySelectDecId || linkedEncId || linkedRegId || (certAction === 'register' ? regCertNo : existingCertNo);

        if (!isVerified) {
            alert('Processing is only available for death certificates recorded within the ISCAG Damayan Department. Please enter a valid registration number or select a recorded deceased person from the department archive.');
            if (certAction === 'register') {
                document.getElementById('reg-cert-no')?.focus();
            } else {
                document.getElementById('existing-cert-no')?.focus();
            }
            return;
        }

        const btnSubmit = document.getElementById('btn-submit-burial');
        const originalText = btnSubmit.innerHTML;

        // Package as FormData to support file upload
        const formData = new FormData();
        formData.append('cert_action_type', certAction);

        if (certAction === 'copy') {
            const decName = document.getElementById('copy-deceased-name')?.value.trim();
            const reason = document.getElementById('copy-reason')?.value.trim();
            if (!decName) {
                alert('Please enter the deceased person full name as appearing on the certificate.');
                document.getElementById('copy-deceased-name')?.focus();
                return;
            }
            if (!reason) {
                alert('Please specify the reason for requesting another copy of the death certificate.');
                document.getElementById('copy-reason')?.focus();
                return;
            }

            formData.append('request_type', 'death_certificate');
            formData.append('death_cert_requested', '1');
            formData.append('cemetery_plot_requested', '0');
            formData.append('deceased_name', decName);
            formData.append('copy_reason', reason);
            formData.append('existing_cert_no', existingCertNo);
            formData.append('encoded_cert_id', linkedEncId);
            formData.append('num_copies', document.getElementById('num-copies')?.value || '1');
            formData.append('dod', document.getElementById('copy-deceased-dod')?.value || '');

            const copyDoc = document.getElementById('copy-supporting-document');
            if (copyDoc && copyDoc.files.length > 0) {
                formData.append('supporting_document', copyDoc.files[0]);
            }
        } else {
            formData.append('request_type', 'death_certificate');
            formData.append('death_cert_requested', '1');
            formData.append('cemetery_plot_requested', '0');
            formData.append('existing_cert_no', regCertNo);
            formData.append('encoded_cert_id', linkedRegId);
            formData.append('firstName', document.getElementById('deceased-first-name')?.value.trim() || '');
            formData.append('middleName', document.getElementById('deceased-middle-name')?.value.trim() || '');
            formData.append('lastName', document.getElementById('deceased-last-name')?.value.trim() || '');
            formData.append('sex', document.getElementById('deceased-sex')?.value || 'Male');
            formData.append('dob', document.getElementById('deceased-dob')?.value || '');
            formData.append('dod', document.getElementById('deceased-dod')?.value || '');
            formData.append('pod', document.getElementById('deceased-pod')?.value.trim() || '');
            formData.append('residence', document.getElementById('deceased-residence')?.value.trim() || '');
            formData.append('religion', document.getElementById('deceased-religion')?.value || 'Islam');
            formData.append('civilStatus', document.getElementById('deceased-civil-status')?.value || '');
            formData.append('citizenship', document.getElementById('deceased-citizenship')?.value || 'Filipino');

            const fileInput = document.getElementById('supporting-document-file');
            if (fileInput && fileInput.files.length > 0) {
                formData.append('supporting_document', fileInput.files[0]);
            }
        }

        const deceasedId = document.getElementById('selected-deceased-id')?.value;
        if(deceasedId) {
            formData.append('deceased_id', deceasedId);
        }

        const informantPhone = document.getElementById('informant-phone').value.trim();
        if (informantPhone !== '' && typeof isValidPhone === 'function' && !isValidPhone(informantPhone)) {
            alert('Please enter a valid phone number with the correct country code and number format.');
            document.getElementById('informant-phone').focus();
            return;
        }

        formData.append('relationship', document.getElementById('informant-rel').value);
        formData.append('informantName', document.getElementById('informant-name').value.trim());
        formData.append('informantContact', informantPhone);
        formData.append('informantEmail', document.getElementById('informant-email').value.trim());
        formData.append('informantAddress', document.getElementById('informant-address').value.trim());
        formData.append('additionalNotes', document.getElementById('additional-notes').value.trim());

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Submitting Request...';

        try {
            const response = await fetch('<?= url("/user/services/burial/submit") ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                const toast = document.getElementById('burial-toast');
                const ref = data.ref_id || 'Submitted';
                const successTitle = (certAction === 'copy') ? 'Copy Request Submitted!' : 'Death Certificate Registration Submitted!';
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

    function printClaimingSlip() {
        const slipData = {
            refId: <?= json_encode($appId) ?>,
            status: <?= json_encode($dcStatus) ?>,
            deceasedName: <?= json_encode($deceasedName) ?>,
            dateOfDeath: <?= json_encode($dateOfDeath) ?>,
            placeOfDeath: <?= json_encode($placeOfDeath) ?>,
            requesterName: <?= json_encode($requesterName) ?>,
            relationship: <?= json_encode($relationship) ?>,
            contact: <?= json_encode($activeBurial['informant_contact'] ?? ($userPhone ?: 'On File')) ?>,
            scheduledDate: <?= json_encode(!empty($activeBurial['scheduled_release_date']) ? date('F d, Y', strtotime($activeBurial['scheduled_release_date'])) : 'To Be Scheduled') ?>,
            scheduledTime: <?= json_encode(!empty($activeBurial['scheduled_release_time']) ? date('g:i A', strtotime($activeBurial['scheduled_release_time'])) : 'Regular Office Hours (8:00 AM - 5:00 PM)') ?>,
            releaseLocation: <?= json_encode(($activeBurial['release_location'] ?? '') ?: 'Damayan Department, ISCAG Office') ?>,
            scheduledBy: <?= json_encode(($activeBurial['release_scheduled_by_name'] ?? '') ?: 'Damayan Admin') ?>,
            instructions: <?= json_encode(($activeBurial['release_instructions'] ?? '') ?: 'Please bring this official claiming slip, a valid government-issued ID of the authorized recipient, and any official receipts upon claiming at the Damayan Department counter.') ?>,
            registryNo: <?= json_encode(($activeBurial['death_cert_registry_no'] ?? '') ?: 'Under Department Processing') ?>,
            registeredDate: <?= json_encode(!empty($activeBurial['death_cert_registered_date']) ? date('F d, Y', strtotime($activeBurial['death_cert_registered_date'])) : 'Pending Registration') ?>,
            certifiedRef: <?= json_encode(($activeBurial['death_cert_certified_copy_ref'] ?? '') ?: 'N/A') ?>,
            printDate: <?= json_encode(date('F d, Y \a\t g:i A')) ?>
        };

        const logoUrl = window.location.origin + '/assets/favicon_io/favicon.ico';
        const printHtml = `
          <!DOCTYPE html>
          <html lang="en">
          <head>
            <meta charset="UTF-8">
            <title>ISCAG Claiming Slip - ${slipData.refId}</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
            <style>
              * { box-sizing: border-box; }
              body { font-family: 'Inter', -apple-system, sans-serif; color: #1e293b; margin: 0; padding: 24px; background: #fff; font-size: 13px; }
              .slip-wrapper { max-width: 720px; margin: 0 auto; border: 2px solid #059669; border-radius: 12px; padding: 28px 30px; position: relative; }
              .slip-top-border { height: 5px; background: linear-gradient(90deg, #059669 0%, #d97706 100%); margin: -28px -30px 22px -30px; border-radius: 10px 10px 0 0; }
              .header-row { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 20px; }
              .brand-col { display: flex; align-items: center; gap: 12px; }
              .brand-col img { width: 48px; height: 48px; object-fit: contain; }
              .org-title { font-family: 'Lora', serif; font-size: 1.15rem; font-weight: 800; color: #065f46; line-height: 1.2; }
              .dept-subtitle { font-size: 0.8rem; font-weight: 600; color: #64748b; margin-top: 2px; }
              .ref-badge { text-align: right; }
              .ref-num { font-size: 1.25rem; font-weight: 800; color: #065f46; font-family: monospace; letter-spacing: 0.04em; }
              .ref-label { font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #64748b; }
              .slip-title-banner { background: #f0fdf4; border: 1.5px solid #a7f3d0; border-radius: 8px; padding: 10px 16px; text-align: center; margin-bottom: 20px; }
              .slip-title { font-size: 1.05rem; font-weight: 800; color: #064e3b; text-transform: uppercase; letter-spacing: 0.05em; margin: 0; }
              .slip-subtitle { font-size: 0.75rem; color: #047857; margin-top: 3px; font-weight: 500; }
              .section-heading { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; margin: 16px 0 8px; display: flex; align-items: center; gap: 6px; }
              .section-heading::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
              .data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 0.82rem; }
              .data-table td { padding: 7px 10px; border: 1px solid #e2e8f0; }
              .data-table td.label-cell { width: 34%; background: #f8fafc; font-weight: 700; color: #475569; }
              .data-table td.val-cell { color: #0f172a; }
              .sched-box { background: #ecfdf5; border: 1.5px solid #6ee7b7; border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
              .sched-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
              .sched-item { display: flex; flex-direction: column; }
              .sched-item-lbl { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #047857; }
              .sched-item-val { font-size: 0.95rem; font-weight: 800; color: #064e3b; margin-top: 2px; }
              .inst-note { margin-top: 10px; padding-top: 8px; border-top: 1px dashed #a7f3d0; font-size: 0.78rem; color: #065f46; line-height: 1.45; }
              .sig-row { display: flex; justify-content: space-between; margin-top: 28px; padding-top: 16px; }
              .sig-box { width: 45%; text-align: center; }
              .sig-line { border-top: 1.5px solid #334155; margin-top: 42px; padding-top: 6px; font-size: 0.8rem; font-weight: 700; color: #1e293b; }
              .sig-caption { font-size: 0.72rem; color: #64748b; margin-top: 2px; }
              .footer-note { margin-top: 22px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 0.72rem; color: #64748b; text-align: center; line-height: 1.4; }
              @media print {
                body { padding: 0; }
                .slip-wrapper { border: 1.5px solid #059669; }
              }
            </style>
          </head>
          <body>
            <div class="slip-wrapper">
              <div class="slip-top-border"></div>
              
              <div class="header-row">
                <div class="brand-col">
                  <img src="${logoUrl}" alt="ISCAG Logo" onerror="this.style.display='none'" />
                  <div>
                    <div class="org-title">Islamic Studies, Call and Guidance of the Philippines</div>
                    <div class="dept-subtitle">Social Welfare &amp; Damayan Department • Civil Registry Services</div>
                  </div>
                </div>
                <div class="ref-badge">
                  <div class="ref-label">Claiming Reference</div>
                  <div class="ref-num">#${slipData.refId}</div>
                </div>
              </div>

              <div class="slip-title-banner">
                <h2 class="slip-title">Official Death Certificate Claiming Slip</h2>
                <div class="slip-subtitle">Present this slip along with one (1) valid government ID at the Damayan counter</div>
              </div>

              <div class="sched-box">
                <div class="sched-grid">
                  <div class="sched-item">
                    <span class="sched-item-lbl">Scheduled Claiming Date:</span>
                    <span class="sched-item-val">${slipData.scheduledDate}</span>
                  </div>
                  <div class="sched-item">
                    <span class="sched-item-lbl">Scheduled Claiming Time:</span>
                    <span class="sched-item-val">${slipData.scheduledTime}</span>
                  </div>
                  <div class="sched-item">
                    <span class="sched-item-lbl">Claiming Counter / Location:</span>
                    <span class="sched-item-val">${slipData.releaseLocation}</span>
                  </div>
                  <div class="sched-item">
                    <span class="sched-item-lbl">Authorized Coordinator:</span>
                    <span class="sched-item-val">${slipData.scheduledBy}</span>
                  </div>
                </div>
                ${slipData.instructions ? `
                  <div class="inst-note">
                    <strong>Claiming Instructions:</strong> ${slipData.instructions}
                  </div>
                ` : ''}
              </div>

              <div class="section-heading">Deceased &amp; Certificate Record</div>
              <table class="data-table">
                <tr>
                  <td class="label-cell">Deceased Full Name</td>
                  <td class="val-cell"><strong>${slipData.deceasedName}</strong></td>
                </tr>
                <tr>
                  <td class="label-cell">Date of Death</td>
                  <td class="val-cell">${slipData.dateOfDeath}</td>
                </tr>
                <tr>
                  <td class="label-cell">Place of Death</td>
                  <td class="val-cell">${slipData.placeOfDeath}</td>
                </tr>
                <tr>
                  <td class="label-cell">LCRO Registry Number</td>
                  <td class="val-cell"><strong>${slipData.registryNo}</strong></td>
                </tr>
                <tr>
                  <td class="label-cell">Registration Date</td>
                  <td class="val-cell">${slipData.registeredDate}</td>
                </tr>
                <tr>
                  <td class="label-cell">Current Processing Status</td>
                  <td class="val-cell"><strong>${slipData.status}</strong></td>
                </tr>
              </table>

              <div class="section-heading">Authorized Claimant Details</div>
              <table class="data-table">
                <tr>
                  <td class="label-cell">Authorized Claimant / Representative</td>
                  <td class="val-cell"><strong>${slipData.requesterName}</strong></td>
                </tr>
                <tr>
                  <td class="label-cell">Relationship to Deceased</td>
                  <td class="val-cell">${slipData.relationship}</td>
                </tr>
                <tr>
                  <td class="label-cell">Contact Number</td>
                  <td class="val-cell">${slipData.contact}</td>
                </tr>
              </table>

              <div class="sig-row">
                <div class="sig-box">
                  <div class="sig-line">${slipData.requesterName}</div>
                  <div class="sig-caption">Signature of Claimant over Printed Name<br>(Sign upon physical receipt)</div>
                </div>
                <div class="sig-box">
                  <div class="sig-line">${slipData.scheduledBy}</div>
                  <div class="sig-caption">Damayan Releasing Officer / Coordinator<br>Islamic Studies, Call and Guidance</div>
                </div>
              </div>

              <div class="footer-note">
                Printed on ${slipData.printDate} • Generated via ISCAG Management Information System (MIS)<br>
                For inquiries, please visit or contact the Damayan Department directly.
              </div>
            </div>

            <script>
              window.onload = function() {
                setTimeout(function() {
                  window.print();
                }, 350);
              };
            <\/script>
          </body>
          </html>
        `;

        const printWin = window.open('', '_blank', 'width=800,height=900');
        if (printWin) {
            printWin.document.open();
            printWin.document.write(printHtml);
            printWin.document.close();
        } else {
            window.print();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const purposeParam = urlParams.get('purpose') || urlParams.get('action');
        if (purposeParam === 'copy') {
            selectCertFunction('copy');
        } else if (purposeParam === 'register' || purposeParam === 'request') {
            selectCertFunction('register');
        }
        const regParam = (urlParams.get('reg_no') || urlParams.get('reg') || '').trim();
        if (regParam) {
            const regInput = document.getElementById('reg-cert-no');
            if (regInput) {
                regInput.value = regParam;
                checkRegCertNumber(regParam);
            }
            const copyCertInput = document.getElementById('existing-cert-no');
            if (copyCertInput && !copyCertInput.value) {
                copyCertInput.value = regParam;
                checkExistingCertNumber(regParam);
            }
        }
    });
</script>

</body>
</html>
