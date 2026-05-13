<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-lock"></i>
                <h1>Reset Password</h1>
                <p>Enter and confirm your new password</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($token)): ?>
                <form method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="form-group">
                        <label>New Password *</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="newPassword" class="form-control" minlength="8" required style="padding-right:45px;">
                            <span onclick="togglePasswordVisibility('newPassword', 'newPassEye')"
                                  style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#888;">
                                <i class="fas fa-eye" id="newPassEye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <div style="position:relative;">
                            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" minlength="8" required style="padding-right:45px;">
                            <span onclick="togglePasswordVisibility('confirmPassword', 'confirmPassEye')"
                                  style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#888;">
                                <i class="fas fa-eye" id="confirmPassEye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-check"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>