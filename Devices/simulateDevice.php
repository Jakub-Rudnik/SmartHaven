<?php
function simulateDevice(DatabaseConnection $db, $deviceID, $numOfSimulations) {
    // Pobranie parametrów przypisanych do danego typu urządzenia
    $sql = "SELECT p.ParameterID, p.Name 
            FROM Parameter p
            JOIN DeviceTypeParameter dtp ON p.ParameterID = dtp.ParameterID
            JOIN Device d ON d.DeviceTypeID = dtp.DeviceTypeID
            WHERE d.DeviceID = :deviceID";

    $params = [':deviceID' => $deviceID];
    
    try {
        $parameters = $db->query($sql, $params);

        // Przeprowadzanie symulacji
        for ($i = 0; $i < $numOfSimulations; $i++) {
            foreach ($parameters as $parameter) {
                $parameterID = $parameter['ParameterID'];
                $simulatedValue = rand(0, 100);  // Generowanie losowej wartości dla symulacji

                $insertSql = "INSERT INTO SimulationData (DeviceID, ParameterID, SimulatedValue) 
                              VALUES (:deviceID, :parameterID, :simulatedValue)";
                $insertParams = [
                    ':deviceID' => $deviceID,
                    ':parameterID' => $parameterID,
                    ':simulatedValue' => $simulatedValue
                ];

                $db->execute($insertSql, $insertParams);
            }
        }
        echo "Symulacja urządzenia o ID: " . $deviceID . " zakończona.<br>";
    } catch (Exception $e) {
        echo "Błąd symulacji urządzenia: " . $e->getMessage() . "<br>";
    }
}
?>