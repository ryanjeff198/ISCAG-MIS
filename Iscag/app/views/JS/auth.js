/**
 * ISCAG MIS — Auth & Access Control Module
 * Profile-gated department access with gender-based routing.
 */

import { getUser, getProfileCompletion } from './data.js';

/* ══════════════════════════════════════════
   Sidebar / Nav helpers
   ══════════════════════════════════════════ */

/** Populate sidebar user info from stored data + sync avatar photo. */
export function loadUserNav() {
  const user = getUser();
  const navName = document.getElementById('nav-name');
  const navAvatar = document.getElementById('nav-avatar');

  if (navName) navName.textContent = user.name;
  if (navAvatar) {
    const photo = localStorage.getItem('mis_user_photo');
    if (photo) {
      navAvatar.textContent = '';
      navAvatar.style.backgroundImage = 'url(' + photo + ')';
      navAvatar.style.backgroundSize = 'cover';
      navAvatar.style.backgroundPosition = 'center';
    } else {
      navAvatar.textContent = user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
    }
  }
}

/* ══════════════════════════════════════════
   Department access logic
   ══════════════════════════════════════════ */

/**
 * Returns list of departments the user can access.
 * @returns {{ id: string, name: string, allowed: boolean }[]}
 */
export function getDepartmentAccess() {
  const user = getUser();
  const { percentage } = getProfileCompletion();

  const departments = [
    { id: 'apartment', name: 'Apartment Department', allowed: percentage === 100 },
    { id: 'damayan', name: 'Damayan Department (Burial Services)', allowed: percentage === 100 },
  ];

  if (user.gender === 'male') {
    departments.push({ id: 'dawah-male', name: "Da'wah Department (Male)", allowed: percentage === 100 });
  } else if (user.gender === 'female') {
    departments.push({ id: 'dawah-female', name: "Da'wah Department (Female)", allowed: percentage === 100 });
  } else {
    // Gender not set — show both as locked
    departments.push({ id: 'dawah-male', name: "Da'wah Department (Male)", allowed: false });
    departments.push({ id: 'dawah-female', name: "Da'wah Department (Female)", allowed: false });
  }

  return departments;
}

/* ══════════════════════════════════════════
   Access gate — call on protected pages
   ══════════════════════════════════════════ */

/**
 * Check if the current user's profile is complete.
 * If not, show the "Profile Incomplete" modal.
 * @param {Object} [opts]
 * @param {string} [opts.redirectUrl] — URL to redirect on "Go to Profile"
 * @param {string} [opts.requiredGender] — if set, also checks gender match
 * @returns {boolean} true if access is granted
 */
export function checkProfileAccess(opts = {}) {
  const { redirectUrl = '../user_profile.html', requiredGender = null } = opts;
  const user = getUser();
  const { percentage, missingFields } = getProfileCompletion();

  // Profile incomplete
  if (percentage < 100) {
    showAccessModal({
      type: 'incomplete',
      percentage,
      missingFields,
      redirectUrl
    });
    return false;
  }

  // Gender gate
  if (requiredGender && user.gender !== requiredGender) {
    showAccessModal({
      type: 'unauthorized',
      redirectUrl: opts.departmentUrl || '../user-dashboard.html'
    });
    return false;
  }

  return true;
}

/* ══════════════════════════════════════════
   Modal system
   ══════════════════════════════════════════ */

/**
 * Inject and display the access control modal.
 * @param {Object} config
 * @param {'incomplete'|'unauthorized'|'restricted'} config.type
 * @param {number} [config.percentage]
 * @param {string[]} [config.missingFields]
 * @param {string} [config.redirectUrl]
 */
