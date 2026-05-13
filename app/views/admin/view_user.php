<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="admin-dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-user"></i> User Verification Details</h1>
            <a href="<?php echo APP_URL; ?>/admin/verify-users" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="form-section">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <h2><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['full_name']); ?></h2>
                <?php if ($user['blood_group']): ?>
                    <span class="blood-badge large"><?php echo $user['blood_group']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-grid" style="margin-top:20px;">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone</label>
                    <p><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-birthday-cake"></i> Date of Birth</label>
                    <p>
                        <?php echo $user['date_of_birth'] ? date('M d, Y', strtotime($user['date_of_birth'])) : 'Not provided'; ?>
                        <?php if ($user['date_of_birth']): 
                            $age = (new DateTime($user['date_of_birth']))->diff(new DateTime())->y;
                            echo " <small>({$age} years)</small>";
                        endif; ?>
                    </p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-venus-mars"></i> Gender</label>
                    <p><?php echo ucfirst($user['gender'] ?? 'Not provided'); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-weight"></i> Weight</label>
                    <p>
                        <?php echo $user['weight'] ? $user['weight'] . ' kg' : 'Not provided'; ?>
                        <?php if ($user['weight'] && $user['weight'] < 45): ?>
                            <span style="color:var(--danger);"> ⚠️ Below minimum</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Last Donation</label>
                    <p><?php echo $user['last_donation_date'] ? date('M d, Y', strtotime($user['last_donation_date'])) : 'Never'; ?></p>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="form-section">
            <h2><i class="fas fa-map-marker-alt"></i> Address</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Province</label>
                    <p><?php echo htmlspecialchars($user['province'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>District</label>
                    <p><?php echo htmlspecialchars($user['district'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>Municipality</label>
                    <p><?php echo htmlspecialchars($user['municipality'] ?? 'N/A'); ?></p>
                </div>
                <div class="form-group">
                    <label>Ward No.</label>
                    <p><?php echo $user['ward_no'] ?? 'N/A'; ?></p>
                </div>
            </div>
        </div>

        <!-- Health Information -->
        <div class="form-section" style="border-left:4px solid var(--danger);">
            <h2><i class="fas fa-heartbeat"></i> Health Information</h2>

            <div style="background:#fff3cd;padding:15px;border-radius:8px;margin-bottom:20px;">
                <strong><i class="fas fa-exclamation-triangle"></i> Medical Conditions:</strong>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>HIV/AIDS</label>
                    <p><?php echo $user['has_hiv'] ? '<span style="color:var(--danger);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--success);">✗ No</span>'; ?></p>
                </div>
                <div class="form-group">
                    <label>Hepatitis B</label>
                    <p><?php echo $user['has_hepatitis_b'] ? '<span style="color:var(--danger);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--success);">✗ No</span>'; ?></p>
                </div>
                <div class="form-group">
                    <label>Hepatitis C</label>
                    <p><?php echo $user['has_hepatitis_c'] ? '<span style="color:var(--danger);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--success);">✗ No</span>'; ?></p>
                </div>
                <div class="form-group">
                    <label>Diabetes</label>
                    <p><?php echo $user['has_diabetes'] ? '<span style="color:var(--warning);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--success);">✗ No</span>'; ?></p>
                </div>
                <div class="form-group">
                    <label>Hypertension</label>
                    <p><?php echo $user['has_hypertension'] ? '<span style="color:var(--warning);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--success);">✗ No</span>'; ?></p>
                </div>
                <div class="form-group">
                    <label>Eligible</label>
                    <p><?php echo $user['is_eligible'] ? '<span style="color:var(--success);font-weight:bold;">✓ YES</span>' : '<span style="color:var(--danger);font-weight:bold;">✗ NO</span>'; ?></p>
                </div>
            </div>

            <?php if ($user['other_diseases']): ?>
                <div style="background:#fff5f5;padding:15px;border-radius:8px;margin-top:15px;">
                    <label><strong>Other Diseases/Complications:</strong></label>
                    <p style="margin-top:10px;"><?php echo nl2br(htmlspecialchars($user['other_diseases'])); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Documents - FIXED PATH -->
        <div class="form-section">
            <h2><i class="fas fa-id-card"></i> Uploaded Documents</h2>
            <div class="upload-grid">
                <div class="upload-box">
                    <label>Nagarikta Front</label>
                    <div class="upload-area" style="min-height:200px;">
                        <?php if ($user['citizenship_front']): ?>
                            <a href="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_front']; ?>" target="_blank">
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_front']; ?>" 
                                     alt="Front" 
                                     style="max-width:100%;max-height:250px;cursor:pointer;">
                            </a>
                            <small style="display:block;margin-top:10px;">Click to view full size</small>
                        <?php else: ?>
                            <i class="fas fa-times-circle" style="color:var(--danger);font-size:2rem;"></i>
                            <p style="color:var(--danger);">Not uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="upload-box">
                    <label>Nagarikta Back</label>
                    <div class="upload-area" style="min-height:200px;">
                        <?php if ($user['citizenship_back']): ?>
                            <a href="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_back']; ?>" target="_blank">
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $user['citizenship_back']; ?>" 
                                     alt="Back" 
                                     style="max-width:100%;max-height:250px;cursor:pointer;">
                            </a>
                            <small style="display:block;margin-top:10px;">Click to view full size</small>
                        <?php else: ?>
                            <i class="fas fa-times-circle" style="color:var(--danger);font-size:2rem;"></i>
                            <p style="color:var(--danger);">Not uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="upload-box">
                    <label>Donation Certificate</label>
                    <div class="upload-area" style="min-height:200px;">
                        <?php if ($user['donation_certificate']): ?>
                            <a href="<?php echo UPLOAD_URL; ?>certificates/<?php echo $user['donation_certificate']; ?>" target="_blank">
                                <img src="<?php echo UPLOAD_URL; ?>certificates/<?php echo $user['donation_certificate']; ?>" 
                                     alt="Certificate" 
                                     style="max-width:100%;max-height:250px;cursor:pointer;">
                            </a>
                            <small style="display:block;margin-top:10px;">Click to view full size</small>
                        <?php else: ?>
                            <i class="fas fa-info-circle" style="color:var(--secondary);font-size:2rem;"></i>
                            <p>Not uploaded (Optional)</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-section">
            <h2><i class="fas fa-gavel"></i> Verification Action</h2>

            <div style="display:flex;gap:20px;flex-wrap:wrap;align-items:flex-start;">
                <a href="<?php echo APP_URL; ?>/admin/approve-user/<?php echo $user['id']; ?>" 
                   class="btn btn-success btn-lg" 
                   onclick="return confirm('Approve this user?')">
                    <i class="fas fa-check"></i> Approve User
                </a>

                <form method="POST" 
                      action="<?php echo APP_URL; ?>/admin/reject-user/<?php echo $user['id']; ?>" 
                      style="display:flex;gap:10px;flex:1;flex-direction:column;"
                      onsubmit="return confirm('Reject this user?')">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <textarea name="reason" 
                              class="form-control" 
                              rows="3"
                              placeholder="Rejection reason (sent to user)..." 
                              required></textarea>
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-times"></i> Reject with Reason
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
