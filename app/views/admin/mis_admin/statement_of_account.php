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
    .soa-container {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 40px;
      max-width: 900px;
      margin: 20px auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
    .soa-table .row-charge { }
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

    .controls-panel {
      background: white;
      padding: 24px;
      border-radius: 12px;
      border: 1px solid var(--border);
      margin-bottom: 24px;
      display: flex;
      gap: 20px;
      align-items: flex-end;
      flex-wrap: wrap;
    }
    .form-group {
      flex: 1;
      min-width: 200px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--primary-dark);
    }
    .form-group select, .form-group input {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.2s;
    }
    .form-group select:focus, .form-group input:focus {
      border-color: var(--primary);
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
          <div class="insight-card">
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
            <select id="tenant-select" onchange="generateSOA()">
              <option value="">— Select a Tenant —</option>
              <?php foreach($tenants as $t): ?>
                <option value="<?= $t['tenant_id'] ?>"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?> (<?= $t['room_number'] ? $t['building'].'-'.$t['room_number'] : 'No Unit' ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Date From</label>
            <input type="date" id="date-from" onchange="generateSOA()" />
          </div>
          <div class="form-group">
            <label>Date To</label>
            <input type="date" id="date-to" onchange="generateSOA()" />
          </div>
          <div>
            <button class="btn-action" style="padding:10px 20px; background:var(--primary); color:white; border-radius:8px;" onclick="generateSOA()">Generate</button>
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
          
          <div id="admin-soa-stamp" class="soa-stamp paid" style="display:none;">FULLY SETTLED</div>

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

    const transactions = <?= json_encode($transactions) ?>;
    const tenants = <?= json_encode($tenants) ?>;
    const memberMap = <?= json_encode($memberMap ?? []) ?>;

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

    function generateSOA() {
      const tenantId = document.getElementById('tenant-select').value;
      const dateFrom = document.getElementById('date-from').value;
      const dateTo = document.getElementById('date-to').value;

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
      
      renderSOA(tenantId, dateFrom, dateTo, info, totalOccupants);
    }

    // ══ AUTO-SELECT FROM URL ══
    window.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const tid = urlParams.get('tenant_id');
      const shouldPrint = urlParams.get('print');

      if (tid) {
        const select = document.getElementById('tenant-select');
        select.value = tid;
        generateSOA();

        if (shouldPrint === '1') {
            setTimeout(() => window.print(), 800); // Small delay to let cards render
        }
      }
    });

    function renderSOA(tenantId, dateFrom, dateTo, info, totalOccupants) {
      let periodText = 'All Time';
      if(dateFrom && dateTo) periodText = `${dateFrom} to ${dateTo}`;
      else if(dateFrom) periodText = `From ${dateFrom}`;
      else if(dateTo) periodText = `Up to ${dateTo}`;
      document.getElementById('soa-period').textContent = periodText;

      // Filter transactions for this tenant
      let filtered = transactions.filter(t => t.tenant_id == tenantId);
      if(dateFrom) filtered = filtered.filter(t => new Date(t.date) >= new Date(dateFrom));
      if(dateTo) filtered = filtered.filter(t => new Date(t.date) <= new Date(dateTo));

      filtered.sort((a,b) => new Date(a.date) - new Date(b.date));

      // Calculate category breakdowns
      let rentTotal = 0, depositTotal = 0, advanceTotal = 0, parkingTotal = 0, waterTotal = 0, contributionTotal = 0, paymentsTotal = 0;
      filtered.forEach(t => {
        const type = (t.type || '').toLowerCase();
        if (t.payment > 0) {
          paymentsTotal += t.payment;
        } else {
          if (type.includes('rent') || type.includes('invoice')) rentTotal += t.charge;
          else if (type.includes('deposit')) depositTotal += t.charge;
          else if (type.includes('advance')) advanceTotal += t.charge;
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
          <div class="bk-label">Security Deposit</div>
          <div class="bk-value">₱${depositTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">Fixed ₱1,000</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Parking Fee</div>
          <div class="bk-value">₱${parkingTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">${parkingTotal > 0 ? 'Fixed ₱1,000' : 'Not Applied'}</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Water Bill</div>
          <div class="bk-value">₱${waterTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
          <div class="bk-sub">${totalOccupants} × ₱100/person</div>
        </div>
        <div class="breakdown-card">
          <div class="bk-label">Contribution</div>
          <div class="bk-value">₱0.00</div>
          <div class="bk-sub">No charges</div>
        </div>
      `;

      // Render table
      const tbody = document.getElementById('soa-tbody');
      tbody.innerHTML = '';

      let runningBalance = 0;
      let totalCharges = 0;
      let totalPayments = 0;

      if (filtered.length === 0) {
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

      // Stamp logic
      const stampEl = document.getElementById('admin-soa-stamp');
      if (stampEl) {
        if (runningBalance <= 0 && filtered.length > 0) {
          stampEl.style.display = 'block';
        } else {
          stampEl.style.display = 'none';
        }
      }
    }

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
