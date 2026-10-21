<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$records = $records ?? [];
$coursesSummary = $coursesSummary ?? [];
$analytics = $analytics ?? [];
$dawah_type = $dawah_type ?? 'female';

// Standard Curriculum Map with live counts from records
$defaultPrograms = [
  'Level 1 — Basic Arabic & Tajweed' => [
    'title' => 'Level 1 — Basic Arabic & Tajweed',
    'track' => 'Foundational Arabic',
    'desc'  => 'Arabic alphabet, phonetics, articulation points (Makharij), and rules of Tajweed.',
    'batch' => 'Batch 08'
  ],
  'Level 2 — Intermediate Islamic Studies & Fiqh' => [
    'title' => 'Level 2 — Intermediate Islamic Studies & Fiqh',
    'track' => 'Intermediate Fiqh',
    'desc'  => 'Practical Fiqh of Taharah, Salah, Sawm, and foundational Aqeedah essentials.',
    'batch' => 'Batch 08'
  ],
  'Level 3 — Tahfidhul Qur\'an & Advanced Hadith' => [
    'title' => 'Level 3 — Tahfidhul Qur\'an & Advanced Hadith',
    'track' => 'Advanced Studies',
    'desc'  => 'Systematic Qur\'an memorization, 40 Hadith of Imam Nawawi, and Tafsir fundamentals.',
    'batch' => 'Batch 08'
  ],
  'Special Class — Revert Support & Essentials' => [
    'title' => 'Special Class — Revert Support & Essentials',
    'track' => 'New Muslim Track',
    'desc'  => 'Nurturing foundational Islamic knowledge, prayer workshops, and sisterhood support.',
    'batch' => 'Batch 08'
  ]
];

$coursesMap = [];
foreach ($defaultPrograms as $k => $info) {
  $coursesMap[$k] = [
    'title'    => $info['title'],
    'track'    => $info['track'],
    'desc'     => $info['desc'],
    'batch'    => $info['batch'],
    'enrolled' => 0,
    'pending'  => 0,
    'total'    => 0,
  ];
}

$totalEnrolledAll = 0;
$totalPendingAll = 0;

