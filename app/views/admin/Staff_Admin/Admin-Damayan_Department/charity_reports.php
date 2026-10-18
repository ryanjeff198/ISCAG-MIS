<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Charity Reports & Audit Trail</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary: #176b45;
      --primary-dark: #0f5c3a;
      --accent: #c79a2b;
      --border: #d9e3de;
      --content-bg: #f4f6f5;
    }

    .report-filter-bar {
      background: white;
      padding: 18px 24px;
      border-radius: 14px;
      border: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .filter-select {
      padding: 8px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .report-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 14px;
      margin-bottom: 24px;
      overflow: hidden;
    }
    .report-card-header {
      padding: 16px 20px;
      background: #fafbfb;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .report-card-header h6 {
      font-family: 'Lora', serif;
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }

    .dept-tag {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
    }
    .dept-damayan { background: #e8f5ed; color: #0f5c3a; }
    .dept-dawah { background: #fdf5e6; color: #a37817; }

    @media print {
      body { background: white; padding: 0; }
      .app-wrapper { display: block; }
      .sidebar, .top-bar, .breadcrumb-bar, .report-filter-bar, .no-print { display: none !important; }
      .main-content { margin: 0; padding: 0; }
      .report-card { border: 1px solid #ccc; box-shadow: none; margin-bottom: 20px; page-break-inside: avoid; }
    }
  </style>
  <link rel="stylesheet" href="<?= asset('css/charity.css') ?>?v=<?= time() ?>" />
</head>
<body class="charity-module">
  <div class="app-wrapper">
    <!-- Damayan Sidebar -->
    <?php 
      $active_page = 'charity';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Charity Reports & Audit History</div>
          <div class="top-bar-subtitle">Consolidated reports of donations received, department allocations, and aid distributions</div>
        </div>
        <div class="top-bar-actions">
          <button onclick="window.print()" class="btn-topbar" style="background:var(--primary); color:white; border:none; border-radius:8px; padding:9px 18px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Report
          </button>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= url('/admin/damayan/charity') ?>">Charity Programs</a>
          <span class="sep">›</span>
          <span class="current">Reports Summary</span>
        </div>

        <!-- Date Range Filter -->
        <form method="GET" action="<?= url('/admin/damayan/charity/reports') ?>" class="report-filter-bar no-print">
          <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <span style="font-weight:700; font-size:0.85rem; color:var(--text-main);">Reporting Period:</span>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>" class="filter-select" />
            <span style="color:var(--text-muted); font-weight:700;">to</span>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>" class="filter-select" />
            <button type="submit" style="background:var(--primary); color:white; border:none; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
              Update Report
            </button>
          </div>
          <div style="font-size:0.82rem; color:var(--text-muted);">
            Report generated for: <strong><?= date('M d, Y', strtotime($filters['date_from'])) ?> — <?= date('M d, Y', strtotime($filters['date_to'])) ?></strong>
          </div>
        </form>

        <!-- Printable Report Header -->
        <div style="text-align:center; margin-bottom:28px;">
          <h2 style="font-family:'Lora', serif; color:var(--primary-dark); margin-bottom:4px;">ISCAG CHARITY & SOCIAL WELFARE ACTIVITY REPORT</h2>
          <div style="font-size:0.85rem; color:var(--text-muted);">Islamic Studies, Call and Guidance Management Information System</div>
          <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">Period: <?= date('F d, Y', strtotime($filters['date_from'])) ?> to <?= date('F d, Y', strtotime($filters['date_to'])) ?></div>
        </div>

        <!-- Financial & In-Kind Summary Table -->
        <div class="report-card">
          <div class="report-card-header">
            <h6>1. Executive Summary & Balances</h6>
          </div>
          <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
              <div style="background:#f9fafb; padding:16px; border-radius:10px; border:1px solid var(--border);">
                <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Donations Received</div>
                <div style="font-size:1.4rem; font-weight:800; color:var(--primary); margin-top:4px;">₱<?= number_format($stats['received']['monetary'], 2) ?></div>
                <div style="font-size:0.8rem; color:var(--text-muted);"><?= $stats['received']['in_kind'] ?> physical in-kind units</div>
              </div>
              <div style="background:#f9fafb; padding:16px; border-radius:10px; border:1px solid var(--border);">
                <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Allocated to Depts</div>
                <div style="font-size:1.4rem; font-weight:800; color:var(--accent); margin-top:4px;">₱<?= number_format($stats['allocated']['monetary'], 2) ?></div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Damayan: ₱<?= number_format($stats['allocated']['damayan_monetary']) ?> | Da'wah: ₱<?= number_format($stats['allocated']['dawah_monetary']) ?></div>
              </div>
              <div style="background:#f9fafb; padding:16px; border-radius:10px; border:1px solid var(--border);">
                <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Confirmed Distributed</div>
                <div style="font-size:1.4rem; font-weight:800; color:#1f6f5a; margin-top:4px;">₱<?= number_format($stats['distributed']['monetary'], 2) ?></div>
                <div style="font-size:0.8rem; color:var(--text-muted);"><?= $stats['distributed']['in_kind'] ?> units handed out to beneficiaries</div>
              </div>
              <div style="background:#f9fafb; padding:16px; border-radius:10px; border:1px solid var(--border);">
                <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Current Available Inventory</div>
                <div style="font-size:1.4rem; font-weight:800; color:var(--success); margin-top:4px;">₱<?= number_format($stats['available']['monetary'], 2) ?></div>
                <div style="font-size:0.8rem; color:var(--text-muted);"><?= $stats['available']['in_kind'] ?> unallocated physical units</div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Donations Inflow -->
        <div class="report-card">
          <div class="report-card-header">
            <h6>2. Donations Inflow (<?= count($donations) ?> records in period)</h6>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Date</th>
                  <th>Donor / Sponsor</th>
                  <th>Type</th>
                  <th>Donated Value</th>
                  <th>Allocated</th>
                  <th>Available</th>
                  <th>Method</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($donations as $d): ?>
                  <tr>
                    <td><strong style="font-family:monospace;"><?= htmlspecialchars($d['donation_ref']) ?></strong></td>
                    <td><?= date('M d, Y', strtotime($d['date_received'])) ?></td>
                    <td><strong><?= htmlspecialchars($d['donor_name']) ?></strong></td>
                    <td><?= ucfirst($d['donation_type']) ?></td>
                    <td>
                      <?php if ($d['donation_type'] === 'monetary'): ?>
                        <strong>₱<?= number_format($d['amount'], 2) ?></strong>
                      <?php else: ?>
                        <strong><?= $d['quantity'] ?>x</strong> <?= htmlspecialchars($d['item_description']) ?>
                      <?php endif; ?>
                    </td>
                    <td><?= $d['donation_type'] === 'monetary' ? '₱' . number_format($d['allocated_amount'], 2) : $d['allocated_quantity'] . ' Units' ?></td>
                    <td><strong style="color:var(--success);"><?= $d['donation_type'] === 'monetary' ? '₱' . number_format($d['available_amount'], 2) : $d['available_quantity'] . ' Units' ?></strong></td>
                    <td><?= htmlspecialchars($d['payment_method'] ?: 'N/A') ?></td>
                    <td><?= htmlspecialchars($d['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 3. Department Allocations -->
        <div class="report-card">
          <div class="report-card-header">
            <h6>3. Allocations to Damayan & Da'wah (<?= count($allocations) ?> records in period)</h6>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Alloc Ref</th>
                  <th>Date</th>
                  <th>Dept</th>
                  <th>Allocated Value</th>
                  <th>Distributed</th>
                  <th>Remaining</th>
                  <th>Purpose</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($allocations as $a): ?>
                  <tr>
                    <td><strong style="font-family:monospace;"><?= htmlspecialchars($a['allocation_ref']) ?></strong></td>
                    <td><?= date('M d, Y', strtotime($a['allocation_date'])) ?></td>
                    <td><span class="dept-tag <?= $a['receiving_department'] === 'Damayan' ? 'dept-damayan' : 'dept-dawah' ?>"><?= htmlspecialchars($a['receiving_department']) ?></span></td>
                    <td><?= $a['allocation_type'] === 'monetary' ? '₱' . number_format($a['allocated_amount'], 2) : $a['allocated_quantity'] . ' Units' ?></td>
                    <td><?= $a['allocation_type'] === 'monetary' ? '₱' . number_format($a['distributed_amount'], 2) : $a['distributed_quantity'] . ' Units' ?></td>
                    <td><strong><?= $a['allocation_type'] === 'monetary' ? '₱' . number_format($a['remaining_allocated_amount'], 2) : $a['remaining_allocated_quantity'] . ' Units' ?></strong></td>
                    <td><?= htmlspecialchars($a['purpose']) ?></td>
                    <td><?= htmlspecialchars($a['status']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 4. Aid Distributions -->
        <div class="report-card">
          <div class="report-card-header">
            <h6>4. Aid Distributions to Beneficiaries (<?= count($distributions) ?> records in period)</h6>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Dist Ref</th>
                  <th>Date</th>
                  <th>Dept</th>
                  <th>Distributed Resources</th>
                  <th>Authorized Recipient</th>
                  <th>Purpose</th>
                  <th>Staff</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($distributions as $dist): ?>
                  <tr>
                    <td><strong style="font-family:monospace;"><?= htmlspecialchars($dist['distribution_ref']) ?></strong></td>
                    <td><?= date('M d, Y', strtotime($dist['distribution_date'])) ?></td>
                    <td><span class="dept-tag <?= $dist['receiving_department'] === 'Damayan' ? 'dept-damayan' : 'dept-dawah' ?>"><?= htmlspecialchars($dist['receiving_department']) ?></span></td>
                    <td><strong><?= htmlspecialchars($dist['items_summary']) ?></strong></td>
                    <td><strong><?= htmlspecialchars($dist['received_by']) ?></strong></td>
                    <td><?= htmlspecialchars($dist['purpose']) ?></td>
                    <td><?= htmlspecialchars($dist['distributed_by']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
