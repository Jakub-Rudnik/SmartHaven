<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';

use Lib\DatabaseConnection; 

// Initiation database connection
$db = new DatabaseConnection();

// Initialize $devices as an empty array to avoid "undefined variable" error
$devices = [];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_device'])) {
    try {
        // Pobranie danych z formularza
        $deviceID = $_POST['device_id'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $cycleDays = isset($_POST['cycle_days']) ? implode(',', $_POST['cycle_days']) : null;

        // Walidacja danych
        if (empty($deviceID) || empty($startTime) || empty($endTime) || empty($cycleDays)) {
            throw new Exception("Wszystkie pola są wymagane!");
        }

        // Pobieramy dzisiejszą datę
        $currentDate = date('Y-m-d');

        // Dodajemy datę do godziny (np. 17:33 -> 2024-11-21 17:33:00)
        $startDateTime = $currentDate . ' ' . $startTime . ':00';
        $endDateTime = $currentDate . ' ' . $endTime . ':00';

        $parameterID = 1;

        // Przygotowanie zapytania SQL do dodania harmonogramu
        $insertQuery = "
            INSERT INTO Schedule (DeviceID, StartTime, EndTime, ParameterID, RepeatPattern, ScheduleState) 
            VALUES (:device_id, :start_time, :end_time, :parameter_id, :repeat_pattern, 0)";
        
        // Parametry do bazy danych
        $params = [
            ':device_id' => $deviceID,
            ':start_time' => $startDateTime,
            ':end_time' => $endDateTime,
            ':parameter_id' => $parameterID,
            ':repeat_pattern' => $cycleDays,
        ];

        // Wykonanie zapytania do bazy danych
        $db->execute($insertQuery, $params);

        echo "Harmonogram został zapisany pomyślnie!";
    } catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
    }
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
        button:disabled {
            background-color: #ccc;
        }
    </style>
    <!-- Link to CSS Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Link to jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Link to JS Select2 -->
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
        
        <!-- Add of cycle_days-->
        <div class="form-group">
            <label>Wybierz dni cyklu:</label><br>
            <label><input type="checkbox" id="everyday" name="cycle_days[]" value="codzien"> Codziennie</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="poniedziałek" class="weekdays"> Poniedziałek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="wtorek" class="weekdays"> Wtorek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="środa" class="weekdays"> Środa</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="czwartek" class="weekdays"> Czwartek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="piątek" class="weekdays"> Piątek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="sobota" class="weekdays"> Sobota</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="niedziela" class="weekdays"> Niedziela</label><br>
        </div>
        
        <button type="submit" name="schedule_device" id="submit-button" disabled>Zapisz</button>
    </form>
<?php else: ?>
    <p>Brak urządzeń do wyświetlenia.</p>
<?php endif; ?>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.device-select').select2({
            placeholder: "Wybierz urządzenie",
            allowClear: true
        });

        // Check if 'Codziennie' is checked
        $('#everyday').change(function() {
            if ($(this).prop('checked')) {
                $('.weekdays').prop('checked', true);  // Check all days
            } else {
                $('.weekdays').prop('checked', false);  // Uncheck all days
            }
            toggleSubmitButton();
        });

        // Check if individual days are selected
        $('.weekdays').change(function() {
            if ($('.weekdays:checked').length === 7) {
                // If all days are selected, check ‘Codziennie’
                $('#everyday').prop('checked', true);
            } else {
                // If not all days are selected, uncheck ‘Codziennie’
                $('#everyday').prop('checked', false);
            }
            toggleSubmitButton();
        });

        // Function to check if all required fields are filled
        function toggleSubmitButton() {
            const deviceSelected = $('#device-select').val() !== '';
            const startTimeSelected = $('#start-time').val() !== '';
            const endTimeSelected = $('#end-time').val() !== '';
            const cycleDaysSelected = ($('#everyday').prop('checked') || $('.weekdays:checked').length > 0);

            // Validate start and end time
            const startTime = $('#start-time').val();
            const endTime = $('#end-time').val();
            const timeValid = startTime < endTime;

            // Enable or disable submit button
            if (deviceSelected && startTimeSelected && endTimeSelected && cycleDaysSelected && timeValid) {
                $('#submit-button').prop('disabled', false);  // Enable submit button
            } else {
                $('#submit-button').prop('disabled', true);  // Disable submit button
            }
        }

        // Validate when fields are changed
        $('#device-select, #start-time, #end-time').change(function() {
            toggleSubmitButton();
        });
    });
</script>

</body>
</html>
