<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Community Events - ISCAG Philippines</title>
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
      background: url('<?= asset('assets/events_hero.png') ?>') center/cover no-repeat;
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

    /* ─── EVENT GRID ───────────────────────────────────────── */
    .event-section {
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

    .event-card {
      background: white;
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid var(--border);
      transition: all 0.4s ease;
      height: 100%;
      position: relative;
    }
    .event-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.05);
      border-color: var(--green-100);
    }
    .event-img {
      height: 220px;
      position: relative;
    }
    .event-img img {
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .event-date-badge {
      position: absolute;
      top: 20px;
      left: 20px;
      background: white;
      padding: 10px 15px;
      border-radius: 12px;
      text-align: center;
      box-shadow: var(--shadow-md);
    }
    .date-day {
      display: block;
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--green-800);
      line-height: 1;
    }
    .date-month {
      display: block;
      font-size: 0.75rem;
      text-transform: uppercase;
      font-weight: 700;
      color: var(--text-muted);
      margin-top: 2px;
    }
    .event-body {
      padding: 30px;
    }
    .event-category {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--gold);
      text-transform: uppercase;
      margin-bottom: 10px;
      display: block;
    }
    .event-body h3 {
      font-family: 'Lora', serif;
      font-size: 1.4rem;
      color: var(--green-900);
      margin-bottom: 15px;
    }
    .event-meta {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
      font-size: 0.85rem;
      color: var(--text-muted);
    }
    .meta-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .event-desc {
      font-size: 0.95rem;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 25px;
    }
    .btn-event {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--green-700);
      text-decoration: none;
      font-weight: 700;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }
    .btn-event:hover {
      color: var(--green-900);
      gap: 12px;
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
    <span class="hero-tag reveal">Connecting Hearts</span>
    <h1 class="hero-title reveal">Community Events</h1>
    <p class="hero-subtitle reveal">Join us in our journey of faith, learning, and fellowship. Discover upcoming programs designed for all members of our community.</p>
  </div>
</header>

<!-- EVENTS LIST -->
<section class="event-section">
  <div class="container">
    
    <div class="section-header reveal">
      <span class="section-tag">Upcoming Events</span>
      <h2 class="section-title">Save the Date</h2>
    </div>

    <div class="row g-4 reveal">
      <!-- Event 1 -->
      <div class="col-lg-4">
        <div class="event-card">
          <div class="event-img">
            <img src="<?= asset('assets/events_hero.png') ?>" alt="Islamic Seminar">
            <div class="event-date-badge">
              <span class="date-day">15</span>
              <span class="date-month">May</span>
            </div>
          </div>
          <div class="event-body">
            <span class="event-category">Educational</span>
            <h3>Understanding the Quran</h3>
            <div class="event-meta">
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                2:00 PM
              </div>
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Main Hall
              </div>
            </div>
            <p class="event-desc">A deep dive into the historical and spiritual context of selected Surahs, led by our resident scholars.</p>
            <a href="#" class="btn-event">
              Event Details
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Event 2 -->
      <div class="col-lg-4">
        <div class="event-card">
          <div class="event-img">
            <img src="<?= asset('assets/hero-mosque.png') ?>" alt="Youth Program">
            <div class="event-date-badge">
              <span class="date-day">22</span>
              <span class="date-month">May</span>
            </div>
          </div>
          <div class="event-body">
            <span class="event-category">Youth</span>
            <h3>Youth Leadership Summit</h3>
            <div class="event-meta">
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                9:00 AM
              </div>
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Auditorium
              </div>
            </div>
            <p class="event-desc">Empowering the next generation of Muslim leaders through workshops on ethics, community service, and professional skills.</p>
            <a href="#" class="btn-event">
              Event Details
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Event 3 -->
      <div class="col-lg-4">
        <div class="event-card">
          <div class="event-img">
            <img src="<?= asset('assets/about-center.png') ?>" alt="Community Dinner">
            <div class="event-date-badge">
              <span class="date-day">05</span>
              <span class="date-month">Jun</span>
            </div>
          </div>
          <div class="event-body">
            <span class="event-category">Social</span>
            <h3>Monthly Community Iftar</h3>
            <div class="event-meta">
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                6:30 PM
              </div>
              <div class="meta-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Courtyard
              </div>
            </div>
            <p class="event-desc">An evening of shared meals and conversation. All families are welcome to join this celebratory gathering.</p>
            <a href="#" class="btn-event">
              Event Details
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- CTA -->
<section class="cta-section container reveal">
  <h2 class="cta-title">Organizing an Event?</h2>
  <p class="cta-desc">If you have a proposal for a community program or would like to support our upcoming events, please reach out to us.</p>
  <a href="<?= url('/contact') ?>" class="btn-cta">
    Submit Event Proposal
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
