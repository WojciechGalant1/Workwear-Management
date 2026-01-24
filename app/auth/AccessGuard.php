<?php
namespace App\Auth;

use App\Auth\SessionManager;
use App\Core\ServiceContainer;
use App\Helpers\UrlHelper;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

/**
 * Klasa odpowiedzialna za kontrolę dostępu (autoryzację)
 */
class AccessGuard {
    private ServiceContainer $serviceContainer;
    private SessionManager $sessionManager;
    
    public function __construct() {
        $this->sessionManager = new SessionManager();
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    public function isAuthenticated(): bool {
        return $this->sessionManager->isLoggedIn();
    }
    
    public function getUserId(): ?int {
        return $this->sessionManager->getUserId();
    }
    
    public function hasRequiredStatus(int $requiredStatus): bool {
        $userId = $this->sessionManager->getUserId();
        if (!$userId) {
            return false;
        }
        $userRepo = $this->serviceContainer->getRepository('UserRepository');
        $user = $userRepo->getUserById($userId);
        return $user && $user['status'] >= $requiredStatus;
    }
    
    public function requireStatus(int $requiredStatus): void {
        if (!$this->isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        if (!$this->hasRequiredStatus($requiredStatus)) {
            $this->denyAccess();
        }
    }
    
    private function redirectToLogin(): void {
        $baseUrl = UrlHelper::getBaseUrl();
        header('Location: ' . $baseUrl . '/login');
        exit();
    }
    
    private function denyAccess(): void {
        $this->initLocalization();
        echo '<div class="alert alert-danger text-center">' . LocalizationHelper::translate('access_denied') . '</div>';
        die();
    }
    
    private function initLocalization(): void {
        if (!isset($_SESSION['current_language'])) {
            LanguageSwitcher::initializeWithRouting();
        }
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        LocalizationHelper::setLanguage($currentLanguage);
    }
}
