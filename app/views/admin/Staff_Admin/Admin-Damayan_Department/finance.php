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
    .finance-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .stat-card {
      background: white;
      padding: 22px 24px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.07);
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 100%;
    }
    .stat-card.incoming::before { background: var(--success); }
    .stat-card.spent::before { background: var(--danger); }
    .stat-card.balance::before { background: var(--damayan-accent); }

    .stat-card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }
    .stat-label-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .stat-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .stat-icon svg {
      width: 20px;
      height: 20px;
    }
    .stat-card.incoming .stat-icon {
      background: rgba(46, 125, 85, 0.12);
      color: var(--success);
    }
    .stat-card.spent .stat-icon {
      background: rgba(211, 47, 47, 0.12);
      color: var(--danger);
    }
    .stat-card.balance .stat-icon {
      background: var(--damayan-light);
      color: var(--damayan-accent);
    }

    .stat-title {
      font-size: 0.76rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .stat-badge {
      font-size: 0.7rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 12px;
    }
    .stat-card.incoming .stat-badge {
      background: rgba(46, 125, 85, 0.1);
      color: var(--success);
    }
    .stat-card.spent .stat-badge {
      background: rgba(211, 47, 47, 0.1);
      color: var(--danger);
    }
    .stat-card.balance .stat-badge {
      background: rgba(23, 107, 69, 0.12);
      color: var(--damayan-accent);
    }

    .stat-value {
      font-size: 1.85rem;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 8px;
      letter-spacing: -0.02em;
    }
    .stat-footer {
      font-size: 0.78rem;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 6px;
    }
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
           <a href="<?= url('/admin/damayan') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-accent);">
             <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
           </a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Damayan</a><span class="sep">›</span><span class="current">Finance</span>
        </div>

        <!-- REFINED STAT CARDS -->
        <div class="finance-stats">
            <div class="stat-card incoming">
                <div class="stat-card-top">
                    <div class="stat-label-wrap">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        </div>
                        <div class="stat-title">Incoming Donations</div>
                    </div>
                    <span class="stat-badge">+ Verified Inflow</span>
                </div>
                <div class="stat-value" style="color:var(--success);">₱<?= number_format($summary['incoming'] ?? 0, 2) ?></div>
                <div class="stat-footer">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;color:var(--success);"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    Total verified community contributions
                </div>
            </div>

            <div class="stat-card spent">
                <div class="stat-card-top">
                    <div class="stat-label-wrap">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 15h4v6h6v-6h4l-7-7-7 7zM5 4v2h14V4H5z"/></svg>
                        </div>
                        <div class="stat-title">Disbursed Funds</div>
                    </div>
                    <span class="stat-badge">- Liquidations</span>
                </div>
                <div class="stat-value" style="color:var(--danger);">₱<?= number_format($summary['spent'] ?? 0, 2) ?></div>
                <div class="stat-footer">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;color:var(--danger);"><path d="M19 13H5v-2h14v2z"/></svg>
                    Burial assistance, medical & welfare aid
                </div>
            </div>

            <div class="stat-card balance">
                <div class="stat-card-top">
                    <div class="stat-label-wrap">
                        <div class="stat-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                        </div>
                        <div class="stat-title">Remaining Treasury</div>
                    </div>
                    <span class="stat-badge">Net Balance</span>
                </div>
                <div class="stat-value" style="color:var(--damayan-accent);">₱<?= number_format($summary['balance'] ?? 0, 2) ?></div>
                <div class="stat-footer">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;color:var(--damayan-accent);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    Available fund ready for social services
                </div>
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
                        <td><span class="badge-status badge-info"><?= htmlspecialchars($l['category']) ?></span></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($l['description']) ?></td>
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
