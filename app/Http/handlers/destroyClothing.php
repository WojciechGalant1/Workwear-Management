<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../core/ServiceContainer.php';
include_once __DIR__ . '/../../helpers/CsrfHelper.php';
include_once __DIR__ . '/../../helpers/LocalizationHelper.php';
include_once __DIR__ . '/../../helpers/LanguageSwitcher.php';

LanguageSwitcher::initializeWithRouting();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!CsrfHelper::validateTokenFromJson($data)) {
        http_response_code(403);
        echo json_encode(CsrfHelper::getErrorResponse());
        exit;
    }
    
    $id = isset($data['id']) ? $data['id'] : null;

    if ($id) {
        $serviceContainer = ServiceContainer::getInstance();
        $wydaneUbraniaRepo = $serviceContainer->getRepository('IssuedClothingRepository');
        $success = $wydaneUbraniaRepo->destroyStatus($id);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => LocalizationHelper::translate('status_update_failed')]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => LocalizationHelper::translate('validation_clothing_id_required')]);
    }
} else {
    echo json_encode(['success' => false, 'error' => LocalizationHelper::translate('error_method_not_allowed')]);
    exit;
}


