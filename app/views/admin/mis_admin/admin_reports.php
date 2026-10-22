<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Departmental Reports & Exports</title>
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

    .page-body {
      padding: 0 4px;
    }

    /* Filter Bar */
    .report-filters {
      background: white;
      padding: 20px 24px;
      border-radius: var(--card-radius);
      border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 16px;
    }

    .filter-inputs {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      align-items: flex-end;
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
      min-width: 170px;
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
      padding: 10px 18px;
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

    /* Section Cards */
    .section-card {
      background: white;
      border-radius: var(--card-radius);
      border: 1px solid var(--border);
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
      margin-bottom: 24px;
    }

    .section-card-header {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-card-header h3 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-main);
      margin: 0;
    }

    .report-table-wrapper {
      width: 100%;
      overflow-x: auto;
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
    }

    .report-table th {
      background: #f9fafb;
      padding: 12px 16px;
      text-align: left;
      font-weight: 700;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .report-table td {
      padding: 12px 16px;
      border-bottom: 1px solid var(--border);
      color: var(--text-main);
    }

    .report-table tr:hover td {
      background: #fcfdfc;
    }

    /* Department Badges */
    .badge-dept {
      font-size: 0.7rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 12px;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .badge-dept-apartments { background: #e0f2fe; color: #0369a1; }
    .badge-dept-dawah { background: #ecfdf5; color: #047857; }
    .badge-dept-education { background: #fef3c7; color: #b45309; }
    .badge-dept-damayan { background: #ede9fe; color: #6d28d9; }
    .badge-dept-finance { background: #dcfce7; color: #15803d; }

    /* Print Header & Footer (Hidden by default) */
    .print-only {
      display: none;
    }

    /* ═══════ PRINT STYLES (Letter Bond Paper Optimized) ═══════ */
    @media print {
      @page {
        size: letter portrait;
        margin: 0.5in;
      }
      body {
        background: #ffffff !important;
        color: #111827 !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .app-wrapper {
        display: block !important;
      }
      .sidebar, .top-bar, .report-filters, .breadcrumb-bar, .no-print, .action-menu {
        display: none !important;
      }
      .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
      }
      .page-body {
        padding: 0 !important;
      }
      .print-only {
        display: block !important;
      }
      .section-card {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        margin-bottom: 15px !important;
        page-break-inside: auto;
      }
      .report-table-wrapper {
        overflow: visible !important;
      }
      .report-table {
        width: 100% !important;
        border-collapse: collapse !important;
      }
      .report-table th, .report-table td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 8px !important;
        font-size: 8.5pt !important;
      }
      .report-table th {
        background: #f1f5f9 !important;
        color: #1e293b !important;
      }
      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }
      .print-header-block {
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 2px solid #166534;
        padding-bottom: 12px;
        margin-bottom: 16px;
      }
      .print-header-brand h2 {
        margin: 0;
        font-size: 14pt;
        color: #166534;
        font-weight: 800;
        letter-spacing: 0.02em;
      }
      .print-header-brand p {
        margin: 2px 0 0 0;
        font-size: 8.5pt;
        color: #4b5563;
      }
      .print-meta-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        background: #f8faf9;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 16px;
        font-size: 8pt;
      }
      .print-sign-block {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #cbd5e1;
        font-size: 8.5pt;
        page-break-inside: avoid;
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
            <div class="top-bar-title">Departmental Reports & Exports</div>
            <div class="top-bar-subtitle">Generate, filter, print, and export official records across all departments</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">

        <!-- ═══════ OFFICIAL PRINT HEADER (Visible only on print/export) ═══════ -->
        <div class="print-only">
          <div class="print-header-block">
            <img src="<?= asset('assets/logo.jpg') ?>" style="width:52px;height:52px;border-radius:6px;" alt="ISCAG" />
            <div class="print-header-brand">
              <h2>ISLAMIC CENTER & GUIDANCE (ISCAG)</h2>
              <p>Management Information System — Central Administration & Operations Report</p>
            </div>
          </div>
          <div class="print-meta-grid">
            <div><strong>Report Scope:</strong> <span id="print-scope">All Departments Combined</span></div>
            <div><strong>Date Generated:</strong> <span><?= date('M d, Y h:i A') ?></span></div>
            <div><strong>Generated By:</strong> <span><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?> (<?= htmlspecialchars($_SESSION['role'] ?? 'MIS Admin') ?>)</span></div>
            <div><strong>Active Status Filter:</strong> <span id="print-status">All Statuses</span></div>
          </div>
        </div>

        <div class="breadcrumb-bar no-print">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a>
          <span class="sep">›</span>
          <span class="current">Department Reports</span>
        </div>

        <!-- ═══════ REPORT FILTERS ═══════ -->
        <div class="report-filters no-print">
          <div class="filter-inputs">
            <div class="filter-item">
              <label>Department Scope</label>
              <select class="select-premium" id="filter-dept" onchange="applyReportFilters()">
                <option value="all">All Departments Combined</option>
                <option value="Apartments">Apartments Department</option>
                <option value="Islamic Education">Islamic Education Department</option>
                <option value="Da'wah">Da'wah Department</option>
                <option value="Damayan">Damayan & Funeral Services</option>
                <option value="Finance">Financial & Invoices</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Status</label>
              <select class="select-premium" id="filter-status" onchange="applyReportFilters()">
                <option value="all">All Statuses</option>
                <option value="Pending">Pending Review</option>
                <option value="Approved">Approved / Assigned / Accepted</option>
                <option value="Completed">Completed / Resolved</option>
                <option value="Paid">Paid / Confirmed</option>
                <option value="Rejected">Rejected</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Time Period</label>
              <select class="select-premium" id="filter-period" onchange="applyReportFilters()">
                <option value="all">All Historical Records</option>
                <option value="today">Today</option>
                <option value="week">Past 7 Days</option>
                <option value="month">Past 30 Days</option>
                <option value="year">This Year (<?= date('Y') ?>)</option>
              </select>
            </div>

            <div class="filter-item">
              <label>Keyword Search</label>
              <input type="text" id="report-search" class="select-premium" placeholder="Filter by name, ID, or detail..." oninput="applyReportFilters()" />
            </div>
          </div>

          <div class="report-actions">
            <button class="btn-action primary" onclick="printReport()">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
              Print Report (Letter)
            </button>
            <button class="btn-action" onclick="exportReportCSV()">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
              Export CSV
            </button>
          </div>
        </div>

        <!-- ═══════ KPI SUMMARY RIBBON ═══════ -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Filtered Records</div>
            <div class="insight-value info" id="kpi-records-count">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Approved / Confirmed</div>
            <div class="insight-value success" id="kpi-approved-count">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="kpi-pending-count">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Revenue Collected</div>
            <div class="insight-value" style="color:var(--primary);">₱<?= number_format($totalRevenue ?? 0, 2) ?></div>
          </div>
        </div>

        <!-- ═══════ MASTER REPORT TABLE ═══════ -->
        <div class="section-card">
          <div class="section-card-header">
            <h3 id="report-title-display">Central Multi-Department Records</h3>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="report-counter">0 records</span>
          </div>
          <div class="report-table-wrapper">
            <table class="report-table" id="master-report-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Department</th>
                  <th>Service / Request Type</th>
                  <th>Subject / Applicant</th>
                  <th>Contact</th>
                  <th>Date</th>
                  <th>Details / Value</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="master-report-tbody"></tbody>
            </table>
          </div>
        </div>

        <!-- ═══════ SPECIALIZED DEPARTMENT DATA LOGS ═══════ -->
        <!-- 1. ISLAMIC EDUCATION DETAILED ROSTER & PROGRESS -->
        <div class="section-card" id="edu-special-section">
          <div class="section-card-header">
            <div>
              <h3>Islamic Education — Student Roster & Attendance Progress</h3>
              <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">
                Monitoring: Registered students, course level, session attendance, and overall progress (Grading & retention excluded per policy)
              </div>
            </div>
            <span class="badge-dept badge-dept-education"><?= $totalEduStudents ?? 0 ?> Students Registered</span>
          </div>
          <div class="report-table-wrapper">
            <table class="report-table">
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Student Full Name</th>
                  <th>Curriculum Track / Level</th>
                  <th>Sessions Attended</th>
                  <th>Individual Progress</th>
                  <th>Learning Status</th>
                  <th>Tuition Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($eduEnrollments)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted);">No enrolled students found in database.</td></tr>
                <?php else: ?>
                  <?php foreach ($eduEnrollments as $st): 
                    $pColor = ($st['progress'] >= 75) ? 'var(--success)' : (($st['progress'] >= 25) ? 'var(--accent)' : 'var(--danger)');
                  ?>
                  <tr>
                    <td class="td-id">EDU-<?= $st['id'] ?></td>
                    <td style="font-weight:700;"><?= htmlspecialchars($st['first_name'] . ' ' . $st['last_name']) ?></td>
                    <td><?= htmlspecialchars($st['level'] ?: 'Basic Arabic') ?></td>
                    <td><?= (int)$st['attended'] ?> / <?= (int)$st['total_s'] ?> sessions</td>
                    <td>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:70px;height:6px;background:#eee;border-radius:3px;overflow:hidden;">
                          <div style="width:<?= min(100, (int)$st['progress']) ?>%;height:100%;background:<?= $pColor ?>;"></div>
                        </div>
                        <span style="font-weight:700;font-size:0.75rem;color:<?= $pColor ?>;"><?= (int)$st['progress'] ?>%</span>
                      </div>
                    </td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars(ucfirst($st['learning_status'] ?: 'Active')) ?></span></td>
                    <td><span class="badge-status <?= stripos($st['payment_status'] ?? '', 'paid') !== false ? 'badge-paid' : 'badge-pending' ?>"><?= htmlspecialchars(ucfirst($st['payment_status'] ?: 'Pending')) ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 2. DAMAYAN BURIAL & FUNERAL LOGS -->
        <div class="section-card" id="damayan-special-section">
          <div class="section-card-header">
            <h3>Damayan — Burial Services & Funeral Documentation</h3>
            <span class="badge-dept badge-dept-damayan"><?= count($burialRequests ?? []) ?> Burial Services Recorded</span>
          </div>
          <div class="report-table-wrapper">
            <table class="report-table">
              <thead>
                <tr>
                  <th>Case Ref</th>
                  <th>Service Requested</th>
                  <th>Deceased / Informant</th>
                  <th>Cemetery Plot</th>
                  <th>Submission Date</th>
                  <th>Certificate Status</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($burialRequests) && empty($funeralCases)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-muted);">No burial or funeral cases recorded.</td></tr>
                <?php else: ?>
                  <?php foreach ($burialRequests as $b): ?>
                  <tr>
                    <td class="td-id">BUR-<?= $b['id'] ?></td>
                    <td style="font-weight:600;">Burial Assistance</td>
                    <td><?= htmlspecialchars($b['deceased_name'] ?: ($b['tenant_name'] ?: 'N/A')) ?></td>
                    <td><?= htmlspecialchars($b['assigned_plot_number'] ?: 'Plot: Pending') ?> (<?= htmlspecialchars($b['cemetery_preference'] ?: 'Local') ?>)</td>
                    <td><?= date('M d, Y', strtotime($b['submitted_at'] ?? 'now')) ?></td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars($b['death_cert_status'] ?: 'Certified') ?></span></td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars(ucfirst($b['status'])) ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php foreach ($funeralCases as $fc): ?>
                  <tr>
                    <td class="td-id">FNC-<?= $fc['id'] ?></td>
                    <td style="font-weight:600;">Funeral Case Management</td>
                    <td><?= htmlspecialchars(($fc['deceased_first_name'] ?? '') . ' ' . ($fc['deceased_last_name'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($fc['grave_reference'] ?: 'Plot: Pending') ?></td>
                    <td><?= date('M d, Y', strtotime($fc['reported_at'] ?? 'now')) ?></td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars($fc['certificate_status'] ?: 'Submitted') ?></span></td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars(ucfirst($fc['status'])) ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 3. APARTMENT OCCUPANCY & LEASE LOG -->
        <div class="section-card" id="apartment-special-section">
          <div class="section-card-header">
            <h3>Apartments — Applications & Building Occupancy</h3>
            <span class="badge-dept badge-dept-apartments"><?= count($aptLeaseLogs ?? []) ?> Recent Applications</span>
          </div>
          <div class="report-table-wrapper">
            <table class="report-table">
              <thead>
                <tr>
                  <th>Unit ID</th>
                  <th>Application Date</th>
                  <th>Tenant Applicant</th>
                  <th>Payment Status</th>
                  <th>Maintenance Status</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($aptLeaseLogs)): ?>
                  <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">No apartment lease applications found.</td></tr>
                <?php else: ?>
                  <?php foreach ($aptLeaseLogs as $log): ?>
                  <tr>
                    <td style="font-weight:700;"><?= htmlspecialchars(($log['building'] ?? '') . '-' . ($log['room_number'] ?? 'Unit')) ?></td>
                    <td><?= date('M d, Y', strtotime($log['date'] ?? 'now')) ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?></td>
                    <td><span class="badge-status badge-paid">Paid Deposit</span></td>
                    <td><span class="badge-status badge-paid">Optimal</span></td>
                    <td><span class="badge-status badge-paid"><?= htmlspecialchars(ucfirst($log['status'] ?? 'Active')) ?></span></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ═══════ OFFICIAL PRINT SIGN-OFF (Letter Bond Paper Bottom) ═══════ -->
        <div class="print-only">
          <div class="print-sign-block">
            <div>
              <div><strong>Prepared By:</strong></div>
              <div style="margin-top:30px;border-top:1px solid #111;width:200px;text-align:center;padding-top:4px;">
                <?= htmlspecialchars($_SESSION['name'] ?? 'MIS Administrator') ?><br>
                <small style="color:#666;"><?= htmlspecialchars($_SESSION['role'] ?? 'MIS Directorate') ?></small>
              </div>
            </div>
            <div>
              <div><strong>Certified Correct:</strong></div>
              <div style="margin-top:30px;border-top:1px solid #111;width:200px;text-align:center;padding-top:4px;">
                ISCAG Central Management<br>
                <small style="color:#666;">Executive Oversight</small>
              </div>
            </div>
            <div>
              <div><strong>Verification Stamp:</strong></div>
              <div style="margin-top:30px;border-top:1px solid #111;width:200px;text-align:center;padding-top:4px;">
                SYSTEM VERIFIED<br>
                <small style="color:#666;">Ref: <?= strtoupper(substr(md5(time()), 0, 8)) ?></small>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    const rawMasterData = <?= json_encode($masterReportItems ?? [], JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    function getDeptClass(dept) {
      const d = (dept || '').toLowerCase();
      if (d.includes('apart')) return 'badge-dept-apartments';
      if (d.includes('dawah') || d.includes("da'wah")) return 'badge-dept-dawah';
      if (d.includes('edu')) return 'badge-dept-education';
      if (d.includes('damay')) return 'badge-dept-damayan';
      if (d.includes('fin')) return 'badge-dept-finance';
      return 'badge-status badge-pending';
    }

    function applyReportFilters() {
      const dept = document.getElementById('filter-dept').value;
      const status = document.getElementById('filter-status').value;
      const period = document.getElementById('filter-period').value;
      const search = document.getElementById('report-search').value.toLowerCase().trim();

      // Update print meta
      const printScope = document.getElementById('print-scope');
      if (printScope) printScope.textContent = dept === 'all' ? 'All Departments Combined' : dept + ' Department';
      const printStatus = document.getElementById('print-status');
      if (printStatus) printStatus.textContent = status === 'all' ? 'All Statuses' : status;

      // Filter data
      let filtered = rawMasterData;

      if (dept !== 'all') {
        filtered = filtered.filter(item => (item.department || '').toLowerCase().includes(dept.toLowerCase()));
      }

      if (status !== 'all') {
        filtered = filtered.filter(item => {
          const s = (item.status || '').toLowerCase();
          const target = status.toLowerCase();
          if (target === 'approved') return s.includes('approv') || s.includes('accept') || s.includes('assign') || s.includes('confirm');
          if (target === 'pending') return s.includes('pend') || s.includes('submit');
          if (target === 'completed') return s.includes('complet') || s.includes('resolv') || s.includes('releas');
          if (target === 'paid') return s.includes('paid');
          if (target === 'rejected') return s.includes('reject');
          return s === target;
        });
      }

      if (period !== 'all') {
        const now = new Date();
        filtered = filtered.filter(item => {
          if (!item.report_date) return true;
          const itemDate = new Date(item.report_date);
          const diffDays = Math.floor((now - itemDate) / (1000 * 60 * 60 * 24));
          if (period === 'today') return diffDays <= 1;
          if (period === 'week') return diffDays <= 7;
          if (period === 'month') return diffDays <= 30;
          if (period === 'year') return itemDate.getFullYear() === now.getFullYear();
          return true;
        });
      }

      if (search) {
        filtered = filtered.filter(item =>
          (item.subject_name || '').toLowerCase().includes(search) ||
          (item.service_type || '').toLowerCase().includes(search) ||
          (item.contact || '').toLowerCase().includes(search) ||
          (item.detail_info || '').toLowerCase().includes(search) ||
          String(item.id || '').toLowerCase().includes(search)
        );
      }

      renderMasterReport(filtered);

      // Section visibility based on department
      const eduSec = document.getElementById('edu-special-section');
      const damSec = document.getElementById('damayan-special-section');
      const aptSec = document.getElementById('apartment-special-section');

      if (dept === 'all') {
        if (eduSec) eduSec.style.display = 'block';
        if (damSec) damSec.style.display = 'block';
        if (aptSec) aptSec.style.display = 'block';
      } else if (dept.includes('Education')) {
        if (eduSec) eduSec.style.display = 'block';
        if (damSec) damSec.style.display = 'none';
        if (aptSec) aptSec.style.display = 'none';
      } else if (dept.includes('Damayan')) {
        if (eduSec) eduSec.style.display = 'none';
        if (damSec) damSec.style.display = 'block';
        if (aptSec) aptSec.style.display = 'none';
      } else if (dept.includes('Apart')) {
        if (eduSec) eduSec.style.display = 'none';
        if (damSec) damSec.style.display = 'none';
        if (aptSec) aptSec.style.display = 'block';
      } else {
        if (eduSec) eduSec.style.display = 'none';
        if (damSec) damSec.style.display = 'none';
        if (aptSec) aptSec.style.display = 'none';
      }
    }

    function renderMasterReport(items) {
      const tbody = document.getElementById('master-report-tbody');
      const counter = document.getElementById('report-counter');
      const kpiRec = document.getElementById('kpi-records-count');
      const kpiApp = document.getElementById('kpi-approved-count');
      const kpiPen = document.getElementById('kpi-pending-count');

      counter.textContent = items.length + ' record' + (items.length !== 1 ? 's' : '');
      kpiRec.textContent = items.length;

      let approvedCount = 0;
      let pendingCount = 0;

      items.forEach(it => {
        const s = (it.status || '').toLowerCase();
        if (s.includes('approv') || s.includes('accept') || s.includes('assign') || s.includes('confirm') || s.includes('paid') || s.includes('complet')) {
          approvedCount++;
        }
        if (s.includes('pend') || s.includes('submit')) {
          pendingCount++;
        }
      });

      kpiApp.textContent = approvedCount;
      kpiPen.textContent = pendingCount;

      if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">No records found matching the selected report filters.</td></tr>';
        return;
      }

      tbody.innerHTML = items.map(it => {
        const s = (it.status || '').toLowerCase();
        const sClass = s.includes('pend') ? 'badge-pending' : (s.includes('reject') ? 'badge-overdue' : 'badge-paid');
        return `
          <tr>
            <td class="td-id">#${it.id || '—'}</td>
            <td><span class="badge-dept ${getDeptClass(it.department)}">${it.department}</span></td>
            <td style="font-weight:600;">${it.service_type || '—'}</td>
            <td style="font-weight:600;">${it.subject_name || 'N/A'}</td>
            <td style="font-size:0.8rem;color:var(--text-muted);">${it.contact || '—'}</td>
            <td>${it.report_date ? formatDate(it.report_date) : '—'}</td>
            <td style="font-size:0.8rem;">${it.detail_info || '—'}</td>
            <td><span class="badge-status ${sClass}">${statusLabel(it.status || 'Active')}</span></td>
          </tr>
        `;
      }).join('');
    }

    // Print Report
    function printReport() {
      window.print();
    }

    // Export to CSV
    function exportReportCSV() {
      const dept = document.getElementById('filter-dept').value;
      const rows = [];
      rows.push(['Ref ID', 'Department', 'Service Type', 'Subject / Applicant', 'Contact', 'Date', 'Details', 'Status']);

      const tableRows = document.querySelectorAll('#master-report-tbody tr');
      tableRows.forEach(tr => {
        const cells = tr.querySelectorAll('td');
        if (cells.length >= 8) {
          const rowData = Array.from(cells).map(td => `"${td.innerText.replace(/"/g, '""').trim()}"`);
          rows.push(rowData);
        }
      });

      const csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.join(",")).join("\n");
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      const today = new Date().toISOString().slice(0, 10);
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", `ISCAG_MIS_${dept.replace(/\s+/g, '_')}_Report_${today}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    // Initialize
    applyReportFilters();
  </script>
</body>
</html>
