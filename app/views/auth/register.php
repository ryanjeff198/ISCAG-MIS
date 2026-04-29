<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Sign Up</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Poppins:wght@300;400;500;600&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    /* ─── VARIABLES ─── */
:root {
  --green: #1c6b3a;
  --green-dark: #134d28;
  --gray-bg: #f5f5f5;
  --border: #e2e2e2;
  --txt: #111;
  --txt-2: #555;
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
.auth-split {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* LEFT SIDE (IMAGE + TEXT) */
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

/* HOME LINK */
.auth-home-link {
  position: absolute;
  top: 20px;
  left: 24px;
  font-size: 13px;
  font-weight: 600;
  color: var(--green);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.auth-home-link:hover {
  text-decoration: underline;
}

/* RIGHT SIDE */
.auth-right {
  flex: 1;
  position: relative;
  display: flex;
  flex-direction: column; /* Changed from center to allow better scrolling */
  align-items: center;
  background: var(--gray-bg);
  padding: 60px 20px; /* Better padding for scrollable area */
  overflow-y: auto;
}

/* CARD */
.auth-card {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  width: 100%;
  max-width: 460px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.08);
  margin-bottom: 40px; /* Space at bottom for scrolling */
}

/* TITLE */
.auth-title {
  font-size: 26px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 24px;
}

/* FIELD */
.field {
  margin-bottom: 16px;
}

.field label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 6px;
}

.field input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 14px;
}

.field input:focus {
  outline: none;
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(28,107,58,0.1);
}

/* NAME ROW */
.name-row {
  display: flex;
  gap: 12px;
}

.name-row .field {
  flex: 1;
}

/* CHECKBOX */
.check-row {
  display: flex;
  gap: 10px;
  font-size: 13px;
  margin: 16px 0;
  align-items: flex-start;
  cursor: pointer; /* Makes the whole row area feel interactive */
}

.check-row input,
.check-row label {
  cursor: pointer;
}

.check-row input {
  margin-top: 3px;
}

/* BUTTON */
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
}

.btn-auth:hover {
  background: var(--green-dark);
}

.btn-auth a {
  color: #fff;
  text-decoration: none;
  display: block;
}

.btn-auth a:hover {
  color: #fff;
}

/* LINKS */
.auth-link {
  color: var(--green);
  text-decoration: none;
}

.auth-link:hover {
  text-decoration: underline;
}

/* FOOTER */
.auth-footer-text {
  text-align: center;
  font-size: 14px;
  margin-top: 16px;
}

/* PASSWORD VALIDATION ENHANCEMENTS */
.password-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-group input {
  padding-right: 40px !important; /* Space for the eye icon */
}

.toggle-password {
  position: absolute;
  right: 12px;
  cursor: pointer;
  color: var(--txt-2);
  font-size: 1.1rem;
  transition: color 0.2s;
  z-index: 5;
}

.toggle-password:hover {
  color: var(--green);
}

.error-msg {
  font-size: 12px;
  color: var(--danger);
  margin-top: 4px;
  display: block;
}

.password-info-btn {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
  cursor: pointer;
  color: var(--txt-2);
  transition: color 0.2s;
}

.password-info-btn:hover {
  color: var(--green);
}

.password-info-icon {
  font-size: 0.85rem; /* Smaller size */
}

.password-info-label {
  font-size: 11px;
  font-weight: 500;
}

/* Dynamic Borders */
.field input.valid-pass {
  border-color: #198754 !important;
  box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1) !important;
}

.field input.invalid-pass {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
}

/* Tooltip/Requirements Box (Smooth & Accurate) */
.requirements-container {
  position: absolute;
  top: 30px; 
  right: 0;
  width: 280px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  z-index: 120;
  
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
  pointer-events: none;
}

.requirements-container.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
  pointer-events: auto;
}

.requirements-container::before {
  content: "";
  position: absolute;
  top: -6px;
  right: 8px; /* Perfectly aligned with the info icon */
  width: 10px;
  height: 10px;
  background: #fff;
  border-left: 1px solid var(--border);
  border-top: 1px solid var(--border);
  transform: rotate(45deg);
}

.password-info-btn {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
  cursor: pointer;
  color: var(--txt-2);
  position: relative; /* Anchor for the tooltip */
  
  /* Hidden by default, appears on input */
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s;
}

.password-info-btn.show-info {
  opacity: 1;
  visibility: visible;
}

.requirements-container.show {
  display: block;
}

.requirements-title {
  font-size: 13px;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--txt);
  display: flex;
  align-items: center;
  gap: 6px;
}

.requirement-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.requirement-item {
  font-size: 12px;
  color: var(--txt-2);
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
  transition: all 0.2s;
}

.requirement-item i {
  font-size: 14px;
}

.requirement-item.met {
  color: #198754;
}

