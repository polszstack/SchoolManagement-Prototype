<?php
/**
 * Get Events API
 * Retrieve all events using EventManager OOP class
 */

require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/EventManager.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $conn = $database->getConnection();
    $eventManager = new EventManager($conn);
    
    $events = $eventManager->getAllEvents();
    
    echo json_encode([
        'success' => true,
        'events' => $events
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>