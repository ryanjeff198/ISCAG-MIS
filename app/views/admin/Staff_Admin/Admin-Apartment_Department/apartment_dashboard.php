<?php $active_page = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ISCAG MIS — Apartment Staff Admin</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <meta name="description" content="Staff Admin dashboard for Apartment department management" />
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>" />
  <style>
    /* Dashboard Specific Styles (from Admin Dashboard) */
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

    .btn-action.btn-assign:disabled { opacity: 0.4; cursor: not-allowed; }
    .verified-glow { border-left: 4px solid var(--success); }
    .empty-state { text-align: center; padding: 30px 20px; color: var(--text-muted); }
    .empty-state svg { width: 40px; height: 40px; fill: var(--border); margin-bottom: 8px; }
    .empty-state h4 { font-family: 'Lora', serif; font-size: 0.92rem; font-weight: 700; margin: 0 0 4px; }
    .empty-state p { font-size: 0.8rem; margin: 0; }

    /* Hover Overrides */
    .btn-view { border-color: var(--accent) !important; color: var(--accent) !important; }
    .btn-view:hover { background: var(--accent) !important; color: white !important; }
    .btn-view:hover svg { fill: white !important; }
    .btn-manage { border-color: var(--accent) !important; color: var(--accent) !important; }
    .btn-manage:hover { background: var(--accent) !important; color: white !important; }
    .btn-manage:hover svg { fill: white !important; }
    .btn-approve { border-color: var(--accent) !important; color: var(--accent) !important; }
    .btn-approve:hover { background: var(--accent) !important; color: white !important; }
    .btn-approve:hover svg { fill: white !important; }
  </style>
</head>

