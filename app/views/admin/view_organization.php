<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Organization Details</h1>
            <a href="<?php echo APP_URL; ?>/admin/organizations" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="form-section">
            <h2><i class="fas fa-user"></i> Contact Person</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <p><?php echo htmlspecialchars($org['full_name']); ?></p>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <p><?php echo htmlspecialchars($org['email']); ?></p>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <p><?php echo htmlspecialchars($org['phone'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>Position</label>
                    <p><?php echo htmlspecialchars($org['position'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2><i class="fas fa-hospital"></i> Organization Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Organization Name</label>
                    <p><?php echo htmlspecialchars($org['organization_name'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>Organization ID</label>
                    <p><?php echo htmlspecialchars($org['organization_id'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <p><?php echo ucfirst(str_replace('_', ' ', $org['organization_type'] ?? 'N/A')); ?></p>
                </div>
                <div class="form-group">
                    <label>District</label>
                    <p><?php echo htmlspecialchars($org['district'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <?php if (!empty($org['organization_id_document'])): ?>
            <div class="form-section">
                <h2><i class="fas fa-id-card"></i> Uploaded ID Document</h2>
                <div class="upload-box" style="max-width:500px;">
                    <a href="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $org['organization_id_document']; ?>" target="_blank">
                        <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $org['organization_id_document']; ?>" 
                             alt="Org ID" 
                             style="max-width:100%;cursor:pointer;border-radius:10px;">
                    </a>
                    <small style="display:block;margin-top:10px;">Click to view full size</small>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h2><i class="fas fa-gavel"></i> Actions</h2>
            <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start;">
                <a href="<?php echo APP_URL; ?>/admin/approve-organization/<?php echo $org['id']; ?>" 
                   class="btn btn-success btn-lg" 
                   onclick="return confirm('Approve this organization?')">
                    <i class="fas fa-check"></i> Approve
                </a>

                <form method="POST" 
                      action="<?php echo APP_URL; ?>/admin/reject-organization/<?php echo $org['id']; ?>" 
                      style="display:flex;gap:10px;flex:1;flex-direction:column;">
                    <input type="hidden" name="org_id" value="<?php echo $org['id']; ?>">
                    <textarea name="reason" class="form-control" rows="3" placeholder="Rejection reason..." required></textarea>
                    <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Reject?')">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
