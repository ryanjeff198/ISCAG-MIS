<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Tenant Information</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* ── STATUS FILTER TABS ── */
    .status-tabs {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    .status-tab {
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 700;
      border: 1.5px solid var(--border);
      background: white;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s;
      font-family: inherit;
    }
    .status-tab:hover {
      border-color: var(--primary);
      color: var(--primary);
    }
    .status-tab.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    /* ── TENANT TABLE ── */
    .tenant-table {
      width: 100%;
      border-collapse: collapse;
    }
    .tenant-table thead th {
      background: #f8f9fa;
      padding: 14px 16px;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      font-weight: 700;
      text-align: left;
      border-bottom: 2px solid var(--border);
      position: sticky;
      top: 0;
    }
    .tenant-table tbody tr {
      transition: background 0.15s;
    }
    .tenant-table tbody tr:hover {
      background: rgba(46, 125, 85, 0.03);
    }
    .tenant-table td {
      padding: 14px 16px;
      font-size: 0.88rem;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    .td-name {
      font-weight: 700;
      color: var(--primary-dark);
    }
    .td-id {
      font-family: monospace;
      font-size: 0.82rem;
      color: var(--text-muted);
    }
    .td-contact {
      font-size: 0.82rem;
      color: var(--text-main);
    }

    /* ── BADGES ── */
    .badge {
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }
    .badge-pending  { background: rgba(199,154,43,0.1); color: #c79a2b; }
    .badge-approved, .badge-assigned { background: rgba(46,125,85,0.1); color: #2f8a60; }
    .badge-rejected { background: rgba(220,53,69,0.1); color: #dc3545; }
    .badge-queued   { background: rgba(90,46,122,0.1); color: #5a2e7a; }
    .badge-guest    { background: rgba(100,100,100,0.1); color: #666; }
    .badge-tenant   { background: rgba(46,125,85,0.1); color: #2f8a60; }
    .badge-admin, .badge-staff_tenant { background: rgba(199,154,43,0.1); color: #c79a2b; }

    /* ── ACTION BUTTONS ── */
    .btn-view {
      padding: 6px 14px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 700;
      border: none;
      cursor: pointer;
      color: white;
      background: var(--primary);
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s;
      font-family: inherit;
    }
    .btn-view:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(23,107,69,0.25);
    }
    .btn-view svg { width: 14px; height: 14px; fill: currentColor; }

    /* ── STATS ROW ── */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .stat-card {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border);
      padding: 20px;
      text-align: center;
      transition: all 0.2s;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .stat-value {
      font-size: 2rem;
      font-weight: 800;
      line-height: 1;
      margin-bottom: 6px;
    }
    .stat-label {
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
    }
    .stat-primary .stat-value  { color: var(--primary-dark); }
    .stat-success .stat-value  { color: var(--success); }
    .stat-warning .stat-value  { color: #c79a2b; }
    .stat-danger  .stat-value  { color: var(--danger); }
    .stat-info    .stat-value  { color: #5a2e7a; }

    /* ── EMPTY STATE ── */
    .empty-state {
      padding: 60px 20px;
      text-align: center;
      background: white;
      border-radius: 12px;
      border: 1px dashed var(--border);
    }

    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .detail-item { }
    .detail-item .label {
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-muted);
      margin-bottom: 4px;
    }
    .detail-item .value {
      font-size: 0.92rem;
      font-weight: 600;
      color: var(--text-main);
      word-break: break-word;
    }
    .detail-item.full { grid-column: 1 / -1; }
    .detail-divider {
      grid-column: 1 / -1;
      height: 1px;
      background: var(--border);
      margin: 4px 0;
    }

    /* ── EMPTY STATE ── */
    .empty-state {
      padding: 60px 20px;
      text-align: center;
      background: white;
      border-radius: 12px;
      border: 1px dashed var(--border);
    }

    @media (max-width: 768px) {
      .detail-grid { grid-template-columns: 1fr; }
      .stats-row { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'tenants';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

    <div class="main-content">
      <div class="top-bar">
        <div>
          <div class="top-bar-title" id="page-title">Tenant Information</div>
          <div class="top-bar-subtitle">View all registered users, their application status, and personal details</div>
        </div>
      </div>

      <div class="page-body">

        <!-- STATS SUMMARY -->
        <?php
          $totalUsers = count($tenants ?? []);
          $totalApps = count($applications ?? []);
          $assignedCount = count(array_filter($applications ?? [], fn($a) => strtolower($a['status'] ?? '') === 'assigned'));
          $pendingCount = count(array_filter($applications ?? [], fn($a) => strtolower($a['status'] ?? '') === 'pending'));
          $queuedCount = count(array_filter($applications ?? [], fn($a) => strtolower($a['status'] ?? '') === 'queued'));
        ?>
        <div class="stats-row">
          <div class="stat-card stat-primary">
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-label">Total Users</div>
          </div>
          <div class="stat-card stat-success">
            <div class="stat-value"><?= $assignedCount ?></div>
            <div class="stat-label">Assigned Tenants</div>
          </div>
          <div class="stat-card stat-warning">
            <div class="stat-value"><?= $pendingCount ?></div>
            <div class="stat-label">Pending Review</div>
          </div>
          <div class="stat-card stat-info">
            <div class="stat-value"><?= $queuedCount ?></div>
            <div class="stat-label">In Waitlist</div>
          </div>
        </div>

        <!-- TENANT TABLE -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
              <h6><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>All Users</h6>
              <div class="status-tabs" id="status-tabs">
                <button class="status-tab active" data-filter="all" onclick="filterByStatus('all', this)">All</button>
                <button class="status-tab" data-filter="assigned" onclick="filterByStatus('assigned', this)">Assigned</button>
                <button class="status-tab" data-filter="pending" onclick="filterByStatus('pending', this)">Pending</button>
                <button class="status-tab" data-filter="queued" onclick="filterByStatus('queued', this)">Queued</button>
                <button class="status-tab" data-filter="rejected" onclick="filterByStatus('rejected', this)">Rejected</button>
                <button class="status-tab" data-filter="no-app" onclick="filterByStatus('no-app', this)">No Application</button>
              </div>
            </div>
            <div style="position:relative; min-width:220px;">
              <input type="text" id="tenant-search" placeholder="Search name, room number,"
                style="width:100%; padding:8px 14px 8px 36px; border:1.5px solid var(--border); border-radius:8px; font-size:0.85rem; font-family:inherit; outline:none; transition: all 0.2s; background: #f8f9fa;"
                oninput="filterTable()" onfocus="this.style.borderColor='var(--primary)'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(46,125,85,0.1)';" onblur="this.style.borderColor='var(--border)'; this.style.background='#f8f9fa'; this.style.boxShadow='none';">
              <svg viewBox="0 0 24 24" style="position:absolute; width:16px; height:16px; left:12px; top:50%; transform:translateY(-50%); fill:var(--text-muted);"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>
          </div>
          <div class="section-card-body" style="padding:0; overflow-x:auto;">
            <table class="tenant-table" data-searchable="false">
              <thead>
                <tr>
                  <th>Tenant ID</th>
                  <th>Full Name</th>
                  <th>Room No.</th>
                  <th>Room Type</th>
                  <th>App Status</th>
                  <th>Role</th>
                  <th>Date Applied</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tenant-tbody">
                <?php
                  // Build a map of applications by tenant_id
                  $appMap = [];
                  foreach ($applications ?? [] as $app) {
                    $appMap[$app['tenant_id']] = $app;
                  }
                ?>
                <?php if (empty($tenants)): ?>
                  <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">No users found in the system.</td></tr>
                <?php else: ?>
                  <?php foreach ($tenants as $t): ?>
                    <?php
                      $app = $appMap[$t['tenant_id']] ?? null;
                      $appStatus = $app ? strtolower($app['status'] ?? '') : 'no-app';
                      $appStatusLabel = $app ? ($app['status'] ?? '—') : 'No Application';
                      $roomtype = $app['roomtype'] ?? '—';
                      $roomNumber = '—';
                      if ($app && !empty($app['room_number'])) {
                          // Sequential rule: 5 rooms per floor
                          $roomStr = (string)$app['room_number'];
                          $isOld = (str_contains($roomStr, 'B') || str_contains($roomStr, '-'));
                          $numericPart = preg_replace('/\D/', '', $roomStr);
                          
                          $appBuilding = $app['building'] ?? 'Building 1';
                          preg_match('/\d+/', $appBuilding, $bm);
                          $bDigit = isset($bm[0]) ? substr($bm[0], 0, 1) : '1';
                          
                          if ($isOld) {
                              $seqNum = (int)$numericPart % 100;
                              $f = floor(($seqNum - 1) / 5) + 1;
                              $r = (($seqNum - 1) % 5) + 1;
                              $floorDigit = (string)$f;
                              $roomPart = str_pad((string)$r, 2, '0', STR_PAD_LEFT);
                          } else {
                              if (strlen($numericPart) >= 4) {
                                  $bDigit = substr($numericPart, 0, 1);
                                  $floorDigit = substr($numericPart, 1, 1);
                                  $roomPart = str_pad(substr($numericPart, 2), 2, '0', STR_PAD_LEFT);
                              } else if (strlen($numericPart) === 3) {
                                  $floorDigit = substr($numericPart, 0, 1);
                                  $roomPart = str_pad(substr($numericPart, 1), 2, '0', STR_PAD_LEFT);
                              } else {
                                  $floorDigit = '1';
                                  $roomPart = str_pad($numericPart, 2, '0', STR_PAD_LEFT);
                              }
                          }
                          
                          $roomPart = substr($roomPart, -2);
                          if ((int)$roomPart < 1) $roomPart = '01';
                          
                          $roomNumber = $bDigit . $floorDigit . $roomPart;
                      }
                      
                      $dateApplied = $app ? date('M d, Y', strtotime($app['submitted_at'])) : '—';
                      $role = $t['role'] ?? 'Guest';
                      $fullName = trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? ''));
                      if (!$fullName) $fullName = 'Unknown';

                      // Badge class
                      $badgeClass = 'badge-' . $appStatus;
                      $roleBadge = 'badge-' . strtolower(str_replace(' ', '_', $role));
                    ?>
                    <tr data-status="<?= $appStatus ?>">
                      <td class="td-id"><?= htmlspecialchars($t['tenant_id']) ?></td>
                      <td class="td-name"><?= htmlspecialchars($fullName) ?></td>
                      <td style="font-family:monospace; font-weight:700; color:var(--text-main);"><?= htmlspecialchars($roomNumber) ?></td>
                      <td style="color:var(--primary); font-weight:600;"><?= htmlspecialchars($roomtype) ?></td>
                      <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($appStatusLabel) ?></span></td>
                      <td><span class="badge <?= $roleBadge ?>"><?= htmlspecialchars($role) ?></span></td>
                      <td style="font-size:0.82rem; color:var(--text-muted);"><?= $dateApplied ?></td>
                      <td>
                        <button class="btn-view" onclick='viewTenant(<?= json_encode([
                          "tenant_id" => $t["tenant_id"],
                          "first_name" => $t["first_name"] ?? "",
                          "last_name" => $t["last_name"] ?? "",
                          "email" => $t["email"] ?? "",
                          "contactnum" => $t["contactnum"] ?? "",
                          "role" => $role,
                          "app_status" => $appStatusLabel,
                          "roomtype" => $roomtype,
                          "date_applied" => $dateApplied,
                          "app_id" => $app["id"] ?? "",
                          "familyname" => $app["familyname"] ?? "",
                          "givenname" => $app["givenname"] ?? "",
                          "muslimname" => $app["muslimname"] ?? "",
                          "civil_status" => $app["civil_status"] ?? "",
                          "address" => $app["address"] ?? "",
                          "birthdate" => $app["birthdate"] ?? "",
                          "age" => $app["age"] ?? "",
                          "sex" => $app["sex"] ?? "",
                          "occupation" => $app["occupation"] ?? "",
                          "monthly_income" => $app["monthly_income"] ?? "",
                          "reject_reason" => $app["reject_reason"] ?? "",
                        ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                          <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                          View
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- DETAIL MODAL -->
  <div class="modal-backdrop" id="modal-backdrop" style="display:none;">
    <div class="modal-content" style="max-width:700px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="modal-title" style="display:flex; align-items:center; gap:10px;">
          <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:var(--primary);"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          Tenant Details
        </h5>
        <button class="modal-close" onclick="closeModal()">&times;</button>
      </div>
      <div class="modal-body" id="modal-body">
        <!-- Populated dynamically -->
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    <?php
      $fullName = trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? ''));
      if (!$fullName) $fullName = $_SESSION['name'] ?? 'Apartment Staff';
      $email = $dbUser['email'] ?? $_SESSION['email'] ?? 'staff@iscag.org';
      $role = $dbUser['role'] ?? $_SESSION['role'] ?? 'Apartment Manager';
    ?>
    standardizePage('staff');
    syncSessionUser("<?= addslashes($fullName) ?>", "<?= addslashes($email) ?>", "<?= addslashes($role) ?>");
    loadUserNav();
    initNotifBadge('staff');

    let currentFilter = 'all';

    function filterByStatus(status, btn) {
      currentFilter = status;
      document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');
      filterTable();
    }

    function filterTable() {
      const term = document.getElementById('tenant-search').value.toLowerCase().trim();
      const rows = document.querySelectorAll('#tenant-tbody tr');
      
      rows.forEach(row => {
        if (!row.dataset.status) return; // skip empty-state row
        
        const matchesStatus = (currentFilter === 'all') || (row.dataset.status === currentFilter);
        const matchesSearch = term === '' || row.textContent.toLowerCase().includes(term);
        
        row.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
      });
    }

    function viewTenant(data) {
      document.getElementById('modal-title').textContent = (data.first_name + ' ' + data.last_name).trim() || 'Tenant Details';
      
      const body = document.getElementById('modal-body');
      body.innerHTML = `
        <div class="detail-grid">
          <div class="detail-item">
            <div class="label">Tenant ID</div>
            <div class="value" style="font-family:monospace;">${data.tenant_id}</div>
          </div>
          <div class="detail-item">
            <div class="label">Application ID</div>
            <div class="value" style="font-family:monospace;">${data.app_id || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">First Name</div>
            <div class="value">${data.first_name || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Last Name</div>
            <div class="value">${data.last_name || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Email</div>
            <div class="value">${data.email || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Contact Number</div>
            <div class="value">${data.contactnum || '—'}</div>
          </div>
          <div class="detail-divider"></div>
          <div class="detail-item">
            <div class="label">Application Status</div>
            <div class="value">${data.app_status}</div>
          </div>
          <div class="detail-item">
            <div class="label">System Role</div>
            <div class="value">${data.role}</div>
          </div>
          <div class="detail-item">
            <div class="label">Room Type Requested</div>
            <div class="value">${data.roomtype || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Date Applied</div>
            <div class="value">${data.date_applied || '—'}</div>
          </div>
          ${data.reject_reason ? `
          <div class="detail-item full">
            <div class="label" style="color:var(--danger);">Rejection Reason</div>
            <div class="value" style="color:var(--danger);">${data.reject_reason}</div>
          </div>` : ''}
          <div class="detail-divider"></div>
          <div class="detail-item">
            <div class="label">Muslim Name</div>
            <div class="value">${data.muslimname || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Civil Status</div>
            <div class="value">${data.civil_status || '—'}</div>
          </div>
          <div class="detail-item full">
            <div class="label">Address</div>
            <div class="value">${data.address || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Birthdate</div>
            <div class="value">${data.birthdate || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Age</div>
            <div class="value">${data.age || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Sex</div>
            <div class="value">${data.sex || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Occupation</div>
            <div class="value">${data.occupation || '—'}</div>
          </div>
          <div class="detail-item">
            <div class="label">Monthly Income</div>
            <div class="value">${data.monthly_income ? '₱' + parseFloat(data.monthly_income).toLocaleString() : '—'}</div>
          </div>
        </div>
      `;
      
      document.getElementById('modal-backdrop').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('modal-backdrop').style.display = 'none';
    }

    // Close on Escape
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
  </script>
</body>

</html>
