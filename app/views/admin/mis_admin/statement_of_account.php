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

    @media print {
      body * { visibility: hidden; }
      .soa-container, .soa-container * { visibility: visible; }
      .soa-container {
        position: absolute; left: 0; top: 0; width: 100%;
        box-shadow: none; border: none; padding: 0;
      }
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
          <div class="insight-card clickable" id="outstanding-filter-card" onclick="setMasterFilter(masterFilter === 'unpaid' ? 'all' : 'unpaid')">
            <div class="filter-active-badge" id="master-filter-badge">Filter On</div>
            <?php
              $totalOutstanding = 0;
              foreach($transactions as $t) {
                $totalOutstanding += ($t['charge'] ?? 0) - ($t['payment'] ?? 0);
              }
            ?>
            <div class="insight-label">Net Outstanding</div>
            <div class="insight-value <?= $totalOutstanding > 0 ? 'danger' : 'success' ?>" id="ribbon-net-standing">₱<?= number_format(abs($totalOutstanding), 2) ?></div>
          </div>
        </div>
        
        <div class="controls-panel">
          <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column; gap: 8px; align-items: flex-start;" id="filter-buttons-group">
            
            <div style="display: flex; gap: 12px; align-items: center; height: 42px;">
              <button class="btn-topbar" id="btn-show-all" onclick="setMasterFilter('all')" style="border-radius: 8px; border-color: var(--border); height: 42px;">All Tenants</button>
              <button class="btn-topbar" id="btn-show-unpaid" onclick="setMasterFilter('unpaid')" style="border-radius: 8px; border-color: var(--border); height: 42px;">Show Only Unpaid</button>
              <button class="btn-topbar" id="btn-show-paid" onclick="setMasterFilter('paid')" style="border-radius: 8px; border-color: var(--border); height: 42px;">Show Only Paid</button>
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

    // === DATA SOURCE ===
    const transactions = <?= json_encode($transactions) ?>;
    const tenants = <?= json_encode($tenants) ?>;
    const memberMap = <?= json_encode($memberMap ?? []) ?>;
    
    // === STATE MANAGEMENT ===
    let currentTenantId = null;
    let masterFilter = 'all'; 
    const tenantBalances = {};
    const tenantTotals = {}; 

    // === GLOBAL FILTER INITIALIZATION ===
    const allMonths = [...new Set(transactions.map(t => t.date.substring(0, 7)))].sort();
    const globalFilter = document.getElementById('month-filter');
    globalFilter.innerHTML = '';
    allMonths.forEach(m => {
        const date = new Date(m + '-01');
        const label = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        globalFilter.innerHTML += `<option value="${m}">${label}</option>`;
    });
    globalFilter.innerHTML += '<option value="all">All Time History</option>';
    if (allMonths.length > 0) {
        globalFilter.value = allMonths[0]; // default to first month
    }

    // === INITIALIZATION ===
    function calculateData() {
        const selectedMonth = globalFilter.value;

        tenants.forEach(t => {
            const tid = t.tenant_id || t.id;
            tenantBalances[tid] = 0;
            tenantTotals[tid] = { charges: 0, payments: 0 };
        });

        transactions.forEach(t => {
            const tid = t.tenant_id;
            if (tenantBalances[tid] === undefined) return;
            
            if (selectedMonth !== 'all' && !t.date.startsWith(selectedMonth)) return;

            tenantBalances[tid] += (parseFloat(t.charge) - parseFloat(t.payment));
            tenantTotals[tid].charges += parseFloat(t.charge);
            tenantTotals[tid].payments += parseFloat(t.payment);
        });
    }

    function onMonthFilterChange() {
        calculateData();
        initMasterTable();
        
        let newNet = 0;
        transactions.forEach(t => {
            // IGNORE ghost admin leases: only calculate for visible tenants in the array
            if (tenantBalances[t.tenant_id] === undefined) return;
            
            if (globalFilter.value !== 'all' && !t.date.startsWith(globalFilter.value)) return;
            newNet += (parseFloat(t.charge || 0) - parseFloat(t.payment || 0));
        });
        
        const ribbon = document.getElementById('ribbon-net-standing');
        if (ribbon) {
            ribbon.className = 'insight-value ' + (newNet > 0 ? 'danger' : 'success');
            ribbon.textContent = '₱' + Math.abs(newNet).toLocaleString(undefined, {minimumFractionDigits:2});
        }

        if (currentTenantId) {
            generateSOA();
        }
    }
    
    // Kickstart the filter to build the initial ribbon properly
    onMonthFilterChange();

    function initMasterTable() {
        const body = document.getElementById('master-tenant-body');
        if (!body) return;
        body.innerHTML = '';
        
        tenants.forEach(t => {
            const tid = t.tenant_id || t.id;
            const fullName = (t.first_name || '') + ' ' + (t.last_name || t.name || 'Unknown');
            const roomNum = t.room_number ? (t.building + '-' + t.room_number) : (t.room_id || 'N/A');
            
            const bal = tenantBalances[tid] || 0;
            const totals = tenantTotals[tid] || { charges: 0, payments: 0 };
            
            const initials = fullName.trim().split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
            
            const tr = document.createElement('tr');
            tr.setAttribute('data-name', fullName.toLowerCase());
            tr.setAttribute('data-room', roomNum.toLowerCase());
            tr.setAttribute('data-bal', bal);
            
            tr.innerHTML = `
                <td>
                    <div class="tenant-info-cell">
                        <div class="tenant-avatar">${initials || '?'}</div>
                        <div class="tenant-meta">
                            <span class="tenant-name">${fullName}</span>
                            <span class="tenant-room">${roomNum}</span>
                        </div>
                    </div>
                </td>
                <td>₱${totals.charges.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                <td>₱${totals.payments.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                <td class="${bal > 0 ? 'balance-positive' : 'balance-zero'}">
                    ₱${bal.toLocaleString(undefined, {minimumFractionDigits: 2})}
                </td>
                <td>
                    <span class="type-badge ${bal > 0 ? 'rent' : 'water'}" style="background:${bal > 0 ? 'rgba(217, 48, 37, 0.1)' : 'rgba(24, 128, 56, 0.1)'}; color:${bal > 0 ? '#d93025' : '#188038'}">
                        ${bal > 0 ? 'Unpaid' : 'Settled'}
                    </span>
                </td>
                <td>
                    <button class="btn-view-soa" onclick="showDetailView('${tid}')">View Details</button>
                </td>
            `;
            body.appendChild(tr);
        });
        applyFilters();
    }

    // === VIEW TRANSITIONS ===
    function showDetailView(tid) {
        currentTenantId = tid;
        document.getElementById('soa-list-view').style.display = 'none';
        document.getElementById('soa-detail-view').style.display = 'block';
        // Hide master filtering buttons, keep month filter
        document.getElementById('filter-buttons-group').style.display = 'none';
        document.getElementById('search-container').style.display = 'none';
        generateSOA();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showListView() {
        document.getElementById('soa-list-view').style.display = 'block';
        document.getElementById('soa-detail-view').style.display = 'none';
        // Show master filtering buttons
        document.getElementById('filter-buttons-group').style.display = 'flex';
        document.getElementById('search-container').style.display = 'block';
        currentTenantId = null;
    }

    // === FILTERING LOGIC ===
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
        const searchInput = document.getElementById('tenant-search');
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const rows = document.querySelectorAll('#master-tenant-body tr');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const room = row.getAttribute('data-room');
            const bal = parseFloat(row.getAttribute('data-bal'));
            
            let visible = true;
            if (query && !name.includes(query) && !room.includes(query)) visible = false;
            if (masterFilter === 'unpaid' && bal <= 0) visible = false;
            if (masterFilter === 'paid' && bal > 0) visible = false;
            
            row.style.display = visible ? '' : 'none';
        });
    }

    // === SOA DOCUMENT LOGIC ===

    // === HELPER FUNCTIONS (Matching tenant_soa.php exactly) ===
    function getCategoryLabel(type) {
        const t = type.toLowerCase();
        if (t.includes('rent') && !t.includes('payment')) return 'Apartment Rent';
        if (t.includes('deposit') || t.includes('advance')) return 'Initial Payments';
        if (t.includes('parking')) return 'Parking';
        if (t.includes('water')) return 'Water Consumption';
        if (t.includes('contribution')) return 'Contribution';
        if (t.includes('payment')) return 'Payment Records';
        return 'Other';
    }
    function getBadgeClass(type) {
        const t = type.toLowerCase();
        if (t.includes('rent')) return 'rent';
        if (t.includes('deposit')) return 'deposit';
        if (t.includes('advance')) return 'advance';
        if (t.includes('parking')) return 'parking';
        if (t.includes('water')) return 'water';
        if (t.includes('contribution')) return 'contribution';
        if (t.includes('payment')) return 'payment';
        return '';
    }
    function fmtDate(dateStr) {
        const d = new Date(dateStr);
        const y = d.getFullYear();
        const m = String(d.getMonth()+1).padStart(2,'0');
        const day = String(d.getDate()).padStart(2,'0');
        return y+'-'+m+'-'+day;
    }
    function fmtPeso(n) { return '₱' + n.toLocaleString(undefined, {minimumFractionDigits:2}); }

    function generateSOA() {
        if (!currentTenantId) return;
        const tenant = tenants.find(t => (t.tenant_id || t.id) == currentTenantId);
        if (!tenant) return;

        const selectedMonth = document.getElementById('month-filter').value;
        const memberCount = memberMap[currentTenantId] || 0;
        const totalOccupants = parseInt(memberCount) + 1;
        
        let filtered = transactions.filter(t => t.tenant_id == currentTenantId).sort((a,b) => new Date(a.date) - new Date(b.date));
        let balForwarded = 0;

        if (selectedMonth !== 'all') {
            balForwarded = transactions
                .filter(t => t.tenant_id == currentTenantId && t.date < selectedMonth + '-01')
                .reduce((sum, t) => sum + (parseFloat(t.charge) - parseFloat(t.payment)), 0);
            filtered = filtered.filter(t => t.date.startsWith(selectedMonth));
        }

        // Header info
        document.getElementById('soa-tenant-name').textContent = (tenant.first_name || '') + ' ' + (tenant.last_name || tenant.name || '');
        document.getElementById('soa-room-id').textContent = tenant.room_number ? (tenant.building + '-' + tenant.room_number) : (tenant.room_id || 'Unassigned');
        document.getElementById('soa-tenant-email').textContent = tenant.email || '';
        document.getElementById('soa-occupants').textContent = totalOccupants + ' person(s)';
        document.getElementById('soa-roomtype').textContent = tenant.roomtype || 'Apartment';
        document.getElementById('soa-period').textContent = selectedMonth === 'all' ? 'All Time' : new Date(selectedMonth + '-01').toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
        // Breakdown Calculation
        let rentTotal = 0, waterTotal = 0, depTotal = 0, parkTotal = 0, contribTotal = 0;
        filtered.forEach(t => {
            const charge = parseFloat(t.charge) || 0;
            if (charge > 0) {
                const type = t.type.toLowerCase();
                if (type.includes('rent')) rentTotal += charge;
                else if (type.includes('deposit')) depTotal += charge;
                else if (type.includes('parking')) parkTotal += charge;
                else if (type.includes('water')) waterTotal += charge;
                else if (type.includes('contribution')) contribTotal += charge;
            }
        });

        let breakdownHtml = '';
        if (rentTotal > 0) breakdownHtml += `<div class="breakdown-card"><div class="bk-label">Monthly Rent</div><div class="bk-value">${fmtPeso(rentTotal)}</div><div class="bk-sub">${tenant.roomtype || 'Unit Fee'}</div></div>`;
        if (depTotal > 0) breakdownHtml += `<div class="breakdown-card"><div class="bk-label">Security Deposit</div><div class="bk-value">${fmtPeso(depTotal)}</div></div>`;
        if (parkTotal > 0) breakdownHtml += `<div class="breakdown-card"><div class="bk-label">Parking Fee</div><div class="bk-value">${fmtPeso(parkTotal)}</div></div>`;
        if (waterTotal > 0) breakdownHtml += `<div class="breakdown-card"><div class="bk-label">Water Bill</div><div class="bk-value">${fmtPeso(waterTotal)}</div><div class="bk-sub">${totalOccupants} person(s)</div></div>`;
        if (contribTotal > 0) breakdownHtml += `<div class="breakdown-card"><div class="bk-label">Monthly Contribution</div><div class="bk-value">${fmtPeso(contribTotal)}</div><div class="bk-sub">Security &amp; Garbage</div></div>`;
        document.getElementById('soa-breakdown').innerHTML = breakdownHtml;

        // Table body
        const tbody = document.getElementById('soa-table-body');
        tbody.innerHTML = '';

        let runningBalance = balForwarded;
        let totalCharges = 0;
        let totalPayments = 0;
        let lastCat = '';

        // Balance forwarded row
        if (selectedMonth !== 'all' && balForwarded !== 0) {
            const bfRow = document.createElement('tr');
            const label = balForwarded < 0 ? 'Overpayment Credit' : 'Unpaid Balance';
            const color = balForwarded > 0 ? 'var(--danger)' : 'var(--success)';
            bfRow.innerHTML = `<td colspan="6"><strong>Balance Forwarded from Previous Months (${label})</strong></td><td style="text-align:right; font-weight:700; color:${color}">${balForwarded < 0 ? '-' : ''}${fmtPeso(Math.abs(balForwarded))}</td>`;
            tbody.appendChild(bfRow);
        }

        // Transaction rows with section grouping
        filtered.forEach(t => {
            const cat = getCategoryLabel(t.type);
            if (cat !== lastCat) {
                const secRow = document.createElement('tr');
                secRow.innerHTML = `<td colspan="7" class="soa-section-title">${cat}</td>`;
                tbody.appendChild(secRow);
                lastCat = cat;
            }

            const charge = parseFloat(t.charge) || 0;
            const payment = parseFloat(t.payment) || 0;
            runningBalance += (charge - payment);
            totalCharges += charge;
            totalPayments += payment;

            const row = document.createElement('tr');
            row.className = payment > 0 ? 'row-payment' : 'row-charge';
            const balColor = runningBalance > 0 ? 'var(--danger)' : 'var(--success)';
            row.innerHTML = `
                <td>${fmtDate(t.date)}</td>
                <td><span class="type-badge ${getBadgeClass(t.type)}">${t.type}</span></td>
                <td>${t.description || ''}</td>
                <td style="font-family:monospace; font-size:0.8rem;">${t.ref || ''}</td>
                <td style="text-align:right;">${charge > 0 ? fmtPeso(charge) : '-'}</td>
                <td style="text-align:right; color:var(--success);">${payment > 0 ? fmtPeso(payment) : '-'}</td>
                <td style="text-align:right; font-weight:700; color:${balColor}">${fmtPeso(runningBalance)}</td>
            `;
            tbody.appendChild(row);
        });

        const netStanding = runningBalance;
        document.getElementById('soa-total-charges').textContent = fmtPeso(totalCharges);
        document.getElementById('soa-total-payments').textContent = fmtPeso(totalPayments);
        const netEl = document.getElementById('soa-net-standing');
        netEl.textContent = fmtPeso(netStanding);
        netEl.style.color = netStanding > 0 ? 'var(--danger)' : 'var(--success)';
    }

    calculateData();
    initMasterTable();

  </script>
</body>
</html>
