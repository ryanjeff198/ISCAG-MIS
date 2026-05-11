<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Data fetched from controller
$history = $history ?? [];
$analytics = $analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0];

$hasActive = false;
$hasPending = false;
foreach ($history as $req) {
    if ($req['status'] === 'active') { $hasActive = true; }
    if ($req['status'] === 'pending') { $hasPending = true; }
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary-female: #D4AF37;
      --primary-female-dark: #B8860B;
      --primary-female-light: #FDF4E3;
    }

    /* ── Analytics Styles ── */
    .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { 
        background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: var(--primary-female); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: #1e293b; font-family: 'Lora', serif; }

    /* ── Simplified Student Card ── */
    .student-entry {
        background: #fdfdfd; border: 1.5px dashed var(--border); border-radius: 12px;
        padding: 24px; margin-bottom: 20px; position: relative;
    }
    .student-entry-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .student-badge { background: var(--primary-female-light); color: var(--primary-female-dark); padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    
    .btn-remove-student { background: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .btn-remove-student:hover { background: #fecaca; }

    .btn-add-applicant {
        width: 100%; padding: 16px; border: 2px dashed var(--primary-female); border-radius: 12px;
        background: #fff; color: var(--primary-female-dark); font-weight: 800; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 30px;
        transition: 0.2s;
    }
    .btn-add-applicant:hover { background: var(--primary-female-light); }

    /* Choice / Radio Styling */
    .choice-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; }
    .choice-card {
        padding: 12px; border-radius: 10px; border: 1.5px solid var(--border);
        display: flex; align-items: center; gap: 10px; cursor: pointer; transition: 0.2s; background: #fff;
    }
    .choice-card input { width: 16px; height: 16px; accent-color: var(--primary-female); }
    .choice-card span { font-size: 0.85rem; font-weight: 700; color: #475569; }
    .choice-card:has(input:checked) { border-color: var(--primary-female); background: var(--primary-female-light); }

    .form-section-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 800; color: var(--primary-female-dark); margin: 30px 0 15px; padding-bottom: 8px; border-bottom: 1.5px solid var(--primary-female-light); }
  </style>
</head>
<body>
<div class="app-wrapper">

  <?php $active_page = 'female_education'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Education Enrollment & History</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Female Education</span>
      </div>

      <!-- ANALYTICS OVERVIEW -->
      <div class="user-analytics">
        <div class="stat-card">
          <span class="stat-label">Total Registered</span>
          <span class="stat-value"><?= $analytics['total'] ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Pending Review</span>
          <span class="stat-value" style="color: #f59e0b;"><?= $analytics['pending'] ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Active Enrollment</span>
          <span class="stat-value" style="color: #10b981;"><?= $analytics['approved'] ?></span>
        </div>
      </div>

      <!-- ENROLLMENT HISTORY -->
      <?php if (!empty($history)): ?>
      <div class="section-card" style="margin-bottom: 30px;">
        <div class="section-card-header">
            <h6>Enrollment History & Status</h6>
        </div>
        <div class="section-card-body" style="padding: 0;">
            <div class="table-wrapper">
                <table class="mis-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Program</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $item): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($item['first_name'] . ' ' . $item['last_name']) ?></strong></td>
                            <td><?= htmlspecialchars($item['program_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($item['created_at'])) ?></td>
                            <td>
                                <span class="badge-status <?= $item['status'] === 'pending' ? 'pending' : 'success' ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!$hasActive && !$hasPending): ?>
            <div style="padding: 24px; text-align: center; border-top: 1px solid var(--border);">
                <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; margin-bottom: 15px;">Would you like to register another student?</p>
                <button class="btn-submit" style="background: var(--primary-female); border-color: var(--primary-female-dark); color: #1a1a1a;" onclick="document.getElementById('enrollment-form-section').scrollIntoView({behavior:'smooth'})">New Registration</button>
            </div>
            <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- REGISTRATION FORM -->
      <?php if (!$hasActive && !$hasPending): ?>
      <div id="enrollment-form-section" class="section-card">
        <div class="section-card-header">
          <h6>Registration Form</h6>
        </div>
        <div class="section-card-body">
            <form id="enrollment-form">
                
                <!-- 1. PROGRAM SELECTION -->
                <div class="form-section-title">Program & Fee Information</div>
                <div class="form-grid cols-2">
                    <div>
                        <label class="form-label">Program Level <span class="required">*</span></label>
                        <select class="form-select" id="program-level" name="level" required>
                            <option value="">Select Level</option>
                            <option>Beginner (B4)</option>
                            <option>Intermediate</option>
                            <option>Advanced</option>
                            <option>Tahfidhul Qur'an</option>
                            <option>Kids (7-9)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Payment Method <span class="required">*</span></label>
                        <select class="form-select" id="payment-method" name="payment"><option>Cash</option><option>GCash</option></select>
                    </div>
                </div>

                <!-- 2. RELATIONSHIP -->
                <div class="form-section-title">Relationship to Applicant</div>
                <div style="margin-bottom: 20px;">
                    <label class="form-label">I am registering as: <span class="required">*</span></label>
                    <div class="choice-grid">
                        <label class="choice-card"><input type="radio" name="rel" value="self" onchange="toggleAddButton()" checked><span>Self</span></label>
                        <label class="choice-card"><input type="radio" name="rel" value="daughter" onchange="toggleAddButton()"><span>Daughter</span></label>
                        <label class="choice-card"><input type="radio" name="rel" value="sister" onchange="toggleAddButton()"><span>Sister</span></label>
                        <label class="choice-card"><input type="radio" name="rel" value="relative" onchange="toggleAddButton()"><span>Relative</span></label>
                    </div>
                </div>

                <!-- 3. STUDENT PROFILES -->
                <div class="form-section-title">Student Profile(s)</div>
                <div id="applicant-container">
                    <div class="student-entry">
                        <div class="student-entry-header">
                            <span class="student-badge">Applicant Details</span>
                            <button type="button" class="btn-remove-student" style="display:none;" onclick="removeStudent(this)">Remove</button>
                        </div>
                        
                        <div class="form-grid cols-3">
                            <div><label class="form-label">Last Name *</label><input type="text" class="form-control student-ln" placeholder="Last Name" required></div>
                            <div><label class="form-label">First Name *</label><input type="text" class="form-control student-fn" placeholder="First Name" required></div>
                            <div><label class="form-label">M.I.</label><input type="text" class="form-control student-mi" placeholder="M.I."></div>
                        </div>
                        
                        <div class="form-grid cols-3">
                            <div><label class="form-label">Muslim Name</label><input type="text" class="form-control student-mn" placeholder="Optional"></div>
                            <div><label class="form-label">Birth Date *</label><input type="date" class="form-control student-dob" required></div>
                            <div>
                                <label class="form-label">Islamic Status</label>
                                <div class="choice-grid" style="grid-template-columns: 1fr 1fr;">
                                    <label class="choice-card"><input type="radio" name="status_0" value="revert" class="student-status"><span>Revert</span></label>
                                    <label class="choice-card"><input type="radio" name="status_0" value="born" class="student-status"><span>Born</span></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-grid cols-2">
                            <div><label class="form-label">Shahadah Date</label><input type="date" class="form-control student-sd"></div>
                            <div><label class="form-label">Previous Islamic Education</label><input type="text" class="form-control student-pe" placeholder="Specify School/Center"></div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-applicant-btn" class="btn-add-applicant" onclick="addStudent()" style="display:none;">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Add Another Daughter / Relative
                </button>

                <!-- 4. CONTACT & EMERGENCY -->
                <div class="form-section-title">Contact & Emergency Details</div>
                <div class="form-grid cols-2">
                    <div><label class="form-label">Home Address *</label><input type="text" name="address" class="form-control" required></div>
                    <div><label class="form-label">Facebook Account</label><input type="text" name="facebook" class="form-control"></div>
                </div>
                <div class="form-grid cols-2">
                    <div><label class="form-label">Contact Number *</label><input type="tel" name="contact" class="form-control" required></div>
                    <div><label class="form-label">Emergency Contact Name *</label><input type="text" name="emergency_name" class="form-control" required></div>
                </div>
                <div class="form-grid cols-2">
                    <div><label class="form-label">Relationship *</label><input type="text" name="emergency_rel" class="form-control" required></div>
                    <div><label class="form-label">Emergency Contact No. *</label><input type="tel" name="emergency_no" class="form-control" required></div>
                </div>

                <!-- 5. FOR MINORS -->
                <div class="form-section-title">For Minors (Parental Consent)</div>
                <div class="form-grid cols-2">
                    <div><label class="form-label">Parent / Guardian Name</label><input type="text" name="guardian_name" class="form-control"></div>
                    <div><label class="form-label">Guardian Mobile No.</label><input type="tel" name="guardian_mobile" class="form-control"></div>
                </div>

                <div class="form-submit-row" style="margin-top: 40px;">
                    <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Discard</button>
                    <button type="submit" class="btn-submit" style="background: var(--primary-female); border-color: var(--primary-female-dark); color: #1a1a1a; font-weight: 800;">Submit Enrollment</button>
                </div>
            </form>
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
            container.querySelector('.student-badge').innerText = 'Applicant Details';
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
            if (i.type === 'radio') { i.name = i.name.replace(/_\d+$/, '_' + nextIdx); i.checked = false; }
            else { i.value = ''; }
        });
        card.querySelector('.btn-remove-student').style.display = 'block';
        container.appendChild(card);
        renumber();
    }

    function removeStudent(btn) {
        if (document.getElementById('applicant-container').children.length > 1) {
            btn.closest('.student-entry').remove();
            renumber();
        }
    }

    function renumber() {
        const cards = document.getElementById('applicant-container').children;
        const rel = document.querySelector('input[name="rel"]:checked').value;
        for (let i = 0; i < cards.length; i++) {
            cards[i].querySelector('.student-badge').innerText = (rel === 'self') ? 'Applicant Details' : 'Student ' + (i + 1);
            if (i > 0) cards[i].querySelector('.btn-remove-student').style.display = 'block';
        }
    }

    window.onload = toggleAddButton;

    document.getElementById('enrollment-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const applicants = [];
        document.querySelectorAll('.student-entry').forEach((card, idx) => {
            const statusRadio = card.querySelector(`.student-status:checked`);
            applicants.push({
                last_name: card.querySelector('.student-ln').value,
                first_name: card.querySelector('.student-fn').value,
                mi: card.querySelector('.student-mi').value,
                muslim_name: card.querySelector('.student-mn').value,
                dob: card.querySelector('.student-dob').value,
                status: statusRadio ? statusRadio.value : null,
                shahadah_date: card.querySelector('.student-sd').value,
                prev_edu: card.querySelector('.student-pe').value
            });
        });

        const formData = {
            level: document.getElementById('program-level').value,
            payment: document.getElementById('payment-method').value,
            relationship: document.querySelector('input[name="rel"]:checked').value,
            address: this.querySelector('input[name="address"]').value,
            facebook: this.querySelector('input[name="facebook"]').value,
            contact: this.querySelector('input[name="contact"]').value,
            emergency_name: this.querySelector('input[name="emergency_name"]').value,
            emergency_rel: this.querySelector('input[name="emergency_rel"]').value,
            emergency_no: this.querySelector('input[name="emergency_no"]').value,
            guardian_name: this.querySelector('input[name="guardian_name"]').value,
            guardian_mobile: this.querySelector('input[name="guardian_mobile"]').value,
            applicants: applicants
        };

        Swal.fire({
            title: 'Submitting...',
            text: 'Please wait while we process your enrollment.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch('<?= url("/user/services/education/female/submit") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const result = await response.json();
            if (result.success) {
                Swal.fire({ icon: 'success', title: 'Success!', text: result.message }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Submission failed.' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred.' });
        }
    });
</script>
</body>
</html>
