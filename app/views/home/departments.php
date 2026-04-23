<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Our Departments - ISCAG Philippines</title>
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

    /* ─── DEPT HERO ────────────────────────────────────────── */
    .dept-hero {
      padding: 160px 0 100px;
      background: linear-gradient(135deg, #0f5c3a 0%, #176b45 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .dept-hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image: url('<?= asset('assets/hero-mosque.png') ?>');
      background-size: cover;
      background-position: center;
      opacity: 0.15;
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
      font-size: clamp(2.5rem, 5vw, 4rem);
      font-weight: 700;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
    }
    .hero-subtitle {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto;
      opacity: 0.9;
      position: relative;
      z-index: 2;
      line-height: 1.6;
    }

    /* ─── DEPT GRID ────────────────────────────────────────── */
    .depts-section {
      padding: 100px 0;
      background: white;
    }

    .dept-main-card {
      background: white;
      border-radius: 32px;
      overflow: hidden;
      border: 1px solid var(--border);
      display: flex;
      margin-bottom: 40px;
      transition: all 0.4s ease;
      box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .dept-main-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 50px rgba(20, 83, 45, 0.12);
      border-color: var(--green-100);
    }
    .dept-img {
      width: 40%;
      min-height: 350px;
      position: relative;
    }
    .dept-img img {
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .dept-body {
      width: 60%;
      padding: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .dept-tag {
      color: var(--gold);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 1px;
      margin-bottom: 12px;
    }
    .dept-body h2 {
      font-family: 'Lora', serif;
      font-size: 2.2rem;
      color: var(--green-900);
      margin-bottom: 20px;
    }
    .dept-body p {
      font-size: 1.1rem;
      color: var(--text-muted);
      line-height: 1.7;
      margin-bottom: 30px;
    }
    .btn-dept {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      color: var(--green-800);
      font-weight: 700;
      text-decoration: none;
      transition: gap 0.3s ease;
    }
    .btn-dept:hover { gap: 15px; color: var(--green-600); }

    /* Reverse layout for alternating cards */
    .dept-main-card.reverse { flex-direction: row-reverse; }

    @media (max-width: 991px) {
      .dept-main-card { flex-direction: column !important; }
      .dept-img, .dept-body { width: 100%; }
      .dept-body { padding: 40px; }
      .dept-img { min-height: 250px; }
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'department';
  include 'partials/navbar.php'; 
?>

<!-- HERO SECTION -->
<header class="dept-hero fade-in">
  <div class="container">
    <span class="hero-label reveal">ISCAG Services</span>
    <h1 class="hero-title reveal">Our Specialized Departments</h1>
    <p class="hero-subtitle reveal">Comprehensive support and services tailored to the needs of the Ummah and the broader community.</p>
  </div>
</header>

<!-- DEPARTMENTS LIST -->
<section class="depts-section fade-in delay-200">
  <div class="container">

    <!-- Apartment -->
    <div class="dept-main-card reveal">
      <div class="dept-img">
        <img src="<?= asset('assets/1BR Type/1BR front.jpg') ?>" alt="Apartment">
      </div>
      <div class="dept-body">
        <span class="dept-tag">Residential Services</span>
        <h2>Apartment Management</h2>
        <p>Providing safe, affordable, and Islamic-centered housing for families and students. Our residential complex offers a supportive environment focused on spiritual and community growth.</p>
        <a href="<?= url('/apartment') ?>" class="btn-dept">
          Explore Apartment Services
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>

    <!-- Daawah -->
    <div class="dept-main-card reverse reveal">
      <div class="dept-img">
        <img src="<?= asset('assets/hero-mosque.png') ?>" alt="Daawah">
      </div>
      <div class="dept-body">
        <span class="dept-tag">Guidance & Call</span>
        <h2>Da'wah Department</h2>
        <p>The heart of our mission. We provide authentic Islamic knowledge, counseling for new Muslims, and programs designed to spread the message of peace and understanding across the Philippines.</p>
        <a href="<?= url('/daawah') ?>" class="btn-dept">
          Learn About Da'wah
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>

    <!-- Damayan -->
    <div class="dept-main-card reveal">
      <div class="dept-img">
        <img src="<?= asset('assets/about-center.png') ?>" alt="Damayan">
      </div>
      <div class="dept-body">
        <span class="dept-tag">Social Welfare</span>
        <h2>Damayan Services</h2>
        <p>Dedicated to bereavement support and social welfare. We assist families during their most difficult times, offering burial services and humanitarian aid to those in need.</p>
        <a href="<?= url('/damayan') ?>" class="btn-dept">
          View Damayan Support
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </div>

  </div>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
