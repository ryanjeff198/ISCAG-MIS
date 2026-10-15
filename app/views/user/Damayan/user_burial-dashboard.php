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

require_once BASE_PATH . '/app/models/FuneralCase.php';
$fcModel = new FuneralCase();
$userCases = $fcModel->getByTenantId($_SESSION['user_id'] ?? '');
$activeCase = !empty($userCases) ? $userCases[0] : null;

// Use the most recent request as the "active" one for the dashboard summary
$activeBurial = !empty($allRequests) ? $allRequests[0] : [
    'ref_id' => 'NONE',
    'status' => 'No active request',
    'submitted_at' => null,
    'deceased_name' => 'N/A',
    'date_of_death' => null,
    'place_of_death' => 'N/A',
    'religion' => 'N/A',
    'relationship' => 'N/A',
    'ghusl_status' => 'Pending',
    'kafan_status' => 'Pending',
    'janazah_status' => 'Pending',
    'cemetery_status' => 'Pending'
];

$rawStatus = $activeBurial['status'] ?? 'No active request';
$st = strtolower($rawStatus);
$hasRequest = !empty($activeBurial['ref_id']) && $activeBurial['ref_id'] !== 'NONE';

$isInfoRequired = ($st === 'additional information required');
$isPending = in_array($st, ['new request', 'pending', 'under review', 'additional information required']);
$isAccepted = in_array($st, [
    'accepted for coordination', 'pending volunteer assignment', 'volunteers confirmed',
    'ghusl scheduled', 'ghusl completed', 'kafan prepared', 'janazah scheduled',
    'janazah completed', 'transportation arranged', 'burial scheduled', 'burial completed', 'approved', 'verified', 'arrived'
]);
$isRitesActive = in_array($st, [
    'ghusl scheduled', 'ghusl completed', 'kafan prepared', 'janazah scheduled',
    'janazah completed', 'transportation arranged', 'burial scheduled', 'burial completed'
]);
$isCompleted = ($st === 'burial completed' || $st === 'completed');
$isRejected = ($st === 'rejected');

