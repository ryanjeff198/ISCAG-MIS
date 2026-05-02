<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>History & Organization - ISCAG Philippines</title>
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
      color: var(--text-main);
    }

    /* ─── HISTORY HERO ────────────────────────────────────────── */
    .history-hero {
      padding: 160px 0 120px;
      background: linear-gradient(135deg, #14532d 0%, #166534 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .history-hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image: url('<?= asset('assets/hero-mosque.png') ?>');
      background-size: cover;
      background-position: center;
      opacity: 0.12;
      mix-blend-mode: overlay;
    }
    .hero-label {
      display: inline-block;
      padding: 6px 18px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      color: white;
      font-size: 0.75rem;
      font-weight: 700;
      border-radius: 50px;
      margin-bottom: 24px;
      text-transform: uppercase;
      letter-spacing: 2px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .hero-title {
      font-family: 'Lora', serif;
      font-size: clamp(2.5rem, 6vw, 4.5rem);
      font-weight: 700;
      margin-bottom: 24px;
      position: relative;
      z-index: 2;
      line-height: 1.1;
    }
    .hero-subtitle {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto;
      opacity: 0.9;
      position: relative;
      z-index: 2;
      line-height: 1.7;
    }

    /* ─── INTRO SECTION ───────────────────────────────────────── */
    .intro-section {
      padding: 100px 0;
      background: white;
      text-align: center;
    }
    .intro-text {
      max-width: 900px;
      margin: 0 auto;
      font-size: 1.3rem;
      line-height: 1.8;
      color: var(--green-900);
      font-family: 'Lora', serif;
      font-style: italic;
    }

    /* ─── TIMELINE SECTION ─────────────────────────────────────── */
    .history-section {
      padding: 60px 0 100px;
      background: #fcfbf8;
    }
    .timeline {
      position: relative;
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 0;
    }
    .timeline::after {
      content: '';
      position: absolute;
      width: 2px;
      background: linear-gradient(to bottom, transparent, var(--gold), var(--gold), transparent);
      top: 0; bottom: 0;
      left: 50%;
      margin-left: -1px;
    }

    .timeline-item {
      padding: 10px 50px;
      position: relative;
      background-color: inherit;
      width: 50%;
    }
    .timeline-item::after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      right: -10px;
      background-color: white;
      border: 4px solid var(--green-700);
      top: 20px;
      border-radius: 50%;
      z-index: 1;
      box-shadow: 0 0 10px rgba(22, 101, 52, 0.2);
    }
    .left { left: 0; }
    .right { left: 50%; }
    .right::after { left: -10px; }

    .content {
      padding: 40px;
      background-color: white;
      position: relative;
      border-radius: 24px;
      border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(0,0,0,0.03);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .content:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(20, 83, 45, 0.08); border-color: var(--green-100); }
    
    .year-tag {
      display: inline-block;
      padding: 6px 16px;
      background: var(--green-50);
      color: var(--green-800);
      font-weight: 800;
      border-radius: 50px;
      margin-bottom: 20px;
      font-size: 1rem;
      letter-spacing: 1px;
    }
    .content h2 {
      font-family: 'Lora', serif;
      font-size: 1.7rem;
      color: var(--green-900);
      margin-bottom: 16px;
      line-height: 1.3;
    }
    .content p {
      font-size: 1.05rem;
      color: var(--text-muted);
      line-height: 1.8;
      margin: 0;
    }

    /* ─── SLIDE ANIMATIONS FOR TIMELINE ─────────────────────── */
    .timeline-item.left.reveal {
      transform: translateX(-80px);
      opacity: 0;
    }
    .timeline-item.right.reveal {
      transform: translateX(80px);
      opacity: 0;
    }
    .timeline-item.reveal.active {
      transform: translateX(0);
      opacity: 1;
    }

    /* ─── ORGANIZATION SECTION ─────────────────────────────────── */
    .org-section {
      padding: 100px 0;
      background: white;
    }
    .section-title {
      text-align: center;
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      color: var(--green-900);
      margin-bottom: 60px;
    }

    .board-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-bottom: 60px;
    }
    .member-card {
      background: white;
      border-radius: 24px;
      padding: 16px;
      text-align: center;
      border: 1px solid var(--border);
      transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      display: flex;
      flex-direction: column;
      width: 100%;
    }
    .member-card:hover {
      transform: translateY(-15px) rotate(-1deg);
      box-shadow: 0 30px 60px rgba(20, 83, 45, 0.12);
      border-color: var(--green-200);
    }
    .member-avatar {
      width: 100%;
      aspect-ratio: 1.6 / 1;
      background: #f8faf8;
      border-radius: 18px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 4rem;
      color: var(--green-600);
      overflow: hidden;
      border: 1px solid rgba(0,0,0,0.03);
      box-shadow: inset 0 0 20px rgba(0,0,0,0.02);
      flex-shrink: 0;
    }
    .member-info {
      padding: 0 10px 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .member-name {
      font-family: 'Lora', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--green-900);
      margin-bottom: 8px;
      line-height: 1.2;
    }
    .member-role {
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .dept-sub-title {
      font-family: 'Lora', serif;
      font-size: 1.8rem;
      color: var(--green-800);
      margin: 40px 0 30px;
      padding-bottom: 12px;
      border-bottom: 2px solid var(--green-50);
      display: inline-block;
      width: 100%;
    }
    .section-divider {
      font-size: 1rem;
      color: var(--green-600);
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 700;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .section-divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background: var(--green-50);
    }

    /* ─── DEPARTMENTS ────────────────────────────────────────── */
    .dept-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 32px;
      margin-bottom: 60px;
    }
    .dept-card {
      background: white;
      border-radius: 24px;
      padding: 16px;
      text-align: center;
      border: 1px solid var(--border);
      transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
      box-shadow: 0 10px 30px rgba(0,0,0,0.04);
      display: flex;
      flex-direction: column;
      width: 100%;
      text-decoration: none;
      color: inherit;
    }
    .dept-card:hover {
      transform: translateY(-15px) rotate(1deg);
      box-shadow: 0 30px 60px rgba(20, 83, 45, 0.12);
      border-color: var(--green-200);
    }
    .dept-icon {
      width: 100%;
      aspect-ratio: 1.6 / 1;
      background: var(--green-50);
      border-radius: 18px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3.5rem;
      color: var(--green-700);
      overflow: hidden;
      border: 1px solid rgba(0,0,0,0.03);
      flex-shrink: 0;
    }
    .dept-info {
      padding: 0 10px 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .dept-info h3 {
      font-family: 'Lora', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--green-900);
      margin-bottom: 8px;
      line-height: 1.2;
    }
    .dept-info p {
      font-size: 1rem;
      color: var(--text-muted);
      line-height: 1.6;
      margin: 0;
    }

    @media (max-width: 1100px) {
      .board-grid, .dept-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
      .board-grid, .dept-grid { grid-template-columns: 1fr; }
      .dept-sub-title { font-size: 1.5rem; }
    }
    .cta-section {
      padding-bottom: 60px;
      background: white;
    }
    .cta-card {
      background: linear-gradient(135deg, var(--green-900) 0%, var(--green-800) 100%);
      border-radius: 32px;
      padding: 65px 40px;
      text-align: center;
      color: white;
      position: relative;
      overflow: hidden;
      max-width: 950px;
      margin: 0 auto;
      box-shadow: 0 15px 40px rgba(20, 83, 45, 0.15);
    }
    .cta-card::before {
      content: '';
      position: absolute;
      top: -40%;
      left: -10%;
      width: 250px;
      height: 250px;
      background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
      border-radius: 50%;
    }
    .cta-title {
      font-family: 'Lora', serif;
      font-size: 2.8rem;
      font-weight: 700;
      margin-bottom: 20px;
      letter-spacing: -0.01em;
    }
    .cta-text {
      font-size: 1.25rem;
      max-width: 650px;
      margin: 0 auto 40px;
      opacity: 0.9;
      line-height: 1.7;
    }
    .cta-btns {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .btn-cta-primary {
      background: var(--gold);
      color: var(--green-900);
      padding: 16px 36px;
      border-radius: 14px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 1.05rem;
    }
    .btn-cta-primary:hover {
      background: white;
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      color: var(--green-900);
    }
    .btn-cta-outline {
      background: transparent;
      color: white;
      padding: 16px 36px;
      border-radius: 14px;
      font-weight: 700;
      text-decoration: none;
      border: 2px solid rgba(255,255,255,0.3);
      transition: all 0.3s ease;
      font-size: 1.05rem;
    }
    .btn-cta-outline:hover {
      background: rgba(255,255,255,0.1);
      border-color: white;
      transform: translateY(-3px);
    }

    @media screen and (max-width: 991px) {
      .timeline::after { left: 31px; }
      .timeline-item { width: 100%; padding-left: 70px; padding-right: 25px; }
      .timeline-item::after { left: 21px; }
      .right { left: 0%; }
      .content { padding: 30px; }
      .dept-card { flex-direction: column; text-align: center; }
      .dept-icon { margin: 0 auto; }
      .cta-card { padding: 50px 25px; border-radius: 24px; }
      .cta-title { font-size: 2.2rem; }
      .cta-text { font-size: 1.1rem; }
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'about';
  include 'partials/navbar.php'; 
?>

<!-- HERO SECTION -->
<header class="history-hero fade-in">
  <div class="container">
    <span class="hero-label reveal">The ISCAG Legacy</span>
    <h1 class="hero-title reveal">History & Organization</h1>
    <p class="hero-subtitle reveal">Discover our heritage and the core departments steering our mission forward.</p>
  </div>
</header>

<!-- INTRO SECTION -->
<section class="intro-section fade-in delay-100">
  <div class="container">
    <div class="reveal">
      <p class="intro-text">
        "Founded on the pillars of Da’wah and community service, ISCAG's story is one of transformation—not just of a physical location, but of the countless lives touched by the light of knowledge and the warmth of a supportive Ummah."
      </p>
    </div>
  </div>
</section>

<!-- TIMELINE SECTION -->
<section class="history-section fade-in delay-200">
  <div class="container">
    <div class="timeline">
      
      <!-- 1991 -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">1991</span>
          <h2>A Vision Ignited</h2>
          <p>Founded by Filipino Muslim converts working in Saudi Arabia, ISCAG began as a shared desire to bring the beauty of Islamic teachings home. Our first office opened in Quezon City.</p>
        </div>
      </div>

      <!-- 1996 -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">1996</span>
          <h2>A Permanent Home</h2>
          <p>A major milestone was reached as ISCAG moved to its permanent location in Dasmariñas, Cavite. This transition marked the evolution into a comprehensive Islamic institution.</p>
        </div>
      </div>

      <!-- 1999 -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">1999</span>
          <h2>Nurturing the Youth</h2>
          <p>Education became a core pillar with the opening of the ISCAG School. Starting with kindergarten, it laid the foundation for academic excellence and Islamic values.</p>
        </div>
      </div>

      <!-- 2005 -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">2005</span>
          <h2>Expanding Horizons</h2>
          <p>Responding to a growing student body, a larger school building was completed. The curriculum expanded to include elementary and high school levels.</p>
        </div>
      </div>

      <!-- Late 90s / Early 2000s -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">2000s</span>
          <h2>Holistic Community Care</h2>
          <p>ISCAG launched a medical clinic and residential housing projects, providing affordable healthcare and a supportive environment for families.</p>
        </div>
      </div>

      <!-- Present -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">Present</span>
          <h2>A Nationwide Impact</h2>
          <p>Today, ISCAG is a cornerstone of the Philippine Islamic community. From building mosques to humanitarian aid, our mission knows no bounds.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- BOARD SECTION -->
<section class="org-section fade-in delay-300">
  <div class="container">

    <h2 class="section-title reveal">Functional Departments</h2>
    <div class="dept-grid">
      <a href="<?= url('/dawah') ?>" class="dept-card reveal">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div class="dept-info">
          <h3>Da'wah Department</h3>
          <p>Overseeing Islamic propagation, counseling, and guidance for new Muslims.</p>
        </div>
      </a>
      <a href="<?= url('/apartment') ?>" class="dept-card reveal delay-1">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="dept-info">
          <h3>Apartment Management</h3>
          <p>Supervising residential facilities and housing services.</p>
        </div>
      </a>
      <a href="<?= url('/damayan') ?>" class="dept-card reveal delay-2">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <div class="dept-info">
          <h3>Damayan Services</h3>
          <p>Coordinating social welfare, bereavement support, and burial services.</p>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- TRANSITION ENDING SECTION -->
<section class="cta-section reveal">
  <div class="container">
    <div class="cta-card">
      <div class="cta-content">
        <h2 class="cta-title">Continuing the Legacy</h2>
        <p class="cta-text">
          Our history is still being written, and every member of our community is a part of it. Together, we continue to uphold the values of education, service, and spiritual growth.
        </p>
        <div class="cta-btns">
          <a href="<?= url('/register') ?>" class="btn-cta-primary">Be Part of Our Future</a>
          <a href="<?= url('/contact') ?>" class="btn-cta-outline">Get in Touch</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
