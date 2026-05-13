<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-hand-holding-heart"></i>
                <h1>Welcome to JeevanDaan</h1>
                <p>Choose how you want to login</p>
            </div>
            
            <div class="login-options">
                <a href="<?php echo APP_URL; ?>/auth/google-user" class="login-option user">
                    <i class="fas fa-user"></i>
                    <h3>Login as User</h3>
                    <p>Donate or request blood</p>
                    <span class="google-badge"><i class="fab fa-google"></i> Continue with Google</span>
                </a>
                
                <a href="<?php echo APP_URL; ?>/auth/google-organization" class="login-option organization">
                    <i class="fas fa-hospital"></i>
                    <h3>Login as Organization</h3>
                    <p>Red Cross / Hospital / Blood Bank</p>
                    <span class="google-badge"><i class="fab fa-google"></i> Continue with Google</span>
                </a>
                
                <a href="<?php echo APP_URL; ?>/auth/admin-login" class="login-option admin">
                    <i class="fas fa-user-shield"></i>
                    <h3>Login as Admin</h3>
                    <p>System Administrator</p>
                    <span class="password-badge"><i class="fas fa-lock"></i> Username & Password</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
