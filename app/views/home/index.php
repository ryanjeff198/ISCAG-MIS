<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Navbar</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-900: #14532d;
      --green-800: #166534;
      --green-700: #15803d;
      --green-600: #16a34a;
      --green-100: #dcfce7;
      --green-50:  #f0fdf4;
      --gold:      #b7973a;
      --gold-light:#e8d48b;
      --text-main: #1a1a1a;
      --text-muted: #6b7280;
      --text-light: #9ca3af;
      --border:    #e5e7eb;
      --white:     #ffffff;
      --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      --shadow-md: 0 4px 20px rgba(0,0,0,.08), 0 2px 8px rgba(0,0,0,.05);
      --shadow-lg: 0 12px 40px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.06);
      --radius-xl: 16px;
      --radius-lg: 12px;
      --radius-md: 8px;
      --transition: 220ms cubic-bezier(.4,0,.2,1);
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #f8f7f4;
      min-height: 100vh;
    }

    /* ─── HERO DEMO AREA ───────────────────────────────────────── */
    .demo-hero {
      padding-top: 120px;
      text-align: center;
      color: var(--text-muted);
    }
    .demo-hero h1 {
      font-family: 'Lora', serif;
      font-size: 2rem;
      color: var(--green-800);
      margin-bottom: 8px;
    }
    .demo-hero p { font-size: .95rem; }

    /* ─── NAVBAR ───────────────────────────────────────────────── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 999;
      background: var(--white);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      height: 68px;
    }

    .nav-inner {
      max-width: 1280px;
      margin: 0 auto;
      padding: 0 28px;
      height: 100%;
      display: flex;
      align-items: center;
      gap: 0;
    }

    /* LOGO */
    .logo {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none; flex-shrink: 0;
      margin-right: 32px;
    }
    .logo-icon {
      width: 40px; height: 40px;
      background: white;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .logo-icon img { 
      max-width: 100%; max-height: 100%; 
      object-fit: scale-down; /* Prevents upscaling/zooming */
    }
    .logo-text {
      font-family: 'Lora', serif;
      font-size: .85rem;
      font-weight: 700;
      color: var(--green-800);
      line-height: 1.2;
      max-width: 200px;
    }

    /* CENTER NAV */
    .nav-links {
      display: flex; align-items: center; gap: 2px;
      flex: 1; justify-content: center;
    }

    .nav-item {
      position: relative;
    }

    .nav-link {
      display: flex; align-items: center; gap: 5px;
      padding: 8px 14px;
      font-size: .875rem;
      font-weight: 500;
      color: var(--text-muted);
      text-decoration: none;
      border-radius: var(--radius-md);
      transition: color var(--transition), background var(--transition);
      cursor: pointer;
      user-select: none;
      white-space: nowrap;
    }
    .nav-link:hover, .nav-link.active { color: var(--green-800); }
    .nav-link.active { background: var(--green-50); }

    .nav-link .chevron {
      width: 14px; height: 14px;
      transition: transform var(--transition);
      opacity: .5;
    }
    .nav-item:hover .chevron,
    .nav-item.open .chevron { transform: rotate(180deg); opacity: 1; }

    /* green underline on active */
    .nav-link.active::after {
      content: '';
      position: absolute; bottom: -22px; left: 50%; transform: translateX(-50%);
      width: 20px; height: 2.5px;
      background: var(--green-700);
      border-radius: 99px;
    }

    /* ─── DROPDOWN BASE ────────────────────────────────────────── */
    .dropdown {
      position: absolute;
      top: calc(100% + 12px);
      left: 50%; transform: translateX(-50%);
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      pointer-events: none;
      opacity: 0;
      transform: translateX(-50%) translateY(-8px);
      transition: opacity var(--transition) 150ms, transform var(--transition) 150ms;
      z-index: 100;
      min-width: 200px;
    }
    .dropdown::after {
      content: '';
      position: absolute;
      top: -15px; left: 0; right: 0; height: 15px;
      background: transparent;
    }
    .nav-item:hover .dropdown,
    .nav-item.open .dropdown {
      opacity: 1;
      pointer-events: all;
      transform: translateX(-50%) translateY(0);
      transition: opacity var(--transition) 0ms, transform var(--transition) 0ms;
    }

    /* ─── SIMPLE DROPDOWN (About / Community / Login) ─────────── */
    .dropdown-simple { padding: 8px; }
    .dropdown-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 12px;
      border-radius: var(--radius-md);
      text-decoration: none;
      color: var(--text-main);
      font-size: .875rem;
      font-weight: 400;
      transition: background var(--transition), color var(--transition);
      cursor: pointer;
    }
    .dropdown-item:hover { background: var(--green-50); color: var(--green-800); }
    .dropdown-item:hover .item-icon { background: var(--green-100); color: var(--green-700); }

    .item-icon {
      width: 30px; height: 30px; flex-shrink: 0;
      background: #f3f4f6;
      border-radius: 7px;
      display: flex; align-items: center; justify-content: center;
      font-size: .9rem;
      color: var(--text-muted);
      transition: background var(--transition), color var(--transition);
    }

    .item-text-wrap { display: flex; flex-direction: column; gap: 1px; }
    .item-title { font-weight: 500; font-size: .85rem; line-height: 1.2; }
    .item-desc { font-size: .75rem; color: var(--text-light); line-height: 1.3; }

    /* ─── DEPT DROPDOWN (2-col mega) ──────────────────────────── */
    .dropdown-dept {
      min-width: 600px;
      padding: 16px;
      left: 50%; transform: translateX(-50%);
    }

    .dept-layout {
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 16px;
      transition: all var(--transition);
    }

    /* image panel */
    .dept-image-panel {
      border-radius: var(--radius-lg);
      overflow: hidden;
      position: relative;
      height: 220px;
      flex-shrink: 0;
      transition: order var(--transition);
    }
    .dept-image-panel img {
      width: 100%; height: 100%;
      object-fit: cover;
      transition: transform 400ms ease;
    }
    .dept-image-panel:hover img { transform: scale(1.03); }
    .dept-img-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(22,101,52,.45) 0%, rgba(0,0,0,.15) 100%);
    }
    .dept-img-label {
      position: absolute; bottom: 12px; left: 14px;
      font-family: 'Lora', serif;
      font-size: .8rem; font-weight: 600;
      color: white;
      letter-spacing: .03em;
    }

    /* content panel */
    .dept-content-panel {
      display: flex; flex-direction: column; gap: 4px;
      justify-content: center;
    }
    .dept-panel-label {
      font-size: .7rem; font-weight: 600;
      text-transform: uppercase; letter-spacing: .08em;
      color: var(--text-light);
      margin-bottom: 6px;
      padding: 0 4px;
    }

    .dept-item {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 9px 10px;
      border-radius: var(--radius-md);
      cursor: pointer;
      text-decoration: none;
      transition: background var(--transition);
    }
    .dept-item:hover { background: var(--green-50); }
    .dept-item:hover .dept-item-icon { background: var(--green-100); color: var(--green-700); }

    .dept-item-icon {
      width: 34px; height: 34px; flex-shrink: 0;
      background: #f3f4f6;
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
      transition: background var(--transition), color var(--transition);
    }
    .dept-item-body { flex: 1; }
    .dept-item-title {
      font-size: .875rem; font-weight: 500;
      color: var(--text-main); line-height: 1.2;
    }
    .dept-item-desc {
      font-size: .75rem; color: var(--text-muted);
      line-height: 1.4; margin-top: 2px;
    }

    /* ─── RIGHT: NAVBAR BUTTONS ────────────────────────────────── */
    .nav-right {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-left: 24px;
    }

    .btn-login {
      padding: 10px 20px;
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--green-800);
      text-decoration: none;
      transition: all 0.3s ease;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-login:hover {
      background: var(--green-50);
      color: var(--green-900);
    }

    .btn-register {
      padding: 10px 22px;
      font-size: 0.875rem;
      font-weight: 600;
      color: white !important;
      background: var(--green-800);
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(20, 83, 45, 0.15);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-register:hover {
      background: var(--green-700);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(20, 83, 45, 0.2);
    }

    .login-btn svg, .register-btn svg { width: 15px; height: 15px; }
    .item-svg { width: 18px; height: 18px; }

    /* LOGIN dropdown aligned right */
    .dropdown-login {
      left: auto; right: 0; transform: translateY(-8px);
      min-width: 200px;
    }
    .nav-item:hover .dropdown-login,
    .nav-item.open .dropdown-login {
      transform: translateY(0);
    }

    /* divider */
    .dropdown-divider {
      height: 1px; background: var(--border);
      margin: 4px 8px;
    }

    /* ─── ARROW TIP on dropdowns ─────────────────────────────── */
    .dropdown::before {
      content: '';
      position: absolute; top: -6px; left: 50%; transform: translateX(-50%);
      width: 11px; height: 11px;
      background: var(--white);
      border-left: 1px solid var(--border);
      border-top: 1px solid var(--border);
      transform: translateX(-50%) rotate(45deg);
    }
    .dropdown-login::before { left: auto; right: 24px; transform: rotate(45deg); }

    /* ─── HERO SECTION ────────────────────────────────────────── */
    .hero-section {
      padding-top: 68px; /* Offset for fixed navbar */
      background: linear-gradient(135deg, #f8f7f4 0%, #ffffff 100%);
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: -10%;
      right: -5%;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(20, 83, 45, 0.05) 0%, transparent 70%);
      border-radius: 50%;
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 2;
    }

    .hero-label {
      display: inline-block;
      padding: 6px 18px;
      background: var(--green-50);
      color: var(--green-800);
      font-size: 0.8rem;
      font-weight: 700;
      border-radius: 50px;
      margin-bottom: 1.5rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      border: 1px solid rgba(20, 83, 45, 0.1);
    }

    .hero-title {
      font-family: 'Lora', serif;
      font-size: clamp(2.5rem, 6vw, 4.2rem);
      font-weight: 700;
      color: var(--green-900);
      line-height: 1.1;
      margin-bottom: 1.5rem;
    }

    .hero-title span {
      color: var(--gold);
    }

    .hero-description {
      font-size: 1.15rem;
      color: var(--text-muted);
      margin-bottom: 2.8rem;
      max-width: 580px;
      line-height: 1.7;
    }

    .hero-btns {
      display: flex;
      gap: 1.2rem;
      flex-wrap: wrap;
      margin-bottom: 100px; /* Increased space for better separation */
    }

    .btn-hero {
      padding: 14px 36px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .btn-hero-primary {
      background: var(--green-800);
      color: white;
      border: none;
      box-shadow: 0 4px 15px rgba(20, 83, 45, 0.2);
    }

    .btn-hero-primary:hover {
      background: var(--green-700);
      transform: translateY(-4px);
      box-shadow: 0 12px 30px rgba(20, 83, 45, 0.3);
      color: white;
    }

    .btn-hero-outline {
      background: transparent;
      color: var(--green-800);
      border: 2px solid var(--green-800);
    }

    .btn-hero-outline:hover {
      background: var(--green-800);
      color: white;
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(20, 83, 45, 0.15);
    }

    .btn-arrow {
      transition: transform 0.3s ease;
    }

    .btn-hero:hover .btn-arrow {
      transform: translateX(5px);
    }

    /* ─── INSIGHTS CARDS ──────────────────────────────────────── */
    .insights-overlay {
      position: relative;
      z-index: 20;
      margin-top: 0; /* Removed negative margin for maximum separation */
      padding: 20px 0 80px;
    }

    .insights-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .insight-card {
      background: white;
      padding: 20px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      border: 1px solid rgba(20, 83, 45, 0.08);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
      transition: all 0.4s ease;
    }

    .insight-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(20, 83, 45, 0.1);
      border-color: rgba(20, 83, 45, 0.15);
    }

    .insight-icon {
      width: 44px;
      height: 44px;
      background: var(--green-50);
      color: var(--green-800);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .insight-value {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--green-900);
      line-height: 1.2;
      font-family: 'Lora', serif;
      display: block;
    }

    .insight-label {
      font-size: 0.7rem;
      font-weight: 600;
      color: var(--text-light);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: block;
    }

    /* ─── MISSION & VISION REDESIGN ────────────────────────────── */
    .mv-section {
      padding: 120px 0;
      background: #ffffff;
      position: relative;
    }

    .mv-header {
      text-align: left;
      margin-bottom: 80px;
    }

    .section-tag {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 3px;
      margin-bottom: 12px;
      display: block;
    }

    .section-title {
      font-family: 'Lora', serif;
      font-size: clamp(2rem, 5vw, 3.2rem);
      color: var(--green-900);
      line-height: 1.1;
      max-width: 600px;
    }

    .mv-main-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-bottom: 40px;
    }

    .mv-card-premium {
      background: #fcfbf8;
      padding: 60px;
      border-radius: 40px;
      position: relative;
      overflow: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(20, 83, 45, 0.04);
    }

    .mv-card-premium:hover {
      background: white;
      box-shadow: 0 40px 80px rgba(20, 83, 45, 0.1);
      transform: translateY(-10px);
      border-color: rgba(20, 83, 45, 0.1);
    }

    .mv-card-premium h3 {
      font-family: 'Lora', serif;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--green-800);
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .mv-card-premium h3 span {
      width: 32px;
      height: 2px;
      background: var(--gold);
      display: inline-block;
    }

    .mv-card-premium p {
      font-size: 1.05rem;
      line-height: 1.8;
      color: var(--text-main);
      opacity: 0.9;
    }

    .objective-bar {
      grid-column: span 2;
      background: var(--green-50);
      padding: 50px;
      border-radius: 40px;
      display: grid;
      grid-template-columns: auto 1fr;
      gap: 40px;
      align-items: center;
      border: 1px solid rgba(20, 83, 45, 0.08);
    }

    .obj-label {
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--green-900);
      padding-right: 40px;
      border-right: 2px solid rgba(20, 83, 45, 0.1);
    }

    .obj-text {
      font-size: 1.1rem;
      color: var(--green-800);
      line-height: 1.6;
      font-weight: 500;
    }

    .motto-banner-wrap {
      margin-top: 60px;
      text-align: center;
    }

    .motto-banner {
      display: inline-block;
      padding: 40px 60px;
      background: var(--green-900);
      border-radius: 100px;
      color: white;
      box-shadow: 0 20px 40px rgba(20, 83, 45, 0.2);
      position: relative;
    }

    .motto-banner blockquote {
      margin: 0;
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      font-style: italic;
      color: white;
    }

    .motto-banner .author {
      display: block;
      margin-top: 10px;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--gold);
      font-weight: 700;
      font-style: normal;
    }

    @media (max-width: 991.98px) {
      .mv-main-grid { grid-template-columns: 1fr; }
      .objective-bar { 
        grid-column: span 1; 
        grid-template-columns: 1fr; 
        gap: 20px;
        text-align: center;
      }
      .obj-label { border-right: none; border-bottom: 2px solid rgba(20, 83, 45, 0.1); padding: 0 0 20px; }
      .motto-banner { padding: 30px 40px; border-radius: 40px; width: 100%; }
      .mv-card-premium { padding: 40px; }
    }

    /* ─── FOOTER ───────────────────────────────────────────────── */
    footer {
      background: var(--green-900);
      color: white;
      padding: 40px 0 20px; /* Minimal padding */
      position: relative;
    }

    footer::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--gold) 0%, transparent 50%, var(--gold) 100%);
      opacity: 0.3;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
      gap: 30px;
      margin-bottom: 30px; /* Minimal bottom margin */
    }

    .footer-about .footer-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 16px;
      text-decoration: none;
    }

    .footer-logo-icon {
      width: 48px; height: 48px;
      background: white;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .footer-logo-icon img {
      max-width: 100%; max-height: 100%;
      object-fit: scale-down;
    }

    .footer-logo-text {
      font-family: 'Lora', serif;
      font-size: 0.9rem;
      font-weight: 700;
      color: white;
      line-height: 1.2;
    }

    .footer-desc {
      font-size: 0.9rem;
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 20px;
    }

    .footer-social {
      display: flex;
      gap: 12px;
    }

    .social-btn {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.1);
      display: flex; align-items: center; justify-content: center;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .social-btn:hover {
      background: var(--gold);
      color: var(--green-900);
      transform: translateY(-4px);
    }

    .footer-title {
      font-family: 'Lora', serif;
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 15px; /* Minimal space */
      color: var(--gold);
    }

    .footer-links {
      list-style: none;
      padding: 0; margin: 0;
    }

    .footer-links li {
      margin-bottom: 15px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .footer-links a:hover {
      color: white;
      padding-left: 8px;
    }

    .footer-contact-item {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }

    .contact-icon {
      width: 20px; height: 20px;
      color: var(--gold);
      flex-shrink: 0;
    }

    .contact-text {
      font-size: 0.95rem;
      color: rgba(255, 255, 255, 0.7);
      line-height: 1.5;
    }

    .footer-bottom {
      padding-top: 30px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.5);
    }

    .footer-legal {
      display: flex;
      gap: 24px;
    }

    .footer-legal a {
      color: inherit;
      text-decoration: none;
    }

    .footer-legal a:hover { color: white; }

    @media (max-width: 991.98px) {
      .footer-grid { grid-template-columns: 1fr 1fr; gap: 40px; }
      .footer-about { grid-column: span 2; }
    }

    @media (max-width: 575.98px) {
      .footer-grid { grid-template-columns: 1fr; }
      .footer-about { grid-column: span 1; }
      .footer-bottom { flex-direction: column; gap: 20px; text-align: center; }
    }

    .btn-hero-text:hover {
      background: var(--green-100);
      color: var(--green-900);
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(20, 83, 45, 0.1);
    }
    
    .btn-hero-text svg {
      transition: transform 0.3s ease;
    }
    
    .btn-hero-text:hover svg {
      transform: translateX(5px);
    }

    .hero-image-container {
      position: relative;
      z-index: 1;
    }

    .hero-image-wrapper {
      position: relative;
      margin-top: -50px; /* Increased lift for images */
    }

    .hero-image-card {
      background: white;
      padding: 15px;
      border-radius: 32px;
      box-shadow: var(--shadow-lg);
      transform: perspective(1200px) rotateY(-8deg) rotateX(2deg);
      transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid rgba(0,0,0,0.05);
    }

    .hero-image-card:hover {
      transform: perspective(1200px) rotateY(0deg) rotateX(0deg) scale(1.02);
    }

    .hero-image-card img {
      width: 100%;
      height: auto;
      border-radius: 22px;
      display: block;
    }

    /* Floating Badge inside Image Area */
    /* Scroll Animation Utility */
    .reveal {
      opacity: 0;
      transform: translateY(60px);
      transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
      will-change: transform, opacity;
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    .delay-1 { transition-delay: 0.15s; }
    .delay-2 { transition-delay: 0.3s; }
    .delay-3 { transition-delay: 0.45s; }
    .delay-4 { transition-delay: 0.6s; }

    @media (max-width: 991.98px) {
      .hero-section {
        padding-top: 100px;
        padding-bottom: 60px;
        text-align: center;
      }
      .hero-description {
        margin-left: auto;
        margin-right: auto;
      }
      .hero-btns {
        justify-content: center;
      }
      .hero-image-container {
        margin-top: 2rem;
      }
      .hero-image-card {
        transform: none;
      }
    }

    .hero-image-card-secondary {
      position: absolute;
      top: -40px;
      right: -30px;
      width: 200px;
      background: white;
      padding: 12px;
      border-radius: 24px;
      box-shadow: var(--shadow-lg);
      transform: perspective(1200px) rotateY(15deg) rotateX(-5deg);
      z-index: 4;
      border: 1px solid rgba(0,0,0,0.05);
      animation: float 6s ease-in-out infinite;
    }
    
    .hero-image-card-secondary img {
      width: 100%;
      height: auto;
      border-radius: 16px;
      display: block;
    }

    @keyframes float {
      0%, 100% { transform: perspective(1200px) rotateY(15deg) rotateX(-5deg) translateY(0); }
      50% { transform: perspective(1200px) rotateY(15deg) rotateX(-5deg) translateY(-15px); }
    }

    @media (max-width: 991.98px) {
      .hero-image-card-secondary {
        width: 140px;
        top: -20px;
        right: 0;
      }
      .hero-image-card-tertiary, .hero-image-card-quaternary, .hero-image-card-quinary {
        display: none; /* Hide extra elements on smaller mobile screens for clarity */
      }
    }

    .hero-image-card-tertiary {
      position: absolute;
      bottom: -30px;
      left: -40px;
      width: 180px;
      background: white;
      padding: 10px;
      border-radius: 24px;
      box-shadow: var(--shadow-lg);
      transform: perspective(1200px) rotateY(-15deg) rotateX(10deg);
      z-index: 5;
      border: 1px solid rgba(0,0,0,0.05);
      animation: floatTertiary 7s ease-in-out infinite;
    }

    .hero-image-card-quaternary {
      position: absolute;
      bottom: -60px;
      right: 40px;
      width: 160px;
      background: white;
      padding: 8px;
      border-radius: 20px;
      box-shadow: var(--shadow-lg);
      transform: perspective(1200px) rotateY(-5deg) rotateX(-10deg);
      z-index: 2;
      border: 1px solid rgba(0,0,0,0.05);
      animation: floatQuaternary 5s ease-in-out infinite;
    }

    .hero-image-card-quinary {
      position: absolute;
      top: -70px;
      left: 30px;
      width: 150px;
      background: white;
      padding: 8px;
      border-radius: 22px;
      box-shadow: var(--shadow-lg);
      transform: perspective(1200px) rotateY(12deg) rotateX(15deg);
      z-index: 1;
      border: 1px solid rgba(0,0,0,0.05);
      animation: floatQuinary 6.5s ease-in-out infinite;
    }

    .hero-image-card-tertiary img, .hero-image-card-quaternary img, .hero-image-card-quinary img {
      width: 100%;
      height: auto;
      border-radius: 16px;
      display: block;
    }

    @keyframes floatTertiary {
      0%, 100% { transform: perspective(1200px) rotateY(-15deg) rotateX(10deg) translateY(0); }
      50% { transform: perspective(1200px) rotateY(-15deg) rotateX(10deg) translateY(-20px); }
    }

    @keyframes floatQuaternary {
      0%, 100% { transform: perspective(1200px) rotateY(-5deg) rotateX(-10deg) translateY(0); }
      50% { transform: perspective(1200px) rotateY(-5deg) rotateX(-10deg) translateY(-12px); }
    }

    @keyframes floatQuinary {
      0%, 100% { transform: perspective(1200px) rotateY(12deg) rotateX(15deg) translateY(0); }
      50% { transform: perspective(1200px) rotateY(12deg) rotateX(15deg) translateY(-18px); }
    }
  </style>
</head>
<body>

<!-- ══════════════════════ NAVBAR ══════════════════════ -->
<nav>
  <div class="nav-inner">

    <!-- LOGO -->
    <a href="#" class="logo">
      <div class="logo-icon">
        <img src="<?= asset('assets/logo.jpg') ?>" alt="Logo" style="width: 100%; height: 100%; border-radius: 9px; object-fit: cover;">
      </div>
      <span class="logo-text">Islamic Studies, Call and Guidance of the Philippines</span>
    </a>

    <!-- CENTER LINKS -->
    <ul class="nav-links" style="list-style:none;">

      <!-- HOME -->
      <li class="nav-item">
        <a href="<?= url('/') ?>" class="nav-link active">Home</a>
      </li>

      <!-- ABOUT -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          About
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="#mission-vision" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Mission & Vision</span>
              <span class="item-desc">Our purpose and guiding principles</span>
            </span>
          </a>
          <a href="<?= url('/history') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">History</span>
              <span class="item-desc">How ISCAG was founded</span>
            </span>
          </a>
          <a href="<?= url('/organization') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Organization</span>
              <span class="item-desc">Leadership and structure</span>
            </span>
          </a>
        </div>
      </li>

      <!-- DEPARTMENT (MEGA) -->
      <li class="nav-item" id="deptNav">
        <a class="nav-link" href="#">
          Department
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>

        <div class="dropdown dropdown-dept">
          <div class="dept-layout" id="deptLayout" onmouseleave="swapLayout(false)">

            <!-- IMAGE PANEL -->
            <div class="dept-image-panel">
              <img
                src="<?= asset('assets/hero-mosque.png') ?>"
                alt="Department Preview"
                id="deptPreviewImg"
              />
              <div class="dept-img-overlay"></div>
              <span class="dept-img-label">ISCAG Departments</span>
            </div>

            <!-- CONTENT PANEL -->
            <div class="dept-content-panel">
              <p class="dept-panel-label">Our Services</p>

              <a href="<?= url('/apartment') ?>" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/1BR Type/1BR front.jpg') ?>', 'Apartment Services')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M3 21h18"/><path d="M9 21V9l-4 2v10"/><path d="M15 21V3l-4 2v16"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Apartment</span>
                  <span class="dept-item-desc">Apply and manage housing units</span>
                </span>
              </a>

              <a href="<?= url('/damayan') ?>" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/about-center.png') ?>', 'Damayan Support')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Damayan</span>
                  <span class="dept-item-desc">Burial and bereavement support services</span>
                </span>
              </a>

              <a href="<?= url('/daawah') ?>" class="dept-item"
                 onmouseenter="swapLayout(true, '<?= asset('assets/hero-mosque.png') ?>', 'Daawah Programs')">
                <span class="dept-item-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </span>
                <span class="dept-item-body">
                  <span class="dept-item-title">Daawah</span>
                  <span class="dept-item-desc">Islamic programs and guidance</span>
                </span>
              </a>

            </div>
          </div>
        </div>
      </li>

      <!-- COMMUNITY -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          Community
          <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <div class="dropdown dropdown-simple" style="min-width:230px;">
          <a href="<?= url('/events') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Events</span>
              <span class="item-desc">Upcoming gatherings and programs</span>
            </span>
          </a>
          <a href="<?= url('/announcements') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Announcements</span>
              <span class="item-desc">Latest news from ISCAG</span>
            </span>
          </a>
          <a href="<?= url('/volunteer') ?>" class="dropdown-item">
            <span class="item-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="item-svg"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
            </span>
            <span class="item-text-wrap">
              <span class="item-title">Volunteer</span>
              <span class="item-desc">Join our community efforts</span>
            </span>
          </a>
        </div>
      </li>

      <!-- CONTACT -->
      <li class="nav-item">
        <a href="<?= url('/contact') ?>" class="nav-link">Contact</a>
      </li>

    </ul>

    <!-- RIGHT: LOGIN & REGISTER -->
    <div class="nav-right">
      <a href="<?= url('/login') ?>" class="btn-login">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Log In
      </a>
      <a href="<?= url('/register') ?>" class="btn-register">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px; height:18px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
        Register
      </a>
    </div>

  </div>
