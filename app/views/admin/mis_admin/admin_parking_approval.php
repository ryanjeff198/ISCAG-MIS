<?php $active_page = 'parking_approval'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Parking Approval</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Admin parking rental application review and approval" />
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
            <div class="top-bar-title">Parking Rental Approval</div>
            <div class="top-bar-subtitle">Review and approve tenant parking rental applications</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="stat-pending"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'PENDING' || empty($r['status']))) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Approved</div>
            <div class="insight-value success" id="stat-approved"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'APPROVED')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Rejected</div>
            <div class="insight-value danger" id="stat-rejected"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'REJECTED')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value info" id="stat-total"><?= count($reports) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Available Slots</div>
            <div class="insight-value">15</div>
          </div>
        </div>

        <!-- TAB NAV -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending', this)">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
            Pending Review
            <span class="tab-count pending" id="tab-pending-count"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'PENDING' || empty($r['status']))) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('approved', this)">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
            Approved
            <span class="tab-count approved" id="tab-approved-count"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'APPROVED')) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('rejected', this)">
            <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" /></svg>
            Rejected
            <span class="tab-count rejected" id="tab-rejected-count"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'REJECTED')) ?></span>
          </button>
        </div>

        <!-- PENDING TAB -->
        <div class="section-card tab-panel active" id="tab-pending">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" /></svg>
              Pending Applications
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Parking ID</th>
                    <th>Applicant</th>
                    <th>Vehicle</th>
                    <th>Plate No.</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="pending-tbody">
                  <?php 
                    $pending = array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'PENDING' || empty($r['status']));
                    if (empty($pending)): 
                  ?>
                      <tr><td colspan="6"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Pending Applications</h4><p>All parking applications have been reviewed.</p></div></td></tr>
                  <?php else: ?>
                      <?php foreach ($pending as $a): ?>
                        <tr>
                          <td class="td-id"><?= $a['id'] ?></td>
                          <td style="font-weight:600;"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '') ?: ($a['ownername'] ?? '—')) ?></td>
                          <td><?= htmlspecialchars($a['vehiclename'] ?? '—') ?></td>
                          <td style="font-weight:700;letter-spacing:0.04em;"><?= htmlspecialchars($a['plateno'] ?? '—') ?></td>
                          <td><?= date('M d, Y', strtotime($a['submitted_at'])) ?></td>
                          <td>
                            <div class="action-menu">
                              <button class="action-menu-btn" onclick="toggleActionMenu(this, event)" title="Actions">
                                <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                              </button>
                              <div class="action-menu-dropdown">
                                <button class="action-menu-item" onclick="openReview(<?= htmlspecialchars(json_encode($a)) ?>)">
                                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                  Review Application
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

        <!-- APPROVED TAB -->
        <div class="section-card tab-panel" id="tab-approved" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
              Approved Parking Rentals
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Parking ID</th>
                    <th>Tenant</th>
                    <th>Vehicle</th>
                    <th>Plate No.</th>
                    <th>Type</th>
                    <th>Date Started</th>
                    <th>Approved On</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="approved-tbody">
                  <?php 
                    $approved = array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'APPROVED');
                    if (empty($approved)): 
                  ?>
                      <tr><td colspan="8"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg><h4>No Approved Parking Yet</h4><p>Approved parking rentals will appear here.</p></div></td></tr>
                  <?php else: ?>
                      <?php foreach ($approved as $a): ?>
                        <tr>
                          <td class="td-id"><?= $a['id'] ?></td>
                          <td style="font-weight:600;"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '') ?: ($a['ownername'] ?? '—')) ?></td>
                          <td><?= htmlspecialchars($a['vehiclename'] ?? '—') ?></td>
                          <td style="font-weight:700;letter-spacing:0.04em;"><?= htmlspecialchars($a['plateno'] ?? '—') ?></td>
                          <td><?= htmlspecialchars($a['typeofvehicle'] ?? '—') ?></td>
                          <td><?= date('M d, Y', strtotime($a['datestarted'] ?? '')) ?></td>
                          <td><?= !empty($a['updated_at']) ? date('M d, Y', strtotime($a['updated_at'])) : '—' ?></td>
                          <td><span class="badge-status badge-approved"><span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span> Approved</span></td>
                        </tr>
                      <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- REJECTED TAB -->
        <div class="section-card tab-panel" id="tab-rejected" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" /></svg>
              Rejected Applications
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Parking ID</th>
                    <th>Applicant</th>
                    <th>Vehicle</th>
                    <th>Plate No.</th>
                    <th>Feedback</th>
                    <th>Rejected On</th>
                  </tr>
                </thead>
                <tbody id="rejected-tbody">
                  <?php 
                    $rejected = array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'REJECTED');
                    if (empty($rejected)): 
                  ?>
                      <tr><td colspan="6"><div class="empty-state"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg><h4>No Rejected Applications</h4><p>No applications have been rejected.</p></div></td></tr>
                  <?php else: ?>
                      <?php foreach ($rejected as $a): ?>
                        <tr>
                          <td class="td-id"><?= $a['id'] ?></td>
                          <td style="font-weight:600;"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '') ?: ($a['ownername'] ?? '—')) ?></td>
                          <td><?= htmlspecialchars($a['vehiclename'] ?? '—') ?></td>
                          <td style="font-weight:700;letter-spacing:0.04em;"><?= htmlspecialchars($a['plateno'] ?? '—') ?></td>
                          <td style="font-size:0.82rem;color:var(--danger);max-width:220px;"><?= htmlspecialchars($a['remarks'] ?? '—') ?></td>
                          <td><?= !empty($a['updated_at']) ? date('M d, Y', strtotime($a['updated_at'])) : '—' ?></td>
                        </tr>
                      <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>

  <!-- ═══ REVIEW MODAL ═══ -->
  <div class="modal-backdrop review-modal" id="review-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5 id="review-modal-title">Parking Application Review</h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body" id="review-modal-body">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer" id="review-modal-footer">
        <!-- Action buttons populated by JS -->
      </div>
    </div>
  </div>

  <!-- ═══ REJECT MODAL ═══ -->
  <div class="modal-backdrop" id="reject-modal" style="display:none;">
    <div class="modal-content" style="max-width:480px;">
      <div class="modal-bar"></div>
      <div class="modal-header">
        <h5>
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--danger);">
            <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
          </svg>
          Reject Application
        </h5>
        <button class="modal-close"><svg viewBox="0 0 24 24">
            <path
              d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
          </svg></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Application Reference</label>
          <p id="reject-ref" style="font-weight:700;font-size:0.9rem;"></p>
        </div>
        <div class="form-group">
          <label class="form-label">Reason for Rejection *</label>
          <textarea class="feedback-area" id="reject-text"
            placeholder="Describe why this application is being rejected (e.g., 'Incomplete vehicle information, plate number does not match records.')"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-topbar" onclick="closeModal('reject-modal')">Cancel</button>
        <button class="btn-topbar primary" style="background:var(--danger);border-color:var(--danger);"
          id="reject-submit-btn">Reject Application</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    // ══ INIT ══
    standardizePage('admin');
    setCurrentRole(ROLES.MIS_ADMIN);

    // ── TAB SWITCHING ──
    function switchTab(tabName, btn) {
      // Hide all panels
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      
      // Deactivate all tab buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      
      // Show target panel and activate button
      const panel = document.getElementById('tab-' + tabName);
      if (panel) {
        panel.style.display = 'block';
        panel.classList.add('active');
      }
      if (btn) btn.classList.add('active');
    }

    // ══ REVIEW MODAL ══
    function openReview(a) {
      const tenantName = (a.first_name || '') + ' ' + (a.last_name || '') || (a.ownername || '—');
      document.getElementById('review-modal-title').textContent = 'Review — ' + a.id + ' · ' + tenantName;

      document.getElementById('review-modal-body').innerHTML = `
        <div class="vehicle-highlight">
          <div class="vehicle-icon-wrap">
            <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
          </div>
          <div class="vehicle-info">
            <h4>${a.vehiclename || '—'}</h4>
            <p>${a.typeofvehicle || 'Vehicle'} · Owner: ${a.ownername || '—'}</p>
          </div>
          <div class="vehicle-plate">${a.plateno || '—'}</div>
        </div>

        <div class="detail-section-title">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
          Personal Information
        </div>
        <div class="detail-grid">
          <div class="detail-item"><label>Full Name</label><p>${tenantName}</p></div>
          <div class="detail-item"><label>Email</label><p>${a.email || '—'}</p></div>
          <div class="detail-item"><label>Tenant ID</label><p>${a.tenant_id}</p></div>
          <div class="detail-item"><label>Contact No.</label><p>${a.contactnum || '—'}</p></div>
        </div>

        <div class="detail-section-title">
          <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
          Rental Period
        </div>
        <div class="detail-grid">
          <div class="detail-item"><label>Date Started</label><p>${a.datestarted ? new Date(a.datestarted).toLocaleDateString() : '—'}</p></div>
          <div class="detail-item"><label>Submitted At</label><p>${new Date(a.submitted_at).toLocaleDateString()}</p></div>
        </div>
      `;

      // Footer actions
      const status = (a.status || 'PENDING').toUpperCase();
      if (status === 'PENDING') {
        document.getElementById('review-modal-footer').innerHTML = `
          <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
          <button class="btn-topbar" style="color:var(--danger);border-color:var(--danger);" onclick="openReject('${a.id}')">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:var(--danger);"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            Reject
          </button>
          <button class="btn-topbar primary" onclick="approveApp('${a.id}')">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:white;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            Approve Parking
          </button>
        `;
      } else {
        document.getElementById('review-modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }

      openModal('review-modal');
    }

    // ══ APPROVE ══
    function approveApp(appId) {
      if (confirm('Approve this parking application?')) {
        window.location.href = '<?= url("/admin/mis_admin/parking/approve") ?>?id=' + appId;
      }
    }

    // ══ REJECT ══
    let currentRejectId = null;
    function openReject(appId) {
      currentRejectId = appId;
      document.getElementById('reject-ref').textContent = appId;
      document.getElementById('reject-text').value = '';
      closeModal('review-modal');
      setTimeout(() => openModal('reject-modal'), 250);
    }

    document.getElementById('reject-submit-btn').addEventListener('click', () => {
      const text = document.getElementById('reject-text').value.trim();
      if (!text) {
        alert('Please provide a reason for rejection.');
        return;
      }
      if (confirm('Reject this parking application?')) {
        window.location.href = '<?= url("/admin/mis_admin/parking/reject") ?>?id=' + currentRejectId + '&reason=' + encodeURIComponent(text);
      }
    });

    // ══ MODAL SETUP ══
    setupModalClose('review-modal');
    setupModalClose('reject-modal');
  </script>
</body>

</html>

