<?php

declare(strict_types=1);

namespace Services;

use Entity\Device;
use Entity\DeviceType;
use Exception;
use Lib\DatabaseConnection;

class DeviceService
{
    private DatabaseConnection $db;
    private DeviceTypeService $deviceTypeService;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
        $this->deviceTypeService = new DeviceTypeService($db);
    }

    public function getDevices(): array
    {
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
                '',
                $status,
                $row['Location'],
            );
            $devices[] = $device;
        }
        return $devices;
    }

    public function getDeviceById(int $id): ?Device
    {
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
                '',
                $status,
                $deviceData['Location'],
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    private function getDeviceStatus(int $deviceId): bool
    {
        $query = "SELECT Value FROM DeviceParameter WHERE DeviceID = :deviceId AND ParameterID = 1"; // ParameterID = 1 oznacza Status
        $params = [':deviceId' => $deviceId];

        try {
            $result = $this->db->query($query, $params);
            if (count($result) > 0) {
                return $result[0]['Value'] === '1';
            }
            return false; // Domyślnie, jeśli nie ma wartości, zakładamy, że urządzenie jest wyłączone
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function getDevicesByType(DeviceType $type): array
    {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceTypeID = :typeId';
        $params = [':typeId' => $type->getId()];

        return $this->queryToArray($query, $params);
    }

    public function getDevicesByLocation(string $location): array
    {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE Location = :location';
        $params = [':location' => $location];

        return $this->queryToArray($query, $params);
    }

    public function getDevicesGroupedByLocations(): array
    {
        $devicesQuery = "
        SELECT d.DeviceID, d.DeviceName, d.DeviceTypeID, 
               COALESCE(d.Location, 'Brak przydzielonego pokoju') AS Location, 
               dp.Value AS Status
        FROM Device d
        JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
        WHERE dp.ParameterID = 1
        ORDER BY d.Location;";

        return $this->queryToArray($devicesQuery, []);
    }

    private function queryToArray(string $query, array $params = []): array
    {
        try {
            $devicesData = $this->db->query($query, $params);
        } catch (Exception $e) {
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
                '',
                $status,
                $deviceData['Location']
            );
        }

        return $deviceList;
    }


    public function assignDeviceToUser(int $userId, int $deviceId): bool
    {
        $query = "INSERT INTO UserDevice (UserID, DeviceID) VALUES (:userId, :deviceId)";
        $params = [
            ':userId' => $userId,
            ':deviceId' => $deviceId
        ];

        try {
            $this->db->execute($query, $params);
            return true;
        } catch (Exception $e) {
            echo 'Error assigning device to user: ' . $e->getMessage();
            return false;
        }
    }
}

?>