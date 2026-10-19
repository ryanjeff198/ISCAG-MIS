<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);

$reqList = $records ?? [];
$pending_reports  = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? 'pending'), ['pending', 'under_review'])));
$approved_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['accepted', 'approved', 'confirmed', 'completed'])));
$rejected_reports = array_values(array_filter($reqList, fn($r) => in_array(strtolower($r['status'] ?? ''), ['rejected', 'cancelled', 'disapproved'])));
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

    /* ── Action Buttons & 3-Dots Dropdown ── */
    .actions-cell { display: flex; align-items: center; justify-content: center; }
    .btn-circle {
      width: 32px; height: 32px; border-radius: 8px;
      display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--border, #e2e8f0); background: #f8fafc;
      cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); padding: 0;
      color: #475569;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: currentColor; }
    .btn-circle:hover, .btn-circle.active { 
      background: #e2e8f0; 
      border-color: #cbd5e1;
      color: var(--primary-dark, #0f172a);
      box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .btn-circle.dots { border-radius: 8px; }

    /* Floating 3-Dots Action Dropdown Menu */
    .counseling-action-menu {
      position: fixed;
      display: none;
      min-width: 175px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.14), 0 8px 10px -6px rgba(0,0,0,0.08);
      padding: 6px;
      z-index: 999999;
      animation: menuFadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes menuFadeIn {
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
          <span class="current">Counseling Requests</span>
        </div>

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card pending" onclick="switchTab('pending', document.querySelector('.tab-btn:nth-child(1)'))">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning" id="stat-pending"><?= count($pending_reports) ?></div>
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
          </div>
          <div class="insight-card approved" onclick="switchTab('approved', document.querySelector('.tab-btn:nth-child(2)'))">
            <div class="insight-label">Accepted / Confirmed</div>
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
            <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" /></svg>
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
                    <th>Client Name</th>
                    <th>Date Submitted</th>
                    <th>Preferred Schedule</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="tbody-pending">
                  <?php if (empty($pending_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No pending counseling requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($pending_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $prefix = ($dawah_type === 'female') ? 'FC-' : 'MC-';
                        $reqId = '#' . $prefix . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $clientName = $r['full_name'] ?? ($r['client_name'] ?? trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')));
                        if (empty($clientName)) $clientName = ($dawah_type === 'female' ? 'Anonymous Sister' : 'Anonymous Brother');
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $prefSchedule = !empty($r['preferred_date']) 
                            ? date('M d, Y', strtotime($r['preferred_date'])) . (!empty($r['preferred_time']) ? ' (' . htmlspecialchars($r['preferred_time']) . ')' : '') 
                            : 'Flexible Schedule';
                        $reason = $r['reason'] ?? ($r['concern_category'] ?? '');
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($clientName) ?></div>
                          <?php if (!empty($reason)): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              <?= htmlspecialchars($reason) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td style="color:#374151; font-weight:600;"><?= $prefSchedule ?></td>
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
              Approved Counseling Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($approved_reports) ?> record<?= count($approved_reports) !== 1 ? 's' : '' ?></span>
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
                <tbody id="tbody-approved">
                  <?php if (empty($approved_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No approved counseling requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($approved_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $prefix = ($dawah_type === 'female') ? 'FC-' : 'MC-';
                        $reqId = '#' . $prefix . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $clientName = $r['full_name'] ?? ($r['client_name'] ?? trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')));
                        if (empty($clientName)) $clientName = ($dawah_type === 'female' ? 'Anonymous Sister' : 'Anonymous Brother');
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $prefSchedule = !empty($r['preferred_date']) 
                            ? date('M d, Y', strtotime($r['preferred_date'])) . (!empty($r['preferred_time']) ? ' (' . htmlspecialchars($r['preferred_time']) . ')' : '') 
                            : 'Flexible Schedule';
                        $reason = $r['reason'] ?? ($r['concern_category'] ?? '');
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($clientName) ?></div>
                          <?php if (!empty($reason)): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              <?= htmlspecialchars($reason) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td style="color:#374151; font-weight:600;"><?= $prefSchedule ?></td>
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
              Rejected Counseling Requests
            </h6>
            <span style="font-size:0.75rem;color:var(--text-muted);"><?= count($rejected_reports) ?> record<?= count($rejected_reports) !== 1 ? 's' : '' ?></span>
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
                <tbody id="tbody-rejected">
                  <?php if (empty($rejected_reports)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No rejected counseling requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($rejected_reports as $r): ?>
                      <?php 
                        $id = (int)$r['id'];
                        $prefix = ($dawah_type === 'female') ? 'FC-' : 'MC-';
                        $reqId = '#' . $prefix . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
                        $clientName = $r['full_name'] ?? ($r['client_name'] ?? trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')));
                        if (empty($clientName)) $clientName = ($dawah_type === 'female' ? 'Anonymous Sister' : 'Anonymous Brother');
                        $dateSubmitted = !empty($r['created_at']) ? date('M d, Y', strtotime($r['created_at'])) : '—';
                        $prefSchedule = !empty($r['preferred_date']) 
                            ? date('M d, Y', strtotime($r['preferred_date'])) . (!empty($r['preferred_time']) ? ' (' . htmlspecialchars($r['preferred_time']) . ')' : '') 
                            : 'Flexible Schedule';
                        $reason = $r['reason'] ?? ($r['concern_category'] ?? '');
                      ?>
                      <tr id="row-<?= $id ?>">
                        <td class="td-id"><?= $reqId ?></td>
                        <td>
                          <div style="font-weight:700; color:#111827;"><?= htmlspecialchars($clientName) ?></div>
                          <?php if (!empty($reason)): ?>
                            <div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">
                              <?= htmlspecialchars($reason) ?>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td style="color:#4b5563;"><?= $dateSubmitted ?></td>
                        <td style="color:#374151; font-weight:600;"><?= $prefSchedule ?></td>
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

  <!-- View Request Details Modal -->
  <div id="view-request-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.6); backdrop-filter:blur(4px); z-index:99999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:14px; width:100%; max-width:580px; box-shadow:0 20px 60px rgba(0,0,0,0.25); overflow:hidden;">
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
            <div id="vm-id" class="td-id" style="font-size:1.1rem; margin-top:2px;">#MC-0000</div>
          </div>
          <div id="vm-status-wrap">
            <span id="vm-status" class="badge-status pending"><span class="status-dot"></span> Pending</span>
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
        <button type="button" id="vm-btn-assign" onclick="openScheduleModalFromDetails()" class="btn-topbar primary" style="display:flex; align-items:center; gap:6px;">
          <svg viewBox="0 0 24 24" style="width:16px;height:16px;fill:currentColor;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
          Assign Counselor &amp; Schedule
        </button>
      </div>
    </div>
  </div>

  <!-- ═══ Assign Counselor & Schedule Modal ═══ -->
  <div id="schedule-assign-modal" style="position:fixed; inset:0; background:rgba(15,30,22,0.68); backdrop-filter:blur(6px); z-index:999999; display:none; align-items:center; justify-content:center; padding:16px;">
    <div style="background:white; border-radius:18px; width:100%; max-width:620px; box-shadow:0 30px 80px rgba(0,0,0,0.35); overflow:hidden; border:1px solid rgba(0,0,0,0.08);">
      <div style="height:4px; background:linear-gradient(90deg, #14532D, #7c3aed);"></div>
      <div style="padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; background:#fafafa;">
        <h5 style="margin:0; font-family:'Lora',serif; color:#14532D; font-size:1.18rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#7c3aed;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
          Assign Counselor &amp; Schedule Appointment
        </h5>
        <button type="button" onclick="closeScheduleModal()" style="background:none; border:none; font-size:24px; line-height:1; color:#9ca3af; cursor:pointer;" title="Close">&times;</button>
      </div>

      <form id="form-assign-sched" onsubmit="submitScheduleAssign(event)" style="padding:22px 24px; max-height:calc(85vh - 120px); overflow-y:auto;">
        <input type="hidden" id="as-request-id" name="id" value="" />

        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Applicant</span>
            <div id="as-client-name" style="font-weight:800; font-size:1.02rem; color:#14532D; margin-top:2px;">—</div>
          </div>
          <div style="text-align:right;">
            <span style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#166534;">Request ID</span>
            <div id="as-ref-no" style="font-weight:800; font-size:0.95rem; color:#14532D; margin-top:2px;">#MC-0000</div>
          </div>
        </div>

        <div id="as-conflict-alert" style="display:none; background:#fef2f2; border:1px solid #f87171; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:0.84rem; color:#b91c1c;">
          <strong>⚠️ Schedule Conflict Alert:</strong> <span id="as-conflict-msg"></span>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Assigned Counselor <span style="color:#ef4444;">*</span>
          </label>
          <select id="as-counselor-id" required style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600; background:#fff;" onchange="checkCounselorConflict()">
            <option value="">-- Select Qualified Counselor --</option>
            <?php foreach ($counselors ?? [] as $c): ?>
              <option value="<?= $c['id'] ?>">
                <?= htmlspecialchars($c['name'] ?? $c['full_name'] ?? '') ?> (<?= htmlspecialchars($c['designation'] ?? 'Counselor') ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Appointment Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" id="as-date" required min="<?= date('Y-m-d') ?>" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" onchange="checkCounselorConflict()" />
          <input type="hidden" id="as-session-type" value="In-Person" />
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              Start Time <span style="color:#ef4444;">*</span>
            </label>
            <input type="time" id="as-start-time" required value="10:00" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" onchange="checkCounselorConflict()" />
          </div>
          <div>
            <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
              End Time
            </label>
            <input type="time" id="as-end-time" value="11:00" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem; font-weight:600;" onchange="checkCounselorConflict()" />
          </div>
        </div>

        <div id="as-wrap-location" style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Venue / Consultation Room
          </label>
          <input type="text" id="as-location" value="ISCAG Center, 2nd Floor Da'wah Counseling Office Room 204" style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.9rem;" />
        </div>



        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#374151; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.04em;">
            Counselor Notes / Guidance to Client
          </label>
          <textarea id="as-notes" rows="2" placeholder="Please bring any relevant questions or notes. May Allah grant you ease." style="width:100%; box-sizing:border-box; padding:9px 12px; border-radius:8px; border:1px solid #cbd5e1; font-size:0.88rem; font-family:inherit;"></textarea>
        </div>

        <div style="padding:14px 0 0; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px;">
          <button type="button" onclick="closeScheduleModal()" style="padding:9px 18px; border-radius:8px; border:1px solid #cbd5e1; background:white; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
          <button type="submit" id="as-submit-btn" class="btn-topbar primary" style="padding:9px 22px; font-weight:700;">
            Confirm Assignment &amp; Schedule
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- 3-Dots Action Dropdown Menu -->
  <div id="counseling-req-action-menu" class="counseling-action-menu">
    <button type="button" class="action-dropdown-item schedule" onclick="triggerReqAction('schedule')">
      <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
      <span>Set Schedule</span>
    </button>
    <button type="button" class="action-dropdown-item view" onclick="triggerReqAction('view')">
      <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
      <span>View</span>
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

    const dawahPrefix = '<?= ($dawah_type ?? "male") === "female" ? "FC-" : "MC-" ?>';

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

    let currentViewingId = null;

    function openViewModal(id) {
      currentViewingId = id;
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
      let badgeClass = 'pending';
      let badgeText = 'Pending';
      if (stLower === 'accepted' || stLower === 'confirmed' || stLower === 'approved') {
        badgeClass = 'approved';
        badgeText = 'Accepted';
      } else if (stLower === 'rejected' || stLower === 'cancelled') {
        badgeClass = 'rejected';
        badgeText = 'Rejected';
      }

      document.getElementById('vm-status-wrap').innerHTML = `
        <span class="badge-status ${badgeClass}">
          <span class="status-dot"></span> ${badgeText}
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

    function openScheduleModalFromDetails() {
      if (currentViewingId) {
        closeViewModal();
        openScheduleModal(currentViewingId);
      }
    }

    function openScheduleModal(id) {
      const r = recordsMap[id];
      if (!r) return;

      document.getElementById('as-request-id').value = id;
      const reqId = '#' + dawahPrefix + String(r.id).padStart(4, '0');
      const clientName = r.full_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous Client';
      document.getElementById('as-client-name').textContent = clientName;
      document.getElementById('as-ref-no').textContent = reqId;

      if (r.preferred_date) {
        document.getElementById('as-date').value = r.preferred_date;
      } else {
        document.getElementById('as-date').value = new Date().toISOString().split('T')[0];
      }

      if (r.preferred_time) {
        const m = r.preferred_time.match(/(\d{1,2}):(\d{2})/);
        if (m) {
          let hrs = parseInt(m[1]);
          if (r.preferred_time.toLowerCase().includes('pm') && hrs < 12) hrs += 12;
          if (r.preferred_time.toLowerCase().includes('am') && hrs === 12) hrs = 0;
          document.getElementById('as-start-time').value = `${String(hrs).padStart(2, '0')}:${m[2]}`;
        }
      }

      if (r.counselor_id) {
        document.getElementById('as-counselor-id').value = r.counselor_id;
      } else {
        document.getElementById('as-counselor-id').value = '';
      }

      document.getElementById('as-conflict-alert').style.display = 'none';
      document.getElementById('schedule-assign-modal').style.display = 'flex';
      checkCounselorConflict();
    }

    function closeScheduleModal() {
      const m = document.getElementById('schedule-assign-modal');
      if (m) m.style.display = 'none';
      const ca = document.getElementById('as-conflict-alert');
      if (ca) ca.style.display = 'none';
    }

    document.getElementById('schedule-assign-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeScheduleModal();
    });

    function toggleAsLocationFields() {
      // All sessions are in-person
    }

    async function checkCounselorConflict() {
      const cid = document.getElementById('as-counselor-id')?.value;
      const date = document.getElementById('as-date')?.value;
      const startTime = document.getElementById('as-start-time')?.value;
      const endTime = document.getElementById('as-end-time')?.value;
      const reqId = document.getElementById('as-request-id')?.value;

      const alertBox = document.getElementById('as-conflict-alert');
      const msgSpan = document.getElementById('as-conflict-msg');
      if (!alertBox || !msgSpan) return;

      if (!cid || !date || !startTime) {
        alertBox.style.display = 'none';
        return;
      }

      try {
        const res = await fetch('<?= url("/admin/dawah/counseling/check-conflict") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ counselor_id: cid, date: date, start_time: startTime, end_time: endTime, exclude_id: reqId })
        });
        const data = await res.json();
        if (data.conflict) {
          alertBox.style.display = 'block';
          msgSpan.textContent = data.message || 'Conflict detected for this counselor at the selected time.';
        } else {
          alertBox.style.display = 'none';
        }
      } catch (e) {
        console.error(e);
      }
    }

    async function submitScheduleAssign(e) {
      e.preventDefault();
      const id = document.getElementById('as-request-id').value;
      const cid = document.getElementById('as-counselor-id').value;
      const aptDate = document.getElementById('as-date').value;
      const startTime = document.getElementById('as-start-time').value;
      const endTime = document.getElementById('as-end-time').value;
      const sessionType = document.getElementById('as-session-type').value;
      const location = document.getElementById('as-location').value;
      const link = document.getElementById('as-link').value;
      const notes = document.getElementById('as-notes').value;

      if (!id || !cid || !aptDate || !startTime) {
        alert('Please complete all required schedule fields.');
        return;
      }

      const submitBtn = document.getElementById('as-submit-btn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving Assignment...';
      }

      try {
        const response = await fetch('<?= url("/admin/dawah/counseling/accept-schedule") ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id: id,
            counselor_id: cid,
            appointment_date: aptDate,
            start_time: startTime,
            end_time: endTime,
            final_session_type: sessionType,
            appointment_location: location,
            meeting_link: link,
            counselor_notes: notes
          })
        });

        const res = await response.json();
        if (res.success) {
          if (typeof showAlert === 'function') {
            showAlert('Counselor Assigned', res.message || 'Counseling appointment confirmed and counselor assigned successfully!', 'success');
          } else {
            alert(res.message || 'Counseling appointment confirmed and counselor assigned successfully!');
          }
          setTimeout(() => location.reload(), 1000);
        } else {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirm Assignment & Schedule';
          }
          alert(res.message || 'Failed to assign counselor.');
        }
      } catch (err) {
        console.error(err);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Confirm Assignment & Schedule';
        }
        alert('An unexpected network error occurred.');
      }
    }

    async function processRequest(id, action) {
      const r = recordsMap[id];
      const confirmMsg = action === 'accept'
        ? `Are you sure you want to ACCEPT counseling request #${dawahPrefix}${String(id).padStart(4, '0')}?`
        : `Are you sure you want to REJECT counseling request #${dawahPrefix}${String(id).padStart(4, '0')}?`;

      if (!confirm(confirmMsg)) return;

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

          // Move row from pending table to approved or rejected table
          const row = document.getElementById('row-' + id);
          if (row) {
            row.remove();

            const targetTbodyId = action === 'accept' ? 'tbody-approved' : 'tbody-rejected';
            const targetTbody = document.getElementById(targetTbodyId);

            const reqId = '#' + dawahPrefix + String(id).padStart(4, '0');
            const clientName = r ? (r.full_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Anonymous') : 'User';
            const dateSub = r && r.created_at ? r.created_at.substring(0, 10) : '—';
            const prefSchedule = r && r.preferred_date ? `${r.preferred_date} ${r.preferred_time || ''}` : 'Flexible';
            const statusBadge = action === 'accept'
              ? `<span class="badge-status approved"><span class="status-dot"></span> Accepted</span>`
              : `<span class="badge-status rejected"><span class="status-dot"></span> Rejected</span>`;

            const newRow = `
              <tr id="row-${id}">
                <td class="td-id">${reqId}</td>
                <td>
                  <div style="font-weight:700; color:#111827;">${clientName}</div>
                  ${r && r.reason ? `<div style="font-size:0.78rem; font-weight:600; color:var(--primary); margin-top:2px;">${r.reason}</div>` : ''}
                </td>
                <td style="color:#4b5563;">${dateSub}</td>
                <td style="color:#374151; font-weight:600;">${prefSchedule}</td>
                <td>${statusBadge}</td>
                <td>
                  <div class="actions-cell">
                    <button type="button" class="btn-circle dots" onclick="toggleReqMenu(event, ${id})" title="Actions" aria-label="Actions">
                      <svg viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            `;

            if (targetTbody) {
              // If only empty row exists, clear it
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

            if (action === 'accept') {
              const statAppr = document.getElementById('stat-approved');
              const countAppr = document.getElementById('count-approved');
              if (statAppr && countAppr) {
                const current = parseInt(statAppr.textContent || '0') + 1;
                statAppr.textContent = current;
                countAppr.textContent = current;
              }
            } else {
              const statRej = document.getElementById('stat-rejected');
              const countRej = document.getElementById('count-rejected');
              if (statRej && countRej) {
                const current = parseInt(statRej.textContent || '0') + 1;
                statRej.textContent = current;
                countRej.textContent = current;
              }
            }
          }
        } else {
          alert(res.message || 'Error processing request.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error.');
      }
    }

    /* ── 3-Dots Action Menu Handlers for Requests Page ── */
    let activeReqId = null;
    let activeReqButton = null;

    function toggleReqMenu(e, id) {
      e.stopPropagation();
      const menu = document.getElementById('counseling-req-action-menu');
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
      const menuHeight = menu.offsetHeight || 140;

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
      const menu = document.getElementById('counseling-req-action-menu');
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
        deleteCounselingReqRecord(id);
      }
    }

    async function deleteCounselingReqRecord(id) {
      const r = recordsMap[id];
      const refNo = '#' + dawahPrefix + String(id).padStart(4, '0');
      const name = r ? (r.full_name || `${r.first_name || ''} ${r.last_name || ''}`.trim() || 'Client') : 'Client';

      if (!confirm(`Are you sure you want to delete counseling request ${refNo} for "${name}"?\n\nThis will permanently remove the record.`)) {
        return;
      }

      try {
        const response = await fetch('<?= url('/admin/dawah/counseling/delete') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const res = await response.json();
        if (res.success) {
          showAlert('Deleted Successfully', `Counseling request ${refNo} has been deleted.`, 'success');
          const row = document.getElementById(`row-${id}`);
          if (row) row.remove();
          setTimeout(() => location.reload(), 900);
        } else {
          showAlert('Deletion Failed', res.message || 'Could not delete request.', 'error');
        }
      } catch (err) {
        console.error(err);
        showAlert('Network Error', 'Failed to connect to server.', 'error');
      }
    }

    document.addEventListener('click', function(e) {
      const menu = document.getElementById('counseling-req-action-menu');
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
