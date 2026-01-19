<?php
include_once __DIR__ . '/../core/ServiceContainer.php';
include_once __DIR__ . '/../helpers/UrlHelper.php';
include_once __DIR__ . '/../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../helpers/LanguageSwitcher.php';

/**
 * Klasa odpowiedzialna za kontrolę dostępu (autoryzację)
 */
class AccessGuard {
    private $serviceContainer;
    
    public function __construct() {
        $this->initSession();
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Sprawdza czy użytkownik jest zalogowany
     * @return bool
     */
    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Pobiera ID zalogowanego użytkownika
     * @return int|null
     */
    public function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Sprawdza czy użytkownik ma wymagany status
     * @param int $requiredStatus
     * @return bool
     */
    public function hasRequiredStatus($requiredStatus) {
        $userRepo = $this->serviceContainer->getRepository('UserRepository');
        $user = $userRepo->getUserById($_SESSION['user_id']);
        return $user && $user['status'] >= $requiredStatus;
    }
    
    /**
     * Wymaga określonego statusu użytkownika
     * Przekierowuje na login lub wyświetla błąd jeśli brak dostępu
     * @param int $requiredStatus
     */
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
