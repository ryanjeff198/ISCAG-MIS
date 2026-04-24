<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Organization - ISCAG Philippines</title>
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

    /* ─── ORG HERO ────────────────────────────────────────── */
    .org-hero {
      padding: 160px 0 100px;
      background: linear-gradient(135deg, #14532d 0%, #166534 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .org-hero::before {
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

    /* ─── ORG CHART / BOARD ─────────────────────────────────── */
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
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin-bottom: 80px;
    }
    .member-card {
      background: #fcfbf8;
      border-radius: 24px;
      padding: 40px 30px;
      text-align: center;
      border: 1px solid var(--border);
      transition: all 0.3s ease;
    }
    .member-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(20, 83, 45, 0.08);
      border-color: var(--green-100);
    }
    .member-avatar {
      width: 120px; height: 120px;
      background: var(--green-50);
      border-radius: 50%;
      margin: 0 auto 24px;
      display: flex; align-items: center; justify-content: center;
      font-size: 3rem; color: var(--green-700);
      border: 4px solid white;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .member-name {
      font-family: 'Lora', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--green-900);
      margin-bottom: 8px;
    }
    .member-role {
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* ─── DEPARTMENTS ────────────────────────────────────────── */
    .dept-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 24px;
    }
    .dept-card {
      display: flex;
      gap: 20px;
      padding: 30px;
      background: white;
      border-radius: 20px;
      border: 1px solid var(--border);
      transition: all 0.3s ease;
    }
    .dept-card:hover {
      background: var(--green-50);
      border-color: var(--green-200);
    }
    .dept-icon {
      width: 60px; height: 60px;
      background: white;
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.8rem;
      color: var(--green-700);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      flex-shrink: 0;
    }
    .dept-info h3 {
      font-family: 'Lora', serif;
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--green-900);
    }
    .dept-info p {
      font-size: 0.95rem;
      color: var(--text-muted);
      line-height: 1.6;
      margin: 0;
    }

    @media (max-width: 768px) {
      .dept-card { flex-direction: column; text-align: center; }
      .dept-icon { margin: 0 auto; }
    }
  </style>
</head>
<body>

<?php 
  $active_page = 'about';
  include 'partials/navbar.php'; 
?>

<!-- HERO SECTION -->
<header class="org-hero">
  <div class="container">
    <span class="hero-label reveal">Our Leadership</span>
    <h1 class="hero-title reveal">Organizational Structure</h1>
    <p class="hero-subtitle reveal">A dedicated team of scholars and professionals committed to the founding vision of ISCAG.</p>
  </div>
</header>

<!-- BOARD SECTION -->
<section class="org-section">
  <div class="container">
    <h2 class="section-title reveal">Executive Board</h2>
    <div class="board-grid">
      
      <div class="member-card reveal">
        <div class="member-avatar">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 class="member-name">Engr. Muhammad Dela Cruz</h3>
        <p class="member-role">President & CEO</p>
      </div>

      <div class="member-card reveal">
        <div class="member-avatar">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 class="member-name">Ustadh Ahmad Abdullah</h3>
        <p class="member-role">Executive Director</p>
      </div>

      <div class="member-card reveal">
        <div class="member-avatar">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 class="member-name">Dr. Fatima Al-Zahra</h3>
        <p class="member-role">Director of Education</p>
      </div>

    </div>

    <h2 class="section-title reveal" style="margin-top: 40px;">Core Departments</h2>
    <div class="dept-grid">
      
      <div class="dept-card reveal">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div class="dept-info">
          <h3>Da'wah Department</h3>
          <p>Overseeing Islamic propagation, counseling, and guidance for new Muslims and the community.</p>
        </div>
      </div>

      <div class="dept-card reveal">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div class="dept-info">
          <h3>Education Dept</h3>
          <p>Managing the ISCAG School and integrated curriculum for elementary and secondary levels.</p>
        </div>
      </div>

      <div class="dept-card reveal">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div class="dept-info">
          <h3>Apartment Management</h3>
          <p>Supervising residential facilities and housing services within the ISCAG complex.</p>
        </div>
      </div>

      <div class="dept-card reveal">
        <div class="dept-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <div class="dept-info">
          <h3>Damayan Services</h3>
          <p>Coordinating social welfare, bereavement support, and humanitarian aid efforts.</p>
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
