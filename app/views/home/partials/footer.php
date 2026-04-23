<!-- ══════════════════════ FOOTER ══════════════════════ -->
<footer>
  <div class="container">
    <div class="footer-grid">
      
      <!-- Column 1: About -->
      <div class="footer-about">
        <a href="<?= url('/') ?>" class="footer-logo">
          <div class="footer-logo-icon">
            <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" style="max-width:100%; max-height:100%; object-fit:scale-down;">
          </div>
          <span class="footer-logo-text">ISCAG<br>PHILIPPINES</span>
        </a>
        <p class="footer-desc">
          Islamic Studies, Call and Guidance of the Philippines is dedicated to empowering the Ummah through knowledge, spiritual growth, and community service.
        </p>
        <div class="footer-social">
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>
          <a href="#" class="social-btn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
        </div>
      </div>

      <!-- Column 2: Navigation -->
      <div class="footer-nav">
        <h4 class="footer-title">Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?= url('/') ?>">Home</a></li>
          <li><a href="<?= url('/') ?>#mission-vision">Mission & Vision</a></li>
          <li><a href="<?= url('/history') ?>">History</a></li>
          <li><a href="<?= url('/announcements') ?>">Announcements</a></li>
          <li><a href="<?= url('/contact') ?>">Contact Us</a></li>
        </ul>
      </div>

      <!-- Column 3: Resources -->
      <div class="footer-nav">
        <h4 class="footer-title">Resources</h4>
        <ul class="footer-links">
          <li><a href="#">Islamic Guidance</a></li>
          <li><a href="#">Community Programs</a></li>
          <li><a href="#">Volunteer Portal</a></li>
          <li><a href="#">Donations</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>

      <!-- Column 4: Contact -->
      <div class="footer-contact">
        <h4 class="footer-title">Get In Touch</h4>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="contact-text">123 Islamic Center Way, Dasmariñas City, Cavite, Philippines</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.18-2.18a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <span class="contact-text">+63 (046) 123 4567</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span class="contact-text">info@iscag.org.ph</span>
        </div>
      </div>

    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> ISCAG Philippines. All Rights Reserved.</p>
      <div class="footer-legal">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </div>
    </div>

  </div>
</footer>
