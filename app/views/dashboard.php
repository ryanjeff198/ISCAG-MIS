<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

if (!function_exists('asset')) {
    function asset($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        return rtrim($baseUrl, '/') . "/public/" . ltrim($path, '/'); 
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
  <title>ISCAG MIS — Admin Hub</title>
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
          <img src="<?= asset('assets/ISCAG_Logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
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
          <!-- Operations -->
          <div class="hub-card">
            <div class="hub-header">
              <div class="hub-icon"><svg viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg></div>
              <div class="hub-title"><h3>Apartment Management</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/apartment_records') ?>" class="hub-link">
                Apartment Inventory <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/tenant_confirmation') ?>" class="hub-link">
                Pending Tenant Approvals <span class="hub-link-arrow">→</span>
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
              <div class="hub-title"><h3>Financial Control</h3></div>
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
              <div class="hub-title"><h3>Community Records</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/daawah_records') ?>" class="hub-link">
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
              <div class="hub-icon" style="background:var(--primary);"><svg viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg></div>
              <div class="hub-title"><h3>System Governance</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/records') ?>" class="hub-link">
                User Management <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/audit_logs') ?>" class="hub-link">
                System Audit Trails <span class="hub-link-arrow">→</span>
              </a>
              <a href="<?= url('/admin/mis_admin/notification') ?>" class="hub-link">
                Internal Notifications <span class="hub-link-arrow">→</span>
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
          'name' => $info['full_name'] ?? trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')),
          'email' => $info['email'] ?? ($account['email'] ?? ''),
          'gender' => !empty($info['sex']) ? $info['sex'] : ($account['sex'] ?? ''),
          'phone' => $info['phone'] ?? ($account['contactnum'] ?? ''),
          'dob' => $info['birthdate'] ?? '',
          'civil' => $info['civil_status'] ?? '',
          'address' => $info['address'] ?? '',
          'occupation' => $info['occupation'] ?? '',
          'arabicName' => $info['muslimname'] ?? '',
          'revertYear' => !empty($info['dateofshahadah']) ? date('Y', strtotime($info['dateofshahadah'])) : '',
      ];
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MIS — User Dashboard</title>
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
          <p>Welcome to the Masjid Management Information System. Select a service below to submit a request.</p>

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

        <!-- SERVICE CARDS -->
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
    const DB_USER = <?= json_encode($dbUser) ?>;
    const PROFILE_FIELDS = ['name', 'email', 'gender', 'phone', 'address', 'dob', 'civil', 'occupation', 'arabicName', 'revertYear'];
    const FIELD_LABELS = { name: 'Full Name', email: 'Email Address', gender: 'Gender', phone: 'Contact Number', address: 'Complete Address', dob: 'Date of Birth', civil: 'Civil Status', occupation: 'Occupation', arabicName: 'Muslim / Arabic Name', revertYear: 'Year Reverted' };
    const DEFAULT_USER = {
      id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
      name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
      email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
      gender: '<?= addslashes($_SESSION['gender'] ?? "") ?>',
      phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', membership: '', revertYear: '', apartment: '', profileComplete: false
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
      // Even if empty, we overwrite localStorage/Mock defaults.
      Object.keys(DB_USER).forEach(key => {
        user[key] = DB_USER[key] || '';
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
    const isComplete = percentage === 100;

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
    if (navRole && navRole.textContent === 'Not Verified') {
      navRole.style.color = 'var(--warning)';
    } else if (navRole) {
      navRole.style.color = 'var(--success)';
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

    // ── Da'wah sidebar dropdown (gender-based, correct paths) ──
    const dawahMenu = document.getElementById('dawah-menu');
    const dawahTrigger = document.getElementById('dawah-trigger');
    if (String(user.gender).toLowerCase() === 'female') {
      dawahMenu.innerHTML = `
      <a href="<?= url('/user/services/counseling/female') ?>">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        Sisters' Counseling
      </a>`;
      dawahTrigger.setAttribute('data-href', "<?= url('/user/services/counseling/female') ?>");
    } else {
      dawahMenu.innerHTML = `
      <a href="<?= url('/user/services/counseling/male') ?>">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
        Brothers' Counseling
      </a>`;
      dawahTrigger.setAttribute('data-href', "<?= url('/user/services/counseling/male') ?>");
    }

    // ── Build service cards ──
    const serviceGrid = document.getElementById('service-grid');

    const dawahHref = String(user.gender).toLowerCase() === 'female'
      ? "<?= url('/user/services/counseling/female') ?>"
      : "<?= url('/user/services/counseling/male') ?>";

    const services = [
      {
        id: 'damayan',
        title: 'Damayan — Burial Services',
        desc: 'Submit a formal request for burial services for the deceased. Fill in the necessary details about the deceased, family contact, and burial preferences.',
        icon: '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>',
        iconClass: '',
        href: '<?= url('/user/services/burial-form') ?>',
        btnText: 'Request Service'
      },
      {
        id: 'dawah',
        title: !isComplete ? "Da'wah — Counseling Services"
          : String(user.gender).toLowerCase() === 'female' ? "Da'wah — Sisters' Counseling"
            : "Da'wah — Brothers' Counseling",
        desc: !isComplete
          ? 'Request a confidential counseling session for personal, family, or spiritual matters. Complete your profile to access gender-specific counseling services.'
          : String(user.gender).toLowerCase() === 'female'
            ? 'Request a confidential session with our female counselors. All sessions are conducted with utmost privacy and respect for Islamic values.'
            : 'Request a confidential counseling session with our male counselors for personal, family, or spiritual matters. Schedule your preferred appointment time.',
        icon: '<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>',
        iconClass: String(user.gender).toLowerCase() === 'female' ? 'purple' : 'teal',
        href: dawahHref,
        btnText: 'Request Service'
      },
      {
        id: 'apartment',
        title: 'Apartment Application',
        desc: 'Apply for a housing unit in the Masjid apartment complex. Submit your family details and preferred unit type for review by the Apartment Management.',
        icon: '<path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zm-8 4H7v-2h2v2zm0-4H7V9h2v2zm0-4H7V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2z"/>',
        iconClass: 'green',
        href: '<?= url('/user/apartment/apply') ?>',
        btnText: 'Apply Now'
      }
    ];

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

      card.addEventListener('click', (e) => {
        e.preventDefault();
        if (!isComplete) {
          showAccessModal({
            percentage,
            missingFields,
            redirectUrl: '<?= url('/user/profile') ?>?edit=true'
          });
        } else {
          window.location.href = svc.href;
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
        const type = r.type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        return '<tr>' +
          '<td class="td-id">#' + r.id + '</td>' +
          '<td>' + type + '</td>' +
          '<td>' + r.date + '</td>' +
          '<td><span class="badge-status badge-' + r.status + '">' + r.status + '</span></td>' +
          '<td style="color:var(--text-muted);">' + (r.updatedAt || r.date) + '</td>' +
          '</tr>';
      }).join('');
    }

    // ── Sidebar toggle ──
    const sidebar = document.getElementById('sidebar');
    document.getElementById('sidebar-toggle').addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      // Close any open dropdowns when collapsing
      if (sidebar.classList.contains('collapsed')) {
        document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
        document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
      }
    });

    // ── Lock/Unlock service dropdowns based on profile completion ──
    function applyDropdownLocks() {
      const wraps = ['damayan-wrap', 'dawah-wrap', 'apartment-wrap'];
      wraps.forEach(id => {
        const wrap = document.getElementById(id);
        if (!wrap) return;
        if (isComplete) {
          wrap.classList.remove('locked');
        } else {
          wrap.classList.add('locked');
        }
      });
    }
    applyDropdownLocks();

    // ── Dropdown toggles (with lock check) ──
    function initDropdown(triggerId, menuId, wrapId) {
      const trigger = document.getElementById(triggerId);
      const menu = document.getElementById(menuId);
      const wrap = document.getElementById(wrapId);
      trigger.addEventListener('click', () => {
        // If locked, show access modal
        if (wrap && wrap.classList.contains('locked')) {
          showAccessModal({ percentage, missingFields, redirectUrl: '<?= url('/user/profile') ?>' });
          return;
        }
        // If sidebar is collapsed, navigate directly to the service page
        if (sidebar.classList.contains('collapsed')) {
          const href = trigger.getAttribute('data-href');
          if (href) window.location.href = href;
          return;
        }
        // Normal dropdown toggle when expanded
        const isOpen = menu.classList.contains('open');
        document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
        document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
        if (!isOpen) { menu.classList.add('open'); trigger.classList.add('open'); }
      });
    }
    initDropdown('damayan-trigger', 'damayan-menu', 'damayan-wrap');
    initDropdown('dawah-trigger', 'dawah-menu', 'dawah-wrap');
    initDropdown('apartment-trigger', 'apartment-menu', 'apartment-wrap');
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
