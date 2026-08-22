<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 5));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$dbUser = $dbUser ?? [];
$history = $history ?? [];

$hasApproved = false;
$hasPending = false;
$activeRequest = null;

foreach ($history as $req) {
    $st = strtolower($req['status'] ?? '');
    if ($st === 'approved') {
        $hasApproved = true;
        $activeRequest = $req;
        break;
    }
    if ($st === 'pending' && !$hasApproved) {
        $hasPending = true;
        $activeRequest = $req;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Conversion to Islam Registration</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    .form-step { display: none; }
    .form-step.active { display: block; animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    
    .step-indicator { display: flex; gap: 12px; margin-bottom: 32px; justify-content: center; }
    .step-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--border); transition: all 0.3s; }
    .step-dot.active { background: var(--primary); transform: scale(1.3); box-shadow: 0 0 10px rgba(15,92,58,0.3); }
    .step-dot.completed { background: var(--success); }

    .testimony-box {
      background: #fdfdfd;
      border: 1.5px dashed var(--primary-light);
      border-radius: 16px;
      padding: 32px;
      margin: 24px 0;
      text-align: center;
    }
    .shahada-text {
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 16px 0;
      line-height: 1.6;
    }
    .shahada-translation {
      font-style: italic;
      color: var(--text-muted);
      font-size: 0.95rem;
    }
    
    .section-divider { height: 1px; background: var(--border); margin: 24px 0; position: relative; }
    .section-divider::after { content: attr(data-label); position: absolute; top: -10px; left: 20px; background: white; padding: 0 10px; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }

    .other-field-wrap { 
      max-height: 0; 
      opacity: 0; 
      overflow: hidden; 
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
      margin-top: 0;
    }
    .other-field-wrap.show { 
      max-height: 80px; 
      opacity: 1; 
      margin-top: 12px;
    }

    /* ══════════════════════════════════════════════════════════ */
    /* 🌟 CERTIFICATE & APPOINTMENT HUB STYLES                   */
    /* ══════════════════════════════════════════════════════════ */
    .certificate-hub-card {
      background: #fff;
      border-radius: 20px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 32px;
    }
    .cert-hero-banner {
      background: linear-gradient(135deg, #0f3d24 0%, #166534 50%, #14532D 100%);
      padding: 36px 40px;
      color: #fff;
      position: relative;
      overflow: hidden;
    }
    .cert-hero-banner::before {
      content: '';
      position: absolute;
      top: -50px; right: -50px;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.12);
      pointer-events: none;
    }
    .cert-hero-top {
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 16px; margin-bottom: 20px;
    }
    .cert-badge-status {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(16, 185, 129, 0.2);
      border: 1px solid rgba(52, 211, 153, 0.4);
      color: #6ee7b7; padding: 6px 16px; border-radius: 30px;
      font-size: 0.8rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.08em;
    }
    .cert-badge-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: #34d399; box-shadow: 0 0 8px #34d399;
      animation: pulseDot 2s infinite;
    }
    @keyframes pulseDot { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: 0.7; } }

    .cert-adopted-banner {
      background: rgba(255, 255, 255, 0.08);
      border: 1.5px solid rgba(212, 175, 55, 0.35);
      border-radius: 14px;
      padding: 20px 24px;
      margin: 16px 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
    }
    .cert-adopted-name {
      font-family: 'Lora', serif;
      font-size: 1.6rem;
      font-weight: 700;
      color: #fde047;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .cert-tools-row {
      display: flex; gap: 12px; flex-wrap: wrap;
    }
    .btn-action-tool {
      display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px;
      border-radius: 10px; font-size: 0.85rem; font-weight: 700; cursor: pointer;
      text-decoration: none; transition: all 0.2s; border: none;
    }
    .btn-tool-print {
      background: linear-gradient(135deg, #D4AF37, #B8860B);
      color: #1a1a1a; font-weight: 800; box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
    }
    .btn-tool-print:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(212, 175, 55, 0.45); }
    
    .btn-tool-resources {
      background: rgba(255, 255, 255, 0.15); color: #fff;
      border: 1.5px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px);
    }
    .btn-tool-resources:hover { background: rgba(255, 255, 255, 0.25); color: #fff; }

    /* Modal for Printable Certificate */
    .cert-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center;
      padding: 20px;
    }
    .cert-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 680px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); max-height: 90vh; overflow-y: auto;
    }

    @media print {
      body * { visibility: hidden; }
      #printable-cert-area, #printable-cert-area * { visibility: visible; }
      #printable-cert-area { position: absolute; left: 0; top: 0; width: 100%; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'conversion_form'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Conversion to Islam Registration</div>
        <div class="top-bar-subtitle">Da'wah Department — Certificate of Conversion to Islam (OCRG Form No. 104)</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Conversion to Islam</span>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 🌟 CASE 1: CONVERSION APPROVED / CERTIFICATE ISSUED        -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php if ($hasApproved && $activeRequest): ?>
      <div class="certificate-hub-card">
        <div class="cert-hero-banner">
          <div class="cert-hero-top">
            <div>
              <span style="font-size:0.8rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#fde047;">Official Record • ISCAG Da'wah Department</span>
              <h3 style="font-family:'Lora',serif; font-size:1.8rem; font-weight:700; margin:4px 0 0; color:white;">Certificate of Conversion to Islam</h3>
            </div>
            <div class="cert-badge-status">
              <div class="cert-badge-dot"></div>
              Official Certificate Issued
            </div>
          </div>

          <div class="cert-adopted-banner">
            <div>
              <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:rgba(255,255,255,0.8); margin-bottom:2px;">Adopted Muslim Name</div>
              <div class="cert-adopted-name"><?= htmlspecialchars($activeRequest['adopted_name']) ?></div>
              <div style="font-size:0.85rem; color:rgba(255,255,255,0.85); margin-top:4px;">Legal Name: <strong><?= htmlspecialchars($activeRequest['fname'] . ' ' . $activeRequest['lname']) ?></strong> • Ref: <strong>#CV-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong></div>
            </div>
            <div class="cert-tools-row">
              <button type="button" class="btn-action-tool btn-tool-print" onclick="openCertModal()">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                Print Official Certificate
              </button>
              <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-action-tool btn-tool-resources">
                Islamic Learning Portal →
              </a>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; background:rgba(0,0,0,0.15); border-radius:12px; padding:16px 20px;">
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Date of Conversion</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= date('F d, Y', strtotime($activeRequest['conversion_date'])) ?></div>
            </div>
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Witness 1</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= htmlspecialchars($activeRequest['witness1_name'] ?: 'Official Da\'wah Officer') ?></div>
            </div>
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Witness 2</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= htmlspecialchars($activeRequest['witness2_name'] ?: 'Official Imam / Ustaz') ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- ⏳ CASE 2: PENDING REVIEW HERO                             -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php elseif ($hasPending && $activeRequest): ?>
      <div style="background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="background: linear-gradient(135deg, #14532D, #166534); padding: 28px 32px 24px; color: white;">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              </div>
              <div>
                <h5 style="font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: white; margin: 0 0 2px;">Conversion Registration Under Review</h5>
                <p style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.7); margin: 0;">Ref No: <strong>#CV-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Filed: <?= date('M d, Y', strtotime($activeRequest['created_at'])) ?></p>
              </div>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; background: rgba(245, 158, 11, 0.25); color: #fef08a; border: 1px solid rgba(253, 224, 71, 0.4);">
              Awaiting Verification & Certificate
            </div>
          </div>
        </div>

        <div style="padding: 18px 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #f9fafb; border-top: 1px solid var(--border);">
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Adopted Muslim Name</div>
            <div style="font-family: 'Lora', serif; font-size: 1rem; font-weight: 700; color: #14532D;"><?= htmlspecialchars($activeRequest['adopted_name']) ?></div>
          </div>
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Conversion Date</div>
            <div style="font-family: 'Lora', serif; font-size: 1rem; font-weight: 700; color: #14532D;"><?= date('M d, Y', strtotime($activeRequest['conversion_date'])) ?></div>
          </div>
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Application Status</div>
            <div style="font-family: 'Lora', serif; font-size: 1rem; font-weight: 700; color: #f59e0b;">Under Review</div>
          </div>
        </div>

        <div style="padding: 24px 32px; text-align: center; background: #fff;">
          <p style="font-size: 0.92rem; color: #4b5563; line-height: 1.6; max-width: 600px; margin: 0 auto 16px;">
            Your conversion registration and testimony details have been received. The Da'wah Department administration is verifying the record. Once verified, your official printable Certificate of Conversion will appear right here and on your dashboard.
          </p>
          <a href="<?= url('/user/dashboard') ?>" class="btn-submit" style="display:inline-block; padding:10px 24px; text-decoration:none;">Return to Dashboard</a>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📝 CASE 3: NO ACTIVE REQUEST -> REGISTRATION FORM          -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php else: ?>

      <div class="step-indicator" id="step-indicator">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
      </div>

      <form id="conversion-form" method="POST" onsubmit="return false;">
        <!-- STEP 1: PERSONAL & FAMILY INFORMATION -->
        <div class="form-step active" id="step-1">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;margin-right:8px;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Personal Information
              </h6>
            </div>
            <div class="section-card-body">
              <div class="form-grid cols-3">
                <div>
                  <label class="form-label">First Name <span class="required">*</span></label>
                  <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($dbUser['first_name'] ?? '') ?>" required />
                </div>
                <div>
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="mname" class="form-control" />
                </div>
                <div>
                  <label class="form-label">Last Name <span class="required">*</span></label>
                  <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($dbUser['last_name'] ?? '') ?>" required />
                </div>
              </div>

              <div class="form-grid cols-3">
                <div>
                  <label class="form-label">Sex <span class="required">*</span></label>
                  <input type="text" name="sex" class="form-control" value="Male" readonly style="background: #f8faf9; cursor: not-allowed;" />
                </div>
                <div>
                  <label class="form-label">Civil Status <span class="required">*</span></label>
                  <select name="civil_status" class="form-select" required>
                    <option value="">— Select —</option>
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widow/Widower</option>
                    <option>Divorced</option>
                  </select>
                </div>
                <div>
                  <label class="form-label">Citizenship <span class="required">*</span></label>
                  <input type="text" name="citizenship" class="form-control" value="Filipino" required />
                </div>
              </div>

              <div class="form-grid cols-3">
                <div>
                  <label class="form-label">Date of Birth <span class="required">*</span></label>
                  <input type="date" name="dob" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Age <span class="required">*</span></label>
                  <input type="number" name="age" class="form-control" min="0" required />
                </div>
                <div>
                  <label class="form-label">Occupation <span class="required">*</span></label>
                  <select name="occupation" id="occupation" class="form-select" required>
                    <option value="">— Select Occupation Type —</option>
                    <option>Student</option>
                    <option>Private Employee</option>
                    <option>Government Employee</option>
                    <option>Self-employed / Business Owner</option>
                    <option>Professional</option>
                    <option>Skilled / Technical Worker</option>
                    <option>Service / Sales Worker</option>
                    <option>Laborer / Agricultural Worker</option>
                    <option>Retired</option>
                    <option>Unemployed / Homemaker</option>
                    <option value="Others">Others</option>
                  </select>
                  <div id="occupation_other_wrap" class="other-field-wrap">
                    <input type="text" name="occupation_other" id="occupation_other" class="form-control" placeholder="Please specify..." />
                  </div>
                </div>
              </div>

              <div class="section-divider" data-label="Former Religion"></div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Former Religion / Sect <span class="required">*</span></label>
                  <select name="former_religion" id="former_religion" class="form-select" required>
                    <option value="">— Select —</option>
                    <option>Roman Catholic</option>
                    <option>Protestant</option>
                    <option>Iglesia ni Cristo</option>
                    <option>Seventh-day Adventist</option>
                    <option value="Others">Others</option>
                  </select>
                  <div id="former_religion_other_wrap" class="other-field-wrap">
                    <input type="text" name="former_religion_other" id="former_religion_other" class="form-control" placeholder="Please specify..." />
                  </div>
                </div>
              </div>

              <div class="section-divider" data-label="Place of Birth & Residence"></div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Place of Birth <span class="required">*</span></label>
                  <input type="text" name="pob" class="form-control" placeholder="Hospital/Clinic/Institution/Street, City, Province" required />
                </div>
                <div>
                  <label class="form-label">Present Residence <span class="required">*</span></label>
                  <input type="text" name="residence" class="form-control" placeholder="House No., St., Barangay, City, Province" required />
                </div>
              </div>

              <div class="section-divider" data-label="Family Information"></div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Name of Father <span class="required">*</span></label>
                  <input type="text" name="father_name" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Religion of Father <span class="required">*</span></label>
                  <input type="text" name="father_religion" class="form-control" required />
                </div>
              </div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Maiden Name of Mother <span class="required">*</span></label>
                  <input type="text" name="mother_name" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Religion of Mother <span class="required">*</span></label>
                  <input type="text" name="mother_religion" class="form-control" required />
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-submit next-btn" data-next="2">Next: Testimony & Adopted Name →</button>
          </div>
        </div>

        <!-- STEP 2: TESTIMONY, NAME, & WITNESSES -->
        <div class="form-step" id="step-2">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Testimony of Faith (Shahada)
              </h6>
            </div>
            <div class="section-card-body">
              <div class="testimony-box">
                <p style="font-size: 0.95rem; line-height: 1.6;">
                  I, <strong id="display-name" style="color:var(--primary-dark);">[Full Name]</strong>, do hereby willfully and willingly embrace Islam as my new religion. In evidence hereof, I hereby utter the testimony:
                </p>
                <div class="shahada-text">
                  ASH-HADU ALLA ILAHA ILLALLAH<br>
                  WA ASH-HADU ANNA MUHAMMADAR RASULULLAH
                </div>
                <p class="shahada-translation">
                  "I bear witness that there is no god but Allah,<br>
                  and I bear witness that Muhammad is the Messenger of Allah."
                </p>
              </div>

              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Adopted Muslim Name <span class="required">*</span></label>
                  <input type="text" name="adopted_name" class="form-control" placeholder="Enter your chosen Muslim name" required />
                </div>
                <div>
                  <label class="form-label">Date of Conversion <span class="required">*</span></label>
                  <input type="date" name="conversion_date" class="form-control" value="<?= date('Y-m-d') ?>" required />
                </div>
              </div>

              <div class="section-divider" data-label="Witnesses"></div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Witness 1 (Full Name) <span class="required">*</span></label>
                  <input type="text" name="witness1_name" class="form-control" required />
                  <input type="text" name="witness1_address" class="form-control" placeholder="Complete Address" style="margin-top:8px;" required />
                </div>
                <div>
                  <label class="form-label">Witness 2 (Full Name) <span class="required">*</span></label>
                  <input type="text" name="witness2_name" class="form-control" required />
                  <input type="text" name="witness2_address" class="form-control" placeholder="Complete Address" style="margin-top:8px;" required />
                </div>
              </div>

              <div class="section-divider" data-label="Declaration"></div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="declaration" required>
                <label class="form-check-label" for="declaration" style="font-size: 0.85rem; line-height: 1.6;">
                  I solemnly swear that I have embraced Islam of my own free will, without any compulsion or threat from anyone, and that the information provided in this form is true and correct.
                </label>
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-cancel prev-btn" data-prev="1">← Back</button>
            <button type="button" class="btn-submit" id="final-submit-btn" onclick="submitConversionForm()">Submit Conversion Registration</button>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📜 MY CONVERSION APPLICATIONS HISTORY                     -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="section-card" style="margin-top:32px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Conversion Records & History
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Adopted Muslim Name</th>
                  <th>Date of Conversion</th>
                  <th>Date Filed</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($history)): ?>
                  <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">No conversion registrations filed yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($history as $h): ?>
                    <tr>
                      <td class="td-id">#CV-<?= str_pad($h['id'], 4, '0', STR_PAD_LEFT) ?></td>
                      <td style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($h['adopted_name']) ?></td>
                      <td><?= date('M d, Y', strtotime($h['conversion_date'])) ?></td>
                      <td><?= date('M d, Y', strtotime($h['created_at'])) ?></td>
                      <td>
                        <?php if (strtolower($h['status']) === 'approved'): ?>
                          <span class="badge-status badge-approved" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;font-weight:700;">Certificate Issued</span>
                        <?php elseif (strtolower($h['status']) === 'pending'): ?>
                          <span class="badge-status badge-pending" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;font-weight:700;">Under Review</span>
                        <?php else: ?>
                          <span class="badge-status badge-rejected" style="font-weight:700;"><?= htmlspecialchars(ucfirst($h['status'])) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (strtolower($h['status']) === 'approved'): ?>
                          <button type="button" class="btn-view-doc" onclick="openCertModal()">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                            View Certificate
                          </button>
                        <?php else: ?>
                          <span style="font-size:0.8rem; color:var(--text-muted);">Awaiting Approval</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- 🎟️ PRINTABLE CERTIFICATE MODAL                             -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if ($activeRequest): ?>
