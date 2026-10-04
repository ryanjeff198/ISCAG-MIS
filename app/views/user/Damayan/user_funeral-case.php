<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Self-sufficient data loading if not already provided by controller
require_once BASE_PATH . '/app/models/FuneralCase.php';
require_once BASE_PATH . '/app/models/User.php';

$fcModel = new FuneralCase();
if (!isset($cases)) {
    $cases = $fcModel->getByTenantId($_SESSION['user_id'] ?? '');
}

$caseNum = $_GET['case'] ?? '';
if (!isset($caseDetail)) {
    $caseDetail = null;
    $documents  = [];
    $psaRequest = null;
    $logs       = [];

    if ($caseNum) {
        $found = $fcModel->findByCaseNumber($caseNum);
        if ($found && $found['tenant_id'] == $_SESSION['user_id']) {
            $caseDetail = $found;
        }
    } elseif (!empty($cases)) {
        $caseDetail = $cases[0];
    }

    if ($caseDetail) {
        $documents  = $fcModel->getDocuments($caseDetail['id']);
        $psaRequest = $fcModel->getPsaRequest($caseDetail['id']);
        $logs       = $fcModel->getLogs($caseDetail['id']);
    }
}

$c = $caseDetail ?? null;
$deceasedName = $c ? trim($c['deceased_first_name'] . ' ' . ($c['deceased_middle_name'] ? $c['deceased_middle_name'] . ' ' : '') . $c['deceased_last_name']) : 'N/A';

// User info for prefilling request form
$userFullName = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''));
if (empty($userFullName)) {
    $userFullName = $_SESSION['name'] ?? '';
}
$userEmail = $_SESSION['email'] ?? '';
$userPhone = $_SESSION['phone'] ?? '';

// Determine active initial tab
// If no cases exist, default to 'request'
$defaultTab = $c ? 'tracking' : 'request';
$initialTab = $_GET['tab'] ?? $defaultTab;
if (!$c && $initialTab !== 'request') {
    $initialTab = 'request';
}

// Status timeline for civil registration workflow
$timelineSteps = [
  ['key'=>'Submitted',          'label'=>'Death Reported',              'icon'=>'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z'],
  ['key'=>'Under Review',       'label'=>'Under Review',               'icon'=>'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z'],
  ['key'=>'Information Verified','label'=>'Information Verified',       'icon'=>'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'],
  ['key'=>'Ready for Registration','label'=>'Ready for Registration',   'icon'=>'M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z'],
  ['key'=>'Submitted to LCRO',  'label'=>'Submitted to Civil Registry','icon'=>'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'],
  ['key'=>'Registered',         'label'=>'Death Registered',           'icon'=>'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z'],
  ['key'=>'Completed',          'label'=>'Case Completed',             'icon'=>'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z'],
];

$currentStatus = $c['status'] ?? '';
$statusOrder = array_column($timelineSteps, 'key');
$currentIdx = array_search($currentStatus, $statusOrder);
if ($currentIdx === false) $currentIdx = 0;
if (in_array($currentStatus, ['Requirements Incomplete','Returned for Correction'])) $currentIdx = 2;
if ($currentStatus === 'Rejected') $currentIdx = -1;

$psaTimelineSteps = [
  'Request Created', 'Request Validated', 'Request Submitted', 'Processing', 'Ready', 'Released', 'Received by Requester'
];
$psaStatus = $psaRequest['request_status'] ?? '';
$psaIdx = array_search($psaStatus, $psaTimelineSteps);
if ($psaIdx === false) $psaIdx = -1;

