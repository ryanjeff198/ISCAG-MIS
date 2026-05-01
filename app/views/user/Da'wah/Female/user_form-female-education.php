<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Mock data for history and analytics (Should be fetched from controller in production)
$history = $history ?? [];
$analytics = $analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0];

$hasApproved = false;
$hasPending = false;
$activeRequest = null;
foreach ($history as $req) {
    if ($req['status'] === 'approved') { $hasApproved = true; $activeRequest = $req; }
    if ($req['status'] === 'pending') { $hasPending = true; $activeRequest = $req; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Education Section</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    :root {
      --primary-female: #D4AF37;
      --primary-female-dark: #B8860B;
      --primary-female-light: #FDF4E3;
    }

    .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { 
        background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: var(--primary-female); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-female-dark); }

    /* 📄 REGISTRATION SLIP STYLING */
    .slip-modal {
      display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;
    }
    .slip-content {
      background: #e2e8f0; width: 400px; padding: 30px; border-radius: 4px; position: relative;
      border: 1px solid #94a3b8; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      font-family: 'Source Sans 3', sans-serif; color: #1e40af;
    }
    .slip-header { text-align: center; margin-bottom: 20px; }
    .slip-logo { width: 60px; height: 60px; margin: 0 auto 10px; display: block; filter: grayscale(1) brightness(0.5) sepia(1) hue-rotate(190deg) saturate(3); }
    .slip-title { font-size: 0.85rem; font-weight: 800; margin-bottom: 4px; text-transform: uppercase; }
    .slip-subtitle { font-size: 0.95rem; font-weight: 900; margin-bottom: 2px; }
    .slip-batch { font-size: 0.85rem; font-weight: 800; }
    
    .slip-field { margin-bottom: 12px; display: flex; align-items: flex-end; gap: 8px; font-weight: 700; font-size: 0.9rem; }
    .slip-label { white-space: nowrap; }
    .slip-line { flex: 1; border-bottom: 1.5px solid #1e40af; height: 18px; }
    
    .slip-payment { margin-top: 20px; display: flex; flex-direction: column; gap: 12px; }
    .pay-item { display: flex; align-items: center; gap: 12px; font-weight: 800; font-size: 0.85rem; }
    .pay-box { width: 34px; height: 18px; border: 1.5px solid #1e40af; background: white; }
    
    .slip-footer { margin-top: 25px; border-top: 0px; }
    .sig-row { display: flex; align-items: flex-end; gap: 8px; font-weight: 700; font-size: 0.85rem; margin-bottom: 5px; }
    .approved-by { font-weight: 800; font-size: 0.8rem; margin-top: 5px; text-transform: uppercase; }

    .btn-print-slip {
      background: #1e40af; color: white; border: none; padding: 10px 20px; border-radius: 8px;
      font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-top: 20px; width: 100%; justify-content: center;
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'female_education'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Enroll in our Islamic education programs and classes for sisters</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Female Education</span>
      </div>

      <!-- ANALYTICS -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">Enrolled Courses</div>
          <div class="stat-value"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Enrollment</div>
          <div class="stat-value" style="color: #f59e0b;"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Credits Earned</div>
          <div class="stat-value" style="color: #10b981;">0</div>
        </div>
      </div>

      <?php if (!$hasPending && !$hasApproved): ?>
      <!-- ENROLLMENT FORM -->
      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="fill: var(--primary-female); width: 20px; height: 20px; margin-right: 8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5.89 12.55L12 15.89l6.11-3.34c.66-.36.89-1.19.53-1.85-.36-.66-1.19-.89-1.85-.53L12 12.5 7.21 9.88c-.66-.36-1.49-.13-1.85.53-.36.67-.13 1.5.53 1.86z"/></svg>
            New Enrollment Application
          </h6>
        </div>
        <div class="section-card-body">
            <form>
                <div class="form-section-title">Program Selection</div>
                <div class="form-grid cols-2">
                    <div>
                        <label class="form-label">Course / Program <span class="required">*</span></label>
                        <select class="form-select">
                            <option value="">— Select Program —</option>
                            <option>Basic Arabic & Tajweed</option>
                            <option>Fiqh for Women</option>
                            <option>Islamic History & Seerah</option>
                            <option>Hifdh (Quran Memorization)</option>
                            <option>Revert Support Class</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Learning Mode</label>
                        <select class="form-select">
                            <option>Physical (On-site)</option>
                            <option>Online (Zoom/Meets)</option>
                        </select>
                    </div>
                </div>
                
                <div style="margin-top: 24px;">
                    <label class="form-label">Why do you want to enroll in this program? <span class="required">*</span></label>
                    <textarea class="form-control" rows="3" placeholder="Tell us about your learning goals..."></textarea>
                </div>

                <div class="form-submit-row" style="margin-top: 32px;">
                    <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Cancel</button>
                    <button type="submit" class="btn-submit" style="background: var(--primary-female); color: #1a1a1a; font-weight: 800;">Submit Enrollment</button>
                </div>
            </form>
        </div>
      </div>
      <?php else: ?>
      <!-- 📢 PREMIUM STATUS HERO (Exact Sync with tenant_status.php) -->
      <div class="status-hero" style="background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div class="status-hero-top" style="background: linear-gradient(135deg, #D4AF37, #B8860B); padding: 28px 32px 24px; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; bottom: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.1);"></div>
            <div style="position: absolute; right: 100px; bottom: -30px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.05);"></div>
            
            <div class="status-hero-header" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; position: relative; z-index: 1;">
                <div class="status-hero-header-left" style="display: flex; align-items: center; gap: 16px;">
                    <div class="status-hero-avatar" style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: white;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                    </div>
                    <div>
                        <h5 class="status-hero-name" style="font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: #1a1a1a; margin: 0 0 2px;">Enrollment Application Status</h5>
                        <p class="status-hero-subtitle" style="font-size: 0.82rem; color: rgba(0, 0, 0, 0.6); margin: 0;">Ref No: <strong>#<?= $activeRequest['id'] ?? 'EDU-001' ?></strong> • Submitted on <?= isset($activeRequest['created_at']) ? date('M d, Y', strtotime($activeRequest['created_at'])) : 'Recently' ?></p>
                    </div>
                </div>
                <div class="status-badge <?= $hasPending ? 'pending' : 'approved' ?>" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; white-space: nowrap; backdrop-filter: blur(8px); background: rgba(255,255,255,0.2); color: #1a1a1a; border: 1px solid rgba(255,255,255,0.3);">
                    <div class="status-badge-dot" style="width: 7px; height: 7px; border-radius: 50%; background: currentColor;"></div>
                    <?= $hasPending ? 'Under Review' : 'Enrolled' ?>
                </div>
            </div>
        </div>
        <!-- Status Summary Bar -->
        <div class="status-summary" style="padding: 18px 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #fdfcf9; border-top: 1px solid var(--border);">
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Application ID</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #B8860B;">#<?= $activeRequest['id'] ?? 'EDU-001' ?></div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Service Type</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #B8860B;">Education</div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Current Phase</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #B8860B;"><?= $hasPending ? 'Verification' : 'Active' ?></div>
            </div>
        </div>
      </div>

      <!-- 🗓️ TIMELINE CARD (Exact Sync) -->
      <div class="timeline-card" style="background: white; border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, rgba(212, 175, 55, 0.05), transparent);">
            <div class="card-header-left" style="display: flex; align-items: center; gap: 10px;">
                <div class="card-header-icon" style="width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #D4AF37, #B8860B); display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 17px; height: 17px; fill: white;"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                </div>
                <h6 class="card-header-title" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #1a1a1a; margin: 0;">Application Timeline</h6>
            </div>
        </div>
        <div class="card-body" style="padding: 32px 24px;">
            <div class="timeline" style="display: flex; align-items: flex-start; gap: 0; position: relative;">
                <!-- 🛤️ Progress Line (Missing fixed) -->
                <div style="position: absolute; top: 18px; left: 0; right: 0; height: 3px; background: #e5e7eb; z-index: 1;"></div>
                <div style="position: absolute; top: 18px; left: 0; width: <?= $hasApproved ? '100%' : '50%' ?>; height: 3px; background: #D4AF37; z-index: 2; transition: width 1s ease;"></div>
                
                <div class="timeline-step completed" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #D4AF37; background: linear-gradient(135deg, #D4AF37, #B8860B); color: white; position: relative; z-index: 3;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #B8860B;">Submitted</span>
                </div>
                
                <div class="timeline-step <?= $hasPending ? 'active' : 'completed' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid <?= $hasPending ? '#f59e0b' : '#D4AF37' ?>; background: <?= $hasPending ? '#fff' : 'linear-gradient(135deg, #D4AF37, #B8860B)' ?>; color: <?= $hasPending ? '#f59e0b' : 'white' ?>; position: relative; z-index: 3;">
                        <?php if ($hasPending): ?>
                            <span style="font-size: 1rem; font-weight: 800;">2</span>
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        <?php endif; ?>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: <?= $hasPending ? '#f59e0b' : '#B8860B' ?>;">Verification</span>
                </div>

                <div class="timeline-step <?= $hasApproved ? 'completed' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid <?= $hasApproved ? '#D4AF37' : '#e5e7eb' ?>; background: <?= $hasApproved ? 'linear-gradient(135deg, #D4AF37, #B8860B)' : '#fff' ?>; color: <?= $hasApproved ? 'white' : '#9ca3af' ?>; position: relative; z-index: 3;">
                        <?php if ($hasApproved): ?>
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        <?php else: ?>
                            <span style="font-size: 0.9rem; font-weight: 700;">3</span>
                        <?php endif; ?>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: <?= $hasApproved ? '#B8860B' : '#9ca3af' ?>;">Enrollment</span>
                </div>
            </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- RECENT APPLICATIONS TABLE -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            Application History
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Service Type</th>
                  <th>Submitted Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="history-tbody">
                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No applications found.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // ── Render History ──
  const historyTbody = document.getElementById('history-tbody');
  const historyData = <?= json_encode($history ?? []) ?>;

  if (historyData.length > 0) {
    historyTbody.innerHTML = historyData.map(h => `
      <tr>
        <td class="td-id">#${h.id}</td>
        <td>${h.reason || 'Education Enrollment'}</td>
        <td>${new Date(h.created_at).toLocaleDateString()}</td>
        <td><span class="badge-status badge-${h.status}">${h.status}</span></td>
        <td>
          <button class="btn-view-doc" onclick="openSlip()" title="View Registration Slip" style="background:rgba(212,175,55,0.1); color:#B8860B; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:700; display:flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Print Slip
          </button>
        </td>
      </tr>
    `).join('');
  }

  // 📄 Slip Functions
  function openSlip() {
    document.getElementById('slip-modal').style.display = 'flex';
  }
  function closeSlip() {
    document.getElementById('slip-modal').style.display = 'none';
  }
  window.onclick = function(event) {
    const modal = document.getElementById('slip-modal');
    if (event.target == modal) closeSlip();
  }
</script>

<!-- 📄 REGISTRATION SLIP MODAL -->
<div id="slip-modal" class="slip-modal">
  <div class="slip-content">
    <div class="slip-header">
      <div style="width:64px; height:64px; background:#fff; border-radius:50%; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; border:1px solid #cbd5e1;">
        <svg viewBox="0 0 24 24" style="width:40px; height:40px; fill:#1e40af;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
      </div>
      <div class="slip-title">ISCAG PHILIPPINES – DAWAH FEMALE SECTION</div>
      <div class="slip-subtitle">REGISTRATION FEE</div>
      <div class="slip-batch">BATCH 08</div>
    </div>
    
    <div class="slip-body">
      <div class="slip-field">
        <span class="slip-label">NAME :</span>
        <div class="slip-line" id="slip-name"><?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?></div>
      </div>
      <div class="slip-field">
        <span class="slip-label">DATE :</span>
        <div class="slip-line" id="slip-date"><?= date('M d, Y') ?></div>
      </div>
      <div class="slip-field">
        <span class="slip-label">LEVEL :</span>
        <div class="slip-line" id="slip-level">Not Assigned</div>
      </div>
      
      <div class="slip-payment">
        <div class="pay-item">CASH <div class="pay-box"></div></div>
        <div class="pay-item">GCASH <div class="pay-box"></div></div>
      </div>
    </div>
    
    <div class="slip-footer">
      <div class="sig-row">
        <span class="slip-label">RECEIVED BY :</span>
        <div class="slip-line"></div>
      </div>
      <div class="approved-by">APPROVED BY : SISTER KHADIJAH DIMAANO</div>
    </div>

    <button class="btn-print-slip" onclick="window.print()">
      <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
      Print Official Slip
    </button>
    <button onclick="closeSlip()" style="margin-top:10px; background:none; border:none; color:#64748b; font-size:0.8rem; cursor:pointer; width:100%; text-align:center; font-weight:700;">Close Preview</button>
  </div>
</div>
</body>
</html>
