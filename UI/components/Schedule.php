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
        $html .= "<div id='response-message'></div>";

        if (count($this->devices) > 0) {
            $html .= "
            <form action='API/saveSchedule.php' method='post'>
                <div class='form-group'>
                    <label for='device-select'>Wybierz urządzenie:</label>
                    <select id='device-select' name='device_id'>
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

        // Dodanie obsługi walidacji czasu
        $html .= "
            <script src='js/ScheduleValidation.js'></script>
        ";

        return $html;
    }
}
