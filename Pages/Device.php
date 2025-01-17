<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\DeviceTypeService;
use UI\Devices\AC;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$deviceTypeService = new DeviceTypeService($db);

$currentPath = $_SERVER['REQUEST_URI'];

$deviceId = (int)explode('/', $_SERVER['REQUEST_URI'])[3];
$device = $deviceService->getDeviceById($deviceId);

$deviceType = $device->getType();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $device->getName() ?> | SmartHaven</title>
    <link rel="stylesheet" href="/styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">

<?php
$navbar = new Navbar($currentPath);
echo $navbar->render();
?>

<main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
    <?php
    $header = new Header('Urządzenie');
    echo $header->render();
    ?>
    <?php
    switch ($deviceType->getName()) {
        case 'AC':
            $ac = new AC($device);
            echo $ac->render();
            break;
    }


    ?>
</main>

</body>
</html>
