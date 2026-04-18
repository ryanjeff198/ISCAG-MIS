<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Sisters' Counseling Request</title>
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'counseling_female'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Sisters' Counseling Request</div>
        <div class="top-bar-subtitle">Schedule a confidential counseling session with our female counselors</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Sisters' Counseling Request Form</span>
      </div>

      <!-- FORM HEADER BANNER -->
      <div class="section-card" style="margin-bottom:20px;">
        <div class="form-page-header">
          <h4>Sisters' Counseling Request Form</h4>
          <p>Da'wah Department — All information will be kept strictly confidential.</p>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>This form is exclusively for <strong>female clients</strong>. Sessions are conducted by female counselors in a private and respectful environment aligned with Islamic values.</span>
      </div>

      <!-- MAIN FORM CARD -->
      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            Counseling Request Form — Sisters' Department
          </h6>
          <span style="font-size:0.75rem;color:var(--text-muted);">Reference No.: <strong style="color:var(--primary);">#FC-AUTO</strong></span>
        </div>
        <div class="section-card-body">

          <!-- Client Information -->
          <div class="form-section-title">Client Information</div>
          <div class="form-grid cols-3">
            <div>
              <label class="form-label">Full Name <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Enter your full name" />
            </div>
            <div>
              <label class="form-label">Age <span class="required">*</span></label>
              <input type="number" class="form-control" placeholder="Age" min="0" />
            </div>
            <div>
              <label class="form-label">Civil Status</label>
              <select class="form-select">
                <option value="">— Select —</option>
                <option>Single</option>
                <option>Married</option>
                <option>Widow</option>
                <option>Divorced</option>
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

          <!-- Counseling Details -->
          <div class="form-section-title">Counseling Details</div>
          <div style="margin-bottom:16px;">
            <label class="form-label">Reason for Counseling <span class="required">*</span></label>
            <select class="form-select" style="max-width:380px;margin-bottom:10px;">
              <option value="">— Select primary concern —</option>
              <option>Family / Marital Issues</option>
              <option>Personal / Spiritual Struggles</option>
              <option>Youth / Parenting Concerns</option>
              <option>Financial Difficulties</option>
              <option>Grief and Loss</option>
              <option>Domestic Issues</option>
              <option>Other</option>
            </select>
            <textarea class="form-control" rows="4" placeholder="Please briefly describe your concern or reason for requesting counseling. All details are kept confidential..."></textarea>
          </div>
          <div class="form-grid cols-2">
            <div>
              <label class="form-label">Preferred Schedule Date <span class="required">*</span></label>
              <input type="date" class="form-control" />
            </div>
            <div>
              <label class="form-label">Preferred Time Slot <span class="required">*</span></label>
              <select class="form-select">
                <option value="">— Select Time —</option>
                <option>8:00 AM – 9:00 AM</option>
                <option>9:00 AM – 10:00 AM</option>
                <option>10:00 AM – 11:00 AM</option>
                <option>1:00 PM – 2:00 PM</option>
                <option>2:00 PM – 3:00 PM</option>
                <option>3:00 PM – 4:00 PM</option>
              </select>
            </div>
          </div>
          <div style="margin-bottom:24px;">
            <label class="form-label">Session Type</label>
            <div style="display:flex;gap:20px;margin-top:6px;">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="session" id="session1f" checked />
                <label class="form-check-label" for="session1f">In-Person</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="session" id="session2f" />
                <label class="form-check-label" for="session2f">By Phone</label>
              </div>
            </div>
          </div>

          <!-- Declaration -->
          <div class="form-section-title">Declaration</div>
          <div class="form-check" style="margin-bottom:16px;">
            <input class="form-check-input" type="checkbox" id="decl1" />
            <label class="form-check-label" for="decl1">
              I hereby declare that the information provided is true and correct. I understand that all counseling sessions are confidential and conducted with respect for Islamic values and principles.
            </label>
          </div>

          <div class="form-submit-row">
            <a href="<?= url('/user/dashboard') ?>" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="button" id="submit-btn">Submit Counseling Request</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // ── Inlined data helpers ──
  const STORAGE_KEYS = { user: 'mis_user', requests: 'mis_requests', initialized: 'mis_data_init' };
  const PROFILE_FIELDS = ['name','email','gender','phone','address','dob','civil','occupation','arabicName','membership'];
  const DEFAULT_USER = { 
    id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>', 
    name: '<?= addslashes($_SESSION['name'] ?? "User") ?>', 
    role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
    email:'<?= $_SESSION['email'] ?? "" ?>', 
    gender:'<?= $_SESSION['gender'] ?? "" ?>', 
    phone:'', address:'', dob:'', civil:'', occupation:'', arabicName:'', membership:'', revertYear:'', apartment:'', profileComplete:false 
  };

  function initData() {
    if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
      localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
      localStorage.setItem(STORAGE_KEYS.initialized, '1');
    }
  }
  function getUser() {
    const raw = localStorage.getItem(STORAGE_KEYS.user);
    const stored = raw ? JSON.parse(raw) : { ...DEFAULT_USER };
    stored.id = DEFAULT_USER.id;
    stored.name = DEFAULT_USER.name;
    return stored;
  }
  function getProfileCompletion() {
    const user = getUser();
    const missing = [];
    let filled = 0;
    const labels = { name:'Full Name', email:'Email Address', gender:'Gender', phone:'Contact Number', address:'Complete Address', dob:'Date of Birth', civil:'Civil Status', occupation:'Occupation', arabicName:'Muslim / Arabic Name', membership:'Masjid Membership' };
    PROFILE_FIELDS.forEach(k => {
      if (user[k] && String(user[k]).trim() !== '') { filled++; } else { missing.push(labels[k] || k); }
    });
    return { percentage: Math.round((filled / PROFILE_FIELDS.length) * 100), filled, total: PROFILE_FIELDS.length, missingFields: missing };
  }
  function addRequest(req) {
    const raw = localStorage.getItem(STORAGE_KEYS.requests);
    const requests = raw ? JSON.parse(raw) : [];
    if (!req.id) req.id = 'FC-' + String(requests.length + 1).padStart(3, '0');
    if (!req.date) req.date = new Date().toISOString().split('T')[0];
    if (!req.updatedAt) req.updatedAt = req.date;
    if (!req.status) req.status = 'pending';
    requests.push(req);
    localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(requests));
    return req;
  }

  initData();

  const user = getUser();

  // ── Profile access gate + gender gate ──
  const { percentage, missingFields } = getProfileCompletion();
  if (percentage < 100 || user.gender !== 'female') {
    if (!document.getElementById('acm-keyframes')) {
      const styleEl = document.createElement('style');
      styleEl.id = 'acm-keyframes';
      styleEl.textContent = `
        @keyframes acmFadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes acmSlideUp { from { opacity:0;transform:translateY(24px) scale(0.96); } to { opacity:1;transform:translateY(0) scale(1); } }
      `;
      document.head.appendChild(styleEl);
    }

    let title = 'Profile Incomplete';
    let message = 'Access to this section is restricted until your profile is fully completed.';
    let primaryLabel = 'Go to Profile';
    let primaryUrl = '../../user_profile.html';

    if (percentage >= 100 && user.gender !== 'female') {
      title = 'Access Restricted';
      message = 'This counseling service is exclusively available for sisters. You do not have access to this section.';
      primaryLabel = 'Back to Dashboard';
      primaryUrl = '../../user-dashboard.html';
    }

    const missingHtml = (percentage < 100 && missingFields.length > 0)
      ? `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">The following information is still required:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => '<li>' + f + '</li>').join('')}
           </ul>
         </div>` : '';

    const modalHtml = `
      <div id="access-control-modal" style="
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);
        padding:24px;animation:acmFadeIn 0.3s ease;
      ">
        <div style="
          background:white;border-radius:16px;
          width:100%;max-width:440px;
          box-shadow:0 20px 60px rgba(0,0,0,0.25);
          overflow:hidden;animation:acmSlideUp 0.35s ease;
        ">
          <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <div style="margin-bottom:8px;">
              <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/>
              </svg>
            </div>
            <div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
              <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="${percentage >= 40 ? '#c79a2b' : '#8b2e2e'}" stroke-width="3"
                  stroke-dasharray="${percentage} ${100 - percentage}" stroke-linecap="round"/>
              </svg>
              <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#0f5c3a;">${percentage}%</span>
            </div>
            <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#0f5c3a;margin:0 0 10px;">${title}</h4>
            <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">${message}</p>
            ${missingHtml}
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="acm-cancel-btn" style="padding:10px 22px;border-radius:8px;border:1.5px solid #d9e3de;background:white;color:#6f7f78;font-size:0.85rem;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="acm-primary-btn" style="padding:10px 22px;border-radius:8px;border:none;background:linear-gradient(135deg,#0f5c3a,#2f8a60);color:white;font-size:0.85rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(15,92,58,0.3);">${primaryLabel}</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('access-control-modal');
    document.getElementById('acm-primary-btn').addEventListener('click', () => { window.location.href = '<?= url('/user/profile') ?>'; });
    document.getElementById('acm-cancel-btn').addEventListener('click', () => {
      modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards';
      setTimeout(() => modal.remove(), 200);
    });
    modal.addEventListener('click', e => {
      if (e.target === modal) { modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards'; setTimeout(() => modal.remove(), 200); }
    });
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

  // ── Submit button ──
  document.getElementById('submit-btn').addEventListener('click', (e) => {
    e.preventDefault();
    const d1 = document.getElementById('decl1').checked;
    if (!d1) {
      showToast('⚠️ Please check the declaration box before submitting.', '#8b2e2e');
      return;
    }
    addRequest({ type: 'female_counseling', user: user.id });

    const pageBody = document.querySelector('.page-body');
    pageBody.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px 20px;">
        <div style="background:white;border-radius:14px;padding:48px 40px;border:1px solid var(--border);box-shadow:0 2px 12px rgba(0,0,0,0.06);max-width:480px;width:100%;">
          <div style="margin-bottom:20px;">
            <svg viewBox="0 0 24 24" style="width:56px;height:56px;fill:var(--accent);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          </div>
          <h3 style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--primary-dark);margin:0 0 12px;">Request Submitted</h3>
          <p style="font-size:1rem;color:var(--warning);font-weight:700;margin:0 0 10px;">Pending... please wait for confirmation.</p>
          <p style="font-size:0.85rem;color:var(--text-muted);line-height:1.7;margin:0 0 24px;">Your counseling request has been received. A counselor will review your request and schedule your session.</p>
          <a href="<?= url('/user/dashboard') ?>" style="display:inline-block;padding:10px 24px;border-radius:8px;background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));color:white;font-size:0.85rem;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(23,107,69,0.25);">Return to Dashboard</a>
        </div>
      </div>
    `;
  });

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + bg + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:Source Sans 3,sans-serif;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
  }
</script>
</body>
</html>
