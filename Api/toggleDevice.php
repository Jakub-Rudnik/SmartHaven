<?php
namespace Api;

use Lib\DatabaseConnection;

header('Content-Type: application/json');

// Checking whether data in JSON format
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['deviceId']) && isset($data['status'])) {
    $deviceId = (int) $data['deviceId'];
    $status = (int) $data['status'];

    $db = new DatabaseConnection();

    try {
        // Status update in the database
        $updateQuery = "UPDATE DeviceParameter SET Value = :status WHERE DeviceID = :deviceId AND ParameterID = 1";
        $stmt = $db->execute($updateQuery, [':status' => $status, ':deviceId' => $deviceId]);

        // Returning the JSON response successfully
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Błąd przy aktualizacji statusu: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe']);
}
?>
