<?php $active_page = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Damayan Staff Admin</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Staff Admin dashboard for Damayan department management" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* Dashboard Specific Styles */
    .db { max-width: 2400px; margin: 0 auto; width: 99%; padding-bottom: 40px; }
    .db-section { margin-bottom: 24px; }
    .db-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .db-section-title { font-size: 0.95rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.08em; }
    .db-section-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

    .kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .kpi { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px 24px; transition: all 0.2s ease; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); text-decoration: none; color: inherit; display: block; }
    .kpi:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); border-color: var(--primary-light); }
    .kpi-accent { position: absolute; top: 0; left: 0; width: 5px; height: 100%; border-radius: 12px 0 0 12px; }
    .kpi-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .kpi-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
    .kpi-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kpi-icon svg { width: 22px; height: 22px; fill: currentColor; }
    .kpi-val { font-family: 'Lora', serif; font-size: 1.8rem; font-weight: 700; color: var(--text-main); line-height: 1.2; margin-bottom: 8px; }
    .kpi-footer { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 600; }
    .kpi-change { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    .kpi-change.up { background: rgba(47, 138, 96, 0.1); color: var(--success); }
    .kpi-period { color: var(--text-muted); font-size: 0.75rem; }

    .card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 24px; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.01); }
    .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .card-title { font-size: 1.05rem; font-weight: 700; color: var(--text-main); }
    .card-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Damayan_Department/sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Damayan Dashboard</div>
            <div class="top-bar-subtitle">Welcome back, <?= htmlspecialchars(explode(' ',trim($dbUser['first_name']??'Staff'))[0]) ?>. Managing bereavement support.</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin/damayan_records') ?>" class="btn-topbar">📋 View Records</a>
        </div>
      </div>

      <div class="page-body">
        <div class="db">
          
          <!-- KPI OVERVIEW -->
          <div class="db-section">
            <div class="kpi-row">
              <?php
                $kpis = [
                  ['Total Burial Requests', $totalBurials, 'All-time requests', 'var(--primary)', 'rgba(23,107,69,.08)', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z'],
                  ['Pending Requests', $pendingBurials, 'Awaiting verification', 'var(--warning)', 'rgba(199,154,43,.08)', 'M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z'],
                  ['Completed Services', ($totalBurials - $pendingBurials), 'Successfully closed', 'var(--success)', 'rgba(47,138,96,.08)', 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'],
                  ['Total Fund', '₱0.00', 'Available assistance', 'var(--info)', 'rgba(31,111,90,.08)', 'M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z']
                ];
                foreach($kpis as $k):
              ?>
              <div class="kpi">
                <div class="kpi-accent" style="background:<?=$k[3]?>"></div>
                <div class="kpi-top">
                  <div class="kpi-label"><?=$k[0]?></div>
                  <div class="kpi-icon" style="background:<?=$k[4]?>;color:<?=$k[3]?>"><svg viewBox="0 0 24 24"><path d="<?=$k[5]?>"/></svg></div>
                </div>
                <div class="kpi-val"><?=$k[1]?></div>
                <div class="kpi-footer">
                  <span class="kpi-change up"><span class="trend-icon">↑</span> <?=$k[2]?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- RECENT ACTIVITY -->
          <div class="db-section">
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Recent Activity</div><div class="card-sub">Latest department updates</div></div>
              </div>
              <div class="empty-state" style="text-align:center;padding:60px 20px;color:var(--text-muted);">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:16px;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-1 7V3.5L18.5 9H13z"/></svg>
                <h4 style="font-family:'Lora',serif;font-size:1.1rem;margin-bottom:8px;">No Recent Burial Requests</h4>
                <p style="font-size:.9rem;">When users submit burial service requests, they will appear here.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('staff');
    initNotifBadge('staff');
  </script>
</body>
</html>
