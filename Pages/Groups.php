<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService;

use UI\Footer;
use UI\Head;
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

$head = new Head('Grupy urządzeń');
echo $head->render();

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

    <?php
$footer = new Footer();
echo $footer->render();
