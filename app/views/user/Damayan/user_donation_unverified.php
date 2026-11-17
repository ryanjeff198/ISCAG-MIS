<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Receipt Unavailable — ISCAG MIS</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f8fafc;
      color: #1e293b;
      font-family: 'Plus Jakarta Sans', sans-serif;
      padding: 60px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
    }
    .card {
      max-width: 540px;
      width: 100%;
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 36px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
      text-align: center;
    }
    .icon-wrap {
      width: 64px;
      height: 64px;
      background: #fff7ed;
      color: #ea580c;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px auto;
    }
    h2 {
      font-size: 1.35rem;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 10px;
    }
    p {
      font-size: 0.9rem;
      color: #64748b;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .details {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 16px;
      margin-bottom: 24px;
      text-align: left;
      font-size: 0.85rem;
    }
    .d-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      border-bottom: 1px solid #edf2f7;
    }
    .d-row:last-child { border-bottom: none; }
    .badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 700;
      background: #fed7aa;
      color: #9a3412;
    }
    .btn-return {
      display: inline-block;
      background: #0f5c3a;
      color: white;
      text-decoration: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.9rem;
      transition: background 0.2s;
    }
    .btn-return:hover { background: #0a3d27; }
  </style>
</head>
<body>

  <div class="card">
    <div class="icon-wrap">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h2>Official Receipt Unavailable</h2>
    <p>
      In accordance with strict database security and audit standards, Official Donation Receipts are generated <strong>only for successfully verified transactions</strong>.
    </p>

    <div class="details">
      <div class="d-row">
        <span style="color:#64748b;">Transaction Reference:</span>
        <strong style="font-family:monospace;"><?= htmlspecialchars($donation['donation_ref']) ?></strong>
      </div>
      <div class="d-row">
        <span style="color:#64748b;">Current Status:</span>
        <span class="badge"><?= htmlspecialchars($donation['status']) ?></span>
      </div>
      <div class="d-row">
        <span style="color:#64748b;">Target Program:</span>
        <strong><?= htmlspecialchars($donation['target_program'] ?: 'General Welfare & Relief') ?></strong>
      </div>
      <?php if (!empty($donation['admin_notes'])): ?>
        <div class="d-row">
          <span style="color:#64748b;">Staff Notes:</span>
          <span><?= htmlspecialchars($donation['admin_notes']) ?></span>
        </div>
      <?php endif; ?>
    </div>

    <p style="font-size:0.82rem;">
      If your transaction is currently pending, our Damayan staff is actively verifying the payment. Once verified, your Official Donation Receipt will become available immediately.
    </p>

    <a href="<?= url('/user/services/charity') ?>" class="btn-return">Return to Donation History</a>
  </div>

</body>
</html>

