<?php
namespace Api;

use Lib\DatabaseConnection;

header('Content-Type: application/json');

// Checking whether data in JSON format
$data = json_decode(file_get_contents('php://input'), true);

session_start(); // To display messages in a session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Download the form data
    $deviceID = $_POST['device_id'] ?? null;
    $startTime = $_POST['start_time'] ?? null;
    $endTime = $_POST['end_time'] ?? null;
    $cycleDays = $_POST['cycle_days'] ?? []; // ["Monday", "Tuesday", ...]

    // Data validation
    if (!$deviceID || !$startTime || !$endTime || empty($cycleDays)) {
        $_SESSION['error'] = "Wszystkie pola są wymagane!";
        header('Location: Schedule.php');
        exit;
    }

    // Debugging: Log the contents of $cycleDays
    error_log("Cycle days received: " . print_r($cycleDays, true));

    // Normalize $cycleDays for case and whitespace issues
    $cycleDays = array_map('trim', $cycleDays); // Remove whitespace characters
    $cycleDays = array_map('strtolower', $cycleDays); // All letters to lowercase

    // Check if "codziennie" is in the array
    if (in_array("codziennie", $cycleDays)) {
        error_log("Codziennie option selected."); // Debugging: Confirm the branch
        try {
            $db = new DatabaseConnection();
            $pdo = $db->getConnection();

            $sql = "INSERT INTO Schedule (DeviceID, StartTime, EndTime, RepeatPattern, ScheduleState, ParameterID, ParameterValue) 
                    VALUES (:deviceID, :startTime, :endTime, 'codziennie', 1, 1, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':deviceID', $deviceID, \PDO::PARAM_INT);
            $stmt->bindParam(':startTime', $startTime);
            $stmt->bindParam(':endTime', $endTime);
            $stmt->execute();

            $_SESSION['success'] = "Harmonogram został zapisany pomyślnie!";
            echo json_encode(['success' => true, 'message' => 'Harmonogram zapisany']);
        } catch (\PDOException $e) {
            // Handle SQL error
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "Błąd podczas zapisu: " . $e->getMessage();
            echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu: ' . $e->getMessage()]);
        }
        // Exit after handling "Codziennie"
        exit;
    } else {
        error_log("Custom days selected: " . print_r($cycleDays, true));
        try {
            $db = new DatabaseConnection();
            $pdo = $db->getConnection();

            foreach ($cycleDays as $day) {
                $sql = "INSERT INTO Schedule (DeviceID, StartTime, EndTime, RepeatPattern, ScheduleState, ParameterID, ParameterValue) 
                        VALUES (:deviceID, :startTime, :endTime, :repeatPattern, 1, 1, 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':deviceID', $deviceID, \PDO::PARAM_INT);
                $stmt->bindParam(':startTime', $startTime);
                $stmt->bindParam(':endTime', $endTime);
                $stmt->bindParam(':repeatPattern', $day); // Insert a single day as RepeatPattern
                $stmt->execute();
            }

            $_SESSION['success'] = "Harmonogram został zapisany pomyślnie!";
            echo json_encode(['success' => true, 'message' => 'Harmonogram zapisany']);
        } catch (\PDOException $e) {
            // Handle SQL error
            error_log("Database error: " . $e->getMessage());
            $_SESSION['error'] = "Błąd podczas zapisu: " . $e->getMessage();
            echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu: ' . $e->getMessage()]);
        }
    }
} else {
    $_SESSION['error'] = "Nieprawidłowe żądanie.";
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie']);
}

// Redirect to a form page with messages
header('Location: Schedule.php');
exit;
