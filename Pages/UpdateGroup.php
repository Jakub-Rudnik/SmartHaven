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
$groupService = new GroupService($db);

$groupId = (int)explode('/', $_SERVER['REQUEST_URI'])[4];

$group = $groupService->getGroupById($groupId);

$currentPath = $_SERVER['REQUEST_URI'];

$head = new Head('Edytuj grupę');
echo $head->render();

$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
    <main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
        <?php
        $header = new Header('Edytuj grupę');
        echo $header->render();
        ?>
        <div class='d-grid gap-4 devices py-5'>
            <form id="editGroupForm">
                <div class='mb-3'>
                    <label for='group-name' class='form-label'>Nazwa grupy:</label>
                    <input name="group_name" id="group-name" class="form-control" required
                           value="<?= $group->getGroupName() ?>">
                </div>
                <button type='submit' class='btn btn-primary'>Edytuj grupę</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('editGroupForm');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(form);
            const response = await fetch('/api/groups/update/<?=$groupId?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToastMessage('Grupa zostało zaktualizowane', true);

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