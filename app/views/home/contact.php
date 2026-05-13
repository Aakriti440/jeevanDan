<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="dashboard">
    <div class="container">
        <div class="page-header" style="margin-top:30px;">
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        </div>

        <div class="form-section">
            <div class="form-grid">
                <div>
                    <h2><i class="fas fa-map-marker-alt"></i> Our Location</h2>
                    <p style="margin:15px 0;"><strong>JeevanDaan Nepal</strong></p>
                    <p><i class="fas fa-map-marker-alt"></i> Kathmandu, Nepal</p>
                    <p><i class="fas fa-phone"></i> +977-1-XXXXXXX</p>
                    <p><i class="fas fa-envelope"></i> info@jeevandaan.org.np</p>
                    <p style="margin-top:20px;"><strong>Emergency Blood Request:</strong></p>
                    <p style="font-size:1.5rem;color:var(--primary);font-weight:700;">+977-9XXXXXXXXX</p>
                </div>
                <div>
                    <h2><i class="fas fa-paper-plane"></i> Send Message</h2>
                    <form style="margin-top:15px;">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
