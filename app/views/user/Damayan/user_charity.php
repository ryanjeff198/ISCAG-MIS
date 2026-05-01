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
      --primary-light: #176b45;
      --accent-gold: #c79a2b;
      --accent-soft: #f4e8c1;
      --text-dark: #1f2e2a;
      --text-muted: #6f7f78;
      --bg-soft: #f8faf9;
      --border-light: #e8ece9;
    }

    body { font-family: 'Source Sans 3', sans-serif; background-color: var(--bg-soft); color: var(--text-dark); }

    /* ── Typography & Section Headers ── */
    h1, h2, h3, h4, .serif { font-family: 'Lora', serif; }
    
    .section-header { text-align: center; margin-bottom: 48px; }
    .section-header h2 { font-size: 2.2rem; color: var(--primary-green); margin-bottom: 12px; }
    .section-header p { font-size: 1.1rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; line-height: 1.6; }

    /* ── Premium Hero Section ── */
    .charity-hero {
      height: 520px; border-radius: 32px; overflow: hidden; position: relative;
      margin-bottom: 56px; box-shadow: 0 30px 60px rgba(15, 92, 58, 0.15);
      background: #111;
    }
    
    .hero-carousel-bg { position: absolute; inset: 0; z-index: 1; }
    .hero-carousel-bg::after {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(to right, rgba(15, 92, 58, 0.95) 0%, rgba(15, 92, 58, 0.4) 50%, rgba(0,0,0,0.2) 100%);
      z-index: 2;
    }

    .carousel-track { display: flex; width: 300%; height: 100%; transition: transform 1.4s cubic-bezier(0.65, 0, 0.35, 1); }
    .carousel-slide { width: 33.333%; height: 100%; flex-shrink: 0; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; filter: saturate(1.1); }

    .hero-content-overlay {
      position: relative; z-index: 3; color: white; padding: 80px 60px;
      height: 100%; display: flex; flex-direction: column; justify-content: center;
    }

    .hero-tag {
      display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px;
      background: rgba(199, 154, 43, 0.2); border: 1px solid rgba(199, 154, 43, 0.4);
      border-radius: 100px; font-size: 0.8rem; font-weight: 800; color: var(--accent-gold);
      text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 24px; width: fit-content;
    }

    .hero-content-overlay h2 { font-size: 3.8rem; line-height: 1.05; font-weight: 800; margin-bottom: 20px; letter-spacing: -0.02em; }
    .hero-content-overlay p { font-size: 1.35rem; max-width: 580px; line-height: 1.6; opacity: 0.95; font-style: italic; }

    .hero-actions { display: flex; gap: 20px; margin-top: 40px; }
    .btn-hero { 
      padding: 16px 36px; border-radius: 14px; font-weight: 800; font-size: 1rem; 
      cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-decoration: none;
    }
    .btn-hero.primary { background: var(--accent-gold); color: white; border: none; box-shadow: 0 10px 25px rgba(199, 154, 43, 0.3); }
    .btn-hero.primary:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(199, 154, 43, 0.45); filter: brightness(1.1); }
    .btn-hero.secondary { background: transparent; color: white; border: 2px solid rgba(255,255,255,0.4); }
    .btn-hero.secondary:hover { background: rgba(255,255,255,0.1); border-color: white; transform: translateY(-3px); }

    .carousel-dots {
      position: absolute; bottom: 40px; left: 60px; display: flex; gap: 12px; z-index: 4;
    }
    .dot {
      width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.25);
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; border: 1px solid transparent;
    }
    .dot.active { width: 32px; border-radius: 6px; background: white; border-color: var(--accent-gold); }

    /* ── Schedule & Announcements ── */
    .schedule-section { margin-bottom: 80px; }
    .schedule-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; }
    
    .schedule-list { display: flex; flex-direction: column; gap: 20px; }
    .schedule-item {
      display: flex; gap: 20px; background: white; border-radius: 20px; padding: 24px;
      border: 1px solid var(--border-light); transition: all 0.3s;
    }
    .schedule-item:hover { border-color: var(--primary-green); transform: translateX(8px); }
    
    .schedule-date {
      width: 64px; height: 64px; background: var(--bg-soft); border-radius: 14px;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      flex-shrink: 0; border: 1px solid var(--border-light);
    }
    .schedule-date .day { font-size: 1.4rem; font-weight: 800; color: var(--primary-green); line-height: 1; }
    .schedule-date .month { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); }

    .announcement-card {
      background: white; border-radius: 24px; padding: 40px;
      border: 1px solid var(--border-light); position: relative; overflow: hidden;
      box-shadow: 0 20px 50px rgba(0,0,0,0.03);
    }
    .announcement-card::before {
      content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%;
      background: var(--accent-gold);
    }
    .announcement-tag {
      display: inline-block; padding: 6px 12px; border-radius: 8px;
      background: var(--accent-soft); color: var(--accent-gold);
      font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 16px;
    }

    /* ── Impact Metrics (Where Your Money Goes) ── */
    .impact-ribbon {
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px;
      background: white; border-radius: 24px; padding: 48px;
      margin-bottom: 64px; box-shadow: 0 20px 50px rgba(0,0,0,0.03);
      border: 1px solid var(--border-light);
    }
    .metric-item { text-align: center; }
    .metric-value { font-size: 2.8rem; font-weight: 800; color: var(--primary-green); margin-bottom: 4px; display: block; }
    .metric-label { font-size: 0.9rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }

    /* ── Donation Blocks (Contextualized) ── */
    .donation-section { margin-bottom: 80px; }
    .donation-experience {
      display: grid; grid-template-columns: 1fr 400px; gap: 48px;
      align-items: start;
    }
    
    .donation-context h3 { font-size: 1.8rem; color: var(--primary-green); margin-bottom: 16px; }
    .donation-context p { font-size: 1.05rem; line-height: 1.7; color: var(--text-muted); margin-bottom: 32px; }

    .impact-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .impact-mini-card {
      background: white; border-radius: 20px; padding: 24px;
      border: 1.5px solid var(--border-light); transition: all 0.3s;
    }
    .impact-mini-card:hover { border-color: var(--primary-green); transform: translateY(-4px); }
    .impact-mini-card .amt { font-weight: 900; color: var(--primary-green); font-size: 1.4rem; margin-bottom: 8px; display: block; }
    .impact-mini-card .desc { font-size: 0.85rem; line-height: 1.5; font-weight: 600; color: var(--text-muted); }

    /* ── Donation Form Sidebar ── */
    .donation-form-card {
      background: white; border-radius: 24px; padding: 32px;
      box-shadow: 0 30px 70px rgba(15, 92, 58, 0.08);
      border: 1px solid var(--border-light); position: sticky; top: 100px;
    }
    
    .frequency-toggle {
      display: flex; background: #f0f4f2; padding: 6px; border-radius: 14px;
      margin-bottom: 32px;
    }
    .freq-btn {
      flex: 1; padding: 12px; border-radius: 10px; border: none;
      font-size: 0.9rem; font-weight: 700; color: var(--text-muted);
      background: transparent; cursor: pointer; transition: all 0.2s;
    }
    .freq-btn.active { background: white; color: var(--primary-green); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    .amount-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .amt-btn {
      padding: 18px; border-radius: 12px; border: 2px solid var(--border-light);
      background: white; font-size: 1.1rem; font-weight: 800; color: var(--text-dark);
      cursor: pointer; transition: all 0.2s;
    }
    .amt-btn:hover { border-color: var(--primary-green); background: rgba(15, 92, 58, 0.02); }
    .amt-btn.active { background: var(--primary-green); color: white; border-color: var(--primary-green); }

    .custom-input-wrap { position: relative; margin-bottom: 32px; }
    .custom-input-wrap span { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-weight: 800; color: var(--text-muted); }
    .custom-input-wrap input {
      width: 100%; padding: 20px 20px 20px 40px; border-radius: 14px;
      border: 2px solid var(--border-light); outline: none;
      font-size: 1.2rem; font-weight: 800; color: var(--primary-green); transition: all 0.3s;
    }
    .custom-input-wrap input:focus { border-color: var(--primary-green); box-shadow: 0 0 0 5px rgba(15, 92, 58, 0.08); }

    .btn-submit-donation {
      width: 100%; padding: 20px; border-radius: 16px; border: none;
      background: linear-gradient(135deg, var(--primary-green), var(--primary-light));
      color: white; font-size: 1.1rem; font-weight: 800; cursor: pointer;
      box-shadow: 0 15px 35px rgba(15, 92, 58, 0.2); transition: all 0.3s;
    }
    .btn-submit-donation:hover { transform: translateY(-3px); box-shadow: 0 20px 45px rgba(15, 92, 58, 0.3); filter: brightness(1.1); }

    .trust-badges {
      display: flex; justify-content: center; gap: 24px; margin-top: 32px; opacity: 0.6;
    }
    .trust-badges svg { height: 20px; fill: var(--text-muted); }

    /* ── Impact Stories (Grid) ── */
    .stories-section { margin-bottom: 80px; }
    .story-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
    .story-card {
      background: white; border-radius: 24px; overflow: hidden;
      border: 1px solid var(--border-light); transition: all 0.3s;
    }
    .story-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.06); }
    .story-image { height: 240px; overflow: hidden; }
    .story-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
    .story-card:hover .story-image img { transform: scale(1.08); }
    .story-content { padding: 28px; }
    .story-content .date { font-size: 0.75rem; font-weight: 800; color: var(--accent-gold); text-transform: uppercase; margin-bottom: 12px; display: block; }
    .story-content h4 { font-size: 1.3rem; color: var(--primary-green); margin-bottom: 12px; line-height: 1.3; }
    .story-content p { font-size: 0.92rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; }
    .btn-read-more { font-size: 0.85rem; font-weight: 800; color: var(--primary-green); text-decoration: none; display: flex; align-items: center; gap: 6px; }
    .btn-read-more:hover { text-decoration: underline; }

    /* ── Non-Stop Projects Ticker ── */
    .projects-carousel-container { position: relative; margin-bottom: 80px; overflow: hidden; }
    .projects-viewport { overflow: hidden; border-radius: 24px; padding: 10px 0; }
    
    .projects-track { 
      display: flex; gap: 32px; 
      width: max-content;
      animation: ticker-scroll 40s linear infinite;
    }
    .projects-track:hover { animation-play-state: paused; }
    
    .projects-track .story-card { 
      flex: 0 0 350px; 
    }
    
    @keyframes ticker-scroll {
      0% { transform: translateX(0); }
      100% { transform: translateX(calc(-350px * 4 - 32px * 4)); }
    }
    
    /* Hide nav buttons for non-stop ticker */
    .carousel-nav { display: none; }

    /* ── Partner / Transparency Footer ── */
    .transparency-section {
      background: white; border-radius: 32px; padding: 64px; text-align: center;
      margin-bottom: 40px; border: 1px solid var(--border-light);
    }
    .partner-logos {
      display: flex; justify-content: center; align-items: center; gap: 60px; margin-top: 40px;
      flex-wrap: wrap; opacity: 0.4; filter: grayscale(1);
    }
    .partner-logos img { height: 32px; }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
      .donation-experience { grid-template-columns: 1fr; }
      .donation-form-card { position: static; max-width: 500px; margin: 0 auto; }
      .metric-ribbon { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 768px) {
      .hero-content-overlay h2 { font-size: 2.8rem; }
      .hero-content-overlay p { font-size: 1.1rem; }
      .story-grid { grid-template-columns: 1fr; }
      .impact-ribbon { grid-template-columns: 1fr 1fr; padding: 24px; gap: 16px; }
      .metric-value { font-size: 1.8rem; }
      .charity-hero { height: auto; min-height: 480px; }
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
    <div class="top-bar">
      <div>
        <div class="top-bar-title">Damayan Charity</div>
        <div class="top-bar-subtitle">Transforming lives through Islamic compassionate giving</div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
      </div>
    </div>

    <div class="page-body">
      
      <!-- ═══ Hero Section ═══ -->
      <section class="charity-hero" aria-label="Hero Section">
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
          <p>"Good deeds should be done with good intention, <br>not for attention." — ISCAG Damayan Project</p>
          
          <div class="hero-actions">
            <a href="#donate" class="btn-hero primary">Donate Now</a>
          </div>
        </div>

        <div class="carousel-dots">
          <div class="dot active" onclick="gotoSlide(0)"></div>
          <div class="dot" onclick="gotoSlide(1)"></div>
          <div class="dot" onclick="gotoSlide(2)"></div>
        </div>
      </section>

      <!-- ═══ Our Completed Projects (Carousel) ═══ -->
      <section class="projects-gallery projects-carousel-container">
        <div class="section-header" style="margin-top: 40px;">
          <h2>Our Impact in Action</h2>
          <p>A glimpse into the successful programs and community projects we've completed recently.</p>
        </div>

        <div class="projects-viewport">
          <div class="projects-track">
            <!-- Original Set -->
            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero2.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">April 2023</span>
                <h4>Ramadan Food Distribution</h4>
                <p>Providing food packs to indigent families across Metro Manila.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 2,000+ Fed</div>
              </div>
            </article>

            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero3.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">June 2023</span>
                <h4>Annual Medical Mission</h4>
                <p>Comprehensive health check-up event serving the revert community.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 350+ Patients</div>
              </div>
            </article>

            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">August 2023</span>
                <h4>Back-to-School Supply Kits</h4>
                <p>Distributed educational sets to orphaned students.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 120 Scholarships</div>
              </div>
            </article>
            
            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/ISCAG2.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">December 2023</span>
                <h4>Winter Warmth Initiative</h4>
                <p>Distribution of blankets and clothing to elderly community members.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 450+ Helped</div>
              </div>
            </article>

            <!-- Duplicate Set for Seamless Ticker -->
            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero2.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">April 2023</span>
                <h4>Ramadan Food Distribution</h4>
                <p>Providing food packs to indigent families across Metro Manila.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 2,000+ Fed</div>
              </div>
            </article>

            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero3.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">June 2023</span>
                <h4>Annual Medical Mission</h4>
                <p>Comprehensive health check-up event serving the revert community.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 350+ Patients</div>
              </div>
            </article>

            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/damayanHero.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">August 2023</span>
                <h4>Back-to-School Supply Kits</h4>
                <p>Distributed educational sets to orphaned students.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 120 Scholarships</div>
              </div>
            </article>
            
            <article class="story-card">
              <div class="story-image"><img src="<?= asset('assets/ISCAG2.png') ?>" alt="Project"></div>
              <div class="story-content">
                <span class="date">December 2023</span>
                <h4>Winter Warmth Initiative</h4>
                <p>Distribution of blankets and clothing to elderly community members.</p>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary-green);">Outcome: 450+ Helped</div>
              </div>
            </article>
          </div>
        </div>

        </div>
      </section>

      <!-- ═══ Donation Experience ═══ -->
      <section class="donation-section" id="donate">
        <div class="section-header">
          <h2>Your Gift, Their Future</h2>
          <p>Choose a program and an amount to make an immediate difference in our community.</p>
        </div>

        <div class="donation-experience">
          <div class="donation-context">
            <h3>How Your Donation Helps</h3>
            <p>Every peso you contribute goes directly to programs that provide spiritual, financial, and emotional support to those who need it most within the ISCAG community.</p>
            
            <div class="impact-cards-grid">
              <article class="impact-mini-card">
                <span class="amt">₱500</span>
                <span class="desc">Provides a complete Food Pack for a family of four for one week.</span>
              </article>
              <article class="impact-mini-card">
                <span class="amt">₱2,500</span>
                <span class="desc">Supports a student's educational supplies for a full semester.</span>
              </article>
              <article class="impact-mini-card">
                <span class="amt">₱10,000</span>
                <span class="desc">Subsidizes Islamic burial services for an underprivileged family.</span>
              </article>
              <article class="impact-mini-card">
                <span class="amt">₱Custom</span>
                <span class="desc">Contributes to the general welfare and emergency relief fund.</span>
              </article>
            </div>

            <div style="margin-top:40px; padding:32px; border-radius:24px; background:white; border:1px solid var(--border-light);">
              <h4 style="margin-bottom:12px; color:var(--primary-green);">Where Your Money Goes</h4>
              <p style="font-size:0.92rem; color:var(--text-muted); margin-bottom:20px;">We pride ourselves on our lean operations. 95% of every donation goes directly to field programs.</p>
              <div style="height:12px; background:#f0f4f2; border-radius:100px; display:flex; overflow:hidden;">
                <div style="width:95%; background:var(--primary-green);" title="Programs: 95%"></div>
                <div style="width:5%; background:var(--accent-gold);" title="Admin: 5%"></div>
              </div>
              <div style="display:flex; justify-content:space-between; margin-top:12px; font-size:0.75rem; font-weight:700;">
                <span style="color:var(--primary-green);">Program Services (95%)</span>
                <span style="color:var(--accent-gold);">Operational Support (5%)</span>
              </div>
            </div>
          </div>

          <!-- Simplified Donation Sidebar (QR Only) -->
          <div class="donation-form-card">
            <div style="text-align:center;">
                <h4 style="margin-bottom:16px; color:var(--primary-green);">Scan to Support</h4>
                
                <div style="margin-bottom:24px; text-align:left;">
                    <label style="font-size:0.85rem; font-weight:700; color:var(--text-muted); margin-bottom:8px; display:block;">Target Project</label>
                    <select id="donation-program" style="width:100%; padding:14px; border-radius:10px; border:2px solid var(--border-light); font-family:inherit; font-weight:600; color:var(--primary-green); outline:none;">
                        <option value="Sadaqah Jariyah">Sadaqah Jariyah (Ongoing Impact)</option>
                        <option value="Burial Assistance">Burial Assistance (Islamic Aid)</option>
                        <option value="General Welfare">General Welfare & Relief</option>
                        <option value="Education Fund">Educational Support</option>
                    </select>
                </div>

                <div style="background:white; padding:20px; border-radius:24px; border:1px solid var(--border-light); margin-bottom:24px; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
                    <img src="<?= asset('assets/donation_qr.png') ?>" alt="Donation QR Code" style="width:220px; height:220px; border-radius:12px; display:block; margin:0 auto 16px;">
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.5;">
                        Scan with GCash or Maya to donate <strong>any amount</strong> to your selected project.
                    </p>
                </div>
            </div>

            <div class="trust-badges">
                <svg viewBox="0 0 24 24"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg>
                <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
            </div>
            <p style="text-align:center; font-size:0.75rem; color:var(--text-muted); margin-top:12px;">Your donation is handled securely.</p>
          </div>
        </div>
      </section>

      <!-- ═══ Schedule & Announcements Section ═══ -->
      <section class="schedule-section">
        <div class="section-header">
          <h2>Upcoming Events & Schedule</h2>
          <p>Stay updated with our community programs, charity drives, and spiritual gatherings.</p>
        </div>

        <div class="schedule-grid">
          <!-- Left: Detailed Schedule -->
          <div class="schedule-list">
            <h4 style="margin-bottom:24px; color:var(--primary-green); display:flex; align-items:center; gap:10px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
              Weekly Activity Schedule
            </h4>
            
            <article class="schedule-item">
              <div class="schedule-date">
                <span class="day">15</span>
                <span class="month">May</span>
              </div>
              <div style="flex:1;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--accent-gold); text-transform:uppercase; margin-bottom:4px;">Charity Drive</div>
                <h5 style="font-size:1.1rem; color:var(--primary-green); margin-bottom:8px;">Community Food Distribution</h5>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;">Monthly distribution of essential food packs to registered indigent families.</p>
                <div style="display:flex; gap:16px; font-size:0.75rem; color:var(--text-muted); font-weight:600;">
                  <span>📍 ISCAG Main Gate</span>
                  <span>⏰ 09:00 AM - 12:00 PM</span>
                </div>
              </div>
            </article>

            <article class="schedule-item">
              <div class="schedule-date">
                <span class="day">22</span>
                <span class="month">May</span>
              </div>
              <div style="flex:1;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--primary-green); text-transform:uppercase; margin-bottom:4px;">Medical Mission</div>
                <h5 style="font-size:1.1rem; color:var(--primary-green); margin-bottom:8px;">General Health Check-up for Reverts</h5>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;">Free medical consultations and basic medicine distribution for the revert community.</p>
                <div style="display:flex; gap:16px; font-size:0.75rem; color:var(--text-muted); font-weight:600;">
                  <span>📍 ISCAG Clinic</span>
                  <span>⏰ 08:30 AM - 04:00 PM</span>
                </div>
              </div>
            </article>

            <article class="schedule-item">
              <div class="schedule-date">
                <span class="day">28</span>
                <span class="month">May</span>
              </div>
              <div style="flex:1;">
                <div style="font-size:0.75rem; font-weight:800; color:#4a90e2; text-transform:uppercase; margin-bottom:4px;">Education</div>
                <h5 style="font-size:1.1rem; color:var(--primary-green); margin-bottom:8px;">School Supplies Handover Ceremony</h5>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:12px;">Awarding ceremony for the Educational Support Fund beneficiaries.</p>
                <div style="display:flex; gap:16px; font-size:0.75rem; color:var(--text-muted); font-weight:600;">
                  <span>📍 ISCAG Multi-purpose Hall</span>
                  <span>⏰ 02:00 PM - 05:00 PM</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Right: Announcements -->
          <div>
            <h4 style="margin-bottom:24px; color:var(--primary-green); display:flex; align-items:center; gap:10px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
              Important Announcements
            </h4>
            
            <div class="announcement-card">
              <span class="announcement-tag">Critical Update</span>
              <h5 style="font-size:1.4rem; color:var(--primary-green); margin-bottom:16px;">New Burial Assistance Hotline</h5>
              <p style="font-size:1rem; line-height:1.7; color:var(--text-muted); margin-bottom:24px;">
                The Damayan department has officially launched a 24/7 dedicated hotline for emergency burial coordination. 
                This service ensures immediate assistance for families during their most difficult times.
              </p>
              <div style="background:var(--bg-soft); padding:20px; border-radius:16px; border:1px solid var(--border-light); text-align:center;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px;">Emergency Number</div>
                <div style="font-size:1.8rem; font-weight:900; color:var(--primary-green); letter-spacing:1px;">0956 029 4935</div>
              </div>
            </div>

            <div style="margin-top:24px; padding:24px; background:rgba(23, 107, 69, 0.04); border-radius:20px; border:1px dashed var(--primary-light);">
              <h6 style="color:var(--primary-green); margin-bottom:8px; font-weight:800;">Volunteer with Damayan</h6>
              <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.5;">Our programs rely on the strength of our volunteers. Join us in making a difference. Send a message to get involved.</p>
            </div>
          </div>
        </div>
      </section>




    </div>
  </div>
