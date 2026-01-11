<?php
header('Content-Type: application/json');
include_once __DIR__ . '/../services/ServiceContainer.php';

try {
    $query = isset($_GET['query']) ? $_GET['query'] : '';

    $serviceContainer = ServiceContainer::getInstance();
    $ubranieRepo = $serviceContainer->getRepository('ClothingRepository');
    $ubrania = $ubranieRepo->searchByName($query);

    if ($ubrania === false) {
        throw new Exception('Failed to fetch data');
    }

    echo json_encode($ubrania);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
