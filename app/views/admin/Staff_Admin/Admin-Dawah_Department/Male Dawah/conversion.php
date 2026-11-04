<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female', 'Staff_Damayan', 'Staff_Tenant']);
$dawah_type = $dawah_type ?? 'male';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — <?= $dawah_type === 'female' ? "Female" : "Male" ?> Conversion &amp; Shahadah Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* ── Insights Ribbon ── */
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
    .insight-card.approved::after { background: var(--success); }
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

    /* ── Tab Navigation ── */
    .tab-nav {
      display: flex; gap: 8px; border-bottom: 2px solid var(--border);
      margin-bottom: 24px; padding-bottom: 0px;
    }
    .tab-btn {
      padding: 10px 18px; font-size: 0.88rem; font-weight: 700;
      color: var(--text-muted); background: none; border: none;
      border-bottom: 3px solid transparent; cursor: pointer;
      display: inline-flex; align-items: center; gap: 8px;
      transition: all 0.2s ease; border-radius: 6px 6px 0 0;
    }
    .tab-btn:hover { color: var(--primary); background: rgba(0,0,0,0.02); }
    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-count {
      padding: 2px 7px; border-radius: 12px; font-size: 0.72rem;
      font-weight: 800; background: #e5e7eb; color: #4b5563;
    }
    .tab-count.pending { background: rgba(199, 154, 43, 0.15); color: #b08d2e; }
    .tab-count.approved { background: rgba(47, 138, 96, 0.15); color: #2f8a60; }
    .tab-count.rejected { background: rgba(139, 46, 46, 0.15); color: #8b2e2e; }

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
    .badge-status.approved, .badge-status.badge-approved { 
      background: rgba(47, 138, 96, 0.1); color: #2f8a60; 
    }
    .badge-status.rejected, .badge-status.badge-rejected { 
      background: rgba(139, 46, 46, 0.1); color: #8b2e2e; 
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* ── Action Buttons ── */
    .actions-cell { display: flex; align-items: center; justify-content: center; }
    .btn-circle {
      width: 32px; height: 32px; border-radius: 8px;
      display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--border, #e2e8f0); background: #f8fafc;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
      color: #475569;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: currentColor; }
    .btn-circle:hover, .btn-circle.active { 
      background: #e2e8f0; 
      border-color: #cbd5e1;
      color: var(--primary-dark, #0f172a);
      box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .btn-circle.dots { border-radius: 8px; }

    /* Floating 3-Dots Action Dropdown Menu */
    .conversion-action-menu {
      position: fixed;
      display: none;
      min-width: 175px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.14), 0 8px 10px -6px rgba(0,0,0,0.08);
      padding: 6px;
      z-index: 999999;
      animation: convMenuFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes convMenuFadeIn {
      from { opacity: 0; transform: scale(0.96) translateY(-4px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .action-dropdown-item {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 9px 12px;
      font-size: 0.84rem;
      font-weight: 600;
      color: #334155;
      background: transparent;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-align: left;
      transition: all 0.15s ease;
      font-family: inherit;
      box-sizing: border-box;
    }
    .action-dropdown-item svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      fill: currentColor;
    }
    .action-dropdown-item:hover {
      background: #f1f5f9;
      color: #0f172a;
    }
    .action-dropdown-item.schedule { color: #6d28d9; }
    .action-dropdown-item.schedule:hover { background: #f5f3ff; color: #5b21b6; }
    .action-dropdown-item.view { color: #1d4ed8; }
    .action-dropdown-item.view:hover { background: #eff6ff; color: #1e40af; }
    .action-dropdown-item.delete { color: #dc2626; }
    .action-dropdown-item.delete:hover { background: #fef2f2; color: #b91c1c; }
    .action-dropdown-divider {
      height: 1px;
      background: #e2e8f0;
      margin: 4px 0;
    }

    /* Modal Backdrop & Cards */
    .conversion-modal-backdrop {
      position: fixed; inset: 0; background: rgba(15,30,22,0.65);
      backdrop-filter: blur(5px); z-index: 99999; display: none;
      align-items: center; justify-content: center; padding: 16px;
    }
    .conversion-modal-card {
      background: white; border-radius: 16px; width: 100%;
      max-width: 650px; box-shadow: 0 25px 70px rgba(0,0,0,0.3);
      overflow: hidden; border: 1px solid rgba(0,0,0,0.08);
      max-height: 90vh; display: flex; flex-direction: column;
    }
    .conversion-modal-header {
      padding: 18px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center;
      background: #fafafa;
    }
    .conversion-modal-close {
      background: none; border: none; font-size: 24px; line-height: 1;
      color: var(--text-muted); cursor: pointer;
    }
    .conversion-modal-body {
      padding: 22px 24px; overflow-y: auto; flex: 1;
      display: flex; flex-direction: column; gap: 14px;
    }
    .conversion-modal-footer {
      padding: 14px 24px; border-top: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center;
      background: #f8fafc;
    }
    .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .info-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
    .info-box {
      border: 1px solid var(--border); border-radius: 8px; padding: 12px;
      background: #fafafa;
    }
    .info-box.highlight {
      background: #f0fdf4; border-color: #bbf7d0;
    }
    .info-box.schedule {
      background: #faf5ff; border-color: #e9d5ff;
    }
    .info-box-lbl {
      font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
      color: var(--text-muted); margin-bottom: 3px; letter-spacing: 0.04em;
    }
    .info-box-val { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }
    .info-box-val.gold { color: var(--primary-dark); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'conversion';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title"><?= $dawah_type === 'female' ? "Female" : "Male" ?> Islamic Conversion &amp; Shahadah</div>
            <div class="top-bar-subtitle">Da'wah Department — Manage conversion applications, ceremony schedules, and certificates</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Islamic Conversion</span>
          <span class="sep">›</span>
          <span class="current">Conversion Management</span>
        </div>

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered by JS -->
        </div>

        <!-- Tab Nav: Default to All Records so cases are never hidden -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="setFilter('all', this)">
            All Records <span class="tab-count" id="tab-count-all">0</span>
          </button>
          <button class="tab-btn" onclick="setFilter('pending', this)">
            Pending <span class="tab-count pending" id="tab-count-pending">0</span>
          </button>
          <button class="tab-btn" onclick="setFilter('approved', this)">
            Accepted / Approved <span class="tab-count approved" id="tab-count-approved">0</span>
          </button>
          <button class="tab-btn" onclick="setFilter('rejected', this)">
            Rejected <span class="tab-count rejected" id="tab-count-rejected">0</span>
          </button>
        </div>

        <!-- Section Card Table -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              Conversion Applications &amp; Records
            </h6>
            <span class="results-badge" id="results-count-badge">Loading records...</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Record ID</th>
                    <th>Legal Full Name</th>
                    <th>Adopted Name</th>
                    <th>Ceremony Schedule</th>
                    <th>Residence</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
                  </tr>
                </thead>
                <tbody id="conversion-tbody">
                  <!-- Rendered by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ 1. Set / Update Schedule Ceremony Modal ═══ -->
  <div id="conversion-schedule-modal" class="conversion-modal-backdrop" onclick="if(event.target===this) closeScheduleModal()">
    <div class="conversion-modal-card" style="max-width:600px;">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #7c3aed);"></div>
      <div class="conversion-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.18rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#7c3aed;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
          Set Conversion Ceremony Schedule
        </h5>
        <button type="button" onclick="closeScheduleModal()" class="conversion-modal-close">&times;</button>
      </div>

      <form id="form-conversion-schedule" onsubmit="submitScheduleConversion(event)" style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <input type="hidden" id="cs-request-id" name="id" value="" />

        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Applicant</span>
            <div id="cs-applicant-name" style="font-weight:800; font-size:1.02rem; color:#14532D; margin-top:2px;">—</div>
          </div>
          <div style="text-align:right;">
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Record ID</span>
            <div id="cs-ref-no" style="font-weight:800; font-size:0.95rem; color:#14532D; margin-top:2px;">#CR-0000</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              Ceremony Date <span style="color:#ef4444;">*</span>
            </label>
            <input type="date" id="cs-date" required style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" />
          </div>
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              Ceremony Time <span style="color:#ef4444;">*</span>
            </label>
            <input type="time" id="cs-time" required value="10:00" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" />
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Ceremony Venue / Location
          </label>
          <input type="text" id="cs-venue" value="ISCAG Main Mosque, Da'wah Ceremony Hall" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem;" />
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Presiding Ustaz / Officiant
          </label>
          <input type="text" id="cs-officiant" value="Da'wah Division Ustaz / Officiant" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem;" />
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Application Status
          </label>
          <select id="cs-status" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600; background:#fff;">
            <option value="approved">Approved &amp; Scheduled</option>
            <option value="pending">Keep as Pending Review</option>
          </select>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Instructions / Guidance for Applicant
          </label>
          <textarea id="cs-notes" rows="2" placeholder="e.g. Please bring two Muslim adult witnesses and a valid government ID. May Allah bless you." style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.88rem; font-family:inherit;"></textarea>
        </div>

        <div style="padding:14px 0 0; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px;">
          <button type="button" onclick="closeScheduleModal()" style="padding:9px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
          <button type="submit" id="cs-submit-btn" class="btn-topbar primary" style="padding:9px 22px; font-weight:700;">
            Confirm &amp; Save Schedule
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══ 2. Conversion Details (View) Modal ═══ -->
  <div id="conversion-modal" class="conversion-modal-backdrop" onclick="if(event.target===this) closeConversionModal()">
    <div class="conversion-modal-card">
      <div class="conversion-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
          Shahadah &amp; Conversion Application Details
        </h5>
        <button type="button" onclick="closeConversionModal()" class="conversion-modal-close">&times;</button>
      </div>
      <div class="conversion-modal-body">
        <div class="info-grid-2">
          <div class="info-box highlight">
            <div class="info-box-lbl">Legal Full Name</div>
            <div id="modal-legal-name" class="info-box-val">—</div>
          </div>
          <div class="info-box highlight">
            <div class="info-box-lbl">Adopted Muslim Name</div>
            <div id="modal-adopted-name" class="info-box-val gold">—</div>
          </div>
        </div>

        <!-- Schedule details -->
        <div class="info-box schedule">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <div class="info-box-lbl" style="color:#6b21a8; display:flex; align-items:center; gap:6px;">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
              Ceremony Schedule &amp; Location
            </div>
            <button type="button" onclick="openScheduleFromDetails()" style="background:none; border:none; color:#7c3aed; font-size:0.78rem; font-weight:700; cursor:pointer; text-decoration:underline;">
              Edit Schedule
            </button>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.88rem;">
            <div><strong style="color:#581c87;">Date &amp; Time:</strong> <span id="modal-conv-date-full" style="color:#3b0764; font-weight:600;">—</span></div>
            <div><strong style="color:#581c87;">Officiant:</strong> <span id="modal-officiant" style="color:#3b0764;">—</span></div>
            <div style="grid-column:span 2;"><strong style="color:#581c87;">Venue:</strong> <span id="modal-venue" style="color:#3b0764;">—</span></div>
          </div>
        </div>

        <div class="info-grid-3">
          <div class="info-box">
            <div class="info-box-lbl">Sex &amp; Age</div>
            <div id="modal-sex-age" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Civil Status</div>
            <div id="modal-civil" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Former Religion</div>
            <div id="modal-former-rel" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Place of Birth</div>
            <div id="modal-pob" class="info-box-val" style="font-size:0.85rem;">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Current Residence</div>
            <div id="modal-residence" class="info-box-val" style="font-size:0.85rem;">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Father's Name &amp; Religion</div>
            <div id="modal-father" class="info-box-val" style="font-size:0.85rem;">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Mother's Name &amp; Religion</div>
            <div id="modal-mother" class="info-box-val" style="font-size:0.85rem;">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Witness 1</div>
            <div id="modal-w1" class="info-box-val" style="font-size:0.82rem;">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Witness 2</div>
            <div id="modal-w2" class="info-box-val" style="font-size:0.82rem;">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Conversion Date</div>
            <div id="modal-conv-date" class="info-box-val gold">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Status</div>
            <div id="modal-status" class="info-box-val">—</div>
          </div>
        </div>
      </div>
      <div class="conversion-modal-footer">
        <button type="button" id="modal-btn-delete" class="btn-topbar danger" style="display:flex; align-items:center; gap:6px;">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          Delete Record
        </button>
        <div style="display:flex; gap:10px;">
          <button type="button" onclick="closeConversionModal()" class="btn-topbar">Close</button>
          <button type="button" id="modal-btn-schedule" class="btn-topbar" style="background:#7c3aed; color:#fff; display:flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Set Schedule
          </button>
          <button type="button" id="modal-btn-reject" class="btn-topbar danger" style="display:none;">Reject</button>
          <button type="button" id="modal-btn-approve" class="btn-topbar primary" style="display:none;">Accept &amp; Issue Certificate</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ 3. Floating 3-Dots Action Dropdown Menu ═══ -->
  <div id="conversion-req-action-menu" class="conversion-action-menu">
    <button type="button" class="action-dropdown-item schedule" onclick="triggerReqAction('schedule')">
      <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
      <span>Set Schedule</span>
    </button>
    <button type="button" class="action-dropdown-item view" onclick="triggerReqAction('view')">
      <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
      <span>View</span>
    </button>
    <div class="action-dropdown-divider"></div>
    <button type="button" class="action-dropdown-item delete" onclick="triggerReqAction('delete')">
      <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
      <span>Delete</span>
    </button>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    let applications = <?= json_encode($applications ?? ($records ?? [])) ?>;
    const recordsMap = {};
    applications.forEach(a => { if (a && a.id) recordsMap[a.id] = a; });

    let currentFilter = 'all';
    let currentViewingId = null;

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = applications.length;
      const pending = applications.filter(a => ['pending', 'under_review'].includes((a.status || '').toLowerCase())).length;
      const approved = applications.filter(a => ['accepted', 'approved', 'confirmed'].includes((a.status || '').toLowerCase())).length;
      const rejected = applications.filter(a => ['rejected', 'disapproved', 'cancelled'].includes((a.status || '').toLowerCase())).length;

      container.innerHTML = `
        <div class="insight-card total" onclick="setFilter('all', document.querySelector('.tab-btn:nth-child(1)'))">
          <div class="insight-label">Total Conversions</div>
          <div class="insight-value info">${total}</div>
          <svg viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
        </div>
        <div class="insight-card pending" onclick="setFilter('pending', document.querySelector('.tab-btn:nth-child(2)'))">
          <div class="insight-label">Pending Review</div>
          <div class="insight-value warning">${pending}</div>
          <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
        </div>
        <div class="insight-card approved" onclick="setFilter('approved', document.querySelector('.tab-btn:nth-child(3)'))">
          <div class="insight-label">Accepted / Approved</div>
          <div class="insight-value success">${approved}</div>
          <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
        </div>
        <div class="insight-card rejected" onclick="setFilter('rejected', document.querySelector('.tab-btn:nth-child(4)'))">
          <div class="insight-label">Rejected</div>
          <div class="insight-value danger">${rejected}</div>
          <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" /></svg>
        </div>
      `;

      document.getElementById('tab-count-all').innerText = total;
      document.getElementById('tab-count-pending').innerText = pending;
      document.getElementById('tab-count-approved').innerText = approved;
      document.getElementById('tab-count-rejected').innerText = rejected;
    }

    function setFilter(val, btn) {
      currentFilter = val;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderTable();
    }

    function renderTable() {
      const tbody = document.getElementById('conversion-tbody');
      
      let filtered = applications;
      if (currentFilter === 'pending') {
        filtered = filtered.filter(a => ['pending', 'under_review'].includes((a.status || '').toLowerCase()));
      } else if (currentFilter === 'approved') {
        filtered = filtered.filter(a => ['accepted', 'approved', 'confirmed'].includes((a.status || '').toLowerCase()));
      } else if (currentFilter === 'rejected') {
        filtered = filtered.filter(a => ['rejected', 'disapproved', 'cancelled'].includes((a.status || '').toLowerCase()));
      }

      document.getElementById('results-count-badge').innerText = `Showing ${filtered.length} of ${applications.length} records`;

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No conversion records found for this filter.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(app => {
        const st = (app.status || 'pending').toLowerCase();
        let sc = 'pending';
        let displayStatus = 'Pending';
        if (['accepted', 'approved', 'confirmed'].includes(st)) {
          sc = 'approved';
          displayStatus = 'Accepted';
        } else if (['rejected', 'disapproved', 'cancelled'].includes(st)) {
          sc = 'rejected';
          displayStatus = 'Rejected';
        }

        const legalName = `${app.fname || ''} ${app.mname ? app.mname + ' ' : ''}${app.lname || ''}`.trim() || '—';
        const datePart = app.conversion_date ? new Date(app.conversion_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
        const timePart = app.preferred_time ? ` (${app.preferred_time})` : '';
        const schedDisplay = datePart ? `${datePart}${timePart}` : '<span style="color:#9ca3af;font-style:italic;">Not scheduled</span>';

        return `
          <tr id="row-${app.id}">
            <td class="td-id">#CR-${String(app.id).padStart(4, '0')}</td>
            <td style="font-weight:700; color:#111827;">${legalName}</td>
            <td style="font-weight:700; color:var(--primary);">${app.adopted_name || '—'}</td>
            <td style="color:#374151; font-weight:600;">${schedDisplay}</td>
            <td>${app.residence || '—'}</td>
            <td><span class="badge-status ${sc}"><span class="status-dot"></span>${displayStatus}</span></td>
            <td>
              <div class="actions-cell">
                <button type="button" class="btn-circle dots" onclick="toggleReqMenu(event, ${app.id})" title="Actions" aria-label="Actions" id="action-btn-${app.id}">
                  <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function viewConversionDetailsById(id) {
      const app = applications.find(a => Number(a.id) === Number(id)) || recordsMap[id];
      if (app) viewConversionDetails(app);
    }

    function viewConversionDetails(app) {
      currentViewingId = app.id;
      const legalName = `${app.fname || ''} ${app.mname ? app.mname + ' ' : ''}${app.lname || ''}`.trim() || '—';
      document.getElementById('modal-legal-name').innerText = legalName;
      document.getElementById('modal-adopted-name').innerText = app.adopted_name || '—';
      document.getElementById('modal-sex-age').innerText = `${app.sex || 'Male'}, ${app.age || '—'} yrs old`;
      document.getElementById('modal-civil').innerText = app.civil_status || '—';
      document.getElementById('modal-former-rel').innerText = app.former_religion || '—';
      document.getElementById('modal-pob').innerText = app.pob || '—';
      document.getElementById('modal-residence').innerText = app.residence || '—';
      document.getElementById('modal-father').innerText = `${app.father_name || '—'} (${app.father_religion || '—'})`;
      document.getElementById('modal-mother').innerText = `${app.mother_name || '—'} (${app.mother_religion || '—'})`;
      document.getElementById('modal-w1').innerText = `${app.witness1_name || '—'} ${app.witness1_address ? '— ' + app.witness1_address : ''}`;
      document.getElementById('modal-w2').innerText = `${app.witness2_name || '—'} ${app.witness2_address ? '— ' + app.witness2_address : ''}`;
      
      const convDate = app.conversion_date ? new Date(app.conversion_date).toLocaleDateString('en-US', { weekday:'long', month: 'long', day: 'numeric', year: 'numeric' }) : '—';
      document.getElementById('modal-conv-date').innerText = convDate;

      // Schedule Box
      const timeFmt = app.preferred_time ? ` at ${app.preferred_time}` : '';
      document.getElementById('modal-conv-date-full').innerText = app.conversion_date ? `${convDate}${timeFmt}` : 'Not scheduled yet';
      document.getElementById('modal-venue').innerText = app.venue || 'ISCAG Main Mosque, Da\'wah Ceremony Hall';
      document.getElementById('modal-officiant').innerText = app.officiant || 'Da\'wah Division Ustaz / Officiant';

      const st = (app.status || 'pending').toLowerCase();
      let statusLabel = 'PENDING';
      if (['accepted', 'approved', 'confirmed'].includes(st)) statusLabel = 'ACCEPTED';
      if (['rejected', 'disapproved', 'cancelled'].includes(st)) statusLabel = 'REJECTED';
      document.getElementById('modal-status').innerText = statusLabel;

      const btnApprove = document.getElementById('modal-btn-approve');
      const btnReject = document.getElementById('modal-btn-reject');
      const btnDelete = document.getElementById('modal-btn-delete');
      const isPending = ['pending', 'under_review'].includes(st);

      if (isPending) {
        btnApprove.style.display = 'inline-flex';
        btnApprove.onclick = () => { closeConversionModal(); handleConversionAction(app.id, 'approve'); };
        btnReject.style.display = 'inline-flex';
        btnReject.onclick = () => { closeConversionModal(); handleConversionAction(app.id, 'reject'); };
      } else {
        btnApprove.style.display = 'none';
        btnReject.style.display = 'none';
      }

      btnDelete.onclick = () => {
        closeConversionModal();
        deleteConversionRecord(app.id);
      };

      document.getElementById('modal-btn-schedule').onclick = () => {
        closeConversionModal();
        openScheduleModal(app.id);
      };

      document.getElementById('conversion-modal').style.display = 'flex';
    }

    function closeConversionModal() {
      document.getElementById('conversion-modal').style.display = 'none';
    }

    function openScheduleFromDetails() {
      if (currentViewingId) {
        closeConversionModal();
        openScheduleModal(currentViewingId);
      }
    }

    /* ── Schedule Modal Logic ── */
    function openScheduleModal(id) {
      const r = applications.find(a => Number(a.id) === Number(id)) || recordsMap[id];
      if (!r) return;

      document.getElementById('cs-request-id').value = id;
      const reqId = '#CR-' + String(r.id).padStart(4, '0');
      const applicantName = `${r.fname || ''} ${r.mname ? r.mname + ' ' : ''}${r.lname || ''}`.trim() || (`${r.first_name || ''} ${r.last_name || ''}`.trim()) || 'Applicant #' + id;
      document.getElementById('cs-applicant-name').textContent = applicantName;
      document.getElementById('cs-ref-no').textContent = reqId;

      if (r.conversion_date) {
        document.getElementById('cs-date').value = r.conversion_date;
      } else {
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dt = String(d.getDate()).padStart(2, '0');
        document.getElementById('cs-date').value = `${y}-${m}-${dt}`;
      }

      if (r.preferred_time) {
        const m = r.preferred_time.match(/(\d{1,2}):(\d{2})/);
        if (m) {
          let hrs = parseInt(m[1]);
          if (r.preferred_time.toLowerCase().includes('pm') && hrs < 12) hrs += 12;
          if (r.preferred_time.toLowerCase().includes('am') && hrs === 12) hrs = 0;
          document.getElementById('cs-time').value = `${String(hrs).padStart(2, '0')}:${m[2]}`;
        } else {
          document.getElementById('cs-time').value = '10:00';
        }
      } else {
        document.getElementById('cs-time').value = '10:00';
      }

      document.getElementById('cs-venue').value = r.venue || 'ISCAG Main Mosque, Da\'wah Ceremony Hall';
      document.getElementById('cs-officiant').value = r.officiant || 'Da\'wah Division Ustaz / Officiant';
      document.getElementById('cs-notes').value = r.notes || '';
      document.getElementById('cs-status').value = 'approved';

      document.getElementById('conversion-schedule-modal').style.display = 'flex';
    }

    function closeScheduleModal() {
      const m = document.getElementById('conversion-schedule-modal');
      if (m) m.style.display = 'none';
    }

    async function submitScheduleConversion(e) {
      e.preventDefault();
      const id = document.getElementById('cs-request-id').value;
      const date = document.getElementById('cs-date').value;
      const time = document.getElementById('cs-time').value;
      const venue = document.getElementById('cs-venue').value;
      const officiant = document.getElementById('cs-officiant').value;
      const notes = document.getElementById('cs-notes').value;
      const status = document.getElementById('cs-status').value;

      if (!id || !date || !time) {
        alert('Please provide ceremony date and time.');
        return;
      }

      const [h, m] = time.split(':');
      let hr = parseInt(h);
      const ampm = hr >= 12 ? 'PM' : 'AM';
      hr = hr % 12 || 12;
      const timeFmt = `${hr}:${m} ${ampm}`;

      const btn = document.getElementById('cs-submit-btn');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Saving Schedule...';
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/conversion/schedule") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: id,
            conversion_date: date,
            preferred_time: timeFmt,
            venue: venue,
            officiant: officiant,
            notes: notes,
            status: status
          })
        });

        const res = await response.json();
        if (res.success) {
          const rec = applications.find(a => Number(a.id) === Number(id));
          if (rec) {
            rec.conversion_date = date;
            rec.preferred_time = timeFmt;
            rec.venue = venue;
            rec.officiant = officiant;
            rec.notes = notes;
            rec.status = status;
          }
          if (recordsMap[id]) {
            recordsMap[id].conversion_date = date;
            recordsMap[id].preferred_time = timeFmt;
            recordsMap[id].venue = venue;
            recordsMap[id].officiant = officiant;
            recordsMap[id].notes = notes;
            recordsMap[id].status = status;
          }

          if (typeof showAlert === 'function') {
            showAlert('Schedule Saved', res.message || 'Conversion ceremony schedule saved successfully.', 'success');
          } else {
            alert(res.message || 'Conversion ceremony schedule saved successfully.');
          }
          closeScheduleModal();
          renderInsights();
          renderTable();
          setTimeout(() => location.reload(), 900);
        } else {
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Confirm & Save Schedule';
          }
          alert(res.message || 'Failed to save ceremony schedule.');
        }
      } catch (err) {
        console.error(err);
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Confirm & Save Schedule';
        }
        alert('A network error occurred while saving the schedule.');
      }
    }

    /* ── Delete Conversion Record Handler ── */
    async function deleteConversionRecord(id) {
      const r = applications.find(a => Number(a.id) === Number(id)) || recordsMap[id];
      const reqRef = '#CR-' + String(id).padStart(4, '0');
      const name = r ? (`${r.fname || ''} ${r.lname || ''}`.trim() || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Record') : 'Record';

      const doDelete = async () => {
        try {
          const response = await fetch('<?= url("/admin/dawah/conversion/delete") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
          });

          const res = await response.json();
          if (res.success) {
            if (typeof showAlert === 'function') {
              showAlert('Deleted Successfully', res.message || `Conversion record ${reqRef} has been removed.`, 'success');
            } else {
              alert(res.message || `Conversion record ${reqRef} has been removed.`);
            }

            // Remove locally and re-render
            applications = applications.filter(a => Number(a.id) !== Number(id));
            delete recordsMap[id];
            renderInsights();
            renderTable();
          } else {
            if (typeof showAlert === 'function') {
              showAlert('Deletion Failed', res.message || 'Failed to delete conversion record.', 'error');
            } else {
              alert(res.message || 'Failed to delete conversion record.');
            }
          }
        } catch (err) {
          console.error(err);
          if (typeof showAlert === 'function') {
            showAlert('Network Error', 'A network error occurred while deleting the record.', 'error');
          } else {
            alert('A network error occurred while deleting the record.');
          }
        }
      };

      const title = 'Delete Conversion Record';
      const msg = `Are you sure you want to delete conversion record ${reqRef} for "${name}"?\n\nThis will permanently remove the record from the database.`;

      if (typeof showConfirm === 'function') {
        showConfirm(title, msg, doDelete, 'danger');
      } else {
        if (confirm(`${title}\n\n${msg}`)) {
          doDelete();
        }
      }
    }

    /* ── Accept / Reject Process Request Handler ── */
    async function handleConversionAction(id, action) {
      const title = action === 'approve' ? 'Accept Conversion & Issue Certificate' : 'Reject Conversion Application';
      const message = `Are you sure you want to ${action === 'approve' ? 'accept' : 'reject'} this conversion application? The applicant will be notified immediately.`;

      const runAction = async () => {
        try {
          const endpoint = action === 'approve' ? '<?= url("/admin/dawah/conversion/accept") ?>' : '<?= url("/admin/dawah/conversion/reject") ?>';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const result = await response.json();
          if (result.success) {
            if (typeof showAlert === 'function') {
              showAlert('Action Successful', result.message || `The application has been updated.`, 'success');
            } else {
              alert(result.message || 'Operation successful.');
            }
            setTimeout(() => location.reload(), 900);
          } else {
            if (typeof showAlert === 'function') {
              showAlert('Action Failed', result.message || 'Could not process request.', 'error');
            } else {
              alert(result.message || 'Could not process request.');
            }
          }
        } catch(e) {
          console.error(e);
          alert('A network error occurred.');
        }
      };

      if (typeof showConfirm === 'function') {
        showConfirm(title, message, runAction, action === 'approve' ? 'success' : 'danger');
      } else {
        if (confirm(`${title}\n\n${message}`)) {
          runAction();
        }
      }
    }

    /* ── 3-Dots Action Menu Handlers ── */
    let activeReqId = null;
    let activeReqButton = null;

    function toggleReqMenu(e, id) {
      e.stopPropagation();
      const menu = document.getElementById('conversion-req-action-menu');
      const btn = e.currentTarget;

      if (menu.style.display === 'block' && activeReqId === id) {
        closeReqMenu();
        return;
      }

      activeReqId = id;
      if (activeReqButton) activeReqButton.classList.remove('active');
      activeReqButton = btn;
      activeReqButton.classList.add('active');

      menu.style.display = 'block';

      const rect = btn.getBoundingClientRect();
      const menuWidth = 180;
      const menuHeight = menu.offsetHeight || 150;

      let top = rect.bottom + 6;
      let left = rect.right - menuWidth;

      if (top + menuHeight > window.innerHeight) {
        top = Math.max(10, rect.top - menuHeight - 6);
      }
      if (left < 10) {
        left = Math.max(10, rect.left);
      }

      menu.style.top = `${top}px`;
      menu.style.left = `${left}px`;
    }

    function closeReqMenu() {
      const menu = document.getElementById('conversion-req-action-menu');
      if (menu) menu.style.display = 'none';
      if (activeReqButton) {
        activeReqButton.classList.remove('active');
        activeReqButton = null;
      }
      activeReqId = null;
    }

    function triggerReqAction(action) {
      if (!activeReqId) return;
      const id = activeReqId;
      closeReqMenu();

      if (action === 'schedule') {
        openScheduleModal(id);
      } else if (action === 'view') {
        viewConversionDetailsById(id);
      } else if (action === 'delete') {
        deleteConversionRecord(id);
      }
    }

    document.addEventListener('click', function(e) {
      const menu = document.getElementById('conversion-req-action-menu');
      if (menu && menu.style.display === 'block') {
        if (!menu.contains(e.target) && !e.target.closest('.btn-circle.dots')) {
          closeReqMenu();
        }
      }
    });

    window.addEventListener('scroll', closeReqMenu, true);
    window.addEventListener('resize', closeReqMenu);

    renderInsights();
    renderTable();
  </script>
</body>
</html>