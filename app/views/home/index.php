<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Navbar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-900: #14532d;
      --green-800: #166534;
      --green-700: #15803d;
      --green-600: #16a34a;
      --green-100: #dcfce7;
      --green-50:  #f0fdf4;
      --gold:      #b7973a;
      --gold-light:#e8d48b;
      --text-main: #1a1a1a;
      --text-muted: #6b7280;
      --text-light: #9ca3af;
      --border:    #e5e7eb;
      --white:     #ffffff;
      --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      --shadow-md: 0 4px 20px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.05);
      --shadow-lg: 0 12px 40px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
      --radius-xl: 16px;
      --radius-lg: 12px;
      --radius-md: 8px;
      --transition: 220ms cubic-bezier(.4,0,.2,1);
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #f8f7f4;
      min-height: 100vh;
    }

    /* ─── HERO DEMO AREA ───────────────────────────────────────── */
    .demo-hero {
      padding-top: 120px;
      text-align: center;
      color: var(--text-muted);
    }
    .demo-hero h1 {
      font-family: 'Lora', serif;
      font-size: 2rem;
      color: var(--green-800);
      margin-bottom: 8px;
    }
    .demo-hero p { font-size: .95rem; }

    /* ─── NAVBAR ───────────────────────────────────────────────── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 999;
      background: var(--white);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      height: 68px;
    }

    .nav-inner {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 28px;
      height: 100%;
      display: flex;
      align-items: center;
      gap: 0;
    }

    /* LOGO */
    .logo {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none; flex-shrink: 0;
      margin-right: 32px;
    }
    .logo-icon {
      width: 36px; height: 36px;
      background: var(--green-800);
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .logo-icon svg { width: 20px; height: 20px; fill: white; }
    .logo-text {
      font-family: 'Lora', serif;
      font-size: .78rem;
      font-weight: 600;
      color: var(--green-800);
      line-height: 1.3;
      max-width: 180px;
    }

    /* CENTER NAV */
    .nav-links {
      display: flex; align-items: center; gap: 2px;
      flex: 1; justify-content: center;
    }

    .nav-item {
      position: relative;
    }

    .nav-link {
      display: flex; align-items: center; gap: 5px;
      padding: 8px 14px;
      font-size: .875rem;
      font-weight: 500;
      color: var(--text-muted);
      text-decoration: none;
      border-radius: var(--radius-md);
      transition: color var(--transition), background var(--transition);
      cursor: pointer;
      user-select: none;
      white-space: nowrap;
    }
    .nav-link:hover, .nav-link.active { color: var(--green-800); }
    .nav-link.active { background: var(--green-50); }

    .nav-link .chevron {
      width: 14px; height: 14px;
      transition: transform var(--transition);
      opacity: .5;
    }
    .nav-item:hover .chevron,
    .nav-item.open .chevron { transform: rotate(180deg); opacity: 1; }

    /* green underline on active */
    .nav-link.active::after {
      content: '';
      position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%);
      width: 20px; height: 2.5px;
      background: var(--green-700);
      border-radius: 99px;
    }

    /* ─── DROPDOWN BASE ────────────────────────────────────────── */
    .dropdown {
      position: absolute;
      top: calc(100% + 12px);
      left: 50%; transform: translateX(-50%);
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      pointer-events: none;
      opacity: 0;
      transform: translateX(-50%) translateY(-8px);
      transition: opacity var(--transition) 150ms, transform var(--transition) 150ms;
      z-index: 100;
      min-width: 200px;
    }
    .dropdown::after {
      content: '';
      position: absolute;
      top: -15px; left: 0; right: 0; height: 15px;
      background: transparent;
    }
    .nav-item:hover .dropdown,
    .nav-item.open .dropdown {
      opacity: 1;
      pointer-events: all;
      transform: translateX(-50%) translateY(0);
      transition: opacity var(--transition) 0ms, transform var(--transition) 0ms;
    }

    /* ─── SIMPLE DROPDOWN (About / Community / Login) ─────────── */
    .dropdown-simple { padding: 8px; }
    .dropdown-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px;
      border-radius: var(--radius-md);
      text-decoration: none;
      color: var(--text-main);
      font-size: .875rem;
      font-weight: 400;
      transition: background var(--transition), color var(--transition);
      cursor: pointer;
    }
    .dropdown-item:hover { background: var(--green-50); color: var(--green-800); }
    .dropdown-item:hover .item-icon { background: var(--green-100); color: var(--green-700); }

    .item-icon {
      width: 30px; height: 30px; flex-shrink: 0;
      background: #f3f4f6;
      border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
      font-size: .9rem;
      color: var(--text-muted);
      transition: background var(--transition), color var(--transition);
    }

    .item-text-wrap { display: flex; flex-direction: column; gap: 1px; }
    .item-title { font-weight: 500; font-size: .85rem; line-height: 1.2; }
    .item-desc { font-size: .75rem; color: var(--text-light); line-height: 1.3; }

    /* ─── DEPT DROPDOWN (2-col mega) ──────────────────────────── */
    .dropdown-dept {
      min-width: 600px;
      padding: 16px;
      left: 50%; transform: translateX(-50%);
    }

    .dept-layout {
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 16px;
      transition: all var(--transition);
    }

    /* image panel */
    .dept-image-panel {
      border-radius: var(--radius-lg);
      overflow: hidden;
      position: relative;
      height: 220px;
      flex-shrink: 0;
      transition: order var(--transition);
    }
    .dept-image-panel img {
      width: 100%; height: 100%;
      object-fit: cover;
      transition: transform 400ms ease;
    }
    .dept-image-panel:hover img { transform: scale(1.03); }
    .dept-img-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(22,101,52,.45) 0%, rgba(0,0,0,.15) 100%);
    }
    .dept-img-label {
      position: absolute; bottom: 12px; left: 14px;
      font-family: 'Lora', serif;
      font-size: .8rem; font-weight: 600;
      color: white;
      letter-spacing: .03em;
    }

    /* content panel */
    .dept-content-panel {
      display: flex; flex-direction: column; gap: 4px;
      justify-content: center;
    }
    .dept-panel-label {
      font-size: .7rem; font-weight: 600;
      text-transform: uppercase; letter-spacing: .08em;
      color: var(--text-light);
      margin-bottom: 6px;
      padding: 0 4px;
    }

    .dept-item {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 9px 10px;
      border-radius: var(--radius-md);
      cursor: pointer;
      text-decoration: none;
      transition: background var(--transition);
    }
    .dept-item:hover { background: var(--green-50); }
    .dept-item:hover .dept-item-icon { background: var(--green-100); color: var(--green-700); }

    .dept-item-icon {
      width: 34px; height: 34px; flex-shrink: 0;
      background: #f3f4f6;
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
      transition: background var(--transition), color var(--transition);
    }
    .dept-item-body { flex: 1; }
    .dept-item-title {
      font-size: .875rem; font-weight: 500;
      color: var(--text-main); line-height: 1.2;
    }
    .dept-item-desc {
      font-size: .75rem; color: var(--text-muted);
      line-height: 1.4; margin-top: 2px;
    }

    /* ─── RIGHT: LOGIN BUTTON ──────────────────────────────────── */
    .nav-right {
      flex-shrink: 0;
      margin-left: 24px;
    }
    .login-btn {
      display: flex; align-items: center; gap: 6px;
      padding: 8px 18px;
      background: var(--green-800);
      color: white;
      font-size: .875rem; font-weight: 500;
      border-radius: var(--radius-md);
      border: none; cursor: pointer;
      transition: all var(--transition);
      font-family: 'DM Sans', sans-serif;
    }
    .login-btn:hover { 
      background: var(--green-700); 
      transform: translateY(-2px); 
      box-shadow: 0 6px 20px rgba(20, 83, 45, 0.25); 
      color: white;
    }
    
    .register-btn {
      display: flex; align-items: center; gap: 6px;
      padding: 8px 18px;
      background: var(--white);
      color: var(--green-800);
      font-size: .875rem; font-weight: 600;
      border-radius: var(--radius-md);
      border: 1.5px solid var(--green-800);
      cursor: pointer;
      transition: all var(--transition);
      font-family: 'DM Sans', sans-serif;
    }
    .register-btn:hover { 
      background: var(--green-800); 
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(20, 83, 45, 0.15);
    }

    .login-btn svg, .register-btn svg { width: 15px; height: 15px; }
    .item-svg { width: 18px; height: 18px; }

    /* LOGIN dropdown aligned right */
    .dropdown-login {
      left: auto; right: 0; transform: translateY(-8px);
      min-width: 200px;
    }
    .nav-item:hover .dropdown-login,
    .nav-item.open .dropdown-login {
      transform: translateY(0);
    }

    /* divider */
    .dropdown-divider {
      height: 1px; background: var(--border);
      margin: 4px 8px;
    }

    /* ─── ARROW TIP on dropdowns ─────────────────────────────── */
    .dropdown::before {
      content: '';
      position: absolute; top: -6px; left: 50%; transform: translateX(-50%);
      width: 11px; height: 11px;
      background: var(--white);
      border-left: 1px solid var(--border);
      border-top: 1px solid var(--border);
      transform: translateX(-50%) rotate(45deg);
    }
    .dropdown-login::before { left: auto; right: 24px; transform: rotate(45deg); }

  </style>
