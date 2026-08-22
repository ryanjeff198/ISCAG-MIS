<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 5));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

$history = $history ?? [];
$hasApproved = false;
$activeRequest = null;
foreach ($history as $req) {
    if (strtolower($req['status'] ?? '') === 'approved') {
        $hasApproved = true;
        $activeRequest = $req;
        break;
    }
}
?>
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

        /* ── Active Session Card ── */
        .active-session-card {
            background: white; border-radius: 20px; border: 1.5px solid #dcfce7;
            box-shadow: 0 4px 20px rgba(22, 101, 52, 0.05); overflow: hidden;
            margin-bottom: 30px;
        }
        .session-card-header {
            background: linear-gradient(135deg, var(--male-accent), var(--male-dark));
            padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
            color: white;
        }
        .session-card-header h5 {
            margin: 0; font-family: 'Lora', serif; font-size: 1.1rem; font-weight: 700;
            display: flex; align-items: center; gap: 8px;
        }
        .session-card-header h5 svg { width: 20px; height: 20px; fill: currentColor; }
        .session-badge {
            background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4);
            color: white; font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
            padding: 4px 12px; border-radius: 20px; letter-spacing: 0.05em;
        }
        .session-card-body { padding: 24px; }
        .session-meta-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;
        }
        .session-meta-item {
            background: #f9fafb; padding: 16px; border-radius: 12px; border: 1px solid var(--border);
            display: flex; flex-direction: column; gap: 4px;
        }
        .meta-label {
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted);
            letter-spacing: 0.05em;
        }
        .meta-val { font-size: 0.95rem; font-weight: 700; color: #1f2937; }
        .meta-val.highlight { color: var(--male-accent); font-family: 'Lora', serif; }

        /* ── Weekly Calendar Card ── */
        .weekly-calendar-card {
            background: white; border-radius: 20px; border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02); overflow: hidden;
            margin-bottom: 35px;
        }
        .weekly-card-header {
            padding: 24px; border-bottom: 1px solid var(--border);
            display: flex; flex-direction: column; gap: 4px;
        }
        .weekly-table-wrap { overflow-x: auto; }
        .weekly-schedule-table {
            width: 100%; border-collapse: collapse; text-align: left;
        }
        .weekly-schedule-table th {
            background: #f9fafb; padding: 16px 24px; font-size: 0.75rem; font-weight: 800;
            text-transform: uppercase; color: var(--text-muted); border-bottom: 1.5px solid var(--border);
        }
        .weekly-schedule-table td {
            padding: 18px 24px; border-bottom: 1px solid var(--border); font-size: 0.88rem; color: #374151;
        }
        .weekly-schedule-table tr:last-child td { border-bottom: none; }
        .day-cell { font-weight: 700; color: var(--male-accent) !important; }
        .counselor-cell { display: flex; align-items: center; gap: 10px; }
        .counselor-avatar-small {
            width: 32px; height: 32px; border-radius: 50%; background: var(--male-light);
            color: var(--male-accent); display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 800; border: 1px solid #dcfce7;
        }

        /* ── Counselor Modal Styles ── */
        .counselor-modal-backdrop {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(15, 30, 22, 0.7); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; padding: 20px;
            animation: fadeIn 0.3s ease;
        }
        .counselor-modal-card {
            background: #fff; border-radius: 20px; width: 100%; max-width: 500px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.3); overflow: hidden;
            border: 1px solid rgba(0,0,0,0.08); animation: slideUp 0.35s ease;
        }
        .counselor-modal-header {
            padding: 20px 24px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .counselor-modal-close {
            background: none; border: none; font-size: 1.8rem; cursor: pointer; color: #6b7280;
            padding: 0; line-height: 1; transition: color 0.2s;
        }
        .counselor-modal-close:hover { color: var(--male-accent); }
        .counselor-modal-body { padding: 24px; }
        .counselor-modal-footer {
            padding: 16px 24px; background: #f9fafb; border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 10px;
        }
        .modal-counselor-avatar {
            width: 60px; height: 60px; border-radius: 50%; background: var(--male-light);
            color: var(--male-accent); display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; font-weight: 800; border: 2.5px solid #dcfce7;
        }
        .info-pair { display: flex; flex-direction: column; gap: 4px; }
        .info-label {
            font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted);
            letter-spacing: 0.05em;
        }
        .info-val { font-size: 0.95rem; font-weight: 600; color: #1f2937; }

        .weekly-schedule-table tbody tr {
            cursor: pointer; transition: all 0.2s;
        }
        .weekly-schedule-table tbody tr:hover {
            background: var(--male-light);
        }
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

            <!-- 🗓️ MY APPROVED SESSION SCHEDULE -->
            <?php if ($hasApproved && $activeRequest): ?>
            <div class="active-session-card">
                <div class="session-card-header">
                    <h5>
                        <svg viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/></svg>
                        My Confirmed Counseling Schedule
                    </h5>
                    <span class="session-badge">Approved</span>
                </div>
                <div class="session-card-body">
                    <div class="session-meta-grid">
                        <div class="session-meta-item">
                            <span class="meta-label">Session Date</span>
                            <span class="meta-val highlight"><?= date('l, F d, Y', strtotime($activeRequest['preferred_date'])) ?></span>
                        </div>
                        <div class="session-meta-item">
                            <span class="meta-label">Session Time</span>
                            <span class="meta-val"><?= htmlspecialchars($activeRequest['preferred_time'] ?? '10:00 AM') ?></span>
                        </div>
                        <div class="session-meta-item">
                            <span class="meta-label">Location / Room</span>
                            <span class="meta-val">Da'wah Counseling Room 204</span>
                        </div>
                        <div class="session-meta-item">
                            <span class="meta-label">Counselor in Charge</span>
                            <span class="meta-val">Ustaz Counselor</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 🗓️ WEEKLY COUNSELING SCHEDULE & AVAILABILITY CALENDAR -->
            <div class="weekly-calendar-card">
                <div class="weekly-card-header">
                    <h5 style="margin:0; font-family:'Lora',serif; color:var(--male-dark); font-weight:800; display:flex; align-items:center; gap:8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                        Weekly Counseling Availability Calendar
                    </h5>
                    <span style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">Department weekly desk schedule and counselors in charge</span>
                </div>
                <div class="weekly-table-wrap">
                    <table class="weekly-schedule-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Counselor In Charge</th>
                                <th>Available Hours</th>
                                <th>Location / Venue</th>
                                <th>Key Support Focus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr onclick="showCounselorDetail('AB', 'Ustaz Abu Bakr', 'Monday', '09:00 AM - 12:00 PM', 'Room 204 (Da\'wah Wing)', 'Faith booster, spiritual concerns', 'Ustaz Abu Bakr has over 10 years of experience helping brothers navigate modern spiritual doubts, build daily habit routines, and establish a firm connection with the Holy Qur\'an.')" title="Click to view counselor profile">
                                <td class="day-cell">Monday</td>
                                <td class="counselor-cell">
                                    <div class="counselor-avatar-small">AB</div>
                                    <span>Ustaz Abu Bakr</span>
                                </td>
                                <td>09:00 AM - 12:00 PM</td>
                                <td>Room 204 (Da'wah Wing)</td>
                                <td>Faith booster, spiritual concerns</td>
                            </tr>
                            <tr onclick="showCounselorDetail('UA', 'Ustaz Umar Al-Faruq', 'Tuesday', '01:00 PM - 04:00 PM', 'Room 204 (Da\'wah Wing)', 'Marital relations, family advice', 'Ustaz Umar Al-Faruq specializes in family dynamics, counseling married couples and offering prophetic wisdom on managing conflicts and building a peaceful, cooperative household.')" title="Click to view counselor profile">
                                <td class="day-cell">Tuesday</td>
                                <td class="counselor-cell">
                                    <div class="counselor-avatar-small">UA</div>
                                    <span>Ustaz Umar Al-Faruq</span>
                                </td>
                                <td>01:00 PM - 04:00 PM</td>
                                <td>Room 204 (Da'wah Wing)</td>
                                <td>Marital relations, family advice</td>
                            </tr>
                            <tr onclick="showCounselorDetail('KB', 'Ustaz Khalid bin Walid', 'Wednesday', '09:00 AM - 12:00 PM', 'Room 204 (Da\'wah Wing)', 'Youth development, anger management', 'Ustaz Khalid bin Walid is our youth counselor, focusing on providing actionable methods to control anger, building a resilient Muslim identity, and handling peer pressure challenges.')" title="Click to view counselor profile">
                                <td class="day-cell">Wednesday</td>
                                <td class="counselor-cell">
                                    <div class="counselor-avatar-small">KB</div>
                                    <span>Ustaz Khalid bin Walid</span>
                                </td>
                                <td>09:00 AM - 12:00 PM</td>
                                <td>Room 204 (Da'wah Wing)</td>
                                <td>Youth development, anger management</td>
                            </tr>
                            <tr onclick="showCounselorDetail('TA', 'Ustaz Tariq Aziz', 'Thursday', '01:00 PM - 04:00 PM', 'Room 204 (Da\'wah Wing)', 'New Muslim mentorship, general counseling', 'Ustaz Tariq Aziz is dedicated to welcoming and supporting brothers who have recently reverted to Islam, offering them basic foundational mentorship and guidance.')" title="Click to view counselor profile">
                                <td class="day-cell">Thursday</td>
                                <td class="counselor-cell">
                                    <div class="counselor-avatar-small">TA</div>
                                    <span>Ustaz Tariq Aziz</span>
                                </td>
                                <td>01:00 PM - 04:00 PM</td>
                                <td>Room 204 (Da'wah Wing)</td>
                                <td>New Muslim mentorship, general counseling</td>
                            </tr>
                            <tr onclick="showCounselorDetail('IM', 'Imam Da\'wah Officer', 'Friday', '02:30 PM - 04:30 PM', 'Main Prayer Hall / Room 204', 'Spiritual consult, post-Jumu\'ah questions', 'The Resident Imam is available post-Jumu\'ah prayers for quick questions, clarifications on Islamic jurisprudence, and personal prayers (dua).')" title="Click to view counselor profile">
                                <td class="day-cell">Friday</td>
                                <td class="counselor-cell">
                                    <div class="counselor-avatar-small">IM</div>
                                    <span>Imam Da'wah Officer</span>
                                </td>
                                <td>02:30 PM - 04:30 PM</td>
                                <td>Main Prayer Hall / Room 204</td>
                                <td>Spiritual consult, post-Jumu'ah questions</td>
                            </tr>
                        </tbody>
                    </table>
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

</div>

<!-- 🎟️ COUNSELOR DETAIL MODAL -->
<div id="counselor-modal" class="counselor-modal-backdrop" onclick="if(event.target===this) closeCounselorModal()">
    <div class="counselor-modal-card">
        <div class="counselor-modal-header">
            <h5 style="margin:0; font-family:'Lora',serif; color:var(--male-dark); font-weight:800;">Counselor Profile & Availability</h5>
            <button type="button" onclick="closeCounselorModal()" class="counselor-modal-close">&times;</button>
        </div>
        <div class="counselor-modal-body">
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; border-bottom:1.5px solid var(--border); padding-bottom:20px;">
                <div id="modal-counselor-avatar" class="modal-counselor-avatar">AB</div>
                <div>
                    <h4 id="modal-counselor-name" style="margin:0; font-family:'Lora',serif; font-weight:800; color:var(--male-accent); font-size:1.3rem;">Ustaz Abu Bakr</h4>
                    <span id="modal-counselor-day" style="font-size:0.72rem; font-weight:800; text-transform:uppercase; color:var(--male-accent); letter-spacing:0.05em; background:var(--male-light); border:1px solid #dcfce7; padding:4px 10px; border-radius:20px; display:inline-block; margin-top:6px;">Monday Schedule</span>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr; gap:16px;">
                <div class="info-pair">
                    <span class="info-label">Available Hours</span>
                    <span id="modal-counselor-hours" class="info-val">09:00 AM - 12:00 PM</span>
                </div>
                <div class="info-pair">
                    <span class="info-label">Counseling Location</span>
                    <span id="modal-counselor-location" class="info-val">Room 204 (Da'wah Wing)</span>
                </div>
                <div class="info-pair">
                    <span class="info-label">Specialization Focus</span>
                    <span id="modal-counselor-focus" class="info-val" style="color:var(--male-accent); font-weight:700;">Faith booster, spiritual concerns</span>
                </div>
                <div class="info-pair" style="border-top:1px solid var(--border); padding-top:16px; margin-top:8px;">
                    <span class="info-label">Counselor Profile</span>
                    <p id="modal-counselor-bio" style="font-size:0.85rem; color:#4b5563; line-height:1.6; margin:6px 0 0;">...</p>
                </div>
            </div>
        </div>
        <div class="counselor-modal-footer">
            <button type="button" onclick="closeCounselorModal()" class="btn-cancel">Close</button>
            <a href="<?= url('/user/services/counseling/male') ?>" class="btn-submit" style="text-align:center; text-decoration:none; padding:10px 20px;">Book A Session</a>
        </div>
    </div>
</div>

<script>
  function showCounselorDetail(avatar, name, day, hours, location, focus, bio) {
    document.getElementById('modal-counselor-avatar').innerText = avatar;
    document.getElementById('modal-counselor-name').innerText = name;
    document.getElementById('modal-counselor-day').innerText = day + ' Schedule';
    document.getElementById('modal-counselor-hours').innerText = hours;
    document.getElementById('modal-counselor-location').innerText = location;
    document.getElementById('modal-counselor-focus').innerText = focus;
    document.getElementById('modal-counselor-bio').innerText = bio;
    
    const modal = document.getElementById('counselor-modal');
    if (modal) modal.style.display = 'flex';
  }

  function closeCounselorModal() {
    const modal = document.getElementById('counselor-modal');
    if (modal) modal.style.display = 'none';
  }
</script>
<script src="<?= asset('JS/user-shared.js') ?>"></script>
</body>
</html>
