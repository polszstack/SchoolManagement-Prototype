<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../../../database/db.php');

try {
    $faculty_id = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : null;

    if (!$faculty_id) {
        throw new Exception('Missing faculty_id parameter');
    }

    $database = new Database();
    $conn = $database->getConnection();

    // Fetch schedule data for the faculty member from cc_schedule
    $query = "SELECT 
                cs.subject_code,
                cs.start_time,
                cs.end_time,
                cs.day_of_week,
                cs.room,
                sec.section_code
              FROM cc_schedule cs
              LEFT JOIN cc_sections sec ON cs.grade_section_id = sec.id
              WHERE cs.faculty_id = :faculty_id
              ORDER BY 
                FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
                cs.start_time";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'schedules' => $schedules
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
