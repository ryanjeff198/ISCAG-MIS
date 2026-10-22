<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Records</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    .insight-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .insight-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--damayan-accent);
    }
    .insight-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }

    /* Filter & Search Bar */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 6px 14px;
      gap: 8px;
      min-width: 280px;
    }
    .search-box input {
      border: none;
      outline: none;
      font-size: 0.85rem;
      width: 100%;
      font-family: inherit;
    }
    .search-box svg {
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
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
      padding: 6px 14px;
      border-radius: 20px;
      text-decoration: none;
      color: var(--text-muted);
      background: white;
      border: 1px solid var(--border);
      transition: all 0.2s;
      white-space: nowrap;
      cursor: pointer;
    }
    .filter-pill:hover, .filter-pill.active {
      background: var(--damayan-light);
      color: var(--damayan-accent);
      border-color: var(--damayan-accent);
    }

    .mini-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 6px;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .mini-badge.pending { background: #f3f4f6; color: #6b7280; }
    .mini-badge.active { background: #eff6ff; color: #1d4ed8; }
    .mini-badge.done { background: #ecfdf5; color: #047857; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'burial';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Burial Service Registry</div>
            <div class="top-bar-subtitle">Complete archive of historical and active Islamic funeral service cases</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/damayan/burial-requests') ?>" class="btn-topbar primary" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Active Requests
          </a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Burial Records</span>
        </div>

        <!-- Burial Records Insights Cards -->
        <?php
          $totalActive = count($records ?? []);
          $receivedCount = count(array_filter($records ?? [], fn($r) => !empty($r['cert_date'])));
          $pendingCount = count(array_filter($records ?? [], fn($r) => empty($r['cert_date'])));
          $archivedCount = count($archivedRecords ?? []);
        ?>
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Active Records</div>
            <div class="insight-value"><?= $totalActive ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#10b981;">
            <div class="insight-label">Certificates Received</div>
            <div class="insight-value success"><?= $receivedCount ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#f59e0b;">
            <div class="insight-label">Pending Certificates</div>
            <div class="insight-value warning"><?= $pendingCount ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#6b7280;">
            <div class="insight-label">Archived Records</div>
            <div class="insight-value" style="color:#4b5563;" id="insight-archived-count"><?= $archivedCount ?></div>
          </div>
        </div>

        <!-- Section Navigation Pills -->
        <div class="filter-bar">
          <div class="filter-pills">
            <button type="button" class="filter-pill active" id="btn-view-requests" onclick="toggleViewMode('requests')">Burial Records</button>
            <button type="button" class="filter-pill" id="btn-view-archive" onclick="toggleViewMode('archive')">
              Archive Records
              <?php if (!empty($archivedRecords)): ?>
                <span id="badge-archive-pill" style="font-size:0.7rem; background:#fee2e2; color:#991b1b; padding:2px 7px; border-radius:10px; margin-left:4px; font-weight:800;"><?= count($archivedRecords) ?></span>
              <?php endif; ?>
            </button>
          </div>
          <div style="display:flex; align-items:center; gap:10px;">
            <div class="search-box">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" id="table-search-input" placeholder="Search deceased, applicant, ref #..." value="<?= htmlspecialchars($searchQuery ?? '') ?>" onkeyup="onSearchInput(event)" />
            </div>
          </div>
        </div>

        <!-- Container 1: Plot Requests Table -->
        <div id="container-requests" class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Burial Records &amp; Requests
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant / Representative</th>
                    <th>Deceased</th>
                    <th>Date Requested</th>
                    <th>Date Certificate Received</th>
                    <th>Status</th>
                    <th style="text-align:center; width:64px;">Action</th>
                  </tr>
                </thead>
                <tbody id="burial-tbody">
                  <?php if(empty($records)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No burial records found.</td></tr>
                  <?php else: ?>
                    <?php foreach($records as $r): ?>
                      <tr id="burial-row-<?= htmlspecialchars($r['id']) ?>">
                        <td class="td-id">
                          #<?= htmlspecialchars($r['id']) ?>
                          <?php if (($r['cert_action_type'] ?? '') === 'copy'): ?>
                            <div style="font-size:0.68rem; font-weight:700; color:#1e40af; background:#dbeafe; padding:1px 6px; border-radius:4px; display:inline-block; margin-top:3px;">Copy Request</div>
                          <?php else: ?>
                            <div style="font-size:0.68rem; font-weight:700; color:#065f46; background:#d1fae5; padding:1px 6px; border-radius:4px; display:inline-block; margin-top:3px;">Death Reg</div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($r['name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);">Representative</div>
                        </td>
                        <td>
                          <div style="font-weight:700;"><?= htmlspecialchars($r['deceased']) ?></div>
                          <span style="font-size:0.7rem; font-weight:700; padding:1px 6px; border-radius:8px; <?= ($r['deceased_sex'] ?? '') === 'Female' ? 'background:#fce7f3;color:#9d174d;' : 'background:#e0e7ff;color:#3730a3;' ?>">
                            <?= htmlspecialchars($r['deceased_sex'] ?? 'Male') ?>
                          </span>
                        </td>
                        <td style="font-size:0.82rem;">
                          <?= !empty($r['submitted_at']) ? date('M d, Y', strtotime($r['submitted_at'])) : (!empty($r['date']) ? date('M d, Y', strtotime($r['date'])) : '—') ?>
                        </td>
                        <td style="font-size:0.82rem;" id="cert-date-cell-<?= htmlspecialchars($r['id']) ?>">
                          <?php if(!empty($r['cert_date'])): ?>
                            <div style="font-weight:600; color:var(--damayan-dark);"><?= date('M d, Y', strtotime($r['cert_date'])) ?></div>
                            <span class="mini-badge done" style="font-size:0.65rem; margin-top:2px; display:inline-flex; align-items:center; gap:2px;"><svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Received</span>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-style:italic;" id="cert-date-text-<?= htmlspecialchars($r['id']) ?>">Pending</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge-status <?= htmlspecialchars($r['status_class'] ?? 'badge-pending') ?>">
                            <?= htmlspecialchars($r['status'] ?? 'Pending') ?>
                          </span>
                        </td>
                        <td style="text-align:center;">
                          <div class="action-menu">
                            <button type="button" class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
                              <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                            </button>
                            <div class="action-menu-dropdown">
                              <button type="button" class="action-menu-item" onclick="openCaseModal('<?= htmlspecialchars($r['id']) ?>')">
                                <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                View
                              </button>
                              <button type="button" class="action-menu-item success" id="menu-mark-received-<?= htmlspecialchars($r['id']) ?>" onclick="markAsReceived('<?= htmlspecialchars($r['id']) ?>', '<?= htmlspecialchars(addslashes($r['deceased'])) ?>')">
                                <svg viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                Mark as Received
                              </button>
                              <button type="button" class="action-menu-item" style="color:#14532d; font-weight:700;" onclick="openSendToDawahModal(<?= htmlspecialchars(json_encode([
                                'source_type' => 'burial_request',
                                'source_id' => $r['id'],
                                'registration_number' => !empty($r['ref_id']) ? $r['ref_id'] : 'REQ-' . $r['id'],
                                'deceased_name' => $r['deceased'],
                                'sex' => $r['deceased_sex'] ?? 'Male',
                                'date_of_death' => !empty($r['date_of_death']) ? $r['date_of_death'] : (!empty($r['date']) ? $r['date'] : date('Y-m-d')),
                                'informant_name' => $r['name'] ?? '',
                                'death_cert_date' => !empty($r['cert_date']) ? $r['cert_date'] : date('Y-m-d')
                              ]), ENT_QUOTES, 'UTF-8') ?>)">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                Send to Da'wah
                              </button>
                              <button type="button" class="action-menu-item danger" onclick="deleteBurialRecord('<?= htmlspecialchars($r['id']) ?>', '<?= htmlspecialchars(addslashes($r['deceased'])) ?>')">
                                <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                Delete
                              </button>
                            </div>
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

        <!-- Container 2: Archive Records Table -->
        <div id="container-archive" class="section-card" style="display:none;">
          <div class="section-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <h6 style="color: var(--damayan-dark); margin:0;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></svg>
              Archive Records
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant / Representative</th>
                    <th>Deceased</th>
                    <th>Date Requested</th>
                    <th>Date Certificate Received</th>
                    <th>Date Archived</th>
                    <th>Status</th>
                    <th style="text-align:center; width:90px;">Action</th>
                  </tr>
                </thead>
                <tbody id="archive-tbody">
                  <?php if(empty($archivedRecords)): ?>
                    <tr id="archive-empty-row"><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No archived records found.</td></tr>
                  <?php else: ?>
                    <?php foreach($archivedRecords as $a): ?>
                      <tr id="archive-row-<?= htmlspecialchars($a['id']) ?>">
                        <td class="td-id">
                          #<?= htmlspecialchars($a['id']) ?>
                          <?php if (($a['cert_action_type'] ?? '') === 'copy'): ?>
                            <div style="font-size:0.68rem; font-weight:700; color:#1e40af; background:#dbeafe; padding:1px 6px; border-radius:4px; display:inline-block; margin-top:3px;">Copy Request</div>
                          <?php else: ?>
                            <div style="font-size:0.68rem; font-weight:700; color:#065f46; background:#d1fae5; padding:1px 6px; border-radius:4px; display:inline-block; margin-top:3px;">Death Reg</div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($a['name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);">Representative</div>
                        </td>
                        <td>
                          <div style="font-weight:700;"><?= htmlspecialchars($a['deceased']) ?></div>
                          <span style="font-size:0.7rem; font-weight:700; padding:1px 6px; border-radius:8px; <?= ($a['deceased_sex'] ?? '') === 'Female' ? 'background:#fce7f3;color:#9d174d;' : 'background:#e0e7ff;color:#3730a3;' ?>">
                            <?= htmlspecialchars($a['deceased_sex'] ?? 'Male') ?>
                          </span>
                        </td>
                        <td style="font-size:0.82rem;">
                          <?= !empty($a['submitted_at']) ? date('M d, Y', strtotime($a['submitted_at'])) : (!empty($a['date']) ? date('M d, Y', strtotime($a['date'])) : '—') ?>
                        </td>
                        <td style="font-size:0.82rem;">
                          <?php if(!empty($a['cert_date'])): ?>
                            <div style="font-weight:600; color:var(--damayan-dark);"><?= date('M d, Y', strtotime($a['cert_date'])) ?></div>
                            <span class="mini-badge done" style="font-size:0.65rem; margin-top:2px; display:inline-flex; align-items:center; gap:2px;"><svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Received</span>
                          <?php else: ?>
                            <span style="color:var(--text-muted); font-style:italic;">Pending</span>
                          <?php endif; ?>
                        </td>
                        <td style="font-size:0.82rem; color:var(--text-muted);">
                          <?= !empty($a['archived_at']) ? date('M d, Y', strtotime($a['archived_at'])) : '—' ?>
                          <?php if(!empty($a['archived_by'])): ?>
                            <div style="font-size:0.7rem; color:var(--text-muted);">by <?= htmlspecialchars($a['archived_by']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <span class="badge-status badge-pending" style="background:#fee2e2; color:#991b1b;">
                            Archived
                          </span>
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                          <button type="button" class="btn-topbar primary" style="padding:5px 12px; font-size:0.78rem; display:inline-flex; align-items:center; gap:4px; font-weight:700;" onclick="restoreBurialRecord('<?= htmlspecialchars($a['id']) ?>', '<?= htmlspecialchars(addslashes($a['deceased'])) ?>')">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                            Restore
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
  </div>

  <?php include __DIR__ . '/burial_modal.php'; ?>
  <?php include __DIR__ . '/encode_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    function toggleViewMode(mode) {
      const btnReq = document.getElementById('btn-view-requests');
      const btnArc = document.getElementById('btn-view-archive');
      const conReq = document.getElementById('container-requests');
      const conArc = document.getElementById('container-archive');

      if(mode === 'archive') {
        if(btnArc) btnArc.classList.add('active');
        if(btnReq) btnReq.classList.remove('active');
        if(conArc) conArc.style.display = 'block';
        if(conReq) conReq.style.display = 'none';
      } else {
        if(btnReq) btnReq.classList.add('active');
        if(btnArc) btnArc.classList.remove('active');
        if(conReq) conReq.style.display = 'block';
        if(conArc) conArc.style.display = 'none';
      }
    }

    async function restoreBurialRecord(id, deceasedName) {
      if (!confirm(`Restore burial record for "${deceasedName}" (Ref #${id}) back to active records?`)) {
        return;
      }

      try {
        const res = await fetch('<?= url('/admin/damayan/burial/restore') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
          showAlert('Restored', data.message || 'Burial record restored to active records.', 'success');
          setTimeout(() => location.reload(), 800);
        } else {
          showAlert('Error', data.error || 'Failed to restore record.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'A network error occurred while restoring the record.', 'error');
      }
    }

    function onSearchInput(e) {
      if(e.key === 'Enter') {
        const url = new URL(window.location.href);
        url.searchParams.set('search', e.target.value.trim());
        window.location.href = url.toString();
      }
    }

    async function markAsReceived(id, deceasedName) {
      document.querySelectorAll('.action-menu-dropdown').forEach(d => { d.classList.remove('show'); d.style.top = '-9999px'; });
      if (!confirm(`Mark death certificate as received for "${deceasedName}" (Ref #${id})?`)) {
        return;
      }

      try {
        const res = await fetch('<?= url('/admin/damayan/burial/mark-received') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
          showAlert('Received', data.message || 'Death certificate marked as received.', 'success');
          
          const dateCell = document.getElementById('cert-date-cell-' + id);
          if (dateCell && data.formatted_date) {
            dateCell.innerHTML = `<div style="font-weight:600; color:var(--damayan-dark);">${data.formatted_date}</div>
                                  <span class="mini-badge done" style="font-size:0.65rem; margin-top:2px; display:inline-flex; align-items:center; gap:2px;"><svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg> Received</span>`;
          }

          const btn = document.getElementById('btn-mark-received-' + id);
          if (btn) {
            btn.outerHTML = `<span class="mini-badge done" style="padding:4px 8px; font-size:0.72rem; display:inline-flex; align-items:center; gap:3px;">
                               <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                               Received
                             </span>`;
          } else {
            setTimeout(() => location.reload(), 800);
          }
        } else {
          showAlert('Error', data.error || 'Failed to mark certificate as received.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'A network error occurred while updating certificate status.', 'error');
      }
    }

    async function deleteBurialRecord(id, deceasedName) {
      document.querySelectorAll('.action-menu-dropdown').forEach(d => { d.classList.remove('show'); d.style.top = '-9999px'; });
      if (!confirm(`Are you sure you want to delete the burial record for "${deceasedName}" (Ref #${id})?\n\nThis will permanently delete the record. This action cannot be undone.`)) {
        return;
      }

      try {
        const res = await fetch('<?= url('/admin/damayan/burial/delete') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });
        const data = await res.json();
        if (data.success) {
          showAlert('Deleted', data.message || 'Record deleted successfully.', 'success');
          const row = document.getElementById('burial-row-' + id);
          if (row) {
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
          } else {
            setTimeout(() => location.reload(), 800);
          }
        } else {
          showAlert('Error', data.error || 'Failed to delete record.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Error', 'A network error occurred while deleting the record.', 'error');
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const caseId = params.get('id');
      if(caseId) {
        openCaseModal(caseId);
      }
    });
  </script>
</body>
</html>
