<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Counseling Requests &amp; Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary-male: #14532D;
      --primary-male-dark: #0f3e21;
      --primary-male-light: #f0fdf4;
      --gold-accent: #D4AF37;
      --gold-hover: #b8972f;
      --border-color: #e5e7eb;
    }

    /* Status Badges */
    .badge-status {
      padding: 6px 12px; border-radius: 20px; font-size: 0.74rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.03em; display: inline-flex; align-items: center; gap: 6px;
    }
    .badge-status.badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-status.badge-under_review { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .badge-status.badge-confirmed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-status.badge-completed { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .badge-status.badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-status.badge-cancelled { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
    .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* Operational KPI Insights Grid */
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white; padding: 18px 20px; border-radius: 16px; border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.25s ease;
      display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; cursor: pointer;
    }
    .insight-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,0.08); border-color: var(--primary); }
    .insight-card.active-kpi { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(20, 83, 45, 0.2); }
    .insight-card::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--border); }
    .insight-card.all::after { background: var(--primary); }
    .insight-card.pending::after { background: #f59e0b; }
    .insight-card.under_review::after { background: #6366f1; }
    .insight-card.confirmed::after { background: #10b981; }
    .insight-card.completed::after { background: #64748b; }
    .insight-card.rejected::after { background: #ef4444; }

    .insight-label { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.65rem; font-weight: 800; color: var(--text-main); line-height: 1; }

    /* Filters & Controls Toolbar */
    .toolbar-filter-card {
      background: #fff; border-radius: 16px; border: 1px solid var(--border);
      padding: 18px 22px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      display: flex; flex-direction: column; gap: 14px;
    }
    .filter-pills-row {
      display: flex; gap: 8px; align-items: center; flex-wrap: wrap; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px;
    }
    .status-pill {
      font-size: 0.78rem; font-weight: 700; padding: 6px 14px; border-radius: 20px;
      cursor: pointer; transition: all 0.2s; border: 1px solid var(--border); background: #fff; color: var(--text-muted);
      display: inline-flex; align-items: center; gap: 6px;
    }
    .status-pill:hover { border-color: var(--primary); color: var(--primary); }
    .status-pill.active { background: var(--primary); border-color: var(--primary); color: #fff; }
    .status-pill .pill-count {
      background: rgba(0,0,0,0.07); padding: 1px 7px; border-radius: 10px; font-size: 0.7rem;
    }
    .status-pill.active .pill-count { background: rgba(255,255,255,0.25); color: #fff; }

    .filter-controls-row {
      display: flex; gap: 14px; align-items: center; flex-wrap: wrap; justify-content: space-between;
    }
    .filter-select-group { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .custom-select {
      background: #f9fafb; border: 1px solid var(--border); border-radius: 10px;
      padding: 8px 14px; font-size: 0.85rem; font-weight: 600; color: var(--text-main);
      cursor: pointer; outline: none; transition: all 0.2s;
    }
    .custom-select:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(20,83,45,0.1); }

    .search-box-wrap {
      display: flex; align-items: center; background: #f9fafb; border: 1px solid var(--border);
      border-radius: 10px; padding: 6px 14px; gap: 8px; min-width: 280px; flex: 1; max-width: 380px;
      transition: all 0.2s;
    }
    .search-box-wrap:focus-within { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(20,83,45,0.1); }
    .search-box-wrap input {
      border: none; outline: none; background: transparent; font-size: 0.85rem; width: 100%; font-family: inherit;
    }
    .search-box-wrap svg { width: 16px; height: 16px; fill: var(--text-muted); flex-shrink: 0; }

    .btn-reset-filters {
      background: none; border: 1px dashed var(--border); padding: 8px 14px;
      border-radius: 10px; font-size: 0.82rem; font-weight: 700; color: var(--text-muted);
      cursor: pointer; transition: all 0.2s;
    }
    .btn-reset-filters:hover { border-color: var(--danger); color: var(--danger); background: #fef2f2; }

    /* Client info cell */
    .client-cell { display: flex; flex-direction: column; gap: 2px; }
    .client-name { font-weight: 700; color: #111827; font-size: 0.92rem; }
    .client-meta { font-size: 0.76rem; color: #6b7280; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .client-tag {
      background: #f3f4f6; padding: 1px 6px; border-radius: 4px; font-size: 0.68rem; font-weight: 700; color: #4b5563;
    }

    /* Monospace Ref Tag */
    .ref-badge {
      font-family: monospace; font-size: 0.85rem; font-weight: 800; color: var(--primary);
      background: var(--primary-light); padding: 4px 8px; border-radius: 6px; border: 1px solid #bbf7d0;
      display: inline-block; cursor: pointer; transition: all 0.2s;
    }
    .ref-badge:hover { background: var(--primary); color: #fff; }

    /* Concern Tag */
    .concern-tag {
      display: inline-flex; align-items: center; gap: 5px; font-weight: 700; font-size: 0.82rem;
      color: var(--primary-male); background: #f0fdf4; border: 1px solid #dcfce7; padding: 4px 10px; border-radius: 8px;
    }

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
    .info-box-val.gold { color: #14532d; font-family: 'Lora', serif; font-size: 1.05rem; }

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
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Male Counseling Requests &amp; Case Registry</div>
            <div class="top-bar-subtitle">Da'wah Department — Brother-to-brother guidance request processing, counselor scheduling &amp; case tracking</div>
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
          <a href="<?= url('/admin/dawah/male') ?>">Male Da'wah Dashboard</a>
          <span class="sep">›</span>
          <span class="current">Counseling Requests Module</span>
        </div>

        <!-- 1. Operational KPI Cards -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered dynamically by JS -->
        </div>

        <!-- 2. Interactive Filter & Search Toolbar -->
        <div class="toolbar-filter-card">
          <!-- Filter Pills by Status -->
          <div class="filter-pills-row" id="filter-pills-wrap">
            <!-- Rendered by JS -->
          </div>

          <!-- Secondary Filters & Realtime Search -->
          <div class="filter-controls-row">
            <div class="filter-select-group">
              <label style="font-size:0.75rem; font-weight:800; text-transform:uppercase; color:var(--text-muted);">Concern Category:</label>
              <select id="category-filter" class="custom-select" onchange="applyFilters()">
                <!-- Rendered by JS -->
              </select>

              <label style="font-size:0.75rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); margin-left:8px;">Session Mode:</label>
              <select id="mode-filter" class="custom-select" onchange="applyFilters()">
                <option value="all">All Session Modes</option>
                <option value="In-Person">In-Person (Mosque Office)</option>
                <option value="Online">Online Consultation</option>
              </select>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
              <div class="search-box-wrap">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="search-input" placeholder="Search applicant, ref #, contact, or concern..." onkeyup="applyFilters()" />
              </div>
              <button type="button" class="btn-reset-filters" onclick="resetAllFilters()" title="Reset all active filters">Reset</button>
            </div>
          </div>
        </div>

        <!-- 3. Requests Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 id="table-header-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Brothers' Counseling Applications Registry
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
                    <th>Date Filed</th>
                    <th>Reason for Counseling</th>
                    <th>Session Mode</th>
                    <th>Preferred Schedule</th>
                    <th>Assigned Counselor &amp; Schedule</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
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
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-male); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary-male);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
          Counseling Case Review &amp; Actions
        </h5>
        <button type="button" onclick="closeCounselingModal()" class="counseling-modal-close" title="Close Modal">&times;</button>
      </div>

      <div class="counseling-modal-body">
        <!-- Client Profile Box -->
        <div class="info-box highlight" style="margin-bottom:14px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
              <div class="info-box-lbl">Applicant / Brother Information</div>
              <div id="modal-c-name" class="info-box-val" style="font-size:1.15rem; color:#14532D;">—</div>
              <div id="modal-c-contact" style="font-size:0.85rem; color:#4b5563; margin-top:3px;">—</div>
              <div id="modal-c-address" style="font-size:0.82rem; color:#64748b; margin-top:2px;">—</div>
            </div>
            <div id="modal-c-badge-wrap">
              <span id="modal-c-status-badge" class="badge-status badge-pending">Pending</span>
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
            <div id="modal-c-ref" class="info-box-val" style="font-family:monospace; color:var(--primary-male);">—</div>
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

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- ⚙️ ACTION PANELS                                         -->
        <!-- ══════════════════════════════════════════════════════════ -->

        <!-- 1. Accept Request & Set Appointment Panel -->
        <div id="panel-accept-schedule" class="counselor-action-panel">
          <div class="action-panel-title" style="color:var(--primary-male);">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="var(--primary-male)"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Accept Request: Assign Counselor &amp; Finalize Appointment Schedule
          </div>

          <!-- Conflict Warning Banner -->
          <div id="conflict-warning" class="conflict-warning-box">
            <strong>⚠️ Scheduling Conflict Warning:</strong>
            <span id="conflict-warning-msg">This counselor already has an appointment during this time slot.</span>
          </div>

          <div class="c-field">
            <label>Assign Male Counselor <span style="color:red;">*</span></label>
            <select id="sched-counselor-id" onchange="runLiveConflictCheck()">
              <option value="">— Select Authorized Counselor —</option>
              <?php foreach ($counselors ?? [] as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['designation']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Appointment Date <span style="color:red;">*</span></label>
              <input type="date" id="sched-date" min="<?= date('Y-m-d') ?>" onchange="runLiveConflictCheck()" />
            </div>
            <div class="c-field">
              <label>Final Session Type</label>
              <select id="sched-type" onchange="toggleLocationFields()">
                <option value="In-Person">In-Person (Mosque Da'wah Office)</option>
                <option value="Online">Online Consultation (Google Meet)</option>
              </select>
            </div>
          </div>

          <div class="info-grid-2">
            <div class="c-field">
              <label>Start Time <span style="color:red;">*</span></label>
              <select id="sched-start-time" onchange="runLiveConflictCheck()">
                <option value="">— Select Start Slot —</option>
                <option value="09:00 AM">09:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
              </select>
            </div>
            <div class="c-field">
              <label>End Time (Estimated)</label>
              <select id="sched-end-time" onchange="runLiveConflictCheck()">
                <option value="">— Auto (+1 hr) —</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="12:00 PM">12:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
              </select>
            </div>
          </div>

          <div class="c-field" id="wrap-location">
            <label>Physical Venue &amp; Room</label>
            <input type="text" id="sched-location" value="ISCAG Center, 2nd Floor Da'wah Counseling Office Room 204" />
          </div>

          <div class="c-field" id="wrap-link" style="display:none;">
            <label>Online Meeting Link (Google Meet / Zoom)</label>
            <input type="url" id="sched-link" placeholder="https://meet.google.com/xxx-xxxx-xxx" />
          </div>

          <div class="c-field" id="wrap-instructions" style="display:none;">
            <label>Online Instructions for Brother</label>
            <textarea id="sched-instructions" rows="2">Please join 5 minutes early with working audio/mic in a private quiet room.</textarea>
          </div>

          <div class="c-field">
            <label>Counselor Preparation Notes (Internal / Confidential)</label>
            <textarea id="sched-notes" rows="2" placeholder="Optional background notes for counselor review..."></textarea>
          </div>

          <div class="panel-btn-row">
            <button type="button" class="btn-action" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-approve" id="btn-submit-schedule" onclick="submitFinalSchedule()">
              Confirm Appointment &amp; Notify Brother
            </button>
          </div>
        </div>

        <!-- 2. Reject Request Panel -->
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
            <button type="button" class="btn-action" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-edit" style="background:var(--danger); color:#fff; border-color:var(--danger);" onclick="submitReject()">
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
            <button type="button" class="btn-action" onclick="hideAllActionPanels()">Cancel</button>
            <button type="button" class="btn-action btn-approve" onclick="submitComplete()">
              Mark Session Concluded
            </button>
          </div>
        </div>
      </div>

      <div class="counseling-modal-footer">
        <div id="modal-actions-left">
          <button type="button" class="btn-action" id="btn-print-slip" onclick="openPrintPassModal(currentSelectedRecord)" style="display:none;">
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
            <p style="margin:2px 0 0; font-size:0.8rem; color:#4b5563; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">Da'wah Department — Brother's Counseling &amp; Guidance Desk</p>
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
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Brother / Applicant:</div>
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
      const pending = records.filter(r => (r.status || '').toLowerCase() === 'pending').length;
      const underReview = records.filter(r => (r.status || '').toLowerCase() === 'under_review').length;
      const confirmed = records.filter(r => ['confirmed', 'approved'].includes((r.status || '').toLowerCase())).length;
      const completed = records.filter(r => (r.status || '').toLowerCase() === 'completed').length;
      const rejected = records.filter(r => (r.status || '').toLowerCase() === 'rejected').length;

      container.innerHTML = `
        <div class="insight-card all ${activeStatusFilter === 'all' ? 'active-kpi' : ''}" onclick="setStatusFilter('all')">
          <div class="insight-label">Total Applications</div>
          <div class="insight-value">${total}</div>
        </div>
        <div class="insight-card pending ${activeStatusFilter === 'pending' ? 'active-kpi' : ''}" onclick="setStatusFilter('pending')">
          <div class="insight-label">Pending Review</div>
          <div class="insight-value" style="color:#d97706;">${pending}</div>
        </div>
        <div class="insight-card under_review ${activeStatusFilter === 'under_review' ? 'active-kpi' : ''}" onclick="setStatusFilter('under_review')">
          <div class="insight-label">Under Review</div>
          <div class="insight-value" style="color:#4f46e5;">${underReview}</div>
        </div>
        <div class="insight-card confirmed ${activeStatusFilter === 'confirmed' ? 'active-kpi' : ''}" onclick="setStatusFilter('confirmed')">
          <div class="insight-label">Confirmed &amp; Scheduled</div>
          <div class="insight-value" style="color:#059669;">${confirmed}</div>
        </div>
        <div class="insight-card completed ${activeStatusFilter === 'completed' ? 'active-kpi' : ''}" onclick="setStatusFilter('completed')">
          <div class="insight-label">Completed Sessions</div>
          <div class="insight-value" style="color:#475569;">${completed}</div>
        </div>
        <div class="insight-card rejected ${activeStatusFilter === 'rejected' ? 'active-kpi' : ''}" onclick="setStatusFilter('rejected')">
          <div class="insight-label">Disapproved / Rejected</div>
          <div class="insight-value" style="color:#dc2626;">${rejected}</div>
        </div>
      `;
    }

    function renderFilterPills() {
      const wrap = document.getElementById('filter-pills-wrap');
      const counts = {
        'all': records.length,
        'pending': records.filter(r => (r.status || '').toLowerCase() === 'pending').length,
        'under_review': records.filter(r => (r.status || '').toLowerCase() === 'under_review').length,
        'confirmed': records.filter(r => ['confirmed', 'approved'].includes((r.status || '').toLowerCase())).length,
        'completed': records.filter(r => (r.status || '').toLowerCase() === 'completed').length,
        'rejected': records.filter(r => (r.status || '').toLowerCase() === 'rejected').length
      };

      const pills = [
        { key: 'all', label: 'All Requests' },
        { key: 'pending', label: 'Pending Review' },
        { key: 'under_review', label: 'Under Review' },
        { key: 'confirmed', label: 'Confirmed' },
        { key: 'completed', label: 'Completed' },
        { key: 'rejected', label: 'Disapproved' }
      ];

      wrap.innerHTML = pills.map(p => `
        <button type="button" class="status-pill ${activeStatusFilter === p.key ? 'active' : ''}" onclick="setStatusFilter('${p.key}')">
          <span>${p.label}</span>
          <span class="pill-count">${counts[p.key] || 0}</span>
        </button>
      `).join('');
    }

    function renderCategoryDropdown() {
      const select = document.getElementById('category-filter');
      select.innerHTML = concernCategories.map(cat => `
        <option value="${cat}" ${activeCategoryFilter === cat ? 'selected' : ''}>${cat}</option>
      `).join('');
    }

    function setStatusFilter(st) {
      activeStatusFilter = st;
      renderInsights();
      renderFilterPills();
      applyFilters();
    }

    function resetAllFilters() {
      activeStatusFilter = 'all';
      activeCategoryFilter = 'All Concerns';
      document.getElementById('category-filter').value = 'All Concerns';
      document.getElementById('mode-filter').value = 'all';
      document.getElementById('search-input').value = '';
      renderInsights();
      renderFilterPills();
      applyFilters();
    }

    function getStatusBadge(st) {
      st = (st || 'pending').toLowerCase();
      const map = {
        'pending': { label: 'Pending', cls: 'badge-pending' },
        'under_review': { label: 'Under Review', cls: 'badge-under_review' },
        'confirmed': { label: 'Confirmed', cls: 'badge-confirmed' },
        'approved': { label: 'Confirmed', cls: 'badge-confirmed' },
        'completed': { label: 'Completed', cls: 'badge-completed' },
        'rejected': { label: 'Disapproved', cls: 'badge-rejected' },
        'cancelled': { label: 'Cancelled', cls: 'badge-cancelled' }
      };
      const item = map[st] || { label: st.toUpperCase(), cls: 'badge-pending' };
      return `<span class="badge-status ${item.cls}"><span class="badge-dot"></span>${item.label}</span>`;
    }

    function applyFilters() {
      const tbody = document.getElementById('counseling-tbody');
      const cat = document.getElementById('category-filter').value;
      const mode = document.getElementById('mode-filter').value;
      const search = (document.getElementById('search-input').value || '').toLowerCase().trim();

      activeCategoryFilter = cat;

      let filtered = records.filter(r => {
        // Status filter
        const st = (r.status || 'pending').toLowerCase();
        if (activeStatusFilter === 'confirmed') {
          if (!['confirmed', 'approved'].includes(st)) return false;
        } else if (activeStatusFilter !== 'all') {
          if (st !== activeStatusFilter) return false;
        }

        // Category filter
        if (cat !== 'All Concerns' && r.reason !== cat) {
          return false;
        }

        // Session mode filter
        if (mode !== 'all') {
          const rMode = r.final_session_type || r.session_type || 'In-Person';
          if (rMode !== mode) return false;
        }

        // Search query
        if (search) {
          const clientName = (r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`).toLowerCase();
          const refNo = `mc-${String(r.id).padStart(4, '0')}`;
          const contact = (r.contact_number || r.email || '').toLowerCase();
          const address = (r.address || r.current_address || '').toLowerCase();
          const reason = (r.reason || '').toLowerCase();
          const counselor = (r.counselor_full_name || r.counselor_name || '').toLowerCase();

          const matches = clientName.includes(search) ||
                          refNo.includes(search) ||
                          String(r.id) === search ||
                          contact.includes(search) ||
                          address.includes(search) ||
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
            <td colspan="9" style="text-align:center; padding:48px 20px; color:var(--text-muted);">
              <svg viewBox="0 0 24 24" style="width:40px;height:40px;fill:#cbd5e1;margin-bottom:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              <div style="font-weight:700; font-size:0.95rem; color:#475569;">No counseling applications found</div>
              <div style="font-size:0.8rem; margin-top:4px;">Try clearing the search query or adjusting your category/status filters.</div>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = filtered.map(r => {
        const st = (r.status || 'pending').toLowerCase();
        const dateSubmitted = new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const prefSchedule = (r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'Flexible')
                            + (r.preferred_time ? ` (${r.preferred_time})` : '');
        const prefType = r.session_type || 'In-Person';
        
        const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous Brother';
        const contact = r.contact_number || r.email || 'No contact provided';
        const age = r.age ? `${r.age} yrs` : '';
        const civil = r.civil_status || '';
        const address = r.address || r.current_address || '';

        // Counselor assigned info
        const isConfirmed = ['confirmed', 'approved'].includes(st);
        let counselorCell = '<span style="color:#9ca3af; font-size:0.8rem;">Awaiting Assignment</span>';
        if (isConfirmed) {
          const cName = r.counselor_full_name || r.counselor_name || 'Assigned Ustaz';
          const aptDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric' }) : '';
          const aptTime = r.start_time ? ` @ ${r.start_time}` : '';
          counselorCell = `
            <div>
              <strong style="color:var(--primary-male); font-size:0.88rem;">${cName}</strong>
              <div style="font-size:0.75rem; color:#15803d; font-weight:700;">${aptDate}${aptTime}</div>
            </div>
          `;
        } else if (st === 'completed') {
          const cName = r.counselor_full_name || r.counselor_name || 'Ustaz';
          counselorCell = `<div style="font-size:0.84rem; font-weight:600; color:#334155;">${cName} (Concluded)</div>`;
        }

        return `
          <tr>
            <td class="td-id">
              <span class="ref-badge" onclick='viewCounselingDetails(${JSON.stringify(r)})' title="Click to view full case">
                #MC-${String(r.id).padStart(4, '0')}
              </span>
            </td>
            <td>
              <div class="client-cell">
                <div class="client-name">${escapeHtml(clientName)}</div>
                <div class="client-meta">
                  ${age ? `<span class="client-tag">${age}</span>` : ''}
                  ${civil ? `<span class="client-tag">${civil}</span>` : ''}
                  <span>${escapeHtml(contact)}</span>
                </div>
                ${address ? `<div style="font-size:0.72rem; color:#9ca3af; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(address)}">${escapeHtml(address)}</div>` : ''}
              </div>
            </td>
            <td style="font-size:0.84rem; color:#4b5563;">${dateSubmitted}</td>
            <td>
              <div>
                <span class="concern-tag">${escapeHtml(r.reason)}</span>
                ${r.description ? `<div style="font-size:0.75rem; color:#6b7280; margin-top:3px; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(r.description)}">${escapeHtml(r.description)}</div>` : ''}
              </div>
            </td>
            <td>
              <span style="font-size:0.82rem; font-weight:700; color:${prefType === 'Online' ? '#1d4ed8' : '#14532d'}; background:${prefType === 'Online' ? '#eff6ff' : '#f0fdf4'}; padding:3px 8px; border-radius:6px; border:1px solid ${prefType === 'Online' ? '#bfdbfe' : '#bbf7d0'};">
                ${prefType}
              </span>
            </td>
            <td style="font-size:0.84rem; font-weight:600; color:#374151;">${prefSchedule}</td>
            <td>${counselorCell}</td>
            <td>${getStatusBadge(st)}</td>
            <td style="text-align:center;">
              <div class="actions-cell" style="justify-content:center; gap:6px;">
                <button type="button" class="btn-action btn-view" onclick='viewCounselingDetails(${JSON.stringify(r)})' title="Review and process application">
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  Review Case
                </button>
                ${(isConfirmed || st === 'completed') ? `
                  <button type="button" class="btn-action" style="background:#f0fdf4; border-color:#bbf7d0; color:#15803d; padding:6px 10px;" onclick='openPrintPassModal(${JSON.stringify(r)})' title="Print Appointment Slip">
                    🖨️ Pass
                  </button>
                ` : ''}
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
      const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous Brother';
      document.getElementById('modal-c-name').innerText = clientName;
      
      const contactInfo = [
        r.contact_number || r.email || 'No phone registered',
        r.civil_status ? `Civil Status: ${r.civil_status}` : '',
        r.age ? `${r.age} yrs old` : ''
      ].filter(Boolean).join(' • ');
      
      document.getElementById('modal-c-contact').innerText = contactInfo;
      document.getElementById('modal-c-address').innerText = (r.address || r.current_address) ? `Address: ${r.address || r.current_address}` : 'No address provided';

      // Concern details
      document.getElementById('modal-c-reason').innerText = r.reason || 'General Islamic Guidance';
      const descEl = document.getElementById('modal-c-desc');
      if (r.description && r.description.trim() !== '') {
        descEl.innerText = r.description;
      } else {
        descEl.innerText = 'No additional detailed remarks provided by the applicant.';
      }

      // Preferred Schedule
      const prefDateText = r.preferred_date ? new Date(r.preferred_date).toLocaleDateString('en-US', { weekday:'short', month:'long', day:'numeric', year:'numeric' }) : 'Flexible Date';
      document.getElementById('modal-c-pref-sched').innerText = prefDateText + (r.preferred_time ? ` — ${r.preferred_time}` : '');
      document.getElementById('modal-c-pref-type').innerText = (r.session_type || 'In-Person') + ' Consultation';
      document.getElementById('modal-c-ref').innerText = `#MC-${String(r.id).padStart(4, '0')}`;
      document.getElementById('modal-c-submitted').innerText = new Date(r.created_at).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'numeric' });

      // Status badge
      const st = (r.status || 'pending').toLowerCase();
      document.getElementById('modal-c-badge-wrap').innerHTML = getStatusBadge(st);

      // Confirmed appointment details
      const confBox = document.getElementById('modal-c-confirmed-box');
      const printBtn = document.getElementById('btn-print-slip');
      if (['confirmed', 'approved', 'completed'].includes(st)) {
        confBox.style.display = 'block';
        if (printBtn) printBtn.style.display = 'inline-block';
        document.getElementById('modal-c-confirmed-counselor').innerText = r.counselor_full_name || r.counselor_name || 'Assigned Counselor';
        const aDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'Date Scheduled';
        const aTime = (r.start_time ? r.start_time : '') + (r.end_time ? ` – ${r.end_time}` : '');
        document.getElementById('modal-c-confirmed-datetime').innerText = `${aDate} (${aTime || 'Scheduled Time'})`;

        const locText = (r.final_session_type === 'Online') 
          ? `Online Google Meet • Link: ${r.meeting_link || 'Link provided in counseling pass'}`
          : (r.appointment_location || 'ISCAG Center, 2nd Floor Da\'wah Counseling Office Room 204');
        document.getElementById('modal-c-confirmed-location').innerText = locText;

        const notesWrap = document.getElementById('modal-c-notes-wrap');
        if (r.counselor_notes) {
          notesWrap.style.display = 'block';
          document.getElementById('modal-c-confirmed-notes').innerText = r.counselor_notes;
        } else {
          notesWrap.style.display = 'none';
        }
      } else {
        confBox.style.display = 'none';
        if (printBtn) printBtn.style.display = 'none';
      }

      // Rejection notice
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
          <button type="button" class="btn-action btn-view" style="color:var(--primary-male); border-color:var(--primary-male);" onclick="markUnderReview(${r.id})">
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
      } else if (['confirmed', 'approved'].includes(st)) {
        actGroup.innerHTML = `
          <button type="button" class="btn-action btn-approve" onclick="showActionPanel('panel-complete')">
            ✓ Mark as Completed
          </button>
          <button type="button" class="btn-action btn-view" onclick="showActionPanel('panel-accept-schedule')">
            Reschedule / Update Session
          </button>
          <button type="button" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="showActionPanel('panel-reject')">
            Cancel Session
          </button>
        `;
      } else if (st === 'completed') {
        actGroup.innerHTML = `
          <span style="font-size:0.85rem; color:#15803d; font-weight:700; align-self:center;">✓ Consultation Successfully Concluded</span>
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
          document.getElementById('conflict-warning-msg').innerText = `Ustaz already has a confirmed session (${d.reason}) on ${d.appointment_date} at ${d.start_time} - ${d.end_time || '1 hour'}. Please adjust date, time slot, or counselor.`;
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
      const refNo = `#MC-${String(r.id).padStart(4, '0')}`;
      const clientName = r.full_name || r.client_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Brother';
      const aDate = r.appointment_date ? new Date(r.appointment_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : 'Confirmed Date';
      const aTime = (r.start_time ? r.start_time : '') + (r.end_time ? ` – ${r.end_time}` : '');
      const mode = r.final_session_type || r.session_type || 'In-Person';

      document.getElementById('slip-ref-no').innerText = refNo;
      document.getElementById('slip-name').innerText = clientName;
      document.getElementById('slip-contact').innerText = [r.contact_number, r.email].filter(Boolean).join(' • ');
      document.getElementById('slip-schedule').innerText = `${aDate} at ${aTime || '10:00 AM'}`;
      document.getElementById('slip-mode').innerText = mode;
      document.getElementById('slip-counselor').innerText = r.counselor_full_name || r.counselor_name || 'Ustaz Norman Ungasin';
      document.getElementById('slip-counselor-role').innerText = r.counselor_designation || r.counselor_role || 'Senior Islamic Scholar & Counselor';

      const locText = (mode === 'Online') 
        ? `Online Google Meet Consultation • Link: ${r.meeting_link || 'Link sent to email'}\nInstructions: ${r.meeting_instructions || 'Ensure working audio 5 mins prior.'}`
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
    renderFilterPills();
    renderCategoryDropdown();
    applyFilters();
  </script>
</body>
</html>
