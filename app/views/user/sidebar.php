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
        <div class="nav-dropdown-wrap <?= in_array($active_page, ['burial_service']) ? 'open' : '' ?>" id="damayan-wrap">
            <button class="nav-dropdown-trigger" id="damayan-trigger" data-tooltip="Damayan"
                data-href="<?= url('/user/services/burial-form') ?>">
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
            <div class="nav-dropdown <?= in_array($active_page, ['burial_service']) ? 'open' : '' ?>" id="damayan-menu">
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
        <div class="nav-dropdown-wrap <?= in_array($active_page, ['counseling_male', 'counseling_female']) ? 'open' : '' ?>" id="dawah-wrap">
            <button class="nav-dropdown-trigger" id="dawah-trigger" data-tooltip="Da'wah">
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
            <div class="nav-dropdown <?= in_array($active_page, ['counseling_male', 'counseling_female']) ? 'open' : '' ?>" id="dawah-menu">
                <a href="<?= url('/user/services/counseling/male') ?>" data-role-filter="male" class="<?= $active_page === 'counseling_male' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                    Brothers' Counseling
                </a>
                <a href="<?= url('/user/services/counseling/female') ?>" data-role-filter="female" class="<?= $active_page === 'counseling_female' ? 'active-submenu' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                    Sisters' Counseling
                </a>
            </div>
        </div>

        <!-- APARTMENT DROPDOWN -->
        <div class="nav-dropdown-wrap <?= in_array($active_page, ['apartment_apply', 'apartment_status', 'apartment_info', 'apartment_parking']) ? 'open' : '' ?>" id="apartment-wrap">
            <button class="nav-dropdown-trigger" id="apartment-trigger" data-tooltip="Apartment">
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
            <div class="nav-dropdown <?= in_array($active_page, ['apartment_apply', 'apartment_status', 'apartment_info', 'apartment_parking']) ? 'open' : '' ?>" id="apartment-menu">
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
     * ISCAG MIS — Dynamic Session Sync
     * Ensures legacy JS (which relies on localStorage) uses correct session data.
     */
    (function() {
        const sessionUser = {
            id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
            name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
            role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
            gender: '<?= addslashes($_SESSION['gender'] ?? "") ?>',
            email: '<?= addslashes($_SESSION['email'] ?? "") ?>'
        };
        const raw = localStorage.getItem('mis_user');
        let stored = raw ? JSON.parse(raw) : {};
        // Sync critical session fields
        stored.id = sessionUser.id;
        stored.name = sessionUser.name;
        stored.role = sessionUser.role;
        stored.gender = sessionUser.gender;
        stored.email = sessionUser.email;
        
        localStorage.setItem('mis_user', JSON.stringify(stored));
    })();
</script>
<style>
.active-submenu { color: var(--primary) !important; font-weight: 600; background: rgba(23,107,69,0.06); border-radius: 6px; }

/* ── Sidebar Role Display — Standardized to Yellow ── */
#nav-role { color: var(--warning) !important; font-weight: 500; font-size: 0.78rem; opacity: 0.95; }

/* ── Lock icon defaults — hidden for authenticated users ── */
.nav-lock-icon { display: none !important; }
.nav-lock-badge { display: none !important; }
.nav-dropdown-wrap.locked .nav-lock-icon { display: block !important; }
.nav-dropdown-wrap.locked .nav-lock-badge { display: inline-flex !important; }
.sidebar.collapsed .nav-lock-badge { display: none !important; }
.sidebar.collapsed .nav-lock-icon { display: none !important; }

