<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Marriage Registration & Reservation</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    /* ── Scheduling Calendar & Booking Styles ── */
    .booking-container {
        background: #fff; border: 1px solid var(--border); border-radius: 12px;
        overflow: hidden; margin-top: 10px;
    }
    .calendar-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; background: #f9fafb; border-bottom: 1px solid var(--border);
    }
    .calendar-title { font-weight: 700; color: var(--primary-dark); font-size: 1rem; }
    .calendar-nav { display: flex; gap: 8px; }
    .btn-nav {
        width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
        background: #fff; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-nav:hover { background: var(--primary-light); border-color: var(--primary); }
    .btn-nav svg { width: 16px; height: 16px; fill: var(--text-main); }

    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); padding: 10px; }
    .weekday {
        text-align: center; font-size: 0.7rem; font-weight: 800; color: var(--text-muted);
        text-transform: uppercase; padding: 10px 0;
    }
    .calendar-day {
        aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
        font-size: 0.9rem; font-weight: 600; cursor: pointer; border-radius: 8px;
        position: relative; transition: all 0.2s;
    }
    .calendar-day:hover:not(.disabled) { background: var(--primary-light); color: var(--primary); }
    .calendar-day.today { color: var(--primary); font-weight: 800; }
    .calendar-day.today::after {
        content: ''; position: absolute; bottom: 6px; width: 4px; height: 4px;
        border-radius: 50%; background: var(--primary);
    }
    .calendar-day.selected { background: var(--primary) !important; color: #fff !important; }
    .calendar-day.selected::after {
        content: 'SELECTED'; position: absolute; bottom: 4px; font-size: 0.5rem;
        font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.9);
    }
    .calendar-day.disabled { color: #d1d5db; cursor: not-allowed; }
    .calendar-day.booked { background: #f3f4f6 !important; color: #9ca3af !important; cursor: not-allowed; }
    .calendar-day.booked::after {
        content: 'UNAVAILABLE'; position: absolute; bottom: 4px; font-size: 0.45rem;
        font-weight: 800; letter-spacing: 0.02em; color: #d1d5db;
    }

    .legend { display: flex; gap: 16px; padding: 12px 20px; font-size: 0.75rem; border-top: 1px solid var(--border); }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    .time-slots-container { padding: 20px; border-top: 1px dashed var(--border); display: none; }
    .time-slots-container.active { display: block; animation: fadeIn 0.3s ease; }
    .slot-group-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px; display: block; }
    .slots-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 20px; }
    .slot-pill {
        padding: 10px; border: 1.5px solid var(--border); border-radius: 10px;
        text-align: center; font-size: 0.85rem; font-weight: 600; cursor: pointer;
        transition: all 0.2s; display: flex; flex-direction: column; gap: 2px;
    }
    .slot-pill:hover:not(.disabled) { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }
    .slot-pill.selected { background: var(--primary); border-color: var(--primary); color: #fff; }
    .slot-pill.disabled { background: #f9fafb; color: #d1d5db; cursor: not-allowed; border-color: #f3f4f6; }

    /* ── Compact Schedule Trigger ── */
    .schedule-trigger {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: #fff; border: 1.5px solid var(--border);
        border-radius: 10px; cursor: pointer; transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .schedule-trigger:hover { border-color: var(--primary); background: var(--primary-light); }

    .trigger-info { display: flex; align-items: center; gap: 12px; }
    .trigger-icon {
        width: 36px; height: 36px; background: var(--primary-light); color: var(--primary);
        border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .trigger-icon svg { width: 20px; height: 20px; fill: currentColor; }
    .trigger-text { display: flex; flex-direction: column; }
    .trigger-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: -2px; }
    .trigger-value { font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); }
    .trigger-arrow { color: var(--text-muted); opacity: 0.5; }

    /* ── Modal Styles ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15, 30, 22, 0.4); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 9999;
        padding: 20px; animation: fadeIn 0.3s ease;
    }
    .modal-overlay.active { display: flex; }
    .modal-card {
        background: #fff; border-radius: 16px; width: 100%; max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;
        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modal-header {
        padding: 20px 24px; border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h4 { margin: 0; font-family: 'Lora', serif; color: var(--primary-dark); }
    .modal-header p { margin: 4px 0 0; font-size: 0.75rem; color: var(--text-muted); }
    
    .modal-step { display: none; animation: fadeIn 0.4s ease; }
    .modal-step.active { display: block; }
    
    .back-to-calendar {
        display: flex; align-items: center; gap: 6px; padding: 10px 0;
        color: var(--primary); font-size: 0.85rem; font-weight: 700; cursor: pointer;
        margin: 0 24px; border-bottom: 1px solid var(--primary-light);
    }
    .back-to-calendar:hover { color: var(--primary-dark); }
    .back-to-calendar svg { width: 16px; height: 16px; fill: currentColor; }

    .btn-close-modal {
        background: none; border: none; cursor: pointer; color: var(--text-muted);
        padding: 4px; border-radius: 50%; display: flex; transition: all 0.2s;
    }
    .btn-close-modal:hover { background: #f3f4f6; color: var(--danger); }
    .btn-close-modal svg { width: 20px; height: 20px; fill: currentColor; }

    .modal-footer {
        padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
        display: flex; justify-content: flex-end; gap: 12px;
    }
    .btn-confirm {
        padding: 10px 24px; background: var(--primary); color: #fff; border: none;
        border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-confirm:disabled { background: #d1d5db; cursor: not-allowed; }

    /* ── Analytics Styles ── */
    .user-analytics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { 
        background: #fff; padding: 24px; border-radius: 16px; border: 1px solid var(--border);
        display: flex; flex-direction: column; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s; cursor: default;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--primary); }
    .stat-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }
    .stat-value.warning { color: #f59e0b; }
    .stat-value.success { color: #10b981; }

    /* ── Venue & Schedule Details Cards ── */
    .approved-details-card {
        background: #f8faf9;
        border: 1.5px solid #d1fae5;
        border-radius: 14px;
        padding: 24px;
        margin-top: 20px;
        text-align: left;
    }
    .approved-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-top: 16px;
    }
    .detail-item {
        background: #fff;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--border);
    }
    .detail-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .detail-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--primary-dark);
    }

    /* ══════════════════════════════════════════════════════════ */
    /* 💍 APPROVED MARRIAGE APPOINTMENT HUB STYLES               */
    /* ══════════════════════════════════════════════════════════ */
    .marriage-hub-card {
      background: #fff; border-radius: 20px; border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(20, 83, 45, 0.07); overflow: hidden; margin-bottom: 28px;
    }
    .marriage-hero-banner {
      background: linear-gradient(135deg, #0f3d24 0%, #166534 50%, #14532D 100%);
      padding: 32px 36px; position: relative; overflow: hidden; color: #fff;
    }
    .marriage-hero-banner::after {
      content: ''; position: absolute; right: -40px; bottom: -40px; width: 220px; height: 220px;
      border-radius: 50%; background: rgba(212, 175, 55, 0.12); pointer-events: none;
    }
    .marriage-hero-banner::before {
      content: ''; position: absolute; right: 120px; top: -30px; width: 140px; height: 140px;
      border-radius: 50%; background: rgba(255, 255, 255, 0.05); pointer-events: none;
    }
    .mhero-top-row {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
      position: relative; z-index: 1; flex-wrap: wrap; margin-bottom: 24px;
    }
    .mhero-title-area { display: flex; align-items: center; gap: 18px; }
    .mhero-avatar-icon {
      width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
      border: 2px solid rgba(212, 175, 55, 0.4); flex-shrink: 0; box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .mhero-avatar-icon svg { width: 32px; height: 32px; fill: #fff; }
    .mhero-headings h3 {
      font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; margin: 0 0 4px;
      letter-spacing: -0.01em; color: #fff;
    }
    .mhero-headings p { font-size: 0.85rem; color: rgba(255, 255, 255, 0.82); margin: 0; }
    .badge-live-pulse {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px;
      border-radius: 30px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.06em; background: rgba(255, 255, 255, 0.2); border: 1.5px solid rgba(212, 175, 55, 0.6);
      backdrop-filter: blur(10px); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .pulse-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #4ade80;
      box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); animation: livePulse 1.8s infinite;
    }
    @keyframes livePulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(74, 222, 128, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }
    .mhero-schedule-strip {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
      position: relative; z-index: 1;
    }
    .msched-metric-box {
      background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 14px 16px;
      transition: all 0.2s;
    }
    .msched-metric-box:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-2px); }
    .msched-metric-lbl {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.75); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .msched-metric-val { font-size: 1rem; font-weight: 700; color: #fff; line-height: 1.3; }
    .msched-metric-val.gold { color: #fde047; }

    .marriage-toolbar {
      padding: 16px 36px; background: #fafafa; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .toolbar-left-msg {
      display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 600; color: #374151;
    }
    .toolbar-left-msg svg { width: 18px; height: 18px; fill: #14532D; flex-shrink: 0; }
    .toolbar-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .btn-action-tool {
      display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
      border-radius: 10px; font-size: 0.84rem; font-weight: 700; cursor: pointer;
      text-decoration: none; transition: all 0.2s; border: none;
    }
    .btn-action-tool svg { width: 16px; height: 16px; fill: currentColor; }
    .btn-tool-print {
      background: linear-gradient(135deg, #14532D, #0f3e21); color: #fff;
      box-shadow: 0 4px 12px rgba(20, 83, 45, 0.25);
    }
    .btn-tool-print:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(20, 83, 45, 0.35); }
    .btn-tool-gold {
      background: linear-gradient(135deg, #D4AF37, #B8860B); color: #1a1a1a;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3); font-weight: 800;
    }
    .btn-tool-gold:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4); }

    .schedule-hub-grid {
      display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; padding: 32px 36px;
    }
    .hub-card {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.03); transition: all 0.2s;
    }
    .hub-card:hover { border-color: rgba(20, 83, 45, 0.2); }
    .hub-card-title {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #111827;
      margin: 0 0 18px; display: flex; align-items: center; justify-content: space-between;
    }
    .hub-card-title span { display: flex; align-items: center; gap: 8px; }
    .hub-card-title svg { width: 18px; height: 18px; fill: #14532D; }
    .detail-table { display: flex; flex-direction: column; gap: 14px; }
    .detail-row {
      display: flex; justify-content: space-between; align-items: center;
      padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;
    }
    .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .detail-key { font-size: 0.8rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; }
    .detail-val-text { font-size: 0.92rem; font-weight: 700; color: #1f2937; text-align: right; }
    .detail-val-text.highlight { color: #14532D; font-family: 'Lora', serif; font-size: 1rem; }
    .detail-badge-pill {
      display: inline-block; padding: 4px 12px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 800; background: #f0fdf4; color: #14532D;
      border: 1px solid #dcfce7;
    }

    .checklist-list { display: flex; flex-direction: column; gap: 12px; }
    .check-item {
      display: flex; align-items: flex-start; gap: 14px; padding: 12px 14px;
      border-radius: 12px; background: #f9fafb; border: 1px solid #f3f4f6; transition: all 0.2s;
    }
    .check-item:hover { background: #f0fdf4; border-color: #dcfce7; }
    .check-num {
      width: 24px; height: 24px; border-radius: 50%; background: #14532D;
      color: #fff; display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem; font-weight: 800; flex-shrink: 0; margin-top: 1px;
    }
    .check-content strong { display: block; font-size: 0.85rem; color: #1f2937; margin-bottom: 2px; }
    .check-content p { font-size: 0.78rem; color: #6b7280; margin: 0; line-height: 1.4; }

    /* Printable Marriage Pass Modal */
    .pass-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .pass-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 600px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); max-height: 90vh; overflow-y: auto;
    }
    @media print {
      body * { visibility: hidden; }
      #printable-pass-area, #printable-pass-area * { visibility: visible; }
      #printable-pass-area { position: absolute; left: 0; top: 0; width: 100%; }
      .no-print { display: none !important; }
    }

    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'marriage_form'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Marriage Reservation & Registration</div>
        <div class="top-bar-subtitle">Schedule & reserve your solemnization ceremony with ISCAG Da'wah Department</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Marriage Booking</span>
      </div>

      <?php 
        $hasApproved = false;
        $hasPending = false;
        $activeRequest = null;
        foreach ($history ?? [] as $req) {
            if ($req['status'] === 'approved') { $hasApproved = true; $activeRequest = $req; }
            if ($req['status'] === 'pending') { $hasPending = true; $activeRequest = $req; }
        }

        $statusText = 'None';
        $statusColorClass = '';
        if ($hasPending) {
            $statusText = 'Pending';
            $statusColorClass = 'warning';
        } elseif ($hasApproved) {
            $statusText = 'Approved';
            $statusColorClass = 'success';
        }
      ?>

      <!-- RIGHT-ALIGNED STATUS BAR -->
      <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
        <div style="display: inline-flex; align-items: center; gap: 12px; background: none; padding: 10px 18px; border-radius: none; border: none;">
          <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">Status:</span>
          <?php if ($hasPending): ?>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
              <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b;"></span>
              Pending
            </span>
          <?php elseif ($hasApproved): ?>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; background: #d1fae5; color: #047857; border: 1px solid #a7f3d0;">
              <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981;"></span>
              Approved & Scheduled
            </span>
          <?php else: ?>
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;">
              <span style="width: 7px; height: 7px; border-radius: 50%; background: #9ca3af;"></span>
              None
            </span>
          <?php endif; ?>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box" style="margin-bottom:20px;">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>Marriage solemnization requests are processed by the ISCAG Da'wah Department. Once submitted, your reservation will undergo administrative verification before approval.</span>
      </div>

      <!-- MAIN BOOKING FORM CARD (Hidden if request exists) -->
      <?php if (!$hasPending && !$hasApproved): ?>
      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
            Marriage Ceremony Reservation Form
          </h6>
          <span style="font-size:0.75rem;color:var(--text-muted);">Ref No.: <strong style="color:var(--primary);">#MR-AUTO</strong></span>
        </div>
        <div class="section-card-body">
          <form id="marriage-booking-form">
            <!-- Couple Information -->
            <div class="form-section-title" style="font-size:0.9rem;font-weight:700;color:var(--primary-dark);margin-bottom:16px;">Couple Information</div>
            <div class="form-grid cols-2" style="margin-bottom:20px;">
              <div>
                <label class="form-label">Groom's Full Name <span class="required">*</span></label>
                <input type="text" name="groom_name" id="groom_name" class="form-control" placeholder="Enter Groom's complete legal name" required />
              </div>
              <div>
                <label class="form-label">Bride's Full Name <span class="required">*</span></label>
                <input type="text" name="bride_name" id="bride_name" class="form-control" placeholder="Enter Bride's complete legal name" required />
              </div>
            </div>

            <!-- Schedule & Venue Selection -->
            <div class="form-section-title" style="font-size:0.9rem;font-weight:700;color:var(--primary-dark);margin-bottom:16px;">Ceremony Venue & Schedule</div>
            <div class="form-grid cols-2" style="margin-bottom:24px;">
              <div>
                <label class="form-label">Building / Venue Name <span class="required">*</span></label>
                <input type="text" name="marriage_venue" id="marriage_venue" class="form-control" placeholder="e.g. ISCAG Main Mosque Hall" value="ISCAG Main Mosque" required />
              </div>
              <div>
                <label class="form-label">Select Preferred Schedule <span class="required">*</span></label>
                <div class="schedule-trigger" id="open-calendar">
                  <div class="trigger-info">
                    <div class="trigger-icon">
                      <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
                    </div>
                    <div class="trigger-text">
                      <span class="trigger-label">Preferred Date & Time</span>
                      <span class="trigger-value" id="selected-schedule-text">Not selected</span>
                      <input type="hidden" name="marriage_date" id="input-date" required />
                      <input type="hidden" name="marriage_time" id="input-time" required />
                    </div>
                  </div>
                  <div class="trigger-arrow">
                    <svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                  </div>
                </div>

                <!-- MODAL STRUCTURE -->
                <div class="modal-overlay" id="scheduling-modal">
                  <div class="modal-card">
                    <div class="modal-header">
                      <div>
                        <h4>Select Schedule</h4>
                        <p>Pick an available date and time for your ceremony</p>
                      </div>
                      <button type="button" class="btn-close-modal" id="close-modal"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg></button>
                    </div>

                    <!-- Step 1: Date -->
                    <div class="modal-step active" id="cal-step">
                      <div class="booking-container">
                        <div class="calendar-header">
                          <div class="calendar-title" id="cal-title">Month 2026</div>
                          <div class="calendar-nav">
                            <button type="button" class="btn-nav" onclick="changeMonth(-1)"><svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></button>
                            <button type="button" class="btn-nav" onclick="changeMonth(1)"><svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></button>
                          </div>
                        </div>
                        <div class="calendar-grid" id="calendar-grid">
                          <!-- JS Rendered -->
                        </div>
                        <div class="legend">
                          <div class="legend-item"><div class="legend-dot" style="background:var(--primary);"></div><span>Selected</span></div>
                          <div class="legend-item"><div class="legend-dot" style="background:#f3f4f6;"></div><span>Unavailable</span></div>
                        </div>
                      </div>
                    </div>

                    <!-- Step 2: Time -->
                    <div class="modal-step" id="time-step">
                      <div class="back-to-calendar" onclick="backToCal()">
                        <svg viewBox="0 0 24 24"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg>
                        Back to Date: <span id="display-selected-date"></span>
                      </div>
                      <div class="time-slots-container active">
                        <span class="slot-group-title">Available Slots</span>
                        <div class="slots-grid">
                          <div class="slot-pill" onclick="selectTime('09:00 AM')">09:00 AM</div>
                          <div class="slot-pill" onclick="selectTime('10:00 AM')">10:00 AM</div>
                          <div class="slot-pill" onclick="selectTime('11:00 AM')">11:00 AM</div>
                          <div class="slot-pill" onclick="selectTime('01:00 PM')">01:00 PM</div>
                          <div class="slot-pill" onclick="selectTime('02:00 PM')">02:00 PM</div>
                          <div class="slot-pill" onclick="selectTime('03:00 PM')">03:00 PM</div>
                        </div>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn-confirm" id="confirm-sched" disabled>Confirm Schedule</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div style="margin-bottom:32px;padding:20px;background:#f0fdf4;border:1px solid #dcfce7;border-radius:12px;">
              <div class="form-check" style="display:flex;align-items:flex-start;gap:12px;">
                <input type="checkbox" id="privacy-check" style="margin-top:3px;width:18px;height:18px;cursor:pointer;" required />
                <label for="privacy-check" style="font-size:0.95rem;color:#166534;line-height:1.6;font-weight:500;cursor:pointer;">
                  I hereby request a marriage solemnization reservation and certify that all details provided are true and accurate.
                </label>
              </div>
            </div>

            <div class="form-submit-row">
              <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Cancel</button>
              <button type="submit" class="btn-submit" id="submit-booking-btn">Submit Reservation Request</button>
            </div>
          </form>
        </div>
      </div>
      <?php elseif ($hasPending && $activeRequest): ?>

      <!-- ⏳ PENDING: UNDER REVIEW STATUS -->
      <div style="background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div style="background: linear-gradient(135deg, #14532D, #166534); padding: 28px 32px 24px; color: white; position: relative; overflow: hidden;">
          <div style="position: absolute; right: -20px; bottom: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.1);"></div>
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 16px;">
              <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: white;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </div>
              <div>
                <h5 style="font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: white; margin: 0 0 2px;">Marriage Reservation Under Review</h5>
                <p style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.7); margin: 0;">Ref No: <strong>#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Submitted on <?= date('M d, Y', strtotime($activeRequest['created_at'])) ?></p>
              </div>
            </div>
            <div style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; background: rgba(245, 158, 11, 0.25); color: #fef08a; border: 1px solid rgba(253, 224, 71, 0.4);">
              Awaiting Verification
            </div>
          </div>
        </div>

        <div style="padding: 18px 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #f9fafb; border-top: 1px solid var(--border);">
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Groom</div>
            <div style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;"><?= htmlspecialchars($activeRequest['groom_name'] ?? '—') ?></div>
          </div>
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Bride</div>
            <div style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;"><?= htmlspecialchars($activeRequest['bride_name'] ?? '—') ?></div>
          </div>
          <div style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <div style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Preferred Date</div>
            <div style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #f59e0b;"><?= isset($activeRequest['marriage_date']) ? date('M d, Y', strtotime($activeRequest['marriage_date'])) : '—' ?></div>
          </div>
        </div>

        <div style="padding: 24px 32px; text-align: center; background: #fff;">
          <p style="font-size: 0.92rem; color: #4b5563; line-height: 1.6; max-width: 600px; margin: 0 auto 16px;">
            Your marriage solemnization request has been received. The Da'wah Department is currently reviewing your reservation. You will be notified once your ceremony is officially approved and scheduled.
          </p>
          <a href="<?= url('/user/dashboard') ?>" class="btn-submit" style="display:inline-block; padding:10px 24px; text-decoration:none;">Return to Dashboard</a>
        </div>
      </div>

      <?php elseif ($hasApproved && $activeRequest): ?>

      <!-- 💍 APPROVED: MARRIAGE CEREMONY APPOINTMENT HUB -->
      <div class="marriage-hub-card">
        <!-- Hero Banner -->
        <div class="marriage-hero-banner">
          <div class="mhero-top-row">
            <div class="mhero-title-area">
              <div class="mhero-avatar-icon">
                <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </div>
              <div class="mhero-headings">
                <h3>Marriage Ceremony Confirmed</h3>
                <p>Ref No: <strong>#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Filed on <?= date('M d, Y', strtotime($activeRequest['created_at'])) ?></p>
              </div>
            </div>
            <div class="badge-live-pulse">
              <div class="pulse-dot"></div>
              Approved &amp; Scheduled
            </div>
          </div>

          <div class="mhero-schedule-strip">
            <div class="msched-metric-box">
              <div class="msched-metric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Ceremony Date
              </div>
              <div class="msched-metric-val gold"><?= date('F d, Y', strtotime($activeRequest['marriage_date'])) ?></div>
            </div>
            <div class="msched-metric-box">
              <div class="msched-metric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                Time Slot
              </div>
              <div class="msched-metric-val"><?= htmlspecialchars($activeRequest['marriage_time']) ?></div>
            </div>
            <div class="msched-metric-box">
              <div class="msched-metric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Venue
              </div>
              <div class="msched-metric-val"><?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque') ?></div>
            </div>
            <div class="msched-metric-box">
              <div class="msched-metric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                Status
              </div>
              <div class="msched-metric-val gold">Approved</div>
            </div>
          </div>
        </div>

        <!-- Action Toolbar -->
        <div class="marriage-toolbar">
          <div class="toolbar-left-msg">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            Your solemnization ceremony is confirmed. Please review the details and bring your ceremony pass on the appointed date.
          </div>
          <div class="toolbar-actions">
            <button type="button" class="btn-action-tool btn-tool-print" onclick="openPassModal()">
              <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
              Print Ceremony Pass
            </button>
            <a href="<?= url('/user/dashboard') ?>" class="btn-action-tool btn-tool-gold">
              ← Return to Dashboard
            </a>
          </div>
        </div>

        <!-- Two-Column Schedule Hub -->
        <div class="schedule-hub-grid">
          <!-- LEFT: Ceremony Details -->
          <div class="hub-card">
            <div class="hub-card-title">
              <span>
                <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                Ceremony Details
              </span>
              <span class="detail-badge-pill">Confirmed</span>
            </div>
            <div class="detail-table">
              <div class="detail-row">
                <span class="detail-key">Groom</span>
                <span class="detail-val-text highlight"><?= htmlspecialchars($activeRequest['groom_name']) ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Bride</span>
                <span class="detail-val-text highlight"><?= htmlspecialchars($activeRequest['bride_name']) ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Date</span>
                <span class="detail-val-text"><?= date('l, F d, Y', strtotime($activeRequest['marriage_date'])) ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Time</span>
                <span class="detail-val-text"><?= htmlspecialchars($activeRequest['marriage_time']) ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Venue</span>
                <span class="detail-val-text"><?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque Hall') ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Ref No.</span>
                <span class="detail-val-text">#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></span>
              </div>
              <div class="detail-row">
                <span class="detail-key">Officiant</span>
                <span class="detail-val-text">ISCAG Da'wah Ustaz</span>
              </div>
            </div>
          </div>

          <!-- RIGHT: Pre-Ceremony Checklist -->
          <div class="hub-card">
            <div class="hub-card-title">
              <span>
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Pre-Ceremony Checklist
              </span>
            </div>
            <div class="checklist-list">
              <div class="check-item">
                <div class="check-num">1</div>
                <div class="check-content">
                  <strong>Valid IDs &amp; Documents</strong>
                  <p>Bring valid government-issued IDs for both groom and bride. Prepare birth certificates and Certificate of No Marriage (CENOMAR).</p>
                </div>
              </div>
              <div class="check-item">
                <div class="check-num">2</div>
                <div class="check-content">
                  <strong>Two Witnesses Required</strong>
                  <p>Both witnesses must be present during the solemnization ceremony and bring their own valid IDs.</p>
                </div>
              </div>
              <div class="check-item">
                <div class="check-num">3</div>
                <div class="check-content">
                  <strong>Marriage License</strong>
                  <p>Ensure your marriage license from the Local Civil Registrar is valid and active on the ceremony date.</p>
                </div>
              </div>
              <div class="check-item">
                <div class="check-num">4</div>
                <div class="check-content">
                  <strong>Arrive 30 Minutes Early</strong>
                  <p>Please arrive at the venue at least 30 minutes before the scheduled time for preparation and final documentation.</p>
                </div>
              </div>
              <div class="check-item">
                <div class="check-num">5</div>
                <div class="check-content">
                  <strong>Ceremony Fee / Mahr</strong>
                  <p>Prepare any applicable ceremony fee and the agreed-upon Mahr (bridal gift). Coordinate with the Da'wah office for details.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php endif; ?>

      <!-- RECENT APPLICATIONS TABLE -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Marriage Reservations History
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Groom</th>
                  <th>Bride</th>
                  <th>Scheduled Date &amp; Time</th>
                  <th>Venue</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="history-tbody">
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No marriage reservations found.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- 🎟️ PRINTABLE CEREMONY PASS MODAL -->
<?php if ($hasApproved && $activeRequest): ?>
<div class="pass-modal-backdrop" id="pass-modal" onclick="if(event.target===this) closePassModal()">
  <div class="pass-modal-card">
    <div style="padding:20px 24px; background:#f8faf9; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;" class="no-print">
      <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark);">Marriage Ceremony Pass</h5>
      <button type="button" onclick="closePassModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280;">&times;</button>
    </div>

    <div id="printable-pass-area" style="padding:40px; background:#fff; text-align:center; position:relative; border:8px double #166534; margin:16px;">
      <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:8px;">
        <img src="<?= asset('assets/logo.jpg') ?>" style="width:50px; height:50px; border-radius:8px;" alt="ISCAG" />
        <div style="text-align:left;">
          <div style="font-family:'Lora',serif; font-size:1.15rem; font-weight:800; color:#14532D;">ISLAMIC STUDIES, CALL AND GUIDANCE</div>
          <div style="font-size:0.75rem; color:#4b5563; font-weight:600;">Da'wah Department — Marriage Solemnization</div>
        </div>
      </div>

      <div style="margin:24px 0 16px;">
        <h2 style="font-family:'Lora',serif; font-size:1.5rem; font-weight:700; color:#14532D; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 6px;">Ceremony Appointment Pass</h2>
        <div style="font-size:0.8rem; font-weight:700; color:#B8860B; text-transform:uppercase; letter-spacing:0.12em;">Ref: #MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; text-align:left; margin:20px 0;">
        <div style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:10px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Groom</div>
          <div style="font-size:1rem; font-weight:700; color:#14532D;"><?= htmlspecialchars($activeRequest['groom_name']) ?></div>
        </div>
        <div style="background:#f0fdf4; border:1px solid #dcfce7; border-radius:10px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Bride</div>
          <div style="font-size:1rem; font-weight:700; color:#14532D;"><?= htmlspecialchars($activeRequest['bride_name']) ?></div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; text-align:left; margin:16px 0 24px;">
        <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Date</div>
          <div style="font-size:0.9rem; font-weight:700; color:#1a1a1a;"><?= date('F d, Y', strtotime($activeRequest['marriage_date'])) ?></div>
        </div>
        <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Time</div>
          <div style="font-size:0.9rem; font-weight:700; color:#1a1a1a;"><?= htmlspecialchars($activeRequest['marriage_time']) ?></div>
        </div>
        <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280;">Venue</div>
          <div style="font-size:0.9rem; font-weight:700; color:#1a1a1a;"><?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Mosque') ?></div>
        </div>
      </div>

      <p style="font-size:0.82rem; color:#6b7280; line-height:1.5; margin-bottom:24px;">
        Please present this pass at the venue on the date of the ceremony. Both parties and two witnesses must be present with valid identification.
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-top:32px; text-align:center;">
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">Da'wah Director / Ustaz</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Da'wah Department</div>
        </div>
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">Authorized Official</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Philippines</div>
        </div>
      </div>
    </div>

    <div style="padding:16px 24px; background:#f8faf9; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;" class="no-print">
      <button type="button" onclick="closePassModal()" class="btn-cancel">Close</button>
      <button type="button" onclick="window.print()" class="btn-submit" style="background:#166534;">Print Document</button>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
  // ── Render History ──
  const historyTbody = document.getElementById('history-tbody');
  const historyData = <?= json_encode($history ?? []) ?>;

  if (historyData.length > 0) {
    historyTbody.innerHTML = historyData.map(h => `
      <tr>
        <td class="td-id">#MR-${String(h.id).padStart(4, '0')}</td>
        <td>${h.groom_name || '—'}</td>
        <td>${h.bride_name || '—'}</td>
        <td>${h.marriage_date ? new Date(h.marriage_date).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : ''} at ${h.marriage_time || ''}</td>
        <td>${h.marriage_venue || 'ISCAG Mosque'}</td>
        <td><span class="badge-status badge-${h.status}">${h.status}</span></td>
      </tr>
    `).join('');
  }

  // ── Scheduling Modal Logic ──
  const BLOCKED_DATES = <?= json_encode($blockedDates ?? []) ?>;
  let currentMonth = new Date().getMonth();
  let currentYear = new Date().getFullYear();
  let selectedDate = null;
  let selectedTime = null;

  const modal = document.getElementById('scheduling-modal');
  const openBtn = document.getElementById('open-calendar');
  const closeBtn = document.getElementById('close-modal');
  const calGrid = document.getElementById('calendar-grid');
  const calTitle = document.getElementById('cal-title');
  const timeStep = document.getElementById('time-step');
  const calStep = document.getElementById('cal-step');
  const confirmBtn = document.getElementById('confirm-sched');

  if(openBtn) openBtn.onclick = () => { modal.classList.add('active'); renderCalendar(); };
  if(closeBtn) closeBtn.onclick = () => closeModal();

  function closeModal() {
    if(modal) {
      modal.classList.remove('active');
      if(calStep) calStep.classList.add('active');
      if(timeStep) timeStep.classList.remove('active');
    }
  }

  function renderCalendar() {
    if(!calGrid) return;
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    
    calTitle.innerText = `${monthNames[currentMonth]} ${currentYear}`;
    calGrid.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(d => `<div class="weekday">${d}</div>`).join('');
    
    for (let i = 0; i < firstDay; i++) {
      calGrid.innerHTML += `<div class="calendar-day disabled"></div>`;
    }
    
    const todayStr = new Date().toISOString().split('T')[0];
    
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const blockReason = BLOCKED_DATES[dateStr];
      const isBlocked = !!blockReason;
      const isPast = dateStr < todayStr;
      const isToday = dateStr === todayStr;
      
      calGrid.innerHTML += `
        <div class="calendar-day ${isBlocked ? 'booked' : ''} ${isPast ? 'disabled' : ''} ${isToday ? 'today' : ''}" 
             title="${isBlocked ? 'Unavailable: ' + blockReason : ''}"
             onclick="${isBlocked ? `showBlockReason('${blockReason}')` : ((!isPast) ? `selectDate('${dateStr}')` : '')}">
          ${day}
        </div>
      `;
    }
  }

  window.showBlockReason = (reason) => {
    alert(`This date is unavailable: ${reason}`);
  };

  window.selectDate = (date) => {
    selectedDate = date;
    if(calStep) calStep.classList.remove('active');
    if(timeStep) timeStep.classList.add('active');
    const el = document.getElementById('display-selected-date');
    if(el) el.innerText = new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  };

  window.selectTime = (time) => {
    selectedTime = time;
    document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    if(confirmBtn) confirmBtn.disabled = false;
  };

  window.backToCal = () => {
    if(timeStep) timeStep.classList.remove('active');
    if(calStep) calStep.classList.add('active');
  };

  if(confirmBtn) confirmBtn.onclick = () => {
    const schedText = document.getElementById('selected-schedule-text');
    const inputDate = document.getElementById('input-date');
    const inputTime = document.getElementById('input-time');
    if(schedText) schedText.innerText = `${new Date(selectedDate).toLocaleDateString()} at ${selectedTime}`;
    if(inputDate) inputDate.value = selectedDate;
    if(inputTime) inputTime.value = selectedTime;
    closeModal();
  };

  window.changeMonth = (delta) => {
    currentMonth += delta;
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    else if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    renderCalendar();
  };

  // ── Booking Form Submission ──
  const bookingForm = document.getElementById('marriage-booking-form');
  if (bookingForm) {
    bookingForm.onsubmit = async (e) => {
      e.preventDefault();
      
      const formData = {
        groom_name: document.getElementById('groom_name').value,
        bride_name: document.getElementById('bride_name').value,
        marriage_venue: document.getElementById('marriage_venue').value,
        marriage_date: document.getElementById('input-date').value,
        marriage_time: document.getElementById('input-time').value
      };

      if (!formData.marriage_date || !formData.marriage_time) {
        showToast('Please select a date and time schedule.', '#e67e22');
        return;
      }

      try {
        const res = await fetch('<?= url('/user/services/marriage/submit') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(formData)
        });
        const data = await res.json();
        if (data.success) {
          showToast('Reservation submitted successfully!');
          setTimeout(() => location.reload(), 1200);
        } else {
          showToast(data.message || 'Failed to submit reservation.', '#e74c3c');
        }
      } catch(err) {
        showToast('An error occurred. Please try again.', '#e74c3c');
      }
    };
  }

  // ── Pass Modal ──
  function openPassModal() {
    const m = document.getElementById('pass-modal');
    if(m) m.style.display = 'flex';
  }
  function closePassModal() {
    const m = document.getElementById('pass-modal');
    if(m) m.style.display = 'none';
  }

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + (bg || 'var(--primary)') + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:inherit;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;animation:fadeIn 0.3s ease;';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
  }
</script>
</body>
</html>
