<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Female']);
?>
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
        --female-accent: #B8860B;
        --female-dark: #78350f;
        --female-light: #fffbeb;
    }
    .top-bar-title { color: var(--female-dark); }
    .breadcrumb-bar .current { color: var(--female-accent); }
    
    .analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
    .card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .card-title { font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: var(--female-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    
    .chart-container { position: relative; height: 300px; width: 100%; }
    
    .admin-insights { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .insight-card { background: white; padding: 24px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s; }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--female-accent); }
    .insight-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--female-dark); line-height: 1; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'analytics';
      $dawah_type = 'female';
      include BASE_PATH . '/app/views/admin/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--female-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--female-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Department Analytics (Female)</div>
            <div class="top-bar-subtitle">Data-driven insights for sisters' services and education</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Dashboard</a>
          <span class="sep">›</span>
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
            <div style="font-size: 0.72rem; color: var(--female-accent); font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px;">
               <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
               <?= $eActiveRate ?>% Active Enrollment
            </div>
          </div>
        </div>

        <div class="analytics-grid">
          <div class="card">
            <h6 class="card-title">Counseling Status Distribution</h6>
            <div class="chart-container">
              <canvas id="counselingChart"></canvas>
            </div>
          </div>
          <div class="card">
            <h6 class="card-title">Service Demand Overview</h6>
            <div class="chart-container">
              <canvas id="serviceChart"></canvas>
            </div>
          </div>
          <div class="card">
            <h6 class="card-title">Islamic Education Enrollment</h6>
            <div class="chart-container">
              <canvas id="educationChart"></canvas>
            </div>
          </div>
          <div class="card">
            <h6 class="card-title">Application Trends</h6>
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
          plugins: { legend: { position: 'bottom' }, tooltip: percentageTooltip }
        }
      });

      new Chart(document.getElementById('serviceChart'), {
        type: 'bar',
        data: {
          labels: ['Counseling', 'Marriage', 'Shahada', 'Education'],
          datasets: [{
            label: 'Total Requests',
            data: [<?= $counseling['total'] ?? 0 ?>, <?= $marriage['total'] ?? 0 ?>, 0, <?= $education['total'] ?? 0 ?>],
            backgroundColor: '#B8860B',
            borderRadius: 8
          }]
        },
        options: {
          maintainAspectRatio: false,
          plugins: { legend: { display: false }, tooltip: percentageTooltip },
          scales: { y: { beginAtZero: true } }
        }
      });

      new Chart(document.getElementById('educationChart'), {
        type: 'doughnut',
        data: {
          labels: ['Enrolled', 'Pending', 'Completed', 'Dropped'],
          datasets: [{
            data: [<?= $education['active'] ?? 0 ?>, <?= $education['pending'] ?? 0 ?>, <?= $education['completed'] ?? 0 ?>, <?= $education['dropped'] ?? 0 ?>],
            backgroundColor: ['#B8860B', '#f59e0b', '#10b981', '#94a3b8'],
            borderWidth: 0
          }]
        },
        options: {
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: { legend: { position: 'bottom' }, tooltip: percentageTooltip }
        }
      });

      new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Applications',
            data: [8, 12, 10, 15, 14, 20],
            borderColor: '#B8860B',
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(184, 134, 11, 0.05)'
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
