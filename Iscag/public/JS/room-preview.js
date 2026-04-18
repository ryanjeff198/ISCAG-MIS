/**
 * ISCAG MIS — Room Preview Module
 * Self-contained modal with image carousel, room details, and availability status.
 * Usage: <script src="path/to/room-preview.js"></script>
 *        openRoomPreview('studio', { availableCount: 2, basePath: 'assets/room-images/', onSelect: fn })
 */

/* ══════════════════════════════════════════
   Room Data
   ══════════════════════════════════════════ */

const ROOM_DATA = {
  studio: {
    type: 'studio',
    label: 'Studio Unit',
    price: '₱3,500 / month',
    capacity: '1–2 persons',
    description: 'A compact and efficient living space perfect for individuals or couples. The studio unit features an open-plan layout combining sleeping, living, and dining areas in one well-designed space, with a separate bathroom and a functional kitchenette.',
    images: ['studio-1.png', 'studio-2.png'],
    imageCaptions: ['Living & Sleeping Area', 'Kitchenette'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '22 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v10h22v-6c0-2.21-1.79-4-4-4z"/></svg>', label: 'Bedrooms', value: 'Open-plan' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M21 14v-4c0-.55-.45-1-1-1h-1V6c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2v3H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1h1v5h2v-1h10v1h2v-5h1c.55 0 1-.45 1-1zM7 6h10v3H7V6z"/></svg>', label: 'Bathroom', value: '1 (with shower)' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18 2.01L6 2c-1.1 0-2 .89-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.11-.9-1.99-2-1.99zM18 20H6v-9.02h12V20zm0-11H6V4h12v5z"/></svg>', label: 'Kitchen', value: 'Kitchenette' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>', label: 'Parking', value: 'Shared lot' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '1–2 persons' }
    ]
  },
  '1br': {
    type: '1br',
    label: 'One-Bedroom Unit',
    price: '₱5,000 / month',
    capacity: 'Small families (2–3 persons)',
    description: 'A comfortable one-bedroom apartment ideal for small families or couples who prefer a separate sleeping area. Features a distinct living room, a private bedroom, a full bathroom, and a dining-kitchen area with ample counter space.',
    images: ['1br-1.png', '1br-2.png'],
    imageCaptions: ['Bedroom', 'Living & Dining Area'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '35 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v10h22v-6c0-2.21-1.79-4-4-4z"/></svg>', label: 'Bedrooms', value: '1 (separate)' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M21 14v-4c0-.55-.45-1-1-1h-1V6c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2v3H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1h1v5h2v-1h10v1h2v-5h1c.55 0 1-.45 1-1zM7 6h10v3H7V6z"/></svg>', label: 'Bathroom', value: '1 (with shower)' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18 2.01L6 2c-1.1 0-2 .89-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.11-.9-1.99-2-1.99zM18 20H6v-9.02h12V20zm0-11H6V4h12v5z"/></svg>', label: 'Kitchen', value: 'Full kitchen' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>', label: 'Parking', value: 'Shared lot' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>', label: 'Capacity', value: '2–3 persons' }
    ]
  },
  '2br': {
    type: '2br',
    label: 'Two-Bedroom Unit',
    price: '₱7,500 / month',
    capacity: 'Larger families (3–5 persons)',
    description: 'A spacious two-bedroom apartment designed for growing families. Includes a master bedroom, a second bedroom, a full living and dining area, a complete kitchen, and a bathroom. Ideal for families seeking comfort and privacy within the community housing complex.',
    images: ['2br-1.png', '2br-2.png'],
    imageCaptions: ['Living & Dining Area', 'Master Bedroom'],
    features: [
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>', label: 'Floor Area', value: '50 sqm' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H3V7H1v10h22v-6c0-2.21-1.79-4-4-4z"/></svg>', label: 'Bedrooms', value: '2 (separate)' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M21 14v-4c0-.55-.45-1-1-1h-1V6c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2v3H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1h1v5h2v-1h10v1h2v-5h1c.55 0 1-.45 1-1zM7 6h10v3H7V6z"/></svg>', label: 'Bathroom', value: '1 (with shower & tub)' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18 2.01L6 2c-1.1 0-2 .89-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.11-.9-1.99-2-1.99zM18 20H6v-9.02h12V20zm0-11H6V4h12v5z"/></svg>', label: 'Kitchen', value: 'Full kitchen' },
      { icon: '<svg viewBox="0 0 24 24" fill="currentColor" style="width:22px;height:22px;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>', label: 'Parking', value: 'Dedicated slot' },
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
    /* ───── OVERLAY ───── */
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

    /* ───── MODAL ───── */
    .rp-modal {
      background: white;
      border-radius: 14px;
      width: 100%; max-width: 600px;
      max-height: 90vh;
      overflow: hidden;
      box-shadow: 0 24px 64px rgba(0,0,0,0.28);
      animation: rpSlideUp 0.3s ease;
      display: flex; flex-direction: column;
    }

    /* ───── HEADER ───── */
    .rp-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 22px;
      border-bottom: 1px solid var(--border, #d9e3de);
      background: linear-gradient(135deg, var(--primary-dark, #0f5c3a) 0%, var(--primary-light, #2f8a60) 100%);
      position: relative;
    }
    .rp-header::before {
      content: '';
      position: absolute; right: -10px; bottom: -10px;
      width: 80px; height: 80px; border-radius: 50%;
      background: rgba(201,168,76,0.12);
    }
    .rp-header-left { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
    .rp-header-left h3 {
      font-family: 'Lora', serif;
      font-size: 1.08rem; font-weight: 700;
      color: white; margin: 0;
    }
    .rp-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 10px; border-radius: 20px;
      font-size: 0.7rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.04em;
    }
    .rp-badge.available { background: rgba(47,138,96,0.2); color: #b8f0d0; }
    .rp-badge.unavailable { background: rgba(139,46,46,0.25); color: #f0c4c4; }
    .rp-badge-dot {
      width: 7px; height: 7px; border-radius: 50%;
    }
    .rp-badge.available .rp-badge-dot { background: #5effa0; }
    .rp-badge.unavailable .rp-badge-dot { background: #ff6b6b; }
    .rp-close {
      background: rgba(255,255,255,0.15); border: none; cursor: pointer;
      width: 32px; height: 32px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      transition: background 0.18s;
      position: relative; z-index: 1;
    }
    .rp-close:hover { background: rgba(255,255,255,0.3); }
    .rp-close svg { width: 16px; height: 16px; fill: white; }

    /* ───── BODY ───── */
    .rp-body {
      overflow-y: auto; flex: 1;
      scrollbar-width: thin;
      scrollbar-color: var(--border, #d9e3de) transparent;
    }
    .rp-body::-webkit-scrollbar { width: 5px; }
    .rp-body::-webkit-scrollbar-track { background: transparent; }
    .rp-body::-webkit-scrollbar-thumb { background: var(--border, #d9e3de); border-radius: 3px; }

    /* ───── CAROUSEL ───── */
    .rp-carousel { position: relative; background: #f0f2f1; overflow: hidden; }
    .rp-carousel-track { display: flex; transition: transform 0.4s cubic-bezier(0.25,0.8,0.25,1); }
    .rp-carousel-slide {
      min-width: 100%; position: relative;
    }
    .rp-carousel-slide img {
      width: 100%; height: 280px;
      object-fit: cover; display: block;
    }
    .rp-slide-caption {
      position: absolute; bottom: 0; left: 0; right: 0;
      padding: 8px 16px;
      background: linear-gradient(to top, rgba(0,0,0,0.55), transparent);
      color: white; font-size: 0.78rem; font-weight: 600;
      text-align: center;
    }
    .rp-carousel-btn {
      position: absolute; top: 50%; transform: translateY(-50%);
      width: 36px; height: 36px; border-radius: 50%;
      background: rgba(255,255,255,0.9); border: none;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      transition: all 0.18s; z-index: 2;
    }
    .rp-carousel-btn:hover { background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .rp-carousel-btn svg { width: 16px; height: 16px; fill: var(--primary-dark, #0f5c3a); }
    .rp-carousel-btn.prev { left: 12px; }
    .rp-carousel-btn.next { right: 12px; }
    .rp-carousel-dots {
      position: absolute; bottom: 36px; left: 50%; transform: translateX(-50%);
      display: flex; gap: 7px; z-index: 2;
    }
    .rp-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: rgba(255,255,255,0.5); border: none; cursor: pointer;
      transition: all 0.2s; padding: 0;
    }
    .rp-dot.active { background: white; transform: scale(1.3); }

    /* ───── DETAILS ───── */
    .rp-details { padding: 22px; }
    .rp-price-row {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 14px;
    }
    .rp-price {
      font-family: 'Lora', serif;
      font-size: 1.2rem; font-weight: 700;
      color: var(--primary-dark, #0f5c3a);
    }
    .rp-capacity {
      font-size: 0.78rem; color: var(--text-muted, #6f7f78);
      display: flex; align-items: center; gap: 5px;
    }
    .rp-desc {
      font-size: 0.87rem; color: var(--text-main, #1f2e2a);
      line-height: 1.65; margin-bottom: 18px;
    }
    .rp-features-title {
      font-family: 'Lora', serif;
      font-size: 0.85rem; font-weight: 700;
      color: var(--primary-dark, #0f5c3a);
      margin-bottom: 10px;
      padding-bottom: 8px;
      border-bottom: 2px solid rgba(23,107,69,0.12);
    }
    .rp-features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
    }
    .rp-feature {
      padding: 10px;
      background: var(--content-bg, #f4f6f5);
      border-radius: 8px;
      text-align: center;
      border: 1px solid var(--border, #d9e3de);
    }
    .rp-feature-icon { display:flex; align-items:center; justify-content:center; margin-bottom: 4px; color: var(--primary, #176b45); }
    .rp-feature-label {
      font-size: 0.68rem; font-weight: 600;
      color: var(--text-muted, #6f7f78);
      text-transform: uppercase; letter-spacing: 0.05em;
      margin-bottom: 2px;
    }
    .rp-feature-value {
      font-size: 0.82rem; font-weight: 700;
      color: var(--text-main, #1f2e2a);
    }

    /* ───── FOOTER ───── */
    .rp-footer {
      padding: 14px 22px;
      border-top: 1px solid var(--border, #d9e3de);
      display: flex; gap: 10px; justify-content: flex-end;
      background: rgba(244,246,245,0.5);
    }
    .rp-btn-close-footer {
      padding: 9px 20px; border-radius: 8px;
      border: 1.5px solid var(--border, #d9e3de);
      background: white; color: var(--text-muted, #6f7f78);
      font-size: 0.85rem; font-weight: 600;
      cursor: pointer; transition: all 0.18s;
      font-family: 'Source Sans 3', sans-serif;
    }
    .rp-btn-close-footer:hover {
      border-color: var(--primary, #176b45);
      color: var(--primary, #176b45);
    }
    .rp-btn-select {
      padding: 9px 20px; border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, var(--primary-dark, #0f5c3a), var(--primary-light, #2f8a60));
      color: white; font-size: 0.85rem; font-weight: 700;
      cursor: pointer; transition: all 0.18s;
      box-shadow: 0 4px 12px rgba(23,107,69,0.25);
      font-family: 'Source Sans 3', sans-serif;
    }
    .rp-btn-select:hover:not(:disabled) {
      box-shadow: 0 6px 20px rgba(23,107,69,0.35);
      transform: translateY(-1px);
    }
    .rp-btn-select:disabled {
      opacity: 0.5; cursor: not-allowed;
      box-shadow: none; transform: none;
    }

    .rp-view-btn { display: none; }
    .rp-view-btn svg { width: 13px; height: 13px; fill: currentColor; }

    /* ───── RESPONSIVE ───── */
    @media (max-width: 640px) {
      .rp-modal { max-width: 100%; margin: 10px; }
      .rp-carousel-slide img { height: 200px; }
      .rp-features-grid { grid-template-columns: repeat(2, 1fr); }
    }
  `;
  document.head.appendChild(style);
})();


/* ══════════════════════════════════════════
   Public API
   ══════════════════════════════════════════ */

/**
 * Open the Room Preview modal.
 * @param {string} unitType — 'studio', '1br', or '2br'
 * @param {Object} options
 * @param {number}   options.availableCount — number of available units (0 = Not Available)
 * @param {string}   options.basePath       — relative path to room images folder (default: 'assets/room-images/')
 * @param {Function} [options.onSelect]     — callback when "Select" is clicked, receives unitType
 * @param {string}   [options.selectLabel]  — custom label for the select button (default: 'Select This Unit')
 */
function openRoomPreview(unitType, options = {}) {
  const room = ROOM_DATA[unitType];
  if (!room) { console.error('Room Preview: Unknown unit type "' + unitType + '"'); return; }

  const {
    availableCount = 0,
    basePath = 'assets/room-images/',
    onSelect = null,
    selectLabel = 'Select This Unit'
  } = options;

  const isAvailable = availableCount > 0;

  // Remove any existing modal
  const existing = document.getElementById('room-preview-overlay');
  if (existing) existing.remove();

  // Build image slides
  const slides = room.images.map((img, i) => `
    <div class="rp-carousel-slide">
      <img src="${basePath}${img}" alt="${room.label} — ${room.imageCaptions[i] || 'View ' + (i+1)}" loading="lazy" />
      <div class="rp-slide-caption">${room.imageCaptions[i] || ''}</div>
    </div>
  `).join('');

  const dots = room.images.map((_, i) => `
    <button class="rp-dot ${i === 0 ? 'active' : ''}" data-index="${i}" aria-label="Slide ${i+1}"></button>
  `).join('');

  // Build features grid
  const features = room.features.map(f => `
    <div class="rp-feature">
      <div class="rp-feature-icon">${f.icon}</div>
      <div class="rp-feature-label">${f.label}</div>
      <div class="rp-feature-value">${f.value}</div>
    </div>
  `).join('');

  // Badge
  const badge = isAvailable
    ? `<span class="rp-badge available"><span class="rp-badge-dot"></span>${availableCount} Available</span>`
    : `<span class="rp-badge unavailable"><span class="rp-badge-dot"></span>Not Available</span>`;

  // Select button
  const selectBtn = onSelect
    ? `<button class="rp-btn-select" id="rp-select-btn" ${!isAvailable ? 'disabled' : ''}>${selectLabel}</button>`
    : '';

  // Build modal HTML
  const html = `
    <div class="rp-overlay" id="room-preview-overlay">
      <div class="rp-modal" id="room-preview-modal">
        <div class="rp-header">
          <div class="rp-header-left">
            <h3>${room.label}</h3>
            ${badge}
          </div>
          <button class="rp-close" id="rp-close-btn" title="Close preview">
            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="rp-body">
          <div class="rp-carousel" id="rp-carousel">
            <div class="rp-carousel-track" id="rp-track">${slides}</div>
            <button class="rp-carousel-btn prev" id="rp-prev" aria-label="Previous image">
              <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button class="rp-carousel-btn next" id="rp-next" aria-label="Next image">
              <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>
            <div class="rp-carousel-dots" id="rp-dots">${dots}</div>
          </div>
          <div class="rp-details">
            <div class="rp-price-row">
              <span class="rp-price">${room.price}</span>
              <span class="rp-capacity"><svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> ${room.capacity}</span>
            </div>
            <p class="rp-desc">${room.description}</p>
            <div class="rp-features-title">Key Features</div>
            <div class="rp-features-grid">${features}</div>
          </div>
        </div>
        <div class="rp-footer">
          <button class="rp-btn-close-footer" id="rp-close-footer">Close</button>
          ${selectBtn}
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', html);

  // ── Carousel logic ──
  let currentSlide = 0;
  const totalSlides = room.images.length;
  const track = document.getElementById('rp-track');
  const dotsContainer = document.getElementById('rp-dots');

  function goToSlide(index) {
    if (index < 0) index = totalSlides - 1;
    if (index >= totalSlides) index = 0;
    currentSlide = index;
    track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
    dotsContainer.querySelectorAll('.rp-dot').forEach((d, i) => {
      d.classList.toggle('active', i === currentSlide);
    });
  }

  document.getElementById('rp-prev').addEventListener('click', (e) => { e.stopPropagation(); goToSlide(currentSlide - 1); });
  document.getElementById('rp-next').addEventListener('click', (e) => { e.stopPropagation(); goToSlide(currentSlide + 1); });
  dotsContainer.querySelectorAll('.rp-dot').forEach(dot => {
    dot.addEventListener('click', (e) => { e.stopPropagation(); goToSlide(parseInt(dot.dataset.index)); });
  });

  // ── Close logic ──
  const overlay = document.getElementById('room-preview-overlay');

  function closeModal() {
    overlay.style.animation = 'rpFadeOut 0.2s ease forwards';
    setTimeout(() => overlay.remove(), 200);
  }

  document.getElementById('rp-close-btn').addEventListener('click', closeModal);
  document.getElementById('rp-close-footer').addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

  // Keyboard
  function onKeyDown(e) {
    if (e.key === 'Escape') { closeModal(); document.removeEventListener('keydown', onKeyDown); }
    if (e.key === 'ArrowLeft') goToSlide(currentSlide - 1);
    if (e.key === 'ArrowRight') goToSlide(currentSlide + 1);
  }
  document.addEventListener('keydown', onKeyDown);

  // ── Select callback ──
  if (onSelect && isAvailable) {
    document.getElementById('rp-select-btn').addEventListener('click', () => {
      onSelect(unitType);
      closeModal();
    });
  }
}
