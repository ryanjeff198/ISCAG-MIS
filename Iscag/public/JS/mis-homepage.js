/* ═══════════════════════════════════════════════════════════════
   MIS HOMEPAGE — JAVASCRIPT
   Handles navigation, dropdowns, smooth scrolling,
   scroll-reveal animations, and interactive behaviors
   ═══════════════════════════════════════════════════════════════ */

(function () {
  'use strict';

  // ─── DOM REFERENCES ───
  const nav          = document.getElementById('misNav');
  const mobileNav    = document.getElementById('misMobileNav');
  const mobileToggle = document.getElementById('misNavToggle');
  const scrollTopBtn = document.getElementById('misScrollTop');

  // ─── 1. NAVBAR — SCROLL EFFECT ───
  let lastScroll = 0;

  function handleNavScroll() {
    const y = window.scrollY;
    if (y > 40) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
    lastScroll = y;
  }

  window.addEventListener('scroll', handleNavScroll, { passive: true });
  handleNavScroll(); // initial check


  // ─── 2. MOBILE NAV TOGGLE ───
  let mobileOpen = false;

  mobileToggle.addEventListener('click', function () {
    mobileOpen = !mobileOpen;
    mobileNav.classList.toggle('open', mobileOpen);
    mobileToggle.setAttribute('aria-expanded', mobileOpen);

    // Update icon
    const icon = mobileToggle.querySelector('i');
    if (mobileOpen) {
      icon.className = 'bi bi-x-lg';
    } else {
      icon.className = 'bi bi-list';
    }
  });

  // Close mobile nav when clicking a link
  mobileNav.querySelectorAll('a:not(.mis-nav__mobile-dropdown-trigger)').forEach(function (link) {
    link.addEventListener('click', function () {
      mobileOpen = false;
      mobileNav.classList.remove('open');
      mobileToggle.setAttribute('aria-expanded', false);
      const icon = mobileToggle.querySelector('i');
      icon.className = 'bi bi-list';
    });
  });


  // ─── 3. DESKTOP DROPDOWN ───
  const desktopDropdowns = document.querySelectorAll('.mis-nav__dropdown');

  desktopDropdowns.forEach(function (dropdown) {
    const trigger = dropdown.querySelector('.mis-nav__dropdown-trigger');

    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Close other dropdowns first
      desktopDropdowns.forEach(function (other) {
        if (other !== dropdown) other.classList.remove('open');
      });

      dropdown.classList.toggle('open');
    });
  });

  // Close dropdown on outside click
  document.addEventListener('click', function (e) {
    desktopDropdowns.forEach(function (dropdown) {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
      }
    });
  });


  // ─── 4. MOBILE DROPDOWN ───
  const mobileDdTriggers = document.querySelectorAll('.mis-nav__mobile-dropdown-trigger');

  mobileDdTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      const items = trigger.nextElementSibling;
      const chevron = trigger.querySelector('.mis-chevron');

      items.classList.toggle('open');

      if (chevron) {
        chevron.style.transform = items.classList.contains('open')
          ? 'rotate(180deg)'
          : 'rotate(0deg)';
      }
    });
  });


  // ─── 5. SMOOTH SCROLL ───
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;

      const target = document.querySelector(targetId);
      if (!target) return;

      e.preventDefault();

      const navHeight = nav.offsetHeight;
      const targetPos = target.getBoundingClientRect().top + window.scrollY - navHeight - 20;

      window.scrollTo({
        top: targetPos,
        behavior: 'smooth'
      });

      // Close mobile nav if open
      if (mobileOpen) {
        mobileOpen = false;
        mobileNav.classList.remove('open');
        mobileToggle.setAttribute('aria-expanded', false);
        const icon = mobileToggle.querySelector('i');
        icon.className = 'bi bi-list';
      }
    });
  });


  // ─── 6. SCROLL-TO-TOP BUTTON ───
  function handleScrollTop() {
    if (window.scrollY > 500) {
      scrollTopBtn.classList.add('visible');
    } else {
      scrollTopBtn.classList.remove('visible');
    }
  }

  window.addEventListener('scroll', handleScrollTop, { passive: true });

  scrollTopBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });


  // ─── 7. SCROLL-REVEAL ANIMATIONS ───
  const revealElements = document.querySelectorAll('.mis-reveal');

  function checkReveal() {
    const windowHeight = window.innerHeight;
    const triggerPoint = windowHeight * 0.88;

    revealElements.forEach(function (el) {
      const rect = el.getBoundingClientRect();
      if (rect.top < triggerPoint) {
        el.classList.add('visible');
      }
    });
  }

  window.addEventListener('scroll', checkReveal, { passive: true });
  window.addEventListener('resize', checkReveal, { passive: true });

  // Run on load
  checkReveal();


  // ─── 8. HERO BACKGROUND FALLBACK ───
  // If the hero has a background image and it fails to load,
  // gracefully fall back to gradient-only mode
  const heroBgImg = document.querySelector('.mis-hero__bg img');

  if (heroBgImg) {
    heroBgImg.addEventListener('error', function () {
      const hero = document.querySelector('.mis-hero');
      hero.classList.add('mis-hero--no-image');
      this.style.display = 'none';
    });

    // Also check if already errored (cached)
    if (heroBgImg.complete && heroBgImg.naturalWidth === 0) {
      const hero = document.querySelector('.mis-hero');
      hero.classList.add('mis-hero--no-image');
      heroBgImg.style.display = 'none';
    }
  }


  // ─── 9. COUNTER ANIMATION ───
  const counterElements = document.querySelectorAll('[data-counter]');
  let countersStarted = false;

  function animateCounters() {
    if (countersStarted) return;

    const windowHeight = window.innerHeight;

    counterElements.forEach(function (el) {
      const rect = el.getBoundingClientRect();
      if (rect.top < windowHeight * 0.9) {
        countersStarted = true;
        startCounter(el);
      }
    });
  }

  function startCounter(el) {
    const target = parseInt(el.getAttribute('data-counter'), 10);
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 2000;
    const stepTime = 16;
    const steps = Math.ceil(duration / stepTime);
    let current = 0;
    let step = 0;

    const timer = setInterval(function () {
      step++;
      // Ease out quad
      const progress = step / steps;
      const eased = 1 - (1 - progress) * (1 - progress);
      current = Math.floor(eased * target);

      el.textContent = current.toLocaleString() + suffix;

      if (step >= steps) {
        el.textContent = target.toLocaleString() + suffix;
        clearInterval(timer);
      }
    }, stepTime);
  }

  window.addEventListener('scroll', animateCounters, { passive: true });
  animateCounters(); // check on load


  // ─── 10. ACTIVE NAV LINK TRACKING ───
  const sections = document.querySelectorAll('[data-nav-section]');
  const navLinks = document.querySelectorAll('.mis-nav__link[data-nav-target]');

  function updateActiveNav() {
    const scrollPos = window.scrollY + 150;

    sections.forEach(function (section) {
      const sectionTop = section.offsetTop;
      const sectionBottom = sectionTop + section.offsetHeight;
      const sectionId = section.getAttribute('data-nav-section');

      if (scrollPos >= sectionTop && scrollPos < sectionBottom) {
        navLinks.forEach(function (link) {
          link.classList.remove('active');
          if (link.getAttribute('data-nav-target') === sectionId) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  window.addEventListener('scroll', updateActiveNav, { passive: true });

})();
