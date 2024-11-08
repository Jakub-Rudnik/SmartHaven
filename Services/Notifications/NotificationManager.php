<?php
class NotificationManager {
    private $config;

    public function __construct() {
        // Configuration of event types and notification priorities
        $this->config = [
            "ON" => [
                "title" => "Device Turned On",
                "priority" => "normal"
            ],
            "OFF" => [
                "title" => "Device Turned Off",
                "priority" => "normal"
            ],
            "ALERT" => [
                "title" => "Alert!",
                "priority" => "high"
            ]
        ];
        $this->config['MAINTENANCE'] = [
            "title" => "Maintenance work",
            "priority" => "low"
        ];
        $this->config['ERROR'] = [
            "title" => "Device error",
            "priority" => "high"
        ];
        
    }

    public function sendNotification($eventData) {
        // Decoding JSON into an array
        $data = json_decode($eventData, true);

        // Data validation
        if (!isset($data['device_id'], $data['event_type'], $data['timestamp'])) {
            die('Invalid event data');
        }

        // Configuration selection based on event type
        $eventType = strtoupper($data['event_type']);
        $config = $this->config[$eventType] ?? ["title" => "Event", "priority" => "normal"];

        // Generating notification content
        $title = "{$config['title']} on device {$data['device_id']}";
        $message = "Event of type {$eventType} occurred at {$data['timestamp']}.";
        $priority = $config['priority'];

        // Display notification (FCM can be added later)
        echo "Notification ($priority): $title - $message\n";

        // Logging notifications to a file (for testing)
        // file_put_contents('notifications_log.txt', "[$priority] $title - $message\n", FILE_APPEND);

        return true;
    }
}
?>
