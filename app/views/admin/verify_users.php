<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user-check"></i> Verify Users</h1>
            <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($users)): ?>
            <div class="verification-list">
                <?php foreach ($users as $user): ?>
                    <div class="form-section" style="margin-bottom:20px;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:20px;">
                            
                            <!-- User Info -->
                            <div style="flex:1;min-width:300px;">
                                <h3 style="margin-bottom:10px;">
                                    <?php echo htmlspecialchars($user['full_name']); ?>
                                    <?php if ($user['blood_group']): ?>
                                        <span class="blood-badge small"><?php echo $user['blood_group']; ?></span>
                                    <?php endif; ?>
                                </h3>
                                <p style="color:var(--secondary);margin-bottom:5px;">
                                    <i class="fas fa-envelope" style="width:20px;"></i> <?php echo htmlspecialchars($user['email']); ?>
                                </p>
                                <p style="color:var(--secondary);margin-bottom:5px;">
                                    <i class="fas fa-phone" style="width:20px;"></i> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?>
                                </p>
                                <p style="color:var(--secondary);margin-bottom:5px;">
                                    <i class="fas fa-map-marker-alt" style="width:20px;"></i> <?php echo htmlspecialchars(($user['district'] ?? 'N/A') . ', ' . ($user['municipality'] ?? '')); ?>
                                </p>
                                <p style="color:var(--secondary);margin-bottom:5px;">
                                    <i class="fas fa-clock" style="width:20px;"></i> Applied: <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                </p>
                            </div>
                            
                            <!-- Document Previews -->
                            <div style="display:flex;gap:10px;">
                                <?php if ($user['citizenship_front']): ?>
                                    <div style="text-align:center;">
                                        <small>Front</small><br>
                                        <img src="<?php echo APP_URL; ?>/../app/uploads/id_cards/<?php echo $user['citizenship_front']; ?>" 
                                             alt="Front" 
                                             style="width:120px;height:80px;object-fit:cover;border-radius:5px;border:2px solid #ddd;cursor:pointer;"
                                             onclick="window.open(this.src)">
                                    </div>
                                <?php endif; ?>
                                <?php if ($user['citizenship_back']): ?>
                                    <div style="text-align:center;">
                                        <small>Back</small><br>
                                        <img src="<?php echo APP_URL; ?>/../app/uploads/id_cards/<?php echo $user['citizenship_back']; ?>" 
                                             alt="Back" 
                                             style="width:120px;height:80px;object-fit:cover;border-radius:5px;border:2px solid #ddd;cursor:pointer;"
                                             onclick="window.open(this.src)">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div style="display:flex;gap:15px;margin-top:20px;padding-top:15px;border-top:1px solid #eee;align-items:flex-start;flex-wrap:wrap;">
                            
                            <!-- View Details -->
                            <a href="<?php echo APP_URL; ?>/admin/view-user/<?php echo $user['id']; ?>" class="btn btn-outline">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            
                            <!-- Approve -->
                            <a href="<?php echo APP_URL; ?>/admin/approve-user/<?php echo $user['id']; ?>" 
                               class="btn btn-success"
                               onclick="return confirm('Approve <?php echo htmlspecialchars($user['full_name']); ?>?')">
                                <i class="fas fa-check"></i> Approve
                            </a>
                            
                            <!-- Reject with Reason -->
                            <form method="POST" 
                                  action="<?php echo APP_URL; ?>/admin/reject-user/<?php echo $user['id']; ?>"
                                  style="display:flex;gap:10px;flex:1;min-width:300px;"
                                  onsubmit="return confirm('Reject <?php echo htmlspecialchars($user['full_name']); ?>?')">
                                
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                
                                <input type="text" 
                                       name="reason" 
                                       class="form-control" 
                                       placeholder="Rejection reason (sent to user)..." 
                                       required
                                       style="flex:1;">
                                
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data" style="background:white;padding:60px;border-radius:10px;box-shadow:var(--shadow);">
                <i class="fas fa-check-circle"></i>
                <h3>All Caught Up!</h3>
                <p>No pending user verifications</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>