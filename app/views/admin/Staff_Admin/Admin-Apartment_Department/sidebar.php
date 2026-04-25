<aside class="sidebar" id="sidebar">
  <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
    <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" /></svg>
  </button>
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
      <div class="brand-text"><strong>ISCAG MIS</strong><span>Apartment Staff</span></div>
    </div>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">
      <?= strtoupper(substr($dbUser['first_name'] ?? 'A', 0, 1) . substr($dbUser['last_name'] ?? 'S', 0, 1)) ?>
    </div>
    <div class="user-info">
      <strong id="nav-name"><?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Apartment Staff' ?></strong>
      <span>Staff Admin</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Admin</div>
    <a href="<?= url('/admin/apartment') ?>" class="nav-item <?= ($active_page ?? '') == 'dashboard' ? 'active' : '' ?>" data-tooltip="Dashboard">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
      <span class="nav-item-label">Dashboard</span>
    </a>
    <a href="<?= url('/admin/apartment/profile') ?>" class="nav-item <?= ($active_page ?? '') == 'profile' ? 'active' : '' ?>" data-tooltip="Profile">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" /></svg>
      <span class="nav-item-label">My Profile</span>
    </a>
    <a href="<?= url('/admin/apartment/confirmation') ?>" class="nav-item <?= ($active_page ?? '') == 'confirmation' ? 'active' : '' ?>" data-tooltip="Applications">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" /></svg>
      <span class="nav-item-label">Review Applications</span>
    </a>
    <div class="nav-section-label">Management</div>
    <a href="<?= url('/admin/apartment/info') ?>" class="nav-item <?= ($active_page ?? '') == 'info' ? 'active' : '' ?>" data-tooltip="Apartment Info">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" /></svg>
      <span class="nav-item-label">Apartment Info</span>
    </a>
    <a href="<?= url('/admin/apartment/tenants') ?>" class="nav-item <?= ($active_page ?? '') == 'tenants' ? 'active' : '' ?>" data-tooltip="Tenant Info">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      <span class="nav-item-label">Tenant Info</span>
    </a>
    <a href="<?= url('/admin/payment') ?>" class="nav-item" data-tooltip="Billing & Payment">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" /></svg>
      <span class="nav-item-label">Billing & Payment</span>
    </a>
    <a href="<?= url('/admin/apartment/soa') ?>" class="nav-item" data-tooltip="SOA">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM9 13h6v2H9v-2zm6 4H9v2h6v-2z" /></svg>
      <span class="nav-item-label">Statement of Account</span>
    </a>
    <a href="<?= url('/admin/apartment/notifications') ?>" class="nav-item" data-tooltip="Notifications">
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
