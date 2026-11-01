<?php
/**
 * ISCAG MIS — Standardized Notification Module Component
 * Shared component across User, MIS Admin, Staff Admin (Apartment, Damayan, Da'wah)
 *
 * Required/Optional Parameters:
 * @var array $notifications List of notification records
 * @var int|null $unreadCount Count of unread notifications
 * @var string $context 'user' | 'admin' | 'apartment' | 'damayan' | 'dawah'
 * @var string|null $markReadUrl Endpoint to mark a single notification as read
 * @var string|null $markAllUrl Endpoint to mark all notifications as read
 */

$rawNotifs = $notifications ?? [];
$context = $context ?? 'user';
$isUserContext = ($context === 'user');

$markReadUrl = $markReadUrl ?? ($isUserContext ? url('/user/notifications/mark-read') : url('/admin/notifications/mark-read'));
$markAllUrl = $markAllUrl ?? ($isUserContext ? url('/user/notifications/mark-all-read') : url('/admin/notifications/mark-all-read'));

// Normalization function
$normalizedNotifs = [];
$computedUnread = 0;

foreach ($rawNotifs as $n) {
    $id = (int)($n['id'] ?? $n['notification_id'] ?? 0);
    $title = trim($n['title'] ?? 'Notification');
    $message = trim($n['message'] ?? '');
    $type = strtolower(trim($n['type'] ?? 'system'));
    $actor = trim($n['actor_name'] ?? '');
    $sourceUrl = trim($n['source_url'] ?? '');
    $isRead = !empty($n['is_read']);
    $createdAt = $n['created_at'] ?? date('Y-m-d H:i:s');

    if (!$isRead) {
        $computedUnread++;
    }

    // Relative time calculation
    $ts = strtotime($createdAt);
    $diff = time() - $ts;
    if ($diff < 60) {
        $timeAgo = 'Just now';
    } elseif ($diff < 3600) {
        $m = floor($diff / 60);
        $timeAgo = $m . 'm ago';
    } elseif ($diff < 86400) {
        $h = floor($diff / 3600);
        $timeAgo = $h . 'h ago';
    } elseif ($diff < 172800) {
        $timeAgo = 'Yesterday';
    } else {
        $timeAgo = date('M j, Y', $ts);
    }
    $fullDate = date('M j, Y — g:i A', $ts);

    $normalizedNotifs[] = [
        'id' => $id,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'actor' => $actor,
        'source_url' => $sourceUrl,
        'is_read' => $isRead,
        'created_at' => $createdAt,
        'time_ago' => $timeAgo,
        'full_date' => $fullDate,
    ];
}

$unreadCount = isset($unreadCount) ? (int)$unreadCount : $computedUnread;
$totalCount = count($normalizedNotifs);

// Helper for type icons
if (!function_exists('getNotifModuleIconSvg')) {
    function getNotifModuleIconSvg(string $type): string {
        switch ($type) {
            case 'approve':
            case 'approval':
            case 'success':
            case 'verified':
                return '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>';
            case 'alert':
            case 'warning':
                return '<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>';
            case 'reject':
            case 'error':
            case 'danger':
                return '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>';
            case 'payment':
            case 'finance':
            case 'billing':
                return '<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>';
            case 'apartment':
            case 'apartment_application':
            case 'parking_application':
            case 'contract_renewal':
            case 'moveout_request':
            case 'maintenance':
                return '<path d="M19 9.3V4h-3v2.6L12 3 2 12h3v8h5v-6h4v6h5v-8h3l-3-2.7zm-9 .7c0-1.1.9-2 2-2s2 .9 2 2h-4z"/>';
            case 'damayan':
            case 'burial':
            case 'funeral':
            case 'charity':
            case 'cemetery':
            case 'death_certificate':
                return '<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>';
            case 'dawah':
            case 'counseling':
            case 'male_counseling':
            case 'female_counseling':
            case 'education':
            case 'female_education':
            case 'marriage':
            case 'conversion':
                return '<path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>';
            case 'system':
            case 'broadcast':
            case 'general':
            default:
                return '<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>';
        }
    }
}
?>

