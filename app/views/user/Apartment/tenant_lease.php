<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Lease Contract</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        .lease-container { max-width: 900px; margin: 0 auto; }

        /* Hero Banner */
        .lease-hero { background: white; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 20px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 24px; animation: slideUp 0.4s ease; }
        .lease-hero-top { background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); padding: 28px 32px 24px; position: relative; overflow: hidden; }
        .lease-hero-top::before { content: ''; position: absolute; right: -20px; bottom: -20px; width: 140px; height: 140px; border-radius: 50%; background: rgba(201,168,76,0.1); }
        .lease-hero-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; position: relative; z-index: 1; }
        .lease-hero-left { display: flex; align-items: center; gap: 16px; }
        .lease-hero-icon { width: 56px; height: 56px; border-radius: 50%; background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.25); flex-shrink: 0; }
        .lease-hero-icon svg { width: 28px; height: 28px; fill: white; }
        .lease-hero-name { font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: white; margin: 0 0 2px; }
        .lease-hero-subtitle { font-size: 0.82rem; color: rgba(255,255,255,0.65); margin: 0; }

        .lease-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 22px; border-radius: 24px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; backdrop-filter: blur(8px); }
        .lease-badge.pending { background: rgba(199,154,43,0.2); color: #ffd666; border: 1px solid rgba(199,154,43,0.3); }
        .lease-badge.accepted { background: rgba(47,138,96,0.2); color: #7ee8b0; border: 1px solid rgba(47,138,96,0.3); }
        .lease-badge.rejected { background: rgba(139,46,46,0.2); color: #f59090; border: 1px solid rgba(139,46,46,0.3); }
        .lease-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: pulse 2s ease infinite; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

        .lease-summary { padding: 18px 32px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .summary-stat { text-align: center; padding: 14px 10px; background: var(--content-bg); border-radius: 10px; border: 1px solid var(--border); transition: all 0.2s; }
        .summary-stat:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .summary-stat-label { font-size: 0.66rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: 4px; }
        .summary-stat-value { font-family: 'Lora', serif; font-size: 1rem; font-weight: 700; color: var(--primary-dark); }

        /* Section Cards */
        .lease-card { background: white; border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 16px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 24px; animation: slideUp 0.4s ease backwards; }
        .lease-card:nth-child(2) { animation-delay: 0.05s; }
        .lease-card:nth-child(3) { animation-delay: 0.1s; }
        .lease-card:nth-child(4) { animation-delay: 0.15s; }

        .card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--border); background: linear-gradient(to right, rgba(26,58,92,0.03), transparent); }
        .card-header-left { display: flex; align-items: center; gap: 10px; }
        .card-header-icon { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card-header-icon svg { width: 17px; height: 17px; fill: white; }
        .card-header-title { font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: var(--primary-dark); margin: 0; }
        .card-body { padding: 24px; }

        /* Info Grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
        .info-field { padding: 14px 20px; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); transition: background 0.15s; }
        .info-field:hover { background: rgba(23,107,69,0.015); }
        .info-grid .info-field:nth-child(even) { border-right: none; }
        .info-field.full-width { grid-column: 1 / -1; border-right: none; }
        .info-field:last-child, .info-grid .info-field:nth-last-child(-n+2) { border-bottom: none; }
        .info-field-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 5px; }
        .info-field-label svg { width: 12px; height: 12px; fill: var(--accent); }
        .info-field-value { font-size: 0.9rem; font-weight: 600; color: var(--text-main); line-height: 1.4; }
        .info-field-value.empty { color: var(--text-muted); font-style: italic; font-weight: 400; }

        /* Terms List */
        .terms-list { list-style: none; padding: 0; margin: 0; }
        .terms-list li { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); font-size: 0.88rem; color: #475569; line-height: 1.6; }
        .terms-list li:last-child { border-bottom: none; }
        .terms-list li .term-num { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white; font-size: 0.72rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }

        /* Rules List */
        .rules-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        .rules-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; color: #475569; line-height: 1.5; padding: 10px 14px; background: #fefce8; border-radius: 8px; border: 1px solid #fef08a; }
        .rules-list li svg { width: 16px; height: 16px; fill: #ca8a04; flex-shrink: 0; margin-top: 2px; }

        /* Action Bar */
        .action-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; background: white; border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 16px rgba(0,0,0,0.06); padding: 20px 24px; margin-bottom: 24px; animation: slideUp 0.4s ease 0.2s backwards; }
        .action-bar-text h4 { font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 4px; }
        .action-bar-text p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }
        .action-bar-btns { display: flex; gap: 10px; flex-shrink: 0; }

        .btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 10px 22px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; text-decoration: none; transition: all 0.18s; cursor: pointer; border: none; font-family: inherit; }
        .btn-action.primary { background: linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color: white; box-shadow: 0 4px 12px rgba(23,107,69,0.25); }
        .btn-action.primary:hover { box-shadow: 0 6px 20px rgba(23,107,69,0.35); transform: translateY(-1px); }
        .btn-action.danger { background: linear-gradient(135deg, #8b2e2e, #b33a3a); color: white; box-shadow: 0 4px 12px rgba(139,46,46,0.25); }
        .btn-action.danger:hover { box-shadow: 0 6px 20px rgba(139,46,46,0.35); transform: translateY(-1px); }
        .btn-action.outline { background: white; color: var(--text-muted); border: 1.5px solid var(--border); }
        .btn-action.outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-action svg { width: 15px; height: 15px; fill: currentColor; }
        .btn-action:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

        /* Empty State */
        .empty-state { text-align: center; padding: 80px 32px; }
        .empty-state svg { width: 64px; height: 64px; fill: var(--border); margin-bottom: 16px; }
        .empty-state h3 { font-family: 'Lora', serif; font-size: 1.2rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 8px; }
        .empty-state p { font-size: 0.88rem; color: var(--text-muted); margin: 0 0 20px; line-height: 1.6; }

        /* Accepted stamp */
        .stamp-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); font-family: 'Lora', serif; font-size: 3rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.15em; opacity: 0.08; pointer-events: none; white-space: nowrap; }
        .stamp-overlay.accepted { color: #2f8a60; }
        .stamp-overlay.rejected { color: #8b2e2e; }

        /* Toast */
        .toast-msg { position: fixed; bottom: 32px; left: 50%; transform: translateX(-50%) translateY(100px); background: #1f2e2a; color: white; padding: 14px 28px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 99999; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .toast-msg.show { transform: translateX(-50%) translateY(0); }

        /* Confirm Modal */
        .confirm-overlay { position: fixed; inset: 0; z-index: 99999; background: rgba(15,30,22,0.55); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; padding: 24px; opacity: 0; transition: opacity 0.2s; }
        .confirm-overlay.show { display: flex; opacity: 1; }
        .confirm-box { background: white; border-radius: 16px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden; transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .confirm-overlay.show .confirm-box { transform: translateY(0); }

        @media (max-width: 640px) {
            .lease-summary { grid-template-columns: repeat(2, 1fr); }
            .info-grid { grid-template-columns: 1fr; }
            .info-field { border-right: none !important; }
            .action-bar { flex-direction: column; text-align: center; }
            .action-bar-btns { width: 100%; justify-content: center; }
        }

        /* ══════════════ FORMAL PRINTABLE CONTRACT STYLES ══════════════ */
        .printable-contract-sheet {
            display: none;
        }

        .contract-page {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: 'Times New Roman', Times, 'Nimbus Roman No9 L', serif;
            font-size: 11pt;
            line-height: 1.5;
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            padding: 44px 50px;
            box-sizing: border-box !important;
        }

        .contract-page-break {
            height: 20px;
        }

        .contract-doc-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .contract-doc-title {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 3px;
            color: #000000 !important;
        }

        .contract-doc-subtitle {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 10px;
            color: #000000 !important;
        }

        .contract-doc-p {
            text-align: justify;
            text-justify: inter-word;
            margin-bottom: 10px;
            color: #000000 !important;
            font-size: 11pt;
            line-height: 1.5;
        }

        .contract-doc-clause {
            margin-bottom: 9px;
            text-align: justify;
            text-justify: inter-word;
            color: #000000 !important;
            font-size: 11pt;
            line-height: 1.5;
        }

        .contract-doc-clause p {
            margin: 0;
            color: #000000 !important;
        }

        .contract-doc-field {
            font-weight: bold;
            text-decoration: underline;
            color: #000000 !important;
        }

        .contract-doc-signatures {
            margin-top: 18px;
            page-break-inside: avoid;
            color: #000000 !important;
        }

        .contract-sig-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 10px;
            background: transparent !important;
            table-layout: fixed;
        }

        .contract-sig-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            background: transparent !important;
            color: #000000 !important;
        }

        .contract-sig-role {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 28px;
        }

        .contract-sig-line {
            border-top: 1px solid #000;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10.5pt;
        }

        .contract-sig-desc {
            font-size: 9pt;
            color: #222;
        }

        .contract-sig-date {
            font-size: 9pt;
            margin-top: 2px;
        }


        /* ─── Contract Preview Modal ─── */
        .contract-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 30, 22, 0.75);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .contract-modal-overlay.show {
            display: flex;
        }
        .contract-modal-box {
            background: #f1f5f9;
            border-radius: 14px;
            width: 100%;
            max-width: 920px;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,0.4);
        }
        .contract-modal-topbar {
            background: white;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .contract-modal-scroll {
            padding: 24px 20px 50px;
            overflow-y: auto;
            flex: 1;
            background: #cbd5e1; /* Clean neutral desk surface */
            box-sizing: border-box;
        }
        .contract-modal-scroll .printable-contract-sheet {
            display: block !important;
            background: transparent !important;
        }
        .contract-modal-scroll .contract-page {
            box-shadow: 0 8px 30px rgba(0,0,0,0.18) !important;
            border-radius: 4px;
            margin: 0 auto 28px auto !important;
        }
        .contract-modal-scroll .contract-page * {
            color: #000000 !important;
        }
        .contract-modal-scroll .contract-page-break {
            height: 0;
            margin: 0;
            padding: 0;
        }

        @media print {
            @page {
                size: letter portrait;
                margin: 0 !important;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 100% !important;
            }
            .app-wrapper,
            .top-bar,
            .sidebar,
            .contract-modal-overlay,
            .confirm-overlay,
            .toast-msg {
                display: none !important;
            }
            #printableContractSheet {
                display: block !important;
                position: static !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                box-shadow: none !important;
            }
            .contract-page {
                display: block !important;
                box-sizing: border-box !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 18mm 20mm 16mm 20mm !important;
                box-shadow: none !important;
                border: none !important;
                background: #ffffff !important;
                color: #000000 !important;
                font-family: 'Times New Roman', Times, serif !important;
                font-size: 10.5pt !important;
                line-height: 1.45 !important;
            }
            .contract-page-1 {
                page-break-after: always !important;
                break-after: page !important;
            }
            .contract-page-2 {
                page-break-before: always !important;
                break-before: page !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            .contract-page-break {
                display: none !important;
            }
            .contract-doc-header {
                text-align: center !important;
                margin-bottom: 12px !important;
            }
            .contract-doc-title {
                font-size: 14pt !important;
                margin-bottom: 3px !important;
                color: #000000 !important;
            }
            .contract-doc-subtitle {
                font-size: 10.5pt !important;
                margin-bottom: 10px !important;
                color: #000000 !important;
            }
            .contract-doc-p {
                margin-bottom: 8px !important;
                font-size: 10.5pt !important;
                line-height: 1.45 !important;
                text-align: justify !important;
            }
            .contract-doc-clause {
                margin-bottom: 8px !important;
                font-size: 10.5pt !important;
                line-height: 1.45 !important;
                text-align: justify !important;
            }
            .contract-doc-signatures {
                margin-top: 16px !important;
                page-break-inside: avoid !important;
            }
            .contract-doc-signatures p {
                margin-top: 8px !important;
                margin-bottom: 12px !important;
                font-size: 10.5pt !important;
                line-height: 1.45 !important;
            }
            .contract-sig-table {
                margin-top: 8px !important;
            }
            .contract-sig-role {
                margin-bottom: 26px !important;
                font-size: 10pt !important;
            }
            .contract-sig-line {
                font-size: 10pt !important;
                padding-top: 3px !important;
            }
            .contract-sig-desc, .contract-sig-date {
                font-size: 8.5pt !important;
                margin-top: 2px !important;
            }
            .contract-doc-footer {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php $active_page = 'apartment_lease'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Lease Contract</div>
                    <div class="top-bar-subtitle">Review and manage your apartment lease agreement</div>
                </div>
                <div class="top-bar-actions" style="display:flex; gap:10px;">
                    <?php if (!empty($lease)): ?>
                    <button type="button" class="btn-topbar primary" onclick="openContractModal()" style="display:inline-flex; align-items:center; gap:8px;">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        Preview Formal Agreement
                    </button>
                    <?php endif; ?>
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                <div class="lease-container">
<?php if (empty($lease)): ?>
                    <!-- No Lease Found -->
                    <div class="lease-card">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                            <h3>No Lease Contract Available</h3>
                            <p>A lease contract will be generated once your apartment application has been approved by the administrator.</p>
                            <a href="<?= url('/user/apartment/status') ?>" class="btn-action primary">Check Application Status</a>
                        </div>
                    </div>
<?php else:
    $statusClass = strtolower($lease['lease_status']);

    // Priority: Formal legal name from the filed apartment application (tenant_addinfo)
    $filedApplicantName = '';
    if (!empty($tenantInfo['givenname']) || !empty($tenantInfo['familyname'])) {
        $nameParts = array_filter([
            trim($tenantInfo['givenname'] ?? ''),
            trim($tenantInfo['middlename'] ?? ''),
            trim($tenantInfo['familyname'] ?? '')
        ]);
        $filedApplicantName = implode(' ', $nameParts);
    }
    
    $tenantName = $filedApplicantName ?: trim(($lease['first_name'] ?? '') . ' ' . ($lease['last_name'] ?? ''));
    if (!$tenantName) $tenantName = $_SESSION['user_full_name'] ?? $_SESSION['name'] ?? 'Tenant';
    $tenantName = htmlspecialchars($tenantName);

    $unitType = htmlspecialchars($lease['unit_type'] ?? $lease['roomtype'] ?? 'N/A');
    $monthlyRent = number_format((float)($lease['monthly_rent'] ?? 0), 2);
    $deposit = number_format((float)($lease['deposit_amount'] ?? 0), 2);
    $advance = number_format((float)($lease['advance_amount'] ?? 0), 2);
    $startDate = $lease['start_date'] ? date('F j, Y', strtotime($lease['start_date'])) : 'Date of Move-In';
    $endDate = $lease['end_date'] ? date('F j, Y', strtotime($lease['end_date'])) : 'End of Lease Term';
    $createdAt = $lease['created_at'] ? date('F j, Y g:i A', strtotime($lease['created_at'])) : 'N/A';

    // Occupants count (Tenant + family members)
    $occupants = 1;
    if (!empty($lease['tenant_id'])) {
        try {
            $dbOcc = getDbConnection();
            $stmtOcc = $dbOcc->prepare("SELECT COUNT(*) FROM family_members WHERE tenant_id = :tid");
            $stmtOcc->execute(['tid' => $lease['tenant_id']]);
            $occupants = (int)$stmtOcc->fetchColumn() + 1;
        } catch (\Exception $e) {
            $occupants = 1;
        }
    }

    // Room / Unit display
    $building = $lease['building'] ?? '';
    $roomNumber = $lease['room_number'] ?? '';
    if (!empty($building) && !empty($roomNumber)) {
        $unitDisplay = htmlspecialchars($building . ' — Room ' . $roomNumber . ' (' . $unitType . ')');
    } elseif (!empty($roomNumber)) {
        $unitDisplay = htmlspecialchars('Room ' . $roomNumber . ' (' . $unitType . ')');
    } else {
        $unitDisplay = htmlspecialchars($unitType . ' (Unit Assignment Pending)');
    }

    $leaseMonths = ($lease['end_date'] && $lease['start_date']) 
        ? round((strtotime($lease['end_date']) - strtotime($lease['start_date'])) / (30*24*3600)) 
        : 12;
    if ($leaseMonths <= 0) $leaseMonths = 12;

    // Parse inclusions and rules from typeData
    $inclusions = [];
    $rules = [];
    if (!empty($typeData)) {
        if (!empty($typeData['inclusions'])) {
            $inclusions = is_string($typeData['inclusions']) ? json_decode($typeData['inclusions'], true) : $typeData['inclusions'];
        }
        if (!empty($typeData['rules'])) {
            $rules = is_string($typeData['rules']) ? json_decode($typeData['rules'], true) : $typeData['rules'];
        }
    }
?>
                    <!-- Hero Banner -->
                    <div class="lease-hero" style="position:relative;">
                        <?php if ($lease['lease_status'] !== 'Pending'): ?>
                        <div class="stamp-overlay <?= $statusClass ?>"><?= $lease['lease_status'] ?></div>
                        <?php endif; ?>

                        <div class="lease-hero-top">
                            <div class="lease-hero-header">
                                <div class="lease-hero-left">
                                    <div class="lease-hero-icon">
                                        <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="lease-hero-name">Lease Agreement — <?= $unitType ?></h2>
                                        <p class="lease-hero-subtitle">Contract #LSE-<?= str_pad($lease['lease_id'], 4, '0', STR_PAD_LEFT) ?> · <?= $tenantName ?></p>
                                    </div>
                                </div>
                                <div class="lease-badge <?= $statusClass ?>">
                                    <span class="lease-badge-dot"></span>
                                    <?= $lease['lease_status'] ?>
                                </div>
                            </div>
                        </div>

                        <div class="lease-summary">
                            <div class="summary-stat">
                                <div class="summary-stat-label">Monthly Rent</div>
                                <div class="summary-stat-value">₱<?= $monthlyRent ?></div>
                            </div>
                            <div class="summary-stat">
                                <div class="summary-stat-label">Security Deposit</div>
                                <div class="summary-stat-value">₱<?= $deposit ?></div>
                            </div>
                            <div class="summary-stat">
                                <div class="summary-stat-label">Advance Rent</div>
                                <div class="summary-stat-value">₱<?= $advance ?></div>
                            </div>
                            <div class="summary-stat">
                                <div class="summary-stat-label">Lease Term</div>
                                <div class="summary-stat-value" id="summary-term-display"><?= ($lease['lease_status'] === 'Pending') ? '12' : (
                                    $lease['end_date'] && $lease['start_date'] 
                                        ? round((strtotime($lease['end_date']) - strtotime($lease['start_date'])) / (30*24*3600)) 
                                        : '12'
                                ) ?> Months</div>
                            </div>
                        </div>
                    </div>

                    <!-- Lease Details -->
                    <div class="lease-card">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon"><svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg></div>
                                <h3 class="card-header-title">Lease Details</h3>
                            </div>
                        </div>
                        <div class="info-grid">
                            <div class="info-field"><div class="info-field-label">Tenant Name</div><div class="info-field-value"><?= $tenantName ?></div></div>
                            <div class="info-field"><div class="info-field-label">Email</div><div class="info-field-value"><?= htmlspecialchars($lease['email'] ?? 'N/A') ?></div></div>
                            <div class="info-field"><div class="info-field-label">Unit Type / Preferred Room</div><div class="info-field-value"><?= $unitType ?></div></div>
                            <div class="info-field"><div class="info-field-label">Contact Number</div><div class="info-field-value"><?= htmlspecialchars($lease['contactnum'] ?? 'N/A') ?></div></div>
                            <div class="info-field"><div class="info-field-label">Lease Start Date</div><div class="info-field-value"><?= $startDate ?></div></div>
                            <div class="info-field"><div class="info-field-label">Lease End Date</div><div class="info-field-value"><?= $endDate ?></div></div>
                            <div class="info-field"><div class="info-field-label">Contract Generated</div><div class="info-field-value"><?= $createdAt ?></div></div>
                            <div class="info-field"><div class="info-field-label">Lease Status</div><div class="info-field-value" style="color: <?= $statusClass === 'accepted' ? '#2f8a60' : ($statusClass === 'rejected' ? '#8b2e2e' : 'var(--accent)') ?>; font-weight: 800;"><?= $lease['lease_status'] ?></div></div>
                        </div>
                    </div>

                    <!-- Agreement Terms -->
                    <div class="lease-card">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon"><svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v6h-2V7zm0 8h2v2h-2v-2z"/></svg></div>
                                <h3 class="card-header-title">Agreement Terms & Conditions</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <ol class="terms-list">
                                <li><span class="term-num">1</span><span>This Lease Agreement (<em>Kasunduan sa Pagpapaupa</em>) is entered into between <strong>ISCAG – Philippines</strong> (Lessor/NAGPAPAUPA), located at #31 Jose Abad Santos St., Salitran I, Dasmariñas City, Cavite, Philippines 4114, and the Lessee/UMUUPA identified herein.</span></li>

                                <li><span class="term-num">2</span><span>The Lessee agrees to pay a monthly rental of <strong>₱<?= $monthlyRent ?></strong> for the unit type <strong><?= $unitType ?></strong>. Rent shall be due every <strong>5th of each month</strong>. Failure to pay the full rent amount within two (2) consecutive months shall result in a <strong>"Notice to Vacate"</strong> with a one (1) month grace period to relocate.</span></li>

                                <li><span class="term-num">3</span><span>Upon signing, the Lessee shall pay the following initial amounts:<br>
                                    • <strong>Security Deposit: ₱<?= $deposit ?></strong> — refundable at the end of the lease, less any unpaid dues, utilities, lost items, or damages to the unit caused by negligence.<br>
                                    • <strong>Advance Rent: ₱<?= $advance ?></strong> — equivalent to one (1) month's rent, payable upon move-in.</span></li>

                                <li><span class="term-num">4</span><span>The lease term is <strong id="term-display"><?= ($lease['lease_status'] === 'Pending') ? '12' : (
                                    $lease['end_date'] && $lease['start_date'] 
                                        ? round((strtotime($lease['end_date']) - strtotime($lease['start_date'])) / (30*24*3600)) 
                                        : '12'
                                ) ?> months</strong>, commencing on <strong><?= $startDate ?></strong> and ending on <strong id="end-date-display"><?= $endDate ?></strong>. The contract is renewable upon mutual agreement of both parties — the Lessor retains the right to extend, terminate, or re-lease the unit per the agreed terms.</span></li>

                                <li><span class="term-num">5</span><span>Occupancy of the unit shall not exceed <strong>two (2) persons</strong> as declared in this agreement. Any excess occupants beyond the registered count must be reported and approved by the Lessor. Unauthorized additional occupants may result in termination of the lease.</span></li>

                                <li><span class="term-num">6</span><span>The unit shall be used <strong>strictly for residential purposes only</strong>. The Lessee shall not sub-lease, assign, or transfer any rights to the unit without prior written consent from ISCAG Management.</span></li>

                                <li><span class="term-num">7</span><span>The Lessee shall maintain the unit in <strong>good and habitable condition</strong>. Any damage beyond normal wear and tear shall be charged to the Lessee and deducted from the Security Deposit upon lease termination.</span></li>

                                <li><span class="term-num">8</span><span>Either party may terminate this lease with a <strong>thirty (30) day written notice</strong>. Early termination by the Lessee prior to the expiration of the agreed term shall result in <strong>forfeiture of the Security Deposit</strong>.</span></li>

                                <li><span class="term-num">9</span><span><strong>Incitement of other tenants to withhold rent payment is strictly prohibited.</strong> Any tenant proven to have encouraged or persuaded others not to pay their monthly dues shall be subject to immediate termination of lease and eviction from the premises.</span></li>

                                <li><span class="term-num">10</span><span>The Lessee agrees to abide by all <strong>ISCAG apartment house rules and community guidelines</strong> at all times. Violation of these rules may result in penalties or termination of the lease at the discretion of ISCAG Management.</span></li>
                            </ol>
                        </div>
                    </div>

<?php if (!empty($rules)): ?>
                    <!-- House Rules -->
                    <div class="lease-card">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon" style="background: linear-gradient(135deg, #ca8a04, #eab308);"><svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg></div>
                                <h3 class="card-header-title">House Rules & Policies</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="rules-list">
                                <?php foreach ($rules as $rule): ?>
                                <li><svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg><span><?= htmlspecialchars($rule) ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
<?php endif; ?>

<?php if (!empty($inclusions)): ?>
                    <!-- Room Inclusions -->
                    <div class="lease-card">
                        <div class="card-header">
                            <div class="card-header-left">
                                <div class="card-header-icon"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
                                <h3 class="card-header-title">Room Inclusions</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                <?php foreach ($inclusions as $inc): ?>
                                <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; background:#f0fdf4; border-radius:8px; font-size:0.85rem; color:#166534;">
                                    <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:#22c55e; flex-shrink:0;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    <?= htmlspecialchars($inc) ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
<?php endif; ?>

<?php if ($lease['lease_status'] === 'Pending'): ?>
                    <!-- Action Bar -->
                    <div class="action-bar">
                        <div class="action-bar-text">
                            <h4>Ready to Accept Your Lease?</h4>
                            <p>By accepting, you agree to all terms and conditions outlined above. This action cannot be undone.</p>
                        </div>
                        <div class="action-bar-btns" style="align-items: center;">
                            <select id="lease-term-select" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: #f8fafc; font-weight: 600; color: var(--primary-dark); cursor: pointer;">
                                <option value="12">12 Months (Standard)</option>
                                <option value="9">9 Months</option>
                                <option value="6">6 Months</option>
                                <option value="3">3 Months</option>
                            </select>
                            <button class="btn-action danger" id="btn-reject-lease" type="button">
                                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                Reject
                            </button>
                            <button class="btn-action primary" id="btn-accept-lease" type="button">
                                <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Accept Lease
                            </button>
                        </div>
                    </div>
<?php elseif ($lease['lease_status'] === 'Accepted'): ?>
                    <div class="action-bar" style="border-color: rgba(47,138,96,0.3); background: rgba(47,138,96,0.03);">
                        <div class="action-bar-text">
                            <h4 style="color:#2f8a60;">✓ Lease Accepted</h4>
                            <p>You have accepted this lease agreement. Welcome to ISCAG Apartment!</p>
                        </div>
                        <div class="action-bar-btns">
                            <button type="button" class="btn-action outline" onclick="printLeaseAgreement()">
                                <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                                Print Contract
                            </button>
                            <a href="<?= url('/user/apartment/info') ?>" class="btn-action primary">View Apartment Info</a>
                        </div>
                    </div>
<?php elseif ($lease['lease_status'] === 'Rejected'): ?>
                    <div class="action-bar" style="border-color: rgba(139,46,46,0.3); background: rgba(139,46,46,0.03);">
                        <div class="action-bar-text">
                            <h4 style="color:#8b2e2e;">✗ Lease Rejected</h4>
                            <p>You have rejected this lease agreement. Please contact the admin if you wish to reconsider.</p>
                        </div>
                        <div class="action-bar-btns">
                            <a href="<?= url('/user/dashboard') ?>" class="btn-action outline">Back to Dashboard</a>
                        </div>
                    </div>
<?php elseif ($lease['lease_status'] === 'Active' || $lease['lease_status'] === 'Renewed'): ?>
                    <?php if (!empty($pendingRenewal)): ?>
                        <div class="action-bar" style="border-color: rgba(199,154,43,0.3); background: rgba(199,154,43,0.03);">
                            <div class="action-bar-text">
                                <h4 style="color:#ca8a04;">⏳ Renewal Request Pending</h4>
                                <p>Your request to renew and extend the lease contract for another 12 months is currently under review by the administrator.</p>
                            </div>
                            <div class="action-bar-btns">
                                <button class="btn-action outline" disabled>Pending Approval</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="action-bar" style="border-color: rgba(47,138,96,0.3); background: rgba(47,138,96,0.03);">
                            <div class="action-bar-text">
                                <h4 style="color:#2f8a60;">✓ Lease Active</h4>
                                <p>Your lease contract is fully active. You can print your official agreement or request a contract extension.</p>
                            </div>
                            <div class="action-bar-btns" style="align-items: center;">
                                <button type="button" class="btn-action outline" onclick="printLeaseAgreement()" style="margin-right: 10px;">
                                    <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                                    Print Contract
                                </button>
                                <select id="renew-term-select" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border); background: #f8fafc; font-weight: 600; color: var(--primary-dark); cursor: pointer; margin-right: 10px;">
                                    <option value="12">12 Months</option>
                                    <option value="9">9 Months</option>
                                    <option value="6">6 Months</option>
                                    <option value="3">3 Months</option>
                                </select>
                                <button class="btn-action primary" id="btn-renew-contract" type="button" data-lease-id="<?= $lease['lease_id'] ?>">
                                    <svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                                    Request Renewal
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
<?php endif; ?>

<?php endif; ?>
                </div><!-- /.lease-container -->
            </div><!-- /.page-body -->
        </div><!-- /.main-content -->
    </div><!-- /.app-wrapper -->

    <?php if (!empty($lease)): ?>
    <!-- ══════════════ FORMAL PRINTABLE CONTRACT DOCUMENT ══════════════ -->
    <div id="printableContractSheet" class="printable-contract-sheet">
        <!-- ─── PAGE 1 ─── -->
        <div class="contract-page contract-page-1">
            <div class="contract-doc-header">
                <h1 class="contract-doc-title">RESIDENTIAL LEASE AGREEMENT</h1>
                <div class="contract-doc-subtitle">[Darul Iman Apartment Complex]</div>
            </div>

            <p class="contract-doc-p">
                This Residential Rental Agreement (&ldquo;Agreement&rdquo;) is entered into by and between 
                <strong class="contract-doc-field"><?= $tenantName ?></strong> (&ldquo;Tenant&rdquo;), and 
                <strong class="contract-doc-field">ISLAMIC STUDIES, CALL AND GUIDANCE OF THE PHILIPPINES (ISCAG)</strong> (&ldquo;Landlord&rdquo;). 
                Landlord and Tenant are collectively referred to in this Agreement as the &ldquo;Parties&rdquo;. 
                This Agreement shall be effective as of the date executed by Landlord, as set forth below.
            </p>

            <p class="contract-doc-p">
                For the covenants contained herein, and other good and valuable consideration, the receipt and sufficiency of which is hereby acknowledged, the Parties agree as follows:
            </p>

            <div class="contract-doc-clause">
                <p><strong>1. PREMISES:</strong> The leased premises shall be comprised of that certain personal apartment residence located at <strong>#31 Jose Abad Santos St., Salitran I, Dasmariñas City, Cavite, Philippines 4114</strong>, specifically identified as <strong class="contract-doc-field"><?= $unitDisplay ?></strong> (&ldquo;Premises&rdquo;). Landlord leases the Premises to Tenant and Tenant leases the Premises from Landlord on the terms and conditions set forth herein.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>2. TERM:</strong> The term of this Agreement shall be a period of <strong class="contract-doc-field"><?= $leaseMonths ?> month(s)</strong>, beginning on <strong class="contract-doc-field"><?= $startDate ?></strong>, and ending on <strong class="contract-doc-field"><?= $endDate ?></strong>. Any holding over after the expiration or earlier termination of the term without Landlord&rsquo;s prior written consent shall be a default of this Agreement and shall not be construed to be a tenancy from month to month, unless Tenant pays and Landlord accepts payment of rent for the next full calendar month (plus, if the term ends on a day other than the last day of the month, rent for the remainder of the month during which the term ends). If such payment of rent is so paid and accepted, this Agreement will automatically renew on a month to month basis, and will continue as such until terminated by either party in accordance with the following: written notice of termination is to be given by either party at least thirty (30) days before the designated date of termination, and the designated date of termination shall be the last day of a calendar month. If notice of termination is given, this Agreement shall terminate on the date for which notice is properly given. Except as otherwise set forth in this Agreement, all of the terms and conditions of this Agreement shall apply during any month to month tenancy.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>3. MONTHLY RENT:</strong> The rent to be paid by Tenant to Landlord throughout the term of this Agreement is <strong class="contract-doc-field">₱<?= $monthlyRent ?></strong> per month and shall be due on the <strong>5th day of each calendar month</strong>. Tenant shall pay any delinquent rents promptly. Failure to pay the full rent amount within two (2) consecutive months shall result in a &ldquo;Notice to Vacate&rdquo; with a one (1) month grace period to relocate. All delinquent rents or other expenses due from Tenant shall accrue interest or penalties as authorized under ISCAG policies until paid. Rent for the first month shall be paid to Landlord at the time this Agreement is executed. Tenant shall not deduct or offset against rent unless expressly permitted by applicable law.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>4. SECURITY DEPOSIT & ADVANCE RENT:</strong> Upon execution of this Agreement, Tenant shall deposit with Landlord the sum of <strong class="contract-doc-field">₱<?= $deposit ?></strong> as a Security Deposit (refundable upon lawful termination of lease, less any unpaid rent, utilities, lost fixtures, or damages to the unit caused by negligence beyond ordinary wear and tear) and the sum of <strong class="contract-doc-field">₱<?= $advance ?></strong> as Advance Rent equivalent to one (1) month rent.</p>
            </div>

        </div>

        <div class="contract-page-break"></div>

        <!-- ─── PAGE 2 ─── -->
        <div class="contract-page contract-page-2">
            <div class="contract-doc-clause">
                <p><strong>5. UTILITIES:</strong> To the extent permitted by applicable utility service providers, Tenant shall be responsible for and shall pay, prior to delinquency, for all utility consumption. Monthly water consumption is computed at ₱100.00 per person based on <strong class="contract-doc-field"><?= $occupants ?> declared occupant(s)</strong>. Tenant shall also pay the mandatory monthly community contribution of <strong>₱150.00</strong> for security and garbage collection services. Electricity shall be billed according to individual sub-meter readings.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>6. OCCUPANCY & USE:</strong> The Premises shall be occupied and used strictly for residential purposes only. Total occupancy shall not exceed <strong class="contract-doc-field"><?= max(2, $occupants) ?> person(s)</strong> as declared. Any excess occupants beyond the registered count must be reported and approved by Landlord. Tenant shall not sub-lease, assign, mortgage, or transfer any rights to the Premises without prior written consent from ISCAG Management.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>7. CONDITION & MAINTENANCE:</strong> Tenant acknowledges receipt of the Premises in good, clean, and tenantable condition. Tenant agrees to maintain the Premises in a sanitary, orderly condition and immediately notify Landlord in writing of any defective or hazardous conditions. Tenant shall be liable for damages resulting from negligence, misuse, or intentional acts.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>8. HOUSE RULES & COMMUNITY GUIDELINES:</strong> Tenant agrees to strictly observe and abide by all ISCAG apartment house rules, Islamic community values, and safety regulations at all times. Incitement of other tenants to withhold rent or breach community order is strictly prohibited and constitutes grounds for immediate termination of the lease and eviction from the premises.<?php if (!empty($inclusions)): ?><br>• <strong>Included Amenities:</strong> <?= htmlspecialchars(implode(', ', $inclusions)) ?>.<?php endif; ?></p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>9. TERMINATION:</strong> Either party may terminate this Agreement by providing thirty (30) days advance written notice. Early termination by Tenant prior to the expiration of the agreed lease term shall result in the forfeiture of the Security Deposit.</p>
            </div>

            <div class="contract-doc-clause">
                <p><strong>10. GOVERNING LAW:</strong> This Agreement shall be construed, interpreted, and governed by the laws of the Republic of the Philippines and the jurisdiction of the competent courts of Dasmariñas City, Cavite.</p>
            </div>

            <div class="contract-doc-signatures">
                <p style="margin-top: 20px; margin-bottom: 20px;"><strong>IN WITNESS WHEREOF</strong>, the Parties have executed this Residential Lease Agreement as of the date first above written.</p>
                
                <table class="contract-sig-table">
                    <tr>
                        <td style="width: 48%; padding-right: 12px;">
                            <div class="contract-sig-role">LANDLORD / LESSOR:</div>
                            <div class="contract-sig-line">ISCAG – PHILIPPINES</div>
                            <div class="contract-sig-desc">Authorized Management Representative</div>
                            <div class="contract-sig-date">Date: _________________________________</div>
                        </td>
                        <td style="width: 4%;"></td>
                        <td style="width: 48%; padding-left: 12px;">
                            <div class="contract-sig-role">TENANT / LESSEE:</div>
                            <div class="contract-sig-line"><?= strtoupper($tenantName) ?></div>
                            <div class="contract-sig-desc">Tenant / Lessee</div>
                            <div class="contract-sig-date">Date: _________________________________</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-top: 26px;">
                            <div class="contract-sig-role" style="margin-bottom: 30px;">SIGNED IN THE PRESENCE OF:</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 48%; padding-right: 12px;">
                            <div class="contract-sig-line" style="font-weight: normal; font-size: 10pt;">Witness Signature over Printed Name</div>
                        </td>
                        <td style="width: 4%;"></td>
                        <td style="width: 48%; padding-left: 12px;">
                            <div class="contract-sig-line" style="font-weight: normal; font-size: 10pt;">Witness Signature over Printed Name</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- ══════════════ MODAL ON-SCREEN PREVIEW ══════════════ -->
    <div class="contract-modal-overlay" id="contract-modal" onclick="if(event.target===this) closeContractModal()">
        <div class="contract-modal-box">
            <div class="contract-modal-topbar">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(23,107,69,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; fill: currentColor;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">Official Lease Agreement Preview</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Contract #LSE-<?= str_pad($lease['lease_id'], 4, '0', STR_PAD_LEFT) ?> · <?= $tenantName ?></div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" class="btn-action primary" onclick="printLeaseAgreement()" style="padding: 8px 18px; font-size: 0.82rem;">
                        <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                        Print Document
                    </button>
                    <button type="button" onclick="closeContractModal()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; padding: 0 8px; line-height: 1;">&times;</button>
                </div>
            </div>
            <div class="contract-modal-scroll" id="contract-modal-scroll">
                <!-- Injected contract cloned for display -->
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Confirmation Modal -->
    <div class="confirm-overlay" id="confirm-modal">
        <div class="confirm-box">
            <div style="height:4px;background:linear-gradient(90deg,var(--primary-dark),var(--accent));"></div>
            <div style="padding:32px 28px 24px;text-align:center;">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--accent);margin-bottom:12px;" id="confirm-icon"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z"/></svg>
                <h4 style="font-family:'Lora',serif;font-size:1.15rem;font-weight:700;color:#1f2e2a;margin:0 0 10px;" id="confirm-title">Confirm Action</h4>
                <p style="font-size:0.87rem;color:#6f7f78;line-height:1.6;margin:0;" id="confirm-msg">Are you sure?</p>
            </div>
            <div style="display:flex;gap:10px;padding:0 28px 24px;justify-content:center;">
                <button id="confirm-cancel" style="flex:1;padding:10px 0;border-radius:8px;border:1.5px solid #d9e3de;background:white;color:#6f7f78;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:inherit;">Cancel</button>
                <button id="confirm-ok" style="flex:1;padding:10px 0;border-radius:8px;border:none;background:linear-gradient(135deg,#0f5c3a,#2f8a60);color:white;font-size:0.85rem;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(15,92,58,0.3);">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-msg" id="toast-msg"></div>

    <script>
    // ── Toast ──
    function showToast(msg, bg) {
        const t = document.getElementById('toast-msg');
        t.textContent = msg;
        t.style.background = bg || '#1f2e2a';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    // ── Confirm Modal ──
    let confirmCallback = null;
    function showConfirm(title, msg, dangerMode, cb) {
        const modal = document.getElementById('confirm-modal');
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-msg').textContent = msg;
        const okBtn = document.getElementById('confirm-ok');
        if (dangerMode) {
            okBtn.style.background = 'linear-gradient(135deg, #8b2e2e, #b33a3a)';
            okBtn.style.boxShadow = '0 4px 12px rgba(139,46,46,0.3)';
        } else {
            okBtn.style.background = 'linear-gradient(135deg, #0f5c3a, #2f8a60)';
            okBtn.style.boxShadow = '0 4px 12px rgba(15,92,58,0.3)';
        }
        confirmCallback = cb;
        modal.classList.add('show');
    }

    document.getElementById('confirm-cancel').addEventListener('click', () => {
        document.getElementById('confirm-modal').classList.remove('show');
        confirmCallback = null;
    });
    document.getElementById('confirm-ok').addEventListener('click', () => {
        document.getElementById('confirm-modal').classList.remove('show');
        if (confirmCallback) confirmCallback();
        confirmCallback = null;
    });

    // ── Dynamic Term Selector Updates ──
    const leaseTermSelect = document.getElementById('lease-term-select');
    if (leaseTermSelect) {
        leaseTermSelect.addEventListener('change', function() {
            const months = parseInt(this.value);
            // Update the hero stat
            const summaryEl = document.getElementById('summary-term-display');
            if (summaryEl) summaryEl.textContent = months + ' Months';

            // Update Term #4 in the agreement
            const termEl = document.getElementById('term-display');
            if (termEl) termEl.textContent = months + ' months';

            // Calculate and update the end date
            const startDateStr = '<?= $lease['start_date'] ?? '' ?>';
            if (startDateStr) {
                const start = new Date(startDateStr);
                const end = new Date(start);
                end.setMonth(end.getMonth() + months);
                const endFormatted = end.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                const endDateEl = document.getElementById('end-date-display');
                if (endDateEl) endDateEl.textContent = endFormatted;
            }
        });
    }

    // ── Lease Actions ──
    function leaseAction(action) {
        const btns = document.querySelectorAll('.action-bar-btns .btn-action');
        btns.forEach(b => b.disabled = true);

        let term = 12;
        if (action === 'accept') {
            const selectBox = document.getElementById('lease-term-select');
            if (selectBox) term = parseInt(selectBox.value);
        }

        fetch('<?= url("/user/apartment/lease/accept") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, term: term })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                showToast(action === 'accept' ? 'Lease accepted successfully!' : 'Lease rejected.', action === 'accept' ? '#2f8a60' : '#8b2e2e');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                showToast('Error: ' + (res.message || 'Unknown error'), '#8b2e2e');
                btns.forEach(b => b.disabled = false);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Network error. Please try again.', '#8b2e2e');
            btns.forEach(b => b.disabled = false);
        });
    }

    const acceptBtn = document.getElementById('btn-accept-lease');
    const rejectBtn = document.getElementById('btn-reject-lease');

    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => {
            showConfirm(
                'Accept Lease Agreement',
                'By accepting, you agree to all terms and conditions. This cannot be undone. Proceed?',
                false,
                () => leaseAction('accept')
            );
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => {
            showConfirm(
                'Reject Lease Agreement',
                'Are you sure you want to reject this lease? You may need to contact admin to request a new one.',
                true,
                () => leaseAction('reject')
            );
        });
    }

    // ── Contract Modal & Print ──
    function printLeaseAgreement() {
        const originalTitle = document.title;
        document.title = ' ';
        window.print();
        setTimeout(() => {
            document.title = originalTitle;
        }, 1500);
    }

    function openContractModal() {
        const modal = document.getElementById('contract-modal');
        const scrollBox = document.getElementById('contract-modal-scroll');
        const sourceSheet = document.getElementById('printableContractSheet');
        if (!modal || !scrollBox || !sourceSheet) return;

        scrollBox.innerHTML = '';
        const clone = sourceSheet.cloneNode(true);
        clone.id = 'previewContractClone';
        scrollBox.appendChild(clone);
        modal.classList.add('show');
    }

    function closeContractModal() {
        const modal = document.getElementById('contract-modal');
        if (modal) modal.classList.remove('show');
    }

    // ── Contract Renewal ──
    const renewBtn = document.getElementById('btn-renew-contract');
    if (renewBtn) {
        renewBtn.addEventListener('click', () => {
            const selectBox = document.getElementById('renew-term-select');
            const term = selectBox ? parseInt(selectBox.value) : 12;
            showConfirm(
                'Request Contract Renewal',
                `This will send a request to the administrator to extend your lease contract for another ${term} months. Do you want to proceed?`,
                false,
                () => {
                    const leaseId = renewBtn.getAttribute('data-lease-id');
                    renewBtn.disabled = true;
                    fetch('<?= url("/user/apartment/lease/renew") ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ lease_id: leaseId, term: term })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            showToast('Renewal request sent successfully!', '#2f8a60');
                            setTimeout(() => window.location.reload(), 1200);
                        } else {
                            showToast('Failed to send request.', '#8b2e2e');
                            renewBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        showToast('Network error.', '#8b2e2e');
                        renewBtn.disabled = false;
                    });
                }
            );
        });
    }
    </script>
</body>
</html>
