<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Registered Death Certificates</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    :root {
      --damayan-accent: #176b45;
      --damayan-dark: #0f5c3a;
      --damayan-light: #e8f5ed;
    }
    .admin-insights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .insight-card {
      background: white;
      padding: 20px 22px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    .insight-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .insight-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 4px; height: 100%;
      background: var(--damayan-accent);
    }
    .insight-label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .insight-value { font-size: 1.8rem; font-weight: 800; color: var(--damayan-dark); line-height: 1; }

    /* Filter & Search Bar */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .search-box {
      display: flex;
      align-items: center;
      background: white;
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 6px 14px;
      gap: 8px;
      min-width: 320px;
    }
    .search-box input {
      border: none;
      outline: none;
      font-size: 0.85rem;
      width: 100%;
      font-family: inherit;
    }
    .search-box svg {
      width: 16px;
      height: 16px;
      fill: var(--text-muted);
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'encoded_certificates';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Registered Death Certificates</div>
            <div class="top-bar-subtitle">Archive and directory of registered civil registry death certificates</div>
          </div>
        </div>
        <div class="top-bar-actions" style="display:flex; align-items:center; gap:8px;">
          <button type="button" class="btn-topbar primary" onclick="openEncodeModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Register Death Certificate
          </button>
          <a href="<?= url('/admin/damayan') ?>" class="btn-topbar" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;color:var(--damayan-accent);">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Dashboard
          </a>
        </div>
      </div>

      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span>
          <span class="current">Registered Death Certificates</span>
        </div>

        <!-- Metric Insights Cards -->
        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Registered Records</div>
            <div class="insight-value"><?= (int)($totalCount ?? 0) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Existing certificates on file</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Available for Copy Issuance</div>
            <div class="insight-value" style="color:var(--damayan-accent);"><?= (int)($totalCount ?? 0) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Verified civil registry entries</div>
          </div>
          <div class="insight-card">
            <div class="insight-label">Registration Status</div>
            <div class="insight-value" style="color:#0284c7; font-size:1.4rem;">Streamlined</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Fast 3-field recording active</div>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="filter-bar">
          <form method="GET" action="<?= url('/admin/damayan/encoded-certificates') ?>" style="display:flex; gap:8px; flex:1; max-width:600px;">
            <div class="search-box" style="flex:1;">
              <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
              <input type="text" name="search" placeholder="Search by Registration #, Deceased Name, or Representative..." value="<?= htmlspecialchars($search ?? '') ?>" />
            </div>
            <button type="submit" class="btn-topbar primary" style="padding:6px 16px;">Search</button>
            <?php if (!empty($search)): ?>
              <a href="<?= url('/admin/damayan/encoded-certificates') ?>" class="btn-topbar" style="padding:6px 14px; text-decoration:none;">Clear</a>
            <?php endif; ?>
          </form>

          <button type="button" class="btn-topbar primary" onclick="openEncodeModal()" style="display:inline-flex; align-items:center; gap:6px;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Register New Certificate
          </button>
        </div>

        <!-- Encoded Certificates Records Table -->
        <div class="section-card">
          <div class="section-card-header" style="display:flex; align-items:center; justify-content:space-between;">
            <h6 style="color: var(--damayan-dark); margin:0; display:flex; align-items:center; gap:8px;">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
              Recorded Civil Registry Death Certificates (<?= count($records ?? []) ?> records shown)
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Registration #</th>
                    <th>Deceased Full Name</th>
                    <th>Representative / Informant</th>
                    <th>Registered By</th>
                    <th>Registration Date &amp; Time</th>
                    <th style="text-align:center;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(empty($records)): ?>
                    <tr>
                      <td colspan="6" style="text-align:center; padding:50px 20px; color:var(--text-muted);">
                        <div style="font-size:1.05rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">No Registered Death Certificates Found</div>
                        <div style="font-size:0.85rem; margin-bottom:16px;">
                          <?= !empty($search) ? 'No records matched your search query.' : 'No existing death certificates have been registered yet.' ?>
                        </div>
                        <button type="button" class="btn-topbar primary" onclick="openEncodeModal()" style="display:inline-flex; align-items:center; gap:6px;">
                          <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Register First Death Certificate
                        </button>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach($records as $r): ?>
                      <tr>
                        <td class="td-id">
                          <span style="font-weight:800; color:var(--damayan-accent);">#<?= htmlspecialchars($r['registration_number']) ?></span>
                        </td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;"><?= htmlspecialchars($r['deceased_name']) ?></div>
                          <span class="badge-status badge-active" style="font-size:0.68rem; padding:2px 6px;">Recorded Certificate</span>
                        </td>
                        <td>
                          <div style="font-weight:600; color:var(--text-main);"><?= htmlspecialchars($r['representative_name']) ?></div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">Representative</div>
                        </td>
                        <td>
                          <div style="font-weight:600; color:var(--damayan-dark);"><?= htmlspecialchars($r['encoded_by_name'] ?: 'Staff Admin') ?></div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">Staff Coordinator</div>
                        </td>
                        <td>
                          <div style="font-size:0.83rem; font-weight:600; color:var(--text-main);">
                            <?= date('M d, Y', strtotime($r['encoded_at'])) ?>
                          </div>
                          <div style="font-size:0.72rem; color:var(--text-muted);">
                            <?= date('g:i A', strtotime($r['encoded_at'])) ?>
                          </div>
                        </td>
                        <td style="text-align:center;">
                          <button type="button" class="btn-topbar primary" style="padding:5px 14px; font-size:0.78rem;" onclick="openEncodedRecordDetails(<?= (int)$r['id'] ?>)">
                            View Details
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

      </div>
    </div>
  </div>

  <?php include __DIR__ . '/encode_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    syncSessionUser('<?= trim(($dbUser['first_name'] ?? '') . ' ' . ($dbUser['last_name'] ?? '')) ?>', '<?= $dbUser['email'] ?? '' ?>', '<?= $_SESSION['role'] ?? '' ?>');
    standardizePage('staff');

    function escapeHtml(text) {
      if(!text) return '';
      const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
      return String(text).replace(/[&<>"']/g, m => map[m]);
    }
  </script>
</body>
</html>
