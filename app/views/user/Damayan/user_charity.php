<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Charity & Donation</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <style>
    .charity-hero {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      border-radius: 24px;
      padding: 60px 40px;
      color: white;
      margin-bottom: 32px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(15, 92, 58, 0.2);
    }
    .charity-hero::after {
      content: '';
      position: absolute;
      top: -50%; right: -20%;
      width: 400px; height: 400px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }
    .charity-hero h2 { font-family: 'Lora', serif; font-size: 2.2rem; margin-bottom: 12px; position: relative; z-index: 1; }
    .charity-hero p { font-size: 1.1rem; opacity: 0.9; max-width: 600px; line-height: 1.6; position: relative; z-index: 1; }

    .donation-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 24px;
      margin-bottom: 40px;
    }

    .donation-card {
      background: white;
      border-radius: 20px;
      padding: 32px;
      border: 1px solid var(--border);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .donation-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); border-color: var(--primary-light); }
    
    .card-icon {
      width: 56px; height: 56px;
      background: rgba(39, 174, 96, 0.1);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
    }
    .card-icon svg { width: 28px; height: 28px; fill: var(--primary); }

    .card-content h4 { font-family: 'Lora', serif; font-size: 1.25rem; color: var(--primary-dark); margin-bottom: 8px; }
    .card-content p { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; }

    .donation-input-group {
      margin-top: auto;
    }
    .amount-presets {
      display: flex;
      gap: 10px;
      margin-bottom: 16px;
    }
    .preset-btn {
      flex: 1;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: #f8faf9;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--text-main);
      cursor: pointer;
      transition: all 0.2s;
    }
    .preset-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(39, 174, 96, 0.05); }
    .preset-btn.active { background: var(--primary); color: white; border-color: var(--primary); }

    .custom-amount {
      position: relative;
    }
    .custom-amount input {
      width: 100%;
      padding: 14px 16px 14px 40px;
      border-radius: 12px;
      border: 2px solid var(--border);
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
      outline: none;
      transition: all 0.3s;
    }
    .custom-amount input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1); }
    .custom-amount::before {
      content: '₱';
      position: absolute;
      left: 16px; top: 50%;
      transform: translateY(-50%);
      font-weight: 700;
      color: var(--text-muted);
    }

    .btn-donate {
      width: 100%;
      padding: 14px;
      border-radius: 12px;
      background: var(--primary);
      color: white;
      font-weight: 700;
      border: none;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: all 0.3s;
      margin-top: 20px;
    }
    .btn-donate:hover { background: var(--primary-dark); transform: scale(1.02); box-shadow: 0 8px 20px rgba(15, 92, 58, 0.25); }

    /* ── Events & Announcements ── */
    .events-section { margin-top: 40px; }
    .events-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid var(--border); }
    .events-header h3 { font-family: 'Lora', serif; font-size: 1.5rem; color: var(--primary-dark); }
    
    .event-card {
      display: flex; gap: 24px; background: white; border-radius: 20px; padding: 24px;
      border: 1px solid var(--border); margin-bottom: 20px; transition: all 0.3s;
      position: relative; overflow: hidden;
    }
    .event-card:hover { border-color: var(--primary); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    
    .event-date {
      width: 70px; height: 70px; background: #f8faf9; border-radius: 16px;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      flex-shrink: 0; border: 1px solid var(--border);
    }
    .event-date .day { font-size: 1.4rem; font-weight: 800; color: var(--primary); line-height: 1; }
    .event-date .month { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }

    .event-content { flex: 1; }
    .event-tag { 
      display: inline-block; padding: 4px 10px; border-radius: 20px; 
      font-size: 0.65rem; font-weight: 700; text-transform: uppercase; 
      margin-bottom: 8px;
    }
    .tag-announcement { background: rgba(199, 154, 43, 0.1); color: #c79a2b; }
    .tag-event { background: rgba(39, 174, 96, 0.1); color: var(--primary); }

    .event-title { font-size: 1.1rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 6px; }
    .event-desc { font-size: 0.88rem; color: var(--text-muted); line-height: 1.6; }
    
    .event-meta { display: flex; gap: 16px; margin-top: 12px; font-size: 0.8rem; color: var(--text-muted); }
    .event-meta-item { display: flex; align-items: center; gap: 6px; }
    .event-meta-item svg { width: 14px; height: 14px; fill: currentColor; }

    /* ── Event Card Images ── */
    .event-img-wrap {
      width: 180px; height: 100%; min-height: 140px; flex-shrink: 0;
      border-radius: 12px; overflow: hidden; position: relative;
    }
    .event-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    
    @media (max-width: 768px) {
      .event-card { flex-direction: column; }
      .event-img-wrap { width: 100%; height: 180px; }
      .event-date { position: absolute; top: 12px; left: 12px; z-index: 2; background: white; }
    }

    /* ── Campaign Cards ── */
    .campaign-section { margin-bottom: 48px; }
    .campaign-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
    .campaign-card { 
      background: white; border-radius: 20px; overflow: hidden; border: 1px solid var(--border);
      transition: all 0.3s; display: flex; flex-direction: column;
    }
    .campaign-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); border-color: var(--primary); }
    .campaign-img { width: 100%; height: 160px; background: #eee; object-fit: cover; }
    .campaign-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
    .campaign-title { font-size: 1rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 8px; }
    .campaign-desc { font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 16px; }
    .campaign-progress-bar { height: 8px; background: #f0f2f1; border-radius: 4px; margin-bottom: 8px; overflow: hidden; }
    .campaign-progress-fill { height: 100%; background: var(--primary); border-radius: 4px; }
    .campaign-stats { display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; }
    .campaign-raised { color: var(--primary); }
    .campaign-goal { color: var(--text-muted); }

    /* ── Service Icons ── */
    .service-mini-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 32px; }
    .service-mini-card {
      background: #f8faf9; border: 1px solid var(--border); border-radius: 16px; padding: 20px;
      text-align: center; transition: all 0.3s;
    }
    .service-mini-card:hover { background: white; border-color: var(--primary); transform: translateY(-3px); }
    .service-mini-card svg { width: 32px; height: 32px; fill: var(--primary); margin-bottom: 12px; }
    .service-mini-card h5 { font-size: 0.95rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 4px; }
    .service-mini-card p { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

    /* ── Hero Carousel (Full Size) ── */
    .charity-hero {
      height: 400px; padding: 0 !important;
      background: #111; overflow: hidden;
      position: relative; border-radius: 24px;
      margin-bottom: 32px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }
    
    .hero-carousel-bg {
      position: absolute; inset: 0; z-index: 1;
    }
    .hero-carousel-bg::after {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(to right, rgba(15, 92, 58, 0.9), rgba(15, 92, 58, 0.4), rgba(0,0,0,0.3));
      z-index: 2;
    }

    .hero-content-overlay {
      position: relative; z-index: 3;
      color: white; padding: 60px; width: 100%;
    }

    .carousel-track {
      display: flex; width: 300%; height: 100%;
      transition: transform 1.2s cubic-bezier(0.65, 0, 0.35, 1);
    }
    .carousel-slide { width: 33.333%; height: 100%; flex-shrink: 0; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; }
    
    .carousel-dots {
      position: absolute; bottom: 24px; left: 60px;
      display: flex; gap: 8px; z-index: 4;
    }
    .dot {
      width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.3);
      transition: all 0.4s; cursor: pointer;
    }
    .dot.active { width: 24px; border-radius: 4px; background: var(--accent); }
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
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Damayan Charity</div>
        <div class="top-bar-subtitle">Support our community through charitable giving and donations</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      <div class="breadcrumb-bar">
        <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
        <span class="sep">›</span>
        <span class="current">Charity & Donation</span>
      </div>

      <div class="charity-hero">
        <div class="hero-carousel-bg">
          <div class="carousel-track" id="carousel-track">
            <div class="carousel-slide"><img src="<?= asset('assets/islamic_charity_hero_1777627730583.png') ?>" alt="Hero 1" /></div>
            <div class="carousel-slide"><img src="<?= asset('assets/charity_community_help_1777632237422.png') ?>" alt="Hero 2" /></div>
            <div class="carousel-slide"><img src="<?= asset('assets/masjid_peace_charity_1777632254116.png') ?>" alt="Hero 3" /></div>
          </div>
        </div>

        <div class="hero-content-overlay">
          <div style="margin-bottom:20px;">
            <h2 style="font-size:3.2rem; margin:0; line-height:1.1; font-weight:900; letter-spacing:-0.02em;">DAMAYAN PROJECT</h2>
            <div style="font-size:1.4rem; font-weight:700; color:var(--accent); letter-spacing:0.2em; margin-top:4px;">ISCAG-PHILIPPINES</div>
          </div>
          <p style="font-size:1.25rem; max-width:650px; font-weight:500; font-style:italic; line-height:1.6; opacity:0.98;">"GOOD DEEDS SHOULD BE DONE WITH GOOD INTENTION NOT FOR ATTENTION"</p>
          
          <div style="margin-top:32px; display:flex; gap:24px;">
            <div style="display:flex; align-items:center; gap:10px; font-size:0.9rem; font-weight:700;">
              <div style="width:36px; height:36px; border-radius:50%; background:rgba(199,154,43,0.2); display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              </div>
              Trusted Management
            </div>
            <div style="display:flex; align-items:center; gap:10px; font-size:0.9rem; font-weight:700;">
              <div style="width:36px; height:36px; border-radius:50%; background:rgba(199,154,43,0.2); display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              </div>
              100% Impact
            </div>
          </div>
        </div>

        <div class="carousel-dots">
          <div class="dot active"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
      </div>

      <!-- Core Services Overview -->
      <div class="services-overview" style="margin-bottom: 48px;">
        <div class="events-header">
          <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:var(--primary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
          <h3>How We Support the Community</h3>
        </div>
        
        <div class="service-mini-grid">
          <div class="service-mini-card">
            <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
            <h5>Burial Support</h5>
            <p>Exclusive Islamic burial services and financial aid for families in need.</p>
          </div>
          <div class="service-mini-card">
            <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            <h5>Welfare Aid</h5>
            <p>Immediate financial assistance for medical emergencies and food security.</p>
          </div>
          <div class="service-mini-card">
            <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
            <h5>Education</h5>
            <p>Scholarships and supply kits for underprivileged students and orphans.</p>
          </div>
          <div class="service-mini-card">
            <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            <h5>Counseling</h5>
            <p>Spiritual and social guidance to help community members navigate life's challenges.</p>
          </div>
        </div>
      </div>

      <!-- Active Campaigns Section -->
      <div class="campaign-section">
        <div class="events-header">
          <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:var(--primary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.82v-1.91c-1.83-.41-3.3-1.68-3.3-3.61h2.09c.06 1.11.72 1.85 2.11 1.85 1.7 0 2.21-.86 2.21-1.85 0-1.12-.51-1.85-2.21-2.27-2.61-.63-3.61-1.34-3.61-3.61 0-1.94 1.47-3.21 3.3-3.61V3.91h2.82v1.91c1.61.35 2.82 1.5 2.82 3.61h-2.09c-.06-1.11-.64-1.85-1.85-1.85-1.21 0-2.11.51-2.11 1.85 0 1.06.51 1.7 2.11 2.11 2.76.63 3.61 1.55 3.61 3.61 0 1.94-1.15 3.3-3.3 3.95z"/></svg>
          <h3>Active Campaigns & Impact</h3>
        </div>
        
        <div class="campaign-grid">
          <!-- Campaign 1 -->
          <div class="campaign-card">
            <div style="background:var(--primary-light); height:160px; display:flex; align-items:center; justify-content:center; color:white;">
              <svg viewBox="0 0 24 24" style="width:64px;height:64px;fill:currentColor;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <div class="campaign-body">
              <div class="campaign-title">Educational Support Fund</div>
              <p class="campaign-desc">Providing school supplies, books, and uniforms for orphan children in our community for the upcoming academic year.</p>
              <div class="campaign-progress-bar">
                <div class="campaign-progress-fill" style="width: 65%;"></div>
              </div>
              <div class="campaign-stats">
                <span class="campaign-raised">₱32,500 Raised</span>
                <span class="campaign-goal">Goal: ₱50,000</span>
              </div>
            </div>
          </div>

          <!-- Campaign 2 -->
          <div class="campaign-card">
            <div style="background:var(--accent); height:160px; display:flex; align-items:center; justify-content:center; color:white;">
              <svg viewBox="0 0 24 24" style="width:64px;height:64px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm0-8h-2V7h2v2zm4 8h-2V7h2v10z"/></svg>
            </div>
            <div class="campaign-body">
              <div class="campaign-title">Mosque Renovation Project</div>
              <p class="campaign-desc">Funding the repair and maintenance of the prayer halls and ablution areas to ensure a comfortable space for all worshippers.</p>
              <div class="campaign-progress-bar">
                <div class="campaign-progress-fill" style="width: 40%;"></div>
              </div>
              <div class="campaign-stats">
                <span class="campaign-raised">₱80,000 Raised</span>
                <span class="campaign-goal">Goal: ₱200,000</span>
              </div>
            </div>
          </div>

          <!-- Campaign 3 -->
          <div class="campaign-card">
            <div style="background:var(--primary); height:160px; display:flex; align-items:center; justify-content:center; color:white;">
              <svg viewBox="0 0 24 24" style="width:64px;height:64px;fill:currentColor;"><path d="M19 13H5v-2h14v2z"/></svg>
            </div>
            <div class="campaign-body">
              <div class="campaign-title">Ramadan Food Packs</div>
              <p class="campaign-desc">Distributing essential food packages to underprivileged families to support their Suhoor and Iftar throughout the holy month.</p>
              <div class="campaign-progress-bar">
                <div class="campaign-progress-fill" style="width: 85%;"></div>
              </div>
              <div class="campaign-stats">
                <span class="campaign-raised">₱127,500 Raised</span>
                <span class="campaign-goal">Goal: ₱150,000</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Events & Announcements Section (Moved Up for Prominence) -->
      <div class="events-section" style="margin-bottom: 48px;">
        <div class="events-header">
          <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:var(--primary);"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
          <h3>Upcoming Events & Announcements</h3>
        </div>

        <div class="events-container">
          <!-- Sample Event 1 -->
          <div class="event-card">
            <div class="event-img-wrap">
              <img src="<?= asset('assets/charity_food_drive_1777627697536.png') ?>" alt="Food Drive" />
            </div>
            <div class="event-date">
              <span class="day">15</span>
              <span class="month">May</span>
            </div>
            <div class="event-content">
              <span class="event-tag tag-event">Upcoming Event</span>
              <div class="event-title">Community Iftar & Food Drive</div>
              <p class="event-desc">Join us for a community-wide Iftar gathering. We will also be collecting non-perishable food items for local families in need. Everyone is welcome to participate and contribute.</p>
              <div class="event-meta">
                <div class="event-meta-item">
                  <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  ISCAG Main Hall
                </div>
                <div class="event-meta-item">
                  <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                  5:30 PM - 8:30 PM
                </div>
              </div>
            </div>
          </div>

          <!-- Sample Event 2 (Medical Mission) -->
          <div class="event-card">
            <div class="event-img-wrap">
              <img src="<?= asset('assets/medical_mission_event_1777627714762.png') ?>" alt="Medical Mission" />
            </div>
            <div class="event-date">
              <span class="day">22</span>
              <span class="month">May</span>
            </div>
            <div class="event-content">
              <span class="event-tag tag-event">Upcoming Event</span>
              <div class="event-title">Medical Mission for Reverts</div>
              <p class="event-desc">In partnership with local health professionals, ISCAG will be hosting a free medical check-up and consultation day specifically for our revert brothers and sisters.</p>
              <div class="event-meta">
                <div class="event-meta-item">
                  <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                  ISCAG Clinic Area
                </div>
                <div class="event-meta-item">
                  <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                  8:00 AM - 12:00 PM
                </div>
              </div>
            </div>
          </div>

          <!-- Sample Announcement 1 -->
          <div class="event-card">
            <div class="event-date" style="background:rgba(199, 154, 43, 0.1); border-color:rgba(199, 154, 43, 0.3);">
              <span class="day" style="color:#c79a2b;">!</span>
              <span class="month">Notice</span>
            </div>
            <div class="event-content">
              <span class="event-tag tag-announcement">Announcement</span>
              <div class="event-title">New Burial Assistance Hotline</div>
              <p class="event-desc">The Damayan department has launched a 24/7 hotline for emergency burial assistance. Please save this number for immediate coordination: +63 9XX XXX XXXX.</p>
              <div class="event-meta">
                <div class="event-meta-item">
                  <svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                  Public Notice
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <h6 style="font-family:'Lora',serif;font-size:0.9rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px;">Make a General Donation</h6>

      <div class="donation-grid">
        <!-- Sadaqah Jariyah -->
        <div class="donation-card">
          <div class="card-icon">
            <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          </div>
          <div class="card-content">
            <h4>Sadaqah Jariyah</h4>
            <p>Support ongoing community projects, facility maintenance, and long-term educational programs that continue to benefit others.</p>
          </div>
          <div class="donation-input-group">
            <div class="amount-presets">
              <button class="preset-btn" onclick="setAmount(this, 500)">₱500</button>
              <button class="preset-btn" onclick="setAmount(this, 1000)">₱1,000</button>
              <button class="preset-btn" onclick="setAmount(this, 2500)">₱2,500</button>
            </div>
            <div class="custom-amount">
              <input type="number" placeholder="Other Amount" oninput="clearPresets(this.parentElement.parentElement)" />
            </div>
            <button class="btn-donate" onclick="processDonation('Sadaqah Jariyah', this.previousElementSibling.querySelector('input').value)">
              Donate Now
            </button>
          </div>
        </div>

        <!-- Burial Assistance Fund -->
        <div class="donation-card">
          <div class="card-icon">
            <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div class="card-content">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
              <h4 style="margin:0;">Burial Assistance</h4>
              <span style="font-size:0.65rem; font-weight:800; background:rgba(214, 48, 49, 0.1); color:#d63031; padding:2px 8px; border-radius:4px; text-transform:uppercase; letter-spacing:0.05em;">Exclusive for Islam</span>
            </div>
            <p>Directly help families in need with burial expenses and funeral services according to Islamic traditions. Your contribution eases the burden of those grieving.</p>
          </div>
          <div class="donation-input-group">
            <div class="amount-presets">
              <button class="preset-btn" onclick="setAmount(this, 1000)">₱1k</button>
              <button class="preset-btn" onclick="setAmount(this, 5000)">₱5k</button>
              <button class="preset-btn" onclick="setAmount(this, 10000)">₱10k</button>
            </div>
            <div class="custom-amount">
              <input type="number" placeholder="Other Amount" />
            </div>
            <button class="btn-donate" onclick="processDonation('Burial Assistance', this.previousElementSibling.querySelector('input').value)">
              Donate Now
            </button>
          </div>
        </div>

        <!-- General Charity -->
        <div class="donation-card">
          <div class="card-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5s2.01-4.5 4.5-4.5 4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5z"/></svg>
          </div>
          <div class="card-content">
            <h4>General Charity</h4>
            <p>Flexible funding for immediate community needs, emergency relief, and various benevolent activities under the Damayan department.</p>
          </div>
          <div class="donation-input-group">
            <div class="amount-presets">
              <button class="preset-btn" onclick="setAmount(this, 100)">₱100</button>
              <button class="preset-btn" onclick="setAmount(this, 200)">₱200</button>
              <button class="preset-btn" onclick="setAmount(this, 500)">₱500</button>
            </div>
            <div class="custom-amount">
              <input type="number" placeholder="Other Amount" />
            </div>
            <button class="btn-donate" onclick="processDonation('General Charity', this.previousElementSibling.querySelector('input').value)">
              Donate Now
            </button>
          </div>
        </div>
      </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  function setAmount(btn, amt) {
    const group = btn.parentElement.parentElement;
    group.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    group.querySelector('input').value = amt;
  }

  function clearPresets(group) {
    group.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
  }

  function processDonation(type, amt) {
    if (!amt || amt <= 0) {
      if (typeof showToast === 'function') showToast('Please enter a valid donation amount.', '#e67e22');
      return;
    }

    const confirmed = confirm(`You are about to donate ₱${Number(amt).toLocaleString()} to ${type}. Proceed?`);
    if (confirmed) {
      if (typeof showToast === 'function') {
        showToast(`Jazakumullahu Khayran! Your donation for ${type} has been received.`, 'var(--success)');
      }
      setTimeout(() => {
        window.location.href = '<?= url('/user/dashboard') ?>';
      }, 2000);
    }
  }

    function showToast(msg, bg) {
      const toast = document.createElement('div');
      toast.textContent = msg;
      toast.style.cssText = 'position:fixed;top:24px;right:24px;background:' + (bg || 'var(--primary)') + ';color:white;padding:14px 22px;border-radius:10px;z-index:99999;font-weight:600;font-family:inherit;font-size:0.9rem;box-shadow:0 4px 16px rgba(0,0,0,0.18);max-width:400px;animation:fadeIn 0.3s ease;';
      document.body.appendChild(toast);
      setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 4000);
    }

    // ── Hero Carousel Logic ──
    const track = document.getElementById('carousel-track');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;

    function nextSlide() {
      currentSlide = (currentSlide + 1) % 3;
      track.style.transform = `translateX(-${currentSlide * 33.333}%)`;
      dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
    }

    setInterval(nextSlide, 5000);
  </script>
</body>
</html>
