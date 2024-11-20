<?php

declare(strict_types=1);

namespace Services;

require_once './Entity/DeviceType.php';
require_once './Lib/DatabaseConnection.php';

use Entity\DeviceType;
use Exception;
use Lib\DatabaseConnection;

class DeviceTypeService
{
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    public function getDeviceTypes(): array 
    {
        $query = 'SELECT DeviceTypeID AS id, TypeName AS name FROM DeviceType';
        return $this->queryToArray($query);
    }

    public function getDeviceTypeById(int $id): DeviceType 
    {
        $query = 'SELECT DeviceTypeID, TypeName, Description FROM DeviceType WHERE DeviceTypeID = :id';
        $params = [':id' => $id];

        try {
            $deviceTypeData = $this->db->query($query, $params)[0];
            return new DeviceType(
                $deviceTypeData['DeviceTypeID'],
                $deviceTypeData['TypeName'],
                $deviceTypeData['Description']
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDeviceTypeByName(string $name): ?DeviceType
    {
        $query = 'SELECT DeviceTypeID, TypeName, Description FROM DeviceType WHERE TypeName = :name';
        $params = [':name' => $name];

        try {
            $deviceTypeData = $this->db->query($query, $params)[0];
            return new DeviceType(
                $deviceTypeData['DeviceTypeID'],
                $deviceTypeData['TypeName'],
                $deviceTypeData['Description']
            );
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function addDeviceType(DeviceType $deviceType): void
    {
        $query = 'INSERT INTO DeviceType (TypeName, Description) VALUES (:name, :description)';
        $params = [
            ':name' => $deviceType->getName(),
            ':description' => $deviceType->getDescription()
        ];

        try {
            $this->db->execute($query, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    public function updateDeviceType(DeviceType $deviceType): void
    {
        $query = 'UPDATE DeviceType SET TypeName = :name, Description = :description WHERE DeviceTypeID = :id';
        $params = [
            ':name' => $deviceType->getName(),
            ':description' => $deviceType->getDescription(),
            ':id' => $deviceType->getId()
        ];

        try {
            $this->db->execute($query, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteDeviceType(DeviceType $deviceType): void
    {
        $query = 'DELETE FROM DeviceType WHERE DeviceTypeID = :id';
        $params = [':id' => $deviceType->getId()];

        try {
            $this->db->execute($query, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

 
    private function queryToArray(string $query, array $params = []): array
    {
        try {
            $deviceTypesData = $this->db->query($query, $params);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }

        $deviceTypesList = [];
        foreach ($deviceTypesData as $deviceTypeData) {
            $deviceTypesList[] = new DeviceType(
                $deviceTypeData['DeviceTypeID'],
                $deviceTypeData['TypeName'],
                $deviceTypeData['Description']
            );
        }

        return $deviceTypesList;
    }
}