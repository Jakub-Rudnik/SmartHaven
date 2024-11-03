<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

// Inicjalizacja połączenia z bazą danych
$db = new DatabaseConnection();

try {
    // Pobranie włączonych urządzeń
    $onDevicesQuery = "
        SELECT d.DeviceID, d.DeviceName 
        FROM Device d
        JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
        WHERE dp.ParameterID = 1 AND dp.Value = '1';"; // 1 oznacza włączone urządzenie

    $onDevices = $db->query($onDevicesQuery);

    echo "<h2>Włączone urządzenia</h2>";
    if (count($onDevices) > 0) {
        foreach ($onDevices as $device) {
            echo "Urządzenie: " . htmlspecialchars($device['DeviceName']) . "<br>";
        }
    } else {
        echo "Brak włączonych urządzeń.";
    }

    // Pobranie wyłączonych urządzeń
    $offDevicesQuery = "
        SELECT d.DeviceID, d.DeviceName 
        FROM Device d
        JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
        WHERE dp.ParameterID = 1 AND dp.Value = '0';"; // 0 oznacza wyłączone urządzenie

    $offDevices = $db->query($offDevicesQuery);

    echo "<h2>Wyłączone urządzenia</h2>";
    if (count($offDevices) > 0) {
        foreach ($offDevices as $device) {
            echo "Urządzenie: " . htmlspecialchars($device['DeviceName']) . "<br>";
        }
    } else {
        echo "Brak wyłączonych urządzeń.";
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}
?>
