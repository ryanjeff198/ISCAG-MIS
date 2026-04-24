<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Announcements - ISCAG Philippines</title>
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
      background: url('<?= asset('assets/announcements_hero.png') ?>') center/cover no-repeat;
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

    /* ─── ANNOUNCEMENTS FEED ────────────────────────────────── */
    .news-section {
      padding: 100px 0;
    }
    .news-item {
      background: white;
      padding: 40px;
      border-radius: 24px;
      border: 1px solid var(--border);
      margin-bottom: 30px;
      transition: all 0.4s ease;
      display: flex;
      gap: 30px;
      align-items: center;
    }
    .news-item:hover {
      transform: translateX(10px);
      border-color: var(--green-200);
      box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .news-date {
      width: 100px;
      flex-shrink: 0;
      text-align: center;
      padding-right: 30px;
      border-right: 1px solid var(--border);
    }
    .news-day {
      display: block;
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--green-800);
    }
    .news-month {
      display: block;
      font-size: 0.85rem;
      text-transform: uppercase;
      font-weight: 700;
      color: var(--text-muted);
    }
    .news-body {
      flex: 1;
    }
    .news-tag {
      display: inline-block;
      padding: 4px 12px;
      background: var(--green-50);
      color: var(--green-700);
      font-size: 0.7rem;
      font-weight: 700;
      border-radius: 6px;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .news-body h3 {
      font-family: 'Lora', serif;
      font-size: 1.6rem;
      color: var(--green-900);
      margin-bottom: 15px;
    }
    .news-body p {
      font-size: 1.05rem;
      color: var(--text-muted);
      line-height: 1.7;
      margin-bottom: 0;
    }
    .news-link {
      margin-left: 30px;
      color: var(--gold);
      transition: transform 0.3s ease;
    }
    .news-item:hover .news-link {
      transform: translateX(5px);
    }

    @media (max-width: 768px) {
      .news-item { flex-direction: column; align-items: flex-start; text-align: left; }
      .news-date { width: auto; border-right: none; border-bottom: 1px solid var(--border); padding: 0 0 15px; margin-bottom: 20px; display: flex; gap: 10px; align-items: baseline; }
      .news-link { margin-left: 0; margin-top: 20px; }
    }

    /* ─── SIDEBAR/NEWSLETTER ────────────────────────────────── */
    .newsletter-card {
      background: var(--green-900);
      color: white;
      padding: 50px;
      border-radius: 32px;
      text-align: center;
      margin-top: 60px;
    }
    .newsletter-card h2 {
      font-family: 'Lora', serif;
      font-size: 2.2rem;
      margin-bottom: 20px;
    }
    .newsletter-card p {
      opacity: 0.8;
      margin-bottom: 30px;
      font-size: 1.1rem;
    }
    .newsletter-form {
      display: flex;
      gap: 15px;
      max-width: 500px;
      margin: 0 auto;
    }
    .newsletter-input {
      flex: 1;
      padding: 16px 24px;
      border-radius: 12px;
      border: none;
      font-size: 1rem;
    }
    .btn-subscribe {
      padding: 16px 32px;
      background: var(--gold);
      color: var(--green-900);
      border: none;
      border-radius: 12px;
      font-weight: 700;
      transition: all 0.3s ease;
    }
    .btn-subscribe:hover {
      background: white;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'community';
  include 'partials/navbar.php'; 
?>

<!-- HERO -->
<header class="service-hero fade-in">
  <div class="container">
    <span class="hero-tag reveal">Stay Informed</span>
    <h1 class="hero-title reveal">Latest Announcements</h1>
    <p class="hero-subtitle reveal">The central hub for news, updates, and important notices from ISCAG. Keeping our community connected and informed.</p>
  </div>
</header>

<!-- ANNOUNCEMENTS FEED -->
<section class="news-section">
  <div class="container">
    
    <div class="reveal">
      <!-- Item 1 -->
      <div class="news-item">
        <div class="news-date">
          <span class="news-day">28</span>
          <span class="news-month">Apr</span>
        </div>
        <div class="news-body">
          <span class="news-tag">Community Aid</span>
          <h3>Ramadan Food Drive Results</h3>
          <p>Thanks to your incredible generosity, we successfully distributed over 1,000 food packs to families in need across Cavite. May Allah reward everyone who contributed.</p>
        </div>
        <div class="news-link">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="news-item">
        <div class="news-date">
          <span class="news-day">24</span>
          <span class="news-month">Apr</span>
        </div>
        <div class="news-body">
          <span class="news-tag">Prayer Times</span>
          <h3>New Fajr Prayer Schedule</h3>
          <p>Effective immediately, the Fajr congregational prayer (Iqamah) will be adjusted to 4:45 AM to better align with the changing season. Please plan accordingly.</p>
        </div>
        <div class="news-link">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="news-item">
        <div class="news-date">
          <span class="news-day">20</span>
          <span class="news-month">Apr</span>
        </div>
        <div class="news-body">
          <span class="news-tag">Education</span>
          <h3>Summer Arabic Intensive Registration</h3>
          <p>Registration is now open for our 8-week summer Arabic program. Classes are available for both beginners and intermediate levels. Limited slots available!</p>
        </div>
        <div class="news-link">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </div>
      </div>
    </div>

    <!-- NEWSLETTER -->
    <div class="newsletter-card reveal">
      <h2>Never Miss an Update</h2>
      <p>Subscribe to our weekly newsletter to receive news and event updates directly in your inbox.</p>
      <form class="newsletter-form">
        <input type="email" class="newsletter-input" placeholder="Your email address" required>
        <button type="submit" class="btn-subscribe">Subscribe</button>
      </form>
    </div>

  </div>
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
