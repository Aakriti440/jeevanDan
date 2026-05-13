<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tint"></i> All Blood Requests</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Blood Group</th>
                        <th>Hospital</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo $request['id']; ?></td>
                            <td><?php echo htmlspecialchars($request['patient_name']); ?></td>
                            <td><span class="blood-badge small"><?php echo $request['blood_group']; ?></span></td>
                            <td><?php echo htmlspecialchars($request['hospital_name']); ?></td>
                            <td><span class="urgency-badge <?php echo $request['urgency']; ?>"><?php echo ucfirst($request['urgency']); ?></span></td>
                            <td><span class="status-badge <?php echo $request['status']; ?>"><?php echo ucfirst($request['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/requests/details/<?php echo $request['id']; ?>" class="btn btn-sm btn-outline">
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
