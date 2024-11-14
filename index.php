<?php
declare(strict_types=1);

require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

use Services\DeviceService;
use Services\DeviceTypeService;
use Lib\DatabaseConnection;

// Utworzenie instancji bazy danych
$db = new DatabaseConnection();

// Utworzenie instancji serwisów
$devicesService = new DeviceService($db);
$devicesTypeService = new DeviceTypeService($db);

// Pobieranie urządzeń
$devices = $devicesService->getDevices();

echo '<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home Devices</title>
    <style>
        .device-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 10px;
            display: inline-block;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .device-card h3 {
            margin: 10px 0;
        }
        .device-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h2>Devices</h2>';

foreach ($devices as $device) {
    echo '<div class="device-card">';
    echo '<h3>' . htmlspecialchars($device->getName()) . '</h3>';
    echo '<p><strong>Stan:</strong> ' . ($device->getState() ? 'Włączony' : 'Wyłączony') . '</p>';
    echo '<p><strong>Pokój:</strong> ' . ($device->getRoom() ? htmlspecialchars($device->getRoom()) : 'Nie przypisano') . '</p>';
    echo '</div>';
}

echo '</body>
</html>';
?>
