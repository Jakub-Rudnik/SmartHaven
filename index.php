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
$device = $devicesService->getDeviceByName('Tv');
echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';

echo '<h2>By Type</h2>';
$deviceType = $devicesTypeService->getDeviceTypeByName('Light');
$devices = $devicesService->getDeviceByType($deviceType);
foreach ($devices as $device) {
    echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';
}

echo '<h2>By State</h2>';
$devices = $devicesService->getDeviceByState(true);
foreach ($devices as $device) {
    echo $device->getName() . ' ' . $device->getType()->getName() . ' ' . $device->getState() . '<br>';
}

echo '<h2>Device Types</h2>';
$devicesTypes = $devicesTypeService->getDeviceTypes();
foreach ($devicesTypes as $deviceType) {
    echo $deviceType->getId() . ' ' .  $deviceType->getName() . '<br>';
}