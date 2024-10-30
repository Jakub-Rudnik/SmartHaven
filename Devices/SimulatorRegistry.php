<?php
class SimulatorRegistry {
    private $simulators = [];
    private $devices = [];

    public function registerSimulator($type, $className) {
        $this->simulators[$type] = $className;
    }

    public function addDevice($type, $room) {
        if (isset($this->simulators[$type])) {
            $deviceId = uniqid($type . "_");
            $className = $this->simulators[$type];
            $this->devices[$deviceId] = new $className($room);
            return $deviceId;
        }
        return null;
    }

    public function getDeviceStatus($deviceId) {
        if (isset($this->devices[$deviceId])) {
            return $this->devices[$deviceId]->getStatus();
        }
        return null;
    }

    public function listDevices() {
        return array_keys($this->devices);
    }
}

$registry = new SimulatorRegistry();
$registry->registerSimulator("ac", "AirConditioner");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$data = json_decode(file_get_contents("php://input"), true);

switch ($requestMethod) {
    case "POST":
        if (isset($data["type"], $data["room"]) && $data["type"] == "ac") {
            $deviceId = $registry->addDevice($data["type"], $data["room"]);
            if ($deviceId) {
                echo json_encode(["message" => "Dodano nowe urządzenie", "device_id" => $deviceId]);
            } else {
                echo json_encode(["error" => "Nieobsługiwany typ urządzenia"]);
            }
        } else {
            echo json_encode(["error" => "Niepoprawne dane wejściowe"]);
        }
        break;

    case "GET":
        if (isset($_GET["device_id"])) {
            $status = $registry->getDeviceStatus($_GET["device_id"]);
            if ($status) {
                echo json_encode($status);
            } else {
                echo json_encode(["error" => "Urządzenie nie zostało znalezione"]);
            }
        } else {
            echo json_encode(["devices" => $registry->listDevices()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Metoda nieobsługiwana"]);
        break;
}
?>