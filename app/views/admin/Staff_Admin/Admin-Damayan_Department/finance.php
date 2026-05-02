<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Financial Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .finance-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--border); }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
    .stat-sub { font-size: 0.8rem; margin-top: 8px; color: var(--text-muted); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'finance';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Financial Management</div>
          <div class="top-bar-subtitle">Monitoring charity funds, donations, and liquidations</div>
        </div>
        <div class="top-bar-actions">
           <button class="btn-topbar primary" onclick="openLiquidationModal()">+ Record Liquidation</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Damayan</a><span class="sep">›</span><span class="current">Finance</span>
        </div>

        <div class="finance-stats">
            <div class="stat-card">
                <div class="stat-label">Incoming Money</div>
                <div class="stat-value" style="color:var(--success);">₱<?= number_format($summary['incoming'], 2) ?></div>
                <div class="stat-sub">Total verified donations</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Spent Money</div>
                <div class="stat-value" style="color:var(--danger);">₱<?= number_format($summary['spent'], 2) ?></div>
                <div class="stat-sub">Disbursements & Liquidations</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Stored Money (Balance)</div>
                <div class="stat-value" style="color:var(--damayan-accent);">₱<?= number_format($summary['balance'], 2) ?></div>
                <div class="stat-sub">Current available fund</div>
            </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              Liquidation Records
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th style="text-align:right;">Amount</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($liquidations)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No liquidation records found.</td></tr>
                  <?php else: ?>
                    <?php foreach($liquidations as $l): ?>
                      <tr>
                        <td><?= date('M d, Y', strtotime($l['date'])) ?></td>
                        <td><span class="badge-status badge-info"><?= $l['category'] ?></span></td>
                        <td style="font-weight:500;"><?= $l['description'] ?></td>
                        <td style="text-align:right;font-weight:700;color:var(--danger);">₱<?= number_format($l['amount'], 2) ?></td>
                        <td><span class="badge-status badge-active">Liquidated</span></td>
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

  <!-- Modal for Liquidation -->
  <div id="liquidationModal" class="mis-modal" style="display:none;">
    <div class="mis-modal-content" style="max-width:500px;">
        <div class="mis-modal-header">
            <h6>Record New Liquidation</h6>
            <button class="close-btn" onclick="closeLiquidationModal()">&times;</button>
        </div>
        <div class="mis-modal-body" style="padding:20px;">
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-size:0.85rem;font-weight:600;">Description</label>
                <input type="text" id="liq-desc" class="mis-input" placeholder="e.g. Purchase of burial materials">
            </div>
            <div style="margin-bottom:16px; display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block;margin-bottom:8px;font-size:0.85rem;font-weight:600;">Amount (₱)</label>
                    <input type="number" id="liq-amount" class="mis-input" placeholder="0.00">
                </div>
                <div>
                    <label style="display:block;margin-bottom:8px;font-size:0.85rem;font-weight:600;">Category</label>
                    <select id="liq-cat" class="mis-input">
                        <option value="Medical">Medical</option>
                        <option value="Burial">Burial</option>
                        <option value="Educational">Educational</option>
                        <option value="Materials">Materials</option>
                        <option value="Operational">Operational</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;margin-bottom:8px;font-size:0.85rem;font-weight:600;">Date</label>
                <input type="date" id="liq-date" class="mis-input" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="mis-modal-footer" style="padding:16px;justify-content:flex-end;">
            <button class="btn-topbar" onclick="closeLiquidationModal()">Cancel</button>
            <button class="btn-topbar primary" onclick="submitLiquidation()">Save Record</button>
        </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    function openLiquidationModal() { document.getElementById('liquidationModal').style.display = 'flex'; }
    function closeLiquidationModal() { document.getElementById('liquidationModal').style.display = 'none'; }

    async function submitLiquidation() {
        const data = {
            description: document.getElementById('liq-desc').value,
            amount: document.getElementById('liq-amount').value,
            category: document.getElementById('liq-cat').value,
            date: document.getElementById('liq-date').value
        };

        if(!data.description || !data.amount) {
            showAlert('Input Error', 'Please fill in all required fields.', 'warning');
            return;
        }

        try {
            const res = await fetch('<?= url('/admin/damayan/liquidation/submit') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(result.success) {
                showAlert('Success', 'Liquidation record saved successfully.', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error', 'Failed to save record.', 'error');
            }
        } catch (e) {
            console.error(e);
            showAlert('Error', 'An unexpected error occurred.', 'error');
        }
    }
  </script>
</body>
</html>
