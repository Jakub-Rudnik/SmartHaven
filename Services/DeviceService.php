<?php

declare(strict_types=1);

namespace Services;

use Entity\Device;
use Entity\DeviceType;
use Exception;
use Lib\DatabaseConnection;
use PDO;

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
    public function createDevice(string $deviceName, int $deviceTypeId, string $deviceUrl, int $groupId): array
    {
        $result = '';

        $sql = "
            INSERT INTO Device (DeviceName, DeviceTypeID, GroupID, DeviceURL)
            VALUES (:deviceName, :deviceTypeId, :groupId, :deviceUrl)
        ";

        $params = [
            ':deviceName' => $deviceName,
            ':deviceTypeId' => $deviceTypeId,
            ':groupId' => $groupId === 0 ? null : $groupId,
            ':deviceUrl' => $deviceUrl
        ];

        try {
            $result = $this->db->execute($sql, $params);
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'data' => $result];

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
                d.DeviceUrl,
                d.GroupID                
            FROM Device d
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

            return new Device(
                (int)$deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                '',  // opis
                $deviceData['GroupID'],
                $deviceData['DeviceUrl']
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
    public function updateDevice(int $deviceId, string $deviceName, string $deviceUrl, int $groupId): array
    {
        $sql = "
            UPDATE Device
            SET 
                DeviceName = :deviceName,
                GroupID = :groupId,
                DeviceUrl = :deviceUrl
            WHERE DeviceID = :deviceId
        ";

        $params = [
            ':deviceName' => $deviceName,
            ':deviceUrl' => $deviceUrl,
            ':groupId' => $groupId === 0 ? null : $groupId,
            ':deviceId' => $deviceId,
        ];

        try {
            $result = $this->db->execute($sql, $params);
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'data' => $result];
    }

    /**
     * Usuwa urządzenie z bazy danych po ID.
     * Zwraca true, jeśli usunięto rekord, false w przypadku błędu.
     */
    public function deleteDevice(int $deviceId): array
    {
        $sql = "DELETE FROM Device WHERE DeviceID = :deviceId";
        $params = [':deviceId' => $deviceId];

        try {
            $result = $this->db->execute($sql, $params);
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'data' => $result];
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
                d.DeviceUrl,
                d.GroupID
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            JOIN UserDevice ud ON d.DeviceID = ud.DeviceID
            WHERE ud.UserID = :userId;
        ";

        $params = [':userId' => $_SESSION['userID']];

        $result = $this->db->query($sql, $params);

        $devices = [];
        foreach ($result as $row) {
            $deviceType = $this->deviceTypeService->getDeviceTypeById((int)$row['DeviceTypeID']);

            $devices[] = new Device(
                (int)$row['DeviceID'],
                $row['DeviceName'],
                $deviceType,
                '',
                $row['GroupID'],
                $row['DeviceUrl']
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
                d.DeviceUrl,
                d.GroupID
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
                d.DeviceUrl,
                d.GroupID
            FROM Device d
            JOIN UserDevice ud ON d.DeviceID = ud.DeviceID
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE ud.UserID = :userId
              AND d.GroupID = :groupId
        ";

        $params = [
            ':userId' => $userId,
            ':groupId' => $groupId,
        ];

        return $this->queryToArray($sql, $params);
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
                d.DeviceUrl,
                d.GroupID
            FROM Device d
            LEFT JOIN `Groups` g ON d.GroupID = g.GroupID
            WHERE d.DeviceTypeID = :typeId
        ";

        $params = [':typeId' => $type->getId()];

        return $this->queryToArray($sql, $params);
    }

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

            $deviceList[] = new Device(
                (int)$deviceData['DeviceID'],
                $deviceData['DeviceName'],
                $deviceType,
                '', // opis
                $deviceData['GroupID'],
                $deviceData['GroupName'] ?? 'Brak grupy'
            );
        }

        return $deviceList;
    }

    /**
     * Przypisuje urządzenie do konkretnego użytkownika (UserDevice).
     */
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