</head>
<body>

<!-- ══════════════════════ NAVBAR ══════════════════════ -->
<nav>
  <div class="nav-inner">

    <!-- LOGO -->
    <a href="#" class="logo">
      <div class="logo-icon">
        <img src="<?= asset('assets/logo.jpg') ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: 9px; object-fit: cover;">
      </div>
      <span class="logo-text">Islamic Studies, Call and Guidance of the Philippines</span>
    </a>

    <!-- CENTER LINKS -->
    <ul class="nav-links" style="list-style:none;">

      <!-- HOME -->
      <li class="nav-item">
        <a href="#" class="nav-link active">Home</a>
      </li>

      <!-- ABOUT -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          About
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Mission & Vision</span>
              <span class="item-desc">Our purpose and guiding principles</span>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">History</span>
              <span class="item-desc">How ISCAG was founded</span>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Organization</span>
              <span class="item-desc">Leadership and structure</span>
            </span>
          </a>
        </div>
      </li>

      <!-- DEPARTMENT (MEGA) -->
      <li class="nav-item" id="deptNav">
        <a class="nav-link" href="#">
          Department
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>

        <div class="dropdown dropdown-dept">
          <div class="dept-layout" id="deptLayout" onmouseleave="swapLayout(false)">

            <!-- IMAGE PANEL -->
            <div class="dept-image-panel">
              <img
                src="<?= asset('assets/hero-mosque.png') ?>"
                alt="Department Preview"
                id="deptPreviewImg"
              />
              <div class="dept-img-overlay"></div>
              <span class="dept-img-label">ISCAG Departments</span>
            </div>

            <!-- CONTENT PANEL -->
            <div class="dept-content-panel">
              <p class="dept-panel-label">Our Services</p>

              <a href="#" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/1BR Type/1BR front.jpg') ?>', 'Apartment Services')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M3 21h18"/><path d="M9 21V9l-4 2v10"/><path d="M15 21V3l-4 2v16"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Apartment</span>
                  <span class="dept-item-desc">Apply and manage housing units</span>
                </span>
              </a>

              <a href="#" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/about-center.png') ?>', 'Damayan Support')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Damayan</span>
                  <span class="dept-item-desc">Burial and bereavement support services</span>
                </span>
              </a>

              <a href="#" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/hero-mosque.png') ?>', 'Daawah Programs')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Daawah</span>
                  <span class="dept-item-desc">Islamic programs and guidance</span>
                </span>
              </a>

            </div>
          </div>
        </div>
      </li>

      <!-- COMMUNITY -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          Community
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Events</span>
              <span class="item-desc">Upcoming gatherings and programs</span>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Announcements</span>
              <span class="item-desc">Latest news from ISCAG</span>
            </span>
          </a>
          <a href="#" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Volunteer</span>
              <span class="item-desc">Join our community efforts</span>
            </span>
          </a>
        </div>
      </li>

      <!-- CONTACT -->
      <li class="nav-item">
        <a href="#" class="nav-link">Contact</a>
      </li>

    </ul>

    <!-- RIGHT: LOGIN & REGISTER -->
    <div class="nav-right" style="display: flex; align-items: center; gap: 12px;">
      <a href="<?= url('/register') ?>" class="register-btn" style="text-decoration: none;">Register</a>
      <a href="<?= url('/login') ?>" class="login-btn" style="text-decoration: none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        Login
      </a>
    </div>

  </div>
</nav>

<!-- ══════════ DEMO HERO ══════════ -->
<div class="demo-hero">
  <h1>ISCAG Navigation Preview</h1>
  <p>Hover over the navbar items to explore dropdowns.</p>
</div>

<!-- ══════════ SCRIPT ══════════ -->
<script>
  function swapLayout(active, imgSrc = null, imgLabel = null) {
    const previewImg = document.getElementById('deptPreviewImg');
    const labelSpan = document.querySelector('.dept-img-label');
    
    if (active) {
      if (imgSrc) previewImg.src = imgSrc;
      if (imgLabel) labelSpan.textContent = imgLabel;
    } else {
      // Optional: Reset to default when not hovering any item
      previewImg.src = "<?= asset('assets/hero-mosque.png') ?>";
      labelSpan.textContent = "ISCAG Departments";
    }
  }

  // Add smooth transition to dept layout grid columns
  const deptLayout = document.getElementById('deptLayout');
  deptLayout.style.transition = 'grid-template-columns 280ms cubic-bezier(.4,0,.2,1)';

  // Make nav-link active highlight follow clicks
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => {
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });
</script>
</body>
</html>