<div class="cert-modal-backdrop" id="cert-modal" onclick="if(event.target===this) closeCertModal()">
  <div class="cert-modal-card">
    <div style="padding:20px 24px; background:#f8faf9; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;" class="no-print">
      <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark);">Official Certificate of Conversion</h5>
      <button type="button" onclick="closeCertModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280;">&times;</button>
    </div>

    <div id="printable-cert-area" style="padding:40px; background:#fff; text-align:center; position:relative; border:8px double #166534; margin:16px;">
      <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:8px;">
        <img src="<?= asset('assets/logo.jpg') ?>" style="width:50px; height:50px; border-radius:8px;" alt="ISCAG" />
        <div style="text-align:left;">
          <div style="font-family:'Lora',serif; font-size:1.15rem; font-weight:800; color:#14532D;">ISLAMIC STUDIES, CALL AND GUIDANCE OF THE PHILIPPINES</div>
          <div style="font-size:0.75rem; color:#4b5563; font-weight:600;">Office of the Da'wah & Educational Department</div>
        </div>
      </div>

      <div style="margin:24px 0 16px;">
        <h2 style="font-family:'Lora',serif; font-size:1.6rem; font-weight:700; color:#14532D; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 6px;">Certificate of Conversion to Islam</h2>
        <div style="font-size:0.8rem; font-weight:700; color:#B8860B; text-transform:uppercase; letter-spacing:0.12em;">Official Registration Ref: #CV-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></div>
      </div>

      <p style="font-size:0.92rem; color:#374151; line-height:1.8; margin-bottom:20px;">
        This is to certify that brother <strong><?= htmlspecialchars($activeRequest['fname'] . ' ' . ($activeRequest['mname'] ? $activeRequest['mname'] . ' ' : '') . $activeRequest['lname']) ?></strong>, of legal age, Filipino citizen, residing at <strong><?= htmlspecialchars($activeRequest['residence']) ?></strong>, has willfully and sincerely embraced the religion of Islam on <strong><?= date('F d, Y', strtotime($activeRequest['conversion_date'])) ?></strong> and has chosen as his Muslim name:
      </p>

      <div style="background:#f0fdf4; border:2px solid #bbf7d0; border-radius:12px; padding:16px; margin:20px 0; font-family:'Lora',serif; font-size:1.6rem; font-weight:800; color:#14532D;">
        <?= htmlspecialchars($activeRequest['adopted_name']) ?>
      </div>

      <p style="font-size:0.88rem; color:#4b5563; line-height:1.6; margin-bottom:32px;">
        He has pronounced the Shahadah (Testimony of Faith) in the presence of competent witnesses and is hereby officially welcomed into the universal brotherhood of Islam.
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-top:40px; text-align:center;">
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">Ustaz / Da'wah Director</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Da'wah Department</div>
        </div>
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">President / Authorized Official</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Philippines</div>
        </div>
      </div>
    </div>

    <div style="padding:16px 24px; background:#f8faf9; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;" class="no-print">
      <button type="button" onclick="closeCertModal()" class="btn-cancel">Close</button>
      <button type="button" onclick="window.print()" class="btn-submit" style="background:#166534;">Print Document</button>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
  // ── Multi-step Logic ──
  document.querySelectorAll('.next-btn').forEach(btn => {
    btn.onclick = () => {
      const current = btn.closest('.form-step');
      const nextId = 'step-' + btn.getAttribute('data-next');
      const next = document.getElementById(nextId);
      
      // Basic validation for current step
      const inputs = current.querySelectorAll('input[required], select[required]');
      let valid = true;
      inputs.forEach(i => { if(!i.value) { i.classList.add('is-invalid'); valid = false; } else { i.classList.remove('is-invalid'); } });
      
      if(valid) {
        // Sync name to testimony
        const fn = document.querySelector('input[name="fname"]').value;
        const ln = document.querySelector('input[name="lname"]').value;
        const disp = document.getElementById('display-name');
        if(disp) disp.textContent = fn + ' ' + ln;

        current.classList.remove('active');
        next.classList.add('active');
        updateDots(btn.getAttribute('data-next'));
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        showToast('Please fill in all required fields.', '#e67e22');
      }
    };
  });

  document.querySelectorAll('.prev-btn').forEach(btn => {
    btn.onclick = () => {
      const current = btn.closest('.form-step');
      const prevId = 'step-' + btn.getAttribute('data-prev');
      const prev = document.getElementById(prevId);
      current.classList.remove('active');
      prev.classList.add('active');
      updateDots(btn.getAttribute('data-prev'));
    };
  });

  function updateDots(step) {
    const dots = document.querySelectorAll('.step-dot');
    dots.forEach((dot, idx) => {
      dot.classList.remove('active', 'completed');
      if (idx + 1 < step) dot.classList.add('completed');
      if (idx + 1 == step) dot.classList.add('active');
    });
  }

  // ── Toggle Other Religion ──
  const relSelect = document.getElementById('former_religion');
  if(relSelect) {
    const relWrap = document.getElementById('former_religion_other_wrap');
    const relInput = document.getElementById('former_religion_other');
    relSelect.onchange = () => {
      if (relSelect.value === 'Others') {
        relWrap.classList.add('show');
        relInput.required = true;
        setTimeout(() => relInput.focus(), 300);
      } else {
        relWrap.classList.remove('show');
        relInput.required = false;
        setTimeout(() => { if(!relWrap.classList.contains('show')) relInput.value = ''; }, 400);
      }
    };
  }

  // ── Toggle Other Occupation ──
  const occSelect = document.getElementById('occupation');
  if(occSelect) {
    const occWrap = document.getElementById('occupation_other_wrap');
    const occInput = document.getElementById('occupation_other');
    occSelect.onchange = () => {
      if (occSelect.value === 'Others') {
        occWrap.classList.add('show');
        occInput.required = true;
        setTimeout(() => occInput.focus(), 300);
      } else {
        occWrap.classList.remove('show');
        occInput.required = false;
        setTimeout(() => { if(!occWrap.classList.contains('show')) occInput.value = ''; }, 400);
      }
    };
  }

  // ── Submit Conversion Form ──
  function submitConversionForm() {
    const decl = document.getElementById('declaration');
    if(decl && !decl.checked) {
      showToast('Please check the declaration box before submitting.', '#e67e22');
      return;
    }

    const form = document.getElementById('conversion-form');
    const formData = new FormData(form);
    const payload = {};
    formData.forEach((val, key) => payload[key] = val);

    if(!payload.fname || !payload.lname || !payload.adopted_name) {
      showToast('Please complete required fields: Name and Adopted Muslim Name.', '#e67e22');
      return;
    }

    const submitBtn = document.getElementById('final-submit-btn');
    if(submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting Registration...';
    }

    fetch('<?= url('/user/services/conversion/submit') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        showToast('Conversion registration submitted successfully!', '#166534');
        setTimeout(() => { window.location.reload(); }, 1200);
      } else {
        if(submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Conversion Registration';
        }
        showToast(data.message || 'Error submitting registration.', '#8b2e2e');
      }
    })
    .catch(err => {
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Conversion Registration';
      }
      showToast('Connection error. Please try again.', '#8b2e2e');
    });
  }

  function openCertModal() {
    const m = document.getElementById('cert-modal');
    if(m) m.style.display = 'flex';
  }
  function closeCertModal() {
    const m = document.getElementById('cert-modal');
    if(m) m.style.display = 'none';
  }

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + (bg || 'var(--primary)') + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:inherit;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;animation:fadeIn 0.3s ease;';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
  }
</script>
</body>
</html>
