<?php
include_once __DIR__ . '/CsrfGuard.php';

class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            CsrfGuard::generateToken();
        }
    }

    public function login(int $userId, int $status): void {
        session_regenerate_id(true); //fixation attack
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_status'] = $status;
        
        CsrfGuard::regenerateToken();
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserStatus(): int {
        return $_SESSION['user_status'] ?? 0;
    }
}