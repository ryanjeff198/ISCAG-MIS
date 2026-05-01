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
          <div class="stat-value" id="ana-total"><?= $analytics['total'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Approval</div>
          <div class="stat-value warning" id="ana-pending"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Successful Sessions</div>
          <div class="stat-value success" id="ana-approved"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
      </div>

      <?php 
        $hasApproved = false;
        $hasPending = false;
        $activeRequest = null;
        foreach ($history ?? [] as $req) {
            if ($req['status'] === 'approved') { $hasApproved = true; $activeRequest = $req; }
            if ($req['status'] === 'pending') { $hasPending = true; $activeRequest = $req; }
        }
      ?>

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

      <!-- MAIN FORM CARD (Hidden if request exists) -->
      <?php if (!$hasPending && !$hasApproved): ?>
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
              <input type="text" class="form-control" placeholder="Enter your full name" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" />
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
              <option>Other</option>
            </select>
          </div>

          <div style="margin-bottom:24px;">
            <label class="form-label">Detailed Description of Concern <span class="required">*</span></label>
            <textarea class="form-control" rows="4" placeholder="Briefly describe your situation so we can assign the best counselor for you..."></textarea>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:8px;">All details shared here are encrypted and only accessible by authorized department counselors.</p>
          </div>

          <!-- Availability & Preferences -->
          <div class="form-section-title">Availability & Preferences</div>
          <div class="form-grid cols-2" style="margin-bottom:32px;">
            <div>
              <label class="form-label">Preferred Session Type</label>
              <div style="display:flex;gap:12px;margin-top:8px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer;">
                  <input type="radio" name="session_type" value="In-Person" checked /> In-Person
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer;">
                  <input type="radio" name="session_type" value="Online" /> Online / Phone
                </label>
              </div>
            </div>
            <div>
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

              <!-- MODAL STRUCTURE -->
              <div class="modal-overlay" id="scheduling-modal">
                <div class="modal-card">
                  <div class="modal-header">
                    <div>
                      <h4>Select Schedule</h4>
                      <p>Pick a date and time for your session</p>
                    </div>
                    <button class="btn-close-modal" id="close-modal"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg></button>
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
                        <div class="legend-item"><div class="legend-dot" style="background:var(--primary);"></div><span>Selected</span></div>
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
                        <div class="slot-pill" onclick="selectTime('09:00 AM')">09:00 AM</div>
                        <div class="slot-pill" onclick="selectTime('10:00 AM')">10:00 AM</div>
                        <div class="slot-pill" onclick="selectTime('11:00 AM')">11:00 AM</div>
                        <div class="slot-pill" onclick="selectTime('01:00 PM')">01:00 PM</div>
                        <div class="slot-pill" onclick="selectTime('02:00 PM')">02:00 PM</div>
                        <div class="slot-pill" onclick="selectTime('03:00 PM')">03:00 PM</div>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button class="btn-confirm" id="confirm-sched" disabled>Confirm Schedule</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div style="margin-bottom:32px;padding:20px;background:#f0fdf4;border:1px solid #dcfce7;border-radius:12px;">
            <div class="form-check" style="display:flex;align-items:flex-start;gap:12px;">
              <input type="checkbox" id="privacy-check" style="margin-top:4px;" />
              <label for="privacy-check" style="font-size:0.85rem;color:#166534;line-height:1.5;cursor:pointer;">
                I understand that this information will be used to process my request and I agree to the <a href="#" style="color:#059669;font-weight:700;text-decoration:underline;">Confidentiality Policy</a> of the Da'wah Department.
              </label>
            </div>
          </div>

          <div class="form-submit-row">
            <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Cancel</button>
            <button type="button" class="btn-submit" id="submit-form">Submit Request</button>
          </div>
        </div>
      </div>
      <?php else: ?>
      <!-- 📢 PREMIUM STATUS HERO (Exact Sync with tenant_status.php) -->
      <div class="status-hero" style="background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div class="status-hero-top" style="background: linear-gradient(135deg, #14532D, #166534); padding: 28px 32px 24px; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; bottom: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.1);"></div>
            <div style="position: absolute; right: 100px; bottom: -30px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.05);"></div>
            
            <div class="status-hero-header" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; position: relative; z-index: 1;">
                <div class="status-hero-header-left" style="display: flex; align-items: center; gap: 16px;">
                    <div class="status-hero-avatar" style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div>
                        <h5 class="status-hero-name" style="font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: white; margin: 0 0 2px;">Counseling Request Status</h5>
                        <p class="status-hero-subtitle" style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.7); margin: 0;">Ref No: <strong>#<?= $activeRequest['id'] ?? 'CR-AUTO' ?></strong> • Submitted on <?= isset($activeRequest['created_at']) ? date('M d, Y', strtotime($activeRequest['created_at'])) : 'Recently' ?></p>
                    </div>
                </div>
                <div class="status-badge <?= $hasPending ? 'pending' : 'approved' ?>" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; white-space: nowrap; backdrop-filter: blur(8px); background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <div class="status-badge-dot" style="width: 7px; height: 7px; border-radius: 50%; background: currentColor;"></div>
                    <?= $hasPending ? 'Under Review' : 'Scheduled' ?>
                </div>
            </div>
        </div>
        <!-- Status Summary Bar -->
        <div class="status-summary" style="padding: 18px 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #f9fafb; border-top: 1px solid var(--border);">
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Request ID</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;">#<?= $activeRequest['id'] ?? 'CR-AUTO' ?></div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Department</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;">Counseling</div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Current Stage</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;"><?= $hasPending ? 'Review' : 'Approved' ?></div>
            </div>
        </div>
      </div>

      <!-- 🗓️ TIMELINE CARD (Exact Sync) -->
      <div class="timeline-card" style="background: white; border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, rgba(20, 83, 45, 0.05), transparent);">
            <div class="card-header-left" style="display: flex; align-items: center; gap: 10px;">
                <div class="card-header-icon" style="width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #14532D, #166534); display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" style="width: 17px; height: 17px; fill: white;"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                </div>
                <h6 class="card-header-title" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #1a1a1a; margin: 0;">Service Lifecycle</h6>
            </div>
        </div>
        <div class="card-body" style="padding: 32px 24px;">
            <div class="timeline" style="display: flex; align-items: flex-start; gap: 0; position: relative;">
                <!-- 🛤️ Progress Line (Missing fixed) -->
                <div style="position: absolute; top: 18px; left: 0; right: 0; height: 3px; background: #e5e7eb; z-index: 1;"></div>
                <div style="position: absolute; top: 18px; left: 0; width: <?= $hasApproved ? '100%' : '50%' ?>; height: 3px; background: #14532D; z-index: 2; transition: width 1s ease;"></div>
                
                <div class="timeline-step completed" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #14532D; background: linear-gradient(135deg, #14532D, #166534); color: white; position: relative; z-index: 3;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #14532D;">Requested</span>
                </div>
                
                <div class="timeline-step <?= $hasPending ? 'active' : 'completed' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid <?= $hasPending ? '#f59e0b' : '#14532D' ?>; background: <?= $hasPending ? '#fff' : 'linear-gradient(135deg, #14532D, #166534)' ?>; color: <?= $hasPending ? '#f59e0b' : 'white' ?>; position: relative; z-index: 3;">
                        <?php if ($hasPending): ?>
                            <span style="font-size: 1rem; font-weight: 800;">2</span>
                        <?php else: ?>
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        <?php endif; ?>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: <?= $hasPending ? '#f59e0b' : '#14532D' ?>;">Review</span>
                </div>

                <div class="timeline-step <?= $hasApproved ? 'completed' : '' ?>" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                    <div class="timeline-dot" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid <?= $hasApproved ? '#14532D' : '#e5e7eb' ?>; background: <?= $hasApproved ? 'linear-gradient(135deg, #14532D, #166534)' : '#fff' ?>; color: <?= $hasApproved ? 'white' : '#9ca3af' ?>; position: relative; z-index: 3;">
                        <?php if ($hasApproved): ?>
                            <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        <?php else: ?>
                            <span style="font-size: 0.9rem; font-weight: 700;">3</span>
                        <?php endif; ?>
                    </div>
                    <span class="timeline-label" style="margin-top: 10px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: <?= $hasApproved ? '#14532D' : '#9ca3af' ?>;">Booked</span>
                </div>
            </div>

            <div style="text-align: center; max-width: 520px; margin: 40px auto 0;">
                <?php if ($hasPending): ?>
                    <p style="font-size: 0.95rem; color: #4b5563; line-height: 1.7; margin: 0;">Our counselors are currently reviewing your request details. Please keep your communication lines open for a possible interview schedule.</p>
                <?php else: ?>
                    <p style="font-size: 0.95rem; color: #4b5563; line-height: 1.7; margin-bottom: 28px;">As-salamu alaykum. Your counseling session has been approved. You may now access the dedicated guidance portal to prepare.</p>
                    <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-submit" style="background: linear-gradient(135deg, #D4AF37, #B8860B); color: #1a1a1a; font-weight: 800; padding: 14px 40px; text-decoration: none; border-radius: 12px; box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3); border: none;">Open Guidance Center</a>
                <?php endif; ?>
            </div>
        </div>
      </div>
      <?php endif; ?>
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

    const modalHtml = `
      <div id="access-control-modal" style="position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);padding:24px;animation:acmFadeIn 0.3s ease;">
        <div style="background:white;border-radius:16px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;animation:acmSlideUp 0.35s ease;">
          <div style="height:4px;background:linear-gradient(90deg,#14532D,#B8860B);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
              <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#14532D" stroke-width="3" stroke-dasharray="${percentage} ${100-percentage}" stroke-linecap="round"/>
              </svg>
              <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#14532D;">${percentage}%</span>
            </div>
            <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#14532D;margin:0 0 10px;">Please Complete Your Profile</h4>
            <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">Please complete your profile information first to access the Counseling & Guidance portal.</p>
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button onclick="window.location.href='<?= url('/user/dashboard') ?>'" style="padding:10px 22px;border-radius:8px;border:1.5px solid #d9e3de;background:white;color:#6f7f78;font-size:0.85rem;font-weight:600;cursor:pointer;">Dashboard</button>
            <button onclick="window.location.href='<?= url('/user/profile') ?>'" style="padding:10px 22px;border-radius:8px;border:none;background:linear-gradient(135deg,#14532D,#166534);color:white;font-size:0.85rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(20,83,45,0.3);">Go to Profile</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
  }

  // ── Render History ──
  const historyTbody = document.getElementById('history-tbody');
  const historyData = <?= json_encode($history ?? []) ?>;

  if (historyData.length > 0) {
    historyTbody.innerHTML = historyData.map(h => `
      <tr>
        <td class="td-id">#${h.id}</td>
        <td>${h.reason}</td>
        <td>${new Date(h.created_at).toLocaleDateString()}</td>
        <td><span class="badge-status badge-${h.status}">${h.status}</span></td>
        <td>
          <button class="btn-view-doc" title="View Details">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            Details
          </button>
        </td>
      </tr>
    `).join('');
  }

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
    calStep.classList.add('active');
    timeStep.classList.remove('active');
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
    
    const todayStr = new Date().toISOString().split('T')[0];
    
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const blockReason = BLOCKED_DATES[dateStr];
      const isBlocked = !!blockReason;
      const isPast = dateStr < todayStr;
      const isToday = dateStr === todayStr;
      
      calGrid.innerHTML += `
        <div class="calendar-day ${isBlocked ? 'booked' : ''} ${isPast ? 'disabled' : ''} ${isToday ? 'today' : ''}" 
             title="${isBlocked ? 'Unavailable: ' + blockReason : ''}"
             onclick="${isBlocked ? `showBlockReason('${blockReason}')` : ((!isPast) ? \`selectDate('${dateStr}')\` : '')}">
          ${day}
        </div>
      `;
    }
  }

  window.showBlockReason = (reason) => {
    alert(`This date is unavailable: ${reason}`);
  };

  window.selectDate = (date) => {
    selectedDate = date;
    calStep.classList.remove('active');
    timeStep.classList.add('active');
    document.getElementById('display-selected-date').innerText = new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  };

  window.selectTime = (time) => {
    selectedTime = time;
    document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    confirmBtn.disabled = false;
  };

  window.backToCal = () => {
    timeStep.classList.remove('active');
    calStep.classList.add('active');
  };

  if(confirmBtn) confirmBtn.onclick = () => {
    document.getElementById('selected-schedule-text').innerText = \`\${new Date(selectedDate).toLocaleDateString()} at \${selectedTime}\`;
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
