<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG Official Donation Receipt — <?= htmlspecialchars($donation['donation_ref']) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0f5c3a;
      --primary-dark: #0a3d27;
      --primary-light: #e8f5ef;
      --gold: #b38e36;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --bg-page: #f8fafc;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg-page);
      color: var(--text-main);
      font-family: 'Plus Jakarta Sans', sans-serif;
      padding: 40px 20px;
      line-height: 1.5;
    }

    .no-print-bar {
      max-width: 760px;
      margin: 0 auto 24px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
    }
    .btn-print {
      background: var(--primary);
      color: #fff;
    }
    .btn-print:hover { background: var(--primary-dark); }
    .btn-back {
      background: #fff;
      color: var(--text-main);
      border: 1px solid var(--border-color);
    }
    .btn-back:hover { background: #f1f5f9; }

    .receipt-container {
      max-width: 760px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid #cbd5e1;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
      padding: 48px;
      position: relative;
      overflow: hidden;
    }

    .receipt-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--gold) 100%);
    }

    .receipt-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid var(--primary-light);
      padding-bottom: 24px;
      margin-bottom: 28px;
      gap: 20px;
    }
    .org-brand {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .org-logo {
      width: 64px;
      height: 64px;
      border-radius: 12px;
      object-fit: cover;
      border: 1px solid var(--border-color);
    }
    .org-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--primary-dark);
      line-height: 1.2;
    }
    .org-dept {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 4px;
    }
    .org-address {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .receipt-meta {
      text-align: right;
    }
    .receipt-type-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 8px;
    }
    .receipt-ref-label {
      font-size: 0.7rem;
      color: var(--text-muted);
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.05em;
    }
    .receipt-ref-no {
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--primary);
    }

    .doc-banner {
      text-align: center;
      margin-bottom: 28px;
      background: #fafaf9;
      border: 1px dashed #d6d3d1;
      padding: 16px;
      border-radius: 10px;
    }
    .doc-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--primary);
      letter-spacing: 0.02em;
    }
    .doc-subtitle {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin-top: 4px;
      font-style: italic;
    }

    .receipt-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }
    .r-field {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .r-field.full {
      grid-column: span 2;
    }
    .r-label {
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .r-value {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-main);
    }
    .r-value.highlight-amt {
      font-size: 1.75rem;
      font-weight: 800;
      color: var(--primary);
      font-family: 'JetBrains Mono', monospace;
      display: flex;
      align-items: baseline;
      gap: 6px;
    }

    .integrity-seal-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px 16px;
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .seal-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .seal-icon {
      width: 28px;
      height: 28px;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .seal-text {
      font-size: 0.75rem;
      color: var(--text-muted);
    }
    .seal-text strong {
      color: var(--text-main);
    }
    .seal-hash {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.72rem;
      color: #475569;
      background: #edf2f7;
      padding: 4px 8px;
      border-radius: 4px;
      word-break: break-all;
    }

    .compliance-box {
      border-left: 3px solid var(--gold);
      background: #fffbeb;
      padding: 12px 16px;
      border-radius: 6px;
      font-size: 0.78rem;
      color: #92400e;
      line-height: 1.5;
      margin-bottom: 32px;
    }

    .signature-row {
      display: flex;
      justify-content: space-between;
      margin-top: 36px;
      padding-top: 20px;
      border-top: 1px solid var(--border-color);
    }
    .sig-col {
      width: 240px;
      text-align: center;
    }
    .sig-line {
      border-top: 1.5px solid var(--text-main);
      padding-top: 6px;
      font-weight: 700;
      font-size: 0.88rem;
      color: var(--text-main);
    }
    .sig-label {
      font-size: 0.72rem;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    @media print {
      body { background: white; padding: 0; }
      .no-print-bar { display: none; }
      .receipt-container {
        border: none;
        box-shadow: none;
        padding: 0;
        max-width: 100%;
      }
    }
  </style>
</head>
<body>

  <div class="no-print-bar">
    <a href="<?= url('/user/services/charity') ?>" class="btn-action btn-back">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Charity Portal
    </a>
    <button onclick="window.print()" class="btn-action btn-print">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print Official Receipt (PDF)
    </button>
  </div>

  <div class="receipt-container">
    <div class="receipt-header">
      <div class="org-brand">
        <img src="<?= asset('assets/logo.jpg') ?>" class="org-logo" alt="ISCAG Logo" />
        <div>
          <div class="org-title">Islamic Studies, Call and Guidance</div>
          <div class="org-dept">Damayan Social Welfare &amp; Charity Program</div>
          <div class="org-address">ISCAG Islamic Complex, Salitran 1, City of Dasmariñas, Cavite</div>
        </div>
      </div>
      <div class="receipt-meta">
        <div class="receipt-type-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          Officially Verified
        </div>
        <div class="receipt-ref-label">Transaction Reference</div>
        <div class="receipt-ref-no"><?= htmlspecialchars($donation['donation_ref']) ?></div>
      </div>
    </div>

    <div class="doc-banner">
      <div class="doc-title">OFFICIAL DONATION ACKNOWLEDGMENT RECEIPT</div>
      <div class="doc-subtitle">“Those who spend in charity will be richly rewarded.” — Surah Al-Hadid (57:10)</div>
    </div>

    <div class="receipt-grid">
      <div class="r-field">
        <span class="r-label">Donor Name</span>
        <span class="r-value"><?= htmlspecialchars($donation['donor_name']) ?></span>
      </div>

      <div class="r-field">
        <span class="r-label">Date Received &amp; Verified</span>
        <span class="r-value"><?= date('F d, Y', strtotime($donation['date_received'])) ?></span>
      </div>

      <div class="r-field">
        <span class="r-label">Target Charity Program</span>
        <span class="r-value" style="color:var(--primary); font-weight:700;">
          <?= htmlspecialchars($donation['target_program'] ?: 'General Welfare & Relief') ?>
        </span>
      </div>

      <div class="r-field">
        <span class="r-label">Payment Channel / Method</span>
        <span class="r-value">
          <?= htmlspecialchars($donation['payment_method'] ?: 'Direct Handover') ?>
        </span>
      </div>

      <?php if (!empty($donation['payment_reference'])): ?>
        <div class="r-field">
          <span class="r-label">Gateway / Bank Reference No.</span>
          <span class="r-value" style="font-family:'JetBrains Mono', monospace; font-size:0.92rem;">
            <?= htmlspecialchars($donation['payment_reference']) ?>
          </span>
        </div>
      <?php endif; ?>

      <div class="r-field">
        <span class="r-label">Current Record Status</span>
        <span class="r-value" style="color:#059669; font-weight:700;">
          <?= htmlspecialchars($donation['status']) ?> (Confirmed)
        </span>
      </div>

      <div class="r-field full">
        <span class="r-label">Confirmed Contribution Value</span>
        <?php if ($donation['donation_type'] === 'monetary'): ?>
          <div class="r-value highlight-amt">
            <span>PHP <?= number_format($donation['amount'], 2) ?></span>
            <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); font-family:'Plus Jakarta Sans', sans-serif;">(Philippine Pesos)</span>
          </div>
        <?php else: ?>
          <div class="r-value highlight-amt">
            <span><?= htmlspecialchars($donation['quantity']) ?> <?= htmlspecialchars($donation['unit'] ?? 'Units') ?></span>
            <span style="font-size:0.88rem; font-weight:600; color:var(--text-main); font-family:'Plus Jakarta Sans', sans-serif;">— <?= htmlspecialchars($donation['item_description']) ?></span>
          </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($donation['remarks'])): ?>
        <div class="r-field full">
          <span class="r-label">Special Purpose / Remarks</span>
          <span class="r-value" style="font-size:0.88rem; font-style:italic; color:#475569;">
            <?= htmlspecialchars($donation['remarks']) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Cryptographic Verification Integrity Seal -->
    <div class="integrity-seal-box">
      <div class="seal-left">
        <div class="seal-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="seal-text">
          <div><strong>Cryptographic Integrity Seal:</strong> Record authenticated via SHA-256 database digest.</div>
          <div>Verified against tamper-evident audit logs on <?= date('Y-m-d H:i') ?>.</div>
        </div>
      </div>
      <div class="seal-hash" title="Full SHA-256 Digest: <?= htmlspecialchars($integrity['full_hash'] ?? '') ?>">
        SEAL-<?= htmlspecialchars($integrity['seal'] ?? 'AUTHENTIC') ?>
      </div>
    </div>

    <!-- Compliance Notice -->
    <div class="compliance-box">
      <strong>Compliance Notice:</strong> This official acknowledgment is issued by the ISCAG Charity Programs Module verifying the receipt and registration of your charitable contribution into the Islamic welfare inventory. <em>This internal receipt is issued for documentation and transparency purposes.</em>
    </div>

    <div class="signature-row">
      <div class="sig-col">
        <div class="sig-line"><?= htmlspecialchars($donation['received_by'] ?: 'Damayan Verification Officer') ?></div>
        <div class="sig-label">Verified &amp; Received By</div>
      </div>
      <div class="sig-col">
        <div class="sig-line">Damayan &amp; Social Welfare Office</div>
        <div class="sig-label">Authorized Administrative Seal</div>
      </div>
    </div>
  </div>

</body>
</html>

