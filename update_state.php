<?php
require_once 'autoload.php';
use Lib\DatabaseConnection;
use Services\DeviceService;

header('Content-Type: application/json; charset=utf-8');

$deviceId = isset($_POST['deviceId']) ? (int)$_POST['deviceId'] : 1;
$newState = isset($_POST['newState']) ? $_POST['newState'] : '0';

$DatabaseConnection = new DatabaseConnection();
$deviceService = new DeviceService($DatabaseConnection);
$success = $deviceService->updateDeviceStatus($deviceId, $newState);

if ($success) {
    echo json_encode([
        'status' => 'ok',
        'message' => "Urządzenie $deviceId zmieniło stan na $newState o godzinie " . date('H:i:s')
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Nie udało się zaktualizować stanu urządzenia.'
    ]);
}
