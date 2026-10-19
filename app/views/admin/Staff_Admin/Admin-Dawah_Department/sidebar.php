<?php
$name_parts = explode(' ', trim($dbUser['first_name'] ?? 'Da\'wah') . ' ' . trim($dbUser['last_name'] ?? 'Staff'));
$first_initial = substr($name_parts[0] ?? 'D', 0, 1);
$last_initial = count($name_parts) > 1 ? substr(end($name_parts), 0, 1) : '';
$initials = strtoupper($first_initial . $last_initial);
?>
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
      <?= $initials ?>
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
    
    <!-- COUNSELING DROPDOWN -->
    <?php $counseling_active = in_array($active_page ?? '', ['counseling', 'counseling_requests', 'counseling_request'], true); ?>
    <div class="nav-dropdown-wrap <?= $counseling_active ? 'open' : '' ?>" id="counseling-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $counseling_active ? 'open' : '' ?>" id="counseling-trigger" data-menu="counseling-menu" data-href="<?= url('/admin/dawah/counseling') ?>" data-tooltip="Counseling">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
        </svg>
        <span class="nav-item-label">Counseling</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
          <path d="M7 10l5 5 5-5z" />
        </svg>
      </button>
      <div class="nav-dropdown <?= $counseling_active ? 'open' : '' ?>" id="counseling-menu">
        <a href="<?= url('/admin/dawah/counseling') ?>" class="<?= ($active_page ?? '') === 'counseling' ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
          </svg>
          Counseling Management
        </a>
        <a href="<?= url('/admin/dawah/counseling/requests') ?>" class="<?= in_array($active_page ?? '', ['counseling_requests', 'counseling_request']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
          Counseling Request
        </a>
      </div>
    </div>

    <a href="<?= url('/admin/dawah/schedule') ?>" class="nav-item <?= ($active_page ?? '') == 'schedule' ? 'active' : '' ?>" data-tooltip="Service Schedule Calendar">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
      <span class="nav-item-label">Schedule</span>
    </a>
    
    <?php if (($dawah_type ?? '') == 'male'): ?>
    <!-- MARRIAGE DROPDOWN -->
    <?php $marriage_active = in_array($active_page ?? '', ['marriage', 'marriage_records', 'marriage_requests', 'marriage_request'], true); ?>
    <div class="nav-dropdown-wrap <?= $marriage_active ? 'open' : '' ?>" id="marriage-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $marriage_active ? 'open' : '' ?>" id="marriage-trigger" data-menu="marriage-menu" data-href="<?= url('/admin/dawah/marriage') ?>" data-tooltip="Marriage Services">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="nav-item-label">Marriage</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
          <path d="M7 10l5 5 5-5z" />
        </svg>
      </button>
      <div class="nav-dropdown <?= $marriage_active ? 'open' : '' ?>" id="marriage-menu">
        <a href="<?= url('/admin/dawah/marriage') ?>" class="<?= in_array($active_page ?? '', ['marriage', 'marriage_records']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
          Marriage Records
        </a>
        <a href="<?= url('/admin/dawah/marriage/requests') ?>" class="<?= in_array($active_page ?? '', ['marriage_requests', 'marriage_request']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
          Marriage Request
        </a>
      </div>
    </div>

    <!-- CONVERSION DROPDOWN -->
    <?php $conversion_active = in_array($active_page ?? '', ['conversion', 'conversion_records', 'conversion_requests', 'conversion_request'], true); ?>
    <div class="nav-dropdown-wrap <?= $conversion_active ? 'open' : '' ?>" id="conversion-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $conversion_active ? 'open' : '' ?>" id="conversion-trigger" data-menu="conversion-menu" data-href="<?= url('/admin/dawah/conversion') ?>" data-tooltip="Conversion / Shahadah">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
        </svg>
        <span class="nav-item-label">Conversion</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
          <path d="M7 10l5 5 5-5z" />
        </svg>
      </button>
      <div class="nav-dropdown <?= $conversion_active ? 'open' : '' ?>" id="conversion-menu">
        <a href="<?= url('/admin/dawah/conversion') ?>" class="<?= in_array($active_page ?? '', ['conversion', 'conversion_records']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
          </svg>
          Conversion Records
        </a>
        <a href="<?= url('/admin/dawah/conversion/requests') ?>" class="<?= in_array($active_page ?? '', ['conversion_requests', 'conversion_request']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
          Conversion Request
        </a>
      </div>
    </div>
    <?php endif; ?>

    <!-- ISLAMIC EDUCATION DROPDOWN -->
    <?php $education_active = in_array($active_page ?? '', ['education', 'education_records', 'education_requests', 'education_request'], true); ?>
    <div class="nav-dropdown-wrap <?= $education_active ? 'open' : '' ?>" id="education-wrap">
      <button type="button" class="nav-dropdown-trigger <?= $education_active ? 'open' : '' ?>" id="education-trigger" data-menu="education-menu" data-href="<?= url('/admin/dawah/education') ?>" data-tooltip="Islamic Education">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
        </svg>
        <span class="nav-item-label">Islamic Education</span>
        <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
          <path d="M7 10l5 5 5-5z" />
        </svg>
      </button>
      <div class="nav-dropdown <?= $education_active ? 'open' : '' ?>" id="education-menu">
        <a href="<?= url('/admin/dawah/education') ?>" class="<?= in_array($active_page ?? '', ['education', 'education_records']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
          </svg>
          Education Records
        </a>
        <a href="<?= url('/admin/dawah/education/requests') ?>" class="<?= in_array($active_page ?? '', ['education_requests', 'education_request']) ? 'active-submenu' : '' ?>">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
          </svg>
          Education Request
        </a>
      </div>
    </div>

    <a href="<?= url('/admin/dawah/notifications') ?>" class="nav-item <?= ($active_page ?? '') == 'notifications' ? 'active' : '' ?>" data-tooltip="System Notifications">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" /></svg>
      <span class="nav-item-label">Notifications</span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout of Session">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" /></svg>
      <span class="nav-item-label">Logout</span>
    </a>
  </div>
