<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Detailed Departmental Reports</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    :root {
      --card-radius: 12px;
      --card-padding: 24px;
    }

    body {
      background: var(--content-bg);
      color: var(--text-main);
    }

    /* Page Body Padding */
    .page-body {
      padding: 0 4px;
    }

    /* Modern Filter Bar */
    .report-filters {
      background: white;
      padding: 20px 24px;
      border-radius: var(--card-radius);
      border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      margin-bottom: 28px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 20px;
    }

    .filter-inputs {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .filter-item {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .filter-item label {
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .select-premium {
      padding: 9px 14px;
      border: 1.5px solid #eef2f1;
      border-radius: 8px;
      font-size: 0.88rem;
      background: #fbfcfb;
      color: var(--text-main);
      min-width: 180px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .select-premium:focus {
      outline: none;
      border-color: var(--primary-light);
      background: white;
      box-shadow: 0 0 0 3px rgba(47, 138, 96, 0.1);
    }

    .report-actions {
      display: flex;
      gap: 10px;
    }

    /* Premium Buttons */
    .btn-action {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.88rem;
      cursor: pointer;
      transition: all 0.2s;
      border: 1px solid var(--border);
      background: white;
      color: var(--text-main);
    }

    .btn-action.primary {
      background: var(--primary);
      color: white;
      border: none;
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2);
    }

    .btn-action.primary:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(23, 107, 69, 0.3);
    }

    .btn-action:hover:not(.primary) {
      background: #f8f9f9;
      border-color: #ccd7d2;
    }

    /* Report Grid */
    .report-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-bottom: 32px;
    }

    .full-width {
      grid-column: span 2;
    }

    /* Section Cards */
    .section-card {
      background: white;
      border-radius: var(--card-radius);
      border: 1px solid var(--border);
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
      transition: box-shadow 0.3s ease;
    }

    .section-card:hover {
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }

    .section-card-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fafbfb;
    }

    .section-card-header h3 {
      font-family: 'Lora', serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }

    /* Custom Table Styling */
    .report-table {
      width: 100%;
      border-collapse: collapse;
    }

    .report-table th {
      text-align: left;
      padding: 14px 20px;
      background: #f1f4f3;
      color: var(--text-muted);
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 1px solid var(--border);
    }

    .report-table td {
      padding: 14px 20px;
      font-size: 0.88rem;
      border-bottom: 1px solid #f6f8f7;
      color: var(--text-main);
    }

    .report-table tr:last-child td {
      border-bottom: none;
    }

    .report-table tr:hover td {
      background: #f9fbfb;
    }

    /* Badges */
    .badge-status {
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .badge-paid { background: #e8f5e9; color: #2e7d32; }
    .badge-pending { background: #fff8e1; color: #f57f17; }
    .badge-overdue { background: #ffebee; color: #c62828; }

    @media (max-width: 1100px) {
      .report-grid { grid-template-columns: 1fr; }
      .full-width { grid-column: span 1; }
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
            <div class="top-bar-title">Departmental Reports</div>
            <div class="top-bar-subtitle">In-depth performance logs and analytics across all ISCAG departments</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Premium Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Active Enrollments</div>
            <div class="insight-value success">1,248</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Monthly Revenue</div>
            <div class="insight-value info">₱425,000</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Maintenance Rate</div>
            <div class="insight-value warning">94%</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Attendance Avg</div>
            <div class="insight-value">98.2%</div>
          </div>
        </div>

        <!-- Filter Bar -->
        <div class="report-filters">
          <div class="filter-inputs">
            <div class="filter-item">
              <label>Reporting Period</label>
              <select class="select-premium">
                <option>Q3 2024 (Jul-Sep)</option>
                <option>Q2 2024 (Apr-Jun)</option>
                <option>Q1 2024 (Jan-Mar)</option>
              </select>
            </div>
            <div class="filter-item">
              <label>Business Unit</label>
              <select class="select-premium">
                <option>All Departments</option>
                <option>Apartment</option>
                <option>Da'wah (Education)</option>
                <option>Damayan (Burial)</option>
              </select>
            </div>
          </div>
          
          <div class="report-actions">
            <button class="btn-action primary">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
              Generate Report
            </button>
            <button class="btn-action">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
              Export PDF
            </button>
          </div>
        </div>

        <div class="report-grid">
          <!-- Burial Service Detailed Log -->
          <div class="section-card">
            <div class="section-card-header">
              <h3>Damayan (Burial Service)</h3>
              <span style="font-size:0.75rem; color:var(--text-muted);">Last 30 Days</span>
            </div>
            <div class="report-card-body">
              <table class="report-table">
                <thead>
                  <tr>
                    <th>Timestamp</th>
                    <th>Service ID</th>
                    <th>Status</th>
                    <th>Plot</th>
                    <th>Cost</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($burialLogs)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted);">No burial records found for this period.</td></tr>
                  <?php else: ?>
                    <?php foreach($burialLogs as $log): ?>
                    <tr>
                      <td style="color:var(--text-muted); font-size:0.75rem;"><?= date('M d, Y H:i', strtotime($log['due_date'])) ?></td>
                      <td style="font-weight:700; color:var(--primary-dark);"><?= $log['billing_id'] ?></td>
                      <td><span class="badge-status badge-paid"><?= $log['status'] ?></span></td>
                      <td>P-<?= rand(1, 99) ?></td>
                      <td style="font-weight:600;">₱<?= number_format($log['amount']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- D'awah Male Education Log -->
          <div class="section-card">
            <div class="section-card-header">
              <h3>D'awah Male</h3>
              <span style="font-size:0.75rem; color:var(--text-muted);">Enrollment & Attendance</span>
            </div>
            <div class="report-card-body">
              <table class="report-table">
                <thead>
                  <tr>
                    <th>Student ID</th>
                    <th>Program</th>
                    <th>Attendance</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="font-weight:700;">M8812</td>
                    <td>Advanced Arabic</td>
                    <td>98.5%</td>
                    <td><span class="badge-status badge-paid">Present</span></td>
                  </tr>
                  <tr>
                    <td style="font-weight:700;">M8815</td>
                    <td>Qur'an (Hifz)</td>
                    <td>92.0%</td>
                    <td><span class="badge-status badge-paid">Present</span></td>
                  </tr>
                  <tr>
                    <td style="font-weight:700;">M8820</td>
                    <td>Islamic Fiqh</td>
                    <td>100%</td>
                    <td><span class="badge-status badge-paid">Present</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Female Section Education Enrollment -->
          <div class="section-card">
            <div class="section-card-header">
              <h3>D'awah Female</h3>
              <span style="font-size:0.75rem; color:var(--text-muted);">Enrollment Records</span>
            </div>
            <div class="report-card-body">
              <table class="report-table">
                <thead>
                  <tr>
                    <th>Student ID</th>
                    <th>Program</th>
                    <th>Attendance</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="font-weight:700;">F4512</td>
                    <td>Arabic Primary</td>
                    <td>94.5%</td>
                    <td><span class="badge-status badge-paid">Active</span></td>
                  </tr>
                  <tr>
                    <td style="font-weight:700;">F4518</td>
                    <td>Qur'an (Tajweed)</td>
                    <td>88.0%</td>
                    <td><span class="badge-status badge-pending">Pending</span></td>
                  </tr>
                  <tr>
                    <td style="font-weight:700;">F4520</td>
                    <td>Hadith Studies</td>
                    <td>95.2%</td>
                    <td><span class="badge-status badge-pending">Pending</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Apartment Lease & Maintenance Log -->
          <div class="section-card full-width">
            <div class="section-card-header">
              <h3>Apartment Lease & Maintenance Log</h3>
              <div class="report-actions">
                <span class="badge-status badge-paid">Optimal Status: 98%</span>
              </div>
            </div>
            <div class="report-card-body">
              <table class="report-table">
                <thead>
                  <tr>
                    <th>Unit ID</th>
                    <th>Application Date</th>
                    <th>Tenant</th>
                    <th>Payment Status</th>
                    <th>Maintenance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($aptLeaseLogs as $log): ?>
                  <tr>
                    <td style="font-weight:700;"><?= $log['building'] ?>-<?= $log['room_number'] ?: 'N/A' ?></td>
                    <td><?= date('M d, Y', strtotime($log['date'])) ?></td>
                    <td style="font-weight:600;"><?= $log['first_name'] ?> <?= $log['last_name'] ?></td>
                    <td><span class="badge-status badge-paid"><?= $log['status'] ?></span></td>
                    <td>
                      <div style="display:flex; align-items:center; gap:8px;">
                        <div style="width:100px; height:6px; background:#eee; border-radius:3px; overflow:hidden;">
                          <div style="width:<?= rand(80, 100) ?>%; height:100%; background:var(--primary-light);"></div>
                        </div>
                        <span style="font-size:0.7rem; color:var(--text-muted); font-weight:700;"><?= $log['status'] === 'Pending' ? 'Warning' : 'Good' ?></span>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');
  </script>
</body>
</html>

