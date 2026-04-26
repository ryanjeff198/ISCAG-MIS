<?php
require_once BASE_PATH . '/app/models/ApartmentApp.php';
$apaModel = new ApartmentApp();
$userId = $_SESSION['user_id'];
$appInfo = $apaModel->getApplication($userId);
$userFullInfo = $apaModel->getInfo($userId);

$assignedRoom = $appInfo['room_number'] ?? '';
$assignedBldg = $appInfo['building'] ?? '';
$fullName = ($userFullInfo['givenname'] ?? '') . ' ' . ($userFullInfo['familyname'] ?? '');
$dob = $userFullInfo['birthdate'] ?? '';
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
        .info-container { max-width: 960px; margin: 0 auto; padding: 24px; }

        /* ── Status Hero (Consistency with Apartment Info) ── */
        .status-hero {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06); overflow: hidden;
            margin-bottom: 24px; animation: slideUp 0.4s ease;
        }
        .status-hero-top {
            background: linear-gradient(135deg, var(--primary-dark), #1c6370);
            padding: 40px 32px; position: relative; overflow: hidden;
        }
        .status-hero-top::before {
            content: ''; position: absolute; right: -20px; bottom: -20px;
            width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.05);
        }
        .status-hero-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; position: relative; z-index: 1; }
        .hero-left { display: flex; align-items: center; gap: 20px; }
        .hero-icon-box {
            width: 64px; height: 64px; border-radius: 50%; background: rgba(255,255,255,0.15);
            backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center;
            border: 2px solid rgba(255,255,255,0.25); flex-shrink: 0;
        }
        .hero-icon-box svg { width: 32px; height: 32px; fill: white; }
        
        .hero-title { font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 800; color: white; margin: 0 0 4px; }
        .hero-subtitle { font-size: 0.85rem; color: rgba(255,255,255,0.7); margin: 0; letter-spacing: 0.05em; text-transform: uppercase; font-weight: 600; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 20px; border-radius: 24px; font-size: 0.72rem;
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;
            backdrop-filter: blur(4px);
        }
        .status-badge.approved { background: rgba(47, 138, 96, 0.2); color: #7ee8b0; border: 1px solid rgba(47, 138, 96, 0.3); }
        .status-badge.pending { background: rgba(199, 154, 43, 0.2); color: #ffd666; border: 1px solid rgba(199, 154, 43, 0.3); }
        .status-badge.rejected { background: rgba(139, 46, 46, 0.2); color: #f59090; border: 1px solid rgba(139, 46, 46, 0.3); }
        .status-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: statusPulse 2s ease infinite; }

        /* ── Details Grid ── */
        .status-body { padding: 40px; background: white; }
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 32px; margin-bottom: 40px; }
        .detail-item label { display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px; }
        .detail-item p { font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin: 0; }
        
        .plate-box {
            display: inline-flex; align-items: center; background: #fffdf5; color: #b8860b;
            font-family: 'Source Code Pro', monospace; font-weight: 900;
            font-size: 1.2rem; padding: 6px 16px; border: 2px solid #f0e6cc;
            border-radius: 6px; letter-spacing: 0.1em;
        }

        /* ── REGISTRATION FORM (Form-Document Style) ── */
        .form-document {
            background: white; padding: 60px; border-radius: 16px; border: 1px solid var(--border);
            max-width: 960px; margin: 0 auto; box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            position: relative; overflow: hidden; animation: slideUp 0.4s ease;
        }
        .form-document::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(to right, var(--primary-dark), #c9a84c); }
        .form-doc-header { border-bottom: 2px solid var(--primary-dark); padding-bottom: 24px; margin-bottom: 40px; }
        .form-doc-header-top { display: flex; align-items: center; gap: 24px; margin-bottom: 20px; }
        .form-doc-header-logo { width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--border); padding: 4px; background: white; }
        
        .form-doc-title-bar { background: #f8faf9; padding: 16px 24px; border-left: 6px solid var(--primary-dark); margin-top: 20px; }
        .form-doc-title { font-family: 'Lora', serif; font-size: 1.6rem; font-weight: 900; color: var(--primary-dark); }
        .form-doc-subtitle { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.15em; font-weight: 700; }

        .doc-section-title {
            font-size: 0.75rem; font-weight: 900; color: var(--primary-dark);
            text-transform: uppercase; letter-spacing: 0.15em; margin: 32px 0 16px;
            display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;
        }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .field-label { width: 160px; padding: 16px; font-size: 0.85rem; font-weight: 800; color: var(--text-muted); background: #fcfdfc; border: 1.5px solid var(--border); }
        .field-value { padding: 12px 16px; border: 1.5px solid var(--border); background: white; }
        .field-value input, .field-value select { width: 100%; border: none; outline: none; font-size: 1rem; color: var(--text-main); font-family: inherit; font-weight: 700; }

        /* ── Buttons ── */
        .btn-action {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 12px 28px; border-radius: 10px;
            font-size: 0.9rem; font-weight: 800;
            text-decoration: none; transition: all 0.3s ease;
            cursor: pointer; border: none; font-family: inherit;
        }
        .btn-action.primary { background: linear-gradient(135deg, var(--primary-dark), #237a8a); color: white; box-shadow: 0 8px 20px rgba(22, 78, 88, 0.2); }
        .btn-action.primary:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(22, 78, 88, 0.3); }
        .btn-action.outline { background: white; color: var(--text-muted); border: 2px solid var(--border); }
        .btn-action.outline:hover { border-color: var(--primary); color: var(--primary); background: #f8faf9; }

        /* ── Modal (Registration) ── */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 34, 31, 0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center;
            z-index: 1000; animation: fadeIn 0.3s ease;
        }
        .modal-container {
            background: white; border-radius: 20px; width: 100%; max-width: 720px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1); margin: 20px;
        }
        .modal-header {
            padding: 24px 32px; border-bottom: 1.5px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
            background: #fcfdfc;
        }
        .modal-header h3 { font-family: 'Lora', serif; font-size: 1.3rem; font-weight: 800; color: var(--primary-dark); margin: 0; }
        .btn-close-modal { background: none; border: none; font-size: 1.8rem; color: #ccc; cursor: pointer; transition: color 0.2s; }
        .btn-close-modal:hover { color: #666; }
        .modal-body { padding: 32px; }

        /* ── Concept Vehicle Container ── */
        .vehicle-concept-container {
            background: white; border-radius: 12px; border: 1.5px solid #eef2f1;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-top: 32px; overflow: hidden;
            animation: slideUp 0.6s ease;
        }
        .concept-header {
            padding: 24px 32px; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1.5px solid #f4f6f5;
        }
        .concept-header-left { display: flex; align-items: center; gap: 16px; }
        .concept-icon-box {
            width: 48px; height: 48px; border-radius: 12px;
            background: linear-gradient(135deg, #e67e22, #d35400);
            display: flex; align-items: center; justify-content: center;
            color: white; box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }
        .concept-header h3 { font-family: 'Lora', serif; font-size: 1.3rem; font-weight: 800; color: #164e58; margin: 0; }
        
        .btn-concept-add {
            background: white; border: 1.5px solid #eef2f1; border-radius: 8px;
            padding: 10px 20px; font-size: 0.85rem; font-weight: 700; color: #666;
            display: flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;
        }
        .btn-concept-add:hover { background: #f8faf9; border-color: #2f8a60; color: #2f8a60; }

        .concept-summary-row {
            display: grid; grid-template-columns: 1fr 1fr; padding: 24px 32px;
            background: #fff; border-bottom: 1.5px solid #f4f6f5;
        }
        .summary-item label { display: block; font-size: 0.72rem; font-weight: 800; color: #999; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
        .summary-item span { font-size: 1.1rem; font-weight: 700; color: #333; font-family: 'Lora', serif; }

        .concept-table { width: 100%; border-collapse: collapse; }
        .concept-table thead { background: #164e58; }
        .concept-table th {
            padding: 14px 32px; text-align: left; font-size: 0.72rem;
            font-weight: 800; color: rgba(255,255,255,0.9); text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .concept-table td { padding: 18px 32px; font-size: 0.9rem; color: #444; border-bottom: 1px solid #f4f6f5; }
        .concept-table tr:hover td { background: #fcfdfc; }
        
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
            border-radius: 20px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .status-pill.approved { background: rgba(47, 138, 96, 0.1); color: #2f8a60; }
        .status-pill.pending { background: rgba(199, 154, 43, 0.1); color: #c79a2b; }
        .status-pill.rejected { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }

        .btn-view {
            padding: 10px 18px; border-radius: 8px; border: 1px solid transparent;
            background: #f0f7f6; color: #164e58; font-size: 0.9rem; font-weight: 700;
            cursor: pointer; transition: all 0.15s ease-out;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-view:hover { 
            background: #164e58; color: white; border-color: #164e58;
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(22, 78, 88, 0.1);
        }
        .btn-view svg { transition: transform 0.15s ease-out; }
        .btn-view:hover svg { transform: scale(1.05); }

        .btn-download {
            padding: 10px 18px; border-radius: 8px; border: 1.5px solid #eef2f1;
            background: white; color: #666; font-size: 0.9rem; font-weight: 700;
            cursor: pointer; transition: all 0.15s ease-out;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-download:hover { background: #f8faf9; border-color: #164e58; color: #164e58; transform: translateY(-1px); }

        /* ── Permit Modal Specific ── */
        .permit-card-modal {
            background: #f8faf9; border-radius: 12px; border: 2px dashed #d1dbd8;
            padding: 32px; margin-bottom: 24px; position: relative;
            overflow: hidden;
        }
        .permit-card-modal::before {
            content: 'ISCAG OFFICIAL'; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg); font-size: 4rem;
            font-weight: 900; color: rgba(47, 138, 96, 0.03); white-space: nowrap; pointer-events: none;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes statusPulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.9); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        
        @media print {
            .app-wrapper, .modal-header, .top-bar, .form-submit-row { display: none !important; }
            .modal-overlay { background: white !important; position: relative !important; display: block !important; padding: 0 !important; }
            .modal-container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; }
            .modal-body { padding: 0 !important; }
            .permit-card-modal { border: 2px solid #333 !important; }
        }
    </style>
</head>

        .reject-feedback h5 {
            font-family: 'Lora', serif;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--danger);
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .reject-feedback h5 svg {
            width: 16px;
            height: 16px;
            fill: var(--danger);
        }

        .reject-feedback p {
            font-size: 0.82rem;
            color: var(--text-main);
            margin: 0;
            line-height: 1.6;
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

        /* ── Scrollbar ── */
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

        /* ── Success Modal ── */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .success-modal {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            transform: translateY(20px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .modal-overlay.active .success-modal {
            transform: translateY(0) scale(1);
        }
        .success-modal-icon {
            width: 72px;
            height: 72px;
            background: rgba(47, 138, 96, 0.1);
            color: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .success-modal-icon svg {
            width: 36px;
            height: 36px;
            fill: currentColor;
        }
        .success-modal h3 {
            font-family: 'Lora', serif;
            font-size: 1.4rem;
            color: var(--primary-dark);
            margin: 0 0 12px;
        }
        .success-modal p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0 0 32px;
            line-height: 1.5;
        }
        .success-modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .success-modal-btn:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-2px);
        }

        /* ── Animations ── */
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

        /* ── Responsive ── */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 220px;
            }

            .page-body {
                padding: 18px;
            }

            .form-doc-header-top {
                flex-direction: column;
                text-align: center;
            }

            .date-row {
                flex-direction: column;
                gap: 12px;
                padding: 16px 18px 12px;
            }

            .address-grid {
                grid-template-columns: 1fr 1fr;
            }

            .form-doc-body {
                padding: 16px 18px 24px;
            }

            .form-submit-row {
                padding: 16px 18px;
                justify-content: center;
            }

            .signature-grid {
                grid-template-columns: 1fr;
            }

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
                            <div class="summary-item">
                                <label>Vehicle Name</label>
                                <span id="detail-vehicle-name" style="font-size:1.2rem;">Toyota Vios</span>
                            </div>
                            <div class="summary-item">
                                <label>Plate Number</label>
                                <span id="detail-plate-no" style="font-family:monospace; background:#fff; padding:4px 12px; border:1px solid #d1dbd8; border-radius:6px; letter-spacing:0.1em;">ABC 1234</span>
                            </div>
                            <div class="summary-item">
                                <label>Authorized Holder</label>
                                <span id="detail-owner-name">Mis Rey</span>
                            </div>
                            <div class="summary-item">
                                <label>Vehicle Type</label>
                                <span id="detail-vehicle-type">Sedan (4-Door)</span>
                            </div>
                        </div>

                        <div style="margin-top:32px; padding-top:16px; border-top:1px dashed #d1dbd8; font-size:0.7rem; color:#888; line-height:1.5;">
                            This permit authorizes the vehicle above to access the ISCAG Apartment parking facilities. Non-transferable and must be presented upon request.
                        </div>
                    </div>
                </div>

                <div class="form-submit-row" style="display:flex; justify-content:center; gap:16px;">
                    <button class="btn-action outline" onclick="closePermitModal()">Close Details</button>
                    <button class="btn-download" onclick="downloadPermit()">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        Download
                    </button>
                    <button class="btn-action primary" onclick="window.print()" id="btn-print-permit">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;margin-right:8px;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                        Print Permit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ REGISTRATION MODAL ═══ -->
    <div class="modal-overlay" id="registration-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Vehicle Registration</h3>
                <button class="btn-close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="doc-section-title" style="margin-top: 0;">Vehicle Details</div>
                <table class="info-table">
                    <tr>
                        <td class="field-label">Vehicle Name</td>
                        <td class="field-value" colspan="3"><input type="text" id="vehicle-name" placeholder="e.g. Toyota Vios 2023" /></td>
                    </tr>
                    <tr>
                        <td class="field-label">Plate No.</td>
                        <td class="field-value"><input type="text" id="plate-no" style="text-transform:uppercase; font-family:monospace; font-weight:800; letter-spacing:0.1em;" placeholder="ABC 1234" /></td>
                        <td class="field-label">Type</td>
                        <td class="field-value">
                            <select id="vehicle-type">
                                <option value="Sedan">Sedan (4-Door)</option>
                                <option value="SUV">SUV / AUV</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Pick-up">Pick-up Truck</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="form-submit-row" style="margin-top:24px; display:flex; justify-content:flex-end; gap: 12px;">
                    <button class="btn-action outline" onclick="closeModal()">Cancel</button>
                    <button class="btn-action primary" id="btn-submit" type="button" style="padding: 14px 40px;">Submit Registration</button>
                </div>
            </div>
        </div>
    </div>

<body>
    <?php if (isset($hasPendingParking) && $hasPendingParking): ?>
    <div class="modal-overlay active" style="z-index: 999999; display: flex !important;">
        <div class="success-modal">
            <div class="success-modal-icon" style="background: rgba(246, 194, 62, 0.1); color: var(--warning);">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <h3 style="color: var(--text-main);">Application is Pending</h3>
            <p>You have a parking rental application that is currently awaiting administrative approval. Please wait for the admins to process your existing application before submitting a new one.</p>
            <a href="<?= url('/user/apartment/info') ?>" class="success-modal-btn" style="background: linear-gradient(135deg, var(--text-main), var(--text-muted));">Return to Dashboard</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="app-wrapper">
        <!-- ═══ SIDEBAR ═══ -->
        <?php 
          $active_page = 'apartment_parking'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Parking Rental</div>
                    <div class="top-bar-subtitle">Manage your apartment vehicle access and permits</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="info-container">
                <?php 
                $hasApps = isset($parkingApps) && !empty($parkingApps);
                ?>

                <?php if ($hasApps): ?>
                    <!-- ═══ CONCEPT VEHICLE CONTAINER ═══ -->
                    <div class="vehicle-concept-container">
                        <div class="concept-header">
                            <div class="concept-header-left">
                                <div class="concept-icon-box">
                                    <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: currentColor;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
                                </div>
                                <h3>Vehicle Information & Permits</h3>
                            </div>
                            <button class="btn-concept-add" onclick="openModal()">
                                <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; fill: currentColor;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                Add Vehicle
                            </button>
                        </div>

                        <div class="concept-summary-row">
                            <div class="summary-item">
                                <label>Primary Tenant</label>
                                <span><?= htmlspecialchars($_SESSION['name'] ?? 'Authorized Tenant') ?></span>
                            </div>
                            <div class="summary-item">
                                <label>Contact</label>
                                <span><?= htmlspecialchars($parkingApps[0]['contactnum'] ?? 'Not Provided') ?></span>
                            </div>
                        </div>

                        <!-- FORM BODY -->
                        <div class="form-doc-body">

                            <!-- PERSONAL INFORMATION -->
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                                </svg>
                                Personal Information
                            </div>

                            <table class="info-table">
                                <tr>
                                    <td class="field-label">Full Name</td>
                                    <td class="field-value" colspan="3">
                                        <input type="text" id="full-name" placeholder="Enter your full name" value="<?= htmlspecialchars($fullName) ?>" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="field-label">Date of Birth</td>
                                    <td class="field-value" colspan="3">
                                        <input type="date" id="date-of-birth" value="<?= htmlspecialchars($dob) ?>" readonly />
                                    </td>
                                </tr>
                            </table>

                            
                            
                                    
                                      
                            </table>

                            <div class="address-grid">
                                <div class="addr-cell">
                                    <div class="addr-label">Room No.</div>
                                    <input type="text" id="room-no" placeholder="e.g. 101" value="<?= htmlspecialchars($assignedRoom) ?>" readonly />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Bldg. No.</div>
                                    <input type="text" id="bldg-no" placeholder="e.g. A" value="<?= htmlspecialchars($assignedBldg) ?>" readonly />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Barangay</div>
                                    <input type="text" id="brgy" value="Salitran I" readonly />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Municipality / City</div>
                                    <input type="text" id="mun-city" value="Dasmariñas City" readonly />
                                </div>
                            </div>

                            <!-- VEHICLE INFORMATION -->
                            <div class="doc-section-title" style="display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                                    </svg>
                                    Vehicle Information
                                </div>
                                <button type="button" id="btn-add-vehicle" style="font-size:0.85rem; padding:6px 14px; background:var(--primary); color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer; align-items:center; display:flex; gap:6px;">
                                    <svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Add Vehicle
                                </button>
                            </div>

                            <div id="vehicles-container">
                                <div class="vehicle-block" style="border: 1.5px solid var(--border); padding: 20px; border-radius: 12px; margin-bottom: 20px; position:relative; background: #fafafa;">
                                    <div class="vehicle-header" style="font-weight:800; margin-bottom:16px; font-size:1rem; color:var(--primary-dark); display:flex; align-items:center; gap:8px;">
                                        <svg style="width:20px;height:20px;fill:currentColor;" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg> 
                                        <span>Vehicle #1</span>
                                    </div>
                                    <table class="info-table" style="background:white;">
                                        <tr>
                                            <td class="field-label">Name of Vehicle</td>
                                            <td class="field-value" colspan="3">
                                                <input type="text" class="vehicle-name" placeholder="e.g. Toyota Vios" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="field-label">Name of Owner</td>
                                            <td class="field-value" colspan="3">
                                                <input type="text" class="vehicle-owner" placeholder="Enter vehicle owner's name" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="field-label">Type of Vehicle</td>
                                            <td class="field-value">
                                                <select class="vehicle-type">
                                                    <option value="">Select type...</option>
                                                    <option value="Sedan">Sedan</option>
                                                    <option value="SUV">SUV</option>
                                                    <option value="Van">Van</option>
                                                    <option value="Motorcycle">Motorcycle</option>
                                                    <option value="Pickup Truck">Pickup Truck</option>
                                                    <option value="Hatchback">Hatchback</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </td>
                                            <td class="field-label">Plate No.</td>
                                            <td class="field-value">
                                                <input type="text" class="plate-no" placeholder="e.g. ABC 1234" style="text-transform:uppercase;font-weight:600;" />
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- DATE STARTED -->
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" />
                                </svg>
                                Rental Period
                            </div>

                            <table class="info-table">
                                <tr>
                                    <td class="field-label">Date Started</td>
                                    <td class="field-value" colspan="3">
                                        <input type="date" id="date-started" />
                                    </td>
                                </tr>
                            </table>

                            <!-- SUBMIT ROW -->
                            <div class="form-submit-row">
                                
                                <button class="btn-submit" id="btn-submit" type="button">
                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                    Submit Application
                                </button>
                            </div>
                            <h3 class="hero-title" style="font-size: 1.8rem;">No Vehicle Registered</h3>
                            <p style="color: rgba(255,255,255,0.8); max-width: 500px; margin: 12px auto 32px; line-height: 1.6;">Register your vehicle to secure a parking permit and gain authorized access to the apartment parking areas.</p>
                            <button class="btn-action primary" onclick="openModal()" style="background: var(--accent); color: var(--primary-dark); box-shadow: 0 8px 24px rgba(201, 168, 76, 0.3);">
                                <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor; margin-right: 8px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                Register Vehicle Now
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            </div>
            </div>
        </div>
    </div>

    <script>
        const STORAGE_KEYS = { user: 'mis_user' };
        const user = JSON.parse(localStorage.getItem(STORAGE_KEYS.user) || '{}');
        const navName = document.getElementById('nav-name');
        if (navName) navName.textContent = user.name || 'User';

        function openModal() {
            document.getElementById('registration-modal').style.display = 'flex';
            document.getElementById('vehicle-name').focus();
        }

        function closeModal() {
            document.getElementById('registration-modal').style.display = 'none';
        }

        function viewDetails(app) {
            const status = (app.status || 'PENDING').toUpperCase();
            const statusLabel = status === 'APPROVED' ? 'Approved' : (status === 'REJECTED' ? 'Rejected' : 'Under Review');
            
            document.getElementById('detail-status').textContent = statusLabel;
            document.getElementById('detail-status').className = 'status-pill ' + status.toLowerCase();
            document.getElementById('detail-permit-id').textContent = '#PKG-' + String(app.parking_id).padStart(4, '0');
            document.getElementById('detail-vehicle-name').textContent = app.vehiclename;
            document.getElementById('detail-plate-no').textContent = app.plateno;
            document.getElementById('detail-owner-name').textContent = app.ownername || 'Registered Tenant';
            document.getElementById('detail-vehicle-type').textContent = app.typeofvehicle;
            
            document.getElementById('permit-modal').style.display = 'flex';
        }

        function closePermitModal() {
            document.getElementById('permit-modal').style.display = 'none';
        }

        function downloadPermit(app = null) {
            // If app is provided, we do a "Silent Download" by creating a hidden element
            const isSilent = app !== null;
            let targetArea;
            let ghostContainer = null;

            if (isSilent) {
                // Create a "Ghost" container for background rendering
                ghostContainer = document.createElement('div');
                ghostContainer.style.position = 'fixed';
                ghostContainer.style.top = '-2000px';
                ghostContainer.style.left = '-2000px';
                ghostContainer.style.width = '600px';
                ghostContainer.innerHTML = `
                    <div class="permit-card-modal" style="margin:0; background:#f8faf9; border:2px solid #d1dbd8;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1.5px solid #d1dbd8; padding-bottom:16px;">
                            <img src="<?= asset('assets/logo.jpg') ?>" style="height:50px; border-radius:6px;" />
                            <div style="text-align:right;">
                                <div class="status-pill ${app.status.toLowerCase()}">${app.status.toUpperCase() === 'APPROVED' ? 'Approved' : (app.status.toUpperCase() === 'REJECTED' ? 'Rejected' : 'Under Review')}</div>
                                <div style="font-size:0.65rem; color:#999; margin-top:4px; font-weight:800;">PERMIT ID: #PKG-${String(app.parking_id).padStart(4, '0')}</div>
                            </div>

                    </div><!-- /.form-document -->

                </div><!-- /.page-body -->
            </div><!-- /.main-content -->
        <!-- Success Modal -->
        <div class="modal-overlay" id="success-modal">
            <div class="success-modal">
                <div class="success-modal-icon">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
                </div>
                <h3>Application Submitted!</h3>
                <p>Your parking rental application has been successfully submitted and is now queued for administrative review. You can track its status in your tenant portal.</p>
                <a href="<?= url('/user/apartment/info') ?>" class="success-modal-btn">Continue to Dashboard</a>
            </div>
        </div>

        </div><!-- /.app-wrapper -->

        <script>
            // ══════════════════════════════════════════
            // DATA HELPERS
            // ══════════════════════════════════════════
            const STORAGE_KEYS = { user: 'mis_user', parking: 'mis_parking_applications' };
            const DEFAULT_USER = { id: 'USR-001', name: 'Muhammad Usman', email: 'musman@example.com', gender: '', phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', membership: '', revertYear: '', apartment: '', profileComplete: false };

            function getUser() {
                const raw = localStorage.getItem(STORAGE_KEYS.user);
                return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
            }

            function getParkingApps() {
                const raw = localStorage.getItem(STORAGE_KEYS.parking);
                return raw ? JSON.parse(raw) : [];
            }

            function saveParkingApps(apps) {
                localStorage.setItem(STORAGE_KEYS.parking, JSON.stringify(apps));
            }

            function generateParkingId() {
                const apps = getParkingApps();
                const num = apps.length + 1;
                return 'PKG-' + String(num).padStart(4, '0');
            }

            const btn = document.querySelector('.btn-download');
            const originalText = btn.innerHTML;
            
            if (!isSilent) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner" style="width:14px;height:14px;border:2px solid #666;border-top-color:transparent;border-radius:50%;display:inline-block;animation:statusPulse 1s infinite;"></span> Saving...';
            }

            html2canvas(targetArea, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                const plate = isSilent ? app.plateno : document.getElementById('detail-plate-no').textContent.trim();
                link.download = `ISCAG-Permit-${plate}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                if (!isSilent) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                if (ghostContainer) document.body.removeChild(ghostContainer);
            }).catch(err => {
                console.error('Download error:', err);
                if (!isSilent) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
                if (ghostContainer) document.body.removeChild(ghostContainer);
            });
        }

        // Close on outside click
        window.addEventListener('click', (e) => {
            if (e.target.id === 'registration-modal') closeModal();
            if (e.target.id === 'permit-modal') closePermitModal();
        });

        document.getElementById('btn-add-vehicle')?.addEventListener('click', () => {
            openModal();
        });

        document.getElementById('btn-submit')?.addEventListener('click', () => {
            const fields = {
                vehicleName: document.getElementById('vehicle-name').value.trim(),
                plateNo: document.getElementById('plate-no').value.trim(),
                vehicleType: document.getElementById('vehicle-type').value
            };
            
            if (!fields.vehicleName || !fields.plateNo) {
                alert('Please fill in all vehicle details.');
                return;
            }



            // ── Auto-fill form date ──
            document.getElementById('form-date').valueAsDate = new Date();
            document.getElementById('parking-no').value = generateParkingId();

            // Pre-fill user data removed (Handled by PHP)

            // ── Dynamic Vehicles ──
            const vehiclesContainer = document.getElementById('vehicles-container');
            const btnAddVehicle = document.getElementById('btn-add-vehicle');
            let vehicleCount = 1;

            btnAddVehicle.addEventListener('click', () => {
                vehicleCount++;
                const block = document.createElement('div');
                block.className = 'vehicle-block';
                block.style.cssText = 'border: 1.5px solid var(--border); padding: 20px; border-radius: 12px; margin-bottom: 20px; position:relative; background: #fafafa;';
                block.innerHTML = `
                    <button type="button" class="btn-remove-vehicle" style="position:absolute; top:20px; right:20px; background:rgba(220,53,69,0.1); border:none; color:var(--danger); font-size:1.2rem; cursor:pointer; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" title="Remove Vehicle">&times;</button>
                    <div class="vehicle-header" style="font-weight:800; margin-bottom:16px; font-size:1rem; color:var(--primary-dark); display:flex; align-items:center; gap:8px;">
                        <svg style="width:20px;height:20px;fill:currentColor;" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg> 
                        <span>Vehicle #${vehicleCount}</span>
                    </div>
                    <table class="info-table" style="background:white;">
                        <tr>
                            <td class="field-label">Name of Vehicle</td>
                            <td class="field-value" colspan="3"><input type="text" class="vehicle-name" placeholder="e.g. Honda Civic" /></td>
                        </tr>
                        <tr>
                            <td class="field-label">Name of Owner</td>
                            <td class="field-value" colspan="3"><input type="text" class="vehicle-owner" placeholder="Enter vehicle owner's name" /></td>
                        </tr>
                        <tr>
                            <td class="field-label">Type of Vehicle</td>
                            <td class="field-value">
                                <select class="vehicle-type">
                                    <option value="">Select type...</option>
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV</option>
                                    <option value="Van">Van</option>
                                    <option value="Motorcycle">Motorcycle</option>
                                    <option value="Pickup Truck">Pickup Truck</option>
                                    <option value="Hatchback">Hatchback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </td>
                            <td class="field-label">Plate No.</td>
                            <td class="field-value"><input type="text" class="plate-no" placeholder="e.g. DEF 5678" style="text-transform:uppercase;font-weight:600;" /></td>
                        </tr>
                    </table>
                `;
                
                block.querySelector('.btn-remove-vehicle').addEventListener('click', () => {
                    block.remove();
                    updateVehicleNumbers();
                });
                
                vehiclesContainer.appendChild(block);
            });

            function updateVehicleNumbers() {
                const headers = vehiclesContainer.querySelectorAll('.vehicle-header span');
                headers.forEach((h, i) => {
                    h.textContent = 'Vehicle #' + (i + 1);
                });
                vehicleCount = headers.length;
            }

            // ══════════════════════════════════════════
            // FORM SUBMISSION
            // ══════════════════════════════════════════
            document.getElementById('btn-submit').addEventListener('click', () => {
                const vehicleBlocks = document.querySelectorAll('.vehicle-block');
                const vehicles = [];
                let hasError = false;

                vehicleBlocks.forEach((block, index) => {
                    const name = block.querySelector('.vehicle-name').value.trim();
                    const owner = block.querySelector('.vehicle-owner').value.trim();
                    const type = block.querySelector('.vehicle-type').value;
                    const plate = block.querySelector('.plate-no').value.trim().toUpperCase();

                    if (!name || !owner || !type || !plate) {
                        showToast('Please complete all fields for Vehicle #' + (index + 1), 'var(--danger)');
                        hasError = true;
                        return;
                    }

                    vehicles.push({ vehicleName: name, vehicleOwner: owner, vehicleType: type, plateNo: plate });
                });

                if (hasError) return;

                const dateStarted = document.getElementById('date-started').value;
                if (!dateStarted) {
                    showToast('Please select Date Started', 'var(--danger)');
                    return;
                }

                const fields = {
                    date: document.getElementById('form-date').value,
                    dateStarted: dateStarted,
                    vehicles: vehicles
                };

                const btn = document.getElementById('btn-submit');
                const originalText = btn.innerHTML;
                btn.innerHTML = 'Submitting...';
                btn.disabled = true;

                fetch('<?= url("/user/apartment/parking/submit") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(fields)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('success-modal').classList.add('active');
                    } else {
                        showToast(data.message || 'Submission failed.', 'var(--danger)');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Network error while submitting.', 'var(--danger)');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        });
    </script>
</body>

</html>