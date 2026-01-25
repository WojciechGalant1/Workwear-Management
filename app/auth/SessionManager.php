<?php
declare(strict_types=1);
namespace App\Auth;

use App\Auth\CsrfGuard;

class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Security hardening
            // Use 'Strict' for SameSite to prevent CSRF, but 'Lax' is often more practical for navigation.
            $cookieParams = [
                'lifetime' => 0, // Session cookie
                'path' => '/',
                'domain' => '', // Current domain
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Only on HTTPS
                'httponly' => true, // JavaScript cannot access session cookie
                'samesite' => 'Strict' 
            ];
            
            session_set_cookie_params($cookieParams);
            session_name('WORKWEAR_SESSION'); // Unique name
            
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