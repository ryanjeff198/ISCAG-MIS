/**
 * ISCAG MIS — Room Preview Module
 * Self-contained modal with image carousel, room details, and availability status.
 * Supports both legacy string IDs and dynamic database objects.
 */

/* ══════════════════════════════════════════
   Room Data (Legacy Fallbacks)
   ══════════════════════════════════════════ */

const ROOM_DATA = {
  studio: {
    type: 'studio', label: 'Studio Unit', price: '₱3,500 / month', capacity: '1–2 persons',
    description: 'A compact and efficient living space perfect for individuals or couples.',
    images: ['Studio Type/Studio type 1.jpg', 'Studio Type/Studio type 2.jpg'],
    imageCaptions: ['Living Area', 'Kitchenette'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '22 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '1–2 persons' }
    ]
  },
  '1br': {
    type: '1br', label: 'One-Bedroom Unit', price: '₱5,000 / month', capacity: '2–3 persons',
    description: 'A comfortable one-bedroom apartment ideal for small families or couples.',
    images: ['1BR Type/1BR 1.jpg', '1BR Type/1BR 2.jpg'],
    imageCaptions: ['Kitchen & Living', 'Bedroom'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '35 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '2–3 persons' }
    ]
  },
  '2br': {
    type: '2br', label: 'Two-Bedroom Unit', price: '₱7,500 / month', capacity: '3–5 persons',
    description: 'A spacious two-bedroom apartment designed for growing families.',
    images: ['2BR Type/2BR 1.png', '2BR Type/2BR front.png'],
    imageCaptions: ['Kitchen', 'Front View'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '50 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '3–5 persons' }
    ]
  }
};

/* ══════════════════════════════════════════
   CSS Injection (runs once)
   ══════════════════════════════════════════ */

