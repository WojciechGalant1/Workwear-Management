<?php

include_once __DIR__ . '/../app/services/ServiceContainer.php';
include_once __DIR__ . '/../app/auth/SessionManager.php';
include_once __DIR__ . '/../app/helpers/UrlHelper.php';
include_once __DIR__ . '/../app/helpers/NavBuilder.php';

class ClassMenu {
    public function navBar($currentPage, $currentLanguage = 'en') {
        
        $sessionManager = new SessionManager();
        $userStatus = $sessionManager->getUserStatus(); 

        $serviceContainer = ServiceContainer::getInstance();
        $stanMagazynuRepo = $serviceContainer->getRepository('WarehouseRepository');
        $hasShortages = $stanMagazynuRepo->checkIlosc();

        $baseUrl = UrlHelper::getBaseUrl();
        $activeUri = UrlHelper::getCleanUrl($currentPage);
        
        echo '
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">

                <!-- Logo -->
                <a class="navbar-brand ms-2" href="' . $baseUrl . '/">
                    <img src="' . $baseUrl . '/img/protective-equipment.png" class="logo-image" alt="Logo" height="30">
                </a>

                <!-- Menu toggle for mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Nav links -->
                <div class="collapse navbar-collapse" id="navbarMenu">
                    <ul class="navbar-nav me-auto d-flex align-items-center">
                        ' . NavBuilder::buildNavGroups($activeUri, $baseUrl, $userStatus, $hasShortages) . '
                    </ul>

                    <!-- Language switcher (aligned right) -->
                    <ul class="navbar-nav ms-auto">
                        ' . NavBuilder::buildLanguageSwitcher($baseUrl, $currentLanguage) . '
                    </ul>
                </div>

            </div>
        </nav>';
    }
}
?>
