<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — User Management</title>
  <meta name="description" content="Manage all registered users, roles, and profiles" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .user-cell {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-cell .mini-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.68rem;
      font-weight: 700;
      color: white;
      flex-shrink: 0;
    }

    .user-cell .user-name {
      font-weight: 600;
    }

    .user-cell .user-email {
      font-size: 0.75rem;
      color: var(--text-muted);
    }

    .profile-bar-wrap {
      width: 50px;
      height: 6px;
      background: #e8ece9;
      border-radius: 3px;
      overflow: hidden;
      display: inline-block;
      vertical-align: middle;
      margin-right: 6px;
    }

    .profile-bar-fill {
      height: 100%;
      border-radius: 3px;
      transition: width 0.4s ease;
    }

    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .detail-grid .detail-item label {
      display: block;
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 4px;
    }

    .detail-grid .detail-item p {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-main);
      margin: 0;
    }

    .role-select {
      padding: 4px 8px;
      border-radius: 5px;
      border: 1.5px solid var(--border);
      font-size: 0.78rem;
      font-family: inherit;
      background: white;
      cursor: pointer;
      transition: border-color 0.18s;
    }

    .role-select:focus {
      outline: none;
      border-color: var(--primary);
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">User Management</div>
            <div class="top-bar-subtitle">View and manage all registered user accounts</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Users</div>
            <div class="insight-value info" id="stat-total-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Active Users</div>
            <div class="insight-value success" id="stat-active-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Complete Profiles</div>
            <div class="insight-value" id="stat-complete-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Monthly Growth</div>
            <div class="insight-value success">+12.4%</div>
          </div>
        </div>
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/mis_admin') ?>">Dashboard</a>
          <span class="sep">›</span>
          <span class="current">User Management</span>
        </div>

        <!-- STATS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon teal">
              <svg viewBox="0 0 24 24">
                <path
                  d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-total">0</div>
              <div class="stat-label">Total Users</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-active">0</div>
              <div class="stat-label">Active</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-complete">0</div>
              <div class="stat-label">Complete Profiles</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24">
                <path
                  d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
              </svg>
            </div>
            <div>
              <div class="stat-value" id="stat-inactive">0</div>
              <div class="stat-label">Inactive</div>
            </div>
          </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
          <input type="text" class="search-input" id="search-input" placeholder="Search by name or email..." />
          <select class="filter-select" id="filter-role">
            <option value="">All Roles</option>
            <option value="user">User</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
          </select>
          <select class="filter-select" id="filter-status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <select class="filter-select" id="filter-gender">
            <option value="">All Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>

        <!-- USERS TABLE -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path
                  d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z" />
              </svg>
              All Users
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="users-count">0 records</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>User ID</th>
                    <th>User</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Profile</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="users-tbody"></tbody>
              </table>
            </div>
          </div>
      </div>
    </main>
  </div>

  <!-- ═══ USER DETAIL MODAL ═══ -->
  <div class="modal-backdrop" id="user-detail-modal" style="display:none;">
    <div class="modal-content" style="max-width:560px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="detail-title">User Details</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body" id="detail-body"></div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('user-detail-modal')">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    initAdminData();
    loadUserNav();
    setTopBarDate();

    let allUsers = getAllUsers();

    // Stats
    function updateStats(users) {
      document.getElementById('stat-total').textContent = users.length;
      document.getElementById('stat-active').textContent = users.filter(u => u.status === 'active').length;
      document.getElementById('stat-complete').textContent = users.filter(u => u.profilePct === 100).length;
      document.getElementById('stat-inactive').textContent = users.filter(u => u.status === 'inactive').length;
    }
    updateStats(allUsers);

    // Render table
    function renderTable(users) {
      const tbody = document.getElementById('users-tbody');
      document.getElementById('users-count').textContent = users.length + ' record' + (users.length !== 1 ? 's' : '');

      if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:28px;color:var(--text-muted);">No users found matching your filters.</td></tr>';
        return;
      }

      tbody.innerHTML = users.map(u => {
        const initials = u.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
        const pctColor = u.profilePct >= 100 ? 'var(--success)' : u.profilePct >= 50 ? 'var(--accent)' : 'var(--danger)';
        const avatarBg = u.gender === 'female' ? '#5a2e7a' : 'var(--primary-light)';
        return `<tr>
        <td class="td-id">${u.id}</td>
        <td>
          <div class="user-cell">
            <div class="mini-avatar" style="background:${avatarBg};">${initials}</div>
            <div>
              <div class="user-name">${u.name}</div>
              <div class="user-email">${u.email}</div>
            </div>
          </div>
        </td>
        <td style="text-transform:capitalize;">${u.gender || '—'}</td>
        <td style="text-transform:capitalize;">${u.role}</td>
        <td>
          <div class="profile-bar-wrap"><div class="profile-bar-fill" style="width:${u.profilePct}%;background:${pctColor};"></div></div>
          <span style="font-size:0.78rem;font-weight:600;color:${pctColor};">${u.profilePct}%</span>
        </td>
        <td><span class="badge-status ${badgeClass(u.status)}">${statusLabel(u.status)}</span></td>
        <td style="font-size:0.82rem;color:var(--text-muted);">${formatDate(u.joined)}</td>
        <td>
          <div class="actions-cell">
            <button class="btn-action btn-view" onclick="viewUser('${u.id}')">
              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
              View
            </button>
            <button class="btn-action btn-edit" onclick="toggleStatus('${u.id}')">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              ${u.status === 'active' ? 'Deactivate' : 'Activate'}
            </button>
          </div>
        </td>
      </tr>`;
      }).join('');
    }
    renderTable(allUsers);

    // Filter logic
    function applyFilters() {
      const search = document.getElementById('search-input').value.toLowerCase().trim();
      const role = document.getElementById('filter-role').value;
      const status = document.getElementById('filter-status').value;
      const gender = document.getElementById('filter-gender').value;

      let filtered = allUsers;
      if (search) {
        filtered = filtered.filter(u =>
          u.name.toLowerCase().includes(search) ||
          u.email.toLowerCase().includes(search) ||
          u.id.toLowerCase().includes(search)
        );
      }
      if (role) filtered = filtered.filter(u => u.role === role);
      if (status) filtered = filtered.filter(u => u.status === status);
      if (gender) filtered = filtered.filter(u => u.gender === gender);

      renderTable(filtered);
    }

    document.getElementById('search-input').addEventListener('input', applyFilters);
    document.getElementById('filter-role').addEventListener('change', applyFilters);
    document.getElementById('filter-status').addEventListener('change', applyFilters);
    document.getElementById('filter-gender').addEventListener('change', applyFilters);

    // View user detail
    function viewUser(id) {
      const u = allUsers.find(usr => usr.id === id);
      if (!u) return;
      const initials = u.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
      const pctColor = u.profilePct >= 100 ? 'var(--success)' : u.profilePct >= 50 ? 'var(--accent)' : 'var(--danger)';

      document.getElementById('detail-title').textContent = 'User Details — ' + u.id;
      document.getElementById('detail-body').innerHTML = `
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border);">
        <div class="mini-avatar" style="width:50px;height:50px;font-size:1.1rem;background:${u.gender === 'female' ? '#5a2e7a' : 'var(--primary-light)'};">${initials}</div>
        <div>
          <div style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);">${u.name}</div>
          <div style="font-size:0.82rem;color:var(--text-muted);">${u.email}</div>
        </div>
      </div>
      <div class="detail-grid">
        <div class="detail-item"><label>User ID</label><p style="font-family:monospace;color:var(--text-muted);">${u.id}</p></div>
        <div class="detail-item"><label>Gender</label><p style="text-transform:capitalize;">${u.gender || '—'}</p></div>
        <div class="detail-item"><label>Phone</label><p>${u.phone || '—'}</p></div>
        <div class="detail-item"><label>Role</label><p style="text-transform:capitalize;">${u.role}</p></div>
        <div class="detail-item"><label>Profile Completion</label>
          <p>
            <div class="profile-bar-wrap" style="width:80px;"><div class="profile-bar-fill" style="width:${u.profilePct}%;background:${pctColor};"></div></div>
            <span style="font-weight:700;color:${pctColor};">${u.profilePct}%</span>
          </p>
        </div>
        <div class="detail-item"><label>Status</label><p><span class="badge-status ${badgeClass(u.status)}">${statusLabel(u.status)}</span></p></div>
        <div class="detail-item"><label>Date Joined</label><p>${formatDate(u.joined)}</p></div>
      </div>
    `;
      openModal('user-detail-modal');
    }

    // Toggle user status
    function toggleStatus(id) {
      const users = getAllUsers();
      const u = users.find(usr => usr.id === id);
      if (!u) return;
      u.status = u.status === 'active' ? 'inactive' : 'active';
      saveAllUsers(users);
      allUsers = users;
      addActivityEntry(
        'User ' + (u.status === 'active' ? 'activated' : 'deactivated'),
        u.id + ' — ' + u.name + ' ' + u.status,
        'MIS Admin', 'user'
      );
      showToast((u.status === 'active' ? '✅ ' : '⛔ ') + u.name + ' is now ' + u.status, u.status === 'active' ? 'var(--success)' : 'var(--danger)');
      updateStats(allUsers);
      applyFilters();
    }

    initSidebar();
    initDropdowns();
    setupModalClose('user-detail-modal');
  </script>
</body>

</html>