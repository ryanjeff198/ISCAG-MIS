<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Counseling Resources & Guidance</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    <style>
        :root {
            --male-accent: #14532D;
            --male-dark: #064e3b;
            --male-light: #f0fdf4;
            --gold: #D4AF37;
        }
        .resource-hero {
            background: linear-gradient(135deg, var(--male-accent), var(--male-dark));
            border-radius: 20px; padding: 40px; color: white; margin-bottom: 30px;
            position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(20, 83, 45, 0.2);
        }
        .resource-hero::after {
            content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
            background: rgba(255,255,255,0.05); border-radius: 50%;
        }
        .resource-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;
        }
        .resource-card {
            background: white; border-radius: 16px; border: 1px solid var(--border);
            padding: 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; flex-direction: column; gap: 16px; position: relative;
        }
        .resource-card:hover {
            transform: translateY(-8px); border-color: var(--male-accent);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .card-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .card-tag {
            position: absolute; top: 20px; right: 20px; font-size: 0.65rem;
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
            padding: 4px 10px; border-radius: 20px;
        }
        .guidance-list {
            margin-top: 40px; background: white; border-radius: 20px; border: 1px solid var(--border);
            overflow: hidden;
        }
        .guidance-item {
            padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex;
            align-items: flex-start; gap: 16px; transition: background 0.2s;
        }
        .guidance-item:last-child { border-bottom: none; }
        .guidance-item:hover { background: var(--male-light); }
        .guidance-number {
            width: 28px; height: 28px; background: var(--male-accent); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 800; flex-shrink: 0;
        }
        .btn-action-gold {
            background: var(--gold); color: #2d2d2d; padding: 10px 20px;
            border-radius: 8px; font-weight: 700; text-decoration: none;
            display: inline-block; font-size: 0.85rem; transition: all 0.2s;
        }
        .btn-action-gold:hover { background: #b8972f; transform: scale(1.02); }
    </style>
</head>
<body>
<div class="app-wrapper">
    <?php 
        $active_page = 'counseling_male';
        include BASE_PATH . '/app/views/user/sidebar.php'; 
    ?>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <div class="top-bar-title">Counseling & Guidance Center</div>
                <div class="top-bar-subtitle">Spiritual resources and personal growth tools for brothers</div>
            </div>
            <div class="top-bar-actions">
                <a href="<?= url('/user/services/counseling/male') ?>" class="btn-topbar">← Back to Form</a>
            </div>
        </div>

        <div class="page-body">
            <div class="breadcrumb-bar">
                <a href="<?= url('/user/dashboard') ?>">Dashboard</a>
                <span class="sep">›</span>
                <a href="<?= url('/user/services/counseling/male') ?>">Counseling</a>
                <span class="sep">›</span>
                <span class="current">Resources & Guidance</span>
            </div>

            <div class="resource-hero">
                <h2 style="font-family:'Lora',serif; font-weight:700; margin-bottom:12px;">Welcome to Your Guidance Portal</h2>
                <p style="opacity:0.9; max-width:600px; font-size:1rem; line-height:1.6;">As your counseling request has been approved, we provide these resources to help you align your personal growth with Islamic principles and spiritual wellness.</p>
                <div style="margin-top:24px; display:flex; gap:12px;">
                    <span class="badge-status success" style="background:rgba(255,255,255,0.2); color:white; border:1px solid white;">Session Status: Approved</span>
                </div>
            </div>

            <h4 style="font-family:'Lora',serif; margin-bottom:24px; color:var(--male-dark);">Knowledge Pillars</h4>
            <div class="resource-grid">
                <!-- Spiritual Resilience -->
                <div class="resource-card">
                    <span class="card-tag" style="background:#f0fdf4; color:#14532d;">Spiritual</span>
                    <div class="card-icon" style="background:#f0fdf4; color:#14532d;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Sabr & Resilience</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Discover techniques to build emotional and spiritual strength through the concept of patience (Sabr) during difficult times.</p>
                    <a href="#" class="btn-action-gold">Explore Topics</a>
                </div>

                <!-- Family & Relationships -->
                <div class="resource-card">
                    <span class="card-tag" style="background:#fff7ed; color:#c2410c;">Family</span>
                    <div class="card-icon" style="background:#fff7ed; color:#c2410c;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Family Dynamics</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Practical advice on managing marital responsibilities, parental duties, and fostering a peaceful Islamic household.</p>
                    <a href="#" class="btn-action-gold">Read Guidance</a>
                </div>

                <!-- Mental Wellness -->
                <div class="resource-card">
                    <span class="card-tag" style="background:#f5f3ff; color:#6d28d9;">Wellness</span>
                    <div class="card-icon" style="background:#f5f3ff; color:#6d28d9;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <h5 style="font-weight:700; color:#1a1a1a;">Peace of Mind</h5>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6;">Integrating Prophetic traditions with modern mindfulness to manage anxiety, anger, and spiritual burnout.</p>
                    <a href="#" class="btn-action-gold">Start Session</a>
                </div>
            </div>

            <div class="guidance-list">
                <div style="padding:24px; background:#f9fafb; border-bottom:1px solid var(--border);">
                    <h5 style="margin:0; color:var(--male-dark); font-weight:800;">Recommended Pre-Session Guidance</h5>
                    <p style="margin:4px 0 0; font-size:0.8rem; color:var(--text-muted);">Please review these points before meeting with your counselor.</p>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">1</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">State of Sincerity (Ikhlas)</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">Approach the session with a sincere intention to improve for the sake of Allah. Openness and honesty are key to a successful outcome.</p>
                    </div>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">2</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">Confidentiality Commitment</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">Remember that all discussions are protected by Islamic confidentiality. You are in a safe and supportive space.</p>
                    </div>
                </div>
                <div class="guidance-item">
                    <div class="guidance-number">3</div>
                    <div>
                        <strong style="display:block; margin-bottom:4px; font-size:0.9rem;">Action-Oriented Mindset</strong>
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5;">Counseling in Islam is not just about talking; it's about Tazkiyah (purification) and taking actionable steps towards betterment.</p>
                    </div>
                </div>
            </div>

            <div style="margin-top:30px; text-align:center; padding:30px; border-radius:20px; background:var(--male-light); border:2px dashed var(--male-accent);">
                <h6 style="color:var(--male-accent); margin-bottom:10px;">Need immediate support?</h6>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:16px;">Our counselors are available for urgent spiritual concerns during office hours.</p>
                <div style="display:flex; justify-content:center; gap:12px;">
                    <span style="font-weight:700; color:var(--male-dark);">Office: (02) 888-ISCAG</span>
                    <span style="color:var(--border);">|</span>
                    <span style="font-weight:700; color:var(--male-dark);">Emergency: 0917-DAWAH-01</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('JS/user-shared.js') ?>"></script>
</body>
</html>
