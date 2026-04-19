<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Tenant Reports</title>
  <meta name="description"
    content="Central tenant report dashboard for application tracking, room assignment, and billing status." />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    /* ── Requirements dots ── */
    .req-dots {
      display: flex;
      gap: 4px;
      align-items: center;
    }

    .req-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: inline-block;
      border: 1.5px solid var(--border);
      position: relative;
      cursor: help;
    }

    .req-dot.done {
      background: var(--success);
      border-color: var(--success);
    }

    .req-dot.missing {
      background: transparent;
      border-color: var(--danger);
    }

    /* ── Report detail panel ── */
    .report-detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .detail-block {
      margin-bottom: 16px;
    }

    .detail-block-label {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-muted);
      margin-bottom: 4px;
    }

    .detail-block-value {
      font-size: 0.87rem;
      color: var(--text-main);
      font-weight: 500;
    }

    /* ── Timeline mini ── */
    .mini-timeline {
      position: relative;
      padding-left: 20px;
    }

    .mini-timeline::before {
      content: '';
      position: absolute;
      left: 5px;
      top: 4px;
      bottom: 4px;
      width: 2px;
      background: var(--border);
      border-radius: 2px;
    }

    .mini-tl-item {
      position: relative;
      margin-bottom: 10px;
      padding-left: 8px;
    }

    .mini-tl-item::before {
      content: '';
      position: absolute;
      left: -19px;
      top: 5px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--primary-light);
      border: 2px solid white;
      box-shadow: 0 0 0 1px var(--primary-light);
    }

    .mini-tl-item .tl-action {
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-main);
    }

    .mini-tl-item .tl-time {
      font-size: 0.72rem;
      color: var(--text-muted);
    }

    /* ── Requirements checklist ── */
    .req-checklist {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .req-checklist li {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 0;
      font-size: 0.84rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }

    .req-checklist li:last-child {
      border-bottom: none;
    }

    .req-check {
      width: 18px;
      height: 18px;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .req-check.yes {
      background: var(--success);
    }

    .req-check.yes svg {
      fill: white;
    }

    .req-check.no {
      background: rgba(139, 46, 46, 0.1);
      border: 1.5px solid var(--danger);
    }

    .req-check.no svg {
      fill: var(--danger);
    }

    .req-check svg {
      width: 12px;
      height: 12px;
    }

    /* ── Status pipeline ── */
    .status-pipeline {
      display: flex;
      align-items: center;
      gap: 0;
      margin: 20px 0;
      overflow-x: auto;
      padding-bottom: 4px;
    }

    .pipeline-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      min-width: 80px;
      position: relative;
    }

    .pipeline-dot {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2.5px solid var(--border);
      background: white;
      transition: all 0.2s;
    }

    .pipeline-dot svg {
      width: 14px;
      height: 14px;
      fill: var(--border);
    }

    .pipeline-dot.done {
      background: var(--success);
      border-color: var(--success);
    }

    .pipeline-dot.done svg {
      fill: white;
    }

    .pipeline-dot.current {
      background: var(--accent);
      border-color: var(--accent);
      animation: pulse 2s ease infinite;
    }

    .pipeline-dot.current svg {
      fill: white;
    }

    .pipeline-dot.rejected {
      background: var(--danger);
      border-color: var(--danger);
    }

    .pipeline-dot.rejected svg {
      fill: white;
    }

    .pipeline-label {
      font-size: 0.65rem;
      font-weight: 600;
      color: var(--text-muted);
      text-align: center;
      max-width: 80px;
    }

    .pipeline-line {
      flex: 1;
      height: 2.5px;
      background: var(--border);
      min-width: 20px;
    }

    .pipeline-line.done {
      background: var(--success);
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(199, 154, 43, 0.4);
      }

      50% {
        box-shadow: 0 0 0 6px rgba(199, 154, 43, 0);
      }
    }

    /* ── Reject modal textarea ── */
    .reject-textarea {
      width: 100%;
      min-height: 80px;
      padding: 10px 12px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      font-family: inherit;
      resize: vertical;
      transition: border-color 0.18s;
    }

    .reject-textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    @media (max-width: 768px) {
      .report-detail-grid {
        grid-template-columns: 1fr;
      }

      .status-pipeline {
        flex-wrap: wrap;
      }
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
            <div class="top-bar-title">Tenant Reports</div>
            <div class="top-bar-subtitle">Application tracking, validation, and lifecycle management</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin') ?>" class="btn-topbar">← Dashboard</a>
          <span id="top-date" style="font-size:0.8rem;color:var(--text-muted);"></span>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value">1,248</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Approved Today</div>
            <div class="insight-value success">5</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending MIS</div>
            <div class="insight-value warning">14</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Rejection Rate</div>
            <div class="insight-value danger">8.2%</div>
          </div>
        </div>
        <!-- BREADCRUMB -->
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/mis_admin') ?>">Dashboard</a><span class="sep">›</span>
          <span class="current">Tenant Reports</span>
        </div>

        <!-- STATS ROW -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon teal"><svg viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-total">0</div>
              <div class="stat-label">Total Reports</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold"><svg viewBox="0 0 24 24">
                <path
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-pending">0</div>
              <div class="stat-label">Pending MIS</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-verified">0</div>
              <div class="stat-label">Verified</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple"><svg viewBox="0 0 24 24">
                <path
                  d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-active">0</div>
              <div class="stat-label">Active</div>
            </div>
          </div>
        </div>

        <!-- RESTRICTION BANNER (MIS scope) -->
        <div class="restriction-banner">
          <svg viewBox="0 0 24 24">
            <path
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
          </svg>
          <div><strong>MIS Admin Scope</strong> — You can view tenant info, uploaded requirements, and approve/reject
            applications. Room assignments and billing are handled by the <strong>Apartment Admin</strong>.</div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
          <input type="text" class="search-input" id="search-reports" placeholder="Search by name or report ID..." />
          <select class="filter-select" id="filter-status">
            <option value="">All Statuses</option>
            <option value="PENDING_MIS">Pending MIS</option>
            <option value="REVISION">Revision</option>
            <option value="VERIFIED">Verified</option>
            <option value="WAITING_LIST">Waiting List</option>
            <option value="APPROVED">Approved</option>
            <option value="ACTIVE">Active</option>
            <option value="DELINQUENT">Delinquent</option>
          </select>
        </div>

        <!-- REPORTS TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z" />
              </svg>Tenant Application Reports</h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="report-count">0 records</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Report #</th>
                    <th>Tenant</th>
                    <th>Requirements</th>
                    <th>Room</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="reports-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- ═══ VIEW REPORT MODAL ═══ -->
  <div class="modal-backdrop" id="modal-view" style="display:none;">
    <div class="modal-content" style="max-width:680px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5><svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);">
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z" />
          </svg>Tenant Report Detail</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body" id="modal-view-body"></div>
    </div>
  </div>

  <!-- ═══ REJECT MODAL ═══ -->
  <div class="modal-backdrop" id="modal-reject" style="display:none;">
    <div class="modal-content" style="max-width:480px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5><svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--danger);">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg>Reject Application</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <p style="font-size:0.87rem;color:var(--text-muted);margin-bottom:12px;">Provide a reason for rejection. The
          tenant will be notified and can resubmit.</p>
        <input type="hidden" id="reject-report-id" />
        <div class="form-group">
          <label class="form-label">Rejection Remarks *</label>
          <textarea class="reject-textarea" id="reject-remarks"
            placeholder="e.g. Missing valid government-issued ID. Please upload and resubmit."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('modal-reject')">Cancel</button>
        <button class="btn-topbar primary" style="background:var(--danger);border-color:var(--danger);"
          onclick="confirmReject()">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg>
          Reject Application
        </button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══════════════════════════════════════
    //  INIT
    // ══════════════════════════════════════
    initAdminData();
    initReportsData();
    setCurrentRole(ROLES.MIS_ADMIN);
    loadUserNav(); setTopBarDate(); initSidebar(); initDropdowns();
    setupModalClose('modal-view');
    setupModalClose('modal-reject');

    let allReports = getReports();
    renderAll();

    // ══════════════════════════════════════
    //  RENDER
    // ══════════════════════════════════════
    function renderAll() {
      allReports = getReports();
      renderStats();
      renderTable(allReports);
    }

    function renderStats() {
      document.getElementById('s-total').textContent = allReports.length;
      document.getElementById('s-pending').textContent = allReports.filter(r => r.status === 'PENDING_MIS').length;
      document.getElementById('s-verified').textContent = allReports.filter(r => r.status === 'VERIFIED').length;
      document.getElementById('s-active').textContent = allReports.filter(r => r.status === 'ACTIVE' || r.status === 'APPROVED').length;
    }

    function renderTable(reports) {
      const tbody = document.getElementById('reports-tbody');
      document.getElementById('report-count').textContent = reports.length + ' records';

      if (!reports.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No reports found.</td></tr>';
        return;
      }

      tbody.innerHTML = reports.map(r => {
        const reqKeys = Object.keys(r.requirements || {});
        const reqDone = reqKeys.filter(k => r.requirements[k]).length;
        const reqTotal = reqKeys.length;
        const reqDots = reqKeys.map(k =>
          `<span class="req-dot ${r.requirements[k] ? 'done' : 'missing'}" title="${k.replace(/_/g, ' ')}: ${r.requirements[k] ? '✓' : '✗'}"></span>`
        ).join('');

        const canApprove = r.status === 'PENDING_MIS';

        return `<tr>
        <td class="td-id">${r.id}</td>
        <td style="font-weight:600;">${r.tenantName}</td>
        <td><div class="req-dots">${reqDots}</div><span style="font-size:0.7rem;color:var(--text-muted);">${reqDone}/${reqTotal}</span></td>
        <td>${r.roomName || '<span style="color:var(--text-muted);font-style:italic;">—</span>'}</td>
        <td><span class="badge-status ${reportBadgeClass(r.status)}">${reportStatusLabel(r.status)}</span></td>
        <td style="color:var(--text-muted);">${formatDate(r.submittedAt)}</td>
        <td>
          <div class="actions-cell">
            <button class="btn-action btn-view" onclick="viewReport('${r.id}')">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/></svg>View
            </button>
            ${canApprove ? `
            <button class="btn-action btn-approve" onclick="handleApprove('${r.id}')">
              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Approve
            </button>
            <button class="btn-action btn-reject" onclick="handleReject('${r.id}')">
              <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>Reject
            </button>` : ''}
          </div>
        </td>
      </tr>`;
      }).join('');
    }

    // ══════════════════════════════════════
    //  VIEW REPORT MODAL
    // ══════════════════════════════════════
    function viewReport(id) {
      const r = allReports.find(x => x.id === id);
      if (!r) return;
      const user = getAllUsers().find(u => u.id === r.tenantId) || {};
      const billing = getBilling().filter(b => r.billingIds.includes(b.id));
      const logs = getActivityLog().filter(l => l.detail && l.detail.includes(r.id));

      // Requirements checklist
      const reqHTML = Object.entries(r.requirements || {}).map(([k, v]) => {
        const label = k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        return `<li>
        <span class="req-check ${v ? 'yes' : 'no'}">
          ${v ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
            : '<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>'}
        </span>
        <span style="color:${v ? 'var(--text-main)' : 'var(--danger)'};">${label}</span>
      </li>`;
      }).join('');

      // Pipeline
      const stages = ['PENDING_MIS', 'VERIFIED', 'APPROVED', 'ACTIVE'];
      const currentIdx = stages.indexOf(r.status);
      const isRejected = r.status === 'REVISION';
      const isWaiting = r.status === 'WAITING_LIST';

      let pipelineHTML = '';
      stages.forEach((s, i) => {
        let dotClass = '';
        if (isRejected && i === 0) dotClass = 'rejected';
        else if (isWaiting && i === 2) dotClass = 'current';
        else if (i < currentIdx) dotClass = 'done';
        else if (i === currentIdx) dotClass = 'current';

        const icon = dotClass === 'done' || dotClass === 'current'
          ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
          : dotClass === 'rejected'
            ? '<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>'
            : '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';

        const label = reportStatusLabel(s);
        pipelineHTML += `<div class="pipeline-step"><div class="pipeline-dot ${dotClass}">${icon}</div><div class="pipeline-label">${isRejected && i === 0 ? 'Revision' : isWaiting && i === 2 ? 'Waiting' : label}</div></div>`;
        if (i < stages.length - 1) {
          pipelineHTML += `<div class="pipeline-line ${i < currentIdx ? 'done' : ''}"></div>`;
        }
      });

      // Billing summary
      let billHTML = '<span style="color:var(--text-muted);font-style:italic;">No billing generated yet.</span>';
      if (billing.length) {
        billHTML = billing.map(b => `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(0,0,0,0.04);">
          <div>
            <div style="font-weight:600;font-size:0.85rem;">${b.id} — ${b.type}</div>
            <div style="font-size:0.73rem;color:var(--text-muted);">Due: ${formatDate(b.dueDate)}</div>
          </div>
          <div style="text-align:right;">
            <div style="font-weight:700;font-size:0.95rem;">${currencyFormat(b.amount)}</div>
            <span class="badge-status ${badgeClass(b.status)}" style="font-size:0.65rem;">${statusLabel(b.status)}</span>
          </div>
        </div>
      `).join('');
      }

      // Activity mini-timeline
      let logsHTML = '';
      if (logs.length) {
        logsHTML = logs.slice(0, 5).map(l => `
        <div class="mini-tl-item">
          <div class="tl-action">${l.action}</div>
          <div class="tl-time">${formatDateTime(l.time)} — ${l.actor}</div>
        </div>
      `).join('');
      } else {
        logsHTML = '<span style="color:var(--text-muted);font-size:0.82rem;">No activity logged yet.</span>';
      }

      document.getElementById('modal-view-body').innerHTML = `
      <!-- Status Pipeline -->
      <div class="status-pipeline">${pipelineHTML}</div>

      <div class="report-detail-grid">
        <!-- Left Column -->
        <div>
          <div class="detail-block">
            <div class="detail-block-label">Tenant Information</div>
            <div class="detail-block-value" style="font-weight:700;font-size:1rem;margin-bottom:4px;">${r.tenantName}</div>
            <div style="font-size:0.82rem;color:var(--text-muted);">
              ID: ${r.tenantId}<br/>
              Email: ${user.email || '—'}<br/>
              Phone: ${user.phone || '—'}<br/>
              Gender: ${user.gender || '—'}<br/>
              Profile: <strong style="color:${(user.profilePct || 0) >= 100 ? 'var(--success)' : 'var(--warning)'};">${user.profilePct || 0}%</strong>
            </div>
          </div>
          <div class="detail-block">
            <div class="detail-block-label">Room Assignment</div>
            <div class="detail-block-value">${r.roomName || '<span style="color:var(--text-muted);font-style:italic;">Not assigned</span>'}</div>
            ${r.roomId ? `<div style="font-size:0.78rem;color:var(--text-muted);">Unit ID: ${r.roomId}</div>` : ''}
          </div>
          <div class="detail-block">
            <div class="detail-block-label">Key Dates</div>
            <div style="font-size:0.82rem;color:var(--text-muted);">
              Submitted: <strong>${formatDate(r.submittedAt)}</strong><br/>
              Verified: <strong>${r.verifiedAt ? formatDate(r.verifiedAt) : '—'}</strong><br/>
              Approved: <strong>${r.approvedAt ? formatDate(r.approvedAt) : '—'}</strong>
            </div>
          </div>
          ${r.remarks ? `<div class="detail-block"><div class="detail-block-label">Remarks</div><div class="detail-block-value" style="padding:10px 12px;background:rgba(199,154,43,0.06);border-radius:8px;border-left:3px solid var(--accent);font-size:0.84rem;">${r.remarks}</div></div>` : ''}
        </div>
        <!-- Right Column -->
        <div>
          <div class="detail-block">
            <div class="detail-block-label">Requirements</div>
            <ul class="req-checklist">${reqHTML}</ul>
          </div>
          <div class="detail-block">
            <div class="detail-block-label">Billing Summary</div>
            ${billHTML}
          </div>
          <div class="detail-block">
            <div class="detail-block-label">Activity Log</div>
            <div class="mini-timeline">${logsHTML}</div>
          </div>
        </div>
      </div>
    `;
      openModal('modal-view');
    }

    // ══════════════════════════════════════
    //  APPROVE / REJECT
    // ══════════════════════════════════════
    function handleApprove(id) {
      if (!confirm('Approve this application? It will be forwarded to the Apartment Admin for room assignment.')) return;
      const result = approveReport(id);
      if (result) {
        showToast('✅ Application verified and forwarded to Apartment Admin.', 'var(--success)');
        renderAll();
      } else {
        showToast('Cannot approve this report — it may have already been processed.', 'var(--danger)');
      }
    }

    function handleReject(id) {
      document.getElementById('reject-report-id').value = id;
      document.getElementById('reject-remarks').value = '';
      openModal('modal-reject');
    }

    function confirmReject() {
      const id = document.getElementById('reject-report-id').value;
      const remarks = document.getElementById('reject-remarks').value.trim();
      if (!remarks) { showToast('Please provide rejection remarks.', 'var(--danger)'); return; }
      const result = rejectReport(id, remarks);
      if (result) {
        closeModal('modal-reject');
        showToast('❌ Application rejected. Tenant has been notified.', 'var(--danger)');
        renderAll();
      }
    }

    // ══════════════════════════════════════
    //  SEARCH & FILTER
    // ══════════════════════════════════════
    document.getElementById('search-reports').addEventListener('input', applyFilters);
    document.getElementById('filter-status').addEventListener('change', applyFilters);

    function applyFilters() {
      const q = document.getElementById('search-reports').value.toLowerCase();
      const status = document.getElementById('filter-status').value;
      let filtered = allReports;
      if (q) filtered = filtered.filter(r => r.tenantName.toLowerCase().includes(q) || r.id.toLowerCase().includes(q));
      if (status) filtered = filtered.filter(r => r.status === status);
      renderTable(filtered);
    }
  </script>
</body>

</html>