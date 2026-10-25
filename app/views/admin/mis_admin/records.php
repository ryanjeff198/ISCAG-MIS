<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — User Management & RBAC</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Manage all registered users, roles, and profiles" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .badge-dept {
      font-size: 0.7rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 12px;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .badge-dept-apartments { background: #e0f2fe; color: #0369a1; }
    .badge-dept-dawah { background: #ecfdf5; color: #047857; }
    .badge-dept-education { background: #fef3c7; color: #b45309; }
    .badge-dept-damayan { background: #ede9fe; color: #6d28d9; }
    .audit-trail-item {
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
      font-size: 0.82rem;
    }
    .audit-trail-item:last-child { border-bottom: none; }
    .audit-trail-head {
      display: flex;
      justify-content: space-between;
      font-weight: 700;
      margin-bottom: 2px;
    }
    .audit-trail-time {
      font-size: 0.72rem;
      color: var(--text-muted);
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
          <div>
            <div class="top-bar-title">User Role Management & Control</div>
            <div class="top-bar-subtitle">View, assign department roles, review audit trails, and manage account access</div>
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
            <div class="insight-label">Total Users</div>
            <div class="insight-value info" id="stat-total-val"><?= count($users ?? []) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Active Users</div>
            <div class="insight-value success" id="stat-active-val">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Staff & Admins</div>
            <div class="insight-value" id="stat-staff-val" style="color:var(--primary);">0</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Deactivated</div>
            <div class="insight-value" id="stat-inactive-val" style="color:var(--danger);">0</div>
          </div>
        </div>

        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dashboard') ?>">MIS Admin</a>
          <span class="sep">›</span>
          <span class="current">User Role Management</span>
        </div>

        <!-- STATS -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-icon teal">
              <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z"/></svg>
            </div>
            <div>
              <div class="stat-value" id="stat-total">0</div>
              <div class="stat-label">Total Accounts</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div>
              <div class="stat-value" id="stat-active">0</div>
              <div class="stat-label">Active Accounts</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon gold">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <div>
              <div class="stat-value" id="stat-staff-count">0</div>
              <div class="stat-label">Department Staff</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon red">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <div>
              <div class="stat-value" id="stat-inactive">0</div>
              <div class="stat-label">Inactive Accounts</div>
            </div>
          </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
          <input type="text" class="search-input" id="search-input" placeholder="Search by name, email, or user ID..." />
          <select class="filter-select" id="filter-role">
            <option value="">All Roles</option>
            <option value="Admin">MIS Administrator</option>
            <option value="Staff_Tenant">Staff — Apartments</option>
            <option value="Staff_Damayan">Staff — Damayan</option>
            <option value="Staff_Female">Staff — Islamic Education</option>
            <option value="Staff_Male">Staff — Da'wah (Male)</option>
            <option value="Guest">Regular User (Guest)</option>
            <option value="Tenant">Apartment Tenant</option>
          </select>
          <select class="filter-select" id="filter-department">
            <option value="">All Departments</option>
            <option value="MIS">MIS Administration</option>
            <option value="Apartments">Apartments</option>
            <option value="Damayan">Damayan</option>
            <option value="Education">Islamic Education</option>
            <option value="Dawah">Da'wah</option>
            <option value="Resident">Residents</option>
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
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z" />
              </svg>
              Registered Users & Assigned Roles
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
                    <th>Assigned Role</th>
                    <th>Department</th>
                    <th>Profile Completion</th>
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
        <button class="modal-close"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body" id="detail-body"></div>
      <div class="modal-footer" id="detail-footer">
        <button class="btn-topbar" onclick="closeModal('user-detail-modal')">Close</button>
      </div>
    </div>
  </div>

  <!-- ═══ CHANGE ROLE MODAL ═══ -->
  <div class="modal-backdrop" id="role-modal" style="display:none;">
    <div class="modal-content" style="max-width:480px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>Assign Department Role</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="role-target-id" />
        <div style="background:#f8faf9;padding:12px;border-radius:8px;border:1px solid var(--border);margin-bottom:16px;">
          <div style="font-weight:700;font-size:0.95rem;color:var(--text-main);" id="role-target-name">Target User</div>
          <div style="font-size:0.8rem;color:var(--text-muted);" id="role-target-email">user@email.com</div>
        </div>

        <div style="margin-bottom:16px;">
          <label style="font-size:0.75rem;font-weight:700;color:var(--text-muted);display:block;margin-bottom:6px;text-transform:uppercase;">Select Department / Role</label>
          <select id="role-select" class="filter-select" style="width:100%;height:40px;font-size:0.88rem;">
            <option value="Admin">MIS Administrator (Full System Control)</option>
            <option value="Staff_Tenant">Apartments Department Staff</option>
            <option value="Staff_Damayan">Damayan & Funeral Services Staff</option>
            <option value="Staff_Female">Islamic Education & Da'wah (Female)</option>
            <option value="Staff_Male">Da'wah Department (Male Division)</option>
            <option value="Guest">Regular User (Guest)</option>
            <option value="Tenant">Apartment Tenant</option>
          </select>
        </div>

        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:0.78rem;color:#92400e;margin-bottom:16px;">
          <strong>Security Notice:</strong> Changing a user's role grants or modifies department-level permissions and is recorded in the permanent audit trail.
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:0.82rem;color:var(--text-main);cursor:pointer;">
          <input type="checkbox" id="role-confirm-check" />
          <span>I confirm this role modification is authorized</span>
        </label>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('role-modal')">Cancel</button>
        <button class="btn-topbar" id="save-role-btn" style="background:var(--primary);color:#fff;" onclick="submitRoleChange()">Save Role</button>
      </div>
    </div>
  </div>

  <!-- ═══ AUDIT HISTORY MODAL ═══ -->
  <div class="modal-backdrop" id="audit-trail-modal" style="display:none;">
    <div class="modal-content" style="max-width:620px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="audit-trail-title">User Audit & Role History</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
      </div>
      <div class="modal-body" id="audit-trail-body" style="max-height:400px;overflow-y:auto;">
        <div style="text-align:center;padding:30px;color:var(--text-muted);">Loading audit records...</div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('audit-trail-modal')">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('admin');

    function getDeptInfo(role) {
      const r = (role || '').trim();
      if (r === 'Admin') return { dept: 'MIS Administration', badge: 'badge-dept-apartments', label: 'MIS Admin', deptKey: 'MIS' };
      if (r === 'Staff_Tenant') return { dept: 'Apartments Department', badge: 'badge-dept-apartments', label: 'Apartments Staff', deptKey: 'Apartments' };
      if (r === 'Staff_Damayan') return { dept: 'Damayan Department', badge: 'badge-dept-damayan', label: 'Damayan Staff', deptKey: 'Damayan' };
      if (r === 'Staff_Female') return { dept: 'Islamic Education', badge: 'badge-dept-education', label: 'Education Staff', deptKey: 'Education' };
      if (r === 'Staff_Male') return { dept: 'Da\'wah Department', badge: 'badge-dept-dawah', label: 'Da\'wah Staff', deptKey: 'Dawah' };
      if (r === 'Tenant') return { dept: 'Apartments (Tenant)', badge: 'badge-dept-apartments', label: 'Apartment Tenant', deptKey: 'Apartments' };
      return { dept: 'General Resident', badge: 'badge-status badge-pending', label: 'Guest', deptKey: 'Resident' };
    }

    <?php
    $jsUsers = [];
    foreach ($users ?? [] as $u) {
        if (($u['tenant_id'] ?? '') == ($_SESSION['user_id'] ?? '')) continue;
        
        $sex = $u['addinfo_sex'] ?: $u['account_sex'];
        $profilePct = 30; 
        if (!empty($sex)) $profilePct += 70; 
        
        $fullName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
        if (!$fullName) $fullName = 'Unknown';

        $isActive = true;
        if (isset($u['is_verified']) && $u['is_verified'] == 0) {
            $isActive = false;
        }

        $jsUsers[] = [
            'id' => $u['tenant_id'] ?? '',
            'name' => $fullName,
            'email' => $u['email'] ?? '',
            'gender' => strtolower($sex ?? 'none'),
            'phone' => $u['contactnum'] ?? '',
            'role' => $u['role'] ?? 'Guest',
            'status' => $isActive ? 'active' : 'inactive',
            'joined' => $u['date_applied'] ?? date('Y-m-d'),
            'profilePct' => $profilePct
        ];
    }
    ?>
    let allUsers = <?= json_encode($jsUsers, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    // Stats
    function updateStats(users) {
      const total = users.length;
      const active = users.filter(u => u.status === 'active').length;
      const staff = users.filter(u => !['Guest', 'Tenant', 'User', ''].includes(u.role)).length;
      const inactive = users.filter(u => u.status === 'inactive').length;

      document.getElementById('stat-total').textContent = total;
      document.getElementById('stat-total-val').textContent = total;
      document.getElementById('stat-active').textContent = active;
      document.getElementById('stat-active-val').textContent = active;
      document.getElementById('stat-staff-count').textContent = staff;
      document.getElementById('stat-staff-val').textContent = staff;
      document.getElementById('stat-inactive').textContent = inactive;
      document.getElementById('stat-inactive-val').textContent = inactive;
    }
    updateStats(allUsers);

    // Render table
    function renderTable(users) {
      const tbody = document.getElementById('users-tbody');
      document.getElementById('users-count').textContent = users.length + ' record' + (users.length !== 1 ? 's' : '');

      if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:28px;color:var(--text-muted);">No user accounts found matching your filters.</td></tr>';
        return;
      }

      tbody.innerHTML = users.map(u => {
        const info = getDeptInfo(u.role);
        const pctColor = u.profilePct >= 100 ? 'var(--success)' : u.profilePct >= 50 ? 'var(--accent)' : 'var(--danger)';
        return `<tr>
        <td class="td-id">${u.id}</td>
        <td>
          <div class="user-cell">
            <div>
              <div class="user-name">${u.name}</div>
              <div class="user-email">${u.email}</div>
            </div>
          </div>
        </td>
        <td><span class="${info.badge}">${info.label}</span></td>
        <td style="font-weight:600;font-size:0.82rem;color:var(--text-muted);">${info.dept}</td>
        <td>
          <div class="profile-bar-wrap"><div class="profile-bar-fill" style="width:${u.profilePct}%;background:${pctColor};"></div></div>
          <span style="font-size:0.78rem;font-weight:600;color:${pctColor};">${u.profilePct}%</span>
        </td>
        <td><span class="badge-status ${badgeClass(u.status)}">${statusLabel(u.status)}</span></td>
        <td style="font-size:0.82rem;color:var(--text-muted);">${formatDate(u.joined)}</td>
        <td>
          <div class="action-menu">
            <button class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
              <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
            </button>
            <div class="action-menu-dropdown">
              <button class="action-menu-item" onclick="viewUser('${u.id}')">
                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                View Details
              </button>
              <button class="action-menu-item" onclick="openRoleModal('${u.id}')">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Assign / Change Role
              </button>
              <button class="action-menu-item" onclick="openAuditTrailModal('${u.id}')">
                <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                Role & Audit History
              </button>
              <button class="action-menu-item" onclick="toggleStatus('${u.id}')">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                ${u.status === 'active' ? 'Deactivate Account' : 'Activate Account'}
              </button>
            </div>
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
      const dept = document.getElementById('filter-department').value;
      const status = document.getElementById('filter-status').value;
      const gender = document.getElementById('filter-gender').value;

      let filtered = allUsers;
      if (search) {
        filtered = filtered.filter(u =>
          u.name.toLowerCase().includes(search) ||
          u.email.toLowerCase().includes(search) ||
          String(u.id).toLowerCase().includes(search)
        );
      }
      if (role) {
        filtered = filtered.filter(u => u.role.toLowerCase() === role.toLowerCase());
      }
      if (dept) {
        filtered = filtered.filter(u => getDeptInfo(u.role).deptKey === dept);
      }
      if (status) {
        filtered = filtered.filter(u => u.status === status);
      }
      if (gender) {
        filtered = filtered.filter(u => u.gender === gender);
      }

      renderTable(filtered);
    }

    document.getElementById('search-input').addEventListener('input', applyFilters);
    document.getElementById('filter-role').addEventListener('change', applyFilters);
    document.getElementById('filter-department').addEventListener('change', applyFilters);
    document.getElementById('filter-status').addEventListener('change', applyFilters);
    document.getElementById('filter-gender').addEventListener('change', applyFilters);

    // View user detail
    function viewUser(id) {
      const u = allUsers.find(usr => String(usr.id) === String(id));
      if (!u) return;
      const info = getDeptInfo(u.role);
      const pctColor = u.profilePct >= 100 ? 'var(--success)' : u.profilePct >= 50 ? 'var(--accent)' : 'var(--danger)';

      document.getElementById('detail-title').textContent = 'User Profile & Department — #' + u.id;
      document.getElementById('detail-body').innerHTML = `
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:var(--primary-dark);">${u.name}</div>
          <div style="font-size:0.82rem;color:var(--text-muted);">${u.email}</div>
        </div>
      </div>
      <div class="detail-grid">
        <div class="detail-item"><label>User ID</label><p style="font-family:monospace;color:var(--text-muted);">#${u.id}</p></div>
        <div class="detail-item"><label>Department</label><p style="font-weight:700;color:var(--primary);">${info.dept}</p></div>
        <div class="detail-item"><label>Role / Permissions</label><p><span class="${info.badge}">${info.label}</span></p></div>
        <div class="detail-item"><label>Gender</label><p style="text-transform:capitalize;">${u.gender || '—'}</p></div>
        <div class="detail-item"><label>Phone Contact</label><p>${u.phone || '—'}</p></div>
        <div class="detail-item"><label>Account Status</label><p><span class="badge-status ${badgeClass(u.status)}">${statusLabel(u.status)}</span></p></div>
        <div class="detail-item"><label>Registration Date</label><p>${formatDate(u.joined)}</p></div>
        <div class="detail-item"><label>Profile Completion</label>
          <p>
            <span style="font-weight:700;color:${pctColor};">${u.profilePct}%</span>
          </p>
        </div>
      </div>
      `;
      document.getElementById('detail-footer').innerHTML = `
        <button class="btn-topbar" onclick="openRoleModal('${u.id}')" style="background:var(--primary);color:#fff;">Change Role</button>
        <button class="btn-topbar" onclick="openAuditTrailModal('${u.id}')">Audit Trail</button>
        <button class="btn-topbar" onclick="closeModal('user-detail-modal')">Close</button>
      `;
      openModal('user-detail-modal');
    }

    // Open Role Modal
    function openRoleModal(id) {
      closeModal('user-detail-modal');
      const u = allUsers.find(usr => String(usr.id) === String(id));
      if (!u) return;

      document.getElementById('role-target-id').value = u.id;
      document.getElementById('role-target-name').textContent = u.name;
      document.getElementById('role-target-email').textContent = u.email + ' (Current: ' + u.role + ')';
      document.getElementById('role-select').value = u.role;
      document.getElementById('role-confirm-check').checked = false;

      openModal('role-modal');
    }

    // Submit Role Change
    function submitRoleChange() {
      const id = document.getElementById('role-target-id').value;
      const newRole = document.getElementById('role-select').value;
      const confirmed = document.getElementById('role-confirm-check').checked;

      if (!confirmed) {
        alert('Please check the confirmation box to authorize this role assignment.');
        return;
      }

      const saveBtn = document.getElementById('save-role-btn');
      saveBtn.disabled = true;
      saveBtn.textContent = 'Saving...';

      fetch('<?= url("/admin/mis_admin/update_user_role") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, role: newRole })
      })
      .then(r => r.json())
      .then(data => {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Role';
        if (data.success) {
          const u = allUsers.find(usr => String(usr.id) === String(id));
          if (u) {
            u.role = data.newRole;
          }
          closeModal('role-modal');
          showToast('✅ ' + (data.message || 'Role updated successfully'), 'var(--success)');
          updateStats(allUsers);
          applyFilters();
        } else {
          alert(data.message || 'Failed to update role.');
        }
      })
      .catch(err => {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Role';
        console.error(err);
        alert('Network communication error while updating user role.');
      });
    }

    // Open Audit Trail Modal
    function openAuditTrailModal(id) {
      closeModal('user-detail-modal');
      const u = allUsers.find(usr => String(usr.id) === String(id));
      const targetName = u ? u.name : '#' + id;

      document.getElementById('audit-trail-title').textContent = 'Audit & Role History — ' + targetName;
      const body = document.getElementById('audit-trail-body');
      body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);">Fetching audit history...</div>';
      openModal('audit-trail-modal');

      fetch('<?= url("/admin/mis_admin/user_audit_trail") ?>?id=' + encodeURIComponent(id))
      .then(r => r.json())
      .then(data => {
        if (data.success && data.trails && data.trails.length > 0) {
          body.innerHTML = data.trails.map(t => `
            <div class="audit-trail-item">
              <div class="audit-trail-head">
                <span style="color:var(--primary);">${t.action}</span>
                <span class="audit-trail-time">${formatDate(t.timestamp)}</span>
              </div>
              <div style="color:var(--text-main);">${t.details || 'No additional details logged'}</div>
              <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">Logged by: ${t.admin_name || 'System'} (${t.display_role})</div>
            </div>
          `).join('');
        } else {
          body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--text-muted);">No specific audit trails recorded for this account.</div>';
        }
      })
      .catch(err => {
        console.error(err);
        body.innerHTML = '<div style="text-align:center;padding:30px;color:var(--danger);">Error loading audit records.</div>';
      });
    }

    // Toggle user status
    function toggleStatus(id) {
      if (!confirm('Are you sure you want to change the active status of this user account?')) return;
      
      fetch('<?= url("/admin/mis_admin/toggle_user_status") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
      })
      .then(r => r.json())
      .then(data => {
          if (data.success) {
              const u = allUsers.find(usr => String(usr.id) === String(id));
              if (u) {
                  u.status = data.newStatus;
                  showToast((u.status === 'active' ? '✅ ' : '⛔ ') + u.name + ' is now ' + u.status, u.status === 'active' ? 'var(--success)' : 'var(--danger)');
                  updateStats(allUsers);
                  applyFilters();
              }
          } else {
              showToast(data.message || 'Error updating status', 'var(--danger)');
          }
      })
      .catch(e => {
          console.error(e);
          showToast('Network error', 'var(--danger)');
      });
    }

    setupModalClose('user-detail-modal');
    setupModalClose('role-modal');
    setupModalClose('audit-trail-modal');
  </script>
</body>
</html>
