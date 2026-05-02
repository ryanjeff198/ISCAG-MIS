<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Charity Programs</title>
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
      gap: 20px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 24px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }
    
    .program-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 32px;
    }
    .program-card {
      background: white;
      border-radius: 16px;
      border: 1px solid var(--border);
      padding: 24px;
      transition: all 0.3s ease;
    }
    .program-card:hover { transform: translateY(-4px); border-color: var(--damayan-accent); box-shadow: 0 12px 24px rgba(0,0,0,0.08); }
    .program-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .program-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--damayan-dark); margin: 0; }
    .program-desc { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5; }
    .program-meta { display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 1px solid var(--border); }
    .meta-item { display: flex; flex-direction: column; }
    .meta-label { font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .meta-value { font-size: 0.9rem; font-weight: 700; color: var(--text-main); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'charity';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Charity & Donation Management</div>
            <div class="top-bar-subtitle">Managing social welfare programs, community donations, and aid distribution</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Charity Programs</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Active Programs</div>
            <div class="insight-value" id="stat-active">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Recipients</div>
            <div class="insight-value" id="stat-recipients">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Funds Allocated</div>
            <div class="insight-value success" id="stat-funds">₱0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Aid</div>
            <div class="insight-value warning" id="stat-pending">0</div>
          </div>
        </div>

        <div class="program-grid" id="program-container">
          <!-- Programs injected via JS -->
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Recent Assistance Requests
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Requester</th>
                    <th>Program Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="charity-tbody">
                  <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No recent requests.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Donation History Modal -->
  <div class="modal-backdrop" id="donation-modal" style="display:none;">
    <div class="modal-content" style="max-width:500px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="modal-program-title">Donation History</h5>
        <button class="modal-close" onclick="closeModal('donation-modal')"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body">
        <div style="margin-bottom: 20px; padding: 16px; background: var(--damayan-light); border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.7rem; font-weight: 700; color: var(--damayan-dark); text-transform: uppercase;">Total Donated</div>
            <div id="modal-total-donated" style="font-size: 1.4rem; font-weight: 800; color: var(--damayan-accent);">₱0</div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 0.7rem; font-weight: 700; color: var(--damayan-dark); text-transform: uppercase;">Contributors</div>
            <div id="modal-contributor-count" style="font-size: 1.4rem; font-weight: 800; color: var(--damayan-accent);">0</div>
          </div>
        </div>
        <div id="donation-list" style="display: flex; flex-direction: column; gap: 10px;">
          <!-- Donations injected via JS -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar primary" onclick="closeModal('donation-modal')">Close History</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');
    
    const realStats = <?= json_encode($stats ?? []) ?>;
    const realDonations = <?= json_encode($donations ?? []) ?>;
    
    const programs = [
      { id: 1, title: 'Medical Assistance', desc: 'Financial support for emergency medical expenses and hospital bills for families in need.', budget: '₱250,000', recipients: 45, status: 'Active' },
      { id: 2, title: 'Food Security Program', desc: 'Weekly food pack distribution for underprivileged community members and elderlies.', budget: '₱120,000', recipients: 120, status: 'Active' },
      { id: 3, title: 'Educational Aid', desc: 'Scholarship fund and school supplies for bright students from low-income backgrounds.', budget: '₱500,000', recipients: 15, status: 'Active' }
    ];

    // Merge real stats into programs
    programs.forEach(p => {
      const stat = realStats.find(s => s.program_id == p.id);
      p.totalDonated = stat ? parseFloat(stat.total) : 0;
      p.contributorCount = stat ? parseInt(stat.contributors) : 0;
    });

    function maskName(name) {
      if (!name) return 'Anonymous';
      const parts = name.split(' ');
      return parts.map(part => {
        if (part.length <= 2) return part[0] + '*';
        return part[0] + '*'.repeat(part.length - 2) + part[part.length - 1];
      }).join(' ');
    }

    function viewDonations(programId) {
      const prog = programs.find(p => p.id === programId);
      const filtered = realDonations[programId] || [];
      
      document.getElementById('modal-program-title').textContent = prog.title + ' History';
      document.getElementById('modal-total-donated').textContent = '₱' + prog.totalDonated.toLocaleString();
      document.getElementById('modal-contributor-count').textContent = prog.contributorCount;
      
      const list = document.getElementById('donation-list');
      if (filtered.length === 0) {
        list.innerHTML = '<div style="text-align:center; padding:20px; color:var(--text-muted);">No donations recorded yet.</div>';
      } else {
        list.innerHTML = filtered.map(d => `
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px; border:1px solid var(--border); border-radius:10px; background:white;">
            <div>
              <div style="font-weight:700; font-size:0.9rem; color:var(--text-main);">${maskName(d.donor_name || d.name)}</div>
              <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;">${new Date(d.submitted_at || d.date).toLocaleDateString()}</div>
            </div>
            <div style="font-weight:800; color:var(--damayan-accent);">₱${parseFloat(d.amount).toLocaleString()}</div>
          </div>
        `).join('');
      }
      
      openModal('donation-modal');
    }

    const requests = <?= json_encode($requests ?? []) ?>;

    function renderPrograms() {
      const container = document.getElementById('program-container');
      container.innerHTML = programs.map(p => `
        <div class="program-card" onclick="viewDonations(${p.id})" style="cursor:pointer;">
          <div class="program-header">
            <h3 class="program-title">${p.title}</h3>
            <span class="badge-status badge-active">${p.status}</span>
          </div>
          <p class="program-desc">${p.desc}</p>
          <div class="program-meta">
            <div class="meta-item">
              <span class="meta-label">Allocation</span>
              <span class="meta-value">${p.budget}</span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Donated</span>
              <span class="meta-value" style="color:var(--damayan-accent);">₱${p.totalDonated.toLocaleString()}</span>
            </div>
          </div>
        </div>
      `).join('');
    }

    function renderTable() {
      const tbody = document.getElementById('charity-tbody');
      if(requests.length === 0) return;
      tbody.innerHTML = requests.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.name}</td>
          <td>${r.type}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:var(--damayan-accent);" onclick="showAlert('Service Update', 'Direct request management is being synchronized with the new charity module.', 'info')">Manage</button>
          </td>
        </tr>
      `).join('');

      // Update stats
      document.getElementById('stat-active').textContent = programs.length;
      document.getElementById('stat-recipients').textContent = programs.reduce((acc, curr) => acc + curr.recipients, 0);
      document.getElementById('stat-funds').textContent = '₱870,000';
      document.getElementById('stat-pending').textContent = requests.filter(x => x.status === 'Pending').length;
    }
    
    renderPrograms();
    renderTable();
  </script>
</body>
</html>