<div class="notif-module-container" id="notif-module-root">

    <!-- ═══ TOOLBAR & FILTER CONTROLS ═══ -->
    <div class="notif-toolbar">
        <div class="notif-filters-group">
            <button type="button" class="notif-filter-tab active" id="tab-notif-all" onclick="filterNotifTab('all')">
                All Notifications
                <span class="notif-filter-count" id="count-notif-all"><?= $totalCount ?></span>
            </button>
            <button type="button" class="notif-filter-tab" id="tab-notif-unread" onclick="filterNotifTab('unread')">
                Unread
                <span class="notif-filter-count <?= $unreadCount > 0 ? 'badge-danger' : '' ?>" id="count-notif-unread"><?= $unreadCount ?></span>
            </button>
        </div>

        <div class="notif-actions-group">
            <div class="notif-search-wrap">
                <svg class="notif-search-icon" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="notif-search-box" class="notif-search-input" placeholder="Search notifications..." oninput="handleNotifSearch(this.value)">
            </div>
            <button type="button" class="btn-notif-markall" id="btn-mark-all-notifs" onclick="executeMarkAllNotifs()" <?= $unreadCount === 0 ? 'disabled' : '' ?>>
                <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                Mark All Read
            </button>
        </div>
    </div>

    <!-- ═══ NOTIFICATION FEED ═══ -->
    <div class="notif-feed" id="notif-feed-list">
        <?php if (empty($normalizedNotifs)): ?>
            <div class="notif-empty-state" id="notif-global-empty">
                <div class="notif-empty-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                </div>
                <h4 class="notif-empty-title">All Caught Up</h4>
                <p class="notif-empty-desc">You don't have any notifications right now. System updates, requests, and activity alerts will appear here.</p>
            </div>
        <?php else: ?>
            <?php foreach ($normalizedNotifs as $notif): 
                $cardClass = 'notif-card' . ($notif['is_read'] ? '' : ' unread');
                $typeClass = 'type-' . htmlspecialchars($notif['type']);
                $searchIndex = htmlspecialchars(strtolower($notif['title'] . ' ' . $notif['message'] . ' ' . $notif['actor'] . ' ' . $notif['type']));
            ?>
                <div class="<?= $cardClass ?>" 
                     id="notif-item-<?= $notif['id'] ?>"
                     data-id="<?= $notif['id'] ?>"
                     data-read="<?= $notif['is_read'] ? '1' : '0' ?>"
                     data-search="<?= $searchIndex ?>"
                     onclick="handleNotifClick(event, <?= htmlspecialchars(json_encode($notif), ENT_QUOTES, 'UTF-8') ?>)">
                    
                    <div class="notif-icon-box <?= $typeClass ?>">
                        <svg viewBox="0 0 24 24"><?= getNotifModuleIconSvg($notif['type']) ?></svg>
                    </div>

                    <div class="notif-details">
                        <div class="notif-header-row">
                            <h4 class="notif-title">
                                <?php if (!$notif['is_read']): ?>
                                    <span class="notif-badge-new" id="badge-new-<?= $notif['id'] ?>">New</span>
                                <?php endif; ?>
                                <?= htmlspecialchars($notif['title']) ?>
                            </h4>
                            <span class="notif-timestamp" title="<?= htmlspecialchars($notif['full_date']) ?>">
                                <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                                <?= htmlspecialchars($notif['time_ago']) ?>
                            </span>
                        </div>

                        <?php if (!empty($notif['actor']) || (!empty($notif['type']) && $notif['type'] !== 'system')): ?>
                            <div class="notif-meta-row">
                                <?php if (!empty($notif['actor'])): ?>
                                    <span class="notif-meta-tag">Sender: <?= htmlspecialchars($notif['actor']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($notif['type']) && $notif['type'] !== 'system'): ?>
                                    <span class="notif-meta-tag"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $notif['type']))) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <p class="notif-message-preview">
                            <?= htmlspecialchars($notif['message']) ?>
                        </p>
                    </div>

                    <?php if (!$notif['is_read']): ?>
                        <div class="notif-quick-actions" id="quick-action-<?= $notif['id'] ?>">
                            <button type="button" class="btn-quick-mark" onclick="handleQuickMark(event, <?= $notif['id'] ?>)">
                                <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:currentColor;"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Mark Read
                            </button>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Empty search/filter container (hidden by default) -->
        <div class="notif-empty-state" id="notif-filter-empty" style="display:none;">
            <div class="notif-empty-icon">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            </div>
            <h4 class="notif-empty-title">No Matching Notifications</h4>
            <p class="notif-empty-desc">No notifications matched your active filter or search keywords. Try clearing search or switching filter tabs.</p>
        </div>
    </div>

</div>

