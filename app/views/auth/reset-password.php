<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Reset Password</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
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

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: var(--gray-bg);
      color: var(--txt);
      line-height: 1.5;
    }

    .auth-split {
      display: flex;
      min-height: 100vh;
    }

    .auth-left {
      flex: 1;
      position: relative;
      background: #1a1a1a;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px;
      overflow: hidden;
    }

    .auth-left img {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 1;
      opacity: 0.7;
    }

    .auth-overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 2;
    }

    .auth-content {
      position: relative;
      z-index: 3;
      max-width: 550px;
      animation: fadeInRight 1.2s ease-out;
    }

    .auth-header-title {
      font-family: 'Poppins', sans-serif;
      font-size: 2.5rem;
      color: #e1ab39;
      line-height: 1.1;
      margin-bottom: 24px;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 800;
    }

    .auth-header-desc {
      font-family: 'Poppins', sans-serif;
      font-size: 1.1rem;
      color: white;
      line-height: 1.6;
      font-weight: 300;
    }

    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .auth-right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
      background: #fff;
    }

    .auth-card {
      width: 100%;
      max-width: 450px;
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .auth-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 10px;
      color: var(--txt);
    }

    .auth-subtitle {
      font-size: 0.95rem;
      color: var(--txt-2);
      margin-bottom: 25px;
    }

    .field {
      margin-bottom: 20px;
      position: relative;
    }

    .field label {
      display: block;
      font-weight: 500;
      margin-bottom: 6px;
      color: var(--txt-2);
    }

    .field input {
      width: 100%;
      padding: 12px 40px 12px 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    .field input:focus {
      border-color: var(--green);
      outline: none;
      box-shadow: 0 0 0 3px var(--green)33;
    }

    .field input.error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px var(--danger)33;
    }

    .field input.valid {
      border-color: #4caf50;
      box-shadow: 0 0 0 3px #4caf5033;
    }

    .input-wrap {
      position: relative;
    }

    .toggle-btn {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      cursor: pointer;
      font-size: 1.1rem;
      color: var(--txt-3);
    }

    .toggle-btn:focus {
      outline: none;
    }

    .error-msg,
    .success-msg {
      font-size: 0.85rem;
      margin-top: 5px;
      display: none;
    }

    .error-msg {
      color: var(--danger);
    }

    .success-msg {
      color: #4caf50;
    }

    .error-msg.show,
    .success-msg.show {
      display: block;
    }

    .strength-label {
      font-size: 0.85rem;
      margin-top: 5px;
      display: none;
    }

    .btn-auth {
      width: 100%;
      padding: 12px;
      background: var(--green);
      color: #fff;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .btn-auth:disabled {
      background: #aaa;
      cursor: not-allowed;
    }

    .btn-auth:hover:not(:disabled) {
      background: var(--green-dark);
    }

    .spinner {
      margin-left: 10px;
      border: 2px solid #fff;
      border-top: 2px solid var(--green);
      border-radius: 50%;
      width: 16px;
      height: 16px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .auth-back {
      display: inline-flex;
      align-items: center;
      margin-top: 20px;
      font-size: 0.9rem;
      color: var(--green);
      text-decoration: none;
      transition: 0.2s;
    }

    .auth-back:hover {
      text-decoration: underline;
    }

    .auth-back i {
      margin-right: 5px;
    }

    @media (max-width: 991px) {
      .auth-split {
        flex-direction: column;
      }
      .auth-left, .auth-right {
        flex: unset;
        width: 100%;
      }
      .auth-left {
        padding: 20px;
      }
      .auth-right {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div id="header-placeholder"></div>

  <div class="auth-split">
    <div class="auth-left">
      <div class="auth-overlay"></div>
      <img src="<?= asset('assets/ISCAG1.png') ?>" alt="ISCAG Philippines">
      <div class="auth-content">
        <h1 class="auth-header-title">Islamic Studies, Call and Guidance of the Philippines</h1>
        <p class="auth-header-desc">To strive for excellence in education and personal growth of its members, promoting service to others, integrity, and love for God.</p>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-card">
        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-subtitle">Create a new secure password for your account.</p>

        <form id="resetPasswordForm" action="<?= url('/reset-password') ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; color: var(--danger); text-align: center;"><?= $error ?></div>
          <?php endif; ?>
          <div class="field">
            <label for="newPassword">New Password</label>
            <div class="input-wrap">
              <input type="password" name="password" id="newPassword" placeholder="Enter new password" required/>
              <button type="button" class="toggle-btn" id="toggleNew">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            <span class="error-msg" id="newPasswordError"></span>
            <span class="strength-label" id="strengthLabel"></span>
          </div>

          <div class="field">
            <label for="confirmPassword">Confirm Password</label>
            <div class="input-wrap">
              <input type="password" name="confirmpass" id="confirmPassword" placeholder="Confirm new password" required/>
              <button type="button" class="toggle-btn" id="toggleConfirm">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            <span class="error-msg" id="confirmPasswordError"></span>
            <span class="success-msg" id="confirmPasswordSuccess">Passwords match!</span>
          </div>

          <button type="submit" class="btn-auth" id="resetBtn" disabled>
            <span id="btnText">Reset Password</span>
            <span id="btnSpinner" class="spinner" style="display:none;"></span>
          </button>
        </form>

        <a href="<?= url('/login') ?>" class="auth-back">
          <i class="bi bi-arrow-left"></i> Back to Login
        </a>
      </div>
    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      
      const newPwInput = document.getElementById('newPassword');
      const confPwInput = document.getElementById('confirmPassword');
      const resetBtn = document.getElementById('resetBtn');
      const btnText = document.getElementById('btnText');
      const btnSpinner = document.getElementById('btnSpinner');
      const newPwErr = document.getElementById('newPasswordError');
      const confPwErr = document.getElementById('confirmPasswordError');
      const confPwOk = document.getElementById('confirmPasswordSuccess');
      const strengthLbl = document.getElementById('strengthLabel');
      const form = document.getElementById('resetPasswordForm');

      document.getElementById('toggleNew').addEventListener('click', (e) => {
        e.preventDefault();
        toggleVisibility(newPwInput, e.currentTarget);
      });

      document.getElementById('toggleConfirm').addEventListener('click', (e) => {
        e.preventDefault();
        toggleVisibility(confPwInput, e.currentTarget);
      });

      newPwInput.addEventListener('input', handleNewPasswordInput);
      confPwInput.addEventListener('input', validatePasswordMatch);

      form.addEventListener('submit', function (e) {
        if (!validateForm()) {
          e.preventDefault();
        } else {
          resetBtn.disabled = true;
          btnText.textContent = 'Resetting...';
          btnSpinner.style.display = 'inline-block';
        }
      });

      function toggleVisibility(input, btn) {
        const icon = btn.querySelector('i');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
      }

      function handleNewPasswordInput() {
        const value = newPwInput.value;
        if (!value) {
          hide(newPwErr);
          newPwInput.classList.remove('error', 'valid');
          updateStrengthIndicator(0);
          return;
        }

        const pwStrength = calculateStrength(value);
        updateStrengthIndicator(pwStrength);
        const validationMsg = validatePassword(value);
        validationMsg ? show(newPwErr, validationMsg) : hide(newPwErr);
        if (confPwInput.value) validatePasswordMatch();
      }

      function calculateStrength(password) {
        if (!password) return 0;
        let score = 0;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;

        const isLongEnough = password.length >= 8;
        if (!isLongEnough || score <= 1) return 1;
        if (isLongEnough && score >= 4) return 3;
        return 2;
      }

      function updateStrengthIndicator(level) {
        const labels = ['', 'Weak', 'Medium', 'Strong'];
        const colors = ['', '#FF4D4F', '#FFC107', '#4CAF50'];

        if (level === 0) {
          newPwInput.style.borderColor = '';
          newPwInput.style.boxShadow = '';
          strengthLbl.style.display = 'none';
          resetBtn.disabled = true;
          return;
        }

        newPwInput.style.borderColor = colors[level];
        newPwInput.style.boxShadow = `0 0 0 3px ${colors[level]}33`;
        strengthLbl.style.display = 'block';
        strengthLbl.style.color = colors[level];
        strengthLbl.textContent = level === 1 ? 'Your password is too weak' : labels[level];
        resetBtn.disabled = level === 1;
      }

      function validatePassword(password) {
        const isValid = password.length >= 8 &&
          /[A-Z]/.test(password) &&
          /[a-z]/.test(password) &&
          /\d/.test(password) &&
          /[!@#$%^&*(),.?":{}|<>]/.test(password);

        newPwInput.classList.toggle('error', !isValid);
        newPwInput.classList.toggle('valid', isValid);
        return isValid ? null : 'Password must be 8+ chars with uppercase, lowercase, number & special character.';
      }

      function validatePasswordMatch() {
        const match = newPwInput.value === confPwInput.value && confPwInput.value !== '';
        if (!confPwInput.value) {
          confPwInput.classList.remove('error', 'valid');
          hide(confPwErr);
          hide(confPwOk);
          return false;
        }

        if (match) {
          confPwInput.classList.add('valid');
          confPwInput.classList.remove('error');
          hide(confPwErr);
          show(confPwOk);
          return true;
        }

        confPwInput.classList.add('error');
        confPwInput.classList.remove('valid');
        show(confPwErr, 'Passwords do not match');
        hide(confPwOk);
        return false;
      }

      function validateForm() {
        if (calculateStrength(newPwInput.value) === 1) return false;
        const msg = validatePassword(newPwInput.value);
        if (msg) {
          show(newPwErr, msg);
          return false;
        }
        return validatePasswordMatch();
      }

      function showSuccessPrompt() {
        let prompt = document.getElementById('successPrompt');
        if (!prompt) {
          prompt = document.createElement('div');
          prompt.id = 'successPrompt';
          prompt.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 9999;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
          `;
          document.body.appendChild(prompt);
        }
        prompt.textContent = 'Password reset successfully! Redirecting to login...';
        prompt.style.display = 'block';

        setTimeout(() => {
          window.location.href = '<?= url('/login') ?>?reset=success';
        }, 3000);
      }

      function show(element, message) {
        if (message) element.textContent = message;
        element.classList.add('show');
      }

      function hide(element) {
        element.classList.remove('show');
      }
    });
  </script>

</body>
</html>
