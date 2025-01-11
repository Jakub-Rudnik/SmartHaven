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

    // ----------------------------------------------------------------------
    //  C R U D
    // ----------------------------------------------------------------------

    /**
     * Tworzy nowe urządzenie w bazie danych i zwraca ID nowego rekordu.
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
            return (int) $this->db->getLastInsertId();
        } catch (Exception $e) {
            echo 'Error creating device: ' . $e->getMessage();
            return 0;
        }
    }

    /**
     * Zwraca obiekt Device lub null, jeśli nie znaleziono.
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
                (int) $deviceData['DeviceID'],
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

    // ----------------------------------------------------------------------
    //  Metody wyszukujące
    // ----------------------------------------------------------------------

    /**
     * Zwraca listę wszystkich urządzeń.
     */
    public function getDevices(): array
    {
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

            $devices[] = new Device(
                (int)$row['DeviceID'],
                $row['DeviceName'],
                $deviceType,
                '',
                $status,
                $row['GroupName']
            );
        }
        return $devices;
    }

    /**
     * Zwraca urządzenia należące do danego użytkownika, które nie mają przypisanej grupy.
     */
    public function getDevicesNoGroupForUser(int $userId): array
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            JOIN UserDevice ud ON d.DeviceID = ud.DeviceID
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE ud.UserID = :userId
              AND d.GroupID IS NULL
        ";

        $params = [':userId' => $userId];

        return $this->queryToArray($sql, $params);
    }

    /**
     * Zwraca urządzenia należące do danej grupy i danego użytkownika.
     */
    public function getDevicesByGroupIdForUser(int $groupId, int $userId): array
    {
        $sql = "
            SELECT 
                d.DeviceID,
                d.DeviceName,
                d.DeviceTypeID,
                COALESCE(g.GroupName, 'Brak grupy') AS GroupName
            FROM Device d
            JOIN UserDevice ud ON d.DeviceID = ud.DeviceID
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE ud.UserID = :userId
              AND d.GroupID = :groupId
        ";

        $params = [
            ':userId'  => $userId,
            ':groupId' => $groupId,
        ];

        return $this->queryToArray($sql, $params);
    }

    /**
     * Zwraca wszystkie urządzenia pogrupowane wg nazwy grupy (dawniej 'Location').
     * Dla każdego urządzenia dołączamy status (ParameterID = 1).
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
                '',
                $status,
                $row['GroupName']
            );
        }
        return $deviceList;
    }

    /**
     * Zwraca listę urządzeń na podstawie typu (DeviceType).
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
     * Zostawione dla kompatybilności, ale semantycznie pobiera po GroupName.
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

    // ----------------------------------------------------------------------
    //  Metody pomocnicze
    // ----------------------------------------------------------------------

    /**
     * Metoda pomocnicza – wykonuje zapytanie i zwraca tablicę obiektów Device.
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
     * Prywatna metoda pomocnicza do pobierania statusu urządzenia
     * z tabeli DeviceParameter (ParameterID = 1 -> Status).
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
     * Przypisuje urządzenie do konkretnego użytkownika (UserDevice).
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
