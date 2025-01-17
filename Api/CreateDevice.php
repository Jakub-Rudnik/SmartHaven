<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\DeviceService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();

    $name = $_POST['device_name'] ?? '';
    $type = (int)$_POST['type_id'] ?? '';
    $url = $_POST['device_url'] ?? '';
    $group = (int)$_POST['group_id'] ?? '';

    if (empty($name) || empty($type) || empty($url)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $deviceService = new DeviceService($db);

    $result = $deviceService->createDevice($name, $type, $url, $group);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    $deviceID = (int)$result["data"];

    $device = $deviceService->assignDeviceToUser($_SESSION['userID'], $deviceID);

    if (!$device) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error assigning device to user']);
        exit();
    }

    http_response_code(201);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

