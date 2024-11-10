<?php
declare(strict_types=1);

namespace Services;

require_once './Entity/Device.php';
require_once './Entity/DeviceType.php';
require_once './Services/DeviceTypeService.php';
require_once './Lib/Database.php';

use Entity\Device;
use Entity\DeviceType;
use Lib\DatabaseConnection;
use Services\DeviceTypeService;

class DeviceService {
    private DatabaseConnection $db;
    private DeviceTypeService $deviceTypeService;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
        $this->deviceTypeService = new DeviceTypeService($db);
    }

    public function getDevices(): array {
        $sql = "SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, d.Location 
                FROM Device d";
        $result = $this->db->query($sql);

        $devices = [];
        foreach ($result as $row) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById($row['DeviceTypeID']);
            $status = $this->getDeviceStatus($row['DeviceID']);

            $device = new Device(
                $row['DeviceID'],
                $row['DeviceName'],
                $deviceType,
                $row['Location'],
                $status
            );
            $devices[] = $device;
        }
        return $devices;
    }

    public function getDeviceById(int $id): ?Device {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceID = :id';
        $params = [':id' => $id];

        try {
            $deviceData = $this->db->query($query, $params)[0];
            $deviceType = $this->deviceTypeService->getDeviceTypeById($deviceData['DeviceTypeID']);
            $status = $this->getDeviceStatus($deviceData['DeviceID']);

            return new Device(
                $deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                $deviceData['Location'],
                $status
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    private function getDeviceStatus(int $deviceId): bool {
        $query = "SELECT Value FROM DeviceParameter WHERE DeviceID = :deviceId AND ParameterID = 1"; // ParameterID = 1 oznacza Status
        $params = [':deviceId' => $deviceId];

        try {
            $result = $this->db->query($query, $params);
            if (count($result) > 0) {
                return $result[0]['Value'] === '1';
            }
            return false; // Domyślnie, jeśli nie ma wartości, zakładamy, że urządzenie jest wyłączone
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getDevicesByType(DeviceType $type): array {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceTypeID = :typeId';
        $params = [':typeId' => $type->getId()];

        return $this->queryToArray($query, $params);
    }

    public function getDevicesByLocation(string $location): array {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE Location = :location';
        $params = [':location' => $location];

        return $this->queryToArray($query, $params);
    }

    private function queryToArray(string $query, array $params = []): array {
        try {
            $devicesData = $this->db->query($query, $params);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }

        $deviceList = [];
        foreach ($devicesData as $deviceData) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById($deviceData['DeviceTypeID']);
            $status = $this->getDeviceStatus($deviceData['DeviceID']);
            
            $deviceList[] = new Device(
                $deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                $deviceData['Location'],
                $status
            );
        }

        return $deviceList;
    }
}