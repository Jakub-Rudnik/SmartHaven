<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\DeviceService;

$id = (int)$_SERVER['REQUEST_URI'] . explode('/', $_SERVER['REQUEST_URI'])[4];
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();

    $name = $_POST['device_name'] ?? '';
    $url = $_POST['device_url'] ?? '';
    $group = (int)$_POST['group_id'] ?? '';

    if (empty($name) || empty($id) || empty($url)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $deviceService = new DeviceService($db);

    $result = $deviceService->updateDevice($id, $name, $url, $group);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

