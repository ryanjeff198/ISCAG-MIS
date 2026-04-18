/**
 * ISCAG MIS — Shared Data Module
 * localStorage-backed data layer for the frontend prototype.
 */

const STORAGE_KEYS = {
  user: 'mis_user',
  requests: 'mis_requests',
  apartments: 'mis_apartments',
  initialized: 'mis_data_init'
};

/* ── Required profile fields (10 total) ── */
const PROFILE_FIELDS = [
  'name', 'email', 'gender', 'phone', 'address',
  'dob', 'civil', 'occupation', 'arabicName', 'membership'
];

/* ── Field display labels for missing-field lists ── */
const FIELD_LABELS = {
  name: 'Full Name',
  email: 'Email Address',
  gender: 'Gender',
  phone: 'Contact Number',
  address: 'Complete Address',
  dob: 'Date of Birth',
  civil: 'Civil Status',
  occupation: 'Occupation',
  arabicName: 'Muslim / Arabic Name',
  membership: 'Masjid Membership'
};

/* ══════════════════════════════════════════
   Default seed data
   ══════════════════════════════════════════ */

const DEFAULT_USER = {
  id: 'USR-001',
  name: 'Muhammad Usman',
  email: 'musman@example.com',
  gender: '',
  phone: '',
  address: '',
  dob: '',
  civil: '',
  occupation: '',
  arabicName: '',
  membership: '',
  revertYear: '',
  apartment: '',
  profileComplete: false
};

const DEFAULT_APARTMENTS = [
  { id: 'APT-A1', name: 'Unit A-1 · Studio', price: 3500, available: 2, status: 'available' },
  { id: 'APT-A2', name: 'Unit A-2 · 1-Bedroom', price: 5000, available: 1, status: 'available' },
  { id: 'APT-B1', name: 'Unit B-1 · 2-Bedroom', price: 7500, available: 0, status: 'occupied' },
  { id: 'APT-B2', name: 'Unit B-2 · 2-Bedroom', price: 7500, available: 1, status: 'available' },
  { id: 'APT-C1', name: 'Unit C-1 · Family Suite', price: 10000, available: 0, status: 'reserved' }
];

const DEFAULT_REQUESTS = [
  { id: 'BUR-001', user: 'USR-001', type: 'burial_service', status: 'pending', date: '2026-03-15', updatedAt: '2026-03-15' },
  { id: 'APT-001', user: 'USR-001', type: 'apartment_application', status: 'approved', date: '2026-03-09', updatedAt: '2026-03-12' }
];

/* ══════════════════════════════════════════
   Public API
   ══════════════════════════════════════════ */

/** Seed localStorage with defaults on first visit. Safe to call multiple times. */
export function initData() {
  if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
    localStorage.setItem(STORAGE_KEYS.apartments, JSON.stringify(DEFAULT_APARTMENTS));
    localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(DEFAULT_REQUESTS));
    localStorage.setItem(STORAGE_KEYS.initialized, '1');
  }
}

/** Return the current user object. */
export function getUser() {
  const raw = localStorage.getItem(STORAGE_KEYS.user);
  return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
}

/**
 * Merge partial updates into the stored user and recalculate profileComplete.
 * @param {Object} data — key/value pairs to merge
 * @returns {Object} the updated user
 */
export function updateUser(data) {
  const user = getUser();
  Object.assign(user, data);

  // Recalculate profile completion
  const filled = PROFILE_FIELDS.filter(k => user[k] && String(user[k]).trim() !== '');
  user.profileComplete = filled.length === PROFILE_FIELDS.length;

  localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));
  return user;
}

/** Return all service requests. */
export function getRequests() {
  const raw = localStorage.getItem(STORAGE_KEYS.requests);
  return raw ? JSON.parse(raw) : [];
}

/**
 * Add a new service request.
 * @param {Object} req — request object (auto-generates id if missing)
 */
export function addRequest(req) {
  const requests = getRequests();
  if (!req.id) req.id = 'REQ-' + String(requests.length + 1).padStart(3, '0');
  if (!req.date) req.date = new Date().toISOString().split('T')[0];
  if (!req.updatedAt) req.updatedAt = req.date;
  if (!req.status) req.status = 'pending';
  requests.push(req);
  localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(requests));
  return req;
}

/** Return all apartment units. */
export function getApartments() {
  const raw = localStorage.getItem(STORAGE_KEYS.apartments);
  return raw ? JSON.parse(raw) : [];
}

/**
 * Profile completion helper.
 * @returns {{ percentage: number, filled: number, total: number, missingFields: string[] }}
 */
export function getProfileCompletion() {
  const user = getUser();
  const missing = [];
  let filled = 0;

  PROFILE_FIELDS.forEach(k => {
    if (user[k] && String(user[k]).trim() !== '') {
      filled++;
    } else {
      missing.push(FIELD_LABELS[k] || k);
    }
  });

  return {
    percentage: Math.round((filled / PROFILE_FIELDS.length) * 100),
    filled,
    total: PROFILE_FIELDS.length,
    missingFields: missing
  };
}
