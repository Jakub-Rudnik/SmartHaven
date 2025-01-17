<?php

namespace Pages;

use UI\Footer;
use UI\Head;
use UI\Header;
use UI\Navbar;

$deviceId = (int)explode('/', $_SERVER['REQUEST_URI'])[4];

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Usuń grupę');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Usuń grupę');
        echo $header->render();
        ?>
        <div class='d-flex flex-column gap-4 devices py-5'>
            <h3>Czy na pewno chcesz usunąć grupę?</h3>
            <form id="deleteDeviceForm">
                <input type="hidden" name="device_id" value="<?= $deviceId ?>">
                <button type='submit' class='btn btn-danger'>Usuń grupę</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('deleteDeviceForm');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const response = await fetch(`/api/groups/delete/<?=$deviceId?>`, {
                method: 'DELETE',
            });

            const data = await response.json();

            if (data.success) {
                showToastMessage('Urządzenie zostało usunięte', true);

                form.reset();

                window.location.href = `/app/groups`;
            } else {
                showToastMessage(data.message, false);
            }
        });
    </script>
    <?php
$footer = new Footer();
echo $footer->render();