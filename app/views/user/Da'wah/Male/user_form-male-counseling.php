<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Counseling & Appointment Schedule</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    :root {
      --primary-male: #14532D;
      --primary-male-dark: #0f3e21;
      --primary-male-light: #f0fdf4;
      --gold-accent: #D4AF37;
      --gold-hover: #b8972f;
      --gold-light: #fefce8;
      --emerald-subtle: #dcfce7;
    }

    /* ── Scheduling Calendar Styles ── */
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
      position: fixed; inset: 0; background: rgba(15, 30, 22, 0.5); backdrop-filter: blur(4px);
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

    /* ══════════════════════════════════════════════════════════ */
    /* 🌟 APPROVED APPOINTMENT & SCHEDULE HUB STYLES             */
    /* ══════════════════════════════════════════════════════════ */
    
    .appointment-hub-card {
      background: #fff; border-radius: 20px; border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(20, 83, 45, 0.07); overflow: hidden; margin-bottom: 28px;
    }

    .appointment-hero-banner {
      background: linear-gradient(135deg, var(--primary-male), var(--primary-male-dark));
      padding: 32px 36px; position: relative; overflow: hidden; color: #fff;
    }
    .appointment-hero-banner::after {
      content: ''; position: absolute; right: -40px; bottom: -40px; width: 220px; height: 220px;
      border-radius: 50%; background: rgba(212, 175, 55, 0.12); pointer-events: none;
    }
    .appointment-hero-banner::before {
      content: ''; position: absolute; right: 120px; top: -30px; width: 140px; height: 140px;
      border-radius: 50%; background: rgba(255, 255, 255, 0.05); pointer-events: none;
    }

    .hero-top-row {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
      position: relative; z-index: 1; flex-wrap: wrap; margin-bottom: 24px;
    }
    .hero-title-area { display: flex; align-items: center; gap: 18px; }
    .hero-avatar-icon {
      width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
      border: 2px solid rgba(212, 175, 55, 0.4); flex-shrink: 0; box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .hero-avatar-icon svg { width: 32px; height: 32px; fill: #fff; }

    .hero-headings h3 {
      font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; margin: 0 0 4px;
      letter-spacing: -0.01em; color: #fff;
    }
    .hero-headings p {
      font-size: 0.85rem; color: rgba(255, 255, 255, 0.82); margin: 0;
    }

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

    /* Key Metric Highlight Strip */
    .hero-schedule-strip {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
      position: relative; z-index: 1;
    }
    .sched-metric-box {
      background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 14px 16px;
      transition: all 0.2s;
    }
    .sched-metric-box:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-2px); }
    .sched-metric-lbl {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.75); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .sched-metric-val {
      font-size: 1rem; font-weight: 700; color: #fff; line-height: 1.3;
    }
    .sched-metric-val.gold { color: #fde047; }

    /* Action Toolbar */
    .appointment-toolbar {
      padding: 16px 36px; background: #fafafa; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .toolbar-left-msg {
      display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 600; color: #374151;
    }
    .toolbar-left-msg svg { width: 18px; height: 18px; fill: var(--primary-male); flex-shrink: 0; }
    
    .toolbar-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    
    .btn-action-tool {
      display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;
      border-radius: 10px; font-size: 0.84rem; font-weight: 700; cursor: pointer;
      text-decoration: none; transition: all 0.2s; border: none;
    }
    .btn-action-tool svg { width: 16px; height: 16px; fill: currentColor; }
    
    .btn-tool-print {
      background: linear-gradient(135deg, var(--primary-male), var(--primary-male-dark));
      color: #fff; box-shadow: 0 4px 12px rgba(20, 83, 45, 0.25);
    }
    .btn-tool-print:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(20, 83, 45, 0.35); }

    .btn-tool-cal {
      background: #fff; color: #374151; border: 1.5px solid var(--border);
    }
    .btn-tool-cal:hover { background: #f3f4f6; border-color: #d1d5db; color: #111827; }

    .btn-tool-guidance {
      background: linear-gradient(135deg, #D4AF37, #B8860B); color: #1a1a1a;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3); font-weight: 800;
    }
    .btn-tool-guidance:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4); }

    /* ── Schedule Hub Main Two Column Grid ── */
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
    .hub-card-title svg { width: 18px; height: 18px; fill: var(--primary-male); }

    /* Detail Pairs */
    .detail-table { display: flex; flex-direction: column; gap: 14px; }
    .detail-row {
      display: flex; justify-content: space-between; align-items: center;
      padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;
    }
    .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .detail-key { font-size: 0.8rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; }
    .detail-val { font-size: 0.92rem; font-weight: 700; color: #1f2937; text-align: right; }
    .detail-val.highlight { color: var(--primary-male); font-family: 'Lora', serif; font-size: 1rem; }
    .detail-badge-pill {
      display: inline-block; padding: 4px 12px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 800; background: var(--primary-male-light); color: var(--primary-male);
      border: 1px solid var(--emerald-subtle);
    }

    /* ── Progress Tracker ── */
    .journey-tracker {
      display: flex; justify-content: space-between; position: relative; margin: 28px 10px 10px;
    }
    .journey-line-bg {
      position: absolute; top: 18px; left: 10px; right: 10px; height: 3px; background: #e5e7eb; z-index: 1;
    }
    .journey-line-fill {
      position: absolute; top: 18px; left: 10px; width: 75%; height: 3px;
      background: linear-gradient(90deg, var(--primary-male), #10b981); z-index: 2;
    }
    .journey-step {
      display: flex; flex-direction: column; align-items: center; position: relative; z-index: 3;
      gap: 8px; width: 75px; text-align: center;
    }
    .journey-dot {
      width: 36px; height: 36px; border-radius: 50%; background: #fff;
      border: 3px solid #e5e7eb; color: #9ca3af; display: flex; align-items: center;
      justify-content: center; font-size: 0.85rem; font-weight: 800; transition: all 0.3s;
    }
    .journey-step.done .journey-dot {
      background: var(--primary-male); border-color: var(--primary-male); color: #fff;
    }
    .journey-step.active .journey-dot {
      border-color: #10b981; color: #10b981; background: #fff;
      box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.18);
    }
    .journey-step.active .journey-dot svg { fill: #10b981; }
    .journey-step.done .journey-dot svg { fill: #fff; }
    .journey-lbl { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #6b7280; line-height: 1.2; }
    .journey-step.done .journey-lbl { color: var(--primary-male); }
    .journey-step.active .journey-lbl { color: #10b981; font-weight: 800; }

    /* ── Pre-Session Checklist (Brothers) ── */
    .checklist-list { display: flex; flex-direction: column; gap: 12px; }
    .check-item {
      display: flex; align-items: flex-start; gap: 14px; padding: 12px 14px;
      border-radius: 12px; background: #f9fafb; border: 1px solid #f3f4f6; transition: all 0.2s;
    }
    .check-item:hover { background: var(--primary-male-light); border-color: var(--emerald-subtle); }
    .check-num {
      width: 24px; height: 24px; border-radius: 50%; background: var(--primary-male);
      color: #fff; display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem; font-weight: 800; flex-shrink: 0; margin-top: 1px;
    }
    .check-content strong { display: block; font-size: 0.85rem; color: #1f2937; margin-bottom: 2px; }
    .check-content p { font-size: 0.78rem; color: #6b7280; margin: 0; line-height: 1.4; }

    /* ── Counselor Contact & Message Card ── */
    .counselor-card {
      background: linear-gradient(to bottom, #fdfbf7, #fff); border: 1px solid #fed7aa;
    }
    .counselor-header { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .counselor-avatar {
      width: 48px; height: 48px; border-radius: 12px; background: #fef3c7;
      display: flex; align-items: center; justify-content: center; color: #d97706; font-weight: 800; font-size: 1.1rem;
    }
    .counselor-info h5 { margin: 0 0 2px; font-size: 0.95rem; font-weight: 700; color: #1f2937; }
    .counselor-info p { margin: 0; font-size: 0.75rem; color: #b45309; font-weight: 600; }

    .counselor-contacts {
      display: flex; flex-direction: column; gap: 8px; font-size: 0.82rem; color: #4b5563; margin-bottom: 16px;
    }
    .contact-item { display: flex; align-items: center; gap: 8px; }
    .contact-item svg { width: 14px; height: 14px; fill: #d97706; }

    .inquiry-box-input {
      width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 10px;
      border: 1.5px solid #e5e7eb; font-size: 0.85rem; outline: none; margin-bottom: 8px;
    }
    .inquiry-box-input:focus { border-color: var(--primary-male); }
    .btn-send-inquiry {
      width: 100%; padding: 9px; background: var(--primary-male); color: #fff;
      border: none; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer;
      transition: all 0.2s;
    }
    .btn-send-inquiry:hover { background: var(--primary-male-dark); }

    /* ══════════════════════════════════════════════════════════ */
    /* 🎟️ PRINTABLE APPOINTMENT SLIP / PASS MODAL STYLES         */
    /* ══════════════════════════════════════════════════════════ */
    .slip-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 580px;
      box-shadow: 0 30px 60px rgba(0,0,0,0.3); overflow: hidden;
      animation: modalPop 0.35s ease; border: 1px solid rgba(0,0,0,0.08);
    }
    .slip-pass-body {
      padding: 32px 36px; background: #fff; position: relative;
    }
    .slip-pass-header {
      text-align: center; border-bottom: 2px dashed #e5e7eb; padding-bottom: 20px; margin-bottom: 24px;
    }
    .slip-logo-badge {
      display: inline-flex; align-items: center; gap: 8px; font-family: 'Lora', serif;
      font-size: 1.25rem; font-weight: 800; color: var(--primary-male); margin-bottom: 4px;
    }
    .slip-title { font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #6b7280; }
    
    .slip-seal {
      position: absolute; right: 36px; top: 40px; width: 84px; height: 84px; border-radius: 50%;
      border: 2px dashed #10b981; display: flex; flex-direction: column; align-items: center;
      justify-content: center; text-align: center; color: #10b981; font-weight: 800; font-size: 0.6rem;
      transform: rotate(-12deg); opacity: 0.85; pointer-events: none;
    }
    .slip-seal svg { width: 22px; height: 22px; fill: currentColor; margin-bottom: 2px; }

    .slip-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;
    }
    .slip-item {
      background: #f9fafb; padding: 12px 14px; border-radius: 10px; border: 1px solid #f3f4f6;
    }
    .slip-item.full { grid-column: span 2; }
    .slip-item-label { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; margin-bottom: 2px; }
    .slip-item-val { font-size: 0.92rem; font-weight: 700; color: #111827; }

    .slip-barcode-area {
      text-align: center; padding-top: 16px; border-top: 2px dashed #e5e7eb;
    }
    .barcode-svg { width: 100%; max-width: 260px; height: 44px; margin: 0 auto 6px; }
    .slip-barcode-text { font-family: monospace; font-size: 0.85rem; font-weight: 700; color: #4b5563; }

    .slip-modal-footer {
      padding: 16px 36px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center;
    }

    /* Print Specific Media Query */
    @media print {
      body * { visibility: hidden; }
      .app-wrapper, .sidebar, .top-bar, .breadcrumb-bar, .user-analytics, .section-card, .appointment-toolbar, .hub-card { display: none !important; }
      #appointment-pass-modal, #appointment-pass-modal * { visibility: visible; }
      #appointment-pass-modal {
        position: fixed; inset: 0; background: #fff !important; padding: 0 !important;
        display: block !important; z-index: 999999;
      }
      .slip-modal-card {
        box-shadow: none !important; border: 1px solid #ccc !important; width: 100% !important;
        max-width: 100% !important; border-radius: 0 !important; margin: 0 auto;
      }
      .slip-modal-footer { display: none !important; }
    }

    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modalPop { 0% { opacity: 0; transform: scale(0.92); } 100% { opacity: 1; transform: scale(1); } }

    @media (max-width: 960px) {
      .schedule-hub-grid { grid-template-columns: 1fr; padding: 20px; }
      .hero-schedule-strip { grid-template-columns: 1fr 1fr; }
      .appointment-hero-banner { padding: 24px 20px; }
      .appointment-toolbar { padding: 16px 20px; }
      .user-analytics { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'counseling_male'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Counseling & Guidance Portal</div>
        <div class="top-bar-subtitle">Da'wah Department — Confidential brother-to-brother guidance & support</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Male Counseling & Appointments</span>
      </div>

      <!-- ANALYTICS DASHBOARD -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">Total Applications</div>
          <div class="stat-value" id="ana-total"><?= $analytics['total'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Approval</div>
          <div class="stat-value warning" id="ana-pending"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Confirmed Sessions</div>
          <div class="stat-value success" id="ana-approved"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
      </div>

      <?php 
        $hasApproved = false;
        $hasPending = false;
        $activeRequest = null;
        foreach ($history ?? [] as $req) {
            $st = strtolower($req['status'] ?? '');
            if ($st === 'approved') { 
              $hasApproved = true; 
              $activeRequest = $req; 
              break; 
            }
            if ($st === 'pending' && !$hasApproved) { 
              $hasPending = true; 
              $activeRequest = $req; 
            }
        }
      ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 🌟 CASE 1: SCHEDULE APPROVED BY ADMIN (APPOINTMENT HUB)    -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php if ($hasApproved && $activeRequest): ?>
      <div class="appointment-hub-card">
        <!-- Hero Header -->
        <div class="appointment-hero-banner">
          <div class="hero-top-row">
            <div class="hero-title-area">
              <div class="hero-avatar-icon">
                <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
              </div>
              <div class="hero-headings">
                <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; color:var(--gold-accent); margin-bottom:2px;">
                  Official Counseling Confirmation
                </div>
                <h3>Counseling & Spiritual Guidance Appointment</h3>
                <p>Ref No: <strong>#MC-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Confirmed with Ustaz Counselor</p>
              </div>
            </div>
            <div>
              <div class="badge-live-pulse">
                <div class="pulse-dot"></div>
                Confirmed & Scheduled
              </div>
            </div>
          </div>

          <!-- Schedule Key Stats Strip -->
          <div class="hero-schedule-strip">
            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Session Date
              </div>
              <div class="sched-metric-val gold"><?= !empty($activeRequest['preferred_date']) ? date('M d, Y', strtotime($activeRequest['preferred_date'])) : 'Confirmed' ?></div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                Session Time
              </div>
              <div class="sched-metric-val"><?= htmlspecialchars($activeRequest['preferred_time'] ?? '10:00 AM') ?></div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Venue / Office
              </div>
              <div class="sched-metric-val">Counseling Room 204</div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Assigned Counselor
              </div>
              <div class="sched-metric-val">Ustaz Counselor</div>
            </div>
          </div>
        </div>

        <!-- Action Toolbar -->
        <div class="appointment-toolbar">
          <div class="toolbar-left-msg">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            <span>Your appointment has been officially confirmed. Please arrive 10 minutes prior to your time slot.</span>
          </div>

          <div class="toolbar-actions">
            <!-- Print Official Pass Button -->
            <button type="button" class="btn-action-tool btn-tool-print" onclick="openAppointmentPassModal(<?= htmlspecialchars(json_encode($activeRequest)) ?>)">
              <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
              Print Appointment Slip
            </button>

            <!-- Add to Calendar -->
            <?php 
              $calDate = !empty($activeRequest['preferred_date']) ? date('Ymd', strtotime($activeRequest['preferred_date'])) : date('Ymd');
              $calUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode("ISCAG Counseling Session — #" . $activeRequest['id']) . "&dates=" . $calDate . "T020000Z/" . $calDate . "T030000Z&details=" . urlencode("ISCAG Confidential Brother's Counseling Session regarding " . $activeRequest['reason'] . ". Counselor: Ustaz Da'wah Office.") . "&location=" . urlencode("ISCAG Mosque & Da'wah Center, 2nd Floor Room 204");
            ?>
            <a href="<?= $calUrl ?>" target="_blank" class="btn-action-tool btn-tool-cal">
              <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
              Add to Google Calendar
            </a>

            <!-- Open Resources -->
            <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-action-tool btn-tool-guidance">
              Open Guidance Center →
            </a>
          </div>
        </div>

        <!-- Two Column Main Grid -->
        <div class="schedule-hub-grid">
          <!-- Left Column -->
          <div>
            <!-- Session Summary Card -->
            <div class="hub-card" style="margin-bottom:24px;">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                  Confirmed Session Details
                </span>
                <span class="detail-badge-pill">In-Person Consultation</span>
              </h5>

              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Primary Concern</span>
                  <span class="detail-val highlight"><?= htmlspecialchars(ucfirst($activeRequest['reason'])) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Appointment Day & Date</span>
                  <span class="detail-val"><?= !empty($activeRequest['preferred_date']) ? date('l, F j, Y', strtotime($activeRequest['preferred_date'])) : 'Confirmed' ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Confirmed Time Slot</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['preferred_time'] ?? '10:00 AM') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Location / Venue</span>
                  <span class="detail-val">ISCAG Mosque, 2nd Floor — Brothers' Guidance Room (Room 204)</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Assigned Counselor</span>
                  <span class="detail-val">Ustaz Male Da'wah Counselor</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Client Reference</span>
                  <span class="detail-val"><?= htmlspecialchars($_SESSION['name'] ?? 'Brother') ?> (ID: #<?= $activeRequest['tenant_id'] ?>)</span>
                </div>
              </div>
            </div>

            <!-- Counseling Journey Tracker -->
            <div class="hub-card">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  Service Lifecycle Tracker
                </span>
                <span style="font-size:0.75rem; color:#10b981; font-weight:800;">Step 4 of 5</span>
              </h5>

              <div class="journey-tracker">
                <div class="journey-line-bg"></div>
                <div class="journey-line-fill"></div>

                <div class="journey-step done">
                  <div class="journey-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <span class="journey-lbl">Request<br>Filed</span>
                </div>

                <div class="journey-step done">
                  <div class="journey-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <span class="journey-lbl">Counselor<br>Review</span>
                </div>

                <div class="journey-step done">
                  <div class="journey-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <span class="journey-lbl">Schedule<br>Approved</span>
                </div>

                <div class="journey-step active">
                  <div class="journey-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  </div>
                  <span class="journey-lbl">Session<br>Ready</span>
                </div>

                <div class="journey-step">
                  <div class="journey-dot">5</div>
                  <span class="journey-lbl">Completed<br>& Follow-up</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!-- Pre-Session Guidance & Islamic Etiquette -->
            <div class="hub-card" style="margin-bottom:24px;">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                  Pre-Session Guidance (Brothers)
                </span>
              </h5>

              <div class="checklist-list">
                <div class="check-item">
                  <div class="check-num">1</div>
                  <div class="check-content">
                    <strong>State of Taharah (Purity & Wudu)</strong>
                    <p>Perform ablution before coming to prepare your heart and mind with calmness.</p>
                  </div>
                </div>

                <div class="check-item">
                  <div class="check-num">2</div>
                  <div class="check-content">
                    <strong>Sincerity of Intention (Ikhlas)</strong>
                    <p>Seek advice purely for personal self-betterment and the pleasure of Allah.</p>
                  </div>
                </div>

                <div class="check-item">
                  <div class="check-num">3</div>
                  <div class="check-content">
                    <strong>Strict Confidentiality (Amanah)</strong>
                    <p>All conversations are private under Islamic trust and Da'wah Department policy.</p>
                  </div>
                </div>

                <div class="check-item">
                  <div class="check-num">4</div>
                  <div class="check-content">
                    <strong>Specific Questions & Notes</strong>
                    <p>Feel free to prepare written questions to make the best use of your 1-hour session.</p>
                  </div>
                </div>
              </div>

              <div style="margin-top:16px; text-align:center;">
                <a href="<?= url('/user/services/counseling/resources') ?>" style="font-size:0.82rem; font-weight:800; color:var(--primary-male); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                  Explore Brothers' Guidance Articles & Topics →
                </a>
              </div>
            </div>

            <!-- Counselor Inquiries / Mosque Contact Card -->
            <div class="hub-card counselor-card">
              <div class="counselor-header">
                <div class="counselor-avatar">
                  <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="counselor-info">
                  <h5>Da'wah Department Helpdesk</h5>
                  <p>Male Guidance & Consultation Desk</p>
                </div>
              </div>

              <div class="counselor-contacts">
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                  <span>Office: <strong>(02) 888-ISCAG</strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  <span>WhatsApp / Mobile: <strong>0917-DAWAH-01</strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>
                  <span>Mon – Sat: 8:00 AM – 5:00 PM</span>
                </div>
              </div>

              <!-- Quick Inquiry Message Box -->
              <div>
                <input type="text" class="inquiry-box-input" id="inquiry-text" placeholder="Have a quick question before your session?" />
                <button type="button" class="btn-send-inquiry" onclick="sendInquiryNote()">Send Note to Counselor</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- ⏳ CASE 2: PENDING REVIEW HERO                             -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php elseif ($hasPending && $activeRequest): ?>
      <div class="status-hero" style="background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06); overflow: hidden; margin-bottom: 24px;">
        <div class="status-hero-top" style="background: linear-gradient(135deg, #14532D, #166534); padding: 28px 32px 24px; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -20px; bottom: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255, 255, 255, 0.1);"></div>
            <div style="position: absolute; right: 100px; bottom: -30px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.05);"></div>
            
            <div class="status-hero-header" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; position: relative; z-index: 1;">
                <div class="status-hero-header-left" style="display: flex; align-items: center; gap: 16px;">
                    <div class="status-hero-avatar" style="width: 56px; height: 56px; border-radius: 50%; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; fill: white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div>
                        <h5 class="status-hero-name" style="font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: white; margin: 0 0 2px;">Counseling Request Under Review</h5>
                        <p class="status-hero-subtitle" style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.7); margin: 0;">Ref No: <strong>#MC-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong> • Preferred Date: <?= !empty($activeRequest['preferred_date']) ? date('M d, Y', strtotime($activeRequest['preferred_date'])) : 'Pending' ?></p>
                    </div>
                </div>
                <div class="status-badge pending" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; white-space: nowrap; backdrop-filter: blur(8px); background: rgba(245, 158, 11, 0.25); color: #fef08a; border: 1px solid rgba(253, 224, 71, 0.4);">
                    <div class="status-badge-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #fef08a;"></div>
                    Awaiting Approval
                </div>
            </div>
        </div>

        <div class="status-summary" style="padding: 18px 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; background: #f9fafb; border-top: 1px solid var(--border);">
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Request Concern</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;"><?= htmlspecialchars(ucfirst($activeRequest['reason'])) ?></div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Requested Slot</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D;"><?= htmlspecialchars($activeRequest['preferred_time'] ?? 'Pending') ?></div>
            </div>
            <div class="summary-stat" style="text-align: center; padding: 14px 10px; background: white; border-radius: 10px; border: 1px solid var(--border);">
                <div class="summary-stat-label" style="font-size: 0.66rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px;">Status</div>
                <div class="summary-stat-value" style="font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #f59e0b;">Under Review</div>
            </div>
        </div>

        <div style="padding: 24px 32px; text-align: center; background: #fff;">
          <p style="font-size: 0.92rem; color: #4b5563; line-height: 1.6; max-width: 600px; margin: 0 auto 16px;">
            Our male counseling staff are evaluating your preferred schedule. Once approved by the administrator, your official appointment confirmation and admission slip will appear right here.
          </p>
          <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-action-tool btn-tool-guidance">
            Browse Pre-Counseling Guidance Portal
          </a>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📝 CASE 3: NO ACTIVE REQUEST -> BOOKING FORM               -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php else: ?>
      <!-- FORM HEADER BANNER -->
      <div class="section-card" style="margin-bottom:20px;">
        <div class="form-page-header">
          <h4>Counseling Request Form</h4>
          <p>Da'wah Department — All information will be kept strictly confidential in accordance with Islamic ethical principles.</p>
        </div>
      </div>

      <!-- NOTICE -->
      <div class="notice-box">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        <span>This portal is exclusively for <strong>male clients</strong>. All sessions and details submitted are handled with complete confidentiality and privacy.</span>
      </div>

      <div class="section-card">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            Book a Counseling Session
          </h6>
          <span style="font-size:0.75rem;color:var(--text-muted);">Reference No.: <strong style="color:var(--primary);">#MC-AUTO</strong></span>
        </div>
        <div class="section-card-body">

          <!-- Client Information -->
          <div class="form-section-title">Client Information</div>
          <div class="form-grid cols-3">
            <div>
              <label class="form-label">Full Name <span class="required">*</span></label>
              <input type="text" class="form-control" id="input-fullname" placeholder="Enter your full name" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" />
            </div>
            <div>
              <label class="form-label">Age <span class="required">*</span></label>
              <input type="number" class="form-control" id="input-age" placeholder="Age" min="1" max="120" value="25" />
            </div>
            <div>
              <label class="form-label">Civil Status</label>
              <select class="form-select" id="input-civil">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widower">Widower</option>
                <option value="Divorced">Divorced</option>
              </select>
            </div>
          </div>
          <div class="form-grid cols-2-1" style="margin-bottom:24px;">
            <div>
              <label class="form-label">Complete Address <span class="required">*</span></label>
              <input type="text" class="form-control" id="input-address" placeholder="Street, Barangay, City, Province" />
            </div>
            <div>
              <label class="form-label">Contact Number <span class="required">*</span></label>
              <input type="tel" class="form-control" id="input-contact" placeholder="09XX-XXX-XXXX" />
            </div>
          </div>

          <!-- Counseling Details -->
          <div class="form-section-title">Counseling Details</div>
          <div style="margin-bottom:16px;">
            <label class="form-label">Reason for Counseling <span class="required">*</span></label>
            <select class="form-select" id="reason-select" style="max-width:420px;margin-bottom:10px;">
              <option value="">— Select primary concern —</option>
              <option value="Family / Marital Issues">Family / Marital Issues</option>
              <option value="Personal / Spiritual Struggles">Personal / Spiritual Struggles</option>
              <option value="Parenting & Family Guidance">Parenting & Family Guidance</option>
              <option value="Youth & Academic Concerns">Youth & Academic Concerns</option>
              <option value="Financial Difficulties">Financial Difficulties</option>
              <option value="Grief and Loss">Grief and Loss</option>
              <option value="Anger Management">Anger Management</option>
              <option value="Revert / New Muslim Support">Revert / New Muslim Support</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div style="margin-bottom:24px;">
            <label class="form-label">Detailed Description of Concern <span class="required">*</span></label>
            <textarea class="form-control" id="input-description" rows="4" placeholder="Briefly describe your situation so our counselor can prepare for your session..."></textarea>
            <p style="font-size:0.75rem;color:var(--text-muted);margin-top:8px;">All details shared here are kept strictly confidential and accessible only to authorized counselors.</p>
          </div>

          <!-- Availability & Preferences -->
          <div class="form-section-title">Availability & Preferences</div>
          <div class="form-grid cols-2" style="margin-bottom:32px;">
            <div>
              <label class="form-label">Preferred Session Type</label>
              <div style="display:flex;gap:16px;margin-top:8px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer;">
                  <input type="radio" name="session_type" value="In-Person" checked /> In-Person (Mosque Office)
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:0.85rem;cursor:pointer;">
                  <input type="radio" name="session_type" value="Online" /> Online Consultation
                </label>
              </div>
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
                    <span class="trigger-value" id="selected-schedule-text">Click to choose date & slot</span>
                    <input type="hidden" name="preferred_date" id="input-date" required />
                    <input type="hidden" name="preferred_time" id="input-time" required />
                  </div>
                </div>
                <div class="trigger-arrow">
                  <svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>
                </div>
              </div>

              <!-- MODAL CALENDAR STRUCTURE -->
              <div class="modal-overlay" id="scheduling-modal">
                <div class="modal-card">
                  <div class="modal-header">
                    <div>
                      <h4>Select Schedule</h4>
                      <p>Pick a date and time for your session</p>
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
                        <div class="slot-pill" onclick="selectTime('04:00 PM')">04:00 PM</div>
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
              <input type="checkbox" id="privacy-check" style="margin-top:4px;" />
              <label for="privacy-check" style="font-size:0.85rem;color:#166534;line-height:1.5;cursor:pointer;">
                I understand that this information will be used to process my counseling session and I agree to the <a href="#" style="color:#059669;font-weight:700;text-decoration:underline;">Confidentiality Policy</a> of the Da'wah Department.
              </label>
            </div>
          </div>

          <div class="form-submit-row">
            <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">Cancel</button>
            <button type="button" class="btn-submit" id="submit-form" onclick="submitCounselingForm()">Submit Request</button>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📜 MY APPLICATION HISTORY TABLE                            -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Counseling History & Records
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Reason for Request</th>
                  <th>Preferred Schedule</th>
                  <th>Date Filed</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="history-tbody">
                <?php if (empty($history)): ?>
                  <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No applications found.</td></tr>
                <?php else: ?>
                  <?php foreach ($history as $h): ?>
                    <tr>
                      <td class="td-id">#MC-<?= str_pad($h['id'], 4, '0', STR_PAD_LEFT) ?></td>
                      <td style="font-weight:600;"><?= htmlspecialchars(ucfirst($h['reason'])) ?></td>
                      <td>
                        <?= !empty($h['preferred_date']) ? date('M d, Y', strtotime($h['preferred_date'])) : 'TBD' ?>
                        <?= !empty($h['preferred_time']) ? ' • ' . htmlspecialchars($h['preferred_time']) : '' ?>
                      </td>
                      <td><?= date('M d, Y', strtotime($h['created_at'])) ?></td>
                      <td>
                        <?php if ($h['status'] === 'approved'): ?>
                          <span class="badge-status badge-approved" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">Approved</span>
                        <?php elseif ($h['status'] === 'pending'): ?>
                          <span class="badge-status badge-pending" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;">Under Review</span>
                        <?php else: ?>
                          <span class="badge-status badge-rejected">Rejected</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($h['status'] === 'approved'): ?>
                          <button type="button" class="btn-view-doc" onclick='openAppointmentPassModal(<?= json_encode($h) ?>)' title="View Official Pass">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                            View Pass
                          </button>
                        <?php else: ?>
                          <span style="font-size:0.75rem; color:var(--text-muted);">—</span>
                        <?php endif; ?>
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
<!-- 🎟️ PRINTABLE APPOINTMENT PASS MODAL                        -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="appointment-pass-modal">
  <div class="slip-modal-card">
    <div class="slip-pass-body" id="printable-pass-area">
      <!-- Official Stamp -->
      <div class="slip-seal">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span>OFFICIAL<br>APPROVED</span>
      </div>

      <div class="slip-pass-header">
        <div class="slip-logo-badge">
          <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:var(--primary-male);"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
          ISCAG ISLAMIC CENTER
        </div>
        <div class="slip-title">Official Counseling Appointment Pass (Male Department)</div>
      </div>

      <div class="slip-grid">
        <div class="slip-item">
          <div class="slip-item-label">Pass Reference Code</div>
          <div class="slip-item-val" id="pass-ref-no">#MC-0001</div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Consultation Mode</div>
          <div class="slip-item-val">In-Person Consultation</div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Applicant Name</div>
          <div class="slip-item-val" id="pass-client-name"><?= htmlspecialchars($_SESSION['name'] ?? 'Brother') ?></div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Assigned Counselor</div>
          <div class="slip-item-val">Ustaz Male Da'wah Counselor</div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Confirmed Date</div>
          <div class="slip-item-val" id="pass-date" style="color:var(--primary-male);">—</div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Confirmed Time Slot</div>
          <div class="slip-item-val" id="pass-time" style="color:var(--primary-male);">—</div>
        </div>
        <div class="slip-item full">
          <div class="slip-item-label">Venue & Room Instructions</div>
          <div class="slip-item-val">ISCAG Mosque & Da'wah Center, 2nd Floor — Brothers' Guidance Room (Room 204)</div>
        </div>
        <div class="slip-item full">
          <div class="slip-item-label">Consultation Concern / Topic</div>
          <div class="slip-item-val" id="pass-reason">—</div>
        </div>
      </div>

      <!-- Barcode Area -->
      <div class="slip-barcode-area">
        <svg class="barcode-svg" viewBox="0 0 200 40">
          <rect x="0" y="0" width="4" height="40" fill="#111" />
          <rect x="8" y="0" width="2" height="40" fill="#111" />
          <rect x="14" y="0" width="6" height="40" fill="#111" />
          <rect x="24" y="0" width="2" height="40" fill="#111" />
          <rect x="30" y="0" width="8" height="40" fill="#111" />
          <rect x="42" y="0" width="3" height="40" fill="#111" />
          <rect x="48" y="0" width="5" height="40" fill="#111" />
          <rect x="58" y="0" width="2" height="40" fill="#111" />
          <rect x="64" y="0" width="7" height="40" fill="#111" />
          <rect x="76" y="0" width="3" height="40" fill="#111" />
          <rect x="84" y="0" width="6" height="40" fill="#111" />
          <rect x="94" y="0" width="2" height="40" fill="#111" />
          <rect x="100" y="0" width="4" height="40" fill="#111" />
          <rect x="108" y="0" width="8" height="40" fill="#111" />
          <rect x="120" y="0" width="3" height="40" fill="#111" />
          <rect x="126" y="0" width="5" height="40" fill="#111" />
          <rect x="136" y="0" width="2" height="40" fill="#111" />
          <rect x="142" y="0" width="6" height="40" fill="#111" />
          <rect x="152" y="0" width="4" height="40" fill="#111" />
          <rect x="160" y="0" width="6" height="40" fill="#111" />
          <rect x="170" y="0" width="3" height="40" fill="#111" />
          <rect x="176" y="0" width="6" height="40" fill="#111" />
          <rect x="186" y="0" width="4" height="40" fill="#111" />
          <rect x="194" y="0" width="6" height="40" fill="#111" />
        </svg>
        <div class="slip-barcode-text" id="pass-barcode-text">ISCAG-MC-PASS-0001</div>
      </div>
    </div>

    <div class="slip-modal-footer">
      <button type="button" class="btn-cancel" onclick="closeAppointmentPassModal()">Close</button>
      <button type="button" class="btn-action-tool btn-tool-print" onclick="window.print()">
        <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
        Print Official Pass
      </button>
    </div>
  </div>
</div>

<script>
  // ── Appointment Pass Modal Logic ──
  function openAppointmentPassModal(data) {
    if(!data) return;
    const modal = document.getElementById('appointment-pass-modal');
    const refPad = String(data.id || '1').padStart(4, '0');
    
    document.getElementById('pass-ref-no').innerText = '#MC-' + refPad;
    document.getElementById('pass-barcode-text').innerText = 'ISCAG-MC-' + refPad;
    document.getElementById('pass-reason').innerText = data.reason || 'General Spiritual Guidance';
    
    if(data.preferred_date) {
      const d = new Date(data.preferred_date);
      document.getElementById('pass-date').innerText = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    } else {
      document.getElementById('pass-date').innerText = 'Confirmed';
    }
    
    document.getElementById('pass-time').innerText = data.preferred_time || '10:00 AM';
    modal.classList.add('active');
  }

  function closeAppointmentPassModal() {
    document.getElementById('appointment-pass-modal').classList.remove('active');
  }

  function sendInquiryNote() {
    const txt = document.getElementById('inquiry-text').value.trim();
    if (!txt) {
      alert('Please type your inquiry message first.');
      return;
    }
    alert('As-salamu alaykum. Your note has been forwarded to the counselor. You will receive a response shortly.');
    document.getElementById('inquiry-text').value = '';
  }

  // ── Scheduling Logic ──
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

  if(openBtn) openBtn.onclick = () => { if(modal) modal.classList.add('active'); renderCalendar(); };
  if(closeBtn) closeBtn.onclick = () => closeModal();

  function closeModal() {
    if(!modal) return;
    modal.classList.remove('active');
    if(calStep) calStep.classList.add('active');
    if(timeStep) timeStep.classList.remove('active');
  }

  function renderCalendar() {
    if(!calGrid || !calTitle) return;
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
        <div class="calendar-day ${isBlocked ? 'booked' : ''} ${isPast ? 'disabled' : ''} ${isToday ? 'today' : ''} ${selectedDate === dateStr ? 'selected' : ''}" 
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
    const disp = document.getElementById('display-selected-date');
    if(disp) disp.innerText = new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
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
    const txt = document.getElementById('selected-schedule-text');
    const inDate = document.getElementById('input-date');
    const inTime = document.getElementById('input-time');
    if(txt) txt.innerText = `${new Date(selectedDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} at ${selectedTime}`;
    if(inDate) inDate.value = selectedDate;
    if(inTime) inTime.value = selectedTime;
    closeModal();
  };

  window.changeMonth = (delta) => {
    currentMonth += delta;
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    else if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    renderCalendar();
  };

  // ── Form Submission Handler ──
  async function submitCounselingForm() {
    const reason = document.getElementById('reason-select')?.value;
    const prefDate = document.getElementById('input-date')?.value;
    const prefTime = document.getElementById('input-time')?.value;
    const desc = document.getElementById('input-description')?.value;
    const privacyCheck = document.getElementById('privacy-check');
    const submitBtn = document.getElementById('submit-form');

    if (!reason) {
      alert('Please select the reason for counseling.');
      document.getElementById('reason-select')?.focus();
      return;
    }
    if (!desc || desc.trim() === '') {
      alert('Please provide a brief description of your concern.');
      document.getElementById('input-description')?.focus();
      return;
    }
    if (!prefDate || !prefTime) {
      alert('Please select your preferred schedule (date and time slot).');
      document.getElementById('open-calendar')?.click();
      return;
    }
    if (!privacyCheck || !privacyCheck.checked) {
      alert('Please agree to the Confidentiality Policy before submitting.');
      privacyCheck?.focus();
      return;
    }

    try {
      if(submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting Request...';
      }

      const payload = {
        gender: 'male',
        reason: reason,
        preferred_date: prefDate,
        preferred_time: prefTime,
        description: desc
      };

      const response = await fetch('<?= url('/user/services/counseling/submit') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const res = await response.json();
      if (res.success) {
        alert('As-salamu alaykum. Your counseling request has been successfully submitted! Our counselors will review your schedule.');
        location.reload();
      } else {
        alert(res.message || 'Failed to submit request. Please verify your information.');
        if(submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerText = 'Submit Request';
        }
      }
    } catch (err) {
      console.error(err);
      alert('An unexpected network error occurred while submitting your request.');
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Submit Request';
      }
    }
  }
</script>
</body>
</html>
