<?php
/**
 * Official Philippine Death Certificate — Municipal Form No. 103 (Revised August 2016)
 * Office of the Civil Registrar General / Philippine Statistics Authority
 * Exactly matching official government form standard (Page 1 Front, Page 2 Back)
 */

if (!function_exists('asset')) {
    function asset($path) {
        return '/' . ltrim($path, '/');
    }
}
if (!function_exists('url')) {
    function url($path = '') {
        return '/' . ltrim($path, '/');
    }
}

$r = $record ?? [];

// Name processing
$firstName = trim($r['first_name'] ?? '');
$middleName = trim($r['middle_name'] ?? '');
$lastName = trim($r['last_name'] ?? '');
$fullName = trim($r['deceased_name'] ?? '');

if (empty($firstName) && empty($lastName) && !empty($fullName)) {
    $parts = preg_split('/\s+/', $fullName);
    if (count($parts) === 1) {
        $firstName = $parts[0];
    } elseif (count($parts) === 2) {
        $firstName = $parts[0];
        $lastName = $parts[1];
    } else {
        $lastName = array_pop($parts);
        $middleName = array_pop($parts);
        $firstName = implode(' ', $parts);
    }
}

// Dates processing
$dodTime = !empty($r['date_of_death']) ? strtotime($r['date_of_death']) : null;
$dodDay = $dodTime ? date('d', $dodTime) : '';
$dodMonth = $dodTime ? date('F', $dodTime) : '';
$dodYear = $dodTime ? date('Y', $dodTime) : '';
$timeOfDeath = !empty($r['time_of_death']) ? date('g:i A', strtotime($r['time_of_death'])) : '';

$dobTime = !empty($r['date_of_birth']) ? strtotime($r['date_of_birth']) : null;
$dobDay = $dobTime ? date('d', $dobTime) : '';
$dobMonth = $dobTime ? date('F', $dobTime) : '';
$dobYear = $dobTime ? date('Y', $dobTime) : '';

// Age processing
$ageStr = trim((string)($r['age'] ?? ''));
$ageYears = '';
if (preg_match('/(\d+)\s*(?:yr|year|y\.?o\.?)/i', $ageStr, $m)) {
    $ageYears = $m[1];
} elseif (is_numeric($ageStr)) {
    $ageYears = $ageStr;
} elseif ($dobTime && $dodTime) {
    $ageYears = (string)date_diff(date_create($r['date_of_birth']), date_create($r['date_of_death']))->y;
}

// Sex & Civil Status
$sex = strtoupper(trim($r['sex'] ?? 'MALE'));
$isMale = ($sex === 'MALE');
$isFemale = ($sex === 'FEMALE');

$civilStatus = strtoupper(trim($r['civil_status'] ?? 'SINGLE'));

// Location & Registry Info
$province = strtoupper(trim($r['province'] ?? 'CAVITE'));
$city = strtoupper(trim($r['city_municipality'] ?? 'CITY OF DASMARIÑAS'));
$regNo = trim($r['registration_number'] ?? '');

$placeOfDeath = trim($r['place_of_death'] ?? 'City of Dasmariñas, Cavite');
$residence = trim($r['residence'] ?? 'City of Dasmariñas, Cavite');
$religion = strtoupper(trim($r['religion'] ?? 'ISLAM'));
$citizenship = strtoupper(trim($r['citizenship'] ?? 'FILIPINO'));
$occupation = strtoupper(trim($r['occupation'] ?? 'NONE'));

// Parents
$fatherName = strtoupper(trim($r['father_name'] ?? ''));
$motherName = strtoupper(trim($r['mother_name'] ?? ''));

// Informant
$informantName = strtoupper(trim($r['informant_name'] ?? ''));
$informantRel = strtoupper(trim($r['informant_relationship'] ?? 'IMMEDIATE FAMILY'));
$certDate = !empty($r['death_cert_date']) ? date('F d, Y', strtotime($r['death_cert_date'])) : date('F d, Y');

