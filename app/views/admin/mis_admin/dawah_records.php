<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Records</title>
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
            <div class="top-bar-title">Da'wah Records</div>
            <div class="top-bar-subtitle">Manage counseling, Islamic education, and marriage service requests</div>
          </div>
        </div>
        <div class="top-bar-actions"><a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a></div>
      </div>
      <div class="page-body">
        
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a><span class="sep">›</span><span class="current">Da'wah
            Records</span>
        </div>

        <!-- STATS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-counsel">0</div>
              <div class="stat-label">Counseling</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon teal"><svg viewBox="0 0 24 24">
                <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-edu">0</div>
              <div class="stat-label">Education</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background:#c75a7a;"><svg viewBox="0 0 24 24">
                <path
                  d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-mar">0</div>
              <div class="stat-label">Marriage</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold"><svg viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-pending">0</div>
              <div class="stat-label">Pending Total</div>
            </div>
          </div>
        </div>

        <!-- FILTER -->
        <div class="filter-bar">
          <input type="text" class="search-input" id="search" placeholder="Search by name or ref #..."
            oninput="filterTable()" />
          <select class="filter-select" id="type-filter" onchange="filterTable()">
            <option value="all">All Services</option>
            <option value="counseling">Counseling</option>
            <option value="islamic_education">Islamic Education</option>
            <option value="marriage_service">Marriage</option>
            <option value="conversion">Shahadah Conversion</option>
          </select>
          <select class="filter-select" id="status-filter" onchange="filterTable()">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="scheduled">Scheduled</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
          </select>
        </div>

        <!-- REQUEST TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z" />
              </svg>Da'wah Service Requests</h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="req-count">0 records</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Name</th>
                    <th>Service Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="req-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- IN-PAGE MIS ADMIN REVIEW MODAL (stays inside MIS Admin) -->
  <div id="dawah-review-modal" class="mis-review-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:2000;align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this) closeDawahReviewModal()">
    <div style="background:var(--bg-card,#fff);border-radius:16px;max-width:600px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 48px rgba(0,0,0,0.22);border:1px solid var(--border-color,#e2e8f0);">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border-color,#e2e8f0);">
        <div>
          <div style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--primary,#1a5632);">MIS Admin • Da'wah Record Review</div>
          <h4 id="dawah-mdl-title" style="margin:3px 0 0;font-size:1.08rem;font-weight:800;color:var(--text-main);">Record Details</h4>
        </div>
        <button type="button" onclick="closeDawahReviewModal()" style="background:none;border:none;font-size:1.5rem;line-height:1;cursor:pointer;color:var(--text-muted);padding:4px 8px;" aria-label="Close modal">&times;</button>
      </div>
      <div id="dawah-mdl-body" style="padding:20px 22px;font-size:0.88rem;color:var(--text-main);"></div>
      <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 22px;border-top:1px solid var(--border-color,#e2e8f0);background:var(--bg-body,#f8fafc);border-radius:0 0 16px 16px;">
        <a href="<?= url('/admin/mis_admin/reports?dept=Da%27wah') ?>" class="card-badge" style="text-decoration:none;padding:8px 14px;">Department Report</a>
        <button type="button" onclick="closeDawahReviewModal()" style="background:var(--primary,#1a5632);color:#fff;border:none;border-radius:8px;padding:8px 18px;font-weight:700;cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');
    const dbDawahReqs = <?= json_encode($dawahRequests ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const DAWAH_TYPES = ['counseling', 'islamic_education', 'marriage_service', 'conversion'];
    let allReqs = (dbDawahReqs && dbDawahReqs.length > 0) ? dbDawahReqs : getRequests().filter(r => DAWAH_TYPES.some(t => (r.type||'').includes(t)));

    function escHtml(v) {
      return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // Stats
    document.getElementById('s-counsel').textContent = allReqs.filter(r => (r.type || '').includes('counseling')).length;
    document.getElementById('s-edu').textContent = allReqs.filter(r => (r.type || '').includes('education')).length;
    document.getElementById('s-mar').textContent = allReqs.filter(r => (r.type || '').includes('marriage')).length;
    document.getElementById('s-pending').textContent = allReqs.filter(r => (r.status || '').toLowerCase().includes('pend')).length;

    function renderTable(data) {
      document.getElementById('req-count').textContent = data.length + ' records';
      const tbody = document.getElementById('req-tbody');
      if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:28px;color:var(--text-muted);">No records found.</td></tr>';
        return;
      }
      tbody.innerHTML = data.map(r => `<tr data-id="${escHtml(r.id)}" data-type="${escHtml(r.type)}">
      <td class="td-id">#${escHtml(r.id)}</td>
      <td style="font-weight:600;">${escHtml(r.name || 'Unknown')}</td>
      <td><span style="font-weight:600;color:var(--text-main);">${escHtml(r.service_type || typeLabel(r.type))}</span></td>
      <td>${formatDate(r.date)}</td>
      <td><span class="badge-status ${badgeClass(r.status)}">${statusLabel(r.status)}</span></td>
      <td>
        <button type="button" class="card-badge" style="border:none;cursor:pointer;" onclick="openDawahReviewModal('${escHtml(r.type || '')}', '${escHtml(r.id)}')">Review</button>
      </td>
    </tr>`).join('');
    }

    function openDawahReviewModal(type, id) {
      const r = allReqs.find(x => String(x.id) === String(id) && (!type || String(x.type) === String(type)))
             || allReqs.find(x => String(x.id) === String(id));
      if (!r) return;

      document.getElementById('dawah-mdl-title').textContent = `#${r.id} — ${r.service_type || typeLabel(r.type)}`;
      const rows = [
        ['Applicant / Member', r.name || 'Unknown'],
        ['Contact / Email', r.email || '—'],
        ['Service Category', r.service_type || typeLabel(r.type)],
        ['Status', `<span class="badge-status ${badgeClass(r.status)}">${statusLabel(r.status)}</span>`],
        ['Submitted On', formatDate(r.date)]
      ];
      if (r.gender) rows.push(['Gender Track', r.gender]);
      if (r.session_type) rows.push(['Session Mode', r.session_type]);
      if (r.reason) rows.push(['Topic / Reason', r.reason]);
      if (r.groom_name || r.bride_name) rows.push(['Couple', `${r.groom_name || '—'} & ${r.bride_name || '—'}`]);
      if (r.adopted_name) rows.push(['Adopted Name', r.adopted_name]);
      if (r.former_religion) rows.push(['Former Religion', r.former_religion]);
      if (r.program_name || r.level) rows.push(['Program / Level', [r.program_name, r.level].filter(Boolean).join(' — ')]);
      if (r.batch_no) rows.push(['Batch', r.batch_no]);
      if (r.progress_percent !== undefined && r.progress_percent !== null && r.progress_percent !== '') {
        const sess = (r.total_sessions) ? ` (${r.sessions_attended || 0}/${r.total_sessions} sessions)` : '';
        rows.push(['Learning Progress', `${r.progress_percent}%${sess}`]);
      }
      if (r.payment_status) rows.push(['Payment Status', r.payment_status]);
      if (r.scheduled_date || r.scheduled_time) rows.push(['Scheduled Date/Time', [r.scheduled_date, r.scheduled_time].filter(Boolean).join(' • ')]);
      if (r.location) rows.push(['Venue / Location', r.location]);
      if (r.handler_name) rows.push(['Assigned Personnel', r.handler_name]);
      if (r.details) rows.push(['Notes / Details', r.details]);

      document.getElementById('dawah-mdl-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr;gap:10px;">
          ${rows.map(([k, v]) => `
            <div style="display:flex;justify-content:space-between;gap:14px;padding:8px 0;border-bottom:1px dashed var(--border-color,#e2e8f0);">
              <span style="color:var(--text-muted);font-weight:600;min-width:140px;">${escHtml(k)}</span>
              <span style="text-align:right;font-weight:600;word-break:break-word;">${k === 'Status' ? v : escHtml(v)}</span>
            </div>
          `).join('')}
        </div>`;
      document.getElementById('dawah-review-modal').style.display = 'flex';
    }

    function closeDawahReviewModal() {
      document.getElementById('dawah-review-modal').style.display = 'none';
    }

    function filterTable() {
      const search = document.getElementById('search').value.toLowerCase();
      const type = document.getElementById('type-filter').value;
      const status = document.getElementById('status-filter').value;
      let filtered = allReqs;
      if (search) filtered = filtered.filter(r => (r.name || '').toLowerCase().includes(search) || String(r.id || '').toLowerCase().includes(search));
      if (type !== 'all') filtered = filtered.filter(r => (r.type || '').includes(type));
      if (status !== 'all') filtered = filtered.filter(r => (r.status || '').toLowerCase().includes(status));
      renderTable(filtered);
    }

    // Apply URL query filters (?type=...&id=...) so MIS Admin Review opens in-place
    (function initDawahParams() {
      const params = new URLSearchParams(window.location.search);
      const qType = params.get('type');
      const qId = params.get('id');
      const typeSel = document.getElementById('type-filter');
      if (qType && typeSel && Array.from(typeSel.options).some(o => o.value === qType)) {
        typeSel.value = qType;
      }
      filterTable();
      if (qId) {
        openDawahReviewModal(qType || '', qId);
      }
    })();
  </script>
</body>

</html>

