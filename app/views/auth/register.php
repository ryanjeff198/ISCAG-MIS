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
          <div id="phoneError" style="font-size: 12px; color: var(--danger); margin-top: 4px; display: none;"></div>
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
          <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.2);">
              <div class="modal-header" style="background: var(--green); color: white; border: none; padding: 25px;">
                <div>
                  <h5 class="modal-title" id="termsModalLabel" style="font-family: 'Poppins', sans-serif; font-weight: 700; letter-spacing: 0.5px; margin: 0;">TERMS AND CONDITIONS</h5>
                  <div style="font-size: 0.82rem; opacity: 0.9; margin-top: 4px;">ISCAG Philippines – Web-Based Management Information System</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" style="padding: 35px 40px; color: var(--txt-2); line-height: 1.75; font-size: 14.5px; max-height: 65vh; overflow-y: auto;">
                <div class="terms-content">
                  <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 0.88rem; color: #166534;">
                    <strong>Effective Date:</strong> <?= date('F d, Y') ?><br>
                    Welcome to the ISCAG Philippines Web-Based Management Information System. By accessing or using this system, you agree to follow the Terms and Conditions stated below. Please read them carefully before proceeding.
                  </div>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">1. Acceptance of Terms</h6>
                  <p style="margin-bottom: 18px;">By creating an account, logging in, or using any feature of the system, you acknowledge that you have read, understood, and agreed to these Terms and Conditions. If you do not agree with these terms, you should not access or use the system.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">2. Purpose of the System</h6>
                  <p style="margin-bottom: 18px;">The ISCAG Management Information System is designed to provide a centralized platform for managing the organization's services, records, transactions, and daily operations. It supports apartment and tenant management, billing and payments, Islamic education, marriage services, Da'wah activities, Damayan and burial services, donations, and other organizational transactions.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">3. User Accounts and Responsibilities</h6>
                  <p style="margin-bottom: 10px;">All users are responsible for maintaining the confidentiality and security of their account credentials.</p>
                  <p style="margin-bottom: 6px;">Users must:</p>
                  <ul style="margin-bottom: 14px; padding-left: 20px;">
                    <li>Provide accurate and updated personal information during registration.</li>
                    <li>Keep their usernames and passwords confidential.</li>
                    <li>Avoid sharing their accounts with unauthorized individuals.</li>
                    <li>Notify the system administrator of any suspicious account activity.</li>
                    <li>Use the system only for its intended purposes.</li>
                    <li>Ensure that all submitted information and documents are accurate and legitimate.</li>
                  </ul>
                  <p style="margin-bottom: 18px;">Users are responsible for activities performed through their respective accounts.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">4. User Access and Permissions</h6>
                  <p style="margin-bottom: 10px;">Access to the system's features and information is limited based on the user's assigned authorization. Users are only permitted to access and use the functions provided to their accounts.</p>
                  <p style="margin-bottom: 18px;">Users must not attempt to access restricted features, confidential records, or information beyond their authorized permissions. Any unauthorized access, misuse, or violation of access restrictions may result in appropriate administrative action, including account suspension or deactivation.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">5. Accuracy of Information and Submitted Documents</h6>
                  <p style="margin-bottom: 10px;">Users must ensure that all information, applications, and uploaded documents submitted through the system are truthful, complete, and accurate.</p>
                  <p style="margin-bottom: 10px;">ISCAG Philippines reserves the right to review, verify, reject, or request corrections to submitted information and documents when necessary.</p>
                  <p style="margin-bottom: 18px;">Providing false, misleading, or unauthorized information may result in the rejection of applications or appropriate account restrictions.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">6. Proper Use of the System</h6>
                  <p style="margin-bottom: 6px;">Users agree not to:</p>
                  <ul style="margin-bottom: 14px; padding-left: 20px;">
                    <li>Use the system for fraudulent, illegal, or unauthorized activities.</li>
                    <li>Attempt to access another user's account.</li>
                    <li>Modify, damage, or interfere with system functions and stored information.</li>
                    <li>Upload malicious files or harmful content.</li>
                    <li>Misuse, copy, or disclose confidential organizational information without authorization.</li>
                    <li>Use the system to harass, threaten, or harm other users.</li>
                  </ul>
                  <p style="margin-bottom: 18px;">Any misuse of the system may result in account suspension or other appropriate administrative action.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">7. Privacy and Protection of Personal Information</h6>
                  <p style="margin-bottom: 10px;">ISCAG Philippines respects the privacy of its users and is committed to protecting personal information collected through the system.</p>
                  <p style="margin-bottom: 10px;">Personal information may be collected, processed, stored, and used for legitimate organizational purposes, including account management, service requests, tenant records, billing, payments, and administrative transactions.</p>
                  <p style="margin-bottom: 10px;">The collection and processing of personal information shall be subject to the applicable provisions of the Data Privacy Act of 2012 (Republic Act No. 10173) and its implementing rules and regulations.</p>
                  <p style="margin-bottom: 18px;">For more information, users should refer to the system's separate Privacy Notice.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">8. Records, Transactions, and Payments</h6>
                  <p style="margin-bottom: 10px;">Users are responsible for reviewing the accuracy of their submitted applications, payment information, and transaction details.</p>
                  <p style="margin-bottom: 10px;">The system provides access to billing statements, payment records, applications, and other transaction-related information. Users should report any incorrect records or discrepancies to the assigned department staff for verification.</p>
                  <p style="margin-bottom: 18px;">Submission of an application or payment document does not automatically guarantee approval or confirmation. All transactions remain subject to the organization's verification and approval procedures.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">9. System Availability and Maintenance</h6>
                  <p style="margin-bottom: 10px;">ISCAG Philippines aims to maintain the availability and proper operation of the system. However, temporary interruptions may occur due to maintenance, technical issues, updates, or other circumstances beyond the organization's control.</p>
                  <p style="margin-bottom: 18px;">The organization reserves the right to update, modify, or temporarily restrict access to certain system features when necessary.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">10. Account Suspension and Termination</h6>
                  <p style="margin-bottom: 10px;">ISCAG Philippines reserves the right to suspend, deactivate, or restrict an account when a user violates these Terms and Conditions, provides false information, misuses system resources, or engages in unauthorized activities.</p>
                  <p style="margin-bottom: 18px;">Account access may also be modified when a user's role, status, or authorization changes.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">11. Changes to the Terms and Conditions</h6>
                  <p style="margin-bottom: 18px;">ISCAG Philippines reserves the right to revise or update these Terms and Conditions when necessary. Any changes will be posted or communicated through the system. Users are encouraged to review the updated terms regularly.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">12. Contact and Assistance</h6>
                  <p style="margin-bottom: 18px;">For questions, concerns, technical issues, or reports regarding the system, users may contact the authorized ISCAG Philippines administrator or the appropriate department staff through the organization's official communication channels.</p>

                  <h6 style="color: var(--green); font-weight: 700; margin-bottom: 8px;">13. Agreement and Consent</h6>
                  <p style="margin-bottom: 6px;">By selecting the "I Agree" option and proceeding to use the system, you confirm that:</p>
                  <ul style="margin-bottom: 18px; padding-left: 20px;">
                    <li>You have read and understood these Terms and Conditions.</li>
                    <li>You agree to comply with the rules and responsibilities stated above.</li>
                    <li>You understand that your account access is subject to your assigned user role and authorization.</li>
                    <li>You agree to use the system responsibly and only for legitimate purposes.</li>
                  </ul>

                  <p style="font-weight: 600; color: var(--green); margin-top: 24px; margin-bottom: 0;">Thank you for using the ISCAG Philippines Web-Based Management Information System.</p>
                </div>
              </div>
              <div class="modal-footer" style="padding: 20px 40px; border: none; background: #f9f9f9;">
                <button type="button" class="btn-auth" id="acceptTermsBtn" data-bs-dismiss="modal" style="width: auto; padding: 10px 30px;">I Agree</button>
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
  <script src="<?= asset('js/phone-validator.js') ?>"></script>

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

      // Phone Number Validation
      const phoneInput = document.getElementById('phone');
      const phoneError = document.getElementById('phoneError');
      const registrationForm = document.getElementById('registrationForm');

      function validatePhoneInput() {
        if (!phoneInput || !phoneError) return true;
        const val = phoneInput.value.trim();
        if (val === '') {
          phoneError.textContent = '';
          phoneError.style.display = 'none';
          phoneInput.style.borderColor = '';
          return true;
        }

        if (typeof isValidPhone === 'function' && !isValidPhone(val)) {
          phoneError.textContent = 'Please enter a valid phone number with the correct country code and number format.';
          phoneError.style.display = 'block';
          phoneInput.style.borderColor = '#dc3545';
          return false;
        } else {
          phoneError.textContent = '';
          phoneError.style.display = 'none';
          phoneInput.style.borderColor = '#198754';
          return true;
        }
      }

      if (phoneInput) {
        phoneInput.addEventListener('blur', validatePhoneInput);
        phoneInput.addEventListener('input', function() {
          if (phoneError.style.display === 'block') {
            validatePhoneInput();
          }
        });
      }

      if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
          if (!validatePhoneInput()) {
            e.preventDefault();
            phoneInput.focus();
            return false;
          }
        });
      }
    });
  </script>

</body>
</html>
