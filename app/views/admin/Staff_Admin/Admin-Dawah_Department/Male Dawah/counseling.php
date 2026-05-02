<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Male Counseling Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    
    /* Category Dropdown */
    .filter-row { display: flex; gap: 16px; align-items: center; margin-bottom: 24px; flex-wrap: wrap; }
    .category-select-wrapper { display: flex; align-items: center; gap: 10px; background: white; padding: 8px 16px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .category-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; }
    .category-dropdown { border: none; font-size: 0.9rem; font-weight: 700; color: var(--primary); cursor: pointer; background: transparent; padding-right: 20px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right center; background-size: 14px; }
    .category-dropdown:focus { outline: none; }
    
    /* Insights */
    .admin-insights { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .insight-card { background: white; padding: 16px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s; display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; cursor: pointer; }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--accent); }
    .insight-card::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--border); }
    .insight-card.all::after { background: var(--accent); }
    .insight-card.active-tab::after { background: var(--primary); }
    
    .insight-label { font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1; }
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
        <div class="top-bar-left">
            <div class="top-bar-title">Counseling Management</div>
            <div class="top-bar-subtitle">Male Da'wah Department — Guidance and Consultation Services</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Counseling Requests</span>
        </div>

        <!-- Dynamic Insights Summary -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered by JS -->
        </div>

        <!-- Category & Filters Row -->
        <div class="filter-row">
          <div class="category-select-wrapper">
            <span class="category-label">Category:</span>
            <select class="category-dropdown" id="category-dropdown" onchange="switchCategory(this.value)">
              <!-- Rendered by JS -->
            </select>
          </div>
        </div>

        <!-- Table Section -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6 id="current-category-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Brothers' Counseling Records
            </h6>
            <div style="display:flex; gap:10px;">
              <select id="status-filter" class="form-control" style="font-size:0.8rem; padding:4px 12px; border-radius:8px; width:auto; appearance:auto;" onchange="renderActiveCategory()">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th style="min-width: 180px;">Specific Concern</th>
                    <th>Date Filed</th>
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
    let activeCategory = 'All Records';

    const categories = [
      "All Records",
      "Family / Marital Issues",
      "Personal / Spiritual Struggles",
      "Parenting & Family Guidance",
      "Youth & Academic Concerns",
      "Financial Difficulties",
      "Grief and Loss",
      "Anger Management",
      "Revert / New Muslim Support",
      "Other"
    ];

    function renderTabs() {
      const dropdown = document.getElementById('category-dropdown');
      dropdown.innerHTML = categories.map(cat => `
        <option value="${cat}" ${activeCategory === cat ? 'selected' : ''}>${cat}</option>
      `).join('');
    }

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = records.length;
      const pending = records.filter(r => r.status === 'pending').length;

      container.innerHTML = `
        <div class="insight-card all" onclick="switchCategory('All Records')">
          <div class="insight-label">Total Requests</div>
          <div class="insight-value">${total}</div>
        </div>
        <div class="insight-card pending" style="--border:var(--warning);" onclick="renderActiveCategory()">
          <div class="insight-label">Awaiting Review</div>
          <div class="insight-value" style="color:var(--warning);">${pending}</div>
        </div>
        <div class="insight-card active-tab" onclick="switchCategory('${activeCategory}')">
          <div class="insight-label">${activeCategory === 'All Records' ? 'Most Recent' : activeCategory}</div>
          <div class="insight-value" style="color:var(--primary);">${activeCategory === 'All Records' ? records.length : records.filter(r => r.reason === activeCategory).length}</div>
        </div>
      `;
    }

    function switchCategory(cat) {
      activeCategory = cat;
      renderTabs();
      renderInsights();
      document.getElementById('current-category-title').innerHTML = `
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        ${cat}
      `;
      renderActiveCategory();
    }

    function renderActiveCategory() {
      const tbody = document.getElementById('counseling-tbody');
      const statusFilter = document.getElementById('status-filter').value;
      
      let filtered = records;
      if (activeCategory !== 'All Records') filtered = filtered.filter(r => r.reason === activeCategory);
      if (statusFilter !== 'all') filtered = filtered.filter(r => r.status === statusFilter);

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No records found for "${activeCategory}".</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(r => {
        const sc = (r.status === 'approved') ? 'badge-approved' : ((r.status === 'rejected') ? 'badge-rejected' : 'badge-pending');
        const displayStatus = r.status === 'rejected' ? 'Disapproved' : r.status.charAt(0).toUpperCase() + r.status.slice(1);
        
        return `
          <tr>
            <td class="td-id">#${r.id}</td>
            <td style="font-weight:600;">${r.first_name} ${r.last_name}</td>
            <td><span style="font-weight:700; color:var(--primary);">${r.reason}</span></td>
            <td>${new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}</td>
            <td><span class="badge-status ${sc}">${displayStatus}</span></td>
            <td>
              <div class="actions-cell">
                <button class="btn-action btn-approve" onclick="handleAction(${r.id}, 'approve')">
                  <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Approve
                </button>
                <button class="btn-action btn-edit" style="color:var(--warning); border-color:var(--warning);" onclick="handleAction(${r.id}, 'resched')">
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg> Resched
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    async function handleAction(id, action) {
      if(action === 'resched') {
        showAlert('Service Update', 'The Reschedule module is currently undergoing system synchronization. Please check back shortly.', 'info');
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
          showAlert('Action Successful', `The request has been ${action}d successfully.`, 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('Action Failed', 'The system could not process this request. Please verify your connection.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('System Error', 'A critical error occurred during the update process.', 'error');
      }
    }

    renderTabs();
    renderInsights();
    renderActiveCategory();
  </script>
</body>
</html>
