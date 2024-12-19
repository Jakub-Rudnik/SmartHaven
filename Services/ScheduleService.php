<?php
declare(strict_types=1);

namespace Services;
use Lib\DatabaseConnection;

// An example of a class that deals with operations on schedules
class ScheduleService
{
    private DatabaseConnection $dbConnection;

    public function __construct(DatabaseConnection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }
    
    // Function to check if the current time is within a 1-minute window of the scheduled time
    public function isTimeWithinWindow($scheduledTime): bool
    {
        $currentTime = strtotime('now');
        $scheduledTime = strtotime($scheduledTime);

        $timeDifference = abs($currentTime - $scheduledTime);
        return $timeDifference <= 60; // 60 seconds = 1 minute
    }

    // Function to download schedules for the device
    public function getSchedulesForDevice($deviceId): array
{
    $currentTime = date('H:i'); // Current time in H:i format
    $currentDayOfWeek = strtolower(date('l')); // Day of week (np. Monday -> 'poniedziałek')

    $query = "SELECT * FROM Schedule 
              WHERE DeviceID = :deviceId
              AND ScheduleState = 1 
              AND (
                  RepeatPattern = 'codziennie'
                  OR
                  (FIND_IN_SET(:currentDayOfWeek, RepeatPattern) > 0)
              )
              AND StartTime = :currentTime"; // Check for exact StartTime match
    
    return $this->dbConnection->query($query, [
        'deviceId' => $deviceId,
        'currentTime' => $currentTime,
        'currentDayOfWeek' => $currentDayOfWeek,
    ]);
}

    // Function to update the status of the device
    public function updateDeviceStatus(int $deviceId, int $newStatus)
    {
        $query = "UPDATE DeviceParameter 
                  SET Value = :newStatus 
                  WHERE DeviceID = :deviceId 
                  AND ParameterID = 1"; // ParameterID = 1 is "Status"
    
        $this->dbConnection->execute($query, [
            'newStatus' => $newStatus,
            'deviceId' => $deviceId
        ]);
}


    // Function to process schedules and change the status of devices
    public function processSchedules()
    {
        // Get all devices with active schedules
        $query = "SELECT DISTINCT DeviceID 
              FROM Schedule 
              WHERE ScheduleState = 1";

        $devices = $this->dbConnection->query($query);

        foreach ($devices as $device) {
            $schedules = $this->getSchedulesForDevice((int)$device['DeviceID']);

            foreach ($schedules as $schedule) {
                $currentTime = strtotime('now');
                $startTime = strtotime($schedule['StartTime']);
                $endTime = strtotime($schedule['EndTime']);
                
                // If the scheduled time is within the window of 1 minute, we change the status
                if ($this->isTimeWithinWindow($schedule['StartTime'])) {
                    $newStatus = 1; // Turn the device on
                } else if ($this->isTimeWithinWindow($schedule['EndTime'])) {
                    $newStatus = 0; // Turn the device off
                }

                $this->updateDeviceStatus((int)$device['DeviceID'], $newStatus);
            }
        }
    }
}
