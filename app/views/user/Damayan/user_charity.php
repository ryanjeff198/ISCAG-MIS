<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/CharityProgram.php';
require_once BASE_PATH . '/app/models/User.php';

$charityProgramModel = new CharityProgram();
$currentTenantId = (int)($_SESSION['user_id'] ?? 0);
$myDonations = $charityProgramModel->getDonationsByTenant($currentTenantId);

$totalDonated = array_reduce($myDonations, function($acc, $curr) {
    return $acc + (in_array($curr['status'], ['Available', 'Partially Allocated', 'Fully Allocated']) ? (float)$curr['amount'] : 0);
}, 0);

$liveStats = $charityProgramModel->getDashboardStats();
$programsSummary = $charityProgramModel->getProgramsSummary();

$userModel = new User();
$dbUser = $userModel->findById($currentTenantId);
$donorFullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?: ($_SESSION['name'] ?? 'ISCAG Community Member');
$donorContact = $dbUser['contactnum'] ?? ($dbUser['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Charity &amp; Donation</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>?v=<?= time() ?>" />
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
      background-color: var(--content-bg, #f4f6f5);
      color: var(--text-dark);
      margin: 0;
      padding: 0;
    }

    h1, h2, h3, h4, h5, .serif {
      font-family: 'Lora', serif;
    }

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
    .charity-breadcrumb .sep { opacity: 0.5; }
    .charity-breadcrumb .current { color: var(--text-dark); font-weight: 600; }

    /* ── Hero Carousel ── */
    .charity-hero {
      position: relative;
      border-radius: var(--radius-lg);
      overflow: hidden;
      margin-bottom: 36px;
      min-height: 400px;
      display: flex;
      align-items: center;
      background: #0f5c3a;
      box-shadow: 0 16px 36px rgba(15, 92, 58, 0.15);
    }
    .hero-carousel-bg { position: absolute; inset: 0; z-index: 1; }
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
    .carousel-slide { width: 33.333%; height: 100%; flex-shrink: 0; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; filter: saturate(1.1); }
    .hero-content-overlay {
      position: relative;
      z-index: 3;
      color: white;
      padding: 56px 44px;
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
      margin-bottom: 18px;
      width: fit-content;
    }
    .hero-tag svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
      flex-shrink: 0;
      display: inline-block;
      vertical-align: middle;
    }
    .hero-content-overlay h2 {
      font-size: clamp(1.8rem, 1.6rem + 1.2vw, 2.6rem);
      line-height: 1.15;
      font-weight: 700;
      margin: 0 0 14px 0;
      color: white;
    }
    .hero-content-overlay p {
      font-size: 1.02rem;
      opacity: 0.92;
      max-width: 520px;
      margin: 0 0 24px 0;
      line-height: 1.6;
    }
    .hero-actions { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }
    .btn-hero {
      background: var(--accent-gold);
      color: #fff;
      padding: 12px 24px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.95rem;
      text-decoration: none;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(199, 154, 43, 0.4);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-hero:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(199, 154, 43, 0.55); color: #fff; }
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
    .dot.active { width: 28px; border-radius: 20px; background: var(--accent-gold); }

    /* ── Section Header ── */
    .section-header-box {
      text-align: center;
      margin-bottom: 28px;
    }
    .section-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--primary-soft);
      color: var(--primary-green);
      padding: 4px 14px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
    }
    .section-eyebrow svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
      flex-shrink: 0;
      display: inline-block;
      vertical-align: middle;
    }
    .section-header-title {
      font-family: 'Lora', serif;
      font-size: 1.85rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 8px;
    }
    .section-header-subtitle {
      font-size: 0.95rem;
      color: var(--text-muted);
      max-width: 620px;
      margin: 0 auto;
      line-height: 1.5;
    }

    /* ── Step 1: Donation Program Cards Grid ── */
    .programs-card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 22px;
      margin-bottom: 36px;
    }
    .program-card {
      background: white;
      border-radius: var(--radius-md);
      border: 2px solid var(--border-light);
      overflow: hidden;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
      display: flex;
      flex-direction: column;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }
    .program-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(15, 92, 58, 0.09);
      border-color: #a0cbb5;
    }
    .program-card.selected-card {
      border-color: var(--primary-green);
      box-shadow: 0 10px 32px rgba(15, 92, 58, 0.16);
      background: #fbfdfc;
    }

    .program-card-img-wrap {
      position: relative;
      width: 100%;
      height: 165px;
      overflow: hidden;
      background: #eef2f0;
    }
    .program-card-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }
    .program-card:hover .program-card-img {
      transform: scale(1.04);
    }
    .program-card-tag {
      position: absolute;
      top: 10px;
      left: 10px;
      background: rgba(15, 92, 58, 0.92);
      backdrop-filter: blur(4px);
      color: white;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 20px;
      letter-spacing: 0.04em;
    }
    .program-card-badge-selected {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--accent-gold);
      color: white;
      font-size: 0.75rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 20px;
      display: none;
      align-items: center;
      gap: 4px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .program-card.selected-card .program-card-badge-selected {
      display: inline-flex;
    }

    .program-card-body {
      padding: 18px;
      display: flex;
      flex-direction: column;
      flex: 1;
    }
    .program-card-title {
      font-family: 'Lora', serif;
      font-size: 1.18rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 6px 0;
    }
    .program-card-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.45;
      margin: 0 0 16px 0;
      min-height: 42px;
    }

    .program-amounts-title {
      font-size: 0.73rem;
      font-weight: 700;
      color: var(--text-dark);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 8px;
    }
    .program-amounts-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 7px;
      margin-bottom: 10px;
    }
    .card-amt-btn {
      padding: 8px 6px;
      border: 1px solid var(--border-light);
      border-radius: 8px;
      background: white;
      color: var(--text-dark);
      font-size: 0.83rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      text-align: center;
    }
    .card-amt-btn:hover {
      border-color: var(--primary-green);
      color: var(--primary-green);
      background: var(--primary-soft);
    }
    .card-amt-btn.active {
      background: var(--primary-green);
      color: white;
      border-color: var(--primary-green);
      box-shadow: 0 2px 6px rgba(15, 92, 58, 0.25);
    }

    .card-custom-amount-box {
      display: none;
      margin-top: 6px;
    }
    .card-custom-input-group {
      position: relative;
      display: flex;
      align-items: center;
    }
    .card-custom-prefix {
      position: absolute;
      left: 10px;
      font-weight: 800;
      color: var(--primary-green);
      font-size: 0.88rem;
    }
    .card-custom-input {
      width: 100%;
      padding: 8px 10px 8px 24px;
      border: 1.5px solid var(--primary-green);
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--text-dark);
      outline: none;
      box-shadow: 0 0 0 2px rgba(15, 92, 58, 0.1);
    }

    /* ── Step 2: Donation Method Section ── */
    .method-section-card {
      background: white;
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-light);
      padding: 30px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
      margin-bottom: 30px;
    }
    .step-banner {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 22px;
    }
    .step-pill {
      background: var(--primary-soft);
      color: var(--primary-green);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .step-heading {
      font-family: 'Lora', serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }

    .method-toggle-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 24px;
    }
    @media (max-width: 768px) {
      .method-toggle-grid { grid-template-columns: 1fr; }
    }
    .method-toggle-btn {
      padding: 16px 18px;
      border: 2px solid var(--border-light);
      border-radius: var(--radius-md);
      background: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 14px;
      text-align: left;
      transition: all 0.25s;
      position: relative;
    }
    .method-toggle-btn:hover {
      border-color: #a0cbb5;
      background: #fafdfb;
    }
    .method-toggle-btn.active {
      border-color: var(--primary-green);
      background: var(--primary-soft);
      box-shadow: 0 4px 14px rgba(15, 92, 58, 0.08);
    }
    .method-btn-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      background: rgba(15, 92, 58, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-green);
      flex-shrink: 0;
    }
    .method-toggle-btn.active .method-btn-icon {
      background: var(--primary-green);
      color: white;
    }
    .method-btn-icon svg { width: 22px; height: 22px; fill: currentColor; }
    .method-btn-title {
      font-weight: 800;
      font-size: 0.95rem;
      color: var(--primary-dark);
      margin-bottom: 3px;
    }
    .method-btn-desc {
      font-size: 0.78rem;
      color: var(--text-muted);
      line-height: 1.35;
    }
    .method-check-circle {
      position: absolute;
      top: 14px;
      right: 14px;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid var(--border-light);
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      color: transparent;
      transition: all 0.2s;
    }
    .method-check-circle svg {
      width: 12px;
      height: 12px;
      fill: currentColor;
      opacity: 0;
      transition: opacity 0.2s;
    }
    .method-toggle-btn.active .method-check-circle {
      border-color: var(--primary-green);
      background: var(--primary-green);
      color: white;
    }
    .method-toggle-btn.active .method-check-circle svg {
      opacity: 1;
    }

    /* ── Direct Donation Details ── */
    .location-banner-card {
      background: #fafbfb;
      border: 1px solid var(--border-light);
      border-left: 4px solid var(--primary-green);
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 20px;
      display: flex;
      align-items: flex-start;
      gap: 14px;
    }
    .location-icon {
      width: 38px;
      height: 38px;
      border-radius: 8px;
      background: var(--primary-soft);
      color: var(--primary-green);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .location-icon svg { width: 20px; height: 20px; fill: currentColor; }
    .location-title { font-weight: 800; font-size: 0.92rem; color: var(--primary-dark); margin-bottom: 4px; }
    .location-address { font-size: 0.85rem; color: var(--text-dark); font-weight: 600; line-height: 1.4; }
    .location-hours { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; }

    .btn-submit-charity {
      background: var(--primary-green);
      color: white;
      border: none;
      padding: 14px 34px;
      border-radius: 12px;
      font-weight: 800;
      font-size: 1rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.25s;
      box-shadow: 0 4px 16px rgba(15, 92, 58, 0.25);
    }
    .btn-submit-charity:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(15, 92, 58, 0.35);
    }
    .btn-submit-charity:disabled {
      opacity: 0.65;
      cursor: not-allowed;
      transform: none;
    }
    .btn-submit-charity svg { width: 18px; height: 18px; fill: currentColor; }

    /* ── Transparency Trust Section ── */
    .charity-info-strip {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 18px;
      margin-bottom: 36px;
    }
    .info-strip-card {
      background: white;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-light);
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .info-strip-card h5 {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 6px 0;
    }
    .info-strip-card p {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin: 0;
      line-height: 1.5;
    }

    /* ── Completed Projects Carousel Ticker ── */
    .projects-carousel-container {
      position: relative;
      background: white;
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-light);
      padding: 28px 24px;
      margin-bottom: 36px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }
    .projects-ticker-wrapper {
      position: relative;
      width: 100%;
      overflow: hidden;
      mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
      -webkit-mask-image: linear-gradient(to right, transparent, black 6%, black 94%, transparent);
    }
    .projects-ticker-track {
      display: flex;
      gap: 20px;
      width: max-content;
      animation: smoothScrollTicker 45s linear infinite;
    }
    .projects-ticker-track:hover { animation-play-state: paused; }
    @keyframes smoothScrollTicker {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    .project-card-slide {
      width: 290px;
      background: var(--bg-soft);
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--border-light);
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
    }
    .project-slide-img { width: 100%; height: 135px; object-fit: cover; display: block; }
    .project-slide-body { padding: 12px 14px; }
    .project-slide-tag { font-size: 0.68rem; font-weight: 800; color: var(--primary-green); text-transform: uppercase; margin-bottom: 4px; }
    .project-slide-title { font-weight: 700; font-size: 0.88rem; color: var(--primary-dark); margin-bottom: 4px; }
    .project-slide-desc { font-size: 0.76rem; color: var(--text-muted); line-height: 1.4; }

    /* ── Contribution History Table ── */
    .history-card {
      background: white;
      border-radius: var(--radius-lg);
      border: 1px solid var(--border-light);
      padding: 24px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
      margin-bottom: 40px;
    }
    .history-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .history-title {
      font-family: 'Lora', serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }
    .history-count-badge {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--text-muted);
      background: var(--bg-soft);
      padding: 4px 12px;
      border-radius: 20px;
    }
    .history-table-wrapper {
      overflow-x: auto;
      border: 1px solid var(--border-light);
      border-radius: 10px;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.84rem;
    }
    .data-table th {
      background: #fafbfb;
      padding: 12px 16px;
      font-weight: 700;
      color: var(--text-muted);
      font-size: 0.74rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      border-bottom: 1px solid var(--border-light);
    }
    .data-table td {
      padding: 14px 16px;
      border-bottom: 1px solid #edf1ef;
      color: var(--text-dark);
    }
    .data-table tr:last-child td { border-bottom: none; }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .st-available { background: #e8f5ed; color: #0f5c3a; }
    .st-pending-verif { background: #fef3c7; color: #92400e; }
    .st-pending-rcpt { background: #e0e7ff; color: #3730a3; }
    .st-rejected { background: #fee2e2; color: #991b1b; }

    @media (max-width: 768px) {
      .programs-card-grid { grid-template-columns: 1fr; }
      .method-toggle-grid { grid-template-columns: 1fr !important; }
    }

    /* ── PayMongo Online Method ── */
    .paymongo-checkout-card {
      background: linear-gradient(135deg, #f0fdf4 0%, #f8faf9 100%);
      border: 2px solid #a7f3d0;
      border-radius: 16px;
      padding: 28px;
      margin-bottom: 0;
    }
    .paymongo-checkout-card .pm-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 18px;
    }
    .paymongo-checkout-card .pm-icon-box {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0f5c3a, #176b45);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 14px rgba(15, 92, 58, 0.25);
    }
    .paymongo-checkout-card .pm-icon-box svg {
      width: 24px;
      height: 24px;
      fill: currentColor;
    }
    .paymongo-checkout-card .pm-title {
      font-family: 'Lora', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--primary-dark);
    }
    .paymongo-checkout-card .pm-subtitle {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-top: 2px;
    }
    .pm-methods-row {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 18px;
    }
    .pm-method-badge {
      font-size: 0.74rem;
      font-weight: 700;
      padding: 5px 12px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .pm-method-badge svg {
      width: 13px;
      height: 13px;
      fill: currentColor;
    }
    .pm-method-badge.qr { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .pm-method-badge.gcash { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .pm-method-badge.maya { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .pm-method-badge.card { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
    .pm-method-badge.grab { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .pm-summary-box {
      background: white;
      border: 1px solid #d4e3dc;
      border-radius: 12px;
      padding: 18px 22px;
      margin-bottom: 18px;
    }
    .pm-summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 0;
    }
    .pm-summary-row .pm-label {
      font-size: 0.82rem;
      color: var(--text-muted);
      font-weight: 600;
    }
    .pm-summary-row .pm-value {
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--primary-dark);
    }
    .pm-summary-row .pm-value.amount {
      font-size: 1.3rem;
      font-family: 'Lora', serif;
      color: var(--primary-green);
    }
    .pm-summary-row.total {
      border-top: 2px solid var(--primary-green);
      margin-top: 8px;
      padding-top: 12px;
    }
    .btn-paymongo-checkout {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #0f5c3a, #176b45);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.25s;
      box-shadow: 0 6px 20px rgba(15, 92, 58, 0.3);
      font-family: inherit;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .btn-paymongo-checkout:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(15, 92, 58, 0.4);
    }
    .btn-paymongo-checkout:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    .btn-paymongo-checkout svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }
    .pm-secure-note {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 14px;
      font-size: 0.78rem;
      color: var(--text-muted);
      justify-content: center;
    }
    .pm-secure-note svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
      flex-shrink: 0;
    }
    .method-toggle-btn .pm-instant-badge {
      position: absolute;
      top: -8px;
      right: -6px;
      background: linear-gradient(135deg, #c79a2b, #dab446);
      color: white;
      font-size: 0.62rem;
      font-weight: 800;
      padding: 2px 8px;
      border-radius: 10px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      box-shadow: 0 2px 6px rgba(199, 154, 43, 0.4);
    }

    /* ── Thank You Pop-up Modal ── */
    .thank-you-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 999999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .thank-you-modal-overlay.show {
      opacity: 1;
      visibility: visible;
    }
    .thank-you-modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(15, 30, 22, 0.72);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }
    .thank-you-modal-card {
      position: relative;
      background: #ffffff;
      border-radius: 20px;
      width: 100%;
      max-width: 580px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 24px 64px rgba(0, 0, 0, 0.35);
      transform: translateY(28px) scale(0.97);
      transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      z-index: 2;
      border: 1px solid rgba(15, 92, 58, 0.18);
    }
    .thank-you-modal-overlay.show .thank-you-modal-card {
      transform: translateY(0) scale(1);
    }
    .thank-you-top-bar {
      height: 5px;
      background: linear-gradient(90deg, #0f5c3a 0%, #2f8a60 50%, #c79a2b 100%);
    }
    .thank-you-close-btn {
      position: absolute;
      top: 14px;
      right: 16px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: none;
      background: rgba(0, 0, 0, 0.05);
      color: var(--text-muted);
      font-size: 1.3rem;
      line-height: 1;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }
    .thank-you-close-btn:hover {
      background: rgba(220, 38, 38, 0.12);
      color: #dc2626;
      transform: rotate(90deg);
    }
    .thank-you-modal-content {
      padding: 36px 32px 30px;
      text-align: center;
    }
    .thank-you-icon-circle {
      width: 68px;
      height: 68px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0f5c3a 0%, #177a4d 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
      box-shadow: 0 8px 24px rgba(15, 92, 58, 0.28);
    }
    .thank-you-icon-circle svg {
      width: 32px;
      height: 32px;
      fill: currentColor;
      animation: pulseHeart 1.8s infinite ease-in-out;
    }
    .thank-you-title {
      font-family: 'Lora', serif;
      font-size: 1.6rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 10px 0;
      line-height: 1.25;
    }
    .thank-you-ref-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--primary-soft);
      border: 1px solid #c2e2d0;
      border-radius: 999px;
      padding: 5px 14px;
      font-size: 0.82rem;
      margin-bottom: 18px;
    }
    .thank-you-ref-badge .ref-label {
      color: var(--text-muted);
      font-weight: 600;
    }
    .thank-you-ref-badge .ref-number {
      font-family: monospace;
      color: var(--primary-green);
      font-size: 0.95rem;
      font-weight: 800;
    }
    .thank-you-quote {
      font-family: 'Lora', serif;
      font-style: italic;
      font-size: 0.98rem;
      color: #0f5c3a;
      background: #fcf6e8;
      border-left: 3px solid var(--accent-gold);
      border-radius: 8px;
      padding: 12px 18px;
      margin: 0 0 20px 0;
      line-height: 1.5;
      text-align: center;
    }
    .thank-you-body {
      font-size: 0.9rem;
      color: var(--text-dark);
      line-height: 1.6;
      text-align: left;
    }
    .thank-you-body p {
      margin: 0 0 12px 0;
    }
    .thank-you-blessing {
      color: #0f5c3a;
      font-weight: 600;
      background: #f0f7f4;
      padding: 10px 14px;
      border-radius: 8px;
      border-left: 3px solid #0f5c3a;
    }
    .thank-you-closing {
      font-size: 0.95rem;
      color: var(--primary-dark);
      text-align: center;
      margin-top: 16px !important;
      margin-bottom: 0 !important;
    }
    .thank-you-footer {
      margin-top: 24px;
      display: flex;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn-thank-you-print {
      background: var(--accent-gold);
      color: white;
      border: none;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 800;
      font-size: 0.96rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.25s;
      box-shadow: 0 4px 14px rgba(199, 154, 43, 0.32);
    }
    .btn-thank-you-print:hover {
      background: #b58920;
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(199, 154, 43, 0.45);
    }
    .btn-thank-you-done {
      background: var(--primary-green);
      color: white;
      border: none;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 800;
      font-size: 0.96rem;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.25s;
      box-shadow: 0 6px 18px rgba(15, 92, 58, 0.25);
    }
    .btn-thank-you-done:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(15, 92, 58, 0.35);
    }

    /* ── Walk-in Donation Slip Modal ── */
    .slip-modal-card {
      position: relative;
      background: #ffffff;
      border-radius: 20px;
      width: 100%;
      max-width: 620px;
      max-height: 92vh;
      overflow-y: auto;
      box-shadow: 0 24px 64px rgba(0, 0, 0, 0.38);
      transform: translateY(28px) scale(0.97);
      transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
      z-index: 2;
      border: 1px solid rgba(15, 92, 58, 0.2);
    }
    .thank-you-modal-overlay.show .slip-modal-card {
      transform: translateY(0) scale(1);
    }
    .slip-paper-ticket {
      background: #ffffff;
      border: 1.5px dashed #0f5c3a;
      border-radius: 14px;
      padding: 20px 22px;
      margin: 16px 0 18px;
      position: relative;
    }
    .slip-brand-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #0f5c3a;
      padding-bottom: 12px;
      margin-bottom: 14px;
      gap: 12px;
    }
    .slip-brand-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .slip-brand-img {
      width: 46px;
      height: 46px;
      border-radius: 8px;
      object-fit: cover;
    }
    .slip-brand-title {
      font-family: 'Lora', serif;
      font-weight: 700;
      font-size: 1.05rem;
      color: #0f5c3a;
      line-height: 1.2;
    }
    .slip-brand-sub {
      font-size: 0.72rem;
      color: var(--text-muted);
      margin-top: 2px;
    }
    .slip-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      background: #f8faf9;
      border-radius: 10px;
      padding: 14px 16px;
      border: 1px solid #e2e8e5;
      text-align: left;
    }
    .slip-item {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }
    .slip-label {
      font-size: 0.68rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .slip-val {
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--primary-dark);
    }
    .slip-val.highlight {
      font-size: 1.28rem;
      color: var(--primary-green);
      font-family: 'Lora', serif;
    }
    @media (max-width: 580px) {
      .slip-grid { grid-template-columns: 1fr; }
      .slip-brand-row { flex-direction: column; align-items: flex-start; }
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

        <!-- ═══ Top Hero Section (Carousel Preserved) ═══ -->
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
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Impact is our Mission
            </div>
            <h2>Transforming <br>Compassion into Action</h2>
            <p>"Good deeds should be done with good intention, not for attention." — ISCAG Damayan Project</p>
            
            <div class="hero-actions">
              <a href="#donation-programs" class="btn-hero">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                Choose a Donation Program
              </a>
            </div>

            <div class="carousel-dots">
              <div class="dot active" onclick="gotoHeroSlide(0)"></div>
              <div class="dot" onclick="gotoHeroSlide(1)"></div>
              <div class="dot" onclick="gotoHeroSlide(2)"></div>
            </div>
          </div>
        </section>

        <!-- ═══ STEP 1: Donation Programs Visual Card Grid ═══ -->
        <section id="donation-programs" class="programs-section">
          <div class="section-header-box">
            <span class="section-eyebrow">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              Choose a Cause to Support
            </span>
            <h3 class="section-header-title">Donation Programs</h3>
            <p class="section-header-subtitle">Select a charitable program below and choose your preferred contribution amount. Amounts are suggested contributions.</p>
          </div>

          <div class="programs-card-grid">

            <!-- ── Card A: Food Donation ── -->
            <article class="program-card selected-card" id="card-food" onclick="selectProgramCard('food')">
              <div class="program-card-img-wrap">
                <img src="<?= asset('assets/ISCAG3.png') ?>" alt="Food Donation relief packs" class="program-card-img" />
                <span class="program-card-tag">Food Assistance</span>
                <span class="program-card-badge-selected"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-1px; margin-right:3px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Selected</span>
              </div>
              <div class="program-card-body">
                <h4 class="program-card-title">Food Donation</h4>
                <p class="program-card-desc">Support food assistance for individuals and families in need.</p>

                <div class="program-amounts-title">Select Donation Amount</div>
                <div class="program-amounts-grid">
                  <button type="button" class="card-amt-btn active" data-prog="food" data-amt="500" onclick="chooseCardAmount('food', 500, this, event)">₱500</button>
                  <button type="button" class="card-amt-btn" data-prog="food" data-amt="1000" onclick="chooseCardAmount('food', 1000, this, event)">₱1,000</button>
                  <button type="button" class="card-amt-btn" data-prog="food" data-amt="2500" onclick="chooseCardAmount('food', 2500, this, event)">₱2,500</button>
                  <button type="button" class="card-amt-btn" data-prog="food" data-amt="custom" onclick="chooseCardAmount('food', 'custom', this, event)">Custom</button>
                </div>
                <div class="card-custom-amount-box" id="custom-box-food">
                  <div class="card-custom-input-group">
                    <span class="card-custom-prefix">₱</span>
                    <input type="number" min="1" step="any" placeholder="Enter custom amount" class="card-custom-input" id="custom-input-food" oninput="handleCustomInput('food', this.value)" onclick="event.stopPropagation()" />
                  </div>
                </div>
              </div>
            </article>

            <!-- ── Card B: Medical Donation ── -->
            <article class="program-card" id="card-medical" onclick="selectProgramCard('medical')">
              <div class="program-card-img-wrap">
                <img src="<?= asset('assets/damayanHero3.png') ?>" alt="Medical Healthcare Assistance" class="program-card-img" />
                <span class="program-card-tag">Healthcare Aid</span>
                <span class="program-card-badge-selected"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-1px; margin-right:3px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Selected</span>
              </div>
              <div class="program-card-body">
                <h4 class="program-card-title">Medical Donation</h4>
                <p class="program-card-desc">Help support medical needs and healthcare assistance for eligible beneficiaries.</p>

                <div class="program-amounts-title">Select Donation Amount</div>
                <div class="program-amounts-grid">
                  <button type="button" class="card-amt-btn" data-prog="medical" data-amt="500" onclick="chooseCardAmount('medical', 500, this, event)">₱500</button>
                  <button type="button" class="card-amt-btn" data-prog="medical" data-amt="1000" onclick="chooseCardAmount('medical', 1000, this, event)">₱1,000</button>
                  <button type="button" class="card-amt-btn" data-prog="medical" data-amt="2500" onclick="chooseCardAmount('medical', 2500, this, event)">₱2,500</button>
                  <button type="button" class="card-amt-btn" data-prog="medical" data-amt="custom" onclick="chooseCardAmount('medical', 'custom', this, event)">Custom</button>
                </div>
                <div class="card-custom-amount-box" id="custom-box-medical">
                  <div class="card-custom-input-group">
                    <span class="card-custom-prefix">₱</span>
                    <input type="number" min="1" step="any" placeholder="Enter custom amount" class="card-custom-input" id="custom-input-medical" oninput="handleCustomInput('medical', this.value)" onclick="event.stopPropagation()" />
                  </div>
                </div>
              </div>
            </article>

            <!-- ── Card C: School Supplies Donation ── -->
            <article class="program-card" id="card-school" onclick="selectProgramCard('school')">
              <div class="program-card-img-wrap">
                <img src="<?= asset('assets/dawahFeatureImage.jpg') ?>" alt="School Supplies and Educational Materials" class="program-card-img" />
                <span class="program-card-tag">Education Aid</span>
                <span class="program-card-badge-selected"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-1px; margin-right:3px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Selected</span>
              </div>
              <div class="program-card-body">
                <h4 class="program-card-title">School Supplies Donation</h4>
                <p class="program-card-desc">Help provide essential learning materials for students in need.</p>

                <div class="program-amounts-title">Select Donation Amount</div>
                <div class="program-amounts-grid">
                  <button type="button" class="card-amt-btn" data-prog="school" data-amt="500" onclick="chooseCardAmount('school', 500, this, event)">₱500</button>
                  <button type="button" class="card-amt-btn" data-prog="school" data-amt="1000" onclick="chooseCardAmount('school', 1000, this, event)">₱1,000</button>
                  <button type="button" class="card-amt-btn" data-prog="school" data-amt="2500" onclick="chooseCardAmount('school', 2500, this, event)">₱2,500</button>
                  <button type="button" class="card-amt-btn" data-prog="school" data-amt="custom" onclick="chooseCardAmount('school', 'custom', this, event)">Custom</button>
                </div>
                <div class="card-custom-amount-box" id="custom-box-school">
                  <div class="card-custom-input-group">
                    <span class="card-custom-prefix">₱</span>
                    <input type="number" min="1" step="any" placeholder="Enter custom amount" class="card-custom-input" id="custom-input-school" oninput="handleCustomInput('school', this.value)" onclick="event.stopPropagation()" />
                  </div>
                </div>
              </div>
            </article>

            <!-- ── Card D: Burial Assistance Donation ── -->
            <article class="program-card" id="card-burial" onclick="selectProgramCard('burial')">
              <div class="program-card-img-wrap">
                <img src="<?= asset('assets/ISCAG2.png') ?>" alt="Damayan Bereavement and Burial Assistance" class="program-card-img" />
                <span class="program-card-tag">Burial Assistance</span>
                <span class="program-card-badge-selected"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-1px; margin-right:3px;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>Selected</span>
              </div>
              <div class="program-card-body">
                <h4 class="program-card-title">Burial Assistance Donation</h4>
                <p class="program-card-desc">Support burial assistance for eligible families in the community.</p>

                <div class="program-amounts-title">Select Donation Amount</div>
                <div class="program-amounts-grid">
                  <button type="button" class="card-amt-btn" data-prog="burial" data-amt="500" onclick="chooseCardAmount('burial', 500, this, event)">₱500</button>
                  <button type="button" class="card-amt-btn" data-prog="burial" data-amt="1000" onclick="chooseCardAmount('burial', 1000, this, event)">₱1,000</button>
                  <button type="button" class="card-amt-btn" data-prog="burial" data-amt="2500" onclick="chooseCardAmount('burial', 2500, this, event)">₱2,500</button>
                  <button type="button" class="card-amt-btn" data-prog="burial" data-amt="custom" onclick="chooseCardAmount('burial', 'custom', this, event)">Custom</button>
                </div>
                <div class="card-custom-amount-box" id="custom-box-burial">
                  <div class="card-custom-input-group">
                    <span class="card-custom-prefix">₱</span>
                    <input type="number" min="1" step="any" placeholder="Enter custom amount" class="card-custom-input" id="custom-input-burial" oninput="handleCustomInput('burial', this.value)" onclick="event.stopPropagation()" />
                  </div>
                </div>
              </div>
            </article>

          </div>
        </section>

        <!-- ═══ STEP 2: Donation Method Form ═══ -->
        <form id="donationForm" onsubmit="handleDonationSubmit(event)" enctype="multipart/form-data">
          <!-- Hidden Inputs for Submission -->
          <input type="hidden" name="target_program" id="form-target-program" value="Food Donation" />
          <input type="hidden" name="amount" id="form-amount" value="500" />
          <input type="hidden" name="donation_method" id="form-method" value="paymongo" />
          <input type="hidden" name="donation_category" id="form-category" value="digital" />

          <!-- ── Step 2 Card: Choose Donation Method ── -->
          <section class="method-section-card" id="donation-method-box">
            <div class="step-banner">
              <span class="step-pill">Step 2</span>
              <h4 class="step-heading">Choose Donation Method</h4>
            </div>

            <!-- 2-Option Method Selector Buttons -->
            <div class="method-toggle-grid">
              <!-- PayMongo Online Option -->
              <button type="button" class="method-toggle-btn active" id="btn-method-paymongo" onclick="setDonationMethod('paymongo')" style="position:relative;">
                <span class="pm-instant-badge">Instant ⚡</span>
                <div class="method-btn-icon">
                  <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-5 0h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm0 2h3v2h-3v-2zm-2 2h2v2h-2v-2z"/></svg>
                </div>
                <div class="method-btn-text">
                  <div class="method-btn-title">PayMongo Online</div>
                  <div class="method-btn-desc">Pay instantly via QR Ph, GCash, Maya, or Card</div>
                </div>
                <div class="method-check-circle"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
              </button>

              <!-- Direct Option -->
              <button type="button" class="method-toggle-btn" id="btn-method-direct" onclick="setDonationMethod('direct')">
                <div class="method-btn-icon">
                  <svg viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                </div>
                <div class="method-btn-text">
                  <div class="method-btn-title">Direct Donation, Face-to-Face</div>
                  <div class="method-btn-desc">In-person cash contribution at the official ISCAG office</div>
                </div>
                <div class="method-check-circle"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
              </button>
            </div>

            <!-- PayMongo Online Donation Section -->
            <div id="method-paymongo-fields">
              <div class="paymongo-checkout-card">
                <div class="pm-header">
                  <div class="pm-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-5 0h2v2h-2v-2zm2 2h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm0 2h3v2h-3v-2zm-2 2h2v2h-2v-2z"/></svg>
                  </div>
                  <div>
                    <div class="pm-title">PayMongo Secure Gateway</div>
                    <div class="pm-subtitle">Instant payment with auto-verified donation status</div>
                  </div>
                </div>

                <div class="pm-methods-row">
                  <span class="pm-method-badge qr">
                    <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5z"/></svg>
                    QR Ph (Scan to Pay)
                  </span>
                  <span class="pm-method-badge gcash">GCash</span>
                  <span class="pm-method-badge maya">Maya</span>
                  <span class="pm-method-badge card">Credit / Debit Card</span>
                  <span class="pm-method-badge grab">GrabPay</span>
                </div>

                <div class="pm-summary-box">
                  <div class="pm-summary-row">
                    <span class="pm-label">Charity Program</span>
                    <span class="pm-value" id="pm-sum-program">Food Donation</span>
                  </div>
                  <div class="pm-summary-row">
                    <span class="pm-label">Donor</span>
                    <span class="pm-value" id="pm-sum-donor"><?= htmlspecialchars($donorFullName) ?></span>
                  </div>
                  <div class="pm-summary-row total">
                    <span class="pm-label">Donation Amount</span>
                    <span class="pm-value amount" id="pm-sum-amount">₱500.00</span>
                  </div>
                </div>

                <button type="button" id="btn-paymongo-pay" class="btn-paymongo-checkout" onclick="submitPayMongoDonation()">
                  <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                  <span id="btn-paymongo-label">Donate ₱<span id="pm-btn-amount">500.00</span> via PayMongo</span>
                </button>

                <div class="pm-secure-note">
                  <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                  You will be securely redirected to PayMongo to complete your donation. No form fields needed — payment is verified instantly.
                </div>

                <div style="margin-top:14px; font-size:0.78rem; color:#0369a1; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:10px 14px; display:flex; align-items:flex-start; gap:8px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="#0284c7" style="flex-shrink:0; margin-top:2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  <div><strong>Instant Verification:</strong> Donations via PayMongo are <strong>auto-verified</strong> and immediately marked as "Available" in the Damayan system. No manual confirmation needed.</div>
                </div>
              </div>
            </div>

            <!-- Direct Donation Walk-in Details & Interactive Map (No Form) -->
            <div id="method-direct-fields" style="display:none;">
              <div class="location-banner-card" style="margin-bottom:16px;">
                <div class="location-icon">
                  <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </div>
                <div style="flex:1;">
                  <div class="location-title">Official ISCAG Location &amp; Handover Desk</div>
                  <div class="location-address"><?= htmlspecialchars(CharityProgram::OFFICIAL_ISCAG_ADDRESS) ?></div>
                  <div class="location-hours" style="margin-top:4px;">Damayan Reception &amp; Cashier Desk • Monday to Sunday, 8:00 AM – 5:00 PM</div>
                  <div style="font-size:0.78rem; color:var(--text-muted); margin-top:4px;">Contact Hotline: (046) 416-2345 • info@iscag.org</div>
                </div>
              </div>

              <!-- Interactive Google Map -->
              <div class="direct-map-card" style="border-radius:14px; overflow:hidden; border:1px solid var(--border-light); box-shadow:0 4px 16px rgba(0,0,0,0.04); margin-bottom:16px; background:white;">
                <div style="background:#f8faf9; padding:10px 16px; border-bottom:1px solid var(--border-light); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                  <span style="font-size:0.75rem; font-weight:700; color:var(--primary-dark); text-transform:uppercase; letter-spacing:0.04em; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--primary-green)"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    ISCAG Islamic Complex Interactive Location Map
                  </span>
                  <a href="https://maps.google.com/?q=ISCAG+Islamic+Complex,+Salitran+1,+Dasmarinas,+Cavite" target="_blank" rel="noopener noreferrer" style="font-size:0.74rem; font-weight:700; color:var(--primary-green); text-decoration:none;">
                    Open in Google Maps ↗
                  </a>
                </div>
                <iframe 
                  title="Official ISCAG Location Map"
                  src="https://maps.google.com/maps?q=ISCAG+Islamic+Complex,+Jose+Abad+Santos+St,+Salitran+1,+Dasmarinas,+Cavite&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                  width="100%" 
                  height="280" 
                  style="border:0; display:block;" 
                  allowfullscreen="" 
                  loading="lazy" 
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
              </div>

              <div style="font-size:0.8rem; color:#78350f; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 14px; display:flex; align-items:flex-start; gap:8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#b45309" style="flex-shrink:0; margin-top:2px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <div><strong>In-Person Walk-in Guide:</strong> Advance online forms are not required. You may visit the Damayan Cashier Desk directly during official office hours. Click below to generate your walk-in donation slip with a printable receipt.</div>
              </div>

              <!-- Direct Submission Action Area -->
              <div class="direct-action-card" style="margin-top:20px; background:#f0f7f4; border:1px solid #c2e2d0; border-radius:14px; padding:18px 22px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                <div style="flex:1; min-width:240px;">
                  <div style="font-weight:800; font-size:0.95rem; color:var(--primary-dark); display:flex; align-items:center; gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-green)"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                    Ready for In-Person Drop-off?
                  </div>
                  <div style="font-size:0.8rem; color:var(--text-muted); margin-top:3px;">
                    Confirm your walk-in intent to receive your printable official acknowledgment slip for the cashier desk.
                  </div>
                </div>
                <button type="button" id="btn-submit-direct" class="btn-submit-charity" onclick="handleDonationSubmit(event)" style="margin:0; box-shadow:0 4px 16px rgba(15,92,58,0.28);">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                  <span id="btn-submit-direct-label">Confirm &amp; Get Walk-in Donation Slip</span>
                </button>
              </div>
            </div>

          </section>


        </form>

        <!-- ═══ Supportive Info / Transparency Cards ═══ -->
        <div class="charity-info-strip">
          <div class="info-strip-card">
            <h5>Lean &amp; Accountable Operations</h5>
            <p>All contributions directly fund program services and beneficiary packages under official ISCAG oversight.</p>
          </div>
          <div class="info-strip-card">
            <h5>Traceable Reference Registry</h5>
            <p>Every transaction receives a unique reference number tracked in your portal history until acknowledgment is issued.</p>
          </div>
          <div class="info-strip-card">
            <h5>Authorized Staff Verification</h5>
            <p>Donations are confirmed by Damayan personnel to ensure complete integrity and accurate financial reporting.</p>
          </div>
        </div>

        <!-- ═══ Non-Stop Completed Projects Carousel Ticker ═══ -->
        <section class="projects-carousel-container" aria-label="Completed Community Projects">
          <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-size:0.75rem; font-weight:800; color:var(--primary-green); text-transform:uppercase; letter-spacing:0.05em;">Our Impact in Action</div>
              <h4 style="font-size:1.25rem; font-weight:700; color:var(--primary-dark); margin:0;">Completed Community Initiatives</h4>
            </div>
            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:700;">Ongoing Community Missions</span>
          </div>

          <div class="projects-ticker-wrapper">
            <div class="projects-ticker-track">
              <!-- Slide 1 -->
              <div class="project-card-slide">
                <img src="<?= asset('assets/ISCAG3.png') ?>" alt="Ramadan Food Package Drive" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Food Assistance</div>
                  <div class="project-slide-title">Ramadan Food Package Drive</div>
                  <div class="project-slide-desc">Distributed 450+ family ration packages across Cavite communities.</div>
                </div>
              </div>
              <!-- Slide 2 -->
              <div class="project-card-slide">
                <img src="<?= asset('assets/damayanHero3.png') ?>" alt="Community Health & Medicine" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Medical Aid</div>
                  <div class="project-slide-title">Community Health &amp; Medicine</div>
                  <div class="project-slide-desc">Provided essential maintenance medication and free clinics.</div>
                </div>
              </div>
              <!-- Slide 3 -->
              <div class="project-card-slide">
                <img src="<?= asset('assets/dawahFeatureImage.jpg') ?>" alt="Back-to-School Kits" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Education Support</div>
                  <div class="project-slide-title">Back-to-School Supplies Kits</div>
                  <div class="project-slide-desc">Equipped 200+ orphans and students with backpacks and books.</div>
                </div>
              </div>
              <!-- Slide 4 -->
              <div class="project-card-slide">
                <img src="<?= asset('assets/ISCAG2.png') ?>" alt="Damayan Janazah Burial Support" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Burial Assistance</div>
                  <div class="project-slide-title">Damayan Janazah Burial Aid</div>
                  <div class="project-slide-desc">Assisted underprivileged bereaved families with dignified Islamic rites.</div>
                </div>
              </div>
              <!-- Duplicate for seamless ticker loop -->
              <div class="project-card-slide">
                <img src="<?= asset('assets/ISCAG3.png') ?>" alt="Ramadan Food Package Drive" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Food Assistance</div>
                  <div class="project-slide-title">Ramadan Food Package Drive</div>
                  <div class="project-slide-desc">Distributed 450+ family ration packages across Cavite communities.</div>
                </div>
              </div>
              <div class="project-card-slide">
                <img src="<?= asset('assets/damayanHero3.png') ?>" alt="Community Health & Medicine" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Medical Aid</div>
                  <div class="project-slide-title">Community Health &amp; Medicine</div>
                  <div class="project-slide-desc">Provided essential maintenance medication and free clinics.</div>
                </div>
              </div>
              <div class="project-card-slide">
                <img src="<?= asset('assets/dawahFeatureImage.jpg') ?>" alt="Back-to-School Kits" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Education Support</div>
                  <div class="project-slide-title">Back-to-School Supplies Kits</div>
                  <div class="project-slide-desc">Equipped 200+ orphans and students with backpacks and books.</div>
                </div>
              </div>
              <div class="project-card-slide">
                <img src="<?= asset('assets/ISCAG2.png') ?>" alt="Damayan Janazah Burial Support" class="project-slide-img" />
                <div class="project-slide-body">
                  <div class="project-slide-tag">Burial Assistance</div>
                  <div class="project-slide-title">Damayan Janazah Burial Aid</div>
                  <div class="project-slide-desc">Assisted underprivileged bereaved families with dignified Islamic rites.</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ═══ Contribution History Table ═══ -->
        <section class="history-card" id="history-section">
          <div class="history-header">
            <div>
              <h4 class="history-title">My Contribution History</h4>
              <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">
                Total Confirmed Donations: <strong style="color:var(--primary-green);">₱<?= number_format($totalDonated, 2) ?></strong>
              </div>
            </div>
            <span class="history-count-badge"><?= count($myDonations) ?> Record(s)</span>
          </div>

          <div class="history-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Category</th>
                  <th>Program / Cause</th>
                  <th>Amount / Qty</th>
                  <th>Method</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th style="text-align:right;">Receipt</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($myDonations)): ?>
                  <tr>
                    <td colspan="8" style="text-align:center; padding:32px; color:var(--text-muted);">
                      You have not submitted any donation records yet. Choose a program above to get started!
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($myDonations as $don): ?>
                    <?php 
                      $cat = $don['donation_category'] ?? ($don['donation_type'] === 'in-kind' ? 'food' : 'digital');
                      $st = $don['status'];
                      $badgeClass = 'st-available';
                      if ($st === 'Pending Verification') $badgeClass = 'st-pending-verif';
                      elseif ($st === 'Pending Receipt' || $st === 'Pending Coordination') $badgeClass = 'st-pending-rcpt';
                      elseif ($st === 'Rejected' || $st === 'Cancelled') $badgeClass = 'st-rejected';
                    ?>
                    <tr>
                      <td>
                        <strong style="color:var(--primary-dark); font-family:monospace;"><?= htmlspecialchars($don['donation_ref']) ?></strong>
                      </td>
                      <td>
                        <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase;">
                          <?= ucfirst($cat) ?>
                        </span>
                      </td>
                      <td>
                        <strong><?= htmlspecialchars($don['target_program'] ?: 'General Welfare & Relief') ?></strong>
                      </td>
                      <td>
                        <?php if ($don['donation_type'] === 'monetary'): ?>
                          <strong style="color:var(--primary-green);">₱<?= number_format($don['amount'], 2) ?></strong>
                        <?php else: ?>
                          <strong><?= (int)$don['quantity'] ?> <?= htmlspecialchars($don['unit'] ?? 'Units') ?></strong>
                        <?php endif; ?>
                      </td>
                      <td><?= htmlspecialchars($don['payment_method'] ?: 'Direct') ?></td>
                      <td style="color:var(--text-muted); font-size:0.8rem;"><?= date('M d, Y', strtotime($don['date_received'])) ?></td>
                      <td>
                        <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($st) ?></span>
                      </td>
                      <td style="text-align:right;">
                        <?php if (!empty($don['ack_issued'])): ?>
                          <a href="<?= url('/admin/damayan/charity/donations/print-ack?id=' . $don['id']) ?>" target="_blank" style="font-size:0.75rem; font-weight:700; color:var(--primary-green); text-decoration:underline;">
                            Print Ack
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

      </div><!-- /.charity-container -->
    </div><!-- /.page-body -->
  </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

<!-- ═══ THANK YOU POP-UP MODAL ═══ -->
<div id="thank-you-modal" class="thank-you-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="tym-title">
  <div class="thank-you-modal-backdrop" onclick="closeThankYouModal()"></div>
  <div class="thank-you-modal-card">
    <div class="thank-you-top-bar"></div>
    <button type="button" class="thank-you-close-btn" onclick="closeThankYouModal()" aria-label="Close modal">&times;</button>

    <div class="thank-you-modal-content">
      <div class="thank-you-icon-circle">
        <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      </div>

      <h3 class="thank-you-title" id="tym-title">Thank You for Your Generous Donation!</h3>

      <div class="thank-you-ref-badge" id="tym-ref-box" style="display:none;">
        <span class="ref-label">Reference Number:</span>
        <strong class="ref-number" id="tym-ref-number">DON-2026-0000</strong>
      </div>

      <blockquote class="thank-you-quote">
        “Giving is not just about making a donation. It is about making a difference in someone's life.”
      </blockquote>

      <div class="thank-you-body">
        <p>
          Thank you for your kindness and generosity in supporting our charity programs. Your contribution helps us provide food assistance, medical support, educational supplies, and burial assistance to individuals and families in need.
        </p>
        <p>
          Every donation, no matter how big or small, brings hope and makes a meaningful difference in our community.
        </p>
        <p class="thank-you-blessing">
          May Allah reward you abundantly for your generosity and bless you and your family with goodness and blessings.
        </p>
        <p class="thank-you-closing">
          <strong>JazakAllahu Khairan!</strong> Thank you for being part of our mission to serve and uplift the community.
        </p>
      </div>

      <div class="thank-you-footer">
        <button type="button" class="btn-thank-you-print" onclick="printModalDonationReceipt()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
          <span>Print Official Receipt</span>
        </button>
        <button type="button" class="btn-thank-you-done" onclick="closeThankYouModal()">
          <span>View in Contribution History</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ═══ WALK-IN DONATION SLIP POP-UP MODAL ═══ -->
<div id="donation-slip-modal" class="thank-you-modal-overlay" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="slip-modal-title">
  <div class="thank-you-modal-backdrop" onclick="closeDonationSlipModal()"></div>
  <div class="slip-modal-card">
    <div class="thank-you-top-bar"></div>
    <button type="button" class="thank-you-close-btn" onclick="closeDonationSlipModal()" aria-label="Close modal">&times;</button>

    <div style="padding: 28px 28px 24px;">
      <!-- Modal Header -->
      <div style="text-align: center; margin-bottom: 16px;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: var(--primary-soft); color: var(--primary-green); margin-bottom: 12px; box-shadow: 0 4px 14px rgba(15, 92, 58, 0.15);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
        </div>
        <h3 id="slip-modal-title" style="font-family: 'Lora', serif; font-size: 1.45rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 6px;">
          Official Walk-in Donation Slip
        </h3>
        <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0;">
          Your in-person donation intent has been registered in the ISCAG Damayan records.
        </p>
      </div>

      <!-- Ticket Voucher Card -->
      <div class="slip-paper-ticket">
        <!-- Top branding inside slip -->
        <div class="slip-brand-row">
          <div class="slip-brand-left">
            <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="slip-brand-img" />
            <div>
              <div class="slip-brand-title">Islamic Studies, Call and Guidance</div>
              <div class="slip-brand-sub">Social Welfare &amp; Damayan Department • Walk-in Slip</div>
            </div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Reference No.</div>
            <div id="slip-modal-ref" style="font-family: monospace; font-size: 1.05rem; font-weight: 800; color: var(--primary-green);">DON-2026-0000</div>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="slip-grid">
          <div class="slip-item">
            <span class="slip-label">Donor Name</span>
            <span class="slip-val" id="slip-donor-val"><?= htmlspecialchars($donorFullName) ?></span>
          </div>
          <div class="slip-item">
            <span class="slip-label">Issuance Date</span>
            <span class="slip-val" id="slip-date-val"><?= date('M d, Y') ?></span>
          </div>
          <div class="slip-item">
            <span class="slip-label">Charity Program</span>
            <span class="slip-val" id="slip-prog-val">Food Donation</span>
          </div>
          <div class="slip-item">
            <span class="slip-label">Pledged Amount</span>
            <span class="slip-val highlight" id="slip-amt-val">₱500.00</span>
          </div>
          <div class="slip-item">
            <span class="slip-label">Handover Method</span>
            <span class="slip-val">Direct Handover (Face-to-Face)</span>
          </div>
          <div class="slip-item">
            <span class="slip-label">Current Status</span>
            <span class="slip-val"><span class="status-badge st-pending-rcpt">Pending In-Person Handover</span></span>
          </div>
        </div>

        <!-- Location Box -->
        <div style="margin-top: 14px; background: #f0f7f4; border: 1px dashed #0f5c3a; border-radius: 8px; padding: 10px 14px; font-size: 0.78rem; color: #0f5c3a; display: flex; align-items: flex-start; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="#0f5c3a" style="flex-shrink:0; margin-top:2px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          <div>
            <strong>Drop-off Location:</strong> Damayan Reception &amp; Cashier Desk, ISCAG Islamic Complex, #31 Jose Abad Santos St., Salitran 1, Dasmariñas City, Cavite (Mon-Sun, 8:00 AM – 5:00 PM).
          </div>
        </div>
      </div>

      <!-- Islamic Blessing / Note -->
      <div style="background: #fcf6e8; border-left: 3px solid var(--accent-gold); border-radius: 6px; padding: 10px 14px; font-size: 0.78rem; color: #78350f; line-height: 1.45; margin-bottom: 20px;">
        “Giving is not just about making a donation. It is about making a difference in someone's life.” May Allah reward your good deeds abundantly. <strong>JazakAllahu Khairan!</strong>
      </div>

      <!-- Action Footer -->
      <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
        <button type="button" class="btn-thank-you-print" onclick="printModalDonationReceipt()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
          <span>Print Donation Slip</span>
        </button>
        <button type="button" class="btn-thank-you-done" onclick="closeDonationSlipModal()">
          <span>Done &amp; View History</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </div>
</div>

  <script>
    // ── Program Data Directory ──
    const PROGRAM_DATA = {
      food: {
        title: 'Food Donation',
        desc: 'Support food assistance for individuals and families in need.',
        amounts: [500, 1000, 2500]
      },
      medical: {
        title: 'Medical Donation',
        desc: 'Help support medical needs and healthcare assistance for eligible beneficiaries.',
        amounts: [500, 1000, 2500]
      },
      school: {
        title: 'School Supplies Donation',
        desc: 'Help provide essential learning materials for students in need.',
        amounts: [500, 1000, 2500]
      },
      burial: {
        title: 'Burial Assistance Donation',
        desc: 'Support burial assistance for eligible families in the community.',
        amounts: [500, 1000, 2500]
      }
    };

    // Current State
    let currentProgram = 'food';
    let currentAmount = 500;
    let isCustomAmount = false;
    let currentMethod = 'paymongo'; // 'paymongo' or 'direct'

    // ── Hero Carousel Logic ──
    const heroTrack = document.getElementById('carousel-track');
    const heroDots = document.querySelectorAll('.carousel-dots .dot');
    let heroSlideIndex = 0;
    let heroTimer = null;

    function gotoHeroSlide(idx) {
      heroSlideIndex = idx;
      if (heroTrack) {
        heroTrack.style.transform = `translateX(-${idx * 33.333}%)`;
      }
      heroDots.forEach((d, i) => d.classList.toggle('active', i === idx));
    }

    function startHeroCarousel() {
      heroTimer = setInterval(() => {
        heroSlideIndex = (heroSlideIndex + 1) % 3;
        gotoHeroSlide(heroSlideIndex);
      }, 5500);
    }
    startHeroCarousel();

    // ── Card & Amount Selection Logic ──
    function selectProgramCard(progKey) {
      if (!PROGRAM_DATA[progKey]) return;
      currentProgram = progKey;

      // Update Card Visual Selected State
      document.querySelectorAll('.program-card').forEach(card => {
        card.classList.remove('selected-card');
      });
      const activeCard = document.getElementById(`card-${progKey}`);
      if (activeCard) {
        activeCard.classList.add('selected-card');
      }

      // Check if current card already has an active button
      const activeBtnInCard = activeCard.querySelector('.card-amt-btn.active');
      if (activeBtnInCard) {
        const amtVal = activeBtnInCard.getAttribute('data-amt');
        if (amtVal === 'custom') {
          isCustomAmount = true;
          const inputVal = parseFloat(document.getElementById(`custom-input-${progKey}`).value || 0);
          currentAmount = inputVal > 0 ? inputVal : 500;
        } else {
          isCustomAmount = false;
          currentAmount = parseFloat(amtVal);
        }
      } else {
        // Default to first preset (₱500)
        chooseCardAmount(progKey, 500, activeCard.querySelector('.card-amt-btn[data-amt="500"]'));
      }

      updateLiveSummary();
    }

    function chooseCardAmount(progKey, amt, btnEl, evt) {
      if (evt) evt.stopPropagation();
      currentProgram = progKey;

      // Highlight selected card and unhighlight others
      document.querySelectorAll('.program-card').forEach(card => {
        card.classList.remove('selected-card');
      });
      const activeCard = document.getElementById(`card-${progKey}`);
      if (activeCard) activeCard.classList.add('selected-card');

      // Clear active states on all buttons across all cards
      document.querySelectorAll('.card-amt-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.card-custom-amount-box').forEach(box => box.style.display = 'none');

      if (btnEl) btnEl.classList.add('active');

      if (amt === 'custom') {
        isCustomAmount = true;
        const customBox = document.getElementById(`custom-box-${progKey}`);
        if (customBox) {
          customBox.style.display = 'block';
          const input = document.getElementById(`custom-input-${progKey}`);
          if (input) {
            input.focus();
            const val = parseFloat(input.value || 0);
            currentAmount = val > 0 ? val : 0;
          }
        }
      } else {
        isCustomAmount = false;
        currentAmount = parseFloat(amt);
      }

      updateLiveSummary();
    }

    function handleCustomInput(progKey, val) {
      const num = parseFloat(val);
      if (!isNaN(num) && num > 0) {
        currentAmount = num;
      } else {
        currentAmount = 0;
      }
      updateLiveSummary();
    }

    // ── Method Selection Logic ──
    function setDonationMethod(method) {
      currentMethod = method;
      const btnDirect = document.getElementById('btn-method-direct');
      const btnPaymongo = document.getElementById('btn-method-paymongo');
      const directBox = document.getElementById('method-direct-fields');
      const paymongoBox = document.getElementById('method-paymongo-fields');

      // Reset all
      if (btnDirect) btnDirect.classList.remove('active');
      if (btnPaymongo) btnPaymongo.classList.remove('active');
      if (directBox) directBox.style.display = 'none';
      if (paymongoBox) paymongoBox.style.display = 'none';

      if (method === 'direct') {
        if (btnDirect) btnDirect.classList.add('active');
        if (directBox) directBox.style.display = 'block';
        document.getElementById('form-method').value = 'direct';
        document.getElementById('form-category').value = 'cash';
      } else if (method === 'paymongo') {
        if (btnPaymongo) btnPaymongo.classList.add('active');
        if (paymongoBox) paymongoBox.style.display = 'block';
        document.getElementById('form-method').value = 'paymongo';
        document.getElementById('form-category').value = 'digital';
      }

      updateLiveSummary();
    }

    // ── Live Summary Updater ──
    function updateLiveSummary() {
      const p = PROGRAM_DATA[currentProgram];
      if (!p) return;

      document.getElementById('form-target-program').value = p.title;
      document.getElementById('form-amount').value = currentAmount;

      const amtDisplay = currentAmount > 0 
        ? '₱' + currentAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        : 'Enter Amount';

      const dName = '<?= htmlspecialchars($donorFullName) ?>';

      // Update PayMongo summary panel
      const pmProg = document.getElementById('pm-sum-program');
      const pmDonor = document.getElementById('pm-sum-donor');
      const pmAmt = document.getElementById('pm-sum-amount');
      const pmBtnAmt = document.getElementById('pm-btn-amount');
      if (pmProg) pmProg.textContent = p.title;
      if (pmDonor) pmDonor.textContent = dName;
      if (pmAmt) pmAmt.textContent = amtDisplay;
      if (pmBtnAmt) pmBtnAmt.textContent = currentAmount > 0 ? currentAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
    }

    // ── Direct Walk-in Donation Submission Handler ──
    async function handleDonationSubmit(e) {
      if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
      }

      // Validate Amount
      if (currentAmount <= 0 || isNaN(currentAmount)) {
        showToast('Please select or enter a valid donation amount greater than ₱0.00.', '#dc2626');
        return;
      }

      const p = PROGRAM_DATA[currentProgram];
      const formData = new FormData();
      formData.append('target_program', p ? p.title : 'General Welfare & Relief');
      formData.append('amount', currentAmount);
      formData.append('donation_method', 'direct');
      formData.append('donation_category', 'cash');
      formData.append('donor_name', '<?= htmlspecialchars($donorFullName) ?>');
      formData.append('donor_contact', '<?= htmlspecialchars($donorContact) ?>');
      formData.append('intended_visit_date', '<?= date('Y-m-d') ?>');
      formData.append('dropoff_location', '<?= htmlspecialchars(CharityProgram::OFFICIAL_ISCAG_ADDRESS) ?>');
      formData.append('remarks', 'Direct in-person walk-in donation at ISCAG Complex Damayan Reception Desk');

      const btn = document.getElementById('btn-submit-direct');
      const label = document.getElementById('btn-submit-direct-label');

      if (btn) btn.disabled = true;
      if (label) label.textContent = 'Submitting Intent...';

      try {
        const res = await fetch('<?= url('/user/services/charity/donate') ?>', {
          method: 'POST',
          body: formData
        });

        const data = await res.json();
        if (data.success) {
          openDonationSlipModal(data.donation_ref);
        } else {
          if (btn) btn.disabled = false;
          if (label) label.textContent = 'Confirm & Get Walk-in Donation Slip';
          showToast(data.error || 'Failed to submit donation. Please review your entries.', '#dc2626');
        }
      } catch (err) {
        console.error(err);
        if (btn) btn.disabled = false;
        if (label) label.textContent = 'Confirm & Get Walk-in Donation Slip';
        showToast('A network error occurred. Please try again.', '#dc2626');
      }
    }

    let currentDonationRef = '';

    // ── Thank You Modal Handlers ──
    function openThankYouModal(refNumber) {
      currentDonationRef = refNumber || '';
      const modal = document.getElementById('thank-you-modal');
      const refBox = document.getElementById('tym-ref-box');
      const refEl = document.getElementById('tym-ref-number');

      if (refNumber) {
        refEl.textContent = refNumber;
        refBox.style.display = 'inline-flex';
      } else {
        refBox.style.display = 'none';
      }

      modal.style.display = 'flex';
      requestAnimationFrame(() => {
        modal.classList.add('show');
      });

      const btnDirect = document.getElementById('btn-submit-direct');
      const labelDirect = document.getElementById('btn-submit-direct-label');
      if (btnDirect) btnDirect.disabled = false;
      if (labelDirect) labelDirect.textContent = 'Confirm & Get Walk-in Donation Slip';

      const btnPaymongo = document.getElementById('btn-paymongo-pay');
      const labelPaymongo = document.getElementById('btn-paymongo-label');
      if (btnPaymongo) btnPaymongo.disabled = false;
      if (labelPaymongo) {
        labelPaymongo.innerHTML = 'Donate ₱<span id="pm-btn-amount">' + (currentAmount > 0 ? currentAmount.toFixed(2) : '500.00') + '</span> via PayMongo';
      }
    }

    function closeThankYouModal() {
      const modal = document.getElementById('thank-you-modal');
      if (!modal) return;
      modal.classList.remove('show');
      setTimeout(() => {
        modal.style.display = 'none';
        window.location.reload();
      }, 300);
    }

    // ── Walk-in Donation Slip Modal Handlers ──
    function openDonationSlipModal(refNumber) {
      currentDonationRef = refNumber || '';
      const modal = document.getElementById('donation-slip-modal');
      const refEl = document.getElementById('slip-modal-ref');
      const progEl = document.getElementById('slip-prog-val');
      const amtEl = document.getElementById('slip-amt-val');
      const dateEl = document.getElementById('slip-date-val');

      if (refEl) refEl.textContent = refNumber || 'DON-2026';
      
      const p = PROGRAM_DATA[currentProgram];
      if (progEl) progEl.textContent = p ? p.title : 'Food Donation';

      const amtNum = parseFloat(currentAmount) || 0;
      if (amtEl) {
        amtEl.textContent = '₱' + amtNum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      if (dateEl) {
        const today = new Date();
        const opts = { month: 'short', day: 'numeric', year: 'numeric' };
        dateEl.textContent = today.toLocaleDateString('en-US', opts);
      }

      modal.style.display = 'flex';
      requestAnimationFrame(() => {
        modal.classList.add('show');
      });

      const btnDirect = document.getElementById('btn-submit-direct');
      const labelDirect = document.getElementById('btn-submit-direct-label');
      if (btnDirect) btnDirect.disabled = false;
      if (labelDirect) labelDirect.textContent = 'Confirm & Get Walk-in Donation Slip';
    }

    function closeDonationSlipModal() {
      const modal = document.getElementById('donation-slip-modal');
      if (!modal) return;
      modal.classList.remove('show');
      setTimeout(() => {
        modal.style.display = 'none';
        window.location.reload();
      }, 300);
    }

    // ── Printable Receipt Function in Thank You Modal ──
    function printModalDonationReceipt() {
      const slipRefEl = document.getElementById('slip-modal-ref');
      const tymRefEl = document.getElementById('tym-ref-number');
      const ref = currentDonationRef || (slipRefEl ? slipRefEl.textContent : (tymRefEl ? tymRefEl.textContent : 'DON-2026'));
      const p = PROGRAM_DATA[currentProgram];
      const progTitle = p ? p.title : 'Charity Donation';
      const amtNum = parseFloat(currentAmount) || 0;
      const amtStr = '₱' + amtNum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const methodStr = currentMethod === 'direct' 
        ? 'Direct Donation, Face-to-Face (ISCAG Complex Walk-in)' 
        : 'PayMongo Secure Gateway (Instant Online)';
      const donorName = '<?= htmlspecialchars($donorFullName) ?>';
      const donorContact = '<?= htmlspecialchars($donorContact ?: "ISCAG Community Member") ?>';
      const dateStr = '<?= date("F d, Y") ?>';
      const statusStr = currentMethod === 'direct' 
        ? 'Pending Receipt (In-Person Handover)' 
        : 'Available (Verified Online)';
      const addressConst = '<?= htmlspecialchars(CharityProgram::OFFICIAL_ISCAG_ADDRESS) ?>';

      const printHtml = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>ISCAG Official Donation Receipt - ${ref}</title>
          <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,400&family=Source+Sans+3:wght@400;600;700;800&display=swap" rel="stylesheet">
          <style>
            * { box-sizing: border-box; }
            body { font-family: 'Source Sans 3', sans-serif; color: #1f2e2a; margin: 0; padding: 24px; background: white; }
            .receipt-container { max-width: 680px; margin: 0 auto; border: 2px solid #0f5c3a; border-radius: 16px; padding: 36px 32px; position: relative; }
            .top-stripe { height: 4px; background: linear-gradient(90deg, #0f5c3a, #c79a2b); border-radius: 12px 12px 0 0; margin: -36px -32px 28px -32px; }
            .receipt-header { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0f5c3a; padding-bottom: 20px; margin-bottom: 24px; }
            .brand-wrap { display: flex; align-items: center; gap: 14px; }
            .brand-wrap img { width: 58px; height: 58px; border-radius: 8px; object-fit: cover; }
            .brand-title { font-family: 'Lora', serif; font-size: 1.25rem; font-weight: 700; color: #0f5c3a; line-height: 1.2; }
            .brand-sub { font-size: 0.78rem; color: #6f7f78; margin-top: 3px; }
            .ref-box { text-align: right; }
            .ref-label { font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: #6f7f78; }
            .ref-val { font-family: monospace; font-size: 1.15rem; font-weight: 800; color: #0f5c3a; }
            .doc-heading { text-align: center; margin-bottom: 22px; }
            .doc-title { font-family: 'Lora', serif; font-size: 1.28rem; font-weight: 700; color: #0f5c3a; margin: 0 0 4px; }
            .doc-tag { font-size: 0.78rem; color: #6f7f78; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
            .receipt-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 22px; background: #f8faf9; border-radius: 10px; padding: 16px 20px; border: 1px solid #e2e8e5; }
            .r-item { display: flex; flex-direction: column; gap: 2px; }
            .r-label { font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #6f7f78; letter-spacing: 0.04em; }
            .r-val { font-size: 0.95rem; font-weight: 700; color: #1f2e2a; }
            .r-val.highlight { font-size: 1.35rem; color: #0f5c3a; font-family: 'Lora', serif; }
            .blessing-card { background: #fcf6e8; border-left: 3px solid #c79a2b; border-radius: 6px; padding: 12px 16px; font-size: 0.8rem; color: #555; line-height: 1.5; margin-bottom: 24px; font-style: italic; }
            .location-reminder { background: #f0f7f4; border: 1px dashed #0f5c3a; border-radius: 8px; padding: 10px 14px; font-size: 0.78rem; color: #0f5c3a; margin-bottom: 24px; }
            .sig-row { display: flex; justify-content: space-between; margin-top: 32px; padding-top: 18px; border-top: 1px dashed #cbd5e1; }
            .sig-block { width: 220px; text-align: center; }
            .sig-line { border-top: 1.5px solid #1f2e2a; margin-top: 36px; padding-top: 6px; font-size: 0.82rem; font-weight: 700; }
            .sig-sub { font-size: 0.72rem; color: #6f7f78; }
            @media print {
              body { padding: 0; }
              .receipt-container { border: 1.5px solid #0f5c3a; }
            }
          </style>
        </head>
        <body>
          <div class="receipt-container">
            <div class="top-stripe"></div>
            <div class="receipt-header">
              <div class="brand-wrap">
                <img src="${window.location.origin}/assets/logo.jpg" alt="ISCAG" />
                <div>
                  <div class="brand-title">Islamic Studies, Call and Guidance</div>
                  <div class="brand-sub">Social Welfare &amp; Damayan Charity • ISCAG MIS</div>
                  <div style="font-size:0.72rem; color:#6f7f78; margin-top:2px;">${addressConst}</div>
                </div>
              </div>
              <div class="ref-box">
                <div class="ref-label">Reference No.</div>
                <div class="ref-val">${ref}</div>
              </div>
            </div>

            <div class="doc-heading">
              <div class="doc-title">OFFICIAL DONATION ACKNOWLEDGMENT SLIP</div>
              <div class="doc-tag">Damayan Charity Assistance Program</div>
            </div>

            <div class="receipt-grid">
              <div class="r-item">
                <span class="r-label">Donor Name</span>
                <span class="r-val">${donorName}</span>
              </div>
              <div class="r-item">
                <span class="r-label">Issuance Date</span>
                <span class="r-val">${dateStr}</span>
              </div>
              <div class="r-item">
                <span class="r-label">Charity Program</span>
                <span class="r-val">${progTitle}</span>
              </div>
              <div class="r-item">
                <span class="r-label">Donation Amount</span>
                <span class="r-val highlight">${amtStr}</span>
              </div>
              <div class="r-item">
                <span class="r-label">Donation Method</span>
                <span class="r-val">${methodStr}</span>
              </div>
              <div class="r-item">
                <span class="r-label">Status</span>
                <span class="r-val">${statusStr}</span>
              </div>
            </div>

            ${currentMethod === 'direct' ? `
              <div class="location-reminder" style="display:flex; align-items:flex-start; gap:8px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="#0f5c3a" style="flex-shrink:0; margin-top:2px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                <div><strong>Walk-in Handover:</strong> Please present this official slip along with your contribution at the <strong>Damayan Reception &amp; Cashier Desk</strong>, ISCAG Islamic Complex, Salitran 1, Dasmariñas, Cavite (Mon-Sun, 8:00 AM – 5:00 PM).</div>
              </div>
            ` : ''}

            <div class="blessing-card">
              “Those who spend their wealth in charity day and night, secretly and openly, their reward is with their Lord, and there will be no fear for them, nor will they grieve.” — Surah Al-Baqarah (2:274)
            </div>

            <div class="sig-row">
              <div class="sig-block">
                <div class="sig-line">${donorName}</div>
                <div class="sig-sub">Donor Signature / Conforme</div>
              </div>
              <div class="sig-block">
                <div class="sig-line">Damayan Receiving Desk</div>
                <div class="sig-sub">Authorized ISCAG Officer</div>
              </div>
            </div>
          </div>
          <script>
            window.onload = function() { window.print(); }
          <\/script>
        </body>
        </html>
      `;

      const printWin = window.open('', '_blank', 'width=780,height=750');
      if (printWin) {
        printWin.document.open();
        printWin.document.write(printHtml);
        printWin.document.close();
      } else {
        window.print();
      }
    }

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const modal1 = document.getElementById('thank-you-modal');
        if (modal1 && modal1.classList.contains('show')) {
          closeThankYouModal();
        }
        const modal2 = document.getElementById('donation-slip-modal');
        if (modal2 && modal2.classList.contains('show')) {
          closeDonationSlipModal();
        }
      }
    });

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

    // ── PayMongo Donation Submission ──
    async function submitPayMongoDonation() {
      if (currentAmount <= 0 || isNaN(currentAmount)) {
        showToast('Please select or enter a valid donation amount greater than ₱0.00.', '#dc2626');
        return;
      }

      const btn = document.getElementById('btn-paymongo-pay');
      const label = document.getElementById('btn-paymongo-label');
      const p = PROGRAM_DATA[currentProgram];

      btn.disabled = true;
      label.textContent = 'Connecting to PayMongo...';

      try {
        const res = await fetch('<?= url("/user/services/charity/paymongo-checkout") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            amount: currentAmount,
            target_program: p ? p.title : 'General Welfare & Relief'
          })
        });

        const text = await res.text();
        let data;
        try {
          data = JSON.parse(text);
        } catch (e) {
          throw new Error(text || ('HTTP ' + res.status));
        }

        if (data.success && data.checkout_url) {
          label.textContent = 'Redirecting to PayMongo...';
          window.location.href = data.checkout_url;
        } else {
          showToast(data.message || 'Could not initiate PayMongo checkout.', '#dc2626');
          btn.disabled = false;
          label.innerHTML = 'Donate ₱<span id="pm-btn-amount">' + currentAmount.toFixed(2) + '</span> via PayMongo';
        }
      } catch (err) {
        console.error('PayMongo Charity Checkout Error:', err);
        showToast(err.message || 'Error connecting to payment gateway.', '#dc2626');
        btn.disabled = false;
        label.innerHTML = 'Donate ₱<span id="pm-btn-amount">' + currentAmount.toFixed(2) + '</span> via PayMongo';
      }
    }

    // Auto-show success toast and Thank You modal if redirected back from PayMongo
    window.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      if (params.get('paid') === '1') {
        const ref = params.get('ref') || '';
        currentMethod = 'paymongo';
        showToast('PayMongo donation confirmed! Ref: ' + ref, '#0f5c3a');
        if (typeof openThankYouModal === 'function' && ref) {
          openThankYouModal(ref);
        }
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });

    // Initialize state
    updateLiveSummary();
  </script>
</body>
</html>