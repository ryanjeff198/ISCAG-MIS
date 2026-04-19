<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    <!-- SIDEBAR -->
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
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
  </div>

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
          plugins: {
            legend: { position: 'bottom' }
          },
          scales: {
            y: { beginAtZero: true, grid: { color: '#eee' } },
            x: { grid: { display: false } }
          }
        }
      });
      initSidebar();
      initDropdowns();
    });
  </script>
</body>

</html>
