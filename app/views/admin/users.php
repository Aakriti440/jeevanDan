<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-users"></i> All Users</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Blood Group</th>
                        <th>District</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['blood_group'] ? '<span class="blood-badge small">' . $user['blood_group'] . '</span>' : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($user['district'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($user['is_verified']): ?>
                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                <?php else: ?>
                                    <span class="pending-badge"><i class="fas fa-clock"></i> <?php echo ucfirst($user['verification_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/admin/view-user/<?php echo $user['id']; ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
