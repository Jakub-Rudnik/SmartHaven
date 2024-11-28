<?php

class DeviceTypeService
{
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    public function getDeviceTypes(): array
    {
        $query = 'SELECT id, name FROM DeviceType';

        return $this->queryToArray($query);
    }

    public function getDeviceTypeById(int $id): DeviceType
    {
        $query = 'SELECT id, name FROM DeviceType WHERE id = ' . $id;

        try {
            $deviceType = $this->db->query($query)[0];
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return new DeviceType($deviceType['id'], $deviceType['name']);
    }

    public function getDeviceTypeByName(string $name): DeviceType
    {
        $query = 'SELECT id, name FROM DeviceType WHERE name = "' . $name . '"';

        try {
            $deviceType = $this->db->query($query)[0];
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return new DeviceType($deviceType['id'], $deviceType['name']);
    }

    public function addDeviceType(DeviceType $deviceType): void
    {
        $query = 'INSERT INTO DeviceType (name) VALUES ("' . $deviceType->getName() . '")';

        try {
            $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function updateDeviceType(DeviceType $deviceType): void
    {
        $query = 'UPDATE DeviceType SET name = "' . $deviceType->getName() . '" WHERE id = ' . $deviceType->getId();

        try {
            $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteDeviceType(DeviceType $deviceType): void
    {
        $query = 'DELETE FROM DeviceType WHERE id = ' . $deviceType->getId();

        try {
            $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param string $query
     * @return array
     */
    public function queryToArray(string $query): array
    {
        try {
            $devicesTypes = $this->db->query($query);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $devicesTypesList = [];
        foreach ($devicesTypes as $deviceType) {
            $devicesTypesList[] = new DeviceType($deviceType['id'], $deviceType['name']);
        }

        return $devicesTypesList;
    }
    public function getDevicesWithoutLocation(): array
    {
        $devicesWithoutLocation = [];
    
        try {
            $query = "SELECT DeviceName FROM Device WHERE Location IS NULL";
            $result = $this->db->query($query);
    
            foreach ($result as $row) {
                $devicesWithoutLocation[] = $row['DeviceName'];
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $devicesWithoutLocation;
    }
    public function assignDeviceToRoom($pdo, $deviceID, $location) {
        try {
            $stmt = $pdo->prepare("UPDATE Device SET Location = :location WHERE DeviceID = :deviceID");
            $stmt->execute(['location' => $location, 'deviceID' => $deviceID]);
    
            echo "Device $deviceID successfully assigned to $location.";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}