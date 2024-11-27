<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';
require_once './Services/UsersService.php';

session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$db = new DatabaseConnection();
$usersService = new UsersService($db);

// Obsługa wylogowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    $usersService->logoutUser();
    header('Location: login.php');
    exit();
}

$devicesService = new DeviceService($db);
$devicesTypeService = new DeviceTypeService($db);

$devices = $devicesService->getDevices();

echo '<h2>Welcome, ' . htmlspecialchars($_SESSION['username'] ?? '') . '!</h2>';

// Przycisk wylogowania
echo '<form method="POST">';
echo '<input type="submit" name="logout" value="Wyloguj">';
echo '</form>';

// Wyświetlanie urządzeń w formie tabelki
echo '<h2>Devices</h2>';
echo '<table border="1">';
echo '<tr><th>Device ID</th><th>Device Name</th><th>Device Type</th><th>Location</th><th>State</th></tr>';
foreach ($devices as $device) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($device->getId()) . '</td>';
    echo '<td>' . htmlspecialchars($device->getName()) . '</td>';
    echo '<td>' . htmlspecialchars($device->getType()->getName()) . '</td>';
    echo '<td>' . htmlspecialchars($device->getLocation() ?? '') . '</td>';
    echo '<td>' . ($device->getState() ? 'On' : 'Off') . '</td>';
    echo '</tr>';
}
echo '</table>';

?>
