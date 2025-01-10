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

    /**
     * Tworzy nowe urządzenie w bazie danych i zwraca ID nowego rekordu.
     *
     * @param string $deviceName
     * @param int $deviceTypeId
     * @param int|null $groupId  (null, jeśli urządzenie nie należy do żadnej grupy)
     * @return int  ID nowo utworzonego urządzenia
     */
    public function createDevice(string $deviceName, int $deviceTypeId, ?int $groupId = null): int
    {
        $sql = "
            INSERT INTO Device (DeviceName, DeviceTypeID, GroupID)
            VALUES (:deviceName, :deviceTypeId, :groupId)
        ";

        $params = [
            ':deviceName'   => $deviceName,
            ':deviceTypeId' => $deviceTypeId,
            ':groupId'      => $groupId,
        ];

        try {
            $this->db->execute($sql, $params);
            // Zwracamy ostatnie wstawione ID (zakładamy, że DatabaseConnection ma taką metodę)
            return (int)$this->db->getLastInsertId();
        } catch (Exception $e) {
            echo 'Error creating device: ' . $e->getMessage();
            return 0;
        }
    }

    /**
     * Zwraca listę wszystkich urządzeń.
     *
     * @return Device[]
     */
    public function getDevices(): array
    {
        // Pobieramy również nazwę grupy (lub zastępujemy ją 'Brak grupy', jeśli GroupID jest NULL)
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
        ";

        $result = $this->db->query($sql);

        $devices = [];
        foreach ($result as $row) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById((int)$row['DeviceTypeID']);
            $status = $this->getDeviceStatus((int)$row['DeviceID']);

            $device = new Device(
                (int)$row['DeviceID'],
                $row['DeviceName'],
                $deviceType,
                '',                 // przykładowy opis, jeśli w Entity\Device mamy 'description'
                $status,
                $row['GroupName']  // nazwa grupy jako "location" w modelu
            );
            $devices[] = $device;
        }
        return $devices;
    }

    /**
     * Zwraca obiekt Device lub null, jeśli nie znaleziono.
     *
     * @param int $id
     * @return Device|null
     */
    public function getDeviceById(int $id): ?Device
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE d.DeviceID = :id
        ";
        $params = [':id' => $id];

        try {
            $rows = $this->db->query($sql, $params);
            if (count($rows) === 0) {
                return null;
            }
            $deviceData = $rows[0];

            $deviceType = $this->deviceTypeService->getDeviceTypeById((int)$deviceData['DeviceTypeID']);
            $status = $this->getDeviceStatus((int)$deviceData['DeviceID']);

            return new Device(
                (int)$deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                '',  // opis
                $status,
                $deviceData['GroupName']
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Aktualizuje istniejące urządzenie w bazie danych.
     * Zwraca true, jeśli aktualizacja się powiodła, w przeciwnym razie false.
     *
     * @param int $deviceId
     * @param string $deviceName
     * @param int $deviceTypeId
     * @param int|null $groupId
     * @return bool
     */
    public function updateDevice(int $deviceId, string $deviceName, int $deviceTypeId, ?int $groupId): bool
    {
        $sql = "
            UPDATE Device
            SET 
                DeviceName = :deviceName,
                DeviceTypeID = :deviceTypeId,
                GroupID = :groupId
            WHERE DeviceID = :deviceId
        ";

        $params = [
            ':deviceName'   => $deviceName,
            ':deviceTypeId' => $deviceTypeId,
            ':groupId'      => $groupId,
            ':deviceId'     => $deviceId
        ];

        try {
            $this->db->execute($sql, $params);
            return true;
        } catch (Exception $e) {
            echo 'Error updating device: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Usuwa urządzenie z bazy danych po ID.
     * Zwraca true, jeśli usunięto rekord, false w przypadku błędu.
     *
     * @param int $deviceId
     * @return bool
     */
    public function deleteDevice(int $deviceId): bool
    {
        $sql = "DELETE FROM Device WHERE DeviceID = :deviceId";
        $params = [':deviceId' => $deviceId];

        try {
            $this->db->execute($sql, $params);
            return true;
        } catch (Exception $e) {
            echo 'Error deleting device: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Prywatna metoda pomocnicza do pobierania statusu urządzenia z tabeli DeviceParameter (ParameterID = 1 -> Status).
     *
     * @param int $deviceId
     * @return bool
     */
    private function getDeviceStatus(int $deviceId): bool
    {
        $query = "
            SELECT Value 
            FROM DeviceParameter 
            WHERE DeviceID = :deviceId 
              AND ParameterID = 1
        ";
        $params = [':deviceId' => $deviceId];

        try {
            $result = $this->db->query($query, $params);
            if (count($result) > 0) {
                return $result[0]['Value'] === '1';
            }
            return false; 
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Zwraca listę urządzeń na podstawie typu (DeviceType).
     *
     * @param DeviceType $type
     * @return Device[]
     */
    public function getDevicesByType(DeviceType $type): array
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE d.DeviceTypeID = :typeId
        ";
        $params = [':typeId' => $type->getId()];

        return $this->queryToArray($sql, $params);
    }

    /**
     * Zwraca listę urządzeń wg nazwy grupy (wcześniej 'lokalizacji').
     * Zostawione dla zachowania kompatybilności, ale semantycznie pobiera po GroupName.
     *
     * @param string $location  Nazwa grupy
     * @return Device[]
     */
    public function getDevicesByLocation(string $location): array
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE g.GroupName = :groupName
        ";
        $params = [':groupName' => $location];

        return $this->queryToArray($sql, $params);
    }

    /**
     * Zwraca wszystkie urządzenia pogrupowane wg nazwy grupy (dawniej Location).
     * Dla każdego urządzenia dołączamy status (ParameterID = 1).
     *
     * @return Device[]
     */
    public function getDevicesGroupedByLocations(): array
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName,
                dp.Value AS Status
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            JOIN DeviceParameter dp ON d.DeviceID = dp.DeviceID
            WHERE dp.ParameterID = 1
            ORDER BY g.GroupName
        ";

        // Możemy użyć queryToArray, ale trzeba delikatnie zinterpretować kolumnę Status (Value).
        try {
            $rows = $this->db->query($sql, []);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }

        $deviceList = [];
        foreach ($rows as $row) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById((int)$row['DeviceTypeID']);
            $status = ($row['Status'] === '1');

            $deviceList[] = new Device(
                (int)$row['DeviceID'],
                $row['DeviceName'],
                $deviceType,
                '',  // opis
                $status,
                $row['GroupName']
            );
        }
        return $deviceList;
    }

    /**
     * Metoda pomocnicza – wykonuje zapytanie i zwraca tablicę obiektów Device.
     *
     * @param string $query
     * @param array $params
     * @return Device[]
     */
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
            $deviceType = $this->deviceTypeService->getDeviceTypeById((int)$deviceData['DeviceTypeID']);
            // Tutaj pobieramy status przez getDeviceStatus() – bo mogło nie być w SELECT
            $status = $this->getDeviceStatus((int)$deviceData['DeviceID']);

            $deviceList[] = new Device(
                (int)$deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                '', // opis
                $status,
                $deviceData['GroupName'] ?? 'Brak grupy'
            );
        }

        return $deviceList;
    }

    /**
     * Przypisuje urządzenie do konkretnego użytkownika (UserDevice).
     *
     * @param int $userId
     * @param int $deviceId
     * @return bool
     */
    public function assignDeviceToUser(int $userId, int $deviceId): bool
    {
        $query = "INSERT INTO UserDevice (UserID, DeviceID) VALUES (:userId, :deviceId)";
        $params = [
            ':userId'   => $userId,
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
