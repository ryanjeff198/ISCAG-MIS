<?php 
// ═══════════════════════════════════════════
//  LOAD SESSION & DATA
// ═══════════════════════════════════════════
require_once BASE_PATH . '/app/models/ApartmentApp.php';
$model = new ApartmentApp();
$userId = $_SESSION['user_id'];

// Fetch data for the dashboard
$parkingApps = $model->getParkingApplicationsByTenant($userId);
$hasPending = $model->hasPendingParkingApplication($userId);

// Fetch user info for pre-filling (if needed)
require_once BASE_PATH . '/app/models/User.php';
$userModel = new User();
$account = $userModel->findById($userId);
$info = $userModel->getAdditionalInfo($userId);
$fullName = ($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '');
$dob = $info['birthdate'] ?? '';

// Fetch room assignment info
require_once BASE_PATH . '/app/models/ApartmentApp.php';
$apaModel = new ApartmentApp();
$appInfo = $apaModel->getApplication($userId);
$assignedRoom = $appInfo['room_number'] ?? 'N/A';
$assignedBldg = $appInfo['building'] ?? 'N/A';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Parking Dashboard</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* ── DASHBOARD LAYOUT ── */
        .info-container { max-width: 1100px; margin: 0 auto; padding: 24px; }

        /* ── Vehicle Table Container ── */
        .dashboard-card {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04); overflow: hidden;
            animation: slideUp 0.4s ease;
        }
        .card-header {
            padding: 24px 32px; background: #fcfdfc; border-bottom: 1.5px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title-box { display: flex; align-items: center; gap: 16px; }
        .card-icon {
            width: 44px; height: 44px; border-radius: 12px; background: var(--primary-dark);
            display: flex; align-items: center; justify-content: center; color: white;
        }
        .card-title { font-family: 'Lora', serif; font-size: 1.25rem; font-weight: 800; color: var(--primary-dark); margin: 0; }
        
        /* ── Table Styling ── */
        .parking-table { width: 100%; border-collapse: collapse; }
        .parking-table th {
            padding: 16px 24px; background: #f8faf9; text-align: left;
            font-size: 0.72rem; font-weight: 800; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1.5px solid #f0f0f0;
        }
        .parking-table td { padding: 20px 24px; border-bottom: 1px solid #f4f6f5; vertical-align: middle; }
        .vehicle-info-cell { display: flex; align-items: center; gap: 14px; }
        .vehicle-avatar {
            width: 40px; height: 40px; border-radius: 10px; background: #f0f4f3;
            display: flex; align-items: center; justify-content: center; color: var(--primary);
        }
        .vehicle-name-text { display: block; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
        .vehicle-type-tag { font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
        
        .plate-badge {
            font-family: 'Source Code Pro', monospace; font-weight: 800; font-size: 0.9rem;
            background: #f4f6f5; color: #333; padding: 4px 10px; border-radius: 6px;
            border: 1px solid #e0e6e4; letter-spacing: 0.05em;
        }

        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
            border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        }
        .status-pill.approved { background: rgba(47, 138, 96, 0.1); color: #2f8a60; }
        .status-pill.pending { background: rgba(199, 154, 43, 0.1); color: #c79a2b; }
        .status-pill.rejected { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }

        .btn-action-sm {
            padding: 8px 14px; border-radius: 8px; border: 1.5px solid #eef2f1;
            background: white; color: #666; font-size: 0.85rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-action-sm:hover { border-color: var(--primary); color: var(--primary); background: #f8faf9; }

        /* ── Registration Form (Document Style) ── */
        .form-doc {
            background: white; border-radius: 16px; border: 1.5px solid var(--border);
            padding: 40px; margin-top: 32px; box-shadow: 0 8px 30px rgba(0,0,0,0.03);
            position: relative; overflow: hidden;
        }
        .form-doc::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--primary-dark); }
        .section-title {
            font-size: 0.75rem; font-weight: 900; color: var(--primary-dark);
            text-transform: uppercase; letter-spacing: 0.15em; margin: 24px 0 16px;
            display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f4f6f5; padding-bottom: 10px;
        }
        
        .vehicle-block {
            background: #fcfdfc; border: 1.5px solid #f0f4f3; border-radius: 12px;
            padding: 24px; margin-bottom: 20px; position: relative;
        }
        .btn-remove-vehicle {
            position: absolute; top: 12px; right: 12px; background: #fff1f0;
            color: #f5222d; border: 1px solid #ffa39e; border-radius: 6px;
            width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-remove-vehicle:hover { background: #f5222d; color: white; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 16px; }
        .form-group label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 8px; }
        .form-control {
            width: 100%; padding: 12px 16px; border: 1.5px solid #e0e6e4; border-radius: 8px;
            font-size: 0.95rem; font-weight: 600; color: #000; transition: all 0.2s;
        }
        .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(47, 138, 96, 0.1); }
        .form-control[readonly] { background: #f8faf9; cursor: not-allowed; }

        /* ── Modals ── */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 34, 31, 0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center;
            z-index: 2000; animation: fadeIn 0.3s ease;
        }
        .modal-overlay.active { display: flex; }
        .modal-container {
            background: white; border-radius: 20px; width: 100%; max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-header {
            padding: 24px 32px; border-bottom: 1.5px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
            background: #fcfdfc;
        }
        .modal-header h3 { font-family: 'Lora', serif; font-size: 1.3rem; font-weight: 800; color: var(--primary-dark); margin: 0; }
        .btn-close-modal { background: none; border: none; font-size: 1.8rem; color: #ccc; cursor: pointer; transition: color 0.2s; }
        .btn-close-modal:hover { color: #666; }

        /* ═══════════════════════════════════════════
           FORM DOCUMENT STYLES (Modern UI/UX)
           ═══════════════════════════════════════════ */
        .form-document {
            background: white; max-width: 900px; margin: 32px auto 0;
            border-radius: 12px; box-shadow: 0 2px 24px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .form-doc-header {
            background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
            padding: 28px 32px 20px; border-bottom: 3px solid var(--primary);
        }
        .form-doc-header-top { display: flex; align-items: center; gap: 20px; margin-bottom: 8px; }
        .form-doc-header-logo {
            width: 72px; height: 72px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--primary); flex-shrink: 0;
        }
        .form-doc-header-text { flex: 1; text-align: center; }
        .form-doc-header-text .arabic-line { font-size: 1rem; color: var(--primary); margin-bottom: 2px; direction: rtl; font-weight: 600; }
        .form-doc-header-text .org-name-ar { font-size: 0.9rem; color: var(--primary); direction: rtl; margin-bottom: 4px; font-weight: 600; }
        .form-doc-header-text .org-name-en { font-size: 0.82rem; font-weight: 700; color: var(--primary-dark); text-transform: uppercase; letter-spacing: 0.06em; }
        .form-doc-header-text .sec-reg { font-size: 0.68rem; color: var(--text-muted); margin-top: 2px; }
        .form-doc-title-bar { text-align: center; margin-top: 12px; }
        .form-doc-title {
            font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700; color: white;
            background: var(--primary-dark); padding: 8px 32px; border-radius: 6px;
            letter-spacing: 0.08em; text-transform: uppercase; display: inline-block;
        }
        .form-doc-body { padding: 24px 40px 32px; }
        .doc-section-title {
            font-family: 'Lora', serif; font-size: 0.9rem; font-weight: 700; color: var(--primary-dark);
            text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 0 8px;
            border-bottom: 2px solid #e8e2d0; margin: 24px 0 16px;
        }
        .doc-section-title:first-child { margin-top: 0; }

        .align-row { display: flex; align-items: center; margin-bottom: 16px; gap: 16px; }
        .align-label { width: 180px; font-weight: 700; color: var(--primary); font-size: 0.95rem; white-space: nowrap; flex-shrink: 0; }
        .align-input { flex: 1; }

        /* ── Parking Form Document (For Print Modal) ── */
        .parking-form-doc {
            width: 100%; max-width: 780px; margin: 0 auto;
            background: #fffef8; position: relative; font-family: 'Segoe UI', 'Inter', sans-serif;
        }
        .parking-form-doc .doc-header {
            padding: 28px 40px 16px; display: flex; align-items: flex-start; justify-content: space-between; gap: 20px;
        }
        .parking-form-doc .doc-header-left { flex: 1; }
        .parking-form-doc .doc-header-left .arabic-line {
            font-size: 0.85rem; color: var(--primary); text-align: left; direction: rtl; margin-bottom: 2px;
        }
        .parking-form-doc .doc-header-left .org-name-ar {
            font-size: 0.95rem; font-weight: 700; color: var(--primary); direction: rtl; text-align: left; margin-bottom: 2px;
        }
        .parking-form-doc .doc-header-left .org-name-en {
            font-size: 0.82rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.03em;
        }
        .parking-form-doc .doc-header-left .sec-reg {
            font-size: 0.68rem; color: #777; font-weight: 600; margin-top: 4px;
        }
        .parking-form-doc .doc-header-logo {
            width: 72px; height: 72px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e8e2d0; flex-shrink: 0;
        }
        .parking-form-doc .doc-title-bar {
            text-align: center; padding: 8px 40px 4px;
        }
        .parking-form-doc .doc-title-bar h2 {
            font-family: 'Lora', serif; font-size: 1rem; font-weight: 700;
            color: var(--primary); margin: 0 0 4px; text-transform: uppercase; letter-spacing: 0.08em;
        }
        .parking-form-doc .doc-title-bar .form-subtitle {
            display: inline-block; border: 1.5px solid var(--primary); padding: 5px 28px;
            font-size: 0.9rem; font-weight: 800; color: var(--primary); text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .parking-form-doc .doc-title-bar .form-date {
            text-align: right; font-size: 0.8rem; color: #555; margin-top: 10px;
        }
        .parking-form-doc .doc-body { padding: 16px 40px 20px; }
        .parking-form-doc .pf-row {
            display: flex; align-items: baseline; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;
        }
        .parking-form-doc .pf-label {
            font-size: 0.82rem; font-weight: 700; color: var(--primary); white-space: nowrap;
        }
        .parking-form-doc .pf-value {
            flex: 1; min-width: 100px; border-bottom: 1.5px solid #555; padding: 2px 6px;
            font-size: 0.85rem; font-weight: 600; color: #000; min-height: 20px;
        }
        .parking-form-doc .pf-addr-grid {
            display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; margin-bottom: 14px;
        }
        .parking-form-doc .pf-addr-grid .pf-addr-item { text-align: center; }
        .parking-form-doc .pf-addr-grid .pf-addr-item .pf-value { text-align: center; display: block; }
        .parking-form-doc .pf-addr-grid .pf-addr-item .pf-sub-label {
            font-size: 0.68rem; color: var(--primary); font-weight: 600; margin-top: 2px;
        }
        .parking-form-doc .pf-date-box {
            display: inline-block; border: 1.5px solid #333; padding: 8px 20px; margin: 18px 0;
        }
        .parking-form-doc .pf-date-box .pf-label { margin-right: 6px; }
        .parking-form-doc .pf-revised {
            text-align: right; font-size: 0.65rem; color: #aaa; font-style: italic; margin-top: 24px;
        }
        .parking-form-doc .doc-footer {
            background: linear-gradient(135deg, #1a3a2a, #2d6b4d); color: white;
            padding: 12px 40px 10px; text-align: center;
        }
        .parking-form-doc .doc-footer .footer-addr {
            font-size: 0.72rem; font-weight: 600; margin-bottom: 6px; opacity: 0.9;
        }
        .parking-form-doc .doc-footer .footer-contacts {
            display: flex; justify-content: center; gap: 24px; flex-wrap: wrap;
            font-size: 0.68rem; opacity: 0.8;
        }
        .parking-form-doc .doc-footer .footer-contacts span { display: flex; align-items: center; gap: 4px; }
        .parking-form-doc .doc-footer .footer-contacts svg { width: 12px; height: 12px; fill: currentColor; }

        .doc-input {
            border: none; border-bottom: 1.5px solid #999; border-radius: 0;
            background: transparent; padding: 2px 6px; font-size: 0.85rem;
            font-weight: 600; color: #1a3a2a; box-shadow: none; outline: none;
            font-family: inherit;
        }
        .doc-input:focus { border-bottom-color: var(--primary); background: #f0f4f3; }
        select.doc-input { appearance: none; -webkit-appearance: none; }

        /* ── Toast ── */
        .toast {
            position: fixed; top: 24px; right: 24px; padding: 16px 24px; border-radius: 12px;
            background: #333; color: white; z-index: 3000; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-weight: 600; display: none; animation: slideUp 0.3s ease;
        }

        /* ── Print Styles ── */
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .parking-form-doc { max-width: 100%; box-shadow: none; }
            .modal-overlay { position: static; background: none; }
            .modal-container { box-shadow: none; max-width: 100%; }
            .modal-header, .modal-actions-bar { display: none !important; }
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body>
    <!-- ═══ PERMIT DETAILS MODAL ═══ -->
    <div class="modal-overlay" id="permit-modal">
        <div class="modal-container" style="max-width:820px; max-height: 90vh; display: flex; flex-direction: column;">
            <div class="modal-header">
                <h3>Parking Rental Application Form</h3>
                <button class="btn-close-modal" onclick="closeModal('permit-modal')">&times;</button>
            </div>
            <div id="print-area" style="padding:24px; background:#f4f4f0; overflow-y: auto; flex: 1;">
                <div class="parking-form-doc" style="box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- ── HEADER ── -->
                    <div class="doc-header">
                        <div class="doc-header-left">
                            <div class="arabic-line">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
                            <div class="org-name-ar">مركز البحوث الإسلامية و الدعوة و الإرشاد في الفلبين</div>
                            <div class="org-name-en">Islamic Studies, Call and Guidance of the Philippines</div>
                            <div class="sec-reg">SEC. REG. NO. 0000185967</div>
                        </div>
                        <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="doc-header-logo" />
                    </div>

                    <!-- ── TITLE ── -->
                    <div class="doc-title-bar">
                        <h2>Application Form</h2>
                        <div class="form-subtitle">Parking Rental</div>
                        <div class="form-date">Date: <span id="p-date" style="border-bottom:1.5px solid #999; padding:0 20px;">—</span></div>
                    </div>

                    <!-- ── BODY ── -->
                    <div class="doc-body">
                        <!-- Parking No -->
                        <div class="pf-row">
                            <span class="pf-label">PARKING NO.:</span>
                            <span class="pf-value" id="p-id" style="max-width:140px;">—</span>
                        </div>

                        <!-- Name -->
                        <div class="pf-row">
                            <span class="pf-label">Name:</span>
                            <span class="pf-value" id="p-name">—</span>
                        </div>

                        <!-- DOB -->
                        <div class="pf-row">
                            <span class="pf-label">Date of Birth:</span>
                            <span class="pf-value" id="p-dob">—</span>
                        </div>

                        <!-- Complete Address -->
                        <div class="pf-row" style="margin-bottom:6px;">
                            <span class="pf-label">Complete Address:</span>
                            <span class="pf-value" id="p-address">—</span>
                        </div>
                        <div class="pf-addr-grid">
                            <div class="pf-addr-item">
                                <span class="pf-value" id="p-room">—</span>
                                <div class="pf-sub-label">Room No.</div>
                            </div>
                            <div class="pf-addr-item">
                                <span class="pf-value" id="p-bldg">—</span>
                                <div class="pf-sub-label">Bldg. No.</div>
                            </div>
                            <div class="pf-addr-item">
                                <span class="pf-value">Salitran I</span>
                                <div class="pf-sub-label">Brgy.</div>
                            </div>
                            <div class="pf-addr-item">
                                <span class="pf-value">Dasmariñas City</span>
                                <div class="pf-sub-label">Mun/City</div>
                            </div>
                        </div>

                        <!-- Vehicle Info -->
                        <div class="pf-row">
                            <span class="pf-label">Name of Vehicle:</span>
                            <span class="pf-value" id="p-vehicle">—</span>
                        </div>
                        <div class="pf-row">
                            <span class="pf-label">Name of Owner:</span>
                            <span class="pf-value" id="p-owner">—</span>
                        </div>
                        <div class="pf-row">
                            <span class="pf-label">Type of Vehicle:</span>
                            <span class="pf-value" id="p-type">—</span>
                        </div>
                        <div class="pf-row">
                            <span class="pf-label">Plate No.:</span>
                            <span class="pf-value" id="p-plate">—</span>
                        </div>

                        <!-- Date Started -->
                        <div class="pf-date-box">
                            <span class="pf-label">DATE STARTED:</span>
                            <span class="pf-value" id="p-datestarted" style="border-bottom:none; min-width:140px;">—</span>
                        </div>

                        <div class="pf-revised">Revised Since 2025</div>
                    </div>

                    <!-- ── FOOTER ── -->
                    <div class="doc-footer">
                        <div class="footer-addr">Jose Abad Santos Street, Salitran I, City of Dasmariñas City, Cavite, Philippines -4114</div>
                        <div class="footer-contacts">
                            <span><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> iscagphilippines@gmail.com</span>
                            <span><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> (046) 4161589</span>
                            <span><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg> /iscagphilippines</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions-bar" style="padding: 16px 32px; display:flex; gap:12px; justify-content:center; border-top:1.5px solid #f0f0f0;">
                <button class="btn-action-sm" onclick="window.print()"><svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print</button>
                <button class="btn-action-sm" onclick="downloadPermit()"><svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg> Download</button>
            </div>
        </div>
    </div>

    <!-- ═══ APP WRAPPER ═══ -->
    <div class="app-wrapper">
        <?php 
          $active_page = 'apartment_parking'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Parking Management</div>
                    <div class="top-bar-subtitle">Manage your vehicle access and permits</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="info-container">
                
                <?php if ($hasPending): ?>
                    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; color: #856404; font-weight: 600;">
                        <svg style="width:24px;height:24px;fill:currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <span>You have a pending parking application. Please wait for approval before submitting more vehicles.</span>
                    </div>
                <?php endif; ?>

                <!-- ═══ PARKING INFORMATION (APPROVED) ═══ -->
                <?php 
                    $approvedApps = array_filter($parkingApps, fn($app) => strtoupper($app['status'] ?? '') === 'APPROVED');
                ?>
                <?php if (!empty($approvedApps)): ?>
                <div class="dashboard-card" style="margin-bottom: 32px; border-left: 5px solid var(--primary);">
                    <div class="card-header">
                        <div class="card-title-box">
                            <div class="card-icon" style="background: var(--primary);">
                                <svg style="width:24px;height:24px;fill:currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            </div>
                            <h2 class="card-title">Active Parking Information</h2>
                        </div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--primary); background: #f0f7f3; padding: 6px 12px; border-radius: 20px;">
                            <?= count($approvedApps) ?> Active <?= count($approvedApps) > 1 ? 'Permits' : 'Permit' ?>
                        </div>
                    </div>
                    <div style="padding: 24px 32px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                            <?php foreach ($approvedApps as $app): ?>
                            <div style="background: #f8faf9; border: 1.5px solid #e0e6e4; border-radius: 12px; padding: 20px; position: relative; overflow: hidden;">
                                <div style="position: absolute; top: 0; right: 0; background: var(--primary); color: white; padding: 4px 12px; font-size: 0.7rem; font-weight: 800; border-bottom-left-radius: 8px;">ACTIVE</div>
                                
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                                    <div style="width: 40px; height: 40px; border-radius: 10px; background: white; border: 1px solid #e0e6e4; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                        <svg style="width:24px;height:24px;fill:currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size: 1rem; font-weight: 700; color: #000;"><?= htmlspecialchars($app['vehiclename']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--primary); font-weight: 600; text-transform: uppercase;"><?= htmlspecialchars($app['typeofvehicle']) ?></div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.85rem;">
                                    <div>
                                        <div style="color: #777; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">Plate Number</div>
                                        <div style="font-weight: 700; color: #000; letter-spacing: 0.05em;"><?= htmlspecialchars($app['plateno']) ?></div>
                                    </div>
                                    <div>
                                        <div style="color: #777; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">Date Started</div>
                                        <div style="font-weight: 700; color: #000;"><?= htmlspecialchars($app['datestarted'] ?: 'N/A') ?></div>
                                    </div>
                                    <div style="grid-column: span 2;">
                                        <div style="color: #777; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 2px;">Registered Owner</div>
                                        <div style="font-weight: 700; color: #000;"><?= htmlspecialchars($app['ownername']) ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ═══ DASHBOARD TABLE ═══ -->
                <?php if (!empty($parkingApps)): ?>
                <div class="dashboard-card" style="margin-bottom: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #e0e6e4;">
                    <div class="card-header" style="border-bottom: 1.5px solid #f0f0f0; padding: 24px; background: #fff;">
                        <h2 class="card-title" style="font-family: 'Lora', serif; color: var(--primary-dark); font-size: 1.25rem;">Registered Vehicles</h2>
                        <span class="status-pill approved" style="font-size:0.8rem; padding: 4px 12px;"><?= count($parkingApps) ?> Active</span>
                    </div>
                        <div style="overflow-x: auto;">
                            <table class="parking-table">
                                <thead>
                                    <tr>
                                        <th>Vehicle Details</th>
                                        <th>Plate Number</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parkingApps as $app): ?>
                                        <?php 
                                            $status = strtoupper($app['status'] ?? 'PENDING');
                                            $pillClass = ($status === 'APPROVED') ? 'approved' : (($status === 'REJECTED') ? 'rejected' : 'pending');
                                            $appJson = htmlspecialchars(json_encode($app), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="vehicle-info-cell">
                                                    <div class="vehicle-avatar"><svg style="width:20px;height:20px;fill:currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg></div>
                                                    <div>
                                                        <span class="vehicle-name-text"><?= htmlspecialchars($app['vehiclename']) ?></span>
                                                        <span class="vehicle-type-tag"><?= htmlspecialchars($app['typeofvehicle']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="plate-badge"><?= htmlspecialchars($app['plateno']) ?></span></td>
                                            <td><span class="status-pill <?= $pillClass ?>"><?= $status ?></span></td>
                                            <td style="text-align: right;">
                                                <button class="btn-action-sm" onclick='openPermit(<?= $appJson ?>)'>View Permit</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                </div>
                <?php endif; ?>

                <!-- ═══ REGISTRATION FORM ═══ -->
                <?php if (!$hasPending): ?>
                
                <div id="form-toggle-container" style="text-align: center; margin: 40px 0 20px;">
                    <button type="button" onclick="showRegistrationForm()" class="btn-action-sm" style="background: var(--primary); color: white; padding: 16px 48px; font-size: 1.05rem; border-radius: 50px; box-shadow: 0 10px 25px rgba(47, 138, 96, 0.2); border: none; font-weight: 700; transition: all 0.3s ease;">
                        <svg style="width:24px;height:24px;fill:currentColor;margin-right:10px;vertical-align:middle" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        Add New Vehicle
                    </button>
                </div>

                <form id="parking-form" class="form-document" style="display: none; margin-top: 20px; animation: slideUp 0.5s ease;">
                    <!-- ── HEADER ── -->
                    <div class="form-doc-header">
                        <div style="display: flex; justify-content: flex-end; margin-bottom: -10px;">
                            <button type="button" onclick="hideRegistrationForm()" style="background: #fff1f0; border: 1.5px solid #ffa39e; color: #f5222d; padding: 6px 16px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-transform: uppercase;">Cancel</button>
                        </div>
                        <div class="form-doc-header-top">
                            <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo" />
                            <div class="form-doc-header-text">
                                <div class="arabic-line">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>
                                <div class="org-name-ar">مركز البحوث الإسلامية و الدعوة و الإرشاد في الفلبين</div>
                                <div class="org-name-en">Islamic Studies, Call and Guidance of the Philippines</div>
                                <div class="sec-reg">SEC. REG. NO. 0000185967</div>
                            </div>
                        </div>
                        <div class="form-doc-title-bar">
                            <span class="form-doc-title">Parking Rental Application</span>
                        </div>
                    </div>

                    <!-- ── BODY ── -->
                    <div class="form-doc-body">
                        
                        <div style="text-align: right; margin-bottom: 24px;">
                            <span style="font-weight: 600; color: #555; margin-right: 8px;">Date:</span>
                            <input type="text" class="form-control" style="width: 160px; display: inline-block; text-align: center;" value="<?= date('Y-m-d') ?>" readonly />
                        </div>

                        <div class="align-row">
                            <div class="align-label">PARKING NO.:</div>
                            <input type="text" class="form-control align-input" style="color: #999; font-style: italic; max-width: 240px;" value="To be generated" readonly />
                        </div>

                        <div class="align-row">
                            <div class="align-label">Name:</div>
                            <input type="text" class="form-control align-input" value="<?= htmlspecialchars($fullName) ?>" readonly />
                        </div>

                        <div class="align-row">
                            <div class="align-label">Date of Birth:</div>
                            <input type="text" class="form-control align-input" value="<?= htmlspecialchars($dob) ?>" readonly />
                        </div>

                        <div class="align-row" style="align-items: flex-start;">
                            <div class="align-label" style="margin-top: 10px;">Complete Address:</div>
                            <div class="align-input">
                                <input type="text" class="form-control" value="ISCAG Apartment, Salitran I, Dasmariñas City" readonly style="margin-bottom: 12px;" />
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px;">
                                    <div>
                                        <input type="text" class="form-control" style="text-align: center;" value="<?= htmlspecialchars($assignedRoom) ?>" readonly />
                                        <div style="font-size: 0.75rem; color: #777; text-align: center; margin-top: 4px; font-weight: 600;">Room No.</div>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control" style="text-align: center;" value="<?= htmlspecialchars($assignedBldg) ?>" readonly />
                                        <div style="font-size: 0.75rem; color: #777; text-align: center; margin-top: 4px; font-weight: 600;">Bldg. No.</div>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control" style="text-align: center;" value="Salitran I" readonly />
                                        <div style="font-size: 0.75rem; color: #777; text-align: center; margin-top: 4px; font-weight: 600;">Brgy.</div>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control" style="text-align: center;" value="Dasmariñas City" readonly />
                                        <div style="font-size: 0.75rem; color: #777; text-align: center; margin-top: 4px; font-weight: 600;">Mun/City</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="doc-section-title" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Vehicle Details</span>
                            <button type="button" onclick="addVehicleBlock()" style="background: none; border: 1.5px solid var(--primary); color: var(--primary-dark); padding: 4px 16px; border-radius: 6px; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-transform: uppercase;">+ Add Vehicle</button>
                        </div>
                        
                        <div id="vehicles-container">
                            <div class="vehicle-block-doc" style="margin-top: 16px; position: relative;">
                                <div class="align-row">
                                    <div class="align-label">Name of Vehicle:</div>
                                    <input type="text" class="form-control align-input v-name" placeholder="Enter vehicle name" required />
                                </div>
                            <div class="align-row">
                                <div class="align-label">Name of Owner:</div>
                                <input type="text" class="form-control align-input v-owner" placeholder="Enter owner's name" required />
                            </div>
                            <div class="align-row">
                                <div class="align-label">Type of Vehicle:</div>
                                <select class="form-control align-input v-type" required>
                                    <option value="" disabled selected>Select vehicle type</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV / AUV</option>
                                    <option value="Pickup">Pickup Truck</option>
                                    <option value="Van">Van / MPV</option>
                                    <option value="Hatchback">Hatchback</option>
                                    <option value="Wagon">Station Wagon</option>
                                    <option value="Motorcycle">Motorcycle</option>
                                </select>
                            </div>
                            <div class="align-row">
                                <div class="align-label">Plate No.:</div>
                                <input type="text" class="form-control align-input v-plate" style="text-transform: uppercase;" placeholder="Enter plate number" required />
                            </div>
                        </div>
                    </div>

                        <div class="align-row" style="margin-top: 32px; padding: 24px; background: #f8faf9; border-radius: 8px; border: 1.5px solid #d1dbd8; width: max-content;">
                            <div class="align-label" style="width: auto;">DATE STARTED:</div>
                            <input type="date" id="date-started" class="form-control" style="width: 180px; margin-left: 12px;" value="<?= date('Y-m-d') ?>" required />
                        </div>

                        <div style="margin-top: 40px; text-align: right; border-top: 1.5px solid #f0f0f0; padding-top: 24px;">
                            <button type="submit" id="btn-submit" class="btn-action-sm" style="padding: 14px 40px; background: var(--primary-dark); color: white; border: none; font-size: 1rem;">Submit Application</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <!-- ═══ SCRIPTS ═══ -->
    <script>
        function showRegistrationForm() {
            document.getElementById('parking-form').style.display = 'block';
            document.getElementById('form-toggle-container').style.display = 'none';
            document.getElementById('parking-form').scrollIntoView({ behavior: 'smooth' });
        }

        function hideRegistrationForm() {
            document.getElementById('parking-form').style.display = 'none';
            document.getElementById('form-toggle-container').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function addVehicleBlock() {
            const container = document.getElementById('vehicles-container');
            const newBlock = document.createElement('div');
            newBlock.className = 'vehicle-block-doc';
            newBlock.style.marginTop = '24px';
            newBlock.style.paddingTop = '24px';
            newBlock.style.borderTop = '1px dashed #ccc';
            newBlock.style.position = 'relative';

            newBlock.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top: 16px; right: 0; background: none; border: none; color: #cc0000; font-size: 1.2rem; cursor: pointer;" title="Remove Vehicle">&times;</button>
                <div class="align-row">
                    <div class="align-label">Name of Vehicle:</div>
                    <input type="text" class="form-control align-input v-name" placeholder="Enter vehicle name" required />
                </div>
                <div class="align-row">
                    <div class="align-label">Name of Owner:</div>
                    <input type="text" class="form-control align-input v-owner" placeholder="Enter owner's name" required />
                </div>
                <div class="align-row">
                    <div class="align-label">Type of Vehicle:</div>
                    <select class="form-control align-input v-type" required>
                        <option value="" disabled selected>Select vehicle type</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV / AUV</option>
                        <option value="Pickup">Pickup Truck</option>
                        <option value="Van">Van / MPV</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Wagon">Station Wagon</option>
                        <option value="Motorcycle">Motorcycle</option>
                    </select>
                </div>
                <div class="align-row">
                    <div class="align-label">Plate No.:</div>
                    <input type="text" class="form-control align-input v-plate" style="text-transform: uppercase;" placeholder="Enter plate number" required />
                </div>
            `;
            container.appendChild(newBlock);
        }

        // ── FORM SUBMISSION ──
        const form = document.getElementById('parking-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('btn-submit');
                btn.disabled = true;
                btn.textContent = 'Submitting...';

                const vehicles = [];
                document.querySelectorAll('.vehicle-block-doc').forEach(block => {
                    vehicles.push({
                        vehicleName: block.querySelector('.v-name').value,
                        vehicleOwner: block.querySelector('.v-owner').value,
                        vehicleType: block.querySelector('.v-type').value,
                        plateNo: block.querySelector('.v-plate').value
                    });
                });

                const payload = {
                    dateStarted: document.getElementById('date-started').value,
                    vehicles: vehicles
                };

                fetch('<?= url("/user/apartment/parking/submit") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Application submitted successfully!', 'var(--success)');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Error submitting application.', '#f5222d');
                        btn.disabled = false;
                        btn.textContent = 'Submit Application';
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Network error. Please try again.', '#f5222d');
                    btn.disabled = false;
                    btn.textContent = 'Submit Application';
                });
            });
        }

        // ── PERMIT MODAL ──
        function openPermit(app) {
            // Parking No
            document.getElementById('p-id').textContent = '#PKG-' + String(app.parking_id).padStart(4, '0');
            
            // Personal Info (from PHP session data)
            document.getElementById('p-name').textContent = '<?= htmlspecialchars($fullName) ?>';
            document.getElementById('p-dob').textContent = '<?= htmlspecialchars($dob) ?>';
            document.getElementById('p-room').textContent = '<?= htmlspecialchars($assignedRoom) ?>';
            document.getElementById('p-bldg').textContent = '<?= htmlspecialchars($assignedBldg) ?>';
            document.getElementById('p-address').textContent = 'ISCAG Apartment, Salitran I, Dasmariñas City';
            
            // Vehicle Info
            document.getElementById('p-vehicle').textContent = app.vehiclename || '—';
            document.getElementById('p-owner').textContent = app.ownername || '—';
            document.getElementById('p-type').textContent = app.typeofvehicle || '—';
            document.getElementById('p-plate').textContent = app.plateno || '—';
            
            // Dates
            const dateStarted = app.datestarted ? new Date(app.datestarted).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
            document.getElementById('p-datestarted').textContent = dateStarted;
            
            const submittedDate = app.date ? new Date(app.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
            document.getElementById('p-date').textContent = submittedDate;
            
            document.getElementById('permit-modal').classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function downloadPermit() {
            const area = document.getElementById('print-area');
            html2canvas(area, { scale: 2, useCORS: true }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Permit-${document.getElementById('p-plate').textContent}.png`;
                link.href = canvas.toDataURL();
                link.click();
            });
        }

        function showToast(msg, bg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.background = bg;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        // Close modal on outside click
        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>