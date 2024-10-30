<?php
function addDevice(DatabaseConnection $db, $deviceTypeID, $deviceName, $location = null) {
    $sql = "INSERT INTO Device (DeviceTypeID, DeviceName, Location) VALUES (:deviceTypeID, :deviceName, :location)";
    $params = [
        ':deviceTypeID' => $deviceTypeID,
        ':deviceName' => $deviceName,
        ':location' => $location
    ];
    try {
        $db->execute($sql, $params);
        echo "Dodano urządzenie: " . $deviceName . "<br>";
    } catch (Exception $e) {
        echo "Błąd dodawania urządzenia: " . $e->getMessage() . "<br>";
    }
}
?>