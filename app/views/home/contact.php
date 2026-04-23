<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | ISCAG-MIS</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/site-shared.css') ?>">
  
  <style>
    :root {
      --green-900: #064e3b;
      --green-800: #065f46;
      --green-700: #047857;
      --green-600: #059669;
      --green-500: #10b981;
      --green-50: #ecfdf5;
      --gold: #d4af37;
      --text-main: #1f2937;
      --text-muted: #4b5563;
      --border: #e5e7eb;
    }

    body {
      font-family: 'Inter', sans-serif;
      color: var(--text-main);
      background-color: #fbfcfb;
      padding-top: 80px; /* Navbar space */
    }

    /* ─── HERO SECTION ───────────────────────────────────────── */
    .contact-hero {
      padding: 100px 0 80px;
      background: linear-gradient(135deg, var(--green-900) 0%, var(--green-800) 100%);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .contact-hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('<?= asset('assets/hero-mosque.png') ?>') center/cover no-repeat;
      opacity: 0.1;
      z-index: 0;
    }
    .contact-hero .container { position: relative; z-index: 1; }
    .hero-tag {
      display: inline-block;
      padding: 8px 20px;
      background: rgba(212, 175, 55, 0.2);
      border: 1px solid var(--gold);
      color: var(--gold);
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 24px;
    }
    .hero-title {
      font-family: 'Lora', serif;
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .hero-subtitle {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
      line-height: 1.6;
    }

    /* ─── CONTACT CONTENT ────────────────────────────────────── */
    .contact-wrapper {
      margin-top: -60px;
      position: relative;
      z-index: 10;
      padding-bottom: 100px;
    }
    .contact-main-card {
      background: white;
      border-radius: 32px;
      box-shadow: 0 30px 60px rgba(0,0,0,0.08);
      overflow: hidden;
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      border: 1px solid var(--border);
    }

    /* Form Side */
    .form-side {
      padding: 60px;
    }
    .form-header { margin-bottom: 40px; }
    .form-header h2 {
      font-family: 'Lora', serif;
      font-size: 2rem;
      color: var(--green-900);
      margin-bottom: 10px;
    }
    .form-header p { color: var(--text-muted); }

    .form-group { margin-bottom: 24px; }
    .form-label {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--green-900);
      margin-bottom: 8px;
      display: block;
    }
    .form-control {
      border: 1px solid var(--border);
      padding: 14px 18px;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f9fafb;
    }
    .form-control:focus {
      border-color: var(--green-500);
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
      background: white;
    }
    textarea.form-control { min-height: 150px; }

    .btn-send {
      width: 100%;
      padding: 16px;
      background: var(--green-800);
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
      margin-top: 10px;
    }
    .btn-send:hover {
      background: var(--green-900);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(6, 78, 59, 0.2);
    }

    /* Info Side */
    .info-side {
      background: var(--green-900);
      padding: 60px;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }
    .info-side::after {
      content: "";
      position: absolute;
      bottom: 0; right: 0;
      width: 200px; height: 200px;
      background: radial-gradient(circle, var(--gold) 0%, transparent 70%);
      opacity: 0.1;
      filter: blur(40px);
    }

    .info-item {
      display: flex;
      gap: 20px;
      margin-bottom: 40px;
    }
    .info-icon {
      width: 50px; height: 50px;
      background: rgba(255,255,255,0.1);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold);
      flex-shrink: 0;
    }
    .info-text h4 {
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .info-text p {
      opacity: 0.8;
      font-size: 1rem;
      line-height: 1.5;
      margin: 0;
    }

    .info-link {
      color: inherit;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .info-link:hover {
      color: var(--gold);
      text-decoration: underline;
      opacity: 1;
    }

    /* ─── FAQ SECTION ────────────────────────────────────────── */
    .faq-section {
      padding: 100px 0;
      background: white;
    }
    .faq-header {
      text-align: center;
      margin-bottom: 60px;
    }
    .faq-header h2 {
      font-family: 'Lora', serif;
      font-size: 2.5rem;
      color: var(--green-900);
      margin-bottom: 15px;
    }
    .faq-header p {
      color: var(--text-muted);
      max-width: 600px;
      margin: 0 auto;
    }

    .faq-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .faq-item {
      border: 1px solid var(--border);
      border-radius: 16px;
      margin-bottom: 15px;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .faq-item:hover {
      border-color: var(--green-500);
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .faq-question {
      padding: 24px 30px;
      background: white;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 600;
      color: var(--green-900);
      transition: all 0.3s ease;
    }
    .faq-item.is-open .faq-question {
      background: var(--green-50);
      color: var(--green-800);
    }
    .faq-icon {
      width: 24px; height: 24px;
      transition: transform 0.3s ease;
    }
    .faq-item.is-open .faq-icon {
      transform: rotate(180deg);
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      background: white;
    }
    .faq-item.is-open .faq-answer {
      max-height: 500px;
    }
    .faq-answer-inner {
      padding: 0 30px 24px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 40px;
    }
    .social-link {
      width: 44px; height: 44px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .social-link:hover {
      background: var(--gold);
      color: var(--green-900);
      transform: translateY(-5px);
    }

    /* ─── MAP SECTION ────────────────────────────────────────── */
    .map-section {
      padding-bottom: 100px;
    }
    .map-label {
      margin-bottom: 30px;
      text-align: center;
    }
    .map-label h3 {
      font-family: 'Lora', serif;
      font-size: 2rem;
      color: var(--green-900);
      margin-bottom: 8px;
    }
    .map-label p {
      color: var(--text-muted);
      font-size: 1.1rem;
    }
    .map-container {
      width: 100%;
      height: 450px;
      border-radius: 32px;
      overflow: hidden;
      border: 1px solid var(--border);
      background: #eee;
    }

    @media (max-width: 991px) {
      .contact-main-card { grid-template-columns: 1fr; }
      .info-side { order: -1; padding: 40px; }
      .form-side { padding: 40px; }
      .hero-title { font-size: 2.5rem; }
    }

    /* ─── REVEAL ANIMATION ───────────────────────────────────── */
    .reveal {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<!-- Hero -->
<section class="contact-hero">
  <div class="container">
    <span class="hero-tag reveal">Connect With Us</span>
    <h1 class="hero-title reveal">How can we <span>help you?</span></h1>
    <p class="hero-subtitle reveal">Whether you have questions about our services, want to volunteer, or need support, our team is here for you.</p>
  </div>
</section>

<!-- Main Contact Section -->
<div class="contact-wrapper">
  <div class="container">
    <div class="contact-main-card reveal">
      <!-- Form -->
      <div class="form-side">
        <div class="form-header">
          <h2>Send a Message</h2>
          <p>Fill out the form below and we'll get back to you shortly.</p>
        </div>
        <form action="<?= url('/contact/send') ?>" method="POST">
          <div class="row">
            <div class="col-md-6 form-group">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" placeholder="John Doe" required>
            </div>
            <div class="col-md-6 form-group">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" placeholder="john@example.com" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" placeholder="What is this regarding?" required>
          </div>
          <div class="form-group">
            <label class="form-label">Message</label>
            <textarea class="form-control" name="message" placeholder="Your message here..." required></textarea>
          </div>
          <button type="submit" class="btn-send">Send Message</button>
        </form>
      </div>

      <!-- Info -->
      <div class="info-side">
        <div>
          <div class="info-item">
            <div class="info-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="info-text">
              <h4>Our Location</h4>
              <p>Aguinaldo Highway, Brgy. Salitran I,<br>Dasmariñas, Cavite, Philippines</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div class="info-text">
              <h4>Phone Number</h4>
              <p>+63 912 345 6789<br>Tue-Sun, 9am - 5pm</p>
            </div>
          </div>
          <div class="info-item">
            <div class="info-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div class="info-text">
              <h4>Email Support</h4>
              <p>
                <a href="mailto:info@iscag.org" class="info-link">info@iscag.org</a><br>
                <a href="mailto:support@iscag.org" class="info-link">support@iscag.org</a>
              </p>
            </div>
          </div>
        </div>

        <div>
          <h4>Follow Us</h4>
          <div class="social-links">
            <a href="#" class="social-link">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="#" class="social-link">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
            <a href="#" class="social-link">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FAQ Section -->
<section class="faq-section">
  <div class="container">
    <div class="faq-header reveal">
      <h2>Frequently Asked Questions</h2>
      <p>Find quick answers to common questions about our services and programs.</p>
    </div>

    <div class="faq-container">
      <!-- Q1 -->
      <div class="faq-item reveal">
        <div class="faq-question">
          <span>What is the mission of ISCAG?</span>
          <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="faq-answer">
          <div class="faq-answer-inner">
            ISCAG is dedicated to Islamic Studies, Call and Guidance in the Philippines. Our mission is to provide authentic religious education, spiritual support, and community services to both Muslims and those interested in learning about Islam.
          </div>
        </div>
      </div>

      <!-- Q2 -->
      <div class="faq-item reveal">
        <div class="faq-question">
          <span>How can I apply for apartment housing?</span>
          <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="faq-answer">
          <div class="faq-answer-inner">
            You can apply directly through our online portal. Simply register for an account, navigate to the Apartment Management section, and fill out the application form. Our staff will review your request and get in touch.
          </div>
        </div>
      </div>

      <!-- Q3 -->
      <div class="faq-item reveal">
        <div class="faq-question">
          <span>What social welfare services do you provide?</span>
          <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="faq-answer">
          <div class="faq-answer-inner">
            Through our Damayan Department, we offer bereavement support, burial services coordination, and emergency welfare assistance to community members in need.
          </div>
        </div>
      </div>

      <!-- Q4 -->
      <div class="faq-item reveal">
        <div class="faq-question">
          <span>Is the center open to visitors?</span>
          <svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="faq-answer">
          <div class="faq-answer-inner">
            Yes, visitors are welcome from Tuesday to Sunday, between 9:00 AM and 5:00 PM. We recommend contacting us in advance if you're planning a group visit or require a specific guide.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Map -->
<section class="map-section reveal">
  <div class="container">
    <div class="map-label">
      <h3>Visit our Center</h3>
      <p>Located along Aguinaldo Highway for easy accessibility.</p>
    </div>
    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps?q=14.3510075,120.9421161(ISCAG+Center)&hl=en&z=16&output=embed" 
        width="100%" 
        height="100%" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Simple reveal on scroll
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

  // FAQ Accordion
  const faqQuestions = document.querySelectorAll('.faq-question');
  faqQuestions.forEach(item => {
    item.addEventListener('click', function() {
      const parent = this.parentElement;
      const isOpen = parent.classList.contains('is-open');
      
      // Close all other items first
      document.querySelectorAll('.faq-item').forEach(child => {
        child.classList.remove('is-open');
      });
      
      // Toggle current item
      if (!isOpen) {
        parent.classList.add('is-open');
      }
    });
  });

  window.addEventListener("scroll", reveal);
  reveal(); // Initial check
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
