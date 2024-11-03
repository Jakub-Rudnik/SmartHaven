<?php
declare(strict_types=1);

namespace Services;

require_once './Entity/DeviceType.php';
require_once './Lib/Database.php';

use Entity\DeviceType;
use Lib\DatabaseConnection;

class DeviceTypeService {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function getDeviceTypes(): array {
        $query = 'SELECT DeviceTypeID, TypeName, Description FROM DeviceType';

        return $this->queryToArray($query);
    }

    public function getDeviceTypeById(int $id): ?DeviceType {
        $query = 'SELECT DeviceTypeID, TypeName, Description FROM DeviceType WHERE DeviceTypeID = ' . $id;

        try {
            $deviceTypeData = $this->db->query($query)[0];
            return new DeviceType(
                $deviceTypeData['DeviceTypeID'],
                $deviceTypeData['TypeName'],
                $deviceTypeData['Description']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function getDeviceTypeByName(string $name): ?DeviceType {
        $query = 'SELECT DeviceTypeID, TypeName, Description FROM DeviceType WHERE TypeName = "' . $name . '"';

        try {
            $deviceTypeData = $this->db->query($query)[0];
            return new DeviceType(
                $deviceTypeData['DeviceTypeID'],
                $deviceTypeData['TypeName'],
                $deviceTypeData['Description']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public function addDeviceType(DeviceType $deviceType): void {
        $query = 'INSERT INTO DeviceType (TypeName, Description) VALUES ("' . $deviceType->getName() . '", "' . $deviceType->getDescription() . '")';

        try {
            $this->db->query($query);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function updateDeviceType(DeviceType $deviceType): void {
        $query = 'UPDATE DeviceType SET TypeName = "' . $deviceType->getName() . '", Description = "' . $deviceType->getDescription() . '" WHERE DeviceTypeID = ' . $deviceType->getId();

        try {
            $this->db->query($query);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteDeviceType(DeviceType $deviceType): void {
        $query = 'DELETE FROM DeviceType WHERE DeviceTypeID = ' . $deviceType->getId();

        try {
            $this->db->query($query);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private function queryToArray(string $query): array {
        try {
            $deviceTypesData = $this->db->query($query);
        } catch (\Exception $e) {
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