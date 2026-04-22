<?php
$name_parts = explode(' ', trim($_SESSION['name'] ?? 'User'));
$first_initial = substr($name_parts[0], 0, 1);
$last_initial = count($name_parts) > 1 ? substr(end($name_parts), 0, 1) : '';
$initials = strtoupper($first_initial . $last_initial);

$active_page = $active_page ?? 'dashboard';
?>
<aside class="sidebar" id="sidebar">
    <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
        <svg viewBox="0 0 24 24">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
        </svg>
    </button>
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
            <div class="brand-text"><strong>ISCAG MIS</strong><span>User Portal</span></div>
        </div>
    </div>
    <div class="sidebar-user">
        <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">
            <?= htmlspecialchars($initials) ?>
        </div>
        <div class="user-info">
            <strong id="nav-name"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></strong>
            <span id="nav-role"><?= htmlspecialchars($_SESSION['role'] ?? 'Verified User') ?></span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu</div>
        <a href="<?= url('/user/dashboard') ?>" class="nav-item <?= $active_page === 'dashboard' ? 'active' : '' ?>" data-tooltip="Dashboard">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
            </svg>
            <span class="nav-item-label">My Dashboard</span>
        </a>
        <a href="<?= url('/user/profile') ?>" class="nav-item <?= $active_page === 'profile' ? 'active' : '' ?>" data-tooltip="Profile">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
            </svg>
            <span class="nav-item-label">My Profile</span>
        </a>
        <a href="<?= url('/user/notifications') ?>" class="nav-item <?= $active_page === 'notifications' ? 'active' : '' ?>" data-tooltip="Notifications">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
            </svg>
            <span class="nav-item-label">Notifications</span>
        </a>
        <div class="nav-section-label">Services</div>

        <!-- DAMAYAN DROPDOWN -->
        <?php $damayan_active = in_array($active_page, ['burial_service']); ?>
        <div class="nav-dropdown-wrap <?= $damayan_active ? 'open' : '' ?>" id="damayan-wrap">
            <button class="nav-dropdown-trigger <?= $damayan_active ? 'open' : '' ?>" id="damayan-trigger" data-tooltip="Damayan">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                </svg>
                <span class="nav-item-label">Damayan</span>
                <span class="nav-lock-badge">Locked</span>
                <svg class="nav-lock-icon" viewBox="0 0 24 24">
                    <path
                        d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" />
                </svg>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>
            <div class="nav-dropdown <?= $damayan_active ? 'open' : '' ?>" id="damayan-menu">
                <a href="<?= url('/user/services/burial-form') ?>" class="<?= $active_page === 'burial_service' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                    Burial Service
                </a>
            </div>
        </div>

        <!-- DA'WAH DROPDOWN -->
        <?php $dawah_active = in_array($active_page, ['counseling_male', 'counseling_female']); ?>
        <div class="nav-dropdown-wrap <?= $dawah_active ? 'open' : '' ?>" id="dawah-wrap">
            <button class="nav-dropdown-trigger <?= $dawah_active ? 'open' : '' ?>" id="dawah-trigger" data-tooltip="Da'wah">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
                </svg>
                <span class="nav-item-label">Da'wah</span>
                <span class="nav-lock-badge">Locked</span>
                <svg class="nav-lock-icon" viewBox="0 0 24 24">
                    <path
                        d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" />
                </svg>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>
            <div class="nav-dropdown <?= $dawah_active ? 'open' : '' ?>" id="dawah-menu">
                <?php if (($_SESSION['gender'] ?? '') !== 'female'): ?>
                <a href="<?= url('/user/services/counseling/male') ?>" class="<?= $active_page === 'counseling_male' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                    Brothers' Counseling
                </a>
                <?php endif; ?>
                <?php if (($_SESSION['gender'] ?? '') !== 'male'): ?>
                <a href="<?= url('/user/services/counseling/female') ?>" class="<?= $active_page === 'counseling_female' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                    Sisters' Counseling
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- APARTMENT DROPDOWN -->
        <?php $apartment_active = in_array($active_page, ['apartment_apply', 'apartment_status', 'apartment_info', 'apartment_parking']); ?>
        <div class="nav-dropdown-wrap <?= $apartment_active ? 'open' : '' ?>" id="apartment-wrap">
            <button class="nav-dropdown-trigger <?= $apartment_active ? 'open' : '' ?>" id="apartment-trigger" data-tooltip="Apartment">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
                </svg>
                <span class="nav-item-label">Apartment</span>
                <span class="nav-lock-badge">Locked</span>
                <svg class="nav-lock-icon" viewBox="0 0 24 24">
                    <path
                        d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z" />
                </svg>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z" />
                </svg>
            </button>
            <div class="nav-dropdown <?= $apartment_active ? 'open' : '' ?>" id="apartment-menu">
                <?php if (($_SESSION['role'] ?? '') === 'Applicant'): ?>
                <a href="<?= url('/user/apartment/apply') ?>" class="<?= $active_page === 'apartment_apply' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                    </svg>
                    Application Form
                </a>
                <a href="<?= url('/user/apartment/status') ?>" class="<?= $active_page === 'apartment_status' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                    </svg>
                    Application Status
                </a>
                <?php endif; ?>
                <a href="<?= url('/user/apartment/info') ?>" class="<?= $active_page === 'apartment_info' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
                    </svg>
                    Apartment Information
                </a>
                <?php if (($_SESSION['role'] ?? '') === 'Tenant'): ?>
                <a href="<?= url('/user/apartment/parking') ?>" class="<?= $active_page === 'apartment_parking' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                    </svg>
                    Parking Rental
                </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
            </svg>
            <span class="nav-item-label">Logout</span>
        </a>
    </div>
