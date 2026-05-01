<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Male Staff Profile</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    :root {
      --male-accent: #14532D;
      --male-dark: #064e3b;
      --male-light: #f0fdf4;
    }
    .profile-avatar-lg {
      width: 88px; height: 88px; border-radius: 50%;
      background: var(--male-accent);
      display: flex; align-items: center; justify-content: center;
      font-size: 2rem; font-weight: 700; color: white; font-family: 'Lora', serif;
    }
    .info-badge {
      display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
    }
    .form-section-title {
      font-family: 'Lora', serif; font-size: 0.88rem; font-weight: 700; color: var(--male-dark);
      padding-bottom: 10px; border-bottom: 2px solid rgba(20, 83, 45, 0.3); margin-bottom: 16px;
    }
    .btn-submit {
      padding: 9px 20px; border-radius: 8px; border: none;
      background: linear-gradient(135deg, #14532D, #064e3b);
      color: white; font-size: 0.85rem; font-weight: 700; cursor: pointer;
      box-shadow: 0 4px 12px rgba(20, 83, 45, 0.3); font-family: inherit;
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #166534, #14532D);
      box-shadow: 0 6px 20px rgba(20, 83, 45, 0.4); transform: translateY(-1px);
    }
    .service-card {
      padding: 14px 18px; border-radius: 10px; border: 1px solid var(--border); background: white;
      display: flex; align-items: center; gap: 14px; transition: all 0.2s;
    }
    .svc-icon {
      width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .svc-icon svg { width: 20px; height: 20px; fill: white; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'profile';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" style="color: var(--male-dark);">Male Da'wah Staff Profile</div>
          <div class="top-bar-subtitle">Manage your staff account and departmental programs</div>
        </div>
      </div>
      <div class="page-body">
        <div class="section-card" style="margin-bottom:24px;overflow:hidden;">
          <div style="background:linear-gradient(135deg,#14532D 0%,#064e3b 100%);height:100px;position:relative;"></div>
          <div style="padding:0 28px 24px; position:relative; z-index:2;">
            <div style="display:flex;align-items:flex-end;gap:20px;margin-top:-44px;margin-bottom:16px;flex-wrap:wrap;">
              <div style="flex-shrink:0;text-align:center;">
                <div class="profile-avatar-lg" id="profile-avatar" style="border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.15);">
                  <?= strtoupper(substr($dbUser['first_name'] ?? 'M', 0, 1) . substr($dbUser['last_name'] ?? 'S', 0, 1)) ?>
                </div>
                <button onclick="document.getElementById('avatar-input').click()" style="margin-top:8px;padding:5px 12px;border-radius:6px;border:none;background:var(--male-accent);color:white;font-size:0.75rem;font-weight:700;cursor:pointer;">Edit Photo</button>
                <input type="file" id="avatar-input" accept="image/*" style="display:none;" />
              </div>
              <div style="flex:1;min-width:220px;padding-top:48px;">
                <h4 style="font-family:'Lora',serif;font-weight:700;color:var(--male-dark);margin:0;font-size:1.15rem;"><?= ($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '') ?: 'Male Da\'wah Staff' ?></h4>
                <p style="color:var(--text-muted);font-size:0.83rem;margin:0 0-10px;"><?= $dbUser['email'] ?? 'male.dawah@iscag.org' ?></p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
                  <span class="info-badge" style="background:rgba(20, 83, 45, 0.1);color:var(--male-accent);">Male Da'wah Section</span>
                  <span class="info-badge" style="background:rgba(46,125,85,0.1);color:var(--success);">Active</span>
                </div>
              </div>
            </div>
            <div style="border-top:1px solid var(--border);margin-bottom:18px;"></div>
            <div style="display:flex;gap:10px;">
              <button id="edit-btn" style="font-size:0.82rem;padding:8px 18px;border-radius:8px;border:none;background:var(--male-accent);color:white;font-weight:700;cursor:pointer;">Update Profile</button>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--male-dark);">Services Managed</h6>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
              <div class="service-card">
                <div class="svc-icon" style="background:var(--male-accent);"><svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg></div>
                <div><div style="font-weight:700;font-size:0.9rem;">Islamic Studies</div></div>
              </div>
              <div class="service-card">
                <div class="svc-icon" style="background:var(--info);"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
                <div><div style="font-weight:700;font-size:0.9rem;">Counseling</div></div>
              </div>
              <div class="service-card">
                <div class="svc-icon" style="background:var(--male-accent);"><svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
                <div><div style="font-weight:700;font-size:0.9rem;">Marriage Services</div></div>
              </div>
            </div>
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header"><h6 style="color: var(--male-dark);">Account Settings</h6></div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div><label class="form-label">Staff ID</label><p style="font-weight:600;"><?= $dbUser['tenant_id'] ?? 'STF-M01' ?></p></div>
              <div><label class="form-label">Role</label><p style="font-weight:600;">Male Da'wah Head</p></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');
  </script>
</body>
</html>
