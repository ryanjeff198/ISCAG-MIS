<?php
  $active_page = 'maintenance';
  $requests = $requests ?? [];
  $tenants = $tenants ?? [];
  $pending = array_values(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'Pending'));
  $active  = array_values(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'In Progress'));
  $history = array_values(array_filter($requests, fn($r) => in_array($r['status'] ?? '', ['Completed', 'Rejected'], true)));
  $resolvedCount = count(array_filter($history, fn($r) => ($r['status'] ?? '') === 'Completed'));

  $flashSuccess = $_SESSION['maint_flash_success'] ?? null;
  $flashError   = $_SESSION['maint_flash_error'] ?? null;
  unset($_SESSION['maint_flash_success'], $_SESSION['maint_flash_error']);

  $sidebarRel      = $maintConfig['sidebar'] ?? '/app/views/components/mis_admin_sidebar.php';
  $dashboardUrl    = $maintConfig['dashboardUrl'] ?? '/admin/dashboard';
  $routeBase       = $maintConfig['routeBase'] ?? '/admin/mis_admin/maintenance';
  $reportUrl       = $maintConfig['reportUrl'] ?? null;
  $standardizeRole = $maintConfig['standardizeRole'] ?? 'admin';
  $subtitle        = $maintConfig['subtitle'] ?? 'Manage facility repairs, tenant service requests, and maintenance reports';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Maintenance Requests &amp; Reports</title>
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

    .modal-content { max-width: 620px; }
    .detail-row { display: flex; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; gap: 12px; }
    .detail-label { width: 140px; flex-shrink: 0; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); }
    .detail-value { flex: 1; font-size: 0.9rem; color: var(--text-main); font-weight: 600; }
    .attachment-preview { width: 100%; border-radius: 8px; margin-top: 12px; border: 1px solid var(--border); max-height: 300px; object-fit: contain; background: #000; }

    .maint-filter-bar {
      display: flex; gap: 12px; flex-wrap: wrap; align-items: center;
      margin-bottom: 18px; background: #fff; border: 1px solid var(--border);
      border-radius: 12px; padding: 12px 16px;
    }
    .maint-filter-bar input, .maint-filter-bar select {
      padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px;
      font-size: 0.85rem; background: #fff; color: var(--text-main);
    }
    .maint-filter-bar input { flex: 1; min-width: 220px; }

    .flash-banner {
      padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 0.88rem; font-weight: 600;
      display: flex; justify-content: space-between; align-items: center;
    }
    .flash-banner.success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .flash-banner.error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    .form-grid { display: grid; gap: 14px; }
    .form-field label {
      display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
      color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.03em;
    }
    .form-field input, .form-field select, .form-field textarea {
      width: 100%; box-sizing: border-box; padding: 9px 12px;
      border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem;
      font-family: inherit; color: var(--text-main); background: #fff;
    }

    .print-report-only { display: none; }
    @media print {
      @page { size: letter portrait; margin: 0.5in; }
      body { background: #fff !important; color: #111827 !important; }
      .sidebar, .sidebar-backdrop, .top-bar, .no-print, .admin-insights, .tab-nav, .tab-panel, .maint-filter-bar, .flash-banner { display: none !important; }
      .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
      .page-body { padding: 0 !important; }
      .print-report-only { display: block !important; }
      .print-header-block {
        display: flex; align-items: center; gap: 14px;
        border-bottom: 2px solid #166534; padding-bottom: 10px; margin-bottom: 14px;
      }
      .print-report-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
      .print-report-table th, .print-report-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
      .print-report-table th { background: #f1f5f9 !important; font-weight: 700; text-transform: uppercase; font-size: 7.5pt; }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . $sidebarRel; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="ISCAG" />
          <div>
            <div class="top-bar-title">Maintenance Requests &amp; Reports</div>
            <div class="top-bar-subtitle"><?= htmlspecialchars($subtitle) ?></div>
          </div>
        </div>
        <div class="top-bar-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
          <button type="button" class="btn-topbar primary" onclick="openModal('new-maintenance-modal')">+ New Maintenance Request</button>
          <button type="button" class="btn-topbar" onclick="window.print()">Print Report</button>
          <?php if ($reportUrl): ?>
            <a href="<?= url($reportUrl) ?>" class="btn-topbar">Department Report</a>
          <?php endif; ?>
          <a href="<?= url($dashboardUrl) ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        <?php if ($flashSuccess): ?>
          <div class="flash-banner success">
            <span><?= htmlspecialchars($flashSuccess) ?></span>
            <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:18px;cursor:pointer;color:inherit;">&times;</button>
          </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
          <div class="flash-banner error">
            <span><?= htmlspecialchars($flashError) ?></span>
            <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;font-size:18px;cursor:pointer;color:inherit;">&times;</button>
          </div>
        <?php endif; ?>

        <!-- Admin Insights Ribbon -->
        <div class="admin-insights">
          <div class="insight-card pending">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning"><?= count($pending) ?></div>
            <div class="insight-icon-bg"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg></div>
          </div>
          <div class="insight-card total">
            <div class="insight-label">In Progress</div>
            <div class="insight-value info"><?= count($active) ?></div>
            <div class="insight-icon-bg"><svg viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg></div>
          </div>
          <div class="insight-card verified">
            <div class="insight-label">Resolved (Completed)</div>
            <div class="insight-value success"><?= $resolvedCount ?></div>
            <div class="insight-icon-bg"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Total Logged</div>
            <div class="insight-value"><?= count($requests) ?></div>
            <div class="insight-icon-bg"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
          </div>
        </div>

        <!-- Search & Category Filter Bar -->
        <div class="maint-filter-bar no-print">
          <input type="text" id="maint-search" placeholder="Search by tenant, unit, description, or ref #..." oninput="filterMaintenanceRows()" />
          <select id="maint-cat-filter" onchange="filterMaintenanceRows()">
            <option value="">All Categories</option>
            <option value="Plumbing">Plumbing</option>
            <option value="Electrical">Electrical</option>
            <option value="Structural">Structural</option>
            <option value="Appliance">Appliance</option>
            <option value="Pest Control">Pest Control</option>
            <option value="Others">Others</option>
          </select>
        </div>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending', this)">
            Pending <span class="tab-count pending"><?= count($pending) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('active', this)">
            In Progress <span class="tab-count approved"><?= count($active) ?></span>
          </button>
          <button class="tab-btn" onclick="switchTab('history', this)">
            History <span class="tab-count rejected"><?= count($history) ?></span>
          </button>
        </div>

        <!-- PENDING TABLE -->
        <div class="section-card tab-panel" id="tab-pending">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;fill:var(--primary);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
              Pending Approval
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant / Reporter</th>
                    <th>Unit / Room</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($pending)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No pending maintenance requests.</td></tr>
                  <?php else: ?>
                    <?php foreach ($pending as $r):
                      $unitStr = !empty($r['room_number']) ? trim(($r['building'] ?? '') . '-' . $r['room_number'], '-') : 'General';
                    ?>
                      <tr class="maint-row" data-cat="<?= htmlspecialchars($r['category'] ?? '') ?>">
                        <td class="td-id">MNT-<?= (int)$r['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($unitStr) ?></td>
                        <td><span style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($r['category']) ?></span></td>
                        <td style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['description']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                        <td><span class="badge-status badge-pending"><?= htmlspecialchars($r['status']) ?></span></td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-circle eye" onclick="viewMaintenance(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                            <button class="btn-circle check" onclick="approveMaintenance(<?= (int)$r['id'] ?>)" title="Approve & Start">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </button>
                            <button class="btn-circle reject" onclick="rejectMaintenance(<?= (int)$r['id'] ?>)" title="Reject">
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

        <!-- ACTIVE TABLE -->
        <div class="section-card tab-panel" id="tab-active" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;fill:var(--success);"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              In Progress Repairs
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant / Reporter</th>
                    <th>Unit / Room</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Updated</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($active)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No active repairs.</td></tr>
                  <?php else: ?>
                    <?php foreach ($active as $r):
                      $unitStr = !empty($r['room_number']) ? trim(($r['building'] ?? '') . '-' . $r['room_number'], '-') : 'General';
                    ?>
                      <tr class="maint-row" data-cat="<?= htmlspecialchars($r['category'] ?? '') ?>">
                        <td class="td-id">MNT-<?= (int)$r['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($unitStr) ?></td>
                        <td><span style="font-weight:700; color:var(--primary);"><?= htmlspecialchars($r['category']) ?></span></td>
                        <td style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['description']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['updated_at'] ?? $r['created_at'])) ?></td>
                        <td><span class="badge-status badge-approved"><?= htmlspecialchars($r['status']) ?></span></td>
                        <td>
                          <div class="actions-cell">
                            <button class="btn-circle eye" onclick="viewMaintenance(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
                              <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                            <button class="btn-circle check" onclick="resolveMaintenance(<?= (int)$r['id'] ?>)" title="Mark as Resolved">
                              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
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

        <!-- HISTORY TABLE -->
        <div class="section-card tab-panel" id="tab-history" style="display:none;">
          <div class="section-card-header">
            <h6>
              <svg viewBox="0 0 24 24" style="width:18px;fill:var(--text-muted);"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
              Completed / Rejected History
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant / Reporter</th>
                    <th>Unit / Room</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Completed / Updated</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($history)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">No history items found.</td></tr>
                  <?php else: ?>
                    <?php foreach ($history as $r):
                      $unitStr = !empty($r['room_number']) ? trim(($r['building'] ?? '') . '-' . $r['room_number'], '-') : 'General';
                    ?>
                      <tr class="maint-row" data-cat="<?= htmlspecialchars($r['category'] ?? '') ?>">
                        <td class="td-id">MNT-<?= (int)$r['id'] ?></td>
                        <td style="font-weight:600;"><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                        <td><?= htmlspecialchars($unitStr) ?></td>
                        <td><?= htmlspecialchars($r['category']) ?></td>
                        <td style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($r['description']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['updated_at'] ?? $r['created_at'])) ?></td>
                        <td>
                          <span class="badge-status <?= $r['status'] === 'Completed' ? 'badge-approved' : 'badge-rejected' ?>">
                            <?= htmlspecialchars($r['status']) ?>
                          </span>
                        </td>
                        <td>
                          <button class="btn-circle eye" onclick="viewMaintenance(<?= htmlspecialchars(json_encode($r)) ?>)" title="View Details">
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

        <!-- PRINTABLE MAINTENANCE REPORT SECTION -->
        <div class="print-report-only">
          <div class="print-header-block">
            <img src="<?= asset('assets/logo.jpg') ?>" style="width:48px;height:48px;border-radius:6px;" alt="ISCAG" />
            <div>
              <h2 style="margin:0;color:#166534;font-size:13pt;">ISLAMIC CENTER &amp; GUIDANCE (ISCAG) — MAINTENANCE REPORT</h2>
              <p style="margin:2px 0 0;font-size:8.5pt;color:#4b5563;">
                Generated: <?= date('M d, Y h:i A') ?> &bull;
                By: <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?> &bull;
                Pending: <?= count($pending) ?> | In Progress: <?= count($active) ?> | Resolved: <?= $resolvedCount ?> | Total: <?= count($requests) ?>
              </p>
            </div>
          </div>
          <table class="print-report-table">
            <thead>
              <tr>
                <th>Ref #</th>
                <th>Category</th>
                <th>Tenant / Reporter</th>
                <th>Unit</th>
                <th>Description</th>
                <th>Reported</th>
                <th>Remarks</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($requests)): ?>
                <tr><td colspan="8" style="text-align:center;padding:18px;">No maintenance records available.</td></tr>
              <?php else: ?>
                <?php foreach ($requests as $r):
                  $unitStr = !empty($r['room_number']) ? trim(($r['building'] ?? '') . '-' . $r['room_number'], '-') : 'General';
                ?>
                  <tr>
                    <td>MNT-<?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['category'] ?? '') ?></td>
                    <td><?= htmlspecialchars(trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($unitStr) ?></td>
                    <td><?= htmlspecialchars($r['description'] ?? '') ?></td>
                    <td><?= date('M d, Y', strtotime($r['created_at'] ?? 'now')) ?></td>
                    <td><?= htmlspecialchars($r['admin_remarks'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($r['status'] ?? 'Pending') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Detail Modal -->
  <div class="modal-backdrop" id="maintenance-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Maintenance Request Details</h5>
        <button class="modal-close" onclick="closeModal('maintenance-modal')">&times;</button>
      </div>
      <div class="modal-body">
        <div class="detail-row">
          <div class="detail-label">Reference</div>
          <div class="detail-value" id="d-ref"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Tenant / Reporter</div>
          <div class="detail-value" id="d-tenant"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Unit / Room</div>
          <div class="detail-value" id="d-unit"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Category</div>
          <div class="detail-value" id="d-category"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Date Reported</div>
          <div class="detail-value" id="d-date"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Status</div>
          <div class="detail-value" id="d-status"></div>
        </div>
        <div class="detail-row">
          <div class="detail-label">Admin Remarks</div>
          <div class="detail-value" id="d-remarks" style="font-weight:400;"></div>
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
      <div class="modal-footer" id="modal-footer"></div>
    </div>
  </div>

  <!-- New Maintenance Request Modal -->
  <div class="modal-backdrop" id="new-maintenance-modal" style="display:none;">
    <div class="modal-content">
      <div class="modal-header">
        <h5>New Maintenance Request / Report</h5>
        <button class="modal-close" onclick="closeModal('new-maintenance-modal')">&times;</button>
      </div>
      <form method="POST" action="<?= url($routeBase . '/create') ?>" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-grid">
            <div class="form-field">
              <label for="new-maint-tenant">Resident / Account (or Facility Reporter)</label>
              <select name="tenant_id" id="new-maint-tenant" required>
                <option value="<?= (int)($_SESSION['user_id'] ?? 0) ?>">General Facility Maintenance (<?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?>)</option>
                <?php foreach ($tenants as $t):
                  $tUnit = !empty($t['room_number']) ? ' [' . trim(($t['building'] ?? '') . '-' . $t['room_number'], '-') . ']' : '';
                ?>
                  <option value="<?= (int)$t['tenant_id'] ?>">
                    <?= htmlspecialchars(trim(($t['first_name'] ?? '') . ' ' . ($t['last_name'] ?? '')) . $tUnit . ' — ' . ($t['role'] ?? 'User')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
              <div class="form-field">
                <label for="new-maint-category">Issue Category</label>
                <select name="category" id="new-maint-category" required>
                  <option value="">Select category</option>
                  <option value="Plumbing">Plumbing</option>
                  <option value="Electrical">Electrical</option>
                  <option value="Structural">Structural</option>
                  <option value="Appliance">Appliance</option>
                  <option value="Pest Control">Pest Control</option>
                  <option value="Others">Others</option>
                </select>
              </div>
              <div class="form-field">
                <label for="new-maint-status">Initial Status</label>
                <select name="status" id="new-maint-status">
                  <option value="Pending">Pending Review</option>
                  <option value="In Progress">In Progress (Immediate Dispatch)</option>
                </select>
              </div>
            </div>
            <div class="form-field">
              <label for="new-maint-desc">Issue Description</label>
              <textarea name="description" id="new-maint-desc" rows="3" placeholder="Describe the maintenance issue or inspection findings..." required></textarea>
            </div>
            <div class="form-field">
              <label for="new-maint-remarks">Admin Remarks / Notes (Optional)</label>
              <input type="text" name="admin_remarks" id="new-maint-remarks" placeholder="Assigned technician, priority, or notes..." />
            </div>
            <div class="form-field">
              <label for="new-maint-att">Photo Attachment (Optional)</label>
              <input type="file" name="attachment" id="new-maint-att" accept="image/*" />
            </div>
          </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:10px;">
          <button type="button" class="btn-topbar" onclick="closeModal('new-maintenance-modal')">Cancel</button>
          <button type="submit" class="btn-topbar primary">Submit Maintenance Request</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage(<?= json_encode($standardizeRole) ?>);

    const maintRouteBase = <?= json_encode(url($routeBase)) ?>;
    const assetBase = <?= json_encode(asset('')) ?>;

    function switchTab(tabName, btn) {
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      const target = document.getElementById('tab-' + tabName);
      if (target) target.style.display = 'block';
      if (btn) btn.classList.add('active');
    }

    function filterMaintenanceRows() {
      const q = (document.getElementById('maint-search').value || '').toLowerCase().trim();
      const cat = (document.getElementById('maint-cat-filter').value || '').toLowerCase();
      document.querySelectorAll('.maint-row').forEach(row => {
        const text = (row.textContent || '').toLowerCase();
        const rowCat = (row.dataset.cat || '').toLowerCase();
        const matchQ = !q || text.includes(q);
        const matchCat = !cat || rowCat === cat;
        row.style.display = (matchQ && matchCat) ? '' : 'none';
      });
    }

    function viewMaintenance(r) {
      document.getElementById('d-ref').textContent = 'MNT-' + r.id;
      document.getElementById('d-tenant').textContent = `${r.first_name || ''} ${r.last_name || ''} (${r.email || 'N/A'})`.trim();
      document.getElementById('d-unit').textContent = r.room_number ? ((r.building ? r.building + '-' : '') + r.room_number) : 'General / Unassigned';
      document.getElementById('d-category').textContent = r.category || 'General';
      document.getElementById('d-date').textContent = r.created_at ? new Date(r.created_at).toLocaleDateString() : '—';
      document.getElementById('d-status').textContent = r.status || 'Pending';
      document.getElementById('d-remarks').textContent = r.admin_remarks || '—';
      document.getElementById('d-desc').textContent = r.description || '';

      const attachment = document.getElementById('d-attachment-area');
      if (r.attachment) {
        document.getElementById('d-attachment').src = assetBase + r.attachment;
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
            <button class="btn-topbar primary" onclick="approveMaintenance(${r.id})">Approve &amp; Start</button>
          </div>
        `;
      } else if (r.status === 'In Progress') {
        footer.innerHTML = `
          <button class="btn-topbar" onclick="closeModal('maintenance-modal')">Close</button>
          <button class="btn-topbar primary" style="margin-left:auto;" onclick="resolveMaintenance(${r.id})">Mark as Resolved / Done</button>
        `;
      } else {
        footer.innerHTML = `<button class="btn-topbar" onclick="closeModal('maintenance-modal')">Close</button>`;
      }

      openModal('maintenance-modal');
    }

    function approveMaintenance(id) {
      if (confirm('Approve this maintenance request and set to "In Progress"?')) {
        window.location.href = maintRouteBase + '/approve?id=' + id;
      }
    }

    function resolveMaintenance(id) {
      if (confirm('Mark this maintenance request as Completed?')) {
        window.location.href = maintRouteBase + '/resolve?id=' + id;
      }
    }

    function rejectMaintenance(id) {
      const reason = prompt('Please enter a reason for rejection:', 'Not approved at this time.');
      if (reason !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = maintRouteBase + '/reject?id=' + id;
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
    setupModalClose('new-maintenance-modal');

    const urlParams = new URLSearchParams(window.location.search);
    const openId = urlParams.get('id');
    if (openId) {
      const requests = <?= json_encode($requests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
      const target = requests.find(r => r.id == openId);
      if (target) {
        if (target.status === 'In Progress') switchTab('active', document.querySelectorAll('.tab-btn')[1]);
        else if (target.status === 'Completed' || target.status === 'Rejected') switchTab('history', document.querySelectorAll('.tab-btn')[2]);
        viewMaintenance(target);
      }
    } else if (urlParams.get('new') === '1') {
      openModal('new-maintenance-modal');
    }
  </script>
</body>
</html>

