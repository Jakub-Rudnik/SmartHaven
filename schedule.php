<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';

use Lib\DatabaseConnection; 

// Initiation database connection
$db = new DatabaseConnection();

try {
    // Downloading devices from the database
    $devicesQuery = "
        SELECT DeviceID, DeviceName, Location 
        FROM Device;";
    $devices = $db->query($devicesQuery);
} catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Harmonogram Urządzeń</title>
    <style>
        /* Basic form UI */
        .device-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            width: 300px;
        }
        .device-container h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .form-group {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<h1>Ustaw harmonogram urządzeń</h1>

<?php if (count($devices) > 0): ?>
    <?php foreach ($devices as $device): ?>
        <div class="device-container">
            <h3><?php echo htmlspecialchars($device['DeviceName']); ?> (<?php echo htmlspecialchars($device['Location']); ?>)</h3>
            <form action="schedule.php" method="post">
                <div class="form-group">
                    <label for="start-time-<?php echo $device['DeviceID']; ?>">Czas włączenia:</label>
                    <input type="time" id="start-time-<?php echo $device['DeviceID']; ?>" name="start_time_<?php echo $device['DeviceID']; ?>">
                </div>
                <div class="form-group">
                    <label for="end-time-<?php echo $device['DeviceID']; ?>">Czas wyłączenia:</label>
                    <input type="time" id="end-time-<?php echo $device['DeviceID']; ?>" name="end_time_<?php echo $device['DeviceID']; ?>">
                </div>
                <!-- Save button (no save function for the time being) -->
                <button type="submit" name="schedule_device" value="<?php echo $device['DeviceID']; ?>">Zapisz</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Brak urządzeń do wyświetlenia.</p>
<?php endif; ?>

</body>
</html>
