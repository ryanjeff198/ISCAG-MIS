<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$dbUser = $dbUser ?? [];
$history = $history ?? [];
$analytics = $analytics ?? ['total' => 0, 'pending' => 0, 'approved' => 0];

$hasApproved = $hasApproved ?? false;
$hasPending = $hasPending ?? false;
$activeRequest = $activeRequest ?? null;

if (!$activeRequest && !empty($history)) {
    foreach ($history as $req) {
        if (($req['status'] ?? '') === 'active' || ($req['status'] ?? '') === 'completed') {
            $hasApproved = true;
            $activeRequest = $req;
            break;
        }
        if (($req['status'] ?? '') === 'pending' && !$activeRequest) {
            $hasPending = true;
            $activeRequest = $req;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Female Section Registration</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-female: #176b45;
      --primary-female-dark: #0f5c3a;
      --primary-female-light: #f0fdf4;
      --primary-female-hover: #0b462c;
      --forest-dark: #176b45;
      --forest-light: #f0fdf4;
      --border-subtle: #e2e8f0;
      --text-dark: #0f172a;
      --text-muted: #64748b;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ── Analytics Bar ── */
    .user-analytics { 
      display: grid; 
      grid-template-columns: repeat(3, 1fr); 
      gap: 20px; 
      margin-bottom: 24px; 
    }
    .stat-card { 
      background: #fff; 
      padding: 20px 22px; 
      border-radius: 14px; 
      border: 1px solid var(--border-subtle);
      display: flex; 
      flex-direction: column; 
      gap: 6px; 
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      transition: all 0.2s ease;
    }
    .stat-card:hover { 
      transform: translateY(-2px); 
      border-color: #cbd5e1;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .stat-label { 
      font-size: 0.72rem; 
      font-weight: 700; 
      color: var(--text-muted); 
      text-transform: uppercase; 
      letter-spacing: 0.05em;
    }
    .stat-value { 
      font-size: 1.85rem; 
      font-weight: 800; 
      color: var(--text-dark); 
      line-height: 1.1;
    }

    /* ── Main Registration Form Card ── */
    .reg-form-card {
      background: #ffffff;
      border-radius: 18px;
      border: 1px solid var(--border-subtle);
      box-shadow: 0 4px 16px rgba(0,0,0,0.03);
      overflow: hidden;
      margin-bottom: 32px;
    }

    .reg-hero-header {
      background: #0f5c3a;
      color: #ffffff;
      padding: 26px 32px;
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
    }
    .reg-hero-left {
      display: flex;
      align-items: center;
      gap: 16px;
      position: relative;
      z-index: 2;
    }
    .reg-logo-badge {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(255, 255, 255, 0.2);
      flex-shrink: 0;
    }
    .reg-logo-badge svg {
      width: 26px;
      height: 26px;
      fill: #ffffff;
    }
    .reg-hero-titles h2 {
      font-family: 'Lora', serif;
      font-size: 1.35rem;
      font-weight: 700;
      margin: 0 0 4px;
      letter-spacing: -0.01em;
      color: #ffffff;
    }
    .reg-hero-titles p {
      margin: 0;
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.85);
      font-weight: 500;
    }
    .reg-batch-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* ── Assessment / Fee Box (From Photo Slip) ── */
    .assessment-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 20px 24px;
      margin: 24px 32px 10px;
      position: relative;
    }
    .assessment-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
      gap: 12px;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 12px;
    }
    .assessment-title-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .assessment-badge-pill {
      background: #0f5c3a;
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 6px;
      letter-spacing: 0.03em;
    }
    .assessment-title {
      font-size: 0.88rem;
      font-weight: 700;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .assessment-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      align-items: center;
    }
    .assessment-field-label {
      font-size: 0.72rem;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
      margin-bottom: 4px;
      display: block;
    }
    .assessment-field-val {
      font-weight: 700;
      font-size: 0.95rem;
      color: #111827;
    }

    /* ── Styled Form Sections ── */
    .form-content-area {
      padding: 28px 36px 36px;
    }
    .form-segment {
      margin-bottom: 32px;
      padding-bottom: 28px;
      border-bottom: 1px solid #f1f5f9;
    }
    .form-segment:last-of-type {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    .segment-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }
    .segment-icon-badge {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: var(--primary-female-light);
      color: var(--primary-female-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .segment-icon-badge svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }
    .segment-title {
      font-family: 'Lora', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #1f2937;
      margin: 0;
    }
    .segment-subtitle {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin: 2px 0 0;
    }

    /* ── Form Inputs & Grids ── */
    .form-grid-3 {
      display: grid;
      grid-template-columns: 2fr 1fr 2fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    .form-grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    .form-grid-1 {
      margin-bottom: 16px;
    }

    @media (max-width: 768px) {
      .form-grid-3, .form-grid-2 {
        grid-template-columns: 1fr;
      }
      .form-content-area {
        padding: 20px 20px 28px;
      }
      .assessment-box {
        margin: 20px 20px 10px;
      }
    }

    .form-group label {
      display: block;
      font-size: 0.8rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: 6px;
      letter-spacing: 0.01em;
    }
    .form-group label .req {
      color: #dc2626;
      font-weight: 800;
    }
    .form-input-control {
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: 1.5px solid var(--border-subtle);
      background: #ffffff;
      font-size: 0.92rem;
      font-family: inherit;
      color: #111827;
      box-sizing: border-box;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .form-input-control:focus {
      outline: none;
      border-color: var(--primary-female-dark);
      box-shadow: 0 0 0 3.5px rgba(212, 175, 55, 0.18);
    }
    .form-input-control::placeholder {
      color: #9ca3af;
      font-size: 0.88rem;
    }

    /* ── Radio Pills / Toggle Selectors ── */
    .radio-pill-group {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    .radio-pill-item {
      position: relative;
    }
    .radio-pill-item input[type="radio"],
    .radio-pill-item input[type="checkbox"] {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }
    .radio-pill-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      border-radius: 10px;
      border: 1.5px solid var(--border-subtle);
      background: #ffffff;
      font-size: 0.88rem;
      font-weight: 600;
      color: #475569;
      cursor: pointer;
      transition: all 0.2s;
      user-select: none;
    }
    .radio-pill-label:hover {
      border-color: #cbd5e1;
      background: #f8fafc;
      color: #0f172a;
    }
    .radio-pill-item input:checked + .radio-pill-label {
      background: #f0fdf4;
      border-color: #176b45;
      color: #176b45;
      box-shadow: none;
    }
    .radio-pill-dot {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      border: 2px solid #cbd5e1;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }
    .radio-pill-item input:checked + .radio-pill-label .radio-pill-dot {
      border-color: #176b45;
      background: #176b45;
    }
    .radio-pill-dot::after {
      content: '';
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: white;
      display: none;
    }
    .radio-pill-item input:checked + .radio-pill-label .radio-pill-dot::after {
      display: block;
    }

    /* ── Online Payment Gateway & E-Wallet Selectors ── */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .gateway-option-card {
      position: relative;
      cursor: pointer;
      display: block;
    }
    .gateway-option-card input[type="radio"] {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }
    .gateway-card-inner {
      padding: 12px 14px;
      border-radius: 10px;
      border: 1.5px solid #e2e8f0;
      background: #ffffff;
      transition: all 0.2s ease;
      text-align: left;
    }
    .gateway-option-card:hover .gateway-card-inner {
      border-color: #cbd5e1;
      background: #f8fafc;
    }
    .gateway-option-card input:checked + .gateway-card-inner {
      border-color: #176b45;
      background: #f0fdf4;
      box-shadow: none;
    }
    .gw-badge-pill {
      font-size: 0.65rem;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-block;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #e2e8f0;
    }
    .gw-title {
      font-size: 0.88rem;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 2px;
    }
    .gw-desc {
      font-size: 0.72rem;
      color: #64748b;
      line-height: 1.25;
    }

    /* ── Minor Notice Callout ── */
    .minor-notice-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 12px;
      padding: 12px 16px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 0.85rem;
      color: #1e40af;
    }
    .minor-notice-box svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
      flex-shrink: 0;
    }

    /* ── Signatures & Approvals Segment ── */
    .signature-card {
      background: #f8fafc;
      border: 1px solid var(--border-subtle);
      border-radius: 16px;
      padding: 20px;
    }
    .sig-line-input {
      border: none;
      border-bottom: 2px solid #cbd5e1;
      background: transparent;
      border-radius: 0;
      padding: 8px 4px;
      font-family: 'Lora', serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: #111827;
      width: 100%;
      box-sizing: border-box;
      transition: border-color 0.2s;
    }
    .sig-line-input:focus {
      outline: none;
      border-bottom-color: var(--primary-female-dark);
    }
    .sig-caption {
      font-size: 0.75rem;
      font-weight: 700;
      color: #64748b;
      margin-top: 6px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* ── Submission Footer ── */
    .reg-form-footer {
      padding: 20px 36px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fafaf9;
      border-top: 1px solid var(--border-subtle);
      flex-wrap: wrap;
      gap: 16px;
    }
    /* ── High-Contrast Button Typography & Visibility ── */
    .btn-topbar, a.btn-topbar, button.btn-topbar {
      background: #ffffff !important;
      color: #1e293b !important;
      border: 1.5px solid #cbd5e1 !important;
      font-weight: 700 !important;
      font-size: 0.84rem !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      text-decoration: none !important;
      cursor: pointer !important;
    }
    .btn-topbar:hover, a.btn-topbar:hover, button.btn-topbar:hover {
      background: #f8fafc !important;
      color: #0f5c3a !important;
      border-color: #0f5c3a !important;
    }
    .btn-topbar svg {
      fill: currentColor !important;
    }

    .btn-status-slip {
      background: #ffffff !important;
      color: #0f5c3a !important;
      font-weight: 800 !important;
      border: none !important;
      padding: 8px 16px !important;
      border-radius: 8px !important;
      cursor: pointer !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      font-size: 0.82rem !important;
      text-decoration: none !important;
      box-shadow: 0 2px 6px rgba(0,0,0,0.12) !important;
      transition: all 0.2s ease !important;
    }
    .btn-status-slip:hover {
      background: #f0fdf4 !important;
      color: #0b462c !important;
      transform: translateY(-1px) !important;
    }
    .btn-status-slip svg {
      fill: #0f5c3a !important;
    }

    .btn-cancel {
      background: #ffffff !important;
      color: #334155 !important;
      border: 1.5px solid #cbd5e1 !important;
      padding: 12px 24px !important;
      border-radius: 10px !important;
      font-weight: 700 !important;
      font-size: 0.9rem !important;
      cursor: pointer !important;
      display: inline-flex !important;
      align-items: center !important;
      text-decoration: none !important;
      transition: all 0.2s ease !important;
    }
    .btn-cancel:hover {
      background: #f1f5f9 !important;
      color: #0f172a !important;
      border-color: #94a3b8 !important;
    }

    .btn-submit-reg {
      background: #176b45 !important;
      color: #ffffff !important;
      border: none !important;
      padding: 12px 30px !important;
      border-radius: 10px !important;
      font-weight: 800 !important;
      font-size: 0.95rem !important;
      cursor: pointer !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 10px !important;
      transition: all 0.2s ease !important;
      box-shadow: 0 2px 6px rgba(23, 107, 69, 0.25) !important;
    }
    .btn-submit-reg:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 14px rgba(23, 107, 69, 0.35) !important;
      background: #0f5c3a !important;
      color: #ffffff !important;
    }
    .btn-submit-reg svg {
      fill: #ffffff !important;
    }
    .btn-submit-reg:disabled {
      background: #d1d5db !important;
      color: #6b7280 !important;
      cursor: not-allowed !important;
      box-shadow: none !important;
      transform: none !important;
    }

    .btn-view-doc, a.btn-view-doc, button.btn-view-doc {
      background: #176b45 !important;
      color: #ffffff !important;
      border: none !important;
      padding: 7px 14px !important;
      border-radius: 8px !important;
      font-weight: 700 !important;
      font-size: 0.8rem !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 6px !important;
      cursor: pointer !important;
      box-shadow: 0 2px 6px rgba(23, 107, 69, 0.2) !important;
      text-decoration: none !important;
    }
    .btn-view-doc:hover, a.btn-view-doc:hover, button.btn-view-doc:hover {
      background: #0f5c3a !important;
      color: #ffffff !important;
    }
    .btn-view-doc svg {
      fill: #ffffff !important;
    }

    /* ── Custom Alert UI Modal ── */
    #edu-alert-modal {
      z-index: 100000;
      backdrop-filter: blur(6px);
      background: rgba(15, 30, 22, 0.65);
      position: fixed; inset: 0;
      display: none; align-items: center; justify-content: center;
      padding: 20px;
      animation: modalFadeIn 0.25s ease-out forwards;
    }
    #edu-alert-modal.active { display: flex; }
    @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modalSlideUp { 
      0% { opacity: 0; transform: scale(0.9) translateY(16px); } 
      100% { opacity: 1; transform: scale(1) translateY(0); } 
    }
    .edu-alert-card {
      background: #ffffff;
      border-radius: 18px;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 20px 40px -8px rgba(15, 30, 22, 0.3);
      overflow: hidden;
      animation: modalSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      display: flex;
      flex-direction: column;
    }
    .edu-alert-stripe { height: 4px; width: 100%; background: #176b45; }
    .edu-alert-stripe.success { background: #10b981; }
    .edu-alert-stripe.warning { background: #f59e0b; }
    .edu-alert-stripe.error { background: #ef4444; }

    .edu-alert-body {
      padding: 32px 28px 16px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .edu-alert-icon-ring {
      width: 68px;
      height: 68px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 18px;
    }
    .edu-alert-icon-ring.success { background: #ecfdf5; color: #10b981; box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.12); }
    .edu-alert-icon-ring.warning { background: #fffbeb; color: #f59e0b; box-shadow: 0 0 0 8px rgba(245, 158, 11, 0.15); }
    .edu-alert-icon-ring.error { background: #fef2f2; color: #ef4444; box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.12); }
    .edu-alert-icon-ring svg { width: 34px; height: 34px; fill: currentColor; }

    .edu-alert-title {
      font-family: 'Lora', serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: #111827;
      margin: 0 0 8px;
    }
    .edu-alert-desc {
      font-size: 0.92rem;
      color: #4b5563;
      line-height: 1.6;
      margin: 0;
    }
    .edu-alert-details {
      margin-top: 16px;
      width: 100%;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 14px 16px;
      text-align: left;
      font-size: 0.85rem;
      box-sizing: border-box;
    }
    .edu-alert-actions {
      padding: 16px 24px 24px;
      display: flex;
      gap: 12px;
      width: 100%;
      box-sizing: border-box;
    }
    .btn-edu-alert {
      flex: 1;
      padding: 12px 18px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: none;
      font-family: inherit;
      transition: all 0.2s;
    }
    .btn-edu-alert.primary {
      background: #176b45;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(23, 107, 69, 0.2);
    }
    .btn-edu-alert.primary:hover {
      background: #0f5c3a;
    }
    .btn-edu-alert.secondary {
      background: #f1f5f9;
      color: #475569;
    }

    /* ── High-Fidelity Official Registration Document Modal (Green Theme) ── */
    .slip-modal {
      display: none; position: fixed; z-index: 10001; left: 0; top: 0; width: 100%; height: 100%;
      background: rgba(15, 45, 30, 0.72); backdrop-filter: blur(6px); align-items: center; justify-content: center;
      padding: 20px; box-sizing: border-box; overflow-y: auto;
    }
    .slip-paper-card {
      background: #ffffff;
      width: 100%;
      max-width: 680px;
      border-radius: 18px;
      box-shadow: 0 25px 60px rgba(15, 92, 58, 0.22);
      border: 1.5px solid #166534;
      overflow: hidden;
      margin: auto;
      animation: modalSlideUp 0.35s ease-out;
    }
    .slip-paper-inner {
      padding: 34px 38px;
      color: #0f172a;
      background: #ffffff;
    }

    /* Receipt Slip Box (Top-Left Slip) — Official ISCAG Green Theme */
    .doc-fee-box {
      border: 2px solid #166534;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 22px;
      background: #f4fbf7;
      font-size: 0.85rem;
      box-shadow: 0 2px 10px rgba(22, 101, 52, 0.08);
      position: relative;
    }
    .doc-fee-top-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 10px;
    }
    .doc-fee-logo {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 1.5px solid #166534;
      flex-shrink: 0;
      background: #ffffff;
      box-shadow: 0 1px 4px rgba(22, 101, 52, 0.15);
    }
    .doc-fee-header {
      font-weight: 800;
      text-align: center;
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #166534;
      line-height: 1.35;
    }
    .doc-fee-header .doc-fee-title {
      font-size: 0.95rem;
      font-weight: 900;
      color: #0f5c3a;
      letter-spacing: 0.05em;
      margin-top: 2px;
    }

    /* Official Document Header */
    .doc-official-header {
      text-align: center;
      border-bottom: 2.5px solid #166534;
      padding-bottom: 16px;
      margin-bottom: 22px;
      position: relative;
    }
    .doc-logo-circle {
      width: 62px;
      height: 62px;
      border-radius: 50%;
      border: 2px solid #166534;
      margin: 0 auto 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #ffffff;
      box-shadow: 0 2px 6px rgba(22, 101, 52, 0.15);
    }
    .doc-main-logo {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    .doc-header-org {
      font-size: 0.95rem;
      font-weight: 800;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: #166534;
    }
    .doc-header-title {
      font-size: 1.18rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin-top: 2px;
      color: #0f5c3a;
    }

    .doc-grid-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
      font-size: 0.86rem;
    }
    .doc-grid-table td {
      padding: 6px 4px;
      vertical-align: bottom;
    }
    .doc-label {
      font-weight: 800;
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      white-space: nowrap;
      width: 1%;
      padding-right: 8px !important;
      color: #166534;
    }
    .doc-underline {
      border-bottom: 1.5px solid #166534;
      font-weight: 700;
      padding-left: 6px;
      min-height: 20px;
      color: #0f172a;
    }
    .doc-section-divider {
      font-size: 0.82rem;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 2px solid #166534;
      padding: 6px 10px;
      margin: 18px 0 10px;
      color: #0f5c3a;
      background: #f0fdf4;
      border-radius: 4px 4px 0 0;
    }
    .doc-course-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      border: 1.5px solid #166534;
      border-radius: 6px;
      overflow: hidden;
      font-size: 0.82rem;
    }
    .doc-course-table td {
      padding: 7px 10px;
      border: 1px solid #bbf7d0;
      vertical-align: middle;
      transition: background 0.2s ease;
    }
    .doc-course-chk {
      font-weight: 900;
      color: #15803d;
      font-size: 0.95rem;
      margin-right: 6px;
      font-family: monospace;
    }
    .doc-course-name {
      font-weight: 700;
      color: #0f5c3a;
      font-size: 0.82rem;
    }
    .doc-signatures-row {
      display: flex;
      justify-content: space-between;
      gap: 30px;
      margin-top: 28px;
      text-align: center;
    }
    .doc-sig-col {
      flex: 1;
    }
    .doc-sig-line {
      border-bottom: 1.5px solid #166534;
      margin-bottom: 4px;
      height: 24px;
      font-family: 'Lora', serif;
      font-weight: 700;
      color: #0f5c3a;
    }
    .doc-sig-caption {
      font-size: 0.75rem;
      font-weight: 800;
      text-transform: uppercase;
      color: #166534;
    }

    @media print {
      @page {
        size: letter portrait;
        margin: 5mm 8mm;
      }

      /* Completely disable and hide all scrollbars across all elements in print */
      html, body {
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        background: #ffffff !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-size: 11px !important;
      }

      * {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        box-sizing: border-box !important;
      }

      ::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
      }

      /* Hide background app layout completely so it does not generate layout or scrollbars */
      .app-wrapper,
      .sidebar,
      .main-content,
      .top-bar,
      .breadcrumb-bar,
      .user-analytics,
      .status-hero,
      .reg-form-card,
      .history-card,
      #edu-alert-modal,
      .no-print {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
      }

      /* Visibility controls for document modal */
      body * {
        visibility: hidden;
      }

      #slip-modal,
      #slip-modal * {
        visibility: visible;
      }

      #slip-modal {
        display: block !important;
        position: static !important;
        inset: auto !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: auto !important;
        max-height: none !important;
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        z-index: auto !important;
      }

      .slip-paper-card {
        box-shadow: none !important;
        border: none !important;
        max-width: 100% !important;
        width: 100% !important;
        height: auto !important;
        border-radius: 0 !important;
        overflow: visible !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
      }

      .slip-paper-inner {
        padding: 0 !important;
        margin: 0 auto !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
        background: #ffffff !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
      }

      /* ── Fit to Letter Bond Paper: Proportional Compact Styling ── */
      .doc-fee-box {
        border: 1.5px solid #166534 !important;
        background: #ffffff !important;
        padding: 6px 12px !important;
        margin-bottom: 8px !important;
        border-radius: 6px !important;
        font-size: 0.74rem !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .doc-fee-top-row {
        gap: 8px !important;
        margin-bottom: 3px !important;
      }

      .doc-fee-logo {
        width: 36px !important;
        height: 36px !important;
        border-width: 1px !important;
      }

      .doc-fee-header {
        font-size: 0.7rem !important;
        line-height: 1.2 !important;
      }

      .doc-fee-header .doc-fee-title {
        font-size: 0.82rem !important;
        margin-top: 1px !important;
      }

      .doc-fee-box table {
        margin-bottom: 3px !important;
        font-size: 0.74rem !important;
      }

      .doc-fee-box table td {
        padding: 1.5px 2px !important;
      }

      /* Main Document Header */
      .doc-official-header {
        border-bottom: 2px solid #166534 !important;
        padding-bottom: 6px !important;
        margin-bottom: 8px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .doc-logo-circle {
        width: 44px !important;
        height: 44px !important;
        margin: 0 auto 3px !important;
        border-width: 1.5px !important;
      }

      .doc-header-org {
        font-size: 0.8rem !important;
        letter-spacing: 0.03em !important;
      }

      .doc-header-title {
        font-size: 1rem !important;
        margin-top: 1px !important;
      }

      /* Main Grid Tables */
      .doc-grid-table {
        margin-bottom: 4px !important;
        font-size: 0.75rem !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .doc-grid-table td {
        padding: 2px 2px !important;
        vertical-align: bottom !important;
      }

      .doc-label {
        font-size: 0.7rem !important;
        padding-right: 5px !important;
        color: #166534 !important;
      }

      .doc-underline {
        border-bottom: 1.2px solid #166534 !important;
        min-height: 15px !important;
        padding-left: 3px !important;
        font-size: 0.75rem !important;
      }

      /* Section Dividers */
      .doc-section-divider {
        font-size: 0.72rem !important;
        border-bottom: 1.5px solid #166534 !important;
        padding: 2.5px 5px !important;
        margin: 6px 0 4px !important;
        background: #f0fdf4 !important;
        color: #0f5c3a !important;
        page-break-after: avoid !important;
        break-after: avoid !important;
      }

      /* Courses Table in Print */
      .doc-course-table {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1.2px solid #166534 !important;
        margin-bottom: 4px !important;
        font-size: 0.72rem !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }
      .doc-course-table td {
        padding: 2px 6px !important;
        border: 1px solid #166534 !important;
        vertical-align: middle !important;
      }
      .doc-course-chk {
        font-weight: 900 !important;
        color: #15803d !important;
        font-size: 0.78rem !important;
        margin-right: 4px !important;
      }
      .doc-course-name {
        font-weight: 700 !important;
        color: #0f5c3a !important;
        font-size: 0.72rem !important;
      }

      /* Signatures */
      .doc-signatures-row {
        margin-top: 10px !important;
        gap: 20px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .doc-sig-line {
        border-bottom: 1.2px solid #166534 !important;
        height: 16px !important;
        font-size: 0.8rem !important;
        margin-bottom: 2px !important;
      }

      .doc-sig-caption {
        font-size: 0.66rem !important;
        color: #166534 !important;
      }

      .doc-underline, .doc-sig-line, .doc-official-header, .doc-section-divider {
        border-color: #166534 !important;
      }

      .doc-label, .doc-header-org, .doc-header-title, .doc-sig-caption, .doc-section-divider {
        color: #166534 !important;
      }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══ -->
  <?php 
    $active_page = 'female_education'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- ═══ MAIN CONTENT ═══ -->
  <div class="main-content">
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Da'wah Female Section — Islamic Education</div>
        <div class="top-bar-subtitle">Official Student Registration Form • Batch 08</div>
      </div>
      <div class="top-bar-actions">
        <?php if ($activeRequest): ?>
          <button type="button" class="btn-topbar" onclick="openSlipModal(<?= htmlspecialchars(json_encode($activeRequest)) ?>)">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            View Registration Slip
          </button>
        <?php endif; ?>
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span>Da'wah Female</span>
        <span class="sep">›</span>
        <span class="current">Registration Form</span>
      </div>

      <!-- ANALYTICS -->
      <div class="user-analytics">
        <div class="stat-card">
          <div class="stat-label">My Registrations</div>
          <div class="stat-value"><?= $analytics['total'] ?? count($history) ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Review</div>
          <div class="stat-value" style="color: #f59e0b;"><?= $analytics['pending'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Enrolled / Confirmed</div>
          <div class="stat-value" style="color: #10b981;"><?= $analytics['approved'] ?? 0 ?></div>
        </div>
      </div>

      <?php if ($hasPending || $hasApproved): ?>
      <!-- 📢 ACTIVE REGISTRATION STATUS HERO -->
      <div class="status-hero" style="background: white; border-radius: 16px; border: 1px solid var(--border-subtle); box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03); overflow: hidden; margin-bottom: 28px;">
        <div class="status-hero-top" style="background: #0f5c3a; padding: 22px 28px; position: relative; overflow: hidden;">
            <div class="status-hero-header" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; position: relative; z-index: 1; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 255, 255, 0.25); flex-shrink: 0;">
                        <svg viewBox="0 0 24 24" style="width: 22px; height: 22px; fill: #ffffff;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5.89 12.55L12 15.89l6.11-3.34c.66-.36.89-1.19.53-1.85-.36-.66-1.19-.89-1.85-.53L12 12.5 7.21 9.88c-.66-.36-1.49-.13-1.85.53-.36.67-.13 1.5.53 1.86z"/></svg>
                    </div>
                    <div>
                        <h4 style="font-family: 'Lora', serif; font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 0 0 3px;">Female Da'wah Education Application Status</h4>
                        <p style="font-size: 0.82rem; color: rgba(255, 255, 255, 0.85); margin: 0;">Ref: <strong>#EDU-<?= str_pad((string)($activeRequest['id'] ?? '1'), 4, '0', STR_PAD_LEFT) ?></strong> • Batch: <strong><?= htmlspecialchars($activeRequest['batch_no'] ?? 'Batch 08') ?></strong> • Submitted on <?= isset($activeRequest['created_at']) ? date('M d, Y', strtotime($activeRequest['created_at'])) : 'Recently' ?></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                  <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.25);">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #ffffff;"></span>
                    <?= $hasPending ? 'Pending Verification' : 'Officially Enrolled' ?>
                  </span>
                  <button type="button" onclick="openSlipModal(<?= htmlspecialchars(json_encode($activeRequest)) ?>)" class="btn-status-slip">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    View Official Slip
                  </button>
                </div>
            </div>
        </div>

        <!-- Summary Strip -->
        <div style="padding: 18px 32px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; background: #fafaf9; border-top: 1px solid var(--border-subtle);">
            <div style="background: white; padding: 12px; border-radius: 12px; border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Student Name</div>
                <div style="font-weight: 700; color: #111827; margin-top: 2px;"><?= htmlspecialchars(trim(($activeRequest['first_name'] ?? '') . ' ' . ($activeRequest['last_name'] ?? ''))) ?></div>
            </div>
            <div style="background: white; padding: 12px; border-radius: 12px; border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Muslim Name</div>
                <div style="font-weight: 700; color: var(--primary-female-dark); margin-top: 2px;"><?= htmlspecialchars($activeRequest['muslim_name'] ?: '—') ?></div>
            </div>
            <div style="background: white; padding: 12px; border-radius: 12px; border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Background</div>
                <div style="font-weight: 700; color: #111827; margin-top: 2px;"><?= htmlspecialchars($activeRequest['muslim_type'] ?? 'Revert') ?></div>
            </div>
            <div style="background: white; padding: 12px; border-radius: 12px; border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 0.68rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Payment Mode</div>
                <?php
                  $rawMethod = trim($activeRequest['payment_method'] ?? 'Cash');
                  $isOnline = (stripos($rawMethod, 'online') !== false || stripos($rawMethod, 'gcash') !== false || stripos($rawMethod, 'maya') !== false);
                  $displayMethod = $isOnline ? 'Online Payment' : 'Cash';
                ?>
                <div style="font-weight: 700; color: #166534; margin-top: 2px;"><?= htmlspecialchars($displayMethod) ?></div>
            </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📋 REGISTRATION FORM (Structure Recreated from Picture)   -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="reg-form-card" id="registration-form-card">
        <!-- Hero Header -->
        <div class="reg-hero-header">
          <div class="reg-hero-left">
            <div class="reg-logo-badge" style="padding:0; overflow:hidden; background:transparent;">
              <img src="<?= asset("assets/da'wah logo.jpg") ?>" alt="Da'wah Female Section Logo" style="width:100%;height:100%;border-radius:12px;object-fit:cover;display:block;" />
            </div>
            <div class="reg-hero-titles">
              <h2>ISCAG PHILIPPINES — DA'WAH FEMALE SECTION</h2>
              <p>Official Student Registration Form • Islamic Studies & Arabic Education</p>
            </div>
          </div>
          <div class="reg-batch-badge">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            BATCH 08 ADMISSION
          </div>
        </div>

        <form id="female-education-form" onsubmit="submitRegistrationForm(event)">
          <!-- ── REGISTRATION FEE & ASSESSMENT PANEL (From Top-Left Slip in Photo) ── -->
          <div class="assessment-box">
            <div class="assessment-header">
              <div class="assessment-title-wrap">
                <span class="assessment-badge-pill">BATCH 08</span>
                <span class="assessment-title">Registration Fee &amp; Level Assessment</span>
              </div>
              <div style="font-size:0.8rem; font-weight:600; color:#64748b;">
                Approved by: <strong style="color:#0f172a;">Sister Khadijah Dimaano</strong>
              </div>
            </div>

            <div class="assessment-grid">
              <div>
                <span class="assessment-field-label">Assigned Level / Track <span style="color:#dc2626;">*</span></span>
                <select name="level" id="reg-level" class="form-input-control" style="background:#ffffff; border-color:#cbd5e1; font-weight:600; color:#0f172a;">
                  <option value="Level 1 — Basic Arabic &amp; Tajweed" selected>Level 1 — Basic Arabic &amp; Tajweed</option>
                  <option value="Level 2 — Intermediate Islamic Studies &amp; Fiqh">Level 2 — Intermediate Islamic Studies &amp; Fiqh</option>
                  <option value="Level 3 — Tahfidhul Qur'an &amp; Advanced Hadith">Level 3 — Tahfidhul Qur'an &amp; Advanced Hadith</option>
                  <option value="Special Class — Revert Support &amp; Essentials">Special Class — Revert Support &amp; Essentials</option>
                </select>
              </div>

              <div>
                <span class="assessment-field-label">Registration Fee</span>
                <div class="form-input-control" style="background:#ffffff; border:1px solid #e2e8f0; font-weight:800; color:#0f172a; display:flex; align-items:center; justify-content:space-between;">
                  <span style="font-size:1.05rem;">₱ 120.00</span>
                  <span style="font-size:0.68rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; border:1px solid #e2e8f0;">Official Rate</span>
                </div>
              </div>

              <div>
                <span class="assessment-field-label">Registration Date</span>
                <div class="form-input-control" style="background:#ffffff; border:1px solid #e2e8f0; font-weight:600; color:#334155;">
                  <?= date('F d, Y') ?>
                </div>
              </div>

              <div>
                <span class="assessment-field-label">Payment Mode <span style="color:#dc2626;">*</span></span>
                <div class="radio-pill-group" style="margin-top:4px;">
                  <div class="radio-pill-item">
                    <input type="radio" name="payment_method" id="pay-cash" value="Cash" checked onchange="handlePaymentModeChange('Cash')">
                    <label for="pay-cash" class="radio-pill-label" style="padding:8px 16px;">
                      <span class="radio-pill-dot"></span> Cash
                    </label>
                  </div>
                  <div class="radio-pill-item">
                    <input type="radio" name="payment_method" id="pay-online" value="Online Payment" onchange="handlePaymentModeChange('Online Payment')">
                    <label for="pay-online" class="radio-pill-label" style="padding:8px 16px;">
                      <span class="radio-pill-dot"></span> Online Payment
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- ── Online Payment E-Wallet / Gateway Selection Panel (Toggled when Online Payment is selected) ── -->
            <div id="online-gateway-panel" style="display:none; margin-top:16px; padding-top:16px; border-top:1px solid #e2e8f0; animation: fadeInDown 0.25s ease;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
                <div>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:0.75rem; font-weight:700; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em;">Select E-Wallet or Payment Gateway</span>
                    <span style="font-size:0.7rem; font-weight:700; background:#f0fdf4; color:#176b45; border:1px solid #bbf7d0; padding:2px 8px; border-radius:4px;">Fee: ₱120.00</span>
                  </div>
                  <div style="font-size:0.82rem; color:#64748b; margin-top:2px;">Select your preferred electronic channel to complete the registration fee.</div>
                </div>
                <div style="background:#ffffff; border:1px solid #e2e8f0; padding:6px 14px; border-radius:8px; text-align:right;">
                  <span style="font-size:0.68rem; font-weight:700; color:#64748b; display:block; text-transform:uppercase;">Registration Fee Due</span>
                  <span style="font-size:1.15rem; font-weight:800; color:#0f172a; line-height:1;">₱120.00</span>
                </div>
              </div>

              <!-- E-Wallet / Gateway Selection Cards -->
              <div class="gateway-options-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(135px, 1fr)); gap:10px; margin-bottom:14px;">
                <!-- GCash -->
                <label class="gateway-option-card" for="gw-gcash">
                  <input type="radio" name="payment_channel" id="gw-gcash" value="GCash" checked onchange="onChannelChange('GCash')">
                  <div class="gateway-card-inner">
                    <span class="gw-badge-pill">e-Wallet</span>
                    <div class="gw-title">GCash</div>
                    <div class="gw-desc">Instant Mobile Wallet</div>
                  </div>
                </label>

                <!-- Maya -->
                <label class="gateway-option-card" for="gw-maya">
                  <input type="radio" name="payment_channel" id="gw-maya" value="Maya" onchange="onChannelChange('Maya')">
                  <div class="gateway-card-inner">
                    <span class="gw-badge-pill">e-Wallet</span>
                    <div class="gw-title">Maya</div>
                    <div class="gw-desc">Maya Wallet / QR</div>
                  </div>
                </label>

                <!-- QR Ph -->
                <label class="gateway-option-card" for="gw-qrph">
                  <input type="radio" name="payment_channel" id="gw-qrph" value="QR Ph" onchange="onChannelChange('QR Ph')">
                  <div class="gateway-card-inner">
                    <span class="gw-badge-pill">Universal QR</span>
                    <div class="gw-title">QR Ph</div>
                    <div class="gw-desc">Scan via Any Bank/App</div>
                  </div>
                </label>

                <!-- PayMongo Gateway -->
                <label class="gateway-option-card" for="gw-paymongo">
                  <input type="radio" name="payment_channel" id="gw-paymongo" value="PayMongo Gateway" onchange="onChannelChange('PayMongo Gateway')">
                  <div class="gateway-card-inner">
                    <span class="gw-badge-pill">Gateway</span>
                    <div class="gw-title">PayMongo</div>
                    <div class="gw-desc">Credit / Debit Card</div>
                  </div>
                </label>

                <!-- Bank Transfer -->
                <label class="gateway-option-card" for="gw-bank">
                  <input type="radio" name="payment_channel" id="gw-bank" value="Bank Transfer" onchange="onChannelChange('Bank Transfer')">
                  <div class="gateway-card-inner">
                    <span class="gw-badge-pill">Online Banking</span>
                    <div class="gw-title">Bank Transfer</div>
                    <div class="gw-desc">BDO / BPI / UnionBank</div>
                  </div>
                </label>
              </div>

              <!-- Channel Guidance & Reference Input -->
              <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:10px; font-size:0.84rem; color:#334155;">
                  <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#176b45;flex-shrink:0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  <span id="gateway-hint-text">Fee: <strong>₱120.00</strong>. Please settle via <strong>GCash</strong> and enter your reference number or mobile number below.</span>
                </div>
                <div style="flex:1; min-width:220px; max-width:320px;">
                  <input type="text" name="payment_reference" id="reg-payment-ref" class="form-input-control" placeholder="Ref # or Sender No. (Optional)" style="padding:9px 12px; font-size:0.84rem; background:#ffffff; border:1.5px solid #cbd5e1;">
                </div>
              </div>
            </div>
          </div>

          <div class="form-content-area">
            <!-- ── SECTION 1: PERSONAL INFORMATION & MUSLIM IDENTITY ── -->
            <div class="form-segment">
              <div class="segment-header">
                <div class="segment-icon-badge">
                  <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div>
                  <h4 class="segment-title">Student Personal Information</h4>
                  <p class="segment-subtitle">Name, Muslim identity, and personal background</p>
                </div>
              </div>

              <!-- Name Row (Last Name, First Name, M.I.) -->
              <div class="form-grid-3">
                <div class="form-group">
                  <label>Last Name <span class="req">*</span></label>
                  <input type="text" name="last_name" id="reg-last-name" class="form-input-control" placeholder="Dela Cruz" value="<?= htmlspecialchars($dbUser['last_name'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                  <label>M.I.</label>
                  <input type="text" name="middle_initial" id="reg-mi" class="form-input-control" placeholder="M." maxlength="10" />
                </div>
                <div class="form-group">
                  <label>First Name <span class="req">*</span></label>
                  <input type="text" name="first_name" id="reg-first-name" class="form-input-control" placeholder="Fatima" value="<?= htmlspecialchars($dbUser['first_name'] ?? '') ?>" required />
                </div>
              </div>

              <!-- Muslim Identity & Revert vs Born-Muslim -->
              <div class="form-grid-2">
                <div class="form-group">
                  <label>Muslim Name (Adopted / Islamic Name)</label>
                  <input type="text" name="muslim_name" id="reg-muslim-name" class="form-input-control" placeholder="e.g. Khadija / Aisha / Maryam" />
                </div>
                <div class="form-group">
                  <label>Islamic Background Status <span class="req">*</span></label>
                  <div class="radio-pill-group" style="margin-top:2px;">
                    <div class="radio-pill-item">
                      <input type="radio" name="muslim_type" id="type-revert" value="Revert" checked onchange="toggleShahadahField(this.value)">
                      <label for="type-revert" class="radio-pill-label">
                        <span class="radio-pill-dot"></span> Revert
                      </label>
                    </div>
                    <div class="radio-pill-item">
                      <input type="radio" name="muslim_type" id="type-born" value="Born-Muslim" onchange="toggleShahadahField(this.value)">
                      <label for="type-born" class="radio-pill-label">
                        <span class="radio-pill-dot"></span> Born-Muslim
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Dates: Date of Birth & Date of Shahadah -->
              <div class="form-grid-2">
                <div class="form-group">
                  <label>Date of Birth <span class="req">*</span></label>
                  <input type="date" name="birthdate" id="reg-dob" class="form-input-control" onchange="checkMinorStatus(this.value)" value="<?= htmlspecialchars($dbUser['birthdate'] ?? '') ?>" required />
                </div>
                <div class="form-group" id="shahadah-date-group">
                  <label>Date of Shahadah</label>
                  <input type="date" name="date_of_shahadah" id="reg-shahadah" class="form-input-control" />
                </div>
              </div>

              <!-- Complete Residential Address -->
              <div class="form-grid-1">
                <div class="form-group">
                  <label>Address <span class="req">*</span></label>
                  <input type="text" name="address" id="reg-address" class="form-input-control" placeholder="House/Bldg No., Street, Barangay, City, Province" value="<?= htmlspecialchars($dbUser['address'] ?? ($dbUser['current_address'] ?? '')) ?>" required />
                </div>
              </div>

              <!-- Contact & Social -->
              <div class="form-grid-2">
                <div class="form-group">
                  <label>Contact Number <span class="req">*</span></label>
                  <input type="tel" name="contact_number" id="reg-contact" class="form-input-control" placeholder="09XX-XXX-XXXX" value="<?= htmlspecialchars($dbUser['contact_number'] ?? ($dbUser['phone'] ?? '')) ?>" required />
                </div>
                <div class="form-group">
                  <label>Facebook Account / Profile Name</label>
                  <input type="text" name="facebook_account" id="reg-facebook" class="form-input-control" placeholder="facebook.com/yourprofile or Account Name" />
                </div>
              </div>
            </div>

            <!-- ── SECTION 2: PRIOR ISLAMIC STUDIES ── -->
            <div class="form-segment">
              <div class="segment-header">
                <div class="segment-icon-badge">
                  <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
                </div>
                <div>
                  <h4 class="segment-title">Islamic Educational Background</h4>
                  <p class="segment-subtitle">Prior madrasah or Islamic studies experience</p>
                </div>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label>Have you studied Islamic Studies before? <span class="req">*</span></label>
                  <div class="radio-pill-group" style="margin-top:2px;">
                    <div class="radio-pill-item">
                      <input type="radio" name="studied_islamic_before" id="study-yes" value="Yes" onchange="togglePreviousSchool(true)">
                      <label for="study-yes" class="radio-pill-label">
                        <span class="radio-pill-dot"></span> YES
                      </label>
                    </div>
                    <div class="radio-pill-item">
                      <input type="radio" name="studied_islamic_before" id="study-no" value="No" checked onchange="togglePreviousSchool(false)">
                      <label for="study-no" class="radio-pill-label">
                        <span class="radio-pill-dot"></span> NO
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group" id="previous-school-group" style="opacity:0.4; pointer-events:none; transition:all 0.3s;">
                  <label>If YES, where? (Institution / Center / Madrasah)</label>
                  <input type="text" name="previous_school" id="reg-previous-school" class="form-input-control" placeholder="e.g. ISCAG Madrasah / Online Academy / Local Mosque" disabled />
                </div>
              </div>
            </div>

            <!-- ── SECTION 3: EMERGENCY CONTACT ── -->
            <div class="form-segment">
              <div class="segment-header">
                <div class="segment-icon-badge">
                  <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <div>
                  <h4 class="segment-title">Person to be Contacted in Case of Emergency</h4>
                  <p class="segment-subtitle">Designated relative, spouse, or emergency contact</p>
                </div>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label>Contact Person Name <span class="req">*</span></label>
                  <input type="text" name="emergency_name" id="reg-emergency-name" class="form-input-control" placeholder="Full Name" required />
                </div>
                <div class="form-group">
                  <label>Relationship <span class="req">*</span></label>
                  <input type="text" name="emergency_relationship" id="reg-emergency-rel" class="form-input-control" placeholder="e.g. Mother / Father / Spouse / Sister" required />
                </div>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label>Emergency Contact Number <span class="req">*</span></label>
                  <input type="tel" name="emergency_contact" id="reg-emergency-contact" class="form-input-control" placeholder="09XX-XXX-XXXX" required />
                </div>
                <div class="form-group">
                  <label>Address</label>
                  <input type="text" name="emergency_address" id="reg-emergency-address" class="form-input-control" placeholder="Complete Address" />
                </div>
              </div>
            </div>

            <!-- ── SECTION 4: PARENT INFORMATION (IF MINOR) ── -->
            <div class="form-segment" id="minor-segment">
              <div class="segment-header">
                <div class="segment-icon-badge">
                  <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
                <div>
                  <h4 class="segment-title">If Minor, Please Indicate Parent's Name</h4>
                  <p class="segment-subtitle">Parent or legal guardian details for students under 18</p>
                </div>
              </div>

              <div class="minor-notice-box" id="minor-callout" style="display:none;">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <span>Based on the entered date of birth, this applicant is below 18 years old. Parent / Guardian information is required.</span>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label>Parent's Name</label>
                  <input type="text" name="parent_name" id="reg-parent-name" class="form-input-control" placeholder="Mother or Father's Full Name" />
                </div>
                <div class="form-group">
                  <label>Parent's Contact Number</label>
                  <input type="tel" name="parent_contact" id="reg-parent-contact" class="form-input-control" placeholder="09XX-XXX-XXXX" />
                </div>
              </div>

              <div class="form-grid-1">
                <div class="form-group">
                  <label>Parent's Address</label>
                  <input type="text" name="parent_address" id="reg-parent-address" class="form-input-control" placeholder="Complete Residential Address" />
                </div>
              </div>
            </div>

            <!-- ── SECTION 5: SIGNATURES & ENDORSEMENT ── -->
            <div class="form-segment">
              <div class="segment-header">
                <div class="segment-icon-badge">
                  <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                </div>
                <div>
                  <h4 class="segment-title">Signatures &amp; Administrative Endorsement</h4>
                  <p class="segment-subtitle">Declaration of truth and official administrative approvals</p>
                </div>
              </div>

              <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:14px 18px; margin-bottom:20px; font-size:0.85rem; color:#166534; line-height:1.5;">
                <strong>Applicant Certification:</strong> I hereby certify that the information provided in this registration form is complete, true, and correct. I agree to abide by the Islamic code of conduct and academic policies of the ISCAG Da'wah Female Section.
              </div>

              <div class="form-grid-2">
                <div class="signature-card">
                  <input type="text" name="student_signature" id="reg-student-sig" class="sig-line-input" placeholder="Type Full Name as Digital Signature" required />
                  <div class="sig-caption">Signature of Student <span style="color:#dc2626;">*</span></div>
                </div>

                <div class="signature-card">
                  <input type="text" name="guardian_signature" id="reg-guardian-sig" class="sig-line-input" placeholder="Parent / Guardian Name (if minor)" />
                  <div class="sig-caption">Signature of Guardian (if minor)</div>
                </div>
              </div>

              <div style="display:flex; justify-content:space-between; align-items:center; background:#fafaf9; border:1px solid var(--border-subtle); border-radius:12px; padding:14px 20px; margin-top:16px; flex-wrap:wrap; gap:12px;">
                <div style="font-size:0.85rem; color:#374151;">
                  Approved by: <strong style="color:var(--primary-female-dark);">KHADIJAH DIMAANO</strong> (Section Head)
                </div>
                <div style="font-size:0.85rem; color:#374151;">
                  Prepared by: <strong style="color:var(--forest-dark);">IDFS STAFF</strong> (ISCAG Philippines)
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Row -->
          <div class="reg-form-footer">
            <button type="button" class="btn-cancel" onclick="window.location.href='<?= url('/user/dashboard') ?>'">
              Cancel
            </button>
            <button type="submit" id="btn-submit-form" class="btn-submit-reg">
              <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              Submit Registration Form
            </button>
          </div>
        </form>
      </div>

      <!-- ══════════════════════════════════════════════════════════ -->
      <!-- 📜 APPLICATION HISTORY TABLE                              -->
      <!-- ══════════════════════════════════════════════════════════ -->
      <div class="section-card" style="margin-top:24px;">
        <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
          <h6>
            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary-female);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            My Registration Records &amp; History
          </h6>
          <span style="font-size:0.8rem; color:var(--text-muted); font-weight:700;">Total Filed: <?= count($history) ?></span>
        </div>
        <div class="section-card-body" style="padding:0;">
          <div class="table-wrapper">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Student Name</th>
                  <th>Program / Level</th>
                  <th>Batch</th>
                  <th>Payment Method</th>
                  <th>Date Filed</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="history-tbody">
                <?php if (empty($history)): ?>
                  <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No student registrations found yet. Fill out the form above to enroll!</td></tr>
                <?php else: ?>
                  <?php foreach ($history as $h): ?>
                    <?php 
                      $st = strtolower($h['status'] ?? 'pending');
                      $refCode = '#EDU-' . str_pad((string)$h['id'], 4, '0', STR_PAD_LEFT);
                    ?>
                    <tr>
                      <td class="td-id"><strong><?= $refCode ?></strong></td>
                      <td style="font-weight:700; color:#111827;"><?= htmlspecialchars(trim(($h['first_name'] ?? '') . ' ' . ($h['last_name'] ?? ''))) ?></td>
                      <td><?= htmlspecialchars($h['level'] ?? ($h['program_name'] ?? 'Basic')) ?></td>
                      <td><span style="background:#fef3c7; color:#b45309; padding:2px 8px; border-radius:6px; font-weight:800; font-size:0.75rem;"><?= htmlspecialchars($h['batch_no'] ?? 'Batch 08') ?></span></td>
                      <td>
                        <?php
                          $hRawMethod = trim($h['payment_method'] ?? 'Cash');
                          $hChannel = trim($h['payment_channel'] ?? '');
                          $hIsOnline = (stripos($hRawMethod, 'online') !== false || stripos($hRawMethod, 'gcash') !== false || stripos($hRawMethod, 'maya') !== false);
                          $hDisplayMethod = $hIsOnline ? 'Online Payment' : 'Cash';
                        ?>
                        <span style="font-weight:700; color:<?= $hIsOnline ? '#0f5c3a' : '#166534' ?>;"><?= htmlspecialchars($hDisplayMethod) ?></span>
                        <?php if ($hIsOnline && !empty($hChannel)): ?>
                          <div style="font-size:0.75rem; color:#64748b; font-weight:600;"><?= htmlspecialchars($hChannel) ?></div>
                        <?php endif; ?>
                      </td>
                      <td><?= date('M d, Y', strtotime($h['created_at'])) ?></td>
                      <td>
                        <?php if ($st === 'active' || $st === 'completed'): ?>
                          <span class="badge-status badge-approved" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-weight:800;">Enrolled</span>
                        <?php elseif ($st === 'pending'): ?>
                          <span class="badge-status badge-pending" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-weight:800;">Under Review</span>
                        <?php else: ?>
                          <span class="badge-status badge-rejected" style="font-weight:800;"><?= ucfirst($st) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn-view-doc" onclick='openSlipModal(<?= json_encode($h) ?>)' title="View Registration Form">
                          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                          View
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

    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- 🔔 CUSTOM FEEDBACK / ALERT MODAL                           -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="edu-alert-modal" onclick="if(event.target===this) closeEduAlert()">
  <div class="edu-alert-card" role="dialog" aria-modal="true">
    <div class="edu-alert-stripe" id="edu-alert-stripe"></div>
    <div class="edu-alert-body">
      <div class="edu-alert-icon-ring" id="edu-alert-icon">
        <svg id="edu-alert-svg" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
      </div>
      <h3 class="edu-alert-title" id="edu-alert-title">Notice</h3>
      <p class="edu-alert-desc" id="edu-alert-desc"></p>
      <div class="edu-alert-details" id="edu-alert-details" style="display:none;"></div>
    </div>
    <div class="edu-alert-actions">
      <button type="button" class="btn-edu-alert secondary" id="edu-alert-cancel-btn" style="display:none;" onclick="closeEduAlert(false)">Cancel</button>
      <button type="button" class="btn-edu-alert primary" id="edu-alert-confirm-btn" onclick="closeEduAlert(true)">Acknowledge</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- 📄 HIGH-FIDELITY REGISTRATION FORM PRINTABLE MODAL         -->
<!-- ══════════════════════════════════════════════════════════ -->
<div id="slip-modal" class="slip-modal" onclick="if(event.target===this) closeSlipModal()">
  <div class="slip-paper-card">
    <!-- Screen View Course Track Banner (Hidden in Print) -->
    <div class="no-print" style="background:#f0fdf4; border-bottom:1.5px solid #bbf7d0; padding:12px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-size:0.75rem; font-weight:800; color:#166534; text-transform:uppercase; letter-spacing:0.04em;">Enrolled Course Track:</span>
        <span id="doc-modal-course-badge" style="background:#166534; color:#ffffff; font-size:0.82rem; font-weight:700; padding:3px 12px; border-radius:6px;">Level 1 — Basic Arabic &amp; Tajweed</span>
      </div>
      <div style="font-size:0.8rem; color:#15803d; font-weight:700;">
        Female Da'wah Section • Batch 08
      </div>
    </div>

    <div class="slip-paper-inner" id="printable-doc-area">
      <!-- Fee Box (Top left slip from photo) -->
      <div class="doc-fee-box">
        <div class="doc-fee-top-row">
          <img src="<?= asset("assets/da'wah logo.jpg") ?>" alt="Da'wah Female Section Logo" class="doc-fee-logo" />
          <div class="doc-fee-header">
            <div>ISCAG PHILIPPINES — DA'WAH FEMALE SECTION</div>
            <div class="doc-fee-title">REGISTRATION FEE — <span id="doc-fee-batch">BATCH 08</span></div>
          </div>
        </div>
        <table style="width:100%; border-collapse:collapse; margin-bottom:8px; font-size:0.85rem;">
          <tr>
            <td style="width:22%; font-weight:800; color:#166534; white-space:nowrap;">NAME:</td>
            <td style="border-bottom:1.5px solid #166534; font-weight:700; color:#0f5c3a;" id="doc-fee-name">—</td>
          </tr>
          <tr>
            <td style="font-weight:800; padding-top:4px; color:#166534;">DATE:</td>
            <td style="border-bottom:1.5px solid #166534; font-weight:700; padding-top:4px; color:#0f5c3a;" id="doc-fee-date">—</td>
          </tr>
          <tr>
            <td style="font-weight:800; padding-top:4px; color:#166534; white-space:nowrap;">LEVEL / COURSE:</td>
            <td style="border-bottom:1.5px solid #166534; font-weight:700; padding-top:4px; color:#0f5c3a;" id="doc-fee-level">Level 1 — Basic Arabic &amp; Tajweed</td>
          </tr>
          <tr>
            <td style="font-weight:800; padding-top:4px; color:#166534;">FEE:</td>
            <td style="border-bottom:1.5px solid #166534; font-weight:800; padding-top:4px; color:#15803d; font-size:0.95rem;" id="doc-fee-price">₱ 120.00</td>
          </tr>
        </table>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; font-size:0.8rem; font-weight:800; color:#166534; flex-wrap:wrap; gap:8px;">
          <div style="display:flex; gap:16px;">
            <label><span id="doc-pay-cash-box" style="font-weight:900; color:#15803d;">[ ]</span> CASH</label>
            <label><span id="doc-pay-online-box" style="font-weight:900; color:#15803d;">[ ]</span> ONLINE PAYMENT <span id="doc-pay-channel-badge" style="font-size:0.75rem; color:#166534; font-weight:700;"></span></label>
          </div>
          <div>
            APPROVED BY: <span style="font-weight:900; color:#0f5c3a;">SISTER KHADIJAH DIMAANO</span>
          </div>
        </div>
      </div>

      <!-- Main Document Header -->
      <div class="doc-official-header">
        <div class="doc-logo-circle">
          <img src="<?= asset("assets/da'wah logo.jpg") ?>" alt="Da'wah Female Section Logo" class="doc-main-logo" />
        </div>
        <div class="doc-header-org">ISCAG PHILIPPINES — DA'WAH FEMALE SECTION</div>
        <div class="doc-header-title">REGISTRATION FORM</div>
      </div>

      <!-- Main Fields Table -->
      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">LAST NAME:</td>
          <td class="doc-underline" style="width:35%;" id="doc-last-name">—</td>
          <td class="doc-label">FIRST NAME:</td>
          <td class="doc-underline" style="width:35%;" id="doc-first-name">—</td>
          <td class="doc-label">M.I.:</td>
          <td class="doc-underline" style="width:10%;" id="doc-mi">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">MUSLIM NAME:</td>
          <td class="doc-underline" style="width:35%;" id="doc-muslim-name">—</td>
          <td class="doc-label">REVERT:</td>
          <td class="doc-underline" style="width:15%; text-align:center;" id="doc-is-revert">—</td>
          <td class="doc-label">BORN-MUSLIM:</td>
          <td class="doc-underline" style="width:15%; text-align:center;" id="doc-is-born">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">DATE OF BIRTH:</td>
          <td class="doc-underline" style="width:40%;" id="doc-dob">—</td>
          <td class="doc-label">DATE OF SHAHADAH:</td>
          <td class="doc-underline" style="width:40%;" id="doc-shahadah">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">ADDRESS:</td>
          <td class="doc-underline" colspan="3" id="doc-address">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">CONTACT NUMBER:</td>
          <td class="doc-underline" style="width:40%;" id="doc-contact">—</td>
          <td class="doc-label">FACEBOOK ACCOUNT:</td>
          <td class="doc-underline" style="width:40%;" id="doc-facebook">—</td>
        </tr>
      </table>

      <!-- Course / Level Enrolled Section -->
      <div class="doc-section-divider">ENROLLED COURSE / PROGRAM TRACK:</div>
      <table class="doc-course-table">
        <tr>
          <td id="doc-course-cell-1" style="width:50%;">
            <span id="doc-course-chk-1" class="doc-course-chk">[✓]</span>
            <span class="doc-course-name">Level 1 — Basic Arabic &amp; Tajweed</span>
          </td>
          <td id="doc-course-cell-2" style="width:50%;">
            <span id="doc-course-chk-2" class="doc-course-chk">[ ]</span>
            <span class="doc-course-name">Level 2 — Intermediate Islamic Studies &amp; Fiqh</span>
          </td>
        </tr>
        <tr>
          <td id="doc-course-cell-3" style="width:50%;">
            <span id="doc-course-chk-3" class="doc-course-chk">[ ]</span>
            <span class="doc-course-name">Level 3 — Tahfidhul Qur'an &amp; Advanced Hadith</span>
          </td>
          <td id="doc-course-cell-4" style="width:50%;">
            <span id="doc-course-chk-4" class="doc-course-chk">[ ]</span>
            <span class="doc-course-name">Special Class — Revert Support &amp; Essentials</span>
          </td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label" style="width:auto;">HAVE YOU STUDIED ISLAMIC STUDIES BEFORE?</td>
          <td style="width:1%; padding-left:12px;">YES:</td>
          <td class="doc-underline" style="width:60px; text-align:center;" id="doc-study-yes">—</td>
          <td style="width:1%; padding-left:12px;">NO:</td>
          <td class="doc-underline" style="width:60px; text-align:center;" id="doc-study-no">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">IF YES, WHERE?</td>
          <td class="doc-underline" id="doc-where">—</td>
        </tr>
      </table>

      <!-- Emergency Section -->
      <div class="doc-section-divider">PERSON TO BE CONTACTED IN CASE OF EMERGENCY:</div>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">NAME:</td>
          <td class="doc-underline" style="width:45%;" id="doc-em-name">—</td>
          <td class="doc-label">RELATIONSHIP:</td>
          <td class="doc-underline" style="width:40%;" id="doc-em-rel">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">ADDRESS:</td>
          <td class="doc-underline" id="doc-em-address">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">CONTACT NUMBER:</td>
          <td class="doc-underline" id="doc-em-contact">—</td>
        </tr>
      </table>

      <!-- Minor Section -->
      <div class="doc-section-divider">IF MINOR, PLEASE INDICATE THE PARENT'S NAME:</div>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">PARENT'S NAME:</td>
          <td class="doc-underline" style="width:45%;" id="doc-par-name">—</td>
          <td class="doc-label">CONTACT NUMBER:</td>
          <td class="doc-underline" style="width:40%;" id="doc-par-contact">—</td>
        </tr>
      </table>

      <table class="doc-grid-table">
        <tr>
          <td class="doc-label">ADDRESS:</td>
          <td class="doc-underline" id="doc-par-address">—</td>
        </tr>
      </table>

      <!-- Signatures Row -->
      <div class="doc-signatures-row">
        <div class="doc-sig-col">
          <div class="doc-sig-line" id="doc-sig-guardian">—</div>
          <div class="doc-sig-caption">Signature of Guardian</div>
        </div>
        <div class="doc-sig-col">
          <div class="doc-sig-line" id="doc-sig-student">—</div>
          <div class="doc-sig-caption">Signature of Student</div>
        </div>
      </div>

      <!-- Approvals -->
      <div style="display:flex; justify-content:space-between; margin-top:24px; font-size:0.8rem; font-weight:800; border-top:1.5px solid #166534; padding-top:12px; color:#166534;">
        <div>APPROVED BY: <span style="font-weight:900; color:#0f5c3a;">KHADIJAH DIMAANO</span></div>
        <div>PREPARED BY: <span style="font-weight:900; color:#0f5c3a;">IDFS STAFF</span></div>
      </div>
    </div>

    <!-- Print Footer Actions -->
    <div style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;" class="no-print">
      <button type="button" onclick="closeSlipModal()" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; padding:10px 22px; border-radius:10px; font-weight:700; cursor:pointer;">Close</button>
      <button type="button" onclick="window.print()" style="background:#176b45; color:#ffffff; border:none; padding:10px 24px; border-radius:10px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:#ffffff;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
        Print Official Document
      </button>
    </div>
  </div>
</div>

<script>
  // ── Toggle Payment Mode (Online Payment vs Cash) ──
  function handlePaymentModeChange(mode) {
    const panel = document.getElementById('online-gateway-panel');
    if (!panel) return;
    if (mode === 'Online Payment') {
      panel.style.display = 'block';
      const selectedChannel = document.querySelector('input[name="payment_channel"]:checked');
      if (selectedChannel) {
        onChannelChange(selectedChannel.value);
      }
    } else {
      panel.style.display = 'none';
    }
  }

  // ── Gateway / E-Wallet Selection Hint Handler ──
  function onChannelChange(channel) {
    const hint = document.getElementById('gateway-hint-text');
    if (!hint) return;
    const hints = {
      'GCash': 'Fee: <strong>₱120.00</strong>. Please settle via <strong>GCash</strong> and enter your reference number or mobile number below.',
      'Maya': 'Fee: <strong>₱120.00</strong>. Please send via <strong>Maya</strong> and enter your transaction reference below.',
      'QR Ph': 'Fee: <strong>₱120.00</strong>. Scan the official ISCAG <strong>QR Ph</strong> code with any banking or e-wallet app.',
      'PayMongo Gateway': 'Fee: <strong>₱120.00</strong>. Pay securely online via <strong>PayMongo</strong> (Credit/Debit Card or e-Wallets).',
      'Bank Transfer': 'Fee: <strong>₱120.00</strong>. Direct deposit to <strong>ISCAG Bank Account</strong>; please enter your transaction reference.'
    };
    hint.innerHTML = hints[channel] || `Fee: <strong>₱120.00</strong>. Please settle via <strong>${channel}</strong>.`;
  }

  // ── Toggle Shahadah Date ──
  function toggleShahadahField(val) {
    const grp = document.getElementById('shahadah-date-group');
    if (grp) {
      if (val === 'Revert') {
        grp.style.display = 'block';
      } else {
        grp.style.display = 'none';
        document.getElementById('reg-shahadah').value = '';
      }
    }
  }

  // ── Toggle Previous School ──
  function togglePreviousSchool(isYes) {
    const grp = document.getElementById('previous-school-group');
    const input = document.getElementById('reg-previous-school');
    if (grp && input) {
      if (isYes) {
        grp.style.opacity = '1';
        grp.style.pointerEvents = 'auto';
        input.disabled = false;
        input.focus();
      } else {
        grp.style.opacity = '0.4';
        grp.style.pointerEvents = 'none';
        input.disabled = true;
        input.value = '';
      }
    }
  }

  // ── Age & Minor Check ──
  function checkMinorStatus(dobVal) {
    if (!dobVal) return;
    const dob = new Date(dobVal);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
      age--;
    }
    const callout = document.getElementById('minor-callout');
    const parentName = document.getElementById('reg-parent-name');
    const parentContact = document.getElementById('reg-parent-contact');

    if (age < 18) {
      if (callout) callout.style.display = 'flex';
      if (parentName) parentName.setAttribute('required', 'required');
      if (parentContact) parentContact.setAttribute('required', 'required');
    } else {
      if (callout) callout.style.display = 'none';
      if (parentName) parentName.removeAttribute('required');
      if (parentContact) parentContact.removeAttribute('required');
    }
  }

  // ── Alert UI Modal Controller ──
  let eduAlertConfirmCallback = null;
  function showEduAlert(options) {
    const {
      title = 'Notice',
      message = '',
      type = 'info',
      detailsHtml = null,
      confirmText = 'Acknowledge',
      cancelText = null,
      onConfirm = null
    } = options;

    const modal = document.getElementById('edu-alert-modal');
    if (!modal) return;

    document.getElementById('edu-alert-stripe').className = 'edu-alert-stripe ' + type;
    document.getElementById('edu-alert-icon').className = 'edu-alert-icon-ring ' + type;
    
    const icons = {
      success: '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>',
      warning: '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
      error: '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>',
      info: '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>'
    };
    document.getElementById('edu-alert-svg').innerHTML = icons[type] || icons.info;

    document.getElementById('edu-alert-title').innerText = title;
    document.getElementById('edu-alert-desc').innerText = message;

    const detailsBox = document.getElementById('edu-alert-details');
    if (detailsHtml) {
      detailsBox.innerHTML = detailsHtml;
      detailsBox.style.display = 'block';
    } else {
      detailsBox.innerHTML = '';
      detailsBox.style.display = 'none';
    }

    const confBtn = document.getElementById('edu-alert-confirm-btn');
    confBtn.innerText = confirmText;
    eduAlertConfirmCallback = onConfirm;

    const cancelBtn = document.getElementById('edu-alert-cancel-btn');
    if (cancelText) {
      cancelBtn.innerText = cancelText;
      cancelBtn.style.display = 'inline-flex';
    } else {
      cancelBtn.style.display = 'none';
    }

    modal.classList.add('active');
  }

  function closeEduAlert(isConfirm = false) {
    const modal = document.getElementById('edu-alert-modal');
    if (modal) modal.classList.remove('active');
    if (isConfirm && typeof eduAlertConfirmCallback === 'function') {
      const cb = eduAlertConfirmCallback;
      eduAlertConfirmCallback = null;
      cb();
    }
  }

  // ── Submit Registration Form ──
  async function submitRegistrationForm(e) {
    e.preventDefault();
    const form = document.getElementById('female-education-form');
    const submitBtn = document.getElementById('btn-submit-form');

    const fname = document.getElementById('reg-first-name')?.value.trim();
    const lname = document.getElementById('reg-last-name')?.value.trim();
    const dob = document.getElementById('reg-dob')?.value;
    const address = document.getElementById('reg-address')?.value.trim();
    const contact = document.getElementById('reg-contact')?.value.trim();
    const emName = document.getElementById('reg-emergency-name')?.value.trim();
    const emRel = document.getElementById('reg-emergency-rel')?.value.trim();
    const emContact = document.getElementById('reg-emergency-contact')?.value.trim();
    const studentSig = document.getElementById('reg-student-sig')?.value.trim();

    if (!fname || !lname) {
      showEduAlert({
        title: 'Full Name Required',
        message: 'Please provide both your First Name and Last Name as required on the official registration form.',
        type: 'warning',
        confirmText: 'Fill Name',
        onConfirm: () => document.getElementById('reg-first-name')?.focus()
      });
      return;
    }

    if (!dob) {
      showEduAlert({
        title: 'Date of Birth Required',
        message: 'Please specify your date of birth for age verification and class grouping.',
        type: 'warning',
        confirmText: 'Select Date',
        onConfirm: () => document.getElementById('reg-dob')?.focus()
      });
      return;
    }

    if (!address) {
      showEduAlert({
        title: 'Residential Address Required',
        message: 'Please provide your current complete address.',
        type: 'warning',
        confirmText: 'Enter Address',
        onConfirm: () => document.getElementById('reg-address')?.focus()
      });
      return;
    }

    if (!contact) {
      showEduAlert({
        title: 'Contact Number Required',
        message: 'Please enter your primary mobile number so our admissions staff can contact you.',
        type: 'warning',
        confirmText: 'Enter Contact',
        onConfirm: () => document.getElementById('reg-contact')?.focus()
      });
      return;
    }

    if (!emName || !emRel || !emContact) {
      showEduAlert({
        title: 'Emergency Contact Required',
        message: 'Please complete all emergency contact fields (Name, Relationship, and Contact Number).',
        type: 'warning',
        confirmText: 'Complete Emergency Info',
        onConfirm: () => document.getElementById('reg-emergency-name')?.focus()
      });
      return;
    }

    if (!studentSig) {
      showEduAlert({
        title: 'Student Signature Confirmation Required',
        message: 'Please type your full legal name in the Student Signature field to verify your registration declaration.',
        type: 'warning',
        confirmText: 'Sign Form',
        onConfirm: () => document.getElementById('reg-student-sig')?.focus()
      });
      return;
    }

    // Prepare Payload
    const formData = new FormData(form);
    const payload = {};
    formData.forEach((val, key) => payload[key] = val);

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <svg viewBox="0 0 24 24" style="width:18px;height:18px;animation:spin 1s linear infinite;fill:currentColor;"><path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8z"/></svg>
        Submitting Registration...
      `;

      const response = await fetch('<?= url('/user/services/education/female/submit') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const res = await response.json();
      if (res.success) {
        const refCode = res.ref || '#EDU-0001';
        const isOnline = payload.payment_method === 'Online Payment';
        const channelLabel = isOnline
          ? `Online Payment (${payload.payment_channel || 'e-Wallet'}${payload.payment_reference ? ' • Ref: ' + escapeHtml(payload.payment_reference) : ''})`
          : 'Cash (Direct to ISCAG Office)';

        const detailsHtml = `
          <div style="margin-bottom:6px;"><strong>Reference No:</strong> <span style="font-family:monospace; color:#15803d; font-weight:800;">${refCode}</span></div>
          <div style="margin-bottom:6px;"><strong>Batch:</strong> Batch 08 • Da'wah Female Section</div>
          <div style="margin-bottom:6px;"><strong>Student Name:</strong> ${escapeHtml(fname)} ${escapeHtml(lname)}</div>
          <div style="margin-bottom:6px;"><strong>Registration Fee:</strong> <span style="font-weight:700; color:#0f172a;">₱120.00</span></div>
          <div><strong>Payment Mode:</strong> ${channelLabel}</div>
        `;

        showEduAlert({
          title: 'Alhamdulillah! Registration Submitted',
          message: 'Your registration has been officially received and submitted to Sister Khadijah Dimaano and IDFS Staff for review.',
          type: 'success',
          detailsHtml: detailsHtml,
          confirmText: 'View Registration Slip',
          cancelText: 'Stay on Page',
          onConfirm: () => {
            payload.id = res.id;
            payload.created_at = new Date().toISOString();
            openSlipModal(payload);
          }
        });

        // Reset submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
          Submit Registration Form
        `;
      } else {
        showEduAlert({
          title: 'Submission Failed',
          message: res.message || 'Could not process your registration. Please check your entries and try again.',
          type: 'error'
        });
        submitBtn.disabled = false;
        submitBtn.innerText = 'Submit Registration Form';
      }
    } catch (err) {
      console.error(err);
      showEduAlert({
        title: 'Connection Error',
        message: 'An unexpected network error occurred while submitting your registration. Please check your internet connection.',
        type: 'error'
      });
      submitBtn.disabled = false;
      submitBtn.innerText = 'Submit Registration Form';
    }
  }

  // ── Open Official Printable Registration Document ──
  function openSlipModal(data) {
    if (!data) return;
    const refPad = String(data.id || '1').padStart(4, '0');
    const fullName = `${data.first_name || ''} ${data.middle_initial ? data.middle_initial + ' ' : ''}${data.last_name || ''}`.trim() || 'Student';
    const dateFormatted = data.created_at ? new Date(data.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const isRevert = (data.muslim_type || 'Revert').toLowerCase() === 'revert';
    const studiedYes = (data.studied_islamic_before || 'no').toLowerCase() === 'yes';
    const isCash = (data.payment_method || 'Cash').toLowerCase() === 'cash';
    const channelText = (!isCash && data.payment_channel) ? ` (${data.payment_channel})` : '';

    // Fee Slip
    document.getElementById('doc-fee-batch').innerText = data.batch_no || 'BATCH 08';
    document.getElementById('doc-fee-name').innerText = fullName;
    document.getElementById('doc-fee-date').innerText = dateFormatted;
    
    // Course resolution & dynamic course checklist
    const courseRaw = String(data.level || data.program_name || 'Level 1 — Basic Arabic & Tajweed').trim();
    const cl = courseRaw.toLowerCase();

    let selCourse = 1;
    if (cl.includes('special') || cl.includes('revert')) {
      selCourse = 4;
    } else if (cl.includes('level 3') || cl.includes('tahfidh') || cl.includes('advanced')) {
      selCourse = 3;
    } else if (cl.includes('level 2') || cl.includes('intermediate') || cl.includes('fiqh')) {
      selCourse = 2;
    } else {
      selCourse = 1;
    }

    if (document.getElementById('doc-fee-level')) {
      document.getElementById('doc-fee-level').innerText = courseRaw;
    }
    if (document.getElementById('doc-modal-course-badge')) {
      document.getElementById('doc-modal-course-badge').innerText = courseRaw;
    }

    const chk1 = document.getElementById('doc-course-chk-1');
    const chk2 = document.getElementById('doc-course-chk-2');
    const chk3 = document.getElementById('doc-course-chk-3');
    const chk4 = document.getElementById('doc-course-chk-4');
    if (chk1) chk1.innerText = selCourse === 1 ? '[✓]' : '[ ]';
    if (chk2) chk2.innerText = selCourse === 2 ? '[✓]' : '[ ]';
    if (chk3) chk3.innerText = selCourse === 3 ? '[✓]' : '[ ]';
    if (chk4) chk4.innerText = selCourse === 4 ? '[✓]' : '[ ]';

    const cell1 = document.getElementById('doc-course-cell-1');
    const cell2 = document.getElementById('doc-course-cell-2');
    const cell3 = document.getElementById('doc-course-cell-3');
    const cell4 = document.getElementById('doc-course-cell-4');
    if (cell1) cell1.style.background = selCourse === 1 ? '#e8f5e9' : '#ffffff';
    if (cell2) cell2.style.background = selCourse === 2 ? '#e8f5e9' : '#ffffff';
    if (cell3) cell3.style.background = selCourse === 3 ? '#e8f5e9' : '#ffffff';
    if (cell4) cell4.style.background = selCourse === 4 ? '#e8f5e9' : '#ffffff';

    if (document.getElementById('doc-fee-price')) {
      document.getElementById('doc-fee-price').innerText = '₱ ' + (parseFloat(data.fee_amount || 120).toFixed(2));
    }
    document.getElementById('doc-pay-cash-box').innerText = isCash ? '[✓]' : '[ ]';
    document.getElementById('doc-pay-online-box').innerText = !isCash ? '[✓]' : '[ ]';
    if (document.getElementById('doc-pay-channel-badge')) {
      document.getElementById('doc-pay-channel-badge').innerText = channelText;
    }

    // Main Registration Form
    document.getElementById('doc-last-name').innerText = data.last_name || '—';
    document.getElementById('doc-first-name').innerText = data.first_name || '—';
    document.getElementById('doc-mi').innerText = data.middle_initial || '—';
    document.getElementById('doc-muslim-name').innerText = data.muslim_name || '—';
    document.getElementById('doc-is-revert').innerText = isRevert ? '✓' : '—';
    document.getElementById('doc-is-born').innerText = !isRevert ? '✓' : '—';
    
    document.getElementById('doc-dob').innerText = data.birthdate ? new Date(data.birthdate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
    document.getElementById('doc-shahadah').innerText = data.date_of_shahadah ? new Date(data.date_of_shahadah).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
    document.getElementById('doc-address').innerText = data.address || '—';
    document.getElementById('doc-contact').innerText = data.contact_number || '—';
    document.getElementById('doc-facebook').innerText = data.facebook_account || '—';

    document.getElementById('doc-study-yes').innerText = studiedYes ? '✓' : ' ';
    document.getElementById('doc-study-no').innerText = !studiedYes ? '✓' : ' ';
    document.getElementById('doc-where').innerText = data.previous_school || 'N/A';

    document.getElementById('doc-em-name').innerText = data.emergency_name || '—';
    document.getElementById('doc-em-rel').innerText = data.emergency_relationship || '—';
    document.getElementById('doc-em-address').innerText = data.emergency_address || '—';
    document.getElementById('doc-em-contact').innerText = data.emergency_contact || '—';

    document.getElementById('doc-par-name').innerText = data.parent_name || 'N/A';
    document.getElementById('doc-par-contact').innerText = data.parent_contact || 'N/A';
    document.getElementById('doc-par-address').innerText = data.parent_address || 'N/A';

    document.getElementById('doc-sig-guardian').innerText = data.guardian_signature || (data.parent_name || '—');
    document.getElementById('doc-sig-student').innerText = data.student_signature || fullName;

    document.getElementById('slip-modal').style.display = 'flex';
  }

  function closeSlipModal() {
    document.getElementById('slip-modal').style.display = 'none';
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  // Pre-fill / initial checks
  document.addEventListener('DOMContentLoaded', () => {
    const dobInput = document.getElementById('reg-dob');
    if (dobInput && dobInput.value) {
      checkMinorStatus(dobInput.value);
    }
  });
</script>
</body>
</html>
