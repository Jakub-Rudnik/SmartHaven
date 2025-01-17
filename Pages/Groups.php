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

$userId = $_SESSION['userID']; // Przykładowo

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$groupService = new GroupService($db);

$groups = $groupService->getGroupsByUserId($userId);

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
        <div class='d-flex flex-column gap-5 py-5'>
            <?php if (count($groups) === 0) : ?>
                <p class="text-muted">Brak grup</p>
            <?php endif ?>
            <div class='d-flex flex-column py-5 gap-3'>

                <?php
                foreach ($groups

                as $group) :
                $devicesInGroup = $deviceService->getDevicesByGroupIdForUser(
                    $group->getGroupId(),
                    $group->getUserId()
                );

                $groupName = $group->getGroupName();
                ?>
                <div class="card rounded-4 shadow-lg">
                    <h3 class="card-header p-3"><?= htmlspecialchars($groupName) ?></h3>
                    <?php if (count($devicesInGroup) === 0) : ?>
                        <p class="text-muted p-3">Brak urządzeń w tej grupie</p>
                    <?php else : ?>
                        <div class='card-body d-grid gap-2 devices py-3'>
                            <?php
                            foreach ($devicesInGroup as $device):
                                $deviceType = $device->getType();
                                ?>
                                <div class='px-3 py-2 device-card d-flex gap-2 justify-content-between align-items-center text-decoration-none text-white'>
                                    <div class='d-flex flex-column justify-content-center'>
                                        <h3 class='mb-0 text-truncate'><?= htmlspecialchars($device->getName()) ?></h3>
                                        <p class='mb-0 text-secondary'>
                                            <?= htmlspecialchars($deviceType->getName()) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-footer gap-3">
                        <a href="/app/groups/update/<?= $group->getGroupId() ?>" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                        </a>
                        <a href="/app/groups/delete/<?= $group->getGroupId() ?>" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-trash-fill" viewBox="0 0 16 16">
                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </main>

    <?php
$footer = new Footer();
echo $footer->render();