/* ── Approval Modal CSS ── */
#approval-modal {
    position: fixed; inset: 0; z-index: 99999;
    display: none; align-items: center; justify-content: center;
    background: rgba(15,30,22,0.6); backdrop-filter: blur(6px);
    opacity: 0; transition: opacity 0.3s ease;
}
#approval-modal.show {
    display: flex; opacity: 1;
}
.approval-modal-content {
    background: white; border-radius: 16px; width: 100%; max-width: 440px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden;
    transform: translateY(30px); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
#approval-modal.show .approval-modal-content {
    transform: translateY(0);
}
.approval-modal-header { height: 4px; background: linear-gradient(90deg,#0f5c3a,#c79a2b); }
.approval-modal-body { padding: 32px 28px 24px; text-align: center; }
.approval-modal-icon { width: 64px; height: 64px; fill: #2f8a60; margin: 0 auto 16px; }
.approval-modal-title { font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 700; color: #0f5c3a; margin: 0 0 10px; }
.approval-modal-text { font-size: 0.9rem; color: #6f7f78; line-height: 1.6; margin: 0; }
.approval-modal-footer { display: flex; gap: 10px; padding: 0 28px 24px; justify-content: center; }
.approval-btn {
    padding: 10px 24px; border-radius: 8px; border: none;
    background: linear-gradient(135deg,#0f5c3a,#2f8a60);
    color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 12px rgba(15,92,58,0.3); transition: all 0.2s;
}
.approval-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15,92,58,0.4); }
</style>

<!-- ── Approval Modal HTML ── -->
<div id="approval-modal">
    <div class="approval-modal-content">
        <div class="approval-modal-header"></div>
        <div class="approval-modal-body">
            <svg class="approval-modal-icon" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            <h4 class="approval-modal-title">Congratulations!</h4>
            <p class="approval-modal-text">You are now officially a tenant. Your account has been successfully approved and all features are now unlocked.</p>
        </div>
        <div class="approval-modal-footer">
            <button class="approval-btn" id="approval-continue-btn">Continue to Dashboard</button>
        </div>
    </div>
</div>

<script>
/**
 * ISCAG MIS — Real-Time Approval Poller
 */
(function() {
    const currentRole = '<?= addslashes($_SESSION['role'] ?? "Applicant") ?>';
    let approvalNotifId = null;

    function checkUserStatus() {
        const cacheBuster = '?t=' + new Date().getTime();
        fetch('<?= url("/user/status/check") ?>' + cacheBuster)
            .then(res => res.json())
            .then(data => {
                // Find an unread approval notification
                const notifs = data.notifications || [];
                const approvalNotif = notifs.find(n => n.type === 'approval' && n.is_read == 0);
                
                if (approvalNotif) {
                    approvalNotifId = approvalNotif.notification_id || approvalNotif.id;
                    showApprovalModal();
                } else if (data.role === 'Tenant' && currentRole !== 'Tenant') {
                    // Fallback: if role changed but no notification found, force reload
                    window.location.reload();
                }
                
                // Update notification badge if element exists
                const unreadCount = notifs.filter(n => n.is_read == 0).length;
                window.MIS_UNREAD_COUNT = unreadCount;
            })
            .catch(err => console.error('Poller error:', err));
    }

    function showApprovalModal() {
        const modal = document.getElementById('approval-modal');
        if (modal && !modal.classList.contains('show')) {
            modal.classList.add('show');
        }
    }

    const continueBtn = document.getElementById('approval-continue-btn');
    if (continueBtn) {
        continueBtn.addEventListener('click', () => {
            // Hide the modal immediately for better UX
            const modal = document.getElementById('approval-modal');
            if (modal) modal.classList.remove('show');
            
            if (approvalNotifId) {
                // Mark notification as read to avoid showing modal again
                fetch('<?= url("/user/notifications/mark-read") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: approvalNotifId })
                })
                .then(res => res.json())
                .then(() => {
                    window.location.href = '<?= url("/user/dashboard") ?>';
                })
                .catch(err => {
                    console.error('Failed to mark read:', err);
                    window.location.href = '<?= url("/user/dashboard") ?>';
                });
            } else {
                window.location.href = '<?= url("/user/dashboard") ?>';
            }
        });
    }

    // Always check immediately on load to catch notifications
    setTimeout(checkUserStatus, 800);

    // Poll every 15 seconds if they are an Applicant waiting
    if (currentRole === 'Applicant') {
        setInterval(checkUserStatus, 15000);
    }
})();

// ══════════════════════════════════════
//  LOGOUT CONFIRMATION MODAL
// ══════════════════════════════════════
function initLogoutModal() {
  if (window._logoutModalInit) return;
  const logoutLinks = document.querySelectorAll('a[href*="/logout"], [data-tooltip="Logout"]');
  if (logoutLinks.length === 0) return;

  window._logoutModalInit = true;

  if (!document.getElementById('logout-confirm-modal')) {
    const modalHtml = `
      <div id="logout-confirm-modal" style="position:fixed;inset:0;background:rgba(15,30,22,0.6);backdrop-filter:blur(6px);z-index:99999;display:none;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;">
        <div style="background:white;border-radius:16px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;transform:translateY(20px);transition:transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
          <div style="height:4px;background:linear-gradient(90deg,#8b2e2e,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <svg viewBox="0 0 24 24" style="width:60px;height:60px;fill:#8b2e2e;margin-bottom:16px;">
              <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
            </svg>
            <h4 style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:#1f2e2a;margin:0 0 10px;">Sign Out</h4>
            <p style="font-size:0.9rem;color:#6f7f78;margin:0;line-height:1.5;">Are you sure you want to log out of your account?</p>
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="logout-cancel-btn" style="padding:10px 24px;border-radius:8px;border:1.5px solid #e8ece9;background:white;color:#6f7f78;font-weight:600;cursor:pointer;transition:background 0.2s;">Cancel</button>
            <button id="logout-confirm-btn" style="padding:10px 24px;border-radius:8px;border:none;background:#8b2e2e;color:white;font-weight:700;cursor:pointer;transition:background 0.2s;">Yes, Log out</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('logout-confirm-modal');
    const inner = modal.querySelector('div');
    const cancelBtn = document.getElementById('logout-cancel-btn');
    const confirmBtn = document.getElementById('logout-confirm-btn');
    let targetHref = '';

    const hideModal = () => {
      modal.style.opacity = '0';
      inner.style.transform = 'translateY(20px)';
      setTimeout(() => modal.style.display = 'none', 200);
    };

    const showModal = (e, href) => {
      e.preventDefault();
      targetHref = href;
      modal.style.display = 'flex';
      // Force reflow
      void modal.offsetWidth;
      modal.style.opacity = '1';
      inner.style.transform = 'translateY(0)';
    };

    cancelBtn.addEventListener('click', hideModal);
    modal.addEventListener('click', e => { if(e.target === modal) hideModal(); });
    
    confirmBtn.addEventListener('click', () => {
      window.location.href = targetHref || '/logout';
    });

    logoutLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        showModal(e, this.getAttribute('href'));
      });
    });
  }
}

document.addEventListener('DOMContentLoaded', initLogoutModal);
if (document.readyState === 'interactive' || document.readyState === 'complete') {
  initLogoutModal();
}
</script>
