/**
 * ISCAG MIS — Admin Shared Module
 * Provides RBAC, data layer, sidebar logic, toast, and utilities
 * for both MIS Admin and Staff Admin dashboards.
 * 
 * References existing localStorage keys from the user profile system:
 *   mis_user, mis_requests, mis_apartments, mis_data_init
 */

// ══════════════════════════════════════
//  STORAGE & DATA LAYER
// ══════════════════════════════════════
const STORAGE_KEYS = {
  user:         'mis_user',
  requests:     'mis_requests',
  apartments:   'mis_apartments',
  initialized:  'mis_data_init',
  adminRole:    'mis_admin_role',
  allUsers:     'mis_all_users',
  billing:      'mis_billing',
  activityLog:  'mis_activity_log',
  staffList:    'mis_staff_list',
  reports:      'mis_reports',
  notifications:'mis_notifications',
  proofpayment: 'mis_proofpayment'
};

const PROFILE_FIELDS = ['name','email','gender','phone','address','dob','civil','occupation','arabicName','revertYear'];

const FIELD_LABELS = {
  name:'Full Name', email:'Email Address', gender:'Gender',
  phone:'Contact Number', address:'Complete Address', dob:'Date of Birth',
  civil:'Civil Status', occupation:'Occupation',
  arabicName:'Muslim / Arabic Name', revertYear:'Year Reverted'
};

// ── Default data (matches user dashboard defaults) ──
const DEFAULT_USER = {
  id:'USR-001', name:'Muhammad Usman', email:'musman@example.com',
  gender:'', phone:'', address:'', dob:'', civil:'', occupation:'',
  arabicName:'', membership:'', revertYear:'', apartment:'', profileComplete:false
};

const DEFAULT_APARTMENTS = [
  { id:'APT-A1', name:'Unit A-1 · Studio',      price:3500,  available:2, status:'available' },
  { id:'APT-A2', name:'Unit A-2 · 1-Bedroom',   price:5000,  available:1, status:'available' },
  { id:'APT-B1', name:'Unit B-1 · 2-Bedroom',   price:7500,  available:0, status:'occupied'  },
  { id:'APT-B2', name:'Unit B-2 · 2-Bedroom',   price:7500,  available:1, status:'available' },
  { id:'APT-C1', name:'Unit C-1 · Family Suite', price:10000, available:0, status:'reserved'  }
];

const DEFAULT_REQUESTS = [
  { id:'BUR-001', user:'USR-001', name:'Muhammad Usman',   type:'burial_service',        status:'pending',  date:'2026-03-15', updatedAt:'2026-03-15' },
  { id:'APT-001', user:'USR-001', name:'Muhammad Usman',   type:'apartment_application', status:'approved', date:'2026-03-09', updatedAt:'2026-03-12' },
  { id:'APT-002', user:'USR-002', name:'Fatimah Binti Ali', type:'apartment_application', status:'pending',  date:'2026-03-28', updatedAt:'2026-03-28' },
  { id:'APT-003', user:'USR-003', name:'Ahmad Saleh',       type:'apartment_application', status:'pending',  date:'2026-04-02', updatedAt:'2026-04-02' },
  { id:'COU-001', user:'USR-004', name:'Aisha Rahman',      type:'counseling_female',     status:'scheduled', date:'2026-04-05', updatedAt:'2026-04-05' },
  { id:'COU-002', user:'USR-005', name:'Omar Farooq',       type:'counseling_male',       status:'pending',  date:'2026-04-06', updatedAt:'2026-04-06' },
  { id:'BUR-002', user:'USR-006', name:'Ibrahim Datu',      type:'burial_service',        status:'completed', date:'2026-02-20', updatedAt:'2026-02-22' },
  { id:'EDU-001', user:'USR-007', name:'Khadijah Lim',      type:'islamic_education',     status:'active',   date:'2026-03-01', updatedAt:'2026-03-01' },
  { id:'MAR-001', user:'USR-008', name:'Yusuf Santos',      type:'marriage_service',      status:'approved', date:'2026-03-20', updatedAt:'2026-03-22' }
];

