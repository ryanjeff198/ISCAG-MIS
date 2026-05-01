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
        <div class="top-bar-title">Conversion Registration</div>
        <div class="top-bar-subtitle">Certificate of Conversion to Islam (OCRG Form No. 104)</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Conversion Form</span>
      </div>

      <div class="step-indicator" id="step-indicator">
        <div class="step-dot active"></div>
        <div class="step-dot"></div>
      </div>

      <form id="conversion-form" method="POST">
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
                  <input type="text" name="fname" class="form-control" required />
                </div>
                <div>
                  <label class="form-label">Middle Name</label>
                  <input type="text" name="mname" class="form-control" />
                </div>
                <div>
                  <label class="form-label">Last Name <span class="required">*</span></label>
                  <input type="text" name="lname" class="form-control" required />
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
                  <input type="text" name="occupation" class="form-control" required />
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
            <button type="button" class="btn-submit" id="final-submit-btn">Submit Conversion Registration</button>
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
        // Sync name to testimony
        const fn = document.querySelector('input[name="fname"]').value;
        const ln = document.querySelector('input[name="lname"]').value;
        document.getElementById('display-name').textContent = fn + ' ' + ln;

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

  // ── Toggle Other Religion ──
  const relSelect = document.getElementById('former_religion');
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

  // ── Final Submit ──
  document.getElementById('final-submit-btn').onclick = (e) => {
    const decl = document.getElementById('declaration').checked;
    if(!decl) {
        if(typeof showToast === 'function') showToast('Please check the declaration box.', '#e67e22');
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
          <h3 style="font-family:'Lora',serif;font-size:1.6rem;font-weight:700;color:var(--primary-dark);margin:0 0 16px;">Registration Submitted</h3>
          <p style="font-size:1rem;color:var(--text-muted);line-height:1.7;margin:0 0 32px;">Your conversion registration has been submitted successfully. A representative from the Da'wah department will review your application and provide your official certificate.</p>
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
