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
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>ISCAG — My Learning & Subjects</title>
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
    
    .hero-top{display:flex;align-items:center;justify-content:space-between;position:relative;z-index:2;width: 100%;flex-wrap: wrap;gap: 20px;}
    .hero-left{display:flex;align-items:center;gap:20px;flex: 1;min-width: 300px;}
    .av{width:70px;height:70px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.6rem;color:#fff;border:4px solid rgba(255,255,255,.3);box-shadow:0 10px 20px rgba(0,0,0,.1);flex-shrink: 0;}
    .hero-name{font-size:2rem;font-weight:800;font-family:'Lora',serif;letter-spacing:-0.5px;line-height:1.2;margin:0;}
    .hero-sub{font-size:1rem;opacity:.9;margin-top:6px;font-weight: 500;}
    .hero-badge{background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);border-radius:30px;padding:10px 24px;font-size:.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;white-space: nowrap;}

    .pw{padding:0 50px 50px; max-width: 1400px; margin: 0 auto;}
    
    .sec{margin-top:50px; margin-bottom:50px;}
    .sec-head{font-family:'Lora',serif;font-size:1.5rem;font-weight:800;color:var(--g);margin-bottom:28px;display:flex;align-items:center;gap:15px;position:relative;padding-bottom:12px;}
    .sec-head::after{content:'';position:absolute;bottom:0;left:0;width:50px;height:4px;background:var(--gold);border-radius:10px;}
    .sec-head svg{width:28px;height:28px;fill:var(--g);}

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

    @media(max-width:900px){.prog-grid,.subj-grid{grid-template-columns:1fr;}}
    @media(max-width:600px){.bottom-row{grid-template-columns:1fr;}.dash-hero{padding:50px 25px 90px;}.pw{padding:0 20px 40px;}.hero-name{font-size:1.6rem;}}
</style></head><body>
<div class="app-wrapper">
<?php $active_page='female_subjects'; include BASE_PATH.'/app/views/user/sidebar.php'; ?>
<div class="main-content">
<div class="top-bar">
    <div class="top-bar-left">
        <div class="top-bar-title">My Learning Portal</div>
        <div class="top-bar-subtitle">Track your subjects and educational progress</div>
    </div>
    <div class="top-bar-actions">
        <a href="<?= url('/user/services/education/female/school') ?>" class="btn-topbar">View School Schedule</a>
    </div>
</div>

<div class="dash-hero">
    <div class="hero-top">
        <div class="hero-left">
            <div class="av"><?= $init ?></div>
            <div>
                <div class="hero-name">Academic Progress</div>
                <div class="hero-sub">Assalamu Alaikum, <?= $name ?> — Dedicated Student</div>
            </div>
        </div>
        <div class="hero-badge">Enrolled • Year 2026</div>
    </div>
</div>

<div class="pw">
    <!-- PROGRESS -->
    <div class="sec">
        <div class="sec-head"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>My Learning Progress</div>
        <div class="prog-grid">
            <div class="prog"><div class="prog-title"><span>Qur'an Reading</span> <span style="opacity:0.6; font-size:0.7rem;">Juz 3</span></div><div class="prog-level">Surah Al-Baqarah Progress</div><div class="bar-bg"><div class="bar-fill" style="width:65%;background:linear-gradient(90deg,var(--g),var(--gl));"></div></div><div class="prog-pct">65%</div></div>
            <div class="prog"><div class="prog-title"><span>Tajweed Mastery</span> <span style="opacity:0.6; font-size:0.7rem;">Phase 2</span></div><div class="prog-level">Intermediate Theory & Practice</div><div class="bar-bg"><div class="bar-fill" style="width:48%;background:linear-gradient(90deg,var(--gd),var(--gold));"></div></div><div class="prog-pct">48%</div></div>
            <div class="prog"><div class="prog-title"><span>Memorization</span> <span style="opacity:0.6; font-size:0.7rem;">Level 1</span></div><div class="prog-level">5 Short Surahs Completed</div><div class="bar-bg"><div class="bar-fill" style="width:30%;background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div></div><div class="prog-pct">30%</div></div>
        </div>
    </div>

    <!-- SUBJECTS -->
    <div class="sec">
        <div class="sec-head"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>My Enrolled Subjects</div>
        <div class="subj-grid">
            <div class="subj"><div class="subj-name">Qur'an Class</div><div class="subj-desc">Comprehensive Tajweed training and recitation practice under certified instructors.</div><div class="subj-meta"><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>Thu 2:00–4:00 PM</div><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>Sr. Rosyanti Ibrahim</div></div></div>
            <div class="subj" style="border-top-color:var(--gold);"><div class="subj-name">Islamic Studies</div><div class="subj-desc">Deep dive into Aqeedah, Fiqh of worship, and the Seerah of Prophet Muhammad (PBUH).</div><div class="subj-meta"><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>Wed 9:00–11:30 AM</div><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>IDFS Instructor</div></div></div>
            <div class="subj" style="border-top-color:#7c3aed;"><div class="subj-name">Tahfidhul Qur'an</div><div class="subj-desc">Systematic memorization program with weekly assessment and revision modules.</div><div class="subj-meta"><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>Sun 1:00–4:30 PM</div><div class="subj-row"><svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>IDFS Instructor</div></div></div>
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
</div></div></div>
</body></html>
