<?php

namespace UI\components;

use Interfaces\UIElement;
use Lib\DatabaseConnection;
use Services\DeviceService;

class AddSchedule implements UIElement
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
        $header = new Header('Dodaj harmonogram');
        $html = $header->render();

        $html .= "<div id='response-message'></div>";

        if (count($this->devices) > 0) {
            $html .= "
            <form action='/api/save-schedule' class='mt-5' method='post'>
                <div class='mb-3'>  
                    <label for='device-select' class='form-label'>Wybierz urządzenie:</label>
                    <select id='device-select' class='form-select' name='device_id'>
                        <option value=''>Wybierz urządzenie</option>";

            foreach ($this->devices as $device) {
                $html .= "<option value='" . htmlspecialchars($device->getId()) . "'>" .
                    htmlspecialchars($device->getName()) . " (" . htmlspecialchars($device->getRoom() ?? 'Nieznany pokój') . ") 
                    </option>";
            }

            $html .= "
                    </select>
                </div>
                <div class='mb-3'>
                    <label for='start-time' class='form-label'>Czas włączenia:</label>
                    <input class='form-control' type='time' id='start-time' name='start_time'>
                </div>
                <div class='mb-3'>
                    <label for='end-time' class='form-label'>Czas wyłączenia:</label>
                    <input class='form-control' type='time' id='end-time' name='end_time'>
                </div>
                <div class='mb-3'>
                    <label>Wybierz dni cyklu:</label><br>
                    <div class='form-check'>
                        <input class='form-check-input' type='checkbox' id='everyday' name='cycle_days[]' value='codziennie'>
                        <label class='form-check-label' for='everyday'>Codziennie</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='monday' type='checkbox' name='cycle_days[]' value='poniedziałek'>
                        <label class='form-check-label' for='monday'>Poniedziałek</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='tuesday' type='checkbox' name='cycle_days[]' value='wtorek'>
                        <label class='form-check-label' for='tuesday'>Wtorek</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='wednesday' type='checkbox' name='cycle_days[]' value='środa'>
                        <label class='form-check-label' for='wednesday'>Środa</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='thursday' type='checkbox' name='cycle_days[]' value='czwartek'>
                        <label class='form-check-label' for='thursday'>Czwartek</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='friday' type='checkbox' name='cycle_days[]' value='piątek'>
                        <label class='form-check-label' for='friday'>Piątek</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='saturday' type='checkbox' name='cycle_days[]' value='sobota'>
                        <label class='form-check-label' for='saturday'>Sobota</label>
                    </div>
                    
                    <div class='form-check'>
                        <input class='form-check-input weekdays' id='sunday' type='checkbox' name='cycle_days[]' value='niedziela'>
                        <label class='form-check-label' for='sunday'>Niedziela</label>
                    </div>
                    
                </div>
                <button class='btn btn-primary' type='submit' name='schedule_device' id='submit-button' disabled>Zapisz</button>
            </form>";
        } else {
            $html .= "<p>Brak urządzeń do wyświetlenia.</p>";
        }

        $html .= "
            <script src='/Js/ScheduleValidation.js'></script>
        ";

        return $html;
    }
}
