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
        
        $_SESSION[self::SESSION_KEY] = array(
            'token' => $token,
            'timestamp' => time()
        );
        
        return $token;
    }
    
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION[self::SESSION_KEY]['token'])) {
            return $_SESSION[self::SESSION_KEY]['token'];
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
        
        if (!isset($_SESSION[self::SESSION_KEY]['token'])) {
            return false;
        }
        
        $sessionToken = $_SESSION[self::SESSION_KEY]['token'];
        $sessionTimestamp = $_SESSION[self::SESSION_KEY]['timestamp'];
        
        if (!hash_equals($sessionToken, $token)) {
            return false;
        }
        
        $maxAge = 3600; // 1 hour
        if ((time() - $sessionTimestamp) > $maxAge) {
            return false;
        }

        //$_SESSION[self::SESSION_KEY]['timestamp'] = time();
        
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
    
    public static function getTokenForAjax() {
        $token = self::getToken();
        if (!$token) {
            $token = self::generateToken();
        }
        
        return array(
            'token' => $token,
            'field_name' => self::FORM_FIELD_NAME
        );
    }
    
    public static function requiresValidation() {
        return in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT', 'PATCH', 'DELETE'));
    }
    
    public static function validateCurrentRequest() {
        if (!self::requiresValidation()) {
            return true;
        }
        
        return self::validateToken();
    }
    
    public static function getErrorResponse() {
        include_once __DIR__ . '/LocalizationHelper.php';
        include_once __DIR__ . '/LanguageSwitcher.php';
        
        // Initialize language if not already done
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
