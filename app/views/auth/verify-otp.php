<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Verify OTP</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    /* ─── VARIABLES ─── */
:root {
  --green: #1c6b3a;
  --green-dark: #134d28;
  --gray-bg: #f5f5f5;
  --border: #e2e2e2;
  --txt: #111;
  --txt-2: #555;
  --txt-3: #888;
  --danger: #dc3545;
}

/* ─── RESET ─── */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', sans-serif;
  background: #fff;
  color: var(--txt);
}

/* ─── SPLIT LAYOUT ─── */
.otp-page {
  display: flex;
  justify-content: center; 
  align-items: center;    
  min-height: 100vh;       
  background: #f5f5f5;     
  padding: 20px;           
}

.otp-page .auth-left {
  flex: 1;
  background: var(--green-dark);
}

.otp-page .auth-left img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.otp-page .auth-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-bg);
  padding: 40px;
}

/* ─── OTP CARD ─── */
.otp-card {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.08);
  text-align: center;
}

/* ─── HEADER ─── */
.otp-header {
  margin-bottom: 24px;
}

.otp-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
}

.otp-subtitle {
  font-size: 14px;
  color: var(--txt-2);
  line-height: 1.6;
}

.otp-email {
  font-weight: 600;
  color: var(--green);
}

/* ─── OTP INPUTS ─── */
.otp-inputs {
  display: flex;
  justify-content: space-between;
  margin: 16px 0;
}

.otp-box {
  width: 48px;
  height: 48px;
  text-align: center;
  font-size: 20px;
  font-weight: 600;
  border: 1px solid var(--border);
  border-radius: 6px;
  transition: 0.2s;
}

.otp-box:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(28,107,58,0.1);
  outline: none;
}

.otp-box.error {
  border-color: var(--danger);
}

/* ─── ERROR MESSAGE ─── */
.otp-error {
  font-size: 13px;
  color: var(--danger);
  margin-bottom: 12px;
  display: none;
  text-align: center;
}

.otp-error.show {
  display: block;
}

/* ─── TIMER & RESEND ─── */
.otp-timer-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  font-size: 14px;
}

.otp-timer strong {
  color: var(--green);
}

.otp-timer.expired {
  color: var(--danger);
}

.otp-resend {
  padding: 6px 12px;
  font-size: 13px;
  border: none;
  background: var(--green);
  color: #fff;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s;
}

.otp-resend:disabled {
  background: var(--txt-3);
  cursor: not-allowed;
}

.otp-resend:hover:not(:disabled) {
  background: var(--green-dark);
}

/* ─── BUTTON ─── */
.btn-auth {
  width: 100%;
  padding: 12px;
  background: var(--green);
  color: #fff;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-auth:hover {
  background: var(--green-dark);
}

.btn-auth:disabled {
  background: var(--txt-3);
  cursor: not-allowed;
}

/* SPINNER */
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #fff;
  border-top: 2px solid transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* BACK LINK */
.otp-back {
  display: inline-block;
  margin-top: 18px;
  font-size: 14px;
  color: var(--green);
  text-decoration: none;
}

.otp-back:hover {
  text-decoration: underline;
}

/* ─── RESPONSIVE ─── */
@media (max-width: 768px) {
  .otp-page {
    flex-direction: column;
  }

  .auth-left {
    height: 200px;
  }

  .auth-right {
    padding: 20px;
  }

  .otp-inputs {
    gap: 8px;
  }
}
  </style>
