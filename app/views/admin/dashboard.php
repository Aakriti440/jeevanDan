<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <a href="<?php echo APP_URL; ?>/auth/logout" class="btn btn-outline">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
</div>
        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-stats">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check"></i>
                <h3><?php echo $stats['verified_users']; ?></h3>
                <p>Verified</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-user-clock"></i>
                <h3><?php echo $stats['pending_user_verifications']; ?></h3>
                <p>Pending Users</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-hospital"></i>
                <h3><?php echo $stats['total_organizations']; ?></h3>
                <p>Organizations</p>
            </div>
            <div class="stat-card warning">
                <i class="fas fa-building"></i>
                <h3><?php echo $stats['pending_org_verifications']; ?></h3>
                <p>Pending Orgs</p>
            </div>
            <div class="stat-card danger">
                <i class="fas fa-tint"></i>
                <h3><?php echo $stats['active_requests']; ?></h3>
                <p>Active Requests</p>
            </div>
        </div>
        
        <div class="admin-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="<?php echo APP_URL; ?>/admin/verify-users" class="action-card">
                    <i class="fas fa-user-check"></i>
                    <span>Verify Users</span>
                    <?php if ($stats['pending_user_verifications'] > 0): ?>
                        <span class="badge"><?php echo $stats['pending_user_verifications']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/verify-organizations" class="action-card">
                    <i class="fas fa-building"></i>
                    <span>Verify Organizations</span>
                    <?php if ($stats['pending_org_verifications'] > 0): ?>
                        <span class="badge"><?php echo $stats['pending_org_verifications']; ?></span>
                    <?php endif; ?>
                </a>
               
                <a href="<?php echo APP_URL; ?>/admin/organizations" class="action-card">
                    <i class="fas fa-building"></i>
                    <span>Manage Organizations</span>
                </a>
                 <a href="<?php echo APP_URL; ?>/admin/users" class="action-card">
                    <i class="fas fa-users-cog"></i>
                    <span>Manage Users</span>
                </a>
                <a href="<?php echo APP_URL; ?>/admin/requests" class="action-card">
                    <i class="fas fa-tint"></i>
                    <span>Blood Requests</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
