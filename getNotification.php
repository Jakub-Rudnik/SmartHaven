<?php
require_once 'autoload.php';

use Lib\DatabaseConnection;

$DatabaseConnection = new DatabaseConnection();

$sql = "SELECT n.NotificationID, n.DeviceID, n.NewState, n.Timestamp, d.DeviceName 
        FROM Notifications n
        JOIN Device d ON n.DeviceID = d.DeviceID
        ORDER BY n.Timestamp DESC 
        LIMIT 10"; // Adjust the limit as needed

$notifications = $DatabaseConnection->query($sql);

header('Content-Type: application/json');
echo json_encode($notifications);