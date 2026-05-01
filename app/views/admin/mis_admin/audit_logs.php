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
                        <div class="top-bar-subtitle">System-wide tracking for accountability and security</div>
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
                                return str_contains($a, 'DELETE') || str_contains($a, 'REJECT') || str_contains($a, 'WARNING');
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
                        placeholder="Search by user, action, or module..." onkeyup="renderLogs()" style="flex:2;" />
                    <select id="filter-module" class="filter-input" onchange="renderLogs()">
                        <option value="">All Modules</option>
                        <option value="USER">User Management</option>
                        <option value="BILLING">Billing</option>
                        <option value="DAMAYAN">Damayan</option>
                        <option value="DA'WAH">Da'wah</option>
                        <option value="APARTMENT">Apartment</option>
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
                                        <th>User ID</th>
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
                        <div id="empty-state" class="empty-state" style="display:none;">
                            <svg viewBox="0 0 24 24"
                                style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;">
                                <path
                                    d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z" />
                            </svg>
                            <h4>No logs match your filter</h4>
                            <p>Try clearing your search or date filter to see more data.</p>
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
            admin_id: l.admin_name + ' (' + l.admin_id + ')',
            module: l.module,
            action: l.action,
            timestamp: l.timestamp,
            details: l.details
        }));

        function formatTime(isoStr) {
            const d = new Date(isoStr);
            return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function getActionTag(action) {
            let a = action.toUpperCase();
            if (a.includes("LOGIN") || a.includes("APPROVE")) return `<span class="tag tag-login">${action}</span>`;
            if (a.includes("DELETE") || a.includes("REJECT") || a.includes("REMOVE")) return `<span class="tag tag-delete">${action}</span>`;
            if (a.includes("UPDATE") || a.includes("EDIT")) return `<span class="tag tag-update">${action}</span>`;
            if (a.includes("CREATE") || a.includes("SUBMIT") || a.includes("ADD")) return `<span class="tag tag-create">${action}</span>`;
            if (a.includes("PAYMENT") || a.includes("BILL")) return `<span class="tag tag-billing">${action}</span>`;
            return `<span class="tag" style="background:#e0e0e0;color:#333;">${action}</span>`;
        }

        function renderLogs() {
            const tbody = document.getElementById('logs-tbody');
            const empty = document.getElementById('empty-state');

            const search = document.getElementById('search-input').value.toLowerCase();
            const module = document.getElementById('filter-module').value;
            const date = document.getElementById('filter-date').value;

            let filtered = allLogs.filter(log => {
                if (search && !(log.admin_id.toLowerCase().includes(search) || log.action.toLowerCase().includes(search) || (log.details && log.details.toLowerCase().includes(search)))) return false;
                if (module && log.module !== module) return false;
                if (date && !log.timestamp.startsWith(date)) return false;
                return true;
            });

            // Already sorted by PHP DESC
            // filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp)); 

            tbody.innerHTML = '';
            if (filtered.length === 0) {
                empty.style.display = 'block';
            } else {
                empty.style.display = 'none';
                filtered.forEach(log => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
            <td style="white-space:nowrap;color:var(--text-muted);font-size:0.85rem;">${formatTime(log.timestamp)}</td>
            <td style="font-weight:600;">${log.admin_id}</td>
            <td><strong>${log.module}</strong></td>
            <td>${getActionTag(log.action)}</td>
            <td><div class="log-details">${log.details || '--'}</div></td>
          `;
                    tbody.appendChild(tr);
                });
            }
        }

        function downloadCSV() {
            let csvContent = "data:text/csv;charset=utf-8,Timestamp,User ID,Module,Action,Details\n";
            allLogs.forEach(function (rowArray) {
                let row = [rowArray.timestamp, `"${rowArray.admin_id}"`, rowArray.module, rowArray.action, `"${rowArray.details || ''}"`];
                csvContent += row.join(",") + "\n";
            });
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "audit_logs.csv");
            document.body.appendChild(link);
            link.click();
        }

        renderLogs();
    </script>
</body>

</html>

