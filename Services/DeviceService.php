<?php
declare(strict_types=1);

namespace Services;

require_once './Entity/Device.php';
require_once './Entity/DeviceType.php';
require_once './Services/DeviceTypeService.php';
require_once './Lib/DatabaseConnection.php';

use Entity\Device;
use Entity\DeviceType;
use Exception;
use Lib\DatabaseConnection;
use Services\DeviceTypeService;

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
                $row['Location'],
                $status
            );
            $devices[] = $device;
        }
        return $devices;
    }
    
    public function getDeviceById(int $id): Device 
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
                $deviceData['Location'],
                $status
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDeviceStatus(int $deviceId): bool
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

    public function updateDeviceStatus(int $deviceId, string $newState)
    {
        $currentStatus = $this->getDeviceStatus($deviceId);

        if ($currentStatus !== ($newState === '1')) {
            $updateQuery = "UPDATE DeviceParameter SET Value = :newState WHERE DeviceID = :deviceId AND ParameterID = 1"; // ParameterID = 1 means 'Status'
            $updateParams = [
                ':newState' => $newState,
                ':deviceId' => $deviceId
            ];
            $this->db->query($updateQuery, $updateParams);

            $insertQuery = "INSERT INTO Notifications (DeviceID, NewState) VALUES (:deviceId, :newState)";
            $insertParams = [
                ':deviceId' => $deviceId,
                ':newState' => $newState
            ];
            $this->db->query($insertQuery, $insertParams);
        }

        $deviceName = $this->getDeviceById($deviceId)->getName();
        $stateText = $newState === '1' ? 'ON' : 'OFF';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "$timestamp - Urządzenie $deviceName (ID: $deviceId) zmieniło status na $stateText\n";
        file_put_contents('notifications_log.txt', $logEntry, FILE_APPEND);
    }


    public function updateDeviceParameter(int $deviceId, int $parameterId, string $newValue): void
    {
        $query = "SELECT Value FROM DeviceParameter WHERE DeviceID = :deviceId AND ParameterID = :parameterId";
        $params = [
            ':deviceId' => $deviceId,
            ':parameterId' => $parameterId
        ];
        
        $currentValues = $this->db->query($query, $params);
        if (!empty($currentValues)) {
            $oldValue = $currentValues[0]['Value'];
    
            // Log changes only if the value changes
            if ($oldValue !== $newValue) {
                // Update the parameter value
                $updateQuery = "UPDATE DeviceParameter SET Value = :newValue WHERE DeviceID = :deviceId AND ParameterID = :parameterId";
                $updateParams = [
                    ':newValue' => $newValue,
                    ':deviceId' => $deviceId,
                    ':parameterId' => $parameterId
                ];
                $this->db->query($updateQuery, $updateParams);
    
                // Log the change
                $parameterName = $this->getParameterNameById($parameterId);
                $this->logParameterChange($deviceId, $parameterName, $oldValue, $newValue);
            }
        }
    }
    
    private function logParameterChange(int $deviceId, string $parameterName, string $oldValue, string $newValue): void
    {
        $deviceName = $this->getDeviceById($deviceId)->getName();
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "$timestamp - Device: $deviceName (ID: $deviceId), Parameter: $parameterName changed from '$oldValue' to '$newValue'\n";
   
        file_put_contents('parameter_changes_log.txt', $logEntry, FILE_APPEND);
    }
    
    private function getParameterNameById(int $parameterId): string
    {
        $query = "SELECT Name FROM Parameter WHERE ParameterID = :parameterId";
        $params = [':parameterId' => $parameterId];

        $result = $this->db->query($query, $params);
        return $result[0]['Name'] ?? 'Unknown';
    }


    public function getDevicesByType(DeviceType $type): array
    {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE DeviceTypeID = :typeId';
        $params = [':typeId' => $type->getId()];

        return $this->queryToArray($query, $params);
    }



    // public function getDevicesWithoutLocation(): array
    // {
    //     $devicesWithoutLocation = [];

    //     try {
    //         $query = "SELECT DeviceName FROM Device WHERE Location IS NULL";
    //         $result = $this->db->query($query);

    //         foreach ($result as $row) {
    //             $devicesWithoutLocation[] = $row['DeviceName'];
    //         }
    //     } catch (Exception $e) {
    //         echo "Error: " . $e->getMessage();
    //     }
    //     return $devicesWithoutLocation;
    // }

    public function getDevicesByLocation(string $location): array
    {
        $query = 'SELECT DeviceID, DeviceName, DeviceTypeID, Location FROM Device WHERE Location = :location';
        $params = [':location' => $location];

        return $this->queryToArray($query, $params);
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
                $deviceData['Location'],
                $status
            );
        }

        return $deviceList;
    }

}