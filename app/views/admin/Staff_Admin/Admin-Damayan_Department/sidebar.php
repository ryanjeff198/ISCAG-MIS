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
      $__charityPages = ['charity', 'charity_donations', 'charity_available', 'charity_allocations', 'charity_distributions'];
      $__burialOpen   = in_array($__ap, $__burialPages, true);
      $__charityOpen  = in_array($__ap, $__charityPages, true);
    ?>

    <!-- Burial Service Dropdown -->
    <div class="nav-dropdown-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $__burialOpen ? 'open' : '' ?>" data-menu="nav-dd-burial" data-href="<?= url('/admin/damayan/burial-requests') ?>" data-tooltip="Burial Service" aria-expanded="<?= $__burialOpen ? 'true' : 'false' ?>" aria-controls="nav-dd-burial">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
        <span class="nav-item-label">Burial Service</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
      </button>
      <div class="nav-dropdown <?= $__burialOpen ? 'open' : '' ?>" id="nav-dd-burial">
        <a href="<?= url('/admin/damayan/burial-requests') ?>" class="<?= $__ap === 'burial_requests' ? 'active-submenu' : '' ?>">Burial Request</a>
        <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="<?= $__ap === 'encoded_certificates' ? 'active-submenu' : '' ?>">Registered Certificate</a>
        <a href="<?= url('/admin/damayan/burial') ?>" class="<?= $__ap === 'burial' ? 'active-submenu' : '' ?>">Burial Records</a>
      </div>
    </div>

    <!-- Charity Programs Dropdown -->
    <div class="nav-dropdown-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $__charityOpen ? 'open' : '' ?>" data-menu="nav-dd-charity" data-href="<?= url('/admin/damayan/charity') ?>" data-tooltip="Charity Programs" aria-expanded="<?= $__charityOpen ? 'true' : 'false' ?>" aria-controls="nav-dd-charity">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        <span class="nav-item-label">Charity Programs</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
      </button>
      <div class="nav-dropdown <?= $__charityOpen ? 'open' : '' ?>" id="nav-dd-charity">
        <a href="<?= url('/admin/damayan/charity') ?>" class="<?= $__ap === 'charity' ? 'active-submenu' : '' ?>">Charity Dashboard</a>
        <a href="<?= url('/admin/damayan/charity/donations') ?>" class="<?= $__ap === 'charity_donations' ? 'active-submenu' : '' ?>">Donation Records</a>
        <a href="<?= url('/admin/damayan/charity/available') ?>" class="<?= $__ap === 'charity_available' ? 'active-submenu' : '' ?>">Available Donations</a>
        <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="<?= $__ap === 'charity_allocations' ? 'active-submenu' : '' ?>">Donation Allocation</a>
        <a href="<?= url('/admin/damayan/charity/distributions') ?>" class="<?= $__ap === 'charity_distributions' ? 'active-submenu' : '' ?>">Distribution Records</a>
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