</aside>
<script>
    /**
     * ISCAG MIS — Sidebar & Shared Portal Logic
     * Handles: Toggles, Dropdowns, State Sync, and Real-time Polling
     */
    (function() {
        // ── 1. SESSION TO LOCALSTORAGE SYNC ──
        const sessionUser = {
            id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
            name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
            role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
            gender: '<?= addslashes($_SESSION['gender'] ?? "") ?>'
        };
        const raw = localStorage.getItem('mis_user');
        let stored = raw ? JSON.parse(raw) : {};
        Object.assign(stored, sessionUser);
        localStorage.setItem('mis_user', JSON.stringify(stored));

        // ── 2. SIDEBAR TOGGLE & DROPDOWNS ──
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebar && sidebarToggle) {
            sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('collapsed'));
        }

        function initDropdown(triggerId, menuId, wrapId) {
            const trigger = document.getElementById(triggerId);
            const menu = document.getElementById(menuId);
            const wrap = document.getElementById(wrapId);
            if (!trigger || !menu) return;

            trigger.addEventListener('click', (e) => {
                e.preventDefault();

                // ── Service Lock Enforcement ──
                if (wrap && wrap.classList.contains('locked')) {
                    if (typeof showToast === 'function') {
                        showToast('Please complete your profile to unlock this service.', '#c79a2b');
                    } else {
                        alert('Please complete your profile to unlock this service.');
                    }
                    setTimeout(() => window.location.href = '<?= url("/user/profile") ?>', 1200);
                    return;
                }

                const isOpen = menu.classList.contains('open');
                document.querySelectorAll('.nav-dropdown, .nav-dropdown-trigger, .nav-dropdown-wrap').forEach(el => el.classList.remove('open'));
                if (!isOpen) {
                    menu.classList.add('open');
                    trigger.classList.add('open');
                    if (wrap) wrap.classList.add('open');
                }
            });
        }

        // ── Apply Locks based on Profile Status ──
        function applySidebarLocks() {
            const stored = JSON.parse(localStorage.getItem('mis_user') || '{}');
            const isComplete = stored.profileComplete || false;
            const wraps = ['damayan-wrap', 'dawah-wrap', 'apartment-wrap'];
            
            wraps.forEach(id => {
                const wrap = document.getElementById(id);
                if (wrap) {
                    if (isComplete) wrap.classList.remove('locked');
                    else wrap.classList.add('locked');
                }
            });
        }

        initDropdown('damayan-trigger', 'damayan-menu', 'damayan-wrap');
        initDropdown('dawah-trigger', 'dawah-menu', 'dawah-wrap');
        initDropdown('apartment-trigger', 'apartment-menu', 'apartment-wrap');
        
        applySidebarLocks();
        // Re-check after a short delay to ensure localStorage is ready
        setTimeout(applySidebarLocks, 500);

        // ── 3. APPROVAL POLLING & NOTIFICATIONS ──
        let approvalNotifId = null;
        function checkUserStatus() {
            fetch('<?= url("/user/status/check") ?>?t=' + Date.now())
                .then(res => res.json())
                .then(data => {
                    const notifs = data.notifications || [];
                    
                    // Update Notification Dot
                    const dot = document.querySelector('.notif-dot');
                    const unreadCount = notifs.filter(n => n.is_read == 0).length;
                    if (dot) {
                        dot.textContent = unreadCount;
                        dot.style.display = unreadCount > 0 ? 'flex' : 'none';
                    }

                    // Check for Approval
                    const approvalNotif = notifs.find(n => n.type === 'approval' && n.is_read == 0);
                    if (approvalNotif) {
                        approvalNotifId = approvalNotif.id || approvalNotif.notification_id;
                        document.getElementById('approval-modal')?.classList.add('show');
                    } else if (data.role === 'Tenant' && sessionUser.role === 'Applicant') {
                        window.location.reload();
                    }
                })
                .catch(err => console.warn('Status check failed:', err));
        }

        const continueBtn = document.getElementById('approval-continue-btn');
        if (continueBtn) {
            continueBtn.addEventListener('click', () => {
                document.getElementById('approval-modal')?.classList.remove('show');
                if (approvalNotifId) {
                    fetch('<?= url("/user/notifications/mark-read") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: approvalNotifId })
                    }).finally(() => window.location.href = '<?= url("/user/dashboard") ?>');
                } else {
                    window.location.href = '<?= url("/user/dashboard") ?>';
                }
            });
        }

        setTimeout(checkUserStatus, 800);
        if (sessionUser.role === 'Applicant') setInterval(checkUserStatus, 15000);
    })();

    // ── 4. LOGOUT MODAL ──
    function initLogoutModal() {
        const logoutLinks = document.querySelectorAll('a[href*="/logout"], [data-tooltip="Logout"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (typeof showLogoutConfirm === 'function') {
                    showLogoutConfirm(href);
                } else if (confirm('Are you sure you want to log out?')) {
                    window.location.href = href;
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', initLogoutModal);
</script>