function statusColor($status) {
  $map = [
    'Submitted'=>'#2563eb','Under Review'=>'#f59e0b','Information Verified'=>'#059669',
    'Requirements Incomplete'=>'#dc2626','Ready for Registration'=>'#7c3aed',
    'Submitted to LCRO'=>'#2563eb','Registered'=>'#059669','Completed'=>'#059669',
    'Returned for Correction'=>'#dc2626','Rejected'=>'#dc2626'
  ];
  return $map[$status] ?? '#6b7280';
}
function statusBg($status) {
  $map = [
    'Submitted'=>'#eff6ff','Under Review'=>'#fffbeb','Information Verified'=>'#ecfdf5',
    'Requirements Incomplete'=>'#fef2f2','Ready for Registration'=>'#f5f3ff',
    'Submitted to LCRO'=>'#eff6ff','Registered'=>'#ecfdf5','Completed'=>'#ecfdf5',
    'Returned for Correction'=>'#fef2f2','Rejected'=>'#fef2f2'
  ];
  return $map[$status] ?? '#f9fafb';
}
function docStatusColor($s) {
  $map = ['Not Uploaded'=>'#9ca3af','Uploaded'=>'#2563eb','Under Review'=>'#f59e0b','Verified'=>'#059669','Rejected'=>'#dc2626','Resubmission Required'=>'#dc2626'];
  return $map[$s] ?? '#9ca3af';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Services &amp; Certificate Tracking</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    :root {
      --primary:#1b5e20;--primary-dark:#0f4c18;--primary-light:#e8f5e9;
      --gold:#c9a84c;--text-main:#1f2937;--text-muted:#6b7280;
      --border:#e5e7eb;--danger:#dc2626;--bg:#f4f6f8;
      --success:#059669;--info:#2563eb;--radius:12px;
      --accent:#c9a84c;
    }
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text-main);margin:0;}
    .app-wrapper{display:flex;min-height:100vh;}
    .main-content{flex:1;overflow-y:auto;background:var(--bg);display:flex;flex-direction:column;}
    .page-wrapper{max-width:1040px;margin:0 auto;padding:28px 24px 60px;width:100%;}
    
    .breadcrumb{display:flex;align-items:center;gap:6px;font-size:0.82rem;margin-bottom:18px;color:var(--text-muted);}
    .breadcrumb a{color:var(--primary);text-decoration:none;font-weight:600;}
    .breadcrumb a:hover{text-decoration:underline;}
    .breadcrumb .sep{color:#ccc;}

    /* Top Bar */
    .top-bar{background:white;border-bottom:1px solid var(--border);padding:16px 28px;display:flex;align-items:center;justify-content:space-between;}
    .top-bar-title{font-family:'Lora',serif;font-size:1.25rem;font-weight:700;color:var(--primary-dark);}
    .top-bar-subtitle{font-size:0.8rem;color:var(--text-muted);margin-top:2px;}

    /* Cards */
    .card{background:white;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.03);}
    .card-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
    .card-header h3{font-family:'Lora',serif;font-size:1rem;font-weight:700;color:var(--primary-dark);margin:0;display:flex;align-items:center;gap:8px;}
    .card-body{padding:24px;}

    /* Case Header Banner */
    .case-header{background:white;border-radius:var(--radius);border:1px solid var(--border);padding:22px 26px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;box-shadow:0 2px 8px rgba(0,0,0,0.02);}
    .case-header .case-num{font-family:'Lora',serif;font-size:1.35rem;font-weight:800;color:var(--primary-dark);}
    .case-header .case-deceased{font-size:0.88rem;color:var(--text-muted);margin-top:3px;}

    /* Status badge */
    .status-badge{display:inline-block;padding:5px 14px;border-radius:20px;font-size:0.75rem;font-weight:700;letter-spacing:0.02em;}

    /* Timeline */
    .timeline{position:relative;padding-left:36px;}
    .timeline::before{content:'';position:absolute;left:13px;top:8px;bottom:8px;width:2px;background:var(--border);}
    .tl-item{position:relative;padding-bottom:26px;}
    .tl-item:last-child{padding-bottom:0;}
    .tl-dot{position:absolute;left:-36px;top:2px;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid var(--border);background:white;z-index:1;}
    .tl-dot svg{width:14px;height:14px;fill:#9ca3af;}
    .tl-item.completed .tl-dot{border-color:var(--success);background:#ecfdf5;}
    .tl-item.completed .tl-dot svg{fill:var(--success);}
    .tl-item.current .tl-dot{border-color:var(--primary);background:var(--primary-light);box-shadow:0 0 0 4px rgba(27,94,32,0.18);}
    .tl-item.current .tl-dot svg{fill:var(--primary);}
    .tl-item.error .tl-dot{border-color:var(--danger);background:#fef2f2;}
    .tl-item.error .tl-dot svg{fill:var(--danger);}
    .tl-label{font-weight:700;font-size:0.88rem;color:var(--text-main);}
    .tl-meta{font-size:0.75rem;color:var(--text-muted);margin-top:3px;}

    /* Info Grid */
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .info-item .il{font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:3px;}
    .info-item .iv{font-size:0.88rem;font-weight:600;color:var(--text-main);}

    /* Doc list */
    .doc-row{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;background:#fafbfc;transition:all 0.2s;}
    .doc-row:hover{border-color:var(--primary-light);}
    .doc-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:0.7rem;font-weight:700;}

    /* Navigation & Tabs */
    .tab-bar{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;border-bottom:1px solid var(--border);padding-bottom:12px;}
    .tab-btn{
      padding:10px 18px;border-radius:8px;font-size:0.82rem;font-weight:700;cursor:pointer;
      border:1.5px solid var(--border);background:white;color:var(--text-muted);
      transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;text-decoration:none;
    }
    .tab-btn:hover{border-color:var(--primary);color:var(--primary);}
    .tab-btn.active{
      background:linear-gradient(135deg,var(--primary),var(--primary-dark));
      color:white;border-color:var(--primary);
      box-shadow:0 3px 10px rgba(27,94,32,0.25);
    }
    .tab-btn .badge-pill{
      padding:2px 7px;border-radius:10px;font-size:0.68rem;font-weight:800;
    }

    /* Buttons */
    .btn{padding:10px 20px;border-radius:8px;font-size:0.82rem;font-weight:700;cursor:pointer;border:none;font-family:inherit;transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
    .btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;box-shadow:0 4px 12px rgba(27,94,32,0.25);}
    .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(27,94,32,0.35);}
    .btn-outline{background:white;color:var(--text-muted);border:1.5px solid var(--border);}
    .btn-outline:hover{border-color:var(--primary);color:var(--primary);}
    .btn-gold{background:linear-gradient(135deg,#D4AF37,#B8860B);color:#1a1a1a;box-shadow:0 4px 12px rgba(212,175,55,0.3);}

    /* Notices */
    .notice-box{padding:14px 18px;border-radius:10px;font-size:0.85rem;line-height:1.5;display:flex;gap:12px;align-items:flex-start;margin-bottom:18px;}
    .notice-box.info{background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;}
    .notice-box.warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e;}
    .notice-box.error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;}
    .notice-box svg{flex-shrink:0;width:18px;height:18px;margin-top:2px;}

    /* Form Styles for Request Certificate */
    .form-document {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border);
      overflow: hidden;
    }
    .form-doc-header {
      background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
      padding: 32px 32px 24px;
      border-bottom: 3px solid var(--primary);
      text-align: center;
    }
    .form-doc-header-top {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-bottom: 14px;
    }
    .form-doc-header-logo {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      object-fit: cover;
      flex-shrink: 0;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    .arabic-line {
      font-size: 1.1rem;
      color: var(--primary-dark);
      margin-bottom: 4px;
      font-weight: 700;
      font-family: 'Lora', serif;
    }
    .org-name-en {
      font-size: 1.3rem;
      font-family: 'Lora', serif;
      font-weight: 800;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      line-height: 1.2;
    }
    .sec-reg {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-top: 4px;
    }
    .form-doc-title-bar {
      margin-top: 14px;
    }
    .form-doc-title {
      display: inline-block;
      font-family: 'Lora', serif;
      font-size: 1.05rem;
      font-weight: 700;
      color: white;
      background: var(--primary-dark);
      padding: 8px 30px;
      border-radius: 6px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }
    .form-doc-body {
      padding: 32px 36px;
    }
    .form-section {
      margin-bottom: 32px;
      padding-bottom: 28px;
      border-bottom: 1px solid var(--border);
    }
    .form-section:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }
    .doc-section-title {
      font-family: 'Lora', serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .doc-section-title svg {
      width: 18px;
      height: 18px;
      fill: var(--accent);
      flex-shrink: 0;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 18px;
    }
    .col-12 { grid-column: span 12; }
    .col-8  { grid-column: span 8; }
    .col-6  { grid-column: span 6; }
    .col-4  { grid-column: span 4; }
    .col-3  { grid-column: span 3; }
    .form-group {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 5px;
    }
    .form-label {
      font-size: 0.84rem;
      font-weight: 600;
      color: #374151;
      width: 100%;
    }
    .form-label .req { color: var(--danger); margin-left: 2px; }
    .form-label .hint { font-size: 0.72rem; color: #6b7280; font-weight: 400; margin-left: 4px; }
    .form-control {
      width: 100%;
      height: 42px;
      padding: 9px 13px;
      font-size: 0.9rem;
      font-family: 'Inter', sans-serif;
      color: var(--text-main);
      background-color: #ffffff;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      transition: all 0.2s ease-in-out;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.15);
    }
    .form-control[readonly] { background-color: #f9fafb; color: #6b7280; cursor: default; }
    select.form-control {
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 14px center;
      background-repeat: no-repeat;
      background-size: 16px 16px;
      padding-right: 36px;
    }
    .form-submit-row {
      display: flex;
      gap: 16px;
      justify-content: space-between;
      align-items: center;
      padding: 20px 36px;
      border-top: 1px solid var(--border);
      background: #f8faf9;
    }
    .btn-submit {
      padding: 11px 32px;
      border-radius: 8px;
      border: none;
      background: var(--primary);
      color: white;
      font-size: 0.92rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      box-shadow: 0 4px 14px rgba(27, 94, 32, 0.25);
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .btn-submit:hover {
      background: var(--primary-dark);
      box-shadow: 0 6px 18px rgba(15, 76, 24, 0.35);
    }
    .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Modals & Animations */
    .modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px;}
    .modal-card{background:white;border-radius:12px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 8px 40px rgba(0,0,0,0.2);}
    .modal-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
    .modal-header h3{font-family:'Lora',serif;font-size:0.95rem;font-weight:700;color:var(--primary-dark);margin:0;}
    .modal-body{padding:24px;}
    .modal-close{background:none;border:none;font-size:1.4rem;color:var(--text-muted);cursor:pointer;}

    .log-entry{padding:10px 0;border-bottom:1px solid var(--border);font-size:0.82rem;}
    .log-entry:last-child{border-bottom:none;}
    .log-action{font-weight:700;color:var(--text-main);}
    .log-time{font-size:0.72rem;color:var(--text-muted);}

    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin { animation: spin 1s linear infinite; }

    @media(max-width:768px){
      .info-grid, .form-grid { grid-template-columns: 1fr; }
      .case-header { flex-direction:column; align-items:flex-start; }
      .form-doc-body { padding: 20px; }
      .form-submit-row { flex-direction: column; text-align: center; }
      .btn-submit { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = ($initialTab === 'request') ? 'burial_service' : 'certificate_tracking';
      include BASE_PATH . '/app/views/user/sidebar.php'; 
    ?>
    <div class="main-content">
      
      <!-- TOP BAR -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Burial Services &amp; Death Certificate Portal</div>
          <div class="top-bar-subtitle">Request Certificate, Verification, Civil Registry &amp; Release Tracking</div>
        </div>
        <div id="top-date" style="font-size:0.82rem;color:var(--text-muted);font-weight:500;"></div>
      </div>

      <div class="page-wrapper">
        <div class="breadcrumb">
          <a href="<?= url('/user/dashboard') ?>">Dashboard</a><span class="sep">›</span>
          <a href="<?= url('/user/services/burial-dashboard') ?>">Damayan Services</a><span class="sep">›</span>
          <span>Death Certificate &amp; Case Tracking</span>
        </div>

        <!-- ISLAMIC SERVICE NOTICE -->
        <div style="
            background: linear-gradient(to right, rgba(255, 152, 0, 0.1), rgba(255, 152, 0, 0.05)); 
            border-left: 5px solid #ff9800; 
            padding: 14px 18px; 
            margin-bottom: 22px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            gap: 14px;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.06);
        ">
            <div style="background:#ff9800; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div style="flex: 1;">
                <div style="font-size: 0.72rem; font-weight: 800; color: #e65100; text-transform: uppercase; letter-spacing: 0.08em;">Important Service Notice</div>
                <p style="margin: 0; color: #5d4037; font-size: 0.88rem; font-weight: 500;">
                    This burial service is <strong style="color: #bf360c; font-weight: 800;">Exclusive Only for Islam</strong>. Please ensure the deceased is a practicing Muslim before proceeding.
                </p>
            </div>
        </div>

        <!-- CASE HEADER (IF USER HAS AT LEAST ONE CASE) -->
        <?php if ($c): ?>
        <div class="case-header">
          <div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
              <span style="font-size:0.72rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;">ACTIVE CERTIFICATE CASE</span>
              <?php if (count($cases) > 1): ?>
                <select onchange="window.location.href='<?= url('/user/services/funeral/tracking?case=') ?>'+this.value+'&tab=<?= $initialTab ?>'" style="padding:2px 8px;font-size:0.75rem;border-radius:6px;border:1px solid var(--border);color:var(--text-main);font-weight:600;">
                  <?php foreach ($cases as $oc): ?>
                    <option value="<?= htmlspecialchars($oc['case_number']) ?>" <?= $oc['case_number'] === $c['case_number'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($oc['case_number']) ?> — <?= htmlspecialchars($oc['deceased_first_name'] . ' ' . $oc['deceased_last_name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              <?php endif; ?>
            </div>
            <div class="case-num" style="margin-top:2px;"><?= htmlspecialchars($c['case_number']) ?></div>
            <div class="case-deceased">Deceased: <strong><?= htmlspecialchars($deceasedName) ?></strong> (Submitted <?= $c['submitted_at'] ? date('M d, Y', strtotime($c['submitted_at'])) : '—' ?>)</div>
          </div>
          <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <?php 
              $userCertSt = $c['certificate_status'] ?? 'Requested';
              $badgeBg = match($userCertSt) {
                'Requested' => '#eff6ff',
                'Under Review' => '#fffbeb',
                'Processing' => '#eef2ff',
                'Ready for Release' => '#f5f3ff',
                'Scheduled for Release' => '#fff7ed',
                'Released' => '#ecfdf5',
                default => '#f9fafb'
              };
              $badgeClr = match($userCertSt) {
                'Requested' => '#1d4ed8',
                'Under Review' => '#b45309',
                'Processing' => '#4338ca',
                'Ready for Release' => '#6d28d9',
                'Scheduled for Release' => '#c2410c',
                'Released' => '#047857',
                default => '#374151'
              };
            ?>
            <span class="status-badge" style="background:<?= $badgeBg ?>;color:<?= $badgeClr ?>;font-weight:800;font-size:0.8rem;">
              ● <?= htmlspecialchars($userCertSt) ?>
            </span>
            <button class="btn btn-outline" onclick="showTab('request')" style="padding:6px 14px;font-size:0.75rem;font-weight:700;">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
              Request New Certificate
            </button>
          </div>
        </div>
        <?php endif; ?>

        <!-- UNIFIED TABS -->
        <div class="tab-bar">
          <?php if ($c): ?>
            <button class="tab-btn <?= $initialTab === 'tracking' ? 'active' : '' ?>" onclick="showTab('tracking')" data-tab="tracking">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
              Certificate Tracking
              <?php if (($c['certificate_status'] ?? '') === 'Scheduled for Release'): ?>
                <span class="badge-pill" style="background:#ea580c;color:white;">Scheduled</span>
              <?php elseif (($c['certificate_status'] ?? '') === 'Ready for Release'): ?>
                <span class="badge-pill" style="background:#7c3aed;color:white;">Ready</span>
              <?php endif; ?>
            </button>
          <?php endif; ?>

          <button class="tab-btn <?= $initialTab === 'request' ? 'active' : '' ?>" onclick="showTab('request')" data-tab="request">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
            Request Certificate Form
          </button>

          <?php if ($c): ?>
            <button class="tab-btn <?= $initialTab === 'info' ? 'active' : '' ?>" onclick="showTab('info')" data-tab="info">
              Case Details
            </button>
            <button class="tab-btn <?= $initialTab === 'docs' ? 'active' : '' ?>" onclick="showTab('docs')" data-tab="docs">
              Documents (<?= count($documents) ?>)
            </button>
            <button class="tab-btn <?= $initialTab === 'reg' ? 'active' : '' ?>" onclick="showTab('reg')" data-tab="reg">
              Civil Registration
            </button>
            <button class="tab-btn <?= $initialTab === 'psa' ? 'active' : '' ?>" onclick="showTab('psa')" data-tab="psa">
              PSA Certificate
            </button>
            <button class="tab-btn <?= $initialTab === 'log' ? 'active' : '' ?>" onclick="showTab('log')" data-tab="log">
              Activity Log
            </button>
          <?php endif; ?>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 1: CERTIFICATE TRACKING & TIMELINE                           -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <?php if ($c): ?>
        <div class="tab-content" id="tab-tracking" style="<?= $initialTab === 'tracking' ? 'display:block;' : 'display:none;' ?>">
          
          <!-- Scheduled Release Notice Banner -->
          <?php if (($c['certificate_status'] ?? '') === 'Scheduled for Release'): ?>
            <div class="notice-box info" style="background:#eff6ff;border:1.5px solid #93c5fd;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
              <div style="width:36px;height:36px;border-radius:50%;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:1rem;font-weight:800;color:#1e3a8a;margin-bottom:6px;">Your certificate is ready and scheduled for release.</div>
                <div style="font-size:0.88rem;color:#1e40af;line-height:1.6;">
                  <div><strong>Release Date:</strong> <?= !empty($c['scheduled_release_date']) ? date('F j, Y', strtotime($c['scheduled_release_date'])) : 'To be confirmed' ?></div>
                  <div><strong>Release Time:</strong> <?= !empty($c['scheduled_release_time']) ? date('g:i A', strtotime($c['scheduled_release_time'])) : 'To be confirmed' ?></div>
                  <div><strong>Location:</strong> <?= htmlspecialchars($c['release_location'] ?: 'Masjid Office / Designated Office') ?></div>
                  <?php if (!empty($c['assigned_staff_name'])): ?>
                    <div><strong>Assigned Staff:</strong> <?= htmlspecialchars($c['assigned_staff_name']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php elseif (($c['certificate_status'] ?? '') === 'Released'): ?>
            <div class="notice-box" style="background:#ecfdf5;border:1.5px solid #6ee7b7;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
              <div style="width:36px;height:36px;border-radius:50%;background:#059669;color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:1rem;font-weight:800;color:#065f46;margin-bottom:4px;">Certificate Released</div>
                <div style="font-size:0.88rem;color:#047857;line-height:1.6;">
                  Your official certificate was released on <strong><?= !empty($c['released_at']) ? date('F j, Y \a\t g:i A', strtotime($c['released_at'])) : 'Completed' ?></strong>.
                  <?= !empty($c['recipient_name']) ? '<br>Received by: <strong>' . htmlspecialchars($c['recipient_name']) . '</strong>' : '' ?>
                  <?= !empty($c['release_method']) ? ' via <strong>' . htmlspecialchars($c['release_method']) . '</strong>.' : '.' ?>
                </div>
              </div>
            </div>
          <?php elseif (($c['certificate_status'] ?? '') === 'Ready for Release'): ?>
            <div class="notice-box" style="background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:18px 20px;margin-bottom:20px;">
              <div style="width:36px;height:36px;border-radius:50%;background:#7c3aed;color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              </div>
              <div style="flex:1;">
                <div style="font-size:1rem;font-weight:800;color:#5b21b6;margin-bottom:4px;">Certificate Completed &amp; Ready for Release</div>
                <div style="font-size:0.88rem;color:#6d28d9;line-height:1.5;">
                  Your certificate has been prepared and verified. The administration will assign your release schedule shortly.
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($c['reschedule_reason'])): ?>
            <div class="notice-box warn" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:0.83rem;color:#92400e;">
              <strong>Schedule Update Notice:</strong> <?= htmlspecialchars($c['reschedule_reason']) ?>
            </div>
          <?php endif; ?>

          <!-- Tracking Card -->
          <div class="card" style="border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px;">
            <div class="card-header" style="background:linear-gradient(135deg,#f8fafc,#ffffff);padding:20px 24px;">
              <div>
                <div style="font-size:0.75rem;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;">Official Certificate Tracking</div>
                <h3 style="font-family:'Lora',serif;font-size:1.35rem;font-weight:700;color:var(--primary-dark);margin:2px 0 0;">Death Certificate Request</h3>
                <div style="font-size:0.85rem;color:var(--text-muted);margin-top:2px;">
                  Request ID: <strong style="color:var(--text-main);font-family:monospace;font-size:0.95rem;"><?= htmlspecialchars($c['case_number']) ?></strong>
                </div>
              </div>
              <div style="text-align:right;">
                <div style="font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Current Status</div>
                <span class="status-badge" style="background:<?= $badgeBg ?>;color:<?= $badgeClr ?>;font-size:0.8rem;padding:6px 16px;border-radius:20px;font-weight:800;">
                  ● <?= htmlspecialchars($userCertSt) ?>
                </span>
              </div>
            </div>

            <div class="card-body" style="padding:24px;">
              <div class="info-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:20px;">
                <div class="info-item">
                  <div class="il">Scheduled Release Date</div>
                  <div class="iv" style="font-size:1rem;color:<?= !empty($c['scheduled_release_date']) ? 'var(--primary-dark)' : 'var(--text-muted)' ?>;">
                    <?= !empty($c['scheduled_release_date']) ? date('F j, Y', strtotime($c['scheduled_release_date'])) : 'Pending scheduling' ?>
                  </div>
                </div>
                <div class="info-item">
                  <div class="il">Scheduled Release Time</div>
                  <div class="iv" style="font-size:1rem;color:<?= !empty($c['scheduled_release_time']) ? 'var(--primary-dark)' : 'var(--text-muted)' ?>;">
                    <?= !empty($c['scheduled_release_time']) ? date('g:i A', strtotime($c['scheduled_release_time'])) : 'Pending scheduling' ?>
                  </div>
                </div>
                <div class="info-item">
                  <div class="il">Release Location</div>
                  <div class="iv" style="font-size:0.95rem;color:var(--text-main);">
                    <?= htmlspecialchars($c['release_location'] ?: 'Masjid Office / Designated Office') ?>
                  </div>
                </div>
                <div class="info-item">
                  <div class="il">Release Method</div>
                  <div class="iv" style="font-size:0.95rem;color:var(--text-main);">
                    <?= htmlspecialchars($c['release_method'] ?: 'Office Pick-up') ?>
                  </div>
                </div>
              </div>

              <?php if (!empty($c['release_notes'])): ?>
                <div style="background:#f8fafc;border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:18px;">
                  <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:2px;">Important Release Instructions</div>
                  <div style="font-size:0.85rem;color:var(--text-main);line-height:1.5;"><?= nl2br(htmlspecialchars($c['release_notes'])) ?></div>
                </div>
              <?php endif; ?>

              <div style="font-size:0.75rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;border-top:1px solid var(--border);padding-top:14px;">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Release schedules are confirmed by the Damayan administration. For urgent inquiries, please contact the Masjid office directly.
              </div>
            </div>
          </div>

          <!-- Vertical Timeline -->
          <div class="card" style="border-radius:16px;border:1px solid var(--border);overflow:hidden;">
            <div class="card-header" style="background:#ffffff;padding:20px 24px;">
              <h3 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:var(--primary-dark);margin:0;display:flex;align-items:center;gap:8px;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Certificate Tracking Timeline
              </h3>
            </div>
            <div class="card-body" style="padding:28px 24px;">
              <?php
                $trackingSteps = [
                  ['id'=>'req',   'status'=>'Requested',            'title'=>'Request Submitted',     'icon'=>'✓', 'desc'=>'Your death certificate request was submitted to the Damayan Department.'],
                  ['id'=>'rev',   'status'=>'Under Review',         'title'=>'Request Reviewed',      'icon'=>'✓', 'desc'=>'The application documents and details have been reviewed by administration.'],
                  ['id'=>'proc',  'status'=>'Processing',           'title'=>'Certificate Processing','icon'=>'✓', 'desc'=>'Official records are being processed and formatted for registration.'],
                  ['id'=>'ready', 'status'=>'Ready for Release',    'title'=>'Certificate Ready',     'icon'=>'✓', 'desc'=>'Certificate has been completed and verified for release.'],
                  ['id'=>'sched', 'status'=>'Scheduled for Release','title'=>'Scheduled for Release', 'icon'=>'●', 'desc'=>'A designated release date and pickup schedule has been set.'],
                  ['id'=>'rel',   'status'=>'Released',             'title'=>'Certificate Released',  'icon'=>'○', 'desc'=>'The physical certificate was successfully handed over to the recipient.']
                ];

                $currentStatusKey = $c['certificate_status'] ?? 'Requested';
                $stageWeights = [
                  'Requested' => 1,
                  'Under Review' => 2,
                  'Processing' => 3,
                  'Ready for Release' => 4,
                  'Scheduled for Release' => 5,
                  'Released' => 6
                ];
                $activeWeight = $stageWeights[$currentStatusKey] ?? 1;

                $stepDates = [];
                $stepDates['Requested'] = !empty($c['submitted_at']) ? date('F j, Y \a\t g:i A', strtotime($c['submitted_at'])) : null;
                $stepDates['Processing'] = !empty($c['processing_started_at']) ? date('F j, Y \a\t g:i A', strtotime($c['processing_started_at'])) : null;
                $stepDates['Ready for Release'] = !empty($c['ready_at']) ? date('F j, Y \a\t g:i A', strtotime($c['ready_at'])) : null;
                if (!empty($c['scheduled_release_date'])) {
                  $stepDates['Scheduled for Release'] = date('F j, Y', strtotime($c['scheduled_release_date'])) . (!empty($c['scheduled_release_time']) ? ' at ' . date('g:i A', strtotime($c['scheduled_release_time'])) : '');
                }
                $stepDates['Released'] = !empty($c['released_at']) ? date('F j, Y \a\t g:i A', strtotime($c['released_at'])) : null;

                if (!empty($logs)) {
                  foreach ($logs as $l) {
                    $lAct = strtoupper($l['action'] ?? '');
                    if (str_contains($lAct, 'REVIEW') && empty($stepDates['Under Review'])) {
                      $stepDates['Under Review'] = date('F j, Y \a\t g:i A', strtotime($l['created_at']));
                    }
                    if (str_contains($lAct, 'PROCESSING') && empty($stepDates['Processing'])) {
                      $stepDates['Processing'] = date('F j, Y \a\t g:i A', strtotime($l['created_at']));
                    }
                    if (str_contains($lAct, 'READY') && empty($stepDates['Ready for Release'])) {
                      $stepDates['Ready for Release'] = date('F j, Y \a\t g:i A', strtotime($l['created_at']));
                    }
                  }
                }
              ?>

              <div class="timeline">
                <?php foreach ($trackingSteps as $idx => $ts):
                  $stepWeight = $stageWeights[$ts['status']];
                  $isCompleted = ($stepWeight < $activeWeight) || ($currentStatusKey === 'Released');
                  $isCurrent = ($stepWeight === $activeWeight) && ($currentStatusKey !== 'Released');
                  $cls = $isCompleted ? 'completed' : ($isCurrent ? 'current' : '');
                  $stepDate = $stepDates[$ts['status']] ?? null;
                ?>
                  <div class="tl-item <?= $cls ?>" style="padding-bottom:<?= ($idx === count($trackingSteps)-1) ? '0' : '28px' ?>;">
                    <div class="tl-dot">
                      <?php if ($isCompleted): ?>
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:var(--success);"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                      <?php elseif ($isCurrent): ?>
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);"></div>
                      <?php else: ?>
                        <div style="width:6px;height:6px;border-radius:50%;background:#d1d5db;"></div>
                      <?php endif; ?>
                    </div>
                    
                    <div class="tl-label" style="font-size:0.92rem;color:<?= $isCurrent ? 'var(--primary-dark)' : ($isCompleted ? 'var(--text-main)' : 'var(--text-muted)') ?>;display:flex;align-items:center;gap:8px;">
                      <span><?= htmlspecialchars($ts['title']) ?></span>
                      <?php if ($isCurrent): ?>
                        <span style="font-size:0.68rem;padding:2px 8px;border-radius:10px;background:var(--primary-light);color:var(--primary-dark);font-weight:800;letter-spacing:0.04em;">ACTIVE STAGE</span>
                      <?php endif; ?>
                    </div>

                    <div style="font-size:0.8rem;color:var(--text-muted);margin-top:3px;">
                      <?= htmlspecialchars($ts['desc']) ?>
                    </div>

                    <div class="tl-meta" style="margin-top:4px;font-size:0.75rem;font-weight:600;color:<?= $stepDate ? 'var(--text-main)' : 'var(--text-muted)' ?>;">
                      <?php if ($stepDate): ?>
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;vertical-align:middle;margin-right:2px;"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        <?= htmlspecialchars($stepDate) ?>
                      <?php elseif ($isCurrent): ?>
                        <span style="color:var(--primary);">● In Progress</span>
                      <?php else: ?>
                        Waiting for stage completion
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 2: REQUEST CERTIFICATE FORM (THE UNIFIED FORM)                 -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="tab-content" id="tab-request" style="<?= $initialTab === 'request' ? 'display:block;' : 'display:none;' ?>">
          <div class="form-document">
            <div class="form-doc-header">
              <div class="form-doc-header-top">
                <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo" />
                <div>
                  <div class="arabic-line">بسم الله الرحمن الرحيم</div>
                  <div class="org-name-en">Islamic Studies, Call and Guidance</div>
                  <div class="sec-reg">SEC Reg. No. 123456789 — Dasmariñas City, Cavite</div>
                </div>
              </div>
              <div class="form-doc-title-bar">
                <div class="form-doc-title">Death Certificate Request Form</div>
              </div>
            </div>

            <div class="form-doc-body">
              <!-- 1. REGISTRY INFORMATION -->
              <div class="form-section">
                <div class="doc-section-title">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                  1. Registry Information
                </div>
                <div class="form-grid">
                  <div class="form-group col-4">
                    <label class="form-label">Province</label>
                    <input type="text" id="reg-province" class="form-control" value="Cavite" placeholder="Enter Province" />
                  </div>
                  <div class="form-group col-4">
                    <label class="form-label">City / Municipality</label>
                    <input type="text" id="reg-city" class="form-control" value="City of Dasmariñas" placeholder="Enter City/Municipality" />
                  </div>
                  <div class="form-group col-4">
                    <label class="form-label">Registry Case No. <span class="hint">(Auto-Assigned)</span></label>
                    <input type="text" class="form-control" value="DC-<?= date('Y') ?>-XXXXX" readonly style="font-family:monospace; font-weight:700; text-align:center; color:var(--primary);" />
                  </div>
                </div>
              </div>

              <!-- 2. DECEASED'S PERSONAL INFORMATION -->
              <div class="form-section">
                <div class="doc-section-title">
                  <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                  2. Deceased's Basic &amp; Personal Information
                </div>
                <div class="form-grid">
                  <div class="form-group col-4">
                    <label class="form-label">First Name <span class="req">*</span></label>
                    <input type="text" id="deceased-first-name" class="form-control" placeholder="Deceased's First Name" required />
                  </div>
                  <div class="form-group col-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" id="deceased-middle-name" class="form-control" placeholder="Deceased's Middle Name" />
                  </div>
                  <div class="form-group col-4">
                    <label class="form-label">Last Name <span class="req">*</span></label>
                    <input type="text" id="deceased-last-name" class="form-control" placeholder="Deceased's Last Name" required />
                  </div>

                  <div class="form-group col-6">
                    <label class="form-label">Date of Birth <span class="hint">(Optional if unknown)</span></label>
                    <input type="date" id="deceased-dob" class="form-control" />
                  </div>
                  <div class="form-group col-6">
                    <label class="form-label">Date of Death <span class="req">*</span></label>
                    <input type="date" id="deceased-dod" class="form-control" value="<?= date('Y-m-d') ?>" required />
                  </div>
                  
                  <div class="form-group col-12">
                    <label class="form-label">Age at the Time of Death</label>
                    <div class="form-grid" style="row-gap: 8px;">
                      <div class="form-group col-4">
                        <label class="form-label hint">Completed Years</label>
                        <input type="number" id="deceased-age-years" class="form-control" placeholder="Years" min="0" max="150" />
                      </div>
                      <div class="form-group col-4">
                        <label class="form-label hint">Months</label>
                        <input type="number" id="deceased-age-months" class="form-control" placeholder="Months" min="0" max="11" />
                      </div>
                      <div class="form-group col-4">
                        <label class="form-label hint">Days</label>
                        <input type="number" id="deceased-age-days" class="form-control" placeholder="Days" min="0" max="31" />
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-3">
                    <label class="form-label">Sex <span class="req">*</span></label>
                    <select id="deceased-sex" class="form-control" required>
                      <option value="">Select Sex</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                  <div class="form-group col-3">
                    <label class="form-label">Civil Status</label>
                    <select id="deceased-civil-status" class="form-control">
                      <option value="">Select Status</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widow/Widower">Widow / Widower</option>
                      <option value="Annulled">Annulled</option>
                      <option value="Divorced">Divorced</option>
                    </select>
                  </div>
                  <div class="form-group col-3">
                    <label class="form-label">Religion</label>
                    <select id="deceased-religion" class="form-control">
                      <option value="Islam" selected>Islam</option>
                      <option value="Christian">Christian</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div class="form-group col-3">
                    <label class="form-label">Citizenship</label>
                    <input type="text" id="deceased-citizenship" class="form-control" value="FILIPINO" />
                  </div>

                  <div class="form-group col-12">
                    <label class="form-label">Place of Death <span class="req">*</span> <span class="hint">(Hospital / Residence Address)</span></label>
                    <input type="text" id="deceased-pod" class="form-control" placeholder="e.g. Dasmariñas City Medical Center / Residence at Salitran I, Dasmariñas City" required />
                  </div>
                  <div class="form-group col-12">
                    <label class="form-label">Residence <span class="req">*</span> <span class="hint">(Complete home address of the deceased)</span></label>
                    <input type="text" id="deceased-residence" class="form-control" placeholder="Complete street, subdivision, barangay, city/municipality" required />
                  </div>
                  <div class="form-group col-12">
                    <label class="form-label">Occupation</label>
                    <input type="text" id="deceased-occupation" class="form-control" placeholder="Occupation / Profession (if applicable)" />
                  </div>
                  
                  <div class="form-group col-6">
                    <label class="form-label">Father's Full Name <span class="hint">(First, Middle, Last)</span></label>
                    <input type="text" id="deceased-father" class="form-control" placeholder="Father's full name" />
                  </div>
                  <div class="form-group col-6">
                    <label class="form-label">Mother's Maiden Name <span class="hint">(First, Middle, Last)</span></label>
                    <input type="text" id="deceased-mother" class="form-control" placeholder="Mother's complete maiden name" />
                  </div>
                </div>
              </div>

              <!-- 3. REQUESTER / INFORMANT INFORMATION -->
              <div class="form-section">
                <div class="doc-section-title">
                  <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                  3. Requester / Informant Details
                </div>
                <div class="form-grid">
                  <div class="form-group col-6">
                    <label class="form-label">Requesting Party / Informant Full Name <span class="req">*</span></label>
                    <input type="text" id="informant-name" class="form-control" value="<?= htmlspecialchars($userFullName) ?>" placeholder="Full name of person requesting certificate" required />
                  </div>
                  <div class="form-group col-6">
                    <label class="form-label">Relationship to Deceased <span class="req">*</span></label>
                    <select id="informant-relationship" class="form-control" required>
                      <option value="">Select Relationship</option>
                      <option value="Spouse">Spouse (Husband / Wife)</option>
                      <option value="Son">Son</option>
                      <option value="Daughter">Daughter</option>
                      <option value="Father">Father</option>
                      <option value="Mother">Mother</option>
                      <option value="Brother">Brother</option>
                      <option value="Sister">Sister</option>
                      <option value="Relative">Other Relative / Next of Kin</option>
                      <option value="Authorized Representative">Authorized Representative</option>
                    </select>
                  </div>
                  <div class="form-group col-6">
                    <label class="form-label">Contact / Mobile Number <span class="req">*</span></label>
                    <input type="tel" id="informant-contact" class="form-control" value="<?= htmlspecialchars($userPhone) ?>" placeholder="e.g. 0912 345 6789" required />
                  </div>
                  <div class="form-group col-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="informant-email" class="form-control" value="<?= htmlspecialchars($userEmail) ?>" placeholder="e.g. email@example.com" />
                  </div>
                  <div class="form-group col-12">
                    <label class="form-label">Requester Postal Address</label>
                    <input type="text" id="informant-address" class="form-control" placeholder="Complete address of requester" />
                  </div>
                </div>
              </div>

            </div><!-- /.form-doc-body -->

            <div class="form-submit-row">
              <p style="font-size:0.84rem;color:var(--text-muted);margin:0;">
                All information will be recorded under your account and linked to the tracking system.
              </p>
              <div style="display:flex;gap:12px;align-items:center;">
                <?php if ($c): ?>
                  <button type="button" class="btn btn-outline" onclick="showTab('tracking')">Cancel</button>
                <?php endif; ?>
                <button type="button" id="btn-submit-cert" class="btn-submit">
                  <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                  Request Certificate
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 3: CASE DETAILS                                               -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <?php if ($c): ?>
        <div class="tab-content" id="tab-info" style="<?= $initialTab === 'info' ? 'display:block;' : 'display:none;' ?>">
          <div class="card">
            <div class="card-header"><h3>Deceased Information</h3></div>
            <div class="card-body">
              <div class="info-grid">
                <div class="info-item"><div class="il">Full Name</div><div class="iv"><?= htmlspecialchars($deceasedName) ?></div></div>
                <div class="info-item"><div class="il">Haj Name</div><div class="iv"><?= htmlspecialchars($c['deceased_haj_name'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Sex</div><div class="iv"><?= htmlspecialchars($c['deceased_sex'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Date of Birth</div><div class="iv"><?= $c['deceased_dob'] ? date('M d, Y', strtotime($c['deceased_dob'])) : '—' ?></div></div>
                <div class="info-item"><div class="il">Date of Death</div><div class="iv"><?= $c['deceased_dod'] ? date('M d, Y', strtotime($c['deceased_dod'])) : '—' ?></div></div>
                <div class="info-item"><div class="il">Time of Death</div><div class="iv"><?= $c['deceased_tod'] ?: '—' ?></div></div>
                <div class="info-item"><div class="il">Place of Death</div><div class="iv"><?= htmlspecialchars($c['deceased_place_of_death'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Civil Status</div><div class="iv"><?= htmlspecialchars($c['deceased_civil_status'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Nationality</div><div class="iv"><?= htmlspecialchars($c['deceased_nationality'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Religion</div><div class="iv"><?= htmlspecialchars($c['deceased_religion'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Occupation</div><div class="iv"><?= htmlspecialchars($c['deceased_occupation'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Address</div><div class="iv"><?= htmlspecialchars($c['deceased_address'] ?: '—') ?></div></div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Family / Informant</h3></div>
            <div class="card-body">
              <div class="info-grid">
                <div class="info-item"><div class="il">Informant</div><div class="iv"><?= htmlspecialchars($c['informant_name'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Relationship</div><div class="iv"><?= htmlspecialchars($c['informant_relationship'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Contact</div><div class="iv"><?= htmlspecialchars($c['informant_contact'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Email</div><div class="iv"><?= htmlspecialchars($c['informant_email'] ?: '—') ?></div></div>
                <div class="info-item" style="grid-column:1/-1;"><div class="il">Address</div><div class="iv"><?= htmlspecialchars($c['informant_address'] ?: '—') ?></div></div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3>Islamic Funeral &amp; Burial Information</h3></div>
            <div class="card-body">
              <div class="info-grid">
                <div class="info-item"><div class="il">Burial Rites Person</div><div class="iv"><?= htmlspecialchars($c['burial_rites_person'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Imam / Leader</div><div class="iv"><?= htmlspecialchars($c['imam_name'] ?: '—') ?></div></div>
                <div class="info-item" style="grid-column:1/-1;"><div class="il">Surviving Spouse(s)</div><div class="iv"><?= htmlspecialchars($c['surviving_spouses'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Burial Date</div><div class="iv"><?= $c['burial_date'] ? date('M d, Y', strtotime($c['burial_date'])) : '—' ?></div></div>
                <div class="info-item"><div class="il">Burial Time</div><div class="iv"><?= $c['burial_time'] ?: '—' ?></div></div>
                <div class="info-item"><div class="il">Burial Location</div><div class="iv"><?= htmlspecialchars($c['burial_location'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Cemetery</div><div class="iv"><?= htmlspecialchars($c['cemetery'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Grave Reference</div><div class="iv"><?= htmlspecialchars($c['grave_reference'] ?: '—') ?></div></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 4: DOCUMENTS                                                  -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="tab-content" id="tab-docs" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>Required Documents</h3></div>
            <div class="card-body">
              <?php if (empty($documents)): ?>
                <p style="color:var(--text-muted);font-size:0.88rem;">No documents found for this case.</p>
              <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                  <div class="doc-row">
                    <div>
                      <div style="font-weight:700;font-size:0.85rem;"><?= htmlspecialchars($doc['doc_name']) ?></div>
                      <?php if ($doc['file_name']): ?>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;"><?= htmlspecialchars($doc['file_name']) ?></div>
                      <?php endif; ?>
                      <?php if ($doc['upload_status'] === 'Rejected' && $doc['rejection_reason']): ?>
                        <div style="font-size:0.75rem;color:var(--danger);margin-top:4px;font-weight:600;">Reason: <?= htmlspecialchars($doc['rejection_reason']) ?></div>
                      <?php endif; ?>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <span class="doc-badge" style="background:<?= docStatusColor($doc['upload_status']) ?>15;color:<?= docStatusColor($doc['upload_status']) ?>;"><?= htmlspecialchars($doc['upload_status']) ?></span>
                      <?php if (in_array($doc['upload_status'], ['Not Uploaded','Rejected','Resubmission Required'])): ?>
                        <label class="btn btn-outline" style="padding:6px 12px;font-size:0.72rem;cursor:pointer;margin:0;">
                          Upload
                          <input type="file" class="upload-doc-input" data-doc-id="<?= $doc['id'] ?>" data-case-id="<?= $c['id'] ?>" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;" />
                        </label>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 5: REGISTRATION                                               -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="tab-content" id="tab-reg" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>Death Certificate Registration</h3></div>
            <div class="card-body">
              <div class="notice-box info" style="margin-bottom:20px;">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <div>The Islamic Funeral Services office facilitates and tracks the death registration process. The official registration is handled by the Local Civil Registry Office (LCRO).</div>
              </div>
              <div class="info-grid">
                <div class="info-item"><div class="il">Registration Status</div><div class="iv"><span class="status-badge" style="background:<?= statusBg($currentStatus) ?>;color:<?= statusColor($currentStatus) ?>;"><?= htmlspecialchars($currentStatus) ?></span></div></div>
                <div class="info-item"><div class="il">Informant</div><div class="iv"><?= htmlspecialchars($c['informant_name'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Date Reported</div><div class="iv"><?= $c['reported_at'] ? date('M d, Y', strtotime($c['reported_at'])) : '—' ?></div></div>
                <div class="info-item"><div class="il">Place of Registration</div><div class="iv"><?= htmlspecialchars($c['registration_place'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Registration Reference</div><div class="iv"><?= htmlspecialchars($c['registration_reference'] ?: '—') ?></div></div>
                <div class="info-item"><div class="il">Assigned Admin</div><div class="iv"><?= htmlspecialchars($c['assigned_admin_name'] ?: '—') ?></div></div>
              </div>

              <?php
              $dodDate = $c['deceased_dod'] ? strtotime($c['deceased_dod']) : null;
              $regDeadline = $dodDate ? date('M d, Y', $dodDate + (30 * 86400)) : null;
              ?>
              <?php if ($dodDate): ?>
              <hr style="border:none;border-top:1px solid var(--border);margin:20px 0;">
              <div style="font-family:'Lora',serif;font-size:0.85rem;font-weight:700;color:var(--primary-dark);margin-bottom:12px;">Deadline Tracking</div>
              <div class="info-grid">
                <div class="info-item">
                  <div class="il">Islamic Funeral Status</div>
                  <div class="iv"><?= $c['burial_date'] ? 'Burial Completed — ' . date('M d, Y', strtotime($c['burial_date'])) : 'Pending' ?></div>
                </div>
                <div class="info-item">
                  <div class="il">Civil Registration Reporting Deadline</div>
                  <div class="iv" style="color:<?= ($c['registration_completed_at'] || time() < $dodDate + (30*86400)) ? 'var(--success)' : 'var(--danger)' ?>;">
                    <?= $c['registration_completed_at'] ? 'Registered — ' . date('M d, Y', strtotime($c['registration_completed_at'])) : $regDeadline ?>
                  </div>
                </div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 6: PSA CERTIFICATE                                            -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="tab-content" id="tab-psa" style="display:none;">
          <div class="card">
            <div class="card-header">
              <h3>PSA Death Certificate Request</h3>
              <div style="font-size:0.72rem;color:var(--text-muted);">PSA Request Assistance &amp; Tracking</div>
            </div>
            <div class="card-body">
              <div class="notice-box info" style="margin-bottom:20px;">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <div>The PSA-issued Death Certificate is a civil registry document. This system assists in preparing and tracking your request but does not itself issue the PSA certificate. <a href="https://www.psaserbilis.com.ph" target="_blank" style="font-weight:700;">Visit PSA Serbilis →</a></div>
              </div>

              <?php if (!in_array($currentStatus, ['Registered','Completed'])): ?>
                <div class="notice-box warn">
                  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                  <div>The death must be officially registered before you can request a PSA Death Certificate. Current status: <strong><?= htmlspecialchars($currentStatus) ?></strong></div>
                </div>
              <?php elseif (!$psaRequest): ?>
                <div style="text-align:center;padding:24px 0;">
                  <p style="color:var(--text-muted);font-size:0.88rem;margin:0 0 16px;">The death has been registered. You may now request a certified PSA Death Certificate copy.</p>
                  <button class="btn btn-gold" onclick="openPsaModal()">Request Certified PSA Death Certificate</button>
                </div>
              <?php else: ?>
                <div style="margin-bottom:24px;">
                  <div style="display:flex;gap:4px;flex-wrap:wrap;">
                    <?php foreach ($psaTimelineSteps as $pi => $ps):
                      $psaCurrent = ($pi === $psaIdx);
                      $psaDone = ($pi < $psaIdx);
                    ?>
                    <div style="flex:1;min-width:80px;text-align:center;padding:10px 6px;border-radius:8px;font-size:0.68rem;font-weight:700;
                      background:<?= $psaDone ? '#ecfdf5' : ($psaCurrent ? '#eff6ff' : '#f9fafb') ?>;
                      color:<?= $psaDone ? 'var(--success)' : ($psaCurrent ? 'var(--info)' : 'var(--text-muted)') ?>;
                      border:1px solid <?= $psaCurrent ? '#bfdbfe' : 'var(--border)' ?>;
                    "><?= $ps ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="info-grid">
                  <div class="info-item"><div class="il">Request Status</div><div class="iv"><span class="status-badge" style="background:#eff6ff;color:#2563eb;"><?= htmlspecialchars($psaRequest['request_status']) ?></span></div></div>
                  <div class="info-item"><div class="il">PSA Reference Number</div><div class="iv"><?= htmlspecialchars($psaRequest['psa_ref_number'] ?: '—') ?></div></div>
                  <div class="info-item"><div class="il">Requesting Party</div><div class="iv"><?= htmlspecialchars($psaRequest['requesting_party_name'] ?: '—') ?></div></div>
                  <div class="info-item"><div class="il">Number of Copies</div><div class="iv"><?= $psaRequest['num_copies'] ?></div></div>
                  <div class="info-item"><div class="il">Purpose</div><div class="iv"><?= htmlspecialchars($psaRequest['purpose'] ?: '—') ?></div></div>
                  <div class="info-item"><div class="il">Payment Status</div><div class="iv"><?= htmlspecialchars($psaRequest['payment_status'] ?: '—') ?></div></div>
                  <div class="info-item"><div class="il">Release Method</div><div class="iv"><?= htmlspecialchars($psaRequest['release_method'] ?: '—') ?></div></div>
                  <div class="info-item"><div class="il">Release Date</div><div class="iv"><?= $psaRequest['release_date'] ? date('M d, Y', strtotime($psaRequest['release_date'])) : '—' ?></div></div>
                  <?php if ($psaRequest['remarks']): ?>
                  <div class="info-item" style="grid-column:1/-1;"><div class="il">Remarks</div><div class="iv"><?= htmlspecialchars($psaRequest['remarks']) ?></div></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- TAB 7: ACTIVITY LOG                                               -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="tab-content" id="tab-log" style="display:none;">
          <div class="card">
            <div class="card-header"><h3>Activity Log</h3></div>
            <div class="card-body">
              <?php if (empty($logs)): ?>
                <p style="color:var(--text-muted);font-size:0.88rem;">No activity recorded yet.</p>
              <?php else: ?>
                <?php foreach ($logs as $log): ?>
                  <div class="log-entry">
                    <div class="log-action"><?= htmlspecialchars($log['description'] ?? $log['action']) ?></div>
                    <div class="log-time"><?= date('M d, Y — g:i A', strtotime($log['created_at'])) ?><?= $log['status'] ? ' — ' . htmlspecialchars($log['status']) : '' ?></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div><!-- /.page-wrapper -->
    </div>
  </div>

  <!-- PSA Request Modal -->
  <?php if ($c): ?>
  <div class="modal-backdrop" id="psa-modal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Request Certified PSA Death Certificate</h3>
        <button class="modal-close" onclick="closePsaModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="notice-box info" style="margin-bottom:20px;">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
          <div style="font-size:0.8rem;">The PSA-issued Death Certificate is a civil registry document. This system assists in preparing and tracking your request but does not itself issue the PSA certificate.</div>
        </div>
        <div class="info-grid" style="margin-bottom:20px;">
          <div class="info-item"><div class="il">Deceased Name</div><div class="iv"><?= htmlspecialchars($deceasedName) ?></div></div>
          <div class="info-item"><div class="il">Date of Death</div><div class="iv"><?= $c['deceased_dod'] ? date('M d, Y', strtotime($c['deceased_dod'])) : '—' ?></div></div>
          <div class="info-item" style="grid-column:1/-1;"><div class="il">Place of Death</div><div class="iv"><?= htmlspecialchars($c['deceased_place_of_death'] ?: '—') ?></div></div>
        </div>
        <div style="display:grid;gap:14px;">
          <div class="form-group" style="display:flex;flex-direction:column;">
            <label style="font-size:0.78rem;font-weight:700;margin-bottom:6px;">Name of Requesting Party</label>
            <input type="text" id="psa-req-name" value="<?= htmlspecialchars($c['informant_name'] ?? '') ?>" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:0.88rem;font-family:inherit;" />
          </div>
          <div class="form-group" style="display:flex;flex-direction:column;">
            <label style="font-size:0.78rem;font-weight:700;margin-bottom:6px;">Address of Requesting Party</label>
            <textarea id="psa-req-addr" rows="2" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:0.88rem;font-family:inherit;resize:vertical;"><?= htmlspecialchars($c['informant_address'] ?? '') ?></textarea>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div class="form-group" style="display:flex;flex-direction:column;">
              <label style="font-size:0.78rem;font-weight:700;margin-bottom:6px;">Number of Copies</label>
              <input type="number" id="psa-copies" value="1" min="1" max="10" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:0.88rem;font-family:inherit;" />
            </div>
            <div class="form-group" style="display:flex;flex-direction:column;">
              <label style="font-size:0.78rem;font-weight:700;margin-bottom:6px;">Purpose</label>
              <select id="psa-purpose" style="padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:0.88rem;font-family:inherit;">
                <option>Claims / Insurance</option>
                <option>Legal Proceedings</option>
                <option>Estate Settlement</option>
                <option>SSS / GSIS Benefits</option>
                <option>Bank Transaction</option>
                <option>Travel / Repatriation</option>
                <option>Personal Records</option>
                <option>Other</option>
              </select>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px;padding-top:16px;border-top:1px solid var(--border);">
          <button class="btn btn-outline" onclick="closePsaModal()">Cancel</button>
          <button class="btn btn-gold" id="psa-submit-btn" onclick="submitPsaRequest()">Submit Request</button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div id="toast" style="display:none; position:fixed; top:24px; right:24px; background:var(--primary); color:white; padding:16px 24px; border-radius:12px; z-index:10000; font-weight:700; box-shadow:0 10px 30px rgba(0,0,0,0.2); max-width:420px; font-size:0.92rem; line-height:1.5;"></div>

  <script src="<?= asset('JS/user-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    // Live date display in top bar
    document.addEventListener('DOMContentLoaded', function() {
      const dateEl = document.getElementById('top-date');
      if (dateEl) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.textContent = now.toLocaleDateString('en-US', options);
      }

      // Auto calculate age
      const dobInput = document.getElementById('deceased-dob');
      const dodInput = document.getElementById('deceased-dod');
      const ageYears = document.getElementById('deceased-age-years');
      const ageMonths = document.getElementById('deceased-age-months');
      const ageDays = document.getElementById('deceased-age-days');

      function calculateAge() {
        if (!dobInput || !dodInput || !dobInput.value || !dodInput.value) return;
        const dob = new Date(dobInput.value);
        const dod = new Date(dodInput.value);
        if (dod < dob) return;

        let years = dod.getFullYear() - dob.getFullYear();
        let months = dod.getMonth() - dob.getMonth();
        let days = dod.getDate() - dob.getDate();

        if (days < 0) {
          months--;
          const prevMonthDays = new Date(dod.getFullYear(), dod.getMonth(), 0).getDate();
          days += prevMonthDays;
        }
        if (months < 0) {
          years--;
          months += 12;
        }

        if (ageYears && years >= 0) ageYears.value = years;
        if (ageMonths && months >= 0) ageMonths.value = months;
        if (ageDays && days >= 0) ageDays.value = days;
      }

      if (dobInput && dodInput) {
        dobInput.addEventListener('change', calculateAge);
        dodInput.addEventListener('change', calculateAge);
      }
    });

    // Tab Navigation
    function showTab(tab) {
      document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
      const target = document.getElementById('tab-' + tab);
      if (target) {
        target.style.display = 'block';
      }
      document.querySelectorAll('.tab-btn').forEach(el => {
        const isActive = el.dataset.tab === tab;
        if (isActive) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });
      // Update browser URL without reload
      try {
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
      } catch (e) {}
    }

    // Auto-select tab if in query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const initialTabParam = urlParams.get('tab');
    if (initialTabParam && document.getElementById('tab-' + initialTabParam)) {
      showTab(initialTabParam);
    }

    // Handle Certificate Request Submission (Basic Info Form)
    const btnSubmitCert = document.getElementById('btn-submit-cert');
    if (btnSubmitCert) {
      btnSubmitCert.addEventListener('click', async function() {
        const btn = this;
        
        const firstName = document.getElementById('deceased-first-name')?.value.trim() || '';
        const middleName = document.getElementById('deceased-middle-name')?.value.trim() || '';
        const lastName = document.getElementById('deceased-last-name')?.value.trim() || '';
        const dob = document.getElementById('deceased-dob')?.value || '';
        const dod = document.getElementById('deceased-dod')?.value || '';
        const sex = document.getElementById('deceased-sex')?.value || '';
        const civilStatus = document.getElementById('deceased-civil-status')?.value || '';
        const religion = document.getElementById('deceased-religion')?.value || 'Islam';
        const citizenship = document.getElementById('deceased-citizenship')?.value.trim() || 'FILIPINO';
        const pod = document.getElementById('deceased-pod')?.value.trim() || '';
        const residence = document.getElementById('deceased-residence')?.value.trim() || '';
        const occupation = document.getElementById('deceased-occupation')?.value.trim() || '';
        const fatherName = document.getElementById('deceased-father')?.value.trim() || '';
        const motherName = document.getElementById('deceased-mother')?.value.trim() || '';

        const informantName = document.getElementById('informant-name')?.value.trim() || '';
        const relationship = document.getElementById('informant-relationship')?.value || '';
        const informantContact = document.getElementById('informant-contact')?.value.trim() || '';
        const informantEmail = document.getElementById('informant-email')?.value.trim() || '';
        const informantAddress = document.getElementById('informant-address')?.value.trim() || '';

        // Validation
        if (!firstName) {
          alert('Please enter the deceased person\'s First Name.');
          document.getElementById('deceased-first-name')?.focus();
          return;
        }
        if (!lastName) {
          alert('Please enter the deceased person\'s Last Name.');
          document.getElementById('deceased-last-name')?.focus();
          return;
        }
        if (!dod) {
          alert('Please specify the Date of Death.');
          document.getElementById('deceased-dod')?.focus();
          return;
        }
        if (!sex) {
          alert('Please select the Sex of the deceased.');
          document.getElementById('deceased-sex')?.focus();
          return;
        }
        if (!pod) {
          alert('Please enter the Place of Death.');
          document.getElementById('deceased-pod')?.focus();
          return;
        }
        if (!residence) {
          alert('Please enter the Residence address.');
          document.getElementById('deceased-residence')?.focus();
          return;
        }
        if (!informantName) {
          alert('Please enter the Informant / Requester Full Name.');
          document.getElementById('informant-name')?.focus();
          return;
        }
        if (!relationship) {
          alert('Please select your Relationship to the deceased.');
          document.getElementById('informant-relationship')?.focus();
          return;
        }
        if (!informantContact) {
          alert('Please provide a Contact / Mobile Number for release scheduling updates.');
          document.getElementById('informant-contact')?.focus();
          return;
        }

        const payload = {
          firstName, middleName, lastName,
          dob: dob || null, dod, sex, civilStatus, religion, citizenship,
          pod, residence, occupation, fatherName, motherName,
          informantName, relationship, informantContact, informantEmail,
          informantAddress: informantAddress || residence
        };

        btn.disabled = true;
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" style="width:18px;height:18px;fill:none;stroke:white;stroke-width:2;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="white"></path></svg> Requesting Certificate...';

        try {
          const res = await fetch('<?= url("/user/services/burial/submit") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const data = await res.json();

          if (data.success) {
            const toast = document.getElementById('toast');
            const trackingId = data.case_number || data.ref_id || 'Submitted';
            toast.innerHTML = '<strong>Success!</strong> Certificate request submitted.<br>Tracking ID: <code>' + trackingId + '</code><br><span style="font-size:0.8rem;opacity:0.9;">Opening certificate tracker...</span>';
            toast.style.display = 'block';

            setTimeout(() => {
              const targetUrl = data.case_number 
                ? '<?= url("/user/services/funeral/tracking") ?>?case=' + encodeURIComponent(data.case_number) + '&tab=tracking'
                : '<?= url("/user/services/funeral/tracking?tab=tracking") ?>';
              window.location.href = targetUrl;
            }, 1500);
          } else {
            throw new Error(data.message || 'Server error');
          }
        } catch (e) {
          console.error(e);
          alert('Failed to submit certificate request. Please try again.');
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      });
    }

    // Document upload
    document.querySelectorAll('.upload-doc-input').forEach(input => {
      input.addEventListener('change', async function() {
        const file = this.files[0];
        if (!file) return;
        const docId = this.dataset.docId;
        const caseId = this.dataset.caseId;
        const fd = new FormData();
        fd.append('document', file);
        fd.append('doc_id', docId);
        fd.append('case_id', caseId);
        try {
          const res = await fetch('<?= url('/user/services/funeral/upload') ?>', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) { location.reload(); }
          else { alert('Upload failed: ' + (data.error || 'Unknown error')); }
        } catch (e) { alert('Upload error. Please try again.'); }
      });
    });

    // PSA Modal
    function openPsaModal() { 
      const m = document.getElementById('psa-modal');
      if (m) m.style.display = 'flex'; 
    }
    function closePsaModal() { 
      const m = document.getElementById('psa-modal');
      if (m) m.style.display = 'none'; 
    }
    const psaModalEl = document.getElementById('psa-modal');
    if (psaModalEl) {
      psaModalEl.addEventListener('click', e => { 
        if (e.target.id === 'psa-modal') closePsaModal(); 
      });
    }

    async function submitPsaRequest() {
      const btn = document.getElementById('psa-submit-btn');
      btn.disabled = true;
      try {
        const res = await fetch('<?= url('/user/services/funeral/psa-request') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            case_id: <?= $c['id'] ?? 0 ?>,
            requesting_party_name: document.getElementById('psa-req-name')?.value.trim() || '',
            requesting_party_address: document.getElementById('psa-req-addr')?.value.trim() || '',
            num_copies: parseInt(document.getElementById('psa-copies')?.value) || 1,
            purpose: document.getElementById('psa-purpose')?.value || 'Personal Records'
          })
        });
        const data = await res.json();
        if (data.success) { location.reload(); }
        else { alert('Error: ' + (data.error || 'Failed')); btn.disabled = false; }
      } catch (e) { alert('Error. Please try again.'); btn.disabled = false; }
    }
  </script>
</body>
</html>
