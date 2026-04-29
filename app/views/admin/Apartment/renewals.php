<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Contract Renewals</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>">
    <style>
        .page-header { background: linear-gradient(135deg, #1a3a5c, #2a5a8c); color: white; padding: 24px 32px; border-radius: 14px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 16px rgba(26,58,92,0.15); }
        .page-header-icon { width: 56px; height: 56px; background: rgba(255,255,255,0.15); border-radius: 14px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
        .page-header-icon svg { width: 28px; height: 28px; fill: white; }
        .page-header-text h2 { margin: 0 0 4px; font-family: 'Lora', serif; font-size: 1.4rem; font-weight: 700; }
        .page-header-text p { margin: 0; font-size: 0.9rem; color: rgba(255,255,255,0.7); }

        .renewals-card { background: white; border-radius: 14px; border: 1px solid var(--border); box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 14px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); border-bottom: 2px solid var(--border); }
        td { padding: 16px 20px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fbfdfc; }
        
        .tenant-cell { display: flex; align-items: center; gap: 12px; }
        .tenant-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; }
        .tenant-info-name { font-weight: 700; color: var(--primary-dark); margin: 0 0 2px; }
        .tenant-info-email { font-size: 0.8rem; color: var(--text-muted); margin: 0; }
        
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        .badge.pending { background: #fffbeb; color: #ca8a04; border: 1px solid #fef08a; }
        .badge.approved { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge.rejected { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        
        .action-btns { display: flex; gap: 8px; }
        .btn-action { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; }
        .btn-approve { background: rgba(47,138,96,0.1); color: #166534; }
        .btn-approve:hover { background: #2f8a60; color: white; }
        .btn-reject { background: rgba(139,46,46,0.1); color: #991b1b; }
        .btn-reject:hover { background: #8b2e2e; color: white; }
        
        .empty-state { text-align: center; padding: 48px; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php $active_page = 'apartment_renewals'; include BASE_PATH . '/app/views/admin/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div class="page-header-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
                </div>
                <div class="page-header-text">
                    <h2>Contract Renewals</h2>
                    <p>Manage tenant requests to extend their active lease agreements.</p>
                </div>
            </div>
            
            <div class="renewals-card">
                <?php if (empty($renewals)): ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:12px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <p>No contract renewal requests found.</p>
                    </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tenant details</th>
                            <th>Current Expiration</th>
                            <th>Unit Type</th>
                            <th>Requested Term</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($renewals as $req): 
                            $tenantName = htmlspecialchars($req['first_name'] . ' ' . $req['last_name']);
                            $initial = strtoupper(substr($req['first_name'], 0, 1));
                            $statusClass = strtolower($req['status']);
                        ?>
                        <tr>
                            <td>
                                <div class="tenant-cell">
                                    <div class="tenant-avatar"><?= $initial ?></div>
                                    <div>
                                        <p class="tenant-info-name"><?= $tenantName ?></p>
                                        <p class="tenant-info-email"><?= htmlspecialchars($req['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong><?= date('M j, Y', strtotime($req['end_date'])) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($req['unit_type']) ?></td>
                            <td><strong>+<?= (int)$req['requested_term_months'] ?> Months</strong></td>
                            <td><?= date('M j, Y h:i A', strtotime($req['created_at'])) ?></td>
                            <td>
                                <div class="badge <?= $statusClass ?>">
                                    <?= $req['status'] ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($req['status'] === 'Pending'): ?>
                                <div class="action-btns">
                                    <button class="btn-action btn-approve" onclick="showConfirmApprove(<?= $req['renewal_id'] ?>, '<?= $tenantName ?>', <?= (int)$req['requested_term_months'] ?>)">Approve</button>
                                    <button class="btn-action btn-reject" onclick="showConfirmReject(<?= $req['renewal_id'] ?>, '<?= $tenantName ?>')">Reject</button>
                                </div>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);font-size:0.8rem;font-style:italic;">Resolved</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:9999;">
        <div style="background:white; padding:24px 32px; border-radius:12px; width:400px; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
            <h3 id="confirmTitle" style="margin:0 0 10px; font-family:'Lora',serif; color:var(--primary-dark);">Confirm Action</h3>
            <p id="confirmMsg" style="margin:0 0 24px; font-size:0.9rem; color:var(--text-muted);"></p>
            <div style="display:flex; gap:12px; justify-content:center;">
                <button onclick="closeConfirm()" style="padding:10px 20px; border-radius:6px; border:1px solid var(--border); background:white; cursor:pointer;">Cancel</button>
                <a id="confirmUrl" href="#" style="padding:10px 20px; border-radius:6px; background:var(--primary); color:white; text-decoration:none; font-weight:700;">Confirm</a>
            </div>
        </div>
    </div>

    <script>
        function showConfirmApprove(id, name, term) {
            document.getElementById('confirmTitle').textContent = 'Approve Renewal';
            document.getElementById('confirmMsg').textContent = 'Extend the lease for ' + name + ' by exactly ' + term + ' months?';
            const urlBtn = document.getElementById('confirmUrl');
            urlBtn.href = "<?= url('/admin/apartment/renewals/approve?id=') ?>" + id;
            urlBtn.style.background = '#166534';
            document.getElementById('confirmModal').style.display = 'flex';
        }
        function showConfirmReject(id, name) {
            document.getElementById('confirmTitle').textContent = 'Reject Renewal';
            document.getElementById('confirmMsg').textContent = 'Reject the contract renewal request for ' + name + '?';
            const urlBtn = document.getElementById('confirmUrl');
            urlBtn.href = "<?= url('/admin/apartment/renewals/reject?id=') ?>" + id;
            urlBtn.style.background = '#991b1b';
            document.getElementById('confirmModal').style.display = 'flex';
        }
        function closeConfirm() {
            document.getElementById('confirmModal').style.display = 'none';
        }
    </script>
</body>
</html>
