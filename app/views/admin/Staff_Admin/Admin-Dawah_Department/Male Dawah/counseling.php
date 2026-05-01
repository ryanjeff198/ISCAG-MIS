<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Counseling Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
        --male-accent: #14532d;
        --male-dark: #064e3b;
        --male-light: #f0fdf4;
    }
    .top-bar-title { color: var(--male-dark); }
    .breadcrumb-bar .current { color: var(--male-accent); }
    .btn-action { color: var(--male-accent); }
    .btn-action-pill:hover { filter: brightness(0.95); transform: translateY(-1px); }
    .btn-action-pill:active { transform: translateY(0); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'counseling';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--male-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--male-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Male Counseling & Guidance</div>
            <div class="top-bar-subtitle">Manage religious counseling sessions and guidance requests</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Counseling Records</span>
        </div>

        <!-- ANALYTICS CARDS -->
        <div class="admin-insights" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px;">
          <div class="insight-card">
            <div class="insight-label" style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Requests</div>
            <div class="insight-value" style="font-size: 1.8rem; font-weight: 800; color: var(--male-dark);"><?= count($records) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label" style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Pending</div>
            <div class="insight-value" style="font-size: 1.8rem; font-weight: 800; color: #f59e0b;"><?= count(array_filter($records, fn($r) => $r['status'] === 'pending')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label" style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Approved</div>
            <div class="insight-value" style="font-size: 1.8rem; font-weight: 800; color: #10b981;"><?= count(array_filter($records, fn($r) => $r['status'] === 'approved')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label" style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Disapproved</div>
            <div class="insight-value" style="font-size: 1.8rem; font-weight: 800; color: #ef4444;"><?= count(array_filter($records, fn($r) => $r['status'] === 'rejected')) ?></div>
          </div>
        </div>

        <!-- TAB NAVIGATION -->
        <style>
          .tab-nav { display: flex; gap: 12px; border-bottom: 2px solid var(--border); margin-bottom: 24px; }
          .tab-btn { padding: 12px 24px; font-size: 0.9rem; font-weight: 700; color: var(--text-muted); background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; transition: all 0.25s ease; }
          .tab-btn.active { color: var(--male-accent) !important; border-bottom-color: var(--male-accent) !important; }
          .insight-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        </style>
        
        <div class="tab-nav">
          <button class="tab-btn active" onclick="filterByStatus('all')">All Requests</button>
          <button class="tab-btn" onclick="filterByStatus('pending')">Pending</button>
          <button class="tab-btn" onclick="filterByStatus('approved')">Approved</button>
          <button class="tab-btn" onclick="filterByStatus('rejected')">Disapproved</button>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--male-dark); margin: 0;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              Counseling Request List (Male)
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Reason / Topic</th>
                    <th>Submitted Date</th>
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
