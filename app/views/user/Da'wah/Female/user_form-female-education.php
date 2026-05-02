<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Fetch dynamic user data
require_once BASE_PATH . '/app/models/User.php';
$userModel = new User();
$dbUser = $userModel->findById($_SESSION['user_id']) ?: [];
$extraInfo = $userModel->getAdditionalInfo($_SESSION['user_id']) ?: [];
$dbUser = array_merge($dbUser, $extraInfo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Education Enrollment</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700;800&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-female: #D4AF37;
      --primary-female-dark: #B8860B;
      --primary-female-light: #fdfcf4;
      --text-main: #1f2937;
      --text-muted: #6b7280;
      --border: #e5e7eb;
      --success: #16a34a;
      --warning: #f59e0b;
    }

    body { font-family: 'Source Sans 3', sans-serif; background: #f9fafb; color: var(--text-main); }

    /* ─── BREADCRUMBS ─── */
    .breadcrumb-row { display: flex; align-items: center; gap: 8px; padding: 24px 0; font-size: 0.85rem; color: var(--text-muted); }
    .breadcrumb-row a { color: var(--primary-female-dark); text-decoration: none; font-weight: 600; }
    .breadcrumb-row .sep { color: #d1d5db; }

    /* ─── STEPPER (3 STEPS) ─── */
    .form-stepper {
        display: flex; justify-content: space-between; padding: 30px 60px; background: white;
        border-bottom: 1px solid var(--border); border-radius: 16px 16px 0 0; position: sticky; top: 0; z-index: 100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .step-item { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
    .step-item:not(:last-child)::after { content: ''; position: absolute; top: 18px; left: 50%; width: 100%; height: 2px; background: var(--border); z-index: 1; }
    .step-item.active:not(:last-child)::after, .step-item.completed:not(:last-child)::after { background: var(--primary-female); }
    .step-circle {
        width: 36px; height: 36px; border-radius: 50%; background: white; border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 700;
        color: var(--text-muted); position: relative; z-index: 2; transition: 0.4s;
    }
    .step-item.active .step-circle { background: var(--primary-female); border-color: var(--primary-female); color: white; box-shadow: 0 0 0 5px rgba(212, 175, 55, 0.15); transform: scale(1.1); }
    .step-item.completed .step-circle { background: var(--primary-female); border-color: var(--primary-female); color: white; }
    .step-label { margin-top: 12px; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .step-item.active .step-label { color: var(--primary-female-dark); }
    .step-item.completed .step-label { color: var(--primary-female-dark); }

    /* ─── VIEW STAGES ─── */
    .view-stage { display: none; max-width: 1000px; margin: 0 auto; }
    .view-stage.active { display: block; animation: viewSlideUp 0.5s ease; }
    @keyframes viewSlideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    /* ─── FORM DOCUMENT ─── */
    .document-card { background: white; border-radius: 0 0 16px 16px; border: 1px solid var(--border); border-top: none; box-shadow: 0 10px 40px rgba(0,0,0,0.03); }
    .doc-header { padding: 50px 60px; border-bottom: 1px solid #e5e7eb; text-align: center; }
    .doc-logo { width: 80px; height: 80px; margin-bottom: 20px; }
    .doc-org { font-size: 1.1rem; font-weight: 700; color: var(--primary-female-dark); text-transform: uppercase; letter-spacing: 1px; }
    .doc-title { font-size: 2.2rem; font-family: 'Lora', serif; font-weight: 700; color: #111827; margin-top: 10px; }
    .doc-body { padding: 60px; }
    .grid-sys { display: grid; grid-template-columns: repeat(12, 1fr); gap: 30px; margin-bottom: 30px; }
    .field-box { display: flex; flex-direction: column; gap: 8px; }
    .field-tag { font-weight: 600; font-size: 0.72rem; color: #6b7280; text-transform: uppercase; }
    .field-line { width: 100%; border: none; border-bottom: 2px solid #d1d5db; background: transparent; color: #111827; font-weight: 500; font-size: 1.1rem; padding-bottom: 8px; outline: none; transition: border-color 0.3s; }
    .field-line:focus { border-color: var(--primary-female); }
    select.field-line { appearance: none; cursor: pointer; }

    .sub-head { font-weight: 700; font-size: 0.9rem; color: var(--primary-female-dark); margin: 50px 0 30px; display: flex; align-items: center; gap: 20px; text-transform: uppercase; }
    .sub-head::after { content: ""; flex: 1; height: 1.5px; background: #e5e7eb; }

    /* ─── CHECKBOXES ─── */
    .check-wrap { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .check-box-inner { width: 22px; height: 22px; border: 2.5px solid #d1d5db; border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .check-wrap input { display: none; }
    .check-wrap input:checked + .check-box-inner { background: var(--primary-female); border-color: var(--primary-female); }
    .check-box-inner::after { content: "✓"; color: white; font-size: 12px; font-weight: 900; display: none; }
    .check-wrap input:checked + .check-box-inner::after { display: block; }

    /* ─── SLIP ─── */
    .slip-container { padding: 60px; text-align: center; background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
    .slip-doc { max-width: 550px; margin: 40px auto; background: #fff; border: 2.5px dashed #cbd5e1; padding: 45px; text-align: left; position: relative; border-radius: 16px; }

    /* ─── GCASH INLINE (INSIDE SLIP) ─── */
    .integrated-gcash { 
        display: none; margin-top: 25px; padding-top: 25px; border-top: 1.5px solid #f1f5f9;
        animation: fadeIn 0.4s ease;
    }
    .integrated-gcash.active { display: flex; align-items: flex-start; gap: 25px; }
    .gcash-left { flex: 1; }
    .gcash-right { flex: 0 0 130px; text-align: center; }
    .ref-input-integrated { width: 100%; border: 1.5px solid var(--primary-female); border-radius: 6px; padding: 10px; font-weight: 800; font-size: 1rem; color: var(--text-main); outline: none; transition: 0.3s; margin-top: 5px; }
    .ref-input-integrated:focus { box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); }
    .qr-img-integrated { width: 100%; border-radius: 8px; border: 2px solid #f1f5f9; margin-top: 5px; }

    /* ─── STATUS NOTIFICATION ─── */
    .status-container { padding: 80px 60px; text-align: center; background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
    .status-icon { width: 90px; height: 90px; background: #f0fdf4; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; border: 3px solid #16a34a; animation: bounceIn 0.6s ease; }
    @keyframes bounceIn { 0% { opacity:0; transform: scale(0.3); } 50% { opacity:1; transform: scale(1.05); } 70% { transform: scale(0.9); } 100% { transform: scale(1); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .btn-finish { background: linear-gradient(135deg, var(--primary-female), var(--primary-female-dark)); color: white; border: none; padding: 20px 60px; border-radius: 14px; font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3); text-transform: uppercase; }

    @media print {
        .app-wrapper, .top-bar, .form-stepper, .breadcrumb-row, .btn-finish, a, #final-submit-area { display: none !important; }
        .view-stage { display: block !important; margin: 0; padding: 0; }
        .integrated-gcash.active { display: flex !important; }
        .document-card, .slip-container { border: none; box-shadow: none; }
        .slip-doc { border: 3px solid black; margin: 0; width: 100%; max-width: 100%; border-radius: 0; padding: 30px; }
        body { background: white; padding: 0; margin: 0; }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <?php 
    $active_page = 'female_education'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <div class="main-content">
    <div class="top-bar">
      <div class="top-bar-left">
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Digital Admission & Enrollment Portal</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Cancel Enrollment</a>
      </div>
    </div>

    <div class="page-body">
      <div class="max-content-width" style="max-width: 1000px; margin: 0 auto;">
          <div class="breadcrumb-row">
            <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
            <span class="sep">›</span>
            <span class="current">Female Education Enrollment</span>
          </div>

          <!-- ═══ 3-STEP STEPPER ═══ -->
          <div class="form-stepper">
            <div class="step-item active" id="node-1"><div class="step-circle">1</div><div class="step-label">Form</div></div>
            <div class="step-item" id="node-2"><div class="step-circle">2</div><div class="step-label">Fee Slip</div></div>
            <div class="step-item" id="node-3"><div class="step-circle">3</div><div class="step-label">Status</div></div>
          </div>

          <!-- ═══════════════════════════════════ -->
          <!-- STAGE 1: REGISTRATION FORM          -->
          <!-- ═══════════════════════════════════ -->
          <div id="stage-form" class="view-stage active">
            <div class="document-card">
              <div class="doc-header">
                <img src="<?= asset('assets/da\'wah logo.jpg') ?>" class="doc-logo" alt="Logo">
                <div class="doc-org">ISCAG PHILIPPINES – DA'WAH FEMALE SECTION</div>
                <div class="doc-title">REGISTRATION FORM</div>
              </div>
              <div class="doc-body">
                <form id="enrollmentForm">
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 5;"><span class="field-tag">Last Name *</span><input type="text" name="lname" class="field-line" value="<?= htmlspecialchars($dbUser['last_name'] ?? '') ?>" required></div>
                    <div class="field-box" style="grid-column: span 5;"><span class="field-tag">First Name *</span><input type="text" name="fname" class="field-line" value="<?= htmlspecialchars($dbUser['first_name'] ?? '') ?>" required></div>
                    <div class="field-box" style="grid-column: span 2;"><span class="field-tag">M.I.</span><input type="text" name="mi" class="field-line"></div>
                  </div>
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 6;"><span class="field-tag">Muslim Name</span><input type="text" name="mname" class="field-line" value="<?= htmlspecialchars($dbUser['muslimname'] ?? '') ?>"></div>
                    <div class="field-box" style="grid-column: span 6; justify-content: flex-end;">
                      <div style="display: flex; gap: 25px;">
                        <label class="check-wrap"><input type="checkbox" id="is_revert" onclick="toggleMutex('is_revert', 'is_born')"><div class="check-box-inner"></div> <span style="font-size:0.8rem; font-weight:800; color:#4b5563;">REVERT</span></label>
                        <label class="check-wrap"><input type="checkbox" id="is_born" onclick="toggleMutex('is_born', 'is_revert')"><div class="check-box-inner"></div> <span style="font-size:0.8rem; font-weight:800; color:#4b5563;">BORN-MUSLIM</span></label>
                      </div>
                    </div>
                  </div>
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 6;"><span class="field-tag">Date of Birth</span><input type="date" name="dob" class="field-line" value="<?= htmlspecialchars($dbUser['birthdate'] ?? '') ?>"></div>
                    <div class="field-box" style="grid-column: span 6;"><span class="field-tag">Date of Shahadah</span><input type="date" name="shahadah" class="field-line" value="<?= htmlspecialchars($dbUser['dateofshahadah'] ?? '') ?>"></div>
                  </div>
                  <div class="grid-sys"><div class="field-box" style="grid-column: span 12;"><span class="field-tag">Residential Address</span><input type="text" name="addr" class="field-line" value="<?= htmlspecialchars($dbUser['address'] ?? '') ?>"></div></div>
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 6;"><span class="field-tag">Phone Number *</span><input type="text" name="phone" class="field-line" value="<?= htmlspecialchars($dbUser['contactnum'] ?? '') ?>" required></div>
                    <div class="field-box" style="grid-column: span 6;"><span class="field-tag">Facebook</span><input type="text" name="fb" class="field-line"></div>
                  </div>
                  <div class="sub-head">Emergency Contact</div>
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 7;"><span class="field-tag">Contact Name</span><input type="text" name="e_name" class="field-line" required></div>
                    <div class="field-box" style="grid-column: span 5;">
                        <span class="field-tag">Relationship</span>
                        <select name="e_rel" class="field-line" required>
                            <option value="" disabled selected>Select Relationship</option>
                            <option value="Parent">Parent</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Guardian">Guardian</option>
                            <option value="Relative">Relative</option>
                            <option value="Friend">Friend</option>
                        </select>
                    </div>
                  </div>
                  <div class="sub-head">Academic Program</div>
                  <div class="grid-sys">
                    <div class="field-box" style="grid-column: span 12;">
                        <span class="field-tag">Program of Interest *</span>
                        <select name="program_name" class="field-line" required>
                            <option value="" disabled selected>Choose a Program</option>
                            <option value="Beginners Qur'an (B4)">Beginners Qur'an (B4)</option>
                            <option value="Intermediate Islamic Studies">Intermediate Islamic Studies</option>
                            <option value="Tajweed Mastery">Tajweed Mastery</option>
                            <option value="Tahfidhul Qur'an">Tahfidhul Qur'an</option>
                            <option value="Arabic Language">Arabic Language</option>
                            <option value="Other Programs">Other Programs</option>
                        </select>
                    </div>
                  </div>
                  <div style="margin-top: 60px; text-align: center;">
                    <button type="submit" class="btn-finish">Continue to Fee Slip →</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- ═══════════════════════════════════ -->
          <!-- STAGE 2: FEE SLIP                   -->
          <!-- ═══════════════════════════════════ -->
          <div id="stage-slip" class="view-stage">
            <div class="slip-container">
              <h2 style="font-family:'Lora',serif; font-size:1.8rem; color:#111827;">Review & Finalize Slip</h2>
              <p style="color:#6b7280; margin-top:8px;">Select your payment mode, then click Submit.</p>

              <div class="slip-doc" id="printable-slip">
                <div style="text-align:center; margin-bottom:30px;">
                  <img src="<?= asset('assets/da\'wah logo.jpg') ?>" style="width:50px; margin-bottom:10px;">
                  <div style="font-size:0.75rem; font-weight:900; color:var(--primary-female-dark); text-transform:uppercase;">ISCAG Philippines – Da'wah Female Section</div>
                  <div style="font-size:1.1rem; font-weight:800; color:#1e293b; margin-top:5px;">REGISTRATION FEE SLIP</div>
                </div>

                <div style="margin-bottom:20px;">
                  <span class="field-tag" style="font-size:0.65rem;">Full Name</span>
                  <div style="font-size:1.2rem; font-weight:800; color:#1e293b; border-bottom:1.5px solid #f1f5f9; padding-bottom:5px;" id="out-name">---</div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom:25px;">
                  <div><span class="field-tag" style="font-size:0.65rem;">Date Submitted</span><div style="font-size:1rem; font-weight:800; color:#1e293b; border-bottom:1.5px solid #f1f5f9; padding-bottom:5px;"><?= date('M d, Y') ?></div></div>
                  <div><span class="field-tag" style="font-size:0.65rem;">Status</span><div style="font-size:1rem; font-weight:900; color:var(--warning); border-bottom:1.5px solid #f1f5f9; padding-bottom:5px;">PENDING</div></div>
                </div>

                <div style="margin-top:20px;">
                  <span class="field-tag" style="font-size:0.65rem;">Payment Mode</span>
                  <div style="display: flex; gap: 25px; margin-top:8px;">
                    <label class="check-wrap"><input type="checkbox" id="slip-chk-cash" checked onclick="selectPayment('cash')"><div class="check-box-inner"></div> <span style="font-size:0.75rem; font-weight:800; color:#4b5563;">CASH</span></label>
                    <label class="check-wrap"><input type="checkbox" id="slip-chk-gcash" onclick="selectPayment('gcash')"><div class="check-box-inner"></div> <span style="font-size:0.75rem; font-weight:800; color:#4b5563;">GCASH</span></label>
                  </div>
                </div>

                <!-- GCash inline: ref input LEFT, QR code RIGHT -->
                <div id="integrated-gcash-box" class="integrated-gcash">
                  <div class="gcash-left">
                    <span class="field-tag" style="font-size:0.6rem; color:#111827;">GCash Reference Number *</span>
                    <input type="text" id="gcash-ref-input" class="ref-input-integrated" placeholder="13-digit Ref No.">
                  </div>
                  <div class="gcash-right">
                    <span class="field-tag" style="font-size:0.55rem;">Scan to Pay</span>
                    <img src="<?= asset('assets/gcash_qr_dawah_female.png') ?>" class="qr-img-integrated" alt="QR">
                  </div>
                </div>
              </div>

              <div style="margin-top: 40px;">
                <button class="btn-finish" style="background:#1e293b;" id="btnSubmitRegistration">Submit Registration</button>
              </div>
            </div>
          </div>

          <!-- ═══════════════════════════════════ -->
          <!-- STAGE 3: STATUS NOTIFICATION         -->
          <!-- ═══════════════════════════════════ -->
          <div id="stage-status" class="view-stage">
            <div class="status-container">
              <div class="status-icon">
                <svg viewBox="0 0 24 24" style="width:40px; height:40px; fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              </div>
              <h2 style="font-family:'Lora',serif; font-size:2.2rem; color:#111827; margin-bottom:15px;">Registration Submitted!</h2>
              <p style="color:#6b7280; max-width:480px; margin:0 auto 40px; line-height:1.7;">
                Alhamdulillah! Your enrollment application has been successfully submitted and is now <strong style="color:var(--warning);">PENDING</strong> for verification by the Da'wah Female Section.
              </p>
              <p style="color:#6b7280; max-width:480px; margin:0 auto 40px; line-height:1.7; font-size:0.85rem;">
                Once approved, you will be able to view the <strong>School Schedule</strong> and class activities.
              </p>
              <div style="margin-top:40px; display:flex; gap:15px; justify-content:center; flex-wrap:wrap;">
                <a href="<?= url('/user/services/education/female/school') ?>" class="btn-finish" style="text-decoration:none; display:inline-block;">View School Schedule</a>
                <a href="<?= url('/user/dashboard') ?>" class="btn-finish" style="background:#1e293b; text-decoration:none; display:inline-block;">Back to Dashboard</a>
              </div>
            </div>
          </div>

      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

  // ─── Elements ───
  var form          = document.getElementById('enrollmentForm');
  var stageForm     = document.getElementById('stage-form');
  var stageSlip     = document.getElementById('stage-slip');
  var stageStatus   = document.getElementById('stage-status');
  var node1         = document.getElementById('node-1');
  var node2         = document.getElementById('node-2');
  var node3         = document.getElementById('node-3');
  var outName       = document.getElementById('out-name');
  var btnSubmit     = document.getElementById('btnSubmitRegistration');
  var gcashBox      = document.getElementById('integrated-gcash-box');
  var slipChkCash   = document.getElementById('slip-chk-cash');
  var slipChkGcash  = document.getElementById('slip-chk-gcash');

  // ─── Mutual exclusion for checkboxes ───
  window.toggleMutex = function(selectedId, otherId) {
    var s = document.getElementById(selectedId);
    var o = document.getElementById(otherId);
    if (s && o && s.checked) o.checked = false;
  };

  // ─── Payment mode toggle (CASH / GCASH) ───
  window.selectPayment = function(mode) {
    if (mode === 'gcash') {
      slipChkGcash.checked = true;
      slipChkCash.checked = false;
      gcashBox.classList.add('active');
    } else {
      slipChkCash.checked = true;
      slipChkGcash.checked = false;
      gcashBox.classList.remove('active');
    }
  };

  // ─── STEP 1 → STEP 2 ───
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var fname = form.querySelector('[name="fname"]').value;
    var lname = form.querySelector('[name="lname"]').value;
    var mi    = form.querySelector('[name="mi"]').value;
    outName.innerText = (fname + ' ' + (mi ? mi + '. ' : '') + lname).toUpperCase();

    stageForm.classList.remove('active');
    stageSlip.classList.add('active');
    node1.classList.remove('active');
    node1.classList.add('completed');
    node2.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // ─── STEP 2 → STEP 3 ───
  btnSubmit.addEventListener('click', function() {
    stageSlip.classList.remove('active');
    stageStatus.classList.add('active');
    node2.classList.remove('active');
    node2.classList.add('completed');
    node3.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

});
</script>
</body>
</html>
