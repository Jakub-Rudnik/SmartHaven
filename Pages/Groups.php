<?php
namespace Pages;

use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService; // <-- Upewnij się, że używasz odpowiedniego serwisu grup
use UI\Header;
use UI\Navbar;
use UI\ToggleDevice;


$userId = $_SESSION['userId'] ?? 2; // Przykładowo

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$groupService = new GroupService($db);

// 1. Pobierz wszystkie grupy danego użytkownika
$groups = $groupService->getGroupsByUserId($userId);

// 2. Dla każdej grupy pobierz listę urządzeń
//    (za chwilę w pętli – w razie potrzeby można pobierać je tu, ale czytelniej będzie w pętli)
// 3. Pobierz urządzenia nieprzypisane do żadnej grupy
$devicesWithoutGroup = $deviceService->getDevicesNoGroupForUser($userId);

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
            integrity="sha384-Yvp0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">

<?php
// Pasek nawigacji
$navbar = new Navbar($currentPath);
echo $navbar->render();
?>

<main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
    <?php
    // Nagłówek strony
    $header = new Header('Grupy urządzeń');
    echo $header->render();
    ?>

    <div class='d-flex flex-column py-5 gap-3'>

        <?php
        /**
         * 1. Wyświetlamy grupy i urządzenia w każdej grupie
         */
        foreach ($groups as $group) :
            // Pobranie urządzeń dla tej konkretnej grupy
            $devicesInGroup = $deviceService->getDevicesByGroupIdForUser(
                $group->getGroupId(),
                $group->getUserId()  // lub $userId, bo to samo w tym przypadku
            );

            // Nazwa grupy:
            $groupName = $group->getGroupName();
            ?>
            <div>
                <h2><?= htmlspecialchars($groupName) ?></h2>
                <?php if (count($devicesInGroup) === 0) : ?>
                    <p class="text-muted">Brak urządzeń w tej grupie</p>
                <?php else : ?>
                    <div class='d-grid gap-4 devices py-3'>
                        <?php
                        foreach ($devicesInGroup as $device):
                            $toggleBtn = new ToggleDevice($device);
                            $deviceType = $device->getType();
                            ?>
                            <div class='rounded-4 p-4 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                                <div class='d-flex flex-column justify-content-center'>
                                    <h3 class='mb-0 text-truncate'><?= htmlspecialchars($device->getName()) ?></h3>
                                    <p class='mb-0 text-secondary'>
                                        <?= htmlspecialchars($deviceType->getName()) ?>
                                    </p>
                                </div>
                                <?= $toggleBtn->render() ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr>
            </div>
        <?php endforeach; ?>

        <?php
        /**
         * 2. Wyświetlamy urządzenia bez żadnej grupy (jeśli takie są)
         */
        if (count($devicesWithoutGroup) > 0):
            ?>
            <div>
                <h2>Urządzenia bez grupy</h2>
                <div class='d-grid gap-4 devices py-3'>
                    <?php
                    foreach ($devicesWithoutGroup as $device):
                        $toggleBtn = new ToggleDevice($device);
                        $deviceType = $device->getType();
                        ?>
                        <div class='rounded-4 p-4 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                            <div class='d-flex flex-column justify-content-center'>
                                <h3 class='mb-0 text-truncate'><?= htmlspecialchars($device->getName()) ?></h3>
                                <p class='mb-0 text-secondary'>
                                    <?= htmlspecialchars($deviceType->getName()) ?>
                                </p>
                            </div>
                            <?= $toggleBtn->render() ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<script src="/Js/ToggleDevice.js"></script>
<script src="/Js/ToastMessage.js"></script>
</body>
</html>
