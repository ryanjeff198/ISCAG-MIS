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
      --bg-light: #f4f7f9;
      --card-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    body { background-color: var(--bg-light); }
    
    .report-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding: 0 4px;
    }
    .report-header h1 {
      font-size: 1.6rem;
      font-weight: 700;
      color: #1a1a1a;
      margin: 0;
    }

    .filter-row {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .filter-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .filter-group label {
      font-size: 0.85rem;
      color: #555;
      font-weight: 500;
    }
    .filter-select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 0.85rem;
      background: white;
      min-width: 140px;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
      margin-left: auto;
    }
    .btn-report {
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      border: 1px solid #ddd;
      background: white;
      color: #333;
      transition: all 0.2s;
    }
    .btn-report.primary {
      background: #0056b3;
      color: white;
      border-color: #0056b3;
    }
    .btn-report:hover { opacity: 0.9; }

    .report-grid-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    .report-card {
      background: white;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      box-shadow: var(--card-shadow);
      display: flex;
      flex-direction: column;
    }
    .report-card-header {
      padding: 12px 16px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .report-card-header h3 {
      margin: 0;
      font-size: 0.95rem;
      font-weight: 600;
      color: #333;
    }
    .report-card-body {
      padding: 0;
      overflow-x: auto;
    }

    .detailed-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.82rem;
    }
    .detailed-table th {
      text-align: left;
      padding: 10px 16px;
      background: #fafafa;
      color: #666;
      font-weight: 600;
      border-bottom: 1px solid #eee;
    }
    .detailed-table td {
      padding: 10px 16px;
      border-bottom: 1px solid #f5f5f5;
      color: #444;
    }
    .detailed-table tr:last-child td { border-bottom: none; }

    .badge-report {
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .badge-report.green { background: #e6f4ea; color: #1e7e34; }
    .badge-report.warning { background: #fff3cd; color: #856404; }
    .badge-report.danger { background: #f8d7da; color: #721c24; }

    @media (max-width: 1200px) {
      .report-grid-layout { grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="report-header">
        <h1>Detailed Departmental Reports</h1>
      </div>

      <div class="filter-row">
        <div class="filter-group">
          <label>Period:</label>
          <select class="filter-select">
            <option>Q3 2024 (Jul-Sep)</option>
            <option>Q2 2024 (Apr-Jun)</option>
            <option>Q1 2024 (Jan-Mar)</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Business Unit:</label>
          <select class="filter-select">
            <option>Municipal</option>
            <option>Apartment</option>
            <option>Da'wah</option>
          </select>
        </div>
        <div class="filter-group">
          <label>View Type:</label>
          <select class="filter-select">
            <option>Full</option>
            <option>Summary</option>
          </select>
        </div>
        
        <div class="action-buttons">
          <button class="btn-report primary">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            Run Report
          </button>
          <button class="btn-report">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Export Summary (PDF)
          </button>
          <button class="btn-report" onclick="location.reload()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
            Refresh
          </button>
        </div>
      </div>

      <div class="report-grid-layout">
        <!-- Burial Service Detailed Log -->
        <div class="report-card">
          <div class="report-card-header">
            <h3>Burial Service Detailed Log</h3>
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#999;"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
          </div>
          <div class="report-card-body">
            <table class="detailed-table">
              <thead>
                <tr>
                  <th>Timestamp ↑</th>
                  <th>Service ID</th>
                  <th>Service Type</th>
                  <th>Status</th>
                  <th>Plot Num.</th>
                  <th>Cost</th>
                </tr>
              </thead>
              <tbody>
                <?php if(empty($burialLogs)): ?>
                  <tr><td colspan="6" style="text-align:center; padding:20px; color:#999;">No burial records found.</td></tr>
                <?php else: ?>
                  <?php foreach($burialLogs as $log): ?>
                  <tr>
                    <td><?= date('Y-m-d H:i', strtotime($log['due_date'])) ?></td>
                    <td><?= $log['billing_id'] ?></td>
                    <td>Burial Service</td>
                    <td><span class="badge-report green"><?= $log['status'] ?></span></td>
                    <td><?= rand(1, 50) ?></td>
                    <td>₱<?= number_format($log['amount']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Female Section Education Enrollment Records -->
        <div class="report-card">
          <div class="report-card-header">
            <h3>Female Section Education Enrollment Records</h3>
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#999;"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
          </div>
          <div class="report-card-body">
            <table class="detailed-table">
              <thead>
                <tr>
                  <th>Enrollment</th>
                  <th>Student ID</th>
                  <th>Program</th>
                  <th>Grade</th>
                  <th>Enrollment Date</th>
                  <th>Status</th>
                  <th>GPA</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>U4514</td>
                  <td>Arabic</td>
                  <td>A</td>
                  <td>03/12/2024</td>
                  <td><span class="badge-report green">Active</span></td>
                  <td>3.85</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>U4518</td>
                  <td>Qur'an</td>
                  <td>B</td>
                  <td>06/01/2024</td>
                  <td><span class="badge-report warning">Pending</span></td>
                  <td>3.20</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>U4520</td>
                  <td>Fiqh</td>
                  <td>B</td>
                  <td>06/01/2024</td>
                  <td><span class="badge-report warning">Pending</span></td>
                  <td>2.75</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="report-grid-layout">
        <!-- Male Section Education Detailed Attendance -->
        <div class="report-card" style="grid-column: span 1;">
          <div class="report-card-header">
            <h3>Male Section Education Detailed Attendance</h3>
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#999;"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
          </div>
          <div class="report-card-body">
            <table class="detailed-table">
              <thead>
                <tr>
                  <th>Date ↑</th>
                  <th>Student ID</th>
                  <th>Program</th>
                  <th>Class</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-05-18</td>
                  <td>M8812</td>
                  <td>Advanced</td>
                  <td>Class A</td>
                  <td>Present</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Female section Education Detailed Attendance -->
        <div class="report-card" style="grid-column: span 1;">
          <div class="report-card-header">
            <h3>Female Section Education Detailed Attendance</h3>
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#999;"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
          </div>
          <div class="report-card-body">
            <table class="detailed-table">
              <thead>
                <tr>
                  <th>Date ↑</th>
                  <th>Student ID</th>
                  <th>Program</th>
                  <th>Class</th>
                  <th>Status</th>
                  <th>Attendance %</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>2024-05-18</td>
                  <td>F4512</td>
                  <td>Primary</td>
                  <td>Class A</td>
                  <td>Present</td>
                  <td>94.5%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Apartment Lease & Maintenance Log -->
      <div class="report-card">
        <div class="report-card-header">
          <h3>Apartment Lease & Maintenance Log</h3>
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#999;"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
        </div>
        <div class="report-card-body">
          <table class="detailed-table">
            <thead>
              <tr>
                <th>Unit ID ↑</th>
                <th>Lease ID</th>
                <th>Application Date</th>
                <th>Tenant</th>
                <th>Status</th>
                <th>Maintenance Request Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($aptLeaseLogs as $log): ?>
              <tr>
                <td><?= $log['building'] ?>-<?= $log['room_number'] ?: 'N/A' ?></td>
                <td>APP-<?= $log['application_id'] ?></td>
                <td><?= date('m/d/Y', strtotime($log['date'])) ?></td>
                <td><?= $log['first_name'] ?> <?= $log['last_name'] ?></td>
                <td><span class="badge-report green"><?= $log['status'] ?></span></td>
                <td><span class="badge-report <?= $log['status'] === 'Pending' ? 'warning' : 'green' ?>"><?= $log['status'] === 'Pending' ? 'Maintenance' : 'Optimal' ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
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

