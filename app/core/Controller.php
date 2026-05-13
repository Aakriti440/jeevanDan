<?php
/**
 * Base Controller
 */
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once APP_ROOT . '/app/views/' . $view . '.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] === 'user';
    }
    
    protected function isOrganization() {
        return isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] === 'organization';
    }
    
    protected function isAdmin() {
        return isset($_SESSION['admin_id']);
    }
    
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    protected function isValidEmail($email) {
        return empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    protected function isNumbersOnly($value) {
        return preg_match('/^\s*\d+\s*$/', (string)$value) === 1;
    }

    protected function isValidLocationText($value, $required = true) {
        $value = trim((string)$value);
        if ($value === '') {
            return !$required;
        }
        return !$this->isNumbersOnly($value);
    }

    protected function isPastDateTime($value) {
        if (empty($value)) {
            return false;
        }

        try {
            $selected = new DateTime($value);
            $now = new DateTime();
            return $selected < $now;
        } catch (Exception $e) {
            return true;
        }
    }

    protected function createNotification($recipientId, $recipientType, $type, $title, $message, $link = null) {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (recipient_id, recipient_type, type, title, message, link)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssss", $recipientId, $recipientType, $type, $title, $message, $link);
        return $stmt->execute();
    }

    protected function notifyAdmins($type, $title, $message, $link = null) {
        $result = $this->db->query("SELECT id FROM admins WHERE is_active = 1");
        $admins = ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
        foreach ($admins as $admin) {
            $this->createNotification((int)$admin['id'], 'admin', $type, $title, $message, $link);
        }
    }

    protected function json($payload, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
