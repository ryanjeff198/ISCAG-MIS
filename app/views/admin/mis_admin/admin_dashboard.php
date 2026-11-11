<?php $active_page = 'admin_dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>ISCAG MIS — Admin Dashboard</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= asset('css/notifications.css') ?>?v=<?= time() ?>" />
  <!-- Chart.js Integration -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Responsive Scale Improvements */
    .db { max-width: 2400px; margin: 0 auto; width: 99%; padding-bottom: 40px; }
    
    .db-section { margin-bottom: 24px; }
    .db-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .db-section-title { font-size: 0.95rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.08em; }
    .db-section-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

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
    .kpi-trend.down { color: var(--danger); }
    .kpi-trend.flat { color: var(--text-muted); }

    /* Tables & Charts Scale */
    .chart-row { display: grid; gap: 20px; }
    .chart-row.two { grid-template-columns: 3fr 1.5fr; }
    .chart-row.three { grid-template-columns: 1fr 1fr 1fr; }
    .chart-row.four { grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
    .chart-row.equal { grid-template-columns: 1fr 1fr; }
    
    @media (max-width: 1400px) {
        .chart-row.two { grid-template-columns: 2fr 1fr; }
        .chart-row.three, .chart-row.four { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 1180px) {
        .chart-row.two, .chart-row.three, .chart-row.equal, .chart-row.four { grid-template-columns: 1fr; }
        .kpi-row { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
    }

    .card { 
        background: var(--card-bg); 
        border: 1px solid var(--border); 
        border-radius: 16px; 
        padding: 24px; 
        height: 100%; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
        transition: all 0.3s ease;
    }
    .card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .card-title { font-size: 1.05rem; font-weight: 700; color: var(--text-main); }
    .card-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
    .card-badge { font-size: 0.7rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; background: rgba(23, 107, 69, 0.05); color: var(--primary); text-decoration: none; }

    /* Department Cards */
    .dept-card {
        border-top: 4px solid var(--primary);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .dept-card.dawah { border-top-color: #2f8a60; }
    .dept-card.education { border-top-color: #1f6f5a; }
    .dept-card.damayan { border-top-color: #6366f1; }
    .dept-card.apartments { border-top-color: #176b45; }

    .dept-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        font-size: 0.82rem;
    }
    .dept-stat-row:last-child { border-bottom: none; }
    .dept-stat-label { color: var(--text-muted); font-weight: 500; }
    .dept-stat-val { font-weight: 700; color: var(--text-main); }

    .dept-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .dept-btn {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        background: #f0fdf4;
        color: var(--primary);
        border: 1px solid rgba(23, 107, 69, 0.2);
        text-decoration: none;
        transition: all 0.2s;
    }
    .dept-btn:hover {
        background: var(--primary);
        color: #fff;
    }

    /* Department Badges */
    .badge-dept {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .badge-dept-apartments { background: #e0f2fe; color: #0369a1; }
    .badge-dept-dawah { background: #ecfdf5; color: #047857; }
    .badge-dept-education { background: #fef3c7; color: #b45309; }
    .badge-dept-damayan { background: #ede9fe; color: #6d28d9; }

    /* Urgent Actions Banner */
    .urgent-banner {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .urgent-title {
        font-weight: 700;
        font-size: 0.88rem;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .urgent-items {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .urgent-tag {
        background: #fff;
        border: 1px solid #fcd34d;
        color: #78350f;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 16px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .urgent-tag:hover {
        background: #fef3c7;
        transform: translateY(-1px);
    }

    /* Activity feed */
    .feed-item { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--border); }
    .feed-item:last-child { border-bottom: none; }
    .feed-av { width: 40px; height: 40px; border-radius: 50%; background: rgba(47, 138, 96, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; }
    .feed-body { flex: 1; min-width: 0; }
    .feed-text { font-size: 0.88rem; font-weight: 500; color: var(--text-main); white-space: nowrap; overflow:hidden; text-overflow: ellipsis; }
    .feed-time { font-size: 0.75rem; color: var(--text-muted); margin-top: 3px; }
    .feed-tag { font-size: 0.68rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; flex-shrink: 0; }
    
    /* Module bars */
    .mod-item { margin-bottom: 20px; } .mod-item:last-child { margin-bottom: 0; }
    .mod-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
    .mod-name { font-size: 0.9rem; font-weight: 600; color: var(--text-main); }
    .mod-val { font-size: 0.85rem; font-weight: 700; }
    .mod-bar { height: 8px; background: #f0f4f1; border-radius: 4px; overflow: hidden; }
    .mod-fill { height: 100%; border-radius: 4px; transition: width 0.8s ease; }

    /* Billing legend */
    .bl-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .bl-row:last-child { margin-bottom: 0; }
    .bl-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
    .bl-label { font-size: 0.9rem; font-weight: 600; color: var(--text-main); }
    .bl-sub { font-size: 0.78rem; color: var(--text-muted); }
  </style>
</head>
<body>
<div class="app-wrapper">
  <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>
  <main class="main-content">
    <div class="top-bar">
      <div class="top-bar-left">
        <div>
          <div class="top-bar-title">MIS Central Operations Dashboard</div>
          <div class="top-bar-subtitle">Welcome back, <?= htmlspecialchars(explode(' ',trim($_SESSION['name']??'Admin'))[0]) ?>. Centralized monitoring across all 4 departments.</div>
        </div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/admin/mis_admin/reports') ?>" class="btn-topbar">📑 Reports & Exports</a>
        <a href="<?= url('/admin/analytics') ?>" class="btn-topbar">📊 Analytics</a>
      </div>
    </div>
    <div class="page-body">
      <div class="db">

        <!-- ═══════ URGENT ACTIONS & ALERTS BANNER ═══════ -->
        <?php if (!empty($pendingActions)): ?>
        <div class="urgent-banner">
          <div class="urgent-title">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#b45309;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            Pending Actions Requiring Attention (<?= count($pendingActions) ?> items)
          </div>
          <div class="urgent-items">
            <?php foreach ($pendingActions as $pa): ?>
            <a href="<?= $pa['url'] ?>" class="urgent-tag">
              <strong><?= $pa['count'] ?></strong> <?= htmlspecialchars($pa['title']) ?> →
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- ═══════ SECTION 1: SYSTEM SUMMARY CARDS ═══════ -->
        <div class="db-section">
          <div class="admin-insights">
            <?php
              $kpis = [
                [
                  'label' => 'Total Requests',
                  'val' => number_format($grandTotalRequests ?? 0),
                  'trend' => 'up',
                  'subtext' => 'Across 4 Departments',
                  'color' => 'var(--primary)',
                  'icon' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
                  'url' => '#unified-requests'
                ],
                [
                  'label' => 'Pending Requests',
                  'val' => number_format($grandPendingRequests ?? 0),
                  'trend' => ($grandPendingRequests > 0 ? 'down' : 'up'),
                  'subtext' => ($grandPendingRequests > 0 ? $grandPendingRequests . ' awaiting review' : 'All clear'),
                  'color' => 'var(--warning)',
                  'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z',
                  'url' => '#unified-requests'
                ],
                [
                  'label' => 'Approved Requests',
                  'val' => number_format($grandApprovedRequests ?? 0),
                  'trend' => 'up',
                  'subtext' => 'Confirmed & In Process',
                  'color' => 'var(--success)',
                  'icon' => 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
                  'url' => '#unified-requests'
                ],
                [
                  'label' => 'Completed Requests',
                  'val' => number_format($grandCompletedRequests ?? 0),
                  'trend' => 'up',
                  'subtext' => 'Resolved & Released',
                  'color' => '#6366f1',
                  'icon' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                  'url' => url('/admin/mis_admin/reports')
                ],
                [
                  'label' => 'Registered Users',
                  'val' => number_format($totalUsers ?? 0),
                  'trend' => 'up',
                  'subtext' => ($activeUsers ?? 0) . ' Verified (' . ($staffUsers ?? 0) . ' Staff)',
                  'color' => 'var(--info)',
                  'icon' => 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z',
                  'url' => url('/admin/mis_admin/records')
                ]
              ];
              foreach($kpis as $k):
            ?>
            <a href="<?=$k['url']?>" class="insight-card" style="text-decoration:none;color:inherit;">
              <div class="insight-label"><?=$k['label']?></div>
              <div class="insight-value" style="color:<?=$k['color']?>"><?=$k['val']?></div>
              <div class="kpi-trend <?=$k['trend']?>">
                <span class="trend-icon"><?=$k['trend']==='up'?'↑':($k['trend']==='down'?'↓':'—')?></span>
                <span style="font-size:0.7rem;"><?=$k['subtext']?></span>
              </div>
              <div class="insight-icon-bg" style="color:<?=$k['color']?>"><svg viewBox="0 0 24 24"><path d="<?=$k['icon']?>"/></svg></div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ═══════ SECTION 2: DEPARTMENT OVERVIEW (Central MIS Layer) ═══════ -->
        <div class="db-section">
          <div class="db-section-head">
            <div>
              <div class="db-section-title">Department Integration Overview</div>
              <div class="db-section-sub">Real-time status across Da'wah, Islamic Education, Damayan, and Apartments</div>
            </div>
            <a href="<?= url('/admin/mis_admin/reports') ?>" style="font-size:.75rem;color:var(--primary);font-weight:600;text-decoration:none">Detailed Department Reports →</a>
          </div>
          <div class="chart-row four">

            <!-- 1. APARTMENTS DEPARTMENT -->
            <div class="card dept-card apartments">
              <div>
                <div class="card-head">
                  <div>
                    <div class="card-title">Apartments</div>
                    <div class="card-sub">Housing & Facilities</div>
                  </div>
                  <span class="badge-dept badge-dept-apartments">Active</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Applications</span>
                  <span class="dept-stat-val"><?= $deptMetrics['apartments']['total'] ?> total (<?= $deptMetrics['apartments']['pending'] ?> pending)</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Unit Occupancy</span>
                  <span class="dept-stat-val"><?= $deptMetrics['apartments']['occupancy'] ?> / <?= $deptMetrics['apartments']['units'] ?> occupied</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Maintenance</span>
                  <span class="dept-stat-val"><?= $pendingMaintenance ?> pending</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Collected Revenue</span>
                  <span class="dept-stat-val" style="color:var(--success);">₱<?= number_format($totalRevenue, 2) ?></span>
                </div>
              </div>
              <div class="dept-actions">
                <a href="<?= url('/admin/mis_admin/apartment_confirmation') ?>" class="dept-btn">Applications</a>
                <a href="<?= url('/admin/mis_admin/apartment_records') ?>" class="dept-btn">Units</a>
                <a href="<?= url('/admin/mis_admin/maintenance') ?>" class="dept-btn">Maintenance</a>
              </div>
            </div>

            <!-- 2. DA'WAH DEPARTMENT -->
            <div class="card dept-card dawah">
              <div>
                <div class="card-head">
                  <div>
                    <div class="card-title">Da'wah Department</div>
                    <div class="card-sub">Counseling & Guidance</div>
                  </div>
                  <span class="badge-dept badge-dept-dawah">Active</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Total Requests</span>
                  <span class="dept-stat-val"><?= $deptMetrics['dawah']['total'] ?> active</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Counseling Sessions</span>
                  <span class="dept-stat-val"><?= $deptMetrics['dawah']['counseling'] ?> consultations</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Marriage Ceremonies</span>
                  <span class="dept-stat-val"><?= $deptMetrics['dawah']['marriage'] ?> solemnized</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Shahadah Conversions</span>
                  <span class="dept-stat-val"><?= $deptMetrics['dawah']['conversion'] ?> completed</span>
                </div>
              </div>
              <div class="dept-actions">
                <a href="<?= url('/admin/dawah/counseling') ?>" class="dept-btn">Counseling</a>
                <a href="<?= url('/admin/dawah/marriage') ?>" class="dept-btn">Marriage</a>
                <a href="<?= url('/admin/dawah/conversion') ?>" class="dept-btn">Conversions</a>
              </div>
            </div>

            <!-- 3. ISLAMIC EDUCATION DEPARTMENT -->
            <div class="card dept-card education">
              <div>
                <div class="card-head">
                  <div>
                    <div class="card-title">Islamic Education</div>
                    <div class="card-sub">Student Learning & Progress</div>
                  </div>
                  <span class="badge-dept badge-dept-education">Curriculum</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Registered Students</span>
                  <span class="dept-stat-val"><?= $deptMetrics['education']['students'] ?> students</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Sessions Attended</span>
                  <span class="dept-stat-val"><?= $deptMetrics['education']['sessions'] ?> completed sessions</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Avg. Student Progress</span>
                  <span class="dept-stat-val" style="color:var(--primary);"><?= $deptMetrics['education']['avg_progress'] ?>%</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Status</span>
                  <span class="dept-stat-val" style="color:var(--success);"><?= $deptMetrics['education']['approved'] ?> active learners</span>
                </div>
              </div>
              <div class="dept-actions">
                <a href="<?= url('/admin/dawah/education') ?>" class="dept-btn">Education Portal</a>
                <a href="<?= url('/admin/mis_admin/dawah_records') ?>" class="dept-btn">Da'wah Records</a>
              </div>
            </div>

            <!-- 4. DAMAYAN DEPARTMENT -->
            <div class="card dept-card damayan">
              <div>
                <div class="card-head">
                  <div>
                    <div class="card-title">Damayan Department</div>
                    <div class="card-sub">Burial & Charity Services</div>
                  </div>
                  <span class="badge-dept badge-dept-damayan">Active</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Burial Requests</span>
                  <span class="dept-stat-val"><?= $deptMetrics['damayan']['burial'] ?> registered</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Funeral Cases</span>
                  <span class="dept-stat-val"><?= $deptMetrics['damayan']['funeral'] ?> tracking</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Pending Reviews</span>
                  <span class="dept-stat-val" style="color:<?= $deptMetrics['damayan']['pending'] > 0 ? 'var(--danger)' : 'var(--text-muted)' ?>;"><?= $deptMetrics['damayan']['pending'] ?> pending</span>
                </div>
                <div class="dept-stat-row">
                  <span class="dept-stat-label">Charity Fund</span>
                  <span class="dept-stat-val">₱<?= number_format($deptMetrics['damayan']['donations_amount'] ?? 0, 2) ?></span>
                </div>
              </div>
              <div class="dept-actions">
                <a href="<?= url('/admin/mis_admin/damayan_records') ?>" class="dept-btn">Damayan Records</a>
                <a href="<?= url('/admin/mis_admin/reports') ?>" class="dept-btn">Reports</a>
              </div>
            </div>

          </div>
        </div>

        <!-- ═══════ SECTION 3: UNIFIED RECENT REQUESTS TABLE ═══════ -->
        <div class="db-section" id="unified-requests">
          <div class="db-section-head">
            <div>
              <div class="db-section-title">Recent Multi-Department Requests</div>
              <div class="db-section-sub">Latest submissions across Apartments, Da'wah, Islamic Education, and Damayan</div>
            </div>
            <a href="<?= url('/admin/mis_admin/reports') ?>" style="font-size:.75rem;color:var(--primary);font-weight:600;text-decoration:none">Generate Full Report →</a>
          </div>
          <div class="card" style="padding:0; overflow:hidden;">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Department</th>
                  <th>Request Type</th>
                  <th>Applicant / User</th>
                  <th>Date Submitted</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(empty($recentRequests)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted);">No recent requests found in the system.</td></tr>
                <?php else: ?>
                  <?php foreach($recentRequests as $req): 
                    $deptSlug = strtolower(explode(' ', trim($req['department']))[0]);
                    $badgeStyle = 'badge-dept-' . $deptSlug;
                    $statusClass = stripos($req['status'], 'pend') !== false ? 'badge-pending' : (stripos($req['status'], 'approv') !== false || stripos($req['status'], 'accept') !== false || stripos($req['status'], 'assign') !== false || stripos($req['status'], 'confirm') !== false ? 'badge-paid' : 'badge-overdue');
                  ?>
                  <tr>
                    <td class="td-id"><?= htmlspecialchars($req['ref_id']) ?></td>
                    <td><span class="badge-dept <?= $badgeStyle ?>"><?= htmlspecialchars($req['department']) ?></span></td>
                    <td style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($req['request_type']) ?></td>
                    <td>
                      <div style="font-weight:600;"><?= htmlspecialchars($req['applicant'] ?: 'N/A') ?></div>
                      <div style="font-size:0.75rem;color:var(--text-muted);"><?= htmlspecialchars($req['email'] ?: '') ?></div>
                    </td>
                    <td><?= date('M d, Y', strtotime($req['submitted_at'] ?? 'now')) ?></td>
                    <td><span class="badge-status <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($req['status'])) ?></span></td>
                    <td>
                      <a href="<?= url($req['action_url']) ?>" class="card-badge" style="text-decoration:none;">Review</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ═══════ SECTION 4: DEPARTMENT & USER ANALYTICS ═══════ -->
        <div class="db-section">
          <div class="chart-row two">
            <!-- Department Requests Comparison -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Requests by Department</div><div class="card-sub">Pending, active/approved, and completed records across all 4 departments</div></div>
                <span class="card-badge">All Departments</span>
              </div>
              <div style="position:relative;height:240px"><canvas id="deptBreakdownChart"></canvas></div>
            </div>
            <!-- Overall Request Status Across All Departments -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Overall Request Status</div><div class="card-sub">Combined across all departments</div></div>
                <span class="card-badge"><?= number_format($grandTotalRequests ?? 0) ?> total</span>
              </div>
              <div style="position:relative;height:160px;display:flex;justify-content:center"><canvas id="overallStatusChart"></canvas></div>
              <div style="margin-top:16px">
                <?php
                  $overallStatuses = [
                    ['label' => 'Pending Review', 'count' => $grandPendingRequests ?? 0, 'color' => 'var(--warning)'],
                    ['label' => 'Approved / Active', 'count' => $grandApprovedRequests ?? 0, 'color' => 'var(--success)'],
                    ['label' => 'Completed / Released', 'count' => $grandCompletedRequests ?? 0, 'color' => '#6366f1'],
                  ];
                  $overallSum = array_sum(array_column($overallStatuses, 'count'));
                  foreach ($overallStatuses as $os):
                    $pct = $overallSum > 0 ? round(($os['count'] / $overallSum) * 100, 1) : 0;
                ?>
                <div class="bl-row" style="margin-bottom:10px;">
                  <div class="bl-dot" style="background:<?= $os['color'] ?>"></div>
                  <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                      <div class="bl-label"><?= $os['label'] ?></div>
                      <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);"><?= number_format($os['count']) ?> (<?= $pct ?>%)</div>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="db-section">
          <div class="chart-row equal">
            <!-- Module Performance -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Department Module Share</div><div class="card-sub">Distribution of records across all 4 departments</div></div>
              </div>
              <?php
                $mods = [
                  ['Apartments', $deptMetrics['apartments']['total'], 'var(--primary)'],
                  ["Da'wah Services", $deptMetrics['dawah']['total'], '#2f8a60'],
                  ['Islamic Education', $deptMetrics['education']['total'], '#1f6f5a'],
                  ['Damayan Services', $deptMetrics['damayan']['total'], '#6366f1']
                ];
                $totalModCount = array_sum(array_column($mods, 1));
                foreach($mods as $m): 
                  $modPct = $totalModCount > 0 ? round(($m[1] / $totalModCount) * 100, 1) : 0;
              ?>
              <div class="mod-item">
                <div class="mod-head">
                  <span class="mod-name"><?=$m[0]?></span>
                  <span class="mod-val" style="color:<?=$m[2]?>"><?=number_format($m[1])?> <small style="font-size:0.65rem;opacity:0.7;margin-left:4px;">(<?=$modPct?>%)</small></span>
                </div>
                <div class="mod-bar"><div class="mod-fill" style="width:<?=min(100,($m[1]/max($totalModCount,1))*100)?>%;background:<?=$m[2]?>"></div></div>
              </div>
              <?php endforeach; ?>
            </div>

            <!-- User Account Overview -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">User Account Overview</div><div class="card-sub">Registered users by role and verification</div></div>
                <a href="<?= url('/admin/mis_admin/records') ?>" class="card-badge" style="text-decoration:none;">Manage Users →</a>
              </div>
              <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:16px;align-items:center;">
                <div style="position:relative;height:180px;display:flex;justify-content:center"><canvas id="userRolesChart"></canvas></div>
                <div>
                  <?php
                    $roleColors = ['Residents / Tenants' => 'var(--primary)', 'Guests' => 'var(--warning)', 'Staff & Admins' => 'var(--info)'];
                    foreach (($userRoleBreakdown ?? []) as $roleLbl => $roleCnt):
                      $rPct = ($totalUsers ?? 0) > 0 ? round(($roleCnt / $totalUsers) * 100, 1) : 0;
                      $rc = $roleColors[$roleLbl] ?? 'var(--text-muted)';
                  ?>
                  <div class="bl-row" style="margin-bottom:12px;">
                    <div class="bl-dot" style="background:<?= $rc ?>"></div>
                    <div style="flex:1;">
                      <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div class="bl-label" style="font-size:0.85rem;"><?= htmlspecialchars($roleLbl) ?></div>
                        <div style="font-size:0.75rem;font-weight:700;color:var(--text-muted);"><?= number_format($roleCnt) ?> (<?= $rPct ?>%)</div>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                  <div style="margin-top:12px;padding-top:10px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:0.78rem;color:var(--text-muted);">
                    <span>Verified: <strong style="color:var(--success);"><?= number_format($activeUsers ?? 0) ?></strong></span>
                    <span>Unverified: <strong style="color:var(--danger);"><?= number_format($unverifiedUsers ?? 0) ?></strong></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══════ SECTION 6: RECENT ACTIVITY & AUDIT TRAIL ═══════ -->
        <div class="db-section">
          <div class="db-section-head">
            <div><div class="db-section-title">System Audit Trail Snippet</div><div class="db-section-sub">Recent administrative actions, role updates, and security logs</div></div>
            <a href="<?= url('/admin/mis_admin/audit_logs') ?>" style="font-size:.75rem;color:var(--primary);font-weight:600;text-decoration:none">View All Audit Logs →</a>
          </div>
          <div class="card" id="activity-feed-container" style="padding:12px 16px">
            <?php if(!empty($recentLogs)): foreach($recentLogs as $log):
              $actor = $log['actor_name'] ?? 'System';
              $actorParts = explode(' ', trim($actor));
              $ini = strtoupper(substr($actorParts[0], 0, 1) . (count($actorParts) > 1 ? substr(end($actorParts), 0, 1) : ''));
              $nm = htmlspecialchars($actor);
              $act = htmlspecialchars($log['title'] ?? 'System Action');
              $details = htmlspecialchars($log['details'] ?? '');
              $tm = date('M d, g:i A', strtotime($log['created_at']));
              $tc = 'n';
              if (stripos($act, 'approv') !== false || stripos($act, 'assign') !== false || stripos($act, 'paid') !== false || stripos($act, 'accept') !== false) $tc = 's';
              elseif (stripos($act, 'pend') !== false || stripos($act, 'wait') !== false || stripos($act, 'receiv') !== false) $tc = 'w';
              elseif (stripos($act, 'reject') !== false || stripos($act, 'delete') !== false) $tc = 'd';
            ?>
            <div class="feed-item">
              <div class="feed-av"><?=$ini?></div>
              <div class="feed-body">
                <div class="feed-text"><strong><?=$nm?></strong> — <?=$act?> <?php if($details): ?><span style="color:var(--text-muted);font-size:0.8rem;">(<?=$details?>)</span><?php endif; ?></div>
                <div class="feed-time"><?=$tm?></div>
              </div>
              <span class="feed-tag <?=$tc?>"><?=ucfirst($log['type']??'info')?></span>
            </div>
            <?php endforeach; else: ?>
            <div style="text-align:center;padding:40px;color:var(--text-muted);font-size:.85rem">No recent activity found.</div>
            <?php endif; ?>
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

  // 1. Department Requests Breakdown
  const deptLabels = ['Apartments', "Da'wah", 'Islamic Education', 'Damayan'];
  const deptPending = <?= json_encode([
    $deptMetrics['apartments']['pending'] ?? 0,
    $deptMetrics['dawah']['pending'] ?? 0,
    $deptMetrics['education']['pending'] ?? 0,
    $deptMetrics['damayan']['pending'] ?? 0,
  ]) ?>;
  const deptApproved = <?= json_encode([
    $deptMetrics['apartments']['approved'] ?? 0,
    $deptMetrics['dawah']['approved'] ?? 0,
    $deptMetrics['education']['approved'] ?? 0,
    $deptMetrics['damayan']['approved'] ?? 0,
  ]) ?>;
  const deptCompleted = <?= json_encode([
    $deptMetrics['apartments']['completed'] ?? 0,
    $deptMetrics['dawah']['completed'] ?? 0,
    $deptMetrics['education']['completed'] ?? 0,
    $deptMetrics['damayan']['completed'] ?? 0,
  ]) ?>;

  new Chart(document.getElementById('deptBreakdownChart'), {
    type: 'bar',
    data: {
      labels: deptLabels,
      datasets: [
        { label: 'Pending', data: deptPending, backgroundColor: W, borderRadius: 4 },
        { label: 'Approved / Active', data: deptApproved, backgroundColor: S, borderRadius: 4 },
        { label: 'Completed', data: deptCompleted, backgroundColor: C, borderRadius: 4 }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } } },
      scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#e8ece9', drawBorder: false }, ticks: { stepSize: 1 } } }
    }
  });

  // 2. Overall Request Status Doughnut (All Departments)
  new Chart(document.getElementById('overallStatusChart'), {
    type: 'doughnut',
    data: {
      labels: ['Pending', 'Approved / Active', 'Completed'],
      datasets: [{
        data: [<?= (int)($grandPendingRequests ?? 0) ?>, <?= (int)($grandApprovedRequests ?? 0) ?>, <?= (int)($grandCompletedRequests ?? 0) ?>],
        backgroundColor: [W, S, C],
        borderWidth: 0,
        hoverOffset: 8
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
  });

  // 3. User Roles Doughnut
  const roleData = <?= json_encode(array_values($userRoleBreakdown ?? [])) ?>;
  const roleLabels = <?= json_encode(array_keys($userRoleBreakdown ?? [])) ?>;
  new Chart(document.getElementById('userRolesChart'), {
    type: 'doughnut',
    data: {
      labels: roleLabels,
      datasets: [{
        data: roleData,
        backgroundColor: [P, W, I],
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '64%', plugins: { legend: { display: false } } }
  });
});
</script>
</body>
</html>
