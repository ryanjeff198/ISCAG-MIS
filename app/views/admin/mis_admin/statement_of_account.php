<?php $active_page = 'soa'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Statement of Account</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Generate and view Statement of Account for tenants" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>    .controls-panel {
      background: white;
      padding: 24px;
      border-radius: 12px;
      border: 1px solid var(--border);
      margin-bottom: 24px;
      display: flex;
      gap: 32px;
      align-items: flex-start; /* Alignment start to handle labels better */
      flex-wrap: wrap;
    }
    .form-group {
      flex: 1;
      min-width: 200px;
      display: flex !important;
      flex-direction: column !important;
      gap: 8px !important;
      align-items: flex-start !important;
    }
    .form-group label {
      display: block !important;
      font-size: 0.85rem !important;
      font-weight: 700 !important;
      color: var(--primary-dark) !important;
      margin: 0 !important;
      text-align: left !important;
    }
    .form-group select, .form-group input {
      width: 100% !important;
      padding: 10px 14px !important;
      border: 1px solid var(--border) !important;
      border-radius: 8px !important;
      font-size: 0.95rem !important;
      outline: none !important;
      height: 42px !important;
      box-sizing: border-box !important;
    }
    
    /* Search Bar Integration - Force it to behave like a normal flex child */
    .table-search-container {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        position: static !important;
        border: none !important;
        box-shadow: none !important;
        display: block !important;
        float: none !important;
    }
    .table-search-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        display: flex !important; /* Switch to flex for centering */
        align-items: center !important;
        position: relative !important;
        height: 42px !important;
    }
    .table-search-wrapper svg {
        position: absolute !important;
        right: 14px !important; /* Move to right */
        left: auto !important;
        z-index: 5 !important;
        pointer-events: none !important;
        fill: var(--text-muted) !important;
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
    }
    .table-search-input {
        height: 42px !important;
        margin: 0 !important;
        width: 100% !important;
        display: block !important;
        padding-left: 14px !important; 
        padding-right: 40px !important; /* Space for icon on right */
    }
    .soa-container {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 40px;
      max-width: 900px;
      margin: 20px auto;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      position: relative; /* Fixed: Absolute stamp relative to this container */
    }
    .soa-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid var(--primary-dark);
      padding-bottom: 20px;
      margin-bottom: 30px;
    }
    .soa-brand {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .soa-brand img {
      width: 80px;
      height: 80px;
      border-radius: 8px;
    }
    .soa-brand-text h2 {
      margin: 0;
      color: var(--primary-dark);
      font-size: 1.5rem;
      font-family: inherit;
    }
    .soa-brand-text p {
      margin: 5px 0 0;
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    .soa-title {
      text-align: right;
    }
    .soa-title h1 {
      margin: 0;
      color: var(--text-main);
      font-size: 2rem;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    .soa-title p {
      margin: 5px 0 0;
      color: var(--text-muted);
      font-weight: 600;
      font-size: 0.9rem;
    }
    .soa-details {
      display: flex;
      justify-content: space-between;
      margin-bottom: 30px;
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
    }
    .soa-details-left p, .soa-details-right p {
      margin: 5px 0;
      font-size: 0.95rem;
    }
    .soa-details strong {
      color: var(--primary-dark);
    }
    .soa-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
      border: 1px solid var(--border);
    }
    .soa-table th {
      background: var(--primary-dark);
      color: white;
      padding: 12px;
      text-align: left;
      font-size: 0.85rem;
      border: 1px solid var(--border);
    }
    .soa-table td {
      padding: 10px 12px;
      border: 1px solid var(--border);
      font-size: 0.9rem;
    }
    .soa-table tr:hover { background: #f8faf9; }
    .soa-table .row-payment { background: rgba(47,138,96,0.03); }
    
    .type-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .type-badge.rent { background: rgba(23,107,69,0.1); color: #176b45; }
    .type-badge.deposit { background: rgba(59,130,246,0.1); color: #2563eb; }
    .type-badge.advance { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .type-badge.parking { background: rgba(245,158,11,0.1); color: #b45309; }
    .type-badge.water { background: rgba(6,182,212,0.1); color: #0891b2; }
    .type-badge.payment { background: rgba(34,197,94,0.1); color: #16a34a; }
    .type-badge.contribution { background: rgba(156,163,175,0.1); color: #6b7280; }
    .type-badge.invoice { background: rgba(249,115,22,0.1); color: #c2410c; }

    .soa-summary {
      width: 100%;
      max-width: 400px;
      margin-left: auto;
    }
    .soa-summary-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
      font-size: 0.95rem;
    }
    .soa-summary-row.total {
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--primary-dark);
      border-bottom: none;
      border-top: 2px solid var(--primary-dark);
      margin-top: 10px;
      padding-top: 15px;
    }
    .soa-summary-row .label-muted { color: var(--text-muted); font-weight: 600; }
    
    .soa-section-title {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--primary);
      padding: 12px;
      background: rgba(23,107,69,0.04);
      border-left: 3px solid var(--primary);
    }
    
    .soa-stamp {
      position: absolute;
      top: 30%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-15deg);
      font-size: 5rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      pointer-events: none;
      z-index: 10;
      border: 8px solid;
      padding: 10px 40px;
      border-radius: 20px;
      display: none;
    }
    .soa-stamp.paid {
      color: rgba(22, 163, 74, 0.12); /* Green */
      border-color: rgba(22, 163, 74, 0.12);
      display: block;
    }
    .soa-stamp.unpaid {
      color: rgba(220, 38, 38, 0.08); /* Red */
      border-color: rgba(220, 38, 38, 0.08);
      display: block;
    }

    .form-group select:focus, .form-group input:focus {
      border-color: var(--primary);
    }
    
    /* Interactive Cards */
    .insight-card.clickable {
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .insight-card.clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }
    .insight-card.clickable:active {
        transform: translateY(0);
    }
    .filter-active-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--danger);
        color: white;
        font-size: 0.6rem;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        display: none;
    }
    .insight-card.filtering .filter-active-badge {
        display: block;
    }
    .insight-card.filtering {
        border-color: var(--danger);
        background: rgba(220, 38, 38, 0.01);
    }

    .breakdown-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      margin-bottom: 24px;
      padding: 0 12px;
    }
    .breakdown-card {
      background: #f8faf9;
      border-radius: 10px;
      padding: 14px 16px;
      border: 1px solid var(--border);
    }
    .breakdown-card .bk-label {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-muted);
      margin-bottom: 6px;
    }
    .breakdown-card .bk-value {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-main);
    }
    .breakdown-card .bk-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    @media print {
      body * { visibility: hidden; }
      .soa-container, .soa-container * { visibility: visible; }
      .soa-container {
        position: absolute;
        left: 0; top: 0;
        width: 100%; max-width: 100%;
        box-shadow: none; border: none; margin: 0; padding: 20px;
      }
      .controls-panel, .top-bar, .sidebar, .admin-insights, #soa-empty-state { display: none !important; }
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
          <div>
            <div class="top-bar-title">Statement of Account</div>
            <div class="top-bar-subtitle">Generate official financial statements for tenants</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
          <button class="btn-topbar primary" onclick="window.print()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
              <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            Print Statement
          </button>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Active Tenants</div>
            <div class="insight-value"><?= count($tenants) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Line Items</div>
            <div class="insight-value"><?= count($transactions) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Last Generated</div>
            <div class="insight-value success"><?= date('M d') ?></div>
          </div>
          <div class="insight-card clickable" id="outstanding-filter-card" onclick="toggleUnpaidFilter()">
            <div class="filter-active-badge">Filter On</div>
            <?php
              $totalOutstanding = 0;
              foreach($transactions as $t) {
                $totalOutstanding += ($t['charge'] ?? 0) - ($t['payment'] ?? 0);
              }
            ?>
            <div class="insight-label">Net Outstanding</div>
            <div class="insight-value <?= $totalOutstanding > 0 ? 'danger' : 'success' ?>">₱<?= number_format(abs($totalOutstanding), 2) ?></div>
          </div>
        </div>
        
        <!-- Controls Panel -->
        <div class="controls-panel">
          <div class="form-group">
            <label>Select Tenant</label>
            <select id="tenant-select" onchange="updateMonthsAndSOA()">
              <option value="">— Select a Tenant —</option>
              <?php foreach($tenants as $t): ?>
                <option value="<?= $t['tenant_id'] ?>"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?> (<?= $t['room_number'] ? $t['building'].'-'.$t['room_number'] : 'No Unit' ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Filter by Month</label>
            <select id="month-filter" onchange="generateSOA()">
              <option value="all">All Time</option>
            </select>
          </div>
          <div class="form-group" style="flex: 1.5; min-width: 250px;" id="search-slot">
            <!-- Search bar will be moved here -->
          </div>
        </div>

        <!-- Statement Document -->
        <div class="soa-container" id="soa-document" style="display: none;">
          <div class="soa-header">
            <div class="soa-brand">
              <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo">
              <div class="soa-brand-text">
                <h2>ISCAG Management Information System</h2>
                <p>Darul Iman Apartment Complex</p>
                <p>Dasmariñas, Cavite, Philippines</p>
              </div>
            </div>
            <div class="soa-title">
              <h1>Statement of Account</h1>
              <p>Generated on: <span id="soa-date-generated">--</span></p>
            </div>
          </div>

          <div class="soa-details">
            <div class="soa-details-left">
              <p>Bill To:</p>
              <p><strong id="soa-tenant-name" style="font-size:1.2rem;">Tenant Name</strong></p>
              <p>Unit: <strong id="soa-tenant-unit">--</strong></p>
              <p>Contact: <span id="soa-tenant-contact">--</span></p>
              <p>Email: <span id="soa-tenant-email">--</span></p>
            </div>
            <div class="soa-details-right" style="text-align: right;">
              <p>Statement Period:</p>
              <p><strong id="soa-period">All Time</strong></p>
              <p style="margin-top:8px;">Occupants: <strong id="soa-occupants">--</strong></p>
              <p>Room Type: <strong id="soa-roomtype">--</strong></p>
            </div>
          </div>

          <!-- Breakdown Cards -->
          <div class="breakdown-grid" id="soa-breakdown"></div>

          <table class="soa-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Reference</th>
                <th style="text-align:right;">Charge</th>
                <th style="text-align:right;">Payment</th>
                <th style="text-align:right;">Balance</th>
              </tr>
            </thead>
            <tbody id="soa-tbody">
              <!-- Rendered via JS -->
            </tbody>
          </table>

          <div class="soa-summary">
            <div class="soa-summary-row">
              <span class="label-muted">Total Charges:</span>
              <span id="soa-total-charges">₱0.00</span>
            </div>
            <div class="soa-summary-row">
              <span class="label-muted">Total Payments:</span>
              <span id="soa-total-payments" style="color:var(--success);">₱0.00</span>
            </div>
            <div class="soa-summary-row total">
              <span>Outstanding Balance:</span>
              <span id="soa-outstanding">₱0.00</span>
            </div>
          </div>
          
          <div id="admin-soa-stamp-paid" class="soa-stamp paid" style="display:none;">FULLY SETTLED</div>
          <div id="admin-soa-stamp-unpaid" class="soa-stamp unpaid" style="display:none;">UNPAID</div>

          <div style="margin-top: 50px; text-align: center; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border); padding-top: 20px;">
            <p>If you have any questions regarding this statement, please contact the Apartment Admin.</p>
            <p>This is a system-generated document and acts as an official statement of account.</p>
          </div>
        </div>
        
        <!-- Empty State -->
        <div id="soa-empty-state" style="text-align:center; padding:60px 20px; color:var(--text-muted); background:white; border-radius:12px; border:1px solid var(--border);">
          <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;">
            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
          </svg>
          <h4 style="font-family:'Lora',serif; margin:0 0 8px;">No Tenant Selected</h4>
          <p style="font-size:0.9rem; margin:0;">Please select a tenant from the dropdown above to generate their statement of account.</p>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    function setupMovableSearch() {
        const searchTarget = document.getElementById('search-slot');
        const container = document.querySelector('.table-search-container');
        if (!container || !searchTarget) return;

        const label = document.createElement('label');
        label.textContent = 'Search Record';
        searchTarget.innerHTML = '';
        searchTarget.appendChild(label);
        searchTarget.appendChild(container);
        
        container.style.cssText = 'width:100%; padding:0; margin-top:4px;';
        const input = container.querySelector('input');
        if (input) {
            input.id = 'tenant-search';
            input.placeholder = 'Search name, unit, or records...';
            input.addEventListener('input', applyFilters);
        }
        if (window._searchObserver) window._searchObserver.disconnect();
    }

    setupMovableSearch();
    window._searchObserver = new MutationObserver(() => setupMovableSearch());
    window._searchObserver.observe(document.body, { childList: true, subtree: true });

    const transactions = <?= json_encode($transactions) ?>;
    const tenants = <?= json_encode($tenants) ?>;
    const memberMap = <?= json_encode($memberMap ?? []) ?>;

    // Pre-calculate balances for each tenant
    const tenantBalances = {};
    transactions.forEach(t => {
        if (!tenantBalances[t.tenant_id]) tenantBalances[t.tenant_id] = 0;
        tenantBalances[t.tenant_id] += (t.charge - t.payment);
    });

    let isFilteringUnpaid = false;

    function toggleUnpaidFilter() {
        const card = document.getElementById('outstanding-filter-card');
        isFilteringUnpaid = !isFilteringUnpaid;
        
        if (isFilteringUnpaid) card.classList.add('filtering');
        else card.classList.remove('filtering');
        
        applyFilters();
    }

    function applyFilters() {
        const select = document.getElementById('tenant-select');
        const searchInput = document.getElementById('tenant-search') || document.querySelector('.table-search-input');
        if (!searchInput || !select) return;
        
        const query = searchInput.value.toLowerCase();
        const options = select.options;
        
        for (let i = 1; i < options.length; i++) {
            const opt = options[i];
            const tid = opt.value;
            const originalName = opt.getAttribute('data-original-name') || opt.textContent.split(' [')[0];
            if (!opt.getAttribute('data-original-name')) opt.setAttribute('data-original-name', originalName);
            
            const text = originalName.toLowerCase();
            const bal = tenantBalances[tid] || 0;
            
            let visible = true;
            if (query && !text.includes(query)) visible = false;
            if (isFilteringUnpaid && bal <= 0) visible = false;
            
            if (visible) {
                opt.style.display = 'block';
                opt.textContent = originalName + (isFilteringUnpaid ? ` [₱${bal.toLocaleString()}]` : '');
            } else {
                opt.style.display = 'none';
            }
        }
    }

    function getTypeBadge(type) {
      const t = (type || '').toLowerCase();
      if (t.includes('rent') && !t.includes('payment')) return '<span class="type-badge rent">Rent</span>';
      if (t.includes('deposit')) return '<span class="type-badge deposit">Deposit</span>';
      if (t.includes('advance')) return '<span class="type-badge advance">Advance</span>';
      if (t.includes('parking')) return '<span class="type-badge parking">Parking</span>';
      if (t.includes('water')) return '<span class="type-badge water">Water</span>';
      if (t.includes('payment') || t.includes('paid')) return '<span class="type-badge payment">Payment</span>';
      if (t.includes('contribution')) return '<span class="type-badge contribution">Contrib</span>';
      if (t.includes('invoice')) return '<span class="type-badge invoice">Invoice</span>';
      return '<span class="type-badge">' + type + '</span>';
    }

    function updateMonthsAndSOA() {
      const tenantId = document.getElementById('tenant-select').value;
      const monthSelect = document.getElementById('month-filter');
      
      // Clear months except "All Time"
      monthSelect.innerHTML = '<option value="all">All Time</option>';
      
      if (tenantId) {
        const tenantTransactions = transactions.filter(t => t.tenant_id == tenantId);
        const uniqueMonths = [...new Set(tenantTransactions.map(t => {
            const d = new Date(t.date);
            return d.getFullYear() + '-' + (d.getMonth() + 1).toString().padStart(2, '0');
        }))].sort().reverse();

        uniqueMonths.forEach(m => {
            const [y, mm] = m.split('-');
            const label = new Date(y, parseInt(mm)-1).toLocaleDateString('en-US', {month: 'long', year: 'numeric'});
            const opt = document.createElement('option');
            opt.value = m;
            opt.textContent = label;
            monthSelect.appendChild(opt);
        });
      }
      generateSOA();
    }

    function generateSOA() {
      const tenantId = document.getElementById('tenant-select').value;
      const selectedMonth = document.getElementById('month-filter').value; // YYYY-MM or 'all'

      if (!tenantId) {
        document.getElementById('soa-document').style.display = 'none';
        document.getElementById('soa-empty-state').style.display = 'block';
        return;
      }

      const info = tenants.find(t => t.tenant_id == tenantId);
      if (!info) return;

      document.getElementById('soa-empty-state').style.display = 'none';
      document.getElementById('soa-document').style.display = 'block';

      // Header info
      document.getElementById('soa-tenant-name').textContent = info.first_name + ' ' + info.last_name;
      document.getElementById('soa-tenant-unit').textContent = info.room_number ? (info.building + '-' + info.room_number) : 'No Unit Assigned';
      document.getElementById('soa-tenant-contact').textContent = info.contactnum || 'N/A';
      document.getElementById('soa-tenant-email').textContent = info.email || 'N/A';
      document.getElementById('soa-roomtype').textContent = info.roomtype || 'N/A';

      const memberCount = memberMap[tenantId] || 0;
      const totalOccupants = memberCount + 1;
      document.getElementById('soa-occupants').textContent = totalOccupants + ' person(s)';

      const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      document.getElementById('soa-date-generated').textContent = today;
      
      renderSOA(tenantId, selectedMonth, info, totalOccupants);
    }

    function renderSOA(tenantId, selectedMonth, info, totalOccupants) {
      let periodText = 'All Time';
      if(selectedMonth !== 'all') {
         const [y, m] = selectedMonth.split('-');
         periodText = new Date(y, parseInt(m)-1).toLocaleDateString('en-US', {month: 'long', year: 'numeric'});
      }
      document.getElementById('soa-period').textContent = periodText;

      const tenantTransactions = transactions.filter(t => t.tenant_id == tenantId).sort((a,b) => new Date(a.date) - new Date(b.date));
      
      let balanceForwarded = 0;
      let filtered = [];

      if (selectedMonth === 'all') {
        filtered = tenantTransactions;
      } else {
        const [sy, sm] = selectedMonth.split('-');
        const monthStart = new Date(sy, parseInt(sm)-1, 1);
        const monthEnd = new Date(sy, parseInt(sm), 0);

        tenantTransactions.forEach(t => {
            const tDate = new Date(t.date);
            if (tDate < monthStart) {
                balanceForwarded += (t.charge - t.payment);
            } else if (tDate >= monthStart && tDate <= monthEnd) {
                filtered.push(t);
            }
        });
      }

      // Calculate category breakdowns
      let rentTotal = 0, initialTotal = 0, parkingTotal = 0, waterTotal = 0, contributionTotal = 0;
      filtered.forEach(t => {
        const type = (t.type || '').toLowerCase();
        if (t.payment === 0) {
          if (type.includes('rent')) rentTotal += t.charge;
          else if (type.includes('deposit') || type.includes('advance')) initialTotal += t.charge;
          else if (type.includes('parking')) parkingTotal += t.charge;
          else if (type.includes('water')) waterTotal += t.charge;
          else if (type.includes('contribution')) contributionTotal += t.charge;
        }
      });

      // Render breakdown cards
      const breakdownEl = document.getElementById('soa-breakdown');
      breakdownEl.innerHTML = `
        <div class="breakdown-card">
          <div class="bk-label">Monthly Rent</div>
          <div class="bk-value">₱${rentTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">${info.roomtype || 'Apartment'}</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Initial Payments</div>
          <div class="bk-value">₱${initialTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">Deposit & Advance</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Parking Fee</div>
          <div class="bk-value">₱${parkingTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">${parkingTotal > 0 ? 'Fixed Charge' : 'None'}</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Water Bill</div>
          <div class="bk-value">₱${waterTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">${totalOccupants} pax × ₱100</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Contribution</div>
          <div class="bk-value">₱${contributionTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">Security/Garbage</div>
        </div>
      `;

      // Render table
      const tbody = document.getElementById('soa-tbody');
      tbody.innerHTML = '';

      let runningBalance = balanceForwarded;
      let totalCharges = 0;
      let totalPayments = 0;

      // Balance Forwarded Row
      if (selectedMonth !== 'all' && balanceForwarded !== 0) {
          const bfRow = document.createElement('tr');
          bfRow.innerHTML = `
            <td colspan="6"><strong>Balance Forwarded from Previous Months (${balanceForwarded < 0 ? 'Overpayment Credit' : 'Unpaid Balance'})</strong></td>
            <td style="text-align:right; font-weight:700; color:${balanceForwarded > 0 ? 'var(--danger)' : 'var(--success)'}">${balanceForwarded < 0 ? '-' : ''}₱${Math.abs(balanceForwarded).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
          `;
          tbody.appendChild(bfRow);
      }

      if (filtered.length === 0 && balanceForwarded === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:20px;">No transactions found for the selected period.</td></tr>`;
      } else {
        let lastCategory = '';
        filtered.forEach(t => {
          // Group by category
          const cat = getCategoryGroup(t.type);
          if (cat !== lastCategory) {
            const sectionRow = document.createElement('tr');
            sectionRow.innerHTML = `<td colspan="7" class="soa-section-title">${cat}</td>`;
            tbody.appendChild(sectionRow);
            lastCategory = cat;
          }

          runningBalance += t.charge;
          runningBalance -= t.payment;
          totalCharges += t.charge;
          totalPayments += t.payment;

          const isPayment = t.payment > 0;
          const tr = document.createElement('tr');
          tr.className = isPayment ? 'row-payment' : 'row-charge';
          tr.innerHTML = `
            <td>${t.date}</td>
            <td>${getTypeBadge(t.type)}</td>
            <td>${t.description}</td>
            <td style="font-family:monospace; font-size:0.8rem;">${t.ref}</td>
            <td style="text-align:right;">${t.charge > 0 ? '₱' + t.charge.toLocaleString(undefined, {minimumFractionDigits:2}) : '-'}</td>
            <td style="text-align:right; color:var(--success); font-weight:600;">${t.payment > 0 ? '₱' + t.payment.toLocaleString(undefined, {minimumFractionDigits:2}) : '-'}</td>
            <td style="text-align:right; font-weight:600; color:${runningBalance > 0 ? 'var(--danger)' : 'var(--success)'};">₱${runningBalance.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
          `;
          tbody.appendChild(tr);
        });
      }

      document.getElementById('soa-total-charges').textContent = '₱' + totalCharges.toLocaleString(undefined, {minimumFractionDigits:2});
      document.getElementById('soa-total-payments').textContent = '₱' + totalPayments.toLocaleString(undefined, {minimumFractionDigits:2});
      const outstandingEl = document.getElementById('soa-outstanding');
      outstandingEl.textContent = '₱' + runningBalance.toLocaleString(undefined, {minimumFractionDigits:2});
      outstandingEl.style.color = runningBalance > 0 ? 'var(--danger)' : 'var(--success)';

      // Update the top Insight Ribbon for this specific tenant
      const ribbonNet = document.querySelector('.insight-card:nth-child(4) .insight-value');
      if (ribbonNet) {
        ribbonNet.textContent = '₱' + Math.abs(runningBalance).toLocaleString(undefined, {minimumFractionDigits:2});
        ribbonNet.className = 'insight-value ' + (runningBalance > 0 ? 'danger' : 'success');
      }

      // Stamp logic
      const stampPaid = document.getElementById('admin-soa-stamp-paid');
      const stampUnpaid = document.getElementById('admin-soa-stamp-unpaid');
      
      if (runningBalance <= 0 && (filtered.length > 0 || balanceForwarded < 0)) {
          stampPaid.style.display = 'block';
          stampUnpaid.style.display = 'none';
      } else if (runningBalance > 0) {
          stampPaid.style.display = 'none';
          stampUnpaid.style.display = 'block';
      } else {
          stampPaid.style.display = 'none';
          stampUnpaid.style.display = 'none';
      }
    }

    // ══ AUTO-SELECT FROM URL ══
    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const tid = urlParams.get('tenant_id');
      const shouldPrint = urlParams.get('print');

      if (tid) {
        const select = document.getElementById('tenant-select');
        select.value = tid;
        updateMonthsAndSOA();

        if (shouldPrint === '1') {
            setTimeout(() => window.print(), 800); 
        }
      }
    });

    function getCategoryGroup(type) {
      const t = (type || '').toLowerCase();
      if (t.includes('rent') && !t.includes('payment')) return 'Apartment Rent';
      if (t.includes('deposit') || t.includes('advance')) return 'Initial Payments (Deposit & Advance)';
      if (t.includes('parking')) return 'Parking';
      if (t.includes('water')) return 'Water Consumption';
      if (t.includes('contribution')) return 'Contribution';
      if (t.includes('invoice')) return 'Billing Invoices';
      if (t.includes('payment')) return 'Payment Records';
      return 'Other';
    }
  </script>
</body>
</html>
