<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use UI\Footer;
use UI\Head;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);

$devices = $deviceService->getDevices();

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Urządzenia');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>

    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Urządzenia');
        echo $header->render();
        ?>
        <div class='d-flex flex-column gap-5 py-5'>
            <?php if (count($devices) === 0) : ?>
                <p class="text-muted">Brak urządzeń</p>
            <?php endif ?>
            <?php foreach ($devices as $device): ?>
                <div id="device-<?php echo $device->getId(); ?>"></div>
            <?php endforeach ?>
    </main>
    <script type="module">
        import AC from '../Js/AC.js';
        import Gate from '../Js/Gate.js';
        import Light from "../Js/Light.js";

        const devices = [
            <?php
            foreach ($devices as $device) {
                echo "{id: {$device->getId()}, name: '{$device->getName()}', type: '{$device->getType()->getName()}', url: '{$device->getUrl()}'},";
            }
            ?>
        ];

        // Twórz instancje dla każdego urządzenia
        devices.forEach((device) => {
            const simulationId = Number(device.url.split('/').pop());
            const container = document.getElementById(`device-${device.id}`);
            switch (device.type) {
                case 'AC':
                    new AC(device.id, simulationId, device.name, device.url, container);
                    break;
                case 'Gate':
                    new Gate(device.id, simulationId, device.name, device.url, container);
                    break;
                case 'Light':
                    new Light(device.id, simulationId, device.name, device.url, container);
                    break;
            }
        })


    </script>
    <?php
$footer = new Footer();
echo $footer->render();