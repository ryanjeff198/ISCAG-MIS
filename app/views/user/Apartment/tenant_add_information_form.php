<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$userId = $_SESSION['user_id'] ?? null;
$dbUser = [];
$appData = [];
$pictureUrl = null;
$pictureRecordExists = false;

if ($userId) {
    require_once BASE_PATH . '/app/models/User.php';
    require_once BASE_PATH . '/app/models/ApartmentApp.php';
    
    $userModel = new User();
    $aptModel  = new ApartmentApp();
    
    $account = $userModel->findById($userId);
    $appInfo = $aptModel->getInfo($userId);
    $profile = $userModel->getAdditionalInfo($userId);
    
    // dbUser is used by the Access Gate and Navigation (should be Profile info)
    $dbUser = [
        'name' => trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')),
        'firstName' => $account['first_name'] ?? '',
        'lastName' => $account['last_name'] ?? '',
        'email' => $profile['email'] ?? ($account['email'] ?? ''),
        'sex' => !empty($profile['sex']) ? $profile['sex'] : ($_SESSION['sex'] ?? $_SESSION['gender'] ?? $account['sex'] ?? ''),
        'phone' => $profile['phone'] ?? ($account['contactnum'] ?? ''),
        'dob' => $profile['birthdate'] ?? '',
        'civil' => $profile['civil_status'] ?? '',
        'address' => $profile['address'] ?? '',
        'pob' => $profile['pob'] ?? ($profile['address'] ?? ''), // Fallback or new field if exists
        'occupation' => $profile['occupation'] ?? '',
        'arabicName' => $profile['muslimname'] ?? '',
        'revertYear' => $profile['dateofshahadah'] ?? '',
        'tribal' => $profile['tribalaffliation'] ?? '',
    ];

    // Check if application is already submitted
    $application = $aptModel->getApplication($userId);
    if ($application && in_array($application['status'], ['Pending', 'Assigned', 'Queued', 'VERIFIED'])) {
        header('Location: ' . url('/user/apartment/status'));
        exit;
    }

    // appData is used to pre-fill the form (should be Application info)
    $appData = $appInfo ?: [];
    $appData['roomtype'] = $application['roomtype'] ?? '';

    // Fetch 2x2 picture
    $pictureInfo = $aptModel->getAddInfoImage($appInfo['tenant_info'] ?? 0, 'picture');
    $pictureUrl = null;
    $pictureRecordExists = false;
    if ($pictureInfo) {
         $pictureRecordExists = true;
         if (!empty($pictureInfo['file_path'])) {
             // Verify file actually exists on DISK
             $fullPath = BASE_PATH . "/public/" . $pictureInfo['file_path'];
             if (file_exists($fullPath)) {
                 $pictureUrl = url($pictureInfo['file_path']);
             }
         }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Tenant Application</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
  <script src="<?= asset('JS/room-preview.js') ?>" defer></script>
  <style>
    /* ═══════════════════════════════════════════
       HIDE NUMBER INPUT SCROLL ARROWS
       ═══════════════════════════════════════════ */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield; /* Firefox */
      appearance: textfield;
    }

    /* ── GLOBAL SELECT STYLES ── */
    select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f5c3a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 10px center;
      background-size: 14px;
      padding-right: 32px !important;
      cursor: pointer;
      font-family: inherit;
      transition: all 0.2s ease;
    }

    select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    /* ═══════════════════════════════════════════
       STEPPER PROGRESS BAR
       ═══════════════════════════════════════════ */
    .stepper-wrapper {
      max-width: 900px;
      margin: 0 auto 28px;
    }

    .stepper-card {
      background: white;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
      overflow: hidden;
    }

    .stepper-hero {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      padding: 24px 32px 20px;
      position: relative;
      overflow: hidden;
    }

    .stepper-hero::before {
      content: '';
      position: absolute;
      right: -20px;
      bottom: -20px;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: rgba(201, 168, 76, 0.1);
    }

    .stepper-hero::after {
      content: '';
      position: absolute;
      right: 80px;
      bottom: -30px;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.05);
    }

    .stepper-hero h2 {
      font-family: 'Lora', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: white;
      margin: 0 0 4px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .stepper-hero h2 svg {
      width: 22px;
      height: 22px;
      fill: var(--accent-light);
    }

    .stepper-hero p {
      font-size: 0.82rem;
      color: rgba(255, 255, 255, 0.68);
      margin: 0;
    }

    .stepper-body {
      padding: 24px 32px 20px;
    }

    .stepper-steps {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      position: relative;
    }

    .stepper-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 2;
      cursor: pointer;
      flex: 0 0 auto;
      min-width: 130px;
      transition: all 0.3s ease;
    }

    .stepper-step:hover .stepper-circle {
      transform: scale(1.08);
      box-shadow: 0 4px 16px rgba(23, 107, 69, 0.2);
    }

    .stepper-circle {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Lora', serif;
      font-size: 1rem;
      font-weight: 700;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 3px solid var(--border);
      background: white;
      color: var(--text-muted);
      position: relative;
    }

    .stepper-circle svg {
      width: 20px;
      height: 20px;
      fill: white;
    }

    .stepper-step.active .stepper-circle {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      border-color: var(--primary);
      color: white;
      box-shadow: 0 4px 16px rgba(23, 107, 69, 0.25);
    }

    .stepper-step.active .stepper-circle::after {
      content: '';
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      border: 2px solid rgba(23, 107, 69, 0.15);
      animation: stepperPulse 2s ease infinite;
    }

    /* PDF Placeholder Styles */
    .doc-preview-pdf-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #f8f9fa;
      color: #dc3545;
      gap: 8px;
    }

    .doc-preview-pdf-placeholder svg {
      width: 48px;
      height: 48px;
      fill: currentColor;
    }

    .doc-preview-pdf-placeholder span {
      font-size: 0.75rem;
      font-weight: 700;
      color: #6c757d;
    }

    @keyframes stepperPulse {

      0%,
      100% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.08);
        opacity: 0.5;
      }
    }

    .stepper-step.completed .stepper-circle {
      background: linear-gradient(135deg, #2f8a60, #3da870);
      border-color: #2f8a60;
      color: white;
      box-shadow: 0 2px 8px rgba(47, 138, 96, 0.2);
    }

    .stepper-label {
      margin-top: 10px;
      font-size: 0.76rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      text-align: center;
      transition: color 0.3s ease;
    }

    .stepper-step.active .stepper-label {
      color: var(--primary-dark);
    }

    .stepper-step.completed .stepper-label {
      color: #2f8a60;
    }

    .stepper-sublabel {
      font-size: 0.68rem;
      color: var(--text-muted);
      margin-top: 2px;
      font-weight: 500;
      opacity: 0.7;
      text-align: center;
    }

    .stepper-step.active .stepper-sublabel {
      opacity: 1;
      color: var(--primary);
    }

    .stepper-connector {
      flex: 1;
      height: 3px;
      background: var(--border);
      border-radius: 2px;
      position: relative;
      margin: 0 -8px;
      align-self: flex-start;
      margin-top: 22px;
      z-index: 1;
      overflow: hidden;
    }

    .stepper-connector-fill {
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, #2f8a60, var(--accent));
      border-radius: 2px;
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stepper-connector.filled .stepper-connector-fill {
      transform: scaleX(1);
    }

    /* ── Progress info ── */
    .stepper-progress-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid var(--border);
    }

    .stepper-progress-text {
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--text-muted);
    }

    .stepper-progress-text strong {
      color: var(--primary-dark);
    }

    .stepper-progress-bar-outer {
      flex: 1;
      max-width: 280px;
      height: 8px;
      border-radius: 4px;
      background: var(--border);
      overflow: hidden;
      margin-left: 16px;
    }

    .stepper-progress-bar-inner {
      height: 100%;
      border-radius: 4px;
      background: linear-gradient(90deg, var(--primary-dark), var(--accent));
      transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }


    /* ═══════════════════════════════════════════
       FORM STEP PANELS (show/hide with transition)
       ═══════════════════════════════════════════ */
    .step-panel {
      display: none;
      animation: stepFadeIn 0.4s ease;
    }

    .step-panel.active {
      display: block;
    }

    @keyframes stepFadeIn {
      from {
        opacity: 0;
        transform: translateY(12px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ═══════════════════════════════════════════
       FORM DOCUMENT STYLES (Paper-like layout)
       ═══════════════════════════════════════════ */
    .form-document {
      background: white;
      max-width: 900px;
      margin: 0 auto;
      border-radius: 12px;
      box-shadow: 0 2px 24px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
      overflow: hidden;
    }

    /* ── FORM HEADER ── */
    .form-doc-header {
      background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
      padding: 28px 32px 20px;
      border-bottom: 3px solid var(--primary);
      position: relative;
    }

    .form-doc-header-top {
      display: grid;
      grid-template-columns: 100px 1fr 100px;
      align-items: center;
      margin-bottom: 8px;
      text-align: center;
    }

    .form-doc-header-logo {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      object-fit: contain;
      background: white;
      border: 3px solid #0f5c3a;
      padding: 4px;
      box-sizing: border-box;
      justify-self: start; /* Pin to start of its 100px lane */
      margin-left: 10px;
    }

    .form-doc-header-text {
      flex: 1;
      text-align: center;
    }

    .form-doc-header-text .arabic-line {
      font-size: 1rem;
      color: var(--primary-dark);
      margin-bottom: 2px;
      direction: rtl;
      font-weight: 600;
    }

    .form-doc-header-text .org-name-ar {
      font-size: 0.9rem;
      color: var(--primary-dark);
      direction: rtl;
      margin-bottom: 4px;
      font-weight: 600;
    }

    .form-doc-header-text .org-name-en {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    .form-doc-header-text .sec-reg {
      font-size: 0.68rem;
      color: var(--text-muted);
      margin-top: 2px;
    }


    .form-doc-title-bar {
      text-align: center;
      margin-top: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 24px;
    }

    .form-doc-title {
      font-family: 'Lora', serif;
      font-size: 1.1rem;
      font-weight: 700;
      color: white;
      background: var(--primary-dark);
      padding: 8px 32px;
      border-radius: 6px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .photo-upload-box {
      width: 100px;
      height: 110px;
      border: 2px dashed var(--border);
      border-radius: 8px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      background: #fafdf9;
      transition: all 0.2s;
      position: relative;
      overflow: hidden;
      flex-shrink: 0;
    }

    .photo-upload-box:hover {
      border-color: var(--primary);
      background: rgba(23, 107, 69, 0.04);
    }

    .photo-upload-box svg {
      width: 28px;
      height: 28px;
      fill: var(--text-muted);
      margin-bottom: 4px;
    }

    .photo-upload-box span {
      font-size: 0.65rem;
      color: var(--text-muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    .photo-upload-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      inset: 0;
    }

    .photo-upload-box input {
      display: none;
    }

    .photo-controls {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      opacity: 0;
      transition: opacity 0.2s;
      z-index: 10;
      border-radius: 6px;
    }

    .photo-upload-box:hover .photo-controls {
      opacity: 1;
    }

    .photo-btn {
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 0.7rem;
      font-weight: 700;
      cursor: pointer;
      border: none;
      width: 80%;
      text-align: center;
      transition: transform 0.1s;
    }

    .photo-btn:active {
      transform: scale(0.95);
    }

    .btn-edit-photo {
      background: white;
      color: #333;
    }

    .btn-remove-photo {
      background: #dc3545;
      color: white;
    }

    /* ── DATE + PHOTO ROW ── */
    .date-photo-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 32px 12px;
    }

    .date-photo-row .date-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .date-photo-row .date-group label {
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--text-main);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      white-space: nowrap;
    }

    .date-photo-row .date-group input[type="date"] {
      width: 170px;
      padding: 6px 10px;
      border: 1.5px solid var(--border);
      border-radius: 6px;
      font-size: 0.85rem;
      font-family: 'Source Sans 3', sans-serif;
      color: var(--text-main);
      background: white;
    }

    .date-photo-row .date-group input[type="date"]:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    /* ── IMAGE PREVIEW MODAL ── */
    .photo-preview-overlay,
    .img-preview-overlay {
      position: fixed;
      inset: 0;
      z-index: 99998;
      background: rgba(15, 30, 22, 0.6);
      backdrop-filter: blur(6px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      animation: fadeIn 0.2s ease;
    }

    .photo-preview-overlay img,
    .img-preview-overlay img {
      max-width: 80vw;
      max-height: 80vh;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      object-fit: contain;
      background: white;
      padding: 12px;
    }

    .photo-preview-close,
    .img-preview-close {
      position: absolute;
      top: 24px;
      right: 24px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      transition: all 0.18s;
    }

    .photo-preview-close:hover,
    .img-preview-close:hover {
      background: white;
      transform: scale(1.1);
    }

    .photo-preview-close svg,
    .img-preview-close svg {
      width: 18px;
      height: 18px;
      fill: var(--text-main);
    }

    /* ── FORM BODY ── */
    .form-doc-body {
      padding: 20px 32px 28px;
    }

    /* ── SECTION TITLES ── */
    .doc-section-title {
      font-family: 'Lora', serif;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 10px 0 8px;
      border-bottom: 2px solid var(--primary);
      margin-bottom: 16px;
      margin-top: 24px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .doc-section-title:first-child {
      margin-top: 0;
    }

    .doc-section-title svg {
      width: 16px;
      height: 16px;
      fill: var(--accent);
    }

    /* ── FORM TABLE GRID ── */
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }

    .info-table td {
      border: 1px solid var(--border);
      padding: 0;
      vertical-align: middle;
    }

    .info-table .field-label {
      font-size: 0.73rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      padding: 6px 10px;
      background: #f8faf9;
      white-space: nowrap;
      min-width: 110px;
    }

    .info-table .field-value {
      padding: 2px 4px;
    }

    .info-table .field-value input,
    .info-table .field-value select {
      width: 100%;
      border: none;
      padding: 7px 10px;
      font-size: 0.85rem;
      font-family: 'Source Sans 3', sans-serif;
      color: var(--text-main);
      background: transparent;
      outline: none;
    }

    .info-table .field-value input:focus,
    .info-table .field-value select:focus {
      background: rgba(23, 107, 69, 0.03);
    }

    .info-table .field-value input::placeholder {
      color: #c0c8c4;
    }

    .info-table .field-value select {
      padding-right: 32px;
    }

    .info-table .field-value select:hover {
      background-color: rgba(23, 107, 69, 0.04);
    }

    /* ── FAMILY TABLE ── */
    .family-doc-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 16px;
    }

    .family-doc-table thead th {
      background: var(--primary-dark);
      color: white;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 9px 10px;
      border: 1px solid var(--primary-dark);
      text-align: left;
    }

    .family-doc-table thead th:first-child {
      width: 35px;
      text-align: center;
    }

    .family-doc-table tbody td {
      border: 1px solid var(--border);
      padding: 0;
      vertical-align: middle;
    }

    .family-doc-table tbody td:first-child {
      text-align: center;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--text-muted);
      background: #f8faf9;
      padding: 6px;
      width: 35px;
    }

    .family-doc-table tbody td input,
    .family-doc-table tbody td select {
      width: 100%;
      border: none;
      padding: 7px 8px;
      font-size: 0.83rem;
      font-family: 'Source Sans 3', sans-serif;
      color: var(--text-main);
      background: transparent;
      outline: none;
    }

    .family-doc-table tbody td input:focus,
    .family-doc-table tbody td select:focus {
      background: rgba(23, 107, 69, 0.03);
    }

    .family-doc-table tbody td input::placeholder {
      color: #c0c8c4;
    }

    .family-doc-table tbody td select {
      padding-right: 32px;
    }

    .family-doc-table tbody td select:hover {
      background-color: rgba(23, 107, 69, 0.04);
    }

    .family-doc-table tbody tr:hover td {
      background: rgba(23, 107, 69, 0.02);
    }

    .family-doc-table tbody tr:hover td:first-child {
      background: #f0f4f2;
    }

    /* ── ISCAG STUDENTS ROW ── */
    .students-row {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 20px 0;
      padding: 12px 16px;
      background: #f8faf9;
      border: 1px solid var(--border);
      border-radius: 8px;
    }

    .students-row label {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--text-main);
    }

    .students-row input,
    .students-row select {
      width: 100px;
      padding: 6px 10px;
      border: 1.5px solid var(--border);
      border-radius: 6px;
      font-size: 0.85rem;
      font-family: 'Source Sans 3', sans-serif;
      color: var(--text-main);
      text-align: left;
      background-color: white;
    }

    .students-row select {
      padding-right: 32px !important;
      text-align: left;
    }

    .students-row input:focus,
    .students-row select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    /* ── CHARACTER REFERENCE ── */
    .char-ref-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }

    .char-ref-field label {
      display: block;
      font-size: 0.73rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-bottom: 5px;
    }

    .char-ref-field input {
      width: 100%;
      padding: 8px 12px;
      border: 1.5px solid var(--border);
      border-radius: 7px;
      font-size: 0.85rem;
      font-family: 'Source Sans 3', sans-serif;
      color: var(--text-main);
      background: white;
    }

    .char-ref-field input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
    }

    /* ── REQUIRED DOCUMENTS ── */
    .docs-and-reserved {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 20px;
    }

    .required-docs-list {
      list-style: none;
      padding: 0;
    }

    .required-docs-list li {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 0;
      font-size: 0.83rem;
      color: var(--text-main);
    }

    .required-docs-list li svg {
      width: 16px;
      height: 16px;
      fill: var(--primary);
      flex-shrink: 0;
    }

    /* ── UNIT CARDS ── */
    .unit-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }

    .unit-card {
      padding: 0;
      border: 2px solid var(--border);
      border-radius: 12px;
      text-align: center;
      cursor: pointer;
      transition: all 0.25s ease;
      background: white;
      position: relative;
      overflow: hidden;
    }

    .unit-card input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    .unit-card.selected {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.12), 0 4px 16px rgba(23, 107, 69, 0.1);
    }

    .unit-card:hover {
      border-color: var(--primary-light);
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }

    .unit-card-thumb {
      position: relative;
      width: 100%;
      height: 150px;
      overflow: hidden;
      background: #e8ece9;
    }

    .unit-card-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }

    .unit-card:hover .unit-card-thumb img {
      transform: scale(1.05);
    }

    .unit-card-thumb-overlay {
      position: absolute;
      top: 8px;
      left: 8px;
      padding: 3px 10px;
      border-radius: 5px;
      background: rgba(15, 92, 58, 0.85);
      color: white;
      font-size: 0.67rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      backdrop-filter: blur(4px);
    }

    .unit-card-check {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: var(--primary);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 2;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .unit-card-check svg {
      width: 14px;
      height: 14px;
      fill: white;
    }

    .unit-card.selected .unit-card-check {
      display: flex;
    }

    .unit-card-body {
      padding: 14px 14px 10px;
    }

    .unit-card-label {
      font-family: 'Lora', serif;
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 3px;
    }

    .unit-card-sub {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-bottom: 0;
    }

    .unit-card-view {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      width: 100%;
      padding: 10px 0;
      border: none;
      border-top: 1px solid var(--border);
      background: var(--primary-dark);
      color: white;
      font-size: 0.73rem;
      font-weight: 700;
      font-family: 'Source Sans 3', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      cursor: pointer;
      transition: background 0.2s;
    }

    .unit-card-view:hover {
      background: var(--primary-light);
    }

    .unit-card-view svg {
      width: 14px;
      height: 14px;
      fill: currentColor;
    }

    /* ── AVAILABILITY BADGES ── */
    .avail-badge {
      font-size: 0.65rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 10px;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .avail-badge.status-ok { background: rgba(47,138,96,0.1); color: var(--success); }
    .avail-badge.status-low { background: rgba(199,154,43,0.1); color: var(--warning); }
    .avail-badge.status-full { background: rgba(139, 46, 46, 0.1); color: var(--danger); }

    .unit-card.unit-full {
      opacity: 0.9;
      cursor: pointer;
      filter: none;
      border-color: #fecaca;
    }
    .unit-card.unit-full .unit-card-label {
      color: #7f1d1d;
    }
    .unit-card.unit-full:hover {
      border-color: var(--border) !important;
      transform: none !important;
    }
    .unit-card.unit-full .unit-card-check {
      display: none !important;
    }

    .unit-card.selected .unit-card-view {
      background: var(--primary);
    }

    .unit-type-note {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin-bottom: 14px;
    }

    /* ── CHECKBOXES ── */
    .form-check {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      margin-bottom: 10px;
    }

    .form-check-input {
      margin-top: 3px;
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: var(--primary);
      flex-shrink: 0;
    }

    .form-check-label {
      cursor: pointer;
      flex: 1;
      font-size: 0.85rem;
      color: var(--text-muted);
      line-height: 1.5;
    }

    /* ── FORM FOOTER ── */
    .form-doc-footer {
      background: var(--primary-dark);
      color: rgba(255, 255, 255, 0.75);
      padding: 16px 32px;
      text-align: center;
      font-size: 0.72rem;
      line-height: 1.8;
    }

    .form-doc-footer .footer-address {
      font-weight: 600;
      color: white;
    }

    .form-doc-footer .footer-contacts {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-top: 4px;
      flex-wrap: wrap;
    }

    .form-doc-footer .footer-contacts span {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .form-doc-footer .footer-contacts svg {
      width: 12px;
      height: 12px;
      fill: var(--accent);
    }

    /* ── STEP NAVIGATION BUTTONS ── */
    .form-submit-row {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      align-items: center;
      margin-top: 28px;
      padding: 20px 32px;
      border-top: 2px solid var(--primary);
      background: #f8faf9;
    }

    .btn-cancel,
    .btn-secondary {
      padding: 10px 28px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      background: white;
      color: var(--text-muted);
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.18s;
      font-family: inherit;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 42px;
    }

    .btn-cancel:hover {
      border-color: var(--danger);
      color: var(--danger);
      background: rgba(139, 46, 46, 0.04);
    }

    .btn-secondary:hover {
      border-color: var(--primary);
      color: var(--primary);
    }

    .btn-submit,
    .btn-primary {
      padding: 10px 32px;
      border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
      color: white;
      font-size: 0.85rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.18s;
      box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
      font-family: inherit;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 42px;
    }

    .btn-submit:hover,
    .btn-primary:hover {
      box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
      transform: translateY(-1px);
    }

    .btn-primary:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-primary svg,
    .btn-secondary svg,
    .btn-submit svg {
      width: 15px;
      height: 15px;
      fill: currentColor;
    }


    /* ═══════════════════════════════════════════
       STEP 2: DOCUMENT CARDS  
       ═══════════════════════════════════════════ */
    .doc-cards-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 28px;
    }

    .doc-card {
      background: white;
      border-radius: 14px;
      border: 1.5px solid var(--border);
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
      overflow: hidden;
      transition: all 0.25s ease;
      animation: slideUp 0.4s ease backwards;
    }

    .doc-card:nth-child(1) {
      animation-delay: 0.05s;
    }

    .doc-card:nth-child(2) {
      animation-delay: 0.1s;
    }

    .doc-card:nth-child(3) {
      animation-delay: 0.15s;
    }

    .doc-card:nth-child(4) {
      animation-delay: 0.2s;
    }

    .doc-card:nth-child(5) {
      animation-delay: 0.25s;
    }

    .doc-card:hover {
      box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
      transform: translateY(-2px);
    }

    .doc-card.uploaded {
      border-color: rgba(47, 138, 96, 0.35);
    }

    .doc-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px 12px;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(to right, rgba(26, 58, 92, 0.02), transparent);
    }

    .doc-card-header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .doc-card-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .doc-card-icon svg {
      width: 18px;
      height: 18px;
      fill: white;
    }

    .doc-card.uploaded .doc-card-icon {
      background: linear-gradient(135deg, var(--success), #3da870);
    }

    .doc-card-title {
      font-family: 'Lora', serif;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0;
    }

    .doc-card-note {
      font-size: 0.72rem;
      color: var(--text-muted);
      margin: 2px 0 0;
    }

    .doc-card-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 12px;
      border-radius: 20px;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      white-space: nowrap;
    }

    .doc-card-status.pending {
      background: rgba(199, 154, 43, 0.12);
      color: var(--warning);
    }

    .doc-card-status.uploaded {
      background: rgba(47, 138, 96, 0.12);
      color: var(--success);
    }

    .doc-card-status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
    }

    .doc-card-status.pending .doc-card-status-dot {
      background: var(--warning);
    }

    .doc-card-status.uploaded .doc-card-status-dot {
      background: var(--success);
    }

    /* Upload Zone */
    .doc-upload-zone {
      padding: 20px;
      min-height: 180px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .doc-upload-dropzone {
      width: 100%;
      min-height: 150px;
      border: 2px dashed var(--border);
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      cursor: pointer;
      transition: all 0.25s ease;
      background: #fafdf9;
      padding: 20px;
      position: relative;
    }

    .doc-upload-dropzone:hover {
      border-color: var(--primary);
      background: rgba(23, 107, 69, 0.03);
    }

    .doc-upload-dropzone.dragover {
      border-color: var(--accent);
      background: rgba(199, 154, 43, 0.06);
      transform: scale(1.01);
    }

    .doc-upload-dropzone input[type="file"] {
      display: none;
    }

    .doc-upload-dropzone svg.upload-icon {
      width: 40px;
      height: 40px;
      fill: var(--text-muted);
      opacity: 0.5;
      transition: all 0.2s;
    }

    .doc-upload-dropzone:hover svg.upload-icon {
      fill: var(--primary);
      opacity: 0.8;
    }

    .doc-upload-dropzone .upload-text {
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--text-muted);
      text-align: center;
    }

    .doc-upload-dropzone .upload-text strong {
      color: var(--primary);
    }

    .doc-upload-dropzone .upload-hint {
      font-size: 0.7rem;
      color: var(--text-muted);
      opacity: 0.7;
    }

    /* Preview */
    .doc-preview-wrap {
      width: 100%;
      position: relative;
      display: none;
    }

    .doc-preview-wrap.visible {
      display: block;
    }

    .doc-preview-img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      object-position: center top;
      border-radius: 10px;
      border: 1.5px solid var(--border);
      background: #fafdf9;
      cursor: pointer;
      transition: all 0.2s;
    }

    .doc-preview-img:hover {
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .doc-preview-actions {
      display: flex;
      gap: 8px;
      margin-top: 10px;
      justify-content: center;
    }

    .doc-preview-btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 14px;
      border-radius: 7px;
      font-size: 0.75rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.18s;
      border: none;
      font-family: inherit;
    }

    .doc-preview-btn.change {
      background: var(--content-bg);
      color: var(--text-muted);
      border: 1.5px solid var(--border);
    }

    .doc-preview-btn.change:hover {
      border-color: var(--primary);
      color: var(--primary);
    }

    .doc-preview-btn.remove {
      background: rgba(139, 46, 46, 0.08);
      color: var(--danger);
      border: 1.5px solid rgba(139, 46, 46, 0.2);
    }

    .doc-preview-btn.remove:hover {
      background: rgba(139, 46, 46, 0.15);
    }

    .doc-preview-btn.view {
      background: rgba(23, 107, 69, 0.08);
      color: var(--primary);
      border: 1.5px solid rgba(23, 107, 69, 0.2);
    }

    .doc-preview-btn.view:hover {
      background: rgba(23, 107, 69, 0.15);
    }

    .doc-preview-btn svg {
      width: 13px;
      height: 13px;
      fill: currentColor;
    }

    /* ── DUAL UPLOAD (Front/Back) ── */
    .doc-dual-upload {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      width: 100%;
    }

    .doc-dual-slot {
      position: relative;
    }

    .doc-dual-label {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .doc-dual-label svg {
      width: 14px;
      height: 14px;
      fill: var(--accent);
    }

    .doc-dual-slot .doc-upload-dropzone {
      min-height: 130px;
      padding: 16px;
    }

    .doc-dual-slot .doc-upload-dropzone svg.upload-icon {
      width: 30px;
      height: 30px;
    }

    .doc-dual-slot .doc-upload-dropzone .upload-text {
      font-size: 0.75rem;
    }

    .doc-dual-slot .doc-upload-dropzone .upload-hint {
      font-size: 0.65rem;
    }

    .doc-dual-slot .doc-preview-img {
      height: 150px;
    }

    .doc-dual-slot .doc-preview-actions {
      gap: 4px;
    }

    .doc-dual-slot .doc-preview-btn {
      padding: 5px 10px;
      font-size: 0.7rem;
    }

    .doc-dual-slot .doc-preview-btn svg {
      width: 11px;
      height: 11px;
    }

    /* ── SUBMIT SECTION ── */
    .submit-section {
      background: white;
      border-radius: 14px;
      border: 1px solid var(--border);
      box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
      padding: 24px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .submit-section-text h4 {
      font-family: 'Lora', serif;
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin: 0 0 4px;
    }

    .submit-section-text p {
      font-size: 0.8rem;
      color: var(--text-muted);
      margin: 0;
    }

    .submit-section-actions {
      display: flex;
      gap: 12px;
      flex-shrink: 0;
    }

    /* ── Toast ── */
    .toast-notification {
      position: fixed;
      top: 24px;
      right: 24px;
      padding: 14px 22px;
      border-radius: 10px;
      z-index: 99999;
      font-weight: 600;
      font-size: 0.9rem;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
      max-width: 400px;
      color: white;
      animation: slideUp 0.3s ease;
    }

    /* ── SCROLLBAR ── */
    .main-content::-webkit-scrollbar {
      width: 6px;
    }

    .main-content::-webkit-scrollbar-track {
      background: transparent;
    }

    .main-content::-webkit-scrollbar-thumb {
      background: var(--border);
      border-radius: 3px;
    }

    .main-content::-webkit-scrollbar-thumb:hover {
      background: #b0bcc8;
    }

    /* ── ANIMATIONS ── */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(16px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .doc-cards-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      :root {
        --sidebar-width: 220px;
      }

      .page-body {
        padding: 18px;
      }

      .unit-cards {
        grid-template-columns: 1fr;
      }

      .form-doc-header-top {
        flex-direction: column;
        text-align: center;
      }

      .date-photo-row {
        flex-direction: column-reverse;
        align-items: center;
        gap: 12px;
        padding: 16px 18px 12px;
      }

      .date-photo-row .date-group {
        width: 100%;
        justify-content: center;
      }

      .docs-and-reserved,
      .char-ref-grid,
      .signature-grid {
        grid-template-columns: 1fr;
      }

      .form-doc-body {
        padding: 16px 18px 24px;
      }

      .form-submit-row {
        padding: 16px 18px;
        justify-content: center;
      }

      .stepper-body {
        padding: 16px 18px;
      }

      .stepper-step {
        min-width: 90px;
      }

      .stepper-label {
        font-size: 0.68rem;
      }

      .submit-section {
        flex-direction: column;
        text-align: center;
      }

      .doc-cards-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php 
      $active_page = 'apartment_apply'; 
      include BASE_PATH . '/app/views/user/sidebar.php'; 
    ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title">Tenant Application</div>
          <div class="top-bar-subtitle">Complete your application form and upload required documents</div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
          <span class="sep">›</span>
          <span class="current">Tenant Application</span>
        </div>

        <!-- ═══════════════════════════════════════
             STEPPER PROGRESS BAR
             ═══════════════════════════════════════ -->
        <div class="stepper-wrapper">
          <div class="stepper-card">
            <div class="stepper-hero">
              <h2>
                <svg viewBox="0 0 24 24">
                  <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                </svg>
                Tenant Application Process
              </h2>
              <p>Complete each step to finalize your apartment application</p>
            </div>
            <div class="stepper-body">
              <div class="stepper-steps">
                <!-- Step 1 -->
                <div class="stepper-step active" id="stepper-step-1" onclick="goToStep(1)">
                  <div class="stepper-circle">1</div>
                  <div class="stepper-label">Application Form</div>
                  <div class="stepper-sublabel">Personal Info & Unit</div>
                </div>

                <!-- Connector 1-2 -->
                <div class="stepper-connector" id="connector-1-2">
                  <div class="stepper-connector-fill"></div>
                </div>

                <!-- Step 2 -->
                <div class="stepper-step" id="stepper-step-2" onclick="goToStep(2)">
                  <div class="stepper-circle">2</div>
                  <div class="stepper-label">Upload Documents</div>
                  <div class="stepper-sublabel">Required Files</div>
                </div>
              </div>

              <div class="stepper-progress-info">
                <div class="stepper-progress-text">
                  Step <strong id="current-step-label">1</strong> of <strong>2</strong>
                </div>
                <div class="stepper-progress-bar-outer">
                  <div class="stepper-progress-bar-inner" id="stepper-bar-fill" style="width: 50%;"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══════════════════════════════════════
             STEP 1: APPLICATION FORM
             ═══════════════════════════════════════ -->
        <div class="step-panel active" id="step-panel-1">
          <div class="form-document">

            <!-- ── FORM HEADER ── -->
            <div class="form-doc-header">
              <div class="form-doc-header-top">
                <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo" />
                <div class="form-doc-header-text">
                  <div class="arabic-line" style="font-size:1.1rem; margin-bottom:5px;">بِسْم. اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
                  <div class="org-name-ar" style="font-size:1rem; font-weight:700; color:#0f5c3a;">مركز البحوث الإسلامية و الدعوة و الإرشاد في الفلبين</div>
                  <div class="org-name-en" style="font-size:0.9rem; font-weight:700;">Islamic Studies, Call and Guidance of the Philippines</div>
                  <div class="sec-reg" style="font-size:0.7rem; opacity:0.8;">SEC. REG. NO. 0000185967</div>
                </div>
                <div style="width: 100px;"></div> <!-- Spacer for perfect symmetry -->
              </div>
              <div class="form-doc-title-bar">
                <span class="form-doc-title">Tenant Application Form</span>
              </div>
            </div>

            <!-- ── DATE + PHOTO ROW ── -->
            <div class="date-photo-row">
              <!-- Date of Application (Left) -->
              <div class="date-group">
                <label for="date-application">Date of Application:</label>
                <input type="date" id="date-application" value="<?= htmlspecialchars($appData['date_applied'] ?? date('Y-m-d')) ?>" />
              </div>

              <!-- 2x2 Photo Upload (right) -->
              <div class="photo-upload-box" id="photo-upload-box" title="View/Edit Photo" onclick="handlePhotoBoxClick(event)">
                <svg viewBox="0 0 24 24" id="photo-placeholder-icon">
                  <path
                    d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                </svg>
                <span id="photo-label-text">2x2 Photo</span>
                <input type="file" accept="image/*" id="photo-input" />
                
                <!-- Controls that appear on hover if photo exists -->
                <div class="photo-controls" id="photo-controls" style="display:none;">
                   <button type="button" class="photo-btn btn-edit-photo" onclick="handleEditPhoto(event)">Edit Photo</button>
                   <button type="button" class="photo-btn btn-remove-photo" onclick="handleRemovePhoto(event)">Remove</button>
                </div>
              </div>
            </div>

            <!-- ── FORM BODY ── -->
            <div class="form-doc-body">

              <!-- ══ SECTION 1: APPLICANT'S INFORMATION ══ -->
              <div class="doc-section-title" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="display: flex; align-items: center; gap: 8px;">
                  <svg viewBox="0 0 24 24">
                    <path
                      d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                  </svg>
                  Guest's Information
                </div>
                <button type="button" onclick="useMyDetails()" class="btn-topbar" style="font-size: 0.72rem; padding: 6px 14px; background: rgba(15, 92, 58, 0.05); color: #0f5c3a; border: 1.5px solid rgba(15, 92, 58, 0.15); border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <svg viewBox="0 0 24 24" style="width:14px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/></svg>
                    Use my Details
                </button>
              </div>

              <!-- Row 1 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Family Name:</td>
                  <td class="field-value"><input type="text" placeholder="Surname / Family name" id="family-name" value="<?= htmlspecialchars($appData['familyname'] ?? '') ?>" />
                  </td>
                  <td class="field-label">Given Name:</td>
                  <td class="field-value"><input type="text" placeholder="First name" id="given-name" value="<?= htmlspecialchars($appData['givenname'] ?? '') ?>" /></td>
                  <td class="field-label">Muslim Name:</td>
                  <td class="field-value"><input type="text" placeholder="Arabic / Muslim name" id="muslim-name" value="<?= htmlspecialchars($appData['muslimname'] ?? '') ?>" /></td>
                  <td class="field-label" style="min-width:40px;">M.I.</td>
                  <td class="field-value" style="width:60px;"><input type="text" placeholder="—" id="mi" maxlength="3"
                      style="text-align:center;" value="<?= htmlspecialchars($appData['middlename'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- Row 2 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Date of Birth:</td>
                  <td class="field-value"><input type="date" id="dob" value="<?= htmlspecialchars($appData['birthdate'] ?? '') ?>" /></td>
                  <td class="field-label" style="min-width:50px;">Age:</td>
                  <td class="field-value" style="width:70px;"><input type="number" id="age" placeholder="—" min="0" value="<?= htmlspecialchars($appData['age'] ?? '') ?>"
                      style="text-align:center;" /></td>
                  <td class="field-label">Place of Birth:</td>
                  <td class="field-value"><input type="text" placeholder="City / Province" id="pob" value="<?= htmlspecialchars($appData['pob'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- Row 3 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Address:</td>
                  <td class="field-value" colspan="5"><input type="text" placeholder="Complete current address"
                      id="address" value="<?= htmlspecialchars($appData['address'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- Row 4 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Date of Shahadah:</td>
                  <td class="field-value"><input type="date" id="shahadah-date" value="<?= htmlspecialchars($appData['dateofshahadah'] ?? '') ?>" /></td>
                  <td class="field-label">Tribal Affiliation:</td>
                  <td class="field-value">
                    <select id="tribal">
                      <option value="">— Select Tribe —</option>
                      <optgroup label="Bangsamoro Tribes">
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Maranao' ? 'selected' : '' ?>>Maranao</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Tausug' ? 'selected' : '' ?>>Tausug</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Maguindanaon' ? 'selected' : '' ?>>Maguindanaon</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Sama' ? 'selected' : '' ?>>Sama</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Yakan' ? 'selected' : '' ?>>Yakan</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Iranun' ? 'selected' : '' ?>>Iranun</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Kalagan' ? 'selected' : '' ?>>Kalagan</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Kolibugan' ? 'selected' : '' ?>>Kolibugan</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Molbog' ? 'selected' : '' ?>>Molbog</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Palawani' ? 'selected' : '' ?>>Palawani</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Sangil' ? 'selected' : '' ?>>Sangil</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Jama Mapun' ? 'selected' : '' ?>>Jama Mapun</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Badjao' ? 'selected' : '' ?>>Badjao</option>
                      </optgroup>
                      <optgroup label="Other Ethnic Groups">
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Tagalog' ? 'selected' : '' ?>>Tagalog</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Visayan' ? 'selected' : '' ?>>Visayan</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Ilocano' ? 'selected' : '' ?>>Ilocano</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Bicolano' ? 'selected' : '' ?>>Bicolano</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Pampango' ? 'selected' : '' ?>>Pampango</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Pangasinense' ? 'selected' : '' ?>>Pangasinense</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Igorot' ? 'selected' : '' ?>>Igorot</option>
                        <option <?= ($appData['tribalaffliation'] ?? '') == 'Lumad' ? 'selected' : '' ?>>Lumad</option>
                        <option value="Other">Other / Not Listed</option>
                      </optgroup>
                    </select>
                  </td>
                  <td class="field-label">Phone No.:</td>
                  <td class="field-value"><input type="tel" placeholder="09XX-XXX-XXXX" id="phone" value="<?= htmlspecialchars($appData['phone'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- Row 5 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">No. of Muslim in the Family:</td>
                  <td class="field-value" style="width:80px;"><input type="number" placeholder="—" id="muslim-count"
                      min="0" style="text-align:center;" value="<?= htmlspecialchars($appData['numofmuslim'] ?? '') ?>" /></td>
                  <td class="field-label">Civil Status:</td>
                  <td class="field-value">
                    <select id="civil-status">
                      <option value="">— Select —</option>
                      <option <?= ($appData['civil_status'] ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
                      <option <?= ($appData['civil_status'] ?? '') == 'Married' ? 'selected' : '' ?>>Married</option>
                      <option <?= ($appData['civil_status'] ?? '') == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                      <option <?= ($appData['civil_status'] ?? '') == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                    </select>
                  </td>
                  <td class="field-label">Sex:</td>
                  <td class="field-value">
                    <select id="sex">
                      <option value="">— Select —</option>
                      <option <?= ($appData['sex'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                      <option <?= ($appData['sex'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                  </td>
                </tr>
              </table>

              <!-- Row 6 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Occupation:</td>
                  <td class="field-value"><input type="text" placeholder="Current job or occupation" id="occupation" value="<?= htmlspecialchars($appData['occupation'] ?? '') ?>" />
                  </td>
                  <td class="field-label">Company Name:</td>
                  <td class="field-value" colspan="3"><input type="text" placeholder="Employer / Company" id="company" value="<?= htmlspecialchars($appData['companyname'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- Row 7 -->
              <table class="info-table">
                <tr>
                  <td class="field-label">Company Business Address:</td>
                  <td class="field-value"><input type="text" placeholder="Business address" id="company-address" value="<?= htmlspecialchars($appData['companyadd'] ?? '') ?>" /></td>
                  <td class="field-label">Company Phone Number.:</td>
                  <td class="field-value" style="width:180px;"><input type="tel" placeholder="Office phone"
                      id="company-phone" value="<?= htmlspecialchars($appData['companyphone'] ?? '') ?>" /></td>
                </tr>
              </table>

              <!-- ══ SECTION 2: PREFERRED UNIT TYPE ══ -->
              <div class="doc-section-title" style="margin-top:28px;">
                <svg viewBox="0 0 24 24">
                  <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
                </svg>
                Preferred Unit Type (Reserved For Office Use)
              </div>
              <p class="unit-type-note">Select your preferred apartment unit type. Final assignment is subject to
                availability.</p>

              <div class="unit-cards" id="unit-cards-container">
                <!-- Dynamically loaded from API -->
                <div style="grid-column: 1/-1; text-align: center; padding: 32px; background: #f9fafb; border-radius: 12px; border: 1px dashed var(--border);">
                  <div class="loader" style="margin: 0 auto 16px;"></div>
                  <p style="color: var(--text-muted); font-size: 0.9rem;">Fetching available apartment units...</p>
                </div>
              </div>

              <!-- ══ SECTION 3: FAMILY MEMBERS ══ -->
              <div id="family-members-section">
                <div class="doc-section-title">
                  <svg viewBox="0 0 24 24">
                    <path
                      d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                  </svg>
                  Complete List of Family Members to Occupy the Unit
                </div>

                <table class="family-doc-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Relation</th>
                      <th style="width:80px;">Age</th>
                      <th style="width:130px;">Religion</th>
                    </tr>
                  </thead>
                  <tbody id="family-members-body">
                    <!-- Rendered by JS -->
                  </tbody>
                </table>
              </div>

              <!-- ══ ISCAG STUDENTS ══ -->
              <div class="students-row" style="flex-direction:column; align-items:flex-start; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px; width:100%;">
                  <label for="iscag-students" style="margin:0;"><strong>How many members of the family are students in ISCAG School?</strong></label>
                  <select id="iscag-students" style="width:100px;">
                    <?php 
                      $currentStudents = $appData['iscag_students'] ?? 0;
                      for ($i=0; $i<=10; $i++): 
                    ?>
                      <option value="<?= $i ?>" <?= $currentStudents == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div id="iscag-student-names-container" style="display:none; width:100%;">
                  <div style="background:rgba(15,92,58,0.03); border:1.5px solid rgba(15,92,58,0.12); border-radius:10px; padding:14px 16px;">
                    <p style="font-size:0.78rem; color:#0f5c3a; font-weight:700; margin:0 0 10px; display:flex; align-items:center; gap:6px;">
                      <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:#0f5c3a;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                      Please provide the name(s) of the student(s) enrolled in ISCAG School for reference.
                    </p>
                    <div id="iscag-names-list" style="display:flex; flex-direction:column; gap:8px;"></div>
                  </div>
                </div>
              </div>

              <!-- ══ ISCAG EMPLOYMENT ══ -->
              <div class="students-row" style="flex-direction:column; align-items:flex-start; gap:12px; margin-top: 20px;">
                <div style="display:flex; align-items:center; gap:12px; width:100%;">
                  <label for="iscag-employee" style="margin:0;"><strong>Are you working in ISCAG?</strong></label>
                  <select id="iscag-employee" style="width:100px;">
                    <option value="0" <?= ($appData['is_iscag_employee'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                    <option value="1" <?= ($appData['is_iscag_employee'] ?? 0) == 1 ? 'selected' : '' ?>>Yes</option>
                  </select>
                </div>
                <div id="iscag-job-container" style="display:<?= ($appData['is_iscag_employee'] ?? 0) == 1 ? 'block' : 'none' ?>; width:100%;">
                  <div style="background:rgba(201,154,43,0.03); border:1.5px solid rgba(201,154,43,0.12); border-radius:10px; padding:12px 16px;">
                    <input type="text" id="iscag-job-role" placeholder="Specify your Job or Role in ISCAG" value="<?= htmlspecialchars($appData['iscag_job_role'] ?? '') ?>" style="width: 100%; border-color: rgba(15,92,58,0.2); font-size: 0.85rem; text-align: left;" />
                  </div>
                </div>
              </div>

              <!-- ══ CHARACTER REFERENCE ══ -->
              <div class="doc-section-title">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                </svg>
                Character Reference
              </div>
              <div class="char-ref-grid">
                <div class="char-ref-field">
                  <label for="ref-name">Name:</label>
                  <input type="text" id="ref-name" placeholder="Full name of reference person" value="<?= htmlspecialchars($appData['ref_name'] ?? '') ?>" />
                </div>
                <div class="char-ref-field">
                  <label for="ref-contact">Contact No.:</label>
                  <input type="tel" id="ref-contact" placeholder="09XX-XXX-XXXX" value="<?= htmlspecialchars($appData['ref_contact'] ?? '') ?>" />
                </div>
              </div>

              <div class="docs-and-reserved">
                <div>
                  <div class="doc-section-title" style="margin-top:0;">
                    <svg viewBox="0 0 24 24">
                      <path
                        d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                    </svg>
                    Please Submit the Following Documents
                  </div>
                  <ul class="required-docs-list">
                    <li><svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                      </svg>Proof of Income</li>
                    <li><svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                      </svg>Valid ID (photocopy)</li>
                    <li><svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                      </svg>Birth Certificate (photocopy)</li>
                    <li><svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                      </svg>NBI / Police Clearance</li>
                  </ul>
                </div>
                <div>
                  <!-- Reserved for info — read-only, shows on print -->
                </div>
              </div>
            </div><!-- /.form-doc-body -->

            <!-- ══ DECLARATION ══ -->
            <div style="padding: 0 32px;">
              <div class="doc-section-title">
                <svg viewBox="0 0 24 24">
                  <path
                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z" />
                </svg>
                Declaration and Consent
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="decl1" />
                <label class="form-check-label" for="decl1">
                  I certify that all information provided in this application is true, accurate, and complete to the
                  best
                  of my knowledge.
                </label>
              </div>
              <div class="form-check" style="margin-bottom:16px;">
                <input class="form-check-input" type="checkbox" id="decl2" />
                <label class="form-check-label" for="decl2">
                  I understand that submitting false information may result in immediate disqualification. I agree to
                  comply with all ISCAG apartment rules and regulations.
                </label>
              </div>
            </div>

            <!-- ══ STEP 1 NAVIGATION ══ -->
            <div class="form-submit-row">
              <a href="<?= url('/user/dashboard') ?>" class="btn-cancel">Cancel</a>
              <button class="btn-submit" type="button" id="next-step-btn">
                Next: Upload Documents
                <svg viewBox="0 0 24 24">
                  <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z" />
                </svg>
              </button>
            </div>

            <!-- ── FORM FOOTER ── -->
            <div class="form-doc-footer">
              <div class="footer-address">Jose Abad Santos Street, Salitran I, City of Dasmariñas, Cavite, Philippines -
                4114</div>
              <div class="footer-contacts">
                <span>
                  <svg viewBox="0 0 24 24">
                    <path
                      d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                  </svg>
                  iscagphilippines@gmail.com
                </span>
                <span>
                  <svg viewBox="0 0 24 24">
                    <path
                      d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                  </svg>
                  (046) 4161589
                </span>
                <span>
                  <svg viewBox="0 0 24 24">
                    <path
                      d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                  </svg>
                  /iscagphilippines
                </span>
              </div>
            </div>
          </div><!-- /.form-document -->
        </div><!-- /#step-panel-1 -->


        <!-- ═══════════════════════════════════════
             STEP 2: UPLOAD DOCUMENTS
             ═══════════════════════════════════════ -->
        <div class="step-panel" id="step-panel-2">

          <!-- ─── Upload Progress Header ─── -->
          <div style="max-width:900px;margin:0 auto;">
            <div class="req-progress-header"
              style="background:white;border-radius:14px;border:1px solid var(--border);box-shadow:0 2px 16px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:24px;">
              <div
                style="background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));padding:24px 32px 20px;position:relative;overflow:hidden;">
                <div
                  style="position:absolute;right:-20px;bottom:-20px;width:120px;height:120px;border-radius:50%;background:rgba(201,168,76,0.1);">
                </div>
                <h2
                  style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:white;margin:0 0 4px;display:flex;align-items:center;gap:8px;">
                  <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--accent-light);">
                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" />
                  </svg>
                  Upload Required Documents
                </h2>
                <p style="font-size:0.82rem;color:rgba(255,255,255,0.68);margin:0;">Upload clear images of each required
                  document to finalize your application.</p>
              </div>
              <div style="padding:20px 32px;display:flex;align-items:center;gap:24px;">
                <div style="flex:1;">
                  <div
                    style="display:flex;justify-content:space-between;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:6px;">
                    <span>Upload Progress</span>
                    <span id="progress-percent">0%</span>
                  </div>
                  <div style="width:100%;height:10px;border-radius:5px;background:var(--border);overflow:hidden;">
                    <div id="progress-fill"
                      style="height:100%;border-radius:5px;background:linear-gradient(90deg,var(--primary-dark),var(--accent));transition:width 0.6s ease;width:0%;">
                    </div>
                  </div>
                </div>
                <div
                  style="font-family:'Lora',serif;font-size:1.5rem;font-weight:700;color:var(--primary-dark);white-space:nowrap;">
                  <span id="completed-count">0</span> / <span id="total-count">4</span>
                  <span style="font-size:0.85rem;font-weight:600;color:var(--text-muted);">Uploaded</span>
                </div>
              </div>
            </div>

            <!-- ─── Document Cards ─── -->
            <div class="doc-cards-grid" id="doc-cards-grid">
              <!-- Rendered by JS -->
            </div>

            <!-- ─── Submit Section ─── -->
            <div class="submit-section">
              <div class="submit-section-text">
                <h4>Ready to Submit?</h4>
                <p>Once all documents are uploaded, click "Submit Application" to complete your application.</p>
              </div>
              <div class="submit-section-actions">
                <button class="btn-secondary" id="back-to-step1-btn" type="button">
                  <svg viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                  </svg>
                  Back to Form
                </button>
                <button class="btn-primary" id="submit-docs-btn" disabled>
                  <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                  </svg>
                  Submit Application
                </button>
              </div>
            </div>
          </div>

        </div><!-- /#step-panel-2 -->

      </div><!-- /.page-body -->
    </div><!-- /.main-content -->
  </div><!-- /.app-wrapper -->

  <script src="<?= asset('JS/room-preview.js') ?>?v=<?= time() ?>"></script>
  <script>
    // ═══ DATA HELPERS ═══
    const STORAGE_KEYS = {
      user: 'mis_user',
      requests: 'mis_requests',
      apartments: 'mis_apartments',
      initialized: 'mis_data_init'
    };
    const PROFILE_FIELDS = ['name', 'email', 'sex', 'phone', 'address', 'dob', 'civil', 'occupation', 'arabicName', 'revertYear'];
    const DEFAULT_USER = {
      id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>',
      name: '<?= addslashes($_SESSION['name'] ?? "User") ?>',
      email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
      sex: '<?= addslashes($_SESSION['sex'] ?? $_SESSION['gender'] ?? "") ?>',
      phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', revertYear: '', apartment: '', profileComplete: false
    };
    const DEFAULT_REQUESTS = [{
        id: 'BUR-001',
        user: 'USR-001',
        type: 'burial_service',
        status: 'pending',
        date: '2026-03-15',
        updatedAt: '2026-03-15'
      },
      {
        id: 'APT-001',
        user: 'USR-001',
        type: 'apartment_application',
        status: 'approved',
        date: '2026-03-09',
        updatedAt: '2026-03-12'
      }
    ];
    const DEFAULT_APARTMENTS = [{
        id: 'APT-A1',
        name: 'Unit A-1 · Studio',
        price: 3500,
        available: 2,
        status: 'available'
      },
      {
        id: 'APT-A2',
        name: 'Unit A-2 · 1-Bedroom',
        price: 5000,
        available: 1,
        status: 'available'
      },
      {
        id: 'APT-B1',
        name: 'Unit B-1 · 2-Bedroom',
        price: 7500,
        available: 0,
        status: 'occupied'
      },
      {
        id: 'APT-B2',
        name: 'Unit B-2 · 2-Bedroom',
        price: 7500,
        available: 1,
        status: 'available'
      },
      {
        id: 'APT-C1',
        name: 'Unit C-1 · Family Suite',
        price: 10000,
        available: 0,
        status: 'reserved'
      }
    ];

    function initData() {
      if (!localStorage.getItem(STORAGE_KEYS.initialized)) {
        localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(DEFAULT_USER));
        localStorage.setItem(STORAGE_KEYS.apartments, JSON.stringify(DEFAULT_APARTMENTS));
        localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(DEFAULT_REQUESTS));
        localStorage.setItem(STORAGE_KEYS.initialized, '1');
      }
    }

    const DB_USER = <?= json_encode($dbUser) ?>;

    function getUser() {
      const raw = localStorage.getItem(STORAGE_KEYS.user);
      const user = raw ? JSON.parse(raw) : {
        ...DEFAULT_USER
      };

      // Sync with DB data — DB is the source of truth.
      // Even if empty, we overwrite localStorage/Mock defaults.
      Object.keys(DB_USER).forEach(key => {
        user[key] = DB_USER[key] || '';
      });
      return user;
    }

    function getProfileCompletion() {
      const user = getUser();
      const missing = [];
      let filled = 0;
      const labels = {
        name: 'Full Name',
        email: 'Email Address',
        sex: 'Sex',
        phone: 'Contact Number',
        address: 'Complete Address',
        dob: 'Date of Birth',
        civil: 'Civil Status',
        occupation: 'Occupation',
        arabicName: 'Muslim / Arabic Name',
        membership: 'ISCAG Membership'
      };
      PROFILE_FIELDS.forEach(k => {
        if (user[k] && String(user[k]).trim() !== '') {
          filled++;
        } else {
          missing.push(labels[k] || k);
        }
      });
      return {
        percentage: Math.round((filled / PROFILE_FIELDS.length) * 100),
        filled,
        total: PROFILE_FIELDS.length,
        missingFields: missing
      };
    }

    function addRequest(req) {
      const raw = localStorage.getItem(STORAGE_KEYS.requests);
      const requests = raw ? JSON.parse(raw) : [];
      if (!req.id) req.id = 'APT-' + String(requests.length + 1).padStart(3, '0');
      if (!req.date) req.date = new Date().toISOString().split('T')[0];
      if (!req.updatedAt) req.updatedAt = req.date;
      if (!req.status) req.status = 'pending';
      requests.push(req);
      localStorage.setItem(STORAGE_KEYS.requests, JSON.stringify(requests));
      return req;
    }

    initData();

    // ── Load user nav ──
    const user = getUser();
    const navName = document.getElementById('nav-name');
    const navAvatar = document.getElementById('nav-avatar');
    if (navName) navName.textContent = user.name;
    if (navAvatar) {
      const photo = localStorage.getItem('mis_user_photo');
      if (photo) {
        navAvatar.textContent = '';
        navAvatar.style.backgroundImage = 'url(' + photo + ')';
        navAvatar.style.backgroundSize = 'cover';
        navAvatar.style.backgroundPosition = 'center';
      } else {
        navAvatar.textContent = user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
      }
    }

    // ── Set role label ──
    const navRole = document.getElementById('nav-role');
    if (navRole) {
      const isComplete = user.profileComplete;
      navRole.textContent = isComplete ? "<?= $_SESSION['role'] ?? 'Verified User' ?>" : 'Guest';
      navRole.style.color = isComplete ? 'var(--warning)' : 'var(--success)'
    }


    // ── Profile access gate ──
    const {
      percentage,
      missingFields
    } = getProfileCompletion();
    if (percentage < 100) {
      if (!document.getElementById('acm-keyframes')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'acm-keyframes';
        styleEl.textContent = `
        @keyframes acmFadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes acmSlideUp { from { opacity:0;transform:translateY(24px) scale(0.96); } to { opacity:1;transform:translateY(0) scale(1); } }
        @keyframes acmShake { 0%,100%{transform:translateX(0)} 10%,30%,50%,70%,90%{transform:translateX(-6px)} 20%,40%,60%,80%{transform:translateX(6px)} }
      `;
        document.head.appendChild(styleEl);
      }

      const missingHtml = missingFields.length > 0 ?
        `<div style="margin-top:16px;text-align:left;">
           <p style="font-size:0.78rem;color:#6f7f78;margin:0 0 8px;font-weight:600;">The following information is still required:</p>
           <ul style="margin:0;padding:0 0 0 18px;font-size:0.8rem;color:#1f2e2a;line-height:1.8;">
             ${missingFields.map(f => '<li>' + f + '</li>').join('')}
           </ul>
         </div>` : '';

      const modalHtml = `
      <div id="access-control-modal" style="
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:rgba(15,30,22,0.55);backdrop-filter:blur(6px);
        padding:24px;
        animation:acmFadeIn 0.3s ease;
      ">
        <div style="
          background:white;border-radius:16px;
          width:100%;max-width:440px;
          box-shadow:0 20px 60px rgba(0,0,0,0.25);
          overflow:hidden;
          animation:acmSlideUp 0.35s ease;
        ">
          <div style="height:4px;background:linear-gradient(90deg,#0f5c3a,#c79a2b);"></div>
          <div style="padding:32px 28px 24px;text-align:center;">
            <div style="margin-bottom:8px;">
              <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:#c79a2b;">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/>
              </svg>
            </div>
            <div style="position:relative;width:80px;height:80px;margin:0 auto 16px;">
              <svg viewBox="0 0 36 36" style="width:80px;height:80px;transform:rotate(-90deg);">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e8ece9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="${percentage >= 40 ? '#c79a2b' : '#8b2e2e'}" stroke-width="3"
                  stroke-dasharray="${percentage} ${100 - percentage}" stroke-linecap="round"
                  style="transition:stroke-dasharray 0.8s ease;"/>
              </svg>
              <span style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Lora',serif;font-size:1.1rem;font-weight:700;color:#0f5c3a;">${percentage}%</span>
            </div>
            <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#0f5c3a;margin:0 0 10px;">Profile Incomplete</h4>
            <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;">Access to this section is restricted until your profile is fully completed. Please update the required information to continue.</p>
            ${missingHtml}
          </div>
          <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
            <button id="acm-cancel-btn" style="
              padding:10px 22px;border-radius:8px;
              border:1.5px solid #d9e3de;background:white;
              color:#6f7f78;font-size:0.85rem;font-weight:600;
              cursor:pointer;transition:all 0.18s;
            ">Go to Dashboard</button>
            <button id="acm-primary-btn" style="
              padding:10px 22px;border-radius:8px;
              border:none;
              background:linear-gradient(135deg,#0f5c3a,#2f8a60);
              color:white;font-size:0.85rem;font-weight:700;
              cursor:pointer;transition:all 0.18s;
              box-shadow:0 4px 12px rgba(15,92,58,0.3);
            ">Go to Profile</button>
          </div>
        </div>
      </div>
    `;
      document.body.insertAdjacentHTML('beforeend', modalHtml);

      const modal = document.getElementById('access-control-modal');
      const modalBox = modal.querySelector('div > div');

      function shakeModal() {
        modalBox.style.animation = 'none';
        modalBox.offsetHeight;
        modalBox.style.animation = 'acmShake 0.5s ease';
      }

      document.getElementById('acm-primary-btn').addEventListener('click', () => {
        window.location.href = '<?= url('/user/profile') ?>';
      });
      document.getElementById('acm-cancel-btn').addEventListener('click', () => {
        window.location.href = '<?= url('/user/dashboard') ?>';
      });
      modal.addEventListener('click', e => {
        if (e.target === modal) {
          shakeModal();
        }
      });
    }

    // ═══ STEPPER NAVIGATION ═══
    let currentStep = 1;

    // Collect all Step 1 fields and POST to server
    function saveStep1ToServer() {
      const v = id => {
        const el = document.getElementById(id);
        return el ? el.value.trim() : '';
      };
      const unitRadio = document.querySelector('input[name="unit"]:checked');
      let roomtype = null;
      if (unitRadio) {
        const card = unitRadio.closest('.unit-card');
        if (card) {
          const labelEl = card.querySelector('.unit-card-label');
          if (labelEl) roomtype = labelEl.textContent.trim();
        } else {
          roomtype = unitRadio.value; // fallback
        }
      }

      const familyRows = document.querySelectorAll('#family-members-body tr');
      const familyData = [];
      familyRows.forEach(row => {
        const inputs = row.querySelectorAll('input, select');
        const name = inputs[0].value.trim();
        if (name) {
          familyData.push({
            name: name,
            relation: inputs[1].value,
            age: inputs[2].value,
            religion: inputs[3].value
          });
        }
      });

      const payload = {
        addinfo: {
          familyname: v('family-name'),
          givenname: v('given-name'),
          middlename: v('mi'),
          muslimname: v('muslim-name'),
          birthdate: v('dob'),
          age: parseInt(v('age')) || 0,
          pob: v('pob'),
          sex: v('sex'),
          address: v('address'),
          dateofshahadah: v('shahadah-date'),
          tribalaffliation: v('tribal'),
          numofmuslim: parseInt(v('muslim-count')) || 0,
          civil_status: v('civil-status'),
          occupation: v('occupation'),
          monthly_income: v('monthly-income'),
          companyname: v('company'),
          companyadd: v('company-address'),
          companyphone: v('company-phone'),
          ref_name: v('ref-name'),
          ref_contact: v('ref-contact'),
          iscag_students: parseInt(v('iscag-students')) || 0,
          iscag_student_names: getIscagStudentNames(),
          is_iscag_employee: parseInt(v('iscag-employee')) || 0,
          iscag_job_role: v('iscag-job-role'),
          date_applied: v('date-application'),
          family_data: JSON.stringify(familyData)
        },
        roomtype: roomtype
      };

      console.log("Saving payload:", payload); // Debug log

      return fetch('<?= url("/user/apartment/save") ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      }).then(r => r.json());
    }
    
    // Quiet background auto-save (Debounced)
    let autosaveTimer = null;
    function triggerAutosave() {
      if (currentStep !== 1) return;
      clearTimeout(autosaveTimer);
      autosaveTimer = setTimeout(() => {
        saveStep1ToServer()
          .then(res => { if (res.success) console.log("Progress auto-saved."); })
          .catch(err => console.warn("Auto-save failed:", err));
      }, 1000); // Save 1 second after last input
    }

    // Attach listeners to all Step 1 inputs for auto-saving
    function initStep1Listeners() {
      document.querySelectorAll('#step-panel-1 input, #step-panel-1 select').forEach(el => {
        // Remove existing to avoid double-firing if called multiple times
        el.removeEventListener('input', triggerAutosave);
        el.removeEventListener('change', triggerAutosave);
        el.addEventListener('input', triggerAutosave);
        el.addEventListener('change', triggerAutosave);
      });
    }
    initStep1Listeners();

    function goToStep(step) {
      if (step === currentStep) return;
      // Validate Step 1 before moving to Step 2
      if (step === 2 && currentStep === 1) {
        
        // 1. Validate required text fields
        const requiredFields = [
          { id: 'family-name', name: 'Family Name' },
          { id: 'given-name', name: 'Given Name' },
          { id: 'dob', name: 'Date of Birth' },
          { id: 'sex', name: 'Sex' },
          { id: 'address', name: 'Address' },
          { id: 'phone', name: 'Phone Number' }
        ];

        for (const field of requiredFields) {
          const el = document.getElementById(field.id);
          if (el && !el.value.trim()) {
            showToast(`Please fill out the ${field.name} field before proceeding.`, '#8b2e2e');
            el.focus();
            return;
          }
        }

        // 2. Validate declaration checkboxes
        const d1 = document.getElementById('decl1').checked;
        const d2 = document.getElementById('decl2').checked;
        if (!d1 || !d2) {
          showToast('Please check both declaration boxes before proceeding.', '#8b2e2e');
          return;
        }
        // Save Step 1 data to server before transitioning
        saveStep1ToServer()
          .then(res => {
            if (res.success) {
              showToast('Application info saved!', '#2f8a60');
            } else {
              showToast('Could not save info: ' + (res.message || 'Unknown error'), '#8b2e2e');
            }
          })
          .catch(err => console.error('Save error:', err));
      }

      currentStep = step;

      // Update step panels
      document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('step-panel-' + step).classList.add('active');

      // Update stepper circles
      for (let i = 1; i <= 2; i++) {
        const stepEl = document.getElementById('stepper-step-' + i);
        stepEl.classList.remove('active', 'completed');
        if (i < step) {
          stepEl.classList.add('completed');
          stepEl.querySelector('.stepper-circle').innerHTML = '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';
        } else if (i === step) {
          stepEl.classList.add('active');
          stepEl.querySelector('.stepper-circle').textContent = i;
        } else {
          stepEl.querySelector('.stepper-circle').textContent = i;
        }
      }

      // Update connector
      const connector = document.getElementById('connector-1-2');
      if (step >= 2) {
        connector.classList.add('filled');
      } else {
        connector.classList.remove('filled');
      }

      // Update progress bar
      const barFill = document.getElementById('stepper-bar-fill');
      const stepLabel = document.getElementById('current-step-label');
      barFill.style.width = (step / 2 * 100) + '%';
      stepLabel.textContent = step;

      // Scroll to top
      document.querySelector('.main-content').scrollTo({
        top: 0,
        behavior: 'smooth'
      });

      // Render doc cards when entering step 2
      if (step === 2) {
        renderCards();
      }
    }

    // Next step button
    document.getElementById('next-step-btn').addEventListener('click', (e) => {
      e.preventDefault();
      goToStep(2);
    });

    // Back to step 1 button
    document.getElementById('back-to-step1-btn').addEventListener('click', () => {
      goToStep(1);
    });

    const familyBody = document.getElementById('family-members-body');
    let savedFamily = [];
    try {
      // Use json_encode for safer transfer of structured data
      savedFamily = <?= json_encode(json_decode($appData['family_data'] ?? '[]')) ?> || [];
    } catch (e) {
      console.error("Family data parse error:", e);
    }

    for (let i = 1; i <= 10; i++) {
      const data = savedFamily[i - 1] || {};
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i}.</td>
        <td><input type="text" placeholder="Full name" value="${data.name || ''}" /></td>
        <td><select>
            <option value="">— Select —</option>
            <option ${data.relation === 'Spouse' ? 'selected' : ''}>Spouse</option>
            <option ${data.relation === 'Son' ? 'selected' : ''}>Son</option>
            <option ${data.relation === 'Daughter' ? 'selected' : ''}>Daughter</option>
            <option ${data.relation === 'Father' ? 'selected' : ''}>Father</option>
            <option ${data.relation === 'Mother' ? 'selected' : ''}>Mother</option>
            <option ${data.relation === 'Sibling' ? 'selected' : ''}>Sibling</option>
            <option ${data.relation === 'Other' ? 'selected' : ''}>Other</option>
          </select></td>
        <td><input type="number" placeholder="—" min="0" style="text-align:center;" value="${data.age || ''}" /></td>
        <td><select>
            <option ${data.religion === 'Islam' ? 'selected' : ''}>Islam</option>
            <option ${data.religion === 'Christian' ? 'selected' : ''}>Christian</option>
            <option ${data.religion === 'Other' ? 'selected' : ''}>Other</option>
          </select></td>
      `;
      familyBody.appendChild(tr);
    }
    initStep1Listeners(); // Important: Attach auto-save listeners to newly created family rows

    // ── 2x2 Photo Upload + Preview ──
    const photoInput = document.getElementById('photo-input');
    const photoBox = document.getElementById('photo-upload-box');
    let uploadedPhotoSrc = null;

    // ── GLOBAL FUNCTIONS (called from inline onclick) ──

    // Called when the main photo box div is clicked
    window.handlePhotoBoxClick = function(e) {
      // If a button or control inside was clicked, do nothing here
      if (e.target.closest('.photo-controls')) return;
      
      // If no photo exists, open file picker
      if (!uploadedPhotoSrc || uploadedPhotoSrc === 'broken') {
        photoInput.click();
        return;
      }
      
      // If photo exists, show preview (on image or box click)
      showPhotoPreview(uploadedPhotoSrc);
    };

    // Called from "Edit Photo" button
    window.handleEditPhoto = function(e) {
      e.stopPropagation();
      photoInput.click();
    };

    // Called from "Remove" button 
    window.handleRemovePhoto = function(e) {
      e.stopPropagation();
      if (!confirm('Are you sure you want to remove your photo?')) return;

      fetch('<?= url("/user/apartment/remove-image") ?>?type=picture', {
        method: 'GET',
        credentials: 'same-origin'
      })
        .then(function(r) {
          if (!r.ok) throw new Error('Server error: ' + r.status);
          return r.json();
        })
        .then(function(res) {
          console.log('Remove response:', res);
          if (res.success) {
            uploadedPhotoSrc = null;
            document.getElementById('photo-placeholder-icon').style.display = 'block';
            document.getElementById('photo-label-text').style.display = 'block';
            document.getElementById('photo-controls').style.display = 'none';
            var img = photoBox.querySelector('img');
            if (img) img.remove();
            showToast('Photo removed successfully.', '#2f8a60');
          } else {
            showToast('Failed to remove photo: ' + (res.message || 'Unknown error'), '#8b2e2e');
          }
        })
        .catch(function(err) {
          console.error('Remove error:', err);
          showToast('Network error while removing photo.', '#8b2e2e');
        });
    };

    // Handle file selection (upload)
    photoInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          uploadedPhotoSrc = e.target.result;
          document.getElementById('photo-placeholder-icon').style.display = 'none';
          document.getElementById('photo-label-text').style.display = 'none';
          document.getElementById('photo-controls').style.display = 'flex';
          let img = photoBox.querySelector('img');
          if (!img) {
            img = document.createElement('img');
            photoBox.insertBefore(img, photoBox.firstChild);
          }
          img.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Upload to server
        const fd = new FormData();
        fd.append('file', file);
        fd.append('type', 'picture');
        fetch('<?= url("/user/apartment/upload") ?>', {
            method: 'POST',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            if (!res.success) console.warn('Photo upload failed:', res.message);
            else showToast('Photo uploaded!', '#2f8a60');
          })
          .catch(err => console.error('Photo upload error:', err));
      }
    });

    // Auto-load existing 2x2 photo from server on page load
    (function() {
      const savedPhotoUrl = '<?= $pictureUrl ?: '' ?>';
      const recordExists = <?= $pictureRecordExists ? 'true' : 'false' ?>;
      
      if (!recordExists && !savedPhotoUrl) return;
      
      uploadedPhotoSrc = savedPhotoUrl || 'broken';
      document.getElementById('photo-placeholder-icon').style.display = 'none';
      document.getElementById('photo-label-text').style.display = 'none';
      document.getElementById('photo-controls').style.display = 'flex';

      if (savedPhotoUrl) {
        let img = photoBox.querySelector('img');
        if (!img) {
          img = document.createElement('img');
          photoBox.insertBefore(img, photoBox.firstChild);
        }
        img.src = savedPhotoUrl;
        img.onerror = function() { this.style.display = 'none'; };
      }
    })();

    function showPhotoPreview(src) {
      if (!src || src === 'broken') return;
      const existing = document.querySelector('.photo-preview-overlay');
      if (existing) existing.remove();
      const overlay = document.createElement('div');
      overlay.className = 'photo-preview-overlay';
      overlay.innerHTML = `
        <button class="photo-preview-close" title="Close preview">
          <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
        <img src="${src}" alt="2x2 Photo Preview" />
      `;
      document.body.appendChild(overlay);
      overlay.querySelector('.photo-preview-close').addEventListener('click', () => {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.2s ease';
        setTimeout(() => overlay.remove(), 200);
      });
      overlay.addEventListener('click', (ev) => {
        if (ev.target === overlay) {
          overlay.style.opacity = '0';
          overlay.style.transition = 'opacity 0.2s ease';
          setTimeout(() => overlay.remove(), 200);
        }
      });
    }

    // ── Auto-fill date of application ──
    const dateInput = document.getElementById('date-application');
    if (dateInput) dateInput.value = new Date().toISOString().split('T')[0];

    // ── Auto-calculate age from DOB ──
    const dobInput = document.getElementById('dob');
    const ageInput = document.getElementById('age');
    if (dobInput && ageInput) {
      dobInput.addEventListener('change', function() {
        if (this.value) {
          const birth = new Date(this.value);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          const m = today.getMonth() - birth.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
          ageInput.value = age >= 0 ? age : 0;
        }
      });
    }

    // ── ISCAG Students names toggle ──
    const studentsInput = document.getElementById('iscag-students');
    const namesContainer = document.getElementById('iscag-student-names-container');
    const namesList = document.getElementById('iscag-names-list');

    function updateStudentNameFields(count, existingNames = []) {
      if (!namesList) return;
      namesList.innerHTML = '';
      if (count > 0) {
        namesContainer.style.display = 'block';
        for (let i = 0; i < count; i++) {
          const input = document.createElement('input');
          input.type = 'text';
          input.className = 'iscag-student-name-input';
          input.placeholder = `Full Name of Student #${i + 1}`;
          input.style.width = '100%';
          input.style.padding = '8px 12px';
          input.style.borderRadius = '6px';
          input.style.border = '1px solid rgba(15,92,58,0.2)';
          input.style.fontSize = '0.85rem';
          input.style.textAlign = 'left';
          input.value = existingNames[i] || '';
          namesList.appendChild(input);
          // Attach auto-save to new name inputs
          input.addEventListener('input', triggerAutosave);
        }
      } else {
        namesContainer.style.display = 'none';
      }
    }

    if (studentsInput) {
      studentsInput.addEventListener('change', function() {
        let count = parseInt(this.value) || 0;
        updateStudentNameFields(count);
      });
      
      // Initialize if there's already data
      const initialCount = parseInt(studentsInput.value) || 0;
      if (initialCount > 0) {
        let existing = [];
        try {
            const raw = `<?= addslashes($appData['iscag_student_names'] ?? '[]') ?>`;
            existing = JSON.parse(raw) || [];
        } catch(e) {
            console.error("Student names parse error:", e);
        }
        updateStudentNameFields(initialCount, Array.isArray(existing) ? existing : []);
      }
    }

    function getIscagStudentNames() {
      const inputs = document.querySelectorAll('.iscag-student-name-input');
      const names = Array.from(inputs).map(i => i.value.trim()).filter(v => v !== '');
      return JSON.stringify(names);
    }

    // ── ISCAG Employment toggle ──
    const empSelect = document.getElementById('iscag-employee');
    const jobContainer = document.getElementById('iscag-job-container');
    if (empSelect && jobContainer) {
      empSelect.addEventListener('change', function() {
        jobContainer.style.display = this.value === '1' ? 'block' : 'none';
        if (this.value === '0') document.getElementById('iscag-job-role').value = '';
      });
    }

    // ── Unit card selection ──
    document.querySelectorAll('.unit-card').forEach(card => {
      card.addEventListener('click', () => {
        document.querySelectorAll('.unit-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
      });
    });

    // ── Room Preview ──
    const unitAvailability = {
      studio: DEFAULT_APARTMENTS.filter(a => a.name.toLowerCase().includes('studio')).reduce((sum, a) => sum + a.available, 0),
      '1br': DEFAULT_APARTMENTS.filter(a => a.name.toLowerCase().includes('1-bedroom')).reduce((sum, a) => sum + a.available, 0),
      '2br': DEFAULT_APARTMENTS.filter(a => a.name.toLowerCase().includes('2-bedroom')).reduce((sum, a) => sum + a.available, 0)
    };

    function previewRoom(unitType) {
      openRoomPreview(unitType, {
        availableCount: unitAvailability[unitType] || 0,
        basePath: '<?= asset('assets/') ?>',
        selectLabel: 'Select This Unit',
        onSelect: function(type) {
          const radioMap = {
            studio: 'unit1',
            '1br': 'unit2',
            '2br': 'unit3'
          };
          const radio = document.getElementById(radioMap[type]);
          if (radio) {
            radio.checked = true;
            document.querySelectorAll('.unit-card').forEach(c => c.classList.remove('selected'));
            radio.closest('.unit-card').classList.add('selected');
          }
        }
      });
    }

    function useMyDetails() {
        const u = DB_USER;
        if (!u) return;

        // Auto-fill fields
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = val || '';
        };

        setVal('family-name', u.lastName);
        setVal('given-name', u.firstName);
        
        // Muslim Name Logic
        const muslimNameEl = document.getElementById('muslim-name');
        if (muslimNameEl) {
            const isNA = (u.arabicName === 'N/A');
            muslimNameEl.value = isNA ? '' : (u.arabicName || '');
            muslimNameEl.disabled = isNA;
            muslimNameEl.style.opacity = isNA ? '0.5' : '1';
            muslimNameEl.style.backgroundColor = isNA ? 'rgba(0,0,0,0.02)' : 'white';
        }

        setVal('dob', u.dob);
        setVal('sex', u.sex);
        setVal('address', u.address);
        setVal('phone', u.phone);
        setVal('civil-status', u.civil);
        setVal('occupation', u.occupation);

        // Date of Shahadah Logic
        const shahadahDateEl = document.getElementById('shahadah-date');
        if (shahadahDateEl) {
            const isNA = (u.revertYear === 'N/A' || u.revertYear === '0000-00-00');
            shahadahDateEl.value = isNA ? '' : (u.revertYear || '');
            shahadahDateEl.disabled = isNA;
            shahadahDateEl.style.opacity = isNA ? '0.5' : '1';
            shahadahDateEl.style.backgroundColor = isNA ? 'rgba(0,0,0,0.02)' : 'white';
        }

        setVal('tribal', u.tribal);
        setVal('pob', u.pob);

        // Trigger age calculation
        if (u.dob) {
            const dobEl = document.getElementById('dob');
            if (dobEl) dobEl.dispatchEvent(new Event('change'));
        }

        showToast('Application form populated with your account details!', '#2f8a60');
        triggerAutosave();
        
        // Visual feedback on the button
        const btn = event.currentTarget;
        if (btn) {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<svg viewBox="0 0 24 24" style="width:14px; fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Details Applied';
            setTimeout(() => { btn.innerHTML = originalText; }, 2000);
        }
    }



    // ═══════════════════════════════════════════
    //  STEP 2: REQUIRED DOCUMENTS LOGIC
    // ═══════════════════════════════════════════
    const REQUIRED_DOCS = [{
        id: 'doc-income',
        name: 'Proof of Income',
        note: 'Submit payslip, certificate of employment, or business permit',
        icon: '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>',
        type: 'single'
      },
      {
        id: 'doc-id',
        name: 'Valid ID (Photocopy)',
        note: 'Upload front and back of any government-issued ID',
        icon: '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 18V6h16v12H4zm6-4c1.38 0 2.5-1.12 2.5-2.5S11.38 9 10 9s-2.5 1.12-2.5 2.5S8.62 14 10 14zm0 1c-1.63 0-5 .97-5 2.5V18h10v-.5C15 15.97 11.63 15 10 15zm6.5-6H20v1h-3.5v-1zm0 2H20v1h-3.5v-1zm0 2H20v1h-3.5v-1z"/>',
        type: 'dual',
        slots: [{
            key: 'doc-id-front',
            label: 'Front Side'
          },
          {
            key: 'doc-id-back',
            label: 'Back Side'
          }
        ]
      },
      {
        id: 'doc-birth',
        name: 'Birth Certificate (Photocopy)',
        note: 'PSA or local civil registrar copy',
        icon: '<path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
        type: 'single'
      },
      {
        id: 'doc-nbi',
        name: 'NBI / Police Clearance',
        note: 'Must be issued within the last 6 months',
        icon: '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>',
        type: 'single'
      }
    ];

    function getAllSlotKeys() {
      const keys = [];
      REQUIRED_DOCS.forEach(doc => {
        if (doc.type === 'dual') {
          doc.slots.forEach(slot => keys.push(slot.key));
        } else {
          keys.push(doc.id);
        }
      });
      return keys;
    }

    const DOC_STORAGE_KEY = 'mis_req_doc_uploads';
    const inMemoryPreviews = {};

    function getUploadedDocs() {
      const raw = localStorage.getItem(DOC_STORAGE_KEY);
      return raw ? JSON.parse(raw) : {};
    }

    function saveUploadedDoc(docId, dataUrl, fileType) {
      const docs = getUploadedDocs();
      docs[docId] = {
        uploadedAt: new Date().toISOString(),
        fileType: fileType
      };
      inMemoryPreviews[docId] = dataUrl;
      try {
        localStorage.setItem(DOC_STORAGE_KEY, JSON.stringify(docs));
      } catch(e) {
        console.warn('localStorage quota exceeded for docs, saving in-memory only.', e);
      }
    }

    function removeUploadedDoc(docId) {
      const docs = getUploadedDocs();
      delete docs[docId];
      delete inMemoryPreviews[docId];
      localStorage.setItem(DOC_STORAGE_KEY, JSON.stringify(docs));
    }

    function getPreviewSrc(docId, docData) {
      if (inMemoryPreviews[docId]) {
        return inMemoryPreviews[docId];
      }
      const typeMap = {
        'doc-income': 'proofofincome',
        'doc-id-front': 'valididfront',
        'doc-id-back': 'valididback',
        'doc-birth': 'birthcert',
        'doc-nbi': 'nbi',
        'doc-photo': 'picture'
      };
      const serverType = typeMap[docId];
      if (!serverType || !docData) return '';
      return `<?= url('/user/apartment/serveImage') ?>?type=${serverType}&t=${new Date(docData.uploadedAt).getTime()}`;
    }

    // ═══ RENDER DOCUMENT CARDS ═══
    const grid = document.getElementById('doc-cards-grid');

    function renderCards() {
      const currentUploads = getUploadedDocs();
      grid.innerHTML = '';

      REQUIRED_DOCS.forEach((doc, idx) => {
        const card = document.createElement('div');
        card.id = 'card-' + doc.id;

        if (doc.type === 'dual') {
          const frontUploaded = !!currentUploads[doc.slots[0].key];
          const backUploaded = !!currentUploads[doc.slots[1].key];
          const allUploaded = frontUploaded && backUploaded;
          const partialUploaded = frontUploaded || backUploaded;

          card.className = 'doc-card' + (allUploaded ? ' uploaded' : '');

          let statusClass = 'pending';
          let statusText = 'Pending';
          if (allUploaded) {
            statusClass = 'uploaded';
            statusText = 'Uploaded';
          } else if (partialUploaded) {
            statusClass = 'pending';
            statusText = '1 of 2';
          }

          card.innerHTML = `
            <div class="doc-card-header">
              <div class="doc-card-header-left">
                <div class="doc-card-icon">
                  <svg viewBox="0 0 24 24">${doc.icon}</svg>
                </div>
                <div>
                  <h4 class="doc-card-title">${doc.name}</h4>
                  <p class="doc-card-note">${doc.note}</p>
                </div>
              </div>
              <div class="doc-card-status ${statusClass}">
                <span class="doc-card-status-dot"></span>
                ${statusText}
              </div>
            </div>
            <div class="doc-upload-zone">
              <div class="doc-dual-upload">
                ${doc.slots.map(slot => {
            const slotUploaded = !!currentUploads[slot.key];
            return `
                    <div class="doc-dual-slot">
                      <div class="doc-dual-label">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 18V6h16v12H4z"/></svg>
                        ${slot.label}
                      </div>
                      <div class="doc-upload-dropzone" id="dropzone-${slot.key}" style="${slotUploaded ? 'display:none;' : ''}">
                        <svg class="upload-icon" viewBox="0 0 24 24">
                          <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                        </svg>
                        <div class="upload-text"><strong>Upload ${slot.label}</strong></div>
                        <div class="upload-hint">JPG, PNG — Max 5MB</div>
                        <input type="file" accept="image/*,.pdf" id="input-${slot.key}" />
                      </div>
                      <div class="doc-preview-wrap ${slotUploaded ? 'visible' : ''}" id="preview-${slot.key}">
                        ${slotUploaded
                ? (function() {
                  const src = getPreviewSrc(slot.key, currentUploads[slot.key]);
                  const isPDF = (currentUploads[slot.key] && currentUploads[slot.key].fileType === 'application/pdf') || 
                                src.startsWith('data:application/pdf') || 
                                src.toLowerCase().includes('.pdf');
                  if (isPDF) {
                    return `<div class="doc-preview-pdf-placeholder"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3.5h-1v1h1V11h-1v2H18V7h2.5v1.5zM9 10h1V8H9v2zm5.5 2h1V8.5h-1V12z"/></svg><span>PDF Document</span></div>`;
                  }
                  return `<img class="doc-preview-img" src="${src}" alt="${slot.label}" id="img-${slot.key}" />`;
                })()
                : `<img class="doc-preview-img" src="" alt="${slot.label}" id="img-${slot.key}" style="display:none;" />`
              }
                        <div class="doc-preview-actions">
                          <button class="doc-preview-btn view" onclick="viewFullImage('${slot.key}')" title="View">
                            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            View
                          </button>
                          <button class="doc-preview-btn change" onclick="changeFile('${slot.key}')" title="Change">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Change
                          </button>
                          <button class="doc-preview-btn remove" onclick="removeFile('${slot.key}')" title="Remove">
                            <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  `;
          }).join('')}
              </div>
            </div>
          `;

          grid.appendChild(card);

          doc.slots.forEach(slot => {
            const dropzone = card.querySelector(`#dropzone-${slot.key}`);
            const fileInput = card.querySelector(`#input-${slot.key}`);
            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', (e) => {
              e.preventDefault();
              dropzone.classList.add('dragover');
            });
            dropzone.addEventListener('dragleave', () => {
              dropzone.classList.remove('dragover');
            });
            dropzone.addEventListener('drop', (e) => {
              e.preventDefault();
              dropzone.classList.remove('dragover');
              const file = e.dataTransfer.files[0];
              if (file) handleFileUpload(slot.key, file);
            });
            fileInput.addEventListener('change', function() {
              const file = this.files[0];
              if (file) handleFileUpload(slot.key, file);
            });
          });

        } else {
          const isUploaded = !!currentUploads[doc.id];
          card.className = 'doc-card' + (isUploaded ? ' uploaded' : '');

          card.innerHTML = `
            <div class="doc-card-header">
              <div class="doc-card-header-left">
                <div class="doc-card-icon">
                  <svg viewBox="0 0 24 24">${doc.icon}</svg>
                </div>
                <div>
                  <h4 class="doc-card-title">${doc.name}</h4>
                  <p class="doc-card-note">${doc.note}</p>
                </div>
              </div>
              <div class="doc-card-status ${isUploaded ? 'uploaded' : 'pending'}">
                <span class="doc-card-status-dot"></span>
                ${isUploaded ? 'Uploaded' : 'Pending'}
              </div>
            </div>
            <div class="doc-upload-zone">
              <div class="doc-upload-dropzone" id="dropzone-${doc.id}" style="${isUploaded ? 'display:none;' : ''}">
                <svg class="upload-icon" viewBox="0 0 24 24">
                  <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                </svg>
                <div class="upload-text"><strong>Click to upload</strong> or drag and drop</div>
                <div class="upload-hint">JPG, PNG, or PDF — Max 5MB</div>
                <input type="file" accept="image/*,.pdf" id="input-${doc.id}" />
              </div>
              <div class="doc-preview-wrap ${isUploaded ? 'visible' : ''}" id="preview-${doc.id}">
                ${isUploaded ? (function() {
                  const src = getPreviewSrc(doc.id, currentUploads[doc.id]);
                  const isPDF = (currentUploads[doc.id] && currentUploads[doc.id].fileType === 'application/pdf') || 
                                src.startsWith('data:application/pdf') || 
                                src.toLowerCase().includes('.pdf');
                  if (isPDF) {
                    return `<div class="doc-preview-pdf-placeholder"><svg viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3.5h-1v1h1V11h-1v2H18V7h2.5v1.5zM9 10h1V8H9v2zm5.5 2h1V8.5h-1V12z"/></svg><span>PDF Document</span></div>`;
                  }
                  return `<img class="doc-preview-img" src="${src}" alt="${doc.name}" id="img-${doc.id}" />`;
                })() : `<img class="doc-preview-img" src="" alt="${doc.name}" id="img-${doc.id}" style="display:none;" />`}
                <div class="doc-preview-actions">
                  <button class="doc-preview-btn view" onclick="viewFullImage('${doc.id}')" title="View full size">
                    <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    View
                  </button>
                  <button class="doc-preview-btn change" onclick="changeFile('${doc.id}')" title="Change file">
                    <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Change
                  </button>
                  <button class="doc-preview-btn remove" onclick="removeFile('${doc.id}')" title="Remove file">
                    <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Remove
                  </button>
                </div>
              </div>
            </div>
          `;

          grid.appendChild(card);
          const dropzone = card.querySelector(`#dropzone-${doc.id}`);
          const fileInput = card.querySelector(`#input-${doc.id}`);
          dropzone.addEventListener('click', () => fileInput.click());
          dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
          });
          dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
          });
          dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) handleFileUpload(doc.id, file);
          });
          fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) handleFileUpload(doc.id, file);
          });
        }
      });

      updateProgress();
    }

    function handleFileUpload(docId, file) {
      if (file.size > 5 * 1024 * 1024) {
        showToast('File is too large. Maximum size is 5MB.', '#8b2e2e');
        return;
      }
      const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
      if (!validTypes.includes(file.type)) {
        showToast('Invalid file type. Please upload JPG, PNG, or PDF.', '#8b2e2e');
        return;
      }
      const reader = new FileReader();
      reader.onload = function(e) {
        saveUploadedDoc(docId, e.target.result, file.type);
        renderCards();
        showToast('Document uploaded successfully!', '#2f8a60');
      };
      reader.readAsDataURL(file);

      // Also upload to server → tenant_requirements
      const typeMap = {
        'doc-income': 'proofofincome',
        'doc-id-front': 'valididfront',
        'doc-id-back': 'valididback',
        'doc-birth': 'birthcert',
        'doc-nbi': 'nbi',
        'doc-photo': 'picture'
      };
      const serverType = typeMap[docId];
      if (serverType) {
        const fd = new FormData();
        fd.append('file', file);
        fd.append('type', serverType);
        fetch('<?= url("/user/apartment/upload") ?>', {
            method: 'POST',
            body: fd
          })
          .then(r => r.json())
          .then(res => {
            if (!res.success) console.warn('Server upload failed:', res.message);
          })
          .catch(err => console.error('Server upload error:', err));
      }
    }

    function changeFile(docId) {
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/*,.pdf';
      input.addEventListener('change', function() {
        if (this.files[0]) handleFileUpload(docId, this.files[0]);
      });
      input.click();
    }

    function removeFile(docId) {
      removeUploadedDoc(docId);
      renderCards();
      showToast('Document removed.', '#c79a2b');
    }

    function viewFullImage(docId) {
      const docs = getUploadedDocs();
      const docData = docs[docId];
      if (!docData) return;

      const src = getPreviewSrc(docId, docData);
      const isPDF = (docData && docData.fileType === 'application/pdf') || 
                    src.startsWith('data:application/pdf') || 
                    src.toLowerCase().includes('.pdf');

      const overlay = document.createElement('div');
      overlay.className = 'img-preview-overlay';
      
      let content = `<img src="${src}" alt="Document Preview" />`;
      if (isPDF) {
        content = `<iframe src="${src}" style="width:85%; height:92%; border:none; border-radius:12px; background-color:white; box-shadow:0 10px 40px rgba(0,0,0,0.3);"></iframe>`;
      }

      overlay.innerHTML = `
        <button class="img-preview-close" title="Close preview">
          <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
        ${content}
      `;
      document.body.appendChild(overlay);

      overlay.querySelector('.img-preview-close').addEventListener('click', () => {
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.2s ease';
        setTimeout(() => overlay.remove(), 200);
      });
      overlay.addEventListener('click', (ev) => {
        if (ev.target === overlay) {
          overlay.style.opacity = '0';
          overlay.style.transition = 'opacity 0.2s ease';
          setTimeout(() => overlay.remove(), 200);
        }
      });
    }

    function updateProgress() {
      const docs = getUploadedDocs();
      const allKeys = getAllSlotKeys();
      const total = allKeys.length;
      const completed = allKeys.filter(k => !!docs[k]).length;
      const percent = Math.round((completed / total) * 100);

      document.getElementById('progress-fill').style.width = percent + '%';
      document.getElementById('progress-percent').textContent = percent + '%';
      document.getElementById('completed-count').textContent = completed;
      document.getElementById('total-count').textContent = total;

      const submitBtn = document.getElementById('submit-docs-btn');
      submitBtn.disabled = completed < total;
    }

    // ── Submit Documents ──
    document.getElementById('submit-docs-btn').addEventListener('click', () => {
      const docs = getUploadedDocs();
      const allKeys = getAllSlotKeys();
      const allUploaded = allKeys.every(k => !!docs[k]);

      if (!allUploaded) {
        showToast('Please upload all required documents before submitting.', '#8b2e2e');
        return;
      }

      // Finalize on Server
      fetch('<?= url("/user/apartment/submit") ?>', { method: 'POST' })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            // Save locally for UI consistency (User Dashboard)
            const newReq = addRequest({
              type: 'apartment_application',
              user: user.id
            });

      // Save required documents status
      const reqDocs = [{
          id: 'doc-income',
          name: 'Proof of Income',
          status: 'completed',
          completedNote: 'Document uploaded and submitted'
        },
        {
          id: 'doc-id',
          name: 'Valid ID (Photocopy)',
          status: 'completed',
          completedNote: 'Document uploaded and submitted'
        },
        {
          id: 'doc-birth',
          name: 'Birth Certificate (Photocopy)',
          status: 'completed',
          completedNote: 'Document uploaded and submitted'
        },
        {
          id: 'doc-nbi',
          name: 'NBI / Police Clearance',
          status: 'completed',
          completedNote: 'Document uploaded and submitted'
        },
        {
          id: 'doc-photo',
          name: '2x2 Picture (2pcs)',
          status: 'completed',
          completedNote: 'Document uploaded and submitted'
        }
      ];
      localStorage.setItem('mis_apt_docs_' + newReq.id, JSON.stringify(reqDocs));

      // ── Create PENDING_MIS Report for MIS Admin review ──
      const reports = JSON.parse(localStorage.getItem('mis_reports') || '[]');
      const existingReport = reports.find(r => r.tenantId === user.id && (r.status === 'PENDING_MIS' || r.status === 'VERIFIED'));
      if (!existingReport) {
        const reportId = 'RPT-' + String(reports.length + 1).padStart(3, '0');
        const today = new Date().toISOString().split('T')[0];
        const uploadedDocs = getUploadedDocs();
        const hasDoc = (key) => !!uploadedDocs[key];

        const newReport = {
          id: reportId,
          tenantId: user.id,
          tenantName: user.name,
          status: 'PENDING_MIS',
          roomId: null,
          roomName: null,
          remarks: '',
          submittedAt: today,
          verifiedAt: null,
          approvedAt: null,
          requirements: {
            valid_id: hasDoc('doc-id-front') && hasDoc('doc-id-back'),
            certificate: hasDoc('doc-birth'),
            photo: hasDoc('doc-photo'),
            contract: hasDoc('doc-income'),
            nbi_clearance: hasDoc('doc-nbi')
          },
          billingIds: [],
          updatedAt: today
        };
        reports.push(newReport);
        localStorage.setItem('mis_reports', JSON.stringify(reports));

        // Activity log
        const actLog = JSON.parse(localStorage.getItem('mis_activity_log') || '[]');
        actLog.unshift({
          action: 'Application submitted',
          detail: reportId + ' — ' + user.name + ' submitted apartment application with all documents',
          actor: user.name,
          time: new Date().toISOString(),
          type: 'request'
        });
        if (actLog.length > 50) actLog.length = 50;
        localStorage.setItem('mis_activity_log', JSON.stringify(actLog));

        // Notification
        const notifs = JSON.parse(localStorage.getItem('mis_notifications') || '[]');
        notifs.unshift({
          id: 'NOT-' + String(notifs.length + 1).padStart(3, '0'),
          tenantId: user.id,
          title: 'Application Submitted',
          message: 'Your apartment application ' + reportId + ' has been submitted and is pending Admin Review.',
          type: 'system',
          read: false,
          createdAt: new Date().toISOString()
        });
        localStorage.setItem('mis_notifications', JSON.stringify(notifs));
      }
      // Clear the temporary uploaded documents from local storage
      localStorage.removeItem(DOC_STORAGE_KEY);

            showSuccessView();
          } else {
            showToast('Submission failed: ' + (res.message || 'Unknown error'), '#8b2e2e');
          }
        })
        .catch(err => {
          console.error(err);
          showToast('An error occurred during submission.', '#8b2e2e');
        });
    });

    function showSuccessView() {
      const pageBody = document.querySelector('.page-body');
      pageBody.innerHTML = `
        <div style="max-width:560px;margin:40px auto;text-align:center;">
          <div style="
            background:white;border-radius:16px;
            border:1px solid var(--border);
            box-shadow:0 4px 24px rgba(0,0,0,0.08);
            overflow:hidden;
          ">
            <div style="
              background:linear-gradient(135deg, var(--primary-dark), var(--primary-light));
              padding:40px 32px 32px;
              position:relative;
              overflow:hidden;
            ">
              <div style="position:absolute;right:-15px;bottom:-15px;width:100px;height:100px;border-radius:50%;background:rgba(201,168,76,0.1);"></div>
              <div style="
                width:72px;height:72px;border-radius:50%;
                background:rgba(255,255,255,0.15);
                margin:0 auto 16px;
                display:flex;align-items:center;justify-content:center;
                backdrop-filter:blur(4px);
              ">
                <svg viewBox="0 0 24 24" style="width:36px;height:36px;fill:white;">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
              </div>
              <h3 style="
                font-family:'Lora',serif;font-size:1.3rem;font-weight:700;
                color:white;margin:0 0 8px;
              ">Application Submitted Successfully!</h3>
              <p style="font-size:0.88rem;color:rgba(255,255,255,0.75);margin:0;line-height:1.6;">
                Your tenant application and all required documents have been submitted for review.
              </p>
            </div>
            <div style="padding:28px 32px;">
              <div style="
                padding:16px;border-radius:10px;
                background:rgba(47,138,96,0.06);
                border:1px solid rgba(47,138,96,0.15);
                margin-bottom:20px;
              ">
                <div style="display:flex;align-items:center;gap:10px;justify-content:center;">
                  <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--success);">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                  </svg>
                  <span style="font-size:0.88rem;font-weight:700;color:var(--success);">All Steps Completed</span>
                </div>
              </div>
              <p style="font-size:0.83rem;color:var(--text-muted);line-height:1.7;margin:0 0 24px;">
                The management team will verify your documents and update your application status.
                You will receive a notification once the review is complete.
              </p>
              <div style="display:flex;gap:12px;justify-content:center;">
                <a href="<?= url('/user/dashboard') ?>" style="
                  display:inline-flex;align-items:center;gap:8px;
                  padding:11px 24px;border-radius:8px;
                  border:1.5px solid var(--border);
                  background:white;
                  color:var(--text-muted);font-size:0.85rem;font-weight:600;
                  text-decoration:none;
                  transition:all 0.18s;
                ">
                  <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:currentColor;"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                  Go to Dashboard
                </a>
                <a href="<?= url('/user/apartment/status') ?>" style="
                  display:inline-flex;align-items:center;gap:8px;
                  padding:11px 24px;border-radius:8px;
                  background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));
                  color:white;font-size:0.85rem;font-weight:700;
                  text-decoration:none;
                  box-shadow:0 4px 12px rgba(23,107,69,0.25);
                  transition:all 0.18s;
                ">
                  <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                  View Application Status
                </a>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // ── Dynamic Apartment Types ──
    async function loadApartmentTypes() {
      const container = document.getElementById('unit-cards-container');
      try {
        const response = await fetch('<?= url("/api/apartment-types") ?>');
        const res = await response.json();
        if (res.success) {
          const types = res.data;
          if (!types.length) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">No available unit types found.</p>';
            return;
          }
          container.innerHTML = types.map((t, idx) => {
            const typeId = t.type_id || idx;
            const typeKey = t.type_key || `unit-${typeId}`;
            const label = t.label || 'Apartment Unit';
            const isFull = (parseInt(t.available_count) || 0) <= 0;
            const hasQueue = (parseInt(t.queue_count) || 0) > 0;
            
            let availText = isFull ? 'No Units Available' : `${t.available_count} Units Available`;
            if (isFull && hasQueue) {
                availText = `Waitlist: ${t.queue_count} Person(s)`;
            } else if (isFull) {
                availText = 'Waitlist Open';
            }

            const statusClass = isFull ? 'status-full' : (t.available_count < 5 ? 'status-low' : 'status-ok');
            const thumbUrl = t.thumbnail_id 
              ? `<?= url('/api/apartment-types/serve-image') ?>?id=${t.thumbnail_id}`
              : `<?= asset('assets/placeholder.png') ?>`;

            const savedRoomType = '<?= addslashes(trim($appData['roomtype'] ?? '')) ?>'.trim();
            const currentLabel = label.trim();
            
            // Flexible matching for labels vs keys
            const isSelected = savedRoomType 
              ? (currentLabel.toLowerCase() === savedRoomType.toLowerCase() || typeKey === savedRoomType) 
              : (idx === 0);

            if (isSelected) console.log("Matching unit found:", currentLabel);

            return `
              <label class="unit-card ${isSelected ? 'selected' : ''} ${isFull ? 'unit-full' : ''}" for="unit-${typeId}">
                <input type="radio" name="unit" id="unit-${typeId}" value="${typeKey}" 
                  ${isSelected ? 'checked' : ''} />
                <div class="unit-card-check">
                  <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
                </div>
                <div class="unit-card-thumb">
                  <img src="${thumbUrl}" alt="${label}" onerror="this.src='<?= asset('assets/placeholder.png') ?>'" />
                  <span class="unit-card-thumb-overlay">${label.split(' ')[0] || 'Unit'}</span>
                </div>
                <div class="unit-card-body">
                  <div class="unit-card-label">${label}</div>
                  <div class="unit-card-sub" style="display:flex; justify-content:space-between; align-items:center;">
                    <span>${t.capacity || 'For residents'}</span>
                    <span class="avail-badge ${statusClass}">${availText}</span>
                  </div>
                </div>
                <button type="button" class="unit-card-view" 
                  data-unit='${JSON.stringify(t).replace(/'/g, "&apos;")}'
                  data-id="${typeId}">
                  <svg viewBox="0 0 24 24"><path d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z" /></svg>
                  View Gallery
                </button>
              </label>
            `;
          }).join('');

          // Attach listeners
          container.querySelectorAll('.unit-card').forEach(card => {
            // Radio selection logic
            card.addEventListener('click', function(e) {
              if (e.target.closest('.unit-card-view')) return;
              if (this.classList.contains('unit-full')) return; // Don't select full units
              
              container.querySelectorAll('.unit-card').forEach(c => c.classList.remove('selected'));
              this.classList.add('selected');
              const radio = this.querySelector('input[type="radio"]');
              if (radio) {
                radio.checked = true;
                // Trigger auto-save immediately on selection
                triggerAutosave();
                // Trigger change event if needed
                radio.dispatchEvent(new Event('change', { bubbles: true }));
              }
            });

            // View Gallery logic
            const viewBtn = card.querySelector('.unit-card-view');
            if (viewBtn) {
              viewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const unitData = JSON.parse(this.getAttribute('data-unit'));
                const typeId = this.getAttribute('data-id');
                
                if (window.openRoomPreview) {
                  window.openRoomPreview(unitData, {
                    basePath: '<?= asset("assets/") ?>',
                    serveUrl: '<?= url("/api/apartment-types/serve-image") ?>',
                    onSelect: function() {
                      const radio = document.getElementById("unit-" + typeId);
                      if (radio) {
                        radio.checked = true;
                        container.querySelectorAll('.unit-card').forEach(c => c.classList.remove('selected'));
                        radio.closest('.unit-card').classList.add('selected');
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                      }
                    }
                  });
                }
              });
            }
          });
        }
        // Scan for new inputs (radios) to attach listeners
        initStep1Listeners();
      } catch (err) {
        console.error("API Error:", err);
      }
    }
    loadApartmentTypes();

    // ── Logic for hiding Family Section for Transient units ──
    document.addEventListener('change', function(e) {
      if (e.target.name === 'unit') {
        const card = e.target.closest('.unit-card');
        if (!card) return;
        
        const labelEl = card.querySelector('.unit-card-label');
        if (labelEl) {
          const label = labelEl.textContent.trim().toLowerCase();
          const familySection = document.getElementById('family-members-section');
          if (!familySection) return;
          
          if (label.includes('transient')) {
            // Hide and Reset Family Section
            familySection.style.display = 'none';
            // Clear inputs to avoid accidental submission of family data
            document.querySelectorAll('#family-members-body input').forEach(input => input.value = '');
            document.querySelectorAll('#family-members-body select').forEach(select => select.selectedIndex = 0);
          } else {
            familySection.style.display = 'block';
          }
        }
      }
    });

    // Run on initial load after a delay to wait for API data
    setTimeout(() => {
        const checked = document.querySelector('input[name="unit"]:checked');
        if (checked) checked.dispatchEvent(new Event('change', { bubbles: true }));

        const dobEl = document.getElementById('dob');
        if (dobEl && dobEl.value) dobEl.dispatchEvent(new Event('change', { bubbles: true }));

        // Restore visibility toggles for ISCAG sections if count is 0 (otherwise already handled)
        const studentsEl = document.getElementById('iscag-students');
        if (studentsEl && studentsEl.value === '0') {
            studentsEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const employeeEl = document.getElementById('iscag-employee');
        if (employeeEl) employeeEl.dispatchEvent(new Event('change', { bubbles: true }));
    }, 1500);

    // ── Check if user already has a pending/approved application ──
    function checkExistingApplication() {
      const raw = localStorage.getItem(STORAGE_KEYS.requests);
      const requests = raw ? JSON.parse(raw) : [];
      const aptReq = requests.find(r => r.type === 'apartment_application' && r.user === user.id && (r.status === 'pending' || r.status === 'approved'));
      return aptReq || null;
    }

    // Auto-disable N/A fields on load based on existing data
    window.addEventListener('DOMContentLoaded', () => {
        const lockNA = (id, val) => {
            const el = document.getElementById(id);
            if (el && (val === 'N/A' || val === '0000-00-00')) {
                el.value = '';
                el.disabled = true;
                el.style.opacity = '0.5';
                el.style.backgroundColor = 'rgba(0,0,0,0.02)';
            }
        };
        lockNA('muslim-name', '<?= addslashes($appData['muslimname'] ?? '') ?>');
        lockNA('shahadah-date', '<?= addslashes($appData['dateofshahadah'] ?? '') ?>');
    });

    const existingApp = checkExistingApplication();
    if (existingApp && existingApp.status !== 'approved') {
      // Only auto-show tracking for pending apps
    }

    // ── Toast Helper ──
    function showToast(msg, bg) {
      const toast = document.createElement('div');
      toast.className = 'toast-notification';
      toast.textContent = msg;
      toast.style.background = bg;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
  </script>
</body>

</html>