<body>
  <div class="app-wrapper">
    <?php include BASE_PATH . '/app/views/admin/Staff_Admin/Admin-Apartment_Department/sidebar.php'; ?>

    <main class="main-content">
      <div class="top-bar">
        <div class="top-bar-left">
          <div>
            <div class="top-bar-title">Apartment Management</div>
            <div class="top-bar-subtitle">Welcome back, <?= htmlspecialchars(explode(' ',trim($dbUser['first_name']??'Staff'))[0]) ?>. Here's your department overview.</div>
          </div>
        </div>
        <div class="top-bar-actions">
          <a href="<?= url('/admin/apartment/info') ?>" class="btn-topbar">🏘️ Manage Units</a>
        </div>
      </div>

      <div class="page-body">
        <div class="db">
          
          <!-- KPI OVERVIEW -->
          <div class="db-section">
            <div class="kpi-row">
              <?php
                $totalUnits = count($units);
                $availableSlots = 0;
                $fullyOccupied = 0;
                $reserved = 0;
                foreach ($units as $u) {
                    $s = strtolower($u['status']);
                    if ($s === 'available') $availableSlots++;
                    elseif ($s === 'occupied') $fullyOccupied++;
                    elseif ($s === 'reserved') $reserved++;
                }
                
                $kpis = [
                  ['Total Units', $totalUnits, 'Managed apartments', 'var(--primary)', 'rgba(23,107,69,.08)', 'M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z'],
                  ['Available Slots', $availableSlots, 'Ready for move-in', 'var(--success)', 'rgba(47,138,96,.08)', 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'],
                  ['Occupied Units', $fullyOccupied, 'Currently rented', 'var(--danger)', 'rgba(139,46,46,.08)', 'M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z'],
                  ['Reserved Units', $reserved, 'Awaiting confirmation', 'var(--warning)', 'rgba(199,154,43,.08)', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z']
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

          <!-- UNIT TABLE -->
          <div class="db-section">
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">All Apartment Units</div><div class="card-sub">Current status of all managed rooms</div></div>
              </div>
              <div class="table-wrapper">
                <table class="mis-table" id="units-table">
                  <thead>
                    <tr>
                      <th>Unit ID</th>
                      <th>Unit Name</th>
                      <th>Building</th>
                      <th>Type</th>
                      <th>Price / mo</th>
                      <th>Available</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="units-tbody">
                    <?php foreach($units as $u): 
                      $statusClass = strtolower($u['status']) === 'available' ? 'badge-available' : (strtolower($u['status']) === 'occupied' ? 'badge-occupied' : 'badge-reserved');
                    ?>
                    <tr>
                      <td class="td-id">#<?=$u['unit_id']?></td>
                      <td style="font-weight:600;">Room <?=$u['room_number']?></td>
                      <td><?=$u['building'] ?: '—'?></td>
                      <td><?=$u['type_label']?></td>
                      <td>₱<?=number_format($u['price'])?></td>
                      <td style="text-align:center;font-weight:700;color:<?=strtolower($u['status']) === 'available' ? 'var(--success)' : 'var(--danger)'?>;"><?=strtolower($u['status']) === 'available' ? '1' : '0'?></td>
                      <td><span class="badge-status <?=$statusClass?>"><?=$u['status']?></span></td>
                      <td>
                        <div class="actions-cell">
                          <button class="btn-action btn-view" onclick="adminPreview('<?=$u['type_key']?>', '<?=$u['status']?>')">
                            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            View
                          </button>
                          <button class="btn-action btn-manage" onclick="location.href='<?= url("/admin/apartment/info") ?>'">
                            <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                            Edit
                          </button>
                        </div>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="grid-2">
            <!-- VERIFIED APPLICATIONS -->
            <div class="card verified-glow">
              <div class="card-head">
                <div><div class="card-title">Verified Applications</div><div class="card-sub">Ready for room assignment</div></div>
                <?php $verified = array_filter($applications, fn($a) => in_array(strtoupper($a['status']), ['APPROVED', 'VERIFIED'])); ?>
                <span class="card-badge"><?=count($verified)?> verified</span>
              </div>
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(empty($verified)): ?>
                    <tr><td colspan="4"><div class="empty-state"><h4>No Verified Applications</h4><p>Applications verified by MIS Admin will appear here.</p></div></td></tr>
                    <?php else: foreach($verified as $a): ?>
                    <tr>
                      <td class="td-id">#<?=$a['id']?></td>
                      <td style="font-weight:600;"><?=($a['first_name']??'').' '.($a['last_name']??'')?></td>
                      <td><?=date('M d, Y', strtotime($a['submitted_at']??'now'))?></td>
                      <td>
                        <button class="btn-action btn-approve" onclick="location.href='<?= url("/admin/apartment/info") ?>'">
                          <svg viewBox="0 0 24 24"><path d="M17 11V3H7v4H3v14h8v-4h2v4h8V11h-4z"/></svg>
                          Manage
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- RECENT APPLICATIONS -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Recent Applications</div><div class="card-sub">Latest department requests</div></div>
              </div>
              <div class="table-wrapper">
                <table class="mis-table">
                  <thead>
                    <tr>
                      <th>Ref #</th>
                      <th>Applicant</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach(array_slice($applications, 0, 5) as $a): 
                      $bc = in_array(strtolower($a['status']), ['approved', 'verified']) ? 'badge-available' : (strtolower($a['status']) === 'pending' ? 'badge-reserved' : 'badge-occupied');
                    ?>
                    <tr>
                      <td class="td-id">#<?=$a['id']?></td>
                      <td style="font-weight:600;"><?=($a['first_name']??'').' '.($a['last_name']??'')?></td>
                      <td><span class="badge-status <?=$bc?>"><?=$a['status']?></span></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>

  <script src="<?= asset('JS/room-preview.js') ?>?v=<?= time() ?>"></script>
  <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
  <script>
    standardizePage('staff');
    setCurrentRole(ROLES.STAFF_TENANT);

    async function adminPreview(unitType, availCount) {
      if (typeof openRoomPreview !== 'function') {
        showToast('ℹ️ Room preview module not loaded.', 'var(--info)');
        return;
      }
      try {
        const res = await fetch('<?= url("/api/apartment-types") ?>').then(r => r.json());
        if (res.success) {
          const typeObj = res.data.find(t => t.type_key === unitType);
          if (typeObj) {
            const detailRes = await fetch(`<?= url("/api/apartment-types/detail") ?>?id=${typeObj.type_id}`).then(r => r.json());
            const fullTypeObj = detailRes.success ? detailRes.data : typeObj;
            openRoomPreview(fullTypeObj, {
              availableCount: availCount,
              serveUrl: '<?= url("/api/apartment-types/serve-image") ?>',
              selectLabel: 'View Unit Details',
              onSelect: function (type) {
                showToast('📋 Unit details for ' + typeObj.label + ' — view only in Staff Admin mode.', 'var(--info)');
              }
            });
            return;
          }
        }
      } catch (e) { console.error(e); }
    }
    
    // Initialize notification badge
    initNotifBadge('staff');
  </script>
</body>
</html>