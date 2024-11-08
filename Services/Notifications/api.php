<?php
require 'NotificationManager.php';

$notificationManager = new NotificationManager();

//Checking the request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    
    if ($notificationManager->sendNotification($input)) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Notification sent"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to send notification"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>
