<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Tenant Information</title>
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        /* ═══════════════════════════════════════════
       INFO DASHBOARD LAYOUT
       ═══════════════════════════════════════════ */
        .info-container {
            max-width: 960px;
            margin: 0 auto;
        }

        /* ── Status Hero Banner ── */
        .status-hero {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
            animation: slideUp 0.4s ease;
        }

        .status-hero-top {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            padding: 28px 32px 24px;
            position: relative;
            overflow: hidden;
        }

        .status-hero-top::before {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(201, 168, 76, 0.1);
        }

        .status-hero-top::after {
            content: '';
            position: absolute;
            right: 100px;
            bottom: -30px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .status-hero-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: relative;
            z-index: 1;
        }

        .status-hero-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-hero-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.25);
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
        }

        .status-hero-name {
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin: 0 0 2px;
        }

        .status-hero-subtitle {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.65);
            margin: 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 18px;
            border-radius: 24px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            white-space: nowrap;
            backdrop-filter: blur(4px);
        }

        .status-badge.pending {
            background: rgba(199, 154, 43, 0.2);
            color: #ffd666;
            border: 1px solid rgba(199, 154, 43, 0.3);
        }

        .status-badge.approved {
            background: rgba(47, 138, 96, 0.2);
            color: #7ee8b0;
            border: 1px solid rgba(47, 138, 96, 0.3);
        }

        .status-badge.rejected {
            background: rgba(139, 46, 46, 0.2);
            color: #f59090;
            border: 1px solid rgba(139, 46, 46, 0.3);
        }

        .status-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            animation: statusPulse 2s ease infinite;
        }

        @keyframes statusPulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Status summary bar */
        .status-summary {
            padding: 18px 32px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .summary-stat {
            text-align: center;
            padding: 14px 10px;
            background: var(--content-bg);
            border-radius: 10px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .summary-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .summary-stat-label {
            font-size: 0.66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .summary-stat-value {
            font-family: 'Lora', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        /* ── Status Timeline ── */
        .timeline-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
            animation: slideUp 0.4s ease 0.05s backwards;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, rgba(26, 58, 92, 0.03), transparent);
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-header-icon svg {
            width: 17px;
            height: 17px;
            fill: white;
        }

        .card-header-title {
            font-family: 'Lora', serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        /* Timeline steps */
        .timeline {
            display: flex;
            align-items: flex-start;
            gap: 0;
            position: relative;
            padding: 0 16px;
        }

        .timeline-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: calc(50% + 20px);
            right: calc(-50% + 20px);
            height: 3px;
            background: var(--border);
            z-index: 1;
        }

        .timeline-step.completed:not(:last-child)::after {
            background: linear-gradient(90deg, #2f8a60, var(--accent));
        }

        .timeline-step.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--accent), var(--border));
        }

        .timeline-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid var(--border);
            background: white;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 700;
            position: relative;
            z-index: 3;
            transition: all 0.3s;
        }

        .timeline-dot svg {
            width: 16px;
            height: 16px;
            fill: white;
        }

        .timeline-step.completed .timeline-dot {
            background: linear-gradient(135deg, #2f8a60, #3da870);
            border-color: #2f8a60;
            color: white;
            box-shadow: 0 2px 8px rgba(47, 138, 96, 0.25);
        }

        .timeline-step.active .timeline-dot {
            background: linear-gradient(135deg, var(--accent), #d4a83a);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 2px 8px rgba(199, 154, 43, 0.25);
            animation: timelinePulse 2s ease infinite;
        }

        @keyframes timelinePulse {

            0%,
            100% {
                box-shadow: 0 2px 8px rgba(199, 154, 43, 0.25);
            }

            50% {
                box-shadow: 0 4px 16px rgba(199, 154, 43, 0.4);
            }
        }

        .timeline-label {
            margin-top: 10px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
        }

        .timeline-step.active .timeline-label {
            color: var(--accent);
        }

        .timeline-step.completed .timeline-label {
            color: #2f8a60;
        }

        .timeline-date {
            font-size: 0.65rem;
            color: var(--text-muted);
            margin-top: 2px;
            opacity: 0.7;
        }

        /* ── Info Section Cards ── */
        .info-section {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .info-section:nth-child(3) {
            animation: slideUp 0.4s ease 0.1s backwards;
        }

        .info-section:nth-child(4) {
            animation: slideUp 0.4s ease 0.15s backwards;
        }

        .info-section:nth-child(5) {
            animation: slideUp 0.4s ease 0.2s backwards;
        }

        .info-section:nth-child(6) {
            animation: slideUp 0.4s ease 0.25s backwards;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .info-grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .info-field {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
            transition: background 0.15s;
        }

        .info-field:hover {
            background: rgba(23, 107, 69, 0.015);
        }

        .info-grid .info-field:nth-child(even) {
            border-right: none;
        }

        .info-grid-3 .info-field:nth-child(3n) {
            border-right: none;
        }

        .info-field.full-width {
            grid-column: 1 / -1;
            border-right: none;
        }

        .info-field:last-child,
        .info-grid .info-field:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .info-field-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-field-label svg {
            width: 12px;
            height: 12px;
            fill: var(--accent);
        }

        .info-field-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.4;
        }

        .info-field-value.empty {
            color: var(--text-muted);
            font-style: italic;
            font-weight: 400;
        }

        /* ── Photo Section ── */
        .photo-section {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 24px;
        }

        .applicant-photo {
            width: 90px;
            height: 105px;
            border-radius: 8px;
            border: 2px solid var(--border);
            object-fit: cover;
            background: #f8faf9;
            flex-shrink: 0;
        }

        .applicant-photo-placeholder {
            width: 90px;
            height: 105px;
            border-radius: 8px;
            border: 2px dashed var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8faf9;
            flex-shrink: 0;
        }

        .applicant-photo-placeholder svg {
            width: 28px;
            height: 28px;
            fill: var(--text-muted);
            opacity: 0.4;
            margin-bottom: 4px;
        }

        .applicant-photo-placeholder span {
            font-size: 0.62rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .photo-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            flex: 1;
        }

        /* ── Family Members Table ── */
        .family-table {
            width: 100%;
            border-collapse: collapse;
        }

        .family-table thead th {
            background: var(--primary-dark);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 14px;
            text-align: left;
            border: none;
        }

        .family-table thead th:first-child {
            width: 40px;
            text-align: center;
        }

        .family-table tbody td {
            padding: 10px 14px;
            font-size: 0.85rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
        }

        .family-table tbody td:first-child {
            text-align: center;
            font-weight: 700;
            color: var(--text-muted);
        }

        .family-table tbody tr:hover td {
            background: rgba(23, 107, 69, 0.02);
        }

        .family-table tbody tr:last-child td {
            border-bottom: none;
        }

        .family-empty {
            text-align: center;
            padding: 32px 20px;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .family-empty svg {
            width: 36px;
            height: 36px;
            fill: var(--border);
            margin-bottom: 8px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Documents Status ── */
        .doc-status-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            padding: 20px 24px;
        }

        .doc-status-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            transition: all 0.2s;
        }

        .doc-status-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .doc-status-item.completed {
            border-color: rgba(47, 138, 96, 0.3);
            background: rgba(47, 138, 96, 0.03);
        }

        .doc-status-item.incomplete {
            border-color: rgba(199, 154, 43, 0.3);
            background: rgba(199, 154, 43, 0.03);
        }

        .doc-status-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .doc-status-item.completed .doc-status-icon {
            background: linear-gradient(135deg, #2f8a60, #3da870);
        }

        .doc-status-item.incomplete .doc-status-icon {
            background: linear-gradient(135deg, var(--accent), #d4a83a);
        }

        .doc-status-icon svg {
            width: 16px;
            height: 16px;
            fill: white;
        }

        .doc-status-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1px;
        }

        .doc-status-badge {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .doc-status-item.completed .doc-status-badge {
            color: #2f8a60;
        }

        .doc-status-item.incomplete .doc-status-badge {
            color: var(--accent);
        }

        /* ── Action Bar ── */
        .action-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            padding: 20px 24px;
            margin-bottom: 24px;
            animation: slideUp 0.4s ease 0.3s backwards;
        }

        .action-bar-text h4 {
            font-family: 'Lora', serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0 0 4px;
        }

        .action-bar-text p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }

        .action-bar-btns {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.18s;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-action.outline {
            background: white;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
        }

        .btn-action.outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-action.primary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
        }

        .btn-action.primary:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-1px);
        }

        .btn-action svg {
            width: 15px;
            height: 15px;
            fill: currentColor;
        }

        /* ── Empty State ── */
        .empty-state-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            text-align: center;
            max-width: 520px;
            margin: 40px auto;
            animation: slideUp 0.4s ease;
        }

        .empty-state-hero {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            padding: 40px 32px 32px;
            position: relative;
            overflow: hidden;
        }

        .empty-state-hero::before {
            content: '';
            position: absolute;
            right: -15px;
            bottom: -15px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(201, 168, 76, 0.1);
        }

        .empty-state-hero svg {
            width: 56px;
            height: 56px;
            fill: rgba(255, 255, 255, 0.9);
            margin-bottom: 14px;
        }

        .empty-state-hero h3 {
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin: 0 0 8px;
        }

        .empty-state-hero p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.72);
            margin: 0;
            line-height: 1.6;
        }

        .empty-state-body {
            padding: 28px 32px;
        }

        /* ── Parking Applications Table ── */
        .parking-section {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
            animation: slideUp 0.4s ease 0.25s backwards;
        }

        .parking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .parking-table thead th {
            background: var(--primary-dark);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 10px 14px;
            text-align: left;
            border: none;
        }

        .parking-table tbody td {
            padding: 11px 14px;
            font-size: 0.84rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
        }

        .parking-table tbody tr:hover td {
            background: rgba(23, 107, 69, 0.02);
        }

        .parking-table tbody tr:last-child td {
            border-bottom: none;
        }

        .parking-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .parking-badge.pending {
            background: rgba(199, 154, 43, 0.12);
            color: var(--warning);
        }

        .parking-badge.approved {
            background: rgba(47, 138, 96, 0.12);
            color: var(--success);
        }

        .parking-badge.rejected {
            background: rgba(139, 46, 46, 0.12);
            color: var(--danger);
        }

        .parking-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .parking-empty {
            text-align: center;
            padding: 36px 20px;
            color: var(--text-muted);
        }

        .parking-empty svg {
            width: 40px;
            height: 40px;
            fill: var(--border);
            margin-bottom: 10px;
        }

        .parking-empty h4 {
            font-family: 'Lora', serif;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-muted);
            margin: 0 0 4px;
        }

        .parking-empty p {
            font-size: 0.82rem;
            margin: 0 0 16px;
        }

        .parking-empty .btn-apply {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 22px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
            transition: all 0.18s;
        }

        .parking-empty .btn-apply:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-1px);
        }

        .parking-empty .btn-apply svg {
            width: 15px;
            height: 15px;
            fill: currentColor;
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

        /* ── Animations ── */
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

            .info-grid,
            .info-grid-3 {
                grid-template-columns: 1fr;
            }

            .info-grid .info-field,
            .info-grid-3 .info-field {
                border-right: none;
            }

            .status-summary {
                grid-template-columns: 1fr 1fr;
            }

            .status-hero-header {
                flex-direction: column;
                text-align: center;
            }

            .status-hero-header-left {
                flex-direction: column;
            }

            .doc-status-grid {
                grid-template-columns: 1fr;
            }

            .timeline {
                flex-direction: column;
                gap: 8px;
                padding: 0;
            }

            .timeline-step:not(:last-child)::after {
                display: none;
            }

            .action-bar {
                flex-direction: column;
                text-align: center;
            }

            .photo-section {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .photo-details {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <?php 
          $active_page = 'apartment_status'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Tenant Information</div>
                    <div class="top-bar-subtitle">View your submitted application details and status</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                <div class="breadcrumb-bar">
                    <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                    <span class="sep">›</span>
                    <a href="<?= url('/user/apartment/apply') ?>">Apartment</a>
                    <span class="sep">›</span>
                    <span class="current">Tenant Information</span>
                </div>

                <!-- Content rendered by JS -->
                <div id="tenant-info-root"></div>

            </div><!-- /.page-body -->
        </div><!-- /.main-content -->
    </div><!-- /.app-wrapper -->

    <script>
        // ═══ DATA HELPERS ═══
        const STORAGE_KEYS = { user: 'mis_user', requests: 'mis_requests', initialized: 'mis_data_init' };
        const DEFAULT_USER = { 
            id: '<?= $_SESSION['user_id'] ?? "USR-001" ?>', 
            name: '<?= addslashes($_SESSION['name'] ?? "User") ?>', 
            role: '<?= addslashes($_SESSION['role'] ?? "Tenant") ?>',
            gender: '<?= addslashes($_SESSION['gender'] ?? "") ?>',
            email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
            phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', membership: '', revertYear: '', apartment: '', profileComplete: false 
        };

        function getUser() {
            const raw = localStorage.getItem(STORAGE_KEYS.user);
            return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
        }

        const user = getUser();

        // ── Load user nav ──
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
            navRole.textContent = isComplete ? "<?= $_SESSION['role'] ?? 'Verified User' ?>" : 'Not Verified';
            navRole.style.color = isComplete ? 'var(--success)' : 'var(--warning)';
        }

        // ── Da'wah dropdown ──
        const dawahMenu = document.getElementById('dawah-menu');
        dawahMenu.innerHTML = user.gender === 'female'
            ? `<a href="<?= url('/user/services/counseling-female') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Sisters' Counseling</a>
       <a href="<?= url('/user/services/islamic-edu-female') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>Sisters' Islamic Education</a>`
            : `<a href="<?= url('/user/services/counseling-male') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Brothers' Counseling</a>
       <a href="<?= url('/user/services/islamic-edu-male') ?>"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>Brothers' Islamic Education</a>`;

        // ── Sidebar collapse ──
        document.getElementById('sidebar-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        // ── Dropdown toggles ──
        function initDropdown(triggerId, menuId) {
            const trigger = document.getElementById(triggerId);
            const menu = document.getElementById(menuId);
            trigger.addEventListener('click', () => {
                const isOpen = menu.classList.contains('open');
                document.querySelectorAll('.nav-dropdown').forEach(m => m.classList.remove('open'));
                document.querySelectorAll('.nav-dropdown-trigger').forEach(btn => btn.classList.remove('open'));
                if (!isOpen) { menu.classList.add('open'); trigger.classList.add('open'); }
            });
        }
        initDropdown('damayan-trigger', 'damayan-menu');
        initDropdown('dawah-trigger', 'dawah-menu');
        initDropdown('apartment-trigger', 'apartment-menu');


        // ═══════════════════════════════════════════
        //  LOAD APPLICATION DATA
        // ═══════════════════════════════════════════
        const root = document.getElementById('tenant-info-root');

        // Get latest application
        const reqRaw = localStorage.getItem(STORAGE_KEYS.requests);
        const requests = reqRaw ? JSON.parse(reqRaw) : [];
        const aptApp = requests.filter(r => r.type === 'apartment_application' && r.user === user.id)
            .sort((a, b) => new Date(b.date) - new Date(a.date))[0];

        // Get report
        const reports = JSON.parse(localStorage.getItem('mis_reports') || '[]');
        const report = reports.find(r => r.tenantId === user.id) || null;

        // Get uploaded docs
        const docUploads = JSON.parse(localStorage.getItem('mis_req_doc_uploads') || '{}');

        // Saved form data
        const formData = JSON.parse(localStorage.getItem('mis_tenant_form_data') || '{}');

        // Helper
        function val(v) { return (v && String(v).trim()) ? v : null; }
        function safeVal(v, fallback) { return val(v) || `<span class="empty">${fallback || 'Not provided'}</span>`; }
        function formatDate(d) {
            if (!d) return 'N/A';
            const date = new Date(d);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        if (!aptApp && !report) {
            // ── NO APPLICATION FOUND ──
            root.innerHTML = `
        <div class="empty-state-card">
          <div class="empty-state-hero">
            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
            <h3>No Application Found</h3>
            <p>You haven't submitted a tenant application yet. Start your application to view your information here.</p>
          </div>
          <div class="empty-state-body">
            <a href="<?= url('/user/apartment/apply') ?>" class="btn-action primary" style="display:inline-flex;">
              <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
              Start Application
            </a>
          </div>
        </div>
      `;
        } else {
            // ── BUILD FULL VIEW ──
            const appStatus = report ? report.status : (aptApp ? aptApp.status : 'pending');
            const statusMap = {
                'pending': { label: 'Pending Review', cls: 'pending', icon: '⏳' },
                'PENDING_MIS': { label: 'Pending MIS Review', cls: 'pending', icon: '⏳' },
                'VERIFIED': { label: 'Verified', cls: 'approved', icon: '✓' },
                'approved': { label: 'Approved', cls: 'approved', icon: '✓' },
                'rejected': { label: 'Rejected', cls: 'rejected', icon: '✕' }
            };
            const sts = statusMap[appStatus] || statusMap.pending;
            const refId = report ? report.id : (aptApp ? aptApp.id : 'N/A');
            const submittedDate = report ? report.submittedAt : (aptApp ? aptApp.date : null);

            // Photo
            const userPhoto = localStorage.getItem('mis_user_photo');

            // Docs status
            const docItems = [
                { name: 'Proof of Income', key: 'doc-income', icon: '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>' },
                { name: 'Valid ID (Front)', key: 'doc-id-front', icon: '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 18V6h16v12H4z"/>' },
                { name: 'Valid ID (Back)', key: 'doc-id-back', icon: '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 18V6h16v12H4z"/>' },
                { name: 'Birth Certificate', key: 'doc-birth', icon: '<path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>' },
                { name: 'NBI / Police Clearance', key: 'doc-nbi', icon: '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>' }
            ];
            const docsUploaded = docItems.filter(d => !!docUploads[d.key]).length;
            const docsTotal = docItems.length;

            // Family members from localStorage
            const famData = JSON.parse(localStorage.getItem('mis_tenant_family') || '[]');

            // Timeline stages
            const stages = [
                { label: 'Application', key: 'submitted' },
                { label: 'Documents', key: 'documents' },
                { label: 'MIS Review', key: 'review' },
                { label: 'Verified', key: 'verified' },
                { label: 'Approved', key: 'approved' }
            ];

            let activeStageIdx = 1; // Default = documents
            if (docsUploaded >= docsTotal) activeStageIdx = 2;
            if (appStatus === 'VERIFIED') activeStageIdx = 3;
            if (appStatus === 'approved' || appStatus === 'VERIFIED') activeStageIdx = 3;
            if (appStatus === 'approved') activeStageIdx = 4;

            const timelineHtml = stages.map((s, i) => {
                const cls = i < activeStageIdx ? 'completed' : (i === activeStageIdx ? 'active' : '');
                const dotContent = i < activeStageIdx
                    ? '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
                    : (i + 1);
                return `
          <div class="timeline-step ${cls}">
            <div class="timeline-dot">${dotContent}</div>
            <div class="timeline-label">${s.label}</div>
          </div>
        `;
            }).join('');

            const docsStatusHtml = docItems.map(d => {
                const uploaded = !!docUploads[d.key];
                return `
          <div class="doc-status-item ${uploaded ? 'completed' : 'incomplete'}">
            <div class="doc-status-icon">
              <svg viewBox="0 0 24 24">${uploaded
                        ? '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>'
                        : '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>'
                    }</svg>
            </div>
            <div>
              <div class="doc-status-name">${d.name}</div>
              <div class="doc-status-badge">${uploaded ? 'Uploaded' : 'Pending'}</div>
            </div>
          </div>
        `;
            }).join('');

            const familyHtml = famData.length > 0
                ? `<table class="family-table">
            <thead><tr><th>#</th><th>Name</th><th>Relation</th><th>Age</th><th>Religion</th></tr></thead>
            <tbody>
              ${famData.map((m, i) => `
                <tr>
                  <td>${i + 1}.</td>
                  <td>${m.name || '—'}</td>
                  <td>${m.relation || '—'}</td>
                  <td>${m.age || '—'}</td>
                  <td>${m.religion || '—'}</td>
                </tr>
              `).join('')}
            </tbody>
           </table>`
                : `<div class="family-empty">
            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            No family members listed in this application.
           </div>`;

            root.innerHTML = `
        <!-- ═══ STATUS HERO ═══ -->
        <div class="status-hero">
          <div class="status-hero-top">
            <div class="status-hero-header">
              <div class="status-hero-header-left">
                <div class="status-hero-avatar" id="hero-avatar">${user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()}</div>
                <div>
                  <h2 class="status-hero-name">${user.name}</h2>
                  <p class="status-hero-subtitle">Ref: ${refId} • Submitted ${formatDate(submittedDate)}</p>
                </div>
              </div>
              <div class="status-badge ${sts.cls}">
                <span class="status-badge-dot"></span>
                ${sts.label}
              </div>
            </div>
          </div>
          <div class="status-summary">
            <div class="summary-stat">
              <div class="summary-stat-label">Reference</div>
              <div class="summary-stat-value">${refId}</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Submitted</div>
              <div class="summary-stat-value">${formatDate(submittedDate)}</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Documents</div>
              <div class="summary-stat-value" style="color:${docsUploaded >= docsTotal ? 'var(--success)' : 'var(--warning)'};">${docsUploaded}/${docsTotal}</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Status</div>
              <div class="summary-stat-value" style="color:${sts.cls === 'approved' ? 'var(--success)' : sts.cls === 'rejected' ? 'var(--danger)' : 'var(--warning)'};">${sts.label}</div>
            </div>
          </div>
        </div>

        <!-- ═══ TIMELINE ═══ -->
        <div class="timeline-card">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon">
                <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
              </div>
              <h3 class="card-header-title">Application Progress</h3>
            </div>
          </div>
          <div class="card-body">
            <div class="timeline">
              ${timelineHtml}
            </div>
          </div>
        </div>

        <!-- ═══ APPLICANT INFORMATION ═══ -->
        <div class="info-section" style="animation: slideUp 0.4s ease 0.1s backwards;">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
              </div>
              <h3 class="card-header-title">Applicant's Information</h3>
            </div>
          </div>
          <div class="photo-section">
            ${userPhoto
                    ? `<img src="${userPhoto}" alt="Applicant Photo" class="applicant-photo" />`
                    : `<div class="applicant-photo-placeholder">
                  <svg viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                  <span>No Photo</span>
                 </div>`
                }
            <div class="photo-details">
              <div>
                <div class="info-field-label">Full Name</div>
                <div class="info-field-value">${safeVal(user.name)}</div>
              </div>
              <div>
                <div class="info-field-label">Muslim Name</div>
                <div class="info-field-value">${safeVal(user.arabicName, 'Not provided')}</div>
              </div>
              <div>
                <div class="info-field-label">Application Date</div>
                <div class="info-field-value">${formatDate(submittedDate)}</div>
              </div>
              <div>
                <div class="info-field-label">Reference ID</div>
                <div class="info-field-value" style="font-family:'Lora',serif;color:var(--accent);">${refId}</div>
              </div>
            </div>
          </div>
          <div class="info-grid">
            <div class="info-field">
              <div class="info-field-label">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Date of Birth
              </div>
              <div class="info-field-value">${safeVal(user.dob ? formatDate(user.dob) : null)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Gender
              </div>
              <div class="info-field-value">${safeVal(user.gender)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">
                <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                Phone Number
              </div>
              <div class="info-field-value">${safeVal(user.phone)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">
                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                Email Address
              </div>
              <div class="info-field-value">${safeVal(user.email)}</div>
            </div>
            <div class="info-field full-width">
              <div class="info-field-label">
                <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                Complete Address
              </div>
              <div class="info-field-value">${safeVal(user.address)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Civil Status</div>
              <div class="info-field-value">${safeVal(user.civil)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Occupation</div>
              <div class="info-field-value">${safeVal(user.occupation)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Masjid Membership</div>
              <div class="info-field-value">${safeVal(user.membership)}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Year of Reversion</div>
              <div class="info-field-value">${safeVal(user.revertYear)}</div>
            </div>
          </div>
        </div>

        <!-- ═══ FAMILY MEMBERS ═══ -->
        <div class="info-section" style="animation: slideUp 0.4s ease 0.15s backwards;">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              </div>
              <h3 class="card-header-title">Family Members</h3>
            </div>
          </div>
          ${familyHtml}
        </div>

        <!-- ═══ DOCUMENTS STATUS ═══ -->
        <div class="info-section" style="animation: slideUp 0.4s ease 0.2s backwards;">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon" style="${docsUploaded >= docsTotal ? 'background:linear-gradient(135deg,#2f8a60,#3da870);' : ''}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
              </div>
              <h3 class="card-header-title">Required Documents</h3>
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.75rem;font-weight:600;color:var(--text-muted);">
              <div style="width:80px;height:6px;border-radius:3px;background:var(--border);overflow:hidden;">
                <div style="height:100%;border-radius:3px;background:linear-gradient(90deg,var(--primary-dark),var(--accent));width:${Math.round(docsUploaded / docsTotal * 100)}%;transition:width 0.5s ease;"></div>
              </div>
              ${docsUploaded}/${docsTotal} Uploaded
            </div>
          </div>
          <div class="doc-status-grid">
            ${docsStatusHtml}
          </div>
        </div>

        <?php if (($_SESSION['role'] ?? '') === 'Tenant'): ?>
        <!-- ═══ PARKING APPLICATIONS ═══ -->
        <div class="parking-section">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon" style="background:linear-gradient(135deg,#1a5c8a,#2980b9);">
                <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
              </div>
              <h3 class="card-header-title">Parking Applications</h3>
            </div>
            <a href="<?= url('/user/apartment/parking') ?>" class="btn-action outline" style="font-size:0.75rem;padding:6px 14px;min-height:auto;text-decoration:none;">
              <svg viewBox="5 -2 24 24" style="width:13px;height:13px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
              New Application
            </a>
          </div>
          <div id="parking-apps-body"></div>
        </div>
        <?php endif; ?>

        <!-- ═══ ACTION BAR ═══ -->
        <div class="action-bar">
          <div class="action-bar-text">
            <h4>${docsUploaded >= docsTotal ? 'Application Complete' : 'Complete Your Application'}</h4>
            <p>${docsUploaded >= docsTotal ? 'All documents have been submitted. Awaiting admin review.' : 'Upload remaining documents to finalize your application.'}</p>
          </div>
          <div class="action-bar-btns">
            <a href="<?= url('/user/dashboard') ?>" class="btn-action outline">
              <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
              Dashboard
            </a>
            ${docsUploaded < docsTotal
                    ? `<a href="<?= url('/user/apartment/apply') ?>" class="btn-action primary">
                  <svg viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                  Upload Documents
                 </a>`
                    : `<a href="<?= url('/user/apartment/apply') ?>" class="btn-action primary">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                  View Application Form
                 </a>`
                }
          </div>
        </div>
      `;

            // Set hero avatar photo
            if (userPhoto) {
                const heroAvatar = document.getElementById('hero-avatar');
                if (heroAvatar) {
                    heroAvatar.textContent = '';
                    heroAvatar.style.backgroundImage = 'url(' + userPhoto + ')';
                }
            }

            // ── Render Parking Applications ──
            renderParkingApps();
        }

        function renderParkingApps() {
            const parkingBody = document.getElementById('parking-apps-body');
            if (!parkingBody) return;

            const parkingRaw = localStorage.getItem('mis_parking_applications');
            const parkingApps = parkingRaw ? JSON.parse(parkingRaw) : [];
            const myApps = parkingApps.filter(a => a.tenantId === user.id);

            if (myApps.length === 0) {
                parkingBody.innerHTML = `
          <div class="parking-empty">
            <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
            <h4>No Parking Applications</h4>
            <p>You haven't submitted any parking application yet.</p>
            <a href="<?= url('/user/apartment/parking') ?>" class="btn-apply">
              <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
              Apply for Parking
            </a>
          </div>
        `;
            } else {
                parkingBody.innerHTML = `
          <table class="parking-table">
            <thead>
              <tr>
                <th>Parking ID</th>
                <th>Vehicle</th>
                <th>Plate No.</th>
                <th>Type</th>
                <th>Date Started</th>
                <th>Submitted</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${[...myApps].reverse().map(app => `
                <tr>
                  <td style="font-weight:700;color:var(--primary-dark);font-size:0.82rem;">${app.id}</td>
                  <td style="font-weight:600;">${app.vehicleName || '—'}</td>
                  <td style="font-weight:700;letter-spacing:0.04em;">${app.plateNo || '—'}</td>
                  <td>${app.vehicleType || '—'}</td>
                  <td>${app.dateStarted ? formatDate(app.dateStarted) : '—'}</td>
                  <td>${app.submittedAt ? formatDate(app.submittedAt) : '—'}</td>
                  <td>
                    <span class="parking-badge ${app.status === 'PENDING' ? 'pending' : app.status === 'APPROVED' ? 'approved' : 'rejected'}">
                      <span class="parking-badge-dot"></span>
                      ${app.status === 'PENDING' ? 'Pending' : app.status === 'APPROVED' ? 'Approved' : 'Rejected'}
                    </span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
            }
        }
    </script>
</body>

</html>
