<?php

declare(strict_types=1);

require_once './Entity/Device.php';
require_once './Entity/DeviceType.php';
require_once './Services/DeviceTypeService.php';
require_once './Lib/Database.php';

class DeviceService {
    private DatabaseConnection $db;
    private DeviceTypeService $deviceTypeService;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
        $this->deviceTypeService = new DeviceTypeService($db);
    }

    public function getDevices(): array {
        $query = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location, dp.Value AS State
                  FROM Device d
                  LEFT JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID AND dp.ParameterID = 1";
        return $this->queryToArray($query);
    }

    public function getDeviceById(int $id): ?Device {
        $query = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location, dp.Value AS State
                  FROM Device d
                  LEFT JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID AND dp.ParameterID = 1
                  WHERE d.DeviceID = $id";
        
        try {
            $devices = $this->db->query($query);
            if (!empty($devices)) {
                $deviceData = $devices[0];
                $deviceType = $this->deviceTypeService->getDeviceTypeById((int) $deviceData['DeviceTypeID']);
                $state = isset($deviceData['State']) && $deviceData['State'] === '1';
                return new Device((int) $deviceData['DeviceID'], $deviceData['DeviceName'], $deviceType, $state, $deviceData['Location']);
            }
            return null;
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDeviceByName(string $name): ?Device {
        $query = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location, dp.Value AS State
                  FROM Device d
                  LEFT JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID AND dp.ParameterID = 1
                  WHERE d.DeviceName = '$name'";

        try {
            $devices = $this->db->query($query);
            if (!empty($devices)) {
                $deviceData = $devices[0];
                $deviceType = $this->deviceTypeService->getDeviceTypeById((int) $deviceData['DeviceTypeID']);
                $state = isset($deviceData['State']) && $deviceData['State'] === '1';
                return new Device((int) $deviceData['DeviceID'], $deviceData['DeviceName'], $deviceType, $state, $deviceData['Location']);
            }
            return null;
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDevicesByType(DeviceType $type): array {
        $query = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location, dp.Value AS State
                  FROM Device d
                  LEFT JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID AND dp.ParameterID = 1
                  WHERE d.DeviceTypeID = " . $type->getId();
        return $this->queryToArray($query);
    }

    public function getDevicesByState(bool $state): array {
        $stateValue = $state ? '1' : '0';
        $query = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location, dp.Value AS State
                  FROM Device d
                  LEFT JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID AND dp.ParameterID = 1
                  WHERE dp.Value = '$stateValue'";
        return $this->queryToArray($query);
    }

    private function queryToArray(string $query): array {
        try {
            $devices = $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }

        $deviceList = [];
        foreach ($devices as $deviceData) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById((int) $deviceData['DeviceTypeID']);
            $state = isset($deviceData['State']) && $deviceData['State'] === '1';
            $deviceList[] = new Device((int) $deviceData['DeviceID'], $deviceData['DeviceName'], $deviceType, $state, $deviceData['Location']);
        }

        return $deviceList;
    }


    public function assignDeviceToUser(int $userId, int $deviceId): bool {
        $query = "INSERT INTO UserDevice (UserID, DeviceID) VALUES (:userId, :deviceId)";
        $params = [
            ':userId' => $userId,
            ':deviceId' => $deviceId
        ];

        try {
            $this->db->executeQuery($query, $params);
            return true;
        } catch (Exception $e) {
            echo 'Error assigning device to user: ' . $e->getMessage();
            return false;
        }
    }
}
?>
