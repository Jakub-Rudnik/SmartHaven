<?php
require_once './Services/ScheduleAutomation.php';
require_once './Lib/DatabaseConnection.php';

$db = new DatabaseConnection();
$scheduleService = new ScheduleService($db);

$scheduleService->processSchedules();

echo json_encode(['status' => 'success']);

