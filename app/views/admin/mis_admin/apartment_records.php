<?php $active_page = 'apartment_records'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Records</title>
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
            <div class="top-bar-title">Apartment Records</div>
            <div class="top-bar-subtitle">Manage apartment units, applications, tenants, and billing</div>
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
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a><span class="sep">›</span><span class="current">Apartment
            Records</span>
        </div>

        <!-- STATS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon gold"><svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-units">0</div>
              <div class="stat-label">Total Units</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-avail">0</div>
              <div class="stat-label">Available Slots</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red"><svg viewBox="0 0 24 24">
                <path
                  d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z" />
              </svg></div>
            <div>
              <div class="stat-value" id="s-occ">0</div>
              <div class="stat-label">Occupied</div>
            </div>
          </div>
        </div>

        <!-- UNIT TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg>Unit Inventory</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Unit ID</th>
                    <th>Name</th>
                    <th>Price/mo</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="unit-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- BILLING -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M6 2h7.5C16.54 2 19 4.46 19 7.5S16.54 13 13.5 13H8.5v8H6V2zm2.5 9H13.5c1.93 0 3.5-1.57 3.5-3.5S15.43 4 13.5 4H8.5v7z"/><path d="M3 6h15v2H3z"/><path d="M3 9.5h14v2H3z"/>
              </svg>Apartment Billing</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Bill ID</th>
                    <th>Tenant</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="bill-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');
    const apts = getApartments();
    const bills = getBilling().filter(b => b.type.toLowerCase().includes('apartment'));

    document.getElementById('s-units').textContent = apts.length;
    document.getElementById('s-avail').textContent = apts.reduce((s, a) => s + a.available, 0);
    document.getElementById('s-occ').textContent = apts.filter(a => a.status === 'occupied').length;

    // Units
    document.getElementById('unit-tbody').innerHTML = apts.map(a => {
      const bc = a.status === 'available' ? 'badge-approved' : a.status === 'occupied' ? 'badge-rejected' : 'badge-pending';
      return `<tr>
      <td class="td-id">${a.id}</td><td style="font-weight:600;">${a.name}</td>
      <td>₱${a.price.toLocaleString()}</td>
      <td style="text-align:center;font-weight:700;color:${a.available > 0 ? 'var(--success)' : 'var(--danger)'};">${a.available}</td>
      <td><span class="badge-status ${bc}">${statusLabel(a.status)}</span></td>
      <td>
        <div class="action-menu">
          <button class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
            <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
          </button>
          <div class="action-menu-dropdown">
            <button class="action-menu-item" onclick="showToast('Viewing ${a.id}','var(--info)')">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/></svg>
              View Unit Details
            </button>
          </div>
        </div>
      </td>
    </tr>`;
    }).join('');

    // Billing
    const billTb = document.getElementById('bill-tbody');
    if (!bills.length) {
      billTb.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text-muted);">No billing records.</td></tr>';
    } else {
      billTb.innerHTML = bills.map(b => `<tr>
      <td class="td-id">${b.id}</td><td style="font-weight:600;">${b.name}</td>
      <td style="font-weight:700;">${currencyFormat(b.amount)}</td>
      <td style="color:var(--text-muted);">${formatDate(b.dueDate)}</td>
      <td><span class="badge-status ${badgeClass(b.status)}">${statusLabel(b.status)}</span></td>
    </tr>`).join('');
    }
  </script>
</body>

</html>

