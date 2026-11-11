<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Records</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          
          <div>
            <div class="top-bar-title">Damayan Records</div>
            <div class="top-bar-subtitle">Manage burial service requests, scheduling, and documentation</div>
          </div>
        </div>
        <div class="top-bar-actions"><a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a></div>
      </div>
      <div class="page-body">
        
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a><span class="sep">›</span><span class="current">Damayan
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

  <!-- IN-PAGE MIS ADMIN REVIEW MODAL (stays inside MIS Admin) -->
  <div id="damayan-review-modal" class="mis-review-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:2000;align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this) closeDamayanReviewModal()">
    <div style="background:var(--bg-card,#fff);border-radius:16px;max-width:620px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 48px rgba(0,0,0,0.22);border:1px solid var(--border-color,#e2e8f0);">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border-color,#e2e8f0);">
        <div>
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--primary,#1a5632);">MIS Admin • Damayan Record Review</div>
          <h4 id="damayan-mdl-title" style="margin:3px 0 0;font-size:1.08rem;font-weight:800;color:var(--text-main);">Record Details</h4>
        </div>
        <button type="button" onclick="closeDamayanReviewModal()" style="background:none;border:none;font-size:1.5rem;line-height:1;cursor:pointer;color:var(--text-muted);padding:4px 8px;" aria-label="Close modal">&times;</button>
      </div>
      <div id="damayan-mdl-body" style="padding:20px 22px;font-size:0.88rem;color:var(--text-main);"></div>
      <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid var(--border-color,#e2e8f0);background:var(--bg-body,#f8fafc);border-radius:0 0 16px 16px;">
        <a href="<?= url('/admin/mis_admin/reports?dept=Damayan') ?>" class="card-badge" style="text-decoration:none;padding:8px 14px;">Department Report</a>
        <button type="button" onclick="closeDamayanReviewModal()" style="background:var(--primary,#1a5632);color:#fff;border:none;border-radius:8px;padding:8px 18px;font-weight:700;cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');
    const dbDamayanReqs = <?= json_encode($damayanRequests ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const reqs = (dbDamayanReqs && dbDamayanReqs.length > 0) ? dbDamayanReqs : getRequests().filter(r => r.type === 'burial_service');
    const bills = getBilling().filter(b => b.type.toLowerCase().includes('burial'));

    function escHtml(v) {
      return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    document.getElementById('s-total').textContent = reqs.length;
    document.getElementById('s-pending').textContent = reqs.filter(r => (r.status || '').toLowerCase().includes('pend') || (r.status || '').toLowerCase().includes('submit')).length;
    document.getElementById('s-completed').textContent = reqs.filter(r => (r.status || '').toLowerCase().includes('complet') || (r.status || '').toLowerCase().includes('releas')).length;
    document.getElementById('s-billing').textContent = bills.length;
    document.getElementById('req-count').textContent = reqs.length + ' records';

    // Requests
    const tbody = document.getElementById('req-tbody');
    if (!reqs.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:28px;color:var(--text-muted);">No burial or funeral requests found.</td></tr>';
    } else {
      tbody.innerHTML = reqs.map(r => `<tr data-id="${escHtml(r.id)}" data-kind="${escHtml(r.record_kind || 'burial')}">
        <td class="td-id">${escHtml(r.ref_code || ('#' + r.id))}</td>
        <td style="font-weight:600;">${escHtml(r.name || 'Unknown')} <div style="font-size:0.72rem;color:var(--text-muted);">${escHtml(r.service_type || 'Burial Service')}</div></td>
        <td>${formatDate(r.date)}</td>
        <td><span class="badge-status ${badgeClass(r.status)}">${statusLabel(r.status)}</span></td>
        <td style="color:var(--text-muted);font-size:0.8rem;">${escHtml(r.plot_number || 'Plot: Pending')} (${escHtml(r.cemetery_pref || 'Local')})</td>
        <td>
          <button type="button" class="card-badge" style="border:none;cursor:pointer;" onclick="openDamayanReviewModal('${escHtml(r.record_kind || 'burial')}', '${escHtml(r.id)}')">Review</button>
        </td>
      </tr>`).join('');
    }

    function openDamayanReviewModal(kind, id) {
      const r = reqs.find(x => String(x.id) === String(id) && (!kind || String(x.record_kind || 'burial') === String(kind)))
             || reqs.find(x => String(x.id) === String(id));
      if (!r) return;

      const refDisplay = r.ref_code || ('#' + r.id);
      document.getElementById('damayan-mdl-title').textContent = `${refDisplay} — ${r.service_type || 'Damayan Service'}`;
      const rows = [
        ['Deceased / Record Name', r.name || 'Unknown'],
        ['Service Type', r.service_type || 'Burial Service'],
        ['Informant / Requestor', r.informant_name || '—'],
        ['Informant Contact', r.contact || '—'],
        ['Status', `<span class="badge-status ${badgeClass(r.status)}">${statusLabel(r.status)}</span>`],
        ['Submitted / Reported', formatDate(r.date)]
      ];
      if (r.request_type) rows.push(['Request / Certificate Type', r.request_type]);
      if (r.date_of_death) rows.push(['Date of Death', r.date_of_death]);
      if (r.place_of_death) rows.push(['Place of Death', r.place_of_death]);
      if (r.requested_services) rows.push(['Requested Services / Location', r.requested_services]);
      rows.push(['Cemetery & Plot', `${r.plot_number || 'Pending'} (${r.cemetery_pref || 'Local'})`]);
      if (r.death_cert_status) rows.push(['Certificate Status', r.death_cert_status]);
      if (r.cemetery_plot_status) rows.push(['Plot Assignment Status', r.cemetery_plot_status]);
      if (r.scheduled_release_date) rows.push(['Scheduled Burial / Release', r.scheduled_release_date]);
      if (r.assigned_staff_name) rows.push(['Assigned Staff', r.assigned_staff_name]);
      if (r.details) rows.push(['Notes / Remarks', r.details]);

      document.getElementById('damayan-mdl-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr;gap:10px;">
          ${rows.map(([k, v]) => `
            <div style="display:flex;justify-content:space-between;gap:14px;padding:8px 0;border-bottom:1px dashed var(--border-color,#e2e8f0);">
              <span style="color:var(--text-muted);font-weight:600;min-width:155px;">${escHtml(k)}</span>
              <span style="text-align:right;font-weight:600;word-break:break-word;">${k === 'Status' ? v : escHtml(v)}</span>
            </div>
          `).join('')}
        </div>`;
      document.getElementById('damayan-review-modal').style.display = 'flex';
    }

    function closeDamayanReviewModal() {
      document.getElementById('damayan-review-modal').style.display = 'none';
    }

    // Billing
    const billTb = document.getElementById('bill-tbody');
    if (!bills.length) {
      billTb.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text-muted);">No billing records.</td></tr>';
    } else {
      billTb.innerHTML = bills.map(b => `<tr>
        <td class="td-id">${b.id}</td><td style="font-weight:600;">${b.name}</td>
        <td style="font-weight:700;">${currencyFormat(b.amount)}</td>
        <td style="color:var(--text-muted);">${formatDate(b.dueDate)}</td>
        <td><span class="badge-status ${badgeClass(b.status)}">${statusLabel(b.status)}</span></td>
      </tr>`).join('');
    }

    // Auto-open review modal when navigated from MIS Admin Dashboard (?type=...&id=...)
    (function initDamayanParams() {
      const params = new URLSearchParams(window.location.search);
      const qType = params.get('type') || '';
      const qId = params.get('id');
      if (qId) {
        openDamayanReviewModal(qType, qId);
      }
    })();
  </script>
</body>

</html>

