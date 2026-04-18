<?php
/**
 * Tenant Information — Admin View
 * Route: /admin/staff_admin/apartment/tenant_information
 * Controller: StaffApartmentController@tenantInformation
 * Access: Admin, Staff_Tenant
 *
 * Variables injected by controller:
 *   $tenants       (array)  — rows from getAllTenantInformation()
 *   $active_page   (string) — 'tenant_information'
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Tenant Information</title>
    <meta name="description" content="View and monitor submitted tenant application details in the Apartment Department." />
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
    <style>
        /* ═══════════════════════════════════
           PAGE-SPECIFIC DESIGN ENHANCEMENTS
           ═══════════════════════════════════ */

        /* ── Clickable Table Row ── */
        .tenant-row {
            cursor: pointer;
            transition: background 0.18s, box-shadow 0.18s;
        }

        .tenant-row:hover {
            background: rgba(23, 107, 69, 0.055) !important;
        }

        .tenant-row:hover td:first-child {
            border-left: 3px solid var(--accent);
        }

        .tenant-row td:first-child {
            border-left: 3px solid transparent;
            transition: border-color 0.18s;
        }

        /* ── Avatar Chip ── */
        .tenant-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: 0.02em;
        }

        .tenant-name-cell {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .tenant-name-cell .name-block strong {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .tenant-name-cell .name-block span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* ── Stats row override (3-col) ── */
        .stats-row.three-col {
            grid-template-columns: repeat(3, 1fr);
        }

        /* ── Filter / Search Bar ── */
        .filter-toolbar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: #fafbfa;
        }

        .filter-toolbar .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .filter-toolbar .search-wrap svg {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            fill: var(--text-muted);
            pointer-events: none;
        }

        .filter-toolbar input[type="search"] {
            width: 100%;
            padding: 8px 14px 8px 34px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-main);
            background: white;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .filter-toolbar input[type="search"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
        }

        .filter-toolbar select {
            padding: 8px 14px;
            border: 1.5px solid var(--border);
            border-radius: 7px;
            font-size: 0.85rem;
            font-family: inherit;
            color: var(--text-main);
            background: white;
            cursor: pointer;
            transition: border-color 0.18s;
        }

        .filter-toolbar select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* ── Row count label ── */
        .row-count-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-left: auto;
            white-space: nowrap;
        }

        /* ── Apartment Type / Floor chips ── */
        .type-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 0.72rem;
            font-weight: 700;
            background: rgba(23, 107, 69, 0.09);
            color: var(--primary-dark);
            letter-spacing: 0.03em;
        }

        /* ── Empty State ── */
        .empty-state-row td {
            text-align: center;
            padding: 48px 20px;
        }

        .empty-state-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .empty-state-inner svg {
            width: 48px;
            height: 48px;
            fill: var(--border);
        }

        .empty-state-inner h4 {
            font-family: 'Lora', serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-muted);
            margin: 0;
        }

        .empty-state-inner p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }

        .actions-td {
            position: relative !important;
            width: 100px; /* Slightly wider for double buttons */
            text-align: center;
        }

        /* ── Inline Action Buttons ── */
        .action-btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-mini-action {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
        }

        .btn-mini-action:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-mini-action.approve:hover {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        .btn-mini-action.reject:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        .btn-mini-action svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* ── Document Checklist ── */
        .doc-checklist {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8faf9;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.82rem;
        }

        .doc-item-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-main);
        }

        .doc-item-info svg {
            width: 14px;
            height: 14px;
            fill: var(--primary);
        }

        .doc-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ── PRINT STYLES ── */
        @media print {
            @page {
                size: legal portrait;
                margin: 0.5in;
            }

            body * {
                visibility: hidden;
            }

            #printable-tenant-info, #printable-tenant-info * {
                visibility: visible;
            }

            #printable-tenant-info {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white !important;
                color: black !important;
                display: block !important;
            }

            .no-print {
                display: none !important;
            }

            .print-header {
                text-align: center;
                border-bottom: 2px solid #333;
                padding-bottom: 15px;
                margin-bottom: 30px;
            }

            .print-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 20px;
            }

            .print-item {
                margin-bottom: 15px;
            }

            .print-label {
                font-weight: 700;
                font-size: 9pt;
                color: #555;
                text-transform: uppercase;
                display: block;
            }

            .print-value {
                font-size: 11pt;
                font-weight: 600;
                border-bottom: 1px solid #ddd;
                padding-bottom: 2px;
                display: block;
            }
        }

        #printable-tenant-info {
            display: none;
        }

        /* ══════════════════════════════════
           TENANT DETAIL MODAL
           ══════════════════════════════════ */
        .tenant-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1050;
            background: rgba(10, 25, 18, 0.55);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: tModalFade 0.2s ease;
        }

        .tenant-modal-backdrop.open {
            display: flex;
        }

        @keyframes tModalFade {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .tenant-modal {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 860px;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.2);
            animation: tModalSlide 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes tModalSlide {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Modal Header */
        .tenant-modal-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            padding: 20px 24px;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tenant-modal-header .modal-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .tenant-modal-header .modal-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .tenant-modal-header h5 {
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: white;
            margin: 0 0 2px;
        }

        .tenant-modal-header .modal-sub {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.68);
        }

        .tenant-modal-close {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: white;
            flex-shrink: 0;
        }

        .tenant-modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--danger) !important;
            box-shadow: 0 0 0 4px rgba(139, 46, 46, 0.1);
        }

        .tenant-modal-close:hover svg {
            fill: var(--danger) !important;
        }

        .tenant-modal-close svg {
            width: 18px;
            height: 18px;
            fill: white;
            transition: fill 0.2s ease;
        }

        /* ── Profile 2x2 Photo in Modal ── */
        .modal-photo-2x2-container {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .modal-photo-2x2-wrap {
            width: 110px;
            height: 110px;
            border-radius: 10px;
            border: 2.5px solid var(--border);
            overflow: hidden;
            background: #f8faf9;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s, border-color 0.2s;
        }

        .modal-photo-2x2-wrap:hover {
            transform: scale(1.03);
            border-color: var(--primary);
        }

        .modal-photo-2x2-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modal-photo-2x2-wrap .modal-avatar {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            opacity: 0.3;
        }

        /* Modal Body */
        .tenant-modal-body {
            padding: 24px;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 24px;
        }

        @media (max-width: 640px) {
            .tenant-modal-body {
                grid-template-columns: 1fr;
            }
        }

        /* Left: Apartment Image Panel */
        .modal-image-panel {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .modal-apt-image-wrap {
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid var(--border);
            background: #f4f6f5;
            position: relative;
        }

        .modal-apt-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .modal-apt-image-wrap img:hover {
            transform: scale(1.04);
        }

        .modal-apt-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-muted);
        }

        .modal-apt-image-placeholder svg {
            width: 48px;
            height: 48px;
            fill: var(--border);
        }

        .modal-apt-image-placeholder span {
            font-size: 0.78rem;
            font-weight: 600;
        }

        .modal-unit-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-dark);
            color: white;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .modal-unit-badge svg {
            width: 18px;
            height: 18px;
            fill: var(--accent);
            flex-shrink: 0;
        }

        .modal-unit-badge .unit-label {
            font-size: 0.7rem;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modal-unit-badge .unit-val {
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Lora', serif;
        }

        /* Right: Detail Panel */
        .modal-detail-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-section-title {
            font-family: 'Lora', serif;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--primary-dark);
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .modal-section-title svg {
            width: 14px;
            height: 14px;
            fill: var(--accent);
        }

        .modal-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .modal-info-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .modal-info-item.full-width {
            grid-column: 1 / -1;
        }

        .modal-info-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .modal-info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        .modal-info-value.empty {
            color: var(--text-muted);
            font-style: italic;
            font-weight: 400;
        }

        /* Description block */
        .modal-description-box {
            background: #f7faf8;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.85rem;
            color: var(--text-main);
            line-height: 1.6;
        }

        .modal-description-box.empty {
            color: var(--text-muted);
            font-style: italic;
        }

        /* Modal Footer */
        .tenant-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #fafbfa;
            border-radius: 0 0 16px 16px;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <aside class="sidebar" id="sidebar">
            <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
                <svg viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                </svg>
            </button>
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="<?= asset('logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
                    <div class="brand-text">
                        <strong>ISCAG MIS</strong>
                        <span>Apartment Staff</span>
                    </div>
                </div>
            </div>
            <div class="sidebar-user">
                <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">AK</div>
                <div class="user-info">
                    <strong id="nav-name">Apartment Staff</strong>
                    <span>Staff Admin</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-label">Admin</div>
                <a href="<?= url('/admin/staff_admin/apartment/dashboard') ?>"
                   class="nav-item <?= ($active_page ?? '') === 'apartment_dashboard' ? 'active' : '' ?>"
                   data-tooltip="Dashboard">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    <span class="nav-item-label">Dashboard</span>
                </a>
                <a href="<?= url('/admin/staff_admin/apartment/profile') ?>"
                   class="nav-item <?= ($active_page ?? '') === 'apartment_profile' ? 'active' : '' ?>"
                   data-tooltip="Profile">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                    </svg>
                    <span class="nav-item-label">My Profile</span>
                </a>
                <div class="nav-section-label">Management</div>
                <a href="<?= url('/admin/staff_admin/apartment/info') ?>"
                   class="nav-item <?= ($active_page ?? '') === 'apartments_info' ? 'active' : '' ?>"
                   data-tooltip="Apartment Info">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
                    </svg>
                    <span class="nav-item-label">Apartment Info</span>
                </a>
                <a href="<?= url('/admin/staff_admin/apartment/tenant_information') ?>"
                   class="nav-item <?= ($active_page ?? '') === 'tenant_information' ? 'active' : '' ?>"
                   data-tooltip="Tenant Information">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                    <span class="nav-item-label">Tenant Information</span>
                </a>
                <a href="<?= url('/admin/staff_admin/apartment/payment') ?>"
                   class="nav-item <?= ($active_page ?? '') === 'apartment_payment' ? 'active' : '' ?>"
                   data-tooltip="Billing & Payment">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                    </svg>
                    <span class="nav-item-label">Billing & Payment</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                    </svg>
                    <span class="nav-item-label">Logout</span>
                </a>
            </div>
        </aside>

        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Tenant Information</div>
                    <div class="top-bar-subtitle">Monitor and review tenant-submitted application data.</div>
                </div>
                <div class="top-bar-actions">
                    <span id="topbar-record-count" style="font-size:0.8rem;color:var(--text-muted);font-weight:600;">
                        <?= count($tenants ?? []) ?> records
                    </span>
                </div>
            </div>

            <div class="page-body">
                <!-- Breadcrumb -->
                <div class="breadcrumb-bar">
                    <a href="<?= url('/admin/staff_admin/apartment/dashboard') ?>">Apartment Department</a>
                    <span class="sep">›</span>
                    <span class="current">Tenant Information</span>
                </div>

                <!-- ── KPI STATS ROW ── -->
                <?php
                $allTenants  = $tenants ?? [];
                $totalCount  = count($allTenants);
                $pendingCount  = count(array_filter($allTenants, fn($t) => strtolower($t['application_status'] ?? '') === 'pending'));
                $approvedCount = count(array_filter($allTenants, fn($t) => in_array(strtolower($t['application_status'] ?? ''), ['approved', 'active', 'occupied'])));
                ?>
                <div class="stats-row three-col">
                    <div class="stat-card">
                        <div class="stat-icon teal">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                        </div>
                        <div>
                            <div class="stat-value"><?= $totalCount ?></div>
                            <div class="stat-label">Total Tenants</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon gold">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </div>
                        <div>
                            <div class="stat-value"><?= $pendingCount ?></div>
                            <div class="stat-label">Pending Review</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        </div>
                        <div>
                            <div class="stat-value"><?= $approvedCount ?></div>
                            <div class="stat-label">Approved / Active</div>
                        </div>
                    </div>
                </div>

                <!-- ── INFO BANNER ── -->
                <div class="restriction-banner" style="margin-bottom:20px;">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                    </svg>
                    <div>
                        <strong>Read-Only View</strong> — Click any row to view complete tenant &amp; apartment details. Data is sourced directly from tenant-submitted forms.
                    </div>
                </div>

                <!-- ── TENANT RECORDS TABLE ── -->
                <div class="section-card">
                    <div class="section-card-header">
                        <h6>
                            <svg viewBox="0 0 24 24">
                                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
                            </svg>
                            Tenant Submissions
                        </h6>
                        <span id="visible-count-badge" style="font-size:0.72rem;color:var(--text-muted);background:var(--border-light,#edf2ef);padding:3px 10px;border-radius:12px;font-weight:600;"></span>
                    </div>

                    <!-- Filter Toolbar -->
                    <div class="filter-toolbar">
                        <div class="search-wrap">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                            </svg>
                            <input type="search" id="search-input" placeholder="Search by name, unit or type…" autocomplete="off" />
                        </div>
                        <select id="filter-status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="occupied">Occupied</option>
                            <option value="rejected">Rejected</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span class="row-count-label" id="row-count-text">
                            <?= $totalCount ?> result<?= $totalCount !== 1 ? 's' : '' ?>
                        </span>
                    </div>

                    <div class="section-card-body" style="padding:0;">
                        <div class="table-wrapper">
                            <table class="mis-table" id="tenant-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Full Name</th>
                                        <th>Unit / Room</th>
                                        <th>Apartment Type</th>
                                        <th>App. Date</th>
                                        <th>Status</th>
                                        <th style="width:60px;text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tenant-tbody">
                                    <?php if (empty($allTenants)): ?>
                                        <tr class="empty-state-row">
                                            <td colspan="6">
                                                <div class="empty-state-inner">
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                                    </svg>
                                                    <h4>No Tenant Records Found</h4>
                                                    <p>Tenant submissions from the application form will appear here once submitted.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($allTenants as $i => $t):
                                            // ── Build full name ──
                                            $fullName = '';
                                            if (!empty($t['givenname']) || !empty($t['familyname'])) {
                                                $fullName = trim(
                                                    ($t['givenname'] ?? '') . ' ' .
                                                    ($t['middlename'] ? $t['middlename'][0] . '. ' : '') .
                                                    ($t['familyname'] ?? '')
                                                );
                                            }
                                            if (empty($fullName)) {
                                                $fullName = $t['account_name'] ?? 'Unknown';
                                            }

                                            // ── Initials for avatar ──
                                            $nameParts = explode(' ', trim($fullName));
                                            $initials  = strtoupper(
                                                substr($nameParts[0] ?? '', 0, 1) .
                                                substr($nameParts[count($nameParts) - 1] ?? '', 0, 1)
                                            );

                                            // ── Status ──
                                            $status    = $t['application_status'] ?? 'N/A';
                                            $statusLow = strtolower($status);
                                            $badge = 'badge-pending';
                                            if (in_array($statusLow, ['approved', 'active'])) $badge = 'badge-approved';
                                            elseif ($statusLow === 'occupied')                 $badge = 'badge-occupied';
                                            elseif (in_array($statusLow, ['rejected','inactive'])) $badge = 'badge-rejected';

                                            // ── Unit / Room ──
                                            $unit    = $t['roomnumber'] ?? '—';
                                            $aptType = $t['roomtype']   ?? '—';
                                            $floor   = '—'; // apartments_info doesn't have floor yet; placeholder
                                            $desc    = $t['unit_description'] ?? '';
                                            $image   = ''; // placeholder — no image col yet in apartments_info

                                            // ── Application date ──
                                            $appDate = !empty($t['application_date'])
                                                ? date('M j, Y', strtotime($t['application_date']))
                                                : '—';

                                            // ── Address as floor/building hint ──
                                            $floorHint = $t['address'] ?? '—';
                                        ?>
                                        <tr class="tenant-row"
                                            data-name="<?= htmlspecialchars($fullName, ENT_QUOTES) ?>"
                                            data-unit="<?= htmlspecialchars($unit, ENT_QUOTES) ?>"
                                            data-type="<?= htmlspecialchars($aptType, ENT_QUOTES) ?>"
                                            data-status="<?= htmlspecialchars($status, ENT_QUOTES) ?>"
                                            data-floor="<?= htmlspecialchars($floorHint, ENT_QUOTES) ?>"
                                            data-description="<?= htmlspecialchars($desc, ENT_QUOTES) ?>"
                                            data-image="<?= htmlspecialchars($image, ENT_QUOTES) ?>"
                                            data-email="<?= htmlspecialchars($t['email'] ?? '', ENT_QUOTES) ?>"
                                            data-contact="<?= htmlspecialchars($t['contactnum'] ?? '', ENT_QUOTES) ?>"
                                            data-occupation="<?= htmlspecialchars($t['occupation'] ?? '', ENT_QUOTES) ?>"
                                            data-civil="<?= htmlspecialchars($t['civil_status'] ?? $t['tribalaffliation'] ?? '', ENT_QUOTES) ?>"
                                            data-date="<?= htmlspecialchars($appDate, ENT_QUOTES) ?>"
                                            data-initials="<?= htmlspecialchars($initials, ENT_QUOTES) ?>"
                                            data-id="<?= htmlspecialchars($t['application_id'] ?? '', ENT_QUOTES) ?>"
                                            data-tenant-id="<?= htmlspecialchars($t['tenant_id'] ?? '', ENT_QUOTES) ?>"
                                            data-has-gov-id="<?= $t['has_gov_id'] ?? 0 ?>"
                                            data-has-psa="<?= $t['has_psa'] ?? 0 ?>"
                                            data-has-nbi="<?= $t['has_nbi'] ?? 0 ?>"
                                            data-has-income="<?= $t['has_income'] ?? 0 ?>"
                                            data-has-picture="<?= $t['has_picture'] ?? 0 ?>"
                                            title="Click to view full details">
                                            <td class="td-id"><?= str_pad($i + 1, 3, '0', STR_PAD_LEFT) ?></td>
                                            <td>
                                                <div class="tenant-name-cell">
                                                    <div class="tenant-avatar"><?= htmlspecialchars($initials) ?></div>
                                                    <div class="name-block">
                                                        <strong><?= htmlspecialchars($fullName) ?></strong>
                                                        <span><?= htmlspecialchars($t['email'] ?? '—') ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($unit !== '—'): ?>
                                                    <strong style="font-size:0.9rem;"><?= htmlspecialchars($unit) ?></strong>
                                                <?php else: ?>
                                                    <span style="color:var(--text-muted);font-style:italic;">Not assigned</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($aptType !== '—'): ?>
                                                    <span class="type-chip"><?= htmlspecialchars($aptType) ?></span>
                                                <?php else: ?>
                                                    <span style="color:var(--text-muted);">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-size:0.83rem;color:var(--text-muted);"><?= $appDate ?></td>
                                            <td><span class="badge-status <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                                            <td class="actions-td" onclick="event.stopPropagation()">
                                                <?php if ($statusLow === 'pending'): ?>
                                                    <!-- Direct Actions for Pending -->
                                                    <div class="action-btn-group">
                                                        <button class="btn-mini-action approve" 
                                                                onclick="actionConfirm('Approve', '<?= $t['application_id'] ?>', '<?= htmlspecialchars($fullName) ?>')" 
                                                                title="Approve tenant">
                                                            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                        </button>
                                                        <button class="btn-mini-action reject" 
                                                                onclick="actionConfirm('Reject', '<?= $t['application_id'] ?>', '<?= htmlspecialchars($fullName) ?>')" 
                                                                title="Reject tenant">
                                                            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                                        </button>
                                                        <button class="btn-mini-action" onclick="viewTenantBtn(this)" title="View Full Details">
                                                            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Ellipsis for Approved/Archived/etc -->
                                                    <div class="action-menu">
                                                        <button class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
                                                            <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                                        </button>
                                                        <div class="action-menu-dropdown">
                                                            <button class="action-menu-item" onclick="viewTenantBtn(this)">
                                                                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                                                View Details
                                                            </button>
                                                            <button class="action-menu-item" onclick="printTenantInfo('<?= $t['application_id'] ?>')">
                                                                <svg viewBox="0 0 24 24"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
                                                                Print Information
                                                            </button>
                                                            <button class="action-menu-item danger" onclick="archiveTenant('<?= $t['application_id'] ?>', '<?= htmlspecialchars($fullName) ?>')">
                                                                <svg viewBox="0 0 24 24"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM6.24 5h11.52l.83 1H5.41l.83-1zM5 19V8h14v11H5zm11-5.5l-4 4-4-4 1.41-1.41L11 13.67V10h2v3.67l1.59-1.59L16 13.5z"/></svg>
                                                                Move to Archive
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /section-card -->

            </div><!-- /page-body -->
        </div><!-- /main-content -->
    </div><!-- /app-wrapper -->

    <!-- ═══════════════════════════════════════════════════
         TENANT DETAIL MODAL
         ═══════════════════════════════════════════════════ -->
    <div class="tenant-modal-backdrop" id="tenant-modal" role="dialog" aria-modal="true" aria-labelledby="modal-tenant-name">

        <div class="tenant-modal">
            <!-- Header -->
            <div class="tenant-modal-header">
                <div class="modal-header-left">
                    <div class="modal-header-icon-wrap" style="background:rgba(255,255,255,0.1); width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                        <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:white;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <h5 id="modal-tenant-name" style="margin:0;">Tenant Name</h5>
                            <span class="badge" id="modal-status-badge">Pending</span>
                        </div>
                        <div class="modal-sub" id="modal-tenant-email">—</div>
                    </div>
                </div>
                <button class="tenant-modal-close" id="modal-close-btn" aria-label="Close modal">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>

            <!-- Body: 2-column -->
            <div class="tenant-modal-body">

                <!-- Left: Image + Unit -->
                <div class="modal-image-panel">
                    <div class="modal-apt-image-wrap" id="modal-image-wrap">
                        <!-- Image or placeholder injected by JS -->
                        <div class="modal-apt-image-placeholder" id="modal-img-placeholder">
                            <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z"/></svg>
                            <span>No Apartment<br>Image Available</span>
                        </div>
                        <img id="modal-apt-image" src="" alt="Apartment" style="display:none;" />
                    </div>

                    <div class="modal-unit-badge">
                        <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        <div>
                            <div class="unit-label">Unit / Room</div>
                            <div class="unit-val" id="modal-unit-display">—</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details -->
                <div class="modal-detail-panel">

                    <!-- Tenant Information -->
                    <div>
                        <div class="modal-section-title">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            Tenant Information
                        </div>
                        <div class="modal-photo-2x2-container">
                            <div class="modal-info-grid">
                                <div class="modal-info-item full-width">
                                    <span class="modal-info-label">Full Name</span>
                                    <span class="modal-info-value" id="modal-fullname">—</span>
                                </div>
                                <div class="modal-info-item">
                                    <span class="modal-info-label">Email</span>
                                    <span class="modal-info-value" id="modal-email">—</span>
                                </div>
                                <div class="modal-info-item">
                                    <span class="modal-info-label">Contact Number</span>
                                    <span class="modal-info-value" id="modal-contact">—</span>
                                </div>
                                <div class="modal-info-item">
                                    <span class="modal-info-label">Occupation</span>
                                    <span class="modal-info-value" id="modal-occupation">—</span>
                                </div>
                                <div class="modal-info-item">
                                    <span class="modal-info-label">Civil Status</span>
                                    <span class="modal-info-value" id="modal-civil">—</span>
                                </div>
                                <div class="modal-info-item">
                                    <span class="modal-info-label">Application Date</span>
                                    <span class="modal-info-value" id="modal-date">—</span>
                                </div>
                            </div>

                            <!-- 2x2 Photo Positioning (User Request) -->
                            <div class="modal-photo-2x2-wrap" id="modal-photo-2x2-wrap" title="2x2 Profile Photo">
                                <img id="modal-tenant-photo" src="" alt="Profile Photo" style="display:none;">
                                <div class="modal-avatar" id="modal-avatar-initials">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- Apartment Details -->
                    <div>
                        <div class="modal-section-title">
                            <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z"/></svg>
                            Apartment Details
                        </div>
                        <div class="modal-info-grid">
                            <div class="modal-info-item">
                                <span class="modal-info-label">Unit Number</span>
                                <span class="modal-info-value" id="modal-unit-val">—</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">Apartment Type</span>
                                <span class="modal-info-value" id="modal-type">—</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">Status</span>
                                <span class="modal-info-value" id="modal-status">—</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">Floor / Address</span>
                                <span class="modal-info-value" id="modal-floor">—</span>
                            </div>
                            <div class="modal-info-item full-width">
                                <span class="modal-info-label">Unit Description</span>
                                <div class="modal-description-box" id="modal-description">No description provided.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Required Documents Checklist -->
                    <div>
                        <div class="modal-section-title">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                            Required Documents Checklist
                        </div>
                    <div id="doc-list-modal" class="doc-checklist">
                        <div class="doc-item" data-doc="gov-id">
                            <span class="doc-item-info"><svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> Government ID</span>
                            <span class="doc-badge">Unknown</span>
                        </div>
                        <div class="doc-item" data-doc="psa">
                            <span class="doc-item-info"><svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> PSA / Birth Cert.</span>
                            <span class="doc-badge">Unknown</span>
                        </div>
                        <div class="doc-item" data-doc="nbi">
                            <span class="doc-item-info"><svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> NBI / Police Clearance</span>
                            <span class="doc-badge">Unknown</span>
                        </div>
                        <div class="doc-item" data-doc="income">
                            <span class="doc-item-info"><svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> Proof of Income</span>
                            <span class="doc-badge">Unknown</span>
                        </div>
                        <div class="doc-item" data-doc="picture">
                            <span class="doc-item-info"><svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> 2x2 Photo</span>
                            <span class="doc-badge">Unknown</span>
                        </div>
                    </div>
                    </div>

                    <div id="rejection-reason-container" style="margin-top:20px; display:none; border-top:1px solid var(--border); padding-top:16px;">
                        <div class="summary-label" style="display:flex; align-items:center; gap:8px; margin-bottom:12px; color:var(--danger);">
                            <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            Reason for Rejection
                        </div>
                        
                        <select id="modal-reject-reason-select" 
                                style="width:100%; padding:12px; border:1.5px solid var(--border); border-radius:10px; font-family:inherit; font-size:0.85rem; margin-bottom:10px; outline:none; transition:all 0.2s;"
                                onchange="toggleRejectOther(this.value)">
                            <option value="">— Select a Reason —</option>
                            <option value="Missing Required IDs">Missing Required ID's</option>
                            <option value="Incomplete Application Information">Incomplete Information</option>
                            <option value="Invalid or Expired Documents">Invalid or Expired Documents</option>
                            <option value="Proof of Income Not Sufficient">Proof of Income Not Sufficient</option>
                            <option value="Background Check Unsuccessful">Background Check Unsuccessful</option>
                            <option value="Other">Other (Specify Below...)</option>
                        </select>

                        <textarea id="modal-reject-reason-custom" 
                                  placeholder="Provide additional details for rejection..." 
                                  style="width:100%; height:80px; padding:12px; border:1.5px solid var(--border); border-radius:10px; font-family:inherit; font-size:0.85rem; resize:none; outline:none; transition:all 0.2s; display:none;"
                                  onfocus="this.style.borderColor='var(--danger)';"
                                  onblur="this.style.borderColor='var(--border)';"></textarea>
                    </div>
                </div><!-- /modal-detail-panel -->
            </div><!-- /tenant-modal-body -->

            <!-- Footer -->
            <div class="tenant-modal-footer">
                <div id="modal-approval-actions" style="display:none; gap:10px;">
                    <button class="btn-topbar approve" id="modal-btn-approve" style="background:var(--success); color:white; border-color:var(--success);">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Accept Application
                    </button>
                    <button class="btn-topbar reject" id="modal-btn-reject" style="background:rgba(139,46,46,0.1); color:var(--danger); border-color:var(--danger);">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                        Reject
                    </button>
                </div>
                <button class="btn-topbar" id="modal-footer-close">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                    Close
                </button>
            </div>
        </div><!-- /tenant-modal -->

    </div><!-- /tenant-modal-backdrop -->

    <div id="printable-tenant-info"></div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
    /* ═══════════════════════════════════════════════════════════
       TENANT INFORMATION — Dynamic Interactions
       ═══════════════════════════════════════════════════════════ */

    (function () {
        'use strict';

        /* ── DOM references ── */
        const modal         = document.getElementById('tenant-modal');
        const modalClose    = document.getElementById('modal-close-btn');
        const modalFooterCl = document.getElementById('modal-footer-close');
        
        const searchInput   = document.getElementById('search-input');
        const filterStatus  = document.getElementById('filter-status');
        const rowCountText  = document.getElementById('row-count-text');
        const visibleBadge  = document.getElementById('visible-count-badge');
        
        const tbody         = document.getElementById('tenant-tbody');
        const allRows       = tbody ? Array.from(tbody.querySelectorAll('.tenant-row')) : [];

        /* ─────────────────────────────────────────
           MODAL — Open / Close
        ───────────────────────────────────────── */
        function openModal(row) {
            const d = row.dataset;

            // Populate fields
            setText('modal-avatar-initials', d.initials || '?');
            setText('modal-fullname',        d.name    || '—');
            setText('modal-email',           d.email   || '—');
            setText('modal-contact',         d.contact || '—');
            setText('modal-occupation',      d.occupation || '—');
            setText('modal-civil',           d.civil   || '—');
            setText('modal-date',            d.date    || '—');
            setText('modal-unit-display',    d.unit    || '—');
            setText('modal-unit-val',        d.unit    || '—');
            setText('modal-type',            d.type    || '—');
            setText('modal-floor',           d.floor   || '—');
            
            // Header display
            setText('modal-tenant-name',     d.name    || '—');
            setText('modal-tenant-email',    d.email   || '—');

            // ── Tenant Profile Photo (User Request: 2x2) ──
            const profileImg = document.getElementById('modal-tenant-photo');
            const initials   = document.getElementById('modal-avatar-initials');
            
            if (d.photo && d.photo.trim() && d.photo !== 'null') {
                profileImg.src = '/Iscag/public/assets/images/user-profiles/' + encodeURIComponent(d.photo);
                profileImg.style.display = 'block';
                initials.style.display   = 'none';
                
                profileImg.onerror = () => {
                    profileImg.style.display = 'none';
                    initials.style.display   = 'flex';
                };
            } else {
                profileImg.style.display = 'none';
                initials.style.display   = 'flex';
            }

            // Status badge
            const statusEl = document.getElementById('modal-status-badge');
            const s        = (d.status || '').toLowerCase();
            let bClass     = 'badge-pending';
            if (['approved','active','verified'].includes(s))  bClass = 'badge-approved';
            else if (s === 'occupied')                         bClass = 'badge-occupied';
            else if (['rejected','inactive'].includes(s))      bClass = 'badge-rejected';

            statusEl.className      = 'badge ' + bClass;
            statusEl.textContent    = d.status || 'Pending';

            // Show/Hide Approval Actions in Footer
            const actionContainer = document.getElementById('modal-approval-actions');
            const isAdmin = (typeof activeRole !== 'undefined') ? (activeRole === ROLES.MIS_ADMIN || activeRole === ROLES.STAFF_ADMIN) : true;
            
            // Show buttons for Pending or similar initial states
            const showActions = ['pending', 'for review', 'submitted', 'new'].includes(s);

            if (showActions && isAdmin) {
                actionContainer.style.display = 'flex';
            } else {
                actionContainer.style.display = 'none';
            }
            // Reset rejection container if open
            document.getElementById('rejection-reason-container').style.display = 'none';

            // ── Document Checklist (User Request: Why unknown?) ──
            const docMap = [
                { id: 'gov-id',  val: d.hasGovId },
                { id: 'psa',     val: d.hasPsa },
                { id: 'nbi',     val: d.hasNbi },
                { id: 'income',  val: d.hasIncome },
                { id: 'picture', val: d.hasPicture }
            ];

            docMap.forEach(item => {
                const docEl = document.querySelector(`.doc-item[data-doc="${item.id}"]`);
                if (docEl) {
                    const badge = docEl.querySelector('.doc-badge');
                    const isSubmitted = (item.val == '1');

                    if (isSubmitted) {
                        badge.textContent = 'Submitted';
                        badge.style.background = 'rgba(23, 107, 69, 0.1)';
                        badge.style.color      = 'var(--primary-dark)';
                    } else {
                        badge.textContent = 'Missing';
                        badge.style.background = 'rgba(139, 46, 46, 0.1)';
                        badge.style.color      = 'var(--danger)';
                    }
                }
            });

            // Description
            const descBox = document.getElementById('modal-description');
            if (d.description && d.description.trim()) {
                descBox.textContent = d.description;
                descBox.classList.remove('empty');
            } else {
                descBox.textContent = 'No description provided.';
                descBox.classList.add('empty');
            }

            // ── Apartment Image ──
            const imgEl         = document.getElementById('modal-apt-image');
            const placeholder   = document.getElementById('modal-img-placeholder');

            if (d.image && d.image.trim()) {
                imgEl.src         = '/Iscag/public/assets/images/apartments/' + encodeURIComponent(d.image);
                imgEl.style.display = 'block';
                placeholder.style.display = 'none';

                imgEl.onerror = () => {
                    imgEl.style.display  = 'none';
                    placeholder.style.display = 'flex';
                };
            } else {
                imgEl.style.display       = 'none';
                placeholder.style.display = 'flex';
            }

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            
            // Store reference for print/actions
            modal.openedFromRow = row;
        }

        function closeModal() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        /* ─────────────────────────────────────────
           HELPERS
        ───────────────────────────────────────── */
        function setText(id, val) {
            const el = document.getElementById(id);
            if (!el) return;
            
            const isEmpty = !val || val === '—' || val.trim() === '';
            if (isEmpty) {
                el.textContent  = '—';
                el.classList.add('empty');
            } else {
                el.textContent  = val;
                el.classList.remove('empty');
            }
        }

        function escHtml(str) {
            return String(str)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        /* ─────────────────────────────────────────
           ROW CLICK EVENTS
        ───────────────────────────────────────── */
        allRows.forEach(row => {
            row.addEventListener('click', () => openModal(row));
        });

        /* ─────────────────────────────────────────
           CLOSE EVENTS
        ───────────────────────────────────────── */
        modalClose.addEventListener('click', closeModal);
        modalFooterCl.addEventListener('click', closeModal);

        // Click outside modal content to close
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // ESC key to close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        /* ─────────────────────────────────────────
           MODAL ACTIONS — Approve / Reject
        ───────────────────────────────────────── */
        document.getElementById('modal-btn-approve').addEventListener('click', function() {
            if (!modal.openedFromRow) return;
            const d = modal.openedFromRow.dataset;
            actionConfirm('Approve', d.id, d.name);
        });

        document.getElementById('modal-btn-reject').addEventListener('click', function() {
            const container = document.getElementById('rejection-reason-container');
            const isHidden = container.style.display === 'none';
            
            if (isHidden) {
                // Show rejection dropdown
                container.style.display = 'block';
                // Animation/Scroll
                container.style.animation = 'tModalSlide 0.3s ease';
                modal.scrollTo({ top: modal.scrollHeight, behavior: 'smooth' });
                
                // Change button text to "Confirm Rejection"
                this.innerHTML = `<svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> Confirm Rejection`;
                this.style.background = 'var(--danger)';
                this.style.color      = 'white';
            } else {
                // Already visible, proceed to commit if reason selected
                const reasonSelect = document.getElementById('modal-reject-reason-select');
                const reason = reasonSelect.value;
                const custom = document.getElementById('modal-reject-reason-custom').value;
                const finalReason = reason === 'Other' ? custom : reason;

                if (!reason) {
                    alert('Please select a reason for rejection.');
                    reasonSelect.style.borderColor = 'var(--danger)';
                    return;
                }

                if (!modal.openedFromRow) return;
                const d = modal.openedFromRow.dataset;
                
                if (confirm(`Are you sure you want to reject the application for ${d.name}?\nReason: ${finalReason}`)) {
                    window.location.href = `<?= url('/admin/staff_admin/apartment/reject') ?>?id=${d.id}&reason=${encodeURIComponent(finalReason)}`;
                }
            }
        });

        /* ─────────────────────────────────────────
           LIVE SEARCH + STATUS FILTER
        ───────────────────────────────────────── */
        function updateRowCount(visible) {
            const txt = visible + ' result' + (visible !== 1 ? 's' : '');
            if (rowCountText) rowCountText.textContent = txt;
            if (visibleBadge) visibleBadge.textContent = visible + ' shown';
        }

        function applyFilters() {
            const query  = (searchInput.value || '').toLowerCase().trim();
            const status = (filterStatus.value || '').toLowerCase().trim();
            let visible  = 0;

            allRows.forEach(row => {
                const name  = (row.dataset.name  || '').toLowerCase();
                const unit  = (row.dataset.unit  || '').toLowerCase();
                const type  = (row.dataset.type  || '').toLowerCase();
                const email = (row.dataset.email || '').toLowerCase();
                const rowStatus = (row.dataset.status || '').toLowerCase();

                const matchSearch = !query ||
                    name.includes(query)  ||
                    unit.includes(query)  ||
                    type.includes(query)  ||
                    email.includes(query);

                const matchStatus = !status || rowStatus === status;

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateRowCount(visible);

            // Show/hide empty state dynamically
            const noDataRow = tbody.querySelector('.empty-state-row');
            if (noDataRow) {
                noDataRow.style.display = visible === 0 ? '' : 'none';
            }

            // If no static empty and no visible rows, inject dynamic empty
            if (visible === 0 && !noDataRow && allRows.length > 0) {
                let dynamicEmpty = tbody.querySelector('.dynamic-empty-row');
                if (!dynamicEmpty) {
                    dynamicEmpty = document.createElement('tr');
                    dynamicEmpty.className = 'dynamic-empty-row';
                    dynamicEmpty.innerHTML = `<td colspan="6" style="text-align:center;padding:40px 20px;color:var(--text-muted);">No records match your search criteria.</td>`;
                    tbody.appendChild(dynamicEmpty);
                }
                dynamicEmpty.style.display = '';
            } else {
                const dynamicEmpty = tbody.querySelector('.dynamic-empty-row');
                if (dynamicEmpty) dynamicEmpty.style.display = 'none';
            }
        }

        if (searchInput)  searchInput.addEventListener('input', applyFilters);
        if (filterStatus) filterStatus.addEventListener('change', applyFilters);

        // Initial count
        updateRowCount(allRows.length);


        /* ─────────────────────────────────────────
           RECORDS TABLE — Action Handlers
        ───────────────────────────────────────── */

        // View Details button in actions
        window.viewTenantBtn = function(btn) {
            const row = btn.closest('tr');
            if (row) openModal(row);
        };

        // Approval Actions
        window.actionConfirm = function(action, id, name) {
            const confirmed = confirm(`Are you sure you want to ${action.toLowerCase()} application for ${name}?`);
            if (confirmed) {
                const actionPath = action.toLowerCase();
                window.location.href = `<?= url('/admin/staff_admin/apartment/') ?>${actionPath}?id=${id}`;
            }
        };

        // Archive
        window.archiveTenant = function(id, name) {
            const confirmed = confirm(`Are you sure you want to move ${name} to archive?`);
            if (confirmed) {
                window.location.href = `<?= url('/admin/staff_admin/apartment/archive') ?>?id=${id}`;
            }
        };

        /* ─────────────────────────────────────────
           PRINT INFORMATION Flow
        ───────────────────────────────────────── */
        window.printTenantInfo = function(id) {
            // Find the row data
            const row = tbody.querySelector(`.tenant-row[data-id="${id}"]`);
            if (!row) return;

            const d = row.dataset;
            const printArea = document.getElementById('printable-tenant-info');
            
            printArea.innerHTML = `
                <div class="print-header">
                    <h1 style="font-family:'Lora',serif; margin:0;">ISCAG APARTMENT DEPARTMENT</h1>
                    <p style="margin:5px 0; color:#666;">Official Tenant Information Record — ISCAG MIS System</p>
                    <p style="font-size:8pt; color:#999; margin-top:10px;">Generated on: ${new Date().toLocaleString()}</p>
                </div>
                
                <h2 style="font-family:'Lora',serif; border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:15px;">A. PERSONAL INFORMATION</h2>
                <div class="print-grid">
                    <div class="print-item"><span class="print-label">Full Name</span><span class="print-value">${d.name}</span></div>
                    <div class="print-item"><span class="print-label">Email Address</span><span class="print-value">${d.email}</span></div>
                    <div class="print-item"><span class="print-label">Contact Number</span><span class="print-value">${d.contact}</span></div>
                    <div class="print-item"><span class="print-label">Civil Status</span><span class="print-value">${d.civil}</span></div>
                    <div class="print-item"><span class="print-label">Occupation</span><span class="print-value">${d.occupation}</span></div>
                    <div class="print-item"><span class="print-label">Application Date</span><span class="print-value">${d.date}</span></div>
                </div>

                <h2 style="font-family:'Lora',serif; border-bottom:1px solid #333; padding-bottom:5px; margin-top:30px; margin-bottom:15px;">B. APARTMENT & UNIT DETAILS</h2>
                <div class="print-grid">
                    <div class="print-item"><span class="print-label">Unit Number</span><span class="print-value">${d.unit}</span></div>
                    <div class="print-item"><span class="print-label">Apartment Type</span><span class="print-value">${d.type}</span></div>
                    <div class="print-item"><span class="print-label">Floor / Location</span><span class="print-value">${d.floor}</span></div>
                    <div class="print-item"><span class="print-label">Current Status</span><span class="print-value">${d.status}</span></div>
                </div>

                <h2 style="font-family:'Lora',serif; border-bottom:1px solid #333; padding-bottom:5px; margin-top:30px; margin-bottom:15px;">C. UNIT DESCRIPTION</h2>
                <p style="font-size:10pt; line-height:1.5;">${d.description || 'No additional description provided.'}</p>

                <div style="margin-top:60px; display:grid; grid-template-columns:1fr 1fr; gap:50px;">
                    <div style="text-align:center;">
                        <div style="border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:5px;"></div>
                        <span style="font-size:9pt; font-weight:700;">TENANT SIGNATURE</span>
                    </div>
                    <div style="text-align:center;">
                        <div style="border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:5px;"></div>
                        <span style="font-size:9pt; font-weight:700;">DEPARTMENT HEAD / ADMINISTRATOR</span>
                    </div>
                </div>

                <div style="position:absolute; bottom:0; left:0; width:100%; border-top:1px dashed #ccc; padding-top:10px; font-size:8pt; text-align:center; color:#999;">
                    ISCAG - Islamic Studies, Call and Guidance Philippine Branch. This document is system-generated and for official departmental use only.
                </div>
            `;

            window.print();
        };

    })();

    /* ─── SUCCESS TOASTS ─── */
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        if (success) {
            let msg = '';
            let color = '#2f8a60';
            switch(success) {
                case 'approved': msg = '✓ Application Successfully Approved!'; break;
                case 'rejected': msg = '✓ Application Successfully Rejected.'; color = '#8b2e2e'; break;
                case 'archived': msg = '✓ Tenant Moved to Archive.'; break;
            }
            if (msg && typeof showToast === 'function') {
                showToast(msg, color);
                // Clean up URL parameters to keep it clean
                const cleanUrl = window.location.href.split('?')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }

        const error = urlParams.get('error');
        if (error) {
            let errorMsg = 'An error occurred. Please try again.';
            if (error === 'not_found') errorMsg = 'Record not found. Status was not updated.';
            if (error === 'db_error') errorMsg = 'Database error. Please contact MIS.';
            if (error === 'invalid_token') errorMsg = 'Security session expired. Please refresh and try again.';
            
            if (typeof showToast === 'function') {
                showToast('✖ ' + errorMsg, '#8b2e2e');
                const cleanUrl = window.location.href.split('?')[0];
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }
    });
</script>
</body>
</html>
