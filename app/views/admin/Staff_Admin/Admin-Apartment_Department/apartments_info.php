<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Apartment Management</title>
    <meta name="description"
        content="Manage and view detailed information regarding apartment buildings and locations." />
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
    <style>
        /* Form Field Styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.9rem;
            color: var(--text-main);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 4px;
            display: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .actions-cell {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* Role-based display */
        .only-staff {
            display: inline-flex;
        }

        .only-mis {
            display: none;
        }

        body.mis-admin-mode .only-staff {
            display: none !important;
        }

        body.mis-admin-mode .only-mis {
            display: inline-flex !important;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!----sidebar---->
        <aside class="sidebar" id="sidebar">
            <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar"><svg viewBox="0 0 24 24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
                </svg></button>
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
                    <div class="brand-text"><strong>ISCAG MIS</strong><span>Apartment Staff</span></div>
                </div>
            </div>
            <div class="sidebar-user">
                <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">AK</div>
                <div class="user-info"><strong id="nav-name">Apartment Staff</strong><span>Staff Admin</span></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-label">Admin</div>
                <a href="<?= url('/admin/apartment') ?>" class="nav-item" data-tooltip="Dashboard">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
                    </svg>
                    <span class="nav-item-label">Dashboard</span>
                </a>
                <a href="<?= url('/admin/apartment/profile') ?>" class="nav-item" data-tooltip="Profile">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                    </svg>
                    <span class="nav-item-label">My Profile</span>
                </a>
                <a href="<?= url('/admin/apartment/confirmation') ?>" class="nav-item" data-tooltip="Applications">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                    </svg>
                    <span class="nav-item-label">Review Applications</span>
                </a>
                <div class="nav-section-label">Management</div>
                <a href="<?= url('/admin/apartment/info') ?>" class="nav-item active" data-tooltip="Apartment Info">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
                    </svg>
                    <span class="nav-item-label">Apartment Info</span>
                </a>
                <a href="<?= url('/admin/payment') ?>" class="nav-item" data-tooltip="Billing & Payment">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                    </svg>
                    <span class="nav-item-label">Billing & Payment</span>
                </a>
                <a href="<?= url('/admin/apartment/soa') ?>" class="nav-item" data-tooltip="SOA">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM9 13h6v2H9v-2zm6 4H9v2h6v-2z" />
                    </svg>
                    <span class="nav-item-label">Statement of Account</span>
                </a>
                <a href="<?= url('/admin/apartment/notifications') ?>" class="nav-item" data-tooltip="Notifications">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                    </svg>
                    <span class="nav-item-label">Notifications</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
                    </svg>
                    <span class="nav-item-label">Logout</span>
                </a>
            </div>
        </aside>
        <!-- ═══ MAIN CONTENT ═══ -->
        <div class="main-content">
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Apartment Information Management</div>
                    <div class="top-bar-subtitle">Create, view, and manage apartment records & buildings.</div>
                </div>
                <div class="top-bar-actions" id="topbar-actions-block">
                    <button class="btn-topbar primary" id="btn-add-apt" onclick="openAptModal('add')">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        Add Apartment
                    </button>
                </div>
            </div>

            <div class="page-body">
                <div class="breadcrumb-bar">
                    <a href="<?= url('/admin/apartment') ?>">Apartment Department</a>
                    <span class="sep">›</span>
                    <span class="current">Apartments Information</span>
                </div>

                <!-- RESTRICTION BANNER -->
                <div class="restriction-banner" style="margin-bottom: 24px;" id="role-banner">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
                    </svg>
                    <div>
                        <strong id="banner-title">Apartment Admin Mode</strong> — Manage apartment location records. New
                        entries are subject to MIS Admin approval before they become active.
                    </div>
                </div>

                <!-- APARTMENTS RECORD TABLE -->
                <div class="section-card">
                    <div class="section-card-header">
                        <h6>
                            <svg viewBox="0 0 24 24">
                                <path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z" />
                            </svg>
                            Registered Apartments
                        </h6>
                    </div>
                    <div class="section-card-body" style="padding:0;">
                        <div class="table-wrapper">
                            <table class="mis-table">
                                <thead>
                                    <tr>
                                        <th>Apartment ID</th>
                                        <th>Application ID</th>
                                        <th>Room Number</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="apt-tbody">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══ ADD/EDIT APARTMENT MODAL ═══ -->
    <div class="modal-backdrop" id="apt-modal" style="display:none;">
        <div class="modal-content" style="max-width:600px;">
            <div class="modal-bar"></div>
            <div class="modal-header">
                <h5 id="apt-modal-title">
                    <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                    <span id="apt-modal-label">Add Apartment Record</span>
                </h5>
                <button class="modal-close" onclick="closeModal('apt-modal')">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="form-mode" value="add">
                <input type="hidden" id="form-apt-id" value="">

                <div class="form-row">
                    <div class="form-group full">
                        <label class="form-label">Application ID</label>
                        <select class="form-control" id="f-application-id">
                            <option value="">— Unassigned / No Application —</option>
                            <option value="APP-1001">APP-1001 (Muhammad Usman)</option>
                            <option value="APP-1002">APP-1002 (Ahmad Khalil)</option>
                            <option value="APP-1003">APP-1003 (Fatima Zahra)</option>
                            <option value="APP-1004">APP-1004 (Yusuf Ali)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label class="form-label">Room Number *</label>
                        <input type="text" class="form-control" id="f-roomnumber" placeholder="e.g. 101-A" required>
                        <div class="error-message" id="err-roomnumber">Room Number is required.</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" id="f-desc" placeholder="Details about this apartment unit"
                            required></textarea>
                        <div class="error-message" id="err-desc">Description is required.</div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="f-status">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-topbar" onclick="closeModal('apt-modal')">Cancel</button>
                <button class="btn-topbar primary" id="btn-save-apt" onclick="saveAptForm()">Save Apartment</button>
            </div>
        </div>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        standardizePage('staff');
        // ══ INIT ROLE ══
        // Check role dynamically: Assume MIS_ADMIN or STAFF_ADMIN 
        // We read from localStorage or default to STAFF_ADMIN for this specific page test
        const savedRole = localStorage.getItem('mis_current_role') || ROLES.STAFF_ADMIN;
        window.currentUserRole = savedRole;
        setCurrentRole(savedRole);

        if (savedRole === ROLES.MIS_ADMIN) {
            document.body.classList.add('mis-admin-mode');
            document.getElementById('sidebar-role-name').textContent = "MIS Admin";
            document.getElementById('banner-title').textContent = "MIS Admin Mode";
            document.querySelector('.restriction-banner div').innerHTML = "<strong>MIS Admin Mode</strong> — You have access to review, approve, or reject new Apartment entries submitted by the Apartment Staff.";
        } else {
            document.body.classList.remove('mis-admin-mode');
        }

        // ══ DATA MANAGEMENT ══
        function getApartmentRecords() {
            if (!localStorage.getItem('mis_apartment_records')) {
                // Initialize default dummy if empty
                const initial = [
                    {
                        apartment_id: "APTREC-001",
                        application_id: "APP-1001",
                        roomnumber: "101-A",
                        description: "Main residential block unit.",
                        status: "Active"
                    },
                    {
                        apartment_id: "APTREC-002",
                        application_id: "APP-1002",
                        roomnumber: "205-B",
                        description: "New extension with 2-bedroom family unit.",
                        status: "Pending"
                    }
                ];
                localStorage.setItem('mis_apartment_records', JSON.stringify(initial));
            }
            return JSON.parse(localStorage.getItem('mis_apartment_records'));
        }

        function saveApartmentRecords(records) {
            localStorage.setItem('mis_apartment_records', JSON.stringify(records));
        }

        function logAudit(actionDesc, actionType) {
            const logs = JSON.parse(localStorage.getItem('mis_audit_logs') || '[]');
            const roleStr = savedRole === ROLES.MIS_ADMIN ? 'MIS_ADMIN' : 'APT_ADMIN';

            logs.push({
                admin_id: roleStr,
                action: actionType,
                module: 'APARTMENT',
                description: actionDesc,
                ip_address: '192.168.1.' + Math.floor(Math.random() * 255), // Mocked IP
                timestamp: new Date().toISOString()
            });
            localStorage.setItem('mis_audit_logs', JSON.stringify(logs));
        }

        // ══ RENDER TABLE ══
        function renderTable() {
            const records = getApartmentRecords();
            const tbody = document.getElementById('apt-tbody');

            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">No apartment records found.</td></tr>';
                return;
            }

            tbody.innerHTML = records.map(r => {
                let badge = 'badge-reserved';
                if (r.status === 'Pending') badge = 'badge-pending';
                if (r.status === 'Approved' || r.status === 'Active') badge = 'badge-approved';
                if (r.status === 'Occupied') badge = 'badge-approved';
                if (r.status === 'Inactive' || r.status === 'Rejected') badge = 'badge-rejected';

                let actions = `<div class="actions-cell">`;

                if (savedRole === ROLES.STAFF_ADMIN || savedRole === ROLES.MIS_ADMIN) {
                    // Both Admins share full control
                    actions += `<button class="btn-action btn-view" onclick="openAptModal('view', '${r.apartment_id}')" title="View Record"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>`;


                    if (r.status === 'Pending') {
                        actions += `<button class="btn-action btn-approve" onclick="approveApt('${r.apartment_id}')" title="Approve"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></button>`;
                        actions += `<button class="btn-action btn-reject" onclick="rejectApt('${r.apartment_id}')" title="Reject"><svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>`;
                    } else if (r.status === 'Approved' || r.status === 'Active') {
                        actions += `<button class="btn-action btn-approve" onclick="toggleStatus('${r.apartment_id}', 'Occupied')" title="Mark Occupied"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm2-4H9V7h4v5z"/></svg></button>`;
                    }

                    if (r.status !== 'Inactive' && r.status !== 'Pending' && r.status !== 'Rejected') {
                        actions += `<button class="btn-action btn-reject" onclick="toggleStatus('${r.apartment_id}', 'Inactive')" title="Set Inactive"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/></svg></button>`;
                    }
                    if (r.status === 'Inactive') {
                        actions += `<button class="btn-action btn-approve" onclick="toggleStatus('${r.apartment_id}', 'Approved')" title="Re-activate / Approve"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg></button>`;
                    }
                } else {
                    actions += `<button class="btn-action btn-view" onclick="openAptModal('view', '${r.apartment_id}')" title="View"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>`;
                }

                actions += `</div>`;

                return `
                    <tr>
                        <td class="td-id">${r.apartment_id}</td>
                        <td style="font-weight:700;">${r.application_id || '—'}</td>
                        <td style="font-size:0.85rem; font-weight:700;">${r.roomnumber || '—'}</td>
                        <td style="font-size:0.85rem;">${r.description || '—'}</td>
                        <td><span class="badge-status ${badge}">${r.status}</span></td>
                        <td>${actions}</td>
                    </tr>
                `;
            }).join('');
        }

        // ══ ACTIONS ══
        function openAptModal(mode, id = null) {
            document.querySelectorAll('.error-message').forEach(e => e.style.display = 'none');
            const modalTitle = document.getElementById('apt-modal-label');
            const fAppId = document.getElementById('f-application-id');
            const fRoom = document.getElementById('f-roomnumber');
            const fDesc = document.getElementById('f-desc');
            const fStatus = document.getElementById('f-status');
            const btnSave = document.getElementById('btn-save-apt');

            document.getElementById('form-mode').value = mode;
            document.getElementById('form-apt-id').value = id || '';

            // Reset disables
            fAppId.disabled = false;
            fRoom.disabled = false;
            fDesc.disabled = false;
            btnSave.style.display = 'inline-flex';

            if (mode === 'add') {
                modalTitle.textContent = "Add New Apartment";
                fAppId.value = '';
                fRoom.value = '';
                fDesc.value = '';
                fStatus.value = 'Pending';
            } else if (mode === 'edit' || mode === 'view') {
                modalTitle.textContent = mode === 'edit' ? "Edit Apartment Record" : "View Apartment Record";
                const records = getApartmentRecords();
                const apt = records.find(r => r.apartment_id === id);
                if (apt) {
                    fAppId.value = apt.application_id || '';
                    fRoom.value = apt.roomnumber || '';
                    fDesc.value = apt.description || '';
                    fStatus.value = apt.status || 'Pending';
                }

                if (mode === 'view') {
                    fAppId.disabled = true;
                    fRoom.disabled = true;
                    fDesc.disabled = true;
                    fStatus.disabled = true;
                    btnSave.style.display = 'none';
                }
            }
            openModal('apt-modal');
        }

        function saveAptForm() {
            // Validation
            let isValid = true;
            const appId = document.getElementById('f-application-id').value.trim();
            const room = document.getElementById('f-roomnumber').value.trim();
            const desc = document.getElementById('f-desc').value.trim();
            const mode = document.getElementById('form-mode').value;
            const id = document.getElementById('form-apt-id').value;

            if (!room) { document.getElementById('err-roomnumber').style.display = 'block'; isValid = false; } else { document.getElementById('err-roomnumber').style.display = 'none'; }
            if (!desc) { document.getElementById('err-desc').style.display = 'block'; isValid = false; } else { document.getElementById('err-desc').style.display = 'none'; }

            if (!isValid) return;

            let records = getApartmentRecords();

            if (mode === 'add') {
                const newId = "APTREC-" + String(records.length + 1).padStart(3, '0');
                const newStatus = document.getElementById('f-status').value || 'Pending';
                records.push({
                    apartment_id: newId,
                    application_id: appId,
                    roomnumber: room,
                    description: desc,
                    status: newStatus
                });
                logAudit(`Created new apartment record: ${room} as ${newStatus}`, 'CREATE');
                showToast(`✅ Apartment created.`, 'var(--success)');
            } else if (mode === 'edit') {
                const index = records.findIndex(r => r.apartment_id === id);
                if (index !== -1) {
                    records[index].application_id = appId;
                    records[index].roomnumber = room;
                    records[index].description = desc;
                    records[index].status = document.getElementById('f-status').value;
                    logAudit(`Updated apartment record: ${id}`, 'UPDATE');
                    showToast(`✅ Apartment updated successfully.`, 'var(--success)');
                }
            }

            saveApartmentRecords(records);
            closeModal('apt-modal');
            renderTable();
        }

        // Staff Actions
        function toggleStatus(id, newStatus) {
            let records = getApartmentRecords();
            let apt = records.find(r => r.apartment_id === id);
            if (apt) {
                apt.status = newStatus;
                saveApartmentRecords(records);
                logAudit(`Changed status of ${id} to ${newStatus}`, 'STATUS_UPDATE');
                showToast(`✅ Apartment marked as ${newStatus}.`, 'var(--success)');
                renderTable();
            }
        }

        // MIS Actions
        function approveApt(id) {
            let records = getApartmentRecords();
            let apt = records.find(r => r.apartment_id === id);
            if (apt) {
                apt.status = 'Approved';
                saveApartmentRecords(records);
                logAudit(`Approved apartment record: ${id}`, 'APPROVE');
                showToast(`✅ Apartment ${id} has been Approved.`, 'var(--success)');
                renderTable();
            }
        }

        function rejectApt(id) {
            let records = getApartmentRecords();
            let apt = records.find(r => r.apartment_id === id);
            if (apt) {
                apt.status = 'Inactive';
                saveApartmentRecords(records);
                logAudit(`Rejected apartment record: ${id}`, 'REJECT');
                showToast(`❌ Apartment ${id} was Rejected.`, 'var(--warning)');
                renderTable();
            }
        }

        renderTable();
    </script>
</body>

</html>