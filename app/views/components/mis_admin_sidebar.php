<?php
/**
 * ISCAG MIS — Unified Admin Sidebar
 * Bridges the gap between the premium Tenant UX and the hardcoded MIS Admin modules.
 */
$admin_name_parts = explode(' ', trim($_SESSION['name'] ?? 'Admin'));
$initials = strtoupper(substr($admin_name_parts[0], 0, 1) . (count($admin_name_parts) > 1 ? substr(end($admin_name_parts), 0, 1) : ''));
$active_page = $active_page ?? 'admin_dashboard';

// Determine which dropdowns should be open based on the active page
$is_apart_open = in_array($active_page, ['apartment_records', 'tenant_confirmation', 'parking_approval']);
$is_finance_open = in_array($active_page, ['billing', 'soa', 'reports']);
$is_comm_open = in_array($active_page, ['daawah_records', 'damayan_records', 'notifications']);
$is_gov_open = in_array($active_page, ['records', 'audit_logs', 'notification']);
?>
<aside class="sidebar" id="sidebar">
    <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
        <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" /></svg>
    </button>
    
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
            <div class="brand-text"><strong>ISCAG MIS</strong><span>Admin Portal</span></div>
        </div>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar" style="background:var(--primary-light); width:32px; height:32px; font-size:0.75rem;">
            <?= htmlspecialchars($initials) ?>
        </div>
        <div class="user-info">
            <strong style="font-size:0.85rem;"><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></strong>
            <span style="color:var(--accent); font-size: 0.68rem; font-weight: 700; text-transform: uppercase;"><?= htmlspecialchars($_SESSION['role'] ?? 'Administrator') ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="<?= url('/admin/dashboard') ?>" class="nav-item <?= $active_page === 'admin_dashboard' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            <span class="nav-item-label">Hub Overview</span>
        </a>

        <!-- OPERATIONS (Apartment Hub) -->
        <div class="nav-section-label">Operations</div>
        <div class="nav-dropdown-wrap" id="apart-wrap">
            <button class="nav-dropdown-trigger <?= $is_apart_open ? 'open' : '' ?>" id="apart-trigger" data-href="<?= url('/admin/mis_admin/apartment_records') ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                <span class="nav-item-label">Apartment Mgmt</span>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
            </button>
            <div class="nav-dropdown <?= $is_apart_open ? 'open' : '' ?>" id="apart-menu">
                <a href="<?= url('/admin/mis_admin/apartment_records') ?>" class="<?= $active_page === 'apartment_records' ? 'active-submenu' : '' ?>">Unit Inventory</a>
                <a href="<?= url('/admin/mis_admin/tenant_confirmation') ?>" class="<?= $active_page === 'tenant_confirmation' ? 'active-submenu' : '' ?>">Tenant Approvals</a>
                <a href="<?= url('/admin/mis_admin/parking_approval') ?>" class="<?= $active_page === 'parking_approval' ? 'active-submenu' : '' ?>">Parking Alloc.</a>
            </div>
        </div>

        <!-- FINANCE (Financial Control) -->
        <div class="nav-dropdown-wrap" id="finance-wrap">
            <button class="nav-dropdown-trigger <?= $is_finance_open ? 'open' : '' ?>" id="finance-trigger" data-href="<?= url('/admin/mis_admin/billing') ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h9zm-9-2h10V7H12v9zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                <span class="nav-item-label">Financial Control</span>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
            </button>
            <div class="nav-dropdown <?= $is_finance_open ? 'open' : '' ?>" id="finance-menu">
                <a href="<?= url('/admin/mis_admin/billing') ?>" class="<?= $active_page === 'billing' ? 'active-submenu' : '' ?>">Billing & Payments</a>
                <a href="<?= url('/admin/mis_admin/statement_of_account') ?>" class="<?= $active_page === 'soa' ? 'active-submenu' : '' ?>">SOA Records</a>
                <a href="<?= url('/admin/mis_admin/reports') ?>" class="<?= $active_page === 'reports' ? 'active-submenu' : '' ?>">Revenue Reports</a>
            </div>
        </div>

        <!-- COMMUNITY (Services) -->
        <div class="nav-dropdown-wrap" id="comm-wrap">
            <button class="nav-dropdown-trigger <?= $is_comm_open ? 'open' : '' ?>" id="comm-trigger" data-href="<?= url('/admin/mis_admin/daawah_records') ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                <span class="nav-item-label">Community Hub</span>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
            </button>
            <div class="nav-dropdown <?= $is_comm_open ? 'open' : '' ?>" id="comm-menu">
                <a href="<?= url('/admin/mis_admin/daawah_records') ?>" class="<?= $active_page === 'daawah_records' ? 'active-submenu' : '' ?>">Da'wah Records</a>
                <a href="<?= url('/admin/mis_admin/damayan_records') ?>" class="<?= $active_page === 'damayan_records' ? 'active-submenu' : '' ?>">Damayan Burial</a>
                <a href="<?= url('/admin/mis_admin/notifications') ?>" class="<?= $active_page === 'notifications' ? 'active-submenu' : '' ?>">System Broadcast</a>
            </div>
        </div>

        <!-- GOVERNANCE DROPDOWN -->
        <div class="nav-section-label">Governance</div>
        <div class="nav-dropdown-wrap" id="gov-wrap">
            <button class="nav-dropdown-trigger <?= $is_gov_open ? 'open' : '' ?>" id="gov-trigger" data-href="<?= url('/admin/mis_admin/records') ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 6c1.4 0 2.5 1.1 2.5 2.5S13.4 12 12 12s-2.5-1.1-2.5-2.5S10.6 7 12 7zm0 14c-2.7 0-5.8-1.3-7.5-3.6.1-2.1 4.5-3.2 7.5-3.2s7.4 1.1 7.5 3.2c-1.7 2.3-4.8 3.6-7.5 3.6z"/></svg>
                <span class="nav-item-label">System Control</span>
                <svg class="nav-dropdown-arrow" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z" /></svg>
            </button>
            <div class="nav-dropdown <?= $is_gov_open ? 'open' : '' ?>" id="gov-menu">
                <a href="<?= url('/admin/mis_admin/records') ?>" class="<?= $active_page === 'records' ? 'active-submenu' : '' ?>">User Management</a>
                <a href="<?= url('/admin/mis_admin/audit_logs') ?>" class="<?= $active_page === 'audit_logs' ? 'active-submenu' : '' ?>">Audit Trails</a>
                <a href="<?= url('/admin/mis_admin/notification') ?>" class="<?= $active_page === 'notification' ? 'active-submenu' : '' ?>">Admin Inbox</a>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= url('/logout') ?>" class="nav-item">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            <span class="nav-item-label">Logout</span>
        </a>
    </div>
</aside>
