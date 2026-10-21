<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Female Islamic Education</title>
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
    .edu-kpi-card.total::after { background: var(--primary-female); }
    .edu-kpi-card.enrolled::after { background: #166534; }
    .edu-kpi-card.pending::after { background: #d97706; }
    .edu-kpi-card.courses::after { background: #0284c7; }

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

    /* ── Courses Grid ── */
    .courses-section {
      margin-bottom: 28px;
    }
    .courses-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 14px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .courses-header-title {
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
      gap: 16px;
    }
    .course-card {
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: 14px;
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.25s ease;
      cursor: pointer;
      position: relative;
    }
    .course-card:hover {
      border-color: var(--primary-female);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(23, 107, 69, 0.1);
    }
    .course-card.active-course-filter {
      border-color: var(--primary-female);
      background: #fbfdfc;
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.12);
    }
    .course-table-row {
      transition: background 0.2s ease;
    }
    .course-table-row:hover td {
      background: #f0fdf4 !important;
    }
    .course-table-row.active-course-filter td {
      background: #ecfdf5 !important;
    }
    .course-top-row {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
    }
    .course-track-badge {
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 3px 8px;
      border-radius: 6px;
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .course-title {
      font-size: 0.98rem;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.35;
      margin-bottom: 4px;
    }
    .course-meta {
      font-size: 0.76rem;
      color: var(--text-muted);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .course-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px dashed var(--border);
      gap: 8px;
    }
    .course-stats-tag {
      font-size: 0.78rem;
      font-weight: 700;
      color: #0f5c3a;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .course-open-btn {
      font-size: 0.74rem;
      font-weight: 800;
      color: #ffffff;
      background: var(--primary-female);
      border: none;
      padding: 6px 12px;
      border-radius: 8px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      transition: all 0.2s;
    }
    .course-open-btn:hover {
      background: var(--primary-female-dark);
      transform: translateY(-1px);
    }

    /* ── Tab & Filter Styles ── */
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
    .badge-status.badge-complete {
      background: #e0f2fe !important;
      color: #0369a1 !important;
      border: 1px solid #bae6fd;
    }
    .badge-status.badge-rejected {
      background: #fee2e2 !important;
      color: #b91c1c !important;
      border: 1px solid #fecaca;
    }
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

    /* ── Modal Common Styles ── */
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
      max-width: 640px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      border: 1px solid var(--border);
    }
    .modal-card.wide-modal {
      max-width: 820px;
    }
    .modal-header {
      background: #0f5c3a;
      color: #ffffff;
      padding: 20px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 2;
    }
    .modal-body {
      padding: 24px;
    }
    .modal-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .modal-field-item {
      background: #f8fafc;
      padding: 10px 14px;
      border-radius: 8px;
      border: 1px solid #e2e8f0;
    }
    .modal-field-label {
      font-size: 0.68rem;
      font-weight: 800;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .modal-field-value {
      font-size: 0.88rem;
      font-weight: 700;
      color: #0f172a;
      margin-top: 2px;
    }

    /* ── Course Roster List Items ── */
    .roster-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 14px;
    }
    .roster-item {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.2s ease;
      gap: 14px;
    }
    .roster-item:hover {
      border-color: var(--primary-female);
      background: #fbfdfc;
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.06);
    }
    .roster-left {
      display: flex;
      align-items: center;
      gap: 14px;
      flex: 1;
      min-width: 0;
    }
    .roster-avatar {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: var(--primary-female-light);
      color: var(--primary-female-dark);
      border: 1.5px solid #bbf7d0;
      font-weight: 800;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .roster-name {
      font-size: 0.92rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .roster-sub {
      font-size: 0.76rem;
      color: var(--text-muted);
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'education';
      $dawah_type = 'female';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <!-- ═══ TOP BAR ═══ -->
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Islamic Education Records</div>
          <div class="top-bar-subtitle">Female Da'wah Department — Manage enrolled student records, status, and verification</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <a href="<?= url('/admin/dawah/education/courses') ?>" class="btn-topbar" style="text-decoration:none;">
             <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
             Enrolled Courses
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
          <span class="current">Education Records</span>
        </div>

        <?php
          // Normalize records & status mapping
          $totalCount = count($records ?? []);
          $enrolledRecords = array_values(array_filter($records ?? [], function($r) {
            $st = strtolower($r['status'] ?? '');
            return in_array($st, ['active', 'accepted', 'approved', 'enrolled']);
          }));
          $pendingRecords = array_values(array_filter($records ?? [], function($r) {
            $st = strtolower($r['status'] ?? '');
            return in_array($st, ['pending', 'under_review']);
          }));
          $completedRecords = array_values(array_filter($records ?? [], function($r) {
            $st = strtolower($r['status'] ?? '');
            return in_array($st, ['completed', 'paid', 'graduated']);
          }));

          // Compute courses summary dynamically
          $coursesMap = [];
          
          // Seed standard Da'wah Female programs
          $standardPrograms = [
            'Level 1 — Basic Arabic & Tajweed' => ['track' => 'Basic', 'desc' => 'Arabic alphabets, pronunciation, basic Tajweed rules'],
            'Level 2 — Intermediate Islamic Studies & Fiqh' => ['track' => 'Intermediate', 'desc' => 'Fiqh of worship, Sirah, and hadith fundamentals'],
            'Level 3 — Tahfidhul Qur\'an & Advanced Hadith' => ['track' => 'Advanced', 'desc' => 'Memorization, tafseer, and advanced Arabic'],
            'Special Class — Revert Support & Essentials' => ['track' => 'Revert Support', 'desc' => 'Foundations of Islam, prayer guides, and spiritual mentoring'],
            'Beginners Qur\'an (B4)' => ['track' => 'Foundational', 'desc' => 'Comprehensive Saturday basic Qur\'anic reading']
          ];

          foreach ($standardPrograms as $progTitle => $progMeta) {
            $coursesMap[$progTitle] = [
              'title' => $progTitle,
              'track' => $progMeta['track'],
              'desc' => $progMeta['desc'],
              'enrolled' => 0,
              'pending' => 0,
              'total' => 0,
              'batch' => 'Batch 08'
            ];
          }

          // Populate with actual student enrollments
          foreach ($records as $r) {
            $title = trim($r['level'] ?? '');
            if (empty($title)) $title = trim($r['program_name'] ?? '');
            if (empty($title)) $title = 'Islamic Education - Batch 08';

            $batch = trim($r['batch_no'] ?? 'Batch 08');
            $st = strtolower($r['status'] ?? '');

            if (!isset($coursesMap[$title])) {
              $coursesMap[$title] = [
                'title' => $title,
                'track' => 'Academic Track',
                'desc' => 'Official ISCAG Female Da\'wah Program',
                'enrolled' => 0,
                'pending' => 0,
                'total' => 0,
                'batch' => $batch
              ];
            }

            $coursesMap[$title]['total']++;
            if (in_array($st, ['active', 'accepted', 'approved', 'enrolled'])) {
              $coursesMap[$title]['enrolled']++;
            } elseif (in_array($st, ['pending', 'under_review'])) {
              $coursesMap[$title]['pending']++;
            }
          }
        ?>

        <!-- ═══ KPI INSIGHTS RIBBON ═══ -->
        <div class="edu-kpi-grid">
          <div class="edu-kpi-card total" onclick="applyTabFilter('All')">
            <span class="edu-kpi-label">Total Applications</span>
            <span class="edu-kpi-value"><?= $totalCount ?></span>
            <span class="edu-kpi-sub">All registered students</span>
          </div>

          <div class="edu-kpi-card enrolled" onclick="applyTabFilter('enrolled')">
            <span class="edu-kpi-label">Enrolled Students</span>
            <span class="edu-kpi-value" style="color:#166534;"><?= count($enrolledRecords) ?></span>
            <span class="edu-kpi-sub" style="color:#166534;">
              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              Accepted &amp; active
            </span>
          </div>

          <div class="edu-kpi-card pending" onclick="applyTabFilter('pending')">
            <span class="edu-kpi-label">Pending Verification</span>
            <span class="edu-kpi-value" style="color:#d97706;"><?= count($pendingRecords) ?></span>
            <span class="edu-kpi-sub" style="color:#d97706;">Awaiting approval</span>
          </div>

          <div class="edu-kpi-card courses" onclick="window.location.href='<?= url('/admin/dawah/education/courses') ?>'">
            <span class="edu-kpi-label">Available Programs</span>
            <span class="edu-kpi-value" style="color:#0284c7;"><?= count($coursesMap) ?></span>
            <span class="edu-kpi-sub">View Enrolled Courses &rarr;</span>
          </div>
        </div>

        <!-- ═══ STUDENT ENROLLMENT LIST CARD ═══ -->
        <div class="section-card" id="student-table-section">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
              Student Enrollment List (Female)
            </h6>
            <div class="filter-group" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
              <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Course / Track:</span>
              <select id="categoryFilter" class="form-select-sm" style="padding:6px 12px; border-radius:8px; border:1.5px solid var(--border); font-size:0.85rem; font-weight:700; color:var(--primary-dark); cursor:pointer;">
                <option value="All">All Courses &amp; Programs</option>
                <?php foreach ($coursesMap as $cTitle => $c): ?>
                  <option value="<?= htmlspecialchars($cTitle) ?>"><?= htmlspecialchars($cTitle) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <!-- Secondary Filter & Search Bar -->
            <div class="filter-bar">
              <div class="tabs-bar" style="display:flex; gap:10px; padding:0; background:transparent;">
                <button class="tab-btn active" data-status="All" style="background:none; border:none; padding:8px 14px; border-radius:8px; font-weight:800; font-size:0.78rem; cursor:pointer; color:var(--text-muted); transition:0.25s;">
                  ALL RECORDS (<?= $totalCount ?>)
                </button>
                <button class="tab-btn" data-status="enrolled" style="background:none; border:none; padding:8px 14px; border-radius:8px; font-weight:800; font-size:0.78rem; cursor:pointer; color:var(--text-muted); transition:0.25s;">
                  ENROLLED (<?= count($enrolledRecords) ?>)
                </button>
                <button class="tab-btn" data-status="pending" style="background:none; border:none; padding:8px 14px; border-radius:8px; font-weight:800; font-size:0.78rem; cursor:pointer; color:var(--text-muted); transition:0.25s;">
                  PENDING (<?= count($pendingRecords) ?>)
                </button>
                <button class="tab-btn" data-status="completed" style="background:none; border:none; padding:8px 14px; border-radius:8px; font-weight:800; font-size:0.78rem; cursor:pointer; color:var(--text-muted); transition:0.25s;">
                  COMPLETED / PAID (<?= count($completedRecords) ?>)
                </button>
              </div>

              <div class="search-box-wrap">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="searchInput" placeholder="Search student name, Ref #, Muslim name..." onkeyup="applyFilters()">
              </div>
            </div>

            <style>
              .tab-btn.active { background: white !important; color: var(--primary-female-dark) !important; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
              .tab-btn:hover:not(.active) { color: var(--accent); }
            </style>

            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Student No.</th>
                    <th>Student Name</th>
                    <th>Age</th>
                    <th>Enrolled Course / Track</th>
                    <th>Batch</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="enrollment-tbody">
                  <?php if (empty($records)): ?>
                    <tr><td colspan="9" style="text-align:center;padding:50px;color:var(--text-muted);">No student records found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($records as $r): 
                      $stRaw = strtolower(trim($r['status'] ?? 'pending'));
                      $isEnrolled = in_array($stRaw, ['active', 'accepted', 'approved', 'enrolled']);
                      $isPending = in_array($stRaw, ['pending', 'under_review']);
                      $isCompleted = in_array($stRaw, ['completed', 'paid', 'graduated']);
                      $isRejected = in_array($stRaw, ['dropped', 'rejected', 'cancelled']);

                      $sc = 'badge-pending';
                      $statusLabel = 'Pending';
                      if ($isEnrolled) {
                        $sc = 'badge-active';
                        $statusLabel = 'Enrolled';
                      } elseif ($isCompleted) {
                        $sc = 'badge-complete';
                        $statusLabel = 'Completed';
                      } elseif ($isRejected) {
                        $sc = 'badge-rejected';
                        $statusLabel = 'Rejected';
                      }

                      $courseDisplay = trim($r['level'] ?? '');
                      if (empty($courseDisplay)) $courseDisplay = trim($r['program_name'] ?? 'Islamic Education');

                      $rRawMethod = trim($r['payment_method'] ?? 'Cash');
                      $rChannel = trim($r['payment_channel'] ?? '');
                      $rIsOnline = (stripos($rRawMethod, 'online') !== false || stripos($rRawMethod, 'gcash') !== false || stripos($rRawMethod, 'maya') !== false);
                      $rDisplayMethod = $rIsOnline ? 'Online Payment' : 'Cash';
                      $rPaymentStatus = trim($r['payment_status'] ?? 'Pending');

                      $refCode = '#EDU-' . str_pad((string)$r['id'], 4, '0', STR_PAD_LEFT);
                      $studentFullName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                      $muslimName = trim($r['muslim_name'] ?? '');
                      $batch = trim($r['batch_no'] ?? 'Batch 08');

                      // Match category filters
                      $normalizedStatus = 'other';
                      if ($isEnrolled) $normalizedStatus = 'enrolled';
                      elseif ($isPending) $normalizedStatus = 'pending';
                      elseif ($isCompleted) $normalizedStatus = 'completed';
                      elseif ($isRejected) $normalizedStatus = 'rejected';

                      $jsonRecord = htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8');
                    ?>
                      <tr class="enrollment-row" 
                          data-program="<?= htmlspecialchars($courseDisplay) ?>"
                          data-raw-program="<?= htmlspecialchars($r['program_name'] ?? '') ?>"
                          data-status="<?= $normalizedStatus ?>"
                          data-search="<?= htmlspecialchars(strtolower($studentFullName . ' ' . $muslimName . ' ' . $refCode . ' ' . $courseDisplay)) ?>">
                        
                        <td class="td-id">
                          <span style="font-family:'Courier New', monospace; font-weight:800; color:var(--primary-female); background:rgba(23,107,69,0.08); padding:3px 8px; border-radius:6px; font-size:0.82rem; border:1px solid rgba(23,107,69,0.2); white-space:nowrap;">
                            <?= $refCode ?>
                          </span>
                        </td>
                        
                        <td>
                          <div style="font-weight:700; color:#0f172a; font-size:0.9rem;"><?= htmlspecialchars($studentFullName) ?></div>
                          <?php if (!empty($muslimName)): ?>
                            <div style="font-size:0.75rem; color:var(--primary-female); font-weight:700;">
                              Muslim Name: <?= htmlspecialchars($muslimName) ?>
                            </div>
                          <?php endif; ?>
                        </td>

                        <td>
                          <?php if (!empty($r['age'])): ?>
                            <span style="font-weight:700; color:var(--text-main);"><?= $r['age'] ?></span> <span style="font-size:0.7rem; color:var(--text-muted);">y/o</span>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                          <?php endif; ?>
                        </td>

                        <td class="td-program">
                          <span style="font-weight:700; color:#0f5c3a; display:block;"><?= htmlspecialchars($courseDisplay) ?></span>
                          <?php if (!empty($r['program_name']) && $r['program_name'] !== $courseDisplay): ?>
                            <span style="font-size:0.72rem; color:var(--text-muted);"><?= htmlspecialchars($r['program_name']) ?></span>
                          <?php endif; ?>
                        </td>

                        <td>
                          <span style="font-size:0.78rem; font-weight:700; color:var(--text-muted);"><?= htmlspecialchars($batch) ?></span>
                        </td>

                        <td>
                          <span style="font-weight:700; color:<?= $rIsOnline ? '#0f5c3a' : '#166534' ?>;"><?= htmlspecialchars($rDisplayMethod) ?></span>
                          <?php if ($rIsOnline && !empty($rChannel)): ?>
                            <div style="font-size:0.7rem; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($rChannel) ?></div>
                          <?php endif; ?>
                          <div style="font-size:0.68rem; font-weight:800; color:<?= strtolower($rPaymentStatus) === 'paid' ? '#166534' : '#b45309' ?>;">
                            <?= htmlspecialchars(ucfirst($rPaymentStatus)) ?>
                          </div>
                        </td>

                        <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>

                        <td>
                          <span class="badge-status <?= $sc ?>">
                            <?= $statusLabel ?>
                          </span>
                          <?php if ($isRejected && !empty($r['rejection_reason'])): ?>
                            <div style="font-size:0.72rem; color:#dc2626; font-weight:600; margin-top:3px; max-width:180px; line-height:1.2;">
                              <?= htmlspecialchars($r['rejection_reason']) ?>
                            </div>
                          <?php endif; ?>
                        </td>

                        <td>
                          <div class="actions-cell" style="display:flex; align-items:center; gap:6px;">
                            <button class="btn-action btn-view" onclick='openStudentDetailsModal(<?= $jsonRecord ?>)'>
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                              Manage
                            </button>
                            <button type="button" class="btn-action" onclick="openProgressModal(<?= (int)$r['id'] ?>)" style="background:#f0fdf4; color:#166534; border:1.5px solid #bbf7d0; font-weight:700; font-size:0.75rem; padding:5px 9px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="View &amp; Track 12-Session Progress">
                              <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                              Progress
                            </button>
                            <?php if (strtolower($rPaymentStatus) !== 'paid'): ?>
                              <button type="button" class="btn-action btn-mark-paid" onclick="markStudentPaidFromModal(<?= (int)$r['id'] ?>)" style="background:#fffbeb; color:#b45309; border:1.5px solid #fcd34d; font-weight:700; font-size:0.75rem; padding:5px 10px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;" title="Mark as Paid (₱120.00)">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                                Mark as Paid
                              </button>
                            <?php endif; ?>
                            <?php if ($isPending): ?>
                              <button type="button" class="btn-action" onclick="openEduRejectModal(<?= (int)$r['id'] ?>)" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-weight:700; font-size:0.75rem; padding:5px 8px; border-radius:6px; cursor:pointer;" title="Reject Request">
                                Reject
                              </button>
                            <?php endif; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🎓 COURSE ROSTER MODAL: WHO ARE THE STUDENTS ENROLLED       -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="course-roster-modal" class="modal-overlay" onclick="handleRosterModalOverlayClick(event)">
    <div class="modal-card wide-modal">
      <div class="modal-header">
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:42px; height:42px; border-radius:10px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:#ffffff;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
          </div>
          <div>
            <div style="font-size:1.15rem; font-weight:800; font-family:'Lora',serif;" id="roster-course-title">Course Title</div>
            <div style="font-size:0.75rem; color:rgba(255,255,255,0.85);" id="roster-course-subtitle">Female Da'wah Academic Track • Batch 08</div>
          </div>
        </div>
        <button onclick="closeCourseRosterModal()" style="background:none; border:none; color:white; font-size:1.6rem; cursor:pointer; line-height:1;">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Roster Insight Stats -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:18px;">
          <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 14px; text-align:center;">
            <div style="font-size:0.68rem; font-weight:800; color:#166534; text-transform:uppercase;">Enrolled Students</div>
            <div style="font-size:1.5rem; font-weight:800; color:#166534; font-family:'Lora',serif;" id="roster-enrolled-count">0</div>
          </div>
          <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 14px; text-align:center;">
            <div style="font-size:0.68rem; font-weight:800; color:#b45309; text-transform:uppercase;">Pending Verification</div>
            <div style="font-size:1.5rem; font-weight:800; color:#b45309; font-family:'Lora',serif;" id="roster-pending-count">0</div>
          </div>
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; text-align:center;">
            <div style="font-size:0.68rem; font-weight:800; color:var(--text-muted); text-transform:uppercase;">Total Applicants</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--primary-dark); font-family:'Lora',serif;" id="roster-total-count">0</div>
          </div>
        </div>

        <!-- Filter bar inside Roster Modal -->
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
          <div style="display:flex; gap:8px;">
            <button class="tab-btn active" id="roster-tab-all" onclick="filterRosterTab('all')" style="padding:6px 12px; font-size:0.75rem; border-radius:6px; font-weight:800;">
              All Students (<span id="count-roster-tab-all">0</span>)
            </button>
            <button class="tab-btn" id="roster-tab-enrolled" onclick="filterRosterTab('enrolled')" style="padding:6px 12px; font-size:0.75rem; border-radius:6px; font-weight:800;">
              Enrolled (<span id="count-roster-tab-enrolled">0</span>)
            </button>
            <button class="tab-btn" id="roster-tab-pending" onclick="filterRosterTab('pending')" style="padding:6px 12px; font-size:0.75rem; border-radius:6px; font-weight:800;">
              Pending (<span id="count-roster-tab-pending">0</span>)
            </button>
          </div>

          <div class="search-box-wrap" style="max-width:280px; min-width:200px;">
            <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="rosterSearchInput" placeholder="Search enrolled student..." onkeyup="filterRosterList()">
          </div>
        </div>

        <!-- Roster Student List -->
        <div id="roster-list-container" class="roster-list">
          <!-- Dynamically populated -->
        </div>

        <!-- Action Footer -->
        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:16px; margin-top:18px;">
          <button type="button" class="btn-topbar primary" onclick="filterMainTableFromRoster()" style="background:#176b45; color:white; border:none; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
            Filter Table Below to This Course &darr;
          </button>
          <button type="button" class="btn-topbar" onclick="closeCourseRosterModal()">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🔍 STUDENT PROFILE & ENROLLMENT RECORD MODAL               -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="student-modal" class="modal-overlay" style="display:none;" onclick="handleModalOverlayClick(event)">
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <div style="font-size:1.1rem; font-weight:800; font-family:'Lora',serif;" id="m-student-name">Student Profile</div>
          <div style="font-size:0.75rem; color:rgba(255,255,255,0.85);" id="m-ref-sub">Student No.: #EDU-0000 • Batch 08</div>
        </div>
        <button onclick="closeStudentModal()" style="background:none; border:none; color:white; font-size:1.4rem; cursor:pointer; line-height:1;">&times;</button>
      </div>

      <div class="modal-body">
        <!-- Status & Track Banner -->
        <div style="display:flex; justify-content:space-between; align-items:center; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:18px;">
          <div>
            <div style="font-size:0.7rem; font-weight:800; color:#166534; text-transform:uppercase;">Enrolled Program Track</div>
            <div style="font-weight:800; color:#0f5c3a; font-size:1rem;" id="m-program-name">—</div>
          </div>
          <div id="m-status-badge">
            <span class="badge-status badge-active">Enrolled</span>
          </div>
        </div>

        <!-- Rejection Reason Banner (shown if status is rejected) -->
        <div id="m-rejection-banner" style="display:none; background:#fef2f2; border:1px solid #fee2e2; border-radius:10px; padding:12px 16px; margin-bottom:18px;">
          <div style="font-size:0.7rem; font-weight:800; color:#dc2626; text-transform:uppercase; margin-bottom:3px; display:flex; align-items:center; gap:5px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#dc2626;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/></svg>
            Rejection Reason
          </div>
          <div style="font-weight:700; color:#991b1b; font-size:0.88rem; line-height:1.4;" id="m-rejection-text">—</div>
        </div>

        <!-- Personal & Identity Information -->
        <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.04em;">Personal Information</div>
        <div class="modal-grid" style="margin-bottom:18px;">
          <div class="modal-field-item">
            <div class="modal-field-label">Legal Name</div>
            <div class="modal-field-value" id="m-fullname">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Muslim Name</div>
            <div class="modal-field-value" id="m-muslimname">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Age &amp; Birthdate</div>
            <div class="modal-field-value" id="m-age-dob">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Muslim Background</div>
            <div class="modal-field-value" id="m-muslim-type">—</div>
          </div>
        </div>

        <!-- Contact & Residence Details -->
        <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.04em;">Contact &amp; Residence</div>
        <div class="modal-grid" style="margin-bottom:18px;">
          <div class="modal-field-item">
            <div class="modal-field-label">Contact Number</div>
            <div class="modal-field-value" id="m-contact">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Facebook Account</div>
            <div class="modal-field-value" id="m-facebook">—</div>
          </div>
          <div class="modal-field-item" style="grid-column: span 2;">
            <div class="modal-field-label">Residential Address</div>
            <div class="modal-field-value" id="m-address">—</div>
          </div>
        </div>

        <!-- Emergency Contact -->
        <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.04em;">Emergency Contact</div>
        <div class="modal-grid" style="margin-bottom:18px;">
          <div class="modal-field-item">
            <div class="modal-field-label">Emergency Person</div>
            <div class="modal-field-value" id="m-em-person">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Relationship &amp; Contact</div>
            <div class="modal-field-value" id="m-em-contact">—</div>
          </div>
        </div>

        <!-- Fee & Payment Info -->
        <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px; letter-spacing:0.04em;">Fee &amp; Payment Settlement</div>
        <div class="modal-grid" style="margin-bottom:20px;">
          <div class="modal-field-item">
            <div class="modal-field-label">Payment Mode</div>
            <div class="modal-field-value" id="m-payment-mode">—</div>
          </div>
          <div class="modal-field-item">
            <div class="modal-field-label">Fee Amount &amp; Status</div>
            <div class="modal-field-value" id="m-fee-status">₱120.00 • Paid</div>
          </div>
          <div class="modal-field-item" style="grid-column: span 2;">
            <div class="modal-field-label">Payment Channel &amp; Reference</div>
            <div class="modal-field-value" id="m-channel-ref">—</div>
          </div>
        <!-- Learning Progress & Attendance Tracker Block -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">Learning Progress &amp; Attendance</div>
            <div id="m-prog-status-badge">
              <span class="badge-status badge-not-started">Not Started</span>
            </div>
          </div>
          <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:10px; margin-bottom:12px;">
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; text-align:center;">
              <div style="font-size:1.15rem; font-weight:800; color:#166534;" id="m-prog-att">0 / 12</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Attended</div>
            </div>
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; text-align:center;">
              <div style="font-size:1.15rem; font-weight:800; color:#0369a1;" id="m-prog-comp">0 / 12</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Completed</div>
            </div>
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; text-align:center;">
              <div style="font-size:1.15rem; font-weight:800; color:#b45309;" id="m-prog-pct">0%</div>
              <div style="font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Progress</div>
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <div style="height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
              <div id="m-prog-bar" style="height:100%; width:0%; background:#176b45; border-radius:4px; transition:width 0.4s ease;"></div>
            </div>
          </div>
          <div style="text-align:right;">
            <button type="button" id="btn-open-progress-detail" class="btn-topbar primary" style="background:#176b45; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-weight:700; font-size:0.75rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
              Manage 12-Session Attendance &amp; Progress &rarr;
            </button>
          </div>
        </div>

        <!-- Action Footer -->
        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:16px;">
          <div id="m-quick-actions" style="display:flex; gap:8px;"></div>
          <button class="btn-topbar" onclick="closeStudentModal()">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🚫 REJECTION REASON MODAL WITH DROPDOWN & DYNAMIC INPUT   -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="edu-reject-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:999999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="height:4px; background:linear-gradient(90deg, #dc2626, #ef4444);"></div>
      
      <div style="padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#991b1b; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#dc2626;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/></svg>
          Reject Enrollment Request
        </h5>
        <button type="button" onclick="closeEduRejectModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:20px 22px;">
        <div id="edu-reject-target-info" style="background:#fef2f2; border:1px solid #fee2e2; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:0.84rem; color:#991b1b; font-weight:600;">
          <!-- Populated by JS -->
        </div>

        <p style="font-size:0.83rem; color:#4b5563; margin:0 0 14px 0; line-height:1.45;">
          Please select the official reason for rejecting this application. The applicant will be notified with this reason.
        </p>

        <!-- Rejection Reason Dropdown -->
        <div style="margin-bottom:14px;">
          <label for="edu-reject-reason-select" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">
            Rejection Reason <span style="color:#dc2626;">*</span>
          </label>
          <select id="edu-reject-reason-select" onchange="toggleEduRejectOthers(this.value)" style="width:100%; box-sizing:border-box; border:1.5px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:0.86rem; background:#fff; font-family:inherit; color:#1f2937; outline:none; transition:border-color 0.15s;">
            <option value="">-- Select Rejection Reason --</option>
            <option value="Class / batch quota reached">Class / batch quota reached</option>
            <option value="Ineligible for this academic level or program">Ineligible for this academic level or program</option>
            <option value="Duplicate enrollment registration">Duplicate enrollment registration</option>
            <option value="Incomplete applicant information or missing documents">Incomplete applicant information or missing documents</option>
            <option value="Registration fee settlement failed or unverified">Registration fee settlement failed or unverified</option>
            <option value="Others">Others</option>
          </select>
        </div>

        <!-- Conditional Input Field for 'Others' -->
        <div id="edu-reject-others-wrap" style="display:none; margin-bottom:14px;">
          <label for="edu-reject-others-input" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">
            Specify Custom Reason <span style="color:#dc2626;">*</span>
          </label>
          <textarea id="edu-reject-others-input" rows="3" placeholder="Enter specific reason for rejection..." style="width:100%; box-sizing:border-box; border:1.5px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:0.86rem; font-family:inherit; color:#1f2937; resize:vertical; outline:none; transition:border-color 0.15s;"></textarea>
        </div>
      </div>

      <div style="padding:14px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb;">
        <button type="button" onclick="closeEduRejectModal()" style="padding:8px 16px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; font-size:0.84rem; cursor:pointer;">
          Cancel
        </button>
        <button type="button" id="btn-confirm-edu-reject" onclick="submitEduRejection()" style="padding:8px 18px; border-radius:8px; border:none; background:#dc2626; color:white; font-weight:700; font-size:0.84rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          Confirm Rejection
        </button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const rawAllRecords = <?= json_encode(array_values($records ?? [])) ?>;
    const catFilter = document.getElementById('categoryFilter');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const searchInput = document.getElementById('searchInput');
    let activeStatus = 'All';
    let activeCourse = 'All';

    // Current course inspected in Roster Modal
    let activeRosterCourse = '';
    let activeRosterTab = 'all';

    function applyFilters() {
      const selectedCategory = catFilter.value;
      const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
      const rows = document.querySelectorAll('.enrollment-row');
      let visibleCount = 0;

      rows.forEach(row => {
        const rowProgram = row.getAttribute('data-program') || '';
        const rawProgram = row.getAttribute('data-raw-program') || '';
        const rowStatus = row.getAttribute('data-status') || '';
        const rowSearch = row.getAttribute('data-search') || '';

        // Category/Course match (check level title or program name)
        let catMatch = false;
        if (activeCourse === 'All' && selectedCategory === 'All') {
          catMatch = true;
        } else {
          const filterTarget = activeCourse !== 'All' ? activeCourse : selectedCategory;
          catMatch = (rowProgram === filterTarget || rawProgram === filterTarget || rowProgram.includes(filterTarget) || filterTarget.includes(rowProgram));
        }

        // Status match (All, enrolled, pending, completed, rejected)
        let statusMatch = false;
        if (activeStatus === 'All') {
          statusMatch = true;
        } else if (activeStatus === 'enrolled') {
          statusMatch = (rowStatus === 'enrolled');
        } else if (activeStatus === 'pending') {
          statusMatch = (rowStatus === 'pending');
        } else if (activeStatus === 'completed') {
          statusMatch = (rowStatus === 'completed');
        } else {
          statusMatch = (rowStatus === activeStatus);
        }

        // Search match
        let searchMatch = true;
        if (query) {
          searchMatch = rowSearch.includes(query);
        }

        if (catMatch && statusMatch && searchMatch) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Show empty state if no records match
      const tbody = document.getElementById('enrollment-tbody');
      let emptyMsg = document.getElementById('no-records-msg');
      
      if (visibleCount === 0) {
        if (!emptyMsg) {
          const tr = document.createElement('tr');
          tr.id = 'no-records-msg';
          tr.innerHTML = `<td colspan="9" style="text-align:center;padding:50px;color:var(--text-muted);">
            <div style="font-size:2rem; margin-bottom:8px;">📚</div>
            No students match the selected <strong>${activeStatus === 'All' ? '' : activeStatus}</strong> criteria.
          </td>`;
          tbody.appendChild(tr);
        }
      } else if (emptyMsg) {
        emptyMsg.remove();
      }
    }

    function applyTabFilter(status) {
      tabBtns.forEach(b => {
        if (b.getAttribute('data-status') === status) {
          b.classList.add('active');
        } else {
          b.classList.remove('active');
        }
      });
      activeStatus = status;
      applyFilters();
    }

    function filterByCourseCard(courseTitle) {
      activeCourse = courseTitle;
      catFilter.value = courseTitle;

      document.querySelectorAll('.course-card, .course-table-row').forEach(card => {
        if (card.getAttribute('data-course') === courseTitle) {
          card.classList.add('active-course-filter');
        } else {
          card.classList.remove('active-course-filter');
        }
      });

      applyFilters();
    }

    function resetCourseFilter() {
      activeCourse = 'All';
      catFilter.value = 'All';
      document.querySelectorAll('.course-card, .course-table-row').forEach(c => c.classList.remove('active-course-filter'));
      applyFilters();
    }

    catFilter.addEventListener('change', function() {
      activeCourse = this.value;
      document.querySelectorAll('.course-card, .course-table-row').forEach(c => {
        if (c.getAttribute('data-course') === activeCourse) {
          c.classList.add('active-course-filter');
        } else {
          c.classList.remove('active-course-filter');
        }
      });
      applyFilters();
    });

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        tabBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeStatus = this.getAttribute('data-status');
        applyFilters();
      });
    });

    // ══════════════════════════════════════════════════════════
    // 🎓 COURSE ROSTER MODAL LOGIC: WHO ARE THE STUDENTS ENROLLED
    // ══════════════════════════════════════════════════════════
    function openCourseRosterModal(courseTitle) {
      window.location.href = '<?= url("/admin/dawah/education/courses?course=") ?>' + encodeURIComponent(courseTitle);
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

      // Rejection reason banner in student modal
      const rejBanner = document.getElementById('m-rejection-banner');
      const rejText = document.getElementById('m-rejection-text');
      if (rejBanner && rejText) {
        if (['dropped', 'rejected'].includes(stLower) && r.rejection_reason) {
          rejText.textContent = r.rejection_reason;
          rejBanner.style.display = 'block';
        } else {
          rejBanner.style.display = 'none';
        }
      }

      // Populate Learning Progress & Attendance Tracker
      const attCount = r.sessions_attended || 0;
      const compCount = r.sessions_completed || 0;
      const totalSess = r.total_sessions || 12;
      const progPct = r.progress_percent || 0;
      const learnStatus = r.learning_status || 'Not Started';

      const progAttEl = document.getElementById('m-prog-att');
      const progCompEl = document.getElementById('m-prog-comp');
      const progPctEl = document.getElementById('m-prog-pct');
      const progBarEl = document.getElementById('m-prog-bar');
      const progBadgeEl = document.getElementById('m-prog-status-badge');
      const btnOpenDetail = document.getElementById('btn-open-progress-detail');

      if (progAttEl) progAttEl.textContent = `${attCount} / ${totalSess}`;
      if (progCompEl) progCompEl.textContent = `${compCount} / ${totalSess}`;
      if (progPctEl) progPctEl.textContent = `${progPct}%`;
      if (progBarEl) progBarEl.style.width = `${progPct}%`;
      if (progBadgeEl) {
        const bCls = learnStatus === 'Completed' ? 'badge-completed' : (learnStatus === 'In Progress' ? 'badge-in-progress' : 'badge-not-started');
        progBadgeEl.innerHTML = `<span class="badge-status ${bCls}">${escapeHtml(learnStatus)}</span>`;
      }
      if (btnOpenDetail) {
        btnOpenDetail.onclick = () => {
          closeStudentModal();
          openProgressModal(r.id);
        };
      }

      // Quick actions: Mark as Paid & Accept/Reject Enrollment
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
      if (['pending', 'under_review'].includes(stLower)) {
        actionsHtml += `
          <button type="button" class="btn-topbar primary" onclick="quickAcceptEnrollment(${r.id})" style="background:#166534; color:white; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer;">
            Accept Enrollment
          </button>
          <button type="button" class="btn-topbar" onclick="openEduRejectModal(${r.id})" style="background:#dc2626; color:white; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer;">
            Reject Enrollment
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

    // ══════════════════════════════════════════════════════════
    // 🚫 REJECTION REASON MODAL LOGIC (DROPDOWN + OTHERS)
    // ══════════════════════════════════════════════════════════
    let eduRejectTargetId = null;

    function openEduRejectModal(id) {
      eduRejectTargetId = id;
      const r = rawAllRecords.find(item => Number(item.id) === Number(id));
      const reqId = '#EDU-' + String(id).padStart(4, '0');
      const studentName = r ? (`${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + id) : 'Student #' + id;

      const infoEl = document.getElementById('edu-reject-target-info');
      if (infoEl) {
        infoEl.innerHTML = `<strong>Record:</strong> ${reqId} &nbsp;•&nbsp; <strong>Student:</strong> ${escapeHtml(studentName)}`;
      }

      const sel = document.getElementById('edu-reject-reason-select');
      if (sel) sel.value = '';

      const othersWrap = document.getElementById('edu-reject-others-wrap');
      if (othersWrap) othersWrap.style.display = 'none';

      const othersInput = document.getElementById('edu-reject-others-input');
      if (othersInput) othersInput.value = '';

      const modal = document.getElementById('edu-reject-modal');
      if (modal) modal.style.display = 'flex';
    }

    function closeEduRejectModal() {
      const modal = document.getElementById('edu-reject-modal');
      if (modal) modal.style.display = 'none';
      eduRejectTargetId = null;
    }

    document.getElementById('edu-reject-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeEduRejectModal();
    });

    function toggleEduRejectOthers(val) {
      const othersWrap = document.getElementById('edu-reject-others-wrap');
      const othersInput = document.getElementById('edu-reject-others-input');
      if (val === 'Others') {
        if (othersWrap) othersWrap.style.display = 'block';
        setTimeout(() => othersInput?.focus(), 60);
      } else {
        if (othersWrap) othersWrap.style.display = 'none';
        if (othersInput) othersInput.value = '';
      }
    }

    async function submitEduRejection() {
      if (!eduRejectTargetId) return;
      const id = eduRejectTargetId;
      const sel = document.getElementById('edu-reject-reason-select');
      const reasonVal = sel ? sel.value.trim() : '';

      if (!reasonVal) {
        alert('Please select a rejection reason from the dropdown.');
        sel?.focus();
        return;
      }

      let finalReason = reasonVal;
      if (reasonVal === 'Others') {
        const customReason = document.getElementById('edu-reject-others-input')?.value.trim();
        if (!customReason) {
          alert('Please specify the custom rejection reason.');
          document.getElementById('edu-reject-others-input')?.focus();
          return;
        }
        finalReason = customReason;
      }

      const btn = document.getElementById('btn-confirm-edu-reject');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Rejecting...';
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/education/reject") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id, reason: finalReason })
        });

        const res = await response.json();
        if (res.success) {
          alert('Enrollment application has been rejected and the applicant notified with the reason.');
          closeEduRejectModal();
          closeStudentModal();
          window.location.reload();
        } else {
          alert(res.message || 'Error rejecting enrollment.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Confirm Rejection';
        }
      }
    }

    async function quickAcceptEnrollment(id) {
      if (!confirm(`Are you sure you want to accept enrollment #EDU-${String(id).padStart(4, '0')}?`)) return;
      try {
        const res = await fetch('<?= url("/admin/dawah/education/accept") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
          alert('Enrollment accepted successfully!');
          window.location.reload();
        } else {
          alert(data.message || 'Error accepting enrollment.');
        }
      } catch (err) {
        alert('Connection error occurred.');
      }
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

    // Auto-filter category if specified in URL query
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const targetCourse = urlParams.get('course');
      if (targetCourse) {
        const catFilter = document.getElementById('categoryFilter');
        if (catFilter) {
          catFilter.value = targetCourse;
          activeCourse = targetCourse;
          applyFilters();
        }
      }
    });

    // ══════════════════════════════════════════════════════════
    // 🎓 DETAILED STUDENT LEARNING PROGRESS & ATTENDANCE LOGIC
    // ══════════════════════════════════════════════════════════
    let currentProgressStudentId = null;

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
  </script>

  <!-- 🎓 DETAILED STUDENT LEARNING PROGRESS & ATTENDANCE MODAL -->
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
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; margin-bottom:18px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            <div>
              <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                <span style="font-size:0.7rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">Academic Program Track</span>
                <span id="pm-student-no-badge" style="font-family:'Courier New', monospace; font-size:0.74rem; font-weight:800; color:var(--primary-female); background:rgba(23,107,69,0.08); padding:2px 7px; border-radius:4px; border:1px solid rgba(23,107,69,0.25);">Student No.: #EDU-0000</span>
              </div>
              <div style="font-size:1.05rem; font-weight:800; color:#0f172a;" id="pm-program-title">—</div>
            </div>
            <div id="pm-status-badge-wrap">
              <span class="badge-status badge-not-started" id="pm-status-badge">Not Started</span>
            </div>
          </div>

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

        <div style="display:flex; gap:10px; align-items:flex-start; background:rgba(23,107,69,0.06); border:1px solid rgba(23,107,69,0.2); border-radius:10px; padding:12px 14px; margin-bottom:18px;">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#176b45;flex-shrink:0;margin-top:1px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
          <div style="font-size:0.78rem; color:#14532d; line-height:1.45;">
            <strong>Learning Evaluation Policy:</strong> Progress and program completion are strictly based on verified attendance and completed learning sessions. Neither attendance alone nor session completion alone determines program completion. Full program completion requires all 12 sessions to be both attended and completed.
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <div style="font-size:0.78rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">Standard 12-Session Curriculum Checklist</div>
          <div style="font-size:0.72rem; color:var(--primary); font-weight:700;" id="pm-save-indicator">✓ Live Synced</div>
        </div>

        <div id="pm-sessions-container" style="display:flex; flex-direction:column; gap:8px;">
          <div style="text-align:center; padding:30px; color:var(--text-muted);">Loading curriculum sessions...</div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:16px; margin-top:20px;">
          <div style="font-size:0.75rem; color:var(--text-muted);">
            Changes to attendance and session completion are saved immediately.
          </div>
          <button type="button" class="btn-topbar" onclick="closeProgressModal()">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
