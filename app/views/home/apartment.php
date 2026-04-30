<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Apartment Management - ISCAG Philippines</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= asset('css/site-shared.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background: url('<?= asset('assets/hero-mosque.png') ?>') center/cover no-repeat;
      opacity: 0.1;
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
      color: var(--green-800);
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

    /* ─── GALLERY/IMAGE SECTION ───────────────────────────── */
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

    /* ─── CTA ─────────────────────────────────────────────── */
    .cta-section {
      padding: 100px 0;
      background: var(--green-50);
      text-align: center;
      border-radius: 40px;
      margin: 0 auto 100px;
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
      background: var(--green-800);
      color: white;
      border-radius: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .btn-cta:hover {
      background: var(--green-900);
      transform: translateY(-4px);
      box-shadow: 0 15px 30px rgba(20, 83, 45, 0.2);
    }

    @media (max-width: 991px) {
      .feature-row { flex-direction: column !important; gap: 40px; }
      .feature-img, .feature-body { width: 100%; }
      .hero-title { font-size: 2.8rem; }
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
    <span class="hero-tag reveal">Residential Services</span>
    <h1 class="hero-title reveal">Apartment Management</h1>
    <p class="hero-subtitle reveal">Secure, affordable, and community-focused housing designed to support your spiritual and family life.</p>
  </div>
</header>

<!-- OVERVIEW -->
<section class="content-section">
  <div class="container">
    
    <div class="feature-row reveal">
      <div class="feature-img">
        <img src="<?= asset('assets/1BR Type/1BR front.jpg') ?>" alt="Apartment Exterior">
      </div>
      <div class="feature-body">
        <span class="section-tag">About Our Housing</span>
        <h2>A Home Within a Community</h2>
        <p>The ISCAG Residential Complex is more than just a place to stay. It is a vibrant community where residents can live in an environment that respects and promotes Islamic values. Our units are well-maintained, secure, and conveniently located near the center's prayer and educational facilities.</p>
        <p>We offer various unit types to accommodate different family sizes, ensuring that everyone has a comfortable place to call home.</p>
      </div>
    </div>

    <!-- NEW: UNIT TYPES SECTION -->
    <div class="section-header reveal" style="margin-top: 120px;">
      <span class="section-tag">Accommodation</span>
      <h2 class="section-title">Available Unit Types</h2>
      <p style="color: var(--text-muted); max-width: 600px; margin: 20px auto 0;">Explore our range of modern, well-designed living spaces tailored to your needs.</p>
    </div>

    <div class="row g-4 reveal">
      <?php if (!empty($apartmentTypes)): ?>
        <?php foreach ($apartmentTypes as $type): ?>
          <?php 
            $inclusions = [];
            if (!empty($type['inclusions'])) {
              $inclusions = is_string($type['inclusions']) ? json_decode($type['inclusions'], true) : $type['inclusions'];
            }
            
            $thumbnailUrl = !empty($type['thumbnail_id']) 
              ? url('/api/apartment-types/serve-image') . '?id=' . $type['thumbnail_id']
              : asset('assets/hero-mosque.png');

            // Custom badge logic based on type
            $badge = $type['capacity'] ?? 'Available';
            if ($type['type_key'] === 'studio') $badge = "Best for Individuals";
            elseif ($type['type_key'] === '1br') $badge = "Best for Couples";
            elseif ($type['type_key'] === '2br') $badge = "Best for Families";
            elseif ($type['type_key'] === '1tr') $badge = "Short-term Stay";
            elseif ($type['type_key'] === '1gr') $badge = "Premium Guest";
            elseif ($type['type_key'] === '1bc') $badge = "Bachelor Living";
          ?>
          <div class="col-lg-4">
            <div class="unit-card">
              <div class="unit-img" onclick="openPreview(<?= htmlspecialchars(json_encode($type['images']), ENT_QUOTES, 'UTF-8') ?>)">
                <img src="<?= $thumbnailUrl ?>" alt="<?= htmlspecialchars($type['label']) ?>">
                <span class="unit-badge"><?= htmlspecialchars($badge) ?></span>
              </div>
              <div class="unit-info">
                <h3><?= htmlspecialchars($type['label']) ?></h3>
                <p><?= htmlspecialchars($type['description'] ?: 'Modern living space designed for comfort and tranquility.') ?></p>
                <ul class="unit-features">
                  <?php if (!empty($inclusions) && is_array($inclusions)): ?>
                    <?php foreach (array_slice($inclusions, 0, 3) as $inc): ?>
                      <li><span></span> <?= htmlspecialchars($inc) ?></li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li><span></span> Private Bathroom</li>
                    <li><span></span> Kitchenette Area</li>
                    <li><span></span> Secure Access</li>
                  <?php endif; ?>
                </ul>
                <div class="unit-footer">
                  <span class="unit-price">
                    ₱<?= number_format($type['price'], 2) ?>
                    <small style="font-size: 0.65rem; color: var(--text-muted); display: block; font-weight: 400;">Per Month</small>
                  </span>
                  <a href="<?= url('/register') ?>" class="unit-btn">Apply Now</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center py-5">
          <p class="text-muted">No units available at the moment. Please check back later.</p>
        </div>
      <?php endif; ?>
    </div>

    <style>
      .unit-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--border);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
      }
      .unit-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: var(--green-100);
      }
      .unit-img {
        height: 240px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
      }
      .unit-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
      }
      .unit-card:hover .unit-img img { transform: scale(1.1); }
      .unit-img::after {
        content: "VIEW";
        font-family: 'DM Sans', sans-serif;
        font-weight: 800;
        position: absolute;
        inset: 0;
        background: rgba(6, 78, 59, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(6px);
        letter-spacing: 2px;
      }
      .carousel-control-prev, .carousel-control-next {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.1) !important;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
      }
      .carousel-control-prev:hover, .carousel-control-next:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
      }
      .carousel-control-prev { left: 40px; }
      .carousel-control-next { right: 40px; }
      
      #imagePreviewModal .modal-content {
        background-color: rgba(0, 0, 0, 0.4) !important;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        overflow: hidden !important;
        height: 100vh;
      }
      #imagePreviewModal {
        overflow: hidden !important;
      }
      .carousel-item {
        height: 100vh;
        background: transparent;
        display: none; /* Let Bootstrap handle basic show/hide */
      }
      .carousel-item.active,
      .carousel-item-next,
      .carousel-item-prev {
        display: flex !important;
        align-items: center;
        justify-content: center;
      }
      .carousel-item img {
        max-height: 100vh;
        max-width: 100vw;
        object-fit: contain;
      }
      .unit-img:hover::after { opacity: 1; }
      .unit-badge {
        position: absolute;
        top: 15px; right: 15px;
        background: var(--gold);
        color: var(--green-900);
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
      }
      .unit-info {
        padding: 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      .unit-info h3 {
        font-family: 'Lora', serif;
        font-size: 1.6rem;
        color: var(--green-900);
        margin-bottom: 15px;
      }
      .unit-info p {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 20px;
      }
      .unit-features {
        list-style: none;
        padding: 0; margin: 0 0 25px;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      .unit-features li {
        font-size: 0.85rem;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
      }
      .unit-features span {
        width: 6px; height: 6px;
        background: var(--gold);
        border-radius: 50%;
      }
      .unit-footer {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .unit-price {
        font-weight: 700;
        color: var(--green-800);
        font-size: 1.1rem;
      }
      .unit-btn {
        color: var(--gold);
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.3s ease;
      }
      .unit-btn:hover { color: var(--green-700); text-decoration: underline; }
    </style>

    <div class="section-header reveal" style="margin-top: 120px;">
      <span class="section-tag">Features</span>
      <h2 class="section-title">Why Choose ISCAG Housing?</h2>
    </div>

    <div class="info-grid reveal">
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Safe & Secure</h3>
        <p>24/7 security and a safe environment for families and children to thrive within the ISCAG premises.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>Community Focus</h3>
        <p>Live among fellow believers and participate in regular community prayers, lectures, and social events.</p>
      </div>
      <div class="info-card">
        <div class="info-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <h3>Islamic Environment</h3>
        <p>Architecture and community rules designed to uphold the modesty and tranquility of an Islamic lifestyle.</p>
      </div>
    </div>

  </div>
</section>

<!-- CTA -->
<section class="cta-section container reveal">
  <h2 class="cta-title">Ready to Join Our Community?</h2>
  <p class="cta-desc">Applications are open for qualified individuals and families. Start your journey with us today by applying through our online portal.</p>
  <a href="<?= url('/register') ?>" class="btn-cta">
    Apply for Apartment
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>
</section>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/scripts.php'; ?>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1100;"></button>
      <div class="modal-body p-0">
        <div id="previewCarousel" class="carousel slide h-100" data-bs-ride="false">
          <div class="carousel-inner" id="carouselInner">
            <!-- Images injected here -->
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function openPreview(images) {
  const inner = document.getElementById('carouselInner');
  inner.innerHTML = '';
  
  if (!images || images.length === 0) return;

  images.forEach((img, idx) => {
    const div = document.createElement('div');
    div.className = `carousel-item ${idx === 0 ? 'active' : ''}`;
    
    const url = `<?= url('/api/apartment-types/serve-image') ?>?id=${img.image_id}`;
    div.innerHTML = `<img src="${url}" class="d-block" alt="Unit View">`;
    inner.appendChild(div);
  });

  const modalEl = document.getElementById('imagePreviewModal');
  const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  
  const carouselEl = document.getElementById('previewCarousel');
  let carousel = bootstrap.Carousel.getInstance(carouselEl);
  if (!carousel) {
    carousel = new bootstrap.Carousel(carouselEl, {
      interval: false,
      ride: false
    });
  } else {
    carousel.to(0); // Reset to first slide
  }
  
  modal.show();
}

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
