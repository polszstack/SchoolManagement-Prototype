<?php

require_once dirname(dirname(dirname(__DIR__))) . '/database/db.php';
require_once dirname(__DIR__) . '/classes/EventManager.php';

header('Content-Type: application/json');

try {

    $database = new Database();
    $conn = $database->getConnection();
    $eventManager = new EventManager($conn);

    // READ JSON INPUT
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        throw new Exception("Invalid input data");
    }

    // CREATE EVENT
    $result = $eventManager->createEvent($input);

    echo json_encode($result);

} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}