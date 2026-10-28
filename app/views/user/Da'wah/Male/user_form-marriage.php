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
    /*  APPROVED MARRIAGE APPOINTMENT HUB STYLES               */
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

    /* ══════════════════════════════════════════════════════════ */
    /*  APPROVED MARRIAGE CEREMONY SCHEDULE CALENDAR          */
    /* ══════════════════════════════════════════════════════════ */
    .marriage-calendar-hub-card {
      background: #fff; border: 1.5px solid #fed7aa; border-radius: 18px;
      padding: 24px 28px; box-shadow: 0 4px 20px rgba(180, 83, 9, 0.05); margin-bottom: 24px;
    }
    .marriage-cal-layout {
      display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px; align-items: stretch;
    }
    .marriage-cal-container {
      background: #fdfbf7; border: 1px solid #fed7aa; border-radius: 14px;
      padding: 18px; display: flex; flex-direction: column;
    }
    .marriage-cal-top {
      display: flex; justify-content: space-between; align-items: center; gap: 10px;
      margin-bottom: 14px; flex-wrap: wrap;
    }
    .marriage-cal-month-nav {
      display: flex; align-items: center; gap: 8px;
    }
    .marriage-cal-month-name {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #9a3412;
      min-width: 140px; text-align: center;
    }
    .btn-mcal-step {
      width: 32px; height: 32px; border-radius: 8px; border: 1px solid #fed7aa;
      background: #fff; display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all 0.2s; color: var(--text-main);
    }
    .btn-mcal-step:hover { background: #fffbeb; border-color: #d97706; color: #b45309; }
    .btn-jump-ceremony {
      display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
      background: #fff; border: 1px solid #fed7aa; border-radius: 8px; font-size: 0.75rem;
      font-weight: 700; color: #b45309; cursor: pointer; transition: all 0.2s;
    }
    .btn-jump-ceremony:hover { background: #fef3c7; border-color: #f59e0b; }
    
    .marriage-weekdays-row {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center;
      font-size: 0.7rem; font-weight: 800; color: #9ca3af; text-transform: uppercase;
      padding-bottom: 8px; border-bottom: 1px solid #fed7aa; margin-bottom: 8px;
    }
    .marriage-days-grid {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;
    }
    .marriage-cal-day {
      aspect-ratio: 1; border-radius: 8px; display: flex; flex-direction: column;
      align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600;
      cursor: pointer; transition: all 0.2s; position: relative; border: 1px solid transparent;
      color: #374151; background: #fff;
    }
    .marriage-cal-day:hover:not(.empty) { background: #fffbeb; border-color: #fde68a; }
    .marriage-cal-day.empty { background: transparent; cursor: default; }
    .marriage-cal-day.today { font-weight: 800; color: #b45309; }
    .marriage-cal-day.today::after {
      content: ''; position: absolute; bottom: 3px; width: 4px; height: 4px;
      border-radius: 50%; background: #b45309;
    }
    .marriage-cal-day.has-ceremony {
      background: linear-gradient(135deg, #14532D, #0f3e21) !important; color: #fff !important; font-weight: 800;
      border-color: #f59e0b !important; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.35);
    }
    .marriage-cal-day.has-ceremony::after {
      content: ''; position: absolute; bottom: 3px; width: 6px; height: 6px;
      border-radius: 50%; background: #fde047; box-shadow: 0 0 0 2px #14532D;
    }
    .marriage-cal-day.selected-view:not(.has-ceremony) {
      border-color: #f59e0b; background: #fffbeb; font-weight: 800;
    }

    /* Marriage Inspector Card */
    .marriage-session-inspector {
      background: linear-gradient(135deg, #fffbeb, #fff); border: 1.5px solid #fed7aa;
      border-radius: 14px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;
    }
    .minspector-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #d1fae5; color: #047857; border: 1px solid #a7f3d0;
      align-self: flex-start; margin-bottom: 8px;
    }
    .minspector-date {
      font-family: 'Lora', serif; font-size: 1.15rem; font-weight: 700; color: #14532D; margin: 0 0 14px;
    }
    .minspector-info-list {
      display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;
    }
    .minspector-item {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;
      padding-bottom: 8px; border-bottom: 1px dashed #fed7aa; font-size: 0.82rem;
    }
    .minspector-item:last-child { border-bottom: none; padding-bottom: 0; }
    .minspector-item .mitem-label { color: #6b7280; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; }
    .minspector-item .mitem-val { color: #1f2937; font-weight: 700; text-align: right; }
    .minspector-item .mitem-val.highlight { color: #b45309; font-family: 'Lora', serif; font-weight: 700; }
    .minspector-actions {
      display: flex; gap: 10px; flex-wrap: wrap; margin-top: auto; padding-top: 12px;
    }
    .btn-minspector-action {
      flex: 1; padding: 8px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 700;
      cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
      justify-content: center; gap: 6px; transition: all 0.2s; border: none;
    }
    .btn-minspector-action.btn-mprint { background: var(--primary); color: #fff; }
    .btn-minspector-action.btn-mprint:hover { background: var(--primary-dark); }
    .btn-minspector-action.btn-mcal { background: #fff; color: #374151; border: 1.5px solid var(--border); }
    .btn-minspector-action.btn-mcal:hover { background: #f9fafb; border-color: #9ca3af; }

    /* ══════════════════════════════════════════════════════════ */
    /*  MARRIAGE PENDING MODAL STYLES                           */
    /* ══════════════════════════════════════════════════════════ */
    .marriage-pending-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 540px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden;
      animation: modalPop 0.35s ease; border: 1px solid rgba(0,0,0,0.06);
    }
    .marriage-pending-header {
      background: linear-gradient(135deg, #14532D, #0f3d24); padding: 24px 28px;
      color: #fff; display: flex; align-items: center; gap: 14px; position: relative;
    }
    .marriage-icon-wrap {
      width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.15);
      border: 1.5px solid rgba(212, 175, 55, 0.4); display: flex; align-items: center;
      justify-content: center; flex-shrink: 0;
    }
    .marriage-icon-wrap svg { width: 26px; height: 26px; fill: #fff; }
    .marriage-tag {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
      color: #fde047; margin-bottom: 2px;
    }
    .marriage-modal-title {
      font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; margin: 0 0 2px; color: #fff;
    }
    .marriage-modal-sub { font-size: 0.78rem; color: rgba(255,255,255,0.8); margin: 0; }
    .marriage-pending-body { padding: 24px 28px; }
    .marriage-status-banner {
      display: flex; align-items: center; gap: 8px; padding: 10px 16px;
      border-radius: 10px; background: #fef3c7; border: 1px solid #fde68a;
      font-size: 0.82rem; color: #92400e; margin-bottom: 16px;
    }
    .status-pulse-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); animation: warningPulse 1.8s infinite;
    }
    .marriage-primary-msg {
      font-size: 0.88rem; color: #4b5563; line-height: 1.5; margin: 0 0 16px;
    }
    .marriage-info-callout {
      display: flex; gap: 12px; background: #f0fdf4; border: 1px solid #bbf7d0;
      border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;
    }
    .mcallout-icon {
      width: 32px; height: 32px; border-radius: 8px; background: #dcfce7;
      color: #166534; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .mcallout-icon svg { width: 18px; height: 18px; fill: currentColor; }
    .mcallout-content strong {
      display: block; font-size: 0.82rem; color: #166534; margin-bottom: 2px;
    }
    .mcallout-content p {
      font-size: 0.78rem; color: #15803d; margin: 0; line-height: 1.45;
    }
    .marriage-summary-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .marriage-summary-item {
      background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 10px 12px;
    }
    .marriage-summary-item .mlbl {
      display: block; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
      color: #9ca3af; margin-bottom: 2px;
    }
    .marriage-summary-item .mval {
      font-size: 0.85rem; font-weight: 700; color: #111827;
    }
    .marriage-modal-footer {
      padding: 16px 28px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-mpending-sec {
      padding: 9px 18px; background: #fff; border: 1.5px solid var(--border);
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #4b5563;
      text-decoration: none; cursor: pointer; transition: all 0.2s;
    }
    .btn-mpending-sec:hover { background: #f3f4f6; color: #111827; }
    .btn-mpending-pri {
      padding: 9px 20px; background: var(--primary); border: none;
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #fff;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-mpending-pri:hover { background: var(--primary-dark); }
    
    /* ══════════════════════════════════════════════════════════ */
    /*  VISUAL MARRIAGE PENDING RESERVATION HUB                 */
    /* ══════════════════════════════════════════════════════════ */
    .marriage-pending-hub-card {
      background: #fff; border-radius: 20px; border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(20, 83, 45, 0.08); overflow: hidden; margin-bottom: 28px;
    }
    .marriage-pending-hero-banner {
      background: linear-gradient(135deg, #064e3b 0%, #14532D 60%, #0f3d24 100%);
      padding: 32px 36px 28px; position: relative; overflow: hidden; color: #fff;
    }
    .marriage-pending-hero-banner::after {
      content: ''; position: absolute; right: -40px; bottom: -40px; width: 240px; height: 240px;
      border-radius: 50%; background: radial-gradient(circle, rgba(212, 175, 55, 0.16) 0%, transparent 70%); pointer-events: none;
    }
    .marriage-pending-hero-banner::before {
      content: ''; position: absolute; right: 140px; top: -30px; width: 150px; height: 150px;
      border-radius: 50%; background: rgba(255, 255, 255, 0.05); pointer-events: none;
    }
    .marriage-pending-top-row {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
      position: relative; z-index: 1; flex-wrap: wrap; margin-bottom: 24px;
    }
    .marriage-pending-title-area { display: flex; align-items: center; gap: 18px; }
    .marriage-pending-avatar-icon {
      width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
      border: 2px solid rgba(212, 175, 55, 0.5); flex-shrink: 0; box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }
    .marriage-pending-avatar-icon svg { width: 32px; height: 32px; fill: #fff; }
    .marriage-pending-headings h3 {
      font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; margin: 0 0 4px;
      letter-spacing: -0.01em; color: #fff;
    }
    .marriage-pending-headings p {
      font-size: 0.85rem; color: rgba(255, 255, 255, 0.85); margin: 0;
    }
    .marriage-badge-pending-pulse {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px;
      border-radius: 30px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.06em; background: rgba(245, 158, 11, 0.22); border: 1.5px solid rgba(253, 224, 71, 0.5);
      backdrop-filter: blur(10px); color: #fef08a; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Metric highlights strip */
    .marriage-pending-schedule-strip {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
      position: relative; z-index: 1;
    }
    .marriage-pmetric-box {
      background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 14px 16px;
      transition: all 0.2s;
    }
    .marriage-pmetric-box:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-2px); }
    .marriage-pmetric-lbl {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.75); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .marriage-pmetric-val {
      font-size: 0.95rem; font-weight: 700; color: #fff; line-height: 1.3;
    }
    .marriage-pmetric-val.gold { color: #fde047; }

    /* Pending 2-Column Hub Grid */
    .marriage-pending-hub-grid {
      display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; padding: 32px 36px;
    }

    /* Calendar Preview Teaser Card */
    .marriage-calendar-teaser-card {
      background: linear-gradient(135deg, #fefce8, #f0fdf4); border: 1.5px dashed #f59e0b;
      border-radius: 16px; padding: 22px 24px; margin-bottom: 24px; position: relative;
    }
    .marriage-calendar-teaser-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
      margin-bottom: 12px;
    }
    .marriage-calendar-teaser-content {
      display: flex; gap: 16px; align-items: center;
    }
    .marriage-calendar-teaser-icon {
      width: 52px; height: 52px; border-radius: 14px; background: #fff;
      border: 2px solid #fde68a; display: flex; align-items: center; justify-content: center;
      color: #d97706; flex-shrink: 0; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
    }
    .marriage-calendar-teaser-icon svg { width: 28px; height: 28px; fill: currentColor; }
    .marriage-calendar-teaser-text h5 {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #14532D; margin: 0 0 4px;
    }
    .marriage-calendar-teaser-text p {
      font-size: 0.82rem; color: #4b5563; margin: 0; line-height: 1.5;
    }

    /* Enhanced 3-Phase Step Tracker */
    .marriage-pending-journey-wrap {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; margin-bottom: 24px;
    }
    .marriage-pending-journey-steps {
      display: flex; justify-content: space-between; position: relative; margin-top: 14px;
    }
    .marriage-pending-journey-steps::before {
      content: ''; position: absolute; top: 22px; left: 12%; right: 12%; height: 3px;
      background: #e5e7eb; z-index: 1;
    }
    .marriage-pjourney-step {
      display: flex; flex-direction: column; align-items: center; position: relative;
      z-index: 2; text-align: center; flex: 1;
    }
    .marriage-pjourney-circle {
      width: 44px; height: 44px; border-radius: 50%; background: #f9fafb;
      border: 3px solid #e5e7eb; display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 0.9rem; color: #9ca3af; margin-bottom: 8px; transition: all 0.3s;
    }
    .marriage-pjourney-step.done .marriage-pjourney-circle {
      background: var(--primary); border-color: var(--primary); color: #fff;
    }
    .marriage-pjourney-step.active .marriage-pjourney-circle {
      background: #fff; border-color: #f59e0b; color: #d97706;
      box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.2);
    }
    .marriage-pjourney-title { font-size: 0.82rem; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
    .marriage-pjourney-desc { font-size: 0.70rem; color: #6b7280; line-height: 1.3; }

    /* Marriage Checklist Card */
    .marriage-checklist-box {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.03); margin-bottom: 24px;
    }
    .marriage-checklist-item {
      display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; font-size: 0.85rem;
    }
    .marriage-checklist-item:last-child { margin-bottom: 0; }
    .marriage-bullet-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #166534;
      flex-shrink: 0; margin-top: 6px; box-shadow: 0 0 0 3px #dcfce7;
    }
    .marriage-checklist-item strong { display: block; color: #1f2937; margin-bottom: 2px; }
    .marriage-checklist-item p { margin: 0; color: #6b7280; font-size: 0.78rem; line-height: 1.4; }

    /* Office & Sunnah Cards */
    .marriage-office-desk-card {
      background: linear-gradient(to bottom, #fdfbf7, #fff); border: 1px solid #fed7aa;
      border-radius: 16px; padding: 22px 24px; margin-bottom: 24px;
    }
    .marriage-sunnah-box {
      background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 16px; padding: 20px 22px;
    }
    .marriage-sunnah-box h5 {
      font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D; margin: 0 0 6px;
      display: flex; align-items: center; gap: 8px;
    }
    .marriage-sunnah-box p {
      font-size: 0.82rem; color: #166534; line-height: 1.5; margin: 0; font-style: italic;
    }
    
    @media (max-width: 960px) {
      .marriage-cal-layout { grid-template-columns: 1fr !important; }
      .marriage-summary-grid { grid-template-columns: 1fr !important; }
      .marriage-pending-hub-grid { grid-template-columns: 1fr !important; padding: 20px !important; }
      .marriage-pending-schedule-strip { grid-template-columns: 1fr 1fr !important; }
      .marriage-pending-hero-banner { padding: 24px 20px !important; }
    }
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
                      <input type="hidden" name="marriage_date" id="input-date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required />
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

      <!--  PENDING: VISUAL MARRIAGE RESERVATION HUB -->
      <div class="marriage-pending-hub-card">
        <!-- Hero Banner with Islamic Deep Emerald & Gold Accents -->
        <div class="marriage-pending-hero-banner">
          <div class="marriage-pending-top-row">
            <div class="marriage-pending-title-area">
              <div class="marriage-pending-avatar-icon">
                <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              </div>
              <div class="marriage-pending-headings">
                <h3>Marriage Solemnization Reservation</h3>
                <p>Booking Ref: <strong>#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Filed on <?= date('M d, Y', strtotime($activeRequest['created_at'])) ?> • Da'wah Marriage Bureau</p>
              </div>
            </div>
            <div class="marriage-badge-pending-pulse">
              <span class="pulse-dot-amber"></span>
              Awaiting Document &amp; Officiant Verification
            </div>
          </div>

          <!-- Glassmorphism 4-Metric Strip -->
          <div class="marriage-pending-schedule-strip">
            <div class="marriage-pmetric-box">
              <div class="marriage-pmetric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Groom
              </div>
              <div class="marriage-pmetric-val"><?= htmlspecialchars($activeRequest['groom_name'] ?? '—') ?></div>
            </div>
            <div class="marriage-pmetric-box">
              <div class="marriage-pmetric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Bride
              </div>
              <div class="marriage-pmetric-val"><?= htmlspecialchars($activeRequest['bride_name'] ?? '—') ?></div>
            </div>
            <div class="marriage-pmetric-box">
              <div class="marriage-pmetric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Requested Date
              </div>
              <div class="marriage-pmetric-val gold"><?= isset($activeRequest['marriage_date']) ? date('M d, Y', strtotime($activeRequest['marriage_date'])) : '—' ?></div>
            </div>
            <div class="marriage-pmetric-box">
              <div class="marriage-pmetric-lbl">
                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Time &amp; Venue
              </div>
              <div class="marriage-pmetric-val"><?= htmlspecialchars($activeRequest['marriage_time'] ?? 'TBD') ?> • <?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque') ?></div>
            </div>
          </div>
        </div>

        <!-- Action Toolbar -->
        <div class="marriage-toolbar">
          <div class="toolbar-left-msg">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            Your solemnization request is currently undergoing review and schedule clearance by the ISCAG Da'wah Department.
          </div>
          <div class="toolbar-actions">
            <a href="<?= url('/user/dashboard') ?>" class="btn-action-tool btn-tool-gold">
              ← Return to Dashboard
            </a>
            <button type="button" class="btn-action-tool" style="background:#fff; border:1.5px solid var(--border); color:#374151;" onclick="document.querySelector('.section-card').scrollIntoView({behavior:'smooth'})">
              View History
            </button>
          </div>
        </div>

        <!-- 2-Column Pending Details Grid -->
        <div class="marriage-pending-hub-grid">
          <!-- LEFT COLUMN: Calendar Teaser, 3-Phase Journey, Details -->
          <div>
            <!-- Interactive Schedule Calendar Teaser Card -->
            <div class="marriage-calendar-teaser-card">
              <div class="marriage-calendar-teaser-badge">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                Ceremony Schedule Calendar • Unlocks on Approval
              </div>
              <div class="marriage-calendar-teaser-content">
                <div class="marriage-calendar-teaser-icon">
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                </div>
                <div class="marriage-calendar-teaser-text">
                  <h5>Interactive Ceremony Schedule Calendar</h5>
                  <p>
                    Once the ISCAG Da'wah Administrator verifies your documents and assigns the solemnizing Ustaz, the <strong>Official Marriage Schedule Calendar</strong> and <strong>Printable Ceremony Pass</strong> will automatically unlock right here on your portal.
                  </p>
                </div>
              </div>
            </div>

            <!-- 3-Phase Journey Tracker -->
            <div class="marriage-pending-journey-wrap">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-family:'Lora',serif; font-size:1.05rem; font-weight:700; color:#111827;">Verification &amp; Scheduling Workflow</span>
                <span class="detail-badge-pill" style="background:#fef3c7; color:#92400e; font-weight:800; border:1px solid #fde68a;">Phase 2 in Progress</span>
              </div>
              <div class="marriage-pending-journey-steps">
                <div class="marriage-pjourney-step done">
                  <div class="marriage-pjourney-circle">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <div class="marriage-pjourney-title">Request Received</div>
                  <div class="marriage-pjourney-desc">Form &amp; details logged into ISCAG MIS</div>
                </div>
                <div class="marriage-pjourney-step active">
                  <div class="marriage-pjourney-circle">2</div>
                  <div class="marriage-pjourney-title">Document &amp; Schedule Review</div>
                  <div class="marriage-pjourney-desc">Da'wah Ustaz reviewing documents &amp; venue</div>
                </div>
                <div class="marriage-pjourney-step">
                  <div class="marriage-pjourney-circle">3</div>
                  <div class="marriage-pjourney-title">Calendar &amp; Ceremony Pass</div>
                  <div class="marriage-pjourney-desc">Unlocks printable pass &amp; confirmed calendar</div>
                </div>
              </div>
            </div>

            <!-- Couple & Booking Information Card -->
            <div class="hub-card">
              <div class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  Reservation Specification
                </span>
                <span class="detail-badge-pill" style="background:#e0f2fe; color:#0369a1; font-weight:800; border:1px solid #bae6fd;">Nikah Solemnization</span>
              </div>
              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Reference Number</span>
                  <span class="detail-val highlight">#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Groom Legal Name</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['groom_name']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Bride Legal Name</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['bride_name']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Requested Ceremony Date</span>
                  <span class="detail-val"><?= isset($activeRequest['marriage_date']) ? date('l, F d, Y', strtotime($activeRequest['marriage_date'])) : '—' ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Preferred Time Slot</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['marriage_time'] ?? 'Morning Slot') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Assigned Solemnization Venue</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Application Submission Date</span>
                  <span class="detail-val"><?= date('F d, Y • h:i A', strtotime($activeRequest['created_at'])) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Assigned Processing Bureau</span>
                  <span class="detail-val" style="color:var(--primary); font-weight:800;">ISCAG Da'wah Department (Marriage Section)</span>
                </div>
              </div>
            </div>
          </div>

          <!-- RIGHT COLUMN: Document Checklist, Office Card & Sunnah Blessing -->
          <div>
            <!-- Required Document Checklist Card -->
            <div class="marriage-checklist-box">
              <div class="hub-card-title" style="margin-bottom:14px;">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                  Documents Required on Ceremony
                </span>
                <span class="detail-badge-pill" style="background:#fef3c7; color:#92400e; font-weight:800; border:1px solid #fde68a;">Prepare Prior</span>
              </div>
              <div class="marriage-checklist-item">
                <div class="marriage-bullet-dot"></div>
                <div>
                  <strong>Valid Identification Cards</strong>
                  <p>Government IDs (Passport, PhilSys, Driver's License) for groom and bride.</p>
                </div>
              </div>
              <div class="marriage-checklist-item">
                <div class="marriage-bullet-dot"></div>
                <div>
                  <strong>PSA Birth Certificates &amp; CENOMAR</strong>
                  <p>Certificate of No Marriage record from Philippine Statistics Authority.</p>
                </div>
              </div>
              <div class="marriage-checklist-item">
                <div class="marriage-bullet-dot"></div>
                <div>
                  <strong>Two Adult Male Witnesses (Shuhood)</strong>
                  <p>Both witnesses must be practicing Muslim males with valid photo IDs.</p>
                </div>
              </div>
              <div class="marriage-checklist-item">
                <div class="marriage-bullet-dot"></div>
                <div>
                  <strong>Agreed Mahr (Bridal Gift)</strong>
                  <p>Specify the confirmed Mahr (prompt or deferred) agreed between both parties.</p>
                </div>
              </div>
            </div>

            <!-- Da'wah Marriage Office Desk Card -->
            <div class="marriage-office-desk-card">
              <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fef3c7; display:flex; align-items:center; justify-content:center; color:#d97706; flex-shrink:0;">
                  <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:currentColor;"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                </div>
                <div>
                  <h5 style="margin:0 0 2px; font-size:0.95rem; font-weight:700; color:#1f2937;">Da'wah Department Office</h5>
                  <p style="margin:0; font-size:0.75rem; color:#b45309; font-weight:600;">Marriage Secretariat Desk</p>
                </div>
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; font-size:0.8rem; color:#4b5563; line-height:1.4;">
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-weight:700; color:#1f2937; min-width:70px;">Location:</span>
                  <span>Room 102, ISCAG Islamic Complex</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-weight:700; color:#1f2937; min-width:70px;">Hours:</span>
                  <span>Mon – Sat: 8:00 AM – 5:00 PM</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-weight:700; color:#1f2937; min-width:70px;">Officiant:</span>
                  <span>Accredited ISCAG Islamic Solemnizer</span>
                </div>
              </div>
            </div>

            <!-- Islamic Sunnah Box -->
            <div class="marriage-sunnah-box">
              <h5>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                The Blessing of Nikah
              </h5>
              <p>
                "Marriage is from my Sunnah, and whoever does not follow my Sunnah is not from me."<br />
                <span style="font-size:0.72rem; color:#15803d; font-weight:700; text-transform:uppercase;">— Sunan Ibn Majah 1846</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <?php elseif ($hasApproved && $activeRequest): ?>

      <!--  APPROVED: MARRIAGE CEREMONY APPOINTMENT HUB -->
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

        <!--  DEDICATED CEREMONY SCHEDULE CALENDAR (APPEARS WHEN ADMIN APPROVES) -->
        <div style="padding: 28px 36px 0;">
          <div class="hub-card marriage-calendar-hub-card">
            <div class="hub-card-title">
              <span>
                <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Marriage Ceremony Schedule Calendar
              </span>
              <span class="detail-badge-pill" style="background:#d1fae5; color:#047857; font-weight:800;">
                Confirmed &amp; Scheduled by Administrator
              </span>
            </div>

            <div class="marriage-cal-layout">
              <!-- Calendar Left: Interactive Monthly Grid -->
              <div class="marriage-cal-container">
                <div class="marriage-cal-top">
                  <div class="marriage-cal-month-nav">
                    <button type="button" class="btn-mcal-step" onclick="navApprovedMarriageCal(-1)" title="Previous Month">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                    </button>
                    <span id="approved-cal-title-marriage" class="marriage-cal-month-name">Loading Month...</span>
                    <button type="button" class="btn-mcal-step" onclick="navApprovedMarriageCal(1)" title="Next Month">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </button>
                  </div>
                  <button type="button" class="btn-jump-ceremony" onclick="jumpToApprovedCeremony()">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    Jump to Ceremony Date
                  </button>
                </div>

                <div class="marriage-weekdays-row">
                  <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
                <div id="approved-cal-grid-marriage" class="marriage-days-grid"></div>
              </div>

              <!-- Calendar Right: Ceremony Details Inspector -->
              <div class="marriage-session-inspector">
                <div>
                  <div class="minspector-badge">
                    <span class="pulse-dot"></span>
                    <span id="minspector-status-text">Approved Solemnization</span>
                  </div>
                  <h4 class="minspector-date" id="minspector-date-text"><?= date('l, F d, Y', strtotime($activeRequest['marriage_date'])) ?></h4>
                  
                  <div class="minspector-info-list">
                    <div class="minspector-item">
                      <span class="mitem-label">Ceremony Time</span>
                      <span class="mitem-val" id="minspector-time-text"><?= htmlspecialchars($activeRequest['marriage_time']) ?></span>
                    </div>
                    <div class="minspector-item">
                      <span class="mitem-label">Couple</span>
                      <span class="mitem-val highlight"><?= htmlspecialchars($activeRequest['groom_name']) ?> &amp; <?= htmlspecialchars($activeRequest['bride_name']) ?></span>
                    </div>
                    <div class="minspector-item">
                      <span class="mitem-label">Solemnization Venue</span>
                      <span class="mitem-val" id="minspector-venue-text"><?= htmlspecialchars($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque') ?></span>
                    </div>
                    <div class="minspector-item">
                      <span class="mitem-label">Officiant Body</span>
                      <span class="mitem-val">ISCAG Da'wah Department</span>
                    </div>
                    <div class="minspector-item">
                      <span class="mitem-label">Reference Number</span>
                      <span class="mitem-val" style="font-family:monospace;">#MR-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    </div>
                  </div>
                </div>

                <div class="minspector-actions">
                  <button type="button" class="btn-minspector-action btn-mprint" onclick="openPassModal()">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                    Print Pass
                  </button>
                  <?php 
                    $mcalDate = date('Ymd', strtotime($activeRequest['marriage_date']));
                    $mcalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode("ISCAG Marriage Ceremony — " . $activeRequest['groom_name'] . " & " . $activeRequest['bride_name']) . "&dates=" . $mcalDate . "T010000Z/" . $mcalDate . "T030000Z&details=" . urlencode("ISCAG Islamic Marriage Solemnization Ceremony. Ref: #MR-" . str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT)) . "&location=" . urlencode($activeRequest['marriage_venue'] ?? 'ISCAG Main Mosque');
                  ?>
                  <a href="<?= $mcalUrl ?>" target="_blank" class="btn-minspector-action btn-mcal">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
                    Google Cal
                  </a>
                </div>
              </div>
            </div>
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

<!-- ️ PRINTABLE CEREMONY PASS MODAL -->
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

<!-- ══════════════════════════════════════════════════════════ -->
<!--  MARRIAGE PENDING REVIEW MODAL                           -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="marriage-pending-modal" onclick="if(event.target===this) closeMarriagePendingModal(false)">
  <div class="marriage-pending-card" role="dialog" aria-modal="true" aria-labelledby="mpm-title">
    <div class="marriage-pending-header">
      <div class="marriage-icon-wrap">
        <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      </div>
      <div>
        <div class="marriage-tag">Reservation Successfully Submitted</div>
        <h3 class="marriage-modal-title" id="mpm-title">Pending Da'wah Department Review</h3>
        <p class="marriage-modal-sub">Reference Number: <strong id="mpm-ref-text">#MR-AUTO</strong></p>
      </div>
      <button type="button" class="btn-close-modal" onclick="closeMarriagePendingModal(false)" style="margin-left:auto; color:rgba(255,255,255,0.7);">
        <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
      </button>
    </div>

    <div class="marriage-pending-body">
      <div class="marriage-status-banner">
        <span class="status-pulse-dot"></span>
        <span>Status: <strong>Pending Administrator Review &amp; Ceremony Scheduling</strong></span>
      </div>

      <p class="marriage-primary-msg">
        Barakallahu feekum. Your marriage ceremony reservation request has been submitted to the ISCAG Da'wah Department.
      </p>

      <div class="marriage-info-callout">
        <div class="mcallout-icon">
          <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
        </div>
        <div class="mcallout-content">
          <strong>Official Schedule Calendar Notice:</strong>
          <p>Once the administrator reviews and approves your reservation form, your <strong>Official Marriage Schedule Calendar</strong> will automatically appear here with your confirmed solemnization date, ceremony time slot, and venue confirmation.</p>
        </div>
      </div>

      <div class="marriage-summary-grid">
        <div class="marriage-summary-item">
          <span class="mlbl">Groom's Name</span>
          <span class="mval" id="mpm-groom">—</span>
        </div>
        <div class="marriage-summary-item">
          <span class="mlbl">Bride's Name</span>
          <span class="mval" id="mpm-bride">—</span>
        </div>
        <div class="marriage-summary-item">
          <span class="mlbl">Requested Date &amp; Time</span>
          <span class="mval" id="mpm-schedule">—</span>
        </div>
        <div class="marriage-summary-item">
          <span class="mlbl">Ceremony Venue</span>
          <span class="mval" id="mpm-venue">ISCAG Main Mosque</span>
        </div>
      </div>
    </div>

    <div class="marriage-modal-footer">
      <a href="<?= url('/user/dashboard') ?>" class="btn-mpending-sec">Return to Dashboard</a>
      <button type="button" class="btn-mpending-pri" onclick="closeMarriagePendingModal(true)">View Status in Portal</button>
    </div>
  </div>
</div>

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

  function getTodayDateStr() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
  }

  function getTomorrowDateStr() {
    const now = new Date();
    const tomorrow = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
    return `${tomorrow.getFullYear()}-${String(tomorrow.getMonth() + 1).padStart(2, '0')}-${String(tomorrow.getDate()).padStart(2, '0')}`;
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
    
    const todayStr = getTodayDateStr();
    const tomorrowStr = getTomorrowDateStr();
    
    for (let day = 1; day <= daysInMonth; day++) {
      const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const blockReason = BLOCKED_DATES[dateStr];
      const isBlocked = !!blockReason;
      const isPastOrToday = dateStr < tomorrowStr;
      const isToday = dateStr === todayStr;
      const isSelected = selectedDate === dateStr;

      let titleAttr = '';
      if (isBlocked) {
        titleAttr = 'Unavailable: ' + blockReason;
      } else if (isPastOrToday) {
        titleAttr = isToday ? 'Today is unavailable. Earliest appointment is tomorrow.' : 'Past date unavailable';
      }
      
      calGrid.innerHTML += `
        <div class="calendar-day ${isBlocked ? 'booked' : ''} ${isPastOrToday ? 'disabled' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}" 
             title="${titleAttr}"
             onclick="${isBlocked ? `showBlockReason('${blockReason}')` : ((!isPastOrToday) ? `selectDate('${dateStr}')` : '')}">
          ${day}
        </div>
      `;
    }
  }

  window.showBlockReason = (reason) => {
    alert(`This date is unavailable: ${reason}`);
  };

  window.selectDate = (date) => {
    const tomorrowStr = getTomorrowDateStr();
    if (!date || date < tomorrowStr) {
      showToast('Appointments can only be scheduled starting tomorrow. Please select a future date.', '#e74c3c');
      return;
    }
    selectedDate = date;
    if(calStep) calStep.classList.remove('active');
    if(timeStep) timeStep.classList.add('active');
    const el = document.getElementById('display-selected-date');
    if(el) el.innerText = new Date(date + 'T00:00:00').toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
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
    const tomorrowStr = getTomorrowDateStr();
    if (!selectedDate || selectedDate < tomorrowStr) {
      showToast('Appointments can only be scheduled starting tomorrow. Please select a future date.', '#e74c3c');
      return;
    }
    const schedText = document.getElementById('selected-schedule-text');
    const inputDate = document.getElementById('input-date');
    const inputTime = document.getElementById('input-time');
    if(schedText) schedText.innerText = `${new Date(selectedDate + 'T00:00:00').toLocaleDateString()} at ${selectedTime}`;
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

  function scheduleMidnightUpdate() {
    const now = new Date();
    const midnight = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 1);
    const msUntilMidnight = midnight.getTime() - now.getTime();
    setTimeout(() => {
      const tomorrowStr = getTomorrowDateStr();
      const inDate = document.getElementById('input-date');
      if (inDate) inDate.min = tomorrowStr;
      if (selectedDate && selectedDate < tomorrowStr) {
        selectedDate = null;
        selectedTime = null;
        const schedText = document.getElementById('selected-schedule-text');
        if (schedText) schedText.innerText = 'Not selected';
        if (inDate) inDate.value = '';
        const inTime = document.getElementById('input-time');
        if (inTime) inTime.value = '';
      }
      renderCalendar();
      scheduleMidnightUpdate();
    }, msUntilMidnight);
  }
  scheduleMidnightUpdate();

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

      const tomorrowStr = getTomorrowDateStr();
      if (formData.marriage_date < tomorrowStr) {
        showToast('Appointments can only be scheduled starting tomorrow. Please select a future date.', '#e74c3c');
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
          const formattedDate = new Date(formData.marriage_date + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
          openMarriagePendingModal({
            ref: data.ref || ('#MR-' + String(data.id || 'AUTO').padStart(4, '0')),
            groom: formData.groom_name,
            bride: formData.bride_name,
            schedule: `${formattedDate} at ${formData.marriage_time}`,
            venue: formData.marriage_venue || 'ISCAG Main Mosque'
          });
        } else {
          showToast(data.message || 'Failed to submit reservation.', '#e74c3c');
        }
      } catch(err) {
        showToast('An error occurred. Please try again.', '#e74c3c');
      }
    };
  }

  // ── Marriage Pending Modal Functions ──
  function openMarriagePendingModal(data) {
    const modal = document.getElementById('marriage-pending-modal');
    if (!modal) return;
    if (data.ref) {
      const refEl = document.getElementById('mpm-ref-text');
      if (refEl) refEl.innerText = data.ref;
    }
    if (data.groom) {
      const gEl = document.getElementById('mpm-groom');
      if (gEl) gEl.innerText = data.groom;
    }
    if (data.bride) {
      const bEl = document.getElementById('mpm-bride');
      if (bEl) bEl.innerText = data.bride;
    }
    if (data.schedule) {
      const sEl = document.getElementById('mpm-schedule');
      if (sEl) sEl.innerText = data.schedule;
    }
    if (data.venue) {
      const vEl = document.getElementById('mpm-venue');
      if (vEl) vEl.innerText = data.venue;
    }
    modal.classList.add('active');
  }

  function closeMarriagePendingModal(shouldReload = true) {
    const modal = document.getElementById('marriage-pending-modal');
    if (modal) modal.classList.remove('active');
    if (shouldReload) {
      window.location.reload();
    }
  }

  // ── Approved Marriage Schedule Calendar Logic ──
  const APPROVED_MARRIAGE_DATE = <?= ($hasApproved && !empty($activeRequest['marriage_date'])) ? json_encode(date('Y-m-d', strtotime($activeRequest['marriage_date']))) : 'null' ?>;
  const APPROVED_MARRIAGE_TIME = <?= ($hasApproved && !empty($activeRequest['marriage_time'])) ? json_encode($activeRequest['marriage_time']) : 'null' ?>;
  const APPROVED_MARRIAGE_VENUE = <?= ($hasApproved && !empty($activeRequest['marriage_venue'])) ? json_encode($activeRequest['marriage_venue']) : json_encode('ISCAG Main Mosque') ?>;
  const APPROVED_MARRIAGE_COUPLE = <?= ($hasApproved && $activeRequest) ? json_encode(($activeRequest['groom_name'] ?? '') . ' & ' . ($activeRequest['bride_name'] ?? '')) : '""' ?>;

  let mApprovedCalYear = APPROVED_MARRIAGE_DATE ? new Date(APPROVED_MARRIAGE_DATE + 'T00:00:00').getFullYear() : new Date().getFullYear();
  let mApprovedCalMonth = APPROVED_MARRIAGE_DATE ? new Date(APPROVED_MARRIAGE_DATE + 'T00:00:00').getMonth() : new Date().getMonth();
  let selectedApprovedMarriageDay = APPROVED_MARRIAGE_DATE;

  function renderApprovedMarriageCal() {
    const titleEl = document.getElementById('approved-cal-title-marriage');
    const gridEl = document.getElementById('approved-cal-grid-marriage');
    if (!titleEl || !gridEl) return;

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    titleEl.innerText = `${monthNames[mApprovedCalMonth]} ${mApprovedCalYear}`;

    const firstDayIndex = new Date(mApprovedCalYear, mApprovedCalMonth, 1).getDay();
    const daysInMonth = new Date(mApprovedCalYear, mApprovedCalMonth + 1, 0).getDate();
    const todayStr = new Date().toISOString().split('T')[0];

    let html = '';
    for (let i = 0; i < firstDayIndex; i++) {
      html += `<div class="marriage-cal-day empty"></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${mApprovedCalYear}-${String(mApprovedCalMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
      const isCeremonyDay = (APPROVED_MARRIAGE_DATE === dateStr);
      const isToday = (todayStr === dateStr);
      const isSelected = (selectedApprovedMarriageDay === dateStr);

      let classes = ['marriage-cal-day'];
      if (isCeremonyDay) classes.push('has-ceremony');
      if (isToday) classes.push('today');
      if (isSelected && !isCeremonyDay) classes.push('selected-view');

      html += `
        <div class="${classes.join(' ')}" onclick="selectApprovedMarriageDay('${dateStr}')" title="${isCeremonyDay ? 'Confirmed Wedding Ceremony' : ''}">
          <span>${d}</span>
        </div>
      `;
    }

    gridEl.innerHTML = html;
  }

  function navApprovedMarriageCal(dir) {
    mApprovedCalMonth += dir;
    if (mApprovedCalMonth < 0) {
      mApprovedCalMonth = 11;
      mApprovedCalYear--;
    } else if (mApprovedCalMonth > 11) {
      mApprovedCalMonth = 0;
      mApprovedCalYear++;
    }
    renderApprovedMarriageCal();
  }

  function jumpToApprovedCeremony() {
    if (!APPROVED_MARRIAGE_DATE) return;
    const d = new Date(APPROVED_MARRIAGE_DATE + 'T00:00:00');
    mApprovedCalYear = d.getFullYear();
    mApprovedCalMonth = d.getMonth();
    selectedApprovedMarriageDay = APPROVED_MARRIAGE_DATE;
    renderApprovedMarriageCal();
    updateMarriageInspector(APPROVED_MARRIAGE_DATE);
  }

  function selectApprovedMarriageDay(dateStr) {
    selectedApprovedMarriageDay = dateStr;
    renderApprovedMarriageCal();
    updateMarriageInspector(dateStr);
  }

  function updateMarriageInspector(dateStr) {
    const isCeremony = (dateStr === APPROVED_MARRIAGE_DATE);
    const dateObj = new Date(dateStr + 'T00:00:00');
    const formatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

    const dateEl = document.getElementById('minspector-date-text');
    const statusEl = document.getElementById('minspector-status-text');
    const timeEl = document.getElementById('minspector-time-text');
    const venueEl = document.getElementById('minspector-venue-text');

    if (dateEl) dateEl.innerText = formatted;
    if (statusEl) {
      statusEl.innerText = isCeremony ? 'Approved Solemnization' : 'No Scheduled Ceremony';
    }
    if (timeEl) {
      timeEl.innerText = isCeremony ? (APPROVED_MARRIAGE_TIME || '—') : '—';
    }
    if (venueEl) {
      venueEl.innerText = isCeremony ? APPROVED_MARRIAGE_VENUE : '—';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('approved-cal-grid-marriage')) {
      renderApprovedMarriageCal();
    }
  });
  if (document.getElementById('approved-cal-grid-marriage')) {
    renderApprovedMarriageCal();
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
