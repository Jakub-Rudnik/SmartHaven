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


    public function createDevice(string $name, int $typeId, ?string $location = null): bool {
        $query = "INSERT INTO Device (DeviceName, DeviceTypeID, Location) VALUES (:name, :typeId, :location)";

        try {
            $params = [
                ':name' => $name,
                ':typeId' => $typeId,
                ':location' => $location
            ];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function deleteDevice(int $id): bool {
        $query = "DELETE FROM Device WHERE DeviceID = :id";

        try {
            $params = [':id' => $id];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateDevice(int $id, string $name, int $typeId, ?string $location = null): bool {
        $query = "UPDATE Device SET DeviceName = :name, DeviceTypeID = :typeId, Location = :location WHERE DeviceID = :id";

        try {
            $params = [
                ':id' => $id,
                ':name' => $name,
                ':typeId' => $typeId,
                ':location' => $location
            ];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateDeviceName(int $id, string $name): bool {
        $query = "UPDATE Device SET DeviceName = :name WHERE DeviceID = :id";

        try {
            $params = [
                ':id' => $id,
                ':name' => $name
            ];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateDeviceType(int $id, int $typeId): bool {
        $query = "UPDATE Device SET DeviceTypeID = :typeId WHERE DeviceID = :id";

        try {
            $params = [
                ':id' => $id,
                ':typeId' => $typeId
            ];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateDeviceLocation(int $id, ?string $location): bool {
        $query = "UPDATE Device SET Location = :location WHERE DeviceID = :id";

        try {
            $params = [
                ':id' => $id,
                ':location' => $location
            ];
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

}