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

    // public function getDevices(): array {
    //     $query = 'SELECT id, name, type, state FROM Device';

    //     return $this->queryToArray($query);
    // }

    // public function getDeviceById(int $id): Device {
    //     $query = 'SELECT id, name, state, type FROM Device WHERE id = ' . $id;

    //     try {
    //         $device = $this->db->query($query)[0];
    //     } catch (Exception $e) {
    //         echo $e->getMessage();
    //     }

    //     $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);

    //     return new Device($device['id'], $device['name'], $deviceType , (bool) $device['state']);
    // }
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
/////////////////////////    

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