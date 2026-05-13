<?php
/**
 * Auth Controller
 */
class Auth extends Controller {
    
    public function index() {
        $this->login();
    }
    
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('user/dashboard');
        }
        if ($this->isOrganization()) {
            $this->redirect('organization/dashboard');
        }
        if ($this->isAdmin()) {
            $this->redirect('admin/dashboard');
        }
        
        $this->view('auth/login', ['title' => 'Login']);
    }
    
    public function google_user() {
        $_SESSION['oauth_type'] = 'user';
        $this->redirectToGoogle();
    }
    
    public function google_organization() {
        $_SESSION['oauth_type'] = 'organization';
        $this->redirectToGoogle();
    }
    
    private function redirectToGoogle() {
        $params = http_build_query([
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        ]);
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;
    }
    
    public function google_callback() {
        if (!isset($_GET['code'])) {
            $this->setFlash('error', 'Authentication failed');
            $this->redirect('auth/login');
        }
        
        // Exchange code for token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $_GET['code'],
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ]));
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $token = json_decode($response, true);
        
        if (!isset($token['access_token'])) {
            $this->setFlash('error', 'Failed to get access token');
            $this->redirect('auth/login');
        }
        
        // Get user info
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        $type = $_SESSION['oauth_type'] ?? 'user';
        
        if ($type === 'user') {
            $this->handleUserLogin($userInfo);
        } else {
            $this->handleOrgLogin($userInfo);
        }
    }
    
    private function handleUserLogin($info) {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT * FROM users WHERE google_id = ?");
        if (!$stmt) {
            $this->setFlash('error', 'Database error. Please try again.');
            $this->redirect('auth/login');
            return;
        }
        $stmt->bind_param("s", $info['id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            // Create new user
            $stmt = $this->db->prepare("INSERT INTO users (google_id, email, full_name, profile_picture) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                $this->setFlash('error', 'Failed to create account. Please try again.');
                $this->redirect('auth/login');
                return;
            }
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $info['picture']);
            $stmt->execute();
            $userId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            if (!$stmt) {
                $this->setFlash('error', 'Error retrieving user data.');
                $this->redirect('auth/login');
                return;
            }
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        }
        
        // Update last login
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_verified'] = $user['is_verified'];
        
        $this->setFlash('success', 'Welcome, ' . $user['full_name'] . '!');
        $this->redirect('user/dashboard');
    }
    
    private function handleOrgLogin($info) {
        // Check if org exists
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE google_id = ?");
        if (!$stmt) {
            $this->setFlash('error', 'Database error. Please try again.');
            $this->redirect('auth/login');
            return;
        }
        $stmt->bind_param("s", $info['id']);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        if (!$org) {
            // Create new
            $stmt = $this->db->prepare("INSERT INTO organization_personnel (google_id, email, full_name, profile_picture) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                $this->setFlash('error', 'Failed to create account. Please try again.');
                $this->redirect('auth/login');
                return;
            }
            $stmt->bind_param("ssss", $info['id'], $info['email'], $info['name'], $info['picture']);
            $stmt->execute();
            $orgId = $this->db->insert_id;
            
            $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
            if (!$stmt) {
                $this->setFlash('error', 'Error retrieving organization data.');
                $this->redirect('auth/login');
                return;
            }
            $stmt->bind_param("i", $orgId);
            $stmt->execute();
            $org = $stmt->get_result()->fetch_assoc();
        }
        
        $_SESSION['user_id'] = $org['id'];
        $_SESSION['user_type'] = 'organization';
        $_SESSION['user_name'] = $org['full_name'];
        $_SESSION['user_email'] = $org['email'];
        $_SESSION['user_verified'] = $org['is_verified'];
        
        $this->setFlash('success', 'Welcome, ' . $org['full_name'] . '!');
        $this->redirect('organization/dashboard');
    }
    
    public function admin_login() {
        if ($this->isAdmin()) {
            $this->redirect('admin/dashboard');
        }
        
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
            if (!$stmt) {
                $error = 'Database error: ' . $this->db->error;
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $admin = $stmt->get_result()->fetch_assoc();
                
                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['full_name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    
                    $updateStmt = $this->db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                    if ($updateStmt) {
                        $updateStmt->bind_param("i", $admin['id']);
                        $updateStmt->execute();
                    }
                    
                    $this->setFlash('success', 'Welcome, ' . $admin['full_name'] . '!');
                    $this->redirect('admin/dashboard');
                } else {
                    $error = 'Invalid username or password';
                }
            }
        }
        
        $this->view('auth/admin_login', [
            'title' => 'Admin Login',
            'error' => $error
        ]);
    }

    // ==================== NEW FUNCTIONS ADDED ====================
    
    public function forgot_password() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/forgot_password', ['title' => 'Forgot Password']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if (!$this->isValidEmail($email) || $email === '') {
            $this->view('auth/forgot_password', [
                'title' => 'Forgot Password',
                'error' => 'Please enter a valid email address.'
            ]);
            return;
        }

        $stmt = $this->db->prepare("SELECT id, full_name FROM admins WHERE email = ? AND is_active = 1");
        if (!$stmt) {
            $this->view('auth/forgot_password', [
                'title' => 'Forgot Password',
                'error' => 'Database error. Please try again.'
            ]);
            return;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $del = $this->db->prepare("DELETE FROM password_reset_tokens WHERE admin_id = ?");
            if ($del) {
                $del->bind_param("i", $admin['id']);
                $del->execute();
            }

            $ins = $this->db->prepare("INSERT INTO password_reset_tokens (admin_id, token, expires_at) VALUES (?, ?, ?)");
            if ($ins) {
                $ins->bind_param("iss", $admin['id'], $token, $expiresAt);
                $ins->execute();
            }

            $resetLink = APP_URL . '/auth/reset-password?token=' . urlencode($token);
            $subject = 'JeevanDaan Password Reset';
            $body = "Hello " . $admin['full_name'] . ",\n\nUse this link to reset your password:\n\n" . $resetLink . "\n\nThis link expires in 1 hour.";
            $headers = 'From: noreply@jeevandaan.org.np';
            @mail($email, $subject, $body, $headers);
        }

        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password',
            'success' => 'If that email is registered, a reset link has been created.'
        ]);
    }

    public function reset_password() {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        if ($token === '') {
            $this->redirect('auth/forgot-password');
        }

        $stmt = $this->db->prepare("
            SELECT prt.*, admins.id AS admin_id
            FROM password_reset_tokens prt
            JOIN admins ON admins.id = prt.admin_id
            WHERE prt.token = ? AND prt.used = 0 AND prt.expires_at > NOW() AND admins.is_active = 1
        ");
        if (!$stmt) {
            $this->view('auth/reset_password', [
                'title' => 'Reset Password',
                'error' => 'Database error. Please try again.',
                'token' => ''
            ]);
            return;
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();

        if (!$reset) {
            $this->view('auth/reset_password', [
                'title' => 'Reset Password',
                'error' => 'This reset link is invalid or has expired.',
                'token' => ''
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view('auth/reset_password', ['title' => 'Reset Password', 'token' => $token]);
            return;
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            $this->view('auth/reset_password', [
                'title' => 'Reset Password',
                'error' => 'Password must be at least 8 characters.',
                'token' => $token
            ]);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->view('auth/reset_password', [
                'title' => 'Reset Password',
                'error' => 'Passwords do not match.',
                'token' => $token
            ]);
            return;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $upd = $this->db->prepare("UPDATE admins SET password = ? WHERE id = ?");
        if (!$upd) {
            $this->view('auth/reset_password', [
                'title' => 'Reset Password',
                'error' => 'Failed to update password. Please try again.',
                'token' => $token
            ]);
            return;
        }
        $upd->bind_param("si", $hashed, $reset['admin_id']);
        $upd->execute();

        $used = $this->db->prepare("UPDATE password_reset_tokens SET used = 1 WHERE id = ?");
        if ($used) {
            $used->bind_param("i", $reset['id']);
            $used->execute();
        }

        $this->setFlash('success', 'Password reset successfully. Please login with your new password.');
        $this->redirect('auth/admin-login');
    }
    
    public function logout() {
        unset($_SESSION['unread_notif_count']);
        session_destroy();
        session_start();
        $this->setFlash('success', 'You have been logged out');
        $this->redirect('');
    }
}