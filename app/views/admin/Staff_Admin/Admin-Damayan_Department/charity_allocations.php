<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Donation Allocation</title>
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
    .insight-card.total-alloc::after { background: var(--accent); }
    .insight-card.damayan::after { background: var(--primary); }
    .insight-card.dawah::after { background: #c79a2b; }
    .insight-card.pending::after { background: #8b2e2e; }

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
      width: 240px;
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
    .badge-pending { background: rgba(139, 46, 46, 0.12); color: #8b2e2e; }
    .badge-partial { background: rgba(199, 154, 43, 0.12); color: #a37817; }
    .badge-distributed { background: rgba(47, 138, 96, 0.12); color: #176b45; }
    .badge-cancelled { background: rgba(111, 127, 120, 0.12); color: #6f7f78; }

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
    .form-control:focus {
      border-color: var(--primary);
    }
  </style>
  <link rel="stylesheet" href="<?= asset('css/charity.css') ?>?v=<?= time() ?>" />
</head>
<body class="charity-module">
  <div class="app-wrapper">
    <!-- Damayan Sidebar -->
    <?php 
      $active_page = 'charity_allocations';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Donation Allocation</div>
          <div class="top-bar-subtitle">Assign available monetary and in-kind resources to Damayan and Da'wah departments</div>
        </div>
        <div class="top-bar-actions">
          <button onclick="openNewAllocModal()" class="btn-topbar" style="background:var(--primary); color:white; border:none; border-radius:8px; padding:9px 18px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> New Allocation
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
          <span class="current">Donation Allocation</span>
        </div>

        <!-- Summary Insights -->
        <div class="admin-insights">
          <div class="insight-card total-alloc">
            <div class="insight-label">Total Allocated</div>
            <div class="insight-value" style="color:var(--accent);">₱<?= number_format($stats['allocated']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['allocated']['in_kind'] ?> units allocated</div>
          </div>
          <div class="insight-card damayan">
            <div class="insight-label">Allocated to Damayan</div>
            <div class="insight-value" style="color:var(--primary);">₱<?= number_format($stats['allocated']['damayan_monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['allocated']['damayan_in_kind'] ?> burial/welfare goods</div>
          </div>
          <div class="insight-card dawah">
            <div class="insight-label">Allocated to Da'wah</div>
            <div class="insight-value" style="color:#a37817;">₱<?= number_format($stats['allocated']['dawah_monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['allocated']['dawah_in_kind'] ?> outreach kits</div>
          </div>
          <div class="insight-card pending">
            <div class="insight-label">Awaiting Distribution</div>
            <div class="insight-value" style="color:#8b2e2e;">₱<?= number_format($stats['pending']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= $stats['pending']['in_kind'] ?> undistributed items</div>
          </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="<?= url('/admin/damayan/charity/allocations') ?>" class="filter-bar">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <input type="text" name="search" class="search-input" placeholder="Search alloc ref, donation, purpose..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />

            <select name="department" class="filter-select" onchange="this.form.submit()">
              <option value="">All Receiving Depts</option>
              <option value="Damayan" <?= ($filters['department'] ?? '') === 'Damayan' ? 'selected' : '' ?>>Damayan</option>
              <option value="Dawah" <?= ($filters['department'] ?? '') === 'Dawah' ? 'selected' : '' ?>>Da'wah</option>
            </select>

            <select name="status" class="filter-select" onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="Pending Distribution" <?= ($filters['status'] ?? '') === 'Pending Distribution' ? 'selected' : '' ?>>Pending Distribution</option>
              <option value="Partially Distributed" <?= ($filters['status'] ?? '') === 'Partially Distributed' ? 'selected' : '' ?>>Partially Distributed</option>
              <option value="Distributed" <?= ($filters['status'] ?? '') === 'Distributed' ? 'selected' : '' ?>>Distributed</option>
              <option value="Cancelled" <?= ($filters['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">Date:</span>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <span style="color:var(--text-muted);">-</span>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <button type="submit" class="btn-action-sm btn-action-primary">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['department']) || !empty($filters['status']) || !empty($filters['date_from'])): ?>
              <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="btn-action-sm">Reset</a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Allocations Data Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);"><path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/></svg>
              Allocations Registry (<?= count($allocations) ?>)
            </h6>
            <div style="font-size:0.75rem; color:var(--text-muted);">
              Allocated resources tracked separately from final distribution
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Alloc Ref</th>
                    <th>Source Donation</th>
                    <th>Receiving Dept</th>
                    <th>Allocated Amount / Qty</th>
                    <th>Remaining to Distribute</th>
                    <th>Purpose</th>
                    <th>Date</th>
                    <th>Allocated By</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($allocations)): ?>
                    <tr>
                      <td colspan="10" style="text-align:center; padding:40px; color:var(--text-muted);">
                        No allocations found matching the current search parameters.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($allocations as $a): ?>
                      <tr>
                        <td>
                          <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($a['allocation_ref']) ?></strong>
                        </td>
                        <td>
                          <div style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.85rem;"><?= htmlspecialchars($a['donation_ref']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($a['donor_name']) ?></div>
                        </td>
                        <td>
                          <span class="dept-tag <?= $a['receiving_department'] === 'Damayan' ? 'dept-damayan' : 'dept-dawah' ?>">
                            <?= htmlspecialchars($a['receiving_department']) ?>
                          </span>
                        </td>
                        <td>
                          <?php if ($a['allocation_type'] === 'monetary'): ?>
                            <strong style="color:var(--primary); font-size:0.95rem;">₱<?= number_format($a['allocated_amount'], 2) ?></strong>
                          <?php else: ?>
                            <strong style="color:var(--accent); font-size:0.95rem;"><?= htmlspecialchars($a['allocated_quantity']) ?> Units</strong>
                            <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($a['item_description'] ?: 'In-Kind') ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($a['allocation_type'] === 'monetary'): ?>
                            <span style="font-weight:700; color:<?= $a['remaining_allocated_amount'] > 0 ? '#8b2e2e' : 'var(--success)' ?>;">
                              ₱<?= number_format($a['remaining_allocated_amount'], 2) ?>
                            </span>
                          <?php else: ?>
                            <span style="font-weight:700; color:<?= $a['remaining_allocated_quantity'] > 0 ? '#8b2e2e' : 'var(--success)' ?>;">
                              <?= $a['remaining_allocated_quantity'] ?> Units
                            </span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div style="max-width:220px; font-weight:600; font-size:0.85rem;"><?= htmlspecialchars($a['purpose']) ?></div>
                          <?php if (!empty($a['remarks'])): ?>
                            <div style="font-size:0.72rem; color:var(--text-muted);"><?= htmlspecialchars($a['remarks']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                          <?= date('M d, Y', strtotime($a['allocation_date'])) ?>
                        </td>
                        <td style="font-size:0.8rem;">
                          <?= htmlspecialchars($a['allocated_by']) ?>
                        </td>
                        <td>
                          <?php 
                            $stClass = 'badge-pending';
                            if ($a['status'] === 'Partially Distributed') $stClass = 'badge-partial';
                            elseif ($a['status'] === 'Distributed') $stClass = 'badge-distributed';
                            elseif ($a['status'] === 'Cancelled') $stClass = 'badge-cancelled';
                          ?>
                          <span class="badge-status <?= $stClass ?>"><?= htmlspecialchars($a['status']) ?></span>
                        </td>
                        <td style="text-align:right;">
                          <div style="display:inline-flex; gap:6px;">
                            <button onclick="viewAllocation(<?= $a['id'] ?>)" class="btn-action-sm" title="View Allocation & Linked Distributions">
                              View
                            </button>
                            <?php if ($a['status'] === 'Pending Distribution' && (float)$a['distributed_amount'] == 0 && (int)$a['distributed_quantity'] == 0): ?>
                              <button onclick="cancelAllocationPrompt(<?= $a['id'] ?>, '<?= htmlspecialchars($a['allocation_ref']) ?>')" class="btn-action-sm" style="color:#8b2e2e; border-color:#f0d0cb;" title="Cancel & Return Balance to Donation">
                                Cancel
                              </button>
                            <?php endif; ?>
                            <?php if (in_array($a['status'], ['Pending Distribution', 'Partially Distributed'])): ?>
                              <a href="<?= url('/admin/damayan/charity/distributions?donation_ref=' . urlencode($a['donation_ref'])) ?>" class="btn-action-sm btn-action-primary" title="Record distribution for this allocation">
                                Distribute
                              </a>
                            <?php endif; ?>
                          </div>
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
  <!-- MODAL: CREATE ALLOCATION -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="alloc-modal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title">New Resource Allocation</h5>
        <button onclick="closeAllocModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <form id="alloc-form" onsubmit="submitAllocForm(event)">
        <div class="modal-body">
          <div id="alloc-error" style="display:none; padding:12px 16px; background:#fdf0ed; color:#8b2e2e; border-radius:8px; margin-bottom:16px; font-weight:600; font-size:0.85rem;"></div>

          <!-- Select Donation with Available Balance -->
          <div class="form-group">
            <label>Select Source Donation (Available Balance) <span style="color:#8b2e2e;">*</span></label>
            <select name="donation_id" id="alloc-donation-select" required class="form-control" onchange="onSourceDonationSelected(this)">
              <option value="">-- Choose an eligible donation lot --</option>
              <?php foreach ($availableDonations as $av): ?>
                <?php 
                  $label = $av['donation_ref'] . ' — ' . $av['donor_name'] . ' (';
                  if ($av['donation_type'] === 'monetary') {
                    $label .= 'Available: ₱' . number_format($av['available_amount'], 2);
                  } else {
                    $label .= 'Available: ' . $av['available_quantity'] . ' Units - ' . ($av['item_description'] ?: 'In-Kind');
                  }
                  $label .= ')';
                ?>
                <option value="<?= $av['id'] ?>" 
                        data-type="<?= $av['donation_type'] ?>"
                        data-avail-amt="<?= $av['available_amount'] ?>"
                        data-avail-qty="<?= $av['available_quantity'] ?>"
                        data-item-desc="<?= htmlspecialchars($av['item_description'] ?? '') ?>"
                        data-donor="<?= htmlspecialchars($av['donor_name']) ?>"
                        data-ref="<?= htmlspecialchars($av['donation_ref']) ?>">
                  <?= $label ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Donation Info Preview Box -->
          <div id="source-preview" style="display:none; background:#f9fafb; border:1px solid var(--border); border-radius:10px; padding:14px; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span id="prev-donor" style="font-weight:700; color:var(--primary-dark);"></span>
              <span id="prev-avail" style="font-weight:800; color:var(--success);"></span>
            </div>
          </div>

          <!-- Receiving Department -->
          <div class="form-group">
            <label>Receiving Department <span style="color:#8b2e2e;">*</span></label>
            <select name="receiving_department" required class="form-control">
              <option value="Damayan">Damayan Department (Funeral, Burial & Social Welfare)</option>
              <option value="Dawah">Da'wah Department (Outreach, New Muslims, Youth & Education)</option>
            </select>
          </div>

          <!-- Allocation Value (Monetary / Qty) -->
          <div id="alloc-amt-group" class="form-group">
            <label>Amount to Allocate (₱) <span style="color:#8b2e2e;">*</span></label>
            <input type="number" step="0.01" min="1" name="allocated_amount" id="alloc-amt-input" class="form-control" placeholder="0.00" oninput="validateAllocBalance()" />
            <div id="alloc-amt-msg" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <div id="alloc-qty-group" class="form-group" style="display:none;">
            <label>Quantity to Allocate <span style="color:#8b2e2e;">*</span></label>
            <input type="number" min="1" name="allocated_quantity" id="alloc-qty-input" class="form-control" placeholder="e.g. 20" oninput="validateAllocBalance()" />
            <div id="alloc-qty-msg" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <!-- Purpose -->
          <div class="form-group">
            <label>Purpose of Allocation <span style="color:#8b2e2e;">*</span></label>
            <input type="text" name="purpose" required class="form-control" placeholder="e.g. Burial Shroud & Grave Subsidy / Convert Welcome Packages" />
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <!-- Allocation Date -->
            <div class="form-group">
              <label>Allocation Date <span style="color:#8b2e2e;">*</span></label>
              <input type="date" name="allocation_date" required value="<?= date('Y-m-d') ?>" class="form-control" />
            </div>
            <!-- Allocated By -->
            <div class="form-group">
              <label>Allocated By (Staff) <span style="color:#8b2e2e;">*</span></label>
              <input type="text" name="allocated_by" required value="<?= htmlspecialchars($_SESSION['name'] ?? 'Damayan Staff') ?>" class="form-control" />
            </div>
          </div>

          <!-- Remarks -->
          <div class="form-group" style="margin-bottom:0;">
            <label>Remarks / Notes</label>
            <textarea name="remarks" rows="2" class="form-control" placeholder="Any instructions or constraints for this allocation..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" onclick="closeAllocModal()" class="btn-action-sm" style="padding:9px 18px;">Cancel</button>
          <button type="submit" id="btn-save-alloc" class="btn-action-sm btn-action-primary" style="padding:9px 24px; font-weight:700;">
            Confirm & Save Allocation
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════ -->
  <!-- MODAL: VIEW ALLOCATION DETAILS -->
  <!-- ═══════════════════════════════════════════════════════════════ -->
  <div id="alloc-view-modal" class="modal-backdrop">
    <div class="modal-box" style="max-width:720px;">
      <div class="modal-header">
        <h5 class="modal-title" id="av-title">Allocation Details</h5>
        <button onclick="closeAllocViewModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <div class="modal-body" id="av-body">
        <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading allocation details...</div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeAllocViewModal()" class="btn-action-sm" style="padding:8px 18px;">Close</button>
      </div>
    </div>
  </div>

  <script>
    let activeSourceDonation = null;

    function openNewAllocModal() {
      document.getElementById('alloc-form').reset();
      document.getElementById('source-preview').style.display = 'none';
      document.getElementById('alloc-error').style.display = 'none';
      document.getElementById('alloc-amt-group').style.display = 'block';
      document.getElementById('alloc-qty-group').style.display = 'none';
      activeSourceDonation = null;
      document.getElementById('alloc-modal').style.display = 'flex';
    }

    function closeAllocModal() {
      document.getElementById('alloc-modal').style.display = 'none';
    }

    function onSourceDonationSelected(selectEl) {
      const opt = selectEl.options[selectEl.selectedIndex];
      if (!opt || !opt.value) {
        document.getElementById('source-preview').style.display = 'none';
        activeSourceDonation = null;
        return;
      }

      activeSourceDonation = {
        id: opt.value,
        type: opt.getAttribute('data-type'),
        availAmt: parseFloat(opt.getAttribute('data-avail-amt') || 0),
        availQty: parseInt(opt.getAttribute('data-avail-qty') || 0),
        donor: opt.getAttribute('data-donor'),
        itemDesc: opt.getAttribute('data-item-desc')
      };

      document.getElementById('prev-donor').textContent = 'Donor: ' + activeSourceDonation.donor;
      const prevAvail = document.getElementById('prev-avail');

      if (activeSourceDonation.type === 'monetary') {
        document.getElementById('alloc-amt-group').style.display = 'block';
        document.getElementById('alloc-qty-group').style.display = 'none';
        document.getElementById('alloc-amt-input').value = activeSourceDonation.availAmt;
        document.getElementById('alloc-amt-input').max = activeSourceDonation.availAmt;
        prevAvail.textContent = 'Available: ₱' + activeSourceDonation.availAmt.toLocaleString('en-US', {minimumFractionDigits: 2});
      } else {
        document.getElementById('alloc-amt-group').style.display = 'none';
        document.getElementById('alloc-qty-group').style.display = 'block';
        document.getElementById('alloc-qty-input').value = activeSourceDonation.availQty;
        document.getElementById('alloc-qty-input').max = activeSourceDonation.availQty;
        prevAvail.textContent = 'Available: ' + activeSourceDonation.availQty + ' Units (' + activeSourceDonation.itemDesc + ')';
      }

      document.getElementById('source-preview').style.display = 'block';
      validateAllocBalance();
    }

    function validateAllocBalance() {
      if (!activeSourceDonation) return;
      if (activeSourceDonation.type === 'monetary') {
        const amt = parseFloat(document.getElementById('alloc-amt-input').value || 0);
        const rem = activeSourceDonation.availAmt - amt;
        const msg = document.getElementById('alloc-amt-msg');
        if (rem < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Requested amount exceeds available balance by ₱' + Math.abs(rem).toFixed(2) + '!</span>';
        } else {
          msg.textContent = 'Remaining available balance after allocation: ₱' + rem.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
      } else {
        const qty = parseInt(document.getElementById('alloc-qty-input').value || 0);
        const remQty = activeSourceDonation.availQty - qty;
        const msg = document.getElementById('alloc-qty-msg');
        if (remQty < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Requested quantity exceeds available inventory by ' + Math.abs(remQty) + ' units!</span>';
        } else {
          msg.textContent = 'Remaining available quantity after allocation: ' + remQty + ' units';
        }
      }
    }

    function submitAllocForm(e) {
      e.preventDefault();
      const form = document.getElementById('alloc-form');
      const formData = new FormData(form);
      const btn = document.getElementById('btn-save-alloc');
      const errBox = document.getElementById('alloc-error');

      btn.disabled = true;
      btn.textContent = 'Processing Allocation...';
      errBox.style.display = 'none';

      fetch('<?= url('/admin/damayan/charity/allocations/save') ?>', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Save Allocation';
        if (res.success) {
          closeAllocModal();
          window.location.reload();
        } else {
          errBox.textContent = res.error || 'Failed to complete allocation.';
          errBox.style.display = 'block';
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Confirm & Save Allocation';
        errBox.textContent = 'Server communication error. Please try again.';
        errBox.style.display = 'block';
      });
    }

    function cancelAllocationPrompt(id, ref) {
      const reason = prompt('Are you sure you want to cancel allocation ' + ref + '?\n\nPlease enter the cancellation reason (the allocated balance will be restored to the source donation):');
      if (reason === null) return;

      const fd = new FormData();
      fd.append('id', id);
      fd.append('reason', reason);

      fetch('<?= url('/admin/damayan/charity/allocations/cancel') ?>', {
        method: 'POST',
        body: fd
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          window.location.reload();
        } else {
          alert(res.error || 'Failed to cancel allocation.');
        }
      });
    }

    function viewAllocation(id) {
      document.getElementById('alloc-view-modal').style.display = 'flex';
      const body = document.getElementById('av-body');
      body.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Loading allocation details...</div>';

      fetch('<?= url('/admin/damayan/charity/allocations/detail') ?>?id=' + id)
        .then(r => r.json())
        .then(res => {
          if (!res.success) {
            body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">' + (res.error || 'Unable to load allocation details.') + '</div>';
            return;
          }
          const a = res.allocation;
          document.getElementById('av-title').textContent = 'Allocation — ' + a.allocation_ref;

          let valDisplay = a.allocation_type === 'monetary'
            ? '₱' + parseFloat(a.allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2})
            : a.allocated_quantity + ' Units (' + (a.item_description || '') + ')';

          let remDisplay = a.allocation_type === 'monetary'
            ? '₱' + parseFloat(a.remaining_allocated_amount).toLocaleString('en-US', {minimumFractionDigits: 2})
            : a.remaining_allocated_quantity + ' Units';

          let distRows = '';
          if (a.distributions && a.distributions.length > 0) {
            a.distributions.forEach(dist => {
              distRows += `<tr>
                <td><strong style="color:var(--primary); font-family:monospace;">${dist.distribution_ref}</strong></td>
                <td><strong>${dist.items_summary}</strong></td>
                <td>${dist.received_by}</td>
                <td>${dist.distribution_date}</td>
                <td>${dist.distributed_by}</td>
              </tr>`;
            });
          } else {
            distRows = '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:16px;">No distributions recorded under this allocation yet.</td></tr>';
          }

          body.innerHTML = `
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; background:#f9fafb; padding:18px; border-radius:12px; border:1px solid var(--border);">
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Receiving Department</div>
                <div style="margin-top:4px;"><span class="dept-tag ${a.receiving_department === 'Damayan' ? 'dept-damayan' : 'dept-dawah'}" style="font-size:0.85rem;">${a.receiving_department}</span></div>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:4px;">Allocated By: <strong>${a.allocated_by}</strong></div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Total Allocated</div>
                <div style="font-size:1.15rem; font-weight:800; color:var(--primary);">${valDisplay}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Source: <strong>${a.donation_ref}</strong> (${a.donor_name})</div>
              </div>
              <div>
                <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Awaiting Distribution</div>
                <div style="font-size:1.15rem; font-weight:800; color:#8b2e2e;">${remDisplay}</div>
                <div style="font-size:0.8rem; color:var(--text-muted);">Status: <strong>${a.status}</strong></div>
              </div>
            </div>

            <div style="margin-bottom:20px;">
              <h6 style="font-size:0.88rem; font-weight:700; margin-bottom:6px; color:var(--primary-dark);">Purpose of Allocation</h6>
              <div style="padding:12px; background:white; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;">
                ${a.purpose}
                ${a.remarks ? '<div style="font-size:0.8rem; color:var(--text-muted); margin-top:6px; border-top:1px dashed var(--border); padding-top:6px;"><strong>Remarks:</strong> ' + a.remarks + '</div>' : ''}
              </div>
            </div>

            <div>
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <h6 style="font-size:0.88rem; font-weight:700; margin:0; color:var(--primary-dark);">Executed Distributions</h6>
                ${a.status !== 'Distributed' && a.status !== 'Cancelled' ? `<a href="<?= url('/admin/damayan/charity/distributions?donation_ref=') ?>${a.donation_ref}" class="btn-action-sm btn-action-primary" style="font-size:0.72rem;">+ Distribute Resources</a>` : ''}
              </div>
              <div class="table-wrapper" style="border:1px solid var(--border); border-radius:10px;">
                <table class="data-table" style="font-size:0.82rem;">
                  <thead>
                    <tr>
                      <th>Dist Ref</th>
                      <th>Resources Handed Out</th>
                      <th>Authorized Recipient</th>
                      <th>Date</th>
                      <th>Staff</th>
                    </tr>
                  </thead>
                  <tbody>${distRows}</tbody>
                </table>
              </div>
            </div>
          `;
        })
        .catch(err => {
          body.innerHTML = '<div style="color:#8b2e2e; padding:20px;">Network error loading details.</div>';
        });
    }

    function closeAllocViewModal() {
      document.getElementById('alloc-view-modal').style.display = 'none';
    }
  </script>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
</body>
</html>
