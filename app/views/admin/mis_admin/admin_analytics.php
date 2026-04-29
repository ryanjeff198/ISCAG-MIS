<?php $active_page = 'analytics'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>ISCAG MIS — Analytics</title>
  <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
  <link rel="stylesheet" href="<?= asset('css/admin-shared.css') ?>?v=<?= time() ?>"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Ultra-wide Analytics Layout */
    .an { max-width: 2400px; margin: 0 auto; width: 98%; padding-bottom: 50px; }
    
    .an-section { margin-bottom: 32px; }
    .an-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .an-section-title { font-size: 0.95rem; font-weight: 700; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.08em; }
    .an-section-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }

    /* KPI Grid Scale */
    /* KPI Grid - Unified Scale */
    .admin-insights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .kpi-trend {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-top: 4px;
    }
    .kpi-trend.up { color: var(--success); }
    .kpi-trend.flat { color: var(--text-muted); }

    /* Charts Scaling */
    .chart-row { display: grid; gap: 20px; }
    .chart-row.two { grid-template-columns: 3fr 1.2fr; }
    .chart-row.three { grid-template-columns: 1fr 1fr 1fr; }
    .chart-row.equal { grid-template-columns: 1fr 1fr; }
    
    @media (max-width: 1400px) {
        .chart-row.two { grid-template-columns: 2fr 1fr; }
    }
    
    @media (max-width: 1100px) {
        .chart-row.two, .chart-row.three, .chart-row.equal { grid-template-columns: 1fr; }
    }

    .card { 
        background: var(--card-bg); 
        border: 1px solid var(--border); 
        border-radius: 16px; 
        padding: 28px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
        transition: all 0.3s ease;
    }
    .card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
    .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .card-title { font-size: 1.05rem; font-weight: 700; color: var(--text-main); }
    .card-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 5px; }
    .card-badge { font-size: 0.7rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; background: rgba(23, 107, 69, 0.05); color: var(--primary); }

    /* Data Table Scale */
    .dt { width: 100%; border-collapse: collapse; }
    .dt th { background: var(--content-bg); color: var(--text-muted); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 14px 20px; text-align: left; border-bottom: 2px solid var(--border); cursor: pointer; user-select: none; }
    .dt th:hover { color: var(--primary); }
    .dt td { padding: 16px 20px; border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--text-main); font-weight: 500; }
    .dt tr:last-child td { border-bottom: none; }
    .dt tr:hover { background: rgba(47, 138, 96, 0.02); }
    .dt-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; }
    .dt-tag { font-size: 0.7rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; }
    .dt-tag.active { background: rgba(47, 138, 96, 0.08); color: var(--success); }

    .dt-search { border: 1px solid var(--border); border-radius: 10px; padding: 10px 18px; font-size: 0.9rem; width: 320px; outline: none; font-family: inherit; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .dt-search:focus { border-color: var(--primary); box-shadow: 0 4px 12px rgba(23,107,69,0.1); width: 380px; }

    .btn-export { background: var(--primary-dark); color: #fff; border: none; padding: 10px 22px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(23,107,69,0.2); }
    .btn-export:hover { transform: translateY(-2px); background: var(--primary); box-shadow: 0 6px 20px rgba(23,107,69,0.3); }
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
          <div class="admin-insights">
            <div class="insight-card">
              <div class="insight-label">Total Users</div>
              <div class="insight-value" style="color:var(--primary)"><?=number_format($totalUsers)?></div>
              <div class="kpi-trend up">↑ Active</div>
              <div class="insight-icon-bg" style="color:var(--primary)"><svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Apartment Apps</div>
              <div class="insight-value" style="color:var(--warning)"><?=number_format($totalApps)?></div>
              <div class="kpi-trend flat">All time</div>
              <div class="insight-icon-bg" style="color:var(--warning)"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Parking Permits</div>
              <div class="insight-value" style="color:var(--info)"><?=number_format($totalParking)?></div>
              <div class="kpi-trend flat">All time</div>
              <div class="insight-icon-bg" style="color:var(--info)"><svg viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg></div>
            </div>
            <div class="insight-card">
              <div class="insight-label">Notifications</div>
              <div class="insight-value" style="color:var(--danger)"><?=number_format($totalNotifs)?></div>
              <div class="kpi-trend up">↑ System</div>
              <div class="insight-icon-bg" style="color:var(--danger)"><svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg></div>
            </div>
            <?php
              $billingTotal=0; foreach($billingDist as $b) $billingTotal+=$b['total'];
            ?>
            <div class="insight-card">
              <div class="insight-label">Total Billing</div>
              <div class="insight-value" style="color:var(--success)">₱<?=number_format($billingTotal,0)?></div>
              <div class="kpi-trend up">↑ Revenue</div>
              <div class="insight-icon-bg" style="color:var(--success)"><svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg></div>
            </div>
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
