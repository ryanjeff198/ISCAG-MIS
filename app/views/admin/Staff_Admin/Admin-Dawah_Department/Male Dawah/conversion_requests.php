<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$reqList = $records ?? ($applications ?? []);
$pending_reports  = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? 'pending'), ['pending', 'under_review'])));
$approved_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['accepted', 'approved', 'confirmed', 'completed'])));
$rejected_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['rejected', 'cancelled', 'disapproved'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Islamic Conversion Requests</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* ── Apartment Admin Consistent Styling ── */
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      gap: 8px;
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }
    .insight-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.08);
      border-color: var(--accent);
    }
    .insight-card::after {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--border);
    }
    .insight-card.pending::after { background: var(--warning); }
    .insight-card.total::after { background: var(--primary); }
    .insight-card.approved::after { background: var(--success); }
    .insight-card.rejected::after { background: var(--danger); }

    .insight-label {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .insight-value {
      font-size: 1.8rem;
      font-weight: 800;
      color: var(--primary-dark);
      line-height: 1;
    }
    .insight-value.info { color: var(--primary); }
    .insight-value.success { color: var(--success); }
    .insight-value.danger { color: var(--danger); }
    .insight-value.warning { color: var(--warning); }
    
    .insight-card svg {
      position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
      width: 24px; height: 24px; opacity: 0.15;
    }

    /* ── Table Typography & Badges ── */
    .td-id { font-weight: 800; color: var(--primary-dark); font-size: 0.85rem; }
    
    .badge-status {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 12px; border-radius: 12px; font-size: 0.72rem; font-weight: 800;
      text-transform: uppercase; letter-spacing: 0.04em;
    }
    .badge-status.pending, .badge-status.badge-pending { 
      background: rgba(199, 154, 43, 0.1); color: #b08d2e; 
    }
    .badge-status.approved, .badge-status.badge-approved { 
      background: rgba(47, 138, 96, 0.1); color: #2f8a60; 
    }
    .badge-status.rejected, .badge-status.badge-rejected { 
      background: rgba(139, 46, 46, 0.1); color: #8b2e2e; 
    }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    /* ── Action Buttons ── */
    .actions-cell { display: flex; align-items: center; gap: 6px; }
    .btn-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      border: none; background: transparent;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: var(--text-muted); }
    .btn-circle:hover { transform: translateY(-1px); background: rgba(0,0,0,0.05); }
    
    .btn-circle.eye svg { fill: #1E90FF; }
    .btn-circle.eye:hover { background: rgba(30,144,255,0.1); }
    
    .btn-circle.check svg { fill: #2f8a60; }
    .btn-circle.check:hover { background: rgba(47,138,96,0.1); }
    
    .btn-circle.reject svg { fill: #8b2e2e; }
    .btn-circle.reject:hover { background: rgba(139,46,46,0.1); }

    .btn-circle:disabled { opacity: 0.4; cursor: not-allowed; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'conversion_requests';
      $dawah_type = $dawah_type ?? 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Islamic Conversion Requests</div>
            <div class="top-bar-subtitle"><?= $dawah_type === 'female' ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Review and process conversion to Islam applications</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Conversion</span>
          <span class="sep">›</span>
          <span class="current">Conversion Requests</span>
        </div>

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card pending" onclick="switchTab('pending', document.querySelector('.tab-btn:nth-child(1)'))">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="stat-pending"><?= count($pending_reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
          </div>
          <div class="insight-card approved" onclick="switchTab('approved', document.querySelector('.tab-btn:nth-child(2)'))">
            <div class="insight-label">Accepted / Approved</div>
            <div class="insight-value success" id="stat-approved"><?= count($approved_reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
          </div>
          <div class="insight-card rejected" onclick="switchTab('rejected', document.querySelector('.tab-btn:nth-child(3)'))">
            <div class="insight-label">Rejected</div>
            <div class="insight-value danger" id="stat-rejected"><?= count($rejected_reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" /></svg>
          </div>
          <div class="insight-card total" onclick="switchTab('pending', document.querySelector('.tab-btn:nth-child(1)'))">
            <div class="insight-label">Total Applications</div>
            <div class="insight-value info" id="stat-total"><?= count($reqList) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z" /></svg>
          </div>
        </div>

        <!-- Tab Nav -->
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending', this)">
            Pending <span class="tab-count pending" id="count-pending"><?= count($pending_reports) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('approved', this)">
            Approved <span class="tab-count approved" id="count-approved"><?= count($approved_reports) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('rejected', this)">
            Rejected <span class="tab-count rejected" id="count-rejected"><?= count($rejected_reports) ?></span>
          </button>
        </div>

        <!-- ═══ PENDING TAB ═══ -->
        <div class="section-card tab-panel" id="tab-pending">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              Pending Review
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($pending_reports) ?> record<?= count($pending_reports) !== 1 ? 's' : '' ?></span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>User Name</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-pending">
                  <?php if (empty($pending_reports)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No pending conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($pending_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = 'User #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($userName) ?></div>
                          <?php if (!empty($r['adopted_name'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              Adopted: <?= htmlspecialchars($r['adopted_name']) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status pending">
                            <span class="status-dot"></span> Pending
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-circle eye" onclick="openViewModal(<?= $id ?>)" title="View Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                            <button class="btn-circle check" onclick="processRequest(<?= $id ?>, 'accept')" title="Accept">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </button>
                            <button class="btn-circle reject" onclick="processRequest(<?= $id ?>, 'reject')" title="Reject">
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

        <!-- ═══ APPROVED TAB ═══ -->
        <div class="section-card tab-panel" id="tab-approved" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              Approved Conversion Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($approved_reports) ?> record<?= count($approved_reports) !== 1 ? 's' : '' ?></span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>User Name</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-approved">
                  <?php if (empty($approved_reports)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No approved conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($approved_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = 'User #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($userName) ?></div>
                          <?php if (!empty($r['adopted_name'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              Adopted: <?= htmlspecialchars($r['adopted_name']) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status approved">
                            <span class="status-dot"></span> Accepted
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-circle eye" onclick="openViewModal(<?= $id ?>)" title="View Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
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

        <!-- ═══ REJECTED TAB ═══ -->
        <div class="section-card tab-panel" id="tab-rejected" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
              Rejected Conversion Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($rejected_reports) ?> record<?= count($rejected_reports) !== 1 ? 's' : '' ?></span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>User Name</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-rejected">
                  <?php if (empty($rejected_reports)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No rejected conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($rejected_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = 'User #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($userName) ?></div>
                          <?php if (!empty($r['adopted_name'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              Adopted: <?= htmlspecialchars($r['adopted_name']) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status rejected">
                            <span class="status-dot"></span> Rejected
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-circle eye" onclick="openViewModal(<?= $id ?>)" title="View Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
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
      </div>
    </div>
  </div>

  <!-- View Conversion Request Details Modal -->
  <div id="view-request-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:620px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #D4AF37);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#14532D;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
          Conversion Request Details
        </h5>
        <button type="button" onclick="closeViewModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.04em;">Request ID</span>
            <div id="vm-id" class="td-id" style="font-size:1.1rem; margin-top:2px;">#CR-0000</div>
          </div>
          <div id="vm-status-wrap">
            <span id="vm-status" class="badge-status pending"><span class="status-dot"></span> Pending</span>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Submitting User</div>
            <div id="vm-user-name" style="font-weight:700; font-size:0.95rem; color:#111;">—</div>
            <div id="vm-user-email" style="font-size:0.8rem; color:#6b7280; margin-top:2px;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Date Submitted</div>
            <div id="vm-date" style="font-weight:600; font-size:0.92rem; color:#374151;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #bbf7d0; background:#f0fdf4; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534; margin-bottom:3px;">Legal Name</div>
            <div id="vm-legal-name" style="font-weight:700; font-size:0.95rem; color:#14532D;">—</div>
          </div>
          <div style="border:1px solid #bbf7d0; background:#f0fdf4; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534; margin-bottom:3px;">Adopted Islamic Name</div>
            <div id="vm-adopted-name" style="font-weight:700; font-size:0.95rem; color:#14532D;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Former Religion</div>
            <div id="vm-former-rel" style="font-weight:600; font-size:0.9rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Conversion Date</div>
            <div id="vm-conv-date" style="font-weight:700; font-size:0.9rem; color:#14532D;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Sex / Age / Civil Status</div>
            <div id="vm-sex-age-civil" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Present Residence</div>
            <div id="vm-residence" style="font-weight:500; font-size:0.88rem; color:#4b5563;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Father's Name &amp; Religion</div>
            <div id="vm-father" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Mother's Name &amp; Religion</div>
            <div id="vm-mother" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Witness 1</div>
            <div id="vm-w1" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Witness 2</div>
            <div id="vm-w2" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
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

    // ── Tab Switching Logic (Apartment Admin Pattern) ──
    function switchTab(tabName, btn) {
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      const panel = document.getElementById('tab-' + tabName);
      if (panel) panel.style.display = 'block';
      if (btn) btn.classList.add('active');
    }

    // Build records lookup map
    const rawRecords = <?= json_encode(array_values($reqList ?? [])) ?>;
    const recordsMap = {};
    rawRecords.forEach(r => { if (r && r.id) recordsMap[r.id] = r; });

    function openViewModal(id) {
      const r = recordsMap[id];
      if (!r) return;

      const modal = document.getElementById('view-request-modal');
      if (!modal) return;

      const reqId = '#CR-' + String(r.id).padStart(4, '0');
      const accountUserName = `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'User #' + (r.tenant_id || r.id);
      const legalName = `${r.fname || ''} ${r.mname ? r.mname + ' ' : ''}${r.lname || ''}`.trim() || '—';
      const adoptedName = r.adopted_name || '—';
      const formerRel = r.former_religion || '—';
      const convDate = r.conversion_date ? new Date(r.conversion_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
      const dateSub = (r.created_at || '').substring(0, 10) || '—';
      const sexAgeCivil = [r.sex || 'Male', r.age ? `${r.age} yrs` : '', r.civil_status || ''].filter(Boolean).join(' • ') || '—';
      const residence = r.residence || r.pob || '—';
      const father = `${r.father_name || '—'} (${r.father_religion || '—'})`;
      const mother = `${r.mother_name || '—'} (${r.mother_religion || '—'})`;
      const w1 = r.witness1_name ? `${r.witness1_name}${r.witness1_address ? ' (' + r.witness1_address + ')' : ''}` : '—';
      const w2 = r.witness2_name ? `${r.witness2_name}${r.witness2_address ? ' (' + r.witness2_address + ')' : ''}` : '—';
      const status = r.status || 'Pending';

      document.getElementById('vm-id').textContent = reqId;
      document.getElementById('vm-user-name').textContent = accountUserName;
      document.getElementById('vm-user-email').textContent = r.email || 'No email registered';
      document.getElementById('vm-date').textContent = dateSub;
      document.getElementById('vm-legal-name').textContent = legalName;
      document.getElementById('vm-adopted-name').textContent = adoptedName;
      document.getElementById('vm-former-rel').textContent = formerRel;
      document.getElementById('vm-conv-date').textContent = convDate;
      document.getElementById('vm-sex-age-civil').textContent = sexAgeCivil;
      document.getElementById('vm-residence').textContent = residence;
      document.getElementById('vm-father').textContent = father;
      document.getElementById('vm-mother').textContent = mother;
      document.getElementById('vm-w1').textContent = w1;
      document.getElementById('vm-w2').textContent = w2;

      const stLower = status.toLowerCase();
      let badgeClass = 'pending';
      let statusText = 'Pending';
      if (['accepted', 'confirmed', 'approved'].includes(stLower)) {
        badgeClass = 'approved';
        statusText = 'Accepted';
      } else if (['rejected', 'cancelled', 'disapproved'].includes(stLower)) {
        badgeClass = 'rejected';
        statusText = 'Rejected';
      }

      document.getElementById('vm-status-wrap').innerHTML = `
        <span class="badge-status ${badgeClass}">
          <span class="status-dot"></span> ${statusText}
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
      const reqRef = '#CR-' + String(id).padStart(4, '0');
      const confirmMsg = action === 'accept'
        ? `Are you sure you want to ACCEPT conversion request ${reqRef}?`
        : `Are you sure you want to REJECT conversion request ${reqRef}?`;

      if (!confirm(confirmMsg)) {
        return;
      }

      const endpoint = action === 'accept'
        ? '<?= url("/admin/dawah/conversion/accept") ?>'
        : '<?= url("/admin/dawah/conversion/reject") ?>';

      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const res = await response.json();

        if (res.success) {
          if (typeof showAlert === 'function') {
            showAlert(action === 'accept' ? 'Request Accepted' : 'Request Rejected', res.message || `Request ${reqRef} has been updated.`, 'success');
          } else {
            alert(res.message || `Request ${reqRef} has been updated.`);
          }
          setTimeout(() => location.reload(), 1000);
        } else {
          alert(res.message || 'Failed to process request. Please try again.');
        }
      } catch (err) {
        console.error(err);
        alert('A network error occurred while processing the request.');
      }
    }
  </script>
</body>
</html>
