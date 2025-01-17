<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService;

$id = (int)$_SERVER['REQUEST_URI'] . explode('/', $_SERVER['REQUEST_URI'])[4];
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();

    $name = $_POST['group_name'] ?? '';

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $groupService = new GroupService($db);

    $result = $groupService->updateGroup($id, $name);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

