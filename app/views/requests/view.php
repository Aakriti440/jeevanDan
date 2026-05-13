<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tint"></i> Blood Request Details</h1>
            <a href="<?php echo APP_URL; ?>/requests" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="form-section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <span class="blood-badge large"><?php echo $request['blood_group']; ?></span>
                <span class="urgency-badge <?php echo $request['urgency']; ?>" style="font-size:1rem;padding:8px 20px;">
                    <?php echo ucfirst($request['urgency']); ?>
                </span>
                <span class="status-badge <?php echo $request['status']; ?>" style="font-size:1rem;padding:8px 20px;">
                    <?php echo ucfirst($request['status']); ?>
                </span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Patient Name</label>
                    <p style="font-size:1.2rem;font-weight:600;"><?php echo htmlspecialchars($request['patient_name']); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-birthday-cake"></i> Patient Age</label>
                    <p style="font-size:1.2rem;"><?php echo $request['patient_age'] ? $request['patient_age'] . ' years' : 'Not specified'; ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-flask"></i> Units Required</label>
                    <p style="font-size:1.2rem;font-weight:600;"><?php echo $request['units_required']; ?> unit(s)</p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-hospital"></i> Hospital</label>
                    <p style="font-size:1.2rem;"><?php echo htmlspecialchars($request['hospital_name']); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> District</label>
                    <p style="font-size:1.2rem;"><?php echo htmlspecialchars($request['hospital_district']); ?></p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Posted</label>
                    <p style="font-size:1.2rem;"><?php echo date('M d, Y - h:i A', strtotime($request['created_at'])); ?></p>
                </div>
            </div>

            <?php if ($request['reason']): ?>
                <div class="form-group" style="margin-top:15px;">
                    <label><i class="fas fa-comment-medical"></i> Reason</label>
                    <p><?php echo htmlspecialchars($request['reason']); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($request['additional_notes']): ?>
                <div class="form-group" style="margin-top:15px;">
                    <label><i class="fas fa-sticky-note"></i> Additional Notes</label>
                    <p><?php echo htmlspecialchars($request['additional_notes']); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contact Section -->
        <div class="form-section">
            <h2><i class="fas fa-phone-alt"></i> Contact Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Contact Person</label>
                    <p style="font-size:1.1rem;"><?php echo htmlspecialchars($request['contact_name'] ?? 'Not provided'); ?></p>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <p style="font-size:1.1rem;font-weight:600;"><?php echo htmlspecialchars($request['contact_phone']); ?></p>
                </div>
                <?php if ($request['contact_email']): ?>
                    <div class="form-group">
                        <label>Email</label>
                        <p style="font-size:1.1rem;"><?php echo htmlspecialchars($request['contact_email']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display:flex;gap:15px;margin-top:20px;flex-wrap:wrap;">
                <a href="tel:<?php echo $request['contact_phone']; ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone"></i> Call Now
                </a>
                <?php if ($request['contact_email']): ?>
                    <a href="mailto:<?php echo $request['contact_email']; ?>?subject=Blood Donation - <?php echo $request['blood_group']; ?> - JeevanDaan&body=Hello, I am interested in donating blood for the patient <?php echo htmlspecialchars($request['patient_name']); ?>. My blood group is <?php echo $request['blood_group']; ?>." class="btn btn-outline btn-lg">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                <?php endif; ?>
                <a href="https://wa.me/977<?php echo ltrim($request['contact_phone'], '0'); ?>?text=Hello, I saw your blood request on JeevanDaan for <?php echo $request['blood_group']; ?> blood. I would like to help." class="btn btn-success btn-lg" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
