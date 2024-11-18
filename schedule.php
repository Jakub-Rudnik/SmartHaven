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
            <label><input type="checkbox" id="everyday" name="cycle_days[]" value="everyday"> Codziennie</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="monday" class="weekdays"> Poniedziałek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="tuesday" class="weekdays"> Wtorek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="wednesday" class="weekdays"> Środa</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="thursday" class="weekdays"> Czwartek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="friday" class="weekdays"> Piątek</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="saturday" class="weekdays"> Sobota</label><br>
            <label><input type="checkbox" name="cycle_days[]" value="sunday" class="weekdays"> Niedziela</label><br>
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

            // Show error message if time is invalid
            if (!timeValid && startTimeSelected && endTimeSelected) {
                alert("Czas włączenia musi być wcześniejszy niż czas wyłączenia.");
            }
        }

        // Run check when any input changes
        $('#device-select, #start-time, #end-time, .weekdays').change(function() {
            toggleSubmitButton();
        });
    });
</script>


</body>
</html>