</nav>

<!-- ══════════════════════ HERO SECTION ══════════════════════ -->
<section class="hero-section min-vh-100 d-flex align-items-center">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- LEFT COLUMN: CONTENT -->
      <div class="col-lg-6 hero-content">
        <span class="hero-label">Welcome to ISCAG MIS</span>
        <h1 class="hero-title">
          Empowering the Community through <span>Guidance</span> & Knowledge
        </h1>
        <p class="hero-description">
          Providing excellence in Islamic studies and social guidance. Access our specialized departments, manage your community services, and stay connected with our mission.
        </p>
        <div class="hero-btns">
          <a href="#" class="btn-hero btn-hero-primary">
            Get Started
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
          <a href="#mission-vision" class="btn-hero btn-hero-outline">
            Explore More
            <svg class="btn-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>

      <!-- RIGHT COLUMN: IMAGE -->
      <div class="col-lg-6 hero-image-container">
        <div class="hero-image-wrapper">
          <div class="hero-image-card">
            <img src="<?= asset('assets/image.png') ?>" alt="ISCAG Modern Facility">
          </div>
          
          <!-- Secondary Floating Image -->
          <div class="hero-image-card-secondary">
            <img src="<?= asset('assets/ISCAG2.png') ?>" alt="ISCAG Detail 1">
          </div>
          
          <!-- Tertiary Floating Image -->
          <div class="hero-image-card-tertiary">
            <img src="<?= asset('assets/ISCAG3.png') ?>" alt="ISCAG Detail 2">
          </div>
          
          <!-- Quaternary Floating Image -->
          <div class="hero-image-card-quaternary">
            <img src="<?= asset('assets/ISCAG4.png') ?>" alt="ISCAG Detail 3">
          </div>
          
          <!-- Quinary Floating Image -->
          <div class="hero-image-card-quinary">
            <img src="<?= asset('assets/ISCAG5.png') ?>" alt="ISCAG Detail 4">
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══════════════════════ INSIGHTS OVERLAY ══════════════════════ -->
<div class="insights-overlay">
  <div class="container">
    <div class="insights-grid">
      <div class="insight-card reveal">
        <div class="insight-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="insight-content">
          <span class="insight-value">5,000+</span>
          <span class="insight-label">Members</span>
        </div>
      </div>
      <div class="insight-card reveal delay-1">
        <div class="insight-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div class="insight-content">
          <span class="insight-value">25+</span>
          <span class="insight-label">Programs</span>
        </div>
      </div>
      <div class="insight-card reveal delay-2">
        <div class="insight-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="insight-content">
          <span class="insight-value">15+</span>
          <span class="insight-label">Years Service</span>
        </div>
      </div>
      <div class="insight-card reveal delay-3">
        <div class="insight-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        </div>
        <div class="insight-content">
          <span class="insight-value">24/7</span>
          <span class="insight-label">Guidance</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════ MISSION & VISION SECTION ══════════════════════ -->
