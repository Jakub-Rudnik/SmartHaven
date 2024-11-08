<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

// Inicjalizacja połączenia z bazą danych
$db = new DatabaseConnection();

try {
    // Pobranie urządzeń wraz z lokalizacją i stanem
    $devicesQuery = "
        SELECT d.DeviceID, d.DeviceName, d.Location, dp.Value AS Status
        FROM Device d
        JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
        WHERE dp.ParameterID = 1
        ORDER BY d.Location;";

    $devices = $db->query($devicesQuery);

    // Grupa urządzeń według lokalizacji
    $currentLocation = null;
    foreach ($devices as $device) {
        // Jeśli lokalizacja jest nowa, wyświetl nagłówek z nazwą pokoju
        if ($currentLocation !== $device['Location']) {
            if ($currentLocation !== null) {
                echo "</ul>";
            }
            $currentLocation = $device['Location'];
            echo "<h2>" . htmlspecialchars($currentLocation) . "</h2><ul>";
        }

        // Wyświetl urządzenie wraz z jego stanem i przyciskiem
        $statusText = $device['Status'] == '1' ? 'Włączone' : 'Wyłączone';
        $toggleText = $device['Status'] == '1' ? 'Wyłącz' : 'Włącz';
        $newStatus = $device['Status'] == '1' ? 0 : 1;
        
        echo "<li>";
        echo "Urządzenie: " . htmlspecialchars($device['DeviceName']) . " - <strong>" . $statusText . "</strong> ";
        echo "<button onclick=\"toggleDevice(" . $device['DeviceID'] . ", " . $newStatus . ")\">" . $toggleText . "</button>";
        echo "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}
?>

<script>
function toggleDevice(deviceId, status) {
    console.log('Przełączanie urządzenia o ID: ' + deviceId + ' na status: ' + status);
    // UI logika do zmiany stanu (można tutaj dodać np. zmianę koloru lub tekstu)
}
</script>
