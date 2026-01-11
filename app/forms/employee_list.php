<?php
include_once __DIR__ . '/../services/ServiceContainer.php';
include_once __DIR__ . '/../helpers/CsrfHelper.php';
include_once __DIR__ . '/../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../helpers/LanguageSwitcher.php';

LanguageSwitcher::initializeWithRouting();

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!CsrfHelper::validateToken()) {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('error_csrf');
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $imie = isset($_POST['imie']) ? $_POST['imie'] : '';
    $nazwisko = isset($_POST['nazwisko']) ? $_POST['nazwisko'] : '';
    $stanowisko = isset($_POST['stanowisko']) ? $_POST['stanowisko'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    if (!empty($id) && !empty($imie) && !empty($nazwisko) && !empty($stanowisko) && $status !== '') {
        $serviceContainer = ServiceContainer::getInstance();
        $pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');

        if ($pracownikRepo->update($id, $imie, $nazwisko, $stanowisko, $status)) {
            $response['success'] = true;
            $response['message'] = LocalizationHelper::translate('employee_update_success');
        } else {
            $response['success'] = false;
            $response['message'] = LocalizationHelper::translate('error_general');
        }
    } else {
        $response['success'] = false;
        $response['message'] = LocalizationHelper::translate('validation_required');
    }
} else {
    $response['success'] = false;
    $response['message'] = LocalizationHelper::translate('error_general');
}

header('Content-Type: application/json');
echo json_encode($response);
?>

