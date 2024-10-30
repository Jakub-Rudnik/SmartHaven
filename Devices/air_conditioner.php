<?php
header("Content-Type: application/json");

class AirConditioner {
    private $state = "off";
    private $temperature = 24;
    private $mode = "cool";
    private $room = NULL;  // Domyślnie NULL, jeśli nie przypisano pokoju

    public function __construct($room) {
        $this->room = $room;
    }

    public function getStatus() {
        return [
            "state" => $this->state,
            "temperature" => $this->temperature,
            "mode" => $this->mode,
            "room" => $this->room
        ];
    }

    public function setState($state) {
        if (in_array($state, ["on", "off"])) {
            $this->state = $state;
            return true;
        }
        return false;
    }

    public function setTemperature($temperature) {
        if (is_numeric($temperature) && $temperature >= 16 && $temperature <= 30) {
            $this->temperature = $temperature;
            return true;
        }
        return false;
    }

    public function setMode($mode) {
        if (in_array($mode, ["cool", "heat", "fan"])) {
            $this->mode = $mode;
            return true;
        }
        return false;
    }
}

?>
