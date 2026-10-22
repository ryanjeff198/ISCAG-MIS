<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Received Death Certificates (Male Da'wah)</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary-male: #14532d;
      --accent-male: #c59b27;
      --light-male: #e8f5ed;
    }
    .badge-received {
      background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
    }
    .badge-reviewed {
      background: #fef3c7; color: #92400e; border: 1px solid #fde68a;
    }
    .badge-completed {
      background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;
    }
    .btn-action-view {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 12px; border-radius: 6px; font-size: 0.76rem;
      font-weight: 700; text-decoration: none; cursor: pointer;
      border: 1px solid var(--border); background: white; color: var(--text-main);
      transition: all 0.2s;
    }
    .btn-action-view:hover {
      border-color: var(--primary-male); color: var(--primary-male); background: #f0fdf4;
    }
    .btn-action-file {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 12px; border-radius: 6px; font-size: 0.76rem;
      font-weight: 700; text-decoration: none; cursor: pointer;
      border: 1px solid var(--primary-male); background: var(--primary-male); color: white;
      transition: all 0.2s;
    }
    .btn-action-file:hover {
      background: #0f3d21; color: white;
    }
    .cert-detail-grid {
      display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 14px;
    }
    .cert-detail-card {
      background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px;
    }
    .cert-detail-card h6 {
      font-size: 0.74rem; font-weight: 800; text-transform: uppercase;
      color: var(--primary-male); margin-bottom: 8px; letter-spacing: 0.04em;
      border-bottom: 1px solid #e5e7eb; padding-bottom: 4px;
    }
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .filter-pills {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
    }
    .filter-pill {
      font-size: 0.78rem;
      font-weight: 700;
      padding: 7px 15px;
      border-radius: 20px;
      text-decoration: none;
      color: var(--text-muted);
      background: white;
      border: 1px solid var(--border);
      transition: all 0.2s ease;
      white-space: nowrap;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .filter-pill:hover, .filter-pill.active {
      background: var(--light-male);
      color: var(--primary-male);
      border-color: var(--primary-male);
      box-shadow: 0 2px 6px rgba(20, 83, 45, 0.1);
    }
    .search-form {
      display: flex;
      align-items: center;
      gap: 8px;
      flex: 1;
      max-width: 520px;
    }
    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border: 1.5px solid var(--border);
      border-radius: 9px;
      padding: 7px 14px;
      gap: 10px;
      flex: 1;
      transition: border-color 0.2s, box-shadow 0.2s;
      position: relative;
    }
    .search-box:focus-within {
      border-color: var(--primary-male);
      box-shadow: 0 0 0 3px rgba(20, 83, 45, 0.12);
    }
    .search-box svg {
      width: 17px;
      height: 17px;
      min-width: 17px;
      fill: var(--text-muted);
      flex-shrink: 0;
      transition: fill 0.2s;
    }
    .search-box:focus-within svg {
      fill: var(--primary-male);
    }
    .search-box input {
      border: none;
      outline: none;
      font-size: 0.85rem;
      width: 100%;
      font-family: inherit;
      background: transparent;
      color: var(--text-main);
    }
    .search-box input::placeholder {
      color: #9ca3af;
      font-size: 0.82rem;
    }
    .btn-clear-inline {
      background: #e5e7eb;
      border: none;
      color: #4b5563;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.65rem;
      font-weight: 800;
      cursor: pointer;
      padding: 0;
      line-height: 1;
      flex-shrink: 0;
      transition: background 0.15s, color 0.15s;
    }
    .btn-clear-inline:hover {
      background: #cbd5e1;
      color: #111827;
    }
    .btn-search-submit {
      padding: 8px 18px;
      border-radius: 8px;
      border: none;
      background: var(--primary-male);
      color: white;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: background 0.18s, box-shadow 0.18s;
      font-family: inherit;
      white-space: nowrap;
      box-shadow: 0 2px 6px rgba(20, 83, 45, 0.2);
    }
    .btn-search-submit:hover {
      background: #0f3d21;
      box-shadow: 0 4px 10px rgba(20, 83, 45, 0.3);
    }
    .btn-search-clear {
      padding: 7px 14px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      background: white;
      color: var(--text-muted);
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      transition: all 0.18s;
      font-family: inherit;
      white-space: nowrap;
    }
    .btn-search-clear:hover {
      border-color: #dc2626;
      color: #dc2626;
      background: #fef2f2;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'death_certificates';
      $dawah_type = 'male'; 
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      
      <!-- Top Bar -->
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--light-male); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary-male);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Received Death Certificates</div>
            <div class="top-bar-subtitle">Civil registry transmittal files and deceased records forwarded by Damayan Department</div>
          </div>
        </div>
        <div class="top-bar-actions" style="display:flex; align-items:center; gap:8px;">
          <a href="<?= url('/admin/dawah/male') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--primary-male);">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Da'wah Dashboard
          </a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Dashboard</a><span class="sep">›</span>
          <span class="current">Received Death Certificates</span>
        </div>

        <!-- Metric Insights Cards -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Certificates Received</div>
            <div class="insight-value" style="color:var(--primary-male);"><?= (int)($allTotal ?? $totalCount ?? 0) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Transmitted from Damayan</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Awaiting Review / Action</div>
            <div class="insight-value" style="color:#d97706;"><?= (int)($allNew ?? $newCount ?? 0) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">New certificate files</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Reviewed &amp; Processed</div>
            <div class="insight-value" style="color:#16a34a;"><?= (int)(($allReviewed ?? $reviewedCount ?? 0) + ($allCompleted ?? $completedCount ?? 0)) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Salatul Janazah &amp; records done</div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="filter-bar">
          <div class="filter-pills">
            <a href="<?= url('/admin/dawah/male/death-certificates?status=all' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="filter-pill <?= ($status ?? 'all') === 'all' ? 'active' : '' ?>">All (<?= (int)$totalCount ?>)</a>
            <a href="<?= url('/admin/dawah/male/death-certificates?status=Received' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="filter-pill <?= ($status ?? '') === 'Received' ? 'active' : '' ?>">New Received (<?= (int)$newCount ?>)</a>
            <a href="<?= url('/admin/dawah/male/death-certificates?status=Reviewed' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="filter-pill <?= ($status ?? '') === 'Reviewed' ? 'active' : '' ?>">Reviewed (<?= (int)$reviewedCount ?>)</a>
            <a href="<?= url('/admin/dawah/male/death-certificates?status=Completed' . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>" class="filter-pill <?= ($status ?? '') === 'Completed' ? 'active' : '' ?>">Completed (<?= (int)$completedCount ?>)</a>
          </div>

          <form method="GET" action="<?= url('/admin/dawah/male/death-certificates') ?>" class="search-form" id="death-cert-search-form">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status ?? 'all') ?>">
            <div class="search-box">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 14z"/></svg>
              <input type="text" id="cert-search-input" name="search" placeholder="Search by Reg #, Deceased Name, Informant..." value="<?= htmlspecialchars($search ?? '') ?>" oninput="liveFilterTable()" autocomplete="off" />
              <button type="button" id="btn-clear-inline" class="btn-clear-inline" onclick="clearSearchInput()" title="Clear search" style="<?= empty($search) ? 'display:none;' : '' ?>">✕</button>
            </div>
            <button type="submit" class="btn-search-submit">Search</button>
            <?php if (!empty($search)): ?>
              <a href="<?= url('/admin/dawah/male/death-certificates' . (($status ?? 'all') !== 'all' ? '?status=' . urlencode($status) : '')) ?>" class="btn-search-clear">Clear</a>
            <?php endif; ?>
          </form>
        </div>

        <!-- Death Certificates Records Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; align-items:center; justify-content:space-between;">
            <h6 style="color: var(--primary-male); margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary-male);"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
              Death Certificate Transmittals (<span id="records-shown-count"><?= count($records ?? []) ?></span> records shown)
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Registration #</th>
                    <th>Deceased Information</th>
                    <th>Date &amp; Time of Death</th>
                    <th>Certificate Date / Sent Date</th>
                    <th>Transmitted By &amp; Purpose</th>
                    <th>Status</th>
                    <th style="text-align:center; min-width:180px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($records)): ?>
                    <tr>
                      <td colspan="7" style="text-align:center; padding:50px 20px; color:var(--text-muted);">
                        <div style="font-size:1.1rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">No Transmitted Death Certificates Found</div>
                        <div style="font-size:0.85rem;">
                          <?= !empty($search) ? 'No records match your search criteria.' : 'No death certificate files have been sent from Damayan department yet.' ?>
                        </div>
                      </td>
                    </tr>
                  <?php else: ?>
                    <tr id="no-live-matches-row" style="display:none;">
                      <td colspan="7" style="text-align:center; padding:45px 20px; color:var(--text-muted);">
                        <div style="font-size:1.05rem; font-weight:700; color:var(--text-main); margin-bottom:4px;">No Matching Records Found</div>
                        <div style="font-size:0.85rem;">Try adjusting your search query or click Clear.</div>
                      </td>
                    </tr>
                    <?php foreach ($records as $r): ?>
                      <tr class="cert-row" data-search="<?= htmlspecialchars(strtolower(
                        ($r['registration_number'] ?? '') . ' ' .
                        ($r['id'] ?? '') . ' ' .
                        ($r['deceased_name'] ?? '') . ' ' .
                        ($r['first_name'] ?? '') . ' ' .
                        ($r['middle_name'] ?? '') . ' ' .
                        ($r['last_name'] ?? '') . ' ' .
                        ($r['informant_name'] ?? '') . ' ' .
                        ($r['sent_by_name'] ?? '') . ' ' .
                        ($r['purpose'] ?? '') . ' ' .
                        ($r['status'] ?? '')
                      )) ?>">
                        <td class="td-id">
                          <span style="font-weight:800; color:var(--primary-male);">#<?= htmlspecialchars($r['registration_number']) ?></span>
                          <div style="font-size:0.68rem; color:var(--text-muted); margin-top:2px;">Ref ID: #<?= (int)$r['id'] ?></div>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;"><?= htmlspecialchars($r['deceased_name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                            <span style="font-weight:700;"><?= htmlspecialchars($r['sex'] ?? 'Male') ?></span>
                            <?= !empty($r['age']) ? ' &bull; ' . htmlspecialchars($r['age']) : '' ?>
                            <?= !empty($r['civil_status']) ? ' &bull; ' . htmlspecialchars($r['civil_status']) : '' ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-weight:700; color:#b91c1c; font-size:0.86rem;">
                            <?= date('M d, Y', strtotime($r['date_of_death'])) ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            <?= !empty($r['time_of_death']) ? date('g:i A', strtotime($r['time_of_death'])) : 'Time unrecorded' ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-size:0.83rem; font-weight:700; color:var(--text-main);">
                            <?= !empty($r['death_cert_date']) ? date('M d, Y', strtotime($r['death_cert_date'])) : '—' ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            Sent: <?= date('M d, Y', strtotime($r['sent_at'])) ?>
                          </div>
                        </td>
                        <td>
                          <div style="font-size:0.83rem; font-weight:700; color:var(--primary-male);">
                            <?= htmlspecialchars($r['purpose'] ?? 'Janazah & Religious Records') ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            From: <?= htmlspecialchars($r['sent_by_name'] ?? 'Damayan Coordinator') ?>
                          </div>
                        </td>
                        <td>
                          <?php 
                            $st = $r['status'] ?? 'Received';
                            $badgeClass = ($st === 'Completed') ? 'badge-completed' : (($st === 'Reviewed') ? 'badge-reviewed' : 'badge-received');
                          ?>
                          <span class="badge-status <?= $badgeClass ?>" style="font-size:0.72rem; padding:3px 8px;">
                            <?= htmlspecialchars($st) ?>
                          </span>
                        </td>
                        <td style="text-align:center;">
                          <div style="display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">
                            <?php if ($st === 'Received'): ?>
                              <button type="button" class="btn-action-view" style="background:#ecfdf5; color:#065f46; border-color:#a7f3d0; font-weight:700;" onclick="quickAcknowledge(<?= (int)$r['id'] ?>, 'Reviewed')" title="Mark as Received & Reviewed">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Receive
                              </button>
                            <?php endif; ?>
                            <button type="button" class="btn-action-view" onclick="openDawahCertModal(<?= (int)$r['id'] ?>)" title="View Certificate Data">
                              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg> View Data
                            </button>
                            <a href="<?= url('/admin/dawah/death-certificates/file?id=' . $r['id']) ?>" target="_blank" class="btn-action-file" title="Print Official Philippine Death Certificate (Form 103)">
                              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg> Print Cert
                            </a>
                          </div>
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
  </div>

  <?php include __DIR__ . '/dawah_cert_detail_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    let currentCert = null;

    async function openDawahCertModal(id) {
      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/detail') ?>?id=' + encodeURIComponent(id));
        const data = await res.json();
        if (!data.success || !data.record) {
          alert('Could not find certificate details.');
          return;
        }

        const r = data.record;
        currentCert = r;

        document.getElementById('ack-id').value = r.id;
        document.getElementById('m-reg-no').textContent = '#' + r.registration_number;
        document.getElementById('m-deceased-name').textContent = r.deceased_name;
        document.getElementById('m-vital-sub').textContent = `${r.sex || 'Male'} • ${r.age || 'Age unrecorded'} • DOD: ${r.date_of_death}`;
        
        const badge = document.getElementById('m-status-badge');
        badge.textContent = r.status;
        badge.className = 'badge-status ' + (r.status === 'Completed' ? 'badge-completed' : (r.status === 'Reviewed' ? 'badge-reviewed' : 'badge-received'));

        document.getElementById('m-reg').textContent = '#' + r.registration_number;
        document.getElementById('m-cert-date').textContent = r.death_cert_date || '—';
        document.getElementById('m-sent-at').textContent = r.sent_at || '—';
        document.getElementById('m-civil').textContent = r.civil_status || '—';
        document.getElementById('m-religion').textContent = r.religion || 'Islam';
        document.getElementById('m-citizenship').textContent = r.citizenship || 'Filipino';

        document.getElementById('m-dod').textContent = r.date_of_death;
        document.getElementById('m-tod').textContent = r.time_of_death || '—';
        document.getElementById('m-dob').textContent = r.date_of_birth || '—';
        document.getElementById('m-age').textContent = r.age || '—';
        document.getElementById('m-occupation').textContent = r.occupation || '—';

        document.getElementById('m-pod').textContent = r.place_of_death || 'City of Dasmariñas, Cavite';
        document.getElementById('m-residence').textContent = r.residence || 'City of Dasmariñas, Cavite';
        
        let inf = r.informant_name || 'Family Representative';
        if (r.informant_relationship) inf += ` (${r.informant_relationship})`;
        if (r.informant_contact) inf += ` • ${r.informant_contact}`;
        document.getElementById('m-informant').textContent = inf;

        document.getElementById('m-sent-by').textContent = r.sent_by_name || 'Damayan Staff';
        document.getElementById('m-purpose').textContent = r.purpose || 'Janazah & Religious Records';
        document.getElementById('m-notes').textContent = r.transmission_notes || 'None provided.';

        document.getElementById('ack-status').value = r.status || 'Received';
        document.getElementById('ack-notes').value = '';

        document.getElementById('btn-open-file-doc').href = '<?= url('/admin/dawah/death-certificates/file?id=') ?>' + r.id;

        document.getElementById('dawahCertModal').style.display = 'flex';
      } catch (err) {
        console.error(err);
        alert('Failed to load certificate record.');
      }
    }

    async function quickAcknowledge(id, status) {
      if (!confirm('Acknowledge and mark this Death Certificate as received & reviewed?')) return;
      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/acknowledge') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status, notes: 'Quick acknowledged by Male Da\'wah Officer.' })
        });
        const data = await res.json();
        if (data.success) {
          location.reload();
        } else {
          alert(data.error || 'Failed to acknowledge.');
        }
      } catch (err) {
        console.error(err);
        alert('Failed to acknowledge certificate.');
      }
    }

    function closeDawahCertModal() {
      document.getElementById('dawahCertModal').style.display = 'none';
    }

    async function submitDawahStatus() {
      const id = document.getElementById('ack-id').value;
      const status = document.getElementById('ack-status').value;
      const notes = document.getElementById('ack-notes').value.trim();
      const btn = document.getElementById('ack-btn');

      if (!id) return;
      btn.disabled = true;
      btn.textContent = 'Saving...';

      try {
        const res = await fetch('<?= url('/admin/dawah/death-certificates/acknowledge') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, status, notes })
        });
        const data = await res.json();
        if (data.success) {
          alert('Status updated successfully.');
          location.reload();
        } else {
          alert(data.error || 'Failed to update status.');
          btn.disabled = false;
          btn.textContent = 'Save Da\'wah Status Update';
        }
      } catch (e) {
        console.error(e);
        alert('Error saving status update.');
        btn.disabled = false;
        btn.textContent = 'Save Da\'wah Status Update';
      }
    }

    document.getElementById('dawahCertModal')?.addEventListener('click', function(e) {
      if (e.target === this) closeDawahCertModal();
    });

    function liveFilterTable() {
      const input = document.getElementById('cert-search-input');
      if (!input) return;
      const q = input.value.toLowerCase().trim();
      const clearBtn = document.getElementById('btn-clear-inline');
      if (clearBtn) {
        clearBtn.style.display = q.length > 0 ? 'inline-flex' : 'none';
      }

      const rows = document.querySelectorAll('.cert-row');
      const terms = q.split(/\s+/).filter(t => t.length > 0);
      let visibleCount = 0;

      rows.forEach(row => {
        const data = row.getAttribute('data-search') || '';
        const matches = terms.length === 0 || terms.every(t => {
          const cleanT = t.replace(/^(#|no\.?|ref\s*id:?)/i, '');
          return data.includes(t) || (cleanT && data.includes(cleanT));
        });

        if (matches) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      const noMatchesRow = document.getElementById('no-live-matches-row');
      if (noMatchesRow) {
        noMatchesRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
      }

      const counter = document.getElementById('records-shown-count');
      if (counter) {
        counter.textContent = visibleCount;
      }
    }

    function clearSearchInput() {
      const input = document.getElementById('cert-search-input');
      if (input) {
        input.value = '';
        liveFilterTable();
        input.focus();
      }
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('search')) {
        const status = urlParams.get('status');
        let target = '<?= url('/admin/dawah/male/death-certificates') ?>';
        if (status && status !== 'all') {
          target += '?status=' + encodeURIComponent(status);
        }
        window.location.href = target;
      }
    }
  </script>
</body>
</html>
