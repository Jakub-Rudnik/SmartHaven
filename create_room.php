<?php
include './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

$database = new DatabaseConnection();

// Pobranie dostępnych urządzeń z bazy
function getDevices(DatabaseConnection $db): array {
    $query = "SELECT DeviceID, DeviceName FROM Device";
    return $db->query($query);
}

// Zaktualizowanie lokalizacji urządzenia
function updateDeviceLocation(DatabaseConnection $db, int $deviceId, string $newLocation): void {
    $query = "UPDATE Device SET Location = :location WHERE DeviceID = :deviceId";
    
    try {
        $pdo = new PDO($db->dsn, $db->username, $db->password);
        $stmt = $pdo->prepare($query);
        $stmt->execute([':location' => $newLocation, ':deviceId' => $deviceId]);
        echo "Lokalizacja urządzenia została zaktualizowana.";
    } catch (PDOException $e) {
        echo "Błąd: " . $e->getMessage();
    }
}

// Inicjalizacja połączenia z bazą danych
$db = new DatabaseConnection();
$devices = [];

try {
    $devices = getDevices($db);
} catch (Exception $e) {
    echo "Błąd pobierania urządzeń: " . $e->getMessage();
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deviceId = (int) $_POST['deviceId'];
    $newLocation = trim($_POST['location']);

    if ($deviceId > 0 && $newLocation !== '') {
        updateDeviceLocation($db, $deviceId, $newLocation);
    } else {
        echo "Nieprawidłowe dane wejściowe.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zmień lokalizację urządzenia</title>
</head>
<body>
    <h1>Zmień lokalizację urządzenia</h1>
    <form method="POST">
        <label for="device_id">Wybierz urządzenie:</label>
        <select id="device_id" name="device_id" required>
            <option value="">-- Wybierz urządzenie --</option>
            <?php foreach ($devices as $device): ?>
                <option value="<?= htmlspecialchars($device['DeviceID']) ?>">
                    <?= htmlspecialchars($device['DeviceName']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <label for="location">Nowa lokalizacja:</label>
        <input type="text" id="location" name="location" required>
        <br><br>
        <button type="submit">Zapisz</button>
    </form>
</body>
</html>