.requirement-item.not-met {
  color: #dc3545;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
  .auth-split {
    flex-direction: column;
    height: auto;
    overflow: visible;
  }

  .auth-left {
    height: 250px;
    flex: none;
  }

  .auth-right {
    padding: 20px;
    height: auto;
    overflow: visible;
  }

  .name-row {
    flex-direction: column;
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
      <a href="<?= url('/') ?>" class="auth-home-link"><i class="bi bi-arrow-left"></i> Home</a>
      <div class="auth-card register-card">
        <h1 class="auth-title">Create Account</h1>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; color: var(--danger); text-align: center;"><?= $error ?></div>
        <?php endif; ?>

        <form action="<?= url('/register') ?>" method="POST" id="registrationForm">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

        <div class="name-row">
          <div class="field">
            <label for="firstName">First Name</label>
            <input type="text" name="first_name" id="firstName" placeholder="Juan" value="<?= e($data['first_name'] ?? '') ?>" required/>
          </div>
          <div class="field">
            <label for="lastName">Last Name</label>
            <input type="text" name="last_name" id="lastName" placeholder="Dela Cruz" value="<?= e($data['last_name'] ?? '') ?>" required/>
          </div>
        </div>

        <div class="field">
          <label for="sex">Sex</label>
          <select name="sex" id="sex" class="form-control" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border);" required>
            <option value="" disabled <?= !isset($data['sex']) ? 'selected' : '' ?>>Select sex</option>
            <option value="Male" <?= (isset($data['sex']) && $data['sex'] === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (isset($data['sex']) && $data['sex'] === 'Female') ? 'selected' : '' ?>>Female</option>
          </select>
        </div>

        <div class="field">
          <label for="email">Email Address</label>
          <input type="email" name="email" id="email" placeholder="juan@email.com" value="<?= e($data['email'] ?? '') ?>" required/>
        </div>

        <div class="field">
          <label for="phone">Phone Number</label>
          <input type="tel" name="contactnum" id="phone" placeholder="+63 9XX XXX XXXX" value="<?= e($data['contactnum'] ?? '') ?>"/>
        </div>

        <div class="field" style="position: relative;">
          <label for="password">Password</label>
          <div class="password-input-group">
            <input type="password" name="password" id="password" placeholder="Create a password" required/>
            <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
          </div>
          
          <div class="password-info-btn" id="infoButtonWrapper">
            <div id="passwordInfoBtn" style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
              <i class="bi bi-info-circle password-info-icon"></i>
              
              <div class="requirements-container" id="requirementsContainer">
                <div class="requirements-title">
                  <i class="bi bi-shield-lock"></i> Password Requirements
                </div>
                <ul class="requirement-list">
                  <li class="requirement-item" id="req-length">
                    <i class="bi bi-x-circle-fill"></i> Minimum 8 characters
                  </li>
                  <li class="requirement-item" id="req-upper">
                    <i class="bi bi-x-circle-fill"></i> At least one uppercase letter
                  </li>
                  <li class="requirement-item" id="req-lower">
                    <i class="bi bi-x-circle-fill"></i> At least one lowercase letter
                  </li>
                  <li class="requirement-item" id="req-number">
                    <i class="bi bi-x-circle-fill"></i> At least one number
                  </li>
                  <li class="requirement-item" id="req-special">
                    <i class="bi bi-x-circle-fill"></i> At least one special character
                  </li>
                </ul>
              </div>
            </div>
            <span class="password-info-label">Password Requirements</span>
          </div>
        </div>

        <div class="field">
          <label for="confirmPassword">Confirm Password</label>
          <div class="password-input-group">
            <input type="password" name="confirmpass" id="confirmPassword" placeholder="Repeat password" required/>
            <i class="bi bi-eye-slash toggle-password" id="toggleConfirmPassword"></i>
          </div>
          <span class="error-msg" id="confirmError"></span>
        </div>

        <div class="check-row d-flex align-items-center gap-2">
          <input type="checkbox" id="terms" required style="width: 16px; height: 16px; cursor: pointer; accent-color: var(--green);"/>
          <label for="terms" style="font-size: 13px; cursor: pointer;">
            I agree to ISCAG Philippines' <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="auth-link">Terms & Conditions</a>
          </label>
        </div>

        <button type="submit" class="btn-auth" id="registerBtn">Create Account</button>
        </form>

        <!-- Terms Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
              <div class="modal-header" style="background: var(--green); color: white; border: none; padding: 25px;">
                <h5 class="modal-title" id="termsModalLabel" style="font-family: 'Poppins', sans-serif; font-weight: 700; letter-spacing: 1px;">TERMS & CONDITIONS</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" style="padding: 40px; color: var(--txt-2); line-height: 1.8; font-size: 15px; max-height: 60vh; overflow-y: auto;">
                <div class="terms-content">
                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 15px;">1. Acceptance of Terms</h6>
                  <p style="margin-bottom: 20px;">By accessing and using the ISCAG Philippines Management Information System (MIS), you agree to be bound by these Terms and Conditions. If you do not agree, please do not use the system.</p>
                  
                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 15px;">2. User Conduct</h6>
                  <p style="margin-bottom: 20px;">You are responsible for maintaining the confidentiality of your account credentials. You agree to use the system only for its intended purposes and in accordance with Islamic values and Philippine laws.</p>
                  
                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 15px;">3. Privacy & Data Protection</h6>
                  <p style="margin-bottom: 20px;">We respect your privacy. Any personal data collected through this system will be handled in accordance with our Privacy Policy and the Data Privacy Act of 2012. Your information will be used solely for administrative and spiritual guidance services.</p>
                  
                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 15px;">4. System Integrity</h6>
                  <p style="margin-bottom: 20px;">Unauthorized attempts to modify, breach, or damage the system are strictly prohibited and may result in legal action and termination of account access.</p>
                  
                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 15px;">5. Amendments</h6>
                  <p>ISCAG Philippines reserves the right to modify these terms at any time. Continued use of the system after such changes constitutes acceptance of the new terms.</p>
                </div>
              </div>
              <div class="modal-footer" style="padding: 20px 40px; border: none; background: #f9f9f9;">
                <button type="button" class="btn-auth" id="acceptTermsBtn" data-bs-dismiss="modal" style="width: auto; padding: 10px 30px;">I Understand</button>
              </div>
            </div>
          </div>
        </div>

        <p class="auth-footer-text">
          Already have an account? <a href="<?= url('/login') ?>" class="auth-link">Log In</a>
        </p>

      </div>
    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById('password');
      const infoBtn = document.getElementById('passwordInfoBtn');
      const requirementsBox = document.getElementById('requirementsContainer');
      
      const reqs = {
        length: { el: document.getElementById('req-length'), regex: /.{8,}/ },
        upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
        lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
        number: { el: document.getElementById('req-number'), regex: /[0-9]/ },
        special: { el: document.getElementById('req-special'), regex: /[!@#$%^&*(),.?":{}|<>]/ }
      };

      // Toggle requirements box ONLY on icon or label hover
      const infoWrapper = document.getElementById('infoButtonWrapper');
      infoWrapper.addEventListener('mouseenter', () => requirementsBox.classList.add('show'));
      infoWrapper.addEventListener('mouseleave', () => {
        requirementsBox.classList.remove('show');
      });
      
      passwordInput.addEventListener('focus', () => {
        // Do not show on focus
      });
      passwordInput.addEventListener('blur', () => {
        // Do not hide on blur as it's hover-only now
      });

      passwordInput.addEventListener('input', function() {
        const value = passwordInput.value;
        const infoWrapper = document.getElementById('infoButtonWrapper');
        let allMet = true;

        // Show the i icon and label only when inputting and NOT yet valid
        if (value.length > 0 && !allMet) {
          infoWrapper.classList.add('show-info');
        } else {
          infoWrapper.classList.remove('show-info');
          requirementsBox.classList.remove('show');
        }

        // Validate each requirement
        for (const key in reqs) {
          const met = reqs[key].regex.test(value);
          const el = reqs[key].el;
          const icon = el.querySelector('i');

          if (met) {
            el.classList.remove('not-met');
            el.classList.add('met');
            icon.className = 'bi bi-check-circle-fill';
          } else {
            el.classList.remove('met');
            el.classList.add('not-met');
            icon.className = 'bi bi-x-circle-fill';
            allMet = false;
          }
        }

        // Show/Hide info guidance based on validation status
        if (value.length > 0 && !allMet) {
          infoWrapper.classList.add('show-info');
        } else {
          infoWrapper.classList.remove('show-info');
          requirementsBox.classList.remove('show');
        }

        // Update border color
        if (value.length === 0) {
          passwordInput.classList.remove('valid-pass', 'invalid-pass');
        } else if (allMet) {
          passwordInput.classList.remove('invalid-pass');
          passwordInput.classList.add('valid-pass');
        } else {
          passwordInput.classList.remove('valid-pass');
          passwordInput.classList.add('invalid-pass');
        }
      });

      // Confirm Password Validation
      const confirmInput = document.getElementById('confirmPassword');
      const confirmError = document.getElementById('confirmError');

      confirmInput.addEventListener('input', function() {
        if (confirmInput.value === passwordInput.value) {
          confirmError.textContent = '';
          confirmInput.style.borderColor = '#198754';
        } else {
          confirmError.textContent = 'Passwords do not match';
          confirmInput.style.borderColor = '#dc3545';
        }
      });
      
      // Password Visibility Toggle
      function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        
        toggle.addEventListener('click', function() {
          const isPassword = input.getAttribute('type') === 'password';
          input.setAttribute('type', isPassword ? 'text' : 'password');
          
          if (isPassword) {
            // Now showing (Text)
            this.classList.remove('bi-eye-slash');
            this.classList.add('bi-eye');
          } else {
            // Now hiding (Dots)
            this.classList.remove('bi-eye');
            this.classList.add('bi-eye-slash');
          }
        });
      }

      setupPasswordToggle('password', 'togglePassword');
      setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');

      // Auto-check terms when modal 'I Understand' is clicked
      document.getElementById('acceptTermsBtn').addEventListener('click', function() {
        document.getElementById('terms').checked = true;
      });
    });
  </script>

</body>
</html>
