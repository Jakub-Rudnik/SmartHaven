<?php

namespace Pages;


use Lib\DatabaseConnection;
use Services\DeviceService;
use UI\Header;
use UI\Navbar;

$db = new DatabaseConnection();
$deviceService = new DeviceService($db);
$devices = $deviceService->getDevicesGroupedByLocations();
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj harmonogram | SmartHaven</title>
    <link rel="stylesheet" href="/styles/main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>
<body class="d-flex flex-md-row p-1 p-md-3 gap-3 w-100 vh-100 overflow-hidden">
<?php
$navbar = new Navbar($currentPath);
echo $navbar->render();
?>
<main class="card bg-dark-subtle flex-grow-1 p-4 overflow-y-auto" style="max-height: 100vh">
    <?php
    $header = new Header('Dodaj harmonogram');
    echo $header->render();
    if (count($devices) > 0): ?>
        <form action='/api/save-schedule' class='mt-5' method='post'>
            <div class='mb-3'>
                <label for='device-select' class='form-label'>Wybierz urządzenie:</label>
                <select id='device-select' class='form-select' name='device_id'>
                    <option value=''>Wybierz urządzenie</option>
                    <? foreach ($devices as $device): ?>
                        <option value='<?= htmlspecialchars($device->getId()) ?>'>
                            <?= htmlspecialchars($device->getName()) . htmlspecialchars($device->getRoom() ?? 'Nieznany pokój') ?>
                        </option>
                    <? endforeach ?>
                </select>
            </div>
            <div class='mb-3'>
                <label for='start-time' class='form-label'>Czas włączenia:</label>
                <input class='form-control' type='time' id='start-time' name='start_time'>
            </div>
            <div class='mb-3'>
                <label for='end-time' class='form-label'>Czas wyłączenia:</label>
                <input class='form-control' type='time' id='end-time' name='end_time'>
            </div>
            <div class='mb-3'>
                <label>Wybierz dni cyklu:</label><br>
                <div class='form-check'>
                    <input class='form-check-input' type='checkbox' id='everyday' name='cycle_days[]'
                           value='codziennie'>
                    <label class='form-check-label' for='everyday'>Codziennie</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='monday' type='checkbox' name='cycle_days[]'
                           value='poniedziałek'>
                    <label class='form-check-label' for='monday'>Poniedziałek</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='tuesday' type='checkbox' name='cycle_days[]'
                           value='wtorek'>
                    <label class='form-check-label' for='tuesday'>Wtorek</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='wednesday' type='checkbox' name='cycle_days[]'
                           value='środa'>
                    <label class='form-check-label' for='wednesday'>Środa</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='thursday' type='checkbox' name='cycle_days[]'
                           value='czwartek'>
                    <label class='form-check-label' for='thursday'>Czwartek</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='friday' type='checkbox' name='cycle_days[]'
                           value='piątek'>
                    <label class='form-check-label' for='friday'>Piątek</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='saturday' type='checkbox' name='cycle_days[]'
                           value='sobota'>
                    <label class='form-check-label' for='saturday'>Sobota</label>
                </div>

                <div class='form-check'>
                    <input class='form-check-input weekdays' id='sunday' type='checkbox' name='cycle_days[]'
                           value='niedziela'>
                    <label class='form-check-label' for='sunday'>Niedziela</label>
                </div>

            </div>
            <button class='btn btn-primary' type='submit' name='schedule_device' id='submit-button' disabled>Zapisz
            </button>
        </form>
    <?php else: ?>
        <p>Brak urządzeń do wyświetlenia.</p>
    <?php endif ?>
    <script src='/Js/ScheduleValidation.js'></script>


