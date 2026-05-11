<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/User.php';
$u = new User();
$d = $u->findById($_SESSION['user_id']) ?: [];
$e = $u->getAdditionalInfo($_SESSION['user_id']) ?: [];
$d = array_merge($d, $e);
$name = htmlspecialchars(($d['first_name']??'').' '.($d['last_name']??''));
$init = strtoupper(substr($d['first_name']??'S',0,1));
$today = date('l');
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>ISCAG — My School Dashboard</title>
<link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
<link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{--g:#1b5e20;--gl:#2e7d32;--gx:#e8f5e9;--gold:#D4AF37;--gd:#B8860B;--t:#1f2937;--m:#6b7280;--b:#e5e7eb;--w:#f59e0b;--bg:#f4f6f8;}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--t);overflow-x:hidden;}
    
    .dash-hero{
        background:linear-gradient(135deg,#1b5e20,#2e7d32 60%,#388e3c);
        color:#fff;
        padding:70px 50px;
        position:relative;
        overflow:hidden;
        min-height: 200px;
        display: flex;
        align-items: center;
    }
    .dash-hero::after{content:'';position:absolute;top:-40%;right:-5%;width:350px;height:350px;background:rgba(212,175,55,.1);border-radius:50%;z-index: 0;}
    .dash-hero::before{content:'';position:absolute;bottom:-10%;left:5%;width:200px;height:200px;background:rgba(255,255,255,.05);border-radius:50%;z-index: 0;}
    
    .hero-top{display:flex;align-items:center;justify-content:space-between;position:relative;z-index:2;width: 100%;flex-wrap: wrap;gap: 20px;}
    .hero-left{display:flex;align-items:center;gap:20px;flex: 1;min-width: 300px;}
    .av{width:70px;height:70px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.6rem;color:#fff;border:4px solid rgba(255,255,255,.3);box-shadow:0 10px 20px rgba(0,0,0,.1);flex-shrink: 0;}
    .hero-name{font-size:2rem;font-weight:800;font-family:'Lora',serif;letter-spacing:-0.5px;line-height:1.2;margin:0;}
    .hero-sub{font-size:1rem;opacity:.9;margin-top:6px;font-weight: 500;}
    .hero-badge{background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);border-radius:30px;padding:10px 24px;font-size:.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;white-space: nowrap;}

    .pw{padding:0 50px 50px; max-width: 1400px; margin: 0 auto;}
    
    /* Stats (Insights) Alignment & Design Refinement */
    .stats{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:20px;
        margin-top:30px;
        margin-bottom:50px;
        position:relative;
        z-index:10;
    }
    .stat{
        background:#fff;
        border-radius:16px;
        padding:24px;
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: 0.3s;
    }
    .stat:hover{ transform: translateY(-4px); border-color: var(--g); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    
    .stat-icon{
        width:48px; height:48px; border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        margin-bottom:16px;
    }
    .stat-icon svg{ width:24px; height:24px; fill:#fff; }
    .stat-val{font-size:2.2rem;font-weight:900;color:var(--t);line-height:1;font-family:'Lora', serif;}
    .stat-lbl{font-size:.7rem;font-weight:800;color:var(--m);text-transform:uppercase;margin-top:10px;letter-spacing:1.5px;}

    /* Quick Actions Section */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 50px;
    }
    .action-card {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid var(--b);
        text-decoration: none;
        transition: 0.3s;
    }
    .action-card:hover {
        background: var(--gx);
        border-color: var(--gl);
        transform: translateX(5px);
    }
    .action-icon {
        width: 50px; height: 50px; border-radius: 12px;
        background: var(--g); display: flex; align-items: center; justify-content: center;
    }
    .action-icon svg { width: 24px; height: 24px; fill: #fff; }
    .action-info h3 { font-size: 1rem; font-weight: 800; color: var(--t); margin-bottom: 4px; }
    .action-info p { font-size: 0.8rem; color: var(--m); font-weight: 600; }

    /* Section Headers */
    .sec{margin-bottom:50px;}
    .sec-head{font-family:'Lora',serif;font-size:1.5rem;font-weight:800;color:var(--g);margin-bottom:28px;display:flex;align-items:center;gap:15px;position:relative;padding-bottom:12px;}
    .sec-head::after{content:'';position:absolute;bottom:0;left:0;width:50px;height:4px;background:var(--gold);border-radius:10px;}
    .sec-head svg{width:28px;height:28px;fill:var(--g);}

    /* ── Tabbed Schedule Styles ── */
    .schedule-tabs {
        display: flex; gap: 10px; margin-bottom: 25px; overflow-x: auto; padding-bottom: 10px;
        scrollbar-width: none; -ms-overflow-style: none;
    }
    .schedule-tabs::-webkit-scrollbar { display: none; }
    
    .day-tab {
        padding: 12px 24px; background: #fff; border: 1.5px solid var(--b); border-radius: 12px;
        font-size: 0.85rem; font-weight: 700; color: var(--m); cursor: pointer; transition: 0.2s;
        white-space: nowrap; display: flex; flex-direction: column; align-items: center; min-width: 100px;
    }
    .day-tab span:last-child { font-size: 0.65rem; font-weight: 500; opacity: 0.7; margin-top: 2px; }
    .day-tab:hover { border-color: var(--gl); color: var(--g); background: var(--gx); }
    .day-tab.active { background: var(--g); border-color: var(--g); color: #fff; box-shadow: 0 8px 16px rgba(27, 94, 32, 0.2); }
    .day-tab.active span:last-child { opacity: 0.9; }
    
    .schedule-content { min-height: 250px; position: relative; }
    .day-pane { display: none; animation: fadeIn 0.3s ease; }
    .day-pane.active { display: block; }
    
    .timeline-item {
        display: flex; gap: 20px; padding: 20px; background: #fff; border: 1px solid var(--b);
        border-radius: 16px; margin-bottom: 15px; align-items: center; justify-content: space-between; transition: 0.3s;
    }
    .timeline-item:hover { transform: translateY(-4px); border-color: var(--gl); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .timeline-time {
        min-width: 130px; font-weight: 900; font-size: 0.75rem; color: var(--g);
        background: var(--gx); padding: 8px 16px; border-radius: 30px; text-align: center;
        white-space: nowrap;
    }
    .timeline-info h4 { font-weight: 800; font-size: 1.05rem; color: var(--t); margin-bottom: 4px; }
    .timeline-info p { font-size: 0.8rem; color: var(--m); font-weight: 600; margin: 0; }
    
    .closed-state {
        text-align: center; padding: 60px 20px; background: #fff; border: 1.5px dashed var(--b);
        border-radius: 20px; color: var(--m);
    }
    .closed-state div:first-child { font-size: 3rem; margin-bottom: 15px; }
    .closed-state h3 { font-weight: 800; color: #ef4444; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }

    /* Progress & Subjects */
    .prog-grid, .subj-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
    .prog, .subj{
        background:#fff;
        border-radius:24px;
        padding:35px;
        border:1px solid var(--b);
        transition:all .3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }
    .prog:hover, .subj:hover{box-shadow:0 20px 45px rgba(0,0,0,0.08);border-color:var(--gl);transform: translateY(-5px);}
    .prog-title{font-weight:800;font-size:1rem;margin-bottom:10px;color:var(--g);display: flex; justify-content: space-between;}
    .prog-level{font-size:.85rem;color:var(--m);font-weight:600;margin-bottom:25px;}
    .bar-bg{width:100%;height:14px;background:#f1f5f9;border-radius:15px;overflow:hidden;box-shadow:inset 0 2px 5px rgba(0,0,0,0.08);}
    .bar-fill{height:100%;border-radius:15px;transition:width 1.5s cubic-bezier(0.19, 1, 0.22, 1);}
    .prog-pct{font-size:.9rem;font-weight:900;color:var(--g);margin-top:12px;text-align:right;}

    .subj{border-top:6px solid var(--g); display: flex; flex-direction: column; justify-content: space-between;}
    .subj-name{font-weight:900;font-size:1.2rem;margin-bottom:12px;color:var(--t);font-family: 'Lora', serif;}
    .subj-desc{font-size:.85rem;color:var(--m);line-height:1.7;margin-bottom:25px; flex-grow: 1;}
    .subj-meta{display:flex;flex-direction:column;gap:12px;padding-top:20px;border-top:2px dashed #f1f5f9;}
    .subj-row{display:flex;align-items:center;gap:12px;font-size:.8rem;font-weight:700;color:var(--m);}
    .subj-row svg{width:18px;height:18px;fill:var(--gl);}

    /* Modal Styles */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(10, 40, 10, 0.7); backdrop-filter: blur(8px);
        display: none; align-items: center; justify-content: center; z-index: 1000;
        animation: fadeIn 0.2s ease;
    }
    .modal-content {
        background: #fff; width: 95%; max-width: 550px; border-radius: 30px;
        max-height: 95vh; display: flex; flex-direction: column;
        overflow: hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.3);
        transform: translateY(10px); transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .modal-header {
        background: linear-gradient(135deg, var(--g), var(--gl)); color: #fff; padding: 30px 40px;
        display: flex; align-items: center; justify-content: space-between;
        position: relative; flex-shrink: 0;
    }
    .modal-header::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px;
        background: rgba(255,255,255,0.1);
    }
    .modal-title { font-family: 'Lora', serif; font-size: 1.8rem; font-weight: 800; }
    .close-modal { cursor: pointer; font-size: 2rem; line-height: 1; opacity: 0.7; transition: 0.3s; }
    .close-modal:hover { opacity: 1; }
    
    .modal-body { padding: 40px; max-height: 100%; overflow-y: auto; flex: 1; }
    .modal-class-item {
        display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #f1f5f9;
        transition: 0.3s;
    }
    .modal-class-item:hover { transform: scale(1.02); }
    .modal-class-item:last-child { border: none; }
    
    .modal-time-box {
        background: var(--gx); color: var(--g); padding: 10px 15px;
        border-radius: 12px; font-weight: 900; font-size: 0.75rem; 
        min-width: 120px; text-align: center; border: 1px solid rgba(27, 94, 32, 0.1);
    }
    .modal-class-info h4 { font-weight: 800; font-size: 1.1rem; color: var(--t); margin-bottom: 5px; }
    .modal-class-info p { font-size: 0.85rem; color: var(--m); font-weight: 600; display: flex; align-items: center; gap: 5px; }
    .modal-class-info p::before { content: '📁'; font-size: 0.7rem; }

    /* Bottom Info Row */
    .bottom-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-top: 10px;
    }
    .info-c {
        background: #fff;
        border-radius: 24px;
        padding: 35px;
        border: 1px solid var(--b);
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    }
    .info-t {
        font-family: 'Lora', serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--g);
        margin-bottom: 25px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--gx);
    }
    .oh-r {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        font-weight: 600;
    }
    .oh-r:last-child { border: none; }
    .oh-d { color: var(--m); }
    .oh-t { color: var(--t); font-weight: 700; }
    
    .ct-line {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 1rem;
        font-weight: 600;
        color: var(--t);
    }
    .ct-line:last-child { margin-bottom: 0; }
    .ct-line svg {
        width: 22px; height: 22px; fill: var(--g); flex-shrink: 0;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    @media(max-width:1200px){.stats{grid-template-columns:repeat(2,1fr);}.sched-grid{grid-template-columns:repeat(4,1fr);}}
    @media(max-width:900px){.prog-grid,.subj-grid,.quick-actions{grid-template-columns:1fr;}.sched-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.stats,.sched-grid,.bottom-row{grid-template-columns:1fr;}.dash-hero{padding:50px 25px 90px;}.pw{padding:0 20px 40px;}.hero-name{font-size:1.6rem;}.stat-val{font-size:1.8rem;}}
</style></head><body>
<div class="app-wrapper">
<?php $active_page='female_school'; include BASE_PATH.'/app/views/user/sidebar.php'; ?>
<div class="main-content">
<div class="top-bar">
    <div class="top-bar-left">
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Student Dashboard</div>
    </div>
    <div class="top-bar-actions">
        <a href="<?= url('/user/dashboard') ?>" class="btn-topbar">Main Dashboard</a>
    </div>
</div>

<div class="dash-hero">
    <div class="hero-top">
        <div class="hero-left">
            <div class="av"><?= $init ?></div>
            <div>
                <div class="hero-name">Assalamu Alaikum, <?= $name ?></div>
                <div class="hero-sub">ISCAG Da'wah Female School — <?= !empty($_SESSION['female_education_enrolled']) ? 'Enrolled Student' : 'General Schedule' ?></div>
            </div>
        </div>
        <div class="hero-badge">
            <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:currentColor; margin-right:4px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            <?= !empty($_SESSION['female_education_enrolled']) ? 'Verified • Active' : 'Public Access' ?> 
            <span style="margin: 0 8px; opacity:0.5;">|</span>
            <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:currentColor; margin-right:4px;"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
            <?= date('F Y') ?>
        </div>
    </div>
</div>

<div class="pw">
    <!-- STATS (INSIGHTS) -->
    <div class="stats">
        <div class="stat">
            <div class="stat-icon" style="background:var(--g);"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg></div>
            <div class="stat-val"><?= !empty($_SESSION['female_education_enrolled']) ? '04' : '--' ?></div>
            <div class="stat-lbl">Enrolled</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:var(--gold);"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            <div class="stat-val"><?= $today==='Monday'?'00':'02' ?></div>
            <div class="stat-lbl">Today</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:#2563eb;"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            <div class="stat-val"><?= !empty($_SESSION['female_education_enrolled']) ? '92%' : '--' ?></div>
            <div class="stat-lbl">Attendance</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:#7c3aed;"><svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg></div>
            <div class="stat-val"><?= !empty($_SESSION['female_education_enrolled']) ? 'Int.' : 'New' ?></div>
            <div class="stat-lbl">Level</div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="sec">
        <div class="sec-head"><svg viewBox="0 0 24 24"><path d="M12 2L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>Quick Services</div>
        <div class="quick-actions">
            <a href="<?= url('/user/services/counseling/female') ?>" class="action-card">
                <div class="action-icon"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
                <div class="action-info">
                    <h3>Request Counseling</h3>
                    <p>Speak with our dedicated Sisters for spiritual guidance.</p>
                </div>
            </a>
            <a href="#" class="action-card" onclick="alert('Module coming soon!')">
                <div class="action-icon" style="background:var(--gold);"><svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
                <div class="action-info">
                    <h3>Community Support</h3>
                    <p>Join study circles and community events.</p>
                </div>
            </a>
        </div>
    </div>

    <!-- SCHEDULE -->
    <div class="sec">
        <div class="sec-head"><svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>School Weekly Schedule</div>
        
        <div class="schedule-tabs" id="dayTabs">
            <?php
            $days = [
                'Monday' => [],
                'Tuesday' => [['Da\'wah Inquiries','8:00–12:00 PM'],['Da\'wah Inquiries','2:00–5:00 PM']],
                'Wednesday' => [['Special Class','9:00–11:30 AM'],['Special Class','2:00–4:30 PM']],
                'Thursday' => [['IDFS Staff Class','8:00–11:30 AM'],['Qur\'an & Tajweed','2:00–4:00 PM']],
                'Friday' => [['Juma\'ah Prayer','1:00 PM'],['Halaqatul Juma\'ah','2:00–4:00 PM']],
                'Saturday' => [['Beginners B4','8:00 AM–4:30 PM'],['Kids 7-9','9:00–11:00 AM']],
                'Sunday' => [['Intermediate','8:00–11:30 AM'],['Tahfidhul Qur\'an','1:00–4:30 PM']]
            ];
            foreach(array_keys($days) as $dayName):
                $isToday = ($dayName === $today);
            ?>
            <div class="day-tab <?= $isToday ? 'active' : '' ?>" onclick="switchDay('<?= $dayName ?>')">
                <div style="display:flex; align-items:center; gap:5px;">
                    <span><?= $dayName ?></span>
                    <?php if($isToday): ?>
                        <svg viewBox="0 0 24 24" style="width:12px; height:12px; fill:currentColor;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <?php endif; ?>
                </div>
                <span><?= $isToday ? 'TODAY' : 'View Schedule' ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="schedule-content" id="schedulePanes">
            <?php foreach($days as $day => $classes): ?>
            <div class="day-pane <?= $day === $today ? 'active' : '' ?>" id="pane-<?= $day ?>">
                <?php if(empty($classes)): ?>
                <div class="closed-state">
                    <div style="color: var(--m); margin-bottom: 15px;">
                        <svg viewBox="0 0 24 24" style="width:48px; height:48px; fill:currentColor;"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-3.03 0-5.5-2.47-5.5-5.5 0-1.82.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
                    </div>
                    <h3>Center Closed</h3>
                    <p>There are no scheduled activities for <?= $day ?>.</p>
                </div>
                <?php else: ?>
                    <?php foreach($classes as $c): ?>
                    <div class="timeline-item">
                        <div class="timeline-info">
                            <h4><?= $c[0] ?></h4>
                            <p>Female Education Program • IDFS Center</p>
                        </div>
                        <div class="timeline-time"><?= $c[1] ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BOTTOM INFO -->
    <div class="bottom-row">
        <div class="info-c">
            <div class="info-t">Center Office Hours</div>
            <div class="oh-r"><span class="oh-d">Monday</span><span class="oh-t" style="color:#ef4444;font-weight:900;">OFFICE CLOSED</span></div>
            <div class="oh-r"><span class="oh-d">Tuesday – Sunday</span><span class="oh-t">8:00 AM – 12:00 PM</span></div>
            <div class="oh-r"><span class="oh-d">Tuesday – Sunday</span><span class="oh-t">2:00 PM – 5:00 PM</span></div>
        </div>
        <div class="info-c">
            <div class="info-t">Official Contact Details</div>
            <div class="ct-line"><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg> Da'wah Female Section, ISCAG PH</div>
            <div class="ct-line"><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> Hotline: 0927 272 9070</div>
        </div>
    </div>
</div>

<!-- Modal for Schedule Details -->
<div id="scheduleModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title" id="modalDay">Day Schedule</div>
            <div class="close-modal" onclick="closeModal()">&times;</div>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Dynamic Content -->
        </div>
    </div>
</div>

<script>
    function switchDay(day) {
        // Update Tabs
        document.querySelectorAll('.day-tab').forEach(t => t.classList.remove('active'));
        const activeTab = Array.from(document.querySelectorAll('.day-tab')).find(t => t.innerText.includes(day));
        if (activeTab) activeTab.classList.add('active');
        
        // Update Panes
        document.querySelectorAll('.day-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('pane-' + day).classList.add('active');
    }
</script>
</div></div></div>
</body></html>
