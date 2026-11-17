<?php
$active_page = 'damayan_records';
$damayanRequests = $damayanRequests ?? [];
$burials = $burials ?? [];
$stats = $stats ?? [
    'totalRequests' => count($damayanRequests),
    'pendingCount' => 0,
    'acceptedCount' => 0,
    'completedCount' => 0
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan & Burial Records</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .damayan-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      padding: 14px 18px;
      background: #ffffff;
      border-bottom: 1px solid var(--border);
    }
    .damayan-toolbar-left,
    .damayan-toolbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .pill-tabs {
      display: inline-flex;
      background: var(--surface-hover, #f1f5f3);
      padding: 3px;
      border-radius: 8px;
      border: 1px solid var(--border);
    }
    .pill-tab {
      border: none;
      background: transparent;
      padding: 6px 14px;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.15s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .pill-tab.active {
      background: #ffffff;
      color: var(--primary);
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .toolbar-search {
      position: relative;
    }
    .toolbar-search input {
      height: 34px;
      padding: 0 12px 0 32px;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-size: 0.82rem;
      width: 250px;
      background: #ffffff;
      color: var(--text-main);
    }
    .toolbar-search svg {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      width: 14px;
      height: 14px;
      fill: var(--text-muted);
      pointer-events: none;
    }
    .toolbar-select {
      height: 34px;
      padding: 0 10px;
      border: 1px solid var(--border);
      border-radius: 6px;
      font-size: 0.82rem;
      background: #ffffff;
      color: var(--text-main);
    }
    .td-stacked {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .td-sub {
      font-size: 0.74rem;
      color: var(--text-muted);
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Damayan & Burial Service Records</div>
            <div class="top-bar-subtitle">Civil registration and Islamic burial services assistance</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin/reports?dept=Damayan') ?>" class="btn-topbar primary">Department Report</a>
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a>
          <span class="sep">›</span>
          <span class="current">Damayan Burial</span>
        </div>

        <!-- STATS ROW (Accurate metrics directly from database) -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon purple">
              <svg viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-total"><?= (int)$stats['totalRequests'] ?></div>
              <div class="stat-label">Total Requests</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-pending"><?= (int)$stats['pendingCount'] ?></div>
              <div class="stat-label">Pending Review</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon blue">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-accepted"><?= (int)($stats['acceptedCount'] ?? 0) ?></div>
              <div class="stat-label">Accepted / In-Progress</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-completed"><?= (int)$stats['completedCount'] ?></div>
              <div class="stat-label">Completed & Released</div>
            </div>
          </div>
        </div>

        <!-- ══════════════ DAMAYAN CASE REGISTRY ══════════════ -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
              </svg>
              Damayan Burial Requests
            </h6>
            <div class="pill-tabs" id="damayanViewSwitch">
              <button type="button" class="pill-tab active" data-view="all">All Requests (<?= count($damayanRequests) ?>)</button>
              <button type="button" class="pill-tab" data-view="pending">Pending (<?= (int)$stats['pendingCount'] ?>)</button>
              <button type="button" class="pill-tab" data-view="accepted">Accepted (<?= (int)($stats['acceptedCount'] ?? 0) ?>)</button>
              <button type="button" class="pill-tab" data-view="completed">Completed (<?= (int)$stats['completedCount'] ?>)</button>
            </div>
          </div>

          <div class="damayan-toolbar">
            <div class="damayan-toolbar-left">
              <div class="toolbar-search">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="damayan-search" placeholder="Search ref, deceased, informant..." />
              </div>
              <select class="toolbar-select" id="damayan-status-filter">
                <option value="">All Statuses</option>
                <option value="Pending">Pending / Under Review</option>
                <option value="Accepted">Accepted</option>
                <option value="Completed">Completed / Released</option>
                <option value="Rejected">Rejected</option>
              </select>
            </div>
            <div class="damayan-toolbar-right" id="damayan-view-counter" style="font-size:0.8rem;color:var(--text-muted);">
              Total: <?= count($damayanRequests) ?> real records
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="damayan-table" data-paginate="false">
                <thead id="damayan-thead">
                  <!-- Populated dynamically based on active tab -->
                </thead>
                <tbody id="damayan-tbody">
                  <!-- Populated dynamically -->
                </tbody>
              </table>
            </div>
            <!-- Pagination Controls (10 per page) -->
            <div id="damayan-pagination" class="mis-pagination-bar"></div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- IN-PAGE MIS ADMIN REVIEW MODAL (Accurate DB Data) -->
  <div id="damayan-review-modal" class="mis-review-overlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);z-index:2000;align-items:center;justify-content:center;padding:16px;" onclick="if(event.target===this) closeDamayanReviewModal()">
    <div style="background:var(--bg-card,#fff);border-radius:16px;max-width:650px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 48px rgba(0,0,0,0.22);border:1px solid var(--border-color,#e2e8f0);">
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
    // ══════════════════════════════════════════════════════════
    // REAL DATABASE RECORDS (Accurate data, zero mock)
    // ══════════════════════════════════════════════════════════
    const RAW_REQUESTS = <?= json_encode($damayanRequests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const PAGE_SIZE = 10;
    let currentView = 'all'; // 'all', 'pending', 'accepted', 'completed'
    let currentPage = 1;

    function escHtml(v) {
      return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function formatDisplayDate(dateStr) {
      if (!dateStr) return '—';
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) return dateStr;
      return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    function formatDisplayDateTime(dateStr) {
      if (!dateStr) return '—';
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) return dateStr;
      return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) +
        ' ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    function getBadgeHtml(status) {
      const s = (status || '').toLowerCase();
      if (s.includes('complet') || s.includes('releas') || s === 'registered') {
        return `<span class="badge-status badge-approved">${escHtml(status)}</span>`;
      }
      if (s.includes('accept') || s.includes('verified')) {
        return `<span class="badge-status badge-approved" style="background:#e0f2fe;color:#0369a1;border-color:#bae6fd;">${escHtml(status)}</span>`;
      }
      if (s.includes('reject') || s.includes('returned')) {
        return `<span class="badge-status badge-rejected">${escHtml(status)}</span>`;
      }
      return `<span class="badge-status badge-pending">${escHtml(status || 'Pending')}</span>`;
    }

    function buildPaginationControls(curr, total) {
      const container = document.getElementById('damayan-pagination');
      if (!container) return;

      if (total <= 1) {
        container.querySelector('.mis-pagination-controls')?.remove();
        return;
      }

      let btns = `<button type="button" class="mis-page-btn" data-act="prev" ${curr === 1 ? 'disabled' : ''}>&laquo; Prev</button>`;

      const pages = [];
      if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
      } else {
        pages.push(1);
        if (curr > 3) pages.push('...');
        const start = Math.max(2, curr - 1);
        const end = Math.min(total - 1, curr + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (curr < total - 2) pages.push('...');
        pages.push(total);
      }

      pages.forEach(p => {
        if (p === '...') {
          btns += `<span class="mis-page-ellipsis">&hellip;</span>`;
        } else {
          btns += `<button type="button" class="mis-page-btn ${p === curr ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }
      });

      btns += `<button type="button" class="mis-page-btn" data-act="next" ${curr === total ? 'disabled' : ''}>Next &raquo;</button>`;

      container.querySelector('.mis-pagination-controls')?.remove();
      const ctrlDiv = document.createElement('div');
      ctrlDiv.className = 'mis-pagination-controls';
      ctrlDiv.innerHTML = btns;

      ctrlDiv.querySelectorAll('.mis-page-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (btn.disabled) return;
          const page = btn.getAttribute('data-page');
          const act = btn.getAttribute('data-act');
          if (page) {
            currentPage = parseInt(page, 10);
            renderTable(false);
          } else if (act === 'prev' && currentPage > 1) {
            currentPage--;
            renderTable(false);
          } else if (act === 'next' && currentPage < total) {
            currentPage++;
            renderTable(false);
          }
        });
      });

      container.appendChild(ctrlDiv);
    }

    function renderTable(resetPage = false) {
      if (resetPage) currentPage = 1;

      const searchTerm = (document.getElementById('damayan-search').value || '').toLowerCase().trim();
      const statusFilter = (document.getElementById('damayan-status-filter').value || '').toLowerCase().trim();
      const thead = document.getElementById('damayan-thead');
      const tbody = document.getElementById('damayan-tbody');
      const paginationBar = document.getElementById('damayan-pagination');
      const counterLabel = document.getElementById('damayan-view-counter');

      thead.innerHTML = `
        <tr>
          <th>Ref / Request #</th>
          <th>Service Type</th>
          <th>Deceased Full Name</th>
          <th>Informant / Contact</th>
          <th>Submitted Date</th>
          <th>Civil Cert Status</th>
          <th>Status</th>
          <th style="text-align:center;">Action</th>
        </tr>
      `;

      const filtered = RAW_REQUESTS.filter(r => {
        const st = (r.status || '').toLowerCase();
        if (currentView === 'pending') {
          if (!st.includes('pend') && !st.includes('submit') && !st.includes('request') && !st.includes('review')) return false;
        } else if (currentView === 'accepted') {
          if (!st.includes('accept') && !st.includes('verified')) return false;
        } else if (currentView === 'completed') {
          if (!st.includes('complet') && !st.includes('releas') && st !== 'registered') return false;
        }

        const matchSearch = !searchTerm ||
          (r.ref_code && r.ref_code.toLowerCase().includes(searchTerm)) ||
          (r.name && r.name.toLowerCase().includes(searchTerm)) ||
          (r.informant_name && r.informant_name.toLowerCase().includes(searchTerm)) ||
          (r.service_type && r.service_type.toLowerCase().includes(searchTerm));

        let matchStatus = true;
        if (statusFilter) {
          if (statusFilter === 'pending') matchStatus = st.includes('pend') || st.includes('submit') || st.includes('request') || st.includes('review');
          else if (statusFilter === 'completed') matchStatus = st.includes('complet') || st.includes('releas') || st === 'registered';
          else if (statusFilter === 'accepted') matchStatus = st.includes('accept') || st.includes('verified');
          else if (statusFilter === 'rejected') matchStatus = st.includes('reject') || st.includes('returned');
        }
        return matchSearch && matchStatus;
      });

      const totalItems = filtered.length;
      const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;
      if (currentPage > totalPages) currentPage = totalPages;

      const startIdx = (currentPage - 1) * PAGE_SIZE;
      const endIdx = Math.min(startIdx + PAGE_SIZE, totalItems);
      const pageItems = filtered.slice(startIdx, endIdx);

      counterLabel.textContent = `Showing: ${totalItems} real records`;

      if (totalItems === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:36px;color:var(--text-muted);">No records found matching the filter criteria.</td></tr>`;
        paginationBar.innerHTML = `<div class="mis-pagination-info">Showing 0 of 0 records</div>`;
        return;
      }

      tbody.innerHTML = pageItems.map(r => {
        const refDisplay = r.ref_code || ('#' + r.id);
        const contactDisplay = r.contact ? `<div class="td-sub">${escHtml(r.contact)}</div>` : '';
        const certStatusDisplay = r.death_cert_status
          ? `<span style="font-size:0.78rem;font-weight:600;">${escHtml(r.death_cert_status)}</span>`
          : `<span style="color:var(--text-muted);font-size:0.78rem;">—</span>`;

        return `
          <tr>
            <td class="td-id" style="font-weight:700;">${escHtml(refDisplay)}</td>
            <td>
              <span class="card-badge" style="font-size:0.75rem;padding:3px 8px;border-radius:4px;">
                ${escHtml(r.service_type || 'Burial Service')}
              </span>
            </td>
            <td style="font-weight:600;">${escHtml(r.name || 'Unknown')}</td>
            <td>
              <div class="td-stacked">
                <span style="font-weight:600;">${escHtml(r.informant_name || '—')}</span>
                ${contactDisplay}
              </div>
            </td>
            <td style="color:var(--text-muted);font-size:0.82rem;">${formatDisplayDateTime(r.date)}</td>
            <td>${certStatusDisplay}</td>
            <td>${getBadgeHtml(r.status)}</td>
            <td style="text-align:center;">
              <button type="button" class="btn-sm" onclick="openDamayanReviewModal('${escHtml(r.id)}')" style="padding:4px 10px;font-size:0.78rem;border-radius:4px;border:1px solid var(--border);background:#fff;cursor:pointer;font-weight:600;">
                Review
              </button>
            </td>
          </tr>
        `;
      }).join('');

      paginationBar.innerHTML = `<div class="mis-pagination-info">Showing ${startIdx + 1}–${endIdx} of ${totalItems} records</div>`;
      buildPaginationControls(currentPage, totalPages);
    }

    // ── Review Modal ──
    function openDamayanReviewModal(id) {
      const r = RAW_REQUESTS.find(x => String(x.id) === String(id));
      if (!r) return;

      const refDisplay = r.ref_code || ('#' + r.id);
      document.getElementById('damayan-mdl-title').textContent = `${refDisplay} — ${r.service_type || 'Burial Request'}`;

      const rows = [
        ['Record / Case Reference', refDisplay],
        ['Service Classification', r.service_type || 'Burial Service'],
        ['Deceased Full Name', r.name || 'Unknown'],
        ['Informant / Applicant', r.informant_name || '—'],
        ['Contact Details', r.contact || '—'],
        ['Overall Case Status', getBadgeHtml(r.status)],
        ['Submitted Date', formatDisplayDateTime(r.date)]
      ];

      if (r.date_of_death) rows.push(['Date of Death', formatDisplayDate(r.date_of_death)]);
      if (r.place_of_death) rows.push(['Place of Death', r.place_of_death]);
      if (r.death_cert_status) rows.push(['Civil Death Certificate Status', r.death_cert_status]);
      if (r.scheduled_release_date) rows.push(['Scheduled Burial / Release', formatDisplayDate(r.scheduled_release_date)]);
      if (r.assigned_staff_name) rows.push(['Assigned Handling Staff', r.assigned_staff_name]);
      if (r.requested_services) rows.push(['Services Requested', r.requested_services]);
      if (r.details) rows.push(['Remarks / Notes', r.details]);

      document.getElementById('damayan-mdl-body').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr;gap:10px;">
          ${rows.map(([k, v]) => `
            <div style="display:flex;justify-content:space-between;gap:14px;padding:8px 0;border-bottom:1px dashed var(--border-color,#e2e8f0);">
              <span style="color:var(--text-muted);font-weight:600;min-width:170px;">${escHtml(k)}</span>
              <span style="text-align:right;font-weight:600;word-break:break-word;">${k === 'Overall Case Status' ? v : escHtml(v)}</span>
            </div>
          `).join('')}
        </div>`;
      document.getElementById('damayan-review-modal').style.display = 'flex';
    }

    function closeDamayanReviewModal() {
      document.getElementById('damayan-review-modal').style.display = 'none';
    }

    // ── View switch listener ──
    document.querySelectorAll('#damayanViewSwitch .pill-tab').forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#damayanViewSwitch .pill-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentView = this.dataset.view;
        renderTable(true);
      });
    });

    // ── Input change listeners ──
    document.getElementById('damayan-search').addEventListener('input', () => renderTable(true));
    document.getElementById('damayan-status-filter').addEventListener('change', () => renderTable(true));

    // ── Handle URL parameters (e.g. ?id=...) ──
    (function initUrlParams() {
      const params = new URLSearchParams(window.location.search);
      const qId = params.get('id');
      if (qId) {
        openDamayanReviewModal(qId);
      }
    })();

    // ── Initial Render ──
    renderTable(true);
  </script>
</body>

</html>