<section class="mv-section" id="mission-vision">
  <div class="container">
    <div class="mv-header reveal">
      <span class="section-tag">Our Philosophy</span>
      <h2 class="section-title">The Foundation of Our <span>Purpose</span></h2>
    </div>
    <div class="mv-main-grid">
      <div class="mv-card-premium reveal">
        <h3><span></span> Mission</h3>
        <p>Commitment and sense of responsibility in order to address and alleviate the financial, social, health and spiritual needs of the Ummah (Society) through sincere and united efforts to the best we can, without counting the cost.</p>
      </div>
      <div class="mv-card-premium reveal delay-1">
        <h3><span></span> Vision</h3>
        <p>Realization of every endeavor for the Akhirah (Life Hereafter), where each and everyone lives with peace, love, understanding, unity and prosperity, in accordance to the sunnah (Way of the Prophet PBUH).</p>
      </div>
      <div class="objective-bar reveal delay-2">
        <div class="obj-label">Objective</div>
        <div class="obj-text">
          To spearhead the trust of the belief in One God (Allah) as manifested through peace, love, unity and harmony, in co-existence with other creations.
        </div>
      </div>
    </div>
    <div class="motto-banner-wrap reveal delay-3">
      <div class="motto-banner">
        <blockquote>“Good deeds should be done with good intention, not for attention.”</blockquote>
        <span class="author">The ISCAG Motto</span>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════ FOOTER ══════════════════════ -->
