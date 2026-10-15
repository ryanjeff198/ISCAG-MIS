<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Records</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    .insight-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .insight-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--damayan-accent);
    }
    .insight-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }

    /* Filter & Search Bar */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 6px 14px;
      gap: 8px;
      min-width: 280px;
    }
    .search-box input {
      border: none;
      outline: none;
      font-size: 0.85rem;
      width: 100%;
      font-family: inherit;
    }
    .search-box svg {
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
    }

    .filter-pills {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
    }
    .filter-pill {
      font-size: 0.78rem;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
      text-decoration: none;
      color: var(--text-muted);
      background: white;
      border: 1px solid var(--border);
      transition: all 0.2s;
      white-space: nowrap;
      cursor: pointer;
    }
    .filter-pill:hover, .filter-pill.active {
      background: var(--damayan-light);
      color: var(--damayan-accent);
      border-color: var(--damayan-accent);
    }

    .mini-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 6px;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .mini-badge.pending { background: #f3f4f6; color: #6b7280; }
    .mini-badge.active { background: #eff6ff; color: #1d4ed8; }
    .mini-badge.done { background: #ecfdf5; color: #047857; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'burial';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Burial Service Registry</div>
            <div class="top-bar-subtitle">Complete archive of historical and active Islamic funeral service cases</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/damayan/burial-requests') ?>" class="btn-topbar primary" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Active Requests
          </a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Burial Records</span>
        </div>

        <!-- Cemetery Operational Insights Cards -->
        <?php
          $totalPlots = count($plots ?? []);
          $availCount = count(array_filter($plots ?? [], fn($p) => ($p['status'] ?? '') === 'Available'));
          $occupiedCount = count(array_filter($plots ?? [], fn($p) => ($p['status'] ?? '') === 'Occupied'));
          $pendingPlots = count(array_filter($records ?? [], fn($r) => in_array(strtolower($r['cemetery_plot_status'] ?? ''), ['pending review', 'under review', 'awaiting plot availability'])));
        ?>
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Cemetery Plots</div>
            <div class="insight-value"><?= $totalPlots ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#10b981;">
            <div class="insight-label">Available for Assignment</div>
            <div class="insight-value success"><?= $availCount ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#3b82f6;">
            <div class="insight-label">Assigned / Occupied</div>
            <div class="insight-value info"><?= $occupiedCount ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#f59e0b;">
            <div class="insight-label">Awaiting Assignment</div>
            <div class="insight-value warning"><?= $pendingPlots ?></div>
          </div>
        </div>

        <!-- Section Navigation Pills -->
        <div class="filter-bar">
          <div class="filter-pills">
            <button type="button" class="filter-pill active" id="btn-view-requests" onclick="toggleViewMode('requests')">Plot Requests &amp; Assignments</button>
            <button type="button" class="filter-pill" id="btn-view-inventory" onclick="toggleViewMode('inventory')">Plot Inventory &amp; Availability</button>
          </div>
          <div style="display:flex; align-items:center; gap:10px;">
            <div class="search-box">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" id="table-search-input" placeholder="Search plot, deceased, ref #..." value="<?= htmlspecialchars($searchQuery ?? '') ?>" onkeyup="onSearchInput(event)" />
            </div>
            <button type="button" class="btn-topbar primary" onclick="showAddPlotModal()" style="padding:7px 14px; font-size:0.82rem; white-space:nowrap;">
              + Add Plot
            </button>
          </div>
        </div>

        <!-- Container 1: Plot Requests Table -->
        <div id="container-requests" class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Cemetery Burial Plot Requests &amp; Case Assignments
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant / Representative</th>
                    <th>Deceased</th>
                    <th>Preferred Cemetery</th>
                    <th>Assigned Burial Plot</th>
                    <th>Plot Status</th>
                    <th style="text-align:center;">Action</th>
                  </tr>
                </thead>
                <tbody id="burial-tbody">
                  <?php if(empty($records)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No cemetery plot requests found.</td></tr>
                  <?php else: ?>
                    <?php foreach($records as $r): ?>
                      <tr>
                        <td class="td-id">#<?= htmlspecialchars($r['id']) ?></td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($r['name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);">Representative</div>
                        </td>
                        <td>
                          <div style="font-weight:700;"><?= htmlspecialchars($r['deceased']) ?></div>
                          <span style="font-size:0.7rem; font-weight:700; padding:1px 6px; border-radius:8px; <?= ($r['deceased_sex'] ?? '') === 'Female' ? 'background:#fce7f3;color:#9d174d;' : 'background:#e0e7ff;color:#3730a3;' ?>">
                            <?= htmlspecialchars($r['deceased_sex'] ?? 'Male') ?>
                          </span>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);"><?= htmlspecialchars($r['cemetery_preference'] ?: 'ISCAG Cemetery') ?></td>
                        <td>
                          <?php if(!empty($r['assigned_plot_number'])): ?>
                            <div style="font-weight:700; color:var(--damayan-dark);">
                              <?= htmlspecialchars($r['assigned_cemetery_name'] ?: 'ISCAG Cemetery') ?>
                            </div>
                            <div style="font-size:0.75rem; color:var(--damayan-accent); font-weight:600;">
                              Sec <?= htmlspecialchars($r['assigned_section'] ?: 'General') ?> - Plot #<?= htmlspecialchars($r['assigned_plot_number']) ?>
                            </div>
                          <?php else: ?>
                            <span style="font-size:0.78rem; color:var(--text-muted); font-style:italic;">Not Assigned Yet</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge-status <?= strtolower($r['cemetery_plot_status'] ?? '') === 'plot assigned' ? 'badge-active' : (in_array(strtolower($r['cemetery_plot_status'] ?? ''), ['approved', 'available for assignment']) ? 'badge-info' : (strtolower($r['cemetery_plot_status'] ?? '') === 'rejected' ? 'badge-danger' : 'badge-pending')) ?>">
                            <?= htmlspecialchars($r['cemetery_plot_status'] ?? 'Pending Review') ?>
                          </span>
                        </td>
                        <td style="text-align:center;">
                          <button type="button" class="btn-topbar primary" style="padding:5px 14px; font-size:0.78rem;" onclick="openCaseModal('<?= htmlspecialchars($r['id']) ?>')">Assign / Manage</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Container 2: Plot Inventory Table -->
        <div id="container-inventory" class="section-card" style="display:none;">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              Cemetery Plot Physical Inventory &amp; Occupancy
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Plot ID</th>
                    <th>Cemetery Name</th>
                    <th>Section</th>
                    <th>Plot Number</th>
                    <th>Plot Type</th>
                    <th>Current Status</th>
                    <th>Assigned Deceased</th>
                    <th style="text-align:center;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($plots)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No inventory plots configured.</td></tr>
                  <?php else: ?>
                    <?php foreach($plots as $p): ?>
                      <tr>
                        <td class="td-id">#PLT-<?= htmlspecialchars($p['id']) ?></td>
                        <td style="font-weight:700;"><?= htmlspecialchars($p['cemetery_name']) ?></td>
                        <td>Section <?= htmlspecialchars($p['section'] ?: 'General') ?></td>
                        <td style="font-weight:700; color:var(--damayan-dark);"><?= htmlspecialchars($p['plot_number']) ?></td>
                        <td style="font-size:0.8rem;"><?= htmlspecialchars($p['plot_type'] ?: 'Standard') ?></td>
                        <td>
                          <span class="badge-status <?= $p['status'] === 'Available' ? 'badge-active' : ($p['status'] === 'Occupied' ? 'badge-info' : 'badge-danger') ?>">
                            <?= htmlspecialchars($p['status']) ?>
                          </span>
                        </td>
                        <td>
                          <?php if(!empty($p['assigned_deceased_name'])): ?>
                            <strong style="color:var(--text-main); font-size:0.83rem;"><?= htmlspecialchars($p['assigned_deceased_name']) ?></strong>
                            <?php if(!empty($p['assigned_at'])): ?>
                              <div style="font-size:0.72rem; color:var(--text-muted);"><?= date('M d, Y', strtotime($p['assigned_at'])) ?></div>
                            <?php endif; ?>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-size:0.78rem;">—</span>
                          <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                          <?php if($p['status'] === 'Available'): ?>
                            <button type="button" class="btn-topbar" style="padding:4px 10px; font-size:0.75rem; color:#dc2626;" onclick="updatePlotStatus(<?= $p['id'] ?>, 'Maintenance')">Mark Unavailable</button>
                          <?php elseif($p['status'] === 'Maintenance' || $p['status'] === 'Reserved'): ?>
                            <button type="button" class="btn-topbar" style="padding:4px 10px; font-size:0.75rem; color:var(--damayan-accent);" onclick="updatePlotStatus(<?= $p['id'] ?>, 'Available')">Make Available</button>
                          <?php else: ?>
                            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Occupied</span>
                          <?php endif; ?>
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

  <!-- Add Plot Modal Dialog -->
  <div id="addPlotModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; border-radius:14px; max-width:480px; width:100%; padding:22px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
      <h5 style="margin:0 0 6px; font-size:1.05rem; color:var(--damayan-dark); font-weight:800;">Add Cemetery Plot to Inventory</h5>
      <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px;">
        Register a new physical burial plot in the database. Duplicate combinations of cemetery, section, and plot number are prevented.
      </div>
      <form id="add-plot-form" onsubmit="event.preventDefault(); submitAddPlotForm();">
        <div class="form-group" style="margin-bottom:12px;">
          <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Cemetery Name *</label>
          <input type="text" id="new-cemetery-name" required value="ISCAG Designated Muslim Cemetery" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
          <div class="form-group">
            <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Section / Block *</label>
            <input type="text" id="new-plot-section" required placeholder="e.g. Section A" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
          </div>
          <div class="form-group">
            <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot Number *</label>
            <input type="text" id="new-plot-number" required placeholder="e.g. A-125" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" />
          </div>
        </div>
        <div class="form-group" style="margin-bottom:12px;">
          <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Plot Type</label>
          <select id="new-plot-type" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;">
            <option value="Standard">Standard Burial Plot</option>
            <option value="Family">Family / Multi Plot</option>
            <option value="Child">Child Plot</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:16px;">
          <label style="font-size:0.8rem; font-weight:700; display:block; margin-bottom:4px;">Initial Notes / Location Details</label>
          <textarea id="new-plot-notes" rows="2" style="width:100%; padding:8px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.85rem;" placeholder="e.g. Near west boundary, leveled ground..."></textarea>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" class="btn-topbar" onclick="closeAddPlotModal()">Cancel</button>
          <button type="submit" class="btn-topbar primary">Save Plot to Inventory</button>
        </div>
      </form>
    </div>
  </div>

  <?php include __DIR__ . '/burial_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    function toggleViewMode(mode) {
      const btnReq = document.getElementById('btn-view-requests');
      const btnInv = document.getElementById('btn-view-inventory');
      const conReq = document.getElementById('container-requests');
      const conInv = document.getElementById('container-inventory');

      if(mode === 'inventory') {
        btnInv.classList.add('active');
        btnReq.classList.remove('active');
        conInv.style.display = 'block';
        conReq.style.display = 'none';
      } else {
        btnReq.classList.add('active');
        btnInv.classList.remove('active');
        conReq.style.display = 'block';
        conInv.style.display = 'none';
      }
    }

    function showAddPlotModal() {
      document.getElementById('addPlotModal').style.display = 'flex';
    }
    function closeAddPlotModal() {
      document.getElementById('addPlotModal').style.display = 'none';
    }

    async function submitAddPlotForm() {
      const payload = {
        cemetery_name: document.getElementById('new-cemetery-name').value.trim(),
        section: document.getElementById('new-plot-section').value.trim(),
        plot_number: document.getElementById('new-plot-number').value.trim(),
        plot_type: document.getElementById('new-plot-type').value,
        notes: document.getElementById('new-plot-notes').value.trim()
      };

      try {
        const res = await fetch('<?= url('/admin/damayan/cemetery-plots/add') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Plot Added', 'New cemetery plot registered in database inventory.', 'success');
          closeAddPlotModal();
          setTimeout(() => location.reload(), 1200);
        } else {
          showAlert('Error', data.error || 'Failed to add cemetery plot.', 'error');
        }
      } catch(e) {
        showAlert('Error', 'Network error occurred.', 'error');
      }
    }

    async function updatePlotStatus(plotId, status) {
      try {
        const res = await fetch('<?= url('/admin/damayan/cemetery-plots/update-status') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ plot_id: plotId, status: status })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Updated', `Plot status changed to ${status}.`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showAlert('Error', data.error || 'Failed to update plot status.', 'error');
        }
      } catch(e) {
        showAlert('Error', 'Network error.', 'error');
      }
    }

    function onSearchInput(e) {
      if(e.key === 'Enter') {
        const url = new URL(window.location.href);
        url.searchParams.set('search', e.target.value.trim());
        window.location.href = url.toString();
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const caseId = params.get('id');
      if(caseId) {
        openCaseModal(caseId);
      }
    });
  </script>
</body>
</html>
