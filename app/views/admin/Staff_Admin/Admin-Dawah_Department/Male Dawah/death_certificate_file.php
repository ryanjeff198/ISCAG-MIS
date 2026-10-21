<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Official Certificate of Death — <?= htmlspecialchars($record['registration_number']) ?> (<?= htmlspecialchars($record['deceased_name']) ?>)</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #475569;
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      color: #000;
      padding: 20px 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      -webkit-font-smoothing: antialiased;
    }

    /* Screen Toolbar */
    .screen-toolbar {
      width: 100%;
      max-width: 8.5in;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn-tool {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 0.85rem;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
      border: 1px solid transparent;
    }
    .btn-tool.back {
      background: #ffffff;
      color: #1e293b;
      border-color: #cbd5e1;
    }
    .btn-tool.back:hover { background: #f1f5f9; }
    .btn-tool.print {
      background: #15803d;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .btn-tool.print:hover { background: #166534; }
    .btn-tool.download {
      background: #0369a1;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .btn-tool.download:hover { background: #075985; }

    /* Standard Letter Page: 8.5 in x 11 in */
    .sheet-wrapper {
      width: 8.5in;
      min-height: 11in;
      background: #ffffff;
      padding: 0.28in 0.35in;
      box-shadow: 0 10px 25px rgba(0,0,0,0.35);
      position: relative;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Subtle background watermark */
    .watermark-seal {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 4.8in;
      height: 4.8in;
      opacity: 0.045;
      pointer-events: none;
      z-index: 0;
      background-image: url('<?= asset('assets/logo.jpg') ?>');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center;
    }

    .form-container {
      position: relative;
      z-index: 1;
      border: 2px solid #000;
      padding: 3px;
    }

    /* Top Government Header */
    .form-header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2px;
    }
    .form-header-table td {
      vertical-align: middle;
    }
    .top-meta-left {
      font-size: 8pt;
      font-weight: 700;
      line-height: 1.2;
    }
    .top-meta-center {
      text-align: center;
    }
    .ph-title-1 {
      font-size: 8.5pt;
      letter-spacing: 0.05em;
      font-weight: 600;
      text-transform: uppercase;
    }
    .ph-title-2 {
      font-size: 9pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }
    .ph-title-main {
      font-family: "Times New Roman", Times, serif;
      font-size: 14pt;
      font-weight: 900;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      margin: 2px 0 1px;
    }
    .ph-title-sub {
      font-size: 7.5pt;
      font-style: italic;
    }
    .registry-box {
      border: 1.5px solid #000;
      padding: 4px 6px;
      font-size: 8pt;
      line-height: 1.3;
      background: #fff;
    }
    .registry-box .reg-num {
      font-size: 9.5pt;
      font-weight: 900;
      color: #000;
      letter-spacing: 0.05em;
    }

    /* Standard Philippine Grid Box */
    .dc-table {
      width: 100%;
      border-collapse: collapse;
      border: 1px solid #000;
      font-size: 8pt;
    }
    .dc-table th, .dc-table td {
      border: 1px solid #000;
      padding: 2.5px 4px;
      vertical-align: top;
    }
    .box-num {
      font-weight: 800;
      font-size: 7.5pt;
      display: inline-block;
      margin-right: 3px;
    }
    .box-label {
      font-size: 6.8pt;
      text-transform: uppercase;
      font-weight: 700;
      color: #111;
      display: block;
      margin-bottom: 2px;
      letter-spacing: 0.02em;
    }
    .box-val {
      font-size: 8.8pt;
      font-weight: 700;
      color: #000;
      min-height: 14px;
    }
    .box-val-lg {
      font-size: 10pt;
      font-weight: 800;
      color: #000;
    }
    .sec-header {
      background: #e2e8f0;
      font-weight: 800;
      font-size: 7.5pt;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 2px 5px !important;
      text-align: left;
    }

    /* Checkbox representation */
    .chk-item {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      margin-right: 12px;
      font-size: 8pt;
    }
    .chk-box {
      display: inline-block;
      width: 11px;
      height: 11px;
      border: 1.2px solid #000;
      text-align: center;
      line-height: 10px;
      font-size: 9pt;
      font-weight: 900;
    }
    .chk-box.checked {
      background: #000;
      color: #fff;
    }

    /* Signatures table */
    .sig-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: -1px;
    }
    .sig-table td {
      border: 1px solid #000;
      padding: 3px 5px;
      vertical-align: top;
      font-size: 7.5pt;
    }
    .sig-line {
      border-bottom: 1px solid #000;
      width: 85%;
      margin: 22px auto 3px;
      text-align: center;
    }
    .sig-name {
      font-weight: 800;
      font-size: 8pt;
      text-align: center;
      text-transform: uppercase;
    }
    .sig-title {
      font-size: 6.8pt;
      text-align: center;
      text-transform: uppercase;
      color: #333;
    }

    /* ISCAG Male Da'wah Endorsement Section */
    .islamic-box {
      border: 1.5px solid #14532d;
      background: #f0fdf4;
      margin-top: 4px;
      padding: 5px 8px;
      font-size: 7.5pt;
      line-height: 1.35;
    }
    .islamic-box-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #bbf7d0;
      padding-bottom: 3px;
      margin-bottom: 4px;
    }
    .islamic-title {
      font-weight: 800;
      font-size: 8pt;
      color: #14532d;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .islamic-badge {
      font-size: 7pt;
      font-weight: 700;
      padding: 1px 6px;
      border-radius: 4px;
      background: #dcfce7;
      color: #166534;
      border: 1px solid #86efac;
    }

    /* Print Setup */
    @media print {
      @page {
        size: letter portrait;
        margin: 0.25in 0.3in 0.25in 0.3in;
      }
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }
      .screen-toolbar {
        display: none !important;
      }
      .sheet-wrapper {
        width: 100% !important;
        min-height: auto !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .form-container {
        border-color: #000 !important;
      }
      .sec-header {
        background: #e2e8f0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .islamic-box {
        background: #f0fdf4 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .chk-box.checked {
        background: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Toolbar -->
  <div class="screen-toolbar">
    <div style="display:flex; align-items:center; gap:8px;">
      <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="btn-tool back">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Back to Received Certificates
      </a>
      <a href="<?= url('/admin/dawah/male') ?>" class="btn-tool back">
        Da'wah Dashboard
      </a>
      <?php if (!empty($record['file_path'])): ?>
        <a href="<?= asset($record['file_path']) ?>" target="_blank" class="btn-tool back">
          View Original Upload
        </a>
      <?php endif; ?>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <a href="<?= url('/admin/dawah/death-certificates/download?id=' . $record['id']) ?>" class="btn-tool download">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> Download Official File
      </a>
      <button type="button" class="btn-tool print" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Letter Size (Form 103)
      </button>
    </div>
  </div>

  <!-- Standard Philippine Letter Sheet (8.5 x 11 inches) -->
  <div class="sheet-wrapper">
    <div class="watermark-seal"></div>

    <div class="form-container">
      
      <!-- 1. Header: Philippine Municipal Form 103 -->
      <table class="form-header-table">
        <tr>
          <td style="width:28%;" class="top-meta-left">
            <div>Municipal Form No. 103</div>
            <div style="font-size:7pt; font-weight:normal; font-style:italic;">(Revised August 2016)</div>
            <div style="font-size:6.8pt; color:#475569; margin-top:2px;">(To be accomplished in quadruplicate)</div>
          </td>
          <td style="width:44%;" class="top-meta-center">
            <div class="ph-title-1">Republic of the Philippines</div>
            <div class="ph-title-2">Office of the Civil Registrar General</div>
            <div class="ph-title-main">Certificate of Death</div>
            <div class="ph-title-sub">Philippine Statistics Authority &bull; Islamic Community Civil Registry</div>
          </td>
          <td style="width:28%; text-align:right;">
            <div class="registry-box" style="display:inline-block; text-align:left;">
              <div><strong>Province:</strong> <?= htmlspecialchars($record['province'] ?? 'CAVITE') ?></div>
              <div><strong>City / Municipality:</strong> <?= htmlspecialchars($record['city_municipality'] ?? 'CITY OF DASMARIÑAS') ?></div>
              <div><strong>Registry No.:</strong> <span class="reg-num">#<?= htmlspecialchars($record['registration_number']) ?></span></div>
            </div>
          </td>
        </tr>
      </table>

      <!-- 2. Main Boxed Table Form (1 to 21) -->
      <table class="dc-table">
        
        <!-- ROW 1: NAME (First, Middle, Last) -->
        <tr>
          <td colspan="4" style="width:70%;">
            <span class="box-num">1.</span><span class="box-label">NAME OF DECEASED (First, Middle, Last)</span>
            <div class="box-val-lg" style="letter-spacing:0.04em;">
              <?= strtoupper(htmlspecialchars($record['deceased_name'])) ?>
            </div>
          </td>
          <td colspan="2" style="width:30%;">
            <span class="box-num">2.</span><span class="box-label">SEX</span>
            <div style="margin-top:2px;">
              <span class="chk-item">
                <span class="chk-box <?= strtolower($record['sex'] ?? 'male') === 'male' ? 'checked' : '' ?>"><?= strtolower($record['sex'] ?? 'male') === 'male' ? '✓' : '' ?></span> 1. Male
              </span>
              <span class="chk-item">
                <span class="chk-box <?= strtolower($record['sex'] ?? '') === 'female' ? 'checked' : '' ?>"><?= strtolower($record['sex'] ?? '') === 'female' ? '✓' : '' ?></span> 2. Female
              </span>
            </div>
          </td>
        </tr>

        <!-- ROW 2: DATE OF DEATH & DATE OF BIRTH -->
        <tr>
          <td colspan="2" style="width:35%;">
            <span class="box-num">3.</span><span class="box-label">DATE OF DEATH</span>
            <div class="box-val">
              <?= date('F d, Y', strtotime($record['date_of_death'])) ?>
              <?php if (!empty($record['time_of_death'])): ?>
                &nbsp;&bull;&nbsp; <?= date('g:i A', strtotime($record['time_of_death'])) ?>
              <?php endif; ?>
            </div>
          </td>
          <td colspan="2" style="width:35%;">
            <span class="box-num">4.</span><span class="box-label">DATE OF BIRTH</span>
            <div class="box-val">
              <?= !empty($record['date_of_birth']) ? date('F d, Y', strtotime($record['date_of_birth'])) : 'Not Indicated' ?>
            </div>
          </td>
          <td colspan="2" style="width:30%;">
            <span class="box-num">5.</span><span class="box-label">AGE AT TIME OF DEATH</span>
            <div class="box-val">
              <?= !empty($record['age']) ? htmlspecialchars($record['age']) . ' years old' : 'Unspecified' ?>
            </div>
          </td>
        </tr>

        <!-- ROW 3: PLACE OF DEATH -->
        <tr>
          <td colspan="6">
            <span class="box-num">6.</span><span class="box-label">PLACE OF DEATH (Hospital / Clinic / Institution / House No., St., Barangay, City/Municipality, Province)</span>
            <div class="box-val">
              <?= htmlspecialchars($record['place_of_death'] ?? 'City of Dasmariñas, Cavite') ?>
            </div>
          </td>
        </tr>

        <!-- ROW 4: CIVIL STATUS, RELIGION, CITIZENSHIP -->
        <tr>
          <td colspan="2" style="width:35%;">
            <span class="box-num">7.</span><span class="box-label">CIVIL STATUS</span>
            <div style="font-size:7.5pt; margin-top:1px;">
              <?php 
                $cs = strtolower($record['civil_status'] ?? 'single'); 
              ?>
              <span class="chk-item"><span class="chk-box <?= $cs === 'single' ? 'checked' : '' ?>"><?= $cs === 'single' ? '✓' : '' ?></span> Single</span>
              <span class="chk-item"><span class="chk-box <?= $cs === 'married' ? 'checked' : '' ?>"><?= $cs === 'married' ? '✓' : '' ?></span> Married</span>
              <span class="chk-item"><span class="chk-box <?= in_array($cs, ['widow', 'widowed']) ? 'checked' : '' ?>"><?= in_array($cs, ['widow', 'widowed']) ? '✓' : '' ?></span> Widowed</span>
            </div>
          </td>
          <td colspan="2" style="width:35%;">
            <span class="box-num">8.</span><span class="box-label">RELIGION / RELIGIOUS AFFILIATION</span>
            <div class="box-val">
              <?= strtoupper(htmlspecialchars($record['religion'] ?? 'ISLAM')) ?>
            </div>
          </td>
          <td colspan="2" style="width:30%;">
            <span class="box-num">9.</span><span class="box-label">CITIZENSHIP</span>
            <div class="box-val">
              <?= strtoupper(htmlspecialchars($record['citizenship'] ?? 'FILIPINO')) ?>
            </div>
          </td>
        </tr>

        <!-- ROW 5: RESIDENCE & OCCUPATION -->
        <tr>
          <td colspan="4" style="width:70%;">
            <span class="box-num">10.</span><span class="box-label">RESIDENCE (House No., Street, Barangay, City/Municipality, Province)</span>
            <div class="box-val">
              <?= htmlspecialchars($record['residence'] ?? 'City of Dasmariñas, Cavite') ?>
            </div>
          </td>
          <td colspan="2" style="width:30%;">
            <span class="box-num">11.</span><span class="box-label">OCCUPATION</span>
            <div class="box-val">
              <?= htmlspecialchars($record['occupation'] ?? '—') ?>
            </div>
          </td>
        </tr>

        <!-- ROW 6: PARENTS -->
        <tr>
          <td colspan="3" style="width:50%;">
            <span class="box-num">12.</span><span class="box-label">NAME OF FATHER (First, Middle, Last)</span>
            <div class="box-val">
              <?= !empty($record['father_name']) ? htmlspecialchars($record['father_name']) : '—' ?>
            </div>
          </td>
          <td colspan="3" style="width:50%;">
            <span class="box-num">13.</span><span class="box-label">MAIDEN NAME OF MOTHER (First, Middle, Last)</span>
            <div class="box-val">
              <?= !empty($record['mother_name']) ? htmlspecialchars($record['mother_name']) : '—' ?>
            </div>
          </td>
        </tr>

        <!-- MEDICAL CERTIFICATE HEADER -->
        <tr>
          <td colspan="6" class="sec-header">
            MEDICAL CERTIFICATE &bull; CAUSES OF DEATH &bull; (For Civil Registry &amp; Religious Endorsement)
          </td>
        </tr>

        <!-- ROW 7: CAUSES OF DEATH -->
        <tr>
          <td colspan="4" style="width:70%;">
            <span class="box-num">14.</span><span class="box-label">CAUSES OF DEATH</span>
            <div style="font-size:7.5pt; padding-left:4px; line-height:1.35;">
              <div><strong>I. Immediate cause:</strong> <?= !empty($record['cause_of_death']) ? htmlspecialchars($record['cause_of_death']) : 'Cardiopulmonary Arrest / Natural Causes' ?></div>
              <div><strong>Antecedent cause:</strong> Severe Multi-Organ Stress / Natural Complications</div>
              <div><strong>Underlying cause:</strong> Natural Health Deterioration &bull; Certified by Attending Health Officer</div>
              <div style="margin-top:2px;"><strong>II. Other significant conditions:</strong> Certified for Islamic Burial Rites</div>
            </div>
          </td>
          <td colspan="2" style="width:30%;">
            <span class="box-label">MANNER OF DEATH</span>
            <div style="font-size:7.5pt; line-height:1.4;">
              <div><span class="chk-box checked">✓</span> 1. Natural</div>
              <div><span class="chk-box"></span> 2. Accident</div>
              <div><span class="chk-box"></span> 3. Others / Pending</div>
            </div>
          </td>
        </tr>

        <!-- ROW 8: ATTENDANT & CORPSE DISPOSAL -->
        <tr>
          <td colspan="3" style="width:50%;">
            <span class="box-num">15.</span><span class="box-label">ATTENDANT &amp; CERTIFICATION OF DEATH</span>
            <div style="font-size:7.5pt;">
              <span class="chk-item"><span class="chk-box checked">✓</span> Physician</span>
              <span class="chk-item"><span class="chk-box"></span> Health Officer</span>
              <span class="chk-item"><span class="chk-box"></span> Hospital Auth.</span>
            </div>
            <div style="font-size:6.8pt; color:#374151; margin-top:2px;">
              "I hereby certify that I attended/examined the deceased and death occurred on the date, time, and causes stated above."
            </div>
          </td>
          <td colspan="3" style="width:50%;">
            <span class="box-num">16.</span><span class="box-label">CORPSE DISPOSAL &amp; CEMETERY NAME</span>
            <div style="font-size:7.5pt;">
              <span class="chk-item"><span class="chk-box checked">✓</span> 1. Burial</span>
              <span class="chk-item"><span class="chk-box"></span> 2. Cremation</span>
              <span class="chk-item"><span class="chk-box"></span> 3. Others</span>
            </div>
            <div style="font-size:7.8pt; font-weight:700; color:#14532d; margin-top:2px;">
              Cemetery: <?= htmlspecialchars($record['cemetery_name'] ?? 'ISCAG Muslim Memorial Cemetery, Dasmariñas, Cavite') ?>
            </div>
          </td>
        </tr>

        <!-- ROW 9: INFORMANT -->
        <tr>
          <td colspan="6">
            <span class="box-num">17.</span><span class="box-label">INFORMANT / REPRESENTATIVE</span>
            <div style="display:flex; justify-content:space-between; font-size:8pt; padding:1px 0;">
              <div><strong>Name:</strong> <?= htmlspecialchars($record['informant_name'] ?? 'Family Representative') ?></div>
              <div><strong>Relationship:</strong> <?= htmlspecialchars($record['informant_relationship'] ?? 'Immediate Family') ?></div>
              <div><strong>Contact:</strong> <?= htmlspecialchars($record['informant_contact'] ?? '—') ?></div>
              <div><strong>Date:</strong> <?= !empty($record['death_cert_date']) ? date('M d, Y', strtotime($record['death_cert_date'])) : date('M d, Y') ?></div>
            </div>
          </td>
        </tr>

      </table>

      <!-- 3. Signatures Table: Boxes 18, 19, 20 -->
      <table class="sig-table">
        <tr>
          <!-- 18. PREPARED BY (Damayan Civil Registry) -->
          <td style="width:33.33%;">
            <span class="box-num">18.</span><span class="box-label">PREPARED BY (Damayan Dept)</span>
            <div class="sig-line"></div>
            <div class="sig-name"><?= htmlspecialchars($record['sent_by_name'] ?? 'Damayan Coordinator') ?></div>
            <div class="sig-title">Damayan Civil Registry Officer</div>
            <div style="text-align:center; font-size:6.8pt; color:#475569; margin-top:2px;">
              Transmitted: <?= date('M d, Y &bull; g:i A', strtotime($record['sent_at'])) ?>
            </div>
          </td>

          <!-- 19. RECEIVED BY (Male Da'wah Dept) -->
          <td style="width:33.33%;">
            <span class="box-num">19.</span><span class="box-label">RECEIVED BY (Male Da'wah Dept)</span>
            <div class="sig-line"></div>
            <div class="sig-name"><?= htmlspecialchars($record['acknowledged_by_name'] ?? 'Male Da\'wah Officiating Officer') ?></div>
            <div class="sig-title">Male Da'wah Religious Officer</div>
            <div style="text-align:center; font-size:6.8pt; color:#475569; margin-top:2px;">
              <?= !empty($record['acknowledged_at']) ? 'Acknowledged: ' . date('M d, Y &bull; g:i A', strtotime($record['acknowledged_at'])) : 'Status: ' . htmlspecialchars($record['status']) ?>
            </div>
          </td>

          <!-- 20. REGISTERED AT CIVIL REGISTRAR / ISCAG ARCHIVE -->
          <td style="width:33.34%;">
            <span class="box-num">20.</span><span class="box-label">OFFICE OF THE CIVIL REGISTRAR</span>
            <div class="sig-line"></div>
            <div class="sig-name">LOCAL CIVIL REGISTRAR / ISCAG</div>
            <div class="sig-title">City of Dasmariñas &bull; ISCAG MIS Archive</div>
            <div style="text-align:center; font-size:6.8pt; color:#475569; margin-top:2px;">
              Reg Date: <?= !empty($record['death_cert_date']) ? date('M d, Y', strtotime($record['death_cert_date'])) : date('M d, Y') ?>
            </div>
          </td>
        </tr>
      </table>

      <!-- 4. Official ISCAG Male Da'wah Islamic Burial & Janazah Endorsement -->
      <div class="islamic-box">
        <div class="islamic-box-header">
          <div style="display:flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#14532d;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            <span class="islamic-title">Male Da'wah Department &bull; Islamic Funeral (Janazah) Endorsement</span>
          </div>
          <span class="islamic-badge">Status: <?= strtoupper(htmlspecialchars($record['status'])) ?></span>
        </div>
        <div style="color:#0f3d21;">
          <strong>Islamic Shari'ah Certification:</strong> This certifies that the death certificate and vital statistics of the deceased <strong><?= strtoupper(htmlspecialchars($record['deceased_name'])) ?></strong> (Registry No. <strong>#<?= htmlspecialchars($record['registration_number']) ?></strong>) have been officially transmitted by the Damayan Department and received by the <strong>Male Da'wah Department</strong> for Salatul Janazah (Islamic Funeral Prayer), Ghusl, Kafan, and coordination with the Islamic cemetery.
        </div>
        <?php if (!empty($record['transmission_notes'])): ?>
          <div style="margin-top:3px; padding-top:3px; border-top:1px dashed #bbf7d0; font-size:7pt; color:#14532d;">
            <strong>Transmittal &amp; Religious Directives:</strong> <?= nl2br(htmlspecialchars($record['transmission_notes'])) ?>
          </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Official Document Footnote -->
    <div style="display:flex; justify-content:space-between; align-items:center; font-size:6.8pt; color:#64748b; margin-top:4px; padding:0 4px;">
      <span>PSA / Republic of the Philippines &bull; Municipal Form No. 103 (Certified Islamic Endorsement File)</span>
      <span>Security Hash Ref: #<?= htmlspecialchars($record['registration_number']) ?>-<?= $record['id'] ?>-ISCAG</span>
      <span>Printed on Standard Letter Bond Paper (8.5&quot; &times; 11&quot;)</span>
    </div>

  </div>

</body>
</html>
