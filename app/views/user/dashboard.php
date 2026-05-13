<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        
        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- ==================== DONATION INTERVAL WARNING BANNER ==================== -->
        <?php if (!$canDonate && !empty($user['last_donation_date'])): ?>
            <?php
                $lastDon = new DateTime($user['last_donation_date']);
                $nextDate = clone $lastDon;
                $nextDate->modify('+' . DONATION_INTERVAL_DAYS . ' days');
                $daysLeft = (new DateTime())->diff($nextDate)->days;
            ?>
            <div style="display:flex;align-items:center;gap:14px;background:#fff8e1;border:1.5px solid #f9a825;border-left:5px solid #f9a825;border-radius:10px;padding:18px 22px;margin-bottom:20px;">
                <i class="fas fa-exclamation-triangle" style="font-size:2rem;color:#f9a825;flex-shrink:0;"></i>
                <div>
                    <strong>Donation Not Yet Available</strong>
                    <p style="margin:4px 0 0;">
                        Last donation: <strong><?php echo $lastDon->format('F j, Y'); ?></strong>.
                        You can donate again after <strong><?php echo $nextDate->format('F j, Y'); ?></strong>
                        (<strong><?php echo $daysLeft; ?> day(s)</strong> remaining).
                    </p>
                    <small style="color:#888;">Minimum interval: <?php echo DONATION_INTERVAL_DAYS; ?> days (~3 months)</small>
                </div>
            </div>
        <?php elseif ($canDonate && $user['is_verified']): ?>
            <div style="display:flex;align-items:center;gap:14px;background:#e8f5e9;border:1.5px solid #4caf50;border-left:5px solid #4caf50;border-radius:10px;padding:14px 22px;margin-bottom:20px;">
                <i class="fas fa-check-circle" style="font-size:1.6rem;color:#4caf50;flex-shrink:0;"></i>
                <div>
                    <strong>You are eligible to donate!</strong>
                    <p style="margin:2px 0 0;font-size:0.9rem;color:#555;">
                        <?php echo empty($user['last_donation_date']) ? 'You have never donated. Be the first to save a life!' : 'Last donation: ' . (new DateTime($user['last_donation_date']))->format('F j, Y'); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <!-- ==================================================================== -->

        <div class="dashboard-header">
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                <p>
                    <?php if ($user['is_verified']): ?>
                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Donor</span>
                    <?php else: ?>
                        <span class="pending-badge"><i class="fas fa-clock"></i> Verification Pending</span>
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="dashboard-actions">
                <a href="<?php echo APP_URL; ?>/user/profile" class="btn btn-outline">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <?php if ($user['is_verified'] && $canDonate): ?>
                    <button class="btn btn-danger" onclick="document.getElementById('donateModal').style.display='flex'">
                        <i class="fas fa-hand-holding-heart"></i> Donate Blood
                    </button>
                <?php elseif ($user['is_verified'] && !$canDonate): ?>
                    <button class="btn btn-danger" disabled title="You must wait before donating again" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-hand-holding-heart"></i> Donate Blood (Not Yet Eligible)
                    </button>
                <?php else: ?>
                    <button class="btn btn-danger" disabled title="Your account must be verified first" style="opacity:0.5;cursor:not-allowed;">
                        <i class="fas fa-hand-holding-heart"></i> Donate Blood (Verify First)
                    </button>
                <?php endif; ?>
                <a href="<?php echo APP_URL; ?>/requests/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Request Blood
                </a>
            </div>
        </div>
        
        <div class="stats-grid" style="margin-bottom:30px;">
            <div class="stat-card">
                <i class="fas fa-tint"></i>
                <h3><?php echo $user['blood_group'] ?? 'Not Set'; ?></h3>
                <p>Blood Group</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar"></i>
                <h3><?php echo $user['last_donation_date'] ? date('M d, Y', strtotime($user['last_donation_date'])) : 'Never'; ?></h3>
                <p>Last Donation</p>
            </div>
            <div class="stat-card <?php echo $canDonate ? '' : 'warning'; ?>">
                <i class="fas <?php echo $canDonate ? 'fa-check' : 'fa-clock'; ?>"></i>
                <h3><?php echo $canDonate ? 'Ready' : 'Wait'; ?></h3>
                <p>Donation Status</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <h3><?php echo $unreadCount; ?></h3>
                <p>Notifications</p>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-heartbeat"></i> People Need Your Blood</h2>
                </div>
                
                <?php if (!empty($matchingRequests)): ?>
                    <div class="request-list">
                        <?php foreach ($matchingRequests as $request): ?>
                            <div class="request-item <?php echo $request['urgency']; ?>">
                                <div class="request-blood">
                                    <span class="blood-badge"><?php echo $request['blood_group']; ?></span>
                                </div>
                                <div class="request-info">
                                    <h4><?php echo htmlspecialchars($request['patient_name']); ?></h4>
                                    <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($request['hospital_district']); ?></p>
                                </div>
                                <div class="request-action">
                                    <span class="urgency-badge <?php echo $request['urgency']; ?>"><?php echo ucfirst($request['urgency']); ?></span>
                                    <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-phone"></i> Contact
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-check-circle"></i>
                        <p>No matching requests</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-bell"></i> Notifications</h2>
                </div>
                
                <?php if (!empty($notifications)): ?>
                    <div class="notification-list">
                        <?php foreach ($notifications as $notif): ?>
                            <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="notification-content">
                                    <h4><?php echo htmlspecialchars($notif['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                    <small><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dashboard-footer">
            <a href="<?php echo APP_URL; ?>/auth/logout" class="btn btn-outline">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- ===== DONATE BLOOD MODAL ===== -->
<div id="donateModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; width:90%; max-width:500px; position:relative; max-height:90vh; overflow-y:auto;">
        <button onclick="document.getElementById('donateModal').style.display='none'" style="position:absolute; top:15px; right:15px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#666;">&times;</button>
        <h2 style="margin-bottom:20px; color:#c0392b;"><i class="fas fa-hand-holding-heart"></i> Record Blood Donation</h2>

        <form method="POST" action="<?php echo APP_URL; ?>/user/donate">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Blood Group</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['blood_group'] ?? 'Not set'); ?>" disabled style="background:#f5f5f5; padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Donation Date *</label>
                <input type="date" name="donation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Donation Location *</label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Bir Hospital, Kathmandu" required style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Units Donated</label>
                <input type="number" name="units" class="form-control" value="1" min="1" max="5" style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;">
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px; font-weight:600;">Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..." style="padding:10px; border:1px solid #ddd; border-radius:6px; width:100%;"></textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1; padding:12px; background:#c0392b; color:#fff; border:none; border-radius:6px; font-size:1rem; cursor:pointer;">
                    <i class="fas fa-save"></i> Save Donation Record
                </button>
                <button type="button" onclick="document.getElementById('donateModal').style.display='none'" style="flex:1; padding:12px; background:#6c757d; color:#fff; border:none; border-radius:6px; font-size:1rem; cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<!-- ===== END DONATE MODAL ===== -->

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>