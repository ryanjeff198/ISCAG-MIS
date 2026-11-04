<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$reqList = $records ?? ($applications ?? []);
$pending_reports  = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? 'pending'), ['pending', 'under_review'])));
$approved_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['accepted', 'approved', 'confirmed', 'completed'])));
$rejected_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['rejected', 'cancelled', 'disapproved'])));
$dawah_type = $dawah_type ?? 'male';
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

    /* ── Tab Navigation ── */
    .tab-nav {
      display: flex; gap: 8px; border-bottom: 2px solid var(--border);
      margin-bottom: 24px; padding-bottom: 0px;
    }
    .tab-btn {
      padding: 10px 18px; font-size: 0.88rem; font-weight: 700;
      color: var(--text-muted); background: none; border: none;
      border-bottom: 3px solid transparent; cursor: pointer;
      display: inline-flex; align-items: center; gap: 8px;
      transition: all 0.2s ease; border-radius: 6px 6px 0 0;
    }
    .tab-btn:hover { color: var(--primary); background: rgba(0,0,0,0.02); }
    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-count {
      padding: 2px 7px; border-radius: 12px; font-size: 0.72rem;
      font-weight: 800; background: #e5e7eb; color: #4b5563;
    }
    .tab-count.pending { background: rgba(199, 154, 43, 0.15); color: #b08d2e; }
    .tab-count.approved { background: rgba(47, 138, 96, 0.15); color: #2f8a60; }
    .tab-count.rejected { background: rgba(139, 46, 46, 0.15); color: #8b2e2e; }

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
      width: 32px; height: 32px; border-radius: 8px;
      display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--border, #e2e8f0); background: #f8fafc;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
      color: #475569;
    }
    .btn-circle svg { width: 17px; height: 17px; fill: currentColor; }
    .btn-circle:hover, .btn-circle.active { 
      background: #e2e8f0; 
      border-color: #cbd5e1;
      color: var(--primary-dark, #0f172a);
      box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    
    .btn-circle.schedule { color: #6d28d9; }
    .btn-circle.schedule:hover { background: #f5f3ff; border-color: #ddd6fe; color: #5b21b6; }
    .btn-circle.eye { color: #1d4ed8; }
    .btn-circle.eye:hover { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .btn-circle.delete { color: #dc2626; }
    .btn-circle.delete:hover { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .btn-circle.dots { border-radius: 8px; }

    /* Floating 3-Dots Action Dropdown Menu */
    .conversion-action-menu {
      position: fixed;
      display: none;
      min-width: 175px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.14), 0 8px 10px -6px rgba(0,0,0,0.08);
      padding: 6px;
      z-index: 999999;
      animation: convMenuFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes convMenuFadeIn {
      from { opacity: 0; transform: scale(0.96) translateY(-4px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .action-dropdown-item {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 9px 12px;
      font-size: 0.84rem;
      font-weight: 600;
      color: #334155;
      background: transparent;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-align: left;
      transition: all 0.15s ease;
      font-family: inherit;
      box-sizing: border-box;
    }
    .action-dropdown-item svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      fill: currentColor;
    }
    .action-dropdown-item:hover {
      background: #f1f5f9;
      color: #0f172a;
    }
    .action-dropdown-item.schedule { color: #6d28d9; }
    .action-dropdown-item.schedule:hover { background: #f5f3ff; color: #5b21b6; }
    .action-dropdown-item.view { color: #1d4ed8; }
    .action-dropdown-item.view:hover { background: #eff6ff; color: #1e40af; }
    .action-dropdown-item.accept { color: #166534; }
    .action-dropdown-item.accept:hover { background: #f0fdf4; color: #14532D; }
    .action-dropdown-item.reject { color: #9a3412; }
    .action-dropdown-item.reject:hover { background: #fff7ed; color: #c2410c; }
    .action-dropdown-item.delete { color: #dc2626; }
    .action-dropdown-item.delete:hover { background: #fef2f2; color: #b91c1c; }
    .action-dropdown-divider {
      height: 1px;
      background: #e2e8f0;
      margin: 4px 0;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'conversion_requests';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    
    <div class="main-content">
      <!-- Top Bar -->
      <div class="top-bar">
        <div class="top-bar-left">
            <div class="top-bar-title">Conversion Requests</div>
            <div class="top-bar-subtitle"><?= $dawah_type === 'female' ? "Female Da'wah Department" : "Male Da'wah Department" ?> — Review submitted Shahadah applications, schedule ceremonies, and manage records</div>
        </div>
        <div class="top-bar-actions">
          <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Dashboard</a>
          <span class="sep">›</span>
          <span>Islamic Conversion</span>
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
            <div class="insight-label">Total Requests</div>
            <div class="insight-value info" id="stat-total"><?= count($reqList) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
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
                    <th>Applicant Name</th>
                    <th>Ceremony Schedule</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-pending">
                  <?php if (empty($pending_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No pending conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($pending_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = 'Applicant #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $schedDate = !empty($r['conversion_date']) ? date('M d, Y', strtotime($r['conversion_date'])) : '';
                        $schedTime = !empty($r['preferred_time']) ? htmlspecialchars($r['preferred_time']) : '';
                        $schedDisplay = $schedDate ? ($schedDate . ($schedTime ? ' (' . $schedTime . ')' : '')) : '<span style="color:#9ca3af;font-style:italic;">Not scheduled</span>';
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
                        <td style="color:#374151; font-weight:600;"><?= $schedDisplay ?></td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status pending">
                            <span class="status-dot"></span> Pending
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button type="button" class="btn-circle dots" onclick="toggleReqMenu(event, <?= $id ?>)" title="Actions" aria-label="Actions">
                              <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
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
                    <th>Applicant Name</th>
                    <th>Ceremony Schedule</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-approved">
                  <?php if (empty($approved_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No approved conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($approved_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = 'Applicant #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $schedDate = !empty($r['conversion_date']) ? date('M d, Y', strtotime($r['conversion_date'])) : '';
                        $schedTime = !empty($r['preferred_time']) ? htmlspecialchars($r['preferred_time']) : '';
                        $schedDisplay = $schedDate ? ($schedDate . ($schedTime ? ' (' . $schedTime . ')' : '')) : '<span style="color:#9ca3af;font-style:italic;">Not scheduled</span>';
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
                        <td style="color:#374151; font-weight:600;"><?= $schedDisplay ?></td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status approved">
                            <span class="status-dot"></span> Accepted
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button type="button" class="btn-circle dots" onclick="toggleReqMenu(event, <?= $id ?>)" title="Actions" aria-label="Actions">
                              <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
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
                    <th>Applicant Name</th>
                    <th>Ceremony Schedule</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-rejected">
                  <?php if (empty($rejected_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No rejected conversion requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($rejected_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $reqId = '#CR-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $userName = trim(($r['fname'] ?? '') . ' ' . ($r['mname'] ? $r['mname'] . ' ' : '') . ($r['lname'] ?? ''));
                        if (empty($userName)) $userName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                        if (empty($userName)) $userName = 'Applicant #' . ($r['tenant_id'] ?? $id);
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $schedDate = !empty($r['conversion_date']) ? date('M d, Y', strtotime($r['conversion_date'])) : '';
                        $schedTime = !empty($r['preferred_time']) ? htmlspecialchars($r['preferred_time']) : '';
                        $schedDisplay = $schedDate ? ($schedDate . ($schedTime ? ' (' . $schedTime . ')' : '')) : '<span style="color:#9ca3af;font-style:italic;">Not scheduled</span>';
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
                        <td style="color:#374151; font-weight:600;"><?= $schedDisplay ?></td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td>
                          <span class="badge-status rejected">
                            <span class="status-dot"></span> Rejected
                          </span>
                        </td>
                        <td>
                          <div class="actions-cell">
                            <button type="button" class="btn-circle dots" onclick="toggleReqMenu(event, <?= $id ?>)" title="Actions" aria-label="Actions">
                              <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
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

  <!-- ═══ Set / Update Schedule Ceremony Modal ═══ -->
  <div id="conversion-schedule-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.68); backdrop-filter:blur(6px); z-index:999999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:600px; box-shadow:0 30px 80px rgba(0,0,0,0.35); overflow:hidden; border:1px solid rgba(0,0,0,0.08);">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #7c3aed);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafafa;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.18rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#7c3aed;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
          Schedule Conversion Ceremony (Shahadah)
        </h5>
        <button type="button" onclick="closeScheduleModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <form id="form-conversion-schedule" onsubmit="submitScheduleConversion(event)" style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <input type="hidden" id="cs-request-id" name="id" value="" />

        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Applicant</span>
            <div id="cs-applicant-name" style="font-weight:800; font-size:1.02rem; color:#14532D; margin-top:2px;">—</div>
          </div>
          <div style="text-align:right;">
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Request ID</span>
            <div id="cs-ref-no" style="font-weight:800; font-size:0.95rem; color:#14532D; margin-top:2px;">#CR-0000</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              Ceremony Date <span style="color:#ef4444;">*</span>
            </label>
            <input type="date" id="cs-date" required style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" />
          </div>
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              Ceremony Time <span style="color:#ef4444;">*</span>
            </label>
            <input type="time" id="cs-time" required value="10:00" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" />
          </div>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Ceremony Venue / Location
          </label>
          <input type="text" id="cs-venue" value="ISCAG Main Mosque, Da'wah Ceremony Hall" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem;" />
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Presiding Ustaz / Officiant
          </label>
          <input type="text" id="cs-officiant" value="Da'wah Division Ustaz / Officiant" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem;" />
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Application Status
          </label>
          <select id="cs-status" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600; background:#fff;">
            <option value="approved">Approved &amp; Scheduled</option>
            <option value="pending">Keep as Pending Review</option>
          </select>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Instructions / Guidance for Applicant
          </label>
          <textarea id="cs-notes" rows="2" placeholder="e.g. Please bring two Muslim adult witnesses and a valid government ID. May Allah bless you." style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.88rem; font-family:inherit;"></textarea>
        </div>

        <div style="padding:14px 0 0; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px;">
          <button type="button" onclick="closeScheduleModal()" style="padding:9px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
          <button type="submit" id="cs-submit-btn" class="btn-topbar primary" style="padding:9px 22px; font-weight:700;">
            Confirm &amp; Save Schedule
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ═══ View Conversion Request Details Modal ═══ -->
  <div id="view-request-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.65); backdrop-filter:blur(5px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:16px; width:100%; max-width:640px; box-shadow:0 25px 70px rgba(0,0,0,0.3); overflow:hidden; border:1px solid rgba(0,0,0,0.08);">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #D4AF37);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafafa;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.18rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#14532D;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
          Conversion Request Details
        </h5>
        <button type="button" onclick="closeViewModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <div style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.04em;">Request ID</span>
            <div id="vm-id" class="td-id" style="font-size:1.15rem; margin-top:2px;">#CR-0000</div>
          </div>
          <div id="vm-status-wrap">
            <span id="vm-status" class="badge-status pending"><span class="status-dot"></span> Pending</span>
          </div>
        </div>

        <!-- Schedule Ceremony Highlight Card -->
        <div style="background:#faf5ff; border:1px solid #e9d5ff; border-radius:10px; padding:12px 16px; margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <span style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:#6b21a8; letter-spacing:0.04em; display:flex; align-items:center; gap:6px;">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
              Ceremony Schedule &amp; Venue
            </span>
            <button type="button" onclick="openScheduleFromViewModal()" style="background:none; border:none; color:#7c3aed; font-size:0.78rem; font-weight:700; cursor:pointer; text-decoration:underline;">
              Edit Schedule
            </button>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.88rem;">
            <div><strong style="color:#581c87;">Date &amp; Time:</strong> <span id="vm-schedule-dt" style="color:#3b0764; font-weight:600;">Not scheduled</span></div>
            <div><strong style="color:#581c87;">Officiant:</strong> <span id="vm-officiant" style="color:#3b0764;">—</span></div>
            <div style="grid-column:span 2;"><strong style="color:#581c87;">Venue:</strong> <span id="vm-venue" style="color:#3b0764;">—</span></div>
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

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Witness 1</div>
            <div id="vm-w1" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
          <div style="border:1px solid #e5e7eb; border-radius:8px; padding:12px;">
            <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:3px;">Witness 2</div>
            <div id="vm-w2" style="font-weight:600; font-size:0.88rem; color:#374151;">—</div>
          </div>
        </div>

        <div id="vm-wrap-notes" style="border:1px solid #e5e7eb; border-radius:8px; padding:12px; display:none;">
          <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#6b7280; margin-bottom:4px;">Guidance / Notes</div>
          <div id="vm-notes" style="font-size:0.88rem; color:#374151; line-height:1.5;">—</div>
        </div>
      </div>

      <div style="padding:14px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
        <button type="button" onclick="deleteFromViewModal()" style="display:flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; border:1px solid #fecaca; background:#fef2f2; color:#dc2626; font-weight:600; font-size:0.85rem; cursor:pointer;">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          Delete Request
        </button>
        <div style="display:flex; gap:10px;">
          <button type="button" onclick="closeViewModal()" style="padding:8px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Close</button>
          <button type="button" onclick="openScheduleFromViewModal()" class="btn-topbar primary" style="display:flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            Set Schedule
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ 3-Dots Action Dropdown Menu ═══ -->
  <div id="conversion-req-action-menu" class="conversion-action-menu">
    <button type="button" class="action-dropdown-item schedule" onclick="triggerReqAction('schedule')">
      <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
      <span>Set Schedule</span>
    </button>
    <button type="button" class="action-dropdown-item view" onclick="triggerReqAction('view')">
      <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
      <span>View Details</span>
    </button>
    <div class="action-dropdown-divider"></div>
    <button type="button" class="action-dropdown-item accept" onclick="triggerReqAction('accept')">
      <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
      <span>Accept Request</span>
    </button>
    <button type="button" class="action-dropdown-item reject" onclick="triggerReqAction('reject')">
      <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
      <span>Reject Request</span>
    </button>
    <div class="action-dropdown-divider"></div>
    <button type="button" class="action-dropdown-item delete" onclick="triggerReqAction('delete')">
      <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
      <span>Delete</span>
    </button>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    // ── Tab Switching Logic ──
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

    let currentViewingId = null;

    function openViewModal(id) {
      currentViewingId = id;
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

      // Schedule fields
      const schedDt = r.conversion_date ? `${convDate}${r.preferred_time ? ' at ' + r.preferred_time : ''}` : 'Not scheduled yet';
      document.getElementById('vm-schedule-dt').textContent = schedDt;
      document.getElementById('vm-venue').textContent = r.venue || 'ISCAG Main Mosque, Da\'wah Ceremony Hall';
      document.getElementById('vm-officiant').textContent = r.officiant || 'Da\'wah Division Ustaz / Officiant';

      if (r.notes && r.notes.trim()) {
        document.getElementById('vm-wrap-notes').style.display = 'block';
        document.getElementById('vm-notes').textContent = r.notes;
      } else {
        document.getElementById('vm-wrap-notes').style.display = 'none';
      }

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

    function openScheduleFromViewModal() {
      if (currentViewingId) {
        closeViewModal();
        openScheduleModal(currentViewingId);
      }
    }

    function deleteFromViewModal() {
      if (currentViewingId) {
        const id = currentViewingId;
        closeViewModal();
        deleteConversionReqRecord(id);
      }
    }

    document.getElementById('view-request-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeViewModal();
    });

    /* ── Schedule Modal Logic ── */
    function openScheduleModal(id) {
      const r = recordsMap[id];
      if (!r) return;

      document.getElementById('cs-request-id').value = id;
      const reqId = '#CR-' + String(r.id).padStart(4, '0');
      const applicantName = `${r.fname || ''} ${r.mname ? r.mname + ' ' : ''}${r.lname || ''}`.trim() || (`${r.first_name || ''} ${r.last_name || ''}`.trim()) || 'Applicant #' + id;
      document.getElementById('cs-applicant-name').textContent = applicantName;
      document.getElementById('cs-ref-no').textContent = reqId;

      if (r.conversion_date) {
        document.getElementById('cs-date').value = r.conversion_date;
      } else {
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dt = String(d.getDate()).padStart(2, '0');
        document.getElementById('cs-date').value = `${y}-${m}-${dt}`;
      }

      if (r.preferred_time) {
        const m = r.preferred_time.match(/(\d{1,2}):(\d{2})/);
        if (m) {
          let hrs = parseInt(m[1]);
          if (r.preferred_time.toLowerCase().includes('pm') && hrs < 12) hrs += 12;
          if (r.preferred_time.toLowerCase().includes('am') && hrs === 12) hrs = 0;
          document.getElementById('cs-time').value = `${String(hrs).padStart(2, '0')}:${m[2]}`;
        }
      } else {
        document.getElementById('cs-time').value = '10:00';
      }

      if (r.venue) document.getElementById('cs-venue').value = r.venue;
      if (r.officiant) document.getElementById('cs-officiant').value = r.officiant;
      if (r.notes) document.getElementById('cs-notes').value = r.notes;

      const stLower = (r.status || 'pending').toLowerCase();
      document.getElementById('cs-status').value = ['accepted', 'approved', 'confirmed'].includes(stLower) ? 'approved' : 'approved';

      document.getElementById('conversion-schedule-modal').style.display = 'flex';
    }

    function closeScheduleModal() {
      const m = document.getElementById('conversion-schedule-modal');
      if (m) m.style.display = 'none';
    }

    document.getElementById('conversion-schedule-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeScheduleModal();
    });

    async function submitScheduleConversion(e) {
      e.preventDefault();
      const id = document.getElementById('cs-request-id').value;
      const date = document.getElementById('cs-date').value;
      const time = document.getElementById('cs-time').value;
      const venue = document.getElementById('cs-venue').value;
      const officiant = document.getElementById('cs-officiant').value;
      const notes = document.getElementById('cs-notes').value;
      const status = document.getElementById('cs-status').value;

      if (!id || !date || !time) {
        alert('Please provide ceremony date and time.');
        return;
      }

      // Convert 24hr to friendly 12hr format
      const [h, m] = time.split(':');
      let hr = parseInt(h);
      const ampm = hr >= 12 ? 'PM' : 'AM';
      hr = hr % 12 || 12;
      const timeFmt = `${hr}:${m} ${ampm}`;

      const btn = document.getElementById('cs-submit-btn');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Saving Schedule...';
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/conversion/schedule") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: id,
            conversion_date: date,
            preferred_time: timeFmt,
            venue: venue,
            officiant: officiant,
            notes: notes,
            status: status
          })
        });

        const res = await response.json();
        if (res.success) {
          if (recordsMap[id]) {
            recordsMap[id].conversion_date = date;
            recordsMap[id].preferred_time = timeFmt;
            recordsMap[id].venue = venue;
            recordsMap[id].officiant = officiant;
            recordsMap[id].notes = notes;
            recordsMap[id].status = status;
          }

          if (typeof showAlert === 'function') {
            showAlert('Schedule Saved', res.message || 'Conversion ceremony schedule saved successfully.', 'success');
          } else {
            alert(res.message || 'Conversion ceremony schedule saved successfully.');
          }
          closeScheduleModal();
          setTimeout(() => location.reload(), 900);
        } else {
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Confirm & Save Schedule';
          }
          alert(res.message || 'Failed to save ceremony schedule.');
        }
      } catch (err) {
        console.error(err);
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Confirm & Save Schedule';
        }
        alert('A network error occurred while saving the schedule.');
      }
    }

    /* ── Delete Request Handler ── */
    async function deleteConversionReqRecord(id) {
      const r = recordsMap[id];
      const reqRef = '#CR-' + String(id).padStart(4, '0');
      const name = r ? (trimName(r) || 'Applicant') : 'Applicant';

      function trimName(rec) {
        return `${rec.fname || ''} ${rec.mname ? rec.mname + ' ' : ''}${rec.lname || ''}`.trim() || `${rec.first_name || ''} ${rec.last_name || ''}`.trim();
      }

      const doDelete = async () => {
        try {
          const response = await fetch('<?= url("/admin/dawah/conversion/delete") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
          });

          const res = await response.json();
          if (res.success) {
            if (typeof showAlert === 'function') {
              showAlert('Deleted Successfully', res.message || `Conversion request ${reqRef} has been removed.`, 'success');
            } else {
              alert(res.message || `Conversion request ${reqRef} has been removed.`);
            }

            const row = document.getElementById('row-' + id);
            if (row) row.remove();

            // Decrement count
            const statTotal = document.getElementById('stat-total');
            if (statTotal) {
              statTotal.textContent = Math.max(0, parseInt(statTotal.textContent || '0') - 1);
            }

            setTimeout(() => location.reload(), 800);
          } else {
            if (typeof showAlert === 'function') {
              showAlert('Deletion Failed', res.message || 'Failed to delete conversion request.', 'error');
            } else {
              alert(res.message || 'Failed to delete conversion request.');
            }
          }
        } catch (err) {
          console.error(err);
          if (typeof showAlert === 'function') {
            showAlert('Network Error', 'A network error occurred while deleting the request.', 'error');
          } else {
            alert('A network error occurred while deleting the request.');
          }
        }
      };

      const title = 'Delete Conversion Request';
      const msg = `Are you sure you want to DELETE conversion request ${reqRef} for "${name}"?\n\nThis action cannot be undone and will permanently remove this record from the system.`;

      if (typeof showConfirm === 'function') {
        showConfirm(title, msg, doDelete, 'danger');
      } else {
        if (confirm(`${title}\n\n${msg}`)) {
          doDelete();
        }
      }
    }

    /* ── Accept / Reject Process Request Handler ── */
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
          setTimeout(() => location.reload(), 900);
        } else {
          alert(res.message || 'Failed to process request. Please try again.');
        }
      } catch (err) {
        console.error(err);
        alert('A network error occurred while processing the request.');
      }
    }

    /* ── 3-Dots Action Menu Handlers ── */
    let activeReqId = null;
    let activeReqButton = null;

    function toggleReqMenu(e, id) {
      e.stopPropagation();
      const menu = document.getElementById('conversion-req-action-menu');
      const btn = e.currentTarget;

      if (menu.style.display === 'block' && activeReqId === id) {
        closeReqMenu();
        return;
      }

      activeReqId = id;
      if (activeReqButton) activeReqButton.classList.remove('active');
      activeReqButton = btn;
      activeReqButton.classList.add('active');

      menu.style.display = 'block';

      const rect = btn.getBoundingClientRect();
      const menuWidth = 180;
      const menuHeight = menu.offsetHeight || 190;

      let top = rect.bottom + 6;
      let left = rect.right - menuWidth;

      if (top + menuHeight > window.innerHeight) {
        top = Math.max(10, rect.top - menuHeight - 6);
      }
      if (left < 10) {
        left = Math.max(10, rect.left);
      }

      menu.style.top = `${top}px`;
      menu.style.left = `${left}px`;
    }

    function closeReqMenu() {
      const menu = document.getElementById('conversion-req-action-menu');
      if (menu) menu.style.display = 'none';
      if (activeReqButton) {
        activeReqButton.classList.remove('active');
        activeReqButton = null;
      }
      activeReqId = null;
    }

    function triggerReqAction(action) {
      if (!activeReqId) return;
      const id = activeReqId;
      closeReqMenu();

      if (action === 'schedule') {
        openScheduleModal(id);
      } else if (action === 'view') {
        openViewModal(id);
      } else if (action === 'delete') {
        deleteConversionReqRecord(id);
      } else if (action === 'accept') {
        processRequest(id, 'accept');
      } else if (action === 'reject') {
        processRequest(id, 'reject');
      }
    }

    document.addEventListener('click', function(e) {
      const menu = document.getElementById('conversion-req-action-menu');
      if (menu && menu.style.display === 'block') {
        if (!menu.contains(e.target) && !e.target.closest('.btn-circle.dots')) {
          closeReqMenu();
        }
      }
    });

    window.addEventListener('scroll', closeReqMenu, true);
    window.addEventListener('resize', closeReqMenu);
  </script>
</body>
</html>