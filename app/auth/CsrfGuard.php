<?php
namespace App\Auth;

use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

class CsrfGuard {
    
    const TOKEN_LENGTH = 32;
    const SESSION_KEY = 'csrf_token';
    const FORM_FIELD_NAME = 'csrf_token';
    
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::SESSION_KEY] = $token;
        
        return $token;
    }
    
    public static function getToken(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION[self::SESSION_KEY] ?? null;
    }
    
    public static function getTokenField(): string {
        $token = self::getToken();
        if (!$token) {
            $token = self::generateToken();
        }
        
        return '<input type="hidden" name="' . self::FORM_FIELD_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    public static function validateToken(?string $token = null): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($token === null) {
            $token = $_POST[self::FORM_FIELD_NAME] ?? null;
        }
        
        if (!isset($_SESSION[self::SESSION_KEY]) || $token === null) {
            error_log('CSRF validation failed: No token in session or request');
            return false;
        }
        
        $sessionToken = $_SESSION[self::SESSION_KEY];
        
        if (!hash_equals($sessionToken, $token)) {
            error_log('CSRF validation failed: Token mismatch');
            return false;
        }
        
        return true;
    }
    
    public static function validateTokenFromJson(array $data): bool {
        if (!isset($data[self::FORM_FIELD_NAME])) {
            return false;
        }
        
        return self::validateToken($data[self::FORM_FIELD_NAME]);
    }
    
    public static function regenerateToken(): string {
        return self::generateToken();
    }
    
    /**
     * Check if the current request method requires CSRF validation
     * @return bool
     */
    public static function requiresValidation(): bool {
        return in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['POST', 'PUT', 'PATCH', 'DELETE']);
    }
    
    /**
     * Validate CSRF token for the current request
     * Only validates if the request method requires it (POST, PUT, PATCH, DELETE)
     * @return bool
     */
    public static function validateCurrentRequest(): bool {
        if (!self::requiresValidation()) {
            return true;
        }
        
        return self::validateToken();
    }
    
    public static function getErrorResponse(): array {
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
        
        return [
            'success' => false,
            'error' => 'CSRF token validation failed',
            'message' => LocalizationHelper::translate('error_csrf')
        ];
    }
}
