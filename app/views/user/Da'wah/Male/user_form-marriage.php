<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Marriage Registration Form</title>
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

    .marriage-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
    @media (max-width: 992px) { .marriage-grid { grid-template-columns: 1fr; } }
    
    .party-card { background: #fbfcfb; border: 1.5px solid var(--border); border-radius: 16px; padding: 24px; }
    .party-card.husband { border-left: 5px solid #27ae60; }
    .party-card.wife { border-left: 5px solid #e91e63; }
    .party-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    
    .section-divider { height: 1px; background: var(--border); margin: 24px 0; position: relative; }
    .section-divider::after { content: attr(data-label); position: absolute; top: -10px; left: 20px; background: white; padding: 0 10px; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }

    .other-religion-wrap { 
      max-height: 0; 
      opacity: 0; 
      overflow: hidden; 
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
      margin-top: 0;
    }
    .other-religion-wrap.show { 
      max-height: 80px; 
      opacity: 1; 
      margin-top: 12px;
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'marriage_form'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Marriage Registration</div>
        <div class="top-bar-subtitle">Application for Certificate of Marriage (Municipal Form No. 97)</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Marriage Service Form</span>
      </div>

      <div class="step-indicator" id="step-indicator">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
        <div class="step-dot"></div>
      </div>

      <form id="marriage-form" method="POST">
        <!-- STEP 1: HUSBAND'S INFORMATION -->
        <div class="form-step active" id="step-1">
          <div class="section-card party-card husband">
            <div class="party-title" style="color: #1e8449;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Husband's Information
            </div>
            
            <div class="form-grid cols-3">
              <div>
                <label class="form-label">First Name <span class="required">*</span></label>
                <input type="text" name="h_fname" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Middle Name</label>
                <input type="text" name="h_mname" class="form-control" />
              </div>
              <div>
                <label class="form-label">Last Name <span class="required">*</span></label>
                <input type="text" name="h_lname" class="form-control" required />
              </div>
            </div>

            <div class="form-grid cols-3">
              <div>
                <label class="form-label">Date of Birth <span class="required">*</span></label>
                <input type="date" name="h_dob" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Age <span class="required">*</span></label>
                <input type="number" name="h_age" class="form-control" min="18" required />
              </div>
              <div>
                <label class="form-label">Citizenship <span class="required">*</span></label>
                <input type="text" name="h_citizenship" class="form-control" value="Filipino" required />
              </div>
            </div>

            <div class="section-divider" data-label="Place of Birth"></div>
            <div class="form-grid cols-3">
              <div>
                <label class="form-label">City / Municipality <span class="required">*</span></label>
                <input type="text" name="h_pob_city" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Province <span class="required">*</span></label>
                <input type="text" name="h_pob_province" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Country <span class="required">*</span></label>
                <input type="text" name="h_pob_country" class="form-control" value="Philippines" required />
              </div>
            </div>

            <div class="section-divider" data-label="Residence & Others"></div>
            <div style="margin-bottom: 20px;">
              <label class="form-label">Full Residence Address <span class="required">*</span></label>
              <input type="text" name="h_residence" class="form-control" placeholder="House No., Street, Barangay, City, Province" required />
            </div>

            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Religion / Religious Sect <span class="required">*</span></label>
                <select name="h_religion" id="h_religion" class="form-select" required>
                  <option value="Islam">Islam</option>
                  <option value="Roman Catholic">Roman Catholic</option>
                  <option value="Protestant">Protestant</option>
                  <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                  <option value="Seventh-day Adventist">Seventh-day Adventist</option>
                  <option value="Others">Others</option>
                </select>
                <div id="h_religion_other_wrap" class="other-religion-wrap">
                  <input type="text" name="h_religion_other" id="h_religion_other" class="form-control" placeholder="Specify your religion / sect..." />
                </div>
              </div>
              <div>
                <label class="form-label">Civil Status <span class="required">*</span></label>
                <select name="h_civil_status" class="form-select" required>
                  <option value="">— Select —</option>
                  <option>Single</option>
                  <option>Widower</option>
                  <option>Divorced</option>
                </select>
              </div>
            </div>

            <div class="section-divider" data-label="Parents"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Father's Full Name <span class="required">*</span></label>
                <input type="text" name="h_father_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Father's Citizenship <span class="required">*</span></label>
                <input type="text" name="h_father_citizenship" class="form-control" required />
              </div>
            </div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Mother's Maiden Name <span class="required">*</span></label>
                <input type="text" name="h_mother_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Mother's Citizenship <span class="required">*</span></label>
                <input type="text" name="h_mother_citizenship" class="form-control" required />
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-submit next-btn" data-next="2">Next: Wife's Details →</button>
          </div>
        </div>

        <!-- STEP 2: WIFE'S INFORMATION -->
        <div class="form-step" id="step-2">
          <div class="section-card party-card wife">
            <div class="party-title" style="color: #c2185b;">
              <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Wife's Information
            </div>
            
            <div class="form-grid cols-3">
              <div>
                <label class="form-label">First Name <span class="required">*</span></label>
                <input type="text" name="w_fname" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Middle Name</label>
                <input type="text" name="w_mname" class="form-control" />
              </div>
              <div>
                <label class="form-label">Last Name <span class="required">*</span></label>
                <input type="text" name="w_lname" class="form-control" required />
              </div>
            </div>

            <div class="form-grid cols-3">
              <div>
                <label class="form-label">Date of Birth <span class="required">*</span></label>
                <input type="date" name="w_dob" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Age <span class="required">*</span></label>
                <input type="number" name="w_age" class="form-control" min="18" required />
              </div>
              <div>
                <label class="form-label">Citizenship <span class="required">*</span></label>
                <input type="text" name="w_citizenship" class="form-control" value="Filipino" required />
              </div>
            </div>

            <div class="section-divider" data-label="Place of Birth"></div>
            <div class="form-grid cols-3">
              <div>
                <label class="form-label">City / Municipality <span class="required">*</span></label>
                <input type="text" name="w_pob_city" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Province <span class="required">*</span></label>
                <input type="text" name="w_pob_province" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Country <span class="required">*</span></label>
                <input type="text" name="w_pob_country" class="form-control" value="Philippines" required />
              </div>
            </div>

            <div class="section-divider" data-label="Residence & Others"></div>
            <div style="margin-bottom: 20px;">
              <label class="form-label">Full Residence Address <span class="required">*</span></label>
              <input type="text" name="w_residence" class="form-control" placeholder="House No., Street, Barangay, City, Province" required />
            </div>

            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Religion / Religious Sect <span class="required">*</span></label>
                <select name="w_religion" id="w_religion" class="form-select" required>
                  <option value="Islam">Islam</option>
                  <option value="Roman Catholic">Roman Catholic</option>
                  <option value="Protestant">Protestant</option>
                  <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                  <option value="Seventh-day Adventist">Seventh-day Adventist</option>
                  <option value="Others">Others</option>
                </select>
                <div id="w_religion_other_wrap" class="other-religion-wrap">
                  <input type="text" name="w_religion_other" id="w_religion_other" class="form-control" placeholder="Specify your religion / sect..." />
                </div>
              </div>
              <div>
                <label class="form-label">Civil Status <span class="required">*</span></label>
                <select name="w_civil_status" class="form-select" required>
                  <option value="">— Select —</option>
                  <option>Single</option>
                  <option>Widow</option>
                  <option>Divorced</option>
                </select>
              </div>
            </div>

            <div class="section-divider" data-label="Parents"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Father's Full Name <span class="required">*</span></label>
                <input type="text" name="w_father_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Father's Citizenship <span class="required">*</span></label>
                <input type="text" name="w_father_citizenship" class="form-control" required />
              </div>
            </div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Mother's Maiden Name <span class="required">*</span></label>
                <input type="text" name="w_mother_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Mother's Citizenship <span class="required">*</span></label>
                <input type="text" name="w_mother_citizenship" class="form-control" required />
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-cancel prev-btn" data-prev="1">← Back</button>
            <button type="button" class="btn-submit next-btn" data-next="3">Next: Marriage Details →</button>
          </div>
        </div>

        <!-- STEP 3: MARRIAGE DETAILS -->
        <div class="form-step" id="step-3">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M12 2L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                Marriage Ceremony Details
              </h6>
            </div>
            <div class="section-card-body">
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Date of Marriage <span class="required">*</span></label>
                  <input type="date" name="marriage_date" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Time of Marriage <span class="required">*</span></label>
                  <input type="time" name="marriage_time" class="form-control" required />
                </div>
              </div>

              <div class="section-divider" data-label="Place of Marriage"></div>
              <div style="margin-bottom: 20px;">
                <label class="form-label">Building / Venue / Mosque Name <span class="required">*</span></label>
                <input type="text" name="marriage_venue" class="form-control" placeholder="e.g. ISCAG Mosque, Conference Room, etc." required />
              </div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">City / Municipality <span class="required">*</span></label>
                  <input type="text" name="marriage_city" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Province <span class="required">*</span></label>
                  <input type="text" name="marriage_province" class="form-control" required />
                </div>
              </div>

              <div class="section-divider" data-label="Solemnizing Officer & Witnesses"></div>
              <div class="form-grid cols-2">
                <div>
                  <label class="form-label">Name of Solemnizing Officer <span class="required">*</span></label>
                  <input type="text" name="solemnizer_name" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Position / Designation <span class="required">*</span></label>
                  <input type="text" name="solemnizer_rank" class="form-control" placeholder="e.g. Imam, Regional Director" required />
                </div>
              </div>

              <div style="margin-top: 16px;">
                <label class="form-label">Witnesses (Name and Signature) <span class="required">*</span></label>
                <textarea name="witnesses" class="form-control" rows="4" placeholder="1. Name of Witness&#10;2. Name of Witness" required></textarea>
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-cancel prev-btn" data-prev="2">← Back</button>
            <button type="button" class="btn-submit next-btn" data-next="4">Next: Legal Info →</button>
          </div>
        </div>

        <!-- STEP 4: LEGAL INFO & REVIEW -->
        <div class="form-step" id="step-4">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/></svg>
                Legal Information & Certifications
              </h6>
            </div>
            <div class="section-card-body">
              <div class="notice-box" style="margin-bottom: 24px;">
                <svg viewBox="0 0 24 24"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>
                <span>Please specify the legal basis for this marriage registration.</span>
              </div>

              <div style="background: #f8faf9; padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
                <div class="form-check" style="margin-bottom: 16px;">
                  <input class="form-check-input" type="radio" name="legal_basis" id="basis-license" value="license" checked>
                  <label class="form-check-label" for="basis-license"><strong>Marriage License</strong> (Regular Marriage)</label>
                  <div id="license-fields" style="margin-top: 12px; padding-left: 28px;">
                    <div class="form-grid cols-3">
                      <div>
                        <label class="form-label" style="font-size: 0.75rem;">License No.</label>
                        <input type="text" name="license_no" class="form-control" />
                      </div>
                      <div>
                        <label class="form-label" style="font-size: 0.75rem;">Issued On</label>
                        <input type="date" name="license_date" class="form-control" />
                      </div>
                      <div>
                        <label class="form-label" style="font-size: 0.75rem;">Issued At (City)</label>
                        <input type="text" name="license_place" class="form-control" />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-check" style="margin-bottom: 16px;">
                  <input class="form-check-input" type="radio" name="legal_basis" id="basis-pd1083" value="pd1083">
                  <label class="form-check-label" for="basis-pd1083"><strong>Presidential Decree No. 1083</strong> (Muslim Personal Laws of the Philippines)</label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="legal_basis" id="basis-eo209" value="eo209">
                  <label class="form-check-label" for="basis-eo209"><strong>Art. 34 of EO 209</strong> (Marriage of exceptional character - no license required)</label>
                </div>
              </div>

              <div class="section-divider" data-label="Review & Submit"></div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="marriage-cert" required>
                <label class="form-check-label" for="marriage-cert" style="font-size: 0.85rem; line-height: 1.6;">
                  WE HEREBY CERTIFY: That we, of our own free will and accord, and in the presence of the person solemnizing this marriage and of the witnesses, take each other as husband and wife. We further certify that all information provided above is true and correct to the best of our knowledge.
                </label>
              </div>
            </div>
          </div>
          
          <div class="form-submit-row" style="margin-top: 30px;">
            <button type="button" class="btn-cancel prev-btn" data-prev="3">← Back</button>
            <button type="button" class="btn-submit" id="final-submit-btn">Submit Marriage Application</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

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
        current.classList.remove('active');
        next.classList.add('active');
        updateDots(btn.getAttribute('data-next'));
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        if(typeof showToast === 'function') showToast('Please fill in all required fields.', '#e67e22');
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

  // ── Toggle License Fields ──
  document.querySelectorAll('input[name="legal_basis"]').forEach(radio => {
    radio.onchange = () => {
      const fields = document.getElementById('license-fields');
      fields.style.opacity = radio.id === 'basis-license' ? '1' : '0.4';
      fields.querySelectorAll('input').forEach(i => i.disabled = radio.id !== 'basis-license');
    };
  });

  // ── Toggle Other Religion ──
  function toggleOtherReligion(selectId, wrapId, inputId) {
    const select = document.getElementById(selectId);
    const wrap = document.getElementById(wrapId);
    const input = document.getElementById(inputId);
    
    select.onchange = () => {
      if (select.value === 'Others') {
        wrap.classList.add('show');
        input.required = true;
        setTimeout(() => input.focus(), 300);
      } else {
        wrap.classList.remove('show');
        input.required = false;
        setTimeout(() => { if(!wrap.classList.contains('show')) input.value = ''; }, 400);
      }
    };
  }
  toggleOtherReligion('h_religion', 'h_religion_other_wrap', 'h_religion_other');
  toggleOtherReligion('w_religion', 'w_religion_other_wrap', 'w_religion_other');

  // ── Final Submit ──
  document.getElementById('final-submit-btn').onclick = (e) => {
    const cert = document.getElementById('marriage-cert').checked;
    if(!cert) {
        if(typeof showToast === 'function') showToast('Please check the certification box.', '#e67e22');
        return;
    }

    const pageBody = document.querySelector('.page-body');
    pageBody.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px 20px;">
        <div style="background:white;border-radius:24px;padding:60px 40px;border:1px solid var(--border);box-shadow:0 10px 40px rgba(0,0,0,0.08);max-width:520px;width:100%;">
          <div style="margin-bottom:24px;">
            <div style="width:80px;height:80px;background:rgba(23,107,69,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--primary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
          </div>
          <h3 style="font-family:'Lora',serif;font-size:1.6rem;font-weight:700;color:var(--primary-dark);margin:0 0 16px;">Application Received</h3>
          <p style="font-size:1rem;color:var(--text-muted);line-height:1.7;margin:0 0 32px;">Your marriage registration application has been submitted successfully. Please wait for the Da'wah department to review and contact you for further verification.</p>
          <a href="<?= url('/user/dashboard') ?>" style="display:inline-block;padding:14px 32px;border-radius:12px;background:var(--primary);color:white;font-size:0.95rem;font-weight:700;text-decoration:none;box-shadow:0 8px 20px rgba(15,92,58,0.25);transition:all 0.3s;">Return to Dashboard</a>
        </div>
      </div>
    `;
  };

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
