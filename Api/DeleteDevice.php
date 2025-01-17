<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\DeviceService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $db = new DatabaseConnection();

    $id = (int)$_SERVER['REQUEST_URI'] . explode('/', $_SERVER['REQUEST_URI'])[4];

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $deviceService = new DeviceService($db);

    $result = $deviceService->deleteDevice($id);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

