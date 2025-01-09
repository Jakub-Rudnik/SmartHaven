<?php
namespace Pages;


use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\DeviceService;
use UI\Header;
use UI\Navbar;
use UI\ToggleDevice;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$devices = $deviceService->getDevicesGroupedByLocations();
$currentPath = $_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupy urządzeń | SmartHaven</title>
    <link rel="stylesheet" href="/styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">
<?php
$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
<main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
    <?php
    $header = new Header('Grupy urządzeń');
    echo $header->render();
    ?>
    <div class='d-flex flex-column py-5 gap-3'>

        <?php
        $rooms = [];

        foreach ($devices as $device) {
            $roomName = $device->getRoom();

            if (!isset($rooms[$roomName])) {
                $rooms[$roomName] = [];
            }

            $rooms[$roomName][] = $device;
        }

        foreach ($rooms as $roomName => $devices):
            ?>
            <div>
                <h2><?= htmlspecialchars($roomName) ?></h2>
                <div class='d-grid gap-4 devices py-3'>
                    <?php
                    foreach ($devices as $device):
                        $toggleBtn = new ToggleDevice($device);
                        $deviceType = $device->getType();
                        ?>
                        <div class='rounded-4 p-4 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                            <div class='d-flex flex-column justify-content-center'>
                                <h3 class='mb-0 text-truncate'><?= htmlspecialchars($device->getName()) ?></h3>
                                <p class='mb-0 text-secondary'><?= htmlspecialchars($deviceType->getName()) ?></p>
                            </div>
                            <?= $toggleBtn->render() ?>
                        </div>
                    <?php endforeach ?>
                </div>
                <hr>
            </div>
        <?php endforeach ?>

    </div>
</main>
<script src="/Js/ToggleDevice.js"></script>
<script src="/Js/ToastMessage.js"></script>
</body>
</html>