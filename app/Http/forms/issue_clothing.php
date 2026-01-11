<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../services/ServiceContainer.php';
include_once __DIR__ . '/../../helpers/CsrfHelper.php';
include_once __DIR__ . '/../../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../../helpers/LanguageSwitcher.php';
include_once __DIR__ . '/../../models/Issue.php';
include_once __DIR__ . '/../../models/IssuedClothing.php';

LanguageSwitcher::initializeWithRouting();

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!CsrfHelper::validateToken()) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('error_csrf');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }
    
    $pracownikID = isset($_POST['pracownikID']) ? trim($_POST['pracownikID']) : '';
    $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';

    if (empty($pracownikID)) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('issue_employee_required');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $serviceContainer = ServiceContainer::getInstance();
    $pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
    $pracownik = $pracownikRepo->getById($pracownikID);

    if (!$pracownik) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('issue_employee_not_found');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $data_wydania_obj = new DateTime();

    $current_user_id = $_SESSION['user_id'];

    $userRepo = $serviceContainer->getRepository('UserRepository');
    $currentUser = $userRepo->getUserById($current_user_id);

    if (!$currentUser) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('error_user_not_found');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    $wydaniaRepo = $serviceContainer->getRepository('IssueRepository');
    $wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');
    $stanMagazynuRepo = $serviceContainer->getRepository('WarehouseRepository');

    $wydanie = new Issue($current_user_id, $pracownik['id_pracownik'], $data_wydania_obj, $uwagi);
    $id_wydania = $wydaniaRepo->create($wydanie);

    $all_items_valid = true;

    if (!isset($_POST['ubrania']) || !is_array($_POST['ubrania'])) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('issue_no_clothing_data');
        header("Content-Type: application/json");
        echo json_encode($response);
        exit;
    }

    foreach ($_POST['ubrania'] as $ubranie) {
        $idUbrania = isset($ubranie['id_ubrania']) ? intval($ubranie['id_ubrania']) : 0;
        $idRozmiar = isset($ubranie['id_rozmiar']) ? intval($ubranie['id_rozmiar']) : 0;
        $ilosc = isset($ubranie['ilosc']) ? intval($ubranie['ilosc']) : 0;
        
        $iloscDostepna = $stanMagazynuRepo->getIlosc($idUbrania, $idRozmiar);

        if ($idUbrania == 0 || $idRozmiar == 0) {
            $response['success'] = false;
            $response['message'] = LocalizationHelper::translate('issue_invalid_code');
            $all_items_valid = false;
            break;
        }

        if ($ilosc <= 0) {
            $response['success'] = false;
            $response['message'] = LocalizationHelper::translate('issue_quantity_positive');
            $all_items_valid = false;
            break;
        }

        if ($ilosc > $iloscDostepna) {
            $response['success'] = false;
            $response['message'] = LocalizationHelper::translate('issue_insufficient_stock');
            $all_items_valid = false;
            break;
        }
    }

    if ($all_items_valid) {
        foreach ($_POST['ubrania'] as $ubranie) {
            $idUbrania = intval($ubranie['id_ubrania']);
            $idRozmiar = intval($ubranie['id_rozmiar']);
            $ilosc = intval($ubranie['ilosc']);
            $status = 1;

            $data_waznosci_miesiace = isset($ubranie['data_waznosci']) ? intval($ubranie['data_waznosci']) : 0;
            $data_waznosci_obj = new DateTime();
            $data_waznosci_obj->modify("+{$data_waznosci_miesiace} months");
            $data_waznosci = $data_waznosci_obj->format('Y-m-d H:i:s');

            $wydaneUbrania = new IssuedClothing($data_waznosci, $id_wydania, $idUbrania, $idRozmiar, $ilosc, $status);
            if ($wydaneUbraniaRepo->create($wydaneUbrania)) {
                $stanMagazynuRepo->updateIlosc($idUbrania, $idRozmiar, $ilosc);
            } else {
                $response['success'] = false;
                $response['message'] = LocalizationHelper::translate('issue_error_processing');
                break;
            }
        }
        if (!isset($response['success']) || $response['success'] !== false) {
            $response['success'] = true;
            $response['message'] = LocalizationHelper::translate('issue_success');
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = LocalizationHelper::translate('error_method_not_allowed');
}

header("Content-Type: application/json");
echo json_encode($response);
?>

