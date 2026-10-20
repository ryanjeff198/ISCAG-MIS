<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Guest', 'Tenant', 'Admin', 'Staff_Male', 'Staff_Female']);

$reqId = $request['id'] ?? 0;
$refNo = '#MC-' . str_pad((string)$reqId, 4, '0', STR_PAD_LEFT);
$applicantName = trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')) ?: 'Valued Brother';
$status = strtolower($request['status'] ?? 'pending');

$statusLabels = [
    'pending' => 'Pending Review',
    'under_review' => 'Under Review',
    'approved_awaiting_schedule' => 'Approved (Awaiting Schedule)',
    'confirmed' => 'Appointment Confirmed',
    'approved' => 'Appointment Confirmed',
    'completed' => 'Completed',
    'no_show' => 'No-Show',
    'rejected' => 'Disapproved',
    'cancelled' => 'Cancelled'
];

$statusColors = [
    'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fde68a'],
    'under_review' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'border' => '#c7d2fe'],
    'approved_awaiting_schedule' => ['bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0'],
    'confirmed' => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#bbf7d0'],
    'approved' => ['bg' => '#dcfce7', 'text' => '#166534', 'border' => '#bbf7d0'],
    'completed' => ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#cbd5e1'],
    'no_show' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fecaca'],
    'rejected' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fecaca'],
    'cancelled' => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'border' => '#e5e7eb']
];

$stColor = $statusColors[$status] ?? $statusColors['pending'];
$stLabel = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

$dateSubmitted = !empty($request['created_at']) ? date('F j, Y — g:i A', strtotime($request['created_at'])) : date('F j, Y');
$preferredSchedule = (!empty($request['preferred_date']) ? date('l, F j, Y', strtotime($request['preferred_date'])) : 'Flexible Date') 
                     . (!empty($request['preferred_time']) ? ' (' . htmlspecialchars($request['preferred_time']) . ')' : '');
$preferredSessionType = htmlspecialchars($request['session_type'] ?? 'In-Person');

