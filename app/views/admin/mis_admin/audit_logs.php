<?php 
$active_page = 'audit_logs'; 
if (!function_exists('asset')) {
    function asset($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        $baseUrl = rtrim($baseUrl, '/');
        if (str_ends_with($baseUrl, '/public')) return $baseUrl . '/' . ltrim($path, '/');
        return $baseUrl . "/public/" . ltrim($path, '/'); 
    }
}
if (!function_exists('url')) {
    function url($path) { 
        $baseUrl = str_replace('/public/index.php', '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? ''));
        return rtrim($baseUrl, '/') . "/" . ltrim($path, '/'); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ISCAG MIS — Data Audit Logs</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <meta name="description" content="System tracking and audit logging" />
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
</head>

<body>
    <div class="app-wrapper">

        <!-- ═══ SIDEBAR ═══ -->
        <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="main-content">
            <div class="top-bar">
                <div class="top-bar-left">
                    <div>
                        <div class="top-bar-title">Data Audit Logs</div>
                        <div class="top-bar-subtitle">System-wide tracking for accountability, compliance, and security</div>
                    </div>
                </div>
                <div class="top-bar-actions">
                    <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
                    <button class="btn-topbar" onclick="downloadCSV()">
                        <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;margin-right:6px;">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                        </svg>
                        Export to CSV
                    </button>
                </div>
            </div>

            <div class="page-body">
                
                <!-- Admin Insights Ribbon -->
                <div class="admin-insights">
                    <div class="insight-card">
                        <div class="insight-label">Total Entries</div>
                        <div class="insight-value"><?= count($logs ?? []) ?></div>
                    </div>
                    <div class="insight-card">
                        <?php 
                            $criticalCount = count(array_filter($logs ?? [], function($l) {
                                $a = strtoupper($l['action'] ?? '');
                                return str_contains($a, 'DELETE') || str_contains($a, 'REJECT') || str_contains($a, 'WARNING') || str_contains($a, 'DEACTIVATE');
                            }));
                        ?>
                        <div class="insight-label">Critical Events</div>
                        <div class="insight-value <?= $criticalCount > 0 ? 'danger' : '' ?>"><?= $criticalCount ?></div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">System Access</div>
                        <div class="insight-value success">LIVE</div>
                    </div>
                    <div class="insight-card">
                        <div class="insight-label">Database Health</div>
                        <div class="insight-value info">Active</div>
                    </div>
                </div>

                <div class="filter-bar">
                    <input type="text" id="search-input" class="filter-input"
                        placeholder="Search by user, action, module, or details..." onkeyup="renderLogs()" style="flex:2;" />
                    <select id="filter-module" class="filter-input" onchange="renderLogs()">
                        <option value="">All Modules</option>
                        <!-- Populated dynamically -->
                    </select>
                    <select id="filter-role" class="filter-input" onchange="renderLogs()">
                        <option value="">All Roles</option>
                        <option value="Admin">MIS Admin</option>
                        <option value="Staff_Tenant">Apartments Staff</option>
                        <option value="Staff_Damayan">Damayan Staff</option>
                        <option value="Staff_Male">Da'wah Male Staff</option>
                        <option value="Staff_Female">Education Staff</option>
                        <option value="Tenant">Tenant</option>
                        <option value="Guest">Guest</option>
                        <option value="System">System</option>
                    </select>
                    <input type="date" id="filter-date" class="filter-input" onchange="renderLogs()" />
                </div>

                <div class="section-card">
                    <div class="section-card-body" style="padding: 0;">
                        <div style="overflow-x:auto;">
                            <table class="audit-table">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Actor / User</th>
                                        <th>Role</th>
                                        <th>Module</th>
                                        <th>Action</th>
                                        <th>Details / Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="logs-tbody">
                                    <!-- JS Injected -->
                                </tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="empty-state" style="display:none;padding:40px 20px;text-align:center;">
                            <svg viewBox="0 0 24 24"
                                style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;">
                                <path
                                    d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z" />
                            </svg>
                            <h4>No logs match your filter</h4>
                            <p style="color:var(--text-muted);font-size:0.88rem;">Try clearing your search query or changing your date and module filters.</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>"></script>
    <script>
        standardizePage('admin');

        // Inject REAL logs from PHP
        let allLogs = <?= json_encode($logs ?? []) ?>;

        // Map database fields to the UI expectation
        allLogs = allLogs.map(l => ({
            admin_name: l.admin_name || 'System',
            admin_id: l.admin_id,
            display_actor: (l.admin_name || 'System') + (l.admin_id && parseInt(l.admin_id) > 0 ? ' (#' + l.admin_id + ')' : ''),
            role: l.display_role || l.admin_role || 'System',
            module: getModuleLabel(l.module),
            raw_module: (l.module || '').toUpperCase(),
            action: l.action || 'ACTIVITY',
            timestamp: l.timestamp,
            details: l.details || ''
        }));

        populateModuleDropdown();

        function formatTime(isoStr) {
            if (!isoStr) return '--';
            const safeStr = isoStr.replace(' ', 'T');
            const d = new Date(safeStr);
            if (isNaN(d.getTime())) return isoStr;
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
                   d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }

        function getRoleBadge(role) {
            const r = (role || '').trim();
            const map = {
                'Admin': { label: 'MIS Admin', color: '#15803d', bg: '#dcfce7' },
                'Staff_Tenant': { label: 'Apartments Staff', color: '#0369a1', bg: '#e0f2fe' },
                'Staff_Damayan': { label: 'Damayan Staff', color: '#0f766e', bg: '#ccfbf1' },
                'Staff_Female': { label: 'Education Staff', color: '#7e22ce', bg: '#f3e8ff' },
                'Staff_Male': { label: 'Da\'wah Male Staff', color: '#4338ca', bg: '#e0e7ff' },
                'Tenant': { label: 'Tenant', color: '#b45309', bg: '#fef3c7' },
                'Guest': { label: 'Guest', color: '#64748b', bg: '#f1f5f9' },
                'System': { label: 'System', color: '#475569', bg: '#f8fafc' }
            };
            const item = map[r] || { label: r || 'User', color: '#475569', bg: '#f1f5f9' };
            return `<span style="display:inline-block;padding:2px 8px;font-size:11px;font-weight:700;border-radius:12px;background:${item.bg};color:${item.color};border:1px solid ${item.color}33;white-space:nowrap;">${item.label}</span>`;
        }

        function getModuleLabel(mod) {
            const map = {
                'AUTH': 'Security / Auth',
                'APARTMENT': 'Apartments MGMT',
                'BILLING': 'Billing & Finance',
                'USER': 'User Accounts',
                'USER_MANAGEMENT': 'User Management',
                'MAINTENANCE': 'Maintenance',
                'PARKING': 'Parking Allocation',
                'RENEWAL': 'Lease Renewals',
                'DAWAH': "Da'wah Services",
                'DAMAYAN': 'Damayan Dept',
                'FUNERAL': 'Burial Services',
                'GOVERNANCE': 'System Governance',
                'BROADCAST': 'System Broadcast',
                'PROFILE': 'Profile Updates'
            };
            return map[(mod || '').toUpperCase()] || mod || 'General';
        }

        function populateModuleDropdown() {
            const select = document.getElementById('filter-module');
            const modules = [...new Set(allLogs.map(l => l.raw_module))].filter(Boolean).sort();
            
            modules.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = getModuleLabel(m);
                select.appendChild(opt);
            });
        }

        function getActionTag(action) {
            let a = (action || '').toUpperCase();
            if (a.includes("LOGIN") || a.includes("APPROVE") || a.includes("ACCEPT") || a.includes("ACTIVE")) return `<span class="tag tag-login">${action}</span>`;
            if (a.includes("DELETE") || a.includes("REJECT") || a.includes("REMOVE") || a.includes("CANCEL") || a.includes("DEACTIVATE")) return `<span class="tag tag-delete">${action}</span>`;
            if (a.includes("UPDATE") || a.includes("EDIT") || a.includes("CHANGE") || a.includes("RESET")) return `<span class="tag tag-update">${action}</span>`;
            if (a.includes("CREATE") || a.includes("SUBMIT") || a.includes("ADD") || a.includes("REGISTER") || a.includes("REQUEST")) return `<span class="tag tag-create">${action}</span>`;
            if (a.includes("PAYMENT") || a.includes("BILL") || a.includes("PAID") || a.includes("SOA")) return `<span class="tag tag-billing">${action}</span>`;
            return `<span class="tag" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">${action}</span>`;
        }

        function renderLogs() {
            const tbody = document.getElementById('logs-tbody');
            const empty = document.getElementById('empty-state');

            const search = document.getElementById('search-input').value.toLowerCase().trim();
            const module = document.getElementById('filter-module').value;
            const role = document.getElementById('filter-role').value;
            const date = document.getElementById('filter-date').value;

            let filtered = allLogs.filter(log => {
                if (search) {
                    const matchSearch = log.display_actor.toLowerCase().includes(search) ||
                                        log.action.toLowerCase().includes(search) ||
                                        log.raw_module.toLowerCase().includes(search) ||
                                        log.module.toLowerCase().includes(search) ||
                                        (log.details && log.details.toLowerCase().includes(search));
                    if (!matchSearch) return false;
                }
                if (module && log.raw_module !== module.toUpperCase()) return false;
                if (role && log.role.toLowerCase() !== role.toLowerCase()) return false;
                if (date && !log.timestamp.startsWith(date)) return false;
                return true;
            });

            tbody.innerHTML = '';
            if (filtered.length === 0) {
                empty.style.display = 'block';
            } else {
                empty.style.display = 'none';
                filtered.forEach(log => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="white-space:nowrap;color:var(--text-muted);font-size:0.82rem;">${formatTime(log.timestamp)}</td>
                        <td style="font-weight:600;color:var(--text-main);white-space:nowrap;">${log.display_actor}</td>
                        <td>${getRoleBadge(log.role)}</td>
                        <td><strong style="color:var(--text-main);">${log.module}</strong></td>
                        <td>${getActionTag(log.action)}</td>
                        <td><div class="log-details" style="font-size:0.83rem;line-height:1.45;color:var(--text-main);">${log.details || '--'}</div></td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        }

        function downloadCSV() {
            let csvContent = "\uFEFFTimestamp,Actor,Role,Module,Action,Details\n";
            allLogs.forEach(function (row) {
                let cleanActor = (row.display_actor || '').replace(/"/g, '""');
                let cleanRole = (row.role || '').replace(/"/g, '""');
                let cleanMod = (row.module || '').replace(/"/g, '""');
                let cleanAct = (row.action || '').replace(/"/g, '""');
                let cleanDet = (row.details || '').replace(/"/g, '""');
                csvContent += `"${row.timestamp}","${cleanActor}","${cleanRole}","${cleanMod}","${cleanAct}","${cleanDet}"\n`;
            });
            let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement("a");
            let url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `iscag_audit_logs_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        renderLogs();
    </script>
</body>

</html>
