<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/User.php';

$u = new User();
$d = $u->findById($_SESSION['user_id']) ?: [];
$e = $u->getAdditionalInfo($_SESSION['user_id']) ?: [];
$d = array_merge($d, $e);
$name = htmlspecialchars(($d['first_name']??'').' '.($d['last_name']??''));
$init = strtoupper(substr($d['first_name']??'S',0,1));

// Fetch Actual Burial Requests from DB
require_once BASE_PATH . '/app/models/BurialRequest.php';
$burialModel = new BurialRequest();
$allRequests = $burialModel->getByTenantId((int)$_SESSION['user_id']);

// Support selecting specific case via ?id= or ?ref= parameter
$selectedRef = $_GET['id'] ?? ($_GET['ref'] ?? null);
$activeBurial = null;
if ($selectedRef) {
    foreach ($allRequests as $r) {
        if ($r['ref_id'] === $selectedRef || (string)$r['id'] === (string)$selectedRef) {
            $activeBurial = $r;
            break;
        }
    }
}
if (!$activeBurial && !empty($allRequests)) {
    $activeBurial = $allRequests[0];
}

$hasRequest = !empty($activeBurial) && !empty($activeBurial['id']);

$rawStatus = $hasRequest ? ($activeBurial['status'] ?? 'Pending') : 'No active request';
if ($hasRequest && !in_array($rawStatus, ['Accepted', 'Rejected', 'Completed'])) {
    $rawStatus = 'Pending';
}
$st = strtolower($rawStatus);

$isInfoRequired = $hasRequest && ($st === 'additional information required' || strtolower($activeBurial['death_cert_status'] ?? '') === 'additional information required');
$isCompleted = $hasRequest && ($st === 'burial completed' || $st === 'completed' || strtolower($activeBurial['death_cert_status'] ?? '') === 'registered' || strtolower($activeBurial['cemetery_plot_status'] ?? '') === 'plot assigned' || !empty($activeBurial['copy_received_confirmed']));
$isRejected = $hasRequest && ($st === 'rejected' || strtolower($activeBurial['death_cert_status'] ?? '') === 'rejected' || strtolower($activeBurial['cemetery_plot_status'] ?? '') === 'rejected');

$isCopyRequest = ($hasRequest && ($activeBurial['cert_action_type'] ?? 'register') === 'copy');

// Copy stages rank (1 to 5)
$copyRank = 1;
if ($isCopyRequest) {
    if (!empty($activeBurial['copy_received_confirmed'])) {
        $copyRank = 5;
    } elseif (!empty($activeBurial['copy_issued_at'])) {
        $copyRank = 4;
    } elseif (!empty($activeBurial['existing_record_verified']) || strtolower($activeBurial['status'] ?? '') === 'accepted for coordination') {
        $copyRank = 3;
    } elseif (!empty($activeBurial['existing_record_verified'])) {
        $copyRank = 2;
    } else {
        $copyRank = 1;
    }
}
$copyPercentages = [1 => 0, 2 => 25, 3 => 50, 4 => 75, 5 => 100];
$copyProgressPercent = $copyPercentages[$copyRank] ?? 0;

// Death Certificate Stages Rank (1 to 6)
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
$currentDcStatus = $hasRequest ? ($activeBurial['death_cert_status'] ?? 'Submitted') : 'Not Requested';
$dcRank = $dcStageRanks[strtolower($currentDcStatus)] ?? 1;
$dcPercentages = [1 => 0, 2 => 20, 3 => 40, 4 => 60, 5 => 80, 6 => 100];
$dcProgressPercent = ($hasRequest && !empty($activeBurial['death_cert_requested'])) ? ($dcPercentages[$dcRank] ?? 0) : 0;

