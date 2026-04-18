<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Sign Up</title>
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
  min-height: 100vh;
}

/* LEFT SIDE */
.auth-left {
  flex: 1;
  background: var(--green-dark);
}

.auth-left img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* RIGHT SIDE */
.auth-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-bg);
  padding: 40px;
}

/* CARD */
.auth-card {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  width: 100%;
  max-width: 460px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.08);
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

/* PASSWORD STRENGTH */
.strength-label {
  font-size: 12px;
  margin-top: 4px;
  display: block;
}

.strength-label.weak { color: var(--danger); }
.strength-label.medium { color: orange; }
.strength-label.strong { color: green; }

/* ERROR */
.error-msg {
  font-size: 12px;
  color: var(--danger);
  margin-top: 4px;
}

/* ─── RESPONSIVE ─── */
@media (max-width: 768px) {
  .auth-split {
    flex-direction: column;
  }

  .auth-left {
    height: 200px;
  }

  .auth-right {
    padding: 20px;
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
      <img src="<?= asset('assets/bg-image.png') ?>" alt="ISCAG Philippines">
    </div>

    <div class="auth-right">
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

        <div class="field">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Create a password" required/>
          <span class="strength-label" id="strengthLabel"></span>
        </div>

        <div class="field">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" name="confirmpass" id="confirmPassword" placeholder="Repeat password" required/>
          <span class="error-msg" id="confirmError"></span>
        </div>

        <div class="check-row">
          <input type="checkbox" id="terms"/>
          <label for="terms">
            I agree to ISCAG Philippines'
            <a href="#" class="auth-link">Terms of Service</a> and
            <a href="#" class="auth-link">Privacy Policy</a>
          </label>
        </div>

        <button type="submit" class="btn-auth" id="registerBtn">Create Account</button>
        </form>

        <p class="auth-footer-text">
          Already have an account? <a href="<?= url('/login') ?>" class="auth-link">Log In</a>
        </p>

      </div>
    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
