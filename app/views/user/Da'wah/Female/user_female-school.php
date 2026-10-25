<?php
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__, 4));
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();
require_once BASE_PATH . '/app/models/User.php';

$u = new User();
$userId = (int)($_SESSION['user_id'] ?? 0);
$d = $u->findById($userId) ?: [];
$e = $u->getAdditionalInfo($userId) ?: [];
$d = array_merge($d, $e);
$name = htmlspecialchars(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? ''));
$init = strtoupper(substr($d['first_name'] ?? 'S', 0, 1));
$today = date('l');

// Fallback retrieval of Islamic Education enrollment status if not provided by controller
if (!isset($isApproved)) {
    require_once BASE_PATH . '/app/models/IslamicEducation.php';
    $eduModel = new IslamicEducation();
    $allEnrollments = $eduModel->getByUser($userId);

    $approvedStatuses = ['active', 'accepted', 'approved', 'enrolled', 'completed'];
    $approvedEnrollments = array_values(array_filter($allEnrollments, function ($rec) use ($approvedStatuses) {
        return in_array(strtolower($rec['status'] ?? ''), $approvedStatuses);
    }));

    $isApproved = !empty($approvedEnrollments);
    $_SESSION['female_education_enrolled'] = $isApproved;

    $pendingStatuses = ['pending', 'under_review'];
    $pendingEnrollment = null;
    foreach ($allEnrollments as $rec) {
        if (in_array(strtolower($rec['status'] ?? ''), $pendingStatuses)) {
            $pendingEnrollment = $rec;
            break;
        }
    }
    $hasPending = ($pendingEnrollment !== null);

    $enrolledCourses = [];
    foreach ($approvedEnrollments as $appr) {
        $c = trim($appr['level'] ?? '');
        if (empty($c)) $c = trim($appr['program_name'] ?? '');
        if (!empty($c) && !in_array($c, $enrolledCourses)) {
            $enrolledCourses[] = $c;
        }
    }
    $latestEnrollment = $allEnrollments[0] ?? null;
}

// Master curriculum schedule mapped by day and course track
$masterSchedule = [
    'Monday' => [
        // Center officially closed on Mondays
    ],
    'Tuesday' => [
        // Administrative Da'wah consultations
    ],
    'Wednesday' => [
        [
            'title' => 'Special Class — Revert Foundations',
            'time' => '9:00 AM – 11:30 AM',
            'track' => 'Special Class — Revert Support & Essentials',
            'course_key' => 'special',
            'instructor' => 'IDFS Sisterhood Staff',
            'room' => 'Female Da\'wah Hall A'
        ],
        [
            'title' => 'Special Class — Prayer Workshop & Essentials',
            'time' => '2:00 PM – 4:30 PM',
            'track' => 'Special Class — Revert Support & Essentials',
            'course_key' => 'special',
            'instructor' => 'Sister Khadijah Dimaano',
            'room' => 'Female Da\'wah Hall A'
        ]
    ],
    'Thursday' => [
        [
            'title' => 'Qur\'an & Tajweed Recitation Practice',
            'time' => '2:00 PM – 4:00 PM',
            'track' => 'Level 1 — Basic Arabic & Tajweed',
            'course_key' => 'level_1',
            'instructor' => 'Sr. Rosyanti Ibrahim',
            'room' => 'Classroom 2'
        ]
    ],
    'Friday' => [
        [
            'title' => 'Halaqatul Juma\'ah (Intermediate Fiqh Circle)',
            'time' => '2:00 PM – 4:00 PM',
            'track' => 'Level 2 — Intermediate Islamic Studies & Fiqh',
            'course_key' => 'level_2',
            'instructor' => 'Sister Khadijah Dimaano',
            'room' => 'Female Da\'wah Hall B'
        ]
    ],
    'Saturday' => [
        [
            'title' => 'Beginners B4 — Basic Arabic & Tajweed',
            'time' => '8:00 AM – 4:30 PM',
            'track' => 'Level 1 — Basic Arabic & Tajweed',
            'course_key' => 'level_1',
            'instructor' => 'IDFS Certified Staff',
            'room' => 'Main Female Learning Center'
        ]
    ],
    'Sunday' => [
        [
            'title' => 'Intermediate Islamic Studies & Fiqh',
            'time' => '8:00 AM – 11:30 AM',
            'track' => 'Level 2 — Intermediate Islamic Studies & Fiqh',
            'course_key' => 'level_2',
            'instructor' => 'IDFS Instructor',
            'room' => 'Classroom 1'
        ],
        [
            'title' => 'Tahfidhul Qur\'an & Advanced Hadith',
            'time' => '1:00 PM – 4:30 PM',
            'track' => 'Level 3 — Tahfidhul Qur\'an & Advanced Hadith',
            'course_key' => 'level_3',
            'instructor' => 'IDFS Instructor',
            'room' => 'Classroom 1'
        ]
    ]
];

