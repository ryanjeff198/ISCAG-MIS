<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines</title>
  <link rel="stylesheet" href="<?= asset('css/homepage.css') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
</head>
<body>

<!-- ═══════ NAVBAR ═══════ -->
<nav class="navbar">
  <div class="container">

    <a href="#home" class="brand">
      <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" style="width:30px; height:30px;"/>
      <span class="brand-name">ISCAG Philippines</span>
    </a>
    <ul class="nav-center">
      <li><a href="#home">Home</a></li>
      <li><a href="#about">About Us</a></li>
      <li><a href="#contact">Contact Us</a></li>
    </ul>
    <div class="nav-right">
      <a href="<?= url('/login') ?>" class="btn-nav-login">Login</a>
    </div>

    <button class="nav-toggle" onclick="document.getElementById('mn').classList.toggle('open')">
      <i class="bi bi-list"></i>
    </button>

  </div>
</nav>

<div class="mob-nav" id="mn">
  <a href="#home"    onclick="document.getElementById('mn').classList.remove('open')">Home</a>
  <a href="#about"   onclick="document.getElementById('mn').classList.remove('open')">About Us</a>
  <a href="#contact" onclick="document.getElementById('mn').classList.remove('open')">Contact Us</a>
  <a href="<?= url('/login') ?>" class="btn-nav-login">Login</a>
</div>


<!-- ═══════ HERO ═══════ -->
<section id="home" class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-5">
        <h1 class="hero-title">
          Islamic Studies, Call and Guidance of the Philippines
        </h1>
        <p class="hero-body">
          Providing education, outreach programs, and community guidance through learning and service.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="#about"   class="btn-learn">Learn More</a>
          <a href="#contact" class="btn-register">Register</a>
        </div>
      </div>


      <div class="col-lg-7">
        
      </div>

    </div>
  </div>
</section>

<div class="sec-divider"></div>

<!-- ═══════ ABOUT ═══════ -->
<section id="about" class="section-white">
  <div class="container">
    <h2 class="sec-title">About Us</h2>
    <p class="sec-body">
      The Islamic Studies, Call and Guidance of the Philippines (ISCAG) is dedicated to nurturing Islamic faith, knowledge, and community among Filipino Muslims through authentic education, compassionate guidance, and impactful outreach programs.
    </p>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-book-half"></i></div>
          <div class="card-ttl">Islamic Education</div>
          <div class="card-desc">Structured Islamic and Arabic studies for all ages, grounded in authentic scholarship and traditional learning.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-people-fill"></i></div>
          <div class="card-ttl">Community Outreach</div>
          <div class="card-desc">Health, livelihood, and social welfare programs reaching underserved Muslim communities nationwide.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-heart-fill"></i></div>
          <div class="card-ttl">Guidance Programs</div>
          <div class="card-desc">Spiritual counseling and family support programs rooted in compassionate Islamic values and service.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="sec-divider"></div>

<!-- ═══════ STATS ═══════ -->
<div class="stats-bar">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3"><div class="s-num">10K+</div><div class="s-lbl">Members Served</div></div>
      <div class="col-6 col-md-3"><div class="s-num">20+</div><div class="s-lbl">Years of Service</div></div>
      <div class="col-6 col-md-3"><div class="s-num">50+</div><div class="s-lbl">Programs Nationwide</div></div>
      <div class="col-6 col-md-3"><div class="s-num">100+</div><div class="s-lbl">Dedicated Staff</div></div>
    </div>
  </div>
</div>

<!-- ═══════ PROGRAMS ═══════ -->
<section id="programs" class="section-gray">
  <div class="container">
    <h2 class="sec-title">Our Programs</h2>
    <p class="sec-body">Comprehensive programs designed to uplift the Filipino Muslim community spiritually, academically, and socially.</p>
    <div class="row g-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-hospital"></i></div>
          <div class="card-ttl">Health Care Services</div>
          <div class="card-desc">Free medical missions and health seminars for Muslim communities across regions.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-mortarboard-fill"></i></div>
          <div class="card-ttl">Islamic &amp; Arabic Studies</div>
          <div class="card-desc">Quranic, jurisprudence, and Arabic language courses for children and adults.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-megaphone-fill"></i></div>
          <div class="card-ttl">Dawah Programs</div>
          <div class="card-desc">Call-to-Islam initiatives and Islamic awareness campaigns throughout the Philippines.</div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card-item">
          <div class="card-ico"><i class="bi bi-building-fill"></i></div>
          <div class="card-ttl">Educational Institution</div>
          <div class="card-desc">Madrasah and integrated schools delivering Islamic and secular curricula side by side.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="sec-divider"></div>

