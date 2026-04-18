<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Parking Rental Application</title>
    <meta name="description" content="Submit a parking rental application for ISCAG apartment tenants" />
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <style>
        /* ═══════════════════════════════════════════
       PARKING FORM — Paper Document Style
       ═══════════════════════════════════════════ */
        .form-document {
            background: white;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: slideUp 0.4s ease;
        }

        /* ── FORM HEADER ── */
        .form-doc-header {
            background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
            padding: 28px 32px 20px;
            border-bottom: 3px solid var(--primary);
            position: relative;
        }

        .form-doc-header-top {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 8px;
        }

        .form-doc-header-logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            flex-shrink: 0;
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

        .form-doc-header-logo-right {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
            flex-shrink: 0;
        }

        .form-doc-title-bar {
            text-align: center;
            margin-top: 16px;
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

        .form-doc-subtitle {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 8px;
        }

        /* ── DATE ROW ── */
        .date-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 32px 12px;
        }

        .date-row .date-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-row .date-group label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .date-row .date-group input[type="date"] {
            width: 170px;
            padding: 6px 10px;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            font-size: 0.85rem;
            font-family: 'Source Sans 3', sans-serif;
            color: var(--text-main);
            background: white;
        }

        .date-row .date-group input[type="date"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
        }

        .parking-no-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .parking-no-group label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .parking-no-group input {
            width: 130px;
            padding: 6px 10px;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            font-size: 0.85rem;
            font-family: 'Source Sans 3', sans-serif;
            color: var(--text-main);
            background: #f8faf9;
            text-align: center;
            font-weight: 700;
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
            min-width: 130px;
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
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
            padding-right: 28px;
        }

        /* ── ADDRESS GRID ── */
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 0;
        }

        .address-grid .addr-cell {
            border: 1px solid var(--border);
            padding: 0;
        }

        .address-grid .addr-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 8px 0;
            background: #f8faf9;
        }

        .address-grid .addr-cell input {
            width: 100%;
            border: none;
            padding: 4px 8px 6px;
            font-size: 0.83rem;
            font-family: 'Source Sans 3', sans-serif;
            color: var(--text-main);
            background: transparent;
            outline: none;
        }

        .address-grid .addr-cell input:focus {
            background: rgba(23, 107, 69, 0.03);
        }

        .address-grid .addr-cell input::placeholder {
            color: #c0c8c4;
        }

        /* ── SIGNATURE SECTION ── */
        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1.5px solid var(--border);
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            width: 100%;
            height: 1px;
            background: var(--text-main);
            margin-top: 50px;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .signature-sublabel {
            font-size: 0.68rem;
            color: var(--text-muted);
            font-style: italic;
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

        .form-doc-footer .footer-revised {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.45);
            margin-top: 4px;
        }

        /* ── SUBMIT ROW ── */
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

        .btn-cancel {
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
            min-height: 42px;
        }

        .btn-cancel:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: rgba(139, 46, 46, 0.04);
        }

        .btn-submit {
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
            min-height: 42px;
            gap: 6px;
        }

        .btn-submit:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ═══════════════════════════════════════════
       STATUS TRACKER (After submission)
       ═══════════════════════════════════════════ */
        .status-tracker {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
            animation: slideUp 0.4s ease;
        }

        .status-tracker-hero {
            padding: 28px 32px 24px;
            position: relative;
            overflow: hidden;
        }

        .status-tracker-hero.pending {
            background: linear-gradient(135deg, #c79a2b, #dab44b);
        }

        .status-tracker-hero.approved {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        }

        .status-tracker-hero.rejected {
            background: linear-gradient(135deg, #8b2e2e, #b94545);
        }

        .status-tracker-hero::before {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -20px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .status-tracker-hero h2 {
            font-family: 'Lora', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin: 0 0 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-tracker-hero h2 svg {
            width: 22px;
            height: 22px;
            fill: rgba(255, 255, 255, 0.85);
        }

        .status-tracker-hero p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
        }

        .status-tracker-body {
            padding: 20px 32px;
        }

        .status-timeline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            padding: 20px 0;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 40px;
            right: 40px;
            height: 3px;
            background: var(--border);
            z-index: 0;
            transform: translateY(-50%);
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            z-index: 1;
            position: relative;
        }

        .timeline-dot {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 3px solid var(--border);
            transition: all 0.3s ease;
        }

        .timeline-dot svg {
            width: 18px;
            height: 18px;
            fill: var(--text-muted);
        }

        .timeline-dot.active {
            border-color: var(--accent);
            background: rgba(199, 154, 43, 0.1);
            animation: pulse 2s infinite;
        }

        .timeline-dot.active svg {
            fill: var(--accent);
        }

        .timeline-dot.done {
            border-color: var(--success);
            background: var(--success);
        }

        .timeline-dot.done svg {
            fill: white;
        }

        .timeline-dot.rejected-dot {
            border-color: var(--danger);
            background: var(--danger);
        }

        .timeline-dot.rejected-dot svg {
            fill: white;
        }

        .timeline-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: center;
            max-width: 90px;
        }

        .timeline-label.active-label {
            color: var(--accent);
        }

        .timeline-label.done-label {
            color: var(--success);
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(199, 154, 43, 0.35);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(199, 154, 43, 0);
            }
        }

        /* ═══════════════════════════════════════════
       APPROVED PARKING TABLE
       ═══════════════════════════════════════════ */
        .parking-table-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            animation: slideUp 0.4s ease 0.1s backwards;
        }

        .parking-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to right, rgba(26, 58, 92, 0.02), transparent);
        }

        .parking-table-header h3 {
            font-family: 'Lora', serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .parking-table-header h3 svg {
            width: 18px;
            height: 18px;
            fill: var(--accent);
        }

        .parking-table-wrapper {
            overflow-x: auto;
        }

        .parking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .parking-table thead th {
            background: var(--primary-dark);
            color: white;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 16px;
            text-align: left;
            white-space: nowrap;
        }

        .parking-table tbody td {
            padding: 12px 16px;
            font-size: 0.85rem;
            color: var(--text-main);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .parking-table tbody tr:hover td {
            background: rgba(23, 107, 69, 0.02);
        }

        .parking-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
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

        .badge-pending {
            background: rgba(199, 154, 43, 0.12);
            color: var(--warning);
        }

        .badge-approved {
            background: rgba(47, 138, 96, 0.12);
            color: var(--success);
        }

        .badge-rejected {
            background: rgba(139, 46, 46, 0.12);
            color: var(--danger);
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }

        .empty-state svg {
            width: 56px;
            height: 56px;
            fill: var(--border);
            margin-bottom: 14px;
        }

        .empty-state h4 {
            font-family: 'Lora', serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-muted);
            margin: 0 0 6px;
        }

        .empty-state p {
            font-size: 0.82rem;
            margin: 0 0 20px;
        }

        .btn-new-application {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 28px;
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
        }

        .btn-new-application:hover {
            box-shadow: 0 6px 20px rgba(23, 107, 69, 0.35);
            transform: translateY(-1px);
        }

        .btn-new-application svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* ── View Switcher ── */
        .view-switcher {
            display: flex;
            gap: 0;
            border-bottom: 2px solid var(--border);
            margin-bottom: 24px;
        }

        .view-tab {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.18s;
            margin-bottom: -2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .view-tab:hover {
            color: var(--primary);
        }

        .view-tab.active {
            color: var(--primary-dark);
            border-bottom-color: var(--primary);
        }

        .view-tab svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .view-tab .tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0 6px;
            background: rgba(23, 107, 69, 0.1);
            color: var(--primary);
        }

        .view-panel {
            display: none;
        }

        .view-panel.active {
            display: block;
        }

        /* ── Reject Feedback Card ── */
        .reject-feedback {
            background: rgba(139, 46, 46, 0.04);
            border: 1.5px solid rgba(139, 46, 46, 0.15);
            border-radius: 10px;
            padding: 16px 20px;
            margin-top: 16px;
        }

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

            .status-timeline {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
                padding-left: 20px;
            }

            .status-timeline::before {
                top: 0;
                bottom: 0;
                left: 38px;
                right: auto;
                width: 3px;
                height: auto;
                transform: none;
            }

            .timeline-step {
                flex-direction: row;
                gap: 14px;
            }
        }
    </style>
