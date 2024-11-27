<?php

namespace UI\components;

use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\DeviceService;

class Schedule implements UIElement
{
    private DeviceService $deviceService;
    private DatabaseConnection $db;
    private array $devices = [];

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
        $this->deviceService = new DeviceService($db);
        $this->devices = $this->deviceService->getDevicesGroupedByLocations();
    }

    public function render(): string
    {
        $html = "<h1>Ustaw harmonogram urządzeń</h1>";

        if (count($this->devices) > 0) {
            $html .= "
            <form action='schedule.php' method='post'>
                <div class='form-group'>
                    <label for='device-select'>Wybierz urządzenie:</label>
                    <select id='device-select' name='device_id' class='device-select' style='width: 100%;'>
                        <option value=''>Wybierz urządzenie</option>";
            
            foreach ($this->devices as $device) {
                $html .= "<option value='".htmlspecialchars($device->getId())."'>".
                    htmlspecialchars($device->getName())." (".htmlspecialchars($device->getRoom() ?? 'Nieznany pokój').")
                    </option>";
            }

            $html .= "
                    </select>
                </div>
                <div class='form-group'>
                    <label for='start-time'>Czas włączenia:</label>
                    <input type='time' id='start-time' name='start_time'>
                </div>
                <div class='form-group'>
                    <label for='end-time'>Czas wyłączenia:</label>
                    <input type='time' id='end-time' name='end_time'>
                </div>
                <div class='form-group'>
                    <label>Wybierz dni cyklu:</label><br>
                    <label><input type='checkbox' id='everyday' name='cycle_days[]' value='codzien'> Codziennie</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='poniedziałek' class='weekdays'> Poniedziałek</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='wtorek' class='weekdays'> Wtorek</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='środa' class='weekdays'> Środa</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='czwartek' class='weekdays'> Czwartek</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='piątek' class='weekdays'> Piątek</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='sobota' class='weekdays'> Sobota</label><br>
                    <label><input type='checkbox' name='cycle_days[]' value='niedziela' class='weekdays'> Niedziela</label><br>
                </div>
                <button type='submit' name='schedule_device' id='submit-button' disabled>Zapisz</button>
            </form>";
        } else {
            $html .= "<p>Brak urządzeń do wyświetlenia.</p>";
        }

        $html .= "
            <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
            <script>
                $(document).ready(function() {
                    // Initialize Select2
                    $('.device-select').select2({
                        placeholder: 'Wybierz urządzenie',
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
        ";

        return $html;
    }

    public function handleFormSubmission(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_device'])) {
            try {
                $deviceID = $_POST['device_id'];
                $startTime = $_POST['start_time'];
                $endTime = $_POST['end_time'];
                $cycleDays = isset($_POST['cycle_days']) ? implode(',', $_POST['cycle_days']) : null;

                if (empty($deviceID) || empty($startTime) || empty($endTime) || empty($cycleDays)) {
                    throw new \Exception("Wszystkie pola są wymagane!");
                }

                $currentDate = date('Y-m-d');
                $startDateTime = $currentDate . ' ' . $startTime . ':00';
                $endDateTime = $currentDate . ' ' . $endTime . ':00';

                $parameterID = 1;

                $insertQuery = "
                    INSERT INTO Schedule (DeviceID, StartTime, EndTime, ParameterID, RepeatPattern, ScheduleState) 
                    VALUES (:device_id, :start_time, :end_time, :parameter_id, :repeat_pattern, 0)";
                
                $params = [
                    ':device_id' => $deviceID,
                    ':start_time' => $startDateTime,
                    ':end_time' => $endDateTime,
                    ':parameter_id' => $parameterID,
                    ':repeat_pattern' => $cycleDays,
                ];

                $this->db->execute($insertQuery, $params);

                return "Harmonogram został zapisany pomyślnie!";
            } catch (\Exception $e) {
                return "Błąd: " . $e->getMessage();
            }
        }
        return "";
    }
}