const DEFAULT_ALL_USERS = [
  { id:'USR-001', name:'Muhammad Usman',   email:'musman@example.com',    role:'user',  gender:'male',   status:'active',   phone:'+63 917 123 4567', profilePct:40,  joined:'2026-01-15' },
  { id:'USR-002', name:'Fatimah Binti Ali', email:'fatimah@example.com',   role:'user',  gender:'female', status:'active',   phone:'+63 918 234 5678', profilePct:100, joined:'2026-02-03' },
  { id:'USR-003', name:'Ahmad Saleh',       email:'asaleh@example.com',    role:'user',  gender:'male',   status:'active',   phone:'+63 919 345 6789', profilePct:80,  joined:'2026-02-15' },
  { id:'USR-004', name:'Aisha Rahman',      email:'aisha.r@example.com',   role:'user',  gender:'female', status:'active',   phone:'+63 920 456 7890', profilePct:100, joined:'2026-02-20' },
  { id:'USR-005', name:'Omar Farooq',       email:'ofarooq@example.com',   role:'user',  gender:'male',   status:'active',   phone:'+63 921 567 8901', profilePct:70,  joined:'2026-03-01' },
  { id:'USR-006', name:'Ibrahim Datu',      email:'idatu@example.com',     role:'user',  gender:'male',   status:'inactive', phone:'+63 922 678 9012', profilePct:100, joined:'2025-11-10' },
  { id:'USR-007', name:'Khadijah Lim',      email:'klim@example.com',      role:'user',  gender:'female', status:'active',   phone:'+63 923 789 0123', profilePct:90,  joined:'2026-03-05' },
  { id:'USR-008', name:'Yusuf Santos',      email:'ysantos@example.com',   role:'user',  gender:'male',   status:'active',   phone:'+63 924 890 1234', profilePct:100, joined:'2026-03-10' },
  { id:'USR-009', name:'Maryam Tan',        email:'mtan@example.com',      role:'user',  gender:'female', status:'active',   phone:'+63 925 901 2345', profilePct:60,  joined:'2026-03-18' },
  { id:'USR-010', name:'Hassan Macarambon', email:'hmacarambon@example.com',role:'user', gender:'male',   status:'active',   phone:'+63 926 012 3456', profilePct:100, joined:'2026-03-25' }
];

const DEFAULT_STAFF = [
  { id:'STF-001', name:'Abdul Karim',    email:'akarim@iscag.org',   department:'Apartment',   status:'active', joined:'2025-06-01' },
  { id:'STF-002', name:'Siti Nurhaliza', email:'snurhaliza@iscag.org', department:"Da'wah (F)", status:'active', joined:'2025-08-15' },
  { id:'STF-003', name:'Rashid Pendatun',email:'rpendatun@iscag.org', department:'Damayan',     status:'active', joined:'2025-09-01' },
  { id:'STF-004', name:'Amira Lucman',   email:'alucman@iscag.org',   department:"Da'wah (M)", status:'active', joined:'2025-10-20' }
];

const DEFAULT_BILLING = [
  { id:'BIL-001', userId:'USR-001', name:'Muhammad Usman',  type:'Apartment Rent', amount:5000,  status:'paid',    dueDate:'2026-04-01', paidDate:'2026-03-28' },
  { id:'BIL-002', userId:'USR-002', name:'Fatimah Binti Ali',type:'Apartment Rent', amount:3500,  status:'paid',    dueDate:'2026-04-01', paidDate:'2026-03-30' },
  { id:'BIL-003', userId:'USR-003', name:'Ahmad Saleh',      type:'Apartment Rent', amount:7500,  status:'overdue', dueDate:'2026-04-01', paidDate:null },
  { id:'BIL-004', userId:'USR-008', name:'Yusuf Santos',     type:'Burial Service', amount:15000, status:'paid',    dueDate:'2026-03-25', paidDate:'2026-03-25' },
  { id:'BIL-005', userId:'USR-004', name:'Aisha Rahman',     type:'Apartment Rent', amount:5000,  status:'pending', dueDate:'2026-05-01', paidDate:null }
];

const DEFAULT_ACTIVITY_LOG = [
  { action:'Application approved',       detail:'APT-001 — Muhammad Usman apartment application approved', actor:'MIS Admin',  time:'2026-04-10T06:30:00Z', type:'approve' },
  { action:'New user registered',        detail:'Maryam Tan (USR-009) completed registration',             actor:'System',     time:'2026-04-09T14:20:00Z', type:'user' },
  { action:'Payment received',           detail:'BIL-001 — ₱5,000 rent payment from Muhammad Usman',       actor:'System',     time:'2026-04-08T09:15:00Z', type:'payment' },
  { action:'Burial request submitted',   detail:'BUR-001 — New burial service request from Muhammad Usman', actor:'Muhammad Usman', time:'2026-04-07T11:00:00Z', type:'request' },
  { action:'Unit status updated',        detail:'APT-B1 marked as occupied',                                actor:'Staff Admin', time:'2026-04-06T16:45:00Z', type:'update' },
  { action:'Counseling session scheduled', detail:'COU-001 — Sisters counseling for Aisha Rahman',          actor:'Staff Admin', time:'2026-04-05T08:30:00Z', type:'schedule' },
  { action:'Staff account created',      detail:'STF-004 — Amira Lucman assigned to Da\'wah (M)',          actor:'MIS Admin',  time:'2026-04-04T10:00:00Z', type:'staff' },
  { action:'Payment overdue',            detail:'BIL-003 — Ahmad Saleh rent payment overdue',               actor:'System',     time:'2026-04-03T00:00:00Z', type:'alert' },
  { action:'Application submitted',      detail:'APT-003 — Ahmad Saleh applied for apartment',             actor:'Ahmad Saleh', time:'2026-04-02T13:22:00Z', type:'request' },
  { action:'System backup completed',    detail:'Automated daily backup completed successfully',            actor:'System',     time:'2026-04-01T02:00:00Z', type:'system' }
];