</aside>

<script>
  (function() {
    function toggleDropdown(trigger, e) {
      if (!trigger) return;
      const menuId = trigger.getAttribute('data-menu');
      const menu = document.getElementById(menuId);
      const wrap = trigger.closest('.nav-dropdown-wrap');
      if (!menu || !wrap) return;

      const sidebar = document.getElementById('sidebar');
      if (sidebar && sidebar.classList.contains('collapsed')) {
        const href = trigger.getAttribute('data-href');
        if (href) { window.location.href = href; return; }
      }

      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }

      const isOpen = menu.classList.contains('open');

      // Close all dropdowns
      document.querySelectorAll('.nav-dropdown').forEach(function(m) { m.classList.remove('open'); });
      document.querySelectorAll('.nav-dropdown-trigger').forEach(function(b) { b.classList.remove('open'); });
      document.querySelectorAll('.nav-dropdown-wrap').forEach(function(w) { w.classList.remove('open'); });

      // If it wasn't open, open it
      if (!isOpen) {
        menu.classList.add('open');
        trigger.classList.add('open');
        wrap.classList.add('open');
      }
    }

    function setupAllDropdowns() {
      document.querySelectorAll('.nav-dropdown-trigger').forEach(function(trigger) {
        if (trigger.dataset.dropdownBound) return;
        trigger.dataset.dropdownBound = 'true';
        trigger.addEventListener('click', function(e) {
          toggleDropdown(this, e);
        });
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', setupAllDropdowns);
    } else {
      setupAllDropdowns();
    }

    // Delegated click listener ensures it works reliably across all modules
    document.addEventListener('click', function(e) {
      const trigger = e.target.closest('.nav-dropdown-trigger');
      if (trigger) {
        toggleDropdown(trigger, e);
      }
    }, true);
  })();
</script>
