<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Analytics and History are passed from UserController
$history = $history ?? [];
$analytics = $analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0];

$hasApproved = false;
$hasPending = false;
$activeRequest = null;
foreach ($history as $req) {
    if ($req['status'] === 'approved') { $hasApproved = true; $activeRequest = $req; }
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
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--primary-female); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-female-dark); line-height: 1; }
    .stat-value.warning { color: #f59e0b; }
    .stat-value.success { color: #10b981; }

    /* ── Progress Tracker Consistency ── */
    .status-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: white; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } 100% { opacity: 1; transform: scale(1); } }

    .timeline-step.active div { animation: shadowPulse 2s infinite; }
    @keyframes shadowPulse { 0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(212, 175, 55, 0); } 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); } }

    /* ── Calendar Modal Styles ── */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-card { 
        background: white; 
        width: 95%; 
        max-width: 440px; 
        max-height: 95vh;
        display: flex;
        flex-direction: column;
        border-radius: 20px; 
        overflow: hidden; 
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); 
        transform: translateY(20px); 
        transition: all 0.3s ease; 
    }
    .modal-overlay.active .modal-card { transform: translateY(0); }
    .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: #fffbeb; flex-shrink: 0; }
    .modal-header h4 { font-family: 'Lora', serif; margin: 0; color: var(--primary-female-dark); }
    .modal-header p { margin: 4px 0 0; font-size: 0.8rem; color: var(--text-muted); }
    .btn-close-modal { background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; border-radius: 50%; transition: all 0.2s; }
    .btn-close-modal:hover { background: #fee2e2; color: #dc2626; }
    .btn-close-modal svg { width: 20px; height: 20px; fill: currentColor; }

    .modal-step { display: none; animation: fadeIn 0.4s ease; overflow-y: auto; flex: 1; }
    .modal-step.active { display: block; }
    
    .booking-container { padding: 0; }
    .calendar-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; background: white; }
    .calendar-title { font-weight: 800; color: #1a1a1a; font-size: 1rem; }
    .calendar-nav { display: flex; gap: 8px; }
    .btn-nav { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .btn-nav:hover { background: var(--primary-female-light); border-color: var(--primary-female); }
    .btn-nav svg { width: 18px; height: 18px; fill: #4b5563; }

    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
    .weekday { text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); padding: 8px 0; text-transform: uppercase; }
    .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: 10px; transition: all 0.2s; margin: 2px; }
    .calendar-day:hover:not(.disabled) { background: var(--primary-female-light); color: var(--primary-female-dark); }
    .calendar-day.today { color: var(--primary-female-dark); font-weight: 800; background: #fffbeb; }
    .calendar-day.selected { background: var(--primary-female) !important; color: #1a1a1a !important; box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3); }
    .calendar-day.booked { color: #d1d5db; cursor: not-allowed; position: relative; }
    .calendar-day.booked::after { content: ''; position: absolute; width: 14px; height: 1px; background: #d1d5db; transform: rotate(-45deg); }
    .calendar-day.disabled { color: #e5e7eb; cursor: not-allowed; pointer-events: none; }

    .legend { display: flex; gap: 16px; padding: 16px 24px; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    .time-slots-container { padding: 20px 24px; }
    .back-to-calendar { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; color: var(--primary-female-dark); cursor: pointer; margin-bottom: 16px; }
    .back-to-calendar svg { width: 16px; height: 16px; fill: currentColor; }
    .slot-group-title { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px; display: block; }
    .slots-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .slot-pill { padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; text-align: center; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .slot-pill:hover { border-color: var(--primary-female); background: var(--primary-female-light); }
    .slot-pill.selected { background: var(--primary-female); border-color: var(--primary-female-dark); color: #1a1a1a; }

    .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }
    .btn-confirm { width: 100%; padding: 14px; border-radius: 12px; border: none; background: var(--primary-female); color: #1a1a1a; font-weight: 800; cursor: pointer; transition: all 0.3s; }
    .btn-confirm:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }

    .schedule-trigger { background: white; border: 1.5px solid var(--border); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.3s; }
    .schedule-trigger:hover { border-color: var(--primary-female); background: #fffcf0; }
    .trigger-info { display: flex; align-items: center; gap: 14px; }
    .trigger-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--primary-female-light); display: flex; align-items: center; justify-content: center; color: var(--primary-female-dark); }
    .trigger-icon svg { width: 22px; height: 22px; fill: currentColor; }
    .trigger-label { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 2px; }
    .trigger-value { font-size: 0.95rem; font-weight: 700; color: #1a1a1a; }
    .trigger-arrow svg { width: 20px; height: 20px; fill: var(--text-muted); transition: transform 0.3s; }
    .schedule-trigger:hover .trigger-arrow svg { transform: translateX(4px); color: var(--primary-female-dark); }

    /* ── Notification Modal ── */
    #notif-modal.modal-overlay { z-index: 10000; }
    .notif-card { background: white; padding: 40px; border-radius: 24px; text-align: center; max-width: 400px; width: 90%; }
    .notif-icon { width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; }
    .notif-icon.success { background: #ecfdf5; color: #10b981; }
    .notif-icon.error { background: #fef2f2; color: #ef4444; }
    .notif-icon svg { width: 35px; height: 35px; fill: currentColor; }
    .notif-title { font-family: 'Lora', serif; font-size: 1.5rem; font-weight: 800; color: #1a1a1a; margin-bottom: 12px; }
    .notif-msg { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 28px; }
    .btn-notif { background: var(--primary-female); color: #1a1a1a; padding: 14px 32px; border-radius: 12px; font-weight: 800; border: none; cursor: pointer; transition: 0.3s; width: 100%; }
    .btn-notif:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3); }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'counseling_female'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Counseling Request (Female)</div>
        <div class="top-bar-subtitle">Schedule a confidential counseling session with our female guidance counselors</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Female Counseling</span>
      </div>

      <!-- ANALYTICS DASHBOARD -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">Total Requests</div>
          <div class="stat-value"><?= $analytics['total'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Currently Reviewing</div>
          <div class="stat-value warning"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Completed Sessions</div>
          <div class="stat-value success"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
      </div>

      <!-- NOTICE BOX -->
      <div class="notice-box" style="border-left: 4px solid var(--primary-female);">
        <svg viewBox="0 0 24 24" style="fill: var(--primary-female);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>This service is dedicated to sisters. All information is strictly confidential and handled by female professionals.</span>
      </div>

      <!-- MAIN FORM CARD -->
      <div class="section-card">
        <div class="section-card-header" style="border-bottom: 2px solid var(--primary-female-light);">
          <h6>
            <svg viewBox="0 0 24 24" style="fill: var(--primary-female);"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            Request New Session
          </h6>
        </div>
        <div class="section-card-body">
          <form id="counselingForm">
            <div class="form-section-title" style="color: var(--primary-female-dark);">Session Information</div>
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
                <textarea class="form-control" name="details" rows="4" placeholder="Briefly describe your situation so we can assign the best counselor for you..." required></textarea>
            </div>

            <div class="form-grid cols-2" style="margin-top: 16px;">
                <div style="grid-column: span 2;">
                    <label class="form-label">Select Preferred Schedule</label>
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
                      <div class="trigger-arrow">
                        <svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                      </div>
                    </div>
                </div>
            </div>

            <!-- MODAL STRUCTURE -->
            <div class="modal-overlay" id="scheduling-modal">
              <div class="modal-card">
                <div class="modal-header">
                  <div>
                    <h4>Select Schedule</h4>
                    <p>Pick a date and time for your session</p>
                  </div>
                  <button type="button" class="btn-close-modal" id="close-modal"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg></button>
                </div>

                <!-- Step 1: Date -->
                <div class="modal-step active" id="cal-step">
                  <div class="booking-container">
                    <div class="calendar-header">
                      <div class="calendar-title" id="cal-title">Month 2026</div>
                      <div class="calendar-nav">
                        <button type="button" class="btn-nav" onclick="changeMonth(-1)"><svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></button>
                        <button type="button" class="btn-nav" onclick="changeMonth(1)"><svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></button>
                      </div>
                    </div>
                    <div class="calendar-grid" id="calendar-grid">
                      <!-- JS Rendered -->
                    </div>
                    <div class="legend">
                      <div class="legend-item"><div class="legend-dot" style="background:var(--primary-female);"></div><span>Selected</span></div>
                      <div class="legend-item"><div class="legend-dot" style="background:#f3f4f6;"></div><span>Unavailable</span></div>
                    </div>
                  </div>
                </div>

                <!-- Step 2: Time -->
                <div class="modal-step" id="time-step">
                  <div class="back-to-calendar" onclick="backToCal()">
                    <svg viewBox="0 0 24 24"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg>
                    Back to Date: <span id="display-selected-date"></span>
                  </div>
                  <div class="time-slots-container active">
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

                <div class="modal-footer">
                  <button type="button" class="btn-confirm" id="confirm-sched" disabled>Confirm Schedule</button>
                </div>
              </div>
            </div>

            <div style="margin-top: 24px; padding: 16px; background: #fffbeb; border-radius: 8px; border: 1px solid #fef3c7;">
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



      <!-- HISTORY TABLE -->
      <div class="section-card" style="margin-top: 24px;">
        <div class="section-card-header">
            <h6>History of Requests</h6>
        </div>
        <div class="section-card-body" style="padding: 0;">
            <div class="table-wrapper">
                <table class="mis-table">
                    <thead>
                        <tr>
                            <th>Ref #</th>
                            <th>Concern</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">No request history found.</td></tr>
                        <?php else: foreach ($history as $row): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <span class="badge-status <?= $row['status'] === 'pending' ? 'pending' : ($row['status'] === 'approved' ? 'success' : 'danger') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-view-doc">View Details</button>
                                </td>
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

<!-- NOTIFICATION MODAL -->
<div class="modal-overlay" id="notif-modal">
    <div class="notif-card">
        <div class="notif-icon success" id="notif-icon-box">
            <svg id="notif-svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
        </div>
        <div class="notif-title" id="notif-title">Success!</div>
        <div class="notif-msg" id="notif-msg">Your request has been submitted.</div>
        <button type="button" class="btn-notif" onclick="closeNotif()">Close</button>
    </div>
</div>

<script>
    // Simplified Profile Completion Check (Consistency with system patterns)
    const STORAGE_KEYS = { user: 'mis_user' };
    function getUser() { return JSON.parse(localStorage.getItem(STORAGE_KEYS.user) || '{}'); }
    
    function showNotification(title, msg, type = 'success') {
        const modal = document.getElementById('notif-modal');
        const iconBox = document.getElementById('notif-icon-box');
        const svg = document.getElementById('notif-svg');
        const titleEl = document.getElementById('notif-title');
        const msgEl = document.getElementById('notif-msg');

        titleEl.innerText = title;
        msgEl.innerText = msg;
        
        if (type === 'error') {
            iconBox.className = 'notif-icon error';
            svg.innerHTML = '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>';
        } else {
            iconBox.className = 'notif-icon success';
            svg.innerHTML = '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>';
        }

        modal.classList.add('active');
    }

    function closeNotif() {
        document.getElementById('notif-modal').classList.remove('active');
        if (document.getElementById('notif-title').innerText === 'Success!') {
            window.location.reload();
        }
    }

    document.getElementById('counselingForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('gender', 'female');
        
        fetch('<?= url('/user/services/counseling/submit') ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showNotification('Success!', 'Request submitted successfully. The dashboard will now reflect your pending status.');
                // Override closeNotif to redirect to the new dashboard
                document.getElementById('notif-modal').querySelector('.btn-notif').onclick = function() {
                    window.location.href = '<?= url('/user/services/education/female/counseling') ?>'; // Wait, what is the route to the new dashboard?
                };
            } else {
                showNotification('Error', data.message || 'Failed to submit request.', 'error');
            }
        })
        .catch(err => {
            showNotification('Error', 'An unexpected error occurred.', 'error');
        });
    });

    // ── Scheduling Logic ──
    const BLOCKED_DATES = <?= json_encode($blockedDates ?? []) ?>;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = null;
    let selectedTime = null;

    const modal = document.getElementById('scheduling-modal');
    const openBtn = document.getElementById('open-calendar');
    const closeBtn = document.getElementById('close-modal');
    const calGrid = document.getElementById('calendar-grid');
    const calTitle = document.getElementById('cal-title');
    const timeStep = document.getElementById('time-step');
    const calStep = document.getElementById('cal-step');
    const confirmBtn = document.getElementById('confirm-sched');

    if(openBtn) openBtn.onclick = () => { modal.classList.add('active'); renderCalendar(); };
    if(closeBtn) closeBtn.onclick = () => closeModal();

    function closeModal() {
      modal.classList.remove('active');
      setTimeout(() => {
        calStep.classList.add('active');
        timeStep.classList.remove('active');
      }, 300);
    }

    function renderCalendar() {
      const firstDay = new Date(currentYear, currentMonth, 1).getDay();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      
      calTitle.innerText = `${monthNames[currentMonth]} ${currentYear}`;
      calGrid.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(d => `<div class="weekday">${d}</div>`).join('');
      
      for (let i = 0; i < firstDay; i++) {
        calGrid.innerHTML += `<div class="calendar-day disabled"></div>`;
      }
      
      const now = new Date();
      const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
      
      for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const blockReason = BLOCKED_DATES[dateStr];
        const isBlocked = !!blockReason;
        const isPast = dateStr < todayStr;
        const isToday = dateStr === todayStr;
        
        let classes = 'calendar-day';
        if (isBlocked) classes += ' booked';
        if (isPast) classes += ' disabled';
        if (isToday) classes += ' today';
        if (selectedDate === dateStr) classes += ' selected';
        
        const dayEl = document.createElement('div');
        dayEl.className = classes;
        dayEl.innerText = day;
        if (isBlocked) {
            dayEl.title = 'Unavailable: ' + blockReason;
            dayEl.onclick = () => showNotification('Date Unavailable', blockReason, 'error');
        } else if (!isPast) {
            dayEl.onclick = () => selectDate(dateStr);
        }
        calGrid.appendChild(dayEl);
      }
    }

    window.selectDate = (date) => {
      selectedDate = date;
      calStep.classList.remove('active');
      timeStep.classList.add('active');
      document.getElementById('display-selected-date').innerText = new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    };

    window.selectTime = (el, time) => {
      selectedTime = time;
      document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
      el.classList.add('selected');
      confirmBtn.disabled = false;
    };

    window.backToCal = () => {
      timeStep.classList.remove('active');
      calStep.classList.add('active');
    };

    if(confirmBtn) confirmBtn.onclick = () => {
      const d = new Date(selectedDate);
      const formattedDate = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      document.getElementById('selected-schedule-text').innerText = `${formattedDate} at ${selectedTime}`;
      document.getElementById('input-date').value = selectedDate;
      document.getElementById('input-time').value = selectedTime;
      closeModal();
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
