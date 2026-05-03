<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$info = $info ?? [];
$account = $account ?? [];
$display_name = trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? ''));

$dbUser = [
    'name' => $display_name,
    'email' => $info['email'] ?? ($account['email'] ?? ''),
    'sex' => (function() use ($info, $account) {
        $s = !empty($info['sex']) ? $info['sex'] : ($account['sex'] ?? $account['gender'] ?? $_SESSION['sex'] ?? $_SESSION['gender'] ?? '');
        $ls = strtolower($s);
        if ($ls === 'female' || $ls === 'f') return 'Female';
        if ($ls === 'male' || $ls === 'm') return 'Male';
        return $s;
    })(),
    'phone' => $info['phone'] ?? ($account['contactnum'] ?? ''),
    'dob' => $info['birthdate'] ?? '',
    'civil' => $info['civil_status'] ?? '',
    'address' => $info['address'] ?? '',
    'occupation' => $info['occupation'] ?? '',
    'arabicName' => $info['muslimname'] ?? '',
    'revertYear' => !empty($info['dateofshahadah']) ? date('Y', strtotime($info['dateofshahadah'])) : '',
];

if (!function_exists('asset')) {
    function asset($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = rtrim($baseUrl, '/');
        // Ensure we don't double up on /public if it's already in the baseUrl
        if (str_ends_with($baseUrl, '/public')) {
            return $baseUrl . '/' . ltrim($path, '/');
        }
        return $baseUrl . "/public/" . ltrim($path, '/'); 
    }
}
if (!function_exists('url')) {
    function url($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        return rtrim($baseUrl, '/') . "/" . ltrim($path, '/'); 
    }
}

if (Auth::hasRole(['Admin', 'Staff_Damayan', 'Staff_Male', 'Staff_Female', 'Staff_Tenant'])) {
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Admin Hub</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <!-- Chart.js Integration -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* ── DASHBOARD SPECIFIC STYLES ── */
    .module-hub-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }

    .hub-card {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      transition: all 0.2s;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .hub-card:hover {
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      transform: translateY(-3px);
    }

    .hub-header {
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 2px solid var(--border);
      padding-bottom: 12px;
    }

    .hub-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--primary-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .hub-icon svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }

    .hub-title h3 {
      font-size: 1rem;
      font-weight: 800;
      color: var(--primary-dark);
      margin: 0;
    }

    .hub-links {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .hub-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 14px;
      border-radius: 8px;
      background: #f8faf9;
      text-decoration: none;
      color: var(--text-main);
      font-size: 0.88rem;
      font-weight: 600;
      transition: all 0.18s;
    }

    .hub-link:hover {
      background: var(--primary-light);
      color: white;
    }

    .hub-link-arrow {
      opacity: 0.5;
      transition: transform 0.2s;
    }

    .hub-link:hover .hub-link-arrow {
      transform: translateX(4px);
      opacity: 1;
    }

    .chart-container {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 24px;
      margin-bottom: 24px;
      height: 400px;
    }

    .activity-feed {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .activity-item {
      display: flex;
      gap: 12px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-marker {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--accent);
      margin-top: 6px;
      flex-shrink: 0;
    }

    .activity-content {
      font-size: 0.85rem;
    }

    .activity-time {
      font-size: 0.72rem;
      color: var(--text-muted);
      margin-top: 2px;
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">Administrative Hub</div>
            <div class="top-bar-subtitle" id="top-date-admin">Unified Management & Service Monitoring</div>
          </div>
        </div>
        <div class="top-bar-actions">
           <button class="btn-topbar primary" onclick="location.reload()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
            Refresh Data
          </button>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Users</div>
            <div class="insight-value info" id="stat-users">--</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Pending Verifications</div>
            <div class="insight-value danger" id="stat-pending">--</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Apartment Occupancy</div>
            <div class="insight-value success" id="stat-occupancy">--</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Outstanding Billing</div>
            <div class="insight-value warning" id="stat-billing">--</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">System Health</div>
            <div class="insight-value success">Operational</div>
          </div>
        </div>

        <!-- CHARTS SECTION -->
        <div class="grid-2">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M5 9.2L11 5l6 4.2v6.1L11 19l-6-3.8V9.2z"/></svg>
                Monthly Service Activity
              </h6>
            </div>
            <div class="section-card-body">
              <div style="height: 300px;">
                <canvas id="activityChart"></canvas>
              </div>
            </div>
          </div>
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
                Recent System Activity
              </h6>
            </div>
            <div class="section-card-body activity-feed" id="recent-logs">
              <!-- Logs Injected -->
            </div>
          </div>
        </div>

        <!-- MODULE HUB -->
        <div class="module-hub-grid">
          <!-- Pending Requests -->
          <div class="hub-card">
            <div class="hub-header">
              <div class="hub-icon"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg></div>
              <div class="hub-title"><h3>Pending Requests</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/apartment_confirmation') ?>" class="hub-link">
                Apartment Confirmation <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/parking_approval') ?>" class="hub-link">
                Parking Allocation <span class="hub-link-arrow">→</span>
              </a>
            </div>
          </div>

          <!-- Finance -->
          <div class="hub-card">
            <div class="hub-header">
              <div class="hub-icon" style="background:var(--accent);"><svg viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h9zm-9-2h10V7H12v9zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg></div>
              <div class="hub-title"><h3>Financial Management</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/billing') ?>" class="hub-link">
                Billing & Payments <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/statement_of_account') ?>" class="hub-link">
                Statement of Account <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/reports') ?>" class="hub-link">
                Revenue Reports <span class="hub-link-arrow">→</span>
              </a>
            </div>
          </div>

          <!-- Community -->
          <div class="hub-card">
            <div class="hub-header">
              <div class="hub-icon" style="background:var(--info);"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
              <div class="hub-title"><h3>Community Hub</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/apartment_records') ?>" class="hub-link">
                Unit Inventory <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/dawah_records') ?>" class="hub-link">
                Da'wah & Counseling Logs <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/damayan_records') ?>" class="hub-link">
                Damayan Burial Records <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/notifications') ?>" class="hub-link">
                System Broadcast Hub <span class="hub-link-arrow">→</span>
              </a>
            </div>
          </div>

          <!-- System -->
          <div class="hub-card" style="border-left: 4px solid var(--primary);">
            <div class="hub-header">
              <div class="hub-icon" style="background:var(--primary);"><svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 6c1.4 0 2.5 1.1 2.5 2.5S13.4 12 12 12s-2.5-1.1-2.5-2.5S10.6 7 12 7zm0 14c-2.7 0-5.8-1.3-7.5-3.6.1-2.1 4.5-3.2 7.5-3.2s7.4 1.1 7.5 3.2c-1.7 2.3-4.8 3.6-7.5 3.6z"/></svg></div>
              <div class="hub-title"><h3>System Control</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/records') ?>" class="hub-link">
                User Management <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/audit_logs') ?>" class="hub-link">
                System Audit Trails <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/notification') ?>" class="hub-link">
                Admin Inbox <span class="hub-link-arrow">→</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // ── MOCK DATA ENGINE ──
      const stats = {
        users: 1248,
        pending: 14,
        occupancy: "92%",
        billing: "₱142,500"
      };

      // Populate Stats
      const statUsersEl = document.getElementById('stat-users');
      const statPendingEl = document.getElementById('stat-pending');
      const statOccupancyEl = document.getElementById('stat-occupancy');
      const statBillingEl = document.getElementById('stat-billing');

      if(statUsersEl) statUsersEl.innerText = stats.users.toLocaleString();
      if(statPendingEl) statPendingEl.innerText = stats.pending;
      if(statOccupancyEl) statOccupancyEl.innerText = stats.occupancy;
      if(statBillingEl) statBillingEl.innerText = stats.billing;

      // ── LOGS ENGINE ──
      const logs = [
        { text: "System broadcast sent to all Tenants", time: "2 minutes ago" },
        { text: "Verification approved for App ID APP-2024-089", time: "45 minutes ago" },
        { text: "Database backup completed successfully", time: "2 hours ago" },
        { text: "Audit log purged for archived records", time: "4 hours ago" }
      ];

      const logContainer = document.getElementById('recent-logs');
      if(logContainer) {
          logs.forEach(log => {
            const div = document.createElement('div');
            div.className = 'activity-item';
            div.innerHTML = `
              <div class="activity-marker"></div>
              <div class="activity-content">
                <div>${log.text}</div>
                <div class="activity-time">${log.time}</div>
              </div>
            `;
            logContainer.appendChild(div);
          });
      }

      // ── CHART ENGINE ──
      const chartEl = document.getElementById('activityChart');
      if(chartEl) {
          const ctx = chartEl.getContext('2d');
          new Chart(ctx, {
            type: 'line',
            data: {
              labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
              datasets: [{
                  label: 'Tenant Verifications',
                  data: [65, 59, 80, 81, 56, 95],
                  borderColor: '#176b45',
                  backgroundColor: 'rgba(23, 107, 69, 0.1)',
                  borderWidth: 3,
                  tension: 0.4,
                  fill: true
                },
                {
                  label: 'Burial Records',
                  data: [12, 19, 3, 5, 2, 8],
                  borderColor: '#c79a2b',
                  backgroundColor: 'rgba(199, 154, 43, 0.1)',
                  borderWidth: 2,
                  tension: 0.4,
                  fill: true
                }
              ]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: { position: 'bottom' }
              },
              scales: {
                y: { beginAtZero: true, grid: { color: '#eee' } },
                x: { grid: { display: false } }
              }
            }
          });
      }

      // ── Date in top bar ──
      const now = new Date();
      const topDate = document.getElementById('top-date-admin');
      if(topDate) {
          topDate.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
      }

      // ── Sidebar Initialization ──
      if (typeof initSidebar === 'function') initSidebar();
      if (typeof initDropdowns === 'function') initDropdowns();
    });
  </script>
