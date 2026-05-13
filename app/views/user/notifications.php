<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<style>
.notification-item {
    position: relative;
}
.notification-delete {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--danger);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    font-size: 14px;
}
.notification-item:hover .notification-delete {
    display: flex;
}
.notification-delete:hover {
    background: #c82333;
    transform: scale(1.1);
}
</style>

<div class="dashboard">
    <div class="container">
        <div class="page-header" style="margin-top:30px;">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
            <div style="display:flex;gap:10px;">
                <button onclick="markAllRead()" class="btn btn-outline">
                    <i class="fas fa-check-double"></i> Mark All Read
                </button>
                <a href="<?php echo APP_URL; ?>/user/dashboard" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <?php if (!empty($notifications)): ?>
            <div class="notification-list" id="notificationList">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>" 
                         id="notif-<?php echo $notif['id']; ?>"
                         style="background:white;padding:20px;border-radius:10px;box-shadow:var(--shadow);margin-bottom:15px;">
                        
                        <button class="notification-delete" 
                                onclick="deleteNotification(<?php echo $notif['id']; ?>)"
                                title="Delete notification">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <div style="display:flex;gap:15px;">
                            <div class="notification-icon">
                                <i class="fas fa-<?php echo $notif['type'] === 'blood_request' ? 'tint' : ($notif['type'] === 'verification' ? 'check-circle' : 'bell'); ?>"></i>
                            </div>
                            <div class="notification-content" style="flex:1;">
                                <h4><?php echo htmlspecialchars($notif['title']); ?></h4>
                                <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                <small><i class="fas fa-clock"></i> <?php echo date('M d, Y - h:i A', strtotime($notif['created_at'])); ?></small>
                            </div>
                            <?php if ($notif['link']): ?>
                                <a href="<?php echo APP_URL . $notif['link']; ?>" class="btn btn-sm btn-primary">
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
    
    fetch('<?php echo APP_URL; ?>/user/delete-notification/' + notifId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('notif-' + notifId).style.opacity = '0';
            setTimeout(() => {
                document.getElementById('notif-' + notifId).remove();
                
                // Check if no notifications left
                if (document.querySelectorAll('.notification-item').length === 0) {
                    document.getElementById('notificationList').innerHTML = `
                        <div class="no-data" style="background:white;padding:60px;border-radius:10px;">
                            <i class="fas fa-bell-slash"></i>
                            <p>No notifications</p>
                        </div>
                    `;
                }
            }, 300);
        }
    });
}

function markAllRead() {
    fetch('<?php echo APP_URL; ?>/user/mark-all-read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            alert('All notifications marked as read!');
        }
    });
}
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
