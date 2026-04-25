<?php 
// ═══════════════════════════════════════════
//  LOAD SESSION & DATA
// ═══════════════════════════════════════════
$user_id = $_SESSION['user_id'] ?? null;
// $parkingApp should be passed from controller
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

<body>
    <!-- ═══ PERMIT DETAILS MODAL ═══ -->
    <div class="modal-overlay" id="permit-modal">
        <div class="modal-container" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Vehicle Permit Details</h3>
                <button class="btn-close-modal" onclick="closePermitModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="print-area">
                    <div class="permit-card-modal">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:1.5px solid #d1dbd8; padding-bottom:16px;">
                            <img src="<?= asset('assets/logo.jpg') ?>" style="height:50px; border-radius:6px;" />
                            <div style="text-align:right;">
                                <div id="detail-status" class="status-pill approved">Verified Access</div>
                                <div style="font-size:0.65rem; color:#999; margin-top:4px; font-weight:800;">PERMIT ID: <span id="detail-permit-id">#PKG-0000</span></div>
                            </div>
                        </div>

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

                        <div style="overflow-x: auto;">
                            <table class="concept-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">No.</th>
                                        <th>Vehicle Name</th>
                                        <th>Plate Number</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($parkingApps as $app): ?>
                                        <?php 
                                            $s = strtoupper($app['status'] ?? 'PENDING');
                                            $pillClass = ($s === 'APPROVED') ? 'approved' : (($s === 'REJECTED') ? 'rejected' : 'pending');
                                            $appJson = htmlspecialchars(json_encode($app), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr>
                                            <td style="font-weight: 700; color: #999;"><?= $no++ ?>.</td>
                                            <td style="font-weight: 700; color: var(--primary-dark);"><?= htmlspecialchars($app['vehiclename']) ?></td>
                                            <td><code style="font-family: monospace; font-weight: 700; background: #f4f6f5; padding: 4px 10px; border-radius: 6px; color: #333;"><?= htmlspecialchars($app['plateno']) ?></code></td>
                                            <td><?= htmlspecialchars($app['typeofvehicle']) ?></td>
                                            <td>
                                                <span class="status-pill <?= $pillClass ?>">
                                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                                                    <?= $s ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center; display: flex; justify-content: center; gap: 8px;">
                                                <button class="btn-view" onclick='viewDetails(<?= $appJson ?>)'>
                                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                                    View Details
                                                </button>
                                                <button class="btn-download" onclick='downloadPermit(<?= $appJson ?>)'>
                                                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                                    Download
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- ═══ EMPTY STATE HERO (Not Registered) ═══ -->
                    <div class="status-hero">
                        <div class="status-hero-top" style="background: linear-gradient(135deg, #164e58, #237a8a); text-align: center; padding: 60px 40px;">
                            <div class="hero-icon-box" style="margin: 0 auto 24px; width: 80px; height: 80px;">
                                <svg viewBox="0 0 24 24" style="width: 40px; height: 40px;"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
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
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
                            <div class="summary-item"><label>Vehicle Name</label><span style="font-size:1.2rem; font-weight:700; color:#333;">${app.vehiclename}</span></div>
                            <div class="summary-item"><label>Plate Number</label><span style="font-family:monospace; background:#fff; padding:4px 12px; border:1px solid #d1dbd8; border-radius:6px; letter-spacing:0.1em; font-weight:700; color:#333;">${app.plateno}</span></div>
                            <div class="summary-item"><label>Authorized Holder</label><span style="font-weight:700; color:#333;">${app.ownername || 'Registered Tenant'}</span></div>
                            <div class="summary-item"><label>Vehicle Type</label><span style="font-weight:700; color:#333;">${app.typeofvehicle}</span></div>
                        </div>
                        <div style="margin-top:32px; padding-top:16px; border-top:1px dashed #d1dbd8; font-size:0.7rem; color:#888; line-height:1.5;">
                            This permit authorizes the vehicle above to access the ISCAG Apartment parking facilities. Non-transferable and must be presented upon request.
                        </div>
                    </div>
                `;
                document.body.appendChild(ghostContainer);
                targetArea = ghostContainer;
            } else {
                targetArea = document.getElementById('print-area');
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

            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            fetch('<?= url("/user/apartment/parking/save") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(fields)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error submitting application.');
                    btn.disabled = false;
                    btn.textContent = 'Submit Registration';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error.');
                btn.disabled = false;
                btn.textContent = 'Submit Application';
            });
        });
    </script>
</body>

</html>