(function injectRoomPreviewCSS() {
  if (document.getElementById('room-preview-styles')) return;
  const style = document.createElement('style');
  style.id = 'room-preview-styles';
  style.textContent = `
    .rp-overlay {
      position: fixed; inset: 0; z-index: 90000;
      display: flex; align-items: center; justify-content: center;
      background: rgba(15,30,22,0.6);
      backdrop-filter: blur(6px);
      padding: 24px;
      animation: rpFadeIn 0.25s ease;
    }
    @keyframes rpFadeIn { from { opacity:0; } to { opacity:1; } }
    @keyframes rpSlideUp {
      from { opacity:0; transform:translateY(20px) scale(0.97); }
      to   { opacity:1; transform:translateY(0) scale(1); }
    }
    @keyframes rpFadeOut { from { opacity:1; } to { opacity:0; } }

    .rp-modal {
      background: white; border-radius: 14px;
      width: 100%; max-width: 600px;
      max-height: 90vh; overflow: hidden;
      box-shadow: 0 24px 64px rgba(0,0,0,0.28);
      animation: rpSlideUp 0.3s ease;
      display: flex; flex-direction: column;
    }

    .rp-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 22px; border-bottom: 1px solid #d9e3de;
      background: linear-gradient(135deg, #0f5c3a 0%, #2f8a60 100%);
      position: relative;
    }
    .rp-header-left { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
    .rp-header-left h3 { font-family: 'Lora', serif; font-size: 1.08rem; font-weight: 700; color: white; margin: 0; }
    .rp-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
    .rp-badge.available { background: rgba(47,138,96,0.2); color: #b8f0d0; }
    .rp-badge.unavailable { background: rgba(139,46,46,0.25); color: #f0c4c4; }
    .rp-badge-dot { width: 7px; height: 7px; border-radius: 50%; }
    .rp-badge.available .rp-badge-dot { background: #5effa0; }
    .rp-badge.unavailable .rp-badge-dot { background: #ff6b6b; }
    .rp-close { background: rgba(255,255,255,0.15); border: none; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .rp-close svg { width: 16px; height: 16px; fill: white; }

    .rp-body { overflow-y: auto; flex: 1; }
    .rp-carousel { position: relative; background: #f0f2f1; overflow: hidden; }
    .rp-carousel-track { display: flex; transition: transform 0.4s cubic-bezier(0.25,0.8,0.25,1); }
    .rp-carousel-slide { min-width: 100%; position: relative; }
    .rp-carousel-slide img { width: 100%; height: 280px; object-fit: cover; display: block; }
    .rp-slide-caption { position: absolute; bottom: 0; left: 0; right: 0; padding: 8px 16px; background: linear-gradient(to top, rgba(0,0,0,0.55), transparent); color: white; font-size: 0.78rem; font-weight: 600; text-align: center; }
    .rp-carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 2; }
    .rp-carousel-btn.prev { left: 12px; }
    .rp-carousel-btn.next { right: 12px; }
    .rp-carousel-dots { position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%); display: flex; gap: 7px; z-index: 2; }
    .rp-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); border: none; cursor: pointer; }
    .rp-dot.active { background: white; transform: scale(1.3); }

    .rp-details { padding: 22px; }
    .rp-price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .rp-price { font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: #0f5c3a; }
    .rp-capacity { font-size: 0.78rem; color: #6f7f78; display: flex; align-items: center; gap: 5px; }
    .rp-desc { font-size: 0.87rem; color: #1f2e2a; line-height: 1.65; margin-bottom: 18px; }
    .rp-features-title { font-family: 'Lora', serif; font-size: 0.85rem; font-weight: 700; color: #0f5c3a; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid rgba(23,107,69,0.12); }
    .rp-features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .rp-feature { padding: 10px; background: #f4f6f5; border-radius: 8px; text-align: center; border: 1px solid #d9e3de; }
    .rp-feature-icon { display:flex; align-items:center; justify-content:center; margin-bottom: 4px; color: #176b45; }
    .rp-feature-label { font-size: 0.68rem; font-weight: 600; color: #6f7f78; text-transform: uppercase; margin-bottom: 2px; }
    .rp-feature-value { font-size: 0.82rem; font-weight: 700; color: #1f2e2a; }

    .rp-footer { padding: 14px 22px; border-top: 1px solid #d9e3de; display: flex; gap: 10px; justify-content: flex-end; background: #f8faf9; }
    .rp-btn-close-footer { padding: 9px 20px; border-radius: 8px; border: 1.5px solid #d9e3de; background: white; color: #6f7f78; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
    .rp-btn-select { padding: 9px 20px; border-radius: 8px; border: none; background: linear-gradient(135deg, #0f5c3a, #2f8a60); color: white; font-size: 0.85rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(23,107,69,0.25); }
  `;
  document.head.appendChild(style);
})();

/**
 * Open the Room Preview modal.
 * @param {string|Object} unitData — 'studio' string OR a full database object
 * @param {Object} options
 */
