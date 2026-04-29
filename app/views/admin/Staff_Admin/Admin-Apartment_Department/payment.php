<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Billing & Payment</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    /* ── UNIFIED BILLING STYLES ── */
    .billing-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    /* Top Selector for Admins */
    .admin-selector-bar {
      display: flex;
      align-items: center;
      gap: 12px;
      background: white;
      padding: 16px 24px;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 14px rgba(0, 0, 0, 0.04);
      margin-bottom: 8px;
    }

    .admin-selector-bar select {
      flex: 1;
      max-width: 400px;
      padding: 10px 14px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      font-size: 0.95rem;
      font-family: inherit;
    }

    .admin-selector-bar select:focus {
      outline: none;
      border-color: var(--primary);
    }

    /* Printable SOA Container (From statement_of_account.php) */
    .soa-container {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 40px;
      width: 100%;
      margin: 0 auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      position: relative;
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
      font-weight: 700;
    }
    .soa-brand-text p {
      margin: 2px 0 0;
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
      font-weight: 800;
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

    /* Controls Panel */
    .controls-panel {
      background: white;
      padding: 24px;
      border-radius: 12px;
      border: 1px solid var(--border);
      margin-bottom: 24px;
      display: flex;
      gap: 16px;
      align-items: flex-end;
      flex-wrap: wrap;
    }
    .form-group-control {
      flex: 1;
      min-width: 150px;
    }
    .form-group-control.flex-2 {
      flex: 2;
      min-width: 240px;
    }
    .form-group-control label {
      display: block;
      margin-bottom: 8px;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .form-group-control select, .form-group-control input {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.95rem;
      outline: none;
      transition: all 0.2s;
      background: #f8f9fa;
      height: 42px;
      box-sizing: border-box;
    }
    .form-group-control select:focus, .form-group-control input:focus {
      border-color: var(--primary);
      background: white;
      box-shadow: 0 0 0 3px rgba(46,125,85,0.1);
    }
    .btn-filter-soa {
      padding: 0 24px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      height: 42px;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
    }
    .btn-filter-soa:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
    }

    /* Layout for Form + History */
    .billing-middle-grid {
      display: grid;
      grid-template-columns: 1fr 1.5fr;
      gap: 24px;
      align-items: start;
    }

    @media (max-width: 1000px) {
      .billing-middle-grid {
        grid-template-columns: 1fr;
      }
    }

    .form-card {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
      padding: 24px;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--primary-dark);
      border-bottom: 2px solid var(--border);
      padding-bottom: 12px;
      margin-bottom: 20px;
    }

    .form-row {
      margin-bottom: 16px;
    }

    .form-label {
      display: block;
      font-size: 0.85rem;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--text-main);
    }

    .form-control {
      width: 100%;
      padding: 10px 14px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 0.9rem;
      font-family: inherit;
    }

    .form-control:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    .file-dropzone {
      border: 2px dashed var(--border);
      border-radius: 8px;
      padding: 24px;
      text-align: center;
      background: #fafafa;
      position: relative;
      cursor: pointer;
      transition: all 0.2s;
    }

    .file-dropzone:hover {
      border-color: var(--primary);
      background: rgba(23, 107, 69, 0.03);
    }

    .file-dropzone input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }

    .file-name {
      margin-top: 10px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--primary-dark);
    }

    .btn-submit {
      width: 100%;
      background: var(--primary);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2);
      transition: all 0.2s;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(23, 107, 69, 0.3);
    }

    /* History Table */
    .history-card {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
      overflow: hidden;
    }

    .history-card-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .history-card-header h3 {
      margin: 0;
      font-size: 1.1rem;
      color: var(--primary-dark);
    }

    table.table-history {
      width: 100%;
      border-collapse: collapse;
    }

    table.table-history th {
      background: #f9f9f9;
      padding: 14px 20px;
      font-size: 0.8rem;
      text-transform: uppercase;
      color: var(--text-muted);
      text-align: left;
    }

    table.table-history td {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      font-size: 0.9rem;
      vertical-align: middle;
    }

    table.table-history tbody tr:hover {
      background: #fdfdfd;
    }

    .badge-status {
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      display: inline-block;
    }

    .status-pending {
      background: rgba(199, 154, 43, 0.1);
      color: #c79a2b;
    }

    .status-verified {
      background: rgba(47, 138, 96, 0.1);
      color: #2f8a60;
    }

    .status-rejected {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
    }

    /* Admin Action Buttons */
    .admin-actions {
      display: flex;
      gap: 8px;
    }

    .btn-act {
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      border: none;
      cursor: pointer;
      color: white;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .btn-act.verify {
      background: var(--success);
    }

    .btn-act.reject {
      background: var(--danger);
    }

    .btn-act.view {
      background: var(--primary);
    }

    /* Removed Modal Styling per request */

    /* Print */
    @media print {
      body * {
        visibility: hidden;
      }

      .soa-container,
      .soa-container * {
        visibility: visible;
      }

      .soa-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
      }
      
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>

<body>

  <div class="app-wrapper">
    <!-- SIDEBAR -->
    <!----sidebar---->
    <?php 
      $active_page = 'payment';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

    <!-- MAIN -->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" id="page-title">Billing & Payment Gateway</div>
          <div class="top-bar-subtitle">Unified module for SOA, payment submission, and verification.</div>
        </div>
        <div class="top-bar-actions">
          <button class="btn-topbar primary" onclick="window.print()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
              <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            Print Statement
          </button>
        </div>
      </div>

      <div class="page-body">
        <!-- Admin Tenant Selector -->
        <!-- Controls Panel -->
        <div class="controls-panel no-print">
          <div class="form-group-control flex-2">
            <label>Select Tenant</label>
            <select id="tenant-dropdown" onchange="loadTenantData()">
              <option value="">— Select a Tenant to Manage —</option>
              <?php foreach ($allUsers ?? [] as $u): ?>
                <?php $uName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')); ?>
                <option value="<?= $u['tenant_id'] ?>"><?= htmlspecialchars($uName ?: 'User '.$u['tenant_id']) ?> (ID: <?= $u['tenant_id'] ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group-control">
            <label>Date From</label>
            <input type="date" id="date-from" onchange="loadTenantData()" />
          </div>
          <div class="form-group-control">
            <label>Date To</label>
            <input type="date" id="date-to" onchange="loadTenantData()" />
          </div>
          <div class="form-group-control" style="flex: 0 0 auto;">
            <label>&nbsp;</label>
            <button class="btn-filter-soa" onclick="loadTenantData()">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/></svg>
              Filter
            </button>
          </div>
        </div>

        <div class="billing-wrapper" id="billing-wrapper" style="display:none;">

          <!-- SOA SECTION (Official Layout) -->
          <div class="soa-container" id="printable-soa">
            <div class="soa-header">
              <div class="soa-brand">
                <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" onerror="this.src='https://via.placeholder.com/80?text=Logo'">
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
                <p><strong id="soa-t-name" style="font-size:1.2rem;">--</strong></p>
                <p>Unit: <strong id="soa-t-room">--</strong></p>
                <p>App ID: <span id="soa-t-app">--</span></p>
              </div>
              <div class="soa-details-right" style="text-align: right;">
                <p>Statement Period:</p>
                <p><strong id="soa-month">April 2026</strong></p>
              </div>
            </div>

            <table class="soa-table">
              <thead>
                <tr>
                  <th>Particulars</th>
                  <th style="text-align:right;">Previous Balance</th>
                  <th style="text-align:right;">Current Charges</th>
                  <th style="text-align:right;">Payments</th>
                  <th style="text-align:right;">Balance</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Water Consumption</td>
                  <td id="b-w-prev" style="text-align:right;">₱0.00</td>
                  <td id="b-w-curr" style="text-align:right;">₱0.00</td>
                  <td id="b-w-pay" style="text-align:right; color:var(--success);">₱0.00</td>
                  <td id="b-w-bal" style="text-align:right; font-weight:600;">₱0.00</td>
                </tr>
                <tr>
                  <td>Monthly Rent</td>
                  <td id="b-r-prev" style="text-align:right;">₱0.00</td>
                  <td id="b-r-curr" style="text-align:right;">₱0.00</td>
                  <td id="b-r-pay" style="text-align:right; color:var(--success);">₱0.00</td>
                  <td id="b-r-bal" style="text-align:right; font-weight:600;">₱0.00</td>
                </tr>
                <tr>
                  <td>Parking Fee</td>
                  <td id="b-p-prev" style="text-align:right;">₱0.00</td>
                  <td id="b-p-curr" style="text-align:right;">₱0.00</td>
                  <td id="b-p-pay" style="text-align:right; color:var(--success);">₱0.00</td>
                  <td id="b-p-bal" style="text-align:right; font-weight:600;">₱0.00</td>
                </tr>
                <tr>
                  <td>Contribution</td>
                  <td id="b-c-prev" style="text-align:right;">₱0.00</td>
                  <td id="b-c-curr" style="text-align:right;">₱0.00</td>
                  <td id="b-c-pay" style="text-align:right; color:var(--success);">₱0.00</td>
                  <td id="b-c-bal" style="text-align:right; font-weight:600;">₱0.00</td>
                </tr>
              </tbody>
            </table>

            <div class="soa-summary">
              <div class="soa-summary-row total">
                <span>Outstanding Balance:</span>
                <span id="soa-total-due">₱0.00</span>
              </div>
            </div>

            <div style="margin-top: 40px; text-align: center; color: var(--text-muted); font-size: 0.8rem; border-top: 1px solid var(--border); padding-top: 15px;" class="no-print">
              <p>This is a system-generated document. For inquiries, please contact the ISCAG Apartment Department.</p>
            </div>
          </div>

          <!-- FORM & HISTORY GRID -->
          <div class="billing-middle-grid">

            <!-- PAYMENT SUBMISSION FORM -->
            <div class="form-card">
              <div class="card-title" id="form-title">Submit Proof of Payment</div>
              <form id="payForm" onsubmit="event.preventDefault(); handlePaymentSubmit();">
                <div class="form-row">
                  <label class="form-label">Amount Paid (₱)</label>
                  <input type="number" step="0.01" class="form-control" id="form-amount" required>
                </div>
                <div class="form-row" style="display:flex;gap:12px;">
                  <div style="flex:1;">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" id="form-method" required>
                      <option value="">Select...</option>
                      <option value="GCash">GCash</option>
                      <option value="Maya">Maya</option>
                      <option value="Bank Transfer">Bank Transfer</option>
                      <option value="Cash OTC">Cash OTC (Admin Only)</option>
                    </select>
                  </div>
                  <div style="flex:1;">
                    <label class="form-label">Date Paid</label>
                    <input type="date" class="form-control" id="form-date" required>
                  </div>
                </div>
                <div class="form-row">
                  <label class="form-label">Reference Number</label>
                  <input type="text" class="form-control" id="form-ref" placeholder="Trace ID / Receipt No" required>
                </div>
                <div class="form-row">
                  <label class="form-label">Attach Proof (Image/PDF)</label>
                  <div class="file-dropzone">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:var(--text-muted);">
                      <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                    <div class="file-name" id="file-label">Click to upload file...</div>
                    <input type="file" id="form-file" accept="image/*,.pdf" onchange="updateFileLabel(this)" required>
                  </div>
                </div>
                <button type="submit" class="btn-submit">Submit Payment Record</button>
              </form>
            </div>

            <!-- PAYMENT STATUS HISTORY -->
            <div class="history-card">
              <div class="history-card-header">
                <h3>Payment Tracking & Verification</h3>
              </div>
              <div style="overflow-x:auto;">
                <table class="table-history">
                  <thead>
                    <tr>
                      <th>Date Uploaded</th>
                      <th>Unit</th>
                      <th>App ID</th>
                      <th>Amount</th>
                      <th>Method & Ref</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="history-tbody">
                    <!-- JS Injected -->
                  </tbody>
                </table>
              </div>
              <div id="no-history-msg" style="padding:40px; text-align:center; color:var(--text-muted); display:none;">
                No payments submitted for this billing cycle yet.</div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal overlay removed per request -->


  <!-- NOTIFICATION TOAST -->
  <div id="toast"
    style="visibility:hidden;min-width:250px;background:#333;color:#fff;text-align:center;border-radius:8px;padding:16px;position:fixed;z-index:9999;bottom:30px;right:30px;font-size:0.9rem;font-weight:600;box-shadow:0 10px 30px rgba(0,0,0,0.2);transition:visibility 0.4s, opacity 0.4s;opacity:0;">
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Apartment Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'staff@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Apartment Manager';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");
    // ── UNIFIED SYSTEM CONTEXT ──
    const sessionRole = "<?= $_SESSION['role'] ?? '' ?>";
    // Map PHP session roles directly to ensure the UI shows for admins
    let activeRole = (sessionRole === 'Admin') ? ROLES.MIS_ADMIN : ROLES.STAFF_ADMIN;
    
    // Also update storage for consistency across other scripts
    localStorage.setItem('mis_current_role', activeRole);
    setCurrentRole(activeRole); 

    let APPROVED_TENANTS = [];
    let CURRENT_TARGET = null;
    let currentProofIdAction = null;

    const APPROVED_FROM_DB = <?= json_encode($approvedTenants ?? []) ?>;
    const ALL_USERS_DB = <?= json_encode($allUsers ?? []) ?>;
    const APT_TYPES_DB = <?= json_encode($apartmentTypes ?? []) ?>;

    function initPage() {
      // 1. Start with tenants who have active/approved applications
      APPROVED_TENANTS = APPROVED_FROM_DB.map(r => {
        const apt = APT_TYPES_DB.find(a => a.type_key === r.roomtype || a.label === r.roomtype);
        const rentAmount = apt ? parseFloat(apt.price) : 5000;
        
        return {
          id: r.tenant_id,
          app_id: r.id,
          name: `${r.first_name} ${r.last_name}`.trim(),
          room: r.roomtype || 'Unassigned',
          current: { water: 350, rent: rentAmount, parking: 0, contribution: 100 },
          prev: { water: 0, rent: 0, parking: 0, contribution: 0 },
          payments: { water: 0, rent: 0, parking: 0, contribution: 0 }
        };
      });

      // 2. Merge all other real users from the database who don't have applications yet
      ALL_USERS_DB.forEach(u => {
        const exists = APPROVED_TENANTS.some(t => t.id === u.tenant_id);
        if (!exists) {
          APPROVED_TENANTS.push({
            id: u.tenant_id,
            app_id: 'N/A',
            name: `${u.first_name} ${u.last_name}`.trim(),
            room: 'General Account',
            current: { water: 0, rent: 0, parking: 0, contribution: 100 },
            prev: { water: 0, rent: 0, parking: 0, contribution: 0 },
            payments: { water: 0, rent: 0, parking: 0, contribution: 0 }
          });
        }
      });

      // 3. (Fallback removed per request - showing only real users)
      if (APPROVED_TENANTS.length === 0) {
        console.warn("No real users found in database for billing dropdown.");
      }

      // UI Adaptation based on roles
      const isAdmin = (activeRole === ROLES.MIS_ADMIN || activeRole === ROLES.STAFF_ADMIN);

      document.getElementById('form-date').valueAsDate = new Date();
      document.getElementById('soa-month').textContent = new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

      if (isAdmin) {
        // Setup Admin View
        setupAdminSidebar();
        const selectorUi = document.getElementById('admin-selector-ui');
        if (selectorUi) selectorUi.style.display = 'flex';

        const sel = document.getElementById('tenant-dropdown');
        if (sel) {
          // Dropdown is already populated by PHP for reliability.
          // We just need to make sure the internal APPROVED_TENANTS list is ready for SOA logic.
        }

      } else {
        // Setup User View Constraints
        setupUserSidebar();
        document.getElementById('admin-selector-ui').style.display = 'none';

        // Simulate User session targeting
        CURRENT_TARGET = APPROVED_TENANTS[0];
        document.getElementById('billing-wrapper').style.display = 'block';
        renderUnifiedModule();
      }
    }

    // Handle Dynamic Sidebars depending on login origin
    function setupAdminSidebar() {
      document.getElementById('sb-user-name').textContent = 'Administrator';
      document.getElementById('sb-user-role').textContent = 'Staff / MIS';
      document.querySelector('.sidebar-nav').innerHTML = `
        <div class="nav-section-label">Admin</div>
        <a href="apartment_dashboard.html" class="nav-item" data-tooltip="Dashboard">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
          </svg>
          <span class="nav-item-label">Dashboard</span>
        </a>
        <a href="<?= url('/admin/apartment/profile') ?>" class="nav-item" data-tooltip="Profile">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
          </svg>
          <span class="nav-item-label">My Profile</span>
        </a>
        <div class="nav-section-label">Management</div>
        <a href="apartments_info.html" class="nav-item" data-tooltip="Apartment Info">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
          </svg>
          <span class="nav-item-label">Apartment Info</span>
        </a>
        <a href="payment.html" class="nav-item active" data-tooltip="Billing & Payment">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
          </svg>
          <span class="nav-item-label">Billing & Payment</span>
        </a>
    `;
    }

    function setupUserSidebar() {
      document.getElementById('sb-user-name').textContent = 'Tenant Account';
      document.getElementById('sb-user-role').textContent = 'User Side Wrapper';
      document.querySelector('.sidebar-nav').innerHTML = `
      <div class="nav-section-label">User Functions</div>
      <a href="javascript:void(0)" class="nav-item active"><span class="nav-item-label">My Billing</span></a>
    `;
    }

    // ── CORE LOGIC ──
    function loadTenantData() {
      const val = document.getElementById('tenant-dropdown').value;
      const wrapper = document.getElementById('billing-wrapper');
      if (!val) {
        wrapper.style.display = 'none';
        CURRENT_TARGET = null;
      } else {
        wrapper.style.display = 'block';
        CURRENT_TARGET = APPROVED_TENANTS.find(x => x.id === val);
        renderUnifiedModule();
      }
    }

    function fmtMon(num) { return '₱' + parseFloat(num).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }

    function renderUnifiedModule() {
      if (!CURRENT_TARGET) return;

      // 1. RENDER SOA Calculations (Balance = Prev + Curr - Payments)
      document.getElementById('soa-t-name').textContent = CURRENT_TARGET.name;
      document.getElementById('soa-t-room').textContent = CURRENT_TARGET.room;
      document.getElementById('soa-t-app').textContent = CURRENT_TARGET.app_id;
      document.getElementById('soa-date-generated').textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

      const dateFrom = document.getElementById('date-from').value;
      const dateTo = document.getElementById('date-to').value;
      let periodText = 'Current Billing Cycle';
      if(dateFrom && dateTo) periodText = `${dateFrom} to ${dateTo}`;
      else if(dateFrom) periodText = `From ${dateFrom}`;
      else if(dateTo) periodText = `Up to ${dateTo}`;
      
      document.getElementById('soa-month').textContent = periodText;

      let subTotal = 0;
      const cat = ['water', 'rent', 'parking', 'contribution'];
      cat.forEach(item => {
        let run = CURRENT_TARGET.prev[item] + CURRENT_TARGET.current[item] - CURRENT_TARGET.payments[item];
        subTotal += run;

        document.getElementById(`b-${item.charAt(0)}-prev`).textContent = fmtMon(CURRENT_TARGET.prev[item]);
        document.getElementById(`b-${item.charAt(0)}-curr`).textContent = fmtMon(CURRENT_TARGET.current[item]);
        document.getElementById(`b-${item.charAt(0)}-pay`).textContent = fmtMon(CURRENT_TARGET.payments[item]);
        document.getElementById(`b-${item.charAt(0)}-bal`).textContent = fmtMon(run);
      });

      document.getElementById('soa-total-due').textContent = fmtMon(subTotal);
      document.getElementById('form-amount').value = subTotal; // Auto-suggest total amount

      // 2. RENDER HISTORY
      renderHistoryTable();
    }

    function updateFileLabel(inp) {
      if (inp.files.length > 0) document.getElementById('file-label').textContent = inp.files[0].name;
      else document.getElementById('file-label').textContent = "Click to upload file...";
    }

    // Payment Subroutines Unified
    function handlePaymentSubmit() {
      if (!CURRENT_TARGET) return;

      const allProofs = JSON.parse(localStorage.getItem('mis_proof_of_payments') || '[]');
      const newProof = {
        proof_id: "PR-2026-" + String(allProofs.length + 1).padStart(3, '0'),
        app_id: CURRENT_TARGET.app_id,
        tenant_name: CURRENT_TARGET.name,
        amount: document.getElementById('form-amount').value,
        method: document.getElementById('form-method').value,
        reference_no: document.getElementById('form-ref').value,
        upload_date: new Date().toISOString(),
        status: "Pending"
      };

      allProofs.push(newProof);
      localStorage.setItem('mis_proof_of_payments', JSON.stringify(allProofs));

      // Audit System Interop
      let audits = JSON.parse(localStorage.getItem('mis_audit_logs') || '[]');
      audits.push({ admin_id: "SYSTEM", module: "BILLING", action: "SUBMIT_PAYMENT", timestamp: new Date().toISOString() });
      localStorage.setItem('mis_audit_logs', JSON.stringify(audits));

      showToast('✅ Payment Document Submitted Successfully!', 'var(--success)');
      document.getElementById('payForm').reset();
      document.getElementById('file-label').textContent = "Click to upload file...";

      renderHistoryTable();
    }

    function renderHistoryTable() {
      if (!CURRENT_TARGET) return;
      const isAdmin = (activeRole === ROLES.MIS_ADMIN || activeRole === ROLES.STAFF_ADMIN);
      const history = JSON.parse(localStorage.getItem('mis_proof_of_payments') || '[]');

      // Map history to current target
      const myHistory = history.filter(h => h.app_id === CURRENT_TARGET.app_id || h.tenant_name === CURRENT_TARGET.name);

      const tbody = document.getElementById('history-tbody');
      if (myHistory.length === 0) {
        document.getElementById('no-history-msg').style.display = 'block';
        tbody.innerHTML = '';
        return;
      }

      document.getElementById('no-history-msg').style.display = 'none';
      tbody.innerHTML = myHistory.sort((a, b) => new Date(b.upload_date) - new Date(a.upload_date)).map(h => {

        let statClass = 'status-pending';
        if (h.status === 'Verified') statClass = 'status-verified';
        if (h.status === 'Rejected') statClass = 'status-rejected';

        // Safe templating
        let actionsHtml = `<a href="javascript:void(0)" style="color:var(--primary);font-size:0.8rem;margin-right:8px;font-weight:700;"><svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;vertical-align:middle;margin-right:4px;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg> File</a>`;

        if (isAdmin && h.status === 'Pending') {
          actionsHtml += `
          <button class="btn-act verify" onclick="commitVerify('${h.proof_id}', 'Verified')">Verify</button>
          <button class="btn-act reject" onclick="commitVerify('${h.proof_id}', 'Rejected')">Reject</button>
        `;
        }

        return `
        <tr>
          <td>${new Date(h.upload_date).toLocaleDateString()}</td>
          <td style="font-weight:600; color:var(--primary-dark);">${CURRENT_TARGET.room}</td>
          <td style="font-size:0.8rem;">${h.app_id || CURRENT_TARGET.app_id}</td>
          <td style="font-weight:700;">₱${parseFloat(h.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
          <td>
            <div style="font-weight:600;">${h.method}</div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Ref: ${h.reference_no}</div>
            <div style="font-size:0.75rem; color:var(--text-muted);">${h.remarks ? 'Remark: ' + h.remarks : ''}</div>
          </td>
          <td><span class="badge-status ${statClass}">${h.status}</span></td>
          <td><div class="admin-actions">${actionsHtml}</div></td>
        </tr>
      `;
      }).join('');
    }

    function commitVerify(proofId, statusAction) {
      const history = JSON.parse(localStorage.getItem('mis_proof_of_payments') || '[]');
      const idx = history.findIndex(h => h.proof_id === proofId);
      if (idx > -1) {
        history[idx].status = statusAction;
        localStorage.setItem('mis_proof_of_payments', JSON.stringify(history));

        let audits = JSON.parse(localStorage.getItem('mis_audit_logs') || '[]');
        audits.push({ admin_id: "ADMIN_SYSTEM", module: "BILLING", action: statusAction.toUpperCase() + "_PAYMENT", timestamp: new Date().toISOString() });
        localStorage.setItem('mis_audit_logs', JSON.stringify(audits));

        showToast(`Payment ${statusAction} successfully.`, statusAction === 'Verified' ? 'var(--success)' : 'var(--danger)');
        renderHistoryTable();

        if (statusAction === 'Verified') {
          // Mock Auto Deduct
          // In a real database, it loops through balance and registers payments logically.
          setTimeout(() => showToast(`Auto-Deduction routine applied to SOA`, 'var(--primary)'), 1000);
        }
      }
    }

    function showToast(msg, bg) {
      const t = document.getElementById("toast");
      t.textContent = msg; t.style.backgroundColor = bg;
      t.style.visibility = "visible"; t.style.opacity = "1";
      setTimeout(() => { t.style.opacity = "0"; t.style.visibility = "hidden"; }, 3500);
    }

    initPage();
  </script>
</body>

</html>