<?php
require_once 'autoload.php';

session_start();
$currentPath = $_SERVER['REQUEST_URI'];
$request = explode('/', $_SERVER['REQUEST_URI']);

use Lib\DatabaseConnection;

if ($request[1] == 'api') {
    switch ($request[2]) {
        case 'toggle-device':
            require_once 'Api/toggleDevice.php';
            break;
        case 'save-schedule':
            require_once 'Api/saveSchedule.php';
            break;
        case 'login':
            require_once 'Api/Login.php';
            break;
        case 'register':
            require_once 'Api/Register.php';
            break;
        case 'logout':
            require_once 'Api/Logout.php';
            break;
        default:
            break;
    }
    return;
}

$isApp = false;
if ($request[1] == 'app')
    $isApp = true;


$DatabaseConnection = new DatabaseConnection();

if ($isApp && !isset($_SESSION['userID'])) {
    header('Location: /login');
    exit();
}

?>


<?php

if ($isApp) {
    switch ($request[2] ?? '') {
        case '':
            require_once 'Pages/Dashboard.php';
            break;
        case 'devices':
            require_once 'Pages/Devices.php';
            break;
        case 'groups':
            require_once 'Pages/Groups.php';
            break;
        case 'schedules':
            require_once 'Pages/AddSchedule.php';
            break;
        default:
            echo '404';
            break;
    }
} else {
    switch ($request[1]) {
        case '':
            require_once 'Pages/LandingPage.php';
            break;
        case 'login':
//                if (isset($_SESSION['userID'])) {
//                    header('Location: /app');
//                    exit();
//                }
            require_once 'Pages/Login.php';
            break;
        case 'register':
            require_once 'Pages/Register.php';
            break;
        default:
            echo '404';
            break;
    }
}
?>