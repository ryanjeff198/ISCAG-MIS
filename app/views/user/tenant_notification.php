<?php
// ISCAG MIS — Tenant Notifications Logic
$notifs = $notifications ?? [];
$unreadCount = 0;
foreach ($notifs as $n) {
    if (!$n['is_read']) $unreadCount++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCAG MIS — Notifications</title>
    <!-- Fonts & CSS -->
    <link rel="icon" type="image/x-icon" href="<?= asset('assets/favicon_io/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= asset('css/user-shared.css') ?>">
    
    <style>
        /* Internal overrides for notification styling while keeping the ISCAG look */
        .page-body {
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        .notif-item {
            display: flex;
            gap: 16px;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
            cursor: pointer;
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item:hover {
            background: rgba(23, 107, 69, 0.03);
        }

        .notif-item.unread {
            background: rgba(199, 154, 43, 0.05); /* Slight accent gold for unread */
        }

        .notif-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent);
        }

        .notif-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.2rem;
            background: #f0f4f2;
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .notif-icon-wrap.type-approve { background: #eef7f2; color: var(--success); border-color: #d1e7dd; }
        .notif-icon-wrap.type-alert { background: #fffcf0; color: var(--warning); border-color: #fceec5; }
        .notif-icon-wrap.type-reject { background: #fdf2f2; color: var(--danger); border-color: #f8d7da; }

        .notif-details {
            flex-grow: 1;
        }

        .notif-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
            padding-right: 24px; /* Space for the quick actions */
        }

        .notif-item-title {
            font-family: 'Lora', serif;
            font-weight: 700;
            color: var(--text-main);
            font-size: 1.05rem;
        }

        .notif-item-time {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .notif-item-msg {
            font-size: 0.9rem;
            color: #55605c;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }

        .notif-badge-unread {
            display: inline-block;
            padding: 2px 8px;
            background: var(--accent);
            color: white;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .notif-quick-actions {
            position: absolute;
            right: 24px;
            top: 20px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .notif-item:hover .notif-quick-actions {
            opacity: 1;
        }

        .btn-mark-row {
            background: white;
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-mark-row:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(23, 107, 69, 0.05);
        }

        .empty-state {
            padding: 80px 40px;
            text-align: center;
        }

        .empty-icon {
            font-size: 3.5rem;
            color: var(--border);
            margin-bottom: 16px;
        }

        .empty-text {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* ── Notif Detail Modal ── */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-content {
            background: white;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateY(20px);
            transition: all 0.3s;
        }
        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border);
            padding-bottom: 16px;
            margin-bottom: 16px;
        }
        .modal-title {
            font-family: 'Lora', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }
        .modal-time {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
            line-height: 1;
        }
        .modal-body {
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 24px;
            white-space: pre-wrap; /* Preserve line breaks */
        }

        /* SESSION DEBUG BOX - Keep hidden unless needed */
        #debug-probe {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #000;
            color: #0f0;
            padding: 10px;
            border: 2px solid #0f0;
            z-index: 99999;
            font-family: monospace;
            font-size: 11px;
            opacity: 0.1;
            transition: opacity 0.3s;
        }
        #debug-probe:hover { opacity: 1; }
    </style>
</head>
<body>

    <div class="app-wrapper">
        <!-- ═══ SIDEBAR ═══ -->
        <?php $active_page = 'notifications'; include BASE_PATH . '/app/views/user/sidebar.php'; ?>

        <!-- ═══ MAIN CONTENT ═══ -->
        <main class="main-content">
            
            <!-- TOP BAR -->
            <div class="top-bar">
                <div>
                    <div class="top-bar-title">Notifications</div>
                    <div class="top-bar-subtitle">Stay updated with your account activity and announcements</div>
                </div>
                <div class="top-bar-actions">
                    <button id="markAllRead" class="btn-topbar btn-outline-sm">Mark All as Read</button>
                    <!-- Auto-hide handled by JS -->
                </div>
            </div>

            <div id="debug-probe">
                ID: <?= $_SESSION['user_id'] ?? 'NONE' ?> | NAME: <?= $_SESSION['name'] ?? 'NONE' ?>
            </div>

            <!-- PAGE BODY -->
            <div class="page-body">
                
                <div class="section-card" style="margin-top: 24px; padding: 20px 0;">
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding:0 24px;">
                        <h3 style="margin:0; font-family:'Lora',serif; color:var(--text-main); font-size:1.15rem;">Your Feed</h3>
                        <button id="markAllReadBody" style="background:var(--primary); color:white; border:none; padding:8px 16px; border-radius:6px; font-weight:600; font-size:0.8rem; cursor:pointer; font-family:inherit; transition:0.2s; box-shadow:0 2px 6px rgba(23,107,69,0.2);">
                            Mark All as Read
                        </button>
                    </div>

                    <?php if (empty($notifs)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">🔔</div>
                            <div class="empty-text">No notifications yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="notif-list">
                            <?php foreach ($notifs as $n): ?>
                                <?php 
                                    $typeClass = '';
                                    $icon = '🔔';
                                    if ($n['type'] === 'approve' || $n['type'] === 'success') { $typeClass = 'type-approve'; $icon = 'check_circle'; }
                                    if ($n['type'] === 'alert' || $n['type'] === 'warning') { $typeClass = 'type-alert'; $icon = 'warning'; }
                                    if ($n['type'] === 'reject' || $n['type'] === 'error') { $typeClass = 'type-reject'; $icon = 'error'; }
                                    
                                    $timeStr = date('M d, Y h:i A', strtotime($n['created_at']));
                                    $is_unread = !$n['is_read'];
                                    
                                    $notifData = [
                                        'id' => $n['notification_id'],
                                        'title' => $n['title'],
                                        'message' => $n['message'],
                                        'time' => $timeStr,
                                        'is_unread' => $is_unread
                                    ];
                                ?>
                                <div class="notif-item <?= $is_unread ? 'unread' : '' ?>" id="notif-<?= $n['notification_id'] ?>" onclick="viewNotification(event, <?= htmlspecialchars(json_encode($notifData)) ?>)">
                                    <div class="notif-icon-wrap <?= $typeClass ?>">
                                        <!-- Using emojis/text in absence of icon fonts -->
                                        <?php if ($icon === 'check_circle') echo '✅'; 
                                              elseif ($icon === 'warning') echo '⚠️';
                                              elseif ($icon === 'error') echo '❌';
                                              else echo '🔔'; ?>
                                    </div>
                                    <div class="notif-details">
                                        <div class="notif-item-header">
                                            <div class="notif-item-title">
                                                <?php if($is_unread): ?>
                                                    <span class="notif-badge-unread" id="badge-<?= $n['notification_id'] ?>">New</span>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($n['title']) ?>
                                            </div>
                                            <div class="notif-item-time"><?= $timeStr ?></div>
                                        </div>
                                        <div class="notif-item-msg">
                                            <?= htmlspecialchars($n['message']) ?>
                                        </div>
                                    </div>
                                    <?php if($is_unread): ?>
                                    <div class="notif-quick-actions" id="action-<?= $n['notification_id'] ?>">
                                        <button class="btn-mark-row" onclick="quickMarkRead(event, <?= $n['notification_id'] ?>)">Mark Read</button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL DETAILS OVERLAY -->
    <div class="modal-overlay" id="notifDetailModal" onclick="closeNotifModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="mdlTitle">--</h3>
                    <div class="modal-time" id="mdlTime">--</div>
                </div>
                <button class="modal-close" onclick="closeNotifModal()">&times;</button>
            </div>
            <div class="modal-body" id="mdlBody">
                --
            </div>
            <div style="display:flex; justify-content: flex-end;">
                <button class="btn-topbar btn-outline-sm" onclick="closeNotifModal()">Close window</button>
            </div>
        </div>
    </div>

    <!-- ═══ SCRIPTS ═══ -->
    <script src="<?= asset('JS/admin-shared.js') ?>?v=<?= time() ?>"></script>
    <script>
        // Inject server-side counts
        const SERVER_NOTIFS = <?= json_encode($notifs) ?>;
        
        document.addEventListener('DOMContentLoaded', () => {
            updateSidebarBadge();

            // Set layout standardized
            if (typeof standardizePage === 'function') {
                standardizePage('tenant');
            }

            // Mark All Read Handler
            const runMarkAll = async (btn) => {
                btn.disabled = true;
                btn.textContent = 'Processing...';
                
                try {
                    const res = await fetch('<?= url("/user/notifications/mark-all-read") ?>');
                    const data = await res.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to update notifications');
                        btn.disabled = false;
                        btn.textContent = 'Mark All as Read';
                    }
                } catch (e) {
                    console.error(e);
                    btn.disabled = false;
                    btn.textContent = 'Mark All as Read';
                }
            };

            const markBtn1 = document.getElementById('markAllRead');
            const markBtn2 = document.getElementById('markAllReadBody');
            
            if (markBtn1) markBtn1.addEventListener('click', () => runMarkAll(markBtn1));
            if (markBtn2) markBtn2.addEventListener('click', () => runMarkAll(markBtn2));

            // Stale identity purge
            const mu = JSON.parse(localStorage.getItem('mis_user') || '{}');
            if (mu.name && mu.name.includes('Setsuna')) {
                localStorage.removeItem('mis_user');
                setTimeout(() => window.location.reload(), 50);
            }
        });

        function updateSidebarBadge() {
            let count = document.querySelectorAll('.notif-item.unread').length;
            const navDot = document.querySelector('.notif-dot');
            if (navDot) {
                navDot.textContent = count;
                navDot.style.display = count > 0 ? 'flex' : 'none';
            }
            
            const markBtn1 = document.getElementById('markAllRead');
            const markBtn2 = document.getElementById('markAllReadBody');
            
            if (count === 0) {
                if (markBtn1) {
                    markBtn1.disabled = true;
                    markBtn1.style.opacity = '0.5';
                    markBtn1.style.cursor = 'not-allowed';
                }
                if (markBtn2) {
                    markBtn2.disabled = true;
                    markBtn2.style.opacity = '0.5';
                    markBtn2.style.cursor = 'not-allowed';
                }
            } else {
                if (markBtn1) {
                    markBtn1.disabled = false;
                    markBtn1.style.opacity = '1';
                    markBtn1.style.cursor = 'pointer';
                }
                if (markBtn2) {
                    markBtn2.disabled = false;
                    markBtn2.style.opacity = '1';
                    markBtn2.style.cursor = 'pointer';
                }
            }
        }

        // View Notification Details in Modal
        window.viewNotification = function(event, data) {
            // Populate Modal
            document.getElementById('mdlTitle').textContent = data.title;
            document.getElementById('mdlTime').textContent = data.time;
            document.getElementById('mdlBody').textContent = data.message;
            
            // Show modal
            document.getElementById('notifDetailModal').classList.add('active');

            // Find the element row
            const row = document.getElementById('notif-' + data.id);
            
            if (row && row.classList.contains('unread')) {
                // Remove UI indicators instantly
                row.classList.remove('unread');
                if(document.getElementById('badge-' + data.id)) document.getElementById('badge-' + data.id).remove();
                if(document.getElementById('action-' + data.id)) document.getElementById('action-' + data.id).remove();
                updateSidebarBadge();
                
                // Send background fetch to mark read
                fetch('<?= url("/user/notifications") ?>?view=' + data.id)
                    .catch(err => console.error("Error marking read", err));
            }
        };

        // Quick Mark as Read using explicit button
        window.quickMarkRead = function(event, id) {
            event.stopPropagation(); // prevent opening the modal
            
            const row = document.getElementById('notif-' + id);
            if (row && row.classList.contains('unread')) {
                row.classList.remove('unread');
                if(document.getElementById('badge-' + id)) document.getElementById('badge-' + id).remove();
                if(document.getElementById('action-' + id)) document.getElementById('action-' + id).remove();
                updateSidebarBadge();
                
                // Send background fetch to mark read
                fetch('<?= url("/user/notifications") ?>?view=' + id)
                    .catch(err => console.error("Error marking read", err));
            }
        };

        // Close Modal
        window.closeNotifModal = function(e) {
            if(e) e.stopPropagation();
            document.getElementById('notifDetailModal').classList.remove('active');
        };
    </script>
</body>
</html>