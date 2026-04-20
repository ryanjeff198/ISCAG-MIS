<?php $active_page = 'soa'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Statement of Account</title>
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
    }
    .soa-table th {
      background: var(--primary-dark);
      color: white;
      padding: 12px;
      text-align: left;
      font-size: 0.9rem;
    }
    .soa-table td {
      padding: 12px;
      border-bottom: 1px solid var(--border);
      font-size: 0.95rem;
    }
    .soa-summary {
      width: 100%;
      max-width: 350px;
      margin-left: auto;
    }
    .soa-summary-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
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
    @media print {
      body * {
        visibility: hidden;
      }
      .soa-container, .soa-container * {
        visibility: visible;
      }
      .soa-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
        box-shadow: none;
        border: none;
        margin: 0;
        padding: 0;
      }
      .controls-panel, .top-bar, .sidebar {
        display: none !important;
      }
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
            <div class="insight-label">Statements Generated</div>
            <div class="insight-value">84</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">System Accuracy</div>
            <div class="insight-value success">100%</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Last Generated</div>
            <div class="insight-value success">Today</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Outstanding Bal.</div>
            <div class="insight-value danger">₱42,500</div>
          </div>
        </div>
        
        <!-- Controls Panel -->
        <div class="controls-panel">
          <div class="form-group">
            <label>Select Tenant</label>
            <select id="tenant-select" onchange="generateSOA()">
              <option value="">— Select a Tenant —</option>
              <option value="USR-001">Muhammad Usman</option>
              <option value="USR-002">Aisha Fatima</option>
              <option value="USR-003">Omar Khan</option>
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
            <button class="btn-action" style="padding:10px 20px; background:var(--primary); color:white; border-radius:8px;" onclick="generateSOA()">Filter Data</button>
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
            </div>
            <div class="soa-details-right" style="text-align: right;">
              <p>Statement Period:</p>
              <p><strong id="soa-period">All Time</strong></p>
            </div>
          </div>

          <table class="soa-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Reference No.</th>
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
              <span>Total Charges:</span>
              <span id="soa-total-charges">₱0.00</span>
            </div>
            <div class="soa-summary-row">
              <span>Total Payments:</span>
              <span id="soa-total-payments">₱0.00</span>
            </div>
            <div class="soa-summary-row total">
              <span>Outstanding Balance:</span>
              <span id="soa-outstanding">₱0.00</span>
            </div>
          </div>
          
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

    const mockTransactions = [
      { tenantId: 'USR-001', date: '2026-03-01', desc: 'Monthly Rent - March', ref: 'INV-2026-004', charge: 3500, payment: 0 },
      { tenantId: 'USR-001', date: '2026-03-10', desc: 'Payment Received', ref: 'PAY-00102', charge: 0, payment: 3500 },
      { tenantId: 'USR-001', date: '2026-04-01', desc: 'Monthly Rent - April', ref: 'INV-2026-001', charge: 3500, payment: 0 },
      { tenantId: 'USR-002', date: '2026-03-01', desc: 'Monthly Rent - March', ref: 'INV-2026-00A', charge: 7500, payment: 0 },
      { tenantId: 'USR-002', date: '2026-04-01', desc: 'Monthly Rent - April', ref: 'INV-2026-002', charge: 7500, payment: 0 },
      { tenantId: 'USR-003', date: '2026-03-01', desc: 'Monthly Rent - March', ref: 'INV-2026-00B', charge: 5000, payment: 0 },
      { tenantId: 'USR-003', date: '2026-03-05', desc: 'Payment Received', ref: 'PAY-00311', charge: 0, payment: 5000 },
    ];

    const tenantInfo = {
      'USR-001': { name: 'Muhammad Usman', unit: 'APT-A1', contact: '0912-345-6789' },
      'USR-002': { name: 'Aisha Fatima', unit: 'APT-B1', contact: '0918-765-4321' },
      'USR-003': { name: 'Omar Khan', unit: 'APT-A2', contact: '0922-111-2222' }
    };

    function generateSOA() {
      const tenantId = document.getElementById('tenant-select').value;
      const dateFrom = document.getElementById('date-from').value;
      const dateTo = document.getElementById('date-to').value;

      if (!tenantId) {
        document.getElementById('soa-document').style.display = 'none';
        document.getElementById('soa-empty-state').style.display = 'block';
        return;
      }

      document.getElementById('soa-empty-state').style.display = 'none';
      document.getElementById('soa-document').style.display = 'block';

      const info = tenantInfo[tenantId];
      document.getElementById('soa-tenant-name').textContent = info.name;
      document.getElementById('soa-tenant-unit').textContent = info.unit;
      document.getElementById('soa-tenant-contact').textContent = info.contact;

      const today = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      document.getElementById('soa-date-generated').textContent = today;
      
      let periodText = 'All Time';
      if(dateFrom && dateTo) periodText = `${dateFrom} to ${dateTo}`;
      else if(dateFrom) periodText = `From ${dateFrom}`;
      else if(dateTo) periodText = `Up to ${dateTo}`;
      document.getElementById('soa-period').textContent = periodText;

      let filtered = mockTransactions.filter(t => t.tenantId === tenantId);
      
      if(dateFrom) filtered = filtered.filter(t => new Date(t.date) >= new Date(dateFrom));
      if(dateTo) filtered = filtered.filter(t => new Date(t.date) <= new Date(dateTo));

      filtered.sort((a,b) => new Date(a.date) - new Date(b.date));

      const tbody = document.getElementById('soa-tbody');
      tbody.innerHTML = '';

      let runningBalance = 0;
      let totalCharges = 0;
      let totalPayments = 0;

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:20px;">No transactions found for the selected period.</td></tr>`;
      } else {
        filtered.forEach(t => {
          runningBalance += t.charge;
          runningBalance -= t.payment;
          totalCharges += t.charge;
          totalPayments += t.payment;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${t.date}</td>
            <td>${t.desc}</td>
            <td>${t.ref}</td>
            <td style="text-align:right;">${t.charge > 0 ? '₱' + t.charge.toLocaleString() : '-'}</td>
            <td style="text-align:right; color:var(--success);">${t.payment > 0 ? '₱' + t.payment.toLocaleString() : '-'}</td>
            <td style="text-align:right; font-weight:600;">₱${runningBalance.toLocaleString()}</td>
          `;
          tbody.appendChild(tr);
        });
      }

      document.getElementById('soa-total-charges').textContent = '₱' + totalCharges.toLocaleString();
      document.getElementById('soa-total-payments').textContent = '₱' + totalPayments.toLocaleString();
      document.getElementById('soa-outstanding').textContent = '₱' + runningBalance.toLocaleString();
    }
  </script>
</body>
</html>

