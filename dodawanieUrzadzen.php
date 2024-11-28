<?php
include './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

$database = new DatabaseConnection();

try {
    $devices = $database->query("SELECT DeviceID, DeviceName, Location FROM Device");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Device to Room</title>
</head>
<body>
    <h1>Assign Device to Room</h1>

    <form action="dodawanieUrzadzen.php" method="POST">
        <label for="device">Select Device:</label>
        <select name="deviceID" id="device" required>
            <?php foreach ($devices as $device): ?>
                <option value="<?= htmlspecialchars($device['DeviceID']) ?>">
                    <?= htmlspecialchars($device['DeviceName']) ?> (<?= htmlspecialchars($device['Location'] ?: 'Unassigned') ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="location">Enter Room:</label>
        <input type="text" id="location" name="location" required>

        <button type="submit">Assign</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $deviceID = $_POST['deviceID'];
        $location = $_POST['location'];

        try {
            $updateQuery = "UPDATE Device SET Location = :location WHERE DeviceID = :deviceID";
            
            $database = new DatabaseConnection();
            $pdo = new PDO($database->dsn, $database->username, $database->password);
            
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute(['location' => $location, 'deviceID' => $deviceID]);

            echo "Device $deviceID successfully assigned to $location.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
</body>
</html>
