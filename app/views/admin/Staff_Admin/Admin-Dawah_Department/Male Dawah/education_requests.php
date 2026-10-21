<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$reqList = $records ?? [];
$pending_reports  = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? 'pending'), ['pending', 'under_review'])));
$approved_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['accepted', 'active', 'enrolled', 'completed', 'approved'])));
$rejected_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['rejected', 'dropped', 'cancelled', 'disapproved'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Islamic Education Requests</title>
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

    .btn-circle.paid svg { fill: #16a34a; }
    .btn-circle.paid:hover { background: rgba(22,163,74,0.12); }

    .badge-pay-status {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.7rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      margin-top: 3px;
    }
    .badge-pay-status.paid {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .badge-pay-status.pending {
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
    }

    .btn-circle:disabled { opacity: 0.4; cursor: not-allowed; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'education_requests';
      $dawah_type = $dawah_type ?? 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Islamic Education Requests</div>
            <div class="top-bar-subtitle"><?= $dawah_type === 'female' ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Review student enrollment applications, accept or reject requests</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Islamic Education</span>
          <span class="sep">›</span>
          <span class="current">Education Requests</span>
        </div>

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card pending" onclick="switchTab('pending', document.querySelector('.tab-btn:nth-child(1)'))">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="stat-pending"><?= count($pending_reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
          </div>
          <div class="insight-card approved" onclick="switchTab('approved', document.querySelector('.tab-btn:nth-child(2)'))">
            <div class="insight-label">Enrolled / Accepted</div>
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
            <svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" /></svg>
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
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Payment Method</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-pending">
                  <?php if (empty($pending_reports)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No pending education enrollment requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($pending_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#ER-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $studentName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($studentName)) $studentName = 'Student #' . $id;
                        $program = $r['program_name'] ?? 'Islamic Studies';
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $pm = trim($r['payment_method'] ?? 'Cash');
                        $pc = trim($r['payment_channel'] ?? '');
                        $isOnline = (stripos($pm, 'online') !== false || stripos($pm, 'gcash') !== false || stripos($pm, 'maya') !== false);
                        $pmDisplay = $isOnline ? 'Online Payment' : 'Cash';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($studentName) ?></div>
                          <?php if (!empty($r['age'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:#6b7280; margin-top:2px;">
                              Age: <?= $r['age'] ?> yrs
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($program) ?></td>
                        <td>
                          <span style="font-weight:700; color:<?= $isOnline ? '#0d9488' : '#15803d' ?>;"><?= htmlspecialchars($pmDisplay) ?></span>
                          <?php if ($isOnline && !empty($pc)): ?>
                            <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($pc) ?></div>
                          <?php endif; ?>
                          <?php $isPaid = strtolower(trim($r['payment_status'] ?? 'pending')) === 'paid'; ?>
                          <div id="pay-tag-<?= $id ?>" class="badge-pay-status <?= $isPaid ? 'paid' : 'pending' ?>">
                            <?= $isPaid ? '✓ Paid' : 'Pending Fee' ?>
                          </div>
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
                            <?php if (!$isPaid): ?>
                              <button id="btn-pay-<?= $id ?>" class="btn-circle paid" onclick="markEnrollmentPaid(<?= $id ?>)" title="Mark as Paid (₱120.00)">
                                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                              </button>
                            <?php endif; ?>
                            <button class="btn-circle check" onclick="processRequest(<?= $id ?>, 'accept')" title="Accept">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </button>
                            <button class="btn-circle reject" onclick="openEduRejectModal(<?= $id ?>)" title="Reject Request">
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
              Approved Enrollment Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($approved_reports) ?> record<?= count($approved_reports) !== 1 ? 's' : '' ?></span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Payment Method</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-approved">
                  <?php if (empty($approved_reports)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No approved education enrollment requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($approved_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#ER-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $studentName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($studentName)) $studentName = 'Student #' . $id;
                        $program = $r['program_name'] ?? 'Islamic Studies';
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $pm = trim($r['payment_method'] ?? 'Cash');
                        $pc = trim($r['payment_channel'] ?? '');
                        $isOnline = (stripos($pm, 'online') !== false || stripos($pm, 'gcash') !== false || stripos($pm, 'maya') !== false);
                        $pmDisplay = $isOnline ? 'Online Payment' : 'Cash';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($studentName) ?></div>
                          <?php if (!empty($r['age'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:#6b7280; margin-top:2px;">
                              Age: <?= $r['age'] ?> yrs
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($program) ?></td>
                        <td>
                          <span style="font-weight:700; color:<?= $isOnline ? '#0d9488' : '#15803d' ?>;"><?= htmlspecialchars($pmDisplay) ?></span>
                          <?php if ($isOnline && !empty($pc)): ?>
                            <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($pc) ?></div>
                          <?php endif; ?>
                          <?php $isPaid = strtolower(trim($r['payment_status'] ?? 'pending')) === 'paid'; ?>
                          <div id="pay-tag-<?= $id ?>" class="badge-pay-status <?= $isPaid ? 'paid' : 'pending' ?>">
                            <?= $isPaid ? '✓ Paid' : 'Pending Fee' ?>
                          </div>
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
                            <?php if (!$isPaid): ?>
                              <button id="btn-pay-<?= $id ?>" class="btn-circle paid" onclick="markEnrollmentPaid(<?= $id ?>)" title="Mark as Paid (₱120.00)">
                                <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
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

        <!-- ═══ REJECTED TAB ═══ -->
        <div class="section-card tab-panel" id="tab-rejected" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
              Rejected Enrollment Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($rejected_reports) ?> record<?= count($rejected_reports) !== 1 ? 's' : '' ?></span>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Request ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Payment Method</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-rejected">
                  <?php if (empty($rejected_reports)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No rejected education enrollment requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($rejected_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#ER-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $studentName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($studentName)) $studentName = 'Student #' . $id;
                        $program = $r['program_name'] ?? 'Islamic Studies';
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $pm = trim($r['payment_method'] ?? 'Cash');
                        $pc = trim($r['payment_channel'] ?? '');
                        $isOnline = (stripos($pm, 'online') !== false || stripos($pm, 'gcash') !== false || stripos($pm, 'maya') !== false);
                        $pmDisplay = $isOnline ? 'Online Payment' : 'Cash';
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($studentName) ?></div>
                          <?php if (!empty($r['age'])): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:#6b7280; margin-top:2px;">
                              Age: <?= $r['age'] ?> yrs
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:var(--primary); font-weight:700;"><?= htmlspecialchars($program) ?></td>
                        <td>
                          <span style="font-weight:700; color:<?= $isOnline ? '#0d9488' : '#15803d' ?>;"><?= htmlspecialchars($pmDisplay) ?></span>
                          <?php if ($isOnline && !empty($pc)): ?>
                            <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;"><?= htmlspecialchars($pc) ?></div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status rejected">
                            <span class="status-dot"></span> Rejected
                          </span>
                          <?php if (!empty($r['rejection_reason'])): ?>
                            <div style="font-size:0.73rem; color:#dc2626; font-weight:600; margin-top:3px; max-width:200px; line-height:1.2;">
                              <?= htmlspecialchars($r['rejection_reason']) ?>
                            </div>
                          <?php endif; ?>
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

  <!-- View Education Enrollment Modal -->
  <div id="view-request-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:580px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #D4AF37);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.15rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#14532D;"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
          Enrollment Application Details
        </h5>
        <button type="button" onclick="closeViewModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.04em;">Request ID</span>
            <div id="vm-id" class="td-id" style="font-size:1.1rem; margin-top:2px;">#ER-0000</div>
          </div>
          <div id="vm-status-wrap">
            <span id="vm-status" class="badge-status pending"><span class="status-dot"></span> Pending</span>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Student Name</div>
            <div id="vm-student-name" style="font-weight:700; font-size:0.95rem; color:#111;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Date Submitted</div>
            <div id="vm-date" style="font-weight:600; font-size:0.92rem; color:#374151;">—</div>
          </div>
        </div>

        <div style="border:1px solid #bbf7d0; background:#f0fdf4; border-radius:8px; padding:12px; margin-bottom:16px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534; margin-bottom:3px;">Enrolled Program</div>
          <div id="vm-program" style="font-weight:700; font-size:0.95rem; color:#14532D;">—</div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Age &amp; Gender</div>
            <div id="vm-age-gender" style="font-weight:600; font-size:0.9rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Birthdate</div>
            <div id="vm-birthdate" style="font-weight:600; font-size:0.9rem; color:#374151;">—</div>
          </div>
        </div>

        <div style="border:1px solid #e2e8f0; border-radius:8px; padding:12px; background:#f8fafc; margin-bottom:16px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#64748b; margin-bottom:3px;">Payment Method &amp; Fee Status</div>
          <div id="vm-payment" style="font-weight:700; font-size:0.92rem; color:#111827;">—</div>
        </div>

        <div id="vm-rejection-wrap" style="display:none; border:1px solid #fecaca; background:#fef2f2; border-radius:8px; padding:12px; margin-bottom:16px;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#dc2626; margin-bottom:3px; display:flex; align-items:center; gap:5px;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:#dc2626;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/></svg>
            Rejection Reason
          </div>
          <div id="vm-rejection-reason" style="font-weight:600; font-size:0.88rem; color:#991b1b; line-height:1.4;">—</div>
        </div>
      </div>

      <div style="padding:14px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#f8fafc; flex-wrap:wrap; gap:8px;">
        <div id="vm-actions-left" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;"></div>
        <button type="button" onclick="closeViewModal()" style="padding:8px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Close</button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════════════════════ -->
  <!-- 🚫 REJECTION REASON MODAL WITH DROPDOWN & DYNAMIC INPUT   -->
  <!-- ══════════════════════════════════════════════════════════ -->
  <div id="edu-reject-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:999999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="height:4px; background:linear-gradient(90deg, #dc2626, #ef4444);"></div>
      
      <div style="padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#991b1b; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#dc2626;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/></svg>
          Reject Enrollment Request
        </h5>
        <button type="button" onclick="closeEduRejectModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:20px 22px;">
        <div id="edu-reject-target-info" style="background:#fef2f2; border:1px solid #fee2e2; border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:0.84rem; color:#991b1b; font-weight:600;">
          <!-- Populated by JS -->
        </div>

        <p style="font-size:0.83rem; color:#4b5563; margin:0 0 14px 0; line-height:1.45;">
          Please select the official reason for rejecting this application. The applicant will be notified with this reason.
        </p>

        <!-- Rejection Reason Dropdown -->
        <div style="margin-bottom:14px;">
          <label for="edu-reject-reason-select" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">
            Rejection Reason <span style="color:#dc2626;">*</span>
          </label>
          <select id="edu-reject-reason-select" onchange="toggleEduRejectOthers(this.value)" style="width:100%; box-sizing:border-box; border:1.5px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:0.86rem; background:#fff; font-family:inherit; color:#1f2937; outline:none; transition:border-color 0.15s;">
            <option value="">-- Select Rejection Reason --</option>
            <option value="Class / batch quota reached">Class / batch quota reached</option>
            <option value="Ineligible for this academic level or program">Ineligible for this academic level or program</option>
            <option value="Duplicate enrollment registration">Duplicate enrollment registration</option>
            <option value="Incomplete applicant information or missing documents">Incomplete applicant information or missing documents</option>
            <option value="Registration fee settlement failed or unverified">Registration fee settlement failed or unverified</option>
            <option value="Others">Others</option>
          </select>
        </div>

        <!-- Conditional Input Field for 'Others' -->
        <div id="edu-reject-others-wrap" style="display:none; margin-bottom:14px;">
          <label for="edu-reject-others-input" style="display:block; font-size:0.78rem; font-weight:700; color:#374151; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">
            Specify Custom Reason <span style="color:#dc2626;">*</span>
          </label>
          <textarea id="edu-reject-others-input" rows="3" placeholder="Enter specific reason for rejection..." style="width:100%; box-sizing:border-box; border:1.5px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:0.86rem; font-family:inherit; color:#1f2937; resize:vertical; outline:none; transition:border-color 0.15s;"></textarea>
        </div>
      </div>

      <div style="padding:14px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb;">
        <button type="button" onclick="closeEduRejectModal()" style="padding:8px 16px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; font-size:0.84rem; cursor:pointer;">
          Cancel
        </button>
        <button type="button" id="btn-confirm-edu-reject" onclick="submitEduRejection()" style="padding:8px 18px; border-radius:8px; border:none; background:#dc2626; color:white; font-weight:700; font-size:0.84rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          Confirm Rejection
        </button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    // Build records lookup map
    const rawRecords = <?= json_encode(array_values($reqList ?? [])) ?>;
    const recordsMap = {};
    rawRecords.forEach(r => { if (r && r.id) recordsMap[r.id] = r; });

    function switchTab(tab, btn) {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');

      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      const target = document.getElementById('tab-' + tab);
      if (target) target.style.display = 'block';
    }

    let currentModalId = null;

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function openViewModal(id) {
      const r = recordsMap[id];
      if (!r) return;
      currentModalId = id;

      const modal = document.getElementById('view-request-modal');
      if (!modal) return;

      const reqId = '#ER-' + String(r.id).padStart(4, '0');
      const studentName = `${r.first_name || ''} ${r.last_name || ''}`.trim() || ('Student #' + r.id);
      const program = r.program_name || 'Islamic Studies';
      const ageGender = [r.age ? `${r.age} yrs old` : '', r.gender || ''].filter(Boolean).join(' • ') || 'Not specified';
      const bdate = r.birthdate ? r.birthdate.substring(0, 10) : 'Not provided';
      const dateSub = (r.created_at || '').substring(0, 10) || 'N/A';
      const status = r.status || 'Pending';

      document.getElementById('vm-id').textContent = reqId;
      document.getElementById('vm-student-name').textContent = studentName;
      document.getElementById('vm-date').textContent = dateSub;
      document.getElementById('vm-program').textContent = program;
      document.getElementById('vm-age-gender').textContent = ageGender;
      document.getElementById('vm-birthdate').textContent = bdate;

      const stLower = status.toLowerCase();
      let badgeClass = 'pending';
      let badgeText = 'Pending';
      if (stLower === 'accepted' || stLower === 'active' || stLower === 'enrolled' || stLower === 'approved') {
        badgeClass = 'approved';
        badgeText = 'Accepted';
      } else if (stLower === 'rejected' || stLower === 'dropped' || stLower === 'cancelled') {
        badgeClass = 'rejected';
        badgeText = 'Rejected';
      }

      document.getElementById('vm-status-wrap').innerHTML = `
        <span class="badge-status ${badgeClass}">
          <span class="status-dot"></span> ${badgeText}
        </span>
      `;

      const payMethod = r.payment_method || 'Cash';
      const payChan = r.payment_channel ? ` (${r.payment_channel})` : '';
      const payRef = r.payment_reference ? ` • Ref: ${r.payment_reference}` : '';
      const feeAmt = r.fee_amount ? ` (₱${parseFloat(r.fee_amount).toFixed(2)})` : ' (₱120.00)';
      const isPaid = (r.payment_status || '').toLowerCase() === 'paid';
      const statusColor = isPaid ? '#166534' : '#b45309';
      const statusText = isPaid ? '✓ PAID (VERIFIED)' : 'PENDING PAYMENT';

      const vmPaymentEl = document.getElementById('vm-payment');
      if (vmPaymentEl) {
        vmPaymentEl.innerHTML = `
          <div>${escapeHtml(payMethod)}${escapeHtml(payChan)}${feeAmt}${escapeHtml(payRef)}</div>
          <div style="font-size:0.75rem; font-weight:800; color:${statusColor}; margin-top:4px;">
            Status: ${statusText}
          </div>
        `;
      }

      // Rejection Reason in View Modal
      const rejWrap = document.getElementById('vm-rejection-wrap');
      const rejText = document.getElementById('vm-rejection-reason');
      if (rejWrap && rejText) {
        if ((stLower === 'rejected' || stLower === 'dropped') && r.rejection_reason) {
          rejText.textContent = r.rejection_reason;
          rejWrap.style.display = 'block';
        } else {
          rejWrap.style.display = 'none';
        }
      }

      const actionsLeft = document.getElementById('vm-actions-left');
      if (actionsLeft) {
        let actsHtml = '';
        if (!isPaid) {
          actsHtml += `
            <button type="button" onclick="markEnrollmentPaid(${r.id})" style="padding:8px 14px; border-radius:8px; border:none; background:#d97706; color:white; font-weight:700; font-size:0.82rem; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
              <svg viewBox="0 0 24 24" style="width:15px;height:15px;fill:currentColor;"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
              Mark as Paid (₱120.00)
            </button>
          `;
        } else {
          actsHtml += `
            <span style="font-size:0.78rem; font-weight:800; color:#166534; background:#dcfce7; padding:6px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:5px; border:1px solid #bbf7d0;">
              ✓ Fee Officially Paid
            </span>
          `;
        }

        if (stLower === 'pending' || stLower === 'under_review') {
          actsHtml += `
            <button type="button" onclick="processRequest(${r.id}, 'accept')" style="padding:8px 14px; border-radius:8px; border:none; background:#166534; color:white; font-weight:700; font-size:0.82rem; cursor:pointer;">
              Accept Enrollment
            </button>
            <button type="button" onclick="openEduRejectModal(${r.id})" style="padding:8px 14px; border-radius:8px; border:1px solid #dc2626; background:#fff; color:#dc2626; font-weight:700; font-size:0.82rem; cursor:pointer;">
              Reject Request
            </button>
          `;
        }

        actionsLeft.innerHTML = actsHtml;
      }

      modal.style.display = 'flex';
    }

    function closeViewModal() {
      const modal = document.getElementById('view-request-modal');
      if (modal) modal.style.display = 'none';
      currentModalId = null;
    }

    document.getElementById('view-request-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeViewModal();
    });

    // ══════════════════════════════════════════════════════════
    // 🚫 REJECTION REASON MODAL LOGIC (DROPDOWN + OTHERS)
    // ══════════════════════════════════════════════════════════
    let eduRejectTargetId = null;

    function openEduRejectModal(id) {
      const r = recordsMap[id];
      eduRejectTargetId = id;
      const reqId = '#ER-' + String(id).padStart(4, '0');
      const studentName = r ? (`${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + id) : 'Student #' + id;

      const infoEl = document.getElementById('edu-reject-target-info');
      if (infoEl) {
        infoEl.innerHTML = `<strong>Request:</strong> ${reqId} &nbsp;•&nbsp; <strong>Student:</strong> ${escapeHtml(studentName)}`;
      }

      const sel = document.getElementById('edu-reject-reason-select');
      if (sel) sel.value = '';

      const othersWrap = document.getElementById('edu-reject-others-wrap');
      if (othersWrap) othersWrap.style.display = 'none';

      const othersInput = document.getElementById('edu-reject-others-input');
      if (othersInput) othersInput.value = '';

      const modal = document.getElementById('edu-reject-modal');
      if (modal) modal.style.display = 'flex';
    }

    function closeEduRejectModal() {
      const modal = document.getElementById('edu-reject-modal');
      if (modal) modal.style.display = 'none';
      eduRejectTargetId = null;
    }

    document.getElementById('edu-reject-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeEduRejectModal();
    });

    function toggleEduRejectOthers(val) {
      const othersWrap = document.getElementById('edu-reject-others-wrap');
      const othersInput = document.getElementById('edu-reject-others-input');
      if (val === 'Others') {
        if (othersWrap) othersWrap.style.display = 'block';
        setTimeout(() => othersInput?.focus(), 60);
      } else {
        if (othersWrap) othersWrap.style.display = 'none';
        if (othersInput) othersInput.value = '';
      }
    }

    async function submitEduRejection() {
      if (!eduRejectTargetId) return;
      const id = eduRejectTargetId;
      const sel = document.getElementById('edu-reject-reason-select');
      const reasonVal = sel ? sel.value.trim() : '';

      if (!reasonVal) {
        alert('Please select a rejection reason from the dropdown.');
        sel?.focus();
        return;
      }

      let finalReason = reasonVal;
      if (reasonVal === 'Others') {
        const customReason = document.getElementById('edu-reject-others-input')?.value.trim();
        if (!customReason) {
          alert('Please specify the custom reason in the input field.');
          document.getElementById('edu-reject-others-input')?.focus();
          return;
        }
        finalReason = customReason;
      }

      const btn = document.getElementById('btn-confirm-edu-reject');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Rejecting...';
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/education/reject") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id, reason: finalReason })
        });

        const res = await response.json();
        if (res.success) {
          if (recordsMap[id]) {
            recordsMap[id].status = 'Rejected';
            recordsMap[id].rejection_reason = finalReason;
          }

          // Remove row from pending table
          const row = document.getElementById('row-' + id);
          if (row) row.remove();

          // Prepend to rejected table
          const r = recordsMap[id];
          const targetTbody = document.getElementById('tbody-rejected');
          const reqId = '#ER-' + String(id).padStart(4, '0');
          const studentName = r ? (`${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + id) : 'Student #' + id;
          const program = r && r.program_name ? r.program_name : 'Islamic Studies';
          const dateSub = r && r.created_at ? r.created_at.substring(0, 10) : '—';
          const pm = r && r.payment_method ? r.payment_method : 'Cash';
          const isOnline = (pm.toLowerCase().includes('online') || pm.toLowerCase().includes('gcash') || pm.toLowerCase().includes('maya'));
          const pmDisplay = isOnline ? 'Online Payment' : 'Cash';

          const newRow = `
            <tr id="row-${id}">
              <td class="td-id">${reqId}</td>
              <td>
                <div style="font-weight:700; color:#111827;">${escapeHtml(studentName)}</div>
                ${r && r.age ? `<div style="font-size:0.78rem; font-weight:600; color:#6b7280; margin-top:2px;">Age: ${r.age} yrs</div>` : ''}
              </td>
              <td style="color:var(--primary); font-weight:700;">${escapeHtml(program)}</td>
              <td>
                <span style="font-weight:700; color:${isOnline ? '#0d9488' : '#15803d'};">${pmDisplay}</span>
                ${isOnline && r && r.payment_channel ? `<div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">${escapeHtml(r.payment_channel)}</div>` : ''}
              </td>
              <td style="color:#4b5563;">${dateSub}</td>
              <td>
                <span class="badge-status rejected"><span class="status-dot"></span> Rejected</span>
                <div style="font-size:0.73rem; color:#dc2626; font-weight:600; margin-top:3px; max-width:200px; line-height:1.2;">
                  ${escapeHtml(finalReason)}
                </div>
              </td>
              <td>
                <div class="actions-cell">
                  <button class="btn-circle eye" onclick="openViewModal(${id})" title="View Details">
                    <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          `;

          if (targetTbody) {
            if (targetTbody.querySelector('td[colspan]')) {
              targetTbody.innerHTML = '';
            }
            targetTbody.insertAdjacentHTML('afterbegin', newRow);
          }

          // Update badge counts
          const statPending = document.getElementById('stat-pending');
          const countPending = document.getElementById('count-pending');
          if (statPending && countPending) {
            const current = Math.max(0, parseInt(statPending.textContent || '0') - 1);
            statPending.textContent = current;
            countPending.textContent = current;
          }

          const statRej = document.getElementById('stat-rejected');
          const countRej = document.getElementById('count-rejected');
          if (statRej && countRej) {
            const current = parseInt(statRej.textContent || '0') + 1;
            statRej.textContent = current;
            countRej.textContent = current;
          }

          closeEduRejectModal();
          if (currentModalId === id) {
            closeViewModal();
          }

          alert('Enrollment request has been rejected. The applicant has been notified with the reason.');
        } else {
          alert(res.message || 'Error rejecting enrollment.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error.');
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Confirm Rejection';
        }
      }
    }

    async function markEnrollmentPaid(id) {
      const r = recordsMap[id];
      const reqId = '#ER-' + String(id).padStart(4, '0');
      const studentName = r ? (`${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + id) : 'Student';

      if (!confirm(`Mark enrollment fee (₱120.00) as PAID for ${studentName} (${reqId})?`)) {
        return;
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/education/mark-paid") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const res = await response.json();

        if (res.success) {
          if (recordsMap[id]) {
            recordsMap[id].payment_status = 'Paid';
            if (res.payment_reference) {
              recordsMap[id].payment_reference = res.payment_reference;
            }
          }

          // Update payment status badge in table
          const payTag = document.getElementById('pay-tag-' + id);
          if (payTag) {
            payTag.className = 'badge-pay-status paid';
            payTag.textContent = '✓ Paid';
          }

          // Remove the mark-paid button from row actions
          const btnPay = document.getElementById('btn-pay-' + id);
          if (btnPay) {
            btnPay.remove();
          }

          // If view modal is open for this record, update it
          if (currentModalId === id) {
            openViewModal(id);
          }

          alert(res.message || 'Payment has been successfully verified and marked as Paid.');
        } else {
          alert(res.message || 'Error updating payment status.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error.');
      }
    }

    async function processRequest(id, action) {
      if (action === 'reject') {
        openEduRejectModal(id);
        return;
      }

      const r = recordsMap[id];
      const reqId = '#ER-' + String(id).padStart(4, '0');
      if (!confirm(`Are you sure you want to ACCEPT enrollment request ${reqId}?`)) return;

      const endpoint = '<?= url("/admin/dawah/education/accept") ?>';

      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const res = await response.json();

        if (res.success) {
          if (recordsMap[id]) {
            recordsMap[id].status = 'Accepted';
          }

          // Move row from pending table to approved table
          const row = document.getElementById('row-' + id);
          if (row) {
            row.remove();

            const targetTbody = document.getElementById('tbody-approved');
            const studentName = r ? (`${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Student #' + id) : 'Student #' + id;
            const program = r && r.program_name ? r.program_name : 'Islamic Studies';
            const dateSub = r && r.created_at ? r.created_at.substring(0, 10) : '—';
            const pm = r && r.payment_method ? r.payment_method : 'Cash';
            const isOnline = (pm.toLowerCase().includes('online') || pm.toLowerCase().includes('gcash') || pm.toLowerCase().includes('maya'));
            const pmDisplay = isOnline ? 'Online Payment' : 'Cash';
            const isPaid = (r && r.payment_status && r.payment_status.toLowerCase() === 'paid');

            const statusBadge = `<span class="badge-status approved"><span class="status-dot"></span> Accepted</span>`;

            const newRow = `
              <tr id="row-${id}">
                <td class="td-id">${reqId}</td>
                <td>
                  <div style="font-weight:700; color:#111827;">${escapeHtml(studentName)}</div>
                  ${r && r.age ? `<div style="font-size:0.78rem; font-weight:600; color:#6b7280; margin-top:2px;">Age: ${r.age} yrs</div>` : ''}
                </td>
                <td style="color:var(--primary); font-weight:700;">${escapeHtml(program)}</td>
                <td>
                  <span style="font-weight:700; color:${isOnline ? '#0d9488' : '#15803d'};">${pmDisplay}</span>
                  ${isOnline && r && r.payment_channel ? `<div style="font-size:0.72rem; color:var(--text-muted); font-weight:600;">${escapeHtml(r.payment_channel)}</div>` : ''}
                  <div id="pay-tag-${id}" class="badge-pay-status ${isPaid ? 'paid' : 'pending'}">${isPaid ? '✓ Paid' : 'Pending Fee'}</div>
                </td>
                <td style="color:#4b5563;">${dateSub}</td>
                <td>${statusBadge}</td>
                <td>
                  <div class="actions-cell">
                    <button class="btn-circle eye" onclick="openViewModal(${id})" title="View Details">
                      <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                    </button>
                    ${!isPaid ? `
                      <button id="btn-pay-${id}" class="btn-circle paid" onclick="markEnrollmentPaid(${id})" title="Mark as Paid (₱120.00)">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                      </button>
                    ` : ''}
                  </div>
                </td>
              </tr>
            `;

            if (targetTbody) {
              if (targetTbody.querySelector('td[colspan]')) {
                targetTbody.innerHTML = '';
              }
              targetTbody.insertAdjacentHTML('afterbegin', newRow);
            }

            // Update badge counts
            const statPending = document.getElementById('stat-pending');
            const countPending = document.getElementById('count-pending');
            if (statPending && countPending) {
              const current = Math.max(0, parseInt(statPending.textContent || '0') - 1);
              statPending.textContent = current;
              countPending.textContent = current;
            }

            const statAppr = document.getElementById('stat-approved');
            const countAppr = document.getElementById('count-approved');
            if (statAppr && countAppr) {
              const current = parseInt(statAppr.textContent || '0') + 1;
              statAppr.textContent = current;
              countAppr.textContent = current;
            }

            if (currentModalId === id) {
              openViewModal(id);
            }
          }
        } else {
          alert(res.message || 'Error processing enrollment.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error.');
      }
    }
  </script>
</body>
</html>