</head>
<body>

  <div id="header-placeholder"></div>

  <div class="otp-page">
    <div class="otp-card">

      <div class="otp-header">
        <h1 class="otp-title">Verify OTP</h1>
        <p class="otp-subtitle">
          Enter the 6-digit code sent to<br>
          <span class="otp-email" id="emailDisplay"></span>
        </p>
      </div>

      <form id="otpForm" action="<?= url('/verify-otp') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
        <input type="hidden" name="otp_full" id="otpFull" value="">
        <?php if (isset($success)): ?>
          <div class="alert alert-success" style="font-size: 13px; margin-bottom: 15px; color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 6px; text-align: center;"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
          <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 10px; border-radius: 6px; text-align: center;"><?= $error ?></div>
        <?php endif; ?>
        <div class="otp-inputs">
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="0" required autofocus>
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="1" required>
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="2" required>
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="3" required>
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="4" required>
          <input type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" name="otp[]" class="otp-box" maxlength="1" data-index="5" required>
        </div>

        <div class="otp-error" id="otpError"></div>

        <div class="otp-timer-row">
          <span class="otp-timer" id="timer">Time remaining: <strong id="timeLeft">10:00</strong></span>
          <button type="button" class="otp-resend" id="resendBtn" disabled>Resend OTP</button>
        </div>

        <button type="submit" class="btn-auth" id="verifyBtn">
          <span id="btnText">Verify OTP</span>
          <span id="btnSpinner" class="spinner" style="display:none;"></span>
        </button>
      </form>

      <a href="<?= url('/register') ?>" class="otp-back">
        <i class="bi bi-arrow-left"></i> Back to Email
      </a>

    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const userEmail = "<?= $_SESSION['temp_email'] ?? '' ?>";
    const expiryTime = "<?= $_SESSION['otp_expiry'] ?? '0' ?>";

    <?php $isReset = isset($_SESSION['reset_mode']) && $_SESSION['reset_mode']; ?>
    const backUrl = '<?= $isReset ? url('/forgot-password') : url('/change-registration-email') ?>';
    const backText = 'Back to Email';

    if (!userEmail) {
      alert('Session expired or invalid. Please start over.');
      window.location.href = backUrl;
      return;
    }

    // Update back link dynamically
    const backBtn = document.querySelector('.otp-back');
    if (backBtn) {
      backBtn.href = backUrl;
      backBtn.innerHTML = `<i class="bi bi-arrow-left"></i> ${backText}`;
    }

    document.getElementById('emailDisplay').textContent = userEmail;

    const form       = document.getElementById('otpForm');
    const otpBoxes   = document.querySelectorAll('.otp-box');
    const verifyBtn  = document.getElementById('verifyBtn');
    const btnText    = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const resendBtn  = document.getElementById('resendBtn');
    const otpError   = document.getElementById('otpError');
    const timeLeftEl = document.getElementById('timeLeft');
    const timerEl    = document.getElementById('timer');
    const otpFullInput = document.getElementById('otpFull');
    let timerInterval;

    startTimer();

    // Auto-focus first input
    if (otpBoxes.length > 0) {
      otpBoxes[0].focus();
    }

    function syncFullOtp() {
      let full = '';
      otpBoxes.forEach(b => full += (b.value || '').trim());
      if (otpFullInput) otpFullInput.value = full;
      return full;
    }

    otpBoxes.forEach((box, i) => {
      // Handle typing and mobile autofill
      box.addEventListener('input', function (e) {
        clearErrors();
        const val = this.value;
        const cleanDigits = val.replace(/\D/g, '');

        if (cleanDigits.length > 1) {
          // Multi-digit (e.g. mobile autofill or fast typing)
          cleanDigits.split('').slice(0, 6).forEach((d, idx) => {
            if (otpBoxes[idx]) otpBoxes[idx].value = d;
          });
          const targetIndex = Math.min(cleanDigits.length, 5);
          otpBoxes[targetIndex].focus();
          const fullCode = syncFullOtp();
          if (fullCode.length === 6) {
            setTimeout(() => {
              if (form.requestSubmit) form.requestSubmit();
              else form.submit();
            }, 200);
          }
        } else if (cleanDigits.length === 1) {
          this.value = cleanDigits;
          if (i < otpBoxes.length - 1) {
            otpBoxes[i + 1].focus();
          } else if (i === otpBoxes.length - 1) {
            const fullCode = syncFullOtp();
            if (fullCode.length === 6) {
              setTimeout(() => {
                if (form.requestSubmit) form.requestSubmit();
                else form.submit();
              }, 200);
            }
          }
        } else {
          this.value = '';
        }
        syncFullOtp();
      });

      // Handle Backspace and arrow navigation
      box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace') {
          if (!this.value && i > 0) {
            e.preventDefault();
            otpBoxes[i - 1].value = '';
            otpBoxes[i - 1].focus();
          } else {
            this.value = '';
          }
          syncFullOtp();
        } else if (e.key === 'ArrowLeft' && i > 0) {
          e.preventDefault();
          otpBoxes[i - 1].focus();
        } else if (e.key === 'ArrowRight' && i < otpBoxes.length - 1) {
          e.preventDefault();
          otpBoxes[i + 1].focus();
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (form.requestSubmit) form.requestSubmit();
          else form.submit();
        }
      });

      // Handle Paste
      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text');
        const digits = pastedData.replace(/\D/g, '').slice(0, 6);
        if (digits.length > 0) {
          digits.split('').forEach((d, idx) => {
            if (otpBoxes[idx]) otpBoxes[idx].value = d;
          });
          const targetIdx = Math.min(digits.length, 5);
          otpBoxes[targetIdx].focus();
          clearErrors();
          syncFullOtp();
          if (digits.length === 6) {
            setTimeout(() => {
              if (form.requestSubmit) form.requestSubmit();
              else form.submit();
            }, 200);
          }
        }
      });

      // Select text on focus for easy overwriting
      box.addEventListener('focus', function () {
        this.select();
      });
    });

    form.addEventListener('submit', function (e) {
      const code = syncFullOtp();
      if (code.length !== 6 || !/^\d{6}$/.test(code)) {
        e.preventDefault();
        showError('Please enter all 6 digits of the verification code.');
        otpBoxes.forEach(b => {
          if (!b.value.trim()) b.classList.add('error');
        });
        return false;
      }
      verifyBtn.style.pointerEvents = 'none';
      btnText.textContent = 'Verifying...';
      btnSpinner.style.display = 'inline-block';
    });

    function startTimer() {
      const expiry = parseInt(expiryTime);
      if (isNaN(expiry) || expiry === 0) return;

      // Allow resend button after 30 seconds cooldown
      setTimeout(() => {
        if (resendBtn) {
          resendBtn.disabled = false;
        }
      }, 30000);

      function update() {
        const left = expiry - Date.now();
        if (left <= 0) {
          if (timerInterval) clearInterval(timerInterval);
          timerEl.classList.add('expired');
          timeLeftEl.textContent = '00:00';
          if (resendBtn) resendBtn.disabled = false;
          return;
        }
        const m = Math.floor(left / 60000);
        const s = Math.floor((left % 60000) / 1000);
        timeLeftEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
      }

      update(); // Update immediately on page load
      timerInterval = setInterval(update, 1000);
    }

    function showError(msg) {
      otpError.textContent = msg;
      otpError.classList.add('show');
    }

    function clearErrors() {
      otpError.classList.remove('show');
      otpBoxes.forEach(b => b.classList.remove('error'));
    }

    resendBtn.addEventListener('click', function() {
      window.location.href = '<?= url('/resend-otp') ?>';
    });
  });
  </script>
</body>
</html>
