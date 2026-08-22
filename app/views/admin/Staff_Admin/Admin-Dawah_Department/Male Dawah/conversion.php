<?php
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protectRole(['Admin', 'Staff_Male']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Conversion & Shahadah Management</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    
    .admin-insights { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .insight-card { background: white; padding: 16px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s; display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden; cursor: pointer; }
    .insight-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); border-color: var(--accent); }
    .insight-card::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--border); }
    .insight-card.all::after { background: #1E90FF; }
    .insight-card.pending::after { background: var(--warning); }
    .insight-card.approved::after { background: var(--success); }
    
    .insight-label { font-size: 0.65rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1; }

    /* Modal Styles */
    .conversion-modal-backdrop {
      position: fixed; inset: 0; z-index: 99999;
      background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
      display: none; align-items: center; justify-content: center; padding: 20px;
      animation: fadeIn 0.3s ease;
    }
    .conversion-modal-card {
      background: #fff; border-radius: 20px; width: 100%; max-width: 650px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
      border: 1px solid rgba(0,0,0,0.08); animation: slideUp 0.35s ease;
    }
    .conversion-modal-header {
      padding: 20px 24px; border-bottom: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center;
      background: #fdfdfd;
    }
    .conversion-modal-close {
      background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280;
      padding: 0; line-height: 1; transition: color 0.2s;
    }
    .conversion-modal-close:hover { color: var(--primary); }
    .conversion-modal-body { padding: 24px; max-height: 75vh; overflow-y: auto; }
    .conversion-modal-footer {
      padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
      display: flex; justify-content: flex-end; gap: 10px;
    }
    .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .info-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .info-box { background: #f9fafb; padding: 14px; border-radius: 12px; border: 1px solid var(--border); }
    .info-box.highlight { background: #eff6ff; border-color: #dbeafe; }
    .info-box-lbl { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-bottom: 4px; }
    .info-box-val { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
    .info-box-val.gold { color: #1E90FF; font-family: 'Lora', serif; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'conversion';
      $dawah_type = 'male';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div class="top-bar-title">Conversion &amp; Shahadah Records</div>
          <div class="top-bar-subtitle">Male Da'wah Department — Conversion to Islam applications and certificate issuance</div>
        </div>
        <div class="top-bar-actions">
           <span id="admin-name" style="font-weight:700;color:var(--text-main);font-size:0.9rem;"></span>
           <button class="btn-topbar primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/dawah/male') ?>">Da'wah Department</a>
          <span class="sep">›</span>
          <span class="current">Conversion Records</span>
        </div>

        <!-- Dynamic Insights Summary -->
        <div class="admin-insights" id="insights-container">
          <!-- Rendered by JS -->
        </div>

        <div class="section-card">
          <div class="section-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h6>
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#1E90FF;margin-right:8px;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
              Brothers' Conversion to Islam Applications
            </h6>
            <div style="display:flex; gap:10px;">
              <select id="status-filter" class="form-control" style="font-size:0.8rem; padding:4px 12px; border-radius:8px; width:auto; appearance:auto;" onchange="renderTable()">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Legal Name</th>
                    <th>Adopted Islamic Name</th>
                    <th>Former Religion</th>
                    <th>Conversion Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="conversion-tbody">
                  <!-- Rendered by JS -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 📜 Conversion Details Modal -->
  <div id="conversion-modal" class="conversion-modal-backdrop" onclick="if(event.target===this) closeConversionModal()">
    <div class="conversion-modal-card">
      <div class="conversion-modal-header">
        <h5 style="margin:0; font-family:'Lora',serif; color:var(--primary-dark); font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#1E90FF;"><path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19H4.5L12 5.8zM11 10v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
          Conversion Application Details
        </h5>
        <button type="button" onclick="closeConversionModal()" class="conversion-modal-close">&times;</button>
      </div>
      <div class="conversion-modal-body">
        <div class="info-grid-2">
          <div class="info-box highlight">
            <div class="info-box-lbl">Legal Name</div>
            <div id="modal-legal-name" class="info-box-val">—</div>
          </div>
          <div class="info-box highlight">
            <div class="info-box-lbl">Adopted Islamic Name</div>
            <div id="modal-adopted-name" class="info-box-val gold">—</div>
          </div>
        </div>

        <div class="info-grid-3">
          <div class="info-box">
            <div class="info-box-lbl">Sex / Age</div>
            <div id="modal-sex-age" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Civil Status</div>
            <div id="modal-civil" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Former Religion</div>
            <div id="modal-former-rel" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Place of Birth</div>
            <div id="modal-pob" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Present Residence</div>
            <div id="modal-residence" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Father's Name &amp; Religion</div>
            <div id="modal-father" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Mother's Name &amp; Religion</div>
            <div id="modal-mother" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">First Witness</div>
            <div id="modal-w1" class="info-box-val">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Second Witness</div>
            <div id="modal-w2" class="info-box-val">—</div>
          </div>
        </div>

        <div class="info-grid-2">
          <div class="info-box">
            <div class="info-box-lbl">Conversion Date</div>
            <div id="modal-conv-date" class="info-box-val gold">—</div>
          </div>
          <div class="info-box">
            <div class="info-box-lbl">Status</div>
            <div id="modal-status" class="info-box-val">—</div>
          </div>
        </div>
      </div>
      <div class="conversion-modal-footer">
        <button type="button" onclick="closeConversionModal()" class="btn-cancel">Close</button>
        <button type="button" id="modal-btn-reject" class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger); display:none;">Reject</button>
        <button type="button" id="modal-btn-approve" class="btn-action btn-approve" style="display:none;">Approve &amp; Issue Certificate</button>
      </div>
    </div>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    const applications = <?= json_encode($applications ?? []) ?>;

    function renderInsights() {
      const container = document.getElementById('insights-container');
      const total = applications.length;
      const pending = applications.filter(a => (a.status || '').toLowerCase() === 'pending').length;
      const approved = applications.filter(a => (a.status || '').toLowerCase() === 'approved').length;

      container.innerHTML = `
        <div class="insight-card all" onclick="setFilter('all')">
          <div class="insight-label">Total Applications</div>
          <div class="insight-value" style="color:#1E90FF;">${total}</div>
        </div>
        <div class="insight-card pending" onclick="setFilter('pending')">
          <div class="insight-label">Pending Verification</div>
          <div class="insight-value" style="color:var(--warning);">${pending}</div>
        </div>
        <div class="insight-card approved" onclick="setFilter('approved')">
          <div class="insight-label">Certificates Issued</div>
          <div class="insight-value" style="color:var(--success);">${approved}</div>
        </div>
      `;
    }

    function setFilter(val) {
      document.getElementById('status-filter').value = val;
      renderTable();
    }

    function renderTable() {
      const tbody = document.getElementById('conversion-tbody');
      const filter = document.getElementById('status-filter').value;
      
      let filtered = applications;
      if (filter !== 'all') {
        filtered = filtered.filter(a => (a.status || '').toLowerCase() === filter);
      }

      if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No conversion records found.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtered.map(app => {
        const st = (app.status || 'pending').toLowerCase();
        const sc = st === 'approved' ? 'badge-approved' : (st === 'rejected' ? 'badge-rejected' : 'badge-pending');
        const displayStatus = st === 'rejected' ? 'Disapproved' : st.charAt(0).toUpperCase() + st.slice(1);
        const convDate = app.conversion_date ? new Date(app.conversion_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
        const legalName = `${app.fname || ''} ${app.mname ? app.mname + ' ' : ''}${app.lname || ''}`.trim() || '—';

        return `
          <tr>
            <td class="td-id">#CR-${String(app.id).padStart(4, '0')}</td>
            <td style="font-weight:700; color:var(--primary);">${legalName}</td>
            <td style="font-weight:700; color:#1E90FF;">${app.adopted_name || '—'}</td>
            <td>${app.former_religion || '—'}</td>
            <td>${convDate}</td>
            <td><span class="badge-status ${sc}">${displayStatus}</span></td>
            <td>
              <div class="actions-cell">
                <button class="btn-action btn-view" onclick='viewConversionDetails(${JSON.stringify(app)})'>
                  <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                  Details
                </button>
                ${st === 'pending' ? `
                  <button class="btn-action btn-approve" onclick="handleConversionAction(${app.id}, 'approve')">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Approve
                  </button>
                  <button class="btn-action btn-edit" style="color:var(--danger); border-color:var(--danger);" onclick="handleConversionAction(${app.id}, 'reject')">
                    Reject
                  </button>
                ` : ''}
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function viewConversionDetails(app) {
      const legalName = `${app.fname || ''} ${app.mname ? app.mname + ' ' : ''}${app.lname || ''}`.trim() || '—';
      document.getElementById('modal-legal-name').innerText = legalName;
      document.getElementById('modal-adopted-name').innerText = app.adopted_name || '—';
      document.getElementById('modal-sex-age').innerText = `${app.sex || 'Male'}, ${app.age || '—'} yrs old`;
      document.getElementById('modal-civil').innerText = app.civil_status || '—';
      document.getElementById('modal-former-rel').innerText = app.former_religion || '—';
      document.getElementById('modal-pob').innerText = app.pob || '—';
      document.getElementById('modal-residence').innerText = app.residence || '—';
      document.getElementById('modal-father').innerText = `${app.father_name || '—'} (${app.father_religion || '—'})`;
      document.getElementById('modal-mother').innerText = `${app.mother_name || '—'} (${app.mother_religion || '—'})`;
      document.getElementById('modal-w1').innerText = `${app.witness1_name || '—'} ${app.witness1_address ? '— ' + app.witness1_address : ''}`;
      document.getElementById('modal-w2').innerText = `${app.witness2_name || '—'} ${app.witness2_address ? '— ' + app.witness2_address : ''}`;
      document.getElementById('modal-conv-date').innerText = app.conversion_date ? new Date(app.conversion_date).toLocaleDateString('en-US', { weekday:'long', month: 'long', day: 'numeric', year: 'numeric' }) : '—';
      document.getElementById('modal-status').innerText = (app.status || 'Pending').toUpperCase();

      const btnApprove = document.getElementById('modal-btn-approve');
      const btnReject = document.getElementById('modal-btn-reject');
      const st = (app.status || 'pending').toLowerCase();

      if (st === 'pending') {
        btnApprove.style.display = 'inline-flex';
        btnApprove.onclick = () => { closeConversionModal(); handleConversionAction(app.id, 'approve'); };
        btnReject.style.display = 'inline-flex';
        btnReject.onclick = () => { closeConversionModal(); handleConversionAction(app.id, 'reject'); };
      } else {
        btnApprove.style.display = 'none';
        btnReject.style.display = 'none';
      }

      document.getElementById('conversion-modal').style.display = 'flex';
    }

    function closeConversionModal() {
      document.getElementById('conversion-modal').style.display = 'none';
    }

    async function handleConversionAction(id, action) {
      const title = action === 'approve' ? 'Approve Conversion & Issue Certificate' : 'Reject Conversion Application';
      const message = `Are you sure you want to ${action} this conversion application? The applicant will be notified immediately.`;

      showConfirm(title, message, async () => {
        try {
          const endpoint = action === 'approve' ? '/admin/dawah/conversion/approve' : '/admin/dawah/conversion/reject';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
          });
          const result = await response.json();
          if (result.success) {
            showAlert('Action Successful', `The application has been ${action}d successfully.`, 'success');
            setTimeout(() => location.reload(), 1200);
          } else {
            showAlert('Action Failed', 'Could not process request. Please try again.', 'error');
          }
        } catch(e) {
          console.error(e);
          showAlert('System Error', 'An unexpected error occurred.', 'error');
        }
      }, action === 'approve' ? 'success' : 'danger');
    }

    renderInsights();
    renderTable();
  </script>
</body>
</html>
