<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Counseling Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .badge-status {
      padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.02em; display: inline-flex; align-items: center; gap: 5px;
    }
    .badge-status.badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-status.badge-under_review { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .badge-status.badge-awaiting { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-status.badge-confirmed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-status.badge-completed { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .badge-status.badge-noshow { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-status.badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-status.badge-cancelled { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }

    /* Category & Filters Row */
    .filter-row { display: flex; gap: 16px; align-items: center; margin-bottom: 24px; flex-wrap: wrap; }
    .category-select-wrapper { display: flex; align-items: center; gap: 10px; background: white; padding: 8px 16px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .category-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
    .category-dropdown { border: none; font-size: 0.9rem; font-weight: 700; color: var(--primary); cursor: pointer; background: transparent; padding-right: 20px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right center; background-size: 14px; }
    .category-dropdown:focus { outline: none; }
    
    /* Insights Cards */
    .admin-insights { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .insight-card { background: white; padding: 16px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s; display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; cursor: pointer; }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--accent); }
    .insight-card::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--border); }
    .insight-card.all::after { background: var(--accent); }
    .insight-card.pending::after { background: #f59e0b; }
    .insight-card.under_review::after { background: #6366f1; }
    .insight-card.confirmed::after { background: #10b981; }
    .insight-card.completed::after { background: #64748b; }
    
    .insight-label { font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1; }

    /* Modal Backdrop and Card */
    .counseling-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center; padding: 20px;
      animation: fadeIn 0.25s ease;
    }
    .counseling-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 660px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); animation: slideUp 0.3s ease;
    }
    .counseling-modal-header {
      padding: 18px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; background: #fdfdfd;
    }
    .counseling-modal-close {
      background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280; padding: 0; line-height: 1;
    }
    .counseling-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .counseling-modal-footer {
      padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    @media (max-width: 600px) { .info-grid-2 { grid-template-columns: 1fr; } }
    .info-box { background: #f9fafb; padding: 12px 14px; border-radius: 12px; border: 1px solid var(--border); }
    .info-box.highlight { background: #f0fdf4; border-color: #dcfce7; }
    .info-box-lbl { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; }
    .info-box-val { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
    .info-box-val.gold { color: #14532d; font-family: 'Lora', serif; }

    /* Action Form Panels inside modal */
    .counselor-action-panel {
      display: none; background: #f8fafc; border: 1.5px solid #cbd5e1;
      border-radius: 14px; padding: 18px; margin-top: 16px; animation: fadeIn 0.25s ease;
    }
    .action-panel-title {
      font-weight: 800; font-size: 0.88rem; color: #0f172a; text-transform: uppercase;
      letter-spacing: 0.04em; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
    }
    .c-field { margin-bottom: 12px; }
    .c-field label { display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase; }
    .c-field input, .c-field select, .c-field textarea {
      width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1px solid #cbd5e1;
      border-radius: 8px; font-size: 0.9rem; font-family: inherit; background: #fff;
    }
    .c-field input:focus, .c-field select:focus, .c-field textarea:focus {
      outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,83,45,0.12);
    }
    .conflict-warning-box {
      display: none; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 10px;
      padding: 10px 14px; font-size: 0.84rem; color: #991b1b; margin-bottom: 14px; line-height: 1.4;
    }
    .panel-btn-row { display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'counseling';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Counseling &amp; Guidance Management</div>
            <div class="top-bar-subtitle">Male Da'wah Department — Review, Counselor Assignment &amp; Appointment Scheduling</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
          <button class="btn-topbar primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Counseling Requests</span>
        </div>

        <!-- Dynamic Insights Summary -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered by JS -->
        </div>

        <!-- Category & Filters Row -->
        <div class="filter-row">
          <div class="category-select-wrapper">
            <span class="category-label">Concern Category:</span>
            <select class="category-dropdown" id="category-dropdown" onchange="switchCategory(this.value)">
              <!-- Rendered by JS -->
            </select>
          </div>

          <div style="margin-left:auto; display:flex; gap:10px; align-items:center;">
            <span class="category-label">Status Filter:</span>
            <select id="status-filter" class="form-control" style="font-size:0.85rem; padding:6px 14px; border-radius:10px; width:auto; appearance:auto;" onchange="renderActiveCategory()">
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="under_review">Under Review</option>
              <option value="approved_awaiting_schedule">Approved, Awaiting Schedule</option>
              <option value="confirmed">Confirmed</option>
              <option value="completed">Completed</option>
              <option value="no_show">No-Show</option>
              <option value="rejected">Rejected</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <!-- Table Section -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 id="current-category-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Brothers' Counseling Records
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Reference Number</th>
                    <th>Client Name</th>
                    <th>Date Submitted</th>
                    <th>Reason for Counseling</th>
                    <th>Preferred Session Type</th>
                    <th>Preferred Schedule</th>
                    <th>Request Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="counseling-tbody">
                  <!-- JS Rendered -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 📋 Detail, Review, Counselor Assignment & Schedule Modal -->
  <div id="counseling-modal" class="counseling-modal-backdrop" onclick="if(event.target===this) closeCounselingModal()">
    <div class="counseling-modal-card">
      <div class="counseling-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
          Counseling Request Details &amp; Actions
        </h5>
        <button type="button" onclick="closeCounselingModal()" class="counseling-modal-close">&times;</button>
      </div>

      <div class="counseling-modal-body">
        <!-- Client Profile -->
        <div class="info-box highlight" style="margin-bottom:14px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
              <div class="info-box-lbl">Client Information</div>
              <div id="modal-c-name" class="info-box-val" style="font-size:1.1rem;">—</div>
              <div id="modal-c-contact" style="font-size:0.84rem; color:#4b5563; margin-top:2px;">—</div>
              <div id="modal-c-address" style="font-size:0.8rem; color:#64748b; margin-top:2px;">—</div>
            </div>
            <div id="modal-c-badge-wrap">
              <span id="modal-c-status-badge" class="badge-status badge-pending">Pending</span>
            </div>
          </div>
        </div>

        <!-- Request Details -->
        <div class="info-box" style="margin-bottom:14px;">
          <div class="info-box-lbl">Reason for Counseling</div>
          <div id="modal-c-reason" class="info-box-val gold">—</div>
          <div id="modal-c-desc" style="font-size:0.86rem; color:#374151; margin-top:6px; line-height:1.45; background:#fff; padding:10px 14px; border-radius:8px; border:1px solid #e5e7eb; display:none;"></div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Preferred Schedule</div>
            <div id="modal-c-pref-sched" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Preferred Session Type</div>
            <div id="modal-c-pref-type" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Reference Number</div>
            <div id="modal-c-ref" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Date Submitted</div>
            <div id="modal-c-submitted" class="info-box-val">—</div>
          </div>
        </div>

        <!-- Confirmed Appointment Section (If confirmed or completed) -->
        <div id="modal-c-confirmed-box" class="info-box" style="margin-bottom:14px; background:#f0fdf4; border-color:#bbf7d0; display:none;">
          <div class="info-box-lbl" style="color:#166534;">Finalized Appointment Details</div>
          <div style="margin-top:6px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Assigned Counselor:</span>
              <div id="modal-c-confirmed-counselor" style="font-weight:700; color:#0f172a;">—</div>
            </div>
            <div>
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Confirmed Date &amp; Time:</span>
              <div id="modal-c-confirmed-datetime" style="font-weight:700; color:#0f172a;">—</div>
            </div>
            <div style="grid-column: span 2;">
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Location / Meeting Details:</span>
              <div id="modal-c-confirmed-location" style="font-size:0.88rem; color:#1f2937;">—</div>
            </div>
          </div>
        </div>

        <!-- Rejection Reason if Rejected -->
        <div id="modal-c-rejection-box" class="info-box" style="margin-bottom:14px; background:#fef2f2; border-color:#fecaca; display:none;">
          <div class="info-box-lbl" style="color:#991b1b;">Rejection Reason Recorded</div>
          <div id="modal-c-rejection-text" style="font-size:0.88rem; color:#7f1d1d; margin-top:4px;">—</div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ⚙️ ACTION PANELS                                         -->
        <!-- ══════════════════════════════════════════════════════════ -->

        <!-- 1. Accept Request & Set Appointment Panel -->
        <div id="panel-accept-schedule" class="counselor-action-panel">
          <div class="action-panel-title" style="color:var(--primary);">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--primary)"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Accept Request: Assign Counselor &amp; Set Appointment
          </div>

          <!-- Conflict Warning Banner -->
          <div id="conflict-warning" class="conflict-warning-box">
            <strong>⚠️ Scheduling Conflict Detected:</strong>
            <span id="conflict-warning-msg">This counselor already has an appointment during this time slot.</span>
          </div>

          <div class="c-field">
            <label>Assign Counselor <span style="color:red;">*</span></label>
            <select id="sched-counselor-id" onchange="runLiveConflictCheck()">
              <option value="">— Select Available Counselor —</option>
              <?php foreach ($counselors ?? [] as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['designation']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Final Appointment Date <span style="color:red;">*</span></label>
              <input type="date" id="sched-date" min="<?= date('Y-m-d') ?>" onchange="runLiveConflictCheck()">
            </div>
            <div class="c-field">
              <label>Final Session Type <span style="color:red;">*</span></label>
              <select id="sched-type" onchange="toggleLocationFields()">
                <option value="In-Person">In-Person Consultation</option>
                <option value="Online">Online Video Consultation</option>
              </select>
            </div>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Start Time <span style="color:red;">*</span></label>
              <input type="time" id="sched-start-time" value="10:00" onchange="runLiveConflictCheck()">
            </div>
            <div class="c-field">
              <label>End Time <span style="color:red;">*</span></label>
              <input type="time" id="sched-end-time" value="11:00" onchange="runLiveConflictCheck()">
            </div>
          </div>

          <div class="c-field" id="wrap-location">
            <label>Location for In-Person Session</label>
            <input type="text" id="sched-location" value="ISCAG Center, 2nd Floor Da'wah Counseling Office">
          </div>

          <div class="c-field" id="wrap-link" style="display:none;">
            <label>Online Meeting Link (Google Meet / Zoom)</label>
            <input type="url" id="sched-link" placeholder="https://meet.google.com/xxx-xxxx-xxx">
          </div>

          <div class="c-field" id="wrap-instructions" style="display:none;">
            <label>Online Meeting Instructions</label>
            <textarea id="sched-instructions" rows="2" placeholder="e.g. Please join 5 minutes before the session with your registered name..."></textarea>
          </div>

          <div class="c-field">
            <label>Administrator / Counselor Notes (Optional)</label>
            <textarea id="sched-notes" rows="2" placeholder="Private coordination remarks or pre-session instructions for client..."></textarea>
          </div>

          <div class="panel-btn-row">
            <button type="button" class="btn-cancel" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-approve" id="btn-submit-schedule" onclick="submitFinalSchedule()">Confirm Appointment</button>
          </div>
        </div>

        <!-- 2. Reject Request Panel -->
        <div id="panel-reject" class="counselor-action-panel">
          <div class="action-panel-title" style="color:var(--danger);">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--danger)"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            Reject Counseling Request
          </div>
          <div class="c-field">
            <label>Required Rejection Reason <span style="color:red;">*</span></label>
            <textarea id="reject-reason" rows="3" placeholder="State the specific reason for rejecting this request (e.g. duplicate request, outside scope of service, unable to verify client identity)..." required></textarea>
          </div>
          <div class="panel-btn-row">
            <button type="button" class="btn-cancel" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="submitReject()">Confirm Rejection</button>
          </div>
        </div>

        <!-- 3. Mark as Completed Panel -->
        <div id="panel-complete" class="counselor-action-panel">
          <div class="action-panel-title" style="color:#166534;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="#166534"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            Mark Consultation as Completed
          </div>
          <div class="c-field">
            <label>Counselor Conclusion Notes</label>
            <textarea id="complete-notes" rows="3" placeholder="Provide confidential wrap-up notes or summary of counseling session..."></textarea>
          </div>
          <div class="panel-btn-row">
            <button type="button" class="btn-cancel" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-approve" onclick="submitComplete()">Confirm Completed</button>
          </div>
        </div>

      </div>

      <!-- Footer Buttons -->
      <div class="counseling-modal-footer">
        <button type="button" onclick="closeCounselingModal()" class="btn-cancel">Close</button>
        <div id="modal-actions-group" style="display:flex; gap:8px; flex-wrap:wrap;">
          <!-- Populated by JS based on status -->
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const records = <?= json_encode($records ?? []) ?>;
    const counselors = <?= json_encode($counselors ?? []) ?>;
    let activeCategory = 'All Records';
    let currentSelectedRecord = null;

    const categories = [
      "All Records",
      "Family / Marital Issues",
      "Personal / Spiritual Struggles",
      "Parenting & Family Guidance",
      "Youth & Academic Concerns",
      "Financial Difficulties",
      "Grief and Loss",
      "Anger Management",
      "Revert / New Muslim Support",
      "Other"
    ];

    function renderTabs() {
      const dropdown = document.getElementById('category-dropdown');
      dropdown.innerHTML = categories.map(cat => `
        <option value="${cat}" ${activeCategory === cat ? 'selected' : ''}>${cat}</option>
      `).join('');
    }

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = records.length;
      const pending = records.filter(r => (r.status || '').toLowerCase() === 'pending').length;
      const underReview = records.filter(r => (r.status || '').toLowerCase() === 'under_review').length;
      const confirmed = records.filter(r => ['confirmed', 'approved'].includes((r.status || '').toLowerCase())).length;
      const completed = records.filter(r => (r.status || '').toLowerCase() === 'completed').length;

      container.innerHTML = `
        <div class="insight-card all" onclick="setFilter('all')">
          <div class="insight-label">Total Requests</div>
          <div class="insight-value">${total}</div>
        </div>
        <div class="insight-card pending" onclick="setFilter('pending')">
          <div class="insight-label">Pending Review</div>
          <div class="insight-value" style="color:#d97706;">${pending}</div>
        </div>
        <div class="insight-card under_review" onclick="setFilter('under_review')">
          <div class="insight-label">Under Review</div>
          <div class="insight-value" style="color:#4f46e5;">${underReview}</div>
        </div>
        <div class="insight-card confirmed" onclick="setFilter('confirmed')">
          <div class="insight-label">Confirmed Appointments</div>
          <div class="insight-value" style="color:#059669;">${confirmed}</div>
        </div>
        <div class="insight-card completed" onclick="setFilter('completed')">
          <div class="insight-label">Completed Sessions</div>
          <div class="insight-value" style="color:#475569;">${completed}</div>
        </div>
      `;
    }

    function setFilter(st) {
      document.getElementById('status-filter').value = st;
      renderActiveCategory();
    }

    function switchCategory(cat) {
      activeCategory = cat;
      renderTabs();
      document.getElementById('current-category-title').innerHTML = `
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        ${cat}
      `;
      renderActiveCategory();
    }

    function getStatusBadge(st) {
      st = (st || 'pending').toLowerCase();
      const map = {
        'pending': { label: 'Pending', cls: 'badge-pending' },
        'under_review': { label: 'Under Review', cls: 'badge-under_review' },
        'approved_awaiting_schedule': { label: 'Approved, Awaiting Schedule', cls: 'badge-awaiting' },
        'confirmed': { label: 'Confirmed', cls: 'badge-confirmed' },
        'approved': { label: 'Confirmed', cls: 'badge-confirmed' },
        'completed': { label: 'Completed', cls: 'badge-completed' },
        'no_show': { label: 'No-Show', cls: 'badge-noshow' },
        'rejected': { label: 'Rejected', cls: 'badge-rejected' },
        'cancelled': { label: 'Cancelled', cls: 'badge-cancelled' }
      };
      const item = map[st] || { label: st.toUpperCase(), cls: 'badge-pending' };
      return `<span class="badge-status ${item.cls}">${item.label}</span>`;
    }

    function renderActiveCategory() {
      const tbody = document.getElementById('counseling-tbody');
      const statusFilter = document.getElementById('status-filter').value;
      
      let filtered = records;
      if (activeCategory !== 'All Records') filtered = filtered.filter(r => r.reason === activeCategory);
      if (statusFilter !== 'all') {
        filtered = filtered.filter(r => {
          const st = (r.status || '').toLowerCase();
          if (statusFilter === 'confirmed') return (st === 'confirmed' || st === 'approved');
          return st === statusFilter;
        });
      }

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No records found matching the active criteria.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(r => {
        const st = (r.status || 'pending').toLowerCase();
        const dateSubmitted = new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const prefSchedule = (r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'Flexible')
                            + (r.preferred_time ? ` (${r.preferred_time})` : '');
        const prefType = r.session_type || 'In-Person';

        return `
          <tr>
            <td class="td-id">#MC-${String(r.id).padStart(4, '0')}</td>
            <td style="font-weight:600;">
              <div>${r.first_name || ''} ${r.last_name || ''}</div>
              <div style="font-size:0.75rem; color:var(--text-muted); font-weight:normal;">${r.contact_number || r.email || ''}</div>
            </td>
            <td>${dateSubmitted}</td>
            <td><span style="font-weight:700; color:var(--primary);">${r.reason}</span></td>
            <td><span style="font-size:0.82rem; font-weight:600; color:#334155;">${prefType}</span></td>
            <td>${prefSchedule}</td>
            <td>${getStatusBadge(st)}</td>
            <td>
              <div class="actions-cell">
                <button class="btn-action btn-view" onclick='viewCounselingDetails(${JSON.stringify(r)})'>
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  View Details
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function viewCounselingDetails(r) {
      currentSelectedRecord = r;
      hideAllActionPanels();

      // Client info
      document.getElementById('modal-c-name').innerText = `${r.first_name || ''} ${r.last_name || ''}`;
      const contactInfo = [r.email, r.contact_number, r.civil_status ? `(${r.civil_status})` : '', r.age ? `${r.age} yrs old` : ''].filter(Boolean).join(' • ');
      document.getElementById('modal-c-contact').innerText = contactInfo || 'No contact registered';
      document.getElementById('modal-c-address').innerText = r.current_address ? `Address: ${r.current_address}` : '';

      // Concern
      document.getElementById('modal-c-reason').innerText = r.reason || 'General Guidance';
      const descEl = document.getElementById('modal-c-desc');
      if (r.description && r.description.trim() !== '') {
        descEl.innerText = r.description;
        descEl.style.display = 'block';
      } else {
        descEl.style.display = 'none';
      }

      // Preferred
      document.getElementById('modal-c-pref-sched').innerText = (r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { weekday:'short', month:'long', day:'numeric', year:'numeric' }) : 'Flexible') + (r.preferred_time ? ` — ${r.preferred_time}` : '');
      document.getElementById('modal-c-pref-type').innerText = (r.session_type || 'In-Person') + ' Consultation';
      document.getElementById('modal-c-ref').innerText = `#MC-${String(r.id).padStart(4, '0')}`;
      document.getElementById('modal-c-submitted').innerText = new Date(r.created_at).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'numeric' });

      // Status badge
      const st = (r.status || 'pending').toLowerCase();
      document.getElementById('modal-c-badge-wrap').innerHTML = getStatusBadge(st);

      // Confirmed appointment details
      const confBox = document.getElementById('modal-c-confirmed-box');
      if (['confirmed', 'approved', 'completed'].includes(st)) {
        confBox.style.display = 'block';
        document.getElementById('modal-c-confirmed-counselor').innerText = r.counselor_full_name || r.counselor_name || 'Assigned Counselor';
        const aDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'Date Set';
        const aTime = (r.start_time ? r.start_time : '') + (r.end_time ? ` - ${r.end_time}` : '');
        document.getElementById('modal-c-confirmed-datetime').innerText = `${aDate} (${aTime || 'Scheduled'})`;

        const locText = (r.final_session_type === 'Online') 
          ? `Online Session • Link: ${r.meeting_link || 'Link will be sent to applicant'}`
          : (r.appointment_location || 'ISCAG Center, 2nd Floor Da\'wah Counseling Office');
        document.getElementById('modal-c-confirmed-location').innerText = locText;
      } else {
        confBox.style.display = 'none';
      }

      // Rejection box
      const rejBox = document.getElementById('modal-c-rejection-box');
      if (st === 'rejected' && r.rejection_reason) {
        rejBox.style.display = 'block';
        document.getElementById('modal-c-rejection-text').innerText = r.rejection_reason;
      } else {
        rejBox.style.display = 'none';
      }

      // Prepopulate accept/scheduling inputs
      if (r.preferred_date) document.getElementById('sched-date').value = r.preferred_date;
      if (r.session_type) {
        document.getElementById('sched-type').value = r.session_type;
        toggleLocationFields();
      }
      if (r.counselor_id) document.getElementById('sched-counselor-id').value = r.counselor_id;

      // Populate Footer Buttons based on Status
      const actGroup = document.getElementById('modal-actions-group');
      actGroup.innerHTML = '';

      if (st === 'pending') {
        actGroup.innerHTML = `
          <button type="button" class="btn-action btn-view" style="color:var(--primary); border-color:var(--primary);" onclick="markUnderReview(${r.id})">
            Begin Review
          </button>
          <button type="button" class="btn-action btn-approve" onclick="showActionPanel('panel-accept-schedule')">
            Accept &amp; Set Appointment
          </button>
          <button type="button" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Reject Request
          </button>
        `;
      } else if (st === 'under_review') {
        actGroup.innerHTML = `
          <button type="button" class="btn-action btn-approve" onclick="showActionPanel('panel-accept-schedule')">
            Accept &amp; Set Appointment
          </button>
          <button type="button" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Reject Request
          </button>
        `;
      } else if (st === 'confirmed' || st === 'approved') {
        actGroup.innerHTML = `
          <button type="button" class="btn-action btn-approve" onclick="showActionPanel('panel-complete')">
            ✓ Mark as Completed
          </button>
          <button type="button" class="btn-action btn-view" onclick="showActionPanel('panel-accept-schedule')">
            Update Appointment Details
          </button>
          <button type="button" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Cancel / Reject
          </button>
        `;
      } else if (st === 'completed') {
        actGroup.innerHTML = `
          <span style="font-size:0.85rem; color:#15803d; font-weight:700; align-self:center;">✓ Session Completed</span>
        `;
      } else if (st === 'rejected') {
        actGroup.innerHTML = `
          <button type="button" class="btn-action btn-approve" onclick="showActionPanel('panel-accept-schedule')">
            Reconsider &amp; Schedule
          </button>
        `;
      }

      document.getElementById('counseling-modal').style.display = 'flex';
    }

    function closeCounselingModal() {
      document.getElementById('counseling-modal').style.display = 'none';
      hideAllActionPanels();
    }

    function showActionPanel(panelId) {
      hideAllActionPanels();
      const p = document.getElementById(panelId);
      if (p) {
        p.style.display = 'block';
        p.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    }

    function hideAllActionPanels() {
      document.querySelectorAll('.counselor-action-panel').forEach(p => p.style.display = 'none');
    }

    function toggleLocationFields() {
      const type = document.getElementById('sched-type').value;
      const isOnline = (type === 'Online');
      document.getElementById('wrap-location').style.display = isOnline ? 'none' : 'block';
      document.getElementById('wrap-link').style.display = isOnline ? 'block' : 'none';
      document.getElementById('wrap-instructions').style.display = isOnline ? 'block' : 'none';
    }

    // Live conflict checking before submission
    async function runLiveConflictCheck() {
      const cid = document.getElementById('sched-counselor-id').value;
      const date = document.getElementById('sched-date').value;
      const start = document.getElementById('sched-start-time').value;
      const end = document.getElementById('sched-end-time').value;
      const warnBox = document.getElementById('conflict-warning');
      const submitBtn = document.getElementById('btn-submit-schedule');

      if (!cid || !date || !start) {
        warnBox.style.display = 'none';
        submitBtn.disabled = false;
        return;
      }

      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/check-conflict') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            counselor_id: cid,
            appointment_date: date,
            start_time: start,
            end_time: end,
            exclude_id: currentSelectedRecord ? currentSelectedRecord.id : 0
          })
        });
        const res = await response.json();
        if (res.conflict && res.details) {
          const d = res.details;
          document.getElementById('conflict-warning-msg').innerText = `Counselor already has an appointment (${d.reason}) on ${d.appointment_date} at ${d.start_time} - ${d.end_time || '1 hour'}.`;
          warnBox.style.display = 'block';
          submitBtn.disabled = true;
        } else {
          warnBox.style.display = 'none';
          submitBtn.disabled = false;
        }
      } catch(e) {
        console.error(e);
      }
    }

    // Submit accept & finalize schedule
    async function submitFinalSchedule() {
      if (!currentSelectedRecord) return;
      const cid = document.getElementById('sched-counselor-id').value;
      const date = document.getElementById('sched-date').value;
      const start = document.getElementById('sched-start-time').value;
      const end = document.getElementById('sched-end-time').value;
      const type = document.getElementById('sched-type').value;
      const loc = document.getElementById('sched-location').value;
      const link = document.getElementById('sched-link').value;
      const instr = document.getElementById('sched-instructions').value;
      const notes = document.getElementById('sched-notes').value;

      if (!cid) {
        alert('Please assign an authorized counselor.');
        return;
      }
      if (!date || !start) {
        alert('Please set the appointment date and start time.');
        return;
      }

      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/accept-schedule') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: currentSelectedRecord.id,
            counselor_id: cid,
            appointment_date: date,
            start_time: start,
            end_time: end,
            final_session_type: type,
            appointment_location: loc,
            meeting_link: link,
            meeting_instructions: instr,
            counselor_notes: notes
          })
        });
        const res = await response.json();
        if (res.success) {
          showAlert('Appointment Confirmed', 'The request has been approved, counselor assigned, and appointment confirmed. Applicant notified.', 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Action Failed', res.message || 'Could not schedule appointment.', 'error');
        }
      } catch(e) {
        console.error(e);
        showAlert('Network Error', 'Could not process scheduling.', 'error');
      }
    }

    async function markUnderReview(id) {
      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/review') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const res = await response.json();
        if (res.success) {
          showAlert('Status Updated', 'Request marked as Under Review.', 'success');
          setTimeout(() => location.reload(), 900);
        }
      } catch(e) {
        console.error(e);
      }
    }

    async function submitReject() {
      if (!currentSelectedRecord) return;
      const reason = document.getElementById('reject-reason').value;

      if (!reason || reason.trim() === '') {
        alert('A rejection reason is strictly required.');
        document.getElementById('reject-reason').focus();
        return;
      }

      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/reject') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: currentSelectedRecord.id,
            reason: reason.trim()
          })
        });
        const res = await response.json();
        if (res.success) {
          showAlert('Request Disapproved', 'The request was rejected and client notified with the reason.', 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Action Failed', res.message || 'Could not reject request.', 'error');
        }
      } catch(e) {
        console.error(e);
        showAlert('Network Error', 'Submission failed.', 'error');
      }
    }

    async function submitComplete() {
      if (!currentSelectedRecord) return;
      const notes = document.getElementById('complete-notes').value;

      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/complete') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: currentSelectedRecord.id,
            notes: notes
          })
        });
        const res = await response.json();
        if (res.success) {
          showAlert('Session Completed', 'Counseling consultation successfully marked as Completed.', 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Notice', res.message || 'Could not complete session.', 'error');
        }
      } catch(e) {
        console.error(e);
        showAlert('Network Error', 'Operation failed.', 'error');
      }
    }

    renderTabs();
    renderInsights();
    renderActiveCategory();
  </script>
</body>
</html>