<!-- ═══ STANDARDIZED NOTIFICATION DETAIL MODAL ═══ -->
<div class="notif-modal-overlay" id="notif-detail-modal" onclick="closeNotifDetailModal(event)">
    <div class="notif-modal-dialog" onclick="event.stopPropagation()">
        <div class="notif-modal-topstrip"></div>
        <div class="notif-modal-header">
            <div class="notif-modal-headline">
                <div class="notif-icon-box" id="mdl-icon-box">
                    <svg viewBox="0 0 24 24" id="mdl-icon-svg"></svg>
                </div>
                <div>
                    <h3 class="notif-modal-title" id="mdl-notif-title">--</h3>
                    <div class="notif-modal-time" id="mdl-notif-time">--</div>
                </div>
            </div>
            <button type="button" class="notif-modal-close" onclick="closeNotifDetailModal()" aria-label="Close dialog">&times;</button>
        </div>

        <div class="notif-modal-meta-strip">
            <span class="notif-modal-meta-pill" id="mdl-status-pill">Status: Read</span>
            <span class="notif-meta-tag" id="mdl-type-tag">General</span>
            <span class="notif-meta-tag" id="mdl-sender-tag" style="display:none;"></span>
        </div>

        <div class="notif-modal-body" id="mdl-notif-body">
            --
        </div>

        <div class="notif-modal-footer">
            <button type="button" class="btn-modal-close" onclick="closeNotifDetailModal()">Close</button>
            <a href="#" class="btn-modal-action" id="mdl-action-link" style="display:none;">
                <span>View Related Record</span>
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
            </a>
        </div>
    </div>
</div>

