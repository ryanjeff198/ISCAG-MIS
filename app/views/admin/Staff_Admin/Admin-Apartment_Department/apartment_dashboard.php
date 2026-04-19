<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Staff Admin</title>
  <meta name="description" content="Staff Admin dashboard for Apartment department management" />
  <link rel="stylesheet" href="../../../css/admin-shared.css" />
  <style>
    .btn-action.btn-assign:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    .verified-glow {
      border-left: 4px solid var(--success);
    }

    .verified-glow .section-card-header h6 {
      color: var(--primary-dark);
    }

    .empty-state {
      text-align: center;
      padding: 30px 20px;
      color: var(--text-muted);
    }

    .empty-state svg {
      width: 40px;
      height: 40px;
      fill: var(--border);
      margin-bottom: 8px;
    }

    .empty-state h4 {
      font-family: 'Lora', serif;
      font-size: 0.92rem;
      font-weight: 700;
      margin: 0 0 4px;
    }

    .empty-state p {
      font-size: 0.8rem;
      margin: 0;
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!----sidebar---->
    <aside class="sidebar" id="sidebar">
      <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar"><svg viewBox="0 0 24 24">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
        </svg></button>
      <div class="sidebar-header">
        <div class="sidebar-brand">
          <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
          <div class="brand-text"><strong>ISCAG MIS</strong><span>Apartment Staff</span></div>
        </div>
      </div>
      <div class="sidebar-user">
        <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">AK</div>
        <div class="user-info"><strong id="nav-name">Apartment Staff</strong><span>Staff Admin</span></div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-label">Admin</div>
        <a href="apartment_dashboard.html" class="nav-item active" data-tooltip="Dashboard">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
          </svg>
          <span class="nav-item-label">Dashboard</span>
        </a>
        <a href="apartment_profile.html" class="nav-item" data-tooltip="Profile">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
          </svg>
          <span class="nav-item-label">My Profile</span>
        </a>
        <div class="nav-section-label">Management</div>
        <a href="apartments_info.html" class="nav-item" data-tooltip="Apartment Info">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
          </svg>
          <span class="nav-item-label">Apartment Info</span>
        </a>
        <a href="payment.html" class="nav-item" data-tooltip="Billing & Payment">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
          </svg>
          <span class="nav-item-label">Billing & Payment</span>
        </a>
      </nav>
      <div class="sidebar-footer">
        <a href="../../homepage/login.html" class="nav-item" data-tooltip="Logout">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
          </svg>
          <span class="nav-item-label">Logout</span>
        </a>
      </div>
    </aside>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Apartment Management</div>
          <div class="top-bar-subtitle">View units, preview rooms, and manage tenant assignments</div>
        </div>
        <div class="top-bar-actions">

        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Apartment Management</span>
        </div>

        <!-- RESTRICTION BANNER -->
        <div class="restriction-banner">
          <svg viewBox="0 0 24 24">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
          </svg>
          <div>
            <strong>Staff Admin View</strong> — You can view units and update room status. Tenant assignments and
            billing modifications require <strong>MIS Admin</strong> approval.
          </div>
        </div>

        <!-- STATS ROW -->
        <div class="stats-row" id="stats-row">
          <div class="stat-card">
            <div class="stat-icon teal">
              <svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-total">5</div>
              <div class="stat-label">Total Units</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-available">4</div>
              <div class="stat-label">Available Slots</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24">
                <path
                  d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-occupied">1</div>
              <div class="stat-label">Fully Occupied</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-reserved">1</div>
              <div class="stat-label">Reserved</div>
            </div>
          </div>
        </div>

        <!-- UNIT TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path
                  d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zM7 19H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm4 4H9v-2h2v2zm0-4H9V9h2v2zm0-4H9V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2z" />
              </svg>
              All Apartment Units
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);">Data loaded from system</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="units-table">
                <thead>
                  <tr>
                    <th>Unit ID</th>
                    <th>Unit Name</th>
                    <th>Type</th>
                    <th>Price / mo</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="units-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- VERIFIED APPLICATIONS (from MIS Admin) -->
        <div class="section-card verified-glow" style="margin-bottom:24px;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
              </svg>
              Verified Applications — Ready for Assignment
            </h6>
            <span
              style="font-size:0.72rem;color:var(--text-muted);background:rgba(47,138,96,0.1);padding:3px 10px;border-radius:12px;font-weight:600;color:var(--success);"
              id="verified-count-badge">0 verified</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Verified Date</th>
                    <th>Documents</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="verified-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TWO-COLUMN: Applications + Billing -->
        <div class="grid-2">
          <!-- PENDING APPLICATIONS -->
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                </svg>
                Recent Applications
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="apps-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- BILLING (Read-Only) -->
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path
                    d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                </svg>
                Tenant Billing
              </h6>
              <span
                style="font-size:0.72rem;color:var(--text-muted);background:rgba(199,154,43,0.1);padding:3px 10px;border-radius:12px;font-weight:600;">Read-Only</span>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Tenant</th>
                      <th>Amount</th>
                      <th>Due</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="billing-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ═══ ASSIGN ROOM MODAL ═══ -->
  <div class="modal-backdrop" id="assign-modal" style="display:none;">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);">
            <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
          </svg>
          Assign Room
        </h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Application</label>
          <p id="assign-ref" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Applicant</label>
          <p id="assign-name" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Select Unit *</label>
          <select class="form-control" id="assign-room-select" style="appearance:auto;"></select>
        </div>
        <div id="assign-preview"
          style="display:none;margin-top:12px;padding:12px 16px;background:rgba(47,138,96,0.06);border:1px solid rgba(47,138,96,0.15);border-radius:8px;">
          <div
            style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);margin-bottom:6px;">
            Billing Preview</div>
          <div style="display:flex;justify-content:space-between;font-size:0.88rem;">
            <span>Monthly Rent:</span>
            <strong id="assign-price" style="color:var(--primary-dark);"></strong>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('assign-modal')">Cancel</button>
        <button class="btn-topbar primary" id="assign-confirm-btn">Assign & Generate Billing</button>
      </div>
    </div>
  </div>

  <!-- ═══ MANAGE UNIT MODAL ═══ -->
  <div class="modal-backdrop" id="manage-modal" style="display:none;">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);">
            <path
              d="M19.43 12.98c.04-.32.07-.64.07-.98 0-.34-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98 0 .33.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z" />
          </svg>
          Manage Unit Slots
        </h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Unit Name</label>
          <p id="manage-unit-name" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <input type="hidden" id="manage-unit-id" />
        <div class="form-group">
          <label class="form-label">Available Slots *</label>
          <input type="number" class="form-control" id="manage-unit-slots" min="0" required />
        </div>
        <div class="form-group">
          <label class="form-label">Status *</label>
          <select class="form-control" id="manage-unit-status" style="appearance:auto;">
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
            <option value="reserved">Reserved</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('manage-modal')">Cancel</button>
        <button class="btn-topbar primary" id="manage-save-btn">Save Changes</button>
      </div>
    </div>
  </div>

  <script src="../../../JS/room-preview.js"></script>
  <script src="../../../JS/admin-shared.js"></script>
  <script>
    initAdminData();
    initReportsData();
    setCurrentRole(ROLES.STAFF_ADMIN);

    function refreshStats() {
      const apts = getApartments();
      document.getElementById('stat-total').textContent = apts.length;
      document.getElementById('stat-available').textContent = apts.reduce((s, a) => s + a.available, 0);
      document.getElementById('stat-occupied').textContent = apts.filter(a => a.status === 'occupied').length;
      document.getElementById('stat-reserved').textContent = apts.filter(a => a.status === 'reserved').length;
    }
    // ── Stats ──
    refreshStats();

    // ── Unit type detection ──
    function getUnitType(name) {
      const n = name.toLowerCase();
      if (n.includes('studio')) return 'studio';
      if (n.includes('1-bedroom') || n.includes('one-bedroom')) return '1br';
      if (n.includes('2-bedroom') || n.includes('two-bedroom')) return '2br';
      return null;
    }

    // ── Units table ──
    function renderUnitsTable() {
      const apts = getApartments();
      const unitsTbody = document.getElementById('units-tbody');
      unitsTbody.innerHTML = apts.map(apt => {
        const unitType = getUnitType(apt.name);
        const bc = apt.status === 'available' ? 'badge-available'
          : apt.status === 'occupied' ? 'badge-occupied'
            : 'badge-reserved';
        const sl = apt.status.charAt(0).toUpperCase() + apt.status.slice(1);

        let tLabel = '—';
        if (unitType === 'studio') tLabel = 'Studio';
        else if (unitType === '1br') tLabel = '1-Bedroom';
        else if (unitType === '2br') tLabel = '2-Bedroom';
        else tLabel = 'Family Suite';

        const viewBtn = unitType
          ? `<button class="btn-action btn-view" onclick="adminPreview('${unitType}', ${apt.available})">
             <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
             View
           </button>` : '';

        const assignBtn = `<button class="btn-action btn-assign" disabled title="Use Verified Applications queue below">
        <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        Assign
      </button>`;

        const manageBtn = `<button class="btn-action btn-manage" style="color:var(--primary);" onclick="openManageUnit('${apt.id}')" title="Manage Unit">
        <svg viewBox="0 0 24 24"><path d="M19.43 12.98c.04-.32.07-.64.07-.98 0-.34-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.3-.61-.22l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.23-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98 0 .33.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.23.09.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zM12 15.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5 3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5z"/></svg>
        Manage
      </button>`;

        return `<tr>
        <td class="td-id">${apt.id}</td>
        <td style="font-weight:600;">${apt.name}</td>
        <td>${tLabel}</td>
        <td>₱${apt.price.toLocaleString()}</td>
        <td style="text-align:center;font-weight:700;color:${apt.available > 0 ? 'var(--success)' : 'var(--danger)'};">${apt.available}</td>
        <td><span class="badge-status ${bc}">${sl}</span></td>
        <td><div class="actions-cell">${viewBtn}${manageBtn}${assignBtn}</div></td>
      </tr>`;
      }).join('');
    }
    renderUnitsTable();

    // ═══ VERIFIED APPLICATIONS (from MIS Admin) ═══
    function renderVerifiedApps() {
      const reports = getReports();
      const verified = reports.filter(r => r.status === 'VERIFIED');
      const tbody = document.getElementById('verified-tbody');
      document.getElementById('verified-count-badge').textContent = verified.length + ' verified';

      if (verified.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Verified Applications</h4><p>Applications verified by MIS Admin will appear here for room assignment.</p></div></td></tr>';
        return;
      }

      tbody.innerHTML = verified.map(r => {
        const reqCount = Object.values(r.requirements || {}).filter(v => v).length;
        const reqTotal = Object.keys(r.requirements || {}).length;
        return `<tr>
        <td class="td-id">${r.id}</td>
        <td style="font-weight:600;">${r.tenantName}</td>
        <td>${formatDate(r.verifiedAt)}</td>
        <td><span style="font-size:0.82rem;font-weight:600;color:var(--success);">${reqCount}/${reqTotal} complete</span></td>
        <td>
          <div class="actions-cell">
            <button class="btn-action btn-approve" onclick="openAssignModal('${r.id}')">
              <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z"/></svg>
              Assign Room
            </button>
          </div>
        </td>
      </tr>`;
      }).join('');
    }
    renderVerifiedApps();

    // ═══ ASSIGN ROOM MODAL ═══
    let currentAssignId = null;

    function openAssignModal(reportId) {
      const reports = getReports();
      const r = reports.find(x => x.id === reportId);
      if (!r) return;

      currentAssignId = reportId;
      document.getElementById('assign-ref').textContent = r.id;
      document.getElementById('assign-name').textContent = r.tenantName;

      // Populate available rooms
      const apts = getApartments();
      const select = document.getElementById('assign-room-select');
      select.innerHTML = '<option value="">Select a unit...</option>' +
        apts.filter(a => a.available > 0).map(a =>
          `<option value="${a.id}" data-price="${a.price}">${a.name} — ₱${a.price.toLocaleString()} (${a.available} available)</option>`
        ).join('');

      document.getElementById('assign-preview').style.display = 'none';
      openModal('assign-modal');
    }

    // Unit selection → show billing preview
    document.getElementById('assign-room-select').addEventListener('change', function () {
      const opt = this.options[this.selectedIndex];
      const price = opt.getAttribute('data-price');
      const preview = document.getElementById('assign-preview');
      if (price) {
        preview.style.display = 'block';
        document.getElementById('assign-price').textContent = '₱' + Number(price).toLocaleString() + ' / month';
      } else {
        preview.style.display = 'none';
      }
    });

    // Confirm assignment
    document.getElementById('assign-confirm-btn').addEventListener('click', () => {
      const roomId = document.getElementById('assign-room-select').value;
      if (!roomId) {
        showToast('⚠️ Please select a unit.', 'var(--danger)');
        return;
      }

      const result = assignRoom(currentAssignId, roomId);
      if (result === true) {
        closeModal('assign-modal');
        showToast('✅ Room assigned successfully! Billing has been generated.', 'var(--success)');
        renderVerifiedApps();
        renderUnitsTable();
        renderBilling();
        // Update stats
        const apts2 = getApartments();
        document.getElementById('stat-available').textContent = apts2.reduce((s, a) => s + a.available, 0);
        document.getElementById('stat-occupied').textContent = apts2.filter(a => a.status === 'occupied').length;
        document.getElementById('stat-reserved').textContent = apts2.filter(a => a.status === 'reserved').length;
      } else if (result === 'waiting') {
        closeModal('assign-modal');
        showToast('⏳ No rooms available. Tenant added to waiting list.', 'var(--warning)');
        renderVerifiedApps();
      } else {
        showToast('⚠️ Unable to assign. Application may have already been processed.', 'var(--danger)');
      }
    });

    // ── Applications table ──
    const allReqs = getRequests();
    const aptReqs = allReqs.filter(r => r.type === 'apartment_application');
    const appsTbody = document.getElementById('apps-tbody');
    if (aptReqs.length === 0) {
      appsTbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text-muted);">No applications found.</td></tr>';
    } else {
      appsTbody.innerHTML = aptReqs.slice(0, 10).map(req => {
        const bc = req.status === 'approved' ? 'badge-available'
          : req.status === 'pending' ? 'badge-reserved'
            : 'badge-occupied';
        return `<tr>
        <td class="td-id">${req.id}</td>
        <td style="font-weight:600;">${req.name || 'Unknown'}</td>
        <td>${formatDate(req.date)}</td>
        <td><span class="badge-status ${bc}">${statusLabel(req.status)}</span></td>
      </tr>`;
      }).join('');
    }

    // ── Billing (read-only) ──
    function renderBilling() {
      const bill = getBilling().filter(b => b.type.toLowerCase().includes('apartment'));
      const bTbody = document.getElementById('billing-tbody');
      if (bill.length === 0) {
        bTbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text-muted);">No billing records.</td></tr>';
      } else {
        bTbody.innerHTML = bill.map(b => `<tr>
        <td style="font-weight:600;">${b.name}</td>
        <td style="font-weight:700;">${currencyFormat(b.amount)}</td>
        <td style="color:var(--text-muted);">${formatDate(b.dueDate)}</td>
        <td><span class="badge-status ${badgeClass(b.status)}">${statusLabel(b.status)}</span></td>
      </tr>`).join('');
      }
    }
    renderBilling();

    // ── Room Preview ──
    function adminPreview(unitType, availCount) {
      if (typeof openRoomPreview === 'function') {
        openRoomPreview(unitType, {
          availableCount: availCount,
          basePath: '../../user/Apartment/assets/room-images/',
          selectLabel: 'View Unit Details',
          onSelect: function (type) {
            showToast('📋 Unit details for ' + type.toUpperCase() + ' — view only in Staff Admin mode.', 'var(--info)');
          }
        });
      } else {
        showToast('ℹ️ Room preview module not loaded.', 'var(--info)');
      }
    }

    // ── Sidebar & Modals ──
    initSidebar();
    setupModalClose('assign-modal');
    setupModalClose('manage-modal');
    refreshStats();

    // ── Manage Unit Logic ──
    function openManageUnit(aptId) {
      const apts = getApartments();
      const apt = apts.find(a => a.id === aptId);
      if (!apt) return;

      document.getElementById('manage-unit-id').value = apt.id;
      document.getElementById('manage-unit-name').textContent = apt.name;
      document.getElementById('manage-unit-slots').value = apt.available;
      document.getElementById('manage-unit-status').value = apt.status;

      openModal('manage-modal');
    }

    document.getElementById('manage-save-btn').addEventListener('click', () => {
      const id = document.getElementById('manage-unit-id').value;
      const slots = parseInt(document.getElementById('manage-unit-slots').value, 10);
      const status = document.getElementById('manage-unit-status').value;

      if (isNaN(slots) || slots < 0) {
        showToast('⚠️ Please enter a valid number of slots.', 'var(--danger)');
        return;
      }

      const apts = getApartments();
      const idx = apts.findIndex(a => a.id === id);
      if (idx > -1) {
        apts[idx].available = slots;
        apts[idx].status = status;
        saveApartments(apts);
        showToast('✅ Unit availability successfully updated.', 'var(--success)');
        renderUnitsTable();
        refreshStats();
        closeModal('manage-modal');
      } else {
        showToast('⚠️ Unit not found.', 'var(--danger)');
      }
    });

    // Initialize notification badge
    initNotifBadge('staff');

  </script>
</body>

</html>