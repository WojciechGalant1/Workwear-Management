<?php
include_once __DIR__ . '/SessionManager.php';
include_once __DIR__ . '/../core/ServiceContainer.php';
include_once __DIR__ . '/../helpers/UrlHelper.php';
include_once __DIR__ . '/../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../helpers/LanguageSwitcher.php';

/**
 * Klasa odpowiedzialna za kontrolę dostępu (autoryzację)
 */
class AccessGuard {
    private $serviceContainer;
    private $sessionManager;
    
    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    public function isAuthenticated() {
        return $this->sessionManager->isLoggedIn();
    }
    
    public function getUserId() {
        return $this->sessionManager->getUserId();
    }
    
    public function hasRequiredStatus($requiredStatus) {
        $userId = $this->sessionManager->getUserId();
        if (!$userId) {
            return false;
        }
        $userRepo = $this->serviceContainer->getRepository('UserRepository');
        $user = $userRepo->getUserById($userId);
        return $user && $user['status'] >= $requiredStatus;
    }
    
    public function requireStatus($requiredStatus) {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        if (!$this->hasRequiredStatus($requiredStatus)) {
            $this->denyAccess();
        }
    }
    
    private function redirectToLogin() {
        $baseUrl = UrlHelper::getBaseUrl();
        header('Location: ' . $baseUrl . '/login');
        exit();
    }
    
    private function denyAccess() {
        $this->initLocalization();
        echo '<div class="alert alert-danger text-center">' . LocalizationHelper::translate('access_denied') . '</div>';
        die();
    }
    
    private function initLocalization() {
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
    }
}
