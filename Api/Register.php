<?php

namespace Api;

use Lib\DatabaseConnection;
use Services\UsersService;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DatabaseConnection();
    $usersService = new UsersService($db);

    $message = '';
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $usersService->registerUser($username, $email, $password);

    if ($result === 'Registration successful!') {
        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $result]);
    }
}

exit();
