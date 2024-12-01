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
    $cycleDays = $_POST['cycle_days'] ?? [];

    // Data validation
    if (!$deviceID || !$startTime || !$endTime || empty($cycleDays)) {
        $_SESSION['error'] = "Wszystkie pola są wymagane!";
        header('Location: Schedule.php'); // Powrót na stronę formularza
        exit;
    }
    
    // Database connection
    try {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();

        // Prepare and execute an SQL query
        $sql = "INSERT INTO Schedule (DeviceID, StartTime, EndTime, RepeatPattern, ScheduleState) 
                VALUES (:deviceID, :startTime, :endTime, :repeatPattern, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':deviceID', $deviceID, \PDO::PARAM_INT);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);
        $stmt->bindParam(':repeatPattern', implode(',', $cycleDays)); // Zapis cyklu jako lista dni

        $stmt->execute();

        // If the write is successful, set a success message
        $_SESSION['success'] = "Harmonogram został zapisany pomyślnie!";
        echo json_encode(['success' => true, 'message' => 'Harmonogram zapisany']);
    } catch (\PDOException $e) {
        // Database error handling
        $_SESSION['error'] = "Błąd podczas zapisu: " . $e->getMessage();
        echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu: ' . $e->getMessage()]);
    }
} else {
    $_SESSION['error'] = "Nieprawidłowe żądanie.";
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie']);
}

// Redirect to a form page with messages
header('Location: Schedule.php');
exit;
