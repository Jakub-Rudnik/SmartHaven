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
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device';

        return $this->queryToArray($query);
    }

    public function getDeviceById(int $id): ?Device {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceID = ' . $id;

        try {
            $deviceData = $this->db->query($query)[0];
            $deviceType = $this->deviceTypeService->getDeviceTypeById($deviceData['DeviceTypeID']);
            return new Device(
                $deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                $deviceData['Location']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDeviceByName(string $name): ?Device {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceName = "' . $name . '"';

        try {
            $deviceData = $this->db->query($query)[0];
            $deviceType = $this->deviceTypeService->getDeviceTypeById($deviceData['DeviceTypeID']);
            return new Device(
                $deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                $deviceData['Location']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDevicesByType(DeviceType $type): array {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceTypeID = ' . $type->getId();

        return $this->queryToArray($query);
    }

    public function getDevicesByLocation(string $location): array {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE Location = "' . $location . '"';

        return $this->queryToArray($query);
    }

    private function queryToArray(string $query): array {
        try {
            $devicesData = $this->db->query($query);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }

        $deviceList = [];
        foreach ($devicesData as $deviceData) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById($deviceData['DeviceTypeID']);
            $deviceList[] = new Device(
                $deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                $deviceData['Location']
            );
        }

        return $deviceList;
    }
}