<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Burial Service Request</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --primary: #2f8a60;
            --primary-dark: #1e5a3e;
            --primary-light: #e6f4ed;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --danger: #dc2626;
            --accent: #c9a84c;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: #f3f4f6; 
            color: var(--text-main); 
        }

        .app-wrapper { display: flex; height: 100vh; overflow: hidden; }
        .main-content { flex: 1; height: 100vh; overflow-y: auto; background: #f9fafb; display: flex; flex-direction: column; }

        .breadcrumb-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 0 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        .breadcrumb-bar a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .breadcrumb-bar a:hover { text-decoration: underline; }
        .breadcrumb-bar .sep { color: #d1d5db; }
        .breadcrumb-bar .current { color: var(--text-main); font-weight: 500; }

        /* ─── Document Container ─── */
        .form-document {
            background: white;
            max-width: 1000px;
            margin: 0 auto 40px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* ─── Header ─── */
        .form-doc-header {
            background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
            padding: 48px 40px;
            border-bottom: 3px solid var(--primary);
        }
        .form-doc-header-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            margin-bottom: 24px;
        }
        .form-doc-header-logo {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .form-doc-header-text {
            text-align: center;
        }
        .form-doc-header-text .arabic-line {
            font-size: 1.1rem;
            color: var(--primary-dark);
            margin-bottom: 8px;
            font-weight: 700;
        }
        .form-doc-header-text .org-name-ar {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-doc-header-text .org-name-en {
            font-size: 1.5rem;
            font-family: 'Lora', serif;
            font-weight: 800;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            line-height: 1.2;
        }
        .form-doc-header-text .sec-reg {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 500;
        }
        .form-doc-title-bar {
            text-align: center;
            margin-top: 16px;
        }
        .form-doc-title {
            display: inline-block;
            font-family: 'Lora', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
            background: var(--primary-dark);
            padding: 10px 36px;
            border-radius: 8px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* ─── Form Body & Sections ─── */
        .form-doc-body {
            padding: 40px 48px;
        }
        
        .form-section {
            margin-bottom: 48px;
        }
        .form-section:last-child {
            margin-bottom: 0;
        }

        .doc-section-title {
            font-family: 'Lora', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .doc-section-title svg {
            width: 20px;
            height: 20px;
            fill: var(--accent);
        }

        /* ─── Grid System ─── */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px 20px;
        }

        .col-12 { grid-column: span 12; }
        .col-8  { grid-column: span 8; }
        .col-6  { grid-column: span 6; }
        .col-4  { grid-column: span 4; }
        .col-3  { grid-column: span 3; }
        .col-2  { grid-column: span 2; }

        /* ─── Form Controls ─── */
        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            width: 100%;
            line-height: 1.4;
        }
        .form-label .hint {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 400;
            display: inline-block;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            height: 44px;
            padding: 10px 14px;
            font-size: 0.9375rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(47, 138, 96, 0.15);
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 14px center;
            background-repeat: no-repeat;
            background-size: 16px 16px;
            padding-right: 36px;
        }
        
        textarea.form-control {
            height: auto;
            min-height: 100px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            padding: 6px 0;
            width: 100%;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
        }

        /* ─── Stepper Progress ─── */
        .form-stepper {
            display: flex;
            justify-content: space-between;
            padding: 24px 48px;
            background: #f8faf9;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }
        .step-item.active:not(:last-child)::after, 
        .step-item.completed:not(:last-child)::after {
            background: var(--primary);
        }
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            position: relative;
            z-index: 2;
            transition: all 0.3s;
        }
        .step-item.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(47, 138, 96, 0.15);
        }
        .step-item.completed .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .step-label {
            margin-top: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .step-item.active .step-label {
            color: var(--primary-dark);
        }

        /* ─── Step Content Visibility ─── */
        .step-content {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }
        .step-content.active {
            display: block;
        }

        /* ─── Navigation Row ─── */
        .form-submit-row {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            align-items: center;
            margin-top: 16px;
            padding: 24px 48px;
            border-top: 1px solid var(--border);
            background: #f8faf9;
        }
        .form-submit-row p {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-style: italic;
            margin: 0;
            margin-right: auto;
        }
        .btn-cancel {
            padding: 10px 28px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: white;
            color: #4b5563;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-cancel:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: rgba(220, 38, 38, 0.04);
        }
        .btn-submit {
            padding: 10px 32px;
            border-radius: 8px;
            border: none;
            background: var(--primary);
            color: white;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-submit svg, .btn-cancel svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            transition: transform 0.2s;
        }
        .btn-submit:hover svg {
            transform: translateX(4px);
        }
        .btn-cancel:hover svg {
            transform: translateX(-4px);
        }
        .btn-submit:active, .btn-cancel:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* ─── Footer ─── */
        .form-doc-footer {
            background: var(--primary-dark);
            color: rgba(255, 255, 255, 0.75);
            padding: 20px 48px;
            text-align: center;
            font-size: 0.8rem;
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
            gap: 24px;
            margin-top: 6px;
            flex-wrap: wrap;
        }
        .form-doc-footer .footer-contacts span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-doc-footer .footer-contacts svg {
            width: 14px;
            height: 14px;
            fill: var(--accent);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @media (max-width: 768px) {
            .form-grid {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            .form-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            .form-label {
                width: 100%;
            }
            .form-doc-body {
                padding: 24px;
            }
            .form-doc-header {
                padding: 24px;
            }
            .form-submit-row {
                flex-direction: column;
                padding: 24px;
                text-align: center;
            }
            .form-submit-row p {
                margin: 0 0 16px 0;
            }
            .btn-submit, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
            .form-doc-footer {
                padding: 24px;
            }
        }

        @media print {
            .main-content { padding: 0; }
            .form-document { box-shadow: none; border: none; }
            .form-submit-row, .breadcrumb-bar, .top-bar, .sidebar { display: none !important; }
        }
    </style>
</head>
<body>
<div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php 
        $active_page = 'burial_service'; 
        include BASE_PATH . '/app/views/user/sidebar.php'; 
    ?>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">
        <div class="top-bar">
            <div class="top-bar-left">
                <div class="top-bar-title">Burial Service Request</div>
                <div class="top-bar-subtitle">Official Death Certificate Documentation Form</div>
            </div>
            <div class="top-bar-actions">
                <div id="top-date" style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;"></div>
            </div>
        </div>
        
        <div class="page-body">
            <div class="breadcrumb-bar">
                <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                <span class="sep">›</span>
                <span class="current">Burial Service Request</span>
            </div>
            
            <!-- REFINED UI/UX ORANGE WARNING -->
            <div style="
                background: linear-gradient(to right, rgba(255, 152, 0, 0.1), rgba(255, 152, 0, 0.05)); 
                border-left: 5px solid #ff9800; 
                padding: 16px 20px; 
                margin-bottom: 28px; 
                border-radius: 8px; 
                display: flex; 
                align-items: center; 
                gap: 16px;
                box-shadow: 0 4px 15px rgba(255, 152, 0, 0.08);
                animation: slideInLeft 0.5s ease-out;
            ">
                <div style="
                    background: #ff9800; 
                    width: 36px; height: 36px; 
                    border-radius: 10px; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    flex-shrink: 0;
                    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
                ">
                    <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:white;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.75rem; font-weight: 800; color: #e65100; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Important Service Notice</div>
                    <p style="margin: 0; color: #5d4037; font-size: 0.92rem; font-weight: 500; line-height: 1.4;">
                        This burial service is <strong style="color: #bf360c; font-weight: 800;">Exclusive Only for Islam</strong>. Please ensure the deceased is a practicing Muslim before proceeding.
                    </p>
                </div>
            </div>

            <style>
                @keyframes slideInLeft {
                    from { opacity: 0; transform: translateX(-20px); }
                    to { opacity: 1; transform: translateX(0); }
                }
            </style>
            <div class="form-document">
                <div class="form-doc-header">
                    <div class="form-doc-header-top">
                        <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo" />
                        <div class="form-doc-header-text">
                            <div class="arabic-line">بسم الله الرحمن الرحيم</div>
                            <div class="org-name-ar">الجمعية الإسلامية للثقافة والرعاية الإجتماعية في غوام</div>
                            <div class="org-name-en">Islamic Studies, Call and Guidance </div>
                            <div class="sec-reg">SEC Reg. No. 123456789</div>
                        </div>
                    </div>
                    <div class="form-doc-title-bar">
                        <div class="form-doc-title">Burial Service Request Form</div>
                    </div>
                </div>

                <!-- ── FORM STEPPER ── -->
                <div class="form-stepper">
                    <div class="step-item active" id="step-1-indicator">
                        <div class="step-circle">1</div>
                        <div class="step-label">Basic info</div>
                    </div>
                    <div class="step-item" id="step-2-indicator">
                        <div class="step-circle">2</div>
                        <div class="step-label">Medical</div>
                    </div>
                    <div class="step-item" id="step-3-indicator">
                        <div class="step-circle">3</div>
                        <div class="step-label">Certs</div>
                    </div>
                    <div class="step-item" id="step-4-indicator">
                        <div class="step-circle">4</div>
                        <div class="step-label">Finalize</div>
                    </div>
                    <div class="step-item" id="step-5-indicator">
                        <div class="step-circle">5</div>
                        <div class="step-label">Affidavit</div>
                    </div>
                </div>

                <div class="form-doc-body">
                    
                    
                    <!-- ══ STEP 1: BASIC INFORMATION ══ -->
                    <div class="step-content active" id="step-1">
                        <!-- SECTION 1: REGISTRY INFO -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                                Registry Information
                            </div>
                            <div class="form-grid">
                                <div class="form-group col-4">
                                    <label class="form-label">Province</label>
                                    <input type="text" class="form-control" placeholder="Enter Province" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">City/Municipality</label>
                                    <input type="text" class="form-control" placeholder="Enter City/Municipality" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Registry No.</label>
                                    <input type="text" class="form-control" placeholder="—" readonly style="text-align: center;" />
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: DECEASED'S PERSONAL INFO -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                Deceased's Personal Information
                            </div>
                            <div class="form-grid">
                                <!-- Name -->
                                <div class="form-group col-4">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" placeholder="First Name" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" placeholder="Middle Name" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name" />
                                </div>

                                <!-- Dates & Metrics -->
                                <div class="form-group col-6">
                                    <label class="form-label">Date of Birth <span class="hint">(Day, Month, Year)</span></label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Date of Death <span class="hint">(Day, Month, Year)</span></label>
                                    <input type="date" class="form-control" />
                                </div>
                                
                                <!-- Detailed Age at Death -->
                                <div class="form-group col-12">
                                    <label class="form-label">5. Age at the Time of Death</label>
                                    <div class="form-grid">
                                        <div class="form-group col-3">
                                            <label class="form-label hint">Completed Years</label>
                                            <input type="number" class="form-control" placeholder="Years" />
                                        </div>
                                        <div class="form-group col-3">
                                            <label class="form-label hint">Months</label>
                                            <input type="number" class="form-control" placeholder="Months" />
                                        </div>
                                        <div class="form-group col-3">
                                            <label class="form-label hint">Days</label>
                                            <input type="number" class="form-control" placeholder="Days" />
                                        </div>
                                        <div class="form-group col-3">
                                            <label class="form-label hint">Hours/Mins</label>
                                            <input type="text" class="form-control" placeholder="Hours:Mins" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Demographics -->
                                <div class="form-group col-3">
                                    <label class="form-label">Sex</label>
                                    <select class="form-control">
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="form-group col-3">
                                    <label class="form-label">Civil Status</label>
                                    <select class="form-control">
                                        <option value="">Select</option>
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Widow/Widower</option>
                                        <option>Annulled</option>
                                        <option>Divorced</option>
                                    </select>
                                </div>
                                <div class="form-group col-3">
                                    <label class="form-label">Religion</label>
                                    <select class="form-control">
                                        <option value="Islam">Islam</option>
                                        <option value="Christian">Christian</option>
                                    </select>
                                </div>
                                <div class="form-group col-3">
                                    <label class="form-label">Citizenship</label>
                                    <input type="text" class="form-control" value="FILIPINO" />
                                </div>

                                <!-- Locations & Occupation -->
                                <div class="form-group col-12">
                                    <label class="form-label">Place of Death <span class="hint">(Hospital/House No., St., Brgy, City/Prov)</span></label>
                                    <input type="text" class="form-control" placeholder="Complete address of death occurrence" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Residence <span class="hint">(Complete home address)</span></label>
                                    <input type="text" class="form-control" placeholder="Complete home address" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" class="form-control" placeholder="Occupation" />
                                </div>
                                
                                <!-- Parents -->
                                <div class="form-group col-6">
                                    <label class="form-label">Father's Name <span class="hint">(First, Middle, Last)</span></label>
                                    <input type="text" class="form-control" placeholder="Father's full name" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Mother's Maiden Name <span class="hint">(First, Middle, Last)</span></label>
                                    <input type="text" class="form-control" placeholder="Mother's maiden name" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ STEP 2: MEDICAL CERTIFICATE ══ -->
                    <div class="step-content" id="step-2">
                        <!-- SECTION: MEDICAL CERTIFICATE -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-1.99.9-1.99 2L3 19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                                Medical Certificate
                            </div>
                            <div class="form-grid">
                                <!-- Causes of Death -->
                                <div class="form-group col-8">
                                    <label class="form-label">I. Immediate Cause</label>
                                    <input type="text" class="form-control" placeholder="Immediate cause of death" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Interval (Onset & Death)</label>
                                    <input type="text" class="form-control" placeholder="e.g. 2 days" />
                                </div>

                                <div class="form-group col-8">
                                    <label class="form-label">Antecedent Cause</label>
                                    <input type="text" class="form-control" placeholder="Antecedent cause" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Interval</label>
                                    <input type="text" class="form-control" />
                                </div>

                                <div class="form-group col-8">
                                    <label class="form-label">Underlying Cause</label>
                                    <input type="text" class="form-control" placeholder="Underlying cause" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Interval</label>
                                    <input type="text" class="form-control" />
                                </div>

                                <div class="form-group col-12">
                                    <label class="form-label">II. Other Significant Conditions</label>
                                    <input type="text" class="form-control" placeholder="Other contributing conditions" />
                                </div>

                                <!-- NEW: For Children Aged 0-7 Days -->
                                <div class="form-group col-12" style="background: #fdfaf0; padding: 20px; border-radius: 8px; border: 1px dashed var(--accent); margin-top: 12px;">
                                    <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
                                        <svg style="width: 18px; height: 18px; fill: var(--accent);" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                        FOR CHILDREN AGED 0 TO 7 DAYS
                                    </h4>
                                    <div class="form-grid">
                                        <div class="form-group col-4">
                                            <label class="form-label">14. Age of Mother</label>
                                            <input type="number" class="form-control" placeholder="Years" />
                                        </div>
                                        <div class="form-group col-4">
                                            <label class="form-label">15. Method of Delivery</label>
                                            <select class="form-control">
                                                <option>Normal spontaneous</option>
                                                <option>Vertex</option>
                                                <option>Others (Specify below)</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-4">
                                            <label class="form-label">16. Length of Pregnancy</label>
                                            <input type="text" class="form-control" placeholder="In completed weeks" />
                                        </div>
                                        <div class="form-group col-6">
                                            <label class="form-label">17. Type of Birth</label>
                                            <input type="text" class="form-control" placeholder="Single, Twin, Triplet, etc." />
                                        </div>
                                        <div class="form-group col-6">
                                            <label class="form-label">18. If Multiple Birth, Child Was</label>
                                            <input type="text" class="form-control" placeholder="First, Second, Third, etc." />
                                        </div>
                                    </div>
                                    
                                    <h4 style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin: 20px 0 10px 0;">19a. CAUSES OF DEATH (FOR INFANTS)</h4>
                                    <div class="form-grid" style="row-gap: 12px;">
                                        <div class="form-group col-12">
                                            <label class="form-label hint">a. Main disease/condition of infant</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="form-label hint">b. Other diseases/conditions of infant</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="form-label hint">c. Main maternal disease/condition affecting infant</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                        <div class="form-group col-12">
                                            <label class="form-label hint">d. Other maternal disease/condition affecting infant</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Maternal & Autopsy -->
                                <div class="form-group col-12" style="margin-top: 24px;">
                                    <label class="form-label">19c. Maternal Condition <span class="hint">(If deceased is female 15-49 years old)</span></label>
                                    <div class="checkbox-group">
                                        <label class="checkbox-item"><input type="radio" name="maternal"> a. Pregnant, not in labour</label>
                                        <label class="checkbox-item"><input type="radio" name="maternal"> b. Pregnant, in labour</label>
                                        <label class="checkbox-item"><input type="radio" name="maternal"> c. Less than 42 days after delivery</label>
                                        <label class="checkbox-item"><input type="radio" name="maternal"> d. 42 days to 1 year after delivery</label>
                                        <label class="checkbox-item"><input type="radio" name="maternal"> e. None of the choices</label>
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label class="form-label">19d. Manner of Death <span class="hint">(Homicide, Suicide, Accident, etc.)</span></label>
                                    <input type="text" class="form-control" placeholder="Manner of death" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Place of Occurrence <span class="hint">(e.g. home, farm, street)</span></label>
                                    <input type="text" class="form-control" placeholder="Occurrence location" />
                                </div>
                                <div class="form-group col-2">
                                    <label class="form-label">20. Autopsy</label>
                                    <select class="form-control">
                                        <option>NO</option>
                                        <option>YES</option>
                                    </select>
                                </div>

                                <!-- Attendant -->
                                <div class="form-group col-12">
                                    <label class="form-label">Attendant</label>
                                    <div class="checkbox-group">
                                        <label class="checkbox-item"><input type="checkbox"> Private Physician</label>
                                        <label class="checkbox-item"><input type="checkbox"> Public Health Officer</label>
                                        <label class="checkbox-item"><input type="checkbox"> Hospital Authority</label>
                                        <label class="checkbox-item"><input type="checkbox"> None</label>
                                        <label class="checkbox-item"><input type="checkbox"> Others</label>
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Duration From</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Duration To</label>
                                    <input type="date" class="form-control" />
                                </div>
                                
                                <!-- Certification of Death -->
                                <div class="form-group col-12" style="margin-top: 16px;">
                                    <label class="form-label">Certification of Death</label>
                                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0 0 16px 0;">I hereby certify that the particulars above are correct and that death occurred on the date specified.</p>
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Time of Death (AM/PM)</label>
                                    <input type="time" class="form-control" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Title / Position</label>
                                    <input type="text" class="form-control" placeholder="Official Title" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Name in Print</label>
                                    <input type="text" class="form-control" placeholder="Full Name" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" placeholder="Office / Hospital Address" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ STEP 3: OFFICIAL CERTIFICATIONS ══ -->
                    <div class="step-content" id="step-3">
                        <!-- POSTMORTEM CERTIFICATE OF DEATH -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.18-5.33l-3.68-3.68L11 6h2v4.17l3.24 3.24-1.06 1.26z"/></svg>
                                Postmortem Certificate of Death
                            </div>
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">
                                I HEREBY CERTIFY that I have performed an autopsy upon the body of the deceased and that the cause of death was as follows:
                            </p>
                            <div class="form-grid">
                                <div class="form-group col-12">
                                    <label class="form-label">Cause of Death (Postmortem findings)</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Signature</label>
                                    <input type="text" class="form-control" placeholder="Type signature" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Title/Designation</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Name in Print</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!-- CERTIFICATION OF EMBALMER -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                Certification of Embalmer
                            </div>
                            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">
                                I HEREBY CERTIFY that I have embalmed the deceased following all regulations prescribed by the Department of Health.
                            </p>
                            <div class="form-grid">
                                <div class="form-group col-6">
                                    <label class="form-label">Signature</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Title/Designation</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Name in Print</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">License No.</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Issued on</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">at (Place of issue)</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="form-group col-4">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ STEP 4: DISPOSAL & INFORMANTS ══ -->
                    <div class="step-content" id="step-4">
                        <!-- DISPOSAL INFO -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                Disposal Information
                            </div>
                            <div class="form-grid">
                                <div class="form-group col-12">
                                    <label class="form-label">23. Corpse Disposal</label>
                                    <div class="checkbox-group">
                                        <label class="checkbox-item"><input type="radio" name="disposal"> Burial</label>
                                        <label class="checkbox-item"><input type="radio" name="disposal"> Cremation</label>
                                        <label class="checkbox-item"><input type="radio" name="disposal"> Others (Specify)</label>
                                        <input type="text" class="form-control" style="width: 200px; height: 32px;" placeholder="Specify if others" />
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">24a. Burial/Cremation Permit No.</label>
                                    <input type="text" class="form-control" placeholder="Permit No." />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Date Issued</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">25. Name & Address of Cemetery or Crematory</label>
                                    <input type="text" class="form-control" value="ISCAG MUSLIM CEMETERY, DASMARIÑAS, CAVITE" />
                                </div>
                            </div>
                        </div>

                        <!-- CERTIFICATION & PREPARATION -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                Certification of Informant
                            </div>
                            <div class="form-grid">
                                <div class="form-group col-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Informant's name" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Relationship to Deceased</label>
                                    <input type="text" class="form-control" placeholder="Relationship" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" placeholder="Complete address" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Signature</label>
                                    <input type="text" class="form-control" placeholder="Type signature" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <!-- PREPARED BY -->
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                                Prepared By
                            </div>
                            <div class="form-grid">
                                <div class="form-group col-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Preparer's name" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Designation</label>
                                    <input type="text" class="form-control" placeholder="Designation / Title" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Signature</label>
                                    <input type="text" class="form-control" placeholder="Type signature" />
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ══ STEP 5: DELAYED REGISTRATION ══ -->
                    <div class="step-content" id="step-5">
                        <div class="form-section">
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                Affidavit for Delayed Registration of Death
                            </div>
                            <div style="background: #f9fafb; padding: 32px; border-radius: 12px; border: 1px solid var(--border); font-family: 'Lora', serif; line-height: 2;">
                                <p style="text-indent: 40px; margin-bottom: 20px;">
                                    I, <input type="text" style="width: 250px; border: none; border-bottom: 1px solid #999; background: transparent; padding: 0 8px;" placeholder="Full Name">, of legal age, <input type="text" style="width: 150px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Civil Status">, with residence and postal address <input type="text" style="width: 350px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Complete Address">, after being duly sworn in accordance with law, do hereby depose and say:
                                </p>
                                <ol style="padding-left: 20px;">
                                    <li style="margin-bottom: 16px;">
                                        That <input type="text" style="width: 250px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Name of Deceased"> died on <input type="date" style="border: none; border-bottom: 1px solid #999; background: transparent;"> in <input type="text" style="width: 200px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Place of Death"> and was buried/cremated in <input type="text" style="width: 200px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Place of Burial"> on <input type="date" style="border: none; border-bottom: 1px solid #999; background: transparent;">.
                                    </li>
                                    <li style="margin-bottom: 16px;">
                                        That the deceased at the time of his/her death: 
                                        <div style="margin-left: 20px;">
                                            <label><input type="radio" name="attended_status"> was attended by <input type="text" style="width: 200px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Name of Physician"></label><br>
                                            <label><input type="radio" name="attended_status"> was not attended.</label>
                                        </div>
                                    </li>
                                    <li style="margin-bottom: 16px;">
                                        That the cause of death of the deceased was <input type="text" style="width: 400px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Cause of death">.
                                    </li>
                                    <li style="margin-bottom: 16px;">
                                        That the reason for the delay in registering this death was due to <input type="text" style="width: 400px; border: none; border-bottom: 1px solid #999; background: transparent;" placeholder="Reason for delay">.
                                    </li>
                                    <li style="margin-bottom: 16px;">
                                        That I am executing this affidavit to attest to the truthfulness of the foregoing statements for all legal intents and purposes.
                                    </li>
                                </ol>
                                <p style="text-align: right; margin-top: 40px;">
                                    <input type="text" style="width: 250px; border: none; border-bottom: 1px solid #000; text-align: center;" placeholder="Signature Over Printed Name"><br>
                                    (Signature Over Printed Name of Affiant)
                                </p>
                                
                                <div style="margin-top: 48px; padding-top: 24px; border-top: 2px solid #eee;">
                                    <p>SUBSCRIBED AND SWORN to before me this <input type="text" style="width: 40px; border: none; border-bottom: 1px solid #999; text-align: center;"> day of <input type="text" style="width: 100px; border: none; border-bottom: 1px solid #999; text-align: center;">, <input type="text" style="width: 60px; border: none; border-bottom: 1px solid #999; text-align: center;" value="2026"> at <input type="text" style="width: 200px; border: none; border-bottom: 1px solid #999;" placeholder="City/Municipality">, Philippines.</p>
                                    
                                    <div class="form-grid" style="margin-top: 32px;">
                                        <div class="form-group col-6">
                                            <label class="form-label">Signature of Administering Officer</label>
                                            <input type="text" class="form-control" placeholder="Signature" />
                                        </div>
                                        <div class="form-group col-6">
                                            <label class="form-label">Position / Title / Designation</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                        <div class="form-group col-6">
                                            <label class="form-label">Name in Print</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                        <div class="form-group col-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /.form-doc-body -->

                <!-- ── FORM NAVIGATION ── -->
                <div class="form-submit-row">
                    <p id="step-hint">Step 1 of 5: Personal Details</p>
                    <button type="button" id="btn-prev" class="btn-cancel" style="display: none;">
                        <svg viewBox="0 0 24 24" style="margin-right: 8px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                        Previous
                    </button>
                    <button type="button" id="btn-next" class="btn-submit">
                        Next Step
                        <svg viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
                    </button>
                    <button type="button" id="btn-submit" class="btn-submit" style="display: none;">
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Submit Request
                    </button>
                </div>

                <!-- ── FORM FOOTER ── -->
                <div class="form-doc-footer">
                    <div class="footer-address">Jose Abad Santos Street, Salitran I, City of Dasmariñas, Cavite, Philippines - 4114</div>
                    <div class="footer-contacts">
                        <span>
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            iscagphilippines@gmail.com
                        </span>
                        <span>
                            <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            (046) 4161589
                        </span>
                        <span>
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                            /iscagphilippines
                        </span>
                    </div>
                </div>

            </div><!-- /.form-document -->
        </div>
    </div>
</div>

<div id="toast" style="display:none; position:fixed; top:24px; right:24px; background:var(--primary); color:white; padding:16px 24px; border-radius:12px; z-index:10000; font-weight:700; box-shadow:0 10px 30px rgba(0,0,0,0.2);"></div>

<script>
    let currentStep = 1;
    const totalSteps = 5;

    function updateStepper() {
        // Update indicators
        for (let i = 1; i <= totalSteps; i++) {
            const indicator = document.getElementById(`step-${i}-indicator`);
            if (i < currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (i === currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        }

        // Update content visibility
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`step-${currentStep}`).classList.add('active');

        // Update buttons
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const btnSubmit = document.getElementById('btn-submit');
        const hint = document.getElementById('step-hint');

        btnPrev.style.display = currentStep === 1 ? 'none' : 'flex';
        
        if (currentStep === totalSteps) {
            btnNext.style.display = 'none';
            btnSubmit.style.display = 'flex';
        } else {
            btnNext.style.display = 'flex';
            btnSubmit.style.display = 'none';
        }

        // Update hint text
        const hints = [
            "Step 1: Personal Details",
            "Step 2: Medical Details",
            "Step 3: Official Certifications",
            "Step 4: Disposal & Informants",
            "Step 5: Affidavit for Delayed Registration"
        ];
        hint.textContent = hints[currentStep - 1];

        // Scroll to top of form
        document.querySelector('.main-content').scrollTop = 0;
    }

    document.getElementById('btn-next').addEventListener('click', () => {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepper();
        }
    });

    document.getElementById('btn-prev').addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateStepper();
        }
    });

    document.getElementById('btn-submit').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<svg class="animate-spin" viewBox="0 0 24 24" style="width:18px;height:18px;fill:none;stroke:white;stroke-width:2;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="white"></path></svg> Submitting...';
        
        setTimeout(() => {
            const toast = document.getElementById('toast');
            toast.textContent = 'Burial service request submitted successfully!';
            toast.style.display = 'block';
            setTimeout(() => window.location.href = '<?= url("/user/services/burial-dashboard") ?>', 2000);
        }, 1500);
    });

    // Set real-time date in top bar
    document.addEventListener('DOMContentLoaded', function() {
        const dateEl = document.getElementById('top-date');
        if (dateEl) {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('en-US', options);
        }
        updateStepper();
    });
</script>
</body>
</html>
