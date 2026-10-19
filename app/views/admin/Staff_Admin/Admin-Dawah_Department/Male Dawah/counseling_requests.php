<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Counseling Requests</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --primary-male: #14532D;
      --primary-male-dark: #0f3e21;
      --primary-male-light: #f0fdf4;
      --gold-accent: #D4AF37;
    }

    /* Status Badges */
    .badge-status {
      padding: 5px 12px; border-radius: 20px; font-size: 0.74rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.03em; display: inline-flex; align-items: center; justify-content: center;
    }
    /* Pending badge color based on Tenant Admin: soft transparent amber, simple and clean */
    .badge-status.badge-pending { 
      background: rgba(199, 154, 43, 0.1); 
      color: #c79a2b; 
      border: 1px solid rgba(199, 154, 43, 0.2); 
    }
    .badge-status.badge-accepted { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .badge-status.badge-rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Action Buttons */
    .action-buttons-wrap {
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-action-view {
      background: #f1f5f9; color: #1e293b; border: 1px solid #cbd5e1;
      padding: 6px 12px; border-radius: 8px; font-size: 0.82rem; font-weight: 700;
      cursor: pointer; transition: all 0.2s ease;
      display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-action-view svg {
      width: 15px; height: 15px; fill: currentColor;
    }
    .btn-action-view:hover {
      background: #e2e8f0; color: #0f172a; border-color: #94a3b8; transform: translateY(-1px);
    }
    .btn-action-accept {
      background: #14532D; color: #ffffff; border: 1px solid #0f3e21;
      padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 700;
      cursor: pointer; transition: all 0.2s ease;
      display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-action-accept:hover:not(:disabled) {
      background: #0f3e21; box-shadow: 0 2px 8px rgba(20, 83, 45, 0.35); transform: translateY(-1px);
    }
    .btn-action-reject {
      background: #dc2626; color: #ffffff; border: 1px solid #b91c1c;
      padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 700;
      cursor: pointer; transition: all 0.2s ease;
      display: inline-flex; align-items: center; justify-content: center;
    }
    .btn-action-reject:hover:not(:disabled) {
      background: #b91c1c; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.35); transform: translateY(-1px);
    }
    .btn-action-accept:disabled, .btn-action-reject:disabled {
      opacity: 0.6; cursor: not-allowed;
    }

    /* Final Status Display */
    .action-final-status {
      font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;
      display: inline-block; text-transform: uppercase; letter-spacing: 0.03em;
    }
    .action-final-status.accepted { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .action-final-status.rejected { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    /* Ref Monospace Tag */
    .ref-tag {
      font-family: monospace; font-size: 0.88rem; font-weight: 800; color: var(--primary-male);
      background: var(--primary-male-light); padding: 4px 8px; border-radius: 6px; border: 1px solid #bbf7d0;
      display: inline-block;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'counseling_requests';
      $dawah_type = $dawah_type ?? 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Counseling Requests</div>
            <div class="top-bar-subtitle"><?= $dawah_type === 'female' ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Review submitted counseling forms, accept or reject requests</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Counseling</span>
          <span class="sep">›</span>
          <span class="current">Counseling Request</span>
        </div>

        <?php
          $totalCount = count($records ?? []);
          $pendingCount = 0;
          $acceptedCount = 0;
          $rejectedCount = 0;
          foreach ($records ?? [] as $rec) {
            $st = strtolower($rec['status'] ?? 'pending');
            if (in_array($st, ['pending', 'under_review'])) {
              $pendingCount++;
            } elseif (in_array($st, ['accepted', 'approved', 'confirmed', 'completed'])) {
              $acceptedCount++;
            } elseif (in_array($st, ['rejected', 'cancelled'])) {
              $rejectedCount++;
            }
          }
        ?>

        <!-- Status Filter Tabs -->
        <div class="tab-nav">
          <button type="button" class="tab-btn active" onclick="switchStatusTab('all', this)">
            All Requests <span class="tab-count" id="count-all"><?= $totalCount ?></span>
          </button>
          <button type="button" class="tab-btn" onclick="switchStatusTab('pending', this)">
            Pending <span class="tab-count pending" id="count-pending"><?= $pendingCount ?></span>
          </button>
          <button type="button" class="tab-btn" onclick="switchStatusTab('accepted', this)">
            Accepted <span class="tab-count approved" id="count-accepted"><?= $acceptedCount ?></span>
          </button>
          <button type="button" class="tab-btn" onclick="switchStatusTab('rejected', this)">
            Rejected <span class="tab-count rejected" id="count-rejected"><?= $rejectedCount ?></span>
          </button>
        </div>

        <!-- Counseling Requests Table Card -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--primary);margin-right:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
              Submitted Counseling Forms
            </h6>
            <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">
              Total Requests: <?= count($records ?? []) ?>
            </div>
          </div>

          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Client Name</th>
                    <th>Date Submitted</th>
                    <th>Preferred Schedule</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($records)): ?>
                    <tr>
                      <td colspan="6" style="text-align:center; padding:48px 20px; color:var(--text-muted);">
                        <svg viewBox="0 0 24 24" style="width:36px;height:36px;fill:#cbd5e1;margin-bottom:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        <div style="font-weight:700; font-size:0.95rem; color:#475569;">No counseling requests submitted yet.</div>
                        <div style="font-size:0.8rem; margin-top:4px;">When users submit the counseling form, requests will appear here automatically.</div>
                      </td>
                    </tr>
                  <?php else: ?>
                    <tr id="tab-empty-row" style="display:none;">
                      <td colspan="6" style="text-align:center; padding:48px 20px; color:var(--text-muted);">
                        <svg viewBox="0 0 24 24" style="width:36px;height:36px;fill:#cbd5e1;margin-bottom:8px;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        <div id="tab-empty-text" style="font-weight:700; font-size:0.95rem; color:#475569;">No counseling requests found in this tab.</div>
                      </td>
                    </tr>
                    <?php foreach ($records as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#' . ($dawah_type === 'female' ? 'FC-' : 'MC-') . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $clientName = $r['full_name'] ?? ($r['client_name'] ?? trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')));
                        if (empty($clientName)) $clientName = ($dawah_type === 'female' ? 'Anonymous Sister' : 'Anonymous Brother');

                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $prefSchedule = !empty($r['preferred_date']) 
                            ? date('M d, Y', strtotime($r['preferred_date'])) . (!empty($r['preferred_time']) ? ' (' . htmlspecialchars($r['preferred_time']) . ')' : '') 
                            : 'Flexible Schedule';

                        $rawStatus = strtolower($r['status'] ?? 'pending');
                        $isPending = in_array($rawStatus, ['pending', 'under_review']);
                        $isAccepted = in_array($rawStatus, ['accepted', 'approved', 'confirmed']);
                        $isRejected = ($rawStatus === 'rejected');
                        $isCompleted = ($rawStatus === 'completed');

                        // Normalized display status & category
                        $displayStatus = 'Pending';
                        $statusClass = 'badge-pending';
                        $statusCategory = 'pending';
                        if ($isAccepted) {
                            $displayStatus = 'Accepted';
                            $statusClass = 'badge-accepted';
                            $statusCategory = 'accepted';
                        } elseif ($isRejected) {
                            $displayStatus = 'Rejected';
                            $statusClass = 'badge-rejected';
                            $statusCategory = 'rejected';
                        } elseif ($isCompleted) {
                            $displayStatus = 'Completed';
                            $statusClass = 'badge-accepted';
                            $statusCategory = 'accepted';
                        }
                      ?>
                      <tr id="row-<?= $id ?>" data-status="<?= $statusCategory ?>">
                        <td>
                          <span class="ref-tag"><?= $reqId ?></span>
                        </td>
                        <td style="font-weight:700; color:#111827;">
                          <?= htmlspecialchars($clientName) ?>
                        </td>
                        <td style="color:#4b5563;">
                          <?= $dateSubmitted ?>
                        </td>
                        <td style="color:#374151; font-weight:600;">
                          <?= $prefSchedule ?>
                        </td>
                        <td id="status-col-<?= $id ?>">
                          <span class="badge-status <?= $statusClass ?>">
                            <?= $displayStatus ?>
                          </span>
                        </td>
                        <td id="actions-col-<?= $id ?>">
                          <div class="action-buttons-wrap">
                            <button type="button" class="btn-action-view" onclick="openViewModal(<?= $id ?>)" title="View Request Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                              View
                            </button>
                            <?php if ($isPending): ?>
                              <button type="button" class="btn-action-accept" onclick="processRequest(<?= $id ?>, 'accept')">
                                Accept
                              </button>
                              <button type="button" class="btn-action-reject" onclick="processRequest(<?= $id ?>, 'reject')">
                                Reject
                              </button>
                            <?php elseif ($isAccepted): ?>
                              <span class="action-final-status accepted">Accepted</span>
                            <?php elseif ($isRejected): ?>
                              <span class="action-final-status rejected">Rejected</span>
                            <?php elseif ($isCompleted): ?>
                              <span class="action-final-status accepted">Completed</span>
                            <?php else: ?>
                              <span class="action-final-status accepted"><?= htmlspecialchars(ucfirst($rawStatus)) ?></span>
                            <?php endif; ?>
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

  <!-- View Request Details Modal -->
  <div id="view-request-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:560px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #D4AF37);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#14532D;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
          Counseling Request Details
        </h5>
        <button type="button" onclick="closeViewModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.04em;">Request ID</span>
            <div id="vm-id" style="font-family:monospace; font-size:1.05rem; font-weight:800; color:#14532D; margin-top:2px;">#MC-0000</div>
          </div>
          <div id="vm-status-wrap">
            <span id="vm-status" class="badge-status badge-pending">Pending</span>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Client Name</div>
            <div id="vm-name" style="font-weight:700; font-size:0.95rem; color:#111;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Contact Number</div>
            <div id="vm-contact" style="font-weight:600; font-size:0.92rem; color:#374151;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Age &amp; Civil Status</div>
            <div id="vm-age-civil" style="font-weight:600; font-size:0.9rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Current Address</div>
            <div id="vm-address" style="font-weight:500; font-size:0.88rem; color:#4b5563;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Preferred Schedule</div>
            <div id="vm-schedule" style="font-weight:700; font-size:0.92rem; color:#14532D;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Date Submitted</div>
            <div id="vm-date" style="font-weight:600; font-size:0.9rem; color:#4b5563;">—</div>
          </div>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin-bottom:16px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Reason / Category</div>
          <div id="vm-reason" style="font-weight:700; font-size:0.92rem; color:#b45309;">—</div>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Problem Statement / Client Message</div>
          <div id="vm-desc" style="font-size:0.88rem; color:#374151; line-height:1.6; background:#f9fafb; padding:10px 12px; border-radius:6px; min-height:48px;">—</div>
        </div>
      </div>

      <div style="padding:14px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc;">
        <button type="button" onclick="closeViewModal()" style="padding:8px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const dawahPrefix = '<?= ($dawah_type ?? "male") === "female" ? "FC-" : "MC-" ?>';

    // Build records lookup map
    const rawRecords = <?= json_encode(array_values($records ?? [])) ?>;
    const recordsMap = {};
    rawRecords.forEach(r => { if (r && r.id) recordsMap[r.id] = r; });

    function openViewModal(id) {
      const r = recordsMap[id];
      if (!r) return;

      const modal = document.getElementById('view-request-modal');
      if (!modal) return;

      const reqId = '#' + dawahPrefix + String(r.id).padStart(4, '0');
      const clientName = r.full_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous User';
      const contact = r.contact_number || r.phone || r.email || 'None';
      const ageCivil = [r.age ? `${r.age} yrs old` : '', r.civil_status || ''].filter(Boolean).join(' • ') || 'Not specified';
      const address = r.address || r.current_address || 'None provided';
      const schedule = r.preferred_schedule || [r.preferred_date || '', r.preferred_time || ''].filter(Boolean).join(' ') || 'Open Schedule';
      const dateSub = (r.created_at || '').substring(0, 10) || 'N/A';
      const reason = r.reason || r.concern_category || 'General Counseling';
      const desc = r.description || r.message || 'No additional details provided.';
      const status = r.status || 'Pending';

      document.getElementById('vm-id').textContent = reqId;
      document.getElementById('vm-name').textContent = clientName;
      document.getElementById('vm-contact').textContent = contact;
      document.getElementById('vm-age-civil').textContent = ageCivil;
      document.getElementById('vm-address').textContent = address;
      document.getElementById('vm-schedule').textContent = schedule;
      document.getElementById('vm-date').textContent = dateSub;
      document.getElementById('vm-reason').textContent = reason;
      document.getElementById('vm-desc').textContent = desc;

      const stLower = status.toLowerCase();
      let badgeClass = 'badge-pending';
      if (stLower === 'accepted' || stLower === 'confirmed' || stLower === 'approved') badgeClass = 'badge-accepted';
      else if (stLower === 'rejected' || stLower === 'cancelled') badgeClass = 'badge-rejected';

      document.getElementById('vm-status-wrap').innerHTML = `
        <span class="badge-status ${badgeClass}">
          ${status.charAt(0).toUpperCase() + status.slice(1)}
        </span>
      `;

      modal.style.display = 'flex';
    }

    function closeViewModal() {
      const modal = document.getElementById('view-request-modal');
      if (modal) modal.style.display = 'none';
    }

    document.getElementById('view-request-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeViewModal();
    });

    async function processRequest(id, action) {
      const actionsCell = document.getElementById(`actions-col-${id}`);
      const statusCell = document.getElementById(`status-col-${id}`);
      if (!actionsCell || !statusCell) return;

      const confirmMsg = action === 'accept'
        ? `Are you sure you want to ACCEPT request #${dawahPrefix}${String(id).padStart(4, '0')}?`
        : `Are you sure you want to REJECT request #${dawahPrefix}${String(id).padStart(4, '0')}?`;

      if (!confirm(confirmMsg)) {
        return;
      }

      // Disable buttons while processing
      const buttons = actionsCell.querySelectorAll('button');
      buttons.forEach(b => b.disabled = true);

      const endpoint = action === 'accept'
        ? '<?= url("/admin/dawah/counseling/accept") ?>'
        : '<?= url("/admin/dawah/counseling/reject-simple") ?>';

      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const res = await response.json();

        if (res.success) {
          if (recordsMap[id]) {
            recordsMap[id].status = (action === 'accept' ? 'Accepted' : 'Rejected');
          }

          const targetRow = document.getElementById(`row-${id}`);
          if (targetRow) {
            targetRow.setAttribute('data-status', action === 'accept' ? 'accepted' : 'rejected');
          }

          if (action === 'accept') {
            // Update status column
            statusCell.innerHTML = `
              <span class="badge-status badge-accepted">Accepted</span>
            `;
            // Replace actions column with View button and final status badge
            actionsCell.innerHTML = `
              <div class="action-buttons-wrap">
                <button type="button" class="btn-action-view" onclick="openViewModal(${id})" title="View Request Details">
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  View
                </button>
                <span class="action-final-status accepted">Accepted</span>
              </div>
            `;
            showAlert('Request Accepted', `Request #${dawahPrefix}${String(id).padStart(4, '0')} has been accepted successfully.`, 'success');
          } else {
            // Update status column
            statusCell.innerHTML = `
              <span class="badge-status badge-rejected">Rejected</span>
            `;
            // Replace actions column with View button and final status badge
            actionsCell.innerHTML = `
              <div class="action-buttons-wrap">
                <button type="button" class="btn-action-view" onclick="openViewModal(${id})" title="View Request Details">
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  View
                </button>
                <span class="action-final-status rejected">Rejected</span>
              </div>
            `;
            showAlert('Request Rejected', `Request #${dawahPrefix}${String(id).padStart(4, '0')} has been marked as rejected.`, 'info');
          }

          updateTabCounts();
          if (currentTab !== 'all') {
            const activeBtn = document.querySelector(`.tab-btn.active`);
            switchStatusTab(currentTab, activeBtn);
          }
        } else {
          alert(res.message || 'Failed to process request. Please try again.');
          buttons.forEach(b => b.disabled = false);
        }
      } catch (err) {
        console.error(err);
        alert('A network error occurred while processing the request.');
        buttons.forEach(b => b.disabled = false);
      }
    }

    // ── STATUS TAB FILTERING ──
    let currentTab = 'all';

    function switchStatusTab(tabName, btn) {
      currentTab = tabName;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');

      const rows = document.querySelectorAll('tbody tr[data-status]');
      let visibleCount = 0;

      rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (tabName === 'all' || rowStatus === tabName) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      const emptyRow = document.getElementById('tab-empty-row');
      if (emptyRow) {
        if (visibleCount === 0 && rows.length > 0) {
          emptyRow.style.display = '';
          const label = tabName.charAt(0).toUpperCase() + tabName.slice(1);
          document.getElementById('tab-empty-text').textContent = `No ${label.toLowerCase()} counseling requests found.`;
        } else {
          emptyRow.style.display = 'none';
        }
      }
    }

    function updateTabCounts() {
      const rows = document.querySelectorAll('tbody tr[data-status]');
      let total = rows.length;
      let pending = 0;
      let accepted = 0;
      let rejected = 0;

      rows.forEach(r => {
        const st = r.getAttribute('data-status');
        if (st === 'pending') pending++;
        else if (st === 'accepted') accepted++;
        else if (st === 'rejected') rejected++;
      });

      const countAll = document.getElementById('count-all');
      const countPending = document.getElementById('count-pending');
      const countAccepted = document.getElementById('count-accepted');
      const countRejected = document.getElementById('count-rejected');

      if (countAll) countAll.textContent = total;
      if (countPending) countPending.textContent = pending;
      if (countAccepted) countAccepted.textContent = accepted;
      if (countRejected) countRejected.textContent = rejected;
    }
  </script>
</body>
</html>
