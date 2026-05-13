<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tint"></i> Blood Requests</h1>
            <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
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
                            <p><i class="fas fa-flask"></i> <?php echo $request['units_required']; ?> unit(s) needed</p>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($request['contact_phone']); ?></p>
                            <?php if ($request['contact_email']): ?>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($request['contact_email']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="request-footer">
                            <div style="display:flex;gap:10px;">
                                <a href="tel:<?php echo $request['contact_phone']; ?>" class="btn btn-primary" style="flex:1;">
                                    <i class="fas fa-phone"></i> Call
                                </a>
                                <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-outline" style="flex:1;">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p>No active blood requests</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