export function showAccessModal(config) {
  // Remove existing modal if any
  const existing = document.getElementById('access-control-modal');
  if (existing) existing.remove();

  const { type, percentage = 0, missingFields = [], redirectUrl = '../user_profile.html' } = config;

  let icon, title, message, missingHtml, primaryBtn, primaryAction;

  if (type === 'incomplete') {
    icon = `<svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/></svg>`;
    title = 'Profile Incomplete';
    message = 'Access to this section is restricted until your profile is fully completed. Please update the required information to continue.';
    missingHtml = missingFields.length > 0
      ? `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">The following information is still required:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => `<li>${f}</li>`).join('')}
           </ul>
         </div>`
      : '';
    primaryBtn = 'Go to Profile';
    primaryAction = () => { window.location.href = redirectUrl; };
  } else if (type === 'unauthorized') {
    icon = `<svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#8b2e2e;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 5h2v6h-2V7zm0 8h2v2h-2v-2z"/></svg>`;
    title = 'Unauthorized Access';
    message = 'This department is not available based on your profile information.';
    missingHtml = '';
    primaryBtn = 'Return to Departments';
    primaryAction = () => { window.location.href = redirectUrl; };
  } else {
    // type === 'restricted' — clicking locked card on dashboard
    icon = `<svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1s3.1 1.39 3.1 3.1v2z"/></svg>`;
    title = 'Access Restricted';
    message = `You currently cannot access this department. Your profile is <strong>${percentage}%</strong> complete. Kindly complete your profile to unlock all available sections.`;
    missingHtml = missingFields.length > 0
      ? `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">Required information:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => `<li>${f}</li>`).join('')}
           </ul>
         </div>`
      : '';
    primaryBtn = 'Go to Profile';
    primaryAction = () => { window.location.href = redirectUrl; };
  }

  // Progress ring (only for incomplete / restricted)
  const showProgress = type !== 'unauthorized';
  const progressHtml = showProgress
    ? `<div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
         <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
           <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
           <circle cx="18" cy="18" r="15.9" fill="none" stroke="${percentage >= 70 ? '#c79a2b' : percentage >= 40 ? '#c79a2b' : '#8b2e2e'}" stroke-width="3"
             stroke-dasharray="${percentage} ${100 - percentage}" stroke-linecap="round"
             style="transition:stroke-dasharray 0.8s ease;"/>
         </svg>
         <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#0f5c3a;">${percentage}%</span>
       </div>`
    : '';

  const modalHtml = `
    <div id="access-control-modal" style="
      position:fixed;inset:0;z-index:99999;
      display:flex;align-items:center;justify-content:center;
      background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);
      padding:24px;
      animation:acmFadeIn 0.3s ease;
    ">
      <div style="
        background:white;border-radius:16px;
        width:100%;max-width:440px;
        box-shadow:0 20px 60px rgba(0,0,0,0.25);
        overflow:hidden;
        animation:acmSlideUp 0.35s ease;
      ">
        <!-- Header accent bar -->
        <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>

        <div style="padding:32px 28px 24px;text-align:center;">
          <!-- Icon -->
          <div style="margin-bottom:8px;">${icon}</div>

          <!-- Progress ring -->
          ${progressHtml}

          <!-- Title -->
          <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#0f5c3a;margin:0 0 10px;">${title}</h4>

          <!-- Message -->
          <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">${message}</p>

          <!-- Missing fields -->
          ${missingHtml}
        </div>

        <!-- Actions -->
        <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
          <button id="acm-cancel-btn" style="
            padding:10px 22px;border-radius:8px;
            border:1.5px solid #d9e3de;background:white;
            color:#6f7f78;font-size:0.85rem;font-weight:600;
            cursor:pointer;transition:all 0.18s;
          ">Cancel</button>
          <button id="acm-primary-btn" style="
            padding:10px 22px;border-radius:8px;
            border:none;
            background:linear-gradient(135deg,#0f5c3a,#2f8a60);
            color:white;font-size:0.85rem;font-weight:700;
            cursor:pointer;transition:all 0.18s;
            box-shadow:0 4px 12px rgba(15,92,58,0.3);
          ">${primaryBtn}</button>
        </div>
      </div>
    </div>
  `;

  // Inject animation keyframes if not already present
  if (!document.getElementById('acm-keyframes')) {
    const styleEl = document.createElement('style');
    styleEl.id = 'acm-keyframes';
    styleEl.textContent = `
      @keyframes acmFadeIn { from { opacity:0; } to { opacity:1; } }
      @keyframes acmSlideUp { from { opacity:0;transform:translateY(24px) scale(0.96); } to { opacity:1;transform:translateY(0) scale(1); } }
    `;
    document.head.appendChild(styleEl);
  }

  // Inject modal
  document.body.insertAdjacentHTML('beforeend', modalHtml);

  const modal = document.getElementById('access-control-modal');
  const cancelBtn = document.getElementById('acm-cancel-btn');
  const primaryBtnEl = document.getElementById('acm-primary-btn');

  // Hover effects
  primaryBtnEl.onmouseenter = () => { primaryBtnEl.style.boxShadow = '0 6px 20px rgba(15,92,58,0.4)'; primaryBtnEl.style.transform = 'translateY(-1px)'; };
  primaryBtnEl.onmouseleave = () => { primaryBtnEl.style.boxShadow = '0 4px 12px rgba(15,92,58,0.3)'; primaryBtnEl.style.transform = 'none'; };
  cancelBtn.onmouseenter = () => { cancelBtn.style.borderColor = '#b0bcc8'; cancelBtn.style.color = '#1f2e2a'; };
  cancelBtn.onmouseleave = () => { cancelBtn.style.borderColor = '#d9e3de'; cancelBtn.style.color = '#6f7f78'; };

  // Event handlers
  primaryBtnEl.addEventListener('click', primaryAction);
  cancelBtn.addEventListener('click', () => dismissModal(modal));
  modal.addEventListener('click', e => { if (e.target === modal) dismissModal(modal); });
}

function dismissModal(modal) {
  modal.style.animation = 'acmFadeIn 0.2s ease reverse forwards';
  setTimeout(() => modal.remove(), 200);
}

/* ══════════════════════════════════════════
   Toast notification
   ══════════════════════════════════════════ */

/**
 * Show a toast message.
 * @param {string} msg
 * @param {string} [bg='#2f8a60']
 * @param {number} [duration=3500]
 */
export function showToast(msg, bg = '#2f8a60', duration = 3500) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = `
    position:fixed;top:24px;right:24px;z-index:99999;
    background:${bg};color:white;
    padding:14px 24px;border-radius:10px;
    font-family:'Source Sans 3',sans-serif;font-size:0.9rem;font-weight:600;
    box-shadow:0 4px 16px rgba(0,0,0,0.18);
    animation:acmSlideUp 0.3s ease;
    max-width:400px;
  `;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}
