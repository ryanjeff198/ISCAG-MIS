<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male', 'Staff_Female']);
$dawah_type = $dawah_type ?? 'male';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — <?= $dawah_type === 'female' ? "Female" : "Male" ?> Marriage Management</title>
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
      width: 32px; height: 32px; border-radius: 50%; border: none;
      display: inline-flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all 0.2s ease;
    }
    .btn-circle svg { width: 15px; height: 15px; }
    .btn-circle.eye { background: #e0f2fe; color: #0284c7; }
    .btn-circle.eye:hover { background: #0284c7; color: #fff; }
    .btn-circle.check { background: #dcfce7; color: #16a34a; }
    .btn-circle.check:hover { background: #16a34a; color: #fff; }
    .btn-circle.reject { background: #fee2e2; color: #dc2626; }
    .btn-circle.reject:hover { background: #dc2626; color: #fff; }

    /* ── Modal ── */
    .marriage-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .marriage-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 580px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); animation: slideUp 0.3s ease;
    }
    .marriage-modal-header {
      padding: 20px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center; background: #fdfdfd;
    }
    .marriage-modal-close {
      background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280; line-height: 1;
    }
    .marriage-modal-close:hover { color: var(--primary); }
    .marriage-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .marriage-modal-footer {
      padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: flex-end; gap: 10px;
    }
    .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .info-box { background: #f9fafb; padding: 14px; border-radius: 12px; border: 1px solid var(--border); }
    .info-box.highlight { background: #f0fdf4; border-color: #dcfce7; }
    .info-box-lbl { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; }
    .info-box-val { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
    .info-box-val.gold { color: #b8860b; font-family: 'Lora', serif; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'marriage';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Marriage Records &amp; Solemnization</div>
          <div class="top-bar-subtitle"><?= $dawah_type === 'female' ? "Female" : "Male" ?> Da'wah Department — Marriage applications, scheduling, and solemnization tracking</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
      </div>
      
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/' . ($dawah_type === 'female' ? 'female' : 'male')) ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span>Marriage</span>
          <span class="sep">›</span>
          <span class="current">Marriage Records</span>
        </div>

        <!-- Dynamic Insights Summary -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered by JS -->
        </div>

        <!-- Tab Nav -->
        <div class="tab-nav">
          <button type="button" class="tab-btn active" onclick="setFilter('pending', this)">
            Pending Review <span class="tab-count pending" id="tab-count-pending">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setFilter('approved', this)">
            Accepted / Approved <span class="tab-count approved" id="tab-count-approved">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setFilter('rejected', this)">
            Rejected <span class="tab-count rejected" id="tab-count-rejected">0</span>
          </button>
          <button type="button" class="tab-btn" onclick="setFilter('all', this)">
            All Records <span class="tab-count" id="tab-count-all">0</span>
          </button>
        </div>

        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);margin-right:8px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              <span id="table-card-title">Marriage Reservations</span>
            </h6>
            <div id="results-count-badge" style="font-size:0.75rem; font-weight:700; color:var(--text-muted);">
              Showing 0 records
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Groom</th>
                    <th>Bride</th>
                    <th>Ceremony Date &amp; Time</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="marriage-tbody">
                  <!-- Rendered by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 💍 Marriage Details Modal -->
  <div id="marriage-modal" class="marriage-modal-backdrop" onclick="if(event.target===this) closeMarriageModal()">
    <div class="marriage-modal-card">
      <div class="marriage-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--accent);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          Marriage Reservation Details
        </h5>
        <button type="button" onclick="closeMarriageModal()" class="marriage-modal-close">&times;</button>
      </div>
      <div class="marriage-modal-body">
        <div class="info-grid-2">
          <div class="info-box highlight">
            <div class="info-box-lbl">Groom Name</div>
            <div id="modal-groom" class="info-box-val">—</div>
          </div>
          <div class="info-box highlight">
            <div class="info-box-lbl">Bride Name</div>
            <div id="modal-bride" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Ceremony Date</div>
            <div id="modal-date" class="info-box-val gold">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Ceremony Time</div>
            <div id="modal-time" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-box" style="margin-bottom:16px;">
          <div class="info-box-lbl">Venue</div>
          <div id="modal-venue" class="info-box-val">—</div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Application Ref #</div>
            <div id="modal-ref" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Current Status</div>
            <div id="modal-status" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-box">
          <div class="info-box-lbl">Applicant Contact Info</div>
          <div id="modal-contact" class="info-box-val" style="font-size:0.85rem; color:#4b5563;">—</div>
        </div>
      </div>
      <div class="marriage-modal-footer">
        <button type="button" onclick="closeMarriageModal()" class="btn-topbar">Close</button>
        <button type="button" id="modal-btn-reject" class="btn-topbar danger" style="display:none;">Reject</button>
        <button type="button" id="modal-btn-approve" class="btn-topbar primary" style="display:none;">Accept Reservation</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const applications = <?= json_encode($applications ?? ($records ?? [])) ?>;
    let currentFilter = 'pending';

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = applications.length;
      const pending = applications.filter(a => ['pending', 'under_review'].includes((a.status || '').toLowerCase())).length;
      const approved = applications.filter(a => ['accepted', 'approved', 'confirmed'].includes((a.status || '').toLowerCase())).length;
      const rejected = applications.filter(a => ['rejected', 'disapproved', 'cancelled'].includes((a.status || '').toLowerCase())).length;

      container.innerHTML = `
        <div class="insight-card pending" onclick="setFilter('pending', document.querySelector('.tab-btn:nth-child(1)'))">
          <div class="insight-label">Pending Review</div>
          <div class="insight-value warning">${pending}</div>
          <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" /></svg>
        </div>
        <div class="insight-card approved" onclick="setFilter('approved', document.querySelector('.tab-btn:nth-child(2)'))">
          <div class="insight-label">Accepted / Approved</div>
          <div class="insight-value success">${approved}</div>
          <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" /></svg>
        </div>
        <div class="insight-card rejected" onclick="setFilter('rejected', document.querySelector('.tab-btn:nth-child(3)'))">
          <div class="insight-label">Rejected</div>
          <div class="insight-value danger">${rejected}</div>
          <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" /></svg>
        </div>
        <div class="insight-card total" onclick="setFilter('all', document.querySelector('.tab-btn:nth-child(4)'))">
          <div class="insight-label">Total Reservations</div>
          <div class="insight-value info">${total}</div>
          <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        </div>
      `;

      // Update tab counts
      document.getElementById('tab-count-pending').innerText = pending;
      document.getElementById('tab-count-approved').innerText = approved;
      document.getElementById('tab-count-rejected').innerText = rejected;
      document.getElementById('tab-count-all').innerText = total;
    }

    function setFilter(val, btn) {
      currentFilter = val;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderTable();
    }

    function renderTable() {
      const tbody = document.getElementById('marriage-tbody');
      
      let filtered = applications;
      if (currentFilter === 'pending') {
        filtered = filtered.filter(a => ['pending', 'under_review'].includes((a.status || '').toLowerCase()));
      } else if (currentFilter === 'approved') {
        filtered = filtered.filter(a => ['accepted', 'approved', 'confirmed'].includes((a.status || '').toLowerCase()));
      } else if (currentFilter === 'rejected') {
        filtered = filtered.filter(a => ['rejected', 'disapproved', 'cancelled'].includes((a.status || '').toLowerCase()));
      }

      document.getElementById('results-count-badge').innerText = `Showing ${filtered.length} record${filtered.length !== 1 ? 's' : ''}`;

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No marriage records found for this filter.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(app => {
        const st = (app.status || 'pending').toLowerCase();
        let sc = 'pending';
        let displayStatus = 'Pending';
        if (['accepted', 'approved', 'confirmed'].includes(st)) {
          sc = 'approved';
          displayStatus = 'Accepted';
        } else if (['rejected', 'disapproved', 'cancelled'].includes(st)) {
          sc = 'rejected';
          displayStatus = 'Rejected';
        }

        const schedDate = app.marriage_date ? new Date(app.marriage_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
        const schedTime = app.marriage_time || '';
        const venue = app.marriage_venue || 'ISCAG Mosque';
        const isPending = ['pending', 'under_review'].includes(st);

        return `
          <tr>
            <td class="td-id">#MR-${String(app.id).padStart(4, '0')}</td>
            <td style="font-weight:700; color:var(--primary);">${app.groom_name || '—'}</td>
            <td style="font-weight:700; color:var(--primary);">${app.bride_name || '—'}</td>
            <td>${schedDate} ${schedTime ? 'at ' + schedTime : ''}</td>
            <td>${venue}</td>
            <td><span class="badge-status ${sc}"><span class="status-dot"></span>${displayStatus}</span></td>
            <td>
              <div class="actions-cell">
                <button class="btn-circle eye" onclick='viewMarriageDetails(${JSON.stringify(app)})' title="View Details">
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </button>
                ${isPending ? `
                  <button class="btn-circle check" onclick="handleMarriageAction(${app.id}, 'approve')" title="Accept">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  </button>
                  <button class="btn-circle reject" onclick="handleMarriageAction(${app.id}, 'reject')" title="Reject">
                    <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  </button>
                ` : ''}
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function viewMarriageDetails(app) {
      document.getElementById('modal-groom').innerText = app.groom_name || '—';
      document.getElementById('modal-bride').innerText = app.bride_name || '—';
      document.getElementById('modal-date').innerText = app.marriage_date ? new Date(app.marriage_date).toLocaleDateString('en-US', { weekday:'long', month: 'long', day: 'numeric', year: 'numeric' }) : '—';
      document.getElementById('modal-time').innerText = app.marriage_time || '—';
      document.getElementById('modal-venue').innerText = app.marriage_venue || 'ISCAG Mosque';
      document.getElementById('modal-ref').innerText = `#MR-${String(app.id).padStart(4, '0')}`;
      
      const st = (app.status || 'pending').toLowerCase();
      let statusLabel = 'PENDING';
      if (['accepted', 'approved', 'confirmed'].includes(st)) statusLabel = 'ACCEPTED';
      if (['rejected', 'disapproved', 'cancelled'].includes(st)) statusLabel = 'REJECTED';
      document.getElementById('modal-status').innerText = statusLabel;
      document.getElementById('modal-contact').innerText = `${app.first_name || ''} ${app.last_name || ''} (${app.email || 'No email registered'})`;

      const btnApprove = document.getElementById('modal-btn-approve');
      const btnReject = document.getElementById('modal-btn-reject');
      const isPending = ['pending', 'under_review'].includes(st);

      if (isPending) {
        btnApprove.style.display = 'inline-flex';
        btnApprove.onclick = () => { closeMarriageModal(); handleMarriageAction(app.id, 'approve'); };
        btnReject.style.display = 'inline-flex';
        btnReject.onclick = () => { closeMarriageModal(); handleMarriageAction(app.id, 'reject'); };
      } else {
        btnApprove.style.display = 'none';
        btnReject.style.display = 'none';
      }

      document.getElementById('marriage-modal').style.display = 'flex';
    }

    function closeMarriageModal() {
      document.getElementById('marriage-modal').style.display = 'none';
    }

    async function handleMarriageAction(id, action) {
      const title = action === 'approve' ? 'Accept Marriage Reservation' : 'Reject Marriage Reservation';
      const message = `Are you sure you want to ${action === 'approve' ? 'accept' : 'reject'} this marriage ceremony reservation? The applicant will be notified immediately.`;

      const runAction = async () => {
        try {
          const endpoint = action === 'approve' ? '<?= url("/admin/dawah/marriage/accept") ?>' : '<?= url("/admin/dawah/marriage/reject") ?>';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const result = await response.json();
          if (result.success) {
            if (typeof showAlert === 'function') {
              showAlert('Action Successful', result.message || `The reservation has been processed.`, 'success');
            } else {
              alert(result.message || 'Operation successful.');
            }
            setTimeout(() => location.reload(), 1000);
          } else {
            if (typeof showAlert === 'function') {
              showAlert('Action Failed', result.message || 'Could not process request.', 'error');
            } else {
              alert(result.message || 'Could not process request.');
            }
          }
        } catch(e) {
          console.error(e);
          alert('A network error occurred.');
        }
      };

      if (typeof showConfirm === 'function') {
        showConfirm(title, message, runAction, action === 'approve' ? 'success' : 'danger');
      } else {
        if (confirm(`${title}\n\n${message}`)) {
          runAction();
        }
      }
    }

    renderInsights();
    renderTable();
  </script>
</body>
</html>
