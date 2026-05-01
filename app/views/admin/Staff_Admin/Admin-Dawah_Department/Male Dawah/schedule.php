<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Service Schedule Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
        --male-accent: #14532d;
        --male-dark: #064e3b;
        --male-light: #f0fdf4;
    }
    .top-bar-title { color: var(--male-dark); }
    .breadcrumb-bar .current { color: var(--male-accent); }
    
    .schedule-layout { display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start; }
    
    /* Calendar Styling */
    .calendar-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .calendar-header { padding: 20px; background: var(--male-light); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .month-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--male-dark); margin: 0; }
    .cal-nav { display: flex; gap: 8px; }
    .cal-nav-btn { background: white; border: 1px solid var(--border); width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--male-dark); transition: all 0.2s; }
    .cal-nav-btn:hover { border-color: var(--male-accent); color: var(--male-accent); }
    
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
    .cal-day-label { text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; padding: 10px 0; }
    .cal-date { height: 45px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 600; cursor: pointer; border-radius: 8px; position: relative; transition: all 0.2s; }
    .cal-date:hover { background: var(--male-light); color: var(--male-accent); }
    .cal-date.today { background: var(--male-accent); color: white; }
    .cal-date.selected { border: 2px solid var(--male-accent); color: var(--male-accent); }
    .cal-date.other-month { opacity: 0.3; pointer-events: none; }
    
    .schedule-indicator { position: absolute; bottom: 6px; width: 5px; height: 5px; border-radius: 50%; background: #ef4444; }

    /* Timeline Styling */
    .day-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 24px; }
    .day-header { padding: 16px 24px; background: var(--male-light); border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .day-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--male-dark); margin: 0; }
    
    .appointment-item { padding: 18px 24px; display: flex; align-items: center; gap: 20px; border-bottom: 1px solid #f1f5f3; transition: all 0.2s; }
    .appointment-item:last-child { border-bottom: none; }
    .appointment-item:hover { background: #fafdfc; }
    
    .time-slot { width: 90px; flex-shrink: 0; font-weight: 800; color: var(--male-accent); font-size: 0.95rem; }
    .appointment-info { flex: 1; }
    .app-type { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 3px; display: flex; align-items: center; gap: 5px; }
    .app-name { font-size: 1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
    .app-desc { font-size: 0.82rem; color: var(--text-muted); }
    
    .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
    .status-pill.confirmed { background: #dcfce7; color: #166534; }
    .cal-date.blocked { background: #f1f5f9; color: #94a3b8; text-decoration: line-through; }
    .cal-date.blocked::after { content: '×'; position: absolute; top: 2px; right: 4px; font-size: 0.6rem; color: #ef4444; font-weight: 900; }
    
    .status-badge-blocked { background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; border: 1px solid #fca5a5; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'schedule';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--male-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--male-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Service Schedule</div>
            <div class="top-bar-subtitle">Monitor and coordinate all Da'wah department appointments</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
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
            <div style="padding: 15px; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); display: grid; gap: 8px;">
               <div style="display: flex; align-items: center; gap: 8px;">
                 <div style="width: 6px; height: 6px; background: #ef4444; border-radius: 50%;"></div>
                 <span>Days with scheduled appointments</span>
               </div>
               <div style="display: flex; align-items: center; gap: 8px;">
                 <div style="width: 12px; height: 12px; background: #f1f5f9; border: 1px solid var(--border); border-radius: 2px; position: relative;">
                    <span style="position: absolute; top: -3px; right: 1px; color: #ef4444; font-size: 8px; font-weight: 900;">×</span>
                 </div>
                 <span>Department Blackout (Unavailable)</span>
               </div>
            </div>
          </div>

          <!-- TIMELINE COLUMN -->
          <div id="schedule-timeline">
            <!-- SELECTION CONTEXT BAR -->
            <div id="selection-context" style="display: none; background: white; padding: 16px 24px; border-radius: 16px; border: 1px solid var(--border); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
               <div style="flex: 1;">
                 <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Selected Date</div>
                 <div id="selected-date-label" style="font-size: 1.1rem; font-weight: 800; color: var(--male-dark);">—</div>
               </div>
               <div style="display: flex; gap: 12px; align-items: center;">
                 <input type="text" id="unavail-reason-input" placeholder="Enter reason (e.g. Public Holiday)" style="display: none; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--male-accent); font-size: 0.85rem; width: 240px; outline: none;" />
                 <button id="toggle-avail-btn" class="btn-action" style="padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; border: 1.5px solid; cursor: pointer; transition: all 0.2s;"></button>
               </div>
            </div>

            <!-- JS Rendered or PHP Grouped -->
            <?php 
              $grouped = [];
              foreach ($schedules as $s) {
                  $grouped[$s['date']][] = $s;
              }
              ksort($grouped);
            ?>

            <?php if (empty($grouped)): ?>
              <div class="day-card">
                <div class="day-header"><h6 class="day-title">No Upcoming Appointments</h6></div>
                <div class="day-body" style="padding: 40px; text-align: center; color: var(--text-muted);">
                  The department schedule is currently empty.
                </div>
              </div>
            <?php else: ?>
              <?php foreach ($grouped as $date => $dayScheds): 
                $isToday = $date === date('Y-m-d');
              ?>
                <div class="day-card schedule-day-card" data-date="<?= $date ?>" style="<?= !$isToday ? 'display: none;' : '' ?>">
                  <div class="day-header">
                    <h6 class="day-title"><?= date('M d, Y', strtotime($date)) ?> <?= $isToday ? ' — Today' : '' ?></h6>
                    <?php if ($isToday): ?>
                      <span style="font-size: 0.75rem; font-weight: 700; color: var(--male-accent); background: white; padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border);">Active Day</span>
                    <?php endif; ?>
                  </div>
                  <div class="day-body">
                    <?php foreach ($dayScheds as $app): 
                      $sc = ($app['status'] === 'approved') ? 'confirmed' : 'pending';
                      $dotColor = ($app['type'] === 'Counseling') ? '#14532d' : '#B8860B';
                    ?>
                      <div class="appointment-item">
                        <div class="time-slot"><?= date('h:i A', strtotime($app['time'])) ?></div>
                        <div class="appointment-info">
                          <div class="app-type"><span style="width:6px;height:6px;border-radius:50%;background:<?= $dotColor ?>;"></span> <?= $app['type'] ?></div>
                          <div class="app-name"><?= $app['name'] ?></div>
                          <div class="app-desc"><?= $app['desc'] ?></div>
                        </div>
                        <span class="status-pill <?= $sc ?>"><?= ucfirst($app['status']) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
              
              <div id="no-selection-msg" class="day-card" style="<?= !isset($grouped[date('Y-m-d')]) ? '' : 'display: none;' ?>">
                <div class="day-header"><h6 class="day-title">Day Selection</h6></div>
                <div class="day-body" style="padding: 40px; text-align: center; color: var(--text-muted);">
                  Select a highlighted date on the calendar to view its schedule.
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const scheduledDates = <?= json_encode(array_keys($grouped)) ?>;
    let blockedDates = <?= json_encode($blockedDates ?? []) ?>;
    let selectedDate = null;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    function renderCalendar() {
      const grid = document.getElementById('calendar-grid');
      const title = document.getElementById('cal-month-title');
      
      const firstDay = new Date(currentYear, currentMonth, 1).getDay();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      
      title.innerText = `${monthNames[currentMonth]} ${currentYear}`;
      grid.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(d => `<div class="cal-day-label">${d}</div>`).join('');
      
      for (let i = 0; i < firstDay; i++) {
        grid.innerHTML += `<div class="cal-date other-month"></div>`;
      }
      
      for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const blockReason = blockedDates[dateStr];
        const isBlocked = !!blockReason;
        const hasSchedule = scheduledDates.includes(dateStr);
        const isToday = dateStr === '<?= date('Y-m-d') ?>';
        const isTodayFormatted = new Date().toISOString().split('T')[0] === dateStr;
        const isSelected = selectedDate === dateStr;
        
        grid.innerHTML += `
          <div class="cal-date ${isToday ? 'today' : ''} ${isBlocked ? 'blocked' : ''} ${isSelected ? 'selected' : ''}" 
               title="${isBlocked ? 'Reason: ' + blockReason : ''}"
               onclick="selectDate('${dateStr}')">
            ${day}
            ${hasSchedule ? '<div class="schedule-indicator"></div>' : ''}
          </div>
        `;
      }
    }

    function selectDate(date) {
      selectedDate = date;
      renderCalendar();
      
      const context = document.getElementById('selection-context');
      const label = document.getElementById('selected-date-label');
      const btn = document.getElementById('toggle-avail-btn');
      const reasonInput = document.getElementById('unavail-reason-input');
      
      context.style.display = 'flex';
      reasonInput.style.display = 'none';
      reasonInput.value = '';

      label.innerHTML = `
        ${new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
        ${blockedDates[date] ? '<div style="font-size:0.8rem; color:#ef4444; font-weight:600; margin-top:4px;">Unavailable: ' + blockedDates[date] + '</div>' : ''}
      `;
      
      const isBlocked = !!blockedDates[date];
      if(isBlocked) {
        btn.innerText = 'Mark as Available';
        btn.style.borderColor = '#10b981';
        btn.style.color = '#10b981';
        btn.onclick = () => toggleAvailability(date, 'unblock');
      } else {
        btn.innerText = 'Mark as Unavailable';
        btn.style.borderColor = '#ef4444';
        btn.style.color = '#ef4444';
        btn.onclick = () => {
          if(reasonInput.style.display === 'none') {
            reasonInput.style.display = 'block';
            reasonInput.focus();
            btn.innerText = 'Confirm Block';
          } else {
            const reason = reasonInput.value.trim() || 'Administrator Blockout';
            toggleAvailability(date, 'block', reason);
          }
        };
      }

      const cards = document.querySelectorAll('.schedule-day-card');
      const noMsg = document.getElementById('no-selection-msg');
      let found = false;
      
      cards.forEach(card => {
        if(card.getAttribute('data-date') === date) {
          card.style.display = 'block';
          found = true;
        } else {
          card.style.display = 'none';
        }
      });
      
      if (noMsg) noMsg.style.display = found ? 'none' : 'block';
    }

    async function toggleAvailability(date, status, reason = '') {
      const btn = document.getElementById('toggle-avail-btn');
      btn.disabled = true;
      btn.style.opacity = '0.5';

      try {
        const resp = await fetch('<?= url('/admin/dawah/toggle-availability') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ date, status, reason })
        });
        const res = await resp.json();
        if(res.success) {
          if(status === 'block') blockedDates[date] = reason;
          else delete blockedDates[date];
          
          selectDate(date); // Refresh UI
          if(typeof showToast === 'function') showToast(`Date marked as ${status === 'block' ? 'unavailable' : 'available'}.`);
        }
      } catch(e) {
        console.error(e);
      } finally {
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    }

    function changeMonth(delta) {
      currentMonth += delta;
      if (currentMonth < 0) { currentMonth = 11; currentYear--; }
      if (currentMonth > 11) { currentMonth = 0; currentYear++; }
      renderCalendar();
    }

    renderCalendar();
  </script>
</body>
</html>
