<?php

namespace Pages;


use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService;
use UI\Header;
use UI\Navbar;

$currentPath = $_SERVER['REQUEST_URI'];

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$groupService = new GroupService($db);

$groups = $groupService->getGroupsByUserId($_SESSION['userID']);
$devices = $deviceService->getDevices();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablica | SmartHaven</title>
    <link rel="stylesheet" href="/styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">
<?php
$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
<main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
    <?php
    $header = new Header('Witaj ' . $_SESSION['username'] . '! 😎');
    echo $header->render();
    ?>

    <div class='d-flex flex-wrap gap-5 py-5'>
        <div class="card flex-1 w-100">
            <h5 class="card-header p-3">Urządzenia</h5>
            <div class='d-flex flex-column gap-5 p-5'>
                <?php if (count($devices) === 0) : ?>
                    <p class="text-muted">Brak urządzeń</p>
                <?php else : ?>
                    <h2 class="text-center"><?= count($devices) ?></h2>
                <?php endif; ?>
            </div>
        </div>
        <div class="card flex-1 w-100">
            <h5 class="card-header p-3">Grupy urządzeń</h5>
            <div class='d-flex flex-column gap-5 p-5'>
                <?php if (count($groups) === 0) : ?>
                    <p class="text-muted">Brak grup</p>
                <?php else : ?>
                    <h2 class="text-center"><?= count($groups) ?></h2>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>
</body>
</html>