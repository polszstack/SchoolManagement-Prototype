<?php
/**
 * Delete Event API
 * Remove event using EventManager OOP class
 */

require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/EventManager.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['event_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Event ID is required'
        ]);
        exit;
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    $eventManager = new EventManager($conn);
    
    $result = $eventManager->deleteEvent($input['event_id']);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>