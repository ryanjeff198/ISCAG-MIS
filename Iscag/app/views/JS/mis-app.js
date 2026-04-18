// ISCAG MIS App - Modular JavaScript
// ========================================

// 1. DATA STRUCTURES
const APP_DATA = {
  // User roles and profiles
  ROLES: {
    'Super Admin': { name: 'Super Admin', init: 'SA', role: 'System Administrator' },
    'Burial Manager': { name: 'Br. Khalid Hassan', init: 'KH', role: 'Burial Dept Manager' },
    'Male Counseling Manager': { name: 'Ustadh Ibrahim', init: 'UI', role: 'Male Counseling Manager' },
    'Female Counseling Manager': { name: 'Sr. Fatima Nur', init: 'FN', role: 'Female Counseling Manager' },
    'Apartment Manager': { name: 'Br. Nasser Cruz', init: 'NC', role: 'Apartment Services Manager' }
  },

  // All users data
  USERS: [
    { name: 'Super Admin', init: 'SA', role: 'System Administrator', dept: 'All Departments', lastActive: '2 min ago', status: 'Active', badge: 'badge-purple' },
    { name: 'Br. Khalid Hassan', init: 'KH', role: 'Dept Manager', dept: 'Burial Services', lastActive: '1 hr ago', status: 'Active', badge: 'badge-blue' },
    { name: 'Sr. Fatima Nur', init: 'FN', role: 'Dept Manager', dept: 'Female Counseling', lastActive: '3 hrs ago', status: 'Active', badge: 'badge-blue' },
    { name: 'Ustadh Ibrahim', init: 'UI', role: 'Dept Manager', dept: 'Male Counseling', lastActive: 'Yesterday', status: 'Active', badge: 'badge-blue' },
    { name: 'Br. Nasser Cruz', init: 'NC', role: 'Dept Manager', dept: 'Apartment Services', lastActive: 'Today', status: 'Active', badge: 'badge-blue' },
    { name: 'Sis. Aisha Hassan', init: 'AH', role: 'Staff Counselor', dept: 'Female Counseling', lastActive: '5 hrs ago', status: 'Active', badge: 'badge-gray' },
    { name: 'Br. Yusuf Santos', init: 'YS', role: 'Staff Coordinator', dept: 'Burial Services', lastActive: '2 days ago', status: 'Inactive', badge: 'badge-gray' },
    { name: 'Sr. Mariam Reyes', init: 'MR', role: 'Staff', dept: 'Apartment Services', lastActive: 'Today', status: 'Active', badge: 'badge-gray' }
  ],

  // Activity logs
  LOGS: [
    ['bi-moon-fill', '#d4edda', '#155724', 'Burial request submitted', 'Family of Ahmad Ramos · Pending verification', '5 min ago'],
    ['bi-person-plus-fill', '#d1ecf1', '#0c5460', 'New user account created', 'Sr. Mariam Reyes added · By Super Admin', '12 min ago'],
    ['bi-calendar-check', '#e2d9f3', '#4a1a8a', 'Counseling session completed', 'Client Hassan D. · Ustadh Ibrahim', '1 hr ago'],
    ['bi-building-fill', '#fde8cc', '#7a4f00', 'Tenant application approved', 'Unit 2A · Br. Ahmad Sali', '2 hrs ago'],
    ['bi-person-hearts', '#f5d4e8', '#5c1a3b', 'Session notes submitted', 'Sr. Aisha Hassan · Female Counseling', '3 hrs ago'],
    ['bi-shield-lock-fill', '#d1ecf1', '#0c5460', 'Login detected', 'Super Admin · 192.168.1.1', '5 hrs ago'],
    ['bi-check-circle-fill', '#d4edda', '#155724', 'Burial service completed', 'Family of Yusuf R. · Documents archived', 'Yesterday'],
    ['bi-x-circle-fill', '#f8d7da', '#721c24', 'Application rejected', 'Unit 3C · Ali Santos · Incomplete docs', 'Yesterday']
  ],

  // Department stats for reports
  DEPT_STATS: [
    { name: 'Burial', manager: 'Br. Khalid Hassan', total: 35, completed: 30, pending: 5, rate: 86, trend: '↑ +5%', color: 'var(--g)' },
    { name: 'Male Counseling', manager: 'Ustadh Ibrahim', total: 48, completed: 45, pending: 3, rate: 94, trend: '↑ +2%', color: '#1a3b5c' },
    { name: 'Female Counseling', manager: 'Sr. Fatima Nur', total: 52, completed: 50, pending: 2, rate: 96, trend: '↑ +4%', color: '#5c1a3b' },
    { name: 'Apartment', manager: 'Br. Nasser Cruz', total: 18, completed: 14, pending: 4, rate: 78, trend: '↓ -2%', color: '#5c4a1a' }
  ],

  // Page titles
  PAGE_TITLES: {
    dashboard: ['Dashboard', 'ISCAG MIS › Overview'],
    users: ['Users & Access Control', 'ISCAG MIS › Administration › Users'],
    reports: ['Reports & Analytics', 'ISCAG MIS › Administration › Reports'],
    activity: ['Activity Log', 'ISCAG MIS › Administration › Activity Log'],
    burial: ['Burial Services', 'ISCAG MIS › Burial Services'],
    mcounseling: ['Male Counseling', 'ISCAG MIS › Male Counseling'],
    fcounseling: ['Female Counseling', 'ISCAG MIS › Female Counseling'],
    apartment: ['Apartment Services', 'ISCAG MIS › Apartment Services'],
    settings: ['System Settings', 'ISCAG MIS › Settings']
  }
};

