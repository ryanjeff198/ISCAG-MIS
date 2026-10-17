<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Donation Acknowledgment (<?= htmlspecialchars($donation['donation_ref']) ?>)</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    body {
      background: #f4f6f5;
      color: #1f2e2a;
      padding: 40px 20px;
      font-family: 'Source Sans 3', sans-serif;
    }
    .ack-container {
      max-width: 720px;
      margin: 0 auto;
      background: white;
      border: 1px solid #d9e3de;
      border-radius: 16px;
      padding: 48px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      position: relative;
    }
    .ack-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #176b45;
      padding-bottom: 24px;
      margin-bottom: 28px;
    }
    .ack-brand {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .ack-title {
      font-family: 'Lora', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: #0f5c3a;
      line-height: 1.2;
    }
    .ack-sub {
      font-size: 0.85rem;
      color: #6f7f78;
      font-weight: 600;
    }
    .ack-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }
    .ack-field {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .ack-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #6f7f78;
    }
    .ack-value {
      font-size: 1.05rem;
      font-weight: 700;
      color: #1f2e2a;
    }
    .ack-value.highlight {
      font-size: 1.4rem;
      color: #176b45;
    }
    .ack-notice {
      background: #fafbfb;
      border-left: 4px solid #c79a2b;
      padding: 16px 20px;
      border-radius: 6px;
      font-size: 0.82rem;
      color: #555;
      line-height: 1.6;
      margin-bottom: 32px;
    }
    .ack-signatures {
      display: flex;
      justify-content: space-between;
      margin-top: 48px;
      padding-top: 24px;
    }
    .sig-block {
      text-align: center;
      width: 240px;
    }
    .sig-line {
      border-top: 1.5px solid #1f2e2a;
      margin-bottom: 6px;
      padding-top: 8px;
      font-weight: 700;
      font-size: 0.9rem;
    }
    .sig-role {
      font-size: 0.78rem;
      color: #6f7f78;
    }
    .no-print {
      text-align: center;
      margin-bottom: 24px;
    }
    @media print {
      body { background: white; padding: 0; }
      .ack-container { border: none; box-shadow: none; padding: 20px 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div class="no-print">
    <button onclick="window.print()" style="background:#176b45; color:white; border:none; padding:10px 24px; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.9rem; display:inline-flex; align-items:center; gap:8px;">
      <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
      Print / Save as PDF
    </button>
    <a href="<?= url('/admin/damayan/charity/donations') ?>" style="margin-left:12px; color:#176b45; text-decoration:none; font-weight:700; font-size:0.9rem;">Back to Donation Records</a>
  </div>

  <div class="ack-container">
    <div class="ack-header">
      <div class="ack-brand">
        <img src="<?= asset('assets/logo.jpg') ?>" style="width:56px; height:56px; border-radius:10px;" alt="ISCAG" />
        <div>
          <div class="ack-title">Islamic Studies, Call and Guidance</div>
          <div class="ack-sub">Charity Programs & Social Welfare Department • ISCAG MIS</div>
        </div>
      </div>
      <div style="text-align:right;">
        <div style="font-size:0.75rem; font-weight:700; color:#6f7f78; text-transform:uppercase;">Reference No.</div>
        <div style="font-family:monospace; font-size:1.1rem; font-weight:800; color:#0f5c3a;"><?= htmlspecialchars($donation['donation_ref']) ?></div>
      </div>
    </div>

    <div style="text-align:center; margin-bottom:28px;">
      <h2 style="font-family:'Lora', serif; font-size:1.35rem; color:#0f5c3a; margin-bottom:4px;">OFFICIAL DONATION ACKNOWLEDGMENT</h2>
      <div style="font-size:0.85rem; color:#6f7f78;">May Allah reward your generosity and bless your wealth — Jazakallahu Khairan</div>
    </div>

    <div class="ack-grid">
      <div class="ack-field">
        <span class="ack-label">Donor / Sponsor Full Name</span>
        <span class="ack-value"><?= htmlspecialchars($donation['donor_name']) ?></span>
      </div>

      <div class="ack-field">
        <span class="ack-label">Date Received</span>
        <span class="ack-value"><?= date('F d, Y', strtotime($donation['date_received'])) ?></span>
      </div>

      <div class="ack-field">
        <span class="ack-label">Donor Address / Location</span>
        <span class="ack-value"><?= htmlspecialchars($donation['donor_address'] ?: 'On Record') ?></span>
      </div>

      <div class="ack-field">
        <span class="ack-label">Payment Channel / Method</span>
        <span class="ack-value"><?= htmlspecialchars($donation['payment_method'] ?: 'Direct Handover') ?> <?= !empty($donation['payment_reference']) ? '(' . htmlspecialchars($donation['payment_reference']) . ')' : '' ?></span>
      </div>

      <div class="ack-field" style="grid-column: span 2;">
        <span class="ack-label">Contribution Description & Value</span>
        <?php if ($donation['donation_type'] === 'monetary'): ?>
          <span class="ack-value highlight">PHP <?= number_format($donation['amount'], 2) ?></span>
          <span style="font-size:0.85rem; color:#6f7f78;">(Monetary Charitable Contribution)</span>
        <?php else: ?>
          <span class="ack-value highlight"><?= htmlspecialchars($donation['quantity']) ?> Units</span>
          <span style="font-size:0.95rem; color:#1f2e2a; font-weight:600;"><?= htmlspecialchars($donation['item_description']) ?></span>
          <span style="font-size:0.85rem; color:#6f7f78;">(In-Kind Relief / Funeral Supplies)</span>
        <?php endif; ?>
      </div>

      <?php if (!empty($donation['remarks'])): ?>
        <div class="ack-field" style="grid-column: span 2;">
          <span class="ack-label">Designated Purpose / Remarks</span>
          <span class="ack-value" style="font-size:0.9rem; font-weight:500; font-style:italic;"><?= htmlspecialchars($donation['remarks']) ?></span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Mandatory Non-Tax Deductible Compliance Notice -->
    <div class="ack-notice">
      <strong>Important Compliance Notice:</strong> This document serves solely as an internal administrative acknowledgment issued by the ISCAG Charity Programs Module verifying the receipt and registration of the above contribution into the Islamic welfare inventory. <em>This is an internal acknowledgment and is NOT an official tax-deductible receipt for BIR tax exemption purposes.</em>
    </div>

    <div class="ack-signatures">
      <div class="sig-block">
        <div class="sig-line"><?= htmlspecialchars($donation['received_by'] ?: 'Authorized Staff') ?></div>
        <div class="sig-role">Received By (ISCAG Staff)</div>
      </div>

      <div class="sig-block">
        <div class="sig-line">Damayan & Da'wah Committee</div>
        <div class="sig-role">Charity Management Department</div>
      </div>
    </div>
  </div>

</body>
</html>