// ══════════════════════════════════════
//  DATA ACCESS
// ══════════════════════════════════════
function initAdminData() {
  // Seed shared data if not yet initialized
  if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
    localStorage.setItem(STORAGE_KEYS.apartments, JSON.stringify(DEFAULT_APARTMENTS));
    localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(DEFAULT_REQUESTS));
    localStorage.setItem(STORAGE_KEYS.initialized, '1');
  }
  // Seed admin-specific data
  if (!localStorage.getItem(STORAGE_KEYS.allUsers)) {
    localStorage.setItem(STORAGE_KEYS.allUsers, JSON.stringify(DEFAULT_ALL_USERS));
  }
  if (!localStorage.getItem(STORAGE_KEYS.staffList)) {
    localStorage.setItem(STORAGE_KEYS.staffList, JSON.stringify(DEFAULT_STAFF));
  }
  if (!localStorage.getItem(STORAGE_KEYS.billing)) {
    localStorage.setItem(STORAGE_KEYS.billing, JSON.stringify(DEFAULT_BILLING));
  }
  if (!localStorage.getItem(STORAGE_KEYS.activityLog)) {
    localStorage.setItem(STORAGE_KEYS.activityLog, JSON.stringify(DEFAULT_ACTIVITY_LOG));
  }
}

function getUser() {
  const raw = localStorage.getItem(STORAGE_KEYS.user);
  return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
}

function getAllUsers() {
  const raw = localStorage.getItem(STORAGE_KEYS.allUsers);
  return raw ? JSON.parse(raw) : [...DEFAULT_ALL_USERS];
}

function getRequests() {
  const raw = localStorage.getItem(STORAGE_KEYS.requests);
  return raw ? JSON.parse(raw) : [...DEFAULT_REQUESTS];
}

function getApartments() {
  const raw = localStorage.getItem(STORAGE_KEYS.apartments);
  return raw ? JSON.parse(raw) : [...DEFAULT_APARTMENTS];
}

function getStaffList() {
  const raw = localStorage.getItem(STORAGE_KEYS.staffList);
  return raw ? JSON.parse(raw) : [...DEFAULT_STAFF];
}

function getBilling() {
  const raw = localStorage.getItem(STORAGE_KEYS.billing);
  return raw ? JSON.parse(raw) : [...DEFAULT_BILLING];
}

function getActivityLog() {
  const raw = localStorage.getItem(STORAGE_KEYS.activityLog);
  return raw ? JSON.parse(raw) : [...DEFAULT_ACTIVITY_LOG];
}

function saveRequests(data) { localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(data)); }
function saveAllUsers(data) { localStorage.setItem(STORAGE_KEYS.allUsers, JSON.stringify(data)); }
function saveApartments(data) { localStorage.setItem(STORAGE_KEYS.apartments, JSON.stringify(data)); }
function saveBilling(data) { localStorage.setItem(STORAGE_KEYS.billing, JSON.stringify(data)); }
function saveStaffList(data) { localStorage.setItem(STORAGE_KEYS.staffList, JSON.stringify(data)); }

function addActivityEntry(action, detail, actor, type) {
  const log = getActivityLog();
  log.unshift({ action, detail, actor, time: new Date().toISOString(), type: type || 'update' });
  if (log.length > 50) log.length = 50;
  localStorage.setItem(STORAGE_KEYS.activityLog, JSON.stringify(log));
}

function getProfileCompletion(user) {
  if (!user) user = getUser();
  let filled = 0;
  const missing = [];
  PROFILE_FIELDS.forEach(k => {
    if (user[k] && String(user[k]).trim() !== '') { filled++; }
    else { missing.push(FIELD_LABELS[k] || k); }
  });
  return {
    percentage: Math.round((filled / PROFILE_FIELDS.length) * 100),
    filled, total: PROFILE_FIELDS.length, missingFields: missing
  };
}