</head>

<body>
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
                    <div class="top-bar-title">Parking Rental Application</div>
                    <div class="top-bar-subtitle">Apply for a parking slot in the ISCAG apartment complex</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                <div class="breadcrumb-bar">
                    <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                    <span class="sep">›</span>
                    <a href="<?= url('/user/apartment/apply') ?>">Apartment</a>
                    <span class="sep">›</span>
                    <span class="current">Parking Rental</span>
                </div>

                <!-- ═══ APPLICATION FORM ═══ -->
                    <div class="form-document">

                        <!-- FORM HEADER -->
                        <div class="form-doc-header">
                            <div class="form-doc-header-top">
                                <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo"
                                    onerror="this.style.display='none'" />
                                <div class="form-doc-header-text">
                                    <div class="arabic-line">بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>
                                    <div class="org-name-ar">مركز البحوث الإسلامية و الدعوة و الإرشاد في الفلبين</div>
                                    <div class="org-name-en">Islamic Studies, Call and Guidance of the Philippines</div>
                                    <div class="sec-reg">SEC. REG. NO. 0000185967</div>
                                </div>
                                <img src="<?= asset('assets/logo.jpg') ?>" alt="ISCAG Logo" class="form-doc-header-logo-right"
                                    onerror="this.style.display='none'" />
                            </div>
                            <div class="form-doc-title-bar">
                                <div>
                                    <div class="form-doc-title">Parking Rental</div>
                                    <div class="form-doc-subtitle">Application Form</div>
                                </div>
                            </div>
                        </div>

                        <!-- DATE & PARKING NO ROW -->
                        <div class="date-row">
                            <div class="parking-no-group">
                                <label>Parking No.:</label>
                                <input type="text" id="parking-no" placeholder="Auto-generated" readonly />
                            </div>
                            <div class="date-group">
                                <label>Date:</label>
                                <input type="date" id="form-date" />
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
                                        <input type="text" id="full-name" placeholder="Enter your full name" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="field-label">Date of Birth</td>
                                    <td class="field-value" colspan="3">
                                        <input type="date" id="date-of-birth" />
                                    </td>
                                </tr>
                            </table>

                            <!-- COMPLETE ADDRESS -->
                            <table class="info-table">
                                <tr>
                                    <td class="field-label">Complete Address</td>
                                    <td class="field-value" colspan="3">
                                        <input type="text" id="address-full" placeholder="House no. / Street" />
                                    </td>
                                </tr>
                            </table>

                            <div class="address-grid">
                                <div class="addr-cell">
                                    <div class="addr-label">Room No.</div>
                                    <input type="text" id="room-no" placeholder="e.g. 101" />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Bldg. No.</div>
                                    <input type="text" id="bldg-no" placeholder="e.g. A" />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Barangay</div>
                                    <input type="text" id="brgy" placeholder="e.g. Salitran I" />
                                </div>
                                <div class="addr-cell">
                                    <div class="addr-label">Municipality / City</div>
                                    <input type="text" id="mun-city" placeholder="e.g. Dasmariñas City" />
                                </div>
                            </div>

                            <!-- VEHICLE INFORMATION -->
                            <div class="doc-section-title">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" />
                                </svg>
                                Vehicle Information
                            </div>

                            <table class="info-table">
                                <tr>
                                    <td class="field-label">Name of Vehicle</td>
                                    <td class="field-value" colspan="3">
                                        <input type="text" id="vehicle-name" placeholder="e.g. Toyota Vios" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="field-label">Name of Owner</td>
                                    <td class="field-value" colspan="3">
                                        <input type="text" id="vehicle-owner"
                                            placeholder="Enter vehicle owner's name" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="field-label">Type of Vehicle</td>
                                    <td class="field-value">
                                        <select id="vehicle-type">
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
                                        <input type="text" id="plate-no" placeholder="e.g. ABC 1234"
                                            style="text-transform:uppercase;font-weight:600;" />
                                    </td>
                                </tr>
                            </table>

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
                                <button class="btn-cancel" id="btn-reset" type="button">Reset Form</button>
                                <button class="btn-submit" id="btn-submit" type="button">
                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                                    </svg>
                                    Submit Application
                                </button>
                            </div>

                            <!-- FORM FOOTER -->
                            <div class="form-doc-footer">
                                <div class="footer-address">Jose Abad Santos Street, Salitran I, City of Dasmariñas
                                    City, Cavite,
                                    Philippines - 4114</div>
                                <div class="footer-contacts">
                                    <span>
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                        </svg>
                                        iscagphilippines@gmail.com
                                    </span>
                                    <span>
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                        </svg>
                                        (046) 4161589
                                    </span>
                                    <span>
                                        <svg viewBox="0 0 24 24">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                                        </svg>
                                        www.kld.edu.ph
                                    </span>
                                </div>
                                <div class="footer-revised">Revised Since 2025</div>
                            </div>

                    </div><!-- /.form-document -->

                </div><!-- /.page-body -->
            </div><!-- /.main-content -->
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

            function formatDate(dateStr) {
                if (!dateStr) return '—';
                const d = new Date(dateStr);
                return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
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
                ? `<a href="../Female/counseling_female.html"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Sisters' Counseling</a>
       <a href="../Female/islamic_edu_female.html"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>Sisters' Islamic Education</a>`
                : `<a href="../Da'awah/counseling_male.html"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>Brothers' Counseling</a>
       <a href="../Da'awah/islamic_edu_male.html"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>Brothers' Islamic Education</a>`;

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

            // ── Auto-fill form date ──
            document.getElementById('form-date').valueAsDate = new Date();
            document.getElementById('parking-no').value = generateParkingId();

            // Pre-fill user data
            if (user.name) document.getElementById('full-name').value = user.name;
            if (user.dob) document.getElementById('date-of-birth').value = user.dob;

            // ══════════════════════════════════════════
            // FORM SUBMISSION
            // ══════════════════════════════════════════
            document.getElementById('btn-submit').addEventListener('click', () => {
                const fields = {
                    fullName: document.getElementById('full-name').value.trim(),
                    dob: document.getElementById('date-of-birth').value,
                    addressFull: document.getElementById('address-full').value.trim(),
                    roomNo: document.getElementById('room-no').value.trim(),
                    bldgNo: document.getElementById('bldg-no').value.trim(),
                    brgy: document.getElementById('brgy').value.trim(),
                    munCity: document.getElementById('mun-city').value.trim(),
                    vehicleName: document.getElementById('vehicle-name').value.trim(),
                    vehicleOwner: document.getElementById('vehicle-owner').value.trim(),
                    vehicleType: document.getElementById('vehicle-type').value,
                    plateNo: document.getElementById('plate-no').value.trim().toUpperCase(),
                    dateStarted: document.getElementById('date-started').value,
                };

                // Validate required fields
                const requiredMap = {
                    fullName: 'Full Name', vehicleName: 'Name of Vehicle',
                    vehicleOwner: 'Name of Owner', vehicleType: 'Type of Vehicle',
                    plateNo: 'Plate No.', dateStarted: 'Date Started'
                };

                for (const [key, label] of Object.entries(requiredMap)) {
                    if (!fields[key]) {
                        showToast('⚠️ Please fill in: ' + label, 'var(--danger)');
                        return;
                    }
                }

                const apps = getParkingApps();
                const newApp = {
                    id: generateParkingId(),
                    tenantId: user.id,
                    tenantName: fields.fullName,
                    date: document.getElementById('form-date').value,
                    ...fields,
                    status: 'PENDING', // PENDING, APPROVED, REJECTED
                    submittedAt: new Date().toISOString(),
                    reviewedAt: null,
                    approvedAt: null,
                    remarks: '',
                };

                apps.push(newApp);
                saveParkingApps(apps);

                showToast('✅ Parking application submitted successfully! Awaiting admin approval.', 'var(--success)');

                // Redirect to Tenant Information page
                setTimeout(() => window.location.href = 'tenant_information.html', 1000);
            });

            // ── Reset form ──
            document.getElementById('btn-reset').addEventListener('click', () => {
                document.getElementById('full-name').value = user.name || '';
                document.getElementById('date-of-birth').value = user.dob || '';
                document.getElementById('address-full').value = '';
                document.getElementById('room-no').value = '';
                document.getElementById('bldg-no').value = '';
                document.getElementById('brgy').value = '';
                document.getElementById('mun-city').value = '';
                document.getElementById('vehicle-name').value = '';
                document.getElementById('vehicle-owner').value = '';
                document.getElementById('vehicle-type').value = '';
                document.getElementById('plate-no').value = '';
                document.getElementById('date-started').value = '';
                document.getElementById('parking-no').value = generateParkingId();
                showToast('🔄 Form has been reset.', 'var(--text-muted)');
            });



            // ══════════════════════════════════════════
            // TOAST NOTIFICATION
            // ══════════════════════════════════════════
            function showToast(msg, bg) {
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.style.background = bg || 'var(--primary-dark)';
                toast.textContent = msg;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s';
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            }
        </script>
</body>

</html>