<?php $active_page = 'admin_dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>ISCAG MIS — Dashboard</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .db{max-width:1280px;margin:0 auto}
    .db-section{margin-bottom:20px}
    .db-section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .db-section-title{font-size:.82rem;font-weight:700;color:var(--text-main);text-transform:uppercase;letter-spacing:.05em}
    .db-section-sub{font-size:.68rem;color:var(--text-muted)}

    /* KPI */
    .kpi-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
    .kpi{background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:14px 18px;transition:transform .2s,box-shadow .2s;position:relative;overflow:hidden}
    .kpi:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.04)}
    .kpi-accent{position:absolute;top:0;left:0;width:4px;height:100%;border-radius:12px 0 0 12px}
    .kpi-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
    .kpi-label{font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em}
    .kpi-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .kpi-icon svg{width:16px;height:16px;fill:currentColor}
    .kpi-val{font-family:'Lora',serif;font-size:1.4rem;font-weight:700;color:var(--text-main);line-height:1.1;margin-bottom:6px}
    .kpi-footer{display:flex;align-items:center;gap:6px;font-size:.72rem;font-weight:600}
    .kpi-change{display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:20px;font-size:.68rem;font-weight:700}
    .kpi-change.up{background:rgba(47,138,96,.08);color:var(--success)}
    .kpi-change.down{background:rgba(139,46,46,.08);color:var(--danger)}
    .kpi-change.flat{background:#e8ece9;color:var(--text-muted)}
    .kpi-period{color:var(--text-muted)}

    /* Charts */
    .chart-row{display:grid;gap:12px}
    .chart-row.two{grid-template-columns:5fr 3fr}
    .chart-row.three{grid-template-columns:1fr 1fr 1fr}
    .chart-row.equal{grid-template-columns:1fr 1fr}
    @media(max-width:1100px){.chart-row.two,.chart-row.three,.chart-row.equal{grid-template-columns:1fr}}
    .card{background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:16px}
    .card-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .card-title{font-size:.92rem;font-weight:700;color:var(--text-main)}
    .card-sub{font-size:.7rem;color:var(--text-muted);margin-top:2px}
    .card-badge{font-size:.65rem;font-weight:700;padding:4px 10px;border-radius:20px;background:#e8ece9;color:var(--text-muted)}

    /* Activity feed */
    .feed-item{display:flex;align-items:center;gap:12px;padding:11px 0;border-bottom:1px solid var(--border)}
    .feed-item:last-child{border-bottom:none}
    .feed-av{width:34px;height:34px;border-radius:50%;background:rgba(47,138,96,.08);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0}
    .feed-body{flex:1;min-width:0}
    .feed-text{font-size:.82rem;font-weight:500;color:var(--text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .feed-time{font-size:.68rem;color:var(--text-muted);margin-top:1px}
    .feed-tag{font-size:.62rem;font-weight:700;padding:3px 8px;border-radius:20px;flex-shrink:0}
    .feed-tag.s{background:rgba(47,138,96,.08);color:var(--success)}
    .feed-tag.w{background:rgba(199,154,43,.08);color:var(--warning)}
    .feed-tag.d{background:rgba(139,46,46,.08);color:var(--danger)}
    .feed-tag.n{background:#e8ece9;color:var(--text-muted)}

    /* Module bars */
    .mod-item{margin-bottom:16px}.mod-item:last-child{margin-bottom:0}
    .mod-head{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:5px}
    .mod-name{font-size:.82rem;font-weight:600;color:var(--text-main)}
    .mod-val{font-size:.75rem;font-weight:700}
    .mod-bar{height:6px;background:#e8ece9;border-radius:3px;overflow:hidden}
    .mod-fill{height:100%;border-radius:3px;transition:width .8s ease}

    /* Billing legend */
    .bl-row{display:flex;align-items:center;gap:10px;margin-bottom:14px}
    .bl-row:last-child{margin-bottom:0}
    .bl-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
    .bl-label{font-size:.82rem;font-weight:600;color:var(--text-main)}
    .bl-sub{font-size:.7rem;color:var(--text-muted)}

    .tooltip-wrap{position:relative;cursor:help}
    .tooltip-wrap:hover::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2e2a;color:#fff;font-size:.68rem;padding:5px 10px;border-radius:6px;white-space:nowrap;z-index:10;pointer-events:none}
  </style>
</head>
<body>
<div class="app-wrapper">
  <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>
  <main class="main-content">
    <div class="top-bar">
      <div class="top-bar-left">
        <div>
          <div class="top-bar-title">Dashboard</div>
          <div class="top-bar-subtitle">Welcome back, <?= htmlspecialchars(explode(' ',trim($_SESSION['name']??'Admin'))[0]) ?>. Here's your operational snapshot.</div>
        </div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/admin/analytics') ?>" class="btn-topbar">📊 Analytics</a>
      </div>
    </div>
    <div class="page-body">
      <div class="db">

        <!-- ═══════ SECTION 1: KPI OVERVIEW (5-second scan) ═══════ -->
        <div class="db-section">
          <div class="kpi-row">
            <?php
              $kpis = [
                ['Total Revenue','₱'.number_format($totalRevenue??0,2),'Collected payments','up','+12.5%','vs last month','var(--success)','rgba(47,138,96,.08)','var(--success)','M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z', url('/admin/mis_admin/billing')],
                ['Registered Users',number_format($totalUsers??0),'Active tenant accounts','up','+8.2%','vs last month','var(--info)','rgba(31,111,90,.08)','var(--info)','M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z', url('/admin/mis_admin/records')],
                ['Applications',number_format($totalApplications??0),$pendingApprovals.' pending review',($pendingApprovals>2?'down':'up'),($pendingApprovals>0?$pendingApprovals.' pending':'All clear'),'this period','var(--warning)','rgba(199,154,43,.08)','var(--warning)','M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z', url('/admin/mis_admin/apartment_confirmation')],
                ['Parking Permits',number_format($totalParking??0),'Active allocations','flat','—','all time','var(--primary)','rgba(23,107,69,.08)','var(--primary)','M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z', url('/admin/mis_admin/parking_approval')],
                ['Unread Alerts',number_format($auditFlags??0),'Requires attention',($auditFlags>5?'down':'flat'),($auditFlags>0?$auditFlags.' unread':'Clear'),'current','var(--danger)','rgba(139,46,46,.08)','var(--danger)','M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z', url('/admin/mis_admin/notification')]
              ];
              foreach($kpis as $k):
            ?>
            <a href="<?=$k[10]?>" class="kpi" style="text-decoration:none;color:inherit;cursor:pointer">
              <div class="kpi-accent" style="background:<?=$k[6]?>"></div>
              <div class="kpi-top">
                <div class="kpi-label"><?=$k[0]?></div>
                <div class="kpi-icon" style="background:<?=$k[7]?>;color:<?=$k[8]?>"><svg viewBox="0 0 24 24"><path d="<?=$k[9]?>"/></svg></div>
              </div>
              <div class="kpi-val"><?=$k[1]?></div>
              <div class="kpi-footer">
                <span class="kpi-change <?=$k[3]?>"><?=$k[3]==='up'?'↑':($k[3]==='down'?'↓':'—')?> <?=$k[4]?></span>
                <span class="kpi-period"><?=$k[5]?></span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ═══════ SECTION 2: ANALYTICS & TRENDS ═══════ -->
        <div class="db-section">
          <div class="chart-row two">
            <!-- System Activity Line Chart -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">System Activity</div><div class="card-sub">Events over the last 7 days</div></div>
                <span class="card-badge">Weekly</span>
              </div>
              <div style="position:relative;height:220px"><canvas id="activityChart"></canvas></div>
            </div>
            <!-- Application Status -->
            <div class="card">
              <div class="card-head">
                <div><div class="card-title">Application Status</div><div class="card-sub">Current distribution</div></div>
                <span class="card-badge"><?=number_format($totalApplications)?> total</span>
              </div>
              <div style="position:relative;height:160px;display:flex;justify-content:center"><canvas id="statusChart"></canvas></div>
              <div style="margin-top:16px">
                <?php foreach($distData as $d):
                  $c = stripos($d['status'],'pend')!==false?'var(--warning)':(stripos($d['status'],'approv')!==false||stripos($d['status'],'assign')!==false?'var(--success)':'var(--danger)');
                ?>
                <div class="bl-row"><div class="bl-dot" style="background:<?=$c?>"></div><div><div class="bl-label"><?=$d['status']?></div><div class="bl-sub"><?=$d['count']?> applications</div></div></div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <div class="db-section">
          <div class="chart-row three">
            <!-- Billing -->
            <div class="card">
              <div class="card-head"><div><div class="card-title">Billing Health</div><div class="card-sub">Invoice collection</div></div></div>
              <div style="position:relative;height:180px"><canvas id="billingChart"></canvas></div>
            </div>
            <!-- Unit Occupancy -->
            <div class="card">
              <div class="card-head"><div><div class="card-title">Unit Occupancy</div><div class="card-sub">Apartment availability</div></div></div>
              <div style="position:relative;height:180px"><canvas id="occupancyChart"></canvas></div>
            </div>
            <!-- Module Performance -->
            <div class="card">
              <div class="card-head"><div><div class="card-title">Module Performance</div><div class="card-sub">Records across services</div></div></div>
              <?php
                $maxM = max($totalApplications,$totalParking,$auditFlags,1);
                $mods = [
                  ['Apartments',$totalApplications,'var(--primary)'],
                  ['Parking',$totalParking,'var(--warning)'],
                  ['Billing',array_sum(array_column($billingStats,'count')),'var(--info)'],
                  ['Alerts',$auditFlags,'var(--danger)']
                ];
              ?>
              <?php foreach($mods as $m): ?>
              <div class="mod-item">
                <div class="mod-head">
                  <span class="mod-name"><?=$m[0]?></span>
                  <span class="mod-val" style="color:<?=$m[2]?>"><?=number_format($m[1])?></span>
                </div>
                <div class="mod-bar"><div class="mod-fill" style="width:<?=min(100,($m[1]/max($maxM,1))*100)?>%;background:<?=$m[2]?>"></div></div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- ═══════ SECTION 3: DETAILED INSIGHTS ═══════ -->
        <div class="db-section">
          <div class="db-section-head">
            <div><div class="db-section-title">Recent Activity</div><div class="db-section-sub">Latest system events and notifications</div></div>
            <a href="<?= url('/admin/mis_admin/audit_logs') ?>" style="font-size:.75rem;color:var(--primary);font-weight:600;text-decoration:none">View All Logs →</a>
          </div>
          <div class="card" style="padding:12px 16px">
            <?php if(!empty($recentLogs)): foreach($recentLogs as $log):
              $ini=strtoupper(substr($log['first_name']??'S',0,1).substr($log['last_name']??'Y',0,1));
              $nm=htmlspecialchars(($log['first_name']??'System').' '.($log['last_name']??''));
              $act=htmlspecialchars($log['title']??'System Action');
              $tm=date('M d, g:i A',strtotime($log['created_at']));
              $tc='n';
              if(stripos($act,'approv')!==false||stripos($act,'assign')!==false)$tc='s';
              elseif(stripos($act,'pend')!==false||stripos($act,'wait')!==false)$tc='w';
              elseif(stripos($act,'reject')!==false||stripos($act,'delete')!==false)$tc='d';
            ?>
            <div class="feed-item">
              <div class="feed-av"><?=$ini?></div>
              <div class="feed-body"><div class="feed-text"><strong><?=$nm?></strong> — <?=$act?></div><div class="feed-time"><?=$tm?></div></div>
              <span class="feed-tag <?=$tc?>"><?=ucfirst($log['type']??'info')?></span>
            </div>
            <?php endforeach; else: ?>
            <div style="text-align:center;padding:40px;color:var(--text-muted);font-size:.85rem">No recent activity found.</div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>
<script src="<?= asset('JS/admin-shared.js') ?>"></script>
<script>
standardizePage('admin');
document.addEventListener("DOMContentLoaded",function(){
  Chart.defaults.font.family="'Source Sans 3',sans-serif";
  Chart.defaults.color='#6f7f78';
  const P='#176b45',S='#2f8a60',W='#c79a2b',D='#8b2e2e',I='#1f6f5a';
  const sc=s=>{const l=(s||'').toLowerCase();if(l.includes('pend'))return W;if(l.includes('approv')||l.includes('assign')||l.includes('paid'))return S;if(l.includes('reject')||l.includes('overdue'))return D;if(l.includes('avail'))return I;if(l.includes('occup'))return P;return'#94a3b8'};

  // 1. Activity Line
  const aR=<?=json_encode($activityData??[])?>;
  const aL=aR.length>0?aR.map(d=>{const dt=new Date(d.date);return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'})}):['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  const aD=aR.length>0?aR.map(d=>+d.count):[5,12,8,15,10,20,14];
  const ctx1=document.getElementById('activityChart').getContext('2d');
  const g1=ctx1.createLinearGradient(0,0,0,300);g1.addColorStop(0,'rgba(23,107,69,.12)');g1.addColorStop(1,'rgba(23,107,69,0)');
  new Chart(ctx1,{type:'line',data:{labels:aL,datasets:[{label:'Events',data:aD,borderColor:P,backgroundColor:g1,borderWidth:2.5,pointBackgroundColor:'#fff',pointBorderColor:P,pointBorderWidth:2,pointRadius:4,pointHoverRadius:7,fill:true,tension:.4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'#1f2e2a',padding:12,cornerRadius:8,titleFont:{weight:'700'}}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#e8ece9',drawBorder:false}}}}});

  // 2. Status Doughnut
  const dR=<?=json_encode($distData??[])?>;
  new Chart(document.getElementById('statusChart'),{type:'doughnut',data:{labels:dR.map(d=>d.status),datasets:[{data:dR.map(d=>+d.count),backgroundColor:dR.map(d=>sc(d.status)),borderWidth:0,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'68%',plugins:{legend:{display:false}}}});

  // 3. Billing Bar
  const bR=<?=json_encode($billingStats??[])?>;
  new Chart(document.getElementById('billingChart'),{type:'bar',data:{labels:bR.map(d=>d.status),datasets:[{label:'Invoices',data:bR.map(d=>+d.count),backgroundColor:bR.map(d=>sc(d.status)),borderRadius:6,barThickness:28}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{afterLabel:c=>'₱'+Number(bR[c.dataIndex]?.total||0).toLocaleString()}}},scales:{x:{beginAtZero:true,grid:{color:'#e8ece9',drawBorder:false},ticks:{stepSize:1}},y:{grid:{display:false}}}}});

  // 4. Occupancy Bar
  const oR=<?=json_encode($occupancyData??[])?>;
  new Chart(document.getElementById('occupancyChart'),{type:'bar',data:{labels:oR.map(d=>d.status),datasets:[{label:'Units',data:oR.map(d=>+d.count),backgroundColor:oR.map(d=>sc(d.status)),borderRadius:6,barThickness:28}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'#e8ece9',drawBorder:false},ticks:{stepSize:1}}}}});
});
</script>
</body>
</html>
