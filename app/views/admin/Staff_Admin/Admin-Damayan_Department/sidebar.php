<aside class="sidebar" id="sidebar" style="--active-accent: #176b45;">
  <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
    <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" /></svg>
  </button>
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
      <div class="brand-text"><strong>ISCAG MIS</strong><span>Damayan Department</span></div>
    </div>
  </div>
  <div class="sidebar-user" onclick="window.location.href='<?= url('/admin/damayan/profile') ?>'" style="cursor:pointer; transition: background 0.2s;" title="View Profile">
    <div class="user-avatar" id="nav-avatar" data-preserve-avatar style="background:#176b45;">
      <?= strtoupper(substr($dbUser['first_name'] ?? 'D', 0, 1) . substr($dbUser['last_name'] ?? 'M', 0, 1)) ?>
    </div>
    <div class="user-info">
      <strong id="nav-name" data-preserve="true" data-force-sync="true"><?= htmlspecialchars(trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'Damayan Staff')) ?></strong>
      <span id="nav-role" data-preserve="true" data-preserve-role>Damayan Manager</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Admin</div>
    <a href="<?= url('/admin/damayan') ?>" class="nav-item <?= ($active_page ?? '') == 'dashboard' ? 'active' : '' ?>" data-tooltip="Dashboard">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
      <span class="nav-item-label">Dashboard</span>
    </a>
    <a href="<?= url('/admin/damayan/analytics') ?>" class="nav-item <?= ($active_page ?? '') == 'analytics' ? 'active' : '' ?>" data-tooltip="Department Analytics">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      <span class="nav-item-label">Analytics</span>
    </a>
    <a href="<?= url('/admin/damayan/profile') ?>" class="nav-item <?= ($active_page ?? '') == 'profile' ? 'active' : '' ?>" data-tooltip="Profile">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" /></svg>
      <span class="nav-item-label">My Profile</span>
    </a>

    <div class="nav-section-label">Services</div>
    <?php
      $__ap = $active_page ?? '';
      $__burialPages  = ['burial_requests', 'encoded_certificates', 'burial'];
      $__charityPages = ['charity', 'charity_donations', 'charity_available', 'charity_allocations', 'charity_distributions', 'charity_reports'];
      $__burialOpen   = in_array($__ap, $__burialPages, true);
      $__charityOpen  = in_array($__ap, $__charityPages, true);
    ?>

    <!-- Burial Service Dropdown -->
    <div class="nav-dropdown-wrap <?= $__burialOpen ? 'open' : '' ?>" id="nav-wrap-burial">
      <button type="button" class="nav-dropdown-trigger <?= $__burialOpen ? 'open' : '' ?>" id="burial-trigger" data-menu="nav-dd-burial" data-href="<?= url('/admin/damayan/burial-requests') ?>" data-tooltip="Burial Service" aria-expanded="<?= $__burialOpen ? 'true' : 'false' ?>" aria-controls="nav-dd-burial">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
        <span class="nav-item-label">Burial Service</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
      </button>
      <div class="nav-dropdown <?= $__burialOpen ? 'open' : '' ?>" id="nav-dd-burial">
        <a href="<?= url('/admin/damayan/burial-requests') ?>" class="<?= $__ap === 'burial_requests' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          Burial Request
        </a>
        <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="<?= $__ap === 'encoded_certificates' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zm-2 9l-3-3 1.41-1.41L11 15.17l4.59-4.59L17 12l-6 6z"/></svg>
          Registered Certificate
        </a>
        <a href="<?= url('/admin/damayan/burial') ?>" class="<?= $__ap === 'burial' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/></svg>
          Burial Records
        </a>
      </div>
    </div>

    <!-- Charity Programs Dropdown -->
    <div class="nav-dropdown-wrap <?= $__charityOpen ? 'open' : '' ?>" id="nav-wrap-charity">
      <button type="button" class="nav-dropdown-trigger <?= $__charityOpen ? 'open' : '' ?>" id="charity-trigger" data-menu="nav-dd-charity" data-href="<?= url('/admin/damayan/charity') ?>" data-tooltip="Charity Programs" aria-expanded="<?= $__charityOpen ? 'true' : 'false' ?>" aria-controls="nav-dd-charity">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        <span class="nav-item-label">Charity Programs</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
      </button>
      <div class="nav-dropdown <?= $__charityOpen ? 'open' : '' ?>" id="nav-dd-charity">
        <a href="<?= url('/admin/damayan/charity') ?>" class="<?= $__ap === 'charity' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
          Charity Dashboard
        </a>
        <a href="<?= url('/admin/damayan/charity/donations') ?>" class="<?= $__ap === 'charity_donations' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          Donation Records
        </a>
        <a href="<?= url('/admin/damayan/charity/available') ?>" class="<?= $__ap === 'charity_available' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          Available Donations
        </a>
        <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="<?= $__ap === 'charity_allocations' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"/></svg>
          Donation Allocation
        </a>
        <a href="<?= url('/admin/damayan/charity/distributions') ?>" class="<?= $__ap === 'charity_distributions' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M18 11.2h-3.2V8c0-.66-.54-1.2-1.2-1.2h-3.2v3.2H7.2c-.66 0-1.2.54-1.2 1.2v3.2h3.2v3.2c0 .66.54 1.2 1.2 1.2h3.2v-3.2H18c.66 0 1.2-.54 1.2-1.2v-3.2c0-.66-.54-1.2-1.2-1.2z"/></svg>
          Distribution Records
        </a>
        <a href="<?= url('/admin/damayan/charity/reports') ?>" class="<?= in_array($__ap, ['charity_reports', 'charity_report']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; flex-shrink:0; opacity:0.7;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
          Charity Reports
        </a>
      </div>
    </div>
    <a href="<?= url('/admin/damayan/finance') ?>" class="nav-item <?= ($active_page ?? '') == 'finance' ? 'active' : '' ?>" data-tooltip="Financial Management">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
      <span class="nav-item-label">Finance</span>
    </a>

    <a href="<?= url('/admin/damayan/notifications') ?>" class="nav-item <?= ($active_page ?? '') == 'notifications' ? 'active' : '' ?>" data-tooltip="Notifications">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" /></svg>
      <span class="nav-item-label">Notifications</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" /></svg>
      <span class="nav-item-label">Logout</span>
    </a>
  </div>
</aside>


