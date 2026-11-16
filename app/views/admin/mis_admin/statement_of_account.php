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
  <style>
    :root {
      --primary: #176b45;
      --primary-dark: #0e452c;
      --primary-light: #e8f5ed;
      --secondary: #f4b400;
      --danger: #d93025;
      --success: #2f8a60;
      --text-main: #202124;
      --text-muted: #5f6368;
      --border: #dadce0;
      --bg-gray: #f8f9fa;
    }

    /* Fix sidebar avatar color - page's --primary-light is too light */
    .sidebar .user-avatar {
      background: #2f8a60 !important;
      color: white !important;
    }

    .controls-panel {
      background: white;
      padding: 24px;
      border-radius: 12px;
      border: 1px solid var(--border);
      margin-bottom: 24px;
      display: flex;
      gap: 32px;
      align-items: flex-end;
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
    
    /* Master List (Eyeview) Styles */
    .soa-overview-container {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .soa-summary-table {
      width: 100%;
      border-collapse: collapse;
    }
    .soa-summary-table th {
      background: #f8faf9;
      padding: 16px;
      text-align: left;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      border-bottom: 2px solid var(--border);
    }
    .soa-summary-table td {
      padding: 16px;
      border-bottom: 1px solid var(--border);
      font-size: 0.9rem;
      color: var(--text-main);
    }
    .soa-summary-table tr:hover { background: #fbfdfc; }
    
    .tenant-info-cell { display: flex; align-items: center; gap: 12px; }
    .tenant-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: #2f8a60; color: white;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 0.8rem;
    }
    .tenant-meta { display: flex; flex-direction: column; }
    .tenant-name { font-weight: 600; color: var(--primary-dark); }
    .tenant-room { font-size: 0.75rem; color: var(--text-muted); }
    
    .balance-positive { color: #d32f2f; font-weight: 700; }
    .balance-zero { color: #2e7d32; font-weight: 600; }
    
    .btn-view-soa {
      padding: 6px 14px;
      border-radius: 8px;
      border: 1.5px solid var(--primary);
      background: transparent;
      color: var(--primary);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-view-soa:hover { background: var(--primary); color: white; }

    /* Transitions */
    #soa-detail-view { display: none; }
    #soa-list-view { display: block; }
    
    .view-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .btn-back {
        display: flex; align-items: center; gap: 8px;
        background: none; border: none; color: var(--text-muted);
        font-weight: 600; cursor: pointer;
    }
    .btn-back:hover { color: var(--primary); }

    /* Search Bar Integration */
    .table-search-container {
        margin: 0 !important;
        width: 100% !important;
        padding: 0 !important;
        position: static !important;
        border: none !important;
    }
    .table-search-wrapper {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        position: relative !important;
        height: 42px !important;
    }
    .table-search-wrapper svg {
        position: absolute !important;
        right: 14px !important;
        left: auto !important;
        z-index: 5 !important;
        fill: var(--text-muted) !important;
        width: 16px !important; height: 16px !important;
    }
    .table-search-input {
        height: 42px !important;
        width: 100% !important;
        padding-left: 14px !important;
        padding-right: 40px !important;
        border: 1px solid var(--border) !important;
        border-radius: 8px !important;
        font-size: 0.95rem !important;
        background: white !important;
    }

    /* Print Button - Remove Shiny View */
    .btn-topbar.primary {
        background: var(--primary) !important;
        box-shadow: 0 2px 6px rgba(23, 107, 69, 0.2) !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }
    .btn-topbar.primary:hover {
        background: var(--primary-dark) !important;
        box-shadow: 0 4px 12px rgba(23, 107, 69, 0.3) !important;
        transform: translateY(-1px);
    }

    /* Document Styling */
    .soa-container { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 40px; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
    .soa-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--primary-dark); padding-bottom: 20px; margin-bottom: 30px; }
    .soa-brand { display: flex; align-items: center; gap: 15px; }
    .soa-brand img { width: 80px; height: 80px; border-radius: 8px; }
    .soa-brand-text h2 { margin: 0; color: var(--primary-dark); font-size: 1.5rem; font-family: inherit; }
    .soa-brand-text p { margin: 5px 0 0; font-size: 0.85rem; color: var(--text-muted); }
    .soa-title { text-align: right; }
    .soa-title h1 { margin: 0; color: var(--text-main); font-size: 2rem; text-transform: uppercase; letter-spacing: 2px; }
    .soa-title p { margin: 5px 0 0; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; }
    .soa-details { display: flex; justify-content: space-between; margin-bottom: 30px; background: #f8f9fa; padding: 20px; border-radius: 8px; }
    .soa-details-left p, .soa-details-right p { margin: 5px 0; font-size: 0.95rem; }
    .soa-details strong { color: var(--primary-dark); }
    .soa-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1px solid var(--border); }
    .soa-table th { background: var(--primary-dark); color: white; padding: 12px; text-align: left; font-size: 0.85rem; border: 1px solid var(--border); }
    .soa-table td { padding: 10px 12px; border: 1px solid var(--border); font-size: 0.9rem; }
    .soa-table tr:hover { background: #f8faf9; }
    .type-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; }
    .type-badge.rent { background: rgba(23,107,69,0.1); color: #176b45; }
    .type-badge.deposit { background: rgba(59,130,246,0.1); color: #2563eb; }
    .type-badge.advance { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .type-badge.parking { background: rgba(245,158,11,0.1); color: #b45309; }
    .type-badge.water { background: rgba(6,182,212,0.1); color: #0891b2; }
    .type-badge.payment { background: rgba(34,197,94,0.1); color: #16a34a; }
    .type-badge.contribution { background: rgba(156,163,175,0.15); color: #4b5563; }
    .soa-summary { width: 100%; max-width: 400px; margin-left: auto; }
    .soa-summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
    .soa-summary-row.total { font-size: 1.2rem; font-weight: 700; color: var(--primary-dark); border-bottom: none; border-top: 2px solid var(--primary-dark); margin-top: 10px; padding-top: 15px; }
    .soa-summary-row .label-muted { color: var(--text-muted); font-weight: 600; }
    .soa-section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--primary); padding: 12px; background: rgba(23,107,69,0.04); border-left: 3px solid var(--primary); }

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
      font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 0.04em; color: var(--text-muted); margin-bottom: 6px;
    }
    .breakdown-card .bk-value { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
    .breakdown-card .bk-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

    .insight-card.clickable {
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .insight-card.clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }
    .filter-active-badge {
        position: absolute; top: 8px; right: 8px;
        background: var(--danger); color: white;
        font-size: 0.6rem; font-weight: 800; padding: 2px 6px;
        border-radius: 4px; text-transform: uppercase; display: none;
    }

    .row-empty td { text-align:center; padding:28px; color: var(--text-muted); }

    @media print {
      body * { visibility: hidden; }
      body.print-detail .soa-container, body.print-detail .soa-container * { visibility: visible; }
      body.print-detail .soa-container {
        position: absolute; left: 0; top: 0; width: 100%;
        box-shadow: none; border: none; padding: 0;
      }
      body.print-list #soa-list-view, body.print-list #soa-list-view * { visibility: visible; }
      body.print-list #soa-list-view { position: absolute; left: 0; top: 0; width: 100%; }
      body.print-list .btn-view-soa { display: none; }
      .controls-panel, .top-bar, .sidebar, .admin-insights { display: none !important; }
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
          <button class="btn-topbar primary" onclick="printCurrentView()">
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
          <?php $summary = $summary ?? ['tenant_count'=>count($tenants),'total_charges'=>0,'total_payments'=>0,'outstanding'=>0,'credits'=>0,'unpaid_tenants'=>0]; ?>
          <div class="insight-card">
            <div class="insight-label">Tenants with Lease</div>
            <div class="insight-value" id="ribbon-tenant-count"><?= (int)$summary['tenant_count'] ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Billed</div>
            <div class="insight-value" id="ribbon-charges">₱<?= number_format($summary['total_charges'], 2) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Collected</div>
            <div class="insight-value success" id="ribbon-payments">₱<?= number_format($summary['total_payments'], 2) ?></div>
          </div>
          <div class="insight-card clickable" id="outstanding-filter-card" onclick="setMasterFilter(masterFilter === 'unpaid' ? 'all' : 'unpaid')" title="Click to show only tenants with unpaid balances">
            <div class="filter-active-badge" id="master-filter-badge">Filter On</div>
            <div class="insight-label">Outstanding Receivables</div>
            <div class="insight-value <?= $summary['outstanding'] > 0 ? 'danger' : 'success' ?>" id="ribbon-net-standing">₱<?= number_format($summary['outstanding'], 2) ?></div>
            <div style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;" id="ribbon-unpaid-sub"><?= (int)$summary['unpaid_tenants'] ?> tenant(s) with balance<?= $summary['credits'] > 0 ? ' · ₱' . number_format($summary['credits'], 2) . ' in credits' : '' ?></div>
          </div>
        </div>
        
        <div class="controls-panel">
          <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 8px; align-items: flex-start;" id="filter-buttons-group">
            
            <div style="display: flex; gap: 12px; align-items: center; height: 42px;">
              <button class="btn-topbar" id="btn-show-all" onclick="setMasterFilter('all')" style="border-radius: 8px; border-color: var(--border); height: 42px;">All Tenants</button>
              <button class="btn-topbar" id="btn-show-unpaid" onclick="setMasterFilter('unpaid')" style="border-radius: 8px; border-color: var(--border); height: 42px;">Show Only Unpaid</button>
              <button class="btn-topbar" id="btn-show-paid" onclick="setMasterFilter('paid')" style="border-radius: 8px; border-color: var(--border); height: 42px;">Show Only Settled</button>
              <button class="btn-topbar" id="btn-export-csv" onclick="exportOverviewCsv()" style="border-radius: 8px; border-color: var(--border); height: 42px;">Export CSV</button>
            </div>
          </div>
          <div id="month-filter-container" style="flex: 1; display: flex; justify-content: center; padding-top: 1px;">
            <select id="month-filter" onchange="onMonthFilterChange()" style="width:220px; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:0.95rem; height:42px;">
            </select>
          </div>

          <div style="flex: none; width: 300px; margin-left: auto; display: flex; flex-direction: column; gap: 8px;" id="search-slot">
            <!-- Search bar (visible in list view) -->
            <div class="table-search-container" id="search-container">
              <div class="table-search-wrapper">
                <input type="text" id="tenant-search" class="table-search-input" placeholder="Search name or room..." oninput="applyFilters()">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- LIST VIEW (EYEVIEW) -->
        <div id="soa-list-view">
          <div class="soa-overview-container">
            <table class="soa-summary-table" id="master-tenant-table">
              <thead>
                <tr>
                  <th>Tenant & Unit</th>
                  <th>Lease</th>
                  <th>Total Charges</th>
                  <th>Total Payments</th>
                  <th>Net Outstanding</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="master-tenant-body">
                <!-- Populated by JS -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- DETAIL VIEW (SOA DOCUMENT) -->
        <div id="soa-detail-view">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
            <button class="btn-back" onclick="showListView()">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
              &nbsp;Back to Overview
            </button>
          </div>
          
          <div class="soa-container">
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
                <p>Generated on: <?= date('F d, Y') ?></p>
              </div>
            </div>

            <div class="soa-details">
              <div class="soa-details-left">
                <p>Bill To:</p>
                <p><strong id="soa-tenant-name" style="font-size:1.2rem;">Tenant Name</strong></p>
                <p>Unit: <strong id="soa-room-id">--</strong></p>
                <p>Email: <span id="soa-tenant-email">--</span></p>
              </div>
              <div class="soa-details-right" style="text-align: right;">
                <p>Statement Period:</p>
                <p><strong id="soa-period">All Time</strong></p>
                <p style="margin-top:8px;">Occupants: <strong id="soa-occupants">--</strong></p>
                <p>Room Type: <strong id="soa-roomtype">--</strong></p>
                <p>Lease: <strong id="soa-lease">--</strong></p>
                <p>Monthly Rent: <strong id="soa-rent">--</strong></p>
              </div>
            </div>

            <!-- Exact Breakdown Cards -->
            <div class="breakdown-grid" id="soa-breakdown">
                <!-- Populated by JS -->
            </div>

            <table class="soa-table" data-searchable="false">
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
              <tbody id="soa-table-body">
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
                <span id="soa-net-standing">₱0.00</span>
              </div>
            </div>
            
            <div style="margin-top: 50px; text-align: center; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border); padding-top: 20px;">
              <p>If you have any questions regarding this statement, please contact the Apartment Admin.</p>
              <p>This is a system-generated document and acts as an official statement of account.</p>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    // === DATA SOURCE (built server-side by SoaHelper — same ledger the tenant sees) ===
    const transactions = <?= json_encode($transactions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const tenants = <?= json_encode($tenants, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // === STATE ===
    let currentTenantId = null;
    let masterFilter = 'all';
    const tenantStats = {}; // tid => { charges, payments, forwarded, balance }

    // Index transactions per tenant (keep SoaHelper's chronological order)
    const txByTenant = {};
    transactions.forEach(t => {
        (txByTenant[t.tenant_id] = txByTenant[t.tenant_id] || []).push(t);
    });

    // === HELPERS ===
    const num = v => { const n = parseFloat(v); return isNaN(n) ? 0 : n; };
    const round2 = n => Math.round(n * 100) / 100;
    function fmtPeso(n) {
        n = round2(n);
        const s = Math.abs(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return (n < 0 ? '-₱' : '₱') + s;
    }
    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function monthLabel(ym) {
        const [y, m] = ym.split('-').map(Number);
        return new Date(y, m - 1, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }
    function fmtDate(dateStr) {
        if (!dateStr) return '—';
        const p = String(dateStr).substring(0, 10).split('-').map(Number);
        if (p.length !== 3 || !p[0]) return String(dateStr);
        return new Date(p[0], p[1] - 1, p[2]).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }
    function tenantName(t) { return ((t.first_name || '') + ' ' + (t.last_name || '')).trim() || 'Unknown'; }
    function tenantUnit(t) { return t.room_number ? ((t.building ? t.building + '-' : '') + t.room_number) : 'Unassigned'; }
    function getBadgeClass(type) {
        const t = String(type || '').toLowerCase();
        if (t.includes('payment') || t.includes('advance)')) return 'payment';
        if (t.includes('deposit')) return 'deposit';
        if (t.includes('advance')) return 'advance';
        if (t.includes('rent')) return 'rent';
        if (t.includes('parking')) return 'parking';
        if (t.includes('water')) return 'water';
        if (t.includes('contribution')) return 'contribution';
        return '';
    }

    // === MONTH FILTER (defaults to All Time so totals match the tenant ledger) ===
    const allMonths = [...new Set(transactions.map(t => (t.date || '').substring(0, 7)).filter(m => /^\d{4}-\d{2}$/.test(m)))].sort();
    const globalFilter = document.getElementById('month-filter');
    globalFilter.innerHTML = '<option value="all">All Time History</option>' +
        allMonths.slice().reverse().map(m => `<option value="${m}">${monthLabel(m)}</option>`).join('');
    globalFilter.value = 'all';

    // === CALCULATIONS ===
    function calculateData() {
        const sel = globalFilter.value;
        tenants.forEach(t => {
            const s = { charges: 0, payments: 0, forwarded: 0, balance: 0 };
            (txByTenant[t.tenant_id] || []).forEach(tx => {
                const c = num(tx.charge), p = num(tx.payment);
                const m = (tx.date || '').substring(0, 7);
                if (sel === 'all' || m === sel) { s.charges += c; s.payments += p; }
                else if (m < sel) { s.forwarded += (c - p); }
            });
            // Balance as of the end of the selected month (or overall)
            s.balance = round2(s.forwarded + s.charges - s.payments);
            tenantStats[t.tenant_id] = s;
        });
    }

    function updateRibbon() {
        let charges = 0, payments = 0, outstanding = 0, credits = 0, unpaid = 0;
        tenants.forEach(t => {
            const s = tenantStats[t.tenant_id];
            charges += s.charges; payments += s.payments;
            if (s.balance > 0.004) { outstanding += s.balance; unpaid++; }
            else if (s.balance < -0.004) credits += Math.abs(s.balance);
        });
        document.getElementById('ribbon-charges').textContent = fmtPeso(charges);
        document.getElementById('ribbon-payments').textContent = fmtPeso(payments);
        const r = document.getElementById('ribbon-net-standing');
        r.className = 'insight-value ' + (outstanding > 0 ? 'danger' : 'success');
        r.textContent = fmtPeso(outstanding);
        document.getElementById('ribbon-unpaid-sub').textContent =
            unpaid + ' tenant(s) with balance' + (credits > 0 ? ' · ' + fmtPeso(credits) + ' in credits' : '') +
            (globalFilter.value !== 'all' ? ' · as of end of ' + monthLabel(globalFilter.value) : '');
    }

    function onMonthFilterChange() {
        calculateData();
        initMasterTable();
        updateRibbon();
        if (currentTenantId) generateSOA();
    }

    // === MASTER TABLE ===
    function initMasterTable() {
        const body = document.getElementById('master-tenant-body');
        body.innerHTML = '';
        if (!tenants.length) {
            body.innerHTML = '<tr class="row-empty"><td colspan="7">No tenants with an active or past lease were found.</td></tr>';
            return;
        }
        tenants.forEach(t => {
            const tid = t.tenant_id;
            const s = tenantStats[tid];
            const name = tenantName(t), unit = tenantUnit(t);
            const initials = name.split(/\s+/).map(n => n[0] || '').join('').substring(0, 2).toUpperCase();
            const bal = s.balance;
            let statusLabel = 'Settled', statusBg = 'rgba(24,128,56,0.1)', statusColor = '#188038';
            if (bal > 0.004) { statusLabel = 'Unpaid'; statusBg = 'rgba(217,48,37,0.1)'; statusColor = '#d93025'; }
            else if (bal < -0.004) { statusLabel = 'Credit'; statusBg = 'rgba(37,99,235,0.1)'; statusColor = '#2563eb'; }

            const tr = document.createElement('tr');
            tr.dataset.name = name.toLowerCase();
            tr.dataset.room = unit.toLowerCase();
            tr.dataset.bal = bal;
            tr.innerHTML = `
                <td>
                    <div class="tenant-info-cell">
                        <div class="tenant-avatar">${esc(initials || '?')}</div>
                        <div class="tenant-meta">
                            <span class="tenant-name">${esc(name)}</span>
                            <span class="tenant-room">${esc(unit)}${t.roomtype ? ' · ' + esc(t.roomtype) : ''}</span>
                        </div>
                    </div>
                </td>
                <td><span style="font-size:0.8rem;font-weight:600;">${esc(t.lease_status || '—')}</span>
                    <div style="font-size:0.72rem;color:var(--text-muted);">${fmtDate(t.lease_start)} – ${t.lease_end ? fmtDate(t.lease_end) : 'Open'}</div></td>
                <td>${fmtPeso(s.charges)}</td>
                <td style="color:var(--success);">${fmtPeso(s.payments)}</td>
                <td class="${bal > 0.004 ? 'balance-positive' : 'balance-zero'}">${fmtPeso(bal)}</td>
                <td><span class="type-badge" style="background:${statusBg};color:${statusColor}">${statusLabel}</span></td>
                <td><button class="btn-view-soa" onclick="showDetailView(${Number(tid)})">View Details</button></td>`;
            body.appendChild(tr);
        });
        applyFilters();
    }

    // === VIEW TRANSITIONS ===
    function showDetailView(tid) {
        currentTenantId = tid;
        document.getElementById('soa-list-view').style.display = 'none';
        document.getElementById('soa-detail-view').style.display = 'block';
        document.getElementById('filter-buttons-group').style.display = 'none';
        document.getElementById('search-container').style.display = 'none';
        generateSOA();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    function showListView() {
        document.getElementById('soa-list-view').style.display = 'block';
        document.getElementById('soa-detail-view').style.display = 'none';
        document.getElementById('filter-buttons-group').style.display = 'flex';
        document.getElementById('search-container').style.display = 'block';
        currentTenantId = null;
    }
    function printCurrentView() {
        document.body.classList.remove('print-list', 'print-detail');
        document.body.classList.add(currentTenantId ? 'print-detail' : 'print-list');
        window.print();
    }

    // === FILTERING ===
    function setMasterFilter(type) {
        masterFilter = type;
        document.getElementById('btn-show-all').style.background = type === 'all' ? 'var(--primary-light)' : 'white';
        document.getElementById('btn-show-unpaid').style.background = type === 'unpaid' ? 'rgba(217, 48, 37, 0.1)' : 'white';
        document.getElementById('btn-show-paid').style.background = type === 'paid' ? 'rgba(24, 128, 56, 0.1)' : 'white';
        document.getElementById('btn-show-unpaid').style.borderColor = type === 'unpaid' ? 'var(--danger)' : 'var(--border)';
        document.getElementById('btn-show-paid').style.borderColor = type === 'paid' ? 'var(--success)' : 'var(--border)';
        document.getElementById('master-filter-badge').style.display = type === 'unpaid' ? 'block' : 'none';
        document.getElementById('outstanding-filter-card').style.borderColor = type === 'unpaid' ? 'var(--danger)' : 'var(--border)';
        applyFilters();
    }
    function applyFilters() {
        const q = (document.getElementById('tenant-search').value || '').toLowerCase().trim();
        let shown = 0;
        document.querySelectorAll('#master-tenant-body tr').forEach(row => {
            if (row.classList.contains('row-empty')) return;
            const bal = parseFloat(row.dataset.bal);
            let visible = true;
            if (q && !row.dataset.name.includes(q) && !row.dataset.room.includes(q)) visible = false;
            if (masterFilter === 'unpaid' && !(bal > 0.004)) visible = false;
            if (masterFilter === 'paid' && bal > 0.004) visible = false;
            row.style.display = visible ? '' : 'none';
            if (visible) shown++;
        });
        let empty = document.getElementById('master-no-match');
        if (!shown && tenants.length) {
            if (!empty) {
                empty = document.createElement('tr');
                empty.id = 'master-no-match';
                empty.className = 'row-empty';
                empty.innerHTML = '<td colspan="7">No tenants match the current filter.</td>';
                document.getElementById('master-tenant-body').appendChild(empty);
            }
            empty.style.display = '';
        } else if (empty) {
            empty.style.display = 'none';
        }
    }

    // === CSV EXPORT (overview, respects month filter) ===
    function exportOverviewCsv() {
        const sel = globalFilter.value;
        const rows = [['Tenant', 'Unit', 'Room Type', 'Lease Status', 'Lease Start', 'Lease End',
                       'Balance Forwarded', 'Charges', 'Payments', 'Balance', 'Status']];
        tenants.forEach(t => {
            const s = tenantStats[t.tenant_id];
            const status = s.balance > 0.004 ? 'Unpaid' : (s.balance < -0.004 ? 'Credit' : 'Settled');
            rows.push([tenantName(t), tenantUnit(t), t.roomtype || '', t.lease_status || '', t.lease_start || '', t.lease_end || '',
                       s.forwarded.toFixed(2), s.charges.toFixed(2), s.payments.toFixed(2), s.balance.toFixed(2), status]);
        });
        const csv = rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')).join('\r\n');
        const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'ISCAG_SOA_Overview_' + (sel === 'all' ? 'AllTime' : sel) + '.csv';
        document.body.appendChild(a); a.click(); a.remove();
    }

    // === SOA DOCUMENT ===
    function generateSOA() {
        if (!currentTenantId) return;
        const tenant = tenants.find(t => t.tenant_id == currentTenantId);
        if (!tenant) return;

        const sel = globalFilter.value;
        const all = txByTenant[currentTenantId] || [];
        let balForwarded = 0;
        let rows = all;
        if (sel !== 'all') {
            balForwarded = all.filter(t => (t.date || '').substring(0, 7) < sel)
                              .reduce((sum, t) => sum + num(t.charge) - num(t.payment), 0);
            rows = all.filter(t => (t.date || '').substring(0, 7) === sel);
        }
        balForwarded = round2(balForwarded);

        // Header
        document.getElementById('soa-tenant-name').textContent = tenantName(tenant);
        document.getElementById('soa-room-id').textContent = tenantUnit(tenant);
        document.getElementById('soa-tenant-email').textContent = tenant.email || '—';
        document.getElementById('soa-occupants').textContent = (tenant.occupants || 1) + ' person(s)';
        document.getElementById('soa-roomtype').textContent = tenant.roomtype || 'Apartment';
        document.getElementById('soa-lease').textContent = (tenant.lease_status || '—') + ' · ' + fmtDate(tenant.lease_start) + ' – ' + (tenant.lease_end ? fmtDate(tenant.lease_end) : 'Open');
        document.getElementById('soa-rent').textContent = fmtPeso(num(tenant.monthly_rent));
        document.getElementById('soa-period').textContent = sel === 'all' ? 'All Time' : monthLabel(sel);

        // Breakdown of charges in period
        const bk = { rent: 0, deposit: 0, advance: 0, parking: 0, water: 0, contribution: 0, other: 0 };
        let advApplied = 0;
        rows.forEach(t => {
            const c = num(t.charge), ty = String(t.type || '').toLowerCase();
            if (c > 0) {
                if (ty.includes('deposit')) bk.deposit += c;
                else if (ty.includes('advance')) bk.advance += c;
                else if (ty.includes('rent')) bk.rent += c;
                else if (ty.includes('parking')) bk.parking += c;
                else if (ty.includes('water')) bk.water += c;
                else if (ty.includes('contribution')) bk.contribution += c;
                else bk.other += c;
            }
            if (num(t.payment) > 0 && (String(t.description || '').toLowerCase().includes('advance') || ty.includes('advance'))) advApplied += num(t.payment);
        });
        const card = (label, val, sub) => val > 0 ? `<div class="breakdown-card"><div class="bk-label">${label}</div><div class="bk-value">${fmtPeso(val)}</div>${sub ? `<div class="bk-sub">${sub}</div>` : ''}</div>` : '';
        document.getElementById('soa-breakdown').innerHTML =
            card('Monthly Rent', bk.rent, esc(tenant.roomtype || 'Unit Fee')) +
            card('Security Deposit', bk.deposit) +
            card('Advance Rent', bk.advance) +
            card('Parking Fee', bk.parking) +
            card('Water Bill', bk.water, (tenant.occupants || 1) + ' person(s) × ₱100') +
            card('Monthly Contribution', bk.contribution, 'Security &amp; Garbage') +
            card('Other Charges', bk.other) +
            card('Paid via Advance Credits', advApplied, 'Applied from prepaid advances');

        // Ledger
        const tbody = document.getElementById('soa-table-body');
        tbody.innerHTML = '';
        let running = balForwarded, totalCharges = 0, totalPayments = 0, lastPeriod = null;

        if (sel !== 'all') {
            const label = balForwarded < 0 ? 'Overpayment Credit' : (balForwarded > 0 ? 'Unpaid Balance' : 'No Carry-over');
            const color = balForwarded > 0 ? 'var(--danger)' : 'var(--success)';
            const bf = document.createElement('tr');
            bf.innerHTML = `<td colspan="6"><strong>Balance Forwarded from Previous Months (${label})</strong></td><td style="text-align:right;font-weight:700;color:${color}">${fmtPeso(balForwarded)}</td>`;
            tbody.appendChild(bf);
        }

        if (!rows.length) {
            const er = document.createElement('tr');
            er.className = 'row-empty';
            er.innerHTML = '<td colspan="7">No transactions recorded for this period.</td>';
            tbody.appendChild(er);
        }

        rows.forEach(t => {
            const period = t.period || monthLabel((t.date || '').substring(0, 7));
            if (period !== lastPeriod) {
                const sec = document.createElement('tr');
                sec.innerHTML = `<td colspan="7" class="soa-section-title">${esc(period)}</td>`;
                tbody.appendChild(sec);
                lastPeriod = period;
            }
            const c = num(t.charge), p = num(t.payment);
            running = round2(running + c - p);
            totalCharges += c; totalPayments += p;
            const tr = document.createElement('tr');
            tr.className = p > 0 ? 'row-payment' : 'row-charge';
            tr.innerHTML = `
                <td>${fmtDate(t.date)}</td>
                <td><span class="type-badge ${getBadgeClass(t.type)}">${esc(t.type)}</span></td>
                <td>${esc(t.description)}</td>
                <td style="font-family:monospace;font-size:0.8rem;">${esc(t.ref)}</td>
                <td style="text-align:right;">${c > 0 ? fmtPeso(c) : '-'}</td>
                <td style="text-align:right;color:var(--success);">${p > 0 ? fmtPeso(p) : '-'}</td>
                <td style="text-align:right;font-weight:700;color:${running > 0.004 ? 'var(--danger)' : 'var(--success)'}">${fmtPeso(running)}</td>`;
            tbody.appendChild(tr);
        });

        document.getElementById('soa-total-charges').textContent = fmtPeso(totalCharges);
        document.getElementById('soa-total-payments').textContent = fmtPeso(totalPayments);
        const netEl = document.getElementById('soa-net-standing');
        netEl.textContent = fmtPeso(running);
        netEl.style.color = running > 0.004 ? 'var(--danger)' : 'var(--success)';
        const netLabel = netEl.previousElementSibling;
        if (netLabel) netLabel.textContent = running < -0.004 ? 'Credit Balance:' : 'Outstanding Balance:';
    }

    // === BOOT ===
    setMasterFilter('all');
    onMonthFilterChange();
  </script>
</body>
</html>
