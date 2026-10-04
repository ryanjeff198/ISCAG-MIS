<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Report a Death</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    :root {
      --primary: #1b5e20; --primary-dark: #0f4c18; --primary-light: #e8f5e9;
      --gold: #c9a84c; --text-main: #1f2937; --text-muted: #6b7280;
      --border: #e5e7eb; --danger: #dc2626; --bg: #f4f6f8;
      --success: #059669; --info: #2563eb; --radius: 12px;
    }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); margin: 0; }
    .app-wrapper { display: flex; min-height: 100vh; }
    .main-content { flex: 1; overflow-y: auto; background: var(--bg); }
    .page-wrapper { max-width: 860px; margin: 0 auto; padding: 32px 24px 60px; }

    /* ─── Header ─── */
    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-family: 'Lora', serif; font-size: 1.6rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 6px; }
    .page-header p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
    .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; margin-bottom: 16px; color: var(--text-muted); }
    .breadcrumb a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .breadcrumb .sep { color: #ccc; }

    /* ─── Step Indicator ─── */
    .step-bar { display: flex; gap: 0; margin-bottom: 32px; background: white; border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
    .step-item { flex: 1; padding: 14px 10px; text-align: center; font-size: 0.72rem; font-weight: 700; color: var(--text-muted); background: white; border-right: 1px solid var(--border); position: relative; transition: all 0.3s; cursor: default; letter-spacing: 0.02em; }
    .step-item:last-child { border-right: none; }
    .step-item .step-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: var(--border); color: var(--text-muted); font-size: 0.68rem; font-weight: 800; margin-bottom: 4px; }
    .step-item.active { color: var(--primary); background: var(--primary-light); }
    .step-item.active .step-num { background: var(--primary); color: white; }
    .step-item.completed { color: var(--success); background: #ecfdf5; }
    .step-item.completed .step-num { background: var(--success); color: white; }

    /* ─── Form Card ─── */
    .form-card { background: white; border-radius: var(--radius); border: 1px solid var(--border); padding: 32px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.03); }
    .form-card-title { font-family: 'Lora', serif; font-size: 1rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
    .form-card-subtitle { font-size: 0.82rem; color: var(--text-muted); margin: 0 0 24px; }
    .form-card-icon { width: 20px; height: 20px; fill: var(--primary); flex-shrink: 0; }
    .form-divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }

    /* ─── Form Elements ─── */
    .form-grid { display: grid; gap: 16px; margin-bottom: 16px; }
    .form-grid-2 { grid-template-columns: 1fr 1fr; }
    .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.78rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
    .form-group label .req { color: var(--danger); margin-left: 2px; }
    .form-group input, .form-group select, .form-group textarea {
      padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 0.88rem;
      font-family: inherit; color: var(--text-main); background: white; transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(27,94,32,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-group .field-error { display: none; font-size: 0.72rem; color: var(--danger); margin-top: 4px; font-weight: 600; }
    .form-group.has-error input, .form-group.has-error select, .form-group.has-error textarea { border-color: var(--danger); }
    .form-group.has-error .field-error { display: block; }

    /* ─── Buttons ─── */
    .form-actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; }
    .btn { padding: 11px 24px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; border: none; font-family: inherit; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; box-shadow: 0 4px 12px rgba(27,94,32,0.25); }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(27,94,32,0.35); }
    .btn-outline { background: white; color: var(--text-muted); border: 1.5px solid var(--border); }
    .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
    .btn-gold { background: linear-gradient(135deg, #D4AF37, #B8860B); color: #1a1a1a; box-shadow: 0 4px 12px rgba(212,175,55,0.3); }
    .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(212,175,55,0.4); }
    .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

    /* ─── Review Section ─── */
    .review-section { margin-bottom: 20px; }
    .review-section h4 { font-family: 'Lora', serif; font-size: 0.88rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 12px; padding-bottom: 8px; border-bottom: 2px solid var(--primary-light); }
    .review-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .review-item { padding: 8px 0; }
    .review-item .rl { font-size: 0.72rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 2px; }
    .review-item .rv { font-size: 0.88rem; font-weight: 600; color: var(--text-main); }

    /* ─── Success ─── */
    .success-card { text-align: center; padding: 48px 32px; }
    .success-icon { width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .success-icon svg { width: 36px; height: 36px; fill: var(--success); }
    .success-case { font-family: 'Lora', serif; font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 4px; }
    .success-status { display: inline-block; padding: 4px 14px; border-radius: 20px; background: var(--primary-light); color: var(--primary); font-size: 0.78rem; font-weight: 700; margin: 8px 0 20px; }

    /* ─── Notice Box ─── */
    .notice-box { padding: 14px 18px; border-radius: 8px; font-size: 0.82rem; line-height: 1.6; display: flex; gap: 10px; align-items: flex-start; margin-bottom: 16px; }
    .notice-box.info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .notice-box svg { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; }

    @media (max-width: 640px) {
      .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
      .review-grid { grid-template-columns: 1fr; }
      .step-bar { flex-wrap: wrap; }
      .step-item { flex: none; width: 33.33%; border-bottom: 1px solid var(--border); }
      .form-actions { flex-direction: column-reverse; }
      .btn { width: 100%; justify-content: center; }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/user/sidebar.php'; ?>
    <div class="main-content">
      <div class="page-wrapper">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
          <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
          <span class="sep">›</span>
          <a href="<?= url('/user/services/burial-dashboard') ?>">Damayan Services</a>
          <span class="sep">›</span>
          <span>Report a Death</span>
        </div>

        <!-- Header -->
        <div class="page-header">
          <h1>Report a Death</h1>
          <p>Submit a death report to begin the Islamic funeral services process. All information entered here will be reused throughout the case — no need to re-enter it later.</p>
        </div>

        <!-- Step Bar -->
        <div class="step-bar" id="step-bar">
          <div class="step-item active" data-step="1"><div class="step-num">1</div><br>Deceased</div>
          <div class="step-item" data-step="2"><div class="step-num">2</div><br>Family</div>
          <div class="step-item" data-step="3"><div class="step-num">3</div><br>Islamic Burial</div>
          <div class="step-item" data-step="4"><div class="step-num">4</div><br>Documents</div>
          <div class="step-item" data-step="5"><div class="step-num">5</div><br>Review</div>
          <div class="step-item" data-step="6"><div class="step-num">6</div><br>Submit</div>
        </div>

        <!-- STEP 1: Deceased Information -->
        <div class="form-step" id="step-1">
          <div class="form-card">
            <div class="form-card-title">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
              Deceased Information
            </div>
            <p class="form-card-subtitle">Enter the complete personal details of the deceased.</p>

            <div class="form-grid form-grid-3">
              <div class="form-group"><label>First Name <span class="req">*</span></label><input type="text" id="d-first-name" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Middle Name</label><input type="text" id="d-middle-name" /></div>
              <div class="form-group"><label>Last Name <span class="req">*</span></label><input type="text" id="d-last-name" required /><span class="field-error">Required</span></div>
            </div>
            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Haj Name <small style="color:var(--text-muted);font-weight:400;">(if applicable)</small></label><input type="text" id="d-haj-name" /></div>
              <div class="form-group">
                <label>Sex <span class="req">*</span></label>
                <select id="d-sex" required><option value="">— Select —</option><option value="Male">Male</option><option value="Female">Female</option></select>
                <span class="field-error">Required</span>
              </div>
            </div>

            <hr class="form-divider" />

            <div class="form-grid form-grid-3">
              <div class="form-group"><label>Date of Birth <span class="req">*</span></label><input type="date" id="d-dob" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Date of Death <span class="req">*</span></label><input type="date" id="d-dod" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Time of Death</label><input type="time" id="d-tod" /></div>
            </div>
            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Place of Death <span class="req">*</span></label><input type="text" id="d-place-of-death" placeholder="e.g. Hospital, Residence" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Occupation</label><input type="text" id="d-occupation" /></div>
            </div>
            <div class="form-group"><label>Address</label><textarea id="d-address" rows="2" placeholder="Complete address of the deceased"></textarea></div>

            <hr class="form-divider" />

            <div class="form-grid form-grid-3">
              <div class="form-group">
                <label>Civil Status</label>
                <select id="d-civil-status"><option value="">— Select —</option><option>Single</option><option>Married</option><option>Widowed</option><option>Divorced</option><option>Separated</option></select>
              </div>
              <div class="form-group"><label>Nationality</label><input type="text" id="d-nationality" value="Filipino" /></div>
              <div class="form-group"><label>Religion</label><input type="text" id="d-religion" value="Islam" /></div>
            </div>
          </div>
          <div class="form-actions"><div></div><button class="btn btn-primary" onclick="goStep(2)">Continue <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></button></div>
        </div>

        <!-- STEP 2: Family / Informant -->
        <div class="form-step" id="step-2" style="display:none;">
          <div class="form-card">
            <div class="form-card-title">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              Family / Informant Information
            </div>
            <p class="form-card-subtitle">Provide details of the person reporting the death.</p>

            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Name of Informant <span class="req">*</span></label><input type="text" id="i-name" value="<?= htmlspecialchars(trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''))) ?>" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Relationship to Deceased <span class="req">*</span></label>
                <select id="i-relationship" required><option value="">— Select —</option><option>Spouse</option><option>Parent</option><option>Child</option><option>Sibling</option><option>Grandparent</option><option>Grandchild</option><option>Other Relative</option><option>Guardian</option><option>Community Leader</option><option>Other</option></select>
                <span class="field-error">Required</span>
              </div>
            </div>
            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Contact Number <span class="req">*</span></label><input type="tel" id="i-contact" value="<?= htmlspecialchars($dbUser['phone_number'] ?? $dbUser['contactnum'] ?? '') ?>" required /><span class="field-error">Required</span></div>
              <div class="form-group"><label>Email Address</label><input type="email" id="i-email" value="<?= htmlspecialchars($dbUser['email'] ?? '') ?>" /></div>
            </div>
            <div class="form-group"><label>Complete Address</label><textarea id="i-address" rows="2" placeholder="Complete address of the informant"><?= htmlspecialchars($dbUser['address'] ?? '') ?></textarea></div>
          </div>
          <div class="form-actions">
            <button class="btn btn-outline" onclick="goStep(1)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Back</button>
            <button class="btn btn-primary" onclick="goStep(3)">Continue <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></button>
          </div>
        </div>

        <!-- STEP 3: Islamic Funeral Information -->
        <div class="form-step" id="step-3" style="display:none;">
          <div class="form-card">
            <div class="form-card-title">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Islamic Funeral &amp; Burial Information
            </div>
            <p class="form-card-subtitle">Enter details about the Islamic funeral rites and burial arrangements.</p>

            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Person Who Performed Burial Rites</label><input type="text" id="b-rites-person" /></div>
              <div class="form-group"><label>Imam / Religious Funeral Leader <small style="color:var(--text-muted);font-weight:400;">(if applicable)</small></label><input type="text" id="b-imam" /></div>
            </div>
            <div class="form-group"><label>Surviving Spouse(s)</label><textarea id="b-spouses" rows="2" placeholder="Enter name(s) of surviving spouse(s), separated by commas"></textarea></div>

            <hr class="form-divider" />
            <div class="form-card-title" style="font-size:0.9rem;margin-bottom:16px;">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/></svg>
              Burial Details
            </div>

            <div class="form-grid form-grid-3">
              <div class="form-group"><label>Burial Date</label><input type="date" id="b-date" /></div>
              <div class="form-group"><label>Burial Time</label><input type="time" id="b-time" /></div>
              <div class="form-group"><label>Grave/Burial Reference</label><input type="text" id="b-grave-ref" placeholder="e.g. Lot-A-12" /></div>
            </div>
            <div class="form-grid form-grid-2">
              <div class="form-group"><label>Burial Location</label><input type="text" id="b-location" placeholder="e.g. City, Province" /></div>
              <div class="form-group"><label>Cemetery</label><input type="text" id="b-cemetery" /></div>
            </div>
          </div>
          <div class="form-actions">
            <button class="btn btn-outline" onclick="goStep(2)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Back</button>
            <button class="btn btn-primary" onclick="goStep(4)">Continue <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></button>
          </div>
        </div>

        <!-- STEP 4: Documents -->
        <div class="form-step" id="step-4" style="display:none;">
          <div class="form-card">
            <div class="form-card-title">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
              Required Documents
            </div>
            <p class="form-card-subtitle">You may upload documents now or after submission from your case dashboard. Uploaded documents will be reviewed by Funeral Services Admin.</p>

            <div class="notice-box info">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              <div>Documents can be uploaded later from your funeral case dashboard. You may skip this step for now and proceed to review.</div>
            </div>

            <div id="doc-checklist" style="display:grid;gap:12px;margin-top:16px;">
              <div class="doc-item" data-doc="identification">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:#fafbfc;border:1.5px dashed var(--border);border-radius:8px;transition:border-color 0.2s;">
                  <div>
                    <div style="font-weight:700;font-size:0.88rem;">Identification Document of Informant</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">Valid ID of the person reporting the death</div>
                    <div class="doc-file-name" style="display:none;font-size:0.78rem;color:var(--success);font-weight:600;margin-top:4px;"></div>
                  </div>
                  <label class="btn btn-outline" style="padding:8px 14px;font-size:0.78rem;cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg> Upload
                    <input type="file" class="doc-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;" />
                  </label>
                </div>
              </div>
              <div class="doc-item" data-doc="death_support">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:#fafbfc;border:1.5px dashed var(--border);border-radius:8px;transition:border-color 0.2s;">
                  <div>
                    <div style="font-weight:700;font-size:0.88rem;">Supporting Death Documentation</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">Medical certificate, hospital record, etc.</div>
                    <div class="doc-file-name" style="display:none;font-size:0.78rem;color:var(--success);font-weight:600;margin-top:4px;"></div>
                  </div>
                  <label class="btn btn-outline" style="padding:8px 14px;font-size:0.78rem;cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg> Upload
                    <input type="file" class="doc-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;" />
                  </label>
                </div>
              </div>
              <div class="doc-item" data-doc="burial_doc">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:#fafbfc;border:1.5px dashed var(--border);border-radius:8px;transition:border-color 0.2s;">
                  <div>
                    <div style="font-weight:700;font-size:0.88rem;">Burial-Related Documentation</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">Burial permit, cemetery record, etc.</div>
                    <div class="doc-file-name" style="display:none;font-size:0.78rem;color:var(--success);font-weight:600;margin-top:4px;"></div>
                  </div>
                  <label class="btn btn-outline" style="padding:8px 14px;font-size:0.78rem;cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg> Upload
                    <input type="file" class="doc-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;" />
                  </label>
                </div>
              </div>
              <div class="doc-item" data-doc="other">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px;background:#fafbfc;border:1.5px dashed var(--border);border-radius:8px;transition:border-color 0.2s;">
                  <div>
                    <div style="font-weight:700;font-size:0.88rem;">Other Required Documents</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">Any other documents required by the responsible government office</div>
                    <div class="doc-file-name" style="display:none;font-size:0.78rem;color:var(--success);font-weight:600;margin-top:4px;"></div>
                  </div>
                  <label class="btn btn-outline" style="padding:8px 14px;font-size:0.78rem;cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg> Upload
                    <input type="file" class="doc-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:none;" />
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="form-actions">
            <button class="btn btn-outline" onclick="goStep(3)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Back</button>
            <button class="btn btn-primary" onclick="goStep(5)">Review Information <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></button>
          </div>
        </div>

        <!-- STEP 5: Review -->
        <div class="form-step" id="step-5" style="display:none;">
          <div class="form-card">
            <div class="form-card-title">
              <svg class="form-card-icon" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
              Review &amp; Confirmation
            </div>
            <p class="form-card-subtitle">Please review all information before submitting. Click "Edit Information" to make corrections.</p>

            <div class="notice-box info" style="margin-bottom:24px;">
              <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              <div>After submission, the information will be locked from accidental editing. A Funeral Case Number will be generated and the case will be sent to the Funeral Services Admin for review.</div>
            </div>

            <div id="review-content"></div>
          </div>
          <div class="form-actions">
            <button class="btn btn-outline" onclick="goStep(1)"><svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Edit Information</button>
            <button class="btn btn-gold" id="submit-btn" onclick="submitReport()">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
              Submit Death Report
            </button>
          </div>
        </div>

        <!-- STEP 6: Success -->
        <div class="form-step" id="step-6" style="display:none;">
          <div class="form-card success-card">
            <div class="success-icon"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
            <h2 style="font-family:'Lora',serif;font-size:1.2rem;font-weight:700;color:var(--text-main);margin:0 0 8px;">Death Report Successfully Submitted</h2>
            <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:700;margin-bottom:4px;">FUNERAL CASE</div>
            <div class="success-case" id="success-case-number">ISL-2026-000001</div>
            <div class="success-status">DEATH REPORT SUBMITTED</div>
            <p style="color:var(--text-muted);font-size:0.88rem;max-width:480px;margin:0 auto 24px;line-height:1.6;">Your information is now being reviewed by the Funeral Services Admin. You can track the progress of your case from your funeral case dashboard.</p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
              <a href="<?= url('/user/services/funeral/case') ?>" class="btn btn-primary" style="text-decoration:none;">View My Funeral Case</a>
              <a href="<?= url('/user/dashboard') ?>" class="btn btn-outline" style="text-decoration:none;">Back to Dashboard</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/user-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    let currentStep = 1;
    const totalSteps = 6;
    const pendingFiles = {}; // { docType: File }

    // Document upload file selection
    document.querySelectorAll('.doc-input').forEach(input => {
      input.addEventListener('change', function() {
        const item = this.closest('.doc-item');
        const docType = item.dataset.doc;
        const fileName = this.files[0]?.name;
        if (fileName) {
          pendingFiles[docType] = this.files[0];
          const nameEl = item.querySelector('.doc-file-name');
          nameEl.textContent = '✓ ' + fileName;
          nameEl.style.display = 'block';
          item.querySelector('div > div').style.borderColor = 'var(--success)';
        }
      });
    });

    function goStep(step) {
      // Validate current step before moving forward
      if (step > currentStep) {
        if (!validateStep(currentStep)) return;
      }

      // Update step bar
      document.querySelectorAll('.step-item').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.remove('active', 'completed');
        if (s < step) el.classList.add('completed');
        if (s === step) el.classList.add('active');
      });

      // Show/hide steps
      document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
      document.getElementById('step-' + step).style.display = 'block';

      // Build review if going to step 5
      if (step === 5) buildReview();

      currentStep = step;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step) {
      let valid = true;
      const fields = {
        1: ['d-first-name', 'd-last-name', 'd-sex', 'd-dob', 'd-dod', 'd-place-of-death'],
        2: ['i-name', 'i-relationship', 'i-contact'],
        3: [], // All optional
        4: []  // All optional
      };

      if (!fields[step]) return true;

      // Clear previous errors
      document.querySelectorAll('#step-' + step + ' .form-group').forEach(g => g.classList.remove('has-error'));

      fields[step].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.value.trim()) {
          el.closest('.form-group').classList.add('has-error');
          valid = false;
        }
      });

      if (!valid) {
        const firstError = document.querySelector('#step-' + step + ' .has-error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return valid;
    }

    function gv(id) { return (document.getElementById(id)?.value || '').trim(); }

    function buildReview() {
      const html = `
        <div class="review-section">
          <h4>Deceased Information</h4>
          <div class="review-grid">
            <div class="review-item"><div class="rl">Full Name</div><div class="rv">${gv('d-first-name')} ${gv('d-middle-name')} ${gv('d-last-name')}</div></div>
            <div class="review-item"><div class="rl">Haj Name</div><div class="rv">${gv('d-haj-name') || '—'}</div></div>
            <div class="review-item"><div class="rl">Sex</div><div class="rv">${gv('d-sex')}</div></div>
            <div class="review-item"><div class="rl">Date of Birth</div><div class="rv">${gv('d-dob')}</div></div>
            <div class="review-item"><div class="rl">Date of Death</div><div class="rv">${gv('d-dod')}</div></div>
            <div class="review-item"><div class="rl">Time of Death</div><div class="rv">${gv('d-tod') || '—'}</div></div>
            <div class="review-item"><div class="rl">Place of Death</div><div class="rv">${gv('d-place-of-death')}</div></div>
            <div class="review-item"><div class="rl">Occupation</div><div class="rv">${gv('d-occupation') || '—'}</div></div>
            <div class="review-item"><div class="rl">Address</div><div class="rv">${gv('d-address') || '—'}</div></div>
            <div class="review-item"><div class="rl">Civil Status</div><div class="rv">${gv('d-civil-status') || '—'}</div></div>
            <div class="review-item"><div class="rl">Nationality</div><div class="rv">${gv('d-nationality')}</div></div>
            <div class="review-item"><div class="rl">Religion</div><div class="rv">${gv('d-religion')}</div></div>
          </div>
        </div>
        <div class="review-section">
          <h4>Family / Informant Information</h4>
          <div class="review-grid">
            <div class="review-item"><div class="rl">Name of Informant</div><div class="rv">${gv('i-name')}</div></div>
            <div class="review-item"><div class="rl">Relationship</div><div class="rv">${gv('i-relationship')}</div></div>
            <div class="review-item"><div class="rl">Contact Number</div><div class="rv">${gv('i-contact')}</div></div>
            <div class="review-item"><div class="rl">Email</div><div class="rv">${gv('i-email') || '—'}</div></div>
            <div class="review-item" style="grid-column:1/-1;"><div class="rl">Address</div><div class="rv">${gv('i-address') || '—'}</div></div>
          </div>
        </div>
        <div class="review-section">
          <h4>Islamic Funeral &amp; Burial Information</h4>
          <div class="review-grid">
            <div class="review-item"><div class="rl">Burial Rites Person</div><div class="rv">${gv('b-rites-person') || '—'}</div></div>
            <div class="review-item"><div class="rl">Imam / Leader</div><div class="rv">${gv('b-imam') || '—'}</div></div>
            <div class="review-item" style="grid-column:1/-1;"><div class="rl">Surviving Spouse(s)</div><div class="rv">${gv('b-spouses') || '—'}</div></div>
            <div class="review-item"><div class="rl">Burial Date</div><div class="rv">${gv('b-date') || '—'}</div></div>
            <div class="review-item"><div class="rl">Burial Time</div><div class="rv">${gv('b-time') || '—'}</div></div>
            <div class="review-item"><div class="rl">Burial Location</div><div class="rv">${gv('b-location') || '—'}</div></div>
            <div class="review-item"><div class="rl">Cemetery</div><div class="rv">${gv('b-cemetery') || '—'}</div></div>
            <div class="review-item"><div class="rl">Grave Reference</div><div class="rv">${gv('b-grave-ref') || '—'}</div></div>
          </div>
        </div>
        <div class="review-section">
          <h4>Documents</h4>
          <div style="display:grid;gap:8px;">
            ${['identification','death_support','burial_doc','other'].map(t => {
              const f = pendingFiles[t];
              return `<div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#fafbfc;border-radius:6px;border:1px solid var(--border);">
                <span style="width:8px;height:8px;border-radius:50%;background:${f ? 'var(--success)' : 'var(--border)'};flex-shrink:0;"></span>
                <span style="font-size:0.82rem;font-weight:600;">${t.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase())}</span>
                <span style="margin-left:auto;font-size:0.75rem;color:${f ? 'var(--success)' : 'var(--text-muted)'};font-weight:600;">${f ? f.name : 'Not uploaded'}</span>
              </div>`;
            }).join('')}
          </div>
        </div>
      `;
      document.getElementById('review-content').innerHTML = html;
    }

    async function submitReport() {
      const btn = document.getElementById('submit-btn');
      btn.disabled = true;
      btn.innerHTML = '<svg style="width:16px;height:16px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg> Submitting...';

      const payload = {
        deceased_first_name: gv('d-first-name'),
        deceased_middle_name: gv('d-middle-name'),
        deceased_last_name: gv('d-last-name'),
        deceased_haj_name: gv('d-haj-name'),
        deceased_sex: gv('d-sex'),
        deceased_dob: gv('d-dob') || null,
        deceased_dod: gv('d-dod') || null,
        deceased_tod: gv('d-tod') || null,
        deceased_place_of_death: gv('d-place-of-death'),
        deceased_address: gv('d-address'),
        deceased_civil_status: gv('d-civil-status'),
        deceased_nationality: gv('d-nationality'),
        deceased_religion: gv('d-religion'),
        deceased_occupation: gv('d-occupation'),
        informant_name: gv('i-name'),
        informant_relationship: gv('i-relationship'),
        informant_contact: gv('i-contact'),
        informant_email: gv('i-email'),
        informant_address: gv('i-address'),
        burial_rites_person: gv('b-rites-person'),
        imam_name: gv('b-imam'),
        surviving_spouses: gv('b-spouses'),
        burial_date: gv('b-date') || null,
        burial_time: gv('b-time') || null,
        burial_location: gv('b-location'),
        cemetery: gv('b-cemetery'),
        grave_reference: gv('b-grave-ref')
      };

      try {
        const res = await fetch('<?= url('/user/services/funeral/submit') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
          document.getElementById('success-case-number').textContent = data.case_number;

          // Upload any pending documents
          for (const [docType, file] of Object.entries(pendingFiles)) {
            try {
              // We need to get the doc IDs from the server but we'll use case_id approach
              // Documents are uploaded after case creation via the case dashboard
            } catch (e) { console.error('Doc upload error:', e); }
          }

          goStep(6);
        } else {
          alert('Error: ' + (data.error || 'Failed to submit. Please try again.'));
          btn.disabled = false;
          btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg> Submit Death Report';
        }
      } catch (err) {
        alert('Network error. Please check your connection and try again.');
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg> Submit Death Report';
      }
    }

    // Spin animation
    const styleSheet = document.createElement('style');
    styleSheet.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(styleSheet);
  </script>
</body>
</html>
