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

    /* Learning Progress Status Badges */
    .badge-status.badge-not-started {
      background: #f1f5f9 !important;
      color: #475569 !important;
      border: 1px solid #cbd5e1 !important;
    }
    .badge-status.badge-in-progress {
      background: #e0f2fe !important;
      color: #0369a1 !important;
      border: 1px solid #bae6fd !important;
    }
    .badge-status.badge-completed {
      background: #dcfce7 !important;
      color: #166534 !important;
      border: 1px solid #bbf7d0 !important;
    }

    /* Modal styling */
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(4px);
      display: none; align-items: center; justify-content: center;
      z-index: 99999;
      padding: 20px;
    }
    .modal-card {
      background: #ffffff;
      border-radius: 18px;
      width: 100%;
      max-width: 760px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      border: 1px solid var(--border);
    }
    .modal-header {
      background: #0f5c3a;
      color: #ffffff;
      padding: 18px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .session-item-row {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0;
      margin-bottom: 10px; background: #fff; transition: all 0.2s;
    }
    .session-item-row:hover { border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
    .session-item-row.completed-session {
      background: #f0fdf4;
      border-color: #bbf7d0;
    }
    .session-checkbox-label {
      display: inline-flex; align-items: center; gap: 7px; font-size: 0.82rem; font-weight: 700; cursor: pointer; user-select: none;
    }
    .session-checkbox-label input[type="checkbox"] {
      width: 18px; height: 18px; accent-color: #176b45; cursor: pointer;
    }
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

        <!-- ═══ ENROLLED COURSES QUICK OVERVIEW ═══ -->
        <?php if (!empty($coursesSummary)): ?>
        <div class="section-card" style="margin-bottom:24px;">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Enrolled Courses &amp; Programs (Female Da'wah)
            </h6>
            <a href="<?= url('/admin/dawah/education/courses') ?>" style="font-size:0.78rem; font-weight:700; color:var(--primary); text-decoration:none;">
              View Enrolled Courses Directory →
            </a>
          </div>
          <div class="section-card-body" style="padding:16px 20px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
              <?php foreach ($coursesSummary as $c): ?>
                <div style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:14px 16px; display:flex; justify-content:space-between; align-items:center; transition:all 0.2s; cursor:pointer;" onclick="location.href='<?= url('/admin/dawah/education/courses') ?>?course=' + encodeURIComponent('<?= addslashes($c['course_title']) ?>')">
                  <div>
                    <div style="font-size:0.68rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;"><?= htmlspecialchars($c['batch_no'] ?? 'Batch 08') ?></div>
                    <div style="font-size:0.9rem; font-weight:800; color:#0f172a; margin-top:2px;"><?= htmlspecialchars($c['course_title']) ?></div>
                    <div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; margin-top:2px;">
                      <?= (int)($c['total_students'] ?? 0) ?> Total Applicant<?= (int)($c['total_students'] ?? 0) !== 1 ? 's' : '' ?>
                    </div>
                  </div>
                  <div style="text-align:right;">
                    <div style="font-size:1.4rem; font-weight:800; color:#166534; font-family:'Lora',serif;"><?= (int)($c['active_count'] ?? 0) ?></div>
                    <div style="font-size:0.68rem; font-weight:700; color:#166534; text-transform:uppercase;">Enrolled</div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <?php
          $notStartedCount = count(array_filter($students, fn($s) => ($s['learning_status'] ?? 'Not Started') === 'Not Started'));
          $inProgressCount = count(array_filter($students, fn($s) => ($s['learning_status'] ?? 'Not Started') === 'In Progress'));
          $completedCount = count(array_filter($students, fn($s) => ($s['learning_status'] ?? 'Not Started') === 'Completed'));
          $totalCount = max(count($students), 1);
        ?>

        <!-- Main Content Grid -->
        <div class="dash-grid-2">
          <!-- Learning Progress Milestones Card -->
          <div class="section-card">
            <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
              <h6>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                Learning Progress Overview
              </h6>
              <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted);"><?= count($students) ?> Enrolled Students</span>
            </div>
            <div class="section-card-body">
              <!-- Progress Milestones Breakdown -->
              <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; margin-bottom:16px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 10px; text-align:center;">
                  <div style="font-size:1.35rem; font-weight:800; color:#475569; font-family:'Lora',serif;" id="milestone-not-started"><?= $notStartedCount ?></div>
                  <div style="font-size:0.68rem; font-weight:700; color:#64748b; text-transform:uppercase; margin-top:2px;">Not Started</div>
                </div>
                <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:12px 10px; text-align:center;">
                  <div style="font-size:1.35rem; font-weight:800; color:#0369a1; font-family:'Lora',serif;" id="milestone-in-progress"><?= $inProgressCount ?></div>
                  <div style="font-size:0.68rem; font-weight:700; color:#0284c7; text-transform:uppercase; margin-top:2px;">In Progress</div>
                </div>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 10px; text-align:center;">
                  <div style="font-size:1.35rem; font-weight:800; color:#166534; font-family:'Lora',serif;" id="milestone-completed"><?= $completedCount ?></div>
                  <div style="font-size:0.68rem; font-weight:700; color:#166534; text-transform:uppercase; margin-top:2px;">Completed</div>
                </div>
              </div>

              <!-- Visual Proportion Bar -->
              <div style="margin-bottom:18px;">
                <div style="height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden; display:flex;">
                  <div id="prop-bar-comp" style="width:<?= round(($completedCount / $totalCount) * 100) ?>%; background:#166534; transition:width 0.6s;" title="Completed"></div>
                  <div id="prop-bar-prog" style="width:<?= round(($inProgressCount / $totalCount) * 100) ?>%; background:#0284c7; transition:width 0.6s;" title="In Progress"></div>
                  <div id="prop-bar-not" style="width:<?= round(($notStartedCount / $totalCount) * 100) ?>%; background:#94a3b8; transition:width 0.6s;" title="Not Started"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:var(--text-muted); font-weight:600; margin-top:6px;">
                  <span><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#166534; margin-right:4px;"></span>Completed</span>
                  <span><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#0284c7; margin-right:4px;"></span>In Progress</span>
                  <span><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#94a3b8; margin-right:4px;"></span>Not Started</span>
                </div>
              </div>

              <!-- Quick Student Highlights -->
              <div style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:10px; letter-spacing:0.04em;">Recent Learners</div>
              <?php if (empty($students)): ?>
                <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.85rem;">No student records found.</div>
              <?php else: ?>
                <?php foreach (array_slice($students, 0, 4) as $s): 
                  $stBadge = match($s['learning_status'] ?? 'Not Started') {
                    'Completed' => 'badge-completed',
                    'In Progress' => 'badge-in-progress',
                    default => 'badge-not-started'
                  };
                  $progTitle = trim($s['level'] ?? '');
                  if (empty($progTitle)) $progTitle = trim($s['program_name'] ?? 'Islamic Education');
                  $sNo = '#EDU-' . str_pad((string)$s['id'], 4, '0', STR_PAD_LEFT);
                ?>
                  <div class="activity-item">
                    <div class="act-dot" style="background:<?= ($s['learning_status'] ?? '') === 'Completed' ? 'var(--success)' : ((($s['learning_status'] ?? '') === 'In Progress') ? 'var(--accent)' : '#94a3b8') ?>;"></div>
                    <div style="flex:1; min-width:0;">
                      <div style="display:flex; align-items:center; gap:6px;">
                        <span class="act-name"><?= htmlspecialchars(trim($s['first_name'] . ' ' . $s['last_name'])) ?></span>
                        <span style="font-family:'Courier New', monospace; font-weight:800; color:var(--primary); background:rgba(23,107,69,0.08); padding:1px 6px; border-radius:4px; font-size:0.7rem; border:1px solid rgba(23,107,69,0.2);"><?= $sNo ?></span>
                      </div>
                      <div class="act-meta"><?= htmlspecialchars($progTitle) ?> · <?= (int)($s['sessions_attended'] ?? 0) ?>/<?= (int)($s['total_sessions'] ?? 12) ?> Attended</div>
                    </div>
                    <div style="text-align:right;">
                      <span class="badge-status <?= $stBadge ?>"><?= htmlspecialchars($s['learning_status'] ?? 'Not Started') ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Department Overview Card -->
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

                <!-- Summary Stats (No Retention, Track Avg Learning Progress) -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-top:8px;">
                  <div style="background:rgba(23,107,69,0.04); padding:14px; border-radius:10px; text-align:center;">
                    <div id="stat-avg-progress" style="font-size:1.4rem; font-weight:800; color:var(--primary); font-family:'Lora',serif;"><?= (int)($analytics['avg_progress'] ?? 0) ?>%</div>
                    <div style="font-size:0.7rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Avg. Progress</div>
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

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- 📋 STUDENT LEARNING PROGRESS & ATTENDANCE TABLE CARD      -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div class="section-card" style="margin-top:24px;">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
              <h6 style="margin:0; display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--primary);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                Student Learning Progress &amp; Attendance
              </h6>
              <div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; margin-top:2px;">
                Strictly tracked by verified session attendance and completed learning sessions (12 Curriculum Sessions)
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
              <input type="text" id="dash-student-search" placeholder="Search student name, student no..." onkeyup="filterDashboardStudents()" style="border:1.5px solid var(--border); border-radius:8px; padding:6px 12px; font-size:0.8rem; outline:none; background:#fff; font-family:inherit;">
              <a href="<?= url('/admin/dawah/education') ?>" class="btn-topbar" style="text-decoration:none; font-size:0.78rem;">Full Education Records →</a>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="dashboard-progress-table">
                <thead>
                  <tr>
                    <th>Student No.</th>
                    <th>Student Name</th>
                    <th>Program Level</th>
                    <th>Sessions Attended</th>
                    <th>Completed Sessions</th>
                    <th>Progress Percentage</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($students)): ?>
                    <tr>
                      <td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">
                        <div style="font-size:2.2rem; margin-bottom:8px;">📚</div>
                        <div style="font-weight:600;">No students enrolled yet</div>
                        <div style="font-size:0.8rem; margin-top:4px;">Accepted enrollments will display here with attendance and progress tracking</div>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($students as $s): 
                      $stStatus = $s['learning_status'] ?? 'Not Started';
                      $stBadge = match($stStatus) {
                        'Completed' => 'badge-completed',
                        'In Progress' => 'badge-in-progress',
                        default => 'badge-not-started'
                      };
                      $pPercent = (int)($s['progress_percent'] ?? 0);
                      $progTitle = trim($s['level'] ?? '');
                      if (empty($progTitle)) $progTitle = trim($s['program_name'] ?? 'Islamic Education');
                      $batchTitle = trim($s['batch_no'] ?? 'Batch 08');
                      $fullName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''));
                      $muslimName = trim($s['muslim_name'] ?? '');
                      $studentNo = '#EDU-' . str_pad((string)$s['id'], 4, '0', STR_PAD_LEFT);
                    ?>
                      <tr id="prog-row-<?= $s['id'] ?>" class="dash-student-row" data-name="<?= strtolower(htmlspecialchars($fullName . ' ' . $muslimName . ' ' . $studentNo . ' ' . $s['id'])) ?>">
                        <td>
                          <span style="font-family:'Courier New', monospace; font-weight:800; color:var(--primary); background:rgba(23,107,69,0.08); padding:3px 8px; border-radius:6px; font-size:0.82rem; border:1px solid rgba(23,107,69,0.2); white-space:nowrap;">
                            <?= $studentNo ?>
                          </span>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main); font-size:0.88rem;"><?= htmlspecialchars($fullName) ?></div>
                          <?php if (!empty($muslimName)): ?>
                            <div style="font-size:0.73rem; color:var(--text-muted);">Muslim Name: <span style="font-weight:600; color:var(--primary);"><?= htmlspecialchars($muslimName) ?></span></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div style="font-weight:600; font-size:0.84rem;"><?= htmlspecialchars($progTitle) ?></div>
                          <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($batchTitle) ?></div>
                        </td>
                        <td>
                          <div style="font-weight:700; font-size:0.85rem;">
                            <span id="row-att-<?= $s['id'] ?>"><?= (int)($s['sessions_attended'] ?? 0) ?></span> / <?= (int)($s['total_sessions'] ?? 12) ?>
                          </div>
                          <div style="font-size:0.7rem; color:var(--text-muted);">sessions verified</div>
                        </td>
                        <td>
                          <div style="font-weight:700; font-size:0.85rem;">
                            <span id="row-comp-<?= $s['id'] ?>"><?= (int)($s['sessions_completed'] ?? 0) ?></span> / <?= (int)($s['total_sessions'] ?? 12) ?>
                          </div>
                          <div style="font-size:0.7rem; color:var(--text-muted);">lessons mastered</div>
                        </td>
                        <td style="min-width:160px;">
                          <div style="display:flex; align-items:center; gap:8px;">
                            <div style="flex:1; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                              <div id="row-bar-<?= $s['id'] ?>" style="height:100%; width:<?= $pPercent ?>%; background:var(--primary); border-radius:4px; transition:width 0.5s ease;"></div>
                            </div>
                            <span id="row-pct-<?= $s['id'] ?>" style="font-weight:800; font-size:0.82rem; color:var(--text-main); min-width:36px; text-align:right;"><?= $pPercent ?>%</span>
                          </div>
                        </td>
                        <td>
                          <span id="row-status-<?= $s['id'] ?>" class="badge-status <?= $stBadge ?>">
                            <?= htmlspecialchars($stStatus) ?>
                          </span>
                        </td>
                        <td style="text-align:right;">
                          <button type="button" class="btn-topbar primary" onclick="openProgressModal(<?= $s['id'] ?>)" style="background:#176b45; color:#ffffff; border:none; padding:7px 12px; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.75rem; display:inline-flex; align-items:center; gap:5px; transition:all 0.15s ease;">
                            <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                            View Progress
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
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
  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🎓 DETAILED STUDENT LEARNING PROGRESS & ATTENDANCE MODAL   -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="student-progress-modal" class="modal-overlay" style="display:none;" onclick="handleProgressModalOverlay(event)">
    <div class="modal-card" style="max-width:820px;">
      <div class="modal-header">
        <div>
          <div style="font-size:1.15rem; font-weight:800; font-family:'Lora',serif;" id="pm-student-name">Student Progress &amp; Attendance</div>
          <div style="font-size:0.75rem; color:rgba(255,255,255,0.85);" id="pm-student-sub">Student No.: #EDU-0000 • Batch 08</div>
        </div>
        <button type="button" onclick="closeProgressModal()" style="background:none; border:none; color:white; font-size:1.6rem; cursor:pointer; line-height:1;">&times;</button>
      </div>

      <div class="modal-body" style="padding:22px 24px;">
        <!-- Program & Status Card -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; margin-bottom:18px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            <div>
              <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                <span style="font-size:0.7rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">Academic Program Track</span>
                <span id="pm-student-no-badge" style="font-family:'Courier New', monospace; font-size:0.74rem; font-weight:800; color:var(--primary); background:rgba(23,107,69,0.08); padding:2px 7px; border-radius:4px; border:1px solid rgba(23,107,69,0.25);">Student No.: #EDU-0000</span>
              </div>
              <div style="font-size:1.05rem; font-weight:800; color:#0f172a;" id="pm-program-title">—</div>
            </div>
            <div id="pm-status-badge-wrap">
              <span class="badge-status badge-not-started" id="pm-status-badge">Not Started</span>
            </div>
          </div>

          <!-- Progress KPIs Grid -->
          <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-bottom:14px;">
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; text-align:center;">
              <div style="font-size:1.25rem; font-weight:800; color:var(--primary); font-family:'Lora',serif;" id="pm-attended-count">0 / 12</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Sessions Attended</div>
            </div>
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; text-align:center;">
              <div style="font-size:1.25rem; font-weight:800; color:#0369a1; font-family:'Lora',serif;" id="pm-completed-count">0 / 12</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Completed Sessions</div>
            </div>
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; text-align:center;">
              <div style="font-size:1.25rem; font-weight:800; color:#b45309; font-family:'Lora',serif;" id="pm-progress-pct">0%</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Overall Progress</div>
            </div>
          </div>

          <!-- Progress Bar -->
          <div>
            <div style="display:flex; justify-content:space-between; font-size:0.75rem; font-weight:700; color:var(--text-muted); margin-bottom:5px;">
              <span>Curriculum Progress</span>
              <span id="pm-progress-label">0% completed</span>
            </div>
            <div style="height:10px; background:#e2e8f0; border-radius:5px; overflow:hidden;">
              <div id="pm-progress-bar" style="height:100%; width:0%; background:linear-gradient(90deg, #176b45, #166534); border-radius:5px; transition:width 0.4s ease;"></div>
            </div>
          </div>
        </div>

        <!-- Official Completion Rule Alert -->
        <div style="display:flex; gap:10px; align-items:flex-start; background:rgba(23,107,69,0.06); border:1px solid rgba(23,107,69,0.2); border-radius:10px; padding:12px 14px; margin-bottom:18px;">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#176b45;flex-shrink:0;margin-top:1px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
          <div style="font-size:0.78rem; color:#14532d; line-height:1.45;">
            <strong>Learning Evaluation Policy:</strong> Progress and program completion are strictly based on verified attendance and completed learning sessions. Neither attendance alone nor session completion alone determines program completion. Full program completion requires all 12 sessions to be both attended and completed.
          </div>
        </div>

        <!-- 12 Sessions Curriculum Checklist -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">Standard 12-Session Curriculum Checklist</div>
          <div style="font-size:0.72rem; color:var(--primary); font-weight:700;" id="pm-save-indicator">✓ Live Synced</div>
        </div>

        <div id="pm-sessions-container" style="display:flex; flex-direction:column; gap:8px;">
          <!-- Sessions dynamically loaded via AJAX -->
          <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading curriculum sessions...</div>
        </div>

        <!-- Modal Footer -->
        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:16px; margin-top:20px;">
          <div style="font-size:0.75rem; color:var(--text-muted);">
            Changes to attendance and session completion are saved immediately.
          </div>
          <button type="button" class="btn-topbar" onclick="closeProgressModal()">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    let currentProgressStudentId = null;

    function filterDashboardStudents() {
      const q = (document.getElementById('dash-student-search')?.value || '').toLowerCase().trim();
      const rows = document.querySelectorAll('.dash-student-row');
      rows.forEach(r => {
        const name = r.getAttribute('data-name') || '';
        r.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    async function openProgressModal(studentId) {
      currentProgressStudentId = studentId;
      const modal = document.getElementById('student-progress-modal');
      if (!modal) return;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';

      const container = document.getElementById('pm-sessions-container');
      container.innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);"><div style="font-size:1.8rem; margin-bottom:6px;">⏳</div>Loading session progress records...</div>';

      try {
        const res = await fetch('<?= url("/admin/dawah/education/progress") ?>?id=' + encodeURIComponent(studentId));
        const json = await res.json();
        if (!json.success || !json.data) {
          container.innerHTML = `<div style="text-align:center; padding:20px; color:#dc2626;">${escapeHtml(json.message || 'Error loading student progress.')}</div>`;
          return;
        }

        renderProgressModal(json.data);
      } catch (err) {
        console.error(err);
        container.innerHTML = '<div style="text-align:center; padding:20px; color:#dc2626;">Failed to connect to server.</div>';
      }
    }

    function closeProgressModal() {
      const modal = document.getElementById('student-progress-modal');
      if (modal) modal.style.display = 'none';
      document.body.style.overflow = '';
      currentProgressStudentId = null;
    }

    function handleProgressModalOverlay(e) {
      if (e.target && e.target.id === 'student-progress-modal') {
        closeProgressModal();
      }
    }

    function renderProgressModal(data) {
      const st = data.enrollment || {};
      const sum = data.summary || {};
      const sessions = data.sessions || [];

      const fullName = `${st.first_name || ''} ${st.last_name || ''}`.trim() || 'Student #' + st.id;
      const muslimName = st.muslim_name ? ` (${st.muslim_name})` : '';
      const prog = st.level || st.program_name || 'Islamic Education';
      const batch = st.batch_no || 'Batch 08';

      const refCode = '#EDU-' + String(st.id).padStart(4, '0');
      document.getElementById('pm-student-name').textContent = fullName + muslimName;
      document.getElementById('pm-student-sub').textContent = `Student No.: ${refCode} • ${batch}`;
      const noBadge = document.getElementById('pm-student-no-badge');
      if (noBadge) noBadge.textContent = `Student No.: ${refCode}`;
      document.getElementById('pm-program-title').textContent = `${prog} • ${batch}`;

      updateProgressModalSummary(sum);

      const container = document.getElementById('pm-sessions-container');
      container.innerHTML = '';

      sessions.forEach(sess => {
        const isAtt = parseInt(sess.is_attended, 10) === 1;
        const isComp = parseInt(sess.is_completed, 10) === 1;
        const attDateStr = sess.attended_at ? new Date(sess.attended_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '';
        const compDateStr = sess.completed_at ? new Date(sess.completed_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '';

        const row = document.createElement('div');
        row.className = 'session-item-row' + (isComp && isAtt ? ' completed-session' : '');
        row.id = `sess-row-${sess.session_number}`;
        row.innerHTML = `
          <div style="flex:1; min-width:0; padding-right:12px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; background:${isComp && isAtt ? '#166534' : '#e2e8f0'}; color:${isComp && isAtt ? '#fff' : '#475569'}; font-size:0.75rem; font-weight:800; flex-shrink:0;">
                ${sess.session_number}
              </span>
              <div style="font-weight:700; font-size:0.86rem; color:#0f172a; line-height:1.3;">
                ${escapeHtml(sess.session_title)}
              </div>
            </div>
            <div style="display:flex; gap:12px; margin-top:4px; margin-left:32px; font-size:0.72rem; color:var(--text-muted);">
              <span id="sess-att-lbl-${sess.session_number}">
                ${isAtt ? `<span style="color:#166534; font-weight:700;">✓ Attended${attDateStr ? ' (' + attDateStr + ')' : ''}</span>` : 'Not attended'}
              </span>
              <span>•</span>
              <span id="sess-comp-lbl-${sess.session_number}">
                ${isComp ? `<span style="color:#0369a1; font-weight:700;">✓ Completed${compDateStr ? ' (' + compDateStr + ')' : ''}</span>` : 'Not completed'}
              </span>
            </div>
          </div>

          <div style="display:flex; align-items:center; gap:16px; flex-shrink:0;">
            <label class="session-checkbox-label" title="Toggle attendance verification">
              <input type="checkbox" id="chk-att-${sess.session_number}" ${isAtt ? 'checked' : ''} onchange="toggleSessionItem(${sess.session_number})">
              <span>Attended</span>
            </label>
            <label class="session-checkbox-label" title="Toggle learning session completion">
              <input type="checkbox" id="chk-comp-${sess.session_number}" ${isComp ? 'checked' : ''} onchange="toggleSessionItem(${sess.session_number})">
              <span>Completed</span>
            </label>
          </div>
        `;
        container.appendChild(row);
      });
    }

    function updateProgressModalSummary(sum) {
      const total = sum.total_sessions || 12;
      const att = sum.sessions_attended || 0;
      const comp = sum.sessions_completed || 0;
      const pct = sum.progress_percent || 0;
      const status = sum.learning_status || 'Not Started';

      document.getElementById('pm-attended-count').textContent = `${att} / ${total}`;
      document.getElementById('pm-completed-count').textContent = `${comp} / ${total}`;
      document.getElementById('pm-progress-pct').textContent = `${pct}%`;
      document.getElementById('pm-progress-label').textContent = `${pct}% completed`;
      document.getElementById('pm-progress-bar').style.width = `${pct}%`;

      const badgeClass = status === 'Completed' ? 'badge-completed' : (status === 'In Progress' ? 'badge-in-progress' : 'badge-not-started');
      const badgeWrap = document.getElementById('pm-status-badge-wrap');
      badgeWrap.innerHTML = `<span class="badge-status ${badgeClass}" id="pm-status-badge">${status}</span>`;
    }

    async function toggleSessionItem(sessionNumber) {
      if (!currentProgressStudentId) return;
      const stId = currentProgressStudentId;
      const chkAtt = document.getElementById(`chk-att-${sessionNumber}`);
      const chkComp = document.getElementById(`chk-comp-${sessionNumber}`);

      const isAttended = chkAtt ? chkAtt.checked : false;
      const isCompleted = chkComp ? chkComp.checked : false;

      const ind = document.getElementById('pm-save-indicator');
      if (ind) {
        ind.textContent = 'Saving...';
        ind.style.color = '#c79a2b';
      }

      try {
        const res = await fetch('<?= url("/admin/dawah/education/progress/update") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            enrollment_id: stId,
            session_number: sessionNumber,
            is_attended: isAttended ? 1 : 0,
            is_completed: isCompleted ? 1 : 0
          })
        });

        const json = await res.json();
        if (json.success && json.summary) {
          updateProgressModalSummary(json.summary);

          // Update labels for this session
          const attLbl = document.getElementById(`sess-att-lbl-${sessionNumber}`);
          const compLbl = document.getElementById(`sess-comp-lbl-${sessionNumber}`);
          if (attLbl) {
            attLbl.innerHTML = isAttended ? '<span style="color:#166534; font-weight:700;">✓ Attended (Just now)</span>' : 'Not attended';
          }
          if (compLbl) {
            compLbl.innerHTML = isCompleted ? '<span style="color:#0369a1; font-weight:700;">✓ Completed (Just now)</span>' : 'Not completed';
          }

          const sessRow = document.getElementById(`sess-row-${sessionNumber}`);
          if (sessRow) {
            if (isAttended && isCompleted) {
              sessRow.classList.add('completed-session');
            } else {
              sessRow.classList.remove('completed-session');
            }
          }

          // Live update the row in the main dashboard table
          updateDashboardTableRow(stId, json.summary);

          if (ind) {
            ind.textContent = '✓ Saved Live';
            ind.style.color = '#166534';
            setTimeout(() => { ind.textContent = '✓ Live Synced'; }, 2000);
          }
        } else {
          alert(json.message || 'Error updating session progress.');
        }
      } catch (err) {
        console.error(err);
        if (ind) {
          ind.textContent = 'Error saving';
          ind.style.color = '#dc2626';
        }
      }
    }

    function updateDashboardTableRow(stId, sum) {
      const total = sum.total_sessions || 12;
      const att = sum.sessions_attended || 0;
      const comp = sum.sessions_completed || 0;
      const pct = sum.progress_percent || 0;
      const status = sum.learning_status || 'Not Started';

      const rowAtt = document.getElementById(`row-att-${stId}`);
      const rowComp = document.getElementById(`row-comp-${stId}`);
      const rowBar = document.getElementById(`row-bar-${stId}`);
      const rowPct = document.getElementById(`row-pct-${stId}`);
      const rowStatus = document.getElementById(`row-status-${stId}`);

      if (rowAtt) rowAtt.textContent = att;
      if (rowComp) rowComp.textContent = comp;
      if (rowBar) rowBar.style.width = `${pct}%`;
      if (rowPct) rowPct.textContent = `${pct}%`;
      if (rowStatus) {
        const badgeClass = status === 'Completed' ? 'badge-completed' : (status === 'In Progress' ? 'badge-in-progress' : 'badge-not-started');
        rowStatus.className = `badge-status ${badgeClass}`;
        rowStatus.textContent = status;
      }
    }
  </script>
</body>
</html>
