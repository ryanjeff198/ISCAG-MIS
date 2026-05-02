<?php $active_page = 'maintenance'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Maintenance Requests</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    .actions-cell { display: flex; gap: 8px; }
    .btn-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      border: none; background: transparent;
      cursor: pointer; transition: all 0.2s; padding: 0;
    }
    .btn-circle svg { width: 18px; height: 18px; }
    .btn-circle.eye svg { fill: var(--info); }
    .btn-circle.check svg { fill: var(--success); }
    .btn-circle.reject svg { fill: var(--danger); }
    .btn-circle:hover { background: rgba(0,0,0,0.05); }

    .modal-content { max-width: 600px; }
    .detail-row { display: flex; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
    .detail-label { width: 140px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); }
    .detail-value { flex: 1; font-size: 0.9rem; color: var(--text-main); font-weight: 600; }
    .attachment-preview { width: 100%; border-radius: 8px; margin-top: 12px; border: 1px solid var(--border); max-height: 300px; object-fit: contain; background: #000; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" />
          <div>
            <div class="top-bar-title">Maintenance Requests</div>
            <div class="top-bar-subtitle">Manage facility repairs and tenant service requests</div>
          </div>
        </div>
        <div class="top-bar-actions">
           <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <div class="section-card">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;fill:var(--primary);"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
              All Requests
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <table class="mis-table">
              <thead>
                <tr>
                  <th>Ref #</th>
                  <th>Tenant Name</th>
                  <th>Category</th>
                  <th>Description</th>
                  <th>Submitted</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($requests)): ?>
                  <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No maintenance requests found.</td></tr>
                <?php else: ?>
                  <?php foreach ($requests as $r): ?>
                    <tr>
                      <td class="td-id"><?= $r['id'] ?></td>
                      <td style="font-weight:600;"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                      <td><span style="font-weight:700; color:var(--primary);"><?= $r['category'] ?></span></td>
                      <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['description']) ?></td>
                      <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                      <td>
                        <?php 
                          $statusClass = 'badge-pending';
                          if($r['status'] === 'In Progress') $statusClass = 'badge-approved';
                          if($r['status'] === 'Completed') $statusClass = 'badge-approved';
                          if($r['status'] === 'Rejected') $statusClass = 'badge-rejected';
                        ?>
                        <span class="badge-status <?= $statusClass ?>"><?= $r['status'] ?></span>
                      </td>
                      <td>
                        <div class="actions-cell">
                          <button class="btn-circle eye" onclick="viewMaintenance(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                          </button>
                          <?php if ($r['status'] === 'Pending'): ?>
                            <button class="btn-circle check" onclick="approveMaintenance(<?= $r['id'] ?>)" title="Approve">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </button>
                            <button class="btn-circle reject" onclick="rejectMaintenance(<?= $r['id'] ?>)" title="Reject">
                              <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                            </button>
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
    </main>
  </div>

  <!-- Detail Modal -->
  <div class="modal-backdrop" id="maintenance-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Request Details</h5>
        <button class="modal-close" onclick="closeModal('maintenance-modal')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="detail-row">
          <div class="detail-label">Tenant</div>
          <div class="detail-value" id="d-tenant"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Category</div>
          <div class="detail-value" id="d-category"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Status</div>
          <div class="detail-value" id="d-status"></div>
        </div>
        <div class="detail-row" style="flex-direction:column; border:none; margin-top:10px;">
          <div class="detail-label">Description</div>
          <div class="detail-value" style="margin-top:8px; font-weight:400; line-height:1.5;" id="d-desc"></div>
        </div>
        <div id="d-attachment-area" style="display:none;">
          <div class="detail-label" style="margin-top:15px;">Attachment</div>
          <img class="attachment-preview" id="d-attachment" src="" alt="Attachment" onclick="window.open(this.src)" />
        </div>
      </div>
      <div class="modal-footer" id="modal-footer">
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('admin');

    function viewMaintenance(r) {
      document.getElementById('d-tenant').textContent = `${r.first_name} ${r.last_name} (${r.email})`;
      document.getElementById('d-category').textContent = r.category;
      document.getElementById('d-status').textContent = r.status;
      document.getElementById('d-desc').textContent = r.description;
      
      const attachment = document.getElementById('d-attachment-area');
      if (r.attachment) {
        document.getElementById('d-attachment').src = '<?= url("/") ?>' + r.attachment;
        attachment.style.display = 'block';
      } else {
        attachment.style.display = 'none';
      }

      const footer = document.getElementById('modal-footer');
      if (r.status === 'Pending') {
        footer.innerHTML = `
          <button class="btn-topbar" onclick="closeModal('maintenance-modal')">Close</button>
          <div style="margin-left:auto; display:flex; gap:10px;">
            <button class="btn-topbar" style="color:var(--danger);" onclick="rejectMaintenance(${r.id})">Reject</button>
            <button class="btn-topbar primary" onclick="approveMaintenance(${r.id})">Approve & Start</button>
          </div>
        `;
      } else {
        footer.innerHTML = `<button class="btn-topbar" onclick="closeModal('maintenance-modal')">Close</button>`;
      }

      openModal('maintenance-modal');
    }

    function approveMaintenance(id) {
      if (confirm('Approve this maintenance request and set to "In Progress"?')) {
        window.location.href = '<?= url("/admin/mis_admin/maintenance/approve") ?>?id=' + id;
      }
    }

    function rejectMaintenance(id) {
      const reason = prompt('Please enter a reason for rejection:', 'Not approved at this time.');
      if (reason !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= url("/admin/mis_admin/maintenance/reject") ?>?id=' + id;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'reason';
        input.value = reason;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }
    }

    setupModalClose('maintenance-modal');

    // Auto-open if ID provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const openId = urlParams.get('id');
    if (openId) {
      const requests = <?= json_encode($requests) ?>;
      const target = requests.find(r => r.id == openId);
      if (target) viewMaintenance(target);
    }
  </script>
</body>
</html>
