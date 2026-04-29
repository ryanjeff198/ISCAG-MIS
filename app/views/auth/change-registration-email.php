<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Change Email</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
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
.auth-split {
  display: flex;
  min-height: 100vh;
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
  max-width: 420px;
  box-shadow: 0 6px 25px rgba(0,0,0,0.08);
  text-align: center;
}

/* ICON */
.change-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 16px;
  border-radius: 50%;
  background: rgba(28,107,58,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--green);
  font-size: 26px;
}

/* TITLE */
.auth-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
}

/* SUBTITLE */
.auth-subtitle {
  font-size: 14px;
  color: var(--txt-2);
  margin-bottom: 24px;
  line-height: 1.6;
}

/* FIELD */
.field {
  margin-bottom: 16px;
  text-align: left;
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
  transition: 0.2s;
}

.field input:focus {
  outline: none;
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(28,107,58,0.1);
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-auth:hover {
  background: var(--green-dark);
}

/* BACK LINK */
.auth-back {
  display: inline-block;
  margin-top: 18px;
  font-size: 14px;
  color: var(--green);
  text-decoration: none;
}

.auth-back:hover {
  text-decoration: underline;
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
        <div class="change-icon">
          <i class="bi bi-envelope-plus"></i>
        </div>

        <h1 class="auth-title">Change Email</h1>
        <p class="auth-subtitle">Update your email address to receive a new OTP for your registration.</p>

        <form action="<?= url('/change-registration-email') ?>" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
          
          <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; color: var(--danger); text-align: center; background: #fff5f5; padding: 10px; border-radius: 6px; border: 1px solid var(--danger);"><?= $error ?></div>
          <?php endif; ?>

          <div class="field">
            <label for="email">New Email Address</label>
            <input type="email" name="email" id="email" value="<?= e($current_email ?? '') ?>" placeholder="Enter your new email address" required/>
          </div>

          <button type="submit" class="btn-auth">
            Update & Send OTP
          </button>
        </form>

        <a href="<?= url('/register') ?>" class="auth-back">
          <i class="bi bi-arrow-left"></i> Back to Registration
        </a>

      </div>
    </div>
  </div>

  <div id="footer-placeholder"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
