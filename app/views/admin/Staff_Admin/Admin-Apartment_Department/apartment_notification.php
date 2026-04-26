<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Staff Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
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
      background: rgba(46, 125, 85, 0.03);
      border-color: rgba(46, 125, 85, 0.3);
    }

    .notif-card.unread::before {
      content: '';
      position: absolute;
      left: 0;
      top: 16px;
      bottom: 16px;
      width: 4px;
      border-radius: 0 4px 4px 0;
      background: var(--success);
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

    .type-reject,
    .type-alert {
      background: var(--danger);
    }

    .type-assign {
      background: var(--accent);
    }

    .type-request {
      background: var(--primary);
    }

    .type-payment {
      background: #2e5a8a;
    }

    .type-user {
      background: var(--info);
    }

    .type-update {
      background: var(--accent);
    }

    .type-schedule {
      background: #5a2e7a;
    }

    .type-staff {
      background: var(--primary-dark);
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
    <?php 
      $active_page = 'notifications';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>


    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" id="page-title">Notifications</div>
          <div class="top-bar-subtitle">System alerts and workflow updates</div>
        </div>
        <div style="display:flex; gap:10px;">
          <button class="btn-topbar primary" onclick="markAllRead()">Mark All Read</button>
        </div>
      </div>

      <div class="page-body">
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px;">
              <h6><svg viewBox="0 0 24 24">
                  <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                </svg>Recent Notifications</h6>
              <span id="unread-badge"
                style="font-size:0.72rem;background:var(--danger);color:white;padding:2px 8px;border-radius:12px;font-weight:700;">0 New</span>
            </div>
            <div class="table-search-wrapper" style="min-width:220px; position:relative; margin:0;">
               <input type="text" id="notif-search" class="table-search-input" placeholder="Search notifications..." style="padding: 8px 14px 8px 36px; font-size: 0.85rem;" oninput="filterNotifs()">
               <svg viewBox="0 0 24 24" style="position:absolute; width:16px; height:16px; left:12px; top:50%; transform:translateY(-50%); fill:var(--text-muted);"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>
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

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Apartment Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'staff@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Apartment Manager';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");

    // Map activity log types to source pages for staff
    function getSourcePage(type) {
      const map = {
        approve: '<?= url("/admin/apartment/info") ?>',
        request: '<?= url("/admin/apartment/info") ?>',
        payment: '<?= url("/admin/apartment/payment") ?>',
        update: '<?= url("/admin/apartment/dashboard") ?>',
        alert: '<?= url("/admin/apartment/payment") ?>',
        schedule: '<?= url("/admin/apartment/info") ?>',
        staff: '<?= url("/admin/apartment/profile") ?>',
        system: '<?= url("/admin/apartment/dashboard") ?>',
        user: '<?= url("/admin/apartment/info") ?>'
      };
      return map[type] || '<?= url("/admin/apartment/dashboard") ?>';
    }

    function getSourceLabel(type) {
      const map = {
        approve: 'Go to Apartment Info',
        request: 'Go to Apartment Info',
        payment: 'Go to Billing & Payment',
        update: 'Go to Dashboard',
        alert: 'Go to Billing & Payment',
        schedule: 'Go to Apartment Info',
        staff: 'Go to Profile',
        system: 'Go to Dashboard',
        user: 'Go to Apartment Info'
      };
      return map[type] || 'Go to Dashboard';
    }

    function getStaffNotifications() {
      const activityLog = getActivityLog();
      const markedReadTime = parseInt(localStorage.getItem('staff_notifs_read') || '0', 10);
      return activityLog.map((log, i) => {
        const logTime = new Date(log.time).getTime();
        return {
          id: 'S-NOT-' + i,
          title: log.action,
          message: log.detail,
          type: log.type,
          createdAt: log.time,
          read: (logTime <= markedReadTime) || (i > 2 && markedReadTime === 0),
          link: getSourcePage(log.type)
        };
      });
    }

    function getIconSvg(type) {
      const icons = {
        approve: '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
        reject: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
        request: '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        payment: '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>',
        alert: '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
        update: '<path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm2 16H5V5h11.17L19 7.83V19zM12 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM8 6H6v4h10V6H8z"/>',
        schedule: '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>',
        staff: '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>',
        user: '<path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>'
      };
      return icons[type] || '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>';
    }

    function renderNotifications() {
      const container = document.getElementById('notif-container');
      const notifs = getStaffNotifications();

      if (notifs.length === 0) {
        container.innerHTML = `
          <div class="empty-state">
            <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            <h4 style="color:var(--text-muted);font-family:'Lora',serif;margin:0;">No notifications</h4>
            <p style="font-size:0.85rem;color:var(--text-muted);">You're all caught up!</p>
          </div>
        `;
        document.getElementById('unread-badge').style.display = 'none';
        return;
      }

      const unreadCount = notifs.filter(n => !n.read).length;
      const b = document.getElementById('unread-badge');
      if (unreadCount > 0) { b.textContent = unreadCount + ' New'; b.style.display = 'inline-block'; }
      else { b.style.display = 'none'; }

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
              <div style="font-size:0.75rem;color:var(--success);font-weight:600;margin-top:4px;">Click to read full details &rarr;</div>
            </div>
          </div>
        `;
      }).join('');
    }

    function viewNotification(id) {
      window.location.href = '?view=' + id;
    }

    function renderDetailView(id) {
      const allNotifs = getStaffNotifications();
      const n = allNotifs.find(x => x.id === id);

      if (!n) { window.location.href = '<?= url("/admin/apartment/notification") ?>'; return; }

      document.getElementById('notif-container').style.display = 'none';
      document.getElementById('unread-badge').style.display = 'none';
      const detailView = document.getElementById('notif-detail-view');
      detailView.style.display = 'block';

      const sourceLink = n.link ? `
        <a href="${n.link}" class="btn-go-source">
          <svg viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
          ${getSourceLabel(n.type)}
        </a>
      ` : '';

      detailView.innerHTML = `
        <button class="btn-back" onclick="window.location.href='<?= url("/admin/apartment/notification") ?>'">
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
            <div class="value">${(n.type || 'system').toUpperCase()}</div>
          </div>
          <div class="detail-meta-item">
            <div class="label">Source</div>
            <div class="value">${getSourceLabel(n.type)}</div>
          </div>
        </div>
        
        ${sourceLink}
      `;
    }

    function markAllRead() {
      localStorage.setItem('staff_notifs_read', Date.now().toString());
      const container = document.getElementById('notif-container');
      container.querySelectorAll('.unread').forEach(node => node.classList.remove('unread'));
      document.getElementById('unread-badge').style.display = 'none';
      document.querySelectorAll('.notif-dot').forEach(d => d.remove());
      showToast('All notifications marked as read', 'var(--success)');
      // Refresh the sidebar dot
      initNotifBadge('staff');
    }

    function filterNotifs() {
      const term = document.getElementById('notif-search').value.toLowerCase().trim();
      const cards = document.querySelectorAll('.notif-card');
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(term) ? 'flex' : 'none';
      });
    }

    // Route
    const urlParams = new URLSearchParams(window.location.search);
    const viewId = urlParams.get('view');
    if (viewId) { renderDetailView(viewId); } else { renderNotifications(); }

    loadUserNav();
    initNotifBadge('staff');
  </script>
</body>

</html>