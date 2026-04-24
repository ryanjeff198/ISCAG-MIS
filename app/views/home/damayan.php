<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Damayan Social Welfare - ISCAG Philippines</title>
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
      background: linear-gradient(135deg, var(--green-900) 0%, var(--green-800) 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .service-hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('<?= asset('assets/about-center.png') ?>') center/cover no-repeat;
      opacity: 0.15;
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
      color: var(--green-700);
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
    .feature-img img { width: 100%; height: auto; display: block; }
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

    /* ─── DONATION SECTION ────────────────────────────────── */
    .donation-section {
      padding: 100px 0;
      background: linear-gradient(rgba(6, 78, 59, 0.02), rgba(6, 78, 59, 0.05));
      border-top: 1px solid rgba(6, 78, 59, 0.05);
      border-bottom: 1px solid rgba(6, 78, 59, 0.05);
    }
    .donation-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
    }
    .donation-content h2 {
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      color: var(--green-900);
      margin-bottom: 24px;
    }
    .donation-content p {
      font-size: 1.1rem;
      color: var(--text-muted);
      line-height: 1.8;
      margin-bottom: 30px;
    }
    .donation-stats {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 40px;
    }
    .stat-item {
      background: white;
      padding: 24px;
      border-radius: 20px;
      border: 1px solid var(--border);
      text-align: center;
    }
    .stat-value {
      display: block;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--green-700);
      margin-bottom: 4px;
    }
    .stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .donation-card {
      background: white;
      padding: 40px;
      border-radius: 32px;
      box-shadow: 0 30px 60px rgba(6, 78, 59, 0.1);
      border: 1px solid rgba(6, 78, 59, 0.1);
    }
    .donation-card h3 {
      font-family: 'Lora', serif;
      font-size: 1.8rem;
      color: var(--green-900);
      margin-bottom: 20px;
      text-align: center;
    }
    .donation-options {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-bottom: 30px;
    }
    .don-opt {
      padding: 15px;
      border: 2px solid var(--border);
      border-radius: 12px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 700;
      color: var(--text-main);
    }
    .don-opt:hover, .don-opt.active {
      border-color: var(--green-600);
      background: var(--green-50);
      color: var(--green-700);
    }
    .custom-amount {
      width: 100%;
      padding: 16px;
      border: 2px solid var(--border);
      border-radius: 12px;
      margin-bottom: 30px;
      font-size: 1.1rem;
      text-align: center;
    }
    .btn-donate {
      width: 100%;
      padding: 18px;
      background: var(--green-700);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }
    .btn-donate:hover {
      background: var(--green-800);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(6, 78, 59, 0.2);
    }
    
    .qr-toggle-btn {
      width: 100%;
      padding: 12px;
      background: transparent;
      color: var(--green-700);
      border: 2px solid var(--green-700);
      border-radius: 12px;
      font-weight: 700;
      margin-top: 15px;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .qr-toggle-btn:hover {
      background: var(--green-50);
    }
    
    .qr-container {
      margin-top: 20px;
      padding: 20px;
      background: var(--green-50);
      border-radius: 20px;
      display: none;
      text-align: center;
      border: 1px dashed var(--green-700);
    }
    .qr-container img {
      width: 180px;
      height: 180px;
      margin-bottom: 15px;
      border-radius: 12px;
      border: 5px solid white;
      box-shadow: var(--shadow-md);
    }
    .qr-container p {
      font-size: 0.85rem;
      color: var(--green-900);
      font-weight: 600;
      margin-bottom: 0;
    }

    /* ─── CTA ─────────────────────────────────────────────── */
    .cta-section {
      padding: 100px 0;
      background: var(--green-50);
      text-align: center;
      border-radius: 40px;
      margin: 0 20px 100px;
      border: 1px solid var(--border);
    }
    .cta-title {
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      color: var(--green-900);
      margin-bottom: 20px;
    }
    .cta-desc {
      color: var(--text-muted);
      max-width: 600px;
      margin: 0 auto 40px;
      font-size: 1.1rem;
    }
    .btn-cta {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 16px 36px;
      background: var(--green-700);
      color: white;
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .btn-cta:hover {
      background: var(--green-800);
      transform: translateY(-4px);
      box-shadow: 0 15px 30px rgba(5, 150, 105, 0.2);
    }

    @media (max-width: 991px) {
      .feature-row { flex-direction: column !important; gap: 40px; }
      .feature-img, .feature-body { width: 100%; }
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
    <span class="hero-tag reveal">Social Welfare</span>
    <h1 class="hero-title reveal">Damayan Services</h1>
    <p class="hero-subtitle reveal">Providing support, care, and practical assistance to our community members during their most challenging times.</p>
  </div>
</header>

<!-- MAIN CONTENT -->
<section class="content-section">
  <div class="container">
    
    <div class="feature-row reveal">
      <div class="feature-img">
        <img src="<?= asset('assets/about-center.png') ?>" alt="Social Support">
      </div>
      <div class="feature-body">
        <span class="section-tag">Compassion in Action</span>
        <h2>Helping Hands, Healing Hearts</h2>
        <p>The word "Damayan" signifies mutual aid and compassion. At ISCAG, our Damayan Department is committed to standing by our community members during bereavement and other social hardships. We believe that no one should face difficult times alone.</p>
        <p>Our dedicated staff works tirelessly to provide both emotional support and practical solutions, ensuring that every member of our Ummah is cared for with dignity and respect.</p>
      </div>
    </div>

    <div class="section-header reveal">
      <span class="section-tag">Our Services</span>
      <h2 class="section-title">Comprehensive Welfare Support</h2>
    </div>

    <div class="info-grid reveal">
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <h3>Bereavement Support</h3>
        <p>Emotional counseling and spiritual guidance for families who have lost loved ones, helping them navigate their grief through faith.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </div>
        <h3>Burial Assistance</h3>
        <p>Practical coordination for burial services, including documentation, transportation, and Janazah prayer arrangements.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <h3>Emergency Aid</h3>
        <p>Providing temporary financial and material assistance to families facing sudden hardships or humanitarian crises.</p>
      </div>
    </div>
  </div>
</section>

<!-- DONATION SECTION -->
<section class="donation-section">
  <div class="container">
    <div class="donation-grid">
      <div class="donation-content reveal">
        <span class="section-tag">Support Our Cause</span>
        <h2>Your Donation Saves Lives and Provides Dignity</h2>
        <p>At ISCAG, our Damayan services are fueled by the generosity of our community. Every peso you contribute goes directly toward assisting families in their times of greatest need—from emergency medical aid to providing a dignified burial for those who cannot afford it.</p>
        
        <div class="donation-stats">
          <div class="stat-item">
            <span class="stat-value">100%</span>
            <span class="stat-label">Direct Impact</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">500+</span>
            <span class="stat-label">Families Helped</span>
          </div>
        </div>

        <p class="small text-muted"><em>"The best of people are those that bring most benefit to the rest of mankind."</em> — Prophet Muhammad (PBUH)</p>
      </div>

      <div class="donation-card reveal delay-1">
        <h3>Donate via QR Code</h3>
        <p class="text-center text-muted mb-4">Scan the QR code below to make a direct donation to our social welfare programs.</p>
        
        <div class="qr-container" style="display: block; border-style: solid; background: white;">
          <img src="<?= asset('assets/donation_qr.png') ?>" alt="Donation QR Code" style="width: 220px; height: 220px;">
          <p class="mt-2" style="font-size: 1rem; color: var(--green-700);">Scan with GCash, Maya, or Bank App</p>
        </div>

        <p class="text-center mt-4 small text-muted">Your generosity directly funds emergency aid and burial assistance for those in need.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section container reveal">
  <h2 class="cta-title">Need Assistance?</h2>
  <p class="cta-desc">If you or someone you know is in need of our Damayan services, please don't hesitate to reach out. We are here to help.</p>
  <a href="<?= url('/contact') ?>" class="btn-cta">
    Contact Damayan Team
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

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
