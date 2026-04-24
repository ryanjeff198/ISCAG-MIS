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
    description: 'A compact and efficient living space perfect for individuals or couples. Features an open-plan layout with a separate bathroom and kitchenette.',
    images: ['Studio Type/Studio type 1.jpg', 'Studio Type/Studio type 2.jpg', 'Studio Type/Studio type 3.jpg', 'Studio Type/Studio type 4.jpg', 'Studio Type/Studio type 5.jpg', 'Studio Type/Studio type 6.jpg', 'Studio Type/Studio type 7.jpg'],
    imageCaptions: ['Hallway', 'Living Area', '', 'Kitchen & Dining', 'Sleeping Area', '', 'Workspace'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '22 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '1–2 persons' }
    ]
  },
  '1br': {
    type: '1br', label: 'One-Bedroom Unit', price: '₱5,000 / month', capacity: '2–3 persons',
    description: 'A comfortable one-bedroom apartment ideal for small families or couples who prefer a separate sleeping area.',
    images: ['1BR Type/1BR 1.jpg', '1BR Type/1BR 2.jpg', '1BR Type/1BR 3.jpg', '1BR Type/1BR 4.jpg'],
    imageCaptions: ['Kitchen & Living', 'Living Room', 'Sleeping Room', 'Hallway'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '35 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '2–3 persons' }
    ]
  },
  '2br': {
    type: '2br', label: 'Two-Bedroom Unit', price: '₱7,500 / month', capacity: '3–5 persons',
    description: 'A spacious two-bedroom apartment designed for growing families. Includes master bedroom, second bedroom, and full living area.',
    images: ['2BR Type/2BR 1.png', '2BR Type/2BR front.png', '1BR Type/1BR 3.jpg', '2BR Type/2BR 4.png'],
    imageCaptions: ['Kitchen', 'Living Room', 'Sleeping Room', 'Bathroom'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '50 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '3–5 persons' }
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
      position: fixed; inset: 0; z-index: 99999;
      display: flex; align-items: center; justify-content: center;
      background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
      padding: 20px; animation: rpFadeIn 0.2s ease;
    }
    @keyframes rpFadeIn { from { opacity:0; } to { opacity:1; } }
    @keyframes rpFadeOut { from { opacity:1; } to { opacity:0; } }
    
    .rp-modal {
      background: white; border-radius: 16px; width: 100%; max-width: 600px;
      max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;
      box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .rp-header {
      padding: 16px 20px; background: #0f5c3a; color: white;
      display: flex; justify-content: space-between; align-items: center;
    }
    .rp-header h3 { margin: 0; font-size: 1.1rem; }
    .rp-close { background: none; border: none; color: white; font-size: 24px; cursor: pointer; line-height: 1; }

    .rp-body { overflow-y: auto; flex: 1; }
    .rp-carousel { position: relative; background: #eee; overflow: hidden; }
    .rp-track { display: flex; transition: transform 0.3s ease; }
    .rp-slide { min-width: 100%; }
    .rp-slide img { width: 100%; height: 300px; object-fit: cover; display: block; }
    
    .rp-nav { position: absolute; top: 50%; width: 100%; display: flex; justify-content: space-between; transform: translateY(-50%); padding: 0 10px; pointer-events: none; }
    .rp-nav button { pointer-events: auto; background: rgba(255,255,255,0.8); border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-weight: bold; font-size: 18px; color: #0f5c3a; display: flex; align-items: center; justify-content: center; }

    .rp-details { padding: 20px; }
    .rp-price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .rp-price { font-size: 1.2rem; font-weight: 700; color: #0f5c3a; }
    .rp-features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px; }
    .rp-feature { background: #f4f7f5; padding: 10px; border-radius: 8px; display: flex; flex-direction: column; align-items: center; text-align: center; border: 1px solid #d9e3de; }
    .rp-feature-label { font-size: 0.7rem; color: #666; text-transform: uppercase; margin-bottom: 2px; }
    .rp-feature-value { font-weight: 700; font-size: 0.9rem; color: #1f2e2a; }

    .rp-footer { padding: 15px 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; background: #f8faf9; }
    .rp-btn { padding: 8px 18px; border-radius: 6px; cursor: pointer; font-weight: 600; font-family: inherit; }
    .rp-btn-close { background: white; border: 1px solid #ddd; color: #666; }
    .rp-btn-select { background: #0f5c3a; color: white; border: none; box-shadow: 0 4px 10px rgba(15,92,58,0.2); }
    .rp-btn-select:disabled { opacity: 0.5; cursor: not-allowed; }
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
    // Dynamic mapping for units not in ROOM_DATA (Automatic Preview)
    const mapFeatureIcon = (label) => {
      const l = label.toLowerCase();
      if (l.includes('area') || l.includes('sqm') || l.includes('size')) return '<path d="M7 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-2h-2v2H5V4h2V2zm14 8V4c0-1.1-.9-2-2-2h-6v2h6v6h2zM9 10h8v8H9v-8zm2 2v4h4v-4h-4z"/>';
      if (l.includes('capacity') || l.includes('person') || l.includes('people')) return '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>';
      return '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>';
    };

    unitType = unitData.type_key;

    // Build image URLs from the API image objects
    // DB images have {image_id, is_thumbnail, ...} — NOT file_path
    // They are served via the serve-image endpoint (passed via options.serveUrl)
    const serveUrl = options.serveUrl || '';
    let imageUrls = [];
    if (Array.isArray(unitData.images) && unitData.images.length > 0) {
      imageUrls = unitData.images.map(img => {
        if (typeof img === 'string') {
          // Already a full URL or path
          if (img.startsWith('http') || img.startsWith('/')) return img;
          return img;
        }
        if (img.image_id && serveUrl) return serveUrl + '?id=' + img.image_id;
        if (img.image_id) return '/api/apartment-types/serve-image?id=' + img.image_id;
        if (img.file_path) return img.file_path;
        return null;
      }).filter(Boolean);
    }
    // Fallback: use the thumbnail_id from the list endpoint
    if (imageUrls.length === 0 && unitData.thumbnail_id) {
      imageUrls = [serveUrl ? serveUrl + '?id=' + unitData.thumbnail_id : '/api/apartment-types/serve-image?id=' + unitData.thumbnail_id];
    }

    room = {
      label: unitData.label || 'Apartment Unit',
      price: '₱' + (Number(unitData.price) || 0).toLocaleString() + ' / month',
      description: unitData.description || 'A modern living space designed for comfort and convenience in the heart of the community.',
      images: imageUrls,
      features: [
        { icon: mapFeatureIcon('Area'), label: 'Floor Area', value: unitData.floor_area || '--' },
        { icon: mapFeatureIcon('Capacity'), label: 'Capacity', value: unitData.capacity || '--' }
      ]
    };
    if (unitData.available_count !== undefined && options.availableCount === undefined) {
      options.availableCount = unitData.available_count;
    }
  }

  if (!room) { console.error('Room Preview: Invalid unit data', unitData); return; }

  const { availableCount = 0, basePath = 'assets/', onSelect = null, selectLabel = 'Select This Unit' } = options;
  const isAvailable = availableCount > 0;

  const existing = document.getElementById('rp-overlay');
  if (existing) existing.remove();

  const slides = (room.images && room.images.length > 0)
    ? room.images.map(img => {
        let src = img;
        if (!img.startsWith('http') && !img.startsWith('/') && !img.startsWith('data:')) {
          // If it starts with uploads/, it's relative to public/
          // Otherwise, it might be relative to assets/
          if (img.startsWith('uploads/')) {
            src = basePath.replace('/assets/', '/') + img;
          } else {
            src = basePath + img;
          }
        }
        return `<div class="rp-slide"><img src="${src}" alt="Room" /></div>`;
      }).join('')
    : `<div class="rp-slide" style="height:300px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; color:#999;">No images available</div>`;

  const html = `
    <div class="rp-overlay" id="rp-overlay">
      <div class="rp-modal">
        <div class="rp-header">
          <h3>${room.label}</h3>
          <button class="rp-close" id="rp-close-x">&times;</button>
        </div>
        <div class="rp-body">
          <div class="rp-carousel">
            <div class="rp-track" id="rp-track">${slides}</div>
            ${room.images && room.images.length > 1 ? `
              <div class="rp-nav">
                <button id="rp-prev" type="button">&lt;</button>
                <button id="rp-next" type="button">&gt;</button>
              </div>
            ` : ''}
          </div>
          <div class="rp-details">
            <div class="rp-price-row">
              <span class="rp-price">${room.price}</span>
              <span style="font-size:0.8rem; color:#666; font-weight:600;">${availableCount} Units Available</span>
            </div>
            <p style="font-size:0.88rem; line-height:1.6; color:#333; margin:0 0 15px;">${room.description}</p>
            <div class="rp-features-grid">
              ${room.features.map(f => `
                <div class="rp-feature">
                  <span class="rp-feature-label">${f.label}</span>
                  <span class="rp-feature-value">${f.value}</span>
                </div>
              `).join('')}
            </div>
          </div>
        </div>
        <div class="rp-footer">
          <button class="rp-btn rp-btn-close" id="rp-close-btn" type="button">Close</button>
          ${onSelect ? `<button class="rp-btn rp-btn-select" id="rp-select" type="button" ${!isAvailable ? 'disabled' : ''}>${selectLabel}</button>` : ''}
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', html);

  const overlay = document.getElementById('rp-overlay');
  const close = () => { 
    overlay.style.animation = 'rpFadeOut 0.2s forwards'; 
    setTimeout(() => overlay.remove(), 200); 
  };
  
  document.getElementById('rp-close-x').onclick = close;
  document.getElementById('rp-close-btn').onclick = close;
  overlay.onclick = (e) => { if (e.target === overlay) close(); };

  if (room.images && room.images.length > 1) {
    let cur = 0;
    const track = document.getElementById('rp-track');
    const prev = document.getElementById('rp-prev');
    const next = document.getElementById('rp-next');
    
    prev.onclick = (e) => { e.stopPropagation(); cur = (cur - 1 + room.images.length) % room.images.length; track.style.transform = `translateX(-${cur * 100}%)`; };
    next.onclick = (e) => { e.stopPropagation(); cur = (cur + 1) % room.images.length; track.style.transform = `translateX(-${cur * 100}%)`; };
  }

  if (onSelect && isAvailable) {
    const selectBtn = document.getElementById('rp-select');
    selectBtn.onclick = (e) => { 
      e.stopPropagation();
      onSelect(unitType); 
      close(); 
    };
  }
}
window.openRoomPreview = openRoomPreview;