// 2. APP STATE
let appState = {
  currentRole: 'Super Admin',
  currentPage: 'dashboard',
  currentUser: null
};

// 3. UTILITY FUNCTIONS
const Utils = {
  // Create HTML element
  el(tag, className = '', content = '') {
    const el = document.createElement(tag);
    if (className) el.className = className;
    el.innerHTML = content;
    return el;
  },

  // Escape HTML
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  },

  // Show toast notification
  showToast(msg) {
    const toast = document.getElementById('toast');
    const msgEl = document.getElementById('toast-msg');
    msgEl.textContent = msg;
    toast.style.display = 'flex';
    setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }
};

// 4. TEMPLATE FUNCTIONS
const Templates = {
  // Generic table renderer
  table(data, columns) {
    let html = '<table class="tbl"><thead><tr>';
    columns.forEach(col => html += `<th>${col}</th>`);
    html += '</tr></thead><tbody>';
    
    data.forEach(row => {
      html += '<tr>';
      columns.forEach((col, i) => {
        const cell = row[i];
        if (Array.isArray(cell)) {
          html += `<td>${cell.join(' ')}</td>`;
        } else {
          html += `<td>${cell}</td>`;
        }
      });
      html += '</tr>';
    });
    
    html += '</tbody></table>';
    return html;
  },

  // Timeline items
  timelineItem(icon, bg, color, action, meta, time) {
    return `
      <div class="tl-item">
        <div class="tl-ico" style="background:${bg};color:${color};">
          <i class="bi ${icon}"></i>
        </div>
        <div class="tl-body">
          <div class="tl-action">${action}</div>
          <div class="tl-meta">${meta}</div>
        </div>
      </div>
    `;
  },

  // Stat card
  statCard(icon, val, label, change, changeType) {
    return `
      <div class="stat-card ${icon}">
        <div class="stat-val">${val}</div>
        <div class="stat-lbl">${label}</div>
        <div class="stat-change ${changeType}">${change}</div>
      </div>
    `;
  },

  // User row in table
  userRow(user) {
    return `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="mini-avatar">${user.init}</div>
            <div>
              <div class="tbl-name">${Utils.escapeHtml(user.name)}</div>
            </div>
          </div>
        </td>
        <td><span class="badge ${user.badge}">${Utils.escapeHtml(user.role)}</span></td>
        <td>${Utils.escapeHtml(user.dept)}</td>
        <td style="font-size:12px;color:var(--txt3);">${Utils.escapeHtml(user.lastActive)}</td>
        <td><span class="badge ${user.status === 'Active' ? 'badge-green' : 'badge-gray'}">${user.status}</span></td>
        <td>
          <div style="display:flex;gap:6px;">
            <button class="btn btn-sm btn-ghost"><i class="bi bi-pencil-fill"></i></button>
            <button class="btn btn-sm btn-danger"><i class="bi bi-trash-fill"></i></button>
          </div>
        </td>
      </tr>
    `;
  }
};

