<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Service Request</title>
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'burial_service'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Burial Service Request</div>
        <div class="top-bar-subtitle">Fill in all required information to submit a burial service request</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Burial Service Request Form</span>
      </div>

      <!-- FORM HEADER BANNER -->
      <div class="section-card" style="margin-bottom:20px;">
        <div class="form-page-header">
          <h4>Burial Service Request Form</h4>
          <p>Damayan Department — ISCAG Management Information System  |  Please ensure all fields are filled accurately.</p>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>All fields marked with an asterisk (<strong>*</strong>) are required. This form will be reviewed by the Burial Department within <strong>24 hours</strong> of submission.</span>
      </div>

      <!-- MAIN FORM CARD -->
      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            Burial Service Request Form
          </h6>
          <span style="font-size:0.75rem;color:var(--text-muted);">Reference No.: <strong style="color:var(--primary);">#BUR-AUTO</strong></span>
        </div>
        <div class="section-card-body">

          <!-- SECTION 1: Deceased Information -->
          <div class="form-section-title">Section 1 — Deceased Information</div>
          <div class="form-grid cols-3">
            <div>
              <label class="form-label">Full Name of Deceased <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Enter full name" />
            </div>
            <div>
              <label class="form-label">Age <span class="required">*</span></label>
              <input type="number" class="form-control" placeholder="Age" min="0" />
            </div>
            <div>
              <label class="form-label">Gender <span class="required">*</span></label>
              <select class="form-select">
                <option value="">— Select —</option>
                <option>Male</option>
                <option>Female</option>
              </select>
            </div>
          </div>
          <div class="form-grid cols-2" style="margin-bottom:24px;">
            <div>
              <label class="form-label">Date of Death <span class="required">*</span></label>
              <input type="date" class="form-control" />
            </div>
            <div>
              <label class="form-label">Place of Death <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Hospital, home address, or location" />
            </div>
          </div>

          <!-- SECTION 2: Family Contact -->
          <div class="form-section-title">Section 2 — Family Contact Information</div>
          <div class="form-grid cols-2">
            <div>
              <label class="form-label">Requester Full Name <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Name of the requesting family member" />
            </div>
            <div>
              <label class="form-label">Relationship to Deceased <span class="required">*</span></label>
              <select class="form-select">
                <option value="">— Select Relationship —</option>
                <option>Spouse</option>
                <option>Son / Daughter</option>
                <option>Father / Mother</option>
                <option>Sibling</option>
                <option>Relative</option>
                <option>Other</option>
              </select>
            </div>
          </div>
          <div class="form-grid cols-2-1" style="margin-bottom:24px;">
            <div>
              <label class="form-label">Complete Address <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Street, Barangay, City, Province" />
            </div>
            <div>
              <label class="form-label">Contact Number <span class="required">*</span></label>
              <input type="tel" class="form-control" placeholder="09XX-XXX-XXXX" />
            </div>
          </div>

          <!-- SECTION 3: Burial Details -->
          <div class="form-section-title">Section 3 — Burial Details</div>
          <div class="form-grid cols-2">
            <div>
              <label class="form-label">Preferred Burial Location <span class="required">*</span></label>
              <select class="form-select">
                <option value="">— Select Section —</option>
                <option>Section A — General</option>
                <option>Section B — Family Plot</option>
                <option>Section C — Extended</option>
                <option>Section D — New Expansion</option>
              </select>
            </div>
            <div>
              <label class="form-label">Preferred Burial Date <span class="required">*</span></label>
              <input type="date" class="form-control" />
            </div>
          </div>
          <div class="form-grid cols-2" style="margin-bottom:24px;">
            <div>
              <label class="form-label">Preferred Burial Time</label>
              <select class="form-select">
                <option value="">— Select Time —</option>
                <option>After Fajr (5:00 – 6:30 AM)</option>
                <option>After Dhuhr (12:30 – 2:00 PM)</option>
                <option>After Asr (3:30 – 5:00 PM)</option>
              </select>
            </div>
            <div>
              <label class="form-label">Additional Notes or Remarks</label>
              <textarea class="form-control" rows="3" placeholder="Any special instructions, requests, or relevant information..."></textarea>
            </div>
          </div>

          <!-- Declaration -->
          <div class="form-section-title">Declaration</div>
          <div class="form-check" style="margin-bottom:16px;">
            <input class="form-check-input" type="checkbox" id="decl1" />
            <label class="form-check-label" for="decl1">
              I certify that all information provided in this request is true, accurate, and complete to the best of my knowledge. I understand that the Burial Department will review and process this request within 24 hours.
            </label>
          </div>

          <div class="form-submit-row">
            <a href="<?= url('/user/dashboard') ?>" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="button" id="submit-btn">Submit Burial Request</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // ── Inlined data helpers ──
  const STORAGE_KEYS = { user: 'mis_user', requests: 'mis_requests', apartments: 'mis_apartments', initialized: 'mis_data_init' };
  const PROFILE_FIELDS = ['name','email','gender','phone','address','dob','civil','occupation','arabicName','membership'];
  const DEFAULT_USER = { 
    id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>', 
    name: '<?= addslashes($_SESSION['name'] ?? "User") ?>', 
    role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
    email:'<?= $_SESSION['email'] ?? "" ?>', 
    gender:'<?= $_SESSION['gender'] ?? "" ?>', 
    phone:'', address:'', dob:'', civil:'', occupation:'', arabicName:'', membership:'', revertYear:'', apartment:'', profileComplete:false 
  };
  const DEFAULT_REQUESTS = [
    { id:'BUR-001', user: DEFAULT_USER.id, type:'burial_service', status:'pending', date:'2026-03-15', updatedAt:'2026-03-15' },
    { id:'APT-001', user: DEFAULT_USER.id, type:'apartment_application', status:'approved', date:'2026-03-09', updatedAt:'2026-03-12' }
  ];

  function initData() {
    if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
      localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
      localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(DEFAULT_REQUESTS));
      localStorage.setItem(STORAGE_KEYS.initialized, '1');
    }
  }
  function getUser() {
    const raw = localStorage.getItem(STORAGE_KEYS.user);
    return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
  }

  initData();
  const user = getUser();

  // ── Da'wah dropdown — Filter by gender ──
  const dawahMenu = document.getElementById('dawah-menu');
  const sessionGender = '<?= strtolower($_SESSION['gender'] ?? '') ?>';
  if (sessionGender === 'female') {
    dawahMenu.innerHTML = `<a href="<?= url('/user/services/counseling/female') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Sisters' Counseling</a>`;
  } else {
    dawahMenu.innerHTML = `<a href="<?= url('/user/services/counseling/male') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Brothers' Counseling</a>`;
  }

  // ── Profile access gate (Server Side Check) ──
  const SESSION_ROLE = '<?= htmlspecialchars($_SESSION['role'] ?? '') ?>';
  if (SESSION_ROLE === '') {
    window.location.href = '<?= url('/user/profile') ?>';
  }

  // ── Sidebar collapse ──
  document.getElementById('sidebar-toggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('collapsed');
  });

  // ── Dropdown toggles ──
  function initDropdown(triggerId, menuId) {
    const trigger = document.getElementById(triggerId);
    const menu = document.getElementById(menuId);
    trigger.addEventListener('click', () => {
      const isOpen = menu.classList.contains('open');
      document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
      document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
      if (!isOpen) { menu.classList.add('open'); trigger.classList.add('open'); }
    });
  }
  initDropdown('damayan-trigger', 'damayan-menu');
  initDropdown('dawah-trigger', 'dawah-menu');
  initDropdown('apartment-trigger', 'apartment-menu');

  // ── Submit button ──
  document.getElementById('submit-btn').addEventListener('click', (e) => {
    e.preventDefault();
    const d1 = document.getElementById('decl1').checked;
    if (!d1) {
      showToast('⚠️ Please check the declaration box before submitting.', '#8b2e2e');
      return;
    }
    addRequest({ type: 'burial_service', user: user.id });

    // Hide the form and show pending message
    const pageBody = document.querySelector('.page-body');
    pageBody.innerHTML = `
      <div style="
        display:flex;flex-direction:column;align-items:center;justify-content:center;
        min-height:60vh;text-align:center;padding:40px 20px;
      ">
        <div style="
          background:white;border-radius:14px;padding:48px 40px;
          border:1px solid var(--border);
          box-shadow:0 2px 12px rgba(0,0,0,0.06);
          max-width:480px;width:100%;
        ">
          <div style="margin-bottom:20px;">
            <svg viewBox="0 0 24 24" style="width:56px;height:56px;fill:var(--accent);">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          <h3 style="
            font-family:'Lora',serif;font-size:1.3rem;font-weight:700;
            color:var(--primary-dark);margin:0 0 12px;
          ">Request Submitted</h3>
          <p style="
            font-size:1rem;color:var(--warning);font-weight:700;
            margin:0 0 10px;
          ">Pending... please wait for confirmation.</p>
          <p style="
            font-size:0.85rem;color:var(--text-muted);line-height:1.7;margin:0 0 24px;
          ">Your burial service request has been received and is now under review. You will be notified once it has been processed.</p>
          <a href="<?= url('/user/dashboard') ?>" style="
            display:inline-block;padding:10px 24px;border-radius:8px;
            background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));
            color:white;font-size:0.85rem;font-weight:700;
            text-decoration:none;
            box-shadow:0 4px 12px rgba(23,107,69,0.25);
            transition:all 0.18s;
          ">Return to Dashboard</a>
        </div>
      </div>
    `;
  });

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + bg + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:Source Sans 3,sans-serif;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;';
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
</script>
</body>
</html>
