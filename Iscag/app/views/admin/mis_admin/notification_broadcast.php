<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Send Notifications</title>
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
    <style>
        .split-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 900px) {
            .split-layout {
                grid-template-columns: 1fr;
            }
        }

        .compose-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .compose-header {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-row {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(23, 107, 69, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(23, 107, 69, 0.2);
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(23, 107, 69, 0.3);
            background: var(--primary-dark);
        }

        /* History side */
        .history-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 0;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .history-list {
            max-height: 550px;
            overflow-y: auto;
            padding: 0 16px 16px;
        }

        .sent-item {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 16px;
        }

        .sent-item:last-child {
            border-bottom: none;
        }

        .sent-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .sent-icon svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        .sent-content h5 {
            margin: 0 0 4px 0;
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .sent-content p {
            margin: 0 0 8px 0;
            font-size: 0.85rem;
            color: var(--text-color);
            line-height: 1.4;
        }

        .sent-meta {
            display: flex;
            gap: 12px;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .target-badge {
            background: rgba(30, 95, 139, 0.1);
            color: #1e5f8b;
            padding: 2px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="main-content">
            <div class="top-bar">
                <div class="top-bar-left">
                    <img src="<?= asset('assets/ISCAG_Logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
                    <div>
                        <div class="top-bar-title">Notification Broadcast Hub</div>
                        <div class="top-bar-subtitle">Create and send instant alerts to specific users or entire departments.</div>
                    </div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/admin/mis_admin') ?>" class="btn-topbar">← Dashboard</a>
                </div>
            </div>

            <div class="page-body">
                
                <!-- Admin Insights Ribbon -->
                <div class="admin-insights">
                    <div class="insight-card">
                        <div class="insight-label">Recent Broadcasts</div>
                        <div class="insight-value info" id="stat-sent-val">0</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Delivery Status</div>
                        <div class="insight-value success">100%</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Open Rate</div>
                        <div class="insight-value info">72.4%</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Active Audience</div>
                        <div class="insight-value">1,248</div>
                    </div>
                </div>
                
                <div class="split-layout">
                    <!-- COMPOSE FORM -->
                    <div class="compose-card">
                        <div class="compose-header">
                            <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                            Compose Message
                        </div>
                        <form id="broadcast-form" onsubmit="event.preventDefault(); sendBroadcast();">
                            <div class="form-row">
                                <label class="form-label">Target Audience</label>
                                <select class="form-control" id="target-audience" required onchange="toggleSpecificUser()">
                                    <option value="">— Select Target Demographic —</option>
                                    <option value="ALL">Global (All Registered Users)</option>
                                    <option value="APARTMENT">Apartment Tenants Only</option>
                                    <option value="DAMAYAN">Damayan Members Only</option>
                                    <option value="DAWAH">Da'wah Members Only</option>
                                    <option value="SPECIFIC">Specific User...</option>
                                </select>
                            </div>
                            
                            <div class="form-row" id="specific-user-row" style="display:none;">
                                <label class="form-label">Select Specific User</label>
                                <select class="form-control" id="target-specific-user">
                                    <option value="">— Choose a User —</option>
                                    <option value="USR-001">Muhammad Usman (Apartment)</option>
                                    <option value="USR-002">Aisha Fatima (Da'wah)</option>
                                    <option value="USR-003">Omar Khan (Damayan)</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Notification Type</label>
                                <select class="form-control" id="notif-type" required>
                                    <option value="system">System Announcement (Default)</option>
                                    <option value="alert">Important Alert / Warning</option>
                                    <option value="request">Update on Request</option>
                                    <option value="payment">Billing / Payment Notice</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Message Title</label>
                                <input type="text" class="form-control" id="notif-title" placeholder="E.g., Scheduled Maintenance Next Week" required />
                            </div>

                            <div class="form-row">
                                <label class="form-label">Message Body</label>
                                <textarea class="form-control" id="notif-body" rows="6" placeholder="Type your full announcement or message here..." required></textarea>
                            </div>

                            <button type="submit" class="btn-submit">
                                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:currentColor;"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                Send Notification Now
                            </button>
                        </form>
                    </div>

                    <!-- SENT HISTORY -->
                    <div class="history-card">
                        <div class="compose-header" style="border-bottom:1px solid var(--border); padding:20px; background:#fcfcfc; margin:0;">
                            <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:currentColor;"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
                            Recent Broadcasts
                        </div>
                        <div class="history-list" id="sent-list">
                            <!-- JS injected -->
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- NOTIFICATION TOAST -->
    <div id="toast" style="visibility:hidden;min-width:250px;background:#333;color:#fff;text-align:center;border-radius:8px;padding:16px;position:fixed;z-index:9999;bottom:30px;right:30px;font-size:0.9rem;font-weight:600;box-shadow:0 10px 30px rgba(0,0,0,0.2);transition:visibility 0.4s, opacity 0.4s;opacity:0;"></div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        initAdminData();
        initSidebar();
        initDropdowns();

        function showToast(msg, bg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.style.background = bg || '#333';
            toast.style.visibility = 'visible';
            toast.style.opacity = '1';
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.style.visibility = 'hidden', 400);
            }, 3000);
        }

        function toggleSpecificUser() {
            const aud = document.getElementById('target-audience').value;
            const specRow = document.getElementById('specific-user-row');
            if (aud === 'SPECIFIC') {
                specRow.style.display = 'block';
                document.getElementById('target-specific-user').setAttribute('required', 'true');
            } else {
                specRow.style.display = 'none';
                document.getElementById('target-specific-user').removeAttribute('required');
            }
        }

        function getIconForType(type) {
            const map = {
                'system': '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>',
                'alert': '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>',
                'request': '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>',
                'payment': '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>'
            };
            return map[type] || map['system'];
        }

        // Mock DB for user mapping
        const MOCK_USERS = [
            { id: "USR-001", name: "Muhammad Usman", roles: ["APARTMENT", "DAMAYAN", "ALL"] },
            { id: "USR-002", name: "Aisha Fatima", roles: ["DAWAH", "ALL"] },
            { id: "USR-003", name: "Omar Khan", roles: ["DAMAYAN", "ALL"] }
        ];

        function sendBroadcast() {
            const audienceGroup = document.getElementById('target-audience').value;
            let specificUser = document.getElementById('target-specific-user').value;
            const specificUserName = document.getElementById('target-specific-user').options[document.getElementById('target-specific-user').selectedIndex].text;
            
            const type = document.getElementById('notif-type').value;
            const title = document.getElementById('notif-title').value;
            const body = document.getElementById('notif-body').value;

            // Load existing
            let globalNotifs = JSON.parse(localStorage.getItem('mis_notifications') || '[]');
            
            let targets = [];
            let targetLabel = audienceGroup;

            if (audienceGroup === 'SPECIFIC') {
                targets.push(specificUser);
                targetLabel = specificUserName.split(' (')[0];
            } else {
                // Map the audience group to corresponding users
                MOCK_USERS.forEach(u => {
                    if (u.roles.includes(audienceGroup)) {
                        targets.push(u.id);
                    }
                });
            }

            // Create individual notification records for each targeted user
            targets.forEach(tId => {
                const n = {
                    id: 'N-' + Date.now() + Math.floor(Math.random()*100),
                    tenantId: tId,
                    title: title,
                    message: body,
                    type: type,
                    createdAt: new Date().toISOString(),
                    read: false
                };
                globalNotifs.push(n);
            });

            // Save back
            localStorage.setItem('mis_notifications', JSON.stringify(globalNotifs));

            // Log broadcast to admin side history
            let broadcasts = JSON.parse(localStorage.getItem('mis_admin_broadcasts') || '[]');
            broadcasts.push({
                title: title,
                body: body,
                type: type,
                target: targetLabel,
                time: new Date().toISOString()
            });
            localStorage.setItem('mis_admin_broadcasts', JSON.stringify(broadcasts));

            showToast(`✅ Successfully broadcasted to ${targets.length} user(s).`, 'var(--success)');
            
            // Reset
            document.getElementById('broadcast-form').reset();
            toggleSpecificUser();
            
            // Re-render
            renderHistory();
        }

        function renderHistory() {
            const list = document.getElementById('sent-list');
            let broadcasts = JSON.parse(localStorage.getItem('mis_admin_broadcasts') || '[]');
            
            if (broadcasts.length === 0) {
                list.innerHTML = `<div style="padding:40px 20px; text-align:center; color:var(--text-muted);">
                    <svg viewBox="0 0 24 24" style="width:40px;height:40px;fill:var(--border);margin-bottom:10px;"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12s4.48 10 10 10 10-4.48 10-10zm-11 5H9v-2h2v2zm0-4H9V7h2v6z"/></svg><br>
                    No broadcasts sent yet.
                </div>`;
                return;
            }

            list.innerHTML = '';
            // Newest first
            broadcasts.sort((a,b) => new Date(b.time) - new Date(a.time)).forEach(b => {
                let d = new Date(b.time).toLocaleString('en-US', {month:'short', day:'numeric', hour:'numeric', minute:'2-digit'});
                const div = document.createElement('div');
                div.className = 'sent-item';
                div.innerHTML = `
                    <div class="sent-icon">
                        <svg viewBox="0 0 24 24">${getIconForType(b.type)}</svg>
                    </div>
                    <div class="sent-content">
                        <h5>${b.title}</h5>
                        <p>${b.body.substring(0, 80)}${b.body.length > 80 ? '...' : ''}</p>
                        <div class="sent-meta">
                            <span class="target-badge">To: ${b.target}</span>
                            <span>${d}</span>
                        </div>
                    </div>
                `;
                list.appendChild(div);
            });
        }

        // Initialize empty mock history if needed
        if (!localStorage.getItem('mis_admin_broadcasts')) {
            localStorage.setItem('mis_admin_broadcasts', JSON.stringify([
                {
                    title: "Welcome to the New Portal",
                    body: "Please complete your profile to access all features like Damayan, Da'wah, and Apartment services.",
                    type: "system",
                    target: "ALL",
                    time: new Date(Date.now() - 86400000).toISOString()
                }
            ]));
        }

        renderHistory();
    </script>
</body>
</html>
