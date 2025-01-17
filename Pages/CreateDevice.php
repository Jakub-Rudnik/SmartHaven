<?php

namespace Pages;

use Lib\DatabaseConnection;
use Services\DeviceService;
use Services\DeviceTypeService;
use Services\GroupService;
use UI\Footer;
use UI\Head;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceTypeService = new DeviceTypeService($db);
$groupService = new GroupService($db);

$deviceTypes = $deviceTypeService->getDeviceTypes();
$userGroups = $groupService->getGroupsByUserId($_SESSION['userID']);

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Dodaj urządzenie');
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
                    <label for='device-type' class='form-label'>Wybierz typ urządzenia:</label>
                    <select id='device-type' class='form-select' name='type_id' required>
                        <option value=''>Wybierz typ urządzenia</option>
                        <? foreach ($deviceTypes as $deviceType): ?>
                            <option value='<?= htmlspecialchars($deviceType->getId()) ?>'>
                                <?= htmlspecialchars($deviceType->getDescription()) ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
                <div class='mb-3'>
                    <label for='device-name' class='form-label'>Nazwa urządzenia:</label>
                    <input name="device_name" id="device-name" class="form-control" required>
                </div>
                <div class='mb-3'>
                    <label for='device-url' class='form-label'>Adres urządzenia:</label>
                    <input name="device_url" id="device-url" class="form-control" required>
                </div>
                <div class='mb-3'>
                    <label for='device-group' class='form-label'>Grupa:</label>
                    <select id='device-group' class='form-select' name='group_id'>
                        <option value='0'>Brak grupy</option>
                        <? foreach ($userGroups as $group): ?>
                            <option value='<?= htmlspecialchars($group->getGroupId()) ?>'>
                                <?= htmlspecialchars($group->getGroupName()) ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </div>
                <button type='submit' class='btn btn-primary'>Dodaj urządzenie</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('createDeviceForm');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch('/api/devices/create', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToastMessage('Urządzenie zostało dodane', true);

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