<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../classes/Schedule.php');
require_once(__DIR__ . '/../../../database/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $schedule_id = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;

    if (!$schedule_id) {
        throw new Exception('Missing schedule_id');
    }

    $database = new Database();
    $conn = $database->getConnection();

    $schedule = new Schedule($conn);
    $schedule->id = $schedule_id;

    if ($schedule->delete()) {
        echo json_encode([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete schedule');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