// Fetch schedule history for active case
$scheduleHistory = ($hasRequest && !empty($activeBurial['id'])) ? $burialModel->getScheduleHistory((int)$activeBurial['id']) : [];
$isAuthorizedForRelease = $hasRequest && in_array(strtolower($currentDcStatus), ['ready for release', 'completed', 'registered', 'released']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ISCAG — Damayan Death Certificate Dashboard</title>
<link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
<link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:#f4f6f8;--primary:#1b5e20;--primary-light:#e8f5e9;--gold:#D4AF37;
    --text-main:#1f2937;--text-muted:#6b7280;--border:#f3f4f6;--white:#fff;
    --radius:16px;--shadow:0 4px 20px rgba(0,0,0,0.03);
    --success:#059669;--success-light:#ecfdf5;
  }
  body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text-main);margin:0;}
  .burial-wrapper{padding:40px;max-width:1200px;margin:0 auto;}

  /* Greeting */
  .greeting{margin-bottom:32px;}
  .greeting h1{font-family:'Lora',serif;font-size:1.8rem;font-weight:700;margin:0;color:var(--text-main);}
  .greeting p{color:var(--text-muted);margin:6px 0 0;font-size:.95rem;}

  /* Grid */
  .grid-container{display:grid;grid-template-columns:repeat(12,1fr);gap:24px;}
  .card{background:var(--white);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);}

  /* Summary Row */
  .summary-row{grid-column:span 12;display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
  .summary-card{background:var(--white);border-radius:var(--radius);padding:24px;display:flex;align-items:center;gap:16px;box-shadow:var(--shadow);border:1px solid var(--border);transition:transform .3s,box-shadow .3s;}
  .summary-card:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(0,0,0,.05);}
  .summary-card.active{background:linear-gradient(135deg,#1b5e20,#2e7d32);color:var(--white);}
  .summary-card.active .val{color:var(--white);}
  .summary-card.active p{color:rgba(255,255,255,.8);}
  .summary-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--primary-light);color:var(--primary);}
  .summary-card.active .summary-icon{background:rgba(255,255,255,.2);color:var(--white);}
  .summary-info h4{margin:0 0 4px;font-size:.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;}
  .summary-card.active .summary-info h4{color:rgba(255,255,255,.8);}
  .summary-info .val{font-size:1.2rem;font-weight:700;color:var(--text-main);margin-bottom:2px;}
  .summary-info p{margin:0;font-size:.85rem;color:var(--text-muted);}

  /* Death Certificate Pipeline Tracker */
  .progress-card{grid-column:span 12;padding:30px;}
  .progress-card h3{margin:0 0 6px;font-size:1.1rem;font-weight:700;}
  .progress-tracker{display:flex;justify-content:space-between;align-items:flex-start;margin-top:24px;position:relative;padding:0 10px;}
  .progress-line{position:absolute;top:14px;left:45px;right:45px;height:2px;background:#e5e7eb;z-index:1;}
  .progress-line-fill{position:absolute;top:14px;left:45px;height:2px;background:var(--primary);z-index:2;transition:width .5s ease;}
  .step{display:flex;flex-direction:column;align-items:center;position:relative;z-index:3;gap:8px;width:110px;}
  .step-icon{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--white);border:2px solid #e5e7eb;color:#9ca3af;font-weight:700;font-size:.75rem;transition:.3s;}
  .step.completed .step-icon{background:var(--primary);border-color:var(--primary);color:var(--white);}
  .step.active .step-icon{border-color:var(--primary);color:var(--primary);box-shadow:0 0 0 4px var(--primary-light);}
  .step-label{font-size:.72rem;font-weight:700;color:var(--text-main);text-align:center;line-height:1.25;}

  /* Dual Parallel Cards */
  .dual-card{grid-column:span 6;padding:26px;}
  .dual-card h3{margin:0 0 16px;font-size:1.1rem;font-weight:700;display:flex;align-items:center;justify-content:space-between;}

  /* Details & Guidelines */
  .details-card{grid-column:span 8;}
  .guidelines-card{grid-column:span 4;background:linear-gradient(135deg,#f8fafc,#f1f5f9);}
  .details-card h3{margin:0 0 20px;font-size:1.1rem;font-weight:700;padding-bottom:15px;border-bottom:1px solid var(--border);}
  .info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;}
  .info-group{display:flex;flex-direction:column;gap:5px;}
  .info-label{font-size:.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:700;letter-spacing:.5px;}
  .info-value{font-size:.95rem;color:var(--text-main);font-weight:600;}

  .guidelines-card h3{margin:0 0 15px;font-size:1.1rem;font-weight:700;color:var(--primary);}
  .guide-item{display:flex;gap:12px;margin-bottom:16px;}
  .guide-icon{color:var(--primary);flex-shrink:0;margin-top:2px;}
  .guide-text h5{margin:0 0 4px;font-size:.88rem;font-weight:700;color:var(--text-main);}
  .guide-text p{margin:0;font-size:.83rem;color:var(--text-muted);line-height:1.5;}

  /* Services Checklist */
  .services-card{grid-column:span 12;padding:30px;}
  .services-card h3{margin:0 0 20px;font-size:1.1rem;font-weight:700;}

  /* Action Row */
  .action-row{grid-column:span 12;display:flex;gap:16px;justify-content:flex-end;flex-wrap:wrap;}
  .btn-action{padding:12px 28px;border-radius:12px;font-weight:700;font-size:.88rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;border:none;}
  .btn-action:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08);}
  .btn-primary-act{background:var(--primary);color:var(--white);}
  .btn-outline-act{background:var(--white);color:var(--primary);border:2px solid var(--primary);}
  .btn-outline-act:hover{background:var(--primary);color:var(--white);}

  .badge-tag {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
  }
  .badge-tag.success { background: #ecfdf5; color: #047857; }
  .badge-tag.info { background: #eff6ff; color: #1d4ed8; }
  .badge-tag.warning { background: #fffbeb; color: #b45309; }
  .badge-tag.danger { background: #fef2f2; color: #dc2626; }

  @media(max-width:900px){
    .summary-row{grid-template-columns:1fr;}
    .dual-card,.details-card,.guidelines-card,.services-card{grid-column:span 12;}
    .info-grid{grid-template-columns:1fr;}
    .action-row{justify-content:center;}
  }
  @media print{
    .top-bar,.sidebar,.action-row,.btn-topbar{display:none!important;}
    .burial-wrapper{padding:20px;}
    .card{box-shadow:none;border:1px solid #ccc;}
  }
</style>
</head>
<body>
<div class="app-wrapper">
<?php $active_page='burial'; include BASE_PATH.'/app/views/user/sidebar.php'; ?>
<div class="main-content">

<div class="top-bar">
    <div class="top-bar-left">
        <div class="top-bar-title">Damayan Services Dashboard</div>
        <div class="top-bar-subtitle">Death Certificate Civil Registration &amp; Official Issuance</div>
    </div>
    <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Main Dashboard</a>
    </div>
</div>

<div class="burial-wrapper">

<div class="greeting">
  <h1>Assalamu Alaikum, <?= $name ?></h1>
  <p>Track your Death Certificate civil registration and scheduled claiming release below.</p>
</div>

<!-- Alert: Additional Information Required -->
<?php if ($isInfoRequired): ?>
<div style="background: #fffbeb; border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
  <div style="display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    </div>
    <div>
      <div style="font-size: 0.75rem; font-weight: 800; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">Action Required — Additional Information Needed</div>
      <div style="font-size: 1.05rem; font-weight: 700; color: #78350f; margin-top: 2px;">
        Request #<?= htmlspecialchars($activeBurial['ref_id']) ?>: <?= htmlspecialchars($activeBurial['info_request_notes'] ?? 'The coordinator has requested additional details or documents.') ?>
      </div>
      <div style="font-size: 0.85rem; color: #92400e; margin-top: 2px;">
        Please review the required details and provide the necessary response to proceed.
      </div>
    </div>
  </div>
  <div>
    <a href="<?= url('/user/services/burial-details?id=' . urlencode($activeBurial['ref_id'])) ?>" class="btn-action" style="background:#b45309; color:white; text-decoration:none; padding:10px 22px; font-size:0.85rem;">
      View &amp; Submit Requirements →
    </a>
  </div>
<?php endif; ?>

<!-- PROMINENT BANNER: CERTIFICATE READY FOR RELEASE WITH CONFIRMED SCHEDULE -->
<?php if ($hasRequest && !empty($activeBurial['scheduled_release_date']) && empty($activeBurial['certificate_released_at']) && strtolower($currentDcStatus) === 'ready for release'): ?>
<div class="card" style="grid-column: span 12; background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border: 2px solid #10b981; border-radius: 16px; padding: 26px 30px; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.12); margin-bottom: 24px;">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:20px; flex-wrap:wrap;">
    <div style="display:flex; align-items:flex-start; gap:16px;">
      <div style="width:52px; height:52px; border-radius:14px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
      </div>
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:#d1fae5; color:#065f46; font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:6px; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg> Confirmed Claiming Appointment
        </div>
        <h2 style="font-family:'Lora',serif; font-size:1.45rem; font-weight:800; color:#064e3b; margin:0 0 6px;">
          Your Death Certificate Is Ready for Release
        </h2>
        <p style="font-size:0.95rem; color:#047857; margin:0; line-height:1.5;">
          Please go to the <strong>Damayan Department</strong> to claim your death certificate on the scheduled date and time below.
        </p>
      </div>
    </div>
    <span class="badge-tag success" style="font-size:0.82rem; padding:6px 16px; background:#059669; color:#fff; border-radius:30px; font-weight:700;">
      Ready for Release
    </span>
  </div>

  <!-- Actual Saved Schedule Details from Database -->
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-top:20px; background:#ffffff; border:1.5px solid #a7f3d0; border-radius:12px; padding:18px 20px;">
    <div>
      <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled Date</span>
      <strong style="font-size:1.15rem; color:#0f172a;"><?= date('F d, Y', strtotime($activeBurial['scheduled_release_date'])) ?></strong>
    </div>
    <div>
      <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled Time</span>
      <strong style="font-size:1.15rem; color:#0f172a;"><?= !empty($activeBurial['scheduled_release_time']) ? date('g:i A', strtotime($activeBurial['scheduled_release_time'])) : '10:00 AM' ?></strong>
    </div>
    <div>
      <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Claiming Location</span>
      <strong style="font-size:1.05rem; color:#065f46;"><?= htmlspecialchars($activeBurial['release_location'] ?: 'Damayan Department') ?></strong>
    </div>
    <div>
      <span style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#6b7280; display:block; letter-spacing:0.04em; margin-bottom:2px;">Scheduled By</span>
      <span style="font-size:0.95rem; color:#374151; font-weight:700;"><?= htmlspecialchars($activeBurial['release_scheduled_by_name'] ?: 'Damayan Admin') ?></span>
    </div>
  </div>

  <?php if (!empty($activeBurial['release_instructions'])): ?>
  <div style="margin-top:14px; background:#f0fdf4; border-left:4px solid #059669; padding:14px 18px; border-radius:0 10px 10px 0; font-size:0.88rem; color:#065f46;">
    <strong style="display:block; margin-bottom:4px; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.04em;">Instructions from Damayan Department:</strong>
    <div style="color:#1f2937; line-height:1.5;"><?= nl2br(htmlspecialchars($activeBurial['release_instructions'])) ?></div>
  </div>
  <?php endif; ?>
</div>
<?php elseif ($hasRequest && !empty($activeBurial['certificate_released_at'])): ?>
<!-- BANNER: CERTIFICATE OFFICIALLY RELEASED & COMPLETED -->
<div class="card" style="grid-column: span 12; background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); border: 2px solid #059669; border-radius: 16px; padding: 22px 28px; margin-bottom: 24px;">
  <div style="display:flex; align-items:center; gap:16px;">
    <div style="width:48px; height:48px; border-radius:12px; background:#059669; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
    </div>
    <div>
      <div style="font-size: 0.75rem; font-weight: 800; color: #065f46; text-transform: uppercase; letter-spacing: 0.05em;">Certificate Claimed &bull; Request Completed</div>
      <h3 style="margin:2px 0 4px; font-size:1.2rem; color:#064e3b; font-family:'Lora',serif;">
        Death Certificate Officially Released
      </h3>
      <p style="margin:0; font-size:0.88rem; color:#047857;">
        Released to <strong><?= htmlspecialchars($activeBurial['certificate_recipient_name'] ?: 'Claimant') ?></strong> on <strong><?= date('F d, Y \a\t g:i A', strtotime($activeBurial['certificate_released_at'])) ?></strong> by <?= htmlspecialchars($activeBurial['certificate_released_by'] ?: 'Damayan Staff') ?>.
        <?php if (!empty($activeBurial['release_notes'])): ?>
          <span style="display:block; margin-top:2px; font-size:0.82rem; color:#475569;"><em>Note / ID Ref: <?= htmlspecialchars($activeBurial['release_notes']) ?></em></span>
        <?php endif; ?>
      </p>
    </div>
  </div>
</div>
<?php elseif ($hasRequest && !empty($activeBurial['release_cancelled_at']) && empty($activeBurial['scheduled_release_date'])): ?>
<!-- BANNER: CLAIMING SCHEDULE CANCELLED NOTICE -->
<div class="card" style="grid-column: span 12; background: #fffbeb; border: 1.5px solid #fde68a; border-left: 5px solid #f59e0b; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px;">
  <div style="display:flex; align-items:center; gap:14px;">
    <div style="width:42px; height:42px; border-radius:10px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
    </div>
    <div>
      <div style="font-size:0.75rem; font-weight:800; color:#92400e; text-transform:uppercase; letter-spacing:0.04em;">Claiming Appointment Update</div>
      <div style="font-size:0.98rem; font-weight:700; color:#78350f; margin-top:2px;">
        Scheduled Release Cancelled by Damayan Department
      </div>
      <div style="font-size:0.85rem; color:#92400e; margin-top:2px;">
        Reason: <strong><?= htmlspecialchars($activeBurial['release_cancellation_reason'] ?: 'Scheduling adjustment') ?></strong>. The request is currently at <strong>Certificate Prepared</strong> stage. You will be notified once a revised claiming appointment is confirmed.
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="grid-container">

<?php if (!$hasRequest): ?>
  <!-- Empty State when user has no submissions -->
  <div class="card" style="grid-column:span 12; text-align:center; padding:50px 30px;">
    <div style="width:64px; height:64px; border-radius:18px; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
    </div>
    <h2 style="font-family:'Lora',serif; font-size:1.5rem; margin:0 0 8px; color:var(--text-main);">No Damayan Requests Filed Yet</h2>
    <p style="color:var(--text-muted); max-width:540px; margin:0 auto 22px; font-size:0.92rem; line-height:1.6;">
      Assalamu Alaikum. You have not filed any death certificate processing requests. Click below to begin your application.
    </p>
    <a href="<?= url('/user/services/burial-details?action=request') ?>" class="btn-action btn-primary-act" style="display:inline-flex; text-decoration:none; margin:0 auto;">
      Submit Death Certificate Request →
    </a>
  </div>
<?php else: ?>

  <?php if(count($allRequests) > 1): ?>
  <!-- Case Selector Bar if multiple cases exist -->
  <div class="card" style="grid-column:span 12; padding:14px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; background:#f9fafb;">
    <div style="display:flex; align-items:center; gap:8px;">
      <span style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Viewing Case:</span>
      <strong style="color:var(--primary); font-size:1rem;">#<?= htmlspecialchars($activeBurial['ref_id']) ?> — <?= htmlspecialchars($activeBurial['deceased_name']) ?></strong>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <label for="switchCaseSelect" style="font-size:0.8rem; font-weight:600; color:var(--text-muted);">Switch Case:</label>
      <select id="switchCaseSelect" onchange="if(this.value) location.href='<?= url('/user/services/burial-dashboard?id=') ?>' + encodeURIComponent(this.value)" style="padding:6px 12px; border-radius:8px; border:1px solid #d1d5db; font-size:0.85rem; font-family:inherit;">
        <?php foreach($allRequests as $ar): ?>
          <option value="<?= htmlspecialchars($ar['ref_id']) ?>" <?= $ar['ref_id'] === $activeBurial['ref_id'] ? 'selected' : '' ?>>
            #<?= htmlspecialchars($ar['ref_id']) ?> - <?= htmlspecialchars($ar['deceased_name']) ?> (<?= htmlspecialchars(in_array($ar['status'], ['Accepted', 'Rejected', 'Completed']) ? $ar['status'] : 'Pending') ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <?php endif; ?>

  <div class="summary-row">
    <div class="summary-card active">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div class="summary-info">
        <h4><?= $isCopyRequest ? 'Copy Request Ref ID' : 'Case Reference ID' ?></h4>
        <div class="val"><?= htmlspecialchars($activeBurial['ref_id']) ?></div>
        <p><?= $isCopyRequest ? 'Keep this reference to track your Certificate Copy Request' : 'Keep this reference for hospital & office coordination' ?></p>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="summary-info">
        <h4>Deceased Record</h4>
        <div class="val"><?= htmlspecialchars($activeBurial['deceased_name']) ?></div>
        <p>Date of Death: <?= !empty($activeBurial['date_of_death']) ? date('M d, Y', strtotime($activeBurial['date_of_death'])) : 'N/A' ?></p>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon" style="background:<?= $isCompleted ? 'var(--success-light)' : ($hasRequest ? '#eff6ff' : '#f3f4f6') ?>;color:<?= $isCompleted ? 'var(--success)' : ($hasRequest ? '#1d4ed8' : '#9ca3af') ?>;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="summary-info">
        <h4>Overall Status</h4>
        <div class="val" style="color:<?= $isCompleted ? 'var(--success)' : '#1d4ed8' ?>;">
          <?php if($isCopyRequest): ?>
            <?= !empty($activeBurial['copy_received_confirmed']) ? 'Copy Received & Confirmed' : (!empty($activeBurial['copy_issued_at']) ? 'Copy Issued / Ready' : ($activeBurial['existing_record_verified'] ? 'Record Verified' : 'Awaiting Record Search')) ?>
          <?php else: ?>
            <?= htmlspecialchars($rawStatus) ?>
          <?php endif; ?>
        </div>
        <p><?= $hasRequest ? ($isCopyRequest ? 'Certificate Copy Request' : 'Active Case Record') : 'No Active Submission' ?></p>
      </div>
    </div>
  </div>

  <!-- Death Certificate Processing Tracker -->
  <div class="card" style="grid-column: span 12; padding: 26px;">
    <?php if ($isCopyRequest): ?>
      <h3>
        <span style="display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          Request Another Death Certificate (Copy)
        </span>
        <span class="badge-tag <?= !empty($activeBurial['copy_received_confirmed']) ? 'success' : (!empty($activeBurial['copy_issued_at']) ? 'info' : ($activeBurial['existing_record_verified'] ? 'info' : 'warning')) ?>">
          <?= !empty($activeBurial['copy_received_confirmed']) ? 'Copy Received' : (!empty($activeBurial['copy_issued_at']) ? 'Copy Issued' : ($activeBurial['existing_record_verified'] ? 'Record Verified' : 'Awaiting Record Search')) ?>
        </span>
      </h3>

      <div style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="display:flex; flex-direction:column; gap:10px; font-size:0.86rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-muted);">Existing Certificate No:</span>
            <strong style="color:var(--primary);"><?= htmlspecialchars($activeBurial['existing_cert_no'] ?: 'Not Provided') ?></strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-muted);">Copies Requested:</span>
            <strong><?= (int)($activeBurial['num_copies'] ?? 1) ?> copy / copies</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-muted);">Reason for Request:</span>
            <span><?= htmlspecialchars($activeBurial['copy_reason'] ?: 'Personal / Official Record') ?></span>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-muted);">Existing Record Check:</span>
            <?php if(!empty($activeBurial['existing_record_verified'])): ?>
              <span style="color:var(--success); font-weight:700;">Verified (Linked to Registered Record)</span>
            <?php else: ?>
              <span style="color:#b45309; font-weight:600;">Verification Pending (Staff Locating Record)</span>
            <?php endif; ?>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-muted);">Issuance Status:</span>
            <?php if(!empty($activeBurial['copy_issued_at'])): ?>
              <span style="color:var(--primary); font-weight:700;">Issued on <?= date('M d, Y', strtotime($activeBurial['copy_issued_at'])) ?> (Ref: <?= htmlspecialchars($activeBurial['death_cert_certified_copy_ref'] ?: 'Ref Assigned') ?>)</span>
            <?php else: ?>
              <span style="color:var(--text-muted); font-style:italic;">Processing / Awaiting Staff Issuance</span>
            <?php endif; ?>
          </div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="color:var(--text-muted);">Receipt Confirmation:</span>
            <?php if(!empty($activeBurial['copy_received_confirmed'])): ?>
              <span style="color:var(--success); font-weight:700;">Confirmed Received on <?= date('M d, Y', strtotime($activeBurial['copy_received_at'])) ?></span>
            <?php elseif(!empty($activeBurial['copy_issued_at'])): ?>
              <a href="<?= url('/user/services/burial-details?id=' . urlencode($activeBurial['ref_id'])) ?>" class="btn-action btn-primary-act" style="font-size:0.75rem; padding:4px 12px; text-decoration:none;">Confirm Receipt →</a>
            <?php else: ?>
              <span style="color:var(--text-muted); font-style:italic;">Awaiting Copy Release</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">
        Requests for additional copies are verified against registered records. Copies are not marked as completed until officially confirmed received.
      </div>
    <?php else: ?>
      <h3>
        <span style="display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color:var(--primary);"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          Death Certificate Processing
        </span>
        <span class="badge-tag <?= in_array(strtolower($currentDcStatus), ['registered', 'certified copy requested or received']) ? 'success' : 'info' ?>">
          <?= htmlspecialchars($currentDcStatus) ?>
        </span>
      </h3>

      <?php if(!empty($activeBurial['death_cert_requested'])): ?>
        <div style="background:#fafbfc; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:16px;">
          <div style="display:flex; flex-direction:column; gap:10px; font-size:0.86rem;">
            <div style="display:flex; justify-content:space-between;">
              <span style="color:var(--text-muted);">Current Stage:</span>
              <strong style="color:var(--damayan-dark);"><?= htmlspecialchars($currentDcStatus) ?></strong>
            </div>
            <?php if(!empty($activeBurial['death_cert_registry_no'])): ?>
              <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);">LCRO Registry No:</span>
                <strong style="color:var(--success);"><?= htmlspecialchars($activeBurial['death_cert_registry_no']) ?></strong>
              </div>
            <?php endif; ?>
            <?php if(!empty($activeBurial['death_cert_registered_date'])): ?>
              <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);">Date Registered:</span>
                <span><?= date('M d, Y', strtotime($activeBurial['death_cert_registered_date'])) ?></span>
              </div>
            <?php endif; ?>
            <?php if(!empty($activeBurial['death_cert_certified_copy_ref'])): ?>
              <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted);">PSA / Certified Copy:</span>
                <span style="font-weight:600;"><?= htmlspecialchars($activeBurial['death_cert_certified_copy_ref']) ?></span>
              </div>
            <?php endif; ?>
            <div style="display:flex; justify-content:space-between;">
              <span style="color:var(--text-muted);">Hospital / Office Personnel:</span>
              <span><?= htmlspecialchars($activeBurial['death_cert_responsible_personnel'] ?: 'Damayan Coordinator Assigned') ?></span>
            </div>
          </div>
        </div>

        <div style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">
          Damayan Staff coordinates civil registration with the Local Civil Registrar (LCRO). Official certifications are processed through authorized government agencies.
        </div>
      <?php else: ?>
        <div style="padding:20px; text-align:center; color:var(--text-muted); background:#fafbfc; border-radius:12px;">
          Death certificate was not requested in this case. Click 'Submit New Request' if you wish to file a death certificate request.
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- Dynamic Progress Trackers based on Requested Services -->
  <?php if ($isCopyRequest): ?>
  <div class="card progress-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
      <div>
        <h3 style="margin:0 0 4px;">Death Certificate Copy Request Pipeline</h3>
        <div style="font-size:0.82rem; color:var(--text-muted);">Tracking verification of existing deceased records, staff processing, and receipt confirmation</div>
      </div>
      <span class="badge-tag <?= !empty($activeBurial['copy_received_confirmed']) ? 'success' : (!empty($activeBurial['copy_issued_at']) ? 'info' : ($activeBurial['existing_record_verified'] ? 'info' : 'warning')) ?>" style="font-size:0.8rem; padding:5px 12px;">
        Stage: <?= !empty($activeBurial['copy_received_confirmed']) ? 'Confirmed Received' : (!empty($activeBurial['copy_issued_at']) ? 'Copy Issued' : ($activeBurial['existing_record_verified'] ? 'Record Verified' : 'Awaiting Search')) ?>
      </span>
    </div>

    <div class="progress-tracker">
      <div class="progress-line"></div>
      <div class="progress-line-fill" style="width: calc((100% - 90px) * <?= $copyProgressPercent / 100 ?>);"></div>
      
      <?php
        $copyStepList = [
          1 => 'Copy Request<br>Received',
          2 => 'Record<br>Verification',
          3 => 'Copy<br>Processing',
          4 => 'Copy<br>Issued',
          5 => 'Confirmed<br>Received'
        ];
        foreach ($copyStepList as $sNum => $label):
          $stepClass = '';
          if ($copyRank > $sNum || ($copyRank === 5 && $sNum === 5)) {
            $stepClass = 'completed';
          } elseif ($copyRank === $sNum) {
            $stepClass = 'active';
          }
      ?>
      <div class="step <?= $stepClass ?>">
        <div class="step-icon">
          <?php if ($stepClass === 'completed'): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
          <?php else: ?>
            <?= $sNum ?>
          <?php endif; ?>
        </div>
        <div class="step-label"><?= $label ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php elseif (!empty($activeBurial['death_cert_requested'])): ?>
  <div class="card progress-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
      <div>
        <h3 style="margin:0 0 4px;">Death Certificate Civil Registration Pipeline</h3>
        <div style="font-size:0.82rem; color:var(--text-muted);">Tracking verification, hospital coordination, and local civil registry stages</div>
      </div>
      <span class="badge-tag <?= in_array(strtolower($currentDcStatus), ['registered', 'certified copy requested or received']) ? 'success' : 'info' ?>" style="font-size:0.8rem; padding:5px 12px;">
        Stage: <?= htmlspecialchars($currentDcStatus) ?>
      </span>
    </div>

    <div class="progress-tracker">
      <div class="progress-line"></div>
      <div class="progress-line-fill" style="width: calc((100% - 90px) * <?= $dcProgressPercent / 100 ?>);"></div>
      
      <?php
        $dcStepList = [
          1 => 'Submitted',
          2 => 'Under<br>Review',
          3 => 'Approved',
          4 => 'Certificate<br>Prepared',
          5 => 'Ready for<br>Release',
          6 => 'Completed'
        ];
        foreach ($dcStepList as $sNum => $label):
          $stepClass = '';
          if ($dcRank > $sNum || ($dcRank === 6 && $sNum === 6)) {
            $stepClass = 'completed';
          } elseif ($dcRank === $sNum) {
            $stepClass = 'active';
          }
      ?>
      <div class="step <?= $stepClass ?>">
        <div class="step-icon">
          <?php if ($stepClass === 'completed'): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
          <?php else: ?>
            <?= $sNum ?>
          <?php endif; ?>
        </div>
        <div class="step-label">
          <?= $label ?>
          <?php if ($sNum === 5 && !empty($activeBurial['scheduled_release_date'])): ?>
            <div style="margin-top:4px; font-size:0.65rem; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; border-radius:6px; padding:2px 4px; font-weight:800; line-height:1.2; text-align:center;">
              <?= date('M d', strtotime($activeBurial['scheduled_release_date'])) ?><br>
              <?= !empty($activeBurial['scheduled_release_time']) ? date('g:i A', strtotime($activeBurial['scheduled_release_time'])) : '' ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Appointment History & Revisions (if rescheduled or cancelled) -->
    <?php if (!empty($scheduleHistory)): ?>
    <div style="margin-top:20px; padding-top:14px; border-top:1px dashed #e2e8f0;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
        <span style="font-size:0.75rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.04em;">
          Claiming Appointment History (<?= count($scheduleHistory) ?>)
        </span>
      </div>
      <div style="display:flex; flex-direction:column; gap:6px;">
        <?php foreach($scheduleHistory as $sh): 
          $shDate = !empty($sh['created_at']) ? date('M d, Y \a\t g:i A', strtotime($sh['created_at'])) : '';
          $badgeBg = ($sh['action_type'] === 'SCHEDULED' ? '#ecfdf5' : ($sh['action_type'] === 'RESCHEDULED' ? '#fffbeb' : ($sh['action_type'] === 'CANCELLED' ? '#fef2f2' : '#f0fdf4')));
          $badgeCol = ($sh['action_type'] === 'SCHEDULED' ? '#065f46' : ($sh['action_type'] === 'RESCHEDULED' ? '#92400e' : ($sh['action_type'] === 'CANCELLED' ? '#991b1b' : '#047857')));
        ?>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; font-size:0.78rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-weight:800; padding:2px 8px; border-radius:4px; font-size:0.68rem; background:<?= $badgeBg ?>; color:<?= $badgeCol ?>;">
              <?= htmlspecialchars($sh['action_type']) ?>
            </span>
            <span style="color:#1e293b;">
              <?php if (!empty($sh['scheduled_date'])): ?>
                Appointment: <strong><?= date('M d, Y', strtotime($sh['scheduled_date'])) ?> <?= !empty($sh['scheduled_time']) ? date('g:i A', strtotime($sh['scheduled_time'])) : '' ?></strong>
              <?php endif; ?>
              <?php if (!empty($sh['reason'])): ?>
                &bull; <em><?= htmlspecialchars($sh['reason']) ?></em>
              <?php endif; ?>
            </span>
          </div>
          <span style="color:#64748b; font-size:0.72rem;"><?= $shDate ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
  <?php endif; ?>

  <!-- Request History Table -->
  <div class="card services-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h3 style="margin: 0;">My Damayan Case Submissions</h3>
      <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); background: var(--bg); padding: 4px 12px; border-radius: 100px;">Total: <?= count($allRequests) ?></span>
    </div>
    <div style="overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
        <thead>
          <tr style="border-bottom: 2px solid var(--border); text-align: left;">
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Ref ID</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Deceased Person</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Services</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Death Cert Stage</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Claiming Appointment</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Date</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($allRequests)): ?>
            <tr><td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">No requests found. Click 'Submit New Request' below to request Death Certificate assistance.</td></tr>
          <?php else: ?>
            <?php foreach($allRequests as $r): 
              $isCurrentRow = ($r['ref_id'] === $activeBurial['ref_id']);
            ?>
            <tr style="border-bottom: 1px solid var(--border); <?= $isCurrentRow ? 'background: #f0fdf4;' : '' ?>">
              <td style="padding: 16px 12px; font-weight: 700; color: var(--primary);">#<?= htmlspecialchars($r['ref_id']) ?></td>
              <td style="padding: 16px 12px; font-weight: 600;"><?= htmlspecialchars($r['deceased_name']) ?></td>
              <td style="padding: 16px 12px; font-size:0.8rem;">
                <?php if(($r['cert_action_type'] ?? '') === 'copy'): ?>
                  <span class="badge-tag info" style="background:#eff6ff;color:#1e40af;">Copy Request</span>
                <?php else: ?>
                  <span class="badge-tag info">Death Certificate</span>
                <?php endif; ?>
              </td>
              <td style="padding: 16px 12px; font-size:0.82rem;">
                <?php if(($r['cert_action_type'] ?? '') === 'copy'): ?>
                  <span class="badge-tag <?= !empty($r['copy_received_confirmed']) ? 'success' : (!empty($r['copy_issued_at']) ? 'info' : ($r['existing_record_verified'] ? 'info' : 'warning')) ?>">
                    <?= !empty($r['copy_received_confirmed']) ? 'Confirmed Received' : (!empty($r['copy_issued_at']) ? 'Copy Issued' : ($r['existing_record_verified'] ? 'Record Verified' : 'Awaiting Search')) ?>
                  </span>
                <?php elseif(!empty($r['death_cert_requested'])): ?>
                  <span class="badge-tag <?= in_array(strtolower($r['death_cert_status'] ?? ''), ['registered', 'certified copy requested or received']) ? 'success' : 'info' ?>">
                    <?= htmlspecialchars($r['death_cert_status'] ?? 'Request Received') ?>
                  </span>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-style:italic;">—</span>
                <?php endif; ?>
              </td>
              <td style="padding: 16px 12px; font-size:0.82rem;">
                <?php if(!empty($r['certificate_released_at'])): ?>
                  <span class="badge-tag success">Released</span>
                  <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;"><?= date('M d, Y', strtotime($r['certificate_released_at'])) ?></div>
                <?php elseif(!empty($r['scheduled_release_date'])): ?>
                  <span class="badge-tag" style="background:#ecfdf5; color:#065f46; font-weight:700;">
                    <?= date('M d, Y', strtotime($r['scheduled_release_date'])) ?>
                  </span>
                  <?php if(!empty($r['scheduled_release_time'])): ?>
                    <div style="font-size:0.75rem; color:#047857; margin-top:2px; font-weight:600;"><?= date('g:i A', strtotime($r['scheduled_release_time'])) ?></div>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:var(--text-muted); font-style:italic;">—</span>
                <?php endif; ?>
              </td>
              <td style="padding: 16px 12px; color: var(--text-muted);"><?= !empty($r['submitted_at']) ? date('M d, Y', strtotime($r['submitted_at'])) : '—' ?></td>
              <td style="padding: 16px 12px; display:flex; gap:8px; align-items:center;">
                <?php if ($isCurrentRow): ?>
                  <span style="color: var(--primary); font-weight: 800; font-size: 0.75rem; background: var(--primary-light); padding: 5px 10px; border-radius: 6px; border: 1px solid #bbf7d0;">Viewing Now</span>
                <?php else: ?>
                  <a href="<?= url('/user/services/burial-dashboard?id=' . urlencode($r['ref_id'])) ?>" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.8rem; background: var(--primary-light); padding: 5px 12px; border-radius: 6px;">Track</a>
                <?php endif; ?>
                <a href="<?= url('/user/services/burial-details?id=' . urlencode($r['ref_id'])) ?>" style="color: var(--text-muted); font-weight: 600; text-decoration: none; font-size: 0.8rem; border: 1px solid #d1d5db; padding: 4px 10px; border-radius: 6px;">Details</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- Details + Guidelines -->
  <div class="card details-card">
    <h3>Submitted Request Information</h3>
    <div class="info-grid">
      <div class="info-group">
        <span class="info-label">Deceased Name</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['deceased_name']) ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Date &amp; Place of Death</span>
        <span class="info-value"><?= !empty($activeBurial['date_of_death']) ? date('F d, Y', strtotime($activeBurial['date_of_death'])) : 'N/A' ?> • <?= htmlspecialchars($activeBurial['place_of_death'] ?: 'Not Specified') ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Applicant / Representative</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['informant_name'] ?? 'Family Member') ?> (<?= htmlspecialchars($activeBurial['relationship'] ?? 'Relative') ?>)</span>
      </div>
      <div class="info-group">
        <span class="info-label">Representative Contact</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['informant_contact'] ?? 'N/A') ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Service Scope</span>
        <span class="info-value" style="color:var(--primary); font-weight:700;">
          <?php if ($isCopyRequest): ?>
            Request Another Death Certificate (<?= (int)($activeBurial['num_copies'] ?? 1) ?> <?= (int)($activeBurial['num_copies'] ?? 1) > 1 ? 'copies' : 'copy' ?>)
          <?php else: ?>
            Death Certificate Registration
          <?php endif; ?>
        </span>
      </div>
      <?php if ($isCopyRequest): ?>
        <div class="info-group">
          <span class="info-label">Reason for Requesting Copy</span>
          <span class="info-value"><?= htmlspecialchars($activeBurial['copy_reason'] ?: 'Personal / Official Record') ?></span>
        </div>
        <div class="info-group">
          <span class="info-label">Existing Certificate / Registry No</span>
          <span class="info-value"><?= htmlspecialchars($activeBurial['existing_cert_no'] ?: 'Not Provided') ?></span>
        </div>
      <?php endif; ?>
      <div class="info-group">
        <span class="info-label">Date Submitted</span>
        <span class="info-value"><?= !empty($activeBurial['submitted_at']) ? date('F d, Y \a\t h:i A', strtotime($activeBurial['submitted_at'])) : 'N/A' ?></span>
      </div>
    </div>
  </div>
<?php endif; ?>

  <div class="card guidelines-card" style="grid-column: <?= $hasRequest ? 'span 4' : 'span 12' ?>;">
    <h3>Coordination Workflow</h3>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <div class="guide-text">
        <h5>1. Request Verification</h5>
        <p>Our Damayan team reviews details, hospital certificates, and valid representative credentials.</p>
      </div>
    </div>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
      <div class="guide-text">
        <h5>2. Death Certificate Processing</h5>
        <p>Staff coordinates with hospital liaisons and transmits to the Local Civil Registrar for official registration.</p>
      </div>
    </div>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg></div>
      <div class="guide-text">
        <h5>3. Certificate Release &amp; Claiming</h5>
        <p>Damayan Admin schedules the official claiming date and time, and releases the certified certificate to the authorized claimant.</p>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="action-row">
    <button class="btn-action btn-outline-act" onclick="window.print()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print Summary
    </button>
    <a href="<?= url('/user/services/burial-details?action=request') ?>" class="btn-action btn-primary-act" style="text-decoration:none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Submit New Request
    </a>
  </div>

</div>
</div>
</div>
</div>
</body>
</html>
