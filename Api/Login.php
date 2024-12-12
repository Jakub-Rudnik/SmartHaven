<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\UsersService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();
    $usersService = new UsersService($db);

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $message = $usersService->loginUserByEmail($email, $password);

    if ($message === 'Login successful!') {
        echo json_encode(['success' => true, 'message' => 'Login successful!']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $message]);
    }
}

exit();

