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
  <link rel="stylesheet" href="<?= asset('css/site-shared.css') ?>">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

<?php 
  $active_page = 'home';
  include 'partials/navbar.php'; 
?>

<!-- ══════════════════════ HERO SECTION ══════════════════════ -->
<section class="hero-section min-vh-100 d-flex align-items-center">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- LEFT COLUMN: CONTENT -->
      <div class="col-lg-6 hero-content">
        <span class="hero-label">Welcome to ISCAG</span>
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
        <span class="author">Our Motto</span>
      </div>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>

<!-- ══════════ SCRIPT ══════════ -->
<?php include 'partials/scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
