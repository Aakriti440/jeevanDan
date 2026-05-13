<?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="fas fa-user-shield"></i>
                <h1>Admin Login</h1>
                <p>Enter your credentials</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php $flash = $flash ?? (function(){ $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; })(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" required autofocus
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="adminPassword" class="form-control" required style="padding-right:45px;">
                        <span onclick="togglePasswordVisibility('adminPassword', 'adminEyeIcon')"
                              style="position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:#888;">
                            <i class="fas fa-eye" id="adminEyeIcon"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;">
                <a href="<?php echo APP_URL; ?>/auth/forgot-password">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
                &nbsp;|&nbsp;
                <a href="<?php echo APP_URL; ?>/auth/login">
                    <i class="fas fa-arrow-left"></i> Back to Login Options
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
