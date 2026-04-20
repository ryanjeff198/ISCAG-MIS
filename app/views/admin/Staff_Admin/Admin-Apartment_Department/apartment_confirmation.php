<?php $active_page = 'apartment_confirmation'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Application Confirmation</title>
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    /* ═══════════════════════════════════════════
       FORM DOCUMENT STYLES (Paper-like layout)
       ═══════════════════════════════════════════ */
    .form-document {
      background: white;
      max-width: 100%;
      margin: 0 auto;
      border-radius: 8px;
      overflow: hidden;
    }

    .form-doc-header {
      background: linear-gradient(135deg, #fafdf9 0%, #f0f5f2 100%);
      padding: 20px 24px 15px;
      border-bottom: 3px solid var(--primary);
      position: relative;
    }

    .form-doc-header-top {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 8px;
    }

    .form-doc-header-logo {
      width: 60px; height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary);
      flex-shrink: 0;
    }

    .form-doc-header-text {
      flex: 1;
      text-align: center;
    }

    .form-doc-header-text .arabic-line { font-size: 0.9rem; color: var(--primary-dark); margin-bottom: 1px; direction: rtl; font-weight: 600; }
    .form-doc-header-text .org-name-ar { font-size: 0.8rem; color: var(--primary-dark); direction: rtl; margin-bottom: 3px; font-weight: 600; }
    .form-doc-header-text .org-name-en { font-size: 0.75rem; font-weight: 700; color: var(--primary-dark); text-transform: uppercase; letter-spacing: 0.06em; }
    .form-doc-header-text .sec-reg { font-size: 0.6rem; color: var(--text-muted); margin-top: 1px; }

    .form-doc-header-logo-right {
      width: 60px; height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--accent);
      flex-shrink: 0;
    }

    .form-doc-title-bar { text-align: center; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 15px; }
    .form-doc-title {
      font-family: 'Lora', serif;
      font-size: 0.9rem; font-weight: 700;
      color: white; background: var(--primary-dark);
      padding: 6px 24px; border-radius: 4px;
      letter-spacing: 0.08em; text-transform: uppercase;
    }

    .photo-display-box {
      width: 90px; height: 100px;
      border: 2px solid var(--border);
      border-radius: 4px;
      background: #fafdf9;
      overflow: hidden;
      flex-shrink: 0;
    }
    .photo-display-box img { width: 100%; height: 100%; object-fit: cover; }

    .date-photo-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 24px 8px; }
    .date-group { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; }
    .date-group label { font-weight: 700; color: var(--text-main); text-transform: uppercase; }

    .form-doc-body { padding: 15px 24px 20px; }
    .doc-section-title {
      font-family: 'Lora', serif;
      font-size: 0.8rem; font-weight: 700;
      color: var(--primary-dark); text-transform: uppercase;
      padding: 8px 0 6px; border-bottom: 2px solid var(--primary);
      margin-bottom: 12px; margin-top: 18px;
      display: flex; align-items: center; gap: 6px;
    }
    .doc-section-title:first-child { margin-top: 0; }
    .doc-section-title svg { width: 14px; height: 14px; fill: var(--accent); }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .info-table td { border: 1px solid var(--border); padding: 0; vertical-align: middle; }
    .info-table .field-label {
      font-size: 0.68rem; font-weight: 700;
      color: var(--text-muted); text-transform: uppercase;
      padding: 5px 8px; background: #f8faf9;
      white-space: nowrap; min-width: 100px;
    }
    .info-table .field-value { padding: 6px 10px; font-size: 0.82rem; color: var(--text-main); font-weight: 600; }

    .family-doc-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .family-doc-table thead th {
      background: var(--primary-dark); color: white;
      font-size: 0.68rem; font-weight: 700;
      text-transform: uppercase; padding: 7px 8px;
      border: 1px solid var(--primary-dark); text-align: left;
    }
    .family-doc-table tbody td { border: 1px solid var(--border); padding: 7px 8px; font-size: 0.8rem; color: var(--text-main); }

    /* ── Verification Cards ── */
    .verification-grid {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 10px;
    }
    .verify-card {
      border: 1px solid var(--border); border-radius: 8px;
      padding: 10px; background: #f8faf9;
      display: flex; flex-direction: column; gap: 8px;
    }
    .verify-card-title { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .verify-img-wrap {
      width: 100%; height: 80px; border-radius: 4px; overflow: hidden;
      background: #e8ece9; cursor: pointer; border: 1px solid var(--border);
    }
    .verify-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s; }
    .verify-img-wrap:hover img { transform: scale(1.05); }

    .img-preview-overlay {
      position: fixed; inset: 0; z-index: 100001;
      background: rgba(0,0,0,0.85); backdrop-filter: blur(5px);
      display: flex; align-items: center; justify-content: center; padding: 20px;
    }
    .img-preview-overlay img { max-width: 90%; max-height: 90%; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.5); }
    .img-preview-close {
      position: absolute; top: 20px; right: 20px;
      width: 40px; height: 40px; border-radius: 50%;
      background: white; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
    }

    .modal-content { max-width: 950px; }

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
    .btn-circle.eye:hover svg { fill: var(--info); }
    
    .btn-circle.check svg { fill: var(--success); }
    .btn-circle.check:hover { background: rgba(46,125,85,0.1); }
    .btn-circle.check:hover svg { fill: var(--success); }
    
    .btn-circle.reject svg { fill: var(--danger); }
    .btn-circle.reject:hover { background: rgba(139,46,46,0.1); }
    .btn-circle.reject:hover svg { fill: var(--danger); }
    
    .actions-cell { display: flex; gap: 6px; }
    
    .reject-reason-wrap {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .reject-select {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      font-size: 0.8rem;
      background: var(--danger);
      color: white;
      font-weight: 700;
      outline: none;
      cursor: pointer;
    }
    .reject-select:focus { border-color: var(--danger); }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <button class="sidebar-toggle" id="sidebar-toggle" title="Toggle sidebar">
        <svg viewBox="0 0 24 24">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
        </svg>
      </button>
      <div class="sidebar-header">
        <div class="sidebar-brand">
          <img src="<?= asset('assets/logo.jpg') ?>" style="max-width:48px;max-height:48px;border-radius:8px;" alt="ISCAG" />
          <div class="brand-text"><strong>ISCAG MIS</strong><span>Apartment Staff</span></div>
        </div>
      </div>
      <div class="sidebar-user">
        <div class="user-avatar" id="nav-avatar" style="background:var(--accent);">AK</div>
        <div class="user-info"><strong id="nav-name">Apartment Staff</strong><span>Staff Admin</span></div>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-section-label">Admin</div>
        <a href="<?= url('/admin/apartment') ?>" class="nav-item" data-tooltip="Dashboard">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" />
          </svg>
          <span class="nav-item-label">Dashboard</span>
        </a>
        <a href="<?= url('/admin/apartment/profile') ?>" class="nav-item" data-tooltip="Profile">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
          </svg>
          <span class="nav-item-label">My Profile</span>
        </a>
        <a href="<?= url('/admin/apartment/confirmation') ?>" class="nav-item active" data-tooltip="Applications">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
          </svg>
          <span class="nav-item-label">Review Applications</span>
        </a>
        <div class="nav-section-label">Management</div>
        <a href="<?= url('/admin/apartment/info') ?>" class="nav-item" data-tooltip="Apartment Info">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z" />
          </svg>
          <span class="nav-item-label">Apartment Info</span>
        </a>
        <a href="<?= url('/admin/payment') ?>" class="nav-item" data-tooltip="Billing & Payment">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
          </svg>
          <span class="nav-item-label">Billing & Payment</span>
        </a>
        <a href="<?= url('/admin/apartment/soa') ?>" class="nav-item" data-tooltip="SOA">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13zM9 13h6v2H9v-2zm6 4H9v2h6v-2z" />
          </svg>
          <span class="nav-item-label">Statement of Account</span>
        </a>
        <a href="<?= url('/admin/apartment/notifications') ?>" class="nav-item" data-tooltip="Notifications">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
          </svg>
          <span class="nav-item-label">Notifications</span>
        </a>
      </nav>
      <div class="sidebar-footer">
        <a href="<?= url('/logout') ?>" class="nav-item" data-tooltip="Logout">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" />
          </svg>
          <span class="nav-item-label">Logout</span>
        </a>
      </div>
    </aside>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">Apartment Application Review</div>
            <div class="top-bar-subtitle">Verify family details and employment for apartment allocation</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/apartment') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning"><?= count(array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'pending')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value info"><?= count($reports) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Verified (Staff)</div>
            <div class="insight-value success"><?= count(array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'approved')) ?></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Rejected</div>
            <div class="insight-value danger"><?= count(array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'rejected')) ?></div>
          </div>
        </div>

         <?php
           $pending_reports  = array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'pending');
           $approved_reports = array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'approved');
           $rejected_reports = array_filter($reports, fn($r) => strtolower($r['status'] ?? '') === 'rejected');
         ?>

         <div class="tab-nav">
           <button class="tab-btn active" onclick="switchTab('pending', this)">Pending <span class="tab-count pending"><?= count($pending_reports) ?></span></button>
           <button class="tab-btn" onclick="switchTab('approved', this)">Approved <span class="tab-count approved"><?= count($approved_reports) ?></span></button>
           <button class="tab-btn" onclick="switchTab('rejected', this)">Rejected <span class="tab-count rejected"><?= count($rejected_reports) ?></span></button>
         </div>

         <!-- ═══ PENDING TABLE ═══ -->
         <div class="section-card tab-panel" id="tab-pending">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                Pending Review
              </h6>
              <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($pending_reports) ?> record<?= count($pending_reports) !== 1 ? 's' : '' ?></span>
            </div>
            <div class="section-card-body" style="padding:0;">
               <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Tenant ID</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Room Type</th>
                      <th>Submitted</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($pending_reports)): ?>
                       <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No pending applications.</td></tr>
                    <?php else: ?>
                       <?php foreach ($pending_reports as $r): ?>
                          <tr>
                             <td class="td-id"><?= $r['id'] ?></td>
                             <td style="font-weight:600;"><?= $r['tenant_id'] ?></td>
                             <td><?= htmlspecialchars($r['first_name'] ?? '—') ?></td>
                             <td><?= htmlspecialchars($r['last_name'] ?? '—') ?></td>
                             <td style="color:var(--primary);font-weight:700;"><?= $r['roomtype'] ?></td>
                             <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                             <td><span class="badge-status badge-pending"><?= $r['status'] ?></span></td>
                             <td>
                                <div class="actions-cell">
                                   <button class="btn-circle eye" onclick="openReview(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                                      <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                   </button>
                                   <button class="btn-circle check" onclick="approveApp(<?= $r['id'] ?>)" title="Approve">
                                      <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                   </button>
                                   <button class="btn-circle reject" onclick="rejectApp(<?= $r['id'] ?>)" title="Reject">
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

         <!-- ═══ APPROVED TABLE ═══ -->
         <div class="section-card tab-panel" id="tab-approved" style="display:none;">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                Approved Applications
              </h6>
              <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($approved_reports) ?> record<?= count($approved_reports) !== 1 ? 's' : '' ?></span>
            </div>
            <div class="section-card-body" style="padding:0;">
               <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Tenant ID</th>
                      <th>Full Name</th>
                      <th>Room Type</th>
                      <th>Submitted</th>
                      <th>Approved At</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($approved_reports)): ?>
                       <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No approved applications yet.</td></tr>
                    <?php else: ?>
                       <?php foreach ($approved_reports as $r): ?>
                          <tr>
                             <td class="td-id"><?= $r['id'] ?></td>
                             <td style="font-weight:600;"><?= $r['tenant_id'] ?></td>
                             <td><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?></td>
                             <td style="color:var(--primary);font-weight:700;"><?= $r['roomtype'] ?></td>
                             <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                             <td><?= !empty($r['updated_at']) ? date('M d, Y', strtotime($r['updated_at'])) : '—' ?></td>
                             <td><span class="badge-status badge-approved"><?= $r['status'] ?></span></td>
                             <td>
                                <button class="btn-circle eye" onclick="openReview(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                                   <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                </button>
                             </td>
                          </tr>
                       <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
               </table>
            </div>
         </div>

         <!-- ═══ REJECTED TABLE ═══ -->
         <div class="section-card tab-panel" id="tab-rejected" style="display:none;">
            <div class="section-card-header">
              <h6>
                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                Rejected Applications
              </h6>
              <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($rejected_reports) ?> record<?= count($rejected_reports) !== 1 ? 's' : '' ?></span>
            </div>
            <div class="section-card-body" style="padding:0;">
               <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Tenant ID</th>
                      <th>Full Name</th>
                      <th>Room Type</th>
                      <th>Submitted</th>
                      <th>Reason</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($rejected_reports)): ?>
                       <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No rejected applications.</td></tr>
                    <?php else: ?>
                       <?php foreach ($rejected_reports as $r): ?>
                          <tr>
                             <td class="td-id"><?= $r['id'] ?></td>
                             <td style="font-weight:600;"><?= $r['tenant_id'] ?></td>
                             <td><?= htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?></td>
                             <td style="color:var(--primary);font-weight:700;"><?= $r['roomtype'] ?></td>
                             <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                             <td style="font-size:0.82rem;color:var(--danger);font-weight:600;"><?= htmlspecialchars($r['reject_reason'] ?? '—') ?></td>
                             <td><span class="badge-status badge-rejected"><?= $r['status'] ?></span></td>
                             <td>
                                <button class="btn-circle eye" onclick="openReview(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                                   <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
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
    </main>
  </div>

  <!-- Review Modal -->
  <div class="modal-backdrop" id="review-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modal-title">Review Application</h5>
        <button class="modal-close" onclick="closeModal('review-modal')">×</button>
      </div>
      <div class="modal-body" id="modal-body" style="padding:0; background: #f0f4f2;">
        <!-- Overhauled Paper Layout will be injected here -->
      </div>
      <div class="modal-footer" id="modal-footer" style="background: white; border-top: 1px solid var(--border);"></div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    initAdminData();
    initSidebar();

    // ── Tab Switching Logic ──
    function switchTab(tabName, btn) {
      // Hide all panels
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      // Deactivate all tab buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      // Show target panel and activate button
      const panel = document.getElementById('tab-' + tabName);
      if (panel) panel.style.display = 'block';
      if (btn) btn.classList.add('active');
    }

    function formatDate(dateStr) {
      if (!dateStr) return '—';
      const date = new Date(dateStr);
      if (isNaN(date)) return dateStr;
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
    }

    function openReview(r) {
      document.getElementById('modal-title').textContent = `Full Details — Record #${r.id} (Tenant ${r.tenant_id})`;
      
      const baseUrl = '<?= url("/admin/mis_admin/tenant_image") ?>';
      const tenantId = r.tenant_id;

      // Handle family data if available
      let familyHtml = '<tr><td colspan="5" style="text-align:center;color:#999;padding:15px;">No family members listed.</td></tr>';
      try {
        const familyData = typeof r.family_data === 'string' ? JSON.parse(r.family_data || '[]') : (r.family_data || []);
        if (Array.isArray(familyData) && familyData.length > 0) {
          familyHtml = familyData.map((f, i) => `<tr><td>${i+1}</td><td>${f.name || '—'}</td><td>${f.relation || '—'}</td><td>${f.age || '—'}</td><td>${f.religion || 'Islam'}</td></tr>`).join('');
        }
      } catch(e) {
        console.error("Family parsing error:", e);
      }

      document.getElementById('modal-body').innerHTML = `
        <div class="form-document">
          <div class="form-doc-header">
            <div class="form-doc-header-top">
              <img src="<?= asset('assets/logo.jpg') ?>" class="form-doc-header-logo" alt="ISCAG" />
              <div class="form-doc-header-text">
                <div class="arabic-line">المؤسسة الإسلامية للدعوة والتوجيه</div>
                <div class="org-name-ar">مؤسسة الدعوة الإسلامية الفلبينية</div>
                <div class="org-name-en">Islamic Society for Call and Guidance</div>
                <div class="sec-reg">SEC REG. NO. 114526</div>
              </div>
              <img src="<?= asset('assets/logo.jpg') ?>" class="form-doc-header-logo-right" alt="ISCAG" />
            </div>
            <div class="form-doc-title-bar">
              <div class="form-doc-title">Apartment Application Details</div>
            </div>
          </div>

          <div class="date-photo-row">
            <div class="date-group">
              <label>Date Applied:</label>
              <span style="border-bottom:1px solid #ccc; padding:0 10px;">${formatDate(r.submitted_at)}</span>
            </div>
            <div class="photo-display-box" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=picture')">
               <img src="${baseUrl}?uid=${tenantId}&type=picture" onerror="this.src='https://via.placeholder.com/90x100?text=2x2+Photo'" alt="Applicant Photo" />
            </div>
          </div>

          <div class="form-doc-body">
            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Personal Information
            </div>
            <table class="info-table">
              <tr>
                <td class="field-label">Family Name</td>
                <td class="field-value">${r.familyname || r.last_name || '—'}</td>
                <td class="field-label">Given Name</td>
                <td class="field-value">${r.givenname || r.first_name || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Middle Initial</td>
                <td class="field-value">${r.middlename || '—'}</td>
                <td class="field-label">Muslim Name</td>
                <td class="field-value">${r.muslimname || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Civil Status</td>
                <td class="field-value">${r.civil_status || '—'}</td>
                <td class="field-label">Address</td>
                <td class="field-value">${r.address || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Sex</td>
                <td class="field-value" style="text-transform:capitalize;">${r.sex || '—'}</td>
                <td class="field-label">Age</td>
                <td class="field-value">${r.age || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Date of Birth</td>
                <td class="field-value">${r.birthdate ? formatDate(r.birthdate) : '—'}</td>
                <td class="field-label">Place of Birth</td>
                <td class="field-value">${r.pob || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Tribal Affiliation</td>
                <td class="field-value">${r.tribalaffliation || '—'}</td>
                <td class="field-label">Contact No.</td>
                <td class="field-value">${r.contactnum || '—'}</td>
              </tr>
            </table>

            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
              Apartment Selection Info
            </div>
            <table class="info-table">
              <tr>
                <td class="field-label">Preferred Unit Type</td>
                <td class="field-value" style="color:var(--primary-dark); font-weight:800; font-size:1rem;">${r.roomtype || '—'}</td>
                <td class="field-label">Number of Occupants</td>
                <td class="field-value">${r.numofmuslim || '—'} (including children)</td>
              </tr>
              <tr>
                <td class="field-label">ISCAG Students</td>
                <td class="field-value">${r.iscag_students || '0'} students</td>
                <td class="field-label">Date of Application</td>
                <td class="field-value">${formatDate(r.submitted_at)}</td>
              </tr>
            </table>

            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" /></svg>
              Employment & Income
            </div>
            <table class="info-table">
              <tr>
                <td class="field-label">Occupation</td>
                <td class="field-value">${r.occupation || '—'}</td>
                <td class="field-label">Monthly Income</td>
                <td class="field-value" style="color:var(--success); font-weight:700;">${r.monthly_income ? '₱' + parseInt(r.monthly_income).toLocaleString() : '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Company Name</td>
                <td class="field-value">${r.companyname || '—'}</td>
                <td class="field-label">Company Phone</td>
                <td class="field-value">${r.companyphone || '—'}</td>
              </tr>
              <tr>
                <td class="field-label">Company Address</td>
                <td class="field-value" colspan="3">${r.companyadd || '—'}</td>
              </tr>
            </table>

            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" /></svg>
              Family Members (Occupants)
            </div>
            <table class="family-doc-table">
              <thead><tr><th>#</th><th>Name</th><th>Relationship</th><th>Age</th><th>Religion</th></tr></thead>
              <tbody>${familyHtml}</tbody>
            </table>

            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" /></svg>
              Character Reference
            </div>
            <table class="info-table">
              <tr>
                <td class="field-label">Ref Name</td>
                <td class="field-value">${r.ref_name || '—'}</td>
                <td class="field-label">Ref Contact</td>
                <td class="field-value">${r.ref_contact || '—'}</td>
              </tr>
            </table>

            <div class="doc-section-title">
              <svg viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z" /></svg>
              Uploaded Documents Verification
            </div>
            <div class="verification-grid">
               <div class="verify-card">
                  <div class="verify-card-title">Proof of Income</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=proofofincome')">
                     <img src="${baseUrl}?uid=${tenantId}&type=proofofincome" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
               <div class="verify-card">
                  <div class="verify-card-title">Valid ID (Front)</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=valididfront')">
                     <img src="${baseUrl}?uid=${tenantId}&type=valididfront" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
               <div class="verify-card">
                  <div class="verify-card-title">Valid ID (Back)</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=valididback')">
                     <img src="${baseUrl}?uid=${tenantId}&type=valididback" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
               <div class="verify-card">
                  <div class="verify-card-title">Birth Certificate</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=birthcert')">
                     <img src="${baseUrl}?uid=${tenantId}&type=birthcert" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
               <div class="verify-card">
                  <div class="verify-card-title">NBI Clearance</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=nbi')">
                     <img src="${baseUrl}?uid=${tenantId}&type=nbi" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
               <div class="verify-card">
                  <div class="verify-card-title">2x2 Photo</div>
                  <div class="verify-img-wrap" onclick="viewFullImage('${baseUrl}?uid=${tenantId}&type=picture')">
                     <img src="${baseUrl}?uid=${tenantId}&type=picture" onerror="this.parentElement.innerHTML='<div style=\'font-size:0.65rem;color:#999;text-align:center;padding:30px 5px;\'>Not Uploaded</div>'" />
                  </div>
               </div>
            </div>

          </div>
        </div>
      `;

      if (r.status.toLowerCase() === 'pending') {
        document.getElementById('modal-footer').innerHTML = `
          <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
          <div id="footer-actions" style="display:flex; gap:10px; align-items:center; margin-left:auto;">
            <button class="btn-topbar" style="color:var(--danger);" onclick="showRejectReason(${r.id})">Reject Application</button>
            <button class="btn-topbar primary" onclick="approveApp(${r.id})">Confirm & Approve Details</button>
          </div>
        `;
      } else {
        document.getElementById('modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }
      openModal('review-modal');
    }

    function showRejectReason(id) {
      document.getElementById('footer-actions').innerHTML = `
        <div class="reject-reason-wrap">
          <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Select Reason:</span>
          <select class="reject-select" id="reject-reason">
            <option value="Incomplete Documents">Incomplete Documents</option>
            <option value="Unmatched Information">Unmatched Information</option>
            <option value="Invalid Identification">Invalid Identification</option>
            <option value="Poor Credit/Income Proof">Poor Credit/Income Proof</option>
            <option value="Other">Other</option>
          </select>
          <button class="btn-topbar" style="color:var(--danger); border-color:var(--danger);" onclick="rejectApp(${id})">Confirm Rejection</button>
          <button class="btn-topbar" onclick="resetFooterActions(${id})">Cancel</button>
        </div>
      `;
    }

    function resetFooterActions(id) {
       document.getElementById('footer-actions').innerHTML = `
          <button class="btn-topbar" style="color:var(--danger);" onclick="showRejectReason(${id})">Reject Application</button>
          <button class="btn-topbar primary" onclick="approveApp(${id})">Confirm & Approve Details</button>
       `;
    }

    function viewFullImage(src) {
      const overlay = document.createElement('div');
      overlay.className = 'img-preview-overlay';
      overlay.innerHTML = `
        <button class="img-preview-close" title="Close preview">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
        <img src="${src}" alt="Document Preview" />
      `;
      document.body.appendChild(overlay);
      overlay.querySelector('.img-preview-close').addEventListener('click', () => overlay.remove());
      overlay.addEventListener('click', (e) => { if(e.target === overlay) overlay.remove(); });
    }

    function approveApp(id) {
      if (confirm('Approve these application details?')) window.location.href = '<?= url('/admin/mis_admin/apartment_confirmation/approve') ?>?id=' + id;
    }
    function rejectApp(id) {
      const reasonEl = document.getElementById('reject-reason');
      const reason = reasonEl ? reasonEl.value : 'Other';
      if (confirm(`Reject this application for: "${reason}"?`)) {
        window.location.href = `<?= url('/admin/mis_admin/apartment_confirmation/reject') ?>?id=${id}&reason=${encodeURIComponent(reason)}`;
      }
    }

    setupModalClose('review-modal');
  </script>
</body>
</html>
