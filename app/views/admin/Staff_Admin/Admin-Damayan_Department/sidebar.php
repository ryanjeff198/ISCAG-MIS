<aside class="sidebar" id="sidebar" style="--active-accent: #dc2626;">
  <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
    <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" /></svg>
  </button>
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
      <div class="brand-text"><strong>ISCAG MIS</strong><span>Damayan Department</span></div>
    </div>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar" id="nav-avatar" data-preserve-avatar style="background:#dc2626;">
      <?= strtoupper(substr($dbUser['first_name'] ?? 'D', 0, 1) . substr($dbUser['last_name'] ?? 'M', 0, 1)) ?>
    </div>
    <div class="user-info">
      <strong id="nav-name"><?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Damayan Staff' ?></strong>
      <span id="nav-role" data-preserve-role>Damayan Manager</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Admin</div>
    <a href="<?= url('/admin/damayan') ?>" class="nav-item <?= ($active_page ?? '') == 'dashboard' ? 'active' : '' ?>" data-tooltip="Dashboard">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
      <span class="nav-item-label">Dashboard</span>
    </a>
    <a href="<?= url('/admin/damayan/profile') ?>" class="nav-item <?= ($active_page ?? '') == 'profile' ? 'active' : '' ?>" data-tooltip="Profile">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" /></svg>
      <span class="nav-item-label">My Profile</span>
    </a>

    <div class="nav-section-label">Services</div>
    <a href="<?= url('/admin/damayan/burial') ?>" class="nav-item <?= ($active_page ?? '') == 'burial' ? 'active' : '' ?>" data-tooltip="Burial Records">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
      <span class="nav-item-label">Burial Records</span>
    </a>
    <a href="<?= url('/admin/damayan/charity') ?>" class="nav-item <?= ($active_page ?? '') == 'charity' ? 'active' : '' ?>" data-tooltip="Charity Programs">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      <span class="nav-item-label">Charity Programs</span>
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
