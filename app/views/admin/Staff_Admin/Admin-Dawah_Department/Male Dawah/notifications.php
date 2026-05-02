<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Da'wah Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
</head>

<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'notifications';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
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
        <div class="breadcrumb-bar">
          <span class="current">Male Da'wah Notifications</span>
        </div>

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
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? '';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? '';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");

    function getSourcePage(type) {
      const map = {
        approve: '<?= url("/admin/dawah/counseling") ?>',
        reject: '<?= url("/admin/dawah/counseling") ?>',
        request: '<?= url("/admin/dawah/counseling") ?>',
        marriage: '<?= url("/admin/dawah/marriage") ?>',
        education: '<?= url("/admin/dawah/education") ?>',
        schedule: '<?= url("/admin/dawah/schedule") ?>',
        analytics: '<?= url("/admin/dawah/analytics") ?>',
        profile: '<?= url("/admin/dawah/male/profile") ?>'
      };
      return map[type] || '<?= url("/admin/dawah/male") ?>';
    }

    function getSourceLabel(type) {
      const map = {
        approve: 'Go to Counseling',
        reject: 'Go to Counseling',
        request: 'Go to Counseling',
        marriage: 'Go to Marriage Services',
        education: 'Go to Islamic Education',
        schedule: 'Go to Schedule',
        analytics: 'Go to Analytics',
        profile: 'Go to Profile'
      };
      return map[type] || 'Go to Dashboard';
    }

    function getStaffNotifications() {
      const activityLog = getActivityLog();
      const markedReadTime = parseInt(localStorage.getItem('male_dawah_notifs_read') || '0', 10);
      return activityLog.map((log, i) => {
        const logTime = new Date(log.time).getTime();
        return {
          id: 'D-NOT-' + i,
          title: log.action,
          message: log.detail,
          type: log.type,
          createdAt: log.time,
          read: (logTime <= markedReadTime) || (i > 3 && markedReadTime === 0),
          link: getSourcePage(log.type)
        };
      });
    }

    function getIconSvg(type) {
      const icons = {
        approve: '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
        reject: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
        request: '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        marriage: '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>',
        education: '<path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>',
        schedule: '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>',
        analytics: '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>',
        profile: '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>'
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
              <div class="notif-footer">
                Click to view details 
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:3;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </div>
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

      if (!n) { window.location.href = '<?= url("/admin/dawah/notifications") ?>'; return; }

      document.getElementById('notif-container').style.display = 'none';
      document.getElementById('unread-badge').style.display = 'none';
      const detailView = document.getElementById('notif-detail-view');
      detailView.style.display = 'block';

      const sourceLink = n.link ? `
        <div style="margin-top:32px;">
          <a href="${n.link}" class="btn-go-source">
            <svg viewBox="0 0 24 24"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
            ${getSourceLabel(n.type)}
          </a>
        </div>
      ` : '';

      detailView.innerHTML = `
        <button class="btn-back" onclick="window.location.href='<?= url("/admin/dawah/notifications") ?>'">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:3;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to all notifications
        </button>
        
        <div class="detail-card">
          <div class="detail-header">
            <div class="detail-icon type-${n.type || 'system'}">
              <svg viewBox="0 0 24 24">${getIconSvg(n.type)}</svg>
            </div>
            <div>
              <h3 class="detail-title">${n.title}</h3>
              <div class="detail-time">${new Date(n.createdAt).toLocaleString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit' })}</div>
            </div>
          </div>
          
          <div class="detail-body">${n.message}</div>
          
          <div class="detail-meta-grid">
            <div class="detail-meta-item">
              <div class="detail-meta-label">Notification ID</div>
              <div class="detail-meta-value" style="font-family:monospace;">${n.id}</div>
            </div>
            <div class="detail-meta-item">
              <div class="detail-meta-label">Category</div>
              <div class="detail-meta-value">${(n.type || 'system').toUpperCase()}</div>
            </div>
            <div class="detail-meta-item">
              <div class="detail-meta-label">Department</div>
              <div class="detail-meta-value">Male Da'wah</div>
            </div>
          </div>
          
          ${sourceLink}
        </div>
      `;
    }

    function markAllRead() {
      localStorage.setItem('male_dawah_notifs_read', Date.now().toString());
      const container = document.getElementById('notif-container');
      container.querySelectorAll('.unread').forEach(node => node.classList.remove('unread'));
      document.getElementById('unread-badge').style.display = 'none';
      showToast('All notifications marked as read', 'var(--success)');
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

    const urlParams = new URLSearchParams(window.location.search);
    const viewId = urlParams.get('view');
    if (viewId) { renderDetailView(viewId); } else { renderNotifications(); }

    loadUserNav();
    initNotifBadge('staff');
  </script>
</body>

</html>
