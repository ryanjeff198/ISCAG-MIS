<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Tenant Verification</title>
  <meta name="description" content="MIS Admin tenant application verification and document review" />
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
    }

    .tab-btn:hover {
      color: var(--primary);
    }

    .tab-btn.active {
      color: var(--primary-dark);
      border-bottom-color: var(--primary);
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
      margin-left: 6px;
      padding: 0 6px;
    }

    .tab-btn .tab-count.pending {
      background: rgba(199, 154, 43, 0.15);
      color: var(--warning);
    }

    .tab-btn .tab-count.incomplete {
      background: rgba(139, 46, 46, 0.12);
      color: var(--danger);
    }

    .tab-btn .tab-count.verified {
      background: rgba(47, 138, 96, 0.12);
      color: var(--success);
    }

    .tab-panel {
      display: none;
    }

    .tab-panel.active {
      display: block;
    }

    /* ── Review Modal Enhancements ── */
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

    .review-tabs {
      display: flex;
      gap: 0;
      border-bottom: 2px solid var(--border);
      margin-bottom: 16px;
    }

    .review-tab-btn {
      padding: 8px 16px;
      background: none;
      border: none;
      border-bottom: 2px solid transparent;
      font-family: inherit;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.15s;
      margin-bottom: -2px;
    }

    .review-tab-btn:hover {
      color: var(--primary);
    }

    .review-tab-btn.active {
      color: var(--primary-dark);
      border-bottom-color: var(--accent);
    }

    .review-tab-panel {
      display: none;
    }

    .review-tab-panel.active {
      display: block;
    }

    /* ── Detail grid ── */
    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .detail-item label {
      display: block;
      font-size: 0.7rem;
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

    /* ── Document thumbnails ── */
    .doc-thumb-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }

    .doc-thumb-card {
      border: 1.5px solid var(--border);
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.2s;
      cursor: pointer;
    }

    .doc-thumb-card:hover {
      border-color: var(--primary);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .doc-thumb-card img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      display: block;
      background: #f4f6f5;
    }

    .doc-thumb-card .doc-thumb-label {
      padding: 8px 10px;
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      text-align: center;
      background: #f8faf9;
    }

    .doc-thumb-card.missing {
      border-color: rgba(139, 46, 46, 0.3);
    }

    .doc-thumb-card.missing .doc-thumb-label {
      color: var(--danger);
      background: rgba(139, 46, 46, 0.05);
    }

    /* ── Req checklist ── */
    .req-checklist {
      list-style: none;
      padding: 0;
    }

    .req-checklist li {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      margin-bottom: 8px;
    }

    .req-checklist li svg {
      width: 18px;
      height: 18px;
      flex-shrink: 0;
    }

    .req-checklist li.ok svg {
      fill: var(--success);
    }

    .req-checklist li.missing svg {
      fill: var(--danger);
    }

    .req-checklist li .req-name {
      font-size: 0.85rem;
      font-weight: 600;
      flex: 1;
    }

    .req-checklist li .req-badge {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 2px 10px;
      border-radius: 10px;
    }

    .req-checklist li.ok .req-badge {
      background: rgba(47, 138, 96, 0.1);
      color: var(--success);
    }

    .req-checklist li.missing .req-badge {
      background: rgba(139, 46, 46, 0.1);
      color: var(--danger);
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
      padding: 40px 20px;
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

    /* ── Image preview overlay ── */
    .img-overlay {
      position: fixed;
      inset: 0;
      z-index: 99998;
      background: rgba(15, 30, 22, 0.65);
      backdrop-filter: blur(8px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      animation: modalFadeIn 0.2s ease;
    }

    .img-overlay img {
      max-width: 80vw;
      max-height: 80vh;
      border-radius: 14px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
      object-fit: contain;
      background: white;
      padding: 12px;
    }

    .img-overlay-close {
      position: absolute;
      top: 24px;
      right: 24px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .img-overlay-close svg {
      width: 20px;
      height: 20px;
      fill: var(--text-main);
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
            <div class="top-bar-title">Tenant Application Verification</div>
            <div class="top-bar-subtitle">Review applications, verify documents, and forward to Apartment Admin</div>
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
            <div class="insight-label">Verified Today</div>
            <div class="insight-value success">8</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Document Accuracy</div>
            <div class="insight-value info">96.4%</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Avg. Processing</div>
            <div class="insight-value">4.2h</div>
          </div>
        </div>

        <!-- STATS ROW -->
        <div class="stats-row" id="stats-row">
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-pending">0</div>
              <div class="stat-label">Pending Review</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-incomplete">0</div>
              <div class="stat-label">Incomplete</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-verified">0</div>
              <div class="stat-label">Verified</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue">
              <svg viewBox="0 0 24 24">
                <path
                  d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-total">0</div>
              <div class="stat-label">Total Applications</div>
            </div>
          </div>
        </div>

        <!-- TAB NAV -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
            </svg>
            Pending Review
            <span class="tab-count pending" id="tab-pending-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('incomplete')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
            Incomplete
            <span class="tab-count incomplete" id="tab-incomplete-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('verified')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
            </svg>
            Verified & Forwarded
            <span class="tab-count verified" id="tab-verified-count">0</span>
          </button>
        </div>

        <!-- PENDING TAB -->
        <div class="tab-panel active" id="tab-pending">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                </svg>
                Pending Applications
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Submitted</th>
                      <th>Documents</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="pending-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- INCOMPLETE TAB -->
        <div class="tab-panel" id="tab-incomplete">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
                </svg>
                Incomplete / Revision Required
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Submitted</th>
                      <th>Feedback</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="incomplete-tbody"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- VERIFIED TAB -->
        <div class="tab-panel" id="tab-verified">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
                Verified & Forwarded to Apartment Admin
              </h6>
            </div>
            <div class="section-card-body" style="padding:0;">
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Submitted</th>
                      <th>Verified Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="verified-tbody"></tbody>
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
        <h5 id="review-modal-title">Application Review</h5>
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

  <!-- ═══ FEEDBACK MODAL ═══ -->
  <div class="modal-backdrop" id="feedback-modal" style="display:none;">
    <div class="modal-content" style="max-width:480px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
          </svg>
          Mark as Incomplete
        </h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Application Reference</label>
          <p id="feedback-ref" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Feedback for Tenant *</label>
          <textarea class="feedback-area" id="feedback-text"
            placeholder="Describe what's missing or incorrect (e.g., 'Valid ID is blurry, please re-upload a clearer copy.')"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('feedback-modal')">Cancel</button>
        <button class="btn-topbar primary" style="background:var(--danger);border-color:var(--danger);"
          id="feedback-submit-btn">Mark Incomplete</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══ INIT ══
    initAdminData();
    initReportsData();
    setCurrentRole(ROLES.MIS_ADMIN);
    loadUserNav();
    initSidebar();
    initDropdowns();

    // ══ RENDER ══
    function renderAll() {
      const reports = getReports();
      const pending = reports.filter(r => r.status === 'PENDING_MIS');
      const incomplete = reports.filter(r => r.status === 'REVISION');
      const verified = reports.filter(r => r.status === 'VERIFIED' || r.status === 'APPROVED' || r.status === 'ACTIVE');

      // Stats
      document.getElementById('stat-pending').textContent = pending.length;
      document.getElementById('stat-incomplete').textContent = incomplete.length;
      document.getElementById('stat-verified').textContent = verified.length;
      document.getElementById('stat-total').textContent = reports.length;

      // Tab counts
      document.getElementById('tab-pending-count').textContent = pending.length;
      document.getElementById('tab-incomplete-count').textContent = incomplete.length;
      document.getElementById('tab-verified-count').textContent = verified.length;

      // ── Pending Table ──
      const pendingTbody = document.getElementById('pending-tbody');
      if (pending.length === 0) {
        pendingTbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Pending Applications</h4><p>All applications have been reviewed.</p></div></td></tr>';
      } else {
        pendingTbody.innerHTML = pending.map(r => {
          const reqCount = Object.values(r.requirements || {}).filter(v => v).length;
          const reqTotal = Object.keys(r.requirements || {}).length;
          return `<tr>
          <td class="td-id">${r.id}</td>
          <td style="font-weight:600;">${r.tenantName}</td>
          <td>${formatDate(r.submittedAt)}</td>
          <td>
            <span style="font-size:0.82rem;font-weight:600;color:${reqCount === reqTotal ? 'var(--success)' : 'var(--warning)'};">
              ${reqCount}/${reqTotal} verified
            </span>
          </td>
          <td>
            <div class="actions-cell">
              <button class="btn-action btn-view" onclick="openReview('${r.id}')">
                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                Review
              </button>
            </div>
          </td>
        </tr>`;
        }).join('');
      }

      // ── Incomplete Table ──
      const incompleteTbody = document.getElementById('incomplete-tbody');
      if (incomplete.length === 0) {
        incompleteTbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Incomplete Applications</h4><p>No applications have been marked as incomplete.</p></div></td></tr>';
      } else {
        incompleteTbody.innerHTML = incomplete.map(r => `<tr>
        <td class="td-id">${r.id}</td>
        <td style="font-weight:600;">${r.tenantName}</td>
        <td>${formatDate(r.submittedAt)}</td>
        <td style="font-size:0.82rem;color:var(--danger);max-width:220px;">${r.remarks || '—'}</td>
        <td><span class="badge-status badge-rejected">Revision Required</span></td>
      </tr>`).join('');
      }

      // ── Verified Table ──
      const verifiedTbody = document.getElementById('verified-tbody');
      if (verified.length === 0) {
        verifiedTbody.innerHTML = '<tr><td colspan="5"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Verified Applications</h4><p>No applications have been verified yet.</p></div></td></tr>';
      } else {
        verifiedTbody.innerHTML = verified.map(r => `<tr>
        <td class="td-id">${r.id}</td>
        <td style="font-weight:600;">${r.tenantName}</td>
        <td>${formatDate(r.submittedAt)}</td>
        <td>${formatDate(r.verifiedAt)}</td>
        <td><span class="badge-status ${reportBadgeClass(r.status)}">${reportStatusLabel(r.status)}</span></td>
      </tr>`).join('');
      }
    }

    renderAll();

    // ══ TAB SWITCHING ══
    function switchTab(tab) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
      // Find correct button by text match
      document.querySelectorAll('.tab-btn').forEach(b => {
        if ((tab === 'pending' && b.textContent.includes('Pending')) ||
          (tab === 'incomplete' && b.textContent.includes('Incomplete')) ||
          (tab === 'verified' && b.textContent.includes('Verified'))) {
          b.classList.add('active');
        }
      });
    }

    // ══ REVIEW MODAL ══
    function openReview(reportId) {
      const reports = getReports();
      const r = reports.find(x => x.id === reportId);
      if (!r) return;

      document.getElementById('review-modal-title').textContent = 'Review — ' + r.id + ' · ' + r.tenantName;

      // Try to get uploaded document images from tenant's localStorage
      const docUploads = JSON.parse(localStorage.getItem('mis_req_doc_uploads') || '{}');

      const docMapping = [
        { key: 'doc-income', label: 'Proof of Income' },
        { key: 'doc-id-front', label: 'Valid ID (Front)' },
        { key: 'doc-id-back', label: 'Valid ID (Back)' },
        { key: 'doc-birth', label: 'Birth Certificate' },
        { key: 'doc-nbi', label: 'NBI / Police Clearance' },
        { key: 'doc-photo', label: '2x2 Photo' }
      ];

      const docsHtml = docMapping.map(doc => {
        const upload = docUploads[doc.key];
        if (upload && upload.dataUrl) {
          return `<div class="doc-thumb-card" onclick="previewImg('${doc.key}')">
          <img src="${upload.dataUrl}" alt="${doc.label}" />
          <div class="doc-thumb-label">${doc.label}</div>
        </div>`;
        } else {
          return `<div class="doc-thumb-card missing">
          <div style="width:100%;height:120px;display:flex;align-items:center;justify-content:center;background:rgba(139,46,46,0.04);">
            <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:var(--danger);opacity:0.3;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </div>
          <div class="doc-thumb-label">${doc.label} — Missing</div>
        </div>`;
        }
      }).join('');

      // Requirements checklist
      const reqItems = [
        { key: 'valid_id', label: 'Valid ID (Front & Back)' },
        { key: 'certificate', label: 'Birth Certificate' },
        { key: 'photo', label: '2x2 Photo' },
        { key: 'contract', label: 'Proof of Income' },
        { key: 'nbi_clearance', label: 'NBI / Police Clearance' }
      ];

      const reqHtml = reqItems.map(item => {
        const ok = r.requirements && r.requirements[item.key];
        return `<li class="${ok ? 'ok' : 'missing'}">
        <svg viewBox="0 0 24 24">${ok
            ? '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>'
            : '<path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>'
          }</svg>
        <span class="req-name">${item.label}</span>
        <span class="req-badge">${ok ? 'Submitted' : 'Missing'}</span>
      </li>`;
      }).join('');

      document.getElementById('review-modal-body').innerHTML = `
      <div class="review-tabs">
        <button class="review-tab-btn active" onclick="switchReviewTab('details')">Applicant Details</button>
        <button class="review-tab-btn" onclick="switchReviewTab('documents')">Uploaded Documents</button>
        <button class="review-tab-btn" onclick="switchReviewTab('checklist')">Requirements</button>
      </div>

      <div class="review-tab-panel active" id="rtab-details">
        <div class="detail-grid">
          <div class="detail-item"><label>Full Name</label><p>${r.tenantName}</p></div>
          <div class="detail-item"><label>Tenant ID</label><p>${r.tenantId}</p></div>
          <div class="detail-item"><label>Submitted</label><p>${formatDate(r.submittedAt)}</p></div>
          <div class="detail-item"><label>Status</label><p><span class="badge-status ${reportBadgeClass(r.status)}">${reportStatusLabel(r.status)}</span></p></div>
          <div class="detail-item"><label>Report ID</label><p>${r.id}</p></div>
          <div class="detail-item"><label>Last Updated</label><p>${formatDate(r.updatedAt)}</p></div>
        </div>
        ${r.remarks ? `<div style="margin-top:16px;padding:12px 16px;background:rgba(199,154,43,0.06);border:1px solid rgba(199,154,43,0.15);border-radius:8px;">
          <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);display:block;margin-bottom:4px;">Admin Remarks</label>
          <p style="font-size:0.85rem;margin:0;color:var(--text-main);">${r.remarks}</p>
        </div>` : ''}
      </div>

      <div class="review-tab-panel" id="rtab-documents">
        <div class="doc-thumb-grid">${docsHtml}</div>
      </div>

      <div class="review-tab-panel" id="rtab-checklist">
        <ul class="req-checklist">${reqHtml}</ul>
      </div>
    `;

      // Footer actions — only show for PENDING_MIS
      if (r.status === 'PENDING_MIS') {
        document.getElementById('review-modal-footer').innerHTML = `
        <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
        <button class="btn-topbar" style="color:var(--danger);border-color:var(--danger);" onclick="openFeedback('${r.id}')">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:var(--danger);"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
          Mark Incomplete
        </button>
        <button class="btn-topbar primary" onclick="verifyAndForward('${r.id}')">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:white;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          Verify & Forward
        </button>
      `;
      } else {
        document.getElementById('review-modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }

      openModal('review-modal');
    }

    function switchReviewTab(tab) {
      document.querySelectorAll('.review-tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.review-tab-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('rtab-' + tab).classList.add('active');
      document.querySelectorAll('.review-tab-btn').forEach(b => {
        if ((tab === 'details' && b.textContent.includes('Details')) ||
          (tab === 'documents' && b.textContent.includes('Documents')) ||
          (tab === 'checklist' && b.textContent.includes('Requirements'))) {
          b.classList.add('active');
        }
      });
    }

    // ══ VERIFY & FORWARD ══
    function verifyAndForward(reportId) {
      const result = approveReport(reportId);
      if (result) {
        closeModal('review-modal');
        showToast('✅ Application ' + reportId + ' verified and forwarded to Apartment Admin!', 'var(--success)');
        renderAll();
      } else {
        showToast('⚠️ Unable to verify. Application may have already been processed.', 'var(--danger)');
      }
    }

    // ══ MARK INCOMPLETE ══
    let currentFeedbackId = null;
    function openFeedback(reportId) {
      currentFeedbackId = reportId;
      document.getElementById('feedback-ref').textContent = reportId;
      document.getElementById('feedback-text').value = '';
      closeModal('review-modal');
      setTimeout(() => openModal('feedback-modal'), 250);
    }

    document.getElementById('feedback-submit-btn').addEventListener('click', () => {
      const text = document.getElementById('feedback-text').value.trim();
      if (!text) {
        showToast('⚠️ Please provide feedback for the tenant.', 'var(--danger)');
        return;
      }
      const result = rejectReport(currentFeedbackId, text);
      if (result) {
        closeModal('feedback-modal');
        showToast('📋 Application ' + currentFeedbackId + ' marked as incomplete. Tenant has been notified.', 'var(--warning)');
        renderAll();
      } else {
        showToast('⚠️ Unable to process. Application may have already been processed.', 'var(--danger)');
      }
    });

    // ══ IMAGE PREVIEW ══
    function previewImg(docKey) {
      const docUploads = JSON.parse(localStorage.getItem('mis_req_doc_uploads') || '{}');
      const upload = docUploads[docKey];
      if (!upload || !upload.dataUrl) return;

      const overlay = document.createElement('div');
      overlay.className = 'img-overlay';
      overlay.innerHTML = `
      <button class="img-overlay-close" title="Close"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      <img src="${upload.dataUrl}" alt="Document Preview" />
    `;
      document.body.appendChild(overlay);
      overlay.querySelector('.img-overlay-close').addEventListener('click', () => { overlay.style.opacity = '0'; overlay.style.transition = 'opacity 0.2s'; setTimeout(() => overlay.remove(), 200); });
      overlay.addEventListener('click', e => { if (e.target === overlay) { overlay.style.opacity = '0'; overlay.style.transition = 'opacity 0.2s'; setTimeout(() => overlay.remove(), 200); } });
    }

    // ══ MODAL SETUP ══
    setupModalClose('review-modal');
    setupModalClose('feedback-modal');
  </script>
</body>

</html>