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
        //save to file
        file_put_contents(
            'parameters.txt', 
            sprintf(
                "%s | Urządzenie o ID=%d zmieniło status na %s\n",
                date('Y-m-d H:i:s', strtotime('+1 hour')),
                $deviceId,
                $status === 1 ? 'włączony' : 'wyłączony'
            ),
            FILE_APPEND
        );
        // Returning the JSON response successfully
        echo json_encode(['success' => true, 'status' => $status]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 'status' => $status, 'message' => 'Błąd przy aktualizacji statusu: '.$e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe dane wejściowe']);
}
?>
