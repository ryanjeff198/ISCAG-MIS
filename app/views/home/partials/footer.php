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
          <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1CiGL37My5/" class="social-btn" title="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
          <!-- Google -->
          <a href="mailto:iscagphilippines@gmail.com" class="social-btn" title="Google"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.48 10.92v3.28h4.74c-.2 1.04-1.2 3.05-4.74 3.05-3.09 0-5.62-2.56-5.62-5.71s2.53-5.71 5.62-5.71c1.76 0 2.94.75 3.61 1.39l2.58-2.48C17.02 3.14 14.93 2.11 12.48 2.11 7.15 2.11 2.83 6.44 2.83 11.78s4.32 9.67 9.65 9.67c5.57 0 9.27-3.91 9.27-9.44 0-.64-.07-1.12-.15-1.6l-9.12-.49z"/></svg></a>
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
          <li><a href="<?= url('/daawah') ?>">Islamic Guidance</a></li>
          <li><a href="<?= url('/events') ?>">Community Events</a></li>
          <li><a href="<?= url('/damayan') ?>#donations">Donations</a></li>
          <li><a href="<?= url('/contact') ?>#faq">FAQ</a></li>
        </ul>
      </div>

      <!-- Column 4: Contact -->
      <div class="footer-contact">
        <h4 class="footer-title">Get In Touch</h4>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span class="contact-text">Aguinaldo Highway, Brgy. Salitran I, Dasmariñas, Cavite, Philippines</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l2.18-2.18a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <span class="contact-text">(046) 4161589</span>
        </div>
        <div class="footer-contact-item">
          <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <span class="contact-text"><a href="mailto:iscagphilippines@gmail.com" style="color: inherit; text-decoration: none;">iscagphilippines@gmail.com</a></span>
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
