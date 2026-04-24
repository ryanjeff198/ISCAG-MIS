<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Our History - ISCAG Philippines</title>
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
      padding: 60px 0 120px;
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

    /* ─── DECORATIVE ELEMENTS ─────────────────────────────────── */
    .timeline-icon {
      font-size: 2rem;
      margin-bottom: 15px;
      display: block;
    }

    @media screen and (max-width: 991px) {
      .timeline::after { left: 31px; }
      .timeline-item { width: 100%; padding-left: 70px; padding-right: 25px; }
      .timeline-item::after { left: 21px; }
      .right { left: 0%; }
      .content { padding: 30px; }
    }

    /* Animation on scroll */
    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: all 1s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'about';
  include 'partials/navbar.php'; 
?>

<!-- HERO SECTION -->
<header class="history-hero">
  <div class="container">
    <span class="hero-label reveal">The ISCAG Legacy</span>
    <h1 class="hero-title reveal">A Journey of Faith & Service</h1>
    <p class="hero-subtitle reveal">From a humble vision shared by Filipino workers in Saudi Arabia to a beacon of Islamic guidance in the heart of Cavite.</p>
  </div>
</header>

<!-- INTRO SECTION -->
<section class="intro-section">
  <div class="container">
    <div class="reveal">
      <p class="intro-text">
        "Founded on the pillars of Da’wah and community service, ISCAG's story is one of transformation—not just of a physical location, but of the countless lives touched by the light of knowledge and the warmth of a supportive Ummah."
      </p>
    </div>
  </div>
</section>

<!-- TIMELINE SECTION -->
<section class="history-section">
  <div class="container">
    <div class="timeline">
      
      <!-- 1991 -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">1991</span>
          <h2>A Vision Ignited</h2>
          <p>Founded by Filipino Muslim converts working in Saudi Arabia, ISCAG began as a shared desire to bring the beauty of Islamic teachings home. Our first office opened in Quezon City as a dedicated information center for all.</p>
        </div>
      </div>

      <!-- 1996 -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">1996</span>
          <h2>A Permanent Home</h2>
          <p>A major milestone was reached as ISCAG moved to its permanent location in Dasmariñas, Cavite. This transition marked the evolution from a small information center into a comprehensive Islamic institution.</p>
        </div>
      </div>

      <!-- 1999 -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">1999</span>
          <h2>Nurturing the Youth</h2>
          <p>Education became a core pillar with the opening of the ISCAG School. Starting with kindergarten, it laid the foundation for a generation rooted in both academic excellence and Islamic values.</p>
        </div>
      </div>

      <!-- 2005 -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">2005</span>
          <h2>Expanding Horizons</h2>
          <p>Responding to a growing student body, a larger school building was completed. The curriculum expanded to include elementary and high school, cementing our role as a premier educational provider.</p>
        </div>
      </div>

      <!-- Late 90s / Early 2000s -->
      <div class="timeline-item left reveal">
        <div class="content">
          <span class="year-tag">2000s</span>
          <h2>Holistic Community Care</h2>
          <p>ISCAG broadened its reach by launching a medical clinic and laboratory, alongside residential housing projects. These initiatives provided affordable healthcare and a supportive environment for Muslim families.</p>
        </div>
      </div>

      <!-- Present -->
      <div class="timeline-item right reveal">
        <div class="content">
          <span class="year-tag">Present</span>
          <h2>A Nationwide Impact</h2>
          <p>Today, ISCAG is a recognized cornerstone of the Philippine Islamic community. From disaster relief to building mosques across Luzon, Visayas, and Mindanao, our mission of service knows no bounds.</p>
        </div>
      </div>

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

<style>
  .cta-section {
    padding-bottom: 60px;
    background: #fcfbf8;
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

  @media (max-width: 768px) {
    .cta-card { padding: 50px 25px; border-radius: 24px; }
    .cta-title { font-size: 2.2rem; }
    .cta-text { font-size: 1.1rem; }
  }
</style>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
