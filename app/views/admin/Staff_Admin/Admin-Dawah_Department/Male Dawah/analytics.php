<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Department Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
        --male-accent: #14532d;
        --male-dark: #064e3b;
        --male-light: #f0fdf4;
    }
    .top-bar-title { color: var(--male-dark); }
    .breadcrumb-bar .current { color: var(--male-accent); }
    
    .analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .card-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--male-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    
    .chart-container { position: relative; height: 300px; width: 100%; }
    
    .admin-insights { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .insight-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s; }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--male-accent); }
    .insight-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--male-dark); line-height: 1; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'analytics';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--male-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--male-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Department Analytics</div>
            <div class="top-bar-subtitle">Data-driven insights for Da'wah services and education</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a>
          <span class="separator">/</span>
          <span class="current">Analytical Reports</span>
        </div>

        <!-- KPI SUMMARY -->
        <?php
          $cTotal = ($counseling['total'] ?? 0) ?: 1;
          $cAppRate = round((($counseling['approved'] ?? 0) / $cTotal) * 100);
          
          $eTotal = ($education['total'] ?? 0) ?: 1;
          $eActiveRate = round((($education['active'] ?? 0) / $eTotal) * 100);
          
          $mTotal = ($marriage['total'] ?? 0) ?: 1;
          $mAppRate = round((($marriage['approved'] ?? 0) / $mTotal) * 100);
        ?>
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Counseling Sessions</div>
            <div class="insight-value"><?= $counseling['total'] ?? 0 ?></div>
            <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px;">
               <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
               <?= $cAppRate ?>% Approval Rate
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Marriage Files</div>
            <div class="insight-value"><?= $marriage['total'] ?? 0 ?></div>
            <div style="font-size: 0.72rem; color: #10b981; font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px;">
               <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
               <?= $mAppRate ?>% Finalized
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Shahada Records</div>
            <div class="insight-value">0</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); font-weight: 700; margin-top: 8px;">0% Growth</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Enrolled Students</div>
            <div class="insight-value"><?= $education['total'] ?? 0 ?></div>
            <div style="font-size: 0.72rem; color: var(--male-accent); font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px;">
               <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
               <?= $eActiveRate ?>% Active Enrollment
            </div>
          </div>
        </div>

        <div class="analytics-grid">
          <!-- Counseling Distribution -->
          <div class="card">
            <h6 class="card-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Counseling Status Distribution
            </h6>
            <div class="chart-container">
              <canvas id="counselingChart"></canvas>
            </div>
          </div>

          <!-- Service Comparison -->
          <div class="card">
            <h6 class="card-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.09-4-4L2 15.68l1.5 1.5z"/></svg>
              Service Demand Overview
            </h6>
            <div class="chart-container">
              <canvas id="serviceChart"></canvas>
            </div>
          </div>

          <!-- Education Programs -->
          <div class="card">
            <h6 class="card-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Islamic Education Enrollment
            </h6>
            <div class="chart-container">
              <canvas id="educationChart"></canvas>
            </div>
          </div>

          <!-- Monthly Trends -->
          <div class="card">
            <h6 class="card-title">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--male-accent);"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
              Application Trends (Last 6 Months)
            </h6>
            <div class="chart-container">
              <canvas id="trendChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Chart.defaults.font.family = "'Source Sans 3', sans-serif";
      Chart.defaults.color = '#6f7f78';

      const percentageTooltip = {
        callbacks: {
          label: function(context) {
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const value = context.raw;
            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
            return `${context.label}: ${value} (${percentage}%)`;
          }
        }
      };

      // 1. Counseling Status (Pie)
      new Chart(document.getElementById('counselingChart'), {
        type: 'pie',
        data: {
          labels: ['Approved', 'Pending', 'Rejected'],
          datasets: [{
            data: [<?= $counseling['approved'] ?? 0 ?>, <?= $counseling['pending'] ?? 0 ?>, <?= $counseling['rejected'] ?? 0 ?>],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { 
            legend: { position: 'bottom' },
            tooltip: percentageTooltip
          }
        }
      });

      // 2. Service Comparison (Bar)
      new Chart(document.getElementById('serviceChart'), {
        type: 'bar',
        data: {
          labels: ['Counseling', 'Marriage', 'Shahada', 'Education'],
          datasets: [{
            label: 'Total Requests',
            data: [<?= $counseling['total'] ?? 0 ?>, <?= $marriage['total'] ?? 0 ?>, 0, <?= $education['total'] ?? 0 ?>],
            backgroundColor: '#14532d',
            borderRadius: 8
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { 
            legend: { display: false },
            tooltip: percentageTooltip
          },
          scales: { y: { beginAtZero: true } }
        }
      });

      // 3. Education (Doughnut)
      new Chart(document.getElementById('educationChart'), {
        type: 'doughnut',
        data: {
          labels: ['Enrolled', 'Pending', 'Completed', 'Dropped'],
          datasets: [{
            data: [<?= $education['active'] ?? 0 ?>, <?= $education['pending'] ?? 0 ?>, <?= $education['completed'] ?? 0 ?>, <?= $education['dropped'] ?? 0 ?>],
            backgroundColor: ['#14532d', '#f59e0b', '#10b981', '#94a3b8'],
            borderWidth: 0
          }]
        },
        options: {
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: { 
            legend: { position: 'bottom' },
            tooltip: percentageTooltip
          }
        }
      });

      // 4. Trends (Line)
      new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Applications',
            data: [12, 19, 15, 22, 18, 25],
            borderColor: '#14532d',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(20, 83, 45, 0.05)'
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } }
        }
      });
    });
  </script>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
  </script>
</body>
</html>
