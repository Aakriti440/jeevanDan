<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tint"></i> Blood Requests</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo APP_URL; ?>/requests/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Request
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($requests)): ?>
            <div class="requests-grid">
                <?php foreach ($requests as $request): ?>
                    <div class="request-card <?php echo $request['urgency']; ?>">
                        <div class="request-header">
                            <span class="blood-badge large"><?php echo $request['blood_group']; ?></span>
                            <span class="urgency-badge <?php echo $request['urgency']; ?>">
                                <?php echo ucfirst($request['urgency']); ?>
                            </span>
                        </div>
                        <div class="request-body">
                            <h3><?php echo htmlspecialchars($request['patient_name']); ?></h3>
                            <p><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($request['hospital_district']); ?></p>
                            <p><i class="fas fa-flask"></i> <?php echo $request['units_required']; ?> unit(s)</p>
                            <p><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($request['created_at'])); ?></p>
                        </div>
                        <div class="request-footer">
                            <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p>No active blood requests at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
