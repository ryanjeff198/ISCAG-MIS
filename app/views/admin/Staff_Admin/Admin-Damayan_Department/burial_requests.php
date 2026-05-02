<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Requests</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'burial_requests';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Active Burial Requests</div>
            <div class="top-bar-subtitle">Reviewing new applications and updating service status</div>
          </div>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Burial Requests</span>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
              Pending & Active Applications
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Deceased Name</th>
                    <th>Date Requested</th>
                    <th>Status</th>
                    <th>Notify Family</th>
                  </tr>
                </thead>
                <tbody id="burial-tbody">
                  <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No active requests found.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');
    
    const records = <?= json_encode($records ?? []) ?>;

    function renderTable() {
      const tbody = document.getElementById('burial-tbody');
      // Filter only active/pending ones for this view
      const activeRecords = records.filter(r => r.status.toLowerCase() !== 'completed' && r.status.toLowerCase() !== 'rejected');
      
      if(activeRecords.length === 0) {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No active requests to notify.</td></tr>';
          return;
      }
      
      tbody.innerHTML = activeRecords.map(r => `
        <tr>
          <td class="td-id">#${r.id}</td>
          <td style="font-weight:600;">${r.name}</td>
          <td>${r.deceased}</td>
          <td>${r.date}</td>
          <td><span class="badge-status ${r.status_class}">${r.status}</span></td>
          <td>
            <button class="btn-action" style="color:var(--damayan-accent);" onclick="manageBurial('${r.id}', '${r.status}')">Notify Family</button>
          </td>
        </tr>
      `).join('');
    }

    let activeBurialId = null;
    function manageBurial(id, currentStatus) {
      activeBurialId = id;
      showConfirm(
        'Update & Notify Family', 
        `Update the status for request #${id}? This will send an automated notification to the family's portal.`,
        'Deceased Arrived',
        () => updateStatus('arrived'),
        'Complete Service',
        () => updateStatus('completed')
      );
    }

    async function updateStatus(newStatus) {
      try {
        const res = await fetch('<?= url('/admin/damayan/burial/update') ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: activeBurialId, status: newStatus })
        });
        const data = await res.json();
        if(data.success) {
          showAlert('Success', 'Status updated and family notified.', 'success');
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('Error', 'Failed to update status.', 'error');
        }
      } catch (e) {
        console.error(e);
        showAlert('Error', 'An unexpected error occurred.', 'error');
      }
    }
    
    renderTable();
  </script>
</body>
</html>