// Helper to check if a class belongs to any course the student is enrolled in
if (!function_exists('isClassEnrolledForStudent')) {
    function isClassEnrolledForStudent(array $classItem, array $enrolledCourses): bool {
        if (empty($enrolledCourses)) {
            return false;
        }
        $classKey = $classItem['course_key'] ?? '';
        $classTrack = strtolower($classItem['track'] ?? '');
        foreach ($enrolledCourses as $course) {
            $cLower = strtolower(trim($course));
            if ($classKey === 'level_1') {
                if (strpos($cLower, 'level 1') !== false || strpos($cLower, 'basic') !== false || strpos($cLower, 'tajweed') !== false) {
                    return true;
                }
            } elseif ($classKey === 'level_2') {
                if (strpos($cLower, 'level 2') !== false || strpos($cLower, 'intermediate') !== false || strpos($cLower, 'fiqh') !== false) {
                    return true;
                }
            } elseif ($classKey === 'level_3') {
                if (strpos($cLower, 'level 3') !== false || strpos($cLower, 'tahfidh') !== false || strpos($cLower, 'advanced') !== false) {
                    return true;
                }
            } elseif ($classKey === 'special') {
                if (strpos($cLower, 'special') !== false || strpos($cLower, 'revert') !== false || strpos($cLower, 'essential') !== false) {
                    return true;
                }
            }
            if (!empty($cLower) && (strpos($classTrack, $cLower) !== false || strpos($cLower, $classTrack) !== false)) {
                return true;
            }
        }
        return false;
    }
}

// Compute filtered schedule and active statistics
$userFilteredSchedule = [];
$todayClassesCount = 0;
$totalEnrolledClassesCount = 0;

if ($isApproved) {
    foreach ($masterSchedule as $day => $dayClasses) {
        $filteredDayClasses = [];
        foreach ($dayClasses as $c) {
            if (isClassEnrolledForStudent($c, $enrolledCourses)) {
                $filteredDayClasses[] = $c;
                $totalEnrolledClassesCount++;
                if ($day === $today) {
                    $todayClassesCount++;
                }
            }
        }
        $userFilteredSchedule[$day] = $filteredDayClasses;
    }
}

