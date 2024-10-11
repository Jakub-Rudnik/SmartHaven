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
        $query = 'SELECT id, name, type, state FROM Device';

        return $this->queryToArray($query);
    }

    public function getDeviceById(int $id): Device {
        $query = 'SELECT id, name, state, type FROM Device WHERE id = ' . $id;

        try {
            $device = $this->db->query($query)[0];
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);

        return new Device($device['id'], $device['name'], $deviceType , (bool) $device['state']);
    }

    public function getDeviceByName(string $name): Device | null {
        $query = 'SELECT id, name, state, type FROM Device WHERE name = "' . $name . '"';

        try {
            $device = $this->db->query($query)[0];
            $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);
            return new Device($device['id'], $device['name'], $deviceType, (bool) $device['state']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return null;
    }

    public function getDeviceByType(DeviceType $type): array {
        $query = 'SELECT id, name, type, state FROM Device WHERE type = "' . $type->getId() . '"';

        return $this->queryToArray($query);
    }

    public function getDeviceByState(bool $state): array {
        $query = 'SELECT id, name, type, state FROM Device WHERE state = "' . (int) $state . '"';

        return $this->queryToArray($query);
    }

    /**
     * @param string $query
     * @return array
     */
    public function queryToArray(string $query): array
    {
        try {
            $devices = $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $deviceList = [];
        foreach ($devices as $device) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById($device['type']);

            $deviceList[] = new Device($device['id'], $device['name'], $deviceType, (bool) $device['state']);
        }

        return $deviceList;
    }

}