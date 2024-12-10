<?php

// An example of a class that deals with operations on schedules
class ScheduleService
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    // Function to download schedules for the device
    public function getSchedulesForDevice($deviceId)
    {

        $query = "SELECT * FROM Schedule WHERE DeviceID = :deviceId AND StartTime <= NOW() AND (EndTime IS NULL OR EndTime >= NOW())";
        
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute(['deviceId' => $deviceId]);

        return $stmt->fetchAll(); // Returns device schedules
    }

    // Function to update the status of the device
    public function updateDeviceStatus($deviceId, $newStatus)
    {
        // Muszę to ogarnąć czy baza dobrze jest połączona
        $query = "UPDATE DeviceParameter 
                  SET Value = :newStatus 
                  WHERE DeviceID = :deviceId 
                  AND ParameterID = (SELECT ParameterID FROM Parameter WHERE Name = 'Status')";
        
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'newStatus' => $newStatus, // 1 = włączone, 0 = wyłączone
            'deviceId' => $deviceId
        ]);
    }

    // Function to process schedules and change the status of devices
    public function processSchedules()
    {
        // Get all devices with active schedules
        $query = "SELECT DISTINCT DeviceID FROM Schedule WHERE StartTime <= NOW() AND (EndTime IS NULL OR EndTime >= NOW())";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute();

        // For each device, download its schedule and update the status
        while ($device = $stmt->fetch()) {
            $schedules = $this->getSchedulesForDevice($device['DeviceID']);
            
            foreach ($schedules as $schedule) {
                $currentTime = strtotime('now');
                $startTime = strtotime($schedule['StartTime']);
                $endTime = $schedule['EndTime'] ? strtotime($schedule['EndTime']) : null;

                // If the current time is within the range, set the state
                if ($currentTime >= $startTime && (!$endTime || $currentTime <= $endTime)) {
                    $newStatus = 1; // On
                } else {
                    $newStatus = 0; // Off
                }

                // Update device status
                $this->updateDeviceStatus($device['DeviceID'], $newStatus);
            }
        }
    }
}