// Calculate progress tracker fill width
$progressWidth = '0%';
if ($hasRequest) {
    if ($isCompleted) {
        $progressWidth = '100%';
    } elseif ($isRitesActive) {
        $progressWidth = '75%';
    } elseif ($isAccepted) {
        $progressWidth = '50%';
    } elseif ($isPending) {
        $progressWidth = '25%';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ISCAG — Damayan Burial Dashboard</title>
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

  /* Progress Tracker */
  .progress-card{grid-column:span 12;padding:30px;}
  .progress-card h3{margin:0 0 16px;font-size:1.1rem;font-weight:700;}
  .progress-tracker{display:flex;justify-content:space-between;align-items:flex-start;margin-top:24px;position:relative;padding:0 10px;}
  .progress-line{position:absolute;top:14px;left:20px;right:20px;height:2px;background:#e5e7eb;z-index:1;}
  .progress-line-fill{position:absolute;top:14px;left:20px;height:2px;background:var(--primary);z-index:2;transition:width .5s ease;width:<?= $progressWidth ?>;}
  .step{display:flex;flex-direction:column;align-items:center;position:relative;z-index:3;gap:10px;width:100px;}
  .step-icon{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--white);border:2px solid #e5e7eb;color:#9ca3af;font-weight:700;font-size:.8rem;transition:.3s;}
  .step.completed .step-icon{background:var(--primary);border-color:var(--primary);color:var(--white);}
  .step.active .step-icon{border-color:var(--primary);color:var(--primary);box-shadow:0 0 0 4px var(--primary-light);}
  .step-label{font-size:.78rem;font-weight:700;color:var(--text-main);text-align:center;line-height:1.3;}
  .step-date{font-size:.7rem;color:var(--text-muted);}

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
  .services-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;}
  .service-item{display:flex;align-items:center;gap:12px;padding:16px;border-radius:12px;background:#f8fafc;border:1px solid var(--border);}
  .service-check{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .service-check.included{background:var(--success-light);color:var(--success);}
  .service-check.pending{background:#fffbeb;color:var(--gold);}
  .service-name{font-size:.88rem;font-weight:600;color:var(--text-main);}

  /* Action Row */
  .action-row{grid-column:span 12;display:flex;gap:16px;justify-content:flex-end;flex-wrap:wrap;}
  .btn-action{padding:12px 28px;border-radius:12px;font-weight:700;font-size:.88rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;border:none;}
  .btn-action:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08);}
  .btn-primary-act{background:var(--primary);color:var(--white);}
  .btn-outline-act{background:var(--white);color:var(--primary);border:2px solid var(--primary);}
  .btn-outline-act:hover{background:var(--primary);color:var(--white);}

  @media(max-width:900px){
    .summary-row,.services-grid{grid-template-columns:1fr;}
    .details-card,.guidelines-card,.services-card{grid-column:span 12;}
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
        <div class="top-bar-title">Burial Dashboard</div>
        <div class="top-bar-subtitle">Damayan Services</div>
    </div>
    <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Main Dashboard</a>
    </div>
</div>

<div class="burial-wrapper">

<div class="greeting">
  <h1>Assalamu Alaikum, <?= $name ?></h1>
  <p>May Allah grant you strength. Here is the status of your burial service request.</p>
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
        Please review the instructions and submit the required information to continue Islamic funeral coordination.
      </div>
    </div>
  </div>
  <div>
    <a href="<?= url('/user/services/burial-details?id=' . urlencode($activeBurial['ref_id'])) ?>" class="btn-action" style="background:#b45309; color:white; text-decoration:none; padding:10px 22px; font-size:0.85rem;">
      View &amp; Submit Requirements →
    </a>
  </div>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="grid-container">

  <div class="summary-row">
    <div class="summary-card active">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div class="summary-info">
        <h4>Reference ID</h4>
        <div class="val"><?= htmlspecialchars($activeBurial['ref_id']) ?></div>
        <p>Keep this for tracking</p>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div class="summary-info">
        <h4>Deceased</h4>
        <div class="val"><?= htmlspecialchars($activeBurial['deceased_name']) ?></div>
        <p>Death: <?= !empty($activeBurial['date_of_death']) ? date('M d, Y', strtotime($activeBurial['date_of_death'])) : 'N/A' ?></p>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-icon" style="background:<?= $isCompleted ? 'var(--success-light)' : ($isAccepted ? '#e0f2fe' : '#fffbeb') ?>;color:<?= $isCompleted ? 'var(--success)' : ($isAccepted ? '#0284c7' : 'var(--gold)') ?>;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="summary-info">
        <h4>Status</h4>
        <?php if ($isCompleted): ?>
          <div class="val" style="color:var(--success);">Completed</div>
          <p>Burial Rites Fulfilled</p>
        <?php elseif ($isAccepted): ?>
          <div class="val" style="color:#0284c7;"><?= htmlspecialchars($rawStatus) ?></div>
          <p>Coordination in Progress</p>
        <?php elseif ($isInfoRequired): ?>
          <div class="val" style="color:#b45309;">Info Required</div>
          <p>Action needed from you</p>
        <?php elseif ($isRejected): ?>
          <div class="val" style="color:#dc2626;">Rejected</div>
          <p>Please contact Damayan</p>
        <?php else: ?>
          <div class="val" style="color:var(--gold);">Under Review</div>
          <p>Processing request</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Progress Tracker -->
  <div class="card progress-card">
    <h3>Burial Preparation Progress</h3>
    <div class="progress-tracker">
      <div class="progress-line"></div>
      <div class="progress-line-fill"></div>
      
      <!-- Step 1: Submitted -->
      <div class="step <?= $hasRequest ? 'completed' : '' ?>">
        <div class="step-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div>
        <div class="step-label">Submitted</div>
        <div class="step-date"><?= !empty($activeBurial['submitted_at']) ? date('M d', strtotime($activeBurial['submitted_at'])) : '—' ?></div>
      </div>

      <!-- Step 2: Under Review -->
      <div class="step <?= $isAccepted || $isCompleted ? 'completed' : ($hasRequest ? 'active' : '') ?>">
        <div class="step-icon">
          <?php if ($isAccepted || $isCompleted): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <?php else: ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div class="step-label">Under<br>Review</div>
        <div class="step-date"><?= $isPending ? 'Current' : '' ?></div>
      </div>

      <!-- Step 3: Accepted for Coordination -->
      <div class="step <?= $isCompleted ? 'completed' : ($isAccepted ? 'active' : '') ?>">
        <div class="step-icon">
          <?php if ($isCompleted): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <?php elseif ($isAccepted): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div class="step-label">Coordination<br>Accepted</div>
        <div class="step-date"><?= $isAccepted && !$isRitesActive && !$isCompleted ? 'Current' : '' ?></div>
      </div>

      <!-- Step 4: Ghusl & Janazah -->
      <div class="step <?= $isCompleted ? 'completed' : ($isRitesActive ? 'active' : '') ?>">
        <div class="step-icon">
          <?php if ($isCompleted): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <?php elseif ($isRitesActive): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div class="step-label">Ghusl &amp;<br>Janazah</div>
        <div class="step-date"><?= $isRitesActive && !$isCompleted ? 'Active' : '' ?></div>
      </div>

      <!-- Step 5: Burial Completed -->
      <div class="step <?= $isCompleted ? 'completed' : '' ?>">
        <div class="step-icon">
          <?php if ($isCompleted): ?>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          <?php endif; ?>
        </div>
        <div class="step-label">Burial<br>Completed</div>
        <div class="step-date"><?= $isCompleted ? 'Completed' : '' ?></div>
      </div>
    </div>
  </div>

  <!-- Services Checklist -->
  <div class="card services-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
      <h3 style="margin:0;">Islamic Funeral Services Status</h3>
      <span style="font-size:0.8rem; font-weight:700; color:var(--primary); background:var(--primary-light); padding:4px 12px; border-radius:100px;">According to Quran &amp; Sunnah</span>
    </div>
    <div class="services-grid">
      <?php
        $ghuslDone = in_array(strtolower($activeBurial['ghusl_status'] ?? ''), ['completed']);
        $kafanDone = in_array(strtolower($activeBurial['kafan_status'] ?? ''), ['ready', 'completed']);
        $janazahDone = in_array(strtolower($activeBurial['janazah_status'] ?? ''), ['completed']);
        $cemeteryDone = in_array(strtolower($activeBurial['cemetery_status'] ?? ''), ['completed']) || $isCompleted;
      ?>
      <div class="service-item">
        <div class="service-check <?= $ghuslDone ? 'included' : 'pending' ?>">
          <?php if ($ghuslDone): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="service-name">Bathing (Ghusl)</div>
          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($activeBurial['ghusl_status'] ?? 'Pending') ?></div>
        </div>
      </div>

      <div class="service-item">
        <div class="service-check <?= $kafanDone ? 'included' : 'pending' ?>">
          <?php if ($kafanDone): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="service-name">Shrouding (Kafn)</div>
          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($activeBurial['kafan_status'] ?? 'Pending') ?></div>
        </div>
      </div>

      <div class="service-item">
        <div class="service-check <?= $janazahDone ? 'included' : 'pending' ?>">
          <?php if ($janazahDone): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="service-name">Janazah Prayer</div>
          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($activeBurial['janazah_status'] ?? 'Pending') ?></div>
        </div>
      </div>

      <div class="service-item">
        <div class="service-check <?= $cemeteryDone ? 'included' : 'pending' ?>">
          <?php if ($cemeteryDone): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <?php endif; ?>
        </div>
        <div>
          <div class="service-name">Cemetery &amp; Burial</div>
          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($activeBurial['cemetery_status'] ?? 'Pending') ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Request History Table -->
  <div class="card services-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h3 style="margin: 0;">My Burial Requests</h3>
      <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); background: var(--bg); padding: 4px 12px; border-radius: 100px;">Total: <?= count($allRequests) ?></span>
    </div>
    <div style="overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
        <thead>
          <tr style="border-bottom: 2px solid var(--border); text-align: left;">
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Ref ID</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Deceased Name</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Relationship</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Date Submitted</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Status</th>
            <th style="padding: 12px; color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($allRequests)): ?>
            <tr><td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted);">No requests found. Click 'Submit New Request' below to request burial assistance.</td></tr>
          <?php else: ?>
            <?php foreach($allRequests as $r): ?>
            <tr style="border-bottom: 1px solid var(--border);">
              <td style="padding: 16px 12px; font-weight: 700; color: var(--primary);">#<?= htmlspecialchars($r['ref_id']) ?></td>
              <td style="padding: 16px 12px; font-weight: 600;"><?= htmlspecialchars($r['deceased_name']) ?></td>
              <td style="padding: 16px 12px;"><?= htmlspecialchars($r['relationship'] ?? 'Relative') ?></td>
              <td style="padding: 16px 12px; color: var(--text-muted);"><?= !empty($r['submitted_at']) ? date('M d, Y', strtotime($r['submitted_at'])) : '—' ?></td>
              <td style="padding: 16px 12px;">
                <?php 
                  $rst = strtolower($r['status'] ?? '');
                  if ($rst === 'burial completed' || $rst === 'completed') {
                      $sc = 'var(--success-light)'; $tc = 'var(--success)';
                  } elseif ($rst === 'additional information required') {
                      $sc = '#fef3c7'; $tc = '#b45309';
                  } elseif ($rst === 'rejected') {
                      $sc = '#fee2e2'; $tc = '#dc2626';
                  } elseif (in_array($rst, ['new request', 'pending', 'under review'])) {
                      $sc = '#fffbeb'; $tc = 'var(--gold)';
                  } else {
                      $sc = '#e0f2fe'; $tc = '#0284c7';
                  }
                ?>
                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: <?= $sc ?>; color: <?= $tc ?>;">
                  <?= htmlspecialchars($r['status']) ?>
                </span>
              </td>
              <td style="padding: 16px 12px;">
                <a href="<?= url('/user/services/burial-details?id=' . urlencode($r['ref_id'])) ?>" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.85rem;">View Details &amp; Tracking</a>
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
        <span class="info-label">Date of Death</span>
        <span class="info-value"><?= !empty($activeBurial['date_of_death']) ? date('F d, Y', strtotime($activeBurial['date_of_death'])) : 'N/A' ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Place of Death</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['place_of_death']) ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Religion</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['religion'] ?? 'Islam') ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Relationship to Deceased</span>
        <span class="info-value"><?= htmlspecialchars($activeBurial['relationship'] ?? 'Family Member') ?></span>
      </div>
      <div class="info-group">
        <span class="info-label">Date Submitted</span>
        <span class="info-value"><?= !empty($activeBurial['submitted_at']) ? date('F d, Y \a\t h:i A', strtotime($activeBurial['submitted_at'])) : 'N/A' ?></span>
      </div>
    </div>
  </div>

  <div class="card guidelines-card">
    <h3>What happens next?</h3>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <div class="guide-text">
        <h5>1. Request Verification</h5>
        <p>Our Damayan team reviews the details and medical/death records promptly.</p>
      </div>
    </div>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
      <div class="guide-text">
        <h5>2. Islamic Rites Coordination</h5>
        <p>We coordinate gender-appropriate volunteers for Ghusl, Kafan shroud, and Salat al-Janazah with the Imam.</p>
      </div>
    </div>
    <div class="guide-item">
      <div class="guide-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
      <div class="guide-text">
        <h5>3. Ambulance &amp; Cemetery</h5>
        <p>Transport to the Islamic cemetery is scheduled immediately according to the Sunnah.</p>
      </div>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="action-row">
    <button class="btn-action btn-outline-act" onclick="window.print()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print Record
    </button>
    <a href="<?= url('/user/services/burial-form?action=request') ?>" class="btn-action btn-primary-act" style="text-decoration:none;">
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
