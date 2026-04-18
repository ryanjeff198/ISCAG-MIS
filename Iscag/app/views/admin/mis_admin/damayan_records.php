<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Records</title>
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/ISCAG_Logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">Damayan Records</div>
            <div class="top-bar-subtitle">Manage burial service requests, scheduling, and documentation</div>
          </div>
        </div>
        <div class="top-bar-actions"><a href="<?= url('/admin/mis_admin') ?>" class="btn-topbar">← Dashboard</a></div>
      </div>
      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Requests</div>
            <div class="insight-value info" id="stat-total-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Reviews</div>
            <div class="insight-value warning" id="stat-pending-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Completed Services</div>
            <div class="insight-value success" id="stat-completed-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Avg. Response</div>
            <div class="insight-value">1.5h</div>
          </div>
        </div>
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/mis_admin') ?>">MIS Admin</a><span class="sep">›</span><span class="current">Damayan
            Records</span>
        </div>

        <!-- STATS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon purple"><svg viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-total">0</div>
              <div class="stat-label">Total Requests</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold"><svg viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-pending">0</div>
              <div class="stat-label">Pending</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-completed">0</div>
              <div class="stat-label">Completed</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue"><svg viewBox="0 0 24 24">
                <path
                  d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-billing">0</div>
              <div class="stat-label">Billing</div>
            </div>
          </div>
        </div>

        <!-- BURIAL REQUESTS -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
              </svg>Burial Service Requests</h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="req-count">0 records</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Requestor</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="req-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- BILLING -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path
                  d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
              </svg>Burial Service Billing</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Bill ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="bill-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    initAdminData(); loadUserNav();
    const reqs = getRequests().filter(r => r.type === 'burial_service');
    const bills = getBilling().filter(b => b.type.toLowerCase().includes('burial'));

    document.getElementById('s-total').textContent = reqs.length;
    document.getElementById('s-pending').textContent = reqs.filter(r => r.status === 'pending').length;
    document.getElementById('s-completed').textContent = reqs.filter(r => r.status === 'completed').length;
    document.getElementById('s-billing').textContent = bills.length;
    document.getElementById('req-count').textContent = reqs.length + ' records';

    // Requests
    const tbody = document.getElementById('req-tbody');
    if (!reqs.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:28px;color:var(--text-muted);">No burial requests found.</td></tr>'; }
    else {
      tbody.innerHTML = reqs.map(r => `<tr>
    <td class="td-id">${r.id}</td><td style="font-weight:600;">${r.name || 'Unknown'}</td>
    <td>${formatDate(r.date)}</td><td><span class="badge-status ${badgeClass(r.status)}">${statusLabel(r.status)}</span></td>
    <td style="color:var(--text-muted);">${formatDate(r.updatedAt)}</td>
    <td class="actions-cell">
      <button class="btn-action btn-approve" onclick="approveReq('${r.id}')"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Approve</button>
      <button class="btn-action btn-reject" onclick="rejectReq('${r.id}')"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>Reject</button>
    </td>
  </tr>`).join('');
    }

    // Billing
    const billTb = document.getElementById('bill-tbody');
    if (!bills.length) { billTb.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text-muted);">No billing records.</td></tr>'; }
    else {
      billTb.innerHTML = bills.map(b => `<tr>
    <td class="td-id">${b.id}</td><td style="font-weight:600;">${b.name}</td>
    <td style="font-weight:700;">${currencyFormat(b.amount)}</td>
    <td style="color:var(--text-muted);">${formatDate(b.dueDate)}</td>
    <td><span class="badge-status ${badgeClass(b.status)}">${statusLabel(b.status)}</span></td>
  </tr>`).join('');
    }

    function approveReq(id) {
      const all = getRequests(); const r = all.find(x => x.id === id);
      if (r) { r.status = 'approved'; r.updatedAt = new Date().toISOString().split('T')[0]; saveRequests(all); addActivityEntry('Burial approved', id + ' approved by MIS Admin', 'MIS Admin', 'approve'); showToast('✅ ' + id + ' approved!', 'var(--success)'); location.reload(); }
    }
    function rejectReq(id) {
      const all = getRequests(); const r = all.find(x => x.id === id);
      if (r) { r.status = 'rejected'; r.updatedAt = new Date().toISOString().split('T')[0]; saveRequests(all); addActivityEntry('Burial rejected', id + ' rejected by MIS Admin', 'MIS Admin', 'reject'); showToast('❌ ' + id + ' rejected.', 'var(--danger)'); location.reload(); }
    }

    initSidebar(); initDropdowns();
  </script>
</body>

</html>