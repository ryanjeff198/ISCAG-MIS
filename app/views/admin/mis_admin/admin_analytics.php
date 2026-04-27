<?php $active_page = 'analytics'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>ISCAG MIS — Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .an{max-width:1280px;margin:0 auto}
    .an-section{margin-bottom:20px}
    .an-section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .an-section-title{font-size:.82rem;font-weight:700;color:var(--text-main);text-transform:uppercase;letter-spacing:.05em}
    .an-section-sub{font-size:.68rem;color:var(--text-muted)}

    .kpi-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px}
    .kpi{background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:12px 14px;text-align:center;transition:transform .2s,box-shadow .2s;position:relative;overflow:hidden}
    .kpi:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.04)}
    .kpi-accent{position:absolute;top:0;left:0;width:100%;height:3px}
    .kpi-label{font-size:.65rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px}
    .kpi-val{font-family:'Lora',serif;font-size:1.3rem;font-weight:700;color:var(--text-main)}
    .kpi-change{display:inline-flex;align-items:center;gap:3px;padding:2px 7px;border-radius:20px;font-size:.65rem;font-weight:700;margin-top:6px}
    .kpi-change.up{background:rgba(47,138,96,.08);color:var(--success)}
    .kpi-change.flat{background:#e8ece9;color:var(--text-muted)}

    .chart-row{display:grid;gap:12px}
    .chart-row.two{grid-template-columns:5fr 3fr}
    .chart-row.three{grid-template-columns:1fr 1fr 1fr}
    .chart-row.equal{grid-template-columns:1fr 1fr}
    @media(max-width:1100px){.chart-row.two,.chart-row.three,.chart-row.equal{grid-template-columns:1fr}}
    .card{background:var(--card-bg);border:1px solid var(--border);border-radius:10px;padding:16px}
    .card-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
    .card-title{font-size:.88rem;font-weight:700;color:var(--text-main)}
    .card-sub{font-size:.68rem;color:var(--text-muted);margin-top:2px}
    .card-badge{font-size:.62rem;font-weight:700;padding:3px 8px;border-radius:20px;background:#e8ece9;color:var(--text-muted)}

    /* Data Table */
    .dt{width:100%;border-collapse:collapse}
    .dt th{background:var(--content-bg);color:var(--text-muted);font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);cursor:pointer;user-select:none}
    .dt th:hover{color:var(--primary)}
    .dt td{padding:8px 14px;border-bottom:1px solid var(--border);font-size:.82rem;color:var(--text-main);font-weight:500}
    .dt tr:last-child td{border-bottom:none}
    .dt tr:hover{background:rgba(47,138,96,.02)}
    .dt-dot{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:8px}
    .dt-tag{font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px}
    .dt-tag.active{background:rgba(47,138,96,.08);color:var(--success)}

    .dt-search{border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:.82rem;width:260px;outline:none;font-family:inherit;transition:border-color .2s}
    .dt-search:focus{border-color:var(--primary)}

    .btn-export{background:var(--primary-dark);color:#fff;border:none;padding:8px 18px;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:transform .2s}
    .btn-export:hover{transform:translateY(-1px);background:var(--primary)}
  </style>
</head>
<body>
<div class="app-wrapper">
  <?php include BASE_PATH . '/app/views/components/mis_admin_sidebar.php'; ?>
  <main class="main-content">
    <div class="top-bar">
      <div class="top-bar-left">
        <div>
          <div class="top-bar-title">Analytics</div>
          <div class="top-bar-subtitle">Comprehensive data insights across all ISCAG MIS modules</div>
        </div>
      </div>
      <div class="top-bar-actions">
        <a href="<?= url('/admin/dashboard') ?>" class="btn-topbar">← Dashboard</a>
        <button class="btn-export" onclick="exportCSV()">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
          Export CSV
        </button>
      </div>
    </div>
    <div class="page-body">
      <div class="an">

        <!-- ═══ SECTION 1: KPI OVERVIEW ═══ -->
        <div class="an-section">
          <div class="kpi-row">
            <div class="kpi"><div class="kpi-accent" style="background:var(--primary)"></div><div class="kpi-label">Total Users</div><div class="kpi-val"><?=number_format($totalUsers)?></div><span class="kpi-change up">↑ Active</span></div>
            <div class="kpi"><div class="kpi-accent" style="background:var(--warning)"></div><div class="kpi-label">Apartment Apps</div><div class="kpi-val"><?=number_format($totalApps)?></div><span class="kpi-change flat">All time</span></div>
            <div class="kpi"><div class="kpi-accent" style="background:var(--info)"></div><div class="kpi-label">Parking Permits</div><div class="kpi-val"><?=number_format($totalParking)?></div><span class="kpi-change flat">All time</span></div>
            <div class="kpi"><div class="kpi-accent" style="background:var(--danger)"></div><div class="kpi-label">Notifications</div><div class="kpi-val"><?=number_format($totalNotifs)?></div><span class="kpi-change up">↑ System</span></div>
            <?php
              $billingTotal=0; foreach($billingDist as $b) $billingTotal+=$b['total'];
            ?>
            <div class="kpi"><div class="kpi-accent" style="background:var(--success)"></div><div class="kpi-label">Total Billing</div><div class="kpi-val">₱<?=number_format($billingTotal,0)?></div><span class="kpi-change up">↑ Revenue</span></div>
          </div>
        </div>

        <!-- ═══ SECTION 2: ANALYTICS & TRENDS ═══ -->
        <div class="an-section">
          <div class="an-section-head">
            <div><div class="an-section-title">Trends & Distributions</div><div class="an-section-sub">Visual breakdown of system activity and user data</div></div>
          </div>

          <!-- Row A: Growth + Activity Timeline -->
          <div class="chart-row two" style="margin-bottom:16px">
            <div class="card">
              <div class="card-head"><div><div class="card-title">User Growth</div><div class="card-sub">Monthly registration trend</div></div><span class="card-badge">Monthly</span></div>
              <div style="position:relative;height:240px"><canvas id="growthChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Module Distribution</div><div class="card-sub">Records per service</div></div></div>
              <div style="position:relative;height:240px;display:flex;justify-content:center"><canvas id="moduleChart"></canvas></div>
            </div>
          </div>

          <!-- Row B: 3 columns — App Status, Parking, Billing -->
          <div class="chart-row three" style="margin-bottom:16px">
            <div class="card">
              <div class="card-head"><div><div class="card-title">Application Status</div><div class="card-sub">Apartment requests</div></div></div>
              <div style="position:relative;height:220px"><canvas id="appChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Parking Permits</div><div class="card-sub">Allocation breakdown</div></div></div>
              <div style="position:relative;height:220px;display:flex;justify-content:center"><canvas id="parkChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Billing Health</div><div class="card-sub">Invoice status</div></div></div>
              <div style="position:relative;height:220px"><canvas id="billChart"></canvas></div>
            </div>
          </div>

          <!-- Row C: 3 columns — Gender, Occupancy, Verification -->
          <div class="chart-row three" style="margin-bottom:16px">
            <div class="card">
              <div class="card-head"><div><div class="card-title">Demographics</div><div class="card-sub">Gender distribution</div></div></div>
              <div style="position:relative;height:220px;display:flex;justify-content:center"><canvas id="genderChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Unit Occupancy</div><div class="card-sub">Apartment availability</div></div></div>
              <div style="position:relative;height:220px"><canvas id="occChart"></canvas></div>
            </div>
            <div class="card">
              <div class="card-head"><div><div class="card-title">Account Verification</div><div class="card-sub">Verified vs unverified</div></div></div>
              <div style="position:relative;height:220px;display:flex;justify-content:center"><canvas id="verifyChart"></canvas></div>
            </div>
          </div>

          <!-- Row D: Full-width timeline -->
          <div class="card">
            <div class="card-head"><div><div class="card-title">Activity Timeline</div><div class="card-sub">System events — last 14 days</div></div><span class="card-badge">14 Days</span></div>
            <div style="position:relative;height:260px"><canvas id="tlChart"></canvas></div>
          </div>
        </div>

        <!-- ═══ SECTION 3: DATA TABLE ═══ -->
        <div class="an-section">
          <div class="an-section-head">
            <div><div class="an-section-title">Module Breakdown</div><div class="an-section-sub">Sortable summary of all ISCAG modules</div></div>
            <input type="text" class="dt-search" id="dtSearch" placeholder="Search modules..." oninput="filterTable()"/>
          </div>
          <div class="card" style="padding:0;overflow:hidden;border-radius:12px">
            <table class="dt" id="dataTable">
              <thead><tr><th onclick="sortTable(0)">Module ↕</th><th onclick="sortTable(1)">Records ↕</th><th>Category</th><th>Status</th></tr></thead>
              <tbody>
                <?php
                  $rows = [
                    ['Apartment Applications', $totalApps, 'Operations', 'var(--primary)'],
                    ['Parking Permits', $totalParking, 'Operations', 'var(--warning)'],
                    ['Billing Invoices', array_sum(array_column($billingDist,'count')), 'Finance', 'var(--info)'],
                    ['System Notifications', $totalNotifs, 'Governance', 'var(--danger)'],
                    ['Registered Users', $totalUsers, 'Governance', 'var(--success)'],
                  ];
                  foreach($rows as $r):
                ?>
                <tr>
                  <td><span class="dt-dot" style="background:<?=$r[3]?>"></span><?=$r[0]?></td>
                  <td style="font-weight:700;font-family:'Lora',serif;font-size:1rem"><?=number_format($r[1])?></td>
                  <td><?=$r[2]?></td>
                  <td><span class="dt-tag active">Active</span></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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
  const P='#176b45',S='#2f8a60',W='#c79a2b',D='#8b2e2e',I='#1f6f5a',A='#e0b84a';
  const sc=s=>{const l=(s||'').toLowerCase();if(l.includes('pend'))return W;if(l.includes('approv')||l.includes('assign')||l.includes('paid')||l.includes('verif'))return S;if(l.includes('reject')||l.includes('overdue')||l.includes('unverif'))return D;if(l.includes('avail'))return I;if(l.includes('occup'))return P;if(l.includes('reserv'))return A;return'#94a3b8'};
  const noLegend={display:false};
  const cleanX={grid:{display:false}};
  const cleanY={beginAtZero:true,grid:{color:'#e8ece9',drawBorder:false}};

  // 1. User Growth — Area Line
  const gR=<?=json_encode($userGrowth??[])?>;
  const ctx1=document.getElementById('growthChart').getContext('2d');
  const g1=ctx1.createLinearGradient(0,0,0,300);g1.addColorStop(0,'rgba(23,107,69,.12)');g1.addColorStop(1,'rgba(23,107,69,0)');
  new Chart(ctx1,{type:'line',data:{labels:gR.map(d=>d.month),datasets:[{label:'Users',data:gR.map(d=>d.count),borderColor:P,backgroundColor:g1,borderWidth:2.5,pointBackgroundColor:'#fff',pointBorderColor:P,pointBorderWidth:2,pointRadius:4,pointHoverRadius:7,fill:true,tension:.4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:cleanX,y:cleanY}}});

  // 2. Module — Polar Area
  const mR=<?=json_encode($moduleDist??[])?>;
  new Chart(document.getElementById('moduleChart'),{type:'polarArea',data:{labels:mR.map(d=>d.module),datasets:[{data:mR.map(d=>d.count),backgroundColor:[P+'bb',W+'bb',I+'bb',S+'bb'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}},scales:{r:{ticks:{display:false},grid:{color:'#e8ece9'}}}}});

  // 3. App Status — Horizontal Bar
  const asR=<?=json_encode($appStatusDist??[])?>;
  new Chart(document.getElementById('appChart'),{type:'bar',data:{labels:asR.map(d=>d.status),datasets:[{label:'Apps',data:asR.map(d=>+d.count),backgroundColor:asR.map(d=>sc(d.status)),borderRadius:6,barThickness:22}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:{...cleanY,ticks:{stepSize:1}},y:cleanX}}});

  // 4. Parking — Pie
  const pkR=<?=json_encode($parkingStatusDist??[])?>;
  new Chart(document.getElementById('parkChart'),{type:'pie',data:{labels:pkR.map(d=>d.status),datasets:[{data:pkR.map(d=>+d.count),backgroundColor:pkR.map(d=>sc(d.status)),borderWidth:2,borderColor:'#fff',hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}}}});

  // 5. Billing — Vertical Bar
  const blR=<?=json_encode($billingDist??[])?>;
  new Chart(document.getElementById('billChart'),{type:'bar',data:{labels:blR.map(d=>d.status),datasets:[{label:'Invoices',data:blR.map(d=>+d.count),backgroundColor:blR.map(d=>sc(d.status)),borderRadius:6,barThickness:32}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend,tooltip:{callbacks:{afterLabel:c=>'₱'+Number(blR[c.dataIndex]?.total||0).toLocaleString()}}},scales:{x:cleanX,y:{...cleanY,ticks:{stepSize:1}}}}});

  // 6. Gender — Doughnut
  const gnR=<?=json_encode($genderDist??[])?>;
  new Chart(document.getElementById('genderChart'),{type:'doughnut',data:{labels:gnR.map(d=>d.gender||'Unknown'),datasets:[{data:gnR.map(d=>+d.count),backgroundColor:[I,W,D,'#94a3b8'],borderWidth:0,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'60%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}}}});

  // 7. Occupancy — Horizontal Bar
  const ocR=<?=json_encode($occupancyDist??[])?>;
  new Chart(document.getElementById('occChart'),{type:'bar',data:{labels:ocR.map(d=>d.status),datasets:[{label:'Units',data:ocR.map(d=>+d.count),backgroundColor:ocR.map(d=>sc(d.status)),borderRadius:4,barThickness:24}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:cleanY,y:cleanX}}});

  // 8. Verification — Polar Area
  const vrR=<?=json_encode($statusDist??[])?>;
  new Chart(document.getElementById('verifyChart'),{type:'polarArea',data:{labels:vrR.map(d=>d.status),datasets:[{data:vrR.map(d=>+d.count),backgroundColor:vrR.map(d=>sc(d.status)+'bb'),borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:12,font:{size:11}}}},scales:{r:{ticks:{display:false},grid:{color:'#e8ece9'}}}}});

  // 9. Timeline — Bar
  const tlR=<?=json_encode($activityTimeline??[])?>;
  const tlL=tlR.length>0?tlR.map(d=>{const dt=new Date(d.date);return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'})}):['No data'];
  const tlD=tlR.length>0?tlR.map(d=>+d.count):[0];
  new Chart(document.getElementById('tlChart'),{type:'bar',data:{labels:tlL,datasets:[{label:'Events',data:tlD,backgroundColor:P+'99',borderRadius:4,barThickness:18}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:noLegend},scales:{x:cleanX,y:cleanY}}});
});

// Table sort
let sortDir=1;
function sortTable(col){
  const tb=document.querySelector('#dataTable tbody');
  const rows=[...tb.querySelectorAll('tr')];
  rows.sort((a,b)=>{
    const av=a.cells[col].textContent.trim(),bv=b.cells[col].textContent.trim();
    return(isNaN(av)?av.localeCompare(bv):+av.replace(/,/g,'')-+bv.replace(/,/g,''))*sortDir;
  });
  sortDir*=-1;
  rows.forEach(r=>tb.appendChild(r));
}
function filterTable(){
  const q=document.getElementById('dtSearch').value.toLowerCase();
  document.querySelectorAll('#dataTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
}
function exportCSV(){
  const t=document.getElementById('dataTable');
  let csv=[];
  t.querySelectorAll('tr').forEach(r=>{
    const cols=r.querySelectorAll('td,th');
    csv.push(Array.from(cols).map(c=>'"'+c.innerText.replace(/"/g,'""')+'"').join(','));
  });
  const a=document.createElement('a');
  a.download='ISCAG_Analytics_'+new Date().toISOString().split('T')[0]+'.csv';
  a.href=URL.createObjectURL(new Blob([csv.join('\n')],{type:'text/csv'}));
  a.click();
}
</script>
</body>
</html>
