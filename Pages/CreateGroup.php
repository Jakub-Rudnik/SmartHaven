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

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Dodaj grupę');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Dodaj grupę');
        echo $header->render();
        ?>
        <div class='d-grid gap-4 devices py-5'>
            <form id="createGroupForm">
                <div class='mb-3'>
                    <label for='group-name' class='form-label'>Nazwa grupy:</label>
                    <input name="group_name" id="group-name" class="form-control" required>
                </div>
                <button type='submit' class='btn btn-primary'>Dodaj grupę</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('createGroupForm');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch('/api/groups/create', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToastMessage('Grupa zostało dodane', true);

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