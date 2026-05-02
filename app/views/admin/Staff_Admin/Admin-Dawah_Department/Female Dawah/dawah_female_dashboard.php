<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Da'wah Manager</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .dash-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
    .dash-kpi {
      background: white; border-radius: 14px; padding: 22px 24px; border: 1px solid var(--border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.03); transition: all 0.3s ease; cursor: pointer; position: relative; overflow: hidden;
    }
    .dash-kpi::before {
      content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; border-radius: 14px 0 0 14px;
    }
    .dash-kpi:nth-child(1)::before { background: var(--primary); }
    .dash-kpi:nth-child(2)::before { background: var(--accent); }
    .dash-kpi:nth-child(3)::before { background: var(--success); }
    .dash-kpi:nth-child(4)::before { background: var(--danger); }
    .dash-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-color: var(--accent); }
    .kpi-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .kpi-icon {
      width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    }
    .kpi-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    .kpi-value { font-size: 2rem; font-weight: 800; line-height: 1; font-family: 'Lora', serif; }
    .kpi-sub { font-size: 0.72rem; font-weight: 700; margin-top: 6px; display: flex; align-items: center; gap: 4px; color: var(--text-muted); }

    .quick-links { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }
    .quick-link {
      display: flex; align-items: center; gap: 14px; padding: 16px 20px; background: white;
      border-radius: 12px; border: 1px solid var(--border); text-decoration: none; color: var(--text-main);
      transition: all 0.25s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .quick-link:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
    .ql-icon {
      width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ql-title { font-weight: 700; font-size: 0.9rem; }
    .ql-desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

    .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .activity-item {
      display: flex; align-items: center; gap: 14px; padding: 14px 0;
      border-bottom: 1px solid rgba(0,0,0,0.04); transition: background 0.2s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: rgba(0,0,0,0.01); border-radius: 8px; padding-left: 8px; padding-right: 8px; }
    .act-dot {
      width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
    .act-name { font-weight: 700; font-size: 0.88rem; }
    .act-meta { font-size: 0.75rem; color: var(--text-muted); }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'dashboard';
      $dawah_type = 'female'; 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Welcome, <?= htmlspecialchars(explode(' ',trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')))[0]) ?: 'Da\'wah Manager' ?></div>
          <div class="top-bar-subtitle">Female Da'wah Department — <?= date('l, F j, Y') ?></div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="location.href='<?= url('/admin/dawah/analytics') ?>'">
             <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2.5;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/><path d="M9 17V10M12 17V7M15 17V13"/></svg>
             Analytics
           </button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <span class="current">Female Da'wah Dashboard</span>
        </div>

        <!-- KPI Cards -->
        <div class="dash-kpi-grid">
          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/education') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Total Students</span>
              <div class="kpi-icon" style="background:rgba(23,107,69,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--primary);"><?= $analytics['total_students'] ?? 0 ?></div>
            <div class="kpi-sub">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:var(--success);"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
              <?= $analytics['active_students'] ?? 0 ?> currently active
            </div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/counseling') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Counseling</span>
              <div class="kpi-icon" style="background:rgba(199,154,43,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--accent);"><?= $analytics['counseling_total'] ?? 0 ?></div>
            <div class="kpi-sub">
              <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:var(--success);"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              <?= $analytics['counseling_approved'] ?? 0 ?> approved
            </div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/education') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Enrolled</span>
              <div class="kpi-icon" style="background:rgba(47,138,96,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--success);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--success);"><?= $analytics['active_students'] ?? 0 ?></div>
            <div class="kpi-sub">Currently active students</div>
          </div>

          <div class="dash-kpi" onclick="window.location.href='<?= url('/admin/dawah/counseling') ?>'">
            <div class="kpi-header">
              <span class="kpi-label">Pending Review</span>
              <div class="kpi-icon" style="background:rgba(139,46,46,0.1);">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              </div>
            </div>
            <div class="kpi-value" style="color:var(--danger);"><?= $analytics['pending'] ?? 0 ?></div>
            <div class="kpi-sub">Awaiting action</div>
          </div>
        </div>

        <!-- Quick Access Links -->
        <div class="quick-links">
          <a href="<?= url('/admin/dawah/education') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(23,107,69,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div>
              <div class="ql-title">Islamic Education</div>
              <div class="ql-desc">Manage student enrollments</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/counseling') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(199,154,43,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--accent);"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div>
              <div class="ql-title">Counseling Services</div>
              <div class="ql-desc">View sisters' sessions</div>
            </div>
          </a>
          <a href="<?= url('/admin/dawah/schedule') ?>" class="quick-link">
            <div class="ql-icon" style="background:rgba(31,111,90,0.08);">
              <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--info);"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
            </div>
            <div>
              <div class="ql-title">Service Schedule</div>
              <div class="ql-desc">Calendar & appointments</div>
            </div>
          </a>
        </div>

        <!-- Main Content Grid -->
        <div class="dash-grid-2">
          <!-- Recent Student Activity -->
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                Recent Student Activity
              </h6>
            </div>
            <div class="section-card-body">
              <?php if (empty($students)): ?>
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                  <div style="font-size:2.5rem; margin-bottom:8px;">📚</div>
                  <div style="font-weight:600;">No student records yet</div>
                  <div style="font-size:0.8rem; margin-top:4px;">Student enrollments will appear here</div>
                </div>
              <?php else: ?>
                <?php foreach (array_slice($students, 0, 6) as $s): 
                  $dotColor = match($s['status']) {
                    'active' => 'var(--success)',
                    'completed' => 'var(--accent)',
                    'dropped' => 'var(--danger)',
                    default => 'var(--warning)',
                  };
                  $sc = match($s['status']) {
                    'active' => 'badge-active',
                    'completed' => 'badge-complete',
                    'dropped' => 'badge-rejected',
                    default => 'badge-pending',
                  };
                ?>
                  <div class="activity-item">
                    <div class="act-dot" style="background:<?= $dotColor ?>;"></div>
                    <div style="flex:1; min-width:0;">
                      <div class="act-name"><?= trim($s['first_name'] . ' ' . $s['last_name']) ?></div>
                      <div class="act-meta"><?= $s['program_name'] ?> · <?= date('M d', strtotime($s['created_at'])) ?></div>
                    </div>
                    <span class="badge-status <?= $sc ?>"><?= ucfirst($s['status']) ?></span>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
              <div style="padding:16px 0 4px; text-align:center; border-top:1px solid var(--border);">
                <a href="<?= url('/admin/dawah/education') ?>" style="font-size:0.82rem; font-weight:700; color:var(--accent); text-decoration:none;">View All Students →</a>
              </div>
            </div>
          </div>

          <!-- Department Overview -->
          <div class="section-card">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Department Overview
              </h6>
            </div>
            <div class="section-card-body">
              <div style="display:flex; flex-direction:column; gap:16px;">
                <!-- Education Progress Bar -->
                <div>
                  <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                    <span style="font-size:0.8rem; font-weight:700;">Active Enrollment</span>
                    <span style="font-size:0.8rem; font-weight:800; color:var(--primary);"><?= $analytics['active_students'] ?? 0 ?> / <?= $analytics['total_students'] ?? 0 ?></span>
                  </div>
                  <?php $pct = ($analytics['total_students'] ?? 0) > 0 ? round((($analytics['active_students'] ?? 0) / ($analytics['total_students'] ?? 0)) * 100) : 0; ?>
                  <div style="height:8px; background:var(--border); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; width:<?= $pct ?>%; background:var(--primary); border-radius:4px; transition:width 0.8s ease;"></div>
                  </div>
                </div>

                <!-- Counseling Progress Bar -->
                <div>
                  <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                    <span style="font-size:0.8rem; font-weight:700;">Counseling Approval</span>
                    <span style="font-size:0.8rem; font-weight:800; color:var(--accent);"><?= $analytics['counseling_approved'] ?? 0 ?> / <?= $analytics['counseling_total'] ?? 0 ?></span>
                  </div>
                  <?php $cPct = ($analytics['counseling_total'] ?? 0) > 0 ? round((($analytics['counseling_approved'] ?? 0) / ($analytics['counseling_total'] ?? 0)) * 100) : 0; ?>
                  <div style="height:8px; background:var(--border); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; width:<?= $cPct ?>%; background:var(--accent); border-radius:4px; transition:width 0.8s ease;"></div>
                  </div>
                </div>

                <!-- Completion Progress Bar -->
                <div>
                  <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                    <span style="font-size:0.8rem; font-weight:700;">Program Completion</span>
                    <span style="font-size:0.8rem; font-weight:800; color:var(--success);"><?= $analytics['completed'] ?? 0 ?> graduated</span>
                  </div>
                  <?php $cpPct = ($analytics['total_students'] ?? 0) > 0 ? round((($analytics['completed'] ?? 0) / ($analytics['total_students'] ?? 0)) * 100) : 0; ?>
                  <div style="height:8px; background:var(--border); border-radius:4px; overflow:hidden;">
                    <div style="height:100%; width:<?= $cpPct ?>%; background:var(--success); border-radius:4px; transition:width 0.8s ease;"></div>
                  </div>
                </div>

                <!-- Summary Stats -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:8px;">
                  <div style="background:rgba(23,107,69,0.04); padding:14px; border-radius:10px; text-align:center;">
                    <div style="font-size:1.4rem; font-weight:800; color:var(--primary); font-family:'Lora',serif;"><?= $pct ?>%</div>
                    <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Retention</div>
                  </div>
                  <div style="background:rgba(199,154,43,0.04); padding:14px; border-radius:10px; text-align:center;">
                    <div style="font-size:1.4rem; font-weight:800; color:var(--accent); font-family:'Lora',serif;"><?= $cPct ?>%</div>
                    <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Approval Rate</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Counseling Requests -->
        <div class="section-card" style="margin-top:24px;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--accent);margin-right:8px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Recent Counseling Requests
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Reason</th>
                    <th>Preferred Date</th>
                    <th>Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($counseling)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                      <div style="font-size:2rem; margin-bottom:8px;">💬</div>
                      <div style="font-weight:600;">No counseling requests yet</div>
                      <div style="font-size:0.8rem; margin-top:4px;">Requests from sisters will appear here</div>
                    </td></tr>
                  <?php else: ?>
                    <?php foreach (array_slice($counseling, 0, 8) as $c):
                      $csc = match($c['status']) {
                        'approved' => 'badge-approved',
                        'rejected' => 'badge-rejected',
                        default => 'badge-pending',
                      };
                    ?>
                      <tr>
                        <td class="td-id">#<?= $c['id'] ?></td>
                        <td style="font-weight:600;"><?= trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($c['reason'] ?? 'General') ?></td>
                        <td><?= !empty($c['preferred_date']) ? date('M d, Y', strtotime($c['preferred_date'])) : '—' ?></td>
                        <td><?= !empty($c['preferred_time']) ? date('h:i A', strtotime($c['preferred_time'])) : '—' ?></td>
                        <td><span class="badge-status <?= $csc ?>"><?= ucfirst($c['status']) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          <div style="padding:14px 24px; text-align:center; border-top:1px solid var(--border);">
            <a href="<?= url('/admin/dawah/counseling') ?>" style="font-size:0.82rem; font-weight:700; color:var(--accent); text-decoration:none;">View All Counseling →</a>
          </div>
        </div>

      </div>
    </div>
  </div>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');
  </script>
</body>
</html>
