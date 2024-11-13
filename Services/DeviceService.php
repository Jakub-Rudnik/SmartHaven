<?php
declare(strict_types=1);

require_once './Entity/Device.php';
require_once './Entity/DeviceType.php';
require_once './Services/DeviceTypeService.php';
require_once './Lib/Database.php';

class DeviceService {
    private DatabaseConnection $db;
    private $deviceTypeService;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
        $this->deviceTypeService = new DeviceTypeService($db);
    }

    public function getDevices(): array {
        $query = 'SELECT DeviceID AS id, DeviceName AS name, DeviceTypeID AS type, Location AS state FROM Device';
        return $this->queryToArray($query);
    }
    
    public function getDeviceById(int $id): Device {
        $query = 'SELECT DeviceID AS id, DeviceName AS name, Location AS state, DeviceTypeID AS type FROM Device WHERE DeviceID = ' . $id;
        try {
            $device = $this->db->query($query)[0];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    
        $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);
        return new Device($device['id'], $device['name'], $deviceType, (bool) $device['state']);
    }

public function updateDeviceState(int $deviceId, int $newState): void {
    $query = 'SELECT State FROM Device WHERE DeviceID = :deviceId';
    $currentState = $this->db->query($query, ['deviceId' => $deviceId])[0]['State'];

    if ($currentState !== $newState) {
        $updateQuery = 'UPDATE Device SET State = :newState WHERE DeviceID = :deviceId';
        $this->db->query($updateQuery, ['newState' => $newState, 'deviceId' => $deviceId]);

        $this->logStateChange($deviceId, $newState);
    }
}


private function logStateChange(int $deviceId, int $newState): void {
    $logQuery = 'INSERT INTO Notifications (DeviceID, NewState, Timestamp) VALUES (:deviceId, :newState, NOW())';
    $this->db->query($logQuery, ['deviceId' => $deviceId, 'newState' => $newState]);
}


public function getRecentNotifications(): array {
    $query = 'SELECT * FROM Notifications WHERE Timestamp >= NOW() - INTERVAL 5 SECOND';
    return $this->db->query($query);
}

public function getDeviceByName(string $name): Device | null {
    $query = 'SELECT DeviceID AS id, DeviceName AS name, DeviceTypeID AS type, Location AS state FROM Device WHERE DeviceName = "' . $name . '"';

    try {
        $device = $this->db->query($query)[0];
        $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);
        return new Device($device['id'], $device['name'], $deviceType, (bool)$device['state']);
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    return null;
}

public function getDeviceByType(DeviceType $type): array {
    $query = 'SELECT DeviceID AS id, DeviceName AS name, DeviceTypeID AS type, Location AS state FROM Device WHERE DeviceTypeID = ' . $type->getId();
    return $this->queryToArray($query);
}


public function getDeviceByState(bool $state): array {
    $query = 'SELECT DeviceID AS id, DeviceName AS name, DeviceTypeID AS type, Location AS state FROM Device WHERE state = :state';
    return $this->queryToArray($query, ['state' => (int) $state]);
}

//    public function getDevicesWithoutLocation(): array
//    {
//        $devicesWithoutLocation = [];

//        try {
//            $query = "SELECT DeviceName FROM Device WHERE Location IS NULL";
//            $result = $this->db->query($query);

//            foreach ($result as $row) {
//                $devicesWithoutLocation[] = $row['DeviceName'];
//            }
//        } catch (Exception $e) {
//            echo "Error: " . $e->getMessage();
//        }
//        return $devicesWithoutLocation;
//    }
    /**
     * @param string $query
     * @return array
     */
    public function queryToArray(string $query, array $params = []): array {
        try {
            $devices = $this->db->query($query, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    
        $deviceList = [];
        foreach ($devices as $device) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);
            $deviceList[] = new Device($device['id'], $device['name'], $deviceType, (bool) $device['state']);
        }
    
        return $deviceList;
    }
    

}