// ══════════════════════════════════════
//  RBAC — Role-Based Access Control
// ══════════════════════════════════════
const ROLES = {
  MIS_ADMIN: 'mis_admin',
  STAFF_ADMIN: 'staff_admin',
  APARTMENT_ADMIN: 'apartment_admin',
  STAFF_TENANT: 'Staff_Tenant'
};

const PERMISSIONS = {
  view_all_users:       [ROLES.MIS_ADMIN],
  manage_users:         [ROLES.MIS_ADMIN],
  approve_applications: [ROLES.MIS_ADMIN],
  reject_applications:  [ROLES.MIS_ADMIN],
  manage_availability:  [ROLES.MIS_ADMIN, ROLES.STAFF_ADMIN, ROLES.STAFF_TENANT],
  view_billing:         [ROLES.MIS_ADMIN, ROLES.STAFF_ADMIN, ROLES.STAFF_TENANT],
  modify_billing:       [ROLES.MIS_ADMIN],
  access_reports:       [ROLES.MIS_ADMIN],
  access_logs:          [ROLES.MIS_ADMIN],
  manage_staff:         [ROLES.MIS_ADMIN],
  edit_records:         [ROLES.MIS_ADMIN],
  update_unit_status:   [ROLES.MIS_ADMIN, ROLES.STAFF_ADMIN, ROLES.STAFF_TENANT],
  communicate:          [ROLES.MIS_ADMIN, ROLES.STAFF_ADMIN, ROLES.STAFF_TENANT],
  system_settings:      [ROLES.MIS_ADMIN],
  view_assigned:        [ROLES.MIS_ADMIN, ROLES.STAFF_ADMIN, ROLES.STAFF_TENANT]
};

function checkPermission(action, role) {
  if (!PERMISSIONS[action]) return false;
  return PERMISSIONS[action].includes(role);
}

function getCurrentRole() {
  return localStorage.getItem(STORAGE_KEYS.adminRole) || ROLES.MIS_ADMIN;
}

function setCurrentRole(role) {
  localStorage.setItem(STORAGE_KEYS.adminRole, role);
}


// ══════════════════════════════════════
//  UI UTILITIES
// ══════════════════════════════════════
function showToast(msg, bg) {
  const toast = document.createElement('div');
  let finalMsg = msg;
  let iconSvg = '';

  // Auto-detect icon based on common emojis or background color
  if (msg.includes('✅')) {
    iconSvg = '<svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:white;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';
    finalMsg = msg.replace('✅', '').trim();
  } else if (msg.includes('⚠️') || msg.includes('❌') || (bg && bg.includes('danger'))) {
    iconSvg = '<svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>';
    finalMsg = msg.replace(/[⚠️❌]/g, '').trim();
  } else if (msg.includes('ℹ️') || msg.includes('📋') || (bg && bg.includes('info'))) {
    iconSvg = '<svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:white;"><path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"/></svg>';
    finalMsg = msg.replace(/[ℹ️📋]/g, '').trim();
  } else if (msg.includes('⏳')) {
    iconSvg = '<svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:white;"><path d="M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm10 14.5V20H8v-3.5l4-4 4 4zM12 11.5l-4-4V4h8v3.5l-4 4z"/></svg>';
    finalMsg = msg.replace('⏳', '').trim();
  }

  toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;">${iconSvg}<span>${finalMsg}</span></div>`;
  toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + (bg || 'var(--success)') +
    ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;' +
    'font-family:Source Sans 3,sans-serif;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);' +
    'max-width:400px;animation:modalFadeIn 0.25s ease;';
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatDateTime(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' +
    d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function timeAgo(dateStr) {
  if (!dateStr) return '—';
  const now = new Date();
  const then = new Date(dateStr);
  const diffMs = now - then;
  const seconds = Math.floor(diffMs / 1000);
  if (seconds < 60) return 'Just now';
  const mins = Math.floor(seconds / 60);
  if (mins < 60) return mins + 'm ago';
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return hrs + 'h ago';
  const days = Math.floor(hrs / 24);
  if (days < 7) return days + 'd ago';
  return formatDate(dateStr);
}

function typeLabel(type) {
  const map = {
    burial_service: 'Burial Service',
    apartment_application: 'Apartment',
    counseling_female: "Counseling",
    counseling_male: "Counseling",
    islamic_education: 'Islamic Education',
    marriage_service: 'Marriage Service'
  };
  return map[type] || type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function badgeClass(status) {
  const s = (status || '').toLowerCase().replace(/_/g,'');
  const map = {
    pending: 'badge-pending', approved: 'badge-approved',
    rejected: 'badge-rejected', completed: 'badge-completed',
    active: 'badge-active', inactive: 'badge-inactive',
    available: 'badge-available', occupied: 'badge-occupied',
    reserved: 'badge-reserved', scheduled: 'badge-scheduled',
    paid: 'badge-approved', overdue: 'badge-rejected',
    complete: 'badge-complete',
    // Tenant Report statuses
    pendingmis: 'badge-pending', revision: 'badge-rejected',
    verified: 'badge-approved', waitinglist: 'badge-pending',
    delinquent: 'badge-rejected', forverification: 'badge-pending',
    unpaid: 'badge-rejected'
  };
  return map[s] || map[status] || 'badge-info';
}

function statusLabel(status) {
  if (!status) return '—';
  return status.charAt(0).toUpperCase() + status.slice(1).replace(/_/g, ' ');
}

function currencyFormat(amount) {
  return '₱' + Number(amount).toLocaleString();
}


// ══════════════════════════════════════
//  SIDEBAR & NAVIGATION
// ══════════════════════════════════════
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebar-toggle');
  if (!sidebar || !toggle) return;

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    if (sidebar.classList.contains('collapsed')) {
      document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
      document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
    }
  });
}

