<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Distribution Records</title>
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

    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 18px 20px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 4px;
      position: relative;
      overflow: hidden;
    }
    .insight-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .insight-card.distributed::after { background: var(--primary); }
    .insight-card.in-kind::after { background: var(--accent); }
    .insight-card.awaiting::after { background: #8b2e2e; }

    .insight-label {
      font-size: 0.7rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .insight-value {
      font-size: 1.65rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.1;
    }

    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      background: white;
      padding: 16px 20px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .search-input {
      padding: 8px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.85rem;
      width: 250px;
      outline: none;
    }
    .search-input:focus { border-color: var(--primary); }
    .filter-select {
      padding: 8px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.82rem;
      font-weight: 600;
      background: white;
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .badge-distributed { background: rgba(47, 138, 96, 0.12); color: #176b45; }
    .badge-pending { background: rgba(139, 46, 46, 0.12); color: #8b2e2e; }

    .dept-tag {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
    }
    .dept-damayan { background: #e8f5ed; color: #0f5c3a; }
    .dept-dawah { background: #fdf5e6; color: #a37817; }

    .btn-action-sm {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 6px;
      border: 1px solid var(--border);
      background: white;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--text-main);
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-action-sm:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: rgba(23, 107, 69, 0.04);
    }
    .btn-action-primary {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    .btn-action-primary:hover {
      background: var(--primary-dark);
      color: white;
    }

    /* Modal Backdrop */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      backdrop-filter: blur(4px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .modal-box {
      background: white;
      border-radius: 20px;
      width: 100%;
      max-width: 680px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0,0,0,0.18);
      animation: modalPop 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes modalPop {
      from { opacity: 0; transform: scale(0.92) translateY(15px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fafbfb;
    }
    .modal-title {
      font-family: 'Lora', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }
    .modal-body { padding: 24px; }
    .modal-footer {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      background: #fafbfb;
    }
    .form-group { margin-bottom: 16px; }
    .form-group label {
      display: block;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--text-main);
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .form-control {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 0.88rem;
      color: var(--text-main);
      background: white;
      outline: none;
    }
    .form-control:focus { border-color: var(--primary); }
  </style>
  <link rel="stylesheet" href="<?= asset('css/charity.css') ?>?v=<?= time() ?>" />
</head>
<body class="charity-module">
  <div class="app-wrapper">
    <!-- Damayan Sidebar -->
    <?php 
      $active_page = 'charity_distributions';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Distribution Records</div>
          <div class="top-bar-subtitle">Record and verify actual handouts of allocated aid to beneficiaries and upload supporting acknowledgments</div>
        </div>
        <div class="top-bar-actions">
          <button onclick="openDistModal()" class="btn-topbar" style="background:var(--primary); color:white; border:none; border-radius:8px; padding:9px 18px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Record Distribution
          </button>
        </div>
      </div>

      <div class="page-body">
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= url('/admin/damayan/charity') ?>">Charity Programs</a>
          <span class="sep">›</span>
          <span class="current">Distribution Records</span>
        </div>

        <!-- Summary Insights -->
        <div class="admin-insights">
          <div class="insight-card distributed">
            <div class="insight-label">Total Cash Aid Distributed</div>
            <div class="insight-value" style="color:var(--primary);">₱<?= number_format($stats['distributed']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['distributed']['count'] ?> confirmed distribution(s)</div>
          </div>
          <div class="insight-card in-kind">
            <div class="insight-label">Physical Goods Distributed</div>
            <div class="insight-value" style="color:var(--accent);"><?= number_format($stats['distributed']['in_kind']) ?> Units</div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Burial shrouds, rice packs, relief aid</div>
          </div>
          <div class="insight-card awaiting">
            <div class="insight-label">Allocations Awaiting Handout</div>
            <div class="insight-value" style="color:#8b2e2e;"><?= count($awaitingAllocations) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Allocated lots ready for distribution</div>
          </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="<?= url('/admin/damayan/charity/distributions') ?>" class="filter-bar">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <input type="text" name="search" class="search-input" placeholder="Search dist ref, recipient, items..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />

            <select name="department" class="filter-select" onchange="this.form.submit()">
              <option value="">All Departments</option>
              <option value="Damayan" <?= ($filters['department'] ?? '') === 'Damayan' ? 'selected' : '' ?>>Damayan</option>
              <option value="Dawah" <?= ($filters['department'] ?? '') === 'Dawah' ? 'selected' : '' ?>>Da'wah</option>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">Date:</span>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <span style="color:var(--text-muted);">-</span>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <button type="submit" class="btn-action-sm btn-action-primary">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['department']) || !empty($filters['date_from'])): ?>
              <a href="<?= url('/admin/damayan/charity/distributions') ?>" class="btn-action-sm">Reset</a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Distributions Data Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
              Distribution Transactions (<?= count($distributions) ?>)
            </h6>
            <div style="font-size:0.75rem; color:var(--text-muted);">
              All distributions confirmed by authorized staff with beneficiary details
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Dist Ref</th>
                    <th>Linked Allocation</th>
                    <th>Dept</th>
                    <th>Distributed Resources</th>
                    <th>Authorized Recipient</th>
                    <th>Purpose</th>
                    <th>Distribution Date</th>
                    <th>Distributed By</th>
                    <th>Supporting Proof</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($distributions)): ?>
                    <tr>
                      <td colspan="11" style="text-align:center; padding:40px; color:var(--text-muted);">
                        No distribution transactions recorded yet.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($distributions as $dist): ?>
                      <tr>
                        <td>
                          <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($dist['distribution_ref']) ?></strong>
                        </td>
                        <td>
                          <div style="font-family:monospace; font-weight:700; color:var(--accent); font-size:0.85rem;"><?= htmlspecialchars($dist['allocation_ref']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($dist['donation_ref']) ?></div>
                        </td>
                        <td>
                          <span class="dept-tag <?= $dist['receiving_department'] === 'Damayan' ? 'dept-damayan' : 'dept-dawah' ?>">
                            <?= htmlspecialchars($dist['receiving_department']) ?>
                          </span>
                        </td>
                        <td>
                          <strong style="color:var(--primary-dark); font-size:0.95rem;">
                            <?= htmlspecialchars($dist['items_summary']) ?>
                          </strong>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($dist['received_by']) ?></div>
                        </td>
                        <td>
                          <div style="max-width:200px; font-size:0.82rem; color:var(--text-muted);"><?= htmlspecialchars($dist['purpose']) ?></div>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                          <?= date('M d, Y', strtotime($dist['distribution_date'])) ?>
                        </td>
                        <td style="font-size:0.8rem;">
                          <?= htmlspecialchars($dist['distributed_by']) ?>
                        </td>
                        <td>
                          <?php if (!empty($dist['supporting_document'])): ?>
                            <a href="<?= asset($dist['supporting_document']) ?>" target="_blank" class="btn-action-sm" style="color:var(--primary); font-size:0.72rem;">
                              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
                              View Proof
                            </a>
                          <?php else: ?>
                            <span style="font-size:0.75rem; color:var(--text-muted);">None</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge-status badge-distributed">Completed</span>
                        </td>
                        <td style="text-align:right;">
                          <button onclick="viewDistribution(<?= $dist['id'] ?>)" class="btn-action-sm" title="View Full Details">
                            Details
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL: RECORD ACTUAL DISTRIBUTION -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="dist-modal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title">Record Aid Distribution</h5>
        <button onclick="closeDistModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <form id="dist-form" onsubmit="submitDistForm(event)" enctype="multipart/form-data">
        <div class="modal-body">
          <div id="dist-error" style="display:none; padding:12px 16px; background:#fdf0ed; color:#8b2e2e; border-radius:8px; margin-bottom:16px; font-weight:600; font-size:0.85rem;"></div>

          <!-- Select Allocation Awaiting Distribution -->
          <div class="form-group">
            <label>Select Allocation Awaiting Distribution <span style="color:#8b2e2e;">*</span></label>
            <select name="allocation_id" id="dist-alloc-select" required class="form-control" onchange="onAllocSelected(this)">
              <option value="">-- Choose an allocation with pending resources --</option>
              <?php foreach ($awaitingAllocations as $aw): ?>
                <?php 
                  $label = $aw['allocation_ref'] . ' (' . $aw['receiving_department'] . ') — ' . $aw['purpose'] . ' [Remaining: ';
                  if ($aw['allocation_type'] === 'monetary') {
                    $label .= '₱' . number_format($aw['remaining_allocated_amount'], 2);
                  } else {
                    $label .= $aw['remaining_allocated_quantity'] . ' Units - ' . ($aw['item_description'] ?: 'Items');
                  }
                  $label .= ']';
                ?>
                <option value="<?= $aw['id'] ?>"
                        data-type="<?= $aw['allocation_type'] ?>"
                        data-rem-amt="<?= $aw['remaining_allocated_amount'] ?>"
                        data-rem-qty="<?= $aw['remaining_allocated_quantity'] ?>"
                        data-dept="<?= $aw['receiving_department'] ?>"
                        data-purpose="<?= htmlspecialchars($aw['purpose']) ?>"
                        data-items="<?= htmlspecialchars($aw['item_description'] ?? '') ?>">
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Allocation Preview Box -->
          <div id="alloc-preview-box" style="display:none; background:#f9fafb; border:1px solid var(--border); border-radius:10px; padding:14px; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <span id="ap-dept" class="dept-tag dept-damayan"></span>
              <span id="ap-rem" style="font-weight:800; color:var(--primary);"></span>
            </div>
            <div id="ap-purpose" style="font-size:0.85rem; color:var(--text-muted);"></div>
          </div>

          <!-- Distributed Resources Amount / Quantity -->
          <div id="dist-amt-group" class="form-group">
            <label>Actual Amount Distributed (₱) <span style="color:#8b2e2e;">*</span></label>
            <input type="number" step="0.01" min="1" name="distributed_amount" id="dist-amt-input" class="form-control" placeholder="0.00" oninput="validateDistBalance()" />
            <div id="dist-amt-msg" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <div id="dist-qty-group" class="form-group" style="display:none;">
            <label>Actual Quantity Distributed <span style="color:#8b2e2e;">*</span></label>
            <input type="number" min="1" name="distributed_quantity" id="dist-qty-input" class="form-control" placeholder="e.g. 5" oninput="validateDistBalance()" />
            <div id="dist-qty-msg" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <!-- Recipient Name -->
          <div class="form-group">
            <label>Received By / Authorized Recipient Full Name <span style="color:#8b2e2e;">*</span></label>
            <input type="text" name="received_by" required class="form-control" placeholder="e.g. Brother Ahmad Rasheed (Next-of-Kin) / Ustadz Farhan" />
          </div>

          <!-- Purpose of Distribution -->
          <div class="form-group">
            <label>Purpose of Distribution</label>
            <input type="text" name="purpose" id="dist-purpose-input" class="form-control" placeholder="Leave blank to use allocation purpose" />
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <!-- Distribution Date -->
            <div class="form-group">
              <label>Distribution Date <span style="color:#8b2e2e;">*</span></label>
              <input type="date" name="distribution_date" required value="<?= date('Y-m-d') ?>" class="form-control" />
            </div>
            <!-- Distributed By -->
            <div class="form-group">
              <label>Distributed By (Staff) <span style="color:#8b2e2e;">*</span></label>
              <input type="text" name="distributed_by" required value="<?= htmlspecialchars($_SESSION['name'] ?? 'Damayan Staff') ?>" class="form-control" />
            </div>
          </div>

          <!-- Supporting Document / Acknowledgment Upload -->
          <div class="form-group">
            <label>Supporting Document / Signed Acknowledgment (Image or PDF)</label>
            <input type="file" name="supporting_document" accept=".png,.jpg,.jpeg,.webp,.pdf" class="form-control" style="padding:6px;" />
            <div style="font-size:0.72rem; color:var(--text-muted); margin-top:3px;">Optional upload of signed receiving form, acknowledgment receipt, or beneficiary ID copy (Max 10MB).</div>
          </div>

          <!-- Remarks -->
          <div class="form-group" style="margin-bottom:0;">
            <label>Remarks</label>
            <textarea name="remarks" rows="2" class="form-control" placeholder="Optional notes regarding the handover or condition..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" onclick="closeDistModal()" class="btn-action-sm" style="padding:9px 18px;">Cancel</button>
          <button type="submit" id="btn-save-dist" class="btn-action-sm btn-action-primary" style="padding:9px 24px; font-weight:700;">
            Confirm & Complete Distribution
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL: VIEW DISTRIBUTION DETAILS -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="dist-view-modal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title" id="dv-title">Distribution Record</h5>
        <button onclick="closeDistViewModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <div class="modal-body" id="dv-body">
        <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading details...</div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeDistViewModal()" class="btn-action-sm" style="padding:8px 18px;">Close</button>
      </div>
    </div>
  </div>

  <script>
    let activeAlloc = null;

    function openDistModal() {
      document.getElementById('dist-form').reset();
      document.getElementById('alloc-preview-box').style.display = 'none';
      document.getElementById('dist-error').style.display = 'none';
      document.getElementById('dist-amt-group').style.display = 'block';
      document.getElementById('dist-qty-group').style.display = 'none';
      activeAlloc = null;
      document.getElementById('dist-modal').style.display = 'flex';
    }

    function closeDistModal() {
      document.getElementById('dist-modal').style.display = 'none';
    }

    function onAllocSelected(selectEl) {
      const opt = selectEl.options[selectEl.selectedIndex];
      if (!opt || !opt.value) {
        document.getElementById('alloc-preview-box').style.display = 'none';
        activeAlloc = null;
        return;
      }

      activeAlloc = {
        id: opt.value,
        type: opt.getAttribute('data-type'),
        remAmt: parseFloat(opt.getAttribute('data-rem-amt') || 0),
        remQty: parseInt(opt.getAttribute('data-rem-qty') || 0),
        dept: opt.getAttribute('data-dept'),
        purpose: opt.getAttribute('data-purpose'),
        items: opt.getAttribute('data-items')
      };

      const deptEl = document.getElementById('ap-dept');
      deptEl.textContent = activeAlloc.dept;
      deptEl.className = 'dept-tag ' + (activeAlloc.dept === 'Damayan' ? 'dept-damayan' : 'dept-dawah');

      document.getElementById('ap-purpose').textContent = 'Purpose: ' + activeAlloc.purpose;
      document.getElementById('dist-purpose-input').placeholder = activeAlloc.purpose;

      if (activeAlloc.type === 'monetary') {
        document.getElementById('dist-amt-group').style.display = 'block';
        document.getElementById('dist-qty-group').style.display = 'none';
        document.getElementById('dist-amt-input').value = activeAlloc.remAmt;
        document.getElementById('dist-amt-input').max = activeAlloc.remAmt;
        document.getElementById('ap-rem').textContent = 'Allocated Balance: ₱' + activeAlloc.remAmt.toLocaleString('en-US', {minimumFractionDigits: 2});
      } else {
        document.getElementById('dist-amt-group').style.display = 'none';
        document.getElementById('dist-qty-group').style.display = 'block';
        document.getElementById('dist-qty-input').value = activeAlloc.remQty;
        document.getElementById('dist-qty-input').max = activeAlloc.remQty;
        document.getElementById('ap-rem').textContent = 'Allocated Quantity: ' + activeAlloc.remQty + ' Units (' + activeAlloc.items + ')';
      }

      document.getElementById('alloc-preview-box').style.display = 'block';
      validateDistBalance();
    }

    function validateDistBalance() {
      if (!activeAlloc) return;
      if (activeAlloc.type === 'monetary') {
        const amt = parseFloat(document.getElementById('dist-amt-input').value || 0);
        const rem = activeAlloc.remAmt - amt;
        const msg = document.getElementById('dist-amt-msg');
        if (rem < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Distributed amount exceeds allocated balance by ₱' + Math.abs(rem).toFixed(2) + '!</span>';
        } else {
          msg.textContent = 'Remaining balance after this distribution: ₱' + rem.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
      } else {
        const qty = parseInt(document.getElementById('dist-qty-input').value || 0);
        const remQty = activeAlloc.remQty - qty;
        const msg = document.getElementById('dist-qty-msg');
        if (remQty < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Distributed quantity exceeds allocated balance by ' + Math.abs(remQty) + ' units!</span>';
        } else {
          msg.textContent = 'Remaining quantity after this distribution: ' + remQty + ' units';
        }
      }
    }

    function submitDistForm(e) {
      e.preventDefault();
      const form = document.getElementById('dist-form');
      const formData = new FormData(form);
      const btn = document.getElementById('btn-save-dist');
      const errBox = document.getElementById('dist-error');

      btn.disabled = true;
      btn.textContent = 'Recording Distribution...';
      errBox.style.display = 'none';

      fetch('<?= url('/admin/damayan/charity/distributions/save') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Complete Distribution';
        if (res.success) {
          closeDistModal();
          window.location.reload();
        } else {
          errBox.textContent = res.error || 'Failed to record distribution.';
          errBox.style.display = 'block';
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Complete Distribution';
        errBox.textContent = 'Server communication error. Please try again.';
        errBox.style.display = 'block';
      });
    }

    function viewDistribution(id) {
      document.getElementById('dist-view-modal').style.display = 'flex';
      const body = document.getElementById('dv-body');
      body.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Loading distribution record...</div>';

      fetch('<?= url('/admin/damayan/charity/distributions/detail') ?>?id=' + id)
        .then(r => r.json())
        .then(res => {
          if (!res.success) {
            body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">' + (res.error || 'Unable to load record.') + '</div>';
            return;
          }
          const d = res.distribution;
          document.getElementById('dv-title').textContent = 'Distribution — ' + d.distribution_ref;

          let proofHtml = '<span style="color:var(--text-muted);">No supporting document uploaded.</span>';
          if (d.supporting_document) {
            proofHtml = `<a href="<?= asset('') ?>${d.supporting_document}" target="_blank" class="btn-action-sm" style="color:var(--primary); font-weight:700;">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>
              Open Attached Document / Acknowledgment
            </a>`;
          }

          body.innerHTML = `
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; background:#f9fafb; padding:18px; border-radius:12px; border:1px solid var(--border);">
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Receiving Department</div>
                <div style="margin-top:4px;"><span class="dept-tag ${d.receiving_department === 'Damayan' ? 'dept-damayan' : 'dept-dawah'}" style="font-size:0.85rem;">${d.receiving_department}</span></div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">Date: <strong>${d.distribution_date}</strong></div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Handed Out Resources</div>
                <div style="font-size:1.15rem; font-weight:800; color:var(--primary-dark);">${d.items_summary}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Allocation: <strong>${d.allocation_ref}</strong></div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Beneficiary / Recipient</div>
                <div style="font-size:1.05rem; font-weight:800; color:var(--text-main);">${d.received_by}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Distributed By: <strong>${d.distributed_by}</strong></div>
              </div>
            </div>

            <div style="margin-bottom:18px;">
              <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px;">Distribution Purpose</div>
              <div style="padding:12px; background:white; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;">
                ${d.purpose}
              </div>
            </div>

            ${d.remarks ? `<div style="margin-bottom:18px;">
              <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px;">Remarks</div>
              <div style="padding:10px 12px; background:#fff; border:1px solid var(--border); border-radius:8px; font-size:0.85rem; color:#555;">${d.remarks}</div>
            </div>` : ''}

            <div>
              <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Supporting Document / Signed Form</div>
              <div style="padding:12px; background:white; border:1px solid var(--border); border-radius:8px;">
                ${proofHtml}
              </div>
            </div>
          `;
        })
        .catch(err => {
          body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">Network error loading details.</div>';
        });
    }

    function closeDistViewModal() {
      document.getElementById('dist-view-modal').style.display = 'none';
    }
  </script>
</body>
</html>
