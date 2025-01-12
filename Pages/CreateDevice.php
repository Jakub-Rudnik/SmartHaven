<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\DeviceTypeService;
use UI\Footer;
use UI\Head;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$deviceTypeService = new DeviceTypeService($db);

$devices = $deviceService->getDevices();
$deviceTypes = $deviceTypeService->getDeviceTypes();

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Urządzenia');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Dodaj urządzenie');
        echo $header->render();
        ?>
        <div class='d-grid gap-4 devices py-5'>
            <form id="createDeviceForm">
                <div class='mb-3'>
                    <label for='device-select' class='form-label'>Wybierz typ urządzenia:</label>
                    <select id='device-select' class='form-select' name='device_id'>
                        <option value=''>Wybierz typ urządzenia</option>
                        <? foreach ($deviceTypes as $deviceType): ?>
                            <option value='<?= htmlspecialchars($deviceType->getId()) ?>'>
                                <?= htmlspecialchars($deviceType->getName()) ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
            </form>
        </div>
    </main>
    <?php
$footer = new Footer();
echo $footer->render();