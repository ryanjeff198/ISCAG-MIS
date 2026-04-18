<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Staff Profile</title>
  <link rel="stylesheet" href="../../../css/admin-shared.css" />
  <style>
    .profile-avatar-lg {
      width: 88px;
      height: 88px;
      border-radius: 50%;
      background: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 700;
      color: white;
      font-family: 'Lora', serif;
    }

    .profile-complete-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .progress-bar-wrap {
      width: 100%;
      background: #e8ece9;
      border-radius: 4px;
      overflow: hidden;
    }

    .progress-bar-fill {
      height: 100%;
      border-radius: 4px;
      transition: width 0.6s ease;
    }

    .info-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 600;
    }

    .form-section-title {
      font-family: 'Lora', serif;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--primary-dark);
      padding-bottom: 10px;
      border-bottom: 2px solid rgba(199, 154, 43, 0.3);
      margin-bottom: 16px;
    }

    .form-submit-row {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
    }

    .btn-cancel {
      padding: 9px 20px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      background: white;
      color: var(--text-muted);
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      font-family: inherit;
    }

    .btn-cancel:hover {
      border-color: var(--danger);
      color: var(--danger);
    }

    .btn-submit {
      padding: 9px 20px;
      border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, #a67c1b, var(--accent-light));
      color: white;
      font-size: 0.85rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(199, 154, 43, 0.3);
      font-family: inherit;
    }

    .btn-submit:hover {
      box-shadow: 0 6px 20px rgba(199, 154, 43, 0.4);
      transform: translateY(-1px);
    }

    .service-card {
      padding: 14px 18px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: white;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: all 0.2s;
    }

    .service-card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .svc-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .svc-icon svg {
      width: 20px;
      height: 20px;
      fill: white;
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!----sidebar---->
    <aside class="sidebar" id="sidebar">
      <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar"><svg viewBox="0 0 24 24">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
        </svg></button>
      <div class="sidebar-header">
        <div class="sidebar-brand">
          <img src="../../logo.jpg" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
          <div class="brand-text"><strong>ISCAG MIS</strong><span>Apartment Staff</span></div>
        </div>
      </div>
      <div class="sidebar-user">
        <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">AK</div>
        <div class="user-info"><strong id="nav-name">Apartment Staff</strong><span>Staff Admin</span></div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-label">Admin</div>
        <a href="apartment_dashboard.html" class="nav-item" data-tooltip="Dashboard">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
          </svg>
          <span class="nav-item-label">Dashboard</span>
        </a>
        <a href="apartment_profile.html" class="nav-item active" data-tooltip="Profile">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
          </svg>
          <span class="nav-item-label">My Profile</span>
        </a>
        <div class="nav-section-label">Management</div>
        <a href="apartments_info.html" class="nav-item" data-tooltip="Apartment Info">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
          </svg>
          <span class="nav-item-label">Apartment Info</span>
        </a>
        <a href="payment.html" class="nav-item" data-tooltip="Billing & Payment">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
          </svg>
          <span class="nav-item-label">Billing & Payment</span>
        </a>
      </nav>
      <div class="sidebar-footer">
        <a href="../../homepage/login.html" class="nav-item" data-tooltip="Logout">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
          </svg>
          <span class="nav-item-label">Logout</span>
        </a>
      </div>
    </aside>

    <!----main content---->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Apartment Staff Profile</div>
          <div class="top-bar-subtitle">Manage your staff account and personal information</div>
        </div>
      </div>
      <div class="page-body">
        <!-- PROFILE HEADER -->
        <div class="section-card" style="margin-bottom:24px;overflow:hidden;">
          <div
            style="background:linear-gradient(135deg,#8a6b1a 0%,var(--accent-light) 100%);height:72px;position:relative;overflow:hidden;">
            <div
              style="position:absolute;right:-20px;bottom:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.1);">
            </div>
          </div>
          <div style="padding:0 28px 24px;">
            <div style="display:flex;align-items:flex-end;gap:20px;margin-top:-44px;margin-bottom:16px;flex-wrap:wrap;">
              <div style="flex-shrink:0;text-align:center;">
                <div class="profile-avatar-lg" id="profile-avatar"
                  style="border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.15);">AK</div>
                <input type="file" id="avatar-input" accept="image/*" style="display:none;" />
                <button onclick="document.getElementById('avatar-input').click()"
                  style="margin-top:8px;padding:5px 12px;border-radius:6px;border:1.5px solid var(--accent);background:white;color:var(--accent);font-size:0.75rem;font-weight:700;cursor:pointer;">Edit
                  Photo</button>
              </div>
              <div style="flex:1;min-width:220px;padding-top:48px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                  <h4
                    style="font-family:'Lora',serif;font-weight:700;color:var(--primary-dark);margin:0;font-size:1.15rem;"
                    id="p-name">Apartment Staff</h4>
                  <span class="profile-complete-badge" id="p-badge"
                    style="background:rgba(46,125,85,0.1);color:var(--success);">✅ Complete</span>
                </div>
                <p style="color:var(--text-muted);font-size:0.83rem;margin:0 0 10px;" id="p-email">apartment@iscag.org
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                  <span class="info-badge" style="background:rgba(199,154,43,0.12);color:var(--accent);">🏠 Apartment
                    Department</span>
                  <span class="info-badge" style="background:rgba(46,125,85,0.1);color:var(--success);">✅ Active</span>
                  <span class="info-badge" style="background:rgba(30,95,139,0.1);color:var(--info);" id="p-since">📅
                    Staff Since —</span>
                </div>
                <div
                  style="margin-bottom:4px;display:flex;justify-content:space-between;font-size:0.75rem;color:var(--text-muted);">
                  <span>Profile Completion</span><span id="p-pct">100%</span>
                </div>
                <div class="progress-bar-wrap" style="height:6px;">
                  <div class="progress-bar-fill" id="p-bar" style="width:100%;background:var(--success);"></div>
                </div>
              </div>
            </div>
            <div style="border-top:1px solid var(--border);margin-bottom:18px;"></div>
            <!-- Stats -->
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
              <div style="text-align:center;padding:10px 8px;border-radius:8px;background:rgba(199,154,43,0.06);">
                <div style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--accent);"
                  id="qs-units">0</div>
                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;">Total Units</div>
              </div>
              <div style="text-align:center;padding:10px 8px;border-radius:8px;background:rgba(46,125,85,0.07);">
                <div style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--success);"
                  id="qs-available">0</div>
                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;">Available</div>
              </div>
              <div style="text-align:center;padding:10px 8px;border-radius:8px;background:rgba(139,46,46,0.07);">
                <div style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--danger);"
                  id="qs-occupied">0</div>
                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;">Occupied</div>
              </div>
              <div style="text-align:center;padding:10px 8px;border-radius:8px;background:rgba(199,154,43,0.08);">
                <div style="font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--warning);"
                  id="qs-apps">0</div>
                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;">Applications</div>
              </div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button id="edit-btn" type="button"
                style="font-size:0.82rem;padding:8px 18px;border-radius:8px;border:1.5px solid var(--accent);background:white;color:var(--accent);font-weight:700;cursor:pointer;font-family:inherit;">Update
                Profile</button>
            </div>
          </div>
        </div>

        <!-- SERVICES MANAGED -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
              </svg>Services Managed</h6>
            <span
              style="font-size:0.72rem;color:var(--accent);background:rgba(199,154,43,0.1);padding:3px 10px;border-radius:12px;font-weight:700;">Apartment
              Only</span>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
              <div class="service-card">
                <div class="svc-icon" style="background:var(--accent);"><svg viewBox="0 0 24 24">
                    <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
                  </svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.9rem;">Unit Management</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Rooms & availability</div>
                </div>
              </div>
              <div class="service-card">
                <div class="svc-icon" style="background:var(--info);"><svg viewBox="0 0 24 24">
                    <path
                      d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z" />
                  </svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.9rem;">Tenant Profiles</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Tenant records</div>
                </div>
              </div>
              <div class="service-card">
                <div class="svc-icon" style="background:var(--primary);"><svg viewBox="0 0 24 24">
                    <path
                      d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                  </svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.9rem;">Billing</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Invoices & payments</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ACCOUNT SETTINGS -->
        <div class="section-card">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24">
                <path
                  d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 00.12-.61l-1.92-3.32a.49.49 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.48.48 0 00-.48-.41h-3.84a.48.48 0 00-.48.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96a.49.49 0 00-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.48-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58z" />
              </svg>Account Settings</h6>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div><label class="form-label">Staff ID</label>
                <p style="font-weight:600;font-family:monospace;color:var(--text-muted);" id="s-id">STF-001</p>
              </div>
              <div><label class="form-label">Role</label>
                <p style="font-weight:600;">Apartment Staff Admin</p>
              </div>
              <div><label class="form-label">Department</label>
                <p style="font-weight:600;">Apartments</p>
              </div>
              <div><label class="form-label">Status</label>
                <p><span class="badge-status badge-approved">Active</span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EDIT MODAL -->
  <div id="profile-modal"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:24px 16px;">
    <div
      style="background:white;border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.2);display:flex;flex-direction:column;">
      <div
        style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);background:white;">
        <h6 style="font-family:'Lora',serif;font-size:0.95rem;font-weight:700;color:var(--accent);margin:0;">✏️ Edit
          Profile</h6>
        <button onclick="closeModal()"
          style="background:none;border:none;cursor:pointer;font-size:1.5rem;color:var(--text-muted);">&times;</button>
      </div>
      <div style="padding:24px;overflow-y:auto;flex:1;">
        <div class="form-section-title">Personal Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div><label class="form-label">Full Name</label><input type="text" class="form-control" id="f-name" /></div>
          <div><label class="form-label">Email</label><input type="email" class="form-control" id="f-email" /></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
          <div><label class="form-label">Phone</label><input type="tel" class="form-control" id="f-phone" /></div>
          <div><label class="form-label">Gender</label><select class="form-control" id="f-gender"
              style="appearance:auto;">
              <option value="">—</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select></div>
        </div>
        <div class="form-section-title">Islamic Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
          <div><label class="form-label">Arabic Name</label><input type="text" class="form-control" id="f-arabic" />
          </div>
          <div><label class="form-label">Occupation</label><input type="text" class="form-control" id="f-occupation" />
          </div>
        </div>
        <div class="form-submit-row">
          <button class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button class="btn-submit" onclick="saveProfile()">Save Profile</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../../../JS/admin-shared.js"></script>
  <script>
    initAdminData();
    const PROFILE_KEY = 'mis_apartment_staff_profile';
    const DEFAULT_PROFILE = { id: 'STF-001', name: 'Abdul Karim', email: 'akarim@iscag.org', phone: '+63 917 123 4567', gender: 'male', arabic: 'Abdul Karim', occupation: 'Property Manager', since: '2025-06-01' };

    function getProfile() {
      const raw = localStorage.getItem(PROFILE_KEY);
      if (raw) return JSON.parse(raw);
      const staff = getStaffList();
      const found = staff.find(s => s.department === 'Apartment');
      if (found) { DEFAULT_PROFILE.name = found.name; DEFAULT_PROFILE.email = found.email; DEFAULT_PROFILE.id = found.id; DEFAULT_PROFILE.since = found.joined; }
      localStorage.setItem(PROFILE_KEY, JSON.stringify(DEFAULT_PROFILE));
      return DEFAULT_PROFILE;
    }

    function render() {
      const p = getProfile();
      const apts = getApartments();
      const apps = getRequests().filter(r => r.type === 'apartment_application');
      const initials = p.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
      document.getElementById('profile-avatar').textContent = initials;
      document.getElementById('p-name').textContent = p.name;
      document.getElementById('p-email').textContent = p.email;
      document.getElementById('p-since').textContent = '📅 Staff Since ' + formatDate(p.since);
      document.getElementById('s-id').textContent = p.id;
      const navA = document.getElementById('nav-avatar'); if (navA) navA.textContent = initials;
      const navN = document.getElementById('nav-name'); if (navN) navN.textContent = p.name;
      document.getElementById('qs-units').textContent = apts.length;
      document.getElementById('qs-available').textContent = apts.filter(a => a.status === 'available').length;
      document.getElementById('qs-occupied').textContent = apts.filter(a => a.status === 'occupied').length;
      document.getElementById('qs-apps').textContent = apps.length;
      const fields = ['name', 'email', 'phone', 'gender', 'arabic', 'occupation'];
      const filled = fields.filter(k => p[k] && String(p[k]).trim()).length;
      const pct = Math.round((filled / fields.length) * 100);
      document.getElementById('p-pct').textContent = pct + '%';
      document.getElementById('p-bar').style.width = pct + '%';
      document.getElementById('p-bar').style.background = pct >= 100 ? 'var(--success)' : 'var(--accent)';
      const badge = document.getElementById('p-badge');
      badge.textContent = pct >= 100 ? '✅ Complete' : '🔒 ' + pct + '%';
      badge.style.background = pct >= 100 ? 'rgba(46,125,85,0.1)' : 'rgba(199,154,43,0.12)';
      badge.style.color = pct >= 100 ? 'var(--success)' : 'var(--warning)';
    }
    render();

    document.getElementById('edit-btn').addEventListener('click', () => {
      const p = getProfile();
      document.getElementById('f-name').value = p.name || '';
      document.getElementById('f-email').value = p.email || '';
      document.getElementById('f-phone').value = p.phone || '';
      document.getElementById('f-gender').value = p.gender || '';
      document.getElementById('f-arabic').value = p.arabic || '';
      document.getElementById('f-occupation').value = p.occupation || '';
      document.getElementById('profile-modal').style.display = 'flex';
    });

    function closeModal() { document.getElementById('profile-modal').style.display = 'none'; }
    document.getElementById('profile-modal').addEventListener('click', e => { if (e.target.id === 'profile-modal') closeModal(); });

    function saveProfile() {
      const p = getProfile();
      p.name = document.getElementById('f-name').value.trim() || p.name;
      p.email = document.getElementById('f-email').value.trim() || p.email;
      p.phone = document.getElementById('f-phone').value.trim();
      p.gender = document.getElementById('f-gender').value;
      p.arabic = document.getElementById('f-arabic').value.trim();
      p.occupation = document.getElementById('f-occupation').value.trim();
      localStorage.setItem(PROFILE_KEY, JSON.stringify(p));
      closeModal(); showToast('✅ Profile updated!', 'var(--success)'); render();
    }

    document.getElementById('avatar-input').addEventListener('change', function (e) {
      const file = e.target.files[0]; if (!file) return;
      const r = new FileReader();
      r.onload = ev => { localStorage.setItem('mis_apartment_photo', ev.target.result); showToast('✅ Photo updated!', 'var(--success)'); render(); };
      r.readAsDataURL(file);
    });

    initSidebar();
  </script>
</body>

</html>