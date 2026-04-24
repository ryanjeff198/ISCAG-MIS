<!-- ══════════════════════ NAVBAR ══════════════════════ -->
<nav>
  <div class="nav-inner">

    <!-- LOGO -->
    <a href="<?= url('/') ?>" class="logo">
      <div class="logo-icon">
        <img src="<?= asset('assets/logo.jpg') ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: 9px; object-fit: cover;">
      </div>
      <span class="logo-text">Islamic Studies, Call and Guidance of the Philippines</span>
    </a>

    <!-- CENTER LINKS -->
    <ul class="nav-links" style="list-style:none;">

      <!-- ABOUT -->
      <li class="nav-item">
        <a class="nav-link <?= (isset($active_page) && $active_page == 'about' || (isset($active_page) && $active_page == 'home')) ? 'active' : '' ?>" href="<?= url('/') ?>">
          About
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="<?= url('/') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Overview</span>
              <span class="item-desc">Welcome to ISCAG</span>
            </span>
          </a>
          <a href="<?= url('/') ?>#mission-vision" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Mission & Vision</span>
              <span class="item-desc">Our purpose and guiding principles</span>
            </span>
          </a>
          <a href="<?= url('/history-organization') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">History & Organization</span>
              <span class="item-desc">Our heritage and leadership</span>
            </span>
          </a>
        </div>
      </li>

      <!-- DEPARTMENT (MEGA) -->
      <li class="nav-item" id="deptNav">
        <a class="nav-link <?= (isset($active_page) && $active_page == 'department') ? 'active' : '' ?>" href="<?= url('/departments') ?>">
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

              <a href="<?= url('/apartment') ?>" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/1BR Type/1BR front.jpg') ?>', 'Apartment Services')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M3 21h18"/><path d="M9 21V9l-4 2v10"/><path d="M15 21V3l-4 2v16"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Apartment</span>
                  <span class="dept-item-desc">Apply and manage housing units</span>
                </span>
              </a>

              <a href="<?= url('/damayan') ?>" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/about-center.png') ?>', 'Damayan Support')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Damayan</span>
                  <span class="dept-item-desc">Burial and bereavement support services</span>
                </span>
              </a>

              <a href="<?= url('/daawah') ?>" class="dept-item"
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
        <a class="nav-link <?= (isset($active_page) && $active_page == 'community') ? 'active' : '' ?>" href="<?= url('/events') ?>">
          Community
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="<?= url('/events') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Events</span>
              <span class="item-desc">Upcoming gatherings and programs</span>
            </span>
          </a>
          <a href="<?= url('/announcements') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Announcements</span>
              <span class="item-desc">Latest news from ISCAG</span>
            </span>
          </a>
        </div>
      </li>

      <!-- CONTACT -->
      <li class="nav-item">
        <a href="<?= url('/contact') ?>" class="nav-link <?= (isset($active_page) && $active_page == 'contact') ? 'active' : '' ?>">Contact</a>
      </li>

    </ul>

    <!-- RIGHT: LOGIN & REGISTER -->
    <div class="nav-right">
      <a href="<?= url('/login') ?>" class="btn-login">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Log In
      </a>
      <a href="<?= url('/register') ?>" class="btn-register">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
        Register
      </a>
    </div>

  </div>
</nav>
