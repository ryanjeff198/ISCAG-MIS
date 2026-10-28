/**
 * PhoneValidator - Client-side phone number validation and normalization.
 * Consistently validates Philippine mobile formats and international E.164 formats,
 * matching PhoneHelper.php.
 */
(function (global) {
  'use strict';

  const ERROR_MESSAGE = 'Please enter a valid phone number with the correct country code and number format.';

  const countryCodes = {
    '1': [10, 10],   // US, CA
    '7': [10, 10],   // RU, KZ
    '20': [9, 10],   // Egypt
    '27': [9, 9],    // South Africa
    '30': [10, 10],  // Greece
    '31': [9, 9],    // Netherlands
    '32': [8, 9],    // Belgium
    '33': [9, 9],    // France
    '34': [9, 9],    // Spain
    '36': [8, 9],    // Hungary
    '39': [9, 11],   // Italy
    '40': [9, 9],    // Romania
    '41': [9, 9],    // Switzerland
    '43': [4, 13],   // Austria
    '44': [9, 10],   // UK
    '45': [8, 8],    // Denmark
    '46': [7, 13],   // Sweden
    '47': [8, 8],    // Norway
    '48': [9, 9],    // Poland
    '49': [9, 12],   // Germany
    '51': [9, 9],    // Peru
    '52': [10, 10],  // Mexico
    '54': [10, 11],  // Argentina
    '55': [10, 11],  // Brazil
    '56': [9, 9],    // Chile
    '57': [10, 10],  // Colombia
    '58': [10, 10],  // Venezuela
    '60': [9, 10],   // Malaysia
    '61': [9, 9],    // Australia
    '62': [8, 12],   // Indonesia
    '63': [10, 10],  // Philippines (10 digits starting with 9)
    '64': [8, 10],   // New Zealand
    '65': [8, 8],    // Singapore
    '66': [8, 9],    // Thailand
    '81': [9, 10],   // Japan
    '82': [9, 10],   // South Korea
    '84': [9, 10],   // Vietnam
    '86': [10, 11],  // China
    '90': [10, 10],  // Turkey
    '91': [10, 10],  // India
    '92': [10, 10],  // Pakistan
    '93': [9, 9],    // Afghanistan
    '94': [9, 9],    // Sri Lanka
    '95': [7, 10],   // Myanmar
    '98': [10, 10],  // Iran
    '212': [9, 9],   // Morocco
    '213': [9, 9],   // Algeria
    '216': [8, 8],   // Tunisia
    '218': [9, 9],   // Libya
    '234': [10, 10], // Nigeria
    '254': [9, 9],   // Kenya
    '351': [9, 9],   // Portugal
    '353': [9, 9],   // Ireland
    '358': [5, 12],  // Finland
    '380': [9, 9],   // Ukraine
    '880': [10, 10], // Bangladesh
    '960': [7, 7],   // Maldives
    '961': [7, 8],   // Lebanon
    '962': [8, 9],   // Jordan
    '963': [8, 9],   // Syria
    '964': [9, 10],  // Iraq
    '965': [8, 8],   // Kuwait
    '966': [9, 9],   // Saudi Arabia
    '967': [8, 9],   // Yemen
    '968': [8, 8],   // Oman
    '971': [9, 9],   // United Arab Emirates
    '972': [8, 9],   // Israel / Palestine
    '973': [8, 8],   // Bahrain
    '974': [8, 8],   // Qatar
    '977': [8, 10]   // Nepal
  };

  const PhoneValidator = {
    ERROR_MESSAGE: ERROR_MESSAGE,

    isValid: function (phone) {
      if (typeof phone !== 'string') return false;
      const str = phone.trim();
      if (!str) return false;

      // 1. Character Check: Only digits, spaces, dashes, parentheses, dots, and optional single leading '+'
      if (!/^\+?[0-9\s\-().]+$/.test(str)) {
        return false;
      }

      // Balanced parentheses check
      const openCount = (str.match(/\(/g) || []).length;
      const closeCount = (str.match(/\)/g) || []).length;
      if (openCount !== closeCount || openCount > 1) {
        return false;
      }
      if (openCount === 1) {
        const openPos = str.indexOf('(');
        const closePos = str.indexOf(')');
        if (closePos <= openPos) {
          return false;
        }
      }

      // Clean out spaces, dashes, parentheses, dots
      let cleaned = str.replace(/[\s\-().]/g, '');

      // Normalize leading 00 to +
      if (cleaned.startsWith('00')) {
        cleaned = '+' + cleaned.substring(2);
      }

      const digitsOnly = cleaned.replace(/\+/g, '');
      const digitCount = digitsOnly.length;

      // ITU-T E.164: 7 to 15 digits
      if (digitCount < 7 || digitCount > 15) {
        return false;
      }

      // Impossible patterns: All repeating identical digits
      if (/^(\d)\1+$/.test(digitsOnly)) {
        return false;
      }

      // Impossible patterns: Trivial ascending/descending sequences
      const impossibleSequences = ['01234567890', '12345678901', '09876543210', '98765432109'];
      if (impossibleSequences.includes(digitsOnly)) {
        return false;
      }

      // 2. Philippine Formats Check
      // Local Philippine Mobile Format (09XXXXXXXXX)
      if (/^09\d{9}$/.test(cleaned)) {
        const subscriber = cleaned.substring(2);
        if (/^(\d)\1+$/.test(subscriber)) return false;
        return true;
      }

      // International Philippine Mobile with '+' (+639XXXXXXXXX)
      if (/^\+639\d{9}$/.test(cleaned)) {
        const subscriber = cleaned.substring(4);
        if (/^(\d)\1+$/.test(subscriber)) return false;
        return true;
      }

      // Philippine Mobile without '+' (639XXXXXXXXX)
      if (/^639\d{9}$/.test(cleaned)) {
        const subscriber = cleaned.substring(3);
        if (/^(\d)\1+$/.test(subscriber)) return false;
        return true;
      }

      // If the number doesn't start with '+', non-PH local numbers without country code are rejected
      if (!cleaned.startsWith('+')) {
        return false;
      }

      // 3. International Format Check (starts with '+')
      const numWithoutPlus = cleaned.substring(1);
      let matchedCode = null;
      let matchedRules = null;

      for (let len = 3; len >= 1; len--) {
        const candidate = numWithoutPlus.substring(0, len);
        if (countryCodes[candidate]) {
          matchedCode = candidate;
          matchedRules = countryCodes[candidate];
          break;
        }
      }

      if (matchedCode !== null && matchedRules !== null) {
        const nationalNumber = numWithoutPlus.substring(matchedCode.length);
        const nationalLen = nationalNumber.length;

        if (nationalLen < matchedRules[0] || nationalLen > matchedRules[1]) {
          return false;
        }

        // National number must not start with 0 in E.164
        if (nationalNumber.startsWith('0')) {
          return false;
        }

        // National subscriber digits cannot be all repeating
        if (/^(\d)\1+$/.test(nationalNumber)) {
          return false;
        }

        // Country-specific structural checks
        if (matchedCode === '1') {
          // NANP: 10 digits
          if (!/^[2-9][0-8][0-9][2-9]\d{6}$/.test(nationalNumber)) {
            return false;
          }
        } else if (matchedCode === '65') {
          // Singapore: 8 digits starting with 3, 6, 8, or 9
          if (!/^[3689]\d{7}$/.test(nationalNumber)) {
            return false;
          }
        } else if (matchedCode === '966' || matchedCode === '971') {
          // Saudi Arabia / UAE: 9 digits, mobile starts with 5
          if (!/^[1-9]\d{8}$/.test(nationalNumber)) {
            return false;
          }
        }

        return true;
      }

      // General E.164 fallback
      const generalMatch = cleaned.match(/^\+([1-9]\d{0,2})([1-9]\d{5,13})$/);
      if (generalMatch) {
        const sub = generalMatch[2];
        if (!/^(\d)\1+$/.test(sub)) {
          return true;
        }
      }

      return false;
    },

    normalize: function (phone) {
      if (typeof phone !== 'string') return '';
      const trimmed = phone.trim();
      if (!trimmed) return '';

      let cleaned = trimmed.replace(/[\s\-().]/g, '');
      if (cleaned.startsWith('00')) {
        cleaned = '+' + cleaned.substring(2);
      }

      if (/^09\d{9}$/.test(cleaned)) {
        return cleaned;
      }

      if (/^639\d{9}$/.test(cleaned)) {
        return '+' + cleaned;
      }

      return cleaned;
    }
  };

  // Export to global scope
  global.PhoneValidator = PhoneValidator;
  global.isValidPhone = PhoneValidator.isValid;
  global.normalizePhone = PhoneValidator.normalize;
  global.PHONE_ERROR_MESSAGE = ERROR_MESSAGE;
})(typeof window !== 'undefined' ? window : this);