function initDropdowns() {
  document.querySelectorAll('.nav-dropdown-trigger').forEach(trigger => {
    const menuId = trigger.getAttribute('data-menu');
    const menu = menuId ? document.getElementById(menuId) : trigger.nextElementSibling;
    if (!menu) return;

    trigger.addEventListener('click', () => {
      const sidebar = document.getElementById('sidebar');
      if (sidebar && sidebar.classList.contains('collapsed')) {
        const href = trigger.getAttribute('data-href');
        if (href) window.location.href = href;
        return;
      }
      const isOpen = menu.classList.contains('open');
      document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
      document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
      if (!isOpen) { menu.classList.add('open'); trigger.classList.add('open'); }
    });
  });
}

function loadUserNav() {
  // Check for staff profile first if in staff mode
  let user = getUser();
  let roleLabel = 'Staff Admin';
  const staffProfileRaw = localStorage.getItem('mis_apartment_staff_profile');
  if (staffProfileRaw) {
    const staff = JSON.parse(staffProfileRaw);
    if (staff.name) user.name = staff.name;
    if (staff.occupation) roleLabel = staff.occupation;
  }

  const navName = document.getElementById('nav-name');
  const navRole = document.querySelector('.sidebar-user .user-info span');
  const navAvatar = document.getElementById('nav-avatar');

  if (navName) navName.textContent = user.name;
  if (navRole) navRole.textContent = roleLabel;
  
  if (navAvatar) {
    const photo = localStorage.getItem('mis_apartment_photo') || localStorage.getItem('mis_user_photo');
    if (photo) {
      navAvatar.textContent = '';
      navAvatar.style.backgroundImage = 'url(' + photo + ')';
      navAvatar.style.backgroundSize = 'cover';
      navAvatar.style.backgroundPosition = 'center';
    } else {
      navAvatar.textContent = user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
    }
  }
}

/**
 * syncSessionUser() — Syncs the PHP session name to localStorage to ensure
 * the UI reflects the actual logged-in account.
 */
function syncSessionUser(sessionName, sessionEmail, sessionRole) {
  if (!sessionName) return;
  
  // Update main user
  const user = getUser();
  if (user.name !== sessionName) {
    user.name = sessionName;
    if (sessionEmail) user.email = sessionEmail;
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));
  }

  // Update staff profile
  const staffRaw = localStorage.getItem('mis_apartment_staff_profile');
  if (staffRaw) {
    const staff = JSON.parse(staffRaw);
    if (staff.name !== sessionName) {
      staff.name = sessionName;
      if (sessionEmail) staff.email = sessionEmail;
      localStorage.setItem('mis_apartment_staff_profile', JSON.stringify(staff));
    }
  } else {
    // Initialize staff profile if missing
    const newStaff = { 
      id: 'STF-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0'), 
      name: sessionName, 
      email: sessionEmail || '', 
      phone: '', 
      gender: '', 
      arabic: sessionName, 
      occupation: sessionRole || 'Apartment Manager', 
      since: new Date().toISOString().split('T')[0] 
    };
    localStorage.setItem('mis_apartment_staff_profile', JSON.stringify(newStaff));
  }

  // Ensure the sidebar reflects the newly synced data immediately
  loadUserNav();
}

function setTopBarDate() {
  const el = document.getElementById('top-date');
  if (el) {
    el.textContent = new Date().toLocaleDateString('en-US', {
      weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
    });
  }
}


