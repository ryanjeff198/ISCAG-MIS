<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);

$fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
if (!$fullName) $fullName = $_SESSION['name'] ?? 'Male Da\'wah Staff';
$email = $dbUser['email'] ?? $_SESSION['email'] ?? 'male.dawah@iscag.org';
$role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Male Da\'wah Manager';
$phone = $dbUser['contactnum'] ?? $dbUser['phone_number'] ?? '';
$gender = $dbUser['sex'] ?? 'Male';
$muslimName = $dbUser['muslim_name'] ?? $dbUser['muslimname'] ?? $dbUser['arabic_name'] ?? '';
$occupation = $dbUser['occupation'] ?? 'Male Da\'wah Section Head';
$address = $dbUser['address'] ?? '';
$tenantId = $dbUser['tenant_id'] ?? 'STF-M01';
$avatarPath = !empty($dbUser['profile_picture_path']) ? asset($dbUser['profile_picture_path']) : '';

$initials = strtoupper(substr($dbUser['first_name'] ?? 'M', 0, 1) . substr($dbUser['last_name'] ?? 'S', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Male Staff Profile</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary-male: #14532d;
      --primary-male-dark: #0f3d21;
      --accent-male: #15803d;
    }
    .profile-avatar-lg {
      width: 96px; height: 96px; border-radius: 50%;
      background: var(--primary-male);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.2rem; font-weight: 700; color: white; font-family: 'Lora', serif;
      position: relative; overflow: hidden;
      border: 3.5px solid white;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      background-size: cover;
      background-position: center;
    }
    .info-badge {
      display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
    }
    .service-card {
      padding: 16px 20px; border-radius: 12px; border: 1.5px solid var(--border); background: white;
      display: flex; align-items: center; gap: 14px; transition: all 0.2s; text-decoration: none; color: inherit;
    }
    .service-card:hover {
      border-color: var(--primary-male);
      box-shadow: 0 6px 18px rgba(20, 83, 45, 0.08);
      transform: translateY(-2px);
    }
    .svc-icon {
      width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .svc-icon svg { width: 22px; height: 22px; fill: white; }

    /* Modal Styling */
    .form-section-title {
      font-family: 'Lora', serif; font-size: 0.9rem; font-weight: 700; color: var(--primary-male-dark);
      padding-bottom: 8px; border-bottom: 2px solid rgba(20, 83, 45, 0.2); margin-bottom: 16px;
    }
    .form-submit-row {
      display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border);
    }
    .btn-cancel {
      padding: 9px 20px; border-radius: 8px; border: 1.5px solid var(--border); background: white;
      color: var(--text-muted); font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s;
    }
    .btn-cancel:hover { border-color: var(--danger); color: var(--danger); }
    .btn-submit {
      padding: 9px 22px; border-radius: 8px; border: none;
      background: linear-gradient(135deg, #14532d, #0f3d21);
      color: white; font-size: 0.85rem; font-weight: 700; cursor: pointer;
      box-shadow: 0 4px 12px rgba(20, 83, 45, 0.25); font-family: inherit; transition: all 0.2s;
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #166534, #14532d);
      box-shadow: 0 6px 20px rgba(20, 83, 45, 0.35); transform: translateY(-1px);
    }

    /* Toast */
    .profile-toast {
      position: fixed; bottom: 28px; right: 28px; z-index: 999999;
      background: #0f172a; color: white; padding: 12px 20px; border-radius: 10px;
      font-size: 0.88rem; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.25);
      display: flex; align-items: center; gap: 10px;
      transform: translateY(80px); opacity: 0; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .profile-toast.show { transform: translateY(0); opacity: 1; }
    .profile-toast.success { background: #064e3b; border-left: 4px solid #10b981; }
    .profile-toast.error { background: #7f1d1d; border-left: 4px solid #ef4444; }
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
        <div class="top-bar-left">
          <div class="top-bar-title">Staff Profile</div>
          <div class="top-bar-subtitle">Male Da'wah Department — Manage your staff account and departmental settings</div>
        </div>
        <div class="top-bar-actions">
           <a href="<?= url('/admin/dawah/male') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;color:var(--primary-male);text-decoration:none;">
             <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
           </a>
        </div>
      </div>

      <div class="page-body">
        <!-- ═══ PROFILE HERO CARD ═══ -->
        <div class="section-card" style="margin-bottom:24px;overflow:hidden;">
          <div style="background:linear-gradient(135deg, #14532d 0%, #0f3d21 100%); height:110px; position:relative; overflow:hidden;">
            <div style="position:absolute; right:-25px; bottom:-25px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.08);"></div>
            <div style="position:absolute; left:20%; top:-20px; width:90px; height:90px; border-radius:50%; background:rgba(21,128,61,0.15);"></div>
          </div>
          <div style="padding:0 28px 24px; position:relative; z-index:2;">
            <div style="display:flex;align-items:flex-end;gap:22px;margin-top:-48px;margin-bottom:16px;flex-wrap:wrap;">
              
              <!-- Profile Avatar & Actions -->
              <div style="flex-shrink:0;text-align:center;">
                <div class="profile-avatar-lg" id="profile-avatar">
                  <?= $initials ?>
                </div>

                <input type="file" id="avatar-input" accept="image/*" style="display:none;" />
                
                <div id="avatar-actions-default" style="margin-top:10px;">
                  <button type="button" onclick="document.getElementById('avatar-input').click()" style="padding:6px 14px;border-radius:6px;border:none;background:var(--primary-male);color:white;font-size:0.76rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(20, 83, 45, 0.25);display:inline-flex;align-items:center;gap:5px;">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M3 4V1h2v3h3v2H5v3H3V6H0V4h3zm3 6V7h3V4h7l1.83 2H21c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V10h3zm7 9c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-3.2-5c0 1.77 1.43 3.2 3.2 3.2s3.2-1.43 3.2-3.2-1.43-3.2-3.2-3.2-3.2 1.43-3.2 3.2z"/></svg>
                    Edit Photo
                  </button>
                </div>

                <div id="avatar-actions-confirm" style="display:none; gap:6px; margin-top:10px; justify-content:center;">
                  <button type="button" id="avatar-cancel" style="padding:5px 10px;border-radius:6px;border:1.5px solid var(--border);background:white;color:var(--text-muted);font-size:0.75rem;font-weight:700;cursor:pointer;">Cancel</button>
                  <button type="button" id="avatar-save" style="padding:5px 12px;border-radius:6px;border:none;background:var(--primary-male);color:white;font-size:0.75rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(20,83,45,0.3);">Save</button>
                </div>
              </div>

              <!-- Name & Quick Info -->
              <div style="flex:1;min-width:240px;padding-top:52px;">
                <h4 style="font-family:'Lora',serif;font-weight:700;color:var(--text-main);margin:0 0 4px;font-size:1.25rem;" id="p-name">
                  <?= htmlspecialchars($fullName) ?>
                </h4>
                <p style="color:var(--text-muted);font-size:0.85rem;margin:0 0 10px;" id="p-email">
                  <?= htmlspecialchars($email) ?>
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                  <span class="info-badge" style="background:rgba(20, 83, 45, 0.1);color:var(--primary-male);" id="p-occupation">
                    <?= htmlspecialchars($occupation) ?>
                  </span>
                  <span class="info-badge" style="background:rgba(46,125,85,0.1);color:#15803d;">
                    Active Staff Account
                  </span>
                </div>
              </div>
            </div>

            <div style="border-top:1px solid var(--border);margin-bottom:18px;"></div>
            
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <button id="edit-btn" type="button" class="btn-topbar primary" style="font-size:0.85rem;padding:9px 22px;border-radius:8px;font-weight:700;cursor:pointer;background:var(--primary-male);display:inline-flex;align-items:center;gap:7px;box-shadow:0 4px 12px rgba(20,83,45,0.25);">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                Update Profile
              </button>
            </div>
          </div>
        </div>

        <!-- ═══ SERVICES MANAGED ═══ -->
        <div class="section-card" style="margin-bottom:24px;">
          <div class="section-card-header">
            <h6 style="color:var(--primary-male-dark);">Services &amp; Sections Managed</h6>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;">
              <a href="<?= url('/admin/dawah/education') ?>" class="service-card">
                <div class="svc-icon" style="background:var(--primary-male);"><svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.92rem;">Islamic Education</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Enrollment &amp; Student Records</div>
                </div>
              </a>

              <a href="<?= url('/admin/dawah/counseling/requests') ?>" class="service-card">
                <div class="svc-icon" style="background:#0284c7;"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.92rem;">Counseling</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Consultations &amp; Guidance</div>
                </div>
              </a>

              <a href="<?= url('/admin/dawah/marriage/requests') ?>" class="service-card">
                <div class="svc-icon" style="background:#db2777;"><svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
                <div>
                  <div style="font-weight:700;font-size:0.92rem;">Marriage Services</div>
                  <div style="font-size:0.75rem;color:var(--text-muted);">Solemnization &amp; Certificates</div>
                </div>
              </a>
            </div>
          </div>
        </div>

        <!-- ═══ ACCOUNT SETTINGS / DETAILS CARD ═══ -->
        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color:var(--primary-male-dark);">Account &amp; Staff Details</h6>
          </div>
          <div class="section-card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:20px;">
              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Staff ID</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-id"><?= htmlspecialchars($tenantId) ?></p>
              </div>

              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Administrative Role</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-role"><?= htmlspecialchars($occupation) ?></p>
              </div>

              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Department</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-dept">Male Da'wah Section</p>
              </div>

              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Contact Number</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-phone"><?= htmlspecialchars($phone ?: 'Not provided') ?></p>
              </div>

              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Gender</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-gender"><?= htmlspecialchars(ucfirst(strtolower($gender ?: 'Male'))) ?></p>
              </div>

              <div>
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Muslim Name</label>
                <p style="font-weight:700;margin-top:4px;color:#111827;" id="s-arabic"><?= htmlspecialchars($muslimName ?: '—') ?></p>
              </div>

              <div style="grid-column: span 2;">
                <label class="form-label" style="color:var(--text-muted);font-size:0.74rem;font-weight:700;text-transform:uppercase;">Office / Residential Address</label>
                <p style="font-weight:600;margin-top:4px;color:#374151;" id="s-address"><?= htmlspecialchars($address ?: 'ISCAG Da\'wah Center, Cavite') ?></p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 📝 EDIT STAFF PROFILE MODAL                                -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="profile-modal" style="display:none;position:fixed;inset:0;background:rgba(15,30,22,0.6);backdrop-filter:blur(4px);z-index:999999;align-items:center;justify-content:center;padding:24px 16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:620px;max-height:90vh;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.25);display:flex;flex-direction:column;">
      <div style="height:4px;background:linear-gradient(90deg, #14532d, #15803d);"></div>
      
      <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border);background:white;">
        <h5 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:800;color:var(--primary-male-dark);margin:0;display:flex;align-items:center;gap:8px;">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:18px;height:18px;fill:var(--primary-male);"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
          Edit Male Da'wah Staff Profile
        </h5>
        <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;font-size:1.6rem;line-height:1;color:var(--text-muted);">&times;</button>
      </div>

      <div style="padding:24px;overflow-y:auto;flex:1;">
        <div class="form-section-title">Personal &amp; Contact Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Full Name <span style="color:#dc2626;">*</span></label>
            <input type="text" class="form-control" id="f-name" style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
          </div>
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Email Address <span style="color:#dc2626;">*</span></label>
            <input type="email" class="form-control" id="f-email" style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Contact Number</label>
            <input type="tel" class="form-control" id="f-phone" placeholder="+63 9XX XXX XXXX" style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
          </div>
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Gender</label>
            <input type="text" class="form-control" id="f-gender" readonly style="width:100%;box-sizing:border-box;border:1.5px solid #e2e8f0;border-radius:8px;padding:9px 12px;font-size:0.88rem;background:#f8fafc;color:#64748b;font-family:inherit;cursor:not-allowed;" value="Male" />
          </div>
        </div>

        <div class="form-section-title">Department &amp; Da'wah Details</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Muslim / Arabic Name</label>
            <input type="text" class="form-control" id="f-arabic" placeholder="e.g. Abdullah" style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
          </div>
          <div>
            <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Designation / Title</label>
            <input type="text" class="form-control" id="f-occupation" placeholder="Male Da'wah Section Head" style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
          </div>
        </div>

        <div style="margin-bottom:20px;">
          <label class="form-label" style="font-size:0.76rem;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:5px;display:block;">Office / Residential Address</label>
          <input type="text" class="form-control" id="f-address" placeholder="Address..." style="width:100%;box-sizing:border-box;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 12px;font-size:0.88rem;font-family:inherit;" />
        </div>

        <div class="form-submit-row">
          <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="button" class="btn-submit" id="btn-save-profile" onclick="saveProfile()">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Element -->
  <div id="profile-toast" class="profile-toast"></div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    const DB_PROFILE = {
      name: "<?= addslashes($fullName) ?>",
      email: "<?= addslashes($email) ?>",
      phone: "<?= addslashes($phone) ?>",
      gender: "<?= addslashes($gender) ?>",
      arabic: "<?= addslashes($muslimName) ?>",
      occupation: "<?= addslashes($occupation) ?>",
      address: "<?= addslashes($address) ?>",
      id: "<?= addslashes($tenantId) ?>",
      avatar: "<?= addslashes($avatarPath) ?>"
    };

    const PROFILE_KEY = 'mis_dawah_male_profile';
    const PHOTO_KEY = 'mis_dawah_male_photo';

    function showToast(message, isSuccess = true) {
      const toast = document.getElementById('profile-toast');
      if (!toast) return;
      toast.textContent = message;
      toast.className = 'profile-toast ' + (isSuccess ? 'success' : 'error') + ' show';
      setTimeout(() => {
        toast.className = 'profile-toast';
      }, 3500);
    }

    function getProfile() {
      const raw = localStorage.getItem(PROFILE_KEY);
      if (!raw) return DB_PROFILE;
      try {
        const local = JSON.parse(raw);
        return { ...DB_PROFILE, ...local };
      } catch (e) {
        return DB_PROFILE;
      }
    }

    function renderProfile() {
      const p = getProfile();
      const initials = p.name.split(' ').filter(n => n).map(n => n[0]).join('').slice(0, 2).toUpperCase() || 'MS';
      const avatarEl = document.getElementById('profile-avatar');
      const photo = localStorage.getItem(PHOTO_KEY) || p.avatar;

      if (avatarEl) {
        if (photo) {
          avatarEl.textContent = '';
          avatarEl.style.backgroundImage = 'url(' + photo + ')';
          avatarEl.style.backgroundSize = 'cover';
          avatarEl.style.backgroundPosition = 'center';
        } else {
          avatarEl.textContent = initials;
          avatarEl.style.backgroundImage = 'none';
        }
      }

      const pName = document.getElementById('p-name');
      const pEmail = document.getElementById('p-email');
      const pOcc = document.getElementById('p-occupation');
      const sId = document.getElementById('s-id');
      const sRole = document.getElementById('s-role');
      const sPhone = document.getElementById('s-phone');
      const sArabic = document.getElementById('s-arabic');
      const sAddress = document.getElementById('s-address');

      if (pName) pName.textContent = p.name;
      if (pEmail) pEmail.textContent = p.email;
      if (pOcc) pOcc.textContent = p.occupation || 'Male Da\'wah Section Head';
      if (sId) sId.textContent = p.id;
      if (sRole) sRole.textContent = p.occupation || 'Male Da\'wah Section Head';
      if (sPhone) sPhone.textContent = p.phone || 'Not provided';
      if (sArabic) sArabic.textContent = p.arabic || '—';
      if (sAddress) sAddress.textContent = p.address || 'ISCAG Da\'wah Center, Cavite';

      // Update sidebar nav user if present
      const navA = document.getElementById('nav-avatar');
      const navN = document.getElementById('nav-name');
      const navR = document.getElementById('nav-role');
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
      if (navR && p.occupation) navR.textContent = p.occupation;

      syncSessionUser(p.name, p.email, 'Staff_Male');
    }

    renderProfile();

    // ── Open Edit Modal ──
    const editBtn = document.getElementById('edit-btn');
    if (editBtn) {
      editBtn.addEventListener('click', () => {
        const p = getProfile();
        document.getElementById('f-name').value = p.name || '';
        document.getElementById('f-email').value = p.email || '';
        document.getElementById('f-phone').value = p.phone || '';
        document.getElementById('f-gender').value = 'Male';
        document.getElementById('f-arabic').value = p.arabic || '';
        document.getElementById('f-occupation').value = p.occupation || '';
        document.getElementById('f-address').value = p.address || '';
        document.getElementById('profile-modal').style.display = 'flex';
      });
    }

    function closeModal() {
      const modal = document.getElementById('profile-modal');
      if (modal) modal.style.display = 'none';
    }

    document.getElementById('profile-modal')?.addEventListener('click', e => {
      if (e.target.id === 'profile-modal') closeModal();
    });

    // ── Save Profile Function ──
    async function saveProfile() {
      const name = document.getElementById('f-name').value.trim();
      const email = document.getElementById('f-email').value.trim();
      const phone = document.getElementById('f-phone').value.trim();
      const arabic = document.getElementById('f-arabic').value.trim();
      const occupation = document.getElementById('f-occupation').value.trim();
      const address = document.getElementById('f-address').value.trim();

      if (!name) {
        alert('Please enter your full name.');
        document.getElementById('f-name').focus();
        return;
      }
      if (!email) {
        alert('Please enter your email address.');
        document.getElementById('f-email').focus();
        return;
      }

      const saveBtn = document.getElementById('btn-save-profile');
      if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
      }

      const formData = new FormData();
      formData.append('name', name);
      formData.append('email', email);
      formData.append('phone', phone);
      formData.append('arabic', arabic);
      formData.append('occupation', occupation);
      formData.append('address', address);

      try {
        const response = await fetch('<?= url("/admin/dawah/profile/update") ?>', {
          method: 'POST',
          body: formData
        });

        const res = await response.json();
        if (res.success) {
          const p = getProfile();
          p.name = name;
          p.email = email;
          p.phone = phone;
          p.arabic = arabic;
          p.occupation = occupation;
          p.address = address;
          localStorage.setItem(PROFILE_KEY, JSON.stringify(p));

          closeModal();
          showToast('Staff profile updated successfully!', true);
          renderProfile();
        } else {
          showToast(res.message || 'Error updating profile.', false);
        }
      } catch (err) {
        console.error(err);
        const p = getProfile();
        p.name = name;
        p.email = email;
        p.phone = phone;
        p.arabic = arabic;
        p.occupation = occupation;
        p.address = address;
        localStorage.setItem(PROFILE_KEY, JSON.stringify(p));
        closeModal();
        showToast('Profile updated locally.', true);
        renderProfile();
      } finally {
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.textContent = 'Save Changes';
        }
      }
    }

    // ── Avatar Photo Upload & Preview Logic ──
    let pendingAvatarUrl = null;
    let selectedAvatarFile = null;

    const avatarInput = document.getElementById('avatar-input');
    if (avatarInput) {
      avatarInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        selectedAvatarFile = file;

        const reader = new FileReader();
        reader.onload = ev => {
          pendingAvatarUrl = ev.target.result;
          const avatarEl = document.getElementById('profile-avatar');
          avatarEl.textContent = '';
          avatarEl.style.backgroundImage = 'url(' + pendingAvatarUrl + ')';
          avatarEl.style.backgroundSize = 'cover';
          avatarEl.style.backgroundPosition = 'center';

          document.getElementById('avatar-actions-default').style.display = 'none';
          document.getElementById('avatar-actions-confirm').style.display = 'flex';
        };
        reader.readAsDataURL(file);
      });
    }

    document.getElementById('avatar-cancel')?.addEventListener('click', () => {
      pendingAvatarUrl = null;
      selectedAvatarFile = null;
      document.getElementById('avatar-input').value = '';
      document.getElementById('avatar-actions-default').style.display = 'block';
      document.getElementById('avatar-actions-confirm').style.display = 'none';
      renderProfile();
    });

    document.getElementById('avatar-save')?.addEventListener('click', async () => {
      if (pendingAvatarUrl) {
        localStorage.setItem(PHOTO_KEY, pendingAvatarUrl);

        if (selectedAvatarFile) {
          const formData = new FormData();
          const p = getProfile();
          formData.append('name', p.name);
          formData.append('email', p.email);
          formData.append('phone', p.phone);
          formData.append('arabic', p.arabic);
          formData.append('occupation', p.occupation);
          formData.append('address', p.address);
          formData.append('photo', selectedAvatarFile);

          try {
            await fetch('<?= url("/admin/dawah/profile/update") ?>', {
              method: 'POST',
              body: formData
            });
          } catch (e) {
            console.warn('Backend avatar upload sync notice:', e);
          }
        }

        showToast('Profile photo updated successfully!', true);
      }

      selectedAvatarFile = null;
      document.getElementById('avatar-input').value = '';
      document.getElementById('avatar-actions-default').style.display = 'block';
      document.getElementById('avatar-actions-confirm').style.display = 'none';
      renderProfile();
    });
  </script>
</body>
</html>
