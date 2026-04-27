<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Apartment Information</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
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
          $active_page = 'apartment_info'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Apartment Information</div>
                    <div class="top-bar-subtitle">View details and rules of your assigned apartment unit</div>
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
                    <span class="current">Apartment Information</span>
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
            sex: '<?= addslashes($_SESSION['sex'] ?? $_SESSION['gender'] ?? "") ?>',
            email: '<?= addslashes($_SESSION['email'] ?? "") ?>',
            phone: '', address: '', dob: '', civil: '', occupation: '', arabicName: '', membership: '', revertYear: '', apartment: '', profileComplete: false 
        };

        function getUser() {
            const raw = localStorage.getItem(STORAGE_KEYS.user);
            return raw ? JSON.parse(raw) : { ...DEFAULT_USER };
        }

        const user = getUser();

        // ── Get data from server ──
        const dbApp = <?= json_encode($application ?? null) ?>;
        const aptApp = (dbApp && Object.keys(dbApp).length > 0) ? dbApp : null;
        const tenantInfo = <?= json_encode($tenantInfo ?? null) ?>;
        const serverDocs = <?= json_encode($uploadedDocs ?? []) ?>;

        // Merge database info into user object for display
        if (tenantInfo && Object.keys(tenantInfo).length > 0) {
            if (tenantInfo.familyname) user.name = (tenantInfo.givenname || '') + ' ' + (tenantInfo.familyname || '');
            if (tenantInfo.muslimname) user.arabicName = tenantInfo.muslimname;
            if (tenantInfo.birthdate) user.dob = tenantInfo.birthdate;
            if (tenantInfo.sex) user.sex = tenantInfo.sex;
            if (tenantInfo.civil_status) user.civil = tenantInfo.civil_status;
            if (tenantInfo.occupation) user.occupation = tenantInfo.occupation;
            if (tenantInfo.address) user.address = tenantInfo.address;
            if (tenantInfo.email) user.email = tenantInfo.email;
            if (tenantInfo.contactnum) user.phone = tenantInfo.contactnum;
        }

        const hasServerPhoto = serverDocs.includes('picture');

        // ── Load user nav ──
        const navName = document.getElementById('nav-name');
        const navAvatar = document.getElementById('nav-avatar');
        if (navName) navName.textContent = user.name;
        if (navAvatar) {
            const photo = localStorage.getItem('mis_user_photo');
            if (photo || hasServerPhoto) {
                const imgUrl = photo || '<?= url("/user/apartment/serve-image") ?>?type=picture';
                navAvatar.textContent = '';
                navAvatar.style.backgroundImage = 'url(' + imgUrl + ')';
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
            navRole.style.color = isComplete ? 'var(--warning)' : 'var(--success)';
        }

        // ═══════════════════════════════════════════
        //  LOAD APARTMENT DATA
        // ═══════════════════════════════════════════
        const root = document.getElementById('tenant-info-root');

        // Simulate reading the apartments database
        const DEFAULT_APARTMENTS = [
            { id: 'APT-A1', name: 'Unit A-1 · Studio', rent: 3500, type: 'Studio', capacity: '2 Persons', floor: '1st Floor', features: ['1 Bathroom', 'Kitchen Sink', 'Built-in Cabinets'], policies: ['No Smoking', 'No Loud Noises after 10 PM', 'No Pets Allowed'], status: 'occupied' },
            { id: 'APT-A2', name: 'Unit A-2 · 1-Bedroom', rent: 5000, type: '1-Bedroom', capacity: '3 Persons', floor: '1st Floor', features: ['1 Bedroom', '1 Bathroom', 'Living Area', 'Kitchen Counter'], policies: ['No Smoking', 'No Loud Noises after 10 PM', 'Subleasing strictly prohibited'], status: 'available' }
        ];
        
        let assignedApt = null;
        let familyMembers = [];

        if (tenantInfo && tenantInfo.family_data) {
            try {
                const parsed = JSON.parse(tenantInfo.family_data);
                if (Array.isArray(parsed)) familyMembers = parsed;
            } catch (e) { }
        }
        
        if (aptApp && aptApp.status.toLowerCase() === 'assigned') {
             assignedApt = DEFAULT_APARTMENTS.find(a => a.type === aptApp.roomtype) || DEFAULT_APARTMENTS[0];
        }

        if (!assignedApt) {
            if (aptApp) {
                // ── APPLICATION PENDING/QUEUED STATE ──
                const appStatus = aptApp.status;
                const isQueued = appStatus === 'Queued';
                
                root.innerHTML = `
                <div class="empty-state-card">
                    <div class="empty-state-hero" style="background: linear-gradient(135deg, var(--accent), #d4a83a);">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        <h3>${isQueued ? 'Waitlisted' : 'Application in Review'}</h3>
                        <p>${isQueued 
                            ? `You are currently <strong>Position #${aptApp.queue_position}</strong> on the waitlist for a ${aptApp.roomtype}. We will notify you once a unit becomes available.` 
                            : 'Your application has been submitted and is currently under Admin Review. Once a room is assigned, you will see the details here.'}</p>
                    </div>
                    <div class="empty-state-body">
                        <div style="display:flex; gap:12px; justify-content:center;">
                            <a href="<?= url('/user/apartment/status') ?>" class="btn-action primary">
                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                View Status Tracker
                            </a>
                        </div>
                    </div>
                </div>`;
            } else {
                // ── NO APPLICATION AT ALL ──
                root.innerHTML = `
                <div class="empty-state-card">
                    <div class="empty-state-hero">
                        <svg viewBox="0 0 24 24"><path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"/></svg>
                        <h3>No Apartment Assigned</h3>
                        <p>You do not currently have an active apartment unit assigned to you. Once your application is approved and finalized, your unit details will appear here.</p>
                    </div>
                    <div class="empty-state-body">
                        <a href="<?= url('/user/apartment/apply') ?>" class="btn-action primary" style="display:inline-flex;">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                            Apply Now
                        </a>
                    </div>
                </div>`;
            }
        } else {
            root.innerHTML = `
        <!-- ═══ TENANT ACCOUNT INFO ═══ -->
        <div class="section-card" style="margin-bottom: 24px; animation: slideUp 0.4s ease;">
          <div class="section-card-body" style="display: flex; align-items: center; gap: 16px; padding: 20px 24px;">
            ${(serverDocs.includes('picture')) ? `
              <div style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid var(--accent); background-image: url('<?= url("/user/apartment/serve-image") ?>?type=picture'); background-size: cover; background-position: center; flex-shrink: 0;"></div>
            ` : `
              <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-family: 'Lora', serif; font-size: 1.3rem; font-weight: 700; color: white;">
                ${user.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase()}
              </div>
            `}
            <div>
              <h3 style="margin: 0 0 4px; font-family: 'Lora', serif; font-size: 1.15rem; color: var(--primary-dark); font-weight: 700;">${user.name}</h3>
              <p style="margin: 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">
                <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; vertical-align: -2px; margin-right: 4px;"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                ${user.email || 'No email provided'}
              </p>
            </div>
            <div style="margin-left: auto;">
              <span style="background: rgba(47, 138, 96, 0.1); color: #2f8a60; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid rgba(47, 138, 96, 0.2);">Official Tenant</span>
            </div>
          </div>
        </div>

        <!-- ═══ STATUS HERO ═══ -->
        <div class="status-hero" style="animation-delay: 0.05s;">
          <div class="status-hero-top" style="background: linear-gradient(135deg, var(--info), #1e6b7a);">
            <div class="status-hero-header">
              <div class="status-hero-header-left">
                <div class="status-hero-avatar" style="background: rgba(255,255,255,0.2); border: none;">
                  <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:white;"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4zm-8 4H7v-2h2v2zm0-4H7V9h2v2zm0-4H7V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 8h-2v-2h2v2zm0-4h-2V9h2v2z"/></svg>
                </div>
                <div>
                  <h2 class="status-hero-name">${assignedApt.name}</h2>
                  <p class="status-hero-subtitle">Assigned Address: ISCAG Compound, Area A</p>
                </div>
              </div>
              <div class="status-badge approved">
                <span class="status-badge-dot"></span>
                Active Unit
              </div>
            </div>
          </div>
          <div class="status-summary">
            <div class="summary-stat">
              <div class="summary-stat-label">Monthly Rent</div>
              <div class="summary-stat-value">₱${assignedApt.rent.toLocaleString()}</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Unit Type</div>
              <div class="summary-stat-value">${assignedApt.type}</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Due Date</div>
              <div class="summary-stat-value">Every 5th</div>
            </div>
            <div class="summary-stat">
              <div class="summary-stat-label">Capacity</div>
              <div class="summary-stat-value" style="color:var(--text-main);">${assignedApt.capacity}</div>
            </div>
          </div>
        </div>

        <!-- ═══ UNIT DETAILS ═══ -->
        <div class="info-section" style="animation: slideUp 0.4s ease 0.1s backwards;">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon" style="background: linear-gradient(135deg, var(--info), #2980b9);">
                <svg viewBox="0 0 24 24"><path d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z"/></svg>
              </div>
              <h3 class="card-header-title">Unit Features & Details</h3>
            </div>
          </div>
          <div class="info-grid">
            <div class="info-field">
              <div class="info-field-label">Floor Level</div>
              <div class="info-field-value">${assignedApt.floor}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Included Amenities</div>
              <div class="info-field-value" style="font-weight: 500; font-size: 0.85rem;">
                <ul style="margin:0; padding-left:16px;">
                  ${assignedApt.features.map(f => `<li>${f}</li>`).join('')}
                  <li>Sub-metered Electricity & Water</li>
                </ul>
              </div>
            </div>
            <div class="info-field full-width">
              <div class="info-field-label">House Rules & Policies</div>
              <div class="info-field-value" style="font-weight: 500; font-size: 0.85rem; color: var(--danger);">
                <ul style="margin:0; padding-left:16px;">
                  ${assignedApt.policies.map(p => `<li>${p}</li>`).join('')}
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ TENANT GENERAL INFO & FAMILY ═══ -->
        <div class="info-section" style="animation: slideUp 0.4s ease 0.2s backwards;">
          <div class="card-header">
            <div class="card-header-left">
              <div class="card-header-icon" style="background: linear-gradient(135deg, #e67e22, #d35400);">
                <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              </div>
              <h3 class="card-header-title">General Info & Family Members</h3>
            </div>
            <button class="btn-action outline" onclick="openAddFamilyModal()" style="padding: 6px 14px; font-size: 0.75rem;">
               <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
               Add Member
            </button>
          </div>
          <div class="info-grid">
            <div class="info-field">
              <div class="info-field-label">Primary Tenant</div>
              <div class="info-field-value">${tenantInfo && tenantInfo.givenname ? tenantInfo.givenname + ' ' + (tenantInfo.familyname || '') : user.name}</div>
            </div>
            <div class="info-field">
              <div class="info-field-label">Contact</div>
              <div class="info-field-value">${user.phone || 'Not provided'}</div>
            </div>
          </div>
          <div style="padding: 0;">
            <table class="family-table" id="family-table-body">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Full Name</th>
                  <th>Relationship</th>
                  <th>Age</th>
                  <th>Religion</th>
                </tr>
              </thead>
              <tbody>
                <!-- Populated by JS -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- ═══ ACTION BAR ═══ -->
        <div class="action-bar">
          <div class="action-bar-text">
             <h4>Need assistance with your unit?</h4>
             <p>Contact the apartment management for maintenance, repairs, or concerns.</p>
          </div>
          <div class="action-bar-btns">
             <button class="btn-action primary" onclick="alert('Maintenance reporting module coming soon.')">
                <svg viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                Request Maintenance
             </button>
          </div>
        </div>
        `;

            // Render family members
            const tbody = document.querySelector('#family-table-body tbody');
            if (tbody) {
                if (familyMembers.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="family-empty">
                        <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                        No family members added yet.
                    </td></tr>`;
                } else {
                    tbody.innerHTML = familyMembers.map((m, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td style="font-weight: 600;">${m.name}</td>
                            <td>${m.relation}</td>
                            <td>${m.age}</td>
                            <td>${m.religion}</td>
                        </tr>
                    `).join('');
                }
            }
        }

        // Add Family Member Modal Logic
        function openAddFamilyModal() {
            const modalHtml = `
            <div id="add-family-modal" style="position:fixed; inset:0; z-index:99999; display:flex; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,0.4); backdrop-filter:blur(4px); animation:fadeIn 0.2s ease;">
                <div style="background:white; border-radius:16px; width:100%; max-width:400px; box-shadow:0 10px 40px rgba(0,0,0,0.1); overflow:hidden; animation:slideUp 0.3s ease;">
                    <div style="padding:20px 24px; border-bottom:1px solid var(--border); background:linear-gradient(135deg, var(--primary-dark), var(--primary-light)); color:white;">
                        <h3 style="margin:0; font-family:'Lora',serif; font-size:1.1rem;">Add Family Member</h3>
                    </div>
                    <div style="padding:24px;">
                        <div style="margin-bottom:16px;">
                            <label style="display:block; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Full Name</label>
                            <input type="text" id="fam-name" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;" placeholder="e.g. John Doe">
                        </div>
                        <div style="margin-bottom:16px;">
                            <label style="display:block; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Relationship</label>
                            <input type="text" id="fam-rel" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;" placeholder="e.g. Son, Daughter">
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
                            <div>
                                <label style="display:block; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Age</label>
                                <input type="number" id="fam-age" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;" placeholder="e.g. 12">
                            </div>
                            <div>
                                <label style="display:block; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Religion</label>
                                <input type="text" id="fam-relig" style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:0.9rem;" placeholder="e.g. Islam">
                            </div>
                        </div>
                        <div style="display:flex; gap:10px; justify-content:flex-end;">
                            <button onclick="document.getElementById('add-family-modal').remove()" style="padding:10px 20px; border-radius:8px; border:1px solid var(--border); background:white; color:var(--text-muted); font-weight:600; cursor:pointer;">Cancel</button>
                            <button onclick="saveFamilyMember()" id="fam-save-btn" style="padding:10px 20px; border-radius:8px; border:none; background:var(--primary); color:white; font-weight:700; cursor:pointer;">Save Member</button>
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function saveFamilyMember() {
            const name = document.getElementById('fam-name').value.trim();
            const relation = document.getElementById('fam-rel').value.trim();
            const age = document.getElementById('fam-age').value.trim();
            const religion = document.getElementById('fam-relig').value.trim();

            if (!name || !relation) {
                alert('Name and Relationship are required.');
                return;
            }

            const btn = document.getElementById('fam-save-btn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            // Add to local array
            familyMembers.push({ name, relation, age, religion });

            // Send to server
            fetch('<?= url("/user/apartment/save") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    addinfo: {
                        family_data: JSON.stringify(familyMembers)
                    }
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error saving family member.');
                    btn.disabled = false;
                    btn.textContent = 'Save Member';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error.');
                btn.disabled = false;
                btn.textContent = 'Save Member';
            });
        }
    </script>
</body>
</html>