// Step index for 3-step Pending Timeline
// Step 1: Request Submitted (always done)
// Step 2: Under Review
// Step 3: Awaiting Confirmation
$step1Done = true;
$step2Active = ($status === 'under_review');
$step2Done = in_array($status, ['under_review', 'approved_awaiting_schedule', 'confirmed', 'approved']);
$step3Active = in_array($status, ['approved_awaiting_schedule', 'pending']);
$step3Done = in_array($status, ['confirmed', 'approved', 'completed']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Counseling Request Status — ISCAG MIS</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>?v=<?= time() ?>">
  <style>
    :root {
      --primary: #14532D;
      --primary-dark: #0f3d21;
      --primary-light: #f0fdf4;
      --accent: #D4AF37;
      --bg-page: #f8fafc;
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
    .status-container {
      max-width: 820px;
      margin: 0 auto;
      padding: 32px 20px 60px;
    }
    .status-card {
      background: #ffffff;
      border-radius: 20px;
      border: 1px solid rgba(20,83,45,0.15);
      box-shadow: 0 10px 30px rgba(15,23,42,0.06);
      overflow: hidden;
    }
    .status-hero {
      background: linear-gradient(135deg, #14532D 0%, #0f3d21 60%, #092614 100%);
      color: #ffffff;
      padding: 40px 32px 34px;
      text-align: center;
      position: relative;
    }
    .status-hero::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, #D4AF37, #fef08a, #D4AF37);
    }
    .status-icon-wrap {
      width: 72px; height: 72px;
      background: rgba(255,255,255,0.12);
      border: 2px solid rgba(212,175,55,0.45);
      border-radius: 50%;
      margin: 0 auto 16px;
      display: flex; align-items: center; justify-content: center;
      color: #fef08a;
    }
    .status-hero h1 {
      font-family: 'Lora', serif;
      font-size: 1.85rem;
      margin: 0 0 10px;
      font-weight: 700;
    }
    .status-hero p {
      margin: 0 auto;
      font-size: 1.05rem;
      color: #e2e8f0;
      max-width: 620px;
      line-height: 1.6;
    }
    .status-body {
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
    .ref-item { display: flex; flex-direction: column; gap: 4px; }
    .ref-lbl {
      font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; color: var(--text-muted);
    }
    .ref-code {
      font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 700;
      color: var(--primary); display: flex; align-items: center; gap: 8px;
    }
    .btn-copy {
      background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px;
      padding: 3px 8px; font-size: 0.72rem; font-weight: 600; color: #334155;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-copy:hover { background: #e2e8f0; color: #0f172a; }
    .status-pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 6px 14px; border-radius: 30px; font-size: 0.85rem; font-weight: 700;
      background: <?= $stColor['bg'] ?>; color: <?= $stColor['text'] ?>; border: 1px solid <?= $stColor['border'] ?>;
    }
    .pulse-dot {
      width: 8px; height: 8px; border-radius: 50%; background: currentColor;
    }

    /* ── 3-Step Simple Status Timeline ── */
    .timeline-wrapper {
      background: #fdfdfd;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 24px 20px;
      margin-bottom: 30px;
    }
    .timeline-heading {
      font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 20px; text-align: center;
    }
    .steps-row {
      display: flex;
      justify-content: space-between;
      position: relative;
    }
    .steps-row::before {
      content: '';
      position: absolute;
      top: 18px; left: 12%; right: 12%;
      height: 3px;
      background: #e2e8f0;
      z-index: 1;
    }
    .step-col {
      position: relative;
      z-index: 2;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      flex: 1;
    }
    .step-circle {
      width: 38px; height: 38px; border-radius: 50%;
      background: #f1f5f9; border: 2px solid #cbd5e1;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 0.88rem; color: #64748b;
      margin-bottom: 8px; transition: all 0.2s;
    }
    .step-col.completed .step-circle {
      background: var(--primary); border-color: var(--primary); color: #fff;
    }
    .step-col.active .step-circle {
      background: #fff; border-color: #f59e0b; color: #d97706; box-shadow: 0 0 0 4px rgba(245,158,11,0.2);
    }
    .step-title-txt {
      font-size: 0.84rem; font-weight: 700; color: #334155; margin-bottom: 2px;
    }
    .step-desc-txt {
      font-size: 0.72rem; color: var(--text-muted); max-width: 140px;
    }

    /* ── Details Grid ── */
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
      background: #ffffff; border: 1px solid #e2e8f0;
      border-radius: 12px; padding: 14px 18px;
    }
    .detail-card-lbl {
      font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .detail-card-val {
      font-size: 0.98rem; font-weight: 700; color: var(--text-main);
    }

    .confidential-notice {
      background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;
      padding: 14px 18px; font-size: 0.85rem; color: #166534; line-height: 1.5;
      margin-bottom: 30px; display: flex; align-items: flex-start; gap: 10px;
    }
    .confidential-notice svg { flex-shrink: 0; width: 18px; height: 18px; fill: currentColor; margin-top: 1px; }

    .actions-bar {
      display: flex; align-items: center; justify-content: center;
      gap: 14px; flex-wrap: wrap;
    }
    .btn-main-action {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--primary); color: #ffffff; padding: 12px 28px;
      border-radius: 12px; font-size: 0.95rem; font-weight: 700; text-decoration: none;
      box-shadow: 0 4px 14px rgba(20,83,45,0.25); transition: all 0.2s;
    }
    .btn-main-action:hover {
      background: var(--primary-dark); transform: translateY(-1px);
    }
    .btn-sec-action {
      display: inline-flex; align-items: center; gap: 6px;
      background: #ffffff; color: #334155; padding: 12px 22px;
      border-radius: 12px; border: 1px solid #cbd5e1; font-size: 0.95rem;
      font-weight: 600; text-decoration: none; transition: all 0.2s;
    }
    .btn-sec-action:hover {
      background: #f8fafc; border-color: #94a3b8; color: #0f172a;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/user/sidebar.php'; ?>

    <div class="main-content">
      <header class="topbar">
        <div class="topbar-left">
          <h1 class="page-title">Counseling Request Status</h1>
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
        <div class="status-container">
          <div class="status-card">
            
            <!-- Hero Heading & Required Message -->
            <div class="status-hero">
              <div class="status-icon-wrap">
                <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </div>
              <h1>Your Counseling Request Has Been Submitted</h1>
              <p>
                "Thank you for reaching out to the Counseling and Guidance service. Your request is now pending review by our administrator. You will be notified once your request has been reviewed."
              </p>
            </div>

            <!-- Body Details -->
            <div class="status-body">
              
              <!-- Reference Bar -->
              <div class="ref-bar">
                <div class="ref-item">
                  <span class="ref-lbl">Request Reference Number</span>
                  <div class="ref-code">
                    <span><?= htmlspecialchars($refNo) ?></span>
                    <button class="btn-copy" onclick="copyRef('<?= htmlspecialchars($refNo) ?>')">Copy Ref</button>
                  </div>
                </div>
                <div class="ref-item" style="align-items:flex-end;">
                  <span class="ref-lbl">Current Request Status</span>
                  <div class="status-pill">
                    <span class="pulse-dot"></span>
                    <span><?= htmlspecialchars($stLabel) ?></span>
                  </div>
                </div>
              </div>

              <!-- 3-Step Simple Status Timeline (While Pending/Review) -->
              <div class="timeline-wrapper">
                <div class="timeline-heading">Request Progress Timeline</div>
                <div class="steps-row">
                  <!-- Step 1 -->
                  <div class="step-col completed">
                    <div class="step-circle">✓</div>
                    <div class="step-title-txt">Request Submitted</div>
                    <div class="step-desc-txt"><?= date('M d, Y', strtotime($request['created_at'])) ?></div>
                  </div>
                  <!-- Step 2 -->
                  <div class="step-col <?= $step2Done ? 'completed' : ($step2Active ? 'active' : '') ?>">
                    <div class="step-circle"><?= $step2Done ? '✓' : '2' ?></div>
                    <div class="step-title-txt">Under Review</div>
                    <div class="step-desc-txt">Administrator Evaluation</div>
                  </div>
                  <!-- Step 3 -->
                  <div class="step-col <?= $step3Done ? 'completed' : ($step3Active ? 'active' : '') ?>">
                    <div class="step-circle"><?= $step3Done ? '✓' : '3' ?></div>
                    <div class="step-title-txt">Awaiting Confirmation</div>
                    <div class="step-desc-txt">Schedule &amp; Counselor Finalization</div>
                  </div>
                </div>
              </div>

              <!-- Request Details Grid -->
              <div class="details-grid">
                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    Submission Date
                  </div>
                  <div class="detail-card-val"><?= $dateSubmitted ?></div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    Preferred Session Type
                  </div>
                  <div class="detail-card-val"><?= $preferredSessionType ?> Consultation</div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                    Preferred Schedule
                  </div>
                  <div class="detail-card-val"><?= $preferredSchedule ?></div>
                </div>

                <div class="detail-card">
                  <div class="detail-card-lbl">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--primary)"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    Primary Concern / Reason
                  </div>
                  <div class="detail-card-val"><?= htmlspecialchars($request['reason'] ?? 'General Guidance') ?></div>
                </div>
              </div>

              <!-- Reassurance Notice -->
              <div class="confidential-notice">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <div>
                  <strong>Appointment Scheduling Note:</strong> Your preferred schedule is a preference and is currently awaiting administrator review. An authorized counselor and final meeting room/link will be assigned upon confirmation. You will receive an in-app notification immediately once confirmed.
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="actions-bar">
                <a href="<?= url('/user/dashboard') ?>" class="btn-sec-action">
                  ← Return to Dashboard
                </a>
                <a href="<?= url('/user/services/counseling/male') ?>" class="btn-main-action">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  View Counseling Details &amp; History
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
          showCopyToast('Reference number copied: ' + code);
        }).catch(() => {
          showCopyToast('Reference number: ' + code);
        });
      } else {
        showCopyToast('Reference number: ' + code);
      }
    }

    function showCopyToast(msg) {
      const existing = document.getElementById('copy-toast-el');
      if (existing) existing.remove();
      const toast = document.createElement('div');
      toast.id = 'copy-toast-el';
      toast.textContent = msg;
      toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#14532D;color:#ffffff;padding:12px 20px;border-radius:12px;font-weight:700;font-size:0.88rem;box-shadow:0 8px 24px rgba(0,0,0,0.18);z-index:99999;transition:all 0.3s ease;transform:translateY(10px);opacity:0;';
      document.body.appendChild(toast);
      requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
      });
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
      }, 2500);
    }
  </script>
</body>
</html>
