<?php
class CsrfHelper {
    
    const TOKEN_LENGTH = 32;
    const SESSION_KEY = 'csrf_token';
    const FORM_FIELD_NAME = 'csrf_token';
    
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::SESSION_KEY] = $token;
        
        return $token;
    }
    
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION[self::SESSION_KEY])) {
            return $_SESSION[self::SESSION_KEY];
        }
        
        return null;
    }
    
    public static function getTokenField() {
        $token = self::getToken();
        if (!$token) {
            $token = self::generateToken();
        }
        
        return '<input type="hidden" name="' . self::FORM_FIELD_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    public static function validateToken($token = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if ($token === null) {
            $token = isset($_POST[self::FORM_FIELD_NAME]) ? $_POST[self::FORM_FIELD_NAME] : null;
        }
        
        if (!isset($_SESSION[self::SESSION_KEY])) {
            error_log('CSRF validation failed: No token in session');
            return false;
        }
        
        $sessionToken = $_SESSION[self::SESSION_KEY];
        
        if (!hash_equals($sessionToken, $token)) {
            error_log('CSRF validation failed: Token mismatch');
            return false;
        }
        
        return true;
    }
    
    public static function validateTokenFromJson($data) {
        if (!isset($data[self::FORM_FIELD_NAME])) {
            return false;
        }
        
        return self::validateToken($data[self::FORM_FIELD_NAME]);
    }
    
    public static function regenerateToken() {
        return self::generateToken();
    }
    
    /**
     * Check if the current request method requires CSRF validation
     * @return bool
     */
    public static function requiresValidation() {
        return in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT', 'PATCH', 'DELETE'));
    }
    
    /**
     * Validate CSRF token for the current request
     * Only validates if the request method requires it (POST, PUT, PATCH, DELETE)
     * @return bool
     */
    public static function validateCurrentRequest() {
        if (!self::requiresValidation()) {
            return true;
        }
        
        return self::validateToken();
    }
    
    public static function getErrorResponse() {
        include_once __DIR__ . '/LocalizationHelper.php';
        include_once __DIR__ . '/LanguageSwitcher.php';
        
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
        
        return array(
            'success' => false,
            'error' => 'CSRF token validation failed',
            'message' => LocalizationHelper::translate('error_csrf')
        );
    }
}
?>