// ══════════════════════════════════════
//  MODAL HELPERS
// ══════════════════════════════════════
function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (m) {
    m.style.animation = 'modalFadeIn 0.2s ease reverse forwards';
    setTimeout(() => { m.style.display = 'none'; m.style.animation = ''; }, 200);
  }
}

function setupModalClose(modalId) {
  const m = document.getElementById(modalId);
  if (!m) return;
  m.addEventListener('click', e => { if (e.target === m) closeModal(modalId); });
  const closeBtn = m.querySelector('.modal-close');
  if (closeBtn) closeBtn.addEventListener('click', () => closeModal(modalId));
}


// ══════════════════════════════════════
//  TENANT REPORT DATA & WORKFLOW
// ══════════════════════════════════════
const DEFAULT_REPORTS = [
  { id:'RPT-001', tenantId:'USR-001', tenantName:'Muhammad Usman', status:'ACTIVE', roomId:'APT-A2', roomName:'Unit A-2 · 1-Bedroom', remarks:'', submittedAt:'2026-03-09', verifiedAt:'2026-03-12', approvedAt:'2026-03-15', requirements:{ valid_id:true, certificate:true, photo:true, contract:true }, billingIds:['BIL-001'], updatedAt:'2026-04-01' },
  { id:'RPT-002', tenantId:'USR-002', tenantName:'Fatimah Binti Ali', status:'PENDING_MIS', roomId:null, roomName:null, remarks:'', submittedAt:'2026-03-28', verifiedAt:null, approvedAt:null, requirements:{ valid_id:true, certificate:true, photo:false, contract:false }, billingIds:[], updatedAt:'2026-03-28' },
  { id:'RPT-003', tenantId:'USR-003', tenantName:'Ahmad Saleh', status:'VERIFIED', roomId:null, roomName:null, remarks:'Documents verified. Ready for room assignment.', submittedAt:'2026-04-02', verifiedAt:'2026-04-05', approvedAt:null, requirements:{ valid_id:true, certificate:true, photo:true, contract:true }, billingIds:[], updatedAt:'2026-04-05' },
  { id:'RPT-004', tenantId:'USR-005', tenantName:'Omar Farooq', status:'REVISION', roomId:null, roomName:null, remarks:'Missing valid ID. Please resubmit a government-issued ID.', submittedAt:'2026-04-06', verifiedAt:null, approvedAt:null, requirements:{ valid_id:false, certificate:true, photo:true, contract:false }, billingIds:[], updatedAt:'2026-04-07' },
  { id:'RPT-005', tenantId:'USR-010', tenantName:'Hassan Macarambon', status:'WAITING_LIST', roomId:null, roomName:null, remarks:'All studio units fully occupied. Added to waiting list.', submittedAt:'2026-03-25', verifiedAt:'2026-03-28', approvedAt:null, requirements:{ valid_id:true, certificate:true, photo:true, contract:true }, billingIds:[], updatedAt:'2026-03-30' },
  { id:'RPT-006', tenantId:'USR-008', tenantName:'Yusuf Santos', status:'APPROVED', roomId:'APT-B2', roomName:'Unit B-2 · 2-Bedroom', remarks:'', submittedAt:'2026-03-20', verifiedAt:'2026-03-22', approvedAt:'2026-03-25', requirements:{ valid_id:true, certificate:true, photo:true, contract:true }, billingIds:['BIL-005'], updatedAt:'2026-03-25' }
];

const DEFAULT_NOTIFICATIONS = [
  { id:'NOT-001', tenantId:'USR-002', title:'Application Received', message:'Your apartment application RPT-002 has been submitted and is pending MIS review.', type:'system', read:false, createdAt:'2026-03-28T10:00:00', link:'/user/dashboard' },
  { id:'NOT-002', tenantId:'USR-005', title:'Revision Required', message:'Your application RPT-004 requires revision. Please resubmit a valid government-issued ID.', type:'reject', read:false, createdAt:'2026-04-07T14:30:00', link:'/user/dashboard' },
  { id:'NOT-003', tenantId:'USR-001', title:'Room Assigned', message:'Congratulations! You have been assigned to Unit A-2, 1-Bedroom.', type:'assign', read:true, createdAt:'2026-03-15T09:00:00', link:'/user/dashboard' },
  { id:'NOT-004', tenantId:'USR-003', title:'Application Verified', message:'Your application RPT-003 has been verified by MIS Admin.', type:'approve', read:false, createdAt:'2026-04-05T11:00:00', link:'/user/dashboard' }
];

