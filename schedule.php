<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

// Inicjalizacja połączenia z bazą danych
$db = new DatabaseConnection();

// Pobranie urządzeń
try {
    $devicesQuery = "SELECT id, name, state FROM Device";
    $devices = $db->query($devicesQuery);

    echo "<h2>Stan Urządzeń</h2>";
    //foreach ($devices as $device) {
    //    echo "Urządzenie: " . $device['name'] . " - Stan: " . ($device['state'] ? "Włączone" : "Wyłączone") . "<br>";
    //}
    
    foreach ($devices as $device) {
        echo "Urządzenie: " . htmlspecialchars($device['name']) . " - Stan: " . ($device['state'] ? "Włączone" : "Wyłączone");
        
        // Formularz do przełączania stanu urządzenia
        echo '<form method="post" style="display:inline;">';
        echo '<input type="hidden" name="device_id" value="' . htmlspecialchars($device['id']) . '">';
        echo '<input type="submit" value="' . ($device['state'] ? 'Wyłącz' : 'Włącz') . '">';
        echo '</form>';
        echo '<br>';
    }   

    // Pobranie włączonych urządzeń
    $onDevicesQuery = "SELECT id, name FROM Device WHERE state = 1";
    $onDevices = $db->query($onDevicesQuery);

    echo "<h2>Włączone urządzenia</h2>";
    if (count($onDevices) > 0) {
        foreach ($onDevices as $device) {
            echo "Urządzenie: " . $device['name'] . "<br>";
        }
    } else {
        echo "Brak włączonych urządzeń.";
    }

    // Pobranie wyłączonych urządzeń
    $offDevicesQuery = "SELECT id, name FROM Device WHERE state = 0";
    $offDevices = $db->query($offDevicesQuery);

    echo "<h2>Wyłączone urządzenia</h2>";
    if (count($offDevices) > 0) {
        foreach ($offDevices as $device) {
            echo "Urządzenie: " . $device['name'] . "<br>";
        }
    } else {
        echo "Brak wyłączonych urządzeń.";
    }

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}
?>