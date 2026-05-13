<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="profile-page">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Create Blood Request</h1>
            <a href="<?php echo APP_URL; ?>/requests" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-section">
                <h2><i class="fas fa-user-injured"></i> Patient Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Patient Name *</label>
                        <input type="text" name="patient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Patient Age</label>
                        <input type="number" name="patient_age" class="form-control" min="1" max="120">
                    </div>
                    <div class="form-group">
                        <label>Blood Group Required *</label>
                        <select name="blood_group" class="form-control" required>
                            <option value="">Select Blood Group</option>
                            <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg): ?>
                                <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Units Required *</label>
                        <input type="number" name="units_required" class="form-control" value="1" min="1" max="10" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-hospital"></i> Hospital Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Hospital Name *</label>
                        <input type="text" name="hospital_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hospital District *</label>
                        <select name="hospital_district" class="form-control" required>
                            <option value="">Select District</option>
                            <?php foreach ($districts as $d): ?>
                                <option value="<?php echo $d['district']; ?>"><?php echo $d['district']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Hospital Address</label>
                        <input type="text" name="hospital_address" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-exclamation-triangle"></i> Urgency & Details</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Urgency Level *</label>
                      <select name="urgency" class="form-control" required>
    <option value="normal">Low — Normal situation, can wait</option>
    <option value="urgent">Medium — Needed within 24 hours</option>
    <option value="critical">High — Emergency, needed immediately</option>
</select>
                    </div>
                    <div class="form-group">
                        <label>Required By (Date)</label>
                        <input type="datetime-local" name="required_by" class="form-control">
                    </div>
                    <div class="form-group full-width">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Surgery, accident, etc."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Additional Notes</label>
                        <textarea name="additional_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-phone-alt"></i> Contact Information</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Person Name *</label>
                        <input type="text" name="contact_name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Phone *</label>
                        <input type="tel" name="contact_phone" class="form-control" placeholder="98XXXXXXXX" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane"></i> Submit Blood Request
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
