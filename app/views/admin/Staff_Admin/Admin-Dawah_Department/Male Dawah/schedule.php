<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$dawah_type = $dawah_type ?? 'male';
$isFemale = ($dawah_type === 'female');

$availableSchedules = $availableSchedules ?? [];
$pendingSchedules = $pendingSchedules ?? [];
$blockedList = $blockedList ?? [];
$blockedDates = $blockedDates ?? [];
$schedules = $schedules ?? [];

// Helper functions for date & time display
function fmtDate($d) {
    if (!$d) return '—';
    $t = strtotime($d);
    return $t ? date('M d, Y', $t) : $d;
}
function fmtDayOfWeek($d) {
    if (!$d) return '';
    $t = strtotime($d);
    return $t ? date('l', $t) : '';
}
function fmtTime($t) {
    if (!$t) return 'Flexible';
    $ts = strtotime($t);
    return $ts ? date('h:i A', $ts) : $t;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Service Schedule Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* ── Layout & Responsive Grid ── */
    .schedule-layout {
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 24px;
      align-items: start;
    }
    @media (max-width: 1024px) {
      .schedule-layout { grid-template-columns: 1fr; }
    }

    /* ── Summary Ribbon ── */
    .schedule-kpis {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .kpi-card {
      background: #fff;
      padding: 18px 20px;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 6px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: all 0.25s ease;
    }
    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .kpi-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
    }
    .kpi-card.avail::after { background: var(--success, #16a34a); }
    .kpi-card.unavail::after { background: var(--danger, #dc2626); }
    .kpi-card.pending::after { background: var(--warning, #d97706); }

    .kpi-label {
      font-size: 0.74rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }
    .kpi-value {
      font-size: 1.8rem;
      font-weight: 800;
      line-height: 1;
    }
    .kpi-value.avail { color: #15803d; }
    .kpi-value.unavail { color: #b91c1c; }
    .kpi-value.pending { color: #b45309; }

    /* ── Calendar Styling ── */
    .calendar-card {
      background: #fff;
      border-radius: 16px;
      border: 1px solid var(--border);
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .calendar-header {
      padding: 18px 20px;
      background: #fafbfc;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .month-title {
      font-family: 'Lora', serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }
    .cal-nav { display: flex; gap: 8px; }
    .cal-nav-btn {
      background: white;
      border: 1px solid var(--border);
      width: 32px; height: 32px;
      border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--primary-dark); transition: all 0.2s;
    }
    .cal-nav-btn:hover { border-color: var(--primary); color: var(--primary); }
    
    .cal-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      padding: 10px;
    }
    .cal-day-label {
      text-align: center;
      font-size: 0.7rem;
      font-weight: 800;
      color: var(--text-muted);
      text-transform: uppercase;
      padding: 8px 0;
    }
    .cal-date {
      height: 44px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      border-radius: 8px;
      position: relative;
      transition: all 0.2s;
    }
    .cal-date:hover { background: rgba(20,83,45,0.08); color: var(--primary); }
    .cal-date.today { background: var(--primary, #14532d); color: white; }
    .cal-date.selected { border: 2px solid var(--primary); color: var(--primary); font-weight: 800; }
    .cal-date.other-month { opacity: 0.25; pointer-events: none; }
    
    .cal-date.blocked {
      background: #fef2f2;
      color: #991b1b;
      text-decoration: line-through;
    }
    .cal-date.blocked::after {
      content: '✕';
      position: absolute;
      top: 2px; right: 4px;
      font-size: 0.6rem;
      color: var(--danger, #dc2626);
      font-weight: 900;
      text-decoration: none;
    }

    .schedule-indicator {
      position: absolute;
      bottom: 5px;
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: #16a34a;
    }
    .cal-date.has-pending .schedule-indicator {
      background: #d97706;
    }

    .cal-legend {
      padding: 12px 18px;
      border-top: 1px solid var(--border);
      background: #f8fafc;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      font-size: 0.72rem;
      color: #475569;
      font-weight: 600;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
    .legend-dot.avail { background: #16a34a; }
    .legend-dot.unavail { background: #dc2626; }
    .legend-dot.pending { background: #d97706; }

    /* ── Tab Navigation ── */
    .tab-nav {
      display: flex;
      gap: 8px;
      border-bottom: 2px solid var(--border);
      margin-bottom: 20px;
      overflow-x: auto;
      padding-bottom: 2px;
    }
    .tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      border-radius: 10px 10px 0 0;
      border: none;
      background: transparent;
      font-size: 0.86rem;
      font-weight: 700;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s ease;
      white-space: nowrap;
    }
    .tab-btn:hover { color: var(--primary-dark); background: rgba(0,0,0,0.03); }
    .tab-btn.active {
      color: var(--primary);
      background: #fff;
      border-bottom: 3px solid var(--primary);
    }
    .tab-count {
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 0.72rem;
      font-weight: 800;
      background: #e2e8f0;
      color: #475569;
    }
    .tab-count.approved { background: rgba(22,163,74,0.12); color: #15803d; }
    .tab-count.rejected { background: rgba(220,38,38,0.12); color: #dc2626; }
    .tab-count.pending { background: rgba(217,119,6,0.14); color: #b45309; }

    /* ── Schedule List Cards ── */
    .sched-list-card {
      background: #fff;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 14px rgba(0,0,0,0.03);
      overflow: hidden;
      margin-bottom: 24px;
    }
    .sched-card-header {
      padding: 16px 20px;
      background: #fafbfc;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .sched-card-title {
      font-family: 'Lora', serif;
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .sched-card-body { padding: 0; }

    .sched-item-row {
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      border-bottom: 1px solid #f1f5f3;
      transition: all 0.2s ease;
    }
    .sched-item-row:last-child { border-bottom: none; }
    .sched-item-row:hover { background: #fafdfc; }

    .sched-datetime-col {
      min-width: 140px;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .sched-time-badge {
      font-weight: 800;
      font-size: 0.95rem;
      color: var(--primary-dark);
    }
    .sched-date-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      font-weight: 600;
    }

    .sched-main-col { flex: 1; min-width: 200px; }
    .sched-type-tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 3px;
    }
    .sched-type-tag.counseling { color: #6d28d9; }
    .sched-type-tag.marriage { color: #b45309; }
    .sched-type-tag.event { color: #0284c7; }
    .sched-type-tag.blocked { color: #dc2626; }
    
    .sched-title-text {
      font-size: 0.96rem;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .sched-desc-text {
      font-size: 0.8rem;
      color: #64748b;
      line-height: 1.35;
    }

    .sched-action-col {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .btn-sched-action {
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      border: 1px solid var(--border);
      background: white;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      text-decoration: none;
      color: #334155;
    }
    .btn-sched-action:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
      color: #0f172a;
    }
    .btn-sched-action.success {
      border-color: #bbf7d0;
      color: #15803d;
      background: #f0fdf4;
    }
    .btn-sched-action.success:hover {
      background: #dcfce7;
      border-color: #86efac;
    }
    .btn-sched-action.danger {
      border-color: #fecaca;
      color: #b91c1c;
      background: #fef2f2;
    }
    .btn-sched-action.danger:hover {
      background: #fee2e2;
      border-color: #fca5a5;
    }
    .btn-sched-action.primary {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    .btn-sched-action.primary:hover {
      background: #0f3e21;
      border-color: #0f3e21;
    }

    .empty-state-box {
      padding: 42px 20px;
      text-align: center;
      color: var(--text-muted);
    }
    .empty-state-box svg {
      width: 42px; height: 42px;
      fill: #cbd5e1;
      margin-bottom: 8px;
    }
    .empty-state-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: #475569;
    }
    .empty-state-sub {
      font-size: 0.8rem;
      color: #94a3b8;
      margin-top: 4px;
    }

    @keyframes modalPop {
      from { opacity:0; transform:scale(0.92) translateY(14px); }
      to { opacity:1; transform:scale(1) translateY(0); }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'schedule';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title"><?= $isFemale ? "Sisters' Schedule Management" : "Departmental Schedule Management" ?></div>
          <div class="top-bar-subtitle"><?= $isFemale ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Coordinate available schedules, blocked blackout dates &amp; pending service requests</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="showAssignModal()" title="Assign New Department Schedule">
             <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
             + Assign Schedule
           </button>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($isFemale ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span class="current">Departmental Schedule</span>
        </div>

        <!-- 1. Operational KPI Cards -->
        <div class="schedule-kpis">
          <div class="kpi-card avail" onclick="setScheduleTab('available')">
            <div class="kpi-label">Available / Confirmed</div>
            <div class="kpi-value avail"><?= count($availableSchedules) ?></div>
            <span style="font-size:0.75rem; color:#166534; font-weight:600;">Active sessions &amp; assignments</span>
          </div>

          <div class="kpi-card unavail" onclick="setScheduleTab('unavailable')">
            <div class="kpi-label">Not Available (Blocked)</div>
            <div class="kpi-value unavail"><?= count($blockedList) ?></div>
            <span style="font-size:0.75rem; color:#991b1b; font-weight:600;">Blackout &amp; holiday dates</span>
          </div>

          <div class="kpi-card pending" onclick="setScheduleTab('pending')">
            <div class="kpi-label">All Pending Requests</div>
            <div class="kpi-value pending"><?= count($pendingSchedules) ?></div>
            <span style="font-size:0.75rem; color:#b45309; font-weight:600;">Awaiting schedule assignment</span>
          </div>
        </div>

        <!-- 2. Main Two-Column Layout -->
        <div class="schedule-layout">
          <!-- ── LEFT: CALENDAR COLUMN ── -->
          <div class="calendar-card">
            <div class="calendar-header">
              <h6 class="month-title" id="cal-month-title">Current Month</h6>
              <div class="cal-nav">
                <button class="cal-nav-btn" onclick="changeMonth(-1)" title="Previous Month">
                  <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                </button>
                <button class="cal-nav-btn" onclick="changeMonth(1)" title="Next Month">
                  <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </button>
              </div>
            </div>

            <div class="cal-grid" id="calendar-grid">
              <!-- Rendered via JavaScript -->
            </div>

            <div class="cal-legend">
              <div class="legend-item"><span class="legend-dot avail"></span> Confirmed Schedule</div>
              <div class="legend-item"><span class="legend-dot unavail"></span> Blocked / Unavailable</div>
              <div class="legend-item"><span class="legend-dot pending"></span> Pending Request</div>
            </div>
          </div>

          <!-- ── RIGHT: SCHEDULE LISTS & TABS COLUMN ── -->
          <div class="timeline-column">
            <!-- Selection Context Ribbon (shown when calendar date is picked) -->
            <div id="selection-context" style="display: none; background: white; padding: 14px 20px; border-radius: 14px; border: 1px solid var(--border); margin-bottom: 20px; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
              <div>
                <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">Selected Calendar Date</div>
                <div id="selected-date-label" style="font-size: 1.05rem; font-weight: 800; color: var(--primary-dark);">—</div>
              </div>
              <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <button type="button" class="btn-sched-action" onclick="clearDateFilter()" title="Clear date selection">Show All Dates</button>
                <button id="assign-sched-btn" class="btn-sched-action primary" style="font-weight:700;">+ Assign On Date</button>
                <button id="toggle-avail-btn" class="btn-sched-action" style="font-weight:700;"></button>
              </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="tab-nav">
              <button type="button" class="tab-btn active" id="btn-tab-available" onclick="setScheduleTab('available')">
                Available / Confirmed <span class="tab-count approved" id="badge-count-avail"><?= count($availableSchedules) ?></span>
              </button>
              <button type="button" class="tab-btn" id="btn-tab-unavailable" onclick="setScheduleTab('unavailable')">
                Not Available (Blocked) <span class="tab-count rejected" id="badge-count-unavail"><?= count($blockedList) ?></span>
              </button>
              <button type="button" class="tab-btn" id="btn-tab-pending" onclick="setScheduleTab('pending')">
                All Pending Schedule <span class="tab-count pending" id="badge-count-pending"><?= count($pendingSchedules) ?></span>
              </button>
            </div>

            <!-- ═══ 1. AVAILABLE / CONFIRMED SCHEDULE TAB ═══ -->
            <div id="view-available" class="sched-tab-panel">
              <div class="sched-list-card">
                <div class="sched-card-header">
                  <div>
                    <h6 class="sched-card-title">
                      <svg viewBox="0 0 24 24" width="18" height="18" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                      Available &amp; Confirmed Schedules
                    </h6>
                    <div id="avail-filter-notice" style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                      Showing active departmental consultations, solemnizations &amp; classes
                    </div>
                  </div>
                  <div style="display:flex; gap:10px; align-items:center;">
                    <input type="text" id="search-available" placeholder="Search event, client or venue..." style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem;" onkeyup="filterAvailableList()" />
                    <button type="button" class="btn-sched-action primary" onclick="showAssignModal()">+ Assign Schedule</button>
                  </div>
                </div>

                <div class="sched-card-body" id="available-list-container">
                  <?php if (empty($availableSchedules)): ?>
                    <div class="empty-state-box">
                      <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                      <div class="empty-state-title">No Confirmed Schedules Found</div>
                      <div class="empty-state-sub">Confirmed counseling, marriage, or assigned classes will appear here.</div>
                    </div>
                  <?php else: ?>
                    <?php foreach ($availableSchedules as $s): 
                      $typeClass = ($s['category'] ?? '') === 'counseling' ? 'counseling' : (($s['category'] ?? '') === 'marriage' ? 'marriage' : 'event');
                      $dateVal = $s['date'] ?? '';
                    ?>
                      <div class="sched-item-row available-item" data-date="<?= htmlspecialchars($dateVal) ?>" data-text="<?= htmlspecialchars(strtolower(($s['name'] ?? '') . ' ' . ($s['desc'] ?? '') . ' ' . ($s['location'] ?? ''))) ?>">
                        <div class="sched-datetime-col">
                          <span class="sched-time-badge"><?= fmtTime($s['time'] ?? '') ?></span>
                          <span class="sched-date-sub"><?= fmtDate($dateVal) ?> (<?= fmtDayOfWeek($dateVal) ?>)</span>
                        </div>
                        <div class="sched-main-col">
                          <div class="sched-type-tag <?= $typeClass ?>">
                            <span style="width:6px; height:6px; border-radius:50%; background:currentColor;"></span>
                            <?= htmlspecialchars($s['type'] ?? 'Session') ?>
                          </div>
                          <div class="sched-title-text"><?= htmlspecialchars($s['name'] ?? '—') ?></div>
                          <div class="sched-desc-text">
                            <?= htmlspecialchars($s['desc'] ?? '') ?>
                            <?php if (!empty($s['counselor'])): ?>
                              • <span style="font-weight:600; color:#334155;"><?= htmlspecialchars($s['counselor']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($s['location'])): ?>
                              • <span style="color:#64748b;"><?= htmlspecialchars($s['location']) ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="sched-action-col">
                          <span class="status-pill confirmed">Confirmed</span>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- ═══ 2. NOT AVAILABLE / BLOCKED SCHEDULE TAB ═══ -->
            <div id="view-unavailable" class="sched-tab-panel" style="display:none;">
              <div class="sched-list-card">
                <div class="sched-card-header">
                  <div>
                    <h6 class="sched-card-title">
                      <svg viewBox="0 0 24 24" width="18" height="18" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/></svg>
                      Not Available / Blocked Schedules
                    </h6>
                    <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                      Blackout dates and unavailable periods where departmental bookings are restricted
                    </div>
                  </div>
                  <div>
                    <button type="button" class="btn-sched-action danger" onclick="showBlockModal()">+ Mark Date Unavailable</button>
                  </div>
                </div>

                <div class="sched-card-body" id="unavailable-list-container">
                  <?php if (empty($blockedList)): ?>
                    <div class="empty-state-box">
                      <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                      <div class="empty-state-title">All Dates Are Currently Available</div>
                      <div class="empty-state-sub">There are no blackout dates or blocked schedules set for this department.</div>
                    </div>
                  <?php else: ?>
                    <?php foreach ($blockedList as $b): 
                      $bDate = $b['blocked_date'] ?? '';
                    ?>
                      <div class="sched-item-row" id="blocked-row-<?= htmlspecialchars($bDate) ?>">
                        <div class="sched-datetime-col">
                          <span class="sched-time-badge" style="color:#b91c1c;">WHOLE DAY</span>
                          <span class="sched-date-sub"><?= fmtDate($bDate) ?> (<?= fmtDayOfWeek($bDate) ?>)</span>
                        </div>
                        <div class="sched-main-col">
                          <div class="sched-type-tag blocked">
                            <span style="width:6px; height:6px; border-radius:50%; background:#dc2626;"></span>
                            Blackout Date • <?= htmlspecialchars(ucfirst($b['department'] ?? $dawah_type)) ?> Da'wah
                          </div>
                          <div class="sched-title-text" style="color:#991b1b;"><?= htmlspecialchars($b['reason'] ?: 'Marked as Unavailable by Admin') ?></div>
                          <div class="sched-desc-text">
                            No user bookings or appointments can be scheduled on this day.
                          </div>
                        </div>
                        <div class="sched-action-col">
                          <button type="button" class="btn-sched-action success" onclick="unblockDate('<?= htmlspecialchars($bDate) ?>')" title="Restore date availability">
                            ✓ Mark as Available
                          </button>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- ═══ 3. ALL PENDING SCHEDULE TAB ═══ -->
            <div id="view-pending" class="sched-tab-panel" style="display:none;">
              <div class="sched-list-card">
                <div class="sched-card-header">
                  <div>
                    <h6 class="sched-card-title">
                      <svg viewBox="0 0 24 24" width="18" height="18" fill="#d97706"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                      All Pending Schedule Requests
                    </h6>
                    <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                      User submitted consultations and ceremony requests awaiting review and date/counselor assignment
                    </div>
                  </div>
                  <div style="display:flex; gap:10px; align-items:center;">
                    <input type="text" id="search-pending" placeholder="Search applicant or concern..." style="padding:6px 12px; border-radius:8px; border:1px solid var(--border); font-size:0.82rem;" onkeyup="filterPendingList()" />
                  </div>
                </div>

                <div class="sched-card-body" id="pending-list-container">
                  <?php if (empty($pendingSchedules)): ?>
                    <div class="empty-state-box">
                      <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                      <div class="empty-state-title">No Pending Requests Awaiting Schedule</div>
                      <div class="empty-state-sub">All incoming requests have been reviewed and scheduled.</div>
                    </div>
                  <?php else: ?>
                    <?php foreach ($pendingSchedules as $p): 
                      $pCat = $p['category'] ?? 'counseling';
                      $pDate = $p['preferred_date'] ?: ($p['date'] ?? '');
                      $pTime = $p['preferred_time'] ?: ($p['time'] ?? '');
                      $typeClass = $pCat === 'counseling' ? 'counseling' : 'marriage';
                      $reviewLink = ($pCat === 'marriage') 
                        ? url('/admin/dawah/marriage') 
                        : url('/admin/dawah/counseling');
                    ?>
                      <div class="sched-item-row pending-item" data-date="<?= htmlspecialchars($pDate) ?>" data-text="<?= htmlspecialchars(strtolower(($p['name'] ?? '') . ' ' . ($p['desc'] ?? '') . ' ' . ($p['contact'] ?? ''))) ?>">
                        <div class="sched-datetime-col">
                          <span class="sched-time-badge" style="color:#b45309;"><?= fmtTime($pTime) ?></span>
                          <span class="sched-date-sub"><?= fmtDate($pDate) ?> (Requested)</span>
                        </div>
                        <div class="sched-main-col">
                          <div class="sched-type-tag <?= $typeClass ?>">
                            <span style="width:6px; height:6px; border-radius:50%; background:currentColor;"></span>
                            <?= htmlspecialchars($p['type'] ?? 'Request') ?> • Pending Assignment
                          </div>
                          <div class="sched-title-text"><?= htmlspecialchars($p['name'] ?? '—') ?></div>
                          <div class="sched-desc-text">
                            <strong>Reason:</strong> <?= htmlspecialchars($p['desc'] ?? 'General Consultation') ?>
                            <?php if (!empty($p['contact'])): ?>
                              • <span style="color:#475569;"><?= htmlspecialchars($p['contact']) ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="sched-action-col">
                          <a href="<?= $reviewLink ?>" class="btn-sched-action primary">
                            Review &amp; Set Schedule →
                          </a>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── ASSIGN MANUAL SCHEDULE MODAL ── -->
  <div id="assign-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(5px); z-index:9999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:480px; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalPop 0.25s ease;">
      <div style="padding:20px 24px; background:#fafbfc; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h6 style="margin:0; font-family:'Lora',serif; font-size:1.1rem; font-weight:700; color:var(--primary-dark);">Assign New Schedule Event</h6>
        <button onclick="closeAssignModal()" style="background:none; border:none; cursor:pointer; font-size:1.6rem; color:var(--text-muted); line-height:1;">&times;</button>
      </div>
      <form onsubmit="submitAssignment(event)" style="padding:22px 24px;">
        <input type="hidden" id="assign-date-input" value="<?= date('Y-m-d') ?>">
        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Event Title / Subject <span style="color:red;">*</span></label>
          <input type="text" id="assign-title" placeholder="e.g. Beginners Qur'an Class" required style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Event Type</label>
            <select id="assign-type" style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; background:#fff;">
              <option value="Class">Class / Subject</option>
              <option value="Seminar">Seminar</option>
              <option value="Special Session">Special Session</option>
              <option value="Other">Other Event</option>
            </select>
          </div>
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Start Time <span style="color:red;">*</span></label>
            <input type="time" id="assign-time" value="10:00" required style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit;">
          </div>
        </div>

        <div style="margin-bottom:18px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Description / Venue Details</label>
          <textarea id="assign-desc" placeholder="Details about the class or venue location..." style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; height:75px; resize:none;"></textarea>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button type="button" onclick="closeAssignModal()" class="btn-topbar">Cancel</button>
          <button type="submit" class="btn-topbar primary" style="font-weight:700;">Save &amp; Confirm Event</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── BLOCK / UNAVAILABLE DATE MODAL ── -->
  <div id="block-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(5px); z-index:9999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:440px; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden; animation:modalPop 0.25s ease;">
      <div style="padding:20px 24px; background:#fef2f2; border-bottom:1px solid #fecaca; display:flex; justify-content:space-between; align-items:center;">
        <h6 style="margin:0; font-family:'Lora',serif; font-size:1.1rem; font-weight:700; color:#b91c1c;">Mark Date as Unavailable</h6>
        <button onclick="closeBlockModal()" style="background:none; border:none; cursor:pointer; font-size:1.6rem; color:#991b1b; line-height:1;">&times;</button>
      </div>
      <form onsubmit="submitBlock(event)" style="padding:22px 24px;">
        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Target Date <span style="color:red;">*</span></label>
          <input type="date" id="block-date-input" min="<?= date('Y-m-d') ?>" required style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; font-weight:600;">
        </div>
        <div style="margin-bottom:20px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; text-transform:uppercase; margin-bottom:6px;">Reason for Blackout / Closure <span style="color:red;">*</span></label>
          <textarea id="block-reason" placeholder="e.g. Department Meeting, Public Holiday, Facility Maintenance..." required style="width:100%; box-sizing:border-box; padding:10px 12px; border-radius:8px; border:1px solid #cbd5e1; font-family:inherit; height:85px; resize:none;"></textarea>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button type="button" onclick="closeBlockModal()" class="btn-topbar">Cancel</button>
          <button type="submit" class="btn-topbar" style="background:#dc2626; color:white; border-color:#dc2626; font-weight:700;">Confirm Blackout Date</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    const BASE_URL = '<?= rtrim(BASE_URL, '/') ?>';
    const DAWAH_TYPE = '<?= $dawah_type ?>';
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const scheduledDates = <?= json_encode(array_values(array_unique(array_filter(array_column($availableSchedules, 'date'))))) ?>;
    const pendingDates = <?= json_encode(array_values(array_unique(array_filter(array_map(fn($p) => $p['preferred_date'] ?: ($p['date'] ?? ''), $pendingSchedules))))) ?>;
    const blockedDates = <?= json_encode(array_values(array_unique(array_filter(array_column($blockedList, 'blocked_date'))))) ?>;

    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let activeCalendarFilterDate = null;

    // ── CALENDAR RENDERING ──
    function renderCalendar() {
      const grid = document.getElementById('calendar-grid');
      const title = document.getElementById('cal-month-title');
      const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
      
      title.textContent = `${months[currentMonth]} ${currentYear}`;
      
      let html = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].map(d => `<div class="cal-day-label">${d}</div>`).join('');
      
      const first = new Date(currentYear, currentMonth, 1).getDay();
      const last = new Date(currentYear, currentMonth + 1, 0).getDate();
      
      for(let i=0; i<first; i++) html += '<div class="cal-date other-month"></div>';
      
      const todayStr = '<?= date('Y-m-d') ?>';
      
      for(let d=1; d<=last; d++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const isToday = dateStr === todayStr;
        const hasApp = scheduledDates.includes(dateStr);
        const hasPending = pendingDates.includes(dateStr);
        const isBlocked = blockedDates.includes(dateStr);
        const isSelected = (activeCalendarFilterDate === dateStr);
        
        let indicatorHtml = '';
        if (hasApp) {
          indicatorHtml = '<div class="schedule-indicator"></div>';
        } else if (hasPending) {
          indicatorHtml = '<div class="schedule-indicator" style="background:#d97706;"></div>';
        }

        html += `
          <div class="cal-date ${isToday?'today':''} ${isBlocked?'blocked':''} ${isSelected?'selected':''}" data-date="${dateStr}" onclick="selectDate('${dateStr}')">
            ${d}
            ${indicatorHtml}
          </div>
        `;
      }
      
      grid.innerHTML = html;
    }

    function changeMonth(dir) {
      currentMonth += dir;
      if(currentMonth > 11) { currentMonth = 0; currentYear++; }
      if(currentMonth < 0) { currentMonth = 11; currentYear--; }
      renderCalendar();
    }

    // ── TAB SWITCHING ──
    function setScheduleTab(tab) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.sched-tab-panel').forEach(p => p.style.display = 'none');

      if (tab === 'available') {
        document.getElementById('btn-tab-available')?.classList.add('active');
        document.getElementById('view-available').style.display = 'block';
      } else if (tab === 'unavailable') {
        document.getElementById('btn-tab-unavailable')?.classList.add('active');
        document.getElementById('view-unavailable').style.display = 'block';
      } else if (tab === 'pending') {
        document.getElementById('btn-tab-pending')?.classList.add('active');
        document.getElementById('view-pending').style.display = 'block';
      }
    }

    // ── DATE SELECTION & FILTERING ──
    function selectDate(dateStr) {
      activeCalendarFilterDate = dateStr;
      document.querySelectorAll('.cal-date').forEach(el => el.classList.remove('selected'));
      const target = document.querySelector(`.cal-date[data-date="${dateStr}"]`);
      if (target) target.classList.add('selected');
      
      const dateObj = new Date(dateStr + 'T00:00:00');
      const label = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

      // Update Selection Context Bar
      const context = document.getElementById('selection-context');
      context.style.display = 'flex';
      document.getElementById('selected-date-label').textContent = label;

      const isBlocked = blockedDates.includes(dateStr);
      const btn = document.getElementById('toggle-avail-btn');
      
      if(isBlocked) {
        btn.textContent = '✓ Mark as Available';
        btn.className = 'btn-sched-action success';
        btn.onclick = () => unblockDate(dateStr);
      } else {
        btn.textContent = '✕ Mark Unavailable';
        btn.className = 'btn-sched-action danger';
        btn.onclick = () => showBlockModal(dateStr);
      }

      document.getElementById('assign-sched-btn').onclick = () => showAssignModal(dateStr);

      // Filter Available list to this date
      filterAvailableRowsByDate(dateStr);
    }

    function clearDateFilter() {
      activeCalendarFilterDate = null;
      document.getElementById('selection-context').style.display = 'none';
      document.querySelectorAll('.cal-date').forEach(el => el.classList.remove('selected'));
      document.querySelectorAll('.available-item').forEach(el => el.style.display = 'flex');
      document.getElementById('avail-filter-notice').textContent = 'Showing active departmental consultations, solemnizations & classes';
    }

    function filterAvailableRowsByDate(dateStr) {
      setScheduleTab('available');
      let visibleCount = 0;
      document.querySelectorAll('.available-item').forEach(el => {
        const itemDate = el.getAttribute('data-date');
        if (itemDate === dateStr) {
          el.style.display = 'flex';
          visibleCount++;
        } else {
          el.style.display = 'none';
        }
      });
      document.getElementById('avail-filter-notice').textContent = `Filtered for ${dateStr} (${visibleCount} appointment${visibleCount===1?'':'s'} found)`;
    }

    function filterAvailableList() {
      const q = (document.getElementById('search-available')?.value || '').toLowerCase().trim();
      document.querySelectorAll('.available-item').forEach(el => {
        const text = el.getAttribute('data-text') || '';
        const dateMatch = !activeCalendarFilterDate || el.getAttribute('data-date') === activeCalendarFilterDate;
        const textMatch = !q || text.includes(q);
        el.style.display = (dateMatch && textMatch) ? 'flex' : 'none';
      });
    }

    function filterPendingList() {
      const q = (document.getElementById('search-pending')?.value || '').toLowerCase().trim();
      document.querySelectorAll('.pending-item').forEach(el => {
        const text = el.getAttribute('data-text') || '';
        el.style.display = (!q || text.includes(q)) ? 'flex' : 'none';
      });
    }

    // ── MODAL INTERACTIONS ──
    function showAssignModal(dateStr) {
      if (dateStr) document.getElementById('assign-date-input').value = dateStr;
      document.getElementById('assign-title').value = '';
      document.getElementById('assign-desc').value = '';
      document.getElementById('assign-modal').style.display = 'flex';
    }
    function closeAssignModal() {
      document.getElementById('assign-modal').style.display = 'none';
    }

    function showBlockModal(dateStr) {
      if (dateStr) document.getElementById('block-date-input').value = dateStr;
      document.getElementById('block-reason').value = '';
      document.getElementById('block-modal').style.display = 'flex';
    }
    function closeBlockModal() {
      document.getElementById('block-modal').style.display = 'none';
    }

    // ── BLOCK & UNBLOCK REQUESTS ──
    async function submitBlock(e) {
      e.preventDefault();
      const date = document.getElementById('block-date-input').value;
      const reason = document.getElementById('block-reason').value;

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/availability/toggle', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ date, status: 'block', reason, department: DAWAH_TYPE })
        });
        const result = await res.json();
        if (result.success) {
          showAlert('Date Blocked', `${date} has been marked as unavailable.`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showAlert('Action Failed', result.error || 'Could not block date.', 'error');
        }
      } catch(err) {
        console.error(err);
        showAlert('Error', 'Communication failed.', 'error');
      }
    }

    async function unblockDate(dateStr) {
      if (!confirm(`Are you sure you want to unblock ${dateStr} and mark it as available again?`)) return;

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/availability/toggle', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ date: dateStr, status: 'unblock', department: DAWAH_TYPE })
        });
        const result = await res.json();
        if (result.success) {
          showAlert('Date Unblocked', `${dateStr} is now marked as available.`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showAlert('Action Failed', result.error || 'Could not unblock date.', 'error');
        }
      } catch(err) {
        console.error(err);
        showAlert('Error', 'Communication failed.', 'error');
      }
    }

    // ── ASSIGN MANUAL EVENT ──
    async function submitAssignment(e) {
      e.preventDefault();
      const data = {
        date: document.getElementById('assign-date-input').value,
        title: document.getElementById('assign-title').value,
        type: document.getElementById('assign-type').value,
        time: document.getElementById('assign-time').value,
        description: document.getElementById('assign-desc').value,
        department: DAWAH_TYPE
      };

      try {
        const res = await fetch(BASE_URL + '/admin/dawah/schedule/assign', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
          closeAssignModal();
          showAlert('Schedule Assigned', `"${data.title}" has been saved.`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showAlert('Action Failed', result.error || 'Failed to save schedule.', 'error');
        }
      } catch(err) {
        console.error(err);
        showAlert('Error', 'Communication failed.', 'error');
      }
    }

    // Initialize UI
    renderCalendar();
  </script>
</body>
</html>