<footer>
  <div class="container">
    <div class="footer-grid">
      
      <!-- Column 1: About -->
      <div class="footer-about">
        <a href="#" class="footer-logo">
          <div class="footer-logo-icon">
            <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" style="max-width:100%; max-height:100%; object-fit:scale-down;">
          </div>
          <span class="footer-logo-text">ISCAG<br>PHILIPPINES</span>
        </a>
        <p class="footer-desc">
          Islamic Studies, Call and Guidance of the Philippines is dedicated to empowering the Ummah through knowledge, spiritual growth, and community service.
        </p>
        <div class="footer-social">
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
        </div>
      </div>

      <!-- Column 2: Navigation -->
      <div class="footer-nav">
        <h4 class="footer-title">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="#">Home</a></li>
          <li><a href="#mission-vision">Mission & Vision</a></li>
          <li><a href="#departments">Departments</a></li>
          <li><a href="#">Announcements</a></li>
          <li><a href="#">Contact Us</a></li>
        </ul>
      </div>

      <!-- Column 3: Resources -->
      <div class="footer-nav">
        <h4 class="footer-title">Resources</h4>
        <ul class="footer-links">
          <li><a href="#">Islamic Guidance</a></li>
          <li><a href="#">Community Programs</a></li>
          <li><a href="#">Volunteer Portal</a></li>
          <li><a href="#">Donations</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>

      <!-- Column 4: Contact -->
      <div class="footer-contact">
        <h4 class="footer-title">Get In Touch</h4>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="contact-text">123 Islamic Center Way, Dasmariñas City, Cavite, Philippines</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.18-2.18a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <span class="contact-text">+63 (046) 123 4567</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span class="contact-text">info@iscag.org.ph</span>
        </div>
      </div>

    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> ISCAG Philippines. All Rights Reserved.</p>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>

  </div>