<!-- ═══ NOTIFICATION SCRIPT CONTROLLER ═══ -->
<script>
(function() {
    let currentFilter = 'all'; // 'all' | 'unread'
    let searchQuery = '';
    const MARK_READ_ENDPOINT = '<?= $markReadUrl ?>';
    const MARK_ALL_ENDPOINT  = '<?= $markAllUrl ?>';
    const IS_USER_CONTEXT    = <?= $isUserContext ? 'true' : 'false' ?>;

    // Filter by Tab
    window.filterNotifTab = function(tab) {
        currentFilter = tab;
        document.getElementById('tab-notif-all').classList.toggle('active', tab === 'all');
        document.getElementById('tab-notif-unread').classList.toggle('active', tab === 'unread');
        applyNotifFilters();
    };

    // Real-time Search Handler
    window.handleNotifSearch = function(val) {
        searchQuery = (val || '').trim().toLowerCase();
        applyNotifFilters();
    };

    function applyNotifFilters() {
        const cards = document.querySelectorAll('.notif-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const isRead = card.getAttribute('data-read') === '1';
            const searchIndex = card.getAttribute('data-search') || '';

            // Check Tab condition
            let passesTab = true;
            if (currentFilter === 'unread' && isRead) {
                passesTab = false;
            }

            // Check Search condition
            let passesSearch = true;
            if (searchQuery.length > 0 && !searchIndex.includes(searchQuery)) {
                passesSearch = false;
            }

            if (passesTab && passesSearch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide filter empty state if there are cards in DOM
        const emptyState = document.getElementById('notif-filter-empty');
        if (emptyState) {
            if (cards.length > 0 && visibleCount === 0) {
                emptyState.style.display = 'flex';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }

    // Open detail modal and mark as read
    window.handleNotifClick = function(event, notif) {
        // Populate modal data
        document.getElementById('mdl-notif-title').textContent = notif.title;
        document.getElementById('mdl-notif-time').textContent = notif.full_date || notif.time_ago;
        document.getElementById('mdl-notif-body').textContent = notif.message;

        // Type tag
        const formattedType = (notif.type || 'system').replace(/_/g, ' ');
        document.getElementById('mdl-type-tag').textContent = formattedType.toUpperCase();

        // Sender tag
        const senderTag = document.getElementById('mdl-sender-tag');
        if (notif.actor) {
            senderTag.textContent = 'From: ' + notif.actor;
            senderTag.style.display = 'inline-flex';
        } else {
            senderTag.style.display = 'none';
        }

        // Action Link
        const actionBtn = document.getElementById('mdl-action-link');
        if (notif.source_url) {
            actionBtn.href = notif.source_url;
            actionBtn.style.display = 'inline-flex';
        } else {
            actionBtn.style.display = 'none';
        }

        // Icon Box class & SVG
        const iconBox = document.getElementById('mdl-icon-box');
        iconBox.className = 'notif-icon-box type-' + notif.type;
        const sourceCard = document.getElementById('notif-item-' + notif.id);
        if (sourceCard) {
            const cardSvg = sourceCard.querySelector('.notif-icon-box svg');
            if (cardSvg) {
                document.getElementById('mdl-icon-svg').innerHTML = cardSvg.innerHTML;
            }
        }

        // Status Pill
        const statusPill = document.getElementById('mdl-status-pill');
        statusPill.className = 'notif-modal-meta-pill pill-status-read';
        statusPill.textContent = 'Status: Read';

        // Open Modal
        document.getElementById('notif-detail-modal').classList.add('active');

        // Mark as read in UI & background API
        markSingleReadLocally(notif.id);
    };

    // Quick Mark Read Button (without opening modal)
    window.handleQuickMark = function(event, id) {
        event.stopPropagation();
        markSingleReadLocally(id);
    };

    function markSingleReadLocally(id) {
        const card = document.getElementById('notif-item-' + id);
        if (!card || card.getAttribute('data-read') === '1') return;

        // Visual update on card
        card.classList.remove('unread');
        card.setAttribute('data-read', '1');

        const badge = document.getElementById('badge-new-' + id);
        if (badge) badge.remove();

        const quickAction = document.getElementById('quick-action-' + id);
        if (quickAction) quickAction.remove();

        // Update counts
        decrementUnreadCounters();

        // If in 'unread' tab, re-apply filter smoothly
        if (currentFilter === 'unread') {
            card.style.display = 'none';
            applyNotifFilters();
        }

        // Background server call
        const payload = JSON.stringify({ id: id });
        fetch(MARK_READ_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: payload
        }).catch(err => console.warn('Mark read background sync error:', err));
    }

    // Mark All Notifications as Read
    window.executeMarkAllNotifs = async function() {
        const btn = document.getElementById('btn-mark-all-notifs');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Updating...';
        }

        // Update all cards in UI instantly
        document.querySelectorAll('.notif-card.unread').forEach(card => {
            card.classList.remove('unread');
            card.setAttribute('data-read', '1');
            const b = card.querySelector('.notif-badge-new');
            if (b) b.remove();
            const qa = card.querySelector('.notif-quick-actions');
            if (qa) qa.remove();
        });

        // Set counts to 0
        setUnreadCounterToZero();

        if (currentFilter === 'unread') {
            applyNotifFilters();
        }

        // Background Server Call
        try {
            await fetch(MARK_ALL_ENDPOINT, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
            });
        } catch (e) {
            console.warn('Mark all background sync error:', e);
        }

        if (btn) {
            btn.innerHTML = `<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> All Read`;
            btn.disabled = true;
        }
    };

    function decrementUnreadCounters() {
        const unreadPill = document.getElementById('count-notif-unread');
        if (unreadPill) {
            let current = parseInt(unreadPill.textContent || '0', 10);
            let updated = Math.max(0, current - 1);
            unreadPill.textContent = updated;
            if (updated === 0) {
                unreadPill.classList.remove('badge-danger');
                const markAllBtn = document.getElementById('btn-mark-all-notifs');
                if (markAllBtn) markAllBtn.disabled = true;
            }
            updateSidebarAndHeaderBadges(updated);
        }
    }

    function setUnreadCounterToZero() {
        const unreadPill = document.getElementById('count-notif-unread');
        if (unreadPill) {
            unreadPill.textContent = '0';
            unreadPill.classList.remove('badge-danger');
        }
        updateSidebarAndHeaderBadges(0);
    }

    function updateSidebarAndHeaderBadges(count) {
        // Update all sidebar .notif-dot indicators
        document.querySelectorAll('.notif-dot, #user-notif-dot, #sidebar-notif-badge').forEach(dot => {
            if (count > 0) {
                dot.textContent = count > 99 ? '99+' : count;
                dot.style.display = 'flex';
            } else {
                dot.style.display = 'none';
            }
        });

        // Update header badges if present
        document.querySelectorAll('#unread-badge, #stat-unread-val').forEach(badge => {
            if (count > 0) {
                badge.textContent = count + (badge.id === 'unread-badge' ? ' New' : '');
                badge.style.display = 'inline-block';
            } else {
                badge.textContent = '0';
                if (badge.id === 'unread-badge') badge.style.display = 'none';
            }
        });

        // Trigger shared helper if available
        if (typeof updateAllAdminNotifDots === 'function') {
            updateAllAdminNotifDots(count);
        }
    }

    // Modal Close handlers
    window.closeNotifDetailModal = function(e) {
        if (e) e.stopPropagation();
        const modal = document.getElementById('notif-detail-modal');
        if (modal) modal.classList.remove('active');
    };

    // Close on Escape Key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotifDetailModal();
        }
    });

})();
</script>