// Format primary level label for display badge
$primaryLevelLabel = '--';
if ($isApproved && !empty($enrolledCourses)) {
    $firstCourseLower = strtolower($enrolledCourses[0]);
    if (strpos($firstCourseLower, 'level 1') !== false) $primaryLevelLabel = 'Level 1';
    elseif (strpos($firstCourseLower, 'level 2') !== false) $primaryLevelLabel = 'Level 2';
    elseif (strpos($firstCourseLower, 'level 3') !== false) $primaryLevelLabel = 'Level 3';
    elseif (strpos($firstCourseLower, 'special') !== false || strpos($firstCourseLower, 'revert') !== false) $primaryLevelLabel = 'Special';
    else $primaryLevelLabel = 'Enrolled';
} elseif ($hasPending) {
    $primaryLevelLabel = 'Pending';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
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
    .hero-sub{font-size:1rem;opacity:.95;margin-top:6px;font-weight: 500;}
    .hero-badge{background:rgba(255,255,255,.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2);border-radius:30px;padding:10px 24px;font-size:.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;white-space: nowrap;}

    .pw{padding:0 50px 50px; max-width: 1400px; margin: 0 auto;}
    
    /* Stats (Insights) */
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
        border-radius:24px;
        padding:30px 20px;
        border:1px solid var(--b);
        box-shadow:0 15px 35px rgba(0,0,0,.05);
        transition:all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .stat::before {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
        background: var(--g); opacity: 0; transition: 0.3s;
    }
    .stat:hover{transform:translateY(-10px);box-shadow:0 25px 50px rgba(0,0,0,.1);border-color:var(--gl);}
    .stat:hover::before { opacity: 1; }
    
    .stat-icon{
        width:54px;height:54px;border-radius:16px;
        display:flex;align-items:center;justify-content:center;
        margin-bottom:18px;
        transition: 0.3s;
    }
    .stat:hover .stat-icon { transform: scale(1.1) rotate(5deg); }
    .stat-icon svg{width:26px;height:26px;fill:#fff;}
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

    /* Course Filter Banner */
    .course-filter-banner {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid rgba(27,94,32,0.18);
        padding: 18px 24px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: 0 6px 16px rgba(27,94,32,0.04);
    }
    .cf-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .cf-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--gx);
        color: var(--g);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .cf-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--t);
    }
    .cf-desc {
        font-size: 0.82rem;
        color: var(--m);
        margin-top: 2px;
    }
    .cf-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .course-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--gx);
        color: var(--g);
        font-weight: 800;
        font-size: 0.78rem;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid rgba(27,94,32,0.25);
    }

    /* Clickable Schedule Cards */
    .sched-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:15px;}
    .sd{
        background:#fff;
        border-radius:20px;
        border:1px solid var(--b);
        overflow:hidden;
        transition:all .4s ease;
        min-height:210px;
        cursor:pointer;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    .sd:hover{transform:translateY(-5px);box-shadow:0 20px 40px rgba(0,0,0,.1);z-index:5;border-color:var(--gl);}
    .sd.today{border-color:var(--gold);background:var(--gx);box-shadow:0 0 0 5px rgba(212,175,55,.15);}
    .sd.closed .sd-h{background:#ef4444;}
    .sd.friday .sd-h{background:linear-gradient(135deg,var(--gd),var(--gold));}
    .sd-h{background:var(--g);color:#fff;padding:15px;font-weight:900;font-size:.8rem;text-transform:uppercase;letter-spacing:1.5px;text-align:center;}
    .sd-b{padding:18px; flex-grow:1;}
    .sd-item{padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:.75rem;}
    .sd-item:last-child{border:none;}
    .sd-cls{font-weight:800;color:var(--t);margin-bottom:3px; line-height: 1.3;}
    .sd-time{color:var(--m);font-weight:700;font-size:0.68rem; display: flex; align-items:center; gap:5px;}
    .sd-time::before { content: '•'; color: var(--gold); font-size: 1.2rem; }
    .sd-closed-txt{text-align:center;padding:50px 10px;color:#ef4444;font-weight:900;font-size:0.95rem;letter-spacing:2px;text-transform:uppercase;opacity:0.85;}
    .sd-empty-txt{
        text-align: center;
        padding: 35px 10px;
        color: #94a3b8;
        font-weight: 600;
        font-size: 0.78rem;
        line-height: 1.4;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .sd-empty-icon{
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Approval Gate Card (When schedule is not approved / enrolled) */
    .sched-gate-card {
        background: #ffffff;
        border-radius: 28px;
        border: 1px solid var(--b);
        padding: 55px 40px;
        box-shadow: 0 20px 45px rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        text-align: center;
        max-width: 880px;
        margin: 10px auto 40px;
    }
    .sched-gate-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--gold), var(--g));
    }
    .sched-gate-card.pending::before {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }
    .sched-gate-icon-wrapper {
        width: 82px;
        height: 82px;
        border-radius: 24px;
        background: var(--gx);
        color: var(--g);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
        box-shadow: 0 10px 22px rgba(27,94,32,0.1);
    }
    .sched-gate-card.pending .sched-gate-icon-wrapper {
        background: #fef3c7;
        color: #b45309;
        box-shadow: 0 10px 22px rgba(245,158,11,0.15);
    }
    .sched-gate-badge {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .badge-pending {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-locked {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .sched-gate-title {
        font-family: 'Lora', serif;
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--t);
        margin-bottom: 14px;
    }
    .sched-gate-desc {
        font-size: 1.02rem;
        line-height: 1.7;
        color: var(--m);
        max-width: 660px;
        margin: 0 auto 30px;
    }
    .gate-details-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        max-width: 660px;
        margin: 0 auto 32px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        text-align: left;
    }
    @media (max-width: 600px) {
        .gate-details-box { grid-template-columns: 1fr; }
    }
    .gate-detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .gate-detail-lbl {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--m);
    }
    .gate-detail-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--t);
    }
    .gate-btn-row {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    .btn-gate-primary {
        background: var(--g);
        color: #ffffff;
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.92rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 8px 18px rgba(27,94,32,0.2);
        transition: all 0.3s ease;
    }
    .btn-gate-primary:hover {
        background: var(--gl);
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(27,94,32,0.3);
    }
    .btn-gate-secondary {
        background: #ffffff;
        color: var(--t);
        border: 1.5px solid var(--b);
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.92rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    .btn-gate-secondary:hover {
        background: #f8fafc;
        border-color: var(--m);
        transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(10, 40, 10, 0.7); backdrop-filter: blur(8px);
        display: none; align-items: center; justify-content: center; z-index: 1000;
        animation: fadeIn 0.2s ease;
    }
    .modal-content {
        background: #fff; width: 95%; max-width: 560px; border-radius: 30px;
        max-height: 95vh; display: flex; flex-direction: column;
        overflow: hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.3);
        transform: translateY(10px); transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .modal-header {
        background: linear-gradient(135deg, var(--g), var(--gl)); color: #fff; padding: 28px 36px;
        display: flex; align-items: center; justify-content: space-between;
        position: relative; flex-shrink: 0;
    }
    .modal-header::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px;
        background: rgba(255,255,255,0.1);
    }
    .modal-title { font-family: 'Lora', serif; font-size: 1.7rem; font-weight: 800; }
    .close-modal { cursor: pointer; font-size: 2rem; line-height: 1; opacity: 0.7; transition: 0.3s; }
    .close-modal:hover { opacity: 1; }
    
    .modal-body { padding: 35px; max-height: 100%; overflow-y: auto; flex: 1; }
    .modal-class-item {
        display: flex; gap: 18px; padding: 18px 0; border-bottom: 1px solid #f1f5f9;
        transition: 0.3s;
    }
    .modal-class-item:last-child { border: none; }
    
    .modal-time-box {
        background: var(--gx); color: var(--g); padding: 10px 14px;
        border-radius: 12px; font-weight: 900; font-size: 0.75rem; 
        min-width: 125px; text-align: center; border: 1px solid rgba(27, 94, 32, 0.15);
        align-self: flex-start;
    }
    .modal-class-info h4 { font-weight: 800; font-size: 1.05rem; color: var(--t); margin-bottom: 5px; line-height: 1.3; }
    .modal-class-info p { font-size: 0.82rem; color: var(--m); font-weight: 600; display: flex; align-items: center; gap: 5px; }

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
</style>
</head>
<body>
<div class="app-wrapper">
<?php $active_page='female_school'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>
<div class="main-content">
<div class="top-bar">
    <div class="top-bar-left">
        <div class="top-bar-title">Da'wah Female Section</div>
        <div class="top-bar-subtitle">Student Dashboard & Schedule</div>
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
                <div class="hero-sub">
                    <?php if ($isApproved): ?>
                        ISCAG Da'wah Female School — Enrolled Student (<?= htmlspecialchars(implode(', ', $enrolledCourses)) ?>)
                    <?php elseif ($hasPending): ?>
                        ISCAG Da'wah Female School — Registration Under Review
                    <?php else: ?>
                        ISCAG Da'wah Female School — Islamic Education Schedule
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="hero-badge">
            <?php if ($isApproved): ?>
                Verified • Active Enrollment
            <?php elseif ($hasPending): ?>
                Application Pending Review
            <?php else: ?>
                Registration Required
            <?php endif; ?>
            • <?= date('F Y') ?>
        </div>
    </div>
</div>

<div class="pw">
    <!-- STATS (INSIGHTS) -->
    <div class="stats">
        <div class="stat">
            <div class="stat-icon" style="background:var(--g);"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg></div>
            <div class="stat-val"><?= $isApproved ? str_pad((string)count($enrolledCourses), 2, '0', STR_PAD_LEFT) : '--' ?></div>
            <div class="stat-lbl">Enrolled Courses</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:var(--gold);"><svg viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg></div>
            <div class="stat-val"><?= $isApproved ? str_pad((string)$todayClassesCount, 2, '0', STR_PAD_LEFT) : '--' ?></div>
            <div class="stat-lbl">Today's Classes</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:#2563eb;"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            <div class="stat-val"><?= $isApproved ? 'Active' : ($hasPending ? 'Pending' : '--') ?></div>
            <div class="stat-lbl">Status</div>
        </div>
        <div class="stat">
            <div class="stat-icon" style="background:#7c3aed;"><svg viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg></div>
            <div class="stat-val"><?= htmlspecialchars($primaryLevelLabel) ?></div>
            <div class="stat-lbl">Course Track</div>
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
                    <p>Speak with our dedicated Sisters for spiritual guidance and support.</p>
                </div>
            </a>
            <?php if ($isApproved): ?>
            <a href="<?= url('/user/services/education/female/subjects') ?>" class="action-card">
                <div class="action-icon" style="background:var(--gold);"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg></div>
                <div class="action-info">
                    <h3>My Learning Portal</h3>
                    <p>Track your enrolled subjects, learning materials, and academic milestones.</p>
                </div>
            </a>
            <?php else: ?>
            <a href="<?= url('/user/services/education/female') ?>" class="action-card">
                <div class="action-icon" style="background:var(--gold);"><svg viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></div>
                <div class="action-info">
                    <h3>Islamic Education Registration</h3>
                    <p><?= $hasPending ? 'Check the status of your submitted enrollment registration.' : 'Submit your enrollment form to access the school schedule.' ?></p>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- SCHEDULE SECTION -->
    <div class="sec">
        <div class="sec-head">
            <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
            <?= $isApproved ? 'My Enrolled School Schedule' : 'School Weekly Schedule' ?>
        </div>

        <?php if ($isApproved): ?>
            <!-- Filter Banner showing only enrolled courses -->
            <div class="course-filter-banner">
                <div class="cf-left">
                    <div class="cf-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <div>
                        <div class="cf-title">Showing Enrolled Course Schedule</div>
                        <div class="cf-desc">The schedule below displays classes exclusively for your approved enrolled course(s).</div>
                    </div>
                </div>
                <div class="cf-badges">
                    <?php foreach ($enrolledCourses as $course): ?>
                        <span class="course-pill">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                            <?= htmlspecialchars($course) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Personalized Weekly Grid -->
            <div class="sched-grid">
                <?php
                foreach ($userFilteredSchedule as $day => $classes):
                    $isToday = ($day === $today);
                    $cls = $isToday ? 'sd today' : 'sd';
                    if ($day === 'Monday') $cls .= ' closed';
                    if ($day === 'Friday') $cls .= ' friday';
                    $jsonClasses = htmlspecialchars(json_encode(['day' => $day, 'classes' => $classes]));
                ?>
                <div class="<?= $cls ?>" onclick='openScheduleModal(<?= $jsonClasses ?>)'>
                    <div class="sd-h">
                        <?= substr($day, 0, 3) ?>
                        <?php if ($isToday): ?>
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="#ffffff" style="display:inline-block; vertical-align:-1px; margin-left:3px;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <?php endif; ?>
                    </div>
                    <?php if ($day === 'Monday'): ?>
                        <div class="sd-closed-txt">CLOSED</div>
                    <?php elseif (empty($classes)): ?>
                        <div class="sd-empty-txt">
                            <span class="sd-empty-icon">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                            </span>
                            No enrolled classes
                        </div>
                    <?php else: ?>
                    <div class="sd-b">
                        <?php foreach ($classes as $c): ?>
                        <div class="sd-item">
                            <div class="sd-cls"><?= htmlspecialchars($c['title'] ?? $c[0]) ?></div>
                            <div class="sd-time"><?= htmlspecialchars($c['time'] ?? $c[1]) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($hasPending): ?>
            <!-- Gated Card: Pending Admin Review -->
            <div class="sched-gate-card pending">
                <div class="sched-gate-icon-wrapper">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm3.3 14.71L11 12.41V7h2v4.59l3.71 3.71-1.42 1.41z"/></svg>
                </div>
                <div><span class="sched-gate-badge badge-pending">Registration Under Review</span></div>
                <h2 class="sched-gate-title">Class Schedule Awaiting Admin Approval</h2>
                <p class="sched-gate-desc">
                    Your Islamic Education registration form has been submitted and is currently being verified by the Da'wah Department. 
                    Once an administrator approves and enrolls your registration, your personalized weekly class schedule will appear here.
                </p>
                
                <?php if (!empty($pendingEnrollment)): ?>
                <div class="gate-details-box">
                    <div class="gate-detail-item">
                        <span class="gate-detail-lbl">Application Reference</span>
                        <span class="gate-detail-val" style="color:var(--g); font-family:monospace; font-size:1.05rem;">#EDU-<?= str_pad((string)$pendingEnrollment['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="gate-detail-item">
                        <span class="gate-detail-lbl">Selected Course Level</span>
                        <span class="gate-detail-val"><?= htmlspecialchars($pendingEnrollment['level'] ?? $pendingEnrollment['program_name'] ?? 'Level 1 — Basic Arabic & Tajweed') ?></span>
                    </div>
                    <div class="gate-detail-item">
                        <span class="gate-detail-lbl">Date Submitted</span>
                        <span class="gate-detail-val"><?= !empty($pendingEnrollment['created_at']) ? date('F d, Y', strtotime($pendingEnrollment['created_at'])) : date('F d, Y') ?></span>
                    </div>
                    <div class="gate-detail-item">
                        <span class="gate-detail-lbl">Reviewing Staff</span>
                        <span class="gate-detail-val"><?= htmlspecialchars($pendingEnrollment['approved_by'] ?? 'Sister Khadijah Dimaano') ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="gate-btn-row">
                    <a href="<?= url('/user/services/education/female#my-registrations-card') ?>" class="btn-gate-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        View My Registration Form
                    </a>
                    <a href="<?= url('/user/services/counseling/female') ?>" class="btn-gate-secondary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                        Contact Sister Khadijah
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Gated Card: Not Enrolled / Registration Required -->
            <div class="sched-gate-card">
                <div class="sched-gate-icon-wrapper">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                </div>
                <div><span class="sched-gate-badge badge-locked">Enrollment Required</span></div>
                <h2 class="sched-gate-title">Islamic Education Schedule Locked</h2>
                <p class="sched-gate-desc">
                    You are not currently enrolled in the ISCAG Da'wah Female Education program. 
                    The class schedule is personalized and displayed exclusively to enrolled and approved students. 
                    Please complete and submit the registration form to choose your course level and receive your official schedule.
                </p>

                <?php if (!empty($latestEnrollment) && strtolower($latestEnrollment['status'] ?? '') === 'rejected'): ?>
                <div class="gate-details-box" style="border-color:#fecaca; background:#fef2f2;">
                    <div class="gate-detail-item" style="grid-column: 1 / -1;">
                        <span class="gate-detail-lbl" style="color:#b91c1c;">Previous Application Notice</span>
                        <span class="gate-detail-val" style="color:#991b1b; font-weight:600;">
                            Your previous application was not accommodated. <?= !empty($latestEnrollment['rejection_reason']) ? 'Reason: ' . htmlspecialchars($latestEnrollment['rejection_reason']) : 'Please inquire with the Da\'wah department or submit a new registration.' ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>

                <div class="gate-btn-row">
                    <a href="<?= url('/user/services/education/female') ?>" class="btn-gate-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                        Complete Registration Form
                    </a>
                    <a href="<?= url('/user/services/counseling/female') ?>" class="btn-gate-secondary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                        Inquire with IDFS
                    </a>
                </div>
            </div>
        <?php endif; ?>
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
    function openScheduleModal(data) {
        const modal = document.getElementById('scheduleModal');
        const modalDay = document.getElementById('modalDay');
        const modalBody = document.getElementById('modalBody');
        
        modalDay.innerText = data.day + ' Classes';
        modalBody.innerHTML = '';
        
        if (!data.classes || data.classes.length === 0) {
            if (data.day === 'Monday') {
                modalBody.innerHTML = `
                    <div style="text-align:center; padding: 40px 20px;">
                        <div style="width:70px; height:70px; border-radius:50%; background:#fee2e2; color:#ef4444; display:inline-flex; align-items:center; justify-content:center; margin-bottom:18px;">
                            <svg viewBox="0 0 24 24" width="34" height="34" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        </div>
                        <div style="color:#ef4444; font-weight:900; font-size: 1.2rem; letter-spacing: 1px;">CENTER CLOSED</div>
                        <p style="color:#6b7280; font-size: 0.9rem; margin-top: 10px;">The ISCAG Center is officially closed every Monday for facility maintenance.</p>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div style="text-align:center; padding: 40px 20px;">
                        <div style="width:70px; height:70px; border-radius:50%; background:var(--gx); color:var(--g); display:inline-flex; align-items:center; justify-content:center; margin-bottom:18px;">
                            <svg viewBox="0 0 24 24" width="34" height="34" fill="currentColor"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
                        </div>
                        <div style="color:var(--g); font-weight:900; font-size: 1.15rem; letter-spacing: 0.5px;">NO SCHEDULED CLASS</div>
                        <p style="color:#6b7280; font-size: 0.9rem; margin-top: 10px;">You have no classes scheduled on ${data.day} for your enrolled course(s). Use this time for personal revision and spiritual reflection!</p>
                    </div>
                `;
            }
        } else {
            data.classes.forEach(cls => {
                const title = typeof cls === 'object' && cls.title ? cls.title : cls[0];
                const time = typeof cls === 'object' && cls.time ? cls.time : cls[1];
                const track = typeof cls === 'object' && cls.track ? cls.track : 'Female Education Program';
                const instructor = typeof cls === 'object' && cls.instructor ? cls.instructor : '';
                const room = typeof cls === 'object' && cls.room ? cls.room : '';

                modalBody.innerHTML += `
                    <div class="modal-class-item">
                        <div class="modal-time-box">${time}</div>
                        <div class="modal-class-info">
                            <h4>${title}</h4>
                            <p>${track}</p>
                            ${instructor ? `<div style="font-size:0.78rem; color:#4b5563; margin-top:6px; font-weight:600; display:flex; align-items:center; gap:6px;"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="flex-shrink:0;"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>Instructor: ${instructor}</div>` : ''}
                            ${room ? `<div style="font-size:0.78rem; color:#6b7280; margin-top:4px; font-weight:600; display:flex; align-items:center; gap:6px;"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="flex-shrink:0;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>Room: ${room}</div>` : ''}
                        </div>
                    </div>
                `;
            });
        }
        
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.querySelector('.modal-content').style.transform = 'translateY(0)';
        }, 10);
    }

    function closeModal() {
        document.getElementById('scheduleModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('scheduleModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
</div>
</div>
</body>
</html>
