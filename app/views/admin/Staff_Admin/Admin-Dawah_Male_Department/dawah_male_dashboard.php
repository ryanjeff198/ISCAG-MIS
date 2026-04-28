<?php $active_page = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Da'wah Male Staff Admin</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Staff Admin dashboard for Da'wah Male department management" />
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
    <?php include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Dawah_Male_Department/sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Da'wah (Male) Dashboard</div>
            <div class="top-bar-subtitle">Welcome back, <?= htmlspecialchars(explode(' ',trim($dbUser['first_name']??'Staff'))[0]) ?>. Managing male conversions & counseling.</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/mis_admin/daawah_records') ?>" class="btn-topbar">📋 View Records</a>
        </div>
      </div>

      <div class="page-body">
        <div class="db">
          
          <!-- KPI OVERVIEW -->
          <div class="db-section">
            <div class="kpi-row">
              <?php
                $kpis = [
                  ['Total Conversions', $totalConversions, 'Success stories', 'var(--primary)', 'rgba(23,107,69,.08)', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5 16.5 9.51 16.5 12 14.49 16.5 12 16.5z'],
                  ['Counseling Sessions', 0, 'Active sessions', 'var(--info)', 'rgba(31,111,90,.08)', 'M21 6h-2v11H6V5c0-1.1.9-2 2-2h3V1h2v2h3c1.1 0 2 .9 2 2v1h3v2zm-10 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z'],
                  ['Class Attendees', 0, 'Weekly participation', 'var(--success)', 'rgba(47,138,96,.08)', 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z'],
                  ['Pending Inquiries', 0, 'Requires attention', 'var(--warning)', 'rgba(199,154,43,.08)', 'M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z']
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
                <div><div class="card-title">Recent Conversions</div><div class="card-sub">Latest male conversion records</div></div>
              </div>
              <div class="empty-state" style="text-align:center;padding:60px 20px;color:var(--text-muted);">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;fill:var(--border);margin-bottom:16px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5 16.5 9.51 16.5 12 14.49 16.5 12 16.5z"/></svg>
                <h4 style="font-family:'Lora',serif;font-size:1.1rem;margin-bottom:8px;">No Recent Conversion Data</h4>
                <p style="font-size:.9rem;">Once new conversions are recorded, they will appear here.</p>
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
