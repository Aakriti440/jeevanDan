<?php
class User extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
    }
    
    public function index() {
        $this->redirect('user/dashboard');
    }
    
    public function dashboard() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        // Force new users to complete their profile before using the dashboard
if (empty($user['phone']) || empty($user['blood_group']) || empty($user['date_of_birth'])) {
    $this->setFlash('error', 'Please complete your profile before using the dashboard. Fill in your phone number, date of birth, and blood group.');
    $this->redirect('user/profile');
}
        $matchingRequests = [];
        if ($user['blood_group']) {
            $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE blood_group = ? AND status = 'active' ORDER BY urgency, created_at DESC LIMIT 5");
            $stmt->bind_param("s", $user['blood_group']);
            $stmt->execute();
            $matchingRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE requested_by = ? AND requester_type = 'user' ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $myRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' ORDER BY created_at DESC LIMIT 5");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' AND is_read = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $unreadCount = $stmt->get_result()->fetch_assoc()['count'];
        $_SESSION['unread_notif_count'] = $unreadCount;
        
        $canDonate = true;
        if ($user['last_donation_date']) {
            $lastDonation = new DateTime($user['last_donation_date']);
            $now = new DateTime();
            $diff = $lastDonation->diff($now)->days;
            $canDonate = $diff >= DONATION_INTERVAL_DAYS;
        }
        
        $this->view('user/dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'matchingRequests' => $matchingRequests,
            'myRequests' => $myRequests,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'canDonate' => $canDonate,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function profile() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        $districts = $this->db->query("SELECT province, district FROM nepal_districts ORDER BY province, district")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('user/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'districts' => $districts,
            'flash' => $this->getFlash()
        ]);
    }
    
    public function update_profile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/profile');
        }
        
        $userId = $_SESSION['user_id'];

        if (!$this->isValidLocationText($_POST['municipality'] ?? '')) {
            $this->setFlash('error', 'Municipality cannot contain numbers only.');
            $this->redirect('user/profile');
        }

        if (!$this->isValidLocationText($_POST['tole'] ?? '', false)) {
            $this->setFlash('error', 'Tole/Street cannot contain numbers only.');
            $this->redirect('user/profile');
        }

        if (!empty($_POST['date_of_birth'])) {
            $dob = new DateTime($_POST['date_of_birth']);
            $age = $dob->diff(new DateTime())->y;
            if ($dob > new DateTime() || $age < MIN_DONATION_AGE) {
                $this->setFlash('error', 'Date of birth must show an age of at least ' . MIN_DONATION_AGE . ' years.');
                $this->redirect('user/profile');
            }
        }

        if (!empty($_POST['last_donation_date']) && new DateTime($_POST['last_donation_date']) > new DateTime()) {
            $this->setFlash('error', 'Last donation date cannot be in the future.');
            $this->redirect('user/profile');
        }
        
        $citizenshipFront = $this->handleUpload('citizenship_front', 'id_cards');
        $citizenshipBack = $this->handleUpload('citizenship_back', 'id_cards');
        $donationCert = $this->handleUpload('donation_certificate', 'certificates');
        $bloodGroupProof = $this->handleUpload('blood_group_proof', 'blood_proofs');
        
        $updateFields = [];
        $params = [];
        $types = "";
        
        $fields = [
            'full_name' => ['value' => trim($_POST['full_name']), 'type' => 's'],
            'phone' => ['value' => trim($_POST['phone']), 'type' => 's'],
            'date_of_birth' => ['value' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null, 'type' => 's'],
            'gender' => ['value' => $_POST['gender'], 'type' => 's'],
            'blood_group' => ['value' => $_POST['blood_group'], 'type' => 's'],
            'weight' => ['value' => (float)$_POST['weight'], 'type' => 'd'],
            'province' => ['value' => $_POST['province'], 'type' => 's'],
            'district' => ['value' => $_POST['district'], 'type' => 's'],
            'municipality' => ['value' => trim($_POST['municipality']), 'type' => 's'],
            'ward_no' => ['value' => (int)$_POST['ward_no'], 'type' => 'i'],
            'tole' => ['value' => trim($_POST['tole'] ?? ''), 'type' => 's'],
            'last_donation_date' => ['value' => !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : null, 'type' => 's'],
            'has_hiv' => ['value' => isset($_POST['has_hiv']) ? 1 : 0, 'type' => 'i'],
            'has_hepatitis_b' => ['value' => isset($_POST['has_hepatitis_b']) ? 1 : 0, 'type' => 'i'],
            'has_hepatitis_c' => ['value' => isset($_POST['has_hepatitis_c']) ? 1 : 0, 'type' => 'i'],
            'has_diabetes' => ['value' => isset($_POST['has_diabetes']) ? 1 : 0, 'type' => 'i'],
            'has_hypertension' => ['value' => isset($_POST['has_hypertension']) ? 1 : 0, 'type' => 'i'],
            'other_diseases' => ['value' => trim($_POST['other_diseases'] ?? ''), 'type' => 's'],
            'willing_to_donate' => ['value' => isset($_POST['willing_to_donate']) ? 1 : 0, 'type' => 'i'],
            'receive_notifications' => ['value' => isset($_POST['receive_notifications']) ? 1 : 0, 'type' => 'i']
        ];
        
        foreach ($fields as $field => $data) {
            $updateFields[] = "$field = ?";
            $params[] = $data['value'];
            $types .= $data['type'];
        }
        
        if ($citizenshipFront) {
            $updateFields[] = "citizenship_front = ?";
            $params[] = $citizenshipFront;
            $types .= "s";
        }
        if ($citizenshipBack) {
            $updateFields[] = "citizenship_back = ?";
            $params[] = $citizenshipBack;
            $types .= "s";
        }
        if ($donationCert) {
            $updateFields[] = "donation_certificate = ?";
            $params[] = $donationCert;
            $types .= "s";
        }
        if ($bloodGroupProof) {
            $updateFields[] = "blood_group_proof = ?";
            $params[] = $bloodGroupProof;
            $types .= "s";
        }
        
        $params[] = $userId;
        $types .= "i";
        
        $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = trim($_POST['full_name']);
            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile');
        }
        
        $this->redirect('user/profile');
    }
    
    private function handleUpload($fieldName, $folder) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $file = $_FILES[$fieldName];
        if ($file['size'] > MAX_FILE_SIZE) return null;
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ['application/pdf']);
        if (!in_array($file['type'], $allowedTypes)) return null;
        $targetDir = UPLOAD_PATH . $folder;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'jd_' . uniqid() . '_' . time() . '.' . $ext;
        $targetPath = $targetDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) return $filename;
        return null;
    }
    
    public function notifications() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' ORDER BY created_at DESC LIMIT 50");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $this->view('user/notifications', ['title' => 'Notifications', 'notifications' => $notifications]);
    }

    public function notifications_json() {
        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'user' ORDER BY created_at DESC LIMIT 6");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $this->json(['notifications' => $notifications]);
    }

    public function donate() {
        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !$user['is_verified']) {
            $this->setFlash('error', 'Your account must be verified before donating.');
            $this->redirect('user/dashboard');
        }

        if (!empty($user['last_donation_date'])) {
            $lastDonation = new DateTime($user['last_donation_date']);
            $daysSince = $lastDonation->diff(new DateTime())->days;
            if ($daysSince < DONATION_INTERVAL_DAYS) {
                $daysLeft = DONATION_INTERVAL_DAYS - $daysSince;
                $this->setFlash('error', 'You cannot donate yet. Please wait ' . $daysLeft . ' more day(s).');
                $this->redirect('user/dashboard');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlash('error', 'Donation submission requires a form request.');
            $this->redirect('user/dashboard');
        }

        $donationDate = !empty($_POST['donation_date']) ? $_POST['donation_date'] : date('Y-m-d');
        if (new DateTime($donationDate) > new DateTime()) {
            $this->setFlash('error', 'Donation date cannot be in the future.');
            $this->redirect('user/dashboard');
        }

        $location = trim($_POST['location'] ?? '');
        if (!$this->isValidLocationText($location, false)) {
            $this->setFlash('error', 'Donation location cannot contain numbers only.');
            $this->redirect('user/dashboard');
        }

        $units = max(1, (int)($_POST['units'] ?? 1));
        $stmt = $this->db->prepare("
            INSERT INTO donations (user_id, blood_group, donation_date, units, location, status, notes)
            VALUES (?, ?, ?, ?, ?, 'recorded', ?)
        ");
        $notes = trim($_POST['notes'] ?? '');
        $stmt->bind_param("ississ", $userId, $user['blood_group'], $donationDate, $units, $location, $notes);

        if ($stmt->execute()) {
            $upd = $this->db->prepare("UPDATE users SET last_donation_date = ? WHERE id = ?");
            $upd->bind_param("si", $donationDate, $userId);
            $upd->execute();
            $this->notifyAdmins('donation', 'New Donation Recorded', $user['full_name'] . ' recorded a blood donation.', '/admin/users');
            $this->setFlash('success', 'Donation record saved successfully.');
        } else {
            $this->setFlash('error', 'Failed to save donation record.');
        }

        $this->redirect('user/dashboard');
    }
    
    public function delete_notification($id) {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND recipient_id = ? AND recipient_type = 'user'");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
    
    public function mark_all_read() {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'user'");
        $stmt->bind_param("i", $_SESSION['user_id']);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }
}
