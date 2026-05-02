<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Notifications</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .notif-container { display: flex; flex-direction: column; gap: 12px; }
    .notif-card {
      background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border);
      display: flex; gap: 16px; transition: all 0.2s ease; cursor: pointer; position: relative;
    }
    .notif-card:hover { transform: translateX(4px); border-color: var(--damayan-accent); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .notif-card.unread::after {
      content: ''; position: absolute; top: 20px; right: 20px; width: 10px; height: 10px; border-radius: 50%; background: var(--damayan-accent);
    }
    .notif-icon {
      width: 44px; height: 44px; border-radius: 10px; background: var(--damayan-light);
      display: flex; align-items: center; justify-content: center; color: var(--damayan-accent); flex-shrink: 0;
    }
    .notif-content { flex: 1; }
    .notif-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
    .notif-title { font-weight: 700; color: var(--text-main); font-size: 0.95rem; }
    .notif-time { font-size: 0.72rem; color: var(--text-muted); font-weight: 600; }
    .notif-desc { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0; }
    
    /* Detail View Modal Styles */
    .notif-detail-meta { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px; padding: 16px; background: #f8faf9; border-radius: 8px; border: 1px solid var(--border); }
    .meta-box label { display: block; font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; }
    .meta-box p { font-size: 0.85rem; font-weight: 600; color: var(--text-main); margin: 0; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'notifications';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" /></svg>
          </div>
          <div>
            <div class="top-bar-title">Department Notifications</div>
            <div class="top-bar-subtitle">System alerts, service requests, and administrative updates</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Notifications</span>
        </div>

        <div class="notif-container" id="notif-list">
          <!-- Notifications injected via JS -->
        </div>
      </div>
    </div>
  </div>

  <!-- Notification Detail Modal -->
  <div class="modal-backdrop" id="notif-modal" style="display:none;">
    <div class="modal-content" style="max-width:550px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="modal-title">Notification Detail</h5>
        <button class="modal-close" onclick="closeModal('notif-modal')"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body">
        <div class="notif-detail-meta">
          <div class="meta-box"><label>Source</label><p id="detail-source">System</p></div>
          <div class="meta-box"><label>Category</label><p id="detail-cat">Service Request</p></div>
          <div class="meta-box"><label>Date Received</label><p id="detail-date">May 02, 2024</p></div>
          <div class="meta-box"><label>Status</label><p id="detail-status">Unread</p></div>
        </div>
        <div style="padding: 0 4px;">
          <h4 id="detail-title" style="font-family:'Lora',serif; color:var(--damayan-dark); margin-bottom:12px;">Notification Title</h4>
          <p id="detail-desc" style="font-size:0.9rem; color:var(--text-muted); line-height:1.6;"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar primary" onclick="closeModal('notif-modal')">Close Details</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');
    
    const notifications = [
      { id: 1, title: 'New Burial Request', desc: 'A new burial service request has been submitted by Ahmad Abdullah. Please review the documentation and schedule.', time: '2 hours ago', cat: 'Burial', unread: true },
      { id: 2, title: 'Charity Fund Update', desc: 'The medical assistance fund for the current quarter has been replenished. You can now approve pending medical aid requests.', time: '1 day ago', cat: 'Charity', unread: false },
      { id: 3, title: 'System Synchronization', desc: 'Damayan records are now fully synchronized with the ISCAG-MIS core database. All analytics are live.', time: '2 days ago', cat: 'System', unread: false }
    ];

    function renderNotifications() {
      const list = document.getElementById('notif-list');
      list.innerHTML = notifications.map(n => `
        <div class="notif-card ${n.unread ? 'unread' : ''}" onclick="viewNotif(${n.id})">
          <div class="notif-icon">
             <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:currentColor;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" /></svg>
          </div>
          <div class="notif-content">
            <div class="notif-header">
              <span class="notif-title">${n.title}</span>
              <span class="notif-time">${n.time}</span>
            </div>
            <p class="notif-desc">${n.desc}</p>
          </div>
        </div>
      `).join('');
    }

    function viewNotif(id) {
      const n = notifications.find(x => x.id === id);
      if(!n) return;
      
      document.getElementById('detail-title').textContent = n.title;
      document.getElementById('detail-desc').textContent = n.desc;
      document.getElementById('detail-cat').textContent = n.cat;
      document.getElementById('detail-date').textContent = n.time.includes('ago') ? 'Today' : n.time;
      document.getElementById('detail-status').textContent = n.unread ? 'Unread' : 'Read';
      
      openModal('notif-modal');
    }
    
    renderNotifications();
  </script>
</body>
</html>
