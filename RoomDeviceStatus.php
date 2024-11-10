<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

use Services\DeviceService;
use Services\DeviceTypeService;
use Lib\DatabaseConnection;

// Initiation database connection
$db = new DatabaseConnection();

try {
    // Download of equipment with location and status
    $devicesQuery = "
        SELECT d.DeviceID, d.DeviceName, d.Location, dp.Value AS Status
        FROM Device d
        JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
        WHERE dp.ParameterID = 1
        ORDER BY d.Location;";

    $devices = $db->query($devicesQuery);

    // Equipment group by location
    $currentLocation = null;
    foreach ($devices as $device) {
        // If the location is new, display a header with the name of the room
        if ($currentLocation !== $device['Location']) {
            if ($currentLocation !== null) {
                echo "Brak przydzielonego pokoju</ul>";
            }
            $currentLocation = $device['Location'];
            echo "<h2>" . htmlspecialchars($currentLocation) . "</h2><ul>";
        }

        // Display the device with its status and button
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
}
</script>
