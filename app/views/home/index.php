<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= asset('css/homepage.css') ?>">
</head>
<body>

<!-- ═══════ NAVBAR ═══════ -->
<nav class="navbar">
  <div class="container">
    <a href="#home" class="navbar-brand">
      <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" onerror="this.src='https://via.placeholder.com/40'"/>
      <span>ISCAG Philippines</span>
    </a>
    <ul class="nav-links">
      <li><a href="#home">Home</a></li>
      <li><a href="#about">About Us</a></li>
      <li><a href="#community">Community</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#donate">Donate</a></li>
      <li><a href="#contact">Contact Us</a></li>
    </ul>
    <div class="nav-right">
      <a href="<?= url('/login') ?>" class="btn-login">Login</a>
    </div>
    <button class="mobile-toggle" onclick="document.getElementById('mobileMenu').classList.toggle('active')">
      <i class="bi bi-list"></i>
    </button>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="#home" onclick="this.parentNode.classList.remove('active')">Home</a>
  <a href="#about" onclick="this.parentNode.classList.remove('active')">About Us</a>
  <a href="#community" onclick="this.parentNode.classList.remove('active')">Community</a>
  <a href="#services" onclick="this.parentNode.classList.remove('active')">Services</a>
  <a href="#donate" onclick="this.parentNode.classList.remove('active')">Donate</a>
  <a href="#contact" onclick="this.parentNode.classList.remove('active')">Contact Us</a>
  <a href="<?= url('/login') ?>" class="btn-login" style="display:inline-block; text-align:center;">Login</a>
</div>

<!-- ═══════ HERO ═══════ -->
<section id="home" class="hero">
  <div class="crescent-moon"></div>
  <div class="container hero-content">
    <h1 class="hero-title">Empowering Faith, Education, and Community</h1>
    <p class="hero-text">
      Islamic Studies, Call and Guidance of the Philippines (ISCAG) is a non-profit Islamic organization dedicated to spreading authentic Islamic knowledge, guiding communities, and strengthening Muslim identity in the Philippines.
    </p>
    <div class="hero-buttons">
      <a href="#donate" class="btn-primary-green">Donate Now</a>
      <a href="#community" class="btn-outline-light">Join Community</a>
    </div>
  </div>
</section>

<!-- ═══════ ABOUT ═══════ -->
<section id="about" class="about-section sec-padding">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <h2 class="sec-title sec-title-left">About ISCAG</h2>
        <div class="about-content">
          <p class="mb-4">
            ISCAG is a non-profit Islamic organization in Dasmariñas, Cavite established by Filipino Muslims (Balik-Islam community) to promote Islamic understanding, education, and spiritual development.
          </p>
          <p class="mb-4">
            It serves as a center for:
          </p>
          <ul class="mb-4" style="line-height: 1.8; list-style: none; padding-left: 0;">
            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Islamic learning and dawah activities</li>
            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Community guidance and counseling</li>
            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Support for new Muslims (reverts)</li>
            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Social and humanitarian services</li>
          </ul>
          <p>
            ISCAG also operates as a community hub with educational facilities, prayer areas, and outreach programs that strengthen Islamic identity and unity.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row g-4">
          <div class="col-12">
            <div class="mission-vision-card">
              <h4><i class="bi bi-bullseye"></i> Mission</h4>
              <p class="m-0 text-muted">To spread authentic Islamic teachings and guide individuals toward spiritual growth through education, dawah, and community service.</p>
            </div>
          </div>
          <div class="col-12">
            <div class="mission-vision-card">
              <h4><i class="bi bi-eye-fill"></i> Vision</h4>
              <p class="m-0 text-muted">A united, educated, and faith-driven Muslim community in the Philippines.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ COMMUNITY ═══════ -->
<section id="community" class="community-section sec-padding">
  <div class="container">
    <h2 class="sec-title">Our Community</h2>
    <p class="sec-subtitle">Strengthening the Ummah through active engagement, continuous learning, and supportive networks.</p>
    
    <div class="row g-4 justify-content-center">
      <div class="col-md-6 col-lg-3">
        <div class="comm-card">
          <div class="comm-icon"><i class="bi bi-book-fill"></i></div>
          <h4>Education</h4>
          <p>Islamic lectures, seminars, and structured learning for Muslims and non-Muslims.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="comm-card">
          <div class="comm-icon"><i class="bi bi-megaphone-fill"></i></div>
          <h4>Outreach (Dawah)</h4>
          <p>House-to-house dawah, public lectures, school seminars, and Islamic awareness programs.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="comm-card">
          <div class="comm-icon"><i class="bi bi-calendar-event-fill"></i></div>
          <h4>Events</h4>
          <p>Community gatherings, Islamic talks, Ramadan programs, and Eid activities.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="comm-card">
          <div class="comm-icon"><i class="bi bi-heart-fill"></i></div>
          <h4>Guidance</h4>
          <p>Support for new Muslims, counseling, and spiritual mentoring.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ SERVICES ═══════ -->
