<?php
/**
 * Admin Controller
 */
class Admin extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect('auth/admin-login');
        }
    }
    
    public function index() {
        $this->redirect('admin/dashboard');
    }
    
    public function dashboard() {
        $stats = [
            'total_users' => $this->db->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
            'verified_users' => $this->db->query("SELECT COUNT(*) as c FROM users WHERE is_verified = 1")->fetch_assoc()['c'],
            'pending_user_verifications' => $this->db->query("SELECT COUNT(*) as c FROM users WHERE verification_status = 'pending' AND citizenship_front IS NOT NULL")->fetch_assoc()['c'],
            'total_organizations' => $this->db->query("SELECT COUNT(*) as c FROM organization_personnel")->fetch_assoc()['c'],
            'pending_org_verifications' => $this->db->query("SELECT COUNT(*) as c FROM organization_personnel WHERE verification_status = 'pending'")->fetch_assoc()['c'],
            'active_requests' => $this->db->query("SELECT COUNT(*) as c FROM blood_requests WHERE status = 'active'")->fetch_assoc()['c']
        ];

        $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM notifications WHERE recipient_id = ? AND recipient_type = 'admin' AND is_read = 0");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $_SESSION['unread_notif_count'] = $stmt->get_result()->fetch_assoc()['c'];
        
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function verify_users() {
        $users = $this->db->query("
            SELECT * FROM users 
            WHERE verification_status = 'pending' AND citizenship_front IS NOT NULL AND citizenship_back IS NOT NULL
            ORDER BY created_at ASC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/verify_users', [
            'title' => 'Verify Users',
            'users' => $users,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function view_user($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('admin/verify-users');
        }
        
        $this->view('admin/view_user', [
            'title' => 'View User',
            'user' => $user
        ]);
    }
    
    public function approve_user($id) {
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1, verification_status = 'approved', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);
        
        if ($stmt->execute()) {
            $stmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'user', 'verification', 'Account Verified!', 'Your account has been verified. You can now donate blood!', '/user/profile')");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $this->setFlash('success', 'User verified successfully!');
        } else {
            $this->setFlash('error', 'Failed to verify user');
        }
        
        $this->redirect('admin/verify-users');
    }
    
    public function reject_user($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : (int)$id;
        } else {
            if (!$id) {
                $this->redirect('admin/verify-users');
            }
            $userId = (int)$id;
            $reason = 'No reason provided';
        }
        
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 0, verification_status = 'rejected', verified_by = ?, verified_at = NOW(), rejection_reason = ? WHERE id = ?");
        $stmt->bind_param("isi", $adminId, $reason, $userId);
        
        if ($stmt->execute()) {
            $notifMessage = "Your verification was rejected. Reason: " . $reason . ". Please re-upload correct documents.";
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'user', 'verification', 'Verification Rejected', ?, '/user/profile')");
            $notifStmt->bind_param("is", $userId, $notifMessage);
            $notifStmt->execute();
            $this->setFlash('success', 'User rejected and notified!');
        } else {
            $this->setFlash('error', 'Failed to reject user');
        }
        
        $this->redirect('admin/verify-users');
    }
    
    public function verify_organizations() {
        $orgs = $this->db->query("SELECT * FROM organization_personnel WHERE verification_status = 'pending' ORDER BY created_at ASC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/verify_organizations', [
            'title' => 'Verify Organizations',
            'orgs' => $orgs,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function approve_organization($id) {
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE organization_personnel SET is_verified = 1, verification_status = 'approved', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $id);
        
        if ($stmt->execute()) {
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'organization', 'verification', 'Organization Verified!', 'Your organization has been verified!', '/organization/dashboard')");
            $notifStmt->bind_param("i", $id);
            $notifStmt->execute();
            $this->setFlash('success', 'Organization verified!');
        }
        
        $this->redirect('admin/verify-organizations');
    }
    
    public function users() {
        $users = $this->db->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/users', [
            'title' => 'All Users',
            'users' => $users
        ]);
    }

    public function delete_user($id = null) {
        if (!$id) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $this->setFlash($stmt->execute() ? 'success' : 'error', $stmt->affected_rows ? 'User deleted successfully.' : 'Failed to delete user.');
        $this->redirect('admin/users');
    }
    
    public function requests() {
        $requests = $this->db->query("SELECT * FROM blood_requests ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/requests', [
            'title' => 'All Requests',
            'requests' => $requests
        ]);
    }
    
    public function organizations() {
        $orgs = $this->db->query("SELECT * FROM organization_personnel ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
        
      $this->view('admin/organizations', [
    'title' => 'Manage Organizations',
    'orgs' => $orgs,
    'flash' => $this->getFlash()
]);
    }

    public function save_organization($id = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/organizations');
        }

        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $organizationName = trim($_POST['organization_name'] ?? '');
        $organizationId = trim($_POST['organization_id'] ?? '');
        $organizationType = $_POST['organization_type'] ?? 'red_cross';
        $position = trim($_POST['position'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $municipality = trim($_POST['municipality'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$this->isValidEmail($email) || $email === '') {
            $this->setFlash('error', 'Please enter a valid organization email.');
            $this->redirect('admin/organizations');
        }
        if (!$this->isValidLocationText($municipality, false) || !$this->isValidLocationText($address, false)) {
            $this->setFlash('error', 'Location fields cannot contain numbers only.');
            $this->redirect('admin/organizations');
        }

        if ($id) {
            $stmt = $this->db->prepare("
                UPDATE organization_personnel SET email = ?, full_name = ?, phone = ?, organization_name = ?,
                    organization_id = ?, organization_type = ?, position = ?, province = ?, district = ?,
                    municipality = ?, address = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssssssssssi", $email, $fullName, $phone, $organizationName, $organizationId, $organizationType, $position, $province, $district, $municipality, $address, $id);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO organization_personnel
                    (email, full_name, phone, organization_name, organization_id, organization_type, position, province, district, municipality, address, is_verified, verification_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'approved')
            ");
            $stmt->bind_param("sssssssssss", $email, $fullName, $phone, $organizationName, $organizationId, $organizationType, $position, $province, $district, $municipality, $address);
        }

        $this->setFlash($stmt->execute() ? 'success' : 'error', $id ? 'Organization updated successfully.' : 'Organization added successfully.');
        $this->redirect('admin/organizations');
    }

    public function delete_organization($id = null) {
        if (!$id) {
            $this->setFlash('error', 'Organization not found.');
            $this->redirect('admin/organizations');
        }

        $stmt = $this->db->prepare("DELETE FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $this->setFlash($stmt->execute() ? 'success' : 'error', $stmt->affected_rows ? 'Organization deleted successfully.' : 'Failed to delete organization.');
        $this->redirect('admin/organizations');
    }
    
    public function view_organization($id) {
        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();
        
        if (!$org) {
            $this->setFlash('error', 'Organization not found');
            $this->redirect('admin/organizations');
        }
        
        $this->view('admin/view_organization', [
            'title' => 'View Organization',
            'org' => $org
        ]);
    }
    
    public function reject_organization($id = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = trim($_POST['reason'] ?? 'No reason provided');
            $orgId = isset($_POST['org_id']) ? (int)$_POST['org_id'] : (int)$id;
        } else {
            if (!$id) {
                $this->redirect('admin/verify-organizations');
            }
            $orgId = (int)$id;
            $reason = 'No reason provided';
        }
        
        $adminId = $_SESSION['admin_id'];
        
        $stmt = $this->db->prepare("UPDATE organization_personnel SET is_verified = 0, verification_status = 'rejected', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $adminId, $orgId);
        
        if ($stmt->execute()) {
            $notifMessage = "Your organization verification was rejected. Reason: " . $reason;
            $notifStmt = $this->db->prepare("INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) VALUES (?, 'organization', 'verification', 'Verification Rejected', ?, '/organization/profile')");
            $notifStmt->bind_param("is", $orgId, $notifMessage);
            $notifStmt->execute();
            $this->setFlash('success', 'Organization rejected and notified!');
        }
        
        $this->redirect('admin/verify-organizations');
    }

    public function notifications() {
        $adminId = $_SESSION['admin_id'];
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'admin' ORDER BY created_at DESC LIMIT 100");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM notifications WHERE recipient_id = ? AND recipient_type = 'admin' AND is_read = 0");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $unreadCount = $stmt->get_result()->fetch_assoc()['c'];

        $mark = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'admin'");
        $mark->bind_param("i", $adminId);
        $mark->execute();
        $_SESSION['unread_notif_count'] = 0;

        $this->view('admin/notifications', [
            'title' => 'Admin Notifications',
            'notifications' => $notifications,
            'unreadCount' => $unreadCount
        ]);
    }

    public function notifications_json() {
        $adminId = $_SESSION['admin_id'];
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'admin' ORDER BY created_at DESC LIMIT 6");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $this->json(['notifications' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }

    public function delete_notification($id = null) {
        if (!$id) {
            $this->json(['success' => false], 400);
        }
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND recipient_id = ? AND recipient_type = 'admin'");
        $stmt->bind_param("ii", $id, $_SESSION['admin_id']);
        $this->json(['success' => $stmt->execute()]);
    }

    public function mark_all_read() {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'admin'");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $this->json(['success' => $stmt->execute()]);
    }
}