// 5. PAGE FUNCTIONS
const Pages = {
  dashboard() {
    const sa = appState.currentRole === 'Super Admin';
    let html = '';

    // Super Admin stats
    if (sa) {
      html += `
        <div class="stats-row cols-4">
          ${Templates.statCard('green', '247', 'Total Active Users', '↑ 12 this month', 'up')}
          ${Templates.statCard('gold', '38', 'Pending Requests', '↑ 5 new today', 'dn')}
          ${Templates.statCard('blue', '12', "Today's Sessions", '↑ 3 vs yesterday', 'up')}
          ${Templates.statCard('rose', '94%', 'Service Completion', '↑ 2% this week', 'up')}
        </div>
        <div class="qa-grid">
          <div class="qa-card" onclick="App.nav('burial')">
            <div class="qa-ico" style="background:#d4edda;color:#155724;"><i class="bi bi-moon-fill"></i></div>
            <div class="qa-label">Burial Services</div><span class="badge badge-red">3 Pending</span>
          </div>
          <div class="qa-card" onclick="App.nav('mcounseling')">
            <div class="qa-ico" style="background:#d1ecf1;color:#0c5460;"><i class="bi bi-person-fill"></i></div>
            <div class="qa-label">Male Counseling</div><span class="badge badge-yellow">8 Today</span>
          </div>
          <div class="qa-card" onclick="App.nav('fcounseling')">
            <div class="qa-ico" style="background:#f5d4e8;color:#5c1a3b;"><i class="bi bi-person-hearts"></i></div>
            <div class="qa-label">Female Counseling</div><span class="badge badge-blue">4 Today</span>
          </div>
          <div class="qa-card" onclick="App.nav('apartment')">
            <div class="qa-ico" style="background:#fde8cc;color:#7a4f00;"><i class="bi bi-building-fill"></i></div>
            <div class="qa-label">Apartment Services</div><span class="badge badge-yellow">6 Applications</span>
          </div>
        </div>
      `;
    }

    // Recent requests + activity (common to all)
    html += `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-title">Recent Requests</div>
            <button class="btn btn-sm btn-outline" onclick="App.showModal('burial-modal')">
              <i class="bi bi-plus"></i> New
            </button>
          </div>
          <div class="card-body" style="padding:0;">
            <table class="tbl">
              <thead><tr><th>Client</th><th>Dept</th><th>Status</th><th>Date</th></tr></thead>
              <tbody>
                <tr><td><div class="tbl-name">Ahmad Ramos</div></td><td><span class="badge badge-green">Burial</span></td><td><span class="badge badge-yellow">Pending</span></td><td>Mar 28</td></tr>
                <tr><td><div class="tbl-name">Hassan Dela Cruz</div></td><td><span class="badge badge-blue">Counseling</span></td><td><span class="badge badge-green">Confirmed</span></td><td>Mar 28</td></tr>
                <tr><td><div class="tbl-name">Fatima Santos</div></td><td><span class="badge badge-purple">Apartment</span></td><td><span class="badge badge-yellow">Review</span></td><td>Mar 27</td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-hd-title">Recent Activity</div></div>
          <div class="card-body">
            <div class="timeline">
    `;

    // Add timeline items
    for (let log of APP_DATA.LOGS.slice(0, 5)) {
      html += Templates.timelineItem(...log);
    }

    html += `
            </div>
          </div>
        </div>
      </div>
    `;

    if (sa) {
      // Department progress bars
      html += `
        <div class="card">
          <div class="card-hd"><div class="card-hd-title">Department Performance — March 2025</div></div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;">
    `;
      
      APP_DATA.DEPT_STATS.forEach(dept => {
        html += `
          <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span style="font-size:12px;font-weight:600;">${dept.name}</span>
              <span style="font-size:12px;font-weight:700;color:${dept.color};">${dept.rate}%</span>
            </div>
            <div class="progress">
              <div class="progress-fill" style="width:${dept.rate}%;background:linear-gradient(90deg,${dept.color},${dept.color.replace(/#(.)./, '#$1f')});"></div>
            </div>
            <div style="font-size:11px;color:var(--txt3);margin-top:5px;">${dept.total} requests · ${dept.completed} completed</div>
          </div>
        `;
      });

      html += `</div></div></div>`;
    }

    return html;
  },

  users() {
    return `
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div style="display:flex;gap:8px;">
          <input class="field-input" placeholder="Search users…" style="width:220px;"/>
          <select class="field-input" style="width:160px;">
            <option>All Departments</option><option>Burial</option><option>Male Counseling</option>
            <option>Female Counseling</option><option>Apartment</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="App.showModal('add-user-modal')">
          <i class="bi bi-person-plus-fill"></i> Add User
        </button>
      </div>
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-title">All System Users <span class="badge badge-green" style="margin-left:8px;">8 Active</span></div>
        </div>
        <div class="card-body" style="padding:0;">
          <table class="tbl">
            <thead>
              <tr><th>User</th><th>Role</th><th>Department</th><th>Last Active</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              ${APP_DATA.USERS.map(user => Templates.userRow(user)).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // Placeholder for other pages - implement similarly
  reports() { return 'Reports page coming soon...'; },
  activity() { return 'Activity page coming soon...'; },
  burial() { return 'Burial Services page coming soon...'; },
  mcounseling() { return 'Male Counseling page coming soon...'; },
  fcounseling() { return 'Female Counseling page coming soon...'; },
  apartment() { return 'Apartment Services page coming soon...'; },
  settings() { return 'Settings page coming soon...'; }
};

// 6. MAIN APP OBJECT
const App = {
  // Initialize app
  init() {
    this.bindEvents();
    // Auto-login for demo
    this.doLogin('Super Admin');
  },

  // Event bindings
  bindEvents() {
    // Role selection
    document.querySelectorAll('.role-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const role = e.currentTarget.dataset.role || e.currentTarget.textContent.trim();
        App.selectRole(e.currentTarget, role);
      });
    });

    // Login button
    document.querySelector('.btn-login-submit').addEventListener('click', () => App.doLogin());

    // Logout
    document.getElementById('sidebar').addEventListener('click', (e) => {
      if (e.target.closest('.sb-link')?.textContent.includes('Sign Out')) {
        App.doLogout();
      }
    });

    // Global modal close/ notifications
    document.addEventListener('click', App.handleGlobalClicks);
  },

  // Navigation
  nav(page) {
    appState.currentPage = page;
    document.querySelectorAll('.sb-link').forEach(link => link.classList.remove('active'));
    const navLink = document.getElementById('nl-' + page);
    if (navLink) navLink.classList.add('active');

    const titles = APP_DATA.PAGE_TITLES[page] || ['Dashboard', 'ISCAG MIS'];
    document.getElementById('page-title').textContent = titles[0];
    document.getElementById('page-breadcrumb').textContent = titles[1];

    const pageContent = Pages[page] ? Pages[page]() : '<div style="padding:40px;text-align:center;color:var(--txt3);">Coming soon</div>';
    document.getElementById('main-content').innerHTML = pageContent;
  },

  // Role selection
  selectRole(btn, role) {
    document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    appState.currentRole = role;
  },

  // Login
  doLogin(role = appState.currentRole) {
    const user = APP_DATA.ROLES[role];
    if (!user) return;

    appState.currentUser = user;
    document.getElementById('login-screen').style.display = 'none';
    document.getElementById('app').classList.add('visible');

    // Update UI
    document.getElementById('sb-avatar').textContent = user.init;
    document.getElementById('sb-user-name').textContent = user.name;
    document.getElementById('sb-user-role').textContent = user.role;
    
    document.getElementById('modal-avatar').textContent = user.init;
    document.getElementById('modal-name').textContent = user.name;
    document.getElementById('modal-role-d').textContent = user.role;

    App.buildSidebar();
    App.nav('dashboard');
  },

  // Logout
  doLogout() {
    document.getElementById('app').classList.remove('visible');
    document.getElementById('login-screen').style.display = 'flex';
    appState.currentUser = null;
  },

  // Build role-based sidebar
  buildSidebar() {
    const sa = appState.currentRole === 'Super Admin';
    let html = `
      <div class="sb-section-hd">Main</div>
      <div class="sb-link" onclick="App.nav('dashboard')" id="nl-dashboard">
        <i class="bi bi-grid-1x2-fill"></i>Dashboard
      </div>
    `;

    if (sa) {
      html += `
        <div class="sb-link" onclick="App.nav('users')" id="nl-users">
          <i class="bi bi-people-fill"></i>Users & Access
        </div>
        <div class="sb-link" onclick="App.nav('reports')" id="nl-reports">
          <i class="bi bi-bar-chart-fill"></i>Reports & Analytics
        </div>
        <div class="sb-link" onclick="App.nav('activity')" id="nl-activity">
          <i class="bi bi-clock-history"></i>Activity Log
        </div>
      `;
    }

    html += '<div class="sb-section-hd">Departments</div>';

    if (sa || appState.currentRole === 'Burial Manager') {
      html += `
        <div class="sb-link" onclick="App.nav('burial')" id="nl-burial">
          <i class="bi bi-moon-fill" style="color:#7de8a8;"></i>Burial Services
        </div>
      `;
    }
    if (sa || appState.currentRole === 'Male Counseling Manager') {
      html += `
        <div class="sb-link" onclick="App.nav('mcounseling')" id="nl-mcounseling">
          <i class="bi bi-person-fill" style="color:#7ab8e8;"></i>Male Counseling
        </div>
      `;
    }
    // ... other departments (similar pattern)

    if (sa) {
      html += `
        <div class="sb-section-hd">System</div>
        <div class="sb-link" onclick="App.nav('settings')" id="nl-settings">
          <i class="bi bi-gear-fill"></i>Settings
        </div>
      `;
    }

    document.getElementById('sidebar-nav').innerHTML = html;
  },

  // Modal functions
  showModal(id) {
    document.getElementById(id).style.display = 'flex';
  },

  closeModal(id) {
    document.getElementById(id).style.display = 'none';
  },

  // Global click handler
  handleGlobalClicks(e) {
    // Close modals on overlay click
    if (e.target.classList.contains('modal-overlay')) {
      App.closeModal(e.target.id);
    }
    
    // Close notifications
    const notifPanel = document.getElementById('notif-panel');
    const notifBtn = document.getElementById('notif-btn');
    if (notifPanel && !notifPanel.contains(e.target) && notifBtn && !notifBtn.contains(e.target)) {
      notifPanel.classList.remove('open');
    }
  },

  // Toggle notifications
  toggleNotif() {
    document.getElementById('notif-panel').classList.toggle('open');
  }
};

// 7. MODAL EVENT HANDLERS (Fix close buttons)
document.addEventListener('DOMContentLoaded', () => {
  App.init();
  
  // Modal close handlers (fix non-functional close buttons)
  document.querySelectorAll('.modal-overlay .btn-close-modal, .modal-overlay .btn-ghost').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const modalId = btn.closest('.modal-overlay').id;
      App.closeModal(modalId);
    });
  });

  // Modal overlay click to close
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) App.closeModal(overlay.id);
    });
  });

  // Form submit buttons
  document.querySelectorAll('.modal-foot .btn-primary').forEach(btn => {
    btn.addEventListener('click', () => {
      const modalId = btn.closest('.modal-overlay').id;
      App.closeModal(modalId);
      Utils.showToast('Action completed successfully');
    });
  });
});

