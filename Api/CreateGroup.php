<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\GroupService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();

    $name = $_POST['group_name'] ?? '';

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    $userId = $_SESSION['userID'];
    $groupService = new GroupService($db);

    $result = $groupService->createGroup($userId, $name);

    if (!$result['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit();
    }

    http_response_code(201);
    echo json_encode(['success' => true, 'data' => $result['data']]);
    exit();
}