function initReportsData() {
  if (!localStorage.getItem(STORAGE_KEYS.reports)) {
    localStorage.setItem(STORAGE_KEYS.reports, JSON.stringify(DEFAULT_REPORTS));
  }
  if (!localStorage.getItem(STORAGE_KEYS.notifications)) {
    localStorage.setItem(STORAGE_KEYS.notifications, JSON.stringify(DEFAULT_NOTIFICATIONS));
  }
}

function getReports() {
  const raw = localStorage.getItem(STORAGE_KEYS.reports);
  return raw ? JSON.parse(raw) : [...DEFAULT_REPORTS];
}
function saveReports(data) { localStorage.setItem(STORAGE_KEYS.reports, JSON.stringify(data)); }

function getNotifications() {
  const raw = localStorage.getItem(STORAGE_KEYS.notifications);
  return raw ? JSON.parse(raw) : [...DEFAULT_NOTIFICATIONS];
}
function saveNotifications(data) { localStorage.setItem(STORAGE_KEYS.notifications, JSON.stringify(data)); }

function addNotification(tenantId, title, message, type, link) {
  const notifs = getNotifications();
  const id = 'NOT-' + String(notifs.length + 1).padStart(3, '0');
  notifs.unshift({ id, tenantId, title, message, type: type || 'system', read: false, createdAt: new Date().toISOString(), link: link || '' });
  saveNotifications(notifs);
}

/**
 * initNotifBadge() — Call on every page to show/hide a badge on the
 * Notifications sidebar link when there are unread notifications.
 */
function initNotifBadge(role) {
  let unread = 0;
  if (role === 'tenant') {
    const user = getUser();
    const notifs = getNotifications();
    unread = notifs.filter(n => n.tenantId === user.id && !n.read).length;
  } else {
    // Admin/Staff sees activity-log-based notifications by convention
    const log = getActivityLog();
    unread = Math.min(5, log.length);
  }
  
  if (unread > 0) {
    document.querySelectorAll('.nav-item').forEach(link => {
      const label = link.querySelector('.nav-item-label');
      if (label && (label.textContent.trim() === 'Notifications' || label.textContent.trim() === 'Admin Inbox')) {
        if (!link.querySelector('.notif-dot')) {
          const dot = document.createElement('span');
          dot.className = 'notif-dot';
          dot.textContent = unread;
          link.style.position = 'relative';
          link.appendChild(dot);
        }
      }
    });
  }
}

// ── Workflow Logic ──
function approveReport(reportId) {
  const reports = getReports();
  const r = reports.find(x => x.id === reportId);
  if (!r || r.status !== 'PENDING_MIS') return false;
  r.status = 'VERIFIED';
  r.verifiedAt = new Date().toISOString().split('T')[0];
  r.updatedAt = r.verifiedAt;
  r.remarks = 'Documents verified. Ready for room assignment.';
  saveReports(reports);
  addActivityEntry('Application Verified', reportId + ' — ' + r.tenantName, 'MIS Admin', 'approve');
  addNotification(r.tenantId, 'Application Verified', 'Your application ' + reportId + ' has been verified.', 'approve');
  return true;
}

function rejectReport(reportId, remarks) {
  const reports = getReports();
  const r = reports.find(x => x.id === reportId);
  if (!r || r.status !== 'PENDING_MIS') return false;
  r.status = 'REVISION';
  r.remarks = remarks || 'Application requires revision.';
  r.updatedAt = new Date().toISOString().split('T')[0];
  saveReports(reports);
  addActivityEntry('Application Rejected', reportId + ' — ' + r.tenantName, 'MIS Admin', 'approve');
  addNotification(r.tenantId, 'Revision Required', 'Your application ' + reportId + ' requires revision: ' + r.remarks, 'reject');
  return true;
}

function assignRoom(reportId, roomId) {
  const reports = getReports();
  const r = reports.find(x => x.id === reportId);
  if (!r || r.status !== 'VERIFIED') return false;
  const apts = getApartments();
  const apt = apts.find(a => a.id === roomId);
  if (!apt || apt.available <= 0) return 'waiting';

  apt.available--;
  if (apt.available <= 0) apt.status = 'occupied';
  saveApartments(apts);

  r.status = 'APPROVED';
  r.roomId = roomId;
  r.roomName = apt.name;
  r.approvedAt = new Date().toISOString().split('T')[0];
  r.updatedAt = r.approvedAt;
  saveReports(reports);

  // Generate billing
  const billing = getBilling();
  const billId = 'BIL-' + String(billing.length + 1).padStart(3, '0');
  const nextMonth = new Date(); nextMonth.setMonth(nextMonth.getMonth() + 1); nextMonth.setDate(1);
  billing.push({ id:billId, userId:r.tenantId, name:r.tenantName, type:'Apartment Rent', amount:apt.price, status:'pending', dueDate:nextMonth.toISOString().split('T')[0], paidDate:null });
  saveBilling(billing);

  addActivityEntry('Room Assigned', reportId + ' — ' + r.tenantName + ' to ' + apt.name, 'Apartment Admin', 'approve');
  addNotification(r.tenantId, 'Room Assigned', 'You have been assigned to ' + apt.name + '.', 'assign');
  return true;
}

