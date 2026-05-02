<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    .insight-card { cursor: default; }
    .kpi-trend { font-size: 0.72rem; font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
    .kpi-trend.up { color: var(--success); }
    .kpi-trend.info { color: var(--accent); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'analytics';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Departmental Analytics</div>
          <div class="top-bar-subtitle">Damayan Social Welfare — Monitoring community support and service performance</div>
        </div>
        <div class="top-bar-actions">
           <button class="btn-topbar" onclick="window.print()">🖨️ Export Report</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Damayan Department</a><span class="sep">›</span><span class="current">Analytical Reports</span>
        </div>

        <?php
          $bTotal = ($burial['total'] ?? 0) ?: 1;
          $bAppRate = round((($burial['approved'] ?? 0) / $bTotal) * 100);
          $bCompRate = round((($burial['completed'] ?? 0) / $bTotal) * 100);
        ?>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Burial Requests</div>
            <div class="insight-value" style="color:var(--damayan-accent);"><?= $burial['total'] ?? 0 ?></div>
            <div class="kpi-trend info">
               <span class="badge-status badge-approved" style="font-size:10px;"><?= $bAppRate ?>% Approved</span>
               <span class="badge-status badge-active" style="font-size:10px;"><?= $bCompRate ?>% Finished</span>
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Recent Activity (Deaths)</div>
            <div class="insight-value" style="color:var(--danger);"><?= $burial['by_day'] ?? 0 ?> <span style="font-size:0.8rem;color:var(--text-muted);font-weight:400;">Today</span></div>
            <div class="kpi-trend">
               <span>Week: <b><?= $burial['by_week'] ?? 0 ?></b></span>
               <span style="margin-left:8px;">Month: <b><?= $burial['by_month'] ?? 0 ?></b></span>
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Community Donors</div>
            <div class="insight-value" style="color:var(--accent);"><?= $charity['unique_donors'] ?? 0 ?></div>
            <div class="kpi-trend info">Total Contributors</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Donations</div>
            <div class="insight-value" style="color:var(--info);">₱<?= number_format($charity['total_amount'] ?? 0) ?></div>
            <div class="kpi-trend">Verified Impact</div>
          </div>
        </div>

        <div class="analytics-grid">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--damayan-accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Monthly Burial Activity (Deaths)
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container">
                <canvas id="burialDemandChart"></canvas>
              </div>
            </div>
          </div>

          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--damayan-accent);margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Monthly Donation Volume
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container">
                <canvas id="donationVolumeChart"></canvas>
              </div>
            </div>
          </div>

          <div class="section-card" style="grid-column: span 1;">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                Service Distribution
              </h6>
            </div>
            <div class="section-card-body">
                <div class="chart-container">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
          </div>

          <div class="section-card" style="grid-column: span 1;">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--damayan-accent);margin-right:8px;"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                Status Breakdown
              </h6>
            </div>
            <div class="section-card-body">
                <div class="chart-container">
                    <canvas id="burialChart"></canvas>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    document.addEventListener("DOMContentLoaded", function() {
      const colors = {
        primary: '#176b45',
        accent: '#c79a2b',
        info: '#1f6f5a',
        success: '#2f8a60',
        danger: '#8b2e2e',
        warning: '#eab308'
      };

      const monthlyLabels = <?= json_encode($burial['monthly_labels'] ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) ?>;

      // Burial Monthly Chart
      new Chart(document.getElementById('burialDemandChart'), {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Deaths Recorded',
            data: <?= json_encode($burial['monthly_data'] ?? []) ?>,
            backgroundColor: colors.primary,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
      });

      // Donation Monthly Chart
      new Chart(document.getElementById('donationVolumeChart'), {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [{
            label: 'Total Donations',
            data: <?= json_encode($charity['monthly_data'] ?? []) ?>,
            backgroundColor: colors.accent,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
      });

      // Distribution Chart
      new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
          labels: ['Burial Services', 'Charity Donors'],
          datasets: [{
            data: [<?= $burial['total'] ?? 0 ?>, <?= $charity['total_count'] ?? 0 ?>],
            backgroundColor: [colors.primary, colors.accent],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom' } }
        }
      });

      // Burial Status
      new Chart(document.getElementById('burialChart'), {
        type: 'pie',
        data: {
          labels: ['Completed', 'Approved', 'Pending'],
          datasets: [{
            data: [<?= $burial['completed'] ?? 0 ?>, <?= $burial['approved'] ?? 0 ?>, <?= $burial['pending'] ?? 0 ?>],
            backgroundColor: [colors.success, colors.info, colors.warning]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'right' } }
        }
      });
    });
  </script>
</body>
</html>
