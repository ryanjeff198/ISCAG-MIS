<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Parking Approval</title>
  <meta name="description" content="Admin parking rental application review and approval" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    /* ── Tab navigation ── */
    .tab-nav {
      display: flex;
      gap: 0;
      border-bottom: 2px solid var(--border);
      margin-bottom: 20px;
    }

    .tab-btn {
      padding: 10px 20px;
      background: none;
      border: none;
      border-bottom: 3px solid transparent;
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.18s;
      margin-bottom: -2px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .tab-btn:hover {
      color: var(--primary);
    }

    .tab-btn.active {
      color: var(--primary-dark);
      border-bottom-color: var(--primary);
    }

    .tab-btn svg {
      width: 15px;
      height: 15px;
      fill: currentColor;
    }

    .tab-btn .tab-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 20px;
      height: 20px;
      border-radius: 10px;
      font-size: 0.68rem;
      font-weight: 700;
      margin-left: 2px;
      padding: 0 6px;
    }

    .tab-btn .tab-count.pending {
      background: rgba(199, 154, 43, 0.15);
      color: var(--warning);
    }

    .tab-btn .tab-count.rejected {
      background: rgba(139, 46, 46, 0.12);
      color: var(--danger);
    }

    .tab-btn .tab-count.approved {
      background: rgba(47, 138, 96, 0.12);
      color: var(--success);
    }

    .tab-panel {
      display: none;
    }

    .tab-panel.active {
      display: block;
    }

    /* ── Review Modal ── */
    .review-modal .modal-content {
      max-width: 760px;
      max-height: 90vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .review-modal .modal-body {
      overflow-y: auto;
      flex: 1;
      max-height: 65vh;
    }

    /* ── Detail grid ── */
    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 16px;
    }

    .detail-item {
      padding: 10px 14px;
      background: #f8faf9;
      border-radius: 8px;
      border: 1px solid var(--border);
    }

    .detail-item label {
      display: block;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-muted);
      margin-bottom: 3px;
    }

    .detail-item p {
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-main);
      margin: 0;
    }

    .detail-item.full-width {
      grid-column: 1 / -1;
    }

    /* ── Section divider ── */
    .detail-section-title {
      font-family: 'Lora', serif;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 10px 0 8px;
      border-bottom: 1.5px solid var(--primary);
      margin: 16px 0 12px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .detail-section-title:first-child {
      margin-top: 0;
    }

    .detail-section-title svg {
      width: 14px;
      height: 14px;
      fill: var(--accent);
    }

    /* ── Vehicle highlight ── */
    .vehicle-highlight {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 20px;
      background: linear-gradient(135deg, rgba(26, 58, 92, 0.04), rgba(23, 107, 69, 0.03));
      border-radius: 10px;
      border: 1.5px solid var(--border);
      margin-bottom: 16px;
    }

    .vehicle-icon-wrap {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .vehicle-icon-wrap svg {
      width: 26px;
      height: 26px;
      fill: white;
    }

    .vehicle-info h4 {
      font-family: 'Lora', serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 2px;
    }

    .vehicle-info p {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin: 0;
    }

    .vehicle-plate {
      margin-left: auto;
      padding: 6px 16px;
      background: var(--primary-dark);
      color: white;
      border-radius: 6px;
      font-size: 0.9rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    /* ── Feedback textarea ── */
    .feedback-area {
      width: 100%;
      min-height: 80px;
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-family: inherit;
      font-size: 0.85rem;
      resize: vertical;
    }

    .feedback-area:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    /* ── Empty state ── */
    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: var(--text-muted);
    }

    .empty-state svg {
      width: 48px;
      height: 48px;
      fill: var(--border);
      margin-bottom: 12px;
    }

    .empty-state h4 {
      font-family: 'Lora', serif;
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-muted);
      margin: 0 0 6px;
    }

    .empty-state p {
      font-size: 0.82rem;
      margin: 0;
    }

    /* ── Badge overrides ── */
    .badge-pending {
      background: rgba(199, 154, 43, 0.12);
      color: var(--warning);
    }

    .badge-approved {
      background: rgba(47, 138, 96, 0.12);
      color: var(--success);
    }

    .badge-rejected {
      background: rgba(139, 46, 46, 0.12);
      color: var(--danger);
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">Parking Rental Approval</div>
            <div class="top-bar-subtitle">Review and approve tenant parking rental applications</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Approved</div>
            <div class="insight-value success" id="stat-approved">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Rejected</div>
            <div class="insight-value danger" id="stat-rejected">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value info" id="stat-total">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Available Slots</div>
            <div class="insight-value">15</div>
          </div>
        </div>

        <!-- TAB NAV -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending')">
            <svg viewBox="0 0 24 24">
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
            </svg>
            Pending Review
            <span class="tab-count pending" id="tab-pending-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('approved')">
            <svg viewBox="0 0 24 24">
              <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
            </svg>
            Approved
            <span class="tab-count approved" id="tab-approved-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('rejected')">
            <svg viewBox="0 0 24 24">
              <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
            Rejected
            <span class="tab-count rejected" id="tab-rejected-count">0</span>
          </button>
        </div>

        <!-- PENDING TAB -->
        <div class="tab-panel active" id="tab-pending">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path
                    d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                </svg>
                Pending Parking Applications
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Parking ID</th>
                      <th>Applicant</th>
                      <th>Vehicle</th>
                      <th>Plate No.</th>
                      <th>Submitted</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="pending-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- APPROVED TAB -->
        <div class="tab-panel" id="tab-approved">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
                Approved Parking Rentals
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Parking ID</th>
                      <th>Tenant</th>
                      <th>Vehicle</th>
                      <th>Plate No.</th>
                      <th>Type</th>
                      <th>Date Started</th>
                      <th>Approved On</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="approved-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- REJECTED TAB -->
        <div class="tab-panel" id="tab-rejected">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                </svg>
                Rejected Applications
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Parking ID</th>
                      <th>Applicant</th>
                      <th>Vehicle</th>
                      <th>Plate No.</th>
                      <th>Feedback</th>
                      <th>Rejected On</th>
                    </tr>
                  </thead>
                  <tbody id="rejected-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- ═══ REVIEW MODAL ═══ -->
  <div class="modal-backdrop review-modal" id="review-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="review-modal-title">Parking Application Review</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body" id="review-modal-body">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer" id="review-modal-footer">
        <!-- Action buttons populated by JS -->
      </div>
    </div>
  </div>

  <!-- ═══ REJECT MODAL ═══ -->
  <div class="modal-backdrop" id="reject-modal" style="display:none;">
    <div class="modal-content" style="max-width:480px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
          </svg>
          Reject Application
        </h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Application Reference</label>
          <p id="reject-ref" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Reason for Rejection *</label>
          <textarea class="feedback-area" id="reject-text"
            placeholder="Describe why this application is being rejected (e.g., 'Incomplete vehicle information, plate number does not match records.')"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('reject-modal')">Cancel</button>
        <button class="btn-topbar primary" style="background:var(--danger);border-color:var(--danger);"
          id="reject-submit-btn">Reject Application</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══ INIT ══
    initAdminData();
    setCurrentRole(ROLES.MIS_ADMIN);
    loadUserNav();
    initSidebar();
    initDropdowns();

    // ══ PARKING DATA ══
    const PARKING_KEY = 'mis_parking_applications';

    function getParkingApps() {
      const raw = localStorage.getItem(PARKING_KEY);
      return raw ? JSON.parse(raw) : [];
    }

    function saveParkingApps(apps) {
      localStorage.setItem(PARKING_KEY, JSON.stringify(apps));
    }

    // ══ RENDER ══
    function renderAll() {
      const apps = getParkingApps();
      const pending = apps.filter(a => a.status === 'PENDING');
      const approved = apps.filter(a => a.status === 'APPROVED');
      const rejected = apps.filter(a => a.status === 'REJECTED');

      // Stats
      document.getElementById('stat-pending').textContent = pending.length;
      document.getElementById('stat-approved').textContent = approved.length;
      document.getElementById('stat-rejected').textContent = rejected.length;
      document.getElementById('stat-total').textContent = apps.length;

      // Tab counts
      document.getElementById('tab-pending-count').textContent = pending.length;
      document.getElementById('tab-approved-count').textContent = approved.length;
      document.getElementById('tab-rejected-count').textContent = rejected.length;

      // ── Pending Table ──
      const pendingTbody = document.getElementById('pending-tbody');
      if (pending.length === 0) {
        pendingTbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Pending Applications</h4><p>All parking applications have been reviewed.</p></div></td></tr>';
      } else {
        pendingTbody.innerHTML = pending.map(a => `<tr>
          <td class="td-id">${a.id}</td>
          <td style="font-weight:600;">${a.tenantName}</td>
          <td>${a.vehicleName}</td>
          <td style="font-weight:700;letter-spacing:0.04em;">${a.plateNo}</td>
          <td>${formatDate(a.submittedAt)}</td>
          <td>
            <div class="actions-cell">
              <button class="btn-action btn-view" onclick="openReview('${a.id}')">
                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                Review
              </button>
            </div>
          </td>
        </tr>`).join('');
      }

      // ── Approved Table ──
      const approvedTbody = document.getElementById('approved-tbody');
      if (approved.length === 0) {
        approvedTbody.innerHTML = '<tr><td colspan="8"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg><h4>No Approved Parking Yet</h4><p>Approved parking rentals will appear here.</p></div></td></tr>';
      } else {
        approvedTbody.innerHTML = approved.map(a => `<tr>
          <td class="td-id">${a.id}</td>
          <td style="font-weight:600;">${a.tenantName}</td>
          <td>${a.vehicleName}</td>
          <td style="font-weight:700;letter-spacing:0.04em;">${a.plateNo}</td>
          <td>${a.vehicleType}</td>
          <td>${formatDate(a.dateStarted)}</td>
          <td>${formatDate(a.approvedAt)}</td>
          <td><span class="badge-status badge-approved"><span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span> Approved</span></td>
        </tr>`).join('');
      }

      // ── Rejected Table ──
      const rejectedTbody = document.getElementById('rejected-tbody');
      if (rejected.length === 0) {
        rejectedTbody.innerHTML = '<tr><td colspan="6"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Rejected Applications</h4><p>No applications have been rejected.</p></div></td></tr>';
      } else {
        rejectedTbody.innerHTML = rejected.map(a => `<tr>
          <td class="td-id">${a.id}</td>
          <td style="font-weight:600;">${a.tenantName}</td>
          <td>${a.vehicleName}</td>
          <td style="font-weight:700;letter-spacing:0.04em;">${a.plateNo}</td>
          <td style="font-size:0.82rem;color:var(--danger);max-width:220px;">${a.remarks || '—'}</td>
          <td>${formatDate(a.reviewedAt)}</td>
        </tr>`).join('');
      }
    }

    renderAll();

    // ══ TAB SWITCHING ══
    function switchTab(tab) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
      document.querySelectorAll('.tab-btn').forEach(b => {
        if ((tab === 'pending' && b.textContent.includes('Pending')) ||
          (tab === 'approved' && b.textContent.includes('Approved')) ||
          (tab === 'rejected' && b.textContent.includes('Rejected'))) {
          b.classList.add('active');
        }
      });
    }

    // ══ REVIEW MODAL ══
    function openReview(appId) {
      const apps = getParkingApps();
      const a = apps.find(x => x.id === appId);
      if (!a) return;

      document.getElementById('review-modal-title').textContent = 'Review — ' + a.id + ' · ' + a.tenantName;

      document.getElementById('review-modal-body').innerHTML = `
        <div class="vehicle-highlight">
          <div class="vehicle-icon-wrap">
            <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
          </div>
          <div class="vehicle-info">
            <h4>${a.vehicleName}</h4>
            <p>${a.vehicleType || 'Vehicle'} · Owner: ${a.vehicleOwner}</p>
          </div>
          <div class="vehicle-plate">${a.plateNo}</div>
        </div>

        <div class="detail-section-title">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          Personal Information
        </div>
        <div class="detail-grid">
          <div class="detail-item"><label>Full Name</label><p>${a.fullName || a.tenantName}</p></div>
          <div class="detail-item"><label>Date of Birth</label><p>${formatDate(a.dob)}</p></div>
          <div class="detail-item"><label>Tenant ID</label><p>${a.tenantId}</p></div>
          <div class="detail-item"><label>Application Date</label><p>${formatDate(a.date)}</p></div>
        </div>

        <div class="detail-section-title">
          <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
          Address
        </div>
        <div class="detail-grid">
          <div class="detail-item full-width"><label>Full Address</label><p>${a.addressFull || '—'}</p></div>
          <div class="detail-item"><label>Room No.</label><p>${a.roomNo || '—'}</p></div>
          <div class="detail-item"><label>Bldg. No.</label><p>${a.bldgNo || '—'}</p></div>
          <div class="detail-item"><label>Barangay</label><p>${a.brgy || '—'}</p></div>
          <div class="detail-item"><label>Municipality / City</label><p>${a.munCity || '—'}</p></div>
        </div>

        <div class="detail-section-title">
          <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
          Rental Period
        </div>
        <div class="detail-grid">
          <div class="detail-item"><label>Date Started</label><p>${formatDate(a.dateStarted)}</p></div>
          <div class="detail-item"><label>Submitted At</label><p>${formatDate(a.submittedAt)}</p></div>
        </div>
      `;

      // Footer actions
      if (a.status === 'PENDING') {
        document.getElementById('review-modal-footer').innerHTML = `
          <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
          <button class="btn-topbar" style="color:var(--danger);border-color:var(--danger);" onclick="openReject('${a.id}')">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:var(--danger);"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            Reject
          </button>
          <button class="btn-topbar primary" onclick="approveApp('${a.id}')">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:white;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            Approve Parking
          </button>
        `;
      } else {
        document.getElementById('review-modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }

      openModal('review-modal');
    }

    // ══ APPROVE ══
    function approveApp(appId) {
      const apps = getParkingApps();
      const idx = apps.findIndex(a => a.id === appId);
      if (idx === -1) return;

      apps[idx].status = 'APPROVED';
      apps[idx].approvedAt = new Date().toISOString();
      apps[idx].reviewedAt = new Date().toISOString();
      saveParkingApps(apps);

      closeModal('review-modal');
      showToast('✅ Parking application ' + appId + ' has been approved!', 'var(--success)');
      renderAll();
    }

    // ══ REJECT ══
    let currentRejectId = null;
    function openReject(appId) {
      currentRejectId = appId;
      document.getElementById('reject-ref').textContent = appId;
      document.getElementById('reject-text').value = '';
      closeModal('review-modal');
      setTimeout(() => openModal('reject-modal'), 250);
    }

    document.getElementById('reject-submit-btn').addEventListener('click', () => {
      const text = document.getElementById('reject-text').value.trim();
      if (!text) {
        showToast('⚠️ Please provide a reason for rejection.', 'var(--danger)');
        return;
      }

      const apps = getParkingApps();
      const idx = apps.findIndex(a => a.id === currentRejectId);
      if (idx === -1) return;

      apps[idx].status = 'REJECTED';
      apps[idx].remarks = text;
      apps[idx].reviewedAt = new Date().toISOString();
      saveParkingApps(apps);

      closeModal('reject-modal');
      showToast('📋 Parking application ' + currentRejectId + ' has been rejected.', 'var(--warning)');
      renderAll();
    });

    // ══ MODAL SETUP ══
    setupModalClose('review-modal');
    setupModalClose('reject-modal');
  </script>
</body>

</html>