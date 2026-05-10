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
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-female: #D4AF37;
      --primary-female-dark: #B8860B;
      --primary-female-light: #FDF4E3;
      --accent-blue: #1e40af;
      --border: #e2e8f0;
      --text-muted: #64748b;
    }

    body { 
        background-color: #f8fafc; 
    }

    /* Target only the form content for specific fonts if needed, otherwise inherit */
    .form-container { font-family: 'Plus Jakarta Sans', sans-serif; }

    .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { 
        background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: var(--primary-female); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: #1e293b; font-family: 'Lora', serif; }

    /* 📄 PREMIUM FORM STYLING */
    .premium-form-card {
        background: #fff; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        border: 1px solid var(--border); overflow: hidden; margin-bottom: 30px;
    }
    .form-header-premium {
        background: linear-gradient(135deg, #fdfcf9, #f5f2e9);
        padding: 40px; text-align: center; border-bottom: 1px solid var(--border);
    }
    .form-header-premium h2 { font-family: 'Lora', serif; font-size: 1.5rem; font-weight: 800; color: var(--primary-female-dark); margin: 0; text-transform: uppercase; }
    .form-header-premium p { font-size: 1.1rem; font-weight: 700; color: var(--text-muted); margin-top: 8px; text-decoration: underline; }

    .form-content-premium { padding: 40px; }
    .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px; }
    .form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
    
    .input-premium-wrapper { position: relative; margin-bottom: 20px; }
    .label-premium {
        display: block; font-size: 0.75rem; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; margin-bottom: 8px;
    }
    .input-premium {
        width: 100%; padding: 12px 16px; border-radius: 10px; border: 1.5px solid #e2e8f0;
        font-size: 0.95rem; font-weight: 600; color: #1e293b; transition: all 0.2s; background: #f8fafc;
    }
    .input-premium:focus {
        outline: none; border-color: var(--primary-female); background: #fff;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }

    .section-divider { display: flex; align-items: center; gap: 15px; margin: 40px 0 25px; }
    .section-divider-text { font-size: 0.85rem; font-weight: 900; color: var(--primary-female-dark); text-transform: uppercase; white-space: nowrap; }
    .section-divider-line { flex: 1; height: 1px; background: linear-gradient(to right, var(--primary-female-light), var(--border)); }

    /* Choice / Radio Styling */
    .choice-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; }
    .choice-card {
        padding: 12px; border-radius: 10px; border: 1.5px solid var(--border);
        display: flex; align-items: center; gap: 10px; cursor: pointer; transition: 0.2s; background: #fff;
    }
    .choice-card input { width: 16px; height: 16px; accent-color: var(--primary-female); }
    .choice-card span { font-size: 0.8rem; font-weight: 700; color: #475569; }
    .choice-card:has(input:checked) { border-color: var(--primary-female); background: var(--primary-female-light); }

    /* Student Card Styling */
    .student-card {
        background: #fdfdfd; border: 1px solid var(--border); border-radius: 16px;
        padding: 30px; margin-bottom: 30px; position: relative;
    }
    .student-tag {
        position: absolute; top: -10px; left: 20px; background: var(--primary-female);
        color: #fff; padding: 2px 12px; border-radius: 20px; font-size: 0.65rem; font-weight: 900;
    }
    .remove-student {
        position: absolute; top: 15px; right: 15px; color: #ef4444; font-size: 0.7rem; font-weight: 800;
        cursor: pointer; background: #fff; border: 1px solid #fee2e2; padding: 4px 10px; border-radius: 8px; display: none;
    }
    .remove-student:hover { background: #fee2e2; }
    .student-card:not(:first-child) .remove-student { display: block; }

    .btn-add-applicant {
        width: 100%; padding: 20px; border: 2px dashed var(--primary-female); border-radius: 16px;
        background: #fff; color: var(--primary-female-dark); font-weight: 800; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 40px;
        transition: 0.3s;
    }
    .btn-add-applicant:hover { background: var(--primary-female-light); transform: translateY(-2px); }
    .btn-add-applicant svg { width: 20px; height: 20px; }

    .fee-highlight-box {
        background: linear-gradient(135deg, #1e40af, #1e3a8a);
        border-radius: 16px; padding: 25px; margin-bottom: 30px; color: #fff;
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
    }
    .fee-input { 
        background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.2);
        border-radius: 8px; color: #fff; padding: 10px; font-size: 0.85rem; font-weight: 600; width: 100%;
    }
    .fee-input option { color: #0f172a; background: #fff; }

    .signature-area { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; margin-top: 60px; }
    .sig-line { width: 100%; border-bottom: 1.5px solid #cbd5e1; margin-bottom: 10px; height: 40px; }
    .sig-label { font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; text-align: center; }

    .btn-submit-premium {
        background: linear-gradient(135deg, var(--primary-female), var(--primary-female-dark));
        color: #fff; border: none; padding: 18px 48px; border-radius: 12px;
        font-weight: 800; font-size: 1rem; cursor: pointer; text-transform: uppercase;
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <?php $active_page = 'female_education'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Premium Registration Form</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <?php if (!$hasPending && !$hasApproved): ?>
      <div class="form-container" style="max-width: 900px; margin: 0 auto;">
        
        <!-- REGISTRATION FEE BOX -->
        <div class="fee-highlight-box">
            <div style="flex: 1.5;">
                <h3 style="margin:0; font-family:'Lora', serif;">Registration Fee & Level</h3>
                <p style="margin:5px 0 0; opacity:0.8; font-size:0.85rem;">Official Batch 08 Enrollment</p>
            </div>
            <div style="flex: 1; display:flex; gap:12px;">
                <div style="flex:1;">
                    <label class="label-premium" style="color:rgba(255,255,255,0.7); font-size:0.6rem;">Level</label>
                    <select class="fee-input">
                        <option value="">Select Level</option>
                        <option>Beginner (B4)</option>
                        <option>Intermediate</option>
                        <option>Advanced</option>
                        <option>Tahfidhul Qur'an</option>
                        <option>Kids (7-9)</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label class="label-premium" style="color:rgba(255,255,255,0.7); font-size:0.6rem;">Payment</label>
                    <select class="fee-input"><option>Cash</option><option>GCash</option></select>
                </div>
            </div>
        </div>

        <div class="premium-form-card">
            <div class="form-header-premium">
                <h2>ISCAG PHILIPPINES — DA'WAH FEMALE SECTION</h2>
                <p>REGISTRATION FORM</p>
            </div>

            <div class="form-content-premium">
                <form id="enrollment-form">
                    
                    <!-- 1. RELATIONSHIP -->
                    <div class="form-section">
                        <div class="section-divider">
                            <span class="section-divider-text">Relationship to Applicant</span>
                            <div class="section-divider-line"></div>
                        </div>
                        <div class="input-premium-wrapper">
                            <label class="label-premium">I am registering as: *</label>
                            <div class="choice-grid" style="grid-template-columns: repeat(4, 1fr);">
                                <label class="choice-card"><input type="radio" name="rel" value="self" onchange="toggleAddButton()" checked><span>Self</span></label>
                                <label class="choice-card"><input type="radio" name="rel" value="daughter" onchange="toggleAddButton()"><span>Daughter</span></label>
                                <label class="choice-card"><input type="radio" name="rel" value="sister" onchange="toggleAddButton()"><span>Sister</span></label>
                                <label class="choice-card"><input type="radio" name="rel" value="relative" onchange="toggleAddButton()"><span>Relative</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- 2. STUDENT PROFILES -->
                    <div class="form-section">
                        <div class="section-divider">
                            <span class="section-divider-text">Personal Identity</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div id="applicant-container">
                            <div class="student-card">
                                <div class="student-tag">Applicant Details</div>
                                <button type="button" class="remove-student" onclick="removeStudent(this)">Remove</button>
                                
                                <div class="form-grid-3">
                                    <div class="input-premium-wrapper"><label class="label-premium">Last Name *</label><input type="text" class="input-premium" placeholder="Last Name" required></div>
                                    <div class="input-premium-wrapper"><label class="label-premium">First Name *</label><input type="text" class="input-premium" placeholder="First Name" required></div>
                                    <div class="input-premium-wrapper"><label class="label-premium">M.I.</label><input type="text" class="input-premium" placeholder="M.I."></div>
                                </div>
                                
                                <div class="form-grid-3">
                                    <div class="input-premium-wrapper">
                                        <label class="label-premium">
                                            Muslim Name <span style="font-weight: 500; color: #7a818bff; margin-left: 5px;">(Optional)</span>
                                        </label>
                                        <input type="text" class="input-premium">
                                    </div>
                                    <div class="input-premium-wrapper"><label class="label-premium">Birth Date *</label><input type="date" class="input-premium" required></div>
                                    <div class="input-premium-wrapper">
                                        <label class="label-premium">Islamic Status</label>
                                        <div class="choice-grid" style="grid-template-columns: 1fr 1fr;">
                                            <label class="choice-card"><input type="radio" name="status_0" value="revert"><span>Revert</span></label>
                                            <label class="choice-card"><input type="radio" name="status_0" value="born"><span>Born</span></label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-grid-2">
                                    <div class="input-premium-wrapper"><label class="label-premium">Shahadah Date</label><input type="date" class="input-premium"></div>
                                    <div class="input-premium-wrapper"><label class="label-premium">Previous Islamic Education</label><input type="text" class="input-premium" placeholder="Specify School/Center"></div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-applicant-btn" class="btn-add-applicant" onclick="addStudent()" style="display:none;">+ Add Another Daughter / Student</button>
                    </div>

                    <!-- 3. CONTACT & EMERGENCY -->
                    <div class="form-section">
                        <div class="section-divider">
                            <span class="section-divider-text">Contact & Emergency</span>
                            <div class="section-divider-line"></div>
                        </div>
                        <div class="form-grid-2">
                            <div class="input-premium-wrapper"><label class="label-premium">Home Address *</label><input type="text" class="input-premium" required></div>
                            <div class="input-premium-wrapper"><label class="label-premium">Facebook Account</label><input type="text" class="input-premium"></div>
                        </div>
                        <div class="form-grid-2">
                            <div class="input-premium-wrapper"><label class="label-premium">Contact Number *</label><input type="tel" class="input-premium" required></div>
                            <div class="input-premium-wrapper"><label class="label-premium">Emergency Contact Name *</label><input type="text" class="input-premium" required></div>
                        </div>
                        <div class="form-grid-2">
                            <div class="input-premium-wrapper"><label class="label-premium">Relationship *</label><input type="text" class="input-premium" required></div>
                            <div class="input-premium-wrapper"><label class="label-premium">Emergency Contact No. *</label><input type="tel" class="input-premium" required></div>
                        </div>
                    </div>

                    <!-- 4. FOR MINORS -->
                    <div class="form-section">
                        <div class="section-divider">
                            <span class="section-divider-text">For Minors (Parental Consent)</span>
                            <div class="section-divider-line"></div>
                        </div>
                        <div class="form-grid-2">
                            <div class="input-premium-wrapper"><label class="label-premium">Parent / Guardian Name</label><input type="text" class="input-premium"></div>
                            <div class="input-premium-wrapper"><label class="label-premium">Guardian Mobile No.</label><input type="tel" class="input-premium"></div>
                        </div>
                    </div>

                    <div class="signature-area">
                        <div class="sig-block"><div class="sig-line"></div><div class="sig-label">Signature of Student</div></div>
                        <div class="sig-block"><div class="sig-line"></div><div class="sig-label">Signature of Guardian</div></div>
                    </div>

                    <div style="margin-top: 60px; text-align: center;">
                        <button type="submit" class="btn-submit-premium">Submit Registration</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
    function toggleAddButton() {
        const rel = document.querySelector('input[name="rel"]:checked').value;
        const addBtn = document.getElementById('add-applicant-btn');
        const container = document.getElementById('applicant-container');
        if (rel === 'self') {
            addBtn.style.display = 'none';
            while (container.children.length > 1) { container.lastElementChild.remove(); }
            container.querySelector('.student-tag').innerText = 'Applicant Details';
        } else {
            addBtn.style.display = 'flex';
            renumber();
        }
    }

    function addStudent() {
        const container = document.getElementById('applicant-container');
        const card = container.children[0].cloneNode(true);
        const nextIdx = container.children.length;
        card.querySelectorAll('input').forEach(i => {
            i.value = '';
            if (i.type === 'radio') { i.name = i.name.replace(/_\d+$/, '_' + nextIdx); i.checked = false; }
        });
        container.appendChild(card);
        renumber();
    }

    function removeStudent(btn) {
        if (document.getElementById('applicant-container').children.length > 1) {
            btn.parentElement.remove();
            renumber();
        }
    }

    function renumber() {
        const cards = document.getElementById('applicant-container').children;
        const rel = document.querySelector('input[name="rel"]:checked').value;
        for (let i = 0; i < cards.length; i++) {
            cards[i].querySelector('.student-tag').innerText = (rel === 'self') ? 'Applicant Details' : 'Student ' + (i + 1);
        }
    }

    window.onload = toggleAddButton;
</script>
</body>
</html>
