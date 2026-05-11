<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Analytics and History are passed from UserController
$history = $history ?? [];
$analytics = $analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0];

$hasActive = false;
$hasPending = false;
$activeRequest = null;
foreach ($history as $req) {
    if ($req['status'] === 'active' || $req['status'] === 'approved') { $hasActive = true; $activeRequest = $req; }
    if ($req['status'] === 'pending') { $hasPending = true; $activeRequest = $req; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Counseling</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-female: #D4AF37;
      --primary-female-dark: #B8860B;
      --primary-female-light: #FDF4E3;
    }

    /* ── Analytics Styles ── */
    .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { 
        background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s; cursor: default;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: var(--primary-female); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-female-dark); line-height: 1; }
    .stat-value.warning { color: #f59e0b; }
    .stat-value.success { color: #10b981; }

    /* ── Calendar Modal Styles ── */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; display: none; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-card { background: white; width: 95%; max-width: 440px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .modal-header h4 { font-family: 'Lora', serif; margin: 0; color: var(--primary-female-dark); }
    .modal-header p { margin: 4px 0 0; font-size: 0.8rem; color: var(--text-muted); }
    .btn-close-modal { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; border-radius: 50%; transition: 0.2s; }
    .btn-close-modal:hover { background: #f3f4f6; color: #ef4444; }

    .modal-step { display: none; }
    .modal-step.active { display: block; }
    
    .calendar-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; }
    .calendar-title { font-weight: 800; color: #1a1a1a; font-size: 1rem; }
    .calendar-nav { display: flex; gap: 8px; }
    .btn-nav { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
    .weekday { text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); padding: 8px 0; text-transform: uppercase; }
    .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: 10px; transition: 0.2s; margin: 2px; }
    .calendar-day:hover:not(.disabled) { background: var(--primary-female-light); color: var(--primary-female-dark); }
    .calendar-day.today { color: var(--primary-female-dark); font-weight: 800; background: #fffbeb; }
    .calendar-day.selected { background: var(--primary-female) !important; color: #1a1a1a !important; }
    .calendar-day.booked { color: #d1d5db; cursor: not-allowed; text-decoration: line-through; }
    .calendar-day.disabled { color: #e5e7eb; cursor: not-allowed; }

    .legend { display: flex; gap: 16px; padding: 16px 24px; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    .time-slots-container { padding: 20px 24px; }
    .back-to-calendar { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; color: var(--primary-female-dark); cursor: pointer; margin-bottom: 16px; }
    .slots-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .slot-pill { padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; text-align: center; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .slot-pill:hover { border-color: var(--primary-female); background: var(--primary-female-light); }
    .slot-pill.selected { background: var(--primary-female); border-color: var(--primary-female-dark); color: #1a1a1a; }

    .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }
    .btn-confirm { width: 100%; padding: 14px; border-radius: 12px; border: none; background: var(--primary-female); color: #1a1a1a; font-weight: 800; cursor: pointer; }
    .btn-confirm:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

    .schedule-trigger { background: white; border: 1.5px solid var(--border); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: 0.3s; }
    .schedule-trigger:hover { border-color: var(--primary-female); background: #fffcf0; }
    .trigger-info { display: flex; align-items: center; gap: 14px; }
    .trigger-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--primary-female-light); display: flex; align-items: center; justify-content: center; color: var(--primary-female-dark); }
    .trigger-icon svg { width: 22px; height: 22px; fill: currentColor; }
    .trigger-text { display: flex; flex-direction: column; }
    .trigger-label { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 2px; }
    .trigger-value { font-size: 0.95rem; font-weight: 700; color: #1a1a1a; }
    .trigger-arrow svg { width: 20px; height: 20px; fill: var(--text-muted); }

    /* ── Notification Modal ── */
    .notif-card { background: white; padding: 40px; border-radius: 24px; text-align: center; max-width: 400px; width: 90%; }
    .notif-icon { width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; }
    .notif-icon.success { background: #ecfdf5; color: #10b981; }
    .notif-icon.error { background: #fef2f2; color: #ef4444; }
    .btn-notif { background: var(--primary-female); color: #1a1a1a; padding: 14px 32px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; width: 100%; }

    /* Simplified Form Section Title */
    .form-section-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 800; color: var(--primary-female-dark); margin: 30px 0 15px; padding-bottom: 8px; border-bottom: 1.5px solid var(--primary-female-light); }
  </style>
</head>
<body>
<div class="app-wrapper">

  <?php $active_page = 'counseling_female'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Counseling Request (Female)</div>
        <div class="top-bar-subtitle">Schedule a confidential session with our female counselors</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">
          <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor; margin-right:4px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
          Dashboard
        </a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep"><svg viewBox="0 0 24 24" style="width:12px; height:12px; fill:currentColor;"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></span>
        <span class="current">Female Counseling</span>
      </div>

      <!-- ANALYTICS -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">Total Requests</div>
          <div class="stat-value"><?= $analytics['total'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Review</div>
          <div class="stat-value warning"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Active Sessions</div>
          <div class="stat-value success"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box" style="border-left: 4px solid var(--primary-female);">
        <svg viewBox="0 0 24 24" style="fill: var(--primary-female);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>Sister's Section: All consultations are held in strict confidence by female professionals.</span>
      </div>

      <!-- FORM -->
      <?php if (!$hasActive && !$hasPending): ?>
      <div class="section-card">
        <div class="section-card-header">
            <h6>New Counseling Request</h6>
        </div>
        <div class="section-card-body">
          <form id="counselingForm">
            <div class="form-section-title">Session Details</div>
            <div class="form-grid cols-2">
                <div>
                    <label class="form-label">Primary Concern <span class="required">*</span></label>
                    <select class="form-select" name="reason" required>
                        <option value="">— Select primary concern —</option>
                        <option>Family / Marital Issues</option>
                        <option>Personal / Spiritual Struggles</option>
                        <option>Parenting & Family Guidance</option>
                        <option>Youth & Academic Concerns</option>
                        <option>Financial Difficulties</option>
                        <option>Grief and Loss</option>
                        <option>Anger Management</option>
                        <option>Revert / New Muslim Support</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Preferred Session Type <span class="required">*</span></label>
                    <select class="form-select" name="type" required>
                        <option value="In-Person">In-Person Session</option>
                        <option value="Phone">Phone Consultation</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 16px;">
                <label class="form-label">Detailed Description <span class="required">*</span></label>
                <textarea class="form-control" name="details" rows="4" placeholder="Briefly describe your situation..." required></textarea>
            </div>

            <div class="form-section-title">Schedule Preference</div>
            <div class="schedule-trigger" id="open-calendar">
                <div class="trigger-info">
                    <div class="trigger-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
                    </div>
                    <div class="trigger-text">
                        <span class="trigger-label">Preferred Date & Time</span>
                        <span class="trigger-value" id="selected-schedule-text">Not selected</span>
                        <input type="hidden" name="preferred_date" id="input-date" required />
                        <input type="hidden" name="preferred_time" id="input-time" required />
                    </div>
                </div>
                <div class="trigger-arrow"><svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg></div>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: #fffbeb; border-radius: 12px; border: 1px solid #fef3c7;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="decl-female" required />
                    <label class="form-check-label" for="decl-female" style="font-size: 0.85rem; color: #92400e;">
                        I confirm that the information provided is accurate and I am requesting this session for myself.
                    </label>
                </div>
            </div>

            <div class="form-submit-row" style="margin-top: 32px;">
                <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Discard</button>
                <button type="submit" class="btn-submit" style="background: var(--primary-female); border-color: var(--primary-female-dark); color: #1a1a1a; font-weight: 800;">Submit Request</button>
            </div>
          </form>
        </div>
      </div>
      <?php else: ?>
      <!-- STATUS DISPLAY -->
      <div class="section-card">
        <div class="section-card-header">
            <h6>Current Request Status</h6>
        </div>
        <div class="section-card-body" style="text-align: center; padding: 40px 24px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary-female-light); color: var(--primary-female-dark); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <svg viewBox="0 0 24 24" style="width: 30px; height: 30px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <h5 style="font-family: 'Lora', serif; margin-bottom: 10px;">Your request is <?= $hasPending ? 'Under Review' : 'Active' ?></h5>
            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto 24px;">
                <?= $hasPending ? 'Our counselors are currently reviewing your details. We will notify you once a counselor is assigned.' : 'Your session has been approved. Please check your notifications for meeting details.' ?>
            </p>
            <span class="badge-status <?= $hasPending ? 'pending' : 'success' ?>" style="padding: 8px 24px; font-size: 0.8rem;"><?= $hasPending ? 'PENDING' : 'ACTIVE' ?></span>
        </div>
      </div>
      <?php endif; ?>

      <!-- HISTORY -->
      <div class="section-card" style="margin-top: 24px;">
        <div class="section-card-header"><h6>Request History</h6></div>
        <div class="section-card-body" style="padding: 0;">
            <div class="table-wrapper">
                <table class="mis-table">
                    <thead>
                        <tr><th>Ref #</th><th>Concern</th><th>Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);">No history found.</td></tr>
                        <?php else: foreach ($history as $row): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td><span class="badge-status <?= $row['status'] === 'pending' ? 'pending' : 'success' ?>"><?= ucfirst($row['status']) ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODALS -->
<div class="modal-overlay" id="scheduling-modal">
  <div class="modal-card">
    <div class="modal-header">
      <div><h4>Select Schedule</h4><p>Pick a date and time</p></div>
      <button type="button" class="btn-close-modal" id="close-modal">
        <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
      </button>
    </div>
    <div class="modal-step active" id="cal-step">
      <div class="calendar-header">
        <div class="calendar-title" id="cal-title">Month 2026</div>
        <div class="calendar-nav">
          <button type="button" class="btn-nav" onclick="changeMonth(-1)">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
          </button>
          <button type="button" class="btn-nav" onclick="changeMonth(1)">
            <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:currentColor;"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
          </button>
        </div>
      </div>
      <div class="calendar-grid" id="calendar-grid"></div>
      <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:var(--primary-female);"></div><span>Selected</span></div>
        <div class="legend-item"><div class="legend-dot" style="background:#f3f4f6;"></div><span>Unavailable</span></div>
      </div>
    </div>
    <div class="modal-step" id="time-step">
      <div class="time-slots-container">
        <div class="back-to-calendar" onclick="backToCal()">
          <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
          Back to Calendar
        </div>
        <span class="slot-group-title">Available Slots</span>
        <div class="slots-grid">
          <div class="slot-pill" onclick="selectTime(this, '09:00 AM')">09:00 AM</div>
          <div class="slot-pill" onclick="selectTime(this, '10:00 AM')">10:00 AM</div>
          <div class="slot-pill" onclick="selectTime(this, '11:00 AM')">11:00 AM</div>
          <div class="slot-pill" onclick="selectTime(this, '01:00 PM')">01:00 PM</div>
          <div class="slot-pill" onclick="selectTime(this, '02:00 PM')">02:00 PM</div>
          <div class="slot-pill" onclick="selectTime(this, '03:00 PM')">03:00 PM</div>
        </div>
      </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn-confirm" id="confirm-sched" disabled>Confirm Schedule</button></div>
  </div>
</div>

<div class="modal-overlay" id="notif-modal">
    <div class="notif-card">
        <div class="notif-icon success" id="notif-icon-box">
          <svg viewBox="0 0 24 24" style="width:32px; height:32px; fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        </div>
        <div class="notif-title" id="notif-title">Success!</div>
        <div class="notif-msg" id="notif-msg">Submitted.</div>
        <button type="button" class="btn-notif" onclick="location.reload()">Close</button>
    </div>
</div>

<script>
    // FORM SUBMISSION
    document.getElementById('counselingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('gender', 'female');
        fetch('<?= url('/user/services/counseling/submit') ?>', { method: 'POST', body: formData })
        .then(res => res.json()).then(data => {
            if(data.success) {
                document.getElementById('notif-modal').classList.add('active');
            } else {
                alert(data.message || 'Error');
            }
        });
    });

    // CALENDAR LOGIC
    const BLOCKED_DATES = <?= json_encode($blockedDates ?? []) ?>;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = null;
    let selectedTime = null;

    const modal = document.getElementById('scheduling-modal');
    const calGrid = document.getElementById('calendar-grid');
    const calTitle = document.getElementById('cal-title');
    const confirmBtn = document.getElementById('confirm-sched');

    document.getElementById('open-calendar').onclick = () => { modal.classList.add('active'); renderCalendar(); };
    document.getElementById('close-modal').onclick = () => modal.classList.remove('active');

    function renderCalendar() {
      const firstDay = new Date(currentYear, currentMonth, 1).getDay();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      calTitle.innerText = `${monthNames[currentMonth]} ${currentYear}`;
      calGrid.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(d => `<div class="weekday">${d}</div>`).join('');
      for (let i = 0; i < firstDay; i++) calGrid.innerHTML += `<div class="calendar-day disabled"></div>`;
      const todayStr = new Date().toISOString().split('T')[0];
      for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isBlocked = !!BLOCKED_DATES[dateStr];
        const isPast = dateStr < todayStr;
        const dayEl = document.createElement('div');
        dayEl.className = `calendar-day ${isBlocked ? 'booked' : ''} ${isPast ? 'disabled' : ''} ${selectedDate === dateStr ? 'selected' : ''}`;
        dayEl.innerText = day;
        if (!isBlocked && !isPast) dayEl.onclick = () => selectDate(dateStr);
        calGrid.appendChild(dayEl);
      }
    }

    window.selectDate = (date) => {
      selectedDate = date;
      document.getElementById('cal-step').classList.remove('active');
      document.getElementById('time-step').classList.add('active');
    };

    window.selectTime = (el, time) => {
      selectedTime = time;
      document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
      el.classList.add('selected');
      confirmBtn.disabled = false;
    };

    window.backToCal = () => {
      document.getElementById('time-step').classList.remove('active');
      document.getElementById('cal-step').classList.add('active');
    };

    confirmBtn.onclick = () => {
      document.getElementById('selected-schedule-text').innerText = `${selectedDate} at ${selectedTime}`;
      document.getElementById('input-date').value = selectedDate;
      document.getElementById('input-time').value = selectedTime;
      modal.classList.remove('active');
    };

    window.changeMonth = (delta) => {
      currentMonth += delta;
      if (currentMonth < 0) { currentMonth = 11; currentYear--; }
      else if (currentMonth > 11) { currentMonth = 0; currentYear++; }
      renderCalendar();
    };
</script>
</body>
</html>
