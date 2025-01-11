<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);

$currentPath = $_SERVER['REQUEST_URI'];

$deviceId = (int)explode('/', $_SERVER['REQUEST_URI'])[3];
$device = $deviceService->getDeviceById($deviceId);
$deviceParameters = $deviceService->getDeviceParameters($deviceId);
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
    $header = new Header('Urządzenie: ' . $device->getName());
    echo $header->render();
    ?>

    <h3 class="pt-5">Parametry</h3>

    <div class='d-grid gap-4 devices py-2'>
        <?php foreach ($deviceParameters as $parameter): ?>
            <div class='rounded-4 p-4 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                <div class='d-flex flex-column justify-content-center'>
                    <h5 class='mb-0 text-truncate' title='<?= htmlspecialchars($parameter['Name']) ?>'>
                        <?= htmlspecialchars($parameter['Name']) ?>
                    </h5>
                    <p class='m-0 text-secondary'>
                        <em><?= htmlspecialchars($parameter['Value']) ?></em>
                    </p>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</main>
</body>
</html>
