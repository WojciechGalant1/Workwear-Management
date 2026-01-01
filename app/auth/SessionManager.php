<?php
include_once __DIR__ . '/../helpers/CsrfHelper.php';

class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            CsrfHelper::generateToken();
        }
    }

    public function login($userId, $status) {
        session_regenerate_id(true); //fixation attack
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_status'] = $status;
        
        CsrfHelper::regenerateToken();
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getUserId() {
        return (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
    }

    public function getUserStatus() {
        return isset($_SESSION['user_status']) ? $_SESSION['user_status'] : 0;
    }
}

?>