</body>
</html>
<?php
} else {
  // Fetch dynamic user data for the dashboard widget
  $userId = $_SESSION['user_id'] ?? null;
  $dbUser = [];
  if ($userId) {
      require_once BASE_PATH . '/app/models/User.php';
      $userModel = new User();
      $account = $userModel->findById($userId);
      $info = $userModel->getAdditionalInfo($userId);
      
        $dbUser = [
            'name' => trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')),
            'email' => $info['email'] ?? ($account['email'] ?? ''),
            'sex' => (function() use ($info, $account) {
                $s = !empty($info['sex']) ? $info['sex'] : ($account['sex'] ?? $account['gender'] ?? $_SESSION['sex'] ?? $_SESSION['gender'] ?? '');
                $ls = strtolower($s);
                if ($ls === 'female' || $ls === 'f') return 'Female';
                if ($ls === 'male' || $ls === 'm') return 'Male';
                return $s;
            })(),
            'phone' => $info['phone'] ?? ($account['contactnum'] ?? ''),
            'dob' => $info['birthdate'] ?? '',
            'civil' => $info['civil_status'] ?? '',
            'address' => $info['address'] ?? '',
            'occupation' => $info['occupation'] ?? '',
            'arabicName' => $info['muslimname'] ?? '',
            'revertYear' => !empty($info['dateofshahadah']) ? (is_numeric($info['dateofshahadah']) ? $info['dateofshahadah'] : date('Y', strtotime($info['dateofshahadah']))) : '',
        ];

        // Fetch apartment application status for announcement modal
        require_once BASE_PATH . '/app/models/ApartmentApp.php';
        $aptModel = new ApartmentApp();
        $application = $aptModel->getApplication($userId);
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MIS — User Dashboard</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    /* ── Locked Dropdown State ── */
    .nav-dropdown-wrap.locked .nav-dropdown-trigger {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .nav-dropdown-wrap.locked .nav-dropdown-trigger:hover {
      background: rgba(139, 46, 46, 0.06);
    }

    .nav-dropdown-wrap.locked .nav-dropdown-arrow {
      display: none;
    }

    .nav-lock-icon {
      width: 14px;
      height: 14px;
      fill: var(--warning);
      margin-left: auto;
      flex-shrink: 0;
      display: none;
    }

    .nav-dropdown-wrap.locked .nav-lock-icon {
      display: block;
    }

    .nav-dropdown-wrap.locked .nav-dropdown {
      display: none !important;
    }

    .nav-lock-badge {
      display: none;
      font-size: 0.6rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--warning);
      background: rgba(199, 154, 43, 0.1);
      padding: 2px 8px;
      border-radius: 10px;
      margin-left: 6px;
      white-space: nowrap;
    }

    .nav-dropdown-wrap.locked .nav-lock-badge {
      display: inline-flex;
    }

    .sidebar.collapsed .nav-lock-badge {
      display: none !important;
    }

    .sidebar.collapsed .nav-lock-icon {
      display: none !important;
    }

    /* ── Status Announcement Modal ── */
    #status-announcement-modal {
      position: fixed; inset: 0; z-index: 99999;
      display: flex; align-items: center; justify-content: center;
      background: rgba(15, 30, 22, 0.65); backdrop-filter: blur(8px);
      opacity: 0; transition: opacity 0.3s ease;
    }

    #announcement-content {
      background: white; border-radius: 20px; width: 100%; max-width: 440px;
      box-shadow: 0 25px 70px rgba(0, 0, 0, 0.3); overflow: hidden;
      transform: translateY(20px) scale(0.95); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .ann-header {
      padding: 32px 24px 20px; text-align: center;
      position: relative;
    }

    .ann-icon {
      width: 72px; height: 72px; border-radius: 50%;
      margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .ann-icon svg { width: 32px; height: 32px; fill: white; }

    .ann-title { font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 8px; }
    .ann-desc { font-size: 0.92rem; color: var(--text-muted); line-height: 1.6; margin: 0; }

    .ann-details {
      margin: 20px 24px; padding: 16px; border-radius: 12px;
      background: #f8faf9; border: 1px solid var(--border);
      text-align: center;
    }

    .ann-footer {
      padding: 0 24px 32px; display: flex; flex-direction: column; gap: 10px;
    }

    .ann-btn {
      width: 100%; padding: 12px; border-radius: 10px; border: none;
      font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
    }

    .ann-btn.primary { background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white; box-shadow: 0 4px 12px rgba(23,107,69,0.25); }
    .ann-btn.secondary { background: white; border: 1.5px solid var(--border); color: var(--text-muted); }
    .ann-btn:hover { transform: translateY(-1px); filter: brightness(1.1); }

    /* Theme colors */
    .theme-success .ann-icon { background: linear-gradient(135deg, #2f8a60, #3da870); }
    .theme-warning .ann-icon { background: linear-gradient(135deg, var(--accent), #d4a83a); }
    .theme-danger .ann-icon { background: linear-gradient(135deg, #8b2e2e, #a94442); }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php 
      $active_page = 'dashboard';
      include 'user/sidebar.php'; 
    ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">User Dashboard</div>
          <div class="top-bar-subtitle">Submit service requests and track your applications</div>
        </div>
        <div class="top-bar-actions">
          <span id="top-date" style="font-size:0.8rem;color:var(--text-muted);"></span>
        </div>
      </div>

      <div class="page-body">

        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
          <h3 id="welcome-heading">Assalamu Alaikum, <?= htmlspecialchars(explode(' ', $_SESSION['name'] ?? 'User')[0]) ?>!</h3>
          <p>Welcome to the ISCAG Management Information System. Select a service below to submit a request.</p>

          <!-- Profile completion widget -->
          <div class="profile-widget" id="profile-widget">
            <div class="profile-ring">
              <svg viewBox="0 0 36 36">
                <circle class="ring-bg" cx="18" cy="18" r="15.9" />
                <circle class="ring-fill" cx="18" cy="18" r="15.9" id="ring-fill" stroke="white"
                  stroke-dasharray="0 100" />
              </svg>
              <span class="ring-pct" id="ring-pct">0%</span>
            </div>
            <div class="profile-widget-info">
              <div class="pw-title">Profile Completion Status</div>
              <p class="pw-sub" id="pw-sub">Complete all required fields to gain full system access.</p>
            </div>
            <a href="<?= url('/user/profile') ?>" class="btn-complete-profile" id="btn-complete-profile">Complete Profile</a>
          </div>
        </div>

        <?php if (($account['role'] ?? '') === 'Tenant'): ?>
        <!-- TENANT DASHBOARD (TENANT ONLY) -->


        <!-- Tenant Onboarding Guide Modal -->
        <div id="tenant-onboarding-modal" style="
            position: fixed; inset: 0; z-index: 99999;
            display: none; align-items: center; justify-content: center;
            background: rgba(15,30,22,0.6); backdrop-filter: blur(6px);
            opacity: 0; transition: opacity 0.3s ease;
        ">
            <div style="
                background: white; border-radius: 16px; width: 100%; max-width: 500px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden;
                transform: translateY(30px); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            " id="onboarding-modal-content">
                <div style="height: 4px; background: linear-gradient(90deg, #0f5c3a, #c79a2b);"></div>
                <div style="padding: 32px 28px 24px; text-align: center;">
                    <svg viewBox="0 0 24 24" style="width: 64px; height: 64px; fill: #2f8a60; margin: 0 auto 16px;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    <h4 style="font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 700; color: #0f5c3a; margin: 0 0 10px;">Welcome to Your Tenant Dashboard!</h4>
                    <p style="font-size: 0.9rem; color: #6f7f78; line-height: 1.6; margin: 0 0 16px;">Now that your application is approved, your interface has automatically transformed to give you access to all tenant services.</p>
                    
                    <div style="text-align: left; background: #f8faf9; padding: 16px; border-radius: 8px; border: 1px solid #e8ece9;">
                        <ul style="font-size: 0.85rem; color: #1f2e2a; line-height: 1.6; margin: 0; padding-left: 18px;">
                            <li style="margin-bottom: 6px;"><strong>Navigation Guide:</strong> Check the left sidebar to access your Apartment Information and Parking features.</li>
                            <li style="margin-bottom: 6px;"><strong>Unit Details:</strong> Click "View Apartment Details" to see your current assignment.</li>
                            <li><strong>Policies:</strong> Please adhere to all ISCAG community guidelines, maintenance rules, and timely payment of bills.</li>
                        </ul>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; padding: 0 28px 24px; justify-content: center;">
                    <button id="btn-start-exploring" style="
                        padding: 10px 24px; border-radius: 8px; border: none;
                        background: linear-gradient(135deg, #0f5c3a, #2f8a60);
                        color: white; font-size: 0.9rem; font-weight: 700; cursor: pointer;
                        box-shadow: 0 4px 12px rgba(15,92,58,0.3); transition: all 0.2s;
                    ">Got it / Start Exploring</button>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!localStorage.getItem('tenant_onboarding_completed')) {
                const modal = document.getElementById('tenant-onboarding-modal');
                const content = document.getElementById('onboarding-modal-content');
                
                modal.style.display = 'flex';
                // Trigger reflow
                void modal.offsetWidth;
                modal.style.opacity = '1';
                content.style.transform = 'translateY(0)';
                
                document.getElementById('btn-start-exploring').addEventListener('click', () => {
                    modal.style.opacity = '0';
                    content.style.transform = 'translateY(30px)';
                    setTimeout(() => modal.style.display = 'none', 400);
                    // Sync to localStorage for sidebar and dashboard
                    let updated = getUser();
                    updated.profileComplete = true;
                    localStorage.setItem('mis_user', JSON.stringify(updated));
                    localStorage.setItem('tenant_onboarding_completed', 'true');
                });
            }
        });
        </script>
        <?php endif; ?>

        <!-- SERVICE CARDS (ALL USERS) -->
        <h6
          style="font-family:'Lora',serif;font-size:0.9rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px;">
          Available Services</h6>

        <div class="service-grid" id="service-grid">
          <!-- Populated by JS -->
        </div>

        <!-- MY REQUESTS HISTORY -->
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24">
                <path
                  d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z" />
              </svg>
              My Recent Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);" id="req-count">Your submission history</span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Reference No.</th>
                    <th>Service Type</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                  </tr>
                </thead>
                <tbody id="req-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    // ── Inlined data helpers (no module imports — works from file://) ──
    const STORAGE_KEYS = { user: 'mis_user', requests: 'mis_requests', apartments: 'mis_apartments', initialized: 'mis_data_init' };
    let DB_USER = <?= json_encode($dbUser) ?>;
    const PROFILE_FIELDS = ['name', 'email', 'sex', 'phone', 'address', 'dob', 'civil', 'occupation', 'arabicName', 'revertYear'];
    const FIELD_LABELS = { name: 'Full Name', email: 'Email Address', sex: 'Sex', phone: 'Contact Number', address: 'Complete Address', dob: 'Date of Birth', civil: 'Civil Status', occupation: 'Occupation', arabicName: 'Muslim / Arabic Name', revertYear: 'Year Reverted' };
    const DEFAULT_USER = {
      id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
      name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
      email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
      sex: '<?= addslashes($_SESSION['sex'] ?? $_SESSION['gender'] ?? "") ?>',
      phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', revertYear: '', apartment: '', profileComplete: false
    };
    const DEFAULT_REQUESTS = [
      { id: 'BUR-001', user: 'USR-001', type: 'burial_service', status: 'pending', date: '2026-03-15', updatedAt: '2026-03-15' },
      { id: 'APT-001', user: 'USR-001', type: 'apartment_application', status: 'approved', date: '2026-03-09', updatedAt: '2026-03-12' }
    ];
    const DEFAULT_APARTMENTS = [
      { id: 'APT-A1', name: 'Unit A-1 · Studio', price: 3500, available: 2, status: 'available' },
      { id: 'APT-A2', name: 'Unit A-2 · 1-Bedroom', price: 5000, available: 1, status: 'available' },
      { id: 'APT-B1', name: 'Unit B-1 · 2-Bedroom', price: 7500, available: 0, status: 'occupied' },
      { id: 'APT-B2', name: 'Unit B-2 · 2-Bedroom', price: 7500, available: 1, status: 'available' },
      { id: 'APT-C1', name: 'Unit C-1 · Family Suite', price: 10000, available: 0, status: 'reserved' }
    ];

    function initData() {
      if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
        localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
        localStorage.setItem(STORAGE_KEYS.apartments, JSON.stringify(DEFAULT_APARTMENTS));
        localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(DEFAULT_REQUESTS));
        localStorage.setItem(STORAGE_KEYS.initialized, '1');
      }
    }
    function getUser() {
      const raw = localStorage.getItem(STORAGE_KEYS.user);
      const user = raw ? JSON.parse(raw) : {
        ...DEFAULT_USER
      };

      // Synchronize with DB data — DB is the source of truth.
      if (typeof DB_USER !== 'undefined' && DB_USER !== null) {
        Object.keys(DB_USER).forEach(key => {
          let val = DB_USER[key] || '';
          // Normalize sex values for consistent comparison
          if (key === 'sex' && val) {
            const v = val.toLowerCase();
            if (v === 'f' || v === 'female') val = 'Female';
            if (v === 'm' || v === 'male') val = 'Male';
          }
          // Always overwrite with DB value to prevent stale localStorage data leakage
          user[key] = val;
        });
      }

      // Final fallback: Use session data if still missing (helps with legacy sync issues)
      const SESSION_FALLBACKS = {
        sex: '<?= addslashes($_SESSION['sex'] ?? $_SESSION['gender'] ?? "") ?>',
        email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
        name: '<?= addslashes($_SESSION['name'] ?? "") ?>'
      };
      Object.entries(SESSION_FALLBACKS).forEach(([key, val]) => {
        if (!user[key] && val) user[key] = val;
      });

      return user;
    }
    function getRequests() {
      const raw = localStorage.getItem(STORAGE_KEYS.requests);
      return raw ? JSON.parse(raw) : [];
    }
    function getProfileCompletion() {
      const user = getUser();
      const missing = [];
      let filled = 0;
      PROFILE_FIELDS.forEach(k => {
        if (user[k] && String(user[k]).trim() !== '') { filled++; } else { missing.push(FIELD_LABELS[k] || k); }
      });
      return { percentage: Math.round((filled / PROFILE_FIELDS.length) * 100), filled, total: PROFILE_FIELDS.length, missingFields: missing };
    }

    function showToast(msg, bg) {
      const toast = document.createElement('div');
      toast.textContent = msg;
      toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + bg + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:Source Sans 3,sans-serif;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;';
      document.body.appendChild(toast);
      setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function showAccessModal(config) {
      const existing = document.getElementById('access-control-modal');
      if (existing) existing.remove();

      const { percentage = 0, missingFields = [], redirectUrl = '<?= url('/user/profile') ?>' } = config;

      if (!document.getElementById('acm-keyframes')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'acm-keyframes';
        styleEl.textContent = `
        @keyframes acmFadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes acmSlideUp { from { opacity:0;transform:translateY(24px) scale(0.96); } to { opacity:1;transform:translateY(0) scale(1); } }
      `;
        document.head.appendChild(styleEl);
      }

      const missingHtml = missingFields.length > 0
        ? `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">Required information:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => '<li>' + f + '</li>').join('')}
           </ul>
         </div>` : '';

      const modalHtml = `
      <div id="access-control-modal" style="
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);
        padding:24px;animation:acmFadeIn 0.3s ease;
      ">
        <div style="
          background:white;border-radius:16px;
          width:100%;max-width:440px;
          box-shadow:0 20px 60px rgba(0,0,0,0.25);
          overflow:hidden;animation:acmSlideUp 0.35s ease;
        ">
          <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <div style="margin-bottom:8px;">
              <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/>
              </svg>
            </div>
            <div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
              <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="${percentage >= 40 ? '#c79a2b' : '#8b2e2e'}" stroke-width="3"
                  stroke-dasharray="${percentage} ${100 - percentage}" stroke-linecap="round"/>
              </svg>
              <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#0f5c3a;">${percentage}%</span>
            </div>
            <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#0f5c3a;margin:0 0 10px;">Please Complete Your Profile</h4>
            <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">Please complete your profile information first. Your profile is <strong>${percentage}%</strong> complete. Kindly fill in all required fields to access this service.</p>
            ${missingHtml}
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="acm-cancel-btn" style="padding:10px 22px;border-radius:8px;border:1.5px solid #d9e3de;background:white;color:#6f7f78;font-size:0.85rem;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="acm-primary-btn" style="padding:10px 22px;border-radius:8px;border:none;background:linear-gradient(135deg,#0f5c3a,#2f8a60);color:white;font-size:0.85rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(15,92,58,0.3);">Go to Profile</button>
          </div>
        </div>
      </div>
    `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);

      const modal = document.getElementById('access-control-modal');
      document.getElementById('acm-primary-btn').addEventListener('click', () => { window.location.href = redirectUrl; });
      document.getElementById('acm-cancel-btn').addEventListener('click', () => {
        modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards';
        setTimeout(() => modal.remove(), 200);
      });
      modal.addEventListener('click', e => {
        if (e.target === modal) { modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards'; setTimeout(() => modal.remove(), 200); }
      });
    }


    // ══════════════════════════════════════
    //  INIT
    // ══════════════════════════════════════
    initData();
    const user = getUser();
    const { percentage, missingFields } = getProfileCompletion();
    const isComplete = (percentage === 100);

    // Sync all fields to localStorage so other pages have fresh data from DB
    user.profileComplete = isComplete;
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));

    // ── Load user nav ──
    const navAvatar = document.getElementById('nav-avatar');
    if (navAvatar) {
      const photo = localStorage.getItem('mis_user_photo');
      if (photo) {
        navAvatar.textContent = '';
        navAvatar.style.backgroundImage = 'url(' + photo + ')';
        navAvatar.style.backgroundSize = 'cover';
        navAvatar.style.backgroundPosition = 'center';
      }
    }

    // ── Set role label color based on status ──
    const navRole = document.getElementById('nav-role');
    if (navRole && navRole.textContent === 'Guest') {
      navRole.style.color = 'var(--warning)';
    } else if (navRole) {
      
    }

    // ── Date in top bar ──
    const now = new Date();
    document.getElementById('top-date').textContent =
      now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

    // ── Welcome heading (JS fallback if needed) ──
    // const firstName = user.name.split(' ')[0] || 'User';
    // document.getElementById('welcome-heading').textContent = `Assalamu Alaikum, ${firstName}!`;

    // ── Profile completion widget ──
    const ringFill = document.getElementById('ring-fill');
    const ringPct = document.getElementById('ring-pct');
    const pwSub = document.getElementById('pw-sub');
    const btnComplete = document.getElementById('btn-complete-profile');

    setTimeout(() => {
      ringFill.setAttribute('stroke-dasharray', `${percentage} ${100 - percentage}`);
      ringFill.setAttribute('stroke', isComplete ? '#e0b84a' : 'white');
    }, 100);
    ringPct.textContent = percentage + '%';

    if (isComplete) {
      pwSub.textContent = 'Your profile is complete. You have full access to all available departments.';
      btnComplete.textContent = 'View Profile';
      btnComplete.style.borderColor = 'rgba(224,184,74,0.5)';
      btnComplete.style.background = 'rgba(224,184,74,0.15)';
    } else {
      pwSub.textContent = `Your profile is ${percentage}% complete. Complete all required fields to gain full system access.`;
    }

    // ── Da'wah dropdown — link handling ──
    const dawahTrigger = document.getElementById('dawah-trigger');
    if (dawahTrigger) {
        const SESSION_SEX = '<?= strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? "") ?>';
        const dawahHref = SESSION_SEX === 'female' ? "<?= url('/user/services/counseling/female') ?>" : "<?= url('/user/services/counseling/male') ?>";
        dawahTrigger.setAttribute('data-href', dawahHref);
    }

    // ── Build service cards ──
    const serviceGrid = document.getElementById('service-grid');

    const dawahHref = String(user.sex).toLowerCase() === 'female'
      ? "<?= url('/user/services/counseling/female') ?>"
      : "<?= url('/user/services/counseling/male') ?>";

    const services = [
      {
        id: 'damayan',
        title: 'Damayan — Burial Services',
        desc: 'Submit a formal request for burial services for the deceased. Fill in the necessary details about the deceased, family contact, and burial preferences.',
        icon: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>',
        iconClass: '',
        href: '<?= url('/user/services/burial-form') ?>',
        btnText: 'Request Service'
      },
      {
        id: 'dawah',
        title: "Da'wah Department Services",
        desc: !isComplete
          ? 'Access our department services including Marriage, Counseling, and Islamic Studies. Complete your profile to access full department features.'
          : 'Access specialized services including Marriage, Counseling, and Islamic Studies. Our department is dedicated to providing spiritual and social support.',
        icon: '<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>',
        iconClass: String(user.sex).toLowerCase() === 'female' ? 'purple' : 'teal',
        href: '#', // Handled by click listener
        btnText: 'View Services'
      }
    ];

    if (user.role === 'Tenant') {
      services.push({
        id: 'parking',
        title: 'Parking Rental',
        desc: 'Register your vehicle and apply for a parking space within the residential premises. Manage your vehicle records and track your rental status.',
        icon: '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>',
        iconClass: 'gold',
        href: '<?= url('/user/apartment/parking') ?>',
        btnText: 'Apply for Parking'
      });
    } else {
      services.push({
        id: 'apartment',
        title: 'Apartment Application',
        desc: 'Apply for a housing unit in the ISCAG apartment complex. Submit your family details and preferred unit type for review by the Apartment Management.',
        icon: '<path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zm-8 4H7v-2h2v2zm0-4H7V9h2v2zm0-4H7V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2z"/>',
        iconClass: 'green',
        href: '<?= url('/user/apartment/apply') ?>',
        btnText: 'Apply Now'
      });
    }

    services.forEach(svc => {
      const card = document.createElement('div');
      card.className = 'service-card';
      card.innerHTML = `
      <div class="service-card-icon ${svc.iconClass}">
        <svg viewBox="0 0 24 24">${svc.icon}</svg>
      </div>
      <div class="service-card-body">
        <h5>${svc.title}</h5>
        <p>${svc.desc}</p>
        <div class="btn-go">
          ${svc.btnText}
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
        </div>
      </div>
    `;

      const isTenant = user.role === 'Tenant';
      card.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Skip profile completion check for Da'wah and Damayan
        const skipAccessCheck = (svc.id === 'dawah' || svc.id === 'damayan');
        
        if (!isComplete && !isTenant && !skipAccessCheck) {
          showAccessModal({
            percentage,
            missingFields,
            redirectUrl: '<?= url('/user/profile') ?>?edit=true'
          });
        } else {
          if (svc.id === 'dawah') {
            showDawahSelectionModal();
          } else if (svc.id === 'damayan') {
            showDamayanSelectionModal();
          } else {
            window.location.href = svc.href;
          }
        }
      });

      serviceGrid.appendChild(card);
    });

    // ── Requests table ──
    const reqs = getRequests().filter(r => r.user === user.id);
    const reqTbody = document.getElementById('req-tbody');
    const reqCount = document.getElementById('req-count');

    reqCount.textContent = reqs.length + ' record' + (reqs.length !== 1 ? 's' : '');

    if (reqs.length === 0) {
      reqTbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:var(--text-muted);">No service requests yet. Submit your first request above.</td></tr>';
    } else {
      reqTbody.innerHTML = reqs.map(r => {
        let type = r.type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        if (r.type === 'male_counseling' || r.type === 'counseling_male') {
          type = 'Counseling & Guidance';
        } else if (r.type === 'female_counseling' || r.type === 'counseling_female' || r.type === 'female_education') {
          type = 'Female Education';
        }
        return '<tr>' +
          '<td class="td-id">#' + r.id + '</td>' +
          '<td>' + type + '</td>' +
          '<td>' + r.date + '</td>' +
          '<td><span class="badge-status badge-' + r.status + '">' + r.status + '</span></td>' +
          '<td style="color:var(--text-muted);">' + (r.updatedAt || r.date) + '</td>' +
          '</tr>';
      }).join('');
    }

      // ── Status Announcement Function ──
      function showStatusAnnouncement(appData, dismissKey) {
          const status = (appData.status || '').toLowerCase();

          const configs = {
              assigned: {
                  theme: 'theme-success',
                  icon: '<path d="M10 15.17l-3.59-3.58L5 13l5 5L20 8l-1.41-1.42z"/>',
                  title: 'Room Assigned!',
                  desc: 'Congratulations! You have been officially assigned to your apartment unit. Your journey as a tenant begins now.',
                  detail: appData.unit_code ? ('Unit: <strong>' + appData.unit_code + '</strong>') : 'Your unit details are available in your Apartment Information page.',
                  primaryBtn: 'View My Apartment',
                  primaryHref: '<?= url("/user/apartment/info") ?>',
              },
              approved: {
                  theme: 'theme-success',
                  icon: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                  title: 'Application Approved!',
                  desc: 'Your apartment application has been approved. Please proceed to accept your lease and submit the initial payment.',
                  detail: 'Next step: Go to <strong>Lease Contract</strong> to review and accept your terms.',
                  primaryBtn: 'View Lease Contract',
                  primaryHref: '<?= url("/user/apartment/lease") ?>',
              },
              queued: {
                  theme: 'theme-warning',
                  icon: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>',
                  title: 'You\'re On the Waitlist',
                  desc: 'All units of your selected type are currently occupied. You\'ve been placed in a priority queue and will be notified when a unit becomes available.',
                  detail: 'Queue Position: <strong>#' + (appData.queue_position || '—') + '</strong>',
                  primaryBtn: 'Check Status',
                  primaryHref: '<?= url("/user/apartment/status") ?>',
              },
              rejected: {
                  theme: 'theme-danger',
                  icon: '<path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>',
                  title: 'Application Not Approved',
                  desc: 'Unfortunately, your apartment application was not approved at this time. You may re-apply or contact the administration office for more details.',
                  detail: appData.reject_reason ? ('Reason: <em>' + appData.reject_reason + '</em>') : 'Please visit the office for further clarification.',
                  primaryBtn: 'Re-apply',
                  primaryHref: '<?= url("/user/apartment/apply") ?>',
              }
          };

          const cfg = configs[status];
          if (!cfg) return;

          const modalHtml = `
              <div id="status-announcement-modal" class="${cfg.theme}" style="
                  position:fixed;inset:0;z-index:99999;
                  display:flex;align-items:center;justify-content:center;
                  background:rgba(15,30,22,0.65);backdrop-filter:blur(8px);
                  opacity:0;transition:opacity 0.3s ease;
              ">
                  <div id="announcement-content" style="
                      background:white;border-radius:20px;width:100%;max-width:440px;
                      box-shadow:0 25px 70px rgba(0,0,0,0.3);overflow:hidden;
                      transform:translateY(20px) scale(0.95);transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                  ">
                      <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>
                      <div class="ann-header">
                          <div class="ann-icon">
                              <svg viewBox="0 0 24 24">${cfg.icon}</svg>
                          </div>
                          <div class="ann-title">${cfg.title}</div>
                          <p class="ann-desc">${cfg.desc}</p>
                      </div>
                      <div class="ann-details">${cfg.detail}</div>
                      <div class="ann-footer">
                          <button class="ann-btn primary" onclick="window.location.href='${cfg.primaryHref}'">${cfg.primaryBtn}</button>
                          <button class="ann-btn secondary" id="ann-dismiss-btn">Dismiss</button>
                      </div>
                  </div>
              </div>
          `;

          document.body.insertAdjacentHTML('beforeend', modalHtml);

          const modal = document.getElementById('status-announcement-modal');
          const content = document.getElementById('announcement-content');

          setTimeout(() => {
              modal.style.opacity = '1';
              content.style.transform = 'translateY(0) scale(1)';
          }, 50);

          document.getElementById('ann-dismiss-btn').addEventListener('click', () => {
              modal.style.opacity = '0';
              content.style.transform = 'translateY(20px) scale(0.95)';
              setTimeout(() => modal.remove(), 300);

              // Mark as seen server-side
              fetch('<?= url("/user/status/mark-seen") ?>', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' }
              }).catch(() => {});

              // Client-side dismiss guard
              localStorage.setItem(dismissKey, '1');
          });
      }

      // ── Status Announcement Logic ──
      const appData = <?= json_encode($application ?? null) ?>;
      
      if (appData) {
          const status = (appData.status || '').toLowerCase();
          const allowed = ['assigned', 'queued', 'rejected', 'approved'];
          const isSeen = parseInt(appData.status_seen);
          const isTarget = allowed.includes(status);

          // Client-side guard: check localStorage to prevent re-showing after dismiss
          const dismissKey = 'ann_dismissed_' + (appData.application_id || '') + '_' + status;
          const alreadyDismissed = localStorage.getItem(dismissKey);

          if (!isSeen && isTarget && !alreadyDismissed) {
              showStatusAnnouncement(appData, dismissKey);
          }
      }


      function showDawahSelectionModal() {
        const existing = document.getElementById('dawah-selection-modal');
        if (existing) existing.remove();

        const modalHtml = `
          <div id="dawah-selection-modal" style="
            position:fixed;inset:0;z-index:99999;
            display:flex;align-items:center;justify-content:center;
            background:rgba(15,30,22,0.65);backdrop-filter:blur(10px);
            padding:24px;opacity:0;transition:opacity 0.3s ease;
          ">
            <div id="dawah-modal-content" style="
              background:white;border-radius:24px;width:100%;max-width:800px;
              box-shadow:0 30px 90px rgba(0,0,0,0.35);overflow:hidden;
              transform:translateY(30px) scale(0.95);transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
              padding:40px 32px;
            ">
              <div style="text-align:center;margin-bottom:32px;">
                <h4 style="font-family:'Lora',serif;font-size:1.6rem;font-weight:700;color:var(--primary-dark);margin:0 0 8px;">Da'wah Department Services</h4>
                <p style="font-size:0.95rem;color:var(--text-muted);">Please select the service you wish to access</p>
              </div>

              <div style="display:grid;grid-template-columns:<?= strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') === 'female' ? 'repeat(2, 1fr)' : 'repeat(3, 1fr)' ?>;gap:20px;margin-bottom:32px;">
                <?php if (strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') !== 'female'): ?>
                <!-- Marriage -->
                <div class="dawah-opt-card" id="opt-marriage" style="
                  border:2px solid #f0f2f1;border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;position:relative;overflow:hidden;
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:rgba(232,96,90,0.1);color:#e8605a;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;transition:all 0.3s;">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Marriage</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Nikah services and pre-marital consultation</p>
                </div>
                <?php endif; ?>

                <!-- Counseling / Education -->
                <div class="dawah-opt-card" id="opt-counseling" style="
                  border:2px solid var(--primary-light);border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;background:rgba(23,107,69,0.02);
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:var(--primary-light);color:white;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 20px rgba(23,107,69,0.2);">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Counseling</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Personal, family, or spiritual guidance sessions</p>
                </div>

                <!-- Islamic Education -->
                <div class="dawah-opt-card" id="opt-education" style="
                  border:2px solid #f0f2f1;border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;
                  <?= strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') === 'female' ? '' : 'display:none;' ?>
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:var(--primary-female-light);color:var(--primary-female-dark);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 20px rgba(212, 175, 55, 0.15);">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Islamic Education</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Enrollment and Islamic classes for sisters</p>
                </div>

                <?php if (strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') !== 'female'): ?>
                <!-- Islamic Studies -->
                <div class="dawah-opt-card" id="opt-studies" style="
                  border:2px solid #f0f2f1;border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:rgba(199,154,43,0.1);color:#c79a2b;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Islamic Studies</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Quran, Hadith, and Fiqh educational programs</p>
                </div>
                <?php endif; ?>
              </div>

              <div style="text-align:center;">
                <button id="close-dawah-modal" style="
                  padding:12px 32px;border-radius:12px;border:1.5px solid var(--border);
                  background:white;color:var(--text-muted);font-weight:700;font-size:0.9rem;
                  cursor:pointer;transition:all 0.2s;
                ">Close Selection</button>
              </div>
            </div>
          </div>

          <style>
            .dawah-opt-card:hover {
              border-color: var(--primary-light) !important;
              transform: translateY(-8px);
              box-shadow: 0 15px 40px rgba(15,92,58,0.12);
            }
            .dawah-opt-card:active { transform: translateY(-4px); }
          </style>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById('dawah-selection-modal');
        const content = document.getElementById('dawah-modal-content');

        setTimeout(() => {
          modal.style.opacity = '1';
          content.style.transform = 'translateY(0) scale(1)';
        }, 10);

        const closeModal = () => {
          modal.style.opacity = '0';
          content.style.transform = 'translateY(20px) scale(0.95)';
          setTimeout(() => modal.remove(), 300);
        };

        document.getElementById('close-dawah-modal').onclick = closeModal;
        modal.onclick = (e) => { if (e.target === modal) closeModal(); };

        document.getElementById('opt-counseling').onclick = () => {
          window.location.href = '<?= url('/user/services/counseling/') ?>' + '<?= strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') === 'female' ? 'female' : 'male' ?>';
        };

        const optEdu = document.getElementById('opt-education');
        if (optEdu) {
          optEdu.onclick = () => {
            window.location.href = '<?= url('/user/services/education/female') ?>';
          };
        }

        <?php if (strtolower($_SESSION['sex'] ?? $_SESSION['gender'] ?? '') !== 'female'): ?>
        document.getElementById('opt-marriage').onclick = () => {
          window.location.href = '<?= url('/user/services/marriage-form') ?>';
        };
        
        document.getElementById('opt-studies').onclick = () => {
          window.location.href = '<?= url('/user/services/conversion-form') ?>';
        };
        <?php endif; ?>
      }

      function showDamayanSelectionModal() {
        const modalHtml = `
          <div id="damayan-selection-modal" style="
            position:fixed;inset:0;z-index:999999;
            background:rgba(15,30,22,0.65);backdrop-filter:blur(8px);
            display:flex;align-items:center;justify-content:center;
            opacity:0;transition:opacity 0.3s ease;
          ">
            <div id="damayan-modal-content" style="
              background:white;border-radius:24px;width:100%;max-width:620px;
              padding:40px;box-shadow:0 25px 70px rgba(0,0,0,0.3);
              transform:translateY(20px) scale(0.95);transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            ">
              <div style="text-align:center;margin-bottom:32px;">
                <h5 style="font-family:'Lora',serif;font-size:1.6rem;font-weight:700;color:var(--primary-dark);margin:0 0 8px;">Damayan Services</h5>
                <p style="font-size:0.95rem;color:var(--text-muted);">How can we assist you today?</p>
              </div>
              
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;">
                <!-- Burial Service -->
                <div class="damayan-opt-card" id="opt-burial" style="
                  border:2px solid var(--primary-light);border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;background:rgba(23,107,69,0.02);
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:var(--primary-light);color:white;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 20px rgba(23,107,69,0.2);">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Burial Service</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Request assistance for funeral and burial arrangements</p>
                </div>

                <!-- Charity -->
                <div class="damayan-opt-card" id="opt-charity" style="
                  border:2px solid #f0f2f1;border-radius:20px;padding:24px 20px;text-align:center;
                  cursor:pointer;transition:all 0.3s ease;
                ">
                  <div style="width:64px;height:64px;border-radius:18px;background:rgba(224,184,74,0.1);color:#c79a2b;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg viewBox="0 0 24 24" style="width:32px;height:32px;fill:currentColor;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  </div>
                  <h6 style="font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:var(--primary-dark);margin:0 0 6px;">Charity & Donation</h6>
                  <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">Sadaqah, Zakat, and benevolent community support</p>
                </div>
              </div>

              <div style="text-align:center;">
                <button id="close-damayan-modal" style="
                  padding:12px 32px;border-radius:12px;border:1.5px solid var(--border);
                  background:white;color:var(--text-muted);font-weight:700;font-size:0.9rem;
                  cursor:pointer;transition:all 0.2s;
                ">Close Selection</button>
              </div>
            </div>
          </div>

          <style>
            .damayan-opt-card:hover {
              border-color: var(--primary-light) !important;
              transform: translateY(-8px);
              box-shadow: 0 15px 40px rgba(15,92,58,0.12);
            }
            .damayan-opt-card:active { transform: translateY(-4px); }
          </style>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = document.getElementById('damayan-selection-modal');
        const content = document.getElementById('damayan-modal-content');

        setTimeout(() => {
          modal.style.opacity = '1';
          content.style.transform = 'translateY(0) scale(1)';
        }, 10);

        const closeModal = () => {
          modal.style.opacity = '0';
          content.style.transform = 'translateY(20px) scale(0.95)';
          setTimeout(() => modal.remove(), 300);
        };

        document.getElementById('close-damayan-modal').onclick = closeModal;
        modal.onclick = (e) => { if (e.target === modal) closeModal(); };

        document.getElementById('opt-burial').onclick = () => {
          window.location.href = '<?= url('/user/services/burial-form') ?>';
        };

        document.getElementById('opt-charity').onclick = () => {
          window.location.href = '<?= url('/user/services/charity') ?>';
        };
      }
  </script>

  <!-- Notification Badge System -->
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    initAdminData();
    initReportsData();
    initNotifBadge('tenant');
  </script>
</body>
</html>
<?php
}
?>