function openRoomPreview(unitData, options = {}) {
  let room;
  let unitType = '';
  
  if (typeof unitData === 'string') {
    unitType = unitData;
    room = ROOM_DATA[unitData];
  } else {
    unitType = unitData.type_key;
    // Map Database object to UI structure
    room = {
      type: unitData.type_key,
      label: unitData.label,
      price: '₱' + Number(unitData.price).toLocaleString() + ' / month',
      capacity: unitData.capacity,
      description: unitData.description,
      // If images is an array of objects from API
      images: Array.isArray(unitData.images) 
        ? unitData.images.map(img => typeof img === 'string' ? img : img.file_path)
        : (unitData.thumbnail_id ? [`/api/apartment-types/serve-image?id=${unitData.thumbnail_id}`] : []),
      imageCaptions: Array.isArray(unitData.images) 
        ? unitData.images.map(img => img.caption || '')
        : ['Main View'],
      features: [
        { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: unitData.floor_area || '--' },
        { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: unitData.capacity || '--' }
      ]
    };
    // Use the available_count from the object if present
    if (unitData.available_count !== undefined && options.availableCount === undefined) {
      options.availableCount = unitData.available_count;
    }
  }

  if (!room) { console.error('Room Preview: Invalid unit data', unitData); return; }

  const {
    availableCount = 0,
    basePath = '', // Default to empty if using dynamic URLs
    onSelect = null,
    selectLabel = 'Select This Unit'
  } = options;

  const isAvailable = availableCount > 0;

  // Remove any existing modal
  const existing = document.getElementById('room-preview-overlay');
  if (existing) existing.remove();

  // Build image slides
  const slides = room.images.length > 0 
    ? room.images.map((img, i) => {
        const src = img.startsWith('http') || img.startsWith('/') ? img : (basePath + img);
        return `
          <div class="rp-carousel-slide">
            <img src="${src}" alt="${room.label}" loading="lazy" />
            <div class="rp-slide-caption">${room.imageCaptions[i] || ''}</div>
          </div>
        `;
      }).join('')
    : `<div class="rp-carousel-slide"><div style="height:280px; display:flex; align-items:center; justify-content:center; background:#eee; color:#999;">No Images Available</div></div>`;

  const dots = room.images.length > 1 ? room.images.map((_, i) => `
    <button class="rp-dot ${i === 0 ? 'active' : ''}" data-index="${i}"></button>
  `).join('') : '';

  // Build features grid
  const features = room.features.map(f => `
    <div class="rp-feature">
      <div class="rp-feature-icon">${f.icon}</div>
      <div class="rp-feature-label">${f.label}</div>
      <div class="rp-feature-value">${f.value}</div>
    </div>
  `).join('');

  const badge = isAvailable
    ? `<span class="rp-badge available"><span class="rp-badge-dot"></span>${availableCount} Available</span>`
    : `<span class="rp-badge unavailable"><span class="rp-badge-dot"></span>Not Available</span>`;

  const html = `
    <div class="rp-overlay" id="room-preview-overlay">
      <div class="rp-modal">
        <div class="rp-header">
          <div class="rp-header-left">
            <h3>${room.label}</h3>
            ${badge}
          </div>
          <button class="rp-close" id="rp-close-btn">&times;</button>
        </div>
        <div class="rp-body">
          <div class="rp-carousel">
            <div class="rp-carousel-track" id="rp-track">${slides}</div>
            ${room.images.length > 1 ? `
              <button class="rp-carousel-btn prev" id="rp-prev">&lt;</button>
              <button class="rp-carousel-btn next" id="rp-next">&gt;</button>
              <div class="rp-carousel-dots" id="rp-dots">${dots}</div>
            ` : ''}
          </div>
          <div class="rp-details">
            <div class="rp-price-row">
              <span class="rp-price">${room.price}</span>
              <span class="rp-capacity">${room.capacity}</span>
            </div>
            <p class="rp-desc">${room.description}</p>
            <div class="rp-features-title">Specifications</div>
            <div class="rp-features-grid">${features}</div>
          </div>
        </div>
        <div class="rp-footer">
          <button class="rp-btn-close-footer" id="rp-close-footer">Close</button>
          ${onSelect ? `<button class="rp-btn-select" id="rp-select-btn" ${!isAvailable ? 'disabled' : ''}>${selectLabel}</button>` : ''}
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', html);

  // Carousel logic
  if (room.images.length > 1) {
    let currentSlide = 0;
    const track = document.getElementById('rp-track');
    const dotsContainer = document.getElementById('rp-dots');
    
    function goToSlide(idx) {
      currentSlide = (idx + room.images.length) % room.images.length;
      track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
      if (dotsContainer) {
        dotsContainer.querySelectorAll('.rp-dot').forEach((d, i) => d.classList.toggle('active', i === currentSlide));
      }
    }
    
    document.getElementById('rp-prev').onclick = () => goToSlide(currentSlide - 1);
    document.getElementById('rp-next').onclick = () => goToSlide(currentSlide + 1);
    if (dotsContainer) {
      dotsContainer.querySelectorAll('.rp-dot').forEach(dot => {
        dot.onclick = () => goToSlide(parseInt(dot.dataset.index));
      });
    }
  }

  // Close logic
  const overlay = document.getElementById('room-preview-overlay');
  const close = () => { overlay.style.animation='rpFadeOut 0.2s forwards'; setTimeout(()=>overlay.remove(), 200); };
  document.getElementById('rp-close-btn').onclick = close;
  document.getElementById('rp-close-footer').onclick = close;
  overlay.onclick = (e) => { if(e.target === overlay) close(); };

  if (onSelect && isAvailable) {
    document.getElementById('rp-select-btn').onclick = () => { onSelect(unitType); close(); };
  }
}