foreach ($records as $r) {
  $rawCourse = trim($r['level'] ?? '');
  if (empty($rawCourse)) $rawCourse = trim($r['program_name'] ?? '');
  
  $matchedKey = null;
  $rcLower = strtolower($rawCourse);
  if (strpos($rcLower, 'special') !== false || strpos($rcLower, 'revert') !== false) {
    $matchedKey = 'Special Class — Revert Support & Essentials';
  } elseif (strpos($rcLower, 'level 3') !== false || strpos($rcLower, 'tahfidh') !== false) {
    $matchedKey = 'Level 3 — Tahfidhul Qur\'an & Advanced Hadith';
  } elseif (strpos($rcLower, 'level 2') !== false || strpos($rcLower, 'intermediate') !== false || strpos($rcLower, 'fiqh') !== false) {
    $matchedKey = 'Level 2 — Intermediate Islamic Studies & Fiqh';
  } elseif (strpos($rcLower, 'level 1') !== false || strpos($rcLower, 'basic') !== false || strpos($rcLower, 'tajweed') !== false) {
    $matchedKey = 'Level 1 — Basic Arabic & Tajweed';
  }

  $title = $matchedKey ?: ($rawCourse ?: 'Level 1 — Basic Arabic & Tajweed');
  $st = strtolower($r['status'] ?? '');

  if (!isset($coursesMap[$title])) {
    $coursesMap[$title] = [
      'title'    => $title,
      'track'    => 'Academic Track',
      'desc'     => 'Official ISCAG Female Da\'wah Program',
      'batch'    => trim($r['batch_no'] ?? 'Batch 08'),
      'enrolled' => 0,
      'pending'  => 0,
      'total'    => 0,
    ];
  }

  $coursesMap[$title]['total']++;
  if (in_array($st, ['active', 'accepted', 'approved', 'enrolled'])) {
    $coursesMap[$title]['enrolled']++;
    $totalEnrolledAll++;
  } elseif (in_array($st, ['pending', 'under_review'])) {
    $coursesMap[$title]['pending']++;
    $totalPendingAll++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Enrolled Courses &amp; Programs</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary-female: #176b45;
      --primary-female-dark: #0f5c3a;
      --primary-female-light: #f0fdf4;
      --accent-gold: #c79a2b;
      --accent-gold-light: #fefce8;
    }

    /* ── KPI Ribbon ── */
    .edu-kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 18px;
      margin-bottom: 24px;
    }
    .edu-kpi-card {
      background: white;
      padding: 20px 22px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 6px rgba(0,0,0,0.03);
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      gap: 6px;
      cursor: pointer;
      transition: all 0.25s ease;
    }
    .edu-kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
      border-color: var(--primary-female);
    }
    .edu-kpi-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .edu-kpi-card.courses::after { background: var(--primary-female); }
    .edu-kpi-card.enrolled::after { background: #166534; }
    .edu-kpi-card.pending::after { background: #d97706; }
    .edu-kpi-card.total::after { background: #0284c7; }

    .edu-kpi-label {
      font-size: 0.72rem;
      font-weight: 800;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .edu-kpi-value {
      font-size: 1.85rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1.1;
      font-family: 'Lora', serif;
    }
    .edu-kpi-sub {
      font-size: 0.74rem;
      font-weight: 600;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* ── Table Typography & Badges ── */
    .td-id { font-weight: 800; color: var(--primary-female); font-size: 0.85rem; }

    .course-table-row {
      transition: background 0.2s ease;
      cursor: pointer;
    }
    .course-table-row:hover td {
      background: #f0fdf4 !important;
    }

    .course-track-badge {
      font-size: 0.7rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 3px 10px;
      border-radius: 12px;
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
      white-space: nowrap;
    }

    .badge-status.badge-active {
      background: #dcfce7 !important;
      color: #166534 !important;
      border: 1px solid #bbf7d0;
    }
    .badge-status.badge-pending {
      background: #fef3c7 !important;
      color: #b45309 !important;
      border: 1px solid #fde68a;
    }

    .course-open-btn {
      font-size: 0.76rem;
      font-weight: 800;
      color: #ffffff;
      background: var(--primary-female);
      border: none;
      padding: 7px 14px;
      border-radius: 8px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      transition: all 0.2s;
    }
    .course-open-btn:hover {
      background: var(--primary-female-dark);
      transform: translateY(-1px);
    }

    /* ── Search & Filter ── */
    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 20px;
      background: #fafaf9;
      border-bottom: 1px solid var(--border);
      gap: 14px;
      flex-wrap: wrap;
    }
    .search-box-wrap {
      position: relative;
      flex: 1;
      min-width: 240px;
      max-width: 380px;
    }
    .search-box-wrap input {
      width: 100%;
      padding: 8px 14px 8px 36px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      font-size: 0.85rem;
      font-weight: 600;
      outline: none;
      transition: border-color 0.2s;
      box-sizing: border-box;
    }
    .search-box-wrap input:focus {
      border-color: var(--primary-female);
    }
    .search-box-wrap svg {
      position: absolute;
      left: 11px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
      pointer-events: none;
    }

    /* ── Roster Modal ── */
    .roster-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 18px;
      border: 1px solid var(--border);
      border-radius: 12px;
      margin-bottom: 10px;
      background: #ffffff;
      transition: all 0.2s ease;
      gap: 12px;
    }
    .roster-item:hover {
      border-color: var(--primary-female);
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.06);
    }
    .roster-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .roster-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: var(--primary-female);
      color: white;
      font-weight: 800;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .roster-name {
      font-weight: 800;
      color: #0f172a;
      font-size: 0.92rem;
    }
    .roster-sub {
      font-size: 0.76rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-top: 2px;
    }

    /* ── Modal Overlay & Student Modal ── */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 30, 22, 0.65);
      backdrop-filter: blur(4px);
      z-index: 99999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .student-modal-card {
      background: #ffffff;
      border-radius: 16px;
      width: 100%;
      max-width: 620px;
      box-shadow: 0 24px 60px rgba(0,0,0,0.3);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'enrolled_courses';
      $dawah_type = $dawah_type ?? 'female';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <!-- ═══ TOP BAR ═══ -->
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Enrolled Courses &amp; Academic Programs</div>
          <div class="top-bar-subtitle">Female Da'wah Department — Course directory and enrolled student rosters</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <a href="<?= url('/admin/dawah/education') ?>" class="btn-topbar" style="text-decoration:none;">
             <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
             Education Records
           </a>
           <a href="<?= url('/admin/dawah/education/requests?type=female') ?>" class="btn-topbar primary" style="text-decoration:none;">
             <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2.5;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
             Review Requests
           </a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/female') ?>">Female Da'wah</a>
          <span class="sep">›</span>
          <span>Islamic Education</span>
          <span class="sep">›</span>
          <span class="current">Enrolled Courses</span>
        </div>

        <!-- ═══ KPI INSIGHTS RIBBON ═══ -->
        <div class="edu-kpi-grid">
          <div class="edu-kpi-card courses">
            <span class="edu-kpi-label">Available Courses</span>
            <span class="edu-kpi-value"><?= count($coursesMap) ?></span>
            <span class="edu-kpi-sub">Curriculum tracks</span>
          </div>

          <div class="edu-kpi-card enrolled">
            <span class="edu-kpi-label">Enrolled Students</span>
            <span class="edu-kpi-value" style="color:#166534;"><?= $totalEnrolledAll ?></span>
            <span class="edu-kpi-sub" style="color:#166534;">
              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              Active students enrolled
            </span>
          </div>

          <div class="edu-kpi-card pending">
            <span class="edu-kpi-label">Pending Verification</span>
            <span class="edu-kpi-value" style="color:#d97706;"><?= $totalPendingAll ?></span>
            <span class="edu-kpi-sub" style="color:#d97706;">Awaiting approval</span>
          </div>

          <div class="edu-kpi-card total">
            <span class="edu-kpi-label">Total Applications</span>
            <span class="edu-kpi-value" style="color:#0284c7;"><?= count($records) ?></span>
            <span class="edu-kpi-sub">Lifetime registered</span>
          </div>
        </div>

        <!-- ═══ ENROLLED COURSES TABLE CARD ═══ -->
        <div class="section-card" id="enrolled-courses-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary-female);margin-right:8px;"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
              Enrolled Courses &amp; Academic Programs
            </h6>
            <div style="font-size:0.75rem; color:var(--text-muted); font-weight:700; text-transform:uppercase;">
              Batch 08 Academic Year
            </div>
          </div>

          <!-- Filter & Search Bar -->
          <div class="filter-bar">
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted);">
              Click any course row or the <strong>View Students</strong> button to see all students enrolled in that course.
            </div>
            <div class="search-box-wrap">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" id="courseSearchInput" placeholder="Search course title or track..." onkeyup="filterCoursesTable()">
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table" id="courses-table">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Course &amp; Academic Program</th>
                    <th>Track / Category</th>
                    <th>Batch</th>
                    <th>Enrolled</th>
                    <th>Pending</th>
                    <th>Tuition Fee</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="courses-tbody">
                  <?php $cIdx = 1; foreach ($coursesMap as $cTitle => $c): ?>
                    <tr class="course-table-row" data-course="<?= htmlspecialchars($cTitle, ENT_QUOTES) ?>" onclick="openCourseRosterModal(this.getAttribute('data-course'))">
                      <td class="td-id">#CRS-0<?= $cIdx++ ?></td>
                      <td>
                        <div style="font-weight:800; color:#0f172a; font-size:0.92rem;"><?= htmlspecialchars($c['title']) ?></div>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px; font-weight:500;"><?= htmlspecialchars($c['desc']) ?></div>
                      </td>
                      <td>
                        <span class="course-track-badge">
                          <?= htmlspecialchars($c['track']) ?>
                        </span>
                      </td>
                      <td>
                        <span style="font-weight:700; color:#475569; font-size:0.8rem;"><?= htmlspecialchars($c['batch']) ?></span>
                      </td>
                      <td>
                        <span class="badge-status badge-active" style="font-weight:800; white-space:nowrap;">
                          <span class="status-dot"></span> <?= $c['enrolled'] ?> Enrolled
                        </span>
                      </td>
                      <td>
                        <?php if ($c['pending'] > 0): ?>
                          <span class="badge-status badge-pending" style="font-weight:800; white-space:nowrap;">
                            <span class="status-dot"></span> <?= $c['pending'] ?> Pending
                          </span>
                        <?php else: ?>
                          <span style="font-size:0.78rem; color:#9ca3af; font-weight:600;">0 Pending</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span style="font-weight:800; color:#166534; font-size:0.85rem;">₱ 120.00</span>
                      </td>
                      <td onclick="event.stopPropagation();">
                        <div class="actions-cell" style="display:flex; align-items:center; gap:8px;">
                          <button type="button" class="course-open-btn" onclick="openCourseRosterModal(this.closest('tr').getAttribute('data-course'))" title="View Enrolled Students">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            View Students (<?= $c['enrolled'] + $c['pending'] ?>)
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🎓 ENROLLED STUDENTS ROSTER MODAL                          -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="course-roster-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:16px; width:100%; max-width:820px; box-shadow:0 24px 60px rgba(0,0,0,0.3); overflow:hidden; display:flex; flex-direction:column; max-height:90vh;">
      <div style="height:5px; background:linear-gradient(90deg, #176b45, #d4af37);"></div>
      
      <!-- Modal Header -->
      <div style="padding:20px 26px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafaf9;">
        <div>
          <div style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:var(--primary-female); letter-spacing:0.04em;" id="roster-track-badge">
            Academic Track
          </div>
          <h4 id="roster-modal-course-title" style="margin:2px 0 0; font-family:'Lora',serif; color:#0f5c3a; font-size:1.25rem; font-weight:800;">
            Course Name
          </h4>
        </div>
        <button type="button" onclick="closeCourseRosterModal()" style="background:none; border:none; font-size:26px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <!-- Quick Stats Ribbon in Modal -->
      <div style="padding:14px 26px; background:#f0fdf4; border-bottom:1px solid #bbf7d0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; gap:20px; align-items:center;">
          <div style="font-size:0.82rem; font-weight:700; color:#166534;">
            Enrolled: <strong id="roster-enrolled-count" style="font-size:1.1rem; color:#14532d;">0</strong>
          </div>
          <div style="font-size:0.82rem; font-weight:700; color:#b45309;">
            Pending: <strong id="roster-pending-count" style="font-size:1.1rem; color:#92400e;">0</strong>
          </div>
          <div style="font-size:0.82rem; font-weight:700; color:#475569;">
            Total: <strong id="roster-total-count" style="font-size:1.1rem; color:#0f172a;">0</strong>
          </div>
        </div>

        <div style="display:flex; gap:8px; align-items:center;">
          <div class="search-box-wrap" style="max-width:240px;">
            <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="rosterSearchInput" placeholder="Filter student..." onkeyup="filterRosterList()">
          </div>
        </div>
      </div>

      <!-- Sub Tabs (All, Enrolled, Pending) -->
      <div style="display:flex; gap:10px; padding:10px 26px; border-bottom:1px solid #e5e7eb; background:#ffffff;">
        <button type="button" class="tab-btn active" id="roster-tab-all" onclick="setRosterTab('all', this)" style="background:none; border:none; padding:6px 14px; border-radius:6px; font-weight:800; font-size:0.76rem; cursor:pointer; color:var(--primary-female);">
          ALL (<span id="count-roster-tab-all">0</span>)
        </button>
        <button type="button" class="tab-btn" id="roster-tab-enrolled" onclick="setRosterTab('enrolled', this)" style="background:none; border:none; padding:6px 14px; border-radius:6px; font-weight:800; font-size:0.76rem; cursor:pointer; color:var(--text-muted);">
          ENROLLED (<span id="count-roster-tab-enrolled">0</span>)
        </button>
        <button type="button" class="tab-btn" id="roster-tab-pending" onclick="setRosterTab('pending', this)" style="background:none; border:none; padding:6px 14px; border-radius:6px; font-weight:800; font-size:0.76rem; cursor:pointer; color:var(--text-muted);">
          PENDING (<span id="count-roster-tab-pending">0</span>)
        </button>
      </div>

      <!-- Roster Items Container -->
      <div id="roster-students-container" style="padding:18px 26px; overflow-y:auto; flex:1;">
        <!-- Dynamically filled by JS -->
      </div>

      <!-- Modal Footer -->
      <div style="padding:14px 26px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafaf9;">
        <button type="button" onclick="closeCourseRosterModal()" style="padding:8px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🔍 STUDENT PROFILE & DETAILS MODAL                         -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div class="modal-overlay" id="student-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;" onclick="handleModalOverlayClick(event)">
    <div class="student-modal-card" style="max-width:620px;">
      <div class="student-modal-header" style="background:#0f5c3a; color:white; padding:18px 24px; border-radius:14px 14px 0 0; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h4 id="m-student-name" style="margin:0; font-family:'Lora',serif; font-size:1.15rem; font-weight:700;">Student Name</h4>
          <p id="m-ref-sub" style="margin:3px 0 0; font-size:0.76rem; opacity:0.85;">Student No.: #EDU-0000 • Batch 08</p>
        </div>
        <button type="button" onclick="closeStudentModal()" style="background:none; border:none; color:white; font-size:22px; cursor:pointer;">&times;</button>
      </div>

      <div class="student-modal-body" style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <!-- Program Banner -->
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:0.7rem; font-weight:800; color:#166534; text-transform:uppercase;">Enrolled Program</div>
            <div id="m-program-name" style="font-weight:700; color:#0f5c3a; font-size:0.95rem; margin-top:2px;">—</div>
          </div>
          <div id="m-status-badge">
            <span class="badge-status badge-active">Active</span>
          </div>
        </div>

        <div class="detail-section-title" style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Personal Information</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Full Legal Name</div>
            <div id="m-fullname" style="font-weight:700; color:#111827; font-size:0.9rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Muslim Name</div>
            <div id="m-muslimname" style="font-weight:700; color:var(--primary-female); font-size:0.9rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Age &amp; Birthdate</div>
            <div id="m-age-dob" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Muslim Background</div>
            <div id="m-muslim-type" style="font-weight:700; color:#111827; font-size:0.85rem;">—</div>
          </div>
        </div>

        <div class="detail-section-title" style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Contact &amp; Emergency</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Contact Number</div>
            <div id="m-contact" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Facebook Account</div>
            <div id="m-facebook" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
          <div style="grid-column: span 2;">
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Complete Address</div>
            <div id="m-address" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Emergency Contact Person</div>
            <div id="m-em-person" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Emergency Tel / Relation</div>
            <div id="m-em-contact" style="font-weight:600; color:#374151; font-size:0.85rem;">—</div>
          </div>
        </div>

        <div class="detail-section-title" style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; border-bottom:1px solid #e5e7eb; padding-bottom:4px;">Payment &amp; Settlement</div>
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:700;">Payment Mode &amp; Amount</div>
            <div id="m-payment-mode" style="font-weight:700; color:#0f172a; font-size:0.88rem; margin-top:2px;">Cash</div>
            <div id="m-channel-ref" style="font-size:0.75rem; color:var(--text-muted); margin-top:1px;">Direct Cash</div>
          </div>
          <div id="m-fee-status" style="font-weight:800; font-size:0.92rem; color:#166534;">
            ₱120.00 • Paid
          </div>
        </div>
      </div>

      <div class="student-modal-footer" style="padding:14px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafaf9;">
        <div id="m-quick-actions"></div>
        <button type="button" class="btn-topbar" onclick="closeStudentModal()" style="border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; padding:6px 16px; border-radius:6px; cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const allEnrollmentRecords = <?= json_encode(array_values($records ?? [])) ?>;
    const coursesSummaryData = <?= json_encode($coursesMap ?? []) ?>;

    let activeRosterCourse = null;
    let studentsInCourse = [];
    let activeRosterTab = 'all';

    // ── Courses Table Filter ──
    function filterCoursesTable() {
      const q = (document.getElementById('courseSearchInput')?.value || '').toLowerCase().trim();
      const rows = document.querySelectorAll('.course-table-row');
      rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
      });
    }

    // ── Course Roster Modal Controller ──
    function openCourseRosterModal(courseTitle) {
      activeRosterCourse = courseTitle;
      const modal = document.getElementById('course-roster-modal');
      if (!modal) return;

      const cData = coursesSummaryData[courseTitle] || {
        title: courseTitle,
        track: 'Academic Track',
        desc: 'ISCAG Da\'wah Program'
      };

      document.getElementById('roster-modal-course-title').textContent = cData.title;
      document.getElementById('roster-track-badge').textContent = cData.track;

      // Filter all students registered for this course
      studentsInCourse = allEnrollmentRecords.filter(r => {
        const raw = (r.level || r.program_name || '').trim().toLowerCase();
        const target = courseTitle.trim().toLowerCase();

        if (target.includes('special') || target.includes('revert')) {
          return raw.includes('special') || raw.includes('revert');
        } else if (target.includes('level 3') || target.includes('tahfidh')) {
          return raw.includes('level 3') || raw.includes('tahfidh');
        } else if (target.includes('level 2') || target.includes('intermediate') || target.includes('fiqh')) {
          return raw.includes('level 2') || raw.includes('intermediate') || raw.includes('fiqh');
        } else if (target.includes('level 1') || target.includes('basic') || target.includes('tajweed')) {
          return raw.includes('level 1') || raw.includes('basic') || raw.includes('tajweed') || (!raw.includes('level 2') && !raw.includes('level 3') && !raw.includes('special'));
        }
        return raw === target || target.includes(raw);
      });

      setRosterTab('all', document.getElementById('roster-tab-all'));
      modal.style.display = 'flex';
    }

    function closeCourseRosterModal() {
      const modal = document.getElementById('course-roster-modal');
      if (modal) modal.style.display = 'none';
    }

    document.getElementById('course-roster-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeCourseRosterModal();
    });

    function setRosterTab(tab, btn) {
      activeRosterTab = tab;
      document.querySelectorAll('#course-roster-modal .tab-btn').forEach(b => {
        b.style.color = 'var(--text-muted)';
        b.style.borderBottom = 'none';
      });
      if (btn) {
        btn.style.color = 'var(--primary-female)';
      }
      renderRosterList();
    }

    function filterRosterList() {
      renderRosterList();
    }

    function renderRosterList() {
      const container = document.getElementById('roster-students-container');
      const query = (document.getElementById('rosterSearchInput')?.value || '').toLowerCase().trim();
      if (!container) return;

      let enrolledCount = 0;
      let pendingCount = 0;

      studentsInCourse.forEach(r => {
        const st = (r.status || '').toLowerCase();
        if (['active', 'accepted', 'approved', 'enrolled'].includes(st)) {
          enrolledCount++;
        } else if (['pending', 'under_review'].includes(st)) {
          pendingCount++;
        }
      });

      document.getElementById('roster-enrolled-count').textContent = enrolledCount;
      document.getElementById('roster-pending-count').textContent = pendingCount;
      document.getElementById('roster-total-count').textContent = studentsInCourse.length;

      document.getElementById('count-roster-tab-all').textContent = studentsInCourse.length;
      document.getElementById('count-roster-tab-enrolled').textContent = enrolledCount;
      document.getElementById('count-roster-tab-pending').textContent = pendingCount;

      const filtered = studentsInCourse.filter(r => {
        const st = (r.status || '').toLowerCase();
        const isEnrolled = ['active', 'accepted', 'approved', 'enrolled'].includes(st);
        const isPending = ['pending', 'under_review'].includes(st);

        let tabMatch = true;
        if (activeRosterTab === 'enrolled') tabMatch = isEnrolled;
        else if (activeRosterTab === 'pending') tabMatch = isPending;

        let searchMatch = true;
        if (query) {
          const studentFull = `${r.first_name || ''} ${r.last_name || ''} ${r.muslim_name || ''} ${r.contact_number || ''} #EDU-${r.id}`.toLowerCase();
          searchMatch = studentFull.includes(query);
        }

        return tabMatch && searchMatch;
      });

      if (filtered.length === 0) {
        container.innerHTML = `
          <div style="text-align:center; padding:45px 20px; color:var(--text-muted); background:#f8fafc; border-radius:12px; border:1px dashed var(--border);">
            <div style="font-size:2.4rem; margin-bottom:8px;">📚</div>
            <div style="font-size:1rem; font-weight:800; color:#0f172a;">No Students Found</div>
            <div style="font-size:0.8rem; margin-top:4px;">No students enrolled in this course matching the current filter.</div>
          </div>
        `;
        return;
      }

      let html = '';
      filtered.forEach(r => {
        const fullName = `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + r.id;
        const initials = ((r.first_name || 'S')[0] + (r.last_name || '')[0]).toUpperCase();
        const muslimName = r.muslim_name ? ` • Muslim Name: <strong style="color:var(--primary-female);">${escapeHtml(r.muslim_name)}</strong>` : '';
        const age = r.age ? `${r.age} y/o` : '';
        const contact = r.contact_number ? ` • Tel: ${escapeHtml(r.contact_number)}` : '';
        const dateSub = r.created_at ? new Date(r.created_at).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
        const feeStatus = r.payment_status || 'Pending';
        const payMethod = r.payment_method || 'Cash';
        const refNo = '#EDU-' + String(r.id).padStart(4, '0');

        const st = (r.status || 'pending').toLowerCase();
        let badgeHtml = '<span class="badge-status badge-pending">Pending Review</span>';
        if (['active', 'accepted', 'approved', 'enrolled'].includes(st)) {
          badgeHtml = '<span class="badge-status badge-active">Enrolled / Accepted</span>';
        } else if (['completed', 'paid'].includes(st)) {
          badgeHtml = '<span class="badge-status badge-complete">Completed</span>';
        } else if (['dropped', 'rejected'].includes(st)) {
          badgeHtml = '<span class="badge-status badge-rejected">Rejected</span>';
        }

        html += `
          <div class="roster-item">
            <div class="roster-left">
              <div class="roster-avatar">${initials}</div>
              <div>
                <div class="roster-name">
                  ${escapeHtml(fullName)}
                  <span style="font-family:'Courier New', monospace; font-size:0.75rem; font-weight:800; color:var(--primary-female); background:rgba(23,107,69,0.08); padding:2px 7px; border-radius:5px; margin-left:6px; border:1px solid rgba(23,107,69,0.25);">Student No.: ${refNo}</span>
                </div>
                <div class="roster-sub">
                  <span>${age}${contact}${muslimName}</span>
                </div>
                <div style="font-size:0.72rem; color:var(--text-muted); margin-top:3px;">
                  Enrolled: <strong>${dateSub}</strong> • Progress: <strong style="color:var(--primary-female);">${r.progress_percent || 0}%</strong> (${r.sessions_attended || 0}/${r.total_sessions || 12} Attended, ${r.sessions_completed || 0}/${r.total_sessions || 12} Completed) • Payment: <strong style="color:${feeStatus.toLowerCase() === 'paid' ? '#166534' : '#b45309'};">${escapeHtml(payMethod)} (${escapeHtml(feeStatus)})</strong>
                </div>
              </div>
            </div>

            <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
              <div>${badgeHtml}</div>
              ${feeStatus.toLowerCase() !== 'paid' ? `
                <button type="button" onclick="event.stopPropagation(); markStudentPaidFromModal(${r.id})" style="padding:6px 10px; font-size:0.75rem; background:#fffbeb; color:#b45309; border:1.5px solid #fcd34d; border-radius:8px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Mark as Paid">
                  <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                  Mark as Paid
                </button>
              ` : ''}
              <button type="button" class="btn-topbar primary" onclick="openStudentDetailsModalById(${r.id})" style="padding:6px 14px; font-size:0.78rem; border-radius:8px; font-weight:700;">
                View Profile
              </button>
            </div>
          </div>
        `;
      });

      container.innerHTML = html;
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

    // ── Student Modal Details ──
    function openStudentDetailsModalById(id) {
      const r = allEnrollmentRecords.find(x => Number(x.id) === Number(id));
      if (r) openStudentDetailsModal(r);
    }

    function openStudentDetailsModal(r) {
      if (!r) return;
      const refCode = '#EDU-' + String(r.id).padStart(4, '0');
      const fullName = `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + r.id;
      const muslimName = r.muslim_name || '—';
      const ageDob = (r.age ? `${r.age} yrs old` : 'Age N/A') + (r.birthdate ? ` • Born: ${r.birthdate}` : '');
      const prog = r.level || r.program_name || 'Islamic Education';
      const batch = r.batch_no || 'Batch 08';

      document.getElementById('m-student-name').textContent = fullName;
      document.getElementById('m-ref-sub').textContent = `Student No.: ${refCode} • ${batch}`;
      document.getElementById('m-program-name').textContent = prog;
      document.getElementById('m-fullname').textContent = fullName;
      document.getElementById('m-muslimname').textContent = muslimName;
      document.getElementById('m-age-dob').textContent = ageDob;
      document.getElementById('m-muslim-type').textContent = r.muslim_type || 'Revert';
      document.getElementById('m-contact').textContent = r.contact_number || '—';
      document.getElementById('m-facebook').textContent = r.facebook_account || '—';
      document.getElementById('m-address').textContent = r.address || '—';
      document.getElementById('m-em-person').textContent = r.emergency_name || '—';
      document.getElementById('m-em-contact').textContent = `${r.emergency_relationship || 'Contact'}: ${r.emergency_contact || '—'}`;
      document.getElementById('m-payment-mode').textContent = r.payment_method || 'Cash';
      const isFeePaid = (r.payment_status || '').toLowerCase() === 'paid';
      const feeStatusText = isFeePaid ? 'Paid (Verified)' : (r.payment_status || 'Pending');
      const feeColor = isFeePaid ? '#166534' : '#b45309';
      document.getElementById('m-fee-status').innerHTML = `₱${parseFloat(r.fee_amount || 120).toFixed(2)} • <strong style="color:${feeColor};">${escapeHtml(feeStatusText)}</strong>`;
      document.getElementById('m-channel-ref').textContent = (r.payment_channel || 'Direct Cash') + (r.payment_reference ? ` • Ref: ${r.payment_reference}` : '');

      const stLower = (r.status || 'pending').toLowerCase();
      let badgeHtml = '<span class="badge-status badge-pending">Pending Review</span>';
      if (['active', 'accepted', 'approved', 'enrolled'].includes(stLower)) {
        badgeHtml = '<span class="badge-status badge-active">Enrolled / Accepted</span>';
      } else if (['completed', 'paid'].includes(stLower)) {
        badgeHtml = '<span class="badge-status badge-complete">Completed</span>';
      } else if (['dropped', 'rejected'].includes(stLower)) {
        badgeHtml = '<span class="badge-status badge-rejected">Rejected</span>';
      }
      document.getElementById('m-status-badge').innerHTML = badgeHtml;

      const actionsEl = document.getElementById('m-quick-actions');
      let actionsHtml = '';
      if (!isFeePaid) {
        actionsHtml += `
          <button type="button" class="btn-topbar" onclick="markStudentPaidFromModal(${r.id})" style="background:#d97706; color:white; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
            Mark as Paid (₱120.00)
          </button>
        `;
      }
      actionsEl.innerHTML = actionsHtml;

      document.getElementById('student-modal').style.display = 'flex';
    }

    function closeStudentModal() {
      document.getElementById('student-modal').style.display = 'none';
    }

    function handleModalOverlayClick(e) {
      if (e.target.id === 'student-modal') closeStudentModal();
    }

    async function markStudentPaidFromModal(id) {
      if (!confirm(`Mark registration fee (₱120.00) as PAID for enrollment #EDU-${String(id).padStart(4, '0')}?`)) return;
      try {
        const res = await fetch('<?= url("/admin/dawah/education/mark-paid") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
          alert('Registration fee marked as Paid successfully!');
          window.location.reload();
        } else {
          alert(data.message || 'Error updating payment.');
        }
      } catch (err) {
        alert('Connection error occurred.');
      }
    }

    // Auto-open course roster if specified in URL query
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const targetCourse = urlParams.get('course');
      if (targetCourse) {
        openCourseRosterModal(targetCourse);
      }
    });
  </script>
</body>
</html>
