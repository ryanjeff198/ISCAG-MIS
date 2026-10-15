<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Burial Records</title>
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
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
      min-width: 280px;
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

    .filter-pills {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
    }
    .filter-pill {
      font-size: 0.78rem;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
      text-decoration: none;
      color: var(--text-muted);
      background: white;
      border: 1px solid var(--border);
      transition: all 0.2s;
      white-space: nowrap;
      cursor: pointer;
    }
    .filter-pill:hover, .filter-pill.active {
      background: var(--damayan-light);
      color: var(--damayan-accent);
      border-color: var(--damayan-accent);
    }

    .mini-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 6px;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .mini-badge.pending { background: #f3f4f6; color: #6b7280; }
    .mini-badge.active { background: #eff6ff; color: #1d4ed8; }
    .mini-badge.done { background: #ecfdf5; color: #047857; }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <?php 
      $active_page = 'burial';
      include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; 
    ?>
    <div class="main-content">
      <div class="top-bar" style="display:flex; align-items:center; justify-content:space-between;">
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 48px; height: 48px; background: var(--damayan-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--damayan-accent);">
            <svg viewBox="0 0 24 24" style="width:28px;height:28px;fill:currentColor;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
          </div>
          <div>
            <div class="top-bar-title">Burial Service Registry</div>
            <div class="top-bar-subtitle">Complete archive of historical and active Islamic funeral service cases</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/damayan/burial-requests') ?>" class="btn-topbar primary" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;font-weight:700;">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Active Requests
          </a>
        </div>
      </div>
      <div class="page-body">
        <div class="breadcrumb-bar">
          <a href="<?= url('/admin/damayan') ?>">Dashboard</a><span class="sep">›</span><span class="current">Burial Records</span>
        </div>

        <div class="admin-insights">
          <div class="insight-card">
            <div class="insight-label">Total Registry Records</div>
            <div class="insight-value"><?= $analytics['total'] ?? 0 ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#f59e0b;">
            <div class="insight-label">Pending Review</div>
            <div class="insight-value warning"><?= $analytics['pending'] ?? 0 ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#3b82f6;">
            <div class="insight-label">Active In Coordination</div>
            <div class="insight-value info"><?= $analytics['coordination'] ?? 0 ?></div>
          </div>
          <div class="insight-card" style="border-left-color:#10b981;">
            <div class="insight-label">Burials Completed</div>
            <div class="insight-value success"><?= $analytics['completed'] ?? 0 ?></div>
          </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="filter-bar">
          <div class="filter-pills">
            <button type="button" class="filter-pill <?= ($currentFilter ?? 'all') === 'all' ? 'active' : '' ?>" onclick="setFilter('all')">All Records</button>
            <button type="button" class="filter-pill <?= ($currentFilter ?? '') === 'pending' ? 'active' : '' ?>" onclick="setFilter('pending')">Pending</button>
            <button type="button" class="filter-pill <?= ($currentFilter ?? '') === 'coordination' ? 'active' : '' ?>" onclick="setFilter('coordination')">In Coordination</button>
            <button type="button" class="filter-pill <?= ($currentFilter ?? '') === 'completed' ? 'active' : '' ?>" onclick="setFilter('completed')">Completed Archive</button>
            <button type="button" class="filter-pill <?= ($currentFilter ?? '') === 'rejected' ? 'active' : '' ?>" onclick="setFilter('rejected')">Declined</button>
          </div>
          <div class="search-box">
            <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="table-search-input" placeholder="Search registry by name, deceased, or ref #..." value="<?= htmlspecialchars($searchQuery ?? '') ?>" onkeyup="onSearchInput(event)" />
          </div>
        </div>

        <div class="section-card">
          <div class="section-card-header">
            <h6 style="color: var(--damayan-dark);">
              <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:var(--damayan-accent);margin-right:8px;"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
              Burial Records Archive
            </h6>
          </div>
          <div class="section-card-body" style="padding:0;">
            <div class="table-wrapper">
              <table class="mis-table">
                <thead>
                  <tr>
                    <th>Ref #</th>
                    <th>Applicant</th>
                    <th>Deceased</th>
                    <th>Date Requested</th>
                    <th>Services Progress</th>
                    <th>Status</th>
                    <th style="text-align:center;">Action</th>
                  </tr>
                </thead>
                <tbody id="burial-tbody">
                  <?php if(empty($records)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No burial records found.</td></tr>
                  <?php else: ?>
                    <?php foreach($records as $r): ?>
                      <tr>
                        <td class="td-id">#<?= htmlspecialchars($r['id']) ?></td>
                        <td>
                          <div style="font-weight:700; color:var(--text-main);"><?= htmlspecialchars($r['name']) ?></div>
                          <div style="font-size:0.75rem; color:var(--text-muted);">Applicant</div>
                        </td>
                        <td>
                          <div style="font-weight:700;"><?= htmlspecialchars($r['deceased']) ?></div>
                          <span style="font-size:0.7rem; font-weight:700; padding:1px 6px; border-radius:8px; <?= ($r['deceased_sex'] ?? '') === 'Female' ? 'background:#fce7f3;color:#9d174d;' : 'background:#e0e7ff;color:#3730a3;' ?>">
                            <?= htmlspecialchars($r['deceased_sex'] ?? 'Male') ?>
                          </span>
                        </td>
                        <td style="font-size:0.82rem;"><?= htmlspecialchars($r['date']) ?></td>
                        <td>
                          <div style="display:flex; gap:4px; flex-wrap:wrap;">
                            <span class="mini-badge <?= strtolower($r['ghusl_status'] ?? '') === 'completed' ? 'done' : (strtolower($r['ghusl_status'] ?? '') === 'scheduled' ? 'active' : 'pending') ?>" title="Ghusl: <?= htmlspecialchars($r['ghusl_status'] ?? 'Pending') ?>">Ghusl</span>
                            <span class="mini-badge <?= strtolower($r['janazah_status'] ?? '') === 'completed' ? 'done' : (strtolower($r['janazah_status'] ?? '') === 'scheduled' ? 'active' : 'pending') ?>" title="Janazah: <?= htmlspecialchars($r['janazah_status'] ?? 'Pending') ?>">Janazah</span>
                            <span class="mini-badge <?= strtolower($r['transport_status'] ?? '') === 'completed' ? 'done' : (strtolower($r['transport_status'] ?? '') === 'scheduled' ? 'active' : 'pending') ?>" title="Transport: <?= htmlspecialchars($r['transport_status'] ?? 'Pending') ?>">Transport</span>
                            <span class="mini-badge <?= strtolower($r['cemetery_status'] ?? '') === 'completed' ? 'done' : (strtolower($r['cemetery_status'] ?? '') === 'scheduled' ? 'active' : 'pending') ?>" title="Cemetery: <?= htmlspecialchars($r['cemetery_status'] ?? 'Pending') ?>">Cemetery</span>
                          </div>
                        </td>
                        <td><span class="badge-status <?= htmlspecialchars($r['status_class']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                        <td style="text-align:center;">
                          <button type="button" class="btn-topbar" style="padding:5px 12px; font-size:0.78rem; color:var(--damayan-accent); border-color:var(--damayan-accent);" onclick="openCaseModal('<?= htmlspecialchars($r['id']) ?>')">Manage</button>
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

  <?php include __DIR__ . '/burial_modal.php'; ?>

  <script src="<?= asset('JS/admin-shared.js') ?>"></script>
  <script>
    standardizePage('staff');

    function setFilter(filter) {
      const url = new URL(window.location.href);
      url.searchParams.set('filter', filter);
      window.location.href = url.toString();
    }

    function onSearchInput(e) {
      if(e.key === 'Enter') {
        const url = new URL(window.location.href);
        url.searchParams.set('search', e.target.value.trim());
        window.location.href = url.toString();
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const params = new URLSearchParams(window.location.search);
      const caseId = params.get('id');
      if(caseId) {
        openCaseModal(caseId);
      }
    });
  </script>
</body>
</html>
