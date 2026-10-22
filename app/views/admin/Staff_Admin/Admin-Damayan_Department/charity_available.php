<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Available Donations</title>
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
    .insight-card.avail-cash::after { background: var(--success); }
    .insight-card.avail-goods::after { background: var(--accent); }
    .insight-card.eligible::after { background: var(--primary); }

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
    .badge-available { background: rgba(47, 138, 96, 0.12); color: #176b45; }
    .badge-allocated { background: rgba(199, 154, 43, 0.12); color: #a37817; }

    .btn-action-sm {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
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
      max-width: 640px;
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
      $active_page = 'charity_available';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Available Donations & Resources</div>
          <div class="top-bar-subtitle">Monitor unallocated funds and physical goods eligible for allocation to Damayan or Da'wah</div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/damayan/charity/allocations') ?>" class="btn-topbar" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; background:var(--accent); color:white; border-radius:8px; padding:9px 18px; font-weight:700; font-size:0.85rem;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/></svg> Go to Donation Allocation
          </a>
        </div>
      </div>

      <div class="page-body">
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= url('/admin/damayan/charity') ?>">Charity Programs</a>
          <span class="sep">›</span>
          <span class="current">Available Donations</span>
        </div>

        <!-- Summary Insights -->
        <div class="admin-insights">
          <div class="insight-card avail-cash">
            <div class="insight-label">Remaining Available Funds</div>
            <div class="insight-value" style="color:var(--success);">₱<?= number_format($stats['available']['monetary'], 2) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Total Received minus Total Allocated</div>
          </div>
          <div class="insight-card avail-goods">
            <div class="insight-label">Available Physical Goods</div>
            <div class="insight-value" style="color:var(--accent);"><?= number_format($stats['available']['in_kind']) ?> Units</div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Unallocated relief & welfare packages</div>
          </div>
          <div class="insight-card eligible">
            <div class="insight-label">Eligible Donation Lots</div>
            <div class="insight-value"><?= count($availableDonations) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted);">Active with positive balance</div>
          </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="<?= url('/admin/damayan/charity/available') ?>" class="filter-bar">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <input type="text" name="search" class="search-input" placeholder="Search ref, donor, or items..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
            
            <select name="type" class="filter-select" onchange="this.form.submit()">
              <option value="">All Resource Types</option>
              <option value="monetary" <?= ($filters['donation_type'] ?? '') === 'monetary' ? 'selected' : '' ?>>Monetary Only</option>
              <option value="in-kind" <?= ($filters['donation_type'] ?? '') === 'in-kind' ? 'selected' : '' ?>>In-Kind (Physical) Only</option>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">Received:</span>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <span style="color:var(--text-muted);">-</span>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="filter-select" style="padding:6px 10px;" />
            <button type="submit" class="btn-action-sm btn-action-primary">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['donation_type']) || !empty($filters['date_from'])): ?>
              <a href="<?= url('/admin/damayan/charity/available') ?>" class="btn-action-sm">Reset</a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Table of Available Resources -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--success);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              Available Resources for Allocation (<?= count($availableDonations) ?>)
            </h6>
            <div style="font-size:0.75rem; color:var(--text-muted);">
              Balances calculated in real-time (Received - Allocated)
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Donation Ref</th>
                    <th>Donor / Sponsor</th>
                    <th>Type</th>
                    <th>Original Received</th>
                    <th>Previously Allocated</th>
                    <th>Remaining Available</th>
                    <th>Date Received</th>
                    <th>Availability Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($availableDonations)): ?>
                    <tr>
                      <td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">
                        No available unallocated donations found. All received resources have been fully allocated.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($availableDonations as $d): ?>
                      <tr>
                        <td>
                          <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($d['donation_ref']) ?></strong>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($d['donor_name']) ?></div>
                          <?php if (!empty($d['donor_address'])): ?>
                            <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($d['donor_address']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge-status" style="background:#f0f4f2; color:var(--text-main);">
                            <?= ucfirst($d['donation_type']) ?>
                          </span>
                        </td>
                        <td>
                          <?php if ($d['donation_type'] === 'monetary'): ?>
                            <strong>₱<?= number_format($d['amount'], 2) ?></strong>
                          <?php else: ?>
                            <strong><?= htmlspecialchars($d['quantity']) ?> Units</strong>
                            <div style="font-size:0.75rem; color:var(--text-muted);"><?= htmlspecialchars($d['item_description']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($d['donation_type'] === 'monetary'): ?>
                            <span style="color:var(--text-muted); font-weight:600;">₱<?= number_format($d['allocated_amount'], 2) ?></span>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-weight:600;"><?= $d['allocated_quantity'] ?> Units</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($d['donation_type'] === 'monetary'): ?>
                            <strong style="font-size:1.05rem; color:var(--success);">
                              ₱<?= number_format($d['available_amount'], 2) ?>
                            </strong>
                          <?php else: ?>
                            <strong style="font-size:1.05rem; color:var(--accent);">
                              <?= $d['available_quantity'] ?> Units Available
                            </strong>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-muted);">
                          <?= date('M d, Y', strtotime($d['date_received'])) ?>
                        </td>
                        <td>
                          <?php 
                            $stClass = 'badge-available';
                            if ($d['status'] === 'Partially Allocated') $stClass = 'badge-allocated';
                          ?>
                          <span class="badge-status <?= $stClass ?>"><?= htmlspecialchars($d['status']) ?></span>
                        </td>
                        <td style="text-align:right;">
                          <div style="display:inline-flex; gap:6px;">
                            <button onclick="quickAllocate(<?= htmlspecialchars(json_encode($d)) ?>)" class="btn-action-sm btn-action-primary" title="Allocate this donation">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/></svg>
                              Allocate
                            </button>
                            <a href="<?= url('/admin/damayan/charity/donations?search=' . urlencode($d['donation_ref'])) ?>" class="btn-action-sm" title="Open donation record">
                              View Record
                            </a>
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

  <!-- Quick Allocation Modal from Available Page -->
  <div id="quick-alloc-modal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h5 class="modal-title" id="alloc-modal-title">Allocate Donation</h5>
        <button onclick="closeQuickAllocModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
      </div>
      <form id="quick-alloc-form" onsubmit="submitQuickAlloc(event)">
        <input type="hidden" name="donation_id" id="qa-donation-id" />
        <div class="modal-body">
          <div id="qa-error" style="display:none; padding:12px 16px; background:#fdf0ed; color:#8b2e2e; border-radius:8px; margin-bottom:16px; font-weight:600; font-size:0.85rem;"></div>

          <div style="background:#f9fafb; border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Source Donation</span>
              <span id="qa-ref" style="font-family:monospace; font-weight:800; color:var(--primary-dark);"></span>
            </div>
            <div id="qa-donor" style="font-weight:700; font-size:1rem; margin-bottom:4px;"></div>
            <div id="qa-available-box" style="font-size:0.9rem; color:var(--success); font-weight:700;"></div>
          </div>

          <!-- Receiving Department -->
          <div class="form-group">
            <label>Receiving Department <span style="color:#8b2e2e;">*</span></label>
            <select name="receiving_department" required class="form-control">
              <option value="Damayan">Damayan (Burial, Funeral & Social Welfare)</option>
              <option value="Dawah">Da'wah (Outreach, New Muslims & Islamic Education)</option>
            </select>
          </div>

          <!-- Allocation Value (Monetary / Qty) -->
          <div id="qa-amt-group" class="form-group">
            <label>Amount to Allocate (₱) <span style="color:#8b2e2e;">*</span></label>
            <input type="number" step="0.01" min="1" name="allocated_amount" id="qa-amt-input" class="form-control" placeholder="0.00" oninput="calculateRemainingAlloc()" />
            <div id="qa-amt-remaining" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <div id="qa-qty-group" class="form-group" style="display:none;">
            <label>Quantity to Allocate <span style="color:#8b2e2e;">*</span></label>
            <input type="number" min="1" name="allocated_quantity" id="qa-qty-input" class="form-control" placeholder="e.g. 10" oninput="calculateRemainingAlloc()" />
            <div id="qa-qty-remaining" style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;"></div>
          </div>

          <!-- Purpose of Allocation -->
          <div class="form-group">
            <label>Purpose of Allocation <span style="color:#8b2e2e;">*</span></label>
            <input type="text" name="purpose" required class="form-control" placeholder="e.g. Indigent Muslim Funeral Assistance / New Muslim Relief Food Packs" />
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <!-- Allocation Date -->
            <div class="form-group">
              <label>Allocation Date <span style="color:#8b2e2e;">*</span></label>
              <input type="date" name="allocation_date" required value="<?= date('Y-m-d') ?>" class="form-control" />
            </div>
            <!-- Allocated By -->
            <div class="form-group">
              <label>Allocated By <span style="color:#8b2e2e;">*</span></label>
              <input type="text" name="allocated_by" required value="<?= htmlspecialchars($_SESSION['name'] ?? 'Damayan Staff') ?>" class="form-control" />
            </div>
          </div>

          <!-- Remarks -->
          <div class="form-group" style="margin-bottom:0;">
            <label>Remarks</label>
            <textarea name="remarks" rows="2" class="form-control" placeholder="Optional notes for receiving department..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" onclick="closeQuickAllocModal()" class="btn-action-sm" style="padding:9px 18px;">Cancel</button>
          <button type="submit" id="qa-submit-btn" class="btn-action-sm btn-action-primary" style="padding:9px 24px; font-weight:700;">
            Confirm & Save Allocation
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let currentDonation = null;

    function quickAllocate(d) {
      currentDonation = d;
      document.getElementById('qa-donation-id').value = d.id;
      document.getElementById('qa-ref').textContent = d.donation_ref;
      document.getElementById('qa-donor').textContent = d.donor_name;
      document.getElementById('qa-error').style.display = 'none';

      if (d.donation_type === 'monetary') {
        document.getElementById('qa-amt-group').style.display = 'block';
        document.getElementById('qa-qty-group').style.display = 'none';
        document.getElementById('qa-amt-input').value = d.available_amount;
        document.getElementById('qa-amt-input').max = d.available_amount;
        document.getElementById('qa-available-box').textContent = 'Available Balance: ₱' + parseFloat(d.available_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
        calculateRemainingAlloc();
      } else {
        document.getElementById('qa-amt-group').style.display = 'none';
        document.getElementById('qa-qty-group').style.display = 'block';
        document.getElementById('qa-qty-input').value = d.available_quantity;
        document.getElementById('qa-qty-input').max = d.available_quantity;
        document.getElementById('qa-available-box').textContent = 'Available Quantity: ' + d.available_quantity + ' Units (' + (d.item_description || '') + ')';
        calculateRemainingAlloc();
      }

      document.getElementById('quick-alloc-modal').style.display = 'flex';
    }

    function calculateRemainingAlloc() {
      if (!currentDonation) return;
      if (currentDonation.donation_type === 'monetary') {
        const inputVal = parseFloat(document.getElementById('qa-amt-input').value || 0);
        const rem = parseFloat(currentDonation.available_amount) - inputVal;
        const msg = document.getElementById('qa-amt-remaining');
        if (rem < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Exceeds available balance by ₱' + Math.abs(rem).toFixed(2) + '!</span>';
        } else {
          msg.textContent = 'Remaining available balance after this allocation: ₱' + rem.toLocaleString('en-US', {minimumFractionDigits: 2});
        }
      } else {
        const inputQty = parseInt(document.getElementById('qa-qty-input').value || 0);
        const remQty = parseInt(currentDonation.available_quantity) - inputQty;
        const msg = document.getElementById('qa-qty-remaining');
        if (remQty < 0) {
          msg.innerHTML = '<span style="color:#8b2e2e; font-weight:700;">Exceeds available quantity by ' + Math.abs(remQty) + ' units!</span>';
        } else {
          msg.textContent = 'Remaining available quantity after this allocation: ' + remQty + ' units';
        }
      }
    }

    function closeQuickAllocModal() {
      document.getElementById('quick-alloc-modal').style.display = 'none';
    }

    function submitQuickAlloc(e) {
      e.preventDefault();
      const form = document.getElementById('quick-alloc-form');
      const formData = new FormData(form);
      const btn = document.getElementById('qa-submit-btn');
      const errBox = document.getElementById('qa-error');

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
          closeQuickAllocModal();
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
  </script>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
</body>
</html>
