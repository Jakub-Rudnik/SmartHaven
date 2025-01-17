<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $db = new DatabaseConnection();

    $id = (int)$_SERVER['REQUEST_URI'] . explode('/', $_SERVER['REQUEST_URI'])[4];

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $groupService = new GroupService($db);
    $deviceService = new DeviceService($db);

    if (count($deviceService->getDevicesByGroupIdForUser($id, $_SESSION['userID'])) > 0) {
        echo json_encode(['success' => false, 'message' => 'You are not allowed to delete this group']);
        exit();
    }

    $result = $groupService->deleteGroup($id);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