<!-- ═══════ CONTACT ═══════ -->
<section id="contact" class="section-white">
  <div class="container">
    <h2 class="sec-title">Contact Us</h2>
    <p class="sec-body">Have a question or want to get involved? We'd love to hear from you.</p>

    <div class="row g-5 justify-content-center">
      <!-- Info -->
      <div class="col-lg-4">
        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="c-ico"><i class="bi bi-envelope-fill"></i></div>
          <div><div class="c-lbl">Email</div><div class="c-val">info@iscag.org.ph</div></div>
        </div>
        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="c-ico"><i class="bi bi-telephone-fill"></i></div>
          <div><div class="c-lbl">Phone</div><div class="c-val">+63 (2) 8XXX XXXX</div></div>
        </div>
        <div class="d-flex align-items-start gap-3 mb-4">
          <div class="c-ico"><i class="bi bi-geo-alt-fill"></i></div>
          <div><div class="c-lbl">Address</div><div class="c-val">Salitran Dasmarinas, Philippines</div></div>
        </div>
        <div>
          <div class="c-lbl mb-2">Follow Us</div>
          <div class="d-flex gap-2">
            <a href="#" class="soc-btn" style="width:38px;height:38px;border-radius:8px;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:17px;"
               onmouseover="this.style.background='#1c6b3a';this.style.color='#fff'"
               onmouseout="this.style.background='var(--green-bg)';this.style.color='var(--green)'">
              <i class="bi bi-facebook"></i></a>
            <a href="#" class="soc-btn" style="width:38px;height:38px;border-radius:8px;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:17px;"
               onmouseover="this.style.background='#1c6b3a';this.style.color='#fff'"
               onmouseout="this.style.background='var(--green-bg)';this.style.color='var(--green)'">
              <i class="bi bi-envelope-fill"></i></a>
            <a href="#" class="soc-btn" style="width:38px;height:38px;border-radius:8px;background:var(--green-bg);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:17px;"
               onmouseover="this.style.background='#1c6b3a';this.style.color='#fff'"
               onmouseout="this.style.background='var(--green-bg)';this.style.color='var(--green)'">
              <i class="bi bi-youtube"></i></a>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" placeholder="Your name"/>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="your@email.com"/>
          </div>
          <div class="col-12">
            <label class="form-label">Message</label>
            <textarea class="form-control" placeholder="Write your message here…"></textarea>
          </div>
          <div class="col-12">
            <button class="btn-send">Send Message</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ═══════ FOOTER ═══════ -->
<footer>
  <div class="container">
    <div class="row g-4">

      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <div class="f-logo">
            <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" style="width:30px; height:30px; border-radius: 20px;"/>
          </div>
          <div class="f-name">ISCAG Philippines</div>
        </div>
        <p class="f-sub">Islamic Studies, Call and Guidance of the Philippines. Serving the Filipino Muslim community through education and service.</p>
        <div class="f-soc">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-envelope-fill"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <div class="col-sm-4 col-lg-2 offset-lg-2">
        <div class="f-col-hd">Quick Links</div>
        <ul class="f-ul">
          <li><a href="#home">Home</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#programs">Programs</a></li>
          <li><a href="#contact">Contact Us</a></li>
        </ul>
      </div>

      <div class="col-sm-4 col-lg-2">
        <div class="f-col-hd">Programs</div>
        <ul class="f-ul">
          <li><a href="#">Health Care</a></li>
          <li><a href="#">Islamic Studies</a></li>
          <li><a href="#">Dawah</a></li>
          <li><a href="#">Education</a></li>
        </ul>
      </div>

      <div class="col-sm-4 col-lg-2">
        <div class="f-col-hd">Contact</div>
        <ul class="f-ul">
          <li><a href="#">info@iscag.org.ph</a></li>
          <li><a href="#">+63 (2) 8XXX XXXX</a></li>
          <li><a href="#">Salitran Dasmarinas, PH</a></li>
        </ul>
      </div>

    </div>
    <hr class="f-hr"/>
    <div class="f-copy">&copy; 2026 ISCAG Philippines. All rights reserved.</div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>