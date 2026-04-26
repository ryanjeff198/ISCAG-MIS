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
  <style>
    .btn-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      border: none; background: transparent;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: var(--text-muted); }
    .btn-circle:hover { transform: translateY(-1px); background: rgba(0,0,0,0.05); }
    
    .btn-circle.eye svg { fill: var(--info); }
    .btn-circle.eye:hover { background: rgba(30,144,255,0.1); }
    
    .btn-circle.check svg { fill: var(--success); }
    .btn-circle.check:hover { background: rgba(46,125,85,0.1); }
    
    .btn-circle.reject svg { fill: var(--danger); }
    .btn-circle.reject:hover { background: rgba(139,46,46,0.1); border: 1px solid var(--danger); }
    .btn-circle.reject:hover svg { fill: var(--danger); }
    
    /* Override shared green hover for danger buttons */
    .btn-topbar[style*="var(--danger)"]:hover {
      border-color: var(--danger) !important;
      color: var(--danger) !important;
      background: rgba(139, 46, 46, 0.05) !important;
    }

    .actions-cell { display: flex; gap: 6px; }
    
    .reject-reason-wrap {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .reject-select {
      height: 34px;
      padding: 0 12px;
      border: 1px solid var(--border);
      border-radius: 4px;
      font-size: 0.85rem;
      background: white;
      color: var(--text-main);
      outline: none;
      cursor: pointer;
      min-width: 200px;
      line-height: 1;
    }
    .reject-select:focus { border-color: var(--danger); }

    .btn-footer-action {
      height: 34px;
      min-width: 100px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0 16px;
      border-radius: 4px;
      font-size: 0.82rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s;
      border: 1px solid transparent;
      font-family: inherit;
      line-height: 1;
    }
    
    .btn-footer-action.confirm-reject {
      background: var(--danger);
      color: white;
      border-color: var(--danger);
    }
    .btn-footer-action.confirm-reject:hover {
      background: #a33535;
      border-color: #a33535;
    }
    
    .btn-footer-action.cancel-reject {
      background: #f4f6f5;
      color: var(--text-muted);
      border-color: var(--border);
    }
    .btn-footer-action.cancel-reject:hover {
      background: #e8ece9;
      color: var(--text-main);
    }

    /* ── UI/UX Enhancements ── */
    .insight-card { position: relative; overflow: hidden; }
    .insight-card::after {
      content: ''; position: absolute; right: -10px; bottom: -10px;
      width: 60px; height: 60px; background: currentColor; opacity: 0.05;
      border-radius: 50%;
    }
    .insight-card svg {
      position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
      width: 24px; height: 24px; opacity: 0.15;
    }
    
    .td-id { font-weight: 800; color: var(--primary-dark); font-size: 0.85rem; }
    .td-applicant { font-weight: 700; color: var(--text-main); }
    .td-plate { font-family: 'Source Code Pro', monospace; font-weight: 800; color: var(--accent); letter-spacing: 0.05em; background: #fffdf5; padding: 2px 8px; border-radius: 4px; border: 1px solid #f0e6cc; }
    
    .badge-status {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 12px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: 0.04em;
    }
    .badge-status.pending { background: rgba(199, 154, 43, 0.1); color: #b08d2e; }
    .badge-status.approved { background: rgba(47, 138, 96, 0.1); color: #2f8a60; }
    .badge-status.rejected { background: rgba(139, 46, 46, 0.1); color: #8b2e2e; }
    
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .status-dot.pulse { animation: statusPulse 2s infinite; }
    @keyframes statusPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    .detail-group { background: #f8faf9; border-radius: 10px; padding: 16px; border: 1px solid var(--border); margin-bottom: 20px; }
    .detail-group-title { font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .detail-group-title svg { width: 14px; height: 14px; fill: var(--primary); }
  </style>
</head>

<body>
  <div class="app-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <?php include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; ?>

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
          <button class="btn-topbar" style="background: rgba(47, 138, 96, 0.1); color: #2f8a60; border-color: rgba(47, 138, 96, 0.2); margin-right: 8px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;vertical-align:-3px;margin-right:4px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            Add Vehicle
          </button>
          <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card warning">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value" id="stat-pending"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'PENDING' || empty($r['status']))) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
          </div>
          <div class="insight-card success">
            <div class="insight-label">Approved</div>
            <div class="insight-value" id="stat-approved"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'APPROVED')) ?></div>
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
          </div>
          <div class="insight-card danger">
            <div class="insight-label">Rejected</div>
            <div class="insight-value" id="stat-rejected"><?= count(array_filter($reports, fn($r) => strtoupper($r['status'] ?? '') === 'REJECTED')) ?></div>
            <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" /></svg>
          </div>
          <div class="insight-card info">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value" id="stat-total"><?= count($reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" /></svg>
          </div>
          <div class="insight-card info">
            <div class="insight-label">Available Slots</div>
            <div class="insight-value">15</div>
            <svg viewBox="0 0 24 24"><path d="M21 16.5C21 16.88 20.79 17.21 20.47 17.38L12.57 21.82C12.41 21.94 12.21 22 12 22C11.79 22 11.59 21.94 11.43 21.82L3.53 17.38C3.21 17.21 3 16.88 3 16.5V7.5C3 7.12 3.21 6.79 3.53 6.62L11.43 2.18C11.59 2.06 11.79 2 12 2C12.21 2 12.41 2.06 12.57 2.18L20.47 6.62C20.79 6.79 21 7.12 21 7.5V16.5Z" /></svg>
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
                    <th>Type</th>
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
                          <td class="td-id">#PKG-<?= str_pad($a['id'], 4, '0', STR_PAD_LEFT) ?></td>
                          <td class="td-applicant"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '') ?: ($a['ownername'] ?? '—')) ?></td>
                          <td><?= htmlspecialchars($a['vehiclename'] ?? '—') ?></td>
                          <td style="font-size: 0.75rem; font-weight: 700; color: #666; text-transform: uppercase;"><?= htmlspecialchars($a['typeofvehicle'] ?? '—') ?></td>
                          <td><span class="td-plate"><?= htmlspecialchars($a['plateno'] ?? '—') ?></span></td>
                          <td><?= date('M d, Y', strtotime($a['submitted_at'])) ?></td>
                          <td>
                            <div class="actions-cell">
                               <button class="btn-circle eye" onclick="openReview(<?= htmlspecialchars(json_encode($a)) ?>)" title="View Details">
                                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                               </button>
                               <button class="btn-circle check" onclick="approveApp('<?= $a['id'] ?>')" title="Approve">
                                  <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                               </button>
                               <button class="btn-circle reject" onclick="openReview(<?= htmlspecialchars(json_encode($a)) ?>, true)" title="Reject">
                                  <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                               </button>
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
                          <td class="td-id">#PKG-<?= str_pad($a['id'], 4, '0', STR_PAD_LEFT) ?></td>
                          <td class="td-applicant"><?= htmlspecialchars(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? '') ?: ($a['ownername'] ?? '—')) ?></td>
                          <td><?= htmlspecialchars($a['vehiclename'] ?? '—') ?></td>
                          <td><span class="td-plate"><?= htmlspecialchars($a['plateno'] ?? '—') ?></span></td>
                          <td><?= htmlspecialchars($a['typeofvehicle'] ?? '—') ?></td>
                          <td><?= date('M d, Y', strtotime($a['datestarted'] ?? '')) ?></td>
                          <td><?= !empty($a['updated_at']) ? date('M d, Y', strtotime($a['updated_at'])) : '—' ?></td>
                          <td><span class="badge-status approved"><span class="status-dot"></span> Approved</span></td>
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
    function openReview(a, shouldAutoReject = false) {
      const tenantName = (a.first_name || '') + ' ' + (a.last_name || '') || (a.ownername || '—');
      document.getElementById('review-modal-title').textContent = 'Review — ' + a.id + ' · ' + tenantName;

      document.getElementById('review-modal-body').innerHTML = `
        <div class="vehicle-highlight">
          <div class="vehicle-icon-wrap">
            <svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
          </div>
          <div class="vehicle-info">
            <h4>${a.vehiclename || '—'}</h4>
            <p>${a.typeofvehicle || 'Vehicle'} · Submitted ${new Date(a.submitted_at).toLocaleDateString()}</p>
          </div>
          <div class="vehicle-plate">${a.plateno || '—'}</div>
        </div>

        <div class="detail-group">
          <div class="detail-group-title">
            <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
            Applicant Information
          </div>
          <div class="detail-grid">
            <div class="detail-item"><label>Full Name</label><p>${tenantName}</p></div>
            <div class="detail-item"><label>Tenant ID</label><p>#${a.tenant_id}</p></div>
            <div class="detail-item"><label>Email Address</label><p>${a.email || '—'}</p></div>
            <div class="detail-item"><label>Contact Number</label><p>${a.contactnum || '—'}</p></div>
          </div>
        </div>

        <div class="detail-group">
          <div class="detail-group-title">
            <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
            Rental Details
          </div>
          <div class="detail-grid">
            <div class="detail-item"><label>Rental Start Date</label><p>${a.datestarted ? new Date(a.datestarted).toLocaleDateString() : '—'}</p></div>
            <div class="detail-item"><label>Owner Name</label><p>${a.ownername || '—'}</p></div>
            <div class="detail-item full"><label>Status</label><p style="color:var(--warning); font-weight:700;">${(a.status || 'Pending Review').toUpperCase()}</p></div>
          </div>
        </div>
      `;

      // Footer actions
      const status = (a.status || 'PENDING').toUpperCase();
      if (status === 'PENDING') {
        document.getElementById('review-modal-footer').innerHTML = `
          <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
          <div id="footer-actions" style="display:flex; gap:10px; align-items:center; margin-left:auto;">
            <button class="btn-topbar" style="color:var(--danger);" onclick="showRejectReason('${a.id}')">Reject Application</button>
            <button class="btn-topbar primary" onclick="approveApp('${a.id}')">Confirm & Approve Details</button>
          </div>
        `;
      } else {
        document.getElementById('review-modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }

      openModal('review-modal');
      
      if (shouldAutoReject) {
        showRejectReason(a.id);
      }
    }

    function showRejectReason(id) {
      document.getElementById('footer-actions').innerHTML = `
        <div class="reject-reason-wrap">
          <span style="font-size:0.75rem; color:var(--text-muted); margin-right:2px;">Select Reason:</span>
          <select class="reject-select" id="reject-reason">
            <option value="Incomplete vehicle information">Incomplete vehicle information</option>
            <option value="Invalid plate number">Invalid plate number</option>
            <option value="Owner name mismatch">Owner name mismatch</option>
            <option value="Vehicle type not allowed">Vehicle type not allowed</option>
            <option value="Other">Other</option>
          </select>
          <button class="btn-footer-action confirm-reject" onclick="rejectApp('${id}')">Confirm Reject</button>
          <button class="btn-footer-action cancel-reject" onclick="resetFooterActions('${id}')">Cancel</button>
        </div>
      `;
    }

    function resetFooterActions(id) {
       document.getElementById('footer-actions').innerHTML = `
          <button class="btn-topbar" style="color:var(--danger);" onclick="showRejectReason('${id}')">Reject Application</button>
          <button class="btn-topbar primary" onclick="approveApp('${id}')">Confirm & Approve Details</button>
       `;
    }

    // ══ APPROVE ══
    function approveApp(appId) {
      if (confirm('Approve this parking application?')) {
        window.location.href = '<?= url("/admin/apartment/parking/approve") ?>?id=' + appId;
      }
    }

    // ══ REJECT ══
    function rejectApp(id) {
      const reasonEl = document.getElementById('reject-reason');
      const reason = reasonEl ? reasonEl.value : 'Other';
      if (confirm(`Reject this parking application for: "${reason}"?`)) {
        window.location.href = `<?= url('/admin/apartment/parking/reject') ?>?id=${id}&reason=${encodeURIComponent(reason)}`;
      }
    }

    // ══ MODAL SETUP ══
    setupModalClose('review-modal');
    setupModalClose('reject-modal');
  </script>
</body>
</html>
