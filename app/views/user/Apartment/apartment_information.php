<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$userId = $_SESSION['user_id'] ?? null;
$tenantInfo = null;
$application = null;

if ($userId) {
    require_once BASE_PATH . '/app/models/ApartmentApp.php';
    $aptModel = new ApartmentApp();
    $tenantInfo = $aptModel->getInfo($userId);
    $application = $aptModel->getApplication($userId);
}

// Ensure variables from controller are handled
$familyCount = $familyCount ?? 0;
$hasParking = $hasParking ?? false;
?>
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
       INFO DASHBOARD LAYOUT (MODERNIZED)
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

        .status-badge.approved { background: rgba(47, 138, 96, 0.2); color: #7ee8b0; border: 1px solid rgba(47, 138, 96, 0.3); }

        /* Status summary bar */
        .status-summary {
            padding: 18px 32px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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

        /* ── Info Section Cards ── */
        .info-section {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .info-field {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
            transition: background 0.15s;
        }

        .info-field:hover { background: rgba(23, 107, 69, 0.015); }

        .info-grid .info-field:nth-child(even) { border-right: none; }

        .info-field.full-width { grid-column: 1 / -1; border-right: none; }

        .info-field:last-child, .info-grid .info-field:nth-last-child(-n+2) { border-bottom: none; }

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

        .info-field-label svg { width: 12px; height: 12px; fill: var(--accent); }

        .info-field-value { font-size: 0.9rem; font-weight: 600; color: var(--text-main); line-height: 1.4; }

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
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.18s;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-action.primary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: white;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.25);
        }

        /* ── Dynamic Modal Styles ── */
        .apt-modal-overlay {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(15, 30, 22, 0.5); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px; opacity: 0; pointer-events: none; transition: all 0.3s ease;
        }
        .apt-modal-overlay.active { opacity: 1; pointer-events: auto; }
        .apt-modal {
            background: white; border-radius: 20px; width: 100%; max-width: 850px;
            max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transform: translateY(20px); transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .apt-modal-overlay.active .apt-modal { transform: translateY(0); }

        .apt-modal-header {
            padding: 20px 24px; background: white; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .apt-modal-body { padding: 24px; overflow-y: auto; flex: 1; display: grid; grid-template-columns: 1fr 320px; gap: 24px; }
        
        .apt-card { background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .apt-card-title { font-family: 'Lora', serif; font-size: 0.95rem; font-weight: 700; color: var(--primary-dark); margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        .apt-card-title svg { width: 18px; fill: var(--accent); }

        .apt-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 12px; }
        .apt-list-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.88rem; color: #475569; line-height: 1.5; }
        .apt-list-item::before { content: "•"; color: var(--primary); font-weight: 800; font-size: 1.2rem; line-height: 1; }
        .apt-list-item.rule::before { content: "⚠"; color: var(--danger); font-size: 0.9rem; margin-top: 2px; }

        .m-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; border: 1px solid transparent; }

        /* Empty State */
        .empty-state-card { background: white; border-radius: 16px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
        .empty-state-hero { padding: 48px 32px; text-align: center; background: #f8fafc; }
        .empty-state-hero svg { width: 64px; height: 64px; fill: var(--text-muted); opacity: 0.3; margin-bottom: 16px; }
        .empty-state-hero h3 { font-family: 'Lora', serif; color: var(--primary-dark); margin: 0 0 8px; }
        .empty-state-hero p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
        .empty-state-body { padding: 24px; text-align: center; border-top: 1px solid var(--border); }
        .btn-action.secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-action.secondary:hover { background: #e2e8f0; }

        /* Maintenance Specific */
        .maintenance-modal {
            max-width: 500px !important;
        }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); font-family: inherit; font-size: 0.9rem; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1); }
    </style>
</head>

<body>
    <div class="app-wrapper">
        <?php 
          $active_page = 'apartment_info'; 
          include BASE_PATH . '/app/views/user/sidebar.php'; 
        ?>

        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Apartment Information</div>
                    <div class="top-bar-subtitle">Dynamic unit management and real-time rules</div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">← Back to Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                <div id="tenant-info-root">
                    <div style="text-align:center; padding:100px;">
                        <div class="loader" style="margin:0 auto 20px;"></div>
                        <p style="color:var(--text-muted);">Syncing apartment configuration...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: APARTMENT DETAILS ═══ -->
    <div class="apt-modal-overlay" id="aptDetailModal">
        <div class="apt-modal">
            <div class="apt-modal-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <h3 id="m-unit-name" style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800;">Unit Details</h3>
                    <span class="m-badge" id="m-availability-badge">Available</span>
                </div>
                <button onclick="closeAptModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:var(--text-muted);">&times;</button>
            </div>
            <div class="apt-modal-body">
                <div class="apt-main-col">
                    <div class="apt-card">
                        <h4 class="apt-card-title"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg> Room Inclusions</h4>
                        <ul class="apt-list" id="m-list-features"></ul>
                    </div>
                    <div class="apt-card">
                        <h4 class="apt-card-title"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-2h-2v2zm0-4h2V7h-2v6z"/></svg> House Rules & Policies</h4>
                        <ul class="apt-list" id="m-list-rules"></ul>
                    </div>
                </div>
                <div class="apt-side-col">
                    <div class="apt-card" style="background:var(--primary-dark); color:white; border:none;">
                        <h4 class="apt-card-title" style="color:white; border-bottom-color:rgba(255,255,255,0.1);"><svg viewBox="0 0 24 24" style="fill:white;"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg> Payment & Terms</h4>
                        <div style="font-size:1.5rem; font-weight:800; margin-bottom:20px;" id="m-rent">₱0.00</div>
                        <ul class="apt-list" id="m-list-payment" style="gap:10px;"></ul>
                    </div>
                    <div class="apt-card">
                        <h4 class="apt-card-title"><svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg> Lease Schedule</h4>
                        <ul class="apt-list" id="m-list-lease"></ul>
                    </div>
                </div>
            </div>
            <div style="padding:16px 24px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; background:#f9fafb;">
                <button onclick="closeAptModal()" class="btn-action primary" style="padding:8px 24px;">Close Overview</button>
            </div>
        </div>
    </div>

    <!-- ═══ MODAL: MAINTENANCE REQUEST ═══ -->
    <div class="apt-modal-overlay" id="maintenanceModal">
        <div class="apt-modal maintenance-modal">
            <div class="apt-modal-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <h3 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800;">Request Maintenance</h3>
                </div>
                <button onclick="closeMaintenanceModal()" style="background:none; border:none; font-size:24px; cursor:pointer; color:var(--text-muted);">&times;</button>
            </div>
            <form id="maintenanceForm" onsubmit="submitMaintenance(event)">
                <div style="padding: 24px;">
                    <div class="form-group">
                        <label class="form-label">Type of Issue</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select a category</option>
                            <option value="Plumbing">Plumbing</option>
                            <option value="Electrical">Electrical</option>
                            <option value="Structural">Structural</option>
                            <option value="Appliance">Appliance</option>
                            <option value="Pest Control">Pest Control</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description / Reason</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe the issue clearly..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Attachment (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept="image/*">
                    </div>
                </div>
                <div style="padding:16px 24px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:12px; background:#f9fafb;">
                    <button type="button" onclick="closeMaintenanceModal()" class="btn-action secondary">Cancel</button>
                    <button type="submit" class="btn-action primary" id="maintenanceSubmitBtn">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const root = document.getElementById('tenant-info-root');
        let assignedApt = null;
        let apartmentTypes = [];

        async function init() {
            try {
                const res = await fetch('<?= url("/api/apartment-types") ?>').then(r => r.json());
                if (res.success) apartmentTypes = res.data;

                const tenantInfo = <?= json_encode($tenantInfo ?? null) ?>;
                const aptApp = <?= json_encode($application ?? null) ?>;
                const familyCount = <?= json_encode($familyCount ?? 0) ?>;
                const hasParking = <?= json_encode($hasParking ?? false) ?>;
                
                // Determine logic for "Automatic Queue"
                if (aptApp && (aptApp.status.toLowerCase() === 'assigned' || aptApp.status.toLowerCase() === 'verified' || aptApp.status.toLowerCase() === 'occupied')) {
                    const type = apartmentTypes.find(t => t.type_key === aptApp.roomtype) || apartmentTypes[0];
                    assignedApt = { ...type, ...aptApp, displayStatus: 'Your Unit', familyCount, hasParking };
                } else if (aptApp && aptApp.status.toLowerCase() === 'queued') {
                    const type = apartmentTypes.find(t => t.type_key === aptApp.roomtype) || apartmentTypes[0];
                    assignedApt = { ...type, ...aptApp, displayStatus: `Waitlisted (Pos #${aptApp.queue_position})`, familyCount, hasParking };
                }

                renderDashboard(aptApp);
            } catch (err) {
                console.error(err);
                root.innerHTML = '<p style="text-align:center; padding:40px; color:var(--danger);">Failed to load apartment data. Please refresh.</p>';
            }
        }

        function renderDashboard(aptApp) {
            if (!assignedApt) {
                if (aptApp) {
                    const isQueued = aptApp.status === 'Queued';
                    root.innerHTML = `<div class="empty-state-card"><div class="empty-state-hero" style="background: linear-gradient(135deg, var(--accent), #d4a83a);"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg><h3>${isQueued ? 'Waitlisted' : 'Application in Review'}</h3><p>${isQueued ? `Position #${aptApp.queue_position} on the waitlist.` : 'Under Admin Review.'}</p></div></div>`;
                } else {
                    root.innerHTML = `<div class="empty-state-card"><div class="empty-state-hero"><svg viewBox="0 0 24 24"><path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"/></svg><h3>No Apartment Assigned</h3><p>Apply for a unit to see details here.</p></div><div class="empty-state-body"><a href="<?= url('/user/apartment/apply') ?>" class="btn-action primary">Apply Now</a></div></div>`;
                }
                return;
            }

            root.innerHTML = `
                <div class="status-hero">
                    <div class="status-hero-top">
                        <div class="status-hero-header">
                            <div class="status-hero-header-left">
                                <div class="status-hero-avatar"><svg viewBox="0 0 24 24" style="width:28px;fill:white;"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z"/></svg></div>
                                <div><h2 class="status-hero-name">${assignedApt.label || assignedApt.name}</h2><p class="status-hero-subtitle">Assigned Unit: ${assignedApt.room_number || 'ISCAG Compound'}</p></div>
                            </div>
                            <div class="status-badge approved"><span class="status-badge-dot"></span>Active Unit</div>
                        </div>
                    </div>
                    <div class="status-summary">
                        <div class="summary-stat"><div class="summary-stat-label">Monthly Rent</div><div class="summary-stat-value">₱${Number(assignedApt.price || assignedApt.rent).toLocaleString()}</div></div>
                        <div class="summary-stat"><div class="summary-stat-label">Security Deposit</div><div class="summary-stat-value">₱1,000</div></div>
                        <div class="summary-stat">
                            <div class="summary-stat-label">Queue / Status</div>
                            <div class="summary-stat-value" style="color:var(--accent);">${assignedApt.displayStatus}</div>
                        </div>
                        <div class="summary-stat"><div class="summary-stat-label">Due Date</div><div class="summary-stat-value">Every 5th</div></div>
                        <div class="summary-stat"><div class="summary-stat-label">Type</div><div class="summary-stat-value">${assignedApt.label}</div></div>
                    </div>
                </div>

                <div class="action-bar">
                    <div class="action-bar-text"><h4>View Full Unit Specifications</h4><p>Check room inclusions, house rules, and detailed lease terms.</p></div>
                    <div class="action-bar-btns" style="display:flex; gap:12px;">
                        <button class="btn-action secondary" onclick="openMaintenanceModal()">
                            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                            Maintenance
                        </button>
                        <button class="btn-action primary" onclick="openAptDetails()">View Unit Modal</button>
                    </div>
                </div>
            `;
        }

        function openAptDetails() {
            if (!assignedApt) return;
            document.getElementById('m-unit-name').textContent = assignedApt.label;
            document.getElementById('m-rent').textContent = `₱${Number(assignedApt.price).toLocaleString()} / Month`;
            
            const badge = document.getElementById('m-availability-badge');
            badge.textContent = assignedApt.displayStatus;
            
            const isWaitlisted = assignedApt.displayStatus.includes('Waitlisted');
            badge.style.background = isWaitlisted ? '#fffbeb' : '#ecfdf5';
            badge.style.color = isWaitlisted ? '#92400e' : '#059669';
            badge.style.borderColor = isWaitlisted ? '#fde68a' : '#10b98133';

            const features = assignedApt.inclusions ? (typeof assignedApt.inclusions === 'string' ? JSON.parse(assignedApt.inclusions) : assignedApt.inclusions) : [];
            document.getElementById('m-list-features').innerHTML = features.length ? features.map(f => `<li class="apt-list-item">${f}</li>`).join('') : '<li class="apt-list-item" style="color:#94a3b8;">No inclusions specified</li>';

            const rules = assignedApt.rules ? (typeof assignedApt.rules === 'string' ? JSON.parse(assignedApt.rules) : assignedApt.rules) : [];
            document.getElementById('m-list-rules').innerHTML = rules.length ? rules.map(r => `<li class="apt-list-item rule">${r}</li>`).join('') : '<li class="apt-list-item" style="color:#94a3b8;">No rules specified</li>';

            const formattedPrice = Number(assignedApt.price).toLocaleString();
            document.getElementById('m-list-payment').innerHTML = `
                <li class="apt-list-item" style="color:white; opacity:0.9;"><strong>Initial:</strong> Advance (₱${formattedPrice} — ${assignedApt.advance_rent || '1 Month'})</li>
                <li class="apt-list-item" style="color:white; opacity:0.9;"><strong>Initial:</strong> Deposit (₱1,000)</li>
                <li class="apt-list-item" style="color:white; opacity:0.9;"><strong>Monthly:</strong> Contribution (₱150)</li>
                <li class="apt-list-item" style="color:white; opacity:0.9;"><strong>Monthly:</strong> Water (₱${(assignedApt.familyCount + 1) * 100})</li>
                ${assignedApt.hasParking ? `<li class="apt-list-item" style="color:white; opacity:0.9;"><strong>Monthly:</strong> Parking (₱1,000)</li>` : ''}
            `;

            document.getElementById('m-list-lease').innerHTML = `
                <li class="apt-list-item">Min Stay: 3 Months</li>
                <li class="apt-list-item">Notice: 25th day</li>
            `;

            document.getElementById('aptDetailModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeAptModal() {
            document.getElementById('aptDetailModal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openMaintenanceModal() {
            document.getElementById('maintenanceModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMaintenanceModal() {
            document.getElementById('maintenanceModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            document.getElementById('maintenanceForm').reset();
        }

        async function submitMaintenance(e) {
            e.preventDefault();
            const btn = document.getElementById('maintenanceSubmitBtn');
            const form = document.getElementById('maintenanceForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.textContent = 'Submitting...';

            try {
                const res = await fetch('<?= url("/user/apartment/maintenance/submit") ?>', {
                    method: 'POST',
                    body: formData
                }).then(r => r.json());

                if (res.success) {
                    alert('Maintenance request submitted successfully!');
                    closeMaintenanceModal();
                } else {
                    alert(res.message || 'Failed to submit request');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Submit Request';
            }
        }

        init();
    </script>
</body>
</html>