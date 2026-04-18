<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Verify OTP</title>
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
        <?php if (isset($error)): ?>
          <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px;"><?= $error ?></div>
        <?php endif; ?>
        <div class="otp-inputs">
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="0" required autofocus>
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="1" required>
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="2" required>
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="3" required>
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="4" required>
          <input type="text" name="otp[]" class="otp-box" maxlength="1" data-index="5" required>
        </div>

        <div class="otp-error" id="otpError"></div>

        <div class="otp-timer-row">
          <span class="otp-timer" id="timer">Time remaining: <strong id="timeLeft">05:00</strong></span>
          <button type="button" class="otp-resend" id="resendBtn" disabled>Resend OTP</button>
        </div>

        <button type="submit" class="btn-auth" id="verifyBtn">
          <span id="btnText">Verify OTP</span>
          <span id="btnSpinner" class="spinner" style="display:none;"></span>
        </button>
      </form>

      <a href="<?= url('/forgot-password') ?>" class="otp-back">
        <i class="bi bi-arrow-left"></i> Back to Email
      </a>

    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const userEmail = "<?= $_SESSION['temp_email'] ?? '' ?>";
    const otpExpiry = "<?= $_SESSION['otp_expiry'] ?? '0' ?>";

    if (!userEmail) {
      alert('Session expired. Please register again.');
      window.location.href = '<?= url('/register') ?>'; return;
    }

    document.getElementById('emailDisplay').textContent = userEmail;

    const otpBoxes   = document.querySelectorAll('.otp-box');
    const verifyBtn  = document.getElementById('verifyBtn');
    const btnText    = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const resendBtn  = document.getElementById('resendBtn');
    const otpError   = document.getElementById('otpError');
    const timerEl    = document.getElementById('timer');
    const timeLeftEl = document.getElementById('timeLeft');
    let timerInterval;

    startTimer();

    otpBoxes.forEach((box, i) => {
      box.addEventListener('input', function (e) {
        if (!/^\d$/.test(e.target.value) && e.target.value !== '') { e.target.value = ''; return; }
        if (e.target.value && i < otpBoxes.length - 1) otpBoxes[i + 1].focus();
        clearErrors();
      });
      box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !e.target.value && i > 0) otpBoxes[i - 1].focus();
      });
      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const d = e.clipboardData.getData('text').replace(/\D/g, '');
        if (d.length === 6) { otpBoxes.forEach((b, j) => b.value = d[j] || ''); otpBoxes[5].focus(); clearErrors(); }
      });
    });

    // Form submission is handled by HTML POST now
    // resendBtn.addEventListener('click', resendOTP);

    function startTimer() {
      const expiry = parseInt(otpExpiry);
      timerInterval = setInterval(() => {
        const left = expiry - Date.now();
        if (left <= 0) {
          clearInterval(timerInterval);
          timerEl.classList.add('expired');
          timeLeftEl.textContent = 'Expired';
          resendBtn.disabled = false;
          verifyBtn.disabled = true;
          showError('OTP has expired. Please request a new one.');
          return;
        }
        const m = Math.floor(left / 60000);
        const s = Math.floor((left % 60000) / 1000);
        timeLeftEl.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
      }, 1000);
    }

    // Server-side handles verification now. 
    // This is just for UI focus/pasting.

    function resendOTP() {
      const newOTP    = Math.floor(100000 + Math.random() * 900000).toString();
      const newExpiry = Date.now() + 5 * 60 * 1000;
      sessionStorage.setItem('resetOTP',  newOTP);
      sessionStorage.setItem('otpExpiry', newExpiry.toString());
      clearInterval(timerInterval);
      timerEl.classList.remove('expired');
      startTimer();
      resendBtn.disabled = true; verifyBtn.disabled = false;
      otpBoxes.forEach(b => { b.value = ''; b.classList.remove('error'); });
      clearErrors(); otpBoxes[0].focus();
      alert(`New OTP sent. For demo purposes, your new OTP is: ${newOTP}`);
    }

    function showError(msg) { otpError.textContent = msg; otpError.classList.add('show'); }
    function clearErrors()  { otpError.classList.remove('show'); otpBoxes.forEach(b => b.classList.remove('error')); }
  });
  </script>
</body>
</html>