function reportStatusLabel(status) {
  const map = {
    PENDING_MIS: 'Pending MIS', REVISION: 'Revision', VERIFIED: 'Verified',
    WAITING_LIST: 'Waiting List', APPROVED: 'Approved', ACTIVE: 'Active', DELINQUENT: 'Delinquent'
  };
  return map[status] || status;
}

function reportBadgeClass(status) {
  const map = {
    PENDING_MIS: 'badge-pending', REVISION: 'badge-rejected', VERIFIED: 'badge-approved',
    WAITING_LIST: 'badge-pending', APPROVED: 'badge-approved', ACTIVE: 'badge-active', DELINQUENT: 'badge-rejected'
  };
  return map[status] || 'badge-info';
}


// ══════════════════════════════════════
//  LOGOUT CONFIRMATION MODAL
// ══════════════════════════════════════
function initLogoutModal() {
  if (window._logoutModalInit) return;
  const logoutLinks = document.querySelectorAll('a[href*="/logout"], [data-tooltip="Logout"]');
  if (logoutLinks.length === 0) return;

  window._logoutModalInit = true;

  if (!document.getElementById('logout-confirm-modal')) {
    const modalHtml = `
      <div id="logout-confirm-modal" style="position:fixed;inset:0;background:rgba(15,30,22,0.6);backdrop-filter:blur(6px);z-index:99999;display:none;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;">
        <div style="background:white;border-radius:16px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;transform:translateY(20px);transition:transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
          <div style="height:4px;background:linear-gradient(90deg,#8b2e2e,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <h4 style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:#1f2e2a;margin:0 0 10px;">Log Out</h4>
            <p style="font-size:0.9rem;color:#6f7f78;margin:0;line-height:1.5;">Are you sure you want to log out of your account?</p>
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="logout-cancel-btn" style="padding:10px 24px;border-radius:8px;border:1.5px solid #e8ece9;background:white;color:#6f7f78;font-weight:600;cursor:pointer;transition:background 0.2s;">Cancel</button>
            <button id="logout-confirm-btn" style="padding:10px 24px;border-radius:8px;border:none;background:#8b2e2e;color:white;font-weight:700;cursor:pointer;transition:background 0.2s;">Yes, Log out</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('logout-confirm-modal');
    const inner = modal.querySelector('div');
    const cancelBtn = document.getElementById('logout-cancel-btn');
    const confirmBtn = document.getElementById('logout-confirm-btn');
    let targetHref = '';

    const hideModal = () => {
      modal.style.opacity = '0';
      inner.style.transform = 'translateY(20px)';
      setTimeout(() => modal.style.display = 'none', 200);
    };

    const showModal = (e, href) => {
      e.preventDefault();
      targetHref = href;
      modal.style.display = 'flex';
      void modal.offsetWidth; // force reflow
      modal.style.opacity = '1';
      inner.style.transform = 'translateY(0)';
    };

    cancelBtn.addEventListener('click', hideModal);
    modal.addEventListener('click', e => { if(e.target === modal) hideModal(); });
    confirmBtn.addEventListener('click', () => { window.location.href = targetHref || '/logout'; });

    logoutLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        showModal(e, this.getAttribute('href'));
      });
    });
  }
}


// ══════════════════════════════════════
//  PAGE BOOTSTRAP
// ══════════════════════════════════════
function standardizePage(role) {
  initAdminData();
  initReportsData();
  initSidebar();
  initDropdowns();
  loadUserNav();
  setTopBarDate();
  initNotifBadge(role);
  initLogoutModal();
}

function toggleActionMenu(btn, e) {
  if (e) e.stopPropagation();
  const dropdown = btn.nextElementSibling;
  if (!dropdown) return;
  const isShow = dropdown.classList.contains('show');
  document.querySelectorAll('.action-menu-dropdown').forEach(d => d.classList.remove('show'));
  if (!isShow) dropdown.classList.add('show');
}

window.addEventListener('click', () => {
  document.querySelectorAll('.action-menu-dropdown').forEach(d => d.classList.remove('show'));
});

// Auto-init for cases where script is loaded after DOM
if (document.readyState === 'interactive' || document.readyState === 'complete') {
  initLogoutModal();
} else {
  document.addEventListener('DOMContentLoaded', initLogoutModal);
}
