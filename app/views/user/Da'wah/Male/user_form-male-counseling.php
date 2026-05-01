<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Counseling Request</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        /* ── Scheduling Calendar Styles ── */
        .booking-container {
            background: #fff; border: 1px solid var(--border); border-radius: 12px;
            overflow: hidden; margin-top: 10px;
        }
        .calendar-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 20px; background: #f9fafb; border-bottom: 1px solid var(--border);
        }
        .calendar-title { font-weight: 700; color: var(--primary-dark); font-size: 1rem; }
        .calendar-nav { display: flex; gap: 8px; }
        .btn-nav {
            width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
            background: #fff; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-nav:hover { background: var(--primary-light); border-color: var(--primary); }
        .btn-nav svg { width: 16px; height: 16px; fill: var(--text-main); }

        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
        .weekday {
            text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted);
            text-transform: uppercase; padding: 10px 0;
        }
        .calendar-day {
            aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 600; cursor: pointer; border-radius: 8px;
            position: relative; transition: all 0.2s;
        }
        .calendar-day:hover:not(.disabled) { background: var(--primary-light); color: var(--primary); }
        .calendar-day.today { color: var(--primary); font-weight: 800; }
        .calendar-day.today::after {
            content: ''; position: absolute; bottom: 6px; width: 4px; height: 4px;
            border-radius: 50%; background: var(--primary);
        }
        .calendar-day.selected { background: var(--primary) !important; color: #fff !important; }
        .calendar-day.selected::after {
            content: 'SELECTED'; position: absolute; bottom: 4px; font-size: 0.5rem;
            font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.9);
        }
        .calendar-day.disabled { color: #d1d5db; cursor: not-allowed; }
        .calendar-day.booked { background: #f3f4f6 !important; color: #9ca3af !important; cursor: not-allowed; }
        .calendar-day.booked::after {
            content: 'UNAVAILABLE'; position: absolute; bottom: 4px; font-size: 0.45rem;
            font-weight: 800; letter-spacing: 0.02em; color: #d1d5db;
        }
        .calendar-day.limited::before {
            content: ''; position: absolute; top: 6px; right: 6px; width: 6px; height: 6px;
            border-radius: 50%; background: var(--accent);
        }

        .legend { display: flex; gap: 16px; padding: 12px 20px; font-size: 0.75rem; border-top: 1px solid var(--border); }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

        .time-slots-container { padding: 20px; border-top: 1px dashed var(--border); display: none; }
        .time-slots-container.active { display: block; animation: fadeIn 0.3s ease; }
        .slot-group-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px; display: block; }
        .slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 20px; }
        .slot-pill {
            padding: 10px; border: 1.5px solid var(--border); border-radius: 10px;
            text-align: center; font-size: 0.85rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; flex-direction: column; gap: 2px;
        }
        .slot-pill:hover:not(.disabled) { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }
        .slot-pill.selected { background: var(--primary); border-color: var(--primary); color: #fff; }
        .slot-pill.disabled { background: #f9fafb; color: #d1d5db; cursor: not-allowed; border-color: #f3f4f6; }
        .slot-capacity { font-size: 0.65rem; font-weight: 400; opacity: 0.8; }

        /* ── Compact Schedule Trigger ── */
        .schedule-trigger {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px; background: #fff; border: 1.5px solid var(--border);
            border-radius: 10px; cursor: pointer; transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .schedule-trigger:hover { border-color: var(--primary); background: var(--primary-light); }
        .schedule-trigger.locked { 
            cursor: not-allowed; background: #f9fafb; border-color: #e5e7eb; 
            box-shadow: none; pointer-events: none;
        }
        .schedule-trigger.locked .trigger-icon { background: #e5e7eb; color: #9ca3af; }
        .schedule-trigger.locked .trigger-value { color: #9ca3af; }
        .schedule-trigger.locked .trigger-arrow { display: none; }
        .schedule-trigger.locked::after {
            content: 'LOCKED'; position: absolute; right: 16px; top: 12px;
            font-size: 0.65rem; font-weight: 800; color: #9ca3af;
            background: #f3f4f6; padding: 2px 8px; border-radius: 4px;
        }

        .trigger-info { display: flex; align-items: center; gap: 12px; }
        .trigger-icon {
            width: 36px; height: 36px; background: var(--primary-light); color: var(--primary);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .trigger-icon svg { width: 20px; height: 20px; fill: currentColor; }
        .trigger-text { display: flex; flex-direction: column; }
        .trigger-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: -2px; }
        .trigger-value { font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); }
        .trigger-arrow { color: var(--text-muted); opacity: 0.5; }

        /* ── Modal Styles ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15, 30, 22, 0.4); backdrop-filter: blur(4px);
            display: none; align-items: center; justify-content: center; z-index: 9999;
            padding: 20px; animation: fadeIn 0.3s ease;
        }
        .modal-overlay.active { display: flex; }
        .modal-card {
            background: #fff; border-radius: 16px; width: 100%; max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .modal-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-header h4 { margin: 0; font-family: 'Lora', serif; color: var(--primary-dark); }
        .modal-header p { margin: 4px 0 0; font-size: 0.75rem; color: var(--text-muted); }
        
        .modal-step { display: none; animation: fadeIn 0.4s ease; }
        .modal-step.active { display: block; }
        
        .back-to-calendar {
            display: flex; align-items: center; gap: 6px; padding: 10px 0;
            color: var(--primary); font-size: 0.85rem; font-weight: 700; cursor: pointer;
            margin: 0 24px; border-bottom: 1px solid var(--primary-light);
        }
        .back-to-calendar:hover { color: var(--primary-dark); }
        .back-to-calendar svg { width: 16px; height: 16px; fill: currentColor; }

        .btn-close-modal {
            background: none; border: none; cursor: pointer; color: var(--text-muted);
            padding: 4px; border-radius: 50%; display: flex; transition: all 0.2s;
        }
        .btn-close-modal:hover { background: #f3f4f6; color: var(--danger); }
        .btn-close-modal svg { width: 20px; height: 20px; fill: currentColor; }

        .modal-footer {
            padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 12px;
        }
        .btn-confirm {
            padding: 10px 24px; background: var(--primary); color: #fff; border: none;
            border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-confirm:disabled { background: #d1d5db; cursor: not-allowed; }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ── Confirmation Modal ── */
        .confirm-modal-overlay {
            position: fixed; inset: 0; background: rgba(15, 30, 22, 0.45); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 10000;
            padding: 20px; animation: fadeIn 0.3s ease;
        }
        .confirm-modal-overlay.active { display: flex; }
        .confirm-modal-card {
            background: #fff; border-radius: 20px; width: 100%; max-width: 420px;
            padding: 32px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.25);
            animation: modalPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .confirm-icon {
            width: 72px; height: 72px; background: #fff4e5; color: #ff9800;
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; transform: rotate(-5deg);
            box-shadow: 0 10px 20px rgba(255, 152, 0, 0.15);
        }
        .confirm-icon svg { width: 38px; height: 38px; fill: currentColor; }
        .confirm-title { font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 800; color: #1a1a1a; margin-bottom: 12px; letter-spacing: -0.02em; }
        .confirm-msg { font-size: 0.95rem; color: #555; line-height: 1.6; margin-bottom: 28px; }
        .confirm-msg strong { color: #d32f2f; font-weight: 800; }
        
        .confirm-buttons { display: flex; flex-direction: column; gap: 12px; }
        .btn-confirm-final {
            padding: 14px; background: linear-gradient(135deg, #ff9800, #f57c00); color: #fff; border: none;
            border-radius: 12px; font-weight: 800; font-size: 0.95rem; cursor: pointer;
            box-shadow: 0 8px 20px rgba(245, 124, 0, 0.25); transition: all 0.2s;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .btn-confirm-final:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(245, 124, 0, 0.35); }
        .btn-cancel-final {
            padding: 14px; background: #f8f9fa; border: 1px solid #e9ecef;
            color: #6c757d; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cancel-final:hover { background: #e9ecef; color: #343a40; }

        /* ── Analytics Styles ── */
        .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { 
            background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
            display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: all 0.3s; cursor: default;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--primary); }
        .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }
        .stat-value.warning { color: #f59e0b; }
        .stat-value.success { color: #10b981; }

        .btn-view-doc {
            padding: 6px 12px; font-size: 0.75rem; font-weight: 700; color: var(--primary);
            background: var(--primary-light); border: 1px solid transparent; border-radius: 6px;
            cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        }
        .btn-view-doc:hover { background: var(--primary); color: #fff; }

        @keyframes modalPop {
            0% { opacity: 0; transform: scale(0.9) translateY(20px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'counseling_male'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Counseling Request</div>
        <div class="top-bar-subtitle">Schedule a confidential counseling session with our male counselors</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Counseling Request</span>
      </div>

      <!-- ANALYTICS DASHBOARD -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">Total Applications</div>
          <div class="stat-value" id="ana-total">0</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Approval</div>
          <div class="stat-value warning" id="ana-pending">0</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Successful Sessions</div>
          <div class="stat-value success" id="ana-approved">0</div>
        </div>
      </div>

      <!-- FORM HEADER BANNER -->
      <div class="section-card" style="margin-bottom:20px;">
        <div class="form-page-header">
          <h4>Counseling Request Form</h4>
          <p>Da'wah Department — All information will be kept strictly confidential.</p>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>This form is exclusively for <strong>male clients</strong>. All sessions and details submitted are handled with complete confidentiality in accordance with Islamic ethical principles.</span>
      </div>

      <!-- MAIN FORM CARD -->
      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            Counseling Request Form
          </h6>
          <span style="font-size:0.75rem;color:var(--text-muted);">Reference No.: <strong style="color:var(--primary);">#MC-AUTO</strong></span>
        </div>
        <div class="section-card-body">

          <!-- Client Information -->
          <div class="form-section-title">Client Information</div>
          <div class="form-grid cols-3">
            <div>
              <label class="form-label">Full Name <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Enter your full name" />
            </div>
            <div>
              <label class="form-label">Age <span class="required">*</span></label>
              <input type="number" class="form-control" placeholder="Age" min="0" />
            </div>
            <div>
              <label class="form-label">Civil Status</label>
              <select class="form-select">
                <option value="">— Select —</option>
                <option>Single</option>
                <option>Married</option>
                <option>Widower</option>
                <option>Divorced</option>
              </select>
            </div>
          </div>
          <div class="form-grid cols-2-1" style="margin-bottom:24px;">
            <div>
              <label class="form-label">Complete Address <span class="required">*</span></label>
              <input type="text" class="form-control" placeholder="Street, Barangay, City, Province" />
            </div>
            <div>
              <label class="form-label">Contact Number <span class="required">*</span></label>
              <input type="tel" class="form-control" placeholder="09XX-XXX-XXXX" />
            </div>
          </div>

          <!-- Counseling Details -->
          <div class="form-section-title">Counseling Details</div>
          <div style="margin-bottom:16px;">
            <label class="form-label">Reason for Counseling <span class="required">*</span></label>
            <select class="form-select" id="reason-select" style="max-width:380px;margin-bottom:10px;">
              <option value="">— Select primary concern —</option>
              <option>Family / Marital Issues</option>
              <option>Personal / Spiritual Struggles</option>
              <option>Youth / Academic Concerns</option>
              <option>Financial Difficulties</option>
              <option>Grief and Loss</option>
              <option>Anger Management</option>
              <option value="Other">Other</option>
            </select>
            
            <div id="other-reason-container" style="display:none; margin-bottom:10px; animation:fadeIn 0.3s ease;">
                <label class="form-label" style="font-size:0.75rem; color:var(--primary);">Please specify other reason:</label>
                <input type="text" class="form-control" placeholder="Type your specific reason here..." style="max-width:380px; border-color:var(--primary-light);">
            </div>

            <textarea class="form-control" rows="4" placeholder="Please briefly describe your concern or reason for requesting counseling. This will remain confidential..."></textarea>
          </div>
          <div style="margin-bottom:24px;">
            <label class="form-label">Available Schedule <span class="required">*</span></label>
            
            <!-- Compact Trigger -->
            <div class="schedule-trigger" id="open-scheduler">
                <div class="trigger-info">
                    <div class="trigger-icon">
                        <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
                    </div>
                    <div class="trigger-text">
                        <span class="trigger-label">Appointment Time</span>
                        <span class="trigger-value" id="display-schedule">Select Date and Time</span>
                    </div>
                </div>
                <div class="trigger-arrow">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M7 10l5 5 5-5z"/></svg>
                </div>
            </div>

            <!-- Scheduler Modal -->
            <div class="modal-overlay" id="scheduler-modal">
                <div class="modal-card">
                    <div class="modal-header">
                        <div>
                            <h4 id="modal-step-title">Select Appointment Date</h4>
                            <p id="modal-step-subtitle">Step 1 of 2: Pick an available date</p>
                        </div>
                        <button class="btn-close-modal" id="close-scheduler" type="button">
                            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                        </button>
                    </div>
                    
                    <div style="max-height:65vh; overflow-y:auto; padding-bottom: 20px;">
                        <!-- Step 1: Calendar -->
                        <div class="modal-step active" id="step-calendar">
                            <div class="booking-container" style="border:none; border-radius:0; margin-top:0;">
                                <div class="calendar-header">
                                    <div class="calendar-title" id="current-month">May 2026</div>
                                    <div class="calendar-nav">
                                        <button class="btn-nav" id="prev-month" type="button"><svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></button>
                                        <button class="btn-nav" id="next-month" type="button"><svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></button>
                                    </div>
                                </div>
                                <div class="calendar-grid" id="calendar-days">
                                    <div class="weekday">Sun</div><div class="weekday">Mon</div><div class="weekday">Tue</div>
                                    <div class="weekday">Wed</div><div class="weekday">Thu</div><div class="weekday">Fri</div>
                                    <div class="weekday">Sat</div>
                                </div>
                                <div class="legend">
                                    <div class="legend-item"><div class="legend-dot" style="background:var(--primary);"></div> Available</div>
                                    <div class="legend-item"><div class="legend-dot" style="background:var(--accent);"></div> Limited</div>
                                    <div class="legend-item"><div class="legend-dot" style="background:#d1d5db;"></div> Not Available</div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Time Slots -->
                        <div class="modal-step" id="step-slots">
                            <div class="back-to-calendar" id="btn-back-calendar">
                                <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                                Change Date
                            </div>
                            <div class="time-slots-container active" id="time-slots" style="display:block; border-top:none;">
                                <div id="morning-slots">
                                    <span class="slot-group-title" style="font-size:0.65rem; color:var(--primary); border-bottom:1px solid var(--primary-light); padding-bottom:4px; margin-bottom:12px; margin-top: 10px;">Morning Sessions</span>
                                    <div class="slots-grid" id="morning-grid"></div>
                                </div>
                                <div id="afternoon-slots">
                                    <span class="slot-group-title" style="font-size:0.65rem; color:var(--accent); border-bottom:1px solid #fdf6e3; padding-bottom:4px; margin-bottom:12px;">Afternoon Sessions</span>
                                    <div class="slots-grid" id="afternoon-grid"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-cancel" style="padding:10px 20px;" id="cancel-scheduler" type="button">Cancel</button>
                        <button class="btn-confirm" id="confirm-schedule" disabled type="button">Confirm Selection</button>
                    </div>
                </div>
            </div>

            <input type="hidden" id="booking-date" name="booking_date" />
            <input type="hidden" id="booking-slot" name="booking_slot" />
          </div>

          <!-- Final Confirmation Modal -->
          <div class="confirm-modal-overlay" id="final-confirm-modal">
            <div class="confirm-modal-card">
              <div class="confirm-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              </div>
              <h5 class="confirm-title">Confirm Final Schedule?</h5>
              <p class="confirm-msg">Are you sure you want to book this schedule? Please note that your selection <strong>cannot be modified or re-selected</strong> once confirmed.</p>
              <div class="confirm-buttons">
                <button class="btn-confirm-final" id="btn-commit-confirm" type="button">Confirm & Finalize</button>
                <button class="btn-cancel-final" id="btn-abort-confirm" type="button">Go Back to Selection</button>
              </div>
            </div>
          </div>
          <div style="margin-bottom:24px;">
            <label class="form-label">Counseling</label>
            <div style="display:flex;gap:20px;margin-top:6px;">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="session" id="session1" checked />
                <label class="form-check-label" for="session1">In-Person</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="session" id="session2" />
                <label class="form-check-label" for="session2">By Phone</label>
              </div>
            </div>
          </div>

          <!-- Declaration -->
          <div class="form-section-title">Declaration</div>
          <div class="form-check" style="margin-bottom:16px;">
            <input class="form-check-input" type="checkbox" id="decl1" />
            <label class="form-check-label" for="decl1">
              I hereby declare that the information provided is true and correct. I understand that all counseling sessions are confidential and conducted in accordance with Islamic principles.
            </label>
          </div>

          <div class="form-submit-row">
            <a href="<?= url('/user/dashboard') ?>" class="btn-cancel">Cancel</a>
            <button class="btn-submit" type="button" id="submit-btn">Submit Counseling Request</button>
          </div>

        </div>
      </div>
      <!-- RECENT APPLICATIONS TABLE -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Application History
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Reason for Request</th>
                  <th>Submitted Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="history-tbody">
                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No applications found.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // ── Inlined data helpers ──
  const STORAGE_KEYS = { user: 'mis_user', requests: 'mis_requests', initialized: 'mis_data_init' };
  const PROFILE_FIELDS = ['name', 'email', 'sex', 'phone', 'address', 'dob', 'civil', 'occupation', 'arabicName', 'revertYear'];
  const DEFAULT_USER = { 
    id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>', 
    name: '<?= addslashes($_SESSION['name'] ?? "User") ?>', 
    role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
    email:'<?= $_SESSION['email'] ?? "" ?>', 
    sex:'<?= $_SESSION['sex'] ?? $_SESSION['gender'] ?? "" ?>', 
    phone:'', address:'', dob:'', civil:'', occupation:'', arabicName:'', revertYear:'', apartment:'', profileComplete:false 
  };

  function initData() {
    if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
      localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
      localStorage.setItem(STORAGE_KEYS.initialized, '1');
    }
  }
  function getUser() {
    const raw = localStorage.getItem(STORAGE_KEYS.user);
    const stored = raw ? JSON.parse(raw) : { ...DEFAULT_USER };
    stored.id = DEFAULT_USER.id;
    stored.name = DEFAULT_USER.name;
    return stored;
  }
  function getProfileCompletion() {
    const user = getUser();
    const missing = [];
    let filled = 0;
    const labels = { name:'Full Name', email:'Email Address', sex:'Sex', phone:'Contact Number', address:'Complete Address', dob:'Date of Birth', civil:'Civil Status', occupation:'Occupation', arabicName:'Muslim / Arabic Name', revertYear:'Year Reverted' };
    PROFILE_FIELDS.forEach(k => {
      if (user[k] && String(user[k]).trim() !== '') { filled++; } else { missing.push(labels[k] || k); }
    });
    return { percentage: Math.round((filled / PROFILE_FIELDS.length) * 100), filled, total: PROFILE_FIELDS.length, missingFields: missing };
  }
  function addRequest(req) {
    const raw = localStorage.getItem(STORAGE_KEYS.requests);
    const requests = raw ? JSON.parse(raw) : [];
    if (!req.id) req.id = 'MC-' + String(requests.length + 1).padStart(3, '0');
    if (!req.date) req.date = new Date().toISOString().split('T')[0];
    if (!req.updatedAt) req.updatedAt = req.date;
    if (!req.status) req.status = 'pending';
    requests.push(req);
    localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(requests));
    return req;
  }

  initData();

  const user = getUser();

  // ── Profile access gate ──
  const { percentage, missingFields } = getProfileCompletion();
  const isComplete = percentage === 100;

  // Sync completion status to localStorage so the sidebar knows to unlock
  const storedUser = JSON.parse(localStorage.getItem(STORAGE_KEYS.user) || '{}');
  if (storedUser.profileComplete !== isComplete) {
    storedUser.profileComplete = isComplete;
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(storedUser));
  }

  if (percentage < 100) {
    if (!document.getElementById('acm-keyframes')) {
      const styleEl = document.createElement('style');
      styleEl.id = 'acm-keyframes';
      styleEl.textContent = `
        @keyframes acmFadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes acmSlideUp { from { opacity:0;transform:translateY(24px) scale(0.96); } to { opacity:1;transform:translateY(0) scale(1); } }
      `;
      document.head.appendChild(styleEl);
    }

    let title = 'Profile Incomplete';
    let message = 'Access to this section is restricted until your profile is fully completed.';
    let primaryLabel = 'Go to Profile';
    let primaryUrl = '../../user_profile.html';

    if (percentage >= 100 && String(user.sex).toLowerCase() !== 'male') {
      title = 'Access Restricted';
      message = 'This counseling service is exclusively available for brothers. You do not have access to this section.';
      primaryLabel = 'Back to Dashboard';
      primaryUrl = '../../user-dashboard.html';
    }

    const missingHtml = missingFields.length > 0
      ? `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">The following information is still required:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => '<li>' + f + '</li>').join('')}
           </ul>
         </div>` : '';

    const modalHtml = `
      <div id="access-control-modal" style="
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);
        padding:24px;animation:acmFadeIn 0.3s ease;
      ">
        <div style="
          background:white;border-radius:16px;
          width:100%;max-width:440px;
          box-shadow:0 20px 60px rgba(0,0,0,0.25);
          overflow:hidden;animation:acmSlideUp 0.35s ease;
        ">
          <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <div style="margin-bottom:8px;">
              <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/>
              </svg>
            </div>
            <div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
              <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="${percentage >= 40 ? '#c79a2b' : '#8b2e2e'}" stroke-width="3"
                  stroke-dasharray="${percentage} ${100 - percentage}" stroke-linecap="round"/>
              </svg>
              <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#0f5c3a;">${percentage}%</span>
            </div>
            <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#0f5c3a;margin:0 0 10px;">${title}</h4>
            <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">${message}</p>
            ${missingHtml}
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="acm-cancel-btn" style="padding:10px 22px;border-radius:8px;border:1.5px solid #d9e3de;background:white;color:#6f7f78;font-size:0.85rem;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="acm-primary-btn" style="padding:10px 22px;border-radius:8px;border:none;background:linear-gradient(135deg,#0f5c3a,#2f8a60);color:white;font-size:0.85rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(15,92,58,0.3);">${primaryLabel}</button>
          </div>
        </div>
      </div>

    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('access-control-modal');
    document.getElementById('acm-primary-btn').addEventListener('click', () => { window.location.href = primaryUrl === '../../user-dashboard.html' ? '<?= url('/user/dashboard') ?>' : '<?= url('/user/profile') ?>'; });
    document.getElementById('acm-cancel-btn').addEventListener('click', () => {
      modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards';
      setTimeout(() => modal.remove(), 200);
    });
    modal.addEventListener('click', e => {
      if (e.target === modal) { modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards'; setTimeout(() => modal.remove(), 200); }
    });
  }


  // ── Submit button ──
  document.getElementById('submit-btn').addEventListener('click', (e) => {
    e.preventDefault();
    const d1 = document.getElementById('decl1').checked;
    if (!d1) {
      showToast('Please check the declaration box before submitting.', '#8b2e2e');
      return;
    }
    
    const reason = document.getElementById('reason-select').value;
    if (!reason || reason === '') {
      showToast('Please select a reason for counseling.', '#8b2e2e');
      return;
    }

    const formData = new FormData();
    formData.append('gender', 'male');
    formData.append('reason', reason);
    formData.append('preferred_date', document.getElementById('booking-date').value);
    formData.append('preferred_time', document.getElementById('booking-slot').value);

    const btn = document.getElementById('submit-btn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    fetch('<?= url("/user/services/counseling/submit") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Success! Your counseling request has been submitted.', 'var(--primary)');
            
            // Still update localStorage for instant history update (optional, but good for UX)
            addRequest({ 
                type: 'counseling_male', 
                reason: reason, 
                user: user.id,
                status: 'pending',
                date: new Date().toISOString().split('T')[0]
            });

            updateAnalytics();

            // Reset form
            document.getElementById('decl1').checked = false;
            document.getElementById('reason-select').value = '';
            document.getElementById('display-schedule').textContent = 'Select preferred date and time';
            document.getElementById('open-scheduler').classList.remove('locked');
        } else {
            showToast(data.message || 'Submission failed.', '#8b2e2e');
        }
    })
    .catch(err => {
        console.error('Error submitting:', err);
        showToast('A network error occurred. Please try again.', '#8b2e2e');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
  });

  // ── Booking Calendar Logic ──
  let currentDate = new Date();
  let selectedDate = null;
  let selectedSlot = null;
  let tempDate = null;
  let tempSlot = null;

  const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  
  // Modal Controls
  const modal = document.getElementById('scheduler-modal');
  const openBtn = document.getElementById('open-scheduler');
  const closeBtn = document.getElementById('close-scheduler');
  const cancelBtn = document.getElementById('cancel-scheduler');
  const confirmBtn = document.getElementById('confirm-schedule');
  const finalModal = document.getElementById('final-confirm-modal');
  const abortBtn = document.getElementById('btn-abort-confirm');
  const commitBtn = document.getElementById('btn-commit-confirm');

  openBtn.onclick = () => modal.classList.add('active');
  const closeModal = () => {
    modal.classList.remove('active');
    finalModal.classList.remove('active');
    setTimeout(() => {
        document.getElementById('step-calendar').classList.add('active');
        document.getElementById('step-slots').classList.remove('active');
        document.getElementById('modal-step-title').textContent = 'Select Appointment Date';
        document.getElementById('modal-step-subtitle').textContent = 'Step 1 of 2: Pick an available date';
        tempDate = null;
        tempSlot = null;
        confirmBtn.disabled = true;
        renderCalendar();
    }, 300);
  };
  closeBtn.onclick = closeModal;
  cancelBtn.onclick = closeModal;

  document.getElementById('btn-back-calendar').onclick = () => {
    document.getElementById('step-calendar').classList.add('active');
    document.getElementById('step-slots').classList.remove('active');
    document.getElementById('modal-step-title').textContent = 'Select Appointment Date';
    document.getElementById('modal-step-subtitle').textContent = 'Step 1 of 2: Pick an available date';
    confirmBtn.disabled = true;
  };

  confirmBtn.onclick = () => {
    finalModal.classList.add('active');
  };

  abortBtn.onclick = () => {
    finalModal.classList.remove('active');
  };

  commitBtn.onclick = () => {
    selectedDate = tempDate;
    selectedSlot = tempSlot;
    document.getElementById('booking-date').value = selectedDate;
    document.getElementById('booking-slot').value = selectedSlot;
    
    const formattedDate = new Date(selectedDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    document.getElementById('display-schedule').textContent = `${formattedDate} at ${selectedSlot}`;
    
    // Lock the trigger as per policy
    openBtn.classList.add('locked');
    
    closeModal();
  };

  // Mock Availability Data
  const mockAvailability = {
    '2026-05-10': { status: 'full', slots: {} },
    '2026-05-15': { status: 'limited', slots: { '08:00 AM': 1, '10:00 AM': 0, '02:00 PM': 3 } },
    '2026-05-18': { status: 'full', slots: {} },
    '2026-05-20': { status: 'full', slots: {} },
    '2026-05-25': { status: 'full', slots: {} }
  };

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    const container = document.getElementById('calendar-days');
    const weekdays = container.querySelectorAll('.weekday');
    container.innerHTML = '';
    weekdays.forEach(w => container.appendChild(w));

    for (let i = 0; i < firstDay; i++) {
      container.appendChild(document.createElement('div'));
    }

    const today = new Date();
    
    for (let d = 1; d <= daysInMonth; d++) {
      const dayEl = document.createElement('div');
      dayEl.className = 'calendar-day';
      dayEl.textContent = d;
      
      const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
      const dateObj = new Date(year, month, d);

      if (dateObj < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
        dayEl.classList.add('disabled');
      } else {
        const avail = mockAvailability[dateStr];
        if (avail) {
          dayEl.classList.add(avail.status);
          if (avail.status === 'full') dayEl.classList.add('booked');
        }
        dayEl.onclick = () => selectDate(dateStr, dayEl);
      }

      if (dateObj.toDateString() === today.toDateString()) dayEl.classList.add('today');
      if (tempDate === dateStr) dayEl.classList.add('selected');
      
      container.appendChild(dayEl);
    }
  }

  function selectDate(date, el) {
    if (el.classList.contains('disabled') || el.classList.contains('booked')) return;
    
    tempDate = date;
    tempSlot = null;
    confirmBtn.disabled = true;

    // Switch Steps with a slight delay so user sees selection
    setTimeout(() => {
        document.getElementById('step-calendar').classList.remove('active');
        document.getElementById('step-slots').classList.add('active');
        
        const formattedDate = new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        document.getElementById('modal-step-title').textContent = 'Select Time Slot';
        document.getElementById('modal-step-subtitle').textContent = `Step 2 of 2: Choosing for ${formattedDate}`;

        renderTimeSlots(date);
    }, 400);
  }

  function renderTimeSlots(date) {
    const morningGrid = document.getElementById('morning-grid');
    const afternoonGrid = document.getElementById('afternoon-grid');
    morningGrid.innerHTML = '';
    afternoonGrid.innerHTML = '';

    const slots = [
      { time: '08:00 AM', period: 'morning', capacity: 3 },
      { time: '09:00 AM', period: 'morning', capacity: 3 },
      { time: '10:00 AM', period: 'morning', capacity: 2 },
      { time: '11:00 AM', period: 'morning', capacity: 1 },
      { time: '01:00 PM', period: 'afternoon', capacity: 4 },
      { time: '02:00 PM', period: 'afternoon', capacity: 0 },
      { time: '03:00 PM', period: 'afternoon', capacity: 2 },
      { time: '04:00 PM', period: 'afternoon', capacity: 3 }
    ];

    const dayData = mockAvailability[date];

    slots.forEach(s => {
      const pill = document.createElement('div');
      pill.className = 'slot-pill';
      
      let capacity = s.capacity;
      if (dayData && dayData.slots && dayData.slots[s.time] !== undefined) {
        capacity = dayData.slots[s.time];
      }

      pill.innerHTML = `<span>${s.time}</span><span class="slot-capacity">${capacity > 0 ? `${capacity} slots left` : 'Fully Booked'}</span>`;

      if (capacity === 0) pill.classList.add('disabled');
      else {
        pill.onclick = () => {
          document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
          pill.classList.add('selected');
          tempSlot = s.time;
          confirmBtn.disabled = false;
        };
      }

      if (tempSlot === s.time) pill.classList.add('selected');

      if (s.period === 'morning') morningGrid.appendChild(pill);
      else afternoonGrid.appendChild(pill);
    });
  }

  document.getElementById('prev-month').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
  };
  document.getElementById('next-month').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
  };

  renderCalendar();

  // ── Other Reason Toggle ──
  const reasonSelect = document.getElementById('reason-select');
  const otherContainer = document.getElementById('other-reason-container');
  
  reasonSelect.onchange = () => {
    if (reasonSelect.value === 'Other') {
        otherContainer.style.display = 'block';
    } else {
        otherContainer.style.display = 'none';
    }
  };

  function updateAnalytics() {
    const raw = localStorage.getItem(STORAGE_KEYS.requests);
    const requests = raw ? JSON.parse(raw) : [];
    
    document.getElementById('ana-total').textContent = requests.length;
    document.getElementById('ana-pending').textContent = requests.filter(r => r.status === 'pending').length;
    document.getElementById('ana-approved').textContent = requests.filter(r => r.status === 'approved').length;

    const tbody = document.getElementById('history-tbody');
    if (requests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No applications found.</td></tr>';
        return;
    }

    tbody.innerHTML = [...requests].reverse().map(r => `
        <tr>
            <td class="td-id">#${r.id}</td>
            <td style="font-weight:600;">${r.reason}</td>
            <td>${r.date}</td>
            <td><span class="badge-status ${r.status === 'pending' ? 'pending' : (r.status === 'approved' ? 'success' : 'danger')}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td>
            <td>
                <button class="btn-view-doc" onclick="showToast('Viewing details for ${r.id}...', 'var(--primary)')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    View Record
                </button>
            </td>
        </tr>
    `).join('');
  }

  // Initial load
  updateAnalytics();

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + bg + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:Source Sans 3,sans-serif;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;animation:fadeIn 0.3s ease;';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
  }
</script>
</body>
</html>
