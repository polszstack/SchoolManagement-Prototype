<?php
/**
 * Update Event API
 * Edit existing event using EventManager OOP class
 */

require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/EventManager.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Debug logging
    error_log('Update Event - Input: ' . json_encode($input));
    
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON input'
        ]);
        exit;
    }
    
    if (!isset($input['event_id']) || empty($input['event_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Event ID is required and cannot be empty'
        ]);
        exit;
    }
    
    $event_id = intval($input['event_id']);
    error_log('Parsed event_id: ' . $event_id . ' (type: ' . gettype($event_id) . ')');
    
    if ($event_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Event ID must be a positive integer'
        ]);
        exit;
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    $eventManager = new EventManager($conn);
    
    $result = $eventManager->updateEvent($event_id, $input);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Update Event Error: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>