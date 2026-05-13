<?php
$userType = $_SESSION['user_type'] ?? null;
$isAdmin  = isset($_SESSION['admin_id']);
$userName = $_SESSION['user_name'] ?? $_SESSION['admin_name'] ?? null;
$isVerified = $_SESSION['user_verified'] ?? false;
$unreadNotif = (int)($_SESSION['unread_notif_count'] ?? 0);

// Determine notification URLs for the bell dropdown
$notifPageUrl  = '';
$notifJsonUrl  = '';
if ($userType === 'user') {
    $notifPageUrl = APP_URL . '/user/notifications';
    $notifJsonUrl = APP_URL . '/user/notifications-json';
} elseif ($userType === 'organization') {
    $notifPageUrl = APP_URL . '/organization/notifications';
    $notifJsonUrl = APP_URL . '/organization/notifications-json';
} elseif ($isAdmin) {
    $notifPageUrl = APP_URL . '/admin/notifications';
    $notifJsonUrl = APP_URL . '/admin/notifications-json';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'JeevanDaan'; ?> | JeevanDaan - Blood Donation Nepal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
    <style>
        /* ===== Notification Bell Styles ===== */
        .notif-bell-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        .notif-bell-btn {
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
            padding: 6px 10px;
            font-size: 1.2rem;
            color: var(--primary, #c0392b);
            border-radius: 50%;
            transition: background 0.2s;
        }
        .notif-bell-btn:hover { background: rgba(192,57,43,0.08); }
        .notif-badge {
            position: absolute;
            top: 0px;
            right: 2px;
            background: #c0392b;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 50%;
            min-width: 17px;
            height: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            line-height: 1;
        }
        .notif-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.16);
            z-index: 9999;
            overflow: hidden;
            border: 1px solid #f0e0e0;
        }
        .notif-dropdown.open { display: block; }
        .notif-dropdown-header {
            padding: 14px 18px;
            background: #c0392b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
        }
        .notif-dropdown-header a { color: #fff; font-size: 0.82rem; opacity: 0.85; text-decoration: none; }
        .notif-dropdown-header a:hover { opacity: 1; }
        #notifList { max-height: 320px; overflow-y: auto; }
        .notif-bell-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-bottom: 1px solid #f5f0f0;
            transition: background 0.15s;
        }
        .notif-bell-item:last-child { border-bottom: none; }
        .notif-bell-item:hover { background: #fdf5f5; }
        .notif-bell-item.unread { background: #fff5f5; border-left: 3px solid #c0392b; }
        .notif-bell-icon { color: #c0392b; font-size: 1rem; margin-top: 3px; flex-shrink: 0; }
        .notif-bell-content { flex: 1; min-width: 0; }
        .notif-bell-content strong { display: block; font-size: 0.88rem; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-bell-content p { font-size: 0.78rem; color: #666; margin: 2px 0 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .notif-bell-content small { font-size: 0.72rem; color: #aaa; }
        .notif-bell-action { color: #c0392b; font-size: 0.8rem; flex-shrink: 0; padding: 4px; }
        .notif-dropdown-footer { padding: 10px 16px; text-align: center; background: #fafafa; border-top: 1px solid #f0e0e0; }
        .notif-dropdown-footer a { font-size: 0.85rem; color: #c0392b; text-decoration: none; font-weight: 600; }

        /* ===== Password field wrapper ===== */
        .password-field-wrapper { position: relative; }
        .password-field-wrapper input[type="password"],
        .password-field-wrapper input[type="text"] { padding-right: 45px !important; }
        .btn-pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
            font-size: 0.95rem;
            padding: 2px 4px;
            line-height: 1;
        }
        .btn-pw-toggle:hover { color: #c0392b; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="<?php echo APP_URL; ?>" class="logo">
                <i class="fas fa-hand-holding-heart"></i>
                <span>JeevanDaan</span>
            </a>

            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="<?php echo APP_URL; ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo APP_URL; ?>/requests"><i class="fas fa-tint"></i> Blood Requests</a></li>
                    <li><a href="<?php echo APP_URL; ?>/home/learn"><i class="fas fa-book"></i> Learn</a></li>
                    <li><a href="<?php echo APP_URL; ?>/home/about"><i class="fas fa-info-circle"></i> About</a></li>
                </ul>

                <div class="nav-auth" style="display:flex;align-items:center;gap:8px;">

                    <?php if ($userType === 'user'): ?>

                        <!-- Notification Bell (User) -->
                        <div class="notif-bell-wrapper">
                            <button class="notif-bell-btn" id="notifBellBtn" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <?php if ($unreadNotif > 0): ?>
                                    <span class="notif-badge"><?php echo $unreadNotif > 99 ? '99+' : $unreadNotif; ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="notif-dropdown" id="notifDropdown">
                                <div class="notif-dropdown-header">
                                    <span><i class="fas fa-bell"></i> Notifications</span>
                                    <a href="<?php echo $notifPageUrl; ?>">View all</a>
                                </div>
                                <div id="notifList">
                                    <p style="text-align:center;padding:20px;color:#888;">Click to load</p>
                                </div>
                                <div class="notif-dropdown-footer">
                                    <a href="<?php echo $notifPageUrl; ?>">See all notifications</a>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo APP_URL; ?>/user/dashboard" class="btn btn-outline">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>

                    <?php elseif ($userType === 'organization'): ?>

                        <!-- Notification Bell (Organization) -->
                        <div class="notif-bell-wrapper">
                            <button class="notif-bell-btn" id="notifBellBtn" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <?php if ($unreadNotif > 0): ?>
                                    <span class="notif-badge"><?php echo $unreadNotif > 99 ? '99+' : $unreadNotif; ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="notif-dropdown" id="notifDropdown">
                                <div class="notif-dropdown-header">
                                    <span><i class="fas fa-bell"></i> Notifications</span>
                                    <a href="<?php echo $notifPageUrl; ?>">View all</a>
                                </div>
                                <div id="notifList">
                                    <p style="text-align:center;padding:20px;color:#888;">Click to load</p>
                                </div>
                                <div class="notif-dropdown-footer">
                                    <a href="<?php echo $notifPageUrl; ?>">See all notifications</a>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo APP_URL; ?>/organization/dashboard" class="btn btn-outline">
                            <i class="fas fa-building"></i> Dashboard
                        </a>

                    <?php elseif ($isAdmin): ?>

                        <!-- Notification Bell (Admin) -->
                        <div class="notif-bell-wrapper">
                            <button class="notif-bell-btn" id="notifBellBtn" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <?php if ($unreadNotif > 0): ?>
                                    <span class="notif-badge"><?php echo $unreadNotif > 99 ? '99+' : $unreadNotif; ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="notif-dropdown" id="notifDropdown">
                                <div class="notif-dropdown-header">
                                    <span><i class="fas fa-bell"></i> Notifications</span>
                                    <a href="<?php echo $notifPageUrl; ?>">View all</a>
                                </div>
                                <div id="notifList">
                                    <p style="text-align:center;padding:20px;color:#888;">Click to load</p>
                                </div>
                                <div class="notif-dropdown-footer">
                                    <a href="<?php echo $notifPageUrl; ?>">See all notifications</a>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline">
                            <i class="fas fa-cog"></i> Admin
                        </a>

                    <?php else: ?>
                        <button class="btn btn-primary portal-btn" onclick="openPortal()">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    <?php endif; ?>
                </div>

                <button class="mobile-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Login Portal Modal -->
    <div id="loginPortal" class="modal">
        <div class="modal-content portal-modal">
            <button class="modal-close" onclick="closePortal()">&times;</button>
            <h2><i class="fas fa-hand-holding-heart"></i> Welcome to JeevanDaan</h2>
            <p>Choose how you want to login</p>

            <div class="portal-options">
                <a href="<?php echo APP_URL; ?>/auth/google-user" class="portal-option user-option">
                    <i class="fas fa-user"></i>
                    <div>
                        <span>User</span>
                        <small>Donate or Request Blood</small>
                    </div>
                </a>

                <a href="<?php echo APP_URL; ?>/auth/google-organization" class="portal-option org-option">
                    <i class="fas fa-hospital"></i>
                    <div>
                        <span>Organization</span>
                        <small>Red Cross / Hospital Staff</small>
                    </div>
                </a>

                <a href="<?php echo APP_URL; ?>/auth/admin-login" class="portal-option admin-option">
                    <i class="fas fa-user-shield"></i>
                    <div>
                        <span>Admin</span>
                        <small>System Administrator</small>
                    </div>
                </a>
            </div>

            <div class="portal-info">
                <p><i class="fab fa-google"></i> Users &amp; Organizations login with Google</p>
                <p><i class="fas fa-lock"></i> Admins login with username &amp; password</p>
            </div>
        </div>
    </div>

    <?php if (!empty($notifJsonUrl)): ?>
    <script>window._notifJsonUrl = '<?php echo $notifJsonUrl; ?>';</script>
    <?php endif; ?>

    <main class="main">
