<?php
$active_page = 'apartment_records';
$kpis = $kpis ?? ['totalUnits' => 0, 'availableUnits' => 0, 'occupiedUnits' => 0];
$unitCategories = $unitCategories ?? [];
$allUnits = $allUnits ?? [];
$apartmentBills = $apartmentBills ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Unit Inventory & Apartment Billing</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .inventory-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      padding: 14px 18px;
      background: #ffffff;
      border-bottom: 1px solid var(--border);
    }
    .inventory-toolbar-left,
    .inventory-toolbar-right {
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
      width: 220px;
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
            <div class="top-bar-title">Unit Inventory & Apartment Billing</div>
            <div class="top-bar-subtitle">Real-time inventory status, tenant occupancy, and verified lease billing schedules</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin/maintenance') ?>" class="btn-topbar">Maintenance Requests</a>
          <a href="<?= url('/admin/mis_admin/reports?dept=Maintenance') ?>" class="btn-topbar">Maintenance Report</a>
          <a href="<?= url('/admin/mis_admin/apartment_confirmation') ?>" class="btn-topbar primary">Applications for Review</a>
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a>
          <span class="sep">›</span>
          <span class="current">Unit Inventory</span>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-units"><?= (int)$kpis['totalUnits'] ?></div>
              <div class="stat-label">Total Units</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-avail"><?= (int)$kpis['availableUnits'] ?></div>
              <div class="stat-label">Available Slots</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="s-occ"><?= (int)$kpis['occupiedUnits'] ?></div>
              <div class="stat-label">Occupied Units</div>
            </div>
          </div>
        </div>

        <!-- ══════════════ UNIT INVENTORY TABLE ══════════════ -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg>
              Unit Inventory
            </h6>
            <div class="pill-tabs" id="inventoryViewSwitch">
              <button type="button" class="pill-tab active" data-view="units">All Units (<?= count($allUnits) ?>)</button>
              <button type="button" class="pill-tab" data-view="categories">By Category (<?= count($unitCategories) ?>)</button>
            </div>
          </div>

          <div class="inventory-toolbar">
            <div class="inventory-toolbar-left">
              <div class="toolbar-search">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="unit-search" placeholder="Search room, building, type..." />
              </div>
              <select class="toolbar-select" id="unit-status-filter">
                <option value="">All Statuses</option>
                <option value="Available">Available</option>
                <option value="Occupied">Occupied</option>
                <option value="Reserved">Reserved</option>
              </select>
            </div>
            <div class="inventory-toolbar-right" id="unit-view-counter" style="font-size:0.8rem;color:var(--text-muted);">
              Total: <?= count($allUnits) ?> registered units
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="inventory-table" data-paginate="false">
                <thead id="unit-thead">
                  <!-- Populated by JS based on current view -->
                </thead>
                <tbody id="unit-tbody">
                  <!-- Populated by JS -->
                </tbody>
              </table>
            </div>
            <!-- Pagination Controls (10 per page) -->
            <div id="unit-pagination" class="mis-pagination-bar"></div>
          </div>
        </div>

        <!-- ══════════════ APARTMENT BILLING TABLE ══════════════ -->
        <div class="section-card" style="margin-top:24px;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path d="M6 2h7.5C16.54 2 19 4.46 19 7.5S16.54 13 13.5 13H8.5v8H6V2zm2.5 9H13.5c1.93 0 3.5-1.57 3.5-3.5S15.43 4 13.5 4H8.5v7z"/><path d="M3 6h15v2H3z"/><path d="M3 9.5h14v2H3z"/>
              </svg>
              Apartment Billing
            </h6>
            <div style="font-size:0.8rem;color:var(--text-muted);font-weight:500;">
              Accurate Lease Schedule & Settlements
            </div>
          </div>

          <div class="inventory-toolbar">
            <div class="inventory-toolbar-left">
              <div class="toolbar-search">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="bill-search" placeholder="Search tenant, Bill ID, room..." />
              </div>
              <select class="toolbar-select" id="bill-status-filter">
                <option value="">All Statuses</option>
                <option value="Paid">Paid</option>
                <option value="Pending">Pending</option>
                <option value="Overdue">Overdue</option>
              </select>
            </div>
            <div class="inventory-toolbar-right" style="font-size:0.8rem;color:var(--text-muted);">
              Total: <?= count($apartmentBills) ?> billing schedules
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="billing-table" data-paginate="false">
                <thead>
                  <tr>
                    <th>Bill ID</th>
                    <th>Tenant</th>
                    <th>Unit / Room</th>
                    <th>Billing Period</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Payment Status</th>
                  </tr>
                </thead>
                <tbody id="bill-tbody">
                  <!-- Populated by JS -->
                </tbody>
              </table>
            </div>
            <!-- Pagination Controls (10 per page) -->
            <div id="bill-pagination" class="mis-pagination-bar"></div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══════════════════════════════════════════════════════════
    // REAL DATABASE RECORDS (Accurate dates, not generated)
    // ══════════════════════════════════════════════════════════
    const RAW_UNITS = <?= json_encode($allUnits, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const RAW_CATEGORIES = <?= json_encode($unitCategories, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const RAW_BILLS = <?= json_encode($apartmentBills, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const PAGE_SIZE = 10;

    // ── Helper formatters ──
    function formatCurrency(num) {
      return '₱' + Number(num || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDisplayDate(dateStr) {
      if (!dateStr) return '—';
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) return dateStr;
      return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    function buildPaginationControls(currentPage, totalPages, targetContainerId, onPageChange) {
      const container = document.getElementById(targetContainerId);
      if (!container) return;

      if (totalPages <= 1) {
        container.innerHTML = `<div class="mis-pagination-info">Showing all records</div>`;
        return;
      }

      let btns = `<button type="button" class="mis-page-btn" data-act="prev" ${currentPage === 1 ? 'disabled' : ''}>&laquo; Prev</button>`;

      const pages = [];
      if (totalPages <= 7) {
        for (let i = 1; i <= totalPages; i++) pages.push(i);
      } else {
        pages.push(1);
        if (currentPage > 3) pages.push('...');
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage < totalPages - 2) pages.push('...');
        pages.push(totalPages);
      }

      pages.forEach(p => {
        if (p === '...') {
          btns += `<span class="mis-page-ellipsis">&hellip;</span>`;
        } else {
          btns += `<button type="button" class="mis-page-btn ${p === currentPage ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }
      });

      btns += `<button type="button" class="mis-page-btn" data-act="next" ${currentPage === totalPages ? 'disabled' : ''}>Next &raquo;</button>`;

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
            onPageChange(parseInt(page, 10));
          } else if (act === 'prev' && currentPage > 1) {
            onPageChange(currentPage - 1);
          } else if (act === 'next' && currentPage < totalPages) {
            onPageChange(currentPage + 1);
          }
        });
      });

      container.appendChild(ctrlDiv);
    }

    // ══════════════════════════════════════════════════════════
    // 1. UNIT INVENTORY CONTROLLER
    // ══════════════════════════════════════════════════════════
    let currentUnitView = 'units'; // 'units' or 'categories'
    let unitCurrentPage = 1;

    function renderUnitInventory(resetPage = false) {
      if (resetPage) unitCurrentPage = 1;

      const searchTerm = (document.getElementById('unit-search').value || '').toLowerCase().trim();
      const statusFilter = document.getElementById('unit-status-filter').value;
      const thead = document.getElementById('unit-thead');
      const tbody = document.getElementById('unit-tbody');
      const paginationBar = document.getElementById('unit-pagination');

      if (currentUnitView === 'units') {
        // Table Header for Individual Units
        thead.innerHTML = `
          <tr>
            <th>Unit / Room</th>
            <th>Type & Building</th>
            <th>Monthly Rate</th>
            <th>Occupant / Tenant</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th style="text-align:center;">Action</th>
          </tr>
        `;

        // Filter individual units
        const filtered = RAW_UNITS.filter(u => {
          const matchSearch = !searchTerm ||
            (u.room_number && u.room_number.toLowerCase().includes(searchTerm)) ||
            (u.building && u.building.toLowerCase().includes(searchTerm)) ||
            (u.type_label && u.type_label.toLowerCase().includes(searchTerm)) ||
            (u.first_name && (u.first_name + ' ' + u.last_name).toLowerCase().includes(searchTerm));

          const matchStatus = !statusFilter || (u.status === statusFilter);
          return matchSearch && matchStatus;
        });

        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;
        if (unitCurrentPage > totalPages) unitCurrentPage = totalPages;

        const startIdx = (unitCurrentPage - 1) * PAGE_SIZE;
        const endIdx = Math.min(startIdx + PAGE_SIZE, totalItems);
        const pageItems = filtered.slice(startIdx, endIdx);

        if (totalItems === 0) {
          tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">No units match the specified search and filter criteria.</td></tr>`;
          paginationBar.innerHTML = `<div class="mis-pagination-info">Showing 0 of 0 units</div>`;
          return;
        }

        tbody.innerHTML = pageItems.map(u => {
          const statusBadge = u.status === 'Available'
            ? '<span class="badge-status badge-approved">Available</span>'
            : u.status === 'Occupied'
              ? '<span class="badge-status badge-rejected">Occupied</span>'
              : '<span class="badge-status badge-pending">' + (u.status || 'Pending') + '</span>';

          const tenantInfo = u.first_name
            ? `<span style="font-weight:600;color:var(--primary);">${u.first_name} ${u.last_name}</span>`
            : `<span style="color:var(--text-muted);font-style:italic;">Vacant</span>`;

          const updatedDate = formatDisplayDate(u.updated_at || u.created_at);

          return `
            <tr>
              <td class="td-id" style="font-weight:700;">${u.room_number || ('Unit #' + u.unit_id)}</td>
              <td>
                <div class="td-stacked">
                  <span style="font-weight:600;">${u.type_label || 'Unit'}</span>
                  <span class="td-sub">${u.building || 'Main Complex'}</span>
                </div>
              </td>
              <td style="font-weight:700;">${formatCurrency(u.price)}</td>
              <td>${tenantInfo}</td>
              <td>${statusBadge}</td>
              <td style="color:var(--text-muted);font-size:0.82rem;">${updatedDate}</td>
              <td style="text-align:center;">
                <button type="button" class="btn-sm" onclick="showToast('Unit: ${u.room_number || u.unit_id} (${u.status})','var(--info)')" style="padding:4px 8px;font-size:0.75rem;border-radius:4px;border:1px solid var(--border);background:#fff;cursor:pointer;">
                  View
                </button>
              </td>
            </tr>
          `;
        }).join('');

        paginationBar.innerHTML = `<div class="mis-pagination-info">Showing ${startIdx + 1}–${endIdx} of ${totalItems} units</div>`;
        buildPaginationControls(unitCurrentPage, totalPages, 'unit-pagination', (p) => {
          unitCurrentPage = p;
          renderUnitInventory(false);
        });

      } else {
        // Table Header for Categories
        thead.innerHTML = `
          <tr>
            <th>Type ID</th>
            <th>Unit Classification</th>
            <th>Monthly Rent</th>
            <th>Total Units</th>
            <th>Available Slots</th>
            <th>Occupied</th>
            <th>Status</th>
          </tr>
        `;

        const filtered = RAW_CATEGORIES.filter(c => {
          const matchSearch = !searchTerm ||
            (c.label && c.label.toLowerCase().includes(searchTerm)) ||
            (c.type_key && c.type_key.toLowerCase().includes(searchTerm));
          const matchStatus = !statusFilter ||
            (statusFilter === 'Available' && Number(c.available_units) > 0) ||
            (statusFilter === 'Occupied' && Number(c.available_units) === 0);
          return matchSearch && matchStatus;
        });

        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;
        if (unitCurrentPage > totalPages) unitCurrentPage = totalPages;

        const startIdx = (unitCurrentPage - 1) * PAGE_SIZE;
        const endIdx = Math.min(startIdx + PAGE_SIZE, totalItems);
        const pageItems = filtered.slice(startIdx, endIdx);

        if (totalItems === 0) {
          tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">No categories match your criteria.</td></tr>`;
          paginationBar.innerHTML = `<div class="mis-pagination-info">Showing 0 of 0 categories</div>`;
          return;
        }

        tbody.innerHTML = pageItems.map(c => {
          const isAvail = Number(c.available_units) > 0;
          const statusBadge = isAvail
            ? '<span class="badge-status badge-approved">Available</span>'
            : '<span class="badge-status badge-rejected">Full</span>';

          return `
            <tr>
              <td class="td-id">TYP-${String(c.type_id).padStart(2, '0')}</td>
              <td style="font-weight:600;">${c.label}</td>
              <td style="font-weight:700;">${formatCurrency(c.price)}</td>
              <td style="font-weight:600;">${c.total_units} units</td>
              <td style="font-weight:700;color:${isAvail ? 'var(--success)' : 'var(--danger)'};">${c.available_units} slots</td>
              <td style="color:var(--text-muted);">${c.occupied_units} occ</td>
              <td>${statusBadge}</td>
            </tr>
          `;
        }).join('');

        paginationBar.innerHTML = `<div class="mis-pagination-info">Showing ${startIdx + 1}–${endIdx} of ${totalItems} categories</div>`;
        buildPaginationControls(unitCurrentPage, totalPages, 'unit-pagination', (p) => {
          unitCurrentPage = p;
          renderUnitInventory(false);
        });
      }
    }

    // ══════════════════════════════════════════════════════════
    // 2. APARTMENT BILLING CONTROLLER (Accurate lease dates)
    // ══════════════════════════════════════════════════════════
    let billCurrentPage = 1;

    function renderApartmentBilling(resetPage = false) {
      if (resetPage) billCurrentPage = 1;

      const searchTerm = (document.getElementById('bill-search').value || '').toLowerCase().trim();
      const statusFilter = document.getElementById('bill-status-filter').value;
      const tbody = document.getElementById('bill-tbody');
      const paginationBar = document.getElementById('bill-pagination');

      const filtered = RAW_BILLS.filter(b => {
        const matchSearch = !searchTerm ||
          (b.id && b.id.toLowerCase().includes(searchTerm)) ||
          (b.tenant_name && b.tenant_name.toLowerCase().includes(searchTerm)) ||
          (b.room && b.room.toLowerCase().includes(searchTerm)) ||
          (b.period && b.period.toLowerCase().includes(searchTerm));

        const matchStatus = !statusFilter || (b.status === statusFilter);
        return matchSearch && matchStatus;
      });

      const totalItems = filtered.length;
      const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;
      if (billCurrentPage > totalPages) billCurrentPage = totalPages;

      const startIdx = (billCurrentPage - 1) * PAGE_SIZE;
      const endIdx = Math.min(startIdx + PAGE_SIZE, totalItems);
      const pageItems = filtered.slice(startIdx, endIdx);

      if (totalItems === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">No apartment billing records found matching the filter criteria.</td></tr>`;
        paginationBar.innerHTML = `<div class="mis-pagination-info">Showing 0 of 0 billing schedules</div>`;
        return;
      }

      tbody.innerHTML = pageItems.map(b => {
        let badge = '<span class="badge-status badge-pending">Pending</span>';
        if (b.status === 'Paid') badge = '<span class="badge-status badge-approved">Paid</span>';
        else if (b.status === 'Overdue') badge = '<span class="badge-status badge-rejected">Overdue</span>';

        const paymentNote = b.status === 'Paid'
          ? (b.payment_date ? `<div class="td-sub" style="color:var(--success);">Settled ${formatDisplayDate(b.payment_date)}</div>` : '')
          : (b.balance > 0 ? `<div class="td-sub" style="color:var(--danger);">Balance: ${formatCurrency(b.balance)}</div>` : '');

        return `
          <tr>
            <td class="td-id" style="font-weight:700;">${b.id}</td>
            <td>
              <div class="td-stacked">
                <span style="font-weight:600;">${b.tenant_name}</span>
                <span class="td-sub">ID: ${b.tenant_id}</span>
              </div>
            </td>
            <td>${b.room}</td>
            <td style="font-size:0.83rem;">${b.period}</td>
            <td style="font-weight:700;">${formatCurrency(b.amount)}</td>
            <td>
              <div class="td-stacked">
                <span style="font-weight:600;">${formatDisplayDate(b.due_date)}</span>
                ${paymentNote}
              </div>
            </td>
            <td>${badge}</td>
          </tr>
        `;
      }).join('');

      paginationBar.innerHTML = `<div class="mis-pagination-info">Showing ${startIdx + 1}–${endIdx} of ${totalItems} billing schedules</div>`;
      buildPaginationControls(billCurrentPage, totalPages, 'bill-pagination', (p) => {
        billCurrentPage = p;
        renderApartmentBilling(false);
      });
    }

    // ── View switch listener ──
    document.querySelectorAll('#inventoryViewSwitch .pill-tab').forEach(tab => {
      tab.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('#inventoryViewSwitch .pill-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentUnitView = this.dataset.view;
        renderUnitInventory(true);
      });
    });

    // ── Input change listeners ──
    document.getElementById('unit-search').addEventListener('input', () => renderUnitInventory(true));
    document.getElementById('unit-status-filter').addEventListener('change', () => renderUnitInventory(true));
    document.getElementById('bill-search').addEventListener('input', () => renderApartmentBilling(true));
    document.getElementById('bill-status-filter').addEventListener('change', () => renderApartmentBilling(true));

    // ── Initial Render ──
    renderUnitInventory(true);
    renderApartmentBilling(true);
  </script>
</body>

</html>
