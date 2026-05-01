<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 4));
}
require_once BASE_PATH . '/app/helpers/Auth.php';
Auth::protect();

// Mock data for demonstration - in production these will come from the database
$appId = "BUR-2024-0512";
$status = "Approved"; // This view is for Approved state
$dateSubmitted = "May 10, 2024";
$deceasedName = "Abdullah Masoud";
$dateOfDeath = "May 09, 2024";
$requesterName = $_SESSION['user_name'] ?? 'John Doe';
$relationship = "Son";
$services = ["Bathing (Ghusl)", "Shrouding (Kafn)", "Janazah Prayer", "Cemetery Plot"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Application Info — Burial Service</title>
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2f8a60;
            --primary-dark: #1e5a3e;
            --primary-light: #e6f4ed;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success-bg: #ecfdf5;
            --success-text: #065f46;
        }

        body { font-family: 'Inter', sans-serif; background: #f9fafb; color: var(--text-main); }
        .details-container { max-width: 900px; margin: 40px auto; padding: 0 20px; }

        .app-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        /* ── Header ── */
        .app-header {
            background: linear-gradient(135deg, #f0f7f4 0%, #ffffff 100%);
            padding: 40px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .app-header-left h1 { font-family: 'Lora', serif; font-size: 1.75rem; color: var(--primary-dark); margin-bottom: 8px; }
        .app-id { font-family: 'Inter'; font-weight: 700; color: var(--text-muted); font-size: 0.9rem; letter-spacing: 0.05em; }

        .status-badge {
            background: var(--success-bg);
            color: var(--success-text);
            padding: 8px 16px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #a7f3d0;
        }

        /* ── Body ── */
        .app-body { padding: 40px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        
        .info-group h4 { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 0.1em; 
            color: var(--text-muted); 
            margin-bottom: 12px;
            font-weight: 800;
        }
        .info-value { font-size: 1.1rem; font-weight: 600; color: var(--text-main); }
        .info-sub { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; }

        .services-list {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }
        .service-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #eef2f6;
            font-weight: 500;
            color: var(--text-main);
        }
        .service-item:last-child { border-bottom: none; }
        .service-item svg { width: 18px; height: 18px; fill: var(--primary); }

        /* ── Footer / Actions ── */
        .app-footer {
            background: #fdfdfd;
            padding: 32px 40px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .next-steps { max-width: 500px; }
        .next-steps h5 { font-weight: 800; color: var(--primary-dark); margin-bottom: 6px; font-size: 0.9rem; }
        .next-steps p { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; }

        .btn-print {
            padding: 12px 24px;
            background: white;
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover { background: var(--primary); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(47, 138, 96, 0.2); }

        @media print {
            .btn-print, .top-bar, .sidebar { display: none; }
            .details-container { margin: 0; padding: 0; max-width: 100%; }
            .app-card { box-shadow: none; border: 1px solid #000; }
        }

        @media (max-width: 650px) {
            .info-grid { grid-template-columns: 1fr; gap: 24px; }
            .app-header { flex-direction: column; gap: 16px; }
            .app-footer { flex-direction: column; gap: 24px; text-align: center; }
        }
    </style>
</head>
<body>

    <div class="details-container">
        <!-- Breadcrumbs -->
        <div style="margin-bottom: 24px; display: flex; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
            <a href="<?= url('/user/dashboard') ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">Dashboard</a>
            <span>/</span>
            <span>Burial Applications</span>
            <span>/</span>
            <span style="color: var(--text-main);">Info</span>
        </div>

        <div class="app-card">
            <!-- Header -->
            <header class="app-header">
                <div class="app-header-left">
                    <div class="app-id">REFERENCE: #<?= $appId ?></div>
                    <h1>Burial Service Details</h1>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Submitted on <?= $dateSubmitted ?></div>
                </div>
                <div class="status-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    <?= $status ?>
                </div>
            </header>

            <!-- Body -->
            <div class="app-body">
                <div class="info-grid">
                    <div class="info-group">
                        <h4>Deceased Person</h4>
                        <div class="info-value"><?= $deceasedName ?></div>
                        <div class="info-sub">Date of Death: <?= $dateOfDeath ?></div>
                    </div>
                    <div class="info-group">
                        <h4>Applicant Info</h4>
                        <div class="info-value"><?= $requesterName ?></div>
                        <div class="info-sub">Relationship: <?= $relationship ?></div>
                    </div>
                </div>

                <div class="info-group">
                    <h4>Approved Services</h4>
                    <div class="services-list">
                        <?php foreach($services as $service): ?>
                        <div class="service-item">
                            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <?= $service ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="app-footer">
                <div class="next-steps">
                    <h5>Coordination Required</h5>
                    <p>Please present this application info (digital or printed) to the Damayan coordinator at the ISCAG office to proceed with the scheduled burial arrangements.</p>
                </div>
                <button class="btn-print" onclick="window.print()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Print Record
                </button>
            </footer>
        </div>

        <div style="text-align: center; margin-top: 32px;">
            <a href="<?= url('/user/dashboard') ?>" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none; font-weight: 600;">← Back to My Dashboard</a>
        </div>
    </div>

</body>
</html>
