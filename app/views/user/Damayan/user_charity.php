<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/CharityProgram.php';

$charityProgramModel = new CharityProgram();
$currentTenantId = (int)($_SESSION['user_id'] ?? 0);
$myDonations = $charityProgramModel->getDonationsByTenant($currentTenantId);

$totalDonated = array_reduce($myDonations, function($acc, $curr) {
    return $acc + ($curr['status'] !== 'Cancelled' ? (float)$curr['amount'] : 0);
}, 0);

$liveStats = $charityProgramModel->getDashboardStats();
$programsSummary = $charityProgramModel->getProgramsSummary();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Charity & Donation</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Source+Sans+3:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-green: #0f5c3a;
      --primary-dark: #0a462b;
      --primary-light: #176b45;
      --primary-soft: #eaf4ef;
      --accent-gold: #c79a2b;
      --accent-soft: #fcf6e8;
      --text-dark: #1f2e2a;
      --text-muted: #6f7f78;
      --bg-soft: #f4f6f5;
      --border-light: #e2e8e5;
      --radius-lg: 20px;
      --radius-md: 14px;
      --radius-sm: 8px;
    }

    body {
      font-family: 'Source Sans 3', sans-serif;
      background-color: var(--bg-soft);
      color: var(--text-dark);
      margin: 0;
      padding: 0;
    }

    h1, h2, h3, h4, .serif {
      font-family: 'Lora', serif;
    }

    /* ── Main Content Container ── */
    .charity-container {
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
      box-sizing: border-box;
    }

    /* ── Breadcrumb Bar ── */
    .charity-breadcrumb {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8rem;
      color: var(--text-muted);
      margin-bottom: 24px;
      background: white;
      padding: 6px 14px;
      border-radius: 999px;
      border: 1px solid var(--border-light);
    }
    .charity-breadcrumb a {
      color: var(--primary-green);
      text-decoration: none;
      font-weight: 700;
    }
    .charity-breadcrumb .sep {
      opacity: 0.5;
    }
    .charity-breadcrumb .current {
      color: var(--text-dark);
      font-weight: 600;
    }

    /* ── Hero Carousel ── */
    .charity-hero {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      margin-bottom: 36px;
      min-height: 410px;
      display: flex;
      align-items: center;
      background: #0f5c3a;
      box-shadow: 0 16px 36px rgba(15, 92, 58, 0.15);
    }
    
    .hero-carousel-bg { 
      position: absolute; 
      inset: 0; 
      z-index: 1; 
    }
    .hero-carousel-bg::after {
      content: ''; 
      position: absolute; 
      inset: 0;
      background: linear-gradient(to right, rgba(15, 92, 58, 0.95) 0%, rgba(15, 92, 58, 0.6) 55%, rgba(0,0,0,0.3) 100%);
      z-index: 2;
    }

    .carousel-track { 
      display: flex; 
      width: 300%; 
      height: 100%; 
      transition: transform 1.2s cubic-bezier(0.65, 0, 0.35, 1); 
    }
    .carousel-slide { 
      width: 33.333%; 
      height: 100%; 
      flex-shrink: 0; 
    }
    .carousel-slide img { 
      width: 100%; 
      height: 100%; 
      object-fit: cover; 
      filter: saturate(1.1); 
    }

    .hero-content-overlay {
      position: relative; 
      z-index: 3; 
      color: white; 
      padding: 60px 48px;
      height: 100%; 
      display: flex; 
      flex-direction: column; 
      justify-content: center;
      max-width: 680px;
    }

    .hero-tag {
      display: inline-flex; 
      align-items: center; 
      gap: 8px; 
      padding: 6px 16px;
      background: rgba(199, 154, 43, 0.22); 
      border: 1px solid rgba(199, 154, 43, 0.45);
      border-radius: 100px; 
      font-size: 0.78rem; 
      font-weight: 800; 
      color: #ffd875;
      text-transform: uppercase; 
      letter-spacing: 0.1em; 
      margin-bottom: 20px; 
      width: fit-content;
    }
    .hero-tag svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
    }

    .hero-content-overlay h2 {
      font-size: clamp(2rem, 1.8rem + 1.2vw, 2.75rem); 
      line-height: 1.15; 
      font-weight: 700; 
      margin: 0 0 16px 0; 
      color: white;
    }
    .hero-content-overlay p {
      font-size: 1.05rem; 
      opacity: 0.92; 
      max-width: 540px; 
      margin: 0 0 28px 0; 
      line-height: 1.6;
    }

    .hero-actions { 
      display: flex; 
      gap: 14px; 
      align-items: center; 
      flex-wrap: wrap;
    }
    .btn-hero {
      background: var(--accent-gold); 
      color: #fff; 
      padding: 12px 26px;
      border-radius: 12px; 
      font-weight: 700; 
      font-size: 0.95rem;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 4px 15px rgba(199, 154, 43, 0.4);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-hero:hover { 
      transform: translateY(-2px); 
      box-shadow: 0 8px 24px rgba(199, 154, 43, 0.55); 
      color: #fff;
    }
    .btn-hero-outline {
      background: rgba(255, 255, 255, 0.12); 
      border: 1px solid rgba(255, 255, 255, 0.35);
      color: white; 
      padding: 12px 24px; 
      border-radius: 12px; 
      font-weight: 700;
      font-size: 0.95rem;
      text-decoration: none; 
      backdrop-filter: blur(8px);
      transition: all 0.25s;
    }
    .btn-hero-outline:hover { 
      background: rgba(255, 255, 255, 0.25); 
      border-color: white; 
      color: white;
    }

    .carousel-dots {
      position: absolute; 
      bottom: 24px; 
      right: 36px; 
      z-index: 4;
      display: flex; 
      gap: 8px;
    }
    .dot {
      width: 10px; 
      height: 10px; 
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.45); 
      cursor: pointer;
      transition: all 0.3s;
    }
    .dot.active { 
      width: 28px; 
      border-radius: 20px; 
      background: var(--accent-gold); 
    }

    /* ── Non-Stop Projects Ticker Carousel ── */
    .projects-carousel-container { 
      position: relative; 
      margin-top: 48px;
      margin-bottom: 36px; 
      overflow: hidden; 
    }
    .projects-section-header {
      margin-bottom: 20px;
    }
    .projects-section-header h3 {
      font-size: 1.55rem;
      color: var(--primary-green);
      margin: 0 0 6px 0;
      font-weight: 700;
    }
    .projects-section-header p {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin: 0;
    }
    .projects-viewport { 
      overflow: hidden; 
      border-radius: 20px; 
      padding: 12px 0; 
    }
    .projects-track { 
      display: flex; 
      gap: 24px; 
      width: max-content;
      animation: ticker-scroll 35s linear infinite;
    }
    .projects-track:hover { 
      animation-play-state: paused; 
    }
    .story-card {
      flex: 0 0 320px;
      background: white;
      border-radius: var(--radius-md);
      overflow: hidden;
      border: 1px solid var(--border-light);
      box-shadow: 0 4px 16px rgba(0,0,0,0.03);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .story-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 24px rgba(15, 92, 58, 0.1);
    }
    .story-image { 
      height: 180px; 
      overflow: hidden; 
    }
    .story-image img { 
      width: 100%; 
      height: 100%; 
      object-fit: cover; 
      transition: transform 0.6s ease; 
    }
    .story-card:hover .story-image img { 
      transform: scale(1.06); 
    }
    .story-content { 
      padding: 20px 22px; 
    }
    .story-content .date { 
      font-size: 0.72rem; 
      font-weight: 800; 
      color: var(--accent-gold); 
      text-transform: uppercase; 
      letter-spacing: 0.05em;
      margin-bottom: 8px; 
      display: block; 
    }
    .story-content h4 { 
      font-size: 1.15rem; 
      color: var(--primary-green); 
      margin: 0 0 8px 0; 
      line-height: 1.3; 
      font-weight: 700;
    }
    .story-content p { 
      font-size: 0.85rem; 
      color: var(--text-muted); 
      line-height: 1.5; 
      margin: 0 0 14px 0; 
    }
    .story-outcome {
      font-size: 0.78rem; 
      font-weight: 700; 
      color: var(--primary-green);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    @keyframes ticker-scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(calc(-320px * 4 - 24px * 4)); }
    }

    /* ── Two-Column Layout ── */
    .charity-grid-layout {
      display: grid;
      grid-template-columns: minmax(0, 1.15fr) 420px;
      gap: 32px;
      align-items: start;
    }

    /* ── Left Column: Donation Information ── */
    .charity-info-column {
      display: flex;
      flex-direction: column;
      gap: 24px;
      min-width: 0;
    }

    .section-intro h3 {
      font-size: 1.5rem;
      color: var(--primary-green);
      margin: 0 0 8px 0;
      font-weight: 700;
    }
    .section-intro p {
      font-size: 0.95rem;
      color: var(--text-muted);
      margin: 0 0 20px 0;
      line-height: 1.6;
    }

    /* ── Impact Cards (2-Column Clean Grid) ── */
    .impact-cards-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }

    .impact-mini-card {
      background: white;
      border: 1.5px solid var(--border-light);
      border-radius: var(--radius-md);
      padding: 22px;
      cursor: pointer;
      transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
      min-height: 140px;
      box-sizing: border-box;
    }
    .impact-mini-card:hover {
      border-color: var(--primary-green);
      transform: translateY(-3px);
      box-shadow: 0 10px 24px rgba(15, 92, 58, 0.08);
    }
    .impact-mini-card.active {
      border: 2px solid var(--primary-green);
      background: #f4fbf7;
      box-shadow: 0 8px 20px rgba(15, 92, 58, 0.1);
    }

    .impact-card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 8px;
    }
    .impact-mini-card .amt {
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--primary-green);
      line-height: 1.1;
      font-family: 'Lora', serif;
    }
    .impact-tag {
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--primary-soft);
      color: var(--primary-green);
      white-space: nowrap;
    }
    .impact-mini-card.active .impact-tag {
      background: var(--primary-green);
      color: white;
    }
    .impact-mini-card .desc {
      font-size: 0.85rem;
      line-height: 1.5;
      color: var(--text-muted);
      margin: 0;
      font-weight: 500;
    }
    .impact-card-check {
      margin-top: 12px;
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--primary-green);
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* ── Where Your Money Goes Card ── */
    .allocation-card {
      background: white;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-lg);
      padding: 26px 30px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }
    .allocation-card h4 {
      font-size: 1.25rem;
      color: var(--primary-green);
      margin: 0 0 8px 0;
      font-weight: 700;
    }
    .allocation-card p {
      font-size: 0.92rem;
      color: var(--text-muted);
      margin: 0 0 18px 0;
      line-height: 1.5;
    }
    .allocation-bar-wrap {
      height: 14px;
      background: #edf1ef;
      border-radius: 999px;
      display: flex;
      overflow: hidden;
      margin-bottom: 12px;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.06);
    }
    .allocation-bar-programs {
      width: 95%;
      background: linear-gradient(90deg, var(--primary-dark), var(--primary-green));
      transition: width 0.6s ease;
    }
    .allocation-bar-ops {
      width: 5%;
      background: linear-gradient(90deg, var(--accent-gold), #e0b84a);
      transition: width 0.6s ease;
    }
    .allocation-legend {
      display: flex;
      justify-content: space-between;
      font-size: 0.78rem;
      font-weight: 700;
      flex-wrap: wrap;
      gap: 8px;
    }
    .allocation-legend .leg-programs {
      color: var(--primary-green);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .allocation-legend .leg-ops {
      color: var(--accent-gold);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .allocation-note {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-top: 14px;
      padding-top: 14px;
      border-top: 1px dashed var(--border-light);
      line-height: 1.45;
    }

    /* ── Transparency Notice Card ── */
    .transparency-notice-card {
      background: #fafbfb;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-md);
      padding: 20px 24px;
      display: flex;
      align-items: flex-start;
      gap: 14px;
    }
    .transparency-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: var(--primary-soft);
      color: var(--primary-green);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .transparency-icon svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }
    .transparency-text h5 {
      margin: 0 0 4px 0;
      font-size: 0.92rem;
      color: var(--text-dark);
      font-weight: 700;
    }
    .transparency-text p {
      margin: 0;
      font-size: 0.82rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* ── Right Column: Support a Program Form ── */
    .donation-form-card {
      background: white;
      border-radius: var(--radius-lg);
      padding: 30px 28px;
      border: 1px solid var(--border-light);
      box-shadow: 0 10px 32px rgba(15, 92, 58, 0.07);
      position: sticky;
      top: 84px;
      box-sizing: border-box;
    }
    .form-header h4 {
      font-size: 1.35rem;
      color: var(--primary-green);
      margin: 0 0 6px 0;
      font-weight: 700;
    }
    .form-header p {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin: 0 0 22px 0;
      line-height: 1.45;
    }

    .form-group-item {
      margin-bottom: 18px;
    }
    .form-group-label {
      font-size: 0.74rem;
      font-weight: 800;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 6px;
      display: block;
    }
    .form-control-input,
    .form-control-select {
      width: 100%;
      padding: 10px 14px;
      border-radius: var(--radius-sm);
      border: 1.5px solid var(--border-light);
      font-family: inherit;
      font-size: 0.88rem;
      color: var(--text-dark);
      background: white;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control-input:focus,
    .form-control-select:focus {
      border-color: var(--primary-green);
      box-shadow: 0 0 0 3px rgba(15, 92, 58, 0.1);
    }

    /* Preset Amount Buttons */
    .preset-amount-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
      margin-bottom: 12px;
    }
    .amt-btn {
      padding: 11px 8px;
      border-radius: var(--radius-sm);
      border: 1.5px solid var(--border-light);
      background: white;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--text-dark);
      cursor: pointer;
      transition: all 0.18s ease;
      text-align: center;
      font-family: inherit;
    }
    .amt-btn:hover {
      border-color: var(--primary-green);
      background: var(--primary-soft);
      color: var(--primary-green);
    }
    .amt-btn.active {
      background: var(--primary-green);
      color: white;
      border-color: var(--primary-green);
      box-shadow: 0 2px 8px rgba(15, 92, 58, 0.2);
    }

    /* Custom Amount Input Wrapper */
    .amount-input-wrapper {
      position: relative;
    }
    .amount-input-wrapper .currency-symbol {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-weight: 800;
      font-size: 1.15rem;
      color: var(--primary-green);
      pointer-events: none;
    }
    .amount-input-wrapper input {
      padding-left: 32px;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--primary-green);
    }

    /* QR Code Box */
    .qr-transfer-box {
      background: #fafbfb;
      padding: 16px;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-light);
      margin-bottom: 20px;
      text-align: center;
    }
    .qr-badge-title {
      font-size: 0.72rem;
      font-weight: 800;
      color: var(--primary-green);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 10px;
    }
    .qr-image {
      width: 140px;
      height: 140px;
      border-radius: 10px;
      display: block;
      margin: 0 auto 10px;
      border: 1px solid var(--border-light);
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .qr-caption {
      font-size: 0.76rem;
      color: var(--text-muted);
      line-height: 1.45;
      margin: 0;
    }

    /* Submit Donation Button */
    .btn-submit-donation {
      width: 100%;
      padding: 14px 20px;
      border-radius: var(--radius-md);
      border: none;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-green));
      color: white;
      font-size: 1rem;
      font-weight: 800;
      cursor: pointer;
      box-shadow: 0 10px 24px rgba(15, 92, 58, 0.22);
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-family: inherit;
    }
    .btn-submit-donation:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(15, 92, 58, 0.3);
      filter: brightness(1.05);
    }
    .btn-submit-donation:active {
      transform: translateY(0);
    }
    .btn-submit-donation:disabled {
      opacity: 0.65;
      cursor: not-allowed;
      transform: none !important;
      box-shadow: none !important;
    }
    .btn-submit-donation svg {
      width: 18px;
      height: 18px;
      fill: currentColor;
    }

    /* Trust Footnote */
    .trust-footer {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 0.72rem;
      color: var(--text-muted);
      margin-top: 14px;
      text-align: center;
    }
    .trust-footer svg {
      width: 14px;
      height: 14px;
      fill: var(--primary-green);
    }

    /* ── Contribution History Section ── */
    .history-card {
      background: white;
      border-radius: var(--radius-lg);
      padding: 32px 36px;
      border: 1px solid var(--border-light);
      margin-top: 44px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
      box-sizing: border-box;
    }
    .history-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      flex-wrap: wrap;
      gap: 16px;
    }
    .history-header h2 {
      font-size: 1.55rem;
      color: var(--primary-green);
      margin: 0 0 6px 0;
      font-weight: 700;
    }
    .history-header p {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin: 0;
    }
    .total-donated-badge {
      background: var(--primary-green);
      color: white;
      padding: 10px 22px;
      border-radius: 999px;
      font-weight: 800;
      font-size: 1rem;
      letter-spacing: 0.02em;
      box-shadow: 0 4px 12px rgba(15, 92, 58, 0.18);
    }

    .table-container {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .charity-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 0.88rem;
    }
    .charity-table thead tr {
      background: #f7faf8;
    }
    .charity-table th {
      padding: 14px 16px;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-muted);
      border-bottom: 2px solid var(--border-light);
      text-align: left;
      white-space: nowrap;
    }
    .charity-table td {
      padding: 16px;
      border-bottom: 1px solid #f0f4f2;
      vertical-align: middle;
    }
    .charity-table tbody tr:hover {
      background: #fafcfb;
    }
    .charity-table tbody tr:last-child td {
      border-bottom: none;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      white-space: nowrap;
    }
    .status-available { background: #e8f5e9; color: #176b45; }
    .status-partially { background: #fef3c7; color: #b45309; }
    .status-fully     { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #dc2626; }

    .btn-ack-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 12px;
      border-radius: 6px;
      border: 1px solid var(--primary-green);
      color: var(--primary-green);
      font-size: 0.75rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.18s;
      background: white;
    }
    .btn-ack-link:hover {
      background: var(--primary-green);
      color: white;
    }
    .btn-ack-link svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
    }

    /* ── Responsive Breakpoints ── */
    @media (max-width: 1024px) {
      .charity-grid-layout {
        grid-template-columns: 1fr;
        gap: 36px;
      }
      .donation-form-card {
        position: static;
        max-width: 600px;
        margin: 0 auto;
        width: 100%;
      }
    }

    @media (max-width: 768px) {
      .charity-hero {
        min-height: 350px;
      }
      .hero-content-overlay {
        padding: 40px 24px;
      }
      .hero-content-overlay h2 {
        font-size: 1.85rem;
      }
      .carousel-dots {
        bottom: 16px;
        right: 20px;
      }
    }

    @media (max-width: 640px) {
      .hero-content-overlay h2 {
        font-size: 1.65rem;
      }
      .hero-actions {
        flex-direction: column;
        align-items: stretch;
      }
      .btn-hero, .btn-hero-outline {
        justify-content: center;
        text-align: center;
      }
      .impact-cards-grid {
        grid-template-columns: 1fr;
      }
      .preset-amount-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .history-card {
        padding: 24px 18px;
      }
      .allocation-card {
        padding: 20px;
      }
      .donation-form-card {
        padding: 22px 18px;
      }
    }
  </style>
</head>
<body>
<div class="app-wrapper">

  <!-- Sidebar -->
  <?php 
    $active_page = 'charity'; 
    include BASE_PATH . '/app/views/user/sidebar.php'; 
  ?>

  <!-- Main Content -->
  <div class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Damayan Charity</div>
        <div class="top-bar-subtitle">Transforming lives through Islamic compassionate giving</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
      </div>
    </div>

    <!-- Page Body -->
    <div class="page-body">
      <div class="charity-container">

        <!-- Breadcrumb Bar -->
        <div class="charity-breadcrumb">
          <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
          <span class="sep">›</span>
          <span class="current">Damayan Charity</span>
        </div>

        <!-- ═══ A. Hero Section (Centered) ═══ -->
        <!-- ═══ Hero Section (Carousel) ═══ -->
        <section class="charity-hero" id="charityHero" aria-label="Damayan Charity Hero">
          <div class="hero-carousel-bg">
            <div class="carousel-track" id="carousel-track">
              <div class="carousel-slide"><img src="<?= asset('assets/damayanHero.png') ?>" alt="Support for families in need" /></div>
              <div class="carousel-slide"><img src="<?= asset('assets/damayanHero2.png') ?>" alt="Islamic educational support" /></div>
              <div class="carousel-slide"><img src="<?= asset('assets/damayanHero3.png') ?>" alt="Community medical mission" /></div>
            </div>
          </div>

          <div class="hero-content-overlay">
            <div class="hero-tag">
              <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Impact is our Mission
            </div>
            <h2>Transforming <br>Compassion into Action</h2>
            <p>"Good deeds should be done with good intention, not for attention." — ISCAG Damayan Project</p>
            
            <div class="hero-actions">
              <a href="#donate-section" class="btn-hero">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                Make a Donation
              </a>
              <a href="<?= url('/user/services/charity/request') ?>" class="btn-hero-outline">Apply for Assistance</a>
            </div>

            <div class="carousel-dots">
              <div class="dot active" onclick="gotoHeroSlide(0)"></div>
              <div class="dot" onclick="gotoHeroSlide(1)"></div>
              <div class="dot" onclick="gotoHeroSlide(2)"></div>
            </div>
          </div>
        </section>

        <!-- ═══ Two-Column Layout (Information Left | Form Right) ═══ -->
        <div class="charity-grid-layout">

          <!-- ── Left Column: Donation Information ── -->
          <div class="charity-info-column">
            
            <div class="section-intro">
              <h3>How Your Donation Helps</h3>
              <p>Every peso you contribute goes directly to programs that provide spiritual, financial, and emotional support to those who need it most within the ISCAG community.</p>

              <!-- ── B. Donation Impact Cards (2-Column Grid) ── -->
              <div class="impact-cards-grid">
                
                <!-- Card 1: ₱500 -->
                <article class="impact-mini-card active" 
                         data-amount="500" 
                         data-program="Ramadan & Food Security" 
                         onclick="selectImpactCard(this, 500, 'Ramadan & Food Security')">
                  <div class="impact-card-top">
                    <span class="amt">₱500</span>
                    <span class="impact-tag">Food Security</span>
                  </div>
                  <p class="desc">Provides a complete food pack for a family of four for one week.</p>
                  <div class="impact-card-check">✓ Selected</div>
                </article>

                <!-- Card 2: ₱2,500 -->
                <article class="impact-mini-card" 
                         data-amount="2500" 
                         data-program="Educational Support" 
                         onclick="selectImpactCard(this, 2500, 'Educational Support')">
                  <div class="impact-card-top">
                    <span class="amt">₱2,500</span>
                    <span class="impact-tag">Student Aid</span>
                  </div>
                  <p class="desc">Supports a student's educational supplies for a full semester.</p>
                  <div class="impact-card-check" style="display:none;">✓ Selected</div>
                </article>

                <!-- Card 3: ₱10,000 -->
                <article class="impact-mini-card" 
                         data-amount="10000" 
                         data-program="Burial Assistance (Damayan Aid)" 
                         onclick="selectImpactCard(this, 10000, 'Burial Assistance (Damayan Aid)')">
                  <div class="impact-card-top">
                    <span class="amt">₱10,000</span>
                    <span class="impact-tag">Burial Aid</span>
                  </div>
                  <p class="desc">Subsidizes Islamic burial services for an underprivileged family.</p>
                  <div class="impact-card-check" style="display:none;">✓ Selected</div>
                </article>

                <!-- Card 4: Custom Amount -->
                <article class="impact-mini-card" 
                         data-amount="custom" 
                         data-program="General Welfare & Relief" 
                         onclick="selectImpactCard(this, 'custom', 'General Welfare & Relief')">
                  <div class="impact-card-top">
                    <span class="amt" style="font-size:1.25rem;">Custom</span>
                    <span class="impact-tag">General Relief</span>
                  </div>
                  <p class="desc">Allows the donor to enter a preferred amount for community welfare.</p>
                  <div class="impact-card-check" style="display:none;">✓ Selected</div>
                </article>

              </div>
            </div>

            <!-- ── D. Where Your Money Goes Card ── -->
            <div class="allocation-card">
              <h4>Where Your Money Goes</h4>
              <p>We pride ourselves on our lean operations. 95% of every donation goes directly to field programs.</p>
              
              <div class="allocation-bar-wrap" title="Program Services: 95% | Operational Support: 5%">
                <div class="allocation-bar-programs"></div>
                <div class="allocation-bar-ops"></div>
              </div>

              <div class="allocation-legend">
                <span class="leg-programs">● Program Services (95%)</span>
                <span class="leg-ops">● Operational Support (5%)</span>
              </div>

              <div class="allocation-note">
                All monetary and in-kind resources are audited and allocated directly under the official oversight of the ISCAG Damayan Department.
              </div>
            </div>

            <!-- Transparency / Trust Card -->
            <div class="transparency-notice-card">
              <div class="transparency-icon">
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
              </div>
              <div class="transparency-text">
                <h5>Official Acknowledgment Receipt</h5>
                <p>Every donation receives a unique tracking reference. Once verified by the Damayan staff, an official printable Acknowledgment Receipt is generated in your record.</p>
              </div>
            </div>

          </div>

          <!-- ── Right Column: Support a Program Form ── -->
          <div class="charity-form-column" id="donate-section">
            <div class="donation-form-card">
              
              <div class="form-header">
                <h4>Support a Program</h4>
                <p>Your contribution will be officially recorded in the ISCAG Charity Registry.</p>
              </div>

              <form id="userDonationForm" onsubmit="submitOnlineDonation(event)">
                
                <!-- Target Charity Program -->
                <div class="form-group-item">
                  <label for="donation-program" class="form-group-label">Target Charity Program</label>
                  <select id="donation-program" name="target_program" required class="form-control-select">
                    <option value="Ramadan & Food Security">Food Security & Ramadan Ration</option>
                    <option value="Educational Support">Orphan & Student Education Support</option>
                    <option value="Burial Assistance (Damayan Aid)">Burial Assistance & Shroud Fund (Damayan)</option>
                    <option value="General Welfare & Relief">General Welfare & Community Relief</option>
                    <option value="Da'wah Outreach & New Muslim Welfare">Da'wah Outreach & Convert Relief</option>
                  </select>
                </div>

                <!-- Preset Donation Amount Buttons -->
                <div class="form-group-item">
                  <label class="form-group-label">Select Amount</label>
                  <div class="preset-amount-grid">
                    <button type="button" class="amt-btn active" data-amt="500" onclick="selectPresetAmt(500, this)">₱500</button>
                    <button type="button" class="amt-btn" data-amt="2500" onclick="selectPresetAmt(2500, this)">₱2,500</button>
                    <button type="button" class="amt-btn" data-amt="10000" onclick="selectPresetAmt(10000, this)">₱10,000</button>
                    <button type="button" class="amt-btn" data-amt="custom" onclick="selectPresetAmt('custom', this)">Custom</button>
                  </div>

                  <!-- Amount Input Box -->
                  <div class="amount-input-wrapper">
                    <span class="currency-symbol">₱</span>
                    <input type="number" 
                           id="donation-amount" 
                           name="amount" 
                           min="1" 
                           step="0.01" 
                           value="500" 
                           required 
                           placeholder="0.00" 
                           class="form-control-input"
                           oninput="onAmountManualInput(this.value)" />
                  </div>
                </div>

                <!-- Payment Channel -->
                <div class="form-group-item">
                  <label for="payment-method" class="form-group-label">Payment Channel</label>
                  <select id="payment-method" name="payment_method" class="form-control-select">
                    <option value="GCash">GCash / Maya (E-Wallet)</option>
                    <option value="Bank Transfer">Bank Transfer (BDO / BPI)</option>
                    <option value="Cash">Cash (ISCAG Office)</option>
                  </select>
                </div>

                <!-- Transaction / Reference # -->
                <div class="form-group-item">
                  <label for="payment-ref" class="form-group-label">Transaction / Reference # (Optional)</label>
                  <input type="text" 
                         id="payment-ref" 
                         name="payment_reference" 
                         placeholder="e.g. GCash Ref # 9018471" 
                         class="form-control-input" />
                </div>

                <!-- QR Transfer Code Box -->
                <div class="qr-transfer-box">
                  <div class="qr-badge-title">Scan to Transfer via QR</div>
                  <img src="<?= asset('assets/donation_qr.png') ?>" alt="Donation QR Code" class="qr-image" />
                  <p class="qr-caption">
                    Account: <strong>ISCAG Charity Fund</strong><br>
                    Scan with GCash or Maya, then enter your reference number above.
                  </p>
                </div>

                <!-- Confirm Donation Button -->
                <button type="submit" id="btn-submit-donation" class="btn-submit-donation">
                  <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                  <span id="btn-submit-label">Confirm Donation Record</span>
                </button>

                <div class="trust-footer">
                  <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                  <span>Secure & directly logged into Damayan Management</span>
                </div>

              </form>

            </div>
          </div>

        </div>

        <!-- ═══ Contribution History (Full Width) ═══ -->
        
        <!-- ═══ Completed Projects Carousel Ticker ═══ -->
        <section class="projects-carousel-container" aria-label="Completed Community Projects">
          <div class="projects-section-header">
            <h3>Our Impact in Action</h3>
            <p>A glimpse into the successful programs and community relief initiatives powered by your donations.</p>
          </div>

          <div class="projects-viewport">
            <div class="projects-track">
              <!-- Set 1 -->
              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero2.png') ?>" alt="Ramadan Food Distribution"></div>
                <div class="story-content">
                  <span class="date">Ramadan Relief</span>
                  <h4>Ramadan Food Distribution</h4>
                  <p>Nutritious food packs and staples distributed to indigent families during the blessed month.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    2,000+ Beneficiaries Served
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero3.png') ?>" alt="Medical Welfare Mission"></div>
                <div class="story-content">
                  <span class="date">Healthcare Aid</span>
                  <h4>Community Medical Mission</h4>
                  <p>Free comprehensive health check-ups and maintenance medicine for elders and reverts.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    350+ Patients Treated
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero.png') ?>" alt="Educational Support"></div>
                <div class="story-content">
                  <span class="date">Education</span>
                  <h4>Back-to-School Kits & Aid</h4>
                  <p>Educational supply packs, bags, and semester study assistance provided to orphaned students.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    120 Students Empowered
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/ISCAG2.png') ?>" alt="Damayan Bereavement Assistance"></div>
                <div class="story-content">
                  <span class="date">Damayan Shroud Fund</span>
                  <h4>Burial & Bereavement Relief</h4>
                  <p>Subsidized dignified Islamic burial arrangements, shrouds, and transport for vulnerable families.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    85 Families Assisted
                  </div>
                </div>
              </article>

              <!-- Duplicate Set for Seamless Continuous Ticker -->
              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero2.png') ?>" alt="Ramadan Food Distribution"></div>
                <div class="story-content">
                  <span class="date">Ramadan Relief</span>
                  <h4>Ramadan Food Distribution</h4>
                  <p>Nutritious food packs and staples distributed to indigent families during the blessed month.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    2,000+ Beneficiaries Served
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero3.png') ?>" alt="Medical Welfare Mission"></div>
                <div class="story-content">
                  <span class="date">Healthcare Aid</span>
                  <h4>Community Medical Mission</h4>
                  <p>Free comprehensive health check-ups and maintenance medicine for elders and reverts.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    350+ Patients Treated
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/damayanHero.png') ?>" alt="Educational Support"></div>
                <div class="story-content">
                  <span class="date">Education</span>
                  <h4>Back-to-School Kits & Aid</h4>
                  <p>Educational supply packs, bags, and semester study assistance provided to orphaned students.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    120 Students Empowered
                  </div>
                </div>
              </article>

              <article class="story-card">
                <div class="story-image"><img src="<?= asset('assets/ISCAG2.png') ?>" alt="Damayan Bereavement Assistance"></div>
                <div class="story-content">
                  <span class="date">Damayan Shroud Fund</span>
                  <h4>Burial & Bereavement Relief</h4>
                  <p>Subsidized dignified Islamic burial arrangements, shrouds, and transport for vulnerable families.</p>
                  <div class="story-outcome">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    85 Families Assisted
                  </div>
                </div>
              </article>
            </div>
          </div>
        </section>

        <section class="history-card">
          <div class="history-header">
            <div>
              <h2>Your Contribution History</h2>
              <p>Directly connected to the ISCAG Charity Programs & Allocation Registry.</p>
            </div>
            <div class="total-donated-badge">
              Total Donated: ₱<?= number_format($totalDonated, 2) ?>
            </div>
          </div>

          <div class="table-container">
            <table class="charity-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Date</th>
                  <th>Target Program</th>
                  <th>Amount</th>
                  <th>Payment</th>
                  <th>Status</th>
                  <th style="text-align:right;">Official Receipt</th>
                </tr>
              </thead>
              <tbody id="userDonationsTbody">
                <?php if(empty($myDonations)): ?>
                  <tr>
                    <td colspan="7" style="padding: 36px; text-align: center; color: var(--text-muted);">
                      No recorded contributions yet. Use the form above to make your first donation!
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach($myDonations as $don): ?>
                  <tr>
                    <td style="font-family: monospace; font-weight:700; color:var(--primary-green);">
                      <?= htmlspecialchars($don['donation_ref']) ?>
                    </td>
                    <td style="font-weight: 600; color:var(--text-dark);">
                      <?= date('M d, Y', strtotime($don['date_received'])) ?>
                    </td>
                    <td style="font-weight: 600;">
                      <?= htmlspecialchars(!empty($don['target_program']) ? $don['target_program'] : ($don['remarks'] ?: 'General Welfare & Relief')) ?>
                    </td>
                    <td style="font-weight: 800; color: var(--primary-green);">
                      ₱<?= number_format((float)$don['amount'], 2) ?>
                    </td>
                    <td style="font-size:0.85rem; color:var(--text-muted);">
                      <?= htmlspecialchars($don['payment_method'] ?: 'N/A') ?>
                      <?php if(!empty($don['payment_reference'])): ?>
                        <div style="font-size:0.75rem; font-family:monospace;"><?= htmlspecialchars($don['payment_reference']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php 
                        $st = $don['status'];
                        $stClass = 'status-available';
                        if ($st === 'Partially Allocated') $stClass = 'status-partially';
                        elseif ($st === 'Fully Allocated') $stClass = 'status-fully';
                        elseif ($st === 'Cancelled') $stClass = 'status-cancelled';
                      ?>
                      <span class="status-pill <?= $stClass ?>"><?= htmlspecialchars($st) ?></span>
                    </td>
                    <td style="text-align:right;">
                      <?php if(!empty($don['ack_issued'])): ?>
                        <a href="<?= url('/admin/damayan/charity/donations/print-ack?id=' . $don['id']) ?>" 
                           target="_blank" 
                           class="btn-ack-link"
                           title="Print Official Acknowledgment Receipt">
                          <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                          Acknowledgment
                        </a>
                      <?php else: ?>
                        <span style="font-size:0.75rem; color:var(--text-muted); font-style:italic;">Processing</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

      </div>
    </div>
  </div>
</div>

<script>
  // ── Hero Carousel Logic ──
  const heroTrack = document.getElementById('carousel-track');
  const heroDots = document.querySelectorAll('.carousel-dots .dot');
  let currentHeroSlide = 0;
  let heroTimer = null;

  function gotoHeroSlide(idx) {
    if (!heroTrack) return;
    currentHeroSlide = idx;
    heroTrack.style.transform = `translateX(-${currentHeroSlide * 33.333}%)`;
    heroDots.forEach((d, i) => d.classList.toggle('active', i === currentHeroSlide));
  }

  function nextHeroSlide() {
    currentHeroSlide = (currentHeroSlide + 1) % 3;
    gotoHeroSlide(currentHeroSlide);
  }

  function startHeroTimer() {
    stopHeroTimer();
    heroTimer = setInterval(nextHeroSlide, 5000);
  }

  function stopHeroTimer() {
    if (heroTimer) clearInterval(heroTimer);
  }

  if (heroTrack) {
    startHeroTimer();
    const heroEl = document.getElementById('charityHero');
    if (heroEl) {
      heroEl.addEventListener('mouseenter', stopHeroTimer);
      heroEl.addEventListener('mouseleave', startHeroTimer);
    }
  }

  // ── Impact Card Selection ──
  function selectImpactCard(cardEl, amt, program) {
    // 1. Highlight clicked card and update indicators
    document.querySelectorAll('.impact-mini-card').forEach(c => {
      c.classList.remove('active');
      const check = c.querySelector('.impact-card-check');
      if (check) check.style.display = 'none';
    });
    if (cardEl) {
      cardEl.classList.add('active');
      const check = cardEl.querySelector('.impact-card-check');
      if (check) check.style.display = 'flex';
    }

    // 2. Sync with preset buttons in form
    document.querySelectorAll('.amt-btn').forEach(b => b.classList.remove('active'));
    const targetPresetBtn = document.querySelector(`.amt-btn[data-amt="${amt}"]`);
    if (targetPresetBtn) {
      targetPresetBtn.classList.add('active');
    }

    // 3. Update Amount Input
    const amtInput = document.getElementById('donation-amount');
    if (amt === 'custom') {
      amtInput.focus();
      amtInput.select();
    } else {
      amtInput.value = amt;
    }

    // 4. Update Target Program dropdown if applicable
    if (program) {
      const progSelect = document.getElementById('donation-program');
      for (let i = 0; i < progSelect.options.length; i++) {
        if (progSelect.options[i].value === program) {
          progSelect.selectedIndex = i;
          break;
        }
      }
    }
  }

  // ── Preset Amount Button Selection ──
  function selectPresetAmt(amt, btnEl) {
    // 1. Highlight button
    document.querySelectorAll('.amt-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    // 2. Highlight matching impact card
    document.querySelectorAll('.impact-mini-card').forEach(c => {
      c.classList.remove('active');
      const check = c.querySelector('.impact-card-check');
      if (check) check.style.display = 'none';
    });
    const targetCard = document.querySelector(`.impact-mini-card[data-amount="${amt}"]`);
    if (targetCard) {
      targetCard.classList.add('active');
      const check = targetCard.querySelector('.impact-card-check');
      if (check) check.style.display = 'flex';

      // Update program dropdown to match card's designated program
      const prog = targetCard.getAttribute('data-program');
      if (prog) {
        const progSelect = document.getElementById('donation-program');
        for (let i = 0; i < progSelect.options.length; i++) {
          if (progSelect.options[i].value === prog) {
            progSelect.selectedIndex = i;
            break;
          }
        }
      }
    }

    // 3. Update Amount Input
    const amtInput = document.getElementById('donation-amount');
    if (amt === 'custom') {
      amtInput.focus();
      amtInput.select();
    } else {
      amtInput.value = amt;
    }
  }

  // ── Manual Amount Input Listener ──
  function onAmountManualInput(val) {
    const num = parseFloat(val);
    document.querySelectorAll('.amt-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.impact-mini-card').forEach(c => {
      c.classList.remove('active');
      const check = c.querySelector('.impact-card-check');
      if (check) check.style.display = 'none';
    });

    if (num === 500 || num === 2500 || num === 10000) {
      const btn = document.querySelector(`.amt-btn[data-amt="${num}"]`);
      if (btn) btn.classList.add('active');
      const card = document.querySelector(`.impact-mini-card[data-amount="${num}"]`);
      if (card) {
        card.classList.add('active');
        const check = card.querySelector('.impact-card-check');
        if (check) check.style.display = 'flex';
      }
    } else {
      const customBtn = document.querySelector(`.amt-btn[data-amt="custom"]`);
      if (customBtn) customBtn.classList.add('active');
      const customCard = document.querySelector(`.impact-mini-card[data-amount="custom"]`);
      if (customCard) {
        customCard.classList.add('active');
        const check = customCard.querySelector('.impact-card-check');
        if (check) check.style.display = 'flex';
      }
    }
  }

  // ── Online Donation Form Submission ──
  async function submitOnlineDonation(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit-donation');
    const label = document.getElementById('btn-submit-label');
    const amt = parseFloat(document.getElementById('donation-amount').value || 0);
    const program = document.getElementById('donation-program').value;
    const method = document.getElementById('payment-method').value;
    const ref = document.getElementById('payment-ref').value;

    if (amt <= 0 || isNaN(amt)) {
      showToast('Please enter a valid donation amount greater than ₱0.00.', '#dc2626');
      return;
    }

    // Prevent duplicate clicks
    btn.disabled = true;
    label.textContent = 'Recording Donation...';

    try {
      const res = await fetch('<?= url('/user/services/charity/donate') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          amount: amt,
          target_program: program,
          payment_method: method,
          payment_reference: ref,
          remarks: `Online donation for ${program} via ${method}`
        })
      });

      const data = await res.json();
      if (data.success) {
        showToast(`Jazakumullahu Khayran! Your donation (${data.donation_ref}) has been recorded.`, '#0f5c3a');
        setTimeout(() => window.location.reload(), 1500);
      } else {
        btn.disabled = false;
        label.textContent = 'Confirm Donation Record';
        showToast(data.error || 'Failed to submit donation. Please check your entries.', '#dc2626');
      }
    } catch (err) {
      console.error(err);
      btn.disabled = false;
      label.textContent = 'Confirm Donation Record';
      showToast('A network error occurred. Please try again.', '#dc2626');
    }
  }

  // ── Toast Notification Helper ──
  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = `
      position: fixed;
      top: 24px;
      right: 24px;
      background: ${bg};
      color: white;
      padding: 16px 24px;
      border-radius: 12px;
      z-index: 99999;
      font-weight: 700;
      font-size: 0.95rem;
      box-shadow: 0 10px 32px rgba(0,0,0,0.2);
      max-width: 420px;
      animation: slideInRight 0.3s ease;
      font-family: 'Source Sans 3', sans-serif;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 4500);
  }

  // Keyframes injection for toast
  if (!document.getElementById('toast-keyframes')) {
    const s = document.createElement('style');
    s.id = 'toast-keyframes';
    s.textContent = `
      @keyframes slideInRight { 
        from { transform: translateX(100%); opacity: 0; } 
        to { transform: translateX(0); opacity: 1; } 
      }
    `;
    document.head.appendChild(s);
  }
</script>
</body>
</html>
