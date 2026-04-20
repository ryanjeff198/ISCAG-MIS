<?php 
$active_page = 'notification'; 
if (!function_exists('asset')) {
    function asset($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = rtrim($baseUrl, '/');
        if (str_ends_with($baseUrl, '/public')) return $baseUrl . '/' . ltrim($path, '/');
        return $baseUrl . "/public/" . ltrim($path, '/'); 
    }
}
if (!function_exists('url')) {
    function url($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        return rtrim($baseUrl, '/') . "/" . ltrim($path, '/'); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Admin Notifications</title>
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          
          <div>
            <div class="top-bar-title" id="page-title">Admin Notifications</div>
            <div class="top-bar-subtitle">System alerts, new requests, and system actions</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
          <button class="btn-topbar primary" onclick="markAllRead()">Mark All Read</button>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Unread Alerts</div>
            <div class="insight-value danger" id="stat-unread-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Reviews</div>
            <div class="insight-value warning">14</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">System Health</div>
            <div class="insight-value success">EXCELLENT</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Active Sessions</div>
            <div class="insight-value info">3</div>
          </div>
        </div>
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path
                  d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
              </svg>System Activity & Notifications</h6>
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
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');
    initReportsData();

    // Map activity log types to admin source pages
    function getSourcePage(type) {
      const map = {
        approve: '<?= url('/admin/mis_admin/tenant_confirmation') ?>',
        request: '<?= url('/admin/mis_admin/reports') ?>',
        payment: '<?= url('/admin/mis_admin/billing') ?>',
        update: '<?= url('/admin/dashboard') ?>',
        alert: '<?= url('/admin/mis_admin/billing') ?>',
        schedule: '<?= url('/admin/mis_admin/reports') ?>',
        staff: '<?= url('/admin/mis_admin/profile') ?>',
        system: '<?= url('/admin/dashboard') ?>',
        user: '<?= url('/admin/mis_admin/records') ?>'
      };
      return map[type] || '<?= url('/admin/dashboard') ?>';
    }

    function getSourceLabel(type) {
      const map = {
        approve: 'Go to Tenant Verification',
        request: 'Go to Tenant Reports',
        payment: 'Go to Billing & Payments',
        update: 'Go to Dashboard',
        alert: 'Go to Billing & Payments',
        schedule: 'Go to Tenant Reports',
        staff: 'Go to Profile',
        system: 'Go to Dashboard',
        user: 'Go to User Management'
      };
      return map[type] || 'Go to Dashboard';
    }

    function getAdminNotifications() {
      const activityLog = getActivityLog();
      return activityLog.map((log, i) => ({
        id: 'A-NOT-' + i,
        title: log.action,
        message: log.detail,
        type: log.type,
        createdAt: log.time,
        read: i > 4,
        link: getSourcePage(log.type)
      }));
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
      const notifs = getAdminNotifications();

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
              <div style="font-size:0.75rem;color:var(--primary);font-weight:600;margin-top:4px;">Click to read full details &rarr;</div>
            </div>
          </div>
        `;
      }).join('');
    }

    function viewNotification(id) {
      window.location.href = '?view=' + id;
    }

    function renderDetailView(id) {
      const allNotifs = getAdminNotifications();
      const n = allNotifs.find(x => x.id === id);

      if (!n) { window.location.href = '<?= url('/admin/mis_admin/notification') ?>'; return; }

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
        <button class="btn-back" onclick="window.location.href='<?= url('/admin/mis_admin/notification') ?>'">
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
      const container = document.getElementById('notif-container');
      container.querySelectorAll('.unread').forEach(node => node.classList.remove('unread'));
      document.getElementById('unread-badge').style.display = 'none';
      document.querySelectorAll('.notif-dot').forEach(d => d.remove());
      showToast('All notifications marked as read', 'var(--success)');
    }

    // Route: detail view or list
    const urlParams = new URLSearchParams(window.location.search);
    const viewId = urlParams.get('view');
    if (viewId) { renderDetailView(viewId); } else { renderNotifications(); }

    loadUserNav();
    initNotifBadge('admin');
  </script>
</body>

</html>

