<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Billing & Payments</title>
  <meta name="description" content="Manage tenant billing, payments, and financial records" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
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
            <div class="insight-label">Total Revenue</div>
            <div class="insight-value success" id="stat-revenue-val">₱0</div>
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
          <div class="stat-card">
            <div class="stat-icon blue">
              <svg viewBox="0 0 24 24">
                <path
                  d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-revenue" style="font-size:1.1rem;">₱0</div>
              <div class="stat-label">Total Revenue</div>
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
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Invoice ID</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="billing-tbody"></tbody>
              </table>
            </div>
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

    // Generate mock billing data if it doesn't exist
    function initBillingData() {
      if (!localStorage.getItem('mis_billing_records')) {
        const mockBilling = [
          { id: 'INV-2026-001', tenantId: 'USR-001', tenantName: 'Muhammad Usman', unit: 'APT-A1', amount: 3500, dueDate: '2026-04-10', status: 'pending', month: 4, year: 2026 },
          { id: 'INV-2026-002', tenantId: 'USR-002', tenantName: 'Aisha Fatima', unit: 'APT-B1', amount: 7500, dueDate: '2026-04-05', status: 'overdue', month: 4, year: 2026 },
          { id: 'INV-2026-003', tenantId: 'USR-003', tenantName: 'Omar Khan', unit: 'APT-A2', amount: 5000, dueDate: '2026-03-05', status: 'paid', month: 3, year: 2026 },
          { id: 'INV-2026-004', tenantId: 'USR-001', tenantName: 'Muhammad Usman', unit: 'APT-A1', amount: 3500, dueDate: '2026-03-10', status: 'paid', month: 3, year: 2026 }
        ];
        localStorage.setItem('mis_billing_records', JSON.stringify(mockBilling));
      }
    }
    initBillingData();

    function getBillingRecords() {
      return JSON.parse(localStorage.getItem('mis_billing_records') || '[]');
    }

    // Tab switching — just updates the filter and re-renders
    function switchTab(tabId) {
      currentTab = tabId;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      event.currentTarget.classList.add('active');
      renderAll();
    }

    function statusBadge(status) {
      if (status === 'paid') return '<span class="badge-status badge-approved">Paid</span>';
      if (status === 'overdue') return '<span class="badge-status badge-rejected">Overdue</span>';
      if (status === 'pending') return '<span class="badge-status badge-pending">Pending</span>';
      return '<span class="badge-status">' + status + '</span>';
    }

    const tabTitles = { all: 'All Invoices', pending: 'Pending Payments', overdue: 'Overdue Invoices', paid: 'Paid Invoices' };

    // ══ RENDER ══
    function renderAll() {
      const records = getBillingRecords();

      // Filter handling
      const term = document.getElementById('search-input').value.toLowerCase();
      const m = document.getElementById('filter-month').value;
      const y = document.getElementById('filter-year').value;

      let filtered = records.filter(r => {
        if (term && !r.tenantName.toLowerCase().includes(term) && !r.id.toLowerCase().includes(term)) return false;
        if (m && r.month != m) return false;
        if (y && r.year != y) return false;
        return true;
      });

      // Apply tab filter
      if (currentTab !== 'all') {
        filtered = filtered.filter(r => r.status === currentTab);
      }

      // Stats (always from unfiltered records)
      document.getElementById('stat-pending').textContent = records.filter(r => r.status === 'pending').length;
      document.getElementById('stat-overdue').textContent = records.filter(r => r.status === 'overdue').length;

      const paidThisMonth = records.filter(r => r.status === 'paid' && r.month == (new Date().getMonth() + 1));
      document.getElementById('stat-paid').textContent = paidThisMonth.length;

      const totalRev = paidThisMonth.reduce((sum, r) => sum + r.amount, 0);
      document.getElementById('stat-revenue').textContent = '₱' + totalRev.toLocaleString();

      // Insight ribbon stats
      const pendEl = document.getElementById('stat-pending-val');
      const overdueEl = document.getElementById('stat-overdue-val');
      const revEl = document.getElementById('stat-revenue-val');
      if (pendEl) pendEl.textContent = records.filter(r => r.status === 'pending').length;
      if (overdueEl) overdueEl.textContent = records.filter(r => r.status === 'overdue').length;
      if (revEl) revEl.textContent = '₱' + records.filter(r => r.status === 'paid').reduce((s, r) => s + r.amount, 0).toLocaleString();

      // Tab counts
      document.getElementById('tab-pending-count').textContent = records.filter(r => r.status === 'pending').length;
      document.getElementById('tab-overdue-count').textContent = records.filter(r => r.status === 'overdue').length;

      // Table header
      document.getElementById('table-title').textContent = tabTitles[currentTab] || 'All Invoices';
      document.getElementById('table-count').textContent = filtered.length + ' record' + (filtered.length !== 1 ? 's' : '');

      // Render single tbody
      const tbody = document.getElementById('billing-tbody');
      if (filtered.length === 0) {
        const emptyMsgs = { all: 'No Invoices', pending: 'No Pending Payments', overdue: 'No Overdue Invoices', paid: 'No Paid Invoices' };
        tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg><h4>' + (emptyMsgs[currentTab] || 'No Records') + '</h4><p>No records match your filters.</p></div></td></tr>';
      } else {
        tbody.innerHTML = filtered.map(r => `
              <tr>
                <td class="td-id">${r.id}</td>
                <td style="font-weight:600;">${r.tenantName}</td>
                <td>${r.unit}</td>
                <td style="font-weight:700;">₱${r.amount.toLocaleString()}</td>
                <td>${r.dueDate}</td>
                <td>${statusBadge(r.status)}</td>
                <td>
                  <div class="action-menu">
                    <button class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
                      <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </button>
                    <div class="action-menu-dropdown">
                      <button class="action-menu-item" onclick="showToast('Viewing ${r.id}','var(--info)')">
                        <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        Invoice Details
                      </button>
                      ${r.status === 'overdue' ? `
                      <button class="action-menu-item danger" onclick="showToast('Reminder sent to ${r.tenantName}','var(--danger)')">
                        <svg viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12s4.48 10 10 10 10-4.48 10-10zm-11 5H9v-2h2v2zm0-4H9V7h2v6z"/></svg>
                        Send Overdue Reminder
                      </button>` : ''}
                      ${r.status !== 'paid' ? `
                      <button class="action-menu-item success" onclick="showToast('Receipt generated for ${r.id}','var(--success)')">
                        <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                        Mark as Paid
                      </button>` : ''}
                      <button class="action-menu-item" onclick="showToast('Downloading PDF...','var(--info)')">
                        <svg viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        Download PDF
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            `).join('');
      }
    }

    document.getElementById('search-input').addEventListener('input', renderAll);
    document.getElementById('filter-month').addEventListener('change', renderAll);
    document.getElementById('filter-year').addEventListener('change', renderAll);

    renderAll();
  </script>
</body>

</html>

