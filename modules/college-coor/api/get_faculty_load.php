<?php
require_once('../classes/FacultyManager.php');
require_once('../../../database/db.php');

header('Content-Type: application/json');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    $manager = new FacultyManager($conn);
    $facultyLoads = $manager->getFacultyLoad();
    
    echo json_encode($facultyLoads);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
