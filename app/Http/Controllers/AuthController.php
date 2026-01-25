<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Helpers\UrlHelper;
use App\Auth\CsrfGuard;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;

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

    public function logout(): void {
        \App\Http\Handlers\Auth\LogoutHandler::run();
    }
}
