<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$dawah_type = $dawah_type ?? 'male';
$isFemale = ($dawah_type === 'female');
$prefix = $isFemale ? 'FC-' : 'MC-';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Counseling Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* ── Apartment Admin Consistent Styling ── */
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }
    .insight-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.08);
      border-color: var(--accent);
    }
    .insight-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .insight-card.pending::after { background: var(--warning); }
    .insight-card.total::after { background: var(--primary); }
    .insight-card.confirmed::after, .insight-card.approved::after { background: var(--success); }
    .insight-card.rejected::after { background: var(--danger); }

    .insight-label {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .insight-value {
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1;
    }
    .insight-value.info { color: var(--primary); }
    .insight-value.success { color: var(--success); }
    .insight-value.danger { color: var(--danger); }
    .insight-value.warning { color: var(--warning); }
    
    .insight-card svg {
      position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
      width: 24px; height: 24px; opacity: 0.15;
    }

    /* ── Table Typography & Badges ── */
    .td-id { font-weight: 800; color: var(--primary-dark); font-size: 0.85rem; }
    
    .badge-status {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 12px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: 0.04em;
    }
    .badge-status.pending, .badge-status.badge-pending { 
      background: rgba(199, 154, 43, 0.1); color: #b08d2e; 
    }
    .badge-status.under_review, .badge-status.badge-under_review { 
      background: rgba(79, 70, 229, 0.1); color: #4f46e5; 
    }
    .badge-status.confirmed, .badge-status.badge-confirmed,
    .badge-status.approved, .badge-status.badge-approved { 
      background: rgba(47, 138, 96, 0.1); color: #2f8a60; 
    }
    .badge-status.completed, .badge-status.badge-completed {
      background: rgba(100, 116, 139, 0.1); color: #475569;
    }
    .badge-status.rejected, .badge-status.badge-rejected { 
      background: rgba(139, 46, 46, 0.1); color: #8b2e2e; 
    }
    .badge-status.cancelled, .badge-status.badge-cancelled {
      background: rgba(156, 163, 175, 0.1); color: #6b7280;
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* ── Action Buttons ── */
    .actions-cell { display: flex; align-items: center; gap: 6px; }
    .btn-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      border: none; background: transparent;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: var(--text-muted); }
    .btn-circle:hover { transform: translateY(-1px); background: rgba(0,0,0,0.05); }
    
    .btn-circle.eye svg { fill: #1E90FF; }
    .btn-circle.eye:hover { background: rgba(30,144,255,0.1); }
    
    .btn-circle.check svg { fill: #2f8a60; }
    .btn-circle.check:hover { background: rgba(47,138,96,0.1); }
    
    .btn-circle.reject svg { fill: #8b2e2e; }
    .btn-circle.reject:hover { background: rgba(139,46,46,0.1); }

    .btn-circle.print svg { fill: #6366f1; }
    .btn-circle.print:hover { background: rgba(99,102,241,0.1); }

    .btn-circle:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Modal Backdrop and Card */
    .counseling-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.72); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center; padding: 20px;
      animation: fadeIn 0.25s ease;
    }
    .counseling-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 720px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.35); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .counseling-modal-header {
      padding: 20px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; background: #fafafa;
    }
    .counseling-modal-close {
      background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280; padding: 0; line-height: 1; transition: all 0.2s;
    }
    .counseling-modal-close:hover { color: var(--danger); transform: scale(1.1); }
    .counseling-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .counseling-modal-footer {
      padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;
    }

    .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    @media (max-width: 600px) { .info-grid-2 { grid-template-columns: 1fr; } }
    .info-box { background: #f9fafb; padding: 14px 16px; border-radius: 12px; border: 1px solid var(--border); }
    .info-box.highlight { background: #f0fdf4; border-color: #dcfce7; }
    .info-box-lbl { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; letter-spacing: 0.04em; }
    .info-box-val { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
    .info-box-val.gold { color: var(--primary); font-size: 1.05rem; }

    /* Action Form Panels inside modal */
    .counselor-action-panel {
      display: none; background: #f8fafc; border: 1.5px solid #cbd5e1;
      border-radius: 14px; padding: 20px; margin-top: 18px; animation: fadeIn 0.25s ease;
    }
    .action-panel-title {
      font-weight: 800; font-size: 0.9rem; color: #0f172a; text-transform: uppercase;
      letter-spacing: 0.04em; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
    }
    .c-field { margin-bottom: 14px; }
    .c-field label { display: block; font-size: 0.74rem; font-weight: 700; color: #475569; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.03em; }
    .c-field input, .c-field select, .c-field textarea {
      width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #cbd5e1;
      border-radius: 8px; font-size: 0.9rem; font-family: inherit; background: #fff;
    }
    .c-field input:focus, .c-field select:focus, .c-field textarea:focus {
      outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,83,45,0.12);
    }
    .conflict-warning-box {
      display: none; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 10px;
      padding: 12px 14px; font-size: 0.85rem; color: #991b1b; margin-bottom: 14px; line-height: 1.4;
    }
    .panel-btn-row { display: flex; gap: 10px; justify-content: flex-end; margin-top: 14px; }

    /* Print Slip Overlay */
    @media print {
      body * { visibility: hidden !important; }
      #printable-slip-area, #printable-slip-area * { visibility: visible !important; }
      #printable-slip-area {
        position: fixed; inset: 0; background: #fff !important; padding: 20px !important;
        display: block !important; z-index: 9999999;
      }
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'counseling';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title"><?= $isFemale ? "Sisters' Counseling Management" : "Male Counseling Management" ?></div>
            <div class="top-bar-subtitle"><?= $isFemale ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Review consultation cases, assign counselors &amp; manage session appointments</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
          <button class="btn-topbar primary" onclick="window.print()" title="Print Summary Report">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
            Print Report
          </button>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($isFemale ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Counseling</span>
          <span class="sep">›</span>
          <span class="current">Counseling Management</span>
        </div>

        <!-- 1. Operational Insights Ribbon (Apartment Admin style) -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered dynamically by JS -->
        </div>

        <!-- 2. Status Filter Tabs (Apartment Admin style) -->
        <div class="tab-nav">
          <button type="button" class="tab-btn active" onclick="setStatusFilter('all', this)">
            All Cases <span class="tab-count" id="count-all">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setStatusFilter('pending', this)">
            Pending <span class="tab-count pending" id="count-pending">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setStatusFilter('confirmed', this)">
            Confirmed <span class="tab-count approved" id="count-confirmed">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setStatusFilter('completed', this)">
            Completed <span class="tab-count" id="count-completed">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setStatusFilter('rejected', this)">
            Disapproved <span class="tab-count rejected" id="count-rejected">0</span>
          </button>
        </div>

        <!-- Secondary Filters & Search Toolbar -->
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
          <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <select id="category-filter" class="form-control" style="font-size:0.82rem; padding:6px 14px; border-radius:8px; border:1px solid var(--border); background:white; font-weight:600;" onchange="applyFilters()">
              <!-- Categories rendered by JS -->
            </select>
            <select id="mode-filter" class="form-control" style="font-size:0.82rem; padding:6px 14px; border-radius:8px; border:1px solid var(--border); background:white; font-weight:600;" onchange="applyFilters()">
              <option value="all">All Session Modes</option>
              <option value="In-Person">In-Person</option>
              <option value="Online">Online Consultation</option>
            </select>
          </div>
          <div style="display:flex; gap:10px; align-items:center;">
            <div style="position:relative; min-width:260px;">
              <input type="text" id="search-input" placeholder="Search case, name, or concern..." style="width:100%; box-sizing:border-box; padding:7px 12px 7px 34px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" onkeyup="applyFilters()" />
              <svg viewBox="0 0 24 24" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); width:16px; height:16px; fill:var(--text-muted);"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>
            <button type="button" class="btn-topbar" onclick="resetAllFilters()" style="padding:6px 14px; font-size:0.82rem;">Reset</button>
          </div>
        </div>

        <!-- 3. Table Section Card -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Counseling Case Registry
            </h6>
            <div id="results-count-badge" style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">
              Showing 0 records
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Client Information</th>
                    <th>Reason for Counseling</th>
                    <th>Date Filed</th>
                    <th>Preferred Schedule</th>
                    <th>Assigned Counselor &amp; Schedule</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="counseling-tbody">
                  <!-- Rendered dynamically by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 📋 4. Comprehensive Request Details, Review & Assignment Modal -->
  <div id="counseling-modal" class="counseling-modal-backdrop" onclick="if(event.target===this) closeCounselingModal()">
    <div class="counseling-modal-card">
      <div class="counseling-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
          Counseling Case Review &amp; Actions
        </h5>
        <button type="button" onclick="closeCounselingModal()" class="counseling-modal-close" title="Close Modal">&times;</button>
      </div>

      <div class="counseling-modal-body">
        <!-- Client Profile Box -->
        <div class="info-box highlight" style="margin-bottom:14px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
              <div class="info-box-lbl">Applicant Information</div>
              <div id="modal-c-name" class="info-box-val" style="font-size:1.15rem; color:var(--primary); font-weight:800;">—</div>
              <div id="modal-c-contact" style="font-size:0.85rem; color:#4b5563; margin-top:3px;">—</div>
              <div id="modal-c-address" style="font-size:0.82rem; color:#64748b; margin-top:2px;">—</div>
            </div>
            <div id="modal-c-badge-wrap">
              <span id="modal-c-status-badge" class="badge-status pending"><span class="status-dot"></span> Pending</span>
            </div>
          </div>
        </div>

        <!-- Request Details -->
        <div class="info-box" style="margin-bottom:14px;">
          <div class="info-box-lbl">Reason for Counseling Consultation</div>
          <div id="modal-c-reason" class="info-box-val gold">—</div>
          <div style="margin-top:8px;">
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); display:block; margin-bottom:4px;">Client's Detailed Statement:</span>
            <div id="modal-c-desc" style="font-size:0.88rem; color:#374151; line-height:1.55; background:#fff; padding:12px 14px; border-radius:8px; border:1px solid #e5e7eb;"></div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Requested Preferred Schedule</div>
            <div id="modal-c-pref-sched" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Requested Session Mode</div>
            <div id="modal-c-pref-type" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Official Reference Number</div>
            <div id="modal-c-ref" class="td-id" style="font-size:1.05rem;">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Date &amp; Time Submitted</div>
            <div id="modal-c-submitted" class="info-box-val">—</div>
          </div>
        </div>

        <!-- Confirmed Appointment Section (If confirmed or completed) -->
        <div id="modal-c-confirmed-box" class="info-box" style="margin-bottom:14px; background:#f0fdf4; border-color:#bbf7d0; display:none;">
          <div class="info-box-lbl" style="color:#166534; font-weight:800;">✓ Confirmed Session &amp; Counselor Details</div>
          <div style="margin-top:8px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Assigned Counselor:</span>
              <div id="modal-c-confirmed-counselor" style="font-weight:700; color:#0f172a; margin-top:2px;">—</div>
            </div>
            <div>
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Confirmed Date &amp; Time:</span>
              <div id="modal-c-confirmed-datetime" style="font-weight:700; color:#0f172a; margin-top:2px;">—</div>
            </div>
            <div style="grid-column: span 2;">
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Venue / Link Details:</span>
              <div id="modal-c-confirmed-location" style="font-size:0.88rem; color:#1f2937; margin-top:2px;">—</div>
            </div>
            <div id="modal-c-notes-wrap" style="grid-column: span 2; display:none;">
              <span style="font-size:0.75rem; color:#15803d; font-weight:700;">Counselor Pre-Session Notes:</span>
              <div id="modal-c-confirmed-notes" style="font-size:0.85rem; color:#475569; margin-top:2px; font-style:italic;">—</div>
            </div>
          </div>
        </div>

        <!-- Rejection Reason if Rejected -->
        <div id="modal-c-rejection-box" class="info-box" style="margin-bottom:14px; background:#fef2f2; border-color:#fecaca; display:none;">
          <div class="info-box-lbl" style="color:#991b1b; font-weight:800;">Rejection Reason Notice</div>
          <div id="modal-c-rejection-text" style="font-size:0.88rem; color:#7f1d1d; margin-top:4px;">—</div>
        </div>

        <!-- ═══ SUB-PANELS FOR ACTIONS ═══ -->
        <!-- 1. Accept & Set Schedule Panel -->
        <div id="panel-accept-schedule" class="counselor-action-panel">
          <div class="action-panel-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--primary)"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Assign Counselor &amp; Finalize Session Appointment
          </div>

          <div id="conflict-warning" class="conflict-warning-box">
            <strong>⚠️ Schedule Conflict Alert:</strong> <span id="conflict-warning-msg"></span>
          </div>

          <div class="c-field">
            <label>Assigned Counselor <span style="color:red;">*</span></label>
            <select id="sched-counselor-id" onchange="runLiveConflictCheck()">
              <option value="">Select Qualified Counselor</option>
              <?php foreach ($counselors ?? [] as $c): ?>
                <option value="<?= $c['id'] ?>">
                  <?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['designation'] ?? 'Counselor') ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Appointment Date <span style="color:red;">*</span></label>
              <input type="date" id="sched-date" min="<?= date('Y-m-d') ?>" onchange="runLiveConflictCheck()" />
            </div>
            <div class="c-field">
              <label>Final Session Mode</label>
              <select id="sched-type" onchange="toggleLocationFields()">
                <option value="In-Person">In-Person (Mosque Office)</option>
                <option value="Online">Online Video Consultation</option>
              </select>
            </div>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Start Time <span style="color:red;">*</span></label>
              <input type="time" id="sched-start-time" value="10:00" onchange="runLiveConflictCheck()" />
            </div>
            <div class="c-field">
              <label>End Time (Expected)</label>
              <input type="time" id="sched-end-time" value="11:00" onchange="runLiveConflictCheck()" />
            </div>
          </div>

          <div class="c-field" id="wrap-location">
            <label>Venue / Consultation Room</label>
            <input type="text" id="sched-location" value="ISCAG Center, 2nd Floor Da'wah Counseling Office Room 204" />
          </div>

          <div class="c-field" id="wrap-link" style="display:none;">
            <label>Online Meeting URL (Google Meet / Zoom)</label>
            <input type="url" id="sched-link" placeholder="https://meet.google.com/xxx-xxxx-xxx" />
          </div>

          <div class="c-field" id="wrap-instructions" style="display:none;">
            <label>Online Meeting Access Instructions</label>
            <textarea id="sched-instructions" rows="2" placeholder="Please join via Google Meet 5 minutes before scheduled session in a quiet, private area."></textarea>
          </div>

          <div class="c-field">
            <label>Pre-Session Guidance / Preparation Notes for Client</label>
            <textarea id="sched-notes" rows="2" placeholder="Please bring any relevant documentation or questions. May Allah grant you ease."></textarea>
          </div>

          <div class="panel-btn-row">
            <button type="button" class="btn-topbar" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-topbar primary" id="btn-submit-schedule" onclick="submitFinalSchedule()">
              Confirm &amp; Notify Client
            </button>
          </div>
        </div>

        <!-- 2. Disapprove / Reject Panel -->
        <div id="panel-reject" class="counselor-action-panel" style="border-color:#fca5a5;">
          <div class="action-panel-title" style="color:var(--danger);">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--danger)"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
            Disapprove / Reject Counseling Request
          </div>
          <p style="font-size:0.82rem; color:#6b7280; margin:0 0 10px;">Please state the specific administrative reason. The applicant will be notified with this message.</p>
          <div class="c-field">
            <label>Rejection Reason <span style="color:red;">*</span></label>
            <textarea id="reject-reason" rows="3" placeholder="Provide clear reason why this request cannot be accommodated..."></textarea>
          </div>
          <div class="panel-btn-row">
            <button type="button" class="btn-topbar" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-topbar" style="background:var(--danger); color:#fff; border-color:var(--danger);" onclick="submitReject()">
              Confirm Disapproval
            </button>
          </div>
        </div>

        <!-- 3. Mark Completed Panel -->
        <div id="panel-complete" class="counselor-action-panel" style="border-color:#bbf7d0;">
          <div class="action-panel-title" style="color:#15803d;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            Conclude &amp; Mark Counseling Consultation as Completed
          </div>
          <div class="c-field">
            <label>Session Remarks / Outcome Summary (Confidential)</label>
            <textarea id="complete-notes" rows="3" placeholder="Session concluded smoothly. Recommended spiritual readings and follow-up in 1 month..."></textarea>
          </div>
          <div class="panel-btn-row">
            <button type="button" class="btn-topbar" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-topbar primary" onclick="submitComplete()">
              Mark Session Concluded
            </button>
          </div>
        </div>
      </div>

      <div class="counseling-modal-footer">
        <div id="modal-actions-left">
          <button type="button" class="btn-topbar" id="btn-print-slip" onclick="openPrintPassModal(currentSelectedRecord)" style="display:none;">
            🖨️ View Appointment Pass
          </button>
        </div>
        <div id="modal-actions-group" style="display:flex; gap:10px; align-items:center;">
          <!-- Dynamically populated buttons -->
        </div>
      </div>
    </div>
  </div>

  <!-- 🖨️ 5. Printable Official Pass Modal Area -->
  <div id="printable-slip-area" style="display:none;">
    <div style="max-width:680px; margin:0 auto; font-family:'Inter', Arial, sans-serif; border:2px solid #14532D; border-radius:12px; padding:32px; background:#fff; color:#111;">
      <div style="display:flex; align-items:center; justify-content:space-between; border-bottom:2px solid #14532D; padding-bottom:18px; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:14px;">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:58px; height:58px; border-radius:8px;" alt="ISCAG">
          <div>
            <h3 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.3rem;">ISLAMIC STUDIES, CALL &amp; GUIDANCE OF THE PHILIPPINES</h3>
            <p style="margin:2px 0 0; font-size:0.8rem; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">Da'wah Department — Counseling &amp; Guidance Desk</p>
          </div>
        </div>
        <div style="text-align:right;">
          <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Official Pass</span>
          <div id="slip-ref-no" style="font-family:monospace; font-size:1.1rem; font-weight:800; color:#14532D;">#MC-0000</div>
        </div>
      </div>

      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <span style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:#166534;">Scheduled Session:</span>
          <div id="slip-schedule" style="font-size:1.15rem; font-weight:800; color:#14532D; margin-top:2px;">—</div>
        </div>
        <div style="text-align:right;">
          <span style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:#166534;">Session Mode:</span>
          <div id="slip-mode" style="font-size:0.95rem; font-weight:700; color:#0f3e21;">In-Person</div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Applicant:</div>
          <div id="slip-name" style="font-weight:700; font-size:1rem; color:#111;">—</div>
          <div id="slip-contact" style="font-size:0.82rem; color:#4b5563; margin-top:2px;">—</div>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Assigned Counselor:</div>
          <div id="slip-counselor" style="font-weight:700; font-size:1rem; color:#14532D;">—</div>
          <div id="slip-counselor-role" style="font-size:0.82rem; color:#4b5563; margin-top:2px;">Senior Islamic Counselor</div>
        </div>
      </div>

      <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin-bottom:20px;">
        <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Venue Location &amp; Instructions:</div>
        <div id="slip-location" style="font-size:0.9rem; font-weight:600; color:#1f2937;">ISCAG Center, 2nd Floor Da'wah Counseling Room 204</div>
      </div>

      <div style="border-top:1px dashed #ccc; padding-top:14px; display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; color:#6b7280;">
        <span>Please arrive 10 minutes prior to your time slot in a state of purity (wudu).</span>
        <span>Issued by ISCAG Da'wah MIS</span>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const prefix = '<?= $prefix ?>';
    const records = <?= json_encode($records ?? []) ?>;
    const counselors = <?= json_encode($counselors ?? []) ?>;
    
    let activeStatusFilter = 'all';
    let activeCategoryFilter = 'All Concerns';
    let currentSelectedRecord = null;

    const concernCategories = [
      "All Concerns",
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

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = records.length;
      const pending = records.filter(r => ['pending', 'under_review'].includes((r.status || '').toLowerCase())).length;
      const confirmed = records.filter(r => ['confirmed', 'approved'].includes((r.status || '').toLowerCase())).length;
      const rejected = records.filter(r => (r.status || '').toLowerCase() === 'rejected').length;

      container.innerHTML = `
        <div class="insight-card pending" onclick="setStatusFilter('pending', document.querySelector('.tab-btn:nth-child(2)'))">
          <div class="insight-label">Pending Review</div>
          <div class="insight-value warning">${pending}</div>
          <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
        </div>
        <div class="insight-card confirmed" onclick="setStatusFilter('confirmed', document.querySelector('.tab-btn:nth-child(3)'))">
          <div class="insight-label">Confirmed &amp; Scheduled</div>
          <div class="insight-value success">${confirmed}</div>
          <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
        </div>
        <div class="insight-card rejected" onclick="setStatusFilter('rejected', document.querySelector('.tab-btn:nth-child(5)'))">
          <div class="insight-label">Disapproved</div>
          <div class="insight-value danger">${rejected}</div>
          <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" /></svg>
        </div>
        <div class="insight-card total" onclick="setStatusFilter('all', document.querySelector('.tab-btn:nth-child(1)'))">
          <div class="insight-label">Total Applications</div>
          <div class="insight-value info">${total}</div>
          <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        </div>
      `;

      // Update tab counts
      document.getElementById('count-all').innerText = total;
      document.getElementById('count-pending').innerText = pending;
      document.getElementById('count-confirmed').innerText = confirmed;
      document.getElementById('count-completed').innerText = records.filter(r => (r.status || '').toLowerCase() === 'completed').length;
      document.getElementById('count-rejected').innerText = rejected;
    }

    function renderCategoryDropdown() {
      const select = document.getElementById('category-filter');
      select.innerHTML = concernCategories.map(cat => `
        <option value="${cat}" ${activeCategoryFilter === cat ? 'selected' : ''}>${cat}</option>
      `).join('');
    }

    function setStatusFilter(st, btn) {
      activeStatusFilter = st;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      applyFilters();
    }

    function resetAllFilters() {
      activeStatusFilter = 'all';
      activeCategoryFilter = 'All Concerns';
      document.getElementById('category-filter').value = 'All Concerns';
      document.getElementById('mode-filter').value = 'all';
      document.getElementById('search-input').value = '';
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelector('.tab-btn:nth-child(1)')?.classList.add('active');
      applyFilters();
    }

    function getStatusBadge(st) {
      st = (st || 'pending').toLowerCase();
      let badgeCls = 'pending';
      let label = 'Pending';
      if (st === 'under_review') {
        badgeCls = 'under_review';
        label = 'Under Review';
      } else if (['confirmed', 'approved'].includes(st)) {
        badgeCls = 'approved';
        label = 'Confirmed';
      } else if (st === 'completed') {
        badgeCls = 'completed';
        label = 'Completed';
      } else if (st === 'rejected') {
        badgeCls = 'rejected';
        label = 'Disapproved';
      } else if (st === 'cancelled') {
        badgeCls = 'cancelled';
        label = 'Cancelled';
      }
      return `<span class="badge-status ${badgeCls}"><span class="status-dot"></span>${label}</span>`;
    }

    function applyFilters() {
      const tbody = document.getElementById('counseling-tbody');
      const cat = document.getElementById('category-filter').value;
      const mode = document.getElementById('mode-filter').value;
      const search = (document.getElementById('search-input').value || '').toLowerCase().trim();

      activeCategoryFilter = cat;

      let filtered = records.filter(r => {
        const st = (r.status || 'pending').toLowerCase();
        if (activeStatusFilter === 'pending') {
          if (!['pending', 'under_review'].includes(st)) return false;
        } else if (activeStatusFilter === 'confirmed') {
          if (!['confirmed', 'approved'].includes(st)) return false;
        } else if (activeStatusFilter !== 'all') {
          if (st !== activeStatusFilter) return false;
        }

        if (cat !== 'All Concerns' && r.reason !== cat) {
          return false;
        }

        if (mode !== 'all') {
          const rMode = r.final_session_type || r.session_type || 'In-Person';
          if (rMode !== mode) return false;
        }

        if (search) {
          const clientName = (r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`).toLowerCase();
          const refNo = `${prefix.toLowerCase()}${String(r.id).padStart(4, '0')}`;
          const contact = (r.contact_number || r.email || '').toLowerCase();
          const reason = (r.reason || '').toLowerCase();
          const counselor = (r.counselor_full_name || r.counselor_name || '').toLowerCase();

          const matches = clientName.includes(search) ||
                          refNo.includes(search) ||
                          String(r.id) === search ||
                          contact.includes(search) ||
                          reason.includes(search) ||
                          counselor.includes(search);
          if (!matches) return false;
        }

        return true;
      });

      document.getElementById('results-count-badge').innerText = `Showing ${filtered.length} of ${records.length} records`;

      if (filtered.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="8" style="text-align:center; padding:48px 20px; color:var(--text-muted);">
              <svg viewBox="0 0 24 24" style="width:40px;height:40px;fill:#cbd5e1;margin-bottom:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              <div style="font-weight:700; font-size:0.95rem; color:#475569;">No counseling cases found</div>
              <div style="font-size:0.8rem; margin-top:4px;">Try clearing search or switching status tabs.</div>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(r => {
        const st = (r.status || 'pending').toLowerCase();
        const dateSubmitted = r.created_at ? new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
        const prefSchedule = (r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'Flexible')
                            + (r.preferred_time ? ` (${r.preferred_time})` : '');
        
        const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous Client';
        const contact = r.contact_number || r.email || '';
        const age = r.age ? `${r.age} yrs` : '';

        const isPending = (st === 'pending' || st === 'under_review');
        const isConfirmed = ['confirmed', 'approved'].includes(st);
        const isCompleted = (st === 'completed');

        let counselorCell = '<span style="color:#9ca3af; font-size:0.8rem;">Awaiting Assignment</span>';
        if (isConfirmed) {
          const cName = r.counselor_full_name || r.counselor_name || 'Assigned Counselor';
          const aptDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric' }) : '';
          const aptTime = r.start_time ? ` @ ${r.start_time}` : '';
          counselorCell = `
            <div>
              <strong style="color:var(--primary); font-size:0.85rem;">${escapeHtml(cName)}</strong>
              <div style="font-size:0.75rem; color:#15803d; font-weight:700;">${aptDate}${aptTime}</div>
            </div>
          `;
        } else if (isCompleted) {
          const cName = r.counselor_full_name || r.counselor_name || 'Counselor';
          counselorCell = `<div style="font-size:0.84rem; font-weight:600; color:#334155;">${escapeHtml(cName)} (Concluded)</div>`;
        }

        return `
          <tr>
            <td class="td-id">#${prefix}${String(r.id).padStart(4, '0')}</td>
            <td>
              <div style="font-weight:700; color:#111827;">${escapeHtml(clientName)}</div>
              <div style="font-size:0.76rem; color:#6b7280; margin-top:2px; display:flex; gap:6px;">
                ${age ? `<span>${age}</span>` : ''}
                ${contact ? `<span>${escapeHtml(contact)}</span>` : ''}
              </div>
            </td>
            <td>
              <span style="font-weight:700; color:var(--primary);">${escapeHtml(r.reason || 'General Counseling')}</span>
              ${r.description ? `<div style="font-size:0.75rem; color:#6b7280; margin-top:2px; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(r.description)}">${escapeHtml(r.description)}</div>` : ''}
            </td>
            <td style="color:#4b5563;">${dateSubmitted}</td>
            <td style="color:#374151; font-weight:600;">${prefSchedule}</td>
            <td>${counselorCell}</td>
            <td>${getStatusBadge(st)}</td>
            <td>
              <div class="actions-cell">
                <button type="button" class="btn-circle eye" onclick='viewCounselingDetails(${JSON.stringify(r)})' title="Review Case Details">
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </button>
                ${isPending ? `
                  <button type="button" class="btn-circle check" onclick='openScheduleFromRow(${JSON.stringify(r)})' title="Accept & Set Appointment">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </button>
                  <button type="button" class="btn-circle reject" onclick='openRejectFromRow(${JSON.stringify(r)})' title="Reject Request">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  </button>
                ` : ''}
                ${(isConfirmed || isCompleted) ? `
                  <button type="button" class="btn-circle print" onclick='openPrintPassModal(${JSON.stringify(r)})' title="Print Appointment Slip">
                    <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                  </button>
                ` : ''}
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function openScheduleFromRow(r) {
      viewCounselingDetails(r);
      showActionPanel('panel-accept-schedule');
    }

    function openRejectFromRow(r) {
      viewCounselingDetails(r);
      showActionPanel('panel-reject');
    }

    function viewCounselingDetails(r) {
      currentSelectedRecord = r;
      hideAllActionPanels();

      const st = (r.status || 'pending').toLowerCase();
      const refNo = `#${prefix}${String(r.id).padStart(4, '0')}`;
      const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous Client';
      const contact = [r.contact_number, r.email].filter(Boolean).join(' • ') || 'No contact specified';
      const address = r.address || r.current_address || 'No physical address provided';
      const prefSched = (r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'Flexible')
                        + (r.preferred_time ? ` (${r.preferred_time})` : '');
      const prefType = r.session_type || 'In-Person';
      const submitted = r.created_at ? new Date(r.created_at).toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit' }) : '—';

      document.getElementById('modal-c-ref').innerText = refNo;
      document.getElementById('modal-c-name').innerText = clientName;
      document.getElementById('modal-c-contact').innerText = contact;
      document.getElementById('modal-c-address').innerText = address;
      document.getElementById('modal-c-reason').innerText = r.reason || 'General Islamic Guidance';
      document.getElementById('modal-c-desc').innerText = r.description || 'No additional narrative statement provided.';
      document.getElementById('modal-c-pref-sched').innerText = prefSched;
      document.getElementById('modal-c-pref-type').innerText = prefType;
      document.getElementById('modal-c-submitted').innerText = submitted;
      document.getElementById('modal-c-badge-wrap').innerHTML = getStatusBadge(st);

      // Print slip button
      const printBtn = document.getElementById('btn-print-slip');
      if (['confirmed', 'approved', 'completed'].includes(st)) {
        printBtn.style.display = 'inline-flex';
      } else {
        printBtn.style.display = 'none';
      }

      // Confirmed box
      const confBox = document.getElementById('modal-c-confirmed-box');
      if (['confirmed', 'approved', 'completed'].includes(st)) {
        confBox.style.display = 'block';
        document.getElementById('modal-c-confirmed-counselor').innerText = r.counselor_full_name || r.counselor_name || 'Assigned Counselor';
        const aDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
        const aTime = (r.start_time ? r.start_time : '') + (r.end_time ? ` – ${r.end_time}` : '');
        document.getElementById('modal-c-confirmed-datetime').innerText = `${aDate} at ${aTime || 'Scheduled Time'}`;
        
        const mode = r.final_session_type || r.session_type || 'In-Person';
        const loc = (mode === 'Online') ? `Online: ${r.meeting_link || 'Link provided upon confirmation'}` : (r.appointment_location || 'ISCAG Office');
        document.getElementById('modal-c-confirmed-location').innerText = loc;

        if (r.counselor_notes) {
          document.getElementById('modal-c-notes-wrap').style.display = 'block';
          document.getElementById('modal-c-confirmed-notes').innerText = r.counselor_notes;
        } else {
          document.getElementById('modal-c-notes-wrap').style.display = 'none';
        }
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
          <button type="button" class="btn-topbar" onclick="markUnderReview(${r.id})">
            Begin Review
          </button>
          <button type="button" class="btn-topbar primary" onclick="showActionPanel('panel-accept-schedule')">
            Accept &amp; Set Appointment
          </button>
          <button type="button" class="btn-topbar" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Reject Request
          </button>
        `;
      } else if (st === 'under_review') {
        actGroup.innerHTML = `
          <button type="button" class="btn-topbar primary" onclick="showActionPanel('panel-accept-schedule')">
            Accept &amp; Set Appointment
          </button>
          <button type="button" class="btn-topbar" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Reject Request
          </button>
        `;
      } else if (['confirmed', 'approved'].includes(st)) {
        actGroup.innerHTML = `
          <button type="button" class="btn-topbar primary" onclick="showActionPanel('panel-complete')">
            ✓ Mark as Completed
          </button>
          <button type="button" class="btn-topbar" onclick="showActionPanel('panel-accept-schedule')">
            Reschedule / Update
          </button>
          <button type="button" class="btn-topbar" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Cancel Session
          </button>
        `;
      } else if (st === 'completed') {
        actGroup.innerHTML = `
          <span style="font-size:0.85rem; color:#15803d; font-weight:700; align-self:center;">✓ Consultation Successfully Concluded</span>
        `;
      } else if (st === 'rejected') {
        actGroup.innerHTML = `
          <button type="button" class="btn-topbar primary" onclick="showActionPanel('panel-accept-schedule')">
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
          document.getElementById('conflict-warning-msg').innerText = `Counselor already has a confirmed session (${d.reason}) on ${d.appointment_date} at ${d.start_time} - ${d.end_time || '1 hour'}. Please adjust date, time slot, or counselor.`;
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
        document.getElementById('sched-counselor-id').focus();
        return;
      }
      if (!date || !start) {
        alert('Please select both the appointment date and starting time slot.');
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
          showAlert('Appointment Confirmed', 'The counseling request has been approved, counselor assigned, and appointment confirmed. Applicant notified.', 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Action Failed', res.message || 'Could not schedule appointment.', 'error');
        }
      } catch(e) {
        console.error(e);
        showAlert('Network Error', 'Could not process scheduling request.', 'error');
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

    function openPrintPassModal(r) {
      if (!r) return;
      const refNo = `#${prefix}${String(r.id).padStart(4, '0')}`;
      const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Client';
      const aDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'Confirmed Date';
      const aTime = (r.start_time ? r.start_time : '') + (r.end_time ? ` – ${r.end_time}` : '');
      const mode = r.final_session_type || r.session_type || 'In-Person';

      document.getElementById('slip-ref-no').innerText = refNo;
      document.getElementById('slip-name').innerText = clientName;
      document.getElementById('slip-contact').innerText = [r.contact_number, r.email].filter(Boolean).join(' • ');
      document.getElementById('slip-schedule').innerText = `${aDate} at ${aTime || '10:00 AM'}`;
      document.getElementById('slip-mode').innerText = mode;
      document.getElementById('slip-counselor').innerText = r.counselor_full_name || r.counselor_name || 'Assigned Counselor';
      document.getElementById('slip-counselor-role').innerText = r.counselor_designation || r.counselor_role || 'Islamic Counselor';

      const locText = (mode === 'Online') 
        ? `Online Consultation • Link: ${r.meeting_link || 'Link provided upon confirmation'}\nInstructions: ${r.meeting_instructions || 'Ensure working audio 5 mins prior.'}`
        : (r.appointment_location || 'ISCAG Center, 2nd Floor Da\'wah Counseling Office Room 204');
      document.getElementById('slip-location').innerText = locText;

      const slipArea = document.getElementById('printable-slip-area');
      slipArea.style.display = 'block';
      window.print();
      slipArea.style.display = 'none';
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str).replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
      });
    }

    // Initialize UI
    renderInsights();
    renderCategoryDropdown();
    applyFilters();
  </script>
</body>
</html>
