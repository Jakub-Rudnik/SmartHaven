<?php
require 'NotificationManager.php';

$notificationManager = new NotificationManager();

//Example event for tests
$sampleEvent = json_encode([
    "device_id" => "lamp_01",
    "event_type" => "ON",
    "timestamp" => date("c")
]);

$notificationManager->sendNotification($sampleEvent);
?>
