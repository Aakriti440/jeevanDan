<?php
/**
 * Organization Controller
 */
class Organization extends Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->isOrganization()) {
            $this->redirect('auth/login');
        }
    }

    public function index() {
        $this->redirect('organization/dashboard');
    }

    public function dashboard() {
        $orgId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();

        $requests = $this->db->query("SELECT * FROM blood_requests WHERE status = 'active' ORDER BY urgency, created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' ORDER BY created_at DESC LIMIT 5");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' AND is_read = 0");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $_SESSION['unread_notif_count'] = $stmt->get_result()->fetch_assoc()['c'];

        $this->view('organization/dashboard', [
            'title'         => 'Organization Dashboard',
            'org'           => $org,
            'requests'      => $requests,
            'notifications' => $notifications,
            'flash'         => $this->getFlash()
        ]);
    }

    public function donors() {
        if (!$_SESSION['user_verified']) {
            $this->setFlash('error', 'Your account must be verified to view donors. Please wait for admin approval.');
            $this->redirect('organization/dashboard');
        }

        $donors = $this->db->query("
            SELECT id, full_name, email, phone, blood_group, district, municipality, last_donation_date, is_verified
            FROM users
            WHERE is_verified = 1 AND willing_to_donate = 1
            ORDER BY blood_group, district
        ")->fetch_all(MYSQLI_ASSOC);

        $this->view('organization/donors', [
            'title'  => 'All Donors',
            'donors' => $donors
        ]);
    }

    public function requests() {
        $requests = $this->db->query("SELECT * FROM blood_requests WHERE status = 'active' ORDER BY urgency, created_at DESC")->fetch_all(MYSQLI_ASSOC);

        $this->view('organization/requests', [
            'title'    => 'Blood Requests',
            'requests' => $requests
        ]);
    }

    public function profile() {
        $orgId = $_SESSION['user_id'];

        // Only validate POST fields when actually submitting (not on GET)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->isValidLocationText($_POST['municipality'] ?? '')) {
                $this->setFlash('error', 'Municipality cannot contain numbers only.');
                $this->redirect('organization/profile');
            }
            if (!$this->isValidLocationText($_POST['address'] ?? '', false)) {
                $this->setFlash('error', 'Address cannot contain numbers only.');
                $this->redirect('organization/profile');
            }
        }

        $stmt = $this->db->prepare("SELECT * FROM organization_personnel WHERE id = ?");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $org = $stmt->get_result()->fetch_assoc();

        $districts = $this->db->query("SELECT province, district FROM nepal_districts ORDER BY province, district")->fetch_all(MYSQLI_ASSOC);

        $this->view('organization/profile', [
            'title'     => 'Organization Profile',
            'org'       => $org,
            'districts' => $districts,
            'flash'     => $this->getFlash()
        ]);
    }

    public function update_profile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('organization/profile');
        }

        $orgId = $_SESSION['user_id'];

        if (!$this->isValidLocationText($_POST['municipality'] ?? '')) {
            $this->setFlash('error', 'Municipality cannot contain numbers only.');
            $this->redirect('organization/profile');
        }
        if (!$this->isValidLocationText($_POST['address'] ?? '', false)) {
            $this->setFlash('error', 'Address cannot contain numbers only.');
            $this->redirect('organization/profile');
        }

        // Handle file upload
        $orgIdDoc = null;
        if (isset($_FILES['organization_id_document']) && $_FILES['organization_id_document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['organization_id_document'];
            if ($file['size'] <= MAX_FILE_SIZE && in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'org_' . uniqid() . '_' . time() . '.' . $ext;
                $targetPath = UPLOAD_PATH . 'id_cards/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $orgIdDoc = $filename;
                }
            }
        }

        $sql = "UPDATE organization_personnel SET
            full_name = ?, phone = ?, organization_name = ?, organization_id = ?,
            organization_type = ?, position = ?, province = ?, district = ?,
            municipality = ?, address = ?, receive_notifications = ?";

        $params = [
            $_POST['full_name'],
            $_POST['phone'],
            $_POST['organization_name'],
            $_POST['organization_id'],
            $_POST['organization_type'],
            $_POST['position'],
            $_POST['province'],
            $_POST['district'],
            $_POST['municipality'],
            $_POST['address'],
            isset($_POST['receive_notifications']) ? 1 : 0
        ];
        $types = "ssssssssssi";

        if ($orgIdDoc) {
            $sql    .= ", organization_id_document = ?";
            $params[] = $orgIdDoc;
            $types  .= "s";
        }

        $sql    .= " WHERE id = ?";
        $params[] = $orgId;
        $types  .= "i";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['user_name'] = $_POST['full_name'];
            $this->setFlash('success', 'Profile updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update profile');
        }

        $this->redirect('organization/profile');
    }

    public function notifications() {
        $orgId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' ORDER BY created_at DESC LIMIT 100");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->db->prepare("SELECT COUNT(*) AS c FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' AND is_read = 0");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $unreadCount = $stmt->get_result()->fetch_assoc()['c'];

        $mark = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'organization'");
        $mark->bind_param("i", $orgId);
        $mark->execute();
        $_SESSION['unread_notif_count'] = 0;

        $this->view('organization/notifications', [
            'title'         => 'Notifications',
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount
        ]);
    }

    public function notifications_json() {
        $orgId = $_SESSION['user_id'];
        $stmt  = $this->db->prepare("SELECT * FROM notifications WHERE recipient_id = ? AND recipient_type = 'organization' ORDER BY created_at DESC LIMIT 6");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $this->json(['notifications' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }

    public function delete_notification($id = null) {
        if (!$id) { $this->json(['success' => false], 400); }
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND recipient_id = ? AND recipient_type = 'organization'");
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $this->json(['success' => $stmt->execute()]);
    }

    public function mark_all_read() {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE recipient_id = ? AND recipient_type = 'organization'");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $this->json(['success' => $stmt->execute()]);
    }

    public function appointments() {
        $orgId = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId       = (int)($_POST['request_id']       ?? 0);
            $donorId         = (int)($_POST['donor_id']         ?? 0);
            $appointmentDate = $_POST['appointment_date']       ?? '';
            $location        = trim($_POST['location']          ?? '');
            $notes           = trim($_POST['notes']             ?? '');

            if ($requestId <= 0 || $donorId <= 0 || $appointmentDate === '') {
                $this->json(['success' => false, 'message' => 'Request, donor, and appointment date are required.'], 422);
            }
            if ($this->isPastDateTime($appointmentDate)) {
                $this->json(['success' => false, 'message' => 'Appointment date cannot be in the past.'], 422);
            }
            if (!$this->isValidLocationText($location, false)) {
                $this->json(['success' => false, 'message' => 'Location cannot contain numbers only.'], 422);
            }

            $stmt = $this->db->prepare("
                INSERT INTO appointments (request_id, donor_id, organization_id, appointment_date, location, notes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiisss", $requestId, $donorId, $orgId, $appointmentDate, $location, $notes);
            $success = $stmt->execute();

            if ($success) {
                $this->createNotification($donorId, 'user', 'appointment', 'Donation Appointment', 'A donation appointment has been scheduled.', '/user/dashboard');
            }

            $this->json(['success' => $success, 'id' => $this->db->insert_id]);
        }

        $stmt = $this->db->prepare("SELECT * FROM appointments WHERE organization_id = ? ORDER BY appointment_date DESC");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $this->json(['appointments' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }

    public function update_appointment($id = null) {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false], 400);
        }

        $status  = $_POST['status'] ?? 'scheduled';
        $allowed = ['scheduled', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        $stmt = $this->db->prepare("UPDATE appointments SET status = ? WHERE id = ? AND organization_id = ?");
        $stmt->bind_param("sii", $status, $id, $_SESSION['user_id']);
        $this->json(['success' => $stmt->execute()]);
    }

    public function inventory() {
        $orgId = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bloodGroup  = $_POST['blood_group']       ?? '';
            $units       = (int)($_POST['units_available'] ?? 0);
            $expiryDate  = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

            if ($units < 0) {
                $this->json(['success' => false, 'message' => 'Units cannot be negative.'], 422);
            }
            if ($expiryDate && $this->isPastDateTime($expiryDate . ' 23:59:59')) {
                $this->json(['success' => false, 'message' => 'Expiry date cannot be in the past.'], 422);
            }

            $stmt = $this->db->prepare("
                INSERT INTO blood_inventory (organization_id, blood_group, units_available, expiry_date)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE units_available = VALUES(units_available), expiry_date = VALUES(expiry_date), updated_at = NOW()
            ");
            $stmt->bind_param("isis", $orgId, $bloodGroup, $units, $expiryDate);
            $this->json(['success' => $stmt->execute()]);
        }

        $stmt = $this->db->prepare("SELECT * FROM blood_inventory WHERE organization_id = ? ORDER BY blood_group");
        $stmt->bind_param("i", $orgId);
        $stmt->execute();
        $this->json(['inventory' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }

    public function blood_testing() {
        $orgId = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $stmt = $this->db->prepare("SELECT * FROM blood_tests WHERE organization_id = ? ORDER BY tested_at DESC");
            $stmt->bind_param("i", $orgId);
            $stmt->execute();
            $this->json(['tests' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
        }

        $donationId = (int)($_POST['donation_id'] ?? 0);
        $result     = $_POST['result']             ?? 'pending';
        $notes      = trim($_POST['notes']         ?? '');
        $allowed    = ['pending', 'safe', 'unsafe'];
        if ($donationId <= 0 || !in_array($result, $allowed, true)) {
            $this->json(['success' => false, 'message' => 'Valid donation and result are required.'], 422);
        }

        $stmt = $this->db->prepare("INSERT INTO blood_tests (donation_id, organization_id, result, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $donationId, $orgId, $result, $notes);
        $success = $stmt->execute();

        if ($success) {
            $status = $result === 'safe' ? 'accepted' : ($result === 'unsafe' ? 'rejected' : 'testing');
            $upd = $this->db->prepare("UPDATE donations SET status = ? WHERE id = ?");
            $upd->bind_param("si", $status, $donationId);
            $upd->execute();
        }

        $this->json(['success' => $success, 'id' => $this->db->insert_id]);
    }
}
