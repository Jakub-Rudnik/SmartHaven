<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\GroupService;
use UI\Footer;
use UI\Head;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);

$deviceId = (int)explode('/', $_SERVER['REQUEST_URI'])[4];

$device = $deviceService->getDeviceById($deviceId);

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Edytuj urządzenie');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Usuń urządzenie');
        echo $header->render();
        ?>
        <div class='d-flex flex-column gap-4 devices py-5'>
            <h3>Czy na pewno chcesz usunąć urządzenie?</h3>
            <form id="deleteDeviceForm">
                <input type="hidden" name="device_id" value="<?= $device->getId() ?>">
                <button type='submit' class='btn btn-danger'>Usuń urządzenie</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('deleteDeviceForm');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const response = await fetch(`/api/devices/delete/<?=$deviceId?>`, {
                method: 'DELETE',
            });

            const data = await response.json();

            if (data.success) {
                showToastMessage('Urządzenie zostało usunięte', true);

                form.reset();

                window.location.href = `/app/devices`;
            } else {
                showToastMessage(data.message, false);
            }
        });
    </script>
    <?php
$footer = new Footer();
echo $footer->render();