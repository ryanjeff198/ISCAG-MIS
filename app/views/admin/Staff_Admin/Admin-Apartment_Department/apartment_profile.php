<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Staff Profile</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
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
      background: linear-gradient(135deg, #8a6b1a, #6d5414);
      color: white;
      font-size: 0.85rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(138, 107, 26, 0.3);
      font-family: inherit;
    }

    .btn-submit:hover {
      background: linear-gradient(135deg, #a6811f, #8a6b1a);
      box-shadow: 0 6px 20px rgba(138, 107, 26, 0.4);
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

    /* ── Hover Overrides ── */
    .tab-btn:hover { color: var(--accent) !important; }
    .tab-btn.active { color: var(--accent) !important; border-bottom-color: var(--accent) !important; }
    
    #edit-btn:hover,
    button[onclick*="avatar-input"]:hover {
      background: #b08925 !important;
      box-shadow: 0 4px 15px rgba(138, 107, 26, 0.35) !important;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!----sidebar---->
    <?php 
      $active_page = 'profile';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

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
            style="background:linear-gradient(135deg,#8a6b1a 0%,var(--accent-light) 100%);height:100px;position:relative;overflow:hidden;">
            <div
              style="position:absolute;right:-20px;bottom:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.1);">
            </div>
          </div>
          <div style="padding:0 28px 24px;">
            <div style="display:flex;align-items:flex-end;gap:20px;margin-top:-44px;margin-bottom:16px;flex-wrap:wrap;">
              <div style="flex-shrink:0;text-align:center;">
                <div class="profile-avatar-lg" id="profile-avatar"
                  style="border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.15);">
                  <?= strtoupper(substr($dbUser['first_name'] ?? 'A', 0, 1) . substr($dbUser['last_name'] ?? 'S', 0, 1)) ?>
                </div>
                <input type="file" id="avatar-input" accept="image/*" style="display:none;" />
                <button onclick="document.getElementById('avatar-input').click()"
                  style="margin-top:8px;padding:5px 12px;border-radius:6px;border:none;background:var(--accent);color:white;font-size:0.75rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(138, 107, 26, 0.25);">Edit
                  Photo</button>
              </div>
              <div style="flex:1;min-width:220px;padding-top:48px;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                  <h4
                    style="font-family:'Lora',serif;font-weight:700;color:var(--primary-dark);margin:0;font-size:1.15rem;"
                    id="p-name"><?= ($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '') ?: ($_SESSION['name'] ?? 'Apartment Staff') ?></h4>

                </div>
                <p style="color:var(--text-muted);font-size:0.83rem;margin:0 0 10px;" id="p-email"><?= $dbUser['email'] ?? 'apartment@iscag.org' ?>
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                  <span class="info-badge" style="background:rgba(199,154,43,0.12);color:var(--accent);"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Apartment
                    Department</span>
                  <span class="info-badge" style="background:rgba(46,125,85,0.1);color:var(--success);"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Active</span>
                  <span class="info-badge" style="background:rgba(30,95,139,0.1);color:var(--info);" id="p-occupation"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg> Staff Admin</span>
                  <span class="info-badge" style="background:rgba(30,95,139,0.1);color:var(--info);" id="p-since"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg> Staff Since —</span>
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
                style="font-size:0.82rem;padding:8px 18px;border-radius:8px;border:none;background:var(--accent);color:white;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(138, 107, 26, 0.25);">Update
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
                <p style="font-weight:600;" id="s-role">Apartment Staff Admin</p>
              </div>
              <div><label class="form-label">Department</label>
                <p style="font-weight:600;" id="s-dept">Apartments</p>
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
        <h6 style="font-family:'Lora',serif;font-size:0.95rem;font-weight:700;color:var(--accent);margin:0;"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Edit
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

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Apartment Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'staff@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Staff Admin';
      $occupation = $dbUser['occupation'] ?? $role;
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");
    const PROFILE_KEY = 'mis_apartment_staff_profile';
    const DEFAULT_PROFILE = { 
      id: '<?= $dbUser['tenant_id'] ?? 'STF-001' ?>', 
      name: "<?= addslashes($fullName) ?>", 
      email: "<?= addslashes($email) ?>", 
      phone: '<?= $dbUser['contactnum'] ?? '+63 917 123 4567' ?>', 
      gender: '<?= strtolower($dbUser['sex'] ?? 'male') ?>', 
      arabic: "<?= addslashes($dbUser['muslimname'] ?? $fullName) ?>", 
      occupation: "<?= addslashes($occupation) ?>", 
      since: '2025-06-01' 
    };

    function getProfile() {
      const raw = localStorage.getItem(PROFILE_KEY);
      const dbProfile = {
        name: "<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Apartment Staff' ?>",
        email: "<?= $dbUser['email'] ?? 'staff@iscag.org' ?>",
        phone: "<?= $dbUser['phone_number'] ?? '' ?>",
        gender: "<?= $dbUser['sex'] ?? 'Male' ?>",
        arabic: "<?= $dbUser['arabic_name'] ?? '' ?>",
        occupation: "<?= $dbUser['occupation'] ?? 'Apartment Manager' ?>",
        id: "<?= $dbUser['user_id'] ?? 'APT-001' ?>"
      };
      if (!raw) return dbProfile;
      
      const local = JSON.parse(raw);
      // Merge: DB data always wins for name and email
      return { ...local, ...dbProfile };
    }

    function render() {
      const p = getProfile();
      const apts = getApartments();
      const apps = getRequests().filter(r => r.type === 'apartment_application');
      const initials = p.name.split(' ').filter(n => n).map(n => n[0]).join('').slice(0, 2).toUpperCase();
      const avatarEl = document.getElementById('profile-avatar');
      const photo = localStorage.getItem('mis_apartment_photo');
      if (photo) {
        avatarEl.textContent = '';
        avatarEl.style.backgroundImage = 'url(' + photo + ')';
        avatarEl.style.backgroundSize = 'cover';
        avatarEl.style.backgroundPosition = 'center';
      } else {
        avatarEl.textContent = initials;
        avatarEl.style.backgroundImage = 'none';
      }
      document.getElementById('p-name').textContent = p.name;
      document.getElementById('p-email').textContent = p.email;
      document.getElementById('p-since').innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg> Staff Since ' + formatDate(p.since);
      document.getElementById('p-occupation').innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg> ' + (p.occupation || 'Staff Admin');
      document.getElementById('s-id').textContent = p.id;
      document.getElementById('s-role').textContent = p.occupation || 'Apartment Staff Admin';
      document.getElementById('s-dept').textContent = p.department || 'Apartments';
      const navA = document.getElementById('nav-avatar');
      const navN = document.getElementById('nav-name');
      const photo2 = localStorage.getItem('mis_apartment_photo');
      if (navA) {
        if (photo2) {
          navA.textContent = '';
          navA.style.backgroundImage = 'url(' + photo2 + ')';
          navA.style.backgroundSize = 'cover';
          navA.style.backgroundPosition = 'center';
        } else {
          navA.textContent = initials;
          navA.style.backgroundImage = 'none';
        }
      }
      if (navN) navN.textContent = p.name;
      document.getElementById('qs-units').textContent = apts.length;
      document.getElementById('qs-available').textContent = apts.filter(a => a.status === 'available').length;
      document.getElementById('qs-occupied').textContent = apts.filter(a => a.status === 'occupied').length;
      document.getElementById('qs-apps').textContent = apps.length;

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

  </script>
</body>

</html>