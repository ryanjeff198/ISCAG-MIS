<?php $active_page = 'analytics'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>ISCAG MIS — Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Ultra-wide Analytics Layout */
    .an { max-width: 2400px; margin: 0 auto; width: 98%; padding-bottom: 50px; }
    
    .an-section { margin-bottom: 32px; }
    .an-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .an-section-title { font-size: 0.95rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.08em; }
    .an-section-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }

    /* KPI Grid Scale */
    /* KPI Grid - Unified Scale */
    .admin-insights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .kpi-trend {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-top: 4px;
    }
    .kpi-trend.up { color: var(--success); }
    .kpi-trend.flat { color: var(--text-muted); }

    /* Charts Scaling */
    .chart-row { display: grid; gap: 20px; }
    .chart-row.two { grid-template-columns: 3fr 1.2fr; }
    .chart-row.three { grid-template-columns: 1fr 1fr 1fr; }
    .chart-row.equal { grid-template-columns: 1fr 1fr; }
    
    @media (max-width: 1400px) {
        .chart-row.two { grid-template-columns: 2fr 1fr; }
        .chart-row.three { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 1180px) {
        .chart-row.two, .chart-row.three, .chart-row.equal { grid-template-columns: 1fr; }
    }

    .card { 
        background: var(--card-bg); 
        border: 1px solid var(--border); 
        border-radius: 16px; 
        padding: 28px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
        transition: all 0.3s ease;
    }
    .card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .card-title { font-size: 1.05rem; font-weight: 700; color: var(--text-main); }
    .card-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 5px; }
    .card-badge { font-size: 0.7rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; background: rgba(23, 107, 69, 0.05); color: var(--primary); }

    /* Data Table Scale */
    .dt { width: 100%; border-collapse: collapse; }
    .dt th { background: var(--content-bg); color: var(--text-muted); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 14px 20px; text-align: left; border-bottom: 2px solid var(--border); cursor: pointer; user-select: none; }
    .dt th:hover { color: var(--primary); }
    .dt td { padding: 16px 20px; border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--text-main); font-weight: 500; }
    .dt tr:last-child td { border-bottom: none; }
    .dt tr:hover { background: rgba(47, 138, 96, 0.02); }
    .dt-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; }
    .dt-tag { font-size: 0.7rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
    .dt-tag.active { background: rgba(47, 138, 96, 0.08); color: var(--success); }

    .dt-search { border: 1px solid var(--border); border-radius: 10px; padding: 10px 18px; font-size: 0.9rem; width: 100%; max-width: 320px; outline: none; font-family: inherit; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .dt-search:focus { border-color: var(--primary); box-shadow: 0 4px 12px rgba(23,107,69,0.1); max-width: 380px; }

    .btn-export { background: var(--primary-dark); color: #fff; border: none; padding: 10px 22px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(23,107,69,0.2); }
    .btn-export:hover { transform: translateY(-2px); background: var(--primary); box-shadow: 0 6px 20px rgba(23,107,69,0.3); }
  </style>
</head>
<body>
<div class="app-wrapper">
  <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>
  <main class="main-content">
    <div class="top-bar">
      <div class="top-bar-left">
        <div>
          <div class="top-bar-title">Analytics</div>
          <div class="top-bar-subtitle">Comprehensive data insights across all ISCAG MIS modules</div>
        </div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        <button class="btn-export" onclick="exportCSV()">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
          Export CSV
        </button>
      </div>
    </div>
    <div class="page-body">
      <div class="an">

        <!-- ═══ SECTION 1: DEPARTMENT & USER KPI OVERVIEW ═══ -->
        <div class="an-section">
          <div class="admin-insights">
            <div class="insight-card">
              <div class="insight-label">Registered Users</div>
              <div class="insight-value" style="color:var(--primary)"><?= number_format($totalUsers ?? 0) ?></div>
              <div class="kpi-trend up"><?= number_format($activeUsers ?? 0) ?> verified · <?= number_format($staffUsers ?? 0) ?> staff</div>
              <div class="insight-icon-bg" style="color:var(--primary)"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Apartments Dept.</div>
              <div class="insight-value" style="color:#1e40af"><?= number_format($deptMetrics['apartments']['total'] ?? 0) ?></div>
              <div class="kpi-trend flat"><?= number_format($deptMetrics['apartments']['occupancy'] ?? 0) ?>/<?= number_format($deptMetrics['apartments']['units'] ?? 0) ?> units · ₱<?= number_format($totalRevenue ?? 0, 0) ?> paid</div>
              <div class="insight-icon-bg" style="color:#1e40af"><svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zM7 19H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm4 4H9v-2h2v2zm0-4H9V9h2v2zm0-4H9V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 12h-2v-2h2v2zm0-4h-2v-2h2v2z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Da'wah Dept.</div>
              <div class="insight-value" style="color:var(--success)"><?= number_format($deptMetrics['dawah']['total'] ?? 0) ?></div>
              <div class="kpi-trend up"><?= number_format($deptMetrics['dawah']['pending'] ?? 0) ?> pending · <?= number_format($deptMetrics['dawah']['approved'] ?? 0) ?> active</div>
              <div class="insight-icon-bg" style="color:var(--success)"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Islamic Education</div>
              <div class="insight-value" style="color:#065f46"><?= number_format($deptMetrics['education']['students'] ?? 0) ?></div>
              <div class="kpi-trend up"><?= number_format($deptMetrics['education']['avg_progress'] ?? 0, 1) ?>% avg · <?= number_format($deptMetrics['education']['sessions'] ?? 0) ?>/<?= number_format($deptMetrics['education']['sessions_total'] ?? 0) ?> sessions</div>
              <div class="insight-icon-bg" style="color:#065f46"><svg viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Damayan Dept.</div>
              <div class="insight-value" style="color:#4338ca"><?= number_format($deptMetrics['damayan']['total'] ?? 0) ?></div>
              <div class="kpi-trend flat"><?= number_format($deptMetrics['damayan']['pending'] ?? 0) ?> pending · ₱<?= number_format($deptMetrics['damayan']['donations_amount'] ?? 0, 0) ?> verified charity</div>
              <div class="insight-icon-bg" style="color:#4338ca"><svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
            </div>
          </div>
        </div>

        <!-- ═══ SECTION 2: ANALYTICS & TRENDS ═══ -->
        <div class="an-section">
          <div class="an-section-head">
            <div><div class="an-section-title">Department & User Trends</div><div class="an-section-sub">Real-time operational distribution across all departments and registered users</div></div>
          </div>

          <!-- Row A: Department Requests Comparison + Module Distribution -->
          <div class="chart-row two" style="margin-bottom:16px">
            <div class="card">
              <div class="card-head"><div><div class="card-title">Requests by Department & Status</div><div class="card-sub">Pending, active/approved, and completed across all 4 departments</div></div><span class="card-badge"><?= number_format($grandTotalRequests ?? 0) ?> Total</span></div>
              <div style="position:relative;height:250px"><canvas id="deptStatusChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Module Distribution</div><div class="card-sub">Share across departments</div></div></div>
              <div style="position:relative;height:250px;display:flex;justify-content:center"><canvas id="moduleChart"></canvas></div>
            </div>
          </div>

          <!-- Row B: Service Breakdown per Department -->
          <div class="chart-row three" style="margin-bottom:16px">
            <div class="card">
              <div class="card-head"><div><div class="card-title">Da'wah Services</div><div class="card-sub">Counseling, marriage & conversion</div></div></div>
              <div style="position:relative;height:150px"><canvas id="dawahSvcChart"></canvas></div>
              <div style="margin-top:16px">
                <?php
                  $dawahRows = [
                    ['label' => 'Counseling', 'count' => $deptMetrics['dawah']['counseling'] ?? 0, 'color' => 'var(--primary)'],
                    ['label' => 'Marriage', 'count' => $deptMetrics['dawah']['marriage'] ?? 0, 'color' => 'var(--warning)'],
                    ['label' => 'Conversion', 'count' => $deptMetrics['dawah']['conversion'] ?? 0, 'color' => 'var(--success)'],
                  ];
                  $dawahSum = array_sum(array_column($dawahRows, 'count'));
                  foreach ($dawahRows as $dr):
                    $pct = $dawahSum > 0 ? round(($dr['count'] / $dawahSum) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?= $dr['color'] ?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?= $dr['label'] ?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?= number_format($dr['count']) ?> (<?= $pct ?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="card">
              <div class="card-head"><div><div class="card-title">Damayan Services</div><div class="card-sub">Burial &amp; funeral case volume (<?= number_format($deptMetrics['damayan']['total'] ?? 0) ?> cases)</div></div></div>
              <div style="position:relative;height:150px;display:flex;justify-content:center"><canvas id="damayanSvcChart"></canvas></div>
              <div style="margin-top:16px">
                <?php
                  $damayanRows = [
                    ['label' => 'Burial Requests', 'count' => $deptMetrics['damayan']['burial'] ?? 0, 'color' => '#6366f1'],
                    ['label' => 'Funeral Cases', 'count' => $deptMetrics['damayan']['funeral'] ?? 0, 'color' => 'var(--warning)'],
                  ];
                  $damayanSum = array_sum(array_column($damayanRows, 'count'));
                  foreach ($damayanRows as $dm):
                    $pct = $damayanSum > 0 ? round(($dm['count'] / $damayanSum) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?= $dm['color'] ?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?= $dm['label'] ?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?= number_format($dm['count']) ?> (<?= $pct ?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
                <div style="margin-top:10px;padding-top:8px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:0.72rem;color:var(--text-muted);">
                  <span>Verified Charity (<?= (int)($deptMetrics['damayan']['donations_verified_count'] ?? 0) ?>): <strong style="color:var(--success);">₱<?= number_format($deptMetrics['damayan']['donations_amount'] ?? 0, 0) ?></strong></span>
                  <span>Pending (<?= (int)($deptMetrics['damayan']['donations_pending_count'] ?? 0) ?>): <strong style="color:var(--warning);">₱<?= number_format($deptMetrics['damayan']['donations_pending_amount'] ?? 0, 0) ?></strong></span>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-head"><div><div class="card-title">Apartments Overview</div><div class="card-sub">Applications, maintenance &amp; parking (<?= number_format($deptMetrics['apartments']['total'] ?? 0) ?> records)</div></div></div>
              <div style="position:relative;height:150px"><canvas id="aptSvcChart"></canvas></div>
              <div style="margin-top:16px">
                <?php
                  $aptRows = [
                    ['label' => 'Applications', 'count' => $deptMetrics['apartments']['applications'] ?? 0, 'color' => 'var(--primary)'],
                    ['label' => 'Maintenance', 'count' => $deptMetrics['apartments']['maintenance'] ?? 0, 'color' => 'var(--warning)'],
                    ['label' => 'Parking Permits', 'count' => $deptMetrics['apartments']['parking'] ?? 0, 'color' => 'var(--info)'],
                  ];
                  $aptSum = max($deptMetrics['apartments']['total'] ?? 0, array_sum(array_column($aptRows, 'count')));
                  foreach ($aptRows as $ar):
                    $pct = $aptSum > 0 ? round(($ar['count'] / $aptSum) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?= $ar['color'] ?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?= $ar['label'] ?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?= number_format($ar['count']) ?> (<?= $pct ?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <!-- Row C: User Analytics Across the Entire System -->
          <div class="chart-row three">
            <div class="card">
              <div class="card-head"><div><div class="card-title">Account Verification</div><div class="card-sub">Verified vs unverified users</div></div></div>
              <div style="position:relative;height:140px;display:flex;justify-content:center"><canvas id="verifyChart"></canvas></div>
              <div style="margin-top:16px">
                <?php 
                  $totalVer = array_sum(array_column($statusDist, 'count'));
                  foreach($statusDist as $d):
                    $vc = stripos($d['status'],'unverif')===false?'var(--success)':'var(--danger)';
                    $pct = $totalVer > 0 ? round(($d['count'] / $totalVer) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?=$vc?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?=$d['status']?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?=$d['count']?> (<?=$pct?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="card">
              <div class="card-head"><div><div class="card-title">User Role Distribution</div><div class="card-sub">Tenants, guests & staff accounts</div></div></div>
              <div style="position:relative;height:140px;display:flex;justify-content:center"><canvas id="rolesChart"></canvas></div>
              <div style="margin-top:16px">
                <?php
                  $roleColors = ['Residents / Tenants' => 'var(--primary)', 'Guests' => 'var(--warning)', 'Staff & Admins' => 'var(--info)'];
                  foreach (($userRoleBreakdown ?? []) as $rName => $rCount):
                    $rc = $roleColors[$rName] ?? '#94a3b8';
                    $pct = ($totalUsers ?? 0) > 0 ? round(($rCount / $totalUsers) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?= $rc ?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?= htmlspecialchars($rName) ?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?= number_format($rCount) ?> (<?= $pct ?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="card">
              <div class="card-head"><div><div class="card-title">User Gender Demographics</div><div class="card-sub">Across all registered accounts</div></div></div>
              <div style="position:relative;height:140px;display:flex;justify-content:center"><canvas id="genderChart"></canvas></div>
              <div style="margin-top:16px">
                <?php 
                  $totalDem = array_sum(array_column($genderDist, 'count'));
                  foreach($genderDist as $d):
                    $gc = ($d['gender'] === 'Male') ? 'var(--info)' : (($d['gender'] === 'Female') ? 'var(--warning)' : '#94a3b8');
                    $pct = $totalDem > 0 ? round(($d['count'] / $totalDem) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:8px;">
                  <div class="bl-dot" style="background:<?=$gc?>"></div>
                  <div style="flex:1;"><div style="display:flex;justify-content:space-between;align-items:center;"><div class="bl-label" style="font-size:0.8rem;"><?=$d['gender'] ?: 'Unspecified'?></div><div style="font-size:0.7rem;font-weight:700;color:var(--text-muted);"><?=$d['count']?> (<?=$pct?>%)</div></div></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ SECTION 3: DATA TABLE ═══ -->
        <div class="an-section">
          <div class="an-section-head">
            <div><div class="an-section-title">Department Operations Breakdown</div><div class="an-section-sub">Sortable status summary across all 4 ISCAG departments (<?= number_format($grandTotalRequests ?? 0) ?> total records)</div></div>
            <input type="text" class="dt-search" id="dtSearch" placeholder="Search departments..." oninput="filterTable()"/>
          </div>
          <div class="card" style="padding:0;overflow:hidden;border-radius:12px">
            <div class="table-wrapper">
              <table class="dt" id="dataTable">
                <thead>
                  <tr>
                    <th onclick="sortTable(0)">Department ↕</th>
                    <th onclick="sortTable(1)">Total ↕</th>
                    <th onclick="sortTable(2)">Pending ↕</th>
                    <th onclick="sortTable(3)">Approved / Active ↕</th>
                    <th onclick="sortTable(4)">Completed ↕</th>
                    <th onclick="sortTable(5)">Rejected ↕</th>
                    <th onclick="sortTable(6)">Share ↕</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $deptSummaryRows = [
                      [$deptMetrics['apartments']['name'], $deptMetrics['apartments'], 'var(--primary)'],
                      [$deptMetrics['dawah']['name'], $deptMetrics['dawah'], 'var(--success)'],
                      [$deptMetrics['education']['name'], $deptMetrics['education'], '#1f6f5a'],
                      [$deptMetrics['damayan']['name'], $deptMetrics['damayan'], '#6366f1'],
                    ];
                    $reqDenom = max((int)($grandTotalRequests ?? 0), 1);
                    foreach ($deptSummaryRows as [$dLabel, $dmData, $dColor]):
                      $dTot = (int)($dmData['total'] ?? 0);
                      $rowPct = ($grandTotalRequests ?? 0) > 0 ? round(($dTot / $reqDenom) * 100, 1) : 0;
                  ?>
                  <tr>
                    <td><span class="dt-dot" style="background:<?= $dColor ?>"></span><?= htmlspecialchars($dLabel) ?></td>
                    <td style="font-weight:700;font-family:'Lora',serif;font-size:1rem"><?= number_format($dTot) ?></td>
                    <td style="color:var(--warning);font-weight:600;"><?= number_format($dmData['pending'] ?? 0) ?></td>
                    <td style="color:var(--success);font-weight:600;"><?= number_format($dmData['approved'] ?? 0) ?></td>
                    <td style="color:#6366f1;font-weight:600;"><?= number_format($dmData['completed'] ?? 0) ?></td>
                    <td style="color:var(--danger);font-weight:600;"><?= number_format($dmData['rejected'] ?? 0) ?></td>
                    <td style="font-weight:600;color:var(--text-muted)"><?= $rowPct ?>%</td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr style="background:var(--content-bg);font-weight:700;">
                    <td>All 4 Departments Total</td>
                    <td style="font-family:'Lora',serif;font-size:1rem"><?= number_format($grandTotalRequests ?? 0) ?></td>
                    <td style="color:var(--warning);"><?= number_format($grandPendingRequests ?? 0) ?></td>
                    <td style="color:var(--success);"><?= number_format($grandApprovedRequests ?? 0) ?></td>
                    <td style="color:#6366f1;"><?= number_format($grandCompletedRequests ?? 0) ?></td>
                    <td style="color:var(--danger);"><?= number_format($grandRejectedRequests ?? 0) ?></td>
                    <td><?= ($grandTotalRequests ?? 0) > 0 ? '100.0%' : '0%' ?></td>
                  </tr>
                </tfoot>
              </table>
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
document.addEventListener("DOMContentLoaded",function(){
  Chart.defaults.font.family="'Source Sans 3',sans-serif";
  Chart.defaults.color='#6f7f78';
  const P='#176b45',S='#2f8a60',W='#c79a2b',D='#8b2e2e',I='#1f6f5a',C='#6366f1';
  const sc=s=>{const l=(s||'').toLowerCase();if(l.includes('unverif'))return D;if(l.includes('verif'))return S;return'#94a3b8'};
  const noLegend={display:false};
  const cleanX={grid:{display:false}};
  const cleanY={beginAtZero:true,grid:{color:'#e8ece9',drawBorder:false}};

  // 1. Department Requests Breakdown — Grouped Bar
  new Chart(document.getElementById('deptStatusChart'),{
    type:'bar',
    data:{
      labels:['Apartments',"Da'wah",'Islamic Education','Damayan'],
      datasets:[
        {label:'Pending',data:<?=json_encode([
          $deptMetrics['apartments']['pending']??0,
          $deptMetrics['dawah']['pending']??0,
          $deptMetrics['education']['pending']??0,
          $deptMetrics['damayan']['pending']??0,
        ])?>,backgroundColor:W,borderRadius:4},
        {label:'Approved / Active',data:<?=json_encode([
          $deptMetrics['apartments']['approved']??0,
          $deptMetrics['dawah']['approved']??0,
          $deptMetrics['education']['approved']??0,
          $deptMetrics['damayan']['approved']??0,
        ])?>,backgroundColor:S,borderRadius:4},
        {label:'Completed',data:<?=json_encode([
          $deptMetrics['apartments']['completed']??0,
          $deptMetrics['dawah']['completed']??0,
          $deptMetrics['education']['completed']??0,
          $deptMetrics['damayan']['completed']??0,
        ])?>,backgroundColor:C,borderRadius:4},
        {label:'Rejected',data:<?=json_encode([
          $deptMetrics['apartments']['rejected']??0,
          $deptMetrics['dawah']['rejected']??0,
          $deptMetrics['education']['rejected']??0,
          $deptMetrics['damayan']['rejected']??0,
        ])?>,backgroundColor:D,borderRadius:4}
      ]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}},scales:{x:cleanX,y:{...cleanY,ticks:{stepSize:1}}}}
  });

  // 2. Module Distribution — Polar Area (All 4 Departments)
  const mR=<?=json_encode($moduleDist??[])?>;
  new Chart(document.getElementById('moduleChart'),{type:'polarArea',data:{labels:mR.map(d=>d.module),datasets:[{data:mR.map(d=>d.count),backgroundColor:[P+'bb',S+'bb',I+'bb',C+'bb'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}},scales:{r:{ticks:{display:false},grid:{color:'#e8ece9'}}}}});

  // 3. Da'wah Services — Horizontal Bar
  new Chart(document.getElementById('dawahSvcChart'),{type:'bar',data:{labels:['Counseling','Marriage','Conversion'],datasets:[{label:'Requests',data:[<?=(int)($deptMetrics['dawah']['counseling']??0)?>,<?=(int)($deptMetrics['dawah']['marriage']??0)?>,<?=(int)($deptMetrics['dawah']['conversion']??0)?>],backgroundColor:[P,W,S],borderRadius:6,barThickness:22}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:{...cleanY,ticks:{stepSize:1}},y:cleanX}}});

  // 4. Damayan Services — Doughnut (Burial & Funeral Cases)
  new Chart(document.getElementById('damayanSvcChart'),{type:'doughnut',data:{labels:['Burial Requests','Funeral Cases'],datasets:[{data:[<?=(int)($deptMetrics['damayan']['burial']??0)?>,<?=(int)($deptMetrics['damayan']['funeral']??0)?>],backgroundColor:[C,W],borderWidth:0,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}}}});

  // 5. Apartments Overview — Vertical Bar
  new Chart(document.getElementById('aptSvcChart'),{type:'bar',data:{labels:['Applications','Maintenance','Parking'],datasets:[{label:'Records',data:[<?=(int)($deptMetrics['apartments']['applications']??0)?>,<?=(int)($deptMetrics['apartments']['maintenance']??0)?>,<?=(int)($deptMetrics['apartments']['parking']??0)?>],backgroundColor:[P,W,I],borderRadius:6,barThickness:30}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:cleanX,y:{...cleanY,ticks:{stepSize:1}}}}});

  // 6. Account Verification — Polar Area
  const vrR=<?=json_encode($statusDist??[])?>;
  new Chart(document.getElementById('verifyChart'),{type:'polarArea',data:{labels:vrR.map(d=>d.status),datasets:[{data:vrR.map(d=>+d.count),backgroundColor:vrR.map(d=>sc(d.status)+'bb'),borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}},scales:{r:{ticks:{display:false},grid:{color:'#e8ece9'}}}}});

  // 7. User Roles — Pie (color mapped by role name)
  const rlLabels=<?=json_encode(array_keys($userRoleBreakdown??[]))?>;
  const rlData=<?=json_encode(array_values($userRoleBreakdown??[]))?>;
  const roleColorMap={'Residents / Tenants':P,'Guests':W,'Staff & Admins':I};
  new Chart(document.getElementById('rolesChart'),{type:'pie',data:{labels:rlLabels,datasets:[{data:rlData,backgroundColor:rlLabels.map(l=>roleColorMap[l]||'#94a3b8'),borderWidth:2,borderColor:'#fff',hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}}}});

  // 8. User Gender Demographics — Doughnut (color mapped by gender label)
  const gnR=<?=json_encode($genderDist??[])?>;
  const genderColorMap={'Male':I,'Female':W,'Unspecified':'#94a3b8'};
  new Chart(document.getElementById('genderChart'),{type:'doughnut',data:{labels:gnR.map(d=>d.gender||'Unspecified'),datasets:[{data:gnR.map(d=>+d.count),backgroundColor:gnR.map(d=>genderColorMap[d.gender||'Unspecified']||'#94a3b8'),borderWidth:0,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}}}});
});

// Table sort
let sortDir=1;
function sortTable(col){
  const tb=document.querySelector('#dataTable tbody');
  const rows=[...tb.querySelectorAll('tr')];
  rows.sort((a,b)=>{
    const av=a.cells[col].textContent.trim(),bv=b.cells[col].textContent.trim();
    const an=parseFloat(av.replace(/[,%]/g,'')),bn=parseFloat(bv.replace(/[,%]/g,''));
    const bothNum=!isNaN(an)&&!isNaN(bn)&&/^[\d,.\s%-]+$/.test(av)&&/^[\d,.\s%-]+$/.test(bv);
    return(bothNum?(an-bn):av.localeCompare(bv))*sortDir;
  });
  sortDir*=-1;
  rows.forEach(r=>tb.appendChild(r));
}
function filterTable(){
  const q=document.getElementById('dtSearch').value.toLowerCase();
  document.querySelectorAll('#dataTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
}
function exportCSV(){
  const t=document.getElementById('dataTable');
  let csv=[];
  t.querySelectorAll('tr').forEach(r=>{
    const cols=r.querySelectorAll('td,th');
    csv.push(Array.from(cols).map(c=>'"'+c.innerText.replace(/"/g,'""')+'"').join(','));
  });
  const a=document.createElement('a');
  a.download='ISCAG_Analytics_'+new Date().toISOString().split('T')[0]+'.csv';
  a.href=URL.createObjectURL(new Blob([csv.join('\n')],{type:'text/csv'}));
  a.click();
}
</script>
</body>
</html>
