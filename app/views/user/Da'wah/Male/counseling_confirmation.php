<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Guest', 'Tenant', 'Admin', 'Staff_Male', 'Staff_Female']);

$reqId = $request['id'] ?? 0;
$refNo = '#MC-' . str_pad((string)$reqId, 4, '0', STR_PAD_LEFT);
$applicantName = trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')) ?: 'Valued Brother';
$status = strtolower($request['status'] ?? 'pending');
$statusLabels = [
    'pending' => 'Pending Review',
    'approved' => 'Confirmed / Approved',
    'rescheduled' => 'New Schedule Proposed',
    'completed' => 'Completed',
    'no_show' => 'No-Show',
    'rejected' => 'Disapproved'
];
$statusColors = [
    'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fde68a'],
    'approved' => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#bbf7d0'],
    'rescheduled' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'border' => '#c7d2fe'],
    'completed' => ['bg' => '#f3f4f6', 'text' => '#374151', 'border' => '#e5e7eb'],
    'no_show' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fecaca'],
    'rejected' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fecaca']
];
$stColor = $statusColors[$status] ?? $statusColors['pending'];
$stLabel = $statusLabels[$status] ?? ucfirst($status);

$dateFormatted = !empty($request['preferred_date']) ? date('l, F j, Y', strtotime($request['preferred_date'])) : 'Pending Counselor Scheduling';
$timeFormatted = !empty($request['preferred_time']) ? htmlspecialchars($request['preferred_time']) : 'Flexible Slot';
$sessionTypeLabel = (($request['session_type'] ?? 'in_person') === 'virtual') ? 'Virtual Online Session' : 'In-Person Consultation (ISCAG Center)';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Confirmation — Guidance &amp; Counseling | ISCAG MIS</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>?v=<?= time() ?>">
  <style>
    :root {
      --primary: #14532D;
      --primary-dark: #0f3d21;
      --primary-light: #1e7e43;
      --accent: #D4AF37;
      --accent-light: #faeac0;
      --bg-page: #f8fafc;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
    }
    body {
      font-family: 'Source Sans 3', sans-serif;
      background: var(--bg-page);
      color: var(--text-main);
      margin: 0;
      padding: 0;
    }
    .confirmation-container {
      max-width: 820px;
      margin: 0 auto;
      padding: 32px 20px 60px;
    }
    .conf-card {
      background: #ffffff;
      border-radius: 20px;
      border: 1px solid rgba(20,83,45,0.12);
      box-shadow: 0 10px 30px rgba(15,23,42,0.06);
      overflow: hidden;
    }
    .conf-hero {
      background: linear-gradient(135deg, #14532D 0%, #0f3d21 60%, #092614 100%);
      color: #ffffff;
      padding: 44px 32px 36px;
      text-align: center;
      position: relative;
    }
    .conf-hero::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #D4AF37, #fef08a, #D4AF37);
    }
    .conf-icon-wrap {
      width: 76px;
      height: 76px;
      background: rgba(255,255,255,0.12);
      border: 2px solid rgba(212,175,55,0.4);
      border-radius: 50%;
      margin: 0 auto 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fef08a;
    }
    .conf-hero h1 {
      font-family: 'Lora', serif;
      font-size: 1.85rem;
      margin: 0 0 8px;
      font-weight: 700;
      letter-spacing: -0.01em;
    }
    .conf-hero p {
      margin: 0 auto;
      font-size: 1.05rem;
      color: #e2e8f0;
      max-width: 600px;
      line-height: 1.5;
    }
    .conf-body {
      padding: 36px 32px 40px;
    }
    .ref-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 16px 20px;
      margin-bottom: 28px;
    }
    .ref-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .ref-lbl {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }
    .ref-code {
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-copy {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 3px 8px;
      font-size: 0.72rem;
      font-weight: 600;
      color: #334155;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-copy:hover {
      background: #e2e8f0;
      color: #0f172a;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      background: <?= $stColor['bg'] ?>;
      color: <?= $stColor['text'] ?>;
      border: 1px solid <?= $stColor['border'] ?>;
    }
    .pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: currentColor;
      box-shadow: 0 0 0 0 rgba(22,101,52,0.4);
      animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22,101,52,0.5); }
      70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(22,101,52,0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(22,101,52,0); }
    }
    .details-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 28px;
    }
    @media (max-width: 600px) {
      .details-grid { grid-template-columns: 1fr; }
    }
    .detail-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 18px;
    }
    .detail-card-lbl {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .detail-card-val {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-main);
    }
    .timeline-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 14px;
      padding: 20px;
      margin-bottom: 32px;
    }
    .timeline-title {
      font-family: 'Lora', serif;
      font-weight: 700;
      font-size: 1.05rem;
      color: var(--primary-dark);
      margin: 0 0 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .timeline-steps {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }
    @media (max-width: 640px) {
      .timeline-steps { grid-template-columns: 1fr; }
    }
    .step-item {
      background: #ffffff;
      border: 1px solid #dcfce7;
      border-radius: 10px;
      padding: 12px;
    }
    .step-num {
      width: 22px;
      height: 22px;
      background: var(--primary);
      color: white;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.72rem;
      font-weight: 700;
      margin-bottom: 6px;
    }
    .step-title {
      font-weight: 700;
      font-size: 0.85rem;
      color: var(--primary-dark);
      margin-bottom: 4px;
    }
    .step-desc {
      font-size: 0.75rem;
      color: var(--text-muted);
      line-height: 1.4;
    }
    .actions-bar {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }
    .btn-main-action {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--primary);
      color: #ffffff;
      padding: 12px 28px;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(20,83,45,0.25);
      transition: all 0.2s;
    }
    .btn-main-action:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(20,83,45,0.32);
    }
    .btn-sec-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ffffff;
      color: #334155;
      padding: 12px 22px;
      border-radius: 12px;
      border: 1px solid #cbd5e1;
      font-size: 0.95rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-sec-action:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      color: #0f172a;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/user/sidebar.php'; ?>

    <div class="main-content">
      <!-- Top Navigation Bar -->
      <header class="topbar">
        <div class="topbar-left">
          <h1 class="page-title">Appointment Confirmation</h1>
          <span class="page-subtitle">Male Da'wah &amp; Islamic Guidance Department</span>
        </div>
        <div class="topbar-right">
          <div class="user-profile">
            <span class="user-name"><?= htmlspecialchars($applicantName) ?></span>
            <span class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? 'Member') ?></span>
          </div>
        </div>
      </header>

      <div class="content-area">
        <div class="confirmation-container">
          <div class="conf-card">
            
            <!-- Hero Banner -->
            <div class="conf-hero">
              <div class="conf-icon-wrap">
                <svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </div>
              <h1>Counseling Request Received!</h1>
              <p>JazakAllahu Khairan. Your appointment request has been safely logged with our Guidance and Counseling office. Our qualified counselors will review your schedule shortly.</p>
            </div>

            <!-- Body Details -->
            <div class="conf-body">
              
              <!-- Reference & Status Bar -->
              <div class="ref-bar">
                <div class="ref-item">
                  <span class="ref-lbl">Appointment Reference Number</span>
                  <div class="ref-code">
                    <span><?= htmlspecialchars($refNo) ?></span>
                    <button class="btn-copy" onclick="copyRef('<?= htmlspecialchars($refNo) ?>')">Copy Ref</button>
                  </div>
                </div>
                <div class="ref-item" style="align-items:flex-end;">
                  <span class="ref-lbl">Current Appointment Status</span>
                  <div class="status-badge">
                    <span class="pulse-dot"></span>
                    <span><?= htmlspecialchars($stLabel) ?></span>
                  </div>
                </div>
              </div>

              <!-- Request Details Grid -->
              <div class="details-grid">
                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    Topic / Primary Concern
                  </div>
                  <div class="detail-card-val"><?= htmlspecialchars($request['reason'] ?? 'General Guidance') ?></div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                    Requested Schedule
                  </div>
                  <div class="detail-card-val"><?= $dateFormatted ?></div>
                  <div style="font-size:0.82rem; color:var(--text-muted); margin-top:2px;">Slot: <?= $timeFormatted ?></div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    Consultation Format
                  </div>
                  <div class="detail-card-val"><?= htmlspecialchars($sessionTypeLabel) ?></div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    Applicant
                  </div>
                  <div class="detail-card-val"><?= htmlspecialchars($applicantName) ?></div>
                  <div style="font-size:0.82rem; color:var(--text-muted); margin-top:2px;"><?= htmlspecialchars($request['email'] ?? '') ?></div>
                </div>
              </div>

              <?php if (!empty($request['description'])): ?>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:28px;">
                  <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); margin-bottom:4px;">Concern Summary Provided</div>
                  <div style="font-size:0.92rem; color:#334155; line-height:1.5;"><?= nl2br(htmlspecialchars($request['description'])) ?></div>
                </div>
              <?php endif; ?>

              <!-- Roadmap Box -->
              <div class="timeline-box">
                <div class="timeline-title">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--primary)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
                  What to Expect Next
                </div>
                <div class="timeline-steps">
                  <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-title">Counselor Review</div>
                    <div class="step-desc">An authorized Da'wah counselor reviews your concern and schedules an available room or link.</div>
                  </div>
                  <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-title">Confirmation Alert</div>
                    <div class="step-desc">You'll receive an in-app notification confirming the time slot or proposing an alternative.</div>
                  </div>
                  <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-title">Attend Session</div>
                    <div class="step-desc">Access your official Consultation Pass in the portal to attend your confidential session.</div>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="actions-bar">
                <a href="<?= url('/user/services/counseling/male') ?>#appointments-history" class="btn-main-action" id="btn-view-requests">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  View My Requests
                </a>
                <a href="<?= url('/user/services/counseling/male') ?>" class="btn-sec-action">
                  Back to Counseling Portal
                </a>
                <a href="<?= url('/user/dashboard') ?>" class="btn-sec-action">
                  User Dashboard
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function copyRef(code) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
          alert('Reference number copied: ' + code);
        });
      } else {
        alert('Reference number: ' + code);
      }
    }
  </script>
</body>
</html>
