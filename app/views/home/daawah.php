<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Da'wah Programs - ISCAG Philippines</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= asset('css/site-shared.css') ?>">
  <style>
    :root {
      --green-900: #064e3b;
      --green-800: #065f46;
      --green-700: #047857;
      --green-600: #059669;
      --green-50: #ecfdf5;
      --gold: #d4af37;
      --text-main: #1f2937;
      --text-muted: #4b5563;
      --border: #e5e7eb;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #fbfcfb;
      color: var(--text-main);
    }

    /* ─── HERO ────────────────────────────────────────────── */
    .service-hero {
      padding: 160px 0 100px;
      background: linear-gradient(135deg, #0f5c3a 0%, #176b45 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .service-hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('<?= asset('assets/dawahHeroBG.jpg') ?>') center/cover no-repeat;
      opacity: 0.2;
      z-index: 0;
    }
    .service-hero .container { position: relative; z-index: 1; }
    
    .hero-tag {
      display: inline-block;
      padding: 8px 20px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 24px;
    }
    .hero-title {
      font-family: 'Lora', serif;
      font-size: clamp(2.5rem, 5vw, 4rem);
      font-weight: 700;
      margin-bottom: 20px;
    }
    .hero-subtitle {
      font-size: 1.25rem;
      max-width: 800px;
      margin: 0 auto;
      opacity: 0.9;
      line-height: 1.6;
    }

    /* ─── SECTION ─────────────────────────────────────────── */
    .content-section {
      padding: 100px 0;
    }
    .section-header {
      margin-bottom: 60px;
      text-align: center;
    }
    .section-tag {
      color: var(--gold);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 1.5px;
      margin-bottom: 12px;
      display: block;
    }
    .section-title {
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      color: var(--green-900);
    }

    /* ─── INFO CARDS ──────────────────────────────────────── */
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }
    .info-card {
      background: white;
      padding: 40px;
      border-radius: 24px;
      border: 1px solid var(--border);
      transition: all 0.4s ease;
      height: 100%;
    }
    .info-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.05);
      border-color: var(--green-100);
    }
    .info-icon {
      width: 60px; height: 60px;
      background: var(--green-50);
      color: var(--green-800);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 24px;
      font-size: 1.5rem;
    }
    .info-card h3 {
      font-family: 'Lora', serif;
      font-size: 1.5rem;
      color: var(--green-900);
      margin-bottom: 16px;
    }
    .info-card p {
      color: var(--text-muted);
      line-height: 1.7;
      margin: 0;
    }

    /* ─── FEATURE ROW ────────────────────────────────────── */
    .feature-row {
      display: flex;
      align-items: center;
      gap: 60px;
      margin-bottom: 100px;
    }
    .feature-row.reverse { flex-direction: row-reverse; }
    .feature-img {
      width: 50%;
      border-radius: 32px;
      overflow: hidden;
      box-shadow: 0 30px 60px rgba(0,0,0,0.1);
    }
    .feature-img .carousel, .feature-img .carousel-inner, .feature-img .carousel-item {
      height: 100%;
    }
    .feature-img img { 
      width: 100%; 
      height: 100%; 
      display: block; 
      object-fit: cover;
      transition: transform 10s ease;
    }
    .carousel-item.active img {
      transform: scale(1.1);
    }
    .feature-body { width: 50%; }
    
    .feature-body h2 {
      font-family: 'Lora', serif;
      font-size: 2.2rem;
      color: var(--green-900);
      margin-bottom: 24px;
    }
    .feature-body p {
      font-size: 1.1rem;
      color: var(--text-muted);
      line-height: 1.8;
      margin-bottom: 30px;
    }

    /* ─── CTA ─────────────────────────────────────────────── */
    .cta-section {
      padding: 100px 0;
      background: var(--green-900);
      color: white;
      text-align: center;
      border-radius: 40px;
      margin: 0 auto 100px;
    }
    .cta-title {
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      margin-bottom: 20px;
    }
    .cta-desc {
      opacity: 0.8;
      max-width: 600px;
      margin: 0 auto 40px;
      font-size: 1.1rem;
    }
    .btn-cta {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 16px 36px;
      background: var(--gold);
      color: var(--green-900);
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .btn-cta:hover {
      background: white;
      transform: translateY(-4px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 991px) {
      .feature-row { flex-direction: column !important; gap: 40px; }
      .feature-img, .feature-body { width: 100%; }
    }
    .hero-logo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 24px;
      display: block;
      border: 4px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'department';
  include 'partials/navbar.php'; 
?>

<!-- HERO -->
<header class="service-hero fade-in">
  <div class="container">
    <span class="hero-tag reveal">Guidance & Call</span>
    <img src="<?= asset("assets/da'wah logo.jpg") ?>" alt="Daawah Logo" class="hero-logo reveal">
    <h1 class="hero-title reveal">Da'wah Department</h1>
    <p class="hero-subtitle reveal">Spreading the message of Islam through knowledge, compassion, and authentic guidance for all seekers of truth.</p>
  </div>
</header>

<!-- MAIN CONTENT -->
<section class="content-section">
  <div class="container">
    
    <div class="feature-row reveal">
      <div class="feature-img">
        <img src="<?= asset('assets/dawahFeatureImage3.jpg') ?>" alt="Islamic Knowledge">
      </div>
      <div class="feature-body">
        <span class="section-tag">Our Mission</span>
        <h2>Enlightening the Community</h2>
        <p>The Da'wah Department is the heart of ISCAG's mission. We are dedicated to providing accurate information about Islam to Muslims and non-Muslims alike. Our approach is based on wisdom, beautiful preaching, and respectful dialogue.</p>
        <p>Through our programs, we aim to build bridges of understanding and support those who have recently embraced Islam, providing them with the necessary tools for their spiritual journey.</p>
      </div>
    </div>

    <div class="feature-row reverse reveal">
      <div class="feature-img">
        <div id="dawahCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
          <!-- Indicators -->
          <div class="carousel-indicators mb-0">
            <button type="button" data-bs-target="#dawahCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#dawahCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#dawahCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#dawahCarousel" data-bs-slide-to="3"></button>
          </div>
          <div class="carousel-inner shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="carousel-item active">
              <img src="<?= asset('assets/dawahFeatureImage4.jpg') ?>" class="d-block w-100" alt="Dawah 4">
            </div>
            <div class="carousel-item">
              <img src="<?= asset('assets/dawahFeatureImage3.jpg') ?>" class="d-block w-100" alt="Dawah 3">
            </div>
            <div class="carousel-item">
              <img src="<?= asset('assets/dawahFeatureImage2.jpg') ?>" class="d-block w-100" alt="Dawah 2">
            </div>
            <div class="carousel-item">
              <img src="<?= asset('assets/dawahFeatureImage.jpg') ?>" class="d-block w-100" alt="Dawah 1">
            </div>
          </div>
        </div>
      </div>
      <div class="feature-body">
        <span class="section-tag">Community Outreach</span>
        <h2>Engaging with the Ummah</h2>
        <p>Our outreach programs bring the community together through various events, workshops, and gatherings. We strive to create an inclusive environment where everyone can learn and grow together.</p>
        <p>Stay updated with our latest activities and join us in our mission to spread peace and guidance.</p>
      </div>
    </div>


    <div class="section-header reveal">
      <span class="section-tag">Programs</span>
      <h2 class="section-title">Our Specialized Services</h2>
    </div>

    <div class="info-grid reveal">
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <h3>Islamic Studies</h3>
        <p>Regular classes and seminars on Quran, Hadith, Fiqh, and Arabic language for all ages and knowledge levels.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
        </div>
        <h3>New Muslim Support</h3>
        <p>Comprehensive guidance for reverts, including educational modules, mentorship, and social integration support.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h.01"/><path d="M7 10h10"/><path d="M12 10v10"/><path d="M12 10a5 5 0 0 1 5 5v3a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2v-3a5 5 0 0 1 5-5z"/></svg>
        </div>
        <h3>Youth Guidance</h3>
        <p>Specialized programs designed to empower Muslim youth with a strong identity and ethical foundation in modern society.</p>
      </div>
    </div>

  </div>
</section>

<!-- CTA -->
<section class="cta-section container reveal">
  <h2 class="cta-title">Want to Learn More?</h2>
  <p class="cta-desc">Whether you're a lifelong Muslim or just curious about Islam, our doors are always open. Join our programs today.</p>
  <a href="<?= url('/contact') ?>" class="btn-cta">
    Get in Touch
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  function reveal() {
    var reveals = document.querySelectorAll(".reveal");
    for (var i = 0; i < reveals.length; i++) {
      var windowHeight = window.innerHeight;
      var elementTop = reveals[i].getBoundingClientRect().top;
      var elementVisible = 150;
      if (elementTop < windowHeight - elementVisible) {
        reveals[i].classList.add("active");
      }
    }
  }
  window.addEventListener("scroll", reveal);
  reveal();
});
</script>

</body>
</html>
