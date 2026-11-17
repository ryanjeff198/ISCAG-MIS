<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Billing & Payments</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Manage tenant billing, payments, and financial records" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .action-menu { position: relative; display: flex; justify-content: center; align-items: center; }
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
          
          <div>
            <div class="top-bar-title">Billing & Payments</div>
            <div class="top-bar-subtitle">Manage tenant invoices, track payments, and follow up on overdue balances</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
          <button class="btn-topbar primary" id="btn-generate-invoice">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
              <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
            </svg>
            Generate Invoice
          </button>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Pending Payments</div>
            <div class="insight-value warning" id="stat-pending-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Overdue Invoices</div>
            <div class="insight-value danger" id="stat-overdue-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Collection Rate</div>
            <div class="insight-value info">97.5%</div>
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
              <div class="stat-label">Pending Payments</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-overdue">0</div>
              <div class="stat-label">Overdue Invoices</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-paid">0</div>
              <div class="stat-label">Paid This Month</div>
            </div>
          </div>
        </div>

        <!-- TAB NAV -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('all')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path
                d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z" />
            </svg>
            All Invoices
          </button>
          <button class="tab-btn" onclick="switchTab('pending')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
            </svg>
            Pending
            <span class="tab-count pending" id="tab-pending-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('overdue')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
            Overdue
            <span class="tab-count overdue" id="tab-overdue-count">0</span>
          </button>
          <button class="tab-btn" onclick="switchTab('paid')">
            <svg viewBox="0 0 24 24"
              style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;">
              <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
            </svg>
            Paid
          </button>
        </div>

        <div class="filter-bar" style="margin-bottom:20px;">
          <input type="text" class="search-input" id="search-input" placeholder="Search by Invoice ID or Tenant..." />
          <select class="filter-select" id="filter-month">
            <option value="">All Months</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
          </select>
          <select class="filter-select" id="filter-year">
            <option value="2026">2026</option>
            <option value="2025">2025</option>
          </select>
        </div>

        <!-- SINGLE TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
              <span id="table-title">All Invoices</span>
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="table-count">0 records</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="invoices-table" data-paginate="false">
                <thead>
                  <tr>
                    <th>Invoice ID</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="billing-tbody"></tbody>
              </table>
            </div>
            <div id="billing-pagination" class="mis-pagination-bar"></div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══ INIT ══
    standardizePage('admin');
    setCurrentRole(ROLES.MIS_ADMIN);

    let currentTab = 'all';

    // Bridge PHP data to JavaScript
    const allInvoices = <?= json_encode(array_map(function($i) {
        return [
            'id' => 'INV-' . str_pad($i['billing_id'], 4, '0', STR_PAD_LEFT),
            'tenantId' => $i['tenant_id'],
            'tenantName' => $i['first_name'] . ' ' . $i['last_name'],
            'unit' => ($i['room_number'] ? $i['building'] . '-' . $i['room_number'] : 'Unassigned'),
            'amount' => (float)$i['amount'],
            'dueDate' => $i['due_date'],
            'status' => strtolower($i['status']),
            'month' => (int)date('n', strtotime($i['due_date'])),
            'year' => (int)date('Y', strtotime($i['due_date']))
        ];
    }, $invoices)) ?>;

    let currentPage = 1;
    const pageSize = 10;

    function switchTab(tabId) {
      currentTab = tabId;
      currentPage = 1;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.currentTarget.classList.add('active');
      renderAll(true);
    }

    function statusBadge(status) {
      const s = status.toLowerCase();
      if (s === 'paid') return '<span class="badge-status badge-approved">Paid</span>';
      if (s === 'overdue') return '<span class="badge-status badge-rejected">Overdue</span>';
      if (s === 'pending') return '<span class="badge-status badge-pending">Pending</span>';
      return '<span class="badge-status">' + status + '</span>';
    }

    const tabTitles = { all: 'All Invoices', pending: 'Pending Payments', overdue: 'Overdue Invoices', paid: 'Paid Invoices' };

    function buildBillingPagination(curr, total) {
      const bar = document.getElementById('billing-pagination');
      if (!bar) return;
      if (total <= 1) {
        bar.innerHTML = '<div class="mis-pagination-info">Showing all invoices</div>';
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
        if (p === '...') btns += `<span class="mis-page-ellipsis">&hellip;</span>`;
        else btns += `<button type="button" class="mis-page-btn ${p === curr ? 'active' : ''}" data-p="${p}">${p}</button>`;
      });
      btns += `<button type="button" class="mis-page-btn" data-act="next" ${curr === total ? 'disabled' : ''}>Next &raquo;</button>`;

      const ctrlDiv = document.createElement('div');
      ctrlDiv.className = 'mis-pagination-controls';
      ctrlDiv.innerHTML = btns;
      ctrlDiv.querySelectorAll('.mis-page-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          if (btn.disabled) return;
          const p = btn.getAttribute('data-p');
          const act = btn.getAttribute('data-act');
          if (p) {
            currentPage = parseInt(p, 10);
            renderAll(false);
          } else if (act === 'prev' && currentPage > 1) {
            currentPage--;
            renderAll(false);
          } else if (act === 'next' && currentPage < total) {
            currentPage++;
            renderAll(false);
          }
        });
      });
      bar.appendChild(ctrlDiv);
    }

    // ══ RENDER ══
    function renderAll(resetPage = false) {
      if (resetPage) currentPage = 1;

      // Filter handling
      const term = document.getElementById('search-input').value.toLowerCase().trim();
      const m = document.getElementById('filter-month').value;
      const y = document.getElementById('filter-year').value;

      let filtered = allInvoices.filter(r => {
        if (term && !r.tenantName.toLowerCase().includes(term) && !r.id.toLowerCase().includes(term)) return false;
        if (m && r.month != m) return false;
        if (y && r.year != y) return false;
        return true;
      });

      // Apply tab filter
      if (currentTab !== 'all') {
        filtered = filtered.filter(r => r.status === currentTab);
      }

      // Stats (from allInvoices)
      const counts = { pending: 0, overdue: 0, paid: 0 };
      allInvoices.forEach(r => counts[r.status]++);

      document.getElementById('stat-pending').textContent = counts.pending;
      document.getElementById('stat-overdue').textContent = counts.overdue;

      const thisMonth = new Date().getMonth() + 1;
      const paidThisMonth = allInvoices.filter(r => r.status === 'paid' && r.month === thisMonth);
      document.getElementById('stat-paid').textContent = paidThisMonth.length;

      const statRevEl = document.getElementById('stat-revenue');
      if (statRevEl) {
        const totalRevThisMonth = paidThisMonth.reduce((sum, r) => sum + r.amount, 0);
        statRevEl.textContent = '₱' + totalRevThisMonth.toLocaleString();
      }

      // Insight ribbon stats
      const pendEl = document.getElementById('stat-pending-val');
      const overdueEl = document.getElementById('stat-overdue-val');
      const revEl = document.getElementById('stat-revenue-val');
      if (pendEl) pendEl.textContent = counts.pending;
      if (overdueEl) overdueEl.textContent = counts.overdue;
      if (revEl) {
          const totalRevOverall = allInvoices.filter(r => r.status === 'paid').reduce((s, r) => s + r.amount, 0);
          revEl.textContent = '₱' + totalRevOverall.toLocaleString();
      }

      // Tab counts
      document.getElementById('tab-pending-count').textContent = counts.pending;
      document.getElementById('tab-overdue-count').textContent = counts.overdue;

      // Table header
      document.getElementById('table-title').textContent = tabTitles[currentTab] || 'All Invoices';
      document.getElementById('table-count').textContent = filtered.length + ' record' + (filtered.length !== 1 ? 's' : '');

      // Pagination slice
      const totalItems = filtered.length;
      const totalPages = Math.ceil(totalItems / pageSize) || 1;
      if (currentPage > totalPages) currentPage = totalPages;

      const startIdx = (currentPage - 1) * pageSize;
      const endIdx = Math.min(startIdx + pageSize, totalItems);
      const pageItems = filtered.slice(startIdx, endIdx);

      // Render single tbody
      const tbody = document.getElementById('billing-tbody');
      const bar = document.getElementById('billing-pagination');

      if (totalItems === 0) {
        const emptyMsgs = { all: 'No Invoices', pending: 'No Pending Payments', overdue: 'No Overdue Invoices', paid: 'No Paid Invoices' };
        tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M6 2h7.5C16.54 2 19 4.46 19 7.5S16.54 13 13.5 13H8.5v8H6V2zm2.5 9H13.5c1.93 0 3.5-1.57 3.5-3.5S15.43 4 13.5 4H8.5v7z"/><path d="M3 6h15v2H3z"/><path d="M3 9.5h14v2H3z"/></svg><h4>' + (emptyMsgs[currentTab] || 'No Records') + '</h4><p>No records match your filters.</p></div></td></tr>';
        if (bar) bar.innerHTML = '<div class="mis-pagination-info">Showing 0 of 0 invoices</div>';
      } else {
        tbody.innerHTML = pageItems.map(r => `
              <tr>
                <td class="td-id">${r.id}</td>
                <td style="font-weight:600;">${r.tenantName}</td>
                <td>${r.unit}</td>
                <td style="font-weight:700;">₱${r.amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td>${formatDate(r.dueDate)}</td>
                <td>${statusBadge(r.status)}</td>
              </tr>
            `).join('');

        if (bar) {
          bar.innerHTML = `<div class="mis-pagination-info">Showing ${startIdx + 1}–${endIdx} of ${totalItems} invoices</div>`;
          buildBillingPagination(currentPage, totalPages);
        }
      }
    }

    document.getElementById('search-input').addEventListener('input', () => renderAll(true));
    document.getElementById('filter-month').addEventListener('change', () => renderAll(true));
    document.getElementById('filter-year').addEventListener('change', () => renderAll(true));

    renderAll(true);
  </script>
</body>

</html>

