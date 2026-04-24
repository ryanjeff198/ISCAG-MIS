<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Admin Profile</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
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
                        <div class="top-bar-title">Account Profile</div>
                        <div class="top-bar-subtitle">Manage your administrator credentials and security settings</div>
                    </div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                    <button class="btn-topbar primary" onclick="saveProfile()">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3-1.34 3-3-1.34-3-3-3zm3-10H6V4h9v4z" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="page-body">
                
                <!-- Admin Insights Ribbon -->
                <div class="admin-insights">
                    <div class="insight-card">
                        <div class="insight-label">Security Level</div>
                        <div class="insight-value success">Level 4</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Account Type</div>
                        <div class="insight-value info">Full Admin</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Last Password Change</div>
                        <div class="insight-value">34 days ago</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">2FA Status</div>
                        <div class="insight-value success">ENABLED</div>
                    </div>
                </div>

                <div class="grid-2">
                    <!-- Basic Info -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <h6>
                                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                Identity Details
                            </h6>
                        </div>
                        <div class="section-card-body">
                            <div class="form-group">
                                <label class="form-label">Full Legal Name</label>
                                <input type="text" class="form-control" id="profile-name" value="<?= htmlspecialchars($_SESSION['name'] ?? 'Admin User') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="profile-email" value="<?= htmlspecialchars($_SESSION['email'] ?? 'admin@iscag.org') ?>" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="profile-phone" placeholder="e.g. 0917-000-0000" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Assigned Department</label>
                                <input type="text" class="form-control" value="Management Information Systems" disabled />
                                <small style="color:var(--text-muted);">Department cannot be changed by the user.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Security & Password -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <h6>
                                <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                                Credential Management
                            </h6>
                        </div>
                        <div class="section-card-body">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="pass-current" placeholder="Enter current password" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" id="pass-new" placeholder="Enter new password" />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="pass-confirm" placeholder="Re-type new password" />
                            </div>
                            <div style="background:rgba(0,0,0,0.03); padding:16px; border-radius:8px; border:1px dashed var(--border);">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--success);"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                    <strong style="font-size:0.85rem;">Two-Factor Authentication</strong>
                                </div>
                                <p style="font-size:0.75rem; color:var(--text-muted); margin:0;">2FA adds an extra layer of security. We'll send a code to your registered email when you log in from a new device.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        standardizePage('admin');

        function saveProfile() {
            const name = document.getElementById('profile-name').value;
            const newPass = document.getElementById('pass-new').value;
            const confirmPass = document.getElementById('pass-confirm').value;

            if (newPass && newPass !== confirmPass) {
                showToast('❌ New passwords do not match!', 'var(--danger)');
                return;
            }

            // Mock local storage update if needed
            const user = JSON.parse(localStorage.getItem('mis_user') || '{}');
            user.name = name;
            localStorage.setItem('mis_user', JSON.stringify(user));

            showToast('✅ Profile settings updated successfully!', 'var(--success)');
            
            // Sync session name if I could, but here we just reload or show success
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
        
        // Load additional data
        window.addEventListener('load', () => {
            const user = JSON.parse(localStorage.getItem('mis_user') || '{}');
            if(user.phone) document.getElementById('profile-phone').value = user.phone;
        });
    </script>
</body>

</html>

