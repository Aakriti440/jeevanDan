<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-building"></i> Organization Profile</h1>
            <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($flash)): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="verification-card <?php echo $org['is_verified'] ? 'verified' : 'pending'; ?>">
            <i class="fas <?php echo $org['is_verified'] ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
            <div>
                <h3><?php echo $org['is_verified'] ? 'Organization Verified' : 'Verification Pending'; ?></h3>
                <p><?php echo $org['is_verified'] ? 'You can view all donors and requests.' : 'Complete your profile and wait for admin verification.'; ?></p>
            </div>
        </div>

        <form method="POST" action="<?php echo APP_URL; ?>/organization/update-profile" enctype="multipart/form-data">
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($org['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($org['email']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Phone (Nepal) *</label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($org['phone'] ?? ''); ?>" placeholder="98XXXXXXXX" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-hospital"></i> Organization Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Organization Name *</label>
                        <input type="text" name="organization_name" class="form-control" value="<?php echo htmlspecialchars($org['organization_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Organization ID *</label>
                        <input type="text" name="organization_id" class="form-control" value="<?php echo htmlspecialchars($org['organization_id'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Organization Type *</label>
                        <select name="organization_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="red_cross" <?php echo (($org['organization_type'] ?? '') === 'red_cross') ? 'selected' : ''; ?>>Red Cross Society</option>
                            <option value="hospital" <?php echo (($org['organization_type'] ?? '') === 'hospital') ? 'selected' : ''; ?>>Hospital</option>
                            <option value="blood_bank" <?php echo (($org['organization_type'] ?? '') === 'blood_bank') ? 'selected' : ''; ?>>Blood Bank</option>
                            <option value="ngo" <?php echo (($org['organization_type'] ?? '') === 'ngo') ? 'selected' : ''; ?>>NGO</option>
                            <option value="other" <?php echo (($org['organization_type'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Position *</label>
                        <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($org['position'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-id-card"></i> Organization ID Document (Optional)</h2>
                <div class="upload-grid">
                    <div class="upload-box">
                        <label>Registration Certificate</label>
                        <div class="upload-area" onclick="document.getElementById('organization_id_document').click()">
                            <?php if (!empty($org['organization_id_document'])): ?>
                                <img src="<?php echo UPLOAD_URL; ?>id_cards/<?php echo $org['organization_id_document']; ?>" alt="Org ID" style="max-width:100%;max-height:150px;">
                                <span class="uploaded-badge"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Click to upload</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="organization_id_document" id="organization_id_document" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Address</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Province *</label>
                        <select name="province" class="form-control" required>
                            <option value="">Select Province</option>
                            <?php foreach (['Koshi', 'Madhesh', 'Bagmati', 'Gandaki', 'Lumbini', 'Karnali', 'Sudurpashchim'] as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo (($org['province'] ?? '') === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>District *</label>
                        <select name="district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo $d['district']; ?>" <?php echo (($org['district'] ?? '') === $d['district']) ? 'selected' : ''; ?>><?php echo $d['district']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Municipality *</label>
                        <input type="text" name="municipality" class="form-control" value="<?php echo htmlspecialchars($org['municipality'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Full Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($org['address'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-cog"></i> Preferences</h2>
                <div class="preferences-grid">
                    <label class="switch-label">
                        <input type="checkbox" name="receive_notifications" <?php echo ($org['receive_notifications'] ?? 1) ? 'checked' : ''; ?>>
                        <span class="switch"></span>
                        <span>Receive notifications</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('input[type="file"]').forEach(function(input) {
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            var uploadArea = this.previousElementSibling;
            reader.onload = function(e) {
                uploadArea.innerHTML = '<img src="' + e.target.result + '" style="max-width:100%;max-height:150px;"><span class="uploaded-badge"><i class="fas fa-check"></i></span>';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
