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

require_once BASE_PATH . '/app/models/CounselingRequest.php';
$reqModel = new CounselingRequest();
$history = $reqModel->getByUser($_SESSION['user_id']);
$activeRequest = null;
$hasPending = false;
$hasApproved = false;
foreach ($history as $req) {
    if ($req['status'] === 'active' || $req['status'] === 'approved') { $hasApproved = true; $activeRequest = $req; }
    if ($req['status'] === 'pending') { $hasPending = true; $activeRequest = $req; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Female Counseling Resources</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    <style>
        :root {
            --primary-female: #D4AF37;
            --primary-female-dark: #B8860B;
            --primary-female-light: #FDF4E3;
            --male-accent: #14532D; /* Consistent with MIS Green */
            --male-dark: #064e3b;
        }
        .resource-hero {
            background: linear-gradient(135deg, var(--male-accent), var(--male-dark));
            border-radius: 20px; padding: 40px; color: white; margin-bottom: 30px;
            position: relative; overflow: hidden;
        }
        .resource-hero::after {
            content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
            background: rgba(212, 175, 55, 0.1); border-radius: 50%;
        }
        .resource-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;
        }
        .resource-card {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            padding: 24px; transition: all 0.3s ease;
            display: flex; flex-direction: column; gap: 16px; position: relative;
        }
        .resource-card:hover {
            transform: translateY(-5px); border-color: var(--primary-female);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        }
        .card-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 1.5rem;
            background: var(--primary-female-light); color: var(--primary-female-dark);
        }
        .card-tag {
            position: absolute; top: 20px; right: 20px; font-size: 0.65rem;
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
            padding: 4px 10px; border-radius: 20px; background: #f8fafc; color: #64748b;
        }
        .guidance-list {
            margin-top: 40px; background: white; border-radius: 20px; border: 1px solid var(--border);
            overflow: hidden;
        }
        .guidance-item {
            padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex;
            align-items: flex-start; gap: 16px;
        }
        .guidance-item:last-child { border-bottom: none; }
        .guidance-number {
            width: 28px; height: 28px; background: var(--primary-female); color: #1a1a1a;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 800; flex-shrink: 0;
        }
        .btn-action-female {
            background: var(--primary-female); color: #1a1a1a; padding: 10px 20px;
            border-radius: 8px; font-weight: 700; text-decoration: none;
            display: inline-block; font-size: 0.85rem; transition: 0.2s;
        }
        .btn-action-female:hover { background: var(--primary-female-dark); transform: scale(1.02); }
    </style>
</head>
<body>
<div class="app-wrapper">
    <?php 
        $active_page = 'counseling_female';
        include BASE_PATH . '/app/views/user/sidebar.php'; 
    ?>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <div class="top-bar-title">Guidance Center (Female)</div>
                <div class="top-bar-subtitle">Resources and support for our sisters</div>
            </div>
            <div class="top-bar-actions">
                <a href="<?= url('/user/services/counseling/female') ?>" class="btn-topbar">
                    <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:currentColor; margin-right:4px;"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    Back to Request Form
                </a>
            </div>
        </div>

        <div class="page-body">
            <div class="breadcrumb-bar">
                <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                <span class="sep"><svg viewBox="0 0 24 24" style="width:12px; height:12px; fill:currentColor;"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></span>
                <span class="current">Guidance Center</span>
            </div>

            <div class="resource-hero">
                <h2 style="font-family:'Lora',serif; font-weight:700; margin-bottom:12px;">Assalamu Alaikum, <?= $name ?></h2>
                <p style="opacity:0.9; max-width:600px; font-size:1rem; line-height:1.6;">Our female counselors are dedicated to providing spiritual and emotional support in a safe, confidential environment.</p>
                <div style="margin-top:24px; display:flex; gap:12px;">
                    <?php if ($hasApproved): ?>
                        <span class="badge-status success" style="background:rgba(255,255,255,0.2); color:white; border:1px solid white;">Status: Session Scheduled</span>
                    <?php elseif ($hasPending): ?>
                        <span class="badge-status warning" style="background:rgba(255,255,255,0.2); color:white; border:1px solid white;">Status: Under Review</span>
                    <?php else: ?>
                        <span class="badge-status" style="background:rgba(255,255,255,0.2); color:white; border:1px solid white;">Status: No Active Request</span>
                    <?php endif; ?>
                </div>
            </div>

            <h4 style="font-family:'Lora',serif; margin-bottom:24px; color:var(--male-dark);">Guidance Resources</h4>
            <div class="resource-grid">
                <div class="resource-card">
                    <span class="card-tag">Spiritual</span>
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:currentColor;"><path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8.17,20C14.33,20 19,15.33 19,9.17C19,8.76 18.97,8.37 18.92,8H17M12,2C9.24,2 7,4.24 7,7C7,8.53 7.7,9.9 8.79,10.82C7.57,11.82 7,13.32 7,15C7,18.31 9.69,21 13,21C16.31,21 19,18.31 19,15C19,13.32 18.43,11.82 17.21,10.82C18.3,9.9 19,8.53 19,7C19,4.24 16.76,2 14,2H12Z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Inner Peace (Sakina)</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Spiritual practices to find tranquility and connection with Allah during life's trials.</p>
                    <a href="#" class="btn-action-female">Read More</a>
                </div>

                <div class="resource-card">
                    <span class="card-tag">Family</span>
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:currentColor;"><path d="M12 3L4 9v12h5v-7h6v7h5V9l-8-6z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Family Harmony</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Building strong Islamic foundations for a peaceful and loving household.</p>
                    <a href="#" class="btn-action-female">View Guide</a>
                </div>

                <div class="resource-card">
                    <span class="card-tag">Wellness</span>
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:currentColor;"><path d="M12,2L14.5,9.5L22,12L14.5,14.5L12,22L9.5,14.5L2,12L9.5,9.5L12,2Z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Self-Care in Islam</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Understanding the importance of mental and physical well-being through prophetic wisdom.</p>
                    <a href="#" class="btn-action-female">Explore</a>
                </div>
            </div>

            <div class="guidance-list">
                <div style="padding:24px; background:#f9fafb; border-bottom:1px solid var(--border);">
                    <h5 style="margin:0; color:var(--male-dark); font-weight:800;">Confidentiality & Etiquette</h5>
                    <p style="margin:4px 0 0; font-size:0.8rem; color:var(--text-muted);">Guidelines for our counseling sessions.</p>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">1</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">Strict Confidentiality</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">All discussions held during sessions are strictly private and kept between you and your female counselor.</p>
                    </div>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">2</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">Safe Space for Sisters</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">We ensure a supportive and non-judgmental environment for every sister seeking guidance.</p>
                    </div>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">3</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">Patience & Growth</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">Change takes time. We are committed to walking with you through every step of your journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
