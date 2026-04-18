<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — User Notifications</title>
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        /* ── Locked Dropdown State ── */
        .nav-dropdown-wrap.locked .nav-dropdown-trigger {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .nav-dropdown-wrap.locked .nav-dropdown-trigger:hover {
            background: rgba(139, 46, 46, 0.06);
        }

        .nav-dropdown-wrap.locked .nav-dropdown-arrow {
            display: none;
        }

        .nav-lock-icon {
            width: 14px;
            height: 14px;
            fill: var(--warning);
            margin-left: auto;
            flex-shrink: 0;
            display: none;
        }

        .nav-dropdown-wrap.locked .nav-lock-icon {
            display: block;
        }

        .nav-dropdown-wrap.locked .nav-dropdown {
            display: none !important;
        }

        .nav-lock-badge {
            display: none;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--warning);
            background: rgba(199, 154, 43, 0.1);
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 6px;
            white-space: nowrap;
        }

        .nav-dropdown-wrap.locked .nav-lock-badge {
            display: inline-flex;
        }

        .sidebar.collapsed .nav-lock-badge {
            display: none !important;
        }

        .sidebar.collapsed .nav-lock-icon {
            display: none !important;
        }

        .notif-card {
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: white;
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
            position: relative;
            transition: all 0.2s;
            cursor: pointer;
        }

        .notif-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .notif-card.unread {
            background: rgba(199, 154, 43, 0.03);
            border-color: rgba(199, 154, 43, 0.3);
        }

        .notif-card.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 16px;
            bottom: 16px;
            width: 4px;
            border-radius: 0 4px 4px 0;
            background: var(--accent);
        }

        .notif-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-icon svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .notif-content {
            flex: 1;
        }

        .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .notif-title {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 0.95rem;
            margin: 0;
        }

        .notif-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .notif-message {
            font-size: 0.85rem;
            color: var(--text-color);
            margin: 0 0 8px 0;
            line-height: 1.4;
        }

        .type-system {
            background: var(--border);
        }

        .type-system svg {
            fill: var(--text-main) !important;
        }

        .type-approve {
            background: var(--success);
        }

        .type-reject {
            background: var(--danger);
        }

        .type-assign {
            background: var(--accent);
        }

        .type-request {
            background: var(--primary);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            background: white;
            border-radius: 12px;
            border: 1px dashed var(--border);
        }

        .notif-detail-view {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 28px;
            display: none;
        }

        .btn-back {
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: white;
            color: var(--text-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-family: inherit;
            font-size: 0.85rem;
            margin-bottom: 20px;
            transition: all 0.18s;
        }

        .btn-back:hover {
            background: #f0f2f1;
            border-color: var(--primary);
            color: var(--primary);
        }

        .detail-meta {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .detail-meta-item {
            padding: 14px 18px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid var(--border);
            flex: 1;
            min-width: 140px;
        }

        .detail-meta-item .label {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .detail-meta-item .value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .btn-go-source {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            border: none;
            font-weight: 700;
            font-size: 0.85rem;
            font-family: inherit;
            cursor: pointer;
            margin-top: 24px;
            transition: all 0.18s;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
        }

        .btn-go-source:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-1px);
        }

        .btn-go-source svg {
            width: 16px;
            height: 16px;
            fill: white;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <?php $active_page = 'notifications'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title" id="page-title">Notifications</div>
                    <div class="top-bar-subtitle">Updates on your applications and account</div>
                </div>
                <div style="display:flex; gap:10px;">
                    <button class="btn-topbar primary" onclick="markAllRead()">Mark All Read</button>
                </div>
            </div>

            <div class="page-body">
                <div class="section-card">
                    <div class="section-card-header">
                        <h6><svg viewBox="0 0 24 24">
                                <path
                                    d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                            </svg>Your Notifications</h6>
                        <span id="unread-badge"
                            style="font-size:0.72rem;background:var(--danger);color:white;padding:2px 8px;border-radius:12px;font-weight:700;">0
                            New</span>
                    </div>
                    <div class="section-card-body" id="notif-container" style="background:#f8f9fa;">
                        <!-- RENDERED DYNAMICALLY -->
                    </div>

                    <div class="notif-detail-view" id="notif-detail-view">
                        <!-- RENDERED DYNAMICALLY -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        initAdminData();
        initReportsData();
        initSidebar();

        // Map notification type to a readable label
        function typeToLabel(type) {
            const map = {
                system: 'System',
                approve: 'Approved',
                reject: 'Action Required',
                assign: 'Room Assigned',
                request: 'Request Update'
            };
            return map[type] || 'System';
        }

        // Map notification type to a source page label
        function typeToSourceLabel(type) {
            const map = {
                system: 'View Application Status',
                approve: 'View Application Status',
                reject: 'Go to Tenant Information',
                assign: 'View Room Assignment',
                request: 'View Request Details'
            };
            return map[type] || 'View Details';
        }

        function getUserNotifications() {
            const user = getUser();
            const allNotifs = getNotifications();
            return allNotifs.filter(n => n.tenantId === user.id);
        }

        function getIconSvg(type) {
            const icons = {
                system: '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>',
                approve: '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
                reject: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
                assign: '<path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>',
                request: '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>'
            };
            return icons[type] || icons.system;
        }

        function renderNotifications() {
            const container = document.getElementById('notif-container');
            const notifs = getUserNotifications();

            if (notifs.length === 0) {
                container.innerHTML = `
          <div class="empty-state">
            <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            <h4 style="color:var(--text-muted);font-family:'Lora',serif;margin:0;">No notifications found</h4>
            <p style="font-size:0.85rem;color:var(--text-muted);">You'll see alerts here when your requests are updated.</p>
          </div>
        `;
                document.getElementById('unread-badge').style.display = 'none';
                return;
            }

            const unreadCount = notifs.filter(n => !n.read).length;
            const b = document.getElementById('unread-badge');
            if (unreadCount > 0) {
                b.textContent = unreadCount + ' New';
                b.style.display = 'inline-block';
            } else {
                b.style.display = 'none';
            }

            container.innerHTML = notifs.map(n => {
                const shortMsg = n.message.length > 80 ? n.message.substring(0, 80) + '...' : n.message;

                return `
          <div class="notif-card ${n.read ? '' : 'unread'}" onclick="viewNotification('${n.id}')">
            <div class="notif-icon type-${n.type || 'system'}">
              <svg viewBox="0 0 24 24">${getIconSvg(n.type)}</svg>
            </div>
            <div class="notif-content">
              <div class="notif-header">
                <h5 class="notif-title">${n.title}</h5>
                <span class="notif-time">${timeAgo(n.createdAt)}</span>
              </div>
              <p class="notif-message">${shortMsg}</p>
              <div style="font-size:0.75rem;color:var(--accent);font-weight:600;margin-top:4px;">Click to read full details &rarr;</div>
            </div>
          </div>
        `;
            }).join('');
        }

        function viewNotification(id) {
            window.location.href = '?view=' + id;
        }

        function renderDetailView(id) {
            const allNotifs = getNotifications();
            const n = allNotifs.find(x => x.id === id);

            if (!n) {
                window.location.href = 'tenant_notification.html';
                return;
            }

            // Mark as read
            if (!n.read && n.tenantId === getUser().id) {
                n.read = true;
                saveNotifications(allNotifs);
            }

            document.getElementById('notif-container').style.display = 'none';
            document.getElementById('unread-badge').style.display = 'none';
            const detailView = document.getElementById('notif-detail-view');
            detailView.style.display = 'block';

            // Build source link button
            const sourceLink = n.link ? `
        <a href="${n.link}" class="btn-go-source">
          <svg viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
          ${typeToSourceLabel(n.type)}
        </a>
      ` : '';

            detailView.innerHTML = `
        <button class="btn-back" onclick="window.location.href='<?= url('/user/notifications') ?>'">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
          Back to all notifications
        </button>
        
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
          <div class="notif-icon type-${n.type || 'system'}" style="width:48px;height:48px;">
            <svg viewBox="0 0 24 24" style="width:24px;height:24px;">${getIconSvg(n.type)}</svg>
          </div>
          <div style="flex:1;">
            <h3 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-size:1.2rem;">${n.title}</h3>
            <span style="color:var(--text-muted); font-size:0.82rem;">${new Date(n.createdAt).toLocaleString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit' })}</span>
          </div>
        </div>
        
        <div style="border-top:1px solid var(--border); padding-top:20px; margin-bottom:20px;">
          <div style="font-size:1rem; color:var(--text-main); line-height:1.7; white-space:pre-wrap;">${n.message}</div>
        </div>
        
        <div class="detail-meta">
          <div class="detail-meta-item">
            <div class="label">Notification ID</div>
            <div class="value" style="font-family:monospace;">${n.id}</div>
          </div>
          <div class="detail-meta-item">
            <div class="label">Category</div>
            <div class="value">${typeToLabel(n.type)}</div>
          </div>
          <div class="detail-meta-item">
            <div class="label">Status</div>
            <div class="value" style="color:var(--success);">✅ Read</div>
          </div>
        </div>
        
        ${sourceLink}
      `;
        }

        function markAllRead() {
            const allNotifs = getNotifications();
            const user = getUser();

            let changed = false;
            allNotifs.forEach(n => {
                if (n.tenantId === user.id && !n.read) {
                    n.read = true;
                    changed = true;
                }
            });

            if (changed) saveNotifications(allNotifs);

            // Update UI visually
            const container = document.getElementById('notif-container');
            container.querySelectorAll('.unread').forEach(node => node.classList.remove('unread'));
            document.getElementById('unread-badge').style.display = 'none';
            // Remove sidebar dot too
            document.querySelectorAll('.notif-dot').forEach(d => d.remove());
            showToast('All notifications marked as read', 'var(--success)');
        }

        // Route: detail view or list
        const urlParams = new URLSearchParams(window.location.search);
        const viewId = urlParams.get('view');

        if (viewId) {
            renderDetailView(viewId);
        } else {
            renderNotifications();
        }

        loadUserNav();
        initNotifBadge('tenant');

        // Set initials
        const user = getUser();
        const initials = user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();

        // Avatar override logic
        const navAvatar = document.getElementById('nav-avatar');
        const photo = localStorage.getItem('mis_user_photo');
        if (photo) {
            navAvatar.textContent = '';
            navAvatar.style.backgroundImage = 'url(' + photo + ')';
            navAvatar.style.backgroundSize = 'cover';
            navAvatar.style.backgroundPosition = 'center';
        } else {
            navAvatar.textContent = initials;
        }

        document.getElementById('nav-name').textContent = user.name;

        // ── Role label: use SESSION data (injected by sidebar sync) ──
        // The sidebar already rendered the correct PHP role; here we just
        // style it green. Lock state also comes from session, not localStorage.
        const SESSION_ROLE   = '<?= htmlspecialchars($_SESSION['role'] ?? '') ?>';
        const SESSION_GENDER = '<?= htmlspecialchars($_SESSION['gender'] ?? '') ?>';

        // Services are unlocked when user has a real role (Applicant / Tenant)
        const isComplete = SESSION_ROLE !== '';

        const navRole = document.getElementById('nav-role');
        if (navRole) {
            // Sidebar PHP already set textContent from $_SESSION['role'];
            // just ensure the colour is green.
            navRole.style.color = 'var(--success)';
        }

        // Da'wah dropdown dynamic path based on SESSION gender
        const dawahMenu    = document.getElementById('dawah-menu');
        const dawahTrigger = document.getElementById('dawah-trigger');
        if (dawahMenu && dawahTrigger) {
            const genderLower = SESSION_GENDER.toLowerCase();
            if (genderLower === 'female') {
                dawahMenu.innerHTML = `
                <a href="<?= url('/user/services/counseling/female') ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                    Sisters' Counseling
                </a>`;
                dawahTrigger.setAttribute('data-href', "<?= url('/user/services/counseling/female') ?>");
            } else {
                dawahMenu.innerHTML = `
                <a href="<?= url('/user/services/counseling/male') ?>">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                    Brothers' Counseling
                </a>`;
                dawahTrigger.setAttribute('data-href', "<?= url('/user/services/counseling/male') ?>");
            }
        }

        // Dropdown Lock Logic — unlock for any authenticated session user
        function applyDropdownLocks() {
            const wraps = ['damayan-wrap', 'dawah-wrap', 'apartment-wrap'];
            wraps.forEach(id => {
                const wrap = document.getElementById(id);
                if (!wrap) return;
                // Always unlocked for logged-in Applicant / Tenant roles
                wrap.classList.remove('locked');
            });
        }
        applyDropdownLocks();

        // Dropdown Click Handlers
        const _sidebar = document.getElementById('sidebar');

        function initDropdown(triggerId, menuId, wrapId) {
            const trigger = document.getElementById(triggerId);
            const menu = document.getElementById(menuId);
            const wrap = document.getElementById(wrapId);
            if (!trigger || !menu) return;
            trigger.addEventListener('click', () => {
                if (wrap && wrap.classList.contains('locked')) {
                    showToast('🔒 Please complete your profile to unlock services.', '#c79a2b');
                    setTimeout(() => window.location.href = '<?= url('/user/profile') ?>', 1000);
                    return;
                }
                if (_sidebar && _sidebar.classList.contains('collapsed')) {
                    const href = trigger.getAttribute('data-href');
                    if (href) window.location.href = href;
                    return;
                }
                const isOpen = menu.classList.contains('open');
                document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
                document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
                if (!isOpen) {
                    menu.classList.add('open');
                    trigger.classList.add('open');
                }
            });
        }
        initDropdown('damayan-trigger', 'damayan-menu', 'damayan-wrap');
        initDropdown('dawah-trigger', 'dawah-menu', 'dawah-wrap');
        initDropdown('apartment-trigger', 'apartment-menu', 'apartment-wrap');
    </script>
</body>

</html>