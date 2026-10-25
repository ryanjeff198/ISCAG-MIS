<?php 
$active_page = 'notification'; 
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Admin Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
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
            <div class="top-bar-subtitle">Real-time system alerts from tenant payments, applications, and renewals</div>
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
            <div class="insight-value danger" id="stat-unread-val"><?= $unreadCount ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Notifications</div>
            <div class="insight-value info"><?= count($notifications) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">System Health</div>
            <div class="insight-value success">EXCELLENT</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Last Activity</div>
            <div class="insight-value info"><?= !empty($notifications) ? date('M j, g:ia', strtotime($notifications[0]['created_at'])) : 'N/A' ?></div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path
                  d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
              </svg>System Activity & Notifications</h6>
            <?php if ($unreadCount > 0): ?>
            <span id="unread-badge"
              style="font-size:0.72rem;background:var(--danger);color:white;padding:2px 8px;border-radius:12px;font-weight:700;"><?= $unreadCount ?>
              New</span>
            <?php endif; ?>
          </div>
          
          <!-- Notification List -->
          <div class="section-card-body" id="notif-container" style="background:#f8f9fa;">
            <?php if (empty($notifications)): ?>
              <div class="empty-state">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--text-muted);opacity:0.3;margin-bottom:16px;">
                  <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                </svg>
                <p style="color:var(--text-muted);">No notifications yet. They will appear here when tenants submit payments, apply for apartments, or request renewals.</p>
              </div>
            <?php else: ?>
              <?php foreach ($notifications as $n): ?>
                <div class="notif-card <?= $n['is_read'] ? '' : 'unread' ?>" 
                     onclick="openNotificationModal(<?= (int)$n['id'] ?>)" 
                     data-id="<?= $n['id'] ?>"
                     style="cursor:pointer;">
                  <div class="notif-icon type-<?= htmlspecialchars($n['type'] ?? 'system') ?>">
                    <svg viewBox="0 0 24 24"><?= getAdminNotifIcon($n['type'] ?? 'system') ?></svg>
                  </div>
                  <div class="notif-content">
                    <div class="notif-header">
                      <h4 class="notif-title"><?= htmlspecialchars($n['title']) ?></h4>
                      <span class="notif-time"><?= timeAgoAdmin($n['created_at']) ?></span>
                    </div>
                    <p class="notif-message"><?= htmlspecialchars($n['message']) ?></p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                      <span style="font-size:0.75rem;color:var(--text-muted);">Sender: <?= htmlspecialchars($n['actor_name'] ?? 'System') ?></span>
                      <span style="font-size:0.75rem;color:var(--primary);font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                        View Alert
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                      </span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- ═══ NOTIFICATION DETAILS MODAL ═══ -->
  <div class="modal-backdrop" id="notif-modal" style="display:none;">
    <div class="modal-content" style="max-width:520px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="notif-modal-title">Notification Details</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body" id="notif-modal-body">
        <!-- Injected via JavaScript -->
      </div>
      <div class="modal-footer">
        <button class="btn-topbar primary" onclick="closeModal('notif-modal')">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('admin');

    const allNotifs = <?= json_encode(array_map(function($n) {
        return [
            'id' => (int)$n['id'],
            'title' => $n['title'] ?? '',
            'message' => $n['message'] ?? '',
            'type' => $n['type'] ?? 'system',
            'actor' => $n['actor_name'] ?? 'System',
            'created_at' => $n['created_at'] ? date('M j, Y — g:i A', strtotime($n['created_at'])) : '',
            'is_read' => (bool)($n['is_read'] ?? 0)
        ];
    }, $notifications), JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    function openNotificationModal(id) {
      const n = allNotifs.find(item => item.id === id);
      if (!n) return;

      markAsRead(id);

      const typeLabel = (n.type || 'system').toUpperCase();
      document.getElementById('notif-modal-title').textContent = n.title;
      document.getElementById('notif-modal-body').innerHTML = `
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--border);">
          <div style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:var(--primary-light, #e6f4ea);color:var(--primary, #176b45);">
            <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:currentColor;">
              <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
          </div>
          <div style="flex:1;">
            <div style="font-weight:700;font-size:0.95rem;color:var(--text-main);">${escapeHtml(n.title)}</div>
            <div style="font-size:0.78rem;color:var(--text-muted);display:flex;gap:10px;align-items:center;margin-top:2px;">
              <span style="font-weight:600;color:var(--primary);">${escapeHtml(typeLabel)}</span>
              <span>•</span>
              <span>${escapeHtml(n.created_at)}</span>
            </div>
          </div>
        </div>
        <div style="background:#f8faf9;padding:12px 14px;border-radius:8px;border:1px solid var(--border);margin-bottom:14px;font-size:0.82rem;color:var(--text-muted);">
          <strong>Sender / Actor:</strong> ${escapeHtml(n.actor || 'System')}
        </div>
        <div style="font-size:0.92rem;color:var(--text-main);line-height:1.6;white-space:pre-wrap;background:#fff;padding:14px;border-radius:8px;border:1px solid var(--border);">
          ${escapeHtml(n.message)}
        </div>
      `;

      openModal('notif-modal');
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    // ── Mark single notification as read (AJAX) ──
    async function markAsRead(id) {
      try {
        const resp = await fetch('<?= url('/admin/mis_admin/notification/read') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await resp.json();
        if (data.success) {
          // Update UI
          const card = document.querySelector(`.notif-card[data-id="${id}"]`);
          if (card) card.classList.remove('unread');
          
          const badge = document.getElementById('unread-badge');
          const statEl = document.getElementById('stat-unread-val');
          if (badge) {
            if (data.unread > 0) {
              badge.textContent = data.unread + ' New';
            } else {
              badge.style.display = 'none';
            }
          }
          if (statEl) statEl.textContent = data.unread;
        }
      } catch (e) {
        console.error('Failed to mark notification:', e);
      }
    }

    // ── Mark ALL as read (AJAX) ──
    async function markAllRead() {
      try {
        const resp = await fetch('<?= url('/admin/mis_admin/notification/read_all') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
        });
        const data = await resp.json();
        if (data.success) {
          // Remove all unread styling
          document.querySelectorAll('.notif-card.unread').forEach(c => c.classList.remove('unread'));
          const badge = document.getElementById('unread-badge');
          if (badge) badge.style.display = 'none';
          const statEl = document.getElementById('stat-unread-val');
          if (statEl) statEl.textContent = '0';
          showToast('All notifications marked as read', 'var(--success)');
        }
      } catch (e) {
        console.error('Failed to mark all:', e);
      }
    }

    setupModalClose('notif-modal');
    if (typeof loadUserNav === 'function') loadUserNav();
  </script>
</body>
</html>

<?php
// ── PHP Helper Functions ──
function getAdminNotifIcon(string $type): string {
    $icons = [
        'payment'  => '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>',
        'approve'  => '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
        'approval' => '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
        'request'  => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        'alert'    => '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
        'warning'  => '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
        'system'   => '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>',
    ];
    return $icons[$type] ?? $icons['system'];
}

function timeAgoAdmin(string $datetime): string {
    $now = time();
    $diff = $now - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}
?>
