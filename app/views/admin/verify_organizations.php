<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Verify Organizations</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($orgs)): ?>
            <div class="verification-list">
                <?php foreach ($orgs as $org): ?>
                    <div class="form-section"> 
                        <h3><?php echo htmlspecialchars($org['full_name']); ?></h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Email</label>
                                <p><?php echo htmlspecialchars($org['email']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Organization</label>
                                <p><?php echo htmlspecialchars($org['organization_name'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Org ID</label>
                                <p><?php echo htmlspecialchars($org['organization_id'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <p><?php echo ucfirst($org['organization_type'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                        
                        <?php if ($org['organization_id_document']): ?>
                            <div style="margin:15px 0;">
                                <strong>Uploaded ID:</strong><br>
                                <img src="<?php echo APP_URL; ?>/../app/uploads/id_cards/<?php echo $org['organization_id_document']; ?>" 
                                     style="max-width:300px;margin-top:10px;border-radius:8px;cursor:pointer;"
                                     onclick="window.open(this.src)">
                            </div>
                        <?php endif; ?>
                        
                        <div style="display:flex;gap:15px;margin-top:20px;align-items:flex-start;flex-wrap:wrap;">
                            <a href="<?php echo APP_URL; ?>/admin/view-organization/<?php echo $org['id']; ?>" class="btn btn-outline">
                                <i class="fas fa-eye"></i> View Full Details
                            </a>
                            
                            <a href="<?php echo APP_URL; ?>/admin/approve-organization/<?php echo $org['id']; ?>" 
                               class="btn btn-success"
                               onclick="return confirm('Approve this organization?')">
                                <i class="fas fa-check"></i> Approve
                            </a>
                            
                            <form method="POST" 
                                  action="<?php echo APP_URL; ?>/admin/reject-organization/<?php echo $org['id']; ?>"
                                  style="display:flex;gap:10px;flex:1;min-width:350px;">
                                <input type="hidden" name="org_id" value="<?php echo $org['id']; ?>">
                                <input type="text" 
                                       name="reason" 
                                       class="form-control" 
                                       placeholder="Rejection reason (sent as notification)..." 
                                       required
                                       style="flex:1;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject?')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-check-circle"></i>
                <p>No pending verifications</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
