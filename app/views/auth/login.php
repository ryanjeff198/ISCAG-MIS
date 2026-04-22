<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ISCAG Philippines – Log In</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    /* ─── VARIABLES ─── */
:root {
  --green: #1c6b3a;
  --green-dark: #134d28;
  --gray-sec: #f5f5f5;
  --border: #e2e2e2;
  --txt: #111;
  --txt-2: #4a4a4a;
  --danger: #d32f2f;
  --danger-bg: #ffebee;
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

/* LEFT SIDE (IMAGE) */
.auth-left {
  flex: 1;
  position: relative;
  background: var(--green-dark);
}

.auth-bg {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

/* RIGHT SIDE (FORM) */
.auth-right {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--gray-sec);
  padding: 40px;
}

/* FORM CARD */
.auth-card {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

/* TITLE */
.auth-title {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 24px;
  text-align: center;
}

/* FORM ELEMENTS */
.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 5px;
}

.form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 14px;
}

.form-control:focus {
  outline: none;
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(28,107,58,0.1);
}

/* BUTTON */
.btn-auth {
  background: var(--green);
  color: #fff;
  border: none;
  padding: 10px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}

.btn-auth:hover {
  background: var(--green-dark);
}

/* LINK */
.auth-link {
  color: var(--green);
  text-decoration: none;
  font-size: 14px;
}

.auth-link:hover {
  text-decoration: underline;
}

/* UTILITIES */
.mb-3 { margin-bottom: 16px; }
.text-center { text-align: center; }
.text-end { text-align: right; }
.w-100 { width: 100%; }

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

<!-- Header Placeholder -->
    <div id="header-placeholder"></div>

<!-- SPLIT -->
<div class="auth-split">
  <div class="auth-left">
    <img src="<?= asset('assets/ISCAG1.png') ?>" alt="ISCAG Philippines" class="auth-bg">
  </div>

  <!-- Log In form -->
  <div class="auth-right">
    <a href="<?= url('/') ?>" class="auth-home-link"><i class="bi bi-arrow-left"></i> Home</a>
    <div class="auth-card">

      <h1 class="auth-title">Log in</h1>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger" style="font-size: 13px; margin-bottom: 15px; color: var(--danger); background: var(--danger-bg); padding: 10px; border-radius: 6px; border: 1px solid var(--danger);"><?= $error ?></div>
      <?php endif; ?>

      <?php if (isset($_GET['verified'])): ?>
        <div class="alert alert-success" style="font-size: 13px; margin-bottom: 15px; color: var(--green);">Account verified! You can now log in.</div>
      <?php endif; ?>

      <form action="<?= url('/login') ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="juan@email.com" required/>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter your password" required/>
        </div>

      <div class="text-end mb-3">
        <a href="<?= url('/forgot-password') ?>" class="auth-link">Forgot password?</a>
      </div>

        <button type="submit" class="btn-auth w-100 mb-3">Sign In</button>
      </form>

      <p class="auth-footer-text" style="text-align: center; font-size: 14px;">
        Don't have an account? <a href="<?= url('/register') ?>" class="auth-link">Sign Up</a>
      </p>

    </div>
  </div>

</div>

<!-- Footer Placeholder -->
<div id="footer-placeholder"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>