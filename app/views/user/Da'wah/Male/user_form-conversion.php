<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 5));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$dbUser = $dbUser ?? [];
$history = $history ?? [];

$hasApproved = false;
$hasPending = false;
$activeRequest = null;

foreach ($history as $req) {
    $st = strtolower($req['status'] ?? '');
    if (in_array($st, ['approved', 'accepted', 'confirmed'])) {
        $hasApproved = true;
        $activeRequest = $req;
        break;
    }
    if (in_array($st, ['pending', 'under_review']) && !$hasApproved) {
        $hasPending = true;
        $activeRequest = $req;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Conversion to Islam Registration</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    .form-step { display: none; }
    .form-step.active { display: block; animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
    
    .step-indicator { display: flex; gap: 12px; margin-bottom: 32px; justify-content: center; }
    .step-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--border); transition: all 0.3s; }
    .step-dot.active { background: var(--primary); transform: scale(1.3); box-shadow: 0 0 10px rgba(15,92,58,0.3); }
    .step-dot.completed { background: var(--success); }

    .testimony-box {
      background: #fdfdfd;
      border: 1.5px dashed var(--primary-light);
      border-radius: 16px;
      padding: 32px;
      margin: 24px 0;
      text-align: center;
    }
    .shahada-text {
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 16px 0;
      line-height: 1.6;
    }
    .shahada-translation {
      font-style: italic;
      color: var(--text-muted);
      font-size: 0.95rem;
    }
    
    .section-divider { height: 1px; background: var(--border); margin: 24px 0; position: relative; }
    .section-divider::after { content: attr(data-label); position: absolute; top: -10px; left: 20px; background: white; padding: 0 10px; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; }

    .other-field-wrap { 
      max-height: 0; 
      opacity: 0; 
      overflow: hidden; 
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
      margin-top: 0;
    }
    .other-field-wrap.show { 
      max-height: 80px; 
      opacity: 1; 
      margin-top: 12px;
    }

    /* ══════════════════════════════════════════════════════════ */
    /* CERTIFICATE & APPOINTMENT HUB STYLES                       */
    /* ══════════════════════════════════════════════════════════ */
    .certificate-hub-card {
      background: #fff;
      border-radius: 20px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 32px;
    }
    .cert-hero-banner {
      background: linear-gradient(135deg, #0f3d24 0%, #166534 50%, #14532D 100%);
      padding: 36px 40px;
      color: #fff;
      position: relative;
      overflow: hidden;
    }
    .cert-hero-banner::before {
      content: '';
      position: absolute;
      top: -50px; right: -50px;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(212, 175, 55, 0.12);
      pointer-events: none;
    }
    .cert-hero-top {
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 16px; margin-bottom: 20px;
    }
    .cert-badge-status {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(16, 185, 129, 0.2);
      border: 1px solid rgba(52, 211, 153, 0.4);
      color: #6ee7b7; padding: 6px 16px; border-radius: 30px;
      font-size: 0.8rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.08em;
    }
    .cert-badge-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: #34d399; box-shadow: 0 0 8px #34d399;
      animation: pulseDot 2s infinite;
    }
    @keyframes pulseDot { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.4); opacity: 0.7; } }

    .cert-adopted-banner {
      background: rgba(255, 255, 255, 0.08);
      border: 1.5px solid rgba(212, 175, 55, 0.35);
      border-radius: 14px;
      padding: 20px 24px;
      margin: 16px 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
    }
    .cert-adopted-name {
      font-family: 'Lora', serif;
      font-size: 1.6rem;
      font-weight: 700;
      color: #fde047;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .cert-tools-row {
      display: flex; gap: 12px; flex-wrap: wrap;
    }
    .btn-action-tool {
      display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px;
      border-radius: 10px; font-size: 0.85rem; font-weight: 700; cursor: pointer;
      text-decoration: none; transition: all 0.2s; border: none;
    }
    .btn-tool-print {
      background: linear-gradient(135deg, #D4AF37, #B8860B);
      color: #1a1a1a; font-weight: 800; box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
    }
    .btn-tool-print:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(212, 175, 55, 0.45); }
    
    .btn-tool-resources {
      background: rgba(255, 255, 255, 0.15); color: #fff;
      border: 1.5px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px);
    }
    .btn-tool-resources:hover { background: rgba(255, 255, 255, 0.25); color: #fff; }

    /* Modal for Printable Certificate */
    .cert-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center;
      padding: 20px;
    }
    .cert-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 680px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); max-height: 90vh; overflow-y: auto;
    }

    @media print {
      body * { visibility: hidden; }
      #printable-cert-area, #printable-cert-area * { visibility: visible; }
      #printable-cert-area { position: absolute; left: 0; top: 0; width: 100%; }
      .no-print { display: none !important; }
    }

    /* ══════════════════════════════════════════════════════════ */
    /* SCHEDULE CALENDAR COMPONENT (APPEARS UPON APPROVAL)        */
    /* ══════════════════════════════════════════════════════════ */
    .conversion-calendar-hub-card {
      background: #fff; border: 1.5px solid #fed7aa; border-radius: 18px;
      padding: 24px 28px; box-shadow: 0 4px 20px rgba(245, 158, 11, 0.06); margin-bottom: 24px;
    }
    .conversion-cal-layout {
      display: grid; grid-template-columns: 1.25fr 1fr; gap: 24px; align-items: stretch;
    }
    .conversion-cal-container {
      background: #fdfbf7; border: 1px solid var(--border); border-radius: 14px;
      padding: 18px; display: flex; flex-direction: column;
    }
    .conversion-cal-top {
      display: flex; justify-content: space-between; align-items: center; gap: 10px;
      margin-bottom: 14px; flex-wrap: wrap;
    }
    .conversion-cal-month-nav { display: flex; align-items: center; gap: 8px; }
    .conversion-cal-month-name {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #14532D;
      min-width: 140px; text-align: center;
    }
    .btn-ccal-step {
      width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
      background: #fff; display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all 0.2s; color: var(--text-main);
    }
    .btn-ccal-step:hover { background: #fef3c7; border-color: #f59e0b; color: #b45309; }
    .btn-jump-ceremony {
      display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
      background: #fff; border: 1px solid #fde68a; border-radius: 8px; font-size: 0.75rem;
      font-weight: 700; color: #b45309; cursor: pointer; transition: all 0.2s;
    }
    .btn-jump-ceremony:hover { background: #fef3c7; }
    .conversion-weekdays-row {
      display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center;
      font-size: 0.7rem; font-weight: 800; color: #9ca3af; text-transform: uppercase;
      padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; margin-bottom: 8px;
    }
    .conversion-days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
    .conversion-cal-day {
      aspect-ratio: 1; border-radius: 8px; display: flex; flex-direction: column;
      align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 600;
      cursor: pointer; transition: all 0.2s; position: relative; border: 1px solid transparent;
      color: #374151; background: #fff;
    }
    .conversion-cal-day:hover:not(.empty) { background: #fef3c7; border-color: #f59e0b; }
    .conversion-cal-day.empty { background: transparent; cursor: default; }
    .conversion-cal-day.today { font-weight: 800; color: #b45309; }
    .conversion-cal-day.today::after {
      content: ''; position: absolute; bottom: 3px; width: 4px; height: 4px;
      border-radius: 50%; background: #b45309;
    }
    .conversion-cal-day.has-ceremony {
      background: linear-gradient(135deg, #14532D, #0f3e21) !important; color: #fff !important; font-weight: 800;
      border-color: #f59e0b !important; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.35);
    }
    .conversion-cal-day.has-ceremony::after {
      content: ''; position: absolute; bottom: 3px; width: 6px; height: 6px;
      border-radius: 50%; background: #fde047; box-shadow: 0 0 0 2px #14532D;
    }
    .conversion-cal-day.selected-view:not(.has-ceremony) {
      border-color: #f59e0b; background: #fffbeb; font-weight: 800;
    }
    .conversion-session-inspector {
      background: linear-gradient(135deg, #fffbeb, #fff); border: 1.5px solid #fed7aa;
      border-radius: 14px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;
    }
    .cinspector-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #d1fae5; color: #047857; border: 1px solid #a7f3d0;
      align-self: flex-start; margin-bottom: 8px;
    }
    .pulse-dot {
      width: 7px; height: 7px; border-radius: 50%; background: #10b981;
      animation: pulseDot 2s infinite;
    }
    .cinspector-date {
      font-family: 'Lora', serif; font-size: 1.15rem; font-weight: 700; color: #14532D; margin: 0 0 14px;
    }
    .cinspector-info-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px; }
    .cinspector-item {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;
      padding-bottom: 8px; border-bottom: 1px dashed #fed7aa; font-size: 0.82rem;
    }
    .cinspector-item:last-child { border-bottom: none; padding-bottom: 0; }
    .citem-label { color: #6b7280; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; }
    .citem-val { color: #1f2937; font-weight: 700; text-align: right; }
    .cinspector-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: auto; padding-top: 12px; }

    /* ══════════════════════════════════════════════════════════ */
    /* PENDING REVIEW MODAL STYLES                                */
    /* ══════════════════════════════════════════════════════════ */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(15, 30, 22, 0.65); backdrop-filter: blur(6px);
      z-index: 99999; display: flex; align-items: center; justify-content: center;
      opacity: 0; visibility: hidden; transition: all 0.3s ease; padding: 20px;
    }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .conversion-pending-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 540px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden;
      animation: modalPop 0.35s ease; border: 1px solid rgba(0,0,0,0.06);
    }
    @keyframes modalPop { 0% { opacity: 0; transform: scale(0.92); } 100% { opacity: 1; transform: scale(1); } }
    .conversion-pending-header {
      background: linear-gradient(135deg, #14532D, #0f3d24); padding: 24px 28px;
      color: #fff; display: flex; align-items: center; gap: 14px; position: relative;
    }
    .conversion-icon-wrap {
      width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.15);
      border: 1.5px solid rgba(212, 175, 55, 0.4); display: flex; align-items: center;
      justify-content: center; flex-shrink: 0;
    }
    .conversion-icon-wrap svg { width: 26px; height: 26px; fill: #fff; }
    .conversion-tag {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
      color: #fde047; margin-bottom: 2px;
    }
    .conversion-modal-title {
      font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; margin: 0 0 2px; color: #fff;
    }
    .conversion-modal-sub { font-size: 0.78rem; color: rgba(255,255,255,0.8); margin: 0; }
    .conversion-pending-body { padding: 24px 28px; }
    .conversion-status-banner {
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
    .conversion-primary-msg {
      font-size: 0.88rem; color: #4b5563; line-height: 1.5; margin: 0 0 16px;
    }
    .conversion-info-callout {
      display: flex; gap: 12px; background: #f0fdf4; border: 1px solid #bbf7d0;
      border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;
    }
    .ccallout-icon {
      width: 32px; height: 32px; border-radius: 8px; background: #dcfce7;
      color: #166534; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ccallout-icon svg { width: 18px; height: 18px; fill: currentColor; }
    .ccallout-content strong {
      display: block; font-size: 0.82rem; color: #166534; margin-bottom: 2px;
    }
    .ccallout-content p {
      font-size: 0.78rem; color: #15803d; margin: 0; line-height: 1.45;
    }
    .conversion-summary-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .conversion-summary-item {
      background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 10px 12px;
    }
    .conversion-summary-item .clbl {
      display: block; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
      color: #9ca3af; margin-bottom: 2px;
    }
    .conversion-summary-item .cval {
      font-size: 0.85rem; font-weight: 700; color: #111827;
    }
    .conversion-modal-footer {
      padding: 16px 28px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: flex-end; gap: 10px;
    }
    .btn-cpending-sec {
      padding: 9px 18px; background: #fff; border: 1.5px solid var(--border);
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #4b5563;
      text-decoration: none; cursor: pointer; transition: all 0.2s;
    }
    .btn-cpending-sec:hover { background: #f3f4f6; color: #111827; }
    .btn-cpending-pri {
      padding: 9px 20px; background: var(--primary); border: none;
      border-radius: 8px; font-weight: 700; font-size: 0.82rem; color: #fff;
      cursor: pointer; transition: all 0.2s;
    }
    .btn-cpending-pri:hover { background: var(--primary-dark); }

    /* ══════════════════════════════════════════════════════════ */
    /* VISUAL ISLAMIC STUDIES PENDING HUB                         */
    /* ══════════════════════════════════════════════════════════ */
    .conversion-pending-hub-card {
      background: #fff; border-radius: 20px; border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(20, 83, 45, 0.08); overflow: hidden; margin-bottom: 28px;
    }
    .conversion-pending-hero-banner {
      background: linear-gradient(135deg, #064e3b 0%, #14532D 60%, #0f3d24 100%);
      padding: 32px 36px 28px; position: relative; overflow: hidden; color: #fff;
    }
    .conversion-pending-hero-banner::after {
      content: ''; position: absolute; right: -40px; bottom: -40px; width: 240px; height: 240px;
      border-radius: 50%; background: radial-gradient(circle, rgba(212, 175, 55, 0.16) 0%, transparent 70%); pointer-events: none;
    }
    .conversion-pending-hero-banner::before {
      content: ''; position: absolute; right: 140px; top: -30px; width: 150px; height: 150px;
      border-radius: 50%; background: rgba(255, 255, 255, 0.05); pointer-events: none;
    }
    .conversion-pending-top-row {
      display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
      position: relative; z-index: 1; flex-wrap: wrap; margin-bottom: 24px;
    }
    .conversion-pending-title-area { display: flex; align-items: center; gap: 18px; }
    .conversion-pending-avatar-icon {
      width: 64px; height: 64px; border-radius: 18px; background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
      border: 2px solid rgba(212, 175, 55, 0.5); flex-shrink: 0; box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }
    .conversion-pending-avatar-icon svg { width: 32px; height: 32px; fill: #fff; }
    .conversion-pending-headings h3 {
      font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; margin: 0 0 4px;
      letter-spacing: -0.01em; color: #fff;
    }
    .conversion-pending-headings p {
      font-size: 0.85rem; color: rgba(255, 255, 255, 0.85); margin: 0;
    }
    .conversion-badge-pending-pulse {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px;
      border-radius: 30px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.06em; background: rgba(245, 158, 11, 0.22); border: 1.5px solid rgba(253, 224, 71, 0.5);
      backdrop-filter: blur(10px); color: #fef08a; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .pulse-dot-amber {
      width: 8px; height: 8px; border-radius: 50%; background: #fde047;
      box-shadow: 0 0 0 0 rgba(253, 224, 71, 0.7); animation: warningPulse 1.8s infinite;
    }
    .conversion-pending-schedule-strip {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
      position: relative; z-index: 1;
    }
    .conversion-pmetric-box {
      background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 14px 16px;
      transition: all 0.2s;
    }
    .conversion-pmetric-box:hover { background: rgba(255, 255, 255, 0.18); transform: translateY(-2px); }
    .conversion-pmetric-lbl {
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
      color: rgba(255, 255, 255, 0.75); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;
    }
    .conversion-pmetric-val {
      font-size: 0.95rem; font-weight: 700; color: #fff; line-height: 1.3;
    }
    .conversion-pmetric-val.gold { color: #fde047; }

    /* Pending 2-Column Hub Grid */
    .conversion-pending-hub-grid {
      display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; padding: 32px 36px;
    }
    .conversion-calendar-teaser-card {
      background: linear-gradient(135deg, #fefce8, #f0fdf4); border: 1.5px dashed #f59e0b;
      border-radius: 16px; padding: 22px 24px; margin-bottom: 24px; position: relative;
    }
    .conversion-calendar-teaser-badge {
      display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
      border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.05em; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
      margin-bottom: 12px;
    }
    .conversion-calendar-teaser-content { display: flex; gap: 16px; align-items: center; }
    .conversion-calendar-teaser-icon {
      width: 52px; height: 52px; border-radius: 14px; background: #fff;
      border: 2px solid #fde68a; display: flex; align-items: center; justify-content: center;
      color: #d97706; flex-shrink: 0; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
    }
    .conversion-calendar-teaser-icon svg { width: 28px; height: 28px; fill: currentColor; }
    .conversion-calendar-teaser-text h5 {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #14532D; margin: 0 0 4px;
    }
    .conversion-calendar-teaser-text p {
      font-size: 0.82rem; color: #4b5563; margin: 0; line-height: 1.5;
    }
    .conversion-pending-journey-wrap {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; margin-bottom: 24px;
    }
    .conversion-pending-journey-steps {
      display: flex; justify-content: space-between; position: relative; margin-top: 14px;
    }
    .conversion-pending-journey-steps::before {
      content: ''; position: absolute; top: 22px; left: 12%; right: 12%; height: 3px;
      background: #e5e7eb; z-index: 1;
    }
    .conversion-pjourney-step {
      display: flex; flex-direction: column; align-items: center; position: relative;
      z-index: 2; text-align: center; flex: 1;
    }
    .conversion-pjourney-circle {
      width: 44px; height: 44px; border-radius: 50%; background: #f9fafb;
      border: 3px solid #e5e7eb; display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 0.9rem; color: #9ca3af; margin-bottom: 8px; transition: all 0.3s;
    }
    .conversion-pjourney-step.done .conversion-pjourney-circle {
      background: var(--primary); border-color: var(--primary); color: #fff;
    }
    .conversion-pjourney-step.active .conversion-pjourney-circle {
      background: #fff; border-color: #f59e0b; color: #d97706;
      box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.2);
    }
    .conversion-pjourney-title { font-size: 0.82rem; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
    .conversion-pjourney-desc { font-size: 0.70rem; color: #6b7280; line-height: 1.3; }

    /* Checklist Card */
    .conversion-checklist-box {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.03); margin-bottom: 24px;
    }
    .conversion-checklist-item {
      display: flex; gap: 12px; align-items: flex-start; margin-bottom: 14px; font-size: 0.85rem;
    }
    .conversion-checklist-item:last-child { margin-bottom: 0; }
    .conversion-bullet-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #166534;
      flex-shrink: 0; margin-top: 6px; box-shadow: 0 0 0 3px #dcfce7;
    }
    .conversion-checklist-item strong { display: block; color: #1f2937; margin-bottom: 2px; }
    .conversion-checklist-item p { margin: 0; color: #6b7280; font-size: 0.78rem; line-height: 1.4; }

    /* Office & Sunnah Cards */
    .conversion-office-desk-card {
      background: linear-gradient(to bottom, #fdfbf7, #fff); border: 1px solid #fed7aa;
      border-radius: 16px; padding: 22px 24px; margin-bottom: 24px;
    }
    .conversion-sunnah-box {
      background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 16px; padding: 20px 22px;
    }
    .conversion-sunnah-box h5 {
      font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: #14532D; margin: 0 0 6px;
      display: flex; align-items: center; gap: 8px;
    }
    .conversion-sunnah-box p {
      font-size: 0.82rem; color: #166534; line-height: 1.5; margin: 0; font-style: italic;
    }

    /* Detail Pairs & Cards */
    .hub-card {
      background: #fff; border: 1px solid var(--border); border-radius: 16px;
      padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.03); transition: all 0.2s;
    }
    .hub-card-title {
      font-family: 'Lora', serif; font-size: 1.05rem; font-weight: 700; color: #111827;
      margin: 0 0 18px; display: flex; align-items: center; justify-content: space-between;
    }
    .hub-card-title span { display: flex; align-items: center; gap: 8px; }
    .detail-table { display: flex; flex-direction: column; gap: 14px; }
    .detail-row {
      display: flex; justify-content: space-between; align-items: center;
      padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;
    }
    .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .detail-key { font-size: 0.8rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; }
    .detail-val { font-size: 0.92rem; font-weight: 700; color: #1f2937; text-align: right; }
    .detail-val.highlight { color: #14532D; font-family: 'Lora', serif; font-size: 1rem; }
    .detail-badge-pill {
      display: inline-block; padding: 4px 12px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 800; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;
    }

    @media (max-width: 960px) {
      .conversion-cal-layout { grid-template-columns: 1fr !important; }
      .conversion-summary-grid { grid-template-columns: 1fr !important; }
      .conversion-pending-hub-grid { grid-template-columns: 1fr !important; padding: 20px !important; }
      .conversion-pending-schedule-strip { grid-template-columns: 1fr 1fr !important; }
      .conversion-pending-hero-banner { padding: 24px 20px !important; }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'conversion_form'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Conversion to Islam Registration</div>
        <div class="top-bar-subtitle">Da'wah Department — Certificate of Conversion to Islam (OCRG Form No. 104)</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Conversion to Islam</span>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- CASE 1: CONVERSION APPROVED / CERTIFICATE ISSUED           -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php if ($hasApproved && $activeRequest): ?>
      <div class="certificate-hub-card">
        <div class="cert-hero-banner">
          <div class="cert-hero-top">
            <div>
              <span style="font-size:0.8rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#fde047;">Official Record • ISCAG Da'wah Department</span>
              <h3 style="font-family:'Lora',serif; font-size:1.8rem; font-weight:700; margin:4px 0 0; color:white;">Certificate of Conversion to Islam</h3>
            </div>
            <div class="cert-badge-status">
              <div class="cert-badge-dot"></div>
              Official Certificate Issued
            </div>
          </div>

          <div class="cert-adopted-banner">
            <div>
              <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:rgba(255,255,255,0.8); margin-bottom:2px;">Adopted Muslim Name</div>
              <div class="cert-adopted-name"><?= htmlspecialchars($activeRequest['adopted_name']) ?></div>
              <div style="font-size:0.85rem; color:rgba(255,255,255,0.85); margin-top:4px;">Legal Name: <strong><?= htmlspecialchars($activeRequest['fname'] . ' ' . $activeRequest['lname']) ?></strong> • Ref: <strong>#CV-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></strong></div>
            </div>
            <div class="cert-tools-row">
              <button type="button" class="btn-action-tool btn-tool-print" onclick="openCertModal()">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                Print Official Certificate
              </button>
              <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-action-tool btn-tool-resources">
                Islamic Learning Portal →
              </a>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; background:rgba(0,0,0,0.15); border-radius:12px; padding:16px 20px;">
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Date of Conversion</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= date('F d, Y', strtotime($activeRequest['conversion_date'])) ?></div>
            </div>
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Witness 1</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= htmlspecialchars($activeRequest['witness1_name'] ?: 'Official Da\'wah Officer') ?></div>
            </div>
            <div>
              <div style="font-size:0.7rem; text-transform:uppercase; color:rgba(255,255,255,0.7); font-weight:700;">Witness 2</div>
              <div style="font-size:0.95rem; font-weight:700; color:white;"><?= htmlspecialchars($activeRequest['witness2_name'] ?: 'Official Imam / Ustaz') ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- APPROVED ISLAMIC STUDIES / CONVERSION SCHEDULE CALENDAR    -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="conversion-calendar-hub-card">
        <div class="hub-card-title">
          <span>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
            Official Islamic Studies &amp; Shahadah Schedule — Male Da'wah
          </span>
          <span class="detail-badge-pill" style="background:#d1fae5; color:#047857; font-weight:800;">
            Confirmed &amp; Scheduled by Administrator
          </span>
        </div>

        <div class="conversion-cal-layout">
          <!-- Left: Calendar View -->
          <div class="conversion-cal-container">
            <div class="conversion-cal-top">
              <div class="conversion-cal-month-nav">
                <button type="button" class="btn-ccal-step" onclick="navApprovedConversionCal(-1)" title="Previous Month">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
                </button>
                <span id="approved-cal-title-conversion" class="conversion-cal-month-name">Loading Month...</span>
                <button type="button" class="btn-ccal-step" onclick="navApprovedConversionCal(1)" title="Next Month">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                </button>
              </div>
              <button type="button" class="btn-jump-ceremony" onclick="jumpToApprovedConversion()">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
                Jump to Ceremony Date
              </button>
            </div>

            <div class="conversion-weekdays-row">
              <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
            </div>
            <div id="approved-cal-grid-conversion" class="conversion-days-grid"></div>
          </div>

          <!-- Right: Session Inspector -->
          <div class="conversion-session-inspector">
            <div>
              <div class="cinspector-badge">
                <span class="pulse-dot"></span>
                <span id="cinspector-status-text">Confirmed Session</span>
              </div>
              <h4 class="cinspector-date" id="cinspector-date-text"><?= date('l, F d, Y', strtotime($activeRequest['conversion_date'])) ?></h4>

              <div class="cinspector-info-list">
                <div class="cinspector-item">
                  <span class="citem-label">Session Time</span>
                  <span class="citem-val" id="cinspector-time-text"><?= !empty($activeRequest['preferred_time']) ? htmlspecialchars($activeRequest['preferred_time']) : '10:00 AM' ?></span>
                </div>
                <div class="cinspector-item">
                  <span class="citem-label">Adopted Muslim Name</span>
                  <span class="citem-val highlight"><?= htmlspecialchars($activeRequest['adopted_name']) ?></span>
                </div>
                <div class="cinspector-item">
                  <span class="citem-label">Legal Name</span>
                  <span class="citem-val"><?= htmlspecialchars($activeRequest['fname'] . ' ' . $activeRequest['lname']) ?></span>
                </div>
                <div class="cinspector-item">
                  <span class="citem-label">Venue</span>
                  <span class="citem-val" id="cinspector-venue-text"><?= !empty($activeRequest['venue']) ? htmlspecialchars($activeRequest['venue']) : 'ISCAG Main Mosque' ?></span>
                </div>
                <div class="cinspector-item">
                  <span class="citem-label">Da'wah Officer / Ustaz</span>
                  <span class="citem-val"><?= htmlspecialchars($activeRequest['witness2_name'] ?: 'Assigned Da\'wah Officer') ?></span>
                </div>
                <div class="cinspector-item">
                  <span class="citem-label">Reference ID</span>
                  <span class="citem-val" style="font-family:monospace;">#CV-<?= str_pad((string)$activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>
              </div>
            </div>

            <div class="cinspector-actions">
              <button type="button" class="btn-action-tool btn-tool-print" onclick="openCertModal()" style="padding:8px 16px; font-size:0.8rem;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                Print Certificate
              </button>
              <?php 
                $ccalDate = date('Ymd', strtotime($activeRequest['conversion_date']));
                $ccalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode("ISCAG Islamic Studies & Shahadah — " . $activeRequest['adopted_name']) . "&dates=" . $ccalDate . "T020000Z/" . $ccalDate . "T040000Z&details=" . urlencode("ISCAG Male Da'wah Islamic Studies & Shahadah Session. Ref: #CV-" . str_pad((string)$activeRequest['id'], 4, '0', STR_PAD_LEFT)) . "&location=" . urlencode('ISCAG Main Mosque');
              ?>
              <a href="<?= $ccalUrl ?>" target="_blank" class="btn-action-tool btn-tool-resources" style="padding:8px 16px; font-size:0.8rem; background:#14532D; border:1px solid #166534; color:#fff;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
                Google Calendar
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- CASE 2: VISUAL ISLAMIC STUDIES PENDING HUB                 -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php elseif ($hasPending && $activeRequest): ?>
      <div class="conversion-pending-hub-card">
        <!-- Hero Banner with 4-Metric Strip -->
        <div class="conversion-pending-hero-banner">
          <div class="conversion-pending-top-row">
            <div class="conversion-pending-title-area">
              <div class="conversion-pending-avatar-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              </div>
              <div class="conversion-pending-headings">
                <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; color:#fde047; margin-bottom:2px;">
                  Male Da'wah Department • Official Submission
                </div>
                <h3>Islamic Studies Registration Under Review</h3>
                <p>Your Shahadah &amp; Islamic Studies submission has been received and logged in the Da'wah MIS.</p>
              </div>
            </div>
            <div class="conversion-badge-pending-pulse">
              <span class="pulse-dot-amber"></span>
              <span>Awaiting Administrator Review &amp; Verification</span>
            </div>
          </div>

          <!-- Frosted Glass 4-Metric Grid -->
          <div class="conversion-pending-schedule-strip">
            <div class="conversion-pmetric-box">
              <div class="conversion-pmetric-lbl">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Tracking Reference
              </div>
              <div class="conversion-pmetric-val gold" style="font-family:monospace;">#CV-<?= str_pad((string)$activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></div>
            </div>
            <div class="conversion-pmetric-box">
              <div class="conversion-pmetric-lbl">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Adopted Muslim Name
              </div>
              <div class="conversion-pmetric-val"><?= htmlspecialchars($activeRequest['adopted_name']) ?></div>
            </div>
            <div class="conversion-pmetric-box">
              <div class="conversion-pmetric-lbl">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                Preferred Ceremony Date
              </div>
              <div class="conversion-pmetric-val"><?= date('F d, Y', strtotime($activeRequest['conversion_date'])) ?></div>
            </div>
            <div class="conversion-pmetric-box">
              <div class="conversion-pmetric-lbl">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Designated Venue
              </div>
              <div class="conversion-pmetric-val">ISCAG Main Mosque</div>
            </div>
          </div>
        </div>

        <!-- 2-Column Hub Layout -->
        <div class="conversion-pending-hub-grid">
          <!-- Left Column: Status, Teaser, Journey & Blessing -->
          <div>
            <!-- Official Schedule Teaser -->
            <div class="conversion-calendar-teaser-card">
              <div class="conversion-calendar-teaser-badge">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Official Schedule Unlocks Upon Approval
              </div>
              <div class="conversion-calendar-teaser-content">
                <div class="conversion-calendar-teaser-icon">
                  <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
                </div>
                <div class="conversion-calendar-teaser-text">
                  <h5>Schedule Calendar &amp; Certificate in Male Da'wah</h5>
                  <p>When the Da'wah administrator approves your registration, your confirmed schedule date, time slot, and assigned Da'wah officer will appear directly on your calendar here, and your official Certificate of Conversion will become printable.</p>
                </div>
              </div>
            </div>

            <!-- 3-Phase Journey Tracker -->
            <div class="conversion-pending-journey-wrap">
              <div class="hub-card-title" style="margin-bottom:12px;">
                <span>
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                  Registration &amp; Verification Journey
                </span>
                <span style="font-size:0.75rem; color:#6b7280; font-weight:700;">Step 2 of 3 Active</span>
              </div>
              <div class="conversion-pending-journey-steps">
                <div class="conversion-pjourney-step done">
                  <div class="conversion-pjourney-circle">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </div>
                  <div class="conversion-pjourney-title">Submitted</div>
                  <div class="conversion-pjourney-desc">Registration filed in system</div>
                </div>
                <div class="conversion-pjourney-step active">
                  <div class="conversion-pjourney-circle">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  </div>
                  <div class="conversion-pjourney-title">Admin Review</div>
                  <div class="conversion-pjourney-desc">Da'wah department review</div>
                </div>
                <div class="conversion-pjourney-step">
                  <div class="conversion-pjourney-circle">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                  </div>
                  <div class="conversion-pjourney-title">Scheduled</div>
                  <div class="conversion-pjourney-desc">Schedule appears in Male Da'wah</div>
                </div>
              </div>
            </div>

            <!-- Sunnah Blessing Box -->
            <div class="conversion-sunnah-box">
              <h5>
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                Da'wah Blessing &amp; Encouragement
              </h5>
              <p>
                "Whoever embraces Islam sincerely, Allah wipes away every evil deed that he had previously done." Welcome to the brotherhood of faith. May Allah bless your journey in knowledge and steadfastness.
              </p>
            </div>
          </div>

          <!-- Right Column: Registration Summary & Required Documents (Bullets only) -->
          <div>
            <!-- Detail Summary Card -->
            <div class="hub-card" style="margin-bottom:24px;">
              <div class="hub-card-title">
                <span>
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                  Registration Details
                </span>
                <span class="detail-badge-pill" style="background:#fef3c7; color:#92400e; border-color:#fde68a;">
                  Pending Verification
                </span>
              </div>
              <div class="detail-table">
                <div class="detail-row">
                  <span class="detail-key">Legal Full Name</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['fname'] . ' ' . $activeRequest['lname']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Adopted Muslim Name</span>
                  <span class="detail-val highlight"><?= htmlspecialchars($activeRequest['adopted_name']) ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Civil Status</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['civil_status'] ?? '—') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Date of Birth</span>
                  <span class="detail-val"><?= !empty($activeRequest['dob']) ? date('M d, Y', strtotime($activeRequest['dob'])) : '—' ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Former Religion</span>
                  <span class="detail-val"><?= htmlspecialchars($activeRequest['former_religion'] ?? '—') ?></span>
                </div>
                <div class="detail-row">
                  <span class="detail-key">Date Submitted</span>
                  <span class="detail-val"><?= date('M d, Y • h:i A', strtotime($activeRequest['created_at'])) ?></span>
                </div>
              </div>
            </div>

            <!-- Documents Required (Bullet points - NO CHECKMARKS or EMOJIS) -->
            <div class="conversion-checklist-box">
              <div class="hub-card-title" style="margin-bottom:14px;">
                <span>
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                  Documents Required on Ceremony
                </span>
                <span style="font-size:0.75rem; font-weight:700; color:#6b7280;">Bring on Day</span>
              </div>
              
              <div class="conversion-checklist-item">
                <div class="conversion-bullet-dot"></div>
                <div>
                  <strong>Valid Government-Issued ID</strong>
                  <p>Present one original government ID (Philippine Passport, Driver's License, or National ID) along with 2 photocopies.</p>
                </div>
              </div>

              <div class="conversion-checklist-item">
                <div class="conversion-bullet-dot"></div>
                <div>
                  <strong>Modest Male Islamic Attire</strong>
                  <p>Clean, modest attire (long pants with sleeved shirt or traditional thobe) suitable for mosque presence and prayer.</p>
                </div>
              </div>

              <div class="conversion-checklist-item">
                <div class="conversion-bullet-dot"></div>
                <div>
                  <strong>Two (2) Muslim Male Witnesses</strong>
                  <p>If you do not have personal witnesses, ISCAG Da'wah officers will be honored to act as official witnesses for you.</p>
                </div>
              </div>

              <div class="conversion-checklist-item">
                <div class="conversion-bullet-dot"></div>
                <div>
                  <strong>Certificate Fee Processing</strong>
                  <p>The Certificate of Conversion to Islam (OCRG Form No. 104) is issued upon completion of testimony verification.</p>
                </div>
              </div>
            </div>

            <!-- Male Da'wah Office Assistance Desk -->
            <div class="conversion-office-desk-card">
              <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:#b45309; margin-bottom:4px;">
                Need Assistance or Schedule Adjustments?
              </div>
              <div style="font-weight:700; color:#14532D; font-size:0.95rem; margin-bottom:6px;">
                ISCAG Male Da'wah Administration Office
              </div>
              <div style="font-size:0.82rem; color:#4b5563; line-height:1.5; margin-bottom:12px;">
                Open Sunday to Thursday from 8:30 AM to 5:00 PM. Feel free to contact our Da'wah desk for inquiries regarding classes or ceremony arrangements.
              </div>
              <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="<?= url('/user/dashboard') ?>" class="btn-action-tool" style="background:#14532D; color:#fff; padding:8px 16px; font-size:0.8rem; border-radius:8px; text-decoration:none;">
                  Back to Dashboard
                </a>
                <a href="<?= url('/user/services/counseling/resources') ?>" class="btn-action-tool" style="background:#fff; border:1px solid #fed7aa; color:#92400e; padding:8px 16px; font-size:0.8rem; border-radius:8px; text-decoration:none;">
                  Islamic Studies Portal
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- CASE 3: NO ACTIVE REQUEST -> REGISTRATION FORM             -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <?php else: ?>

      <form id="conversion-form" method="POST" onsubmit="return false;">
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:currentColor;margin-right:8px;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Personal &amp; Conversion Information
            </h6>
          </div>
          <div class="section-card-body">
            <div class="form-grid cols-3">
              <div>
                <label class="form-label">First Name <span class="required">*</span></label>
                <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($dbUser['first_name'] ?? '') ?>" required />
              </div>
              <div>
                <label class="form-label">Middle Name</label>
                <input type="text" name="mname" class="form-control" value="<?= htmlspecialchars($dbUser['middle_name'] ?? '') ?>" />
              </div>
              <div>
                <label class="form-label">Last Name <span class="required">*</span></label>
                <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($dbUser['last_name'] ?? '') ?>" required />
              </div>
            </div>

            <div class="form-grid cols-3">
              <div>
                <label class="form-label">Sex <span class="required">*</span></label>
                <input type="text" name="sex" class="form-control" value="Male" readonly style="background: #f8faf9; cursor: not-allowed;" />
              </div>
              <div>
                <label class="form-label">Civil Status <span class="required">*</span></label>
                <select name="civil_status" class="form-select" required>
                  <option value="">Select Status</option>
                  <option <?= ($dbUser['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                  <option <?= ($dbUser['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                  <option <?= ($dbUser['civil_status'] ?? '') === 'Widow/Widower' ? 'selected' : '' ?>>Widow/Widower</option>
                  <option <?= ($dbUser['civil_status'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                </select>
              </div>
              <div>
                <label class="form-label">Citizenship <span class="required">*</span></label>
                <input type="text" name="citizenship" class="form-control" value="Filipino" required />
              </div>
            </div>

            <div class="form-grid cols-3">
              <div>
                <label class="form-label">Date of Birth <span class="required">*</span></label>
                <input type="date" name="dob" id="dob" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($dbUser['birthdate'] ?? $dbUser['dob'] ?? '') ?>" required />
              </div>
              <div>
                <label class="form-label">Age <span class="required">*</span></label>
                <input type="number" name="age" id="age" class="form-control" min="0" placeholder="Auto-calculated" readonly style="background: #f8faf9; cursor: default;" required />
              </div>
              <div>
                <label class="form-label">Occupation <span class="required">*</span></label>
                <select name="occupation" id="occupation" class="form-select" required>
                  <option value="">— Select Occupation Type —</option>
                  <option>Student</option>
                  <option>Private Employee</option>
                  <option>Government Employee</option>
                  <option>Self-employed / Business Owner</option>
                  <option>Professional</option>
                  <option>Skilled / Technical Worker</option>
                  <option>Service / Sales Worker</option>
                  <option>Laborer / Agricultural Worker</option>
                  <option>Retired</option>
                  <option>Unemployed / Homemaker</option>
                  <option value="Others">Others</option>
                </select>
                <div id="occupation_other_wrap" class="other-field-wrap">
                  <input type="text" name="occupation_other" id="occupation_other" class="form-control" placeholder="Please specify..." />
                </div>
              </div>
            </div>

            <div class="section-divider" data-label="Conversion Details"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Adopted Muslim Name <span class="required">*</span></label>
                <input type="text" name="adopted_name" class="form-control" placeholder="Enter your chosen Muslim name" required />
              </div>
              <div>
                <label class="form-label">Date of Conversion <span class="required">*</span></label>
                <input type="date" name="conversion_date" class="form-control" value="<?= date('Y-m-d') ?>" required />
              </div>
            </div>

            <div class="section-divider" data-label="Former Religion"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Former Religion / Sect <span class="required">*</span></label>
                <select name="former_religion" id="former_religion" class="form-select" required>
                  <option value="">— Select —</option>
                  <option>Roman Catholic</option>
                  <option>Protestant</option>
                  <option>Iglesia ni Cristo</option>
                  <option>Seventh-day Adventist</option>
                  <option value="Others">Others</option>
                </select>
                <div id="former_religion_other_wrap" class="other-field-wrap">
                  <input type="text" name="former_religion_other" id="former_religion_other" class="form-control" placeholder="Please specify..." />
                </div>
              </div>
            </div>

            <div class="section-divider" data-label="Place of Birth & Residence"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Place of Birth <span class="required">*</span></label>
                <input type="text" name="pob" class="form-control" placeholder="Hospital/Clinic/Institution/Street, City, Province" required />
              </div>
              <div>
                <label class="form-label">Present Residence <span class="required">*</span></label>
                <input type="text" name="residence" class="form-control" placeholder="House No., St., Barangay, City, Province" value="<?= htmlspecialchars($dbUser['address'] ?? '') ?>" required />
              </div>
            </div>

            <div class="section-divider" data-label="Family Information"></div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Name of Father <span class="required">*</span></label>
                <input type="text" name="father_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Religion of Father <span class="required">*</span></label>
                <input type="text" name="father_religion" class="form-control" required />
              </div>
            </div>
            <div class="form-grid cols-2">
              <div>
                <label class="form-label">Maiden Name of Mother <span class="required">*</span></label>
                <input type="text" name="mother_name" class="form-control" required />
              </div>
              <div>
                <label class="form-label">Religion of Mother <span class="required">*</span></label>
                <input type="text" name="mother_religion" class="form-control" required />
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-submit-row" style="margin-top: 30px;">
          <button type="button" class="btn-submit" id="final-submit-btn" onclick="submitConversionForm()">Submit Conversion Registration</button>
        </div>
      </form>
      <?php endif; ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📜 MY CONVERSION APPLICATIONS HISTORY                     -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="section-card" style="margin-top:32px;">
        <div class="section-card-header">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Conversion Records & History
          </h6>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Adopted Muslim Name</th>
                  <th>Date of Conversion</th>
                  <th>Date Filed</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($history)): ?>
                  <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">No conversion registrations filed yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($history as $h): ?>
                    <tr>
                      <td class="td-id">#CV-<?= str_pad($h['id'], 4, '0', STR_PAD_LEFT) ?></td>
                      <td style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($h['adopted_name']) ?></td>
                      <td><?= date('M d, Y', strtotime($h['conversion_date'])) ?></td>
                      <td><?= date('M d, Y', strtotime($h['created_at'])) ?></td>
                      <td>
                        <?php if (strtolower($h['status']) === 'approved'): ?>
                          <span class="badge-status badge-approved" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;font-weight:700;">Certificate Issued</span>
                        <?php elseif (strtolower($h['status']) === 'pending'): ?>
                          <span class="badge-status badge-pending" style="background:#fef3c7;color:#b45309;border:1px solid #fde68a;font-weight:700;">Under Review</span>
                        <?php else: ?>
                          <span class="badge-status badge-rejected" style="font-weight:700;"><?= htmlspecialchars(ucfirst($h['status'])) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if (strtolower($h['status']) === 'approved'): ?>
                          <button type="button" class="btn-view-doc" onclick="openCertModal()">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                            View Certificate
                          </button>
                        <?php else: ?>
                          <span style="font-size:0.8rem; color:var(--text-muted);">Awaiting Approval</span>
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
<!-- PRINTABLE CERTIFICATE MODAL                                -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if ($activeRequest): ?>
<div class="cert-modal-backdrop" id="cert-modal" onclick="if(event.target===this) closeCertModal()">
  <div class="cert-modal-card">
    <div style="padding:20px 24px; background:#f8faf9; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;" class="no-print">
      <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark);">Official Certificate of Conversion</h5>
      <button type="button" onclick="closeCertModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280;">&times;</button>
    </div>

    <div id="printable-cert-area" style="padding:40px; background:#fff; text-align:center; position:relative; border:8px double #166534; margin:16px;">
      <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:8px;">
        <img src="<?= asset('assets/logo.jpg') ?>" style="width:50px; height:50px; border-radius:8px;" alt="ISCAG" />
        <div style="text-align:left;">
          <div style="font-family:'Lora',serif; font-size:1.15rem; font-weight:800; color:#14532D;">ISLAMIC STUDIES, CALL AND GUIDANCE OF THE PHILIPPINES</div>
          <div style="font-size:0.75rem; color:#4b5563; font-weight:600;">Office of the Da'wah & Educational Department</div>
        </div>
      </div>

      <div style="margin:24px 0 16px;">
        <h2 style="font-family:'Lora',serif; font-size:1.6rem; font-weight:700; color:#14532D; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 6px;">Certificate of Conversion to Islam</h2>
        <div style="font-size:0.8rem; font-weight:700; color:#B8860B; text-transform:uppercase; letter-spacing:0.12em;">Official Registration Ref: #CV-<?= str_pad($activeRequest['id'], 4, '0', STR_PAD_LEFT) ?></div>
      </div>

      <p style="font-size:0.92rem; color:#374151; line-height:1.8; margin-bottom:20px;">
        This is to certify that brother <strong><?= htmlspecialchars($activeRequest['fname'] . ' ' . ($activeRequest['mname'] ? $activeRequest['mname'] . ' ' : '') . $activeRequest['lname']) ?></strong>, of legal age, Filipino citizen, residing at <strong><?= htmlspecialchars($activeRequest['residence']) ?></strong>, has willfully and sincerely embraced the religion of Islam on <strong><?= date('F d, Y', strtotime($activeRequest['conversion_date'])) ?></strong> and has chosen as his Muslim name:
      </p>

      <div style="background:#f0fdf4; border:2px solid #bbf7d0; border-radius:12px; padding:16px; margin:20px 0; font-family:'Lora',serif; font-size:1.6rem; font-weight:800; color:#14532D;">
        <?= htmlspecialchars($activeRequest['adopted_name']) ?>
      </div>

      <p style="font-size:0.88rem; color:#4b5563; line-height:1.6; margin-bottom:32px;">
        He has pronounced the Shahadah (Testimony of Faith) in the presence of competent witnesses and is hereby officially welcomed into the universal brotherhood of Islam.
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-top:40px; text-align:center;">
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">Ustaz / Da'wah Director</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Da'wah Department</div>
        </div>
        <div>
          <div style="border-bottom:1px solid #000; height:32px; margin-bottom:6px;"></div>
          <div style="font-size:0.8rem; font-weight:700; color:#111;">President / Authorized Official</div>
          <div style="font-size:0.72rem; color:#6b7280;">ISCAG Philippines</div>
        </div>
      </div>
    </div>

    <div style="padding:16px 24px; background:#f8faf9; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;" class="no-print">
      <button type="button" onclick="closeCertModal()" class="btn-cancel">Close</button>
      <button type="button" onclick="window.print()" class="btn-submit" style="background:#166534;">Print Document</button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- CONVERSION PENDING SUBMISSION MODAL                        -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="conversion-pending-modal">
  <div class="conversion-pending-card">
    <div class="conversion-pending-header">
      <div class="conversion-icon-wrap">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
      </div>
      <div>
        <div class="conversion-tag">Islamic Studies • Male Da'wah</div>
        <h4 class="conversion-modal-title">Registration Submitted</h4>
        <p class="conversion-modal-sub">Awaiting Administrator Review &amp; Schedule Confirmation</p>
      </div>
    </div>

    <div class="conversion-pending-body">
      <div class="conversion-status-banner">
        <span class="status-pulse-dot"></span>
        <span>Status: <strong>Pending Administrator Approval</strong></span>
      </div>

      <p class="conversion-primary-msg">
        Your Islamic Studies and Shahadah registration has been securely transmitted. It is now awaiting review by the Da'wah Department Administration.
      </p>

      <div class="conversion-info-callout">
        <div class="ccallout-icon">
          <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm-7-7h5v5h-5z"/></svg>
        </div>
        <div class="ccallout-content">
          <strong>Official Schedule Notice</strong>
          <p>Once the administrator approves your registration, your confirmed schedule will automatically appear in Male Da'wah and unlock your printable certificate.</p>
        </div>
      </div>

      <div class="conversion-summary-grid">
        <div class="conversion-summary-item">
          <span class="clbl">Reference Number</span>
          <span class="cval" id="cpm-ref" style="font-family:monospace; color:#14532D;">#CV-PENDING</span>
        </div>
        <div class="conversion-summary-item">
          <span class="clbl">Adopted Muslim Name</span>
          <span class="cval" id="cpm-adopted">—</span>
        </div>
        <div class="conversion-summary-item">
          <span class="clbl">Legal Full Name</span>
          <span class="cval" id="cpm-legal">—</span>
        </div>
        <div class="conversion-summary-item">
          <span class="clbl">Preferred Date</span>
          <span class="cval" id="cpm-date">—</span>
        </div>
      </div>
    </div>

    <div class="conversion-modal-footer">
      <button type="button" class="btn-cpending-sec" onclick="closeConversionPendingModal(true)">View Status in Da'wah</button>
      <a href="<?= url('/user/dashboard') ?>" class="btn-cpending-pri" style="text-decoration:none; display:inline-block; line-height:1.4;">Return to Dashboard</a>
    </div>
  </div>
</div>

<script>
  // ── Date of Birth -> Age Calculation ──
  function calculateAge(dobVal) {
    if (!dobVal) return null;
    const parts = dobVal.split('-');
    if (parts.length !== 3) return null;
    const birthYear = parseInt(parts[0], 10);
    const birthMonth = parseInt(parts[1], 10) - 1;
    const birthDay = parseInt(parts[2], 10);
    if (isNaN(birthYear) || isNaN(birthMonth) || isNaN(birthDay)) return null;

    const today = new Date();
    const todayYear = today.getFullYear();
    const todayMonth = today.getMonth();
    const todayDay = today.getDate();

    const birthDate = new Date(birthYear, birthMonth, birthDay);
    const currentDate = new Date(todayYear, todayMonth, todayDay);
    if (birthDate > currentDate) {
      return -1; // Future date
    }

    let age = todayYear - birthYear;
    if (todayMonth < birthMonth || (todayMonth === birthMonth && todayDay < birthDay)) {
      age--;
    }
    return age;
  }

  function syncDobToAge(showToastError = true) {
    const dobInput = document.getElementById('dob') || document.querySelector('input[name="dob"]');
    const ageInput = document.getElementById('age') || document.querySelector('input[name="age"]');
    if (!dobInput || !ageInput) return;

    const dobVal = dobInput.value;
    if (!dobVal) {
      ageInput.value = '';
      return;
    }

    const age = calculateAge(dobVal);
    if (age === null || age < 0) {
      ageInput.value = '';
      dobInput.classList.add('is-invalid');
      if (showToastError) {
        showToast('Invalid date of birth. Future dates are not allowed.', '#e67e22');
      }
    } else {
      dobInput.classList.remove('is-invalid');
      ageInput.value = age;
      ageInput.classList.remove('is-invalid');
    }
  }

  const dobEl = document.getElementById('dob') || document.querySelector('input[name="dob"]');
  const ageEl = document.getElementById('age') || document.querySelector('input[name="age"]');
  if (dobEl) {
    dobEl.addEventListener('input', () => syncDobToAge(false));
    dobEl.addEventListener('change', () => syncDobToAge(true));
  }
  if (ageEl && dobEl) {
    ageEl.addEventListener('click', () => {
      if (!dobEl.value) {
        dobEl.focus();
        showToast('Please select your Date of Birth first to calculate your age.', '#2563eb');
      }
    });
  }

  // Initial calculation on page load
  if (dobEl && dobEl.value) {
    syncDobToAge(false);
  }

  // ── Toggle Other Religion ──
  const relSelect = document.getElementById('former_religion');
  if(relSelect) {
    const relWrap = document.getElementById('former_religion_other_wrap');
    const relInput = document.getElementById('former_religion_other');
    relSelect.onchange = () => {
      if (relSelect.value === 'Others') {
        relWrap.classList.add('show');
        relInput.required = true;
        setTimeout(() => relInput.focus(), 300);
      } else {
        relWrap.classList.remove('show');
        relInput.required = false;
        setTimeout(() => { if(!relWrap.classList.contains('show')) relInput.value = ''; }, 400);
      }
    };
  }

  // ── Toggle Other Occupation ──
  const occSelect = document.getElementById('occupation');
  if(occSelect) {
    const occWrap = document.getElementById('occupation_other_wrap');
    const occInput = document.getElementById('occupation_other');
    occSelect.onchange = () => {
      if (occSelect.value === 'Others') {
        occWrap.classList.add('show');
        occInput.required = true;
        setTimeout(() => occInput.focus(), 300);
      } else {
        occWrap.classList.remove('show');
        occInput.required = false;
        setTimeout(() => { if(!occWrap.classList.contains('show')) occInput.value = ''; }, 400);
      }
    };
  }

  // ── Submit Conversion Form ──
  function submitConversionForm() {
    syncDobToAge(false);

    const form = document.getElementById('conversion-form');
    if (!form) return;

    // Validate all required inputs
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    let hasError = false;
    requiredInputs.forEach(i => {
      if (!i.value || !i.value.trim()) {
        i.classList.add('is-invalid');
        hasError = true;
      } else {
        i.classList.remove('is-invalid');
      }
    });

    if (hasError) {
      showToast('Please fill in all required fields.', '#e67e22');
      return;
    }

    const formData = new FormData(form);
    const payload = {};
    formData.forEach((val, key) => payload[key] = val);

    if (payload.dob) {
      const calcedAge = calculateAge(payload.dob);
      if (calcedAge === null || calcedAge < 0) {
        showToast('Invalid date of birth. Future dates are not allowed.', '#e67e22');
        return;
      }
      payload.age = calcedAge;
    }

    if (!payload.fname || !payload.lname || !payload.adopted_name) {
      showToast('Please complete required fields: Name and Adopted Muslim Name.', '#e67e22');
      return;
    }

    const submitBtn = document.getElementById('final-submit-btn');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting Registration...';
    }

    fetch('<?= url('/user/services/conversion/submit') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        showToast('Conversion registration submitted successfully!', '#166534');
        const formattedDate = payload.conversion_date ? new Date(payload.conversion_date + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'To be confirmed';
        openConversionPendingModal({
          ref: data.ref || ('#CV-' + String(data.id || 'AUTO').padStart(4, '0')),
          adopted_name: payload.adopted_name,
          legal_name: (payload.fname + ' ' + (payload.mname ? payload.mname + ' ' : '') + payload.lname).trim(),
          date: formattedDate
        });
      } else {
        if(submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit Conversion Registration';
        }
        showToast(data.message || 'Error submitting registration.', '#8b2e2e');
      }
    })
    .catch(err => {
      if(submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Conversion Registration';
      }
      showToast('Connection error. Please try again.', '#8b2e2e');
  }

  // ── Conversion Pending Modal Functions ──
  function openConversionPendingModal(data) {
    const modal = document.getElementById('conversion-pending-modal');
    if (!modal) return;
    if (data.ref) {
      const refEl = document.getElementById('cpm-ref');
      if (refEl) refEl.innerText = data.ref;
    }
    if (data.adopted_name) {
      const aEl = document.getElementById('cpm-adopted');
      if (aEl) aEl.innerText = data.adopted_name;
    }
    if (data.legal_name) {
      const lEl = document.getElementById('cpm-legal');
      if (lEl) lEl.innerText = data.legal_name;
    }
    if (data.date) {
      const dEl = document.getElementById('cpm-date');
      if (dEl) dEl.innerText = data.date;
    }
    modal.classList.add('active');
  }

  function closeConversionPendingModal(shouldReload = true) {
    const modal = document.getElementById('conversion-pending-modal');
    if (modal) modal.classList.remove('active');
    if (shouldReload) {
      window.location.reload();
    }
  }

  // ── Approved Conversion Schedule Calendar Logic ──
  const APPROVED_CONVERSION_DATE = <?= ($hasApproved && !empty($activeRequest['conversion_date'])) ? json_encode(date('Y-m-d', strtotime($activeRequest['conversion_date']))) : 'null' ?>;
  const APPROVED_CONVERSION_TIME = <?= ($hasApproved && !empty($activeRequest['preferred_time'])) ? json_encode($activeRequest['preferred_time']) : json_encode('10:00 AM') ?>;
  const APPROVED_CONVERSION_VENUE = <?= ($hasApproved && !empty($activeRequest['venue'])) ? json_encode($activeRequest['venue']) : json_encode('ISCAG Main Mosque') ?>;
  const APPROVED_CONVERSION_ADOPTED = <?= ($hasApproved && $activeRequest) ? json_encode($activeRequest['adopted_name'] ?? '') : '""' ?>;

  let cApprovedCalYear = APPROVED_CONVERSION_DATE ? new Date(APPROVED_CONVERSION_DATE + 'T00:00:00').getFullYear() : new Date().getFullYear();
  let cApprovedCalMonth = APPROVED_CONVERSION_DATE ? new Date(APPROVED_CONVERSION_DATE + 'T00:00:00').getMonth() : new Date().getMonth();
  let selectedApprovedConversionDay = APPROVED_CONVERSION_DATE;

  function renderApprovedConversionCal() {
    const titleEl = document.getElementById('approved-cal-title-conversion');
    const gridEl = document.getElementById('approved-cal-grid-conversion');
    if (!titleEl || !gridEl) return;

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    titleEl.innerText = `${monthNames[cApprovedCalMonth]} ${cApprovedCalYear}`;

    const firstDayIndex = new Date(cApprovedCalYear, cApprovedCalMonth, 1).getDay();
    const daysInMonth = new Date(cApprovedCalYear, cApprovedCalMonth + 1, 0).getDate();
    const todayStr = new Date().toISOString().split('T')[0];

    let html = '';
    for (let i = 0; i < firstDayIndex; i++) {
      html += `<div class="conversion-cal-day empty"></div>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${cApprovedCalYear}-${String(cApprovedCalMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
      const isCeremonyDay = (APPROVED_CONVERSION_DATE === dateStr);
      const isToday = (todayStr === dateStr);
      const isSelected = (selectedApprovedConversionDay === dateStr);

      let classes = ['conversion-cal-day'];
      if (isCeremonyDay) classes.push('has-ceremony');
      if (isToday) classes.push('today');
      if (isSelected && !isCeremonyDay) classes.push('selected-view');

      html += `
        <div class="${classes.join(' ')}" onclick="selectApprovedConversionDay('${dateStr}')" title="${isCeremonyDay ? 'Confirmed Islamic Studies / Shahadah Session' : ''}">
          <span>${d}</span>
        </div>
      `;
    }

    gridEl.innerHTML = html;
  }

  function navApprovedConversionCal(dir) {
    cApprovedCalMonth += dir;
    if (cApprovedCalMonth < 0) {
      cApprovedCalMonth = 11;
      cApprovedCalYear--;
    } else if (cApprovedCalMonth > 11) {
      cApprovedCalMonth = 0;
      cApprovedCalYear++;
    }
    renderApprovedConversionCal();
  }

  function jumpToApprovedConversion() {
    if (!APPROVED_CONVERSION_DATE) return;
    const d = new Date(APPROVED_CONVERSION_DATE + 'T00:00:00');
    cApprovedCalYear = d.getFullYear();
    cApprovedCalMonth = d.getMonth();
    selectedApprovedConversionDay = APPROVED_CONVERSION_DATE;
    renderApprovedConversionCal();
    updateConversionInspector(APPROVED_CONVERSION_DATE);
  }

  function selectApprovedConversionDay(dateStr) {
    selectedApprovedConversionDay = dateStr;
    renderApprovedConversionCal();
    updateConversionInspector(dateStr);
  }

  function updateConversionInspector(dateStr) {
    const isCeremony = (dateStr === APPROVED_CONVERSION_DATE);
    const dateObj = new Date(dateStr + 'T00:00:00');
    const formatted = dateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

    const dateEl = document.getElementById('cinspector-date-text');
    const statusEl = document.getElementById('cinspector-status-text');
    const timeEl = document.getElementById('cinspector-time-text');
    const venueEl = document.getElementById('cinspector-venue-text');

    if (dateEl) dateEl.innerText = formatted;
    if (statusEl) {
      statusEl.innerText = isCeremony ? 'Confirmed Session' : 'No Scheduled Session';
    }
    if (timeEl) {
      timeEl.innerText = isCeremony ? (APPROVED_CONVERSION_TIME || '10:00 AM') : '—';
    }
    if (venueEl) {
      venueEl.innerText = isCeremony ? APPROVED_CONVERSION_VENUE : '—';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('approved-cal-grid-conversion')) {
      renderApprovedConversionCal();
    }
  });
  if (document.getElementById('approved-cal-grid-conversion')) {
    renderApprovedConversionCal();
  }

  // ── Certificate Modal ──
  function openCertModal() {
    const m = document.getElementById('cert-modal');
    if(m) m.style.display = 'flex';
  }
  function closeCertModal() {
    const m = document.getElementById('cert-modal');
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
