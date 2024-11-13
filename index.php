<?php
require_once './Lib/Database.php';
require_once './Services/DeviceService.php';
require_once './Services/DeviceTypeService.php';

$db = new DatabaseConnection();

$Device = new Device($db);
$DevicesType = new DeviceType($db);

$devices = $Device->getDevices();

echo '<h2>Devices</h2>';
foreach ($devices as $Device) {
    echo $Device->getName() . ' ' . $Device->getType()->getName() . ' ' . $Device->getState() . '<br>';
}

echo '<h2>By Id</h2>';
$Device = $Device->getDeviceById(1);
echo $Device->getName() . ' ' . $Device->getType()->getName() . ' ' . $Device->getState() . '<br>';

echo '<h2>By Name</h2>';
$Device = $Device->getDeviceByName('Tv');
echo $Device->getName() . ' ' . $Device->getType()->getName() . ' ' . $Device->getState() . '<br>';

echo '<h2>By Type</h2>';
$DeviceType = $DevicesType->getDeviceTypeByName('Light');
$devices = $Device->getDeviceByType($DeviceType);
foreach ($devices as $Device) {
    echo $Device->getName() . ' ' . $Device->getType()->getName() . ' ' . $Device->getState() . '<br>';
}

echo '<h2>By State</h2>';
$devices = $Device->getDeviceByState(true);
foreach ($devices as $Device) {
    echo $Device->getName() . ' ' . $Device->getType()->getName() . ' ' . $Device->getState() . '<br>';
}

echo '<h2>Device Types</h2>';
$DevicesTypes = $DevicesType->getDeviceTypes();
foreach ($evicesTypes as $DeviceType) {
    echo $DeviceType->getId() . ' ' .  $dDviceType->getName() . '<br>';
}