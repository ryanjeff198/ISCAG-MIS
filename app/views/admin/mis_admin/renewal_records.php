<?php $active_page = 'renewal_records'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Renewal Records</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .renewal-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .status-active { background: rgba(47, 138, 96, 0.1); color: var(--success); }
    .status-pending { background: rgba(199, 154, 43, 0.1); color: var(--warning); }
    .status-expired { background: rgba(139, 46, 46, 0.1); color: var(--danger); }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Contract Renewal Records</div>
            <div class="top-bar-subtitle">Overview of apartment lease extensions and active contracts</div>
          </div>
        </div>
        <div class="top-bar-actions">
           <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Active Leases</div>
            <div class="insight-value info" id="stat-active-leases"><?= $stats['activeLeases'] ?? 0 ?></div>
            <div class="insight-icon-bg" style="color:var(--info)"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Renewals</div>
            <div class="insight-value warning" id="stat-pending-renewals"><?= $stats['pendingRenewals'] ?? 0 ?></div>
            <div class="insight-icon-bg" style="color:var(--warning)"><svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Avg. Extension</div>
            <div class="insight-value success">6.2 Mo</div>
            <div class="insight-icon-bg" style="color:var(--success)"><svg viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg></div>
          </div>
        </div>

        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a><span class="sep">›</span><span class="current">Renewal Records</span>
        </div>

        <!-- RENEWALS TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>Lease Renewal History</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="renewalTable">
                <thead>
                  <tr>
                    <th>Lease ID</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Current Expiration</th>
                    <th>Extension</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                  </tr>
                </thead>
                <tbody id="renewal-tbody">
                   <!-- Populated by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    function loadRenewals() {
      const data = <?= json_encode($renewals ?? []) ?>;
      const container = document.getElementById('renewal-tbody');
      
      document.getElementById('stat-active-leases').textContent = <?= json_encode($stats['activeLeases'] ?? 0) ?>;
      document.getElementById('stat-pending-renewals').textContent = <?= json_encode($stats['pendingRenewals'] ?? 0) ?>;

      if (data.length === 0) {
        container.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted);">No renewal records found.</td></tr>';
        return;
      }

      container.innerHTML = data.map(r => `
        <tr>
          <td class="td-id">L-${r.lease_id}</td>
          <td style="font-weight:600;">${r.first_name} ${r.last_name}</td>
          <td style="font-weight:700; color:var(--primary);">${r.unit_type || '—'}</td>
          <td>${formatDate(r.end_date)}</td>
          <td><strong>+${r.requested_term_months} Mo</strong></td>
          <td><span class="renewal-status status-${r.status.toLowerCase()}">${r.status}</span></td>
          <td style="color:var(--text-muted); font-size:0.75rem;">${formatDate(r.created_at)}</td>
        </tr>
      `).join('');
    }

    loadRenewals();

    function formatDate(d) {
      const date = new Date(d);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
  </script>
</body>
</html>