// Staff
$sentByName = strtoupper(trim($r['sent_by_name'] ?? 'DAMAYAN STAFF'));
$sentDate = !empty($r['sent_at']) ? date('F d, Y', strtotime($r['sent_at'])) : date('F d, Y');
$ackByName = strtoupper(trim($r['acknowledged_by_name'] ?? 'MALE DA\'WAH OFFICER'));
$ackDate = !empty($r['acknowledged_at']) ? date('F d, Y', strtotime($r['acknowledged_at'])) : date('F d, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Municipal Form No. 103 (Certificate of Death) — <?= htmlspecialchars($regNo) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      background: #334155;
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      color: #000;
      padding: 24px 10px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      -webkit-font-smoothing: antialiased;
    }

    /* Screen Top Toolbar */
    .screen-toolbar {
      width: 100%;
      max-width: 8.5in;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      gap: 12px;
      flex-wrap: wrap;
    }
    .btn-tool {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 7px;
      font-weight: 700;
      font-size: 0.84rem;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
      border: 1px solid transparent;
      font-family: inherit;
    }
    .btn-tool.back {
      background: #ffffff;
      color: #1e293b;
      border-color: #cbd5e1;
    }
    .btn-tool.back:hover { background: #f8fafc; }
    .btn-tool.nav-page {
      background: #475569;
      color: #fff;
    }
    .btn-tool.nav-page:hover { background: #64748b; }
    .btn-tool.print {
      background: #14532d;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .btn-tool.print:hover { background: #0f3d21; }
    .btn-tool.download {
      background: #0284c7;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }
    .btn-tool.download:hover { background: #0369a1; }

    /* Page Break indicator between pages on screen */
    .page-separator {
      width: 100%;
      max-width: 8.5in;
      text-align: center;
      margin: 24px 0 16px;
      color: #cbd5e1;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .page-separator::before, .page-separator::after {
      content: "";
      flex: 1;
      height: 1px;
      background: #64748b;
    }

    /* Standard Philippine Letter Sheet (8.5in x 11in) */
    .sheet-page {
      width: 8.5in;
      min-height: 11in;
      height: 11in;
      background: #ffffff;
      padding: 0.22in 0.28in 0.22in 0.28in;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
      position: relative;
      margin: 0 auto;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Outer borders matching official form */
    .form-border {
      border: 1.5px solid #000;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
    }

    /* Header styling */
    .header-top {
      display: flex;
      justify-content: space-between;
      padding: 2px 6px 1px;
      font-size: 6.8pt;
      line-height: 1.15;
    }
    .header-center {
      text-align: center;
      margin-top: -2px;
    }
    .ph-title-1 {
      font-size: 7.2pt;
      letter-spacing: 0.04em;
    }
    .ph-title-2 {
      font-size: 7.8pt;
      font-weight: 700;
      letter-spacing: 0.04em;
    }
    .ph-title-main {
      font-size: 11.5pt;
      font-weight: 900;
      letter-spacing: 0.06em;
      margin: 1px 0 1px;
      text-transform: uppercase;
    }

    .meta-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding: 1px 6px 3px;
      font-size: 7.4pt;
    }
    .meta-col-left {
      width: 58%;
      line-height: 1.35;
    }
    .meta-line {
      display: inline-block;
      border-bottom: 1px solid #000;
      min-width: 160px;
      font-weight: 700;
      padding-left: 4px;
    }
    .meta-col-right {
      width: 38%;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 6px;
    }
    .registry-box {
      border: 1.2px solid #000;
      padding: 2px 10px;
      font-size: 8.5pt;
      font-weight: 800;
      letter-spacing: 0.05em;
      min-width: 140px;
      text-align: center;
    }

    /* Main Table Grid */
    .form-table {
      width: 100%;
      border-collapse: collapse;
      border-top: 1.5px solid #000;
      border-bottom: 1.5px solid #000;
      table-layout: fixed;
    }
    .form-table td, .form-table th {
      border: 1px solid #000;
      padding: 1.5px 3.5px;
      vertical-align: top;
      font-size: 7.2pt;
      line-height: 1.2;
    }
    .label-num {
      font-weight: 800;
      font-size: 7pt;
      margin-right: 3px;
    }
    .label-title {
      font-size: 6.2pt;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.01em;
    }
    .label-sub {
      font-size: 5.8pt;
      color: #333;
      font-weight: normal;
    }
    .val-cell {
      font-weight: 700;
      font-size: 7.8pt;
      color: #000;
      margin-top: 1px;
      min-height: 13px;
    }
    .val-cell-lg {
      font-weight: 800;
      font-size: 8.8pt;
      letter-spacing: 0.03em;
      margin-top: 1px;
    }

    /* Section divider header inside table */
    .section-banner {
      background: #ffffff;
      text-align: center;
      font-weight: 800;
      font-size: 7.6pt;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 2px !important;
      border-top: 1.5px solid #000 !important;
      border-bottom: 1.5px solid #000 !important;
    }
    .section-banner .sub {
      font-size: 6pt;
      font-weight: normal;
      font-style: italic;
      text-transform: none;
      display: block;
      margin-top: 1px;
    }

    /* Checkbox & Options */
    .chk-box {
      display: inline-block;
      width: 9px;
      height: 9px;
      border: 1px solid #000;
      text-align: center;
      line-height: 8px;
      font-size: 7.5pt;
      font-weight: 900;
      vertical-align: middle;
      margin-right: 2px;
    }
    .chk-box.checked {
      background: #000;
      color: #fff;
    }
    .chk-item {
      display: inline-flex;
      align-items: center;
      margin-right: 8px;
      font-size: 6.8pt;
    }

    /* Coding boxes at bottom */
    .code-grid {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 2px 6px;
      font-size: 6pt;
      font-weight: 700;
    }
    .code-group {
      display: inline-flex;
      align-items: center;
      gap: 2px;
    }
    .code-box {
      width: 12px;
      height: 13px;
      border: 1px solid #000;
      display: inline-block;
    }

    /* Underline lines for blank inputs */
    .blank-line {
      display: inline-block;
      border-bottom: 1px solid #000;
      height: 11px;
      vertical-align: bottom;
    }

    /* Page 2 Specific Sections */
    .page2-header-banner {
      text-align: center;
      font-weight: 800;
      font-size: 7.8pt;
      text-transform: uppercase;
      padding: 3px 6px;
      border-bottom: 1.5px solid #000;
      letter-spacing: 0.05em;
    }
    .p2-section-box {
      border: 1px solid #000;
      margin-top: 5px;
      padding: 4px 6px;
      font-size: 7pt;
      line-height: 1.3;
    }
    .p2-section-title {
      text-align: center;
      font-weight: 800;
      font-size: 7.8pt;
      text-transform: uppercase;
      margin-bottom: 4px;
      letter-spacing: 0.04em;
    }

    /* Signatures inside boxes */
    .sig-row {
      display: flex;
      justify-content: space-between;
      margin-top: 12px;
      padding: 0 4px;
      font-size: 6.8pt;
    }
    .sig-field {
      width: 48%;
      line-height: 1.4;
    }
    .sig-underline {
      display: inline-block;
      border-bottom: 1px solid #000;
      width: 60%;
      min-height: 12px;
    }

    /* Print styles */
    @media print {
      @page {
        size: letter portrait;
        margin: 0.18in 0.22in;
      }
      body {
        background: #ffffff !important;
        padding: 0 !important;
      }
      .screen-toolbar, .page-separator {
        display: none !important;
      }
      .sheet-page {
        width: 100% !important;
        height: 10.55in !important;
        min-height: 10.55in !important;
        max-height: 10.6in !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0.05in !important;
        page-break-after: always !important;
        break-after: page !important;
      }
      .sheet-page:last-child {
        page-break-after: avoid !important;
        break-after: avoid !important;
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
  <div class="screen-toolbar no-print">
    <div style="display:flex; align-items:center; gap:8px;">
      <a href="<?= url('/admin/dawah/male/death-certificates') ?>" class="btn-tool back">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Back to Received Transmittals
      </a>
      <a href="#page-1" class="btn-tool nav-page">Page 1: Front</a>
      <a href="#page-2" class="btn-tool nav-page">Page 2: Back / Affidavit</a>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
      <button type="button" class="btn-tool download" id="btn-download-pdf" onclick="downloadAsPdf()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> 📥 Download as PDF
      </button>
      <button type="button" class="btn-tool print" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> 🖨️ Print Form 103 (2 Pages)
      </button>
    </div>
  </div>

  <div id="printable-document-container">
  <!-- ========================================== -->
  <!-- PAGE 1: FRONT (MUNICIPAL FORM NO. 103)    -->
  <!-- ========================================== -->
  <div class="sheet-page page-1" id="page-1">
    <div class="form-border">
      
      <!-- Top Title Header -->
      <div>
        <div class="header-top">
          <div>
            <strong>Municipal Form No. 103</strong><br>
            <span style="font-size:6.2pt; font-style:italic;">(Revised August 2016)</span>
          </div>
          <div style="font-size:6.2pt; font-style:italic;">
            (To be accomplished in quadruplicate using black ink)
          </div>
        </div>

        <div class="header-center">
          <div class="ph-title-1">Republic of the Philippines</div>
          <div class="ph-title-2">OFFICE OF THE CIVIL REGISTRAR GENERAL</div>
          <div class="ph-title-main">CERTIFICATE OF DEATH</div>
        </div>

        <div class="meta-row">
          <div class="meta-col-left">
            <div>Province <span class="meta-line" style="min-width:220px;"><?= htmlspecialchars($province) ?></span></div>
            <div style="margin-top:2px;">City/Municipality <span class="meta-line" style="min-width:180px;"><?= htmlspecialchars($city) ?></span></div>
          </div>
          <div class="meta-col-right">
            <span style="font-size:7.4pt; font-weight:700;">Registry No.</span>
            <div class="registry-box"><?= htmlspecialchars($regNo) ?></div>
          </div>
        </div>
      </div>

      <!-- Main Boxed Table (1 to 29) -->
      <table class="form-table">
        <colgroup>
          <col style="width: 25%;">
          <col style="width: 25%;">
          <col style="width: 22%;">
          <col style="width: 14%;">
          <col style="width: 14%;">
        </colgroup>

        <!-- 1. NAME & 2. SEX -->
        <tr>
          <td colspan="4">
            <span class="label-num">1. NAME</span>
            <span class="label-sub">(First)</span> <span style="font-weight:700;"><?= htmlspecialchars($firstName) ?></span>
            &nbsp;&nbsp;
            <span class="label-sub">(Middle)</span> <span style="font-weight:700;"><?= htmlspecialchars($middleName) ?></span>
            &nbsp;&nbsp;
            <span class="label-sub">(Last)</span> <span style="font-weight:700;"><?= htmlspecialchars($lastName) ?></span>
            <div class="val-cell-lg" style="margin-top:2px;">
              <?= htmlspecialchars($fullName) ?>
            </div>
          </td>
          <td>
            <span class="label-num">2. SEX</span> <span class="label-sub">(Male/Female)</span>
            <div class="val-cell" style="margin-top:4px;">
              <span class="chk-box <?= $isMale ? 'checked' : '' ?>"><?= $isMale ? '✓' : '' ?></span> Male
              &nbsp;&nbsp;
              <span class="chk-box <?= $isFemale ? 'checked' : '' ?>"><?= $isFemale ? '✓' : '' ?></span> Female
            </div>
          </td>
        </tr>

        <!-- 3. DATE OF DEATH, 4. DATE OF BIRTH, 5. AGE AT TIME OF DEATH -->
        <tr>
          <td colspan="2">
            <span class="label-num">3. DATE OF DEATH</span> <span class="label-sub">(Day, Month, Year)</span>
            <div class="val-cell">
              <?= $dodDay ? htmlspecialchars("{$dodDay} {$dodMonth} {$dodYear}") : '—' ?>
              <?php if (!empty($timeOfDeath)): ?>
                &nbsp;&bull;&nbsp; <?= htmlspecialchars($timeOfDeath) ?>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <span class="label-num">4. DATE OF BIRTH</span> <span class="label-sub">(Day) (Month) (Year)</span>
            <div class="val-cell">
              <?= $dobDay ? htmlspecialchars("{$dobDay} {$dobMonth} {$dobYear}") : '—' ?>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">5. AGE AT THE TIME OF DEATH</span>
            <div style="display:flex; font-size:5.8pt; gap:4px; margin-top:1px;">
              <div style="flex:1;">
                <strong>a. IF 1 YEAR OR ABOVE</strong><br>
                [Completed years]: <span style="font-weight:800; font-size:7.5pt;"><?= htmlspecialchars($ageYears) ?></span>
              </div>
              <div style="flex:1;">
                <strong>b. UNDER 1 YEAR</strong><br>
                [Months] [Days]
              </div>
              <div style="flex:1;">
                <strong>c. UNDER 24 HRS</strong><br>
                [Hours] [Min/Sec]
              </div>
            </div>
          </td>
        </tr>

        <!-- 6. PLACE OF DEATH, 7. CIVIL STATUS -->
        <tr>
          <td colspan="3">
            <span class="label-num">6. PLACE OF DEATH</span>
            <span class="label-sub">(Name of Hospital/Clinic/Institution/House No., St., Barangay, City/Municipality, Province)</span>
            <div class="val-cell">
              <?= htmlspecialchars($placeOfDeath) ?>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">7. CIVIL STATUS</span>
            <span class="label-sub">(Single/Married/Widow/Widower/Annulled/Divorced)</span>
            <div class="val-cell">
              <?= htmlspecialchars($civilStatus) ?>
            </div>
          </td>
        </tr>

        <!-- 8. RELIGION, 9. CITIZENSHIP, 10. RESIDENCE -->
        <tr>
          <td>
            <span class="label-num">8. RELIGION/RELIGIOUS SECT</span>
            <div class="val-cell"><?= htmlspecialchars($religion) ?></div>
          </td>
          <td>
            <span class="label-num">9. CITIZENSHIP</span>
            <div class="val-cell"><?= htmlspecialchars($citizenship) ?></div>
          </td>
          <td colspan="3">
            <span class="label-num">10. RESIDENCE</span>
            <span class="label-sub">(House No., St., Barangay, City/Municipality, Province, Country)</span>
            <div class="val-cell"><?= htmlspecialchars($residence) ?></div>
          </td>
        </tr>

        <!-- 11. OCCUPATION, 12. FATHER, 13. MOTHER -->
        <tr>
          <td>
            <span class="label-num">11. OCCUPATION</span>
            <div class="val-cell"><?= htmlspecialchars($occupation) ?></div>
          </td>
          <td colspan="2">
            <span class="label-num">12. NAME OF FATHER</span> <span class="label-sub">(First, Middle, Last)</span>
            <div class="val-cell"><?= htmlspecialchars($fatherName ?: '—') ?></div>
          </td>
          <td colspan="2">
            <span class="label-num">13. MAIDEN NAME OF MOTHER</span> <span class="label-sub">(First, Middle, Last)</span>
            <div class="val-cell"><?= htmlspecialchars($motherName ?: '—') ?></div>
          </td>
        </tr>

        <!-- BANNER: MEDICAL CERTIFICATE -->
        <tr>
          <td colspan="5" class="section-banner">
            MEDICAL CERTIFICATE
            <span class="sub">(For ages 0 to 7 days, accomplish items 14-19a at the back)</span>
          </td>
        </tr>

        <!-- 19b. CAUSES OF DEATH & INTERVAL -->
        <tr>
          <td colspan="4">
            <span class="label-num">19b. CAUSES OF DEATH</span> <span class="label-sub">(If the deceased is aged 8 days and over)</span>
            <div style="font-size:6.8pt; line-height:1.35; padding-left:4px; margin-top:2px;">
              <div><strong>I. Immediate cause : a.</strong> <?= htmlspecialchars($r['cause_of_death'] ?? 'Natural Causes / Cardiopulmonary Arrest') ?></div>
              <div>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Antecedent cause : b.</strong> ____________________________________________________________________</div>
              <div>&nbsp;&nbsp;&nbsp;&nbsp;<strong>Underlying cause : c.</strong> ____________________________________________________________________</div>
              <div><strong>II. Other significant conditions contributing to death:</strong> Certified for Islamic Janazah Rites</div>
            </div>
          </td>
          <td>
            <span class="label-title">Interval Between Onset and Death</span>
            <div style="font-size:6.8pt; line-height:1.4; margin-top:4px;">
              <div>a. ____________</div>
              <div>b. ____________</div>
              <div>c. ____________</div>
            </div>
          </td>
        </tr>

        <!-- 19c. MATERNAL CONDITION -->
        <tr>
          <td colspan="5">
            <span class="label-num">19c. MATERNAL CONDITION</span> <span class="label-sub">(If the deceased is female aged 15-49 years old)</span>
            <div style="font-size:6.5pt; display:flex; gap:10px; margin-top:1px;">
              <span>____ a. pregnant, not in labour</span>
              <span>____ b. pregnant, in labour</span>
              <span>____ c. less than 42 days after delivery</span>
              <span>____ d. 42 days to 1 year after delivery</span>
              <span><span class="chk-box"></span> e. None of the choices</span>
            </div>
          </td>
        </tr>

        <!-- 19d. DEATH BY EXTERNAL CAUSES & 20. AUTOPSY -->
        <tr>
          <td colspan="4">
            <span class="label-num">19d. DEATH BY EXTERNAL CAUSES</span>
            <div style="font-size:6.6pt; line-height:1.3; margin-top:1px;">
              <div>a. Manner of death (Homicide, Suicide, Accident, Legal intervention, etc.) ________________________________________</div>
              <div>b. Place of Occurrence of External Cause (e.g. home, farm, factory, street, sea, etc.) _____________________________</div>
            </div>
          </td>
          <td>
            <span class="label-num">20. AUTOPSY</span> <span class="label-sub">(Yes / No)</span>
            <div style="margin-top:3px; font-size:7pt;">
              <span class="chk-box"></span> Yes &nbsp;&nbsp;
              <span class="chk-box checked">✓</span> No
            </div>
          </td>
        </tr>

        <!-- 21a. ATTENDANT & 21b. DURATION -->
        <tr>
          <td colspan="3">
            <span class="label-num">21a. ATTENDANT</span>
            <div style="font-size:6.4pt; margin-top:2px;">
              <span class="chk-item"><span class="chk-box checked">✓</span> 1 Private Physician</span>
              <span class="chk-item"><span class="chk-box"></span> 2 Public Health Officer</span>
              <span class="chk-item"><span class="chk-box"></span> 3 Hospital Authority</span>
              <span class="chk-item"><span class="chk-box"></span> 4 None</span>
              <span class="chk-item"><span class="chk-box"></span> 5 Others (Specify)</span>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">21b. If attended, state duration (mm/dd/yy)</span>
            <div style="font-size:6.8pt; margin-top:2px;">
              From: ________________ &nbsp; To: ________________
            </div>
          </td>
        </tr>

        <!-- 22. CERTIFICATION OF DEATH & REVIEWED BY -->
        <tr>
          <td colspan="3">
            <span class="label-num">22. CERTIFICATION OF DEATH</span>
            <div style="font-size:6.2pt; line-height:1.2; margin-top:1px;">
              <span class="chk-box"></span> I hereby certify that the foregoing particulars are correct as near as same can be ascertained and I further certify that I
              <span class="chk-box checked">✓</span> have attended / <span class="chk-box"></span> have not attended the deceased and that death occurred at
              <strong><?= !empty($timeOfDeath) ? htmlspecialchars($timeOfDeath) : '______' ?></strong> on the date of death specified above.
            </div>
            <div style="font-size:6.4pt; margin-top:3px; line-height:1.25;">
              <div>Signature _________________________________________________</div>
              <div>Name in Print <strong>DR. HEALTH OFFICER / ATTENDING PHYSICIAN</strong></div>
              <div>Title or Position <strong>Medical Officer / Attending Physician</strong></div>
              <div>Address <strong>City of Dasmariñas, Cavite</strong></div>
              <div>Date <strong><?= htmlspecialchars($certDate) ?></strong></div>
            </div>
          </td>
          <td colspan="2">
            <span class="label-title">REVIEWED BY:</span>
            <div style="text-align:center; margin-top:16px; font-size:6.5pt;">
              <div style="border-bottom:1px solid #000; width:85%; margin:0 auto 2px;"></div>
              <div>Signature Over Printed Name of Health Officer</div>
              <div style="margin-top:6px; text-align:left; font-size:6.4pt;">Date: ________________________</div>
            </div>
          </td>
        </tr>

        <!-- 23. DISPOSAL, 24a. BURIAL PERMIT, 24b. TRANSFER PERMIT -->
        <tr>
          <td>
            <span class="label-num">23. CORPSE DISPOSAL</span>
            <div style="font-size:6.2pt; margin-top:2px;">
              <span class="chk-item"><span class="chk-box checked">✓</span> Burial</span>
              <span class="chk-item"><span class="chk-box"></span> Cremation</span>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">24a. BURIAL/CREMATION PERMIT</span>
            <div style="font-size:6.4pt;">
              Number: <strong>BP-<?= date('Y') ?>-<?= htmlspecialchars(substr($regNo, -5)) ?></strong><br>
              Date Issued: <strong><?= htmlspecialchars($certDate) ?></strong>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">24b. TRANSFER PERMIT</span>
            <div style="font-size:6.4pt;">
              Number: ____________________<br>
              Date Issued: ________________
            </div>
          </td>
        </tr>

        <!-- 25. CEMETERY NAME -->
        <tr>
          <td colspan="5">
            <span class="label-num">25. NAME AND ADDRESS OF CEMETERY OR CREMATORY</span>
            <div class="val-cell">
              <?= htmlspecialchars($r['cemetery_name'] ?? 'ISCAG Muslim Memorial Cemetery, City of Dasmariñas, Cavite') ?>
            </div>
          </td>
        </tr>

        <!-- 26. INFORMANT & 27. PREPARED BY -->
        <tr>
          <td colspan="3">
            <span class="label-num">26. CERTIFICATION OF INFORMANT</span>
            <div style="font-size:6pt; font-style:italic;">I hereby certify that all information supplied are true and correct to my own knowledge and belief.</div>
            <div style="font-size:6.4pt; margin-top:2px; line-height:1.25;">
              <div>Signature _________________________________________________</div>
              <div>Name in Print <strong><?= htmlspecialchars($informantName ?: 'FAMILY REPRESENTATIVE') ?></strong></div>
              <div>Relationship to Deceased <strong><?= htmlspecialchars($informantRel) ?></strong></div>
              <div>Address <strong><?= htmlspecialchars($residence) ?></strong></div>
              <div>Date <strong><?= htmlspecialchars($certDate) ?></strong></div>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">27. PREPARED BY</span>
            <div style="font-size:6.4pt; margin-top:7px; line-height:1.25;">
              <div>Signature _______________________________________</div>
              <div>Name in Print <strong><?= htmlspecialchars($sentByName) ?></strong></div>
              <div>Title or Position <strong>Damayan Coordinator / Officer</strong></div>
              <div>Date <strong><?= htmlspecialchars($sentDate) ?></strong></div>
            </div>
          </td>
        </tr>

        <!-- 28. RECEIVED BY & 29. REGISTERED AT LCRO -->
        <tr>
          <td colspan="3">
            <span class="label-num">28. RECEIVED BY</span>
            <div style="font-size:6.4pt; margin-top:2px; line-height:1.25;">
              <div>Signature _________________________________________________</div>
              <div>Name in Print <strong><?= htmlspecialchars($ackByName) ?></strong></div>
              <div>Title or Position <strong>Male Da'wah Religious Officer</strong></div>
              <div>Date <strong><?= htmlspecialchars($ackDate) ?></strong></div>
            </div>
          </td>
          <td colspan="2">
            <span class="label-num">29. REGISTERED AT THE OFFICE OF THE CIVIL REGISTRAR</span>
            <div style="font-size:6.4pt; margin-top:2px; line-height:1.25;">
              <div>Signature _______________________________________</div>
              <div>Name in Print <strong>LOCAL CIVIL REGISTRAR</strong></div>
              <div>Title or Position <strong>City Civil Registrar</strong></div>
              <div>Date <strong><?= htmlspecialchars($certDate) ?></strong></div>
            </div>
          </td>
        </tr>

        <!-- REMARKS/ANNOTATIONS -->
        <tr>
          <td colspan="5" style="min-height:26px;">
            <span class="label-title">REMARKS/ANNOTATIONS (For LCRO/OCRG Use Only)</span>
            <div style="font-size:6.5pt; color:#14532d; font-weight:700; padding:1px 4px;">
              [ISCAG Civil Registry &bull; Salatul Janazah Religious Transmittal File &bull; Reg: #<?= htmlspecialchars($regNo) ?>]
              <?php if (!empty($r['transmission_notes'])): ?>
                &nbsp;&bull;&nbsp; <?= htmlspecialchars($r['transmission_notes']) ?>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      </table>

      <!-- Bottom Coding Box -->
      <div>
        <div style="font-size:6pt; font-weight:700; padding:1px 6px;">
          TO BE FILLED-UP AT THE OFFICE OF THE CIVIL REGISTRAR
        </div>
        <div class="code-grid">
          <div class="code-group">5 <span class="code-box"></span><span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">8 <span class="code-box"></span><span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">9 <span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">10 <span class="code-box"></span><span class="code-box"></span><span class="code-box"></span><span class="code-box"></span><span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">11 <span class="code-box"></span><span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">19a(a)/19b <span class="code-box"></span><span class="code-box"></span><span class="code-box"></span></div>
          <div class="code-group">19a(c) <span class="code-box"></span></div>
        </div>
      </div>

    </div>
  </div>

  <!-- Page Separator for Screen View -->
  <div class="page-separator no-print" data-html2canvas-ignore="true">
    Page 2: Back / Medical Certificate 0-7 Days / Embalmer / Delayed Registration Affidavit
  </div>

  <!-- ========================================== -->
  <!-- PAGE 2: BACK (CHILDREN, AUTOPSY, EMBALMER, -->
  <!--               AFFIDAVIT FOR DELAYED REG)   -->
  <!-- ========================================== -->
  <div class="sheet-page page-2" id="page-2">
    <div class="form-border" style="padding:4px;">
      
      <!-- Section: FOR CHILDREN AGED 0 TO 7 DAYS -->
      <div style="border:1px solid #000;">
        <div class="page2-header-banner">
          FOR CHILDREN AGED 0 TO 7 DAYS
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:6.8pt;">
          <colgroup>
            <col style="width: 33.33%;">
            <col style="width: 33.33%;">
            <col style="width: 33.34%;">
          </colgroup>
          <tr>
            <td style="border:1px solid #000; padding:2px 4px;">
              <strong>14. AGE OF MOTHER</strong>
              <div style="min-height:12px; margin-top:2px;">__________________</div>
            </td>
            <td style="border:1px solid #000; padding:2px 4px;">
              <strong>15. METHOD OF DELIVERY</strong> <span style="font-size:5.8pt;">(Normal spontaneous vertex, if others, specify)</span>
              <div style="min-height:12px; margin-top:2px;">____________________________________</div>
            </td>
            <td style="border:1px solid #000; padding:2px 4px;">
              <strong>16. LENGTH OF PREGNANCY:</strong> <span style="font-size:5.8pt;">(In completed weeks)</span>
              <div style="min-height:12px; margin-top:2px;">__________________</div>
            </td>
          </tr>
          <tr>
            <td style="border:1px solid #000; padding:2px 4px;">
              <strong>17. TYPE OF BIRTH</strong> <span style="font-size:5.8pt;">(Single, Twin, Triplet, etc)</span>
              <div style="min-height:12px; margin-top:2px;">__________________</div>
            </td>
            <td colspan="2" style="border:1px solid #000; padding:2px 4px;">
              <strong>18. IF MULTIPLE BIRTH, CHILD WAS</strong> <span style="font-size:5.8pt;">(First, Second, Third, etc)</span>
              <div style="min-height:12px; margin-top:2px;">__________________</div>
            </td>
          </tr>
        </table>

        <!-- Medical Certificate for 0-7 Days -->
        <div style="padding:3px 6px; border-top:1px solid #000;">
          <div style="font-weight:800; font-size:7.2pt; text-align:center; margin-bottom:2px;">MEDICAL CERTIFICATE</div>
          <div style="font-weight:700; font-size:6.8pt; margin-bottom:2px;">19a. CAUSES OF DEATH</div>
          <div style="font-size:6.6pt; line-height:1.45; padding-left:6px;">
            <div>a. Main disease/condition of infant __________________________________________________________________________________</div>
            <div>b. Other diseases/conditions of infant _________________________________________________________________________________</div>
            <div>c. Main maternal disease/condition affecting infant _____________________________________________________________________</div>
            <div>d. Other maternal disease/condition affecting infant ____________________________________________________________________</div>
            <div>e. Other relevant circumstances _____________________________________________________________________________________</div>
          </div>
          <div style="text-align:center; font-weight:800; font-size:7pt; letter-spacing:0.04em; margin-top:3px;">
            CONTINUE TO FILL UP ITEM 20
          </div>
        </div>
      </div>

      <!-- Section: POSTMORTEM CERTIFICATE OF DEATH -->
      <div class="p2-section-box">
        <div class="p2-section-title">POSTMORTEM CERTIFICATE OF DEATH</div>
        <div style="font-size:6.6pt; line-height:1.4;">
          I HEREBY CERTIFY that I have performed an autopsy upon the body of the deceased and that the cause of death was
          <span class="blank-line" style="width:98%;"></span>
          <span class="blank-line" style="width:98%;"></span>
        </div>
        <div class="sig-row">
          <div class="sig-field">
            Signature ____________________________________<br>
            Name in Print ________________________________<br>
            Date ________________________________________
          </div>
          <div class="sig-field">
            Title/Designation ____________________________<br>
            Address ______________________________________
          </div>
        </div>
      </div>

      <!-- Section: CERTIFICATION OF EMBALMER -->
      <div class="p2-section-box">
        <div class="p2-section-title">CERTIFICATION OF EMBALMER</div>
        <div style="font-size:6.6pt; line-height:1.35;">
          I HEREBY CERTIFY that I have embalmed <span class="blank-line" style="width:40%;"></span> following all the regulations prescribed by the Department of Health.
        </div>
        <div class="sig-row">
          <div class="sig-field">
            Signature ____________________________________<br>
            Name in Print ________________________________<br>
            Address ______________________________________
          </div>
          <div class="sig-field">
            Title/Designation ____________________________<br>
            License No. __________________________________<br>
            Issued on ________________ at _______________<br>
            Expiry Date __________________________________
          </div>
        </div>
      </div>

      <!-- Section: AFFIDAVIT FOR DELAYED REGISTRATION OF DEATH -->
      <div class="p2-section-box" style="flex:1; display:flex; flex-direction:column; justify-content:space-between;">
        <div>
          <div class="p2-section-title">AFFIDAVIT FOR DELAYED REGISTRATION OF DEATH</div>
          
          <div style="font-size:6.6pt; line-height:1.35; text-align:justify;">
            I, <span class="blank-line" style="width:40%;"></span>, of legal age, single/married/divorced/widow/widower, with residence and postal address <span class="blank-line" style="width:42%;"></span>, after being duly sworn in accordance with law, do hereby depose and say:
          </div>

          <div style="font-size:6.6pt; line-height:1.45; margin-top:4px; padding-left:8px;">
            <div>
              1. That <span class="blank-line" style="width:38%;"></span> died on <span class="blank-line" style="width:16%;"></span> in <span class="blank-line" style="width:28%;"></span> and was buried/cremated in <span class="blank-line" style="width:25%;"></span> on <span class="blank-line" style="width:18%;"></span>.
            </div>
            <div>
              2. That the deceased at the time of his/her death:<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<span class="chk-box"></span> was attended by <span class="blank-line" style="width:50%;"></span>;<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<span class="chk-box"></span> was not attended.
            </div>
            <div>
              3. That the cause of death of the deceased was <span class="blank-line" style="width:68%;"></span>.
            </div>
            <div>
              4. That the reason for the delay in registering this death was due to <span class="blank-line" style="width:60%;"></span>.
            </div>
            <div>
              5. That I am executing this affidavit to attest to the truthfulness of the foregoing statements for all legal intents and purposes.
            </div>
          </div>

          <div style="font-size:6.6pt; margin-top:6px;">
            In truth whereof, I have affixed my signature below this _____ day of ___________________ at ________________________, Philippines.
          </div>

          <div style="text-align:right; margin-top:14px; font-size:6.6pt;">
            <div style="display:inline-block; text-align:center; min-width:240px;">
              <div style="border-bottom:1px solid #000; width:100%; margin-bottom:2px;"></div>
              (Signature Over Printed Name of Affiant)
            </div>
          </div>
        </div>

        <div style="border-top:1px dashed #666; padding-top:4px; font-size:6.4pt; line-height:1.35; margin-top:6px;">
          <div>
            SUBSCRIBED AND SWORN to before me this _____ day of _____________________, Philippines, affiant who exhibited to me his/her CTC/valid ID <span class="blank-line" style="width:140px;"></span> issued on <span class="blank-line" style="width:80px;"></span> at <span class="blank-line" style="width:100px;"></span>.
          </div>

          <div class="sig-row" style="margin-top:10px;">
            <div class="sig-field">
              Signature of the Administering Officer ______________________<br>
              Name in Print ______________________________________________
            </div>
            <div class="sig-field">
              Position / Title / Designation _____________________________<br>
              Address ___________________________________________________
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  </div><!-- /#printable-document-container -->

  <script src="<?= asset('JS/html2pdf.bundle.min.js') ?>"></script>
  <script>
    async function downloadAsPdf() {
      const btn = document.getElementById('btn-download-pdf');
      const originalText = btn ? btn.innerHTML : '';
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<svg style="animation:spin 1s linear infinite;width:15px;height:15px;display:inline-block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor"></path></svg> Generating Official PDF...`;
      }

      try {
        const container = document.getElementById('printable-document-container');
        const filename = 'Death_Certificate_<?= preg_replace('/[^a-zA-Z0-9_\-]/', '_', $regNo ?: 'Form103') ?>.pdf';

        const opt = {
          margin:       0,
          filename:     filename,
          image:        { type: 'jpeg', quality: 0.99 },
          html2canvas:  { 
            scale: 2, 
            useCORS: true, 
            logging: false,
            windowWidth: 816 // standard 8.5in at 96dpi
          },
          jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
          pagebreak:    { mode: ['css', 'legacy'] }
        };

        await html2pdf().set(opt).from(container).save();
      } catch (err) {
        console.error('PDF generation error:', err);
        // Fallback to native print dialog
        window.print();
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      }
    }

    <?php if (!empty($auto_download_pdf)): ?>
      window.addEventListener('DOMContentLoaded', () => {
        setTimeout(downloadAsPdf, 500);
      });
    <?php endif; ?>
  </script>
  <style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
  </style>
</body>
</html>
