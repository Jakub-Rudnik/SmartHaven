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