<section id="services" class="services-section sec-padding">
  <div class="container">
    <h2 class="sec-title">Our Core Services</h2>
    <p class="sec-subtitle">Dedicated departments and services providing holistic support and development for the community.</p>

    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="srv-icon"><i class="bi bi-building-fill"></i></div>
          <h4>Apartment Services</h4>
          <p>ISCAG provides residential apartment facilities within its compound for staff, students, and community members. These accommodations support long-term Islamic learning, community living, and accessibility for those involved in ISCAG programs.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="srv-icon"><i class="bi bi-flower1"></i></div>
          <h4>Damayan (Burial Services)</h4>
          <p class="mb-2">Islamic burial assistance (Janazah services) ensuring proper rites with dignity according to Shariah.</p>
          <ul class="text-muted small ps-3 mb-0">
            <li>Janazah preparation assistance</li>
            <li>Coordination with burial grounds</li>
            <li>Islamic washing (ghusl) guidance</li>
            <li>Burial support & arrangements</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="srv-icon"><i class="bi bi-people-fill"></i></div>
          <h4>Dawah Department</h4>
          <p class="mb-2">Dedicated male and female teams spreading Islamic knowledge respectfully and effectively.</p>
          <ul class="text-muted small ps-3 mb-0">
            <li>Islamic lectures and seminars</li>
            <li>Street and community dawah</li>
            <li>School and university outreach</li>
            <li>Support for new Muslims (reverts)</li>
          </ul>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="srv-icon"><i class="bi bi-moon-stars-fill"></i></div>
          <h4>Mosque & Religious Support</h4>
          <p>ISCAG assists in mosque establishment, prayer activities, and Islamic education support programs in various communities across the region to ensure continuous spiritual development.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="srv-icon"><i class="bi bi-box-seam-fill"></i></div>
          <h4>Charity & Social Services</h4>
          <p class="mb-2">Humanitarian initiatives aimed at supporting the vulnerable and aiding communities.</p>
          <ul class="text-muted small ps-3 mb-0">
            <li>Zakat and Sadaqah distribution</li>
            <li>Disaster relief assistance</li>
            <li>Ramadan iftar programs</li>
            <li>Orphan and scholarship support</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ DONATE ═══════ -->
<section id="donate" class="donate-section">
  <div class="container">
    <h2 class="sec-title">Support ISCAG's Mission</h2>
    <p class="sec-subtitle text-white">Your contribution helps sustain Islamic education and humanitarian service in the Philippines.</p>
    
    <ul class="donate-list">
      <li><i class="bi bi-check-circle-fill text-white"></i> Islamic education</li>
      <li><i class="bi bi-check-circle-fill text-white"></i> Dawah expansion</li>
      <li><i class="bi bi-check-circle-fill text-white"></i> Burial assistance programs</li>
      <li><i class="bi bi-check-circle-fill text-white"></i> Community relief efforts</li>
      <li><i class="bi bi-check-circle-fill text-white"></i> Mosque & infrastructure development</li>
    </ul>

    <a href="#donate" class="btn-donate">Donate Now</a>
  </div>
</section>

<!-- ═══════ CONTACT ═══════ -->
<section id="contact" class="contact-section sec-padding">
  <div class="container">
    <h2 class="sec-title">Get In Touch</h2>
    <p class="sec-subtitle">Have a question or want to get involved? We'd love to hear from you.</p>

    <div class="row g-5">
      <div class="col-lg-5">
        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
          <div class="contact-info-content">
            <h5>Location</h5>
            <p>Dasmariñas, Cavite<br>Philippines</p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
          <div class="contact-info-content">
            <h5>Email</h5>
            <p>info@iscag.org.ph</p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
          <div class="contact-info-content">
            <h5>Phone</h5>
            <p>+63 (2) 8XXX XXXX</p>
          </div>
        </div>
      </div>
      
      <div class="col-lg-7">
        <div class="contact-form">
          <div class="row g-4">
            <div class="col-md-6">
              <input type="text" class="form-control" placeholder="Your Name">
            </div>
            <div class="col-md-6">
              <input type="email" class="form-control" placeholder="Your Email">
            </div>
            <div class="col-12">
              <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
            </div>
            <div class="col-12">
              <button type="button" class="btn-submit">Send Message</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════ FOOTER ═══════ -->
<footer class="footer">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="footer-brand">
          <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" onerror="this.src='https://via.placeholder.com/40'"/>
          ISCAG Philippines
        </div>
        <p class="footer-desc">
          "Sharing Islamic Information Towards Understanding, Peace, and Prosperity of the Nation."
        </p>
        <div class="footer-social">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
          <a href="#"><i class="bi bi-twitter-x"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
        </div>
      </div>
      
      <div class="col-lg-3 offset-lg-1">
        <h4 class="footer-title">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="#home">Home</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#community">Community</a></li>
          <li><a href="#services">Services</a></li>
        </ul>
      </div>
      
      <div class="col-lg-3">
        <h4 class="footer-title">Get Involved</h4>
        <ul class="footer-links">
          <li><a href="#donate">Donate</a></li>
          <li><a href="#contact">Contact Us</a></li>
          <li><a href="#">Volunteer Programs</a></li>
          <li><a href="<?= url('/login') ?>">Member Login</a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-bottom">
      &copy; <?= date('Y') ?> Islamic Studies, Call and Guidance of the Philippines (ISCAG). All Rights Reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>