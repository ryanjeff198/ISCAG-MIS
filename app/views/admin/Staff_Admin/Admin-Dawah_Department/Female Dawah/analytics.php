<?php
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
    .analytics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 24px; }
    .chart-container { position: relative; height: 320px; width: 100%; }
    
    .insight-card { cursor: default; }
    .insight-card:hover { border-color: var(--accent); }
    
    .kpi-trend { font-size: 0.72rem; font-weight: 700; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
    .kpi-trend.up { color: var(--success); }
    .kpi-trend.info { color: var(--accent); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'analytics';
      $dawah_type = 'female';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Department Analytics</div>
          <div class="top-bar-subtitle">Female Da'wah Department — Data-driven insights for sisters' services</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar" onclick="window.print()">🖨️ Export Report</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Analytical Reports</span>
        </div>

        <?php
          $cTotal = ($counseling['total'] ?? 0) ?: 1;
          $cAppRate = round((($counseling['approved'] ?? 0) / $cTotal) * 100);
          
          $eTotal = ($education['total'] ?? 0) ?: 1;
          $eActiveRate = round((($education['active'] ?? 0) / $eTotal) * 100);
        ?>
        
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Counseling Sessions</div>
            <div class="insight-value" style="color:var(--primary);"><?= $counseling['total'] ?? 0 ?></div>
            <div class="kpi-trend up">
               <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
               <?= $cAppRate ?>% Approval Rate
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">New Muslim Program</div>
            <div class="insight-value" style="color:var(--accent);">0</div>
            <div class="kpi-trend info">
               <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
               Active Tracking
            </div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Sisters' Outreach</div>
            <div class="insight-value" style="color:var(--info);">0</div>
            <div class="kpi-trend" style="color:var(--text-muted);">0% Growth</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Active Students</div>
            <div class="insight-value" style="color:var(--success);"><?= $education['total'] ?? 0 ?></div>
            <div class="kpi-trend info">
               <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
               <?= $eActiveRate ?>% Retention Rate
            </div>
          </div>
        </div>

        <div class="analytics-grid">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Service Distribution
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container">
                <canvas id="distributionChart"></canvas>
              </div>
            </div>
          </div>

          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Counseling Status Breakdown
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container">
                <canvas id="counselingChart"></canvas>
              </div>
            </div>
          </div>

          <div class="section-card" style="grid-column: span 2;">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79s7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.5-9.11 0-12.58s9.21-3.47 12.72 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.75 1.23-4.25-2.56V8h1.5z"/></svg>
                Primary Concerns Breakdown
              </h6>
            </div>
            <div class="section-card-body">
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:32px; align-items:center;">
                <div class="chart-container" style="height:250px;">
                  <canvas id="concernsChart"></canvas>
                </div>
                <div id="concerns-list">
                  <?php if (!empty($counseling['concerns'])): ?>
                    <table style="width:100%; font-size:0.85rem; border-collapse:collapse;">
                      <thead>
                        <tr style="text-align:left; border-bottom:1px solid var(--border);">
                          <th style="padding:8px 0;">Concern Type</th>
                          <th style="padding:8px 0; text-align:right;">Requests</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($counseling['concerns'] as $label => $count): ?>
                          <tr style="border-bottom:1px solid var(--border-light);">
                            <td style="padding:8px 0;"><?= htmlspecialchars($label) ?></td>
                            <td style="padding:8px 0; text-align:right; font-weight:700; color:var(--primary);"><?= $count ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <div style="text-align:center; color:var(--text-muted); padding:20px;">No concern data recorded yet.</div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                <?= ucfirst($dawah_type) ?> Student Enrollment
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container" style="height: 250px;">
                <canvas id="educationChart"></canvas>
              </div>
            </div>
          </div>

          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Monthly Age Composition (%)
              </h6>
            </div>
            <div class="section-card-body">
              <div class="chart-container" style="height: 250px;">
                <canvas id="monthlyAgeCompositionChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-top:24px;">
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                Active Enrollment by Age
              </h6>
            </div>
            <div class="section-card-body" style="max-height: 300px; overflow-y: auto;">
              <?php if (!empty($education['active_ages'])): ?>
                <table style="width:100%; font-size:0.85rem; border-collapse:collapse;">
                  <thead>
                    <tr style="text-align:left; border-bottom:1px solid var(--border);">
                      <th style="padding:10px 0;">Student Age</th>
                      <th style="padding:10px 0; text-align:right;">Active Students</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($education['active_ages'] as $age => $count): ?>
                      <tr style="border-bottom:1px solid var(--border-light);">
                        <td style="padding:10px 0; font-weight:600;"><?= $age ?> years old</td>
                        <td style="padding:10px 0; text-align:right; font-weight:800; color:var(--primary);"><?= $count ?> students</td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div style="text-align:center; color:var(--text-muted); padding:40px;">No active students with recorded age.</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                Student Demographics Summary
              </h6>
            </div>
            <div class="section-card-body">
              <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin:0;">
                The majority of active students are in the 
                <strong><?php 
                  $groups = $education['age_groups'] ?? [];
                  arsort($groups);
                  echo ucfirst(str_replace('_', ' ', array_key_first($groups)));
                ?></strong> category. 
                This data helps the Female Da'wah department allocate resources and tailor curricula to the specific needs of different age groups.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
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

      // Distribution Chart
      new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
          labels: ['Counseling', 'Education', 'Outreach', 'Other'],
          datasets: [{
            data: [<?= $counseling['total'] ?? 0 ?>, <?= $education['total'] ?? 0 ?>, 0, 0],
            backgroundColor: [colors.primary, colors.info, colors.accent, '#94a3b8'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
        }
      });

      // Counseling Status
      new Chart(document.getElementById('counselingChart'), {
        type: 'pie',
        data: {
          labels: ['Approved', 'Pending', 'Rejected'],
          datasets: [{
            data: [<?= $counseling['approved'] ?? 0 ?>, <?= $counseling['pending'] ?? 0 ?>, <?= $counseling['rejected'] ?? 0 ?>],
            backgroundColor: [colors.success, colors.warning, colors.danger]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 20 } } }
        }
      });

      // Education Enrollment
      new Chart(document.getElementById('educationChart'), {
        type: 'bar',
        data: {
          labels: ['Active', 'Completed', 'Dropped', 'Pending'],
          datasets: [{
            label: 'Students',
            data: [<?= $education['active'] ?? 0 ?>, <?= $education['completed'] ?? 0 ?>, <?= $education['dropped'] ?? 0 ?>, <?= $education['pending'] ?? 0 ?>],
            backgroundColor: colors.info,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
          plugins: { legend: { display: false } }
        }
      });

      // Concerns Breakdown Chart
      new Chart(document.getElementById('concernsChart'), {
        type: 'polarArea',
        data: {
          labels: <?= json_encode(array_keys($counseling['concerns'] ?? [])) ?>,
          datasets: [{
            data: <?= json_encode(array_values($counseling['concerns'] ?? [])) ?>,
            backgroundColor: [
              'rgba(23, 107, 69, 0.7)',
              'rgba(199, 154, 43, 0.7)',
              'rgba(31, 111, 90, 0.7)',
              'rgba(47, 138, 96, 0.7)',
              'rgba(139, 46, 46, 0.7)'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } }
        }
      });

      // Monthly Age Composition Chart (100% Stacked Bar)
      <?php
        $months = array_keys($education['monthly_demographics'] ?? []);
        $groups = ['Children', 'Youth', 'Adults', 'Middle-Aged', 'Seniors'];
        $datasets = [];
        $groupColors = [
          'Children' => 'rgba(23, 107, 69, 0.8)',
          'Youth' => 'rgba(199, 154, 43, 0.8)',
          'Adults' => 'rgba(31, 111, 90, 0.8)',
          'Middle-Aged' => 'rgba(47, 138, 96, 0.8)',
          'Seniors' => 'rgba(139, 46, 46, 0.8)'
        ];

        foreach ($groups as $g) {
            $dataPoints = [];
            foreach ($months as $m) {
                $dataPoints[] = $education['monthly_demographics'][$m]['groups'][$g] ?? 0;
            }
            $datasets[] = [
                'label' => $g,
                'data' => $dataPoints,
                'backgroundColor' => $groupColors[$g]
            ];
        }
      ?>

      new Chart(document.getElementById('monthlyAgeCompositionChart'), {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_map(function($m){ return date('M Y', strtotime($m . '-01')); }, $months)) ?>,
          datasets: <?= json_encode($datasets) ?>
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { stacked: true, grid: { display: false } },
            y: { 
              stacked: true, 
              beginAtZero: true, 
              max: 100,
              ticks: { callback: value => value + '%' }
            }
          },
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10, weight: 700 } } },
            tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.raw}%` } }
          }
        }
      });
    });
  </script>
</body>
</html>
