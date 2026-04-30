<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Contract Renewals</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>">
    <style>
        /* ── Page Header ── */
        .page-header {
            background: var(--primary-gradient);
            color: white;
            padding: 32px;
            border-radius: 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .page-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .header-text h2 {
            margin: 0 0 4px;
            font-family: 'Lora', serif;
            font-size: 1.6rem;
            font-weight: 700;
        }

        .header-text p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.85;
        }

        /* ── KPI Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-light);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-info h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .stat-info p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Table Styling ── */
        .renewals-card {
            background: white;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .table-header-row {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fdfdfd;
        }

        .table-header-row h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-dark);
            font-family: 'Lora', serif;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box svg {
            position: absolute;
            left: 12px;
            width: 16px;
            height: 16px;
            fill: var(--text-muted);
            pointer-events: none;
        }

        .search-box input {
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.85rem;
            width: 240px;
            transition: all 0.2s;
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 92, 58, 0.1);
            width: 280px;
        }

        .mis-table th {
            background: var(--primary-dark);
            font-size: 0.72rem;
            color: white;
            padding: 16px 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .mis-table td {
            padding: 18px 24px;
            font-size: 0.88rem;
        }

        .tenant-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tenant-name {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 2px;
        }

        .tenant-email {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending { background: rgba(199, 154, 43, 0.1); color: var(--accent); }
        .badge-active { background: rgba(47, 138, 96, 0.1); color: var(--success); }
        .badge-expired { background: rgba(139, 46, 46, 0.1); color: var(--danger); }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-approve:hover { background: var(--success); border-color: var(--success); color: white; }
        .btn-reject:hover { background: var(--danger); border-color: var(--danger); color: white; }
        .btn-action svg { width: 16px; height: 16px; fill: currentColor; }

        /* ── Glass Modal ── */
        .glass-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 92, 58, 0.2);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-box {
            background: white;
            width: 100%;
            max-width: 420px;
            border-radius: 24px;
            padding: 32px;
            box-shadow: var(--shadow-2xl);
            text-align: center;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .modal-icon {
            width: 72px;
            height: 72px;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        <?php 
            $active_page = 'apartment_renewals'; 
            include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
        ?>

        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Contract Renewals</div>
                    <div class="top-bar-subtitle">Review and manage lease extension requests from tenants</div>
                </div>
                <div class="top-bar-actions"></div>
            </div>

            <div class="page-body">
                <!-- Breadcrumb -->
                <div class="breadcrumb-bar" style="margin-bottom: 24px;">
                    <a href="<?= url('/admin/apartment') ?>" style="text-decoration:none; color:var(--text-muted);">Apartment Management</a>
                    <span style="margin: 0 8px; color: var(--border);">/</span>
                    <span class="current">Contract Renewals</span>
                </div>

                <!-- Stats -->
            <?php
                $pendingCount = count(array_filter($renewals, fn($r) => $r['status'] === 'Pending'));
                $activeLeases = count(array_filter($allLeases, fn($l) => $l['lease_status'] === 'Accepted'));
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(199, 154, 43, 0.1); color: var(--accent);">
                        <svg viewBox="0 0 24 24" style="width:24px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div class="stat-info">
                        <h4><?= $pendingCount ?></h4>
                        <p>Pending Requests</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(47, 138, 96, 0.1); color: var(--success);">
                        <svg viewBox="0 0 24 24" style="width:24px; fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <div class="stat-info">
                        <h4><?= $activeLeases ?></h4>
                        <p>Active Leases</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(15, 92, 58, 0.1); color: var(--primary);">
                        <svg viewBox="0 0 24 24" style="width:24px; fill:currentColor;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    </div>
                    <div class="stat-info">
                        <h4><?= count($renewals) ?></h4>
                        <p>Total History</p>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="renewals-card">
                <div class="table-header-row">
                    <h3>Renewal Requests & Active Contracts</h3>
                    <div class="table-actions">
                        <div class="search-box">
                            <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                            <input type="text" id="renewalSearch" placeholder="Search tenant or unit..." onkeyup="filterRenewals()">
                        </div>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="mis-table" data-searchable="false">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Unit ID</th>
                                <th>Tenant Details</th>
                                <th>Unit Type</th>
                                <th>Current Expiry</th>
                                <th>Extension</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $displayedLeaseIds = array_column($renewals, 'lease_id');
                                $combinedData = $renewals;
                                foreach ($allLeases as $lease) {
                                    if (!in_array($lease['lease_id'], $displayedLeaseIds) && $lease['lease_status'] === 'Accepted') {
                                        $lease['renewal_id'] = null;
                                        $lease['status'] = 'Active';
                                        $lease['requested_term_months'] = 0;
                                        $lease['created_at'] = $lease['start_date'];
                                        $combinedData[] = $lease;
                                    }
                                }
                                usort($combinedData, function($a, $b) {
                                    return strtotime($a['end_date']) - strtotime($b['end_date']);
                                });

                                if (empty($combinedData)):
                            ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; padding:60px; color:var(--text-muted);">
                                        <svg viewBox="0 0 24 24" style="width:40px; fill:var(--border); margin-bottom:12px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                        <p>No renewal activity found at this time.</p>
                                    </td>
                                </tr>
                            <?php else: $rowNum = 1; foreach ($combinedData as $req): 
                                $tenantName = htmlspecialchars($req['first_name'] . ' ' . $req['last_name']);
                                $initial = strtoupper(substr($req['first_name'], 0, 1));
                                $status = strtolower($req['status'] ?? 'pending');
                                $badgeClass = ($status === 'pending') ? 'badge-pending' : (($status === 'active') ? 'badge-active' : 'badge-expired');
                            ?>
                                <tr>
                                    <td><?= $rowNum++ ?></td>
                                    <td><span style="font-weight:700; color:var(--primary); font-family:monospace;"><?= htmlspecialchars($req['unit_code'] ?? $req['unit_id'] ?? '—') ?></span></td>
                                    <td>
                                        <div class="tenant-profile">
                                            <div>
                                                <div class="tenant-name"><?= $tenantName ?></div>
                                                <div class="tenant-email"><?= htmlspecialchars($req['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span style="font-weight:600; color:var(--primary-dark);"><?= htmlspecialchars($req['unit_type'] ?? '—') ?></span></td>
                                    <td><?= date('M d, Y', strtotime($req['end_date'])) ?></td>
                                    <td>
                                        <?php if ($req['requested_term_months'] > 0): ?>
                                            <span style="color:var(--accent); font-weight:700;">+<?= (int)$req['requested_term_months'] ?> Mo</span>
                                        <?php else: ?>
                                            <span style="color:var(--text-muted); font-size:0.8rem;">No request</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span></td>
                                    <td>
                                        <div style="display:flex; gap:8px;">
                                            <?php if ($status === 'pending'): ?>
                                                <button class="btn-action btn-approve" title="Approve Extension" onclick="showConfirmApprove(<?= $req['renewal_id'] ?>, '<?= $tenantName ?>', <?= (int)$req['requested_term_months'] ?>)">
                                                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                                </button>
                                                <button class="btn-action btn-reject" title="Reject Extension" onclick="showConfirmReject(<?= $req['renewal_id'] ?>, '<?= $tenantName ?>')">
                                                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                                </button>
                                            <?php elseif ($status === 'active'): ?>
                                                <div style="color:var(--success); font-size:0.7rem; font-weight:700; display:flex; align-items:center; gap:4px;">
                                                    <svg viewBox="0 0 24 24" style="width:14px; fill:currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> Verified
                                                </div>
                                            <?php else: ?>
                                                <span style="color:var(--text-muted); font-size:0.75rem; font-style:italic;">Archived</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="glass-modal">
        <div class="modal-box">
            <div class="modal-icon" id="modal-icon-bg">
                <svg id="modal-svg" viewBox="0 0 24 24" style="width:32px; fill:var(--primary);"></svg>
            </div>
            <h3 id="confirmTitle" style="margin:0 0 8px; font-family:'Lora',serif; font-size:1.4rem; color:var(--primary-dark);">Confirm Action</h3>
            <p id="confirmMsg" style="margin:0 0 24px; font-size:0.95rem; color:var(--text-muted); line-height:1.5;"></p>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <button class="btn-topbar" onclick="closeConfirm()" style="height:48px; border-radius:12px;">Cancel</button>
                <a id="confirmUrl" href="#" class="btn-topbar primary" style="height:48px; border-radius:12px; text-decoration:none; display:flex; align-items:center; justify-content:center;">Confirm</a>
            </div>
        </div>
    </div>

    <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
    <script>
        standardizePage('staff');
        syncSessionUser("<?= addslashes(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>", "<?= addslashes($dbUser['email'] ?? '') ?>", "Apartment Manager");

        function showConfirmApprove(id, name, term) {
            document.getElementById('confirmTitle').textContent = 'Approve Renewal';
            document.getElementById('confirmMsg').innerHTML = `Are you sure you want to extend <strong>${name}'s</strong> lease by <strong>${term} months</strong>? This will update their lease end date automatically.`;
            
            const svg = document.getElementById('modal-svg');
            svg.innerHTML = '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>';
            svg.style.fill = 'var(--success)';
            document.getElementById('modal-icon-bg').style.background = 'rgba(47, 138, 96, 0.1)';

            const urlBtn = document.getElementById('confirmUrl');
            urlBtn.href = "<?= url('/admin/apartment/renewals/approve?id=') ?>" + id;
            urlBtn.style.background = 'var(--success)';
            
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function showConfirmReject(id, name) {
            document.getElementById('confirmTitle').textContent = 'Reject Renewal';
            document.getElementById('confirmMsg').innerHTML = `Are you sure you want to reject the renewal request for <strong>${name}</strong>? This action cannot be undone.`;
            
            const svg = document.getElementById('modal-svg');
            svg.innerHTML = '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
            svg.style.fill = 'var(--danger)';
            document.getElementById('modal-icon-bg').style.background = 'rgba(139, 46, 46, 0.1)';

            const urlBtn = document.getElementById('confirmUrl');
            urlBtn.href = "<?= url('/admin/apartment/renewals/reject?id=') ?>" + id;
            urlBtn.style.background = 'var(--danger)';
            
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function filterRenewals() {
            const query = document.getElementById('renewalSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.mis-table tbody tr');
            
            rows.forEach(row => {
                if (row.cells.length < 2) return; // Skip empty state row
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Close on backdrop click
        window.onclick = function(event) {
            const modal = document.getElementById('confirmModal');
            if (event.target == modal) closeConfirm();
        }
    </script>
</body>
</html>

