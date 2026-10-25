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

    /* ── Counseling Alert UI Modal & Toast ── */
    #counseling-alert-modal {
      z-index: 100000;
      backdrop-filter: blur(6px);
      background: rgba(15, 30, 22, 0.65);
      position: fixed; inset: 0;
      display: none; align-items: center; justify-content: center;
      padding: 20px;
      animation: alertBackdropFade 0.25s ease-out forwards;
    }
    #counseling-alert-modal.active {
      display: flex;
    }
    @keyframes alertBackdropFade {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    @keyframes alertCardPop {
      0% { opacity: 0; transform: scale(0.9) translateY(16px); }
      70% { transform: scale(1.02) translateY(-2px); }
      100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .counseling-alert-card {
      background: #ffffff;
      border-radius: 24px;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 30px 60px -12px rgba(15, 30, 22, 0.35), 0 0 0 1px rgba(0,0,0,0.05);
      overflow: hidden;
      animation: alertCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      display: flex;
      flex-direction: column;
    }
    .alert-top-stripe {
      height: 6px;
      width: 100%;
      background: linear-gradient(90deg, #14532D, #D4AF37);
      transition: background 0.3s;
    }
    .alert-top-stripe.success { background: linear-gradient(90deg, #10b981, #059669); }
    .alert-top-stripe.warning { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .alert-top-stripe.error { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .alert-top-stripe.info { background: linear-gradient(90deg, #14532D, #D4AF37); }

    .counseling-alert-body {
      padding: 32px 28px 16px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .alert-icon-ring {
      width: 68px;
      height: 68px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
      position: relative;
      transition: all 0.3s;
    }
    .alert-icon-ring.success { background: #ecfdf5; color: #10b981; box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.12); }
    .alert-icon-ring.warning { background: #fffbeb; color: #f59e0b; box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.15); }
    .alert-icon-ring.error { background: #fef2f2; color: #ef4444; box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.12); }
    .alert-icon-ring.info { background: #f0fdf4; color: #14532D; box-shadow: 0 0 0 8px rgba(20, 83, 45, 0.1); }

    .alert-icon-ring svg {
      width: 34px;
      height: 34px;
      fill: currentColor;
    }

    .counseling-alert-title {
      font-family: 'Lora', serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: #111827;
      margin: 0 0 8px;
      line-height: 1.3;
    }
    .counseling-alert-desc {
      font-size: 0.92rem;
      color: #4b5563;
      line-height: 1.6;
      margin: 0;
      max-width: 370px;
    }

    /* Details Box in Alert (for reference number & schedule summary) */
    .counseling-alert-details {
      margin-top: 18px;
      width: 100%;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 14px 16px;
      text-align: left;
      font-size: 0.85rem;
      box-sizing: border-box;
    }
    .alert-detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    .alert-detail-row:last-child {
      margin-bottom: 0;
    }
    .alert-detail-label {
      color: #64748b;
      font-weight: 600;
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .alert-detail-val {
      font-weight: 700;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .alert-ref-pill {
      background: #dcfce7;
      color: #15803d;
      padding: 3px 8px;
      border-radius: 6px;
      font-family: monospace;
      font-weight: 800;
      font-size: 0.88rem;
      border: 1px solid #bbf7d0;
    }

    /* Progress bar for auto-redirect */
    .alert-countdown-wrap {
      width: 100%;
      margin-top: 16px;
      background: #f1f5f9;
      border-radius: 10px;
      height: 5px;
      overflow: hidden;
    }
    .alert-countdown-bar {
      height: 100%;
      width: 100%;
      background: linear-gradient(90deg, #14532D, #10b981);
      transition: width 1s linear;
    }

    .counseling-alert-actions {
      padding: 16px 24px 24px;
      display: flex;
      gap: 12px;
      width: 100%;
      box-sizing: border-box;
    }
    .btn-alert-btn {
      flex: 1;
      padding: 12px 18px;
      border-radius: 12px;
      font-size: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      border: none;
      font-family: inherit;
    }
    .btn-alert-btn.primary {
      background: linear-gradient(135deg, #14532D, #0f3e21);
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(20, 83, 45, 0.25);
    }
    .btn-alert-btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(20, 83, 45, 0.32);
    }
    .btn-alert-btn.primary.warning-btn {
      background: linear-gradient(135deg, #d97706, #b45309);
      box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
    }
    .btn-alert-btn.primary.error-btn {
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
    }
    .btn-alert-btn.secondary {
      background: #f8fafc;
      color: #475569;
      border: 1px solid #e2e8f0;
      flex: 0 0 auto;
      padding: 12px 16px;
    }
    .btn-alert-btn.secondary:hover {
      background: #f1f5f9;
      color: #1e293b;
    }

    /* Floating Toast Notification */
    .counseling-toast-box {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 100001;
      display: flex;
      flex-direction: column;
      gap: 10px;
      pointer-events: none;
    }
    .counseling-toast {
      pointer-events: auto;
      background: #ffffff;
      color: #1e293b;
      padding: 12px 18px;
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.06);
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.88rem;
      font-weight: 600;
      animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      max-width: 380px;
    }
    @keyframes toastSlideIn {
      from { opacity: 0; transform: translateX(40px); }
      to { opacity: 1; transform: translateX(0); }
    }

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
    /*  APPROVED APPOINTMENT & SCHEDULE HUB STYLES             */
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

    /* ── 6-Step Dynamic Lifecycle Journey ── */
    .journey-tracker-6 {
      display: flex; justify-content: space-between; position: relative; margin: 28px 12px 14px;
    }
    .journey-6-line-bg {
      position: absolute; top: 18px; left: 24px; right: 24px; height: 3px; background: #e5e7eb; z-index: 1;
    }
    .journey-6-line-fill {
      position: absolute; top: 18px; left: 24px; height: 3px;
      background: linear-gradient(90deg, var(--primary-male), #10b981); z-index: 2; transition: width 0.4s ease;
    }
    .journey-6-step {
      display: flex; flex-direction: column; align-items: center; position: relative; z-index: 3;
      gap: 8px; width: 85px; text-align: center;
    }
    .journey-6-dot {
      width: 36px; height: 36px; border-radius: 50%; background: #fff;
      border: 3px solid #e5e7eb; color: #9ca3af; display: flex; align-items: center;
      justify-content: center; font-size: 0.82rem; font-weight: 800; transition: all 0.3s;
    }
    .journey-6-step.done .journey-6-dot {
      background: var(--primary-male); border-color: var(--primary-male); color: #fff;
    }
    .journey-6-step.done .journey-6-dot svg { width: 16px; height: 16px; fill: #fff; }
    .journey-6-step.active .journey-6-dot {
      border-color: #10b981; color: #10b981; background: #fff;
      box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.18);
    }
    .journey-6-step.active .journey-6-dot svg { width: 16px; height: 16px; fill: #10b981; }
    .journey-6-lbl { font-size: 0.70rem; font-weight: 700; text-transform: uppercase; color: #6b7280; line-height: 1.25; }
    .journey-6-step.done .journey-6-lbl { color: var(--primary-male); font-weight: 800; }
    .journey-6-step.active .journey-6-lbl { color: #10b981; font-weight: 800; }

    /* ── 3-Step Simple Pending Timeline ── */
    .timeline-3-wrap {
      background: #fdfdfd; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 20px; margin: 20px 0;
    }
    .timeline-3-row {
      display: flex; justify-content: space-between; position: relative; margin-top: 10px;
    }
    .timeline-3-row::before {
      content: ''; position: absolute; top: 18px; left: 14%; right: 14%; height: 3px; background: #e2e8f0; z-index: 1;
    }
    .timeline-3-step {
      position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; text-align: center; flex: 1;
    }
    .timeline-3-circle {
      width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: 2.5px solid #cbd5e1;
      display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #64748b; margin-bottom: 8px;
    }
    .timeline-3-step.done .timeline-3-circle {
      background: var(--primary-male); border-color: var(--primary-male); color: #fff;
    }
    .timeline-3-step.active .timeline-3-circle {
      background: #fff; border-color: #f59e0b; color: #d97706; box-shadow: 0 0 0 4px rgba(245,158,11,0.2);
    }
    .timeline-3-title { font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 2px; }
    .timeline-3-desc { font-size: 0.70rem; color: var(--text-muted); }

    /* ── Online Meeting Callout ── */
    .online-meeting-box {
      background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; margin-top: 14px;
    }
    .online-meeting-btn {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px;
      background: #2563eb; color: #fff; font-size: 0.85rem; font-weight: 700; text-decoration: none; margin-top: 8px; transition: all 0.2s;
    }
    .online-meeting-btn:hover { background: #1d4ed8; transform: translateY(-1px); }

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
    /* ️ PRINTABLE APPOINTMENT SLIP / PASS MODAL STYLES         */
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
      .approved-calendar-layout { grid-template-columns: 1fr !important; }
      .pending-summary-grid { grid-template-columns: 1fr !important; }
    }

    /* ══════════════════════════════════════════════════════════ */
    /*  APPROVED SCHEDULE CALENDAR COMPONENT                   */
    /* ══════════════════════════════════════════════════════════ */
    .schedule-calendar-hub-card {
      background: #fff; border: 1.5px solid #bbf7d0; border-radius: 18px;
      padding: 24px 28px; box-shadow: 0 4px 20px rgba(20, 83, 45, 0.05); margin-bottom: 24px;
    }
    .approved-calendar-layout {
      display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px; align-items: stretch;
    }
    .approved-cal-container {
      background: #f8faf9; border: 1px solid var(--border); border-radius: 14px;
      padding: 18px; display: flex; flex-direction: column;
    }
    .approved-cal-top {
      display: flex; justify-content: space-between; align-items: center; gap: 10px;
      margin-bottom: 14px; flex-wrap: wrap;
    }
    .approved-cal-month-nav {
      display: flex; align-items: center; gap: 8px;
    }
    .approved-cal-month-name {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: var(--primary-male);
      min-width: 140px; text-align: center;
    }
    .btn-cal-step {
      width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
      background: #fff; display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all 0.2s; color: var(--text-main);
    }
    .btn-cal-step:hover { background: var(--primary-male-light); border-color: var(--primary-male); color: var(--primary-male); }
    .btn-jump-today {
      display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
      background: #fff; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 0.75rem;
      font-weight: 700; color: #166534; cursor: pointer; transition: all 0.2s;
    }
    .btn-jump-today:hover { background: #dcfce7; border-color: #86efac; }
    
    .approved-weekdays-row {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center;
      font-size: 0.7rem; font-weight: 800; color: #9ca3af; text-transform: uppercase;
      padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; margin-bottom: 8px;
    }
    .approved-days-grid {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;
    }
    .approved-cal-day {
      aspect-ratio: 1; border-radius: 8px; display: flex; flex-direction: column;
      align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600;
      cursor: pointer; transition: all 0.2s; position: relative; border: 1px solid transparent;
      color: #374151; background: #fff;
    }
    .approved-cal-day:hover:not(.empty) { background: #f0fdf4; border-color: #86efac; }
    .approved-cal-day.empty { background: transparent; cursor: default; }
    .approved-cal-day.today { font-weight: 800; color: var(--primary-male); }
    .approved-cal-day.today::after {
      content: ''; position: absolute; bottom: 3px; width: 4px; height: 4px;
      border-radius: 50%; background: var(--primary-male);
    }
    .approved-cal-day.has-session {
      background: #14532D !important; color: #fff !important; font-weight: 800;
      border-color: #d4af37 !important; box-shadow: 0 4px 10px rgba(20, 83, 45, 0.35);
    }
    .approved-cal-day.has-session::after {
      content: ''; position: absolute; bottom: 3px; width: 6px; height: 6px;
      border-radius: 50%; background: #fde047; box-shadow: 0 0 0 2px #14532D;
    }
    .approved-cal-day.selected-view:not(.has-session) {
      border-color: var(--primary-male); background: #f0fdf4; font-weight: 800;
    }

    /* Inspector Card */
    .approved-session-inspector {
      background: linear-gradient(135deg, #f0fdf4, #fbfefc); border: 1.5px solid #bbf7d0;
      border-radius: 14px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;
    }
    .inspector-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #dcfce7; color: #166534; border: 1px solid #86efac;
      align-self: flex-start; margin-bottom: 8px;
    }
    .inspector-date {
      font-family: 'Lora', serif; font-size: 1.15rem; font-weight: 700; color: #14532D; margin: 0 0 14px;
    }
    .inspector-info-list {
      display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;
    }
    .inspector-item {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;
      padding-bottom: 8px; border-bottom: 1px dashed #dcfce7; font-size: 0.82rem;
    }
    .inspector-item:last-child { border-bottom: none; padding-bottom: 0; }
    .inspector-item .item-label { color: #6b7280; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; }
    .inspector-item .item-val { color: #1f2937; font-weight: 700; text-align: right; }
    .inspector-item .item-val.highlight { color: #166534; font-family: 'Lora', serif; font-weight: 700; }
    .inspector-actions {
      display: flex; gap: 10px; flex-wrap: wrap; margin-top: auto; padding-top: 12px;
    }
    .btn-inspector-action {
      flex: 1; padding: 8px 14px; border-radius: 8px; font-size: 0.78rem; font-weight: 700;
      cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
      justify-content: center; gap: 6px; transition: all 0.2s; border: none;
    }
    .btn-inspector-action.btn-print { background: var(--primary-male); color: #fff; }
    .btn-inspector-action.btn-print:hover { background: var(--primary-male-dark); }
    .btn-inspector-action.btn-google-cal { background: #fff; color: #374151; border: 1.5px solid var(--border); }
    .btn-inspector-action.btn-google-cal:hover { background: #f9fafb; border-color: #9ca3af; }

    /* ══════════════════════════════════════════════════════════ */
    /*  PENDING REVIEW MODAL STYLES                             */
    /* ══════════════════════════════════════════════════════════ */
    .pending-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 540px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden;
      animation: modalPop 0.35s ease; border: 1px solid rgba(0,0,0,0.06);
    }
    .pending-modal-header {
      background: linear-gradient(135deg, #14532D, #0f3d24); padding: 24px 28px;
      color: #fff; display: flex; align-items: center; gap: 14px; position: relative;
    }
    .pending-icon-wrap {
      width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.15);
      border: 1.5px solid rgba(212, 175, 55, 0.4); display: flex; align-items: center;
      justify-content: center; flex-shrink: 0;
    }
    .pending-icon-wrap svg { width: 26px; height: 26px; fill: #fff; }
    .pending-tag {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
      color: #fde047; margin-bottom: 2px;
    }
    .pending-modal-title {
      font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; margin: 0 0 2px; color: #fff;
    }
    .pending-modal-sub { font-size: 0.78rem; color: rgba(255,255,255,0.8); margin: 0; }
    .pending-modal-body { padding: 24px 28px; }
    .pending-status-banner {
      display: flex; align-items: center; gap: 8px; padding: 10px 16px;
      border-radius: 10px; background: #fef3c7; border: 1px solid #fde68a;
      font-size: 0.82rem; color: #92400e; margin-bottom: 16px;
    }
    .status-pulse-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;
      box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); animation: warningPulse 1.8s infinite;
    }
    @keyframes warningPulse {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
    .pending-primary-msg {
      font-size: 0.88rem; color: #4b5563; line-height: 1.5; margin: 0 0 16px;
    }
    .pending-info-callout {
      display: flex; gap: 12px; background: #f0fdf4; border: 1px solid #bbf7d0;
      border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;
    }
    .callout-icon {
      width: 32px; height: 32px; border-radius: 8px; background: #dcfce7;
      color: #166534; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .callout-icon svg { width: 18px; height: 18px; fill: currentColor; }
    .callout-content strong {
      display: block; font-size: 0.82rem; color: #166534; margin-bottom: 2px;
    }
    .callout-content p {
      font-size: 0.78rem; color: #15803d; margin: 0; line-height: 1.45;
    }
    .pending-summary-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .pending-summary-item {
      background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 10px 12px;
    }
    .pending-summary-item .lbl {
      display: block; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
      color: #9ca3af; margin-bottom: 2px;
    }
    .pending-summary-item .val {
      font-size: 0.85rem; font-weight: 700; color: #111827;
    }
    .pending-modal-footer {
      padding: 16px 28px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-pending-sec {
      padding: 9px 18px; background: #fff; border: 1.5px solid var(--border);
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #4b5563;
      text-decoration: none; cursor: pointer; transition: all 0.2s;
    }
    .btn-pending-sec:hover { background: #f3f4f6; color: #111827; }
    .btn-pending-pri {
      padding: 9px 20px; background: var(--primary-male); border: none;
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #fff;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-pending-pri:hover { background: var(--primary-male-dark); }

    /* ══════════════════════════════════════════════════════════ */
    /*  VISUAL PENDING REQUEST HUB (PREMIUM REDESIGN)           */
    /* ══════════════════════════════════════════════════════════ */
    .pending-hub-card {
      background: #fff; border-radius: 20px; border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(20, 83, 45, 0.08); overflow: hidden; margin-bottom: 28px;
    }
    .pending-hero-banner {
      background: linear-gradient(135deg, #064e3b 0%, #14532D 60%, #0f3d24 100%);
      padding: 32px 36px 28px; position: relative; overflow: hidden; color: #fff;
    }
    .pending-hero-banner::after {
      content: ''; position: absolute; right: -40px; bottom: -40px; width: 220px; height: 220px;
      border-radius: 50%; background: rgba(212, 175, 55, 0.12); pointer-events: none;
    }
    .pending-hero-banner::before {
      content: ''; position: absolute; right: 140px; top: -30px; width: 140px; height: 140px;
      border-radius: 50%; background: rgba(255, 255, 255, 0.05); pointer-events: none;
    }
    .pending-top-row {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
      position: relative; z-index: 1; flex-wrap: wrap; margin-bottom: 24px;
    }
    .pending-title-area { display: flex; align-items: center; gap: 18px; }
    .pending-avatar-icon {
      width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
      border: 2px solid rgba(212, 175, 55, 0.5); flex-shrink: 0; box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }
    .pending-avatar-icon svg { width: 32px; height: 32px; fill: #fff; }
    .pending-headings h3 {
      font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; margin: 0 0 4px;
      letter-spacing: -0.01em; color: #fff;
    }
    .pending-headings p {
      font-size: 0.85rem; color: rgba(255, 255, 255, 0.85); margin: 0;
    }
    .badge-pending-pulse {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px;
      border-radius: 30px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.06em; background: rgba(245, 158, 11, 0.22); border: 1.5px solid rgba(253, 224, 71, 0.5);
      backdrop-filter: blur(10px); color: #fef08a; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .pulse-dot-amber {
      width: 8px; height: 8px; border-radius: 50%; background: #fde047;
      box-shadow: 0 0 0 0 rgba(253, 224, 71, 0.7); animation: warningPulse 1.8s infinite;
    }

    /* Metric highlights strip */
    .pending-schedule-strip {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
      position: relative; z-index: 1;
    }
    .pmetric-box {
      background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 14px 16px;
      transition: all 0.2s;
    }
    .pmetric-box:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-2px); }
    .pmetric-lbl {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.75); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .pmetric-val {
      font-size: 0.95rem; font-weight: 700; color: #fff; line-height: 1.3;
    }
    .pmetric-val.gold { color: #fde047; }

    /* Pending 2-Column Hub Grid */
    .pending-hub-grid {
      display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; padding: 32px 36px;
    }

    /* Calendar Preview Teaser Card */
    .calendar-teaser-card {
      background: linear-gradient(135deg, #fefce8, #f0fdf4); border: 1.5px dashed #f59e0b;
      border-radius: 16px; padding: 22px 24px; margin-bottom: 24px; position: relative;
    }
    .calendar-teaser-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
      margin-bottom: 12px;
    }
    .calendar-teaser-content {
      display: flex; gap: 16px; align-items: center;
    }
    .calendar-teaser-icon {
      width: 52px; height: 52px; border-radius: 14px; background: #fff;
      border: 2px solid #fde68a; display: flex; align-items: center; justify-content: center;
      color: #d97706; flex-shrink: 0; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
    }
    .calendar-teaser-icon svg { width: 28px; height: 28px; fill: currentColor; }
    .calendar-teaser-text h5 {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #14532D; margin: 0 0 4px;
    }
    .calendar-teaser-text p {
      font-size: 0.82rem; color: #4b5563; margin: 0; line-height: 1.5;
    }

    /* Enhanced 3-Phase Step Tracker */
    .pending-journey-wrap {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; margin-bottom: 24px;
    }
    .pending-journey-steps {
      display: flex; justify-content: space-between; position: relative; margin-top: 14px;
    }
    .pending-journey-steps::before {
      content: ''; position: absolute; top: 22px; left: 12%; right: 12%; height: 3px;
      background: #e5e7eb; z-index: 1;
    }
    .pjourney-step {
      display: flex; flex-direction: column; align-items: center; position: relative;
      z-index: 2; text-align: center; flex: 1;
    }
    .pjourney-circle {
      width: 44px; height: 44px; border-radius: 50%; background: #f9fafb;
      border: 3px solid #e5e7eb; display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 0.9rem; color: #9ca3af; margin-bottom: 8px; transition: all 0.3s;
    }
    .pjourney-step.done .pjourney-circle {
      background: var(--primary-male); border-color: var(--primary-male); color: #fff;
    }
    .pjourney-step.active .pjourney-circle {
      background: #fff; border-color: #f59e0b; color: #d97706;
      box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.2);
    }
    .pjourney-title { font-size: 0.82rem; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
    .pjourney-desc { font-size: 0.70rem; color: #6b7280; line-height: 1.3; }

    /* Amanah Trust Notice Box */
    .amanah-trust-box {
      background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 14px;
      padding: 18px 20px; display: flex; gap: 14px; align-items: center;
    }
    .amanah-icon {
      width: 40px; height: 40px; border-radius: 10px; background: #dcfce7;
      color: #166534; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .amanah-icon svg { width: 22px; height: 22px; fill: currentColor; }
    .amanah-text strong { display: block; font-size: 0.85rem; color: #166534; margin-bottom: 2px; }
    .amanah-text p { font-size: 0.78rem; color: #15803d; margin: 0; line-height: 1.45; }

    @media (max-width: 960px) {
      .pending-hub-grid { grid-template-columns: 1fr; padding: 20px; }
      .pending-schedule-strip { grid-template-columns: 1fr 1fr; }
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

      <?php 
        $activeRequest = $activeRequest ?? null;
        if (!$activeRequest && !empty($history)) {
            $activeRequest = $history[0];
        }

        $curStatus = $activeRequest ? strtolower($activeRequest['status'] ?? '') : '';
        $isConfirmed = in_array($curStatus, ['confirmed', 'approved']);
        $isPending = in_array($curStatus, ['pending', 'under_review', 'approved_awaiting_schedule']);
        $isRejected = ($curStatus === 'rejected');
        $isCompleted = ($curStatus === 'completed');

        $activeRef = $activeRequest ? '#MC-' . str_pad((string)$activeRequest['id'], 4, '0', STR_PAD_LEFT) : '';
        $assignedCounselorName = !empty($activeRequest['counselor_full_name']) 
            ? $activeRequest['counselor_full_name'] 
            : (!empty($activeRequest['counselor_name']) ? $activeRequest['counselor_name'] : 'Ustaz Norman Ungasin');
        $counselorRole = !empty($activeRequest['counselor_designation']) 
            ? $activeRequest['counselor_designation'] 
            : (!empty($activeRequest['counselor_role']) ? $activeRequest['counselor_role'] : 'Senior Islamic Counselor & Scholar');
        $counselorContact = !empty($activeRequest['counselor_contact_info']) 
            ? $activeRequest['counselor_contact_info'] 
            : (!empty($activeRequest['counselor_contact']) ? $activeRequest['counselor_contact'] : 'kizumikaze1@gmail.com');
        $counselorPhone = !empty($activeRequest['counselor_phone']) ? $activeRequest['counselor_phone'] : '+63 906 574 0819';
        $counselorOffice = !empty($activeRequest['counselor_office']) ? $activeRequest['counselor_office'] : 'ISCAG Center, 2nd Floor Da\'wah Office Room 201';

        $confirmedDate = !empty($activeRequest['appointment_date']) 
            ? $activeRequest['appointment_date'] 
            : (!empty($activeRequest['preferred_date']) ? $activeRequest['preferred_date'] : null);
        $confirmedTime = !empty($activeRequest['start_time']) 
            ? date('g:i A', strtotime($activeRequest['start_time'])) . (!empty($activeRequest['end_time']) ? ' – ' . date('g:i A', strtotime($activeRequest['end_time'])) : '')
            : (!empty($activeRequest['preferred_time']) ? $activeRequest['preferred_time'] : '10:00 AM');
        $sessionMode = !empty($activeRequest['final_session_type']) 
            ? $activeRequest['final_session_type'] 
            : (!empty($activeRequest['session_type']) ? $activeRequest['session_type'] : 'In-Person');
        $venueLocation = !empty($activeRequest['appointment_location']) 
            ? $activeRequest['appointment_location'] 
            : 'ISCAG Mosque & Da\'wah Center, 2nd Floor — Brothers\' Guidance Room (Room 204)';
        $meetingLink = !empty($activeRequest['meeting_link']) ? $activeRequest['meeting_link'] : '';
        $meetingInstructions = !empty($activeRequest['meeting_instructions']) ? $activeRequest['meeting_instructions'] : 'Please ensure a quiet private setting and test your microphone/audio 5 minutes prior to the session.';

        // 6 Lifecycle Steps for Dynamic Tracker:
        $step1 = true;
        $step2 = in_array($curStatus, ['under_review', 'approved_awaiting_schedule', 'confirmed', 'approved', 'completed']);
        $step3 = in_array($curStatus, ['approved_awaiting_schedule', 'confirmed', 'approved', 'completed']);
        $step4 = (!empty($activeRequest['counselor_id']) || !empty($activeRequest['counselor_name'])) || in_array($curStatus, ['confirmed', 'approved', 'completed']);
        $step5 = in_array($curStatus, ['confirmed', 'approved', 'completed']);
        $step6 = ($curStatus === 'completed');

        $step2Active = ($curStatus === 'under_review');
        $step3Active = ($curStatus === 'approved_awaiting_schedule');
        $step5Active = in_array($curStatus, ['confirmed', 'approved']);
      ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  A. COUNSELING OVERVIEW SUMMARY CARDS                    -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="user-analytics" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card">
          <div class="stat-label">Request Status</div>
          <div class="stat-value <?= $isConfirmed ? 'success' : ($isPending ? 'warning' : ($isRejected ? 'danger' : '')) ?>" style="font-size: 1.25rem;">
            <?php 
              if ($isConfirmed) echo 'Confirmed';
              elseif ($isPending) echo 'Pending Review';
              elseif ($isRejected) echo 'Rejected';
              elseif ($isCompleted) echo 'Completed';
              else echo 'Ready to Book';
            ?>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-label">Next Counseling Session</div>
          <div class="stat-value" style="font-size: 1.05rem;">
            <?php if ($isConfirmed && !empty($confirmedDate)): ?>
              <?= date('M d, Y', strtotime($confirmedDate)) ?>
            <?php elseif ($isPending): ?>
              <span style="font-size:0.92rem; color:#b45309;">Awaiting Schedule</span>
            <?php else: ?>
              <span style="font-size:0.92rem; color:#6b7280;">None Scheduled</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-label">Assigned Counselor</div>
          <div class="stat-value" style="font-size: 1.05rem;">
            <?php if ($isConfirmed): ?>
              <?= htmlspecialchars($assignedCounselorName) ?>
            <?php elseif ($isPending): ?>
              <span style="font-size:0.92rem; color:#b45309;">Pending Review</span>
            <?php else: ?>
              <span style="font-size:0.92rem; color:#6b7280;">None Assigned</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-label">Session Type</div>
          <div class="stat-value" style="font-size: 1.05rem;">
            <?= htmlspecialchars($sessionMode) ?>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  CASE 1: SCHEDULE APPROVED BY ADMIN (APPOINTMENT HUB)    -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php if ($isConfirmed && $activeRequest): ?>
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
                <h3>Counseling &amp; Spiritual Guidance Appointment</h3>
                <p>Ref No: <strong><?= htmlspecialchars($activeRef) ?></strong> • Confirmed with <?= htmlspecialchars($assignedCounselorName) ?></p>
              </div>
            </div>
            <div>
              <div class="badge-live-pulse">
                <div class="pulse-dot"></div>
                Confirmed &amp; Scheduled
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
              <div class="sched-metric-val gold"><?= !empty($confirmedDate) ? date('M d, Y', strtotime($confirmedDate)) : 'Confirmed' ?></div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                Session Time
              </div>
              <div class="sched-metric-val"><?= htmlspecialchars($confirmedTime) ?></div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Session Mode &amp; Venue
              </div>
              <div class="sched-metric-val">In-Person Room 204</div>
            </div>

            <div class="sched-metric-box">
              <div class="sched-metric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Assigned Counselor
              </div>
              <div class="sched-metric-val"><?= htmlspecialchars($assignedCounselorName) ?></div>
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
              $calDate = !empty($confirmedDate) ? date('Ymd', strtotime($confirmedDate)) : date('Ymd');
              $calUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode("ISCAG Counseling Session — " . $activeRef) . "&dates=" . $calDate . "T020000Z/" . $calDate . "T030000Z&details=" . urlencode("ISCAG Confidential Brother's Counseling Session regarding " . ($activeRequest['reason'] ?? 'Guidance') . ". Counselor: " . $assignedCounselorName) . "&location=" . urlencode($venueLocation);
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

        <!--  DEDICATED APPOINTMENT SCHEDULE CALENDAR (APPEARS WHEN ADMIN APPROVES) -->
        <div style="padding: 28px 36px 0;">
          <div class="hub-card schedule-calendar-hub-card">
            <div class="hub-card-title">
              <span>
                <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Appointment Schedule Calendar
              </span>
              <span class="detail-badge-pill" style="background:#dcfce7; color:#166534; font-weight:800;">
                Confirmed &amp; Scheduled by Administrator
              </span>
            </div>

            <div class="approved-calendar-layout">
              <!-- Calendar Left: Interactive Monthly Grid -->
              <div class="approved-cal-container">
                <div class="approved-cal-top">
                  <div class="approved-cal-month-nav">
                    <button type="button" class="btn-cal-step" onclick="navApprovedCounselingCal(-1)" title="Previous Month">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                    </button>
                    <span id="approved-cal-title-counseling" class="approved-cal-month-name">Loading Month...</span>
                    <button type="button" class="btn-cal-step" onclick="navApprovedCounselingCal(1)" title="Next Month">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </button>
                  </div>
                  <button type="button" class="btn-jump-today" onclick="jumpToConfirmedCounselingSession()">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    Jump to My Confirmed Session
                  </button>
                </div>

                <div class="approved-weekdays-row">
                  <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
                <div id="approved-cal-grid-counseling" class="approved-days-grid"></div>
              </div>

              <!-- Calendar Right: Schedule Details Inspector -->
              <div class="approved-session-inspector">
                <div>
                  <div class="inspector-badge">
                    <span class="pulse-dot"></span>
                    <span id="inspector-status-text">Confirmed Session</span>
                  </div>
                  <h4 class="inspector-date" id="inspector-date-text"><?= !empty($confirmedDate) ? date('l, F j, Y', strtotime($confirmedDate)) : 'Scheduled Consultation' ?></h4>
                  
                  <div class="inspector-info-list">
                    <div class="inspector-item">
                      <span class="item-label">Consultation Time</span>
                      <span class="item-val" id="inspector-time-text"><?= htmlspecialchars($confirmedTime) ?></span>
                    </div>
                    <div class="inspector-item">
                      <span class="item-label">Assigned Counselor</span>
                      <span class="item-val highlight" id="inspector-counselor-text"><?= htmlspecialchars($assignedCounselorName) ?></span>
                    </div>
                    <div class="inspector-item">
                      <span class="item-label">Session Venue</span>
                      <span class="item-val" id="inspector-venue-text"><?= htmlspecialchars($venueLocation) ?></span>
                    </div>
                    <div class="inspector-item">
                      <span class="item-label">Primary Concern</span>
                      <span class="item-val"><?= htmlspecialchars(ucfirst($activeRequest['reason'] ?? 'Guidance')) ?></span>
                    </div>
                    <div class="inspector-item">
                      <span class="item-label">Reference Number</span>
                      <span class="item-val" style="font-family:monospace;"><?= htmlspecialchars($activeRef) ?></span>
                    </div>
                  </div>
                </div>

                <div class="inspector-actions">
                  <button type="button" class="btn-inspector-action btn-print" onclick="openAppointmentPassModal(<?= htmlspecialchars(json_encode($activeRequest)) ?>)">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                    Print Slip
                  </button>
                  <a href="<?= $calUrl ?>" target="_blank" class="btn-inspector-action btn-google-cal">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
                    Google Cal
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Two Column Main Grid -->
        <div class="schedule-hub-grid">
          <!-- Left Column -->
          <div>
            <!--  B. DEDICATED APPOINTMENT SCHEDULE SECTION -->
            <div class="hub-card" style="margin-bottom:24px; border-left: 4px solid var(--primary-male);">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                  Confirmed Appointment Schedule
                </span>
                <span class="detail-badge-pill" style="background:#dcfce7; color:#166534; font-weight:800;"><?= htmlspecialchars($sessionMode) ?> Consultation</span>
              </h5>

              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Appointment Date</span>
                  <span class="detail-val highlight"><?= !empty($confirmedDate) ? date('l, F j, Y', strtotime($confirmedDate)) : 'Confirmed' ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Start &amp; End Time</span>
                  <span class="detail-val"><?= htmlspecialchars($confirmedTime) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Assigned Counselor</span>
                  <span class="detail-val"><?= htmlspecialchars($assignedCounselorName) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Session Type</span>
                  <span class="detail-val">In-Person Consultation</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Room / Location</span>
                  <span class="detail-val"><?= htmlspecialchars($venueLocation) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Appointment Status</span>
                  <span class="detail-val" style="color:#166534; font-weight:800;">Confirmed by Administrator</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Reference Number</span>
                  <span class="detail-val" style="font-family:monospace; font-weight:800;"><?= htmlspecialchars($activeRef) ?></span>
                </div>
              </div>
            </div>

            <!--  D. COUNSELING REQUEST DETAILS SECTION (CONFIDENTIAL) -->
            <div class="hub-card" style="margin-bottom:24px;">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                  Submitted Request Details
                </span>
                <span class="detail-badge-pill" style="background:#f1f5f9; color:#475569;">Confidential Amanah</span>
              </h5>

              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Primary Concern</span>
                  <span class="detail-val highlight"><?= htmlspecialchars(ucfirst($activeRequest['reason'] ?? 'General Guidance')) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Preferred Schedule</span>
                  <span class="detail-val">
                    <?= !empty($activeRequest['preferred_date']) ? date('M d, Y', strtotime($activeRequest['preferred_date'])) : 'Flexible Date' ?>
                    <?= !empty($activeRequest['preferred_time']) ? ' at ' . htmlspecialchars($activeRequest['preferred_time']) : '' ?>
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Preferred Session Type</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['session_type'] ?? 'In-Person') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Reference Number</span>
                  <span class="detail-val" style="font-family:monospace; font-weight:800; color:var(--primary-male);"><?= htmlspecialchars($activeRef) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Submission Date</span>
                  <span class="detail-val"><?= !empty($activeRequest['created_at']) ? date('F j, Y — g:i A', strtotime($activeRequest['created_at'])) : '—' ?></span>
                </div>
              </div>
              <div style="margin-top:12px; padding:10px 14px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; font-size:0.78rem; color:#166534; display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <span>Client records are confidential and protected under Da'wah Department Islamic privacy standards.</span>
              </div>
            </div>

            <!--  E. 6-STEP DYNAMIC LIFECYCLE JOURNEY TRACKER -->
            <div class="hub-card">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  Counseling Journey Lifecycle
                </span>
                <span style="font-size:0.75rem; color:#10b981; font-weight:800;">
                  Step <?= $step6 ? '6 of 6' : ($step5 ? '5 of 6' : ($step4 ? '4 of 6' : ($step3 ? '3 of 6' : ($step2 ? '2 of 6' : '1 of 6')))) ?>
                </span>
              </h5>

              <div class="journey-tracker-6">
                <div class="journey-6-line-bg"></div>
                <div class="journey-6-line-fill" style="width: <?= $step6 ? '100%' : ($step5 ? '80%' : ($step4 ? '60%' : ($step3 ? '40%' : ($step2 ? '20%' : '5%')))) ?>;"></div>

                <!-- 1: Request Submitted -->
                <div class="journey-6-step <?= $step1 ? 'done' : '' ?>">
                  <div class="journey-6-dot">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <span class="journey-6-lbl">Request<br>Submitted</span>
                </div>

                <!-- 2: Under Review -->
                <div class="journey-6-step <?= $step2 ? 'done' : ($step2Active ? 'active' : '') ?>">
                  <div class="journey-6-dot">
                    <?= $step2 ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' : '2' ?>
                  </div>
                  <span class="journey-6-lbl">Under<br>Review</span>
                </div>

                <!-- 3: Approved -->
                <div class="journey-6-step <?= $step3 ? 'done' : ($step3Active ? 'active' : '') ?>">
                  <div class="journey-6-dot">
                    <?= $step3 ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' : '3' ?>
                  </div>
                  <span class="journey-6-lbl">Approved</span>
                </div>

                <!-- 4: Counselor Assigned -->
                <div class="journey-6-step <?= $step4 ? 'done' : '' ?>">
                  <div class="journey-6-dot">
                    <?= $step4 ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' : '4' ?>
                  </div>
                  <span class="journey-6-lbl">Counselor<br>Assigned</span>
                </div>

                <!-- 5: Appointment Confirmed -->
                <div class="journey-6-step <?= $step5 ? 'done' : ($step5Active ? 'active' : '') ?>">
                  <div class="journey-6-dot">
                    <?= $step5 ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' : '5' ?>
                  </div>
                  <span class="journey-6-lbl">Appointment<br>Confirmed</span>
                </div>

                <!-- 6: Completed -->
                <div class="journey-6-step <?= $step6 ? 'done' : '' ?>">
                  <div class="journey-6-dot">
                    <?= $step6 ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' : '6' ?>
                  </div>
                  <span class="journey-6-lbl">Completed</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!--  C. ASSIGNED COUNSELOR INFORMATION SECTION -->
            <div class="hub-card counselor-card" style="margin-bottom:24px;">
              <div class="counselor-header">
                <div class="counselor-avatar" style="width: 56px; height: 56px; border-radius: 14px; background: #fef3c7; color: #b45309; font-size: 1.3rem;">
                  <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="counselor-info">
                  <h5 style="margin:0 0 2px; font-size:1rem; font-weight:700; color:#1f2937;"><?= htmlspecialchars($assignedCounselorName) ?></h5>
                  <p style="margin:0 0 4px; font-size:0.78rem; color:#b45309; font-weight:700;"><?= htmlspecialchars($counselorRole) ?></p>
                  <span style="font-size:0.72rem; color:#6b7280;">Resident Islamic Guidance Scholar</span>
                </div>
              </div>

              <div class="counselor-contacts">
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                  <span>Direct Line: <strong><?= htmlspecialchars($counselorPhone) ?></strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                  <span>Official Email: <strong><?= htmlspecialchars($counselorContact) ?></strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  <span>Office: <strong><?= htmlspecialchars($counselorOffice) ?></strong></span>
                </div>
              </div>

              <div style="background:#fff; border-radius:10px; padding:12px; border:1px solid #fed7aa; margin-bottom:14px;">
                <div style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:#b45309; margin-bottom:4px;">Scheduled Session With Counselor</div>
                <div style="font-size:0.88rem; font-weight:700; color:#1f2937;">
                  <?= !empty($confirmedDate) ? date('l, F j, Y', strtotime($confirmedDate)) : 'Confirmed' ?> • <?= htmlspecialchars($confirmedTime) ?>
                </div>
              </div>

              <!-- Quick Inquiry Message Box -->
              <div>
                <input type="text" class="inquiry-box-input" id="inquiry-text" placeholder="Have a quick question for Ustaz before your session?" />
                <button type="button" class="btn-send-inquiry" onclick="sendInquiryNote()">Send Note to Counselor</button>
              </div>
            </div>

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
                    <strong>State of Taharah (Purity &amp; Wudu)</strong>
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
                    <strong>Specific Questions &amp; Notes</strong>
                    <p>Feel free to prepare written questions to make the best use of your 1-hour session.</p>
                  </div>
                </div>
              </div>

              <div style="margin-top:16px; text-align:center;">
                <a href="<?= url('/user/services/counseling/resources') ?>" style="font-size:0.82rem; font-weight:800; color:var(--primary-male); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                  Explore Brothers' Guidance Articles &amp; Topics →
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Toggle Button to File Another Request -->
        <div style="padding: 16px 36px 24px; background: #fafafa; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
          <span style="font-size:0.85rem; color:#6b7280;">Need to submit another counseling request?</span>
          <button type="button" class="btn-action-tool btn-tool-cal" onclick="toggleBookingForm()">
            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            <span>Book Another Session</span>
          </button>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  CASE 2: PENDING REVIEW HERO (REQUIREMENT 1)             -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php elseif ($isPending && $activeRequest): ?>
      <div class="pending-hub-card">
        <!-- Hero Header Banner -->
        <div class="pending-hero-banner">
          <div class="pending-top-row">
            <div class="pending-title-area">
              <div class="pending-avatar-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              </div>
              <div class="pending-headings">
                <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; color:#fde047; margin-bottom:3px;">
                  Counseling &amp; Spiritual Guidance • Pending Review
                </div>
                <h3>Your Counseling Request Has Been Submitted</h3>
                <p>Ref No: <strong style="color:#fde047; font-family:monospace;"><?= htmlspecialchars($activeRef) ?></strong> • Filed on <?= !empty($activeRequest['created_at']) ? date('M d, Y — g:i A', strtotime($activeRequest['created_at'])) : date('M d, Y') ?></p>
              </div>
            </div>
            <div>
              <div class="badge-pending-pulse">
                <div class="pulse-dot-amber"></div>
                Awaiting Administrator Review
              </div>
            </div>
          </div>

          <!-- Frosted Glass Metric Strip -->
          <div class="pending-schedule-strip">
            <div class="pmetric-box">
              <div class="pmetric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                Primary Concern
              </div>
              <div class="pmetric-val gold"><?= htmlspecialchars(ucfirst($activeRequest['reason'])) ?></div>
            </div>

            <div class="pmetric-box">
              <div class="pmetric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Preferred Schedule
              </div>
              <div class="pmetric-val">
                <?= !empty($activeRequest['preferred_date']) ? date('M d, Y', strtotime($activeRequest['preferred_date'])) : 'Pending' ?>
                <?= !empty($activeRequest['preferred_time']) ? ' (' . htmlspecialchars($activeRequest['preferred_time']) . ')' : '' ?>
              </div>
            </div>

            <div class="pmetric-box">
              <div class="pmetric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Consultation Mode
              </div>
              <div class="pmetric-val"><?= htmlspecialchars($sessionMode) ?> (Room 204)</div>
            </div>

            <div class="pmetric-box">
              <div class="pmetric-lbl">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Assigned Counselor
              </div>
              <div class="pmetric-val" style="color:#e2e8f0; font-size:0.85rem;">Pending Administrator Assignment</div>
            </div>
          </div>
        </div>

        <!-- Two Column Main Grid -->
        <div class="pending-hub-grid">
          <!-- Left Column -->
          <div>
            <!--  Schedule Calendar Teaser / What Happens Next -->
            <div class="calendar-teaser-card">
              <div class="calendar-teaser-badge">
                <span class="status-pulse-dot" style="width:6px;height:6px;"></span>
                Schedule Calendar Preview Notice
              </div>
              <div class="calendar-teaser-content">
                <div class="calendar-teaser-icon">
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
                </div>
                <div class="calendar-teaser-text">
                  <h5>Schedule Calendar Will Unlock Upon Approval</h5>
                  <p>
                    Your request is currently being processed by the Da'wah Guidance Scholars. Once approved, the interactive <strong>Schedule Calendar</strong> will automatically appear on this page showing your confirmed consultation date, assigned scholar, and room pass.
                  </p>
                </div>
              </div>
            </div>

            <!--  3-Phase Dynamic Journey Timeline -->
            <div class="pending-journey-wrap">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h5 style="margin:0; font-family:'Lora',serif; font-size:1.05rem; font-weight:700; color:#14532D;">
                  Request Progress Journey
                </h5>
                <span style="font-size:0.75rem; font-weight:800; color:#d97706; background:#fef3c7; padding:4px 12px; border-radius:20px; border:1px solid #fde68a;">
                  Step 2 of 3: Under Review
                </span>
              </div>

              <div class="pending-journey-steps">
                <!-- Step 1: Received -->
                <div class="pjourney-step done">
                  <div class="pjourney-circle">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <span class="pjourney-title">Request Received</span>
                  <span class="pjourney-desc"><?= !empty($activeRequest['created_at']) ? date('M d, Y', strtotime($activeRequest['created_at'])) : 'Completed' ?></span>
                </div>

                <!-- Step 2: Under Review -->
                <div class="pjourney-step active">
                  <div class="pjourney-circle">2</div>
                  <span class="pjourney-title">Administrator Review</span>
                  <span class="pjourney-desc" style="color:#b45309; font-weight:700;">In Progress</span>
                </div>

                <!-- Step 3: Approved & Calendar -->
                <div class="pjourney-step">
                  <div class="pjourney-circle">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                  </div>
                  <span class="pjourney-title">Schedule Calendar</span>
                  <span class="pjourney-desc">Unlocks upon approval</span>
                </div>
              </div>
            </div>

            <!-- Submitted Request Details Card -->
            <div class="hub-card" style="margin-bottom:0;">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                  Submitted Consultation Details
                </span>
                <span class="detail-badge-pill" style="background:#fef3c7; color:#92400e; font-weight:800;">Pending Review</span>
              </h5>

              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Primary Concern</span>
                  <span class="detail-val highlight"><?= htmlspecialchars(ucfirst($activeRequest['reason'])) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Preferred Schedule</span>
                  <span class="detail-val">
                    <?= !empty($activeRequest['preferred_date']) ? date('l, F j, Y', strtotime($activeRequest['preferred_date'])) : 'Flexible Date' ?>
                    <?= !empty($activeRequest['preferred_time']) ? ' at ' . htmlspecialchars($activeRequest['preferred_time']) : '' ?>
                  </span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Preferred Mode</span>
                  <span class="detail-val"><?= htmlspecialchars($sessionMode) ?> (Room 204)</span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Reference Number</span>
                  <span class="detail-val" style="font-family:monospace; font-weight:800; color:var(--primary-male);"><?= htmlspecialchars($activeRef) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Filing Date</span>
                  <span class="detail-val"><?= !empty($activeRequest['created_at']) ? date('F j, Y — g:i A', strtotime($activeRequest['created_at'])) : '—' ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!--  Amanah Trust Guarantee Box -->
            <div class="amanah-trust-box" style="margin-bottom:24px;">
              <div class="amanah-icon">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
              </div>
              <div class="amanah-text">
                <strong>Strict Confidentiality (Amanah)</strong>
                <p>All guidance conversations and client data are protected under Islamic trust standards and Da'wah Department privacy policies.</p>
              </div>
            </div>

            <!-- Da'wah Department Office Desk -->
            <div class="hub-card counselor-card" style="margin-bottom:24px;">
              <div class="counselor-header">
                <div class="counselor-avatar" style="width: 56px; height: 56px; border-radius: 14px; background: #fef3c7; color: #b45309; font-size: 1.3rem;">
                  <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div class="counselor-info">
                  <h5 style="margin:0 0 2px; font-size:1rem; font-weight:700; color:#1f2937;">Ustaz Norman Ungasin</h5>
                  <p style="margin:0 0 4px; font-size:0.78rem; color:#b45309; font-weight:700;">Resident Islamic Guidance Scholar</p>
                  <span style="font-size:0.72rem; color:#6b7280;">ISCAG Da'wah Department Head</span>
                </div>
              </div>

              <div class="counselor-contacts">
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                  <span>Office Direct: <strong>+63 906 574 0819</strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                  <span>Official Email: <strong>kizumikaze1@gmail.com</strong></span>
                </div>
                <div class="contact-item">
                  <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  <span>Office: <strong>2nd Floor Da'wah Room 201</strong></span>
                </div>
              </div>

              <div style="background:#fefce8; border-radius:10px; padding:12px; border:1px solid #fde68a;">
                <div style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:#b45309; margin-bottom:2px;">Review Turnaround</div>
                <div style="font-size:0.82rem; color:#451a03; line-height:1.4;">
                  Requests are typically reviewed within <strong>24 to 48 hours</strong>. You will receive an in-app notification upon confirmation.
                </div>
              </div>
            </div>

            <!-- Pre-Session Guidance Checklist -->
            <div class="hub-card">
              <h5 class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                  While Awaiting Confirmation
                </span>
              </h5>
              <div class="checklist-list">
                <div class="check-item">
                  <div class="check-num">1</div>
                  <div class="check-content">
                    <strong>Prepare Specific Questions</strong>
                    <p>Writing down key points helps maximize the benefit of your 1-hour consultation.</p>
                  </div>
                </div>
                <div class="check-item">
                  <div class="check-num">2</div>
                  <div class="check-content">
                    <strong>Purity of Intention (Niyyah)</strong>
                    <p>Approach the guidance session seeking sincere Islamic reconciliation and spiritual growth.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Toolbar at Bottom -->
        <div style="padding: 20px 36px 24px; background: #fafafa; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
          <div style="font-size: 0.85rem; color: #4b5563;">
            Need to change your preferred time or topic before admin review?
          </div>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?= url('/user/dashboard') ?>" class="btn-action-tool btn-tool-cal">
              ← Return to Dashboard
            </a>
            <a href="<?= url('/user/services/counseling/status?id=' . $activeRequest['id']) ?>" class="btn-action-tool btn-tool-guidance">
              View Status Page →
            </a>
            <button type="button" class="btn-action-tool btn-tool-cal" onclick="toggleBookingForm()">
              Submit Another Request
            </button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  CASE 3: REJECTED REQUEST NOTICE                         -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php elseif ($isRejected && $activeRequest): ?>
      <div class="section-card" style="margin-bottom:24px; border-left: 4px solid #ef4444;">
        <div style="padding: 24px 28px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 14px;">
            <div>
              <span class="badge-status badge-rejected" style="margin-bottom: 8px;">Request Disapproved</span>
              <h4 style="font-family: 'Lora', serif; margin: 0; color: #991b1b;">Counseling Request <?= htmlspecialchars($activeRef) ?> Could Not Be Scheduled</h4>
            </div>
            <button type="button" class="btn-action-tool btn-tool-print" onclick="toggleBookingForm()">
              Submit a New Request
            </button>
          </div>
          <div style="background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 18px; margin-bottom: 14px; font-size: 0.9rem; color: #991b1b;">
            <strong>Reason Provided by Administrator:</strong><br>
            <?= nl2br(htmlspecialchars($activeRequest['rejection_reason'] ?: 'The requested schedule could not be accommodated. Please submit an alternative schedule.')) ?>
          </div>
          <p style="margin: 0; font-size: 0.85rem; color: #6b7280;">
            You may review our available counseling schedule and submit another counseling request using the form below.
          </p>
        </div>
      </div>
      <?php endif; ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  BOOKING FORM WRAPPER (PRESERVES EXISTING FORM INTACT)   -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div id="counseling-booking-form-wrap" style="<?= ($isConfirmed || $isPending) ? 'display:none;' : '' ?>;">

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
                <label style="display:flex;align-items:center;gap:8px;font-size:0.88rem;color:var(--text-main,#111827);font-weight:600;">
                  <input type="hidden" name="session_type" value="In-Person" />
                  <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:var(--primary);"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  In-Person (Da'wah Male Department)
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
      </div> <!-- end counseling-booking-form-wrap -->

      <!-- ══════════════════════════════════════════════════════════ -->
      <!--  MY APPLICATION HISTORY TABLE                            -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Counseling History &amp; Records
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Reason for Request</th>
                  <th>Appointment / Preferred Schedule</th>
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
                    <?php 
                      $hSt = strtolower($h['status'] ?? '');
                      $hRef = '#MC-' . str_pad((string)$h['id'], 4, '0', STR_PAD_LEFT);
                      $hDate = !empty($h['appointment_date']) ? $h['appointment_date'] : (!empty($h['preferred_date']) ? $h['preferred_date'] : null);
                      $hTime = !empty($h['start_time']) ? date('g:i A', strtotime($h['start_time'])) . (!empty($h['end_time']) ? ' – ' . date('g:i A', strtotime($h['end_time'])) : '') : (!empty($h['preferred_time']) ? $h['preferred_time'] : '');
                    ?>
                    <tr>
                      <td class="td-id"><strong><?= $hRef ?></strong></td>
                      <td style="font-weight:600;"><?= htmlspecialchars(ucfirst($h['reason'])) ?></td>
                      <td>
                        <?= !empty($hDate) ? date('M d, Y', strtotime($hDate)) : 'Flexible Schedule' ?>
                        <?= !empty($hTime) ? ' • ' . htmlspecialchars($hTime) : '' ?>
                      </td>
                      <td><?= date('M d, Y', strtotime($h['created_at'])) ?></td>
                      <td>
                        <?php if (in_array($hSt, ['confirmed', 'approved'])): ?>
                          <span class="badge-status badge-approved" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">Confirmed</span>
                        <?php elseif (in_array($hSt, ['pending', 'under_review', 'approved_awaiting_schedule'])): ?>
                          <span class="badge-status badge-pending" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;">
                            <?= ($hSt === 'approved_awaiting_schedule') ? 'Awaiting Schedule' : ucfirst(str_replace('_', ' ', $hSt)) ?>
                          </span>
                        <?php elseif ($hSt === 'completed'): ?>
                          <span class="badge-status" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">Completed</span>
                        <?php else: ?>
                          <span class="badge-status badge-rejected" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;"><?= ucfirst($hSt) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (in_array($hSt, ['confirmed', 'approved', 'completed'])): ?>
                          <button type="button" class="btn-view-doc" onclick='openAppointmentPassModal(<?= json_encode($h) ?>)' title="View Official Pass">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                            View Pass
                          </button>
                        <?php else: ?>
                          <a href="<?= url('/user/services/counseling/status?id=' . $h['id']) ?>" class="btn-view-doc" style="text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                            Track Status →
                          </a>
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
<!-- ️ PRINTABLE APPOINTMENT PASS MODAL                        -->
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
          <div class="slip-item-val" id="pass-session-mode">In-Person Consultation</div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Applicant Name</div>
          <div class="slip-item-val" id="pass-client-name"><?= htmlspecialchars($_SESSION['name'] ?? 'Brother') ?></div>
        </div>
        <div class="slip-item">
          <div class="slip-item-label">Assigned Counselor</div>
          <div class="slip-item-val" id="pass-counselor-name">Ustaz Norman Ungasin</div>
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
          <div class="slip-item-val" id="pass-venue">ISCAG Mosque & Da'wah Center, 2nd Floor — Brothers' Guidance Room (Room 204)</div>
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

<!-- ══════════════════════════════════════════════════════════ -->
<!--  COUNSELING ALERT / NOTIFICATION MODAL                   -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="counseling-alert-modal" onclick="if(event.target===this) closeCounselingAlertModal()">
  <div class="counseling-alert-card" role="dialog" aria-modal="true" aria-labelledby="counseling-alert-title">
    <div class="alert-top-stripe" id="counseling-alert-stripe"></div>
    <div class="counseling-alert-body">
      <div class="alert-icon-ring" id="counseling-alert-icon">
        <svg id="counseling-alert-svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
      </div>
      <h3 class="counseling-alert-title" id="counseling-alert-title">Notice</h3>
      <p class="counseling-alert-desc" id="counseling-alert-desc"></p>
      
      <!-- Structured summary box for confirmations / details -->
      <div class="counseling-alert-details" id="counseling-alert-details" style="display:none;"></div>
      
      <!-- Animated countdown progress bar for auto-redirection -->
      <div class="alert-countdown-wrap" id="counseling-alert-countdown-wrap" style="display:none;">
        <div class="alert-countdown-bar" id="counseling-alert-countdown-bar"></div>
      </div>
    </div>
    <div class="counseling-alert-actions">
      <button type="button" class="btn-alert-btn secondary" id="counseling-alert-cancel-btn" style="display:none;" onclick="closeCounselingAlertModal(false)">Cancel</button>
      <button type="button" class="btn-alert-btn primary" id="counseling-alert-confirm-btn" onclick="closeCounselingAlertModal(true)">Acknowledge</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!--  COUNSELING PENDING REVIEW MODAL                         -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="counseling-pending-modal" onclick="if(event.target===this) closeCounselingPendingModal(false)">
  <div class="pending-modal-card" role="dialog" aria-modal="true" aria-labelledby="cpm-title">
    <div class="pending-modal-header">
      <div class="pending-icon-wrap">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
      </div>
      <div>
        <div class="pending-tag">Request Successfully Submitted</div>
        <h3 class="pending-modal-title" id="cpm-title">Pending Administrator Review</h3>
        <p class="pending-modal-sub">Reference Number: <strong id="cpm-ref-text">#MC-AUTO</strong></p>
      </div>
      <button type="button" class="btn-close-modal" onclick="closeCounselingPendingModal(false)" style="margin-left:auto; color:rgba(255,255,255,0.7);">
        <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
      </button>
    </div>

    <div class="pending-modal-body">
      <div class="pending-status-banner">
        <span class="status-pulse-dot"></span>
        <span>Status: <strong>Pending Administrator Review &amp; Counselor Assignment</strong></span>
      </div>

      <p class="pending-primary-msg">
        As-salamu alaykum. Your counseling appointment request has been transmitted to the ISCAG Da'wah &amp; Guidance Department.
      </p>

      <div class="pending-info-callout">
        <div class="callout-icon">
          <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
        </div>
        <div class="callout-content">
          <strong>Official Schedule Calendar Notice:</strong>
          <p>Once the administrator reviews and approves your form, your <strong>Official Schedule Calendar</strong> will automatically appear here with your confirmed appointment date, session time, room number, and assigned Islamic counselor.</p>
        </div>
      </div>

      <div class="pending-summary-grid">
        <div class="pending-summary-item">
          <span class="lbl">Primary Concern</span>
          <span class="val" id="cpm-reason">—</span>
        </div>
        <div class="pending-summary-item">
          <span class="lbl">Preferred Schedule</span>
          <span class="val" id="cpm-schedule">—</span>
        </div>
        <div class="pending-summary-item">
          <span class="lbl">Consultation Mode</span>
          <span class="val" id="cpm-mode">In-Person Consultation</span>
        </div>
        <div class="pending-summary-item">
          <span class="lbl">Reviewing Office</span>
          <span class="val">ISCAG Da'wah Department</span>
        </div>
      </div>
    </div>

    <div class="pending-modal-footer">
      <a href="<?= url('/user/dashboard') ?>" class="btn-pending-sec">Return to Dashboard</a>
      <button type="button" class="btn-pending-pri" onclick="closeCounselingPendingModal(true)">View Status in Portal</button>
    </div>
  </div>
</div>

<!-- Floating Toast Container -->
<div class="counseling-toast-box" id="counseling-toast-box"></div>

<script>
  // ── Appointment Pass Modal Logic ──
  function openAppointmentPassModal(data) {
    if(!data) return;
    const modal = document.getElementById('appointment-pass-modal');
    const refPad = String(data.id || '1').padStart(4, '0');
    
    document.getElementById('pass-ref-no').innerText = '#MC-' + refPad;
    document.getElementById('pass-barcode-text').innerText = 'ISCAG-MC-' + refPad;
    document.getElementById('pass-reason').innerText = data.reason || 'General Spiritual Guidance';
    
    const counselorName = data.counselor_full_name || data.counselor_name || 'Ustaz Norman Ungasin';
    const cEl = document.getElementById('pass-counselor-name');
    if(cEl) cEl.innerText = counselorName;

    const sessionMode = 'In-Person';
    const modeEl = document.getElementById('pass-session-mode');
    if(modeEl) modeEl.innerText = 'In-Person Consultation';

    const venue = data.appointment_location || 'ISCAG Center, 2nd Floor — Brothers\' Guidance Room (Room 204)';
    const venueEl = document.getElementById('pass-venue');
    if(venueEl) venueEl.innerText = venue;
    
    const aptDate = data.appointment_date || data.preferred_date;
    if(aptDate) {
      const d = new Date(aptDate);
      document.getElementById('pass-date').innerText = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    } else {
      document.getElementById('pass-date').innerText = 'Confirmed';
    }
    
    const timeSlot = data.start_time 
      ? (data.start_time + (data.end_time ? ' – ' + data.end_time : '')) 
      : (data.preferred_time || '10:00 AM');
    document.getElementById('pass-time').innerText = timeSlot;
    modal.classList.add('active');
  }

  function closeAppointmentPassModal() {
    document.getElementById('appointment-pass-modal').classList.remove('active');
  }

  function toggleBookingForm() {
    const wrap = document.getElementById('counseling-booking-form-wrap');
    if (!wrap) return;
    if (wrap.style.display === 'none' || wrap.style.display === '') {
      wrap.style.display = 'block';
      wrap.scrollIntoView({ behavior: 'smooth' });
    } else {
      wrap.style.display = 'none';
    }
  }

  // ── Escape HTML Helper ──
  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  // ── Custom Alert Modal Controller ──
  let counselingAlertCallback = null;
  let counselingAlertCancelCallback = null;
  let alertCountdownTimer = null;

  function showCounselingAlert(options) {
    const {
      title = 'Notice',
      message = '',
      type = 'info', // 'success', 'warning', 'error', 'info'
      detailsHtml = null,
      confirmText = 'Acknowledge',
      cancelText = null,
      onConfirm = null,
      onCancel = null,
      autoRedirectUrl = null,
      autoRedirectSeconds = null
    } = options;

    const modal = document.getElementById('counseling-alert-modal');
    if (!modal) return;

    if (alertCountdownTimer) {
      clearInterval(alertCountdownTimer);
      alertCountdownTimer = null;
    }

    const stripe = document.getElementById('counseling-alert-stripe');
    const icon = document.getElementById('counseling-alert-icon');
    const svg = document.getElementById('counseling-alert-svg');
    const titleEl = document.getElementById('counseling-alert-title');
    const descEl = document.getElementById('counseling-alert-desc');
    const detailsBox = document.getElementById('counseling-alert-details');
    const countdownWrap = document.getElementById('counseling-alert-countdown-wrap');
    const countdownBar = document.getElementById('counseling-alert-countdown-bar');
    const confirmBtn = document.getElementById('counseling-alert-confirm-btn');
    const cancelBtn = document.getElementById('counseling-alert-cancel-btn');

    // Reset style classes
    stripe.className = 'alert-top-stripe ' + type;
    icon.className = 'alert-icon-ring ' + type;
    confirmBtn.className = 'btn-alert-btn primary' + (type === 'warning' ? ' warning-btn' : (type === 'error' ? ' error-btn' : ''));

    // SVG icons by type
    const icons = {
      success: '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>',
      warning: '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
      error: '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>',
      info: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>'
    };
    svg.innerHTML = icons[type] || icons.info;

    titleEl.innerText = title;
    descEl.innerText = message;

    if (detailsHtml) {
      detailsBox.innerHTML = detailsHtml;
      detailsBox.style.display = 'block';
    } else {
      detailsBox.innerHTML = '';
      detailsBox.style.display = 'none';
    }

    confirmBtn.innerText = confirmText;
    counselingAlertCallback = onConfirm;
    counselingAlertCancelCallback = onCancel;

    if (cancelText) {
      cancelBtn.innerText = cancelText;
      cancelBtn.style.display = 'inline-flex';
    } else {
      cancelBtn.style.display = 'none';
    }

    if (autoRedirectUrl && autoRedirectSeconds) {
      countdownWrap.style.display = 'block';
      countdownBar.style.width = '100%';
      let remaining = autoRedirectSeconds;
      const initialSeconds = autoRedirectSeconds;
      confirmBtn.innerText = `${confirmText} (${remaining}s)`;

      alertCountdownTimer = setInterval(() => {
        remaining -= 1;
        const pct = Math.max(0, (remaining / initialSeconds) * 100);
        countdownBar.style.width = pct + '%';
        confirmBtn.innerText = `${confirmText} (${remaining}s)`;
        if (remaining <= 0) {
          clearInterval(alertCountdownTimer);
          alertCountdownTimer = null;
          closeCounselingAlertModal(true);
          window.location.href = autoRedirectUrl;
        }
      }, 1000);
    } else {
      countdownWrap.style.display = 'none';
    }

    modal.classList.add('active');
  }

  function closeCounselingAlertModal(triggeredByConfirm = false) {
    if (alertCountdownTimer) {
      clearInterval(alertCountdownTimer);
      alertCountdownTimer = null;
    }
    const modal = document.getElementById('counseling-alert-modal');
    if (modal) modal.classList.remove('active');

    if (triggeredByConfirm && typeof counselingAlertCallback === 'function') {
      const cb = counselingAlertCallback;
      counselingAlertCallback = null;
      cb();
    } else if (!triggeredByConfirm && typeof counselingAlertCancelCallback === 'function') {
      const cb = counselingAlertCancelCallback;
      counselingAlertCancelCallback = null;
      cb();
    }
  }

  // Floating toast notification helper
  function showCounselingToast(msg, type = 'info') {
    const box = document.getElementById('counseling-toast-box');
    if (!box) return;
    const toast = document.createElement('div');
    toast.className = 'counseling-toast';
    const color = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#14532D');
    toast.innerHTML = `
      <div style="width:20px;height:20px;color:${color};flex-shrink:0;">
        <svg viewBox="0 0 24 24" style="fill:currentColor;width:100%;height:100%;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
      </div>
      <div>${escapeHtml(msg)}</div>
    `;
    box.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(40px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 3200);
  }

  function sendInquiryNote() {
    const txt = document.getElementById('inquiry-text')?.value.trim();
    if (!txt) {
      showCounselingAlert({
        title: 'Message Required',
        message: 'Please type your inquiry or note first before sending.',
        type: 'warning',
        confirmText: 'Type Message',
        onConfirm: () => document.getElementById('inquiry-text')?.focus()
      });
      return;
    }
    showCounselingAlert({
      title: 'Inquiry Forwarded',
      message: 'As-salamu alaykum. Your note has been forwarded to the counselor. You will receive a response shortly.',
      type: 'success',
      confirmText: 'Done'
    });
    const inquiryInput = document.getElementById('inquiry-text');
    if (inquiryInput) inquiryInput.value = '';
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
             onclick="${isBlocked ? `showBlockReason('${escapeHtml(blockReason)}')` : ((!isPast) ? `selectDate('${dateStr}')` : '')}">
          ${day}
        </div>
      `;
    }
  }

  window.showBlockReason = (reason) => {
    showCounselingAlert({
      title: 'Date Unavailable',
      message: reason ? `This date cannot be selected: ${reason}` : 'This date is currently not open for bookings.',
      type: 'info',
      confirmText: 'Understood'
    });
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
      showCounselingAlert({
        title: 'Counseling Concern Required',
        message: 'Please choose the primary reason or topic for your counseling consultation before proceeding.',
        type: 'warning',
        confirmText: 'Select Concern',
        onConfirm: () => {
          const el = document.getElementById('reason-select');
          if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }
      });
      return;
    }

    if (!desc || desc.trim() === '') {
      showCounselingAlert({
        title: 'Description Required',
        message: 'Please provide a brief description of your concern so our counselor can properly prepare for your consultation.',
        type: 'warning',
        confirmText: 'Provide Description',
        onConfirm: () => {
          const el = document.getElementById('input-description');
          if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        }
      });
      return;
    }

    if (!prefDate || !prefTime) {
      showCounselingAlert({
        title: 'Schedule Selection Required',
        message: 'Please choose your preferred consultation date and available time slot using the calendar.',
        type: 'warning',
        confirmText: 'Select Date & Time',
        onConfirm: () => {
          document.getElementById('open-calendar')?.click();
        }
      });
      return;
    }

    if (!privacyCheck || !privacyCheck.checked) {
      showCounselingAlert({
        title: 'Confidentiality Policy Agreement',
        message: 'Please review and agree to the Confidentiality Policy of the Da\'wah Department before submitting your request.',
        type: 'warning',
        confirmText: 'Agree & Check Box',
        onConfirm: () => {
          if (privacyCheck) {
            privacyCheck.checked = true;
            privacyCheck.focus();
          }
        }
      });
      return;
    }

    try {
      if(submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting Request...';
      }

      const sessionType = document.querySelector('input[name="session_type"]:checked')?.value || 'In-Person';
      const payload = {
        gender: 'male',
        full_name: document.getElementById('input-fullname')?.value || '',
        age: document.getElementById('input-age')?.value || '',
        civil_status: document.getElementById('input-civil')?.value || '',
        address: document.getElementById('input-address')?.value || '',
        contact_number: document.getElementById('input-contact')?.value || '',
        reason: reason,
        session_type: sessionType,
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
        const refFormatted = res.ref || ('#MC-' + String(res.id || '').padStart(4, '0'));
        const targetUrl = res.redirect || ('<?= url('/user/services/counseling/status') ?>?id=' + (res.id || ''));
        const dateFormatted = new Date(prefDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        openCounselingPendingModal({
          ref: refFormatted,
          reason: reason,
          schedule: `${dateFormatted} at ${prefTime}`,
          mode: sessionType,
          id: res.id,
          targetUrl: targetUrl
        });
        return;
      } else {
        showCounselingAlert({
          title: 'Submission Failed',
          message: res.message || 'Failed to submit request. Please verify your information and try again.',
          type: 'error',
          confirmText: 'Review Details'
        });
        if(submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerText = 'Submit Request';
        }
      }
    } catch (err) {
      console.error(err);
      showCounselingAlert({
        title: 'Connection Error',
        message: 'An unexpected network error occurred while submitting your counseling request. Please check your connection and try again.',
        type: 'error',
        confirmText: 'Try Again'
      });
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Submit Request';
      }
    }
  }

  // ── Counseling Pending Modal Functions ──
  function openCounselingPendingModal(data) {
    const modal = document.getElementById('counseling-pending-modal');
    if (!modal) return;
    if (data.ref) {
      const refEl = document.getElementById('cpm-ref-text');
      if (refEl) refEl.innerText = data.ref;
    }
    if (data.reason) {
      const rEl = document.getElementById('cpm-reason');
      if (rEl) rEl.innerText = data.reason;
    }
    if (data.schedule) {
      const sEl = document.getElementById('cpm-schedule');
      if (sEl) sEl.innerText = data.schedule;
    }
    if (data.mode) {
      const mEl = document.getElementById('cpm-mode');
      if (mEl) mEl.innerText = data.mode;
    }
    modal.classList.add('active');
  }

  function closeCounselingPendingModal(shouldReload = true) {
    const modal = document.getElementById('counseling-pending-modal');
    if (modal) modal.classList.remove('active');
    if (shouldReload) {
      window.location.reload();
    }
  }

  // ── Approved Counseling Schedule Calendar Logic ──
  const CONFIRMED_COUNSELING_DATE = <?= !empty($confirmedDate) ? json_encode(date('Y-m-d', strtotime($confirmedDate))) : 'null' ?>;
  const CONFIRMED_COUNSELING_TIME = <?= json_encode($confirmedTime ?? '10:00 AM') ?>;
  const ASSIGNED_COUNSELOR_NAME = <?= json_encode($assignedCounselorName ?? 'Ustaz') ?>;
  const CONFIRMED_COUNSELING_VENUE = <?= json_encode($venueLocation ?? 'ISCAG Center Room 204') ?>;

  let approvedCalYear = CONFIRMED_COUNSELING_DATE ? new Date(CONFIRMED_COUNSELING_DATE + 'T00:00:00').getFullYear() : new Date().getFullYear();
  let approvedCalMonth = CONFIRMED_COUNSELING_DATE ? new Date(CONFIRMED_COUNSELING_DATE + 'T00:00:00').getMonth() : new Date().getMonth();
  let selectedApprovedDay = CONFIRMED_COUNSELING_DATE;

  function renderApprovedCounselingCal() {
    const titleEl = document.getElementById('approved-cal-title-counseling');
    const gridEl = document.getElementById('approved-cal-grid-counseling');
    if (!titleEl || !gridEl) return;

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    titleEl.innerText = `${monthNames[approvedCalMonth]} ${approvedCalYear}`;

    const firstDayIndex = new Date(approvedCalYear, approvedCalMonth, 1).getDay();
    const daysInMonth = new Date(approvedCalYear, approvedCalMonth + 1, 0).getDate();
    const todayStr = new Date().toISOString().split('T')[0];

    let html = '';
    for (let i = 0; i < firstDayIndex; i++) {
      html += `<div class="approved-cal-day empty"></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${approvedCalYear}-${String(approvedCalMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
      const isConfirmedSession = (CONFIRMED_COUNSELING_DATE === dateStr);
      const isToday = (todayStr === dateStr);
      const isSelected = (selectedApprovedDay === dateStr);

      let classes = ['approved-cal-day'];
      if (isConfirmedSession) classes.push('has-session');
      if (isToday) classes.push('today');
      if (isSelected && !isConfirmedSession) classes.push('selected-view');

      html += `
        <div class="${classes.join(' ')}" onclick="selectApprovedCounselingDay('${dateStr}')" title="${isConfirmedSession ? 'Confirmed Counseling Session' : ''}">
          <span>${d}</span>
        </div>
      `;
    }

    gridEl.innerHTML = html;
  }

  function navApprovedCounselingCal(dir) {
    approvedCalMonth += dir;
    if (approvedCalMonth < 0) {
      approvedCalMonth = 11;
      approvedCalYear--;
    } else if (approvedCalMonth > 11) {
      approvedCalMonth = 0;
      approvedCalYear++;
    }
    renderApprovedCounselingCal();
  }

  function jumpToConfirmedCounselingSession() {
    if (!CONFIRMED_COUNSELING_DATE) return;
    const d = new Date(CONFIRMED_COUNSELING_DATE + 'T00:00:00');
    approvedCalYear = d.getFullYear();
    approvedCalMonth = d.getMonth();
    selectedApprovedDay = CONFIRMED_COUNSELING_DATE;
    renderApprovedCounselingCal();
    updateApprovedInspector(CONFIRMED_COUNSELING_DATE);
  }

  function selectApprovedCounselingDay(dateStr) {
    selectedApprovedDay = dateStr;
    renderApprovedCounselingCal();
    updateApprovedInspector(dateStr);
  }

  function updateApprovedInspector(dateStr) {
    const isSessionDay = (dateStr === CONFIRMED_COUNSELING_DATE);
    const dateObj = new Date(dateStr + 'T00:00:00');
    const formatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

    const dateEl = document.getElementById('inspector-date-text');
    const statusEl = document.getElementById('inspector-status-text');
    const timeEl = document.getElementById('inspector-time-text');
    const counselorEl = document.getElementById('inspector-counselor-text');
    const venueEl = document.getElementById('inspector-venue-text');

    if (dateEl) dateEl.innerText = formatted;
    if (statusEl) {
      statusEl.innerText = isSessionDay ? 'Confirmed Session' : 'No Scheduled Session';
    }
    if (timeEl) {
      timeEl.innerText = isSessionDay ? CONFIRMED_COUNSELING_TIME : 'No Appointment';
    }
    if (counselorEl) {
      counselorEl.innerText = isSessionDay ? ASSIGNED_COUNSELOR_NAME : '—';
    }
    if (venueEl) {
      venueEl.innerText = isSessionDay ? CONFIRMED_COUNSELING_VENUE : '—';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('approved-cal-grid-counseling')) {
      renderApprovedCounselingCal();
    }
  });
  if (document.getElementById('approved-cal-grid-counseling')) {
    renderApprovedCounselingCal();
  }
</script>
</body>
</html>
