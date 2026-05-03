<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Apartment Analytics — ISCAG MIS</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .analytics-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 24px;
      margin-top: 20px;
    }
    .chart-container {
      background: white;
      padding: 28px;
      border-radius: 20px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 20px rgba(0,0,0,0.04);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
    }
    .chart-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    .chart-header {
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .chart-title {
      font-family: 'Lora', serif;
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .chart-title svg {
      width: 20px;
      height: 20px;
      fill: var(--accent);
    }
    .chart-summary {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-muted);
      background: rgba(0,0,0,0.03);
      padding: 4px 12px;
      border-radius: 20px;
    }
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 24px;
    }
    .kpi-card {
      background: white;
      padding: 20px;
      border-radius: 16px;
      border: 1px solid var(--border);
      text-align: center;
    }
    .kpi-card h3 {
      font-size: 1.8rem;
      font-weight: 800;
      margin: 0;
      color: var(--primary-dark);
    }
    .kpi-card span {
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    @media (max-width: 1200px) {
      .kpi-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 992px) {
      .analytics-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
      .kpi-row { grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <?php 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Real-Time Insights</div>
          <div class="top-bar-subtitle">Dynamic performance monitoring for Apartment Operations</div>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/apartment') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Analytics</span>
        </div>

        <div class="kpi-row">
          <div class="kpi-card" style="border-top: 4px solid var(--accent);">
            <h3><?= array_sum($appStats) ?></h3>
            <span>Total Applications</span>
          </div>
          <div class="kpi-card" style="border-top: 4px solid var(--success);">
            <h3><?= $occStats['Occupied'] ?></h3>
            <span>Occupied Units</span>
          </div>
          <div class="kpi-card" style="border-top: 4px solid var(--primary);">
            <h3>₱<?= number_format(array_sum($billingSummary)) ?></h3>
            <span>Total Receivables</span>
          </div>
          <div class="kpi-card" style="border-top: 4px solid var(--warning);">
            <h3><?= $appStats['Pending'] + $appStats['Applied'] ?></h3>
            <span>Pending Review</span>
          </div>
        </div>

        <div class="analytics-grid">
          <!-- Application Distribution -->
          <div class="chart-container">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.52-4.25 8.99-8.99h-8.99z"/></svg>
                Application Status Distribution
              </div>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="appStatusChart"></canvas>
            </div>
          </div>

          <!-- Unit Occupancy -->
          <div class="chart-container">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg>
                Current Unit Availability
              </div>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="roomOccupancyChart"></canvas>
            </div>
          </div>

          <!-- Billing Performance -->
          <div class="chart-container">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" /></svg>
                Billing Status (Financial Summary)
              </div>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="billingStatusChart"></canvas>
            </div>
          </div>

          <!-- Tenant Preferences -->
          <div class="chart-container">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
                Requested Unit Types
              </div>
            </div>
            <div style="height: 320px; position: relative;">
              <canvas id="typePrefsChart"></canvas>
            </div>
          </div>

          <!-- Revenue Trend -->
          <div class="chart-container" style="grid-column: 1 / -1;">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M23 8c0 1.1-.9 2-2 2-.18 0-.35-.02-.51-.07l-3.56 3.55c.05.16.07.33.07.52 0 1.1-.9 2-2 2s-2-.9-2-2c0-.19.02-.36.07-.52l-2.55-2.55c-.16.05-.33.07-.52.07s-.36-.02-.52-.07l-4.55 4.56c.05.16.07.33.07.51 0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2c.18 0 .35.02.51.07l4.56-4.55c-.05-.16-.07-.33-.07-.51 0-1.1.9-2 2-2s2 .9 2 2c0 .19-.02.36-.07.52l2.55 2.55c.16-.05.33-.07.52-.07s.36.02.52.07l3.55-3.56c-.05-.16-.07-.33-.07-.51 0-1.1.9-2 2-2s2 .9 2 2z"/></svg>
                Revenue Growth Trend
              </div>
              <div class="chart-summary">Last 12 Months</div>
            </div>
            <div style="height: 350px; position: relative;">
              <canvas id="revenueTrendChart"></canvas>
            </div>
          </div>

          <!-- Building Occupancy -->
          <div class="chart-container" style="grid-column: 1 / -1;">
            <div class="chart-header">
              <div class="chart-title">
                <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zM7 19H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm4 4H9v-2h2v2zm0-4H9V9h2v2zm0-4H9V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 12h-2v-2h2v2zm0-4h-2v-2h2v2z"/></svg>
                Building-wise Occupancy (Occupied vs Available)
              </div>
            </div>
            <div style="height: 350px; position: relative;">
              <canvas id="buildingOccupancyChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      standardizePage('staff');
      initAnalytics();
    });

    function initAnalytics() {
      Chart.defaults.font.family = "'Source Sans 3', sans-serif";
      Chart.defaults.color = '#64748b';

      // 1. App Status Chart
      const appData = <?= json_encode($appStats) ?>;
      new Chart(document.getElementById('appStatusChart'), {
        type: 'doughnut',
        data: {
          labels: Object.keys(appData),
          datasets: [{
            data: Object.values(appData),
            backgroundColor: ['#4facfe', '#2f8a60', '#ff5e62', '#f9d423', '#c79a2b', '#1e3c72'],
            borderWidth: 0,
            hoverOffset: 20
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { weight: '600' } } }
          },
          cutout: '75%'
        }
      });

      // 2. Room Occupancy Chart
      const occData = <?= json_encode($occStats) ?>;
      new Chart(document.getElementById('roomOccupancyChart'), {
        type: 'pie',
        data: {
          labels: Object.keys(occData),
          datasets: [{
            data: Object.values(occData),
            backgroundColor: ['#2f8a60', '#ff5e62', '#f9d423'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { weight: '600' } } }
          }
        }
      });

      // 3. Billing Status Chart
      const billData = <?= json_encode($billingSummary) ?>;
      new Chart(document.getElementById('billingStatusChart'), {
        type: 'bar',
        data: {
          labels: Object.keys(billData),
          datasets: [{
            label: 'Total Amount (₱)',
            data: Object.values(billData),
            backgroundColor: ['#ff5e62', '#2f8a60', '#f9d423'],
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
          },
          plugins: { legend: { display: false } }
        }
      });

      // 4. Tenant Preferences Chart
      const prefData = <?= json_encode($typePrefs) ?>;
      new Chart(document.getElementById('typePrefsChart'), {
        type: 'polarArea',
        data: {
          labels: Object.keys(prefData),
          datasets: [{
            data: Object.values(prefData),
            backgroundColor: ['rgba(79, 172, 254, 0.6)', 'rgba(47, 138, 96, 0.6)', 'rgba(255, 94, 98, 0.6)', 'rgba(249, 212, 35, 0.6)', 'rgba(199, 154, 43, 0.6)'],
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15 } }
          }
        }
      });

      // 5. Revenue Trend Chart
      const revTrend = <?= json_encode($revenueTrend) ?>;
      new Chart(document.getElementById('revenueTrendChart'), {
        type: 'line',
        data: {
          labels: Object.keys(revTrend),
          datasets: [{
            label: 'Collected Revenue',
            data: Object.values(revTrend),
            borderColor: '#2f8a60',
            backgroundColor: 'rgba(47, 138, 96, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#fff',
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' } },
            x: { grid: { display: false } }
          },
          plugins: { legend: { display: false } }
        }
      });

      // 6. Building Occupancy Chart
      const buildStats = <?= json_encode($buildingStats) ?>;
      new Chart(document.getElementById('buildingOccupancyChart'), {
        type: 'bar',
        data: {
          labels: buildStats.map(s => s.building),
          datasets: [
            {
              label: 'Occupied',
              data: buildStats.map(s => s.occupied),
              backgroundColor: '#ff5e62',
              borderRadius: 6
            },
            {
              label: 'Available',
              data: buildStats.map(s => s.available),
              backgroundColor: '#2f8a60',
              borderRadius: 6
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: { stacked: true, beginAtZero: true, grid: { display: false } },
            x: { stacked: true, grid: { display: false } }
          }
        }
      });
    }
  </script>
</body>
</html>
