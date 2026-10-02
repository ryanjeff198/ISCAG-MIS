<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Staff Profile</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .profile-avatar-lg {
      width: 88px; height: 88px; border-radius: 50%;
      background: var(--damayan-accent);
      display: flex; align-items: center; justify-content: center;
      font-size: 2rem; font-weight: 700; color: white; font-family: 'Lora', serif;
    }
    .info-badge {
      display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
    }
    .form-section-title {
      font-family: 'Lora', serif; font-size: 0.88rem; font-weight: 700; color: var(--damayan-dark);
      padding-bottom: 10px; border-bottom: 2px solid rgba(23, 107, 69, 0.3); margin-bottom: 16px;
    }
    .form-submit-row {
      display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--border);
    }
    .btn-cancel {
      padding: 9px 20px; border-radius: 8px; border: 1.5px solid var(--border); background: white;
      color: var(--text-muted); font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: inherit;
    }
    .btn-cancel:hover { border-color: var(--danger); color: var(--danger); }
    .btn-submit {
      padding: 9px 20px; border-radius: 8px; border: none;
      background: linear-gradient(135deg, #176b45, #0f5c3a);
      color: white; font-size: 0.85rem; font-weight: 700; cursor: pointer;
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.3); font-family: inherit;
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #1e8a5a, #176b45);
      box-shadow: 0 6px 20px rgba(23, 107, 69, 0.4); transform: translateY(-1px);
    }
    .service-card {
      padding: 14px 18px; border-radius: 10px; border: 1px solid var(--border); background: white;
      display: flex; align-items: center; gap: 14px; transition: all 0.2s;
    }
    .service-card:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06); }
    .svc-icon {
      width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .svc-icon svg { width: 20px; height: 20px; fill: white; }
    #edit-btn:hover {
      background: var(--damayan-dark) !important;
      box-shadow: 0 4px 15px rgba(23, 107, 69, 0.35) !important;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'profile';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
          <div>
            <div class="top-bar-title" style="color: var(--damayan-dark);">Damayan Staff Profile</div>
            <div class="top-bar-subtitle">Manage your staff account and personal information</div>
          </div>
          <div class="top-bar-actions">
            <a href="<?= url('/admin/damayan') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-accent);">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
            </a>
          </div>
        </div>
      </div>

      <div class="page-body">
        <!-- PROFILE HEADER -->
        <div class="section-card" style="margin-bottom:24px;overflow:hidden;">
          <div style="background:linear-gradient(135deg,#176b45 0%,#0f5c3a 100%);height:100px;position:relative;overflow:hidden;">
            <div style="position:absolute;right:-20px;bottom:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.1);"></div>
          </div>
          <div style="padding:0 28px 24px; position:relative; z-index:2;">
            <div style="display:flex;align-items:flex-end;gap:20px;margin-top:-44px;margin-bottom:16px;flex-wrap:wrap;">
              <div style="flex-shrink:0;text-align:center;">
                <div class="profile-avatar-lg" id="profile-avatar" style="border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.15);">
                  <?= strtoupper(substr($dbUser['first_name'] ?? 'D', 0, 1) . substr($dbUser['last_name'] ?? 'S', 0, 1)) ?>
                </div>
                <input type="file" id="avatar-input" accept="image/*" style="display:none;" />
                
                <div id="avatar-actions-default">
                  <button onclick="document.getElementById('avatar-input').click()" style="margin-top:8px;padding:5px 12px;border-radius:6px;border:none;background:var(--damayan-accent);color:white;font-size:0.75rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(23, 107, 69, 0.25);">Edit Photo</button>
                </div>
                <div id="avatar-actions-confirm" style="display:none; gap:6px; margin-top:8px; justify-content:center;">
                  <button id="avatar-cancel" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);background:white;color:var(--text-muted);font-size:0.75rem;font-weight:700;cursor:pointer;">Cancel</button>
                  <button id="avatar-save" style="padding:5px 10px;border-radius:6px;border:none;background:var(--success);color:white;font-size:0.75rem;font-weight:700;cursor:pointer;">Save</button>
                </div>
              </div>
              <div style="flex:1;min-width:220px;padding-top:48px;">
                <h4 style="font-family:'Lora',serif;font-weight:700;color:var(--damayan-dark);margin:0;font-size:1.15rem;" id="p-name">
                  <?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: 'Damayan Staff' ?>
                </h4>
                <p style="color:var(--text-muted);font-size:0.83rem;margin:0 0 10px;" id="p-email"><?= $dbUser['email'] ?? 'damayan@iscag.org' ?></p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                  <span class="info-badge" style="background:rgba(23, 107, 69, 0.1);color:var(--damayan-accent);">Damayan Department</span>
                  <span class="info-badge" style="background:rgba(46,125,85,0.1);color:var(--success);">Active</span>
                  <span class="info-badge" style="background:rgba(23,107,69,0.1);color:var(--damayan-dark);" id="p-occupation">Damayan Manager</span>
                </div>
              </div>
            </div>
            <div style="border-top:1px solid var(--border);margin-bottom:18px;"></div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button id="edit-btn" type="button" style="font-size:0.82rem;padding:8px 18px;border-radius:8px;border:none;background:var(--damayan-accent);color:white;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(23, 107, 69, 0.25);">
                Update Profile
              </button>
            </div>
          </div>
        </div>

        <!-- SERVICES MANAGED -->
        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">Services Managed</h6>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
              <div class="service-card">
                <div class="svc-icon" style="background:var(--damayan-accent);"><svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.9rem;">Burial Services</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Assistance & scheduling</div>
                </div>
              </div>
              <div class="service-card">
                <div class="svc-icon" style="background:var(--info);"><svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.9rem;">Charity Programs</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Donations & aid distribution</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ACCOUNT SETTINGS -->
        <div class="section-card">
          <div class="section-card-header"><h6 style="color: var(--damayan-dark);">Account Settings</h6></div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div><label class="form-label">Staff ID</label><p style="font-weight:600;" id="s-id"><?= $dbUser['tenant_id'] ?? 'DMY-001' ?></p></div>
              <div><label class="form-label">Role</label><p style="font-weight:600;" id="s-role">Damayan Manager</p></div>
              <div><label class="form-label">Department</label><p style="font-weight:600;" id="s-dept">Damayan Social Welfare</p></div>
              <div><label class="form-label">Contact Number</label><p style="font-weight:600;" id="s-phone"><?= $dbUser['phone_number'] ?? $dbUser['contactnum'] ?? '+63 917 000 0000' ?></p></div>
              <div><label class="form-label">Muslim Name</label><p style="font-weight:600;" id="s-arabic"><?= $dbUser['arabic_name'] ?? $dbUser['muslimname'] ?? '—' ?></p></div>
              <div><label class="form-label">Status</label><p><span class="badge-status badge-approved">Active</span></p></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- EDIT PROFILE MODAL -->
  <div id="profile-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:24px 16px;">
    <div style="background:white;border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,0.2);display:flex;flex-direction:column;">
      <div style="display:flex;align-items:center;justify-space-between;padding:18px 24px;border-bottom:1px solid var(--border);background:white;">
        <h6 style="font-family:'Lora',serif;font-size:0.95rem;font-weight:700;color:var(--damayan-dark);margin:0;">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
          Edit Damayan Staff Profile
        </h6>
        <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;font-size:1.5rem;color:var(--text-muted);">&times;</button>
      </div>
      <div style="padding:24px;overflow-y:auto;flex:1;">
        <div class="form-section-title">Personal Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div><label class="form-label">Full Name</label><input type="text" class="form-control" id="f-name" /></div>
          <div><label class="form-label">Email</label><input type="email" class="form-control" id="f-email" /></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
          <div><label class="form-label">Phone</label><input type="tel" class="form-control" id="f-phone" /></div>
          <div>
            <label class="form-label">Gender</label>
            <select class="form-control" id="f-gender" style="appearance:auto;">
              <option value="">—</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>
        </div>
        <div class="form-section-title">Staff Details</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
          <div><label class="form-label">Muslim Name</label><input type="text" class="form-control" id="f-arabic" /></div>
          <div><label class="form-label">Occupation / Title</label><input type="text" class="form-control" id="f-occupation" /></div>
        </div>
        <div class="form-submit-row">
          <button class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button class="btn-submit" onclick="saveProfile()">Save Profile</button>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Damayan Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'damayan@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Damayan Manager';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");

    const PROFILE_KEY = 'mis_damayan_staff_profile';

    function getProfile() {
      const raw = localStorage.getItem(PROFILE_KEY);
      const dbProfile = {
        name: "<?= addslashes($fullName) ?>",
        email: "<?= addslashes($email) ?>",
        phone: "<?= addslashes($dbUser['phone_number'] ?? $dbUser['contactnum'] ?? '+63 917 000 0000') ?>",
        gender: "<?= addslashes($dbUser['sex'] ?? 'male') ?>",
        arabic: "<?= addslashes($dbUser['arabic_name'] ?? $dbUser['muslimname'] ?? '') ?>",
        occupation: "Damayan Manager",
        id: "<?= addslashes($dbUser['tenant_id'] ?? 'DMY-001') ?>"
      };
      if (!raw) return dbProfile;
      try {
        const local = JSON.parse(raw);
        return { ...dbProfile, ...local };
      } catch (e) {
        return dbProfile;
      }
    }

    function render() {
      const p = getProfile();
      const initials = p.name.split(' ').filter(n => n).map(n => n[0]).join('').slice(0, 2).toUpperCase() || 'DM';
      const avatarEl = document.getElementById('profile-avatar');
      const photo = localStorage.getItem('mis_damayan_photo');

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
      document.getElementById('p-occupation').textContent = p.occupation || 'Damayan Manager';
      document.getElementById('s-id').textContent = p.id;
      document.getElementById('s-role').textContent = p.occupation || 'Damayan Manager';
      document.getElementById('s-phone').textContent = p.phone || '—';
      document.getElementById('s-arabic').textContent = p.arabic || '—';

      const navA = document.getElementById('nav-avatar');
      const navN = document.getElementById('nav-name');
      if (navA) {
        if (photo) {
          navA.textContent = '';
          navA.style.backgroundImage = 'url(' + photo + ')';
          navA.style.backgroundSize = 'cover';
          navA.style.backgroundPosition = 'center';
        } else {
          navA.textContent = initials;
          navA.style.backgroundImage = 'none';
        }
      }
      if (navN) navN.textContent = p.name;
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

    function closeModal() {
      document.getElementById('profile-modal').style.display = 'none';
    }

    document.getElementById('profile-modal').addEventListener('click', e => {
      if (e.target.id === 'profile-modal') closeModal();
    });

    function saveProfile() {
      const p = getProfile();
      p.name = document.getElementById('f-name').value.trim() || p.name;
      p.email = document.getElementById('f-email').value.trim() || p.email;
      p.phone = document.getElementById('f-phone').value.trim();
      p.gender = document.getElementById('f-gender').value;
      p.arabic = document.getElementById('f-arabic').value.trim();
      p.occupation = document.getElementById('f-occupation').value.trim();

      localStorage.setItem(PROFILE_KEY, JSON.stringify(p));
      closeModal();
      if (typeof showToast === 'function') {
        showToast('✅ Profile updated successfully!', 'var(--success)');
      }
      render();
    }

    let pendingAvatarUrl = null;

    document.getElementById('avatar-input').addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
      const r = new FileReader();
      r.onload = ev => {
        pendingAvatarUrl = ev.target.result;
        const avatarEl = document.getElementById('profile-avatar');
        avatarEl.textContent = '';
        avatarEl.style.backgroundImage = 'url(' + pendingAvatarUrl + ')';
        avatarEl.style.backgroundSize = 'cover';
        avatarEl.style.backgroundPosition = 'center';

        document.getElementById('avatar-actions-default').style.display = 'none';
        document.getElementById('avatar-actions-confirm').style.display = 'flex';
      };
      r.readAsDataURL(file);
    });

    document.getElementById('avatar-cancel').addEventListener('click', () => {
      pendingAvatarUrl = null;
      document.getElementById('avatar-input').value = '';
      document.getElementById('avatar-actions-default').style.display = 'block';
      document.getElementById('avatar-actions-confirm').style.display = 'none';
      render();
    });

    document.getElementById('avatar-save').addEventListener('click', () => {
      if (pendingAvatarUrl) {
        localStorage.setItem('mis_damayan_photo', pendingAvatarUrl);
        if (typeof showToast === 'function') {
          showToast('✅ Profile photo updated!', 'var(--success)');
        }
      }
      document.getElementById('avatar-input').value = '';
      document.getElementById('avatar-actions-default').style.display = 'block';
      document.getElementById('avatar-actions-confirm').style.display = 'none';
      render();
    });
  </script>
</body>
</html>
