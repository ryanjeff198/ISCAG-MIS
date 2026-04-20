<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Admin Hub</title>
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <!-- Chart.js Integration -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="app-wrapper">
    <!-- SIDEBAR -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          
          <div>
            <div class="top-bar-title">Administrative Hub</div>
            <div class="top-bar-subtitle">Unified Management & Service Monitoring</div>
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
              <div class="hub-icon"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg></div>
              <div class="hub-title"><h3>Pending Requests</h3></div>
            </div>
            <div class="hub-links">
              <a href="<?= url('/admin/mis_admin/apartment_confirmation') ?>" class="hub-link">
                Apartment Confirmations <span class="hub-link-arrow">→</span>
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
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      standardizePage('admin');

      // ── MOCK DATA ENGINE ──
      const stats = {
        users: 1248,
        pending: 14,
        occupancy: "92%",
        billing: "₱142,500"
      };

      // Populate Stats
      document.getElementById('stat-users').innerText = stats.users.toLocaleString();
      document.getElementById('stat-pending').innerText = stats.pending;
      document.getElementById('stat-occupancy').innerText = stats.occupancy;
      document.getElementById('stat-billing').innerText = stats.billing;

      // ── LOGS ENGINE ──
      const logs = [
        { text: "System broadcast sent to all Tenants", time: "2 minutes ago" },
        { text: "Verification approved for App ID APP-2024-089", time: "45 minutes ago" },
        { text: "Database backup completed successfully", time: "2 hours ago" },
        { text: "Audit log purged for archived records", time: "4 hours ago" }
      ];

      const logContainer = document.getElementById('recent-logs');
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

      // ── CHART ENGINE ──
      const ctx = document.getElementById('activityChart').getContext('2d');
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
          plugins: { legend: { position: 'bottom' } },
          scales: {
            y: { beginAtZero: true, grid: { color: '#eee' } },
            x: { grid: { display: false } }
          }
        }
      });
    });
  </script>
</body>

</html>

