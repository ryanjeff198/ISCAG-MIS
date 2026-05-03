<?php $active_page = 'moveout_requests'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Move-Out Requests</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>" />
  <style>
    /* ═══════════════════════════════════════════
       MOVE-OUT DASHBOARD STYLES
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
      border-bottom: 3px solid var(--danger);
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

    .form-doc-title-bar { text-align: center; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 15px; }
    .form-doc-title {
      font-family: 'Lora', serif;
      font-size: 0.9rem; font-weight: 700;
      color: white; background: var(--danger);
      padding: 6px 24px; border-radius: 4px;
      letter-spacing: 0.08em; text-transform: uppercase;
    }

    .form-doc-body { padding: 15px 24px 20px; }
    .doc-section-title {
      font-family: 'Lora', serif;
      font-size: 0.8rem; font-weight: 700;
      color: var(--primary-dark); text-transform: uppercase;
      padding: 8px 0 6px; border-bottom: 2px solid var(--danger);
      margin-bottom: 12px; margin-top: 18px;
      display: flex; align-items: center; gap: 6px;
    }
    .doc-section-title:first-child { margin-top: 0; }

    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .info-table td { border: 1px solid var(--border); padding: 0; vertical-align: middle; }
    .info-table .field-label {
      font-size: 0.68rem; font-weight: 700;
      color: var(--text-muted); text-transform: uppercase;
      padding: 5px 8px; background: #fffcfc;
      white-space: nowrap; min-width: 120px;
    }
    .info-table .field-value { padding: 8px 10px; font-size: 0.82rem; color: var(--text-main); font-weight: 600; }

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
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .insight-card::after {
      content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--border);
    }
    .insight-card.pending::after { background: var(--warning); }
    .insight-card.total::after { background: var(--primary); }
    .insight-card.completed::after { background: var(--success); }

    .insight-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--primary-dark); line-height: 1; }

    .btn-circle {
      width: 32px; height: 32px; border-radius: 50%;
      display: inline-flex; align-items: center; justify-content: center;
      border: none; background: transparent; cursor: pointer; padding: 0;
    }
    .btn-circle svg { width: 18px; height: 18px; fill: var(--text-muted); }
    .btn-circle.eye:hover { background: rgba(30,144,255,0.1); }
    .btn-circle.eye:hover svg { fill: var(--info); }
    .btn-circle.check:hover { background: rgba(46,125,85,0.1); }
    .btn-circle.check:hover svg { fill: var(--success); }
    .btn-circle.cancel:hover { background: rgba(139,46,46,0.1); }
    .btn-circle.cancel:hover svg { fill: var(--danger); }

    /* ═══════════════════════════════════════════
       SETTLEMENT MODAL ENHANCEMENTS
       ═══════════════════════════════════════════ */
    .settlement-card {
      background: #f8fafc;
      border-radius: 12px;
      padding: 20px;
      border: 1px solid #e2e8f0;
      margin-bottom: 20px;
    }

    .settlement-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .stat-box {
      padding: 15px;
      border-radius: 10px;
      background: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
      border: 1px solid #edf2f7;
    }

    .stat-label {
      font-size: 0.7rem;
      font-weight: 700;
      color: #718096;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 4px;
      display: block;
    }

    .stat-value {
      font-size: 1.25rem;
      font-weight: 800;
    }

    .stat-value.deposit { color: var(--success); }
    .stat-value.deduction { color: var(--danger); }


    .input-icon-prefix {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.1rem;
      font-weight: 700;
      color: #718096;
      pointer-events: none;
      z-index: 5;
    }

    .input-with-icon {
      position: relative;
      display: block;
      width: 100%;
    }

    .input-with-icon .mis-input {
      width: 100%;
      box-sizing: border-box;
      padding-left: 38px;
      padding-top: 14px;
      padding-bottom: 14px;
      font-size: 1.25rem;
      font-weight: 800;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      transition: all 0.2s ease;
      background: #ffffff;
    }

    .input-with-icon .mis-input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(30, 80, 50, 0.1);
    }

    .input-with-icon .mis-input:read-only {
      background-color: #f1f5f9;
      color: #64748b;
      cursor: not-allowed;
      border-style: dashed;
    }

    .refund-banner {
      background: #ffffff;
      padding: 24px;
      border-radius: 12px;
      text-align: center;
      border: 2px dashed #e2e8f0;
      margin-top: 20px;
    }

    .refund-banner .stat-label {
      color: #64748b;
      margin-bottom: 8px;
    }

    .refund-banner .stat-value {
      font-size: 2.8rem;
      font-weight: 900;
      color: var(--primary);
    }

    #set-notes, .input-with-icon .mis-input {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    #set-notes {
      width: 100% !important;
      max-width: 100% !important;
      line-height: 1.6;
      display: block;
      box-sizing: border-box;
      padding: 12px;
    }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <!-- SIDEBAR -->
    <?php 
       $active_page = 'moveout_requests';
       include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; 
    ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <img src="<?= asset('assets/logo.jpg') ?>" style="width:40px;height:40px;border-radius:8px;margin-right:12px;" alt="Logo" />
          <div>
            <div class="top-bar-title">Move-Out Requests</div>
            <div class="top-bar-subtitle">Manage exit notices, final inspections, and account settlements</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/apartment') ?>" class="btn-topbar">← Dashboard</a>
        </div>
      </div>

      <div class="page-body">
        
        <!-- Admin Insights -->
        <div class="admin-insights">
          <div class="insight-card pending">
            <div class="insight-label">Pending Notices</div>
            <div class="insight-value warning"><?= count(array_filter($requests, fn($r) => $r['status'] === 'Pending')) ?></div>
          </div>
          <div class="insight-card total">
            <div class="insight-label">Total Requests</div>
            <div class="insight-value info"><?= count($requests) ?></div>
          </div>
          <div class="insight-card completed">
            <div class="insight-label">Move-Outs Completed</div>
            <div class="insight-value success"><?= count(array_filter($requests, fn($r) => $r['status'] === 'Completed')) ?></div>
          </div>
        </div>

        <?php
          $pending_reqs    = array_filter($requests, fn($r) => $r['status'] === 'Pending');
          $processing_reqs = array_filter($requests, fn($r) => $r['status'] === 'Processing');
          $history_reqs    = array_filter($requests, fn($r) => !in_array($r['status'], ['Pending', 'Processing']));
        ?>

        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchTab('pending', this)">Pending Notices <span class="tab-count pending"><?= count($pending_reqs) ?></span></button>
          <button class="tab-btn" onclick="switchTab('processing', this)">In Processing <span class="tab-count info"><?= count($processing_reqs) ?></span></button>
          <button class="tab-btn" onclick="switchTab('history', this)">History <span class="tab-count"><?= count($history_reqs) ?></span></button>
        </div>

        <!-- ═══ PENDING TABLE ═══ -->
        <div class="section-card tab-panel" id="tab-pending">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg> Active Notices to Vacate</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
             <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Date Filed</th>
                    <th>Target Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($pending_reqs)): ?>
                     <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No pending move-out notices.</td></tr>
                  <?php else: ?>
                     <?php foreach ($pending_reqs as $r): ?>
                        <tr>
                           <td class="td-id"><?= $r['request_id'] ?></td>
                           <td style="font-weight:600;"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                           <td style="color:var(--primary);font-weight:700;"><?= htmlspecialchars($r['room_number']) ?></td>
                           <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                           <td><?= $r['move_out_date'] ? date('M d, Y', strtotime($r['move_out_date'])) : 'TBD' ?></td>
                           <td>
                              <div class="actions-cell">
                                 <button class="btn-circle eye" onclick="openReview(<?= htmlspecialchars(json_encode($r)) ?>)" title="Review Details">
                                    <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                 </button>
                                 <button class="btn-circle check" onclick="startProcessing(<?= $r['request_id'] ?>)" title="Start Processing">
                                    <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
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

        <!-- ═══ PROCESSING TABLE ═══ -->
        <div class="section-card tab-panel" id="tab-processing" style="display:none;">
          <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg> Move-Outs Under Processing</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
             <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($processing_reqs)): ?>
                     <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">No move-outs currently in processing.</td></tr>
                  <?php else: ?>
                     <?php foreach ($processing_reqs as $r): ?>
                        <tr>
                           <td class="td-id"><?= $r['request_id'] ?></td>
                           <td style="font-weight:600;"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                           <td style="color:var(--primary);font-weight:700;"><?= htmlspecialchars($r['room_number']) ?></td>
                           <td><span class="badge-status badge-info">Processing</span></td>
                           <td>
                              <button class="btn-topbar primary" style="font-size:0.75rem; padding: 4px 12px;" onclick="openSettlementModal(<?= htmlspecialchars(json_encode($r)) ?>)">Clearance Form</button>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
             </table>
          </div>
        </div>

        <!-- ═══ HISTORY TABLE ═══ -->
        <div class="section-card tab-panel" id="tab-history" style="display:none;">
           <div class="section-card-header">
            <h6><svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg> Move-Out History</h6>
          </div>
          <div class="section-card-body" style="padding:0;">
             <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Tenant Name</th>
                    <th>Unit</th>
                    <th>Filed</th>
                    <th>Completed</th>
                    <th>Refund</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($history_reqs)): ?>
                     <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No historical records.</td></tr>
                  <?php else: ?>
                     <?php foreach ($history_reqs as $r): ?>
                        <tr>
                           <td class="td-id"><?= $r['request_id'] ?></td>
                           <td style="font-weight:600;"><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                           <td style="color:var(--primary);font-weight:700;"><?= htmlspecialchars($r['room_number']) ?></td>
                           <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                           <td><?= $r['status'] === 'Completed' ? date('M d, Y', strtotime($r['updated_at'])) : '—' ?></td>
                           <td style="font-weight:700;"><?= $r['status'] === 'Completed' ? '₱' . number_format($r['final_refund'], 2) : '—' ?></td>
                           <td><span class="badge-status <?= $r['status'] === 'Completed' ? 'badge-approved' : 'badge-rejected' ?>"><?= $r['status'] ?></span></td>
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

  <!-- Settlement Modal -->
  <div class="modal-backdrop" id="settlement-modal" style="display:none;">
    <div class="modal-content" style="max-width: 650px; border-radius: 16px;">
      <div class="modal-header" style="background: white; border-bottom: 1px solid #f1f5f9;">
         <h5 style="color: #1e293b; font-weight: 700;">
            <svg viewBox="0 0 24 24" style="width:22px;height:22px;fill:var(--primary);vertical-align:middle;margin-right:10px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
            Departure Clearance & Settlement
         </h5>
         <button class="modal-close" onclick="closeModal('settlement-modal')">×</button>
      </div>
      <div class="modal-body" style="padding: 24px;">
         <form id="settlement-form">
            <input type="hidden" name="request_id" id="set-request-id">
            
            <div class="doc-section-title" style="margin-top:0; border-color: var(--primary); color: #334155;">Final Inspection Findings</div>
            <div class="form-group" style="margin-bottom:24px;">
               <label class="stat-label">Inspection Notes</label>
               <textarea name="notes" id="set-notes" class="mis-input" style="min-height:120px; border-radius: 8px; font-size: 0.95rem; resize: vertical;" placeholder="Detail the condition of the unit (e.g. wall paint, fixtures, cleanliness, repairs needed)..."></textarea>
            </div>

            <div class="doc-section-title" style="border-color: var(--primary); color: #334155;">Financial Settlement</div>
            
            <div class="settlement-card">
               <div class="settlement-grid">
                  <div class="stat-box">
                     <span class="stat-label">Security Deposit</span>
                     <div class="stat-value deposit">₱1,000.00</div>
                  </div>
                  <div class="stat-box">
                     <span class="stat-label">Total Deductions</span>
                     <div class="stat-value deduction" id="display-deductions">₱0.00</div>
                  </div>
               </div>

               <div style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                  <div class="form-group">
                     <label class="stat-label">Damage / Repair Costs</label>
                     <div class="input-with-icon">
                        <span class="input-icon-prefix">₱</span>
                        <input type="number" name="damage" id="set-damage" class="mis-input" step="0.01" value="0" oninput="calculateRefund()">
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="stat-label">Unpaid Utility / Bills</label>
                     <div class="input-with-icon">
                        <span class="input-icon-prefix">₱</span>
                        <input type="number" name="utility" id="set-utility" class="mis-input" step="0.01" value="0" readonly oninput="calculateRefund()">
                     </div>
                     <span id="balance-info" style="font-size: 0.65rem; color: var(--primary); font-weight: 600; display: none; margin-top: 5px;">
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;vertical-align:middle;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg> 
                        System-generated outstanding balance
                     </span>
                  </div>
               </div>
            </div>

            <div class="refund-banner">
               <span class="stat-label">NET REFUND TO TENANT</span>
               <div class="stat-value" id="display-refund">₱1,000.00</div>
               <input type="hidden" name="refund" id="set-refund" value="1000">
            </div>
         </form>
      </div>
      <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px;">
         <button class="btn-topbar" style="background: white; border: 1px solid #e2e8f0; color: #64748b;" onclick="closeModal('settlement-modal')">Discard Changes</button>
         <button class="btn-topbar primary" id="btnFinalize" style="padding: 10px 24px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" onclick="finalizeMoveOut()">Complete Clearance & Vacate Unit</button>
      </div>
    </div>
  </div>

  <!-- Review Modal -->
  <div class="modal-backdrop" id="review-modal" style="display:none;">
    <div class="modal-content" style="max-width: 800px;">
      <div class="modal-header">
        <h5>Move-Out Request Details</h5>
        <button class="modal-close" onclick="closeModal('review-modal')">×</button>
      </div>
      <div class="modal-body" id="modal-body" style="padding:0; background: #f0f4f2;"></div>
      <div class="modal-footer" id="modal-footer"></div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    function switchTab(tabName, btn) {
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('tab-' + tabName).style.display = 'block';
      btn.classList.add('active');
    }

    function openReview(r) {
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
            </div>
            <div class="form-doc-title-bar">
              <div class="form-doc-title">Notice of Intent to Vacate</div>
            </div>
          </div>
          <div class="form-doc-body">
            <div class="doc-section-title">Tenant & Unit Information</div>
            <table class="info-table">
              <tr>
                <td class="field-label">Tenant Name</td>
                <td class="field-value">${r.first_name} ${r.last_name}</td>
                <td class="field-label">Tenant ID</td>
                <td class="field-value">${r.tenant_id}</td>
              </tr>
              <tr>
                <td class="field-label">Assigned Unit</td>
                <td class="field-value">${r.room_number}</td>
                <td class="field-label">Notice Date</td>
                <td class="field-value">${new Date(r.created_at).toLocaleDateString()}</td>
              </tr>
            </table>

            <div class="doc-section-title">Exit Clearance Readiness</div>
            <div style="padding: 15px; background: #fffafb; border: 1px solid #fecaca; border-radius: 8px;">
                <p style="font-size: 0.85rem; color: #7f1d1d; margin-bottom: 10px;"><strong>Status:</strong> ${r.status}</p>
                <p style="font-size: 0.82rem; color: #4b5563; line-height: 1.5;">This tenant has formally submitted a request to vacate. The administration must now perform a final inspection and financial settlement.</p>
            </div>
          </div>
        </div>
      `;

      if (r.status === 'Pending') {
        document.getElementById('modal-footer').innerHTML = `
          <button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>
          <div style="margin-left:auto; display:flex; gap:10px;">
             <button class="btn-topbar" style="color:var(--danger);" onclick="updateStatus(${r.request_id}, 'Cancelled')">Cancel Request</button>
             <button class="btn-topbar primary" onclick="startProcessing(${r.request_id})">Start Move-Out Processing</button>
          </div>
        `;
      } else {
        document.getElementById('modal-footer').innerHTML = `<button class="btn-topbar" onclick="closeModal('review-modal')">Close</button>`;
      }
      openModal('review-modal');
    }

    async function startProcessing(id) {
       if(!confirm('Acknowledge this notice and start processing? (Tenant will be notified)')) return;
       const res = await fetch('<?= url("/admin/apartment/moveout/action") ?>', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({request_id: id, action: 'start'})
       }).then(r => r.json());

       if(res.success) {
          showToast('info', 'Processing started successfully.');
          setTimeout(() => location.reload(), 1000);
       } else {
          alert('Error: ' + res.message);
       }
    }

    async function openSettlementModal(r) {
       document.getElementById('set-request-id').value = r.request_id;
       document.getElementById('set-damage').value = 0;
       document.getElementById('set-notes').value = '';
       
       const utilInput = document.getElementById('set-utility');
       const balanceInfo = document.getElementById('balance-info');
       
       utilInput.value = 0;
       balanceInfo.style.display = 'none';

       // Pull customer outstanding balance (Rental + Utility Charges)
       try {
          const res = await fetch(`<?= url("/admin/apartment/tenant/balance") ?>?tid=${r.tenant_id}`)
                             .then(response => response.json());
          if (res.success && res.balance > 0) {
             utilInput.value = res.balance;
             balanceInfo.style.display = 'block';
             showToast('info', `Pulled outstanding balance: ₱${res.balance.toLocaleString()}`);
          }
       } catch (err) {
          console.error("Error fetching balance:", err);
       }

       calculateRefund();
       openModal('settlement-modal');
    }

    function calculateRefund() {
       const dmg = parseFloat(document.getElementById('set-damage').value) || 0;
       const util = parseFloat(document.getElementById('set-utility').value) || 0;
       
       // Deductions only count damages (Utilities are separate charges)
       const totalDeductions = dmg;
       const totalRefund = Math.max(0, 1000 - totalDeductions);

       document.getElementById('display-deductions').textContent = '₱' + totalDeductions.toLocaleString(undefined, {minimumFractionDigits:2});
       document.getElementById('display-refund').textContent = '₱' + totalRefund.toLocaleString(undefined, {minimumFractionDigits:2});
       document.getElementById('set-refund').value = totalRefund;
    }

    async function finalizeMoveOut() {
       if(!confirm('Are you sure you want to finalize this move-out? This will mark the unit as VACANT.')) return;
       
       const btn = document.getElementById('btnFinalize');
       btn.disabled = true;
       btn.textContent = 'Processing...';

       const form = document.getElementById('settlement-form');
       const formData = new FormData(form);
       const data = Object.fromEntries(formData.entries());
       data.action = 'finalize';

       const res = await fetch('<?= url("/admin/apartment/moveout/action") ?>', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify(data)
       }).then(r => r.json());

       if(res.success) {
          showToast('success', 'Move-out successfully finalized.');
          setTimeout(() => location.reload(), 1500);
       } else {
          alert('Error: ' + res.message);
          btn.disabled = false;
          btn.textContent = 'Complete Clearance & Vacate Unit';
       }
    }

    async function updateStatus(id, status) {
       // Placeholder for generic state changes
    }
  </script>
</body>
</html>

</body>
</html>
