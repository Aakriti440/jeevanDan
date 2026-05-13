<?php
/**
 * Requests Controller
 */
class Requests extends Controller {
    
    public function index() {
        $result = $this->db->query("
            SELECT * FROM blood_requests 
            WHERE status = 'active' 
            ORDER BY FIELD(urgency, 'critical', 'urgent', 'normal'),
                created_at DESC
        ");
        $requests = ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('requests/index', [
            'title' => 'Blood Requests',
            'requests' => $requests
        ]);
    }
    
    /**
     * View single request - renamed to avoid conflict
     */
    public function details($id = null) {
        if (!$id) {
            $this->redirect('requests');
        }
        
        $stmt = $this->db->prepare("SELECT * FROM blood_requests WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if (!$request) {
            $this->setFlash('error', 'Request not found');
            $this->redirect('requests');
        }
        
        $this->view('requests/view', [
            'title' => 'Blood Request',
            'request' => $request
        ]);
    }
    
    public function create() {
        if (!$this->isLoggedIn() && !$this->isOrganization()) {
            $this->redirect('auth/login');
        }
        
        $districts = $this->db->query("SELECT DISTINCT district FROM nepal_districts ORDER BY district")->fetch_all(MYSQLI_ASSOC);
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requiredBy = !empty($_POST['required_by']) ? $_POST['required_by'] : null;
            $contactEmail = trim($_POST['contact_email'] ?? '');
            $hospitalAddress = trim($_POST['hospital_address'] ?? '');
            $hospitalName = trim($_POST['hospital_name'] ?? '');

            if (!$this->isValidEmail($contactEmail)) {
                $error = 'Please enter a valid contact email address.';
            } elseif (!$this->isValidLocationText($hospitalName)) {
                $error = 'Hospital name cannot contain numbers only.';
            } elseif (!$this->isValidLocationText($hospitalAddress, false)) {
                $error = 'Hospital address cannot contain numbers only.';
            } elseif ($requiredBy && $this->isPastDateTime($requiredBy)) {
                $error = 'Required by date cannot be in the past.';
            }

            if ($error) {
                $this->view('requests/create', [
                    'title' => 'Create Blood Request',
                    'districts' => $districts,
                    'error' => $error
                ]);
                return;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO blood_requests 
                (requested_by, requester_type, patient_name, patient_age, blood_group, 
                units_required, hospital_name, hospital_district, hospital_address, 
                urgency, contact_name, contact_phone, contact_email, reason, 
                required_by, additional_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("ississssssssssss",
                $_SESSION['user_id'],
                $_SESSION['user_type'],
                $_POST['patient_name'],
                $_POST['patient_age'],
                $_POST['blood_group'],
                $_POST['units_required'],
                $_POST['hospital_name'],
                $_POST['hospital_district'],
                $_POST['hospital_address'],
                $_POST['urgency'],
                $_POST['contact_name'],
                $_POST['contact_phone'],
                $contactEmail,
                $_POST['reason'],
                $requiredBy,
                $_POST['additional_notes']
            );
            
            if ($stmt->execute()) {
                $requestId = $this->db->insert_id;
                
                // Notify matching donors
                $bloodGroup = $this->db->real_escape_string($_POST['blood_group']);
                $donors = $this->db->query("
                    SELECT id FROM users 
                    WHERE blood_group = '$bloodGroup' 
                    AND is_verified = 1 
                    AND willing_to_donate = 1 
                    AND receive_notifications = 1
                ")->fetch_all(MYSQLI_ASSOC);
                
                foreach ($donors as $donor) {
                    $donorId = $donor['id'];
                    $msg = "Someone needs $bloodGroup blood at " . $_POST['hospital_name'] . ". You can save a life!";
                    $link = "/requests/details/$requestId";
                    
                    $notifStmt = $this->db->prepare("
                        INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link) 
                        VALUES (?, 'user', 'blood_request', 'Urgent Blood Request!', ?, ?)
                    ");
                    $notifStmt->bind_param("iss", $donorId, $msg, $link);
                    $notifStmt->execute();
                }

                $orgs = $this->db->query("
                    SELECT id FROM organization_personnel
                    WHERE is_active = 1 AND receive_notifications = 1
                ")->fetch_all(MYSQLI_ASSOC);

                foreach ($orgs as $org) {
                    $msg = "New " . $_POST['urgency'] . " request for $bloodGroup blood at " . $_POST['hospital_name'] . ".";
                    $this->createNotification((int)$org['id'], 'organization', 'blood_request', 'New Blood Request', $msg, "/requests/details/$requestId");
                }

                $this->notifyAdmins(
                    'new_request',
                    'New Blood Request',
                    'A new blood request was submitted for ' . $_POST['blood_group'] . ' at ' . $_POST['hospital_name'] . '.',
                    '/admin/requests'
                );
                
                $this->setFlash('success', 'Blood request created! ' . count($donors) . ' matching donors notified.');
                $this->redirect('requests/details/' . $requestId);
            } else {
                $error = 'Failed to create request: ' . $this->db->error;
            }
        }
        
        $this->view('requests/create', [
            'title' => 'Create Blood Request',
            'districts' => $districts,
            'error' => $error
        ]);
    }
}