</footer>

<!-- ══════════ SCRIPT ══════════ -->
<script>
  function swapLayout(active, imgSrc = null, imgLabel = null) {
    const previewImg = document.getElementById('deptPreviewImg');
    const labelSpan = document.querySelector('.dept-img-label');
    
    if (active) {
      if (imgSrc) previewImg.src = imgSrc;
      if (imgLabel) labelSpan.textContent = imgLabel;
    } else {
      // Optional: Reset to default when not hovering any item
      previewImg.src = "<?= asset('assets/hero-mosque.png') ?>";
      labelSpan.textContent = "ISCAG Departments";
    }
  }

  // Add smooth transition to dept layout grid columns
  const deptLayout = document.getElementById('deptLayout');
  deptLayout.style.transition = 'grid-template-columns 280ms cubic-bezier(.4,0,.2,1)';

  // Make nav-link active highlight follow clicks
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => {
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });

  // Hero Reveal Animation on Scroll/Load
  function reveal() {
    var reveals = document.querySelectorAll(".reveal");
    var windowHeight = window.innerHeight;
    
    for (var i = 0; i < reveals.length; i++) {
      var elementTop = reveals[i].getBoundingClientRect().top;
      var elementVisible = 40; // Low threshold for better trigger reliability
      
      if (elementTop < windowHeight - elementVisible) {
        reveals[i].classList.add("active");
      }
    }
  }

  // Trigger reveal on load, scroll, and resize
  window.addEventListener("scroll", reveal);
  window.addEventListener("resize", reveal);
  window.addEventListener("DOMContentLoaded", reveal);
  
  // Extra safety: trigger after a short timeout to ensure layout is ready
  setTimeout(reveal, 150);

  window.addEventListener("scroll", reveal);
  // Initial check
  window.addEventListener("load", reveal);
  reveal(); // Trigger once on script load
</script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
