<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../core/ServiceContainer.php';
include_once __DIR__ . '/../../helpers/CsrfHelper.php';
include_once __DIR__ . '/../../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../../helpers/LanguageSwitcher.php';
include_once __DIR__ . '/../../models/OrderHistory.php';
include_once __DIR__ . '/../../models/OrderDetails.php';
include_once __DIR__ . '/../../models/Clothing.php';
include_once __DIR__ . '/../../models/Size.php';
include_once __DIR__ . '/../../models/Code.php';

$currentLanguage = LanguageSwitcher::initializeWithRouting();

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!CsrfHelper::validateToken()) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('error_csrf');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }
    
    $data_zamowienia_obj = new DateTime();
    $status = 1;
    $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';

    $current_user_id = $_SESSION['user_id'];
    $serviceContainer = ServiceContainer::getInstance();
    $userRepo = $serviceContainer->getRepository('UserRepository');
    $currentUser = $userRepo->getUserById($current_user_id);

    if (!$currentUser) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('error_user_not_found');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $zamowienie = new OrderHistory($data_zamowienia_obj, $current_user_id, $uwagi, $status);
    $zamowienieRepo = $serviceContainer->getRepository('OrderHistoryRepository');
    $szczegolyZamowieniaRepo = $serviceContainer->getRepository('OrderDetailsRepository');
    $kodRepo = $serviceContainer->getRepository('CodeRepository');

    if ($zamowienieRepo->create($zamowienie)) {
        $zamowienieId = $zamowienieRepo->getLastInsertId();
        $zamowienie->setId($zamowienieId); 
        $ubrania = isset($_POST['ubrania']) ? $_POST['ubrania'] : array();

        if (!empty($ubrania) && is_array($ubrania)) {
            foreach ($ubrania as $ubranie) {
                $nazwa = isset($ubranie['nazwa']) ? trim($ubranie['nazwa']) : '';
                $rozmiar = isset($ubranie['rozmiar']) ? trim($ubranie['rozmiar']) : '';
                $firma = isset($ubranie['firma']) ? trim($ubranie['firma']) : '';
                $ilosc = isset($ubranie['ilosc']) ? intval($ubranie['ilosc']) : 0;
                $iloscMin = isset($ubranie['iloscMin']) ? intval($ubranie['iloscMin']) : 0; 
                $kod_nazwa = isset($ubranie['kod']) ? trim($ubranie['kod']) : '';

                if (empty($nazwa) || empty($rozmiar) || empty($firma) || $ilosc <= 0) {
                    $response['success'] = false;
                    $response['message'] = LocalizationHelper::translate('order_required_fields');
                    echo json_encode($response);
                    exit;
                }

                $ubranieRepo = $serviceContainer->getRepository('ClothingRepository');
                $rozmiarRepo = $serviceContainer->getRepository('SizeRepository');

                $idUbrania = $ubranieRepo->firstOrCreate(new Clothing($nazwa));
                $idRozmiaru = $rozmiarRepo->firstOrCreate(new Size($rozmiar));

                $kod = $kodRepo->findKodByNazwa($kod_nazwa);  

                if (!$kod) {
                    $nowyKod = new Code($kod_nazwa, $idUbrania, $idRozmiaru, $status); 
                    $kodId = $kodRepo->create($nowyKod);  
                } else {
                    $kodId = $kod->getIdKod();
                }

                $szczegol = new OrderDetails($zamowienieId, $idUbrania, $idRozmiaru, $ilosc, $iloscMin, $firma, $kodId);

                if (!$szczegolyZamowieniaRepo->create($szczegol)) {
                    $response['success'] = false;
                    $response['message'] = LocalizationHelper::translate('order_details_error');
                    echo json_encode($response);
                    exit;
                }
            }
        } else {
            $response['success'] = false;
            $response['message'] = LocalizationHelper::translate('order_no_items');
            echo json_encode($response);
            exit;
        }

        if ($status == 1) {
            $zamowienieRepo->dodajDoMagazynu($zamowienie);
        }

        $response['success'] = true;
        $response['message'] = LocalizationHelper::translate('order_add_success');
    } else {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('order_create_error');
    }
} else {
    $response['success'] = false;
    $response['message'] = LocalizationHelper::translate('error_general');
}

header('Content-Type: application/json');
echo json_encode($response);

