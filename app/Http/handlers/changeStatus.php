<?php
header("Content-Type: application/json; charset=UTF-8");

include_once __DIR__ . '/../../services/ServiceContainer.php';
include_once __DIR__ . '/../../helpers/CsrfHelper.php';
include_once __DIR__ . '/../../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../../helpers/LanguageSwitcher.php';

LanguageSwitcher::initializeWithRouting();

try {
    $serviceContainer = ServiceContainer::getInstance();
    $wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'], $data['currentStatus'])) {
        throw new Exception(LocalizationHelper::translate('validation_invalid_input'));
    }

    if (!CsrfHelper::validateTokenFromJson($data)) {
        echo json_encode(CsrfHelper::getErrorResponse());
        exit;
    }

    $id = intval($data['id']);
    $currentStatus = intval($data['currentStatus']);
    $newStatus = ($currentStatus == 1) ? 0 : 1;

    if ($wydaneUbraniaRepo->updateStatus($id, $newStatus)) {
        echo json_encode(['success' => true, 'newStatus' => $newStatus]);
    } else {
        echo json_encode(['success' => false, 'message' => LocalizationHelper::translate('status_update_failed')]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
