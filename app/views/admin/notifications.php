<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
.notification-item { position: relative; }
.notification-delete {
    position: absolute;
    top: 15px; right: 15px;
    background: var(--danger, #c0392b);
    color: white;
    width: 30px; height: 30px;
    border-radius: 50%;
    display: none;
    align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
}
.notification-item:hover .notification-delete { display: flex; }
.notification-delete:hover { background: #a93226; transform: scale(1.1); }
</style>

<div class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-bell"></i> Admin Notifications
                <?php if (!empty($unreadCount)): ?>
                    <span class="badge" style="background:#c0392b;color:#fff;font-size:0.75rem;padding:4px 10px;border-radius:20px;vertical-align:middle;"><?php echo $unreadCount; ?> unread</span>
                <?php endif; ?>
            </h1>
            <div style="display:flex;gap:10px;">
                <button onclick="markAllRead()" class="btn btn-outline">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
                <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if (!empty($notifications)): ?>
            <div class="notification-list" id="notificationList">
                <?php foreach ($notifications as $notif): ?>
                    <?php
                        $icon = 'bell';
                        if ($notif['type'] === 'blood_request' || $notif['type'] === 'new_request') $icon = 'tint';
                        elseif ($notif['type'] === 'verification') $icon = 'check-circle';
                        elseif ($notif['type'] === 'donation') $icon = 'hand-holding-heart';
                        elseif ($notif['type'] === 'appointment') $icon = 'calendar-check';
                    ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>"
                         id="notif-<?php echo $notif['id']; ?>"
                         style="background:white;padding:20px;border-radius:10px;box-shadow:var(--shadow);margin-bottom:15px;">

                        <button class="notification-delete"
                                onclick="deleteNotification(<?php echo $notif['id']; ?>)"
                                title="Delete notification">
                            <i class="fas fa-times"></i>
                        </button>

                        <div style="display:flex;gap:15px;padding-right:40px;">
                            <div class="notification-icon">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div class="notification-content" style="flex:1;">
                                <h4><?php echo htmlspecialchars($notif['title']); ?></h4>
                                <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                <small><i class="fas fa-clock"></i> <?php echo date('M d, Y - h:i A', strtotime($notif['created_at'])); ?></small>
                            </div>
                            <?php if ($notif['link']): ?>
                                <a href="<?php echo APP_URL . $notif['link']; ?>" class="btn btn-sm btn-primary" style="align-self:center;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data" style="background:white;padding:60px;border-radius:10px;box-shadow:var(--shadow);">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function deleteNotification(notifId) {
    if (!confirm('Delete this notification?')) return;
    fetch('<?php echo APP_URL; ?>/admin/delete-notification/' + notifId, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                var el = document.getElementById('notif-' + notifId);
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    el.remove();
                    if (!document.querySelector('.notification-item')) {
                        document.getElementById('notificationList').innerHTML =
                            '<div class="no-data" style="background:white;padding:60px;border-radius:10px;"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
                    }
                }, 300);
            }
        });
}

function markAllRead() {
    fetch('<?php echo APP_URL; ?>/admin/mark-all-read', { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item.unread').forEach(el => el.classList.remove('unread'));
                // Update header badge
                var badge = document.querySelector('.admin-header .badge');
                if (badge) badge.remove();
            }
        });
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