</div>

<script>
  // ── Hero Carousel Logic ──
  const track = document.getElementById('carousel-track');
  const dots = document.querySelectorAll('.dot');
  let currentSlide = 0;

  function gotoSlide(idx) {
    currentSlide = idx;
    track.style.transform = `translateX(-${currentSlide * 33.333}%)`;
    dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % 3;
    gotoSlide(currentSlide);
  }

  let slideInterval = setInterval(nextSlide, 6000);

  // ── Donation Logic (Simplified) ──
  function confirmDonated() {
    const program = document.getElementById('donation-program').value;
    showToast(`Jazakumullahu Khayran! Your donation to the ${program} has been reported for verification.`, 'var(--primary-green)');
    setTimeout(() => {
        window.location.href = '<?= url('/user/dashboard') ?>';
    }, 2500);
  }

  function showToast(msg, bg) {
    const toast = document.createElement('div');
    toast.textContent = msg;
    toast.style.cssText = `
        position:fixed;top:24px;right:24px;background:${bg};color:white;
        padding:16px 24px;border-radius:12px;z-index:99999;
        font-weight:700;font-size:0.95rem;box-shadow:0 8px 30px rgba(0,0,0,0.2);
        max-width:400px;animation:slideInRight 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
  }

  // Define animations in JS if not in CSS
  if (!document.getElementById('custom-animations')) {
    const s = document.createElement('style');
    s.id = 'custom-animations';
    s.textContent = `
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    `;
    document.head.appendChild(s);
  }
</script>
</body>
</html>
