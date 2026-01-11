<?php
include_once __DIR__ . '/../services/ServiceContainer.php';

$ubranie_id  = isset($_GET['ubranie_id']) ? $_GET['ubranie_id'] : '';

$serviceContainer = ServiceContainer::getInstance();
$ubranieRepo = $serviceContainer->getRepository('ClothingRepository');
$rozmiary = $ubranieRepo->getRozmiaryByUbranieId($ubranie_id);

header('Content-Type: application/json');
echo json_encode($rozmiary);
?>

