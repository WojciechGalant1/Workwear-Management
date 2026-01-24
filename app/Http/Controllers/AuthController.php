<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../helpers/UrlHelper.php';
require_once __DIR__ . '/../../auth/CsrfGuard.php';
require_once __DIR__ . '/../../helpers/LocalizationHelper.php';
require_once __DIR__ . '/../../helpers/LanguageSwitcher.php';

class AuthController extends BaseController {
    
    public function login(): array {
        $currentLanguage = LanguageSwitcher::getCurrentLanguage();
        $baseUrl = UrlHelper::getBaseUrl();
        
        $csrfToken = CsrfGuard::getToken();
        if (!$csrfToken) {
            $csrfToken = CsrfGuard::generateToken();
        }
        
        return [
            'currentLanguage' => $currentLanguage,
            'baseUrl' => $baseUrl,
            'csrfToken' => $csrfToken
        ];
    }
}
