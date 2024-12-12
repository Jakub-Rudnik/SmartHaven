<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\UsersService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();
    $usersService = new UsersService($db);


    $message = $usersService->logoutUser();

    if ($message === 'Logout successful!') {
        echo json_encode(['success' => true, 'message' => 'Logout successful!']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $message]);
    }
}

exit();
