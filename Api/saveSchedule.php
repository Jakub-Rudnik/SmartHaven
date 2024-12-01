<?php
namespace Api;

use Lib\DatabaseConnection;

header('Content-Type: application/json');

session_start(); // Do wyświetlania komunikatów w sesji

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza
    $deviceID = $_POST['device_id'] ?? null;
    $startTime = $_POST['start_time'] ?? null;
    $endTime = $_POST['end_time'] ?? null;
    $cycleDays = $_POST['cycle_days'] ?? [];

    // Walidacja danych
    if (!$deviceID || !$startTime || !$endTime || empty($cycleDays)) {
        $_SESSION['error'] = "Wszystkie pola są wymagane!";
        header('Location: Schedule.php'); // Powrót na stronę formularza
        exit;
    }

    // Połączenie z bazą danych
    try {
        $db = new DatabaseConnection();
        $pdo = $db->getConnection();  // Używamy publicznej metody getConnection()

        // Przygotuj i wykonaj zapytanie SQL
        $sql = "INSERT INTO Schedule (DeviceID, StartTime, EndTime, RepeatPattern, ScheduleState) 
                VALUES (:deviceID, :startTime, :endTime, :repeatPattern, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':deviceID', $deviceID, PDO::PARAM_INT);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);
        $stmt->bindParam(':repeatPattern', implode(',', $cycleDays)); // Zapis cyklu jako lista dni

        $stmt->execute();

        // Jeśli zapis się powiedzie, ustaw komunikat sukcesu
        $_SESSION['success'] = "Harmonogram został zapisany pomyślnie!";
        echo json_encode(['success' => true, 'message' => 'Harmonogram zapisany']);
    } catch (PDOException $e) {
        // Obsługa błędów bazy danych
        $_SESSION['error'] = "Błąd podczas zapisu: " . $e->getMessage();
        echo json_encode(['success' => false, 'message' => 'Błąd podczas zapisu: ' . $e->getMessage()]);
    }
} else {
    $_SESSION['error'] = "Nieprawidłowe żądanie.";
    echo json_encode(['success' => false, 'message' => 'Nieprawidłowe żądanie']);
}

// Przekierowanie na stronę formularza z komunikatami
header('Location: Schedule.php');
exit;
