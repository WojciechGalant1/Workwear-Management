<?php
include_once __DIR__ . '/../../core/ServiceContainer.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

$serviceContainer = ServiceContainer::getInstance();
$pracownikRepo = $serviceContainer->getRepository('EmployeeRepository');
$pracownicy = $pracownikRepo->searchByName($query);

header('Content-Type: application/json');
echo json_encode($pracownicy);