<aside class="sidebar" id="sidebar" style="--active-accent: <?= ($dawah_type ?? '') == 'female' ? '#d4af37' : '#14532d' ?>;">
  <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
    <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" /></svg>
  </button>
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
      <div class="brand-text">
        <strong>ISCAG MIS</strong>
        <span>Da'wah Department</span>
      </div>
    </div>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar" id="nav-avatar" data-preserve-avatar style="background:<?= ($dawah_type ?? '') == 'female' ? '#d4af37' : '#14532d' ?>;">
      <?= strtoupper(substr($dbUser['first_name'] ?? 'D', 0, 1) . substr($dbUser['last_name'] ?? 'A', 0, 1)) ?>
    </div>
    <div class="user-info">
      <strong id="nav-name"><?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Da\'wah Staff' ?></strong>
      <span id="nav-role" data-preserve-role><?= ($dawah_type ?? '') == 'female' ? 'Female Da\'wah Manager' : 'Male Da\'wah Manager' ?></span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Department Dashboard</div>
    <?php if (($dawah_type ?? '') == 'male'): ?>
    <a href="<?= url('/admin/dawah/male') ?>" class="nav-item <?= ($active_page ?? '') == 'dashboard' ? 'active' : '' ?>" data-tooltip="Male Da'wah Dashboard">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
      <span class="nav-item-label">Male Da'wah</span>
    </a>
    <?php endif; ?>
    <?php if (($dawah_type ?? '') == 'female'): ?>
    <a href="<?= url('/admin/dawah/female') ?>" class="nav-item <?= ($active_page ?? '') == 'dashboard' ? 'active' : '' ?>" data-tooltip="Female Da'wah Dashboard">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" /></svg>
      <span class="nav-item-label">Female Da'wah</span>
    </a>
    <?php endif; ?>

    <div class="nav-section-label">Analysis & Profile</div>
    <a href="<?= url('/admin/dawah/analytics') ?>" class="nav-item <?= ($active_page ?? '') == 'analytics' ? 'active' : '' ?>" data-tooltip="Department Analytics">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      <span class="nav-item-label">Analytics</span>
    </a>
    <a href="<?= url(($dawah_type ?? '') == 'female' ? '/admin/dawah/female/profile' : '/admin/dawah/male/profile') ?>" class="nav-item <?= ($active_page ?? '') == 'profile' ? 'active' : '' ?>" data-tooltip="Profile Settings">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" /></svg>
      <span class="nav-item-label">My Profile</span>
    </a>

    <div class="nav-section-label">Management Services</div>
    <a href="<?= url('/admin/dawah/counseling') ?>" class="nav-item <?= ($active_page ?? '') == 'counseling' ? 'active' : '' ?>" data-tooltip="Counseling Management">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
      <span class="nav-item-label">Counseling</span>
    </a>
    
    <?php if (($dawah_type ?? '') == 'male'): ?>
    <a href="<?= url('/admin/dawah/marriage') ?>" class="nav-item <?= ($active_page ?? '') == 'marriage' ? 'active' : '' ?>" data-tooltip="Marriage Services Management">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      <span class="nav-item-label">Marriage Records</span>
    </a>
    <?php endif; ?>

    <a href="<?= url('/admin/dawah/education') ?>" class="nav-item <?= ($active_page ?? '') == 'education' ? 'active' : '' ?>" data-tooltip="Islamic Education Module">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
      <span class="nav-item-label">Islamic Education</span>
    </a>

    <div class="nav-section-label">Operations</div>
    <a href="<?= url('/admin/dawah/notifications') ?>" class="nav-item <?= ($active_page ?? '') == 'notifications' ? 'active' : '' ?>" data-tooltip="System Notifications">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" /></svg>
      <span class="nav-item-label">Notifications</span>
    </a>
    <a href="<?= url('/admin/dawah/schedule') ?>" class="nav-item <?= ($active_page ?? '') == 'schedule' ? 'active' : '' ?>" data-tooltip="Service Schedule Calendar">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
      <span class="nav-item-label">Schedule</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout of Session">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" /></svg>
      <span class="nav-item-label">Logout</span>
    </a>
  </div>
</aside>
