<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

$db = new DatabaseConnection();

$devicesService = new DeviceService($db);
$devicesTypeService = new DeviceTypeService($db);

$devices = $devicesService->getDevices();

echo '<h2>Devices</h2>';
foreach ($devices as $device) {
    echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';
}

echo '<h2>By Id</h2>';
$device = $devicesService->getDeviceById(1);
echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';

echo '<h2>By Name</h2>';
$device = $devicesService->getDeviceByName('klimatyzacja1');
if ($device !== null) {
    echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';
} else {
    echo 'Device not found.<br>';
}


echo '<h2>By Type</h2>';
$deviceType = $devicesTypeService->getDeviceTypeByName('Lampa');
if ($deviceType !== null) {
    $devices = $devicesService->getDeviceByType($deviceType);
    foreach ($devices as $device) {
        echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';
    }
} else {
    echo 'Device Type not found.<br>';
}


echo '<h2>By State</h2>';
$devices = $devicesService->getDeviceByState(true);
if (!empty($devices)) {
    foreach ($devices as $device) {
        echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . ($device->getState() ? 'On' : 'Off') . '<br>';
    }
} else {
    echo 'No devices found for the given state.<br>';
}

echo '<h2>Device Types</h2>';
$devicesTypes = $devicesTypeService->getDeviceTypes();
foreach ($devicesTypes as $deviceType) {
    echo $deviceType->getId() . ' ' .  $deviceType->getName() . '<br>';
}

//change state here
$devicesService->updateDeviceState(2, 0); 

$notifications = $devicesService->getRecentNotifications();

$devicesWithoutLocation = $devicesService->getDevicesWithoutLocation();
if (!empty($notifications)) {
    foreach ($notifications as $notification) {
        echo "<p>Device ID: {$notification['DeviceID']} changed state to " . ($notification['NewState'] ? 'On' : 'Off') . ".</p>";
    }
} else {
    echo "<p>No recent notifications.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Device Monitor</title> -->
    <title>Device Monitor</title>
    <script>
        const notifications = <?php echo json_encode($notifications); ?>;

        function stateToText(state) {
            switch (state) {
                case 1:
                    return 'On';
                case 0:
                    return 'Off';
                case -1:
                    return 'Error';
                default:
                    return 'Unknown';
            }
        }

        function showPopup(message) {
            alert(message);
        }

        window.onload = function() {
            notifications.forEach(notification => {
                const message = `Device ID: ${notification.DeviceID} changed state to ${stateToText(notification.NewState)} at ${notification.Timestamp}`;
                showPopup(message);
            });
        };
    </script>
</head>
<body>
<h1>Devices Without Location</h1>
    <?php if (!empty($devicesWithoutLocation)): ?>
        <ul>
            <?php foreach ($devicesWithoutLocation as $deviceName): ?>
                <li><?php echo htmlspecialchars($deviceName); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>All devices have locations assigned.</p>
    <?php endif; ?>
   
</body>
</html>