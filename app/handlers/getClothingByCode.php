<?php
include_once __DIR__ . '/../services/ServiceContainer.php';
include_once __DIR__ . '/../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../helpers/LanguageSwitcher.php';

LanguageSwitcher::initializeWithRouting();

if (isset($_GET['kod'])) {
    $serviceContainer = ServiceContainer::getInstance();
    $kodRepo = $serviceContainer->getRepository('CodeRepository');
    $kodData = $kodRepo->findByNazwa($_GET['kod']);

    if ($kodData) {
        $response = [
            'id_ubrania' => $kodData['id_ubrania'],
            'nazwa_ubrania' => $kodData['nazwa_ubrania'],
            'id_rozmiar' => $kodData['id_rozmiar'],
            'nazwa_rozmiaru' => $kodData['nazwa_rozmiaru'],
        ];
    } else {
        $response = ['error' => LocalizationHelper::translate('clothing_code_not_found')];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>


