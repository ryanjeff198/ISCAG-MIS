<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Female']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Service Schedule Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .schedule-layout { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }
    
    /* Calendar Styling */
    .calendar-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .calendar-header { padding: 20px; background: rgba(199,154,43,0.05); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .month-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); margin: 0; }
    .cal-nav { display: flex; gap: 8px; }
    .cal-nav-btn { background: white; border: 1px solid var(--border); width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--primary-dark); transition: all 0.2s; }
    .cal-nav-btn:hover { border-color: var(--accent); color: var(--accent); }
    
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
    .cal-day-label { text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; padding: 10px 0; }
    .cal-date { height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 600; cursor: pointer; border-radius: 8px; position: relative; transition: all 0.2s; }
    .cal-date:hover { background: rgba(199,154,43,0.08); color: var(--accent); }
    .cal-date.today { background: var(--accent); color: white; }
    .cal-date.selected { border: 2px solid var(--accent); color: var(--accent); }
    .cal-date.other-month { opacity: 0.3; pointer-events: none; }
    
    .schedule-indicator { position: absolute; bottom: 6px; width: 5px; height: 5px; border-radius: 50%; background: var(--danger); }

    /* Timeline Styling */
    .day-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 24px; }
    .day-header { padding: 16px 24px; background: rgba(199,154,43,0.05); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .day-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); margin: 0; }
    
    .appointment-item { padding: 18px 24px; display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #f1f5f3; transition: all 0.2s; }
    .appointment-item:last-child { border-bottom: none; }
    .appointment-item:hover { background: #fafdfc; }
    
    .time-slot { width: 90px; flex-shrink: 0; font-weight: 800; color: var(--accent); font-size: 0.95rem; }
    .appointment-info { flex: 1; }
    .app-type { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 3px; display: flex; align-items: center; gap: 5px; }
    .app-name { font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
    .app-desc { font-size: 0.82rem; color: var(--text-muted); }
    
    .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
    .status-pill.confirmed { background: #dcfce7; color: #166534; }
    .cal-date.blocked { background: #f1f5f9; color: #94a3b8; text-decoration: line-through; }
    .cal-date.blocked::after { content: '×'; position: absolute; top: 2px; right: 4px; font-size: 0.6rem; color: var(--danger); font-weight: 900; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'schedule';
      $dawah_type = 'female';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Departmental Schedule</div>
          <div class="top-bar-subtitle">Female Da'wah Department — Monitor and coordinate all departmental appointments</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Departmental Schedule</span>
        </div>

        <div class="schedule-layout">
          <!-- CALENDAR COLUMN -->
          <div class="calendar-card">
            <div class="calendar-header">
              <h6 class="month-title" id="cal-month-title">May 2026</h6>
              <div class="cal-nav">
                <button class="cal-nav-btn" onclick="changeMonth(-1)"><svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></button>
                <button class="cal-nav-btn" onclick="changeMonth(1)"><svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></button>
              </div>
            </div>
            <div class="cal-grid" id="calendar-grid">
              <!-- JS Rendered -->
            </div>
          </div>

          <!-- TIMELINE COLUMN -->
          <div class="timeline-column">
             <!-- Selection Context Bar -->
             <div id="selection-context" style="display: none; background: white; padding: 16px 24px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 24px; justify-content: space-between; align-items: center;">
               <div style="flex: 1;">
                 <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Selected Date</div>
                 <div id="selected-date-label" style="font-size: 1.1rem; font-weight: 800; color: var(--primary-dark);">—</div>
               </div>
               <div style="display: flex; gap: 12px; align-items: center;">
                 <button id="assign-sched-btn" class="btn-action" style="padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; border: 1.5px solid var(--accent); background:white; color:var(--accent); cursor: pointer; transition: all 0.2s;">Assign Schedule</button>
                 <button id="toggle-avail-btn" class="btn-action" style="padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; border: 1.5px solid; cursor: pointer; transition: all 0.2s;"></button>
               </div>
            </div>

            <div class="day-card">
              <div class="day-header">
                <h6 class="day-title" id="selected-day-title">Today, <?= date('M d') ?></h6>
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent);">SCHEDULED APPOINTMENTS</div>
              </div>
              <div class="day-body" id="timeline-body">
                 <!-- Items Rendered via JS -->
                 <?php 
                    $grouped = [];
                    foreach ($schedules as $s) {
                        $grouped[$s['date']][] = $s;
                    }
                    ksort($grouped);
                 ?>
                 <?php if (empty($grouped)): ?>
                   <div style="padding: 40px; text-align: center; color: var(--text-muted);">No appointments found.</div>
                 <?php else: ?>
                    <?php foreach ($grouped as $date => $dayScheds): 
                      $isToday = $date === date('Y-m-d');
                    ?>
                      <div class="schedule-day-group" data-date="<?= $date ?>" style="<?= !$isToday ? 'display: none;' : '' ?>">
                        <?php foreach ($dayScheds as $app): 
                          $sc = ($app['status'] === 'approved') ? 'badge-approved' : 'badge-pending';
                        ?>
                          <div class="appointment-item">
                            <div class="time-slot"><?= date('h:i A', strtotime($app['time'])) ?></div>
                            <div class="appointment-info">
                              <div class="app-type"><span style="width:6px;height:6px;border-radius:50%;background:var(--primary);"></span> <?= $app['type'] ?></div>
                              <div class="app-name">
                                <?= $app['name'] ?>
                                <?php if (!empty($app['age'])): ?>
                                  <span style="font-size: 0.75rem; font-weight: 800; color: var(--accent); margin-left: 8px;">(<?= $app['age'] ?> y/o)</span>
                                <?php endif; ?>
                              </div>
                              <div class="app-desc"><?= $app['desc'] ?></div>
                            </div>
                            <span class="badge-status <?= $sc ?>"><?= ucfirst($app['status']) ?></span>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php endforeach; ?>
                 <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ASSIGN SCHEDULE MODAL -->
  <div id="assign-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); z-index:9999; display:none; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,0.15); overflow:hidden; animation:modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
      <div style="padding:24px; background:rgba(199,154,43,0.05); border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h6 style="margin:0; font-family:'Lora',serif; font-size:1.1rem; font-weight:700; color:var(--primary-dark);">Assign New Schedule</h6>
        <button onclick="closeAssignModal()" style="background:none; border:none; cursor:pointer; color:var(--text-muted);"><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <form onsubmit="submitAssignment(event)" style="padding:24px;">
        <input type="hidden" id="assign-date-input">
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Event Title / Subject</label>
          <input type="text" id="assign-title" placeholder="e.g. Beginners Qur'an Class" required style="width:100%; padding:12px; border-radius:10px; border:1.5px solid var(--border); font-family:inherit;">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Event Type</label>
            <select id="assign-type" style="width:100%; padding:12px; border-radius:10px; border:1.5px solid var(--border); font-family:inherit;">
              <option value="Class">Class / Subject</option>
              <option value="Seminar">Seminar</option>
              <option value="Special Session">Special Session</option>
              <option value="Other">Other Event</option>
            </select>
          </div>
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Start Time</label>
            <input type="time" id="assign-time" required style="width:100%; padding:12px; border-radius:10px; border:1.5px solid var(--border); font-family:inherit;">
          </div>
        </div>
        <div style="margin-bottom:24px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Description / Venue</label>
          <textarea id="assign-desc" placeholder="Details about the class or session..." style="width:100%; padding:12px; border-radius:10px; border:1.5px solid var(--border); font-family:inherit; height:80px; resize:none;"></textarea>
        </div>
        <div style="display:flex; gap:12px;">
          <button type="button" onclick="closeAssignModal()" style="flex:1; padding:12px; border-radius:10px; border:1.5px solid var(--border); background:white; font-weight:700; cursor:pointer;">Cancel</button>
          <button type="submit" style="flex:2; padding:12px; border-radius:10px; border:none; background:var(--primary); color:white; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(15,92,58,0.2);">Save Assignment</button>
        </div>
      </form>
    </div>
  </div>

  <!-- BLOCK DATE MODAL -->
  <div id="block-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); z-index:9999; display:none; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:20px; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.15); overflow:hidden; animation:modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
      <div style="padding:24px; background:rgba(185,28,28,0.05); border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h6 style="margin:0; font-family:'Lora',serif; font-size:1.1rem; font-weight:700; color:#b91c1c;">Mark as Unavailable</h6>
        <button onclick="closeBlockModal()" style="background:none; border:none; cursor:pointer; color:var(--text-muted);"><svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <form onsubmit="submitBlock(event)" style="padding:24px;">
        <input type="hidden" id="block-date-input">
        <div style="margin-bottom:24px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">Reason for Blackout</label>
          <textarea id="block-reason" placeholder="e.g. Departmental Meeting, Public Holiday, etc." required style="width:100%; padding:12px; border-radius:10px; border:1.5px solid var(--border); font-family:inherit; height:100px; resize:none;"></textarea>
        </div>
        <div style="display:flex; gap:12px;">
          <button type="button" onclick="closeBlockModal()" style="flex:1; padding:12px; border-radius:10px; border:1.5px solid var(--border); background:white; font-weight:700; cursor:pointer;">Cancel</button>
          <button type="submit" style="flex:2; padding:12px; border-radius:10px; border:none; background:#b91c1c; color:white; font-weight:700; cursor:pointer; box-shadow:0 8px 20px rgba(185,28,28,0.2);">Confirm Block</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ALERT MODAL -->
  <div id="alert-modal" style="position:fixed; inset:0; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center;" onclick="closeAlertModal()">
    <div style="background:white; border-radius:20px; width:100%; max-width:380px; box-shadow:0 20px 60px rgba(0,0,0,0.15); overflow:hidden; animation:modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); text-align:center; padding:40px 32px;" onclick="event.stopPropagation()">
      <div id="alert-icon" style="width:64px; height:64px; border-radius:50%; margin:0 auto 20px; display:flex; align-items:center; justify-content:center;"></div>
      <h6 id="alert-title" style="margin:0 0 8px; font-family:'Lora',serif; font-size:1.2rem; font-weight:700;"></h6>
      <p id="alert-message" style="margin:0 0 24px; color:var(--text-muted); font-size:0.9rem; line-height:1.5;"></p>
      <button onclick="closeAlertModal()" style="padding:12px 40px; border-radius:10px; border:none; font-weight:700; font-size:0.9rem; cursor:pointer; transition:all 0.2s;" id="alert-dismiss-btn">OK</button>
    </div>
  </div>

  <style>
    @keyframes modalPop { from { opacity:0; transform:scale(0.9) translateY(20px); } to { opacity:1; transform:scale(1) translateY(0); } }
    @keyframes modalFadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
  </style>

  <script>
    function showAlert(title, message, type) {
      const modal = document.getElementById('alert-modal');
      const icon = document.getElementById('alert-icon');
      const titleEl = document.getElementById('alert-title');
      const msgEl = document.getElementById('alert-message');
      const btn = document.getElementById('alert-dismiss-btn');

      titleEl.textContent = title;
      msgEl.textContent = message;

      if (type === 'success') {
        icon.style.background = '#dcfce7';
        icon.innerHTML = '<svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:#16a34a;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';
        titleEl.style.color = '#166534';
        btn.style.background = '#16a34a';
        btn.style.color = 'white';
      } else {
        icon.style.background = '#fef2f2';
        icon.innerHTML = '<svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:#dc2626;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';
        titleEl.style.color = '#991b1b';
        btn.style.background = '#dc2626';
        btn.style.color = 'white';
      }

      modal.style.display = 'flex';

      if (window._alertTimer) clearTimeout(window._alertTimer);
      window._alertTimer = setTimeout(() => closeAlertModal(), 2500);
    }

    function closeAlertModal() {
      document.getElementById('alert-modal').style.display = 'none';
      if (window._alertTimer) clearTimeout(window._alertTimer);
    }
  </script>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    const BASE_URL = '<?= rtrim(BASE_URL, '/') ?>';
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const scheduledDates = <?= json_encode(array_keys($grouped)) ?>;
    const blockedDates = <?= json_encode(array_keys($blockedDates)) ?>;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    function renderCalendar() {
      const grid = document.getElementById('calendar-grid');
      const title = document.getElementById('cal-month-title');
      const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
      
      title.textContent = `${months[currentMonth]} ${currentYear}`;
      
      let html = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].map(d => `<div class="cal-day-label">${d}</div>`).join('');
      
      const first = new Date(currentYear, currentMonth, 1).getDay();
      const last = new Date(currentYear, currentMonth + 1, 0).getDate();
      
      for(let i=0; i<first; i++) html += '<div class="cal-date other-month"></div>';
      
      const todayStr = '<?= date('Y-m-d') ?>';
      
      for(let d=1; d<=last; d++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const isToday = dateStr === todayStr;
        const hasApp = scheduledDates.includes(dateStr);
        const isBlocked = blockedDates.includes(dateStr);
        
        html += `
          <div class="cal-date ${isToday?'today':''} ${isBlocked?'blocked':''}" data-date="${dateStr}" onclick="selectDate('${dateStr}')">
            ${d}
            ${hasApp?'<div class="schedule-indicator"></div>':''}
          </div>
        `;
      }
      
      grid.innerHTML = html;
    }

    function changeMonth(dir) {
      currentMonth += dir;
      if(currentMonth > 11) { currentMonth = 0; currentYear++; }
      if(currentMonth < 0) { currentMonth = 11; currentYear--; }
      renderCalendar();
    }

    function selectDate(dateStr) {
      document.querySelectorAll('.cal-date').forEach(el => el.classList.remove('selected'));
      const target = document.querySelector(`.cal-date[data-date="${dateStr}"]`);
      if (!target) return;
      target.classList.add('selected');
      
      const dateObj = new Date(dateStr + 'T00:00:00');
      const label = dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
      document.getElementById('selected-day-title').textContent = label;
      
      document.querySelectorAll('.schedule-day-group').forEach(el => {
        el.style.display = el.getAttribute('data-date') === dateStr ? 'block' : 'none';
      });

      // Show selection context bar
      const context = document.getElementById('selection-context');
      context.style.display = 'flex';
      document.getElementById('selected-date-label').textContent = label;
      
      const isBlocked = blockedDates.includes(dateStr);
      const btn = document.getElementById('toggle-avail-btn');
      
      if(isBlocked) {
        btn.textContent = 'Mark as Available';
        btn.style.borderColor = '#16a34a';
        btn.style.color = '#16a34a';
        btn.onclick = () => unblockDate(dateStr);
      } else {
        btn.textContent = 'Mark as Unavailable';
        btn.style.borderColor = '#dc2626';
        btn.style.color = '#dc2626';
        btn.onclick = () => showBlockModal(dateStr);
      }

      document.getElementById('assign-sched-btn').onclick = () => showAssignModal(dateStr);
    }

    // ── TIMELINE HELPERS ──
    function addToTimeline(dateStr, html) {
      const body = document.getElementById('timeline-body');
      let group = body.querySelector(`.schedule-day-group[data-date="${dateStr}"]`);
      if (!group) {
        group = document.createElement('div');
        group.className = 'schedule-day-group';
        group.setAttribute('data-date', dateStr);
        group.style.display = 'block';
        body.appendChild(group);
      }
      group.style.display = 'block';
      group.insertAdjacentHTML('beforeend', html);
      const empty = body.querySelector('div[style*="text-align: center"]');
      if (empty && empty.textContent.includes('No appointments')) empty.remove();
    }

    function formatTime12(t) {
      if (!t) return '';
      const [h, m] = t.split(':');
      const hr = parseInt(h);
      const ampm = hr >= 12 ? 'PM' : 'AM';
      return ((hr % 12) || 12) + ':' + m + ' ' + ampm;
    }

    function showDateInTimeline(dateStr) {
      const dateObj = new Date(dateStr + 'T00:00:00');
      const label = dateObj.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
      document.getElementById('selected-day-title').textContent = label;
      document.querySelectorAll('.schedule-day-group').forEach(el => {
        el.style.display = el.getAttribute('data-date') === dateStr ? 'block' : 'none';
      });
    }

    // ── BLOCK / UNBLOCK ──
    function showBlockModal(dateStr) {
      document.getElementById('block-date-input').value = dateStr;
      document.getElementById('block-reason').value = '';
      document.getElementById('block-modal').style.display = 'flex';
    }

    function closeBlockModal() {
      document.getElementById('block-modal').style.display = 'none';
    }

    async function submitBlock(e) {
      e.preventDefault();
      const btn = e.target.querySelector('button[type="submit"]');
      const origText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Blocking...';

      const date = document.getElementById('block-date-input').value;
      const reason = document.getElementById('block-reason').value;

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/availability/toggle', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ date, status: 'block', reason })
        });
        const text = await res.text();
        let result;
        try { result = JSON.parse(text); } catch(pe) { throw new Error('Server returned invalid response: ' + text.substring(0, 200)); }

        if (result.success) {
          if (!blockedDates.includes(date)) blockedDates.push(date);
          renderCalendar();
          addToTimeline(date, `
            <div class="appointment-item" style="background:#fef2f2;">
              <div class="time-slot" style="color:#dc2626;">BLOCKED</div>
              <div class="appointment-info">
                <div class="app-type"><span style="width:6px;height:6px;border-radius:50%;background:#dc2626;"></span> Date Unavailable</div>
                <div class="app-name" style="color:#dc2626;">Blocked by Admin</div>
                <div class="app-desc">${reason || 'No reason provided'}</div>
              </div>
              <span class="badge-status badge-rejected">Blocked</span>
            </div>
          `);
          showDateInTimeline(date);
          closeBlockModal();
          showAlert('Date Blocked', `${date} has been marked as unavailable.`, 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          throw new Error(result.error || 'Server returned failure');
        }
      } catch (err) {
        console.error('Block Error:', err);
        showAlert('Error', 'Failed to block date: ' + err.message, 'error');
        btn.disabled = false;
        btn.textContent = origText;
      }
    }

    async function unblockDate(dateStr) {
      if (!confirm(`Unblock ${dateStr} and make it available again?`)) return;

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/availability/toggle', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ date: dateStr, status: 'unblock' })
        });
        const text = await res.text();
        let result;
        try { result = JSON.parse(text); } catch(pe) { throw new Error('Server returned invalid response: ' + text.substring(0, 200)); }

        if (result.success) {
          const idx = blockedDates.indexOf(dateStr);
          if (idx > -1) blockedDates.splice(idx, 1);
          renderCalendar();
          showAlert('Date Unblocked', `${dateStr} is now available.`, 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          throw new Error(result.error || 'Server returned failure');
        }
      } catch (err) {
        console.error('Unblock Error:', err);
        showAlert('Error', 'Failed to unblock date: ' + err.message, 'error');
      }
    }

    // ── ASSIGN SCHEDULE ──
    function showAssignModal(dateStr) {
      document.getElementById('assign-date-input').value = dateStr;
      document.getElementById('assign-title').value = '';
      document.getElementById('assign-desc').value = '';
      document.getElementById('assign-modal').style.display = 'flex';
    }

    function closeAssignModal() {
      document.getElementById('assign-modal').style.display = 'none';
    }

    async function submitAssignment(e) {
      e.preventDefault();
      const btn = e.target.querySelector('button[type="submit"]');
      const origText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Saving...';

      const data = {
        date: document.getElementById('assign-date-input').value,
        title: document.getElementById('assign-title').value,
        type: document.getElementById('assign-type').value,
        time: document.getElementById('assign-time').value,
        description: document.getElementById('assign-desc').value
      };

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/schedule/assign', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const text = await res.text();
        let result;
        try { result = JSON.parse(text); } catch(pe) { throw new Error('Server returned invalid response: ' + text.substring(0, 200)); }

        if (result.success) {
          if (!scheduledDates.includes(data.date)) scheduledDates.push(data.date);
          renderCalendar();
          addToTimeline(data.date, `
            <div class="appointment-item" style="animation: modalFadeIn 0.3s ease;">
              <div class="time-slot">${formatTime12(data.time)}</div>
              <div class="appointment-info">
                <div class="app-type"><span style="width:6px;height:6px;border-radius:50%;background:var(--primary);"></span> ${data.type}</div>
                <div class="app-name">${data.title}</div>
                <div class="app-desc">${data.description || 'No description'}</div>
              </div>
              <span class="badge-status badge-approved">Assigned</span>
            </div>
          `);
          showDateInTimeline(data.date);
          closeAssignModal();
          showAlert('Schedule Assigned', `"${data.title}" has been added to ${data.date}.`, 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          throw new Error(result.error || 'Server returned failure');
        }
      } catch (err) {
        console.error('Assign Error:', err);
        showAlert('Error', 'Failed to assign schedule: ' + err.message, 'error');
        btn.disabled = false;
        btn.textContent = origText;
      }
    }

    renderCalendar();
  </script>
</body>
</html>
