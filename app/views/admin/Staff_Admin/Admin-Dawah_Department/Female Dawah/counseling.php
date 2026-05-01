<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Female']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Counseling Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .badge-status.pending { background: #fef3c7; color: #d97706; }
    .badge-status.success { background: #dcfce7; color: #16a34a; }
    .badge-status.danger { background: #fee2e2; color: #dc2626; }
    
    .tab-btn { padding: 10px 20px; border: none; background: none; font-size: 0.9rem; font-weight: 600; color: var(--text-muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.3s; }
    .tab-btn.active { color: #B8860B; border-bottom-color: #B8860B; }
    
    .btn-action-pill { transition: all 0.2s; }
    .btn-action-pill:hover { transform: scale(1.05); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/admin/sidebar.php'; ?>
    
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-title">Counseling Management (Female)</div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Counseling Requests</span>
        </div>

        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#B8860B;margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Sisters' Counseling Records
            </h6>
            <div style="display:flex; gap:10px;">
              <button class="tab-btn active" onclick="filterByStatus('all')">All</button>
              <button class="tab-btn" onclick="filterByStatus('pending')">Pending</button>
              <button class="tab-btn" onclick="filterByStatus('approved')">Approved</button>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Client Name</th>
                    <th>Concern Type</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="counseling-tbody">
                  <!-- JS Rendered -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const records = <?= json_encode($records ?? []) ?>;
    
    function renderTable(filter = 'all') {
      const tbody = document.getElementById('counseling-tbody');
      let filtered = records;
      if(filter !== 'all') filtered = records.filter(r => r.status === filter);
      
      if(filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No ${filter === 'all' ? '' : filter} counseling records found.</td></tr>`;
        return;
      }
      
      tbody.innerHTML = filtered.map(r => {
        const sc = (r.status === 'approved') ? 'success' : ((r.status === 'rejected') ? 'danger' : 'pending');
        const displayStatus = r.status === 'rejected' ? 'Disapproved' : r.status.charAt(0).toUpperCase() + r.status.slice(1);
        
        return `
          <tr>
            <td class="td-id">#${r.id}</td>
            <td style="font-weight:600;">${r.first_name} ${r.last_name}</td>
            <td>${r.reason || 'General Consultation'}</td>
            <td>${new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</td>
            <td><span class="badge-status ${sc}">${displayStatus}</span></td>
            <td>
              <div style="display: flex; gap: 8px;">
                <button class="btn-action-pill approve" onclick="handleAction(${r.id}, 'approve')" style="color: #10b981; font-weight:700; background:rgba(16,185,129,0.1); padding:6px 14px; border-radius:8px; border:none; cursor:pointer; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                  <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Approve
                </button>
                <button class="btn-action-pill resched" onclick="handleAction(${r.id}, 'resched')" style="color: #f59e0b; font-weight:700; background:rgba(245,158,11,0.1); padding:6px 14px; border-radius:8px; border:none; cursor:pointer; font-size: 0.75rem; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                  <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg> Resched
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function filterByStatus(status) {
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      event.target.classList.add('active');
      renderTable(status);
    }

    async function handleAction(id, action) {
      if(action === 'resched') {
        alert('Reschedule modal will be implemented in the next phase.');
        return;
      }

      if(!confirm(`Are you sure you want to ${action} this request?`)) return;

      try {
        const endpoint = action === 'approve' ? '/admin/dawah/counseling/approve' : '/admin/dawah/counseling/reject';
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const result = await response.json();
        if(result.success) {
          location.reload();
        } else {
          alert('Action failed. Please try again.');
        }
      } catch (err) {
        console.error(err);
        alert('An error occurred.');
      }
    }

    // Initial render
    renderTable();
  </script>
</body>
</html>
