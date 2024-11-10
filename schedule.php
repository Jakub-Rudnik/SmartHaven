<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';

use Lib\DatabaseConnection; 

// Initiation database connection
$db = new DatabaseConnection();

try {
    // Downloading devices from the database
    $devicesQuery = "
        SELECT DeviceID, DeviceName, 
               COALESCE(Location, 'Brak lokalizacji') AS Location 
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
    <!-- Link do CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Link do jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Link do JS Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>

<h1>Ustaw harmonogram urządzeń</h1>

<?php if (count($devices) > 0): ?>
    <form action="schedule.php" method="post">
        <div class="form-group">
            <label for="device-select">Wybierz urządzenie:</label>
            <select id="device-select" name="device_id" class="device-select" style="width: 100%;">
                <option value="">Wybierz urządzenie</option>
                <?php foreach ($devices as $device): ?>
                    <option value="<?php echo $device['DeviceID']; ?>">
                        <?php echo htmlspecialchars($device['DeviceName']); ?> (<?php echo htmlspecialchars($device['Location']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="start-time">Czas włączenia:</label>
            <input type="time" id="start-time" name="start_time">
        </div>
        <div class="form-group">
            <label for="end-time">Czas wyłączenia:</label>
            <input type="time" id="end-time" name="end_time">
        </div>
        <button type="submit" name="schedule_device">Zapisz</button>
    </form>
<?php else: ?>
    <p>Brak urządzeń do wyświetlenia.</p>
<?php endif; ?>

<script>
    // Inicjalizacja Select2
    $(document).ready(function() {
        $('.device-select').select2({
            placeholder: "Wybierz urządzenie",
            allowClear: true
        });
    });
</script>

</body>
</html>
